<?php include "../../config/config.php" ?>
<?php include "../../libs/App.php" ?>
<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light"
    data-menu-styles="dark" data-toggled="close">

<head>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="http://localhost/dfcs/assets/images/favicon/favicon-96x96.png"
        sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="http://localhost/dfcs/assets/images/favicon/favicon.svg" />
    <link rel="shortcut icon" href="http://localhost/dfcs/assets/images/favicon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180"
        href="http://localhost/dfcs/assets/images/favicon/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Baituti Adventures" />
    <link rel="manifest" href="http://localhost/dfcs/assets/images/favicon/site.webmanifest" />

    <!-- Choices JS -->
    <script src="http://localhost/dfcs/assets/libs/choices.js/public/assets/scripts/choices.min.js">
    </script>

    <!-- Main Theme Js -->
    <script src="http://localhost/dfcs/assets/js/main.js"></script>

    <!-- Bootstrap Css -->
    <link id="style" href="http://localhost/dfcs/assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Style Css -->
    <link href="http://localhost/dfcs/assets/css/styles.min.css" rel="stylesheet" />

    <!-- Icons Css -->
    <link href="http://localhost/dfcs/assets/css/icons.css" rel="stylesheet" />

    <!-- Node Waves Css -->
    <link href="http://localhost/dfcs/assets/libs/node-waves/waves.min.css" rel="stylesheet" />

    <!-- Simplebar Css -->
    <link href="http://localhost/dfcs/assets/libs/simplebar/simplebar.min.css" rel="stylesheet" />

    <!-- Color Picker Css -->
    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/flatpickr/flatpickr.min.css" />
    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/%40simonwep/pickr/themes/nano.min.css" />

    <!-- Choices Css -->
    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/choices.js/public/assets/styles/choices.min.css" />

    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/jsvectormap/css/jsvectormap.min.css" />

    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/swiper/swiper-bundle.min.css" />
</head>

<body>

    <?php include "../../includes/loader.php" ?>

    <div class="page">
        <!-- app-header -->
        <?php include "../../includes/navigation.php" ?>
        <!-- /app-header -->
        <!-- Start::app-sidebar -->
        <?php include "../../includes/sidebar.php" ?>
        <!-- End::app-sidebar -->

        <!-- Start::app-content -->
        <div class="main-content app-content">
            <div class="container-fluid">
                <!-- Start::page-header -->

                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                    <div>
                        <?php 
            $app = new App;
            if (isset($_SESSION['role_id'])) {
                $query = "SELECT * FROM users WHERE id=" . $_SESSION['user_id'];
                $user = $app->select_one($query);
                
                if ($_SESSION['role_id'] == 2) {
                    // SACCO staff welcome message
                    ?>
                        <p class="fw-semibold fs-18 mb-0">Welcome back,
                            <?php echo htmlspecialchars($user->first_name . ' ' . $user->last_name); ?>!</p>
                        <span class="fs-semibold text-muted">SACCO Staff Dashboard - Manage produce, loans, and member
                            activities</span>
                        <?php
                } else {
                    // Other roles welcome message
                    ?>
                        <p class="fw-semibold fs-18 mb-0">Welcome back,
                            <?php echo htmlspecialchars($user->first_name . ' ' . $user->last_name); ?>!</p>
                        <span class="fs-semibold text-muted">Dashboard - Track your activities and manage
                            resources</span>
                        <?php
                }
            }
            ?>
                    </div>
                </div>

                <!-- End::page-header -->

                <!-- Start::row-1 -->
                <div class="row">
                    <div class="col-xxl-12 col-xl-12">
                        <div class="row">

                            <div class="col-xl-12">
                                <div class="row">
                                    <!-- Total Farmers Card -->
                                    <div class="col-xxl-3 col-lg-6 col-md-6">
                                        <div class="card custom-card overflow-hidden">
                                            <div class="card-body">
                                                <div class="d-flex align-items-top justify-content-between">
                                                    <div>
                                                        <span class="avatar avatar-md avatar-rounded bg-primary">
                                                            <i class="ti ti-users fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill ms-3">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between flex-wrap">
                                                            <div>
                                                                <p class="text-muted mb-0">Total Farmers</p>
                                                                <h4 class="fw-semibold mt-1">
                                                                    <?php 
                                            $totalFarmers = $app->select_one("SELECT COUNT(*) as count FROM farmers");
                                            echo number_format($totalFarmers->count); 
                                        ?>
                                                                </h4>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="d-flex align-items-center justify-content-between mt-1">
                                                            <div>
                                                                <a class="text-primary" href="#">View All<i
                                                                        class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                                            </div>
                                                            <div class="text-end">
                                                                <p class="mb-0 text-success fw-semibold">Registered</p>
                                                                <span class="text-muted op-7 fs-11">farmers</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Total Produce Deliveries Card -->
                                    <div class="col-xxl-3 col-lg-6 col-md-6">
                                        <div class="card custom-card overflow-hidden">
                                            <div class="card-body">
                                                <div class="d-flex align-items-top justify-content-between">
                                                    <div>
                                                        <span class="avatar avatar-md avatar-rounded bg-secondary">
                                                            <i class="ti ti-package fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill ms-3">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between flex-wrap">
                                                            <div>
                                                                <p class="text-muted mb-0">Total Produce Deliveries</p>
                                                                <h4 class="fw-semibold mt-1">
                                                                    <?php 
                                            $totalDeliveries = $app->select_one("SELECT COUNT(*) as count FROM produce_deliveries");
                                            echo number_format($totalDeliveries->count); 
                                        ?>
                                                                </h4>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="d-flex align-items-center justify-content-between mt-1">
                                                            <div>
                                                                <a class="text-secondary" href="#">View All<i
                                                                        class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                                            </div>
                                                            <div class="text-end">
                                                                <p class="mb-0 text-success fw-semibold">Received</p>
                                                                <span class="text-muted op-7 fs-11">produce</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Verified Produce Card -->
                                    <div class="col-xxl-3 col-lg-6 col-md-6">
                                        <div class="card custom-card overflow-hidden">
                                            <div class="card-body">
                                                <div class="d-flex align-items-top justify-content-between">
                                                    <div>
                                                        <span class="avatar avatar-md avatar-rounded bg-info">
                                                            <i class="ti ti-checkbox fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill ms-3">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between flex-wrap">
                                                            <div>
                                                                <p class="text-muted mb-0">Verified Produce</p>
                                                                <h4 class="fw-semibold mt-1">
                                                                    <?php 
                                            $verifiedProduce = $app->select_one("SELECT COUNT(*) as count FROM produce_deliveries WHERE status = 'verified'");
                                            echo number_format($verifiedProduce->count); 
                                        ?>
                                                                </h4>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="d-flex align-items-center justify-content-between mt-1">
                                                            <div>
                                                                <a class="text-info" href="#">View All<i
                                                                        class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                                            </div>
                                                            <div class="text-end">
                                                                <p class="mb-0 text-info fw-semibold">Quality Checked
                                                                </p>
                                                                <span class="text-muted op-7 fs-11">produce</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sold Produce Card -->
                                    <div class="col-xxl-3 col-lg-6 col-md-6">
                                        <div class="card custom-card overflow-hidden">
                                            <div class="card-body">
                                                <div class="d-flex align-items-top justify-content-between">
                                                    <div>
                                                        <span class="avatar avatar-md avatar-rounded bg-success">
                                                            <i class="ti ti-shopping-cart fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill ms-3">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between flex-wrap">
                                                            <div>
                                                                <p class="text-muted mb-0">Sold Produce</p>
                                                                <h4 class="fw-semibold mt-1">
                                                                    <?php 
                                            $soldProduce = $app->select_one("SELECT COUNT(*) as count FROM produce_deliveries WHERE status = 'sold'");
                                            echo number_format($soldProduce->count); 
                                        ?>
                                                                </h4>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="d-flex align-items-center justify-content-between mt-1">
                                                            <div>
                                                                <a class="text-success" href="#">View All<i
                                                                        class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                                            </div>
                                                            <div class="text-end">
                                                                <p class="mb-0 text-success fw-semibold">Transactions
                                                                </p>
                                                                <span class="text-muted op-7 fs-11">completed</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Total Loan Applications Card -->
                                    <div class="col-xxl-3 col-lg-6 col-md-6">
                                        <div class="card custom-card overflow-hidden">
                                            <div class="card-body">
                                                <div class="d-flex align-items-top justify-content-between">
                                                    <div>
                                                        <span class="avatar avatar-md avatar-rounded bg-warning">
                                                            <i class="ti ti-file-text fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill ms-3">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between flex-wrap">
                                                            <div>
                                                                <p class="text-muted mb-0">Total Loan Applications</p>
                                                                <h4 class="fw-semibold mt-1">
                                                                    <?php 
                                            $totalLoanApplications = $app->select_one("SELECT COUNT(*) as count FROM loan_applications");
                                            echo number_format($totalLoanApplications->count); 
                                        ?>
                                                                </h4>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="d-flex align-items-center justify-content-between mt-1">
                                                            <div>
                                                                <a class="text-warning" href="#">View All<i
                                                                        class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                                            </div>
                                                            <div class="text-end">
                                                                <p class="mb-0 text-warning fw-semibold">Submitted</p>
                                                                <span class="text-muted op-7 fs-11">loans</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Approved Loans Card -->
                                    <div class="col-xxl-3 col-lg-6 col-md-6">
                                        <div class="card custom-card overflow-hidden">
                                            <div class="card-body">
                                                <div class="d-flex align-items-top justify-content-between">
                                                    <div>
                                                        <span class="avatar avatar-md avatar-rounded bg-danger">
                                                            <i class="ti ti-check fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill ms-3">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between flex-wrap">
                                                            <div>
                                                                <p class="text-muted mb-0">Approved Loans</p>
                                                                <h4 class="fw-semibold mt-1">
                                                                    <?php 
                                            $approvedLoans = $app->select_one("SELECT COUNT(*) as count FROM approved_loans");
                                            echo number_format($approvedLoans->count); 
                                        ?>
                                                                </h4>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="d-flex align-items-center justify-content-between mt-1">
                                                            <div>
                                                                <a class="text-danger" href="#">View All<i
                                                                        class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                                            </div>
                                                            <div class="text-end">
                                                                <p class="mb-0 text-danger fw-semibold">Processed</p>
                                                                <span class="text-muted op-7 fs-11">loans</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- System Activity Card -->
                                    <div class="col-xxl-3 col-lg-6 col-md-6">
                                        <div class="card custom-card overflow-hidden">
                                            <div class="card-body">
                                                <div class="d-flex align-items-top justify-content-between">
                                                    <div>
                                                        <span class="avatar avatar-md avatar-rounded bg-purple">
                                                            <i class="ti ti-activity fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill ms-3">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between flex-wrap">
                                                            <div>
                                                                <p class="text-muted mb-0">Today's Activities</p>
                                                                <h4 class="fw-semibold mt-1">
                                                                    <?php 
                                            $todayActivities = $app->select_one("SELECT COUNT(*) as count FROM activity_logs WHERE DATE(created_at) = CURDATE()");
                                            echo number_format($todayActivities->count); 
                                        ?>
                                                                </h4>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="d-flex align-items-center justify-content-between mt-1">
                                                            <div>
                                                                <a class="text-purple" href="#">View All<i
                                                                        class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                                            </div>
                                                            <div class="text-end">
                                                                <p class="mb-0 text-purple fw-semibold">Activities</p>
                                                                <span class="text-muted op-7 fs-11">today</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <!-- User Distribution Graph -->
                                <div class="col-xl-8">
                                    <div class="card custom-card">
                                        <div class="card-header justify-content-between">
                                            <div class="card-title">Product Distribution Deliveries</div>
                                            <div class="dropdown">
                                                <a href="javascript:void(0);" class="p-2 fs-12 text-muted"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    View All<i
                                                        class="ri-arrow-down-s-line align-middle ms-1 d-inline-block"></i>
                                                </a>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="javascript:void(0);">Monthly</a>
                                                    </li>
                                                    <li><a class="dropdown-item" href="javascript:void(0);">Yearly</a>
                                                    </li>
                                                    <li><a class="dropdown-item" href="javascript:void(0);">Weekly</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <?php include "graphs/product-distribution.php" ?>
                                        </div>
                                    </div>
                                </div>



                                <!-- Produce Statistics Card -->
                                <div class="col-xl-4">
                                    <div class="card custom-card">
                                        <div class="card-header">
                                            <div class="card-title">Produce Statistics</div>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="list-group list-group-flush">
                                                <!-- Produce Delivered -->
                                                <div class="list-group-item">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2">
                                                            <span class="avatar avatar-sm avatar-rounded bg-primary">
                                                                <i class="ti ti-truck-delivery fs-16"></i>
                                                            </span>
                                                        </div>
                                                        <div class="flex-fill">
                                                            <p class="mb-0">Produce Delivered</p>
                                                            <span class="text-muted fs-12">
                                                                <?php 
                                                                   $deliveredProduceQuery = "SELECT COUNT(*) as count, SUM(quantity) as total_kg FROM produce_deliveries";
                                                                   $deliveredProduce = $app->select_one($deliveredProduceQuery);
                                                                   echo number_format($deliveredProduce->count) . ' Deliveries (' . number_format($deliveredProduce->total_kg, 2) . ' KGs)';
                                                                   ?>
                                                            </span>
                                                        </div>
                                                        <div class="text-end">
                                                            <span class="badge bg-primary-transparent">
                                                                <?php 
                                                              $recentDeliveriesQuery = "SELECT COUNT(*) as count FROM produce_deliveries 
                                                                                       WHERE delivery_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                                                              $recentDeliveries = $app->select_one($recentDeliveriesQuery);
                                                              echo number_format($recentDeliveries->count) . ' This Week';
                                                              ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="progress mt-2" style="height: 5px;">
                                                        <div class="progress-bar bg-primary" role="progressbar"
                                                            style="width: 100%" aria-valuenow="100" aria-valuemin="0"
                                                            aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Verified Produce -->
                                                <div class="list-group-item">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2">
                                                            <span class="avatar avatar-sm avatar-rounded bg-success">
                                                                <i class="ti ti-checkbox fs-16"></i>
                                                            </span>
                                                        </div>
                                                        <div class="flex-fill">
                                                            <p class="mb-0">Verified Produce</p>
                                                            <span class="text-muted fs-12">
                                                                <?php 
                                                                    $verifiedProduceQuery = "SELECT COUNT(*) as count, SUM(quantity) as total_kg FROM produce_deliveries WHERE status = 'verified'";
                                                                    $verifiedProduce = $app->select_one($verifiedProduceQuery);
                                                                    echo number_format($verifiedProduce->count) . ' Verified (' . number_format($verifiedProduce->total_kg, 2) . ' KGs)';
                                                                    ?>
                                                            </span>
                                                        </div>
                                                        <div class="text-end">
                                                            <span class="badge bg-success-transparent">
                                                                <?php 
                                                                 $percentVerified = ($deliveredProduce->count > 0) ? 
                                                                     round(($verifiedProduce->count / $deliveredProduce->count) * 100) : 0;
                                                                 echo $percentVerified . '% Rate';
                                                                 ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="progress mt-2" style="height: 5px;">
                                                        <div class="progress-bar bg-success" role="progressbar"
                                                            style="width: <?php echo $percentVerified; ?>%"
                                                            aria-valuenow="<?php echo $percentVerified; ?>"
                                                            aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Sold Produce -->
                                                <div class="list-group-item">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2">
                                                            <span class="avatar avatar-sm avatar-rounded bg-warning">
                                                                <i class="ti ti-shopping-cart fs-16"></i>
                                                            </span>
                                                        </div>
                                                        <div class="flex-fill">
                                                            <p class="mb-0">Sold Produce</p>
                                                            <span class="text-muted fs-12">
                                                                <?php 
                                                                $soldProduceQuery = "SELECT COUNT(*) as count, SUM(total_value) as total_value FROM produce_deliveries WHERE status = 'sold'";
                                                                $soldProduce = $app->select_one($soldProduceQuery);
                                                                echo number_format($soldProduce->count) . ' Sales (KES ' . number_format($soldProduce->total_value, 2) . ')';
                                                                ?>
                                                            </span>
                                                        </div>
                                                        <div class="text-end">
                                                            <span class="badge bg-warning-transparent">
                                                                <?php 
                                                                  $percentSold = ($deliveredProduce->count > 0) ? 
                                                                      round(($soldProduce->count / $deliveredProduce->count) * 100) : 0;
                                                                  echo $percentSold . '% Sold';
                                                                  ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="progress mt-2" style="height: 5px;">
                                                        <div class="progress-bar bg-warning" role="progressbar"
                                                            style="width: <?php echo $percentSold; ?>%"
                                                            aria-valuenow="<?php echo $percentSold; ?>"
                                                            aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Commission Earned -->
                                                <div class="list-group-item bg-light">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2">
                                                            <span class="avatar avatar-sm avatar-rounded bg-info">
                                                                <i class="ti ti-currency-dollar fs-16"></i>
                                                            </span>
                                                        </div>
                                                        <div class="flex-fill">
                                                            <p class="mb-0 fw-semibold">Commission Earned</p>
                                                            <span class="text-muted fs-12">
                                                                <?php 
                                                                     $commissionQuery = "SELECT SUM(amount) as total FROM sacco_account_transactions WHERE transaction_type = 'credit'";
                                                                     $commission = $app->select_one($commissionQuery);
                                                                     echo 'KES ' . number_format($commission->total, 2) . ' Total Earned';
                                                                     ?>
                                                            </span>
                                                        </div>
                                                        <div class="text-end">
                                                            <span class="badge bg-info-transparent">
                                                                <?php 
                                                           // Calculate this month's commission
                                                           $monthlyCommissionQuery = "SELECT SUM(amount) as total FROM sacco_account_transactions 
                                                                                    WHERE transaction_type = 'credit'
                                                                                    AND MONTH(created_at) = MONTH(CURRENT_DATE())
                                                                                    AND YEAR(created_at) = YEAR(CURRENT_DATE())";
                                                           $monthlyCommission = $app->select_one($monthlyCommissionQuery);
                                                           echo 'KES ' . number_format($monthlyCommission->total, 2) . ' This Month';
                                                           ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="progress mt-2" style="height: 5px;">
                                                        <div class="progress-bar bg-info" role="progressbar"
                                                            style="width: 100%" aria-valuenow="100" aria-valuemin="0"
                                                            aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <!-- Product Type Distribution Card -->
                                    <div class="col-xl-12">
                                        <div class="card custom-card">
                                            <div class="card-header">
                                                <div class="card-title">Product Type Distribution</div>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="list-group list-group-flush">
                                                    <?php
                                                           // Get top 5 product types by delivery count
                                                           $productTypesQuery = "SELECT 
                                                                                  pt.name, 
                                                                                  COUNT(pd.id) as delivery_count,
                                                                                  SUM(pd.quantity) as total_kg,
                                                                                  SUM(pd.total_value) as total_value
                                                                               FROM produce_deliveries pd
                                                                               JOIN farm_products fp ON pd.farm_product_id = fp.id
                                                                               JOIN product_types pt ON fp.product_type_id = pt.id
                                                                               GROUP BY pt.id
                                                                               ORDER BY delivery_count DESC
                                                                               LIMIT 5";
                                                           $productTypes = $app->select_all($productTypesQuery);
                                                           
                                                           // Get total count for percentage calculation
                                                           $totalDeliveriesQuery = "SELECT COUNT(*) as total FROM produce_deliveries";
                                                           $totalDeliveries = $app->select_one($totalDeliveriesQuery);
                                                           $total = $totalDeliveries->total;
                                                           
                                                           // Array of colors for different product types
                                                           $colors = ['primary', 'success', 'warning', 'danger', 'info', 'purple'];
                                                           
                                                           $counter = 0;
                                                           foreach ($productTypes as $product) {
                                                               $percentage = ($total > 0) ? round(($product->delivery_count / $total) * 100) : 0;
                                                               $color = $colors[$counter % count($colors)];
                                                               ?>
                                                    <?php echo htmlspecialchars($product->name); ?>
                                                    <div class="list-group-item">
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-2">
                                                                <span
                                                                    class="avatar avatar-sm avatar-rounded bg-<?php echo $color; ?>">
                                                                    <i class="ti ti-plant-2 fs-16"></i>
                                                                </span>
                                                            </div>
                                                            <div class="flex-fill">
                                                                <p class="mb-0">
                                                                    <?php echo htmlspecialchars($product->name); ?></p>
                                                                <span class="text-muted fs-12">
                                                                    <?php echo number_format($product->delivery_count); ?>
                                                                    Deliveries
                                                                    (<?php echo number_format($product->total_kg, 2); ?>
                                                                    KGs)
                                                                </span>
                                                            </div>
                                                            <div class="text-end">
                                                                <span
                                                                    class="badge bg-<?php echo $color; ?>-transparent">
                                                                    KES
                                                                    <?php echo number_format($product->total_value, 2); ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="progress mt-2" style="height: 5px;">
                                                            <div class="progress-bar bg-<?php echo $color; ?>"
                                                                role="progressbar"
                                                                style="width: <?php echo $percentage; ?>%"
                                                                aria-valuenow="<?php echo $percentage; ?>"
                                                                aria-valuemin="0" aria-valuemax="100">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                      $counter++;
                                                  }
                                                  
                                                  // If there are no product types, display a message
                                                  if (empty($productTypes)) {
                                                       ?>
                                                    <div class="list-group-item">
                                                        <div class="text-center">
                                                            <p class="text-muted mb-0">No produce data available</p>
                                                        </div>
                                                    </div>
                                                    <?php
                                                           }
                                                           ?>

                                                    <!-- Total Value Summary -->
                                                    <div class="list-group-item bg-light">
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-2">
                                                                <span
                                                                    class="avatar avatar-sm avatar-rounded bg-secondary">
                                                                    <i class="ti ti-chart-pie fs-16"></i>
                                                                </span>
                                                            </div>
                                                            <div class="flex-fill">
                                                                <p class="mb-0 fw-semibold">Total Produce Value</p>
                                                                <span class="text-muted fs-12">
                                                                    <?php 
                                                                     $totalValueQuery = "SELECT SUM(total_value) as value FROM produce_deliveries";
                                                                     $totalValue = $app->select_one($totalValueQuery);
                                                                     echo 'KES ' . number_format($totalValue->value, 2) . ' Across All Types';
                                                                     ?>
                                                                </span>
                                                            </div>
                                                            <div class="text-end">
                                                                <span class="badge bg-secondary-transparent">
                                                                    <?php echo number_format($total); ?> Deliveries
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
                    <div class="row mt-4">
                        <div class="col-xl-12">
                            <div class="card custom-card">
                                <div class="card-header justify-content-between">
                                    <div class="card-title">Loan Activity Analysis</div>
                                    <div class="dropdown">
                                        <a href="javascript:void(0);" class="p-2 fs-12 text-muted"
                                            data-bs-toggle="dropdown">
                                            View All<i
                                                class="ri-arrow-down-s-line align-middle ms-1 d-inline-block"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Loan Activity Summary Cards -->
                                    <div class="row g-3 mb-4">
                                        <!-- Loan Applications Card -->
                                        <div class="col-xl-3 col-lg-6">
                                            <div class="p-3 border rounded-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <span class="avatar avatar-sm avatar-rounded bg-primary">
                                                            <i class="ti ti-file-text fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 text-muted fs-12">Loan Applications</p>
                                                        <h5 class="mb-0">
                                                            <?php
                                            $loanApps = $app->select_one("SELECT COUNT(*) as count FROM loan_applications 
                                                                        WHERE DATE(application_date) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
                                            echo number_format($loanApps->count ?? 0);
                                        ?>
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Approved Loans Card -->
                                        <div class="col-xl-3 col-lg-6">
                                            <div class="p-3 border rounded-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <span class="avatar avatar-sm avatar-rounded bg-success">
                                                            <i class="ti ti-check fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 text-muted fs-12">Approved Loans</p>
                                                        <h5 class="mb-0">
                                                            <?php
                                            $approvedLoans = $app->select_one("SELECT COUNT(*) as count FROM loan_applications 
                                                                            WHERE status = 'approved' 
                                                                            AND DATE(application_date) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
                                            echo number_format($approvedLoans->count ?? 0);
                                        ?>
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Rejected Loans Card -->
                                        <div class="col-xl-3 col-lg-6">
                                            <div class="p-3 border rounded-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <span class="avatar avatar-sm avatar-rounded bg-danger">
                                                            <i class="ti ti-x fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 text-muted fs-12">Rejected Loans</p>
                                                        <h5 class="mb-0">
                                                            <?php
                                            $rejectedLoans = $app->select_one("SELECT COUNT(*) as count FROM loan_applications 
                                                                            WHERE status = 'rejected' 
                                                                            AND DATE(application_date) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
                                            echo number_format($rejectedLoans->count ?? 0);
                                        ?>
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Disbursed Amount Card -->
                                        <div class="col-xl-3 col-lg-6">
                                            <div class="p-3 border rounded-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <span class="avatar avatar-sm avatar-rounded bg-info">
                                                            <i class="ti ti-cash fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 text-muted fs-12">Disbursed Amount</p>
                                                        <h5 class="mb-0">
                                                            <?php
                                            $disbursedAmount = $app->select_one("SELECT SUM(approved_amount) as total FROM approved_loans 
                                                                             WHERE disbursement_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
                                            echo 'KES ' . number_format($disbursedAmount->total ?? 0, 2);
                                        ?>
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Recent Loan Activity List -->
                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped align-middle">
                                            <thead>
                                                <tr>
                                                    <th>Farmer</th>
                                                    <th>Loan Type</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Application Date</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                $recentLoans = $app->select_all("SELECT la.*, u.first_name, u.last_name, lt.name as loan_type_name 
                                                              FROM loan_applications la 
                                                              JOIN farmers f ON la.farmer_id = f.id 
                                                              JOIN users u ON f.user_id = u.id 
                                                              JOIN loan_types lt ON la.loan_type_id = lt.id 
                                                              ORDER BY la.application_date DESC LIMIT 5");
                                if($recentLoans):
                                    foreach($recentLoans as $loan):
                            ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-2">
                                                                <span class="avatar avatar-sm avatar-rounded bg-light">
                                                                    <?php echo strtoupper(substr($loan->first_name, 0, 1)); ?>
                                                                </span>
                                                            </div>
                                                            <div>
                                                                <?php echo htmlspecialchars($loan->first_name . ' ' . $loan->last_name); ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($loan->loan_type_name); ?></td>
                                                    <td><strong>KES
                                                            <?php echo number_format($loan->amount_requested, 2); ?></strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?php 
                                        echo match($loan->status) {
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            'under_review' => 'warning',
                                            'disbursed' => 'info',
                                            'completed' => 'primary',
                                            'defaulted' => 'dark',
                                            default => 'secondary'
                                        };
                                    ?>-transparent">
                                                            <?php echo ucwords(str_replace('_', ' ', $loan->status)); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <small>
                                                            <?php echo $app->formatTimeAgo($loan->application_date); ?>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <div class="hstack gap-2 fs-15">
                                                            <a href="javascript:void(0);"
                                                                class="btn btn-icon btn-sm btn-light"><i
                                                                    class="ri-eye-line"></i></a>
                                                            <?php if($loan->status == 'under_review'): ?>
                                                            <a href="javascript:void(0);"
                                                                class="btn btn-icon btn-sm btn-success"><i
                                                                    class="ri-check-line"></i></a>
                                                            <a href="javascript:void(0);"
                                                                class="btn btn-icon btn-sm btn-danger"><i
                                                                    class="ri-close-line"></i></a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php 
                                    endforeach;
                                else:
                            ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">No recent loan applications
                                                        found</td>
                                                </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12">
                            <div class="card custom-card">
                                <div class="card-header justify-content-between">
                                    <div class="card-title">Loan Performance Analytics</div>
                                    <div class="d-flex gap-2">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-primary active">Monthly</button>
                                            <button type="button"
                                                class="btn btn-sm btn-outline-primary">Quarterly</button>
                                            <button type="button" class="btn btn-sm btn-outline-primary">Yearly</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Loan Summary Statistics -->
                                        <div class="col-xl-4">
                                            <div class="list-group">
                                                <div class="list-group-item border-0">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2">
                                                            <span class="avatar avatar-sm avatar-rounded bg-primary">
                                                                <i class="ti ti-chart-pie fs-16"></i>
                                                            </span>
                                                        </div>
                                                        <div class="flex-fill">
                                                            <p class="mb-0">Approval Rate</p>
                                                            <div
                                                                class="d-flex justify-content-between align-items-center">
                                                                <h5 class="mb-0">
                                                                    <?php
                                                                       $totalApps = $app->select_one("SELECT COUNT(*) as count FROM loan_applications 
                                                                                             WHERE DATE(application_date) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
                                                                       $approvedCount = $approvedLoans->count ?? 0;
                                                                       $totalCount = ($totalApps && isset($totalApps->count) && $totalApps->count > 0) ? $totalApps->count : 1; // Avoid division by zero
                                                                       $approvalRate = ($approvedCount / $totalCount) * 100;
                                                                       echo round($approvalRate) . '%';
                                                                   ?>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="progress mt-2" style="height: 5px;">
                                                        <div class="progress-bar bg-primary"
                                                            style="width: <?php echo $approvalRate; ?>%"
                                                            role="progressbar"></div>
                                                    </div>
                                                </div>
                                                <div class="list-group-item border-0">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2">
                                                            <span class="avatar avatar-sm avatar-rounded bg-success">
                                                                <i class="ti ti-currency-dollar fs-16"></i>
                                                            </span>
                                                        </div>
                                                        <div class="flex-fill">
                                                            <p class="mb-0">Average Loan Amount</p>
                                                            <div
                                                                class="d-flex justify-content-between align-items-center">
                                                                <h5 class="mb-0">
                                                                    <?php
                                                    $avgLoan = $app->select_one("SELECT AVG(amount_requested) as average FROM loan_applications 
                                                                            WHERE DATE(application_date) >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)");
                                                    echo 'KES ' . number_format($avgLoan->average ?? 0, 2);
                                                ?>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="list-group-item border-0">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2">
                                                            <span class="avatar avatar-sm avatar-rounded bg-warning">
                                                                <i class="ti ti-clock fs-16"></i>
                                                            </span>
                                                        </div>
                                                        <div class="flex-fill">
                                                            <p class="mb-0">Average Processing Time</p>
                                                            <div
                                                                class="d-flex justify-content-between align-items-center">
                                                                <h5 class="mb-0">
                                                                    <?php
                                                    // This would need a more complex query to calculate actual processing time
                                                    echo '2.4 Days';
                                                ?>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="list-group-item border-0">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2">
                                                            <span class="avatar avatar-sm avatar-rounded bg-danger">
                                                                <i class="ti ti-alert-triangle fs-16"></i>
                                                            </span>
                                                        </div>
                                                        <div class="flex-fill">
                                                            <p class="mb-0">Default Rate</p>
                                                            <div
                                                                class="d-flex justify-content-between align-items-center">
                                                                <h5 class="mb-0">
                                                                    <?php
                                                    $defaultedLoans = $app->select_one("SELECT COUNT(*) as count FROM approved_loans 
                                                                                    WHERE status = 'defaulted'");
                                                    $totalLoans = $app->select_one("SELECT COUNT(*) as count FROM approved_loans");
                                                    $defaultRate = ($totalLoans->count > 0) ? 
                                                        ($defaultedLoans->count / $totalLoans->count) * 100 : 0;
                                                    echo round($defaultRate, 1) . '%';
                                                ?>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="progress mt-2" style="height: 5px;">
                                                        <div class="progress-bar bg-danger"
                                                            style="width: <?php echo $defaultRate; ?>%"
                                                            role="progressbar"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Loan Chart Placeholder - Will be replaced with actual chart -->
                                        <div class="col-xl-8">
                                            <?php include "graphs/loan-analytics.php" ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-12 col-xl-12">
                        <div class="row">
                            <div class="col-xxl-12 col-xl-12">
                                <div class="row">

                                    <div class="col-xxl-12 col-xl-12">
                                        <div class="card custom-card">
                                            <div class="card-header justify-content-between">
                                                <div class="card-title">System Status Overview</div>

                                            </div>
                                            <div class="card-body">
                                                <?php
                                              // Get total users count
                                              $totalUsers = $app->select_one("SELECT COUNT(*) as count FROM users WHERE is_active = 1");
                                              
                                              // Get today's activity count
                                              $todayActivity = $app->select_one("SELECT COUNT(*) as count FROM activity_logs WHERE DATE(created_at) = CURDATE()");
                                              
                                              // Calculate percentage change from previous day
                                              $yesterdayActivity = $app->select_one("SELECT COUNT(*) as count FROM activity_logs WHERE DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)");
                                              $percentageChange = $yesterdayActivity->count > 0 ? 
                                                  (($todayActivity->count - $yesterdayActivity->count) / $yesterdayActivity->count) * 100 : 0;
                                              ?>

                                                <div class="d-flex align-items-center mb-3">
                                                    <h4 class="fw-bold mb-0">
                                                        <?php echo number_format($todayActivity->count); ?></h4>
                                                    <div class="ms-2">
                                                        <span
                                                            class="badge bg-<?php echo $percentageChange >= 0 ? 'success' : 'danger'; ?>-transparent">
                                                            <?php echo number_format(abs($percentageChange), 2); ?>%
                                                            <i
                                                                class="ri-arrow-<?php echo $percentageChange >= 0 ? 'up' : 'down'; ?>-s-fill align-middle ms-1"></i>
                                                        </span>
                                                        <span class="text-muted ms-1">compared to yesterday</span>
                                                    </div>
                                                </div>

                                                <?php
                                                    // Get component percentages
                                                    $createOps = $app->select_one("SELECT COUNT(*) as count FROM audit_logs WHERE action_type = 'create' AND DATE(created_at) = CURDATE()");
                                                    $updateOps = $app->select_one("SELECT COUNT(*) as count FROM audit_logs WHERE action_type = 'update' AND DATE(created_at) = CURDATE()");
                                                    $deleteOps = $app->select_one("SELECT COUNT(*) as count FROM audit_logs WHERE action_type = 'delete' AND DATE(created_at) = CURDATE()");
                                                    $loginOps = $app->select_one("SELECT COUNT(*) as count FROM activity_logs WHERE activity_type = 'login' AND DATE(created_at) = CURDATE()");
                                                    
                                                    $total = $createOps->count + $updateOps->count + $deleteOps->count + $loginOps->count;
                                                    $createPercent = $total > 0 ? ($createOps->count / $total) * 100 : 0;
                                                    $updatePercent = $total > 0 ? ($updateOps->count / $total) * 100 : 0;
                                                    $deletePercent = $total > 0 ? ($deleteOps->count / $total) * 100 : 0;
                                                    $loginPercent = $total > 0 ? ($loginOps->count / $total) * 100 : 0;
                                                    ?>

                                                <div class="progress-stacked progress-animate progress-xs mb-4">
                                                    <div class="progress-bar" role="progressbar"
                                                        style="width: <?php echo $createPercent; ?>%"
                                                        aria-valuenow="<?php echo $createPercent; ?>" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                    <div class="progress-bar bg-info" role="progressbar"
                                                        style="width: <?php echo $updatePercent; ?>%"
                                                        aria-valuenow="<?php echo $updatePercent; ?>" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                    <div class="progress-bar bg-warning" role="progressbar"
                                                        style="width: <?php echo $deletePercent; ?>%"
                                                        aria-valuenow="<?php echo $deletePercent; ?>" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                    <div class="progress-bar bg-success" role="progressbar"
                                                        style="width: <?php echo $loginPercent; ?>%"
                                                        aria-valuenow="<?php echo $loginPercent; ?>" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>

                                                <ul class="list-unstyled mb-0 pt-2 crm-deals-status">
                                                    <li class="primary">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <div>Create Operations</div>
                                                            <div class="fs-12 text-muted">
                                                                <?php echo number_format($createOps->count); ?>
                                                                operations</div>
                                                        </div>
                                                    </li>
                                                    <li class="info">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <div>Update Operations</div>
                                                            <div class="fs-12 text-muted">
                                                                <?php echo number_format($updateOps->count); ?>
                                                                operations</div>
                                                        </div>
                                                    </li>
                                                    <li class="warning">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <div>Delete Operations</div>
                                                            <div class="fs-12 text-muted">
                                                                <?php echo number_format($deleteOps->count); ?>
                                                                operations</div>
                                                        </div>
                                                    </li>
                                                    <li class="success">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <div>Login Activities</div>
                                                            <div class="fs-12 text-muted">
                                                                <?php echo number_format($loginOps->count); ?>
                                                                activities</div>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xxl-12 col-xl-12">
                                        <div class="card custom-card">
                                            <div class="card-header justify-content-between">
                                                <div class="card-title">Recent System Activities</div>

                                            </div>
                                            <div class="card-body">
                                                <div>
                                                    <ul class="list-unstyled mb-0 crm-recent-activity">
                                                        <?php
                                                              $recentActivities = $app->select_all("
                                                                  SELECT al.*, u.first_name, u.last_name, u.email 
                                                                  FROM activity_logs al 
                                                                  JOIN users u ON al.user_id = u.id 
                                                                  ORDER BY al.created_at DESC 
                                                                  LIMIT 10
                                                              ");
                                          
                                                              if($recentActivities):
                                                                  foreach($recentActivities as $activity):
                                                                      $activityColor = match($activity->activity_type) {
                                                                          'login' => 'primary',
                                                                          'registration' => 'success',
                                                                          'profile_update' => 'warning',
                                                                          'bank_added' => 'info',
                                                                          'agrovet_added' => 'purple',
                                                                          default => 'secondary'
                                                                      };
                                                              ?>
                                                        <li class="crm-recent-activity-content">
                                                            <div class="d-flex align-items-top">
                                                                <div class="me-3">
                                                                    <span
                                                                        class="avatar avatar-xs bg-<?php echo $activityColor; ?>-transparent avatar-rounded">
                                                                        <i class="bi bi-circle-fill fs-8"></i>
                                                                    </span>
                                                                </div>
                                                                <div class="crm-timeline-content">
                                                                    <span
                                                                        class="fw-semibold"><?php echo htmlspecialchars($activity->description); ?></span>
                                                                    <span class="d-block text-muted fs-11">
                                                                        by
                                                                        <?php echo htmlspecialchars($activity->first_name . ' ' . $activity->last_name); ?>
                                                                    </span>
                                                                </div>
                                                                <div class="flex-fill text-end">
                                                                    <span class="d-block text-muted fs-11 op-7">
                                                                        <?php echo $app->formatTimeAgo($activity->created_at); ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <?php 
                                                                     endforeach;
                                                                 else:
                                                                 ?>
                                                        <li class="text-center text-muted">No recent activities found
                                                        </li>
                                                        <?php endif; ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End::row-1 -->
            </div>
        </div>
        <!-- End::app-content -->


        <!-- Footer Start -->
        <?php include "../../includes/footer.php" ?>
        <!-- Footer End -->
    </div>

    <!-- Scroll To Top -->
    <div class="scrollToTop">
        <span class="arrow"><i class="ri-arrow-up-s-fill fs-20"></i></span>
    </div>
    <div id="responsive-overlay"></div>
    <!-- Scroll To Top -->

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

    <!-- JSVector Maps JS -->
    <script src="http://localhost/dfcs/assets/libs/jsvectormap/js/jsvectormap.min.js"></script>

    <!-- JSVector Maps MapsJS -->
    <script src="http://localhost/dfcs/assets/libs/jsvectormap/maps/world-merc.js"></script>

    <!-- Apex Charts JS -->
    <script src="http://localhost/dfcs/assets/libs/apexcharts/apexcharts.min.js"></script>

    <!-- Chartjs Chart JS -->
    <script src="http://localhost/dfcs/assets/libs/chart.js/chart.min.js"></script>

    <!-- CRM-Dashboard -->
    <script src="http://localhost/dfcs/assets/js/crm-dashboard.js"></script>

    <!-- Custom JS -->
    <script src="http://localhost/dfcs/assets/js/custom.js"></script>

    <!-- Custom-Switcher JS -->
    <script src="http://localhost/dfcs/assets/js/custom-switcher.min.js"></script>
</body>

</html>