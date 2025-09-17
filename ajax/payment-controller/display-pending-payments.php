<?php
include "../../config/config.php";
include "../../libs/App.php";

if (isset($_POST['displayPendingPayments'])):
    $app = new App;
   // Query to get verified produce that's been marked for sale but payment hasn't been processed yet
$query = "SELECT 
         pd.id,
         pd.quantity,
         pd.unit_price,
         pd.total_value,
         pd.quality_grade,
         pd.delivery_date,
         pd.status,
         pd.notes,
         pd.is_sold,
         pd.sale_date,
         pt.name as product_name,
         f.name as farm_name,
         CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
         fm.id as farmer_id,
         (pd.total_value * 0.9) as farmer_payment_amount
       FROM produce_deliveries pd
       JOIN farm_products fp ON pd.farm_product_id = fp.id
       JOIN product_types pt ON fp.product_type_id = pt.id
       JOIN farms f ON fp.farm_id = f.id
       JOIN farmers fm ON f.farmer_id = fm.id
       JOIN users u ON fm.user_id = u.id
       WHERE pd.status = 'verified'
       AND pd.is_sold = 1
       AND NOT EXISTS (
           SELECT 1 FROM farmer_account_transactions fat
           WHERE fat.reference_id = pd.id
           AND fat.transaction_type = 'credit'
       )
       ORDER BY pd.sale_date DESC";

$payments = $app->select_all($query);
?>
<div class="card custom-card">
    <div class="card-header justify-content-between" style="background-color: #6AA32D; color: white;">
        <div class="card-title">
            <i class="fa-solid fa-money-bill-transfer me-2"></i> Pending Farmer Payments
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="datatable-payments" class="table table-bordered text-nowrap w-100">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Farmer</th>
                        <th>Product</th>
                        <th>Quantity (KGs)</th>
                        <th>Sale Value</th>
                        <th>Farmer Amount</th>
                        <th>Sale Date</th>
                        <th>Outstanding Loans</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($payments && count($payments) > 0): ?>
                    <?php foreach ($payments as $payment): ?>
                    <?php
                    // Check for outstanding loans
                    $loanQuery = "SELECT COUNT(*) as loan_count, SUM(remaining_balance) as total_outstanding 
                                 FROM approved_loans 
                                 WHERE status = 'active' 
                                 AND loan_application_id IN (
                                    SELECT id FROM loan_applications 
                                    WHERE farmer_id = :farmer_id
                                 )";
                    $loanParams = [':farmer_id' => $payment->farmer_id];
                    $loanInfo = $app->selectOne($loanQuery, $loanParams);
                    
                    $hasLoans = ($loanInfo && $loanInfo->loan_count > 0);
                    $loanBadgeClass = $hasLoans ? 'bg-warning' : 'bg-success';
                    $loanText = $hasLoans ? 
                                'KES ' . number_format($loanInfo->total_outstanding, 2) : 
                                'None';
                    ?>
                    <tr>
                        <td>DLVR<?php echo str_pad($payment->id, 5, '0', STR_PAD_LEFT); ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs avatar-rounded bg-light me-2">
                                    <i class="fa-solid fa-user" style="color: #6AA32D;"></i>
                                </span>
                                <span class="fw-medium">
                                    <?php echo htmlspecialchars($payment->farmer_name) ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs avatar-rounded bg-light me-2">
                                    <i class="fa-solid fa-apple-whole" style="color: #6AA32D;"></i>
                                </span>
                                <?php echo htmlspecialchars($payment->product_name) ?>
                            </div>
                        </td>
                        <td><?php echo number_format($payment->quantity, 2) ?> KGs</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs avatar-rounded bg-light me-2">
                                    <i class="fa-solid fa-tag" style="color: #6AA32D;"></i>
                                </span>
                                KES <?php echo number_format($payment->total_value, 2) ?>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs avatar-rounded bg-light me-2">
                                    <i class="fa-solid fa-hand-holding-dollar" style="color: #6AA32D;"></i>
                                </span>
                                KES <?php echo number_format($payment->farmer_payment_amount, 2) ?>
                            </div>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($payment->sale_date)) ?></td>
                        <td>
                            <span class="badge <?php echo $loanBadgeClass ?>">
                                <?php echo $loanText ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-primary"
                                    style="background-color: #6AA32D; border-color: #6AA32D;" title="Process Payment"
                                    onclick="openPaymentModal(<?php echo $payment->id ?>, <?php echo $payment->farmer_id ?>)">
                                    <i class="fa-solid fa-money-bill-transfer"></i> Process Payment
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">
                            <div class="d-flex flex-column align-items-center py-4">
                                <i class="fa-solid fa-check-circle fa-3x mb-3" style="color: #6AA32D;"></i>
                                <h5>No pending payments found</h5>
                                <p class="text-muted">All sold produce payments have been processed.</p>
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
$(document).ready(function() {
    $('#datatable-payments').DataTable({
        responsive: true,
        order: [
            [6, 'desc']
        ], // Sort by sale date
        language: {
            searchPlaceholder: 'Search payments...',
            sSearch: '',
        },
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
        ],
        buttons: ['copy', 'excel', 'pdf', 'print']
    });
});
</script>
<?php endif; ?>