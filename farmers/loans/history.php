<?php include "../../config/config.php" ?>
<?php include "../../libs/App.php" ?>
<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-vertical-style="overlay" data-theme-mode="light"
    data-header-styles="light" data-menu-styles="light" data-toggled="close">

<head>
    <!-- Meta Data -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Makueni Distributed Farmers Cooperative System</title>
    <meta name="Description"
        content="Digital platform connecting Kilimo SACCO, farmers, banks, and agrovets in Makueni County">
    <meta name="Author" content="Joshua Ngumbau John">
    <meta name="keywords" content="Makueni farming, Kilimo SACCO, agricultural cooperative, digital farming, 
        fruit farming, mango farming, orange farming, pixie farming, agricultural inputs, farm loans">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="http://localhost/dfcs/assets/images/favicon/favicon-96x96.png"
        sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="http://localhost/dfcs/assets/images/favicon/favicon.svg" />
    <link rel="shortcut icon" href="http://localhost/dfcs/assets/images/favicon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180"
        href="http://localhost/dfcs/assets/images/favicon/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Baituti Adventures" />
    <link rel="manifest" href="http://localhost/dfcs/assets/images/favicon/site.webmanifest" />
    <!-- font awesome -->
    <!-- Choices JS -->
    <script src="http://localhost/dfcs/assets/libs/choices.js/public/assets/scripts/choices.min.js">
    </script>
    <!-- font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Main Theme Js -->
    <script src="http://localhost/dfcs/assets/js/main.js"></script>

    <!-- Bootstrap Css -->
    <link id="style" href="http://localhost/dfcs/assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Style Css -->
    <link href="http://localhost/dfcs/assets/css/styles.min.css" rel="stylesheet">

    <!-- Icons Css -->
    <link href="http://localhost/dfcs/assets/css/icons.css" rel="stylesheet">

    <!-- Node Waves Css -->
    <link href="http://localhost/dfcs/assets/libs/node-waves/waves.min.css" rel="stylesheet">

    <!-- Simplebar Css -->
    <link href="http://localhost/dfcs/assets/libs/simplebar/simplebar.min.css" rel="stylesheet">

    <!-- Color Picker Css -->
    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/flatpickr/flatpickr.min.css">
    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/%40simonwep/pickr/themes/nano.min.css">

    <!-- Choices Css -->
    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/choices.js/public/assets/styles/choices.min.css">
    <!-- mermaid -->

    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/gridjs/theme/mermaid.min.css">

    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/apexcharts/apexcharts.css">
    <script src="https://cdn.jsdelivr.net/npm/tinycolor2@1.4.1/dist/tinycolor-min.js"></script>
    <link rel="stylesheet" href="http://localhost/dfcs/toast/toast.css">
    <!-- datatables -->
    <link rel="stylesheet" href="http://localhost/dfcs/assets/data-tables/1.12.1/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet"
        href="http://localhost/dfcs/assets/data-tables/responsive/2.3.0/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" href="http://localhost/dfcs/assets/data-tables/buttons/2.2.3/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="http://localhost/dfcs/toast/toast.css">
    <!-- TINY COLORS -->


</head>
<style>
/* Custom styles for the page */
.circular-chart {
    display: block;
    margin: 0 auto;
}

.circle-bg {
    fill: none;
    stroke: #eee;
    stroke-width: 2;
}

.circle {
    fill: none;
    stroke-width: 2;
    stroke-linecap: round;
    animation: progress 1s ease-out forwards;
}

@keyframes progress {
    0% {
        stroke-dasharray: 0, 100;
    }
}

.percentage {
    font-family: sans-serif;
    font-size: 0.5em;
    text-anchor: middle;
}

.tier-item {
    transition: all 0.3s ease;
}

.tier-item:hover {
    transform: translateX(3px);
}

.avatar-xs {
    width: 24px;
    height: 24px;
    line-height: 24px;
    font-size: 12px;
}
</style>

<body>
    <!-- loader -->
    <?php include "../../includes/loader.php" ?>

    <div class="page">
        <!-- app-header -->
        <?php include "../../includes/navigation.php" ?>
        <!-- /app-header -->
        <!-- Start::app-sidebar -->
        <?php include "../../includes/sidebar.php" ?>
        <!-- End::app-sidebar -->

        <!-- End::app-sidebar -->
        <!-- End::app-sidebar -->

        <!-- Start::app-content -->
        <div class="main-content app-content">
            <div class="container-fluid">
                <!-- Page Header -->
                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                    <div>
                        <?php
                            $app = new App;
                            
                            // Get farmer details including their registration number
                            $query = "SELECT u.*, f.registration_number, f.category_id, fc.name as category_name
                                      FROM users u
                                      LEFT JOIN farmers f ON u.id = f.user_id
                                      LEFT JOIN farmer_categories fc ON f.category_id = fc.id
                                      WHERE u.id = " . $_SESSION['user_id'];
                            
                            $farmer = $app->select_one($query);
                            ?>

                        <p class="fw-semibold fs-18 mb-0">
                            Welcome <?php echo $farmer->first_name ?> <?php echo $farmer->last_name ?>
                            <span class="badge bg-success ms-2"><?php echo $farmer->registration_number ?></span>
                        </p>

                        <span class="fs-semibold text-muted pt-5">
                            Active Loans Dashboard
                            <?php if($farmer->category_name): ?>
                            - <?php echo $farmer->category_name ?> Farmer
                            <?php endif; ?>
                        </span>
                    </div>
                </div>

                <!-- Active Loans Summary -->
                <?php 
                        // Initialize the app
                        $app = new App;
                        
                        // Get farmer details including their registration number
                        $query = "SELECT u.*, f.id as farmer_id, f.registration_number, f.category_id, fc.name as category_name
                                  FROM users u
                                  LEFT JOIN farmers f ON u.id = f.user_id
                                  LEFT JOIN farmer_categories fc ON f.category_id = fc.id
                                  WHERE u.id = " . $_SESSION['user_id'];
                        
                        $farmer = $app->select_one($query);
                        $farmer_id = $farmer->farmer_id;
                        
                       
                        ?>

                <!-- Row 1: Loan Performance Summary -->
                <div class="row mt-2">
                    <div class="col-12">
                        <h6 class="mb-3 text-dark"><i class="fa-solid fa-chart-line text-success me-2"></i>Loan
                            Performance Summary</h6>
                    </div>

                    <!-- Total Loans Ever Requested -->
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden" style="background:#6AA32D!important;">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded"
                                            style="background-color: white!important;">
                                            <i style="color:#6AA32D" class="fa-solid fa-file-invoice fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-white mb-0">Total Loans</p>
                                                <h4 style="color:wheat;" class="fw-semibold mt-1">
                                                    <?php 
                                                     // Get total number of loans ever requested
                                                     $totalLoansQuery = "SELECT COUNT(*) as total_loans 
                                                                        FROM loan_applications 
                                                                        WHERE farmer_id = {$farmer_id} AND provider_type = 'sacco'";
                                                                        
                                                     $totalLoans = $app->select_one($totalLoansQuery);
                                                     echo $totalLoans ? $totalLoans->total_loans : 0;
                                                     ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Approval Rate -->
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-check-circle fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Approval Rate</p>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php 
                                                         // Calculate approval rate
                                                         $approvalRateQuery = "SELECT 
                                                                              (SUM(CASE WHEN status = 'approved' OR status = 'disbursed' OR status = 'completed' THEN 1 ELSE 0 END) / COUNT(*)) * 100 as approval_rate
                                                                              FROM loan_applications 
                                                                              WHERE farmer_id = {$farmer_id} AND provider_type = 'sacco'";
                                                         $approvalRate = $app->select_one($approvalRateQuery);
                                                         echo $approvalRate && $approvalRate->approval_rate ? number_format($approvalRate->approval_rate, 1) : '0.0';
                                                         ?>%
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Average Loan Amount -->
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-calculator fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Average Loan</p>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php 
                                                        // Calculate average approved loan amount
                                                        $avgLoanQuery = "SELECT AVG(approved_amount) as avg_amount
                                                                        FROM approved_loans al
                                                                        JOIN loan_applications la ON al.loan_application_id = la.id
                                                                        WHERE la.farmer_id = {$farmer_id} AND la.provider_type = 'sacco'";
                                                        $avgLoan = $app->select_one($avgLoanQuery);
                                                        echo 'KES ' . number_format($avgLoan && $avgLoan->avg_amount ? $avgLoan->avg_amount : 0, 2);
                                                        ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Borrowed Historically -->
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-money-bill-wave fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Total Borrowed</p>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php 
                                                        // Calculate total amount borrowed historically
                                                        $totalBorrowedQuery = "SELECT SUM(approved_amount) as total_borrowed
                                                                             FROM approved_loans al
                                                                             JOIN loan_applications la ON al.loan_application_id = la.id
                                                                             WHERE la.farmer_id = {$farmer_id} AND la.provider_type = 'sacco'";
                                                        $totalBorrowed = $app->select_one($totalBorrowedQuery);
                                                        echo 'KES ' . number_format($totalBorrowed && $totalBorrowed->total_borrowed ? $totalBorrowed->total_borrowed : 0, 2);
                                                        ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Row 2: Repayment Efficiency - Redesigned -->
                <div class="row mt-4">
                    <div class="col-lg-12">
                        <div class="card custom-card shadow-sm border-0">
                            <div class="card-header d-flex align-items-center"
                                style="background: linear-gradient(to right, #f8faf5, #ffffff);">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-success-transparent me-2">
                                        <i class="fa-solid fa-chart-pie text-success"></i>
                                    </div>
                                    <h6 class="mb-0 fw-semibold">Repayment Performance</h6>
                                </div>
                                <div class="ms-auto">
                                    <span class="badge rounded-pill bg-success-transparent text-success">
                                        <i class="fa-solid fa-arrow-trend-up me-1"></i> Performance Metrics
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Left side metrics -->
                                    <div class="col-lg-7">
                                        <div class="d-flex align-items-center gap-3 mb-4">
                                            <!-- On-Time Payment Rate -->
                                            <div class="text-center position-relative" style="width: 100px;">
                                                <?php 
                                                        // On-time payment rate calculation (same as before)
                                                        $completedLoansQuery = "SELECT 
                                                                              (SUM(CASE WHEN al.status = 'completed' THEN 1 ELSE 0 END) / COUNT(*)) * 100 as completion_rate
                                                                              FROM approved_loans al
                                                                              JOIN loan_applications la ON al.loan_application_id = la.id
                                                                              WHERE la.farmer_id = {$farmer_id} AND la.provider_type = 'sacco' AND la.status IN ('approved', 'completed', 'defaulted')";
                                                        $completedLoans = $app->select_one($completedLoansQuery);
                                                        $completionRate = $completedLoans && $completedLoans->completion_rate ? $completedLoans->completion_rate : 0;
                                                        ?>
                                                <div class="position-relative d-inline-block">
                                                    <svg viewBox="0 0 36 36" class="circular-chart" width="80"
                                                        height="80">
                                                        <path class="circle-bg" d="M18 2.0845
                                                                    a 15.9155 15.9155 0 0 1 0 31.831
                                                                    a 15.9155 15.9155 0 0 1 0 -31.831" fill="none"
                                                            stroke="#eee" stroke-width="2" />
                                                        <path class="circle" d="M18 2.0845
                                                                    a 15.9155 15.9155 0 0 1 0 31.831
                                                                    a 15.9155 15.9155 0 0 1 0 -31.831" fill="none"
                                                            stroke="#6AA32D" stroke-width="2"
                                                            stroke-dasharray="<?php echo $completionRate; ?>, 100" />
                                                        <text x="18" y="20.5" class="percentage"
                                                            style="font-size: 0.5em; font-weight: bold; fill: #333; text-anchor: middle;"><?php echo number_format($completionRate, 1); ?>%</text>
                                                    </svg>
                                                </div>
                                                <p class="mb-0 mt-1 text-muted small">On-Time Payments</p>
                                            </div>

                                            <div class="border-start ps-3">
                                                <!-- Total Interest Paid -->
                                                <div class="mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2">
                                                            <span class="avatar avatar-xs bg-warning-transparent">
                                                                <i class="fa-solid fa-percentage text-warning"></i>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 text-muted small">Interest Paid</p>
                                                            <h6 class="mb-0 fw-semibold">
                                                                <?php 
                                                                 // Calculate total interest paid (total repayment - principal)
                                                                 $interestPaidQuery = "SELECT 
                                                                                     SUM(total_repayment_amount - approved_amount) as total_interest
                                                                                     FROM approved_loans al
                                                                                     JOIN loan_applications la ON al.loan_application_id = la.id
                                                                                     WHERE la.farmer_id = {$farmer_id} AND la.provider_type = 'sacco' AND al.status = 'completed'";
                                                                 $interestPaid = $app->select_one($interestPaidQuery);
                                                                 echo 'KES ' . number_format($interestPaid && $interestPaid->total_interest ? $interestPaid->total_interest : 0, 2);
                                                                 ?>
                                                            </h6>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Average Repayment Speed -->
                                                <div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2">
                                                            <span class="avatar avatar-xs bg-info-transparent">
                                                                <i class="fa-solid fa-gauge-high text-info"></i>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 text-muted small">Avg. Repayment Time</p>
                                                            <h6 class="mb-0 fw-semibold">
                                                                <?php 
                                                                // Calculate average days to completion from approved loans
                                                                $avgRepaymentQuery = "SELECT 
                                                                                    AVG(DATEDIFF(
                                                                                        (SELECT MAX(payment_date) FROM loan_repayments WHERE approved_loan_id = al.id),
                                                                                        al.disbursement_date
                                                                                    )) as avg_days
                                                                                    FROM approved_loans al
                                                                                    JOIN loan_applications la ON al.loan_application_id = la.id
                                                                                    WHERE la.farmer_id = {$farmer_id} AND la.provider_type = 'sacco' AND al.status = 'completed'";
                                                                $avgRepayment = $app->select_one($avgRepaymentQuery);
                                                                echo $avgRepayment && $avgRepayment->avg_days ? number_format($avgRepayment->avg_days, 0) . ' days' : 'N/A';
                                                                ?>
                                                            </h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right side metrics -->
                                    <div class="col-lg-5">
                                        <!-- Early Repayment Progress -->
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="me-2">
                                                <span class="avatar avatar-xs bg-success-transparent">
                                                    <i class="fa-solid fa-bolt text-success"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <p class="mb-0 small">Early Payments</p>
                                                    <?php 
                                                       // Calculate early repayment rate
                                                       $earlyRepaymentQuery = "SELECT 
                                                                             (SUM(CASE 
                                                                                 WHEN (SELECT MAX(payment_date) FROM loan_repayments WHERE approved_loan_id = al.id) < al.expected_completion_date 
                                                                                 THEN 1 ELSE 0 END) / COUNT(*)) * 100 as early_rate
                                                                             FROM approved_loans al
                                                                             JOIN loan_applications la ON al.loan_application_id = la.id
                                                                             WHERE la.farmer_id = {$farmer_id} AND al.status = 'completed'";
                                                       $earlyRepayment = $app->select_one($earlyRepaymentQuery);
                                                       $earlyRate = $earlyRepayment && $earlyRepayment->early_rate ? $earlyRepayment->early_rate : 0;
                                                       ?>
                                                    <span
                                                        class="small text-success fw-semibold"><?php echo number_format($earlyRate, 1); ?>%</span>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-success" role="progressbar"
                                                        style="width: <?php echo $earlyRate; ?>%"
                                                        aria-valuenow="<?php echo $earlyRate; ?>" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Financial Insight -->
                                        <div class="mt-3 p-3 bg-light-transparent rounded-3 border border-light">
                                            <div class="d-flex">
                                                <div class="me-2">
                                                    <span class="avatar avatar-sm bg-primary-transparent">
                                                        <i class="fa-solid fa-lightbulb text-primary"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 fs-14">Repayment Insight:</h6>
                                                    <?php if($completionRate > 80): ?>
                                                    <p class="mb-0 text-muted small">Your excellent payment history
                                                        qualifies you for premium interest rates on future loans.</p>
                                                    <?php elseif($completionRate > 50): ?>
                                                    <p class="mb-0 text-muted small">Consistent on-time payments are
                                                        improving your creditworthiness score.</p>
                                                    <?php else: ?>
                                                    <p class="mb-0 text-muted small">Improving your payment consistency
                                                        will help you qualify for larger loans.</p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Row 3: Creditworthiness Trends - Redesigned -->
                <div class="row mt-4">
                    <div class="col-lg-12">
                        <div class="card custom-card shadow-sm border-0">
                            <div class="card-header d-flex align-items-center"
                                style="background: linear-gradient(to right, #f8faf5, #ffffff);">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-success-transparent me-2">
                                        <i class="fa-solid fa-award text-success"></i>
                                    </div>
                                    <h6 class="mb-0 fw-semibold">Credit Profile & Eligibility</h6>
                                </div>
                                <div class="ms-auto">
                                    <span class="badge rounded-pill bg-primary-transparent text-primary">
                                        <i class="fa-solid fa-star me-1"></i> Credit Assessment
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Credit Score Section -->
                                    <div class="col-lg-4 col-md-6">
                                        <?php 
                        // Get the most recent creditworthiness score
                        $creditScoreQuery = "SELECT 
                                          creditworthiness_score
                                          FROM loan_applications
                                          WHERE farmer_id = {$farmer_id}
                                          ORDER BY application_date DESC
                                          LIMIT 1";
                        $creditScore = $app->select_one($creditScoreQuery);
                        $score = $creditScore ? number_format($creditScore->creditworthiness_score, 1) : 0;
                        
                        // Determine score range and styling
                        $scoreColorClass = 'success';
                        $scoreLabel = 'Excellent';
                        $scoreBgColor = '#ebf7ee';
                        
                        if($score < 60) {
                            $scoreColorClass = 'danger';
                            $scoreLabel = 'Needs Work';
                            $scoreBgColor = '#fbeaea';
                        } elseif($score < 75) {
                            $scoreColorClass = 'warning';
                            $scoreLabel = 'Fair';
                            $scoreBgColor = '#fff8e9';
                        } elseif($score < 90) {
                            $scoreColorClass = 'info';
                            $scoreLabel = 'Good';
                            $scoreBgColor = '#e5f5fa';
                        }
                        ?>

                                        <div class="credit-score-container py-3" style="position: relative;">
                                            <!-- Credit Score Dial -->
                                            <div class="d-flex align-items-center justify-content-center">
                                                <div class="position-relative" style="width:140px; height:140px;">
                                                    <!-- Circular background -->
                                                    <div class="position-absolute top-0 start-0 w-100 h-100 rounded-circle"
                                                        style="background: linear-gradient(135deg, #f5f7fa 0%, <?php echo $scoreBgColor; ?> 100%); 
                                                border: 1px solid rgba(0,0,0,0.05);">
                                                    </div>

                                                    <!-- Score value -->
                                                    <div
                                                        class="position-absolute top-50 start-50 translate-middle text-center">
                                                        <h2 class="mb-0 fw-bold text-<?php echo $scoreColorClass; ?>">
                                                            <?php echo $score; ?></h2>
                                                        <p
                                                            class="mb-0 badge bg-<?php echo $scoreColorClass; ?>-transparent text-<?php echo $scoreColorClass; ?>">
                                                            <?php echo $scoreLabel; ?>
                                                        </p>
                                                    </div>

                                                    <!-- Label -->
                                                    <div class="position-absolute"
                                                        style="bottom: -25px; width: 100%; text-align: center;">
                                                        <span class="badge bg-light text-dark border fw-normal">Credit
                                                            Score</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Score Change -->
                                            <?php 
                            // Get the earliest and most recent creditworthiness scores to calculate change
                            $firstScoreQuery = "SELECT 
                                              creditworthiness_score, application_date
                                              FROM loan_applications
                                              WHERE farmer_id = {$farmer_id}
                                              ORDER BY application_date ASC
                                              LIMIT 1";
                            $firstScore = $app->select_one($firstScoreQuery);
                            
                            $lastScoreQuery = "SELECT 
                                             creditworthiness_score, application_date
                                             FROM loan_applications
                                             WHERE farmer_id = {$farmer_id}
                                             ORDER BY application_date DESC
                                             LIMIT 1";
                            $lastScore = $app->select_one($lastScoreQuery);
                            
                            // Calculate change if both scores exist
                            if($firstScore && $lastScore && $firstScore->application_date != $lastScore->application_date) {
                                $change = $lastScore->creditworthiness_score - $firstScore->creditworthiness_score;
                                $changeDirection = ($change >= 0) ? 'up' : 'down';
                                $changeColor = ($change >= 0) ? 'success' : 'danger';
                                $changeIcon = ($change >= 0) ? 'fa-arrow-up' : 'fa-arrow-down';
                                
                                echo '<div class="text-center mt-4">';
                                echo '<span class="badge bg-'.$changeColor.'-transparent text-'.$changeColor.' px-2 py-1">';
                                echo '<i class="fas '.$changeIcon.' me-1"></i>';
                                echo number_format(abs($change), 1) . " points since first application";
                                echo '</span>';
                                echo '</div>';
                            }
                            ?>
                                        </div>
                                    </div>

                                    <!-- Credit Tiers and Eligibility -->
                                    <div class="col-lg-4 col-md-6">
                                        <div class="p-3 h-100">
                                            <h6 class="fw-semibold d-flex align-items-center mb-3">
                                                <i class="fa-solid fa-layer-group text-success me-2"></i> Credit Tier
                                                Status
                                            </h6>

                                            <?php 
                            // Determine credit tier based on latest score
                            $tiers = [
                                ['name' => 'Platinum', 'min' => 90, 'icon' => 'fa-gem', 'color' => 'info'],
                                ['name' => 'Gold', 'min' => 80, 'icon' => 'fa-medal', 'color' => 'warning'],
                                ['name' => 'Silver', 'min' => 70, 'icon' => 'fa-award', 'color' => 'secondary'],
                                ['name' => 'Bronze', 'min' => 60, 'icon' => 'fa-certificate', 'color' => 'danger'],
                                ['name' => 'Basic', 'min' => 0, 'icon' => 'fa-star', 'color' => 'dark']
                            ];
                            
                            $currentTier = 'Not Rated';
                            $currentScore = $creditScore ? $creditScore->creditworthiness_score : 0;
                            
                            foreach($tiers as $index => $tier) {
                                $isActive = $currentScore >= $tier['min'];
                                $isCurrentTier = $isActive && (!isset($tiers[$index-1]) || $currentScore < $tiers[$index-1]['min']);
                                
                                if($isCurrentTier) {
                                    $currentTier = $tier['name'];
                                }
                                
                                echo '<div class="d-flex align-items-center mb-2 tier-item ' . ($isActive ? 'opacity-100' : 'opacity-50') . '">';
                                echo '<div class="me-3">';
                                echo '<span class="avatar avatar-sm avatar-rounded bg-' . $tier['color'] . '-transparent">';
                                echo '<i class="fa-solid ' . $tier['icon'] . ' text-' . $tier['color'] . '"></i>';
                                echo '</span>';
                                echo '</div>';
                                echo '<div class="flex-grow-1">';
                                echo '<div class="d-flex justify-content-between align-items-center">';
                                echo '<span class="fw-medium">' . $tier['name'] . ' Tier</span>';
                                if($isCurrentTier) {
                                    echo '<span class="badge bg-' . $tier['color'] . '-transparent text-' . $tier['color'] . ' rounded-pill">Current</span>';
                                }
                                echo '</div>';
                                echo '<div class="progress mt-1" style="height: 4px;">';
                                echo '<div class="progress-bar bg-' . $tier['color'] . '" role="progressbar" style="width: ' . ($isActive ? '100' : '0') . '%" aria-valuenow="' . ($isActive ? '100' : '0') . '" aria-valuemin="0" aria-valuemax="100"></div>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                            ?>
                                        </div>
                                    </div>

                                    <!-- Loan Eligibility -->
                                    <div class="col-lg-4 col-md-12">
                                        <div class="p-3 h-100 border-start border-light">
                                            <h6 class="fw-semibold d-flex align-items-center mb-3">
                                                <i class="fa-solid fa-coins text-warning me-2"></i> Loan Eligibility
                                            </h6>

                                            <?php 
                            // Calculate maximum eligible loan amount based on credit score and history
                            $maxLoanAmount = 0;
                            
                            if($creditScore) {
                                $scoreValue = $creditScore->creditworthiness_score;
                                
                                // Get farmer's category
                                $farmerCategoryQuery = "SELECT 
                                                      fc.name as category_name
                                                      FROM farmers f
                                                      JOIN farmer_categories fc ON f.category_id = fc.id
                                                      WHERE f.id = {$farmer_id}";
                                $farmerCategory = $app->select_one($farmerCategoryQuery);
                                $categoryMultiplier = 1;
                                
                                // Adjust multiplier based on farmer category
                                if($farmerCategory) {
                                    if($farmerCategory->category_name == 'Commercial Farmer') {
                                        $categoryMultiplier = 3;
                                    } elseif($farmerCategory->category_name == 'Emerging Farmer') {
                                        $categoryMultiplier = 2;
                                    }
                                }
                                
                                // Base amount on credit score
                                if($scoreValue >= 90) {
                                    $maxLoanAmount = 500000 * $categoryMultiplier;
                                } elseif($scoreValue >= 80) {
                                    $maxLoanAmount = 350000 * $categoryMultiplier;
                                } elseif($scoreValue >= 70) {
                                    $maxLoanAmount = 200000 * $categoryMultiplier;
                                } elseif($scoreValue >= 60) {
                                    $maxLoanAmount = 100000 * $categoryMultiplier;
                                } else {
                                    $maxLoanAmount = 50000 * $categoryMultiplier;
                                }
                                
                                // Check if there are any existing active loans
                                $activeLoansQuery = "SELECT 
                                                   COUNT(*) as active_count
                                                   FROM approved_loans al
                                                   JOIN loan_applications la ON al.loan_application_id = la.id
                                                   WHERE la.farmer_id = {$farmer_id}
                                                   AND al.status IN ('active', 'pending_disbursement')";
                                $activeLoans = $app->select_one($activeLoansQuery);
                                
                                // Reduce eligible amount if there are active loans
                                if($activeLoans && $activeLoans->active_count > 0) {
                                    $maxLoanAmount = $maxLoanAmount * 0.7;
                                }
                            }
                            ?>

                                            <!-- Maximum Eligible Amount -->
                                            <div class="alert bg-light-transparent border mb-3">
                                                <div class="d-flex">
                                                    <div class="me-3">
                                                        <div
                                                            class="avatar avatar-md avatar-rounded bg-success-transparent">
                                                            <i class="fa-solid fa-check-circle text-success fs-18"></i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">Maximum Eligible Amount</h6>
                                                        <h4 class="mb-0 text-success">KES
                                                            <?php echo number_format($maxLoanAmount, 2); ?></h4>
                                                        <small class="text-muted">Based on your current credit
                                                            profile</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Action Button -->
                                            <div class="text-center mt-3">
                                                <button class="btn btn-success btn-sm rounded-pill"
                                                    onclick="applyForLoan()">
                                                    <i class="fa-solid fa-plus me-1"></i> Apply for New Loan
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loan History Table Container -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card custom-card shadow-sm border-0">
                            <div class="card-header d-flex align-items-center justify-content-between"
                                style="background: linear-gradient(to right, #f8faf5, #ffffff);">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-success-transparent me-2">
                                        <i class="fa-solid fa-history text-success"></i>
                                    </div>
                                    <h6 class="mb-0 fw-semibold">Complete Loan History</h6>
                                </div>
                                <div>
                                    <button class="btn btn-sm btn-outline-success rounded-pill" id="refreshLoanHistory">
                                        <i class="fa-solid fa-arrows-rotate me-1"></i> Refresh History
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="displayLoanHistory">
                                    <!-- Content will be loaded here by AJAX -->
                                    <div class="d-flex justify-content-center py-5">
                                        <div class="spinner-border text-success" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End::app-content -->



    </div>


    <!-- Scroll To Top -->
    <div class="scrollToTop">
        <span class="arrow"><i class="ri-arrow-up-s-fill fs-20"></i></span>
    </div>
    <div id="responsive-overlay"></div>
    <!-- Scroll To Top -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- Popper JS -->
    <script src="http://localhost/dfcs/assets/libs/%40popperjs/core/umd/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="http://localhost/dfcs/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Defaultmenu JS -->
    <script src="http://localhost/dfcs/assets/js/defaultmenu.min.js"></script>
    <!-- Node Waves JS-->
    <script src="http://localhost/dfcs/assets/libs/node-waves/waves.min.js"></script>
    <!-- Sticky JS -->
    <script src="http://localhost/dfcs/assets/js/sticky.js"></script>
    <!-- Simplebar JS -->
    <script src="http://localhost/dfcs/assets/libs/simplebar/simplebar.min.js"></script>
    <script src="http://localhost/dfcs/assets/js/simplebar.js"></script>

    <!-- Color Picker JS -->
    <script src="http://localhost/dfcs/assets/libs/%40simonwep/pickr/pickr.es5.min.js"></script>
    <!-- Custom-Switcher JS -->
    <script src="http://localhost/dfcs/assets/js/custom-switcher.min.js"></script>

    <!-- Custom JS -->
    <script src="http://localhost/dfcs/assets/js/custom.js"></script>
    <!-- Used In Zoomable TIme Series Chart -->
    <script src="http://localhost/dfcs/assets/js/dataseries.js"></script>
    <!---Used In Annotations Chart-->
    <script src="http://localhost/dfcs/assets/js/apexcharts-stock-prices.js"></script>
    <!-- Datatables Cdn -->
    <script src="http://localhost/dfcs/assets/data-tables/1.12.1/js/jquery.dataTables.min.js">
    </script>
    <script src="http://localhost/dfcs/assets/data-tables/1.12.1/js/dataTables.bootstrap5.min.js">
    </script>
    <script src="http://localhost/dfcs/assets/data-tables/responsive/2.3.0/js/dataTables.responsive.min.js">
    </script>
    <script src="http://localhost/dfcs/assets/data-tables/buttons/2.2.3/js/dataTables.buttons.min.js">
    </script>
    <script src="http://localhost/dfcs/assets/data-tables/buttons/2.2.3/js/buttons.print.min.js">
    </script>
    <script src="http://localhost/dfcs/assets/cloudflare/ajax/libs/pdfmake/0.2.6/pdfmake.min.js">
    </script>
    <script src="http://localhost/dfcs/assets/cloudflare/ajax/libs/pdfmake/0.1.53/vfs_fonts.js">
    </script>
    <script src="http://localhost/dfcs/assets/data-tables/buttons/2.2.3/js/buttons.html5.min.js">
    </script>
    <script src="http://localhost/dfcs/assets/cloudflare/ajax/libs/jszip/3.10.1/jszip.min.js">
    </script>
    <!-- Internal Datatables JS -->
    <script src="http://localhost/dfcs/assets/js/datatables.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4"></script>
    <script>
    $(document).ready(() => {
        displayLoanHistory();

        // Refresh loan history when refresh button is clicked
        $("#refreshLoanHistory").on("click", function() {
            displayLoanHistory();
        });
    });

    function displayLoanHistory() {
        let displayLoanHistory = "true";
        $.ajax({
            url: "http://localhost/dfcs/ajax/loan-controller/display-farmer-loan-history.php",
            type: 'POST',
            data: {
                displayLoanHistory: displayLoanHistory,
            },
            success: function(data, status) {
                $('#displayLoanHistory').html(data);
            },
            error: function() {
                toastr.error('Failed to load loan history', 'Error', {
                    "positionClass": "toast-top-right",
                    "progressBar": true,
                    "timeOut": 3000,
                    "extendedTimeOut": 1000,
                    "hideMethod": "fadeOut"
                });
            }
        });
    }

    function viewLoanDetails(loanId) {
        // Redirect to loan details page
        window.location.href = "http://localhost/dfcs/farmers/loans/view-details?id=" + loanId;
    }

    function viewRepayments(loanId) {
        // Redirect to loan repayments page
        window.location.href = "http://localhost/dfcs/farmers/loans/view-details?id=" + loanId;
    }

    function applyForLoan() {
        // Redirect to loan application form
        window.location.href = "http://localhost/dfcs/farmers/loans/apply";
    }
    </script>
</body>



</html>