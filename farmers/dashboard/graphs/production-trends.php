<div id="productionTrendsChart"></div>
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.30.0/dist/apexcharts.min.js"></script>
<script>
<?php
$app = new App();

// Get farmer_id for the current user
$farmerQuery = "SELECT id FROM farmers WHERE user_id = {$_SESSION['user_id']}";
$farmer = $app->select_one($farmerQuery);
$farmerId = $farmer->id ?? 0;

// Get monthly data for different metrics
$months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

// Get monthly delivery counts
$deliveryCounts = [];
foreach ($months as $index => $month) {
    $monthNum = $index + 1;
    $query = "SELECT COUNT(*) as count 
              FROM produce_deliveries pd
              JOIN farm_products fp ON pd.farm_product_id = fp.id
              JOIN farms f ON fp.farm_id = f.id
              WHERE MONTH(pd.delivery_date) = $monthNum
              AND YEAR(pd.delivery_date) = YEAR(CURRENT_DATE())
              AND f.farmer_id = $farmerId";
    $result = $app->select_one($query);
    $deliveryCounts[] = (int)$result->count;
}

// Get monthly revenue (in thousands)
$monthlyRevenue = [];
foreach ($months as $index => $month) {
    $monthNum = $index + 1;
    $query = "SELECT COALESCE(SUM(pd.total_value), 0) as revenue 
              FROM produce_deliveries pd
              JOIN farm_products fp ON pd.farm_product_id = fp.id
              JOIN farms f ON fp.farm_id = f.id
              WHERE MONTH(pd.delivery_date) = $monthNum
              AND YEAR(pd.delivery_date) = YEAR(CURRENT_DATE())
              AND f.farmer_id = $farmerId
              AND pd.status = 'paid'";
    $result = $app->select_one($query);
    $monthlyRevenue[] = round(($result->revenue ?? 0) / 1000, 1); // Convert to thousands
}

// Get monthly quality percentage (Grade A deliveries)
$qualityPercentage = [];
foreach ($months as $index => $month) {
    $monthNum = $index + 1;
    $query = "SELECT 
                COUNT(*) as total_deliveries,
                SUM(CASE WHEN pd.quality_grade = 'A' THEN 1 ELSE 0 END) as grade_a_count
              FROM produce_deliveries pd
              JOIN farm_products fp ON pd.farm_product_id = fp.id
              JOIN farms f ON fp.farm_id = f.id
              WHERE MONTH(pd.delivery_date) = $monthNum
              AND YEAR(pd.delivery_date) = YEAR(CURRENT_DATE())
              AND f.farmer_id = $farmerId
              AND pd.quality_grade IS NOT NULL";
    $result = $app->select_one($query);
    
    if ($result->total_deliveries > 0) {
        $qualityPercentage[] = round(($result->grade_a_count / $result->total_deliveries) * 100, 1);
    } else {
        $qualityPercentage[] = 0;
    }
}

// Prepare series data
$seriesData = [
    [
        'name' => 'Deliveries Count',
        'type' => 'column',
        'data' => $deliveryCounts
    ],
    [
        'name' => 'Revenue (K)',
        'type' => 'line',
        'data' => $monthlyRevenue
    ],
    [
        'name' => 'Quality %',
        'type' => 'line',
        'data' => $qualityPercentage
    ]
];
?>

var options = {
    series: <?php echo json_encode($seriesData); ?>,
    chart: {
        height: 350,
        type: 'line',
        zoom: {
            enabled: true
        },
        toolbar: {
            show: true,
            tools: {
                download: true,
                selection: true,
                zoom: true,
                zoomin: true,
                zoomout: true,
                pan: true,
                reset: true
            }
        }
    },
    colors: ['#70A136', '#4A220F', '#FF6B6B'], // Green for deliveries, brown for revenue, red for quality
    dataLabels: {
        enabled: false
    },
    stroke: {
        width: [0, 4, 4], // Column has no stroke, lines have 4px stroke
        curve: 'smooth',
        dashArray: [0, 0, 5] // Solid lines for deliveries and revenue, dashed for quality
    },
    plotOptions: {
        bar: {
            columnWidth: '50%',
            borderRadius: 4
        }
    },
    legend: {
        position: 'bottom',
        horizontalAlign: 'center',
        offsetY: 10
    },
    markers: {
        size: [0, 6, 6], // No markers for columns, 6px for lines
        hover: {
            sizeOffset: 8
        }
    },
    xaxis: {
        categories: <?php echo json_encode($months); ?>,
        title: {
            text: 'Month (<?php echo date("Y"); ?>)',
            style: {
                fontSize: '12px',
                fontWeight: 600
            }
        },
        axisBorder: {
            show: true,
            color: '#e0e0e0'
        }
    },
    yaxis: [{
            title: {
                text: 'Number of Deliveries',
                style: {
                    color: '#70A136',
                    fontSize: '12px',
                    fontWeight: 600
                }
            },
            labels: {
                style: {
                    colors: '#70A136'
                }
            },
            min: 0
        },
        {
            opposite: true,
            title: {
                text: 'Revenue (KES Thousands) / Quality %',
                style: {
                    color: '#4A220F',
                    fontSize: '12px',
                    fontWeight: 600
                }
            },
            labels: {
                style: {
                    colors: '#4A220F'
                }
            },
            min: 0
        }
    ],
    tooltip: {
        shared: true,
        intersect: false,
        y: [{
                title: {
                    formatter: function(val) {
                        return val + " Deliveries"
                    }
                }
            },
            {
                title: {
                    formatter: function(val) {
                        return "KES " + val + "K Revenue"
                    }
                }
            },
            {
                title: {
                    formatter: function(val) {
                        return val + "% Grade A Quality"
                    }
                }
            }
        ]
    },
    grid: {
        borderColor: '#f1f1f1',
        strokeDashArray: 3,
        xaxis: {
            lines: {
                show: true
            }
        },
        yaxis: {
            lines: {
                show: true
            }
        }
    },
    fill: {
        type: ['solid', 'solid', 'solid'],
        opacity: [0.8, 1, 1]
    },
    responsive: [{
        breakpoint: 480,
        options: {
            chart: {
                height: 300
            },
            legend: {
                position: 'bottom'
            }
        }
    }]
};

var chart = new ApexCharts(document.querySelector("#productionTrendsChart"), options);
chart.render();
</script>