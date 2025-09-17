<?php
include "../../config/config.php";
include "../../libs/App.php";
include "../../vendor/autoload.php";


use TCPDF as PDF;


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request method. Only POST is allowed.']);
    exit;
}
 $app = new App();
// Check required parameters
$required = ['period', 'report_type'];
foreach ($required as $field) {
    if (!isset($_POST[$field]) || empty($_POST[$field])) {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['error' => "Missing required parameter: $field"]);
        exit;
    }
}

// Get parameters
$period = $_POST['period'];
$reportType = $_POST['report_type'];

// If custom period, check date range
if ($period === 'custom') {
    if (!isset($_POST['start_date']) || !isset($_POST['end_date']) || 
        empty($_POST['start_date']) || empty($_POST['end_date'])) {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['error' => 'Start and end dates are required for custom period']);
        exit;
    }
    
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    
    // Validate date format and range
    if (strtotime($startDate) === false || strtotime($endDate) === false) {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['error' => 'Invalid date format']);
        exit;
    }
    
    if (strtotime($startDate) > strtotime($endDate)) {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['error' => 'Start date cannot be after end date']);
        exit;
    }
}

try {
    if (ob_get_length()) ob_clean();
    ob_start();
    
    $app = new App();
    
    // Calculate date range based on period
    $dateRange = calculateDateRange($period);
    if ($period === 'custom') {
        $dateRange['start_date'] = $startDate;
        $dateRange['end_date'] = $endDate;
    }
    
    // Get report data based on report type and date range
    $reportData = getReportData($app, $reportType, $dateRange);
    
    // Generate PDF report
    $pdf = generatePDF($reportType, $dateRange, $reportData);
    
    // Output PDF
    $filename = 'Financial_Report_' . ucfirst($reportType) . '_' . formatPeriodName($period, $dateRange) . '.pdf';
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

/**
 * Calculate date range based on period
 */
function calculateDateRange($period) {
    $now = new DateTime();
    $startDate = new DateTime();
    $endDate = new DateTime();
    
    switch ($period) {
        case 'current_month':
            $startDate->modify('first day of this month');
            $endDate->modify('last day of this month');
            break;
            
        case 'last_month':
            $startDate->modify('first day of last month');
            $endDate->modify('last day of last month');
            break;
            
        case 'current_quarter':
            $month = $now->format('n');
            $quarterStartMonth = ceil($month/3) * 3 - 2;
            $startDate->setDate($now->format('Y'), $quarterStartMonth, 1);
            $endDate->setDate($now->format('Y'), $quarterStartMonth + 2, 1);
            $endDate->modify('last day of this month');
            break;
            
        case 'last_quarter':
            $month = $now->format('n');
            $quarterStartMonth = ceil($month/3) * 3 - 2;
            $startDate->setDate($now->format('Y'), $quarterStartMonth - 3, 1);
            $endDate->setDate($now->format('Y'), $quarterStartMonth - 1, 1);
            $endDate->modify('last day of this month');
            break;
            
        case 'ytd':
            $startDate->setDate($now->format('Y'), 1, 1);
            break;
            
        case 'last_year':
            $startDate->setDate($now->format('Y') - 1, 1, 1);
            $endDate->setDate($now->format('Y') - 1, 12, 31);
            break;
            
        default:
            // Default to last 30 days
            $startDate->modify('-30 days');
    }
    
    return [
        'start_date' => $startDate->format('Y-m-d'),
        'end_date' => $endDate->format('Y-m-d'),
        'period_name' => $period
    ];
}

/**
 * Format period name for display in the report
 */
function formatPeriodName($period, $dateRange) {
    switch ($period) {
        case 'current_month':
            return date('F Y');
            
        case 'last_month':
            return date('F Y', strtotime('first day of last month'));
            
        case 'current_quarter':
            $month = date('n');
            $quarter = ceil($month/3);
            return "Q$quarter " . date('Y');
            
        case 'last_quarter':
            $month = date('n');
            $quarter = ceil($month/3) - 1;
            $year = date('Y');
            if ($quarter < 1) {
                $quarter = 4;
                $year -= 1;
            }
            return "Q$quarter $year";
            
        case 'ytd':
            return "YTD " . date('Y');
            
        case 'last_year':
            return date('Y', strtotime('-1 year'));
            
        case 'custom':
            return date('M d, Y', strtotime($dateRange['start_date'])) . ' to ' . 
                   date('M d, Y', strtotime($dateRange['end_date']));
            
        default:
            return "Last 30 Days";
    }
}
/**
 * Get report data based on report type and date range
 */
function getReportData($app, $reportType, $dateRange) {
    $startDate = $dateRange['start_date'];
    $endDate = $dateRange['end_date'];
    
    $data = [];
    
    // Common query parameters for date filtering
    $params = [
        ':start_date' => $startDate,
        ':end_date' => $endDate . ' 23:59:59'  // Include the entire end date
    ];
    
    // Get account summary for all report types
   $accountQuery = "SELECT 
                (SELECT balance FROM sacco_accounts WHERE id = 1) as current_balance,
                (SELECT COUNT(*) FROM sacco_account_transactions 
                 WHERE created_at BETWEEN '{$dateRange['start_date']}' AND '{$dateRange['end_date']} 23:59:59') as transaction_count,
                (SELECT COALESCE(SUM(amount), 0) FROM sacco_account_transactions 
                 WHERE transaction_type = 'credit' AND created_at BETWEEN '{$dateRange['start_date']}' AND '{$dateRange['end_date']} 23:59:59') as total_credits,
                (SELECT COALESCE(SUM(amount), 0) FROM sacco_account_transactions 
                 WHERE transaction_type = 'debit' AND created_at BETWEEN '{$dateRange['start_date']}' AND '{$dateRange['end_date']} 23:59:59') as total_debits";
    
    $accountSummary = $app->select_one($accountQuery);
    if ($accountSummary) {
        $data['account_summary'] = $accountSummary;
    } else {
        $data['account_summary'] = (object)[
            'current_balance' => 0,
            'transaction_count' => 0,
            'total_credits' => 0,
            'total_debits' => 0
        ];
    }
    
    // Get loan portfolio data if required
    if ($reportType === 'loans' || $reportType === 'complete') {
        // Total loan statistics
        $loanStatsQuery = "SELECT 
                         COUNT(al.id) as total_loans,
                         COALESCE(SUM(al.approved_amount), 0) as total_disbursed,
                         COALESCE(SUM(al.total_repayment_amount), 0) as total_expected_repayment,
                         COALESCE(SUM(al.remaining_balance), 0) as total_outstanding,
                         COALESCE(SUM(CASE WHEN al.status = 'active' THEN 1 ELSE 0 END), 0) as active_loans,
                         COALESCE(SUM(CASE WHEN al.status = 'completed' THEN 1 ELSE 0 END), 0) as completed_loans,
                         COALESCE(SUM(CASE WHEN al.status = 'defaulted' THEN 1 ELSE 0 END), 0) as defaulted_loans
                         FROM approved_loans al
                         WHERE al.approval_date BETWEEN :start_date AND :end_date";
        
        $loanStats = $app->selectOne($loanStatsQuery, $params);
        if ($loanStats) {
            $data['loan_stats'] = $loanStats;
        } else {
            $data['loan_stats'] = (object)[
                'total_loans' => 0,
                'total_disbursed' => 0,
                'total_expected_repayment' => 0,
                'total_outstanding' => 0,
                'active_loans' => 0,
                'completed_loans' => 0,
                'defaulted_loans' => 0
            ];
        }
        
        // Loan disbursements by loan type
$loansByTypeQuery = "SELECT 
                   lt.name as loan_type,
                   COUNT(al.id) as loan_count,
                   COALESCE(SUM(al.approved_amount), 0) as total_amount,
                   ROUND(AVG(al.interest_rate), 2) as avg_interest_rate
                   FROM approved_loans al
                   JOIN loan_applications la ON al.loan_application_id = la.id
                   JOIN loan_types lt ON la.loan_type_id = lt.id
                   WHERE al.approval_date BETWEEN '{$dateRange['start_date']}' AND '{$dateRange['end_date']} 23:59:59'
                   GROUP BY lt.name
                   ORDER BY total_amount DESC";

$loansByType = $app->select_all($loansByTypeQuery);
$data['loans_by_type'] = $loansByType ?: [];

// Get monthly loan disbursements and repayments
$monthlyLoansQuery = "SELECT 
                    DATE_FORMAT(al.approval_date, '%Y-%m') as month,
                    COUNT(al.id) as loans_count,
                    COALESCE(SUM(al.approved_amount), 0) as disbursed_amount
                    FROM approved_loans al
                    WHERE al.approval_date BETWEEN '{$dateRange['start_date']}' AND '{$dateRange['end_date']} 23:59:59'
                    GROUP BY DATE_FORMAT(al.approval_date, '%Y-%m')
                    ORDER BY month";

$monthlyLoans = $app->select_all($monthlyLoansQuery);
$data['monthly_loans'] = $monthlyLoans ?: [];

// Get monthly loan repayments
$monthlyRepaymentsQuery = "SELECT 
                          DATE_FORMAT(lr.payment_date, '%Y-%m') as month,
                          COUNT(lr.id) as repayment_count,
                          COALESCE(SUM(lr.amount), 0) as repayment_amount
                          FROM loan_repayments lr
                          WHERE lr.payment_date BETWEEN '{$dateRange['start_date']}' AND '{$dateRange['end_date']} 23:59:59'
                          GROUP BY DATE_FORMAT(lr.payment_date, '%Y-%m')
                          ORDER BY month";

$monthlyRepayments = $app->select_all($monthlyRepaymentsQuery);
$data['monthly_repayments'] = $monthlyRepayments ?: [];
    }
    
    // Get commission revenue data if required
    if ($reportType === 'commissions' || $reportType === 'complete') {
        // Total commission statistics
        $commissionStatsQuery = "SELECT 
                               COUNT(sat.id) as total_transactions,
                               COALESCE(SUM(sat.amount), 0) as total_commissions
                               FROM sacco_account_transactions sat
                               WHERE sat.transaction_type = 'credit'
                               AND sat.description LIKE '%Commission%'
                               AND sat.created_at BETWEEN :start_date AND :end_date";
        
        $commissionStats = $app->selectOne($commissionStatsQuery, $params);
        if ($commissionStats) {
            $data['commission_stats'] = $commissionStats;
        } else {
            $data['commission_stats'] = (object)[
                'total_transactions' => 0,
                'total_commissions' => 0
            ];
        }
        
      // Commission by product type
$commissionsByProductQuery = "SELECT 
                            pt.name as product_name,
                            COUNT(sat.id) as transaction_count,
                            COALESCE(SUM(sat.amount), 0) as commission_amount
                            FROM sacco_account_transactions sat
                            JOIN produce_deliveries pd ON sat.reference_id = pd.id
                            JOIN farm_products fp ON pd.farm_product_id = fp.id
                            JOIN product_types pt ON fp.product_type_id = pt.id
                            WHERE sat.transaction_type = 'credit'
                            AND sat.description LIKE '%Commission%'
                            AND sat.created_at BETWEEN '{$dateRange['start_date']}' AND '{$dateRange['end_date']} 23:59:59'
                            GROUP BY pt.name
                            ORDER BY commission_amount DESC";

$commissionsByProduct = $app->select_all($commissionsByProductQuery);
$data['commissions_by_product'] = $commissionsByProduct ?: [];

// Monthly commission data
$monthlyCommissionsQuery = "SELECT 
                           DATE_FORMAT(sat.created_at, '%Y-%m') as month,
                           COUNT(sat.id) as transaction_count,
                           COALESCE(SUM(sat.amount), 0) as commission_amount
                           FROM sacco_account_transactions sat
                           WHERE sat.transaction_type = 'credit'
                           AND sat.description LIKE '%Commission%'
                           AND sat.created_at BETWEEN '{$dateRange['start_date']}' AND '{$dateRange['end_date']} 23:59:59'
                           GROUP BY DATE_FORMAT(sat.created_at, '%Y-%m')
                           ORDER BY month";

$monthlyCommissions = $app->select_all($monthlyCommissionsQuery);
$data['monthly_commissions'] = $monthlyCommissions ?: [];
    }
    
    // Get financial KPIs for summary and complete reports
    if ($reportType === 'summary' || $reportType === 'complete') {
        // Calculate financial KPIs
        $netIncome = $accountSummary->total_credits - $accountSummary->total_debits;
        
        // Get total produce value in period
        $produceValueQuery = "SELECT 
                            COALESCE(SUM(pd.total_value), 0) as total_produce_value
                            FROM produce_deliveries pd
                            WHERE pd.status IN ('verified', 'sold', 'paid')
                            AND pd.delivery_date BETWEEN :start_date AND :end_date";
        
        $produceValue = $app->selectOne($produceValueQuery, $params);
        $totalProduceValue = $produceValue ? $produceValue->total_produce_value : 0;
        
        // Get KPIs
        $data['financial_kpis'] = [
            'net_income' => $netIncome,
            'total_produce_value' => $totalProduceValue,
            'commission_percentage' => $totalProduceValue > 0 ? 
                (($data['commission_stats']->total_commissions ?? 0) / $totalProduceValue) * 100 : 0,
            'loan_to_deposit_ratio' => $accountSummary->current_balance > 0 ? 
                (($data['loan_stats']->total_outstanding ?? 0) / $accountSummary->current_balance) * 100 : 0
        ];
        
        // Transaction summaries by type
      // Transaction summaries by type - Fixed query
// Transaction summaries by type - Fixed query
$transactionTypesQuery = "SELECT 
                        description,
                        COUNT(*) as transaction_count,
                        COALESCE(SUM(CASE WHEN transaction_type = 'credit' THEN amount ELSE 0 END), 0) as credit_amount,
                        COALESCE(SUM(CASE WHEN transaction_type = 'debit' THEN amount ELSE 0 END), 0) as debit_amount
                        FROM sacco_account_transactions
                        WHERE created_at BETWEEN '{$dateRange['start_date']}' AND '{$dateRange['end_date']} 23:59:59'
                        GROUP BY description
                        ORDER BY SUM(amount) DESC
                        LIMIT 10";
        
$transactionTypes = $app->select_all($transactionTypesQuery);
$data['transaction_types'] = $transactionTypes ?: [];
    }
    
    return $data;
}
/**
 * Generate PDF report
 */
function generatePDF($reportType, $dateRange, $reportData) {
    // Create PDF
    $pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document info
    $pdf->SetCreator('DFCS System');
    $pdf->SetAuthor('Makueni DFCS');
    $pdf->SetTitle('SACCO Financial Report');
    
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
    
    // Report header
    $pdf->Image('../../../assets/images/brand-logos/logo3.png', 15, 10, 30);
    
    $pdf->SetFont('helvetica', 'B', 22);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    
    // Set title based on report type
    $title = '';
    switch ($reportType) {
        case 'summary':
            $title = 'SACCO ACCOUNT SUMMARY';
            break;
        case 'loans':
            $title = 'LOAN PORTFOLIO REPORT';
            break;
        case 'commissions':
            $title = 'COMMISSION REVENUE REPORT';
            break;
        case 'complete':
            $title = 'COMPREHENSIVE FINANCIAL REPORT';
            break;
        default:
            $title = 'SACCO FINANCIAL REPORT';
    }
    
    $pdf->Cell(0, 10, $title, 0, 1, 'C');
    
    $period = formatPeriodName($dateRange['period_name'], $dateRange);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
    $pdf->Cell(0, 6, 'Period: ' . $period, 0, 1, 'C');
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 6, 'Generated on: ' . date('F d, Y, h:i A'), 0, 1, 'C');
    
    // Document purpose
    $pdf->SetFont('helvetica', 'I', 9);
    $pdf->Cell(0, 6, 'This is an official financial report generated by the Makueni DFCS system', 0, 1, 'C');
    
    $pdf->Ln(5);
    
    // Account Summary Section (for all report types)
    renderAccountSummary($pdf, $reportData['account_summary'], $primaryColor, $secondaryColor);
    
    // Render specific report sections based on report type
    switch ($reportType) {
        case 'summary':
            renderFinancialKPIs($pdf, $reportData['financial_kpis'] ?? [], $primaryColor, $accentColor);
            renderTransactionSummary($pdf, $reportData['transaction_types'] ?? [], $primaryColor);
            break;
            
        case 'loans':
            if (isset($reportData['loan_stats'])) {
                renderLoanPortfolio($pdf, $reportData['loan_stats'], $reportData['loans_by_type'] ?? [], 
                                 $reportData['monthly_loans'] ?? [], $reportData['monthly_repayments'] ?? [], 
                                 $primaryColor, $successColor, $dangerColor);
            }
            break;
            
        case 'commissions':
            if (isset($reportData['commission_stats'])) {
                renderCommissionRevenue($pdf, $reportData['commission_stats'], 
                                      $reportData['commissions_by_product'] ?? [],
                                      $reportData['monthly_commissions'] ?? [], 
                                      $primaryColor, $successColor);
            }
            break;
            
        case 'complete':
            renderFinancialKPIs($pdf, $reportData['financial_kpis'] ?? [], $primaryColor, $accentColor);
            
            if (isset($reportData['loan_stats'])) {
                renderLoanPortfolio($pdf, $reportData['loan_stats'], $reportData['loans_by_type'] ?? [], 
                                 $reportData['monthly_loans'] ?? [], $reportData['monthly_repayments'] ?? [], 
                                 $primaryColor, $successColor, $dangerColor);
                $pdf->AddPage();
            }
            
            if (isset($reportData['commission_stats'])) {
                renderCommissionRevenue($pdf, $reportData['commission_stats'], 
                                     $reportData['commissions_by_product'] ?? [],
                                     $reportData['monthly_commissions'] ?? [], 
                                     $primaryColor, $successColor);
            }
            
            renderTransactionSummary($pdf, $reportData['transaction_types'] ?? [], $primaryColor);
            break;
    }
    
    // Footer with disclaimer
    $pdf->Ln(10);
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->MultiCell(0, 4, 'Disclaimer: This report is for internal use only. The information contained herein is confidential and proprietary to Makueni DFCS.', 0, 'L');
    
    // Report verification ID
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(0, 5, 'Report ID: FIN-' . date('Ymd') . '-' . $reportType . '-' . substr(md5($period), 0, 6), 0, 1, 'R');
    
    return $pdf;
}

/**
 * Render Account Summary Section
 * Displays the current account balance and transaction summary
 */
function renderAccountSummary($pdf, $accountSummary, $primaryColor, $secondaryColor) {
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 8, 'ACCOUNT SUMMARY', 0, 1, 'L');
    
    // Account summary box
    $pdf->SetFillColor(245, 247, 250);
    $pdf->SetDrawColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetLineWidth(0.5);
    
    $infoBoxY = $pdf->GetY() + 2;
    $pdf->Rect(15, $infoBoxY, 180, 35, 'DF');
    
    // Current Balance
    $pdf->SetXY(20, $infoBoxY + 5);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(80, 8, 'Current Account Balance:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(80, 8, 'KES ' . number_format($accountSummary->current_balance, 2), 0, 1);
    
    // Transaction Count
    $pdf->SetXY(20, $infoBoxY + 15);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(80, 6, 'Total Transactions in Period:', 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(80, 6, number_format($accountSummary->transaction_count), 0, 1);
    
    // Credits and Debits
    $pdf->SetXY(20, $infoBoxY + 22);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(40, 6, 'Total Credits:', 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(40, 6, 'KES ' . number_format($accountSummary->total_credits, 2), 0, 0);
    
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(40, 6, 'Total Debits:', 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(40, 6, 'KES ' . number_format($accountSummary->total_debits, 2), 0, 1);
    
    $pdf->Ln(8);
}

/**
 * Render Financial KPIs Section
 * Shows key performance indicators for SACCO finances
 */
function renderFinancialKPIs($pdf, $kpis, $primaryColor, $accentColor) {
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 8, 'FINANCIAL KEY PERFORMANCE INDICATORS', 0, 1, 'L');
    
    // Create KPI grid (2x2)
    $cellWidth = 85;
    $cellHeight = 30;
    $startY = $pdf->GetY() + 2;
    
    // First row
    // Net Income
    $pdf->SetFillColor(245, 247, 250);
    $pdf->Rect(15, $startY, $cellWidth, $cellHeight, 'DF');
    $pdf->SetXY(20, $startY + 5);
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell($cellWidth - 10, 6, 'Net Income (Period)', 0, 1);
    $pdf->SetXY(20, $pdf->GetY());
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($cellWidth - 10, 10, 'KES ' . number_format($kpis['net_income'] ?? 0, 2), 0, 1);
    
    // Loan-to-Deposit Ratio
    $pdf->SetFillColor(245, 247, 250);
    $pdf->Rect(15 + $cellWidth + 10, $startY, $cellWidth, $cellHeight, 'DF');
    $pdf->SetXY(20 + $cellWidth + 10, $startY + 5);
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->SetTextColor($accentColor[0], $accentColor[1], $accentColor[2]);
    $pdf->Cell($cellWidth - 10, 6, 'Loan-to-Deposit Ratio', 0, 1);
    $pdf->SetXY(20 + $cellWidth + 10, $pdf->GetY());
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($cellWidth - 10, 10, number_format($kpis['loan_to_deposit_ratio'] ?? 0, 2) . '%', 0, 1);
    
    // Second row
    $startY = $startY + $cellHeight + 5;
    
    // Total Produce Value
    $pdf->SetFillColor(245, 247, 250);
    $pdf->Rect(15, $startY, $cellWidth, $cellHeight, 'DF');
    $pdf->SetXY(20, $startY + 5);
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell($cellWidth - 10, 6, 'Total Produce Value', 0, 1);
    $pdf->SetXY(20, $pdf->GetY());
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($cellWidth - 10, 10, 'KES ' . number_format($kpis['total_produce_value'] ?? 0, 2), 0, 1);
    
    // Commission Percentage
    $pdf->SetFillColor(245, 247, 250);
    $pdf->Rect(15 + $cellWidth + 10, $startY, $cellWidth, $cellHeight, 'DF');
    $pdf->SetXY(20 + $cellWidth + 10, $startY + 5);
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->SetTextColor($accentColor[0], $accentColor[1], $accentColor[2]);
    $pdf->Cell($cellWidth - 10, 6, 'Commission Percentage', 0, 1);
    $pdf->SetXY(20 + $cellWidth + 10, $pdf->GetY());
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($cellWidth - 10, 10, number_format($kpis['commission_percentage'] ?? 0, 2) . '%', 0, 1);
    
    $pdf->Ln(10);
}

/**
 * Render Loan Portfolio Section
 * Displays loan statistics, loans by type, and monthly loan data
 */
function renderLoanPortfolio($pdf, $loanStats, $loansByType, $monthlyLoans, $monthlyRepayments, 
                          $primaryColor, $successColor, $dangerColor) {
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 8, 'LOAN PORTFOLIO OVERVIEW', 0, 1, 'L');
    
    // Loan statistics summary
    $pdf->SetFillColor(245, 247, 250);
    $pdf->SetDrawColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Rect(15, $pdf->GetY() + 2, 180, 22, 'DF');
    
    $pdf->Ln(4);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', 'B', 9);
    
    $pdf->Cell(40, 8, 'Total Loans: ' . number_format($loanStats->total_loans), 0, 0, 'L');
    $pdf->Cell(70, 8, 'Total Disbursed: KES ' . number_format($loanStats->total_disbursed, 2), 0, 0, 'L');
    $pdf->Cell(70, 8, 'Expected Repayment: KES ' . number_format($loanStats->total_expected_repayment, 2), 0, 1, 'L');
    
    $pdf->Cell(40, 8, 'Active Loans: ' . number_format($loanStats->active_loans), 0, 0, 'L');
    $pdf->Cell(70, 8, 'Completed Loans: ' . number_format($loanStats->completed_loans), 0, 0, 'L');
    $pdf->Cell(70, 8, 'Outstanding Balance: KES ' . number_format($loanStats->total_outstanding, 2), 0, 1, 'L');
    
    $pdf->Ln(5);
    
    // Loans by Type Table
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 8, 'Loans by Type', 0, 1, 'L');
    
    if (!empty($loansByType)) {
        // Table header
        $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 9);
        
        $pdf->Cell(60, 7, 'Loan Type', 1, 0, 'L', true);
        $pdf->Cell(30, 7, 'Count', 1, 0, 'C', true);
        $pdf->Cell(50, 7, 'Total Amount', 1, 0, 'R', true);
        $pdf->Cell(40, 7, 'Avg. Interest Rate', 1, 1, 'C', true);
        
        // Table rows
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $rowCount = 0;
        $totalCount = 0;
        $totalAmount = 0;
        
        foreach ($loansByType as $loan) {
            // Alternate row colors
            $fill = ($rowCount % 2 == 0) ? false : true;
            if ($fill) {
                $pdf->SetFillColor(245, 247, 250);
            }
            
            $pdf->Cell(60, 7, $loan->loan_type, 1, 0, 'L', $fill);
            $pdf->Cell(30, 7, number_format($loan->loan_count), 1, 0, 'C', $fill);
            $pdf->Cell(50, 7, 'KES ' . number_format($loan->total_amount, 2), 1, 0, 'R', $fill);
            $pdf->Cell(40, 7, $loan->avg_interest_rate . '%', 1, 1, 'C', $fill);
            
            $totalCount += $loan->loan_count;
            $totalAmount += $loan->total_amount;
            $rowCount++;
        }
        
        // Total row
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(60, 7, 'TOTAL', 1, 0, 'L', true);
        $pdf->Cell(30, 7, number_format($totalCount), 1, 0, 'C', true);
        $pdf->Cell(50, 7, 'KES ' . number_format($totalAmount, 2), 1, 0, 'R', true);
        $pdf->Cell(40, 7, '', 1, 1, 'C', true);
    } else {
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', 'I', 10);
        $pdf->Cell(0, 8, 'No loan data available for this period.', 0, 1, 'L');
    }
    
    $pdf->Ln(5);
    
    // Monthly Loan Disbursements and Repayments
    if (!empty($monthlyLoans) || !empty($monthlyRepayments)) {
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->Cell(0, 8, 'Monthly Loan Activity', 0, 1, 'L');
        
        // Table header
        $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 9);
        
        $pdf->Cell(40, 7, 'Month', 1, 0, 'L', true);
        $pdf->Cell(35, 7, 'Loans Disbursed', 1, 0, 'C', true);
        $pdf->Cell(50, 7, 'Disbursement Amount', 1, 0, 'R', true);
        $pdf->Cell(55, 7, 'Repayment Amount', 1, 1, 'R', true);
        
        // Prepare monthly data
        $monthlyData = [];
        
        // Process loan disbursements
        foreach ($monthlyLoans as $loan) {
            $month = $loan->month;
            if (!isset($monthlyData[$month])) {
                $monthlyData[$month] = [
                    'loans_count' => 0,
                    'disbursed_amount' => 0,
                    'repayment_amount' => 0
                ];
            }
            $monthlyData[$month]['loans_count'] = $loan->loans_count;
            $monthlyData[$month]['disbursed_amount'] = $loan->disbursed_amount;
        }
        
        // Process loan repayments
        foreach ($monthlyRepayments as $repayment) {
            $month = $repayment->month;
            if (!isset($monthlyData[$month])) {
                $monthlyData[$month] = [
                    'loans_count' => 0,
                    'disbursed_amount' => 0,
                    'repayment_amount' => 0
                ];
            }
            $monthlyData[$month]['repayment_amount'] = $repayment->repayment_amount;
        }
        
        // Sort by month
        ksort($monthlyData);
        
        // Table rows
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $rowCount = 0;
        $totalLoansCount = 0;
        $totalDisbursed = 0;
        $totalRepayment = 0;
        
        foreach ($monthlyData as $month => $data) {
            // Format month
            $formattedMonth = date('F Y', strtotime($month . '-01'));
            
            // Alternate row colors
            $fill = ($rowCount % 2 == 0) ? false : true;
            if ($fill) {
                $pdf->SetFillColor(245, 247, 250);
            }
            
            $pdf->Cell(40, 7, $formattedMonth, 1, 0, 'L', $fill);
            $pdf->Cell(35, 7, number_format($data['loans_count']), 1, 0, 'C', $fill);
            $pdf->Cell(50, 7, 'KES ' . number_format($data['disbursed_amount'], 2), 1, 0, 'R', $fill);
            $pdf->Cell(55, 7, 'KES ' . number_format($data['repayment_amount'], 2), 1, 1, 'R', $fill);
            
            $totalLoansCount += $data['loans_count'];
            $totalDisbursed += $data['disbursed_amount'];
            $totalRepayment += $data['repayment_amount'];
            $rowCount++;
        }
        
        // Total row
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(40, 7, 'TOTAL', 1, 0, 'L', true);
        $pdf->Cell(35, 7, number_format($totalLoansCount), 1, 0, 'C', true);
        $pdf->Cell(50, 7, 'KES ' . number_format($totalDisbursed, 2), 1, 0, 'R', true);
        $pdf->Cell(55, 7, 'KES ' . number_format($totalRepayment, 2), 1, 1, 'R', true);
    }
    
    $pdf->Ln(5);
}

/**
 * Render Commission Revenue Section
 * Displays commission statistics and breakdown by product
 */
function renderCommissionRevenue($pdf, $commissionStats, $commissionsByProduct, $monthlyCommissions, 
                              $primaryColor, $successColor) {
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 8, 'COMMISSION REVENUE', 0, 1, 'L');
    
    // Commission statistics summary
    $pdf->SetFillColor(245, 247, 250);
    $pdf->SetDrawColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $infoBoxY = $pdf->GetY() + 2;
    $pdf->Rect(15, $infoBoxY, 180, 20, 'DF');
    
    $pdf->SetXY(20, $infoBoxY + 5);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(100, 8, 'Total Commission Revenue:', 0, 0);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(60, 8, 'KES ' . number_format($commissionStats->total_commissions, 2), 0, 1);
    
    $pdf->SetXY(20, $infoBoxY + 12);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(100, 6, 'Total Transactions:', 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(60, 6, number_format($commissionStats->total_transactions), 0, 1);
    
    $pdf->Ln(10);
    
    // Commissions by Product Table
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 8, 'Commissions by Product', 0, 1, 'L');
    
    if (!empty($commissionsByProduct)) {
        // Table header
        $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 9);
        
        $pdf->Cell(80, 7, 'Product Name', 1, 0, 'L', true);
        $pdf->Cell(40, 7, 'Transactions', 1, 0, 'C', true);
        $pdf->Cell(60, 7, 'Commission Amount', 1, 1, 'R', true);
        
        // Table rows
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $rowCount = 0;
        $totalTransactions = 0;
        $totalAmount = 0;
        
        foreach ($commissionsByProduct as $commission) {
            // Alternate row colors
            $fill = ($rowCount % 2 == 0) ? false : true;
            if ($fill) {
                $pdf->SetFillColor(245, 247, 250);
            }
            
            $pdf->Cell(80, 7, $commission->product_name, 1, 0, 'L', $fill);
            $pdf->Cell(40, 7, number_format($commission->transaction_count), 1, 0, 'C', $fill);
            $pdf->Cell(60, 7, 'KES ' . number_format($commission->commission_amount, 2), 1, 1, 'R', $fill);
            
            $totalTransactions += $commission->transaction_count;
            $totalAmount += $commission->commission_amount;
            $rowCount++;
        }
        
        // Total row
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(80, 7, 'TOTAL', 1, 0, 'L', true);
        $pdf->Cell(40, 7, number_format($totalTransactions), 1, 0, 'C', true);
        $pdf->Cell(60, 7, 'KES ' . number_format($totalAmount, 2), 1, 1, 'R', true);
    } else {
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', 'I', 10);
        $pdf->Cell(0, 8, 'No commission data available for this period.', 0, 1, 'L');
    }
    
    $pdf->Ln(5);
    
    // Monthly Commission Trend
    if (!empty($monthlyCommissions)) {
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->Cell(0, 8, 'Monthly Commission Trend', 0, 1, 'L');
        
        // Table header
        $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 9);
        
        $pdf->Cell(60, 7, 'Month', 1, 0, 'L', true);
        $pdf->Cell(50, 7, 'Transactions', 1, 0, 'C', true);
        $pdf->Cell(70, 7, 'Commission Amount', 1, 1, 'R', true);
        
        // Table rows
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $rowCount = 0;
        $totalTransactions = 0;
        $totalAmount = 0;
        
        foreach ($monthlyCommissions as $commission) {
            // Format month
            $formattedMonth = date('F Y', strtotime($commission->month . '-01'));
            
            // Alternate row colors
            $fill = ($rowCount % 2 == 0) ? false : true;
            if ($fill) {
                $pdf->SetFillColor(245, 247, 250);
            }
            
            $pdf->Cell(60, 7, $formattedMonth, 1, 0, 'L', $fill);
            $pdf->Cell(50, 7, number_format($commission->transaction_count), 1, 0, 'C', $fill);
          $pdf->Cell(70, 7, 'KES ' . number_format($commission->commission_amount, 2), 1, 1, 'R', $fill);
            
            $totalTransactions += $commission->transaction_count;
            $totalAmount += $commission->commission_amount;
            $rowCount++;
        }
        
        // Total row
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(60, 7, 'TOTAL', 1, 0, 'L', true);
        $pdf->Cell(50, 7, number_format($totalTransactions), 1, 0, 'C', true);
        $pdf->Cell(70, 7, 'KES ' . number_format($totalAmount, 2), 1, 1, 'R', true);
    }
    
    $pdf->Ln(5);
}

/**
 * Render Transaction Summary Section
 * Displays a summary of transactions by type
 */
function renderTransactionSummary($pdf, $transactionTypes, $primaryColor) {
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 8, 'TRANSACTION SUMMARY', 0, 1, 'L');
    
    if (!empty($transactionTypes)) {
        // Table header
        $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 9);
        
        $pdf->Cell(80, 7, 'Transaction Type', 1, 0, 'L', true);
        $pdf->Cell(25, 7, 'Count', 1, 0, 'C', true);
        $pdf->Cell(50, 7, 'Credits', 1, 0, 'R', true);
        $pdf->Cell(25, 7, 'Debits', 1, 1, 'R', true);
        
        // Table rows
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $rowCount = 0;
        $totalCount = 0;
        $totalCredits = 0;
        $totalDebits = 0;
        
        foreach ($transactionTypes as $transaction) {
            // Alternate row colors
            $fill = ($rowCount % 2 == 0) ? false : true;
            if ($fill) {
                $pdf->SetFillColor(245, 247, 250);
            }
            
            $pdf->Cell(80, 7, $transaction->description, 1, 0, 'L', $fill);
            $pdf->Cell(25, 7, number_format($transaction->transaction_count), 1, 0, 'C', $fill);
            $pdf->Cell(50, 7, 'KES ' . number_format($transaction->credit_amount, 2), 1, 0, 'R', $fill);
            $pdf->Cell(25, 7, 'KES ' . number_format($transaction->debit_amount, 2), 1, 1, 'R', $fill);
            
            $totalCount += $transaction->transaction_count;
            $totalCredits += $transaction->credit_amount;
            $totalDebits += $transaction->debit_amount;
            $rowCount++;
        }
        
        // Total row
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(80, 7, 'TOTAL', 1, 0, 'L', true);
        $pdf->Cell(25, 7, number_format($totalCount), 1, 0, 'C', true);
        $pdf->Cell(50, 7, 'KES ' . number_format($totalCredits, 2), 1, 0, 'R', true);
        $pdf->Cell(25, 7, 'KES ' . number_format($totalDebits, 2), 1, 1, 'R', true);
    } else {
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', 'I', 10);
        $pdf->Cell(0, 8, 'No transaction data available for this period.', 0, 1, 'L');
    }
    
    $pdf->Ln(5);
}