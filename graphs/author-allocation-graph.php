<canvas id="leads-source" class="chartjs-chart w-100 p-4"></canvas>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    <?php


    $app = new App();

    // Fetch the total number of posts for each author
    $query = "SELECT a.Admin_firstname, a.Admin_lastname, COUNT(p.post_id) AS post_count
          FROM admin a
          LEFT JOIN posts p ON a.Admin_email = p.post_author
          GROUP BY a.Admin_id
          ORDER BY post_count DESC
          LIMIT 4";
    $result = $app->select_all($query);

    $authorData = [];
    $backgroundColor = ['#6AA32D', '#BA8448', '#C09F80', '#D3B48C'];

    foreach ($result as $row) {
        $authorName = $row->Admin_firstname . ' ' . $row->Admin_lastname;
        $postCount = $row->post_count;

        $authorData[] = $postCount;
    }
    ?>

    /* Leads By Source Chart */
    var ctx = document.getElementById('leads-source').getContext('2d');
    var chartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            datasets: [{
                label: 'Posts by Author',
                data: <?php echo json_encode($authorData); ?>,
                backgroundColor: <?php echo json_encode($backgroundColor); ?>,
                borderWidth: 0,
            }],
        },
        options: {
            cutout: '85%',
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.parsed;
                            return label;
                        },
                    },
                },
            },
        },
        plugins: [{
            afterUpdate: function(chart) {
                var arcs = chart.getDatasetMeta(0).data;
                arcs.forEach(function(arc) {
                    arc.round = {
                        x: (chart.chartArea.left + chart.chartArea.right) / 2,
                        y: (chart.chartArea.top + chart.chartArea.bottom) / 2,
                        radius: (arc.outerRadius + arc.innerRadius) / 2,
                        thickness: (arc.outerRadius - arc.innerRadius) / 2,
                        backgroundColor: arc.options.backgroundColor,
                    };
                });
            },
            afterDraw: function(chart) {
                var ctx = chart.ctx;
                chart.getDatasetMeta(0).data.forEach(function(arc) {
                    var startAngle = Math.PI / 2 - arc.startAngle;
                    var endAngle = Math.PI / 2 - arc.endAngle;
                    ctx.save();
                    ctx.translate(arc.round.x, arc.round.y);
                    ctx.fillStyle = arc.options.backgroundColor;
                    ctx.beginPath();
                    ctx.arc(
                        arc.round.radius * Math.sin(endAngle),
                        arc.round.radius * Math.cos(endAngle),
                        arc.round.thickness,
                        0,
                        2 * Math.PI
                    );
                    ctx.closePath();
                    ctx.fill();
                    ctx.restore();
                });
            },
        }],
    });

    function leads(myVarVal) {
        chartInstance.data.datasets[0].backgroundColor = [
            `rgb(${myVarVal})`,
            ...<?php echo json_encode(array_slice($backgroundColor, 1)); ?>,
        ];
        chartInstance.update();
    }
</script>