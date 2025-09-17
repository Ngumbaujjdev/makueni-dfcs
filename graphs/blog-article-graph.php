<?php
$app = new App();

// Fetch monthly counts for posts
$monthlyPostCounts = $app->countItemsByMonth('posts');

// Fetch monthly counts for comments
$monthlyCommentCounts = $app->countItemsByMonth('comments');

// Fetch monthly counts for categories
$monthlyCategoryCounts = $app->countItemsByMonth('categories');

// Fetch monthly counts for authors
$monthlyAuthorCounts = $app->countDistinctAuthorsByMonth('posts');

// Month names
$monthNames = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

// Generate JavaScript code for the data points
$postDataPoints = [];
$commentDataPoints = [];
$categoryDataPoints = [];
$authorDataPoints = [];

foreach ($monthNames as $monthName) {
    $postCount = isset($monthlyPostCounts[$monthName]) ? $monthlyPostCounts[$monthName] : 0;
    $commentCount = isset($monthlyCommentCounts[$monthName]) ? $monthlyCommentCounts[$monthName] : 0;
    $categoryCount = isset($monthlyCategoryCounts[$monthName]) ? $monthlyCategoryCounts[$monthName] : 0;
    $authorCount = isset($monthlyAuthorCounts[$monthName]) ? $monthlyAuthorCounts[$monthName] : 0;

    $postDataPoints[] = "{ x: '$monthName', y: $postCount }";
    $commentDataPoints[] = "{ x: '$monthName', y: $commentCount }";
    $categoryDataPoints[] = "{ x: '$monthName', y: $categoryCount }";
    $authorDataPoints[] = "{ x: '$monthName', y: $authorCount }";
}

$postDataPointsJS = implode(',', $postDataPoints);
$commentDataPointsJS = implode(',', $commentDataPoints);
$categoryDataPointsJS = implode(',', $categoryDataPoints);
$authorDataPointsJS = implode(',', $authorDataPoints);
?>

<!-- ApexCharts JS -->
<div id="monthlyChart"></div>
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.30.0/dist/apexcharts.min.js"></script>
<script>
    var monthlyPostCounts = <?php echo json_encode($monthlyPostCounts); ?>;
    var monthlyCommentCounts = <?php echo json_encode($monthlyCommentCounts); ?>;
    var monthlyCategoryCounts = <?php echo json_encode($monthlyCategoryCounts); ?>;
    var monthlyAuthorCounts = <?php echo json_encode($monthlyAuthorCounts); ?>;
    var monthNames = <?php echo json_encode($monthNames); ?>;

    var postDataPoints = [];
    var commentDataPoints = [];
    var categoryDataPoints = [];
    var authorDataPoints = [];

    // Ensure the correct order of months
    monthNames.forEach(function(monthName) {
        var postCount = Math.floor(monthlyPostCounts[monthName]) || 0;
        var commentCount = Math.floor(monthlyCommentCounts[monthName]) || 0;
        var categoryCount = Math.floor(monthlyCategoryCounts[monthName]) || 0;
        var authorCount = Math.floor(monthlyAuthorCounts[monthName]) || 0;

        postDataPoints.push({
            x: monthName,
            y: postCount
        });
        commentDataPoints.push({
            x: monthName,
            y: commentCount
        });
        categoryDataPoints.push({
            x: monthName,
            y: categoryCount
        });
        authorDataPoints.push({
            x: monthName,
            y: authorCount
        });
    });

    var options = {
        chart: {
            height: 350,
            type: 'line', // Line chart
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
            curve: 'smooth' // Smooth curve
        },
        colors: ['#6AA32D', '#BA8448', '#C09F80', '#D3B48C'], // Adjust colors as needed
        series: [{
                name: 'Posts',
                data: postDataPoints
            },
            {
                name: 'Comments',
                data: commentDataPoints
            },
            {
                name: 'Categories',
                data: categoryDataPoints
            },
            {
                name: 'Authors',
                data: authorDataPoints
            }
        ],
        xaxis: {
            categories: monthNames,
        },
        yaxis: {
            title: {
                text: 'Number of Items'
            }
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return Math.floor(val); // Remove decimal places in tooltip
                }
            }
        }
    };

    var chart = new ApexCharts(document.querySelector("#monthlyChart"), options);
    chart.render();
</script>