<div id="loanTypeDistributionChart"></div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.30.0/dist/apexcharts.min.js"></script>
<script>
<?php
$app = new App();

// Get loan counts by type for SACCO loans only
$query = "SELECT 
            lt.name as loan_type,
            COUNT(la.id) as loan_count,
            SUM(CASE WHEN la.status = 'approved' OR la.status = 'disbursed' OR la.status = 'completed' THEN 1 ELSE 0 END) as approved_count,
            SUM(CASE WHEN la.status = 'rejected' THEN 1 ELSE 0 END) as rejected_count,
            SUM(CASE WHEN la.status = 'pending' OR la.status = 'under_review' THEN 1 ELSE 0 END) as pending_count,
            COALESCE(SUM(CASE 
                WHEN la.status = 'approved' OR la.status = 'disbursed' OR la.status = 'completed' 
                THEN (SELECT approved_amount FROM approved_loans WHERE loan_application_id = la.id) 
                ELSE 0 
            END), 0) as total_approved_amount
          FROM loan_applications la
          JOIN loan_types lt ON la.loan_type_id = lt.id
          WHERE la.provider_type = 'sacco'
          GROUP BY lt.name
          ORDER BY loan_count DESC";

$loanTypeData = $app->select_all($query);

$loanTypes = [];
$loanCounts = [];
$approvedCounts = [];
$rejectedCounts = [];
$pendingCounts = [];
$totalAmounts = [];
$colors = ['#6AA32D', '#3498DB', '#F5B041', '#9B59B6', '#1ABC9C', '#34495E', '#E74C3C', '#2ECC71'];

// Process the data
foreach ($loanTypeData as $index => $data) {
    $loanTypes[] = $data->loan_type;
    $loanCounts[] = (int)$data->loan_count;
    $approvedCounts[] = (int)$data->approved_count;
    $rejectedCounts[] = (int)$data->rejected_count;
    $pendingCounts[] = (int)$data->pending_count;
    $totalAmounts[] = (float)$data->total_approved_amount;
}

// Calculate totals
$totalLoans = array_sum($loanCounts);
$totalApproved = array_sum($approvedCounts);
$totalRejected = array_sum($rejectedCounts);
$totalPending = array_sum($pendingCounts);
$totalAmount = array_sum($totalAmounts);

// Format the amounts
$formattedTotalAmount = 'KES ' . number_format($totalAmount, 2);

// Calculate percentages for the labels
$percentages = array_map(function($count) use ($totalLoans) {
    return $totalLoans > 0 ? round(($count / $totalLoans) * 100, 1) : 0;
}, $loanCounts);

// Combine loan types with percentages for the chart labels
$chartLabels = array_map(function($type, $percentage) {
    return "$type ($percentage%)";
}, $loanTypes, $percentages);
?>

// Add summary statistics above the chart
document.write(`
<div class="row mb-3">
    <div class="col-md-3 col-sm-6">
        <div class="card bg-light mb-3">
            <div class="card-body py-2">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <span class="avatar avatar-sm" style="background-color: #6AA32D;">
                            <i class="ri-pie-chart-2-line"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">SACCO Loan Types</h6>
                        <h3 class="mb-0"><?php echo count($loanTypes); ?></h3>
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
                            <i class="ri-file-list-3-line"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">SACCO Applications</h6>
                        <h3 class="mb-0"><?php echo $totalLoans; ?></h3>
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
                            <i class="ri-money-dollar-circle-line"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Total SACCO Amount</h6>
                        <h3 class="mb-0" style="font-size: 1.1rem;"><?php echo $formattedTotalAmount; ?></h3>
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
                        <span class="avatar avatar-sm" style="background-color: #F5B041;">
                            <i class="ri-check-line"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">SACCO Approval Rate</h6>
                        <h3 class="mb-0"><?php echo $totalLoans > 0 ? round(($totalApproved / $totalLoans) * 100, 1) : 0; ?>%</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
`);

// First chart: Pie chart showing distribution of loan types
var optionsPie = {
    series: <?php echo json_encode($loanCounts); ?>,
    chart: {
        type: 'pie',
        height: 350,
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
    title: {
        text: 'SACCO Loan Type Distribution',
        align: 'center',
        style: {
            fontSize: '16px',
            fontWeight: 'bold'
        }
    },
    labels: <?php echo json_encode($chartLabels); ?>,
    colors: <?php echo json_encode(array_slice($colors, 0, count($loanTypes))); ?>,
    legend: {
        position: 'bottom',
        horizontalAlign: 'center'
    },
    dataLabels: {
        enabled: true,
        formatter: function(val, opts) {
            return opts.w.globals.series[opts.seriesIndex];
        },
        dropShadow: {
            enabled: false
        }
    },
    tooltip: {
        y: {
            formatter: function(val) {
                return val + " SACCO applications";
            }
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
    }]
};

// Create and render the pie chart
var chartPie = new ApexCharts(document.querySelector("#loanTypeDistributionChart"), optionsPie);
chartPie.render();

// Add a second chart below showing the status breakdown per loan type
document.write(`<div id="loanTypeStatusChart" class="mt-4"></div>`);

var optionsBar = {
    series: [{
        name: 'Approved',
        data: <?php echo json_encode($approvedCounts); ?>
    }, {
        name: 'Rejected',
        data: <?php echo json_encode($rejectedCounts); ?>
    }, {
        name: 'Pending',
        data: <?php echo json_encode($pendingCounts); ?>
    }],
    chart: {
        type: 'bar',
        height: 350,
        stacked: true,
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
    title: {
        text: 'SACCO Loan Status by Type',
        align: 'center',
        style: {
            fontSize: '16px',
            fontWeight: 'bold'
        }
    },
    plotOptions: {
        bar: {
            horizontal: false,
            borderRadius: 5,
            columnWidth: '70%',
            endingShape: 'rounded'
        },
    },
    dataLabels: {
        enabled: false
    },
    colors: ['#3498DB', '#E74C3C', '#F5B041'],
    stroke: {
        width: 1,
        colors: ['#fff']
    },
    xaxis: {
        categories: <?php echo json_encode($loanTypes); ?>,
        labels: {
            rotate: -45,
            rotateAlways: true,
            style: {
                fontSize: '12px',
                fontWeight: 500
            }
        }
    },
    yaxis: {
        title: {
            text: 'Number of SACCO Loans'
        }
    },
    tooltip: {
        y: {
            formatter: function(val) {
                return val + " SACCO loans";
            }
        }
    },
    fill: {
        opacity: 1
    },
    legend: {
        position: 'bottom',
        horizontalAlign: 'center'
    },
    grid: {
        borderColor: '#f1f1f1',
        strokeDashArray: 4
    }
};

// Create and render the stacked bar chart
var chartBar = new ApexCharts(document.querySelector("#loanTypeStatusChart"), optionsBar);
chartBar.render();

// Add a third chart showing loan amounts by type
document.write(`<div id="loanTypeAmountChart" class="mt-4"></div>`);

var optionsAmount = {
    series: [{
        name: 'Approved Amount',
        data: <?php echo json_encode($totalAmounts); ?>
    }],
    chart: {
        type: 'bar',
        height: 350,
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
    title: {
        text: 'SACCO Loan Amounts by Type',
        align: 'center',
        style: {
            fontSize: '16px',
            fontWeight: 'bold'
        }
    },
    plotOptions: {
        bar: {
            horizontal: true,
            borderRadius: 6,
            dataLabels: {
                position: 'top',
            },
        }
    },
    colors: ['#6AA32D'],
    dataLabels: {
        enabled: true,
        formatter: function(val) {
            return 'KES ' + val.toLocaleString();
        },
        offsetX: 30,
        style: {
            fontSize: '12px',
            colors: ['#000']
        }
    },
    stroke: {
        width: 1,
        colors: ['#fff']
    },
    xaxis: {
        categories: <?php echo json_encode($loanTypes); ?>,
        labels: {
            formatter: function(val) {
                return 'KES ' + val.toLocaleString();
            }
        }
    },
    yaxis: {
        title: {
            text: 'SACCO Loan Type'
        }
    },
    tooltip: {
        y: {
            formatter: function(val) {
                return 'KES ' + val.toLocaleString();
            }
        }
    },
    grid: {
        borderColor: '#f1f1f1',
        strokeDashArray: 4
    }
};

// Create and render the horizontal bar chart
var chartAmount = new ApexCharts(document.querySelector("#loanTypeAmountChart"), optionsAmount);
chartAmount.render();
</script>