<?php
include "../../config/config.php";
include "../../libs/App.php";

if (isset($_POST['displayPendingCredits'])):
    $app = new App;
    
    // Get session user_id to identify agrovet staff
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        exit;
    }
    
    // Get staff agrovet_id
    $staffQuery = "SELECT s.id as staff_id, s.agrovet_id 
                  FROM agrovet_staff s 
                  WHERE s.user_id = :user_id";
    
    $staff = $app->selectOne($staffQuery, [':user_id' => $userId]);
    
    if (!$staff) {
        echo "<div class='alert alert-danger'>Error: Staff information not found.</div>";
        exit;
    }
    
    // Query to get pending input credit applications for this agrovet
    $query = "SELECT 
                ica.id,
                ica.farmer_id,
                ica.agrovet_id,
                ica.total_amount,
                ica.credit_percentage,
                ica.total_with_interest,
                ica.repayment_percentage,
                ica.application_date,
                ica.creditworthiness_score,
                ica.status,
                CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                f.registration_number as farmer_reg,
                a.name as agrovet_name
              FROM input_credit_applications ica
              JOIN farmers f ON ica.farmer_id = f.id
              JOIN users u ON f.user_id = u.id
              JOIN agrovets a ON ica.agrovet_id = a.id
              WHERE ica.agrovet_id = '{$staff->agrovet_id}'
              AND ica.status = 'under_review'
              ORDER BY ica.application_date DESC";
    
    $applications = $app->select_all($query);
?>
<div class="card custom-card">
    <div class="card-header justify-content-between">
        <div class="card-title">
            <i class="ri-file-list-3-line me-2"></i> Pending Input Credit Applications
        </div>
        <div class="btn-group">
            <button class="btn btn-outline-primary btn-sm" id="btnShowAll">All</button>
            <button class="btn btn-outline-warning btn-sm" id="btnShowHighScore">High Score</button>
            <button class="btn btn-outline-danger btn-sm" id="btnShowLowScore">Low Score</button>
            <button class="btn btn-outline-success btn-sm" id="btnShowRecent">Recent</button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="datatable-pending-credits" class="table table-bordered text-nowrap w-100">
                <thead>
                    <tr>
                        <th><i class="ri-hash-line me-1"></i>Reference</th>
                        <th><i class="ri-user-line me-1"></i>Farmer</th>
                        <th><i class="ri-store-line me-1"></i>Agrovet</th>
                        <th><i class="ri-money-dollar-circle-line me-1"></i>Amount (KES)</th>
                        <th><i class="ri-percent-line me-1"></i>Interest</th>
                        <th><i class="ri-bar-chart-line me-1"></i>Credit Score</th>
                        <th><i class="ri-time-line me-1"></i>Application Date</th>
                        <th><i class="ri-settings-3-line me-1"></i>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($applications): ?>
                    <?php foreach ($applications as $app): ?>
                    <tr>
                        <td class="fw-semibold">ICREDIT<?php echo str_pad($app->id, 5, '0', STR_PAD_LEFT); ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm bg-success me-2">
                                    <?php echo strtoupper(substr($app->farmer_name, 0, 1)); ?>
                                </span>
                                <span class="fw-medium">
                                    <?php echo htmlspecialchars($app->farmer_name) ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info-transparent text-info">
                                <?php echo htmlspecialchars($app->agrovet_name) ?>
                            </span>
                        </td>
                        <td class="fw-semibold">
                            <span>KES <?php echo number_format($app->total_amount, 2) ?></span>
                            <small class="d-block text-muted">Total: KES
                                <?php echo number_format($app->total_with_interest, 2) ?></small>
                        </td>
                        <td class="text-center"><?php echo $app->credit_percentage ?>%</td>
                        <td>
                            <span class="badge <?php 
                                if ($app->creditworthiness_score >= 80) echo 'bg-success'; 
                                elseif ($app->creditworthiness_score >= 60) echo 'bg-warning';
                                else echo 'bg-danger';
                            ?>">
                                <?php echo number_format($app->creditworthiness_score, 1) ?>
                            </span>
                        </td>
                        <td>
                            <?php echo date('M d, Y', strtotime($app->application_date)) ?>
                            <small class="d-block text-muted">
                                <?php
                                $days_ago = floor((time() - strtotime($app->application_date)) / (60 * 60 * 24));
                                echo $days_ago . ' day' . ($days_ago != 1 ? 's' : '') . ' ago';
                                ?>
                            </small>
                        </td>
                        <td>
                            <div class="d-flex">
                                <button class="btn btn-sm btn-primary me-1" title="View Details"
                                    onclick="viewCreditDetails(<?php echo $app->id ?>)">
                                    <i class="ri-eye-line"></i>
                                </button>
                                <button class="btn btn-sm btn-warning" title="Review Application"
                                    onclick="reviewCreditApplication(<?php echo $app->id ?>)">
                                    <i class="ri-file-search-line"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="ri-information-line fs-2 text-muted mb-2"></i>
                            <p>No pending input credit applications found</p>
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
    var table = $('#datatable-pending-credits').DataTable({
        responsive: true,
        order: [
            [6, 'desc']
        ], // Sort by date
        language: {
            searchPlaceholder: 'Search applications...',
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

    // Filter buttons
    $('#btnShowAll').click(function() {
        table.search('').draw();
    });

    $('#btnShowHighScore').click(function() {
        table.column(5).search('80|90|100', true, false).draw();
    });

    $('#btnShowLowScore').click(function() {
        table.column(5).search('^[0-5]', true, false).draw();
    });

    $('#btnShowRecent').click(function() {
        // Filter for applications in the last 3 days
        var threeDaysAgo = new Date();
        threeDaysAgo.setDate(threeDaysAgo.getDate() - 3);

        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                if (settings.nTable.id !== 'datatable-pending-credits') return true;
                var date = new Date(data[6]);
                return date >= threeDaysAgo;
            }
        );

        table.draw();
        $.fn.dataTable.ext.search.pop();
    });

    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
<?php endif; ?>