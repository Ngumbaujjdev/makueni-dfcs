<?php
include "../../config/config.php";
include "../../libs/App.php";

if (isset($_POST['displayTransactionSources'])):
    $app = new App;
    
    // Get top commission sources
    $query = "SELECT 
                description, 
                COUNT(*) as transaction_count, 
                SUM(amount) as total_amount,
                (SUM(amount) / (SELECT SUM(amount) FROM sacco_account_transactions WHERE transaction_type = 'credit')) * 100 as percentage
              FROM sacco_account_transactions 
              WHERE transaction_type = 'credit'
              GROUP BY description
              ORDER BY total_amount DESC
              LIMIT 4";
    
    $transactionSources = $app->select_all($query);
    
    // Get total credits
    $query = "SELECT SUM(amount) as total_credits FROM sacco_account_transactions WHERE transaction_type = 'credit'";
    $totalCreditsResult = $app->select_one($query);
    $totalCredits = $totalCreditsResult->total_credits ?? 0;
    
    // Get top staff by transaction volume
    $query = "SELECT 
                u.id,
                CONCAT(u.first_name, ' ', u.last_name) as staff_name,
                COUNT(*) as transaction_count,
                SUM(CASE WHEN sat.transaction_type = 'credit' THEN sat.amount ELSE 0 END) as total_credits
              FROM sacco_account_transactions sat
              JOIN users u ON sat.processed_by = u.id
              GROUP BY sat.processed_by
              ORDER BY transaction_count DESC
              LIMIT 3";
    
    $topStaff = $app->select_all($query);
?>
<div class="card custom-card">
    <div class="card-header justify-content-between">
        <div class="card-title">
            <i class="ri-pie-chart-line me-2"></i> Transaction Source Analysis
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Left side: Income Sources Pie Chart -->
            <div class="col-lg-7">
                <h6 class="mb-3 text-muted fw-semibold"><i class="ri-funds-line me-1"></i> Commission Sources</h6>
                <div id="income-sources-chart" style="height: 250px;"></div>
            </div>

            <!-- Right side: Top Staff & Additional Metrics -->
            <div class="col-lg-5">
                <h6 class="mb-3 text-muted fw-semibold"><i class="ri-user-star-line me-1"></i> Top Staff by Transaction
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
                            <span class="badge bg-light text-dark"><?php echo $staff->transaction_count; ?> txns</span>
                        </div>
                        <div class="progress progress-sm">
                            <?php 
                            // Calculate percentage of max value (for progress bar)
                            $maxTransactions = max(array_column($topStaff, 'transaction_count'));
                            $percentage = ($staff->transaction_count / $maxTransactions) * 100;
                            ?>
                            <div class="progress-bar <?php echo 'bg-' . ['primary', 'success', 'info'][$index]; ?>"
                                style="width: <?php echo $percentage; ?>%" role="progressbar"
                                aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <small class="text-muted">KES <?php echo number_format($staff->total_credits, 0); ?>
                            processed</small>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <div class="text-center py-4">
                    <i class="ri-information-line fs-2 text-muted mb-2 d-block"></i>
                    <p class="mb-0">No staff transaction data available</p>
                </div>
                <?php endif; ?>

                <div class="d-grid mt-4">
                    <a href="transaction-analysis" class="btn btn-outline-primary btn-sm">
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
        <?php foreach ($transactionSources as $source): ?> {
            name: "<?php echo addslashes($source->description); ?>",
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

    const pieChart = new ApexCharts(document.querySelector("#income-sources-chart"), pieOptions);
    pieChart.render();
});
</script>
<?php endif; ?>