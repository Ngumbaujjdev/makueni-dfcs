<?php
include "../../config/config.php";
include "../../libs/App.php";

if (isset($_POST['displayActiveCredits'])):
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
    
    // Get stats for the cards
    $statsQuery = "SELECT 
                    COUNT(*) as total_credits,
                    COALESCE(SUM(remaining_balance), 0) as total_outstanding,
                    COALESCE(SUM(total_with_interest), 0) as total_original,
                    COALESCE(AVG(DATEDIFF(NOW(), fulfillment_date)), 0) as avg_duration
                  FROM approved_input_credits aic
                  JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                  WHERE ica.agrovet_id = '{$staff->agrovet_id}'
                  AND aic.status = 'active' 
                  AND aic.remaining_balance > 0";
    
    $stats = $app->select_one($statsQuery);
    
    // Calculate repayment performance (percentage paid overall)
    $totalOriginal = $stats->total_original ?? 0;
    $totalOutstanding = $stats->total_outstanding ?? 0;
    $repaymentPerformance = 0;
    
    if ($totalOriginal > 0) {
        $totalPaid = $totalOriginal - $totalOutstanding;
        $repaymentPerformance = round(($totalPaid / $totalOriginal) * 100, 1);
    }
    
    // Query to get active input credits for this agrovet with remaining balance
    $query = "SELECT 
                aic.id,
                aic.credit_application_id,
                aic.approved_amount,
                aic.credit_percentage,
                aic.total_with_interest,
                aic.repayment_percentage,
                aic.remaining_balance,
                aic.fulfillment_date,
                aic.status,
                ica.farmer_id,
                ica.agrovet_id,
                ica.status as application_status,
                CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                f.registration_number as farmer_reg,
                DATEDIFF(NOW(), aic.fulfillment_date) as active_days,
                a.name as agrovet_name
              FROM approved_input_credits aic
              JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
              JOIN farmers f ON ica.farmer_id = f.id
              JOIN users u ON f.user_id = u.id
              JOIN agrovets a ON ica.agrovet_id = a.id
              WHERE ica.agrovet_id = '{$staff->agrovet_id}'
              AND aic.status = 'active'
              AND aic.remaining_balance > 0
              ORDER BY aic.fulfillment_date ASC";
    
    $activeCredits = $app->select_all($query);
?>

<!-- Active Input Credits Table Card -->
<div class="card custom-card mt-4">
    <div class="card-header justify-content-between">
        <div class="card-title">
            <i class="ri-shopping-bag-line me-2"></i> Active Input Credits
        </div>
        <div class="btn-group">
            <button class="btn btn-outline-primary btn-sm" id="btnShowAll">All</button>
            <button class="btn btn-outline-warning btn-sm" id="btnShowHighBalance">High Balance</button>
            <button class="btn btn-outline-danger btn-sm" id="btnShowLongstanding">Longstanding</button>
            <button class="btn btn-outline-success btn-sm" id="btnShowNearCompletion">Near Completion</button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="datatable-active-credits" class="table table-bordered text-nowrap w-100">
                <thead>
                    <tr>
                        <th><i class="ri-hash-line me-1"></i>Credit ID</th>
                        <th><i class="ri-user-line me-1"></i>Farmer</th>
                        <th><i class="ri-money-dollar-circle-line me-1"></i>Original Amount</th>
                        <th><i class="ri-money-dollar-circle-line me-1"></i>Remaining Balance</th>
                        <th><i class="ri-percent-line me-1"></i>Repayment Progress</th>
                        <th><i class="ri-calendar-check-line me-1"></i>Fulfillment Date</th>
                        <th><i class="ri-time-line me-1"></i>Active Duration</th>
                        <th><i class="ri-settings-3-line me-1"></i>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($activeCredits): ?>
                    <?php foreach ($activeCredits as $credit): ?>
                    <?php 
                        // Calculate percentage paid
                        $percentPaid = 0;
                        if ($credit->total_with_interest > 0) {
                            $amountPaid = $credit->total_with_interest - $credit->remaining_balance;
                            $percentPaid = ($amountPaid / $credit->total_with_interest) * 100;
                        }
                        
                        // Determine badge color based on percentage paid
                        $badgeClass = 'bg-danger';
                        if ($percentPaid >= 80) {
                            $badgeClass = 'bg-success';
                        } elseif ($percentPaid >= 50) {
                            $badgeClass = 'bg-info';
                        } elseif ($percentPaid >= 30) {
                            $badgeClass = 'bg-warning';
                        }
                        
                        // Determine badge color for active duration
                        $durationBadgeClass = 'bg-info';
                        if ($credit->active_days > 90) {
                            $durationBadgeClass = 'bg-danger';
                        } elseif ($credit->active_days > 60) {
                            $durationBadgeClass = 'bg-warning';
                        } elseif ($credit->active_days > 30) {
                            $durationBadgeClass = 'bg-primary';
                        }
                    ?>
                    <tr>
                        <td class="fw-semibold">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs bg-success me-2 rounded-circle">
                                    <i class="ri-checkbox-circle-fill"></i>
                                </span>
                                <div>
                                    <span
                                        class="d-block">ICREDIT<?php echo str_pad($credit->id, 5, '0', STR_PAD_LEFT); ?></span>
                                    <small class="text-muted">App:
                                        #<?php echo $credit->credit_application_id; ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm bg-success me-2">
                                    <?php echo strtoupper(substr($credit->farmer_name, 0, 1)); ?>
                                </span>
                                <div>
                                    <span class="fw-medium d-block">
                                        <?php echo htmlspecialchars($credit->farmer_name) ?>
                                    </span>
                                    <small class="text-muted">
                                        Reg: <?php echo htmlspecialchars($credit->farmer_reg) ?>
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td class="fw-semibold">
                            <div>
                                <span class="text-dark">
                                    KES <?php echo number_format($credit->total_with_interest, 2) ?>
                                </span>
                                <div class="d-flex align-items-center mt-1">
                                    <span class="badge bg-primary-transparent me-1">
                                        <i class="ri-bank-card-line me-1"></i><?php echo $credit->credit_percentage ?>%
                                    </span>
                                    <span class="badge bg-info-transparent">
                                        <i class="ri-refund-line me-1"></i><?php echo $credit->repayment_percentage ?>%
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="fw-semibold">
                            <div class="d-flex align-items-center">
                                <span
                                    class="avatar avatar-xs <?php echo ($credit->remaining_balance > ($credit->total_with_interest / 2)) ? 'bg-danger' : 'bg-warning'; ?> me-2">
                                    <i class="ri-funds-line"></i>
                                </span>
                                <div>
                                    <span class="d-block">KES
                                        <?php echo number_format($credit->remaining_balance, 2) ?></span>
                                    <small class="text-muted">
                                        KES
                                        <?php echo number_format($credit->total_with_interest - $credit->remaining_balance, 2) ?>
                                        paid
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="progress-stacked mb-2" style="height: 8px;">
                                <div class="progress" role="progressbar" style="width: <?php echo $percentPaid; ?>%"
                                    aria-valuenow="<?php echo $percentPaid; ?>" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar bg-success"></div>
                                </div>
                                <div class="progress" role="progressbar"
                                    style="width: <?php echo 100 - $percentPaid; ?>%"
                                    aria-valuenow="<?php echo 100 - $percentPaid; ?>" aria-valuemin="0"
                                    aria-valuemax="100">
                                    <div class="progress-bar bg-transparent"></div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge <?php echo $badgeClass; ?>-transparent">
                                    <?php echo number_format($percentPaid, 1); ?>% Complete
                                </span>
                                <?php if ($percentPaid < 25): ?>
                                <span class="badge bg-danger-transparent">Just Started</span>
                                <?php elseif ($percentPaid >= 75): ?>
                                <span class="badge bg-success-transparent">Nearly Complete</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs bg-primary me-2 rounded-circle">
                                    <i class="ri-calendar-check-fill"></i>
                                </span>
                                <div>
                                    <span
                                        class="d-block"><?php echo date('M d, Y', strtotime($credit->fulfillment_date)) ?></span>
                                    <small class="text-muted">
                                        <?php echo ucfirst($credit->application_status); ?> application
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column align-items-center">
                                <span class="badge <?php echo $durationBadgeClass; ?>-transparent mb-1">
                                    <?php echo $credit->active_days; ?> days
                                </span>
                                <?php if ($credit->active_days > 90): ?>
                                <small class="text-danger">Long outstanding</small>
                                <?php elseif ($credit->active_days < 15): ?>
                                <small class="text-success">Recently approved</small>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary w-100" title="View Details"
                                onclick="viewCreditDetails(<?php echo $credit->credit_application_id ?>)">
                                <i class="ri-eye-line me-1"></i> View Details
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="ri-information-line fs-2 text-muted mb-2 d-block"></i>
                            <p class="mb-1">No active input credits found</p>
                            <small class="text-muted">All credits have been fully repaid or no credits have been issued
                                yet.</small>
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
    var table = $('#datatable-active-credits').DataTable({
        responsive: true,
        order: [
            [6, 'desc']
        ], // Sort by active duration
        language: {
            searchPlaceholder: 'Search credits...',
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

    $('#btnShowHighBalance').click(function() {
        // Get the average remaining balance
        var avgBalance = 0;
        var count = 0;
        table.column(3).data().each(function(value) {
            var numValue = parseFloat(value.replace(/[^0-9.-]+/g, ""));
            if (!isNaN(numValue)) {
                avgBalance += numValue;
                count++;
            }
        });
        avgBalance = count > 0 ? avgBalance / count : 0;

        // Filter for balances above average
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                if (settings.nTable.id !== 'datatable-active-credits') return true;
                var balance = parseFloat(data[3].replace(/[^0-9.-]+/g, ""));
                return balance > avgBalance;
            }
        );

        table.draw();
        $.fn.dataTable.ext.search.pop();
    });

    $('#btnShowLongstanding').click(function() {
        // Filter for credits active for more than 60 days
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                if (settings.nTable.id !== 'datatable-active-credits') return true;
                var days = parseInt(data[6].match(/\d+/)[0]);
                return days > 60;
            }
        );

        table.draw();
        $.fn.dataTable.ext.search.pop();
    });

    $('#btnShowNearCompletion').click(function() {
        // Filter for credits that are at least 75% paid
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                if (settings.nTable.id !== 'datatable-active-credits') return true;
                var percentPaid = parseFloat(data[4].match(/[\d.]+/)[0]);
                return percentPaid >= 75;
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