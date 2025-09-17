<div id="supplyChainFlowChart"></div>
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.30.0/dist/apexcharts.min.js"></script>
<script>
<?php
$app = new App();

// Get farmer_id for the current user
$farmerQuery = "SELECT id FROM farmers WHERE user_id = {$_SESSION['user_id']}";
$farmer = $app->select_one($farmerQuery);
$farmerId = $farmer->id ?? 0;

// Get last 12 months for supply chain flow
$months = [];
$supplyChainData = [];

// Generate last 12 months
for ($i = 11; $i >= 0; $i--) {
    $months[] = date('M Y', strtotime("-$i months"));
}

// Get monthly deliveries (outgoing produce) - BARS  
$monthlyDeliveries = [];
foreach ($months as $index => $month) {
    $monthYear = date('Y-m', strtotime("-" . (11 - $index) . " months"));
    
    $query = "SELECT COUNT(*) as count 
              FROM produce_deliveries pd
              JOIN farm_products fp ON pd.farm_product_id = fp.id
              JOIN farms f ON fp.farm_id = f.id
              WHERE f.farmer_id = $farmerId
              AND DATE_FORMAT(pd.delivery_date, '%Y-%m') = '$monthYear'";
    $result = $app->select_one($query);
    $monthlyDeliveries[] = floatval($result->count);
}

// Get monthly revenue (incoming money) - BARS
$monthlyRevenue = [];
foreach ($months as $index => $month) {
    $monthYear = date('Y-m', strtotime("-" . (11 - $index) . " months"));
    
    $query = "SELECT COALESCE(SUM(pd.total_value), 0) as revenue 
              FROM produce_deliveries pd
              JOIN farm_products fp ON pd.farm_product_id = fp.id
              JOIN farms f ON fp.farm_id = f.id
              WHERE f.farmer_id = $farmerId 
              AND pd.status = 'paid'
              AND DATE_FORMAT(pd.delivery_date, '%Y-%m') = '$monthYear'";
    $result = $app->select_one($query);
    $monthlyRevenue[] = floatval($result->revenue);
}

// Get success rate percentage - LINE
$successRateFlow = [];
foreach ($months as $index => $month) {
    $monthYear = date('Y-m', strtotime("-" . (11 - $index) . " months"));
    
    $query = "SELECT 
              COUNT(*) as total,
              SUM(CASE WHEN status IN ('verified', 'paid') THEN 1 ELSE 0 END) as successful
              FROM produce_deliveries pd
              JOIN farm_products fp ON pd.farm_product_id = fp.id
              JOIN farms f ON fp.farm_id = f.id
              WHERE f.farmer_id = $farmerId
              AND DATE_FORMAT(pd.delivery_date, '%Y-%m') = '$monthYear'";
    $result = $app->select_one($query);
    
    if ($result->total > 0) {
        $successRateFlow[] = floatval(($result->successful / $result->total) * 100);
    } else {
        $successRateFlow[] = 0;
    }
}

// Get average payment time - LINE
$paymentTimeFlow = [];
foreach ($months as $index => $month) {
    $monthYear = date('Y-m', strtotime("-" . (11 - $index) . " months"));
    
    $query = "SELECT AVG(DATEDIFF(sale_date, delivery_date)) as avg_days
              FROM produce_deliveries pd
              JOIN farm_products fp ON pd.farm_product_id = fp.id
              JOIN farms f ON fp.farm_id = f.id
              WHERE f.farmer_id = $farmerId
              AND pd.status = 'paid' 
              AND sale_date IS NOT NULL
              AND DATE_FORMAT(pd.delivery_date, '%Y-%m') = '$monthYear'";
    $result = $app->select_one($query);
    $paymentTimeFlow[] = floatval($result->avg_days ?? 0);
}

$supplyChainSeries = [
    [
        'name' => 'Deliveries Made',
        'type' => 'column',
        'data' => $monthlyDeliveries
    ],
    [
        'name' => 'Revenue (KES 000s)',
        'type' => 'column',
        'data' => array_map(function($value) { return $value / 1000; }, $monthlyRevenue) // Convert to thousands
    ],
    [
        'name' => 'Success Rate %',
        'type' => 'line',
        'data' => $successRateFlow
    ],
    [
        'name' => 'Payment Days',
        'type' => 'line',
        'data' => $paymentTimeFlow
    ]
];
?>

var options = {
    series: <?php echo json_encode($supplyChainSeries); ?>,
    chart: {
        height: 350,
        type: 'line',
        stacked: false,
        zoom: {
            enabled: true
        },
        toolbar: {
            show: false
        }
    },
    colors: ['#70A136', '#4A220F', '#17a2b8', '#ffc107'], // Using your brand colors
    dataLabels: {
        enabled: false
    },
    stroke: {
        width: [0, 0, 4, 4], // No stroke for bars, 4px for lines
        curve: 'smooth'
    },
    plotOptions: {
        bar: {
            columnWidth: '50%',
            borderRadius: 4
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
    legend: {
        position: 'top',
        horizontalAlign: 'left'
    },
    markers: {
        size: [0, 0, 6, 6], // No markers for bars, 6px for lines
        hover: {
            sizeOffset: 3
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
                text: 'Deliveries / Revenue (000s)',
            },
            labels: {
                formatter: function(val) {
                    return val.toLocaleString();
                }
            }
        },
        {
            opposite: true,
            title: {
                text: 'Success Rate % / Payment Days'
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
        y: [{
                formatter: function(val) {
                    return val.toFixed(0) + ' deliveries';
                }
            },
            {
                formatter: function(val) {
                    return 'KES ' + (val * 1000).toLocaleString();
                }
            },
            {
                formatter: function(val) {
                    return val.toFixed(1) + '% success rate';
                }
            },
            {
                formatter: function(val) {
                    return val.toFixed(1) + ' days to payment';
                }
            }
        ]
    },
    grid: {
        borderColor: '#f1f1f1',
        strokeDashArray: 3
    }
};

var chart = new ApexCharts(document.querySelector("#supplyChainFlowChart"), options);
chart.render();
</script>