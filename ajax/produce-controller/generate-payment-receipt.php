<?php
include "../../config/config.php";
include "../../libs/App.php";
include "../../vendor/autoload.php";

use TCPDF as PDF;

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['produceId'])) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request. Produce ID is required.']);
    exit;
}

try {
    if (ob_get_length()) ob_clean();
    ob_start();
    
    $app = new App();
    $produceId = intval($_POST['produceId']);
    
    // Fetch complete payment details including farmer details, produce info, and payment info
   $query = "SELECT 
            pd.id,
            pd.quantity,
            pd.unit_price,
            pd.total_value,
            pd.quality_grade,
            pd.delivery_date,
            pd.status,
            pd.sale_date,
            pt.name as product_name,
            f.name as farm_name,
            f.location as farm_location,
            CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
            u.phone as farmer_phone,
            u.email as farmer_email,
            fm.registration_number,
            fat.amount as payment_amount,
            fat.created_at as payment_date,
            GROUP_CONCAT(
                CONCAT(
                    'LOAN', LPAD(lr.approved_loan_id, 5, '0'),
                    ':', COALESCE(lr.amount, 0)
                )
            ) as loan_repayments
          FROM produce_deliveries pd
          JOIN farm_products fp ON pd.farm_product_id = fp.id
          JOIN product_types pt ON fp.product_type_id = pt.id
          JOIN farms f ON fp.farm_id = f.id
          JOIN farmers fm ON f.farmer_id = fm.id
          JOIN users u ON fm.user_id = u.id
          JOIN farmer_accounts fa ON fm.id = fa.farmer_id
          LEFT JOIN farmer_account_transactions fat ON fa.id = fat.farmer_account_id 
               AND fat.reference_id = pd.id
          LEFT JOIN loan_repayments lr ON lr.produce_delivery_id = pd.id
          WHERE pd.id = :produce_id
          AND pd.status = 'sold' 
          AND pd.is_sold = 1
          GROUP BY pd.id";
    
    $params = [':produce_id' => $produceId];
    $payment = $app->selectOne($query, $params);
    
    if (!$payment) {
        throw new Exception("Payment record not found");
    }

    // Parse loan repayments
    $loanRepayments = [];
    if ($payment->loan_repayments) {
        foreach (explode(',', $payment->loan_repayments) as $repayment) {
            list($reference, $amount) = explode(':', $repayment);
            $loanRepayments[] = [
                'reference' => $reference,
                'amount' => floatval($amount)
            ];
        }
    }

    // Calculate payment breakdown
    $totalValue = $payment->total_value;
    $commission = $totalValue * 0.10; // 10% commission
    $grossPayment = $totalValue - $commission;
    $totalLoanRepayments = array_sum(array_column($loanRepayments, 'amount'));
    $netPayment = $payment->payment_amount;

    // Create PDF
    $pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document info
    $pdf->SetCreator('DFCS System');
    $pdf->SetAuthor('Makueni DFCS');
    $pdf->SetTitle('Payment Receipt');
    
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
    
    // ===== RECEIPT HEADER =====
    $pdf->Image('http://localhost/dfcs/assets/images/brand-logos/logo3.png', 15, 10, 30);
    
    $pdf->SetFont('helvetica', 'B', 20);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, 'PAYMENT RECEIPT', 0, 1, 'C');
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
    $pdf->Cell(0, 6, 'Reference #: RCPT' . str_pad($payment->id, 5, '0', STR_PAD_LEFT), 0, 1, 'C');
    $pdf->Cell(0, 6, 'Date: ' . date('F d, Y', strtotime($payment->payment_date)), 0, 1, 'C');
    
    // Add success badge
    $pdf->Ln(5);
    $pdf->SetFillColor(40, 167, 69);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('helvetica', 'B', 10);
    $statusWidth = 40;
    $pageWidth = $pdf->GetPageWidth() - 30;
    $statusX = ($pageWidth - $statusWidth) / 2 + 15;
    $pdf->SetXY($statusX, $pdf->GetY());
    $pdf->Cell($statusWidth, 6, 'PAID', 0, 1, 'C', true);
    
    // ===== FARMER & PAYMENT INFO =====
    $pdf->Ln(5);
    $pdf->SetDrawColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetLineWidth(0.5);
    
    $infoBoxY = $pdf->GetY();
    $boxWidth = 85;
    $boxHeight = 45;
    
    // Farmer box
    $pdf->Rect(15, $infoBoxY, $boxWidth, $boxHeight);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY(20, $infoBoxY + 2);
    $pdf->Cell(75, 6, 'Paid To', 0, 1);
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY(20, $pdf->GetY() + 1);
    $pdf->Cell(75, 5, $payment->farmer_name, 0, 1);
    $pdf->SetX(20);
    $pdf->Cell(75, 5, 'Reg: ' . $payment->registration_number, 0, 1);
    $pdf->SetX(20);
    $pdf->Cell(75, 5, 'Phone: ' . $payment->farmer_phone, 0, 1);
    $pdf->SetX(20);
    $pdf->Cell(75, 5, 'Farm: ' . $payment->farm_name, 0, 1);
    
    // Payment box
    $pdf->Rect(110, $infoBoxY, $boxWidth, $boxHeight);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY(115, $infoBoxY + 2);
    $pdf->Cell(75, 6, 'Payment Details', 0, 1);
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY(115, $pdf->GetY() + 1);
    $pdf->Cell(75, 5, 'Receipt #: RCPT' . str_pad($payment->id, 5, '0', STR_PAD_LEFT), 0, 1);
    $pdf->SetX(115);
    $pdf->Cell(75, 5, 'Produce: DLVR' . str_pad($produceId, 5, '0', STR_PAD_LEFT), 0, 1);
    $pdf->SetX(115);
    $pdf->Cell(75, 5, 'Date: ' . date('M d, Y', strtotime($payment->payment_date)), 0, 1);
    $pdf->SetX(115);
    $pdf->Cell(75, 5, 'Status: Paid', 0, 1);
    
    $pdf->Ln(10);
// After the two info boxes and before the produce table section
$pdf->Ln(10); // Add space after the info boxes

// ===== PRODUCE INFORMATION =====
$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
$pdf->Cell(0, 8, 'PRODUCE INFORMATION', 0, 1, 'L');
$pdf->Ln(2);

// Add the green header row
$pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('helvetica', 'B', 10);

// Table headers
$pdf->Cell(60, 8, 'Product', 1, 0, 'L', true);
$pdf->Cell(30, 8, 'Grade', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Quantity', 1, 0, 'R', true);
$pdf->Cell(30, 8, 'Unit Price', 1, 0, 'R', true);
$pdf->Cell(30, 8, 'Total', 1, 1, 'R', true);
    
    // Produce table
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('helvetica', 'B', 10);
    
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(60, 8, $payment->product_name, 1, 0, 'L');
    $pdf->Cell(30, 8, 'Grade ' . $payment->quality_grade, 1, 0, 'C');
    $pdf->Cell(30, 8, number_format($payment->quantity, 2), 1, 0, 'R');
    $pdf->Cell(30, 8, number_format($payment->unit_price, 2), 1, 0, 'R');
    $pdf->Cell(30, 8, number_format($payment->total_value, 2), 1, 1, 'R');
    
    $pdf->Ln(5);

    // ===== PAYMENT BREAKDOWN =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 8, 'PAYMENT BREAKDOWN', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Payment table
    $pdf->SetFillColor(245, 245, 245);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    
    // Sale value
    $pdf->Cell(130, 8, 'Sale Value', 1, 0, 'L', true);
    $pdf->Cell(50, 8, 'KES ' . number_format($totalValue, 2), 1, 1, 'R', true);
    
    // Commission
    $pdf->Cell(130, 8, 'SACCO Commission (10%)', 1, 0, 'L', true);
    $pdf->Cell(50, 8, 'KES ' . number_format($commission, 2), 1, 1, 'R', true);
    
    // Gross payment
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(130, 8, 'Gross Payment', 1, 0, 'L', true);
    $pdf->Cell(50, 8, 'KES ' . number_format($grossPayment, 2), 1, 1, 'R', true);
    
    // Loan repayments if any
    if (!empty($loanRepayments)) {
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(130, 8, 'Loan Repayments:', 1, 0, 'L', true);
        $pdf->Cell(50, 8, 'KES ' . number_format($totalLoanRepayments, 2), 1, 1, 'R', true);
        
        foreach ($loanRepayments as $repayment) {
            $pdf->Cell(130, 6, '  - ' . $repayment['reference'], 1, 0, 'L');
            $pdf->Cell(50, 6, 'KES ' . number_format($repayment['amount'], 2), 1, 1, 'R');
        }
    }
    
    // Net payment
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(130, 8, 'NET PAYMENT TO FARMER', 1, 0, 'L', true);
    $pdf->Cell(50, 8, 'KES ' . number_format($netPayment, 2), 1, 1, 'R', true);
    
    $pdf->Ln(5);

    // ===== PAYMENT NOTES =====
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(0, 8, 'PAYMENT NOTES', 0, 1, 'L');
    
    $pdf->SetFont('helvetica', '', 10);
    $notes = "This payment has been processed and credited to the farmer's account. ";
    $notes .= !empty($loanRepayments) ? "Loan repayments have been automatically deducted as shown above." : "No loan repayments were due for this payment.";
    
    $pdf->MultiCell(0, 6, $notes, 0, 'L');
    $pdf->Ln(5);

    // ===== TERMS =====
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, 'Terms & Conditions', 0, 1, 'L');
    
    $pdf->SetFont('helvetica', '', 8);
    $pdf->SetTextColor(80, 80, 80);
    $terms = "1. This receipt serves as proof of payment for the produce delivery.\n";
    $terms .= "2. The payment includes standard SACCO commission deductions.\n";
    $terms .= "3. Any disputes regarding this payment must be reported within 7 days.\n";
    $terms .= "4. Please retain this receipt for your records.";
    // Continue from where we left off...
    
    $pdf->MultiCell(0, 4, $terms, 0, 'L');
    
    // ===== VERIFICATION QR CODE =====
    $pdf->Ln(5);
    
    // Payment verification data for QR code
    $qrData = "Receipt #RCPT" . str_pad($payment->id, 5, '0', STR_PAD_LEFT) . "\n" .
              "Farmer: " . $payment->farmer_name . "\n" .
              "Amount: KES " . number_format($netPayment, 2) . "\n" .
              "Date: " . date('Y-m-d H:i:s', strtotime($payment->payment_date)) . "\n" .
              "Produce: DLVR" . str_pad($produceId, 5, '0', STR_PAD_LEFT);
    
    // Add QR code
    $pdf->write2DBarcode(
        $qrData,
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
    
    // Add verification text next to QR code
    $pdf->SetXY(50, $pdf->GetY());
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(0, 4, 'Scan QR code to verify payment authenticity', 0, 1, 'L');
    $pdf->SetX(50);
    $pdf->Cell(0, 4, 'or visit our portal and enter receipt number', 0, 1, 'L');
    
    // ===== FOOTER =====
    $pdf->Ln(10);
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->Cell(0, 6, 'This is a computer-generated receipt and does not require a signature.', 0, 1, 'C');
    $pdf->Cell(0, 6, 'For questions or concerns, please contact DFCS support.', 0, 1, 'C');
    
    // Output the PDF
    $pdf->Output('Payment_Receipt_RCPT' . str_pad($payment->id, 5, '0', STR_PAD_LEFT) . '.pdf', 'I');
    exit;

} catch (Exception $e) {
    // Clean output buffer
    if (ob_get_length()) ob_clean();
    
    // Return error as JSON
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to generate receipt: ' . $e->getMessage()
    ]);
    exit;
}