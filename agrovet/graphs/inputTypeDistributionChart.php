 <!-- input -->
 <div id="inputTypeDistributionChart" style="height: 300px;"></div>
 <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.30.0/dist/apexcharts.min.js"></script>
 <script>
// Input Type Distribution Chart
<?php
                          // Get input type distribution data
                          $app = new App();
                          $staffQuery = "SELECT s.agrovet_id FROM agrovet_staff s WHERE s.user_id = {$_SESSION['user_id']}";
                          $staffResult = $app->select_one($staffQuery);
                          $agrovetId = $staffResult->agrovet_id ?? 0;
                          
                          $inputTypeQuery = "SELECT 
                                              ici.input_type as name, 
                                              COUNT(*) as count, 
                                              SUM(ici.total_price) as total_amount
                                            FROM input_credit_items ici
                                            JOIN input_credit_applications ica ON ici.credit_application_id = ica.id
                                            WHERE ica.agrovet_id = $agrovetId
                                            GROUP BY ici.input_type
                                            ORDER BY total_amount DESC";
                          $inputTypes = $app->select_all($inputTypeQuery);
                          
                          $inputTypeLabels = [];
                          $inputTypeCounts = [];
                          $inputTypeColors = ['#6AA32D', '#3498DB', '#E67E22', '#9B59B6', '#34495E'];
                          
                          foreach ($inputTypes as $index => $type) {
                              $inputTypeLabels[] = ucfirst($type->name);
                              $inputTypeCounts[] = $type->count;
                          }
                          ?>

const inputTypeOptions = {
    series: <?php echo json_encode($inputTypeCounts); ?>,
    chart: {
        type: 'donut',
        height: 300
    },
    labels: <?php echo json_encode($inputTypeLabels); ?>,
    colors: <?php echo json_encode($inputTypeColors); ?>,
    legend: {
        position: 'bottom'
    },
    plotOptions: {
        pie: {
            donut: {
                size: '55%',
                labels: {
                    show: true,
                    total: {
                        show: true,
                        label: 'Total Inputs',
                        formatter: function(w) {
                            return w.globals.seriesTotals.reduce((a, b) => a +
                                b, 0);
                        }
                    }
                }
            }
        }
    },
    dataLabels: {
        formatter: function(val, opts) {
            return opts.w.config.series[opts.seriesIndex] + ' (' + Math.round(
                val) + '%)';
        }
    },
    responsive: [{
        breakpoint: 480,
        options: {
            chart: {
                width: 300
            },
            legend: {
                position: 'bottom'
            }
        }
    }]
};

new ApexCharts(document.querySelector("#inputTypeDistributionChart"), inputTypeOptions)
    .render();
 </script>