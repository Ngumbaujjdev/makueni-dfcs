<div id="productSalesPerformanceChart"></div>

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

    // Get product sales performance data
    $productQuery = "SELECT 
                        pt.name as product_name,
                        SUM(pd.quantity) as total_quantity,
                        SUM(pd.total_value) as total_value,
                        AVG(pd.total_value / pd.quantity) as avg_price
                     FROM produce_deliveries pd
                     JOIN farm_products fp ON pd.farm_product_id = fp.id
                     JOIN product_types pt ON fp.product_type_id = pt.id
                     JOIN farms f ON fp.farm_id = f.id
                     WHERE f.farmer_id = $farmerId
                     AND pd.status = 'sold'
                     GROUP BY pt.name
                     ORDER BY total_value DESC";
    
    $products = $app->select_all($productQuery);
    
    $productNames = [];
    $productQuantities = [];
    $productValues = [];
    $avgPrices = [];
    
    if ($products) {
        foreach ($products as $product) {
            $productNames[] = $product->product_name;
            $productQuantities[] = floatval($product->total_quantity);
            $productValues[] = floatval($product->total_value);
            $avgPrices[] = floatval($product->avg_price);
        }
    }
}
?>

var options = {
    series: [{
        name: 'Total Value (KES)',
        type: 'bar',
        data: <?php echo json_encode($productValues); ?>
    }, {
        name: 'Average Price per KG (KES)',
        type: 'line',
        data: <?php echo json_encode($avgPrices); ?>
    }],
    chart: {
        height: 400,
        type: 'line',
        stacked: false,
        toolbar: {
            show: false
        }
    },
    colors: ['#6AA32D', '#4B3D8F'],
    stroke: {
        width: [0, 4],
        curve: 'smooth'
    },
    plotOptions: {
        bar: {
            columnWidth: '60%',
            borderRadius: 5,
            dataLabels: {
                position: 'top'
            }
        }
    },
    dataLabels: {
        enabled: true,
        enabledOnSeries: [1],
        formatter: function(val) {
            return 'KES ' + val.toFixed(2);
        },
        style: {
            fontSize: '10px',
            colors: ['#4B3D8F']
        },
        background: {
            enabled: true,
            foreColor: '#fff',
            borderRadius: 2,
            padding: 4,
            opacity: 0.9,
            borderWidth: 1,
            borderColor: '#4B3D8F'
        },
        offsetY: -10,
    },
    markers: {
        size: [0, 6],
        colors: ['#4B3D8F'],
        strokeColors: '#fff',
        strokeWidth: 2,
        hover: {
            size: 8,
        }
    },
    xaxis: {
        categories: <?php echo json_encode($productNames); ?>,
        labels: {
            rotate: -45,
            style: {
                fontSize: '12px'
            }
        }
    },
    yaxis: [{
            title: {
                text: 'Total Value (KES)',
            },
            labels: {
                formatter: function(val) {
                    return 'KES ' + val.toFixed(0);
                }
            }
        },
        {
            opposite: true,
            title: {
                text: 'Avg Price per KG (KES)',
            },
            labels: {
                formatter: function(val) {
                    return 'KES ' + val.toFixed(2);
                }
            }
        }
    ],
    legend: {
        position: 'bottom',
        horizontalAlign: 'center'
    },
    fill: {
        opacity: [0.85, 1],
        gradient: {
            inverseColors: false,
            shade: 'light',
            type: "vertical",
            opacityFrom: 0.85,
            opacityTo: 0.55,
        }
    },
    tooltip: {
        shared: true,
        intersect: false,
        y: [{
                formatter: function(y) {
                    return "KES " + y.toFixed(2);
                }
            },
            {
                formatter: function(y) {
                    return "KES " + y.toFixed(2) + " per KG";
                }
            }
        ]
    },
    title: {
        text: 'Product Sales Performance',
        align: 'center',
        style: {
            fontSize: '16px',
            fontWeight: 'bold',
            color: '#6AA32D'
        }
    },
    annotations: {
        points: []
    },
    grid: {
        borderColor: '#f1f1f1',
        strokeDashArray: 4
    }
};

<?php if (!empty($productNames)): ?>
// Add annotations for highest and lowest price points
var maxPriceIndex = <?php echo json_encode(array_search(max($avgPrices), $avgPrices)); ?>;
var minPriceIndex = <?php echo json_encode(array_search(min($avgPrices), $avgPrices)); ?>;

options.annotations.points = [{
        x: options.xaxis.categories[maxPriceIndex],
        y: <?php echo json_encode(max($avgPrices)); ?>,
        marker: {
            size: 8,
            fillColor: '#FF5252',
            strokeColor: '#fff',
            strokeWidth: 2,
            radius: 2
        },
        label: {
            borderColor: '#FF5252',
            offsetY: 0,
            style: {
                color: '#fff',
                background: '#FF5252',
            },
            text: 'Highest Price',
        },
        seriesIndex: 1
    },
    {
        x: options.xaxis.categories[minPriceIndex],
        y: <?php echo json_encode(min($avgPrices)); ?>,
        marker: {
            size: 8,
            fillColor: '#FFC107',
            strokeColor: '#fff',
            strokeWidth: 2,
            radius: 2
        },
        label: {
            borderColor: '#FFC107',
            offsetY: 0,
            style: {
                color: '#fff',
                background: '#FFC107',
            },
            text: 'Lowest Price',
        },
        seriesIndex: 1
    }
];
<?php endif; ?>

var chart = new ApexCharts(document.querySelector("#productSalesPerformanceChart"), options);
chart.render();
</script>