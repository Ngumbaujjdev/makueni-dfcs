<?php
include "../../config/config.php";
include "../../libs/App.php";

// Verify deduction ID was provided
if (isset($_POST['deductionId'])) {
    $deduction_id = intval($_POST['deductionId']);
    $app = new App;
    
    // Query to get detailed information about the deduction
    $query = "SELECT 
                icr.id as deduction_id,
                icr.approved_credit_id,
                icr.produce_delivery_id,
                icr.produce_sale_amount,
                icr.deducted_amount,
                icr.deduction_date,
                aic.credit_application_id,
                aic.repayment_percentage,
                aic.total_with_interest,
                aic.remaining_balance,
                aic.approved_amount,
                aic.credit_percentage as interest_rate,
                CONCAT('INPT', LPAD(ica.id, 5, '0')) as credit_reference,
                CONCAT('DLVR', LPAD(pd.id, 5, '0')) as delivery_reference,
                pd.delivery_date,
                pd.quantity as delivery_quantity,
                pd.unit_price as delivery_price,
                pd.status as delivery_status,
                pd.quality_grade,
                CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                u.phone as farmer_phone,
                u.email as farmer_email,
                fm.registration_number as farmer_reg,
                a.id as agrovet_id,
                a.name as agrovet_name,
                a.location as agrovet_location,
                a.phone as agrovet_phone,
                a.email as agrovet_email,
                a.address as agrovet_address,
                a.license_number as agrovet_license,
                pt.name as product_name,
                pt.measurement_unit
              FROM input_credit_repayments icr
              JOIN approved_input_credits aic ON icr.approved_credit_id = aic.id
              JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
              JOIN farmers fm ON ica.farmer_id = fm.id
              JOIN users u ON fm.user_id = u.id
              JOIN agrovets a ON ica.agrovet_id = a.id
              JOIN produce_deliveries pd ON icr.produce_delivery_id = pd.id
              LEFT JOIN farm_products fp ON pd.farm_product_id = fp.id
              LEFT JOIN product_types pt ON fp.product_type_id = pt.id
              WHERE icr.id = " . $deduction_id;

    $deduction = $app->select_one($query);
    
    if ($deduction) {
        // Get input items for this credit
        $items_query = "SELECT 
                    input_type,
                    input_name,
                    quantity,
                    unit,
                    unit_price,
                    total_price
                FROM input_credit_items
                WHERE credit_application_id = " . $deduction->credit_application_id;
        
        $items = $app->select_all($items_query);
        
        // Format dates
        $deduction_date = date('d M Y', strtotime($deduction->deduction_date));
        $delivery_date = date('d M Y', strtotime($deduction->delivery_date));
        $current_date = date('d M Y H:i:s');
        
        // Generate receipt HTML
        $receipt_html = '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Input Credit Deduction Receipt | DCT-' . $deduction->deduction_id . '</title>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <style>
                :root {
                    --primary-color: #6AA32D;
                    --primary-light: rgba(106, 163, 45, 0.1);
                    --text-gray: #6c757d;
                }
                
                body {
                    font-family: "Segoe UI", Arial, sans-serif;
                    padding: 0;
                    margin: 0;
                    color: #333;
                    background-color: #f8f9fa;
                }
                
                .receipt-container {
                    max-width: 850px;
                    margin: 20px auto;
                    background: white;
                    box-shadow: 0 0 25px rgba(0, 0, 0, 0.1);
                    border-radius: 8px;
                    overflow: hidden;
                }
                
                .receipt-header {
                    background-color: var(--primary-color);
                    color: white;
                    padding: 20px;
                    text-align: center;
                    position: relative;
                }
                
                .logo-container {
                    text-align: center;
                    margin-bottom: 15px;
                }
                
                .logo-container img {
                    height: 80px;
                    width: auto;
                }
                
                .receipt-title {
                    font-size: 24px;
                    font-weight: 600;
                    margin: 0;
                }
                
                .receipt-subtitle {
                    font-size: 16px;
                    opacity: 0.9;
                    margin-top: 5px;
                }
                
                .receipt-meta {
                    display: flex;
                    justify-content: space-between;
                    padding: 12px 20px;
                    background-color: var(--primary-light);
                    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
                }
                
                .receipt-meta-item {
                    font-size: 14px;
                }
                
                .meta-label {
                    font-weight: 600;
                    margin-right: 5px;
                    color: var(--primary-color);
                }
                
                .receipt-body {
                    padding: 20px;
                }
                
                .info-section {
                    margin-bottom: 25px;
                }
                
                .section-title {
                    color: var(--primary-color);
                    font-size: 18px;
                    font-weight: 600;
                    margin-bottom: 15px;
                    padding-bottom: 8px;
                    border-bottom: 2px solid var(--primary-light);
                }
                
                .info-card {
                    background-color: #fff;
                    border-radius: 6px;
                    border: 1px solid rgba(0, 0, 0, 0.08);
                    padding: 15px;
                    height: 100%;
                }
                
                .info-card strong {
                    color: #444;
                }
                
                .info-card i {
                    color: var(--primary-color);
                    width: 20px;
                    text-align: center;
                    margin-right: 8px;
                }
                
                .info-card p {
                    margin-bottom: 8px;
                }
                
                .summary-box {
                    background-color: var(--primary-light);
                    border-radius: 6px;
                    padding: 15px;
                    margin-top: 20px;
                }
                
                .summary-item {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 10px;
                }
                
                .summary-total {
                    font-weight: bold;
                    font-size: 16px;
                    border-top: 2px solid var(--primary-color);
                    padding-top: 10px;
                    margin-top: 10px;
                    color: var(--primary-color);
                }
                
                .receipt-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 20px 0;
                }
                
                .receipt-table th {
                    background-color: var(--primary-light);
                    color: var(--primary-color);
                    font-weight: 600;
                    text-align: left;
                    padding: 12px 15px;
                }
                
                .receipt-table td {
                    padding: 10px 15px;
                    border-bottom: 1px solid #eee;
                }
                
                .receipt-table tr:last-child td {
                    border-bottom: none;
                }
                
                .receipt-table tr:nth-child(even) {
                    background-color: #f9f9f9;
                }
                
                .badge-light {
                    background-color: #f8f9fa;
                    color: #495057;
                    padding: 4px 8px;
                    border-radius: 4px;
                    font-size: 12px;
                    border: 1px solid #ddd;
                }
                
                .progress {
                    height: 12px;
                    border-radius: 6px;
                    background-color: #e9ecef;
                    overflow: hidden;
                }
                
                .progress-bar {
                    background-color: var(--primary-color);
                    height: 100%;
                }
                
                .progress-text {
                    text-align: center;
                    font-size: 13px;
                    color: var(--text-gray);
                    margin-top: 5px;
                }
                
                .signature-section {
                    margin-top: 40px;
                    padding-top: 20px;
                    border-top: 1px dashed #ddd;
                }
                
                .signature-line {
                    border-top: 1px solid #ddd;
                    width: 80%;
                    margin: 15px auto 5px;
                }
                
                .signature-name {
                    font-size: 14px;
                    color: var(--text-gray);
                }
                
                .receipt-footer {
                    background-color: #f8f9fa;
                    text-align: center;
                    padding: 20px;
                    border-top: 1px solid #eee;
                }
                
                .qr-code {
                    width: 100px;
                    height: 100px;
                    background-color: #fff;
                    margin: 0 auto 15px;
                    padding: 5px;
                    border: 1px solid #ddd;
                }
                
                .qr-code img {
                    width: 100%;
                    height: 100%;
                }
                
                .footer-note {
                    font-size: 13px;
                    color: var(--text-gray);
                    margin-bottom: 5px;
                }
                
                .watermark {
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%) rotate(-45deg);
                    font-size: 100px;
                    color: rgba(106, 163, 45, 0.03);
                    pointer-events: none;
                    z-index: -1;
                    font-weight: bold;
                    white-space: nowrap;
                }
                
                @media print {
                    body {
                        background-color: white;
                    }
                    
                    .receipt-container {
                        box-shadow: none;
                        margin: 0;
                        max-width: 100%;
                    }
                    
                    .no-print {
                        display: none !important;
                    }
                }
            </style>
        </head>
        <body>
            <div class="watermark">DEDUCTION RECEIPT</div>
            
            <div class="receipt-container">
                <div class="receipt-header">
                    <div class="logo-container">
                        <img src="http://localhost/dfcs/assets/images/brand-logos/logo3.png" alt="Company Logo">
                    </div>
                    <h1 class="receipt-title">Input Credit Deduction Receipt</h1>
                    <p class="receipt-subtitle">Official payment receipt for input credit deduction</p>
                </div>
                
                <div class="receipt-meta">
                    <div class="receipt-meta-item">
                        <span class="meta-label">Receipt No:</span>
                        <span>DCT-' . $deduction->deduction_id . '</span>
                    </div>
                    <div class="receipt-meta-item">
                        <span class="meta-label">Date:</span>
                        <span>' . $deduction_date . '</span>
                    </div>
                    <div class="receipt-meta-item">
                        <span class="meta-label">Status:</span>
                        <span><span class="badge bg-success">Completed</span></span>
                    </div>
                </div>
                
                <div class="receipt-body">
                    <div class="row info-section">
                        <div class="col-md-6 mb-3">
                            <h3 class="section-title">
                                <i class="fas fa-store me-2"></i>Agrovet Information
                            </h3>
                            <div class="info-card">
                                <p><strong>' . htmlspecialchars($deduction->agrovet_name) . '</strong></p>
                                <p><i class="fas fa-map-marker-alt"></i>' . htmlspecialchars($deduction->agrovet_location) . '</p>
                                <p><i class="fas fa-phone"></i>' . htmlspecialchars($deduction->agrovet_phone) . '</p>
                                ' . ($deduction->agrovet_email ? '<p><i class="fas fa-envelope"></i>' . htmlspecialchars($deduction->agrovet_email) . '</p>' : '') . '
                                <p><i class="fas fa-id-card"></i>License: ' . htmlspecialchars($deduction->agrovet_license) . '</p>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <h3 class="section-title">
                                <i class="fas fa-user me-2"></i>Farmer Information
                            </h3>
                            <div class="info-card">
                                <p><strong>' . htmlspecialchars($deduction->farmer_name) . '</strong></p>
                                <p><i class="fas fa-id-card"></i>Reg #: ' . htmlspecialchars($deduction->farmer_reg) . '</p>
                                <p><i class="fas fa-phone"></i>' . htmlspecialchars($deduction->farmer_phone) . '</p>
                                ' . ($deduction->farmer_email ? '<p><i class="fas fa-envelope"></i>' . htmlspecialchars($deduction->farmer_email) . '</p>' : '') . '
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-section">
                        <h3 class="section-title">
                            <i class="fas fa-money-bill-wave me-2"></i>Deduction Details
                        </h3>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <p><strong>Credit Reference:</strong> <span class="badge badge-light">' . $deduction->credit_reference . '</span></p>
                                    <p><strong>Sale Reference:</strong> <span class="badge badge-light">' . $deduction->delivery_reference . '</span></p>
                                    <p><strong>Deduction Date:</strong> <i class="fas fa-calendar text-muted"></i> ' . $deduction_date . '</p>
                                    <p><strong>Deduction Rate:</strong> <i class="fas fa-percentage text-muted"></i> ' . $deduction->repayment_percentage . '%</p>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="info-card">
                                    <p><strong>Product:</strong> ' . htmlspecialchars($deduction->product_name ?? 'Produce') . '</p>
                                    <p><strong>Quantity:</strong> ' . $deduction->delivery_quantity . ' ' . htmlspecialchars($deduction->measurement_unit ?? 'units') . '</p>
                                    <p><strong>Unit Price:</strong> KES ' . number_format($deduction->delivery_price, 2) . '</p>
                                    <p><strong>Quality Grade:</strong> ' . ($deduction->quality_grade ? 'Grade ' . $deduction->quality_grade : 'N/A') . '</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="summary-box">
                            <div class="summary-item">
                                <span>Total Sale Amount:</span>
                                <span>KES ' . number_format($deduction->produce_sale_amount, 2) . '</span>
                            </div>
                            <div class="summary-item">
                                <span>Deducted Amount:</span>
                                <span>KES ' . number_format($deduction->deducted_amount, 2) . '</span>
                            </div>
                            <div class="summary-item summary-total">
                                <span>Amount Paid to Farmer:</span>
                                <span>KES ' . number_format($deduction->produce_sale_amount - $deduction->deducted_amount, 2) . '</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-section">
                        <h3 class="section-title">
                            <i class="fas fa-credit-card me-2"></i>Input Credit Status
                        </h3>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <p><strong>Original Amount:</strong> KES ' . number_format($deduction->approved_amount, 2) . '</p>
                                    <p><strong>Interest Rate:</strong> ' . $deduction->interest_rate . '%</p>
                                    <p><strong>Total With Interest:</strong> KES ' . number_format($deduction->total_with_interest, 2) . '</p>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="info-card">
                                    <p><strong>Amount Paid Before:</strong> KES ' . number_format($deduction->total_with_interest - $deduction->remaining_balance - $deduction->deducted_amount, 2) . '</p>
                                    <p><strong>This Deduction:</strong> KES ' . number_format($deduction->deducted_amount, 2) . '</p>
                                    <p><strong>Remaining Balance:</strong> KES ' . number_format($deduction->remaining_balance, 2) . '</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <div class="progress">
                                <div class="progress-bar" style="width: ' . round((($deduction->total_with_interest - $deduction->remaining_balance) / $deduction->total_with_interest) * 100) . '%;"></div>
                            </div>
                            <p class="progress-text">Repayment Progress: ' . round((($deduction->total_with_interest - $deduction->remaining_balance) / $deduction->total_with_interest) * 100) . '% Complete</p>
                        </div>
                    </div>';
        
        // Show input items if available
        if ($items && count($items) > 0) {
            $receipt_html .= '
                    <div class="info-section">
                        <h3 class="section-title">
                            <i class="fas fa-list me-2"></i>Input Items
                        </h3>
                        <table class="receipt-table">
                            <thead>
                                <tr>
                                    <th>Input Type</th>
                                    <th>Name</th>
                                    <th>Quantity</th>
                                    <th>Unit</th>
                                    <th>Unit Price</th>
                                    <th>Total Price</th>
                                </tr>
                            </thead>
                            <tbody>';
            
            $items_total = 0;
            foreach ($items as $item) {
                $items_total += $item->total_price;
                
                // Set icon based on input type
                switch($item->input_type) {
                    case 'fertilizer':
                        $icon = '<i class="fas fa-fill-drip me-1" style="color:#6AA32D;"></i> ';
                        break;
                    case 'seeds':
                        $icon = '<i class="fas fa-seedling me-1" style="color:#6AA32D;"></i> ';
                        break;
                    case 'pesticide':
                        $icon = '<i class="fas fa-spray-can me-1" style="color:#6AA32D;"></i> ';
                        break;
                    case 'tools':
                        $icon = '<i class="fas fa-tools me-1" style="color:#6AA32D;"></i> ';
                        break;
                    default:
                        $icon = '<i class="fas fa-box me-1" style="color:#6AA32D;"></i> ';
                }
                
                $receipt_html .= '
                                <tr>
                                    <td>' . $icon . ucfirst(htmlspecialchars($item->input_type)) . '</td>
                                    <td>' . htmlspecialchars($item->input_name) . '</td>
                                    <td>' . $item->quantity . '</td>
                                    <td>' . ucfirst(htmlspecialchars($item->unit)) . '</td>
                                    <td>KES ' . number_format($item->unit_price, 2) . '</td>
                                    <td>KES ' . number_format($item->total_price, 2) . '</td>
                                </tr>';
            }
            
            $receipt_html .= '
                                <tr>
                                    <td colspan="5" style="text-align: right;"><strong>Total:</strong></td>
                                    <td><strong>KES ' . number_format($items_total, 2) . '</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>';
        }
        
        $receipt_html .= '
                    <div class="signature-section">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="signature-line"></div>
                                <p class="signature-name">Farmer\'s Signature</p>
                            </div>
                            <div class="col-md-4">
                                <div class="signature-line"></div>
                                <p class="signature-name">Agrovet Representative</p>
                            </div>
                            <div class="col-md-4">
                                <div class="signature-line"></div>
                                <p class="signature-name">Bank Officer</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                                    <div class="receipt-footer">
                    <div class="qr-code">
                        <img src="data:image/png;base64,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" alt="QR Code">
                    </div>
                    <p class="footer-note">Receipt ID: DCT-' . $deduction->deduction_id . '</p>
                    <p class="footer-note">Generated on: ' . $current_date . '</p>
                    <p class="footer-note mb-3">This is an official receipt for input credit deduction.</p>
                    <p class="footer-note">
                        <strong>For any inquiries, please contact your bank branch or agrovet.</strong>
                    </p>
                </div>
            </div>
            
            <!-- Print & Close buttons - only visible on screen, not when printing -->
            <div class="text-center mt-4 mb-5 no-print">
                <button class="btn btn-success me-2" onclick="window.print();">
                    <i class="fas fa-print me-2"></i> Print Receipt
                </button>
                <button class="btn btn-secondary" onclick="window.close();">
                    <i class="fas fa-times me-2"></i> Close
                </button>
            </div>
        </body>
        </html>';
        
        echo $receipt_html;
    } else {
        // If deduction not found, show error
        echo '
        <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Error:</strong> Deduction record not found. Please verify the deduction ID and try again.
        </div>
        <div class="text-center mt-4">
            <button class="btn btn-secondary" onclick="window.close();">
                <i class="fas fa-times me-2"></i> Close
            </button>
        </div>';
    }
} else {
    // If no deduction ID provided, show error
    echo '
    <div class="alert alert-danger" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Error:</strong> No deduction ID provided. Please try again.
    </div>
    <div class="text-center mt-4">
        <button class="btn btn-secondary" onclick="window.close();">
            <i class="fas fa-times me-2"></i> Close
            </button>
    </div>';
}
?>