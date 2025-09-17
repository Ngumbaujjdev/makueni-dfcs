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
        
        // Get all input credit applications for this farmer
        $applicationsQuery = "SELECT 
            ica.id,
            ica.agrovet_id,
            a.name as agrovet_name,
            ica.total_amount,
            ica.total_with_interest,
            ica.credit_percentage,
            ica.repayment_percentage,
            ica.creditworthiness_score,
            ica.application_date,
            ica.status,
            CASE 
                WHEN ica.status = 'approved' OR ica.status = 'fulfilled' THEN 
                    (SELECT aic.fulfillment_date FROM approved_input_credits aic WHERE aic.credit_application_id = ica.id)
                ELSE NULL
            END as fulfillment_date,
            CASE 
                WHEN ica.status = 'rejected' THEN ica.rejection_reason 
                ELSE NULL
            END as rejection_reason,
            (SELECT GROUP_CONCAT(DISTINCT input_type SEPARATOR ', ') 
             FROM input_credit_items 
             WHERE credit_application_id = ica.id) as input_types
        FROM input_credit_applications ica
        JOIN agrovets a ON ica.agrovet_id = a.id
        WHERE ica.farmer_id = $farmerId
        ORDER BY ica.application_date DESC";
        
        $applications = $app->select_all($applicationsQuery);
    } else {
        $applications = [];
    }
?>
<!-- Input Credit Applications Table -->
<div class="card custom-card mt-2 shadow-sm border-0">
    <div class="card-header justify-content-between" style="background-color: #f8faf5;">
        <div class="card-title d-flex align-items-center">
            <i class="fa-solid fa-seedling text-success me-2 fs-4"></i>
            <span class="fw-bold">My Input Credit Application History</span>
        </div>
        <div>
            <button class="btn btn-sm btn-outline-success rounded-pill">
                <i class="fa-solid fa-file-export me-1"></i> Export History
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="datatable-input-applications" class="table table-hover table-striped text-nowrap w-100">
                <thead>
                    <tr class="bg-light">
                        <th><i class="fa-solid fa-hashtag text-muted me-1"></i> Reference</th>
                        <th><i class="fa-solid fa-store text-primary me-1"></i> Agrovet</th>
                        <th><i class="fa-solid fa-tag text-info me-1"></i> Input Types</th>
                        <th><i class="fa-solid fa-money-bill text-success me-1"></i> Amount</th>
                        <th><i class="fa-solid fa-percentage text-warning me-1"></i> Interest</th>
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
                                INPCR<?php echo str_pad($app->id, 5, '0', STR_PAD_LEFT); ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs avatar-rounded bg-success-transparent me-2">
                                    <?php echo substr($app->agrovet_name, 0, 1); ?>
                                </span>
                                <span class="fw-medium text-dark">
                                    <?php echo htmlspecialchars($app->agrovet_name) ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <?php 
                            $inputTypesArray = explode(', ', $app->input_types);
                            $icons = [
                                'fertilizer' => 'seedling',
                                'pesticide' => 'bug-slash',
                                'seeds' => 'wheat-awn',
                                'tools' => 'tools',
                                'other' => 'box'
                            ];
                            
                            $colors = [
                                'fertilizer' => 'success',
                                'pesticide' => 'danger',
                                'seeds' => 'warning',
                                'tools' => 'primary',
                                'other' => 'info'
                            ];
                            
                            foreach ($inputTypesArray as $inputType) {
                                $icon = isset($icons[$inputType]) ? $icons[$inputType] : 'box';
                                $color = isset($colors[$inputType]) ? $colors[$inputType] : 'secondary';
                                echo '<span class="badge bg-' . $color . '-transparent text-' . $color . ' me-1 mb-1">';
                                echo '<i class="fa-solid fa-' . $icon . ' me-1"></i>' . ucfirst($inputType);
                                echo '</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <div>
                                <span class="text-success fw-bold">
                                    KES <?php echo number_format($app->total_amount, 2) ?>
                                </span>
                            </div>
                            <div class="small text-muted">
                                <i class="fa-solid fa-coins me-1"></i> With Interest: KES
                                <?php echo number_format($app->total_with_interest, 2) ?>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    <span class="badge bg-warning-transparent text-warning">
                                        <?php echo $app->credit_percentage ?>%
                                    </span>
                                </div>
                                <div class="small text-muted">
                                    Repay: <?php echo $app->repayment_percentage ?>%
                                </div>
                            </div>
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
                            } elseif ($app->status == 'fulfilled') {
                                $statusClass = 'success';
                                $statusIcon = 'circle-check';
                            } elseif ($app->status == 'rejected') {
                                $statusClass = 'danger';
                                $statusIcon = 'circle-xmark';
                            } elseif ($app->status == 'completed') {
                                $statusClass = 'success';
                                $statusIcon = 'trophy';
                            } elseif ($app->status == 'cancelled') {
                                $statusClass = 'danger';
                                $statusIcon = 'ban';
                            }
                            ?>
                            <span
                                class="badge bg-<?php echo $statusClass; ?>-transparent text-<?php echo $statusClass; ?> py-1 px-2 rounded">
                                <i class="fa-solid fa-<?php echo $statusIcon; ?> me-1"></i>
                                <?php echo str_replace('_', ' ', ucfirst($app->status)); ?>
                            </span>

                            <?php if ($app->status == 'rejected' && $app->rejection_reason): ?>
                            <div class="text-muted small mt-1">
                                <i class="fa-solid fa-info-circle me-1 text-danger"></i>
                                <?php echo substr(htmlspecialchars($app->rejection_reason), 0, 30) . '...'; ?>
                            </div>
                            <?php endif; ?>

                            <?php if ($app->status == 'fulfilled' && $app->fulfillment_date): ?>
                            <div class="text-muted small mt-1">
                                <i class="fa-solid fa-calendar-check me-1 text-success"></i>
                                <?php echo date('M d, Y', strtotime($app->fulfillment_date)); ?>
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
                                    onclick="viewInputCreditDetails(<?php echo $app->id ?>)">
                                    <i class="ri-eye-line me-1"></i> Details
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <i class="fa-solid fa-seedling text-muted fa-3x mb-3 opacity-50"></i>
                                <h5 class="text-muted">No input credit applications found</h5>
                                <p class="text-muted">Your application history will appear here</p>
                                <button class="btn btn-success mt-3" onclick="applyForInputCredit()">
                                    <i class="fa-solid fa-plus me-2"></i>Apply for Input Credit
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
    $('#datatable-input-applications').DataTable({
        responsive: true,
        order: [
            [7, 'desc']
        ], // Sort by application date
        language: {
            searchPlaceholder: 'Search input credit applications...',
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