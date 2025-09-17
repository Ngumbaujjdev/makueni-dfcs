<?php
include "../../config/config.php";
include "../../libs/App.php";
include "../../vendor/autoload.php";

use TCPDF as PDF;

// Check if the request method is POST and the loanId is set
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['loanId'])) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request. Loan ID is required.']);
    exit;
}

$loanId = intval($_POST['loanId']);

try {
    // Clean any output buffers
    if (ob_get_length()) ob_clean();
    ob_start();
    
    // Initialize App for database operations
    $app = new App();
    
    // Get loan application details with related information
    $query = "SELECT 
                la.id,
                la.farmer_id,
                la.provider_type,
                la.loan_type_id,
                la.bank_id,
                la.amount_requested,
                la.term_requested,
                la.purpose,
                la.application_date,
                la.creditworthiness_score,
                la.status,
                la.rejection_reason,
                la.review_date,
                la.created_at,
                la.updated_at,
                lt.name as loan_type_name,
                lt.interest_rate,
                lt.processing_fee,
                CASE 
                    WHEN la.status = 'approved' OR la.status = 'disbursed' OR la.status = 'completed' THEN 
                        (SELECT al.disbursement_date FROM approved_loans al WHERE al.loan_application_id = la.id)
                    ELSE NULL
                END as disbursement_date,
                CASE 
                    WHEN la.status = 'approved' OR la.status = 'disbursed' OR la.status = 'completed' THEN 
                        (SELECT al.total_repayment_amount FROM approved_loans al WHERE al.loan_application_id = la.id)
                    ELSE NULL
                END as total_repayment_amount,
                CASE 
                    WHEN la.status = 'approved' OR la.status = 'disbursed' OR la.status = 'completed' THEN 
                        (SELECT al.remaining_balance FROM approved_loans al WHERE al.loan_application_id = la.id)
                    ELSE NULL
                END as remaining_balance,
                CASE 
                    WHEN la.status = 'approved' OR la.status = 'disbursed' OR la.status = 'completed' THEN 
                        (SELECT al.expected_completion_date FROM approved_loans al WHERE al.loan_application_id = la.id)
                    ELSE NULL
                END as expected_completion_date,
                CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                u.phone as farmer_phone,
                u.email as farmer_email,
                f.registration_number as farmer_registration,
                fc.name as farmer_category
              FROM loan_applications la
              JOIN loan_types lt ON la.loan_type_id = lt.id
              JOIN farmers f ON la.farmer_id = f.id
              LEFT JOIN farmer_categories fc ON f.category_id = fc.id
              JOIN users u ON f.user_id = u.id
              WHERE la.id = :loan_id";

    $params = [
        ':loan_id' => $loanId
    ];

    $loan = $app->selectOne($query, $params);
    
    if (!$loan) {
        throw new Exception("Loan application with ID $loanId not found in database.");
    }
    
    // Get creditworthiness breakdown from loan logs
    $creditScoreQuery = "SELECT description 
                        FROM loan_logs 
                        WHERE loan_application_id = :loan_id 
                        AND action_type = 'creditworthiness_check' 
                        ORDER BY created_at DESC 
                        LIMIT 1";
                        
    $creditScoreLog = $app->selectOne($creditScoreQuery, [':loan_id' => $loanId]);

    // Parse credit score components if available
    $creditScores = [
        'repayment_history' => 0,
        'financial_obligations' => 0,
        'produce_history' => 0,
        'amount_ratio' => 0
    ];

    if ($creditScoreLog && $creditScoreLog->description) {
        $description = $creditScoreLog->description;
        
        // Extract scores using regex
        preg_match('/Repayment history score: (\d+)/', $description, $repaymentMatches);
        preg_match('/Financial obligations score: (\d+)/', $description, $obligationsMatches);
        preg_match('/Produce history score: (\d+)/', $description, $produceMatches);
        preg_match('/Amount ratio score: (\d+)/', $description, $amountMatches);
        
        if (!empty($repaymentMatches)) $creditScores['repayment_history'] = intval($repaymentMatches[1]);
        if (!empty($obligationsMatches)) $creditScores['financial_obligations'] = intval($obligationsMatches[1]);
        if (!empty($produceMatches)) $creditScores['produce_history'] = intval($produceMatches[1]);
        if (!empty($amountMatches)) $creditScores['amount_ratio'] = intval($amountMatches[1]);
    }
    
    // Get loan repayment history if the loan is active or completed
    $repaymentHistory = [];
    if ($loan->status == 'disbursed' || $loan->status == 'completed') {
        $repaymentQuery = "SELECT 
                           lr.id,
                           lr.amount,
                           lr.payment_date,
                           pd.id as produce_delivery_id,
                           pt.name as product_name,
                           pd.quantity,
                           pd.total_value
                        FROM loan_repayments lr
                        JOIN approved_loans al ON lr.approved_loan_id = al.id
                        JOIN produce_deliveries pd ON lr.produce_delivery_id = pd.id
                        JOIN farm_products fp ON pd.farm_product_id = fp.id
                        JOIN product_types pt ON fp.product_type_id = pt.id
                        WHERE al.loan_application_id = '{$loanId}'
                        ORDER BY lr.payment_date ASC";
        
     
        $repaymentHistory = $app->select_all($repaymentQuery);
    }
    // Get approved loan details if the loan is approved, disbursed, or completed
    $approvedLoan = null;
    if (in_array($loan->status, ['approved', 'disbursed', 'completed'])) {
        $approvedLoanQuery = "SELECT * FROM approved_loans WHERE loan_application_id = :loan_id LIMIT 1";
        $approvedLoan = $app->selectOne($approvedLoanQuery, [':loan_id' => $loanId]);
    }
    
    // Calculate months elapsed if loan is active
    $monthsElapsed = 0;
    if ($loan->status == 'disbursed' && $loan->disbursement_date) {
        $start = new DateTime($loan->disbursement_date);
        $end = new DateTime();
        $diff = $start->diff($end);
        $monthsElapsed = $diff->y * 12 + $diff->m;
    }
    
    // Calculate repayment progress percentage
    $repaymentProgress = 0;
    if ($loan->status == 'disbursed' && $loan->total_repayment_amount > 0 && $loan->remaining_balance >= 0) {
        $paid = $loan->total_repayment_amount - $loan->remaining_balance;
        $repaymentProgress = ($paid / $loan->total_repayment_amount) * 100;
    }
    
    // Create new PDF document
    $pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('DFCS System');
    $pdf->SetAuthor('Makueni DFCS');
    $pdf->SetTitle('Loan Application Statement');
    $pdf->SetSubject('Loan Application #LOAN' . str_pad($loanId, 5, '0', STR_PAD_LEFT));
    
    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(true);
    
    // Set margins
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetHeaderMargin(0);
    $pdf->SetFooterMargin(10);
    
    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, 25); // Increased footer margin to accommodate QR code
    
    // Set default font
    $pdf->SetFont('helvetica', '', 10);
    
    // Add a page
    $pdf->AddPage();
    
 // Set theme colors based on your website's theme
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
    
    // Document Title & Number
    $pdf->SetFont('helvetica', 'B', 20);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY(15, 15);
    $pdf->Cell(0, 10, 'LOAN APPLICATION STATEMENT', 0, 1, 'C');
    
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 6, 'Reference #: LOAN' . str_pad($loanId, 5, '0', STR_PAD_LEFT), 0, 1, 'C');
    
    // Date
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
    $pdf->Cell(0, 6, 'Date: ' . date('F d, Y', strtotime($loan->application_date)), 0, 1, 'C');
    
    // Status badge
    $pdf->Ln(5);
    
    // Status label with color based on status
    $statusColors = [
        'pending' => $secondaryColor,
        'under_review' => $primaryColor,
        'approved' => $infoColor,
        'rejected' => $dangerColor,
        'disbursed' => $successColor,
        'completed' => $successColor,
        'defaulted' => $dangerColor
    ];
    
    $statusText = ucfirst(str_replace('_', ' ', $loan->status));
    $statusColor = isset($statusColors[$loan->status]) ? $statusColors[$loan->status] : $secondaryColor;
    
    $pdf->SetFillColor($statusColor[0], $statusColor[1], $statusColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('helvetica', 'B', 10);
    
    // Calculate the width of the status badge and center it
    $statusWidth = 50;
    $pageWidth = $pdf->GetPageWidth() - 30; // Page width minus margins
    $statusX = ($pageWidth - $statusWidth) / 2 + 15; // Add left margin
    
    $pdf->SetXY($statusX, $pdf->GetY());
    $pdf->Cell($statusWidth, 6, $statusText, 0, 1, 'C', true);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(5);
    // ===== BORROWER & LOAN INFO SECTION =====
    $pdf->SetDrawColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetLineWidth(0.5);

    // Y position after status badge
    $infoBoxY = $pdf->GetY();

    // Draw rectangles for borrower and loan info
    $boxWidth = 85;
    $boxHeight = 45;
    $pdf->Rect(15, $infoBoxY, $boxWidth, $boxHeight, 'D');
    $pdf->Rect(110, $infoBoxY, $boxWidth, $boxHeight, 'D');

    // Borrower Information
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY(20, $infoBoxY + 2);
    $pdf->Cell($boxWidth - 10, 6, 'Borrower Information', 0, 1);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY(20, $pdf->GetY() + 1);
    $pdf->Cell($boxWidth - 10, 5, 'Name: ' . $loan->farmer_name, 0, 1);
    $pdf->SetX(20);
    $pdf->Cell($boxWidth - 10, 5, 'Phone: ' . $loan->farmer_phone, 0, 1);
    $pdf->SetX(20);
    $pdf->Cell($boxWidth - 10, 5, 'Email: ' . $loan->farmer_email, 0, 1);
    $pdf->SetX(20);
    $pdf->Cell($boxWidth - 10, 5, 'Reg #: ' . $loan->farmer_registration, 0, 1);

    // Loan Information - Position correctly in the right box
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY(115, $infoBoxY + 2);
    $pdf->Cell($boxWidth - 10, 6, 'Loan Information', 0, 1);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY(115, $pdf->GetY() + 1);
    $pdf->Cell($boxWidth - 10, 5, 'Type: ' . $loan->loan_type_name, 0, 1);
    $pdf->SetX(115);
    $pdf->Cell($boxWidth - 10, 5, 'Provider: ' . ucfirst($loan->provider_type), 0, 1);
    $pdf->SetX(115);
    $pdf->Cell($boxWidth - 10, 5, 'Application Date: ' . date('M d, Y', strtotime($loan->application_date)), 0, 1);
    $pdf->SetX(115);
    $pdf->Cell($boxWidth - 10, 5, 'Status: ' . ucfirst(str_replace('_', ' ', $loan->status)), 0, 1);

    // Set Y position after the info boxes
    $pdf->SetY($infoBoxY + $boxHeight + 5);
    
    // ===== LOAN DETAILS SECTION =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, 'LOAN DETAILS', 0, 1, 'C');
    $pdf->Ln(2);
    
    // Create table for loan details
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('helvetica', 'B', 10);
    
    // Table Header
    $pdf->Cell(100, 8, 'Loan Type', 1, 0, 'C', true);
    $pdf->Cell(40, 8, 'Term (Months)', 1, 0, 'C', true);
    $pdf->Cell(40, 8, 'Interest Rate', 1, 1, 'C', true);
    
    // Table Row
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetFillColor(245, 245, 245);
    
    $pdf->Cell(100, 8, $loan->loan_type_name, 1, 0, 'L', true);
    $pdf->Cell(40, 8, $loan->term_requested, 1, 0, 'C', true);
    $pdf->Cell(40, 8, $loan->interest_rate . '%', 1, 1, 'C', true);
    
    // Add some space before amounts
    $pdf->Ln(5);
    
    // Amounts section
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 8, 'LOAN AMOUNTS', 0, 1, 'L');
    $pdf->Ln(2);
    
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColor(245, 245, 245);
    $pdf->SetTextColor(0, 0, 0);
    
    // Calculate fees and total amounts
    $processingFee = $loan->amount_requested * ($loan->processing_fee / 100);
    $interestAmount = 0;
    
    if (in_array($loan->status, ['approved', 'disbursed', 'completed']) && $loan->total_repayment_amount) {
        $interestAmount = $loan->total_repayment_amount - $loan->amount_requested - $processingFee;
    } else {
        // Estimate interest if loan is not yet approved
        $interestAmount = $loan->amount_requested * ($loan->interest_rate / 100) * ($loan->term_requested / 12);
    }
    
    $totalRepayable = $loan->amount_requested + $processingFee + $interestAmount;
    $monthlyPayment = $totalRepayable / $loan->term_requested;
    
    // Amount requested
    $pdf->Cell(120, 8, 'Amount Requested:', 1, 0, 'L', true);
    $pdf->Cell(60, 8, 'KES ' . number_format($loan->amount_requested, 2), 1, 1, 'R', true);
    
    // Processing fee
    $pdf->Cell(120, 8, 'Processing Fee (' . $loan->processing_fee . '%):', 1, 0, 'L', true);
    $pdf->Cell(60, 8, 'KES ' . number_format($processingFee, 2), 1, 1, 'R', true);
    
    // Interest
    $pdf->Cell(120, 8, 'Interest (' . $loan->interest_rate . '% p.a. for ' . $loan->term_requested . ' months):', 1, 0, 'L', true);
    $pdf->Cell(60, 8, 'KES ' . number_format($interestAmount, 2), 1, 1, 'R', true);
    
    // Total repayable
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(120, 8, 'TOTAL REPAYABLE AMOUNT:', 1, 0, 'L', true);
    $pdf->Cell(60, 8, 'KES ' . number_format($totalRepayable, 2), 1, 1, 'R', true);
    
    // Monthly installment
    $pdf->Cell(120, 8, 'MONTHLY INSTALLMENT:', 1, 0, 'L', true);
    $pdf->Cell(60, 8, 'KES ' . number_format($monthlyPayment, 2), 1, 1, 'R', true);
    
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(5);
    // ===== CREDITWORTHINESS SECTION =====
$pdf->Ln(8);
$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
$pdf->Cell(0, 10, 'CREDITWORTHINESS ASSESSMENT', 0, 1, 'L');
$pdf->Ln(2);

// Overall score display
$scoreClass = $dangerColor;
$scoreText = 'Poor';

if ($loan->creditworthiness_score >= 85) {
    $scoreClass = $successColor;
    $scoreText = 'Excellent';
} elseif ($loan->creditworthiness_score >= 70) {
    $scoreClass = $successColor;
    $scoreText = 'Good';
} elseif ($loan->creditworthiness_score >= 50) {
    $scoreClass = $warningColor;
    $scoreText = 'Fair';
}

// Draw circle with score
$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetFillColor($scoreClass[0], $scoreClass[1], $scoreClass[2]);
$pdf->SetTextColor(255, 255, 255);

// Position for the score circle
$circleX = 40;
$circleY = $pdf->GetY() + 15;
$circleRadius = 15;

// Draw filled circle for score
$pdf->Circle($circleX, $circleY, $circleRadius, 0, 360, 'F');

// Add text to circle
$pdf->SetXY($circleX - $circleRadius, $circleY - 5);
$pdf->Cell($circleRadius * 2, 10, $loan->creditworthiness_score, 0, 1, 'C');

// Add score description
$pdf->SetFont('helvetica', '', 10);
$pdf->SetTextColor($scoreClass[0], $scoreClass[1], $scoreClass[2]);
$pdf->SetXY($circleX - $circleRadius, $circleY + 8);
$pdf->Cell($circleRadius * 2, 10, $scoreText, 0, 1, 'C');

// Score breakdown table - position next to circle
$pdf->SetXY(70, $circleY - 15);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
$pdf->Cell(100, 8, 'Creditworthiness Score Components', 0, 1);

// Reset X position but keep Y position
$pdf->SetXY(70, $pdf->GetY());
$pdf->SetFont('helvetica', '', 9);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(245, 245, 245);

// Table headers for score breakdown
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(80, 6, 'Component', 1, 0, 'L', true);
$pdf->Cell(30, 6, 'Score', 1, 0, 'C', true);
$pdf->Cell(20, 6, 'Weight', 1, 1, 'C', true);

// Reset to normal font
$pdf->SetFont('helvetica', '', 9);

// Each component row
$pdf->SetXY(70, $pdf->GetY());
$pdf->Cell(80, 6, 'Repayment History', 1, 0, 'L');
$pdf->Cell(30, 6, $creditScores['repayment_history'] . '/100', 1, 0, 'C');
$pdf->Cell(20, 6, '30%', 1, 1, 'C');

$pdf->SetXY(70, $pdf->GetY());
$pdf->Cell(80, 6, 'Financial Obligations', 1, 0, 'L');
$pdf->Cell(30, 6, $creditScores['financial_obligations'] . '/100', 1,
0, 'C');
$pdf->Cell(20, 6, '25%', 1, 1, 'C');

$pdf->SetXY(70, $pdf->GetY());
$pdf->Cell(80, 6, 'Produce History', 1, 0, 'L');
$pdf->Cell(30, 6, $creditScores['produce_history'] . '/100', 1, 0, 'C');
$pdf->Cell(20, 6, '35%', 1, 1, 'C');

$pdf->SetXY(70, $pdf->GetY());
$pdf->Cell(80, 6, 'Amount Ratio', 1, 0, 'L');
$pdf->Cell(30, 6, $creditScores['amount_ratio'] . '/100', 1, 0, 'C');
$pdf->Cell(20, 6, '10%', 1, 1, 'C');

// Set Y position after creditworthiness section
$pdf->SetY($circleY + 25);

// Add explanation of score
$pdf->Ln(5);
$pdf->SetFont('helvetica', '', 9);
$pdf->SetTextColor(80, 80, 80);
$pdf->MultiCell(0, 5, 'The creditworthiness score is determined by four key factors: Repayment History (30%), Financial Obligations (25%), Produce History (35%), and Amount Ratio (10%). A score of 70 or higher is considered good for loan approval.', 0, 'L');
// ===== REPAYMENT HISTORY SECTION =====
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
$pdf->Cell(0, 10, 'REPAYMENT HISTORY', 0, 1, 'L');
$pdf->Ln(2);

// Only show repayment history if the loan is disbursed or completed
if ($loan->status == 'disbursed' || $loan->status == 'completed') {
    if (count($repaymentHistory) > 0) {
        // Set up the table header
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        
        $pdf->Cell(30, 8, 'Date', 1, 0, 'C', true);
        $pdf->Cell(45, 8, 'Product', 1, 0, 'C', true);
        $pdf->Cell(35, 8, 'Sale Amount', 1, 0, 'C', true);
        $pdf->Cell(40, 8, 'Payment Amount', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'Balance', 1, 1, 'C', true);
        
        // Reset text color for table content
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        
        // Track the running balance
        $runningBalance = $loan->total_repayment_amount;
        
        // Loop through each repayment record
        foreach ($repaymentHistory as $idx => $payment) {
            // Alternate row colors for better readability
            $fillColor = ($idx % 2 == 0) ? false : true;
            if ($fillColor) {
                $pdf->SetFillColor(245, 245, 245);
            }
            
            // Reduce balance by payment amount
            $runningBalance -= $payment->amount;
            
            // Format date
            $paymentDate = date('M d, Y', strtotime($payment->payment_date));
            
            $pdf->Cell(30, 7, $paymentDate, 1, 0, 'C', $fillColor);
            $pdf->Cell(45, 7, $payment->product_name, 1, 0, 'L', $fillColor);
            $pdf->Cell(35, 7, 'KES ' . number_format($payment->total_value, 2), 1, 0, 'R', $fillColor);
            $pdf->Cell(40, 7, 'KES ' . number_format($payment->amount, 2), 1, 0, 'R', $fillColor);
            $pdf->Cell(30, 7, 'KES ' . number_format($runningBalance, 2), 1, 1, 'R', $fillColor);
        }
        
        // Add totals row
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        
        $totalPaid = $loan->total_repayment_amount - $loan->remaining_balance;
        
        $pdf->Cell(110, 8, 'TOTAL REPAID', 1, 0, 'R', true);
        $pdf->Cell(40, 8, 'KES ' . number_format($totalPaid, 2), 1, 0, 'R', true);
        $pdf->Cell(30, 8, '', 1, 1, 'C', true);
        
        // Add summary after the table
        $pdf->Ln(5);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        
        // Progress bar for repayment
        $pdf->Cell(40, 8, 'Repayment Progress:', 0, 0);
        
        // Draw progress bar
        $barWidth = 120;
        $barHeight = 6;
        $barX = $pdf->GetX();
        $barY = $pdf->GetY() + 4;
        
        // Draw background bar
        $pdf->SetFillColor(230, 230, 230);
        $pdf->Rect($barX, $barY, $barWidth, $barHeight, 'F');
        
        // Draw progress bar
        $progress = min(100, max(0, $repaymentProgress));
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
        $pdf->SetXY($barX + $barWidth + 5, $barY - 4);
        $pdf->Cell(20, 8, round($progress) . '%', 0, 1);
        
        // Add months elapsed
        $pdf->Cell(40, 8, 'Time Elapsed:', 0, 0);
        $pdf->Cell(140, 8, $monthsElapsed . ' of ' . $loan->term_requested . ' months (' . 
                  round(($monthsElapsed / $loan->term_requested) * 100) . '%)', 0, 1);
        
    } else {
        // No repayment records yet
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(0, 10, 'No repayment records found for this loan.', 1, 1, 'C', true);
        $pdf->Ln(5);
        
        if ($loan->disbursement_date) {
            $pdf->MultiCell(0, 6, 'Loan was disbursed on ' . date('F d, Y', strtotime($loan->disbursement_date)) . 
                          '. Repayments will be automatically deducted from produce sales.', 0, 'L');
        }
    }
} else {
    // Loan not yet disbursed
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', 'I', 10);
    $pdf->SetFillColor(245, 245, 245);
    
    if ($loan->status == 'approved') {
        $message = 'Loan has been approved but not yet disbursed. Repayment history will be available after disbursement.';
    } else {
        $message = 'Loan has not yet been disbursed. Repayment history will be available after approval and disbursement.';
    }
    
    $pdf->Cell(0, 10, $message, 1, 1, 'C', true);
}
// ===== PAYMENT SCHEDULE SECTION (for approved but not completed loans) =====
if (($loan->status == 'approved' || $loan->status == 'disbursed') && $approvedLoan) {
    $pdf->Ln(8);
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, 'PAYMENT SCHEDULE', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Table header for payment schedule
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    
    $pdf->Cell(40, 8, 'Month', 1, 0, 'C', true);
    $pdf->Cell(40, 8, 'Due Date', 1, 0, 'C', true);
    $pdf->Cell(60, 8, 'Expected Payment', 1, 0, 'C', true);
    $pdf->Cell(40, 8, 'Status', 1, 1, 'C', true);
    
    // Reset text color for table content
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 9);
    
    // Calculate monthly payment
    $monthlyPayment = $loan->total_repayment_amount / $loan->term_requested;
    
    // Get disbursement date or use approved date if not yet disbursed
    $startDate = $loan->disbursement_date ? new DateTime($loan->disbursement_date) : new DateTime($loan->review_date);
    $currentDate = new DateTime();
    
    // Create rows for each month
    for ($i = 1; $i <= $loan->term_requested; $i++) {
        // Calculate due date (same day each month)
        $dueDate = clone $startDate;
        $dueDate->modify('+' . ($i - 1) . ' months');
        $dueDateFormatted = $dueDate->format('M d, Y');
        
        // Determine payment status
        $status = 'Upcoming';
        $fillColor = false;
        
        if ($dueDate < $currentDate) {
            if ($i <= $monthsElapsed) {
                $status = 'Paid';
                $pdf->SetFillColor(230, 255, 230); // Light green
                $fillColor = true;
            } else {
                $status = 'Overdue';
                $pdf->SetFillColor(255, 230, 230); // Light red
                $fillColor = true;
            }
        } elseif ($i == $monthsElapsed + 1) {
            $status = 'Current';
            $pdf->SetFillColor(230, 242, 255); // Light blue
            $fillColor = true;
        }
        
        $pdf->Cell(40, 7, 'Month ' . $i, 1, 0, 'C', $fillColor);
        $pdf->Cell(40, 7, $dueDateFormatted, 1, 0, 'C', $fillColor);
        $pdf->Cell(60, 7, 'KES ' . number_format($monthlyPayment, 2), 1, 0, 'R', $fillColor);
        $pdf->Cell(40, 7, $status, 1, 1, 'C', $fillColor);
        
        // Reset fill color for upcoming payments
        $pdf->SetFillColor(245, 245, 245);
    }
}

// ===== TERMS AND CONDITIONS =====
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
$pdf->Cell(0, 8, 'TERMS AND CONDITIONS', 0, 1, 'L');
$pdf->Ln(2);

$pdf->SetFont('helvetica', '', 9);
$pdf->SetTextColor(0, 0, 0);

// Terms text
$termsText = "1. This loan is subject to a " . $loan->interest_rate . "% per annum interest rate.\n";
$termsText .= "2. A processing fee of " . $loan->processing_fee . "% is applied to the loan amount.\n";
$termsText .= "3. Repayments will be automatically deducted from produce sales.\n";
$termsText .= "4. Early repayment is allowed without any penalty.\n";
$termsText .= "5. Default on repayment may affect future loan eligibility.\n";
$termsText .= "6. The SACCO reserves the right to adjust terms as per the loan agreement.";

$pdf->MultiCell(0, 5, $termsText, 0, 'L');

// ===== IMPORTANT INFORMATION =====
$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetFillColor(245, 245, 245);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(0, 8, 'IMPORTANT INFORMATION', 1, 1, 'L', true);

$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(0, 5, "This statement provides a summary of your loan application and its current status. For inquiries or any discrepancies, please contact our office at support@dfcs.com or call +254 700 000 000 within 7 days of receiving this statement.", 1, 'L');

// ===== QR CODE AND DOCUMENT AUTHENTICATION =====
$pdf->Ln(10);

// Add QR code with document reference
$pdf->write2DBarcode(
    'Reference #LOAN' . str_pad($loanId, 5, '0', STR_PAD_LEFT) . 
    "\nFarmer: " . $loan->farmer_name . 
    "\nAmount: KES " . number_format($loan->amount_requested, 2) .
    "\nStatus: " . ucfirst(str_replace('_', ' ', $loan->status)),
    'QRCODE,L', 
    15, 
    $pdf->GetY(), 
    30, 
    30, 
    [
        'border' => false,
        'vpadding' => 'auto',
        'hpadding' => 'auto',
        'fgcolor' => [0, 0, 0],
        'bgcolor' => false,
        'module_width' => 1,
        'module_height' => 1
    ]
);

// Authentication text beside QR code
$pdf->SetXY(50, $pdf->GetY() + 5);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(140, 6, 'Document Authentication', 0, 1);

$pdf->SetXY(50, $pdf->GetY());
$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(140, 5, "This document can be verified using the QR code or by entering the reference number on our portal. This is an official document issued by Makueni Digital Farming Credit System (DFCS).", 0, 'L');

// ===== DOCUMENT FOOTER =====
$pdf->Ln(15);
$pdf->SetFont('helvetica', 'I', 8);
$pdf->Cell(0, 6, 'This is a computer-generated document and does not require a signature.', 0, 1, 'C');
$pdf->Cell(0, 6, 'Generated on: ' . date('F d, Y h:i A'), 0, 1, 'C');

// Output the PDF
$pdf->Output('Loan_Statement_LOAN' . str_pad($loanId, 5, '0', STR_PAD_LEFT) . '.pdf', 'I');
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