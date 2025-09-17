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
                <!-- Agrovet Input Credits Dashboard -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                    <h1 class="page-title fw-semibold fs-18 mb-0">
                        <i class="fas fa-store me-2" style="color:#6AA32D;"></i>Agrovet Input Credits
                    </h1>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#"><i class="fas fa-home me-1"></i>Dashboard</a>
                                </li>
                                <li class="breadcrumb-item"><a href="#"><i class="fas fa-seedling me-1"></i>Inputs</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Active Credits</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <!-- Agrovet Input Credit Deductions Dashboard -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                    <h1 class="page-title fw-semibold fs-18 mb-0">
                        <i class="fas fa-money-bill-wave me-2" style="color:#6AA32D;"></i>Agrovet Input Credit
                        Deductions
                    </h1>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#"><i class="fas fa-home me-1"></i>Dashboard</a>
                                </li>
                                <li class="breadcrumb-item"><a href="#"><i class="fas fa-store me-1"></i>Agrovets</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Credit Deductions</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <!-- Deduction Stats Cards -->
                <div class="row mt-2">
                    <!-- Total Deductions -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden shadow-sm" style="background:#6AA32D!important;">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span
                                            class="avatar avatar-md avatar-rounded d-flex align-items-center justify-content-center"
                                            style="background-color: white!important;">
                                            <i style="color:#6AA32D" class="fa-solid fa-money-bill-transfer fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-white mb-0">Total Deductions</p>
                                                <?php
                                // Count total deductions for input credits
                                $query = "SELECT COALESCE(SUM(deducted_amount), 0) as total_deductions 
                                         FROM input_credit_repayments";
                                $result = $app->select_one($query);
                                $total_deductions = ($result) ? number_format($result->total_deductions, 2) : '0.00';
                                ?>
                                                <h4 style="color:wheat;" class="fw-semibold mt-1">
                                                    <i class="fas fa-coins me-1"></i>KES <?php echo $total_deductions ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Deductions This Month -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span
                                            class="avatar avatar-md avatar-rounded d-flex align-items-center justify-content-center"
                                            style="background:#6AA32D;">
                                            <i class="fa-solid fa-calendar-check fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">This Month</p>
                                                <?php
                                // Calculate deductions for current month
                                $query = "SELECT COALESCE(SUM(deducted_amount), 0) as month_deductions
                                         FROM input_credit_repayments
                                         WHERE MONTH(deduction_date) = MONTH(CURRENT_DATE()) 
                                         AND YEAR(deduction_date) = YEAR(CURRENT_DATE())";
                                $result = $app->select_one($query);
                                $month_deductions = ($result) ? number_format($result->month_deductions, 2) : '0.00';
                                ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <i class="fas fa-coins me-1"></i>KES <?php echo $month_deductions ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Number of Agrovets -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span
                                            class="avatar avatar-md avatar-rounded d-flex align-items-center justify-content-center"
                                            style="background:#6AA32D;">
                                            <i class="fa-solid fa-store fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Agrovets with Credits
                                                </p>
                                                <?php
                                // Count unique agrovets with input credit repayments
                                $query = "SELECT COUNT(DISTINCT ica.agrovet_id) as count 
                                         FROM input_credit_repayments icr
                                         JOIN approved_input_credits aic ON icr.approved_credit_id = aic.id
                                         JOIN input_credit_applications ica ON aic.credit_application_id = ica.id";
                                $result = $app->select_one($query);
                                $agrovet_count = ($result) ? $result->count : 0;
                                ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <i class="fas fa-building me-1"></i><?php echo $agrovet_count ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Farmers with Deductions -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span
                                            class="avatar avatar-md avatar-rounded d-flex align-items-center justify-content-center"
                                            style="background:#6AA32D;">
                                            <i class="fa-solid fa-users fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Farmers with
                                                    Deductions</p>
                                                <?php
                                // Count unique farmers with input credit repayments
                                $query = "SELECT COUNT(DISTINCT ica.farmer_id) as count 
                                         FROM input_credit_repayments icr
                                         JOIN approved_input_credits aic ON icr.approved_credit_id = aic.id
                                         JOIN input_credit_applications ica ON aic.credit_application_id = ica.id";
                                $result = $app->select_one($query);
                                $farmer_count = ($result) ? $result->count : 0;
                                ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <i class="fas fa-user-friends me-1"></i><?php echo $farmer_count ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Deduction Analysis Section -->
                <div class="row mt-4">
                    <div class="col-xl-12">
                        <div class="card custom-card shadow-sm">
                            <div class="card-header d-flex align-items-center border-bottom"
                                style="background-color: rgba(106, 163, 45, 0.1);">
                                <div class="card-title mb-0">
                                    <h5 class="mb-0" style="color:#6AA32D;">
                                        <i class="fas fa-chart-line me-2"></i>Credit Deduction Analysis
                                    </h5>
                                </div>
                                <div class="ms-auto">
                                    <button class="btn btn-sm" style="background-color: #6AA32D; color: white;"
                                        onclick="refreshDeductionsData()">
                                        <i class="fas fa-sync-alt me-1"></i> Refresh Data
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Deduction Categories -->
                                    <div class="col-lg-5">
                                        <div class="p-4 border-end h-100">
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <h6 class="mb-0" style="color:#6AA32D;">
                                                    <i class="fas fa-tags me-2"></i> Deduction by Input Type
                                                </h6>
                                                <span class="badge rounded-pill px-3 py-2"
                                                    style="background-color: rgba(106, 163, 45, 0.15); color: #6AA32D;">
                                                    <i class="fas fa-chart-pie me-1"></i> Distribution
                                                </span>
                                            </div>

                                            <?php
                            // Get input credit deductions by type
                            // Fertilizers
                            $query = "SELECT 
                                    SUM(icr.deducted_amount) as total_amount
                                    FROM input_credit_repayments icr
                                    JOIN approved_input_credits aic ON icr.approved_credit_id = aic.id
                                    JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                    JOIN input_credit_items ici ON ici.credit_application_id = ica.id
                                    WHERE ici.input_type = 'fertilizer'";
                            $fertilizers = $app->select_one($query);
                            
                            // Seeds
                            $query = "SELECT 
                                    SUM(icr.deducted_amount) as total_amount
                                    FROM input_credit_repayments icr
                                    JOIN approved_input_credits aic ON icr.approved_credit_id = aic.id
                                    JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                    JOIN input_credit_items ici ON ici.credit_application_id = ica.id
                                    WHERE ici.input_type = 'seeds'";
                            $seeds = $app->select_one($query);
                            
                            // Pesticides
                            $query = "SELECT 
                                    SUM(icr.deducted_amount) as total_amount
                                    FROM input_credit_repayments icr
                                    JOIN approved_input_credits aic ON icr.approved_credit_id = aic.id
                                    JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                    JOIN input_credit_items ici ON ici.credit_application_id = ica.id
                                    WHERE ici.input_type = 'pesticide'";
                            $pesticides = $app->select_one($query);
                            
                            // Tools and Other items
                            $query = "SELECT 
                                    SUM(icr.deducted_amount) as total_amount
                                    FROM input_credit_repayments icr
                                    JOIN approved_input_credits aic ON icr.approved_credit_id = aic.id
                                    JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                    JOIN input_credit_items ici ON ici.credit_application_id = ica.id
                                    WHERE (ici.input_type = 'tools' OR ici.input_type = 'other')";
                            $tools_others = $app->select_one($query);
                            
                            // Calculate grand total for percentages
                            $grand_total = 
                                ($fertilizers && $fertilizers->total_amount ? $fertilizers->total_amount : 0) +
                                ($seeds && $seeds->total_amount ? $seeds->total_amount : 0) +
                                ($pesticides && $pesticides->total_amount ? $pesticides->total_amount : 0) +
                                ($tools_others && $tools_others->total_amount ? $tools_others->total_amount : 0);
                            
                            // Calculate percentages
                            $fertilizer_percentage = ($grand_total > 0 && $fertilizers && $fertilizers->total_amount) ? 
                                round(($fertilizers->total_amount / $grand_total) * 100) : 0;
                            
                            $seed_percentage = ($grand_total > 0 && $seeds && $seeds->total_amount) ? 
                                round(($seeds->total_amount / $grand_total) * 100) : 0;
                                
                            $pesticide_percentage = ($grand_total > 0 && $pesticides && $pesticides->total_amount) ? 
                                round(($pesticides->total_amount / $grand_total) * 100) : 0;
                                
                            $tools_percentage = ($grand_total > 0 && $tools_others && $tools_others->total_amount) ? 
                                round(($tools_others->total_amount / $grand_total) * 100) : 0;
                            ?>

                                            <!-- Card-based deduction categories -->
                                            <div class="row g-3">
                                                <!-- Fertilizers Card -->
                                                <div class="col-12">
                                                    <div class="card border-0 shadow-sm">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar avatar-md rounded-circle d-flex align-items-center justify-content-center me-3"
                                                                    style="background-color: #6AA32D;">
                                                                    <i class="fas fa-fill-drip text-white"></i>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center mb-2">
                                                                        <h6 class="mb-0">Fertilizers</h6>
                                                                        <span class="badge rounded-pill px-3"
                                                                            style="background-color: rgba(106, 163, 45, 0.15); color: #6AA32D;">
                                                                            <?php echo $fertilizer_percentage; ?>%
                                                                        </span>
                                                                    </div>
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center text-muted small">
                                                                        <span>Deductions for fertilizer inputs</span>
                                                                        <span>KES
                                                                            <?php echo ($fertilizers && $fertilizers->total_amount) ? number_format($fertilizers->total_amount, 2) : '0.00'; ?></span>
                                                                    </div>
                                                                    <div class="progress mt-2"
                                                                        style="height: 6px; border-radius: 4px;">
                                                                        <div class="progress-bar"
                                                                            style="width: <?php echo $fertilizer_percentage; ?>%; background-color: #6AA32D; border-radius: 4px;">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Seeds Card -->
                                                <div class="col-12">
                                                    <div class="card border-0 shadow-sm">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar avatar-md rounded-circle d-flex align-items-center justify-content-center me-3"
                                                                    style="background-color: #6AA32D;">
                                                                    <i class="fas fa-seedling text-white"></i>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center mb-2">
                                                                        <h6 class="mb-0">Seeds</h6>
                                                                        <span class="badge rounded-pill px-3"
                                                                            style="background-color: rgba(106, 163, 45, 0.15); color: #6AA32D;">
                                                                            <?php echo $seed_percentage; ?>%
                                                                        </span>
                                                                    </div>
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center text-muted small">
                                                                        <span>Deductions for seed inputs</span>
                                                                        <span>KES
                                                                            <?php echo ($seeds && $seeds->total_amount) ? number_format($seeds->total_amount, 2) : '0.00'; ?></span>
                                                                    </div>
                                                                    <div class="progress mt-2"
                                                                        style="height: 6px; border-radius: 4px;">
                                                                        <div class="progress-bar"
                                                                            style="width: <?php echo $seed_percentage; ?>%; background-color: #6AA32D; border-radius: 4px;">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Pesticides Card -->
                                                <div class="col-12">
                                                    <div class="card border-0 shadow-sm">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar avatar-md rounded-circle d-flex align-items-center justify-content-center me-3"
                                                                    style="background-color: #6AA32D;">
                                                                    <i class="fas fa-spray-can text-white"></i>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center mb-2">
                                                                        <h6 class="mb-0">Pesticides</h6>
                                                                        <span class="badge rounded-pill px-3"
                                                                            style="background-color: rgba(106, 163, 45, 0.15); color: #6AA32D;">
                                                                            <?php echo $pesticide_percentage; ?>%
                                                                        </span>
                                                                    </div>
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center text-muted small">
                                                                        <span>Deductions for pesticide inputs</span>
                                                                        <span>KES
                                                                            <?php echo ($pesticides && $pesticides->total_amount) ? number_format($pesticides->total_amount, 2) : '0.00'; ?></span>
                                                                    </div>
                                                                    <div class="progress mt-2"
                                                                        style="height: 6px; border-radius: 4px;">
                                                                        <div class="progress-bar"
                                                                            style="width: <?php echo $pesticide_percentage; ?>%; background-color: #6AA32D; border-radius: 4px;">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Tools & Others Card -->
                                                <div class="col-12">
                                                    <div class="card border-0 shadow-sm">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar avatar-md rounded-circle d-flex align-items-center justify-content-center me-3"
                                                                    style="background-color: #6AA32D;">
                                                                    <i class="fas fa-tools text-white"></i>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center mb-2">
                                                                        <h6 class="mb-0">Tools & Others</h6>
                                                                        <span class="badge rounded-pill px-3"
                                                                            style="background-color: rgba(106, 163, 45, 0.15); color: #6AA32D;">
                                                                            <?php echo $tools_percentage; ?>%
                                                                        </span>
                                                                    </div>
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center text-muted small">
                                                                        <span>Deductions for tools & other inputs</span>
                                                                        <span>KES
                                                                            <?php echo ($tools_others && $tools_others->total_amount) ? number_format($tools_others->total_amount, 2) : '0.00'; ?></span>
                                                                    </div>
                                                                    <div class="progress mt-2"
                                                                        style="height: 6px; border-radius: 4px;">
                                                                        <div class="progress-bar"
                                                                            style="width: <?php echo $tools_percentage; ?>%; background-color: #6AA32D; border-radius: 4px;">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Total Summary Card -->
                                                <div class="col-12">
                                                    <div class="card border-0 shadow-sm"
                                                        style="background-color: rgba(106, 163, 45, 0.08);">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar avatar-md rounded-circle d-flex align-items-center justify-content-center me-3"
                                                                    style="background-color: #6AA32D;">
                                                                    <i class="fas fa-calculator text-white"></i>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center mb-2">
                                                                        <h6 class="mb-0 fw-bold">Total Deductions</h6>
                                                                        <span class="badge rounded-pill px-3"
                                                                            style="background-color: #6AA32D; color: white;">
                                                                            100%
                                                                        </span>
                                                                    </div>
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center fw-semibold">
                                                                        <span>All input credit deductions</span>
                                                                        <span>KES
                                                                            <?php echo number_format($grand_total, 2); ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Top Agrovets by Deduction Amount -->
                                    <div class="col-lg-7">
                                        <div class="p-4 h-100">
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <h6 class="mb-0" style="color:#6AA32D;">
                                                    <i class="fas fa-award me-2"></i> Top Agrovets by Deduction Amount
                                                </h6>
                                                <div>
                                                    <button class="btn btn-sm shadow-sm border"
                                                        onclick="refreshAgrovetDeductionData()">
                                                        <i class="fas fa-sync-alt" style="color: #6AA32D;"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="table-responsive shadow-sm rounded">
                                                <table class="table table-hover mb-0">
                                                    <thead>
                                                        <tr style="background-color: rgba(106, 163, 45, 0.1);">
                                                            <th style="color:#6AA32D;"><i
                                                                    class="fas fa-hashtag me-1"></i> Rank</th>
                                                            <th style="color:#6AA32D;"><i class="fas fa-store me-1"></i>
                                                                Agrovet</th>
                                                            <th style="color:#6AA32D;"><i
                                                                    class="fas fa-map-marker-alt me-1"></i> Location
                                                            </th>
                                                            <th style="color:#6AA32D;" class="text-center"><i
                                                                    class="fas fa-users me-1"></i> Farmers</th>
                                                            <th style="color:#6AA32D;" class="text-center"><i
                                                                    class="fas fa-receipt me-1"></i> Deductions</th>
                                                            <th style="color:#6AA32D;" class="text-end"><i
                                                                    class="fas fa-money-bill me-1"></i> Amount (KES)
                                                            </th>
                                                            <th style="color:#6AA32D;" class="text-center"><i
                                                                    class="fas fa-chart-pie me-1"></i> % of Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                      // Query to get top agrovets by deduction amount
                                      $query = "SELECT 
                                              a.id as agrovet_id,
                                              a.name as agrovet_name,
                                              a.location,
                                              COUNT(DISTINCT icr.id) as deduction_count,
                                              COUNT(DISTINCT ica.farmer_id) as farmer_count,
                                              COALESCE(SUM(icr.deducted_amount), 0) as deduction_amount
                                              FROM input_credit_repayments icr
                                              JOIN approved_input_credits aic ON icr.approved_credit_id = aic.id
                                              JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                              JOIN agrovets a ON ica.agrovet_id = a.id
                                              GROUP BY a.id, a.name, a.location
                                              ORDER BY deduction_amount DESC
                                              LIMIT 10";
                                      
                                      $top_agrovets = $app->select_all($query);
                                      
                                      // Get total deductions for percentage calculation
                                      $query = "SELECT COALESCE(SUM(deducted_amount), 0) as total_deductions 
                                               FROM input_credit_repayments";
                                      $total_result = $app->select_one($query);
                                      $total_deductions = ($total_result) ? $total_result->total_deductions : 0;
                                      
                                      if ($top_agrovets && count($top_agrovets) > 0) {
                                          $rank = 1;
                                          foreach ($top_agrovets as $agrovet) {
                                              // Calculate percentage of total
                                              $percentage = ($total_deductions > 0) ? 
                                                  round(($agrovet->deduction_amount / $total_deductions) * 100, 1) : 0;
                                              
                                              // Determine percentage class based on value
                                              if ($percentage >= 25) {
                                                  $percentage_class = 'bg-success';
                                              } elseif ($percentage >= 10) {
                                                  $percentage_class = 'bg-info';
                                              } elseif ($percentage >= 5) {
                                                  $percentage_class = 'bg-warning';
                                              } else {
                                                  $percentage_class = 'bg-secondary';
                                              }
                                              
                                              echo '<tr>
                                                  <td class="fw-medium text-center">'.$rank.'</td>
                                                  <td>
                                                      <div class="d-flex align-items-center">
                                                          <span class="avatar avatar-xs me-2" style="background-color:#6AA32D;">
                                                              <i class="fas fa-store text-white fs-10"></i>
                                                          </span>
                                                          <span class="text-truncate" style="max-width: 150px;">'.$agrovet->agrovet_name.'</span>
                                                      </div>
                                                  </td>
                                                  <td><span class="text-muted small">'.$agrovet->location.'</span></td>
                                                  <td class="text-center">'.$agrovet->farmer_count.'</td>
                                                  <td class="text-center">'.$agrovet->deduction_count.'</td>
                                                  <td class="text-end fw-semibold">'.number_format($agrovet->deduction_amount, 2).'</td>
                                                  <td class="text-center">
                                                      <div class="d-flex align-items-center justify-content-center">
                                                          <div class="me-2" style="width: 50px; height: 6px; background-color: rgba(106, 163, 45, 0.1); border-radius: 3px;">
                                                              <div class="'.$percentage_class.'" style="width: '.$percentage.'%; height: 6px; border-radius: 3px;"></div>
                                                          </div>
                                                          <span class="small">'.$percentage.'%</span>
                                                      </div>
                                                  </td>
                                              </tr>';
                                              $rank++;
                                          }
                                      } else {
                                          echo '<tr><td colspan="7" class="text-center">No input credit deductions found</td></tr>';
                                      }
                                      ?>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="d-flex justify-content-center mt-4">
                                                <a href="#" class="btn btn-sm px-4"
                                                    style="background-color: #6AA32D; color: white;">
                                                    <i class="fas fa-search me-1"></i> View All Agrovets
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Deduction Transactions Section -->
                <div class="row mt-4">
                    <div class="col-xl-12">
                        <div class="card custom-card shadow-sm">
                            <div class="card-header d-flex align-items-center border-bottom"
                                style="background-color: rgba(106, 163, 45, 0.1);">
                                <div class="card-title mb-0">
                                    <h5 class="mb-0" style="color:#6AA32D;">
                                        <i class="fas fa-exchange-alt me-2"></i>Recent Deduction Transactions
                                    </h5>
                                </div>
                                <div class="ms-auto">
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control" placeholder="Search transactions...">
                                        <button class="btn" type="button"
                                            style="background-color: #6AA32D; color: white;">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                            <tr style="background-color: rgba(106, 163, 45, 0.1);">
                                                <th style="color:#6AA32D;"><i class="fas fa-hashtag me-1"></i> ID</th>
                                                <th style="color:#6AA32D;"><i class="fas fa-calendar me-1"></i> Date
                                                </th>
                                                <th style="color:#6AA32D;"><i class="fas fa-user me-1"></i> Farmer</th>
                                                <th style="color:#6AA32D;"><i class="fas fa-store me-1"></i> Agrovet
                                                </th>
                                                <th style="color:#6AA32D;"><i class="fas fa-receipt me-1"></i> Sale
                                                    Reference</th>
                                                <th style="color:#6AA32D;"><i class="fas fa-percentage me-1"></i> Rate
                                                </th>
                                                <th style="color:#6AA32D;"><i class="fas fa-shopping-cart me-1"></i>
                                                    Sale Amount</th>
                                                <th style="color:#6AA32D;" class="text-end"><i
                                                        class="fas fa-money-bill-wave me-1"></i> Deducted</th>
                                                <th style="color:#6AA32D;" class="text-center"><i
                                                        class="fas fa-ellipsis-h me-1"></i> Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                            // Query to get recent input credit deductions
                            $query = "SELECT 
                                    icr.id,
                                    icr.deduction_date,
                                    u.first_name as farmer_fname,
                                    u.last_name as farmer_lname,
                                    a.name as agrovet_name,
                                    pd.id as delivery_id,
                                    aic.repayment_percentage,
                                    icr.produce_sale_amount,
                                    icr.deducted_amount
                                    FROM input_credit_repayments icr
                                    JOIN approved_input_credits aic ON icr.approved_credit_id = aic.id
                                    JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                    JOIN farmers f ON ica.farmer_id = f.id
                                    JOIN users u ON f.user_id = u.id
                                    JOIN agrovets a ON ica.agrovet_id = a.id
                                    JOIN produce_deliveries pd ON icr.produce_delivery_id = pd.id
                                    ORDER BY icr.deduction_date DESC
                                    LIMIT 10";
                            
                            $recent_deductions = $app->select_all($query);
                            
                            if ($recent_deductions && count($recent_deductions) > 0) {
                                foreach ($recent_deductions as $deduction) {
                                    // Format the date
                                    $date = date('d M Y', strtotime($deduction->deduction_date));
                                    
                                    // Calculate percentage for progress bar
                                    $percentage = round(($deduction->deducted_amount / $deduction->produce_sale_amount) * 100);
                                    
                                    echo '<tr>
                                        <td><span class="badge bg-light text-dark">DCT-'.$deduction->id.'</span></td>
                                        <td>'.$date.'</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-xs me-2" style="background-color:#6AA32D;">
                                                    <i class="fas fa-user text-white fs-10"></i>
                                                </span>
                                                <span>'.$deduction->farmer_fname.' '.$deduction->farmer_lname.'</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-truncate d-inline-block" style="max-width: 150px;">'.$deduction->agrovet_name.'</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">DLVR'.str_pad($deduction->delivery_id, 5, '0', STR_PAD_LEFT).'</span>
                                        </td>
                                        <td>'.$deduction->repayment_percentage.'%</td>
                                        <td>KES '.number_format($deduction->produce_sale_amount, 2).'</td>
                                        <td class="text-end fw-semibold" style="color:#6AA32D;">KES '.number_format($deduction->deducted_amount, 2).'</td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm" style="color:#6AA32D;" 
                                                    onclick="viewDeductionDetails('.$deduction->id.')">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm" style="color:#6AA32D;"
                                                    onclick="printDeductionReceipt('.$deduction->id.')">
                                                    <i class="fas fa-print"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>';
                                }
                            } else {
                                echo '<tr><td colspan="9" class="text-center">No recent deduction transactions found</td></tr>';
                            }
                            ?>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <div>
                                        <span class="text-muted small">Showing 1 to 10 of recent transactions</span>
                                    </div>
                                    <div>
                                        <a href="#" class="btn btn-sm px-4"
                                            style="background-color: #6AA32D; color: white;">
                                            <i class="fas fa-list me-1"></i> View All Transactions
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Input Credit Deductions Section -->
                <div class="row mt-4">
                    <div class="col-xl-12">
                        <div id="activeDeductionsSection"></div>
                    </div>
                </div>

            </div>


            <!-- Scroll To Top -->
            <div class=" scrollToTop">
                <span class="arrow"><i class="ri-arrow-up-s-fill fs-20"></i></span>
            </div>
            <div id="responsive-overlay"></div>
            <!-- Scroll To Top -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
                integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
                crossorigin="anonymous" referrerpolicy="no-referrer">
            </script>
            <!-- Popper JS -->
            <script src="http://localhost/dfcs/assets/libs/%40popperjs/core/umd/popper.min.js">
            </script>
            <!-- Bootstrap JS -->
            <script src="http://localhost/dfcs/assets/libs/bootstrap/js/bootstrap.bundle.min.js">
            </script>
            <!-- Defaultmenu JS -->
            <script src="http://localhost/dfcs/assets/js/defaultmenu.min.js">
            </script>
            <!-- Node Waves JS-->
            <script src="http://localhost/dfcs/assets/libs/node-waves/waves.min.js">
            </script>
            <!-- Sticky JS -->
            <script src="http://localhost/dfcs/assets/js/sticky.js">
            </script>
            <!-- Simplebar JS -->
            <script src="http://localhost/dfcs/assets/libs/simplebar/simplebar.min.js">
            </script>
            <script src="http://localhost/dfcs/assets/js/simplebar.js">
            </script>

            <!-- Color Picker JS -->
            <script src="http://localhost/dfcs/assets/libs/%40simonwep/pickr/pickr.es5.min.js">
            </script>
            <!-- Custom-Switcher JS -->
            <script src="http://localhost/dfcs/assets/js/custom-switcher.min.js">
            </script>

            <!-- Custom JS -->
            <script src="http://localhost/dfcs/assets/js/custom.js">
            </script>
            <!-- Used In Zoomable TIme Series Chart -->
            <script src="http://localhost/dfcs/assets/js/dataseries.js">
            </script>
            <!---Used In Annotations Chart-->
            <script src="http://localhost/dfcs/assets/js/apexcharts-stock-prices.js">
            </script>
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
            <script src="http://localhost/dfcs/assets/js/datatables.js">
            </script>
            <!-- Toastr JS -->
            <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4">
            </script>
            <script>
            $(document).ready(() => {
                // Initially load active deductions section
                loadActiveDeductions();
            });

            // Function to refresh deductions data
            function refreshDeductionsData() {
                loadActiveDeductions();
            }

            // Function to display active deductions
            function loadActiveDeductions() {
                // Show enhanced loader
                $('#activeDeductionsSection').html(`
         <div class="card custom-card shadow-sm">
             <div class="card-body">
                 <div class="text-center py-5">
                     <div class="spinner-grow" style="width: 3rem; height: 3rem; color: #6AA32D;" role="status">
                         <span class="visually-hidden">Loading...</span>
                     </div>
                     <div class="mt-3">
                         <h5 style="color: #6AA32D;"><i class="fas fa-sync-alt fa-spin me-2"></i>Loading input credit deductions...</h5>
                         <p class="text-muted mb-0">Please wait while we fetch the latest information</p>
                     </div>
                 </div>
             </div>
         </div>
         `);

                // Fetch active deductions data
                $.ajax({
                    url: "http://localhost/dfcs/ajax/agrovet-controller/display-input-credit-deductions.php",
                    type: 'POST',
                    data: {
                        displayInputCreditDeductions: "true",
                    },
                    success: function(data, status) {
                        $('#activeDeductionsSection').html(data);

                        // Initialize DataTable with sorting and export features
                        if ($.fn.DataTable) {
                            $('#datatable-deductions').DataTable({
                                responsive: true,
                                order: [
                                    [1, 'desc'] // Sort by date by default (newest first)
                                ],
                                language: {
                                    searchPlaceholder: 'Search deductions...',
                                    sSearch: '',
                                },
                                lengthMenu: [
                                    [10, 25, 50, -1],
                                    [10, 25, 50, "All"]
                                ],
                                dom: 'Bfrtip',
                                buttons: [{
                                    extend: 'collection',
                                    text: '<i class="fa-solid fa-download me-1"></i> Export',
                                    buttons: [{
                                            extend: 'excel',
                                            text: '<i class="fa-solid fa-file-excel me-1"></i> Excel',
                                            className: 'dropdown-item',
                                            exportOptions: {
                                                columns: [0, 1, 2, 3, 4, 5, 6, 7]
                                            }
                                        },
                                        {
                                            extend: 'pdf',
                                            text: '<i class="fa-solid fa-file-pdf me-1"></i> PDF',
                                            className: 'dropdown-item',
                                            exportOptions: {
                                                columns: [0, 1, 2, 3, 4, 5, 6, 7]
                                            }
                                        },
                                        {
                                            extend: 'print',
                                            text: '<i class="fa-solid fa-print me-1"></i> Print',
                                            className: 'dropdown-item',
                                            exportOptions: {
                                                columns: [0, 1, 2, 3, 4, 5, 6, 7]
                                            }
                                        }
                                    ],
                                    className: 'btn btn-outline-success me-3'
                                }]
                            });

                            // Add custom filter controls for agrovet
                            $("<div class='d-flex gap-2 mb-3 ms-3'></div>")
                                .append(
                                    "<button class='btn btn-sm btn-success filter-btn' data-filter='all'><i class='fa-solid fa-filter me-1'></i> All</button>"
                                )
                                .append(
                                    "<button class='btn btn-sm btn-outline-success filter-btn' data-filter='month'><i class='fa-solid fa-calendar-alt me-1'></i> This Month</button>"
                                )
                                .append(
                                    "<button class='btn btn-sm btn-outline-success filter-btn' data-filter='paid'><i class='fa-solid fa-check-circle me-1'></i> Paid</button>"
                                )
                                .append(
                                    "<button class='btn btn-sm btn-outline-success filter-btn' data-filter='pending'><i class='fa-solid fa-clock me-1'></i> Pending</button>"
                                )
                                .insertBefore('#datatable-deductions_wrapper .dataTables_filter');

                            // Add filter functionality
                            $('.filter-btn').on('click', function() {
                                let filterValue = $(this).data('filter');
                                let table = $('#datatable-deductions').DataTable();

                                $('.filter-btn').removeClass('btn-success').addClass(
                                    'btn-outline-success');
                                $(this).removeClass('btn-outline-success').addClass('btn-success');

                                if (filterValue === 'all') {
                                    table.search('').draw();
                                } else if (filterValue === 'month') {
                                    // Get current month name
                                    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                                        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                                    ];
                                    const currentMonth = monthNames[new Date().getMonth()];
                                    table.search(currentMonth).draw();
                                } else {
                                    table.search(filterValue).draw();
                                }
                            });
                        }
                    },
                    error: function() {
                        $('#activeDeductionsSection').html(`
                           <div class="card custom-card shadow-sm">
                               <div class="card-body">
                                   <div class="text-center py-4">
                                       <i class="fa-solid fa-triangle-exclamation fa-3x mb-3" style="color: #dc3545;"></i>
                                       <h5>Error Loading Data</h5>
                                       <p class="text-muted">There was a problem loading the input credit deductions. Please try again.</p>
                                       <button class="btn" style="background-color: #6AA32D; color: white;" onclick="loadActiveDeductions()">
                                           <i class="fa-solid fa-sync-alt me-1"></i> Retry
                                       </button>
                                   </div>
                               </div>
                           </div>
                           `);
                    }
                });
            }

            // Function to toggle details row visibility
            function toggleDeductionDetails(deductionId) {
                const detailsRow = document.getElementById('deduction-details-' + deductionId);

                if (detailsRow.style.display === 'none') {
                    // Close all other detail rows first
                    document.querySelectorAll('.deduction-detail-row').forEach(row => {
                        row.style.display = 'none';
                    });

                    // Toggle buttons visual state
                    document.querySelectorAll('.view-deduction-details').forEach(btn => {
                        btn.innerHTML = '<i class="fa-solid fa-eye me-1"></i> View';
                        btn.classList.remove('btn-success');
                        btn.classList.add('btn-outline-success');
                    });

                    // Show this detail row
                    detailsRow.style.display = 'table-row';

                    // Update button
                    const button = document.querySelector(`.view-deduction-details[data-id="${deductionId}"]`);
                    button.innerHTML = '<i class="fa-solid fa-eye-slash me-1"></i> Hide';
                    button.classList.remove('btn-outline-success');
                    button.classList.add('btn-success');
                } else {
                    // Hide detail row
                    detailsRow.style.display = 'none';

                    // Update button
                    const button = document.querySelector(`.view-deduction-details[data-id="${deductionId}"]`);
                    button.innerHTML = '<i class="fa-solid fa-eye me-1"></i> View';
                    button.classList.remove('btn-success');
                    button.classList.add('btn-outline-success');
                }
            }

            // Function to print deduction receipt
            function printDeductionReceipt(deductionId) {
                // Create a popup window for the receipt
                const printWindow = window.open('', '_blank', 'width=800,height=600');

                // Show loading message
                printWindow.document.write(`
                                    <html>
                                    <head>
                                        <title>Deduction Receipt #${deductionId}</title>
                                        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
                                        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
                                        <style>
                                            body { font-family: Arial, sans-serif; padding: 20px; }
                                            .loading { text-align: center; padding: 50px; }
                                            .receipt-header { text-align: center; margin-bottom: 30px; }
                                            .receipt-body { margin-bottom: 30px; }
                                            .receipt-footer { text-align: center; margin-top: 50px; font-size: 0.9em; color: #666; }
                                            .company-logo { max-width: 150px; margin-bottom: 15px; }
                                            .receipt-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                                            .receipt-table th, .receipt-table td { padding: 10px; border: 1px solid #ddd; }
                                            .receipt-table th { background-color: #f5f5f5; }
                                            .receipt-meta { margin-bottom: 20px; }
                                            .receipt-meta div { margin-bottom: 5px; }
                                            @media print {
                                                .no-print { display: none; }
                                                body { padding: 0; }
                                            }
                                        </style>
                                    </head>
                                    <body>
                                        <div class="loading">
                                            <div class="spinner-border text-success" role="status"></div>
                                            <p class="mt-3">Loading receipt data...</p>
                                        </div>
                                    </body>
                                    </html>
                                `);

                // Fetch receipt data
                $.ajax({
                    url: "http://localhost/dfcs/ajax/agrovet-controller/get-deduction-receipt.php",
                    type: 'POST',
                    data: {
                        deductionId: deductionId
                    },
                    success: function(data) {
                        printWindow.document.body.innerHTML = data;

                        // Add print button
                        const printButton = printWindow.document.createElement('button');
                        printButton.innerHTML = '<i class="fas fa-print me-1"></i> Print Receipt';
                        printButton.className = 'btn btn-success no-print mt-3';
                        printButton.onclick = function() {
                            printWindow.print();
                        };
                        printWindow.document.body.appendChild(printButton);

                        // Add close button
                        const closeButton = printWindow.document.createElement('button');
                        closeButton.innerHTML = '<i class="fas fa-times me-1"></i> Close';
                        closeButton.className = 'btn btn-outline-secondary no-print mt-3 ms-2';
                        closeButton.onclick = function() {
                            printWindow.close();
                        };
                        printWindow.document.body.appendChild(closeButton);
                    },
                    error: function() {
                        printWindow.document.body.innerHTML = `
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error loading receipt data. Please try again.
                </div>
                <button class="btn btn-primary no-print" onclick="window.close()">Close</button>
            `;
                    }
                });
            }
            </script>
</body>



</html>