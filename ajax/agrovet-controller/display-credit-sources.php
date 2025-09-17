<?php
include "../../config/config.php";
include "../../libs/App.php";

if (isset($_POST['displayCreditSources'])):
    $app = new App;
    
    // Get the agrovet_id for the logged-in user
    $staff_query = "SELECT s.agrovet_id 
                    FROM agrovet_staff s 
                    WHERE s.user_id = " . $_SESSION['user_id'];
    $staff_result = $app->select_one($staff_query);
    $agrovet_id = $staff_result->agrovet_id;
    
    // Get top input credit types
    $query = "SELECT 
                ici.input_type, 
                COUNT(*) as credit_count, 
                SUM(ici.total_price) as total_amount,
                (SUM(ici.total_price) / (
                    SELECT SUM(ici2.total_price) 
                    FROM input_credit_items ici2
                    JOIN input_credit_applications ica2 ON ici2.credit_application_id = ica2.id
                    WHERE ica2.agrovet_id = $agrovet_id
                )) * 100 as percentage
              FROM input_credit_items ici
              JOIN input_credit_applications ica ON ici.credit_application_id = ica.id
              WHERE ica.agrovet_id = $agrovet_id
              GROUP BY ici.input_type
              ORDER BY total_amount DESC
              LIMIT 4";
    
    $creditSources = $app->select_all($query);
    
    // Get total credit amount
    $query = "SELECT 
               SUM(ici.total_price) as total_credits 
               FROM input_credit_items ici
               JOIN input_credit_applications ica ON ici.credit_application_id = ica.id
               WHERE ica.agrovet_id = $agrovet_id";
    $totalCreditsResult = $app->select_one($query);
    $totalCredits = $totalCreditsResult->total_credits ?? 0;
    
    // Get top staff by credit volume
    $query = "SELECT 
                u.id,
                CONCAT(u.first_name, ' ', u.last_name) as staff_name,
                COUNT(ica.id) as credit_count,
                SUM(ica.total_amount) as total_credits
              FROM input_credit_applications ica
              JOIN approved_input_credits aic ON aic.credit_application_id = ica.id
              JOIN agrovet_staff ast ON aic.approved_by = ast.id
              JOIN users u ON ast.user_id = u.id
              WHERE ica.agrovet_id = $agrovet_id
              GROUP BY u.id
              ORDER BY credit_count DESC
              LIMIT 3";
    
    $topStaff = $app->select_all($query);
?>
<div class="card custom-card">
    <div class="card-header justify-content-between">
        <div class="card-title">
            <i class="ri-pie-chart-line me-2"></i> Input Credit Analysis
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Left side: Credit Types Pie Chart -->
            <div class="col-lg-7">
                <h6 class="mb-3 text-muted fw-semibold"><i class="ri-funds-line me-1"></i> Input Credit Distribution by
                    Type</h6>
                <div id="credit-types-chart" style="height: 250px;"></div>
            </div>

            <!-- Right side: Top Staff & Additional Metrics -->
            <div class="col-lg-5">
                <h6 class="mb-3 text-muted fw-semibold"><i class="ri-user-star-line me-1"></i> Top Staff by Credit
                    Volume</h6>

                <?php if ($topStaff): ?>
                <?php foreach ($topStaff as $index => $staff): ?>
                <div class="d-flex align-items-center mt-3">
                    <span class="avatar avatar-sm <?php echo 'bg-' . ['primary', 'success', 'info'][$index]; ?> me-3">
                        <?php echo strtoupper(substr($staff->staff_name, 0, 1)); ?>
                    </span>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <h6 class="mb-0"><?php echo htmlspecialchars($staff->staff_name); ?></h6>
                            <span class="badge bg-light text-dark"><?php echo $staff->credit_count; ?> credits</span>
                        </div>
                        <div class="progress progress-sm">
                            <?php 
                            // Calculate percentage of max value (for progress bar)
                            $maxCredits = max(array_column($topStaff, 'credit_count'));
                            $percentage = ($staff->credit_count / $maxCredits) * 100;
                            ?>
                            <div class="progress-bar <?php echo 'bg-' . ['primary', 'success', 'info'][$index]; ?>"
                                style="width: <?php echo $percentage; ?>%" role="progressbar"
                                aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <small class="text-muted">KES <?php echo number_format($staff->total_credits, 0); ?>
                            approved</small>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <div class="text-center py-4">
                    <i class="ri-information-line fs-2 text-muted mb-2 d-block"></i>
                    <p class="mb-0">No staff credit data available</p>
                </div>
                <?php endif; ?>

                <div class="d-grid mt-4">
                    <a href="credit-analysis" class="btn btn-outline-primary btn-sm">
                        <i class="ri-bar-chart-box-line me-1"></i> View Detailed Analysis
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.30.0/dist/apexcharts.min.js"></script>
<script>
$(document).ready(function() {
    // Prepare data for pie chart
    const sourcesData = [
        <?php foreach ($creditSources as $source): ?> {
            name: "<?php echo ucfirst(addslashes($source->input_type)); ?>",
            value: <?php echo round($source->total_amount, 2); ?>,
            percentage: <?php echo round($source->percentage, 1); ?>
        },
        <?php endforeach; ?>
    ];

    // Calculate "Other" category if needed
    let totalPercentage = sourcesData.reduce((total, item) => total + item.percentage, 0);
    if (totalPercentage < 100) {
        sourcesData.push({
            name: "Other",
            value: <?php echo round($totalCredits, 2); ?> - sourcesData.reduce((total, item) => total +
                item.value, 0),
            percentage: 100 - totalPercentage
        });
    }

    // Create the pie chart
    const pieOptions = {
        series: sourcesData.map(item => item.value),
        chart: {
            type: 'donut',
            height: 250
        },
        labels: sourcesData.map(item => item.name),
        colors: ['#6AA32D', '#3498DB', '#E67E22', '#9B59B6', '#34495E'],
        legend: {
            position: 'bottom',
            horizontalAlign: 'center',
            fontSize: '14px',
            markers: {
                width: 10,
                height: 10,
                radius: 2
            },
            itemMargin: {
                horizontal: 10,
                vertical: 5
            }
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '50%',
                    labels: {
                        show: true,
                        name: {
                            show: true,
                            fontSize: '16px',
                            fontWeight: 600,
                            offsetY: 0
                        },
                        value: {
                            show: true,
                            fontSize: '14px',
                            fontWeight: 400,
                            formatter: function(val) {
                                return 'KES ' + new Intl.NumberFormat().format(Math.round(val));
                            }
                        },
                        total: {
                            show: true,
                            label: 'Total Credits',
                            fontSize: '16px',
                            fontWeight: 600,
                            formatter: function(w) {
                                return 'KES ' + new Intl.NumberFormat().format(Math.round(
                                    <?php echo $totalCredits; ?>));
                            }
                        }
                    }
                }
            }
        },
        tooltip: {
            y: {
                formatter: function(value, {
                    series,
                    seriesIndex,
                    dataPointIndex,
                    w
                }) {
                    return 'KES ' + new Intl.NumberFormat().format(Math.round(value)) +
                        ' (' + sourcesData[seriesIndex].percentage.toFixed(1) + '%)';
                }
            }
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    height: 270
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };

    const pieChart = new ApexCharts(document.querySelector("#credit-types-chart"), pieOptions);
    pieChart.render();
});
</script>
<?php endif; ?>