<?php
include "../../config/config.php";
include "../../libs/App.php";
include "../../vendor/autoload.php";

use TCPDF as PDF;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request method.']);
    exit;
}

try {
    if (ob_get_length()) ob_clean();
    ob_start();
    
    $app = new App();
    
    // Fetch SACCO account details
    $query = "SELECT * FROM sacco_accounts WHERE id = 1";
    $saccoAccount = $app->selectOne($query);
    
    if (!$saccoAccount) {
        throw new Exception("SACCO account information not found");
    }
    
    // Get current date for the report
    $currentDate = date('Y-m-d');
    $formattedDate = date('F d, Y', strtotime($currentDate));
    
    // Get key financial metrics
    // 1. Total SACCO Balance
    $saccoBalance = $saccoAccount->balance;
    
    // 2. Loan Statistics
    $loanStatsQuery = "SELECT 
                        COUNT(*) as total_applications,
                        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_count,
                        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_count,
                        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count,
                        AVG(amount_requested) as avg_requested_amount
                      FROM loan_applications 
                      WHERE provider_type = 'sacco'";
    $loanStats = $app->selectOne($loanStatsQuery);
    
    // 3. Approved Loans
    $approvedLoansQuery = "SELECT 
                            SUM(approved_amount) as total_approved,
                            AVG(approved_amount) as avg_approved,
                            COUNT(*) as count,
                            SUM(processing_fee) as total_fees
                          FROM approved_loans 
                          WHERE bank_id IS NULL"; // SACCO loans only
    $approvedLoans = $app->selectOne($approvedLoansQuery);
    
    // 4. Loan Repayments
    $repaymentQuery = "SELECT 
                        SUM(amount) as total_repaid,
                        COUNT(*) as repayment_count
                       FROM loan_repayments
                       WHERE bank_id IS NULL"; // SACCO repayments only
    $repayments = $app->selectOne($repaymentQuery);
    
    // 5. Produce Statistics
    $produceQuery = "SELECT 
                     COUNT(*) as delivery_count,
                     SUM(total_value) as total_value,
                     SUM(quantity) as total_quantity,
                     COUNT(CASE WHEN status = 'sold' OR status = 'paid' THEN 1 END) as sold_count
                    FROM produce_deliveries";
    $produceStats = $app->selectOne($produceQuery);
    
    // 6. SACCO commission calculation - typically 10% of produce sales
    $commissionRate = 0.10; // 10%
    $commissionAmount = $produceStats->total_value * $commissionRate;
    
    // 7. Active farmers count
    $activeFarmersQuery = "SELECT COUNT(DISTINCT farmer_id) as active_farmers 
                           FROM loan_applications
                           WHERE application_date >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)";
    $activeFarmers = $app->selectOne($activeFarmersQuery);
    
    // Create PDF
    $pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document info
    $pdf->SetCreator('DFCS System');
    $pdf->SetAuthor('Makueni DFCS');
    $pdf->SetTitle('SACCO Operations Report');
    
    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(true);
    
    // Set margins
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(TRUE, 15);
    
    // Add page
    $pdf->AddPage();
    
    // Define colors
    $primaryColor = [106, 163, 45]; // #6AA32D
    $secondaryColor = [108, 117, 125]; // Gray
    // ===== REPORT HEADER =====
    $pdf->Image('http://localhost/dfcs/assets/images/brand-logos/logo3.png', 15, 10, 30);
    
    $pdf->SetFont('helvetica', 'B', 20);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, 'SACCO OPERATIONS REPORT', 0, 1, 'C');
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
    $pdf->Cell(0, 6, 'Report Date: ' . $formattedDate, 0, 1, 'C');
    
    // Add success badge
    $pdf->Ln(5);
    $pdf->SetFillColor(40, 167, 69);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('helvetica', 'B', 10);
    $statusWidth = 50;
    $pageWidth = $pdf->GetPageWidth() - 30;
    $statusX = ($pageWidth - $statusWidth) / 2 + 15;
    $pdf->SetXY($statusX, $pdf->GetY());
    $pdf->Cell($statusWidth, 6, 'SACCO SUMMARY', 0, 1, 'C', true);
    
    // ===== ACCOUNT INFORMATION =====
    $pdf->Ln(5);
    $pdf->SetDrawColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetLineWidth(0.5);
    
    $infoBoxY = $pdf->GetY();
    $boxWidth = 85;
    $boxHeight = 45;
    
    // SACCO info box
    $pdf->Rect(15, $infoBoxY, $boxWidth, $boxHeight);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY(20, $infoBoxY + 2);
    $pdf->Cell(75, 6, 'SACCO Information', 0, 1);
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY(20, $pdf->GetY() + 1);
    $pdf->Cell(75, 5, 'Name: ' . $saccoAccount->sacco_name, 0, 1);
    $pdf->SetX(20);
    $pdf->Cell(75, 5, 'Account: ' . $saccoAccount->account_number, 0, 1);
    $pdf->SetX(20);
    $pdf->Cell(75, 5, 'Type: ' . $saccoAccount->account_type, 0, 1);
    $pdf->SetX(20);
    $pdf->Cell(75, 5, 'Current Balance: KES ' . number_format($saccoBalance, 2), 0, 1);
    
    // Financial Summary box
    $pdf->Rect(110, $infoBoxY, $boxWidth, $boxHeight);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY(115, $infoBoxY + 2);
    $pdf->Cell(75, 6, 'Financial Summary', 0, 1);
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY(115, $pdf->GetY() + 1);
    $pdf->Cell(75, 5, 'Total Loans: ' . ($loanStats->total_applications ?? 0), 0, 1);
    $pdf->SetX(115);
    $pdf->Cell(75, 5, 'Loans Approved: ' . ($loanStats->approved_count ?? 0), 0, 1);
    $pdf->SetX(115);
    $pdf->Cell(75, 5, 'Loans Completed: ' . ($loanStats->completed_count ?? 0), 0, 1);
    $pdf->SetX(115);
    $totalLoanValue = ($approvedLoans->total_approved ?? 0);
    $pdf->Cell(75, 5, 'Total Loan Value: KES ' . number_format($totalLoanValue, 2), 0, 1);
    
    $pdf->Ln(10);
    
    // ===== LOAN INFORMATION =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 8, 'LOAN PERFORMANCE', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Add the green header row
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('helvetica', 'B', 10);
    
    // Loan metrics table
    $pdf->Cell(60, 8, 'Metric', 1, 0, 'L', true);
    $pdf->Cell(40, 8, 'Value', 1, 0, 'C', true);
    $pdf->Cell(80, 8, 'Notes', 1, 1, 'C', true);
    
    // Table data
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    
    // Row 1: Loan Applications
    $pdf->Cell(60, 8, 'Loan Applications', 1, 0, 'L');
    $pdf->Cell(40, 8, ($loanStats->total_applications ?? 0), 1, 0, 'C');
    $pdf->Cell(80, 8, 'Total loan applications received', 1, 1, 'L');
    
    // Row 2: Approval Rate
    $pdf->Cell(60, 8, 'Approval Rate', 1, 0, 'L');
    $approvalRate = ($loanStats->total_applications > 0) ? 
                  (($loanStats->approved_count / $loanStats->total_applications) * 100) : 0;
    $pdf->Cell(40, 8, number_format($approvalRate, 2) . '%', 1, 0, 'C');
    $pdf->Cell(80, 8, 'Percentage of applications approved', 1, 1, 'L');
    
    // Row 3: Average Loan Amount
    $pdf->Cell(60, 8, 'Average Loan Amount', 1, 0, 'L');
    $avgLoanAmount = ($approvedLoans->avg_approved ?? 0);
    $pdf->Cell(40, 8, 'KES ' . number_format($avgLoanAmount, 2), 1, 0, 'C');
    $pdf->Cell(80, 8, 'Average amount per approved loan', 1, 1, 'L');
    
    // Row 4: Processing Fees
    $pdf->Cell(60, 8, 'Processing Fees Collected', 1, 0, 'L');
    $processingFees = ($approvedLoans->total_fees ?? 0);
    $pdf->Cell(40, 8, 'KES ' . number_format($processingFees, 2), 1, 0, 'C');
    $pdf->Cell(80, 8, 'Total processing fees from loans', 1, 1, 'L');
    
    // Row 5: Loan Repayments
    $pdf->Cell(60, 8, 'Loan Repayments', 1, 0, 'L');
    $totalRepaid = ($repayments->total_repaid ?? 0);
    $pdf->Cell(40, 8, 'KES ' . number_format($totalRepaid, 2), 1, 0, 'C');
    $pdf->Cell(80, 8, 'Total repayments received', 1, 1, 'L');
    
    $pdf->Ln(5);
    // ===== PRODUCE INFORMATION =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 8, 'PRODUCE OPERATIONS', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Add the green header row
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('helvetica', 'B', 10);
    
    // Produce metrics table
    $pdf->Cell(60, 8, 'Metric', 1, 0, 'L', true);
    $pdf->Cell(40, 8, 'Value', 1, 0, 'C', true);
    $pdf->Cell(80, 8, 'Notes', 1, 1, 'C', true);
    
    // Table data
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    
    // Row 1: Produce Deliveries
    $pdf->Cell(60, 8, 'Produce Deliveries', 1, 0, 'L');
    $pdf->Cell(40, 8, ($produceStats->delivery_count ?? 0), 1, 0, 'C');
    $pdf->Cell(80, 8, 'Total produce deliveries processed', 1, 1, 'L');
    
    // Row 2: Produce Sales
    $pdf->Cell(60, 8, 'Produce Sales', 1, 0, 'L');
    $pdf->Cell(40, 8, ($produceStats->sold_count ?? 0), 1, 0, 'C');
    $pdf->Cell(80, 8, 'Deliveries sold to buyers', 1, 1, 'L');
    
    // Row 3: Total Produce Quantity
    $pdf->Cell(60, 8, 'Total Produce Quantity', 1, 0, 'L');
    $totalQuantity = ($produceStats->total_quantity ?? 0);
    $pdf->Cell(40, 8, number_format($totalQuantity, 2) . ' KGs', 1, 0, 'C');
    $pdf->Cell(80, 8, 'Total quantity of produce delivered', 1, 1, 'L');
    
    // Row 4: Total Produce Value
    $pdf->Cell(60, 8, 'Total Produce Value', 1, 0, 'L');
    $totalValue = ($produceStats->total_value ?? 0);
    $pdf->Cell(40, 8, 'KES ' . number_format($totalValue, 2), 1, 0, 'C');
    $pdf->Cell(80, 8, 'Total value of produce delivered', 1, 1, 'L');
    
    // Row 5: Commission Earned
    $pdf->Cell(60, 8, 'Commission Earned', 1, 0, 'L');
    $pdf->Cell(40, 8, 'KES ' . number_format($commissionAmount, 2), 1, 0, 'C');
    $pdf->Cell(80, 8, 'Commission from produce sales (10%)', 1, 1, 'L');
    
    $pdf->Ln(5);
    
    // ===== MEMBER INFORMATION =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 8, 'MEMBER ENGAGEMENT', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Get additional member statistics
    $memberStatsQuery = "SELECT 
                        COUNT(DISTINCT f.id) as total_farmers,
                        COUNT(DISTINCT fa.id) as accounts_with_balance,
                        SUM(fa.balance) as total_farmer_balances
                      FROM farmers f
                      LEFT JOIN farmer_accounts fa ON f.id = fa.farmer_id";
    $memberStats = $app->selectOne($memberStatsQuery);
    
    // Add the green header row
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('helvetica', 'B', 10);
    
    // Member metrics table
    $pdf->Cell(60, 8, 'Metric', 1, 0, 'L', true);
    $pdf->Cell(40, 8, 'Value', 1, 0, 'C', true);
    $pdf->Cell(80, 8, 'Notes', 1, 1, 'C', true);
    
    // Table data
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    
    // Row 1: Total Farmers
    $pdf->Cell(60, 8, 'Total Farmers', 1, 0, 'L');
    $pdf->Cell(40, 8, ($memberStats->total_farmers ?? 0), 1, 0, 'C');
    $pdf->Cell(80, 8, 'Total registered farmers', 1, 1, 'L');
    
    // Row 2: Active Farmers
    $pdf->Cell(60, 8, 'Active Farmers', 1, 0, 'L');
    $pdf->Cell(40, 8, ($activeFarmers->active_farmers ?? 0), 1, 0, 'C');
    $pdf->Cell(80, 8, 'Farmers active in last 6 months', 1, 1, 'L');
    
    // Row 3: Farmer Accounts with Balance
    $pdf->Cell(60, 8, 'Accounts with Balance', 1, 0, 'L');
    $pdf->Cell(40, 8, ($memberStats->accounts_with_balance ?? 0), 1, 0, 'C');
    $pdf->Cell(80, 8, 'Farmer accounts with non-zero balance', 1, 1, 'L');
    
    // Row 4: Total Farmer Account Balances
    $pdf->Cell(60, 8, 'Farmer Account Balances', 1, 0, 'L');
    $totalFarmerBalances = ($memberStats->total_farmer_balances ?? 0);
    $pdf->Cell(40, 8, 'KES ' . number_format($totalFarmerBalances, 2), 1, 0, 'C');
    $pdf->Cell(80, 8, 'Total balance in farmer accounts', 1, 1, 'L');
    
    $pdf->Ln(5);
    // ===== FINANCIAL SUMMARY =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 8, 'FINANCIAL SUMMARY', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Calculate financial summary
    $totalIncome = $commissionAmount + $processingFees;
    $totalAssets = $saccoBalance + $totalLoanValue - $totalRepaid;
    
    // Add the green header row for summary
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(100, 8, 'Income Sources', 1, 0, 'L', true);
    $pdf->Cell(80, 8, 'Amount (KES)', 1, 1, 'R', true);
    
    // Income summary table
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(100, 8, 'Commission from Produce Sales', 1, 0, 'L');
    $pdf->Cell(80, 8, number_format($commissionAmount, 2), 1, 1, 'R');
    
    $pdf->Cell(100, 8, 'Loan Processing Fees', 1, 0, 'L');
    $pdf->Cell(80, 8, number_format($processingFees, 2), 1, 1, 'R');
    
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(100, 8, 'Total Income', 1, 0, 'L', true);
    $pdf->Cell(80, 8, number_format($totalIncome, 2), 1, 1, 'R', true);
    
    $pdf->Ln(5);
    
    // Asset summary
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(100, 8, 'Asset Category', 1, 0, 'L', true);
    $pdf->Cell(80, 8, 'Amount (KES)', 1, 1, 'R', true);
    
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(100, 8, 'Cash (SACCO Account Balance)', 1, 0, 'L');
    $pdf->Cell(80, 8, number_format($saccoBalance, 2), 1, 1, 'R');
    
    $pdf->Cell(100, 8, 'Outstanding Loans (Principal)', 1, 0, 'L');
    $outstandingLoans = $totalLoanValue - $totalRepaid;
    $pdf->Cell(80, 8, number_format($outstandingLoans, 2), 1, 1, 'R');
    
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(100, 8, 'Total Assets', 1, 0, 'L', true);
    $pdf->Cell(80, 8, number_format($totalAssets, 2), 1, 1, 'R', true);
    
    $pdf->Ln(5);
    
    // ===== SUMMARY NOTES =====
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 8, 'REPORT SUMMARY', 0, 1, 'L');
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    
    // Get summary notes
    $loanApprovalTrend = ($approvalRate >= 70) ? "healthy" : (($approvalRate >= 50) ? "moderate" : "low");
    $commissionPercentage = ($totalIncome > 0) ? ($commissionAmount / $totalIncome * 100) : 0;
    
    $summaryNotes = "This report provides an overview of the SACCO operations as of $formattedDate. ";
    $summaryNotes .= "The SACCO has processed " . ($loanStats->total_applications ?? 0) . " loan applications with a $loanApprovalTrend approval rate of " . number_format($approvalRate, 2) . "%. ";
    $summaryNotes .= "Produce operations have generated KES " . number_format($commissionAmount, 2) . " in commissions, representing " . number_format($commissionPercentage, 2) . "% of total income. ";
    $summaryNotes .= "The current SACCO account balance stands at KES " . number_format($saccoBalance, 2) . ", with KES " . number_format($outstandingLoans, 2) . " in outstanding loans.";
    
    $pdf->MultiCell(0, 6, $summaryNotes, 0, 'L');
    $pdf->Ln(3);
    
    // Add recommendations based on data
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, 'Recommendations:', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 10);
    
    $recommendations = array();
    
    // Generate recommendations based on data
    if ($approvalRate < 50) {
        $recommendations[] = "Review loan approval criteria as current approval rate is low at " . number_format($approvalRate, 2) . "%.";
    }
    
    if ($outstandingLoans > $saccoBalance * 2) {
        $recommendations[] = "Monitor outstanding loans which currently represent a high proportion of assets.";
    }
    
    if ($commissionAmount < $processingFees) {
        $recommendations[] = "Consider strategies to increase produce sales to improve commission revenue.";
    }
    
    if (count($recommendations) == 0) {
        $recommendations[] = "Current operations are running efficiently. Maintain the current strategy.";
    }
    
    foreach ($recommendations as $recommendation) {
        $pdf->MultiCell(0, 6, "• " . $recommendation, 0, 'L');
    }
    
    // ===== VERIFICATION QR CODE =====
    $pdf->Ln(5);
    
    // Generate report verification data for QR code
    $qrData = "SACCO Operations Report\n" .
              "Date: $formattedDate\n" .
              "Balance: KES " . number_format($saccoBalance, 2) . "\n" .
              "Loans: " . ($loanStats->total_applications ?? 0) . "\n" .
              "Produce: " . ($produceStats->delivery_count ?? 0);
    
    // Add QR code
    $pdf->write2DBarcode(
        $qrData,
        'QRCODE,L',
        15,
        $pdf->GetY(),
        30,
        30,
        [
            'border' => false,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => [0, 0, 0],
            'bgcolor' => false,
            'module_width' => 1,
            'module_height' => 1
        ]
    );
    
    // Add verification text next to QR code
    $pdf->SetXY(50, $pdf->GetY());
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(0, 4, 'Scan QR code to verify report authenticity', 0, 1, 'L');
    $pdf->SetX(50);
    $pdf->Cell(0, 4, 'or visit our portal and enter report reference number', 0, 1, 'L');
    
    // ===== FOOTER =====
    $pdf->Ln(10);
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->Cell(0, 6, 'This is a computer-generated report and does not require a signature.', 0, 1, 'C');
    $pdf->Cell(0, 6, 'For questions or concerns, please contact DFCS support.', 0, 1, 'C');
    
    // Output the PDF
    $pdf->Output('SACCO_Operations_Report.pdf', 'I');
    exit;

} catch (Exception $e) {
    // Clean output buffer
    if (ob_get_length()) ob_clean();
    
    // Return error as JSON
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to generate report: ' . $e->getMessage()
    ]);
    exit;
}
?>