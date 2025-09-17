   <div id="monthlyAccountMetricsChart"></div>

   <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.30.0/dist/apexcharts.min.js">
   </script>
   <script>
<?php
                $app = new App();

                // Get monthly transaction totals for the current year
                $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                $creditAmounts = [];
                $debitAmounts = [];
                $netChangeAmounts = [];
                $transactionCounts = [];

                foreach ($months as $index => $month) {
                    $monthNum = $index + 1;
                    
                    // Credit amounts for month
                    $query = "SELECT COALESCE(SUM(amount), 0) as total 
                              FROM sacco_account_transactions 
                              WHERE transaction_type = 'credit'
                              AND MONTH(created_at) = $monthNum 
                              AND YEAR(created_at) = YEAR(CURRENT_DATE())";
                    $result = $app->select_one($query);
                    $creditAmounts[] = round($result->total ?? 0, 2);
                    
                    // Debit amounts for month
                    $query = "SELECT COALESCE(SUM(amount), 0) as total 
                              FROM sacco_account_transactions 
                              WHERE transaction_type = 'debit'
                              AND MONTH(created_at) = $monthNum 
                              AND YEAR(created_at) = YEAR(CURRENT_DATE())";
                    $result = $app->select_one($query);
                    $debitAmounts[] = round($result->total ?? 0, 2);
                    
                    // Calculate net change
                    $netChangeAmounts[] = $creditAmounts[$index] - $debitAmounts[$index];
                    
                    // Transaction count
                    $query = "SELECT COUNT(*) as count 
                              FROM sacco_account_transactions 
                              WHERE MONTH(created_at) = $monthNum 
                              AND YEAR(created_at) = YEAR(CURRENT_DATE())";
                    $result = $app->select_one($query);
                    $transactionCounts[] = $result->count ?? 0;
                }

                // Calculate yearly totals
                $totalCredits = array_sum($creditAmounts);
                $totalDebits = array_sum($debitAmounts);
                $totalNet = $totalCredits - $totalDebits;
                $totalTransactions = array_sum($transactionCounts);
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
                                        <h3 class="mb-0">KES <?php echo number_format($totalCredits, 0); ?></h3>
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
                                        <h3 class="mb-0">KES <?php echo number_format($totalDebits, 0); ?></h3>
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
                                            <i class="ri-exchange-line"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-0">Net Change</h6>
                                        <h3 class="mb-0">KES <?php echo number_format($totalNet, 0); ?></h3>
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
                                            <i class="ri-file-list-3-line"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-0">Transactions</h6>
                                        <h3 class="mb-0"><?php echo number_format($totalTransactions); ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                `);

var options = {
    series: [{
            name: 'Credits',
            type: 'column',
            data: [<?php echo implode(',', $creditAmounts); ?>]
        },
        {
            name: 'Debits',
            type: 'column',
            data: [<?php echo implode(',', $debitAmounts); ?>]
        },
        {
            name: 'Net Change',
            type: 'line',
            data: [<?php echo implode(',', $netChangeAmounts); ?>]
        }
    ],
    chart: {
        height: 350,
        type: 'line',
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
        }
    },
    stroke: {
        width: [0, 0, 3],
        curve: 'smooth'
    },
    plotOptions: {
        bar: {
            columnWidth: '50%',
            borderRadius: 5
        }
    },
    colors: ['#6AA32D', '#E74C3C', '#3498DB'],
    dataLabels: {
        enabled: false
    },
    fill: {
        opacity: [0.85, 0.85, 1],
        gradient: {
            inverseColors: false,
            shade: 'light',
            type: "vertical",
            opacityFrom: 0.85,
            opacityTo: 0.55,
            stops: [0, 100, 100, 100]
        }
    },
    markers: {
        size: 4,
        strokeColors: '#fff',
        strokeWidth: 2,
        hover: {
            size: 7,
        }
    },
    labels: <?php echo json_encode($months); ?>,
    xaxis: {
        title: {
            text: 'Month'
        }
    },
    yaxis: [{
        title: {
            text: 'Amount (KES)'
        },
        labels: {
            formatter: function(val) {
                return 'KES ' + val.toFixed(0).toString().replace(
                    /\B(?=(\d{3})+(?!\d))/g, ",");
            }
        }
    }],
    tooltip: {
        shared: true,
        intersect: false,
        y: {
            formatter: function(val) {
                return 'KES ' + val.toFixed(0).toString().replace(
                    /\B(?=(\d{3})+(?!\d))/g, ",");
            }
        }
    },
    legend: {
        position: 'bottom',
        horizontalAlign: 'center'
    },
    grid: {
        borderColor: '#f1f1f1',
        strokeDashArray: 4,
    }
};

var chart = new ApexCharts(document.querySelector("#monthlyAccountMetricsChart"),
    options);
chart.render();
   </script>