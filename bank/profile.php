<?php include "../config/config.php" ?>
<?php include "../libs/App.php" ?>
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
    <?php include "../includes/loader.php" ?>

    <div class="page">
        <!-- app-header -->
        <?php include "../includes/navigation.php" ?>
        <!-- /app-header -->
        <!-- Start::app-sidebar -->
        <?php include "../includes/sidebar.php" ?>
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
                                           
                               // Get Bank staff profile info from user_id
                               $query = "SELECT s.id as staff_id, s.position, s.staff_id as employee_number, s.bank_id, s.department,
                                         u.first_name, u.last_name, u.phone, u.email, u.location, u.profile_picture,
                                         b.name as bank_name, b.branch, b.location as bank_location, b.phone as bank_phone
                                         FROM bank_staff s
                                         JOIN users u ON s.user_id = u.id
                                         JOIN banks b ON s.bank_id = b.id
                                         WHERE s.user_id = $userId";
                               $staff = $app->select_one($query);
                                           
                               if (!$staff) {
                                   header("Location: http://localhost/dfcs/"); 
                                   exit();
                                                       }
                        
                                // Get bank-specific stats
                                $statsQuery = "SELECT 
                                    (SELECT COUNT(*) FROM loan_applications la 
                                     WHERE la.bank_id = {$staff->bank_id} AND la.reviewed_by = {$staff->staff_id}) as processed_loans,
                                    
                                    (SELECT COUNT(*) FROM approved_loans al 
                                     JOIN loan_applications la ON al.loan_application_id = la.id
                                     WHERE al.bank_id = {$staff->bank_id} AND la.reviewed_by = {$staff->staff_id} 
                                     AND al.status = 'active') as active_loans,
                                    
                                    (SELECT CONCAT(ROUND(
                                        (COUNT(CASE WHEN la.status = 'approved' OR la.status = 'disbursed' THEN 1 END) * 100.0 / 
                                        NULLIF(COUNT(*), 0)), 0), '%')
                                     FROM loan_applications la
                                     WHERE la.bank_id = {$staff->bank_id} AND la.reviewed_by = {$staff->staff_id}) as approval_rate,
                                    
                                    (SELECT COALESCE(SUM(al.approved_amount), 0)
                                     FROM approved_loans al 
                                     JOIN loan_applications la ON al.loan_application_id = la.id
                                     WHERE al.bank_id = {$staff->bank_id} AND la.reviewed_by = {$staff->staff_id} 
                                     AND al.status = 'active') as portfolio_value";
                                            
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
                                    <i class="ri-bank-line me-1 align-middle"></i>
                                    <?= htmlspecialchars($staff->bank_name . ($staff->branch ? ' - ' . $staff->branch : '')) ?>
                                </span>
                                <span class="me-3">
                                    <i class="ri-user-3-line me-1 align-middle"></i>
                                    Staff ID: <?= htmlspecialchars($staff->employee_number) ?>
                                </span>
                                <?php if ($staff->department): ?>
                                <span class="me-3">
                                    <i class="ri-building-2-line me-1 align-middle"></i>
                                    <?= htmlspecialchars($staff->department) ?>
                                </span>
                                <?php endif; ?>
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
                                        KSh <?= number_format($stats->portfolio_value ?? 0, 0) ?></p>
                                    <p class="mb-0 fs-11 op-5 text-fixed-white">Portfolio Value</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                                 // First get the bank_staff record for the logged-in user
                                 $staffQuery = "SELECT id as staff_id, bank_id 
                                                FROM bank_staff 
                                                WHERE user_id = $userId";
                                 $staffInfo = $app->select_one($staffQuery);
                                 
                                 if (!$staffInfo) {
                                     header("Location: http://localhost/dfcs/");
                                     exit();
                                 }
                                 
                                 $staffId = $staffInfo->staff_id;
                                 $bankId = $staffInfo->bank_id;
                                 
                                 // Get loan statistics
                                 $loanStatsQuery = "SELECT
                                     (SELECT COUNT(*) FROM loan_applications la
                                     WHERE la.bank_id = $bankId AND la.reviewed_by = $staffId) as total_processed,
                                 
                                     (SELECT ROUND(
                                     ((SELECT COUNT(*) FROM loan_applications la
                                     WHERE la.bank_id = $bankId AND la.reviewed_by = $staffId 
                                     AND MONTH(la.updated_at) = MONTH(CURRENT_DATE)
                                     AND YEAR(la.updated_at) = YEAR(CURRENT_DATE)) -
                                     (SELECT COUNT(*) FROM loan_applications la
                                     WHERE la.bank_id = $bankId AND la.reviewed_by = $staffId 
                                     AND MONTH(la.updated_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
                                     AND YEAR(la.updated_at) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH))) /
                                     NULLIF((SELECT COUNT(*) FROM loan_applications la
                                     WHERE la.bank_id = $bankId AND la.reviewed_by = $staffId 
                                     AND MONTH(la.updated_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
                                     AND YEAR(la.updated_at) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)), 0) * 100, 1)
                                     ) as monthly_change,
                                 
                                     (SELECT COUNT(*) FROM loan_applications la
                                     WHERE la.bank_id = $bankId AND la.reviewed_by = $staffId 
                                     AND MONTH(la.updated_at) = MONTH(CURRENT_DATE)
                                     AND YEAR(la.updated_at) = YEAR(CURRENT_DATE)) as current_month_loans,
                                 
                                     (SELECT COUNT(*) FROM loan_applications la
                                     WHERE la.bank_id = $bankId AND la.reviewed_by = $staffId 
                                     AND MONTH(la.updated_at) = MONTH(CURRENT_DATE)
                                     AND YEAR(la.updated_at) = YEAR(CURRENT_DATE) AND la.status = 'approved') as approved_this_month,
                                 
                                     (SELECT COUNT(*) FROM loan_applications la
                                     WHERE la.bank_id = $bankId AND la.reviewed_by = $staffId 
                                     AND MONTH(la.updated_at) = MONTH(CURRENT_DATE)
                                     AND YEAR(la.updated_at) = YEAR(CURRENT_DATE) AND la.status = 'rejected') as rejected_this_month,
                                     
                                     (SELECT CONCAT(ROUND(
                                         (COUNT(CASE WHEN la.status = 'approved' OR la.status = 'disbursed' THEN 1 END) * 100.0 / 
                                         NULLIF(COUNT(*), 0)), 0), '%')
                                      FROM loan_applications la
                                      WHERE la.bank_id = $bankId AND la.reviewed_by = $staffId) as approval_rate,
                                      
                                     (SELECT COALESCE(SUM(al.approved_amount), 0)
                                      FROM approved_loans al 
                                      JOIN loan_applications la ON al.loan_application_id = la.id
                                      WHERE al.bank_id = $bankId AND la.reviewed_by = $staffId 
                                      AND al.status = 'active') as active_portfolio_value";
                                 
                                 $loanStats = $app->select_one($loanStatsQuery);
                                 
                                 // Get bank account balance
                                 $accountQuery = "SELECT 
                                     ac.balance, 
                                     ac.account_number,
                                     b.name as bank_name,
                                     b.branch
                                     FROM bank_branch_accounts ac
                                     JOIN banks b ON ac.bank_id = b.id
                                     WHERE ac.bank_id = $bankId";
                                 $accountInfo = $app->select_one($accountQuery);
                                 ?>
                    <!-- Middle Section Stats Cards -->
                    <div class="row mt-4 mb-4">
                        <!-- Loan Processing Summary Card -->
                        <div class="col-xl-3">
                            <div class="card custom-card bg-primary-transparent">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-lg bg-primary-transparent rounded-circle">
                                            <i class="ti ti-file-dollar fs-20 text-primary"></i>
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
                                                <i
                                                    class="ti ti-trending-<?= ($loanStats->monthly_change ?? 0) >= 0 ? 'up text-success' : 'down text-danger' ?>"></i>
                                                <?= $loanStats->monthly_change ?? 0 ?>%
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
                                            <i class="ti ti-chart-line fs-20 text-success"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h5 class="mb-1 fw-semibold"><?= $loanStats->approval_rate ?? '0%' ?></h5>
                                            <p class="mb-0 text-muted fs-12">Loan Approval Rate</p>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <div class="progress progress-xs progress-animate">
                                            <div class="progress-bar bg-success"
                                                style="width: <?= str_replace('%', '', $loanStats->approval_rate ?? 0) ?>%">
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mt-1">
                                            <span class="fs-12 text-muted">Target: 75%</span>
                                            <span class="fs-12 text-muted">Current:
                                                <?= $loanStats->approval_rate ?? '0%' ?></span>
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
                                            <i class="ti ti-calendar-dollar fs-20 text-warning"></i>
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
                                                    <i class="ti ti-x"></i>
                                                    <?= $loanStats->rejected_this_month ?? 0 ?> Rejected
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Portfolio Value Card -->
                        <div class="col-xl-3">
                            <div class="card custom-card bg-info-transparent">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-lg bg-info-transparent rounded-circle">
                                            <i class="ti ti-wallet fs-20 text-info"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h5 class="mb-1 fw-semibold">KES
                                                <?= number_format($loanStats->active_portfolio_value ?? 0, 2) ?></h5>
                                            <p class="mb-0 text-muted fs-12">Active Portfolio Value</p>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <div class="d-flex align-items-center">
                                            <span class="me-2 fs-12 text-muted">
                                                <?= ($accountInfo->bank_name ?? '') . ($accountInfo->branch ? ' - ' . $accountInfo->branch : '') ?>
                                            </span>
                                        </div>
                                        <?php if ($accountInfo && $accountInfo->account_number): ?>
                                        <div class="mt-1">
                                            <span class="fs-12 text-muted">Account:
                                                <?= $accountInfo->account_number ?></span>
                                        </div>
                                        <?php endif; ?>
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
                $staffIdQuery = "SELECT id as staff_id, bank_id
                                FROM bank_staff 
                                WHERE user_id = $userId";
                $staffIdResult = $app->select_one($staffIdQuery);
                
                if (!$staffIdResult) {
                    echo "<div class='alert alert-danger'>No bank staff record found for this user</div>";
                    exit;
                }
                
                $staffId = $staffIdResult->staff_id;
                $bankId = $staffIdResult->bank_id;
            
                // Fixed the SQL syntax - renamed the table alias from 'as' to 'bst'
                $query = "SELECT 
                        bst.id,
                        bst.staff_id as employee_number,
                        bst.position,
                        bst.bank_id,
                        bst.department,
                        bst.user_id,
                        b.name as bank_name,
                        b.branch,
                        b.location as bank_location,
                        u.first_name,
                        u.last_name,
                        u.email,
                        u.phone,
                        u.username
                      FROM bank_staff bst
                      INNER JOIN users u ON bst.user_id = u.id
                      INNER JOIN banks b ON bst.bank_id = b.id
                      WHERE bst.id = $staffId";
                      
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
                                    <div class="card-title">Update Bank Staff Details</div>
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
                                    <input type="hidden" id="bank-id"
                                        value="<?php echo htmlspecialchars($staff->bank_id); ?>">

                                    <!-- Tabs Navigation -->
                                    <ul class="nav nav-tabs" id="bankTabs" role="tablist">
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
                                                <div class="col-xl-6">
                                                    <label class="form-label">Department</label>
                                                    <select class="form-control" id="department">
                                                        <option value="">Select Department</option>
                                                        <option value="Credit & Loans"
                                                            <?php echo ($staff->department == 'Credit & Loans') ? 'selected' : ''; ?>>
                                                            Credit & Loans</option>
                                                        <option value="Agricultural Finance"
                                                            <?php echo ($staff->department == 'Agricultural Finance') ? 'selected' : ''; ?>>
                                                            Agricultural Finance</option>
                                                        <option value="Risk Management"
                                                            <?php echo ($staff->department == 'Risk Management') ? 'selected' : ''; ?>>
                                                            Risk Management</option>
                                                        <option value="Customer Relations"
                                                            <?php echo ($staff->department == 'Customer Relations') ? 'selected' : ''; ?>>
                                                            Customer Relations</option>
                                                        <option value="Operations"
                                                            <?php echo ($staff->department == 'Operations') ? 'selected' : ''; ?>>
                                                            Operations</option>
                                                        <option value="Compliance"
                                                            <?php echo ($staff->department == 'Compliance') ? 'selected' : ''; ?>>
                                                            Compliance</option>
                                                        <option value="IT & Systems"
                                                            <?php echo ($staff->department == 'IT & Systems') ? 'selected' : ''; ?>>
                                                            IT & Systems</option>
                                                        <option value="Administration"
                                                            <?php echo ($staff->department == 'Administration') ? 'selected' : ''; ?>>
                                                            Administration</option>
                                                    </select>
                                                </div>
                                                <div class="col-xl-6">
                                                    <label class="form-label">Bank</label>
                                                    <input type="text" class="form-control" id="bank-name"
                                                        value="<?php echo htmlspecialchars($staff->bank_name . ($staff->branch ? ' - ' . $staff->branch : '') . ($staff->bank_location ? ' (' . $staff->bank_location . ')' : '')); ?>"
                                                        readonly>
                                                    <small class="text-muted">Bank assignment cannot be changed
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
                                                <button class="btn text-white" onclick="updateBankStaff()"
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
                    <!-- Loan Management Overview Card -->
                    <div class="row mt-4">
                        <div class="col-xl-12">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <h6 class="card-title"><i class="ti ti-building-bank me-2"></i>Loan Management
                                        Overview</h6>
                                </div>
                                <div class="card-body">
                                    <?php
                                    // Get loan statistics for this bank and staff
                                    $loanOverviewQuery = "SELECT 
                                        COUNT(CASE WHEN la.status = 'pending' OR la.status = 'under_review' THEN 1 END) as pending_loans,
                                        COUNT(CASE WHEN la.status = 'approved' THEN 1 END) as approved_loans,
                                        COUNT(CASE WHEN la.status = 'rejected' THEN 1 END) as rejected_loans,
                                        COUNT(CASE WHEN la.status = 'disbursed' THEN 1 END) as disbursed_loans,
                                        COUNT(CASE WHEN la.status = 'completed' THEN 1 END) as completed_loans,
                                        COUNT(CASE WHEN la.status = 'defaulted' THEN 1 END) as defaulted_loans,
                                        ROUND(
                                            (COUNT(CASE WHEN la.status = 'approved' OR la.status = 'disbursed' THEN 1 END) * 100.0 / 
                                            NULLIF(COUNT(*), 0)), 1) as approval_rate,
                                        ROUND(
                                            (COUNT(CASE WHEN la.status = 'rejected' THEN 1 END) * 100.0 / 
                                            NULLIF(COUNT(*), 0)), 1) as rejection_rate,
                                        ROUND(AVG(la.amount_requested), 2) as avg_loan_amount,
                                        SUM(CASE WHEN la.status IN ('approved', 'disbursed', 'completed') THEN la.amount_requested ELSE 0 END) as total_approved_amount,
                                        ROUND(
                                            (COUNT(CASE WHEN la.status = 'defaulted' THEN 1 END) * 100.0 / 
                                            NULLIF(COUNT(CASE WHEN la.status IN ('disbursed', 'completed', 'defaulted') THEN 1 END), 0)), 1) as default_rate
                                    FROM loan_applications la 
                                    WHERE la.bank_id = $bankId 
                                    AND la.reviewed_by = $staffId";
                                    
                                    $loanOverview = $app->select_one($loanOverviewQuery);
                                    
                                    // Get loan type distribution
                                    $loanTypeDistributionQuery = "SELECT 
                                        lt.name as loan_type,
                                        COUNT(*) as application_count,
                                        SUM(la.amount_requested) as total_requested,
                                        AVG(la.amount_requested) as avg_amount,
                                        ROUND(COUNT(*) * 100.0 / NULLIF((
                                            SELECT COUNT(*) 
                                            FROM loan_applications 
                                            WHERE bank_id = $bankId AND reviewed_by = $staffId
                                        ), 0), 1) as application_percentage,
                                        lt.interest_rate,
                                        lt.min_amount,
                                        lt.max_amount
                                    FROM loan_applications la
                                    JOIN loan_types lt ON la.loan_type_id = lt.id
                                    WHERE la.bank_id = $bankId AND la.reviewed_by = $staffId
                                    GROUP BY lt.id, lt.name
                                    ORDER BY total_requested DESC";
                                    
                                    $loanTypeDistribution = $app->select_all($loanTypeDistributionQuery);
                                    
                                    // Get recent loan applications
                                    $recentLoansQuery = "SELECT 
                                        la.id, 
                                        la.amount_requested,
                                        la.term_requested,
                                        la.status,
                                        la.application_date,
                                        la.updated_at,
                                        la.purpose,
                                        lt.name as loan_type,
                                        lt.interest_rate,
                                        u.first_name,
                                        u.last_name,
                                        f.id as farmer_id
                                    FROM loan_applications la
                                    JOIN farmers f ON la.farmer_id = f.id
                                    JOIN users u ON f.user_id = u.id
                                    JOIN loan_types lt ON la.loan_type_id = lt.id
                                    WHERE la.bank_id = $bankId
                                    ORDER BY la.updated_at DESC
                                    LIMIT 5";
                                    
                                    $recentLoans = $app->select_all($recentLoansQuery);
                                    ?>

                                    <div class="row mb-4">
                                        <!-- Loan Status Summary -->
                                        <div class="col-md-6">
                                            <h6 class="mb-3"><i class="ti ti-activity me-1"></i>Loan Status Summary
                                            </h6>
                                            <div class="d-flex flex-wrap gap-3">
                                                <div class="card bg-primary-transparent p-3 text-center">
                                                    <div class="avatar avatar-sm bg-primary mx-auto mb-2">
                                                        <i class="ti ti-hourglass text-white"></i>
                                                    </div>
                                                    <h3 class="fs-20 fw-semibold mb-0">
                                                        <?= $loanOverview->pending_loans ?? 0 ?></h3>
                                                    <p class="mb-0 fs-12">Pending</p>
                                                </div>
                                                <div class="card bg-success-transparent p-3 text-center">
                                                    <div class="avatar avatar-sm bg-success mx-auto mb-2">
                                                        <i class="ti ti-check text-white"></i>
                                                    </div>
                                                    <h3 class="fs-20 fw-semibold mb-0">
                                                        <?= $loanOverview->approved_loans ?? 0 ?></h3>
                                                    <p class="mb-0 fs-12">Approved</p>
                                                </div>
                                                <div class="card bg-danger-transparent p-3 text-center">
                                                    <div class="avatar avatar-sm bg-danger mx-auto mb-2">
                                                        <i class="ti ti-x text-white"></i>
                                                    </div>
                                                    <h3 class="fs-20 fw-semibold mb-0">
                                                        <?= $loanOverview->rejected_loans ?? 0 ?></h3>
                                                    <p class="mb-0 fs-12">Rejected</p>
                                                </div>
                                                <div class="card bg-info-transparent p-3 text-center">
                                                    <div class="avatar avatar-sm bg-info mx-auto mb-2">
                                                        <i class="ti ti-cash text-white"></i>
                                                    </div>
                                                    <h3 class="fs-20 fw-semibold mb-0">
                                                        <?= $loanOverview->disbursed_loans ?? 0 ?></h3>
                                                    <p class="mb-0 fs-12">Disbursed</p>
                                                </div>
                                                <div class="card bg-warning-transparent p-3 text-center">
                                                    <div class="avatar avatar-sm bg-warning mx-auto mb-2">
                                                        <i class="ti ti-alert-triangle text-white"></i>
                                                    </div>
                                                    <h3 class="fs-20 fw-semibold mb-0">
                                                        <?= $loanOverview->defaulted_loans ?? 0 ?></h3>
                                                    <p class="mb-0 fs-12">Defaulted</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Loan Metrics -->
                                        <div class="col-md-6">
                                            <h6 class="mb-3"><i class="ti ti-chart-bar me-1"></i>Performance Metrics
                                            </h6>
                                            <div class="d-flex flex-column gap-3">
                                                <div>
                                                    <p class="mb-1 fw-semibold d-flex justify-content-between">
                                                        <span>Approval Rate</span>
                                                        <span><?= $loanOverview->approval_rate ?? 0 ?>%</span>
                                                    </p>
                                                    <div class="progress progress-sm">
                                                        <div class="progress-bar bg-success" role="progressbar"
                                                            style="width: <?= $loanOverview->approval_rate ?? 0 ?>%"
                                                            aria-valuenow="<?= $loanOverview->approval_rate ?? 0 ?>"
                                                            aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <p class="mb-1 fw-semibold d-flex justify-content-between">
                                                        <span>Default Rate</span>
                                                        <span><?= $loanOverview->default_rate ?? 0 ?>%</span>
                                                    </p>
                                                    <div class="progress progress-sm">
                                                        <div class="progress-bar bg-warning" role="progressbar"
                                                            style="width: <?= $loanOverview->default_rate ?? 0 ?>%"
                                                            aria-valuenow="<?= $loanOverview->default_rate ?? 0 ?>"
                                                            aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between gap-3">
                                                    <div class="card bg-light p-3 flex-grow-1">
                                                        <p class="mb-1 fs-12 text-muted">Avg. Loan Amount</p>
                                                        <h5 class="mb-0 fw-semibold">KES
                                                            <?= number_format($loanOverview->avg_loan_amount ?? 0, 2) ?>
                                                        </h5>
                                                    </div>
                                                    <div class="card bg-light p-3 flex-grow-1">
                                                        <p class="mb-1 fs-12 text-muted">Total Portfolio Value</p>
                                                        <h5 class="mb-0 fw-semibold">KES
                                                            <?= number_format($loanOverview->total_approved_amount ?? 0, 2) ?>
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h6 class="mb-3">
                                                <i class="ti ti-category text-success me-1"></i>Loan Type Distribution
                                                <span class="badge bg-primary-transparent ms-2 fs-12">
                                                    <i class="ti ti-chart-pie me-1"></i>Breakdown by Type
                                                </span>
                                            </h6>
                                            <div class="table-responsive">
                                                <table class="table table-hover border table-striped">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th><i class="ti ti-tag me-1 text-muted"></i>Loan Type</th>
                                                            <th><i
                                                                    class="ti ti-stack-2 me-1 text-muted"></i>Applications
                                                            </th>
                                                            <th><i class="ti ti-cash me-1 text-muted"></i>Total
                                                                Requested (KES)</th>
                                                            <th><i class="ti ti-calculator me-1 text-muted"></i>Avg
                                                                Amount (KES)</th>
                                                            <th><i class="ti ti-percentage me-1 text-muted"></i>Interest
                                                                Rate</th>
                                                            <th><i class="ti ti-chart-bar me-1 text-muted"></i>% of
                                                                Applications</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                       $typeColors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
                                                       $colorIndex = 0;
                                                       
                                                       if ($loanTypeDistribution): foreach($loanTypeDistribution as $item): 
                                                           $color = $typeColors[$colorIndex % count($typeColors)];
                                                           $colorIndex++;
                                                       ?>
                                                        <tr>
                                                            <td class="align-middle">
                                                                <div class="d-flex align-items-center">
                                                                    <span
                                                                        class="avatar avatar-sm bg-<?= $color ?>-transparent me-2">
                                                                        <i
                                                                            class="ti ti-building-bank text-<?= $color ?>"></i>
                                                                    </span>
                                                                    <div>
                                                                        <span
                                                                            class="fw-semibold"><?= htmlspecialchars($item->loan_type) ?></span>
                                                                        <div class="fs-12 text-muted">
                                                                            Range: KES
                                                                            <?= number_format($item->min_amount) ?> -
                                                                            <?= number_format($item->max_amount) ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="align-middle">
                                                                <span class="badge bg-<?= $color ?>-transparent">
                                                                    <?= number_format($item->application_count) ?>
                                                                </span>
                                                            </td>
                                                            <td class="align-middle fw-semibold text-<?= $color ?>">
                                                                KES <?= number_format($item->total_requested, 2) ?>
                                                            </td>
                                                            <td class="align-middle">
                                                                KES <?= number_format($item->avg_amount, 2) ?>
                                                            </td>
                                                            <td class="align-middle">
                                                                <span class="badge bg-info">
                                                                    <?= $item->interest_rate ?>% p.a.
                                                                </span>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <div class="progress progress-xs flex-grow-1">
                                                                        <div class="progress-bar bg-<?= $color ?>"
                                                                            role="progressbar"
                                                                            style="width: <?= $item->application_percentage ?>%">
                                                                        </div>
                                                                    </div>
                                                                    <span
                                                                        class="badge bg-<?= $color ?>"><?= $item->application_percentage ?>%</span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; else: ?>
                                                        <tr>
                                                            <td colspan="6" class="text-center py-5">
                                                                <div class="empty-state">
                                                                    <i
                                                                        class="ti ti-clipboard-text fs-40 text-muted d-block mb-3 mx-auto"></i>
                                                                    <p class="text-muted mb-2">No loan type data
                                                                        available</p>
                                                                    <a href="#" class="btn btn-sm btn-primary">
                                                                        <i class="ti ti-plus me-1"></i>Configure Loan
                                                                        Types
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                    <tfoot class="bg-light">
                                                        <tr>
                                                            <td colspan="6" class="text-end">
                                                                <a href="http://localhost/dfcs/bank/loans/portfolio-report.php"
                                                                    class="btn btn-sm btn-outline-primary">
                                                                    <i class="ti ti-report me-1"></i>View Portfolio
                                                                    Report
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
                                                <i class="ti ti-clipboard-list text-primary me-1"></i>Recent Loan
                                                Applications
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
                                                            <th><i class="ti ti-category me-1 text-muted"></i>Loan Type
                                                            </th>
                                                            <th><i class="ti ti-receipt me-1 text-muted"></i>Amount
                                                                Requested</th>
                                                            <th><i class="ti ti-calendar me-1 text-muted"></i>Term</th>
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
                                                        <?php if ($recentLoans): foreach($recentLoans as $loan): 
                                                           $statusInfo = [
                                                               'pending' => ['color' => 'warning', 'icon' => 'hourglass', 'text' => 'Pending Review'],
                                                               'under_review' => ['color' => 'info', 'icon' => 'eye', 'text' => 'Under Review'],
                                                               'approved' => ['color' => 'success', 'icon' => 'check', 'text' => 'Approved'],
                                                               'rejected' => ['color' => 'danger', 'icon' => 'x', 'text' => 'Rejected'],
                                                               'disbursed' => ['color' => 'primary', 'icon' => 'cash', 'text' => 'Disbursed'],
                                                               'completed' => ['color' => 'success', 'icon' => 'circle-check', 'text' => 'Completed'],
                                                               'defaulted' => ['color' => 'warning', 'icon' => 'alert-triangle', 'text' => 'Defaulted'],
                                                               'cancelled' => ['color' => 'secondary', 'icon' => 'ban', 'text' => 'Cancelled']
                                                           ];
                                                           
                                                           $status = $statusInfo[$loan->status] ?? ['color' => 'secondary', 'icon' => 'info-circle', 'text' => ucfirst($loan->status)];
                                                       ?>
                                                        <tr>
                                                            <td class="align-middle">
                                                                <span
                                                                    class="badge bg-primary rounded-pill">#<?= $loan->id ?></span>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="d-flex align-items-center">
                                                                    <span
                                                                        class="avatar avatar-sm bg-light text-dark me-2">
                                                                        <?= strtoupper(substr($loan->first_name, 0, 1) . substr($loan->last_name, 0, 1)) ?>
                                                                    </span>
                                                                    <div>
                                                                        <span
                                                                            class="fw-semibold"><?= htmlspecialchars($loan->first_name . ' ' . $loan->last_name) ?></span>
                                                                        <div class="fs-12 text-muted">Farmer ID:
                                                                            FRM<?= str_pad($loan->farmer_id ?? 0, 6, '0', STR_PAD_LEFT) ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div>
                                                                    <span
                                                                        class="fw-semibold"><?= htmlspecialchars($loan->loan_type) ?></span>
                                                                    <div class="fs-12 text-muted">
                                                                        <?= $loan->interest_rate ?>% p.a.</div>
                                                                </div>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="text-nowrap">
                                                                    <span class="fs-14 fw-semibold">KES
                                                                        <?= number_format($loan->amount_requested, 2) ?></span>
                                                                    <div class="fs-12 text-muted">Requested amount</div>
                                                                </div>
                                                            </td>
                                                            <td class="align-middle">
                                                                <span
                                                                    class="badge bg-info-transparent"><?= $loan->term_requested ?>
                                                                    months</span>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="d-flex align-items-center">
                                                                    <span
                                                                        class="avatar avatar-xs bg-light text-dark me-2">
                                                                        <i class="ti ti-calendar-event"></i>
                                                                    </span>
                                                                    <div>
                                                                        <span
                                                                            class="fs-14"><?= date('M d, Y', strtotime($loan->application_date)) ?></span>
                                                                        <div class="fs-12 text-muted">
                                                                            <?= date('h:i A', strtotime($loan->application_date)) ?>
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
                                                                    <a href="http://localhost/dfcs/bank/loan/review.php?id=<?= $loan->id ?>"
                                                                        class="btn btn-sm btn-primary">
                                                                        <i class="ti ti-eye me-1"></i> View
                                                                    </a>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split"
                                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                                        <span class="visually-hidden">More</span>
                                                                    </button>
                                                                    <ul class="dropdown-menu">
                                                                        <?php if ($loan->status == 'pending' || $loan->status == 'under_review'): ?>
                                                                        <li><a class="dropdown-item" href="#"><i
                                                                                    class="ti ti-check me-1 text-success"></i>
                                                                                Approve</a></li>
                                                                        <li><a class="dropdown-item" href="#"><i
                                                                                    class="ti ti-x me-1 text-danger"></i>
                                                                                Reject</a></li>
                                                                        <?php elseif ($loan->status == 'approved'): ?>
                                                                        <li><a class="dropdown-item" href="#"><i
                                                                                    class="ti ti-cash me-1 text-primary"></i>
                                                                                Disburse</a></li>
                                                                        <?php endif; ?>
                                                                        <li><a class="dropdown-item" href="#"><i
                                                                                    class="ti ti-printer me-1"></i>
                                                                                Print Details</a></li>
                                                                        <li><a class="dropdown-item" href="#"><i
                                                                                    class="ti ti-history me-1"></i> View
                                                                                History</a></li>
                                                                    </ul>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; else: ?>
                                                        <tr>
                                                            <td colspan="8" class="text-center py-5">
                                                                <div class="empty-state">
                                                                    <i
                                                                        class="ti ti-file-off fs-40 text-muted d-block mb-3 mx-auto"></i>
                                                                    <p class="text-muted mb-2">No recent loan
                                                                        applications found</p>
                                                                    <a href="http://localhost/dfcs/bank/loan/new.php"
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
                                                            <td colspan="8" class="text-end">
                                                                <a href="http://localhost/dfcs/bank/loan/applications.php"
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

                    <!-- Farmer Loan Selection Insights -->
                    <div class="row mt-4">
                        <div class="col-xl-12">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <h6 class="card-title"><i class="ti ti-coins me-2"></i>Farmer Loan
                                        Selection Insights</h6>
                                </div>
                                <div class="card-body">
                                    <?php
                                            // Get popular loan types selected by farmers for this bank
                                            $popularLoanTypesQuery = "SELECT 
                                                lt.id,
                                                lt.name,
                                                lt.interest_rate,
                                                lt.min_amount,
                                                lt.max_amount,
                                                lt.min_term,
                                                lt.max_term,
                                                lt.processing_fee,
                                                COUNT(la.id) as application_count,
                                                SUM(la.amount_requested) as total_requested,
                                                AVG(la.amount_requested) as avg_amount_requested,
                                                AVG(la.term_requested) as avg_term_requested,
                                                COUNT(DISTINCT la.farmer_id) as unique_farmers,
                                                COUNT(CASE WHEN la.status IN ('approved', 'disbursed', 'completed') THEN 1 END) as approved_count,
                                                ROUND((COUNT(CASE WHEN la.status IN ('approved', 'disbursed', 'completed') THEN 1 END) * 100.0 / COUNT(la.id)), 1) as approval_rate
                                            FROM loan_applications la
                                            JOIN loan_types lt ON la.loan_type_id = lt.id
                                            WHERE la.bank_id = $bankId
                                            GROUP BY lt.id, lt.name, lt.interest_rate, lt.min_amount, lt.max_amount, lt.min_term, lt.max_term, lt.processing_fee
                                            ORDER BY application_count DESC
                                            LIMIT 8";
                                            
                                            $popularLoanTypes = $app->select_all($popularLoanTypesQuery);
                                            
                                            // Get farmer loan application patterns
                                            $loanPatternsQuery = "SELECT 
                                                COUNT(DISTINCT la.id) as total_applications,
                                                AVG(la.amount_requested) as avg_loan_amount,
                                                AVG(la.term_requested) as avg_term_requested,
                                                SUM(CASE WHEN la.amount_requested < 50000 THEN 1 ELSE 0 END) as small_loans,
                                                SUM(CASE WHEN la.amount_requested BETWEEN 50000 AND 200000 THEN 1 ELSE 0 END) as medium_loans,
                                                SUM(CASE WHEN la.amount_requested > 200000 THEN 1 ELSE 0 END) as large_loans,
                                                COUNT(DISTINCT la.farmer_id) as unique_farmers,
                                                COUNT(CASE WHEN la.status IN ('approved', 'disbursed', 'completed') THEN 1 END) as total_approved,
                                                COUNT(CASE WHEN la.status = 'rejected' THEN 1 END) as total_rejected,
                                                COUNT(CASE WHEN la.status = 'defaulted' THEN 1 END) as total_defaulted
                                            FROM loan_applications la
                                            WHERE la.bank_id = $bankId";
                                            
                                            $loanPatterns = $app->select_one($loanPatternsQuery);
                                            
                                            // Get common loan purposes/categories
                                            $purposeAnalysisQuery = "SELECT 
                                                CASE 
                                                    WHEN LOWER(la.purpose) LIKE '%seed%' OR LOWER(la.purpose) LIKE '%planting%' THEN 'Seeds & Planting'
                                                    WHEN LOWER(la.purpose) LIKE '%fertilizer%' OR LOWER(la.purpose) LIKE '%fertiliser%' THEN 'Fertilizers'
                                                    WHEN LOWER(la.purpose) LIKE '%equipment%' OR LOWER(la.purpose) LIKE '%machinery%' OR LOWER(la.purpose) LIKE '%tool%' THEN 'Equipment & Tools'
                                                    WHEN LOWER(la.purpose) LIKE '%harvest%' OR LOWER(la.purpose) LIKE '%processing%' THEN 'Harvest & Processing'
                                                    WHEN LOWER(la.purpose) LIKE '%livestock%' OR LOWER(la.purpose) LIKE '%cattle%' OR LOWER(la.purpose) LIKE '%chicken%' THEN 'Livestock'
                                                    WHEN LOWER(la.purpose) LIKE '%land%' OR LOWER(la.purpose) LIKE '%expansion%' THEN 'Land & Expansion'
                                                    ELSE 'General Agriculture'
                                                END as purpose_category,
                                                COUNT(*) as application_count,
                                                AVG(la.amount_requested) as avg_amount,
                                                SUM(la.amount_requested) as total_amount
                                            FROM loan_applications la
                                            WHERE la.bank_id = $bankId
                                            GROUP BY purpose_category
                                            ORDER BY application_count DESC
                                            LIMIT 5";
                                            
                                            $purposeAnalysis = $app->select_all($purposeAnalysisQuery);
                                            ?>

                                    <div class="row mb-4">
                                        <!-- Farmer Loan Patterns -->
                                        <div class="col-md-4">
                                            <h6 class="mb-3"><i class="ti ti-chart-bar me-1"></i>Loan Application
                                                Patterns</h6>
                                            <div class="card border p-3">
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="fs-12 text-muted">Total Applications</span>
                                                        <span class="badge bg-primary-transparent">
                                                            <?= number_format($loanPatterns->total_applications ?? 0) ?>
                                                        </span>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="fs-12 text-muted">Unique Farmers</span>
                                                        <span class="badge bg-info-transparent">
                                                            <?= number_format($loanPatterns->unique_farmers ?? 0) ?>
                                                        </span>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="fs-12 text-muted">Avg. Loan Amount</span>
                                                        <span class="badge bg-success-transparent">
                                                            KES
                                                            <?= number_format($loanPatterns->avg_loan_amount ?? 0, 0) ?>
                                                        </span>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="fs-12 text-muted">Avg. Term</span>
                                                        <span class="fw-semibold">
                                                            <?= number_format($loanPatterns->avg_term_requested ?? 0, 1) ?>
                                                            months</span>
                                                    </div>
                                                </div>

                                                <h6 class="mb-2 fs-12 text-muted">Loan Size Distribution</h6>
                                                <div class="d-flex gap-2 align-items-center mb-3">
                                                    <div class="progress flex-grow-1 progress-sm">
                                                        <?php
                                        $totalLoans = ($loanPatterns->small_loans ?? 0) + 
                                                      ($loanPatterns->medium_loans ?? 0) + 
                                                      ($loanPatterns->large_loans ?? 0);
                                        $smallPercent = $totalLoans > 0 ? ($loanPatterns->small_loans / $totalLoans) * 100 : 0;
                                        $mediumPercent = $totalLoans > 0 ? ($loanPatterns->medium_loans / $totalLoans) * 100 : 0;
                                        $largePercent = $totalLoans > 0 ? ($loanPatterns->large_loans / $totalLoans) * 100 : 0;
                                        ?>
                                                        <div class="progress-bar bg-info" role="progressbar"
                                                            style="width: <?= $smallPercent ?>%"
                                                            title="Small Loans: <?= number_format($loanPatterns->small_loans ?? 0) ?>">
                                                        </div>
                                                        <div class="progress-bar bg-primary" role="progressbar"
                                                            style="width: <?= $mediumPercent ?>%"
                                                            title="Medium Loans: <?= number_format($loanPatterns->medium_loans ?? 0) ?>">
                                                        </div>
                                                        <div class="progress-bar bg-success" role="progressbar"
                                                            style="width: <?= $largePercent ?>%"
                                                            title="Large Loans: <?= number_format($loanPatterns->large_loans ?? 0) ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between fs-12">
                                                    <span><i class="ti ti-circle-filled text-info me-1"></i>Small (
                                                        <50K)< /span>
                                                            <span><i
                                                                    class="ti ti-circle-filled text-primary me-1"></i>Medium
                                                                (50K-200K)</span>
                                                            <span><i
                                                                    class="ti ti-circle-filled text-success me-1"></i>Large
                                                                (>200K)</span>
                                                </div>
                                            </div>

                                            <h6 class="mb-3 mt-4"><i class="ti ti-target me-1"></i>Loan Purposes</h6>
                                            <div class="card border p-0">
                                                <ul class="list-group list-group-flush">
                                                    <?php if ($purposeAnalysis): foreach($purposeAnalysis as $purpose): ?>
                                                    <li
                                                        class="list-group-item d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <span
                                                                class="fw-semibold"><?= htmlspecialchars($purpose->purpose_category) ?></span>
                                                            <div class="fs-12 text-muted">Avg: KES
                                                                <?= number_format($purpose->avg_amount, 0) ?></div>
                                                        </div>
                                                        <span
                                                            class="badge bg-primary rounded-pill"><?= $purpose->application_count ?></span>
                                                    </li>
                                                    <?php endforeach; else: ?>
                                                    <li class="list-group-item text-center py-3">
                                                        <p class="text-muted mb-0">No loan purpose data found</p>
                                                    </li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        </div>

                                        <!-- Popular Loan Types Visualization -->
                                        <div class="col-md-8">
                                            <h6 class="mb-3"><i class="ti ti-star me-1"></i>Most Popular Loan Types</h6>
                                            <div class="row g-3">
                                                <?php 
                                $loanTypeColors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary', 'dark', 'purple'];
                                $colorIndex = 0;
                                
                                if ($popularLoanTypes): foreach($popularLoanTypes as $loanType): 
                                    $color = $loanTypeColors[$colorIndex % count($loanTypeColors)];
                                    $colorIndex++;
                                ?>
                                                <div class="col-md-6">
                                                    <div class="card border h-100">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex align-items-center mb-3">
                                                                <span
                                                                    class="avatar avatar-sm bg-<?= $color ?>-transparent me-2">
                                                                    <i class="ti ti-building-bank"></i>
                                                                </span>
                                                                <div>
                                                                    <div class="fw-semibold text-truncate"
                                                                        style="max-width: 200px;"
                                                                        title="<?= htmlspecialchars($loanType->name) ?>">
                                                                        <?= htmlspecialchars($loanType->name) ?>
                                                                    </div>
                                                                    <div class="fs-12 text-muted">
                                                                        <?= $loanType->interest_rate ?>% p.a. Interest
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row g-2 fs-12 mb-3">
                                                                <div class="col-6">
                                                                    <div class="card bg-light p-2">
                                                                        <div class="text-muted">Applications</div>
                                                                        <div class="fw-semibold">
                                                                            <?= number_format($loanType->application_count) ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="card bg-light p-2">
                                                                        <div class="text-muted">Approval Rate</div>
                                                                        <div class="fw-semibold">
                                                                            <?= $loanType->approval_rate ?>%</div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="card bg-light p-2">
                                                                        <div class="text-muted">Avg. Amount</div>
                                                                        <div class="fw-semibold">KES
                                                                            <?= number_format($loanType->avg_amount_requested, 0) ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="card bg-light p-2">
                                                                        <div class="text-muted">Avg. Term</div>
                                                                        <div class="fw-semibold">
                                                                            <?= number_format($loanType->avg_term_requested, 1) ?>
                                                                            months</div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="mb-2">
                                                                <div
                                                                    class="d-flex justify-content-between fs-12 text-muted mb-1">
                                                                    <span>Loan Range</span>
                                                                    <span>Processing Fee</span>
                                                                </div>
                                                                <div class="d-flex justify-content-between fs-12">
                                                                    <span class="fw-semibold">KES
                                                                        <?= number_format($loanType->min_amount) ?> -
                                                                        <?= number_format($loanType->max_amount) ?></span>
                                                                    <span
                                                                        class="fw-semibold"><?= $loanType->processing_fee ?>%</span>
                                                                </div>
                                                            </div>

                                                            <div
                                                                class="d-flex justify-content-between align-items-center small">
                                                                <span class="text-muted">Unique Farmers</span>
                                                                <span
                                                                    class="badge bg-<?= $color ?>"><?= $loanType->unique_farmers ?>
                                                                    farmers</span>
                                                            </div>
                                                            <div class="progress progress-sm mt-1">
                                                                <div class="progress-bar bg-<?= $color ?>"
                                                                    role="progressbar"
                                                                    style="width: <?= min(($loanType->application_count / ($popularLoanTypes[0]->application_count ?? 1)) * 100, 100) ?>%">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endforeach; else: ?>
                                                <div class="col-12">
                                                    <div class="card border p-4 text-center">
                                                        <i
                                                            class="ti ti-coins-off fs-40 text-muted d-block mb-3 mx-auto"></i>
                                                        <p class="text-muted mb-2">No loan application data available
                                                        </p>
                                                    </div>
                                                </div>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Performance Summary -->
                                            <?php if ($loanPatterns->total_applications > 0): ?>
                                            <div class="row mt-4">
                                                <div class="col-12">
                                                    <div class="card bg-light border-0 p-3">
                                                        <div class="row text-center">
                                                            <div class="col-3">
                                                                <div class="fs-20 fw-bold text-success">
                                                                    <?= number_format($loanPatterns->total_approved ?? 0) ?>
                                                                </div>
                                                                <div class="fs-12 text-muted">Approved</div>
                                                            </div>
                                                            <div class="col-3">
                                                                <div class="fs-20 fw-bold text-danger">
                                                                    <?= number_format($loanPatterns->total_rejected ?? 0) ?>
                                                                </div>
                                                                <div class="fs-12 text-muted">Rejected</div>
                                                            </div>
                                                            <div class="col-3">
                                                                <div class="fs-20 fw-bold text-warning">
                                                                    <?= number_format($loanPatterns->total_defaulted ?? 0) ?>
                                                                </div>
                                                                <div class="fs-12 text-muted">Defaulted</div>
                                                            </div>
                                                            <div class="col-3">
                                                                <div class="fs-20 fw-bold text-primary">
                                                                    <?= $loanPatterns->total_applications > 0 ? round(($loanPatterns->total_approved / $loanPatterns->total_applications) * 100, 1) : 0 ?>%
                                                                </div>
                                                                <div class="fs-12 text-muted">Success Rate</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end mt-2">
                                        <a href="http://localhost/dfcs/bank/reports/loan-analysis.php"
                                            class="btn btn-primary">
                                            <i class="ti ti-report-analytics me-1"></i>View Full Loan Analysis
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
            <?php include "../includes/footer.php" ?>
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
            // Tab navigation - Updated for bank staff tabs
            $('#nextBasic').click(function(e) {
                e.preventDefault();
                $('#bankTabs button[data-bs-target="#work-info"]').tab('show');
            });

            $('#prevWork').click(function(e) {
                e.preventDefault();
                $('#bankTabs button[data-bs-target="#basic-info"]').tab('show');
            });

            $('#nextWork').click(function(e) {
                e.preventDefault();
                $('#bankTabs button[data-bs-target="#account-info"]').tab('show');
            });

            $('#prevAccount').click(function(e) {
                e.preventDefault();
                $('#bankTabs button[data-bs-target="#work-info"]').tab('show');
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
                                $("button[onclick='updateBankStaff()']").prop("disabled",
                                    false);
                            } else {
                                $("#email-error").text("Email is already in use");
                                $("#email-success").text("");
                                $("button[onclick='updateBankStaff()']").prop("disabled",
                                    true);
                            }
                        }
                    });
                } else {
                    $("#email-error").text("");
                    $("#email-success").text("");
                    $("button[onclick='updateBankStaff()']").prop("disabled", false);
                }
            });

            // Username availability checking - only check if username has changed
            $("#username").on("input", function() {
                const username = $(this).val();
                const originalUsername = $("#original-username").val();

                if (username && username !== originalUsername) {
                    $.ajax({
                        type: "POST",
                        url: "http://localhost/dfcs/ajax/authentication-controller/check-username-availability.php",
                        data: {
                            username: username
                        },
                        success: function(response) {
                            if (response === "available") {
                                $("#username-success").text("Username is available");
                                $("#username-error").text("");
                                $("button[onclick='updateBankStaff()']").prop("disabled",
                                    false);
                            } else {
                                $("#username-error").text("Username is already taken");
                                $("#username-success").text("");
                                $("button[onclick='updateBankStaff()']").prop("disabled",
                                    true);
                            }
                        }
                    });
                } else {
                    $("#username-error").text("");
                    $("#username-success").text("");
                    $("button[onclick='updateBankStaff()']").prop("disabled", false);
                }
            });
        });

        function updateBankStaff() {
            // Validate passwords if provided
            if ($('#password').val() || $('#confirm-password').val()) {
                if ($('#password').val() !== $('#confirm-password').val()) {
                    toastr.error('Passwords do not match', 'Error');
                    return;
                }

                // Password strength validation
                const password = $('#password').val();
                if (password.length < 8) {
                    toastr.error('Password must be at least 8 characters long', 'Error');
                    return;
                }
            }

            // Get form values
            const formData = new FormData();

            // Bank staff table data (matching PHP expectations)
            formData.append('id', $('#staff-id').val()); // bank_staff.id
            formData.append('staff_id', $('#employee-number').val()); // bank_staff.staff_id
            formData.append('position', $('#position').val()); // bank_staff.position
            formData.append('department', $('#department').val()); // bank_staff.department

            // User table data (matching PHP expectations)
            formData.append('first_name', $('#first-name').val()); // users.first_name
            formData.append('last_name', $('#last-name').val()); // users.last_name
            formData.append('email', $('#email').val()); // users.email
            formData.append('phone', $('#phone').val()); // users.phone
            formData.append('username', $('#username').val()); // users.username

            // Only append password if it's being changed
            if ($('#password').val()) {
                formData.append('password', $('#password').val()); // users.password
            }

            // Validate required fields
            const requiredFields = {
                'First Name': $('#first-name').val(),
                'Last Name': $('#last-name').val(),
                'Email': $('#email').val(),
                'Staff ID': $('#employee-number').val(),
                'Position': $('#position').val(),
                'Username': $('#username').val()
            };

            for (const [field, value] of Object.entries(requiredFields)) {
                if (!value) {
                    toastr.error(`${field} is required`, 'Error');
                    return;
                }
            }

            // Validate email format
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test($('#email').val())) {
                toastr.error('Please enter a valid email address', 'Error');
                return;
            }

            // Validate phone format (optional but if provided should be valid)
            const phone = $('#phone').val();
            if (phone && !/^[\d\s\-\+\(\)]+$/.test(phone)) {
                toastr.error('Please enter a valid phone number', 'Error');
                return;
            }

            // Show loading indicator
            const updateButton = $("button[onclick='updateBankStaff()']");
            const originalText = updateButton.html();
            updateButton.html('<i class="ti ti-loader ti-spin me-1"></i>Updating...').prop('disabled', true);

            // Send update request
            $.ajax({
                url: 'http://localhost/dfcs/ajax/bank-controller/update-staff.php',
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

                            // Clear password fields on successful update
                            $('#password').val('');
                            $('#confirm-password').val('');

                            setTimeout(() => {
                                window.location.href = 'http://localhost/dfcs/bank/profile';
                            }, 2000);
                        } else {
                            toastr.error(data.message, 'Error');
                            updateButton.html(originalText).prop('disabled', false);
                        }
                    } catch (e) {
                        toastr.error('Error processing response', 'Error');
                        updateButton.html(originalText).prop('disabled', false);
                        console.error('Parse error:', e);
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('Server error occurred: ' + error, 'Error');
                    updateButton.html(originalText).prop('disabled', false);
                    console.error('AJAX error:', xhr.responseText);
                }
            });
        }

        // Additional bank-specific functions
        function validateStaffId() {
            const staffId = $('#employee-number').val();
            if (staffId && !/^[A-Z0-9]{3,20}$/.test(staffId)) {
                toastr.warning('Staff ID should contain only uppercase letters and numbers (3-20 characters)',
                    'Validation');
            }
        }

        function formatStaffId() {
            const input = $('#employee-number');
            let value = input.val().toUpperCase().replace(/[^A-Z0-9]/g, '');
            input.val(value);
        }

        // Initialize additional validations
        $(document).ready(function() {
            // Staff ID formatting
            $('#employee-number').on('input', function() {
                formatStaffId();
                validateStaffId();
            });

            // Department selection validation
            $('#department').on('change', function() {
                if (!this.value) {
                    toastr.info('Please select a department for better organization', 'Info');
                }
            });

            // Add tooltips for better UX
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
        </script>
</body>

</html>