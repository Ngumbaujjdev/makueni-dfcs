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
    
    // Section 1: Loan Portfolio Summary
    $portfolioQuery = "SELECT 
                    COUNT(la.id) as total_applications,
                    COUNT(al.id) as total_approved_loans,
                    COUNT(CASE WHEN al.status = 'active' THEN 1 END) as active_loans,
                    COUNT(CASE WHEN al.status = 'completed' THEN 1 END) as completed_loans,
                    COUNT(CASE WHEN al.status = 'defaulted' THEN 1 END) as defaulted_loans,
                    COUNT(CASE WHEN al.status = 'pending_disbursement' THEN 1 END) as pending_disbursement,
                    COALESCE(SUM(al.approved_amount), 0) as total_approved_amount,
                    COALESCE(SUM(al.remaining_balance), 0) as total_outstanding_balance,
                    COALESCE(AVG(al.approved_amount), 0) as avg_loan_amount,
                    COALESCE(AVG(al.approved_term), 0) as avg_term_months,
                    COALESCE(AVG(al.interest_rate), 0) as avg_interest_rate
                  FROM loan_applications la
                  LEFT JOIN approved_loans al ON la.id = al.loan_application_id
                  WHERE la.bank_id = :bank_id
                  AND la.application_date BETWEEN :start_date AND :end_date";
    
    $portfolio = $app->selectOne($portfolioQuery, [
        ':bank_id' => $staff->bank_id,
        ':start_date' => $startDate . ' 00:00:00',
        ':end_date' => $endDate . ' 23:59:59'
    ]);
    
    // Calculate approval rate
    $approvalRate = $portfolio->total_applications > 0 ? 
        ($portfolio->total_approved_loans / $portfolio->total_applications) * 100 : 0;
    
    // Section 2: Application Pipeline Analysis
    $pipelineQuery = "SELECT 
                status,
                COUNT(*) as count,
                AVG(CASE WHEN reviewed_by IS NOT NULL THEN 
                    TIMESTAMPDIFF(DAY, application_date, review_date) 
                END) as avg_processing_days
              FROM loan_applications 
              WHERE bank_id = '{$staff->bank_id}'
              AND application_date BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'
              GROUP BY status
              ORDER BY count DESC";
    
    $pipeline = $app->select_all($pipelineQuery);
    
    // Section 3: Farmer Segment Analysis
    $farmerSegmentQuery = "SELECT 
                fc.name as category_name,
                COUNT(la.id) as applications,
                COUNT(al.id) as approved_loans,
                COALESCE(SUM(al.approved_amount), 0) as total_amount,
                COALESCE(AVG(la.creditworthiness_score), 0) as avg_creditworthiness,
                COUNT(CASE WHEN al.status = 'defaulted' THEN 1 END) as defaults
              FROM loan_applications la
              JOIN farmers f ON la.farmer_id = f.id
              LEFT JOIN farmer_categories fc ON f.category_id = fc.id
              LEFT JOIN approved_loans al ON la.id = al.loan_application_id
              WHERE la.bank_id = '{$staff->bank_id}'
              AND la.application_date BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'
              GROUP BY fc.id, fc.name
              ORDER BY total_amount DESC";
    
    $farmerSegments = $app->select_all($farmerSegmentQuery);
    
    // Section 4: Financial Performance
    $financialQuery = "SELECT 
                COALESCE(SUM(al.total_repayment_amount - al.approved_amount), 0) as total_interest_revenue,
                COALESCE(SUM(al.processing_fee), 0) as total_processing_fees,
                COALESCE(SUM(lr.amount), 0) as total_repayments_received,
                COUNT(lr.id) as total_repayment_transactions
              FROM approved_loans al
              LEFT JOIN loan_repayments lr ON al.id = lr.approved_loan_id
              WHERE al.bank_id = '{$staff->bank_id}'
              AND al.approval_date BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'";
    
    $financial = $app->selectOne($financialQuery);
    
    // Section 5: Recent Loan Activities
    $recentLoansQuery = "SELECT 
                la.id,
                CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                fc.name as farmer_category,
                lt.name as loan_type,
                al.approved_amount,
                al.approved_term,
                al.interest_rate,
                al.remaining_balance,
                al.status,
                al.disbursement_date,
                al.expected_completion_date
              FROM loan_applications la
              JOIN farmers f ON la.farmer_id = f.id
              JOIN users u ON f.user_id = u.id
              LEFT JOIN farmer_categories fc ON f.category_id = fc.id
              JOIN loan_types lt ON la.loan_type_id = lt.id
              LEFT JOIN approved_loans al ON la.id = al.loan_application_id
              WHERE la.bank_id = '{$staff->bank_id}'
              AND la.application_date BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'
              ORDER BY la.application_date DESC
              LIMIT 25";
    
    $recentLoans = $app->select_all($recentLoansQuery);
    
    // Section 6: Repayment Analysis
    $repaymentQuery = "SELECT 
                COUNT(lr.id) as total_repayments,
                COALESCE(SUM(lr.amount), 0) as total_repayment_amount,
                COALESCE(AVG(lr.amount), 0) as avg_repayment_amount,
                COUNT(DISTINCT lr.approved_loan_id) as loans_with_repayments
              FROM loan_repayments lr
              JOIN approved_loans al ON lr.approved_loan_id = al.id
              WHERE al.bank_id = '{$staff->bank_id}'
              AND lr.payment_date BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'";
    
    $repayments = $app->selectOne($repaymentQuery);
    
    // Create new PDF document
    $pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('DFCS System');
    $pdf->SetAuthor('Makueni DFCS');
    $pdf->SetTitle('Bank Loan Report');
    $pdf->SetSubject('Bank Loan Report for ' . $staff->bank_name);
    
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
    $pdf->Cell(0, 10, 'BANK LOAN PORTFOLIO REPORT', 0, 1, 'C');
    
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
    
    // ===== SECTION 1: LOAN PORTFOLIO SUMMARY =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '1. LOAN PORTFOLIO SUMMARY', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Create a 2x2 grid of summary boxes
    $boxWidth = 85;
    $boxHeight = 40;
    $margin = 10;
    $startY = $pdf->GetY();
    
    // Box styling
    $pdf->SetDrawColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetLineWidth(0.5);
    
    // First row - Total Applications and Approval Rate
    // Total Applications box
    $pdf->Rect(15, $startY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY(20, $startY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 6, 'Total Applications', 0, 1);
    $pdf->SetFont('helvetica', 'B', 18);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY(20, $pdf->GetY() + 2);
    $pdf->Cell($boxWidth - 10, 10, number_format($portfolio->total_applications), 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY(20, $pdf->GetY());
    $pdf->Cell($boxWidth - 10, 5, 'Approved: ' . number_format($portfolio->total_approved_loans), 0, 1);
    
    // Approval Rate box
    $rightColX = 15 + $boxWidth + $margin;
    $pdf->Rect($rightColX, $startY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY($rightColX + 5, $startY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 6, 'Approval Rate', 0, 1);
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetTextColor($successColor[0], $successColor[1], $successColor[2]);
    $pdf->SetXY($rightColX + 5, $startY + 13);
    $pdf->Cell($boxWidth - 10, 10, number_format($approvalRate, 1) . '%', 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY($rightColX + 5, $startY + 25);
    $pdf->Cell($boxWidth - 10, 5, 'Active: ' . number_format($portfolio->active_loans), 0, 1);
    
    // Second row - Total Portfolio Value and Outstanding Balance
    $secondRowY = $startY + $boxHeight + 10;
    
    // Portfolio Value box
    $pdf->Rect(15, $secondRowY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY(20, $secondRowY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 6, 'Portfolio Value', 0, 1);
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($infoColor[0], $infoColor[1], $infoColor[2]);
    $pdf->SetXY(20, $secondRowY + 13);
    $pdf->Cell($boxWidth - 10, 10, 'KES ' . number_format($portfolio->total_approved_amount, 0), 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY(20, $secondRowY + 25);
    $pdf->Cell($boxWidth - 10, 5, 'Avg: KES ' . number_format($portfolio->avg_loan_amount, 0), 0, 1);
    
    // Outstanding Balance box
    $pdf->Rect($rightColX, $secondRowY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY($rightColX + 5, $secondRowY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 6, 'Outstanding Balance', 0, 1);
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($warningColor[0], $warningColor[1], $warningColor[2]);
    $pdf->SetXY($rightColX + 5, $secondRowY + 13);
    $pdf->Cell($boxWidth - 10, 10, 'KES ' . number_format($portfolio->total_outstanding_balance, 0), 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY($rightColX + 5, $secondRowY + 25);
    $collectionRate = $portfolio->total_approved_amount > 0 ? 
        (($portfolio->total_approved_amount - $portfolio->total_outstanding_balance) / $portfolio->total_approved_amount) * 100 : 0;
    $pdf->Cell($boxWidth - 10, 5, 'Collection: ' . number_format($collectionRate, 1) . '%', 0, 1);
    
    // Set Y position after summary boxes
    $pdf->SetY($secondRowY + $boxHeight + 10);
    
    $pdf->Ln(5);
    
    // ===== SECTION 2: APPLICATION PIPELINE ANALYSIS =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '2. APPLICATION PIPELINE ANALYSIS', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Pipeline table
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    
    // Table header
    $pdf->Cell(50, 7, 'Status', 1, 0, 'C', true);
    $pdf->Cell(30, 7, 'Applications', 1, 0, 'C', true);
    $pdf->Cell(25, 7, 'Percentage', 1, 0, 'C', true);
    $pdf->Cell(25, 7, 'Avg Days', 1, 1, 'C', true);
    
    // Reset styling for table rows
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->SetFillColor(245, 245, 245);
    
    // Pipeline rows
    if ($pipeline) {
        foreach ($pipeline as $index => $status) {
            $fillRow = $index % 2 == 0;
            $percentage = $portfolio->total_applications > 0 ? 
                ($status->count / $portfolio->total_applications) * 100 : 0;
            
            $pdf->Cell(50, 5, ucwords(str_replace('_', ' ', $status->status)), 1, 0, 'L', $fillRow);
            $pdf->Cell(30, 5, number_format($status->count), 1, 0, 'C', $fillRow);
            $pdf->Cell(25, 5, number_format($percentage, 1) . '%', 1, 0, 'C', $fillRow);
            $pdf->Cell(25, 5, $status->avg_processing_days ? number_format($status->avg_processing_days, 1) : '-', 1, 1, 'C', $fillRow);
        }
    } else {
        $pdf->Cell(130, 6, 'No applications found in the selected period', 1, 1, 'C', true);
    }
    
    $pdf->Ln(5);
    
    // ===== SECTION 3: FARMER SEGMENT ANALYSIS =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '3. FARMER SEGMENT ANALYSIS', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Farmer segment table
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    
    // Table header
    $pdf->Cell(35, 7, 'Farmer Category', 1, 0, 'C', true);
    $pdf->Cell(20, 7, 'Apps', 1, 0, 'C', true);
    $pdf->Cell(20, 7, 'Approved', 1, 0, 'C', true);
    $pdf->Cell(30, 7, 'Total Amount', 1, 0, 'C', true);
    $pdf->Cell(25, 7, 'Avg Score', 1, 0, 'C', true);
    $pdf->Cell(20, 7, 'Defaults', 1, 1, 'C', true);
    
    // Reset styling for table rows
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 7);
    $pdf->SetFillColor(245, 245, 245);
    
    // Farmer segment rows
    if ($farmerSegments) {
        foreach ($farmerSegments as $index => $segment) {
            $fillRow = $index % 2 == 0;
            $defaultRate = $segment->approved_loans > 0 ? ($segment->defaults / $segment->approved_loans) * 100 : 0;
            
            $pdf->Cell(35, 5, $segment->category_name ?? 'Uncategorized', 1, 0, 'L', $fillRow);
            $pdf->Cell(20, 5, number_format($segment->applications), 1, 0, 'C', $fillRow);
            $pdf->Cell(20, 5, number_format($segment->approved_loans), 1, 0, 'C', $fillRow);
            $pdf->Cell(30, 5, number_format($segment->total_amount/1000, 0) . 'K', 1, 0, 'R', $fillRow);
            $pdf->Cell(25, 5, number_format($segment->avg_creditworthiness, 1), 1, 0, 'C', $fillRow);
            $pdf->SetTextColor($segment->defaults > 0 ? $dangerColor[0] : 0, $segment->defaults > 0 ? $dangerColor[1] : 0, $segment->defaults > 0 ? $dangerColor[2] : 0);
            $pdf->Cell(20, 5, $segment->defaults . ' (' . number_format($defaultRate, 1) . '%)', 1, 1, 'C', $fillRow);
            $pdf->SetTextColor(0, 0, 0);
        }
    } else {
        $pdf->Cell(150, 6, 'No farmer segment data found', 1, 1, 'C', true);
    }
    
    $pdf->Ln(5);
    
    // ===== SECTION 5: RECENT LOAN ACTIVITIES =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '5. RECENT LOAN ACTIVITIES (Last 25)', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Recent loans table
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    
    // Table header - more compact columns
    $pdf->Cell(12, 7, 'ID', 1, 0, 'C', true);
    $pdf->Cell(35, 7, 'Farmer', 1, 0, 'C', true);
    $pdf->Cell(22, 7, 'Category', 1, 0, 'C', true);
    $pdf->Cell(28, 7, 'Amount', 1, 0, 'C', true);
    $pdf->Cell(15, 7, 'Term', 1, 0, 'C', true);
    $pdf->Cell(15, 7, 'Rate', 1, 0, 'C', true);
    $pdf->Cell(28, 7, 'Status', 1, 0, 'C', true);
    $pdf->Cell(25, 7, 'Balance', 1, 1, 'C', true);
    
    // Reset styling for table rows
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 7);
    
    // Recent loans rows - show only first 15 to fit on page
    if ($recentLoans) {
        $displayLoans = array_slice($recentLoans, 0, 15);
        foreach ($displayLoans as $index => $loan) {
            $fillRow = $index % 2 == 0;
            if ($fillRow) {
                $pdf->SetFillColor(245, 245, 245);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            
            $pdf->Cell(12, 5, 'L' . str_pad($loan->id, 3, '0', STR_PAD_LEFT), 1, 0, 'C', $fillRow);
            $pdf->Cell(35, 5, substr($loan->farmer_name, 0, 18), 1, 0, 'L', $fillRow);
            $pdf->Cell(22, 5, substr($loan->farmer_category ?? 'N/A', 0, 10), 1, 0, 'C', $fillRow);
            $pdf->Cell(28, 5, $loan->approved_amount ? number_format($loan->approved_amount/1000, 0) . 'K' : '-', 1, 0, 'R', $fillRow);
            $pdf->Cell(15, 5, $loan->approved_term ? $loan->approved_term . 'M' : '-', 1, 0, 'C', $fillRow);
            $pdf->Cell(15, 5, $loan->interest_rate ? number_format($loan->interest_rate, 1) . '%' : '-', 1, 0, 'C', $fillRow);
            
            // Status with color
            $statusColor = [0, 0, 0];
            if ($loan->status == 'active') $statusColor = $successColor;
            elseif ($loan->status == 'defaulted') $statusColor = $dangerColor;
            elseif ($loan->status == 'completed') $statusColor = $infoColor;
            elseif ($loan->status == 'pending_disbursement') $statusColor = $warningColor;
            
            $pdf->SetTextColor($statusColor[0], $statusColor[1], $statusColor[2]);
            $statusText = str_replace('_', ' ', $loan->status ?? 'pending');
            $pdf->Cell(28, 5, ucwords(substr($statusText, 0, 12)), 1, 0, 'C', $fillRow);
            $pdf->SetTextColor(0, 0, 0);
            
            $pdf->Cell(25, 5, $loan->remaining_balance ? number_format($loan->remaining_balance/1000, 0) . 'K' : '-', 1, 1, 'R', $fillRow);
        }
    } else {
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(180, 6, 'No recent loan activities found', 1, 1, 'C', true);
    }
    
    $pdf->Ln(5);
    
    // Check if we need a new page for findings section
    if ($pdf->GetY() > 220) {
        $pdf->AddPage();
    }
    
    // ===== SECTION 6: FINDINGS & RECOMMENDATIONS =====
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '6. FINDINGS & RECOMMENDATIONS', 0, 1, 'L');
    $pdf->Ln(3);
    
    // Calculate key metrics for insights
    $defaultRate = $portfolio->total_approved_loans > 0 ? 
        ($portfolio->defaulted_loans / $portfolio->total_approved_loans) * 100 : 0;
    
    $repaymentRate = $repayments->loans_with_repayments > 0 ? 
        ($repayments->loans_with_repayments / $portfolio->active_loans) * 100 : 0;
    
    // FINDINGS
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor($infoColor[0], $infoColor[1], $infoColor[2]);
    $pdf->Cell(0, 7, 'KEY FINDINGS:', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(0, 0, 0);
    
    $findings = [
        "Portfolio approval rate of " . number_format($approvalRate, 1) . "% indicates " . 
        ($approvalRate > 70 ? "strong lending standards" : "potential for increased approvals"),
        
        "Default rate of " . number_format($defaultRate, 1) . "% " . 
        ($defaultRate < 5 ? "demonstrates excellent risk management" : 
         ($defaultRate < 10 ? "shows acceptable risk levels" : "requires immediate attention")),
        
        "Average loan amount of KES " . number_format($portfolio->avg_loan_amount, 0) . " suggests " . 
        ($portfolio->avg_loan_amount > 100000 ? "focus on larger commercial farmers" : "support for smallholder farmers"),
        
        "Outstanding balance of KES " . number_format($portfolio->total_outstanding_balance, 0) . " represents " . 
        number_format($collectionRate, 1) . "% collection efficiency"
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
    
    if ($approvalRate < 50) {
        $recommendations[] = "Review loan criteria to increase approval rate and capture more market share";
    }
    
    if ($defaultRate > 10) {
        $recommendations[] = "Implement enhanced credit scoring and risk assessment protocols";
        $recommendations[] = "Establish early intervention programs for at-risk borrowers";
    }
    
    if ($portfolio->active_loans > 0 && $repayments->total_repayments < $portfolio->active_loans * 0.5) {
        $recommendations[] = "Strengthen collection processes and borrower follow-up systems";
    }
    
    if ($portfolio->avg_loan_amount < 50000) {
        $recommendations[] = "Consider product diversification to attract larger commercial clients";
    }
    
    // Default recommendations if none specific
    if (empty($recommendations)) {
        $recommendations = [
            "Continue current risk management practices given strong performance metrics",
            "Explore opportunities for portfolio expansion in underserved segments",
            "Implement digital lending solutions to improve operational efficiency",
            "Develop farmer education programs to improve repayment rates"
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
    $pdf->Output('Bank_Loan_Report_' . $startDate . '_to_' . $endDate . '.pdf', 'I');
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