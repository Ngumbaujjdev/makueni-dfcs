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
    
    // Section 1: Get distribution summary stats for the agrovet within date range
    $statsQuery = "SELECT 
                    COUNT(ici.id) as total_items_distributed,
                    COALESCE(SUM(ici.total_price), 0) as total_value_distributed,
                    COUNT(DISTINCT ica.farmer_id) as farmers_served,
                    COUNT(DISTINCT ici.input_type) as input_types_count,
                    COUNT(DISTINCT ica.id) as total_applications
                  FROM input_credit_items ici
                  JOIN input_credit_applications ica ON ici.credit_application_id = ica.id
                  JOIN approved_input_credits aic ON ica.id = aic.credit_application_id
                  WHERE ica.agrovet_id = '{$staff->agrovet_id}'
                  AND aic.fulfillment_date BETWEEN '{$startDate}' AND '{$endDate}'
                  AND aic.status IN ('active', 'completed')";
    
    $stats = $app->selectOne($statsQuery);
    
    // Ensure stats has default values if query returns null
    if (!$stats) {
        $stats = (object)[
            'total_items_distributed' => 0,
            'total_value_distributed' => 0,
            'farmers_served' => 0,
            'input_types_count' => 0,
            'total_applications' => 0
        ];
    }
    
    // Section 2: Get distribution breakdown by input type
    $typeBreakdownQuery = "SELECT 
                            ici.input_type,
                            COUNT(ici.id) as item_count,
                            COALESCE(SUM(ici.quantity), 0) as total_quantity,
                            COALESCE(SUM(ici.total_price), 0) as total_value,
                            COUNT(DISTINCT ica.id) as application_count,
                            COUNT(DISTINCT ica.farmer_id) as farmer_count,
                            ROUND((SUM(ici.total_price) / (SELECT SUM(ici2.total_price) 
                                                         FROM input_credit_items ici2
                                                         JOIN input_credit_applications ica2 ON ici2.credit_application_id = ica2.id
                                                         JOIN approved_input_credits aic2 ON ica2.id = aic2.credit_application_id
                                                         WHERE ica2.agrovet_id = '{$staff->agrovet_id}'
                                                         AND aic2.fulfillment_date BETWEEN '{$startDate}' AND '{$endDate}'
                                                         AND aic2.status IN ('active', 'completed'))) * 100, 2) as percentage_of_total
                          FROM input_credit_items ici
                          JOIN input_credit_applications ica ON ici.credit_application_id = ica.id
                          JOIN approved_input_credits aic ON ica.id = aic.credit_application_id
                          WHERE ica.agrovet_id = '{$staff->agrovet_id}'
                          AND aic.fulfillment_date BETWEEN '{$startDate}' AND '{$endDate}'
                          AND aic.status IN ('active', 'completed')
                          GROUP BY ici.input_type
                          ORDER BY total_value DESC";
    
    $typeBreakdown = $app->select_all($typeBreakdownQuery);
    
    // Fix: Ensure typeBreakdown is an array
    if (!$typeBreakdown || !is_array($typeBreakdown)) {
        $typeBreakdown = [];
    }
    
    // Section 3: Get top requested input items
    $topItemsQuery = "SELECT 
                        ici.input_name,
                        ici.input_type,
                        ici.unit,
                        COUNT(ici.id) as request_count,
                        COALESCE(SUM(ici.quantity), 0) as total_quantity,
                        COALESCE(SUM(ici.total_price), 0) as total_value,
                        COALESCE(AVG(ici.unit_price), 0) as avg_unit_price
                      FROM input_credit_items ici
                      JOIN input_credit_applications ica ON ici.credit_application_id = ica.id
                      JOIN approved_input_credits aic ON ica.id = aic.credit_application_id
                      WHERE ica.agrovet_id = '{$staff->agrovet_id}'
                      AND aic.fulfillment_date BETWEEN '{$startDate}' AND '{$endDate}'
                      AND aic.status IN ('active', 'completed')
                      GROUP BY ici.input_name, ici.input_type, ici.unit
                      ORDER BY total_value DESC
                      LIMIT 10";
    
    $topItems = $app->select_all($topItemsQuery);
    
    // Fix: Ensure topItems is an array
    if (!$topItems || !is_array($topItems)) {
        $topItems = [];
    }
    
    // Create new PDF document
    $pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('DFCS System');
    $pdf->SetAuthor('Makueni DFCS');
    $pdf->SetTitle('Input Type Distribution Report');
    $pdf->SetSubject('Input Distribution Analysis for ' . $staff->agrovet_name);
    
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
    // Logo - Fix: Use a relative path or check if file exists
    $logoPath = '../../assets/images/brand-logos/logo3.png';
    if (file_exists($logoPath)) {
        $pdf->Image($logoPath, 15, 10, 30, 0, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    }
    
    // Document Title
    $pdf->SetFont('helvetica', 'B', 20);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY(15, 15);
    $pdf->Cell(0, 10, 'INPUT TYPE DISTRIBUTION REPORT', 0, 1, 'C');
    
    // Agrovet name and date range
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 6, $staff->agrovet_name, 0, 1, 'C');
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
    $pdf->Cell(0, 6, 'Period: ' . date('F d, Y', strtotime($startDate)) . ' to ' . date('F d, Y', strtotime($endDate)), 0, 1, 'C');
    $pdf->Cell(0, 6, 'Generated on: ' . date('F d, Y'), 0, 1, 'C');
    
    $pdf->Ln(5);
    
    // ===== SECTION 1: DISTRIBUTION SUMMARY =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '1. DISTRIBUTION SUMMARY', 0, 1, 'L');
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
    // Total Items Distributed box
    $pdf->Rect(15, $startY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY(20, $startY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 6, 'Total Items Distributed', 0, 1);
    $pdf->SetFont('helvetica', 'B', 18);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY(20, $startY + 13);
    $pdf->Cell($boxWidth - 10, 10, $stats->total_items_distributed, 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY(20, $startY + 25);
    $pdf->Cell($boxWidth - 10, 5, 'From ' . $stats->total_applications . ' approved applications', 0, 1);
    
    // Total Value Distributed box
    $rightColX = 15 + $boxWidth + $margin;
    $pdf->Rect($rightColX, $startY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY($rightColX + 5, $startY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 6, 'Total Value Distributed', 0, 1);
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY($rightColX + 5, $startY + 13);
    $pdf->Cell($boxWidth - 10, 10, 'KES ' . number_format($stats->total_value_distributed, 2), 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY($rightColX + 5, $startY + 25);
    $pdf->Cell($boxWidth - 10, 5, 'Across ' . $stats->input_types_count . ' different input types', 0, 1);
    
    // Set Y position for next row
    $secondRowY = $startY + $boxHeight + 10;
    
    // Second row of boxes
    // Farmers Served box
    $pdf->Rect(15, $secondRowY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY(20, $secondRowY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 6, 'Farmers Served', 0, 1);
    $pdf->SetFont('helvetica', 'B', 18);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY(20, $secondRowY + 13);
    $pdf->Cell($boxWidth - 10, 10, $stats->farmers_served, 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY(20, $secondRowY + 25);
    
    // Calculate average value per farmer
    $avgValuePerFarmer = $stats->farmers_served > 0 ? $stats->total_value_distributed / $stats->farmers_served : 0;
    $pdf->Cell($boxWidth - 10, 5, 'Avg: KES ' . number_format($avgValuePerFarmer, 2) . ' per farmer', 0, 1);
    
    // Input Types Count box
    $pdf->Rect($rightColX, $secondRowY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY($rightColX + 5, $secondRowY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 6, 'Input Type Diversity', 0, 1);
    $pdf->SetFont('helvetica', 'B', 18);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY($rightColX + 5, $secondRowY + 13);
    $pdf->Cell($boxWidth - 10, 10, $stats->input_types_count . ' Types', 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY($rightColX + 5, $secondRowY + 25);
    
    // Calculate average items per type
    $avgItemsPerType = $stats->input_types_count > 0 ? $stats->total_items_distributed / $stats->input_types_count : 0;
    $pdf->Cell($boxWidth - 10, 5, 'Avg: ' . number_format($avgItemsPerType, 1) . ' items per type', 0, 1);
    
    // Set Y position after summary boxes
    $pdf->SetY($secondRowY + $boxHeight + 10);
    
    // ===== SECTION 2: DISTRIBUTION BY INPUT TYPE =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '2. DISTRIBUTION BY INPUT TYPE', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Input type breakdown table
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    
    // Table header
    $pdf->Cell(30, 8, 'Input Type', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Items Count', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Total Value (KES)', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Applications', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Farmers', 1, 0, 'C', true);
    $pdf->Cell(25, 8, '% of Total', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Avg/Item', 1, 1, 'C', true);
    
    // Reset styling for table rows
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    
    // Input type breakdown rows
    if (count($typeBreakdown) > 0) {
        foreach ($typeBreakdown as $index => $type) {
            // Alternate row fill
            $fillRow = $index % 2 == 0;
            if ($fillRow) {
                $pdf->SetFillColor(245, 245, 245);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            
            // Input Type
            $inputType = ucfirst($type->input_type);
            $pdf->Cell(30, 7, $inputType, 1, 0, 'L', $fillRow);
            
            // Items Count
            $pdf->Cell(25, 7, $type->item_count, 1, 0, 'C', $fillRow);
            
            // Total Value
            $pdf->Cell(30, 7, number_format($type->total_value, 2), 1, 0, 'R', $fillRow);
            
            // Applications Count
            $pdf->Cell(25, 7, $type->application_count, 1, 0, 'C', $fillRow);
            
            // Farmers Count
            $pdf->Cell(25, 7, $type->farmer_count, 1, 0, 'C', $fillRow);
            
            // Percentage of Total
            $percentage = $type->percentage_of_total ?? 0;
            $pdf->Cell(25, 7, number_format($percentage, 1) . '%', 1, 0, 'C', $fillRow);
            
            // Average per item
            $avgPerItem = $type->item_count > 0 ? $type->total_value / $type->item_count : 0;
            $pdf->Cell(25, 7, number_format($avgPerItem, 2), 1, 1, 'R', $fillRow);
        }
    } else {
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(185, 8, 'No input distribution data found in the selected period', 1, 1, 'C', true);
    }
    
    $pdf->Ln(5);
    
    // ===== SECTION 3: TOP REQUESTED INPUT ITEMS =====
    // Check if we need a new page
    if ($pdf->GetY() > 220) {
        $pdf->AddPage();
    }
    
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '3. TOP REQUESTED INPUT ITEMS', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Top items table
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    
    // Table header
    $pdf->Cell(45, 8, 'Input Name', 1, 0, 'C', true);
    $pdf->Cell(20, 8, 'Type', 1, 0, 'C', true);
    $pdf->Cell(20, 8, 'Requests', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Total Qty', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Total Value (KES)', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Avg Unit Price', 1, 0, 'C', true);
    $pdf->Cell(20, 8, 'Unit', 1, 1, 'C', true);
    
    // Reset styling for table rows
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    
    // Top items rows
    if (count($topItems) > 0) {
        foreach ($topItems as $index => $item) {
            // Alternate row fill
            $fillRow = $index % 2 == 0;
            if ($fillRow) {
                $pdf->SetFillColor(245, 245, 245);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            
            // Input Name (truncated if too long)
            $inputName = strlen($item->input_name) > 25 ? 
                        substr($item->input_name, 0, 22) . '...' : $item->input_name;
            $pdf->Cell(45, 7, $inputName, 1, 0, 'L', $fillRow);
            
            // Type
            $pdf->Cell(20, 7, ucfirst($item->input_type), 1, 0, 'C', $fillRow);
            
            // Request Count
            $pdf->Cell(20, 7, $item->request_count, 1, 0, 'C', $fillRow);
            
            // Total Quantity
            $pdf->Cell(25, 7, number_format($item->total_quantity, 1), 1, 0, 'R', $fillRow);
            
            // Total Value
            $pdf->Cell(30, 7, number_format($item->total_value, 2), 1, 0, 'R', $fillRow);
            
            // Average Unit Price
            $pdf->Cell(25, 7, number_format($item->avg_unit_price, 2), 1, 0, 'R', $fillRow);
            
            // Unit
            $pdf->Cell(20, 7, $item->unit, 1, 1, 'C', $fillRow);
        }
    } else {
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(185, 8, 'No input items data found in the selected period', 1, 1, 'C', true);
    }
    
    $pdf->Ln(5);
    
    // ===== DOCUMENT FOOTER =====
    $pdf->SetY(-25);
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->Cell(0, 6, 'This is a computer-generated report and does not require a signature.', 0, 1, 'C');
    $pdf->Cell(0, 6, 'Generated on: ' . date('F d, Y h:i A'), 0, 1, 'C');
    $pdf->Cell(0, 6, 'Report Period: ' . date('F d, Y', strtotime($startDate)) . ' to ' . date('F d, Y', strtotime($endDate)), 0, 1, 'C');
    
    // Output the PDF
    $pdf->Output('Input_Type_Distribution_Report_' . $startDate . '_to_' . $endDate . '.pdf', 'I');
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