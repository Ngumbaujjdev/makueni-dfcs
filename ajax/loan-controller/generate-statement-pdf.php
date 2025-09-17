<?php
// Include necessary files
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
    
    // Get loan details with related information
    $query = "SELECT 
                la.id,
                la.farmer_id,
                la.provider_type,
                la.loan_type_id,
                la.amount_requested,
                la.term_requested,
                la.purpose,
                la.application_date,
                la.status,
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
                CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                u.phone as farmer_phone,
                u.email as farmer_email,
                f.registration_number as farmer_registration
              FROM loan_applications la
              JOIN loan_types lt ON la.loan_type_id = lt.id
              JOIN farmers f ON la.farmer_id = f.id
              JOIN users u ON f.user_id = u.id
              WHERE la.id = :loan_id";

    $params = [':loan_id' => $loanId];
    $loan = $app->selectOne($query, $params);
    
    if (!$loan) {
        throw new Exception("Loan with ID $loanId not found.");
    }
    
    // Get loan repayment history if the loan is active or completed
    $repayments = [];
    if ($loan->status == 'disbursed' || $loan->status == 'completed') {
        $repaymentQuery = "SELECT 
                            lr.id,
                            lr.amount,
                            lr.payment_date,
                            lr.payment_method,
                            lr.notes,
                            pd.id as delivery_id,
                            pd.total_value,
                            pt.name as product_name
                          FROM loan_repayments lr
                          LEFT JOIN approved_loans al ON lr.approved_loan_id = al.id
                          LEFT JOIN produce_deliveries pd ON lr.produce_delivery_id = pd.id
                          LEFT JOIN farm_products fp ON pd.farm_product_id = fp.id
                          LEFT JOIN product_types pt ON fp.product_type_id = pt.id
                          WHERE al.loan_application_id = '{$loanId}'
                          ORDER BY lr.payment_date ASC";
        
        $repayments = $app->select_all($repaymentQuery);
    }
    
    // Create new PDF document
    $pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('DFCS System');
    $pdf->SetAuthor('Makueni DFCS');
    $pdf->SetTitle('Loan Statement');
    $pdf->SetSubject('Loan Statement #LOAN' . str_pad($loanId, 5, '0', STR_PAD_LEFT));
    
    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(true);
    
    // Set margins
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetHeaderMargin(0);
    $pdf->SetFooterMargin(10);
    
    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, 15);
    
    // Set default font
    $pdf->SetFont('helvetica', '', 10);
    
    // Add a page
    $pdf->AddPage();
    
    // Set theme colors
    $primaryColor = [106, 163, 45]; // Green #6AA32D
    $secondaryColor = [74, 34, 15]; // Brown #4A220F
    $successColor = [40, 167, 69]; // Success green
    $warningColor = [255, 193, 7]; // Warning yellow
    $dangerColor = [220, 53, 69]; // Danger red
    
    // ===== DOCUMENT HEADER =====
    // Logo
    $logoPath = '../../assets/images/brand-logos/logo3.png'; // Adjust path as needed
    $pdf->Image($logoPath, 15, 10, 30, 0, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    
    // Document Title & Number
    $pdf->SetFont('helvetica', 'B', 20);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY(15, 15);
    $pdf->Cell(0, 10, 'LOAN STATEMENT', 0, 1, 'C');
    
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 6, 'Reference #: LOAN' . str_pad($loanId, 5, '0', STR_PAD_LEFT), 0, 1, 'C');
    
    // Date
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
    $pdf->Cell(0, 6, 'Date: ' . date('F d, Y'), 0, 1, 'C');
    
    // Status badge
    $pdf->Ln(5);
    
    // Status label with color based on status
    $statusColors = [
        'pending' => $secondaryColor,
        'under_review' => $primaryColor,
        'approved' => [70, 130, 180], // Steel blue
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
    
    // ===== SECTION 1: BORROWER & LOAN INFO =====
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
    if ($loan->disbursement_date) {
        $pdf->Cell($boxWidth - 10, 5, 'Disbursement: ' . date('M d, Y', strtotime($loan->disbursement_date)), 0, 1);
    } else {
        $pdf->Cell($boxWidth - 10, 5, 'Status: ' . ucfirst(str_replace('_', ' ', $loan->status)), 0, 1);
    }
    
    // Set Y position after the info boxes
    $pdf->SetY($infoBoxY + $boxHeight + 10);
    
    // ===== SECTION 2: LOAN AMOUNT DETAILS =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, 'LOAN DETAILS', 0, 1, 'L');
    
    // Create table for loan details
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('helvetica', 'B', 10);
    
    // Calculate fees and total amounts
    $processingFee = ($loan->amount_requested * $loan->processing_fee) / 100;
    
    if ($loan->total_repayment_amount) {
        $totalRepayable = $loan->total_repayment_amount;
        $interestAmount = $totalRepayable - $loan->amount_requested - $processingFee;
    } else {
        // Estimate if not yet approved
        $interestAmount = ($loan->amount_requested * $loan->interest_rate / 100) * ($loan->term_requested / 12);
        $totalRepayable = $loan->amount_requested + $processingFee + $interestAmount;
    }
    
    // Amount requested
    $pdf->SetFillColor(245, 245, 245);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    
    $pdf->Cell(140, 8, 'Amount Requested:', 1, 0, 'L', true);
    $pdf->Cell(40, 8, 'KES ' . number_format($loan->amount_requested, 2), 1, 1, 'R', true);
    
    // Processing fee
    $pdf->Cell(140, 8, 'Processing Fee (' . $loan->processing_fee . '%):', 1, 0, 'L');
    $pdf->Cell(40, 8, 'KES ' . number_format($processingFee, 2), 1, 1, 'R');
    
    // Interest amount
    $pdf->Cell(140, 8, 'Interest (' . $loan->interest_rate . '% p.a. for ' . $loan->term_requested . ' months):', 1, 0, 'L', true);
    $pdf->Cell(40, 8, 'KES ' . number_format($interestAmount, 2), 1, 1, 'R', true);
    
    // Total repayable
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    
    $pdf->Cell(140, 8, 'TOTAL REPAYABLE AMOUNT:', 1, 0, 'L', true);
    $pdf->Cell(40, 8, 'KES ' . number_format($totalRepayable, 2), 1, 1, 'R', true);
    
    // Add repayment information if available
    if ($loan->status == 'disbursed' || $loan->status == 'completed') {
        $totalPaid = $loan->total_repayment_amount - $loan->remaining_balance;
        $progress = ($totalPaid / $loan->total_repayment_amount) * 100;
        
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        
        $pdf->Ln(2);
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(140, 8, 'Paid to Date:', 1, 0, 'L', true);
        $pdf->Cell(40, 8, 'KES ' . number_format($totalPaid, 2), 1, 1, 'R', true);
        
        $pdf->Cell(140, 8, 'Remaining Balance:', 1, 0, 'L');
        $pdf->Cell(40, 8, 'KES ' . number_format($loan->remaining_balance, 2), 1, 1, 'R');
        
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(140, 8, 'Repayment Progress:', 1, 0, 'L', true);
        $pdf->Cell(40, 8, round($progress, 1) . '%', 1, 1, 'R', true);
    }
    
    $pdf->Ln(10);
    
    // ===== SECTION 3: REPAYMENT HISTORY =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, 'REPAYMENT HISTORY', 0, 1, 'L');
    
    if (count($repayments) > 0) {
        // Table header
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        
        $pdf->Cell(40, 8, 'Date', 1, 0, 'C', true);
        $pdf->Cell(35, 8, 'Source', 1, 0, 'C', true);
        $pdf->Cell(60, 8, 'Reference', 1, 0, 'C', true);
        $pdf->Cell(45, 8, 'Amount', 1, 1, 'C', true);
        
        // Table content
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        
        $totalRepaid = 0;
        
        foreach ($repayments as $idx => $payment) {
            // Alternate row colors
            $fillColor = ($idx % 2 == 0) ? true : false;
            if ($fillColor) {
                $pdf->SetFillColor(245, 245, 245);
            }
            
            $totalRepaid += $payment->amount;
            $paymentDate = date('M d, Y', strtotime($payment->payment_date));
            $source = $payment->payment_method;
            
            if ($payment->delivery_id) {
                $reference = 'DLVR' . str_pad($payment->delivery_id, 6, '0', STR_PAD_LEFT);
                if ($payment->product_name) {
                    $reference .= ' (' . $payment->product_name . ')';
                }
            } else {
                $reference = $payment->notes ?? 'Direct Payment';
            }
            
            $pdf->Cell(40, 7, $paymentDate, 1, 0, 'C', $fillColor);
            $pdf->Cell(35, 7, ucfirst($source), 1, 0, 'L', $fillColor);
            $pdf->Cell(60, 7, $reference, 1, 0, 'L', $fillColor);
            $pdf->Cell(45, 7, 'KES ' . number_format($payment->amount, 2), 1, 1, 'R', $fillColor);
        }
        
        // Total row
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        
        $pdf->Cell(135, 8, 'TOTAL REPAID', 1, 0, 'R', true);
        $pdf->Cell(45, 8, 'KES ' . number_format($totalRepaid, 2), 1, 1, 'R', true);
        
    } else {
        // No repayment data
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetFillColor(245, 245, 245);
        
        if ($loan->status == 'disbursed') {
            $message = 'No repayments recorded yet. Repayments will be automatically deducted from produce sales.';
        } else if ($loan->status == 'approved' || $loan->status == 'pending' || $loan->status == 'under_review') {
            $message = 'Loan has not yet been disbursed. Repayment history will be available after disbursement.';
        } else {
            $message = 'No repayment records found for this loan.';
        }
        
        $pdf->Cell(0, 10, $message, 1, 1, 'C', true);
    }
    
    // ===== DOCUMENT FOOTER =====
    $pdf->Ln(10);
    
    // Add contact information
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->MultiCell(0, 5, "For inquiries about this loan statement, please contact our office at support@dfcs.com or call +254 700 000 000.", 0, 'L');
    
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->Cell(0, 6, 'This is a computer-generated document and does not require a signature.', 0, 1, 'C');
    $pdf->Cell(0, 6, 'Generated on: ' . date('F d, Y h:i A'), 0, 1, 'C');
    
    // Output the PDF
    $pdfName = 'Loan_Statement_LOAN' . str_pad($loanId, 5, '0', STR_PAD_LEFT) . '.pdf';
    $pdf->Output($pdfName, 'I');
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