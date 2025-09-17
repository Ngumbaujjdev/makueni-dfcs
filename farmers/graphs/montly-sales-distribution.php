<div id="monthlySalesTrendChart"></div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.30.0/dist/apexcharts.min.js"></script>
<script>
<?php
$app = new App();
$userId = $_SESSION['user_id'];

// Get the farmer's ID from the user ID
$farmerQuery = "SELECT id FROM farmers WHERE user_id = $userId";
$farmerResult = $app->select_one($farmerQuery);

if ($farmerResult) {
    $farmerId = $farmerResult->id;

    // Get monthly sales trend data
    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    $salesValues = [];
    $quantitySold = [];
    $avgPrices = [];
    
    foreach ($months as $index => $month) {
        $monthNum = $index + 1;
        
        // Get sales value for month
        $query = "SELECT 
                    COALESCE(SUM(pd.total_value), 0) as total_value,
                    COALESCE(SUM(pd.quantity), 0) as total_quantity
                  FROM produce_deliveries pd
                  JOIN farm_products fp ON pd.farm_product_id = fp.id
                  JOIN farms f ON fp.farm_id = f.id
                  WHERE f.farmer_id = $farmerId
                  AND pd.status = 'sold'
                  AND MONTH(pd.delivery_date) = $monthNum 
                  AND YEAR(pd.delivery_date) = YEAR(CURRENT_DATE())";
        
        $result = $app->select_one($query);
        
        $salesValues[] = floatval($result->total_value);
        $quantitySold[] = floatval($result->total_quantity);
        
        // Calculate average price per kg
        if ($result->total_quantity > 0) {
            $avgPrices[] = round($result->total_value / $result->total_quantity, 2);
        } else {
            $avgPrices[] = 0;
        }
    }
}
?>

var options = {
    series: [{
        name: 'Sales Value (KES)',
        type: 'column',
        data: <?php echo json_encode($salesValues); ?>
    }, {
        name: 'Quantity Sold (KGs)',
        type: 'column',
        data: <?php echo json_encode($quantitySold); ?>
    }, {
        name: 'Average Price (KES/KG)',
        type: 'line',
        data: <?php echo json_encode($avgPrices); ?>
    }],
    chart: {
        height: 350,
        type: 'line',
        stacked: false,
        toolbar: {
            show: false
        }
    },
    stroke: {
        width: [0, 0, 4],
        curve: 'smooth'
    },
    plotOptions: {
        bar: {
            columnWidth: '50%',
            borderRadius: 5
        }
    },
    colors: ['#6AA32D', '#BA8448', '#4B3D8F'],
    fill: {
        opacity: [0.85, 0.85, 1],
        gradient: {
            inverseColors: false,
            shade: 'light',
            type: "vertical",
            opacityFrom: 0.85,
            opacityTo: 0.55,
        }
    },
    markers: {
        size: [0, 0, 4],
        colors: ['#4B3D8F'],
        strokeColors: '#fff',
        strokeWidth: 2,
        hover: {
            size: 6,
        }
    },
    xaxis: {
        categories: <?php echo json_encode($months); ?>,
        title: {
            text: 'Month'
        }
    },
    yaxis: [{
            title: {
                text: 'Sales Value (KES)',
            },
            labels: {
                formatter: function(val) {
                    return val.toFixed(0);
                }
            }
        },
        {
            title: {
                text: 'Quantity (KGs)',
            },
            opposite: true,
            labels: {
                formatter: function(val) {
                    return val.toFixed(0);
                }
            }
        }
    ],
    tooltip: {
        shared: true,
        intersect: false,
        y: {
            formatter: function(y, {
                series,
                seriesIndex,
                dataPointIndex
            }) {
                if (seriesIndex === 0) {
                    return "KES " + y.toFixed(2);
                } else if (seriesIndex === 1) {
                    return y.toFixed(2) + " KGs";
                } else {
                    return "KES " + y.toFixed(2) + " per KG";
                }
            }
        }
    },
    title: {
        text: 'Monthly Sales Trend',
        align: 'center',
        style: {
            fontSize: '16px',
            fontWeight: 'bold',
            color: '#6AA32D'
        }
    },
    legend: {
        position: 'bottom',
        horizontalAlign: 'center',
        offsetY: 5
    },
    grid: {
        borderColor: '#f1f1f1',
        strokeDashArray: 4
    }
};

var chart = new ApexCharts(document.querySelector("#monthlySalesTrendChart"), options);
chart.render();
</script>