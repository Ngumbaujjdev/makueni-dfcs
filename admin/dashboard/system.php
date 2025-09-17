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
                                   
                                   if ($_SESSION['role_id'] == 5) {
                                       // Admin welcome message
                                       ?>
                        <p class="fw-semibold fs-18 mb-0">Welcome back,
                            <?php echo htmlspecialchars($user->first_name . ' ' . $user->last_name); ?>!</p>
                        <span class="fs-semibold text-muted">System Administration Dashboard - Monitor and manage system
                            activities</span>
                        <?php
                                   } else {
                                       // Staff welcome message
                                       ?>
                        <p class="fw-semibold fs-18 mb-0">Welcome back,
                            <?php echo htmlspecialchars($user->first_name . ' ' . $user->last_name); ?>!</p>
                        <span class="fs-semibold text-muted">Staff Dashboard - Track your activities and manage
                            resources</span>
                        <?php
                                   }
                               }
                               ?>
                    </div>
                </div>
                <!-- Start::row-1 -->
                <div class="row">
                    <div class="col-xxl-12 col-xl-12">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="row">
                                    <!-- Total Users Card -->
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
                                                                <p class="text-muted mb-0">Total Users</p>
                                                                <h4 class="fw-semibold mt-1">
                                                                    <?php 
                                                                      $totalUsers = $app->select_one("SELECT COUNT(*) as count FROM users WHERE is_active = 1");
                                                                      echo number_format($totalUsers->count); 
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
                                                                <p class="mb-0 text-success fw-semibold">Active Users
                                                                </p>
                                                                <span class="text-muted op-7 fs-11">system wide</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Total Banks Card -->
                                    <div class="col-xxl-3 col-lg-6 col-md-6">
                                        <div class="card custom-card overflow-hidden">
                                            <div class="card-body">
                                                <div class="d-flex align-items-top justify-content-between">
                                                    <div>
                                                        <span class="avatar avatar-md avatar-rounded bg-secondary">
                                                            <i class="ti ti-building-bank fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill ms-3">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between flex-wrap">
                                                            <div>
                                                                <p class="text-muted mb-0">Total Banks</p>
                                                                <h4 class="fw-semibold mt-1">
                                                                    <?php 
                                                                    $totalBanks = $app->select_one("SELECT COUNT(*) as count FROM banks");
                                                                    echo number_format($totalBanks->count); 
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
                                                                <p class="mb-0 text-success fw-semibold">Registered</p>
                                                                <span class="text-muted op-7 fs-11">institutions</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Bank Staff Card -->
                                    <div class="col-xxl-3 col-lg-6 col-md-6">
                                        <div class="card custom-card overflow-hidden">
                                            <div class="card-body">
                                                <div class="d-flex align-items-top justify-content-between">
                                                    <div>
                                                        <span class="avatar avatar-md avatar-rounded bg-info">
                                                            <i class="ti ti-user-check fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill ms-3">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between flex-wrap">
                                                            <div>
                                                                <p class="text-muted mb-0">Bank Staff</p>
                                                                <h4 class="fw-semibold mt-1">
                                                                    <?php 
                                                                  $bankStaff = $app->select_one("SELECT COUNT(*) as count FROM bank_staff");
                                                                  echo number_format($bankStaff->count); 
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
                                                                <p class="mb-0 text-info fw-semibold">Active Staff</p>
                                                                <span class="text-muted op-7 fs-11">bank
                                                                    employees</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Total Agrovets Card -->
                                    <div class="col-xxl-3 col-lg-6 col-md-6">
                                        <div class="card custom-card overflow-hidden">
                                            <div class="card-body">
                                                <div class="d-flex align-items-top justify-content-between">
                                                    <div>
                                                        <span class="avatar avatar-md avatar-rounded bg-success">
                                                            <i class="ti ti-building-store fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill ms-3">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between flex-wrap">
                                                            <div>
                                                                <p class="text-muted mb-0">Total Agrovets</p>
                                                                <h4 class="fw-semibold mt-1">
                                                                    <?php 
                                                                    $totalAgrovets = $app->select_one("SELECT COUNT(*) as count FROM agrovets WHERE is_active = 1");
                                                                    echo number_format($totalAgrovets->count); 
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
                                                                <p class="mb-0 text-success fw-semibold">Active Stores
                                                                </p>
                                                                <span class="text-muted op-7 fs-11">registered</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Agrovet Staff Card -->
                                    <div class="col-xxl-3 col-lg-6 col-md-6">
                                        <div class="card custom-card overflow-hidden">
                                            <div class="card-body">
                                                <div class="d-flex align-items-top justify-content-between">
                                                    <div>
                                                        <span class="avatar avatar-md avatar-rounded bg-warning">
                                                            <i class="ti ti-users-group fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill ms-3">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between flex-wrap">
                                                            <div>
                                                                <p class="text-muted mb-0">Agrovet Staff</p>
                                                                <h4 class="fw-semibold mt-1">
                                                                    <?php 
                                                                      $agrovetStaff = $app->select_one("SELECT COUNT(*) as count FROM agrovet_staff WHERE is_active = 1");
                                                                      echo number_format($agrovetStaff->count); 
                                                                      ?>
                                                                </h4>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="d-flex align-items-center justify-content-between mt-1">
                                                            <div>
                                                                <a class="text-warning" href="agrovet-staff.php">View
                                                                    All<i
                                                                        class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                                            </div>
                                                            <div class="text-end">
                                                                <p class="mb-0 text-warning fw-semibold">Active Staff
                                                                </p>
                                                                <span class="text-muted op-7 fs-11">agrovet
                                                                    employees</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Total Farmers Card -->
                                    <div class="col-xxl-3 col-lg-6 col-md-6">
                                        <div class="card custom-card overflow-hidden">
                                            <div class="card-body">
                                                <div class="d-flex align-items-top justify-content-between">
                                                    <div>
                                                        <span class="avatar avatar-md avatar-rounded bg-danger">
                                                            <i class="ti ti-tractor fs-16"></i>
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
                                                                <a class="text-danger" href="farmers.php">View All<i
                                                                        class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                                            </div>
                                                            <div class="text-end">
                                                                <p class="mb-0 text-danger fw-semibold">Registered</p>
                                                                <span class="text-muted op-7 fs-11">farmers</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Verified Farmers Card -->
                                    <div class="col-xxl-3 col-lg-6 col-md-6">
                                        <div class="card custom-card overflow-hidden">
                                            <div class="card-body">
                                                <div class="d-flex align-items-top justify-content-between">
                                                    <div>
                                                        <span class="avatar avatar-md avatar-rounded bg-purple">
                                                            <i class="ti ti-user-check fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill ms-3">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between flex-wrap">
                                                            <div>
                                                                <p class="text-muted mb-0">Verified Farmers</p>
                                                                <h4 class="fw-semibold mt-1">
                                                                    <?php 
                                                                    $verifiedFarmers = $app->select_one("SELECT COUNT(*) as count FROM farmers WHERE is_verified = 1");
                                                                    echo number_format($verifiedFarmers->count); 
                                                                    ?>
                                                                </h4>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="d-flex align-items-center justify-content-between mt-1">
                                                            <div>
                                                                <a class="text-purple" href="#">View
                                                                    All<i
                                                                        class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                                            </div>
                                                            <div class="text-end">
                                                                <p class="mb-0 text-purple fw-semibold">Verified</p>
                                                                <span class="text-muted op-7 fs-11">active
                                                                    farmers</span>
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
                                                        <span class="avatar avatar-md avatar-rounded bg-pink">
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
                                                                <a class="text-pink" href="#">View All<i
                                                                        class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                                            </div>
                                                            <div class="text-end">
                                                                <p class="mb-0 text-pink fw-semibold">Activities</p>
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
                                            <div class="card-title">User Distribution Analytics</div>
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
                                            <?php include "graphs/user-distribution.php" ?>
                                        </div>
                                    </div>
                                </div>



                                <!-- Staff Distribution Card -->
                                <div class="col-xl-4">
                                    <div class="card custom-card">
                                        <div class="card-header">
                                            <div class="card-title">Staff Distribution</div>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="list-group list-group-flush">
                                                <!-- Bank Staff -->
                                                <div class="list-group-item">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2">
                                                            <span class="avatar avatar-sm avatar-rounded bg-primary">
                                                                <i class="ti ti-building-bank fs-16"></i>
                                                            </span>
                                                        </div>
                                                        <div class="flex-fill">
                                                            <p class="mb-0">Bank Staff</p>
                                                            <span class="text-muted fs-12">
                                                                <?php 
                                                             $bankStaffQuery = "SELECT COUNT(*) as staff_count FROM bank_staff WHERE user_id IS NOT NULL";
                                                             $bankStaff = $app->select_one($bankStaffQuery);
                                                             echo $bankStaff ? number_format($bankStaff->staff_count) : '0';
                                                             ?> Members
                                                            </span>
                                                        </div>
                                                        <div class="text-end">
                                                            <span class="badge bg-primary-transparent">
                                                                <?php 
                                                                    $activeBankStaffQuery = "SELECT COUNT(*) as active_count 
                                                                                            FROM bank_staff bs 
                                                                                            JOIN users u ON bs.user_id = u.id 
                                                                                            WHERE u.is_active = 1";
                                                                    $activeBankStaff = $app->select_one($activeBankStaffQuery);
                                                                    echo $activeBankStaff ? number_format($activeBankStaff->active_count) : '0';
                                                                    ?> Active
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- SACCO Staff -->
                                                <div class="list-group-item">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2">
                                                            <span class="avatar avatar-sm avatar-rounded bg-success">
                                                                <i class="ti ti-building fs-16"></i>
                                                            </span>
                                                        </div>
                                                        <div class="flex-fill">
                                                            <p class="mb-0">SACCO Staff</p>
                                                            <span class="text-muted fs-12">
                                                                <?php 
                                                                  $saccoStaffQuery = "SELECT COUNT(*) as staff_count FROM sacco_staff WHERE user_id IS NOT NULL";
                                                                  $saccoStaff = $app->select_one($saccoStaffQuery);
                                                                  echo $saccoStaff ? number_format($saccoStaff->staff_count) : '0';
                                                                  ?> Members
                                                            </span>
                                                        </div>
                                                        <div class="text-end">
                                                            <span class="badge bg-success-transparent">
                                                                <?php 
                                                                  $activeSaccoStaffQuery = "SELECT COUNT(*) as active_count 
                                                                                           FROM sacco_staff ss 
                                                                                           JOIN users u ON ss.user_id = u.id 
                                                                                           WHERE u.is_active = 1";
                                                                  $activeSaccoStaff = $app->select_one($activeSaccoStaffQuery);
                                                                  echo $activeSaccoStaff ? number_format($activeSaccoStaff->active_count) : '0';
                                                                  ?> Active
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Agrovet Staff -->
                                                <div class="list-group-item">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2">
                                                            <span class="avatar avatar-sm avatar-rounded bg-warning">
                                                                <i class="ti ti-building-store fs-16"></i>
                                                            </span>
                                                        </div>
                                                        <div class="flex-fill">
                                                            <p class="mb-0">Agrovet Staff</p>
                                                            <span class="text-muted fs-12">
                                                                <?php 
                                                                    $agrovetStaffQuery = "SELECT COUNT(*) as staff_count FROM agrovet_staff WHERE user_id IS NOT NULL";
                                                                    $agrovetStaff = $app->select_one($agrovetStaffQuery);
                                                                    echo $agrovetStaff ? number_format($agrovetStaff->staff_count) : '0';
                                                                    ?> Members
                                                            </span>
                                                        </div>
                                                        <div class="text-end">
                                                            <span class="badge bg-warning-transparent">
                                                                <?php 
                                                                       $activeAgrovetStaffQuery = "SELECT COUNT(*) as active_count 
                                                                                                 FROM agrovet_staff ags 
                                                                                                 JOIN users u ON ags.user_id = u.id 
                                                                                                 WHERE ags.is_active = 1";
                                                                       $activeAgrovetStaff = $app->select_one($activeAgrovetStaffQuery);
                                                                       echo $activeAgrovetStaff ? number_format($activeAgrovetStaff->active_count) : '0';
                                                                       ?> Active
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Total Distribution -->
                                                <div class="list-group-item bg-light">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2">
                                                            <span class="avatar avatar-sm avatar-rounded bg-info">
                                                                <i class="ti ti-users fs-16"></i>
                                                            </span>
                                                        </div>
                                                        <div class="flex-fill">
                                                            <p class="mb-0 fw-semibold">Total Staff</p>
                                                            <span class="text-muted fs-12">
                                                                <?php 
                                                                $totalStaff = ($bankStaff ? $bankStaff->staff_count : 0) + 
                                                                             ($saccoStaff ? $saccoStaff->staff_count : 0) + 
                                                                             ($agrovetStaff ? $agrovetStaff->staff_count : 0);
                                                                echo number_format($totalStaff);
                                                                ?> Total Members
                                                            </span>
                                                        </div>
                                                        <div class="text-end">
                                                            <span class="badge bg-info-transparent">
                                                                <?php 
                                                               $totalActive = ($activeBankStaff ? $activeBankStaff->active_count : 0) + 
                                                                            ($activeSaccoStaff ? $activeSaccoStaff->active_count : 0) + 
                                                                            ($activeAgrovetStaff ? $activeAgrovetStaff->active_count : 0);
                                                               echo number_format($totalActive);
                                                               ?> Active
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
                    <div class="row mt-4">
                        <div class="col-xl-12">
                            <div class="card custom-card">
                                <div class="card-header justify-content-between">
                                    <div class="card-title">System Activity Analysis</div>
                                    <div class="dropdown">
                                        <a href="javascript:void(0);" class="p-2 fs-12 text-muted"
                                            data-bs-toggle="dropdown">
                                            View All<i
                                                class="ri-arrow-down-s-line align-middle ms-1 d-inline-block"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Activity Type Summary Cards -->
                                    <div class="row g-3 mb-4">
                                        <!-- Create Operations Card -->
                                        <div class="col-xl-3 col-lg-6">
                                            <div class="p-3 border rounded-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <span class="avatar avatar-sm avatar-rounded bg-success">
                                                            <i class="ti ti-plus fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 text-muted fs-12">Create Operations</p>
                                                        <h5 class="mb-0">
                                                            <?php
                                                                 $createOps = $app->select_one("SELECT COUNT(*) as count FROM audit_logs 
                                                                                               WHERE action_type = 'create' 
                                                                                               AND DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
                                                                 echo number_format($createOps->count ?? 0);
                                                                 ?>
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Update Operations Card -->
                                        <div class="col-xl-3 col-lg-6">
                                            <div class="p-3 border rounded-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <span class="avatar avatar-sm avatar-rounded bg-warning">
                                                            <i class="ti ti-pencil fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 text-muted fs-12">Update Operations</p>
                                                        <h5 class="mb-0">
                                                            <?php
                                                               $updateOps = $app->select_one("SELECT COUNT(*) as count FROM audit_logs 
                                                                                             WHERE action_type = 'update' 
                                                                                             AND DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
                                                               echo number_format($updateOps->count ?? 0);
                                                               ?>
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Delete Operations Card -->
                                        <div class="col-xl-3 col-lg-6">
                                            <div class="p-3 border rounded-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <span class="avatar avatar-sm avatar-rounded bg-danger">
                                                            <i class="ti ti-trash fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 text-muted fs-12">Delete Operations</p>
                                                        <h5 class="mb-0">
                                                            <?php
                                                               $deleteOps = $app->select_one("SELECT COUNT(*) as count FROM audit_logs 
                                                                                             WHERE action_type = 'delete' 
                                                                                             AND DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
                                                               echo number_format($deleteOps->count ?? 0);
                                                               ?>
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Login Operations Card -->
                                        <div class="col-xl-3 col-lg-6">
                                            <div class="p-3 border rounded-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <span class="avatar avatar-sm avatar-rounded bg-info">
                                                            <i class="ti ti-login fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 text-muted fs-12">Login Activities</p>
                                                        <h5 class="mb-0">
                                                            <?php
                                                                    $loginOps = $app->select_one("SELECT COUNT(*) as count FROM activity_logs 
                                                                                                 WHERE activity_type = 'login' 
                                                                                                 AND DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
                                                                    echo number_format($loginOps->count ?? 0);
                                                                    ?>
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Recent Activity List -->
                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped align-middle">
                                            <thead>
                                                <tr>
                                                    <th>User</th>
                                                    <th>Action</th>
                                                    <th>Table</th>
                                                    <th>Details</th>
                                                    <th>Timestamp</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                      $recentLogs = $app->select_all("SELECT al.*, u.first_name, u.last_name 
                                                                                     FROM audit_logs al 
                                                                                     JOIN users u ON al.user_id = u.id 
                                                                                     ORDER BY al.created_at DESC LIMIT 5");
                                                      if($recentLogs):
                                                          foreach($recentLogs as $log):
                                                      ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-2">
                                                                <span class="avatar avatar-sm avatar-rounded bg-light">
                                                                    <?php echo strtoupper(substr($log->first_name, 0, 1)); ?>
                                                                </span>
                                                            </div>
                                                            <div>
                                                                <?php echo htmlspecialchars($log->first_name . ' ' . $log->last_name); ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?php 
                                                                    echo match($log->action_type) {
                                                                        'create' => 'success',
                                                                        'update' => 'warning',
                                                                        'delete' => 'danger',
                                                                        default => 'info'
                                                                    };
                                                                ?>-transparent">
                                                            <?php echo ucfirst($log->action_type); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo ucfirst($log->table_name); ?></td>
                                                    <td>
                                                        <small class="text-muted">
                                                            Record #<?php echo $log->record_id; ?>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <small>
                                                            <?php echo $app->formatTimeAgo($log->created_at); ?>
                                                        </small>
                                                    </td>
                                                </tr>
                                                <?php 
                                                         endforeach;
                                                     else:
                                                     ?>
                                                <tr>
                                                    <td colspan="5" class="text-center">No recent activity found</td>
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
                                    <div class="card-title">System Activity Analytics</div>
                                    <div class="dropdown">
                                        <a href="javascript:void(0);" class="p-2 fs-12 text-muted"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            View All<i
                                                class="ri-arrow-down-s-line align-middle ms-1 d-inline-block"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php include "graphs/system-activity.php" ?>
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
            </div>
        </div>
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