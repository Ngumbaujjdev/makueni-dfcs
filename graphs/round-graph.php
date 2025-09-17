<div id="crm-main"></div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.30.0/dist/apexcharts.min.js"></script>
<script>
    <?php
    $app = new App();

    $totalPosts = $app->count("SELECT COUNT(*) AS total FROM posts");
    $totalComments = $app->count("SELECT COUNT(*) AS total FROM comments");
    $totalItems = $totalPosts + $totalComments;

    $postPercentage = ($totalPosts / $totalItems) * 100;
    $commentPercentage = ($totalComments / $totalItems) * 100;
    ?>

    var options = {
        chart: {
            height: 127, // Adjust the height as desired
            width: 100, // Adjust the width as desired
            type: "radialBar",
        },
        series: [<?php echo $postPercentage; ?>, <?php echo $commentPercentage; ?>],
        colors: ["#6AA32D", "#BA8448"], // Update the colors as needed
        plotOptions: {
            radialBar: {
                hollow: {
                    margin: 0,
                    size: "55%",
                    background: "#fff",
                },
                dataLabels: {
                    name: {
                        offsetY: -10,
                        color: "#333",
                        fontSize: "0.625rem", // Adjust the font size as needed
                        show: true,
                    },
                    value: {
                        offsetY: 5,
                        color: "#333",
                        fontSize: "0.875rem", // Adjust the font size as needed
                        show: true,
                        fontWeight: 600,
                    },
                },
            },
        },
        stroke: {
            lineCap: "round",
        },
        labels: ["Posts", "Comments"],
    };

    var chart = new ApexCharts(document.querySelector("#crm-main"), options);
    chart.render();
</script>

<style>
    #crm-main .apexcharts-canvas,
    #crm-main .apexcharts-svg {
        width: auto !important;
    }

    #crm-main circle {
        fill: transparent;
    }

    #crm-main .apexcharts-datalabels-group text {
        fill: rgba(255, 255, 255, 0.9);
    }

    #crm-main #apexcharts-radialbarTrack-0 {
        stroke: rgba(0, 0, 0, 0.2);
    }
</style>