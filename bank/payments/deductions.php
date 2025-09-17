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
                <!-- Start::page-header -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                    <div>
                        <!-- if the user is an admin -->
                        <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 5): ?>
                        <?php
                        $app = new App;
                        $email = $_SESSION['email'];
                        $query = "SELECT * FROM users WHERE id=" . $_SESSION['user_id'];
                        $admin = $app->select_one($query);
                        ?>
                        <p class="fw-semibold fs-18 mb-0">Welcome <?php echo $admin->first_name ?>
                            <?php echo $admin->last_name ?></p>

                        <?php else: ?>
                        <?php
                        $app = new App;
                        $query = "SELECT * FROM users WHERE id=" . $_SESSION['user_id'];
                        $staff = $app->select_one($query);
                        ?>
                        <p class="fw-semibold fs-18 mb-0">Welcome <?php echo $staff->first_name ?>
                            <?php echo $staff->last_name ?></p>
                        <?php endif; ?>
                        <span class="fs-semibold text-muted pt-5">Produce Management Dashboard</span>
                    </div>
                </div>

                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                    <h1 class="page-title fw-semibold fs-18 mb-0">Pending Deductions Analysis</h1>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Banking</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Deductions Analysis</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <!-- Deductions Summary Stats Cards -->
                <div class="row mt-2">
                    <!-- Total Pending Deductions -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden" style="background:#6AA32D!important;">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded"
                                            style="background-color: white!important;">
                                            <i style="color:#6AA32D" class="fa-solid fa-calculator fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-white mb-0">Total Pending Deductions</p>
                                                <?php
                                                              // Calculate total pending deductions across all types
                                                       $query = "SELECT 
                                                                 COUNT(DISTINCT pd.id) as count,
                                                                 COALESCE(SUM(pd.total_value * 0.9), 0) as total_amount
                                                                 FROM produce_deliveries pd
                                                                 JOIN farm_products fp ON pd.farm_product_id = fp.id
                                                                 JOIN farms f ON fp.farm_id = f.id
                                                                 JOIN farmers fm ON f.farmer_id = fm.id
                                                                 WHERE pd.status = 'verified'
                                                                 AND pd.is_sold = 1
                                                                 AND (
                                                                   EXISTS (
                                                                     SELECT 1 FROM loan_applications la 
                                                                     JOIN approved_loans al ON la.id = al.loan_application_id
                                                                     WHERE la.farmer_id = fm.id AND al.status = 'active' AND al.remaining_balance > 0
                                                                   ) 
                                                                   OR 
                                                                   EXISTS (
                                                                     SELECT 1 FROM input_credit_applications ica 
                                                                     JOIN approved_input_credits aic ON ica.id = aic.credit_application_id
                                                                     WHERE ica.farmer_id = fm.id AND aic.status = 'active' AND aic.remaining_balance > 0
                                                                   )
                                                                 )
                                                                 AND NOT EXISTS (
                                                                    SELECT 1 FROM farmer_account_transactions fat
                                                                    WHERE fat.reference_id = pd.id
                                                                    AND fat.transaction_type = 'credit'
                                                                 )";
                                                       $result = $app->select_one($query);
                                                       $total_deductions_count = ($result) ? $result->count : 0;
                                                       $total_deductions_amount = ($result) ? number_format($result->total_amount, 2) : 0;
                                                   ?>
                                                <h4 style="color:wheat;" class="fw-semibold mt-1">
                                                    <?php echo $total_deductions_count ?> <small
                                                        style="color:#f0f0f0;">(KES
                                                        <?php echo $total_deductions_amount ?>)</small>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Farmers With Deductions -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-users fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Farmers With
                                                    Deductions</p>
                                                <?php
                                                      // Count unique farmers with pending deductions
                                                      $query = "SELECT COUNT(DISTINCT fm.id) as count 
                                                               FROM produce_deliveries pd
                                                               JOIN farm_products fp ON pd.farm_product_id = fp.id
                                                               JOIN farms f ON fp.farm_id = f.id
                                                               JOIN farmers fm ON f.farmer_id = fm.id
                                                               WHERE pd.status = 'verified'
                                                               AND pd.is_sold = 1
                                                               AND (
                                                                  EXISTS (
                                                                    SELECT 1 FROM loan_applications la 
                                                                    JOIN approved_loans al ON la.id = al.loan_application_id
                                                                    WHERE la.farmer_id = fm.id AND al.status = 'active' AND al.remaining_balance > 0
                                                                  ) 
                                                                  OR 
                                                                  EXISTS (
                                                                    SELECT 1 FROM input_credit_applications ica 
                                                                    JOIN approved_input_credits aic ON ica.id = aic.credit_application_id
                                                                    WHERE ica.farmer_id = fm.id AND aic.status = 'active' AND aic.remaining_balance > 0
                                                                  )
                                                                )
                                                               AND NOT EXISTS (
                                                                   SELECT 1 FROM farmer_account_transactions fat
                                                                   WHERE fat.reference_id = pd.id
                                                                   AND fat.transaction_type = 'credit'
                                                               )";
                                                      $result = $app->select_one($query);
                                                      $farmers_with_deductions = ($result) ? $result->count : 0;
                                                  ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php echo $farmers_with_deductions ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Processing Fee -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
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
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Processing Fee Revenue
                                                </p>
                                                <?php
                                                    // Calculate the processing fee (1% of total deduction amount)
                                                    $total_deduction_value = isset($result->total_amount) ? $result->total_amount : 0;
                                                    $processing_fee = $total_deduction_value * 0.01; // 1% fee
                                                    $formatted_fee = number_format($processing_fee, 2);
                                                ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    KES <?php echo $formatted_fee ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Oldest Pending Deduction -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-clock fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Oldest Pending
                                                    Deduction</p>
                                                <?php
                                                    // Find the oldest pending deduction
                                                    $query = "SELECT 
                                                               MIN(pd.delivery_date) as oldest_date,
                                                               DATEDIFF(CURRENT_DATE(), MIN(pd.delivery_date)) as days_pending
                                                              FROM produce_deliveries pd
                                                              JOIN farm_products fp ON pd.farm_product_id = fp.id
                                                              JOIN farms f ON fp.farm_id = f.id
                                                              JOIN farmers fm ON f.farmer_id = fm.id
                                                              WHERE pd.status = 'verified'
                                                              AND pd.is_sold = 1
                                                              AND (
                                                                EXISTS (
                                                                  SELECT 1 FROM loan_applications la 
                                                                  JOIN approved_loans al ON la.id = al.loan_application_id
                                                                  WHERE la.farmer_id = fm.id AND al.status = 'active' AND al.remaining_balance > 0
                                                                ) 
                                                                OR 
                                                                EXISTS (
                                                                  SELECT 1 FROM input_credit_applications ica 
                                                                  JOIN approved_input_credits aic ON ica.id = aic.credit_application_id
                                                                  WHERE ica.farmer_id = fm.id AND aic.status = 'active' AND aic.remaining_balance > 0
                                                                )
                                                              )
                                                              AND NOT EXISTS (
                                                                 SELECT 1 FROM farmer_account_transactions fat
                                                                 WHERE fat.reference_id = pd.id
                                                                 AND fat.transaction_type = 'credit'
                                                              )";
                                                    $result = $app->select_one($query);
                                                    $days_pending = ($result && $result->days_pending) ? $result->days_pending : 0;
                                                    $oldest_date = ($result && $result->oldest_date) ? date("M d, Y", strtotime($result->oldest_date)) : "N/A";
                                                ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php echo $days_pending ?> <small class="text-muted">days
                                                        (<?php echo $oldest_date ?>)</small>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Deductions Breakdown Analysis -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card custom-card shadow-sm">
                            <div class="card-header border-bottom bg-light">
                                <div class="card-title d-flex align-items-center">
                                    <i class="fas fa-chart-pie me-2 text-success"></i>
                                    <h5 class="mb-0">Deductions Analysis by Type</h5>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Bank Loans Analysis -->
                                    <div class="col-md-4">
                                        <div class="p-4 border-end border-bottom position-relative overflow-hidden">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="me-3">
                                                    <span
                                                        class="avatar avatar-md rounded-circle d-flex align-items-center justify-content-center"
                                                        style="background-color:#6AA32D">
                                                        <i class="fa-solid fa-building-columns text-white"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="fw-semibold mb-0" style="color:#6AA32D">Bank Loans</h6>
                                                    <p class="text-muted mb-0 fs-12">Pending bank loan deductions</p>
                                                </div>
                                                <div class="ms-auto">
                                                    <i class="fas fa-money-bill-wave text-success opacity-25 fa-2x"></i>
                                                </div>
                                            </div>
                                            <?php
                                               // Get bank loan deductions statistics
                                               $query = "SELECT 
                                                     COUNT(DISTINCT pd.id) as count,
                                                     COUNT(DISTINCT fm.id) as farmer_count,
                                                     COALESCE(SUM(
                                                         CASE 
                                                             WHEN al.remaining_balance > (pd.total_value * 0.9) 
                                                             THEN (pd.total_value * 0.9) 
                                                             ELSE al.remaining_balance 
                                                         END
                                                     ), 0) as amount
                                                   FROM produce_deliveries pd
                                                   JOIN farm_products fp ON pd.farm_product_id = fp.id
                                                   JOIN farms f ON fp.farm_id = f.id
                                                   JOIN farmers fm ON f.farmer_id = fm.id
                                                   JOIN loan_applications la ON fm.id = la.farmer_id
                                                   JOIN approved_loans al ON la.id = al.loan_application_id
                                                   WHERE pd.status = 'verified'
                                                   AND pd.is_sold = 1
                                                   AND la.provider_type = 'bank'
                                                   AND al.status = 'active'
                                                   AND al.remaining_balance > 0
                                                   AND NOT EXISTS (
                                                       SELECT 1 FROM farmer_account_transactions fat
                                                       WHERE fat.reference_id = pd.id
                                                       AND fat.transaction_type = 'credit'
                                                   )";
                                               $bank_stats = $app->select_one($query);
                                               $bank_count = ($bank_stats) ? $bank_stats->count : 0;
                                               $bank_farmers = ($bank_stats) ? $bank_stats->farmer_count : 0;
                                               $bank_amount = ($bank_stats) ? number_format($bank_stats->amount, 2) : 0;
                                           ?>
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <div class="text-center">
                                                    <h3 class="fw-semibold mb-0" style="color:#6AA32D">
                                                        <i class="fas fa-file-invoice-dollar me-1 fs-5"></i>
                                                        <?php echo $bank_count ?>
                                                    </h3>
                                                    <span class="text-muted fs-12">Deductions</span>
                                                </div>
                                                <div class="text-center">
                                                    <h3 class="fw-semibold mb-0" style="color:#6AA32D">
                                                        <i class="fas fa-users me-1 fs-5"></i>
                                                        <?php echo $bank_farmers ?>
                                                    </h3>
                                                    <span class="text-muted fs-12">Farmers</span>
                                                </div>
                                                <div class="text-center">
                                                    <h3 class="fw-semibold mb-0" style="color:#6AA32D">
                                                        <i class="fas fa-coins me-1 fs-5"></i>
                                                        KES <?php echo $bank_amount ?>
                                                    </h3>
                                                    <span class="text-muted fs-12">Total</span>
                                                </div>
                                            </div>
                                            <div class="progress mt-3" style="height: 6px;">
                                                <div class="progress-bar"
                                                    style="width: 100%; background-color: #6AA32D;" role="progressbar">
                                                </div>
                                            </div>
                                            <p class="fs-12 text-muted mt-2 d-flex align-items-center">
                                                <i class="fas fa-calculator me-1"></i>
                                                <?php 
                                    $total_amount = isset($bank_stats->amount) ? $bank_stats->amount : 0;
                                    $processing_fee = $total_amount * 0.01;
                                    echo "Processing fee revenue: KES " . number_format($processing_fee, 2);
                                ?>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- SACCO Loans Analysis -->
                                    <div class="col-md-4">
                                        <div class="p-4 border-end border-bottom position-relative overflow-hidden">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="me-3">
                                                    <span
                                                        class="avatar avatar-md rounded-circle d-flex align-items-center justify-content-center"
                                                        style="background-color:#6AA32D">
                                                        <i class="fa-solid fa-landmark text-white"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="fw-semibold mb-0" style="color:#6AA32D">SACCO Loans</h6>
                                                    <p class="text-muted mb-0 fs-12">Pending SACCO loan deductions</p>
                                                </div>
                                                <div class="ms-auto">
                                                    <i
                                                        class="fas fa-hand-holding-usd text-success opacity-25 fa-2x"></i>
                                                </div>
                                            </div>
                                            <?php
                                                // Get SACCO loan deductions statistics
                                                    $query = "SELECT 
                                                          COUNT(DISTINCT pd.id) as count,
                                                          COUNT(DISTINCT fm.id) as farmer_count,
                                                          COALESCE(SUM(
                                                              CASE 
                                                                  WHEN al.remaining_balance > (pd.total_value * 0.9) 
                                                                  THEN (pd.total_value * 0.9) 
                                                                  ELSE al.remaining_balance 
                                                              END
                                                          ), 0) as amount
                                                        FROM produce_deliveries pd
                                                        JOIN farm_products fp ON pd.farm_product_id = fp.id
                                                        JOIN farms f ON fp.farm_id = f.id
                                                        JOIN farmers fm ON f.farmer_id = fm.id
                                                        JOIN loan_applications la ON fm.id = la.farmer_id
                                                        JOIN approved_loans al ON la.id = al.loan_application_id
                                                        WHERE pd.status = 'verified'
                                                        AND pd.is_sold = 1
                                                        AND la.provider_type = 'sacco'
                                                        AND al.status = 'active'
                                                        AND al.remaining_balance > 0
                                                        AND NOT EXISTS (
                                                            SELECT 1 FROM farmer_account_transactions fat
                                                            WHERE fat.reference_id = pd.id
                                                            AND fat.transaction_type = 'credit'
                                                        )";
                                                    $sacco_stats = $app->select_one($query);
                                                    $sacco_count = ($sacco_stats) ? $sacco_stats->count : 0;
                                                    $sacco_farmers = ($sacco_stats) ? $sacco_stats->farmer_count : 0;
                                                    $sacco_amount = ($sacco_stats) ? number_format($sacco_stats->amount, 2) : 0;
                                                ?>
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <div class="text-center">
                                                    <h3 class="fw-semibold mb-0" style="color:#6AA32D">
                                                        <i class="fas fa-file-invoice-dollar me-1 fs-5"></i>
                                                        <?php echo $sacco_count ?>
                                                    </h3>
                                                    <span class="text-muted fs-12">Deductions</span>
                                                </div>
                                                <div class="text-center">
                                                    <h3 class="fw-semibold mb-0" style="color:#6AA32D">
                                                        <i class="fas fa-users me-1 fs-5"></i>
                                                        <?php echo $sacco_farmers ?>
                                                    </h3>
                                                    <span class="text-muted fs-12">Farmers</span>
                                                </div>
                                                <div class="text-center">
                                                    <h3 class="fw-semibold mb-0" style="color:#6AA32D">
                                                        <i class="fas fa-coins me-1 fs-5"></i>
                                                        KES <?php echo $sacco_amount ?>
                                                    </h3>
                                                    <span class="text-muted fs-12">Total</span>
                                                </div>
                                            </div>
                                            <div class="progress mt-3" style="height: 6px;">
                                                <div class="progress-bar"
                                                    style="width: 100%; background-color: #6AA32D;" role="progressbar">
                                                </div>
                                            </div>
                                            <p class="fs-12 text-muted mt-2 d-flex align-items-center">
                                                <i class="fas fa-calculator me-1"></i>
                                                <?php 
                                                     $total_amount = isset($sacco_stats->amount) ? $sacco_stats->amount : 0;
                                                     $processing_fee = $total_amount * 0.01;
                                                     echo "Processing fee revenue: KES " . number_format($processing_fee, 2);
                                                 ?>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Input Credits Analysis -->
                                    <div class="col-md-4">
                                        <div class="p-4 border-bottom position-relative overflow-hidden">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="me-3">
                                                    <span
                                                        class="avatar avatar-md rounded-circle d-flex align-items-center justify-content-center"
                                                        style="background-color:#6AA32D">
                                                        <i class="fa-solid fa-leaf text-white"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="fw-semibold mb-0" style="color:#6AA32D">Input Credits
                                                    </h6>
                                                    <p class="text-muted mb-0 fs-12">Pending input credit deductions</p>
                                                </div>
                                                <div class="ms-auto">
                                                    <i class="fas fa-seedling text-success opacity-25 fa-2x"></i>
                                                </div>
                                            </div>
                                            <?php
                                                     // Get input credit deductions statistics
                                                     $query = "SELECT 
                                                           COUNT(DISTINCT pd.id) as count,
                                                           COUNT(DISTINCT fm.id) as farmer_count,
                                                           COALESCE(SUM(
                                                               CASE 
                                                                   WHEN aic.remaining_balance > (pd.total_value * aic.repayment_percentage / 100) 
                                                                   THEN (pd.total_value * aic.repayment_percentage / 100) 
                                                                   ELSE aic.remaining_balance 
                                                               END
                                                           ), 0) as amount
                                                         FROM produce_deliveries pd
                                                         JOIN farm_products fp ON pd.farm_product_id = fp.id
                                                         JOIN farms f ON fp.farm_id = f.id
                                                         JOIN farmers fm ON f.farmer_id = fm.id
                                                         JOIN input_credit_applications ica ON fm.id = ica.farmer_id
                                                         JOIN approved_input_credits aic ON ica.id = aic.credit_application_id
                                                         WHERE pd.status = 'verified'
                                                         AND pd.is_sold = 1
                                                         AND aic.status = 'active'
                                                         AND aic.remaining_balance > 0
                                                         AND NOT EXISTS (
                                                             SELECT 1 FROM farmer_account_transactions fat
                                                             WHERE fat.reference_id = pd.id
                                                             AND fat.transaction_type = 'credit'
                                                         )";
                                                     $input_stats = $app->select_one($query);
                                                     $input_count = ($input_stats) ? $input_stats->count : 0;
                                                     $input_farmers = ($input_stats) ? $input_stats->farmer_count : 0;
                                                     $input_amount = ($input_stats) ? number_format($input_stats->amount, 2) : 0;
                                                 ?>
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <div class="text-center">
                                                    <h3 class="fw-semibold mb-0" style="color:#6AA32D">
                                                        <i class="fas fa-file-invoice-dollar me-1 fs-5"></i>
                                                        <?php echo $input_count ?>
                                                    </h3>
                                                    <span class="text-muted fs-12">Deductions</span>
                                                </div>
                                                <div class="text-center">
                                                    <h3 class="fw-semibold mb-0" style="color:#6AA32D">
                                                        <i class="fas fa-users me-1 fs-5"></i>
                                                        <?php echo $input_farmers ?>
                                                    </h3>
                                                    <span class="text-muted fs-12">Farmers</span>
                                                </div>
                                                <div class="text-center">
                                                    <h3 class="fw-semibold mb-0" style="color:#6AA32D">
                                                        <i class="fas fa-coins me-1 fs-5"></i>
                                                        KES <?php echo $input_amount ?>
                                                    </h3>
                                                    <span class="text-muted fs-12">Total</span>
                                                </div>
                                            </div>
                                            <div class="progress mt-3" style="height: 6px;">
                                                <div class="progress-bar"
                                                    style="width: 100%; background-color: #6AA32D;" role="progressbar">
                                                </div>
                                            </div>
                                            <p class="fs-12 text-muted mt-2 d-flex align-items-center">
                                                <i class="fas fa-calculator me-1"></i>
                                                <?php 
                                                   $total_amount = isset($input_stats->amount) ? $input_stats->amount : 0;
                                                   $processing_fee = $total_amount * 0.01;
                                                   echo "Processing fee revenue: KES " . number_format($processing_fee, 2);
                                               ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Summary Analysis Footer -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="p-4">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <span
                                                        class="avatar avatar-md rounded-circle d-flex align-items-center justify-content-center"
                                                        style="background-color:#6AA32D">
                                                        <i class="fa-solid fa-chart-line text-white"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="fw-semibold mb-0" style="color:#6AA32D">Total Deductions
                                                        Summary</h6>
                                                    <p class="text-muted mb-0 fs-12">Combined deductions across all
                                                        categories</p>
                                                </div>
                                                <div class="ms-auto">
                                                    <button class="btn btn-sm"
                                                        style="background-color: #6AA32D; color: white;"
                                                        onclick="displayDeductions()">
                                                        <i class="fas fa-sync-alt me-1"></i> Refresh Data
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="row mt-4">
                                                <div class="col-md-8">
                                                    <div class="mb-3">
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <span class="fs-12 d-flex align-items-center">
                                                                <i
                                                                    class="fas fa-building-columns me-1 text-success"></i>
                                                                Bank Loans
                                                            </span>
                                                            <span class="fs-12 badge bg-light text-success"><?php 
                                                                     $bank_amount_raw = isset($bank_stats->amount) ? $bank_stats->amount : 0;
                                                                     $total_raw = $bank_amount_raw + 
                                                                                (isset($sacco_stats->amount) ? $sacco_stats->amount : 0) + 
                                                                                (isset($input_stats->amount) ? $input_stats->amount : 0);
                                                                     $bank_percentage = ($total_raw > 0) ? round(($bank_amount_raw / $total_raw) * 100) : 0;
                                                                     echo $bank_percentage . '%';
                                                                 ?></span>
                                                        </div>
                                                        <div class="progress mb-3" style="height: 8px;">
                                                            <div class="progress-bar bg-success"
                                                                style="width: <?php echo $bank_percentage; ?>%;"
                                                                role="progressbar"></div>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <span class="fs-12 d-flex align-items-center">
                                                                <i class="fas fa-landmark me-1 text-success"></i> SACCO
                                                                Loans
                                                            </span>
                                                            <span class="fs-12 badge bg-light text-success"><?php 
                                                              $sacco_amount_raw = isset($sacco_stats->amount) ? $sacco_stats->amount : 0;
                                                              $sacco_percentage = ($total_raw > 0) ? round(($sacco_amount_raw / $total_raw) * 100) : 0;
                                                              echo $sacco_percentage . '%';
                                                          ?></span>
                                                        </div>
                                                        <div class="progress mb-3" style="height: 8px;">
                                                            <div class="progress-bar bg-success"
                                                                style="width: <?php echo $sacco_percentage; ?>%;"
                                                                role="progressbar"></div>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <span class="fs-12 d-flex align-items-center">
                                                                <i class="fas fa-leaf me-1 text-success"></i> Input
                                                                Credits
                                                            </span>
                                                            <span class="fs-12 badge bg-light text-success"><?php 
                                                $input_amount_raw = isset($input_stats->amount) ? $input_stats->amount : 0;
                                                $input_percentage = ($total_raw > 0) ? round(($input_amount_raw / $total_raw) * 100) : 0;
                                                echo $input_percentage . '%';
                                            ?></span>
                                                        </div>
                                                        <div class="progress mb-3" style="height: 8px;">
                                                            <div class="progress-bar bg-success"
                                                                style="width: <?php echo $input_percentage; ?>%;"
                                                                role="progressbar"></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="text-center p-3 border rounded shadow-sm"
                                                        style="background-color: #f8f9fa;">
                                                        <div class="mb-2">
                                                            <i
                                                                class="fas fa-hand-holding-dollar fa-2x text-success"></i>
                                                        </div>
                                                        <h2 class="mb-1 fw-semibold" style="color:#6AA32D">KES <?php 
                                            $total_processing_fee = ($total_raw * 0.01);
                                            echo number_format($total_processing_fee, 2); 
                                        ?></h2>
                                                        <p class="mb-0 text-muted">Total Processing Fee Revenue</p>
                                                        <hr class="my-2">
                                                        <div class="d-flex justify-content-between mt-2">
                                                            <div>
                                                                <span class="badge"
                                                                    style="background-color: rgba(106, 163, 45, 0.2); color: #6AA32D;">
                                                                    <i class="fas fa-building-columns me-1"></i> KES
                                                                    <?php echo number_format($bank_amount_raw * 0.01, 2); ?>
                                                                </span>
                                                            </div>
                                                            <div>
                                                                <span class="badge"
                                                                    style="background-color: rgba(106, 163, 45, 0.2); color: #6AA32D;">
                                                                    <i class="fas fa-landmark me-1"></i> KES
                                                                    <?php echo number_format($sacco_amount_raw * 0.01, 2); ?>
                                                                </span>
                                                            </div>
                                                            <div>
                                                                <span class="badge"
                                                                    style="background-color: rgba(106, 163, 45, 0.2); color: #6AA32D;">
                                                                    <i class="fas fa-leaf me-1"></i> KES
                                                                    <?php echo number_format($input_amount_raw * 0.01, 2); ?>
                                                                </span>
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
                    </div>
                </div>

                <!-- Pending Deductions Section -->
                <div class="row mt-4">
                    <div class="col-xl-12">
                        <div id="pendingDeductionsSection"></div>
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
    <!-- JavaScript for handling deductions display -->
    <script>
    $(document).ready(() => {
        displayDeductions();
    });

    // Function to display deductions
    function displayDeductions() {
        // Show enhanced loader
        $('#pendingDeductionsSection').html(`
        <div class="card custom-card shadow-sm">
            <div class="card-body">
                <div class="text-center py-5">
                    <div class="spinner-grow" style="width: 3rem; height: 3rem; color: #6AA32D;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="mt-3">
                        <h5 style="color: #6AA32D;"><i class="fas fa-sync-alt fa-spin me-2"></i>Loading deductions data...</h5>
                        <p class="text-muted mb-0">Please wait while we fetch the latest information</p>
                        <div class="progress mt-3 mx-auto" style="height: 4px; max-width: 200px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" 
                                 style="width: 100%; background-color: #6AA32D;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        `);

        // Fetch deductions data
        $.ajax({
            url: "http://localhost/dfcs/ajax/payment-controller/display-deductions.php",
            type: 'POST',
            data: {
                displayDeductions: "true",
            },
            success: function(data, status) {
                $('#pendingDeductionsSection').html(data);
            },
            error: function() {
                $('#pendingDeductionsSection').html(`
                <div class="card custom-card shadow-sm">
                    <div class="card-body">
                        <div class="text-center py-4">
                            <i class="fa-solid fa-triangle-exclamation fa-3x mb-3" style="color: #dc3545;"></i>
                            <h5>Error Loading Data</h5>
                            <p class="text-muted">There was a problem loading the deductions data. Please try again.</p>
                            <button class="btn" style="background-color: #6AA32D; color: white;" onclick="displayDeductions()">
                                <i class="fa-solid fa-sync-alt me-1"></i> Retry
                            </button>
                        </div>
                    </div>
                </div>
            `);
            }
        });
    }
    </script>


</body>



</html>