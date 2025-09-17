<!-- Chart Scripts -->
<div id="productValueChart"></div>
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

// Product Value Chart (Horizontal Bar Chart instead of Pie)
// Product Value Chart (Horizontal Bar Chart instead of Pie)
var productValueOptions = {
    series: [{
        name: 'Total Value',
        data: [<?php echo implode(',', $productValues); ?>]
    }, {
        name: 'Delivery Count',
        data: [<?php echo implode(',', $productCounts); ?>]
    }],
    chart: {
        type: 'bar',
        height: 350,
        stacked: false,
        toolbar: {
            show: true
        },
        background: '#f8f9fa'
    },
    plotOptions: {
        bar: {
            horizontal: true,
            dataLabels: {
                position: 'top',
            },
            barHeight: '80%',
            borderRadius: 6,
            columnWidth: '70%',
            distributed: false,
            rangeBarOverlap: true,
            rangeBarGroupRows: false
        }
    },
    colors: ['#FF5733', '#3498DB'],
    dataLabels: {
        enabled: true,
        offsetX: 20,
        style: {
            fontSize: '12px',
            fontWeight: 'bold',
            colors: ['#2c3e50']
        },
        formatter: function(val, opt) {
            return opt.w.globals.seriesNames[opt.seriesIndex] === 'Total Value' ?
                'KES ' + val.toFixed(0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") :
                val.toFixed(0);
        },
        background: {
            enabled: true,
            foreColor: '#fff',
            padding: 4,
            borderRadius: 2,
            borderWidth: 1,
            borderColor: '#fff',
            opacity: 0.9
        }
    },
    stroke: {
        width: 2,
        colors: ['#fff']
    },
    xaxis: {
        categories: <?php echo json_encode($productNames); ?>,
        labels: {
            style: {
                fontSize: '12px',
                fontFamily: 'Helvetica, Arial, sans-serif',
                colors: '#2c3e50'
            }
        },
        title: {
            text: 'Value & Count',
            style: {
                fontSize: '14px',
                fontWeight: 'bold',
                color: '#34495e'
            }
        }
    },
    yaxis: {
        title: {
            text: 'Product Type',
            style: {
                fontSize: '14px',
                fontWeight: 'bold',
                color: '#34495e'
            }
        },
        labels: {
            style: {
                colors: '#2c3e50',
                fontWeight: 'medium'
            }
        }
    },
    tooltip: {
        shared: true,
        intersect: false,
        theme: 'dark',
        x: {
            show: true
        },
        y: [{
            formatter: function(val) {
                return 'KES ' + val.toFixed(0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
        }, {
            formatter: function(val) {
                return val.toFixed(0) + " deliveries";
            }
        }]
    },
    legend: {
        position: 'bottom',
        horizontalAlign: 'center',
        fontSize: '14px',
        markers: {
            width: 12,
            height: 12,
            radius: 6
        },
        itemMargin: {
            horizontal: 10,
            vertical: 0
        }
    },
    grid: {
        borderColor: '#e0e0e0',
        strokeDashArray: 4,
        xaxis: {
            lines: {
                show: true
            }
        },
        padding: {
            top: 0,
            right: 0,
            bottom: 0,
            left: 10
        }
    },
    fill: {
        type: 'gradient',
        gradient: {
            shade: 'light',
            type: "horizontal",
            shadeIntensity: 0.25,
            gradientToColors: ['#FFC300', '#3867D6'],
            inverseColors: false,
            opacityFrom: 0.9,
            opacityTo: 1,
            stops: [0, 90, 100]
        }
    }
};

var productValueChart = new ApexCharts(document.querySelector("#productValueChart"), productValueOptions);
productValueChart.render();
</script>