<?php
include "../../config/config.php";
include "../../libs/App.php";
if (isset($_POST['displayApplications'])):
    $app = new App;
    $userId = $_SESSION['user_id']; 
    
    // Get the farmer's ID from the user ID
    $farmerQuery = "SELECT id FROM farmers WHERE user_id = $userId";
    $farmerResult = $app->select_one($farmerQuery);
    
    if ($farmerResult) {
        $farmerId = $farmerResult->id;
        
        // Get all bank loan applications for this farmer
        $applicationsQuery = "SELECT 
            la.id,
            la.loan_type_id,
            lt.name as loan_type_name,
            b.name as bank_name,
            la.amount_requested,
            la.term_requested,
            la.purpose,
            la.creditworthiness_score,
            la.application_date,
            la.status,
            CASE 
                WHEN la.status = 'approved' OR la.status = 'disbursed' THEN 
                    (SELECT al.disbursement_date FROM approved_loans al WHERE al.loan_application_id = la.id)
                ELSE NULL
            END as disbursement_date,
            CASE 
                WHEN la.status = 'rejected' THEN la.rejection_reason 
                ELSE NULL
            END as rejection_reason
        FROM loan_applications la
        JOIN loan_types lt ON la.loan_type_id = lt.id
        JOIN banks b ON la.bank_id = b.id
        WHERE la.farmer_id = $farmerId AND la.provider_type = 'bank'
        ORDER BY la.application_date DESC";
        
        $applications = $app->select_all($applicationsQuery);
    } else {
        $applications = [];
    }
?>
<!-- Loan Applications Table -->
<div class="card custom-card mt-2 shadow-sm border-0">
    <div class="card-header justify-content-between" style="background-color: #f8faf5;">
        <div class="card-title d-flex align-items-center">
            <i class="fa-solid fa-file-invoice text-success me-2 fs-4"></i>
            <span class="fw-bold">My Loan Application History</span>
        </div>
        <div>
            <button class="btn btn-sm btn-outline-success rounded-pill">
                <i class="fa-solid fa-file-export me-1"></i> Export History
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="datatable-applications" class="table table-hover table-striped text-nowrap w-100">
                <thead>
                    <tr class="bg-light">
                        <th><i class="fa-solid fa-hashtag text-muted me-1"></i> Reference</th>
                        <th><i class="fa-solid fa-tag text-primary me-1"></i> Loan Type</th>
                        <th><i class="fa-solid fa-money-bill text-success me-1"></i> Amount</th>
                        <th><i class="fa-solid fa-calendar-day text-warning me-1"></i> Term</th>
                        <th><i class="fa-solid fa-chart-line text-info me-1"></i> Score</th>
                        <th><i class="fa-solid fa-circle-info text-info me-1"></i> Status</th>
                        <th><i class="fa-solid fa-calendar text-warning me-1"></i> Date</th>
                        <th><i class="fa-solid fa-sliders text-secondary me-1"></i> Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($applications)): ?>
                    <?php foreach ($applications as $app): ?>
                    <tr>
                        <td>
                            <span class="badge bg-light text-dark rounded-pill">
                                LOAN<?php echo str_pad($app->id, 5, '0', STR_PAD_LEFT); ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs avatar-rounded bg-success-transparent me-2">
                                    <?php echo substr($app->loan_type_name, 0, 1); ?>
                                </span>
                                <span class="fw-medium text-dark">
                                    <?php echo htmlspecialchars($app->loan_type_name) ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <span class="text-success fw-bold">
                                KES <?php echo number_format($app->amount_requested, 2) ?>
                            </span>
                        </td>
                        <td>
                            <span class="text-nowrap">
                                <i class="fa-solid fa-clock text-warning me-1"></i>
                                <?php echo $app->term_requested ?> months
                            </span>
                        </td>
                        <td>
                            <?php if ($app->creditworthiness_score): ?>
                            <?php 
                                $scoreClass = 'danger';
                                if ($app->creditworthiness_score >= 70) {
                                    $scoreClass = 'success';
                                } elseif ($app->creditworthiness_score >= 50) {
                                    $scoreClass = 'warning';
                                }
                                ?>
                            <div class="d-flex align-items-center">
                                <div class="progress me-2" style="width: 40px; height: 5px;">
                                    <div class="progress-bar bg-<?php echo $scoreClass; ?>"
                                        style="width: <?php echo $app->creditworthiness_score; ?>%">
                                    </div>
                                </div>
                                <span class="text-<?php echo $scoreClass; ?> fw-medium">
                                    <?php echo number_format($app->creditworthiness_score, 1) ?>
                                </span>
                            </div>
                            <?php else: ?>
                            <span class="text-muted">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                            $statusClass = 'secondary';
                            $statusIcon = 'clock';
                            
                            if ($app->status == 'under_review') {
                                $statusClass = 'primary';
                                $statusIcon = 'magnifying-glass';
                            } elseif ($app->status == 'approved') {
                                $statusClass = 'info';
                                $statusIcon = 'check-double';
                            } elseif ($app->status == 'disbursed') {
                                $statusClass = 'success';
                                $statusIcon = 'circle-check';
                            } elseif ($app->status == 'rejected') {
                                $statusClass = 'danger';
                                $statusIcon = 'circle-xmark';
                            } elseif ($app->status == 'completed') {
                                $statusClass = 'success';
                                $statusIcon = 'trophy';
                            } elseif ($app->status == 'defaulted') {
                                $statusClass = 'danger';
                                $statusIcon = 'triangle-exclamation';
                            }
                            ?>
                            <span
                                class="badge bg-<?php echo $statusClass; ?>-transparent text-<?php echo $statusClass; ?> py-1 px-2 rounded">
                                <i class="fa-solid fa-<?php echo $statusIcon; ?> me-1"></i>
                                <?php echo ucfirst($app->status); ?>
                            </span>

                            <?php if ($app->status == 'rejected' && $app->rejection_reason): ?>
                            <div class="text-muted small mt-1">
                                <i class="fa-solid fa-info-circle me-1 text-danger"></i>
                                <?php echo substr(htmlspecialchars($app->rejection_reason), 0, 30) . '...'; ?>
                            </div>
                            <?php endif; ?>

                            <?php if ($app->status == 'disbursed' && $app->disbursement_date): ?>
                            <div class="text-muted small mt-1">
                                <i class="fa-solid fa-calendar-check me-1 text-success"></i>
                                <?php echo date('M d, Y', strtotime($app->disbursement_date)); ?>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="text-nowrap">
                                <i class="fa-regular fa-calendar me-1 text-muted"></i>
                                <?php echo date('M d, Y', strtotime($app->application_date)) ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-success rounded-pill btn-icon-text" title="View Details"
                                    onclick="viewApplicationDetails(<?php echo $app->id ?>)">
                                    <i class="ri-eye-line me-1"></i> Details
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <i class="fa-solid fa-file-circle-xmark text-muted fa-3x mb-3"></i>
                                <h5 class="text-muted">No loan applications found</h5>
                                <p class="text-muted">Your application history will appear here</p>
                                <button class="btn btn-success mt-3" onclick="applyForLoan()">
                                    <i class="fa-solid fa-plus me-2"></i>Apply for New Loan
                                </button>
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
    $('#datatable-applications').DataTable({
        responsive: true,
        order: [
            [6, 'desc']
        ], // Sort by application date
        language: {
            searchPlaceholder: 'Search applications...',
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