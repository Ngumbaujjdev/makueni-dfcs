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
    
    // Get session user_id to identify agrovet staff
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        throw new Exception('User not authenticated');
    }
    
    // Get staff agrovet_id
    $staffQuery = "SELECT s.id as staff_id, s.agrovet_id, a.name as agrovet_name 
                  FROM agrovet_staff s 
                  JOIN agrovets a ON s.agrovet_id = a.id
                  WHERE s.user_id = :user_id";
    
    $staff = $app->selectOne($staffQuery, [':user_id' => $userId]);
    
    if (!$staff) {
        throw new Exception('Staff information not found');
    }
    
    // Section 1: Get application summary stats for the agrovet within date range
    $statsQuery = "SELECT 
                    COUNT(*) as total_applications,
                    SUM(CASE WHEN status = 'pending' OR status = 'under_review' THEN 1 ELSE 0 END) as pending_applications,
                    SUM(CASE WHEN status = 'approved' OR status = 'fulfilled' THEN 1 ELSE 0 END) as approved_applications,
                    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_applications,
                    AVG(creditworthiness_score) as avg_creditworthiness,
                    SUM(total_amount) as total_requested_amount,
                    SUM(CASE WHEN status = 'approved' OR status = 'fulfilled' THEN total_amount ELSE 0 END) as total_approved_amount
                  FROM input_credit_applications
                  WHERE agrovet_id = :agrovet_id
                  AND application_date BETWEEN :start_date AND :end_date";
    
    $stats = $app->selectOne($statsQuery, [
        ':agrovet_id' => $staff->agrovet_id,
        ':start_date' => $startDate . ' 00:00:00',
        ':end_date' => $endDate . ' 23:59:59'
    ]);
    
    // Calculate approval rate
    $approvalRate = 0;
    if ($stats->total_applications > 0) {
        $approvalRate = ($stats->approved_applications / $stats->total_applications) * 100;
    }
     
    // Section 2: Get all applications in the date range
    $applicationsQuery = "SELECT 
                ica.id,
                ica.farmer_id,
                ica.total_amount,
                ica.credit_percentage,
                ica.total_with_interest,
                ica.repayment_percentage,
                ica.application_date,
                ica.status,
                ica.creditworthiness_score,
                ica.rejection_reason,
                CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                f.registration_number as farmer_reg,
                CASE 
                    WHEN ica.status = 'approved' OR ica.status = 'fulfilled' THEN 
                        (SELECT aic.fulfillment_date FROM approved_input_credits aic WHERE aic.credit_application_id = ica.id)
                    ELSE NULL
                END as fulfillment_date
              FROM input_credit_applications ica
              JOIN farmers f ON ica.farmer_id = f.id
              JOIN users u ON f.user_id = u.id
              WHERE ica.agrovet_id = '{$staff->agrovet_id}'
              AND ica.application_date BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'
              ORDER BY ica.application_date DESC";
    
    $applications = $app->select_all($applicationsQuery);
    // Section 3: Get application items breakdown by type
    $itemsQuery = "SELECT 
                    ici.input_type,
                    COUNT(ici.id) as item_count,
                    SUM(ici.quantity) as total_quantity,
                    ici.unit,
                    SUM(ici.total_price) as total_value,
                    AVG(ici.unit_price) as avg_price
                  FROM input_credit_items ici
                  JOIN input_credit_applications ica ON ici.credit_application_id = ica.id
                  WHERE ica.agrovet_id = '{$staff->agrovet_id}'
                  AND ica.application_date BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'
                  GROUP BY ici.input_type, ici.unit
                  ORDER BY total_value DESC";
    
    $itemsSummary = $app->select_all($itemsQuery);
    // Section 4: Get creditworthiness analysis
    $creditScoreQuery = "SELECT 
                        AVG(CASE WHEN icl.description LIKE '%Repayment history score: %' 
                            THEN SUBSTRING_INDEX(SUBSTRING_INDEX(icl.description, 'Repayment history score: ', -1), ',', 1) 
                            ELSE NULL END) as avg_repayment_score,
                        AVG(CASE WHEN icl.description LIKE '%Financial obligations score: %' 
                            THEN SUBSTRING_INDEX(SUBSTRING_INDEX(icl.description, 'Financial obligations score: ', -1), ',', 1) 
                            ELSE NULL END) as avg_financial_score,
                        AVG(CASE WHEN icl.description LIKE '%Produce history score: %' 
                            THEN SUBSTRING_INDEX(SUBSTRING_INDEX(icl.description, 'Produce history score: ', -1), ',', 1) 
                            ELSE NULL END) as avg_produce_score,
                        AVG(CASE WHEN icl.description LIKE '%Amount ratio score: %' 
                            THEN SUBSTRING_INDEX(SUBSTRING_INDEX(icl.description, 'Amount ratio score: ', -1), '.', 1) 
                            ELSE NULL END) as avg_amount_score
                    FROM input_credit_logs icl
                    JOIN input_credit_applications ica ON icl.input_credit_application_id = ica.id
                    WHERE ica.agrovet_id = :agrovet_id
                    AND icl.action_type = 'creditworthiness_check'
                    AND ica.application_date BETWEEN :start_date AND :end_date";
    
    $creditScores = $app->selectOne($creditScoreQuery, [
        ':agrovet_id' => $staff->agrovet_id,
        ':start_date' => $startDate . ' 00:00:00',
        ':end_date' => $endDate . ' 23:59:59'
    ]);
    
   // Section 5: Get monthly trend data
    $trendQuery = "SELECT 
                    DATE_FORMAT(application_date, '%Y-%m') as month,
                    COUNT(*) as application_count,
                    SUM(CASE WHEN status = 'approved' OR status = 'fulfilled' THEN 1 ELSE 0 END) as approved_count,
                    SUM(total_amount) as total_amount,
                    AVG(creditworthiness_score) as avg_score
                  FROM input_credit_applications
                  WHERE agrovet_id = '{$staff->agrovet_id}'
                  AND application_date BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'
                  GROUP BY DATE_FORMAT(application_date, '%Y-%m')
                  ORDER BY month ASC";
    
    $trends = $app->select_all($trendQuery);
    // Create new PDF document
    $pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('DFCS System');
    $pdf->SetAuthor('Makueni DFCS');
    $pdf->SetTitle('Input Credit Applications Report');
    $pdf->SetSubject('Input Credit Applications for ' . $staff->agrovet_name);
    
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
    $pdf->Cell(0, 10, 'INPUT CREDIT APPLICATIONS REPORT', 0, 1, 'C');
    
    // Agrovet name and date range
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 6, $staff->agrovet_name, 0, 1, 'C');
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
    $pdf->Cell(0, 6, 'Period: ' . date('F d, Y', strtotime($startDate)) . ' to ' . date('F d, Y', strtotime($endDate)), 0, 1, 'C');
    $pdf->Cell(0, 6, 'Generated on: ' . date('F d, Y'), 0, 1, 'C');
    
    $pdf->Ln(5);
    
    // ===== SECTION 1: SUMMARY STATS =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '1. SUMMARY STATISTICS', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Create a 2x2 grid of summary boxes with consistent dimensions
    $boxWidth = 85;
    $boxHeight = 40;
    $margin = 10;
    $startY = $pdf->GetY(); // Store the starting Y position
    
    // Box styling
    $pdf->SetDrawColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetLineWidth(0.5);
    
    // First row of boxes
    // Total Applications box
    $pdf->Rect(15, $startY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY(20, $startY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 6, 'Total Applications', 0, 1);
    $pdf->SetFont('helvetica', 'B', 18);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY(20, $pdf->GetY() + 2);
    $pdf->Cell($boxWidth - 10, 10, $stats->total_applications, 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY(20, $pdf->GetY());
    $pdf->Cell($boxWidth - 10, 5, 'During selected period', 0, 1);
    
    // Approval Rate box (positioned to align with the first box)
    $rightColX = 15 + $boxWidth + $margin;
    $pdf->Rect($rightColX, $startY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY($rightColX + 5, $startY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 6, 'Approval Rate', 0, 1);
    $pdf->SetFont('helvetica', 'B', 18);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY($rightColX + 5, $startY + 13); // Align with first box
    $pdf->Cell($boxWidth - 10, 10, number_format($approvalRate, 1) . '%', 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY($rightColX + 5, $startY + 25); // Align with first box
    $pdf->Cell($boxWidth - 10, 5, $stats->approved_applications . ' approved of ' . $stats->total_applications, 0, 1);
    
    // Set Y position for next row
    $secondRowY = $startY + $boxHeight + 10;
    
    // Second row of boxes
    // Total Amount box
    $pdf->Rect(15, $secondRowY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY(20, $secondRowY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 6, 'Total Amount Requested', 0, 1);
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY(20, $secondRowY + 13);
    $pdf->Cell($boxWidth - 10, 10, 'KES ' . number_format($stats->total_requested_amount, 2), 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY(20, $secondRowY + 25);
    $pdf->Cell($boxWidth - 10, 5, 'Approved: KES ' . number_format($stats->total_approved_amount, 2), 0, 1);
    
    // Average Creditworthiness box
    $pdf->Rect($rightColX, $secondRowY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY($rightColX + 5, $secondRowY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 6, 'Average Creditworthiness', 0, 1);
    $pdf->SetFont('helvetica', 'B', 18);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY($rightColX + 5, $secondRowY + 13);
    $pdf->Cell($boxWidth - 10, 10, number_format($stats->avg_creditworthiness, 1), 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY($rightColX + 5, $secondRowY + 25);
    $pdf->Cell($boxWidth - 10, 5, 'Score out of 100', 0, 1);
    
    // Set Y position after summary boxes
    $pdf->SetY($secondRowY + $boxHeight + 10);
    
    // ===== SECTION 2: APPLICATION STATUS BREAKDOWN =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '2. APPLICATION STATUS BREAKDOWN', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Status breakdown table
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    
    // Table header
    $pdf->Cell(60, 8, 'Status', 1, 0, 'C', true);
    $pdf->Cell(40, 8, 'Count', 1, 0, 'C', true);
    $pdf->Cell(40, 8, 'Percentage', 1, 0, 'C', true);
    $pdf->Cell(40, 8, 'Amount (KES)', 1, 1, 'C', true);
    
    // Reset styling for table rows
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetFillColor(245, 245, 245);
    
    // Pending/Under Review row
    $pendingPercentage = $stats->total_applications > 0 ? ($stats->pending_applications / $stats->total_applications * 100) : 0;
    $pdf->Cell(60, 8, 'Pending/Under Review', 1, 0, 'L', true);
    $pdf->Cell(40, 8, $stats->pending_applications, 1, 0, 'C', true);
    $pdf->Cell(40, 8, number_format($pendingPercentage, 1) . '%', 1, 0, 'C', true);
    $pendingAmount = $stats->total_requested_amount - $stats->total_approved_amount; // Approximate
    $pdf->Cell(40, 8, number_format($pendingAmount, 2), 1, 1, 'R', true);
    
    // Approved/Fulfilled row
    $approvedPercentage = $stats->total_applications > 0 ? ($stats->approved_applications / $stats->total_applications * 100) : 0;
    $pdf->Cell(60, 8, 'Approved/Fulfilled', 1, 0, 'L');
    $pdf->Cell(40, 8, $stats->approved_applications, 1, 0, 'C');
    $pdf->Cell(40, 8, number_format($approvedPercentage, 1) . '%', 1, 0, 'C');
    $pdf->Cell(40, 8, number_format($stats->total_approved_amount, 2), 1, 1, 'R');
    
    // Rejected row
    $rejectedPercentage = $stats->total_applications > 0 ? ($stats->rejected_applications / $stats->total_applications * 100) : 0;
    $pdf->Cell(60, 8, 'Rejected', 1, 0, 'L', true);
    $pdf->Cell(40, 8, $stats->rejected_applications, 1, 0, 'C', true);
    $pdf->Cell(40, 8, number_format($rejectedPercentage, 1) . '%', 1, 0, 'C', true);
    // Approximate amount - we don't have this directly
    $rejectedAmount = $stats->total_applications > 0 ? 
                      ($stats->total_requested_amount / $stats->total_applications) * $stats->rejected_applications : 0;
    $pdf->Cell(40, 8, number_format($rejectedAmount, 2), 1, 1, 'R', true);
    
    // Total row
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(60, 8, 'TOTAL', 1, 0, 'L', true);
    $pdf->Cell(40, 8, $stats->total_applications, 1, 0, 'C', true);
    $pdf->Cell(40, 8, '100%', 1, 0, 'C', true);
    $pdf->Cell(40, 8, number_format($stats->total_requested_amount, 2), 1, 1, 'R', true);
    
    $pdf->Ln(5);
    
    
    // ===== SECTION 2: APPLICATION STATUS BREAKDOWN =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '2. APPLICATION STATUS BREAKDOWN', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Status breakdown table
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    
    // Table header
    $pdf->Cell(60, 8, 'Status', 1, 0, 'C', true);
    $pdf->Cell(40, 8, 'Count', 1, 0, 'C', true);
    $pdf->Cell(40, 8, 'Percentage', 1, 0, 'C', true);
    $pdf->Cell(40, 8, 'Amount (KES)', 1, 1, 'C', true);
    
    // Reset styling for table rows
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetFillColor(245, 245, 245);
    
    // Pending/Under Review row
    $pendingPercentage = $stats->total_applications > 0 ? ($stats->pending_applications / $stats->total_applications * 100) : 0;
    $pdf->Cell(60, 8, 'Pending/Under Review', 1, 0, 'L', true);
    $pdf->Cell(40, 8, $stats->pending_applications, 1, 0, 'C', true);
    $pdf->Cell(40, 8, number_format($pendingPercentage, 1) . '%', 1, 0, 'C', true);
    $pendingAmount = $stats->total_requested_amount - $stats->total_approved_amount; // Approximate
    $pdf->Cell(40, 8, number_format($pendingAmount, 2), 1, 1, 'R', true);
    
    // Approved/Fulfilled row
    $approvedPercentage = $stats->total_applications > 0 ? ($stats->approved_applications / $stats->total_applications * 100) : 0;
    $pdf->Cell(60, 8, 'Approved/Fulfilled', 1, 0, 'L');
    $pdf->Cell(40, 8, $stats->approved_applications, 1, 0, 'C');
    $pdf->Cell(40, 8, number_format($approvedPercentage, 1) . '%', 1, 0, 'C');
    $pdf->Cell(40, 8, number_format($stats->total_approved_amount, 2), 1, 1, 'R');
    
    // Rejected row
    $rejectedPercentage = $stats->total_applications > 0 ? ($stats->rejected_applications / $stats->total_applications * 100) : 0;
    $pdf->Cell(60, 8, 'Rejected', 1, 0, 'L', true);
    $pdf->Cell(40, 8, $stats->rejected_applications, 1, 0, 'C', true);
    $pdf->Cell(40, 8, number_format($rejectedPercentage, 1) . '%', 1, 0, 'C', true);
    // Approximate amount - we don't have this directly
    $rejectedAmount = $stats->total_applications > 0 ? 
                      ($stats->total_requested_amount / $stats->total_applications) * $stats->rejected_applications : 0;
    $pdf->Cell(40, 8, number_format($rejectedAmount, 2), 1, 1, 'R', true);
    
    // Total row
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(60, 8, 'TOTAL', 1, 0, 'L', true);
    $pdf->Cell(40, 8, $stats->total_applications, 1, 0, 'C', true);
    $pdf->Cell(40, 8, '100%', 1, 0, 'C', true);
    $pdf->Cell(40, 8, number_format($stats->total_requested_amount, 2), 1, 1, 'R', true);
    
    $pdf->Ln(5);
    
    // ===== SECTION 3: INPUT TYPE BREAKDOWN =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '3. INPUT TYPE BREAKDOWN', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Input type breakdown table
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    
    // Table header
    $pdf->Cell(50, 8, 'Input Type', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Count', 1, 0, 'C', true);
    $pdf->Cell(40, 8, 'Total Quantity', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Unit', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Amount (KES)', 1, 1, 'C', true);
    
    // Reset styling for table rows
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetFillColor(245, 245, 245);
    
    $totalValue = 0;
    $totalItems = 0;
    
    // Input type rows
    if ($itemsSummary) {
        foreach ($itemsSummary as $index => $item) {
            $fillRow = $index % 2 == 0;
            $pdf->Cell(50, 8, ucfirst($item->input_type), 1, 0, 'L', $fillRow);
            $pdf->Cell(30, 8, $item->item_count, 1, 0, 'C', $fillRow);
            $pdf->Cell(40, 8, number_format($item->total_quantity, 2), 1, 0, 'C', $fillRow);
            $pdf->Cell(30, 8, $item->unit, 1, 0, 'C', $fillRow);
            $pdf->Cell(30, 8, number_format($item->total_value, 2), 1, 1, 'R', $fillRow);
            
            $totalValue += $item->total_value;
            $totalItems += $item->item_count;
        }
    } else {
        $pdf->Cell(180, 8, 'No input items found in the selected period', 1, 1, 'C', true);
    }
    
    // Total row for input types
    if ($itemsSummary) {
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(50, 8, 'TOTAL', 1, 0, 'L', true);
        $pdf->Cell(30, 8, $totalItems, 1, 0, 'C', true);
        $pdf->Cell(40, 8, '', 1, 0, 'C', true);
        $pdf->Cell(30, 8, '', 1, 0, 'C', true);
        $pdf->Cell(30, 8, number_format($totalValue, 2), 1, 1, 'R', true);
    }
    
    $pdf->Ln(5);
    
    // ===== SECTION 4: CREDITWORTHINESS ANALYSIS =====
    // Check if we need a new page for the creditworthiness section
    if ($pdf->GetY() > 220) {
        $pdf->AddPage();
    }
    
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '4. CREDITWORTHINESS ANALYSIS', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Set up the creditworthiness breakdown
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    
    // Table header
    $pdf->Cell(120, 8, 'Creditworthiness Component', 1, 0, 'C', true);
    $pdf->Cell(60, 8, 'Average Score', 1, 1, 'C', true);
    
    // Reset styling for table rows
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetFillColor(245, 245, 245);
    
    // Component rows
    $pdf->Cell(120, 8, 'Repayment History (30% weight)', 1, 0, 'L', true);
    $pdf->Cell(60, 8, number_format($creditScores->avg_repayment_score, 1) . ' / 100', 1, 1, 'C', true);
    
    $pdf->Cell(120, 8, 'Financial Obligations (25% weight)', 1, 0, 'L');
    $pdf->Cell(60, 8, number_format($creditScores->avg_financial_score, 1) . ' / 100', 1, 1, 'C');
    
    $pdf->Cell(120, 8, 'Produce History (35% weight)', 1, 0, 'L', true);
    $pdf->Cell(60, 8, number_format($creditScores->avg_produce_score, 1) . ' / 100', 1, 1, 'C', true);
    
    $pdf->Cell(120, 8, 'Amount Ratio (10% weight)', 1, 0, 'L');
    $pdf->Cell(60, 8, number_format($creditScores->avg_amount_score, 1) . ' / 100', 1, 1, 'C');
    
    // Calculate overall average score
    $overallAvg = (
        ($creditScores->avg_repayment_score * 0.3) + 
        ($creditScores->avg_financial_score * 0.25) + 
        ($creditScores->avg_produce_score * 0.35) + 
        ($creditScores->avg_amount_score * 0.1)
    );
    
    // Overall score row
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(120, 8, 'OVERALL AVERAGE SCORE', 1, 0, 'L', true);
    $pdf->Cell(60, 8, number_format($overallAvg, 1) . ' / 100', 1, 1, 'C', true);
    
    $pdf->Ln(5);
    
    // ===== SECTION 5: DETAILED APPLICATION LIST =====
    // Start a new page for the applications list
    $pdf->AddPage();
    
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '5. DETAILED APPLICATION LIST', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Applications list table
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    
    // Table header
    $pdf->Cell(15, 8, 'ID', 1, 0, 'C', true);
    $pdf->Cell(40, 8, 'Farmer', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Amount (KES)', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Status', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Score', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Application Date', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Fulfillment Date', 1, 1, 'C', true);
    
    // Reset styling for table rows
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    
    // Status color mapping
    $statusColors = [
        'pending' => [220, 220, 220],
        'under_review' => [255, 193, 7],
        'approved' => [0, 123, 255],
        'rejected' => [220, 53, 69],
        'fulfilled' => [40, 167, 69],
        'cancelled' => [108, 117, 125]
    ];
    
    // Application rows
    if ($applications) {
        foreach ($applications as $index => $app) {
            // Alternate row fill
            $fillRow = $index % 2 == 0;
            if ($fillRow) {
                $pdf->SetFillColor(245, 245, 245);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            
            // Application ID
            $pdf->Cell(15, 6, 'IC' . str_pad($app->id, 5, '0', STR_PAD_LEFT), 1, 0, 'C', $fillRow);
            
            // Farmer name and reg
            $pdf->Cell(40, 6, $app->farmer_name . ' (' . $app->farmer_reg . ')', 1, 0, 'L', $fillRow);
            
            // Amount
            $pdf->Cell(25, 6, number_format($app->total_amount, 2), 1, 0, 'R', $fillRow);
            
            // Status with color
            $statusColor = $statusColors[$app->status] ?? [220, 220, 220];
            $pdf->SetFillColor($statusColor[0], $statusColor[1], $statusColor[2]);
            $pdf->Cell(25, 6, ucfirst(str_replace('_', ' ', $app->status)), 1, 0, 'C', true);
            
            // Reset fill color
            if ($fillRow) {
                $pdf->SetFillColor(245, 245, 245);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            
            // Creditworthiness score
            $pdf->Cell(25, 6, number_format($app->creditworthiness_score, 1), 1, 0, 'C', $fillRow);
            
            // Application date
            $appDate = date('M d, Y', strtotime($app->application_date));
            $pdf->Cell(30, 6, $appDate, 1, 0, 'C', $fillRow);
            
            // Fulfillment date (if available)
            $fulfillDate = $app->fulfillment_date ? date('M d, Y', strtotime($app->fulfillment_date)) : '-';
            $pdf->Cell(30, 6, $fulfillDate, 1, 1, 'C', $fillRow);
        }
    } else {
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(190, 8, 'No applications found in the selected period', 1, 1, 'C', true);
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
    $pdf->Output('Input_Credit_Applications_Report_' . $startDate . '_to_' . $endDate . '.pdf', 'I');
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