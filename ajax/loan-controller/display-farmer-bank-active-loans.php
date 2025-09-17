<?php
include "../../config/config.php";
include "../../libs/App.php";

if (isset($_POST['displayActiveLoans'])):
   $app = new App;
$userId = $_SESSION['user_id']; 

// Get the farmer's ID from the user ID
$farmerQuery = "SELECT id FROM farmers WHERE user_id = $userId";
$farmerResult = $app->select_one($farmerQuery);

if ($farmerResult) {
    $farmerId = $farmerResult->id;
    
    // Get active bank loans for this farmer
    $activeLoansQuery = "SELECT 
        la.id AS application_id,
        la.farmer_id,
        la.loan_type_id,
        lt.name AS loan_type,
        b.name AS bank_name,
        al.id AS loan_id,
        al.approved_amount,
        al.approved_term,
        al.interest_rate,
        al.total_repayment_amount,
        al.remaining_balance,
        al.disbursement_date,
        al.expected_completion_date,
        al.status AS loan_status,
        DATEDIFF(al.expected_completion_date, CURDATE()) AS days_remaining
    FROM 
        loan_applications la
    JOIN 
        approved_loans al ON la.id = al.loan_application_id
    JOIN 
        loan_types lt ON la.loan_type_id = lt.id
    JOIN
        banks b ON la.bank_id = b.id
    WHERE 
        la.farmer_id = $farmerId
        AND la.provider_type = 'bank'
        AND al.status IN ('active', 'pending_disbursement')
    ORDER BY 
        al.expected_completion_date ASC";
    
    $activeLoans = $app->select_all($activeLoansQuery);
} else {
    $activeLoans = [];
}
?>
<!-- Active Loans Card -->
<div class="card custom-card mt-4 shadow-sm border-0">
    <div class="card-header justify-content-between" style="background-color: #f8faf5;">
        <div class="card-title d-flex align-items-center">
            <i class="fa-solid fa-credit-card text-success me-2 fs-4"></i>
            <span class="fw-bold">My Active Loans</span>
        </div>
        <div>
            <button class="btn btn-sm btn-success rounded-pill" onclick="applyForLoan()">
                <i class="fa-solid fa-plus me-1"></i> Apply for New Loan
            </button>
        </div>
    </div>
    <div class="card-body">
        <?php if (!empty($activeLoans)): ?>
        <div class="table-responsive">
            <table id="datatable-activeloans" class="table table-hover table-striped text-nowrap w-100">
                <thead>
                    <tr class="bg-light">
                        <th><i class="fa-solid fa-hashtag text-muted me-1"></i> Reference</th>
                        <th><i class="fa-solid fa-tag text-primary me-1"></i> Loan Type</th>
                        <th><i class="fa-solid fa-money-bill text-success me-1"></i> Amount</th>
                        <th><i class="fa-solid fa-coins text-danger me-1"></i> Remaining</th>
                        <th><i class="fa-solid fa-chart-pie text-info me-1"></i> Progress</th>
                        <th><i class="fa-solid fa-calendar-day text-warning me-1"></i> Due Date</th>

                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($activeLoans as $loan): ?>
                    <?php
                            // Calculate payment progress
                            $amountPaid = $loan->total_repayment_amount - $loan->remaining_balance;
                            $progressPercentage = ($amountPaid / $loan->total_repayment_amount) * 100;
                            
                            // Determine progress color based on percentage
                            $progressColor = 'success';
                            if ($progressPercentage < 25) {
                                $progressColor = 'danger';
                            } elseif ($progressPercentage < 50) {
                                $progressColor = 'warning';
                            } elseif ($progressPercentage < 75) {
                                $progressColor = 'info';
                            }
                            ?>
                    <tr>
                        <td>
                            <span class="badge bg-light text-dark rounded-pill">
                                LOAN<?php echo str_pad($loan->loan_id, 5, '0', STR_PAD_LEFT); ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs avatar-rounded bg-success-transparent me-2">
                                    <?php echo substr($loan->loan_type, 0, 1); ?>
                                </span>
                                <span class="fw-medium text-dark">
                                    <?php echo htmlspecialchars($loan->loan_type) ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div>
                                <span class="text-success fw-bold">
                                    KES <?php echo number_format($loan->approved_amount, 2) ?>
                                </span>
                                <div class="text-muted small">
                                    <i class="fa-solid fa-calendar-alt me-1"></i>
                                    <?php echo $loan->approved_term ?> months
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <span class="fw-bold text-danger">
                                    KES <?php echo number_format($loan->remaining_balance, 2) ?>
                                </span>
                                <div class="text-muted small">
                                    <i class="fa-solid fa-percentage me-1"></i>
                                    <?php echo $loan->interest_rate ?>% interest
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="progress w-100" style="height: 6px;">
                                    <div class="progress-bar bg-<?php echo $progressColor; ?>" role="progressbar"
                                        style="width: <?php echo round($progressPercentage); ?>%"
                                        aria-valuenow="<?php echo round($progressPercentage); ?>" aria-valuemin="0"
                                        aria-valuemax="100">
                                    </div>
                                </div>
                                <span class="ms-2"><?php echo round($progressPercentage); ?>%</span>
                            </div>
                        </td>
                        <td>
                            <div>
                                <span class="text-nowrap fw-medium">
                                    <?php echo date('M d, Y', strtotime($loan->expected_completion_date)) ?>
                                </span>
                                <div
                                    class="text-<?php echo $loan->days_remaining < 30 ? 'danger' : 'success'; ?> small">
                                    <i class="fa-solid fa-clock me-1"></i>
                                    <?php echo $loan->days_remaining; ?> days left
                                </div>
                            </div>
                        </td>

                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <div class="d-flex flex-column align-items-center">
                <i class="fa-solid fa-credit-card text-muted fa-3x mb-3"></i>
                <h5 class="text-muted">No active loans found</h5>
                <p class="text-muted mb-4">You don't have any active loans at the moment</p>
                <button class="btn btn-success" onclick="applyForLoan()">
                    <i class="fa-solid fa-plus me-2"></i>Apply for New Loan
                </button>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#datatable-activeloans').DataTable({
        responsive: true,
        order: [
            [5, 'asc']
        ], // Sort by due date
        language: {
            searchPlaceholder: 'Search active loans...',
            sSearch: '',
        },
        lengthMenu: [
            [5, 10, 25, -1],
            [5, 10, 25, "All"]
        ]
    });
});
</script>
<?php endif; ?>