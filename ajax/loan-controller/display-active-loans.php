<?php
include "../../config/config.php";
include "../../libs/App.php";

if (isset($_POST['displayActiveLoans'])):
    $app = new App;
    
    $query = "SELECT 
                al.id,
                al.loan_application_id,  
                al.approved_amount,
                al.approved_term,
                al.interest_rate,
                al.total_repayment_amount,
                al.remaining_balance,
                al.disbursement_date,
                al.expected_completion_date,
                CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                fm.registration_number as farmer_reg
              FROM approved_loans al 
              JOIN loan_applications la ON al.loan_application_id = la.id
              JOIN farmers fm ON la.farmer_id = fm.id
              JOIN users u ON fm.user_id = u.id
              WHERE al.status = 'active' AND la.provider_type = 'sacco'
              ORDER BY al.disbursement_date DESC";
    
    $activeLoans = $app->select_all($query);
?>
<div class="card custom-card">
    <div class="card-header justify-content-between">
        <div class="card-title">
            <i class="ri-file-list-3-line me-2"></i> Active Loans
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="datatable-active-loans" class="table table-bordered text-nowrap w-100">
                <thead>
                    <tr>
                        <th><i class="ri-hash-line me-1"></i>Reference</th>
                        <th><i class="ri-user-line me-1"></i>Farmer</th>
                        <th><i class="ri-fingerprint-line me-1"></i>Registration #</th>
                        <th><i class="ri-money-dollar-circle-line me-1"></i>Approved Amount</th>
                        <th><i class="ri-calendar-line me-1"></i>Term</th>
                        <th><i class="ri-money-dollar-circle-line me-1"></i>Repayment Amount</th>
                        <th><i class="ri-money-dollar-circle-line me-1"></i>Balance</th>
                        <th><i class="ri-calendar-check-line me-1"></i>Disbursed</th>
                        <th><i class="ri-calendar-todo-line me-1"></i>Expected Completion</th>
                        <th><i class="ri-calendar-todo-line me-1"></i>View</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($activeLoans): ?>
                    <?php foreach ($activeLoans as $loan): ?>
                    <tr>
                        <td class="fw-semibold">ACTV<?php echo str_pad($loan->id, 5, '0', STR_PAD_LEFT); ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm bg-success me-2">
                                    <?php echo strtoupper(substr($loan->farmer_name, 0, 1)); ?>
                                </span>
                                <span class="fw-medium">
                                    <?php echo htmlspecialchars($loan->farmer_name) ?>
                                </span>
                            </div>
                        </td>
                        <td class="text-muted"><?php echo htmlspecialchars($loan->farmer_reg) ?></td>
                        <td class="fw-semibold">KES <?php echo number_format($loan->approved_amount, 2) ?></td>
                        <td class="text-center"><?php echo $loan->approved_term ?> Months</td>
                        <td class="fw-semibold">KES <?php echo number_format($loan->total_repayment_amount, 2) ?></td>
                        <td class="fw-semibold">KES <?php echo number_format($loan->remaining_balance, 2) ?></td>
                        <td><?php echo date('M d, Y', strtotime($loan->disbursement_date)) ?></td>
                        <td><?php echo date('M d, Y', strtotime($loan->expected_completion_date)) ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary" title="View Loan Details"
                                onclick="viewLoanDetails(<?php echo $loan->loan_application_id ?>)">
                                <i class="ri-eye-line"></i> View
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <i class="ri-information-line fs-2 text-muted mb-2"></i>
                            <p>No active loans found</p>
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
    $('#datatable-active-loans').DataTable({
        responsive: true,
        order: [
            [7, 'asc']
        ], // Sort by disbursement date
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
</script>
<?php endif; ?>