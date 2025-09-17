<?php
include "../../config/config.php";
include "../../libs/App.php";
include "../../vendor/autoload.php";

use TCPDF as PDF;

// Check if the request method is POST and the inputCreditId is set
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['inputCreditId'])) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request. Input Credit ID is required.']);
    exit;
}

$inputCreditId = intval($_POST['inputCreditId']);

try {
    // Clean any output buffers
    if (ob_get_length()) ob_clean();
    ob_start();
    
    // Initialize App for database operations
    $app = new App();
    
    // Get input credit application details with related information
    $query = "SELECT 
                ica.id,
                ica.farmer_id,
                ica.agrovet_id,
                ica.total_amount,
                ica.credit_percentage,
                ica.total_with_interest,
                ica.repayment_percentage,
                ica.application_date,
                ica.status,
                ica.creditworthiness_score,
                ica.rejection_reason,
                ica.review_date,
                ica.created_at,
                ica.updated_at,
                a.name as agrovet_name,
                a.location as agrovet_location,
                a.phone as agrovet_phone,
                a.email as agrovet_email,
                at.name as agrovet_type,
                CASE 
                    WHEN ica.status = 'approved' OR ica.status = 'fulfilled' OR ica.status = 'completed' THEN 
                        (SELECT aic.fulfillment_date FROM approved_input_credits aic WHERE aic.credit_application_id = ica.id)
                    ELSE NULL
                END as fulfillment_date,
                CASE 
                    WHEN ica.status = 'approved' OR ica.status = 'fulfilled' OR ica.status = 'completed' THEN 
                        (SELECT aic.remaining_balance FROM approved_input_credits aic WHERE aic.credit_application_id = ica.id)
                    ELSE NULL
                END as remaining_balance,
                CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                u.phone as farmer_phone,
                u.email as farmer_email,
                f.registration_number as farmer_registration,
                fc.name as farmer_category
              FROM input_credit_applications ica
              JOIN agrovets a ON ica.agrovet_id = a.id
              JOIN agrovet_types at ON a.type_id = at.id
              JOIN farmers f ON ica.farmer_id = f.id
              LEFT JOIN farmer_categories fc ON f.category_id = fc.id
              JOIN users u ON f.user_id = u.id
              WHERE ica.id = :input_credit_id";

    $params = [
        ':input_credit_id' => $inputCreditId
    ];

    $inputCredit = $app->selectOne($query, $params);
    
    if (!$inputCredit) {
        throw new Exception("Input credit application with ID $inputCreditId not found in database.");
    }
    
    // Get creditworthiness breakdown from input credit logs
    $creditScoreQuery = "SELECT description 
                        FROM input_credit_logs 
                        WHERE input_credit_application_id = :input_credit_id 
                        AND action_type = 'creditworthiness_check' 
                        ORDER BY created_at DESC 
                        LIMIT 1";
                        
    $creditScoreLog = $app->selectOne($creditScoreQuery, [':input_credit_id' => $inputCreditId]);

    // Parse credit score components if available
    $creditScores = [
        'input_repayment_history' => 0,
        'financial_obligations' => 0,
        'produce_history' => 0,
        'amount_ratio' => 0
    ];

    if ($creditScoreLog && $creditScoreLog->description) {
        $description = $creditScoreLog->description;
        
        // Extract scores using regex
        preg_match('/Input repayment history score: (\d+)/', $description, $repaymentMatches);
        preg_match('/Financial obligations score: (\d+)/', $description, $obligationsMatches);
        preg_match('/Produce history score: (\d+)/', $description, $produceMatches);
        preg_match('/Amount ratio score: (\d+)/', $description, $amountMatches);
        
        if (!empty($repaymentMatches)) $creditScores['input_repayment_history'] = intval($repaymentMatches[1]);
        if (!empty($obligationsMatches)) $creditScores['financial_obligations'] = intval($obligationsMatches[1]);
        if (!empty($produceMatches)) $creditScores['produce_history'] = intval($produceMatches[1]);
        if (!empty($amountMatches)) $creditScores['amount_ratio'] = intval($amountMatches[1]);
    }
    
    // Get input items
    $inputItemsQuery = "SELECT 
                    ici.id,
                    ici.input_type,
                    ici.input_name,
                    ici.quantity,
                    ici.unit,
                    ici.unit_price,
                    ici.total_price,
                    ici.description
                  FROM input_credit_items ici
                  WHERE ici.credit_application_id = '{$inputCreditId}'
                  ORDER BY ici.input_type, ici.input_name";

    $inputItems = $app->select_all($inputItemsQuery);
    // Get repayment history if available
$repaymentHistory = [];
if ($inputCredit->status == 'fulfilled' || $inputCredit->status == 'completed') {
    $repaymentQuery = "SELECT 
                       icr.id,
                       icr.produce_delivery_id,
                       icr.produce_sale_amount,
                       icr.deducted_amount, 
                       icr.amount,
                       icr.deduction_date,
                       pd.quantity,
                       pt.name as product_name
                    FROM input_credit_repayments icr
                    JOIN approved_input_credits aic ON icr.approved_credit_id = aic.id
                    JOIN produce_deliveries pd ON icr.produce_delivery_id = pd.id
                    JOIN farm_products fp ON pd.farm_product_id = fp.id
                    JOIN product_types pt ON fp.product_type_id = pt.id
                    WHERE aic.credit_application_id = '{$inputCreditId}'
                    ORDER BY icr.deduction_date ASC";
    
    $result = $app->select_all($repaymentQuery);
    $repaymentHistory = is_array($result) ? $result : [];
}
    
    // Get approved input credit details if applicable
    $approvedCredit = null;
    if (in_array($inputCredit->status, ['approved', 'fulfilled', 'completed'])) {
        $approvedCreditQuery = "SELECT * FROM approved_input_credits WHERE credit_application_id = :input_credit_id LIMIT 1";
        $approvedCredit = $app->selectOne($approvedCreditQuery, [':input_credit_id' => $inputCreditId]);
    }
    
    // Calculate repayment progress percentage
    $repaymentProgress = 0;
    $paidAmount = 0;
    if (($inputCredit->status == 'fulfilled' || $inputCredit->status == 'completed') && 
        $inputCredit->total_with_interest > 0 && isset($inputCredit->remaining_balance)) {
        $paidAmount = $inputCredit->total_with_interest - $inputCredit->remaining_balance;
        $repaymentProgress = ($paidAmount / $inputCredit->total_with_interest) * 100;
    }
    
    // Group input items by type for statistics
    $inputTypeStats = [];
    foreach ($inputItems as $item) {
        if (!isset($inputTypeStats[$item->input_type])) {
            $inputTypeStats[$item->input_type] = [
                'count' => 0,
                'total' => 0
            ];
        }
        
        $inputTypeStats[$item->input_type]['count']++;
        $inputTypeStats[$item->input_type]['total'] += $item->total_price;
    }
    
    // Create new PDF document
    $pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('DFCS System');
    $pdf->SetAuthor('Makueni DFCS');
    $pdf->SetTitle('Input Credit Statement');
    $pdf->SetSubject('Input Credit Application #INPCR' . str_pad($inputCreditId, 5, '0', STR_PAD_LEFT));
    
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
    $primaryColor = [106, 163, 45]; // Green #6AA32D
    $secondaryColor = [74, 34, 15]; // Brown #4A220F
    $successColor = [40, 167, 69]; // Success green
    $warningColor = [255, 193, 7]; // Warning yellow
    $dangerColor = [220, 53, 69]; // Danger red
    $infoColor = [23, 162, 184]; // Info blue
    
    // ===== DOCUMENT HEADER =====
    // Logo
    $logoPath = 'http://localhost/dfcs/assets/images/brand-logos/logo3.png';
    $pdf->Image($logoPath, 15, 10, 30, 0, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    
    // Document Title & Number
    $pdf->SetFont('helvetica', 'B', 20);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY(15, 15);
    $pdf->Cell(0, 10, 'INPUT CREDIT STATEMENT', 0, 1, 'C');
    
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 6, 'Reference #: INPCR' . str_pad($inputCreditId, 5, '0', STR_PAD_LEFT), 0, 1, 'C');
    
    // Date
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
    $pdf->Cell(0, 6, 'Date: ' . date('F d, Y', strtotime($inputCredit->application_date)), 0, 1, 'C');
    
    // Status badge
    $pdf->Ln(5);
    
    // Status label with color based on status
    $statusColors = [
        'pending' => $secondaryColor,
        'under_review' => $primaryColor,
        'approved' => $infoColor,
        'rejected' => $dangerColor,
        'fulfilled' => $successColor,
        'completed' => $successColor,
        'cancelled' => $dangerColor
    ];
    
    $statusText = ucfirst(str_replace('_', ' ', $inputCredit->status));
    $statusColor = isset($statusColors[$inputCredit->status]) ? $statusColors[$inputCredit->status] : $secondaryColor;
    
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
    
    // ===== FARMER & AGROVET INFO SECTION =====
    $pdf->SetDrawColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetLineWidth(0.5);

    // Y position after status badge
    $infoBoxY = $pdf->GetY();

    // Draw rectangles for farmer and agrovet info
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
    $pdf->Cell($boxWidth - 10, 5, 'Name: ' . $inputCredit->farmer_name, 0, 1);
    $pdf->SetX(20);
    $pdf->Cell($boxWidth - 10, 5, 'Phone: ' . $inputCredit->farmer_phone, 0, 1);
    $pdf->SetX(20);
    $pdf->Cell($boxWidth - 10, 5, 'Email: ' . $inputCredit->farmer_email, 0, 1);
    $pdf->SetX(20);
    $pdf->Cell($boxWidth - 10, 5, 'Reg #: ' . $inputCredit->farmer_registration, 0, 1);

    // Agrovet Information - Position correctly in the right box
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetXY(115, $infoBoxY + 2);
    $pdf->Cell($boxWidth - 10, 6, 'Agrovet Information', 0, 1);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY(115, $pdf->GetY() + 1);
    $pdf->Cell($boxWidth - 10, 5, 'Name: ' . $inputCredit->agrovet_name, 0, 1);
    $pdf->SetX(115);
    $pdf->Cell($boxWidth - 10, 5, 'Type: ' . $inputCredit->agrovet_type, 0, 1);
    $pdf->SetX(115);
    $pdf->Cell($boxWidth - 10, 5, 'Location: ' . $inputCredit->agrovet_location, 0, 1);
    $pdf->SetX(115);
    $pdf->Cell($boxWidth - 10, 5, 'Phone: ' . $inputCredit->agrovet_phone, 0, 1);

    // Set Y position after the info boxes
    $pdf->SetY($infoBoxY + $boxHeight + 5);
    
    // ===== INPUT CREDIT DETAILS SECTION =====
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, 'INPUT CREDIT DETAILS', 0, 1, 'C');
    $pdf->Ln(2);
    
    // Create table for input credit details
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('helvetica', 'B', 10);
    
    // Table Header
    $pdf->Cell(100, 8, 'Application Details', 1, 0, 'L', true);
    $pdf->Cell(40, 8, 'Date', 1, 0, 'C', true);
    $pdf->Cell(40, 8, 'Status', 1, 1, 'C', true);
    
    // Table Row
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetFillColor(245, 245, 245);
    
    $pdf->Cell(100, 8, 'Input Credit Application', 1, 0, 'L', true);
    $pdf->Cell(40, 8, date('M d, Y', strtotime($inputCredit->application_date)), 1, 0, 'C', true);
    $pdf->Cell(40, 8, ucfirst(str_replace('_', ' ', $inputCredit->status)), 1, 1, 'C', true);
    
    // Show review date if available
    if ($inputCredit->review_date) {
        $pdf->Cell(100, 8, 'Application Review', 1, 0, 'L', true);
        $pdf->Cell(40, 8, date('M d, Y', strtotime($inputCredit->review_date)), 1, 0, 'C', true);
        $pdf->Cell(40, 8, ($inputCredit->status == 'rejected') ? 'Rejected' : 'Reviewed', 1, 1, 'C', true);
    }
    
    // Show fulfillment date if available
    if ($inputCredit->fulfillment_date) {
        $pdf->Cell(100, 8, 'Input Fulfillment', 1, 0, 'L', true);
        $pdf->Cell(40, 8, date('M d, Y', strtotime($inputCredit->fulfillment_date)), 1, 0, 'C', true);
        $pdf->Cell(40, 8, 'Delivered', 1, 1, 'C', true);
    }
    
    // Add some space before amounts
    $pdf->Ln(5);
    
    // Amounts section
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 8, 'CREDIT AMOUNTS', 0, 1, 'L');
    $pdf->Ln(2);
    
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColor(245, 245, 245);
    $pdf->SetTextColor(0, 0, 0);
    
    // Calculate interest amount
    $interestAmount = $inputCredit->total_with_interest - $inputCredit->total_amount;
    
    // Principal amount
    $pdf->Cell(120, 8, 'Principal Amount (Input Value):', 1, 0, 'L', true);
    $pdf->Cell(60, 8, 'KES ' . number_format($inputCredit->total_amount, 2), 1, 1, 'R', true);
    
    // Interest
    $pdf->Cell(120, 8, 'Interest (' . $inputCredit->credit_percentage . '%):', 1, 0, 'L', true);
    $pdf->Cell(60, 8, 'KES ' . number_format($interestAmount, 2), 1, 1, 'R', true);
    
    // Total repayable
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(120, 8, 'TOTAL REPAYABLE AMOUNT:', 1, 0, 'L', true);
    $pdf->Cell(60, 8, 'KES ' . number_format($inputCredit->total_with_interest, 2), 1, 1, 'R', true);
    
    // Repayment percentage
    $pdf->Cell(120, 8, 'REPAYMENT PERCENTAGE:', 1, 0, 'L', true);
    $pdf->Cell(60, 8, $inputCredit->repayment_percentage . '% of produce sales', 1, 1, 'R', true);
    
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(5);
    
    // ===== ITEMIZED INPUTS SECTION =====
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 8, 'ITEMIZED INPUTS', 0, 1, 'L');
    $pdf->Ln(2);
    
    if (count($inputItems) > 0) {
        // Define icons for input types (we'll use text for PDF)
        $inputTypeIcons = [
            'fertilizer' => 'F',
            'pesticide' => 'P',
            'seeds' => 'S',
            'tools' => 'T',
            'other' => 'O'
        ];
        
        // Set up the table header
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        
        $pdf->Cell(10, 8, '#', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'Input Type', 1, 0, 'C', true);
        $pdf->Cell(60, 8, 'Input Name', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'Quantity', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'Unit Price', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'Total Price', 1, 1, 'C', true);
        
        // Reset text color for table content
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        
        // Track the current input type for grouping
        $currentType = '';
        $counter = 1;
        
        // Loop through each input item
        foreach ($inputItems as $item) {
            // If the input type changes, add a group header
            if ($currentType != $item->input_type) {
                $pdf->SetFont('helvetica', 'B', 9);
                $pdf->SetFillColor(230, 230, 230);
                $pdf->Cell(180, 6, ucfirst($item->input_type) . ' Items', 1, 1, 'L', true);
                $pdf->SetFont('helvetica', '', 9);
                $pdf->SetFillColor(245, 245, 245);
                $currentType = $item->input_type;
            }
            
            // Alternate row colors for better readability
            $fillColor = ($counter % 2 == 0) ? true : false;
            
            $pdf->Cell(10, 6, $counter, 1, 0, 'C', $fillColor);
            
            // Icon for input type
            $icon = isset($inputTypeIcons[$item->input_type]) ? $inputTypeIcons[$item->input_type] : 'O';
            $pdf->Cell(30, 6, ucfirst($item->input_type), 1, 0, 'L', $fillColor);
            
            // Input name (with description as tooltip in actual web UI)
            $pdf->Cell(60, 6, $item->input_name, 1, 0, 'L', $fillColor);
            
            // Quantity and unit
            $pdf->Cell(25, 6, $item->quantity . ' ' . $item->unit, 1, 0, 'C', $fillColor);
            
            // Unit price
            $pdf->Cell(25, 6, 'KES ' . number_format($item->unit_price, 2), 1, 0, 'R', $fillColor);
            
            // Total price
            $pdf->Cell(30, 6, 'KES ' . number_format($item->total_price, 2), 1, 1, 'R', $fillColor);
            
            $counter++;
        }
        
        // Add summary row
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        
        $pdf->Cell(150, 8, 'TOTAL INPUT VALUE', 1, 0, 'R', true);
        $pdf->Cell(30, 8, 'KES ' . number_format($inputCredit->total_amount, 2), 1, 1, 'R', true);
    } else {
        // No input items found
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(0, 10, 'No input items found for this input credit application.', 1, 1, 'C', true);
    }
    
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
    
    if ($inputCredit->creditworthiness_score >= 85) {
        $scoreClass = $successColor;
        $scoreText = 'Excellent';
    } elseif ($inputCredit->creditworthiness_score >= 70) {
        $scoreClass = $successColor;
        $scoreText = 'Good';
    } elseif ($inputCredit->creditworthiness_score >= 50) {
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
    $pdf->Cell($circleRadius * 2, 10, $inputCredit->creditworthiness_score, 0, 1, 'C');
    
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
    $pdf->Cell(80, 6, 'Input Repayment History', 1, 0, 'L');
    $pdf->Cell(30, 6, $creditScores['input_repayment_history'] . '/100', 1, 0, 'C');
    $pdf->Cell(20, 6, '30%', 1, 1, 'C');
    
    $pdf->SetXY(70, $pdf->GetY());
    $pdf->Cell(80, 6, 'Financial Obligations', 1, 0, 'L');
    $pdf->Cell(80, 6, 'Financial Obligations', 1, 0, 'L');
    $pdf->Cell(30, 6, $creditScores['financial_obligations'] . '/100', 1, 0, 'C');
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
    $pdf->MultiCell(0, 5, 'The creditworthiness score is determined by four key factors: Input Repayment History (30%), Financial Obligations (25%), Produce History (35%), and Amount Ratio (10%). A score of 70 or higher is considered good for input credit approval.', 0, 'L');

    // ===== REPAYMENT HISTORY SECTION =====
    $pdf->Ln(10);
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
    $pdf->Cell(0, 10, 'REPAYMENT HISTORY', 0, 1, 'L');
    $pdf->Ln(2);
    
    // Only show repayment history if the input credit is fulfilled or completed
    if ($inputCredit->status == 'fulfilled' || $inputCredit->status == 'completed') {
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
            $runningBalance = $inputCredit->total_with_interest;
            
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
                $paymentDate = date('M d, Y', strtotime($payment->deduction_date));
                
                $pdf->Cell(30, 7, $paymentDate, 1, 0, 'C', $fillColor);
                $pdf->Cell(45, 7, $payment->product_name, 1, 0, 'L', $fillColor);
                $pdf->Cell(35, 7, 'KES ' . number_format($payment->produce_sale_amount, 2), 1, 0, 'R', $fillColor);
                $pdf->Cell(40, 7, 'KES ' . number_format($payment->amount, 2), 1, 0, 'R', $fillColor);
                $pdf->Cell(30, 7, 'KES ' . number_format($runningBalance, 2), 1, 1, 'R', $fillColor);
            }
            
            // Add totals row
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->SetFillColor($secondaryColor[0], $secondaryColor[1], $secondaryColor[2]);
            $pdf->SetTextColor(255, 255, 255);
            
            $pdf->Cell(110, 8, 'TOTAL REPAID', 1, 0, 'R', true);
            $pdf->Cell(40, 8, 'KES ' . number_format($paidAmount, 2), 1, 0, 'R', true);
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
            
        } else {
            // No repayment records yet
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->SetFillColor(245, 245, 245);
            $pdf->Cell(0, 10, 'No repayment records found for this input credit.', 1, 1, 'C', true);
            $pdf->Ln(5);
            
            if ($inputCredit->fulfillment_date) {
                $pdf->MultiCell(0, 6, 'Inputs were delivered on ' . date('F d, Y', strtotime($inputCredit->fulfillment_date)) . 
                              '. Repayments will be automatically deducted from produce sales.', 0, 'L');
            }
        }
    } else {
        // Input credit not yet fulfilled
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', 'I', 10);
        $pdf->SetFillColor(245, 245, 245);
        
        if ($inputCredit->status == 'approved') {
            $message = 'Input credit has been approved but not yet fulfilled. Repayment history will be available after fulfillment.';
        } else {
            $message = 'Input credit has not yet been fulfilled. Repayment history will be available after approval and fulfillment.';
        }
        
        $pdf->Cell(0, 10, $message, 1, 1, 'C', true);
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
    $termsText = "1. This input credit is subject to a " . $inputCredit->credit_percentage . "% interest rate.\n";
    $termsText .= "2. Repayments will be automatically deducted from produce sales at a rate of " . $inputCredit->repayment_percentage . "%.\n";
    $termsText .= "3. Agricultural inputs must be used as intended for farming activities.\n";
    $termsText .= "4. Early repayment is allowed without any penalty.\n";
    $termsText .= "5. Default on repayment may affect future input credit eligibility.\n";
    $termsText .= "6. The agrovet reserves the right to adjust terms as per the input credit agreement.";
    
    $pdf->MultiCell(0, 5, $termsText, 0, 'L');
    
    // ===== IMPORTANT INFORMATION =====
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColor(245, 245, 245);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(0, 8, 'IMPORTANT INFORMATION', 1, 1, 'L', true);
    
    $pdf->SetFont('helvetica', '', 9);
    $pdf->MultiCell(0, 5, "This statement provides a summary of your input credit application and its current status. For inquiries or any discrepancies, please contact our office at support@dfcs.com or call +254 700 000 000 within 7 days of receiving this statement.", 1, 'L');
    
    // ===== QR CODE AND DOCUMENT AUTHENTICATION =====
    $pdf->Ln(10);
    
    // Add QR code with document reference
    $pdf->write2DBarcode(
        'Reference #INPCR' . str_pad($inputCreditId, 5, '0', STR_PAD_LEFT) . 
        "\nFarmer: " . $inputCredit->farmer_name . 
        "\nAmount: KES " . number_format($inputCredit->total_amount, 2) .
        "\nStatus: " . ucfirst(str_replace('_', ' ', $inputCredit->status)),
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
    $pdf->Output('Input_Credit_Statement_INPCR' . str_pad($inputCreditId, 5, '0', STR_PAD_LEFT) . '.pdf', 'I');
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