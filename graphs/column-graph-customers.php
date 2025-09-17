<div id="crm-total-revenue"></div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.30.0/dist/apexcharts.min.js"></script>
<script>
/* Comments chart */
var crm2 = {
    chart: {
        type: "line",
        height: 40,
        width: 100,
        sparkline: {
            enabled: true,
        },
    },
    stroke: {
        show: true,
        curve: "smooth",
        lineCap: "butt",
        colors: undefined,
        width: 1.5,
        dashArray: 0,
    },
    fill: {
        type: "gradient",
        gradient: {
            opacityFrom: 0.9,
            opacityTo: 0.9,
            stops: [0, 98],
        },
    },
    series: [{
        name: "Value",
        data: [15, 18, 22, 26, 30, 34, 28, 36, 40], // Adjusted data for a curved pattern
    }, ],
    yaxis: {
        min: 0,
        show: false,
        axisBorder: {
            show: false,
        },
    },
    xaxis: {
        show: false,
        axisBorder: {
            show: false,
        },
    },
    tooltip: {
        enabled: false,
    },
    colors: ["#BA8448"],
};

var crm2 = new ApexCharts(document.querySelector("#crm-total-revenue"), crm2);
crm2.render();
</script>