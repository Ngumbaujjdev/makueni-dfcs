  <!-- Mini Chart Script -->
                <script>
                // Commission Mini Chart
                var commissionOptions = {
                    series: [{
                        name: 'Commission',
                        data: [<?php echo implode(',', $chart_values); ?>]
                    }],
                    chart: {
                        height: 120,
                        type: 'area',
                        sparkline: {
                            enabled: true
                        },
                        toolbar: {
                            show: false
                        }
                    },
                    colors: ['#6AA32D'],
                    stroke: {
                        curve: 'smooth',
                        width: 2
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.7,
                            opacityTo: 0.3,
                            stops: [0, 90, 100]
                        }
                    },
                    tooltip: {
                        fixed: {
                            enabled: false
                        },
                        x: {
                            show: true,
                            formatter: function(val, opts) {
                                return <?php echo json_encode($chart_months); ?>[val - 1];
                            }
                        },
                        y: {
                            formatter: function(val) {
                                return 'KES ' + val.toFixed(0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                            }
                        },
                        marker: {
                            show: false
                        }
                    }
                };

                if (document.getElementById('commissionMiniChart')) {
                    var commissionChart = new ApexCharts(document.querySelector("#commissionMiniChart"),
                        commissionOptions);
                    commissionChart.render();
                }
                </script>