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
    <meta name="apple-mobile-web-app-title" content="Makueni DFCS" />
    <link rel="manifest" href="http://localhost/dfcs/assets/images/favicon/site.webmanifest" />
    <!-- Main Theme Js -->
    <!-- Choices JS -->
    <script src="http://localhost/dfcs/assets/libs/choices.js/public/assets/scripts/choices.min.js"></script>
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

    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/glightbox/css/glightbox.min.css">
    <link rel="stylesheet" href="http://localhost/dfcs/toast/toast.css">

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

                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                    <h1 class="page-title fw-semibold fs-18 mb-0">Profile</h1>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Pages</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Profile</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <!-- Page Header Close -->

                <!-- Start::row-1 -->
                <div class="row">
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
                
                        // Get stats
                        $statsQuery = "SELECT 
                            (SELECT COUNT(*) FROM input_credit_applications ica 
                             JOIN input_credit_logs icl ON ica.id = icl.input_credit_application_id 
                             WHERE icl.user_id = $userId AND ica.agrovet_id = {$staff->agrovet_id}) as processed_credits,
                            
                            (SELECT COUNT(*) FROM input_credit_applications ica 
                             WHERE ica.agrovet_id = {$staff->agrovet_id} AND ica.reviewed_by = {$staff->staff_id} 
                             AND ica.status = 'approved') as active_credits,
                            
                            (SELECT CONCAT(ROUND(
                                (COUNT(CASE WHEN ica.status = 'approved' OR ica.status = 'fulfilled' THEN 1 END) * 100.0 / 
                                NULLIF(COUNT(*), 0)), 0), '%')
                             FROM input_credit_applications ica
                             WHERE ica.agrovet_id = {$staff->agrovet_id} AND ica.reviewed_by = {$staff->staff_id}) as approval_rate,
                            
                            (SELECT ROUND(
                                (COUNT(CASE WHEN ica.status = 'approved' OR ica.status = 'fulfilled' THEN 1 END) * 100.0 / 
                                NULLIF(10, 0)), 0)
                             FROM input_credit_applications ica 
                             WHERE ica.agrovet_id = {$staff->agrovet_id} AND ica.reviewed_by = {$staff->staff_id} 
                             AND MONTH(ica.review_date) = MONTH(CURRENT_DATE) 
                             AND YEAR(ica.review_date) = YEAR(CURRENT_DATE)) as monthly_target";
                                    
                        $stats = $app->select_one($statsQuery);
                    ?>
                    <div class="d-sm-flex align-items-top p-4 border-bottom-0 main-profile-cover">
                        <div>
                            <span class="avatar avatar-xxl avatar-rounded online me-3">
                                <img src="http://localhost/dfcs/<?= $staff->profile_picture ?? 'assets/images/faces/face-image-1.jpg' ?>"
                                    alt="">
                            </span>
                        </div>
                        <div class="flex-fill main-profile-info">
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="fw-semibold mb-1 text-fixed-white">
                                    <?= htmlspecialchars($staff->first_name . ' ' . $staff->last_name) ?>
                                </h6>
                            </div>
                            <p class="mb-1 text-muted text-fixed-white op-7"><?= htmlspecialchars($staff->position) ?>
                            </p>
                            <p class="fs-12 text-fixed-white mb-4 op-5">
                                <span class="me-3">
                                    <i class="ri-building-line me-1 align-middle"></i>
                                    <?= htmlspecialchars($staff->agrovet_name . ' (' . $staff->agrovet_type . ')') ?>
                                </span>
                                <span class="me-3">
                                    <i class="ri-user-3-line me-1 align-middle"></i>
                                    Staff ID: <?= htmlspecialchars($staff->employee_number) ?>
                                </span>
                            </p>
                            <div class="d-flex mb-0">
                                <div class="me-4">
                                    <p class="fw-bold fs-20 text-fixed-white text-shadow mb-0">
                                        <?= $stats->processed_credits ?? 0 ?></p>
                                    <p class="mb-0 fs-11 op-5 text-fixed-white">Credits Processed</p>
                                </div>
                                <div class="me-4">
                                    <p class="fw-bold fs-20 text-fixed-white text-shadow mb-0">
                                        <?= $stats->active_credits ?? 0 ?></p>
                                    <p class="mb-0 fs-11 op-5 text-fixed-white">Active Credits</p>
                                </div>
                                <div class="me-4">
                                    <p class="fw-bold fs-20 text-fixed-white text-shadow mb-0">
                                        <?= $stats->approval_rate ?? '0%' ?></p>
                                    <p class="mb-0 fs-11 op-5 text-fixed-white">Approval Rate</p>
                                </div>
                                <div class="me-4">
                                    <p class="fw-bold fs-20 text-fixed-white text-shadow mb-0">
                                        <?= $stats->monthly_target ?? 0 ?>%</p>
                                    <p class="mb-0 fs-11 op-5 text-fixed-white">Monthly Target</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                                 // First get the agrovet_staff record for the logged-in user
                                 $staffQuery = "SELECT id as staff_id, agrovet_id 
                                                FROM agrovet_staff 
                                                WHERE user_id = $userId";
                                 $staffInfo = $app->select_one($staffQuery);
                                 
                                 if (!$staffInfo) {
                                     header("Location: http://localhost/dfcs/");
                                     exit();
                                 }
                                 
                                 $staffId = $staffInfo->staff_id;
                                 $agrovetId = $staffInfo->agrovet_id;
                                 
                                 // Get input credit statistics
                                 $creditStatsQuery = "SELECT
                                     (SELECT COUNT(*) FROM input_credit_applications ica
                                     WHERE ica.agrovet_id = $agrovetId AND ica.reviewed_by = $staffId) as total_processed,
                                 
                                     (SELECT ROUND(
                                     ((SELECT COUNT(*) FROM input_credit_applications ica
                                     WHERE ica.agrovet_id = $agrovetId AND ica.reviewed_by = $staffId 
                                     AND MONTH(ica.updated_at) = MONTH(CURRENT_DATE)
                                     AND YEAR(ica.updated_at) = YEAR(CURRENT_DATE)) -
                                     (SELECT COUNT(*) FROM input_credit_applications ica
                                     WHERE ica.agrovet_id = $agrovetId AND ica.reviewed_by = $staffId 
                                     AND MONTH(ica.updated_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
                                     AND YEAR(ica.updated_at) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH))) /
                                     NULLIF((SELECT COUNT(*) FROM input_credit_applications ica
                                     WHERE ica.agrovet_id = $agrovetId AND ica.reviewed_by = $staffId 
                                     AND MONTH(ica.updated_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
                                     AND YEAR(ica.updated_at) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)), 0) * 100, 1)
                                     ) as monthly_change,
                                 
                                     (SELECT COUNT(*) FROM input_credit_applications ica
                                     WHERE ica.agrovet_id = $agrovetId AND ica.reviewed_by = $staffId 
                                     AND MONTH(ica.updated_at) = MONTH(CURRENT_DATE)
                                     AND YEAR(ica.updated_at) = YEAR(CURRENT_DATE)) as current_month_credits,
                                 
                                     (SELECT COUNT(*) FROM input_credit_applications ica
                                     WHERE ica.agrovet_id = $agrovetId AND ica.reviewed_by = $staffId 
                                     AND MONTH(ica.updated_at) = MONTH(CURRENT_DATE)
                                     AND YEAR(ica.updated_at) = YEAR(CURRENT_DATE) AND ica.status = 'approved') as approved_this_month,
                                 
                                     (SELECT COUNT(*) FROM input_credit_applications ica
                                     WHERE ica.agrovet_id = $agrovetId AND ica.reviewed_by = $staffId 
                                     AND MONTH(ica.updated_at) = MONTH(CURRENT_DATE)
                                     AND YEAR(ica.updated_at) = YEAR(CURRENT_DATE) AND ica.status = 'rejected') as rejected_this_month,
                                     
                                     (SELECT CONCAT(ROUND(
                                         (COUNT(CASE WHEN ica.status = 'approved' OR ica.status = 'fulfilled' THEN 1 END) * 100.0 / 
                                         NULLIF(COUNT(*), 0)), 0), '%')
                                      FROM input_credit_applications ica
                                      WHERE ica.agrovet_id = $agrovetId AND ica.reviewed_by = $staffId) as approval_rate";
                                 
                                 $creditStats = $app->select_one($creditStatsQuery);
                                 
                                 // Get agrovet account balance
                                 $accountQuery = "SELECT 
                                     ac.balance, 
                                     ac.account_number,
                                     a.name as agrovet_name
                                     FROM agrovet_accounts ac
                                     JOIN agrovets a ON ac.agrovet_id = a.id
                                     WHERE ac.agrovet_id = $agrovetId";
                                 $accountInfo = $app->select_one($accountQuery);
                                 ?>
                    <!-- Middle Section Stats Cards -->
                    <div class="row mt-4 mb-4">
                        <!-- Input Credit Processing Summary Card -->
                        <div class="col-xl-3">
                            <div class="card custom-card bg-primary-transparent">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-lg bg-primary-transparent rounded-circle">
                                            <i class="ti ti-file-description fs-20 text-primary"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h5 class="mb-1 fw-semibold">
                                                <?= number_format($creditStats->total_processed ?? 0) ?></h5>
                                            <p class="mb-0 text-muted fs-12">Total Input Credits Processed</p>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <div class="d-flex align-items-center">
                                            <span class="me-2 fs-14">
                                                <i
                                                    class="ti ti-trending-<?= ($creditStats->monthly_change ?? 0) >= 0 ? 'up text-success' : 'down text-danger' ?>"></i>
                                                <?= $creditStats->monthly_change ?? 0 ?>%
                                            </span>
                                            <span class="fs-12 text-muted">from last month</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Performance Metrics Card -->
                        <div class="col-xl-3">
                            <div class="card custom-card bg-success-transparent">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-lg bg-success-transparent rounded-circle">
                                            <i class="ti ti-chart-pie fs-20 text-success"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h5 class="mb-1 fw-semibold"><?= $creditStats->approval_rate ?? '0%' ?></h5>
                                            <p class="mb-0 text-muted fs-12">Credit Approval Rate</p>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <div class="progress progress-xs progress-animate">
                                            <div class="progress-bar bg-success"
                                                style="width: <?= str_replace('%', '', $creditStats->approval_rate ?? 0) ?>%">
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mt-1">
                                            <span class="fs-12 text-muted">Target: 85%</span>
                                            <span class="fs-12 text-muted">Current:
                                                <?= $creditStats->approval_rate ?? '0%' ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Current Period Activity Card -->
                        <div class="col-xl-3">
                            <div class="card custom-card bg-warning-transparent">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-lg bg-warning-transparent rounded-circle">
                                            <i class="ti ti-calendar-stats fs-20 text-warning"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h5 class="mb-1 fw-semibold">
                                                <?= number_format($creditStats->current_month_credits ?? 0) ?></h5>
                                            <p class="mb-0 text-muted fs-12">This Month's Credits</p>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <span class="badge bg-success-transparent me-1">
                                                    <i class="ti ti-check"></i>
                                                    <?= $creditStats->approved_this_month ?? 0 ?> Approved
                                                </span>
                                            </div>
                                            <div>
                                                <span class="badge bg-danger-transparent">
                                                    <i class="ti ti-x"></i>
                                                    <?= $creditStats->rejected_this_month ?? 0 ?> Rejected
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Agrovet Account Balance Card -->
                        <div class="col-xl-3">
                            <div class="card custom-card bg-info-transparent">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-lg bg-info-transparent rounded-circle">
                                            <i class="ti ti-coin fs-20 text-info"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h5 class="mb-1 fw-semibold">KES
                                                <?= number_format($accountInfo->balance ?? 0, 2) ?></h5>
                                            <p class="mb-0 text-muted fs-12">Agrovet Account Balance</p>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <div class="d-flex align-items-center">
                                            <span class="me-2 fs-12 text-muted">Account:
                                                <?= $accountInfo->account_number ?? 'N/A' ?></span>
                                            <span
                                                class="fs-12 text-muted"><?= $accountInfo->agrovet_name ?? '' ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                $userId = $_SESSION['user_id'] ?? null;
                if (!$userId) {
                    header("Location: http://localhost/dfcs/");
                    exit();
                }
                // First query: Get the staff ID from user ID
                $staffIdQuery = "SELECT id as staff_id, agrovet_id
                                FROM agrovet_staff 
                                WHERE user_id = $userId";
                $staffIdResult = $app->select_one($staffIdQuery);
                
                if (!$staffIdResult) {
                    echo "<div class='alert alert-danger'>No agrovet staff record found for this user</div>";
                    exit;
                }
                
                $staffId = $staffIdResult->staff_id;
                $agrovetId = $staffIdResult->agrovet_id;
            
                // Fixed the SQL syntax - renamed the table alias from 'as' to 'ast'
                $query = "SELECT 
                        ast.id,
                        ast.employee_number,
                        ast.position,
                        ast.agrovet_id,
                        ast.user_id,
                        ag.name as agrovet_name,
                        at.name as agrovet_type,
                        u.first_name,
                        u.last_name,
                        u.email,
                        u.phone,
                        u.username
                      FROM agrovet_staff ast
                      INNER JOIN users u ON ast.user_id = u.id
                      INNER JOIN agrovets ag ON ast.agrovet_id = ag.id
                      INNER JOIN agrovet_types at ON ag.type_id = at.id
                      WHERE ast.id = $staffId";
                      
                $staff = $app->select_one($query);
                
                if (!$staff) {
                    echo "<div class='alert alert-danger'>Staff member not found</div>";
                    exit;
                }
            ?>
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card custom-card">
                                <div class="card-header justify-content-between">
                                    <div class="card-title">Update Agrovet Staff Details</div>
                                </div>
                                <div class="card-body add-products p-0">
                                    <!-- Hidden fields for original values -->
                                    <input type="hidden" id="staff-id"
                                        value="<?php echo htmlspecialchars($staff->id); ?>">
                                    <input type="hidden" id="original-email"
                                        value="<?php echo htmlspecialchars($staff->email); ?>">
                                    <input type="hidden" id="original-username"
                                        value="<?php echo htmlspecialchars($staff->username); ?>">
                                    <input type="hidden" id="user-id"
                                        value="<?php echo htmlspecialchars($staff->user_id); ?>">
                                    <input type="hidden" id="agrovet-id"
                                        value="<?php echo htmlspecialchars($staff->agrovet_id); ?>">

                                    <!-- Tabs Navigation -->
                                    <ul class="nav nav-tabs" id="agrovetTabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" data-bs-toggle="tab"
                                                data-bs-target="#basic-info" type="button" role="tab">
                                                <i class="bi bi-info-circle me-1"></i>Personal Info
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#work-info"
                                                type="button" role="tab">
                                                <i class="bi bi-briefcase me-1"></i>Work Details
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#account-info"
                                                type="button" role="tab">
                                                <i class="bi bi-person-circle me-1"></i>Account Details
                                            </button>
                                        </li>
                                    </ul>

                                    <!-- Tab Content -->
                                    <div class="tab-content p-4">
                                        <!-- Personal Info Tab -->
                                        <div class="tab-pane fade show active" id="basic-info" role="tabpanel">
                                            <div class="row gy-3">
                                                <div class="col-xl-6">
                                                    <label class="form-label">First Name</label>
                                                    <input type="text" class="form-control" id="first-name"
                                                        value="<?php echo htmlspecialchars($staff->first_name); ?>"
                                                        required>
                                                </div>
                                                <div class="col-xl-6">
                                                    <label class="form-label">Last Name</label>
                                                    <input type="text" class="form-control" id="last-name"
                                                        value="<?php echo htmlspecialchars($staff->last_name); ?>"
                                                        required>
                                                </div>
                                                <div class="col-xl-6">
                                                    <label class="form-label">Phone Number</label>
                                                    <input type="tel" class="form-control" id="phone"
                                                        value="<?php echo htmlspecialchars($staff->phone); ?>">
                                                </div>
                                                <div class="col-xl-6">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" class="form-control" id="email"
                                                        value="<?php echo htmlspecialchars($staff->email); ?>" required>
                                                    <div style="color:red;" id="email-error"></div>
                                                    <div style="color:green;" id="email-success"></div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end mt-3">
                                                <button class="btn text-white" id="nextBasic"
                                                    style="background:#6AA32D;">
                                                    Next <i class="bi bi-arrow-right ms-2"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Work Details Tab -->
                                        <div class="tab-pane fade" id="work-info" role="tabpanel">
                                            <div class="row gy-3">
                                                <div class="col-xl-6">
                                                    <label class="form-label">Employee Number</label>
                                                    <input type="text" class="form-control" id="employee-number"
                                                        value="<?php echo htmlspecialchars($staff->employee_number); ?>"
                                                        required>
                                                </div>
                                                <div class="col-xl-6">
                                                    <label class="form-label">Position</label>
                                                    <input type="text" class="form-control" id="position"
                                                        value="<?php echo htmlspecialchars($staff->position); ?>"
                                                        required>
                                                </div>
                                                <div class="col-xl-12">
                                                    <label class="form-label">Agrovet</label>
                                                    <input type="text" class="form-control" id="agrovet-name"
                                                        value="<?php echo htmlspecialchars($staff->agrovet_name . ' (' . $staff->agrovet_type . ')'); ?>"
                                                        readonly>
                                                    <small class="text-muted">Agrovet assignment cannot be changed
                                                        here</small>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between mt-4">
                                                <button class="btn btn-light" id="prevWork">
                                                    <i class="bi bi-arrow-left me-2"></i>Previous
                                                </button>
                                                <button class="btn text-white" id="nextWork"
                                                    style="background:#6AA32D;">
                                                    Next <i class="bi bi-arrow-right ms-2"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Account Details Tab -->
                                        <div class="tab-pane fade" id="account-info" role="tabpanel">
                                            <div class="row gy-3">
                                                <div class="col-xl-6">
                                                    <label class="form-label">Username</label>
                                                    <input type="text" class="form-control" id="username"
                                                        value="<?php echo htmlspecialchars($staff->username); ?>"
                                                        required>
                                                </div>
                                                <div class="col-xl-6">
                                                    <label class="form-label">New Password (leave blank to keep
                                                        current)</label>
                                                    <input type="password" class="form-control" id="password"
                                                        placeholder="Enter new password if changing">
                                                </div>
                                                <div class="col-xl-6">
                                                    <label class="form-label">Confirm New Password</label>
                                                    <input type="password" class="form-control" id="confirm-password"
                                                        placeholder="Confirm new password">
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between mt-3">
                                                <button class="btn btn-light" id="prevAccount">
                                                    <i class="bi bi-arrow-left me-2"></i>Previous
                                                </button>
                                                <button class="btn text-white" onclick="updateAgrovetStaff()"
                                                    style="background:#6AA32D;">
                                                    Update <i class="bi bi-check-lg ms-2"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Input Credit Management Overview Card -->
                    <div class="row mt-4">
                        <div class="col-xl-12">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <h6 class="card-title"><i class="ti ti-credit-card me-2"></i>Input Credit Management
                                        Overview</h6>
                                </div>
                                <div class="card-body">
                                    <?php
                                    // Get input credit statistics for this agrovet and staff
                                    $creditOverviewQuery = "SELECT 
                                        COUNT(CASE WHEN ica.status = 'pending' OR ica.status = 'under_review' THEN 1 END) as pending_credits,
                                        COUNT(CASE WHEN ica.status = 'approved' THEN 1 END) as approved_credits,
                                        COUNT(CASE WHEN ica.status = 'rejected' THEN 1 END) as rejected_credits,
                                        COUNT(CASE WHEN ica.status = 'fulfilled' THEN 1 END) as fulfilled_credits,
                                        ROUND(
                                            (COUNT(CASE WHEN ica.status = 'approved' OR ica.status = 'fulfilled' THEN 1 END) * 100.0 / 
                                            NULLIF(COUNT(*), 0)), 1) as approval_rate,
                                        ROUND(
                                            (COUNT(CASE WHEN ica.status = 'rejected' THEN 1 END) * 100.0 / 
                                            NULLIF(COUNT(*), 0)), 1) as rejection_rate,
                                        ROUND(AVG(ica.total_amount), 2) as avg_credit_amount,
                                        SUM(CASE WHEN ica.status = 'approved' OR ica.status = 'fulfilled' THEN ica.total_amount ELSE 0 END) as total_approved_amount
                                    FROM input_credit_applications ica 
                                    WHERE ica.agrovet_id = $agrovetId 
                                    AND ica.reviewed_by = $staffId";
                                    
                                    $creditOverview = $app->select_one($creditOverviewQuery);
                                    
                                    // Get input credit items distribution
                                    $inputDistributionQuery = "SELECT 
                                        ici.input_type,
                                        COUNT(*) as item_count,
                                        SUM(ici.quantity) as total_quantity,
                                        SUM(ici.total_price) as total_value,
                                        ROUND(SUM(ici.total_price) * 100.0 / NULLIF((
                                            SELECT SUM(total_price) 
                                            FROM input_credit_items 
                                            JOIN input_credit_applications ON input_credit_items.credit_application_id = input_credit_applications.id
                                            WHERE input_credit_applications.agrovet_id = $agrovetId
                                        ), 0), 1) as value_percentage
                                    FROM input_credit_items ici
                                    JOIN input_credit_applications ica ON ici.credit_application_id = ica.id
                                    WHERE ica.agrovet_id = $agrovetId
                                    GROUP BY ici.input_type
                                    ORDER BY total_value DESC";
                                    
                                    $inputDistribution = $app->select_all($inputDistributionQuery);
                                    
                                    // Get recent input credit applications
                                    $recentCreditsQuery = "SELECT 
                                        ica.id, 
                                        ica.total_amount,
                                        ica.credit_percentage,
                                        ica.total_with_interest,
                                        ica.status,
                                        ica.application_date,
                                        ica.updated_at,
                                        u.first_name,
                                        u.last_name
                                    FROM input_credit_applications ica
                                    JOIN farmers f ON ica.farmer_id = f.id
                                    JOIN users u ON f.user_id = u.id
                                    WHERE ica.agrovet_id = $agrovetId
                                    ORDER BY ica.updated_at DESC
                                    LIMIT 5";
                                    
                                    $recentCredits = $app->select_all($recentCreditsQuery);
                                    ?>

                                    <div class="row mb-4">
                                        <!-- Credit Status Summary -->
                                        <div class="col-md-6">
                                            <h6 class="mb-3"><i class="ti ti-activity me-1"></i>Credit Status Summary
                                            </h6>
                                            <div class="d-flex flex-wrap gap-3">
                                                <div class="card bg-primary-transparent p-3 text-center">
                                                    <div class="avatar avatar-sm bg-primary mx-auto mb-2">
                                                        <i class="ti ti-hourglass text-white"></i>
                                                    </div>
                                                    <h3 class="fs-20 fw-semibold mb-0">
                                                        <?= $creditOverview->pending_credits ?? 0 ?></h3>
                                                    <p class="mb-0 fs-12">Pending</p>
                                                </div>
                                                <div class="card bg-success-transparent p-3 text-center">
                                                    <div class="avatar avatar-sm bg-success mx-auto mb-2">
                                                        <i class="ti ti-check text-white"></i>
                                                    </div>
                                                    <h3 class="fs-20 fw-semibold mb-0">
                                                        <?= $creditOverview->approved_credits ?? 0 ?></h3>
                                                    <p class="mb-0 fs-12">Approved</p>
                                                </div>
                                                <div class="card bg-danger-transparent p-3 text-center">
                                                    <div class="avatar avatar-sm bg-danger mx-auto mb-2">
                                                        <i class="ti ti-x text-white"></i>
                                                    </div>
                                                    <h3 class="fs-20 fw-semibold mb-0">
                                                        <?= $creditOverview->rejected_credits ?? 0 ?></h3>
                                                    <p class="mb-0 fs-12">Rejected</p>
                                                </div>
                                                <div class="card bg-info-transparent p-3 text-center">
                                                    <div class="avatar avatar-sm bg-info mx-auto mb-2">
                                                        <i class="ti ti-box text-white"></i>
                                                    </div>
                                                    <h3 class="fs-20 fw-semibold mb-0">
                                                        <?= $creditOverview->fulfilled_credits ?? 0 ?></h3>
                                                    <p class="mb-0 fs-12">Fulfilled</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Credit Metrics -->
                                        <div class="col-md-6">
                                            <h6 class="mb-3"><i class="ti ti-chart-bar me-1"></i>Performance Metrics
                                            </h6>
                                            <div class="d-flex flex-column gap-3">
                                                <div>
                                                    <p class="mb-1 fw-semibold d-flex justify-content-between">
                                                        <span>Approval Rate</span>
                                                        <span><?= $creditOverview->approval_rate ?? 0 ?>%</span>
                                                    </p>
                                                    <div class="progress progress-sm">
                                                        <div class="progress-bar bg-success" role="progressbar"
                                                            style="width: <?= $creditOverview->approval_rate ?? 0 ?>%"
                                                            aria-valuenow="<?= $creditOverview->approval_rate ?? 0 ?>"
                                                            aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <p class="mb-1 fw-semibold d-flex justify-content-between">
                                                        <span>Rejection Rate</span>
                                                        <span><?= $creditOverview->rejection_rate ?? 0 ?>%</span>
                                                    </p>
                                                    <div class="progress progress-sm">
                                                        <div class="progress-bar bg-danger" role="progressbar"
                                                            style="width: <?= $creditOverview->rejection_rate ?? 0 ?>%"
                                                            aria-valuenow="<?= $creditOverview->rejection_rate ?? 0 ?>"
                                                            aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between gap-3">
                                                    <div class="card bg-light p-3 flex-grow-1">
                                                        <p class="mb-1 fs-12 text-muted">Avg. Credit Amount</p>
                                                        <h5 class="mb-0 fw-semibold">KES
                                                            <?= number_format($creditOverview->avg_credit_amount ?? 0, 2) ?>
                                                        </h5>
                                                    </div>
                                                    <div class="card bg-light p-3 flex-grow-1">
                                                        <p class="mb-1 fs-12 text-muted">Total Approved Value</p>
                                                        <h5 class="mb-0 fw-semibold">KES
                                                            <?= number_format($creditOverview->total_approved_amount ?? 0, 2) ?>
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h6 class="mb-3">
                                                <i class="ti ti-category text-success me-1"></i>Input Items Distribution
                                                <span class="badge bg-primary-transparent ms-2 fs-12">
                                                    <i class="ti ti-chart-pie me-1"></i>Breakdown by Type
                                                </span>
                                            </h6>
                                            <div class="table-responsive">
                                                <table class="table table-hover border table-striped">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th><i class="ti ti-tag me-1 text-muted"></i>Input Type</th>
                                                            <th><i class="ti ti-stack-2 me-1 text-muted"></i>Item Count
                                                            </th>
                                                            <th><i class="ti ti-scale me-1 text-muted"></i>Total
                                                                Quantity</th>
                                                            <th><i class="ti ti-cash me-1 text-muted"></i>Total Value
                                                                (KES)</th>
                                                            <th><i class="ti ti-percentage me-1 text-muted"></i>% of
                                                                Total Value</th>
                                                            <th><i
                                                                    class="ti ti-chart-bar me-1 text-muted"></i>Distribution
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                       $typeIcons = [
                                                           'fertilizer' => '<i class="ti ti-garden-cart text-success me-1"></i>',
                                                           'pesticide' => '<i class="ti ti-spray text-danger me-1"></i>',
                                                           'seeds' => '<i class="ti ti-plant-2 text-primary me-1"></i>',
                                                           'tools' => '<i class="ti ti-tool text-warning me-1"></i>',
                                                           'other' => '<i class="ti ti-package text-info me-1"></i>'
                                                       ];
                                                       
                                                       $typeColors = [
                                                           'fertilizer' => 'success',
                                                           'pesticide' => 'danger',
                                                           'seeds' => 'primary',
                                                           'tools' => 'warning',
                                                           'other' => 'info'
                                                       ];
                                                       
                                                       if ($inputDistribution): foreach($inputDistribution as $item): 
                                                           $color = $typeColors[$item->input_type] ?? 'info';
                                                       ?>
                                                        <tr>
                                                            <td class="align-middle">
                                                                <div class="d-flex align-items-center">
                                                                    <span
                                                                        class="avatar avatar-sm bg-<?= $color ?>-transparent me-2">
                                                                        <?= $typeIcons[$item->input_type] ?? '' ?>
                                                                    </span>
                                                                    <span
                                                                        class="fw-semibold"><?= ucfirst($item->input_type) ?></span>
                                                                </div>
                                                            </td>
                                                            <td class="align-middle">
                                                                <span class="badge bg-<?= $color ?>-transparent">
                                                                    <?= number_format($item->item_count) ?>
                                                                </span>
                                                            </td>
                                                            <td class="align-middle">
                                                                <?= number_format($item->total_quantity, 2) ?></td>
                                                            <td class="align-middle fw-semibold text-<?= $color ?>">
                                                                KES <?= number_format($item->total_value, 2) ?>
                                                            </td>
                                                            <td class="align-middle">
                                                                <span class="badge bg-<?= $color ?>">
                                                                    <?= $item->value_percentage ?>%
                                                                </span>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <div class="progress progress-xs flex-grow-1">
                                                                        <div class="progress-bar bg-<?= $color ?>"
                                                                            role="progressbar"
                                                                            style="width: <?= $item->value_percentage ?>%">
                                                                        </div>
                                                                    </div>
                                                                    <i
                                                                        class="ti ti-trending-<?= $item->value_percentage > 25 ? 'up' : 'down' ?> text-<?= $item->value_percentage > 25 ? 'success' : 'danger' ?>"></i>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; else: ?>
                                                        <tr>
                                                            <td colspan="6" class="text-center py-5">
                                                                <div class="empty-state">
                                                                    <i
                                                                        class="ti ti-clipboard-text fs-40 text-muted d-block mb-3 mx-auto"></i>
                                                                    <p class="text-muted mb-2">No input credit items
                                                                        data available</p>
                                                                    <a href="#" class="btn btn-sm btn-primary">
                                                                        <i class="ti ti-plus me-1"></i>Add Input Items
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                    <tfoot class="bg-light">
                                                        <tr>
                                                            <td colspan="6" class="text-end">
                                                                <a href="http://localhost/dfcs/agrovet/credits/inventory-report.php"
                                                                    class="btn btn-sm btn-outline-primary">
                                                                    <i class="ti ti-report me-1"></i>View Full Report
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <h6 class="mb-3">
                                                <i class="ti ti-clipboard-list text-primary me-1"></i>Recent Input
                                                Credit Applications
                                                <span class="badge bg-info-transparent ms-2 fs-12">
                                                    <i class="ti ti-calendar me-1"></i>Last 5 Applications
                                                </span>
                                            </h6>
                                            <div class="table-responsive">
                                                <table class="table table-hover border table-striped">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th><i class="ti ti-hash me-1 text-muted"></i>ID</th>
                                                            <th><i class="ti ti-user me-1 text-muted"></i>Farmer</th>
                                                            <th><i class="ti ti-receipt me-1 text-muted"></i>Amount</th>
                                                            <th><i class="ti ti-coin me-1 text-muted"></i>With Interest
                                                            </th>
                                                            <th><i
                                                                    class="ti ti-calendar me-1 text-muted"></i>Application
                                                                Date</th>
                                                            <th><i class="ti ti-activity me-1 text-muted"></i>Status
                                                            </th>
                                                            <th><i
                                                                    class="ti ti-dots-vertical me-1 text-muted"></i>Action
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if ($recentCredits): foreach($recentCredits as $credit): 
                                                           $statusInfo = [
                                                               'pending' => ['color' => 'warning', 'icon' => 'hourglass', 'text' => 'Pending Review'],
                                                               'under_review' => ['color' => 'info', 'icon' => 'eye', 'text' => 'Under Review'],
                                                               'approved' => ['color' => 'success', 'icon' => 'check', 'text' => 'Approved'],
                                                               'rejected' => ['color' => 'danger', 'icon' => 'x', 'text' => 'Rejected'],
                                                               'fulfilled' => ['color' => 'primary', 'icon' => 'package-check', 'text' => 'Fulfilled'],
                                                               'cancelled' => ['color' => 'secondary', 'icon' => 'ban', 'text' => 'Cancelled']
                                                           ];
                                                           
                                                           $status = $statusInfo[$credit->status] ?? ['color' => 'secondary', 'icon' => 'info-circle', 'text' => ucfirst($credit->status)];
                                                       ?>
                                                        <tr>
                                                            <td class="align-middle">
                                                                <span
                                                                    class="badge bg-primary rounded-pill">#<?= $credit->id ?></span>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="d-flex align-items-center">
                                                                    <span
                                                                        class="avatar avatar-sm bg-light text-dark me-2">
                                                                        <?= strtoupper(substr($credit->first_name, 0, 1) . substr($credit->last_name, 0, 1)) ?>
                                                                    </span>
                                                                    <div>
                                                                        <span
                                                                            class="fw-semibold"><?= htmlspecialchars($credit->first_name . ' ' . $credit->last_name) ?></span>
                                                                        <div class="fs-12 text-muted">Farmer ID:
                                                                            FRM<?= str_pad($credit->farmer_id ?? 0, 6, '0', STR_PAD_LEFT) ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="text-nowrap">
                                                                    <span class="fs-14 fw-semibold">KES
                                                                        <?= number_format($credit->total_amount, 2) ?></span>
                                                                    <div class="fs-12 text-muted">Base amount</div>
                                                                </div>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="text-nowrap">
                                                                    <span class="fs-14 fw-semibold">KES
                                                                        <?= number_format($credit->total_with_interest, 2) ?></span>
                                                                    <div class="fs-12 text-muted">Interest:
                                                                        <?= $credit->credit_percentage ?>%</div>
                                                                </div>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="d-flex align-items-center">
                                                                    <span
                                                                        class="avatar avatar-xs bg-light text-dark me-2">
                                                                        <i class="ti ti-calendar-event"></i>
                                                                    </span>
                                                                    <div>
                                                                        <span
                                                                            class="fs-14"><?= date('M d, Y', strtotime($credit->application_date)) ?></span>
                                                                        <div class="fs-12 text-muted">
                                                                            <?= date('h:i A', strtotime($credit->application_date)) ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="align-middle">
                                                                <span
                                                                    class="badge bg-<?= $status['color'] ?>-transparent">
                                                                    <i class="ti ti-<?= $status['icon'] ?> me-1"></i>
                                                                    <?= $status['text'] ?>
                                                                </span>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="btn-group" role="group">
                                                                    <a href="http://localhost/dfcs/agrovet/credit/review.php?id=<?= $credit->id ?>"
                                                                        class="btn btn-sm btn-primary">
                                                                        <i class="ti ti-eye me-1"></i> View
                                                                    </a>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split"
                                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                                        <span class="visually-hidden">More</span>
                                                                    </button>
                                                                    <ul class="dropdown-menu">
                                                                        <?php if ($credit->status == 'pending' || $credit->status == 'under_review'): ?>
                                                                        <li><a class="dropdown-item" href="#"><i
                                                                                    class="ti ti-check me-1 text-success"></i>
                                                                                Approve</a></li>
                                                                        <li><a class="dropdown-item" href="#"><i
                                                                                    class="ti ti-x me-1 text-danger"></i>
                                                                                Reject</a></li>
                                                                        <?php elseif ($credit->status == 'approved'): ?>
                                                                        <li><a class="dropdown-item" href="#"><i
                                                                                    class="ti ti-package me-1 text-primary"></i>
                                                                                Mark Fulfilled</a></li>
                                                                        <?php endif; ?>
                                                                        <li><a class="dropdown-item" href="#"><i
                                                                                    class="ti ti-printer me-1"></i>
                                                                                Print Details</a></li>
                                                                    </ul>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; else: ?>
                                                        <tr>
                                                            <td colspan="7" class="text-center py-5">
                                                                <div class="empty-state">
                                                                    <i
                                                                        class="ti ti-file-off fs-40 text-muted d-block mb-3 mx-auto"></i>
                                                                    <p class="text-muted mb-2">No recent input credit
                                                                        applications found</p>
                                                                    <a href="http://localhost/dfcs/agrovet/credit/new.php"
                                                                        class="btn btn-sm btn-primary">
                                                                        <i class="ti ti-plus me-1"></i>Create New
                                                                        Application
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                    <tfoot class="bg-light">
                                                        <tr>
                                                            <td colspan="7" class="text-end">
                                                                <a href="http://localhost/dfcs/agrovet/credit/applications.php"
                                                                    class="btn btn-sm btn-outline-primary">
                                                                    <i class="ti ti-list me-1"></i>View All Applications
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Farmer Input Selection Insights -->
                    <div class="row mt-4">
                        <div class="col-xl-12">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <h6 class="card-title"><i class="ti ti-shopping-cart me-2"></i>Farmer Input
                                        Selection Insights</h6>
                                </div>
                                <div class="card-body">
                                    <?php
                // Get popular inputs selected by farmers for this agrovet
                $popularInputsQuery = "SELECT 
                    ic.id,
                    ic.name,
                    ic.type,
                    ic.standard_unit,
                    ic.standard_price,
                    COUNT(ici.id) as selection_count,
                    SUM(ici.quantity) as total_quantity,
                    SUM(ici.total_price) as total_value,
                    COUNT(DISTINCT ica.farmer_id) as farmer_count
                FROM input_credit_items ici
                JOIN input_credit_applications ica ON ici.credit_application_id = ica.id
                JOIN input_catalog ic ON ici.input_catalog_id = ic.id
                WHERE ica.agrovet_id = $agrovetId
                GROUP BY ic.id, ic.name, ic.type, ic.standard_unit, ic.standard_price
                ORDER BY selection_count DESC
                LIMIT 8";
                
                $popularInputs = $app->select_all($popularInputsQuery);
                
                // Get farmer selection patterns
                $selectionPatternsQuery = "SELECT 
                    COUNT(DISTINCT ica.id) as total_applications,
                    AVG(ica.total_amount) as avg_credit_amount,
                    SUM(CASE WHEN ica.total_amount < 1000 THEN 1 ELSE 0 END) as small_orders,
                    SUM(CASE WHEN ica.total_amount BETWEEN 1000 AND 5000 THEN 1 ELSE 0 END) as medium_orders,
                    SUM(CASE WHEN ica.total_amount > 5000 THEN 1 ELSE 0 END) as large_orders,
                    COUNT(DISTINCT ica.farmer_id) as unique_farmers,
                    ROUND(AVG(items_per_app.item_count), 1) as avg_items_per_application
                FROM input_credit_applications ica
                JOIN (
                    SELECT 
                        credit_application_id,
                        COUNT(*) as item_count
                    FROM input_credit_items
                    GROUP BY credit_application_id
                ) as items_per_app ON ica.id = items_per_app.credit_application_id
                WHERE ica.agrovet_id = $agrovetId";
                
                $selectionPatterns = $app->select_one($selectionPatternsQuery);
                
                // Get common input combinations
                $combinationsQuery = "SELECT 
                    GROUP_CONCAT(DISTINCT ic.name ORDER BY ic.name SEPARATOR ' + ') as combination,
                    COUNT(DISTINCT ica.id) as application_count
                FROM input_credit_applications ica
                JOIN input_credit_items ici1 ON ica.id = ici1.credit_application_id
                JOIN input_credit_items ici2 ON ica.id = ici2.credit_application_id AND ici1.id < ici2.id
                JOIN input_catalog ic ON ici1.input_catalog_id = ic.id OR ici2.input_catalog_id = ic.id
                WHERE ica.agrovet_id = $agrovetId
                GROUP BY ica.id
                HAVING COUNT(DISTINCT ic.id) >= 2
                ORDER BY application_count DESC
                LIMIT 5";
                
                $combinations = $app->select_all($combinationsQuery);
                ?>

                                    <div class="row mb-4">
                                        <!-- Farmer Selection Patterns -->
                                        <div class="col-md-4">
                                            <h6 class="mb-3"><i class="ti ti-chart-bar me-1"></i>Selection Patterns</h6>
                                            <div class="card border p-3">
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="fs-12 text-muted">Total Applications</span>
                                                        <span class="badge bg-primary-transparent">
                                                            <?= number_format($selectionPatterns->total_applications ?? 0) ?>
                                                        </span>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="fs-12 text-muted">Unique Farmers</span>
                                                        <span class="badge bg-info-transparent">
                                                            <?= number_format($selectionPatterns->unique_farmers ?? 0) ?>
                                                        </span>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="fs-12 text-muted">Avg. Items Per Application</span>
                                                        <span class="badge bg-success-transparent">
                                                            <?= $selectionPatterns->avg_items_per_application ?? 0 ?>
                                                        </span>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="fs-12 text-muted">Avg. Credit Amount</span>
                                                        <span class="fw-semibold">KES
                                                            <?= number_format($selectionPatterns->avg_credit_amount ?? 0, 2) ?></span>
                                                    </div>
                                                </div>

                                                <h6 class="mb-2 fs-12 text-muted">Order Size Distribution</h6>
                                                <div class="d-flex gap-2 align-items-center mb-3">
                                                    <div class="progress flex-grow-1 progress-sm">
                                                        <?php
                                    $totalOrders = ($selectionPatterns->small_orders ?? 0) + 
                                                  ($selectionPatterns->medium_orders ?? 0) + 
                                                  ($selectionPatterns->large_orders ?? 0);
                                    $smallPercent = $totalOrders > 0 ? ($selectionPatterns->small_orders / $totalOrders) * 100 : 0;
                                    $mediumPercent = $totalOrders > 0 ? ($selectionPatterns->medium_orders / $totalOrders) * 100 : 0;
                                    $largePercent = $totalOrders > 0 ? ($selectionPatterns->large_orders / $totalOrders) * 100 : 0;
                                    ?>
                                                        <div class="progress-bar bg-info" role="progressbar"
                                                            style="width: <?= $smallPercent ?>%"
                                                            title="Small Orders: <?= number_format($selectionPatterns->small_orders ?? 0) ?>">
                                                        </div>
                                                        <div class="progress-bar bg-primary" role="progressbar"
                                                            style="width: <?= $mediumPercent ?>%"
                                                            title="Medium Orders: <?= number_format($selectionPatterns->medium_orders ?? 0) ?>">
                                                        </div>
                                                        <div class="progress-bar bg-success" role="progressbar"
                                                            style="width: <?= $largePercent ?>%"
                                                            title="Large Orders: <?= number_format($selectionPatterns->large_orders ?? 0) ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between fs-12">
                                                    <span><i class="ti ti-circle-filled text-info me-1"></i>Small (<1K)<
                                                            /span>
                                                            <span><i
                                                                    class="ti ti-circle-filled text-primary me-1"></i>Medium
                                                                (1K-5K)</span>
                                                            <span><i
                                                                    class="ti ti-circle-filled text-success me-1"></i>Large
                                                                (>5K)</span>
                                                </div>
                                            </div>

                                            <h6 class="mb-3 mt-4"><i class="ti ti-packge-import me-1"></i>Common
                                                Combinations</h6>
                                            <div class="card border p-0">
                                                <ul class="list-group list-group-flush">
                                                    <?php if ($combinations): foreach($combinations as $combo): ?>
                                                    <li
                                                        class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span><?= htmlspecialchars($combo->combination) ?></span>
                                                        <span
                                                            class="badge bg-primary rounded-pill"><?= $combo->application_count ?></span>
                                                    </li>
                                                    <?php endforeach; else: ?>
                                                    <li class="list-group-item text-center py-3">
                                                        <p class="text-muted mb-0">No common combinations found</p>
                                                    </li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        </div>

                                        <!-- Popular Inputs Visualization -->
                                        <div class="col-md-8">
                                            <h6 class="mb-3"><i class="ti ti-star me-1"></i>Most Selected Inputs</h6>
                                            <div class="row g-3">
                                                <?php 
                            $categoryIcons = [
                                'fertilizer' => ['icon' => 'ti-garden-cart', 'color' => 'success'],
                                'pesticide' => ['icon' => 'ti-spray', 'color' => 'danger'],
                                'seeds' => ['icon' => 'ti-plant-2', 'color' => 'primary'],
                                'tools' => ['icon' => 'ti-tool', 'color' => 'warning'],
                                'other' => ['icon' => 'ti-package', 'color' => 'info']
                            ];
                            
                            if ($popularInputs): foreach($popularInputs as $input): 
                                $icon = $categoryIcons[$input->type]['icon'] ?? 'ti-package';
                                $color = $categoryIcons[$input->type]['color'] ?? 'secondary';
                            ?>
                                                <div class="col-md-6">
                                                    <div class="card border h-100">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex align-items-center mb-3">
                                                                <span
                                                                    class="avatar avatar-sm bg-<?= $color ?>-transparent me-2">
                                                                    <i class="ti <?= $icon ?>"></i>
                                                                </span>
                                                                <div>
                                                                    <div class="fw-semibold text-truncate"
                                                                        style="max-width: 200px;"
                                                                        title="<?= htmlspecialchars($input->name) ?>">
                                                                        <?= htmlspecialchars($input->name) ?>
                                                                    </div>
                                                                    <div class="fs-12 text-muted">
                                                                        <?= ucfirst($input->type) ?></div>
                                                                </div>
                                                            </div>

                                                            <div class="row g-2 fs-12 mb-3">
                                                                <div class="col-6">
                                                                    <div class="card bg-light p-2">
                                                                        <div class="text-muted">Selected by</div>
                                                                        <div class="fw-semibold">
                                                                            <?= number_format($input->farmer_count) ?>
                                                                            farmers</div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="card bg-light p-2">
                                                                        <div class="text-muted">Total Quantity</div>
                                                                        <div class="fw-semibold">
                                                                            <?= number_format($input->total_quantity, 2) ?>
                                                                            <?= $input->standard_unit ?></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="card bg-light p-2">
                                                                        <div class="text-muted">Unit Price</div>
                                                                        <div class="fw-semibold">KES
                                                                            <?= number_format($input->standard_price, 2) ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="card bg-light p-2">
                                                                        <div class="text-muted">Total Value</div>
                                                                        <div class="fw-semibold">KES
                                                                            <?= number_format($input->total_value, 2) ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div
                                                                class="d-flex justify-content-between align-items-center small">
                                                                <span class="text-muted">Selection Frequency</span>
                                                                <span
                                                                    class="badge bg-<?= $color ?>"><?= $input->selection_count ?>
                                                                    times</span>
                                                            </div>
                                                            <div class="progress progress-sm mt-1">
                                                                <div class="progress-bar bg-<?= $color ?>"
                                                                    role="progressbar"
                                                                    style="width: <?= min(($input->selection_count / ($popularInputs[0]->selection_count ?? 1)) * 100, 100) ?>%">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endforeach; else: ?>
                                                <div class="col-12">
                                                    <div class="card border p-4 text-center">
                                                        <i
                                                            class="ti ti-shopping-cart-off fs-40 text-muted d-block mb-3 mx-auto"></i>
                                                        <p class="text-muted mb-2">No input selection data available</p>
                                                    </div>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end mt-2">
                                        <a href="http://localhost/dfcs/agrovet/reports/input-analysis.php"
                                            class="btn btn-primary">
                                            <i class="ti ti-report-analytics me-1"></i>View Full Input Analysis
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
        <script src="http://localhost/dfcs/assets/js/sticky.js"></script>

        <!-- Simplebar JS -->
        <script src="http://localhost/dfcs/assets/libs/simplebar/simplebar.min.js">
        </script>
        <script src="http://localhost/dfcs/assets/js/simplebar.js"></script>

        <!-- Color Picker JS -->
        <script src="http://localhost/dfcs/assets/libs/%40simonwep/pickr/pickr.es5.min.js">
        </script>



        <!-- Custom-Switcher JS -->
        <script src="http://localhost/dfcs/assets/js/custom-switcher.min.js">
        </script>

        <!-- Gallery JS -->
        <script src="http://localhost/dfcs/assets/libs/glightbox/js/glightbox.min.js">
        </script>

        <!-- Internal Profile JS -->
        <script src="http://localhost/dfcs/assets/js/profile.js"></script>

        <!-- Custom JS -->
        <script src="http://localhost/dfcs/assets/js/custom.js"></script>
        <!-- the toast -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
            integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js">
        </script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Event listener for view details buttons
            document.querySelectorAll('.view-details').forEach(button => {
                button.addEventListener('click', function() {
                    const oldValues = this.getAttribute('data-old-values');
                    const newValues = this.getAttribute('data-new-values');

                    // Set modal content
                    document.getElementById('modal-action').textContent = ucfirst(
                        this.getAttribute('data-action'));
                    document.getElementById('modal-table').textContent = ucfirst(
                        this.getAttribute('data-table').replace(/_/g, ' '));
                    document.getElementById('modal-record').textContent = this
                        .getAttribute('data-record');
                    document.getElementById('modal-date').textContent = this
                        .getAttribute('data-date');

                    // Format and display the values
                    try {
                        const oldValuesObj = oldValues ? JSON.parse(oldValues) :
                            null;
                        const newValuesObj = newValues ? JSON.parse(newValues) :
                            null;

                        // Display old values
                        const oldValuesContainer = document.getElementById(
                            'old-values-container');
                        oldValuesContainer.innerHTML = '';

                        if (oldValuesObj) {
                            const table = document.createElement('table');
                            table.className = 'table table-sm';
                            table.innerHTML =
                                '<thead><tr><th>Field</th><th>Value</th></tr></thead><tbody></tbody>';

                            for (const [key, value] of Object.entries(
                                    oldValuesObj)) {
                                const row = table.querySelector('tbody')
                                    .insertRow();
                                const cell1 = row.insertCell(0);
                                const cell2 = row.insertCell(1);

                                cell1.textContent = ucfirst(key.replace(/_/g, ' '));
                                cell2.textContent = value !== null ? value : 'null';
                            }

                            oldValuesContainer.appendChild(table);
                        } else {
                            oldValuesContainer.textContent = 'No previous data';
                        }

                        // Display new values
                        const newValuesContainer = document.getElementById(
                            'new-values-container');
                        newValuesContainer.innerHTML = '';

                        if (newValuesObj) {
                            const table = document.createElement('table');
                            table.className = 'table table-sm';
                            table.innerHTML =
                                '<thead><tr><th>Field</th><th>Value</th></tr></thead><tbody></tbody>';

                            for (const [key, value] of Object.entries(
                                    newValuesObj)) {
                                const row = table.querySelector('tbody')
                                    .insertRow();
                                const cell1 = row.insertCell(0);
                                const cell2 = row.insertCell(1);

                                cell1.textContent = ucfirst(key.replace(/_/g, ' '));
                                cell2.textContent = value !== null ? value : 'null';
                            }

                            newValuesContainer.appendChild(table);
                        } else {
                            newValuesContainer.textContent = 'No new data';
                        }

                    } catch (e) {
                        // Handle parsing errors
                        document.getElementById('old-values-container')
                            .textContent = 'Unable to parse old values';
                        document.getElementById('new-values-container')
                            .textContent = 'Unable to parse new values';
                        console.error('Error parsing JSON:', e);
                    }
                });
            });

            // Helper function to capitalize first letter
            function ucfirst(string) {
                return string.charAt(0).toUpperCase() + string.slice(1);
            }
        });
        </script>


        <script>
        $(document).ready(function() {
            // Tab navigation
            $('#nextBasic').click(function(e) {
                e.preventDefault();
                $('#agrovetTabs button[data-bs-target="#work-info"]').tab('show');
            });

            $('#prevWork').click(function(e) {
                e.preventDefault();
                $('#agrovetTabs button[data-bs-target="#basic-info"]').tab('show');
            });

            $('#nextWork').click(function(e) {
                e.preventDefault();
                $('#agrovetTabs button[data-bs-target="#account-info"]').tab('show');
            });

            $('#prevAccount').click(function(e) {
                e.preventDefault();
                $('#agrovetTabs button[data-bs-target="#work-info"]').tab('show');
            });

            // Email availability checking - only check if email has changed
            $("#email").on("input", function() {
                const email = $(this).val();
                const originalEmail = $("#original-email").val();

                if (email && email !== originalEmail) {
                    $.ajax({
                        type: "POST",
                        url: "http://localhost/dfcs/ajax/authentication-controller/check-email-availability.php",
                        data: {
                            email: email
                        },
                        success: function(response) {
                            if (response === "available") {
                                $("#email-success").text("Email is available");
                                $("#email-error").text("");
                                $("button[onclick='updateAgrovetStaff()']").prop("disabled",
                                    false);
                            } else {
                                $("#email-error").text("Email is already in use");
                                $("#email-success").text("");
                                $("button[onclick='updateAgrovetStaff()']").prop("disabled",
                                    true);
                            }
                        }
                    });
                } else {
                    $("#email-error").text("");
                    $("#email-success").text("");
                    $("button[onclick='updateAgrovetStaff()']").prop("disabled", false);
                }
            });
        });

        function updateAgrovetStaff() {
            // Validate passwords if provided
            if ($('#password').val() || $('#confirm-password').val()) {
                if ($('#password').val() !== $('#confirm-password').val()) {
                    toastr.error('Passwords do not match', 'Error');
                    return;
                }
            }

            // Get form values
            const formData = new FormData();

            // Staff table data
            formData.append('id', $('#staff-id').val());
            formData.append('employee_number', $('#employee-number').val());
            formData.append('position', $('#position').val());
            formData.append('phone', $('#phone').val());

            // User table data
            formData.append('user_id', $('#user-id').val());
            formData.append('first_name', $('#first-name').val());
            formData.append('last_name', $('#last-name').val());
            formData.append('email', $('#email').val());
            formData.append('username', $('#username').val());

            // Only append password if it's being changed
            if ($('#password').val()) {
                formData.append('password', $('#password').val());
            }

            // Validate required fields
            const requiredFields = {
                'First Name': $('#first-name').val(),
                'Last Name': $('#last-name').val(),
                'Email': $('#email').val(),
                'Employee Number': $('#employee-number').val(),
                'Position': $('#position').val(),
                'Username': $('#username').val()
            };

            for (const [field, value] of Object.entries(requiredFields)) {
                if (!value) {
                    toastr.error(`${field} is required`, 'Error');
                    return;
                }
            }

            // Send update request
            $.ajax({
                url: 'http://localhost/dfcs/ajax/agrovet-controller/update-staff.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    try {
                        const data = JSON.parse(response);
                        if (data.success) {
                            toastr.success(data.message, 'Success', {
                                timeOut: 3000,
                                closeButton: true,
                                progressBar: true,
                                positionClass: "toast-top-right"
                            });
                            setTimeout(() => {
                                window.location.href = 'http://localhost/dfcs/agrovet/profile/';
                            }, 2000);
                        } else {
                            toastr.error(data.message, 'Error');
                        }
                    } catch (e) {
                        toastr.error('Error processing response', 'Error');
                    }
                },
                error: function() {
                    toastr.error('Server error occurred', 'Error');
                }
            });
        }
        </script>
</body>

</html>