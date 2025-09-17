<div id="inputTrendChart" style="height: 350px;"></div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.30.0/dist/apexcharts.min.js"></script>
<script>
<?php
$app = new App();

// Get agrovet_id for the current staff
$staffQuery = "SELECT s.agrovet_id 
              FROM agrovet_staff s 
              WHERE s.user_id = {$_SESSION['user_id']}";
$staffResult = $app->select_one($staffQuery);
$agrovetId = $staffResult->agrovet_id ?? 0;

// Get input request trend data for the last 12 months
$trendCategories = [];
$trendData = [];

for ($i = 11; $i >= 0; $i--) {
    $monthStart = date('Y-m-01', strtotime("-$i months"));
    $monthEnd = date('Y-m-t', strtotime("-$i months"));
    
    $trendCategories[] = date('M Y', strtotime($monthStart));
    
    $query = "SELECT COUNT(*) as count
              FROM input_credit_applications
              WHERE agrovet_id = $agrovetId
              AND application_date BETWEEN '$monthStart' AND '$monthEnd'";
    
    $result = $app->select_one($query);
    $trendData[] = $result->count ?? 0;
}
?>

var trendOptions = {
    series: [{
        name: 'Input Requests',
        data: <?php echo json_encode($trendData); ?>
    }],
    chart: {
        height: 350,
        type: 'line',
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
    dataLabels: {
        enabled: false
    },
    stroke: {
        curve: 'smooth',
        width: 2
    },
    colors: ['#6AA32D'],
    xaxis: {
        categories: <?php echo json_encode($trendCategories); ?>,
        title: {
            text: 'Month'
        }
    },
    yaxis: {
        title: {
            text: 'Number of Input Requests'
        }
    },
    grid: {
        borderColor: '#e7e7e7',
        row: {
            colors: ['#f3f3f3', 'transparent'],
            opacity: 0.5
        }
    },
    markers: {
        size: 5
    },
    tooltip: {
        y: {
            formatter: function(val) {
                return val + " input requests"
            }
        }
    }
};

var chart = new ApexCharts(document.querySelector("#inputTrendChart"), trendOptions);
chart.render();
</script>