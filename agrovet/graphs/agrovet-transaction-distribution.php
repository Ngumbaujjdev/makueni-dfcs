<div id="monthlyAgrovetTransactionMetricsChart"></div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.30.0/dist/apexcharts.min.js"></script>
<script>
<?php
$app = new App();

// Get agrovet_id for the current staff
$staffQuery = "SELECT s.agrovet_id 
              FROM agrovet_staff s 
              WHERE s.user_id = {$_SESSION['user_id']}";
$staffResult = $app->select_one($staffQuery);
$agrovetId = $staffResult->agrovet_id ?? 0;

// Get agrovet account id
$accountQuery = "SELECT id FROM agrovet_accounts WHERE agrovet_id = $agrovetId";
$accountResult = $app->select_one($accountQuery);
$agrovetAccountId = $accountResult->id ?? 0;

// Get monthly transaction metrics
$months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$creditTransactionCounts = [];
$debitTransactionCounts = [];
$creditTransactionAmounts = [];
$debitTransactionAmounts = [];

foreach ($months as $index => $month) {
    $monthNum = $index + 1;
    
    // Credit transaction counts (money in)
    $query = "SELECT COUNT(*) as count, COALESCE(SUM(amount), 0) as total 
              FROM agrovet_account_transactions 
              WHERE agrovet_account_id = $agrovetAccountId 
              AND transaction_type = 'credit'
              AND MONTH(created_at) = $monthNum 
              AND YEAR(created_at) = YEAR(CURRENT_DATE())";
    $result = $app->select_one($query);
    $creditTransactionCounts[] = $result->count ?? 0;
    $creditTransactionAmounts[] = $result->total ?? 0;
    
    // Debit transaction counts (money out)
    $query = "SELECT COUNT(*) as count, COALESCE(SUM(amount), 0) as total 
              FROM agrovet_account_transactions 
              WHERE agrovet_account_id = $agrovetAccountId 
              AND transaction_type = 'debit'
              AND MONTH(created_at) = $monthNum 
              AND YEAR(created_at) = YEAR(CURRENT_DATE())";
    $result = $app->select_one($query);
    $debitTransactionCounts[] = $result->count ?? 0;
    $debitTransactionAmounts[] = $result->total ?? 0;
}

// Get total metrics for the year
$totalCreditTransactions = array_sum($creditTransactionCounts);
$totalDebitTransactions = array_sum($debitTransactionCounts);
$totalCreditAmount = array_sum($creditTransactionAmounts);
$totalDebitAmount = array_sum($debitTransactionAmounts);

// Get input credit stats for this agrovet
$creditStatsQuery = "SELECT 
                      COUNT(*) as total_credits,
                      SUM(CASE WHEN aic.status = 'active' THEN 1 ELSE 0 END) as active_credits,
                      SUM(CASE WHEN aic.status = 'completed' THEN 1 ELSE 0 END) as completed_credits,
                      SUM(aic.remaining_balance) as outstanding_balance
                    FROM approved_input_credits aic
                    JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                    WHERE ica.agrovet_id = $agrovetId";
$creditStats = $app->select_one($creditStatsQuery);
?>

// Add yearly metrics above the chart
document.write(`
<div class="row mb-3">
    <div class="col-md-3 col-sm-6">
        <div class="card bg-light mb-3">
            <div class="card-body py-2">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <span class="avatar avatar-sm" style="background-color: #6AA32D;">
                            <i class="ri-arrow-down-line"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Total Credits</h6>
                        <h3 class="mb-0"><?php echo $totalCreditTransactions; ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6">
        <div class="card bg-light mb-3">
            <div class="card-body py-2">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <span class="avatar avatar-sm" style="background-color: #E74C3C;">
                            <i class="ri-arrow-up-line"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Total Debits</h6>
                        <h3 class="mb-0"><?php echo $totalDebitTransactions; ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6">
        <div class="card bg-light mb-3">
            <div class="card-body py-2">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <span class="avatar avatar-sm" style="background-color: #3498DB;">
                            <i class="ri-credit-card-line"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Active Credits</h6>
                        <h3 class="mb-0"><?php echo $creditStats->active_credits; ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6">
        <div class="card bg-light mb-3">
            <div class="card-body py-2">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <span class="avatar avatar-sm" style="background-color: #6AA32D;">
                            <i class="ri-check-double-line"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Completed Credits</h6>
                        <h3 class="mb-0"><?php echo $creditStats->completed_credits; ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add KES metrics row -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-light mb-3">
            <div class="card-body py-2">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <span class="avatar avatar-sm" style="background-color: #6AA32D;">
                            <i class="ri-funds-line"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Total Credits</h6>
                        <h3 class="mb-0">KES <?php echo number_format($totalCreditAmount, 2); ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-light mb-3">
            <div class="card-body py-2">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <span class="avatar avatar-sm" style="background-color: #E74C3C;">
                            <i class="ri-money-dollar-box-line"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Total Debits</h6>
                        <h3 class="mb-0">KES <?php echo number_format($totalDebitAmount, 2); ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-light mb-3">
            <div class="card-body py-2">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <span class="avatar avatar-sm" style="background-color: #3498DB;">
                            <i class="ri-shopping-bag-line"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Outstanding Balance</h6>
                        <h3 class="mb-0">KES <?php echo number_format($creditStats->outstanding_balance, 2); ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
`);

// Create chart for transaction counts
var options = {
    series: [{
        name: 'Credit Transactions',
        data: [<?php echo implode(',', $creditTransactionCounts); ?>]
    }, {
        name: 'Debit Transactions',
        data: [<?php echo implode(',', $debitTransactionCounts); ?>]
    }],
    chart: {
        type: 'bar',
        height: 350,
        stacked: false,
        toolbar: {
            show: true,
            tools: {
                download: true,
                selection: false,
                zoom: false,
                zoomin: false,
                zoomout: false,
                pan: false,
                reset: false
            }
        },
        zoom: {
            enabled: false
        }
    },
    responsive: [{
        breakpoint: 480,
        options: {
            legend: {
                position: 'bottom',
                offsetX: -10,
                offsetY: 0
            }
        }
    }],
    plotOptions: {
        bar: {
            horizontal: false,
            borderRadius: 5,
            columnWidth: '55%',
            endingShape: 'rounded'
        },
    },
    colors: ['#6AA32D', '#E74C3C'],
    dataLabels: {
        enabled: false
    },
    stroke: {
        show: true,
        width: 2,
        colors: ['transparent']
    },
    xaxis: {
        categories: <?php echo json_encode($months); ?>,
        title: {
            text: 'Month'
        }
    },
    yaxis: {
        title: {
            text: 'Number of Transactions'
        }
    },
    legend: {
        position: 'bottom',
        horizontalAlign: 'center',
        floating: false,
        offsetY: 0,
        offsetX: 0
    },
    fill: {
        opacity: 1
    },
    tooltip: {
        y: {
            formatter: function(val) {
                return val + " Transactions"
            }
        }
    },
    grid: {
        borderColor: '#f1f1f1',
        strokeDashArray: 4,
        xaxis: {
            lines: {
                show: false
            }
        }
    },
    states: {
        hover: {
            filter: {
                type: 'darken',
                value: 0.9
            }
        }
    }
};

var chart = new ApexCharts(document.querySelector("#monthlyAgrovetTransactionMetricsChart"), options);
chart.render();

// Add a line chart for amounts below the bars
document.write(`
<div class="mt-4">
    <h5 class="text-center">Monthly Transaction Amounts</h5>
    <div id="monthlyTransactionAmountsChart"></div>
</div>
`);

var amountsOptions = {
    series: [{
        name: 'Credit Amounts',
        type: 'column',
        data: [<?php echo implode(',', $creditTransactionAmounts); ?>]
    }, {
        name: 'Debit Amounts',
        type: 'column',
        data: [<?php echo implode(',', $debitTransactionAmounts); ?>]
    }],
    chart: {
        height: 350,
        type: 'line',
        toolbar: {
            show: true,
            tools: {
                download: true
            }
        }
    },
    stroke: {
        width: [0, 0]
    },
    colors: ['#6AA32D', '#E74C3C'],
    dataLabels: {
        enabled: false
    },
    labels: <?php echo json_encode($months); ?>,
    xaxis: {
        title: {
            text: 'Month'
        }
    },
    yaxis: [{
        title: {
            text: 'Amount (KES)',
        },
        labels: {
            formatter: function(val) {
                return 'KES ' + val.toLocaleString();
            }
        }
    }],
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
        position: 'bottom',
        horizontalAlign: 'center'
    }
};

var amountsChart = new ApexCharts(document.querySelector("#monthlyTransactionAmountsChart"), amountsOptions);
amountsChart.render();
</script>