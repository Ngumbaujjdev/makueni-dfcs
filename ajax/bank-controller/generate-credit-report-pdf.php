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
    
    // Section 1: Credit Portfolio Summary
    $portfolioQuery = "SELECT 
                    COUNT(ica.id) as total_applications,
                    COUNT(aic.id) as total_approved_credits,
                    COUNT(CASE WHEN aic.status = 'active' THEN 1 END) as active_credits,
                    COUNT(CASE WHEN aic.status = 'completed' THEN 1 END) as completed_credits,
                    COUNT(CASE WHEN aic.status = 'defaulted' THEN 1 END) as defaulted_credits,
                    COUNT(CASE WHEN aic.status = 'pending_fulfillment' THEN 1 END) as pending_fulfillment,
                    COALESCE(SUM(aic.approved_amount), 0) as total_approved_amount,
                    COALESCE(SUM(aic.remaining_balance), 0) as total_outstanding_balance,
                    COALESCE(AVG(aic.approved_amount), 0) as avg_credit_amount,
                    COALESCE(AVG(aic.credit_percentage), 0) as avg_interest_rate,
                    COALESCE(AVG(ica.creditworthiness_score), 0) as avg_creditworthiness
                  FROM input_credit_applications ica
                  LEFT JOIN approved_input_credits aic ON ica.id = aic.credit_application_id
                  JOIN farmers f ON ica.farmer_id = f.id
                  JOIN loan_applications la ON f.id = la.farmer_id
                  WHERE la.bank_id = :bank_id
                  AND ica.application_date BETWEEN :start_date AND :end_date";
    
    $portfolio = $app->selectOne($portfolioQuery, [
        ':bank_id' => $staff->bank_id,
        ':start_date' => $startDate . ' 00:00:00',
        ':end_date' => $endDate . ' 23:59:59'
    ]);
    
    // Calculate approval rate
    $approvalRate = $portfolio->total_applications > 0 ? 
        ($portfolio->total_approved_credits / $portfolio->total_applications) * 100 : 0;
    
    // Section 2: Application Pipeline Analysis
    $pipelineQuery = "SELECT 
                ica.status,
                COUNT(*) as count,
                AVG(CASE WHEN ica.reviewed_by IS NOT NULL THEN 
                    TIMESTAMPDIFF(DAY, ica.application_date, ica.review_date) 
                END) as avg_processing_days
              FROM input_credit_applications ica
              JOIN farmers f ON ica.farmer_id = f.id
              JOIN loan_applications la ON f.id = la.farmer_id 
              WHERE la.bank_id = '{$staff->bank_id}'
              AND ica.application_date BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'
              GROUP BY ica.status
              ORDER BY count DESC";
    
    $pipeline = $app->select_all($pipelineQuery);
    
    // Section 3: Farmer Segment Analysis
    $farmerSegmentQuery = "SELECT 
                fc.name as category_name,
                COUNT(ica.id) as applications,
                COUNT(aic.id) as approved_credits,
                COALESCE(SUM(aic.approved_amount), 0) as total_amount,
                COALESCE(AVG(ica.creditworthiness_score), 0) as avg_creditworthiness,
                COUNT(CASE WHEN aic.status = 'defaulted' THEN 1 END) as defaults,
                COALESCE(AVG(aic.repayment_percentage), 0) as avg_repayment_rate
              FROM input_credit_applications ica
              JOIN farmers f ON ica.farmer_id = f.id
              LEFT JOIN farmer_categories fc ON f.category_id = fc.id
              LEFT JOIN approved_input_credits aic ON ica.id = aic.credit_application_id
              JOIN loan_applications la ON f.id = la.farmer_id
              WHERE la.bank_id = '{$staff->bank_id}'
              AND ica.application_date BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'
              GROUP BY fc.id, fc.name
              ORDER BY total_amount DESC";
    
    $farmerSegments = $app->select_all($farmerSegmentQuery);
    
    // Section 4: Agrovet Partnership Analysis
    $agrovetQuery = "SELECT 
                a.name as agrovet_name,
                COUNT(ica.id) as applications,
                COUNT(aic.id) as approved_credits,
                COALESCE(SUM(aic.approved_amount), 0) as total_amount,
                COALESCE(AVG(ica.creditworthiness_score), 0) as avg_creditworthiness,
                COUNT(CASE WHEN aic.status = 'defaulted' THEN 1 END) as defaults
              FROM input_credit_applications ica
              JOIN agrovets a ON ica.agrovet_id = a.id
              LEFT JOIN approved_input_credits aic ON ica.id = aic.credit_application_id
              JOIN farmers f ON ica.farmer_id = f.id
              JOIN loan_applications la ON f.id = la.farmer_id
              WHERE la.bank_id = '{$staff->bank_id}'
              AND ica.application_date BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'
              GROUP BY a.id, a.name
              ORDER BY total_amount DESC
              LIMIT 10";
    
    $agrovetPartners = $app->select_all($agrovetQuery);
    
    // Section 5: Recent Credit Activities
    $recentCreditsQuery = "SELECT 
                ica.id,
                CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                fc.name as farmer_category,
                a.name as agrovet_name,
                aic.approved_amount,
                aic.credit_percentage,
                aic.repayment_percentage,
                aic.remaining_balance,
                aic.status,
                aic.fulfillment_date,
                aic.approval_date
              FROM input_credit_applications ica
              JOIN farmers f ON ica.farmer_id = f.id
              JOIN users u ON f.user_id = u.id
              LEFT JOIN farmer_categories fc ON f.category_id = fc.id
              JOIN agrovets a ON ica.agrovet_id = a.id
              LEFT JOIN approved_input_credits aic ON ica.id = aic.credit_application_id
              JOIN loan_applications la ON f.id = la.farmer_id
              WHERE la.bank_id = '{$staff->bank_id}'
              AND ica.application_date BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'
              ORDER BY ica.application_date DESC
              LIMIT 25";
    
    $recentCredits = $app->select_all($recentCreditsQuery);
    
    // Section 6: Repayment Performance
    $repaymentQuery = "SELECT 
                COUNT(icr.id) as total_repayments,
                COALESCE(SUM(icr.amount), 0) as total_repayment_amount,
                COALESCE(AVG(icr.amount), 0) as avg_repayment_amount,
                COUNT(DISTINCT icr.approved_credit_id) as credits_with_repayments
              FROM input_credit_repayments icr
              JOIN approved_input_credits aic ON icr.approved_credit_id = aic.id
              JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
              JOIN farmers f ON ica.farmer_id = f.id
              JOIN loan_applications la ON f.id = la.farmer_id
              WHERE la.bank_id = '{$staff->bank_id}'
              AND icr.deduction_date BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'";
    
    $repayments = $app->selectOne($repaymentQuery);
    
    // Create new PDF document
    $pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('DFCS System');
    $pdf->SetAuthor('Makueni DFCS');
    $pdf->SetTitle('Bank Credit Report');
    $pdf->SetSubject('Bank Credit Report for ' . $staff->bank_name);
    
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
    $pdf->Cell(0, 10, 'BANK CREDIT PORTFOLIO REPORT', 0, 1, 'C');
    
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
    
    // ===== SECTION 1: CREDIT PORTFOLIO SUMMARY =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '1. INPUT CREDIT PORTFOLIO SUMMARY', 0, 1, 'L');
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
    $pdf->Cell($boxWidth - 10, 6, 'Total Credit Applications', 0, 1);
    $pdf->SetFont('helvetica', 'B', 18);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY(20, $pdf->GetY() + 2);
    $pdf->Cell($boxWidth - 10, 10, number_format($portfolio->total_applications), 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY(20, $pdf->GetY());
    $pdf->Cell($boxWidth - 10, 5, 'Approved: ' . number_format($portfolio->total_approved_credits), 0, 1);
    
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
    $pdf->Cell($boxWidth - 10, 5, 'Active: ' . number_format($portfolio->active_credits), 0, 1);
    
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
    $pdf->Cell($boxWidth - 10, 5, 'Avg: KES ' . number_format($portfolio->avg_credit_amount, 0), 0, 1);
    
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
            $defaultRate = $segment->approved_credits > 0 ? ($segment->defaults / $segment->approved_credits) * 100 : 0;
            
            $pdf->Cell(35, 5, $segment->category_name ?? 'Uncategorized', 1, 0, 'L', $fillRow);
            $pdf->Cell(20, 5, number_format($segment->applications), 1, 0, 'C', $fillRow);
            $pdf->Cell(20, 5, number_format($segment->approved_credits), 1, 0, 'C', $fillRow);
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
    
    // ===== SECTION 4: AGROVET PARTNERSHIP ANALYSIS =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '4. TOP AGROVET PARTNERSHIPS', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Agrovet partnership table
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    
    // Table header
    $pdf->Cell(40, 7, 'Agrovet Name', 1, 0, 'C', true);
    $pdf->Cell(20, 7, 'Apps', 1, 0, 'C', true);
    $pdf->Cell(20, 7, 'Approved', 1, 0, 'C', true);
    $pdf->Cell(30, 7, 'Total Amount', 1, 0, 'C', true);
    $pdf->Cell(25, 7, 'Avg Score', 1, 0, 'C', true);
    $pdf->Cell(15, 7, 'Defaults', 1, 1, 'C', true);
    
    // Reset styling for table rows
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 7);
    $pdf->SetFillColor(245, 245, 245);
    
    // Agrovet partnership rows
    if ($agrovetPartners) {
        foreach ($agrovetPartners as $index => $partner) {
            $fillRow = $index % 2 == 0;
            $defaultRate = $partner->approved_credits > 0 ? ($partner->defaults / $partner->approved_credits) * 100 : 0;
            
            $pdf->Cell(40, 5, substr($partner->agrovet_name, 0, 20), 1, 0, 'L', $fillRow);
            $pdf->Cell(20, 5, number_format($partner->applications), 1, 0, 'C', $fillRow);
            $pdf->Cell(20, 5, number_format($partner->approved_credits), 1, 0, 'C', $fillRow);
            $pdf->Cell(30, 5, number_format($partner->total_amount/1000, 0) . 'K', 1, 0, 'R', $fillRow);
            $pdf->Cell(25, 5, number_format($partner->avg_creditworthiness, 1), 1, 0, 'C', $fillRow);
            $pdf->SetTextColor($partner->defaults > 0 ? $dangerColor[0] : 0, $partner->defaults > 0 ? $dangerColor[1] : 0, $partner->defaults > 0 ? $dangerColor[2] : 0);
            $pdf->Cell(15, 5, $partner->defaults, 1, 1, 'C', $fillRow);
            $pdf->SetTextColor(0, 0, 0);
        }
    } else {
        $pdf->Cell(150, 6, 'No agrovet partnership data found', 1, 1, 'C', true);
    }
    
    $pdf->Ln(5);
    
    // ===== SECTION 5: RECENT CREDIT ACTIVITIES =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '5. RECENT CREDIT ACTIVITIES (Last 25)', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Recent credits table
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    
    // Table header - more compact columns
    $pdf->Cell(12, 7, 'ID', 1, 0, 'C', true);
    $pdf->Cell(30, 7, 'Farmer', 1, 0, 'C', true);
    $pdf->Cell(25, 7, 'Agrovet', 1, 0, 'C', true);
    $pdf->Cell(25, 7, 'Amount', 1, 0, 'C', true);
    $pdf->Cell(15, 7, 'Rate', 1, 0, 'C', true);
    $pdf->Cell(25, 7, 'Status', 1, 0, 'C', true);
    $pdf->Cell(23, 7, 'Balance', 1, 1, 'C', true);
    
    // Reset styling for table rows
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 7);
    
    // Recent credits rows - show only first 15 to fit on page
    if ($recentCredits) {
        $displayCredits = array_slice($recentCredits, 0, 15);
        foreach ($displayCredits as $index => $credit) {
            $fillRow = $index % 2 == 0;
            if ($fillRow) {
                $pdf->SetFillColor(245, 245, 245);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            
            $pdf->Cell(12, 5, 'C' . str_pad($credit->id, 3, '0', STR_PAD_LEFT), 1, 0, 'C', $fillRow);
            $pdf->Cell(30, 5, substr($credit->farmer_name, 0, 15), 1, 0, 'L', $fillRow);
            $pdf->Cell(25, 5, substr($credit->agrovet_name, 0, 12), 1, 0, 'L', $fillRow);
            $pdf->Cell(25, 5, $credit->approved_amount ? number_format($credit->approved_amount/1000, 0) . 'K' : '-', 1, 0, 'R', $fillRow);
            $pdf->Cell(15, 5, $credit->credit_percentage ? number_format($credit->credit_percentage, 1) . '%' : '-', 1, 0, 'C', $fillRow);
            
            // Status with color
            $statusColor = [0, 0, 0];
            if ($credit->status == 'active') $statusColor = $successColor;
            elseif ($credit->status == 'defaulted') $statusColor = $dangerColor;
            elseif ($credit->status == 'completed') $statusColor = $infoColor;
            elseif ($credit->status == 'pending_fulfillment') $statusColor = $warningColor;
            
            $pdf->SetTextColor($statusColor[0], $statusColor[1], $statusColor[2]);
            $statusText = str_replace('_', ' ', $credit->status ?? 'pending');
            $pdf->Cell(25, 5, ucwords(substr($statusText, 0, 10)), 1, 0, 'C', $fillRow);
            $pdf->SetTextColor(0, 0, 0);
            
            $pdf->Cell(23, 5, $credit->remaining_balance ? number_format($credit->remaining_balance/1000, 0) . 'K' : '-', 1, 1, 'R', $fillRow);
        }
    } else {
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(155, 6, 'No recent credit activities found', 1, 1, 'C', true);
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
    $defaultRate = $portfolio->total_approved_credits > 0 ? 
        ($portfolio->defaulted_credits / $portfolio->total_approved_credits) * 100 : 0;
    
    $repaymentPerformance = $portfolio->active_credits > 0 && $repayments->credits_with_repayments > 0 ? 
        ($repayments->credits_with_repayments / $portfolio->active_credits) * 100 : 0;
    
    // FINDINGS
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor($infoColor[0], $infoColor[1], $infoColor[2]);
    $pdf->Cell(0, 7, 'KEY FINDINGS:', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(0, 0, 0);
    
    $findings = [
        "Credit approval rate of " . number_format($approvalRate, 1) . "% indicates " . 
        ($approvalRate > 60 ? "strong credit assessment standards" : "potential for improved approvals"),
        
        "Default rate of " . number_format($defaultRate, 1) . "% " . 
        ($defaultRate < 8 ? "demonstrates excellent credit risk management" : 
         ($defaultRate < 15 ? "shows acceptable credit risk levels" : "requires immediate attention")),
        
        "Average credit amount of KES " . number_format($portfolio->avg_credit_amount, 0) . " suggests " . 
        ($portfolio->avg_credit_amount > 50000 ? "focus on larger input packages" : "support for small-scale farmers"),
        
        "Outstanding balance of KES " . number_format($portfolio->total_outstanding_balance, 0) . " represents " . 
        number_format($collectionRate, 1) . "% collection efficiency from produce sales"
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
    
    if ($approvalRate < 40) {
        $recommendations[] = "Review credit assessment criteria to increase approval rate and support more farmers";
    }
    
    if ($defaultRate > 15) {
        $recommendations[] = "Implement enhanced credit scoring and strengthen agrovet partnership oversight";
        $recommendations[] = "Establish early intervention programs for at-risk credit recipients";
    }
    
    if ($portfolio->active_credits > 0 && $repayments->total_repayments < $portfolio->active_credits * 0.3) {
        $recommendations[] = "Strengthen produce-based repayment collection and farmer follow-up systems";
    }
    
    if ($portfolio->avg_credit_amount < 30000) {
        $recommendations[] = "Consider larger input packages to improve farmer productivity and credit value";
    }
    
    if (count($agrovetPartners) < 3) {
        $recommendations[] = "Expand agrovet partnerships to increase credit access and competition";
    }
    
    // Default recommendations if none specific
    if (empty($recommendations)) {
        $recommendations = [
            "Continue current credit risk management practices given strong performance metrics",
            "Explore opportunities for expanding credit portfolio to underserved farmer segments",
            "Implement digital credit monitoring solutions to improve operational efficiency",
            "Develop farmer input education programs to improve credit utilization and repayment rates"
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
    $pdf->Output('Bank_Credit_Report_' . $startDate . '_to_' . $endDate . '.pdf', 'I');
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