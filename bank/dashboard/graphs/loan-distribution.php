<div id="loanDistributionChart"></div>
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.30.0/dist/apexcharts.min.js"></script>
<script>
<?php
$app = new App();

// Get bank_id for the current staff
$staffQuery = "SELECT s.bank_id FROM bank_staff s WHERE s.user_id = {$_SESSION['user_id']}";
$staffResult = $app->select_one($staffQuery);
$bankId = $staffResult->bank_id ?? 0;

// Get monthly counts for different loan types
$months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$loanTypes = $app->select_all("SELECT DISTINCT lt.name FROM loan_types lt WHERE lt.bank_id = $bankId OR lt.provider_type = 'bank'");
$loanCounts = [];

foreach ($loanTypes as $loanType) {
    $counts = [];
    foreach ($months as $index => $month) {
        $monthNum = $index + 1;
        
        $query = "SELECT COUNT(*) as count FROM approved_loans al
                  JOIN loan_applications la ON al.loan_application_id = la.id
                  JOIN loan_types lt ON la.loan_type_id = lt.id
                  WHERE MONTH(al.disbursement_date) = $monthNum
                  AND YEAR(al.disbursement_date) = YEAR(CURRENT_DATE())
                  AND la.bank_id = $bankId
                  AND lt.name = '$loanType->name'";
        $result = $app->select_one($query);
        $counts[] = $result->count;
    }
    $loanCounts[] = [
        'name' => $loanType->name,
        'data' => $counts
    ];
}
?>

var options = {
    series: <?php echo json_encode($loanCounts); ?>,
    chart: {
        height: 350,
        type: 'line',
        zoom: {
            enabled: true
        },
        toolbar: {
            show: false
        }
    },
    colors: ['#70A136', '#4A220F', '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4'], // Using your brand colors first
    dataLabels: {
        enabled: false
    },
    stroke: {
        width: 3,
        curve: 'smooth',
        dashArray: 0
    },
    legend: {
        position: 'bottom',
        horizontalAlign: 'center'
    },
    markers: {
        size: 6,
        hover: {
            sizeOffset: 8
        }
    },
    xaxis: {
        categories: <?php echo json_encode($months); ?>,
        title: {
            text: 'Month'
        }
    },
    yaxis: {
        title: {
            text: 'Loans Disbursed'
        }
    },
    tooltip: {
        y: {
            title: {
                formatter: function(val) {
                    return val + " Loans"
                }
            }
        }
    },
    grid: {
        borderColor: '#f1f1f1',
    }
};

var chart = new ApexCharts(document.querySelector("#loanDistributionChart"), options);
chart.render();
</script>