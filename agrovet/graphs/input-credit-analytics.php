<!-- Input Credit Analytics Chart -->
<div id="inputCreditAnalyticsChart" style="height: 350px;"></div>

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

// Get months for the x-axis
$months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$currentMonth = date('n'); // 1-12

// Collect data for credit applications, approvals, and disbursements by month
$applicationData = [];
$approvalData = [];
$disbursementData = [];
$repaymentData = [];

for ($i = 1; $i <= 12; $i++) {
    // Applications data
    $appQuery = "SELECT COUNT(*) as count FROM input_credit_applications 
                WHERE agrovet_id = $agrovetId
                AND MONTH(application_date) = $i 
                AND YEAR(application_date) = YEAR(CURRENT_DATE())";
    $appResult = $app->select_one($appQuery);
    $applicationData[] = $appResult->count;
    
    // Approvals data
    $approvalQuery = "SELECT COUNT(*) as count FROM input_credit_applications 
                     WHERE agrovet_id = $agrovetId
                     AND MONTH(application_date) = $i 
                     AND YEAR(application_date) = YEAR(CURRENT_DATE())
                     AND status IN ('approved', 'fulfilled', 'completed')";
    $approvalResult = $app->select_one($approvalQuery);
    $approvalData[] = $approvalResult->count;
    
    // Disbursements data (amount)
    $disbursementQuery = "SELECT COALESCE(SUM(aic.approved_amount), 0) as total 
                         FROM approved_input_credits aic
                         JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                         WHERE ica.agrovet_id = $agrovetId
                         AND MONTH(aic.fulfillment_date) = $i 
                         AND YEAR(aic.fulfillment_date) = YEAR(CURRENT_DATE())";
    $disbursementResult = $app->select_one($disbursementQuery);
    $disbursementData[] = $disbursementResult->total / 1000; // Convert to thousands
    
    // Repayment data
    $repaymentQuery = "SELECT COALESCE(SUM(icr.amount), 0) as total 
                      FROM input_credit_repayments icr
                      JOIN approved_input_credits aic ON icr.approved_credit_id = aic.id
                      JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                      WHERE ica.agrovet_id = $agrovetId
                      AND MONTH(icr.deduction_date) = $i 
                      AND YEAR(icr.deduction_date) = YEAR(CURRENT_DATE())";
    $repaymentResult = $app->select_one($repaymentQuery);
    $repaymentData[] = $repaymentResult->total / 1000; // Convert to thousands
}

// Get input type distribution data
$inputTypeQuery = "SELECT 
                    ici.input_type, 
                    COUNT(ica.id) as count 
                  FROM input_credit_applications ica 
                  JOIN input_credit_items ici ON ici.credit_application_id = ica.id
                  WHERE ica.agrovet_id = $agrovetId
                  AND YEAR(ica.application_date) = YEAR(CURRENT_DATE()) 
                  GROUP BY ici.input_type 
                  ORDER BY count DESC";
$inputTypes = $app->select_all($inputTypeQuery);

$inputTypeLabels = [];
$inputTypeCounts = [];

foreach ($inputTypes as $type) {
    $inputTypeLabels[] = ucfirst($type->input_type);
    $inputTypeCounts[] = $type->count;
}
?>

// Initialize the chart
var options = {
    series: [{
            name: 'Applications',
            type: 'column',
            data: <?php echo json_encode($applicationData); ?>
        },
        {
            name: 'Approvals',
            type: 'column',
            data: <?php echo json_encode($approvalData); ?>
        },
        {
            name: 'Disbursements (000s)',
            type: 'line',
            data: <?php echo json_encode($disbursementData); ?>
        },
        {
            name: 'Repayments (000s)',
            type: 'line',
            data: <?php echo json_encode($repaymentData); ?>
        }
    ],
    chart: {
        height: 350,
        type: 'line',
        stacked: false,
        toolbar: {
            show: false
        },
        animations: {
            enabled: true,
            easing: 'easeinout',
            speed: 800,
            animateGradually: {
                enabled: true,
                delay: 150
            },
            dynamicAnimation: {
                enabled: true,
                speed: 350
            }
        },
        fontFamily: 'Roboto, Arial, sans-serif'
    },
    plotOptions: {
        bar: {
            borderRadius: 5,
            columnWidth: '60%',
        },
    },
    colors: ['#6AA32D', '#10b981', '#f59e0b', '#ef4444'],
    stroke: {
        width: [0, 0, 3, 3],
        curve: 'smooth'
    },
    title: {
        text: 'Input Credit Activity Overview - <?php echo date("Y"); ?>',
        align: 'left',
        style: {
            fontSize: '16px',
            fontWeight: 'bold',
            fontFamily: 'Roboto, Arial, sans-serif',
            color: '#263238'
        }
    },
    fill: {
        opacity: [0.85, 0.85, 1, 1],
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
            text: 'Month',
            style: {
                fontFamily: 'Roboto, Arial, sans-serif',
            }
        }
    },
    yaxis: [{
            title: {
                text: 'Applications & Approvals Count',
                style: {
                    fontFamily: 'Roboto, Arial, sans-serif',
                }
            },
            min: 0
        },
        {
            opposite: true,
            title: {
                text: 'Amount (KES 000s)',
                style: {
                    fontFamily: 'Roboto, Arial, sans-serif',
                }
            },
            min: 0
        }
    ],
    tooltip: {
        shared: true,
        intersect: false,
        y: {
            formatter: function(y, {
                seriesIndex,
                dataPointIndex,
                w
            }) {
                if (seriesIndex === 0 || seriesIndex === 1) {
                    // For Applications and Approvals
                    return y.toFixed(0) + " credits";
                } else {
                    // For Disbursements and Repayments
                    return "KES " + y.toFixed(2) + "k";
                }
            }
        }
    },
    legend: {
        position: 'top',
        horizontalAlign: 'right',
        offsetY: -20,
        fontSize: '13px',
        fontFamily: 'Roboto, Arial, sans-serif',
        markers: {
            width: 10,
            height: 10,
            radius: 2
        },
        itemMargin: {
            horizontal: 10,
            vertical: 8
        }
    },
    grid: {
        borderColor: '#f1f1f1'
    },
    annotations: {
        points: [{
            x: '<?php echo $months[$currentMonth-1]; ?>',
            y: <?php echo $applicationData[$currentMonth-1]; ?>,
            marker: {
                size: 6,
                fillColor: '#6AA32D',
                strokeColor: '#FFF',
                strokeWidth: 2,
                radius: 2
            },
            label: {
                borderColor: '#6AA32D',
                offsetY: 0,
                style: {
                    color: '#fff',
                    background: '#6AA32D',
                    padding: {
                        left: 5,
                        right: 5,
                        top: 2,
                        bottom: 2
                    }
                },
                text: 'Current'
            }
        }]
    }
};

var chart = new ApexCharts(document.querySelector("#inputCreditAnalyticsChart"), options);
chart.render();

// Add event listeners for the period buttons
document.querySelectorAll('.card-header .btn-group button').forEach(function(button, index) {
    button.addEventListener('click', function() {
        // Update button states
        document.querySelectorAll('.card-header .btn-group button').forEach(function(btn) {
            btn.classList.remove('btn-primary', 'active');
            btn.classList.add('btn-outline-primary');
        });
        this.classList.remove('btn-outline-primary');
        this.classList.add('btn-primary', 'active');

        // This is where you would update the chart data based on the selected period
        const periods = ['monthly', 'quarterly', 'yearly'];
        const selected = periods[index];

        // You would fetch new data and update the chart here
        if (selected === 'quarterly') {
            chart.updateOptions({
                xaxis: {
                    categories: ['Q1', 'Q2', 'Q3', 'Q4']
                },
                title: {
                    text: 'Quarterly Input Credit Activity Overview - <?php echo date("Y"); ?>'
                }
            });
        } else if (selected === 'yearly') {
            chart.updateOptions({
                xaxis: {
                    categories: ['2020', '2021', '2022', '2023', '2024', '2025']
                },
                title: {
                    text: 'Yearly Input Credit Activity Overview'
                }
            });
        } else {
            chart.updateOptions({
                xaxis: {
                    categories: <?php echo json_encode($months); ?>
                },
                title: {
                    text: 'Monthly Input Credit Activity Overview - <?php echo date("Y"); ?>'
                }
            });
        }
    });
});

// Create a secondary chart for input type distribution if element exists
if (document.getElementById('inputTypeDistributionChart')) {
    var inputTypeChartOptions = {
        series: <?php echo json_encode($inputTypeCounts); ?>,
        chart: {
            type: 'donut',
            height: 200,
            offsetY: 10,
            toolbar: {
                show: false
            }
        },
        labels: <?php echo json_encode($inputTypeLabels); ?>,
        legend: {
            position: 'bottom',
            fontFamily: 'Roboto, Arial, sans-serif',
            fontSize: '12px',
            offsetY: 0,
            markers: {
                width: 8,
                height: 8,
                radius: 12
            },
            itemMargin: {
                horizontal: 8,
                vertical: 0
            }
        },
        colors: ['#6AA32D', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
        dataLabels: {
            enabled: false
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total',
                            formatter: function(w) {
                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                            }
                        }
                    }
                }
            }
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }],
        tooltip: {
            y: {
                formatter: function(value) {
                    return value + " applications";
                }
            }
        }
    };

    var inputTypeChart = new ApexCharts(document.querySelector("#inputTypeDistributionChart"), inputTypeChartOptions);
    inputTypeChart.render();
}
</script>