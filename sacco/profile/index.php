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
                                                    
                                        // Get SACCO staff profile info from user_id
                                        $query = "SELECT s.id as staff_id, s.position, s.staff_id as employee_id, s.department,
                                                  u.first_name, u.last_name, u.phone, u.email, u.location, u.profile_picture 
                                                  FROM sacco_staff s
                                                  JOIN users u ON s.user_id = u.id
                                                  WHERE s.user_id = $userId";
                                        $staff = $app->select_one($query);
                                                    
                                        if (!$staff) {
                                            header("Location: http://localhost/dfcs/"); 
                                            exit();
                                        }
                                                    
                                        // Get stats
                                        $statsQuery = "SELECT 
                                            (SELECT COUNT(*) FROM loan_applications la 
                                             JOIN loan_logs ll ON la.id = ll.loan_application_id 
                                             WHERE ll.user_id = $userId) as processed_loans,
                                            
                                            (SELECT COUNT(*) FROM loan_applications la 
                                             JOIN approved_loans al ON la.id = al.loan_application_id 
                                             WHERE al.approved_by = $userId AND al.status = 'active') as active_loans,
                                            
                                            (SELECT CONCAT(ROUND(
                                                (COUNT(CASE WHEN la.status = 'approved' OR la.status = 'disbursed' THEN 1 END) * 100.0 / 
                                                NULLIF(COUNT(*), 0)), 0), '%')
                                             FROM loan_applications la
                                             WHERE la.reviewed_by = $userId) as approval_rate,
                                            
                                            (SELECT ROUND(
                                                (COUNT(CASE WHEN la.status = 'approved' OR la.status = 'disbursed' THEN 1 END) * 100.0 / 
                                                NULLIF(10, 0)), 0)
                                             FROM loan_applications la 
                                             WHERE la.reviewed_by = $userId AND MONTH(la.review_date) = MONTH(CURRENT_DATE) 
                                             AND YEAR(la.review_date) = YEAR(CURRENT_DATE)) as monthly_target";
                                                    
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
                                    <?= htmlspecialchars($staff->department ?? 'Department not set') ?>
                                </span>
                                <span class="me-3">
                                    <i class="ri-user-3-line me-1 align-middle"></i>
                                    Staff ID: <?= htmlspecialchars($staff->staff_id) ?>
                                </span>
                            </p>
                            <div class="d-flex mb-0">
                                <div class="me-4">
                                    <p class="fw-bold fs-20 text-fixed-white text-shadow mb-0">
                                        <?= $stats->processed_loans ?? 0 ?></p>
                                    <p class="mb-0 fs-11 op-5 text-fixed-white">Loans Processed</p>
                                </div>
                                <div class="me-4">
                                    <p class="fw-bold fs-20 text-fixed-white text-shadow mb-0">
                                        <?= $stats->active_loans ?? 0 ?></p>
                                    <p class="mb-0 fs-11 op-5 text-fixed-white">Active Loans</p>
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
                    <!-- loan stats -->
                    <?php
                    // Get loan statistics
                    $loanStatsQuery = "SELECT
                    (SELECT COUNT(*) FROM loan_applications la
                    WHERE la.reviewed_by = $userId) as total_processed,

                    (SELECT ROUND(
                    ((SELECT COUNT(*) FROM loan_applications la
                    WHERE la.reviewed_by = $userId AND MONTH(la.updated_at) = MONTH(CURRENT_DATE)
                    AND YEAR(la.updated_at) = YEAR(CURRENT_DATE)) -
                    (SELECT COUNT(*) FROM loan_applications la
                    WHERE la.reviewed_by = $userId AND MONTH(la.updated_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
                    AND YEAR(la.updated_at) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH))) /
                    NULLIF((SELECT COUNT(*) FROM loan_applications la
                    WHERE la.reviewed_by = $userId AND MONTH(la.updated_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
                    AND YEAR(la.updated_at) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)), 0) * 100, 1)
                    ) as monthly_change,

                    (SELECT COUNT(*) FROM loan_applications la
                    WHERE la.reviewed_by = $userId AND MONTH(la.updated_at) = MONTH(CURRENT_DATE)
                    AND YEAR(la.updated_at) = YEAR(CURRENT_DATE)) as current_month_loans,

                    (SELECT COUNT(*) FROM loan_applications la
                    WHERE la.reviewed_by = $userId AND MONTH(la.updated_at) = MONTH(CURRENT_DATE)
                    AND YEAR(la.updated_at) = YEAR(CURRENT_DATE) AND la.status = 'approved') as approved_this_month,

                    (SELECT COUNT(*) FROM loan_applications la
                    WHERE la.reviewed_by = $userId AND MONTH(la.updated_at) = MONTH(CURRENT_DATE)
                    AND YEAR(la.updated_at) = YEAR(CURRENT_DATE) AND la.status = 'rejected') as rejected_this_month";

                    $loanStats = $app->select_one($loanStatsQuery);
                    ?>
                    <!-- Middle Section Stats Cards -->
                    <div class="row mt-4 mb-4">
                        <!-- Loan Processing Summary Card -->
                        <div class="col-xl-4">
                            <div class="card custom-card bg-primary-transparent">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-lg bg-primary-transparent rounded-circle">
                                            <i class="ti ti-file-description fs-20 text-primary"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h5 class="mb-1 fw-semibold">
                                                <?= number_format($loanStats->total_processed ?? 0) ?></h5>
                                            <p class="mb-0 text-muted fs-12">Total Loans Processed</p>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <div class="d-flex align-items-center">
                                            <span class="me-2 fs-14">
                                                <i class="ti ti-trending-up text-success"></i>
                                                <?= $loanStats->monthly_change ?? 0 ?>%
                                            </span>
                                            <span class="fs-12 text-muted">from last month</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Performance Metrics Card -->
                        <div class="col-xl-4">
                            <div class="card custom-card bg-success-transparent">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-lg bg-success-transparent rounded-circle">
                                            <i class="ti ti-chart-pie fs-20 text-success"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h5 class="mb-1 fw-semibold"><?= $loanStats->approval_rate ?? '0%' ?></h5>
                                            <p class="mb-0 text-muted fs-12">Approval Rate</p>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <div class="progress progress-xs progress-animate">
                                            <div class="progress-bar bg-success"
                                                style="width: <?= str_replace('%', '', $loanStats->approval_rate ?? 0) ?>%">
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mt-1">
                                            <span class="fs-12 text-muted">Target: 85%</span>
                                            <span class="fs-12 text-muted">Current:
                                                <?= $loanStats->approval_rate ?? '0%' ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Current Period Activity Card -->
                        <div class="col-xl-4">
                            <div class="card custom-card bg-warning-transparent">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-lg bg-warning-transparent rounded-circle">
                                            <i class="ti ti-calendar-stats fs-20 text-warning"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h5 class="mb-1 fw-semibold">
                                                <?= number_format($loanStats->current_month_loans ?? 0) ?></h5>
                                            <p class="mb-0 text-muted fs-12">This Month's Loans</p>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <span class="badge bg-success-transparent me-1">
                                                    <i class="ti ti-check"></i>
                                                    <?= $loanStats->approved_this_month ?? 0 ?> Approved
                                                </span>
                                            </div>
                                            <div>
                                                <span class="badge bg-danger-transparent">
                                                    <i class="ti ti-x"></i> <?= $loanStats->rejected_this_month ?? 0 ?>
                                                    Rejected
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php 
               
                     // First query: Get the staff ID from user ID
                 $staffIdQuery = "SELECT id as staff_id 
                                 FROM sacco_staff 
                                 WHERE user_id = $userId";
                 $staffIdResult = $app->select_one($staffIdQuery);
                $staffId = $staffIdResult->staff_id;

                     $query = "SELECT 
                                 ss.id,
                                 ss.staff_id,
                                 ss.position,
                                 ss.department,
                                 ss.user_id,
                                 u.first_name,
                                 u.last_name,
                                 u.email,
                                 u.phone,
                                 u.username
                               FROM sacco_staff ss
                               INNER JOIN users u ON ss.user_id = u.id 
                               WHERE ss.id = $staffId";
                               
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
                                    <div class="card-title">Update SACCO Staff Details</div>
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

                                    <!-- Tabs Navigation -->
                                    <ul class="nav nav-tabs" id="saccoTabs" role="tablist">
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
                                                    <label class="form-label">Staff ID</label>
                                                    <input type="text" class="form-control" id="staff-number"
                                                        value="<?php echo htmlspecialchars($staff->staff_id); ?>"
                                                        required>
                                                </div>
                                                <div class="col-xl-6">
                                                    <label class="form-label">Position</label>
                                                    <input type="text" class="form-control" id="position"
                                                        value="<?php echo htmlspecialchars($staff->position); ?>"
                                                        required>
                                                </div>
                                                <div class="col-xl-12">
                                                    <label class="form-label">Department</label>
                                                    <input type="text" class="form-control" id="department"
                                                        value="<?php echo htmlspecialchars($staff->department); ?>">
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
                                                <button class="btn text-white" onclick="updateSaccoStaff()"
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

                    <!-- Activity Logs Card with Improved Styling -->
                    <div class="row mt-4">
                        <div class="col-xl-12">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <h6 class="card-title">Recent Activity Logs</h6>
                                </div>
                                <div class="card-body">
                                    <?php
                                   $activityLogsQuery = "SELECT al.*, la.id as loan_id, 
                                       CONCAT(u.first_name, ' ', u.last_name) as farmer_name
                                       FROM activity_logs al
                                       LEFT JOIN loan_applications la ON al.description LIKE CONCAT('%', la.id, '%')
                                       LEFT JOIN users u ON la.farmer_id = u.id
                                       WHERE al.user_id = $userId
                                       ORDER BY al.created_at DESC
                                       LIMIT 10";
                                   $activityLogs = $app->select_all($activityLogsQuery);
                                   ?>

                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Activity Type</th>
                                                    <th>Description</th>
                                                    <th>Date & Time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if ($activityLogs): foreach($activityLogs as $log): ?>
                                                <tr>
                                                    <td>
                                                        <span class="d-flex align-items-center">
                                                            <?php 
                                                              $icon = '';
                                                              $badgeClass = '';
                                                             switch (explode('_', $log->activity_type)[0]) {
                                                                  case 'loan': 
                                                                      $icon = 'fa fa-file-invoice'; 
                                                                      $badgeClass = 'bg-primary';
                                                                      break;
                                                                  case 'farmer': 
                                                                      $icon = 'fa fa-user'; 
                                                                      $badgeClass = 'bg-success';
                                                                      break;
                                                                  case 'system': 
                                                                      $icon = 'fa fa-cogs'; 
                                                                      $badgeClass = 'bg-warning';
                                                                      break;
                                                                  case 'produce': 
                                                                      $icon = 'fa fa-shopping-cart'; 
                                                                      $badgeClass = 'bg-info';
                                                                      break;
                                                                  default: 
                                                                      $icon = 'fa fa-history'; 
                                                                      $badgeClass = 'bg-secondary';
                                                                      break;
                                                              }
                                                              ?>
                                                            <span class="avatar avatar-sm <?= $badgeClass ?> me-2">
                                                                <i class="<?= $icon ?> text-white"></i>
                                                            </span>
                                                            <?= ucfirst(str_replace('_', ' ', $log->activity_type)) ?>
                                                        </span>
                                                    </td>
                                                    <td><?= htmlspecialchars($log->description) ?></td>
                                                    <td><?= date('M d, Y h:i A', strtotime($log->created_at)) ?></td>
                                                </tr>
                                                <?php endforeach; else: ?>
                                                <tr>
                                                    <td colspan="3" class="text-center py-5">
                                                        <i
                                                            class="ti ti-mood-empty fs-40 text-muted d-block mx-auto mb-3"></i>
                                                        <p class="text-muted">No activity logs found</p>
                                                    </td>
                                                </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Loan Management Overview Card -->
                    <div class="row mt-4">
                        <div class="col-xl-12">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <h6 class="card-title">Loan Management Overview</h6>
                                </div>
                                <div class="card-body">
                                    <?php
                                                    // Get loan statistics
                                                    $loanOverviewQuery = "SELECT 
                                                        COUNT(CASE WHEN la.status = 'pending' OR la.status = 'under_review' THEN 1 END) as pending_loans,
                                                        COUNT(CASE WHEN la.status = 'approved' OR la.status = 'disbursed' THEN 1 END) as approved_loans,
                                                        COUNT(CASE WHEN la.status = 'rejected' THEN 1 END) as rejected_loans,
                                                        COUNT(CASE WHEN la.status = 'completed' THEN 1 END) as completed_loans,
                                                        ROUND(
                                                            (COUNT(CASE WHEN la.status = 'approved' OR la.status = 'disbursed' OR la.status = 'completed' THEN 1 END) * 100.0 / 
                                                            NULLIF(COUNT(*), 0)), 1) as approval_rate,
                                                        ROUND(
                                                            (COUNT(CASE WHEN la.status = 'rejected' THEN 1 END) * 100.0 / 
                                                            NULLIF(COUNT(*), 0)), 1) as rejection_rate,
                                                        ROUND(AVG(la.amount_requested), 2) as avg_loan_amount
                                                    FROM loan_applications la 
                                                    WHERE la.reviewed_by = $userId";
                                                    
                                                    $loanOverview = $app->select_one($loanOverviewQuery);
                                                    
                                                    // Get loans by month for current year
                                                    $loansByMonthQuery = "SELECT 
                                                        MONTH(la.application_date) as month,
                                                        COUNT(*) as total_loans,
                                                        COUNT(CASE WHEN la.status = 'approved' OR la.status = 'disbursed' OR la.status = 'completed' THEN 1 END) as approved_loans
                                                    FROM loan_applications la 
                                                    WHERE la.reviewed_by = $userId
                                                    AND YEAR(la.application_date) = YEAR(CURRENT_DATE)
                                                    GROUP BY MONTH(la.application_date)
                                                    ORDER BY MONTH(la.application_date)";
                                                    
                                                    $loansByMonth = $app->select_all($loansByMonthQuery);
                                                    
                                                    // Get recent loans
                                                    $recentLoansQuery = "SELECT 
                                                        la.id, 
                                                        la.amount_requested,
                                                        la.status,
                                                        la.application_date,
                                                        la.updated_at,
                                                        u.first_name,
                                                        u.last_name,
                                                        lt.name as loan_type
                                                    FROM loan_applications la
                                                    JOIN users u ON la.farmer_id = u.id
                                                    JOIN loan_types lt ON la.loan_type_id = lt.id
                                                    WHERE la.reviewed_by = $userId
                                                    ORDER BY la.updated_at DESC
                                                    LIMIT 5";
                                                    
                                                    $recentLoans = $app->select_all($recentLoansQuery);
                                                    ?>

                                    <div class="row mb-4">
                                        <!-- Loan Status Summary -->
                                        <div class="col-md-6">
                                            <h6 class="mb-3">Loan Status Summary</h6>
                                            <div class="d-flex flex-wrap gap-3">
                                                <div class="card bg-primary-transparent p-3 text-center">
                                                    <h3 class="fs-20 fw-semibold mb-0">
                                                        <?= $loanOverview->pending_loans ?? 0 ?></h3>
                                                    <p class="mb-0 fs-12">Pending</p>
                                                </div>
                                                <div class="card bg-success-transparent p-3 text-center">
                                                    <h3 class="fs-20 fw-semibold mb-0">
                                                        <?= $loanOverview->approved_loans ?? 0 ?></h3>
                                                    <p class="mb-0 fs-12">Approved</p>
                                                </div>
                                                <div class="card bg-danger-transparent p-3 text-center">
                                                    <h3 class="fs-20 fw-semibold mb-0">
                                                        <?= $loanOverview->rejected_loans ?? 0 ?></h3>
                                                    <p class="mb-0 fs-12">Rejected</p>
                                                </div>
                                                <div class="card bg-info-transparent p-3 text-center">
                                                    <h3 class="fs-20 fw-semibold mb-0">
                                                        <?= $loanOverview->completed_loans ?? 0 ?></h3>
                                                    <p class="mb-0 fs-12">Completed</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Loan Metrics -->
                                        <div class="col-md-6">
                                            <h6 class="mb-3">Performance Metrics</h6>
                                            <div class="d-flex flex-column gap-3">
                                                <div>
                                                    <p class="mb-1 fw-semibold">Approval Rate</p>
                                                    <div class="progress progress-sm">
                                                        <div class="progress-bar bg-success" role="progressbar"
                                                            style="width: <?= $loanOverview->approval_rate ?? 0 ?>%"
                                                            aria-valuenow="<?= $loanOverview->approval_rate ?? 0 ?>"
                                                            aria-valuemin="0" aria-valuemax="100">
                                                            <?= $loanOverview->approval_rate ?? 0 ?>%
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <p class="mb-1 fw-semibold">Rejection Rate</p>
                                                    <div class="progress progress-sm">
                                                        <div class="progress-bar bg-danger" role="progressbar"
                                                            style="width: <?= $loanOverview->rejection_rate ?? 0 ?>%"
                                                            aria-valuenow="<?= $loanOverview->rejection_rate ?? 0 ?>"
                                                            aria-valuemin="0" aria-valuemax="100">
                                                            <?= $loanOverview->rejection_rate ?? 0 ?>%
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <p class="mb-1 fw-semibold">Average Loan Amount</p>
                                                    <h4 class="mb-0">KES
                                                        <?= number_format($loanOverview->avg_loan_amount ?? 0, 2) ?>
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <h6 class="mb-3">Recent Loan Applications</h6>
                                            <div class="table-responsive">
                                                <table class="table table-hover border">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Farmer</th>
                                                            <th>Loan Type</th>
                                                            <th>Amount</th>
                                                            <th>Application Date</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if ($recentLoans): foreach($recentLoans as $loan): ?>
                                                        <tr>
                                                            <td><a href="#" class="text-primary"><?= $loan->id ?></a>
                                                            </td>
                                                            <td><?= htmlspecialchars($loan->first_name . ' ' . $loan->last_name) ?>
                                                            </td>
                                                            <td><?= htmlspecialchars($loan->loan_type) ?></td>
                                                            <td>KES <?= number_format($loan->amount_requested, 2) ?>
                                                            </td>
                                                            <td><?= date('M d, Y', strtotime($loan->application_date)) ?>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-<?php
                                                                             switch ($loan->status) {
                                                                                 case 'pending':
                                                                                 case 'under_review':
                                                                                     echo 'warning';
                                                                                     break;
                                                                                 case 'approved':
                                                                                 case 'disbursed':
                                                                                     echo 'success';
                                                                                     break;
                                                                                 case 'rejected':
                                                                                     echo 'danger';
                                                                                     break;
                                                                                 case 'completed':
                                                                                     echo 'info';
                                                                                     break;
                                                                                 default:
                                                                                     echo 'secondary';
                                                                             }
                                                                         ?>-transparent">
                                                                    <?= ucfirst(str_replace('_', ' ', $loan->status)) ?>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; else: ?>
                                                        <tr>
                                                            <td colspan="6" class="text-center py-4">
                                                                <p class="text-muted mb-0">No recent loan applications
                                                                    found</p>
                                                            </td>
                                                        </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Farmer Interactions Card -->
                    <div class="row mt-4">
                        <div class="col-xl-12">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <h6 class="card-title">Farmer Interactions</h6>
                                </div>
                                <div class="card-body">
                                    <?php
                                       // Get farmers this staff has interacted with
                                       $farmersQuery = "SELECT DISTINCT 
                                           f.id as farmer_id,
                                           u.first_name,
                                           u.last_name,
                                           u.phone,
                                           u.email,
                                           f.registration_number,
                                           (SELECT COUNT(*) FROM loan_applications la WHERE la.farmer_id = f.id AND la.reviewed_by = $userId) as loan_count,
                                           (SELECT MAX(la.updated_at) FROM loan_applications la WHERE la.farmer_id = f.id AND la.reviewed_by = $userId) as last_interaction
                                       FROM farmers f
                                       JOIN users u ON f.user_id = u.id
                                       JOIN loan_applications la ON f.id = la.farmer_id
                                       WHERE la.reviewed_by = $userId
                                       ORDER BY last_interaction DESC
                                       LIMIT 10";
                                       
                                       $farmers = $app->select_all($farmersQuery);
                                       
                                       // Get pending requests
                                       $pendingRequestsQuery = "SELECT 
                                           la.id,
                                           la.amount_requested,
                                           la.purpose,
                                           la.application_date,
                                           u.first_name,
                                           u.last_name,
                                           lt.name as loan_type
                                       FROM loan_applications la
                                       JOIN farmers f ON la.farmer_id = f.id
                                       JOIN users u ON f.user_id = u.id
                                       JOIN loan_types lt ON la.loan_type_id = lt.id
                                       WHERE (la.status = 'pending' OR la.status = 'under_review')
                                       AND la.reviewed_by = $userId
                                       ORDER BY la.application_date ASC
                                       LIMIT 5";
                                       
                                       $pendingRequests = $app->select_all($pendingRequestsQuery);
                                       ?>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="mb-3">Recent Farmer Interactions</h6>
                                            <div class="table-responsive">
                                                <table class="table table-hover border">
                                                    <thead>
                                                        <tr>
                                                            <th>Farmer</th>
                                                            <th>Registration #</th>
                                                            <th>Loans</th>
                                                            <th>Last Interaction</th>
                                                            <th>Contact</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if ($farmers): foreach($farmers as $farmer): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($farmer->first_name . ' ' . $farmer->last_name) ?>
                                                            </td>
                                                            <td><?= htmlspecialchars($farmer->registration_number) ?>
                                                            </td>
                                                            <td><?= $farmer->loan_count ?></td>
                                                            <td><?= date('M d, Y', strtotime($farmer->last_interaction)) ?>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex gap-2">
                                                                    <a href="tel:<?= $farmer->phone ?>"
                                                                        class="btn btn-sm btn-outline-primary">
                                                                        <i class="ti ti-phone"></i>
                                                                    </a>
                                                                    <a href="mailto:<?= $farmer->email ?>"
                                                                        class="btn btn-sm btn-outline-info">
                                                                        <i class="ti ti-mail"></i>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; else: ?>
                                                        <tr>
                                                            <td colspan="5" class="text-center py-4">
                                                                <p class="text-muted mb-0">No farmer interactions found
                                                                </p>
                                                            </td>
                                                        </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <h6 class="mb-3">Pending Loan Requests</h6>
                                            <div class="table-responsive">
                                                <table class="table table-hover border">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Farmer</th>
                                                            <th>Loan Type</th>
                                                            <th>Amount</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if ($pendingRequests): foreach($pendingRequests as $request): ?>
                                                        <tr>
                                                            <td><a href="#" class="text-primary"><?= $request->id ?></a>
                                                            </td>
                                                            <td><?= htmlspecialchars($request->first_name . ' ' . $request->last_name) ?>
                                                            </td>
                                                            <td><?= htmlspecialchars($request->loan_type) ?></td>
                                                            <td>KES <?= number_format($request->amount_requested, 2) ?>
                                                            </td>
                                                            <td>
                                                                <a href="http://localhost/dfcs/sacco/loans/review.php?id=<?= $request->id ?>"
                                                                    class="btn btn-sm btn-primary">
                                                                    Review
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; else: ?>
                                                        <tr>
                                                            <td colspan="5" class="text-center py-4">
                                                                <p class="text-muted mb-0">No pending loan requests</p>
                                                            </td>
                                                        </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Audit Trail Card - Fixed Version -->
                    <div class="row mt-4">
                        <div class="col-xl-12">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <h6 class="card-title">Audit Trail</h6>
                                </div>
                                <div class="card-body">
                                    <?php
                                             // Get audit logs
                                             $auditLogsQuery = "SELECT 
                                                 al.id,
                                                 al.action_type,
                                                 al.table_name,
                                                 al.record_id,
                                                 al.created_at,
                                                 al.old_values,
                                                 al.new_values,
                                                 CASE
                                                     WHEN al.table_name = 'users' THEN CONCAT('User ID: ', al.record_id)
                                                     WHEN al.table_name = 'farmers' THEN CONCAT('Farmer ID: ', al.record_id)
                                                     WHEN al.table_name = 'loan_applications' THEN CONCAT('Loan ID: ', al.record_id)
                                                     ELSE CONCAT(al.table_name, ' ID: ', al.record_id)
                                                 END as record_description
                                             FROM audit_logs al
                                             WHERE al.user_id = $userId
                                             ORDER BY al.created_at DESC
                                             LIMIT 10";
                                             
                                             $auditLogs = $app->select_all($auditLogsQuery);
                                             
                                             // Get summary of changes by table
                                             $auditSummaryQuery = "SELECT 
                                                 table_name,
                                                 COUNT(*) as change_count
                                             FROM audit_logs
                                             WHERE user_id = $userId
                                             GROUP BY table_name
                                             ORDER BY change_count DESC";
                                             
                                             $auditSummary = $app->select_all($auditSummaryQuery);
                                             ?>

                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h6 class="mb-3">Changes by Category</h6>
                                            <div class="d-flex flex-wrap gap-3">
                                                <?php if ($auditSummary): foreach($auditSummary as $summary): ?>
                                                <div class="card bg-light p-3 text-center">
                                                    <h3 class="fs-20 fw-semibold mb-0"><?= $summary->change_count ?>
                                                    </h3>
                                                    <p class="mb-0 fs-12">
                                                        <?= ucfirst(str_replace('_', ' ', $summary->table_name)) ?>
                                                        Changes</p>
                                                </div>
                                                <?php endforeach; else: ?>
                                                <p class="text-muted">No audit data available</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <h6 class="mb-3">Recent System Changes</h6>
                                            <div class="table-responsive">
                                                <table class="table table-hover border">
                                                    <thead>
                                                        <tr>
                                                            <th>Date & Time</th>
                                                            <th>Action</th>
                                                            <th>Table</th>
                                                            <th>Record</th>
                                                            <th>Details</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if ($auditLogs): foreach($auditLogs as $log): ?>
                                                        <tr>
                                                            <td><?= date('M d, Y h:i A', strtotime($log->created_at)) ?>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-<?php
                                                switch ($log->action_type) {
                                                    case 'create':
                                                        echo 'success';
                                                        break;
                                                    case 'update':
                                                        echo 'primary';
                                                        break;
                                                    case 'delete':
                                                        echo 'danger';
                                                        break;
                                                    case 'status_change':
                                                        echo 'warning';
                                                        break;
                                                    default:
                                                        echo 'secondary';
                                                }
                                                 ?>-transparent">
                                                                    <i class="fa fa-<?php
                                                    switch ($log->action_type) {
                                                        case 'create':
                                                            echo 'plus';
                                                            break;
                                                        case 'update':
                                                            echo 'edit';
                                                            break;
                                                        case 'delete':
                                                            echo 'trash';
                                                            break;
                                                        case 'status_change':
                                                            echo 'exchange-alt';
                                                            break;
                                                        default:
                                                            echo 'history';
                                                    }
                                                ?> me-1"></i>
                                                                    <?= ucfirst($log->action_type) ?>
                                                                </span>
                                                            </td>
                                                            <td><?= ucfirst(str_replace('_', ' ', $log->table_name)) ?>
                                                            </td>
                                                            <td><?= $log->record_description ?></td>
                                                            <td>
                                                                <button
                                                                    class="btn btn-sm btn-outline-primary view-details"
                                                                    data-bs-toggle="modal" data-bs-target="#auditModal"
                                                                    data-id="<?= $log->id ?>"
                                                                    data-old-values='<?= htmlspecialchars($log->old_values) ?>'
                                                                    data-new-values='<?= htmlspecialchars($log->new_values) ?>'
                                                                    data-table="<?= htmlspecialchars($log->table_name) ?>"
                                                                    data-action="<?= htmlspecialchars($log->action_type) ?>"
                                                                    data-record="<?= htmlspecialchars($log->record_description) ?>"
                                                                    data-date="<?= date('M d, Y h:i A', strtotime($log->created_at)) ?>">
                                                                    View Details
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; else: ?>
                                                        <tr>
                                                            <td colspan="5" class="text-center py-4">
                                                                <i
                                                                    class="fa fa-history fs-40 text-muted d-block mx-auto mb-3"></i>
                                                                <p class="text-muted">No audit trail records found</p>
                                                            </td>
                                                        </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Single Reusable Modal -->
                                    <div class="modal fade" id="auditModal" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Change Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <p><strong>Action:</strong> <span id="modal-action"></span></p>
                                                        <p><strong>Table:</strong> <span id="modal-table"></span></p>
                                                        <p><strong>Record:</strong> <span id="modal-record"></span></p>
                                                        <p><strong>Date:</strong> <span id="modal-date"></span></p>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h6 class="fw-semibold mb-2">Before Changes</h6>
                                                            <div id="old-values-container" class="bg-light p-3 rounded"
                                                                style="max-height: 300px; overflow-y: auto;">
                                                                <!-- Old values will be inserted here -->
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6 class="fw-semibold mb-2">After Changes</h6>
                                                            <div id="new-values-container" class="bg-light p-3 rounded"
                                                                style="max-height: 300px; overflow-y: auto;">
                                                                <!-- New values will be inserted here -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add this JavaScript at the end of your page -->


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
                $('#saccoTabs button[data-bs-target="#work-info"]').tab('show');
            });

            $('#prevWork').click(function(e) {
                e.preventDefault();
                $('#saccoTabs button[data-bs-target="#basic-info"]').tab('show');
            });

            $('#nextWork').click(function(e) {
                e.preventDefault();
                $('#saccoTabs button[data-bs-target="#account-info"]').tab('show');
            });

            $('#prevAccount').click(function(e) {
                e.preventDefault();
                $('#saccoTabs button[data-bs-target="#work-info"]').tab('show');
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
                                $("button[onclick='updateSaccoStaff()']").prop("disabled",
                                    false);
                            } else {
                                $("#email-error").text("Email is already in use");
                                $("#email-success").text("");
                                $("button[onclick='updateSaccoStaff()']").prop("disabled",
                                    true);
                            }
                        }
                    });
                } else {
                    $("#email-error").text("");
                    $("#email-success").text("");
                    $("button[onclick='updateSaccoStaff()']").prop("disabled", false);
                }
            });
        });

        function updateSaccoStaff() {
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
            formData.append('staff_id', $('#staff-number').val());
            formData.append('position', $('#position').val());
            formData.append('department', $('#department').val());

            // User table data
            formData.append('user_id', $('#user-id').val());
            formData.append('first_name', $('#first-name').val());
            formData.append('last_name', $('#last-name').val());
            formData.append('email', $('#email').val());
            formData.append('phone', $('#phone').val());
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
                'Staff ID': $('#staff-number').val(),
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
                url: 'http://localhost/dfcs/ajax/sacco-controller/update-staff.php',
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
                                window.location.href =
                                    'http://localhost/dfcs/sacco/profile/';
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