<div id="monthlySaccoLoanMetricsChart"></div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.30.0/dist/apexcharts.min.js"></script>
<script>
<?php
$app = new App();

// Get monthly counts for different SACCO loan statuses
$months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$applicationCounts = [];
$approvedCounts = [];
$rejectedCounts = [];
$disbursedCounts = [];

foreach ($months as $index => $month) {
    $monthNum = $index + 1;
    
    // Application counts (all SACCO applications made in this month)
    $query = "SELECT COUNT(*) as count 
              FROM loan_applications 
              WHERE MONTH(application_date) = $monthNum 
              AND YEAR(application_date) = YEAR(CURRENT_DATE())
              AND provider_type = 'sacco'";
    $result = $app->select_one($query);
    $applicationCounts[] = $result->count ?? 0;
    
    // Approved counts (SACCO loans approved in this month)
    $query = "SELECT COUNT(*) as count 
              FROM loan_applications 
              WHERE status = 'approved' 
              AND MONTH(review_date) = $monthNum 
              AND YEAR(review_date) = YEAR(CURRENT_DATE())
              AND provider_type = 'sacco'";
    $result = $app->select_one($query);
    $approvedCounts[] = $result->count ?? 0;
    
    // Rejected counts (SACCO loans rejected in this month)
    $query = "SELECT COUNT(*) as count 
              FROM loan_applications 
              WHERE status = 'rejected' 
              AND MONTH(review_date) = $monthNum 
              AND YEAR(review_date) = YEAR(CURRENT_DATE())
              AND provider_type = 'sacco'";
    $result = $app->select_one($query);
    $rejectedCounts[] = $result->count ?? 0;
    
    // Disbursed counts (SACCO loans disbursed in this month)
    $query = "SELECT COUNT(*) as count 
              FROM approved_loans al
              JOIN loan_applications la ON al.loan_application_id = la.id
              WHERE (al.status = 'pending_disbursement' OR al.status = 'active')
              AND MONTH(al.disbursement_date) = $monthNum 
              AND YEAR(al.disbursement_date) = YEAR(CURRENT_DATE())
              AND la.provider_type = 'sacco'";
    $result = $app->select_one($query);
    $disbursedCounts[] = $result->count ?? 0;
}

// Get total SACCO metrics for the year
$totalSaccoApplications = array_sum($applicationCounts);
$totalSaccoApproved = array_sum($approvedCounts);
$totalSaccoRejected = array_sum($rejectedCounts);
$totalSaccoDisbursed = array_sum($disbursedCounts);

// Calculate SACCO approval rate
$saccoApprovalRate = ($totalSaccoApplications > 0) ? round(($totalSaccoApproved / $totalSaccoApplications) * 100, 1) : 0;
?>

// Add yearly SACCO metrics above the chart
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
                        <h6 class="mb-0">Total SACCO Applications</h6>
                        <h3 class="mb-0"><?php echo $totalSaccoApplications; ?></h3>
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
                        <h6 class="mb-0">SACCO Loans Approved</h6>
                        <h3 class="mb-0"><?php echo $totalSaccoApproved; ?> <small class="text-muted">(<?php echo $saccoApprovalRate; ?>%)</small></h3>
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
                        <h6 class="mb-0">SACCO Loans Rejected</h6>
                        <h3 class="mb-0"><?php echo $totalSaccoRejected; ?></h3>
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
                            <i class="ri-money-dollar-circle-line"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">SACCO Loans Disbursed</h6>
                        <h3 class="mb-0"><?php echo $totalSaccoDisbursed; ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
`);

var options = {
    series: [{
        name: 'SACCO Applications',
        data: [<?php echo implode(',', $applicationCounts); ?>]
    }, {
        name: 'SACCO Approved',
        data: [<?php echo implode(',', $approvedCounts); ?>]
    }, {
        name: 'SACCO Rejected',
        data: [<?php echo implode(',', $rejectedCounts); ?>]
    }, {
        name: 'SACCO Disbursed',
        data: [<?php echo implode(',', $disbursedCounts); ?>]
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
            text: 'Number of SACCO Loans'
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
                return val + " SACCO Loans"
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

var chart = new ApexCharts(document.querySelector("#monthlySaccoLoanMetricsChart"), options);
chart.render();
</script>