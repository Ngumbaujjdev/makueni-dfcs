<?php
// Suppress display of errors
ini_set('display_errors', 0);
error_reporting(0);

// Start output buffering
ob_start();

// Include necessary files
include "../../config/config.php";
include "../../libs/App.php";
include "../../vendor/autoload.php";

use TCPDF as PDF;

// Check if the request method is POST and the repaymentId is set
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['repaymentId'])) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request. Repayment ID is required.']);
    exit;
}

$repaymentId = intval($_POST['repaymentId']);

try {
    // Clean any output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Initialize App for database operations
    $app = new App();
    
    // First, get the basic repayment information to verify it exists
    $basicQuery = "SELECT * FROM input_credit_repayments WHERE id = :repayment_id";
    $basicRepayment = $app->selectOne($basicQuery, [':repayment_id' => $repaymentId]);
    
    if (!$basicRepayment) {
        throw new Exception("Repayment with ID $repaymentId not found in the database.");
    }
    
    // Get the approved credit details
    $creditQuery = "SELECT * FROM approved_input_credits WHERE id = :approved_credit_id";
    $approvedCredit = $app->selectOne($creditQuery, [':approved_credit_id' => $basicRepayment->approved_credit_id]);
    
    if (!$approvedCredit) {
        throw new Exception("Approved credit ID {$basicRepayment->approved_credit_id} not found.");
    }
    
    // Get the credit application details
    $applicationQuery = "SELECT * FROM input_credit_applications WHERE id = :credit_application_id";
    $application = $app->selectOne($applicationQuery, [':credit_application_id' => $approvedCredit->credit_application_id]);
    
    if (!$application) {
        throw new Exception("Credit application ID {$approvedCredit->credit_application_id} not found.");
    }
    
    // Get the farmer details
    $farmerQuery = "SELECT f.*, CONCAT(u.first_name, ' ', u.last_name) as farmer_name, 
                    u.phone as farmer_phone, u.email as farmer_email 
                    FROM farmers f 
                    JOIN users u ON f.user_id = u.id 
                    WHERE f.id = :farmer_id";
    $farmer = $app->selectOne($farmerQuery, [':farmer_id' => $application->farmer_id]);
    
    if (!$farmer) {
        throw new Exception("Farmer ID {$application->farmer_id} not found.");
    }
    
    // Get the agrovet details
    $agrovetQuery = "SELECT * FROM agrovets WHERE id = :agrovet_id";
    $agrovet = $app->selectOne($agrovetQuery, [':agrovet_id' => $application->agrovet_id]);
    
    // Get the processed_by user if available
    $processedBy = "System"; // Default value
    // Check if the 'processed_by' property exists before using it
    if (property_exists($basicRepayment, 'processed_by') && !empty($basicRepayment->processed_by)) {
        $userQuery = "SELECT CONCAT(first_name, ' ', last_name) as processed_by_name 
                     FROM users WHERE id = :user_id";
        $user = $app->selectOne($userQuery, [':user_id' => $basicRepayment->processed_by]);
        if ($user) {
            $processedBy = $user->processed_by_name;
        }
    }
    
    // Get the produce delivery details if available
    $produce = null;
    $produceType = null;
    if ($basicRepayment->produce_delivery_id) {
        $produceQuery = "SELECT * FROM produce_deliveries WHERE id = :produce_id";
        $produce = $app->selectOne($produceQuery, [':produce_id' => $basicRepayment->produce_delivery_id]);
        
        if ($produce && $produce->farm_product_id) {
            $productQuery = "SELECT pt.name as produce_type 
                            FROM farm_products fp 
                            JOIN product_types pt ON fp.product_type_id = pt.id 
                            WHERE fp.id = :farm_product_id";
            $product = $app->selectOne($productQuery, [':farm_product_id' => $produce->farm_product_id]);
            if ($product) {
                $produceType = $product->produce_type;
            }
        }
    }
    
    // Build a repayment object with all the gathered data
    $repayment = (object)[
        'id' => $basicRepayment->id,
        'approved_credit_id' => $basicRepayment->approved_credit_id,
        'produce_delivery_id' => $basicRepayment->produce_delivery_id,
        'amount' => $basicRepayment->amount,
        'payment_date' => $basicRepayment->deduction_date,
        'payment_method' => 'produce_deduction',
        'notes' => $basicRepayment->notes,
        'created_at' => $basicRepayment->created_at,
        'credit_application_id' => $approvedCredit->credit_application_id,
        'approved_amount' => $approvedCredit->approved_amount,
        'credit_percentage' => $approvedCredit->credit_percentage,
        'total_with_interest' => $approvedCredit->total_with_interest,
        'repayment_percentage' => $approvedCredit->repayment_percentage,
        'remaining_balance' => $approvedCredit->remaining_balance,
        'balance_before' => floatval($approvedCredit->remaining_balance) + floatval($basicRepayment->amount),
        'fulfillment_date' => $approvedCredit->fulfillment_date,
        'credit_status' => $approvedCredit->status,
        'farmer_id' => $application->farmer_id,
        'agrovet_id' => $application->agrovet_id,
        'application_status' => $application->status,
        'farmer_name' => $farmer->farmer_name,
        'farmer_reg' => $farmer->registration_number,
        'farmer_phone' => $farmer->farmer_phone,
        'farmer_email' => $farmer->farmer_email,
        'agrovet_name' => $agrovet ? $agrovet->name : null,
        'processed_by_name' => $processedBy
    ];
    
    // Add produce details if available
    if ($produce) {
        $repayment->produce_quantity = $produce->quantity;
        $repayment->produce_unit_price = $produce->unit_price;
        $repayment->produce_total_value = $produce->total_value;
        $repayment->produce_quality = $produce->quality_grade;
        $repayment->produce_type = $produceType;
    }
    
    // Create new PDF document
    $pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('DFCS System');
    $pdf->SetAuthor('Makueni DFCS');
    $pdf->SetTitle('Input Credit Payment Receipt');
    $pdf->SetSubject('Payment Receipt #REP' . str_pad($repaymentId, 5, '0', STR_PAD_LEFT));
    
    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    
    // Set margins
    $pdf->SetMargins(10, 10, 10);
    $pdf->SetHeaderMargin(0);
    $pdf->SetFooterMargin(0);
    
    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, 10);
    
    // Set default font
    $pdf->SetFont('helvetica', '', 10);
    
    // Add a page (A4 size)
    $pdf->AddPage();
    
    // Set theme colors
    $primaryColor = [106, 163, 45]; // Green #6AA32D
    $secondaryColor = [74, 34, 15]; // Brown #4A220F
    $successColor = [40, 167, 69]; // Success green
    
    // ===== DOCUMENT HEADER =====
    // Logo
    $logoPath = '../../assets/images/brand-logos/logo3.png'; // Adjust path as needed
    $pdf->Image($logoPath, 93, 15, 25, 0, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    
    // Right side company information
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY(110, 25);
    $pdf->Cell(90, 4, 'Makueni DFCS', 0, 1, 'R');
    $pdf->SetX(110);
    $pdf->Cell(90, 4, 'P.O. Box 123, Makueni', 0, 1, 'R');
    $pdf->SetX(110);
    $pdf->Cell(90, 4, 'Tel: +254 700 000 000', 0, 1, 'R');
    $pdf->SetX(110);
    $pdf->Cell(90, 4, 'Email: support@dfcs.com', 0, 1, 'R');
    
    // Document Title
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY(10, 45);
    $pdf->Cell(190, 10, 'INPUT CREDIT PAYMENT RECEIPT', 0, 1, 'C');
    
    // Receipt Number and Date
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->SetTextColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
    $pdf->Cell(190, 6, 'Receipt #: REP' . str_pad($repaymentId, 5, '0', STR_PAD_LEFT), 0, 1, 'C');
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(190, 6, 'Date: ' . date('F d, Y', strtotime($repayment->payment_date)), 0, 1, 'C');
    
    // Divider line
    $pdf->Line(10, $pdf->GetY() + 3, 200, $pdf->GetY() + 3);
    
    // ===== PAYMENT DETAILS SECTION =====
    $pdf->Ln(5);
    
    // Section Title
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(190, 8, 'PAYMENT DETAILS', 0, 1, 'L');
    
    // Create payment details table
    $pdf->SetFillColor(245, 245, 245);
    $pdf->SetLineWidth(0.1);
    $pdf->SetDrawColor(200, 200, 200);
    
    // Table - Payment Details
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(80, 80, 80);
    
    // Row 1
    $pdf->Cell(50, 8, 'Payment Date:', 1, 0, 'L', true);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(45, 8, date('M d, Y', strtotime($repayment->payment_date)), 1, 0, 'L');
    
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->Cell(50, 8, 'Payment Method:', 1, 0, 'L', true);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(45, 8, 'Produce Deduction', 1, 1, 'L');
    
    // Row 2
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->Cell(50, 8, 'Amount Paid:', 1, 0, 'L', true);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor($successColor[0], $successColor[1], $successColor[2]);
    $pdf->Cell(45, 8, 'KES ' . number_format($repayment->amount, 2), 1, 0, 'L');
    
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->Cell(50, 8, 'Status:', 1, 0, 'L', true);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor($successColor[0], $successColor[1], $successColor[2]);
    $pdf->Cell(45, 8, 'COMPLETED', 1, 1, 'L');
    
    // Row 3
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->Cell(50, 8, 'Reference:', 1, 0, 'L', true);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(140, 8, 'ICRED' . str_pad($repayment->credit_application_id, 5, '0', STR_PAD_LEFT), 1, 1, 'L');
    
    // ===== FARMER INFORMATION SECTION =====
    $pdf->Ln(5);
    
    // Section Title
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(190, 8, 'FARMER INFORMATION', 0, 1, 'L');
    
    // Table - Farmer Info
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(80, 80, 80);
    
    // Row 1
    $pdf->Cell(50, 8, 'Name:', 1, 0, 'L', true);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(140, 8, $repayment->farmer_name, 1, 1, 'L');
    
    // Row 2
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->Cell(50, 8, 'Registration No:', 1, 0, 'L', true);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(140, 8, $repayment->farmer_reg, 1, 1, 'L');
    
    // Row 3
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->Cell(50, 8, 'Phone:', 1, 0, 'L', true);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(45, 8, $repayment->farmer_phone, 1, 0, 'L');
    
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->Cell(50, 8, 'Email:', 1, 0, 'L', true);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(45, 8, $repayment->farmer_email, 1, 1, 'L');
    
    // ===== CREDIT INFORMATION SECTION =====
    $pdf->Ln(5);
    
    // Section Title
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(190, 8, 'CREDIT INFORMATION', 0, 1, 'L');
    
    // Table - Credit Info
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(80, 80, 80);
    
    // Row 1
    $pdf->Cell(50, 8, 'Original Credit Amount:', 1, 0, 'L', true);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(140, 8, 'KES ' . number_format($repayment->approved_amount, 2), 1, 1, 'L');
    
    // Row 2
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->Cell(50, 8, 'Interest Rate:', 1, 0, 'L', true);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(140, 8, $repayment->credit_percentage . '%', 1, 1, 'L');
    
    // Row 3
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->Cell(50, 8, 'Total with Interest:', 1, 0, 'L', true);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(140, 8, 'KES ' . number_format($repayment->total_with_interest, 2), 1, 1, 'L');
    
    // Row 4
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->Cell(50, 8, 'Balance Before Payment:', 1, 0, 'L', true);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(140, 8, 'KES ' . number_format($repayment->balance_before, 2), 1, 1, 'L');
    
    // Row 5
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->Cell(50, 8, 'Balance After Payment:', 1, 0, 'L', true);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor($successColor[0], $successColor[1], $successColor[2]);
    $pdf->Cell(140, 8, 'KES ' . number_format($repayment->remaining_balance, 2), 1, 1, 'L');
    
    // ===== PRODUCE INFORMATION SECTION (if payment is from produce deduction) =====
    if ($repayment->payment_method == 'produce_deduction' && $repayment->produce_delivery_id && $produce) {
        $pdf->Ln(5);
        
        // Section Title
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->Cell(190, 8, 'PRODUCE INFORMATION', 0, 1, 'L');
        
        // Table - Produce Info
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetTextColor(80, 80, 80);
        
        // Row 1
        $pdf->Cell(50, 8, 'Delivery Reference:', 1, 0, 'L', true);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(140, 8, 'DLVR' . str_pad($repayment->produce_delivery_id, 5, '0', STR_PAD_LEFT), 1, 1, 'L');
        
        // Row 2
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->Cell(50, 8, 'Produce Type:', 1, 0, 'L', true);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(140, 8, $repayment->produce_type ?? 'Not specified', 1, 1, 'L');
        
        // Row 3
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->Cell(50, 8, 'Quantity:', 1, 0, 'L', true);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(140, 8, number_format($repayment->produce_quantity, 2) . ' KGs', 1, 1, 'L');
        
        // Row 4
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->Cell(50, 8, 'Total Produce Value:', 1, 0, 'L', true);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(140, 8, 'KES ' . number_format($repayment->produce_total_value, 2), 1, 1, 'L');
        
        // Row 5
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->Cell(50, 8, 'Deduction Percentage:', 1, 0, 'L', true);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(140, 8, $repayment->repayment_percentage . '%', 1, 1, 'L');
    }
    
    // ===== AUTHORIZATION SECTION =====
    $pdf->Ln(5);
    
    // Section Title
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(190, 8, 'AUTHORIZATION', 0, 1, 'L');
    
    // Table - Authorization Info
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(80, 80, 80);
    
    // Row 1
    $pdf->Cell(50, 8, 'Processed By:', 1, 0, 'L', true);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(140, 8, $repayment->processed_by_name, 1, 1, 'L');
    
    // Row 2
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->Cell(50, 8, 'Processing Date:', 1, 0, 'L', true);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(140, 8, date('F d, Y h:i A', strtotime($repayment->created_at)), 1, 1, 'L');
    
    // Notes section (if available)
    if (!empty($repayment->notes)) {
        $pdf->Ln(5);
        
        // Section Title
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->Cell(190, 8, 'NOTES', 0, 1, 'L');
        
        // Notes content
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->MultiCell(190, 8, $repayment->notes, 1, 'L');
    }
    
    // ===== SIGNATURE SECTION =====
    $pdf->Ln(15);
    
    // Signature lines
    $pdf->SetDrawColor(0, 0, 0);
    $pdf->Line(20, $pdf->GetY(), 80, $pdf->GetY());
    $pdf->Line(120, $pdf->GetY(), 180, $pdf->GetY());
    
    $pdf->SetXY(20, $pdf->GetY() + 1);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(60, 5, 'AUTHORIZED SIGNATURE', 0, 0, 'C');
    
    $pdf->SetXY(120, $pdf->GetY());
    $pdf->Cell(60, 5, 'OFFICIAL STAMP', 0, 1, 'C');
    
    // ===== FOOTER SECTION =====
    $pdf->Ln(10);
    
    // Disclaimer
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->Cell(190, 5, 'This is a computer-generated document and does not require a physical signature.', 0, 1, 'C');
    $pdf->Cell(190, 5, 'Please retain this receipt for your records.', 0, 1, 'C');
    
    // Contact information
    $pdf->Ln(3);
    $pdf->Cell(190, 5, 'For any inquiries about this payment, please contact our office at support@dfcs.com or call +254 700 000 000.', 0, 1, 'C');
    
    // Generation date at bottom
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', 'I', 7);
    $pdf->Cell(190, 4, 'Generated on: ' . date('F d, Y h:i A'), 0, 1, 'R');
    
    // Output the PDF
    $pdfName = 'Credit_Payment_Receipt_REP' . str_pad($repaymentId, 5, '0', STR_PAD_LEFT) . '.pdf';
    $pdf->Output($pdfName, 'I');
    exit;
    
} catch (Exception $e) {
    // Clean output buffer
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Return error as JSON
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => 'Failed to generate PDF: ' . $e->getMessage()]);
    exit;
}
?>