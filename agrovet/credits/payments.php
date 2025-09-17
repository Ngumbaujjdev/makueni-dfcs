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
                <!-- Start::page-header -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                    <div>
                        <?php
                         // Get session user_id
                         if (session_status() === PHP_SESSION_NONE) {
                             session_start();
                         }
                                 
                         $userId = $_SESSION['user_id'] ?? null;
                         if (!$userId) {
                             header("Location: http://localhost/dfcs/");
                             exit();
                         }
                                 
                         $app = new App();
                                 
                         // Get Agrovet staff profile info from user_id
                         $query = "SELECT s.id as staff_id, s.position, s.employee_number, s.agrovet_id, s.is_active,
                                     u.first_name, u.last_name, u.phone, u.email, u.location, u.profile_picture,
                                     a.name as agrovet_name, a.type_id, at.name as agrovet_type
                                     FROM agrovet_staff s
                                     JOIN users u ON s.user_id = u.id
                                     JOIN agrovets a ON s.agrovet_id = a.id
                                     JOIN agrovet_types at ON a.type_id = at.id
                                     WHERE s.user_id = $userId";
                         $staff = $app->select_one($query);
                                 
                         if (!$staff) {
                             header("Location: http://localhost/dfcs/");
                             exit();
                         }
                         ?>
                        <p class="fw-semibold fs-18 mb-0">Welcome <?php echo $staff->first_name ?>
                            <?php echo $staff->last_name ?></p>
                        <span class="fs-semibold text-muted pt-5">Input Credit Management Dashboard</span>
                    </div>
                </div>

                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                    <h1 class="page-title fw-semibold fs-18 mb-0">Input Credit Repayments</h1>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Credits</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Input Credit Repayments</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <!-- Repayment Stats Cards -->
                <div class="row mt-2">
                    <!-- Total Repayments -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden" style="background:#6AA32D!important;">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded"
                                            style="background-color: white!important;">
                                            <i style="color:#6AA32D" class="fa-solid fa-money-bill-transfer fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-white mb-0">Total Repayments</p>
                                                <?php
                                                   $query = "SELECT COUNT(*) as count FROM input_credit_repayments icr
                                                              JOIN approved_input_credits aic ON icr.approved_credit_id = aic.id
                                                              JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                                              WHERE ica.agrovet_id = {$staff->agrovet_id}";
                                                   $result = $app->select_one($query);
                                                   $total_repayments = ($result) ? $result->count : 0;
                                                    ?>
                                                <h4 style="color:wheat;" class="fw-semibold mt-1">
                                                    <?php echo $total_repayments ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Repayment Amount -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-coins fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Total Amount Repaid
                                                </p>
                                                <?php
                                                 $query = "SELECT COALESCE(SUM(icr.amount), 0) as total_amount 
                                                           FROM input_credit_repayments icr
                                                           JOIN approved_input_credits aic ON icr.approved_credit_id = aic.id
                                                           JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                                           WHERE ica.agrovet_id = {$staff->agrovet_id}";
                                                 $result = $app->select_one($query);
                                                 $total_amount = ($result) ? number_format($result->total_amount, 2) : 0;
                                                 ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    KES <?php echo $total_amount ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Current Month Repayments -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-calendar-check fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">This Month</p>
                                                <?php
                                                   $query = "SELECT COUNT(*) as count, COALESCE(SUM(icr.amount), 0) as amount 
                                                             FROM input_credit_repayments icr
                                                             JOIN approved_input_credits aic ON icr.approved_credit_id = aic.id
                                                             JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                                             WHERE ica.agrovet_id = {$staff->agrovet_id}
                                                             AND MONTH(icr.deduction_date) = MONTH(CURRENT_DATE()) 
                                                             AND YEAR(icr.deduction_date) = YEAR(CURRENT_DATE())";
                                                   $result = $app->select_one($query);
                                                   $monthly_count = ($result) ? $result->count : 0;
                                                   $monthly_amount = ($result) ? number_format($result->amount, 2) : 0;
                                                     ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php echo $monthly_count ?> <small class="text-muted">(KES
                                                        <?php echo $monthly_amount ?>)</small>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Active Credits -->
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
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Active Credits</p>
                                                <?php
                                                  $query = "SELECT COUNT(*) as count, COALESCE(SUM(remaining_balance), 0) as balance 
                                                           FROM approved_input_credits aic
                                                           JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                                           WHERE ica.agrovet_id = {$staff->agrovet_id} AND aic.status = 'active'";
                                                  $result = $app->select_one($query);
                                                  $active_credits = ($result) ? $result->count : 0;
                                                  $remaining_balance = ($result) ? number_format($result->balance, 2) : 0;
                                                 ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php echo $active_credits ?> <small class="text-muted">(KES
                                                        <?php echo $remaining_balance ?>)</small>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Repayment History Section -->
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <div class="card-header justify-content-between">
                            <div class="card-title">
                                <i class="ri-history-line me-2"></i> Recent Input Credit Repayments
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-outline-primary btn-sm" id="btnShowAllRepayments">All</button>
                                <button class="btn btn-outline-success btn-sm" id="btnShowThisMonth">This Month</button>
                                <button class="btn btn-outline-info btn-sm" id="btnShowLastMonth">Last Month</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- This div is needed for loading the repayments table -->
                            <div id="repaymentsSection">
                                <!-- Loading spinner initially shown -->
                                <div class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2">Loading repayments...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Repayment Details Card - Initially Hidden -->
                <div class="col-xl-12 mt-4" id="repaymentDetailsCard" style="display: none;">
                    <div class="card custom-card">
                        <div class="card-header d-flex justify-content-between align-items-center"
                            style="background-color: #6AA32D; color: white;">
                            <div class="card-title">
                                <i class="ri-information-line me-2"></i> Input Credit Repayment Details
                            </div>
                            <button class="btn btn-sm btn-light" id="closeRepaymentDetails">
                                <i class="ri-close-line me-1"></i> Close
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <!-- Loading spinner -->
                            <div id="repaymentDetailsLoader" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Loading repayment details...</p>
                            </div>
                            <!-- Details will be loaded here -->
                            <div id="repaymentDetailsContent" style="display: none;" class="p-4">
                                <!-- Title and ID badge -->
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="text-primary">
                                        <i class="ri-exchange-dollar-line me-2"></i>Input Credit Repayment Details
                                    </h5>
                                    <span
                                        class="badge bg-primary-transparent text-primary fs-14 px-3 py-2 rounded-pill">
                                        <i class="ri-price-tag-3-line me-1"></i> <span
                                            id="details-repayment-id">-</span>
                                    </span>
                                </div>
                                <div class="row">
                                    <!-- Main information -->
                                    <div class="col-lg-6">
                                        <div class="card custom-card border shadow-sm h-100">
                                            <div class="card-header bg-light-subtle d-flex align-items-center">
                                                <i class="ri-money-dollar-circle-line fs-18 text-primary me-2"></i>
                                                <div class="card-title mb-0">Repayment Information</div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row gy-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label text-muted mb-1">Credit
                                                            Reference</label>
                                                        <p class="fs-15 fw-semibold text-primary"
                                                            id="details-credit-reference">-</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label text-muted mb-1">Amount Paid</label>
                                                        <p class="fs-15 fw-semibold text-success" id="details-amount">-
                                                        </p>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label class="form-label text-muted mb-1">Farmer</label>
                                                        <p class="fs-15 fw-semibold" id="details-farmer-name">-</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label text-muted mb-1">
                                                            <i class="ri-calendar-line text-info me-1"></i> Payment Date
                                                        </label>
                                                        <p class="fs-15 fw-semibold" id="details-payment-date">-</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label text-muted mb-1">
                                                            <i class="ri-bank-line text-info me-1"></i> Payment Method
                                                        </label>
                                                        <p class="fs-15 fw-semibold" id="details-payment-method">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Processing information -->
                                    <div class="col-lg-6">
                                        <div class="card custom-card border shadow-sm h-100">
                                            <div class="card-header bg-light-subtle d-flex align-items-center">
                                                <i class="ri-user-settings-line fs-18 text-success me-2"></i>
                                                <div class="card-title mb-0">Processing Details</div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row gy-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label text-muted mb-1">
                                                            <i class="ri-user-line text-info me-1"></i> Processed By
                                                        </label>
                                                        <p class="fs-15 fw-semibold" id="details-processed-by">-</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label text-muted mb-1">
                                                            <i class="ri-time-line text-info me-1"></i> Processed Date
                                                        </label>
                                                        <p class="fs-15 fw-semibold" id="details-processed-date">-</p>
                                                    </div>
                                                    <div class="col-12 mt-2">
                                                        <label class="form-label text-muted mb-1">
                                                            <i class="ri-file-text-line text-info me-1"></i> Notes
                                                        </label>
                                                        <div class="p-3 bg-light-subtle rounded"
                                                            id="details-notes-container">
                                                            <p class="fs-15 mb-0" id="details-notes">-</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Produce information (conditional) -->
                                <div class="row mt-4" id="produceInfoSection">
                                    <div class="col-12">
                                        <div class="card custom-card border shadow-sm">
                                            <div class="card-header bg-light-subtle d-flex align-items-center">
                                                <i class="ri-plant-line fs-18 text-warning me-2"></i>
                                                <div class="card-title mb-0">Produce Information</div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row gy-3">
                                                    <div class="col-md-3">
                                                        <div class="d-flex align-items-center">
                                                            <div
                                                                class="avatar avatar-sm bg-success-subtle text-success rounded-circle me-2">
                                                                <i class="ri-leaf-line"></i>
                                                            </div>
                                                            <div>
                                                                <label class="form-label text-muted mb-0 small">Produce
                                                                    Type</label>
                                                                <p class="fs-15 fw-semibold mb-0"
                                                                    id="details-produce-type">-</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="d-flex align-items-center">
                                                            <div
                                                                class="avatar avatar-sm bg-info-subtle text-info rounded-circle me-2">
                                                                <i class="ri-scales-3-line"></i>
                                                            </div>
                                                            <div>
                                                                <label
                                                                    class="form-label text-muted mb-0 small">Quantity</label>
                                                                <p class="fs-15 fw-semibold mb-0"
                                                                    id="details-produce-quantity">-</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="d-flex align-items-center">
                                                            <div
                                                                class="avatar avatar-sm bg-warning-subtle text-warning rounded-circle me-2">
                                                                <i class="ri-medal-line"></i>
                                                            </div>
                                                            <div>
                                                                <label class="form-label text-muted mb-0 small">Quality
                                                                    Grade</label>
                                                                <p class="fs-15 fw-semibold mb-0"
                                                                    id="details-produce-quality">-</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="d-flex align-items-center">
                                                            <div
                                                                class="avatar avatar-sm bg-success-subtle text-success rounded-circle me-2">
                                                                <i class="ri-money-dollar-circle-line"></i>
                                                            </div>
                                                            <div>
                                                                <label class="form-label text-muted mb-0 small">Produce
                                                                    Value</label>
                                                                <p class="fs-15 fw-semibold mb-0"
                                                                    id="details-produce-value">-</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Credit information -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="card custom-card border shadow-sm">
                                            <div class="card-header bg-light-subtle d-flex align-items-center">
                                                <i class="ri-shopping-cart-line fs-18 text-primary me-2"></i>
                                                <div class="card-title mb-0">Input Credit Information</div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-lg-8">
                                                        <div class="row gy-3">
                                                            <div class="col-md-4">
                                                                <div class="d-flex align-items-center">
                                                                    <div
                                                                        class="avatar avatar-sm bg-primary-subtle text-primary rounded-circle me-2">
                                                                        <i class="ri-coins-line"></i>
                                                                    </div>
                                                                    <div>
                                                                        <label
                                                                            class="form-label text-muted mb-0 small">Original
                                                                            Credit Amount</label>
                                                                        <p class="fs-15 fw-semibold mb-0"
                                                                            id="details-credit-amount">-</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="d-flex align-items-center">
                                                                    <div
                                                                        class="avatar avatar-sm bg-warning-subtle text-warning rounded-circle me-2">
                                                                        <i class="ri-percent-line"></i>
                                                                    </div>
                                                                    <div>
                                                                        <label
                                                                            class="form-label text-muted mb-0 small">Interest
                                                                            Rate</label>
                                                                        <p class="fs-15 fw-semibold mb-0"
                                                                            id="details-interest-rate">-</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="d-flex align-items-center">
                                                                    <div
                                                                        class="avatar avatar-sm bg-info-subtle text-info rounded-circle me-2">
                                                                        <i class="ri-store-2-line"></i>
                                                                    </div>
                                                                    <div>
                                                                        <label
                                                                            class="form-label text-muted mb-0 small">Agrovet</label>
                                                                        <p class="fs-15 fw-semibold mb-0"
                                                                            id="details-agrovet">-</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="d-flex align-items-center">
                                                                    <div
                                                                        class="avatar avatar-sm bg-danger-subtle text-danger rounded-circle me-2">
                                                                        <i class="ri-arrow-left-circle-line"></i>
                                                                    </div>
                                                                    <div>
                                                                        <label
                                                                            class="form-label text-muted mb-0 small">Balance
                                                                            Before Payment</label>
                                                                        <p class="fs-15 fw-semibold mb-0"
                                                                            id="details-balance-before">-</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="d-flex align-items-center">
                                                                    <div
                                                                        class="avatar avatar-sm bg-success-subtle text-success rounded-circle me-2">
                                                                        <i class="ri-arrow-right-circle-line"></i>
                                                                    </div>
                                                                    <div>
                                                                        <label
                                                                            class="form-label text-muted mb-0 small">Balance
                                                                            After Payment</label>
                                                                        <p class="fs-15 fw-semibold mb-0"
                                                                            id="details-balance-after">-</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="d-flex align-items-center">
                                                                    <div
                                                                        class="avatar avatar-sm bg-primary-subtle text-primary rounded-circle me-2">
                                                                        <i class="ri-calendar-check-line"></i>
                                                                    </div>
                                                                    <div>
                                                                        <label
                                                                            class="form-label text-muted mb-0 small">Fulfillment
                                                                            Date</label>
                                                                        <p class="fs-15 fw-semibold mb-0"
                                                                            id="details-fulfillment-date">-</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div
                                                            class="d-flex flex-column align-items-center justify-content-center h-100 p-3 bg-light-subtle rounded">
                                                            <label class="form-label text-muted mb-2">Credit
                                                                Status</label>
                                                            <div id="details-credit-status" class="mb-2">-</div>
                                                            <div class="progress w-100 mt-2" style="height: 10px;">
                                                                <div class="progress-bar bg-success"
                                                                    id="repayment-progress" role="progressbar"
                                                                    style="width: 0%"></div>
                                                            </div>
                                                            <small class="text-muted mt-2">
                                                                <span id="repayment-percentage">0%</span> repaid
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Action buttons -->
                                <div class="row mt-4">
                                    <div class="col-12 d-flex justify-content-end">
                                        <button type="button" class="btn btn-secondary me-2" id="closeDetailsBtn">
                                            <i class="ri-close-line me-1"></i> Close
                                        </button>
                                        <button type="button" class="btn btn-success" id="printReceiptBtn">
                                            <i class="ri-printer-line me-1"></i> Print Receipt
                                        </button>
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
    // Main script for input credit repayments page
    $(document).ready(() => {
        // Load all repayments
        displayAllCreditRepayments();

        // Close repayment details card handlers
        $('#closeRepaymentDetails, #closeDetailsBtn').on('click', function() {
            $('#repaymentDetailsCard').slideUp(300);
        });
    });
    // Function to display all input credit repayments
    function displayAllCreditRepayments() {
        let displayAllRepayments = "true";
        $.ajax({
            url: "http://localhost/dfcs/ajax/input-credit-controller/display-all-repayments.php",
            type: 'POST',
            data: {
                displayAllRepayments: displayAllRepayments,
            },
            success: function(data, status) {
                $('#repaymentsSection').html(data);
                console.log("Repayments loaded successfully");
            },
            error: function(xhr, status, error) {
                console.error("Error loading input credit repayments:", error);
                toastr.error('Failed to load input credit repayments', 'Error', {
                    "positionClass": "toast-top-right",
                    "progressBar": true,
                    "timeOut": 5000
                });
                $('#repaymentsSection').html(
                    '<div class="alert alert-danger"><i class="ri-error-warning-line me-1"></i> Failed to load repayments. Please try again.</div>'
                );
            }
        });
    }

    function viewCreditRepaymentDetails(repaymentId) {
        console.log("Loading details for repayment ID:", repaymentId);
        // Show the details card if hidden
        if (!$('#repaymentDetailsCard').is(':visible')) {
            $('#repaymentDetailsCard').slideDown(300);
        }
        // Scroll to the details card
        $('html, body').animate({
            scrollTop: $("#repaymentDetailsCard").offset().top - 100
        }, 500);
        // Show loader, hide content
        $('#repaymentDetailsLoader').show();
        $('#repaymentDetailsContent').hide();
        // Show loading message with toastr
        toastr.info('Loading repayment details...', 'Please wait', {
            "positionClass": "toast-top-right",
            "progressBar": true,
            "timeOut": 2000
        });
        $.ajax({
            url: "http://localhost/dfcs/ajax//input-credit-controller/get-credit-repayment-details.php",
            type: "POST",
            data: {
                repaymentId: repaymentId
            },
            success: function(response) {
                try {
                    console.log("Response received:", response);
                    let data = JSON.parse(response);

                    // Fill in the repayment details
                    $('#details-repayment-id').text('REP' + String(data.id).padStart(5, '0'));
                    $('#details-credit-reference').text('ICRED' + String(data.credit_application_id)
                        .padStart(5, '0'));
                    $('#details-farmer-name').text(data.farmer_name + ' (' + data.farmer_reg + ')');
                    $('#details-amount').text('KES ' + numberWithCommas(data.amount));
                    $('#details-payment-date').text(formatDate(data.payment_date));
                    $('#details-payment-method').text(formatPaymentMethod(data.payment_method));
                    $('#details-processed-by').text(data.processed_by_name || 'System');
                    $('#details-processed-date').text(formatDateTime(data.created_at));

                    // Handle notes with better formatting
                    if (data.notes) {
                        $('#details-notes').text(data.notes);
                        $('#details-notes-container').show();
                    } else {
                        $('#details-notes').text('No notes provided');
                        $('#details-notes-container').addClass('text-muted fst-italic');
                    }

                    // Credit information
                    $('#details-credit-amount').text('KES ' + numberWithCommas(data.approved_amount));
                    $('#details-interest-rate').text(data.credit_percentage + '%');
                    $('#details-balance-before').text('KES ' + numberWithCommas(data.balance_before));
                    $('#details-balance-after').text('KES ' + numberWithCommas(data.remaining_balance));
                    $('#details-agrovet').text(data.agrovet_name || '-');
                    $('#details-fulfillment-date').text(formatDate(data.fulfillment_date));

                    // Calculate repayment progress
                    const totalAmount = parseFloat(data.total_with_interest) || parseFloat(data
                        .approved_amount);
                    const remainingBalance = parseFloat(data.remaining_balance) || 0;
                    const repaidAmount = totalAmount - remainingBalance;
                    const repaymentPercentage = totalAmount > 0 ? Math.round((repaidAmount / totalAmount) *
                        100) : 0;

                    $('#repayment-progress').css('width', repaymentPercentage + '%');
                    $('#repayment-percentage').text(repaymentPercentage + '%');
                    // Status badge with better styling
                    let statusClass = '';
                    let statusIcon = '';

                    switch (data.credit_status) {
                        case 'pending':
                        case 'under_review':
                            statusClass = 'bg-warning';
                            statusIcon = 'ri-time-line';
                            break;
                        case 'approved':
                        case 'fulfilled':
                        case 'active':
                            statusClass = 'bg-success';
                            statusIcon = 'ri-check-double-line';
                            break;
                        case 'completed':
                            statusClass = 'bg-info';
                            statusIcon = 'ri-checkbox-circle-line';
                            break;
                        case 'rejected':
                            statusClass = 'bg-danger';
                            statusIcon = 'ri-close-circle-line';
                            break;
                        default:
                            statusClass = 'bg-secondary';
                            statusIcon = 'ri-question-mark';
                    }

                    const statusText = data.credit_status ? data.credit_status.charAt(0).toUpperCase() +
                        data.credit_status.slice(1).replace(/_/g, ' ') : 'Unknown';
                    const statusBadge = `<span class="badge ${statusClass} fs-6 px-3 py-2">
                                      <i class="${statusIcon} me-1"></i> ${statusText}
                                    </span>`;
                    $('#details-credit-status').html(statusBadge);

                    // Handle produce section visibility
                    if (data.payment_method === 'produce_deduction' && data.produce_delivery_id) {
                        $('#produceInfoSection').show();
                        $('#details-produce-type').text(data.produce_type || '-');
                        $('#details-produce-quantity').text(numberWithCommas(data.produce_quantity) +
                            ' KGs');
                        $('#details-produce-quality').text('Grade ' + data.produce_quality);
                        $('#details-produce-value').text('KES ' + numberWithCommas(data
                            .produce_total_value));
                    } else {
                        $('#produceInfoSection').hide();
                    }
                    // Set up print receipt button
                    $('#printReceiptBtn').off('click').on('click', function() {
                        printCreditRepaymentReceipt(data.id);
                    });

                    // Hide loader, show content
                    $('#repaymentDetailsLoader').hide();
                    $('#repaymentDetailsContent').show();

                } catch (e) {
                    console.error("Error parsing JSON response:", e);
                    $('#repaymentDetailsContent').html(
                        '<div class="alert alert-danger"><i class="ri-error-warning-line me-1"></i> Error loading repayment details: ' +
                        e.message + '</div>');
                    $('#repaymentDetailsLoader').hide();
                    $('#repaymentDetailsContent').show();
                }
            },
            error: function(xhr, status, error) {
                toastr.error('Failed to load repayment details. Please try again.', 'Error', {
                    "positionClass": "toast-top-right",
                    "progressBar": true,
                    "timeOut": 5000
                });
                console.error('Error loading repayment details:', error);
                $('#repaymentDetailsLoader').hide();
                $('#repaymentDetailsContent').html(
                    '<div class="alert alert-danger"><i class="ri-error-warning-line me-1"></i> Failed to load repayment details</div>'
                ).show();
            }
        });
    }
    // Function to print credit repayment receipt
    function printCreditRepaymentReceipt(repaymentId) {
        // Show loading message with toastr
        toastr.info('Preparing your receipt for download...', 'Please wait', {
            "positionClass": "toast-top-right",
            "progressBar": true,
            "timeOut": 0,
            "extendedTimeOut": 0,
            "closeButton": false,
            "hideMethod": "fadeOut"
        });

        // AJAX call to generate PDF
        $.ajax({
            url: "http://localhost/dfcs/ajax/input-credit-controller/generate-credit-receipt-pdf.php",
            type: "POST",
            data: {
                repaymentId: repaymentId
            },
            xhrFields: {
                responseType: 'blob'
            },
            success: function(response, status, xhr) {
                toastr.clear(); // Clear the loading message

                try {
                    // Create a blob from the PDF data
                    const blob = new Blob([response], {
                        type: 'application/pdf'
                    });

                    // Get filename from Content-Disposition header if available
                    let filename = 'Credit_Repayment_Receipt_REP' + String(repaymentId).padStart(5, '0') +
                        '.pdf';
                    const contentDisposition = xhr.getResponseHeader('Content-Disposition');
                    if (contentDisposition) {
                        const filenameMatch = contentDisposition.match(
                            /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                        if (filenameMatch && filenameMatch[1]) {
                            filename = filenameMatch[1].replace(/['"]/g, '');
                        }
                    }

                    // Create a download link and trigger it
                    const url = window.URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = filename;
                    document.body.appendChild(link);
                    link.click();

                    // Clean up
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(link);

                    toastr.success('Receipt downloaded successfully', 'Success', {
                        "positionClass": "toast-top-right",
                        "progressBar": true,
                        "timeOut": 3000,
                        "extendedTimeOut": 1000,
                        "hideMethod": "fadeOut"
                    });
                } catch (e) {
                    // If response isn't a PDF, it might be a JSON error message
                    try {
                        const reader = new FileReader();
                        reader.onload = function() {
                            const errorJson = JSON.parse(reader.result);
                            toastr.error(errorJson.error || 'Failed to generate receipt',
                                'Error', {
                                    "positionClass": "toast-top-right",
                                    "progressBar": true,
                                    "timeOut": 5000
                                });
                        };
                        reader.readAsText(response);
                    } catch (readError) {
                        toastr.error('Failed to process server response', 'Error', {
                            "positionClass": "toast-top-right",
                            "progressBar": true,
                            "timeOut": 5000
                        });
                    }
                }
            },
            error: function(xhr, status, error) {
                toastr.clear();
                toastr.error('Failed to generate receipt. Please try again.', 'Error', {
                    "positionClass": "toast-top-right",
                    "progressBar": true,
                    "timeOut": 5000
                });
                console.error('Error generating PDF:', error);
            }
        });
    }

    // Helper functions
    function numberWithCommas(x) {
        if (!x) return '0.00';
        return parseFloat(x).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    function formatDateTime(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function formatPaymentMethod(method) {
        if (!method) return '-';
        return method.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function getStatusBadge(status) {
        let badgeClass = '';
        let statusText = status ? status.charAt(0).toUpperCase() + status.slice(1).replace(/_/g, ' ') : 'Unknown';

        switch (status) {
            case 'pending':
            case 'under_review':
                badgeClass = 'bg-warning';
                break;
            case 'approved':
            case 'fulfilled':
            case 'active':
                badgeClass = 'bg-success';
                break;
            case 'completed':
                badgeClass = 'bg-info';
                break;
            case 'rejected':
                badgeClass = 'bg-danger';
                break;
            default:
                badgeClass = 'bg-secondary';
        }

        return `<span class="badge ${badgeClass}">${statusText}</span>`;
    }
    </script>
</body>

</html>