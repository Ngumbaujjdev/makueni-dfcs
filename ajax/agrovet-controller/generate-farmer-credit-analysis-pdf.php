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
    
    // Section 1: Get agrovet-specific credit portfolio summary
    $portfolioQuery = "SELECT 
                        COUNT(DISTINCT f.id) as total_farmers_with_credits,
                        COUNT(DISTINCT aca.id) as total_active_credits,
                        COALESCE(SUM(aic.total_with_interest), 0) as total_credit_value_distributed,
                        COALESCE(SUM(aic.total_with_interest - aic.remaining_balance), 0) as total_amount_repaid,
                        COALESCE(AVG(aic.total_with_interest), 0) as avg_credit_per_application,
                        COUNT(DISTINCT aca.id) as total_applications
                      FROM input_credit_applications aca
                      JOIN approved_input_credits aic ON aca.id = aic.credit_application_id
                      JOIN farmers f ON aca.farmer_id = f.id
                      WHERE aca.agrovet_id = '{$staff->agrovet_id}'
                      AND aca.application_date BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'
                      AND aic.status IN ('active', 'completed')";
    
    $portfolio = $app->selectOne($portfolioQuery);
    
    // Ensure portfolio has default values
    if (!$portfolio) {
        $portfolio = (object)[
            'total_farmers_with_credits' => 0,
            'total_active_credits' => 0,
            'total_credit_value_distributed' => 0,
            'total_amount_repaid' => 0,
            'avg_credit_per_application' => 0,
            'total_applications' => 0
        ];
    }
    
    // Calculate system repayment rate
    $systemRepaymentRate = $portfolio->total_credit_value_distributed > 0 ? 
                          ($portfolio->total_amount_repaid / $portfolio->total_credit_value_distributed) * 100 : 0;
    
    // Section 2: Get agrovet's farmer performance distribution
    $performanceQuery = "SELECT 
                          CASE 
                              WHEN repayment_rate >= 90 THEN 'Excellent (90%+)'
                              WHEN repayment_rate >= 70 THEN 'Good (70-89%)'
                              WHEN repayment_rate >= 50 THEN 'Average (50-69%)'
                              ELSE 'Poor (<50%)'
                          END as performance_tier,
                          COUNT(*) as farmer_count,
                          ROUND(AVG(repayment_rate), 2) as avg_repayment_rate,
                          COALESCE(SUM(total_credit_value), 0) as total_value
                        FROM (
                            SELECT 
                                f.id,
                                CASE 
                                    WHEN SUM(aic.total_with_interest) > 0 
                                    THEN ((SUM(aic.total_with_interest) - SUM(aic.remaining_balance)) / SUM(aic.total_with_interest)) * 100
                                    ELSE 0 
                                END as repayment_rate,
                                SUM(aic.total_with_interest) as total_credit_value
                            FROM farmers f
                            JOIN input_credit_applications ica ON f.id = ica.farmer_id
                            JOIN approved_input_credits aic ON ica.id = aic.credit_application_id
                            WHERE ica.agrovet_id = '{$staff->agrovet_id}'
                            AND ica.application_date BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'
                            AND aic.status IN ('active', 'completed')
                            GROUP BY f.id
                        ) farmer_performance
                        GROUP BY performance_tier
                        ORDER BY 
                            CASE performance_tier
                                WHEN 'Excellent (90%+)' THEN 1
                                WHEN 'Good (70-89%)' THEN 2
                                WHEN 'Average (50-69%)' THEN 3
                                WHEN 'Poor (<50%)' THEN 4
                            END";
    
    $performance = $app->select_all($performanceQuery);
    if (!$performance || !is_array($performance)) {
        $performance = [];
    }
    
    // Section 3: Get agrovet's farmer category distribution
    $categoryQuery = "SELECT 
                        fc.name as category_name,
                        COUNT(DISTINCT f.id) as farmer_count,
                        COUNT(DISTINCT ica.id) as application_count,
                        COALESCE(SUM(aic.total_with_interest), 0) as total_credit_value,
                        COALESCE(AVG(aic.total_with_interest), 0) as avg_credit_value
                      FROM farmer_categories fc
                      LEFT JOIN farmers f ON fc.id = f.category_id
                      LEFT JOIN input_credit_applications ica ON f.id = ica.farmer_id
                      LEFT JOIN approved_input_credits aic ON ica.id = aic.credit_application_id
                      WHERE ica.agrovet_id = '{$staff->agrovet_id}'
                      AND ica.application_date BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'
                      AND aic.status IN ('active', 'completed')
                      GROUP BY fc.id, fc.name
                      ORDER BY total_credit_value DESC";
    
    $categories = $app->select_all($categoryQuery);
    if (!$categories || !is_array($categories)) {
        $categories = [];
    }
    
    // Section 4: Get agrovet's input demand analysis
    $inputDemandQuery = "SELECT 
                          ici.input_type,
                          COUNT(ici.id) as total_requests,
                          COUNT(DISTINCT ica.farmer_id) as unique_farmers,
                          COALESCE(SUM(ici.total_price), 0) as total_value,
                          COALESCE(AVG(ici.total_price), 0) as avg_value_per_request
                        FROM input_credit_items ici
                        JOIN input_credit_applications ica ON ici.credit_application_id = ica.id
                        JOIN approved_input_credits aic ON ica.id = aic.credit_application_id
                        WHERE ica.agrovet_id = '{$staff->agrovet_id}'
                        AND ica.application_date BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'
                        AND aic.status IN ('active', 'completed')
                        GROUP BY ici.input_type
                        ORDER BY total_value DESC";
    
    $inputDemand = $app->select_all($inputDemandQuery);
    if (!$inputDemand || !is_array($inputDemand)) {
        $inputDemand = [];
    }
    
    // Section 5: Get agrovet's monthly repayment trends
    $repaymentTrendsQuery = "SELECT 
                              DATE_FORMAT(icr.deduction_date, '%Y-%m') as month_year,
                              COUNT(icr.id) as repayment_count,
                              COALESCE(SUM(icr.amount), 0) as total_repaid,
                              COUNT(DISTINCT icr.approved_credit_id) as unique_credits_repaid
                            FROM input_credit_repayments icr
                            JOIN approved_input_credits aic ON icr.approved_credit_id = aic.id
                            JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                            WHERE ica.agrovet_id = '{$staff->agrovet_id}'
                            AND icr.deduction_date BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'
                            GROUP BY DATE_FORMAT(icr.deduction_date, '%Y-%m')
                            ORDER BY month_year DESC
                            LIMIT 6";
    
    $repaymentTrends = $app->select_all($repaymentTrendsQuery);
    if (!$repaymentTrends || !is_array($repaymentTrends)) {
        $repaymentTrends = [];
    }
    
    // Create new PDF document
    $pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('DFCS System');
    $pdf->SetAuthor('Makueni DFCS');
    $pdf->SetTitle('Farmer Input Credit Analysis Report');
    $pdf->SetSubject('System-wide Farmer Credit Performance Analysis');
    
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
    $logoPath = '../../assets/images/brand-logos/logo3.png';
    if (file_exists($logoPath)) {
        $pdf->Image($logoPath, 15, 10, 30, 0, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    }
    
    // Document Title
    $pdf->SetFont('helvetica', 'B', 20);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY(15, 15);
    $pdf->Cell(0, 10, 'FARMER INPUT CREDIT ANALYSIS REPORT', 0, 1, 'C');
    
    // Agrovet name and subtitle
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 6, $staff->agrovet_name, 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 6, 'Farmer Credit Performance Analysis', 0, 1, 'C');
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
    $pdf->Cell(0, 6, 'Period: ' . date('F d, Y', strtotime($startDate)) . ' to ' . date('F d, Y', strtotime($endDate)), 0, 1, 'C');
    $pdf->Cell(0, 6, 'Generated on: ' . date('F d, Y h:i A'), 0, 1, 'C');
    
    $pdf->Ln(8);
    
    // ===== SECTION 1: AGROVET CREDIT PORTFOLIO SUMMARY =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '1. AGROVET CREDIT PORTFOLIO SUMMARY', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Create a 2x3 grid of summary boxes
    $boxWidth = 85;
    $boxHeight = 35;
    $margin = 10;
    $startY = $pdf->GetY();
    
    // Box styling
    $pdf->SetDrawColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetLineWidth(0.5);
    
    // First row
    // Total Farmers with Credits
    $pdf->Rect(15, $startY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY(20, $startY + 3);
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 5, 'Total Farmers with Credits', 0, 1);
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY(20, $startY + 10);
    $pdf->Cell($boxWidth - 10, 8, $portfolio->total_farmers_with_credits, 0, 1);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY(20, $startY + 20);
    $pdf->Cell($boxWidth - 10, 4, 'Served by this agrovet', 0, 1);
    
    // Total Credit Value
    $rightColX = 15 + $boxWidth + $margin;
    $pdf->Rect($rightColX, $startY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY($rightColX + 5, $startY + 3);
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 5, 'Total Credit Value', 0, 1);
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY($rightColX + 5, $startY + 10);
    $pdf->Cell($boxWidth - 10, 8, 'KES ' . number_format($portfolio->total_credit_value_distributed, 0), 0, 1);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY($rightColX + 5, $startY + 20);
    $pdf->Cell($boxWidth - 10, 4, 'Distributed by this agrovet', 0, 1);
    
    // Second row
    $secondRowY = $startY + $boxHeight + 8;
    
    // Total Amount Repaid
    $pdf->Rect(15, $secondRowY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY(20, $secondRowY + 3);
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 5, 'Total Amount Repaid', 0, 1);
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY(20, $secondRowY + 10);
    $pdf->Cell($boxWidth - 10, 8, 'KES ' . number_format($portfolio->total_amount_repaid, 0), 0, 1);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY(20, $secondRowY + 20);
    $pdf->Cell($boxWidth - 10, 4, 'Agrovet repayment rate: ' . number_format($systemRepaymentRate, 1) . '%', 0, 1);
    
    // Average Credit Size
    $pdf->Rect($rightColX, $secondRowY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY($rightColX + 5, $secondRowY + 3);
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 5, 'Average Credit Size', 0, 1);
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY($rightColX + 5, $secondRowY + 10);
    $pdf->Cell($boxWidth - 10, 8, 'KES ' . number_format($portfolio->avg_credit_per_application, 0), 0, 1);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY($rightColX + 5, $secondRowY + 20);
    $pdf->Cell($boxWidth - 10, 4, 'For this agrovet', 0, 1);
    
    $pdf->SetY($secondRowY + $boxHeight + 10);
    
    // ===== SECTION 2: FARMER PERFORMANCE DISTRIBUTION =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '2. FARMER PERFORMANCE DISTRIBUTION', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Performance distribution table
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    
    // Table header
    $pdf->Cell(45, 8, 'Performance Tier', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Farmer Count', 1, 0, 'C', true);
    $pdf->Cell(35, 8, 'Avg Repayment Rate', 1, 0, 'C', true);
    $pdf->Cell(40, 8, 'Total Credit Value (KES)', 1, 0, 'C', true);
    $pdf->Cell(35, 8, 'Percentage of Farmers', 1, 1, 'C', true);
    
    // Reset styling for table rows
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    
    // Calculate total farmers for percentage
    $totalFarmersInPerformance = array_sum(array_column($performance, 'farmer_count'));
    
    if (count($performance) > 0) {
        foreach ($performance as $index => $tier) {
            $fillRow = $index % 2 == 0;
            if ($fillRow) {
                $pdf->SetFillColor(245, 245, 245);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            
            $pdf->Cell(45, 7, $tier->performance_tier, 1, 0, 'L', $fillRow);
            $pdf->Cell(30, 7, $tier->farmer_count, 1, 0, 'C', $fillRow);
            $pdf->Cell(35, 7, number_format($tier->avg_repayment_rate, 1) . '%', 1, 0, 'C', $fillRow);
            $pdf->Cell(40, 7, number_format($tier->total_value, 2), 1, 0, 'R', $fillRow);
            
            $percentage = $totalFarmersInPerformance > 0 ? ($tier->farmer_count / $totalFarmersInPerformance) * 100 : 0;
            $pdf->Cell(35, 7, number_format($percentage, 1) . '%', 1, 1, 'C', $fillRow);
        }
    } else {
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(185, 8, 'No farmer performance data found in the selected period', 1, 1, 'C', true);
    }
    
    $pdf->Ln(5);
    
    // ===== SECTION 3: FARMER CATEGORY DISTRIBUTION =====
    if ($pdf->GetY() > 230) {
        $pdf->AddPage();
    }
    
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '3. FARMER CATEGORY DISTRIBUTION', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Category distribution table
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    
    // Table header
    $pdf->Cell(40, 8, 'Farmer Category', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Farmers', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Applications', 1, 0, 'C', true);
    $pdf->Cell(45, 8, 'Total Credit Value (KES)', 1, 0, 'C', true);
    $pdf->Cell(45, 8, 'Avg Credit Value (KES)', 1, 1, 'C', true);
    
    // Reset styling for table rows
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    
    if (count($categories) > 0) {
        foreach ($categories as $index => $category) {
            $fillRow = $index % 2 == 0;
            if ($fillRow) {
                $pdf->SetFillColor(245, 245, 245);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            
            $pdf->Cell(40, 7, $category->category_name, 1, 0, 'L', $fillRow);
            $pdf->Cell(25, 7, $category->farmer_count, 1, 0, 'C', $fillRow);
            $pdf->Cell(30, 7, $category->application_count, 1, 0, 'C', $fillRow);
            $pdf->Cell(45, 7, number_format($category->total_credit_value, 2), 1, 0, 'R', $fillRow);
            $pdf->Cell(45, 7, number_format($category->avg_credit_value, 2), 1, 1, 'R', $fillRow);
        }
    } else {
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(185, 8, 'No farmer category data found in the selected period', 1, 1, 'C', true);
    }
    
    $pdf->Ln(5);
    
    // ===== SECTION 4: AGROVET INPUT DEMAND ANALYSIS =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '4. AGROVET INPUT DEMAND ANALYSIS', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Input demand table
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    
    // Table header
    $pdf->Cell(35, 8, 'Input Type', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Total Requests', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Unique Farmers', 1, 0, 'C', true);
    $pdf->Cell(45, 8, 'Total Value (KES)', 1, 0, 'C', true);
    $pdf->Cell(45, 8, 'Avg Value/Request (KES)', 1, 1, 'C', true);
    
    // Reset styling for table rows
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    
    if (count($inputDemand) > 0) {
        foreach ($inputDemand as $index => $input) {
            $fillRow = $index % 2 == 0;
            if ($fillRow) {
                $pdf->SetFillColor(245, 245, 245);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            
            $pdf->Cell(35, 7, ucfirst($input->input_type), 1, 0, 'L', $fillRow);
            $pdf->Cell(30, 7, $input->total_requests, 1, 0, 'C', $fillRow);
            $pdf->Cell(30, 7, $input->unique_farmers, 1, 0, 'C', $fillRow);
            $pdf->Cell(45, 7, number_format($input->total_value, 2), 1, 0, 'R', $fillRow);
            $pdf->Cell(45, 7, number_format($input->avg_value_per_request, 2), 1, 1, 'R', $fillRow);
        }
    } else {
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(185, 8, 'No input demand data found in the selected period', 1, 1, 'C', true);
    }
    
    $pdf->Ln(8);
    
    // ===== SECTION 5: REPAYMENT TRENDS =====
    if ($pdf->GetY() > 220) {
        $pdf->AddPage();
    }
    
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '5. MONTHLY REPAYMENT TRENDS', 0, 1, 'L');
    $pdf->Ln(2);
    
    if (count($repaymentTrends) > 0) {
        // Repayment trends table
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        
        // Table header
        $pdf->Cell(40, 8, 'Month', 1, 0, 'C', true);
        $pdf->Cell(35, 8, 'Repayment Count', 1, 0, 'C', true);
        $pdf->Cell(50, 8, 'Total Repaid (KES)', 1, 0, 'C', true);
        $pdf->Cell(40, 8, 'Credits Involved', 1, 0, 'C', true);
        $pdf->Cell(20, 8, 'Trend', 1, 1, 'C', true);
        
        // Reset styling for table rows
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 8);
        
        foreach ($repaymentTrends as $index => $trend) {
            $fillRow = $index % 2 == 0;
            if ($fillRow) {
                $pdf->SetFillColor(245, 245, 245);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            
            $monthName = date('M Y', strtotime($trend->month_year . '-01'));
            $pdf->Cell(40, 7, $monthName, 1, 0, 'C', $fillRow);
            $pdf->Cell(35, 7, $trend->repayment_count, 1, 0, 'C', $fillRow);
            $pdf->Cell(50, 7, number_format($trend->total_repaid, 2), 1, 0, 'R', $fillRow);
            $pdf->Cell(40, 7, $trend->unique_credits_repaid, 1, 0, 'C', $fillRow);
            
            // Simple trend indicator
            $trendIndicator = $index < count($repaymentTrends) - 1 ? 
                            ($trend->total_repaid > $repaymentTrends[$index + 1]->total_repaid ? '↑' : '↓') : '-';
            $pdf->Cell(20, 7, $trendIndicator, 1, 1, 'C', $fillRow);
        }
    } else {
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(185, 8, 'No repayment trend data found in the selected period', 1, 1, 'C', true);
    }
    
    $pdf->Ln(8);
    
    // ===== SECTION 6: STRATEGIC RECOMMENDATIONS =====
    if ($pdf->GetY() > 200) {
        $pdf->AddPage();
    }
    
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '6. STRATEGIC RECOMMENDATIONS', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Generate dynamic recommendations based on data
    $recommendations = [];
    
    // Repayment rate analysis
    if ($systemRepaymentRate >= 80) {
        $recommendations[] = "• Excellent agrovet repayment rate of " . number_format($systemRepaymentRate, 1) . "%. Consider expanding credit program.";
    } elseif ($systemRepaymentRate >= 60) {
        $recommendations[] = "• Moderate repayment rate of " . number_format($systemRepaymentRate, 1) . "%. Focus on farmer education and support.";
    } else {
        $recommendations[] = "• Low repayment rate of " . number_format($systemRepaymentRate, 1) . "%. Implement stricter credit assessment.";
    }
    
    // Farmer distribution analysis
    $totalFarmersCount = array_sum(array_column($performance, 'farmer_count'));
    if ($totalFarmersCount > 0) {
        $excellentPerformers = 0;
        $poorPerformers = 0;
        
        foreach ($performance as $tier) {
            if (strpos($tier->performance_tier, 'Excellent') !== false) {
                $excellentPerformers = $tier->farmer_count;
            }
            if (strpos($tier->performance_tier, 'Poor') !== false) {
                $poorPerformers = $tier->farmer_count;
            }
        }
        
        $excellentPercentage = ($excellentPerformers / $totalFarmersCount) * 100;
        $poorPercentage = ($poorPerformers / $totalFarmersCount) * 100;
        
        if ($excellentPercentage >= 30) {
            $recommendations[] = "• " . number_format($excellentPercentage, 1) . "% excellent performers. Use as mentors for struggling farmers.";
        }
        
        if ($poorPercentage >= 20) {
            $recommendations[] = "• " . number_format($poorPercentage, 1) . "% poor performers need intervention and support programs.";
        }
    }
    
    // Input demand analysis
    if (count($inputDemand) > 0) {
        $topInput = $inputDemand[0];
        $recommendations[] = "• " . ucfirst($topInput->input_type) . " is the most demanded input type. Ensure adequate supply.";
        
        if (count($inputDemand) >= 3) {
            $recommendations[] = "• Diversified input demand across " . count($inputDemand) . " categories indicates healthy farming practices.";
        }
    }
    
    // Credit size analysis
    if ($portfolio->avg_credit_per_application < 50000) {
        $recommendations[] = "• Low average credit size suggests smallholder focus. Consider micro-finance partnerships.";
    } elseif ($portfolio->avg_credit_per_application > 200000) {
        $recommendations[] = "• High average credit size indicates commercial farmer focus. Ensure adequate risk management.";
    }
    
    // Display recommendations
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    
    foreach ($recommendations as $index => $recommendation) {
        if ($pdf->GetY() > 250) {
            $pdf->AddPage();
        }
        
        $pdf->SetXY(20, $pdf->GetY());
        $pdf->MultiCell(165, 6, ($index + 1) . ". " . $recommendation, 0, 'L', false, 1, '', '', true);
        $pdf->Ln(2);
    }
    
    $pdf->Ln(5);
    
    // ===== KEY PERFORMANCE INDICATORS SUMMARY =====
    if ($pdf->GetY() > 200) {
        $pdf->AddPage();
    }
    
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '7. KEY PERFORMANCE INDICATORS', 0, 1, 'L');
    $pdf->Ln(2);
    
    // KPI boxes in a grid
    $kpiBoxWidth = 85;
    $kpiBoxHeight = 30;
    $kpiStartY = $pdf->GetY();
    
    // KPI 1: Portfolio Health
    $pdf->Rect(15, $kpiStartY, $kpiBoxWidth, $kpiBoxHeight, 'D');
    $pdf->SetXY(20, $kpiStartY + 3);
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($kpiBoxWidth - 10, 5, 'Portfolio Health Score', 0, 1);
    
    // Calculate portfolio health (0-100)
    $healthScore = min(100, ($systemRepaymentRate + ($excellentPercentage ?? 50) + 
                              min(100, ($portfolio->total_farmers_with_credits / 100) * 20)) / 3);
    
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($healthScore >= 70 ? $successColor[0] : ($healthScore >= 50 ? $warningColor[0] : $dangerColor[0]), 
                      $healthScore >= 70 ? $successColor[1] : ($healthScore >= 50 ? $warningColor[1] : $dangerColor[1]), 
                      $healthScore >= 70 ? $successColor[2] : ($healthScore >= 50 ? $warningColor[2] : $dangerColor[2]));
    $pdf->SetXY(20, $kpiStartY + 10);
    $pdf->Cell($kpiBoxWidth - 10, 8, number_format($healthScore, 1) . '/100', 0, 1);
    
    // KPI 2: Growth Trend
    $kpiRightX = 15 + $kpiBoxWidth + $margin;
    $pdf->Rect($kpiRightX, $kpiStartY, $kpiBoxWidth, $kpiBoxHeight, 'D');
    $pdf->SetXY($kpiRightX + 5, $kpiStartY + 3);
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($kpiBoxWidth - 10, 5, 'Credit Program Growth', 0, 1);
    
    // Calculate growth based on applications
    $growthIndicator = $portfolio->total_applications > 50 ? 'Expanding' : 
                      ($portfolio->total_applications > 20 ? 'Stable' : 'Emerging');
    
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY($kpiRightX + 5, $kpiStartY + 10);
    $pdf->Cell($kpiBoxWidth - 10, 8, $growthIndicator, 0, 1);
    
    $pdf->SetY($kpiStartY + $kpiBoxHeight + 10);
    
    // ===== DOCUMENT FOOTER WITH QR CODE =====
    $pdf->SetY(-50);
    
    // QR Code - Generate QR code with agrovet-specific report details
    $qrData = "DFCS Farmer Credit Analysis Report\n";
    $qrData .= "Agrovet: " . $staff->agrovet_name . "\n";
    $qrData .= "Period: " . date('M d, Y', strtotime($startDate)) . " to " . date('M d, Y', strtotime($endDate)) . "\n";
    $qrData .= "Generated: " . date('M d, Y H:i') . "\n";
    $qrData .= "Total Farmers: " . $portfolio->total_farmers_with_credits . "\n";
    $qrData .= "Total Credits: KES " . number_format($portfolio->total_credit_value_distributed, 2);
    
    // Create QR Code in footer
    $style = array(
        'border' => 1,
        'vpadding' => 'auto',
        'hpadding' => 'auto',
        'fgcolor' => array(0,0,0),
        'bgcolor' => array(255,255,255),
        'module_width' => 1,
        'module_height' => 1
    );
    
    $pdf->write2DBarcode($qrData, 'QRCODE,L', 15, $pdf->GetY(), 20, 20, $style, 'N');
    
    // Footer text next to QR code
    $pdf->SetXY(40, $pdf->GetY());
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->Cell(0, 4, 'This is a computer-generated report and does not require a signature.', 0, 1, 'L');
    $pdf->SetXY(40, $pdf->GetY());
    $pdf->Cell(0, 4, 'Generated on: ' . date('F d, Y h:i A') . ' | User ID: ' . $userId, 0, 1, 'L');
    $pdf->SetXY(40, $pdf->GetY());
    $pdf->Cell(0, 4, 'Report Period: ' . date('F d, Y', strtotime($startDate)) . ' to ' . date('F d, Y', strtotime($endDate)), 0, 1, 'L');
    $pdf->SetXY(40, $pdf->GetY());
    $pdf->Cell(0, 4, 'QR Code contains report summary for verification purposes', 0, 1, 'L');
    
    // Output the PDF
    $filename = 'Farmer_Credit_Analysis_Report_' . $startDate . '_to_' . $endDate . '.pdf';
    $pdf->Output($filename, 'I');
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