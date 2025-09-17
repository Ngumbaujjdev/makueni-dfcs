<?php
include "../../config/config.php";
include "../../libs/App.php";
include "../../vendor/autoload.php";

use TCPDF as PDF;

// Check if the request method is POST and start/end dates are set
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['start_date']) || !isset($_POST['end_date'])) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request. Start and end dates are required.']);
    exit;
}

$startDate = $_POST['start_date'];
$endDate = $_POST['end_date'];

try {
    // Clean any output buffers
    if (ob_get_length()) ob_clean();
    ob_start();
    
    // Initialize App for database operations
    $app = new App();
    
    // Get session user_id to identify bank staff
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        throw new Exception('User not authenticated');
    }
    
    // Get bank staff information
    $staffQuery = "SELECT s.id as staff_id, s.bank_id, b.name as bank_name, b.branch, b.location 
                  FROM bank_staff s 
                  JOIN banks b ON s.bank_id = b.id
                  WHERE s.user_id = :user_id";
    
    $staff = $app->selectOne($staffQuery, [':user_id' => $userId]);
    
    if (!$staff) {
        throw new Exception('Bank staff information not found');
    }
    
    // Section 1: Payment Collection Summary
    $collectionQuery = "SELECT 
                    COUNT(fat.id) as total_transactions,
                    COALESCE(SUM(CASE WHEN fat.transaction_type = 'credit' THEN fat.amount ELSE 0 END), 0) as total_collected,
                    COALESCE(SUM(CASE WHEN fat.transaction_type = 'debit' THEN fat.amount ELSE 0 END), 0) as total_deducted,
                    COUNT(CASE WHEN fat.description LIKE '%produce%' OR fat.description LIKE '%sale%' THEN 1 END) as produce_transactions,
                    COUNT(CASE WHEN fat.description LIKE '%loan%' OR fat.description LIKE '%repayment%' THEN 1 END) as loan_transactions,
                    COUNT(CASE WHEN fat.description LIKE '%credit%' OR fat.description LIKE '%input%' THEN 1 END) as credit_transactions
                  FROM farmer_account_transactions fat
                  JOIN farmer_accounts fa ON fat.farmer_account_id = fa.id
                  JOIN farmers f ON fa.farmer_id = f.id
                  JOIN loan_applications la ON f.id = la.farmer_id
                  WHERE la.bank_id = :bank_id
                  AND fat.created_at BETWEEN :start_date AND :end_date";
    
    $collections = $app->selectOne($collectionQuery, [
        ':bank_id' => $staff->bank_id,
        ':start_date' => $startDate . ' 00:00:00',
        ':end_date' => $endDate . ' 23:59:59'
    ]);
    
    // Section 2: Settlement Analysis
    $settlementQuery = "SELECT 
                    COUNT(lr.id) as loan_repayments_count,
                    COALESCE(SUM(lr.amount), 0) as loan_repayments_amount,
                    COUNT(icr.id) as credit_repayments_count,
                    COALESCE(SUM(icr.amount), 0) as credit_repayments_amount,
                    COUNT(DISTINCT lr.approved_loan_id) as unique_loans_settled,
                    COUNT(DISTINCT icr.approved_credit_id) as unique_credits_settled
                  FROM loan_repayments lr
                  LEFT JOIN input_credit_repayments icr ON DATE(lr.payment_date) = DATE(icr.deduction_date)
                  WHERE lr.bank_id = '{$staff->bank_id}'
                  AND lr.payment_date BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'";
    
    $settlements = $app->selectOne($settlementQuery);
    
    // Section 3: Reconciliation Status
    $reconciliationQuery = "SELECT 
                    COALESCE(SUM(CASE WHEN bat.transaction_type = 'credit' THEN bat.amount ELSE 0 END), 0) as bank_credits,
                    COALESCE(SUM(CASE WHEN bat.transaction_type = 'debit' THEN bat.amount ELSE 0 END), 0) as bank_debits,
                    COUNT(bat.id) as total_bank_transactions,
                    COALESCE(SUM(aat.amount), 0) as agrovet_payments
                  FROM bank_account_transactions bat
                  JOIN bank_branch_accounts bba ON bat.bank_account_id = bba.id
                  LEFT JOIN agrovet_account_transactions aat ON DATE(bat.created_at) = DATE(aat.created_at)
                  WHERE bba.bank_id = '{$staff->bank_id}'
                  AND bat.created_at BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'";
    
    $reconciliation = $app->selectOne($reconciliationQuery);
    
    // Section 4: Outstanding Balances
    $outstandingQuery = "SELECT 
                    COALESCE(SUM(al.remaining_balance), 0) as outstanding_loans,
                    COALESCE(SUM(aic.remaining_balance), 0) as outstanding_credits,
                    COUNT(CASE WHEN al.status = 'active' THEN 1 END) as active_loans,
                    COUNT(CASE WHEN aic.status = 'active' THEN 1 END) as active_credits
                  FROM approved_loans al
                  LEFT JOIN approved_input_credits aic ON al.bank_id = aic.approved_by
                  WHERE al.bank_id = '{$staff->bank_id}'
                  AND al.remaining_balance > 0";
    
    $outstanding = $app->selectOne($outstandingQuery);
    
    // Section 5: Transaction Matching Analysis
    $matchingQuery = "SELECT 
                pd.id as delivery_id,
                pd.total_value,
                CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                lr.amount as loan_repayment,
                icr.amount as credit_repayment,
                (pd.total_value - COALESCE(lr.amount, 0) - COALESCE(icr.amount, 0)) as farmer_net_amount,
                pd.delivery_date
              FROM produce_deliveries pd
              JOIN farm_products fp ON pd.farm_product_id = fp.id
              JOIN farms fm ON fp.farm_id = fm.id
              JOIN farmers f ON fm.farmer_id = f.id
              JOIN users u ON f.user_id = u.id
              LEFT JOIN loan_repayments lr ON pd.id = lr.produce_delivery_id AND lr.bank_id = '{$staff->bank_id}'
              LEFT JOIN input_credit_repayments icr ON pd.id = icr.produce_delivery_id
              WHERE pd.delivery_date BETWEEN '{$startDate}' AND '{$endDate}'
              AND pd.status = 'verified'
              ORDER BY pd.delivery_date DESC
              LIMIT 20";
    
    $transactions = $app->select_all($matchingQuery);
    
    // Section 6: Daily Payment Trends
    $trendsQuery = "SELECT 
                DATE(fat.created_at) as transaction_date,
                COUNT(fat.id) as daily_transactions,
                COALESCE(SUM(CASE WHEN fat.transaction_type = 'credit' THEN fat.amount ELSE 0 END), 0) as daily_collections,
                COALESCE(SUM(CASE WHEN fat.transaction_type = 'debit' THEN fat.amount ELSE 0 END), 0) as daily_deductions
              FROM farmer_account_transactions fat
              JOIN farmer_accounts fa ON fat.farmer_account_id = fa.id
              JOIN farmers f ON fa.farmer_id = f.id
              JOIN loan_applications la ON f.id = la.farmer_id
              WHERE la.bank_id = '{$staff->bank_id}'
              AND fat.created_at BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'
              GROUP BY DATE(fat.created_at)
              ORDER BY transaction_date DESC
              LIMIT 10";
    
    $trends = $app->select_all($trendsQuery);
    
    // Calculate key metrics
    $netPosition = $collections->total_collected - $collections->total_deducted;
    $settlementTotal = $settlements->loan_repayments_amount + $settlements->credit_repayments_amount;
    $collectionEfficiency = $collections->total_collected > 0 ? ($settlementTotal / $collections->total_collected) * 100 : 0;
    $reconciliationAccuracy = $reconciliation->bank_credits > 0 ? ($reconciliation->bank_debits / $reconciliation->bank_credits) * 100 : 0;
    
    // Create new PDF document
    $pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('DFCS System');
    $pdf->SetAuthor('Makueni DFCS');
    $pdf->SetTitle('Bank Payment Reconciliation Report');
    $pdf->SetSubject('Payment Reconciliation Report for ' . $staff->bank_name);
    
    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(true);
    
    // Set margins
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetHeaderMargin(0);
    $pdf->SetFooterMargin(10);
    
    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, 25);
    
    // Set default font
    $pdf->SetFont('helvetica', '', 10);
    
    // Add a page
    $pdf->AddPage();
    
    // Set theme colors
    $primaryColor = [112, 161, 54]; // Green #70A136
    $secondaryColor = [74, 34, 15]; // Brown #4A220F
    $successColor = [112, 161, 54]; // Same green for success
    $warningColor = [255, 193, 7]; // Warning yellow
    $dangerColor = [220, 53, 69]; // Danger red
    $infoColor = [17, 125, 171]; // Blue for info
    
    // ===== DOCUMENT HEADER =====
    // Logo
    $logoPath = 'http://localhost/dfcs/assets/images/brand-logos/logo3.png';
    $pdf->Image($logoPath, 15, 10, 30, 0, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    
    // Document Title
    $pdf->SetFont('helvetica', 'B', 20);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY(15, 15);
    $pdf->Cell(0, 10, 'BANK PAYMENT RECONCILIATION REPORT', 0, 1, 'C');
    
    // Bank name and details
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 6, $staff->bank_name, 0, 1, 'C');
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
    if ($staff->branch) {
        $pdf->Cell(0, 6, 'Branch: ' . $staff->branch . ' | Location: ' . ($staff->location ?? 'N/A'), 0, 1, 'C');
    }
    $pdf->Cell(0, 6, 'Period: ' . date('F d, Y', strtotime($startDate)) . ' to ' . date('F d, Y', strtotime($endDate)), 0, 1, 'C');
    $pdf->Cell(0, 6, 'Generated on: ' . date('F d, Y'), 0, 1, 'C');
    
    $pdf->Ln(5);
    
    // ===== SECTION 1: PAYMENT COLLECTION SUMMARY =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '1. PAYMENT COLLECTION SUMMARY', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Create a 2x2 grid of summary boxes
    $boxWidth = 85;
    $boxHeight = 40;
    $margin = 10;
    $startY = $pdf->GetY();
    
    // Box styling
    $pdf->SetDrawColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetLineWidth(0.5);
    
    // First row - Total Collections and Total Deductions
    // Total Collections box
    $pdf->Rect(15, $startY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY(20, $startY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 6, 'Total Collections', 0, 1);
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetTextColor($successColor[0], $successColor[1], $successColor[2]);
    $pdf->SetXY(20, $pdf->GetY() + 2);
    $pdf->Cell($boxWidth - 10, 10, 'KES ' . number_format($collections->total_collected, 0), 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY(20, $pdf->GetY());
    $pdf->Cell($boxWidth - 10, 5, number_format($collections->total_transactions) . ' transactions', 0, 1);
    
    // Total Deductions box
    $rightColX = 15 + $boxWidth + $margin;
    $pdf->Rect($rightColX, $startY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY($rightColX + 5, $startY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 6, 'Total Deductions', 0, 1);
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetTextColor($warningColor[0], $warningColor[1], $warningColor[2]);
    $pdf->SetXY($rightColX + 5, $startY + 13);
    $pdf->Cell($boxWidth - 10, 10, 'KES ' . number_format($collections->total_deducted, 0), 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY($rightColX + 5, $startY + 25);
    $pdf->Cell($boxWidth - 10, 5, 'For loans & credits', 0, 1);
    
    // Second row - Settlement Total and Net Position
    $secondRowY = $startY + $boxHeight + 10;
    
    // Settlement Total box
    $pdf->Rect(15, $secondRowY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY(20, $secondRowY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 6, 'Total Settlements', 0, 1);
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetTextColor($infoColor[0], $infoColor[1], $infoColor[2]);
    $pdf->SetXY(20, $secondRowY + 13);
    $pdf->Cell($boxWidth - 10, 10, 'KES ' . number_format($settlementTotal, 0), 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY(20, $secondRowY + 25);
    $pdf->Cell($boxWidth - 10, 5, 'Loans + Credits', 0, 1);
    
    // Net Position box
    $pdf->Rect($rightColX, $secondRowY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY($rightColX + 5, $secondRowY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 6, 'Net Position', 0, 1);
    $pdf->SetFont('helvetica', 'B', 16);
    $netColor = $netPosition >= 0 ? $successColor : $dangerColor;
    $pdf->SetTextColor($netColor[0], $netColor[1], $netColor[2]);
    $pdf->SetXY($rightColX + 5, $secondRowY + 13);
    $pdf->Cell($boxWidth - 10, 10, 'KES ' . number_format($netPosition, 0), 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY($rightColX + 5, $secondRowY + 25);
    $pdf->Cell($boxWidth - 10, 5, $netPosition >= 0 ? 'Surplus' : 'Deficit', 0, 1);
    
    // Set Y position after summary boxes
    $pdf->SetY($secondRowY + $boxHeight + 10);
    
    $pdf->Ln(5);
    
    // ===== SECTION 2: SETTLEMENT BREAKDOWN =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '2. SETTLEMENT BREAKDOWN', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Settlement breakdown table
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    
    // Table header
    $pdf->Cell(60, 7, 'Settlement Type', 1, 0, 'C', true);
    $pdf->Cell(30, 7, 'Count', 1, 0, 'C', true);
    $pdf->Cell(40, 7, 'Amount (KES)', 1, 0, 'C', true);
    $pdf->Cell(30, 7, 'Unique Items', 1, 1, 'C', true);
    
    // Reset styling for table rows
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->SetFillColor(245, 245, 245);
    
    // Settlement rows
    $settlements_data = [
        ['Loan Repayments', $settlements->loan_repayments_count, $settlements->loan_repayments_amount, $settlements->unique_loans_settled],
        ['Credit Repayments', $settlements->credit_repayments_count, $settlements->credit_repayments_amount, $settlements->unique_credits_settled]
    ];
    
    foreach ($settlements_data as $index => $settlement) {
        $fillRow = $index % 2 == 0;
        $pdf->Cell(60, 6, $settlement[0], 1, 0, 'L', $fillRow);
        $pdf->Cell(30, 6, number_format($settlement[1]), 1, 0, 'C', $fillRow);
        $pdf->Cell(40, 6, number_format($settlement[2], 2), 1, 0, 'R', $fillRow);
        $pdf->Cell(30, 6, number_format($settlement[3]), 1, 1, 'C', $fillRow);
    }
    
    $pdf->Ln(5);
    
    // ===== SECTION 3: OUTSTANDING BALANCES =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '3. OUTSTANDING BALANCES', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Outstanding balances table
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    
    // Table header
    $pdf->Cell(60, 7, 'Balance Type', 1, 0, 'C', true);
    $pdf->Cell(40, 7, 'Outstanding (KES)', 1, 0, 'C', true);
    $pdf->Cell(30, 7, 'Active Count', 1, 0, 'C', true);
    $pdf->Cell(30, 7, 'Status', 1, 1, 'C', true);
    
    // Reset styling for table rows
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    
    // Outstanding balances rows
    $outstanding_data = [
        ['Loan Balances', $outstanding->outstanding_loans, $outstanding->active_loans, $outstanding->outstanding_loans > 0 ? 'Pending' : 'Clear'],
        ['Credit Balances', $outstanding->outstanding_credits, $outstanding->active_credits, $outstanding->outstanding_credits > 0 ? 'Pending' : 'Clear']
    ];
    
    foreach ($outstanding_data as $index => $balance) {
        $fillRow = $index % 2 == 0;
        if ($fillRow) {
            $pdf->SetFillColor(245, 245, 245);
        } else {
            $pdf->SetFillColor(255, 255, 255);
        }
        
        $pdf->Cell(60, 6, $balance[0], 1, 0, 'L', $fillRow);
        $pdf->Cell(40, 6, number_format($balance[1], 2), 1, 0, 'R', $fillRow);
        $pdf->Cell(30, 6, number_format($balance[2]), 1, 0, 'C', $fillRow);
        
        $statusColor = $balance[3] == 'Clear' ? $successColor : $warningColor;
        $pdf->SetTextColor($statusColor[0], $statusColor[1], $statusColor[2]);
        $pdf->Cell(30, 6, $balance[3], 1, 1, 'C', $fillRow);
        $pdf->SetTextColor(0, 0, 0);
    }
    
    $pdf->Ln(5);
    
    // ===== SECTION 4: RECENT TRANSACTION MATCHING =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '4. RECENT TRANSACTION MATCHING (Last 20)', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Transaction matching table
    $pdf->SetFont('helvetica', 'B', 7);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    
    // Table header
    $pdf->Cell(25, 7, 'Farmer', 1, 0, 'C', true);
    $pdf->Cell(25, 7, 'Sale Value', 1, 0, 'C', true);
    $pdf->Cell(25, 7, 'Loan Repay', 1, 0, 'C', true);
    $pdf->Cell(25, 7, 'Credit Repay', 1, 0, 'C', true);
    $pdf->Cell(25, 7, 'Net Amount', 1, 0, 'C', true);
    $pdf->Cell(25, 7, 'Date', 1, 1, 'C', true);
    
    // Reset styling for table rows
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 6);
    
    // Transaction matching rows
    if ($transactions) {
        $displayTransactions = array_slice($transactions, 0, 15);
        foreach ($displayTransactions as $index => $txn) {
            $fillRow = $index % 2 == 0;
            if ($fillRow) {
                $pdf->SetFillColor(245, 245, 245);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            
            $pdf->Cell(25, 5, substr($txn->farmer_name, 0, 12), 1, 0, 'L', $fillRow);
            $pdf->Cell(25, 5, number_format($txn->total_value/1000, 1) . 'K', 1, 0, 'R', $fillRow);
            $pdf->Cell(25, 5, $txn->loan_repayment ? number_format($txn->loan_repayment/1000, 1) . 'K' : '-', 1, 0, 'R', $fillRow);
            $pdf->Cell(25, 5, $txn->credit_repayment ? number_format($txn->credit_repayment/1000, 1) . 'K' : '-', 1, 0, 'R', $fillRow);
            
            $netColor = $txn->farmer_net_amount >= 0 ? $successColor : $dangerColor;
            $pdf->SetTextColor($netColor[0], $netColor[1], $netColor[2]);
            $pdf->Cell(25, 5, number_format($txn->farmer_net_amount/1000, 1) . 'K', 1, 0, 'R', $fillRow);
            $pdf->SetTextColor(0, 0, 0);
            
            $pdf->Cell(25, 5, date('M d', strtotime($txn->delivery_date)), 1, 1, 'C', $fillRow);
        }
    } else {
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(150, 6, 'No transaction matching data found', 1, 1, 'C', true);
    }
    
    $pdf->Ln(5);
    
    // Check if we need a new page for findings section
    if ($pdf->GetY() > 220) {
        $pdf->AddPage();
    }
    
    // ===== SECTION 5: FINDINGS & RECOMMENDATIONS =====
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '5. FINDINGS & RECOMMENDATIONS', 0, 1, 'L');
    $pdf->Ln(3);
    
    // FINDINGS
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor($infoColor[0], $infoColor[1], $infoColor[2]);
    $pdf->Cell(0, 7, 'KEY FINDINGS:', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(0, 0, 0);
    
    $findings = [
        "Collection efficiency of " . number_format($collectionEfficiency, 1) . "% indicates " . 
        ($collectionEfficiency > 80 ? "excellent payment processing performance" : "room for improvement in collections"),
        
        "Net position of KES " . number_format(abs($netPosition), 0) . " shows " . 
        ($netPosition >= 0 ? "healthy cash flow surplus" : "cash flow deficit requiring attention"),
        
        "Outstanding balances total KES " . number_format($outstanding->outstanding_loans + $outstanding->outstanding_credits, 0) . " representing " . 
        ($outstanding->active_loans + $outstanding->active_credits) . " active accounts",
        
        "Settlement processing handled " . number_format($settlements->loan_repayments_count + $settlements->credit_repayments_count) . " transactions totaling KES " . 
        number_format($settlementTotal, 0)
    ];
    
    foreach ($findings as $finding) {
        $pdf->Cell(5, 5, '•', 0, 0, 'L');
        $pdf->Cell(175, 5, $finding, 0, 1, 'L');
        $pdf->Ln(0.5);
    }
    
    $pdf->Ln(2);
    
    // RECOMMENDATIONS
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor($successColor[0], $successColor[1], $successColor[2]);
    $pdf->Cell(0, 7, 'STRATEGIC RECOMMENDATIONS:', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(0, 0, 0);
    
    $recommendations = [];
    
    if ($collectionEfficiency < 70) {
        $recommendations[] = "Improve collection processes and reduce settlement delays to enhance cash flow";
    }
    
    if ($netPosition < 0) {
        $recommendations[] = "Implement cash flow management strategies to address deficit position";
        $recommendations[] = "Review settlement timing to optimize liquidity management";
    }
    
    if ($outstanding->outstanding_loans + $outstanding->outstanding_credits > $collections->total_collected * 0.5) {
        $recommendations[] = "Strengthen collection efforts for outstanding balances to improve recovery rates";
    }
    
    if ($reconciliation->total_bank_transactions < ($settlements->loan_repayments_count + $settlements->credit_repayments_count) * 0.8) {
        $recommendations[] = "Enhance transaction matching accuracy to improve reconciliation processes";
    }
    
    // Default recommendations if none specific
    if (empty($recommendations)) {
        $recommendations = [
            "Continue current payment processing practices given strong reconciliation performance",
            "Implement automated reconciliation tools to improve operational efficiency",
            "Develop real-time payment tracking systems for better cash flow visibility",
            "Establish regular reconciliation reviews to maintain accuracy and identify issues early"
        ];
    }
    
    foreach ($recommendations as $recommendation) {
        $pdf->Cell(5, 5, '•', 0, 0, 'L');
        $pdf->Cell(175, 5, $recommendation, 0, 1, 'L');
        $pdf->Ln(0.5);
    }
    
    // ===== DOCUMENT FOOTER =====
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->Cell(0, 5, 'This is a computer-generated report and does not require a signature.', 0, 1, 'C');
    $pdf->Cell(0, 5, 'Generated on: ' . date('F d, Y h:i A'), 0, 1, 'C');
    $pdf->Cell(0, 5, 'Report Period: ' . date('F d, Y', strtotime($startDate)) . ' to ' . date('F d, Y', strtotime($endDate)), 0, 1, 'C');
    
    // Output the PDF
    $pdf->Output('Bank_Payment_Reconciliation_Report_' . $startDate . '_to_' . $endDate . '.pdf', 'I');
    exit;

} catch (Exception $e) {
    // Clean output buffer
    if (ob_get_length()) ob_clean();
    
    // Return error as JSON
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => 'Failed to generate PDF: ' . $e->getMessage()]);
    exit;
}
?>