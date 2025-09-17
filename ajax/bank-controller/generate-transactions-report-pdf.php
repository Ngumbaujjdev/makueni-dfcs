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
    
    // Section 1: Get transaction summary stats for the bank within date range
    $statsQuery = "SELECT 
                    COUNT(*) as total_transactions,
                    SUM(CASE WHEN transaction_type = 'credit' THEN 1 ELSE 0 END) as total_credits,
                    SUM(CASE WHEN transaction_type = 'debit' THEN 1 ELSE 0 END) as total_debits,
                    SUM(CASE WHEN transaction_type = 'credit' THEN amount ELSE 0 END) as total_credit_amount,
                    SUM(CASE WHEN transaction_type = 'debit' THEN amount ELSE 0 END) as total_debit_amount,
                    SUM(amount) as total_transaction_volume,
                    AVG(amount) as avg_transaction_amount,
                    MIN(amount) as min_transaction_amount,
                    MAX(amount) as max_transaction_amount
                  FROM bank_account_transactions bat
                  JOIN bank_branch_accounts bba ON bat.bank_account_id = bba.id
                  WHERE bba.bank_id = :bank_id
                  AND bat.created_at BETWEEN :start_date AND :end_date";
    
    $stats = $app->selectOne($statsQuery, [
        ':bank_id' => $staff->bank_id,
        ':start_date' => $startDate . ' 00:00:00',
        ':end_date' => $endDate . ' 23:59:59'
    ]);
    
    // Calculate net position
    $netPosition = $stats->total_credit_amount - $stats->total_debit_amount;
    
    // Section 2: Get account-wise transaction summary
    $accountSummaryQuery = "SELECT 
                bba.account_number,
                bba.account_type,
                bba.balance as current_balance,
                COUNT(bat.id) as transaction_count,
                SUM(CASE WHEN bat.transaction_type = 'credit' THEN bat.amount ELSE 0 END) as total_credits,
                SUM(CASE WHEN bat.transaction_type = 'debit' THEN bat.amount ELSE 0 END) as total_debits,
                MIN(bat.created_at) as first_transaction,
                MAX(bat.created_at) as last_transaction
              FROM bank_branch_accounts bba
              LEFT JOIN bank_account_transactions bat ON bba.id = bat.bank_account_id 
                AND bat.created_at BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'
              WHERE bba.bank_id = '{$staff->bank_id}'
              GROUP BY bba.id, bba.account_number, bba.account_type, bba.balance
              ORDER BY transaction_count DESC";
    
    $accountSummary = $app->select_all($accountSummaryQuery);
    
    // Section 3: Get transaction type breakdown
    $transactionTypesQuery = "SELECT 
                bat.description as transaction_description,
                COUNT(*) as transaction_count,
                SUM(bat.amount) as total_amount,
                AVG(bat.amount) as avg_amount,
                bat.transaction_type
              FROM bank_account_transactions bat
              JOIN bank_branch_accounts bba ON bat.bank_account_id = bba.id
              WHERE bba.bank_id = '{$staff->bank_id}'
              AND bat.created_at BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'
              GROUP BY bat.description, bat.transaction_type
              ORDER BY total_amount DESC
              LIMIT 10";
    
    $transactionTypes = $app->select_all($transactionTypesQuery);
    
    // Section 4: Get detailed transaction list
    $transactionsQuery = "SELECT 
                bat.id,
                bat.transaction_type,
                bat.amount,
                bat.reference_id,
                bat.description,
                bat.created_at,
                bba.account_number,
                bba.account_type,
                CONCAT(u.first_name, ' ', u.last_name) as processed_by_name
              FROM bank_account_transactions bat
              JOIN bank_branch_accounts bba ON bat.bank_account_id = bba.id
              LEFT JOIN users u ON bat.processed_by = u.id
              WHERE bba.bank_id = '{$staff->bank_id}'
              AND bat.created_at BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'
              ORDER BY bat.created_at DESC
              LIMIT 100";
    
    $transactions = $app->select_all($transactionsQuery);
    
    // Section 5: Get daily transaction trends
    $dailyTrendsQuery = "SELECT 
                DATE(bat.created_at) as transaction_date,
                COUNT(*) as daily_count,
                SUM(CASE WHEN bat.transaction_type = 'credit' THEN bat.amount ELSE 0 END) as daily_credits,
                SUM(CASE WHEN bat.transaction_type = 'debit' THEN bat.amount ELSE 0 END) as daily_debits,
                SUM(bat.amount) as daily_volume
              FROM bank_account_transactions bat
              JOIN bank_branch_accounts bba ON bat.bank_account_id = bba.id
              WHERE bba.bank_id = '{$staff->bank_id}'
              AND bat.created_at BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'
              GROUP BY DATE(bat.created_at)
              ORDER BY transaction_date ASC";
    
    $dailyTrends = $app->select_all($dailyTrendsQuery);
    
    // Create new PDF document
    $pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('DFCS System');
    $pdf->SetAuthor('Makueni DFCS');
    $pdf->SetTitle('Bank Transaction Report');
    $pdf->SetSubject('Bank Transaction Report for ' . $staff->bank_name);
    
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
    $infoColor = [112, 161, 54]; // Use green for info too
    
    // ===== DOCUMENT HEADER =====
    // Logo
    $logoPath = 'http://localhost/dfcs/assets/images/brand-logos/logo3.png';
    $pdf->Image($logoPath, 15, 10, 30, 0, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    
    // Document Title
    $pdf->SetFont('helvetica', 'B', 20);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY(15, 15);
    $pdf->Cell(0, 10, 'BANK TRANSACTION REPORT', 0, 1, 'C');
    
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
    
    // ===== SECTION 1: SUMMARY STATISTICS =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '1. TRANSACTION SUMMARY STATISTICS', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Create a 2x2 grid of summary boxes
    $boxWidth = 85;
    $boxHeight = 40;
    $margin = 10;
    $startY = $pdf->GetY();
    
    // Box styling
    $pdf->SetDrawColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetLineWidth(0.5);
    
    // First row - Total Transactions and Total Volume
    // Total Transactions box
    $pdf->Rect(15, $startY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY(20, $startY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 6, 'Total Transactions', 0, 1);
    $pdf->SetFont('helvetica', 'B', 18);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY(20, $pdf->GetY() + 2);
    $pdf->Cell($boxWidth - 10, 10, number_format($stats->total_transactions), 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY(20, $pdf->GetY());
    $pdf->Cell($boxWidth - 10, 5, 'Credits: ' . number_format($stats->total_credits) . ' | Debits: ' . number_format($stats->total_debits), 0, 1);
    
    // Total Volume box
    $rightColX = 15 + $boxWidth + $margin;
    $pdf->Rect($rightColX, $startY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY($rightColX + 5, $startY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 6, 'Total Volume', 0, 1);
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY($rightColX + 5, $startY + 13);
    $pdf->Cell($boxWidth - 10, 10, 'KES ' . number_format($stats->total_transaction_volume, 2), 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY($rightColX + 5, $startY + 25);
    $pdf->Cell($boxWidth - 10, 5, 'Avg: KES ' . number_format($stats->avg_transaction_amount, 2), 0, 1);
    
    // Second row - Credits and Debits
    $secondRowY = $startY + $boxHeight + 10;
    
    // Credits box
    $pdf->Rect(15, $secondRowY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY(20, $secondRowY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 6, 'Total Credits', 0, 1);
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetTextColor($successColor[0], $successColor[1], $successColor[2]);
    $pdf->SetXY(20, $secondRowY + 13);
    $pdf->Cell($boxWidth - 10, 10, 'KES ' . number_format($stats->total_credit_amount, 2), 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY(20, $secondRowY + 25);
    $pdf->Cell($boxWidth - 10, 5, number_format($stats->total_credits) . ' transactions', 0, 1);
    
    // Debits box
    $pdf->Rect($rightColX, $secondRowY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY($rightColX + 5, $secondRowY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 6, 'Total Debits', 0, 1);
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetTextColor($dangerColor[0], $dangerColor[1], $dangerColor[2]);
    $pdf->SetXY($rightColX + 5, $secondRowY + 13);
    $pdf->Cell($boxWidth - 10, 10, 'KES ' . number_format($stats->total_debit_amount, 2), 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY($rightColX + 5, $secondRowY + 25);
    $pdf->Cell($boxWidth - 10, 5, number_format($stats->total_debits) . ' transactions', 0, 1);
    
    // Set Y position after summary boxes
    $pdf->SetY($secondRowY + $boxHeight + 10);
    
    // Net Position indicator
    $pdf->SetFont('helvetica', 'B', 12);
    $netColor = $netPosition >= 0 ? $successColor : $dangerColor;
    $pdf->SetTextColor($netColor[0], $netColor[1], $netColor[2]);
    $netText = $netPosition >= 0 ? 'Net Inflow: KES ' . number_format(abs($netPosition), 2) : 'Net Outflow: KES ' . number_format(abs($netPosition), 2);
    $pdf->Cell(0, 8, $netText, 0, 1, 'C');
    
    $pdf->Ln(5);
    
    // ===== SECTION 2: ACCOUNT-WISE BREAKDOWN =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '2. ACCOUNT-WISE TRANSACTION BREAKDOWN', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Account breakdown table
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    
    // Table header
    $pdf->Cell(40, 8, 'Account Number', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Type', 1, 0, 'C', true);
    $pdf->Cell(20, 8, 'Txns', 1, 0, 'C', true);
    $pdf->Cell(35, 8, 'Credits (KES)', 1, 0, 'C', true);
    $pdf->Cell(35, 8, 'Debits (KES)', 1, 0, 'C', true);
    $pdf->Cell(35, 8, 'Balance (KES)', 1, 1, 'C', true);
    
    // Reset styling for table rows
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->SetFillColor(245, 245, 245);
    
    // Account rows
    if ($accountSummary) {
        foreach ($accountSummary as $index => $account) {
            $fillRow = $index % 2 == 0;
            $pdf->Cell(40, 6, $account->account_number, 1, 0, 'C', $fillRow);
            $pdf->Cell(25, 6, $account->account_type, 1, 0, 'C', $fillRow);
            $pdf->Cell(20, 6, number_format($account->transaction_count), 1, 0, 'C', $fillRow);
            $pdf->Cell(35, 6, number_format($account->total_credits, 2), 1, 0, 'R', $fillRow);
            $pdf->Cell(35, 6, number_format($account->total_debits, 2), 1, 0, 'R', $fillRow);
            $pdf->Cell(35, 6, number_format($account->current_balance, 2), 1, 1, 'R', $fillRow);
        }
    } else {
        $pdf->Cell(190, 8, 'No accounts found', 1, 1, 'C', true);
    }
    
    $pdf->Ln(5);
    
    // ===== SECTION 3: TRANSACTION TYPE BREAKDOWN =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '3. TOP TRANSACTION TYPES', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Transaction type breakdown table
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    
    // Table header
    $pdf->Cell(80, 8, 'Transaction Description', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Type', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Count', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Total (KES)', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Average (KES)', 1, 1, 'C', true);
    
    // Reset styling for table rows
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetFillColor(245, 245, 245);
    
    // Transaction type rows
    if ($transactionTypes) {
        foreach ($transactionTypes as $index => $type) {
            $fillRow = $index % 2 == 0;
            $pdf->Cell(80, 6, substr($type->transaction_description, 0, 35), 1, 0, 'L', $fillRow);
            $typeColor = $type->transaction_type == 'credit' ? $successColor : $dangerColor;
            $pdf->SetTextColor($typeColor[0], $typeColor[1], $typeColor[2]);
            $pdf->Cell(25, 6, ucfirst($type->transaction_type), 1, 0, 'C', $fillRow);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(25, 6, number_format($type->transaction_count), 1, 0, 'C', $fillRow);
            $pdf->Cell(30, 6, number_format($type->total_amount, 2), 1, 0, 'R', $fillRow);
            $pdf->Cell(30, 6, number_format($type->avg_amount, 2), 1, 1, 'R', $fillRow);
        }
    } else {
        $pdf->Cell(190, 8, 'No transaction types found in the selected period', 1, 1, 'C', true);
    }
    
    $pdf->Ln(5);
    
    // ===== SECTION 4: DETAILED TRANSACTION LIST =====
    // Start a new page for the transaction list
    $pdf->AddPage();
    
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '4. DETAILED TRANSACTION LIST (Last 100)', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Transaction list table
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    
    // Table header
    $pdf->Cell(15, 8, 'ID', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Account', 1, 0, 'C', true);
    $pdf->Cell(20, 8, 'Type', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Amount (KES)', 1, 0, 'C', true);
    $pdf->Cell(60, 8, 'Description', 1, 0, 'C', true);
    $pdf->Cell(40, 8, 'Date & Time', 1, 1, 'C', true);
    
    // Reset styling for table rows
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 7);
    
    // Transaction rows
    if ($transactions) {
        foreach ($transactions as $index => $txn) {
            $fillRow = $index % 2 == 0;
            if ($fillRow) {
                $pdf->SetFillColor(245, 245, 245);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            
            $pdf->Cell(15, 6, 'T' . str_pad($txn->id, 4, '0', STR_PAD_LEFT), 1, 0, 'C', $fillRow);
            $pdf->Cell(25, 6, $txn->account_number, 1, 0, 'C', $fillRow);
            
            // Type with color
            $typeColor = $txn->transaction_type == 'credit' ? $successColor : $dangerColor;
            $pdf->SetFillColor($typeColor[0], $typeColor[1], $typeColor[2]);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(20, 6, ucfirst($txn->transaction_type), 1, 0, 'C', true);
            
            // Reset colors
            if ($fillRow) {
                $pdf->SetFillColor(245, 245, 245);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            $pdf->SetTextColor(0, 0, 0);
            
            $pdf->Cell(30, 6, number_format($txn->amount, 2), 1, 0, 'R', $fillRow);
            $pdf->Cell(60, 6, substr($txn->description, 0, 25), 1, 0, 'L', $fillRow);
            $pdf->Cell(40, 6, date('M d, Y H:i', strtotime($txn->created_at)), 1, 1, 'C', $fillRow);
        }
    } else {
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(190, 8, 'No transactions found in the selected period', 1, 1, 'C', true);
    }
    
    $pdf->Ln(5);
    
    // ===== DOCUMENT FOOTER =====
    $pdf->SetY($pdf->GetY() + 5);
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->Cell(0, 6, 'This is a computer-generated report and does not require a signature.', 0, 1, 'C');
    $pdf->Cell(0, 6, 'Generated on: ' . date('F d, Y h:i A'), 0, 1, 'C');
    $pdf->Cell(0, 6, 'Report Period: ' . date('F d, Y', strtotime($startDate)) . ' to ' . date('F d, Y', strtotime($endDate)), 0, 1, 'C');
    
    // Output the PDF
    $pdf->Output('Bank_Transaction_Report_' . $startDate . '_to_' . $endDate . '.pdf', 'I');
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