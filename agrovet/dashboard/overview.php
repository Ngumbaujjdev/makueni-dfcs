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

        <!-- Start::app-content -->
        <div class="main-content app-content">
            <div class="container-fluid">
                <!-- Start::page-header -->

                <!-- Start::page-header -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                    <div>
                        <?php
                          $app = new App;
                          if (isset($_SESSION['role_id'])) {
                              $query = "SELECT * FROM users WHERE id=" . $_SESSION['user_id'];
                              $user = $app->select_one($query);
                              
                              // Get agrovet details if user is agrovet staff
                              if ($_SESSION['role_id'] == 4) {
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
                              } else if ($_SESSION['role_id'] == 2) {
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
                                    <!-- Total Input Credits Card -->
                                    <div class="col-xxl-3 col-lg-6 col-md-6">
                                        <div class="card custom-card overflow-hidden">
                                            <div class="card-body">
                                                <div class="d-flex align-items-top justify-content-between">
                                                    <div>
                                                        <span class="avatar avatar-md avatar-rounded bg-primary">
                                                            <i class="ti ti-credit-card fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill ms-3">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between flex-wrap">
                                                            <div>
                                                                <p class="text-muted mb-0">Total Input Credits</p>
                                                                <h4 class="fw-semibold mt-1">
                                                                    <?php 
                                                                     // Get agrovet_id for the current staff
                                                                     $staffQuery = "SELECT s.agrovet_id 
                                                                                   FROM agrovet_staff s 
                                                                                   WHERE s.user_id = {$_SESSION['user_id']}";
                                                                     $staffResult = $app->select_one($staffQuery);
                                                                     $agrovetId = $staffResult->agrovet_id ?? 0;
                                                                     
                                                                     $totalCredits = $app->select_one("SELECT COUNT(*) as count 
                                                                                                     FROM input_credit_applications 
                                                                                                     WHERE agrovet_id = $agrovetId");
                                                                     echo number_format($totalCredits->count); 
                                                                 ?>
                                                                </h4>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="d-flex align-items-center justify-content-between mt-1">
                                                            <div>
                                                                <a class="text-primary" href="input-credits">View All<i
                                                                        class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                                            </div>
                                                            <div class="text-end">
                                                                <p class="mb-0 text-success fw-semibold">Processed</p>
                                                                <span class="text-muted op-7 fs-11">credits</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Active Credits Card -->
                                    <div class="col-xxl-3 col-lg-6 col-md-6">
                                        <div class="card custom-card overflow-hidden">
                                            <div class="card-body">
                                                <div class="d-flex align-items-top justify-content-between">
                                                    <div>
                                                        <span class="avatar avatar-md avatar-rounded bg-success">
                                                            <i class="ti ti-check-circle fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill ms-3">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between flex-wrap">
                                                            <div>
                                                                <p class="text-muted mb-0">Active Credits</p>
                                                                <h4 class="fw-semibold mt-1">
                                                                    <?php 
                                                                    $activeCredits = $app->select_one("SELECT COUNT(*) as count 
                                                                                                     FROM approved_input_credits aic
                                                                                                     JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                                                                                     WHERE ica.agrovet_id = $agrovetId 
                                                                                                     AND aic.status = 'active'");
                                                                    echo number_format($activeCredits->count); 
                                                                ?>
                                                                </h4>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="d-flex align-items-center justify-content-between mt-1">
                                                            <div>
                                                                <a class="text-success" href="active-credits">View All<i
                                                                        class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                                            </div>
                                                            <div class="text-end">
                                                                <p class="mb-0 text-success fw-semibold">Outstanding</p>
                                                                <span class="text-muted op-7 fs-11">credits</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pending Applications Card -->
                                    <div class="col-xxl-3 col-lg-6 col-md-6">
                                        <div class="card custom-card overflow-hidden">
                                            <div class="card-body">
                                                <div class="d-flex align-items-top justify-content-between">
                                                    <div>
                                                        <span class="avatar avatar-md avatar-rounded bg-warning">
                                                            <i class="ti ti-clock fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill ms-3">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between flex-wrap">
                                                            <div>
                                                                <p class="text-muted mb-0">Pending Applications</p>
                                                                <h4 class="fw-semibold mt-1">
                                                                    <?php 
                                                                        $pendingApps = $app->select_one("SELECT COUNT(*) as count 
                                                                                                       FROM input_credit_applications 
                                                                                                       WHERE agrovet_id = $agrovetId 
                                                                                                       AND status IN ('pending', 'under_review')");
                                                                        echo number_format($pendingApps->count); 
                                                                    ?>
                                                                </h4>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="d-flex align-items-center justify-content-between mt-1">
                                                            <div>
                                                                <a class="text-warning" href="pending-applications">View
                                                                    All<i
                                                                        class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                                            </div>
                                                            <div class="text-end">
                                                                <p class="mb-0 text-warning fw-semibold">Waiting</p>
                                                                <span class="text-muted op-7 fs-11">for review</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Total Disbursed Amount Card -->
                                    <div class="col-xxl-3 col-lg-6 col-md-6">
                                        <div class="card custom-card overflow-hidden">
                                            <div class="card-body">
                                                <div class="d-flex align-items-top justify-content-between">
                                                    <div>
                                                        <span class="avatar avatar-md avatar-rounded bg-info">
                                                            <i class="ti ti-cash fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill ms-3">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between flex-wrap">
                                                            <div>
                                                                <p class="text-muted mb-0">Total Disbursed</p>
                                                                <h4 class="fw-semibold mt-1">
                                                                    <?php 
                                                                       $totalDisbursed = $app->select_one("SELECT SUM(aic.approved_amount) as amount 
                                                                                                         FROM approved_input_credits aic
                                                                                                         JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                                                                                         WHERE ica.agrovet_id = $agrovetId");
                                                                       echo 'KES ' . number_format($totalDisbursed->amount ?? 0, 0); 
                                                                   ?>
                                                                </h4>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="d-flex align-items-center justify-content-between mt-1">
                                                            <div>
                                                                <a class="text-info" href="disbursement-report">View
                                                                    Report<i
                                                                        class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                                            </div>
                                                            <div class="text-end">
                                                                <p class="mb-0 text-info fw-semibold">Amount</p>
                                                                <span class="text-muted op-7 fs-11">disbursed</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Outstanding Balance Card -->
                                    <div class="col-xxl-3 col-lg-6 col-md-6">
                                        <div class="card custom-card overflow-hidden">
                                            <div class="card-body">
                                                <div class="d-flex align-items-top justify-content-between">
                                                    <div>
                                                        <span class="avatar avatar-md avatar-rounded bg-danger">
                                                            <i class="ti ti-currency-dollar fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill ms-3">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between flex-wrap">
                                                            <div>
                                                                <p class="text-muted mb-0">Outstanding Balance</p>
                                                                <h4 class="fw-semibold mt-1">
                                                                    <?php 
                                                                     $outstandingBalance = $app->select_one("SELECT SUM(aic.remaining_balance) as amount 
                                                                                                           FROM approved_input_credits aic
                                                                                                           JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                                                                                           WHERE ica.agrovet_id = $agrovetId 
                                                                                                           AND aic.status = 'active'");
                                                                     echo 'KES ' . number_format($outstandingBalance->amount ?? 0, 0); 
                                                                 ?>
                                                                </h4>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="d-flex align-items-center justify-content-between mt-1">
                                                            <div>
                                                                <a class="text-danger" href="outstanding-balances">View
                                                                    All<i
                                                                        class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                                            </div>
                                                            <div class="text-end">
                                                                <p class="mb-0 text-danger fw-semibold">Amount</p>
                                                                <span class="text-muted op-7 fs-11">to be
                                                                    collected</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Completed Credits Card -->
                                    <div class="col-xxl-3 col-lg-6 col-md-6">
                                        <div class="card custom-card overflow-hidden">
                                            <div class="card-body">
                                                <div class="d-flex align-items-top justify-content-between">
                                                    <div>
                                                        <span class="avatar avatar-md avatar-rounded bg-success">
                                                            <i class="ti ti-circle-check fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill ms-3">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between flex-wrap">
                                                            <div>
                                                                <p class="text-muted mb-0">Completed Credits</p>
                                                                <h4 class="fw-semibold mt-1">
                                                                    <?php 
                                                                    $completedCredits = $app->select_one("SELECT COUNT(*) as count 
                                                                                                        FROM approved_input_credits aic
                                                                                                        JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                                                                                        WHERE ica.agrovet_id = $agrovetId 
                                                                                                        AND aic.status = 'completed'");
                                                                    echo number_format($completedCredits->count); 
                                                                ?>
                                                                </h4>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="d-flex align-items-center justify-content-between mt-1">
                                                            <div>
                                                                <a class="text-success" href="completed-credits">View
                                                                    All<i
                                                                        class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                                            </div>
                                                            <div class="text-end">
                                                                <p class="mb-0 text-success fw-semibold">Fully</p>
                                                                <span class="text-muted op-7 fs-11">repaid</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Default Rate Card -->
                                    <div class="col-xxl-3 col-lg-6 col-md-6">
                                        <div class="card custom-card overflow-hidden">
                                            <div class="card-body">
                                                <div class="d-flex align-items-top justify-content-between">
                                                    <div>
                                                        <span class="avatar avatar-md avatar-rounded bg-purple">
                                                            <i class="ti ti-chart-pie fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill ms-3">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between flex-wrap">
                                                            <div>
                                                                <p class="text-muted mb-0">Default Rate</p>
                                                                <h4 class="fw-semibold mt-1">
                                                                    <?php 
                                                                      $defaultedCredits = $app->select_one("SELECT COUNT(*) as count 
                                                                                                          FROM approved_input_credits aic
                                                                                                          JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                                                                                          WHERE ica.agrovet_id = $agrovetId 
                                                                                                          AND aic.status = 'defaulted'");
                                                                      
                                                                      $totalApprovedCredits = $app->select_one("SELECT COUNT(*) as count 
                                                                                                             FROM approved_input_credits aic
                                                                                                             JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                                                                                             WHERE ica.agrovet_id = $agrovetId");
                                                                      
                                                                      $defaultRate = 0;
                                                                      if ($totalApprovedCredits->count > 0) {
                                                                          $defaultRate = ($defaultedCredits->count / $totalApprovedCredits->count) * 100;
                                                                      }
                                                                      
                                                                      echo number_format($defaultRate, 1) . '%'; 
                                                                  ?>
                                                                </h4>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="d-flex align-items-center justify-content-between mt-1">
                                                            <div>
                                                                <a class="text-purple" href="default-analysis">View
                                                                    Details<i
                                                                        class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                                            </div>
                                                            <div class="text-end">
                                                                <p class="mb-0 text-purple fw-semibold">
                                                                    <?php echo $defaultedCredits->count; ?></p>
                                                                <span class="text-muted op-7 fs-11">defaulted</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Account Balance Card -->
                                    <div class="col-xxl-3 col-lg-6 col-md-6">
                                        <div class="card custom-card overflow-hidden">
                                            <div class="card-body">
                                                <div class="d-flex align-items-top justify-content-between">
                                                    <div>
                                                        <span class="avatar avatar-md avatar-rounded bg-secondary">
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
                                                                    $accountBalance = $app->select_one("SELECT balance 
                                                                                                      FROM agrovet_accounts 
                                                                                                      WHERE agrovet_id = $agrovetId");
                                                                    echo 'KES ' . number_format($accountBalance->balance ?? 0, 0); 
                                                                ?>
                                                                </h4>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="d-flex align-items-center justify-content-between mt-1">
                                                            <div>
                                                                <a class="text-secondary" href="account-details">View
                                                                    Details<i
                                                                        class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                                            </div>
                                                            <div class="text-end">
                                                                <p class="mb-0 text-secondary fw-semibold">Current</p>
                                                                <span class="text-muted op-7 fs-11">balance</span>
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
                    <!-- row 2 -->
                    <div class="row mt-4">
                        <div class="col-xl-12">
                            <div class="card custom-card">
                                <div class="card-header justify-content-between">
                                    <div class="card-title">Input Credit Activity Analysis</div>
                                    <div class="dropdown">
                                        <a href="javascript:void(0);" class="p-2 fs-12 text-muted"
                                            data-bs-toggle="dropdown">
                                            View All<i
                                                class="ri-arrow-down-s-line align-middle ms-1 d-inline-block"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Input Credit Activity Summary Cards -->
                                    <div class="row g-3 mb-4">
                                        <!-- Applications Card -->
                                        <div class="col-xl-3 col-lg-6">
                                            <div class="p-3 border rounded-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <span class="avatar avatar-sm avatar-rounded bg-primary">
                                                            <i class="ti ti-file-text fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 text-muted fs-12">Credit Applications</p>
                                                        <h5 class="mb-0">
                                                            <?php
                                                             $app = new App();
                                                             // Get agrovet_id for the current staff
                                                             $staffQuery = "SELECT s.agrovet_id 
                                                                           FROM agrovet_staff s 
                                                                           WHERE s.user_id = {$_SESSION['user_id']}";
                                                             $staffResult = $app->select_one($staffQuery);
                                                             $agrovetId = $staffResult->agrovet_id ?? 0;
                                                             
                                                             $creditApps = $app->select_one("SELECT COUNT(*) as count 
                                                                                           FROM input_credit_applications 
                                                                                           WHERE agrovet_id = $agrovetId
                                                                                           AND DATE(application_date) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
                                                             echo number_format($creditApps->count ?? 0);
                                                         ?>
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Approved Credits Card -->
                                        <div class="col-xl-3 col-lg-6">
                                            <div class="p-3 border rounded-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <span class="avatar avatar-sm avatar-rounded bg-success">
                                                            <i class="ti ti-check fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 text-muted fs-12">Approved Credits</p>
                                                        <h5 class="mb-0">
                                                            <?php
                                                            $approvedCredits = $app->select_one("SELECT COUNT(*) as count 
                                                                                              FROM input_credit_applications 
                                                                                              WHERE agrovet_id = $agrovetId
                                                                                              AND status IN ('approved', 'fulfilled', 'completed')
                                                                                              AND DATE(application_date) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
                                                            echo number_format($approvedCredits->count ?? 0);
                                                        ?>
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Rejected Credits Card -->
                                        <div class="col-xl-3 col-lg-6">
                                            <div class="p-3 border rounded-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <span class="avatar avatar-sm avatar-rounded bg-danger">
                                                            <i class="ti ti-x fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 text-muted fs-12">Rejected Credits</p>
                                                        <h5 class="mb-0">
                                                            <?php
                                                                 $rejectedCredits = $app->select_one("SELECT COUNT(*) as count 
                                                                                                   FROM input_credit_applications 
                                                                                                   WHERE agrovet_id = $agrovetId
                                                                                                   AND status = 'rejected' 
                                                                                                   AND DATE(application_date) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
                                                                 echo number_format($rejectedCredits->count ?? 0);
                                                             ?>
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Fulfilled Amount Card -->
                                        <div class="col-xl-3 col-lg-6">
                                            <div class="p-3 border rounded-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <span class="avatar avatar-sm avatar-rounded bg-info">
                                                            <i class="ti ti-cash fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 text-muted fs-12">Fulfilled Amount</p>
                                                        <h5 class="mb-0">
                                                            <?php
                                                            $fulfilledAmount = $app->select_one("SELECT SUM(aic.approved_amount) as total 
                                                                                              FROM approved_input_credits aic
                                                                                              JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                                                                              WHERE ica.agrovet_id = $agrovetId
                                                                                              AND aic.fulfillment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
                                                            echo 'KES ' . number_format($fulfilledAmount->total ?? 0, 2);
                                                        ?>
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Recent Input Credit Activity List -->
                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped align-middle">
                                            <thead>
                                                <tr>
                                                    <th>Farmer</th>
                                                    <th>Input Types</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Application Date</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $recentCredits = $app->select_all("SELECT ica.*, u.first_name, u.last_name,
                                                                                 GROUP_CONCAT(DISTINCT ici.input_type) as input_types 
                                                                              FROM input_credit_applications ica 
                                                                              JOIN farmers f ON ica.farmer_id = f.id 
                                                                              JOIN users u ON f.user_id = u.id 
                                                                              JOIN input_credit_items ici ON ici.credit_application_id = ica.id
                                                                              WHERE ica.agrovet_id = $agrovetId
                                                                              GROUP BY ica.id
                                                                              ORDER BY ica.application_date DESC LIMIT 5");
                                                if($recentCredits):
                                                    foreach($recentCredits as $credit):
                                                ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-2">
                                                                <span class="avatar avatar-sm avatar-rounded bg-light">
                                                                    <?php echo strtoupper(substr($credit->first_name, 0, 1)); ?>
                                                                </span>
                                                            </div>
                                                            <div>
                                                                <?php echo htmlspecialchars($credit->first_name . ' ' . $credit->last_name); ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                           $types = explode(',', $credit->input_types);
                                                           foreach (array_unique($types) as $type) {
                                                               $badgeClass = match($type) {
                                                                   'fertilizer' => 'success',
                                                                   'pesticide' => 'danger',
                                                                   'seeds' => 'primary',
                                                                   'tools' => 'info',
                                                                   default => 'secondary'
                                                               };
                                                               echo '<span class="badge bg-' . $badgeClass . '-transparent me-1">' . ucfirst($type) . '</span>';
                                                           }
                                                           ?>
                                                    </td>
                                                    <td><strong>KES
                                                            <?php echo number_format($credit->total_amount, 2); ?></strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?php 
                                                           echo match($credit->status) {
                                                               'approved' => 'success',
                                                               'rejected' => 'danger',
                                                               'under_review' => 'warning',
                                                               'fulfilled' => 'info',
                                                               'completed' => 'primary',
                                                               'pending' => 'secondary',
                                                               'cancelled' => 'dark',
                                                               default => 'secondary'
                                                           };
                                                           ?>-transparent">
                                                            <?php echo ucwords(str_replace('_', ' ', $credit->status)); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <small>
                                                            <?php 
                                        // Define formatTimeAgo function if not available
                                        if (method_exists($app, 'formatTimeAgo')) {
                                            echo $app->formatTimeAgo($credit->application_date);
                                        } else {
                                            echo date('M d, Y', strtotime($credit->application_date));
                                        }
                                        ?>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <div class="hstack gap-2 fs-15">
                                                            <a href="javascript:void(0);"
                                                                class="btn btn-icon btn-sm btn-light"><i
                                                                    class="ri-eye-line"></i></a>
                                                            <?php if($credit->status == 'under_review' || $credit->status == 'pending'): ?>
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
                                                    <td colspan="6" class="text-center">No recent input credit
                                                        applications found</td>
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
                                    <div class="card-title">Input Credit Performance Analytics</div>
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
                                        <!-- Credit Summary Statistics -->
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
                                                                    $totalApps = $app->select_one("SELECT COUNT(*) as count 
                                                                    FROM input_credit_applications 
                                                                    WHERE agrovet_id = $agrovetId 
                                                                    AND DATE(application_date) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
                                                                        $approvedCount = $approvedCredits->count ?? 0;
                                                                        $totalCount = isset($totalApps->count) ? $totalApps->count : 0;
                                                                        $approvalRate = $totalCount > 0 ? ($approvedCount / $totalCount) * 100 : 0;
                                                                        echo round($approvalRate, 1) . '%';
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
                                                            <p class="mb-0">Average Credit Amount</p>
                                                            <div
                                                                class="d-flex justify-content-between align-items-center">
                                                                <h5 class="mb-0">
                                                                    <?php
                                                                 $avgCredit = $app->select_one("SELECT AVG(total_amount) as average 
                                                                                              FROM input_credit_applications 
                                                                                              WHERE agrovet_id = $agrovetId
                                                                                              AND DATE(application_date) >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)");
                                                                 echo 'KES ' . number_format($avgCredit->average ?? 0, 2);
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
                                                                     echo '1.8 Days';
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
                                                            <p class="mb-0">Repayment Rate</p>
                                                            <div
                                                                class="d-flex justify-content-between align-items-center">
                                                                <h5 class="mb-0">
                                                                    <?php
                                                                   $fulfilledCredits = $app->select_one("SELECT 
                                                                                                       COALESCE(SUM(aic.total_with_interest), 0) as total_due,
                                                                                                       COALESCE(SUM(aic.total_with_interest - aic.remaining_balance), 0) as total_repaid
                                                                                                   FROM approved_input_credits aic
                                                                                                   JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                                                                                   WHERE ica.agrovet_id = $agrovetId");
                                                                   
                                                                   $repaymentRate = ($fulfilledCredits->total_due > 0) ? 
                                                                       ($fulfilledCredits->total_repaid / $fulfilledCredits->total_due) * 100 : 0;
                                                                   echo round($repaymentRate, 1) . '%';
                                                                   ?>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="progress mt-2" style="height: 5px;">
                                                        <div class="progress-bar bg-success"
                                                            style="width: <?php echo $repaymentRate; ?>%"
                                                            role="progressbar"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-8">
                                            <?php include "../graphs/input-credit-analytics.php" ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Row 4: Input Credit Analytics -->
                    <div class="row mt-4">
                        <div class="col-xl-12">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">
                                        <i class="ri-bar-chart-grouped-line me-2" style="color: #6AA32D;"></i> Input
                                        Credit Analytics
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Input Type Distribution Chart -->
                                        <div class="col-xl-6 col-lg-12">
                                            <div class="card border shadow-sm h-100">
                                                <div class="card-header bg-light">
                                                    <div class="card-title">
                                                        <i class="ri-pie-chart-2-line me-2" style="color: #6AA32D;"></i>
                                                        Input Type Distribution
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <?php include "../graphs/inputTypeDistributionChart.php" ?>

                                                </div>
                                            </div>
                                        </div>
                                        <!-- Disbursement vs. Repayment Trends -->
                                        <div class="col-xl-6 col-lg-12">
                                            <div class="card border shadow-sm h-100">
                                                <div class="card-header bg-light">
                                                    <div class="card-title">
                                                        <i class="ri-exchange-funds-line me-2"
                                                            style="color: #6AA32D;"></i> Disbursement vs. Repayment
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <?php include "../graphs/disbursementRepaymentChart.php" ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Performance by Farmer Category -->
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <div class="card border shadow-sm">
                                                <div class="card-header bg-light">
                                                    <div class="card-title">
                                                        <i class="ri-user-star-line me-2" style="color: #6AA32D;"></i>
                                                        Performance by Farmer Category
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <?php
                                                       $app = new App();
                                                       
                                                       // Get agrovet_id for the current staff
                                                       $staffQuery = "SELECT s.agrovet_id 
                                                                     FROM agrovet_staff s 
                                                                     WHERE s.user_id = {$_SESSION['user_id']}";
                                                       $staffResult = $app->select_one($staffQuery);
                                                       $agrovetId = $staffResult->agrovet_id ?? 0;
                                                       
                                                       // Get performance metrics by farmer category
                                                       $categoryQuery = "SELECT 
                                                                          fc.id as category_id,
                                                                          fc.name as category_name,
                                                                          COUNT(DISTINCT ica.id) as application_count,
                                                                          COUNT(DISTINCT CASE WHEN ica.status IN ('approved', 'fulfilled', 'completed') THEN ica.id END) as approved_count,
                                                                          SUM(CASE WHEN aic.id IS NOT NULL THEN aic.approved_amount ELSE 0 END) as total_disbursed,
                                                                          SUM(CASE WHEN aic.id IS NOT NULL THEN aic.total_with_interest - aic.remaining_balance ELSE 0 END) as total_repaid,
                                                                          CASE 
                                                                              WHEN SUM(CASE WHEN aic.id IS NOT NULL THEN aic.total_with_interest ELSE 0 END) > 0 
                                                                              THEN (SUM(CASE WHEN aic.id IS NOT NULL THEN aic.total_with_interest - aic.remaining_balance ELSE 0 END) / 
                                                                                   SUM(CASE WHEN aic.id IS NOT NULL THEN aic.total_with_interest ELSE 0 END)) * 100
                                                                              ELSE 0
                                                                          END as repayment_rate
                                                                        FROM farmer_categories fc
                                                                        LEFT JOIN farmers f ON f.category_id = fc.id
                                                                        LEFT JOIN input_credit_applications ica ON ica.farmer_id = f.id AND ica.agrovet_id = $agrovetId
                                                                        LEFT JOIN approved_input_credits aic ON aic.credit_application_id = ica.id
                                                                        GROUP BY fc.id, fc.name
                                                                        ORDER BY total_disbursed DESC";
                                                       $categories = $app->select_all($categoryQuery);
                                                       ?>

                                                    <div class="table-responsive">
                                                        <table class="table table-hover table-striped align-middle">
                                                            <thead class="bg-light">
                                                                <tr>
                                                                    <th><i class="ri-user-line me-2"
                                                                            style="color: #6AA32D;"></i>Category</th>
                                                                    <th><i class="ri-file-list-3-line me-2"
                                                                            style="color: #6AA32D;"></i>Applications
                                                                    </th>
                                                                    <th><i class="ri-funds-line me-2"
                                                                            style="color: #6AA32D;"></i>Disbursed Amount
                                                                    </th>
                                                                    <th><i class="ri-refund-2-line me-2"
                                                                            style="color: #6AA32D;"></i>Repayment Rate
                                                                    </th>
                                                                    <th><i class="ri-line-chart-line me-2"
                                                                            style="color: #6AA32D;"></i>Trend</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php if ($categories): ?>
                                                                <?php foreach ($categories as $category): ?>
                                                                <?php if ($category->application_count > 0): ?>
                                                                <?php 
                                                        $repaymentRate = round($category->repayment_rate, 1);
                                                        $badgeClass = 'bg-danger';
                                                        if ($repaymentRate >= 90) {
                                                            $badgeClass = 'bg-success';
                                                        } elseif ($repaymentRate >= 75) {
                                                            $badgeClass = 'bg-info';
                                                        } elseif ($repaymentRate >= 50) {
                                                            $badgeClass = 'bg-warning';
                                                        }
                                                        
                                                        // Calculate approval rate
                                                        $approvalRate = ($category->application_count > 0) ? 
                                                            ($category->approved_count / $category->application_count) * 100 : 0;
                                                        $approvalRound = round($approvalRate);
                                                        
                                                        // Generate icon for trend based on approval and repayment rates
                                                        $trendIcon = 'ri-arrow-right-line';
                                                        $trendColor = 'warning';
                                                        if ($approvalRate >= 70 && $repaymentRate >= 70) {
                                                            $trendIcon = 'ri-arrow-up-line';
                                                            $trendColor = 'success';
                                                        } elseif ($approvalRate <= 30 || $repaymentRate <= 30) {
                                                            $trendIcon = 'ri-arrow-down-line';
                                                            $trendColor = 'danger';
                                                        }
                                                        ?>
                                                                <tr>
                                                                    <td>
                                                                        <div class="d-flex align-items-center">
                                                                            <span class="avatar avatar-xs me-2 
                                                                        <?php 
                                                                            echo match($category->category_id) {
                                                                                1 => 'bg-primary',
                                                                                2 => 'bg-success',
                                                                                3 => 'bg-info',
                                                                                default => 'bg-secondary'
                                                                            };
                                                                        ?>">
                                                                                <i class="ri-user-line"></i>
                                                                            </span>
                                                                            <?php echo htmlspecialchars($category->category_name); ?>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="d-flex flex-column">
                                                                            <span><?php echo $category->application_count; ?>
                                                                                <small
                                                                                    class="text-muted">(<?php echo $approvalRound; ?>%
                                                                                    approved)</small>
                                                                            </span>
                                                                            <div class="progress mt-1"
                                                                                style="height: 4px; width: 100px;">
                                                                                <div class="progress-bar"
                                                                                    style="width: <?php echo $approvalRate; ?>%; background-color: #6AA32D;"
                                                                                    role="progressbar"></div>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <strong>KES
                                                                            <?php echo number_format($category->total_disbursed, 2); ?></strong>
                                                                    </td>
                                                                    <td>
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="progress flex-grow-1 me-2"
                                                                                style="height: 5px">
                                                                                <div class="progress-bar <?php echo $badgeClass; ?>"
                                                                                    style="width: <?php echo $repaymentRate; ?>%"
                                                                                    role="progressbar"></div>
                                                                            </div>
                                                                            <span
                                                                                class="badge <?php echo $badgeClass; ?>-transparent">
                                                                                <?php echo $repaymentRate; ?>%
                                                                            </span>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="d-flex align-items-center">
                                                                            <span
                                                                                class="avatar avatar-xs bg-<?php echo $trendColor; ?>-transparent text-<?php echo $trendColor; ?> me-2">
                                                                                <i
                                                                                    class="<?php echo $trendIcon; ?>"></i>
                                                                            </span>
                                                                            <span
                                                                                class="text-<?php echo $trendColor; ?> fw-semibold">
                                                                                <?php 
                                                                        echo match($trendColor) {
                                                                            'success' => 'Strong Performance',
                                                                            'warning' => 'Average Performance',
                                                                            'danger' => 'Needs Attention',
                                                                            default => 'Neutral'
                                                                        }; 
                                                                        ?>
                                                                            </span>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <?php endif; ?>
                                                                <?php endforeach; ?>
                                                                <?php else: ?>
                                                                <tr>
                                                                    <td colspan="5" class="text-center py-3">No data
                                                                        available for farmer categories</td>
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
                        </div>
                    </div>
                    <!-- Row 4: Input Credit Analytics -->
                    <div class="row mt-4">
                        <div class="col-xl-12">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">
                                        <i class="ri-bar-chart-grouped-line me-2" style="color: #6AA32D;"></i> Input
                                        Credit Analytics
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Input Type Distribution Cards -->
                                    <div class="row g-3 mb-4">
                                        <?php
                                        $app = new App();
                                        
                                        // Get agrovet_id for the current staff
                                        $staffQuery = "SELECT s.agrovet_id 
                                                      FROM agrovet_staff s 
                                                      WHERE s.user_id = {$_SESSION['user_id']}";
                                        $staffResult = $app->select_one($staffQuery);
                                        $agrovetId = $staffResult->agrovet_id ?? 0;
                                        
                                        // Get input type distribution data
                                        $inputTypeQuery = "SELECT 
                                                            ici.input_type, 
                                                            COUNT(*) as count,
                                                            SUM(ici.total_price) as total_amount
                                                          FROM input_credit_items ici
                                                          JOIN input_credit_applications ica ON ici.credit_application_id = ica.id
                                                          WHERE ica.agrovet_id = $agrovetId
                                                          GROUP BY ici.input_type
                                                          ORDER BY count DESC";
                                        $inputTypes = $app->select_all($inputTypeQuery);
                                        
                                        // Get total for percentage calculation
                                        $totalCount = 0;
                                        $totalAmount = 0;
                                        foreach ($inputTypes as $type) {
                                            $totalCount += $type->count;
                                            $totalAmount += $type->total_amount;
                                        }
                                        
                                        // Define visuals for each input type
                                        $typeIcons = [
                                            'fertilizer' => 'ri-seedling-line',
                                            'pesticide' => 'ri-bug-line',
                                            'seeds' => 'ri-plant-line',
                                            'tools' => 'ri-tools-line',
                                            'other' => 'ri-box-3-line'
                                        ];
                                        
                                        $typeColors = [
                                            'fertilizer' => '#6AA32D',
                                            'pesticide' => '#E74C3C',
                                            'seeds' => '#3498DB',
                                            'tools' => '#F39C12',
                                            'other' => '#9B59B6'
                                        ];
                                        
                                        foreach ($inputTypes as $type):
                                            $percentage = ($totalCount > 0) ? round(($type->count / $totalCount) * 100) : 0;
                                            $icon = $typeIcons[$type->input_type] ?? 'ri-box-3-line';
                                            $color = $typeColors[$type->input_type] ?? '#6AA32D';
                                        ?>
                                        <div class="col-xl-3 col-md-6">
                                            <div class="card border h-100">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-3">
                                                        <div class="me-3">
                                                            <span class="avatar avatar-md"
                                                                style="background-color: <?php echo $color; ?>;">
                                                                <i class="<?php echo $icon; ?> fs-18 text-white"></i>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0"><?php echo ucfirst($type->input_type); ?>
                                                            </h6>
                                                            <span class="text-muted fs-12"><?php echo $type->count; ?>
                                                                requests (<?php echo $percentage; ?>%)</span>
                                                        </div>
                                                    </div>
                                                    <div class="progress mb-2" style="height: 6px;">
                                                        <div class="progress-bar"
                                                            style="width: <?php echo $percentage; ?>%; background-color: <?php echo $color; ?>;"
                                                            role="progressbar"
                                                            aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0"
                                                            aria-valuemax="100"></div>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="text-muted fs-12">Total Value</div>
                                                        <div class="fw-semibold">KES
                                                            <?php echo number_format($type->total_amount, 2); ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <!-- Disbursement vs Repayment Metrics -->
                                    <div class="row g-3 mb-4">
                                        <div class="col-12">
                                            <div class="card border">
                                                <div class="card-header bg-light">
                                                    <div class="card-title">
                                                        <i class="ri-exchange-funds-line me-2"
                                                            style="color: #6AA32D;"></i> Disbursement vs. Repayment
                                                        Metrics
                                                    </div>
                                                </div>
                                                <div class="card-body p-0">
                                                    <?php
                                // Get quarterly disbursement and repayment data
                                $quarters = ['Q1', 'Q2', 'Q3', 'Q4'];
                                $currentMonth = date('n');
                                $currentQuarter = ceil($currentMonth / 3);
                                
                                $quarterlyData = [];
                                
                                foreach ($quarters as $index => $quarter) {
                                    $quarterNum = $index + 1;
                                    $startMonth = ($quarterNum - 1) * 3 + 1;
                                    $endMonth = $quarterNum * 3;
                                    
                                    // Disbursements
                                    $disbursementQuery = "SELECT COALESCE(SUM(aic.approved_amount), 0) as total 
                                                       FROM approved_input_credits aic
                                                       JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                                       WHERE ica.agrovet_id = $agrovetId
                                                       AND MONTH(aic.fulfillment_date) BETWEEN $startMonth AND $endMonth
                                                       AND YEAR(aic.fulfillment_date) = YEAR(CURRENT_DATE())";
                                    $disbursementResult = $app->select_one($disbursementQuery);
                                    
                                    // Repayments
                                    $repaymentQuery = "SELECT COALESCE(SUM(icr.amount), 0) as total 
                                                     FROM input_credit_repayments icr
                                                     JOIN approved_input_credits aic ON icr.approved_credit_id = aic.id
                                                     JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                                     WHERE ica.agrovet_id = $agrovetId
                                                     AND MONTH(icr.deduction_date) BETWEEN $startMonth AND $endMonth
                                                     AND YEAR(icr.deduction_date) = YEAR(CURRENT_DATE())";
                                    $repaymentResult = $app->select_one($repaymentQuery);
                                    
                                    $quarterlyData[] = [
                                        'quarter' => $quarter,
                                        'disbursed' => $disbursementResult->total,
                                        'repaid' => $repaymentResult->total,
                                        'is_current' => $quarterNum == $currentQuarter
                                    ];
                                }
                                ?>

                                                    <div class="table-responsive">
                                                        <table class="table table-hover mb-0">
                                                            <thead class="bg-light">
                                                                <tr>
                                                                    <th>Quarter</th>
                                                                    <th>Disbursed</th>
                                                                    <th>Repaid</th>
                                                                    <th>Repayment Rate</th>
                                                                    <th>Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($quarterlyData as $data): ?>
                                                                <?php
                                                $repaymentRate = ($data['disbursed'] > 0) ? 
                                                    ($data['repaid'] / $data['disbursed']) * 100 : 0;
                                                
                                                $statusBadge = 'bg-warning';
                                                $statusText = 'Average';
                                                
                                                if ($repaymentRate >= 80) {
                                                    $statusBadge = 'bg-success';
                                                    $statusText = 'Excellent';
                                                } elseif ($repaymentRate >= 60) {
                                                    $statusBadge = 'bg-info';
                                                    $statusText = 'Good';
                                                } elseif ($repaymentRate < 40) {
                                                    $statusBadge = 'bg-danger';
                                                    $statusText = 'Poor';
                                                }
                                                ?>
                                                                <tr
                                                                    class="<?php echo $data['is_current'] ? 'table-active' : ''; ?>">
                                                                    <td>
                                                                        <div class="d-flex align-items-center">
                                                                            <span class="avatar avatar-xs me-2"
                                                                                style="background-color: #6AA32D;">
                                                                                <i class="ri-calendar-line"></i>
                                                                            </span>
                                                                            <?php echo $data['quarter']; ?>
                                                                            <?php echo date('Y'); ?>
                                                                            <?php if ($data['is_current']): ?>
                                                                            <span
                                                                                class="badge bg-primary-transparent ms-2">Current</span>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="d-flex align-items-center">
                                                                            <span
                                                                                class="avatar avatar-xs me-2 bg-success-transparent text-success">
                                                                                <i class="ri-arrow-right-up-line"></i>
                                                                            </span>
                                                                            <strong>KES
                                                                                <?php echo number_format($data['disbursed'], 2); ?></strong>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="d-flex align-items-center">
                                                                            <span
                                                                                class="avatar avatar-xs me-2 bg-info-transparent text-info">
                                                                                <i class="ri-arrow-left-down-line"></i>
                                                                            </span>
                                                                            <strong>KES
                                                                                <?php echo number_format($data['repaid'], 2); ?></strong>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="progress flex-grow-1 me-2"
                                                                                style="height: 5px;">
                                                                                <div class="progress-bar"
                                                                                    role="progressbar"
                                                                                    style="width: <?php echo round($repaymentRate); ?>%; background-color: #6AA32D;"
                                                                                    aria-valuenow="<?php echo round($repaymentRate); ?>"
                                                                                    aria-valuemin="0"
                                                                                    aria-valuemax="100"></div>
                                                                            </div>
                                                                            <span><?php echo round($repaymentRate, 1); ?>%</span>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <span
                                                                            class="badge <?php echo $statusBadge; ?>-transparent">
                                                                            <?php echo $statusText; ?>
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Performance by Farmer Category -->
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card border">
                                                <div class="card-header bg-light">
                                                    <div class="card-title">
                                                        <i class="ri-user-star-line me-2" style="color: #6AA32D;"></i>
                                                        Performance by Farmer Category
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <?php
                                // Get performance metrics by farmer category
                                $categoryQuery = "SELECT 
                                                   fc.id as category_id,
                                                   fc.name as category_name,
                                                   COUNT(DISTINCT ica.id) as application_count,
                                                   COUNT(DISTINCT CASE WHEN ica.status IN ('approved', 'fulfilled', 'completed') THEN ica.id END) as approved_count,
                                                   SUM(CASE WHEN aic.id IS NOT NULL THEN aic.approved_amount ELSE 0 END) as total_disbursed,
                                                   SUM(CASE WHEN aic.id IS NOT NULL THEN aic.total_with_interest - aic.remaining_balance ELSE 0 END) as total_repaid,
                                                   CASE 
                                                       WHEN SUM(CASE WHEN aic.id IS NOT NULL THEN aic.total_with_interest ELSE 0 END) > 0 
                                                       THEN (SUM(CASE WHEN aic.id IS NOT NULL THEN aic.total_with_interest - aic.remaining_balance ELSE 0 END) / 
                                                            SUM(CASE WHEN aic.id IS NOT NULL THEN aic.total_with_interest ELSE 0 END)) * 100
                                                       ELSE 0
                                                   END as repayment_rate
                                                 FROM farmer_categories fc
                                                 LEFT JOIN farmers f ON f.category_id = fc.id
                                                 LEFT JOIN input_credit_applications ica ON ica.farmer_id = f.id AND ica.agrovet_id = $agrovetId
                                                 LEFT JOIN approved_input_credits aic ON aic.credit_application_id = ica.id
                                                 GROUP BY fc.id, fc.name
                                                 ORDER BY total_disbursed DESC";
                                $categories = $app->select_all($categoryQuery);
                                ?>

                                                    <div class="row g-3">
                                                        <?php 
                                    $categoryColors = [
                                        1 => '#6AA32D', // Smallholder
                                        2 => '#3498DB', // Emerging
                                        3 => '#F39C12', // Commercial
                                    ];
                                    
                                    $categoryIcons = [
                                        1 => 'ri-user-line', // Smallholder
                                        2 => 'ri-team-line', // Emerging
                                        3 => 'ri-building-line', // Commercial
                                    ];
                                    
                                    foreach ($categories as $category): 
                                        if ($category->application_count > 0): 
                                            $repaymentRate = round($category->repayment_rate, 1);
                                            $approvalRate = ($category->application_count > 0) ? 
                                                round(($category->approved_count / $category->application_count) * 100) : 0;
                                            
                                            $color = $categoryColors[$category->category_id] ?? '#6AA32D';
                                            $icon = $categoryIcons[$category->category_id] ?? 'ri-user-line';
                                    ?>
                                                        <div class="col-xl-4 col-md-6">
                                                            <div class="card border h-100">
                                                                <div class="card-header d-flex align-items-center"
                                                                    style="border-left: 4px solid <?php echo $color; ?>;">
                                                                    <span class="avatar avatar-sm me-2"
                                                                        style="background-color: <?php echo $color; ?>;">
                                                                        <i class="<?php echo $icon; ?>"></i>
                                                                    </span>
                                                                    <h6 class="mb-0">
                                                                        <?php echo htmlspecialchars($category->category_name); ?>
                                                                    </h6>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="row g-3">
                                                                        <div class="col-6">
                                                                            <div class="d-flex flex-column">
                                                                                <span
                                                                                    class="text-muted fs-12">Applications</span>
                                                                                <span
                                                                                    class="fs-5 fw-semibold"><?php echo $category->application_count; ?></span>
                                                                                <div class="progress mt-2"
                                                                                    style="height: 4px;">
                                                                                    <div class="progress-bar"
                                                                                        style="width: <?php echo $approvalRate; ?>%; background-color: <?php echo $color; ?>;"
                                                                                        role="progressbar"></div>
                                                                                </div>
                                                                                <span
                                                                                    class="text-muted fs-11 mt-1"><?php echo $approvalRate; ?>%
                                                                                    approved</span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-6">
                                                                            <div class="d-flex flex-column">
                                                                                <span class="text-muted fs-12">Repayment
                                                                                    Rate</span>
                                                                                <span
                                                                                    class="fs-5 fw-semibold"><?php echo $repaymentRate; ?>%</span>
                                                                                <div class="progress mt-2"
                                                                                    style="height: 4px;">
                                                                                    <div class="progress-bar"
                                                                                        style="width: <?php echo $repaymentRate; ?>%; background-color: <?php echo $color; ?>;"
                                                                                        role="progressbar"></div>
                                                                                </div>
                                                                                <span class="text-muted fs-11 mt-1">
                                                                                    <?php
                                                                if ($repaymentRate >= 80) echo "Excellent";
                                                                elseif ($repaymentRate >= 60) echo "Good";
                                                                elseif ($repaymentRate >= 40) echo "Average";
                                                                else echo "Needs attention";
                                                                ?>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center mt-4">
                                                                        <div>
                                                                            <span class="text-muted fs-12">Total
                                                                                Disbursed</span>
                                                                            <div class="fw-semibold">KES
                                                                                <?php echo number_format($category->total_disbursed, 2); ?>
                                                                            </div>
                                                                        </div>
                                                                        <div>
                                                                            <span class="text-muted fs-12">Total
                                                                                Repaid</span>
                                                                            <div class="fw-semibold">KES
                                                                                <?php echo number_format($category->total_repaid, 2); ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php 
                                        endif; 
                                    endforeach; 
                                    ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Row 5: Inventory & Input Type Analysis -->
                    <div class="row mt-4">
                        <div class="col-xl-12">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">
                                        <i class="ri-stack-line me-2" style="color: #6AA32D;"></i> Inventory & Input
                                        Type Analysis
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php
                $app = new App();
                
                // Get agrovet_id for the current staff
                $staffQuery = "SELECT s.agrovet_id 
                              FROM agrovet_staff s 
                              WHERE s.user_id = {$_SESSION['user_id']}";
                $staffResult = $app->select_one($staffQuery);
                $agrovetId = $staffResult->agrovet_id ?? 0;
                
                // Get most popular input items
                $popularInputsQuery = "SELECT 
                                        ici.input_type,
                                        ici.input_name,
                                        COUNT(*) as request_count,
                                        SUM(ici.quantity) as total_quantity,
                                        ici.unit,
                                        SUM(ici.total_price) as total_revenue
                                      FROM input_credit_items ici
                                      JOIN input_credit_applications ica ON ici.credit_application_id = ica.id
                                      WHERE ica.agrovet_id = $agrovetId
                                      GROUP BY ici.input_type, ici.input_name
                                      ORDER BY request_count DESC
                                      LIMIT 6";
                $popularInputs = $app->select_all($popularInputsQuery);
                ?>

                                    <!-- Popular Inputs Cards -->
                                    <div class="row g-3 mb-4">
                                        <div class="col-xl-12">
                                            <div class="card border">
                                                <div class="card-header bg-light">
                                                    <div class="card-title">
                                                        <i class="ri-trophy-line me-2" style="color: #6AA32D;"></i> Most
                                                        Popular Input Items
                                                    </div>
                                                </div>
                                                <div class="card-body p-0">
                                                    <div class="row g-0">
                                                        <?php
                                    $inputTypeIcons = [
                                        'fertilizer' => 'ri-seedling-line',
                                        'pesticide' => 'ri-bug-line',
                                        'seeds' => 'ri-plant-line',
                                        'tools' => 'ri-tools-line',
                                        'other' => 'ri-box-3-line'
                                    ];
                                    
                                    $inputTypeColors = [
                                        'fertilizer' => '#6AA32D',
                                        'pesticide' => '#E74C3C',
                                        'seeds' => '#3498DB',
                                        'tools' => '#F39C12',
                                        'other' => '#9B59B6'
                                    ];
                                    
                                    if ($popularInputs):
                                        foreach ($popularInputs as $index => $input):
                                            $icon = $inputTypeIcons[$input->input_type] ?? 'ri-box-3-line';
                                            $color = $inputTypeColors[$input->input_type] ?? '#6AA32D';
                                    ?>
                                                        <div class="col-xl-4 col-md-6 border-end border-bottom p-3">
                                                            <div class="d-flex">
                                                                <div class="me-3">
                                                                    <span class="avatar avatar-md avatar-rounded"
                                                                        style="background-color: <?php echo $color; ?>;">
                                                                        <i class="<?php echo $icon; ?> fs-18"></i>
                                                                    </span>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <h6 class="mb-1">
                                                                        <?php echo htmlspecialchars($input->input_name); ?>
                                                                    </h6>
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center">
                                                                        <span class="text-muted fs-12">
                                                                            <?php echo ucfirst($input->input_type); ?> •
                                                                            <?php echo $input->total_quantity; ?>
                                                                            <?php echo $input->unit; ?>
                                                                        </span>
                                                                        <span class="badge"
                                                                            style="background-color: <?php echo $color; ?>;">
                                                                            <?php echo $input->request_count; ?>
                                                                            requests
                                                                        </span>
                                                                    </div>
                                                                    <div class="mt-2">
                                                                        <div class="progress" style="height: 5px;">
                                                                            <div class="progress-bar"
                                                                                style="width: <?php echo min(100, $input->request_count * 5); ?>%; background-color: <?php echo $color; ?>;"
                                                                                role="progressbar"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center mt-2">
                                                                        <span class="text-muted fs-12">Revenue</span>
                                                                        <span class="fw-semibold"
                                                                            style="color: <?php echo $color; ?>;">
                                                                            KES
                                                                            <?php echo number_format($input->total_revenue, 2); ?>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php 
                                        endforeach; 
                                    else:
                                    ?>
                                                        <div class="col-12">
                                                            <div class="text-center py-4">
                                                                <i class="ri-inbox-line fs-2 text-muted"></i>
                                                                <p class="mb-0 mt-2">No input request data available</p>
                                                            </div>
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Seasonal Trends Cards -->
                                    <div class="row g-3 mb-4">
                                        <?php
                    // Get seasonal trends data
                    $seasonalQuery = "SELECT 
                                       CASE 
                                           WHEN MONTH(ica.application_date) BETWEEN 1 AND 3 THEN 'Q1'
                                           WHEN MONTH(ica.application_date) BETWEEN 4 AND 6 THEN 'Q2'
                                           WHEN MONTH(ica.application_date) BETWEEN 7 AND 9 THEN 'Q3'
                                           ELSE 'Q4'
                                       END as quarter,
                                       ici.input_type,
                                       COUNT(*) as request_count
                                     FROM input_credit_items ici
                                     JOIN input_credit_applications ica ON ici.credit_application_id = ica.id
                                     WHERE ica.agrovet_id = $agrovetId
                                     AND YEAR(ica.application_date) = YEAR(CURRENT_DATE())
                                     GROUP BY quarter, ici.input_type
                                     ORDER BY quarter, request_count DESC";
                    $seasonalData = $app->select_all($seasonalQuery);
                    
                    // Organize by quarter
                    $quarterData = [
                        'Q1' => [],
                        'Q2' => [],
                        'Q3' => [],
                        'Q4' => []
                    ];
                    
                    foreach ($seasonalData as $data) {
                        $quarterData[$data->quarter][] = $data;
                    }
                    
                    // Get current quarter
                    $currentMonth = date('n');
                    $currentQuarter = 'Q' . ceil($currentMonth / 3);
                    
                    $seasonalColors = [
                        'Q1' => '#3498DB', // Winter/Spring - Blue
                        'Q2' => '#2ECC71', // Spring/Summer - Green
                        'Q3' => '#F39C12', // Summer/Fall - Orange
                        'Q4' => '#9B59B6'  // Fall/Winter - Purple
                    ];
                    
                    $seasonalIcons = [
                        'Q1' => 'ri-snowy-line',
                        'Q2' => 'ri-sun-line',
                        'Q3' => 'ri-leaf-line',
                        'Q4' => 'ri-cloud-windy-line'
                    ];
                    
                    foreach ($quarterData as $quarter => $data):
                        $isCurrentQuarter = ($quarter == $currentQuarter);
                        $color = $seasonalColors[$quarter] ?? '#6AA32D';
                        $icon = $seasonalIcons[$quarter] ?? 'ri-calendar-line';
                    ?>
                                        <div class="col-xl-3 col-md-6">
                                            <div
                                                class="card border h-100 <?php echo $isCurrentQuarter ? 'border-primary' : ''; ?>">
                                                <div class="card-header d-flex align-items-center"
                                                    style="border-bottom: 2px solid <?php echo $color; ?>; <?php echo $isCurrentQuarter ? 'background-color: rgba(52, 152, 219, 0.1);' : ''; ?>">
                                                    <span class="avatar avatar-sm me-2"
                                                        style="background-color: <?php echo $color; ?>;">
                                                        <i class="<?php echo $icon; ?>"></i>
                                                    </span>
                                                    <h6 class="mb-0">
                                                        <?php echo $quarter; ?> <?php echo date('Y'); ?>
                                                        <?php if ($isCurrentQuarter): ?>
                                                        <span class="badge bg-primary-transparent ms-2">Current</span>
                                                        <?php endif; ?>
                                                    </h6>
                                                </div>
                                                <div class="card-body p-0">
                                                    <ul class="list-group list-group-flush">
                                                        <?php
                                    if (!empty($data)):
                                        // Get total count for percentage
                                        $totalCount = 0;
                                        foreach ($data as $item) {
                                            $totalCount += $item->request_count;
                                        }
                                        
                                        foreach (array_slice($data, 0, 3) as $item):
                                            $percentage = ($totalCount > 0) ? 
                                                ($item->request_count / $totalCount) * 100 : 0;
                                            
                                            $typeIcon = $inputTypeIcons[$item->input_type] ?? 'ri-box-3-line';
                                            $typeColor = $inputTypeColors[$item->input_type] ?? '#6AA32D';
                                    ?>
                                                        <li class="list-group-item">
                                                            <div class="d-flex align-items-center">
                                                                <span class="avatar avatar-xs me-2"
                                                                    style="background-color: <?php echo $typeColor; ?>;">
                                                                    <i class="<?php echo $typeIcon; ?>"></i>
                                                                </span>
                                                                <div class="flex-grow-1">
                                                                    <div class="d-flex justify-content-between">
                                                                        <span><?php echo ucfirst($item->input_type); ?></span>
                                                                        <span class="badge bg-light text-dark">
                                                                            <?php echo $item->request_count; ?> requests
                                                                        </span>
                                                                    </div>
                                                                    <div class="progress mt-1" style="height: 4px;">
                                                                        <div class="progress-bar"
                                                                            style="width: <?php echo $percentage; ?>%; background-color: <?php echo $typeColor; ?>;"
                                                                            role="progressbar"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <?php 
                                       endforeach; 
                                   else:
                                   ?>
                                                        <li class="list-group-item text-center py-3">
                                                            <span class="text-muted">No data available</span>
                                                        </li>
                                                        <?php endif; ?>
                                                    </ul>
                                                </div>
                                                <div class="card-footer bg-light text-center">
                                                    <small class="text-muted">
                                                        <?php echo count($data); ?> input types requested this quarter
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <!-- Inventory Recommendations Cards -->
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="card border">
                                                <div class="card-header bg-light">
                                                    <div class="card-title">
                                                        <i class="ri-lightbulb-line me-2" style="color: #6AA32D;"></i>
                                                        Inventory & Stocking Recommendations
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3">
                                                        <?php
                                   // Generate recommendations based on the data
                                   $currentMonth = date('n');
                                   $currentQuarter = ceil($currentMonth / 3);
                                   $nextQuarter = $currentQuarter == 4 ? 1 : $currentQuarter + 1;
                                   $nextQuarterText = 'Q' . $nextQuarter;
                                   
                                   $recommendations = [
                                       [
                                           'title' => 'Stock Up for Next Quarter',
                                           'icon' => 'ri-store-3-line',
                                           'color' => '#6AA32D',
                                           'description' => 'Based on historical data, prepare inventory for ' . $nextQuarterText . ' with a focus on these input types:',
                                           'items' => ['Seeds', 'Fertilizers', 'Pesticides'],
                                           'tip' => 'Order 15-20% more than current quarter to meet expected demand'
                                       ],
                                       [
                                           'title' => 'High Demand Products',
                                           'icon' => 'ri-fire-line',
                                           'color' => '#E74C3C',
                                           'description' => 'These products have consistently shown high demand:',
                                           'items' => ['NPK Fertilizer', 'Organic Pesticides', 'Hybrid Seeds'],
                                           'tip' => 'Maintain at least 2 weeks of stock for these high-turnover items'
                                       ],
                                       [
                                           'title' => 'Seasonal Promotion Opportunity',
                                           'icon' => 'ri-price-tag-3-line',
                                           'color' => '#F39C12',
                                           'description' => 'Consider running promotions on seasonal inputs to increase farmer adoption:',
                                           'items' => ['Irrigation Equipment', 'Post-Harvest Tools', 'Soil Amendments'],
                                           'tip' => 'Bundle popular items with slower-moving inventory to boost sales'
                                       ],
                                       [
                                           'title' => 'Inventory Health Check',
                                           'icon' => 'ri-heart-pulse-line',
                                           'color' => '#3498DB',
                                           'description' => 'Conduct regular checks on:',
                                           'items' => ['Product Expiry Dates', 'Storage Conditions', 'Stock Levels'],
                                           'tip' => 'Schedule weekly inventory audits to maintain optimal stock levels'
                                       ]
                                   ];
                                   
                                   foreach ($recommendations as $rec):
                                   ?>
                                                        <div class="col-xl-3 col-md-6">
                                                            <div class="card border h-100"
                                                                style="border-top: 3px solid <?php echo $rec['color']; ?>;">
                                                                <div class="card-body">
                                                                    <div class="d-flex align-items-center mb-3">
                                                                        <span class="avatar avatar-md me-3"
                                                                            style="background-color: <?php echo $rec['color']; ?>;">
                                                                            <i
                                                                                class="<?php echo $rec['icon']; ?> fs-18"></i>
                                                                        </span>
                                                                        <h6 class="mb-0"><?php echo $rec['title']; ?>
                                                                        </h6>
                                                                    </div>
                                                                    <p class="text-muted">
                                                                        <?php echo $rec['description']; ?></p>
                                                                    <ul class="list-unstyled">
                                                                        <?php foreach ($rec['items'] as $item): ?>
                                                                        <li class="mb-1">
                                                                            <div class="d-flex align-items-center">
                                                                                <span
                                                                                    class="avatar avatar-xs bg-light me-2 text-dark">
                                                                                    <i
                                                                                        class="ri-checkbox-circle-line"></i>
                                                                                </span>
                                                                                <?php echo $item; ?>
                                                                            </div>
                                                                        </li>
                                                                        <?php endforeach; ?>
                                                                    </ul>
                                                                </div>
                                                                <div class="card-footer bg-light">
                                                                    <div class="d-flex align-items-center">
                                                                        <span
                                                                            class="avatar avatar-xs me-2 bg-warning-transparent text-warning">
                                                                            <i class="ri-lightbulb-flash-line"></i>
                                                                        </span>
                                                                        <small><?php echo $rec['tip']; ?></small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Input Credit Repayment Dashboard for Agrovet Staff -->
                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <div class="card custom-card shadow-sm border-0">
                                <div class="card-header d-flex align-items-center"
                                    style="background: linear-gradient(to right, #f5f8ff, #ffffff);">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-primary-transparent me-2">
                                            <i class="fa-solid fa-calendar-check text-primary"></i>
                                        </div>
                                        <h6 class="mb-0 fw-semibold">Input Credit Repayment Calendar</h6>
                                    </div>
                                    <div class="ms-auto">
                                        <span class="badge rounded-pill bg-success-transparent text-success">
                                            <i class="fa-solid fa-info-circle me-1"></i> Repayment Tracking
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Credit Summary Statistics -->
                                        <div class="col-lg-4 col-md-6">
                                            <div class="d-flex p-3 border rounded-3 bg-light-transparent">
                                                <div class="me-3">
                                                    <div class="avatar avatar-md avatar-rounded bg-primary-transparent">
                                                        <i
                                                            class="fa-solid fa-file-invoice-dollar text-primary fs-18"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="fw-semibold mb-1">Active Credits</h6>
                                                    <?php
                                                    // Get the agrovet_id for the logged-in user
                                                              $staff_query = "SELECT s.agrovet_id 
                                                                              FROM agrovet_staff s 
                                                                              WHERE s.user_id = " . $_SESSION['user_id'];
                                                              $staff_result = $app->select_one($staff_query);
                                                              $agrovet_id = $staff_result->agrovet_id;
    
                                                              // Get active credits count and total outstanding amount
                                                              $statsQuery = "SELECT 
                                                                  COUNT(aic.id) as active_credits,
                                                                  SUM(aic.remaining_balance) AS total_outstanding
                                                              FROM 
                                                                  approved_input_credits aic
                                                              JOIN 
                                                                  input_credit_applications ica ON aic.credit_application_id = ica.id
                                                              WHERE 
                                                                  ica.agrovet_id = {$agrovet_id} AND aic.status = 'active'";
                                                              
                                                              $creditStats = $app->select_one($statsQuery);
                                                              
                                                              if($creditStats && $creditStats->active_credits > 0):
                                                          ?>
                                                    <div>
                                                        <span class="fs-14 fw-semibold text-primary">
                                                            <?php echo $creditStats->active_credits; ?> active credit(s)
                                                        </span>
                                                        <div class="text-muted small">
                                                            Outstanding balance:
                                                            <span class="text-danger fw-semibold">
                                                                KES
                                                                <?php echo number_format($creditStats->total_outstanding, 2); ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <?php else: ?>
                                                    <div class="text-muted">No active input credits</div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Recent Repayments -->
                                        <div class="col-lg-4 col-md-6">
                                            <div class="d-flex p-3 border rounded-3 bg-light-transparent">
                                                <div class="me-3">
                                                    <div class="avatar avatar-md avatar-rounded bg-success-transparent">
                                                        <i
                                                            class="fa-solid fa-hand-holding-dollar text-success fs-18"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="fw-semibold mb-1">Recent Repayments</h6>
                                                    <?php
                                                                      // Get recent repayments statistics
                                                                      $recentQuery = "SELECT 
                                                                          COUNT(icr.id) as recent_count,
                                                                          SUM(icr.amount) AS recent_total
                                                                      FROM 
                                                                          input_credit_repayments icr
                                                                      JOIN 
                                                                          approved_input_credits aic ON icr.approved_credit_id = aic.id
                                                                      JOIN 
                                                                          input_credit_applications ica ON aic.credit_application_id = ica.id
                                                                      WHERE 
                                                                          ica.agrovet_id = {$agrovet_id} AND 
                                                                          icr.deduction_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
                                                                      
                                                                      $recentStats = $app->select_one($recentQuery);
                                                                      
                                                                      if($recentStats && $recentStats->recent_count > 0):
                                                                  ?>
                                                    <div>
                                                        <span class="fs-14 fw-semibold text-success">
                                                            KES
                                                            <?php echo number_format($recentStats->recent_total, 2); ?>
                                                        </span>
                                                        <div class="text-muted small">
                                                            <?php echo $recentStats->recent_count; ?> repayment(s) in
                                                            the last 30 days
                                                        </div>
                                                    </div>
                                                    <?php else: ?>
                                                    <div class="text-muted">No recent repayments in the last 30 days
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Calendar Options -->
                                        <div class="col-lg-4 col-md-12">
                                            <div class="p-3 border rounded-3 bg-light-transparent h-100">
                                                <h6 class="fw-semibold mb-3">
                                                    <i class="fa-solid fa-sliders text-primary me-1"></i> Calendar View
                                                    Options
                                                </h6>
                                                <div class="mb-2 d-flex gap-2 flex-wrap">
                                                    <button id="viewMonthBtn"
                                                        class="btn btn-sm btn-outline-primary active">
                                                        <i class="fa-solid fa-calendar-days me-1"></i> Month
                                                    </button>
                                                    <button id="viewWeekBtn" class="btn btn-sm btn-outline-primary">
                                                        <i class="fa-solid fa-calendar-week me-1"></i> Week
                                                    </button>
                                                    <button id="viewListBtn" class="btn btn-sm btn-outline-primary">
                                                        <i class="fa-solid fa-list-ul me-1"></i> List
                                                    </button>
                                                </div>
                                                <div>
                                                    <div class="d-flex align-items-center mb-2">
                                                        <span class="badge rounded-pill bg-primary me-2"></span>
                                                        <span class="small">Credit issuance</span>
                                                    </div>
                                                    <div class="d-flex align-items-center mb-2">
                                                        <span class="badge rounded-pill bg-success me-2"></span>
                                                        <span class="small">Completed repayments</span>
                                                    </div>
                                                    <div class="d-flex align-items-center mb-2">
                                                        <span class="badge rounded-pill bg-warning me-2"></span>
                                                        <span class="small">Pending repayments</span>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge rounded-pill bg-orange me-2"></span>
                                                        <span class="small">Expected repayments</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Alert for upcoming expected repayments -->
                                    <?php
                                    // Get the agrovet_id for the logged-in user
                                             $staff_query = "SELECT s.agrovet_id 
                                                             FROM agrovet_staff s 
                                                             WHERE s.user_id = " . $_SESSION['user_id'];
                                             $staff_result = $app->select_one($staff_query);
                                             $agrovet_id = $staff_result->agrovet_id;
    
                                          // Get nearest upcoming expected repayment
                                          $upcomingQuery = "SELECT 
                                              aic.id AS credit_id,
                                              aic.credit_application_id,
                                              f.registration_number AS farmer_reg,
                                              CONCAT(u.first_name, ' ', u.last_name) AS farmer_name,
                                              aic.total_with_interest / 6 AS estimated_amount,
                                              DATE_ADD(aic.fulfillment_date, INTERVAL 30 DAY) AS next_expected_date
                                          FROM 
                                              approved_input_credits aic
                                          JOIN 
                                              input_credit_applications ica ON aic.credit_application_id = ica.id
                                          JOIN 
                                              farmers f ON ica.farmer_id = f.id
                                          JOIN 
                                              users u ON f.user_id = u.id
                                          WHERE 
                                              ica.agrovet_id = {$agrovet_id} AND 
                                              aic.status = 'active' AND
                                              DATE_ADD(aic.fulfillment_date, INTERVAL 30 DAY) >= CURDATE()
                                          ORDER BY 
                                              next_expected_date ASC
                                          LIMIT 1";
                                          
                                          $upcomingRepayment = $app->select_one($upcomingQuery);
                                          
                                          if($upcomingRepayment):
                                          $today = new DateTime();
                                          $expectedDate = new DateTime($upcomingRepayment->next_expected_date);
                                          $interval = $today->diff($expectedDate);
                                          $daysUntil = $interval->days;
                                          
                                          if($daysUntil <= 14): // Only show if within 14 days
                                                            ?>
                                    <div class="alert alert-info mt-3 mb-0">
                                        <div class="d-flex">
                                            <div class="me-3">
                                                <i class="fa-solid fa-bell fs-24 text-info"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">Upcoming Expected Repayment</h6>
                                                <p class="mb-0">
                                                    Farmer
                                                    <strong><?php echo $upcomingRepayment->farmer_name; ?></strong>
                                                    (<?php echo $upcomingRepayment->farmer_reg; ?>) may make a repayment
                                                    around
                                                    <strong><?php echo date('F d, Y', strtotime($upcomingRepayment->next_expected_date)); ?></strong>
                                                    of approximately <strong>KES
                                                        <?php echo number_format($upcomingRepayment->estimated_amount, 2); ?></strong>.
                                                    This is an estimate based on the credit terms.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php 
                    endif;
                    endif; 
                ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Repayment Calendar -->
                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <div class="card custom-card shadow-sm border-0">
                                <div class="card-body">
                                    <!-- Calendar will be rendered here -->
                                    <div id="inputCreditCalendar" class="input-credit-calendar"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Stats Cards -->
                    <div class="row mt-4">
                        <div class="col-lg-4 col-md-6">
                            <div class="card custom-card shadow-sm border-0 mb-4">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="avatar avatar-sm bg-warning-transparent rounded-circle">
                                                <i class="fa-solid fa-hourglass-half text-warning"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="mb-0 text-muted fs-12">Pending Repayments</p>
                                            <?php
                                                // Get count of expected repayments
                                                $pendingQuery = "SELECT 
                                                    COUNT(aic.id) as pending_count
                                                FROM 
                                                    approved_input_credits aic
                                                JOIN 
                                                    input_credit_applications ica ON aic.credit_application_id = ica.id
                                                WHERE 
                                                    ica.agrovet_id = {$agrovet_id} AND 
                                                    aic.status = 'active' AND
                                                    aic.fulfillment_date <= CURDATE() - INTERVAL 30 DAY";
                                                
                                                $pendingStats = $app->select_one($pendingQuery);
                                            ?>
                                            <h6 class="fw-semibold mb-0">
                                                <?php echo $pendingStats ? $pendingStats->pending_count : 0; ?>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="card custom-card shadow-sm border-0 mb-4">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="avatar avatar-sm bg-success-transparent rounded-circle">
                                                <i class="fa-solid fa-check-circle text-success"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="mb-0 text-muted fs-12">Completed Credits</p>
                                            <?php
                                                  // Get count of completed credits
                                                  $completedQuery = "SELECT 
                                                      COUNT(aic.id) as completed_count
                                                  FROM 
                                                      approved_input_credits aic
                                                  JOIN 
                                                      input_credit_applications ica ON aic.credit_application_id = ica.id
                                                  WHERE 
                                                      ica.agrovet_id = {$agrovet_id} AND 
                                                      aic.status = 'completed'";
                                                  
                                                  $completedStats = $app->select_one($completedQuery);
                                              ?>
                                            <h6 class="fw-semibold mb-0">
                                                <?php echo $completedStats ? $completedStats->completed_count : 0; ?>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-12">
                            <div class="card custom-card shadow-sm border-0 mb-4">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="avatar avatar-sm bg-danger-transparent rounded-circle">
                                                <i class="fa-solid fa-credit-card text-danger"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="mb-0 text-muted fs-12">Repayment Rate</p>
                                            <?php
                                                    // Calculate repayment rate
                                                    $rateQuery = "SELECT 
                                                        COALESCE(SUM(icr.amount) / SUM(aic.total_with_interest) * 100, 0) as repayment_rate
                                                    FROM 
                                                        approved_input_credits aic
                                                    JOIN 
                                                        input_credit_applications ica ON aic.credit_application_id = ica.id
                                                    LEFT JOIN 
                                                        input_credit_repayments icr ON aic.id = icr.approved_credit_id
                                                    WHERE 
                                                        ica.agrovet_id = {$agrovet_id} AND 
                                                        (aic.status = 'active' OR aic.status = 'completed')";
                                                    
                                                    $rateStats = $app->select_one($rateQuery);
                                                    $rate = $rateStats ? round($rateStats->repayment_rate, 1) : 0;
                                                ?>
                                            <h6 class="fw-semibold mb-0">
                                                <?php echo $rate; ?>%
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- logs -->
                    <div class="col-xxl-12 col-xl-12">
                        <div class="card custom-card">
                            <div class="card-header justify-content-between">
                                <div class="card-title">
                                    <i class="ri-exchange-dollar-line me-2"></i> Recent Credit Activities
                                </div>
                            </div>
                            <div class="card-body">
                                <div>
                                    <ul class="list-unstyled mb-0 crm-recent-activity">
                                        <?php
                                                             // Get the agrovet_id if not already set
                                                             if (!isset($agrovet_id)) {
                                                                 $staff_query = "SELECT s.agrovet_id 
                                                                                 FROM agrovet_staff s 
                                                                                 WHERE s.user_id = " . $_SESSION['user_id'];
                                                                 $staff_result = $app->select_one($staff_query);
                                                                 $agrovet_id = $staff_result->agrovet_id;
                                                             }
                                                             
                                                             // Get recent activities related to input credits
                                                             $recentActivities = $app->select_all("
                                                                 SELECT icl.*, 
                                                                        u.first_name, u.last_name, u.email,
                                                                        f.registration_number as farmer_reg,
                                                                        ica.id as application_id,
                                                                        fu.first_name as farmer_fname, fu.last_name as farmer_lname
                                                                 FROM input_credit_logs icl
                                                                 JOIN users u ON icl.user_id = u.id
                                                                 JOIN input_credit_applications ica ON icl.input_credit_application_id = ica.id
                                                                 JOIN farmers f ON ica.farmer_id = f.id
                                                                 JOIN users fu ON f.user_id = fu.id
                                                                 WHERE ica.agrovet_id = $agrovet_id
                                                                 ORDER BY icl.created_at DESC
                                                                 LIMIT 8
                                                             ");
                                                             
                                                             // Also get recent account transactions
                                                             $accountQuery = "
                                                                 SELECT aat.*, 
                                                                        u.first_name, u.last_name, 
                                                                        'account_transaction' as log_type
                                                                 FROM agrovet_account_transactions aat
                                                                 JOIN users u ON aat.processed_by = u.id
                                                                 JOIN agrovet_accounts aa ON aat.agrovet_account_id = aa.id
                                                                 WHERE aa.agrovet_id = $agrovet_id
                                                                 ORDER BY aat.created_at DESC
                                                                 LIMIT 4
                                                             ";
                                                             $accountTransactions = $app->select_all($accountQuery);
                                                             
                                                             // Combine and sort both types of activities
                                                             $combinedActivities = [];
                                                             
                                                             if ($recentActivities) {
                                                                 foreach ($recentActivities as $activity) {
                                                                     $activity->log_type = 'credit_log';
                                                                     $combinedActivities[] = $activity;
                                                                 }
                                                             }
                                                             
                                                             if ($accountTransactions) {
                                                                 foreach ($accountTransactions as $transaction) {
                                                                     $combinedActivities[] = $transaction;
                                                                 }
                                                             }
                                                             
                                                             // Sort by created_at
                                                             usort($combinedActivities, function($a, $b) {
                                                                 return strtotime($b->created_at) - strtotime($a->created_at);
                                                             });
                                                             
                                                             // Limit to 10 most recent
                                                             $combinedActivities = array_slice($combinedActivities, 0, 10);
                                                             
                                                             if($combinedActivities):
                                                                 foreach($combinedActivities as $activity):
                                                                     // Set colors and icons based on activity type
                                                                     if ($activity->log_type == 'credit_log') {
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
                                                                             'application_submitted' => 'ri-file-list-3-line',
                                                                             'approved' => 'ri-check-double-line',
                                                                             'rejected' => 'ri-close-circle-line',
                                                                             'fulfilled' => 'ri-shopping-basket-line',
                                                                             'payment_made' => 'ri-money-dollar-circle-line',
                                                                             'completed' => 'ri-medal-line',
                                                                             default => 'ri-information-line'
                                                                         };
                                                                     } else {
                                                                         // Account transaction
                                                                         $activityColor = $activity->transaction_type == 'credit' ? 'success' : 'danger';
                                                                         $icon = $activity->transaction_type == 'credit' ? 'ri-arrow-down-circle-line' : 'ri-arrow-up-circle-line';
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
                                                    <?php if ($activity->log_type == 'credit_log'): ?>
                                                    <span class="fw-semibold">
                                                        <?php 
                                                                                 $actionLabel = match($activity->action_type) {
                                                                                     'application_submitted' => 'New credit application submitted',
                                                                                     'approved' => 'Credit application approved',
                                                                                     'rejected' => 'Credit application rejected',
                                                                                     'fulfilled' => 'Credit inputs provided to farmer',
                                                                                     'payment_made' => 'Credit repayment received',
                                                                                     'completed' => 'Credit fully repaid',
                                                                                     default => ucfirst(str_replace('_', ' ', $activity->action_type))
                                                                                 };
                                                                                 echo $actionLabel; 
                                                                                 ?>
                                                    </span>
                                                    <span class="d-block text-muted fs-11">
                                                        Farmer:
                                                        <?php echo htmlspecialchars($activity->farmer_fname . ' ' . $activity->farmer_lname); ?>
                                                        (<?php echo $activity->farmer_reg; ?>)
                                                    </span>
                                                    <span class="d-block text-muted fs-11">
                                                        by
                                                        <?php echo htmlspecialchars($activity->first_name . ' ' . $activity->last_name); ?>
                                                    </span>
                                                    <?php else: ?>
                                                    <span class="fw-semibold">
                                                        <?php echo $activity->transaction_type == 'credit' ? 'Money received' : 'Money sent'; ?>
                                                        <span class="fw-bold text-<?php echo $activityColor; ?>">
                                                            KES <?php echo number_format($activity->amount, 2); ?>
                                                        </span>
                                                    </span>
                                                    <span class="d-block text-muted fs-11">
                                                        <?php echo htmlspecialchars($activity->description); ?>
                                                    </span>
                                                    <span class="d-block text-muted fs-11">
                                                        by
                                                        <?php echo htmlspecialchars($activity->first_name . ' ' . $activity->last_name); ?>
                                                    </span>
                                                    <?php endif; ?>
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
                                        <li class="text-center text-muted">
                                            No recent activities found
                                        </li>
                                        <?php endif; ?>
                                    </ul>
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

    <script>
    // JavaScript for Input Credit Calendar Implementation
    $(document).ready(function() {
        // Initialize calendar when document is ready
        initializeInputCreditCalendar();

        // Handle the view buttons
        $('#viewMonthBtn').on('click', function() {
            calendar.changeView('dayGridMonth');
            updateActiveButton('viewMonthBtn');
        });

        $('#viewWeekBtn').on('click', function() {
            calendar.changeView('timeGridWeek');
            updateActiveButton('viewWeekBtn');
        });

        $('#viewListBtn').on('click', function() {
            calendar.changeView('listMonth');
            updateActiveButton('viewListBtn');
        });
    });

    let calendar; // Global variable to access calendar

    // Function to view full credit details - redirects to the credit details page
    function viewCreditDetails(creditId) {
        window.location.href = `input-credit-details?id=${creditId}`;
    }

    function updateActiveButton(activeButtonId) {
        // Remove active class from all buttons
        $('.btn-outline-primary').removeClass('active');
        // Add active class to the clicked button
        $('#' + activeButtonId).addClass('active');
    }

    function initializeInputCreditCalendar() {
        // Get the calendar container
        const calendarEl = document.getElementById('inputCreditCalendar');

        // Initialize FullCalendar
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            height: 'auto',
            themeSystem: 'bootstrap5',
            firstDay: 1, // Start the week on Monday
            dayMaxEvents: true, // Allow "more" link when too many events
            events: function(info, successCallback, failureCallback) {
                // Fetch events via AJAX
                $.ajax({
                    url: 'http://localhost/dfcs/ajax/agrovet-controller/get-input-credit-events.php',
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        successCallback(response);
                    },
                    error: function() {
                        failureCallback({
                            message: 'Error loading input credit data'
                        });
                        toastr.error('Failed to load input credit repayment data', 'Error', {
                            "positionClass": "toast-top-right",
                            "progressBar": true,
                            "timeOut": 3000
                        });
                    }
                });
            },
            eventClick: function(info) {
                showCreditEventDetails(info.event);
            },
            eventDidMount: function(info) {
                // Enable Bootstrap tooltips on events
                $(info.el).tooltip({
                    title: info.event.extendedProps.tooltip,
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body'
                });
            }
        });

        // Render the calendar
        calendar.render();
    }

    function showCreditEventDetails(event) {
        const eventType = event.extendedProps.type;
        let modalTitle, modalContent;

        // Different modal content based on event type
        if (eventType === 'fulfillment') {
            // Credit Issuance Event
            modalTitle = 'Credit Issuance Details';
            modalContent = `
            <div class="d-flex align-items-center mb-3">
                <div class="avatar avatar-md bg-primary-transparent me-3">
                    <i class="fa-solid fa-hand-holding-usd text-primary"></i>
                </div>
                <div>
                    <h6 class="mb-0">Credit Issued to ${event.extendedProps.farmerName}</h6>
                    <span class="badge bg-primary-transparent text-primary">Credit Reference: ${event.extendedProps.creditReference}</span>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <td class="fw-medium text-muted">Farmer:</td>
                            <td>${event.extendedProps.farmerName} (${event.extendedProps.farmerReg})</td>
                        </tr>
                        <tr>
                            <td class="fw-medium text-muted">Date Issued:</td>
                            <td>${event.start ? new Date(event.start).toLocaleDateString('en-US', {weekday:'long', year:'numeric', month:'long', day:'numeric'}) : 'N/A'}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium text-muted">Principal Amount:</td>
                            <td class="text-primary fw-semibold">KES ${event.extendedProps.amount}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium text-muted">Total With Interest:</td>
                            <td class="text-danger fw-semibold">KES ${event.extendedProps.totalWithInterest}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="alert alert-info mt-3">
                <i class="fa-solid fa-info-circle me-2"></i>
                This record shows when the input credit was issued to the farmer.
            </div>
        `;
        } else if (eventType === 'payment' || eventType === 'expected') {
            // Expected Repayment
            const status = event.extendedProps.status;
            let statusLabel, statusClass;

            switch (status) {
                case 'completed':
                    statusLabel = 'Completed';
                    statusClass = 'success';
                    break;
                case 'expected':
                    statusLabel = 'Expected';
                    statusClass = 'warning';
                    break;
                case 'pending':
                    statusLabel = 'Pending';
                    statusClass = 'warning';
                    break;
                default:
                    statusLabel = 'Unknown';
                    statusClass = 'secondary';
            }

            modalTitle = 'Expected Repayment Details';
            modalContent = `
            <div class="d-flex align-items-center mb-3">
                <div class="avatar avatar-md bg-${statusClass}-transparent me-3">
                    <i class="fa-solid fa-calendar-check text-${statusClass}"></i>
                </div>
                <div>
                    <h6 class="mb-0">Expected Repayment from ${event.extendedProps.farmerName}</h6>
                    <span class="badge bg-${statusClass}-transparent text-${statusClass}">${statusLabel}</span>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <td class="fw-medium text-muted">Credit Reference:</td>
                            <td>${event.extendedProps.creditReference}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium text-muted">Farmer:</td>
                            <td>${event.extendedProps.farmerName} (${event.extendedProps.farmerReg})</td>
                        </tr>
                        <tr>
                            <td class="fw-medium text-muted">Expected Date:</td>
                            <td>${event.start ? new Date(event.start).toLocaleDateString('en-US', {weekday:'long', year:'numeric', month:'long', day:'numeric'}) : 'N/A'}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium text-muted">Estimated Amount:</td>
                            <td class="text-${statusClass} fw-semibold">KES ${event.extendedProps.estimatedAmount}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            ${status === 'expected' ? `
            <div class="alert alert-info mt-3">
                <i class="fa-solid fa-info-circle me-2"></i>
                This is an estimated repayment date. Actual repayment will depend on the farmer's produce delivery and sales.
            </div>
            ` : ''}
            
            ${status === 'pending' ? `
            <div class="alert alert-warning mt-3">
                <i class="fa-solid fa-exclamation-triangle me-2"></i>
                This repayment was expected but hasn't been recorded yet. It will be automatically processed when the farmer delivers produce.
            </div>
            ` : ''}
        `;
        } else if (eventType === 'actual-payment') {
            // Actual Repayment
            modalTitle = 'Repayment Receipt';
            modalContent = `
            <div class="d-flex align-items-center mb-3">
                <div class="avatar avatar-md bg-success-transparent me-3">
                    <i class="fa-solid fa-check-circle text-success"></i>
                </div>
                <div>
                    <h6 class="mb-0">Repayment Received from ${event.extendedProps.farmerName}</h6>
                    <span class="badge bg-success-transparent text-success">Completed</span>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <td class="fw-medium text-muted">Credit Reference:</td>
                            <td>${event.extendedProps.creditReference}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium text-muted">Farmer:</td>
                            <td>${event.extendedProps.farmerName} (${event.extendedProps.farmerReg})</td>
                        </tr>
                        <tr>
                            <td class="fw-medium text-muted">Repayment Date:</td>
                            <td>${event.start ? new Date(event.start).toLocaleDateString('en-US', {weekday:'long', year:'numeric', month:'long', day:'numeric'}) : 'N/A'}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium text-muted">Amount Repaid:</td>
                            <td class="text-success fw-semibold">KES ${event.extendedProps.amount}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium text-muted">Produce Sale Amount:</td>
                            <td>KES ${event.extendedProps.produceSaleAmount}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium text-muted">Deduction Rate:</td>
                            <td>${event.extendedProps.deductionPercent}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="alert alert-success mt-3">
                <i class="fa-solid fa-check-circle me-2"></i>
                This repayment was automatically processed from the farmer's produce sale.
            </div>
        `;
        } else {
            // Default case
            modalTitle = 'Event Details';
            modalContent = `
            <div class="alert alert-info">
                <i class="fa-solid fa-info-circle me-2"></i>
                No detailed information available for this event.
            </div>
        `;
        }

        // Create modal HTML
        let modalHTML = `
        <div class="modal fade" id="creditEventModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title">${modalTitle}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ${modalContent}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        ${event.extendedProps.creditId ? `
                        <button type="button" class="btn btn-primary" onclick="viewCreditDetails(${event.extendedProps.creditId})">
                            View Credit Details
                        </button>
                        ` : ''}
                    </div>
                </div>
            </div>
        </div>
    `;

        // Append modal to body and show it
        $('body').append(modalHTML);
        $('#creditEventModal').modal('show');

        // Remove modal from DOM when hidden
        $('#creditEventModal').on('hidden.bs.modal', function() {
            $(this).remove();
        });
    }
    </script>
</body>

</html>