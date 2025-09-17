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
        <!-- Start::app-content -->
        <div class="main-content app-content">
            <div class="container-fluid">
                <!-- Start::page-header -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                    <div>
                        <?php
                         $app = new App;
                         
                         // Get session user_id to identify agrovet staff
                         if (session_status() === PHP_SESSION_NONE) {
                             session_start();
                         }
                         
                         $userId = $_SESSION['user_id'] ?? null;
                         
                         // Get staff agrovet_id
                         $staffQuery = "SELECT s.id as staff_id, s.agrovet_id, s.position,
                                       u.first_name, u.last_name, 
                                       a.name as agrovet_name
                                       FROM agrovet_staff s 
                                       JOIN users u ON s.user_id = u.id
                                       JOIN agrovets a ON s.agrovet_id = a.id
                                       WHERE s.user_id = :user_id";
                         
                         $staff = $app->selectOne($staffQuery, [':user_id' => $userId]);
                         ?>
                        <p class="fw-semibold fs-18 mb-0">Welcome <?php echo $staff->first_name ?>
                            <?php echo $staff->last_name ?></p>
                        <span class="fs-semibold text-muted pt-5">Input Credit History Dashboard</span>
                    </div>
                </div>

                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                    <h1 class="page-title fw-semibold fs-18 mb-0">Input Credit History</h1>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Input Credits</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Credit History</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <!-- Input Credit Stats Cards -->
                <div class="row mt-2">
                    <!-- Total Input Credits -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden" style="background:#6AA32D!important;">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded"
                                            style="background-color: white!important;">
                                            <i style="color:#6AA32D" class="fa-solid fa-folder-open fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-white mb-0">Total Input Credits</p>
                                                <?php
                                                 $query = "SELECT COUNT(*) as count 
                                                           FROM input_credit_applications 
                                                           WHERE agrovet_id = {$staff->agrovet_id}";
                                                 $result = $app->select_one($query);
                                                 $total_credits = ($result) ? $result->count : 0;
                                                 ?>
                                                <h4 style="color:wheat;" class="fw-semibold mt-1">
                                                    <?php echo $total_credits ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Approved Credits -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
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
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Approved Credits</p>
                                                <?php
                                                 $query = "SELECT COUNT(*) as count 
                                                           FROM input_credit_applications 
                                                           WHERE agrovet_id = {$staff->agrovet_id} 
                                                           AND (status = 'approved' OR status = 'fulfilled' OR status = 'completed')";
                                                 $result = $app->select_one($query);
                                                 $approved_credits = ($result) ? $result->count : 0;
                                                 $approval_rate = ($total_credits > 0) ? round(($approved_credits / $total_credits) * 100, 1) : 0;
                                                 ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php echo $approved_credits ?> <small
                                                        class="text-muted">(<?php echo $approval_rate ?>%)</small>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rejected Credits -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-times-circle fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Rejected Credits</p>
                                                <?php
                                                $query = "SELECT COUNT(*) as count 
                                                          FROM input_credit_applications 
                                                          WHERE agrovet_id = {$staff->agrovet_id} 
                                                          AND status = 'rejected'";
                                                $result = $app->select_one($query);
                                                $rejected_credits = ($result) ? $result->count : 0;
                                                $rejection_rate = ($total_credits > 0) ? round(($rejected_credits / $total_credits) * 100, 1) : 0;
                                                ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php echo $rejected_credits ?> <small
                                                        class="text-muted">(<?php echo $rejection_rate ?>%)</small>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Credit Amount -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
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
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Total Provided</p>
                                                <?php
                                                $query = "SELECT COALESCE(SUM(aic.approved_amount), 0) as total_amount 
                                                          FROM approved_input_credits aic
                                                          JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                                          WHERE ica.agrovet_id = {$staff->agrovet_id}";
                                                $result = $app->select_one($query);
                                                $total_amount = ($result) ? number_format($result->total_amount, 2) : '0.00';
                                                
                                                // Also get repayment amount
                                                $repayment_query = "SELECT 
                                                          COALESCE(SUM(icr.amount), 0) as total_repaid 
                                                          FROM input_credit_repayments icr
                                                          JOIN approved_input_credits aic ON icr.approved_credit_id = aic.id
                                                          JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                                          WHERE ica.agrovet_id = {$staff->agrovet_id}";
                                                $repayment_result = $app->select_one($repayment_query);
                                                $total_repaid = ($repayment_result) ? number_format($repayment_result->total_repaid, 2) : '0.00';
                                                ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    KES <?php echo $total_amount ?>
                                                </h4>
                                                <small class="text-muted">KES <?php echo $total_repaid ?> repaid</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Stats Row -->
                <div class="row mt-4">
                    <!-- Active Credits -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-bolt fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Active Credits</p>
                                                <?php
                                                 $query = "SELECT COUNT(*) as count 
                                                           FROM approved_input_credits aic
                                                           JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                                           WHERE ica.agrovet_id = {$staff->agrovet_id} 
                                                           AND aic.status = 'active'";
                                                 $result = $app->select_one($query);
                                                 $active_credits = ($result) ? $result->count : 0;
                                                 ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php echo $active_credits ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Completed Credits -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-flag-checkered fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Completed Credits</p>
                                                <?php
                                                    $query = "SELECT COUNT(*) as count 
                                                              FROM approved_input_credits aic
                                                              JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                                              WHERE ica.agrovet_id = {$staff->agrovet_id} 
                                                              AND aic.status = 'completed'";
                                                    $result = $app->select_one($query);
                                                    $completed_credits = ($result) ? $result->count : 0;
                                                    $completion_rate = ($approved_credits > 0) ? round(($completed_credits / $approved_credits) * 100, 1) : 0;
                                                    ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php echo $completed_credits ?> <small
                                                        class="text-muted">(<?php echo $completion_rate ?>%)</small>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Applications -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-hourglass-half fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Pending Applications
                                                </p>
                                                <?php
                                                   $query = "SELECT COUNT(*) as count 
                                                             FROM input_credit_applications 
                                                             WHERE agrovet_id = {$staff->agrovet_id} 
                                                             AND (status = 'pending' OR status = 'under_review')";
                                                   $result = $app->select_one($query);
                                                   $pending_credits = ($result) ? $result->count : 0;
                                                   ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php echo $pending_credits ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Outstanding Amount -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-hand-holding-usd fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Outstanding Balance
                                                </p>
                                                <?php
                                                    $query = "SELECT COALESCE(SUM(aic.remaining_balance), 0) as total_outstanding 
                                                              FROM approved_input_credits aic
                                                              JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                                              WHERE ica.agrovet_id = {$staff->agrovet_id}
                                                              AND aic.status = 'active'";
                                                    $result = $app->select_one($query);
                                                    $outstanding_amount = ($result) ? number_format($result->total_outstanding, 2) : '0.00';
                                                    ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    KES <?php echo $outstanding_amount ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Monthly Input Credit Metrics -->
                <div class="row mt-4">
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-header">
                                <div class="card-title">
                                    <i class="ri-line-chart-line me-2"></i> Monthly Input Credit Metrics
                                </div>
                            </div>
                            <div class="card-body">
                                <?php include "../graphs/monthly-input-distribution.php" ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- All Input Credits Section -->
                <div class="row mt-4">
                    <div class="col-xl-12">
                        <div id="allInputCreditsSection">
                            <div class="card custom-card">
                                <div class="card-header justify-content-between">
                                    <div class="card-title">
                                        <i class="ri-shopping-bag-line me-2"></i> All Input Credit Applications
                                    </div>
                                    <div class="btn-group">
                                        <button class="btn btn-outline-primary btn-sm" id="btnShowAll">All</button>
                                        <button class="btn btn-outline-success btn-sm"
                                            id="btnShowApproved">Approved</button>
                                        <button class="btn btn-outline-danger btn-sm"
                                            id="btnShowRejected">Rejected</button>
                                        <button class="btn btn-outline-warning btn-sm"
                                            id="btnShowPending">Pending</button>
                                        <button class="btn btn-outline-info btn-sm" id="btnShowActive">Active</button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="datatable-all-credits"
                                            class="table table-bordered text-nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th><i class="ri-hash-line me-1"></i>Reference</th>
                                                    <th><i class="ri-user-line me-1"></i>Farmer</th>
                                                    <th><i class="ri-store-line me-1"></i>Agrovet</th>
                                                    <th><i class="ri-money-dollar-circle-line me-1"></i>Amount (KES)
                                                    </th>
                                                    <th><i class="ri-percent-line me-1"></i>Interest</th>
                                                    <th><i class="ri-bar-chart-line me-1"></i>Credit Score</th>
                                                    <th><i class="ri-time-line me-1"></i>Date</th>
                                                    <th><i class="ri-shield-check-line me-1"></i>Status</th>
                                                    <th><i class="ri-settings-3-line me-1"></i>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Rows will be populated by AJAX call -->
                                            </tbody>
                                        </table>
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
    // Main script for input credit history page
    $(document).ready(() => {
        // Load all input credits
        displayAllInputCredits();
    });
    // Function to display all input credit applications
    function displayAllInputCredits() {
        let displayAllCredits = "true";
        $.ajax({
            url: "http://localhost/dfcs/ajax/input-credit-controller/display-all-credits.php",
            type: 'POST',
            data: {
                displayAllCredits: displayAllCredits,
            },
            success: function(data, status) {
                $('#allInputCreditsSection').html(data);
            },
            error: function(xhr, status, error) {
                console.error("Error loading input credit applications:", error);
                toastr.error('Failed to load input credit applications', 'Error', {
                    "positionClass": "toast-top-right",
                    "progressBar": true,
                    "timeOut": 5000
                });
            }
        });
    }
    // Function to view input credit details
    function viewCreditDetails(creditId) {
        window.location.href = "input-credit-details?id=" + creditId;
    }
    </script>

</body>



</html>