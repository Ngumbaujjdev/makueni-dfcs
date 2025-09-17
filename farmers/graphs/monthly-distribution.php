<div id="monthlyDeliveriesChart"></div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.30.0/dist/apexcharts.min.js"></script>
<script>
<?php
$app = new App();
$userId = $_SESSION['user_id'];

// Get the farmer's ID from the user ID
$farmerQuery = "SELECT id FROM farmers WHERE user_id = $userId";
$farmerResult = $app->select_one($farmerQuery);
$farmerId = $farmerResult->id;

// Get monthly counts for different delivery statuses
$months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$pendingCounts = [];
$verifiedCounts = [];
$soldCounts = [];
$rejectedCounts = [];

foreach ($months as $index => $month) {
    $monthNum = $index + 1;
    
    // Pending deliveries count
    $query = "SELECT COUNT(*) as count 
              FROM produce_deliveries pd
              JOIN farm_products fp ON pd.farm_product_id = fp.id
              JOIN farms f ON fp.farm_id = f.id
              WHERE f.farmer_id = $farmerId
              AND pd.status = 'pending' 
              AND MONTH(pd.delivery_date) = $monthNum 
              AND YEAR(pd.delivery_date) = YEAR(CURRENT_DATE())";
    $result = $app->select_one($query);
    $pendingCounts[] = $result->count ?? 0;
    
    // Verified deliveries count
    $query = "SELECT COUNT(*) as count 
              FROM produce_deliveries pd
              JOIN farm_products fp ON pd.farm_product_id = fp.id
              JOIN farms f ON fp.farm_id = f.id
              WHERE f.farmer_id = $farmerId
              AND pd.status = 'verified' 
              AND MONTH(pd.delivery_date) = $monthNum 
              AND YEAR(pd.delivery_date) = YEAR(CURRENT_DATE())";
    $result = $app->select_one($query);
    $verifiedCounts[] = $result->count ?? 0;
    
    // Sold deliveries count
    $query = "SELECT COUNT(*) as count 
              FROM produce_deliveries pd
              JOIN farm_products fp ON pd.farm_product_id = fp.id
              JOIN farms f ON fp.farm_id = f.id
              WHERE f.farmer_id = $farmerId
              AND pd.status = 'sold' 
              AND MONTH(pd.delivery_date) = $monthNum 
              AND YEAR(pd.delivery_date) = YEAR(CURRENT_DATE())";
    $result = $app->select_one($query);
    $soldCounts[] = $result->count ?? 0;
    
    // Rejected deliveries count
    $query = "SELECT COUNT(*) as count 
              FROM produce_deliveries pd
              JOIN farm_products fp ON pd.farm_product_id = fp.id
              JOIN farms f ON fp.farm_id = f.id
              WHERE f.farmer_id = $farmerId
              AND pd.status = 'rejected' 
              AND MONTH(pd.delivery_date) = $monthNum 
              AND YEAR(pd.delivery_date) = YEAR(CURRENT_DATE())";
    $result = $app->select_one($query);
    $rejectedCounts[] = $result->count ?? 0;
}
?>

var options = {
    series: [{
        name: 'Pending',
        data: [<?php echo implode(',', $pendingCounts); ?>]
    }, {
        name: 'Verified',
        data: [<?php echo implode(',', $verifiedCounts); ?>]
    }, {
        name: 'Sold',
        data: [<?php echo implode(',', $soldCounts); ?>]
    }, {
        name: 'Rejected',
        data: [<?php echo implode(',', $rejectedCounts); ?>]
    }],
    chart: {
        type: 'bar',
        height: 350,
        stacked: true,
        toolbar: {
            show: false
        },
        zoom: {
            enabled: false
        }
    },
    responsive: [{
        breakpoint: 480,
        options: {
            legend: {
                position: 'bottom',
                offsetX: -10,
                offsetY: 0
            }
        }
    }],
    plotOptions: {
        bar: {
            horizontal: false,
            borderRadius: 5,
            columnWidth: '55%',
            endingShape: 'rounded'
        },
    },
    colors: ['#F5B041', '#3498DB', '#6AA32D', '#E74C3C'],
    dataLabels: {
        enabled: false
    },
    stroke: {
        show: true,
        width: 2,
        colors: ['transparent']
    },
    xaxis: {
        categories: <?php echo json_encode($months); ?>,
        title: {
            text: 'Month'
        }
    },
    yaxis: {
        title: {
            text: 'Number of Deliveries'
        }
    },
    legend: {
        position: 'bottom',
        horizontalAlign: 'center',
        floating: false,
        offsetY: 0,
        offsetX: 0
    },
    fill: {
        opacity: 1
    },
    tooltip: {
        y: {
            formatter: function(val) {
                return val + " Deliveries"
            }
        }
    },
    grid: {
        borderColor: '#f1f1f1',
        strokeDashArray: 4,
        xaxis: {
            lines: {
                show: false
            }
        }
    },
    states: {
        hover: {
            filter: {
                type: 'darken',
                value: 0.9
            }
        }
    }
};

var chart = new ApexCharts(document.querySelector("#monthlyDeliveriesChart"), options);
chart.render();
</script>