<?php
include "../../config/config.php";
include "../../libs/App.php";

// Check if repayment ID is provided
if (!isset($_POST['repaymentId']) || empty($_POST['repaymentId'])) {
    echo '<div class="alert alert-danger">
            <i class="ri-error-warning-line me-1"></i> Repayment ID is required
          </div>';
    exit;
}

$repaymentId = $_POST['repaymentId'];
$app = new App();

try {
    // Query to get detailed repayment information
    $query = "SELECT 
                lr.id,
                lr.approved_loan_id,
                lr.produce_delivery_id,
                lr.amount,
                lr.payment_date,
                lr.payment_method,
                lr.notes,
                lr.created_at,
                al.loan_application_id,
                al.approved_amount as loan_amount,
                al.approved_term as loan_term,
                al.total_repayment_amount,
                al.remaining_balance as balance_after,
                (al.remaining_balance + lr.amount) as balance_before,
                al.disbursement_date,
                al.expected_completion_date,
                la.farmer_id,
                la.provider_type,
                la.loan_type_id,
                la.amount_requested,
                la.status as loan_status,
                lt.name as loan_type,
                lt.interest_rate,
                CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                fm.registration_number as farmer_reg,
                pd.quantity as produce_quantity,
                pd.unit_price as produce_unit_price,
                pd.total_value as produce_total_value,
                pd.quality_grade as produce_quality,
                pt.name as produce_type,
                CONCAT(staff.first_name, ' ', staff.last_name) as processed_by_name
              FROM loan_repayments lr
              JOIN approved_loans al ON lr.approved_loan_id = al.id
              JOIN loan_applications la ON al.loan_application_id = la.id
              JOIN loan_types lt ON la.loan_type_id = lt.id
              JOIN farmers fm ON la.farmer_id = fm.id
              JOIN users u ON fm.user_id = u.id
              LEFT JOIN produce_deliveries pd ON lr.produce_delivery_id = pd.id
              LEFT JOIN farm_products fp ON pd.farm_product_id = fp.id
              LEFT JOIN product_types pt ON fp.product_type_id = pt.id
              LEFT JOIN users staff ON lr.processed_by = staff.id
              WHERE lr.id = '{$repaymentId}'";
    
    $repayment = $app->select_one($query);
    
    // If repayment not found, return error
    if (!$repayment) {
        echo '<div class="alert alert-danger">
                <i class="ri-error-warning-line me-1"></i> Repayment not found
              </div>';
        exit;
    }
    
    // Format payment method for display
    $paymentMethod = ucwords(str_replace('_', ' ', $repayment->payment_method));
?>

<div class="row gy-3">
    <div class="col-md-6">
        <label class="form-label text-muted mb-1">Repayment ID</label>
        <p class="fs-15 fw-semibold">REP<?php echo str_pad($repayment->id, 5, '0', STR_PAD_LEFT); ?></p>
    </div>
    <div class="col-md-6">
        <label class="form-label text-muted mb-1">Loan Reference</label>
        <p class="fs-15 fw-semibold">LOAN<?php echo str_pad($repayment->loan_application_id, 5, '0', STR_PAD_LEFT); ?>
        </p>
    </div>
    <div class="col-md-6">
        <label class="form-label text-muted mb-1">Farmer</label>
        <p class="fs-15 fw-semibold"><?php echo htmlspecialchars($repayment->farmer_name); ?>
            <?php if($repayment->farmer_reg): ?>
            (<?php echo htmlspecialchars($repayment->farmer_reg); ?>)
            <?php endif; ?>
        </p>
    </div>
    <div class="col-md-6">
        <label class="form-label text-muted mb-1">Amount Paid</label>
        <p class="fs-15 fw-semibold text-success">KES <?php echo number_format($repayment->amount, 2); ?></p>
    </div>
    <div class="col-md-6">
        <label class="form-label text-muted mb-1">Payment Date</label>
        <p class="fs-15 fw-semibold"><?php echo date('F d, Y', strtotime($repayment->payment_date)); ?></p>
    </div>
    <div class="col-md-6">
        <label class="form-label text-muted mb-1">Payment Method</label>
        <p class="fs-15 fw-semibold"><?php echo $paymentMethod; ?></p>
    </div>
    <div class="col-md-6">
        <label class="form-label text-muted mb-1">Processed By</label>
        <p class="fs-15 fw-semibold"><?php echo $repayment->processed_by_name ?: 'System'; ?></p>
    </div>
    <div class="col-md-6">
        <label class="form-label text-muted mb-1">Processed Date</label>
        <p class="fs-15 fw-semibold"><?php echo date('F d, Y H:i', strtotime($repayment->created_at)); ?></p>
    </div>
    <div class="col-12">
        <label class="form-label text-muted mb-1">Notes</label>
        <p class="fs-15"><?php echo $repayment->notes ?: 'No notes provided'; ?></p>
    </div>
</div>

<?php if ($repayment->payment_method == 'produce_deduction' && $repayment->produce_delivery_id): ?>
<div class="divider my-3">
    <span class="divider-text">Produce Information</span>
</div>
<div class="row gy-3">
    <div class="col-md-6">
        <label class="form-label text-muted mb-1">Produce Type</label>
        <p class="fs-15 fw-semibold"><?php echo htmlspecialchars($repayment->produce_type ?: 'Not specified'); ?></p>
    </div>
    <div class="col-md-6">
        <label class="form-label text-muted mb-1">Quantity</label>
        <p class="fs-15 fw-semibold">
            <?php echo $repayment->produce_quantity ? number_format($repayment->produce_quantity, 2) . ' KGs' : 'Not specified'; ?>
        </p>
    </div>
    <div class="col-md-6">
        <label class="form-label text-muted mb-1">Quality Grade</label>
        <p class="fs-15 fw-semibold">Grade <?php echo $repayment->produce_quality ?: 'Not graded'; ?></p>
    </div>
    <div class="col-md-6">
        <label class="form-label text-muted mb-1">Produce Value</label>
        <p class="fs-15 fw-semibold">KES <?php echo number_format($repayment->produce_total_value ?: 0, 2); ?></p>
    </div>
</div>
<?php endif; ?>

<div class="divider my-3">
    <span class="divider-text">Loan Information</span>
</div>
<div class="row gy-3">
    <div class="col-md-6">
        <label class="form-label text-muted mb-1">Original Loan Amount</label>
        <p class="fs-15 fw-semibold">KES <?php echo number_format($repayment->loan_amount, 2); ?></p>
    </div>
    <div class="col-md-6">
        <label class="form-label text-muted mb-1">Term</label>
        <p class="fs-15 fw-semibold"><?php echo $repayment->loan_term; ?> months</p>
    </div>
    <div class="col-md-6">
        <label class="form-label text-muted mb-1">Loan Type</label>
        <p class="fs-15 fw-semibold"><?php echo htmlspecialchars($repayment->loan_type); ?>
            (<?php echo $repayment->interest_rate; ?>% p.a.)</p>
    </div>
    <div class="col-md-6">
        <label class="form-label text-muted mb-1">Status</label>
        <p class="fs-15 fw-semibold">
            <span class="badge <?php 
                switch($repayment->loan_status) {
                    case 'completed':
                        echo 'bg-success';
                        break;
                    case 'active':
                    case 'disbursed':
                        echo 'bg-primary';
                        break;
                    case 'defaulted':
                        echo 'bg-danger';
                        break;
                    default:
                        echo 'bg-secondary';
                }
            ?>">
                <?php echo ucfirst(str_replace('_', ' ', $repayment->loan_status)); ?>
            </span>
        </p>
    </div>
    <div class="col-md-6">
        <label class="form-label text-muted mb-1">Balance Before Payment</label>
        <p class="fs-15 fw-semibold">KES <?php echo number_format($repayment->balance_before, 2); ?></p>
    </div>
    <div class="col-md-6">
        <label class="form-label text-muted mb-1">Balance After Payment</label>
        <p class="fs-15 fw-semibold text-success">KES <?php echo number_format($repayment->balance_after, 2); ?></p>
    </div>
</div>

<script>
// Update the print receipt button with the current repayment ID
$(document).ready(function() {
    $('#btnPrintReceipt').off('click').on('click', function() {
        printRepaymentReceipt(<?php echo $repayment->id; ?>);
    });
});
</script>

<?php
} catch (Exception $e) {
    echo '<div class="alert alert-danger">
            <i class="ri-error-warning-line me-1"></i> Error fetching repayment details: ' . $e->getMessage() . '
          </div>';
}
?>