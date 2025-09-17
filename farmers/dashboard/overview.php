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

    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
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
        <!-- End::app-sidebar -->
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
                               
                               // Get farmer details if user is a farmer
                               if ($_SESSION['role_id'] == 1) { // Assuming role_id 5 is for farmers
                                   $farmerQuery = "SELECT f.*, fc.name as category_name 
                                                  FROM farmers f
                                                  LEFT JOIN farmer_categories fc ON f.category_id = fc.id
                                                  WHERE f.user_id=" . $_SESSION['user_id'];
                                   $farmer = $app->select_one($farmerQuery);
                                   
                                   // Get farmer's farm statistics
                                   $farmStatsQuery = "SELECT 
                                       COUNT(farms.id) as total_farms,
                                       COALESCE(SUM(farms.size), 0) as total_acreage,
                                       farms.size_unit
                                       FROM farms 
                                       WHERE farmer_id = " . $farmer->id . " AND is_active = 1
                                       GROUP BY farms.size_unit
                                       LIMIT 1";
                                   $farmStats = $app->select_one($farmStatsQuery);
                                   
                                   // Get active products count
                                   $activeProductsQuery = "SELECT COUNT(DISTINCT fp.id) as active_products
                                                          FROM farm_products fp
                                                          JOIN farms f ON fp.farm_id = f.id
                                                          WHERE f.farmer_id = " . $farmer->id . " AND fp.is_active = 1";
                                   $productStats = $app->select_one($activeProductsQuery);
                                   ?>
                        <p class="fw-semibold fs-18 mb-0">Welcome back,
                            <?php echo htmlspecialchars($user->first_name . ' ' . $user->last_name); ?>!</p>
                        <span class="fs-semibold text-muted">
                            <?php echo htmlspecialchars($farmer->category_name ?? 'Farmer'); ?> Dashboard -
                            Managing <?php echo ($farmStats->total_farms ?? 0); ?> farms
                            (<?php echo number_format($farmStats->total_acreage ?? 0, 1); ?>
                            <?php echo $farmStats->size_unit ?? 'acres'; ?>)
                            with <?php echo ($productStats->active_products ?? 0); ?> active products
                        </span>
                        <?php
                               } else if ($_SESSION['role_id'] == 3) { // Bank staff
                                   $bankQuery = "SELECT b.* FROM banks b
                                                JOIN bank_staff s ON b.id = s.bank_id
                                                WHERE s.user_id=" . $_SESSION['user_id'];
                                   $bank = $app->select_one($bankQuery);
                                   ?>
                        <p class="fw-semibold fs-18 mb-0">Welcome back,
                            <?php echo htmlspecialchars($user->first_name . ' ' . $user->last_name); ?>!</p>
                        <span class="fs-semibold text-muted"><?php echo htmlspecialchars($bank->name); ?> Banking
                            Dashboard -
                            Manage agricultural loans, portfolios, and financial operations</span>
                        <?php
                               } else if ($_SESSION['role_id'] == 4) { // Agrovet staff
                                   $agrovetQuery = "SELECT a.* FROM agrovets a
                                                   JOIN agrovet_staff s ON a.id = s.agrovet_id
                                                   WHERE s.user_id=" . $_SESSION['user_id'];
                                   $agrovet = $app->select_one($agrovetQuery);
                                   ?>
                        <p class="fw-semibold fs-18 mb-0">Welcome back,
                            <?php echo htmlspecialchars($user->first_name . ' ' . $user->last_name); ?>!</p>
                        <span class="fs-semibold text-muted"><?php echo htmlspecialchars($agrovet->name); ?> Dashboard -
                            Manage input credits, inventory, and farmer accounts</span>
                        <?php
                               } else if ($_SESSION['role_id'] == 2) { // SACCO staff
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
                <div class="col-xxl-12 col-xl-12">
                    <?php
                    // Get session user_id and role_id
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }
                    
                    $userId = $_SESSION['user_id'] ?? null;
                    if (!$userId) {
                        header("Location: http://localhost/dfcs/");
                        exit();
                    }
                    
                    $app = new App();
                    
                    // Get farmer ID and profile info from user_id
                    $farmerQuery = "SELECT f.id as farmer_id, u.first_name, u.last_name, u.phone, u.email, u.location, u.profile_picture,
                                           f.registration_number, f.is_verified, fc.name as category_name
                                    FROM farmers f
                                    JOIN users u ON f.user_id = u.id
                                    LEFT JOIN farmer_categories fc ON f.category_id = fc.id
                                    WHERE f.user_id = $userId";
                    $farmer = $app->select_one($farmerQuery);
                    
                    if (!$farmer) {
                        header("Location: http://localhost/dfcs/"); 
                        exit();
                    }
                    
                    $farmerId = $farmer->farmer_id;
                    ?>

                    <div class="row">
                        <!-- Farmer Profile Card - col-4 -->
                        <div class="col-xl-4 col-lg-12">
                            <div class="card custom-card overflow-hidden h-100 shadow-sm">
                                <!-- Enhanced Header with Gradient -->
                                <div class="card-header bg-gradient-primary text-white position-relative"
                                    style="background: linear-gradient(135deg, #6AA32D 0%, #441E07 100%);">
                                    <div class="position-absolute top-0 end-0 p-3">
                                        <i class="ti ti-star fs-20 text-warning"></i>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <span
                                                class="avatar avatar-xl avatar-rounded border border-3 border-white shadow">
                                                <img src="http://localhost/dfcs/<?= $farmer->profile_picture ?? 'http://localhost/dfcs/assets/images/faces/face-image-1.jpg' ?>"
                                                    alt="Profile" class="rounded-circle">
                                            </span>
                                        </div>
                                        <div class="flex-fill">
                                            <h5 class="card-title mb-1 text-white fw-bold">
                                                <?php echo htmlspecialchars($farmer->first_name . ' ' . $farmer->last_name); ?>
                                            </h5>
                                            <p class="mb-0 text-white-75 d-flex align-items-center">
                                                <i class="ti ti-user-check me-2 fs-14"></i>
                                                <?php echo $farmer->category_name ?? 'Farmer'; ?>
                                            </p>
                                            <div class="d-flex align-items-center mt-2">
                                                <i class="ti ti-map-pin me-1 fs-12 text-white-50"></i>
                                                <small class="text-white-50">
                                                    <?php echo htmlspecialchars($farmer->location ?? 'Location not set'); ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body p-4">
                                    <!-- Enhanced Verification Status -->
                                    <div class="d-flex align-items-center justify-content-center mb-4">
                                        <?php if ($farmer->is_verified): ?>
                                        <span
                                            class="badge bg-success-gradient px-3 py-2 rounded-pill d-flex align-items-center">
                                            <i class="ti ti-shield-check me-2 fs-14"></i>
                                            <span class="fw-semibold">Verified Farmer</span>
                                        </span>
                                        <?php else: ?>
                                        <span
                                            class="badge bg-warning-gradient px-3 py-2 rounded-pill d-flex align-items-center">
                                            <i class="ti ti-clock me-2 fs-14"></i>
                                            <span class="fw-semibold">Pending Verification</span>
                                        </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Enhanced Contact Information -->
                                    <div class="mb-4">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="ti ti-address-book text-primary me-2 fs-16"></i>
                                            <h6 class="fw-bold mb-0 text-primary">Contact Information</h6>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <div class="d-flex align-items-center p-2 rounded bg-light-primary">
                                                    <div class="avatar avatar-sm avatar-rounded bg-primary me-3">
                                                        <i class="ti ti-mail text-white fs-14"></i>
                                                    </div>
                                                    <div class="flex-fill">
                                                        <small class="text-muted d-block">Email Address</small>
                                                        <span class="fw-semibold text-dark">
                                                            <?php echo htmlspecialchars($farmer->email); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="d-flex align-items-center p-2 rounded bg-light-success">
                                                    <div class="avatar avatar-sm avatar-rounded bg-success me-3">
                                                        <i class="ti ti-phone text-white fs-14"></i>
                                                    </div>
                                                    <div class="flex-fill">
                                                        <small class="text-muted d-block">Phone Number</small>
                                                        <span class="fw-semibold text-dark">
                                                            <?php echo htmlspecialchars($farmer->phone); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php if ($farmer->registration_number): ?>
                                            <div class="col-12">
                                                <div class="d-flex align-items-center p-2 rounded bg-light-info">
                                                    <div class="avatar avatar-sm avatar-rounded bg-info me-3">
                                                        <i class="ti ti-id text-white fs-14"></i>
                                                    </div>
                                                    <div class="flex-fill">
                                                        <small class="text-muted d-block">Registration Number</small>
                                                        <span class="fw-semibold text-dark">
                                                            <?php echo htmlspecialchars($farmer->registration_number); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Enhanced Quick Stats -->
                                    <div class="mb-4">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="ti ti-dashboard text-primary me-2 fs-16"></i>
                                            <h6 class="fw-bold mb-0 text-primary">Quick Overview</h6>
                                        </div>
                                        <?php 
                                            $quickStats = $app->select_one("SELECT 
                                                (SELECT COUNT(*) FROM farms WHERE farmer_id = $farmerId AND is_active = 1) as total_farms,
                                                (SELECT COALESCE(SUM(size), 0) FROM farms WHERE farmer_id = $farmerId AND is_active = 1) as total_acreage,
                                                (SELECT COUNT(*) FROM farm_products fp JOIN farms f ON fp.farm_id = f.id WHERE f.farmer_id = $farmerId AND fp.is_active = 1) as active_products,
                                                (SELECT COUNT(*) FROM produce_deliveries pd JOIN farm_products fp ON pd.farm_product_id = fp.id JOIN farms f ON fp.farm_id = f.id WHERE f.farmer_id = $farmerId AND MONTH(pd.delivery_date) = MONTH(NOW())) as monthly_deliveries");
                                        ?>
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <div
                                                    class="text-center p-3 bg-primary-transparent rounded-3 border border-primary-transparent">
                                                    <div class="avatar avatar-md avatar-rounded bg-primary mb-2">
                                                        <i class="ti ti-building-estate text-white fs-18"></i>
                                                    </div>
                                                    <h4 class="mb-1 text-primary fw-bold">
                                                        <?php echo $quickStats->total_farms ?? 0; ?>
                                                    </h4>
                                                    <small class="text-muted fw-semibold">Total Farms</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div
                                                    class="text-center p-3 bg-success-transparent rounded-3 border border-success-transparent">
                                                    <div class="avatar avatar-md avatar-rounded bg-success mb-2">
                                                        <i class="ti ti-ruler-2 text-white fs-18"></i>
                                                    </div>
                                                    <h4 class="mb-1 text-success fw-bold">
                                                        <?php echo number_format($quickStats->total_acreage ?? 0, 1); ?>
                                                    </h4>
                                                    <small class="text-muted fw-semibold">Total Acres</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div
                                                    class="text-center p-3 bg-info-transparent rounded-3 border border-info-transparent">
                                                    <div class="avatar avatar-md avatar-rounded bg-info mb-2">
                                                        <i class="ti ti-plant-2 text-white fs-18"></i>
                                                    </div>
                                                    <h4 class="mb-1 text-info fw-bold">
                                                        <?php echo $quickStats->active_products ?? 0; ?>
                                                    </h4>
                                                    <small class="text-muted fw-semibold">Products</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div
                                                    class="text-center p-3 bg-warning-transparent rounded-3 border border-warning-transparent">
                                                    <div class="avatar avatar-md avatar-rounded bg-warning mb-2">
                                                        <i class="ti ti-truck-delivery text-white fs-18"></i>
                                                    </div>
                                                    <h4 class="mb-1 text-warning fw-bold">
                                                        <?php echo $quickStats->monthly_deliveries ?? 0; ?>
                                                    </h4>
                                                    <small class="text-muted fw-semibold">This Month</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Enhanced Category Progress -->
                                    <div class="mb-4">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="ti ti-trophy text-primary me-2 fs-16"></i>
                                            <h6 class="fw-bold mb-0 text-primary">Farmer Category Progress</h6>
                                        </div>
                                        <div class="card bg-gradient-light border-0 p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="ti ti-award text-primary me-2 fs-16"></i>
                                                    <div>
                                                        <small class="text-muted d-block">Current Level</small>
                                                        <span class="fw-bold text-primary">
                                                            <?php echo $farmer->category_name ?? 'Smallholder'; ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <small class="text-muted d-block">Next Target</small>
                                                    <span class="fw-semibold text-secondary">
                                                        <?php 
                                                                 $currentAcreage = $quickStats->total_acreage ?? 0;
                                                                 if ($currentAcreage < 5) {
                                                                     echo "Emerging (5+ acres)";
                                                                 } elseif ($currentAcreage < 20) {
                                                                     echo "Commercial (20+ acres)";
                                                                 } else {
                                                                     echo "Achieved: Commercial";
                                                                 }
                                                             ?>
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="progress progress-lg mb-2" style="height: 8px;">
                                                <?php 
                                                         $categoryProgress = 0;
                                                         if ($currentAcreage >= 20) $categoryProgress = 100;
                                                         elseif ($currentAcreage >= 5) $categoryProgress = 60;
                                                         else $categoryProgress = ($currentAcreage / 5) * 40;
                                                     ?>
                                                <div class="progress-bar bg-gradient-primary progress-bar-striped progress-bar-animated"
                                                    style="width: <?php echo $categoryProgress; ?>%"></div>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    <i class="ti ti-percentage me-1"></i>
                                                    <?php echo number_format($categoryProgress, 1); ?>% Complete
                                                </small>
                                                <small class="text-primary fw-semibold">
                                                    <?php echo number_format($currentAcreage, 1); ?> /
                                                    <?php echo $currentAcreage < 5 ? '5' : ($currentAcreage < 20 ? '20' : '20+'); ?>
                                                    acres
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Enhanced Action Buttons -->
                                    <div class="d-grid gap-2">
                                        <a href="farmer-profile"
                                            class="btn btn-primary btn-lg d-flex align-items-center justify-content-center">
                                            <i class="ti ti-user-edit me-2 fs-16"></i>
                                            <span class="fw-semibold">Edit Profile</span>
                                        </a>

                                    </div>
                                </div>

                                <!-- Card Footer -->
                                <div class="card-footer bg-light text-center">
                                    <small class="text-muted">
                                        <i class="ti ti-calendar me-1"></i>
                                        Member since <?php echo date('M Y', strtotime($farmer->created_at ?? 'now')); ?>
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Dashboard Cards - col-8 -->
                        <div class="col-xl-8 col-lg-12">
                            <div class="row">
                                <!-- Farm Portfolio Card -->
                                <div class="col-lg-6 col-md-6">
                                    <div class="card custom-card overflow-hidden">
                                        <div class="card-body">
                                            <div class="d-flex align-items-top justify-content-between">
                                                <div>
                                                    <span class="avatar avatar-md avatar-rounded bg-primary">
                                                        <i class="ti ti-map-2 fs-16"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill ms-3">
                                                    <div
                                                        class="d-flex align-items-center justify-content-between flex-wrap">
                                                        <div>
                                                            <p class="text-muted mb-0">Total Farms</p>
                                                            <h4 class="fw-semibold mt-1">
                                                                <?php 
                                                                         $totalFarms = $app->select_one("SELECT COUNT(*) as count, 
                                                                                                               COALESCE(SUM(size), 0) as total_size,
                                                                                                               (SELECT size_unit FROM farms WHERE farmer_id = $farmerId LIMIT 1) as size_unit
                                                                                                        FROM farms 
                                                                                                        WHERE farmer_id = $farmerId AND is_active = 1");
                                                                         echo $totalFarms->count ?? 0; 
                                                                     ?>
                                                            </h4>
                                                            <small class="text-muted">
                                                                <?php echo number_format($totalFarms->total_size ?? 0, 1) . ' ' . ($totalFarms->size_unit ?? 'acres'); ?>
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <div class="progress progress-xs mt-2">
                                                        <?php 
                                                            $maxFarms = 10;
                                                            $progressPercent = min((($totalFarms->count ?? 0) / $maxFarms) * 100, 100);
                                                        ?>
                                                        <div class="progress-bar bg-primary"
                                                            style="width: <?php echo $progressPercent; ?>%"></div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between mt-2">
                                                        <div>
                                                            <a class="text-primary" href="my-farms">Manage Farms<i
                                                                    class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                                        </div>
                                                        <div class="text-end">
                                                            <p class="mb-0 text-primary fw-semibold">Active</p>
                                                            <span class="text-muted op-7 fs-11">farms</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Active Products Card -->
                                <div class="col-lg-6 col-md-6">
                                    <div class="card custom-card overflow-hidden">
                                        <div class="card-body">
                                            <div class="d-flex align-items-top justify-content-between">
                                                <div>
                                                    <span class="avatar avatar-md avatar-rounded bg-success">
                                                        <i class="ti ti-plant fs-16"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill ms-3">
                                                    <div
                                                        class="d-flex align-items-center justify-content-between flex-wrap">
                                                        <div>
                                                            <p class="text-muted mb-0">Active Products</p>
                                                            <h4 class="fw-semibold mt-1">
                                                                <?php 
                                                                     $activeProducts = $app->select_one("SELECT COUNT(DISTINCT fp.id) as count,
                                                                                                                COUNT(DISTINCT pt.name) as product_types
                                                                                                         FROM farm_products fp
                                                                                                         JOIN farms f ON fp.farm_id = f.id
                                                                                                         LEFT JOIN product_types pt ON fp.product_type_id = pt.id
                                                                                                         WHERE f.farmer_id = $farmerId AND fp.is_active = 1");
                                                                     echo $activeProducts->count ?? 0; 
                                                                 ?>
                                                            </h4>
                                                            <small class="text-muted">
                                                                <?php echo ($activeProducts->product_types ?? 0) . ' different types'; ?>
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <div class="progress progress-xs mt-2">
                                                        <?php 
                                                            $maxProducts = 15;
                                                            $productProgress = min((($activeProducts->count ?? 0) / $maxProducts) * 100, 100);
                                                        ?>
                                                        <div class="progress-bar bg-success"
                                                            style="width: <?php echo $productProgress; ?>%"></div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between mt-2">
                                                        <div>
                                                            <a class="text-success" href="my-products">View Products<i
                                                                    class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                                        </div>
                                                        <div class="text-end">
                                                            <p class="mb-0 text-success fw-semibold">Growing</p>
                                                            <span class="text-muted op-7 fs-11">products</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Account Balance Card -->
                                <div class="col-lg-6 col-md-6">
                                    <div class="card custom-card overflow-hidden">
                                        <div class="card-body">
                                            <div class="d-flex align-items-top justify-content-between">
                                                <div>
                                                    <span class="avatar avatar-md avatar-rounded bg-info">
                                                        <i class="ti ti-wallet fs-16"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill ms-3">
                                                    <div
                                                        class="d-flex align-items-center justify-content-between flex-wrap">
                                                        <div>
                                                            <p class="text-muted mb-0">Account Balance</p>
                                                            <h4 class="fw-semibold mt-1">
                                                                <?php 
                                                                       $accountBalance = $app->select_one("SELECT COALESCE(fa.balance, 0) as balance,
                                                                                                                  COUNT(fat.id) as transactions_count
                                                                                                           FROM farmer_accounts fa
                                                                                                           LEFT JOIN farmer_account_transactions fat 
                                                                                                               ON fa.id = fat.farmer_account_id 
                                                                                                               AND MONTH(fat.created_at) = MONTH(NOW())
                                                                                                           WHERE fa.farmer_id = $farmerId
                                                                                                           GROUP BY fa.balance");
                                                                       echo 'KES ' . number_format($accountBalance->balance ?? 0, 0); 
                                                                   ?>
                                                            </h4>
                                                            <small class="text-muted">
                                                                <?php echo ($accountBalance->transactions_count ?? 0) . ' transactions this month'; ?>
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <div class="progress progress-xs mt-2">
                                                        <?php 
                                                           $targetBalance = 100000;
                                                           $balanceProgress = min((($accountBalance->balance ?? 0) / $targetBalance) * 100, 100);
                                                       ?>
                                                        <div class="progress-bar bg-info"
                                                            style="width: <?php echo $balanceProgress; ?>%"></div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between mt-2">
                                                        <div>
                                                            <a class="text-info" href="account-statement">View
                                                                Statement<i
                                                                    class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                                        </div>
                                                        <div class="text-end">
                                                            <p class="mb-0 text-info fw-semibold">Current</p>
                                                            <span class="text-muted op-7 fs-11">balance</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Monthly Revenue Card -->
                                <div class="col-lg-6 col-md-6">
                                    <div class="card custom-card overflow-hidden">
                                        <div class="card-body">
                                            <div class="d-flex align-items-top justify-content-between">
                                                <div>
                                                    <span class="avatar avatar-md avatar-rounded"
                                                        style="background-color: rgba(112, 161, 54, 0.1); color: #70A136;">
                                                        <i class="ti ti-trending-up fs-16"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill ms-3">
                                                    <div
                                                        class="d-flex align-items-center justify-content-between flex-wrap">
                                                        <div>
                                                            <p class="text-muted mb-0">This Month's Sales</p>
                                                            <h4 class="fw-semibold mt-1">
                                                                <?php 
                                                                $monthlyRevenue = $app->select_one("SELECT COALESCE(SUM(pd.total_value), 0) as amount,
                                                                                                           COUNT(*) as deliveries_count
                                                                                                    FROM produce_deliveries pd
                                                                                                    JOIN farm_products fp ON pd.farm_product_id = fp.id
                                                                                                    JOIN farms f ON fp.farm_id = f.id
                                                                                                    WHERE f.farmer_id = $farmerId 
                                                                                                    AND pd.status = 'paid'
                                                                                                    AND MONTH(pd.delivery_date) = MONTH(NOW())
                                                                                                    AND YEAR(pd.delivery_date) = YEAR(NOW())");
                                                                echo 'KES ' . number_format($monthlyRevenue->amount ?? 0, 0); 
                                                            ?>
                                                            </h4>
                                                            <small class="text-muted">
                                                                <?php echo ($monthlyRevenue->deliveries_count ?? 0) . ' deliveries'; ?>
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <div class="progress progress-xs mt-2">
                                                        <?php 
                                                               $targetRevenue = 50000;
                                                               $revenueProgress = min((($monthlyRevenue->amount ?? 0) / $targetRevenue) * 100, 100);
                                                           ?>
                                                        <div class="progress-bar"
                                                            style="width: <?php echo $revenueProgress; ?>%; background-color: #70A136;">
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between mt-2">
                                                        <div>
                                                            <a style="color: #70A136;" href="sales-report">View Report<i
                                                                    class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                                        </div>
                                                        <div class="text-end">
                                                            <p class="mb-0 fw-semibold" style="color: #70A136;">Monthly
                                                            </p>
                                                            <span class="text-muted op-7 fs-11">earnings</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- NEW CARD 1: Pending Deliveries -->
                                <div class="col-lg-6 col-md-6">
                                    <div class="card custom-card overflow-hidden">
                                        <div class="card-body">
                                            <div class="d-flex align-items-top justify-content-between">
                                                <div>
                                                    <span class="avatar avatar-md avatar-rounded bg-warning">
                                                        <i class="ti ti-truck fs-16"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill ms-3">
                                                    <div
                                                        class="d-flex align-items-center justify-content-between flex-wrap">
                                                        <div>
                                                            <p class="text-muted mb-0">Pending Deliveries</p>
                                                            <h4 class="fw-semibold mt-1">
                                                                <?php 
                                                                 $pendingDeliveries = $app->select_one("SELECT COUNT(*) as count,
                                                                                                               COALESCE(SUM(total_value), 0) as total_value
                                                                                                        FROM produce_deliveries pd
                                                                                                        JOIN farm_products fp ON pd.farm_product_id = fp.id
                                                                                                        JOIN farms f ON fp.farm_id = f.id
                                                                                                        WHERE f.farmer_id = $farmerId 
                                                                                                        AND pd.status IN ('pending', 'accepted', 'verified')");
                                                                 echo $pendingDeliveries->count ?? 0; 
                                                             ?>
                                                            </h4>
                                                            <small class=" text-muted">
                                                                KES
                                                                <?php echo number_format($pendingDeliveries->total_value ?? 0, 0); ?>
                                                                value
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <div class="progress progress-xs mt-2">
                                                        <?php 
                                                          $maxPending = 20; // Assume max 20 pending deliveries
                                                          $pendingProgress = min((($pendingDeliveries->count ?? 0) / $maxPending) * 100, 100);
                                                      ?>
                                                        <div class="progress-bar bg-warning"
                                                            style="width: <?php echo $pendingProgress; ?>%"></div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between mt-2">
                                                        <div>
                                                            <a class="text-warning" href="my-deliveries">Track
                                                                Deliveries<i
                                                                    class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                                        </div>
                                                        <div class="text-end">
                                                            <p class="mb-0 text-warning fw-semibold">Awaiting</p>
                                                            <span class="text-muted op-7 fs-11">payment</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- NEW CARD 2: Active Loans & Credits -->
                                <div class="col-lg-6 col-md-6">
                                    <div class="card custom-card overflow-hidden">
                                        <div class="card-body">
                                            <div class="d-flex align-items-top justify-content-between">
                                                <div>
                                                    <span class="avatar avatar-md avatar-rounded bg-purple">
                                                        <i class="ti ti-credit-card fs-16"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill ms-3">
                                                    <div
                                                        class="d-flex align-items-center justify-content-between flex-wrap">
                                                        <div>
                                                            <p class="text-muted mb-0">Active Loans & Credits</p>
                                                            <h4 class="fw-semibold mt-1">
                                                                <?php 
                                                                  $activeLoansCredits = $app->select_one("SELECT 
                                                                      (SELECT COUNT(*) FROM approved_loans al 
                                                                       JOIN loan_applications la ON al.loan_application_id = la.id 
                                                                       WHERE la.farmer_id = $farmerId AND al.status = 'active') + 
                                                                      (SELECT COUNT(*) FROM approved_input_credits aic 
                                                                       JOIN input_credit_applications ica ON aic.credit_application_id = ica.id 
                                                                       WHERE ica.farmer_id = $farmerId AND aic.status = 'active') as total_count,
                                                                      
                                                                      (SELECT COALESCE(SUM(al.remaining_balance), 0) FROM approved_loans al 
                                                                       JOIN loan_applications la ON al.loan_application_id = la.id 
                                                                       WHERE la.farmer_id = $farmerId AND al.status = 'active') + 
                                                                      (SELECT COALESCE(SUM(aic.remaining_balance), 0) FROM approved_input_credits aic 
                                                                       JOIN input_credit_applications ica ON aic.credit_application_id = ica.id 
                                                                       WHERE ica.farmer_id = $farmerId AND aic.status = 'active') as total_balance");
                                                                  echo $activeLoansCredits->total_count ?? 0; 
                                                              ?>
                                                            </h4>
                                                            <small class="text-muted">
                                                                KES
                                                                <?php echo number_format($activeLoansCredits->total_balance ?? 0, 0); ?>
                                                                outstanding
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <div class="progress progress-xs mt-2">
                                                        <?php 
                                                             $maxDebt = 500000; // KES 500K max debt capacity
                                                             $debtProgress = min((($activeLoansCredits->total_balance ?? 0) / $maxDebt) * 100, 100);
                                                         ?>
                                                        <div class="progress-bar bg-purple"
                                                            style="width: <?php echo $debtProgress; ?>%"></div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between mt-2">
                                                        <div>
                                                            <a class="text-purple" href="my-loans">Manage Loans<i
                                                                    class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                                        </div>
                                                        <div class="text-end">
                                                            <p class="mb-0 text-purple fw-semibold">Outstanding</p>
                                                            <span class="text-muted op-7 fs-11">balance</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Recommendations Section -->
                            <div class="row mt-4">
                                <div class="col-xl-12">
                                    <div class="card custom-card">
                                        <div class="card-header justify-content-between">
                                            <div class="card-title">
                                                <i class="ti ti-bulb me-2" style="color: #70A136;"></i>
                                                Smart Recommendations for You
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <!-- Crop Optimization -->
                                                <div class="col-xl-4 col-lg-6 col-md-12">
                                                    <div class="alert alert-outline-success" role="alert">
                                                        <div class="d-flex align-items-start">
                                                            <div class="me-2">
                                                                <span class="avatar avatar-sm svg-success">
                                                                    <i class="ti ti-plant fs-14"></i>
                                                                </span>
                                                            </div>
                                                            <div class="flex-fill">
                                                                <p class="fw-semibold mb-1">Crop Optimization</p>
                                                                <p class="op-8 mb-1 fs-12">
                                                                    <?php 
                                                                            $cropOptimization = $app->select_one("SELECT 
                                                                                COUNT(DISTINCT pt.name) as product_types,
                                                                                AVG(pd.total_value) as avg_delivery_value,
                                                                                SUM(CASE WHEN pd.quality_grade = 'A' THEN 1 ELSE 0 END) as grade_a_count,
                                                                                COUNT(pd.id) as total_deliveries
                                                                                FROM farm_products fp
                                                                                JOIN farms f ON fp.farm_id = f.id
                                                                                JOIN product_types pt ON fp.product_type_id = pt.id
                                                                                LEFT JOIN produce_deliveries pd ON fp.id = pd.farm_product_id
                                                                                WHERE f.farmer_id = $farmerId AND fp.is_active = 1");
                                                                            
                                                                            $qualityRate = $cropOptimization->total_deliveries > 0 ? 
                                                                                ($cropOptimization->grade_a_count / $cropOptimization->total_deliveries) * 100 : 0;
                                                                            
                                                                            if ($qualityRate < 60) {
                                                                                echo "Focus on quality improvement - current Grade A rate: " . number_format($qualityRate, 1) . "%";
                                                                            } elseif ($cropOptimization->product_types < 3) {
                                                                                echo "Consider diversifying crops - currently growing " . ($cropOptimization->product_types ?? 0) . " types";
                                                                            } else {
                                                                                echo "Great crop diversity! Focus on scaling high-value products";
                                                                            }
                                                                        ?>
                                                                </p>
                                                                <div class="mt-2">
                                                                    <small class="text-success fw-semibold">
                                                                        <i class="ti ti-arrow-up me-1"></i>
                                                                        Avg delivery value: KES
                                                                        <?php echo number_format($cropOptimization->avg_delivery_value ?? 0, 0); ?>
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Financial Growth -->
                                                <div class="col-xl-4 col-lg-6 col-md-12">
                                                    <div class="alert alert-outline-primary" role="alert">
                                                        <div class="d-flex align-items-start">
                                                            <div class="me-2">
                                                                <span class="avatar avatar-sm svg-primary">
                                                                    <i class="ti ti-trending-up fs-14"></i>
                                                                </span>
                                                            </div>
                                                            <div class="flex-fill">
                                                                <p class="fw-semibold mb-1">Financial Growth</p>
                                                                <p class="op-8 mb-1 fs-12">
                                                                    <?php 
                                                                        $financialGrowth = $app->select_one("SELECT 
                                                                            fa.balance,
                                                                            (SELECT COUNT(*) FROM approved_loans al 
                                                                             JOIN loan_applications la ON al.loan_application_id = la.id 
                                                                             WHERE la.farmer_id = $farmerId AND al.status = 'active') as active_loans,
                                                                            (SELECT COUNT(*) FROM approved_input_credits aic 
                                                                             JOIN input_credit_applications ica ON aic.credit_application_id = ica.id 
                                                                             WHERE ica.farmer_id = $farmerId AND aic.status = 'active') as active_credits
                                                                            FROM farmer_accounts fa
                                                                            WHERE fa.farmer_id = $farmerId");
                                                                        
                                                                        if ($financialGrowth->balance < 50000 && $financialGrowth->active_loans == 0) {
                                                                            echo "Consider applying for a growth loan to expand operations";
                                                                        } elseif ($financialGrowth->active_credits == 0) {
                                                                            echo "Input credits can help reduce upfront costs for next season";
                                                                        } else {
                                                                            echo "Focus on loan repayment to maintain good credit score";
                                                                        }
                                                                    ?>
                                                                </p>
                                                                <div class="mt-2">
                                                                    <small class="text-primary fw-semibold">
                                                                        <i class="ti ti-wallet me-1"></i>
                                                                        Current balance: KES
                                                                        <?php echo number_format($financialGrowth->balance ?? 0, 0); ?>
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Farm Expansion -->
                                                <div class="col-xl-4 col-lg-6 col-md-12">
                                                    <div class="alert alert-outline-warning" role="alert">
                                                        <div class="d-flex align-items-start">
                                                            <div class="me-2">
                                                                <span class="avatar avatar-sm svg-warning">
                                                                    <i class="ti ti-map-2 fs-14"></i>
                                                                </span>
                                                            </div>
                                                            <div class="flex-fill">
                                                                <p class="fw-semibold mb-1">Farm Expansion</p>
                                                                <p class="op-8 mb-1 fs-12">
                                                                    <?php 
                                                                         $expansionData = $app->select_one("SELECT 
                                                                             COUNT(*) as total_farms,
                                                                             SUM(size) as total_acreage,
                                                                             fc.name as current_category
                                                                             FROM farms f
                                                                             LEFT JOIN farmers fr ON f.farmer_id = fr.id
                                                                             LEFT JOIN farmer_categories fc ON fr.category_id = fc.id
                                                                             WHERE f.farmer_id = $farmerId AND f.is_active = 1
                                                                             GROUP BY fc.name");
                                                                         
                                                                         $currentAcreage = $expansionData->total_acreage ?? 0;
                                                                         if ($currentAcreage < 5) {
                                                                             $needed = 5 - $currentAcreage;
                                                                             echo "Add " . number_format($needed, 1) . " more acres to become an Emerging Farmer";
                                                                         } elseif ($currentAcreage < 20) {
                                                                             $needed = 20 - $currentAcreage;
                                                                             echo "Add " . number_format($needed, 1) . " more acres to become a Commercial Farmer";
                                                                         } else {
                                                                             echo "Consider specializing in high-value crops for maximum returns";
                                                                         }
                                                                     ?>
                                                                </p>
                                                                <div class="mt-2">
                                                                    <small class="text-warning fw-semibold">
                                                                        <i class="ti ti-ruler-2 me-1"></i>
                                                                        Current:
                                                                        <?php echo number_format($currentAcreage, 1); ?>
                                                                        acres
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Second Row -->
                                            <div class="row mt-3">
                                                <!-- Seasonal Planning -->
                                                <div class="col-xl-6 col-lg-12">
                                                    <div class="alert alert-outline-info" role="alert">
                                                        <div class="d-flex align-items-start">
                                                            <div class="me-2">
                                                                <span class="avatar avatar-sm svg-info">
                                                                    <i class="ti ti-calendar fs-14"></i>
                                                                </span>
                                                            </div>
                                                            <div class="flex-fill">
                                                                <p class="fw-semibold mb-1">Seasonal Planning</p>
                                                                <p class="op-8 mb-1 fs-12">
                                                                    <?php 
                                                                       $currentMonth = date('n');
                                                                       if ($currentMonth >= 3 && $currentMonth <= 5) {
                                                                           echo "Perfect time for land preparation and planting - consider applying for input credits now";
                                                                       } elseif ($currentMonth >= 6 && $currentMonth <= 8) {
                                                                           echo "Focus on crop maintenance and pest control - monitor weather patterns";
                                                                       } elseif ($currentMonth >= 9 && $currentMonth <= 11) {
                                                                           echo "Harvest season approaching - prepare storage and plan deliveries";
                                                                       } else {
                                                                           echo "Post-harvest period - good time for soil testing and farm planning";
                                                                       }
                                                                   ?>
                                                                </p>
                                                                <div class="mt-2">
                                                                    <span
                                                                        class="badge bg-info-transparent text-info fs-11">
                                                                        <?php echo date('F Y'); ?> - Planning Period
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Technology Adoption -->
                                                <div class="col-xl-6 col-lg-12">
                                                    <div class="alert"
                                                        style="border: 1px solid rgba(112, 161, 54, 0.2); background-color: rgba(112, 161, 54, 0.05);"
                                                        role="alert">
                                                        <div class="d-flex align-items-start">
                                                            <div class="me-2">
                                                                <span class="avatar avatar-sm"
                                                                    style="background-color: rgba(112, 161, 54, 0.1); color: #70A136;">
                                                                    <i class="ti ti-device-mobile fs-14"></i>
                                                                </span>
                                                            </div>
                                                            <div class="flex-fill">
                                                                <p class="fw-semibold mb-1">Technology Adoption</p>
                                                                <p class="op-8 mb-1 fs-12">
                                                                    <?php 
                                                                       // Check if farmer has been using the system actively
                                                                       $techAdoption = $app->select_one("SELECT 
                                                                           COUNT(DISTINCT DATE(pd.created_at)) as active_days,
                                                                           COUNT(pd.id) as total_deliveries
                                                                           FROM produce_deliveries pd
                                                                           JOIN farm_products fp ON pd.farm_product_id = fp.id
                                                                           JOIN farms f ON fp.farm_id = f.id
                                                                           WHERE f.farmer_id = $farmerId 
                                                                           AND pd.created_at >= DATE_SUB(NOW(), INTERVAL 3 MONTH)");
                                                                       
                                                                       if ($techAdoption->active_days < 10) {
                                                                           echo "Consider using mobile apps for weather updates and market prices";
                                                                       } elseif ($techAdoption->total_deliveries > 0) {
                                                                           echo "Great digital engagement! Explore precision farming tools for better yields";
                                                                       } else {
                                                                           echo "Start with basic record keeping - track expenses and income digitally";
                                                                       }
                                                                   ?>
                                                                </p>
                                                                <div class="mt-2">
                                                                    <span class="badge fs-11"
                                                                        style="background-color: rgba(112, 161, 54, 0.1); color: #70A136;">
                                                                        Digital Farming Tools Available
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
                </div>
                <!-- laon -->
                <div class="row">
                    <!-- Production Trends Graph -->
                    <div class="col-xl-8">
                        <div class="card custom-card">
                            <div class="card-header justify-content-between">
                                <div class="card-title">
                                    <i class="ti ti-chart-line me-2" style="color: #70A136;"></i>
                                    Production & Sales Trends
                                </div>
                            </div>
                            <div class="card-body">
                                <?php include "graphs/production-trends.php" ?>
                            </div>
                        </div>
                    </div>

                    <!-- Production Statistics Card -->
                    <div class="col-xl-4">
                        <div class="card custom-card">
                            <div class="card-header">
                                <div class="card-title">
                                    <i class="ti ti-plant me-2" style="color: #4A220F;"></i>
                                    Production Statistics
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    <!-- Total Deliveries -->
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <span class="avatar avatar-sm avatar-rounded bg-primary">
                                                    <i class="ti ti-truck fs-16"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <p class="mb-0">Total Deliveries</p>
                                                <span class="text-muted fs-12">
                                                    <?php 
                                        $totalDeliveries = $app->select_one("SELECT COUNT(*) as count, SUM(total_value) as total_value 
                                                                            FROM produce_deliveries pd
                                                                            JOIN farm_products fp ON pd.farm_product_id = fp.id
                                                                            JOIN farms f ON fp.farm_id = f.id
                                                                            WHERE f.farmer_id = $farmerId");
                                        echo number_format($totalDeliveries->count) . ' Deliveries (KES ' . number_format($totalDeliveries->total_value ?? 0, 0) . ')';
                                    ?>
                                                </span>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-primary-transparent">
                                                    <?php 
                                        $thisMonthDeliveries = $app->select_one("SELECT COUNT(*) as count FROM produce_deliveries pd
                                                                                JOIN farm_products fp ON pd.farm_product_id = fp.id
                                                                                JOIN farms f ON fp.farm_id = f.id
                                                                                WHERE f.farmer_id = $farmerId 
                                                                                AND MONTH(pd.delivery_date) = MONTH(NOW())
                                                                                AND YEAR(pd.delivery_date) = YEAR(NOW())");
                                        echo number_format($thisMonthDeliveries->count) . ' This Month';
                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="progress mt-2" style="height: 5px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"
                                                aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Quality Performance -->
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <span class="avatar avatar-sm avatar-rounded bg-success">
                                                    <i class="ti ti-award fs-16"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <p class="mb-0">Quality Performance</p>
                                                <span class="text-muted fs-12">
                                                    <?php 
                                        $qualityStats = $app->select_one("SELECT 
                                            SUM(CASE WHEN quality_grade = 'A' THEN 1 ELSE 0 END) as grade_a,
                                            COUNT(*) as total_graded
                                            FROM produce_deliveries pd
                                            JOIN farm_products fp ON pd.farm_product_id = fp.id
                                            JOIN farms f ON fp.farm_id = f.id
                                            WHERE f.farmer_id = $farmerId AND quality_grade IS NOT NULL");
                                        $gradeAPercent = ($qualityStats->total_graded > 0) ? 
                                            round(($qualityStats->grade_a / $qualityStats->total_graded) * 100) : 0;
                                        echo $qualityStats->grade_a . ' Grade A (' . $gradeAPercent . '% Quality Rate)';
                                    ?>
                                                </span>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-success-transparent">
                                                    <?php echo $gradeAPercent . '% Grade A'; ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="progress mt-2" style="height: 5px;">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: <?php echo $gradeAPercent; ?>%"
                                                aria-valuenow="<?php echo $gradeAPercent; ?>" aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Revenue Performance -->
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <span class="avatar avatar-sm avatar-rounded"
                                                    style="background-color: rgba(112, 161, 54, 0.1); color: #70A136;">
                                                    <i class="ti ti-coins fs-16"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <p class="mb-0">Revenue Performance</p>
                                                <span class="text-muted fs-12">
                                                    <?php 
                                        $revenueStats = $app->select_one("SELECT 
                                            SUM(total_value) as total_revenue,
                                            AVG(total_value) as avg_delivery_value
                                            FROM produce_deliveries pd
                                            JOIN farm_products fp ON pd.farm_product_id = fp.id
                                            JOIN farms f ON fp.farm_id = f.id
                                            WHERE f.farmer_id = $farmerId AND pd.status = 'paid'");
                                        echo 'KES ' . number_format($revenueStats->total_revenue ?? 0, 0) . ' Total (Avg: KES ' . number_format($revenueStats->avg_delivery_value ?? 0, 0) . ')';
                                    ?>
                                                </span>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge"
                                                    style="background-color: rgba(112, 161, 54, 0.1); color: #70A136;">
                                                    KES
                                                    <?php echo number_format($revenueStats->avg_delivery_value ?? 0, 0); ?>
                                                    Avg
                                                </span>
                                            </div>
                                        </div>
                                        <div class="progress mt-2" style="height: 5px;">
                                            <div class="progress-bar" style="background-color: #70A136;"
                                                role="progressbar" style="width: 85%" aria-valuenow="85"
                                                aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Monthly Growth -->
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <span class="avatar avatar-sm avatar-rounded bg-warning">
                                                    <i class="ti ti-trending-up fs-16"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <p class="mb-0">Monthly Growth</p>
                                                <span class="text-muted fs-12">
                                                    <?php 
                                        $thisMonthRevenue = $app->select_one("SELECT SUM(total_value) as revenue 
                                                                            FROM produce_deliveries pd
                                                                            JOIN farm_products fp ON pd.farm_product_id = fp.id
                                                                            JOIN farms f ON fp.farm_id = f.id
                                                                            WHERE f.farmer_id = $farmerId 
                                                                            AND pd.status = 'paid'
                                                                            AND MONTH(pd.delivery_date) = MONTH(NOW())
                                                                            AND YEAR(pd.delivery_date) = YEAR(NOW())");
                                        
                                        $lastMonthRevenue = $app->select_one("SELECT SUM(total_value) as revenue 
                                                                            FROM produce_deliveries pd
                                                                            JOIN farm_products fp ON pd.farm_product_id = fp.id
                                                                            JOIN farms f ON fp.farm_id = f.id
                                                                            WHERE f.farmer_id = $farmerId 
                                                                            AND pd.status = 'paid'
                                                                            AND MONTH(pd.delivery_date) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))
                                                                            AND YEAR(pd.delivery_date) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH))");
                                        
                                        echo 'KES ' . number_format($thisMonthRevenue->revenue ?? 0, 0) . ' This Month';
                                    ?>
                                                </span>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-warning-transparent">
                                                    <?php 
                                        $growthRate = 0;
                                        if (($lastMonthRevenue->revenue ?? 0) > 0) {
                                            $growthRate = ((($thisMonthRevenue->revenue ?? 0) - ($lastMonthRevenue->revenue ?? 0)) / ($lastMonthRevenue->revenue ?? 0)) * 100;
                                        }
                                        echo ($growthRate >= 0 ? '+' : '') . number_format($growthRate, 1) . '% Growth';
                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="progress mt-2" style="height: 5px;">
                                            <div class="progress-bar bg-warning" role="progressbar"
                                                style="width: <?php echo min(100, abs($growthRate)); ?>%"
                                                aria-valuenow="<?php echo abs($growthRate); ?>" aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Productivity Score -->
                                    <div class="list-group-item bg-light">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <span class="avatar avatar-sm avatar-rounded"
                                                    style="background-color: rgba(74, 34, 15, 0.1); color: #4A220F;">
                                                    <i class="ti ti-target fs-16"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <p class="mb-0 fw-semibold">Productivity Score</p>
                                                <span class="text-muted fs-12">
                                                    <?php 
                                        // Calculate productivity score based on deliveries frequency, quality, and revenue
                                        $productivityScore = 0;
                                        if ($thisMonthDeliveries->count >= 2) $productivityScore += 30;
                                        if ($gradeAPercent >= 70) $productivityScore += 40;
                                        if (($thisMonthRevenue->revenue ?? 0) > 25000) $productivityScore += 30;
                                        
                                        echo 'Score: ' . $productivityScore . '/100 (Based on delivery frequency, quality & revenue)';
                                    ?>
                                                </span>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge"
                                                    style="background-color: rgba(74, 34, 15, 0.1); color: #4A220F;">
                                                    <?php 
                                        if ($productivityScore >= 80) echo 'Excellent';
                                        elseif ($productivityScore >= 60) echo 'Good';
                                        elseif ($productivityScore >= 40) echo 'Fair';
                                        else echo 'Needs Improvement';
                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="progress mt-2" style="height: 5px;">
                                            <div class="progress-bar" style="background-color: #4A220F;"
                                                role="progressbar" style="width: <?php echo $productivityScore; ?>%"
                                                aria-valuenow="<?php echo $productivityScore; ?>" aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <!-- Product Type Distribution and Recommendations -->
                    <div class="col-xl-12">
                        <div class="row">
                            <!-- Product Type Distribution Card -->
                            <div class="col-xl-8">
                                <div class="card custom-card">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <i class="ti ti-chart-bar me-2" style="color: #70A136;"></i>
                                            Crop Production Distribution
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="list-group list-group-flush">
                                            <?php
                                // Get top product types by delivery count and value
                                $productTypesQuery = "SELECT 
                                                        pt.name, 
                                                        COUNT(pd.id) as delivery_count,
                                                        SUM(pd.total_value) as total_value,
                                                        AVG(pd.total_value) as avg_value,
                                                        SUM(CASE WHEN pd.quality_grade = 'A' THEN 1 ELSE 0 END) as grade_a_count
                                                     FROM produce_deliveries pd
                                                     JOIN farm_products fp ON pd.farm_product_id = fp.id
                                                     JOIN farms f ON fp.farm_id = f.id
                                                     JOIN product_types pt ON fp.product_type_id = pt.id
                                                     WHERE f.farmer_id = $farmerId
                                                     GROUP BY pt.id, pt.name
                                                     ORDER BY delivery_count DESC
                                                     LIMIT 5";
                                $productTypes = $app->select_all($productTypesQuery);
                                
                                // Get total count for percentage calculation
                                $totalDeliveriesQuery = "SELECT COUNT(*) as total FROM produce_deliveries pd
                                                        JOIN farm_products fp ON pd.farm_product_id = fp.id
                                                        JOIN farms f ON fp.farm_id = f.id
                                                        WHERE f.farmer_id = $farmerId";
                                $totalDeliveriesCount = $app->select_one($totalDeliveriesQuery);
                                $total = $totalDeliveriesCount->total;
                                
                                // Array of colors for different product types
                                $colors = ['primary', 'success', 'warning', 'danger', 'info', 'purple'];
                                
                                $counter = 0;
                                if ($productTypes && count($productTypes) > 0) {
                                    foreach ($productTypes as $productType) {
                                        $percentage = ($total > 0) ? round(($productType->delivery_count / $total) * 100) : 0;
                                        $qualityRate = ($productType->delivery_count > 0) ? 
                                            round(($productType->grade_a_count / $productType->delivery_count) * 100) : 0;
                                        $color = $colors[$counter % count($colors)];
                                        ?>
                                            <div class="list-group-item">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2">
                                                        <span
                                                            class="avatar avatar-sm avatar-rounded bg-<?php echo $color; ?>">
                                                            <i class="ti ti-plant fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill">
                                                        <p class="mb-0">
                                                            <?php echo htmlspecialchars($productType->name); ?></p>
                                                        <span class="text-muted fs-12">
                                                            <?php echo number_format($productType->delivery_count); ?>
                                                            Deliveries
                                                            (Quality: <?php echo $qualityRate; ?>% Grade A)
                                                        </span>
                                                    </div>
                                                    <div class="text-end">
                                                        <div class="mb-1">
                                                            <span class="badge bg-<?php echo $color; ?>-transparent">
                                                                KES
                                                                <?php echo number_format($productType->total_value, 0); ?>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <small class="text-muted">Avg: KES
                                                                <?php echo number_format($productType->avg_value, 0); ?></small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress mt-2" style="height: 5px;">
                                                    <div class="progress-bar bg-<?php echo $color; ?>"
                                                        role="progressbar" style="width: <?php echo $percentage; ?>%"
                                                        aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0"
                                                        aria-valuemax="100">
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                    $counter++;
                                }
                            } else {
                                ?>
                                            <div class="list-group-item">
                                                <div class="text-center">
                                                    <p class="text-muted mb-0">No production data available</p>
                                                </div>
                                            </div>
                                            <?php
                            }
                            ?>

                                            <!-- Total Production Summary -->
                                            <div class="list-group-item bg-light">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2">
                                                        <span class="avatar avatar-sm avatar-rounded bg-secondary">
                                                            <i class="ti ti-chart-pie fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill">
                                                        <p class="mb-0 fw-semibold">Total Production Value</p>
                                                        <span class="text-muted fs-12">
                                                            <?php 
                                                $totalProductionQuery = "SELECT SUM(pd.total_value) as value FROM produce_deliveries pd
                                                                        JOIN farm_products fp ON pd.farm_product_id = fp.id
                                                                        JOIN farms f ON fp.farm_id = f.id
                                                                        WHERE f.farmer_id = $farmerId";
                                                $totalProduction = $app->select_one($totalProductionQuery);
                                                echo 'KES ' . number_format($totalProduction->value ?? 0, 0) . ' Across All Crops';
                                            ?>
                                                        </span>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="badge bg-secondary-transparent">
                                                            <?php echo number_format($total); ?> Total Deliveries
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Production Insights -->
                            <div class="col-xl-4">
                                <div class="card custom-card">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <i class="ti ti-lightbulb me-2" style="color: #4A220F;"></i>
                                            Production Insights
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <!-- Top Performing Crop -->
                                        <div class="alert alert-outline-success mb-3" role="alert">
                                            <div class="d-flex align-items-start">
                                                <div class="me-2">
                                                    <span class="avatar avatar-sm svg-success">
                                                        <i class="ti ti-trending-up fs-14"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill">
                                                    <p class="fw-semibold mb-1">Top Performing Crop</p>
                                                    <p class="op-8 mb-1 fs-12">
                                                        <?php 
                                            if ($productTypes && count($productTypes) > 0) {
                                                $topProduct = $productTypes[0];
                                                echo htmlspecialchars($topProduct->name) . " - " . $topProduct->delivery_count . " deliveries";
                                            } else {
                                                echo "No data available";
                                            }
                                        ?>
                                                    </p>
                                                    <small class="text-success fw-semibold">Focus on expanding this
                                                        crop</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Quality Improvement -->
                                        <div class="alert alert-outline-warning mb-3" role="alert">
                                            <div class="d-flex align-items-start">
                                                <div class="me-2">
                                                    <span class="avatar avatar-sm svg-warning">
                                                        <i class="ti ti-award fs-14"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill">
                                                    <p class="fw-semibold mb-1">Quality Improvement</p>
                                                    <p class="op-8 mb-1 fs-12">
                                                        <?php 
                                            $overallQualityQuery = "SELECT 
                                                COUNT(*) as total_graded,
                                                SUM(CASE WHEN quality_grade = 'A' THEN 1 ELSE 0 END) as grade_a_count
                                                FROM produce_deliveries pd
                                                JOIN farm_products fp ON pd.farm_product_id = fp.id
                                                JOIN farms f ON fp.farm_id = f.id
                                                WHERE f.farmer_id = $farmerId AND quality_grade IS NOT NULL";
                                            $overallQuality = $app->select_one($overallQualityQuery);
                                            
                                            $overallQualityRate = ($overallQuality->total_graded > 0) ? 
                                                round(($overallQuality->grade_a_count / $overallQuality->total_graded) * 100) : 0;
                                            
                                            if ($overallQualityRate < 60) {
                                                echo "Current Grade A rate: " . $overallQualityRate . "% - needs improvement";
                                            } else {
                                                echo "Good quality rate: " . $overallQualityRate . "% Grade A deliveries";
                                            }
                                        ?>
                                                    </p>
                                                    <small class="text-warning fw-semibold">
                                                        <?php echo $overallQualityRate < 60 ? "Focus on quality enhancement" : "Maintain current standards"; ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Seasonal Planning -->
                                        <div class="alert alert-outline-info mb-3" role="alert">
                                            <div class="d-flex align-items-start">
                                                <div class="me-2">
                                                    <span class="avatar avatar-sm svg-info">
                                                        <i class="ti ti-calendar fs-14"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill">
                                                    <p class="fw-semibold mb-1">Seasonal Planning</p>
                                                    <p class="op-8 mb-1 fs-12">
                                                        <?php 
                                            $currentMonth = date('n');
                                            $plantingMonths = [3, 4, 5]; // March, April, May
                                            $harvestMonths = [9, 10, 11]; // September, October, November
                                            
                                            if (in_array($currentMonth, $plantingMonths)) {
                                                echo "Planting season - consider input credits for new crops";
                                            } elseif (in_array($currentMonth, $harvestMonths)) {
                                                echo "Harvest season - focus on quality and timely delivery";
                                            } else {
                                                echo "Planning period - analyze performance and prepare for next season";
                                            }
                                        ?>
                                                    </p>
                                                    <small class="text-info fw-semibold">Optimize timing for better
                                                        yields</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Diversification Strategy -->
                                        <div class="alert"
                                            style="border: 1px solid rgba(74, 34, 15, 0.2); background-color: rgba(74, 34, 15, 0.05);"
                                            role="alert">
                                            <div class="d-flex align-items-start">
                                                <div class="me-2">
                                                    <span class="avatar avatar-sm"
                                                        style="background-color: rgba(74, 34, 15, 0.1); color: #4A220F;">
                                                        <i class="ti ti-chart-pie fs-14"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill">
                                                    <p class="fw-semibold mb-1">Crop Diversification</p>
                                                    <p class="op-8 mb-1 fs-12">
                                                        <?php
                                                  // Ensure $productTypes is properly defined as an array
                                                  if (!isset($productTypes) || !is_array($productTypes)) {
                                                      // If $productTypes doesn't exist, fetch it
                                                      $productQuery = "SELECT DISTINCT pt.name 
                                                                      FROM product_types pt
                                                                      INNER JOIN farm_products fp ON pt.id = fp.product_type_id
                                                                      INNER JOIN farms f ON fp.farm_id = f.id
                                                                      INNER JOIN farmers fr ON f.farmer_id = fr.id
                                                                      WHERE fr.user_id = $userId AND fp.is_active = 1";
                                                      $productTypes = $app->select_all($productQuery);
                                                  }
                                                  
                                                  // Get crop count safely
                                                  $cropCount = is_array($productTypes) ? count($productTypes) : 0;
                                                  
                                                  if ($cropCount >= 4) {
                                                      echo "Well diversified with " . $cropCount . " different crops";
                                                  } elseif ($cropCount >= 2) {
                                                      echo "Moderately diversified - consider adding " . (4 - $cropCount) . " more crop types";
                                                  } else {
                                                      echo "Limited diversification - high dependency on single crop";
                                                  }
                                                  ?>
                                                        </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Financial Overview and Credit Management -->
                    <div class="col-xl-12">
                        <div class="row">
                            <!-- Financial Overview Card -->
                            <div class="col-xl-8">
                                <div class="card custom-card">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <i class="ti ti-wallet me-2" style="color: #70A136;"></i>
                                            Financial Overview & Credit Status
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="list-group list-group-flush">
                                            <!-- Active Loans -->
                                            <?php
                                                  $activeLoansQuery = "SELECT 
                                                      lt.name as loan_type,
                                                      al.approved_amount,
                                                      al.remaining_balance,
                                                      al.interest_rate,
                                                      al.disbursement_date,
                                                      al.expected_completion_date,
                                                      b.name as bank_name
                                                  FROM approved_loans al
                                                  JOIN loan_applications la ON al.loan_application_id = la.id
                                                  JOIN loan_types lt ON la.loan_type_id = lt.id
                                                  LEFT JOIN banks b ON la.bank_id = b.id
                                                  WHERE la.farmer_id = $farmerId AND al.status = 'active'
                                                  ORDER BY al.remaining_balance DESC
                                                  LIMIT 3";
                                                  $activeLoans = $app->select_all($activeLoansQuery);
                                                  
                                                  if ($activeLoans && count($activeLoans) > 0) {
                                                      foreach ($activeLoans as $index => $loan) {
                                                          $repaymentProgress = ($loan->approved_amount > 0) ? 
                                                              round((($loan->approved_amount - $loan->remaining_balance) / $loan->approved_amount) * 100) : 0;
                                                          $colors = ['primary', 'success', 'warning'];
                                                          $color = $colors[$index % count($colors)];
                                              ?>
                                            <div class="list-group-item">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2">
                                                        <span
                                                            class="avatar avatar-sm avatar-rounded bg-<?php echo $color; ?>">
                                                            <i class="ti ti-credit-card fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill">
                                                        <p class="mb-0">
                                                            <?php echo htmlspecialchars($loan->loan_type); ?></p>
                                                        <span class="text-muted fs-12">
                                                            <?php echo $loan->bank_name ? htmlspecialchars($loan->bank_name) : 'SACCO'; ?>
                                                            •
                                                            <?php echo $loan->interest_rate; ?>% Interest
                                                        </span>
                                                    </div>
                                                    <div class="text-end">
                                                        <div class="mb-1">
                                                            <span class="badge bg-<?php echo $color; ?>-transparent">
                                                                KES
                                                                <?php echo number_format($loan->remaining_balance, 0); ?>
                                                                Remaining
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <small class="text-muted">Of KES
                                                                <?php echo number_format($loan->approved_amount, 0); ?></small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress mt-2" style="height: 5px;">
                                                    <div class="progress-bar bg-<?php echo $color; ?>"
                                                        role="progressbar"
                                                        style="width: <?php echo $repaymentProgress; ?>%"
                                                        aria-valuenow="<?php echo $repaymentProgress; ?>"
                                                        aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between mt-1">
                                                    <small class="text-muted"><?php echo $repaymentProgress; ?>%
                                                        Repaid</small>
                                                    <small class="text-muted">Due:
                                                        <?php echo date('M Y', strtotime($loan->expected_completion_date)); ?></small>
                                                </div>
                                            </div>
                                            <?php
                                                       }
                                                   } else {
                                               ?>
                                            <div class="list-group-item">
                                                <div class="text-center py-3">
                                                    <i class="ti ti-info-circle fs-24 text-muted mb-2"></i>
                                                    <p class="text-muted mb-0">No active loans</p>
                                                    <small class="text-muted">Consider applying for growth
                                                        financing</small>
                                                </div>
                                            </div>
                                            <?php } ?>

                                            <!-- Active Input Credits -->
                                            <?php
                                                     $activeCreditsQuery = "SELECT 
                                                         aic.approved_amount,
                                                         aic.remaining_balance,
                                                         aic.credit_percentage,
                                                         aic.repayment_percentage,
                                                         aic.fulfillment_date,
                                                         a.name as agrovet_name
                                                     FROM approved_input_credits aic
                                                     JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                                     JOIN agrovets a ON ica.agrovet_id = a.id
                                                     WHERE ica.farmer_id = $farmerId AND aic.status = 'active'
                                                     ORDER BY aic.remaining_balance DESC
                                                     LIMIT 2";
                                                     $activeCredits = $app->select_all($activeCreditsQuery);
                                                     
                                                     if ($activeCredits && count($activeCredits) > 0) {
                                                         foreach ($activeCredits as $index => $credit) {
                                                             $repaymentProgress = ($credit->approved_amount > 0) ? 
                                                                 round((($credit->approved_amount - $credit->remaining_balance) / $credit->approved_amount) * 100) : 0;
                                                             $colors = ['info', 'purple'];
                                                             $color = $colors[$index % count($colors)];
                                                 ?>
                                            <div class="list-group-item">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2">
                                                        <span
                                                            class="avatar avatar-sm avatar-rounded bg-<?php echo $color; ?>">
                                                            <i class="ti ti-package fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill">
                                                        <p class="mb-0">Input Credit</p>
                                                        <span class="text-muted fs-12">
                                                            <?php echo htmlspecialchars($credit->agrovet_name); ?> •
                                                            <?php echo $credit->repayment_percentage; ?>% Deduction Rate
                                                        </span>
                                                    </div>
                                                    <div class="text-end">
                                                        <div class="mb-1">
                                                            <span class="badge bg-<?php echo $color; ?>-transparent">
                                                                KES
                                                                <?php echo number_format($credit->remaining_balance, 0); ?>
                                                                Remaining
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <small class="text-muted">Of KES
                                                                <?php echo number_format($credit->approved_amount, 0); ?></small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress mt-2" style="height: 5px;">
                                                    <div class="progress-bar bg-<?php echo $color; ?>"
                                                        role="progressbar"
                                                        style="width: <?php echo $repaymentProgress; ?>%"
                                                        aria-valuenow="<?php echo $repaymentProgress; ?>"
                                                        aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between mt-1">
                                                    <small class="text-muted"><?php echo $repaymentProgress; ?>%
                                                        Repaid</small>
                                                    <small class="text-muted">From:
                                                        <?php echo date('M Y', strtotime($credit->fulfillment_date)); ?></small>
                                                </div>
                                            </div>
                                            <?php
                                                        }
                                                    }
                                                ?>

                                            <!-- Total Financial Summary -->
                                            <div class="list-group-item bg-light">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2">
                                                        <span class="avatar avatar-sm avatar-rounded bg-secondary">
                                                            <i class="ti ti-calculator fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill">
                                                        <p class="mb-0 fw-semibold">Total Outstanding Debt</p>
                                                        <span class="text-muted fs-12">
                                                            <?php 
                                                    $totalDebtQuery = "SELECT 
                                                        (SELECT COALESCE(SUM(al.remaining_balance), 0) FROM approved_loans al
                                                         JOIN loan_applications la ON al.loan_application_id = la.id
                                                         WHERE la.farmer_id = $farmerId AND al.status = 'active') +
                                                        (SELECT COALESCE(SUM(aic.remaining_balance), 0) FROM approved_input_credits aic
                                                         JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                                         WHERE ica.farmer_id = $farmerId AND aic.status = 'active') as total_debt";
                                                    $totalDebt = $app->select_one($totalDebtQuery);
                                                    echo 'KES ' . number_format($totalDebt->total_debt ?? 0, 0) . ' Total Debt';
                                                ?>
                                                        </span>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="badge bg-secondary-transparent">
                                                            <?php 
                                                    $loanCount = is_array($activeLoans) ? count($activeLoans) : 0;
                                                    $creditCount = is_array($activeCredits) ? count($activeCredits) : 0;
                                                    echo ($loanCount + $creditCount) . ' Active Credits';
                                                ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Financial Recommendations -->
                            <div class="col-xl-4">
                                <div class="card custom-card">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <i class="ti ti-lightbulb me-2" style="color: #4A220F;"></i>
                                            Financial Recommendations
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <!-- Credit Score Status -->
                                        <div class="alert alert-outline-success mb-3" role="alert">
                                            <div class="d-flex align-items-start">
                                                <div class="me-2">
                                                    <span class="avatar avatar-sm svg-success">
                                                        <i class="ti ti-shield-check fs-14"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill">
                                                    <p class="fw-semibold mb-1">Credit Score Status</p>
                                                    <p class="op-8 mb-1 fs-12">
                                                        <?php 
                                            // Simple credit scoring based on repayment history
                                            $repaymentHistoryQuery = "SELECT 
                                                COUNT(*) as total_repayments,
                                                COUNT(CASE WHEN payment_date <= (SELECT expected_completion_date FROM approved_loans WHERE id = lr.approved_loan_id) THEN 1 END) as on_time_payments
                                                FROM loan_repayments lr
                                                JOIN approved_loans al ON lr.approved_loan_id = al.id
                                                JOIN loan_applications la ON al.loan_application_id = la.id
                                                WHERE la.farmer_id = $farmerId";
                                            $repaymentHistory = $app->select_one($repaymentHistoryQuery);
                                            
                                            $creditScore = 750; // Base score
                                            if ($repaymentHistory->total_repayments > 0) {
                                                $onTimeRate = ($repaymentHistory->on_time_payments / $repaymentHistory->total_repayments) * 100;
                                                if ($onTimeRate >= 90) $creditScore = 800;
                                                elseif ($onTimeRate >= 70) $creditScore = 750;
                                                else $creditScore = 650;
                                            }
                                            
                                            echo "Current score: " . $creditScore . " - ";
                                            if ($creditScore >= 800) echo "Excellent";
                                            elseif ($creditScore >= 750) echo "Good";
                                            else echo "Fair";
                                        ?>
                                                    </p>
                                                    <small class="text-success fw-semibold">Maintain timely payments for
                                                        better rates</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Loan Opportunity -->
                                        <div class="alert alert-outline-primary mb-3" role="alert">
                                            <div class="d-flex align-items-start">
                                                <div class="me-2">
                                                    <span class="avatar avatar-sm svg-primary">
                                                        <i class="ti ti-coins fs-14"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill">
                                                    <p class="fw-semibold mb-1">Loan Opportunity</p>
                                                    <p class="op-8 mb-1 fs-12">
                                                        <?php 
                                                           
                                                           // Ensure $activeLoans is properly defined as an array
                                                           if (!isset($activeLoans) || !is_array($activeLoans)) {
                                                               $activeLoans = [];
                                                           }
                                                           
                                                           // Get active loans count safely
                                                           $activeLoanCount = is_array($activeLoans) ? count($activeLoans) : 0;
                                                           
                                                           if ($balance > 100000 && $activeLoanCount == 0) {
                                                               echo "Strong financial position - eligible for expansion loans up to KES 500,000";
                                                           } elseif ($activeLoanCount == 0) {
                                                               echo "No active loans - consider agricultural financing for growth";
                                                           } else {
                                                               echo "Focus on current loan repayment before taking new credit";
                                                           }
                                                           ?>

                                                    </p>
                                                    <small class="text-primary fw-semibold">
                                                        <a href="apply-loan" class="text-primary">Apply for Agricultural
                                                            Loan</a>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Input Credit Planning -->
                                        <div class="alert alert-outline-warning mb-3" role="alert">
                                            <div class="d-flex align-items-start">
                                                <div class="me-2">
                                                    <span class="avatar avatar-sm svg-warning">
                                                        <i class="ti ti-calendar fs-14"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill">
                                                    <p class="fw-semibold mb-1">Input Credit Planning</p>
                                                    <p class="op-8 mb-1 fs-12">
                                                        <?php 
                                            $currentMonth = date('n');
                                            if ($currentMonth >= 2 && $currentMonth <= 4) {
                                                echo "Planting season approaching - apply for input credits now";
                                            } elseif ($currentMonth >= 9 && $currentMonth <= 11) {
                                                echo "Post-harvest period - good time to settle input credit balances";
                                            } else {
                                                echo "Plan ahead for next season's input requirements";
                                            }
                                        ?>
                                                    </p>
                                                    <small class="text-warning fw-semibold">
                                                        <a href="apply-input-credit" class="text-warning">Apply for
                                                            Input Credits</a>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Financial Health -->
                                        <div class="alert"
                                            style="border: 1px solid rgba(74, 34, 15, 0.2); background-color: rgba(74, 34, 15, 0.05);"
                                            role="alert">
                                            <div class="d-flex align-items-start">
                                                <div class="me-2">
                                                    <span class="avatar avatar-sm"
                                                        style="background-color: rgba(74, 34, 15, 0.1); color: #4A220F;">
                                                        <i class="ti ti-heart fs-14"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill">
                                                    <p class="fw-semibold mb-1">Financial Health</p>
                                                    <p class="op-8 mb-1 fs-12">
                                                        <?php 
                                            $debtToIncomeRatio = 0;
                                            $monthlyIncome = $app->select_one("SELECT AVG(total_value) as avg_income 
                                                FROM produce_deliveries pd
                                                JOIN farm_products fp ON pd.farm_product_id = fp.id
                                                JOIN farms f ON fp.farm_id = f.id
                                                WHERE f.farmer_id = $farmerId AND pd.status = 'paid'
                                                AND pd.delivery_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)");
                                            
                                            if (($monthlyIncome->avg_income ?? 0) > 0) {
                                                $debtToIncomeRatio = (($totalDebt->total_debt ?? 0) / ($monthlyIncome->avg_income * 12)) * 100;
                                            }
                                            
                                            if ($debtToIncomeRatio < 30) {
                                                echo "Healthy debt-to-income ratio: " . number_format($debtToIncomeRatio, 1) . "%";
                                            } elseif ($debtToIncomeRatio < 50) {
                                                echo "Moderate debt level: " . number_format($debtToIncomeRatio, 1) . "% - monitor closely";
                                            } else {
                                                echo "High debt level: " . number_format($debtToIncomeRatio, 1) . "% - focus on repayment";
                                            }
                                        ?>
                                                    </p>
                                                    <small class="fw-semibold" style="color: #4A220F;">
                                                        <?php 
                                            if ($debtToIncomeRatio < 30) echo "Good financial position";
                                            elseif ($debtToIncomeRatio < 50) echo "Maintain current strategy";
                                            else echo "Consider debt consolidation";
                                        ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- recommendations -->
                <div class="row">
                    <!-- Supply Chain & Market Performance Graph -->
                    <div class="col-xl-8">
                        <div class="card custom-card">
                            <div class="card-header justify-content-between">
                                <div class="card-title">
                                    <i class="ti ti-chart-line me-2" style="color: #70A136;"></i>
                                    Supply Chain & Market Performance
                                </div>
                            </div>
                            <div class="card-body">
                                <?php include "graphs/supply-chain-performance.php" ?>
                            </div>
                        </div>
                    </div>

                    <!-- Market Access Statistics Card -->
                    <div class="col-xl-4">
                        <div class="card custom-card">
                            <div class="card-header">
                                <div class="card-title">
                                    <i class="ti ti-truck me-2" style="color: #4A220F;"></i>
                                    Market Access Metrics
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    <!-- Delivery Success Rate -->
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <span class="avatar avatar-sm avatar-rounded bg-primary">
                                                    <i class="ti ti-check-circle fs-16"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <p class="mb-0">Delivery Success Rate</p>
                                                <span class="text-muted fs-12">
                                                    <?php 
                                                      $deliverySuccessQuery = "SELECT 
                                                          COUNT(*) as total_deliveries,
                                                          SUM(CASE WHEN status IN ('verified', 'paid') THEN 1 ELSE 0 END) as successful_deliveries,
                                                          SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_deliveries
                                                          FROM produce_deliveries pd
                                                          JOIN farm_products fp ON pd.farm_product_id = fp.id
                                                          JOIN farms f ON fp.farm_id = f.id
                                                          WHERE f.farmer_id = $farmerId";
                                                      $deliverySuccess = $app->select_one($deliverySuccessQuery);
                                                      
                                                      $successRate = ($deliverySuccess->total_deliveries > 0) ? 
                                                          round(($deliverySuccess->successful_deliveries / $deliverySuccess->total_deliveries) * 100, 1) : 0;
                                                      
                                                      echo $successRate . "% (" . $deliverySuccess->successful_deliveries . "/" . $deliverySuccess->total_deliveries . " deliveries)";
                                                  ?>
                                                </span>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-primary-transparent">
                                                    <?php echo $successRate >= 90 ? 'Excellent' : ($successRate >= 70 ? 'Good' : 'Needs Improvement'); ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="progress mt-2" style="height: 5px;">
                                            <div class="progress-bar bg-primary" role="progressbar"
                                                style="width: <?php echo $successRate; ?>%"
                                                aria-valuenow="<?php echo $successRate; ?>" aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Average Payment Time -->
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <span class="avatar avatar-sm avatar-rounded bg-success">
                                                    <i class="ti ti-clock fs-16"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <p class="mb-0">Average Payment Time</p>
                                                <span class="text-muted fs-12">
                                                    <?php 
                                                     $paymentTimeQuery = "SELECT 
                                                         AVG(DATEDIFF(sale_date, delivery_date)) as avg_payment_days,
                                                         COUNT(*) as paid_deliveries
                                                         FROM produce_deliveries pd
                                                         JOIN farm_products fp ON pd.farm_product_id = fp.id
                                                         JOIN farms f ON fp.farm_id = f.id
                                                         WHERE f.farmer_id = $farmerId 
                                                         AND status = 'paid' 
                                                         AND sale_date IS NOT NULL";
                                                     $paymentTime = $app->select_one($paymentTimeQuery);
                                                     
                                                     $avgDays = round($paymentTime->avg_payment_days ?? 0);
                                                     echo $avgDays . " days average (" . ($paymentTime->paid_deliveries ?? 0) . " transactions)";
                                                 ?>
                                                </span>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-success-transparent">
                                                    <?php echo $avgDays <= 7 ? 'Fast' : ($avgDays <= 14 ? 'Normal' : 'Slow'); ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="progress mt-2" style="height: 5px;">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: <?php echo max(0, 100 - ($avgDays * 3)); ?>%"
                                                aria-valuenow="<?php echo max(0, 100 - ($avgDays * 3)); ?>"
                                                aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Market Price Performance -->
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <span class="avatar avatar-sm avatar-rounded"
                                                    style="background-color: rgba(112, 161, 54, 0.1); color: #70A136;">
                                                    <i class="ti ti-trending-up fs-16"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <p class="mb-0">Market Price Performance</p>
                                                <span class="text-muted fs-12">
                                                    <?php 
                                                       $pricePerformanceQuery = "SELECT 
                                                           AVG(pd.unit_price) as avg_unit_price,
                                                           MAX(pd.unit_price) as max_unit_price,
                                                           MIN(pd.unit_price) as min_unit_price
                                                           FROM produce_deliveries pd
                                                           JOIN farm_products fp ON pd.farm_product_id = fp.id
                                                           JOIN farms f ON fp.farm_id = f.id
                                                           WHERE f.farmer_id = $farmerId 
                                                           AND pd.delivery_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)";
                                                       $pricePerformance = $app->select_one($pricePerformanceQuery);
                                                       
                                                       echo "KES " . number_format($pricePerformance->avg_unit_price ?? 0, 0) . " avg unit price";
                                                   ?>
                                                </span>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge"
                                                    style="background-color: rgba(112, 161, 54, 0.1); color: #70A136;">
                                                    Market Rate
                                                </span>
                                            </div>
                                        </div>
                                        <div class="progress mt-2" style="height: 5px;">
                                            <div class="progress-bar" style="background-color: #70A136;"
                                                role="progressbar" style="width: 80%" aria-valuenow="80"
                                                aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Quality Grade Distribution -->
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <span class="avatar avatar-sm avatar-rounded bg-warning">
                                                    <i class="ti ti-award fs-16"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <p class="mb-0">Quality Grade Distribution</p>
                                                <span class="text-muted fs-12">
                                                    <?php 
                                                        $qualityDistributionQuery = "SELECT 
                                                            SUM(CASE WHEN quality_grade = 'A' THEN 1 ELSE 0 END) as grade_a,
                                                            SUM(CASE WHEN quality_grade = 'B' THEN 1 ELSE 0 END) as grade_b,
                                                            SUM(CASE WHEN quality_grade = 'C' THEN 1 ELSE 0 END) as grade_c,
                                                            COUNT(*) as total_graded
                                                            FROM produce_deliveries pd
                                                            JOIN farm_products fp ON pd.farm_product_id = fp.id
                                                            JOIN farms f ON fp.farm_id = f.id
                                                            WHERE f.farmer_id = $farmerId 
                                                            AND quality_grade IS NOT NULL";
                                                        $qualityDist = $app->select_one($qualityDistributionQuery);
                                                        
                                                        $gradeAPercent = ($qualityDist->total_graded > 0) ? 
                                                            round(($qualityDist->grade_a / $qualityDist->total_graded) * 100) : 0;
                                                        
                                                        echo "A: " . $qualityDist->grade_a . " | B: " . $qualityDist->grade_b . " | C: " . $qualityDist->grade_c;
                                                    ?>
                                                </span>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-warning-transparent">
                                                    <?php echo $gradeAPercent; ?>% Grade A
                                                </span>
                                            </div>
                                        </div>
                                        <div class="progress mt-2" style="height: 5px;">
                                            <div class="progress-bar bg-warning" role="progressbar"
                                                style="width: <?php echo $gradeAPercent; ?>%"
                                                aria-valuenow="<?php echo $gradeAPercent; ?>" aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Supply Chain Efficiency -->
                                    <div class="list-group-item bg-light">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <span class="avatar avatar-sm avatar-rounded"
                                                    style="background-color: rgba(74, 34, 15, 0.1); color: #4A220F;">
                                                    <i class="ti ti-truck-delivery fs-16"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <p class="mb-0 fw-semibold">Supply Chain Efficiency Score</p>
                                                <span class="text-muted fs-12">
                                                    <?php 
                                                           // Calculate efficiency score based on success rate, payment time, and quality
                                                           $efficiencyScore = 0;
                                                           if ($successRate >= 90) $efficiencyScore += 40;
                                                           elseif ($successRate >= 70) $efficiencyScore += 25;
                                                           
                                                           if ($avgDays <= 7) $efficiencyScore += 30;
                                                           elseif ($avgDays <= 14) $efficiencyScore += 20;
                                                           
                                                           if ($gradeAPercent >= 70) $efficiencyScore += 30;
                                                           elseif ($gradeAPercent >= 50) $efficiencyScore += 20;
                                                           
                                                           echo 'Score: ' . $efficiencyScore . '/100 (Success Rate + Payment Speed + Quality)';
                                                       ?>
                                                </span>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge"
                                                    style="background-color: rgba(74, 34, 15, 0.1); color: #4A220F;">
                                                    <?php 
                                                           if ($efficiencyScore >= 80) echo 'Excellent';
                                                           elseif ($efficiencyScore >= 60) echo 'Good';
                                                           elseif ($efficiencyScore >= 40) echo 'Fair';
                                                           else echo 'Poor';
                                                       ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="progress mt-2" style="height: 5px;">
                                            <div class="progress-bar" style="background-color: #4A220F;"
                                                role="progressbar" style="width: <?php echo $efficiencyScore; ?>%"
                                                aria-valuenow="<?php echo $efficiencyScore; ?>" aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Supply Chain Recommendations Section - Separate Row -->
                <div class="row mt-4">
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-header justify-content-between">
                                <div class="card-title">
                                    <i class="ti ti-bulb me-2" style="color: #70A136;"></i>
                                    Supply Chain & Market Recommendations
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Delivery Optimization -->
                                    <div class="col-xl-4 col-lg-6 col-md-12">
                                        <div class="alert alert-outline-success" role="alert">
                                            <div class="d-flex align-items-start">
                                                <div class="me-2">
                                                    <span class="avatar avatar-sm svg-success">
                                                        <i class="ti ti-truck fs-14"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill">
                                                    <p class="fw-semibold mb-1">Delivery Optimization</p>
                                                    <p class="op-8 mb-1 fs-12">
                                                        <?php 
                                                              if ($successRate < 70) {
                                                                  echo "Improve delivery success rate - currently at " . $successRate . "%. Focus on quality control and timing.";
                                                              } elseif ($successRate < 90) {
                                                                  echo "Good delivery performance at " . $successRate . "%. Aim for 90%+ success rate.";
                                                              } else {
                                                                  echo "Excellent delivery success rate! Maintain current quality standards.";
                                                              }
                                                          ?>
                                                    </p>
                                                    <small class="text-success fw-semibold">
                                                        <?php echo $successRate < 90 ? "Focus on pre-delivery quality checks" : "Maintain excellence"; ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Payment Terms -->
                                    <div class="col-xl-4 col-lg-6 col-md-12">
                                        <div class="alert alert-outline-primary" role="alert">
                                            <div class="d-flex align-items-start">
                                                <div class="me-2">
                                                    <span class="avatar avatar-sm svg-primary">
                                                        <i class="ti ti-clock fs-14"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill">
                                                    <p class="fw-semibold mb-1">Payment Terms</p>
                                                    <p class="op-8 mb-1 fs-12">
                                                        <?php 
                                                             if ($avgDays > 14) {
                                                                 echo "Payment takes " . $avgDays . " days on average. Negotiate better terms with buyers.";
                                                             } elseif ($avgDays > 7) {
                                                                 echo "Payment in " . $avgDays . " days is acceptable. Consider incentives for faster payment.";
                                                             } else {
                                                                 echo "Excellent payment terms! Average " . $avgDays . " days shows strong buyer relationships.";
                                                             }
                                                         ?>
                                                    </p>
                                                    <small class="text-primary fw-semibold">
                                                        <?php echo $avgDays > 14 ? "Negotiate better payment terms" : "Maintain good buyer relationships"; ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Market Diversification -->
                                    <div class="col-xl-4 col-lg-6 col-md-12">
                                        <div class="alert alert-outline-warning" role="alert">
                                            <div class="d-flex align-items-start">
                                                <div class="me-2">
                                                    <span class="avatar avatar-sm svg-warning">
                                                        <i class="ti ti-users fs-14"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill">
                                                    <p class="fw-semibold mb-1">Market Diversification</p>
                                                    <p class="op-8 mb-1 fs-12">
                                                        <?php 
                                                           $buyerDiversityQuery = "SELECT COUNT(DISTINCT received_by) as unique_buyers 
                                                                                  FROM produce_deliveries pd
                                                                                  JOIN farm_products fp ON pd.farm_product_id = fp.id
                                                                                  JOIN farms f ON fp.farm_id = f.id
                                                                                  WHERE f.farmer_id = $farmerId AND received_by IS NOT NULL";
                                                           $buyerDiversity = $app->select_one($buyerDiversityQuery);
                                                           $uniqueBuyers = $buyerDiversity->unique_buyers ?? 0;
                                                           
                                                           if ($uniqueBuyers < 2) {
                                                               echo "Limited to " . $uniqueBuyers . " buyer. Diversify your market to reduce risk.";
                                                           } elseif ($uniqueBuyers < 4) {
                                                               echo "Good buyer diversity with " . $uniqueBuyers . " buyers. Consider expanding further.";
                                                           } else {
                                                               echo "Excellent market diversification with " . $uniqueBuyers . " different buyers.";
                                                           }
                                                       ?>
                                                    </p>
                                                    <small class="text-warning fw-semibold">
                                                        <?php echo $uniqueBuyers < 3 ? "Expand buyer network" : "Maintain buyer relationships"; ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Second Row of Recommendations -->
                                <div class="row mt-3">
                                    <!-- Pricing Strategy -->
                                    <div class="col-xl-6 col-lg-12">
                                        <div class="alert alert-outline-info" role="alert">
                                            <div class="d-flex align-items-start">
                                                <div class="me-2">
                                                    <span class="avatar avatar-sm svg-info">
                                                        <i class="ti ti-chart-line fs-14"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill">
                                                    <p class="fw-semibold mb-1">Pricing Strategy</p>
                                                    <p class="op-8 mb-1 fs-12">
                                                        Current average unit price: KES
                                                        <?php echo number_format($pricePerformance->avg_unit_price ?? 0, 0); ?>.
                                                        Monitor market prices and adjust timing for premium sales during
                                                        peak demand periods.
                                                    </p>
                                                    <div class="mt-2">
                                                        <span class="badge bg-info-transparent text-info fs-11 me-1">
                                                            Track Market Prices
                                                        </span>
                                                        <span class="badge bg-info-transparent text-info fs-11">
                                                            Seasonal Timing
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Quality Improvement -->
                                    <div class="col-xl-6 col-lg-12">
                                        <div class="alert"
                                            style="border: 1px solid rgba(112, 161, 54, 0.2); background-color: rgba(112, 161, 54, 0.05);"
                                            role="alert">
                                            <div class="d-flex align-items-start">
                                                <div class="me-2">
                                                    <span class="avatar avatar-sm"
                                                        style="background-color: rgba(112, 161, 54, 0.1); color: #70A136;">
                                                        <i class="ti ti-award fs-14"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill">
                                                    <p class="fw-semibold mb-1">Quality Enhancement</p>
                                                    <p class="op-8 mb-1 fs-12">
                                                        Current Grade A rate: <?php echo $gradeAPercent; ?>%.
                                                        <?php 
                                                            if ($gradeAPercent < 60) {
                                                                echo "Focus on post-harvest handling and storage to improve quality grades and command premium prices.";
                                                            } else {
                                                                echo "Good quality standards. Maintain consistent practices for premium market access.";
                                                            }
                                                        ?>
                                                    </p>
                                                    <div class="mt-2">
                                                        <span class="badge fs-11"
                                                            style="background-color: rgba(112, 161, 54, 0.1); color: #70A136;">
                                                            <?php echo $gradeAPercent < 60 ? "Improve Handling" : "Maintain Standards"; ?>
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
                <div class="row">
                    <!-- Farmer Activity Logs -->
                    <div class="col-xxl-12 col-xl-12">
                        <div class="card custom-card">
                            <div class="card-header justify-content-between">
                                <div class="card-title">
                                    <i class="ri-pulse-line me-2" style="color: #70A136;"></i>
                                    Recent Farm Activities
                                </div>
                            </div>
                            <div class="card-body">
                                <div>
                                    <ul class="list-unstyled mb-0 crm-recent-activity">
                                        <?php
                            // Get recent produce deliveries
                            $produceDeliveries = $app->select_all("
                                SELECT pd.*, 
                                       pt.name as product_name,
                                       u.first_name, u.last_name,
                                       'produce_delivery' as log_type
                                FROM produce_deliveries pd
                                JOIN farm_products fp ON pd.farm_product_id = fp.id
                                JOIN farms f ON fp.farm_id = f.id
                                JOIN product_types pt ON fp.product_type_id = pt.id
                                LEFT JOIN users u ON pd.received_by = u.id
                                WHERE f.farmer_id = $farmerId
                                ORDER BY pd.created_at DESC
                                LIMIT 6
                            ");
                            
                            // Get recent farmer account transactions
                            $farmerTransactions = $app->select_all("
                                SELECT fat.*, 
                                       u.first_name, u.last_name, 
                                       'farmer_transaction' as log_type
                                FROM farmer_account_transactions fat
                                JOIN farmer_accounts fa ON fat.farmer_account_id = fa.id
                                LEFT JOIN users u ON fat.processed_by = u.id
                                WHERE fa.farmer_id = $farmerId
                                ORDER BY fat.created_at DESC
                                LIMIT 4
                            ");
                            
                            // Get recent loan activities
                            $loanActivities = $app->select_all("
                                SELECT ll.*, 
                                       u.first_name, u.last_name,
                                       la.amount_requested,
                                       lt.name as loan_type_name,
                                       b.name as bank_name,
                                       'loan_activity' as log_type
                                FROM loan_logs ll
                                JOIN users u ON ll.user_id = u.id
                                JOIN loan_applications la ON ll.loan_application_id = la.id
                                JOIN loan_types lt ON la.loan_type_id = lt.id
                                LEFT JOIN banks b ON la.bank_id = b.id
                                WHERE la.farmer_id = $farmerId
                                ORDER BY ll.created_at DESC
                                LIMIT 4
                            ");
                            
                            // Get recent input credit activities
                            $creditActivities = $app->select_all("
                                SELECT icl.*, 
                                       u.first_name, u.last_name,
                                       ica.total_amount,
                                       a.name as agrovet_name,
                                       'credit_activity' as log_type
                                FROM input_credit_logs icl
                                JOIN users u ON icl.user_id = u.id
                                JOIN input_credit_applications ica ON icl.input_credit_application_id = ica.id
                                JOIN agrovets a ON ica.agrovet_id = a.id
                                WHERE ica.farmer_id = $farmerId
                                ORDER BY icl.created_at DESC
                                LIMIT 4
                            ");
                            
                            // Combine and sort all activities
                            $combinedActivities = [];
                            
                            if ($produceDeliveries) {
                                foreach ($produceDeliveries as $activity) {
                                    $combinedActivities[] = $activity;
                                }
                            }
                            
                            if ($farmerTransactions) {
                                foreach ($farmerTransactions as $transaction) {
                                    $combinedActivities[] = $transaction;
                                }
                            }
                            
                            if ($loanActivities) {
                                foreach ($loanActivities as $activity) {
                                    $combinedActivities[] = $activity;
                                }
                            }
                            
                            if ($creditActivities) {
                                foreach ($creditActivities as $activity) {
                                    $combinedActivities[] = $activity;
                                }
                            }
                            
                            // Sort by created_at
                            usort($combinedActivities, function($a, $b) {
                                return strtotime($b->created_at) - strtotime($a->created_at);
                            });
                            
                            // Limit to 12 most recent
                            $combinedActivities = array_slice($combinedActivities, 0, 12);
                            
                            if($combinedActivities):
                                foreach($combinedActivities as $activity):
                                    // Set colors and icons based on activity type
                                    if ($activity->log_type == 'produce_delivery') {
                                        $activityColor = match($activity->status) {
                                            'pending' => 'warning',
                                            'accepted', 'verified' => 'info',
                                            'paid' => 'success',
                                            'rejected' => 'danger',
                                            default => 'secondary'
                                        };
                                        
                                        $icon = match($activity->status) {
                                            'pending' => 'ri-time-line',
                                            'accepted' => 'ri-check-line',
                                            'verified' => 'ri-shield-check-line',
                                            'paid' => 'ri-coins-line',
                                            'rejected' => 'ri-close-line',
                                            default => 'ri-truck-line'
                                        };
                                    } elseif ($activity->log_type == 'farmer_transaction') {
                                        $activityColor = $activity->transaction_type == 'credit' ? 'success' : 'danger';
                                        $icon = $activity->transaction_type == 'credit' ? 'ri-arrow-down-circle-line' : 'ri-arrow-up-circle-line';
                                    } elseif ($activity->log_type == 'loan_activity') {
                                        $activityColor = match($activity->action_type) {
                                            'application_submitted' => 'primary',
                                            'approved', 'auto_approved' => 'success',
                                            'rejected', 'auto_rejected' => 'danger',
                                            'disbursed' => 'info',
                                            'repayment_made' => 'warning',
                                            'completed' => 'purple',
                                            default => 'secondary'
                                        };
                                        
                                        $icon = match($activity->action_type) {
                                            'application_submitted' => 'ri-file-add-line',
                                            'approved', 'auto_approved' => 'ri-check-double-line',
                                            'rejected', 'auto_rejected' => 'ri-close-circle-line',
                                            'disbursed' => 'ri-hand-coin-line',
                                            'repayment_made' => 'ri-money-dollar-circle-line',
                                            'completed' => 'ri-medal-line',
                                            default => 'ri-information-line'
                                        };
                                    } else {
                                        // Credit activity
                                        $activityColor = match($activity->action_type) {
                                            'application_submitted' => 'primary',
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            'fulfilled' => 'info',
                                            'payment_made' => 'warning',
                                            'completed' => 'purple',
                                            default => 'secondary'
                                        };
                                        
                                        $icon = match($activity->action_type) {
                                            'application_submitted' => 'ri-package-line',
                                            'approved' => 'ri-check-line',
                                            'rejected' => 'ri-close-line',
                                            'fulfilled' => 'ri-truck-line',
                                            'payment_made' => 'ri-coins-line',
                                            'completed' => 'ri-checkbox-circle-line',
                                            default => 'ri-information-line'
                                        };
                                    }
                        ?>
                                        <li class="crm-recent-activity-content">
                                            <div class="d-flex align-items-top">
                                                <div class="me-3">
                                                    <span
                                                        class="avatar avatar-xs bg-<?php echo $activityColor; ?>-transparent avatar-rounded">
                                                        <i class="<?php echo $icon; ?> fs-12"></i>
                                                    </span>
                                                </div>
                                                <div class="crm-timeline-content">
                                                    <?php if ($activity->log_type == 'produce_delivery'): ?>
                                                    <span class="fw-semibold">
                                                        <?php 
                                            $statusLabel = match($activity->status) {
                                                'pending' => 'Produce delivery submitted',
                                                'accepted' => 'Produce delivery accepted',
                                                'verified' => 'Produce delivery verified',
                                                'paid' => 'Payment received for produce',
                                                'rejected' => 'Produce delivery rejected',
                                                default => 'Produce delivery updated'
                                            };
                                            echo $statusLabel; 
                                        ?>
                                                    </span>
                                                    <span class="d-block text-muted fs-11">
                                                        Product:
                                                        <?php echo htmlspecialchars($activity->product_name); ?>
                                                    </span>
                                                    <span class="d-block text-muted fs-11">
                                                        Quantity: <?php echo number_format($activity->quantity, 1); ?> |
                                                        Value: KES
                                                        <?php echo number_format($activity->total_value, 0); ?>
                                                        <?php if ($activity->quality_grade): ?>
                                                        | Grade: <?php echo $activity->quality_grade; ?>
                                                        <?php endif; ?>
                                                    </span>
                                                    <?php if ($activity->first_name): ?>
                                                    <span class="d-block text-muted fs-11">
                                                        Handled by:
                                                        <?php echo htmlspecialchars($activity->first_name . ' ' . $activity->last_name); ?>
                                                    </span>
                                                    <?php endif; ?>

                                                    <?php elseif ($activity->log_type == 'farmer_transaction'): ?>
                                                    <span class="fw-semibold">
                                                        <?php echo $activity->transaction_type == 'credit' ? 'Payment received' : 'Payment deducted'; ?>
                                                        <span class="fw-bold text-<?php echo $activityColor; ?>">
                                                            KES <?php echo number_format($activity->amount, 0); ?>
                                                        </span>
                                                    </span>
                                                    <span class="d-block text-muted fs-11">
                                                        <?php echo htmlspecialchars($activity->description ?? 'Account transaction'); ?>
                                                    </span>
                                                    <?php if ($activity->first_name): ?>
                                                    <span class="d-block text-muted fs-11">
                                                        Processed by:
                                                        <?php echo htmlspecialchars($activity->first_name . ' ' . $activity->last_name); ?>
                                                    </span>
                                                    <?php endif; ?>

                                                    <?php elseif ($activity->log_type == 'loan_activity'): ?>
                                                    <span class="fw-semibold">
                                                        <?php 
                                            $actionLabel = match($activity->action_type) {
                                                'application_submitted' => 'Loan application submitted',
                                                'auto_approved' => 'Loan auto-approved',
                                                'approved' => 'Loan application approved',
                                                'auto_rejected' => 'Loan auto-rejected',
                                                'rejected' => 'Loan application rejected',
                                                'disbursed' => 'Loan funds received',
                                                'repayment_made' => 'Loan repayment made',
                                                'completed' => 'Loan fully repaid',
                                                'creditworthiness_check' => 'Credit assessment completed',
                                                default => ucfirst(str_replace('_', ' ', $activity->action_type))
                                            };
                                            echo $actionLabel; 
                                        ?>
                                                    </span>
                                                    <span class="d-block text-muted fs-11">
                                                        Loan Type:
                                                        <?php echo htmlspecialchars($activity->loan_type_name); ?>
                                                    </span>
                                                    <span class="d-block text-muted fs-11">
                                                        Amount: KES
                                                        <?php echo number_format($activity->amount_requested, 0); ?>
                                                        <?php if ($activity->bank_name): ?>
                                                        | Bank: <?php echo htmlspecialchars($activity->bank_name); ?>
                                                        <?php else: ?>
                                                        | SACCO Loan
                                                        <?php endif; ?>
                                                    </span>

                                                    <?php else: // Credit activity ?>
                                                    <span class="fw-semibold">
                                                        <?php 
                                            $actionLabel = match($activity->action_type) {
                                                'application_submitted' => 'Input credit application submitted',
                                                'approved' => 'Input credit approved',
                                                'rejected' => 'Input credit rejected',
                                                'fulfilled' => 'Input credit delivered',
                                                'payment_made' => 'Input credit payment made',
                                                'completed' => 'Input credit fully repaid',
                                                default => 'Input credit ' . str_replace('_', ' ', $activity->action_type)
                                            };
                                            echo $actionLabel; 
                                        ?>
                                                    </span>
                                                    <span class="d-block text-muted fs-11">
                                                        Agrovet:
                                                        <?php echo htmlspecialchars($activity->agrovet_name); ?>
                                                    </span>
                                                    <span class="d-block text-muted fs-11">
                                                        Amount: KES
                                                        <?php echo number_format($activity->total_amount, 0); ?>
                                                    </span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="flex-fill text-end">
                                                    <span class="d-block text-muted fs-11 op-7">
                                                        <?php echo date('M j, g:i A', strtotime($activity->created_at)); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </li>
                                        <?php 
                                endforeach;
                            else:
                        ?>
                                        <li class="text-center text-muted">
                                            No recent activities found
                                        </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>

                            <!-- Activity Summary Footer -->
                            <div class="card-footer bg-light border-top">
                                <div class="row text-center">
                                    <div class="col-3">
                                        <div class="border-end">
                                            <div class="d-flex align-items-center justify-content-center mb-1">
                                                <i class="ri-truck-line me-2 text-primary"></i>
                                                <h6 class="mb-0 fw-bold text-primary">
                                                    <?php 
                                        $todayDeliveriesQuery = "SELECT COUNT(*) as count FROM produce_deliveries pd
                                                                JOIN farm_products fp ON pd.farm_product_id = fp.id
                                                                JOIN farms f ON fp.farm_id = f.id
                                                                WHERE f.farmer_id = $farmerId 
                                                                AND DATE(pd.delivery_date) = CURDATE()";
                                        $todayDeliveries = $app->select_one($todayDeliveriesQuery);
                                        echo number_format($todayDeliveries->count);
                                    ?>
                                                </h6>
                                            </div>
                                            <small class="text-muted">
                                                <i class="ri-calendar-line me-1"></i>
                                                Deliveries Today
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="border-end">
                                            <div class="d-flex align-items-center justify-content-center mb-1">
                                                <i class="ri-coins-line me-2" style="color: #70A136;"></i>
                                                <h6 class="mb-0 fw-bold" style="color: #70A136;">
                                                    <?php 
                                        $todayPaymentsQuery = "SELECT COUNT(*) as count FROM produce_deliveries pd
                                                              JOIN farm_products fp ON pd.farm_product_id = fp.id
                                                              JOIN farms f ON fp.farm_id = f.id
                                                              WHERE f.farmer_id = $farmerId 
                                                              AND pd.status = 'paid'
                                                              AND DATE(pd.sale_date) = CURDATE()";
                                        $todayPayments = $app->select_one($todayPaymentsQuery);
                                        echo number_format($todayPayments->count);
                                    ?>
                                                </h6>
                                            </div>
                                            <small class="text-muted">
                                                <i class="ri-check-line me-1"></i>
                                                Payments Received Today
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="border-end">
                                            <div class="d-flex align-items-center justify-content-center mb-1">
                                                <i class="ri-bank-card-line me-2" style="color: #4A220F;"></i>
                                                <h6 class="mb-0 fw-bold" style="color: #4A220F;">
                                                    <?php 
                                        $activeLoansQuery = "SELECT COUNT(*) as count FROM approved_loans al
                                                            JOIN loan_applications la ON al.loan_application_id = la.id
                                                            WHERE la.farmer_id = $farmerId 
                                                            AND al.status = 'active'";
                                        $activeLoansCount = $app->select_one($activeLoansQuery);
                                        echo number_format($activeLoansCount->count);
                                    ?>
                                                </h6>
                                            </div>
                                            <small class="text-muted">
                                                <i class="ri-wallet-line me-1"></i>
                                                Active Loans
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="d-flex align-items-center justify-content-center mb-1">
                                            <i class="ri-package-line me-2 text-success"></i>
                                            <h6 class="mb-0 fw-bold text-success">
                                                <?php 
                                    $activeCreditsQuery = "SELECT COUNT(*) as count FROM approved_input_credits aic
                                                          JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                                          WHERE ica.farmer_id = $farmerId 
                                                          AND aic.status = 'active'";
                                    $activeCreditsCount = $app->select_one($activeCreditsQuery);
                                    echo number_format($activeCreditsCount->count);
                                ?>
                                            </h6>
                                        </div>
                                        <small class="text-muted">
                                            <i class="ri-shopping-cart-line me-1"></i>
                                            Active Input Credits
                                        </small>
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
    <!-- full calendar -->

    <!-- JavaScript -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>

</body>

</html>