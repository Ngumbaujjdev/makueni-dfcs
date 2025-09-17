<div id="crm-total-customers"></div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.30.0/dist/apexcharts.min.js"></script>
<script>
/* Total Customers chart */
var crm1 = {
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
        curve: "smooth", // Changed to "smooth" for a curved line
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
        data: [20, 24, 32, 28, 36, 42, 38, 46, 52], // Adjusted data for a curved line
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
    colors: ["rgb(245, 184, 73)"],
};

var crm1 = new ApexCharts(document.querySelector("#crm-total-customers"), crm1);
crm1.render();
</script>