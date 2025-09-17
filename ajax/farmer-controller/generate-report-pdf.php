<?php
include "../../config/config.php";
include "../../libs/App.php";
include "../../vendor/autoload.php";

use TCPDF as PDF;

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['farmerId'])) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request. Farmer ID is required.']);
    exit;
}

try {
    if (ob_get_length()) ob_clean();
    ob_start();
    
   
    $app = new App();
    $farmerId = intval($_POST['farmerId']);
    
    // Fetch farmer information
    $farmerQuery = "SELECT f.*, u.first_name, u.last_name, u.email, u.phone, u.location, u.profile_picture,
                   u.created_at as registration_date, fc.name as category_name
                   FROM farmers f
                   JOIN users u ON f.user_id = u.id
                   LEFT JOIN farmer_categories fc ON f.category_id = fc.id
                   WHERE f.id = :farmer_id";
    
    $params = [':farmer_id' => $farmerId];
    $farmer = $app->selectOne($farmerQuery, $params);
    if (!$farmer) {
        throw new Exception("Farmer not found");
    }
    
    // Fetch farm information
    $farmsQuery = "SELECT f.*, ft.name as farm_type_name,
                  COUNT(DISTINCT fp.id) as product_count,
                  SUM(fp.estimated_production) as total_estimated_production
                  FROM farms f
                  JOIN farm_types ft ON f.farm_type_id = ft.id
                  LEFT JOIN farm_products fp ON f.id = fp.farm_id
                  WHERE f.farmer_id = '{$farmerId}'
                  GROUP BY f.id
                  ORDER BY f.created_at DESC";
    
    $farms = $app->select_all($farmsQuery);
    if ($farms === false) {
        $farms = []; // Initialize as empty array if no farms found
    }
    
    // Get all fruit types for each farm
    $farmFruitsQuery = "SELECT f.id as farm_id, f.name as farm_name, 
                        GROUP_CONCAT(ft.name SEPARATOR ', ') as fruits
                        FROM farms f
                        JOIN farm_fruit_mapping ffm ON f.id = ffm.farm_id
                        JOIN fruit_types ft ON ffm.fruit_type_id = ft.id
                        WHERE f.farmer_id = '{$farmerId}'
                        GROUP BY f.id";
    
    $farmFruits = $app->select_all($farmFruitsQuery);
    if ($farmFruits === false) {
        $farmFruits = []; // Initialize as empty array if no fruit mappings found
    }
    
    // Convert to associative array for easy lookup
    $fruitsByFarm = [];
    foreach ($farmFruits as $item) {
        $fruitsByFarm[$item->farm_id] = $item->fruits;
    }
    
    $deliveriesQuery = "SELECT pd.*, pt.name as product_name, f.name as farm_name
                    FROM produce_deliveries pd
                    JOIN farm_products fp ON pd.farm_product_id = fp.id
                    JOIN farms f ON fp.farm_id = f.id
                    JOIN product_types pt ON fp.product_type_id = pt.id
                    WHERE f.farmer_id = '{$farmerId}'
                    ORDER BY pd.delivery_date DESC";
    
    $deliveries = $app->select_all($deliveriesQuery);
    if ($deliveries === false) {
        $deliveries = []; // Initialize as empty array if no deliveries found
    }
   
    
    // Group deliveries by status for statistics
    $deliveryStats = [
        'total' => count($deliveries),
        'pending' => 0,
        'verified' => 0,
        'rejected' => 0,
        'sold' => 0,
        'paid' => 0,
        'value_total' => 0,
        'value_sold' => 0,
        'value_paid' => 0
    ];
    
    foreach ($deliveries as $delivery) {
        $deliveryStats[$delivery->status]++;
        $deliveryStats['value_total'] += $delivery->total_value;
        
        if ($delivery->status == 'sold' || $delivery->status == 'paid') {
            $deliveryStats['value_sold'] += $delivery->total_value;
        }
        
        if ($delivery->status == 'paid') {
            $deliveryStats['value_paid'] += $delivery->total_value;
        }
    }
    
    // Get all loans (both active and completed)
    $loansQuery = "SELECT la.*, al.*, lt.name as loan_type_name, IFNULL(b.name, 'SACCO') as provider_name,
                  al.status as loan_status, al.approved_amount, al.approved_term,
                  al.interest_rate, al.total_repayment_amount, al.remaining_balance,
                  al.disbursement_date, al.expected_completion_date,
                  u.first_name as approved_by_first_name, u.last_name as approved_by_last_name
                  FROM loan_applications la
                  JOIN approved_loans al ON la.id = al.loan_application_id
                  LEFT JOIN banks b ON la.bank_id = b.id
                  JOIN loan_types lt ON la.loan_type_id = lt.id
                  JOIN users u ON al.approved_by = u.id
                  WHERE la.farmer_id = '{$farmerId}'
                  ORDER BY al.approval_date DESC";
    
    $loans = $app->select_all($loansQuery);
    if ($loans === false) {
        $loans = []; // Initialize as empty array if no loans found
    }
    
    // Calculate loan statistics
    $loanStats = [
        'total_count' => count($loans),
        'active_count' => 0,
        'completed_count' => 0,
        'total_borrowed' => 0,
        'total_remaining' => 0,
        'total_repaid' => 0
    ];
    
    foreach ($loans as $loan) {
        $loanStats['total_borrowed'] += $loan->approved_amount;
        
        if ($loan->loan_status == 'active' || $loan->loan_status == 'pending_disbursement') {
            $loanStats['active_count']++;
            $loanStats['total_remaining'] += $loan->remaining_balance;
        } else if ($loan->loan_status == 'completed') {
            $loanStats['completed_count']++;
            $loanStats['total_repaid'] += $loan->total_repayment_amount;
        }
    }
    
    // Get payment history - MODIFIED to fix the reference issue
    $paymentsQuery = "SELECT fat.*, 
                    CONCAT('DLVR', LPAD(pd.id, 5, '0')) as delivery_reference,
                    pt.name as product_name, pd.delivery_date,
                    pd.total_value as produce_value,
                    u.first_name as processed_by_first_name, u.last_name as processed_by_last_name
                    FROM farmer_account_transactions fat
                    JOIN farmer_accounts fa ON fat.farmer_account_id = fa.id
                    LEFT JOIN produce_deliveries pd ON fat.reference_id = pd.id
                    LEFT JOIN farm_products fp ON pd.farm_product_id = fp.id
                    LEFT JOIN product_types pt ON fp.product_type_id = pt.id
                    LEFT JOIN users u ON fat.processed_by = u.id
                    WHERE fa.farmer_id = '{$farmerId}'
                    ORDER BY fat.created_at DESC
                    LIMIT 20";
    
    $payments = $app->select_all($paymentsQuery);
    if ($payments === false) {
        $payments = []; // Initialize as empty array if no payments found
    }
    // Get financial summary - using parameter binding for selectOne
    $params = [':farmer_id' => $farmerId];
    $financialQuery = "SELECT 
                      (SELECT COALESCE(SUM(pd.total_value), 0)
                       FROM produce_deliveries pd
                       JOIN farm_products fp ON pd.farm_product_id = fp.id
                       JOIN farms fm ON fp.farm_id = fm.id
                       WHERE fm.farmer_id = :farmer_id) as total_earnings,
                      
                      (SELECT COALESCE(SUM(al.remaining_balance), 0)
                       FROM approved_loans al
                       JOIN loan_applications la ON al.loan_application_id = la.id
                       WHERE la.farmer_id = :farmer_id AND al.status = 'active') as outstanding_loans,
                      
                      (SELECT COALESCE(SUM(pd.total_value), 0)
                       FROM produce_deliveries pd
                       JOIN farm_products fp ON pd.farm_product_id = fp.id
                       JOIN farms fm ON fp.farm_id = fm.id
                       WHERE fm.farmer_id = :farmer_id AND pd.status = 'paid') as paid_produce,
                      
                      (SELECT COUNT(pd.id)
                       FROM produce_deliveries pd
                       JOIN farm_products fp ON pd.farm_product_id = fp.id
                       JOIN farms fm ON fp.farm_id = fm.id
                       WHERE fm.farmer_id = :farmer_id) as total_deliveries";
    
    $financials = $app->selectOne($financialQuery, $params);
    if ($financials === false) {
        $financials = (object)['total_earnings' => 0, 'outstanding_loans' => 0, 'paid_produce' => 0, 'total_deliveries' => 0];
    }
    
    // Get account balance
    $balanceQuery = "SELECT balance FROM farmer_accounts WHERE farmer_id = :farmer_id";
    $balance = $app->selectOne($balanceQuery, $params);
    if ($balance === false) {
        $balance = (object)['balance' => 0];
    }
    
    // Get total acres/hectares farmed
    $totalFarmSizeQuery = "SELECT SUM(size) as total_size, size_unit 
                          FROM farms 
                          WHERE farmer_id = '{$farmerId}' 
                          GROUP BY size_unit";
    $farmSizes = $app->select_all($totalFarmSizeQuery);
    if ($farmSizes === false) {
        $farmSizes = [];
    }
    
    $totalFarmSize = 0;
    $sizeUnit = 'acres';
    if (!empty($farmSizes)) {
        $totalFarmSize = $farmSizes[0]->total_size;
        $sizeUnit = $farmSizes[0]->size_unit;
    }
    
    // Create PDF
    $pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document info
    $pdf->SetCreator('DFCS System');
    $pdf->SetAuthor('Makueni DFCS');
    $pdf->SetTitle('Comprehensive Farmer Report');
    
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
    $accentColor = [23, 162, 184]; // Info blue
    $warningColor = [255, 193, 7]; // Warning yellow
    $dangerColor = [220, 53, 69]; // Danger red
    $successColor = [40, 167, 69]; // Success green

    // ===== REPORT HEADER =====
    $pdf->Image('../../assets/images/brand-logos/logo3.png', 15, 10, 30);
    
    $pdf->SetFont('helvetica', 'B', 22);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, 'COMPREHENSIVE FARMER REPORT', 0, 1, 'C');
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
    $pdf->Cell(0, 6, 'Generated on: ' . date('F d, Y, h:i A'), 0, 1, 'C');
    
    // Document purpose
    $pdf->SetFont('helvetica', 'I', 9);
    $pdf->Cell(0, 6, 'This document is an official record of farmer activities and financial status with Makueni DFCS', 0, 1, 'C');
    
    // ===== FARMER PROFILE =====
    $pdf->Ln(10);
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 8, 'FARMER PROFILE', 0, 1, 'L');
    
    // Farmer profile box
    $pdf->SetFillColor(245, 247, 250);
    $pdf->SetDrawColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetLineWidth(0.5);
    
    $infoBoxY = $pdf->GetY() + 2;
    $pdf->Rect(15, $infoBoxY, 180, 50, 'DF');
    
    // Farmer name
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY(20, $infoBoxY + 5);
    $pdf->Cell(170, 8, strtoupper($farmer->first_name . ' ' . $farmer->last_name), 0, 1);
    
    // Status badge
    $pdf->SetXY(20, $infoBoxY + 15);
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor($farmer->is_verified ? 40 : 255, $farmer->is_verified ? 167 : 193, $farmer->is_verified ? 69 : 7);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(30, 6, $farmer->is_verified ? 'VERIFIED' : 'PENDING', 0, 0, 'C', true);
    
    // Registration number
    $pdf->SetXY(60, $infoBoxY + 15);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Cell(30, 6, 'Reg #:', 0, 0);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->Cell(40, 6, $farmer->registration_number, 0, 0);
    
    // Category
    $pdf->SetXY(130, $infoBoxY + 15);
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Cell(30, 6, 'Category:', 0, 0);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->Cell(40, 6, $farmer->category_name ?? 'Uncategorized', 0, 0);
    
    // Contact and location
    $pdf->SetXY(20, $infoBoxY + 23);
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Cell(30, 6, 'Phone:', 0, 0);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->Cell(40, 6, $farmer->phone, 0, 0);
    
    $pdf->SetXY(130, $infoBoxY + 23);
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Cell(30, 6, 'Email:', 0, 0);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->Cell(40, 6, $farmer->email, 0, 0);
    
    $pdf->SetXY(20, $infoBoxY + 30);
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Cell(30, 6, 'Location:', 0, 0);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->Cell(40, 6, $farmer->location, 0, 0);
    
    $pdf->SetXY(130, $infoBoxY + 30);
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Cell(30, 6, 'Registered:', 0, 0);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->Cell(40, 6, date('M d, Y', strtotime($farmer->registration_date)), 0, 0);
    
    // Farming details
    $pdf->SetXY(20, $infoBoxY + 37);
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Cell(30, 6, 'Farms:', 0, 0);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->Cell(40, 6, count($farms) . ' registered farms', 0, 0);
    
    $pdf->SetXY(130, $infoBoxY + 37);
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Cell(30, 6, 'Total Area:', 0, 0);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->Cell(40, 6, number_format($totalFarmSize, 2) . ' ' . $sizeUnit, 0, 0);
    
    // Farmer type
    $pdf->SetXY(20, $infoBoxY + 44);
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Cell(30, 6, 'Farmer Type:', 0, 0);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->Cell(150, 6, $farmer->farmer_type_name ?? 'Fruit Farmer', 0, 0);
    
    // ===== FINANCIAL DASHBOARD =====
    $pdf->Ln(42);
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 8, 'FINANCIAL DASHBOARD', 0, 1, 'L');
    
    // Financial stats
    $pdf->SetFillColor(245, 247, 250);
    $pdf->SetDrawColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    
    // Create financial summary grid (2x3)
    $cellWidth = 87.5;
    $cellHeight = 30;
    $startY = $pdf->GetY() + 2;
    
    // First row
    // Cell 1: Total Earnings
    $pdf->SetFillColor(245, 247, 250);
    $pdf->Rect(15, $startY, $cellWidth, $cellHeight, 'DF');
    $pdf->SetXY(20, $startY + 5);
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell($cellWidth - 10, 6, 'Total Earnings', 0, 1);
    $pdf->SetXY(20, $pdf->GetY());
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($cellWidth - 10, 10, 'KES ' . number_format($financials->total_earnings ?? 0, 2), 0, 1);
    
    // Cell 2: Account Balance
    $pdf->SetFillColor(245, 247, 250);
    $pdf->Rect(15 + $cellWidth + 5, $startY, $cellWidth, $cellHeight, 'DF');
    $pdf->SetXY(20 + $cellWidth + 5, $startY + 5);
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell($cellWidth - 10, 6, 'Account Balance', 0, 1);
    $pdf->SetXY(20 + $cellWidth + 5, $pdf->GetY());
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($cellWidth - 10, 10, 'KES ' . number_format($balance->balance ?? 0, 2), 0, 1);
    
    // Second row
    $startY = $startY + $cellHeight + 5;
    
    // Cell 3: Outstanding Loans
    $pdf->SetFillColor(245, 247, 250);
    $pdf->Rect(15, $startY, $cellWidth, $cellHeight, 'DF');
    $pdf->SetXY(20, $startY + 5);
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell($cellWidth - 10, 6, 'Outstanding Loans', 0, 1);
    $pdf->SetXY(20, $pdf->GetY());
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($cellWidth - 10, 10, 'KES ' . number_format($financials->outstanding_loans ?? 0, 2), 0, 1);
    
    // Cell 4: Activity Summary
    $pdf->SetFillColor(245, 247, 250);
    $pdf->Rect(15 + $cellWidth + 5, $startY, $cellWidth, $cellHeight, 'DF');
    $pdf->SetXY(20 + $cellWidth + 5, $startY + 5);
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell($cellWidth - 10, 6, 'Activity Summary', 0, 1);
    $pdf->SetXY(20 + $cellWidth + 5, $pdf->GetY());
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($cellWidth - 10, 5, 'Deliveries: ' . ($financials->total_deliveries ?? 0), 0, 1);
    $pdf->SetXY(20 + $cellWidth + 5, $pdf->GetY());
    $pdf->Cell($cellWidth - 10, 5, 'Loans: ' . ($loanStats['total_count'] ?? 0), 0, 1);
    $pdf->SetXY(20 + $cellWidth + 5, $pdf->GetY());
    $pdf->Cell($cellWidth - 10, 5, 'Farms: ' . (count($farms) ?? 0), 0, 1);
    
    // ===== FARMS SUMMARY =====
    $pdf->Ln(22);
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 8, 'FARMS & PRODUCTION SUMMARY', 0, 1, 'L');
    
    if (!empty($farms)) {
        // Table header
        $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 9);
        
        $pdf->Cell(45, 8, 'Farm Name', 1, 0, 'L', true);
        $pdf->Cell(25, 8, 'Size', 1, 0, 'L', true);
        $pdf->Cell(30, 8, 'Location', 1, 0, 'L', true);
        $pdf->Cell(65, 8, 'Crops/Fruits', 1, 0, 'L', true);
        $pdf->Cell(15, 8, 'Products', 1, 1, 'C', true);
        
        // Table rows
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 8);
        $rowCount = 0;
        
        foreach ($farms as $farm) {
            // Alternate row colors
            $fill = ($rowCount % 2 == 0) ? false : true;
            if ($fill) {
                $pdf->SetFillColor(245, 247, 250);
            }
            
            $pdf->Cell(45, 7, $farm->name, 1, 0, 'L', $fill);
            $pdf->Cell(25, 7, $farm->size . ' ' . $farm->size_unit, 1, 0, 'L', $fill);
            $pdf->Cell(30, 7, $farm->location, 1, 0, 'L', $fill);
            
            // Get fruits for this farm
            $farmFruitsText = isset($fruitsByFarm[$farm->id]) ? $fruitsByFarm[$farm->id] : '-';
            $pdf->Cell(65, 7, $farmFruitsText, 1, 0, 'L', $fill);
            
            $pdf->Cell(15, 7, $farm->product_count, 1, 1, 'C', $fill);
            
            $rowCount++;
        }
    } else {
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', 'I', 10);
        $pdf->Cell(0, 8, 'No farms registered for this farmer.', 0, 1, 'L');
    }
    
    // ===== LOAN HISTORY =====
    $pdf->AddPage();
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 8, 'LOAN HISTORY & REPAYMENT STATUS', 0, 1, 'L');
    
    // Loan statistics summary
    $pdf->SetFillColor(245, 247, 250);
    $pdf->SetDrawColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Rect(15, $pdf->GetY() + 2, 180, 20, 'DF');
    
    $pdf->Ln(4);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', 'B', 9);
    
    $pdf->Cell(40, 8, 'Total Loans: ' . $loanStats['total_count'], 0, 0, 'L');
    $pdf->Cell(55, 8, 'Total Borrowed: KES ' . number_format($loanStats['total_borrowed'], 2), 0, 0, 'L');
    $pdf->Cell(50, 8, 'Active Loans: ' . $loanStats['active_count'], 0, 0, 'L');
    $pdf->Cell(35, 8, 'Completed: ' . $loanStats['completed_count'], 0, 1, 'L');
    
    $pdf->Cell(95, 8, 'Outstanding Balance: KES ' . number_format($loanStats['total_remaining'], 2), 0, 0, 'L');
    $pdf->Cell(85, 8, 'Total Repaid: KES ' . number_format($loanStats['total_repaid'], 2), 0, 1, 'L');
    
    $pdf->Ln(4);
    
    if (!empty($loans)) {
        // Separate active and completed loans
        $activeLoans = array_filter($loans, function($loan) {
            return $loan->loan_status == 'active' || $loan->loan_status == 'pending_disbursement';
        });
        
        $completedLoans = array_filter($loans, function($loan) {
            return $loan->loan_status == 'completed';
        });
        
        // Active Loans Section
        if (!empty($activeLoans)) {
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->SetTextColor($accentColor[0], $accentColor[1], $accentColor[2]);
            $pdf->Cell(0, 8, 'Active Loans', 0, 1, 'L');
            
            // Table header
            $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('helvetica', 'B', 8);
            
            $pdf->Cell(20, 7, 'Date', 1, 0, 'L', true);
            $pdf->Cell(40, 7, 'Loan Type', 1, 0, 'L', true);
            $pdf->Cell(25, 7, 'Amount', 1, 0, 'R', true);
            $pdf->Cell(15, 7, 'Term', 1, 0, 'C', true);
            $pdf->Cell(15, 7, 'Rate', 1, 0, 'C', true);
            $pdf->Cell(25, 7, 'Balance', 1, 0, 'R', true);
            $pdf->Cell(25, 7, 'Completion', 1, 0, 'C', true);
            $pdf->Cell(15, 7, 'Status', 1, 1, 'C', true);
            
            // Table rows
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('helvetica', '', 8);
            $rowCount = 0;
            
            foreach ($activeLoans as $loan) {
                // Alternate row colors
                $fill = ($rowCount % 2 == 0) ? false : true;
                if ($fill) {
                    $pdf->SetFillColor(245, 247, 250);
                }
                
                $pdf->Cell(20, 7, date('M d, Y', strtotime($loan->disbursement_date)), 1, 0, 'L', $fill);
                $pdf->Cell(40, 7, $loan->loan_type_name, 1, 0, 'L', $fill);
                $pdf->Cell(25, 7, number_format($loan->approved_amount, 2), 1, 0, 'R', $fill);
                $pdf->Cell(15, 7, $loan->approved_term . ' mo', 1, 0, 'C', $fill);
                $pdf->Cell(15, 7, $loan->interest_rate . '%', 1, 0, 'C', $fill);
                $pdf->Cell(25, 7, number_format($loan->remaining_balance, 2), 1, 0, 'R', $fill);
                $pdf->Cell(25, 7, date('M d, Y', strtotime($loan->expected_completion_date)), 1, 0, 'C', $fill);
                
                // Status with appropriate color
                if ($loan->loan_status == 'active') {
                    $pdf->SetTextColor($successColor[0], $successColor[1], $successColor[2]);
                } else {
                    $pdf->SetTextColor($warningColor[0], $warningColor[1], $warningColor[2]);
                }
                
                $pdf->Cell(15, 7, ucfirst($loan->loan_status), 1, 1, 'C', $fill);
                $pdf->SetTextColor(0, 0, 0);
                
                $rowCount++;
        }
        } else {
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('helvetica', 'I', 10);
            $pdf->Cell(0, 8, 'No active loans.', 0, 1, 'L');
        }
        
        $pdf->Ln(5);
        
        // Completed Loans Section
        if (!empty($completedLoans)) {
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->SetTextColor($successColor[0], $successColor[1], $successColor[2]);
            $pdf->Cell(0, 8, 'Completed Loans', 0, 1, 'L');
            
            // Table header
            $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('helvetica', 'B', 8);
            
            $pdf->Cell(25, 7, 'Date', 1, 0, 'L', true);
            $pdf->Cell(45, 7, 'Loan Type', 1, 0, 'L', true);
            $pdf->Cell(25, 7, 'Amount', 1, 0, 'R', true);
            $pdf->Cell(15, 7, 'Term', 1, 0, 'C', true);
            $pdf->Cell(15, 7, 'Rate', 1, 0, 'C', true);
            $pdf->Cell(30, 7, 'Total Paid', 1, 0, 'R', true);
            $pdf->Cell(25, 7, 'Completion', 1, 1, 'C', true);
            
            // Table rows
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('helvetica', '', 8);
            $rowCount = 0;
            
            foreach ($completedLoans as $loan) {
                // Alternate row colors
                $fill = ($rowCount % 2 == 0) ? false : true;
                if ($fill) {
                    $pdf->SetFillColor(245, 247, 250);
                }
                
                $pdf->Cell(25, 7, date('M d, Y', strtotime($loan->disbursement_date)), 1, 0, 'L', $fill);
                $pdf->Cell(45, 7, $loan->loan_type_name, 1, 0, 'L', $fill);
                $pdf->Cell(25, 7, number_format($loan->approved_amount, 2), 1, 0, 'R', $fill);
                $pdf->Cell(15, 7, $loan->approved_term . ' mo', 1, 0, 'C', $fill);
                $pdf->Cell(15, 7, $loan->interest_rate . '%', 1, 0, 'C', $fill);
                $pdf->Cell(30, 7, number_format($loan->total_repayment_amount, 2), 1, 0, 'R', $fill);
                
                // Determine completion date (if available)
                $completionDate = '-';
                if (!empty($loan->expected_completion_date)) {
                    $completionDate = date('M d, Y', strtotime($loan->expected_completion_date));
                }
                
                $pdf->Cell(25, 7, $completionDate, 1, 1, 'C', $fill);
                
                $rowCount++;
            }
        } else {
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('helvetica', 'I', 10);
            $pdf->Cell(0, 8, 'No completed loans.', 0, 1, 'L');
        }
    } else {
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', 'I', 10);
        $pdf->Cell(0, 8, 'No loan history for this farmer.', 0, 1, 'L');
    }
    
    // ===== PRODUCE DELIVERIES =====
    $pdf->AddPage();
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 8, 'PRODUCE DELIVERIES & PAYMENT HISTORY', 0, 1, 'L');
    
    // Produce statistics summary
    $pdf->SetFillColor(245, 247, 250);
    $pdf->SetDrawColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Rect(15, $pdf->GetY() + 2, 180, 20, 'DF');
    
    $pdf->Ln(4);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', 'B', 9);
    
    $pdf->Cell(40, 8, 'Total Deliveries: ' . $deliveryStats['total'], 0, 0, 'L');
    $pdf->Cell(55, 8, 'Total Value: KES ' . number_format($deliveryStats['value_total'], 2), 0, 0, 'L');
    $pdf->Cell(50, 8, 'Sold Value: KES ' . number_format($deliveryStats['value_sold'], 2), 0, 0, 'L');
    $pdf->Cell(35, 8, 'Paid Value: KES ' . number_format($deliveryStats['value_paid'], 2), 0, 1, 'L');
    
    $pdf->Cell(40, 8, 'Pending: ' . $deliveryStats['pending'], 0, 0, 'L');
    $pdf->Cell(45, 8, 'Verified: ' . $deliveryStats['verified'], 0, 0, 'L');
    $pdf->Cell(35, 8, 'Sold: ' . $deliveryStats['sold'], 0, 0, 'L');
    $pdf->Cell(30, 8, 'Paid: ' . $deliveryStats['paid'], 0, 0, 'L');
    $pdf->Cell(30, 8, 'Rejected: ' . $deliveryStats['rejected'], 0, 1, 'L');
    
    $pdf->Ln(4);
    
    if (!empty($deliveries)) {
     
        // Table header
        $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 8);
        
        $pdf->Cell(20, 7, 'Date', 1, 0, 'L', true);
        $pdf->Cell(15, 7, 'Ref #', 1, 0, 'L', true);
        $pdf->Cell(28, 7, 'Product', 1, 0, 'L', true);
        $pdf->Cell(24, 7, 'Farm', 1, 0, 'L', true);
        $pdf->Cell(18, 7, 'Quantity', 1, 0, 'R', true);
        $pdf->Cell(15, 7, 'Grade', 1, 0, 'C', true);
        $pdf->Cell(20, 7, 'Unit Price', 1, 0, 'R', true);
        $pdf->Cell(25, 7, 'Total Value', 1, 0, 'R', true);
        $pdf->Cell(15, 7, 'Status', 1, 1, 'C', true);
        
        // Table rows
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 7);
        
        $rowCount = 0;
        
        // Limit to 25 most recent deliveries for the report
        $recentDeliveries = array_slice($deliveries, 0, 25);
        
        foreach ($recentDeliveries as $delivery) {
            // Alternate row colors
            $fill = ($rowCount % 2 == 0) ? false : true;
            if ($fill) {
                $pdf->SetFillColor(245, 247, 250);
            }
            
            // Create reference from ID instead of using non-existent reference column
            $reference = 'DLVR' . str_pad($delivery->id, 5, '0', STR_PAD_LEFT);
                
            $pdf->Cell(20, 6, date('M d, Y', strtotime($delivery->delivery_date)), 1, 0, 'L', $fill);
            $pdf->Cell(15, 6, $reference, 1, 0, 'L', $fill);
            $pdf->Cell(28, 6, substr($delivery->product_name, 0, 20), 1, 0, 'L', $fill);
            $pdf->Cell(24, 6, substr($delivery->farm_name, 0, 15), 1, 0, 'L', $fill);
            $pdf->Cell(18, 6, number_format($delivery->quantity, 2), 1, 0, 'R', $fill);
            $pdf->Cell(15, 6, 'Grade ' . $delivery->quality_grade, 1, 0, 'C', $fill);
            $pdf->Cell(20, 6, number_format($delivery->unit_price, 2), 1, 0, 'R', $fill);
            $pdf->Cell(25, 6, number_format($delivery->total_value, 2), 1, 0, 'R', $fill);
            
            // Status with color
            if ($delivery->status == 'paid') {
                $pdf->SetTextColor($successColor[0], $successColor[1], $successColor[2]);
            } else if ($delivery->status == 'sold' || $delivery->status == 'verified') {
                $pdf->SetTextColor($accentColor[0], $accentColor[1], $accentColor[2]);
            } else if ($delivery->status == 'rejected') {
                $pdf->SetTextColor($dangerColor[0], $dangerColor[1], $dangerColor[2]);
            } else if ($delivery->status == 'pending') {
                $pdf->SetTextColor($warningColor[0], $warningColor[1], $warningColor[2]);
            }
            
            $pdf->Cell(15, 6, ucfirst($delivery->status), 1, 1, 'C', $fill);
            $pdf->SetTextColor(0, 0, 0);
            
            $rowCount++;
        }
        
        // Total
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(120, 7, 'TOTAL VALUE (All ' . count($deliveries) . ' Deliveries)', 1, 0, 'R', true);
        $pdf->Cell(25, 7, number_format($deliveryStats['value_total'], 2), 1, 0, 'R', true);
        $pdf->Cell(15, 7, '', 1, 1, 'C', true);
    } else {
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', 'I', 10);
        $pdf->Cell(0, 8, 'No deliveries recorded for this farmer.', 0, 1, 'L');
    }
    
    // Recent payments section
    if (!empty($payments)) {
        $pdf->Ln(8);
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->Cell(0, 8, 'Recent Payments', 0, 1, 'L');
        
        // Table header
        $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 8);
        
        $pdf->Cell(25, 7, 'Date', 1, 0, 'L', true);
        $pdf->Cell(20, 7, 'Reference', 1, 0, 'L', true);
        $pdf->Cell(35, 7, 'Transaction Type', 1, 0, 'L', true);
        $pdf->Cell(25, 7, 'Amount', 1, 0, 'R', true);
        $pdf->Cell(75, 7, 'Description', 1, 1, 'L', true);
        
        // Table rows
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 8);
        $rowCount = 0;
        
        foreach ($payments as $payment) {
            // Alternate row colors
            $fill = ($rowCount % 2 == 0) ? false : true;
            if ($fill) {
                $pdf->SetFillColor(245, 247, 250);
            }
            
            $description = '';
            if (!empty($payment->delivery_reference)) {
                // Payment for produce
                $description = "Payment for produce delivery (Ref: " . substr($payment->delivery_reference, -8) . ")";
                if (!empty($payment->product_name)) {
                    $description .= " - " . $payment->product_name;
                }
            } else if (stripos($payment->description, 'loan') !== false) {
                // Loan related
                $description = $payment->description;
            } else {
                // Other transactions
                $description = $payment->description ?? 'Account transaction';
            }
            
            $transactionType = ucfirst($payment->transaction_type);
            
            $pdf->Cell(25, 6, date('M d, Y', strtotime($payment->created_at)), 1, 0, 'L', $fill);
            $pdf->Cell(20, 6, 'TXN' . str_pad($payment->id, 5, '0', STR_PAD_LEFT), 1, 0, 'L', $fill);
            $pdf->Cell(35, 6, $transactionType, 1, 0, 'L', $fill);
            
            // Set color for amount based on transaction type
            if ($payment->transaction_type == 'credit') {
                $pdf->SetTextColor($successColor[0], $successColor[1], $successColor[2]);
            } else {
                $pdf->SetTextColor($dangerColor[0], $dangerColor[1], $dangerColor[2]);
            }
            
            $pdf->Cell(25, 6, number_format($payment->amount, 2), 1, 0, 'R', $fill);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(75, 6, substr($description, 0, 55), 1, 1, 'L', $fill);
            
            $rowCount++;
        }
    }
    
    // ===== FOOTER =====
    $pdf->AddPage();
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 8, 'REPORT SUMMARY & VERIFICATION', 0, 1, 'L');
    
    // Summary statement
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Ln(5);
    $pdf->MultiCell(0, 6, 
        "This report provides a comprehensive overview of $farmer->first_name $farmer->last_name's farming activities, financial status, and transactions with Makueni DFCS. The data contained in this report is accurate as of " . date('F d, Y') . ".", 0, 'L');
    
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, 'KEY METRICS SUMMARY', 0, 1, 'L');
    
    // Create summary metrics table
    $pdf->SetFillColor(245, 247, 250);
    $pdf->SetFont('helvetica', '', 9);
    
    $pdf->Cell(90, 7, 'Registration Number', 1, 0, 'L', true);
    $pdf->Cell(90, 7, $farmer->registration_number, 1, 1, 'L');
    
    $pdf->Cell(90, 7, 'Total Registered Farm Area', 1, 0, 'L', true);
    $pdf->Cell(90, 7, number_format($totalFarmSize, 2) . ' ' . $sizeUnit, 1, 1, 'L');
    
    $pdf->Cell(90, 7, 'Total Produce Value (Lifetime)', 1, 0, 'L', true);
    $pdf->Cell(90, 7, 'KES ' . number_format($deliveryStats['value_total'], 2), 1, 1, 'L');
    
    $pdf->Cell(90, 7, 'Total Payments Received', 1, 0, 'L', true);
    $pdf->Cell(90, 7, 'KES ' . number_format($deliveryStats['value_paid'], 2), 1, 1, 'L');
    
    $pdf->Cell(90, 7, 'Total Loans Borrowed', 1, 0, 'L', true);
    $pdf->Cell(90, 7, 'KES ' . number_format($loanStats['total_borrowed'], 2), 1, 1, 'L');
    
    $pdf->Cell(90, 7, 'Current Outstanding Loans', 1, 0, 'L', true);
    $pdf->Cell(90, 7, 'KES ' . number_format($loanStats['total_remaining'], 2), 1, 1, 'L');
    
    $pdf->Cell(90, 7, 'Current Account Balance', 1, 0, 'L', true);
    $pdf->Cell(90, 7, 'KES ' . number_format($balance->balance ?? 0, 2), 1, 1, 'L');
    
    // Signatures section
    $pdf->Ln(10);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, 'AUTHORIZATION', 0, 1, 'L');
    
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->Ln(2);
    $pdf->Cell(90, 6, 'Report generated by: DFCS System', 0, 0, 'L');
    $pdf->Cell(90, 6, 'Date: ' . date('F d, Y'), 0, 1, 'L');
    
    $pdf->Ln(15);
    $pdf->Cell(90, 0, '', 'T', 0, 'L');
    $pdf->Cell(20, 0, '', 0, 0, 'L');
    $pdf->Cell(70, 0, '', 'T', 1, 'L');
    
    $pdf->Cell(90, 6, 'Authorized Signature', 0, 0, 'C');
    $pdf->Cell(20, 6, '', 0, 0, 'L');
    $pdf->Cell(70, 6, "Farmer's Acknowledgment", 0, 1, 'C');
    
    // Disclaimer
    $pdf->Ln(10);
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->MultiCell(0, 5, 'Disclaimer: This is an official document generated by the Makueni DFCS system. All information contained herein is confidential and is intended solely for the named farmer. The information presented reflects the records in our system as of the date of generation. For any discrepancies, please contact DFCS support.', 0, 'L');
    
    // Verification text
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(0, 5, 'Report ID: RPT-' . date('Ymd') . '-' . str_pad($farmer->id, 4, '0', STR_PAD_LEFT), 0, 1, 'R');
    
    // Output the PDF
    $filename = 'Farmer_Report_' . $farmer->registration_number . '_' . date('Ymd') . '.pdf';
    $pdf->Output($filename, 'I');
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