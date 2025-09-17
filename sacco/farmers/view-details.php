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
                        <!-- 5 is admin role from your roles table -->
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
                        <span class="fs-semibold text-muted pt-5">Farmers Dashboard</span>
                    </div>
                </div>

                <!-- End::page-header -->
                <?php 
                                            
                                $app = new App();
                                
                                // Get farmer ID from URL
                                $farmerId = isset($_GET['id']) ? intval($_GET['id']) : 0;
                                
                                if (!$farmerId) {
                                    header("Location: index.php");
                                    exit();
                                }
                                
                                // 1. Get Basic Farmer Information
                                $farmerQuery = "SELECT f.*, u.first_name, u.last_name, u.email, u.phone, u.location, u.profile_picture,
                                                u.created_at as registration_date, fc.name as category_name
                                                FROM farmers f
                                                JOIN users u ON f.user_id = u.id
                                                LEFT JOIN farmer_category_mapping fcm ON f.id = fcm.farmer_id
                                                LEFT JOIN farmer_categories fc ON fcm.category_id = fc.id
                                                WHERE f.id = $farmerId";
                                $farmer = $app->select_one($farmerQuery);
                                
                              
                                 // 2. Get Farming Activities
                                  $activitiesQuery = "SELECT 
                                      fp.product_type_id as product_id,
                                      CASE 
                                          WHEN ft.name = 'Fruit Farm' THEN 
                                              CONCAT('Fruit - ', GROUP_CONCAT(DISTINCT frt.name SEPARATOR ', '))
                                          ELSE ft.name
                                      END as farming_type
                                      FROM farms f
                                      JOIN farm_types ft ON f.farm_type_id = ft.id
                                      LEFT JOIN farm_products fp ON f.id = fp.farm_id
                                      LEFT JOIN farm_fruit_mapping ffm ON f.id = ffm.farm_id
                                      LEFT JOIN fruit_types frt ON ffm.fruit_type_id = frt.id
                                      WHERE f.farmer_id = $farmerId
                                      GROUP BY f.id, ft.name";
                                  $activities = $app->select_all($activitiesQuery);
                                
                                // 3. Get Financial Overview
                                $financialQuery = "SELECT 
                                    (SELECT COALESCE(SUM(pd.total_value), 0)
                                     FROM produce_deliveries pd
                                     JOIN farm_products fp ON pd.farm_product_id = fp.id
                                     JOIN farms fm ON fp.farm_id = fm.id
                                     WHERE fm.farmer_id = $farmerId) as total_earnings,
                                    
                                    (SELECT COALESCE(SUM(al.remaining_balance), 0)
                                     FROM approved_loans al
                                     JOIN loan_applications la ON al.loan_application_id = la.id
                                     WHERE la.farmer_id = $farmerId AND al.status = 'active') as outstanding_loans,
                                    
                                    (SELECT COALESCE(SUM(aic.remaining_balance), 0)
                                     FROM approved_input_credits aic
                                     JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                     WHERE ica.farmer_id = $farmerId AND aic.status = 'active') as input_credit_balance";
                                $financials = $app->select_one($financialQuery);
                                
                                // 4. Get Farm Performance
                                $performanceQuery = "SELECT 
                                    COUNT(pd.id) as total_deliveries,
                                    AVG(CASE WHEN pd.quality_grade = 'A' THEN 3 
                                             WHEN pd.quality_grade = 'B' THEN 2
                                             WHEN pd.quality_grade = 'C' THEN 1 END) as avg_quality_score,
                                    COUNT(CASE WHEN pd.status = 'accepted' THEN 1 END) * 100.0 / COUNT(pd.id) as acceptance_rate
                                    FROM produce_deliveries pd
                                    JOIN farm_products fp ON pd.farm_product_id = fp.id
                                    JOIN farms f ON fp.farm_id = f.id
                                    WHERE f.farmer_id = $farmerId";
                                $performance = $app->select_one($performanceQuery);
                                
                                // 5. Get Active Farms
                                $farmsQuery = "SELECT f.*, ft.name as farm_type_name,
                                               COUNT(fp.id) as product_count
                                               FROM farms f
                                               JOIN farm_types ft ON f.farm_type_id = ft.id
                                               LEFT JOIN farm_products fp ON f.id = fp.farm_id
                                               WHERE f.farmer_id = $farmerId
                                               GROUP BY f.id";
                                $farms = $app->select_all($farmsQuery);
                                
                                // 6. Get Recent Deliveries
                                $deliveriesQuery = "SELECT pd.*, pt.name as product_name, f.name as farm_name
                                                    FROM produce_deliveries pd
                                                    JOIN farm_products fp ON pd.farm_product_id = fp.id
                                                    JOIN farms f ON fp.farm_id = f.id
                                                    JOIN product_types pt ON fp.product_type_id = pt.id
                                                    WHERE f.farmer_id = $farmerId
                                                    ORDER BY pd.delivery_date DESC
                                                    LIMIT 5";
                                $recentDeliveries = $app->select_all($deliveriesQuery);
                                
                                // 7. Get Active Loans
                                $loansQuery = "SELECT la.*, al.*, b.name as bank_name, lt.name as loan_type_name
                                               FROM loan_applications la
                                               JOIN approved_loans al ON la.id = al.loan_application_id
                                               LEFT JOIN banks b ON la.bank_id = b.id
                                               JOIN loan_types lt ON la.loan_type_id = lt.id
                                               WHERE la.farmer_id = $farmerId AND al.status = 'active'";
                                $activeLoans = $app->select_all($loansQuery);
                                
                                // 8. Get Activity Logs
                                $logsQuery = "SELECT al.*, u.first_name, u.last_name
                                              FROM activity_logs al
                                              JOIN users u ON al.user_id = u.id
                                              WHERE al.description LIKE CONCAT('%', (SELECT registration_number FROM farmers WHERE id = $farmerId), '%')
                                              ORDER BY al.created_at DESC
                                              LIMIT 10";
                                $activityLogs = $app->select_all($logsQuery);
                                $inputCreditsQuery = "SELECT ica.*, aic.*, a.name as agrovet_name
                                                 FROM input_credit_applications ica
                                                 LEFT JOIN approved_input_credits aic ON ica.id = aic.credit_application_id
                                                 LEFT JOIN agrovets a ON ica.agrovet_id = a.id
                                                 WHERE ica.farmer_id = $farmerId 
                                                 AND aic.status = 'active'
                                                 ORDER BY ica.application_date DESC";
                                 $inputCredits = $app->select_all($inputCreditsQuery);
                                                                 
                                ?>


                <!-- Farmer Profile Header -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <img src="http://localhost/dfcs/<?= $farmer->profile_picture ?? 'http://localhost/dfcs/assets/images/faces/face-image-1.jpg' ?>"
                                            class="img-fluid rounded-circle" style="width: 100px; height: 100px;"
                                            alt="Farmer Photo">
                                    </div>
                                    <div>
                                        <h4 class="mb-1">
                                            <?php echo htmlspecialchars($farmer->first_name . ' ' . $farmer->last_name) ?>
                                        </h4>
                                        <p class="mb-1">
                                            <span
                                                class="badge bg-<?php echo $farmer->is_verified ? 'success' : 'warning' ?>">
                                                <?php echo $farmer->is_verified ? 'Verified Farmer' : 'Pending Verification' ?>
                                            </span>
                                            <span
                                                class="badge bg-info ms-2"><?php echo htmlspecialchars($farmer->registration_number) ?></span>
                                            <span
                                                class="badge bg-primary ms-2"><?php echo htmlspecialchars($farmer->category_name ?? 'Uncategorized') ?></span>
                                        </p>
                                        <p class="text-muted mb-0">
                                            <i class="ri-map-pin-line me-1"></i>
                                            <?php echo htmlspecialchars($farmer->location) ?>
                                        </p>
                                    </div>
                                    <div class="ms-auto">
                                        <button class="btn btn-outline-primary btn-sm">
                                            <i class="ri-mail-line me-1"></i> Contact
                                        </button>
                                        <?php if (!$farmer->is_verified): ?>
                                        <button class="btn btn-success btn-sm ms-2"
                                            onclick="verifyFarmer(<?php echo $farmerId ?>)">
                                            <i class="ri-check-line me-1"></i> Verify Farmer
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Total Earnings Card -->
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span class="avatar avatar-md bg-primary">
                                            <i class="ri-money-dollar-circle-line"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="mb-1">Total Earnings</p>
                                        <h5 class="mb-0">KES
                                            <?php echo number_format($financials->total_earnings ?? 0, 2) ?></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Outstanding Loans -->
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span class="avatar avatar-md bg-warning">
                                            <i class="ri-bank-line"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="mb-1">Outstanding Loans</p>
                                        <h5 class="mb-0">KES
                                            <?php echo number_format($financials->outstanding_loans ?? 0, 2) ?></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Active Farms -->
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span class="avatar avatar-md bg-success">
                                            <i class="ri-plant-line"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="mb-1">Active Farms</p>
                                        <h5 class="mb-0"><?php echo is_array($farms) ? count($farms) : 0 ?></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Success Rate -->
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span class="avatar avatar-md bg-info">
                                            <i class="ri-truck-line"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="mb-1">Delivery Success Rate</p>
                                        <h5 class="mb-0">
                                            <?php 
                                                $rate = $performance->acceptance_rate ?? 0;
                                                echo number_format($rate, 1) . '%';
                                                ?>
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Information Tabs -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-body">
                                <ul class="nav nav-tabs mb-3" id="farmerDetailsTabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#overview">Overview</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#farms">Farms</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#finances">Finances</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#deliveries">Deliveries</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#activity">Activity</a>
                                    </li>
                                </ul>

                                <div class="tab-content" id="farmerDetailsContent">
                                    <!-- Overview Tab -->
                                    <div class="tab-pane fade show active" id="overview">
                                        <!-- Personal Information -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h5 class="mb-3">
                                                    <i class="ri-user-settings-line me-2 text-primary"></i>Personal
                                                    Information
                                                </h5>
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <td>
                                                            <strong>
                                                                <i class="ri-user-line text-muted me-2"></i>Full Name
                                                            </strong>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($farmer->first_name . ' ' . $farmer->last_name) ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong>
                                                                <i class="ri-hashtag text-muted me-2"></i>Registration
                                                                Number
                                                            </strong>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-light text-dark">
                                                                <?php echo htmlspecialchars($farmer->registration_number) ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong>
                                                                <i class="ri-phone-line text-muted me-2"></i>Phone
                                                            </strong>
                                                        </td>
                                                        <td>
                                                            <a href="tel:<?php echo htmlspecialchars($farmer->phone) ?>"
                                                                class="text-primary">
                                                                <?php echo htmlspecialchars($farmer->phone) ?>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong>
                                                                <i class="ri-mail-line text-muted me-2"></i>Email
                                                            </strong>
                                                        </td>
                                                        <td>
                                                            <a href="mailto:<?php echo htmlspecialchars($farmer->email) ?>"
                                                                class="text-primary">
                                                                <?php echo htmlspecialchars($farmer->email) ?>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong>
                                                                <i class="ri-map-pin-line text-muted me-2"></i>Location
                                                            </strong>
                                                        </td>
                                                        <td>
                                                            <span class="text-muted">
                                                                <?php echo htmlspecialchars($farmer->location) ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong>
                                                                <i
                                                                    class="ri-calendar-line text-muted me-2"></i>Registration
                                                                Date
                                                            </strong>
                                                        </td>
                                                        <td>
                                                            <span class="text-muted">
                                                                <?php echo date('M d, Y', strtotime($farmer->registration_date)) ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <!-- Farming Activities Summary -->
                                            <div class="col-md-6">
                                                <h5 class="mb-3"><i class="ri-plant-line me-2"></i>Farming Activities
                                                </h5>
                                                <?php if ($activities && count($activities) > 0): ?>
                                                <div class="list-group">
                                                    <?php foreach ($activities as $activity): ?>
                                                    <div
                                                        class="list-group-item d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <i class="ri-leaf-line me-2 text-success"></i>
                                                            <?php echo htmlspecialchars($activity->farming_type) ?>
                                                        </div>
                                                        <span class="badge bg-primary rounded-pill">Active</span>
                                                    </div>
                                                    <?php endforeach; ?>
                                                </div>
                                                <?php else: ?>
                                                <div class="text-center p-4 border rounded">
                                                    <i class="ri-plant-line fs-3 text-muted mb-3"></i>
                                                    <p class="mb-0">No farming activities recorded yet</p>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Farms Tab -->
                                    <!-- Farms Tab -->
                                    <div class="tab-pane fade" id="farms">
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <h5><i class="ri-farm-line me-2"></i>Registered Farms</h5>

                                        </div>

                                        <?php if ($farms && count($farms) > 0): ?>
                                        <div class="row">
                                            <?php foreach ($farms as $farm): ?>
                                            <div class="col-md-6 col-xl-4 mb-4">
                                                <div class="card custom-card">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between mb-3">
                                                            <h6 class="card-title mb-0">
                                                                <?php echo htmlspecialchars($farm->name) ?></h6>
                                                            <span
                                                                class="badge bg-<?php echo $farm->is_active ? 'success' : 'warning' ?>">
                                                                <?php echo $farm->is_active ? 'Active' : 'Inactive' ?>
                                                            </span>
                                                        </div>
                                                        <div class="mb-3">
                                                            <small class="text-muted">
                                                                <i class="ri-map-pin-line me-1"></i>
                                                                <?php echo htmlspecialchars($farm->location) ?>
                                                            </small>
                                                        </div>
                                                        <div class="mb-3">
                                                            <p class="mb-1"><strong>Size:</strong>
                                                                <?php echo $farm->size . ' ' . $farm->size_unit ?></p>
                                                            <p class="mb-1"><strong>Type:</strong>
                                                                <?php echo htmlspecialchars($farm->farm_type_name) ?>
                                                            </p>
                                                            <p class="mb-0"><strong>Products:</strong>
                                                                <?php echo $farm->product_count ?></p>
                                                        </div>
                                                        <div class="d-flex gap-2 mt-3">
                                                            <button class="btn btn-sm btn-outline-primary">
                                                                <i class="ri-edit-line me-1"></i>Edit
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-info">
                                                                <i class="ri-file-list-line me-1"></i>Details
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php else: ?>
                                        <div class="text-center p-5 border rounded">
                                            <i class="ri-farm-line fs-1 text-muted mb-3"></i>
                                            <h6>No Farms Registered</h6>
                                            <p class="text-muted mb-3">This farmer hasn't registered any farms yet</p>

                                        </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Finances Tab -->
                                    <div class="tab-pane fade" id="finances">
                                        <div class="row">
                                            <!-- Loans Summary -->
                                            <div class="col-md-6 mb-4">
                                                <div class="card custom-card">
                                                    <div class="card-header">
                                                        <h6 class="card-title mb-0"><i
                                                                class="ri-bank-line me-2"></i>Active Loans</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <?php if ($activeLoans && count($activeLoans) > 0): ?>
                                                        <div class="list-group">
                                                            <?php foreach ($activeLoans as $loan): ?>
                                                            <div class="list-group-item">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <h6 class="mb-1">
                                                                            <?php echo htmlspecialchars($loan->bank_name ?? 'SACCO') ?>
                                                                        </h6>
                                                                        <small class="text-muted">
                                                                            <?php echo htmlspecialchars($loan->loan_type_name) ?>
                                                                        </small>
                                                                    </div>
                                                                    <div class="text-end">
                                                                        <h6 class="mb-1">KES
                                                                            <?php echo number_format($loan->remaining_balance, 2) ?>
                                                                        </h6>
                                                                        <small class="text-muted">Outstanding</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                        <?php else: ?>
                                                        <div class="text-center p-3">
                                                            <i class="ri-bank-line fs-3 text-muted mb-2"></i>
                                                            <p class="mb-0">No active loans</p>
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Input Credits Summary -->
                                            <!-- Input Credits Card in Finances Tab -->
                                            <div class="col-md-6 mb-4">
                                                <div class="card custom-card">
                                                    <div class="card-header">
                                                        <h6 class="card-title mb-0">
                                                            <i class="ri-shopping-cart-line me-2"></i>Input Credits
                                                        </h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <?php if ($inputCredits && is_array($inputCredits) && count($inputCredits) > 0): ?>
                                                        <div class="list-group">
                                                            <?php foreach ($inputCredits as $credit): ?>
                                                            <div class="list-group-item">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <h6 class="mb-1">
                                                                            <?php echo htmlspecialchars($credit->agrovet_name) ?>
                                                                        </h6>
                                                                        <small class="text-muted">
                                                                            Taken:
                                                                            <?php echo date('M d, Y', strtotime($credit->application_date)) ?>
                                                                        </small>
                                                                    </div>
                                                                    <div class="text-end">
                                                                        <h6 class="mb-1">KES
                                                                            <?php echo number_format($credit->remaining_balance ?? 0, 2) ?>
                                                                        </h6>
                                                                        <small class="text-success">
                                                                            <?php echo number_format($credit->credit_percentage ?? 0, 1) ?>%
                                                                            Interest
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                        <?php else: ?>
                                                        <div class="text-center py-4">
                                                            <i class="ri-shopping-cart-line fs-2 text-muted mb-2"></i>
                                                            <p class="mb-0">No active input credits</p>
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Deliveries Tab -->
                                    <div class="tab-pane fade" id="deliveries">
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <h5><i class="ri-truck-line me-2"></i>Produce Deliveries</h5>
                                            <div>

                                            </div>
                                        </div>

                                        <?php if ($recentDeliveries && count($recentDeliveries) > 0): ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Product</th>
                                                        <th>Farm</th>
                                                        <th>Quantity</th>
                                                        <th>Quality Grade</th>
                                                        <th>Value</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($recentDeliveries as $delivery): ?>
                                                    <tr>
                                                        <td><?php echo date('M d, Y', strtotime($delivery->delivery_date)) ?>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($delivery->product_name) ?></td>
                                                        <td><?php echo htmlspecialchars($delivery->farm_name) ?></td>
                                                        <td><?php echo number_format($delivery->quantity, 2) ?></td>
                                                        <td>
                                                            <span class="badge bg-<?php 
                                                                  echo $delivery->quality_grade === 'A' ? 'success' : 
                                                                      ($delivery->quality_grade === 'B' ? 'warning' : 'danger') 
                                                              ?>">
                                                                Grade <?php echo $delivery->quality_grade ?>
                                                            </span>
                                                        </td>
                                                        <td>KES <?php echo number_format($delivery->total_value, 2) ?>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-<?php 
                                                            echo $delivery->status === 'accepted' ? 'success' : 
                                                                ($delivery->status === 'pending' ? 'warning' : 'danger') 
                                                        ?>">
                                                                <?php echo ucfirst($delivery->status) ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-outline-primary">
                                                                <i class="ri-file-list-line"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php else: ?>
                                        <div class="text-center p-5 border rounded">
                                            <i class="ri-truck-line fs-1 text-muted mb-3"></i>
                                            <h6>No Deliveries Found</h6>
                                            <p class="text-muted mb-3">No produce deliveries have been recorded yet</p>

                                        </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Activity Tab -->
                                    <div class="tab-pane fade" id="activity">
                                        <h5 class="mb-4"><i class="ri-history-line me-2"></i>Recent Activities</h5>

                                        <?php if ($activityLogs && count($activityLogs) > 0): ?>
                                        <div class="timeline-page mb-5">
                                            <?php foreach ($activityLogs as $log): ?>
                                            <div class="timeline-item">
                                                <div class="timeline-badge">
                                                    <i class="ri-record-circle-line"></i>
                                                </div>
                                                <div class="timeline-item-content">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="text-primary fw-semibold">
                                                            <?php echo htmlspecialchars($log->activity_type) ?>
                                                        </span>
                                                        <small class="text-muted">
                                                            <?php echo $app->formatTimeAgo($log->created_at) ?>
                                                        </small>
                                                    </div>
                                                    <p class="mt-2 mb-0">
                                                        <?php echo htmlspecialchars($log->description) ?></p>
                                                    <small class="text-muted">
                                                        By:
                                                        <?php echo htmlspecialchars($log->first_name . ' ' . $log->last_name) ?>
                                                    </small>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php else: ?>
                                        <div class="text-center p-5 border rounded">
                                            <i class="ri-history-line fs-1 text-muted mb-3"></i>
                                            <h6>No Activity History</h6>
                                            <p class="text-muted mb-0">No activities have been recorded for this farmer
                                                yet</p>
                                        </div>
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


</body>



</html>