<?php
include "../../config/config.php";
include "../../libs/App.php";

if (isset($_POST['displayInputCreditAnalytics'])):
    $app = new App;
    
    // Get the agrovet_id for the logged-in user
    $staff_query = "SELECT s.agrovet_id 
                    FROM agrovet_staff s 
                    WHERE s.user_id = " . $_SESSION['user_id'];
    $staff_result = $app->select_one($staff_query);
    $agrovet_id = $staff_result->agrovet_id;
    
    // Get monthly disbursement vs repayment data
    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    $monthlyDisbursements = [];
    $monthlyRepayments = [];
    
    foreach ($months as $index => $month) {
        $monthNum = $index + 1;
        
        // Disbursements for this month
        $query = "SELECT COALESCE(SUM(aic.approved_amount), 0) as total 
                  FROM approved_input_credits aic
                  JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                  WHERE ica.agrovet_id = $agrovet_id
                  AND MONTH(aic.fulfillment_date) = $monthNum 
                  AND YEAR(aic.fulfillment_date) = YEAR(CURRENT_DATE())";
        $result = $app->select_one($query);
        $monthlyDisbursements[] = $result->total ?? 0;
        
        // Repayments for this month
        $query = "SELECT COALESCE(SUM(icr.amount), 0) as total 
                  FROM input_credit_repayments icr
                  JOIN approved_input_credits aic ON icr.approved_credit_id = aic.id
                  JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                  WHERE ica.agrovet_id = $agrovet_id
                  AND MONTH(icr.deduction_date) = $monthNum 
                  AND YEAR(icr.deduction_date) = YEAR(CURRENT_DATE())";
        $result = $app->select_one($query);
        $monthlyRepayments[] = $result->total ?? 0;
    }
    
    // Get breakdown of input types
    $query = "SELECT 
                ici.input_type, 
                COUNT(*) as count, 
                SUM(ici.total_price) as total_amount
              FROM input_credit_items ici
              JOIN input_credit_applications ica ON ici.credit_application_id = ica.id
              WHERE ica.agrovet_id = $agrovet_id
              GROUP BY ici.input_type
              ORDER BY total_amount DESC";
    $inputTypes = $app->select_all($query);
    
    // Get performance metrics by farmer category
    $query = "SELECT 
                fc.id as category_id,
                fc.name as category_name,
                COUNT(DISTINCT ica.id) as application_count,
                COUNT(DISTINCT CASE WHEN ica.status IN ('approved', 'fulfilled', 'completed') THEN ica.id END) as approved_count,
                SUM(CASE WHEN aic.id IS NOT NULL THEN aic.approved_amount ELSE 0 END) as total_disbursed,
                SUM(CASE WHEN aic.id IS NOT NULL THEN aic.total_with_interest - aic.remaining_balance ELSE 0 END) as total_repaid,
                CASE 
                    WHEN SUM(CASE WHEN aic.id IS NOT NULL THEN aic.total_with_interest ELSE 0 END) > 0 
                    THEN (SUM(CASE WHEN aic.id IS NOT NULL THEN aic.total_with_interest - aic.remaining_balance ELSE 0 END) / 
                         SUM(CASE WHEN aic.id IS NOT NULL THEN aic.total_with_interest ELSE 0 END)) * 100
                    ELSE 0
                END as repayment_rate
              FROM farmer_categories fc
              LEFT JOIN farmers f ON f.category_id = fc.id
              LEFT JOIN input_credit_applications ica ON ica.farmer_id = f.id AND ica.agrovet_id = $agrovet_id
              LEFT JOIN approved_input_credits aic ON aic.credit_application_id = ica.id
              GROUP BY fc.id, fc.name
              ORDER BY total_disbursed DESC";
    $farmerCategories = $app->select_all($query);
?>

<!-- Disbursement vs Repayment Chart -->
<div class="row">
    <div class="col-md-12 mb-4">
        <h5 class="mb-3"><i class="ri-exchange-funds-line me-2"></i>Credit Disbursement vs Repayment Trends</h5>
        <div id="disbursementRepaymentChart" style="height: 350px;"></div>
    </div>
</div>

<!-- Input Types and Farmer Categories -->
<div class="row">
    <!-- Input Types Breakdown -->
    <div class="col-md-6 mb-4">
        <h5 class="mb-3"><i class="ri-stack-line me-2"></i>Input Types Breakdown</h5>
        <div id="inputTypesChart" style="height: 300px;"></div>
    </div>

    <!-- Farmer Category Performance -->
    <div class="col-md-6 mb-4">
        <h5 class="mb-3"><i class="ri-user-star-line me-2"></i>Performance by Farmer Category</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>Category</th>
                        <th>Applications</th>
                        <th>Disbursed (KES)</th>
                        <th>Repayment Rate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($farmerCategories): ?>
                    <?php foreach ($farmerCategories as $category): ?>
                    <?php if ($category->application_count > 0): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs me-2 
                                                <?php 
                                                    echo match($category->category_id) {
                                                        1 => 'bg-primary',
                                                        2 => 'bg-success',
                                                        3 => 'bg-info',
                                                        default => 'bg-secondary'
                                                    };
                                                ?>">
                                    <i class="ri-user-line"></i>
                                </span>
                                <?php echo htmlspecialchars($category->category_name); ?>
                            </div>
                        </td>
                        <td>
                            <?php echo $category->application_count; ?>
                            <small class="text-muted">(<?php echo $category->approved_count; ?> approved)</small>
                        </td>
                        <td>
                            <strong>KES <?php echo number_format($category->total_disbursed, 2); ?></strong>
                        </td>
                        <td>
                            <?php 
                                            $repaymentRate = round($category->repayment_rate, 1);
                                            $badgeClass = 'bg-danger';
                                            if ($repaymentRate >= 90) {
                                                $badgeClass = 'bg-success';
                                            } elseif ($repaymentRate >= 75) {
                                                $badgeClass = 'bg-info';
                                            } elseif ($repaymentRate >= 50) {
                                                $badgeClass = 'bg-warning';
                                            }
                                        ?>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 me-2" style="height: 5px">
                                    <div class="progress-bar <?php echo $badgeClass; ?>"
                                        style="width: <?php echo $repaymentRate; ?>%" role="progressbar"></div>
                                </div>
                                <span class="badge <?php echo $badgeClass; ?>-transparent">
                                    <?php echo $repaymentRate; ?>%
                                </span>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center py-3">No data available for farmer categories</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Credit Performance Stats -->
<div class="row">
    <div class="col-md-12 mb-2">
        <h5><i class="ri-bar-chart-box-line me-2"></i>Input Credit Performance Summary</h5>
    </div>
    <?php
    // Get overall performance stats
    $query = "SELECT 
               COUNT(DISTINCT ica.id) as total_applications,
               COUNT(DISTINCT CASE WHEN ica.status IN ('approved', 'fulfilled', 'completed') THEN ica.id END) as approved_count,
               COUNT(DISTINCT CASE WHEN ica.status = 'rejected' THEN ica.id END) as rejected_count,
               COUNT(DISTINCT CASE WHEN aic.status = 'completed' THEN aic.id END) as completed_count,
               COUNT(DISTINCT CASE WHEN aic.status = 'active' THEN aic.id END) as active_count,
               SUM(CASE WHEN aic.id IS NOT NULL THEN aic.approved_amount ELSE 0 END) as total_disbursed,
               SUM(CASE WHEN aic.id IS NOT NULL THEN aic.total_with_interest ELSE 0 END) as total_with_interest,
               SUM(CASE WHEN aic.id IS NOT NULL THEN aic.remaining_balance ELSE 0 END) as outstanding_balance,
               SUM(CASE WHEN aic.id IS NOT NULL THEN aic.total_with_interest - aic.remaining_balance ELSE 0 END) as total_repaid
             FROM input_credit_applications ica
             LEFT JOIN approved_input_credits aic ON aic.credit_application_id = ica.id
             WHERE ica.agrovet_id = $agrovet_id";
    $stats = $app->select_one($query);
    
    // Calculate rates
    $approvalRate = ($stats->total_applications > 0) ? ($stats->approved_count / $stats->total_applications) * 100 : 0;
    $repaymentRate = ($stats->total_with_interest > 0) ? ($stats->total_repaid / $stats->total_with_interest) * 100 : 0;
    $completionRate = ($stats->approved_count > 0) ? ($stats->completed_count / $stats->approved_count) * 100 : 0;
    ?>

    <!-- Performance Metrics -->
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card bg-light-subtle">
            <div class="card-body p-3">
                <h6 class="card-title mb-0">Approval Rate</h6>
                <div class="d-flex align-items-center mt-2">
                    <span class="display-6 fw-bold me-2"><?php echo round($approvalRate); ?>%</span>
                    <div class="ms-auto">
                        <span class="avatar avatar-md" style="background-color: #6AA32D;">
                            <i class="ri-check-double-line"></i>
                        </span>
                    </div>
                </div>
                <div class="mt-2">
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-success" style="width: <?php echo $approvalRate; ?>%"></div>
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <small class="text-muted"><?php echo $stats->approved_count; ?> approved</small>
                    <small class="text-muted">of <?php echo $stats->total_applications; ?> applications</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card bg-light-subtle">
            <div class="card-body p-3">
                <h6 class="card-title mb-0">Repayment Rate</h6>
                <div class="d-flex align-items-center mt-2">
                    <span class="display-6 fw-bold me-2"><?php echo round($repaymentRate); ?>%</span>
                    <div class="ms-auto">
                        <span class="avatar avatar-md" style="background-color: #3498DB;">
                            <i class="ri-refund-2-line"></i>
                        </span>
                    </div>
                </div>
                <div class="mt-2">
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-primary" style="width: <?php echo $repaymentRate; ?>%"></div>
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <small class="text-muted">KES <?php echo number_format($stats->total_repaid, 0); ?> repaid</small>
                    <small class="text-muted">of <?php echo number_format($stats->total_with_interest, 0); ?></small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card bg-light-subtle">
            <div class="card-body p-3">
                <h6 class="card-title mb-0">Completion Rate</h6>
                <div class="d-flex align-items-center mt-2">
                    <span class="display-6 fw-bold me-2"><?php echo round($completionRate); ?>%</span>
                    <div class="ms-auto">
                        <span class="avatar avatar-md" style="background-color: #27AE60;">
                            <i class="ri-medal-line"></i>
                        </span>
                    </div>
                </div>
                <div class="mt-2">
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-success" style="width: <?php echo $completionRate; ?>%"></div>
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <small class="text-muted"><?php echo $stats->completed_count; ?> completed</small>
                    <small class="text-muted"><?php echo $stats->active_count; ?> still active</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card bg-light-subtle">
            <div class="card-body p-3">
                <h6 class="card-title mb-0">Outstanding Balance</h6>
                <div class="d-flex align-items-center mt-2">
                    <span class="display-6 fw-bold me-2" style="font-size: 1.5rem;">
                        KES <?php echo number_format($stats->outstanding_balance, 0); ?>
                    </span>
                    <div class="ms-auto">
                        <span class="avatar avatar-md" style="background-color: #E74C3C;">
                            <i class="ri-funds-line"></i>
                        </span>
                    </div>
                </div>
                <div class="mt-2">
                    <?php 
                    $outstandingPercentage = ($stats->total_with_interest > 0) ? 
                                           ($stats->outstanding_balance / $stats->total_with_interest) * 100 : 0;
                    ?>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-danger" style="width: <?php echo $outstandingPercentage; ?>%"></div>
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <small class="text-muted"><?php echo number_format($outstandingPercentage, 1); ?>% of total</small>
                    <small class="text-muted">From <?php echo $stats->active_count; ?> active credits</small>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.30.0/dist/apexcharts.min.js"></script>
<script>
$(document).ready(function() {
    // Disbursement vs Repayment Chart
    const disbursementRepaymentOptions = {
        series: [{
            name: 'Disbursements',
            type: 'column',
            data: [<?php echo implode(',', $monthlyDisbursements); ?>]
        }, {
            name: 'Repayments',
            type: 'area',
            data: [<?php echo implode(',', $monthlyRepayments); ?>]
        }],
        chart: {
            height: 350,
            type: 'line',
            stacked: false,
            toolbar: {
                show: true
            }
        },
        stroke: {
            width: [0, 3],
            curve: 'smooth'
        },
        plotOptions: {
            bar: {
                columnWidth: '50%'
            }
        },
        colors: ['#6AA32D', '#3498DB'],
        fill: {
            opacity: [1, 0.3],
            gradient: {
                inverseColors: false,
                shade: 'light',
                type: "vertical",
                opacityFrom: 0.85,
                opacityTo: 0.55,
                stops: [0, 100, 100, 100]
            }
        },
        labels: <?php echo json_encode($months); ?>,
        markers: {
            size: 0
        },
        xaxis: {
            title: {
                text: 'Month'
            }
        },
        yaxis: {
            title: {
                text: 'Amount (KES)',
            },
            labels: {
                formatter: function(val) {
                    return 'KES ' + val.toLocaleString();
                }
            }
        },
        tooltip: {
            shared: true,
            intersect: false,
            y: {
                formatter: function(val) {
                    return 'KES ' + val.toLocaleString();
                }
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'left'
        },
        grid: {
            borderColor: '#f1f1f1'
        }
    };

    const disbursementRepaymentChart = new ApexCharts(document.querySelector("#disbursementRepaymentChart"),
        disbursementRepaymentOptions);
    disbursementRepaymentChart.render();

    // Input Types Breakdown Chart
    const inputTypesOptions = {
        series: [
            <?php foreach ($inputTypes as $type): ?>
            <?php echo $type->total_amount; ?>,
            <?php endforeach; ?>
        ],
        chart: {
            type: 'donut',
            height: 300
        },
        labels: [
            <?php foreach ($inputTypes as $type): ?> "<?php echo ucfirst($type->input_type); ?>",
            <?php endforeach; ?>
        ],
        colors: ['#6AA32D', '#3498DB', '#E74C3C', '#F39C12', '#9B59B6'],
        plotOptions: {
            pie: {
                donut: {
                    size: '50%',
                    labels: {
                        show: true,
                        name: {
                            show: true,
                            formatter: function(val) {
                                return val;
                            }
                        },
                        value: {
                            show: true,
                            formatter: function(val) {
                                return 'KES ' + val.toLocaleString();
                            }
                        },
                        total: {
                            show: true,
                            label: 'Total',
                            formatter: function(w) {
                                const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                return 'KES ' + total.toLocaleString();
                            }
                        }
                    }
                }
            }
        },
        legend: {
            position: 'bottom',
            formatter: function(seriesName, opts) {
                return [seriesName, ' - ', opts.w.globals.series[opts.seriesIndex].toLocaleString()]
                    .join('');
            }
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 300
                },
                legend: {
                    position: 'bottom'
                }
            }
        }],
        tooltip: {
            y: {
                formatter: function(val) {
                    return 'KES ' + val.toLocaleString();
                }
            }
        }
    };

    const inputTypesChart = new ApexCharts(document.querySelector("#inputTypesChart"), inputTypesOptions);
    inputTypesChart.render();
});
</script>
<?php endif; ?>