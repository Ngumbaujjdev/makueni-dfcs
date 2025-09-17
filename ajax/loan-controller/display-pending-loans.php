<?php
include "../../config/config.php";
include "../../libs/App.php";

if (isset($_POST['displayPendingLoans'])):
    $app = new App;
    
    $query = "SELECT 
                la.id,
                la.amount_requested,
                la.term_requested,
                la.purpose,
                la.application_date,
                la.creditworthiness_score,
                la.status,
                lt.name as loan_type,
                lt.interest_rate,
                CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                fm.registration_number as farmer_reg
              FROM loan_applications la
              JOIN loan_types lt ON la.loan_type_id = lt.id
              JOIN farmers fm ON la.farmer_id = fm.id
              JOIN users u ON fm.user_id = u.id
              WHERE la.status = 'under_review'
              ORDER BY la.application_date DESC";
    
    $loanApplications = $app->select_all($query);
?>
<div class="card custom-card">
    <div class="card-header justify-content-between">
        <div class="card-title">
            <i class="ri-file-list-3-line me-2"></i> Pending Loan Applications
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="datatable-loans" class="table table-bordered text-nowrap w-100">
                <thead>
                    <tr>
                        <th><i class="ri-hash-line me-1"></i>Reference</th>
                        <th><i class="ri-user-line me-1"></i>Farmer</th>
                        <th><i class="ri-fingerprint-line me-1"></i>Registration #</th>
                        <th><i class="ri-file-list-line me-1"></i>Loan Type</th>
                        <th><i class="ri-money-dollar-circle-line me-1"></i>Amount (KES)</th>
                        <th><i class="ri-calendar-line me-1"></i>Term (Months)</th>
                        <th><i class="ri-bar-chart-line me-1"></i>Credit Score</th>
                        <th><i class="ri-time-line me-1"></i>Application Date</th>
                        <th><i class="ri-settings-3-line me-1"></i>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($loanApplications): ?>
                    <?php foreach ($loanApplications as $loan): ?>
                    <tr>
                        <td class="fw-semibold">LOAN<?php echo str_pad($loan->id, 5, '0', STR_PAD_LEFT); ?></td>
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
                        <td>
                            <span class="badge bg-info-transparent text-info">
                                <?php echo htmlspecialchars($loan->loan_type) ?>
                            </span>
                        </td>
                        <td class="fw-semibold">KES <?php echo number_format($loan->amount_requested, 2) ?></td>
                        <td class="text-center"><?php echo $loan->term_requested ?></td>
                        <td>
                            <span class="badge <?php 
                                if ($loan->creditworthiness_score >= 80) echo 'bg-success'; 
                                elseif ($loan->creditworthiness_score >= 60) echo 'bg-warning';
                                else echo 'bg-danger';
                            ?>">
                                <?php echo number_format($loan->creditworthiness_score, 1) ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($loan->application_date)) ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary" title="Review Application"
                                onclick="reviewLoanApplication(<?php echo $loan->id ?>)">
                                <i class="ri-file-search-line"></i> Review
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <i class="ri-information-line fs-2 text-muted mb-2"></i>
                            <p>No pending loan applications found</p>
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
    $('#datatable-loans').DataTable({
        responsive: true,
        order: [
            [7, 'desc']
        ], // Sort by application date
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

// Helper function to get CSS class based on credit score
function getScoreClass(score) {
    if (score >= 80) return 'badge bg-success';
    if (score >= 60) return 'badge bg-warning';
    return 'badge bg-danger';
}
</script>
<?php endif; ?>