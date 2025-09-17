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
                        
                        // Get active loans data
                        $activeLoansQuery = "SELECT 
                            COUNT(*) as active_count,
                            SUM(al.approved_amount) as total_borrowed,
                            SUM(al.remaining_balance) as total_remaining,
                            MIN(al.expected_completion_date) as next_due_date,
                            AVG(al.interest_rate) as avg_interest_rate
                        FROM 
                            approved_loans al 
                        JOIN 
                            loan_applications la ON al.loan_application_id = la.id 
                        WHERE 
                            la.farmer_id = {$farmer_id} 
                            AND al.status IN ('active', 'pending_disbursement')";
                        
                        $activeLoans = $app->select_one($activeLoansQuery);
                        
                        // Get loan repayment data
                        $repaymentQuery = "SELECT 
                            SUM(lr.amount) as total_repaid
                        FROM 
                            loan_repayments lr
                        JOIN 
                            approved_loans al ON lr.approved_loan_id = al.id
                        JOIN 
                            loan_applications la ON al.loan_application_id = la.id
                        WHERE 
                            la.farmer_id = {$farmer_id}";
                        
                        $repayment = $app->select_one($repaymentQuery);
                        $totalRepaid = $repayment ? $repayment->total_repaid : 0;
                        
                        // Calculate progress percentage
                        $totalBorrowed = $activeLoans->total_borrowed > 0 ? $activeLoans->total_borrowed : 1;
                        $progressPercentage = ($totalRepaid / $totalBorrowed) * 100;
                        ?>

                <!-- Active Loans Summary Cards -->
                <div class="row mt-2">
                    <!-- Total Active Loans -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden" style="background:#6AA32D!important;">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded"
                                            style="background-color: white!important;">
                                            <i style="color:#6AA32D" class="fa-solid fa-credit-card fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-white mb-0">Active Loans</p>
                                                <h4 style="color:wheat;" class="fw-semibold mt-1">
                                                    <?php echo $activeLoans ? $activeLoans->active_count : 0 ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Borrowed -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-money-bill fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Total Borrowed</p>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    KES <?php echo number_format($activeLoans->total_borrowed, 2) ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Remaining Balance -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-hand-holding-dollar fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Remaining Balance</p>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    KES <?php echo number_format($activeLoans->total_remaining, 2) ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Next Payment Due -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-calendar-day fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Next Payment Due</p>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php echo $activeLoans->next_due_date ? date('M d, Y', strtotime($activeLoans->next_due_date)) : 'N/A' ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Average Interest Rate -->
                    <div class="col-xxl-6 col-lg-6 col-md-6 mt-2">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-percent fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Average Interest Rate
                                                </p>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php echo number_format($activeLoans->avg_interest_rate, 2) ?>%
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Repayment Progress -->
                    <div class="col-xxl-6 col-lg-6 col-md-6 mt-2">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-chart-pie fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div class="w-100">
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Repayment Progress</p>
                                                <div class="progress mt-2" style="height: 10px;">
                                                    <div class="progress-bar bg-success" role="progressbar"
                                                        style="width: <?php echo min(100, $progressPercentage); ?>%"
                                                        aria-valuenow="<?php echo min(100, $progressPercentage); ?>"
                                                        aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between mt-1">
                                                    <small class="text-muted">KES
                                                        <?php echo number_format($totalRepaid, 2) ?> paid</small>
                                                    <small
                                                        class="text-muted"><?php echo number_format($progressPercentage, 1) ?>%
                                                        complete</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Loan Facts Section -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card custom-card shadow-sm">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <div class="card-title">
                                    <i class="fa-solid fa-chart-column text-success me-2"></i> Quick Loan Facts
                                </div>
                                <button class="btn btn-sm btn-outline-success rounded-pill" id="refreshStats">
                                    <i class="fa-solid fa-arrows-rotate me-1"></i> Refresh
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Left Column - Statistics -->


                                    <!-- Right Column - Recommendations -->
                                    <div class="col-md-12 border-start border-light">
                                        <h6 class="fw-semibold mb-3"><i
                                                class="fa-solid fa-lightbulb text-warning me-2"></i>Loan Insights</h6>

                                        <div class="alert bg-success-transparent mb-3">
                                            <div class="d-flex">
                                                <div class="me-2">
                                                    <i class="fa-solid fa-check-circle text-success fs-4"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 text-success">Strong Payment History</h6>
                                                    <p class="mb-0 text-dark">Your consistent loan repayments have
                                                        improved your creditworthiness score by 15 points in the last 3
                                                        months.</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="alert bg-info-transparent mb-3">
                                            <div class="d-flex">
                                                <div class="me-2">
                                                    <i class="fa-solid fa-chart-line text-info fs-4"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 text-info">Loan Eligibility</h6>
                                                    <p class="mb-0 text-dark">Based on your repayment history, you may
                                                        qualify for up to KES 150,000 on your next loan application.</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="alert bg-warning-transparent">
                                            <div class="d-flex">
                                                <div class="me-2">
                                                    <i class="fa-solid fa-bell text-warning fs-4"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 text-warning">Payment Reminder</h6>
                                                    <p class="mb-0 text-dark">Your next produce delivery could help
                                                        clear 35% of your remaining loan balance.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Repayments Section -->
                <?php if (!empty($detailedLoans)): ?>
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card custom-card shadow-sm">
                            <div class="card-header bg-light">
                                <div class="card-title">
                                    <i class="fa-solid fa-receipt text-success me-2"></i> Recent Repayments
                                </div>
                            </div>
                            <div class="card-body">
                                <?php
                                     // Get recent repayments
                                     $repaymentsQuery = "SELECT 
                                         lr.id,
                                         lr.approved_loan_id,
                                         lr.amount,
                                         lr.payment_date,
                                         lr.notes,
                                         al.loan_application_id,
                                         lt.name AS loan_type
                                     FROM 
                                         loan_repayments lr
                                     JOIN 
                                         approved_loans al ON lr.approved_loan_id = al.id
                                     JOIN 
                                         loan_applications la ON al.loan_application_id = la.id
                                     JOIN 
                                         loan_types lt ON la.loan_type_id = lt.id
                                     WHERE 
                                         la.farmer_id = {$farmer_id}
                                     ORDER BY 
                                         lr.payment_date DESC
                                     LIMIT 5";
                                     
                                     $repayments = $app->select_all($repaymentsQuery);
                                     
                                     if (!empty($repayments)):
                                     ?>
                                <div class="table-responsive">
                                    <table class="table table-borderless table-hover">
                                        <thead>
                                            <tr class="bg-light-transparent">
                                                <th><i class="fa-solid fa-calendar text-primary me-1"></i> Date</th>
                                                <th><i class="fa-solid fa-tag text-success me-1"></i> Loan</th>
                                                <th><i class="fa-solid fa-money-bill text-warning me-1"></i> Amount</th>
                                                <th><i class="fa-solid fa-info-circle text-info me-1"></i> Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($repayments as $repayment): ?>
                                            <tr>
                                                <td>
                                                    <span class="text-nowrap">
                                                        <i class="fa-regular fa-calendar-check text-success me-1"></i>
                                                        <?php echo date('M d, Y', strtotime($repayment->payment_date)) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-success-transparent text-success rounded-pill">
                                                        LOAN<?php echo str_pad($repayment->approved_loan_id, 5, '0', STR_PAD_LEFT); ?>
                                                        <span
                                                            class="ms-1 fw-normal">(<?php echo $repayment->loan_type ?>)</span>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="fw-semibold text-success">
                                                        KES <?php echo number_format($repayment->amount, 2) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?php echo $repayment->notes ? htmlspecialchars($repayment->notes) : 'Automatic deduction from produce sale' ?>
                                                    </small>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fa-solid fa-receipt text-muted mb-2 fs-4"></i>
                                    <p class="text-muted mb-0">No repayment records found for your active loans yet</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>


                <!-- Active Loans Table -->
                <div id="displayActiveLoans">
                    <!-- Content will be loaded here by AJAX -->
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Loading...</span>
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
        displayActiveLoans();
    });

    function displayActiveLoans() {
        let displayActiveLoans = "true";
        $.ajax({
            url: "http://localhost/dfcs/ajax/loan-controller/display-farmer-active-loans.php",
            type: 'POST',
            data: {
                displayActiveLoans: displayActiveLoans,
            },
            success: function(data, status) {
                $('#displayActiveLoans').html(data);
            },
            error: function() {
                toastr.error('Failed to load active loans', 'Error', {
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
        window.location.href = "http://localhost/dfcs/farmers/loans/view-loan-details?id=" + loanId;
    }

    function viewRepayments(loanId) {
        // Redirect to loan repayments page
        window.location.href = "http://localhost/dfcs/farmers/loans/view-repayments?id=" + loanId;
    }

    function applyForLoan() {
        // Redirect to loan application form
        window.location.href = "http://localhost/dfcs/farmers/loans/apply";
    }
    </script>
</body>



</html>