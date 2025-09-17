<!-- Loan Analytics Chart -->
<div id="loanAnalyticsChart" style="height: 350px;"></div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.30.0/dist/apexcharts.min.js"></script>
<script>
<?php
$app = new App();

// Get months for the x-axis
$months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$currentMonth = date('n'); // 1-12

// Collect data for loan applications, approvals, and disbursements by month
$applicationData = [];
$approvalData = [];
$disbursementData = [];
$repaymentData = [];

for ($i = 1; $i <= 12; $i++) {
    // Applications data
    $appQuery = "SELECT COUNT(*) as count FROM loan_applications 
                WHERE MONTH(application_date) = $i 
                AND YEAR(application_date) = YEAR(CURRENT_DATE())";
    $appResult = $app->select_one($appQuery);
    $applicationData[] = $appResult->count;
    
    // Approvals data
    $approvalQuery = "SELECT COUNT(*) as count FROM loan_applications 
                     WHERE MONTH(application_date) = $i 
                     AND YEAR(application_date) = YEAR(CURRENT_DATE())
                     AND status = 'approved'";
    $approvalResult = $app->select_one($approvalQuery);
    $approvalData[] = $approvalResult->count;
    
    // Disbursements data (amount)
    $disbursementQuery = "SELECT COALESCE(SUM(approved_amount), 0) as total FROM approved_loans 
                         WHERE MONTH(disbursement_date) = $i 
                         AND YEAR(disbursement_date) = YEAR(CURRENT_DATE())";
    $disbursementResult = $app->select_one($disbursementQuery);
    $disbursementData[] = $disbursementResult->total / 1000; // Convert to thousands
    
    // Repayment data
    $repaymentQuery = "SELECT COALESCE(SUM(amount), 0) as total FROM loan_repayments 
                      WHERE MONTH(payment_date) = $i 
                      AND YEAR(payment_date) = YEAR(CURRENT_DATE())";
    $repaymentResult = $app->select_one($repaymentQuery);
    $repaymentData[] = $repaymentResult->total / 1000; // Convert to thousands
}

// Get loan type distribution data
$loanTypeQuery = "SELECT lt.name, COUNT(la.id) as count 
                FROM loan_applications la 
                JOIN loan_types lt ON la.loan_type_id = lt.id 
                WHERE YEAR(la.application_date) = YEAR(CURRENT_DATE()) 
                GROUP BY lt.id 
                ORDER BY count DESC";
$loanTypes = $app->select_all($loanTypeQuery);

$loanTypeLabels = [];
$loanTypeCounts = [];

foreach ($loanTypes as $type) {
    $loanTypeLabels[] = $type->name;
    $loanTypeCounts[] = $type->count;
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
    colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444'],
    stroke: {
        width: [0, 0, 3, 3],
        curve: 'smooth'
    },
    title: {
        text: 'Loan Activity Overview - <?php echo date("Y"); ?>',
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
                    return y.toFixed(0) + " loans";
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
                fillColor: '#3b82f6',
                strokeColor: '#FFF',
                strokeWidth: 2,
                radius: 2
            },
            label: {
                borderColor: '#3b82f6',
                offsetY: 0,
                style: {
                    color: '#fff',
                    background: '#3b82f6',
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

var chart = new ApexCharts(document.querySelector("#loanAnalyticsChart"), options);
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
        // For now, we'll just show a notification
        const periods = ['monthly', 'quarterly', 'yearly'];
        const selected = periods[index];

        // You would fetch new data and update the chart here
        // For demo purposes, we're just showing different configurations
        if (selected === 'quarterly') {
            chart.updateOptions({
                xaxis: {
                    categories: ['Q1', 'Q2', 'Q3', 'Q4']
                },
                title: {
                    text: 'Quarterly Loan Activity Overview - <?php echo date("Y"); ?>'
                }
            });
        } else if (selected === 'yearly') {
            chart.updateOptions({
                xaxis: {
                    categories: ['2020', '2021', '2022', '2023', '2024', '2025']
                },
                title: {
                    text: 'Yearly Loan Activity Overview'
                }
            });
        } else {
            chart.updateOptions({
                xaxis: {
                    categories: <?php echo json_encode($months); ?>
                },
                title: {
                    text: 'Monthly Loan Activity Overview - <?php echo date("Y"); ?>'
                }
            });
        }
    });
});

// Loan Type Distribution Chart (as secondary visualization)
var loanTypeChartOptions = {
    series: <?php echo json_encode($loanTypeCounts); ?>,
    chart: {
        type: 'donut',
        height: 200,
        offsetY: 10,
        toolbar: {
            show: false
        }
    },
    labels: <?php echo json_encode($loanTypeLabels); ?>,
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
    colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'],
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

var loanTypeChart = new ApexCharts(document.querySelector("#loanTypeDistributionChart"), loanTypeChartOptions);
loanTypeChart.render();
</script>