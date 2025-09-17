<div id="productDistributionChart"></div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.30.0/dist/apexcharts.min.js"></script>
<script>
<?php
$app = new App();
$userId = $_SESSION['user_id'];

// Get the farmer's ID from the user ID
$farmerQuery = "SELECT id FROM farmers WHERE user_id = $userId";
$farmerResult = $app->select_one($farmerQuery);
$farmerId = $farmerResult->id;

// Get product distribution data
$query = "SELECT 
            pt.name as product_name,
            SUM(pd.quantity) as total_quantity
          FROM produce_deliveries pd
          JOIN farm_products fp ON pd.farm_product_id = fp.id
          JOIN product_types pt ON fp.product_type_id = pt.id
          JOIN farms f ON fp.farm_id = f.id
          WHERE f.farmer_id = $farmerId
          GROUP BY pt.name
          ORDER BY total_quantity DESC";

$products = $app->select_all($query);

$productNames = [];
$productQuantities = [];

if ($products) {
    foreach ($products as $product) {
        $productNames[] = $product->product_name;
        $productQuantities[] = floatval($product->total_quantity);
    }
}
?>

var options = {
    series: [<?php echo implode(',', $productQuantities); ?>],
    chart: {
        width: 380,
        type: 'pie',
        height: 400,
    },
    labels: <?php echo json_encode($productNames); ?>,
    colors: ['#6AA32D', '#BA8448', '#4B3D8F', '#2E93fA', '#34C759', '#F5B041', '#E74C3C', '#8E44AD'],
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
    }],
    plotOptions: {
        pie: {
            expandOnClick: true,
            donut: {
                size: '0%',
            }
        }
    },
    legend: {
        position: 'bottom',
        horizontalAlign: 'center'
    },
    title: {
        text: 'Product Distribution by Quantity (KGs)',
        align: 'center',
        style: {
            fontSize: '16px',
            fontWeight: 'bold',
            color: '#6AA32D'
        }
    },
    dataLabels: {
        enabled: true,
        formatter: function(val, opts) {
            return Math.round(val) + '%';
        },
        style: {
            fontSize: '14px',
            fontFamily: 'Helvetica, Arial, sans-serif',
            fontWeight: 'bold',
        },
        dropShadow: {
            enabled: false
        }
    },
    tooltip: {
        y: {
            formatter: function(val) {
                return val.toFixed(2) + ' KGs';
            }
        }
    },
    theme: {
        monochrome: {
            enabled: false
        }
    },
    states: {
        hover: {
            filter: {
                type: 'none',
            }
        },
        active: {
            filter: {
                type: 'none',
            }
        }
    }
};

var chart = new ApexCharts(document.querySelector("#productDistributionChart"), options);
chart.render();
</script>