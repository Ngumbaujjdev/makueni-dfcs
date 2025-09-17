<div id="produceDistributionChart"></div>
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.30.0/dist/apexcharts.min.js"></script>
<script>
<?php
$app = new App();

// Get monthly counts for different produce types
$months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$productTypes = $app->select_all("SELECT DISTINCT product_types.name FROM product_types");
$productCounts = [];

foreach ($productTypes as $productType) {
    $counts = [];
    foreach ($months as $index => $month) {
        $monthNum = $index + 1;
        
        $query = "SELECT COUNT(*) as count FROM produce_deliveries 
                  JOIN farm_products ON produce_deliveries.farm_product_id = farm_products.id
                  JOIN product_types ON farm_products.product_type_id = product_types.id
                  WHERE MONTH(produce_deliveries.delivery_date) = $monthNum
                  AND YEAR(produce_deliveries.delivery_date) = YEAR(CURRENT_DATE())
                  AND product_types.name = '$productType->name'";
        $result = $app->select_one($query);
        $counts[] = $result->count;
    }
    $productCounts[] = [
        'name' => $productType->name,
        'data' => $counts
    ];
}
?>

var options = {
    series: <?php echo json_encode($productCounts); ?>,
    chart: {
        height: 350,
        type: 'line',
        zoom: {
            enabled: true
        },
        toolbar: {
            show: false
        }
    },
    colors: ['#FF4500', '#FFA500', '#FFD700', '#90EE90', '#00BFFF', '#BA55D3'], // Custom colors
    dataLabels: {
        enabled: false
    },
    stroke: {
        width: 2,
        curve: 'smooth',
        dashArray: 0
    },
    legend: {
        position: 'bottom',
        horizontalAlign: 'center'
    },
    markers: {
        size: 0,
        hover: {
            sizeOffset: 6
        }
    },
    xaxis: {
        categories: <?php echo json_encode($months); ?>,
        title: {
            text: 'Month'
        }
    },
    yaxis: {
        title: {
            text: 'Produce Deliveries'
        }
    },
    tooltip: {
        y: {
            title: {
                formatter: function(val) {
                    return val + " Deliveries"
                }
            }
        }
    },
    grid: {
        borderColor: '#f1f1f1',
    }
};

var chart = new ApexCharts(document.querySelector("#produceDistributionChart"), options);
chart.render();
</script>