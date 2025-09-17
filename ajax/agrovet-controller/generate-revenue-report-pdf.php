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
    
    // Section 1: Get revenue summary stats for the agrovet within date range
    $statsQuery = "SELECT 
                    COUNT(aic.id) as total_credits,
                    SUM(ica.total_amount) as principal_amount,
                    SUM(aic.total_with_interest) as total_with_interest,
                    SUM(aic.total_with_interest - ica.total_amount) as interest_amount,
                    AVG((aic.total_with_interest - ica.total_amount) / ica.total_amount * 100) as avg_interest_rate,
                    SUM(aic.total_with_interest - aic.remaining_balance) as amount_collected,
                    SUM(aic.remaining_balance) as amount_outstanding
                  FROM approved_input_credits aic
                  JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                  WHERE ica.agrovet_id = '{$staff->agrovet_id}'
                  AND aic.approval_date BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'";
    
    $stats = $app->select_one($statsQuery);
    
    // Calculate revenue percentages
    $collectionPercentage = $stats->total_with_interest > 0 ? 
                          ($stats->amount_collected / $stats->total_with_interest) * 100 : 0;
    
    $interestPercentage = $stats->principal_amount > 0 ? 
                         ($stats->interest_amount / $stats->principal_amount) * 100 : 0;
    
    // Section 2: Get monthly revenue breakdown
    $monthlyQuery = "SELECT 
                      DATE_FORMAT(aic.approval_date, '%Y-%m') as month,
                      COUNT(aic.id) as credit_count,
                      SUM(ica.total_amount) as principal_amount,
                      SUM(aic.total_with_interest) as total_with_interest,
                      SUM(aic.total_with_interest - ica.total_amount) as interest_amount,
                      SUM(aic.total_with_interest - aic.remaining_balance) as amount_collected
                    FROM approved_input_credits aic
                    JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                    WHERE ica.agrovet_id = '{$staff->agrovet_id}'
                    AND aic.approval_date BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'
                    GROUP BY DATE_FORMAT(aic.approval_date, '%Y-%m')
                    ORDER BY month ASC";
    
    $monthlyRevenue = $app->select_all($monthlyQuery);
    
    // Section 3: Get revenue by input type
    $inputTypeQuery = "SELECT 
                        ici.input_type,
                        COUNT(DISTINCT aic.id) as credit_count,
                        SUM(ici.total_price) as principal_amount,
                        SUM(ici.total_price * (1 + (ica.credit_percentage / 100))) as total_with_interest,
                        SUM(ici.total_price * (ica.credit_percentage / 100)) as interest_amount
                      FROM input_credit_items ici
                      JOIN input_credit_applications ica ON ici.credit_application_id = ica.id
                      JOIN approved_input_credits aic ON ica.id = aic.credit_application_id
                      WHERE ica.agrovet_id = '{$staff->agrovet_id}'
                      AND aic.approval_date BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'
                      GROUP BY ici.input_type
                      ORDER BY interest_amount DESC";
    
    $inputTypeRevenue = $app->select_all($inputTypeQuery);
    
    // Section 4: Get top 10 revenue generating credits
    $topCreditsQuery = "SELECT 
                         aic.id,
                         aic.credit_application_id,
                         ica.total_amount as principal_amount,
                         aic.total_with_interest,
                         (aic.total_with_interest - ica.total_amount) as interest_amount,
                         aic.credit_percentage,
                         aic.fulfillment_date,
                         aic.status,
                         (aic.total_with_interest - aic.remaining_balance) as amount_collected,
                         aic.remaining_balance,
                         CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                         f.registration_number as farmer_reg
                       FROM approved_input_credits aic
                       JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                       JOIN farmers f ON ica.farmer_id = f.id
                       JOIN users u ON f.user_id = u.id
                       WHERE ica.agrovet_id = '{$staff->agrovet_id}'
                       AND aic.approval_date BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'
                       ORDER BY interest_amount DESC
                       LIMIT 10";
    
    $topCredits = $app->select_all($topCreditsQuery);
    
    // Create new PDF document
    $pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('DFCS System');
    $pdf->SetAuthor('Makueni DFCS');
    $pdf->SetTitle('Commission & Revenue Report');
    $pdf->SetSubject('Commission & Revenue for ' . $staff->agrovet_name);
    
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
    $pdf->Cell(0, 10, 'COMMISSION & REVENUE REPORT', 0, 1, 'C');
    
    // Agrovet name and date range
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 6, $staff->agrovet_name, 0, 1, 'C');
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
    $pdf->Cell(0, 6, 'Period: ' . date('F d, Y', strtotime($startDate)) . ' to ' . date('F d, Y', strtotime($endDate)), 0, 1, 'C');
    $pdf->Cell(0, 6, 'Generated on: ' . date('F d, Y'), 0, 1, 'C');
    
    $pdf->Ln(5);
    
    // ===== SECTION 1: REVENUE SUMMARY =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '1. REVENUE SUMMARY', 0, 1, 'L');
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
    // Total Credits box
    $pdf->Rect(15, $startY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY(20, $startY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 6, 'Total Credits Issued', 0, 1);
    $pdf->SetFont('helvetica', 'B', 18);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY(20, $startY + 13);
    $pdf->Cell($boxWidth - 10, 10, $stats->total_credits, 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY(20, $startY + 25);
    $pdf->Cell($boxWidth - 10, 5, 'During selected period', 0, 1);
    
    // Total Commission/Interest box
    $rightColX = 15 + $boxWidth + $margin;
    $pdf->Rect($rightColX, $startY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY($rightColX + 5, $startY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 6, 'Total Commission', 0, 1);
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY($rightColX + 5, $startY + 13);
    $pdf->Cell($boxWidth - 10, 10, 'KES ' . number_format($stats->interest_amount, 2), 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY($rightColX + 5, $startY + 25);
    $pdf->Cell($boxWidth - 10, 5, 'Interest on KES ' . number_format($stats->principal_amount, 2), 0, 1);
    
    // Set Y position for next row
    $secondRowY = $startY + $boxHeight + 10;
    
    // Second row of boxes
    // Revenue Collected box
    $pdf->Rect(15, $secondRowY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY(20, $secondRowY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 6, 'Revenue Collected', 0, 1);
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY(20, $secondRowY + 13);
    $pdf->Cell($boxWidth - 10, 10, 'KES ' . number_format($stats->amount_collected, 2), 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY(20, $secondRowY + 25);
    $pdf->Cell($boxWidth - 10, 5, number_format($collectionPercentage, 1) . '% of total value', 0, 1);
    
    // Average Interest Rate box
    $pdf->Rect($rightColX, $secondRowY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY($rightColX + 5, $secondRowY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 6, 'Average Interest Rate', 0, 1);
    $pdf->SetFont('helvetica', 'B', 18);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY($rightColX + 5, $secondRowY + 13);
    $pdf->Cell($boxWidth - 10, 10, number_format($stats->avg_interest_rate, 1) . '%', 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY($rightColX + 5, $secondRowY + 25);
    
    // Calculate markup percentage
    $markup = $stats->principal_amount > 0 ? 
             ($stats->interest_amount / $stats->principal_amount) * 100 : 0;
    
    $pdf->Cell($boxWidth - 10, 5, 'Overall markup: ' . number_format($markup, 1) . '%', 0, 1);
    
    // Set Y position after summary boxes
    $pdf->SetY($secondRowY + $boxHeight + 10);
    
    // ===== SECTION 2: MONTHLY REVENUE BREAKDOWN =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '2. MONTHLY REVENUE BREAKDOWN', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Monthly revenue table
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    
    // Table header
    $pdf->Cell(30, 8, 'Month', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Credits', 1, 0, 'C', true);
    $pdf->Cell(35, 8, 'Principal (KES)', 1, 0, 'C', true);
    $pdf->Cell(35, 8, 'Interest (KES)', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Interest %', 1, 0, 'C', true);
    $pdf->Cell(35, 8, 'Collected (KES)', 1, 1, 'C', true);
    
    // Reset styling for table rows
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    
    // Monthly revenue rows
    if ($monthlyRevenue && count($monthlyRevenue) > 0) {
        foreach ($monthlyRevenue as $index => $month) {
            // Alternate row fill
            $fillRow = $index % 2 == 0;
            if ($fillRow) {
                $pdf->SetFillColor(245, 245, 245);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            
            // Format month
            $monthDate = date_create($month->month . '-01');
            $formattedMonth = date_format($monthDate, 'M Y');
            $pdf->Cell(30, 7, $formattedMonth, 1, 0, 'C', $fillRow);
            
            // Credits count
            $pdf->Cell(25, 7, $month->credit_count, 1, 0, 'C', $fillRow);
            
            // Principal amount
            $pdf->Cell(35, 7, number_format($month->principal_amount, 2), 1, 0, 'R', $fillRow);
            
            // Interest amount
            $pdf->Cell(35, 7, number_format($month->interest_amount, 2), 1, 0, 'R', $fillRow);
            
            // Interest percentage
            $interestPct = $month->principal_amount > 0 ? 
                         ($month->interest_amount / $month->principal_amount) * 100 : 0;
            $pdf->Cell(25, 7, number_format($interestPct, 1) . '%', 1, 0, 'C', $fillRow);
            
            // Collected amount
            $pdf->Cell(35, 7, number_format($month->amount_collected, 2), 1, 1, 'R', $fillRow);
        }
        
        // Totals row
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(30, 8, 'TOTAL', 1, 0, 'C', true);
        $pdf->Cell(25, 8, $stats->total_credits, 1, 0, 'C', true);
        $pdf->Cell(35, 8, number_format($stats->principal_amount, 2), 1, 0, 'R', true);
        $pdf->Cell(35, 8, number_format($stats->interest_amount, 2), 1, 0, 'R', true);
        $pdf->Cell(25, 8, number_format($markup, 1) . '%', 1, 0, 'C', true);
        $pdf->Cell(35, 8, number_format($stats->amount_collected, 2), 1, 1, 'R', true);
    } else {
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(185, 8, 'No monthly revenue data available for the selected period', 1, 1, 'C', true);
    }
    
    $pdf->Ln(5);
    
    // ===== SECTION 3: REVENUE BY INPUT TYPE =====
    // Check if we need a new page
    if ($pdf->GetY() > 220) {
        $pdf->AddPage();
    }
    
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '3. REVENUE BY INPUT TYPE', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Input type revenue table
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    
    // Table header
    $pdf->Cell(50, 8, 'Input Type', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Credits', 1, 0, 'C', true);
    $pdf->Cell(35, 8, 'Principal (KES)', 1, 0, 'C', true);
    $pdf->Cell(35, 8, 'Interest (KES)', 1, 0, 'C', true);
    $pdf->Cell(35, 8, 'Interest %', 1, 1, 'C', true);
    
    // Reset styling for table rows
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    
    // Input type revenue rows
    if ($inputTypeRevenue && count($inputTypeRevenue) > 0) {
        $totalPrincipal = 0;
        $totalInterest = 0;
        $totalCredits = 0;
        
        foreach ($inputTypeRevenue as $index => $type) {
            // Alternate row fill
            $fillRow = $index % 2 == 0;
            if ($fillRow) {
                $pdf->SetFillColor(245, 245, 245);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            
            // Input type name
            $pdf->Cell(50, 7, ucfirst($type->input_type), 1, 0, 'L', $fillRow);
            
            // Credits count
            $pdf->Cell(25, 7, $type->credit_count, 1, 0, 'C', $fillRow);
            
            // Principal amount
            $pdf->Cell(35, 7, number_format($type->principal_amount, 2), 1, 0, 'R', $fillRow);
            
            // Interest amount
            $pdf->Cell(35, 7, number_format($type->interest_amount, 2), 1, 0, 'R', $fillRow);
            
            // Interest percentage
            $typeInterestPct = $type->principal_amount > 0 ? 
                             ($type->interest_amount / $type->principal_amount) * 100 : 0;
            $pdf->Cell(35, 7, number_format($typeInterestPct, 1) . '%', 1, 1, 'C', $fillRow);
            
            // Add to totals
            $totalPrincipal += $type->principal_amount;
            $totalInterest += $type->interest_amount;
            $totalCredits += $type->credit_count;
        }
        
        // Totals row
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(50, 8, 'TOTAL', 1, 0, 'C', true);
        $pdf->Cell(25, 8, $totalCredits, 1, 0, 'C', true);
        $pdf->Cell(35, 8, number_format($totalPrincipal, 2), 1, 0, 'R', true);
        $pdf->Cell(35, 8, number_format($totalInterest, 2), 1, 0, 'R', true);
        
        $totalInterestPct = $totalPrincipal > 0 ? 
                          ($totalInterest / $totalPrincipal) * 100 : 0;
        $pdf->Cell(35, 8, number_format($totalInterestPct, 1) . '%', 1, 1, 'C', true);
    } else {
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(180, 8, 'No input type revenue data available for the selected period', 1, 1, 'C', true);
    }
    
    $pdf->Ln(5);
    
    // ===== SECTION 4: TOP REVENUE GENERATING CREDITS =====
    // Add a new page for top credits
    $pdf->AddPage();
    
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '4. TOP REVENUE GENERATING CREDITS', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Top credits table
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    
    // Table header
    $pdf->Cell(25, 8, 'Credit ID', 1, 0, 'C', true);
    $pdf->Cell(35, 8, 'Farmer', 1, 0, 'C', true);
    $pdf->Cell(35, 8, 'Principal (KES)', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Interest (KES)', 1, 0, 'C', true);
    $pdf->Cell(20, 8, 'Rate %', 1, 0, 'C', true);
    $pdf->Cell(35, 8, 'Collection Status', 1, 1, 'C', true);
    
    // Reset styling for table rows
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    
    // Top credits rows
    if ($topCredits && count($topCredits) > 0) {
        foreach ($topCredits as $index => $credit) {
            // Alternate row fill
            $fillRow = $index % 2 == 0;
            if ($fillRow) {
                $pdf->SetFillColor(245, 245, 245);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            
            // Credit ID
            $creditId = 'IC' . str_pad($credit->id, 5, '0', STR_PAD_LEFT);
            $pdf->Cell(25, 7, $creditId, 1, 0, 'C', $fillRow);
            
            // Farmer name (truncated if too long)
            $farmerName = strlen($credit->farmer_name) > 15 ? 
                         substr($credit->farmer_name, 0, 12) . '...' : $credit->farmer_name;
            $pdf->Cell(35, 7, $farmerName . ' (' . $credit->farmer_reg . ')', 1, 0, 'L', $fillRow);
            
            // Principal amount
            $pdf->Cell(35, 7, number_format($credit->principal_amount, 2), 1, 0, 'R', $fillRow);
            
            // Interest amount
            $pdf->Cell(30, 7, number_format($credit->interest_amount, 2), 1, 0, 'R', $fillRow);
            
            // Interest rate
            $pdf->Cell(20, 7, $credit->credit_percentage . '%', 1, 0, 'C', $fillRow);
            
            // Collection status with progress bar
            $cellX = $pdf->GetX();
            $cellY = $pdf->GetY();
            $cellWidth = 35;
            $cellHeight = 7;
            
            // Draw cell background
            $pdf->Cell($cellWidth, $cellHeight, '', 1, 0, 'C', $fillRow);
            
            // Calculate collection percentage
            $collectionPct = $credit->total_with_interest > 0 ? 
                           ($credit->amount_collected / $credit->total_with_interest) * 100 : 0;
            
            // Draw progress bar background
            $barWidth = 25;
            $barHeight = 3;
            $barX = $cellX + ($cellWidth - $barWidth) / 2;
            $barY = $cellY + ($cellHeight - $barHeight) / 2;
            
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Rect($barX, $barY, $barWidth, $barHeight, 'F');
            
            // Draw progress bar fill
            $progress = min(100, max(0, $collectionPct));
            $progressWidth = ($progress / 100) * $barWidth;
            
            // Set color based on progress
            if ($progress >= 75) {
                $pdf->SetFillColor($successColor[0], $successColor[1], $successColor[2]);
            } elseif ($progress >= 40) {
                $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
            } else {
                $pdf->SetFillColor($warningColor[0], $warningColor[1], $warningColor[2]);
            }
            
            $pdf->Rect($barX, $barY, $progressWidth, $barHeight, 'F');
            
            // Add percentage text
            $pdf->SetXY($cellX, $cellY + 1);
            $pdf->SetFont('helvetica', 'B', 7);
            $pdf->Cell($cellWidth, $cellHeight - 2, number_format($progress, 1) . '%', 0, 1, 'C', false);
            
            // Reset text position for next row
            $pdf->SetXY($cellX + $cellWidth, $cellY);
            $pdf->SetFont('helvetica', '', 8);
            $pdf->Ln();
        }
    } else {
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(180, 8, 'No credit data available for the selected period', 1, 1, 'C', true);
    }
    
    $pdf->Ln(5);
    
    // ===== ANALYSIS SECTION =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '5. REVENUE PERFORMANCE ANALYSIS', 0, 1, 'L');
    $pdf->Ln(2);
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    
    // Calculate average commission per credit
    $avgCommission = $stats->total_credits > 0 ? 
                   $stats->interest_amount / $stats->total_credits : 0;
    
    // Collection ratio
    $collectionRatio = $stats->total_with_interest > 0 ? 
                     $stats->amount_collected / $stats->total_with_interest : 0;
    
    // Analysis text
    $pdf->MultiCell(0, 6, 'During the period from ' . date('F d, Y', strtotime($startDate)) . ' to ' . 
                 date('F d, Y', strtotime($endDate)) . ', ' . $staff->agrovet_name . ' has issued a total of ' . 
                 $stats->total_credits . ' input credits worth KES ' . number_format($stats->principal_amount, 2) . 
                 ' in principal value.', 0, 'L');
    
    $pdf->Ln(2);
    
    $pdf->MultiCell(0, 6, 'The total commission/interest generated from these credits is KES ' . 
                 number_format($stats->interest_amount, 2) . ', representing an average markup of ' . 
                 number_format($markup, 1) . '% on the principal amount. This translates to an average of KES ' . 
                 number_format($avgCommission, 2) . ' per credit issued.', 0, 'L');
    
    $pdf->Ln(2);
    
    $pdf->MultiCell(0, 6, 'Of the total expected revenue of KES ' . number_format($stats->total_with_interest, 2) . 
                 ' (principal + interest), KES ' . number_format($stats->amount_collected, 2) . ' (' . 
                 number_format($collectionPercentage, 1) . '%) has been collected, with KES ' . 
                 number_format($stats->amount_outstanding, 2) . ' still outstanding.', 0, 'L');
    
    $pdf->Ln(2);
    
    // Recommendations based on data
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
    $pdf->Cell(0, 8, 'Recommendations:', 0, 1, 'L');
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    
    // Add bullet points with recommendations
    $pdf->SetFont('', 'B', 9);
    $pdf->Cell(5, 6, '•', 0, 0, 'L');
    $pdf->SetFont('', '', 10);
    
    if ($collectionPercentage < 50) {
        $pdf->MultiCell(0, 6, 'Focus on improving collection rates. Current collection rate of ' . 
                     number_format($collectionPercentage, 1) . '% indicates potential challenges with repayments.', 0, 'L');
    } else {
        $pdf->MultiCell(0, 6, 'Maintain the current collection strategies which have achieved a ' . 
                     number_format($collectionPercentage, 1) . '% collection rate.', 0, 'L');
    }
    
    $pdf->SetFont('', 'B', 9);
    $pdf->Cell(5, 6, '•', 0, 0, 'L');
    $pdf->SetFont('', '', 10);
    
    // Identify the most profitable input type if available
    if ($inputTypeRevenue && count($inputTypeRevenue) > 0) {
        $topInputType = $inputTypeRevenue[0];
        $pdf->MultiCell(0, 6, 'Consider focusing more on ' . ucfirst($topInputType->input_type) . 
                     ' credits which have shown the highest revenue generation at KES ' . 
                     number_format($topInputType->interest_amount, 2) . '.', 0, 'L');
    } else {
        $pdf->MultiCell(0, 6, 'Diversify input types to identify which generates the highest revenue.', 0, 'L');
    }
    
    $pdf->SetFont('', 'B', 9);
    $pdf->Cell(5, 6, '•', 0, 0, 'L');
    $pdf->SetFont('', '', 10);
    
    // Examine monthly trends
    if ($monthlyRevenue && count($monthlyRevenue) > 1) {
        // Check if trend is increasing or decreasing
        $firstMonth = $monthlyRevenue[0];
        $lastMonth = $monthlyRevenue[count($monthlyRevenue) - 1];
        
        if ($lastMonth->interest_amount > $firstMonth->interest_amount) {
            $pdf->MultiCell(0, 6, 'The positive trend in monthly revenue should be maintained and built upon.', 0, 'L');
        } else {
            $pdf->MultiCell(0, 6, 'Investigate the declining trend in monthly revenue to identify causes and implement corrective measures.', 0, 'L');
        }
    } else {
        $pdf->MultiCell(0, 6, 'Track monthly performance metrics to identify seasonal patterns and optimize revenue generation accordingly.', 0, 'L');
    }
    
    // ===== DOCUMENT FOOTER =====
    $pdf->SetY(-25);
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->Cell(0, 6, 'This is a computer-generated report and does not require a signature.', 0, 1, 'C');
    $pdf->Cell(0, 6, 'Generated on: ' . date('F d, Y h:i A'), 0, 1, 'C');
    $pdf->Cell(0, 6, 'Report Period: ' . date('F d, Y', strtotime($startDate)) . ' to ' . date('F d, Y', strtotime($endDate)), 0, 1, 'C');
    
    // Output the PDF
    $pdf->Output('Commission_Revenue_Report_' . $startDate . '_to_' . $endDate . '.pdf', 'I');
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