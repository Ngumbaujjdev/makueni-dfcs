<div id="crm-conversion-ratio"></div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.30.0/dist/apexcharts.min.js"></script>
<script>
/* Conversion Ratio Chart */
var crm3 = {
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
        data: [10, 14, 20, 16, 24, 30, 26, 34, 40],
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
    colors: ["#C09F80"],
};

var crm3 = new ApexCharts(document.querySelector("#crm-conversion-ratio"), crm3);
crm3.render();
</script>