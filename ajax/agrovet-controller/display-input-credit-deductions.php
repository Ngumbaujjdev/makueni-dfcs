<?php
include "../../config/config.php";
include "../../libs/App.php";

if (isset($_POST['displayInputCreditDeductions'])):
    $app = new App;
    
    // Query to get all input credit deductions with farmer, agrovet, and credit details
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
                CONCAT('INPT', LPAD(ica.id, 5, '0')) as credit_reference,
                CONCAT('DLVR', LPAD(pd.id, 5, '0')) as delivery_reference,
                pd.delivery_date,
                pd.quantity as delivery_quantity,
                pd.unit_price as delivery_price,
                pd.status as delivery_status,
                CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                fm.registration_number as farmer_reg,
                a.id as agrovet_id,
                a.name as agrovet_name,
                a.location as agrovet_location
              FROM input_credit_repayments icr
              JOIN approved_input_credits aic ON icr.approved_credit_id = aic.id
              JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
              JOIN farmers fm ON ica.farmer_id = fm.id
              JOIN users u ON fm.user_id = u.id
              JOIN agrovets a ON ica.agrovet_id = a.id
              JOIN produce_deliveries pd ON icr.produce_delivery_id = pd.id
              ORDER BY icr.deduction_date DESC";

    $deductions = $app->select_all($query);
?>
<div class="card custom-card shadow-sm">
    <div class="card-header d-flex justify-content-between" style="background-color: #6AA32D; color: white;">
        <div class="card-title d-flex align-items-center">
            <i class="fa-solid fa-money-bill-wave me-2"></i> Input Credit Deductions
            <span class="badge bg-white text-success ms-2">
                <?php echo count($deductions); ?> deductions
            </span>
            <span class="badge bg-white text-success ms-2">
                KES <?php 
                    $total_amount = 0;
                    if ($deductions) {
                        foreach ($deductions as $deduction) {
                            $total_amount += $deduction->deducted_amount;
                        }
                    }
                    echo number_format($total_amount, 2);
                ?>
            </span>
        </div>
        <div>
            <button class="btn btn-sm btn-light" onclick="loadActiveDeductions()">
                <i class="fa-solid fa-sync-alt me-1"></i> Refresh
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="datatable-deductions" class="table table-bordered text-nowrap w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Farmer</th>
                        <th>Agrovet</th>
                        <th>Sale Reference</th>
                        <th>Sale Amount</th>
                        <th>Deduction</th>
                        <th>Rate %</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($deductions && count($deductions) > 0): ?>
                    <?php foreach ($deductions as $deduction): ?>
                    <?php
                    // Format date
                    $deduction_date = date('d M Y', strtotime($deduction->deduction_date));
                    
                    // Calculate deduction percentage
                    $deduction_percentage = ($deduction->produce_sale_amount > 0) ? 
                        round(($deduction->deducted_amount / $deduction->produce_sale_amount) * 100, 1) : 0;
                    
                    // Determine status based on delivery status
                    $status = $deduction->delivery_status;
                    $status_badge = '';
                    
                    switch($status) {
                        case 'paid':
                            $status_badge = '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Paid</span>';
                            break;
                        case 'verified':
                        case 'sold':
                            $status_badge = '<span class="badge bg-warning"><i class="fas fa-clock me-1"></i>Pending</span>';
                            break;
                        default:
                            $status_badge = '<span class="badge bg-secondary"><i class="fas fa-info-circle me-1"></i>' . ucfirst($status) . '</span>';
                    }
                    ?>
                    <tr>
                        <td><span class="badge bg-light text-dark">DCT-<?php echo $deduction->deduction_id; ?></span>
                        </td>
                        <td>
                            <i class="fas fa-calendar-day me-1 text-success"></i>
                            <?php echo $deduction_date; ?>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs avatar-rounded bg-light me-2">
                                    <i class="fa-solid fa-user" style="color: #6AA32D;"></i>
                                </span>
                                <div>
                                    <span class="fw-medium d-block">
                                        <?php echo htmlspecialchars($deduction->farmer_name) ?>
                                    </span>
                                    <small
                                        class="text-muted"><?php echo htmlspecialchars($deduction->farmer_reg) ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs avatar-rounded bg-light me-2">
                                    <i class="fa-solid fa-store" style="color: #6AA32D;"></i>
                                </span>
                                <div>
                                    <span
                                        class="d-block"><?php echo htmlspecialchars($deduction->agrovet_name) ?></span>
                                    <small
                                        class="text-muted"><?php echo htmlspecialchars($deduction->agrovet_location) ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark">
                                <?php echo htmlspecialchars($deduction->delivery_reference); ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs avatar-rounded bg-light me-2">
                                    <i class="fa-solid fa-shopping-cart" style="color: #6AA32D;"></i>
                                </span>
                                <span>KES <?php echo number_format($deduction->produce_sale_amount, 2) ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs avatar-rounded bg-light me-2">
                                    <i class="fa-solid fa-money-bill-wave" style="color: #6AA32D;"></i>
                                </span>
                                <span class="fw-semibold" style="color: #6AA32D;">KES
                                    <?php echo number_format($deduction->deducted_amount, 2) ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="progress me-2" style="width: 50px; height: 6px;">
                                    <div class="progress-bar bg-success"
                                        style="width: <?php echo $deduction_percentage; ?>%;" role="progressbar">
                                    </div>
                                </div>
                                <span><?php echo $deduction->repayment_percentage; ?>%</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <?php echo $status_badge; ?>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-success view-deduction-details"
                                    data-id="<?php echo $deduction->deduction_id ?>"
                                    onclick="toggleDeductionDetails(<?php echo $deduction->deduction_id ?>)">
                                    <i class="fa-solid fa-eye me-1"></i> View
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-success"
                                    onclick="printDeductionReceipt(<?php echo $deduction->deduction_id ?>)">
                                    <i class="fa-solid fa-print me-1"></i> Print
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Deduction Details Row -->
                    <tr class="deduction-detail-row" id="deduction-details-<?php echo $deduction->deduction_id; ?>"
                        style="display: none; background-color: rgba(106, 163, 45, 0.05);">
                        <td colspan="10" class="p-0">
                            <div class="card m-3 border-0 shadow-sm">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-info-circle me-2" style="color:#6AA32D;"></i>
                                        <span style="color:#6AA32D;" class="fw-medium">Deduction Details</span>
                                    </div>
                                    <button type="button" class="btn-close"
                                        onclick="toggleDeductionDetails(<?php echo $deduction->deduction_id ?>)"></button>
                                </div>
                                <div class="card-body pt-3">
                                    <div class="row">
                                        <!-- Left Column: Deduction Details -->
                                        <div class="col-md-6">
                                            <h6 class="mb-3 p-2 bg-light rounded" style="color:#6AA32D;">
                                                <i class="fas fa-money-bill-wave me-2"></i>Deduction Information
                                            </h6>
                                            <table class="table table-sm table-borderless mb-0">
                                                <tbody>
                                                    <tr>
                                                        <td style="width: 40%;" class="fw-medium">Deduction ID:</td>
                                                        <td>
                                                            <span class="badge bg-light text-dark px-2">
                                                                DCT-<?php echo $deduction->deduction_id ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-medium">Deduction Date:</td>
                                                        <td>
                                                            <i class="fas fa-calendar-alt me-1 text-success"></i>
                                                            <?php echo $deduction_date ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-medium">Deduction Rate:</td>
                                                        <td>
                                                            <i class="fas fa-percentage me-1 text-success"></i>
                                                            <?php echo $deduction->repayment_percentage ?>%
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-medium">Sale Amount:</td>
                                                        <td>
                                                            <i class="fas fa-shopping-cart me-1 text-success"></i>
                                                            KES
                                                            <?php echo number_format($deduction->produce_sale_amount, 2) ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-medium">Deducted Amount:</td>
                                                        <td>
                                                            <span class="fw-semibold" style="color:#6AA32D;">
                                                                <i class="fas fa-money-bill-wave me-1"></i>
                                                                KES
                                                                <?php echo number_format($deduction->deducted_amount, 2) ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-medium">Actual Percentage:</td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="progress me-2"
                                                                    style="width: 60px; height: 6px;">
                                                                    <div class="progress-bar bg-success"
                                                                        style="width: <?php echo $deduction_percentage; ?>%;"
                                                                        role="progressbar">
                                                                    </div>
                                                                </div>
                                                                <span><?php echo $deduction_percentage; ?>%</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Right Column: Credit Details -->
                                        <div class="col-md-6">
                                            <h6 class="mb-3 p-2 bg-light rounded" style="color:#6AA32D;">
                                                <i class="fas fa-credit-card me-2"></i>Credit Information
                                            </h6>
                                            <?php
                                            // Query to get input credit information
                                            $credit_query = "SELECT 
                                                        aic.approved_amount,
                                                        aic.credit_percentage as interest_rate,
                                                        aic.total_with_interest,
                                                        aic.remaining_balance,
                                                        aic.fulfillment_date,
                                                        (aic.total_with_interest - aic.remaining_balance) as paid_amount,
                                                        ROUND((aic.total_with_interest - aic.remaining_balance) / aic.total_with_interest * 100, 1) as overall_repayment_percentage
                                                    FROM approved_input_credits aic
                                                    WHERE aic.id = " . $deduction->approved_credit_id;
                                            
                                            $credit_info = $app->select_one($credit_query);
                                            
                                            if ($credit_info) {
                                                // Calculate repayment progress class
                                                if ($credit_info->overall_repayment_percentage >= 75) {
                                                    $overall_progress_class = 'bg-success';
                                                } elseif ($credit_info->overall_repayment_percentage >= 50) {
                                                    $overall_progress_class = 'bg-info';
                                                } elseif ($credit_info->overall_repayment_percentage >= 25) {
                                                    $overall_progress_class = 'bg-warning';
                                                } else {
                                                    $overall_progress_class = 'bg-danger';
                                                }
                                            ?>
                                            <table class="table table-sm table-borderless mb-0">
                                                <tbody>
                                                    <tr>
                                                        <td style="width: 40%;" class="fw-medium">Credit Reference:</td>
                                                        <td>
                                                            <span class="badge bg-light text-dark px-2">
                                                                <?php echo $deduction->credit_reference ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-medium">Original Amount:</td>
                                                        <td>
                                                            <i class="fas fa-tag me-1 text-success"></i>
                                                            KES
                                                            <?php echo number_format($credit_info->approved_amount, 2) ?>
                                                            <span class="badge bg-light text-dark ms-1">
                                                                <i
                                                                    class="fas fa-percentage me-1"></i><?php echo $credit_info->interest_rate ?>%
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-medium">Total With Interest:</td>
                                                        <td>
                                                            <i class="fas fa-coins me-1 text-success"></i>
                                                            KES
                                                            <?php echo number_format($credit_info->total_with_interest, 2) ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-medium">Total Paid Amount:</td>
                                                        <td>
                                                            <i class="fas fa-check-circle me-1 text-success"></i>
                                                            KES
                                                            <?php echo number_format($credit_info->paid_amount, 2) ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-medium">Remaining Balance:</td>
                                                        <td>
                                                            <span class="fw-semibold">
                                                                <i class="fas fa-balance-scale me-1"></i>
                                                                KES
                                                                <?php echo number_format($credit_info->remaining_balance, 2) ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-medium">Overall Repayment:</td>
                                                        <td>
                                                            <div class="d-flex flex-column">
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <span
                                                                        class="fs-12"><?php echo $credit_info->overall_repayment_percentage ?>%
                                                                        Complete</span>
                                                                </div>
                                                                <div class="progress" style="height: 6px;">
                                                                    <div class="progress-bar <?php echo $overall_progress_class ?>"
                                                                        style="width: <?php echo $credit_info->overall_repayment_percentage ?>%;"
                                                                        role="progressbar">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <?php } else { ?>
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Credit information not available.
                                            </div>
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <!-- Produce Sale Details -->
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <h6 class="mb-3 p-2 bg-light rounded" style="color:#6AA32D;">
                                                <i class="fas fa-shopping-basket me-2"></i>Produce Sale Details
                                            </h6>

                                            <?php
                                            // Query to get produce delivery details
                                            $produce_query = "SELECT 
                                                        pd.id as delivery_id,
                                                        pd.farm_product_id,
                                                        pd.quantity,
                                                        pd.unit_price,
                                                        pd.total_value,
                                                        pd.quality_grade,
                                                        pd.delivery_date,
                                                        pd.received_by,
                                                        pd.status,
                                                        CONCAT(u.first_name, ' ', u.last_name) as received_by_name,
                                                        fp.product_type_id,
                                                        pt.name as product_name,
                                                        pt.measurement_unit
                                                    FROM produce_deliveries pd
                                                    LEFT JOIN users u ON pd.received_by = u.id
                                                    LEFT JOIN farm_products fp ON pd.farm_product_id = fp.id
                                                    LEFT JOIN product_types pt ON fp.product_type_id = pt.id
                                                    WHERE pd.id = " . $deduction->produce_delivery_id;
                                            
                                            $produce_info = $app->select_one($produce_query);
                                            ?>

                                            <div class="card border-0 bg-light">
                                                <div class="card-body p-3">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <table class="table table-sm table-borderless mb-0">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width: 40%;" class="fw-medium">
                                                                            Delivery Reference:</td>
                                                                        <td>
                                                                            <span class="badge bg-light text-dark px-2">
                                                                                <?php echo $deduction->delivery_reference ?>
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="fw-medium">Delivery Date:</td>
                                                                        <td>
                                                                            <i
                                                                                class="fas fa-calendar-alt me-1 text-success"></i>
                                                                            <?php echo date('d M Y', strtotime($deduction->delivery_date)) ?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="fw-medium">Product:</td>
                                                                        <td>
                                                                            <?php if ($produce_info && $produce_info->product_name): ?>
                                                                            <i class="fas fa-tag me-1 text-success"></i>
                                                                            <?php echo htmlspecialchars($produce_info->product_name) ?>
                                                                            <?php else: ?>
                                                                            <span class="text-muted">Unknown
                                                                                product</span>
                                                                            <?php endif; ?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="fw-medium">Status:</td>
                                                                        <td>
                                                                            <?php echo $status_badge ?>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <table class="table table-sm table-borderless mb-0">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width: 40%;" class="fw-medium">
                                                                            Quantity:</td>
                                                                        <td>
                                                                            <i
                                                                                class="fas fa-weight me-1 text-success"></i>
                                                                            <?php if ($produce_info): ?>
                                                                            <?php echo $produce_info->quantity ?>
                                                                            <?php echo $produce_info->measurement_unit ? $produce_info->measurement_unit : 'units' ?>
                                                                            <?php else: ?>
                                                                            <?php echo $deduction->delivery_quantity ?>
                                                                            units
                                                                            <?php endif; ?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="fw-medium">Unit Price:</td>
                                                                        <td>
                                                                            <i class="fas fa-tag me-1 text-success"></i>
                                                                            KES
                                                                            <?php echo number_format($deduction->delivery_price, 2) ?>
                                                                            per
                                                                            <?php echo $produce_info && $produce_info->measurement_unit ? $produce_info->measurement_unit : 'unit' ?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="fw-medium">Total Value:</td>
                                                                        <td>
                                                                            <i
                                                                                class="fas fa-money-bill me-1 text-success"></i>
                                                                            KES
                                                                            <?php echo number_format($deduction->produce_sale_amount, 2) ?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="fw-medium">Quality Grade:</td>
                                                                        <td>
                                                                            <?php if ($produce_info && $produce_info->quality_grade): ?>
                                                                            <span class="badge bg-light text-dark px-2">
                                                                                Grade
                                                                                <?php echo $produce_info->quality_grade ?>
                                                                            </span>
                                                                            <?php else: ?>
                                                                            <span class="text-muted">Not
                                                                                specified</span>
                                                                            <?php endif; ?>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Deduction Summary -->
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <div class="card bg-success text-white shadow-sm">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-4 text-center border-end">
                                                            <h6 class="mb-1">Total Sale Amount</h6>
                                                            <h4 class="mb-0">KES
                                                                <?php echo number_format($deduction->produce_sale_amount, 2) ?>
                                                            </h4>
                                                        </div>
                                                        <div class="col-md-4 text-center border-end">
                                                            <h6 class="mb-1">Deducted Amount</h6>
                                                            <h4 class="mb-0">KES
                                                                <?php echo number_format($deduction->deducted_amount, 2) ?>
                                                            </h4>
                                                        </div>
                                                        <div class="col-md-4 text-center">
                                                            <h6 class="mb-1">Paid to Farmer</h6>
                                                            <h4 class="mb-0">KES
                                                                <?php echo number_format($deduction->produce_sale_amount - $deduction->deducted_amount, 2) ?>
                                                            </h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="row mt-4">
                                        <div class="col-12 text-center">
                                            <button type="button" class="btn btn-success"
                                                onclick="printDeductionReceipt(<?php echo $deduction->deduction_id ?>)">
                                                <i class="fas fa-print me-1"></i> Print Receipt
                                            </button>
                                            <button type="button" class="btn btn-outline-success ms-2"
                                                onclick="toggleDeductionDetails(<?php echo $deduction->deduction_id ?>)">
                                                <i class="fas fa-times me-1"></i> Close Details
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center">
                            <div class="d-flex flex-column align-items-center py-4">
                                <i class="fa-solid fa-check-circle fa-3x mb-3" style="color: #6AA32D;"></i>
                                <h5>No input credit deductions found</h5>
                                <p class="text-muted">There are no input credit deductions to display at this time.</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Global variable to store the DataTable instance
let deductionsTable;

$(document).ready(function() {
    // Initialize DataTable
    initializeDeductionsTable();
});

// Function to initialize the DataTable
function initializeDeductionsTable() {
    // If table is already initialized, destroy it first
    if ($.fn.DataTable.isDataTable('#datatable-deductions')) {
        $('#datatable-deductions').DataTable().destroy();
    }

    // Initialize DataTable with advanced features
    deductionsTable = $('#datatable-deductions').DataTable({
        responsive: true,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
        ],
        dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex"f>>t<"d-flex justify-content-between align-items-center mt-3"<"d-flex align-items-center"i><"d-flex"p>>',
        language: {
            search: "<i class='fa fa-search search-icon'></i>",
            lengthMenu: "_MENU_ records per page",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        },
        // Define column-specific sorting and filtering
        columnDefs: [{
                orderable: false,
                targets: [9]
            }, // Disable sorting on action column
            {
                targets: [5, 6], // For amount columns
                render: function(data, type, row) {
                    if (type === 'sort' || type === 'type') {
                        // Extract numbers for sorting
                        return data.replace(/[^0-9.]/g, '');
                    }
                    return data;
                }
            },
            {
                targets: [7], // For percentage column
                render: function(data, type, row) {
                    if (type === 'sort' || type === 'type') {
                        // Extract percentage value for sorting
                        const match = data.match(/(\d+(\.\d+)?)%/);
                        return match ? match[1] : 0;
                    }
                    return data;
                }
            }
        ],
        // Make table rows collapsible (for detail view)
        drawCallback: function() {
            // Re-initialize row click handlers after DataTable redraw
            $('.view-deduction-details').off('click').on('click', function(e) {
                e.stopPropagation(); // Prevent event bubbling
                const deductionId = $(this).data('id');
                toggleDeductionDetails(deductionId);
            });
        }
    });

    // Add search placeholder
    $('.dataTables_filter input').attr('placeholder', 'Search deductions...');

    // Style the length menu
    $('.dataTables_length select').addClass('form-select form-select-sm');

    // Add custom class to search input
    $('.dataTables_filter input').addClass('form-control form-control-sm');
}
</script>
<?php endif; ?>