<div id="buyerComparisonChart"></div>

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

    // Get buyer statistics
    $buyerQuery = "SELECT 
                      SUBSTRING_INDEX(SUBSTRING_INDEX(pd.notes, 'Sold to:', -1), '.', 1) as buyer,
                      COUNT(*) as purchase_count,
                      SUM(pd.quantity) as total_quantity,
                      SUM(pd.total_value) as total_value
                   FROM produce_deliveries pd
                   JOIN farm_products fp ON pd.farm_product_id = fp.id
                   JOIN farms f ON fp.farm_id = f.id
                   WHERE f.farmer_id = $farmerId
                   AND pd.status = 'sold'
                   AND pd.notes LIKE '%Sold to:%'
                   GROUP BY buyer
                   ORDER BY total_value DESC
                   LIMIT 10";  // Get top 10 buyers
    
    $buyers = $app->select_all($buyerQuery);
    
    $buyerNames = [];
    $purchaseCounts = [];
    $totalQuantities = [];
    $totalValues = [];
    
    if ($buyers) {
        foreach ($buyers as $buyer) {
            $buyerNames[] = trim($buyer->buyer);
            $purchaseCounts[] = intval($buyer->purchase_count);
            $totalQuantities[] = floatval($buyer->total_quantity);
            $totalValues[] = floatval($buyer->total_value);
        }
    }
}
?>

var options = {
    series: [{
        name: 'Total Value (KES)',
        data: <?php echo json_encode($totalValues); ?>
    }, {
        name: 'Quantity (KGs)',
        data: <?php echo json_encode($totalQuantities); ?>
    }, {
        name: 'Number of Purchases',
        data: <?php echo json_encode($purchaseCounts); ?>
    }],
    chart: {
        type: 'bar',
        height: 350,
        stacked: false,
        toolbar: {
            show: false
        }
    },
    plotOptions: {
        bar: {
            horizontal: false,
            columnWidth: '55%',
            borderRadius: 5,
            endingShape: 'rounded'
        },
    },
    dataLabels: {
        enabled: false
    },
    stroke: {
        show: true,
        width: 2,
        colors: ['transparent']
    },
    colors: ['#6AA32D', '#4B3D8F', '#BA8448'],
    xaxis: {
        categories: <?php echo json_encode($buyerNames); ?>,
        title: {
            text: 'Buyers'
        },
        labels: {
            rotate: -45,
            style: {
                fontSize: '12px'
            }
        }
    },
    yaxis: [{
            title: {
                text: 'Value (KES)'
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
                text: 'Quantity (KGs)'
            },
            labels: {
                formatter: function(val) {
                    return val.toFixed(0);
                }
            }
        }
    ],
    fill: {
        opacity: 1
    },
    tooltip: {
        y: {
            formatter: function(val, {
                seriesIndex
            }) {
                if (seriesIndex === 0) {
                    return "KES " + val.toFixed(2);
                } else if (seriesIndex === 1) {
                    return val.toFixed(2) + " KGs";
                } else {
                    return val + " purchases";
                }
            }
        }
    },
    title: {
        text: 'Buyer Comparison',
        align: 'center',
        style: {
            fontSize: '16px',
            fontWeight: 'bold',
            color: '#6AA32D'
        }
    },
    legend: {
        position: 'bottom',
        horizontalAlign: 'center'
    },
    grid: {
        borderColor: '#f1f1f1',
        strokeDashArray: 4
    }
};

var chart = new ApexCharts(document.querySelector("#buyerComparisonChart"), options);
chart.render();
</script>