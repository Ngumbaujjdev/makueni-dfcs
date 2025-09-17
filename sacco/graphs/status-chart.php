  <script>
                // Status Donut Chart
                var statusChartOptions = {
                    series: [
                        <?php echo $status_counts['pending']; ?>,
                        <?php echo $status_counts['verified']; ?>,
                        <?php echo $status_counts['rejected']; ?>,
                        <?php echo $status_counts['sold']; ?>,
                        <?php echo $status_counts['paid']; ?>
                    ],
                    chart: {
                        type: 'donut',
                        height: 200,
                        sparkline: {
                            enabled: true
                        }
                    },
                    colors: ['#FFC107', '#3498DB', '#E74C3C', '#2ECC71', '#9B59B6'],
                    labels: ['Pending', 'Verified', 'Rejected', 'Sold', 'Paid'],
                    stroke: {
                        width: 2
                    },
                    legend: {
                        show: false
                    },
                    dataLabels: {
                        enabled: false
                    },
                    tooltip: {
                        fillSeriesColor: false,
                        y: {
                            formatter: function(val) {
                                return val + " deliveries";
                            }
                        }
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '65%',
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Total',
                                        formatter: function(w) {
                                            return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                        }
                                    }
                                }
                            }
                        }
                    }
                };

                if (document.getElementById('statusDonutChart')) {
                    var statusChart = new ApexCharts(document.querySelector("#statusDonutChart"), statusChartOptions);
                    statusChart.render();
                }
                </script>
