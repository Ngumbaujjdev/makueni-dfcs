<div id="systemActivityChart"></div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.30.0/dist/apexcharts.min.js"></script>
<script>
<?php
$app = new App();

// Get monthly counts for different activity types
$months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$createCounts = [];
$updateCounts = [];
$loginCounts = [];
$otherCounts = [];

foreach ($months as $index => $month) {
    $monthNum = $index + 1;
    
    // Create operations count
    $query = "SELECT COUNT(*) as count FROM audit_logs 
              WHERE action_type = 'create' 
              AND MONTH(created_at) = $monthNum 
              AND YEAR(created_at) = YEAR(CURRENT_DATE())";
    $result = $app->select_one($query);
    $createCounts[] = $result->count ?? 0;
    
    // Update operations count
    $query = "SELECT COUNT(*) as count FROM audit_logs 
              WHERE action_type = 'update' 
              AND MONTH(created_at) = $monthNum 
              AND YEAR(created_at) = YEAR(CURRENT_DATE())";
    $result = $app->select_one($query);
    $updateCounts[] = $result->count ?? 0;
    
    // Login activities count
    $query = "SELECT COUNT(*) as count FROM activity_logs 
              WHERE activity_type = 'login' 
              AND MONTH(created_at) = $monthNum 
              AND YEAR(created_at) = YEAR(CURRENT_DATE())";
    $result = $app->select_one($query);
    $loginCounts[] = $result->count ?? 0;
    
    // Other activities count
    $query = "SELECT COUNT(*) as count FROM activity_logs 
              WHERE activity_type NOT IN ('login', 'create', 'update') 
              AND MONTH(created_at) = $monthNum 
              AND YEAR(created_at) = YEAR(CURRENT_DATE())";
    $result = $app->select_one($query);
    $otherCounts[] = $result->count ?? 0;
}
?>

var options = {
    series: [{
        name: 'Create Operations',
        data: [<?php echo implode(',', $createCounts); ?>]
    }, {
        name: 'Update Operations',
        data: [<?php echo implode(',', $updateCounts); ?>]
    }, {
        name: 'Login Activities',
        data: [<?php echo implode(',', $loginCounts); ?>]
    }, {
        name: 'Other Activities',
        data: [<?php echo implode(',', $otherCounts); ?>]
    }],
    chart: {
        type: 'bar',
        height: 350,
        stacked: false,
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
    colors: ['#6AA32D', '#BA8448', '#4B3D8F', '#2E93fA'],
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
            text: 'Number of Activities'
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
                return val + " Activities"
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

var chart = new ApexCharts(document.querySelector("#systemActivityChart"), options);
chart.render();
</script>