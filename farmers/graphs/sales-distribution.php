<div id="salesValueChart"></div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.30.0/dist/apexcharts.min.js"></script>
<script>
<?php
$app = new App();
$userId = $_SESSION['user_id'];

// Get the farmer's ID from the user ID
$farmerQuery = "SELECT id FROM farmers WHERE user_id = $userId";
$farmerResult = $app->select_one($farmerQuery);
$farmerId = $farmerResult->id;

// Get monthly sales data
$months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$quantityValues = [];
$salesValues = [];
$avgPriceValues = [];

foreach ($months as $index => $month) {
    $monthNum = $index + 1;
    
    // Get total quantity for month
    $query = "SELECT COALESCE(SUM(pd.quantity), 0) as total_quantity 
              FROM produce_deliveries pd
              JOIN farm_products fp ON pd.farm_product_id = fp.id
              JOIN farms f ON fp.farm_id = f.id
              WHERE f.farmer_id = $farmerId
              AND MONTH(pd.delivery_date) = $monthNum 
              AND YEAR(pd.delivery_date) = YEAR(CURRENT_DATE())";
    $result = $app->select_one($query);
    $quantityValues[] = floatval($result->total_quantity);
    
    // Get total sales value for month
    $query = "SELECT COALESCE(SUM(pd.total_value), 0) as total_value 
              FROM produce_deliveries pd
              JOIN farm_products fp ON pd.farm_product_id = fp.id
              JOIN farms f ON fp.farm_id = f.id
              WHERE f.farmer_id = $farmerId
              AND pd.status = 'sold'
              AND MONTH(pd.delivery_date) = $monthNum 
              AND YEAR(pd.delivery_date) = YEAR(CURRENT_DATE())";
    $result = $app->select_one($query);
    $salesValues[] = floatval($result->total_value);
    
    // Calculate average price per kg
    if ($quantityValues[$index] > 0) {
        $avgPriceValues[] = round($salesValues[$index] / $quantityValues[$index], 2);
    } else {
        $avgPriceValues[] = 0;
    }
}
?>

var options = {
    series: [{
        name: 'Quantity (KGs)',
        type: 'column',
        data: [<?php echo implode(',', $quantityValues); ?>]
    }, {
        name: 'Sales Value (KES)',
        type: 'column',
        data: [<?php echo implode(',', $salesValues); ?>]
    }, {
        name: 'Avg. Price per KG (KES)',
        type: 'line',
        data: [<?php echo implode(',', $avgPriceValues); ?>]
    }],
    chart: {
        height: 400,
        type: 'line',
        stacked: false,
        toolbar: {
            show: false
        }
    },
    colors: ['#6AA32D', '#BA8448', '#4B3D8F'],
    stroke: {
        width: [0, 0, 4],
        curve: 'smooth'
    },
    plotOptions: {
        bar: {
            borderRadius: 5,
            columnWidth: '50%'
        }
    },
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
        size: [0, 0, 5],
        colors: ['#4B3D8F'],
        strokeColors: '#fff',
        strokeWidth: 2,
        hover: {
            size: 7,
        }
    },
    labels: <?php echo json_encode($months); ?>,
    xaxis: {
        type: 'category',
        title: {
            text: 'Month'
        }
    },
    yaxis: [{
            title: {
                text: 'Quantity (KGs)',
            },
            labels: {
                formatter: function(val) {
                    return val.toFixed(0);
                }
            }
        },
        {
            opposite: true,
            title: {
                text: 'Values (KES)'
            },
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
                    return y.toFixed(2) + " KGs";
                } else if (seriesIndex === 1) {
                    return "KES " + y.toFixed(2);
                } else {
                    return "KES " + y.toFixed(2) + " per KG";
                }
            }
        }
    },
    title: {
        text: 'Monthly Produce Quantity, Sales Value, and Average Price',
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
        offsetY: 7
    },
    grid: {
        borderColor: '#f1f1f1',
        strokeDashArray: 4
    }
};

var chart = new ApexCharts(document.querySelector("#salesValueChart"), options);
chart.render();
</script>