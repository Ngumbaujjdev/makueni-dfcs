  <div id="userDistributionChart"></div>

  <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.30.0/dist/apexcharts.min.js"></script>
  <script>
<?php
$app = new App();

// Get monthly counts for different user types
$months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$farmerCounts = [];
$bankStaffCounts = [];
$agrovetStaffCounts = [];

foreach ($months as $index => $month) {
    $monthNum = $index + 1;
    
    // Farmers count
    $query = "SELECT COUNT(*) as count FROM farmers 
             WHERE MONTH(created_at) = $monthNum 
             AND YEAR(created_at) = YEAR(CURRENT_DATE())";
    $result = $app->select_one($query);
    $farmerCounts[] = $result->count;
    
    // Bank staff count
    $query = "SELECT COUNT(*) as count FROM bank_staff 
             WHERE MONTH(created_at) = $monthNum 
             AND YEAR(created_at) = YEAR(CURRENT_DATE())";
    $result = $app->select_one($query);
    $bankStaffCounts[] = $result->count;
    
    // Agrovet staff count
    $query = "SELECT COUNT(*) as count FROM agrovet_staff 
             WHERE MONTH(created_at) = $monthNum 
             AND YEAR(created_at) = YEAR(CURRENT_DATE())";
    $result = $app->select_one($query);
    $agrovetStaffCounts[] = $result->count;
}
?>

var options = {
    series: [{
        name: 'Farmers',
        data: [<?php echo implode(',', $farmerCounts); ?>]
    }, {
        name: 'Bank Staff',
        data: [<?php echo implode(',', $bankStaffCounts); ?>]
    }, {
        name: 'Agrovet Staff',
        data: [<?php echo implode(',', $agrovetStaffCounts); ?>]
    }],
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
    colors: ['#6AA32D', '#BA8448', '#4B3D8F'], // Custom colors matching your theme
    dataLabels: {
        enabled: false
    },
    stroke: {
        width: [2, 2, 2],
        curve: 'smooth',
        dashArray: [0, 0, 0]
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
            text: 'Number of Users'
        }
    },
    tooltip: {
        y: [{
            title: {
                formatter: function(val) {
                    return val + " Users"
                }
            }
        }, {
            title: {
                formatter: function(val) {
                    return val + " Users"
                }
            }
        }, {
            title: {
                formatter: function(val) {
                    return val + " Users"
                }
            }
        }]
    },
    grid: {
        borderColor: '#f1f1f1',
    }
};

var chart = new ApexCharts(document.querySelector("#userDistributionChart"), options);
chart.render();
  </script>