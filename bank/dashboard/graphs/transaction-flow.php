<div id="transactionFlowChart"></div>
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.30.0/dist/apexcharts.min.js"></script>
<script>
<?php
$app = new App();

// Get bank_id for the current staff
$staffQuery = "SELECT s.bank_id FROM bank_staff s WHERE s.user_id = {$_SESSION['user_id']}";
$staffResult = $app->select_one($staffQuery);
$bankId = $staffResult->bank_id ?? 0;

// Get last 12 months for transaction flow
$months = [];
$transactionData = [];

// Generate last 12 months
for ($i = 11; $i >= 0; $i--) {
    $months[] = date('M Y', strtotime("-$i months"));
}

// Get farmer payments (money going out) - BARS
$farmerPayments = [];
foreach ($months as $index => $month) {
    $monthYear = date('Y-m', strtotime("-" . (11 - $index) . " months"));
    
    $query = "SELECT COALESCE(SUM(amount), 0) as amount 
              FROM farmer_account_transactions 
              WHERE transaction_type = 'credit' 
              AND DATE_FORMAT(created_at, '%Y-%m') = '$monthYear'";
    $result = $app->select_one($query);
    $farmerPayments[] = floatval($result->amount);
}

// Get loan repayments (money coming in) - BARS
$loanRepayments = [];
foreach ($months as $index => $month) {
    $monthYear = date('Y-m', strtotime("-" . (11 - $index) . " months"));
    
    $query = "SELECT COALESCE(SUM(lr.amount), 0) as amount 
              FROM loan_repayments lr
              JOIN approved_loans al ON lr.approved_loan_id = al.id
              WHERE al.bank_id = $bankId 
              AND DATE_FORMAT(lr.payment_date, '%Y-%m') = '$monthYear'";
    $result = $app->select_one($query);
    $loanRepayments[] = floatval($result->amount);
}

// Get net cash flow (inflow - outflow) - LINE
$netCashFlow = [];
foreach ($months as $index => $month) {
    $monthYear = date('Y-m', strtotime("-" . (11 - $index) . " months"));
    
    // Total inflow (loan repayments)
    $inflowQuery = "SELECT COALESCE(SUM(lr.amount), 0) as amount 
                    FROM loan_repayments lr
                    JOIN approved_loans al ON lr.approved_loan_id = al.id
                    WHERE al.bank_id = $bankId 
                    AND DATE_FORMAT(lr.payment_date, '%Y-%m') = '$monthYear'";
    $inflow = $app->select_one($inflowQuery);
    
    // Total outflow (farmer payments + commission + agrovet repayments)
    $outflowQuery = "SELECT 
                     COALESCE((SELECT SUM(amount) FROM farmer_account_transactions 
                              WHERE transaction_type = 'credit' 
                              AND DATE_FORMAT(created_at, '%Y-%m') = '$monthYear'), 0) +
                     COALESCE((SELECT SUM(amount) FROM sacco_account_transactions 
                              WHERE transaction_type = 'credit' 
                              AND DATE_FORMAT(created_at, '%Y-%m') = '$monthYear'), 0) +
                     COALESCE((SELECT SUM(amount) FROM agrovet_account_transactions 
                              WHERE transaction_type = 'credit' 
                              AND DATE_FORMAT(created_at, '%Y-%m') = '$monthYear'), 0) as amount";
    $outflow = $app->select_one($outflowQuery);
    
    $netCashFlow[] = floatval($inflow->amount) - floatval($outflow->amount);
}

// Get transaction count - LINE
$transactionCount = [];
foreach ($months as $index => $month) {
    $monthYear = date('Y-m', strtotime("-" . (11 - $index) . " months"));
    
    $query = "SELECT 
              (SELECT COUNT(*) FROM farmer_account_transactions 
               WHERE DATE_FORMAT(created_at, '%Y-%m') = '$monthYear') +
              (SELECT COUNT(*) FROM loan_repayments lr
               JOIN approved_loans al ON lr.approved_loan_id = al.id
               WHERE al.bank_id = $bankId 
               AND DATE_FORMAT(lr.payment_date, '%Y-%m') = '$monthYear') +
              (SELECT COUNT(*) FROM sacco_account_transactions 
               WHERE DATE_FORMAT(created_at, '%Y-%m') = '$monthYear') as count";
    $result = $app->select_one($query);
    $transactionCount[] = floatval($result->count);
}

$transactionSeries = [
    [
        'name' => 'Loan Repayments (Inflow)',
        'type' => 'column',
        'data' => $loanRepayments
    ],
    [
        'name' => 'Farmer Payments (Outflow)',
        'type' => 'column',
        'data' => $farmerPayments
    ],
    [
        'name' => 'Net Cash Flow',
        'type' => 'line',
        'data' => $netCashFlow
    ],
    [
        'name' => 'Transaction Count',
        'type' => 'line',
        'data' => $transactionCount
    ]
];
?>

var options = {
    series: <?php echo json_encode($transactionSeries); ?>,
    chart: {
        height: 350,
        type: 'line',
        stacked: false,
        zoom: {
            enabled: true
        },
        toolbar: {
            show: false
        }
    },
    colors: ['#70A136', '#4A220F', '#17a2b8', '#ffc107'], // Using your brand colors
    dataLabels: {
        enabled: false
    },
    stroke: {
        width: [0, 0, 4, 4], // No stroke for bars, 4px for lines
        curve: 'smooth'
    },
    plotOptions: {
        bar: {
            columnWidth: '50%',
            borderRadius: 4
        }
    },
    fill: {
        opacity: [0.85, 0.85, 1, 1],
        gradient: {
            inverseColors: false,
            shade: 'light',
            type: "vertical",
            opacityFrom: 0.85,
            opacityTo: 0.55,
            stops: [0, 100, 100, 100]
        }
    },
    legend: {
        position: 'top',
        horizontalAlign: 'left'
    },
    markers: {
        size: [0, 0, 6, 6], // No markers for bars, 6px for lines
        hover: {
            sizeOffset: 3
        }
    },
    xaxis: {
        categories: <?php echo json_encode($months); ?>,
        title: {
            text: 'Month'
        }
    },
    yaxis: [{
            title: {
                text: 'Amount (KES)',
            },
            labels: {
                formatter: function(val) {
                    return 'KES ' + val.toLocaleString();
                }
            }
        },
        {
            opposite: true,
            title: {
                text: 'Transaction Count'
            },
            labels: {
                formatter: function(val) {
                    return val.toFixed(0);
                }
            }
        }
    ],
    tooltip: {
        shared: true,
        intersect: false,
        y: [{
                formatter: function(val) {
                    return 'KES ' + val.toLocaleString();
                }
            },
            {
                formatter: function(val) {
                    return 'KES ' + val.toLocaleString();
                }
            },
            {
                formatter: function(val) {
                    return 'KES ' + val.toLocaleString();
                }
            },
            {
                formatter: function(val) {
                    return val.toFixed(0) + ' transactions';
                }
            }
        ]
    },
    grid: {
        borderColor: '#f1f1f1',
        strokeDashArray: 3
    }
};

var chart = new ApexCharts(document.querySelector("#transactionFlowChart"), options);
chart.render();
</script>