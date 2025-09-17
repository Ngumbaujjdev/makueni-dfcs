<?php
include "../../config/config.php";
include "../../libs/App.php";

if (isset($_POST['displayAllRepayments'])):
    $app = new App;
    
    // Query to get all SACCO loan repayments with related data
    $query = "SELECT 
                lr.id,
                lr.approved_loan_id,
                lr.produce_delivery_id,
                lr.amount,
                lr.payment_date,
                lr.payment_method,
                lr.notes,
                al.loan_application_id,
                la.farmer_id,
                CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                fm.registration_number as farmer_reg,
                CONCAT('LOAN', LPAD(la.id, 5, '0')) as loan_reference,
                lt.name as loan_type
              FROM loan_repayments lr
              JOIN approved_loans al ON lr.approved_loan_id = al.id
              JOIN loan_applications la ON al.loan_application_id = la.id
              JOIN loan_types lt ON la.loan_type_id = lt.id
              JOIN farmers fm ON la.farmer_id = fm.id
              JOIN users u ON fm.user_id = u.id
              WHERE la.provider_type = 'sacco'
              ORDER BY lr.payment_date DESC";

    $repayments = $app->select_all($query);
?>
<div id="repaymentsSection">
    <div class="card custom-card">
        <div class="card-header justify-content-between">
            <div class="card-title">
                <i class="ri-history-line me-2"></i> All Loan Repayments
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="datatable-repayments" class="table table-bordered text-nowrap w-100">
                    <thead>
                        <tr>
                            <th><i class="ri-hash-line me-1"></i>ID</th>
                            <th><i class="ri-file-list-line me-1"></i>Loan</th>
                            <th><i class="ri-user-line me-1"></i>Farmer</th>
                            <th><i class="ri-money-dollar-circle-line me-1"></i>Amount (KES)</th>
                            <th><i class="ri-calendar-line me-1"></i>Date</th>
                            <th><i class="ri-bank-line me-1"></i>Method</th>
                            <th><i class="ri-settings-3-line me-1"></i>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($repayments): ?>
                        <?php foreach ($repayments as $repayment): ?>
                        <tr data-repayment-id="<?php echo $repayment->id ?>">
                            <td class="fw-semibold">REP<?php echo str_pad($repayment->id, 5, '0', STR_PAD_LEFT); ?></td>
                            <td>
                                <span class="badge bg-primary-transparent text-primary">
                                    <?php echo htmlspecialchars($repayment->loan_reference) ?>
                                </span>
                                <small
                                    class="d-block text-muted"><?php echo htmlspecialchars($repayment->loan_type) ?></small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-sm bg-success me-2">
                                        <?php echo strtoupper(substr($repayment->farmer_name, 0, 1)); ?>
                                    </span>
                                    <span class="fw-medium">
                                        <?php echo htmlspecialchars($repayment->farmer_name) ?>
                                    </span>
                                </div>
                            </td>
                            <td class="fw-semibold text-success">
                                KES <?php echo number_format($repayment->amount, 2) ?>
                            </td>
                            <td>
                                <?php echo date('M d, Y', strtotime($repayment->payment_date)) ?>
                            </td>
                            <td>
                                <?php
                                $badgeClass = '';
                                $methodName = '';
                                
                                switch($repayment->payment_method) {
                                    case 'produce_deduction':
                                        $badgeClass = 'bg-success-transparent text-success';
                                        $methodName = 'Produce Deduction';
                                        break;
                                    case 'cash':
                                        $badgeClass = 'bg-info-transparent text-info';
                                        $methodName = 'Cash';
                                        break;
                                    case 'bank_transfer':
                                        $badgeClass = 'bg-primary-transparent text-primary';
                                        $methodName = 'Bank Transfer';
                                        break;
                                    case 'mobile_money':
                                        $badgeClass = 'bg-warning-transparent text-warning';
                                        $methodName = 'Mobile Money';
                                        break;
                                    default:
                                        $badgeClass = 'bg-secondary-transparent text-secondary';
                                        $methodName = ucfirst(str_replace('_', ' ', $repayment->payment_method));
                                }
                                ?>
                                <span class="badge <?php echo $badgeClass ?>">
                                    <?php echo $methodName ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-primary view-details-btn" title="View Details"
                                        data-repayment-id="<?php echo $repayment->id ?>"
                                        data-farmer-name="<?php echo htmlspecialchars($repayment->farmer_name) ?>"
                                        data-farmer-reg="<?php echo htmlspecialchars($repayment->farmer_reg) ?>"
                                        data-loan-ref="<?php echo htmlspecialchars($repayment->loan_reference) ?>"
                                        data-loan-type="<?php echo htmlspecialchars($repayment->loan_type) ?>"
                                        data-amount="<?php echo number_format($repayment->amount, 2) ?>"
                                        data-date="<?php echo date('F d, Y', strtotime($repayment->payment_date)) ?>"
                                        data-method="<?php echo $methodName ?>"
                                        data-notes="<?php echo htmlspecialchars($repayment->notes ?: 'No notes provided') ?>">
                                        <i class="ri-eye-line"></i>
                                    </button>

                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="ri-information-line fs-2 text-muted mb-2"></i>
                                <p>No loan repayments found</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var currentRepaymentId = null;
    var currentDetailsRow = null;

    var table = $('#datatable-repayments').DataTable({
        responsive: true,
        order: [
            [4, 'desc']
        ],
        language: {
            searchPlaceholder: 'Search repayments...',
            sSearch: '',
        },
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
        ],
        dom: 'Bfrtip',
        buttons: [{
            extend: 'collection',
            text: '<i class="ri-download-2-line me-1"></i> Export',
            buttons: [
                'copy',
                'excel',
                'csv',
                'pdf',
                'print'
            ]
        }]
    });

    // View Details Button Click Handler
    $(document).on('click', '.view-details-btn', function(e) {
        e.preventDefault();

        var repaymentId = $(this).data('repayment-id');
        var clickedRow = $(this).closest('tr');

        // If clicking the same row that's already expanded, collapse it
        if (currentRepaymentId === repaymentId && currentDetailsRow) {
            currentDetailsRow.remove();
            currentDetailsRow = null;
            currentRepaymentId = null;
            clickedRow.removeClass('table-active');
            return;
        }

        // Remove any existing details row
        if (currentDetailsRow) {
            currentDetailsRow.remove();
            $('tr[data-repayment-id]').removeClass('table-active');
        }

        // Get data from button
        var farmerName = $(this).data('farmer-name');
        var farmerReg = $(this).data('farmer-reg');
        var loanRef = $(this).data('loan-ref');
        var loanType = $(this).data('loan-type');
        var amount = $(this).data('amount');
        var paymentDate = $(this).data('date');
        var paymentMethod = $(this).data('method');
        var notes = $(this).data('notes');

        currentRepaymentId = repaymentId;

        // Build the beautiful details HTML
        var detailsHtml = `
            <tr class="details-row" style="background: linear-gradient(135deg, #f8f9fc 0%, #e9ecef 100%);">
                <td colspan="7" class="p-0">
                    <div class="card border-0 shadow-sm m-3">
                        <div class="card-header bg-primary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="ri-file-info-line me-2"></i>
                                    Repayment Details - REP${String(repaymentId).padStart(5, '0')}
                                </h6>
                                <button type="button" class="btn btn-sm btn-light close-details-btn">
                                    <i class="ri-close-line"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <!-- Payment Information -->
                                <div class="col-lg-6">
                                    <div class="border rounded p-3 h-100" style="background: #f8f9fa;">
                                        <h6 class="text-primary mb-3">
                                            <i class="ri-money-dollar-circle-line me-2"></i>Payment Information
                                        </h6>
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <label class="form-label text-muted small mb-1">Repayment ID</label>
                                                <p class="fw-bold mb-0">REP${String(repaymentId).padStart(5, '0')}</p>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label text-muted small mb-1">Amount Paid</label>
                                                <p class="fw-bold text-success mb-0 fs-5">KES ${amount}</p>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label text-muted small mb-1">Payment Date</label>
                                                <p class="fw-semibold mb-0">
                                                    <i class="ri-calendar-line me-1 text-primary"></i>${paymentDate}
                                                </p>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label text-muted small mb-1">Payment Method</label>
                                                <p class="mb-0">
                                                    <span class="badge bg-info-transparent text-info">
                                                        <i class="ri-bank-line me-1"></i>${paymentMethod}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Farmer & Loan Information -->
                                <div class="col-lg-6">
                                    <div class="border rounded p-3 h-100" style="background: #f8f9fa;">
                                        <h6 class="text-success mb-3">
                                            <i class="ri-user-line me-2"></i>Farmer & Loan Details
                                        </h6>
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label text-muted small mb-1">Farmer</label>
                                                <p class="fw-bold mb-1">
                                                    <i class="ri-user-3-line me-1 text-success"></i>${farmerName}
                                                </p>
                                                <small class="text-muted">${farmerReg}</small>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label text-muted small mb-1">Loan Reference</label>
                                                <p class="mb-0">
                                                    <span class="badge bg-primary-transparent text-primary">
                                                        ${loanRef}
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label text-muted small mb-1">Loan Type</label>
                                                <p class="fw-semibold mb-0 small">${loanType}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Notes Section -->
                                <div class="col-12">
                                    <div class="border rounded p-3" style="background: #fff3cd;">
                                        <h6 class="text-warning mb-2">
                                            <i class="ri-sticky-note-line me-2"></i>Additional Notes
                                        </h6>
                                        <p class="mb-0 text-dark">${notes}</p>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                        <div class="text-muted small">
                                            <i class="ri-information-line me-1"></i>
                                            Click the close button or another row to hide details
                                        </div>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-outline-secondary btn-sm close-details-btn">
                                                <i class="ri-close-line me-1"></i>Close
                                            </button>
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        `;

        // Insert the details row after the clicked row
        currentDetailsRow = $(detailsHtml);
        clickedRow.after(currentDetailsRow);
        clickedRow.addClass('table-active');

        // Animate the details row
        currentDetailsRow.hide().slideDown(400);

        toastr.success('Repayment details loaded', 'Success', {
            "positionClass": "toast-top-right",
            "progressBar": true,
            "timeOut": 2000
        });
    });

    // Close Details Button Click Handler
    $(document).on('click', '.close-details-btn', function() {
        if (currentDetailsRow) {
            currentDetailsRow.slideUp(300, function() {
                $(this).remove();
            });
            $('tr[data-repayment-id]').removeClass('table-active');
            currentDetailsRow = null;
            currentRepaymentId = null;

            toastr.info('Details panel closed', '', {
                "positionClass": "toast-top-right",
                "progressBar": true,
                "timeOut": 1000
            });
        }
    });

    // Filter buttons
    $('#btnShowAllRepayments').click(function() {
        table.search('').draw();
    });

    $('#btnShowThisMonth').click(function() {
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
            'Dec'
        ];
        const now = new Date();
        const monthName = months[now.getMonth()];
        table.search(monthName + ' ' + now.getFullYear()).draw();
    });

    $('#btnShowLastMonth').click(function() {
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
            'Dec'
        ];
        const now = new Date();
        now.setMonth(now.getMonth() - 1);
        const monthName = months[now.getMonth()];
        table.search(monthName + ' ' + now.getFullYear()).draw();
    });

    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});

// Print Receipt Function
function printRepaymentReceipt(repaymentId) {
    console.log('Print receipt for repayment:', repaymentId);

    toastr.info('Preparing your receipt for download...', 'Please wait', {
        "positionClass": "toast-top-right",
        "progressBar": true,
        "timeOut": 3000
    });

    // Your existing print receipt code here
    setTimeout(function() {
        toastr.success('Receipt would be downloaded here', 'Success', {
            "positionClass": "toast-top-right",
            "progressBar": true,
            "timeOut": 3000
        });
    }, 1000);
}

// Global function for backward compatibility
function viewRepaymentDetails(repaymentId) {
    $('.view-details-btn[data-repayment-id="' + repaymentId + '"]').click();
}
</script>
<?php endif; ?>