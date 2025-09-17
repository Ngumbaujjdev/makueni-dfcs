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
                  WHERE s.user_id = '{$userId}'";
    
    $staff = $app->selectOne($staffQuery);
    
    if (!$staff) {
        throw new Exception('Staff information not found');
    }
    
    // Section 1: Get farmer summary stats for the agrovet within date range
    $statsQuery = "SELECT 
                    COUNT(DISTINCT ica.farmer_id) as total_farmers,
                    COUNT(ica.id) as total_applications,
                    AVG(ica.creditworthiness_score) as avg_creditworthiness,
                    SUM(ica.total_amount) as total_requested_amount,
                    SUM(CASE WHEN ica.status = 'approved' OR ica.status = 'fulfilled' THEN ica.total_amount ELSE 0 END) as total_approved_amount,
                    COUNT(CASE WHEN ica.status = 'approved' OR ica.status = 'fulfilled' THEN 1 ELSE NULL END) as approved_applications
                  FROM input_credit_applications ica
                  WHERE ica.agrovet_id = '{$staff->agrovet_id}'
                  AND ica.application_date BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'";
    
    $stats = $app->select_one($statsQuery);
    
    // Calculate averages and percentages
    $avgApplicationsPerFarmer = $stats->total_farmers > 0 ? 
                               $stats->total_applications / $stats->total_farmers : 0;
    
    $avgAmountPerFarmer = $stats->total_farmers > 0 ? 
                         $stats->total_requested_amount / $stats->total_farmers : 0;
    
    $approvalRate = $stats->total_applications > 0 ? 
                   ($stats->approved_applications / $stats->total_applications) * 100 : 0;
    
    // Section 2: Get farmer category distribution
    $categoryQuery = "SELECT 
                      fc.name as category_name,
                      COUNT(DISTINCT ica.farmer_id) as farmer_count,
                      AVG(ica.creditworthiness_score) as avg_score,
                      COUNT(ica.id) as applications_count,
                      SUM(ica.total_amount) as requested_amount,
                      SUM(CASE WHEN ica.status = 'approved' OR ica.status = 'fulfilled' THEN ica.total_amount ELSE 0 END) as approved_amount
                    FROM input_credit_applications ica
                    JOIN farmers f ON ica.farmer_id = f.id
                    LEFT JOIN farmer_categories fc ON f.category_id = fc.id
                    WHERE ica.agrovet_id = '{$staff->agrovet_id}'
                    AND ica.application_date BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'
                    GROUP BY fc.name
                    ORDER BY farmer_count DESC";
    
    $categories = $app->select_all($categoryQuery);
    
    // Section 3: Get top farmers by credit amount
    $topFarmersQuery = "SELECT 
                         ica.farmer_id,
                         CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                         f.registration_number as farmer_reg,
                         fc.name as category_name,
                         COUNT(ica.id) as applications_count,
                         SUM(ica.total_amount) as requested_amount,
                         AVG(ica.creditworthiness_score) as avg_creditworthiness,
                         SUM(CASE WHEN ica.status = 'approved' OR ica.status = 'fulfilled' THEN ica.total_amount ELSE 0 END) as approved_amount,
                         COUNT(CASE WHEN ica.status = 'approved' OR ica.status = 'fulfilled' THEN 1 ELSE NULL END) as approved_count
                       FROM input_credit_applications ica
                       JOIN farmers f ON ica.farmer_id = f.id
                       JOIN users u ON f.user_id = u.id
                       LEFT JOIN farmer_categories fc ON f.category_id = fc.id
                       WHERE ica.agrovet_id = '{$staff->agrovet_id}'
                       AND ica.application_date BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'
                       GROUP BY ica.farmer_id, farmer_name, farmer_reg, category_name
                       ORDER BY requested_amount DESC
                       LIMIT 10";
    
    $topFarmers = $app->select_all($topFarmersQuery);
    
    // Section 4: Get repeat applicants (farmers with multiple applications)
    $repeatFarmersQuery = "SELECT 
                           ica.farmer_id,
                           CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                           f.registration_number as farmer_reg,
                           COUNT(ica.id) as applications_count,
                           MIN(ica.application_date) as first_application,
                           MAX(ica.application_date) as last_application,
                           SUM(ica.total_amount) as total_amount,
                           COUNT(CASE WHEN ica.status = 'approved' OR ica.status = 'fulfilled' THEN 1 ELSE NULL END) as approved_count
                         FROM input_credit_applications ica
                         JOIN farmers f ON ica.farmer_id = f.id
                         JOIN users u ON f.user_id = u.id
                         WHERE ica.agrovet_id = '{$staff->agrovet_id}'
                         AND ica.application_date BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'
                         GROUP BY ica.farmer_id, farmer_name, farmer_reg
                         HAVING COUNT(ica.id) > 1
                         ORDER BY applications_count DESC, total_amount DESC
                         LIMIT 10";
    
    $repeatFarmers = $app->select_all($repeatFarmersQuery);
    
    // Section 5: Get repayment performance by farmer category
    $repaymentQuery = "SELECT 
                       fc.name as category_name,
                       COUNT(DISTINCT ica.farmer_id) as farmer_count,
                       COUNT(aic.id) as credits_count,
                       SUM(aic.total_with_interest) as total_amount,
                       SUM(aic.total_with_interest - aic.remaining_balance) as amount_repaid,
                       AVG((aic.total_with_interest - aic.remaining_balance) / aic.total_with_interest * 100) as avg_repayment_percent
                     FROM approved_input_credits aic
                     JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                     JOIN farmers f ON ica.farmer_id = f.id
                     LEFT JOIN farmer_categories fc ON f.category_id = fc.id
                     WHERE ica.agrovet_id = '{$staff->agrovet_id}'
                     AND aic.approval_date BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'
                     GROUP BY fc.name
                     ORDER BY avg_repayment_percent DESC";
    
    $repaymentByCategory = $app->select_all($repaymentQuery);
    
    // Create new PDF document
    $pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('DFCS System');
    $pdf->SetAuthor('Makueni DFCS');
    $pdf->SetTitle('Farmer Credit Analysis Report');
    $pdf->SetSubject('Farmer Credit Analysis for ' . $staff->agrovet_name);
    
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
    $pdf->Cell(0, 10, 'FARMER CREDIT ANALYSIS REPORT', 0, 1, 'C');
    
    // Agrovet name and date range
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 6, $staff->agrovet_name, 0, 1, 'C');
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
    $pdf->Cell(0, 6, 'Period: ' . date('F d, Y', strtotime($startDate)) . ' to ' . date('F d, Y', strtotime($endDate)), 0, 1, 'C');
    $pdf->Cell(0, 6, 'Generated on: ' . date('F d, Y'), 0, 1, 'C');
    
    $pdf->Ln(5);
    
    // ===== SECTION 1: FARMER SUMMARY =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '1. FARMER SUMMARY', 0, 1, 'L');
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
    // Total Farmers box
    $pdf->Rect(15, $startY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY(20, $startY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 6, 'Total Farmers', 0, 1);
    $pdf->SetFont('helvetica', 'B', 18);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY(20, $startY + 13);
    $pdf->Cell($boxWidth - 10, 10, $stats->total_farmers, 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY(20, $startY + 25);
    $pdf->Cell($boxWidth - 10, 5, 'During selected period', 0, 1);
    
    // Applications Per Farmer box
    $rightColX = 15 + $boxWidth + $margin;
    $pdf->Rect($rightColX, $startY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY($rightColX + 5, $startY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 6, 'Applications Per Farmer', 0, 1);
    $pdf->SetFont('helvetica', 'B', 18);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY($rightColX + 5, $startY + 13);
    $pdf->Cell($boxWidth - 10, 10, number_format($avgApplicationsPerFarmer, 1), 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY($rightColX + 5, $startY + 25);
    $pdf->Cell($boxWidth - 10, 5, $stats->total_applications . ' total applications', 0, 1);
    
    // Set Y position for next row
    $secondRowY = $startY + $boxHeight + 10;
    
    // Second row of boxes
    // Average Credit Amount box
    $pdf->Rect(15, $secondRowY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY(20, $secondRowY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 6, 'Average Credit Amount', 0, 1);
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY(20, $secondRowY + 13);
    $pdf->Cell($boxWidth - 10, 10, 'KES ' . number_format($avgAmountPerFarmer, 2), 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY(20, $secondRowY + 25);
    $pdf->Cell($boxWidth - 10, 5, 'Per farmer average', 0, 1);
    
    // Approval Rate box
    $pdf->Rect($rightColX, $secondRowY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY($rightColX + 5, $secondRowY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 6, 'Approval Rate', 0, 1);
    $pdf->SetFont('helvetica', 'B', 18);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY($rightColX + 5, $secondRowY + 13);
    $pdf->Cell($boxWidth - 10, 10, number_format($approvalRate, 1) . '%', 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY($rightColX + 5, $secondRowY + 25);
    $pdf->Cell($boxWidth - 10, 5, $stats->approved_applications . ' of ' . $stats->total_applications . ' approved', 0, 1);
    
    // Set Y position after summary boxes
    $pdf->SetY($secondRowY + $boxHeight + 10);
    
    // ===== SECTION 2: FARMER CATEGORY DISTRIBUTION =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '2. FARMER CATEGORY DISTRIBUTION', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Farmer category table
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    
    // Table header
    $pdf->Cell(50, 8, 'Farmer Category', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Farmers', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Applications', 1, 0, 'C', true);
    $pdf->Cell(35, 8, 'Total Amount (KES)', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Approval %', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Avg. Score', 1, 1, 'C', true);
    
    // Reset styling for table rows
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    
    // Category rows
    if ($categories && count($categories) > 0) {
        $totalFarmers = 0;
        $totalApps = 0;
        $totalAmount = 0;
        
        foreach ($categories as $index => $category) {
            // Alternate row fill
            $fillRow = $index % 2 == 0;
            if ($fillRow) {
                $pdf->SetFillColor(245, 245, 245);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            
            // Category name (use "Uncategorized" if null)
            $categoryName = $category->category_name ? $category->category_name : 'Uncategorized';
            $pdf->Cell(50, 7, $categoryName, 1, 0, 'L', $fillRow);
            
            // Farmer count
            $pdf->Cell(25, 7, $category->farmer_count, 1, 0, 'C', $fillRow);
            
            // Applications count
            $pdf->Cell(25, 7, $category->applications_count, 1, 0, 'C', $fillRow);
            
            // Requested amount
            $pdf->Cell(35, 7, number_format($category->requested_amount, 2), 1, 0, 'R', $fillRow);
            
            // Approval percentage
            $approvalPct = $category->applications_count > 0 ? 
                         ($category->approved_amount / $category->requested_amount) * 100 : 0;
            $pdf->Cell(25, 7, number_format($approvalPct, 1) . '%', 1, 0, 'C', $fillRow);
            
            // Average creditworthiness score
            $pdf->Cell(25, 7, number_format($category->avg_score, 1), 1, 1, 'C', $fillRow);
            
            // Add to totals
            $totalFarmers += $category->farmer_count;
            $totalApps += $category->applications_count;
            $totalAmount += $category->requested_amount;
        }
        
        // Totals row
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(50, 8, 'TOTAL', 1, 0, 'C', true);
        $pdf->Cell(25, 8, $totalFarmers, 1, 0, 'C', true);
        $pdf->Cell(25, 8, $totalApps, 1, 0, 'C', true);
        $pdf->Cell(35, 8, number_format($totalAmount, 2), 1, 0, 'R', true);
        
        $overallApprovalPct = $totalAmount > 0 ? 
                             ($stats->total_approved_amount / $totalAmount) * 100 : 0;
        $pdf->Cell(25, 8, number_format($overallApprovalPct, 1) . '%', 1, 0, 'C', true);
        $pdf->Cell(25, 8, number_format($stats->avg_creditworthiness, 1), 1, 1, 'C', true);
    } else {
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(185, 8, 'No farmer category data available for the selected period', 1, 1, 'C', true);
    }
    
    $pdf->Ln(5);
    
    // ===== SECTION 3: TOP FARMERS BY CREDIT AMOUNT =====
    // Check if we need a new page
    if ($pdf->GetY() > 220) {
        $pdf->AddPage();
    }
    
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '3. TOP FARMERS BY CREDIT AMOUNT', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Top farmers table
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    
    // Table header
    $pdf->Cell(50, 8, 'Farmer', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Category', 1, 0, 'C', true);
    $pdf->Cell(20, 8, 'Apps', 1, 0, 'C', true);
    $pdf->Cell(35, 8, 'Requested (KES)', 1, 0, 'C', true);
    $pdf->Cell(35, 8, 'Approved (KES)', 1, 0, 'C', true);
    $pdf->Cell(20, 8, 'Score', 1, 1, 'C', true);
    
    // Reset styling for table rows
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    
    // Top farmers rows
    if ($topFarmers && count($topFarmers) > 0) {
        foreach ($topFarmers as $index => $farmer) {
            // Alternate row fill
            $fillRow = $index % 2 == 0;
            if ($fillRow) {
                $pdf->SetFillColor(245, 245, 245);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            
            // Farmer name and registration number
            $pdf->Cell(50, 7, $farmer->farmer_name . ' (' . $farmer->farmer_reg . ')', 1, 0, 'L', $fillRow);
            
            // Farmer category
            $categoryName = $farmer->category_name ? $farmer->category_name : 'Uncategorized';
            $pdf->Cell(30, 7, $categoryName, 1, 0, 'C', $fillRow);
            
            // Applications count
            $pdf->Cell(20, 7, $farmer->applications_count, 1, 0, 'C', $fillRow);
            
            // Requested amount
            $pdf->Cell(35, 7, number_format($farmer->requested_amount, 2), 1, 0, 'R', $fillRow);
            
            // Approved amount
            $pdf->Cell(35, 7, number_format($farmer->approved_amount, 2), 1, 0, 'R', $fillRow);
            
            // Creditworthiness score
            $pdf->Cell(20, 7, number_format($farmer->avg_creditworthiness, 1), 1, 1, 'C', $fillRow);
        }
    } else {
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(190, 8, 'No farmer data available for the selected period', 1, 1, 'C', true);
    }
    
    $pdf->Ln(5);
    
    // ===== SECTION 4: REPEAT APPLICANTS =====
    // Check if we need a new page
    if ($pdf->GetY() > 220) {
        $pdf->AddPage();
    }
    
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '4. TOP REPEAT APPLICANTS', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Repeat applicants table
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    
    // Table header
    $pdf->Cell(50, 8, 'Farmer', 1, 0, 'C', true);
    $pdf->Cell(20, 8, 'Apps', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'First Application', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Last Application', 1, 0, 'C', true);
    $pdf->Cell(35, 8, 'Total Amount (KES)', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Approval Rate', 1, 1, 'C', true);
    
    // Reset styling for table rows
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    
    // Repeat applicants rows
    if ($repeatFarmers && count($repeatFarmers) > 0) {
        foreach ($repeatFarmers as $index => $farmer) {
            // Alternate row fill
            $fillRow = $index % 2 == 0;
            if ($fillRow) {
                $pdf->SetFillColor(245, 245, 245);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            
            // Farmer name and registration number
            $pdf->Cell(50, 7, $farmer->farmer_name . ' (' . $farmer->farmer_reg . ')', 1, 0, 'L', $fillRow);
            
            // Applications count
            $pdf->Cell(20, 7, $farmer->applications_count, 1, 0, 'C', $fillRow);
            
            // First application date
            $firstAppDate = date('M d, Y', strtotime($farmer->first_application));
            $pdf->Cell(30, 7, $firstAppDate, 1, 0, 'C', $fillRow);
            
            // Last application date
            $lastAppDate = date('M d, Y', strtotime($farmer->last_application));
            $pdf->Cell(30, 7, $lastAppDate, 1, 0, 'C', $fillRow);
            
            // Total amount
            $pdf->Cell(35, 7, number_format($farmer->total_amount, 2), 1, 0, 'R', $fillRow);
            
            // Approval rate
            $approvalRate = $farmer->applications_count > 0 ? 
                          ($farmer->approved_count / $farmer->applications_count) * 100 : 0;
            $pdf->Cell(25, 7, number_format($approvalRate, 1) . '%', 1, 1, 'C', $fillRow);
        }
    } else {
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(190, 8, 'No repeat applicants found in the selected period', 1, 1, 'C', true);
    }
    
    $pdf->Ln(5);
    
    // ===== SECTION 5: REPAYMENT PERFORMANCE BY CATEGORY =====
    // Add a new page for repayment performance
    $pdf->AddPage();
    
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '5. REPAYMENT PERFORMANCE BY CATEGORY', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Repayment performance table
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    
    // Table header
    $pdf->Cell(50, 8, 'Farmer Category', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Farmers', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Credits', 1, 0, 'C', true);
    $pdf->Cell(35, 8, 'Total Amount (KES)', 1, 0, 'C', true);
    $pdf->Cell(35, 8, 'Repaid (KES)', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Repayment %', 1, 1, 'C', true);
    
    // Reset styling for table rows
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    
    // Repayment by category rows
    if ($repaymentByCategory && count($repaymentByCategory) > 0) {
        $totalFarmers = 0;
        $totalCredits = 0;
        $totalAmount = 0;
        $totalRepaid = 0;
        
        foreach ($repaymentByCategory as $index => $category) {
            // Alternate row fill
            $fillRow = $index % 2 == 0;
            if ($fillRow) {
                $pdf->SetFillColor(245, 245, 245);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            
            // Category name (use "Uncategorized" if null)
            $categoryName = $category->category_name ? $category->category_name : 'Uncategorized';
            $pdf->Cell(50, 7, $categoryName, 1, 0, 'L', $fillRow);
            
            // Farmer count
            $pdf->Cell(25, 7, $category->farmer_count, 1, 0, 'C', $fillRow);
            
            // Credits count
            $pdf->Cell(25, 7, $category->credits_count, 1, 0, 'C', $fillRow);
            
            // Total amount
            $pdf->Cell(35, 7, number_format($category->total_amount, 2), 1, 0, 'R', $fillRow);
            
            // Amount repaid
            $pdf->Cell(35, 7, number_format($category->amount_repaid, 2), 1, 0, 'R', $fillRow);
            
            // Repayment percentage
            $pdf->Cell(25, 7, number_format($category->avg_repayment_percent, 1) . '%', 1, 1, 'C', $fillRow);
            
            // Add to totals
            $totalFarmers += $category->farmer_count;
            $totalCredits += $category->credits_count;
            $totalAmount += $category->total_amount;
            $totalRepaid += $category->amount_repaid;
        }
        
        // Totals row
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(50, 8, 'TOTAL', 1, 0, 'C', true);
        $pdf->Cell(25, 8, $totalFarmers, 1, 0, 'C', true);
        $pdf->Cell(25, 8, $totalCredits, 1, 0, 'C', true);
        $pdf->Cell(35, 8, number_format($totalAmount, 2), 1, 0, 'R', true);
        $pdf->Cell(35, 8, number_format($totalRepaid, 2), 1, 0, 'R', true);
        
        $overallRepaymentPct = $totalAmount > 0 ? ($totalRepaid / $totalAmount) * 100 : 0;
        $pdf->Cell(25, 8, number_format($overallRepaymentPct, 1) . '%', 1, 1, 'C', true);
    } else {
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(195, 8, 'No repayment data available for the selected period', 1, 1, 'C', true);
    }
    
    $pdf->Ln(5);
    
    // ===== SECTION 6: FARMER RELATIONSHIP ANALYSIS =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '6. FARMER RELATIONSHIP ANALYSIS', 0, 1, 'L');
    $pdf->Ln(2);
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    
    // Analysis text
    $pdf->MultiCell(0, 6, 'During the period from ' . date('F d, Y', strtotime($startDate)) . ' to ' . 
                 date('F d, Y', strtotime($endDate)) . ', ' . $staff->agrovet_name . ' has served a total of ' . 
                 $stats->total_farmers . ' farmers who made ' . $stats->total_applications . 
                 ' credit applications. This represents an average of ' . 
                 number_format($avgApplicationsPerFarmer, 1) . ' applications per farmer.', 0, 'L');
    
    $pdf->Ln(2);
    
    // Repeat applicant analysis
    $repeatCount = count($repeatFarmers ?? []);
    $repeatPercentage = $stats->total_farmers > 0 ? ($repeatCount / $stats->total_farmers) * 100 : 0;
    
    $pdf->MultiCell(0, 6, $repeatCount . ' farmers (' . number_format($repeatPercentage, 1) . 
                 '% of total) have applied for credit multiple times during this period, showing strong repeat business. ' . 
                 'The average amount requested per farmer was KES ' . number_format($avgAmountPerFarmer, 2) . 
                 ', with an approval rate of ' . number_format($approvalRate, 1) . '%.', 0, 'L');
    
    $pdf->Ln(2);
    
    // Category analysis
    if ($categories && count($categories) > 0) {
        // Find the category with most farmers
        $topCategory = $categories[0];
        $categoryName = $topCategory->category_name ? $topCategory->category_name : 'Uncategorized';
        
        $pdf->MultiCell(0, 6, 'The ' . $categoryName . ' category represents the largest segment with ' . 
                     $topCategory->farmer_count . ' farmers (' . 
                     number_format(($topCategory->farmer_count / $stats->total_farmers) * 100, 1) . 
                     '% of total) and accounts for KES ' . number_format($topCategory->requested_amount, 2) . 
                     ' in credit applications.', 0, 'L');
    }
    
    $pdf->Ln(2);
    
    // Repayment analysis
    if ($repaymentByCategory && count($repaymentByCategory) > 0) {
        // Find the category with best repayment
        $bestCategory = $repaymentByCategory[0];
        $bestCategoryName = $bestCategory->category_name ? $bestCategory->category_name : 'Uncategorized';
        
        $pdf->MultiCell(0, 6, 'In terms of repayment performance, the ' . $bestCategoryName . 
                     ' category shows the highest repayment rate at ' . 
                     number_format($bestCategory->avg_repayment_percent, 1) . 
                     '%. The overall repayment rate across all categories is ' . 
                     number_format($overallRepaymentPct, 1) . '%.', 0, 'L');
    }
    
    $pdf->Ln(5);
    
    // Recommendations section
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
    $pdf->Cell(0, 8, 'Recommendations for Farmer Engagement:', 0, 1, 'L');
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    
    // Add bullet points with recommendations
    $pdf->SetFont('', 'B', 9);
    $pdf->Cell(5, 6, '•', 0, 0, 'L');
    $pdf->SetFont('', '', 10);
    
    if ($repeatPercentage < 30) {
        $pdf->MultiCell(0, 6, 'Focus on increasing repeat applications by implementing a loyalty program for farmers who consistently repay on time.', 0, 'L');
    } else {
        $pdf->MultiCell(0, 6, 'Continue to nurture the high rate of repeat applications by offering preferential terms to reliable farmers.', 0, 'L');
    }
    
    $pdf->SetFont('', 'B', 9);
    $pdf->Cell(5, 6, '•', 0, 0, 'L');
    $pdf->SetFont('', '', 10);
    
    // Find the category with lowest approval or repayment rate
    if ($categories && count($categories) > 1) {
        $lastIndex = count($categories) - 1;
        $lowestCategory = $categories[$lastIndex];
        $lowestCategoryName = $lowestCategory->category_name ? $lowestCategory->category_name : 'Uncategorized';
        
        $pdf->MultiCell(0, 6, 'Consider targeted education and support for ' . $lowestCategoryName . 
                     ' farmers to improve their creditworthiness and repayment performance.', 0, 'L');
    } else {
        $pdf->MultiCell(0, 6, 'Develop targeted educational programs for farmers to improve their understanding of credit terms and repayment obligations.', 0, 'L');
    }
    
    $pdf->SetFont('', 'B', 9);
    $pdf->Cell(5, 6, '•', 0, 0, 'L');
    $pdf->SetFont('', '', 10);
    
    if ($topFarmers && count($topFarmers) > 0) {
        $pdf->MultiCell(0, 6, 'Implement a VIP program for top credit customers like ' . $topFarmers[0]->farmer_name . 
                     ' who has requested KES ' . number_format($topFarmers[0]->requested_amount, 2) . 
                     ' in credit.', 0, 'L');
    } else {
        $pdf->MultiCell(0, 6, 'Identify and cultivate relationships with farmers who have the potential to become high-value customers.', 0, 'L');
    }
    
    // ===== DOCUMENT FOOTER =====
    $pdf->SetY(-25);
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->Cell(0, 6, 'This is a computer-generated report and does not require a signature.', 0, 1, 'C');
    $pdf->Cell(0, 6, 'Generated on: ' . date('F d, Y h:i A'), 0, 1, 'C');
    $pdf->Cell(0, 6, 'Report Period: ' . date('F d, Y', strtotime($startDate)) . ' to ' . date('F d, Y', strtotime($endDate)), 0, 1, 'C');
    
    // Output the PDF
    $pdf->Output('Farmer_Credit_Analysis_' . $startDate . '_to_' . $endDate . '.pdf', 'I');
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