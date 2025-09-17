<div id="loanStatusChart"></div>
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.30.0/dist/apexcharts.min.js"></script>
<script>
<?php
$app = new App();

// Get loan counts by status for SACCO loans only
$query = "SELECT
            CASE
                WHEN status = 'pending' OR status = 'under_review' THEN 'Pending'
                WHEN status = 'approved' OR status = 'disbursed' THEN 'Approved'
                WHEN status = 'completed' THEN 'Completed'
                WHEN status = 'rejected' THEN 'Rejected'
                WHEN status = 'defaulted' THEN 'Defaulted'
                WHEN status = 'cancelled' THEN 'Cancelled'
                ELSE 'Other'
            END as status_group,
            COUNT(*) as count
          FROM loan_applications
          WHERE provider_type = 'sacco'
          GROUP BY status_group
          ORDER BY count DESC";

$loanStatusData = $app->select_all($query);

// Process the data
$statusLabels = [];
$statusCounts = [];
$statusColors = [
    'Approved' => '#6AA32D',    // Green
    'Pending' => '#F5B041',     // Orange
    'Rejected' => '#E74C3C',    // Red
    'Completed' => '#3498DB',   // Blue
    'Defaulted' => '#9B59B6',   // Purple
    'Cancelled' => '#95A5A6',   // Gray
    'Other' => '#BDC3C7'        // Light Gray
];
$colorList = [];

// Calculate total for percentages
$totalLoans = 0;
foreach ($loanStatusData as $data) {
    $totalLoans += $data->count;
}

// Process each status
foreach ($loanStatusData as $data) {
    $statusLabels[] = $data->status_group;
    $statusCounts[] = (int)$data->count;
    $colorList[] = $statusColors[$data->status_group] ?? '#BDC3C7';
}

// Calculate percentages for the status labels
$percentages = array_map(function($count) use ($totalLoans) {
    return $totalLoans > 0 ? round(($count / $totalLoans) * 100, 1) : 0;
}, $statusCounts);

// Combine status with percentages for display
$statusWithPercentages = array_map(function($label, $percentage) {
    return "$label ($percentage%)";
}, $statusLabels, $percentages);
?>

var options = {
    series: [{
        data: <?php echo json_encode($statusCounts); ?>
    }],
    chart: {
        type: 'bar',
        height: 350,
        toolbar: {
            show: true,
            tools: {
                download: true,
                selection: false,
                zoom: false,
                zoomin: false,
                zoomout: false,
                pan: false,
                reset: false
            }
        }
    },
    title: {
        text: 'SACCO Loan Status Distribution',
        align: 'center',
        style: {
            fontSize: '16px',
            fontWeight: 'bold'
        }
    },
    plotOptions: {
        bar: {
            borderRadius: 6,
            distributed: true,
            horizontal: true,
            dataLabels: {
                position: 'top',
            },
        }
    },
    colors: <?php echo json_encode($colorList); ?>,
    dataLabels: {
        enabled: true,
        formatter: function(val) {
            return val;
        },
        offsetX: 20,
        style: {
            fontSize: '12px',
            colors: ['#000']
        }
    },
    stroke: {
        width: 1,
        colors: ['#fff']
    },
    xaxis: {
        categories: <?php echo json_encode($statusWithPercentages); ?>,
    },
    yaxis: {
        title: {
            text: 'Status'
        }
    },
    tooltip: {
        y: {
            formatter: function(val) {
                return val + " SACCO loans";
            }
        }
    },
    legend: {
        show: false
    },
    grid: {
        borderColor: '#f1f1f1',
        strokeDashArray: 4
    }
};

var chart = new ApexCharts(document.querySelector("#loanStatusChart"), options);
chart.render();
</script>