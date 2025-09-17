<div id="deliveryTrendsChart"></div>

<!-- Chart Scripts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.30.0/dist/apexcharts.min.js"></script>
<script>
<?php
// Get monthly delivery data for the current year
$months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$deliveryCounts = [];
$deliveryValues = [];

foreach ($months as $index => $month) {
    $monthNum = $index + 1;
    
    // Delivery counts for month
    $query = "SELECT COUNT(*) as count 
              FROM produce_deliveries 
              WHERE MONTH(delivery_date) = $monthNum 
              AND YEAR(delivery_date) = YEAR(CURRENT_DATE())";
    $result = $app->select_one($query);
    $deliveryCounts[] = $result->count ?? 0;
    
    // Delivery values for month
    $query = "SELECT COALESCE(SUM(total_value), 0) as total 
              FROM produce_deliveries 
              WHERE MONTH(delivery_date) = $monthNum 
              AND YEAR(delivery_date) = YEAR(CURRENT_DATE())";
    $result = $app->select_one($query);
    $deliveryValues[] = round($result->total ?? 0, 2);
}

// Get product type distribution data
$query = "SELECT 
            pt.name as product_name,
            SUM(pd.total_value) as total_value,
            COUNT(pd.id) as delivery_count
          FROM produce_deliveries pd
          JOIN farm_products fp ON pd.farm_product_id = fp.id
          JOIN product_types pt ON fp.product_type_id = pt.id
          GROUP BY pt.id
          ORDER BY total_value DESC
          LIMIT 10";
$productData = $app->select_all($query);

$productNames = [];
$productValues = [];
$productCounts = [];

foreach ($productData as $product) {
    $productNames[] = $product->product_name;
    $productValues[] = round($product->total_value, 2);
    $productCounts[] = $product->delivery_count;
}
?>

// Delivery Trends Over Time Chart
// Delivery Trends Over Time Chart
var deliveryTrendsOptions = {
    series: [{
        name: 'Deliveries',
        type: 'column',
        data: [<?php echo implode(',', $deliveryCounts); ?>]
    }, {
        name: 'Value (KES)',
        type: 'line',
        data: [<?php echo implode(',', $deliveryValues); ?>]
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
        width: [0, 4],
        curve: 'smooth',
        colors: ['transparent', '#E74C3C']
    },
    plotOptions: {
        bar: {
            columnWidth: '50%',
            borderRadius: 5,
            distributed: true,
            colors: {
                ranges: [{
                    from: 0,
                    to: 100,
                    color: '#6AA32D'
                }],
                backgroundBarColors: ['#f0f8ff', '#e6f2ff', '#d9edff', '#cce5ff'],
                backgroundBarOpacity: 0.2
            }
        }
    },
    colors: ['#2ECC71', '#E74C3C'],
    dataLabels: {
        enabled: false
    },
    fill: {
        type: ['gradient', 'solid'],
        gradient: {
            shade: 'light',
            type: "vertical",
            shadeIntensity: 0.5,
            gradientToColors: ['#9B59B6'],
            inverseColors: false,
            opacityFrom: 0.9,
            opacityTo: 0.7,
            stops: [0, 90, 100]
        }
    },
    markers: {
        size: 5,
        colors: ['#FF9800'],
        strokeColors: '#fff',
        strokeWidth: 2,
        hover: {
            size: 8,
        }
    },
    labels: <?php echo json_encode($months); ?>,
    xaxis: {
        title: {
            text: 'Month',
            style: {
                fontSize: '14px',
                fontWeight: 'bold'
            }
        },
        labels: {
            style: {
                colors: '#333'
            }
        }
    },
    yaxis: [{
        title: {
            text: 'Delivery Count',
            style: {
                fontSize: '14px',
                fontWeight: 'bold'
            }
        },
        labels: {
            style: {
                colors: '#2ECC71'
            }
        }
    }, {
        opposite: true,
        title: {
            text: 'Value (KES)',
            style: {
                fontSize: '14px',
                fontWeight: 'bold',
                color: '#E74C3C'
            }
        },
        labels: {
            style: {
                colors: '#E74C3C'
            },
            formatter: function(val) {
                return 'KES ' + val.toFixed(0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
        }
    }],
    tooltip: {
        shared: true,
        intersect: false,
        theme: 'dark',
        y: [{
            formatter: function(val) {
                return val.toFixed(0) + " deliveries";
            }
        }, {
            formatter: function(val) {
                return 'KES ' + val.toFixed(0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
        }]
    },
    legend: {
        position: 'bottom',
        horizontalAlign: 'center',
        markers: {
            width: 12,
            height: 12,
            radius: 12
        }
    },
    grid: {
        borderColor: '#f1f1f1',
        strokeDashArray: 4,
    }
};

var deliveryTrendsChart = new ApexCharts(document.querySelector("#deliveryTrendsChart"), deliveryTrendsOptions);
deliveryTrendsChart.render();
</script>