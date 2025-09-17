<div id="monthlyInputCreditMetricsChart"></div>

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

// Get monthly counts for different input credit statuses
$months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$applicationCounts = [];
$approvedCounts = [];
$rejectedCounts = [];
$fulfilledCounts = [];

foreach ($months as $index => $month) {
    $monthNum = $index + 1;
    
    // Application counts (all applications made in this month)
    $query = "SELECT COUNT(*) as count 
              FROM input_credit_applications 
              WHERE MONTH(application_date) = $monthNum 
              AND YEAR(application_date) = YEAR(CURRENT_DATE())
              AND agrovet_id = $agrovetId";
    $result = $app->select_one($query);
    $applicationCounts[] = $result->count ?? 0;
    
    // Approved counts (approved in this month)
    $query = "SELECT COUNT(*) as count 
              FROM input_credit_applications 
              WHERE (status = 'approved' OR status = 'fulfilled' OR status = 'completed')
              AND MONTH(review_date) = $monthNum 
              AND YEAR(review_date) = YEAR(CURRENT_DATE())
              AND agrovet_id = $agrovetId";
    $result = $app->select_one($query);
    $approvedCounts[] = $result->count ?? 0;
    
    // Rejected counts (rejected in this month)
    $query = "SELECT COUNT(*) as count 
              FROM input_credit_applications 
              WHERE status = 'rejected' 
              AND MONTH(review_date) = $monthNum 
              AND YEAR(review_date) = YEAR(CURRENT_DATE())
              AND agrovet_id = $agrovetId";
    $result = $app->select_one($query);
    $rejectedCounts[] = $result->count ?? 0;
    
    // Fulfilled counts (inputs provided in this month)
    $query = "SELECT COUNT(*) as count 
              FROM approved_input_credits aic
              JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
              WHERE ica.agrovet_id = $agrovetId
              AND MONTH(aic.fulfillment_date) = $monthNum 
              AND YEAR(aic.fulfillment_date) = YEAR(CURRENT_DATE())";
    $result = $app->select_one($query);
    $fulfilledCounts[] = $result->count ?? 0;
}

// Get total metrics for the year
$totalApplications = array_sum($applicationCounts);
$totalApproved = array_sum($approvedCounts);
$totalRejected = array_sum($rejectedCounts);
$totalFulfilled = array_sum($fulfilledCounts);

// Calculate approval rate
$approvalRate = ($totalApplications > 0) ? round(($totalApproved / $totalApplications) * 100, 1) : 0;

// Get monthly amounts for input credits
$monthlyAmounts = [];
$monthlyRepayments = [];

foreach ($months as $index => $month) {
    $monthNum = $index + 1;
    
    // Credit amounts approved in this month
    $query = "SELECT COALESCE(SUM(aic.approved_amount), 0) as total 
              FROM approved_input_credits aic
              JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
              WHERE ica.agrovet_id = $agrovetId
              AND MONTH(aic.approval_date) = $monthNum 
              AND YEAR(aic.approval_date) = YEAR(CURRENT_DATE())";
    $result = $app->select_one($query);
    $monthlyAmounts[] = $result->total ?? 0;
    
    // Repayment amounts received in this month
    $query = "SELECT COALESCE(SUM(icr.amount), 0) as total 
              FROM input_credit_repayments icr
              JOIN approved_input_credits aic ON icr.approved_credit_id = aic.id
              JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
              WHERE ica.agrovet_id = $agrovetId
              AND MONTH(icr.deduction_date) = $monthNum 
              AND YEAR(icr.deduction_date) = YEAR(CURRENT_DATE())";
    $result = $app->select_one($query);
    $monthlyRepayments[] = $result->total ?? 0;
}

// Get total amounts for the year
$totalAmount = array_sum($monthlyAmounts);
$totalRepaid = array_sum($monthlyRepayments);
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
                            <i class="ri-file-list-3-line"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Total Applications</h6>
                        <h3 class="mb-0"><?php echo $totalApplications; ?></h3>
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
                            <i class="ri-check-line"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Approved</h6>
                        <h3 class="mb-0"><?php echo $totalApproved; ?> <small class="text-muted">(<?php echo $approvalRate; ?>%)</small></h3>
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
                            <i class="ri-close-line"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Rejected</h6>
                        <h3 class="mb-0"><?php echo $totalRejected; ?></h3>
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
                            <i class="ri-shopping-bag-line"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Fulfilled</h6>
                        <h3 class="mb-0"><?php echo $totalFulfilled; ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add KES metrics row -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card bg-light mb-3">
            <div class="card-body py-2">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <span class="avatar avatar-sm" style="background-color: #6AA32D;">
                            <i class="ri-funds-line"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Total Credit Amount</h6>
                        <h3 class="mb-0">KES <?php echo number_format($totalAmount, 2); ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card bg-light mb-3">
            <div class="card-body py-2">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <span class="avatar avatar-sm" style="background-color: #3498DB;">
                            <i class="ri-arrow-go-back-line"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Total Repayments</h6>
                        <h3 class="mb-0">KES <?php echo number_format($totalRepaid, 2); ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
`);
// Create chart for application counts
var options = {
    series: [{
        name: 'Applications',
        data: [<?php echo implode(',', $applicationCounts); ?>]
    }, {
        name: 'Approved',
        data: [<?php echo implode(',', $approvedCounts); ?>]
    }, {
        name: 'Rejected',
        data: [<?php echo implode(',', $rejectedCounts); ?>]
    }, {
        name: 'Fulfilled',
        data: [<?php echo implode(',', $fulfilledCounts); ?>]
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
    colors: ['#6AA32D', '#3498DB', '#E74C3C', '#F5B041'],
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
            text: 'Number of Input Credits'
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
                return val + " Input Credits"
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

var chart = new ApexCharts(document.querySelector("#monthlyInputCreditMetricsChart"), options);
chart.render();

// Add a line chart for amounts below the bars
document.write(`
<div class="mt-4">
    <h5 class="text-center">Monthly Credit Amounts & Repayments</h5>
    <div id="monthlyCreditAmountsChart"></div>
</div>
`);

var amountsOptions = {
    series: [{
        name: 'Credit Amounts',
        type: 'column',
        data: [<?php echo implode(',', $monthlyAmounts); ?>]
    }, {
        name: 'Repayments',
        type: 'line',
        data: [<?php echo implode(',', $monthlyRepayments); ?>]
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
        width: [0, 4]
    },
    colors: ['#6AA32D', '#3498DB'],
    dataLabels: {
        enabled: true,
        enabledOnSeries: [1],
        formatter: function(val) {
            return 'KES ' + val.toLocaleString();
        },
        style: {
            fontSize: '10px'
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

var amountsChart = new ApexCharts(document.querySelector("#monthlyCreditAmountsChart"), amountsOptions);
amountsChart.render();
</script>