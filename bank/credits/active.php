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

                <!-- Input Credit Stats Cards -->
                <div class="row mt-2">
                    <!-- Total Active Credits -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden shadow-sm" style="background:#6AA32D!important;">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span
                                            class="avatar avatar-md avatar-rounded d-flex align-items-center justify-content-center"
                                            style="background-color: white!important;">
                                            <i style="color:#6AA32D" class="fa-solid fa-credit-card fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-white mb-0">Active Input Credits</p>
                                                <?php
                                                    // Count active input credit applications
                                                    $query = "SELECT COUNT(*) as count 
                                                             FROM approved_input_credits
                                                             WHERE status = 'active'";
                                                    $result = $app->select_one($query);
                                                    $active_credits = ($result) ? $result->count : 0;
                                                    ?>
                                                <h4 style="color:wheat;" class="fw-semibold mt-1">
                                                    <i class="fas fa-layer-group me-1"></i><?php echo $active_credits ?>
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
                        <div class="card custom-card overflow-hidden shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span
                                            class="avatar avatar-md avatar-rounded d-flex align-items-center justify-content-center"
                                            style="background:#6AA32D;">
                                            <i class="fa-solid fa-money-bill-wave fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Outstanding Amount</p>
                                                <?php
                                // Calculate total outstanding amounts for input credits
                                $query = "SELECT COALESCE(SUM(remaining_balance), 0) as total_outstanding
                                         FROM approved_input_credits
                                         WHERE status = 'active'";
                                $result = $app->select_one($query);
                                $outstanding_amount = ($result) ? number_format($result->total_outstanding, 2) : 0;
                                ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <i class="fas fa-coins me-1"></i>KES
                                                    <?php echo $outstanding_amount ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Participating Agrovets -->
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
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Participating Agrovets
                                                </p>
                                                <?php
                                // Count unique agrovets with active input credits
                                $query = "SELECT COUNT(DISTINCT a.id) as count 
                                         FROM approved_input_credits aic
                                         JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                         JOIN agrovets a ON ica.agrovet_id = a.id
                                         WHERE aic.status = 'active'";
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

                    <!-- Average Repayment Rate -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span
                                            class="avatar avatar-md avatar-rounded d-flex align-items-center justify-content-center"
                                            style="background:#6AA32D;">
                                            <i class="fa-solid fa-chart-pie fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Avg. Repayment Rate
                                                </p>
                                                <?php
                                // Calculate average repayment rate
                                $query = "SELECT 
                                         AVG((total_with_interest - remaining_balance) / total_with_interest * 100) as avg_repayment
                                         FROM approved_input_credits
                                         WHERE status = 'active'";
                                $result = $app->select_one($query);
                                $avg_repayment = ($result && $result->avg_repayment) ? round($result->avg_repayment) : 0;
                                ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <i class="fas fa-percentage me-1"></i><?php echo $avg_repayment ?>%
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Input Credit Analysis Section -->
                <div class="row mt-4">
                    <div class="col-xl-12">
                        <div class="card custom-card shadow-sm">
                            <div class="card-header d-flex align-items-center border-bottom"
                                style="background-color: rgba(106, 163, 45, 0.1);">
                                <div class="card-title mb-0">
                                    <h5 class="mb-0" style="color:#6AA32D;">
                                        <i class="fas fa-chart-line me-2"></i>Input Credit Analysis
                                    </h5>
                                </div>
                                <div class="ms-auto">
                                    <button class="btn btn-sm" style="background-color: #6AA32D; color: white;"
                                        onclick="refreshInputCreditsData()">
                                        <i class="fas fa-sync-alt me-1"></i> Refresh Data
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Input Credit Categories -->
                                    <div class="col-lg-5">
                                        <div class="p-4 border-end h-100">
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <h6 class="mb-0" style="color:#6AA32D;">
                                                    <i class="fas fa-tags me-2"></i> Input Credit Categories
                                                </h6>
                                                <span class="badge rounded-pill px-3 py-2"
                                                    style="background-color: rgba(106, 163, 45, 0.15); color: #6AA32D;">
                                                    <i class="fas fa-chart-pie me-1"></i> Distribution
                                                </span>
                                            </div>

                                            <?php
                            // Get input credit distribution by type
                            // Fertilizers
                            $query = "SELECT 
                                    COUNT(DISTINCT aic.id) as count,
                                    SUM(aic.remaining_balance) as total_amount
                                    FROM approved_input_credits aic
                                    JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                    JOIN input_credit_items ici ON ici.credit_application_id = ica.id
                                    WHERE aic.status = 'active'
                                    AND ici.input_type = 'fertilizer'";
                            $fertilizers = $app->select_one($query);
                            
                            // Seeds
                            $query = "SELECT 
                                    COUNT(DISTINCT aic.id) as count,
                                    SUM(aic.remaining_balance) as total_amount
                                    FROM approved_input_credits aic
                                    JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                    JOIN input_credit_items ici ON ici.credit_application_id = ica.id
                                    WHERE aic.status = 'active'
                                    AND ici.input_type = 'seeds'";
                            $seeds = $app->select_one($query);
                            
                            // Pesticides
                            $query = "SELECT 
                                    COUNT(DISTINCT aic.id) as count,
                                    SUM(aic.remaining_balance) as total_amount
                                    FROM approved_input_credits aic
                                    JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                    JOIN input_credit_items ici ON ici.credit_application_id = ica.id
                                    WHERE aic.status = 'active'
                                    AND ici.input_type = 'pesticide'";
                            $pesticides = $app->select_one($query);
                            
                            // Tools and Other items
                            $query = "SELECT 
                                    COUNT(DISTINCT aic.id) as count,
                                    SUM(aic.remaining_balance) as total_amount
                                    FROM approved_input_credits aic
                                    JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                    JOIN input_credit_items ici ON ici.credit_application_id = ica.id
                                    WHERE aic.status = 'active'
                                    AND (ici.input_type = 'tools' OR ici.input_type = 'other')";
                            $tools_others = $app->select_one($query);
                            
                            // Calculate grand total for percentages
                            $grand_total = 
                                ($fertilizers ? $fertilizers->total_amount : 0) +
                                ($seeds ? $seeds->total_amount : 0) +
                                ($pesticides ? $pesticides->total_amount : 0) +
                                ($tools_others ? $tools_others->total_amount : 0);
                            
                            // Calculate percentages
                            $fertilizer_percentage = ($grand_total > 0 && $fertilizers && $fertilizers->total_amount) ? 
                                round(($fertilizers->total_amount / $grand_total) * 100) : 0;
                            
                            $seed_percentage = ($grand_total > 0 && $seeds && $seeds->total_amount) ? 
                                round(($seeds->total_amount / $grand_total) * 100) : 0;
                                
                            $pesticide_percentage = ($grand_total > 0 && $pesticides && $pesticides->total_amount) ? 
                                round(($pesticides->total_amount / $grand_total) * 100) : 0;
                                
                            $tools_percentage = ($grand_total > 0 && $tools_others && $tools_others->total_amount) ? 
                                round(($tools_others->total_amount / $grand_total) * 100) : 0;
                                
                            // Calculate total count of active input credits
                            $total_count = 
                                ($fertilizers ? $fertilizers->count : 0) +
                                ($seeds ? $seeds->count : 0) +
                                ($pesticides ? $pesticides->count : 0) +
                                ($tools_others ? $tools_others->count : 0);
                            ?>

                                            <!-- Card-based input credit categories -->
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
                                                                        <span><?php echo ($fertilizers) ? $fertilizers->count : 0; ?>
                                                                            active credits</span>
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
                                                                        <span><?php echo ($seeds) ? $seeds->count : 0; ?>
                                                                            active credits</span>
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
                                                                        <span><?php echo ($pesticides) ? $pesticides->count : 0; ?>
                                                                            active credits</span>
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
                                                                        <span><?php echo ($tools_others) ? $tools_others->count : 0; ?>
                                                                            active credits</span>
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
                                                                        <h6 class="mb-0 fw-bold">Total Active Credits
                                                                        </h6>
                                                                        <span class="badge rounded-pill px-3"
                                                                            style="background-color: #6AA32D; color: white;">
                                                                            100%
                                                                        </span>
                                                                    </div>
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center fw-semibold">
                                                                        <span><?php echo $total_count; ?> credits</span>
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

                                    <!-- Top Agrovets by Credit Volume -->
                                    <div class="col-lg-7">
                                        <div class="p-4 h-100">
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <h6 class="mb-0" style="color:#6AA32D;">
                                                    <i class="fas fa-award me-2"></i> Top Agrovets by Credit Volume
                                                </h6>
                                                <div>
                                                    <button class="btn btn-sm shadow-sm border"
                                                        onclick="refreshAgrovetData()">
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
                                                                    class="fas fa-layer-group me-1"></i> Credits</th>
                                                            <th style="color:#6AA32D;" class="text-end"><i
                                                                    class="fas fa-money-bill me-1"></i> Amount (KES)
                                                            </th>
                                                            <th style="color:#6AA32D;" class="text-center"><i
                                                                    class="fas fa-chart-pie me-1"></i> Recovery</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                        // Query to get top agrovets by input credit volume
                                        $query = "SELECT 
                                                a.id as agrovet_id,
                                                a.name as agrovet_name,
                                                a.location,
                                                COUNT(DISTINCT aic.id) as credit_count,
                                                COUNT(DISTINCT ica.farmer_id) as farmer_count,
                                                SUM(aic.remaining_balance) as outstanding_amount,
                                                ROUND(AVG((aic.total_with_interest - aic.remaining_balance) / aic.total_with_interest * 100), 1) as recovery_rate
                                                FROM approved_input_credits aic
                                                JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                                JOIN agrovets a ON ica.agrovet_id = a.id
                                                WHERE aic.status = 'active'
                                                GROUP BY a.id, a.name, a.location
                                                ORDER BY outstanding_amount DESC
                                                LIMIT 10";
                                        
                                        $top_agrovets = $app->select_all($query);
                                        
                                        if ($top_agrovets && count($top_agrovets) > 0) {
                                            $rank = 1;
                                            foreach ($top_agrovets as $agrovet) {
                                                // Determine recovery rate class based on percentage
                                                if ($agrovet->recovery_rate >= 75) {
                                                    $recovery_class = 'bg-success';
                                                } elseif ($agrovet->recovery_rate >= 50) {
                                                    $recovery_class = 'bg-info';
                                                } elseif ($agrovet->recovery_rate >= 25) {
                                                    $recovery_class = 'bg-warning';
                                                } else {
                                                    $recovery_class = 'bg-danger';
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
                                                    <td class="text-center">'.$agrovet->credit_count.'</td>
                                                    <td class="text-end fw-semibold">'.number_format($agrovet->outstanding_amount, 2).'</td>
                                                    <td class="text-center">
                                                        <div class="d-flex align-items-center justify-content-center">
                                                            <div class="me-2" style="width: 50px; height: 6px; background-color: rgba(106, 163, 45, 0.1); border-radius: 3px;">
                                                                <div class="'.$recovery_class.'" style="width: '.$agrovet->recovery_rate.'%; height: 6px; border-radius: 3px;"></div>
                                                            </div>
                                                            <span class="small">'.$agrovet->recovery_rate.'%</span>
                                                        </div>
                                                    </td>
                                                </tr>';
                                                $rank++;
                                            }
                                        } else {
                                            echo '<tr><td colspan="7" class="text-center">No active input credits found</td></tr>';
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

                <!-- Active Input Credits Section -->
                <div class="row mt-4">
                    <div class="col-xl-12">
                        <div id="activeInputCreditsSection"></div>
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
                // Initially load active input credits section
                loadActiveInputCredits();
            });

            // Function to refresh input credits data
            function refreshInputCreditsData() {
                location.reload();
            }

            // Function to display active input credits
            function loadActiveInputCredits() {
                // Show enhanced loader
                $('#activeInputCreditsSection').html(`
                     <div class="card custom-card shadow-sm">
                         <div class="card-body">
                             <div class="text-center py-5">
                                 <div class="spinner-grow" style="width: 3rem; height: 3rem; color: #6AA32D;" role="status">
                                     <span class="visually-hidden">Loading...</span>
                                 </div>
                                 <div class="mt-3">
                                     <h5 style="color: #6AA32D;"><i class="fas fa-sync-alt fa-spin me-2"></i>Loading active input credits...</h5>
                                     <p class="text-muted mb-0">Please wait while we fetch the latest information</p>
                                 </div>
                             </div>
                         </div>
                     </div>
                     `);

                // Fetch active input credits data
                $.ajax({
                    url: "http://localhost/dfcs/ajax/agrovet-controller/display-active-credits.php",
                    type: 'POST',
                    data: {
                        displayActiveInputCredits: "true",
                    },
                    success: function(data, status) {
                        $('#activeInputCreditsSection').html(data);

                        // Initialize DataTable with sorting and export features
                        if ($.fn.DataTable) {
                            $('#datatable-input-credits').DataTable({
                                responsive: true,
                                order: [
                                    [5, 'desc'] // Sort by original amount by default
                                ],
                                language: {
                                    searchPlaceholder: 'Search credits...',
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

                            // Add custom filter controls for input type
                            $("<div class='d-flex gap-2 mb-3 ms-3'></div>")
                                .append(
                                    "<button class='btn btn-sm btn-success filter-btn' data-filter='all'><i class='fa-solid fa-filter me-1'></i> All</button>"
                                )
                                .append(
                                    "<button class='btn btn-sm btn-outline-success filter-btn' data-filter='fertilizer'><i class='fa-solid fa-fill-drip me-1'></i> Fertilizers</button>"
                                )
                                .append(
                                    "<button class='btn btn-sm btn-outline-success filter-btn' data-filter='seeds'><i class='fa-solid fa-seedling me-1'></i> Seeds</button>"
                                )
                                .append(
                                    "<button class='btn btn-sm btn-outline-success filter-btn' data-filter='pesticide'><i class='fa-solid fa-spray-can me-1'></i> Pesticides</button>"
                                )
                                .append(
                                    "<button class='btn btn-sm btn-outline-success filter-btn' data-filter='tools'><i class='fa-solid fa-tools me-1'></i> Tools</button>"
                                )
                                .insertBefore('#datatable-input-credits_wrapper .dataTables_filter');

                            // Add filter functionality
                            $('.filter-btn').on('click', function() {
                                let filterValue = $(this).data('filter');
                                let table = $('#datatable-input-credits').DataTable();

                                $('.filter-btn').removeClass('btn-success').addClass(
                                    'btn-outline-success');
                                $(this).removeClass('btn-outline-success').addClass('btn-success');

                                if (filterValue === 'all') {
                                    table.column(3).search('').draw();
                                } else {
                                    table.column(3).search(filterValue).draw();
                                }
                            });
                        }
                    },
                    error: function() {
                        $('#activeInputCreditsSection').html(`
            <div class="card custom-card shadow-sm">
                <div class="card-body">
                    <div class="text-center py-4">
                        <i class="fa-solid fa-triangle-exclamation fa-3x mb-3" style="color: #dc3545;"></i>
                        <h5>Error Loading Data</h5>
                        <p class="text-muted">There was a problem loading the active input credits. Please try again.</p>
                        <button class="btn" style="background-color: #6AA32D; color: white;" onclick="loadActiveInputCredits()">
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
            function toggleDetails(creditId) {
                const detailsRow = document.getElementById('details-' + creditId);

                if (detailsRow.style.display === 'none') {
                    // Close all other detail rows first
                    document.querySelectorAll('.detail-row').forEach(row => {
                        row.style.display = 'none';
                    });

                    // Toggle buttons visual state
                    document.querySelectorAll('.view-details').forEach(btn => {
                        btn.innerHTML = '<i class="fa-solid fa-eye me-1"></i> View';
                        btn.classList.remove('btn-success');
                        btn.classList.add('btn-outline-success');
                    });

                    // Show this detail row
                    detailsRow.style.display = 'table-row';

                    // Update button
                    const button = document.querySelector(`.view-details[data-id="${creditId}"]`);
                    button.innerHTML = '<i class="fa-solid fa-eye-slash me-1"></i> Hide';
                    button.classList.remove('btn-outline-success');
                    button.classList.add('btn-success');
                } else {
                    // Hide detail row
                    detailsRow.style.display = 'none';

                    // Update button
                    const button = document.querySelector(`.view-details[data-id="${creditId}"]`);
                    button.innerHTML = '<i class="fa-solid fa-eye me-1"></i> View';
                    button.classList.remove('btn-success');
                    button.classList.add('btn-outline-success');
                }
            }
            </script>
</body>



</html>