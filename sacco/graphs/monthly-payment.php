<div id="monthlyRepaymentChart"></div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.30.0/dist/apexcharts.min.js"></script>
<script>
<?php
$app = new App();

// Get monthly counts and amounts for repayments
$months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$repaymentCounts = [];
$repaymentAmounts = [];
$produceDeductionAmounts = [];
$otherMethodAmounts = [];

foreach ($months as $index => $month) {
    $monthNum = $index + 1;
    
    // Repayment counts (all repayments made in this month)
    $query = "SELECT COUNT(*) as count 
              FROM loan_repayments 
              WHERE MONTH(payment_date) = $monthNum 
              AND YEAR(payment_date) = YEAR(CURRENT_DATE())";
    $result = $app->select_one($query);
    $repaymentCounts[] = $result->count ?? 0;
    
    // Total repayment amounts for this month
    $query = "SELECT COALESCE(SUM(amount), 0) as amount 
              FROM loan_repayments 
              WHERE MONTH(payment_date) = $monthNum 
              AND YEAR(payment_date) = YEAR(CURRENT_DATE())";
    $result = $app->select_one($query);
    $repaymentAmounts[] = $result->amount ?? 0;
    
    // Produce deduction amounts for this month
    $query = "SELECT COALESCE(SUM(amount), 0) as amount 
              FROM loan_repayments 
              WHERE payment_method = 'produce_deduction'
              AND MONTH(payment_date) = $monthNum 
              AND YEAR(payment_date) = YEAR(CURRENT_DATE())";
    $result = $app->select_one($query);
    $produceDeductionAmounts[] = $result->amount ?? 0;
    
    // Other methods amounts for this month (everything except produce deduction)
    $query = "SELECT COALESCE(SUM(amount), 0) as amount 
              FROM loan_repayments 
              WHERE payment_method != 'produce_deduction'
              AND MONTH(payment_date) = $monthNum 
              AND YEAR(payment_date) = YEAR(CURRENT_DATE())";
    $result = $app->select_one($query);
    $otherMethodAmounts[] = $result->amount ?? 0;
}

// Get total metrics for the year
$totalRepayments = array_sum($repaymentCounts);
$totalAmount = array_sum($repaymentAmounts);
$totalProduceDeduction = array_sum($produceDeductionAmounts);
$totalOtherMethods = array_sum($otherMethodAmounts);

// Calculate percentage of produce deductions
$producePercentage = ($totalAmount > 0) ? round(($totalProduceDeduction / $totalAmount) * 100, 1) : 0;
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
                            <i class="ri-money-dollar-box-line"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Total Repayments</h6>
                        <h3 class="mb-0"><?php echo $totalRepayments; ?></h3>
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
                            <i class="ri-coins-line"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Total Amount</h6>
                        <h3 class="mb-0">KES <?php echo number_format($totalAmount, 0); ?></h3>
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
                            <i class="ri-plant-line"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Produce Deductions</h6>
                        <h3 class="mb-0">KES <?php echo number_format($totalProduceDeduction, 0); ?> <small class="text-muted">(<?php echo $producePercentage; ?>%)</small></h3>
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
                            <i class="ri-bank-card-line"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Other Methods</h6>
                        <h3 class="mb-0">KES <?php echo number_format($totalOtherMethods, 0); ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
`);

var options = {
    series: [{
        name: 'Repayment Count',
        type: 'column',
        data: [<?php echo implode(',', $repaymentCounts); ?>]
    }, {
        name: 'Total Amount (KES)',
        type: 'line',
        data: [<?php echo implode(',', $repaymentAmounts); ?>]
    }, {
        name: 'Produce Deductions (KES)',
        type: 'area',
        data: [<?php echo implode(',', $produceDeductionAmounts); ?>]
    }, {
        name: 'Other Methods (KES)',
        type: 'area',
        data: [<?php echo implode(',', $otherMethodAmounts); ?>]
    }],
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
        width: [0, 4, 2, 2],
        curve: 'smooth'
    },
    plotOptions: {
        bar: {
            columnWidth: '50%',
            borderRadius: 5
        }
    },
    colors: ['#6AA32D', '#3498DB', '#2ECC71', '#E74C3C'],
    fill: {
        opacity: [0.85, 1, 0.25, 0.25],
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
        size: 0
    },
    xaxis: {
        categories: <?php echo json_encode($months); ?>,
        title: {
            text: 'Month'
        }
    },
    yaxis: [
        {
            seriesName: 'Repayment Count',
            title: {
                text: 'Repayment Count'
            },
            axisTicks: {
                show: true,
            },
        },
        {
            seriesName: 'Total Amount (KES)',
            show: false
        },
        {
            opposite: true,
            seriesName: 'Total Amount (KES)',
            title: {
                text: 'Amount (KES)'
            },
            axisTicks: {
                show: true,
            },
        }
    ],
    tooltip: {
        shared: true,
        intersect: false,
        y: {
            formatter: function (y, { seriesIndex, dataPointIndex }) {
                if (seriesIndex === 0) {
                    return y + " repayments";
                } else {
                    return "KES " + y.toLocaleString();
                }
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

var chart = new ApexCharts(document.querySelector("#monthlyRepaymentChart"), options);
chart.render();
</script>