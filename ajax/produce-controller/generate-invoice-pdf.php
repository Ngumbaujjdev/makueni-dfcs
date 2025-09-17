<?php
include "../../config/config.php";
include "../../libs/App.php";
include "../../vendor/autoload.php";


use TCPDF as PDF;

// Check if the request method is POST and the produceId is set
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['produceId'])) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request. Produce ID is required.']);
    exit;
}

$produceId = intval($_POST['produceId']);

try {
    // Clean any output buffers
    if (ob_get_length()) ob_clean();
    ob_start();
    
    // Initialize App for database operations
    $app = new App();
    
    // Use the EXACT SAME QUERY as your view-details page to ensure compatibility
    $query = "SELECT 
                pd.id,
                pd.farm_product_id,
                pd.quantity,
                pd.unit_price,
                pd.total_value,
                pd.quality_grade,
                pd.delivery_date,
                pd.status,
                pd.notes,
                pd.created_at,
                pt.name as product_name,
                f.id as farm_id,
                f.name as farm_name,
                f.location as farm_location,
                CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                u.phone as farmer_phone,
                u.email as farmer_email
              FROM produce_deliveries pd
              JOIN farm_products fp ON pd.farm_product_id = fp.id
              JOIN product_types pt ON fp.product_type_id = pt.id
              JOIN farms f ON fp.farm_id = f.id
              JOIN farmers fm ON f.farmer_id = fm.id
              JOIN users u ON fm.user_id = u.id
              WHERE pd.id = :produce_id";
    
    $params = [
        ':produce_id' => $produceId
    ];
    
    $produce = $app->selectOne($query, $params);
    
    if (!$produce) {
        throw new Exception("Produce record with ID $produceId not found in database.");
    }
    
    // Create new PDF document
    $pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('DFCS System');
    $pdf->SetAuthor('Makueni DFCS');
    $pdf->SetTitle('Produce Delivery Document');
    $pdf->SetSubject('Produce Delivery #DLVR' . str_pad($produceId, 5, '0', STR_PAD_LEFT));
    
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
    
    // Set theme colors based on your system's theme
    $primaryColor = [106, 163, 45]; // Green from your UI (#6AA32D)
    $secondaryColor = [13, 110, 253]; // Blue
    $accentColor = [108, 117, 125]; // Gray
    $successColor = [40, 167, 69]; // Success green
    
    // ===== DOCUMENT HEADER =====
    // Logo - fixed the Image() parameter
    $logoPath = 'http://localhost/dfcs/assets/images/brand-logos/logo3.png';
    $pdf->Image($logoPath, 15, 10, 30, 0, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    
    // Document Title & Number
    $pdf->SetFont('helvetica', 'B', 20);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY(15, 15);
    $pdf->Cell(0, 10, 'PRODUCE DELIVERY DOCUMENT', 0, 1, 'C');
    
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 6, 'Reference #: DLVR' . str_pad($produceId, 5, '0', STR_PAD_LEFT), 0, 1, 'C');
    
    // Date
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor($accentColor[0], $accentColor[1], $accentColor[2]);
    $pdf->Cell(0, 6, 'Date: ' . date('F d, Y', strtotime($produce->delivery_date)), 0, 1, 'C');
    
    // Status badge
    $pdf->Ln(5);
    
    // Status label with color based on status
    $statusText = ucfirst($produce->status);
    $statusColors = [
        'pending' => [255, 193, 7],
        'verified' => [23, 162, 184],
        'rejected' => [220, 53, 69],
        'sold' => [40, 167, 69]
    ];
    $statusColor = isset($statusColors[$produce->status]) ? $statusColors[$produce->status] : $accentColor;
    
    $pdf->SetFillColor($statusColor[0], $statusColor[1], $statusColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('helvetica', 'B', 10);
    
    // Calculate the width of the status badge and center it
    $statusWidth = 40;
    $pageWidth = $pdf->GetPageWidth() - 30; // Page width minus margins
    $statusX = ($pageWidth - $statusWidth) / 2 + 15; // Add left margin
    
    $pdf->SetXY($statusX, $pdf->GetY());
    $pdf->Cell($statusWidth, 6, $statusText, 0, 1, 'C', true);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(5);
    
    // ===== FARMER & PRODUCE INFO SECTION =====
    // This section replaces the existing farmer & produce info section
$pdf->SetDrawColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
$pdf->SetLineWidth(0.5);

// Y position after status badge
$infoBoxY = $pdf->GetY();

// Draw rectangles for farmer and produce info - set specific width and height
$boxWidth = 85;
$boxHeight = 45;
$pdf->Rect(15, $infoBoxY, $boxWidth, $boxHeight, 'D');
$pdf->Rect(110, $infoBoxY, $boxWidth, $boxHeight, 'D');

// Farmer Information
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
$pdf->SetXY(20, $infoBoxY + 2);
$pdf->Cell($boxWidth - 10, 6, 'Farmer Information', 0, 1);

$pdf->SetFont('helvetica', '', 10);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetXY(20, $pdf->GetY() + 1);
$pdf->Cell($boxWidth - 10, 5, 'Name: ' . $produce->farmer_name, 0, 1);
$pdf->SetX(20);
$pdf->Cell($boxWidth - 10, 5, 'Phone: ' . $produce->farmer_phone, 0, 1);
$pdf->SetX(20);
$pdf->Cell($boxWidth - 10, 5, 'Email: ' . $produce->farmer_email, 0, 1);
$pdf->SetX(20);
$pdf->Cell($boxWidth - 10, 5, 'Farm: ' . $produce->farm_name, 0, 1);

// Product Information - Position correctly in the right box
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
$pdf->SetXY(115, $infoBoxY + 2);
$pdf->Cell($boxWidth - 10, 6, 'Product Information', 0, 1);

$pdf->SetFont('helvetica', '', 10);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetXY(115, $pdf->GetY() + 1);
$pdf->Cell($boxWidth - 10, 5, 'Product: ' . $produce->product_name, 0, 1);
$pdf->SetX(115);
$pdf->Cell($boxWidth - 10, 5, 'Quality: Grade ' . $produce->quality_grade, 0, 1);
$pdf->SetX(115);
$pdf->Cell($boxWidth - 10, 5, 'Location: ' . $produce->farm_location, 0, 1);
$pdf->SetX(115);
$pdf->Cell($boxWidth - 10, 5, 'Delivery Date: ' . date('M d, Y', strtotime($produce->delivery_date)), 0, 1);

// Set Y position after the info boxes
$pdf->SetY($infoBoxY + $boxHeight + 5);
    
    // ===== PRODUCE DETAILS SECTION =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, 'PRODUCE DETAILS', 0, 1, 'C');
    $pdf->Ln(5);
    
    // Create table for produce details
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('helvetica', 'B', 10);
    
    // Table Header
    $pdf->Cell(60, 8, 'Item', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Grade', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Quantity (KGs)', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Rate (KES/KG)', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Total (KES)', 1, 1, 'C', true);
    
    // Table Row
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    
    // Set background color based on quality grade
    $gradeColors = [
        'A' => [229, 255, 229], // Light green for Grade A
        'B' => [255, 250, 230], // Light yellow for Grade B
        'C' => [255, 240, 240]  // Light red for Grade C
    ];
    
    $gradeBgColor = isset($gradeColors[$produce->quality_grade]) 
        ? $gradeColors[$produce->quality_grade] 
        : [255, 255, 255];
    
    $pdf->SetFillColor($gradeBgColor[0], $gradeBgColor[1], $gradeBgColor[2]);
    
    $pdf->Cell(60, 8, $produce->product_name, 1, 0, 'L', true);
    $pdf->Cell(30, 8, 'Grade ' . $produce->quality_grade, 1, 0, 'C', true);
    $pdf->Cell(30, 8, number_format($produce->quantity, 2), 1, 0, 'R', true);
    $pdf->Cell(30, 8, number_format($produce->unit_price, 2), 1, 0, 'R', true);
    $pdf->Cell(30, 8, number_format($produce->total_value, 2), 1, 1, 'R', true);
    
    // Add some space before totals
    $pdf->Ln(5);
    
    // Calculate commission and farmer payment
    $commission = $produce->total_value * 0.10; // 10% commission
    $farmerPayment = $produce->total_value - $commission;
    
    // Totals section
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColor(240, 240, 240);
    $pdf->Cell(120, 8, '', 0, 0);
    $pdf->Cell(30, 8, 'Sale Value:', 1, 0, 'L', true);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(30, 8, 'KES ' . number_format($produce->total_value, 2), 1, 1, 'R', true);
    
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(120, 8, '', 0, 0);
    $pdf->Cell(30, 8, 'Commission (10%):', 1, 0, 'L', true);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(30, 8, 'KES ' . number_format($commission, 2), 1, 1, 'R', true);
    
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(120, 8, '', 0, 0);
    $pdf->Cell(30, 8, 'FARMER PAYMENT:', 1, 0, 'L', true);
    $pdf->Cell(30, 8, 'KES ' . number_format($farmerPayment, 2), 1, 1, 'R', true);
    
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(10);
    
    // ===== TRANSACTION STATUS =====
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 8, 'TRANSACTION STATUS', 0, 1, 'L');
    $pdf->Ln(2);
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetDrawColor(200, 200, 200);
    
    $pdf->SetFillColor(245, 245, 245);
    $pdf->Cell(50, 8, 'Current Status', 1, 0, 'L', true);
    
    // Set text color based on status
    $pdf->SetTextColor($statusColor[0], $statusColor[1], $statusColor[2]);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(130, 8, ucfirst($produce->status), 1, 1, 'L');
    
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    
    $pdf->Cell(50, 8, 'Payment Status', 1, 0, 'L', true);
    $paymentStatus = "Not processed";
    if ($produce->status == 'sold') {
        $paymentStatus = 'Paid';
    } elseif ($produce->status == 'verified') {
        $paymentStatus = 'Ready for Sale';
    } elseif ($produce->status == 'rejected') {
        $paymentStatus = 'Rejected';
    } else {
        $paymentStatus = 'Pending Verification';
    }
    $pdf->Cell(130, 8, $paymentStatus, 1, 1, 'L');
    
    $pdf->Cell(50, 8, 'Delivery Date', 1, 0, 'L', true);
    $pdf->Cell(130, 8, date('F d, Y', strtotime($produce->delivery_date)), 1, 1, 'L');
    
    // ===== NOTES =====
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 8, 'NOTES', 0, 1, 'L');
    
    // Get any notes from the produce record
    $notesText = "No additional notes for this delivery.";
    if (!empty($produce->notes)) {
        $notesText = $produce->notes;
    }
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(248, 248, 248);
    $pdf->MultiCell(0, 8, $notesText, 1, 'L', true);
    
    $pdf->Ln(10);
    
    // ===== TERMS AND CONDITIONS =====
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, 'Terms & Conditions', 0, 1, 'L');
    
    $pdf->SetFont('helvetica', '', 8);
    $pdf->SetTextColor(80, 80, 80);
    $termsText = "1. This document serves as proof of produce delivery.\n";
    $termsText .= "2. The SACCO charges a 10% commission on all sales.\n";
    $termsText .= "3. Payment will be processed within 3-5 business days after sale.\n";
    $termsText .= "4. Any disputes must be reported within 7 days of this document date.";
    
    $pdf->MultiCell(0, 4, $termsText, 0, 'L');
    
    // ===== FOOTER =====
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->Cell(0, 6, 'This is a computer-generated document and does not require a signature.', 0, 1, 'C');
    
    // Add QR code with document reference
    $pdf->write2DBarcode(
        'Reference #DLVR' . str_pad($produceId, 5, '0', STR_PAD_LEFT) . 
        "\nFarmer: " . $produce->farmer_name . 
        "\nProduct: " . $produce->product_name .
        "\nStatus: " . ucfirst($produce->status),
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
    
    // Output the PDF
    $pdf->Output('Produce_Document_DLVR' . str_pad($produceId, 5, '0', STR_PAD_LEFT) . '.pdf', 'I');
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