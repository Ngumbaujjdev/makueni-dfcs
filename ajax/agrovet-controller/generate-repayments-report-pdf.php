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
    
    // Section 1: Get repayment summary stats for the agrovet within date range
    $statsQuery = "SELECT 
                    COUNT(icr.id) as total_repayments,
                    COALESCE(SUM(icr.amount), 0) as total_repaid_amount,
                    COALESCE(SUM(icr.produce_sale_amount), 0) as total_produce_sales,
                    COUNT(DISTINCT icr.approved_credit_id) as unique_credits_repaid
                  FROM input_credit_repayments icr
                  JOIN approved_input_credits aic ON icr.approved_credit_id = aic.id
                  JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                  WHERE ica.agrovet_id = '{$staff->agrovet_id}'
                  AND icr.deduction_date BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'";
    
    $stats = $app->selectOne($statsQuery);
    
    // Ensure stats has default values if query returns null
    if (!$stats) {
        $stats = (object)[
            'total_repayments' => 0,
            'total_repaid_amount' => 0,
            'total_produce_sales' => 0,
            'unique_credits_repaid' => 0
        ];
    }
    
    // Section 2: Calculate repayment performance
    $performanceQuery = "SELECT
                           aic.id,
                           aic.total_with_interest,
                           aic.remaining_balance,
                           (aic.total_with_interest - aic.remaining_balance) as amount_paid,
                           CASE 
                               WHEN aic.total_with_interest > 0 
                               THEN ((aic.total_with_interest - aic.remaining_balance) / aic.total_with_interest) * 100 
                               ELSE 0 
                           END as percentage_paid
                        FROM approved_input_credits aic
                        JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                        WHERE ica.agrovet_id = '{$staff->agrovet_id}'
                        AND aic.status = 'active'";
    
    $performance = $app->select_all($performanceQuery);
    
    // Fix: Check if performance query returned valid results
    if (!$performance || !is_array($performance)) {
        $performance = []; // Initialize as empty array if query failed
    }
    
    // Calculate average repayment percentage
    $totalPercentage = 0;
    $creditCount = count($performance); // Now this won't fail
    $totalPaid = 0;
    $totalOriginal = 0;
    
    // Only process if we have performance data
    if ($creditCount > 0) {
        foreach ($performance as $credit) {
            $totalPercentage += $credit->percentage_paid;
            $totalPaid += $credit->amount_paid;
            $totalOriginal += $credit->total_with_interest;
        }
    }
    
    // Avoid division by zero
    $avgRepaymentPercentage = $creditCount > 0 ? $totalPercentage / $creditCount : 0;
    $overallRepaymentPercentage = $totalOriginal > 0 ? ($totalPaid / $totalOriginal) * 100 : 0;
    
    // Section 3: Get detailed repayment transactions in the date range
    $repaymentsQuery = "SELECT 
                          icr.id,
                          icr.approved_credit_id,
                          icr.produce_delivery_id,
                          icr.produce_sale_amount,
                          icr.deducted_amount,
                          icr.amount,
                          icr.deduction_date,
                          icr.notes,
                          aic.total_with_interest,
                          aic.remaining_balance,
                          ica.farmer_id,
                          CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                          f.registration_number as farmer_reg,
                          pd.id as delivery_id,
                          pd.quantity as produce_quantity,
                          pd.unit_price as produce_unit_price,
                          pd.total_value as produce_total_value
                        FROM input_credit_repayments icr
                        JOIN approved_input_credits aic ON icr.approved_credit_id = aic.id
                        JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                        JOIN farmers f ON ica.farmer_id = f.id
                        JOIN users u ON f.user_id = u.id
                        JOIN produce_deliveries pd ON icr.produce_delivery_id = pd.id
                        WHERE ica.agrovet_id = '{$staff->agrovet_id}'
                        AND icr.deduction_date BETWEEN '{$startDate} 00:00:00' AND '{$endDate} 23:59:59'
                        ORDER BY icr.deduction_date DESC";
    
    $repayments = $app->select_all($repaymentsQuery);
    
    // Fix: Ensure repayments is an array
    if (!$repayments || !is_array($repayments)) {
        $repayments = [];
    }
    
    // Create new PDF document
    $pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('DFCS System');
    $pdf->SetAuthor('Makueni DFCS');
    $pdf->SetTitle('Input Credit Repayments Report');
    $pdf->SetSubject('Input Credit Repayments for ' . $staff->agrovet_name);
    
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
    $pdf->Cell(0, 10, 'INPUT CREDIT REPAYMENTS REPORT', 0, 1, 'C');
    
    // Agrovet name and date range
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 6, $staff->agrovet_name, 0, 1, 'C');
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
    $pdf->Cell(0, 6, 'Period: ' . date('F d, Y', strtotime($startDate)) . ' to ' . date('F d, Y', strtotime($endDate)), 0, 1, 'C');
    $pdf->Cell(0, 6, 'Generated on: ' . date('F d, Y'), 0, 1, 'C');
    
    $pdf->Ln(5);
    
    // ===== SECTION 1: REPAYMENT SUMMARY =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '1. REPAYMENT SUMMARY', 0, 1, 'L');
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
    // Total Repayments box
    $pdf->Rect(15, $startY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY(20, $startY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 6, 'Total Repayments', 0, 1);
    $pdf->SetFont('helvetica', 'B', 18);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY(20, $startY + 13);
    $pdf->Cell($boxWidth - 10, 10, $stats->total_repayments, 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY(20, $startY + 25);
    $pdf->Cell($boxWidth - 10, 5, 'From ' . $stats->unique_credits_repaid . ' different credits', 0, 1);
    
    // Total Amount Repaid box
    $rightColX = 15 + $boxWidth + $margin;
    $pdf->Rect($rightColX, $startY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY($rightColX + 5, $startY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 6, 'Total Amount Repaid', 0, 1);
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY($rightColX + 5, $startY + 13);
    $pdf->Cell($boxWidth - 10, 10, 'KES ' . number_format($stats->total_repaid_amount, 2), 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY($rightColX + 5, $startY + 25);
    $pdf->Cell($boxWidth - 10, 5, 'From produce sales worth KES ' . number_format($stats->total_produce_sales, 2), 0, 1);
    
    // Set Y position for next row
    $secondRowY = $startY + $boxHeight + 10;
    
    // Second row of boxes
    // Repayment Percentage box
    $pdf->Rect(15, $secondRowY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY(20, $secondRowY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 6, 'Overall Repayment Rate', 0, 1);
    $pdf->SetFont('helvetica', 'B', 18);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY(20, $secondRowY + 13);
    $pdf->Cell($boxWidth - 10, 10, number_format($overallRepaymentPercentage, 1) . '%', 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY(20, $secondRowY + 25);
    $pdf->Cell($boxWidth - 10, 5, 'Average per credit: ' . number_format($avgRepaymentPercentage, 1) . '%', 0, 1);
    
    // Average Repayment Days box - if available
    $pdf->Rect($rightColX, $secondRowY, $boxWidth, $boxHeight, 'D');
    $pdf->SetXY($rightColX + 5, $secondRowY + 5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($boxWidth - 10, 6, 'Sales to Repayment Ratio', 0, 1);
    $pdf->SetFont('helvetica', 'B', 18);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY($rightColX + 5, $secondRowY + 13);
    
    // Calculate deduction percentage - Fix: Avoid division by zero
    $deductionPercentage = $stats->total_produce_sales > 0 ? 
                          ($stats->total_repaid_amount / $stats->total_produce_sales) * 100 : 0;
    
    $pdf->Cell($boxWidth - 10, 10, number_format($deductionPercentage, 1) . '%', 0, 1);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY($rightColX + 5, $secondRowY + 25);
    $pdf->Cell($boxWidth - 10, 5, 'Of total produce sales value', 0, 1);
    
    // Set Y position after summary boxes
    $pdf->SetY($secondRowY + $boxHeight + 10);
    
    // ===== SECTION 2: REPAYMENT TRANSACTION DETAILS =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '2. REPAYMENT TRANSACTION DETAILS', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Repayments table
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    
    // Table header
    $pdf->Cell(25, 8, 'Date', 1, 0, 'C', true);
    $pdf->Cell(40, 8, 'Farmer', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Credit ID', 1, 0, 'C', true);
    $pdf->Cell(35, 8, 'Produce Sale (KES)', 1, 0, 'C', true);
    $pdf->Cell(35, 8, 'Repayment (KES)', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Deduction %', 1, 1, 'C', true);
    
    // Reset styling for table rows
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    
    // Repayment transaction rows
    if (count($repayments) > 0) {
        foreach ($repayments as $index => $repayment) {
            // Alternate row fill
            $fillRow = $index % 2 == 0;
            if ($fillRow) {
                $pdf->SetFillColor(245, 245, 245);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            
            // Date
            $repaymentDate = date('M d, Y', strtotime($repayment->deduction_date));
            $pdf->Cell(25, 7, $repaymentDate, 1, 0, 'C', $fillRow);
            
            // Farmer name (truncated if too long)
            $farmerName = strlen($repayment->farmer_name) > 20 ? 
                         substr($repayment->farmer_name, 0, 17) . '...' : $repayment->farmer_name;
            $pdf->Cell(40, 7, $farmerName . ' (' . $repayment->farmer_reg . ')', 1, 0, 'L', $fillRow);
            
            // Credit ID
            $creditId = 'IC' . str_pad($repayment->approved_credit_id, 5, '0', STR_PAD_LEFT);
            $pdf->Cell(25, 7, $creditId, 1, 0, 'C', $fillRow);
            
            // Produce sale amount
            $pdf->Cell(35, 7, number_format($repayment->produce_sale_amount, 2), 1, 0, 'R', $fillRow);
            
            // Repayment amount
            $pdf->Cell(35, 7, number_format($repayment->amount, 2), 1, 0, 'R', $fillRow);
            
            // Deduction percentage - Fix: Avoid division by zero
            $deductionPct = $repayment->produce_sale_amount > 0 ? 
                          ($repayment->amount / $repayment->produce_sale_amount) * 100 : 0;
            $pdf->Cell(25, 7, number_format($deductionPct, 1) . '%', 1, 1, 'C', $fillRow);
        }
    } else {
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(185, 8, 'No repayment transactions found in the selected period', 1, 1, 'C', true);
    }
    
    $pdf->Ln(5);
    
    // ===== SECTION 3: REPAYMENT PERFORMANCE BY CREDIT =====
    // Check if we need a new page
    if ($pdf->GetY() > 220) {
        $pdf->AddPage();
    }
    
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, '3. REPAYMENT PERFORMANCE BY CREDIT', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Credit performance table
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    
    // Table header
    $pdf->Cell(25, 8, 'Credit ID', 1, 0, 'C', true);
    $pdf->Cell(40, 8, 'Original Amount (KES)', 1, 0, 'C', true);
    $pdf->Cell(40, 8, 'Amount Paid (KES)', 1, 0, 'C', true);
    $pdf->Cell(40, 8, 'Remaining (KES)', 1, 0, 'C', true);
    $pdf->Cell(40, 8, 'Repayment Progress', 1, 1, 'C', true);
    
    // Reset styling for table rows
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    
    // Get the unique credits that had repayments in this period
    $uniqueCreditIds = [];
    
    foreach ($repayments as $repayment) {
        if (!in_array($repayment->approved_credit_id, $uniqueCreditIds)) {
            $uniqueCreditIds[] = $repayment->approved_credit_id;
        }
    }
    
    // Get detailed credit information
    $uniqueCreditsData = [];
    
    if (!empty($uniqueCreditIds)) {
        $idsString = implode(',', $uniqueCreditIds);
        $creditsQuery = "SELECT 
                          aic.id,
                          aic.total_with_interest,
                          aic.remaining_balance,
                          aic.fulfillment_date,
                          aic.status,
                          (aic.total_with_interest - aic.remaining_balance) as amount_paid,
                          CASE 
                              WHEN aic.total_with_interest > 0 
                              THEN ((aic.total_with_interest - aic.remaining_balance) / aic.total_with_interest) * 100 
                              ELSE 0 
                          END as percentage_paid,
                          CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                          f.registration_number as farmer_reg
                        FROM approved_input_credits aic
                        JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                        JOIN farmers f ON ica.farmer_id = f.id
                        JOIN users u ON f.user_id = u.id
                        WHERE aic.id IN ({$idsString})
                        AND ica.agrovet_id = '{$staff->agrovet_id}'";
        
        $uniqueCreditsData = $app->select_all($creditsQuery);
        
        // Fix: Ensure uniqueCreditsData is an array
        if (!$uniqueCreditsData || !is_array($uniqueCreditsData)) {
            $uniqueCreditsData = [];
        }
    }
    
    if (count($uniqueCreditsData) > 0) {
        foreach ($uniqueCreditsData as $index => $credit) {
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
            
            // Original amount
            $pdf->Cell(40, 7, number_format($credit->total_with_interest, 2), 1, 0, 'R', $fillRow);
            
            // Amount paid
            $pdf->Cell(40, 7, number_format($credit->amount_paid, 2), 1, 0, 'R', $fillRow);
            
            // Remaining amount
            $pdf->Cell(40, 7, number_format($credit->remaining_balance, 2), 1, 0, 'R', $fillRow);
            
            // Progress bar and percentage
            $cellX = $pdf->GetX();
            $cellY = $pdf->GetY();
            $cellWidth = 40;
            $cellHeight = 7;
            
            // Draw cell background
            $pdf->Cell($cellWidth, $cellHeight, '', 1, 0, 'C', $fillRow);
            
            // Draw progress bar background
            $barWidth = 30;
            $barHeight = 3;
            $barX = $cellX + ($cellWidth - $barWidth) / 2;
            $barY = $cellY + ($cellHeight - $barHeight) / 2;
            
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Rect($barX, $barY, $barWidth, $barHeight, 'F');
            
            // Draw progress bar fill
            $progress = min(100, max(0, $credit->percentage_paid));
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
        $pdf->Cell(185, 8, 'No credit performance data available for the selected period', 1, 1, 'C', true);
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
    $pdf->Output('Input_Credit_Repayments_Report_' . $startDate . '_to_' . $endDate . '.pdf', 'I');
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