<?php
include "../../config/config.php";
include "../../libs/App.php";

if (isset($_POST['displayLoanHistory'])):
    $app = new App;
$userId = $_SESSION['user_id']; 

// Get the farmer's ID from the user ID
$farmerQuery = "SELECT id FROM farmers WHERE user_id = $userId";
$farmerResult = $app->select_one($farmerQuery);

if ($farmerResult) {
    $farmerId = $farmerResult->id;
    
    // Get bank loan history for this farmer
    $loanHistoryQuery = "SELECT 
        la.id AS application_id,
        la.amount_requested,
        la.term_requested,
        la.purpose,
        la.application_date,
        la.status AS application_status,
        la.rejection_reason,
        lt.name AS loan_type,
        b.name AS bank_name,
        lt.interest_rate AS default_interest_rate,
        al.id AS approved_loan_id,
        al.approved_amount,
        al.approved_term,
        al.interest_rate,
        al.processing_fee,
        al.total_repayment_amount,
        al.remaining_balance,
        al.disbursement_date,
        al.expected_completion_date,
        al.status AS loan_status,
        (SELECT SUM(amount) FROM loan_repayments WHERE approved_loan_id = al.id) AS total_repaid
    FROM 
        loan_applications la
    JOIN 
        loan_types lt ON la.loan_type_id = lt.id
    JOIN
        banks b ON la.bank_id = b.id
    LEFT JOIN 
        approved_loans al ON la.id = al.loan_application_id
    WHERE 
        la.farmer_id = $farmerId
        AND la.provider_type = 'bank'
    ORDER BY 
        la.application_date DESC";
    
    $loanHistory = $app->select_all($loanHistoryQuery);
} else {
    $loanHistory = [];
}
?>
<div class="table-responsive">
    <table id="loanHistoryTable" class="table table-hover table-striped text-nowrap w-100">
        <thead>
            <tr class="bg-light">
                <th><i class="fa-solid fa-hashtag text-muted me-1"></i> Reference</th>
                <th><i class="fa-solid fa-tag text-primary me-1"></i> Loan Type</th>
                <th><i class="fa-solid fa-money-bill text-success me-1"></i> Amount</th>
                <th><i class="fa-solid fa-calendar-alt text-info me-1"></i> Applied</th>
                <th><i class="fa-solid fa-bullseye text-warning me-1"></i> Status</th>
                <th><i class="fa-solid fa-percentage text-danger me-1"></i> Interest</th>
                <th><i class="fa-solid fa-layer-group text-secondary me-1"></i> Term</th>
                <th><i class="fa-solid fa-sliders text-primary me-1"></i> Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($loanHistory)): ?>
            <?php foreach ($loanHistory as $loan): ?>
            <?php 
                    // Format the loan reference
                    $loanRef = 'LOAN' . str_pad($loan->application_id, 5, '0', STR_PAD_LEFT);
                    
                    // Determine amount to display (requested or approved)
                    $amount = ($loan->approved_amount) ? $loan->approved_amount : $loan->amount_requested;
                    
                    // Determine interest rate to display
                    $interestRate = ($loan->interest_rate) ? $loan->interest_rate : $loan->default_interest_rate;
                    
                    // Determine term to display
                    $term = ($loan->approved_term) ? $loan->approved_term : $loan->term_requested;
                    
                    // Determine status colors and labels
                    $statusColor = '';
                    $statusLabel = ucfirst(str_replace('_', ' ', $loan->application_status));
                    
                    switch($loan->application_status) {
                        case 'approved':
                        case 'disbursed':
                        case 'completed':
                            $statusColor = 'success';
                            break;
                        case 'rejected':
                        case 'defaulted':
                            $statusColor = 'danger';
                            break;
                        case 'pending':
                        case 'under_review':
                            $statusColor = 'warning';
                            break;
                        default:
                            $statusColor = 'info';
                    }
                    
                    // Override with loan status if available
                    if ($loan->loan_status) {
                        $statusLabel = ucfirst(str_replace('_', ' ', $loan->loan_status));
                        
                        switch($loan->loan_status) {
                            case 'active':
                            case 'completed':
                                $statusColor = 'success';
                                break;
                            case 'defaulted':
                                $statusColor = 'danger';
                                break;
                            case 'pending_disbursement':
                                $statusColor = 'warning';
                                break;
                        }
                    }
                    ?>
            <tr>
                <td>
                    <span class="badge bg-light text-dark rounded-pill">
                        <?php echo $loanRef; ?>
                    </span>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-xs avatar-rounded bg-primary-transparent me-2">
                            <?php echo substr($loan->loan_type, 0, 1); ?>
                        </span>
                        <span class="fw-medium">
                            <?php echo htmlspecialchars($loan->loan_type) ?>
                        </span>
                    </div>
                </td>
                <td>
                    <span class="text-success fw-semibold">
                        KES <?php echo number_format($amount, 2) ?>
                    </span>
                </td>
                <td>
                    <i class="fa-regular fa-calendar-alt text-muted me-1"></i>
                    <?php echo date('M d, Y', strtotime($loan->application_date)) ?>
                </td>
                <td>
                    <span
                        class="badge bg-<?php echo $statusColor; ?>-transparent text-<?php echo $statusColor; ?> rounded-pill">
                        <?php echo $statusLabel; ?>
                    </span>
                </td>
                <td>
                    <?php echo number_format($interestRate, 2) ?>%
                </td>
                <td>
                    <?php echo $term ?> months
                </td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-sm btn-info"
                            onclick="viewLoanDetails(<?php echo $loan->application_id; ?>)" title="View Details">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                        <?php if ($loan->approved_loan_id): ?>
                        <button class="btn btn-sm btn-success"
                            onclick="viewRepayments(<?php echo $loan->approved_loan_id; ?>)" title="View Repayments">
                            <i class="fa-solid fa-money-bill"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="d-flex flex-column align-items-center">
                        <i class="fa-solid fa-file-circle-xmark text-muted fa-3x mb-3"></i>
                        <h5 class="text-muted">No loan history found</h5>
                        <p class="text-muted mb-4">You haven't applied for any loans yet</p>
                        <button class="btn btn-success" onclick="applyForLoan()">
                            <i class="fa-solid fa-plus me-2"></i>Apply for Your First Loan
                        </button>
                    </div>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    $('#loanHistoryTable').DataTable({
        responsive: true,
        order: [
            [3, 'desc'] // Sort by application date, newest first
        ],
        language: {
            searchPlaceholder: 'Search loan history...',
            sSearch: '',
        },
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
        ]
    });
});
</script>
<?php endif; ?>