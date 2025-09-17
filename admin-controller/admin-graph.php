<?php
$app = new App;
$monthlyAdminCounts = $app->countItemsByMonth('admin');

// Month names
$monthNames = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

// Generate JavaScript code for the data points of admins
$adminDataPoints = [];
foreach ($monthNames as $monthName) {
    $count = isset($monthlyAdminCounts[$monthName]) ? $monthlyAdminCounts[$monthName] : 0;
    $adminDataPoints[] = "{ x: '$monthName', y: $count }";
}
$adminDataPointsJS = implode(',', $adminDataPoints);
?>
<!-- ApexCharts JS -->
<div id="monthlyChart"></div>
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.30.0/dist/apexcharts.min.js"></script>
<script>
var monthlyAdminCounts = <?php echo json_encode($monthlyAdminCounts); ?>;
var monthNames = <?php echo json_encode($monthNames); ?>;
var adminDataPoints = [];
// Ensure the correct order of months
monthNames.forEach(function(monthName) {
    var count = Math.floor(monthlyAdminCounts[monthName]) || 0;
    adminDataPoints.push({
        x: monthName,
        y: count
    });
});
var options = {
    chart: {
        height: 350,
        type: 'line',
    },
    plotOptions: {
        line: {
            markers: {
                size: 6,
            }
        }
    },
    dataLabels: {
        enabled: false
    },
    stroke: {
        width: [2],
        curve: 'smooth'
    },
    colors: ['#6AA32D'],
    series: [{
        name: 'Admins',
        data: adminDataPoints,
    }],
    xaxis: {
        categories: monthNames,
    },
    yaxis: {
        title: {
            text: 'Number of Admins'
        }
    },
    tooltip: {
        y: {
            formatter: function(val) {
                return Math.floor(val);
            }
        }
    }
};
var chart = new ApexCharts(document.querySelector("#monthlyChart"), options);
chart.render();
</script>