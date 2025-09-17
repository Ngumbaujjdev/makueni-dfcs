  <div id="disbursementRepaymentChart" style="height: 300px;"></div>
  <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.30.0/dist/apexcharts.min.js"></script>
  <script>
// Disbursement vs Repayment Chart
<?php
                            // Get monthly disbursement and repayment data
                            $app = new App();
                            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                            $currentMonth = date('n'); // 1-12
                            
                            $disbursementData = [];
                            $repaymentData = [];
                            
                            for ($i = 1; $i <= 12; $i++) {
                                // Disbursements
                                $disbursementQuery = "SELECT COALESCE(SUM(aic.approved_amount), 0) as total 
                                                     FROM approved_input_credits aic
                                                     JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                                     WHERE ica.agrovet_id = $agrovetId
                                                     AND MONTH(aic.fulfillment_date) = $i 
                                                     AND YEAR(aic.fulfillment_date) = YEAR(CURRENT_DATE())";
                                $disbursementResult = $app->select_one($disbursementQuery);
                                $disbursementData[] = $disbursementResult->total ?? 0;
                                
                                // Repayments
                                $repaymentQuery = "SELECT COALESCE(SUM(icr.amount), 0) as total 
                                                  FROM input_credit_repayments icr
                                                  JOIN approved_input_credits aic ON icr.approved_credit_id = aic.id
                                                  JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                                  WHERE ica.agrovet_id = $agrovetId
                                                  AND MONTH(icr.deduction_date) = $i 
                                                  AND YEAR(icr.deduction_date) = YEAR(CURRENT_DATE())";
                                $repaymentResult = $app->select_one($repaymentQuery);
                                $repaymentData[] = $repaymentResult->total ?? 0;
                            }
                            ?>

let disbursementRepaymentOptions = {
    series: [{
        name: 'Disbursements',
        type: 'column',
        data: <?php echo json_encode($disbursementData); ?>
    }, {
        name: 'Repayments',
        type: 'area',
        data: <?php echo json_encode($repaymentData); ?>
    }],
    chart: {
        height: 300,
        type: 'line',
        toolbar: {
            show: false
        }
    },
    stroke: {
        width: [0, 3],
        curve: 'smooth'
    },
    colors: ['#6AA32D', '#3498DB'],
    fill: {
        type: ['solid', 'gradient'],
        opacity: [1, 0.3],
        gradient: {
            shade: 'light',
            type: "vertical",
            shadeIntensity: 0.5,
            opacityFrom: 0.7,
            opacityTo: 0.2,
            stops: [0, 100]
        }
    },
    labels: <?php echo json_encode($months); ?>,
    markers: {
        size: 0
    },
    xaxis: {
        type: 'category'
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
    }],
    tooltip: {
        y: {
            formatter: function(val) {
                return 'KES ' + val.toLocaleString();
            }
        }
    },
    legend: {
        position: 'top'
    }
};

new ApexCharts(document.querySelector("#disbursementRepaymentChart"),
    disbursementRepaymentOptions).render();
  </script>