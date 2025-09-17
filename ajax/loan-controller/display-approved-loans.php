<?php
include "../../config/config.php";
include "../../libs/App.php";
?>
<?php
if (isset($_POST['displayApprovedLoans'])):
    $app = new App;
    
    $query = "SELECT 
                la.id,
                al.approved_amount,
                al.approved_term,
                al.interest_rate,
                al.total_repayment_amount,
                al.remaining_balance,
                al.disbursement_date,
                al.expected_completion_date,
                al.status,
                lt.name as loan_type,
                CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                fm.registration_number as farmer_reg
              FROM approved_loans al
              JOIN loan_applications la ON al.loan_application_id = la.id
              JOIN loan_types lt ON la.loan_type_id = lt.id
              JOIN farmers fm ON la.farmer_id = fm.id
              JOIN users u ON fm.user_id = u.id
              ORDER BY al.approval_date DESC";
    
    $approvedLoans = $app->select_all($query);
?>
<div class="card custom-card mt-4">
    <div class="card-header justify-content-between">
        <div class="card-title">
            Recently Approved Loans
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="datatable-approved" class="table table-bordered text-nowrap w-100">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Farmer</th>
                        <th>Registration #</th>
                        <th>Loan Type</th>
                        <th>Approved Amount</th>
                        <th>Remaining Balance</th>
                        <th>Term</th>
                        <th>Interest Rate</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($approvedLoans): ?>
                    <?php foreach ($approvedLoans as $loan): ?>
                    <tr>
                        <td>LOAN<?php echo str_pad($loan->id, 5, '0', STR_PAD_LEFT); ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="fw-medium">
                                    <?php echo htmlspecialchars($loan->farmer_name) ?>
                                </span>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($loan->farmer_reg) ?></td>
                        <td><?php echo htmlspecialchars($loan->loan_type) ?></td>
                        <td>KES <?php echo number_format($loan->approved_amount, 2) ?></td>
                        <td>KES <?php echo number_format($loan->remaining_balance, 2) ?></td>
                        <td><?php echo $loan->approved_term ?> months</td>
                        <td><?php echo $loan->interest_rate ?>%</td>
                        <td>
                            <span class="badge <?php 
                                                     if ($loan->status == 'pending_disbursement') echo 'bg-warning';
                                                     elseif ($loan->status == 'active') echo 'bg-primary'; 
                                                     elseif ($loan->status == 'completed') echo 'bg-success';
                                                     elseif ($loan->status == 'defaulted') echo 'bg-danger';
                                                     else echo 'bg-secondary';
                                                 ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $loan->status)) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">No approved loans found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#datatable-approved').DataTable({
        responsive: true,
        order: [
            [0, 'desc']
        ], // Sort by loan ID (most recent first)
        language: {
            searchPlaceholder: 'Search loans...',
            sSearch: '',
        },
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
        ],
        buttons: ['copy', 'excel', 'pdf', 'print']
    });
});

// Helper function to get CSS class based on loan status
function getLoanStatusClass(status) {
    switch (status) {
        case 'pending_disbursement':
            return 'warning';
        case 'active':
            return 'primary';
        case 'completed':
            return 'success';
        case 'defaulted':
            return 'danger';
        default:
            return 'secondary';
    }
}
</script>
<?php endif; ?>