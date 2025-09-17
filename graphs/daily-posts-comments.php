<div id="crm-profits-earned"></div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.30.0/dist/apexcharts.min.js"></script>
<script>
<?php
    $app = new App();

    // Fetch the counts of posts and comments for the past 7 days
    $postCounts = array();
    $commentCounts = array();
    $days = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');

    foreach ($days as $day) {
        $query = "SELECT COUNT(*) AS count FROM posts WHERE DATE(created_at) = DATE(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(DATE_SUB(CURDATE(), INTERVAL 1 DAY)) + 1 - WEEKDAY(CURDATE()) DAY))";
        $result = $app->select_one($query);
        $postCounts[$day] = $result->count;

        $query = "SELECT COUNT(*) AS count FROM comments WHERE DATE(created_at) = DATE(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(DATE_SUB(CURDATE(), INTERVAL 1 DAY)) + 1 - WEEKDAY(CURDATE()) DAY))";
        $result = $app->select_one($query);
        $commentCounts[$day] = $result->count;
    }
    ?>

var options1 = {
    series: [{
            name: "Posts",
            data: [
                <?php echo $postCounts['Sun']; ?>,
                <?php echo $postCounts['Mon']; ?>,
                <?php echo $postCounts['Tue']; ?>,
                <?php echo $postCounts['Wed']; ?>,
                <?php echo $postCounts['Thu']; ?>,
                <?php echo $postCounts['Fri']; ?>,
                <?php echo $postCounts['Sat']; ?>
            ],
        },
        {
            name: "Comments",
            data: [
                <?php echo $commentCounts['Sun']; ?>,
                <?php echo $commentCounts['Mon']; ?>,
                <?php echo $commentCounts['Tue']; ?>,
                <?php echo $commentCounts['Wed']; ?>,
                <?php echo $commentCounts['Thu']; ?>,
                <?php echo $commentCounts['Fri']; ?>,
                <?php echo $commentCounts['Sat']; ?>
            ],
        },
    ],
    chart: {
        type: "bar",
        height: 180,
        toolbar: {
            show: false,
        },
    },
    grid: {
        borderColor: "#f1f1f1",
        strokeDashArray: 3,
    },
    colors: ['#6AA32D', '#BA8448'], // Updated colors
    plotOptions: {
        bar: {
            colors: {
                ranges: [{
                        from: -100,
                        to: -46,
                        color: "#ebeff5",
                    },
                    {
                        from: -45,
                        to: 0,
                        color: "#ebeff5",
                    },
                ],
            },
            columnWidth: "60%",
            borderRadius: 5,
        },
    },
    dataLabels: {
        enabled: false,
    },
    stroke: {
        show: true,
        width: 2,
        colors: undefined,
    },
    legend: {
        show: false,
        position: "top",
    },
    yaxis: {
        title: {
            style: {
                color: "#adb5be",
                fontSize: "13px",
                fontFamily: "poppins, sans-serif",
                fontWeight: 600,
                cssClass: "apexcharts-yaxis-label",
            },
        },
        labels: {
            formatter: function(y) {
                return y.toFixed(0) + "";
            },
        },
    },
    xaxis: {
        type: "week",
        categories: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
        axisBorder: {
            show: true,
            color: "rgba(119, 119, 142, 0.05)",
            offsetX: 0,
            offsetY: 0,
        },
        axisTicks: {
            show: true,
            borderType: "solid",
            color: "rgba(119, 119, 142, 0.05)",
            width: 6,
            offsetX: 0,
            offsetY: 0,
        },
        labels: {
            rotate: -90,
        },
    },
};

var chart1 = new ApexCharts(document.querySelector("#crm-profits-earned"), options1);
chart1.render();
</script>