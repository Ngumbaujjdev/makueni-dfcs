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
                            
                            // Get bank details if user is bank staff
                            if ($_SESSION['role_id'] == 3) { // Assuming role_id 5 is for bank staff
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
                            } else if ($_SESSION['role_id'] == 4) {
                                // Agrovet staff welcome message
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
                <div class="col-xxl-12 col-xl-12">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="row">
                                <!-- Total Loan Portfolio Card -->
                                <div class="col-xxl-3 col-lg-6 col-md-6">
                                    <div class="card custom-card overflow-hidden">
                                        <div class="card-body">
                                            <div class="d-flex align-items-top justify-content-between">
                                                <div>
                                                    <span class="avatar avatar-md avatar-rounded bg-primary">
                                                        <i class="ti ti-briefcase fs-16"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill ms-3">
                                                    <div
                                                        class="d-flex align-items-center justify-content-between flex-wrap">
                                                        <div>
                                                            <p class="text-muted mb-0">Total Loan Portfolio</p>
                                                            <h4 class="fw-semibold mt-1">
                                                                <?php 
                                                                   // Get bank_id for the current staff
                                                                   $staffQuery = "SELECT s.bank_id 
                                                                                 FROM bank_staff s 
                                                                                 WHERE s.user_id = {$_SESSION['user_id']}";
                                                                   $staffResult = $app->select_one($staffQuery);
                                                                   $bankId = $staffResult->bank_id ?? 0;
                                                                   
                                                                   $totalLoans = $app->select_one("SELECT SUM(al.approved_amount) as amount 
                                                                                                 FROM approved_loans al
                                                                                                 WHERE al.bank_id = $bankId");
                                                                   echo 'KES ' . number_format($totalLoans->amount ?? 0, 0); 
                                                               ?>
                                                            </h4>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between mt-1">
                                                        <div>
                                                            <a class="text-primary" href="loan-portfolio">View
                                                                Portfolio<i
                                                                    class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                                        </div>
                                                        <div class="text-end">
                                                            <p class="mb-0 text-success fw-semibold">Active</p>
                                                            <span class="text-muted op-7 fs-11">loans</span>
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
                                                    <span class="avatar avatar-md avatar-rounded bg-success">
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
                                                                       $outstandingBalance = $app->select_one("SELECT SUM(al.remaining_balance) as amount 
                                                                                                             FROM approved_loans al
                                                                                                             WHERE al.bank_id = $bankId 
                                                                                                             AND al.status = 'active'");
                                                                       echo 'KES ' . number_format($outstandingBalance->amount ?? 0, 0); 
                                                                   ?>
                                                            </h4>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between mt-1">
                                                        <div>
                                                            <a class="text-success" href="outstanding-loans">View
                                                                Details<i
                                                                    class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                                        </div>
                                                        <div class="text-end">
                                                            <p class="mb-0 text-success fw-semibold">Receivable</p>
                                                            <span class="text-muted op-7 fs-11">amount</span>
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
                                                                                                   FROM loan_applications 
                                                                                                   WHERE bank_id = $bankId 
                                                                                                   AND status IN ('pending', 'under_review')");
                                                                    echo number_format($pendingApps->count); 
                                                                ?>
                                                            </h4>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between mt-1">
                                                        <div>
                                                            <a class="text-warning" href="pending-applications">Review
                                                                Applications<i
                                                                    class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                                        </div>
                                                        <div class="text-end">
                                                            <p class="mb-0 text-warning fw-semibold">Awaiting</p>
                                                            <span class="text-muted op-7 fs-11">review</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bank Account Balance Card -->
                                <div class="col-xxl-3 col-lg-6 col-md-6">
                                    <div class="card custom-card overflow-hidden">
                                        <div class="card-body">
                                            <div class="d-flex align-items-top justify-content-between">
                                                <div>
                                                    <span class="avatar avatar-md avatar-rounded bg-info">
                                                        <i class="ti ti-building-bank fs-16"></i>
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
                                                                                                        FROM bank_branch_accounts 
                                                                                                        WHERE bank_id = $bankId 
                                                                                                        AND account_type = 'Current'");
                                                                      echo 'KES ' . number_format($accountBalance->balance ?? 0, 0); 
                                                                  ?>
                                                            </h4>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between mt-1">
                                                        <div>
                                                            <a class="text-info" href="account-details">View
                                                                Transactions<i
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
                                <div class="col-xxl-3 col-lg-6 col-md-6">
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
                                                            <p class="text-muted mb-0">Monthly Revenue</p>
                                                            <h4 class="fw-semibold mt-1">
                                                                <?php 
                                                                       $monthlyRevenue = $app->select_one("SELECT SUM(lt.amount) as amount 
                                                                                                         FROM loan_transactions lt
                                                                                                         JOIN approved_loans al ON lt.loan_id = al.id
                                                                                                         WHERE al.bank_id = $bankId 
                                                                                                         AND lt.transaction_type = 'repayment'
                                                                                                         AND MONTH(lt.created_at) = MONTH(NOW())
                                                                                                         AND YEAR(lt.created_at) = YEAR(NOW())");
                                                                       echo 'KES ' . number_format($monthlyRevenue->amount ?? 0, 0); 
                                                                   ?>
                                                            </h4>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between mt-1">
                                                        <div>
                                                            <a style="color: #70A136;" href="revenue-report">View
                                                                Report<i
                                                                    class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                                        </div>
                                                        <div class="text-end">
                                                            <p class="mb-0 fw-semibold" style="color: #70A136;">This
                                                                Month</p>
                                                            <span class="text-muted op-7 fs-11">earnings</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Active Borrowers Card -->
                                <div class="col-xxl-3 col-lg-6 col-md-6">
                                    <div class="card custom-card overflow-hidden">
                                        <div class="card-body">
                                            <div class="d-flex align-items-top justify-content-between">
                                                <div>
                                                    <span class="avatar avatar-md avatar-rounded bg-purple">
                                                        <i class="ti ti-users fs-16"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill ms-3">
                                                    <div
                                                        class="d-flex align-items-center justify-content-between flex-wrap">
                                                        <div>
                                                            <p class="text-muted mb-0">Active Borrowers</p>
                                                            <h4 class="fw-semibold mt-1">
                                                                <?php 
                                                                   $activeBorrowers = $app->select_one("SELECT COUNT(DISTINCT la.farmer_id) as count 
                                                                                                      FROM loan_applications la
                                                                                                      JOIN approved_loans al ON la.id = al.loan_application_id
                                                                                                      WHERE la.bank_id = $bankId 
                                                                                                      AND al.status = 'active'");
                                                                   echo number_format($activeBorrowers->count); 
                                                               ?>
                                                            </h4>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between mt-1">
                                                        <div>
                                                            <a class="text-purple" href="borrower-profiles">View
                                                                Farmers<i
                                                                    class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                                        </div>
                                                        <div class="text-end">
                                                            <p class="mb-0 text-purple fw-semibold">Farmers</p>
                                                            <span class="text-muted op-7 fs-11">with loans</span>
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
                                                    <span class="avatar avatar-md avatar-rounded bg-danger">
                                                        <i class="ti ti-alert-triangle fs-16"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill ms-3">
                                                    <div
                                                        class="d-flex align-items-center justify-content-between flex-wrap">
                                                        <div>
                                                            <p class="text-muted mb-0">Default Rate</p>
                                                            <h4 class="fw-semibold mt-1">
                                                                <?php 
                                                                    $defaultedLoans = $app->select_one("SELECT COUNT(*) as count 
                                                                                                      FROM approved_loans al
                                                                                                      WHERE al.bank_id = $bankId 
                                                                                                      AND al.status = 'defaulted'");
                                                                    
                                                                    $totalLoansCount = $app->select_one("SELECT COUNT(*) as count 
                                                                                                       FROM approved_loans al
                                                                                                       WHERE al.bank_id = $bankId");
                                                                    
                                                                    $defaultRate = 0;
                                                                    if ($totalLoansCount->count > 0) {
                                                                        $defaultRate = ($defaultedLoans->count / $totalLoansCount->count) * 100;
                                                                    }
                                                                    
                                                                    echo number_format($defaultRate, 1) . '%'; 
                                                                ?>
                                                            </h4>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between mt-1">
                                                        <div>
                                                            <a class="text-danger" href="risk-analysis">Risk Analysis<i
                                                                    class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                                        </div>
                                                        <div class="text-end">
                                                            <p class="mb-0 text-danger fw-semibold">
                                                                <?php echo $defaultedLoans->count; ?></p>
                                                            <span class="text-muted op-7 fs-11">defaulted</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Loan Applications This Month Card -->
                                <div class="col-xxl-3 col-lg-6 col-md-6">
                                    <div class="card custom-card overflow-hidden">
                                        <div class="card-body">
                                            <div class="d-flex align-items-top justify-content-between">
                                                <div>
                                                    <span class="avatar avatar-md avatar-rounded"
                                                        style="background-color: rgba(74, 34, 15, 0.1); color: #4A220F;">
                                                        <i class="ti ti-file-plus fs-16"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill ms-3">
                                                    <div
                                                        class="d-flex align-items-center justify-content-between flex-wrap">
                                                        <div>
                                                            <p class="text-muted mb-0">New Applications</p>
                                                            <h4 class="fw-semibold mt-1">
                                                                <?php 
                                                                  $monthlyApps = $app->select_one("SELECT COUNT(*) as count 
                                                                                                 FROM loan_applications 
                                                                                                 WHERE bank_id = $bankId 
                                                                                                 AND MONTH(application_date) = MONTH(NOW())
                                                                                                 AND YEAR(application_date) = YEAR(NOW())");
                                                                  echo number_format($monthlyApps->count); 
                                                              ?>
                                                            </h4>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between mt-1">
                                                        <div>
                                                            <a style="color: #4A220F;" href="monthly-applications">View
                                                                Details<i
                                                                    class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a>
                                                        </div>
                                                        <div class="text-end">
                                                            <p class="mb-0 fw-semibold" style="color: #4A220F;">This
                                                                Month</p>
                                                            <span class="text-muted op-7 fs-11">applications</span>
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

                    <!-- Recommendations Section -->
                    <div class="row mt-4">
                        <div class="col-xl-12">
                            <div class="card custom-card">
                                <div class="card-header justify-content-between">
                                    <div class="card-title">
                                        <i class="ti ti-bulb me-2" style="color: #70A136;"></i>
                                        Smart Recommendations
                                    </div>

                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- High Performers -->
                                        <div class="col-xl-4 col-lg-6 col-md-12">
                                            <div class="alert alert-outline-success" role="alert">
                                                <div class="d-flex align-items-start">
                                                    <div class="me-2">
                                                        <span class="avatar avatar-sm svg-success">
                                                            <i class="ti ti-trending-up fs-14"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill">
                                                        <p class="fw-semibold mb-1">High-Performing Farmers</p>
                                                        <p class="op-8 mb-1 fs-12">
                                                            <?php 
                                                               $highPerformersQuery = "SELECT DISTINCT f.id, 
                                                                                      CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                                                                                      COUNT(pd.id) as deliveries,
                                                                                      AVG(pd.total_value) as avg_value
                                                                                      FROM loan_applications la
                                                                                      JOIN farmers f ON la.farmer_id = f.id
                                                                                      JOIN users u ON f.user_id = u.id
                                                                                      JOIN farms fm ON f.id = fm.farmer_id
                                                                                      JOIN farm_products fp ON fm.id = fp.farm_id
                                                                                      JOIN produce_deliveries pd ON fp.id = pd.farm_product_id
                                                                                      WHERE la.bank_id = $bankId 
                                                                                      AND pd.delivery_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                                                                                      AND pd.status = 'verified'
                                                                                      GROUP BY f.id, u.first_name, u.last_name
                                                                                      HAVING deliveries >= 3 AND avg_value > 20000
                                                                                      ORDER BY avg_value DESC
                                                                                      LIMIT 3";
                                                               $highPerformers = $app->select_all($highPerformersQuery);
                                                                                      
                                                                                      if ($highPerformers && count($highPerformers) > 0) {
                                                                                          echo count($highPerformers) . " farmers ready for loan upgrades";
                                                                                      } else {
                                                                   echo "No high performers identified this period";
                                                               }
                                                               ?>
                                                        </p>

                                                        <?php if ($highPerformers && count($highPerformers) > 0): ?>
                                                        <div class="mt-2">
                                                            <div class="table-responsive"
                                                                style="max-height: 150px; overflow-y: auto;">
                                                                <table class="table table-sm table-borderless mb-0">
                                                                    <tbody>
                                                                        <?php foreach ($highPerformers as $farmer): ?>
                                                                        <tr>
                                                                            <td class="ps-0">
                                                                                <div class="d-flex align-items-center">
                                                                                    <span
                                                                                        class="avatar avatar-xs bg-success-transparent rounded-pill me-2">
                                                                                        <i class="ti ti-user fs-10"></i>
                                                                                    </span>
                                                                                    <div>
                                                                                        <p
                                                                                            class="mb-0 fs-12 fw-semibold">
                                                                                            <?php echo htmlspecialchars($farmer->farmer_name); ?>
                                                                                        </p>
                                                                                        <p
                                                                                            class="mb-0 fs-10 text-muted">
                                                                                            <?php echo $farmer->deliveries; ?>
                                                                                            deliveries, Avg: KES
                                                                                            <?php echo number_format($farmer->avg_value, 0); ?>
                                                                                        </p>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-end pe-0">
                                                                                <button
                                                                                    class="btn btn-sm btn-outline-success btn-xs">
                                                                                    <i class="ti ti-plus fs-10"></i>
                                                                                    Upgrade
                                                                                </button>
                                                                            </td>
                                                                        </tr>
                                                                        <?php endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Risk Alerts -->
                                        <div class="col-xl-4 col-lg-6 col-md-12">
                                            <div class="alert alert-outline-warning" role="alert">
                                                <div class="d-flex align-items-start">
                                                    <div class="me-2">
                                                        <span class="avatar avatar-sm svg-warning">
                                                            <i class="ti ti-alert-circle fs-14"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill">
                                                        <p class="fw-semibold mb-1">Risk Alerts</p>
                                                        <p class="op-8 mb-1 fs-12">
                                                            <?php 
                                                               $riskFarmersQuery = "SELECT DISTINCT f.id,
                                                                                   CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                                                                                   al.remaining_balance,
                                                                                   COUNT(pd.id) as recent_deliveries,
                                                                                   DATEDIFF(NOW(), MAX(pd.delivery_date)) as days_since_last
                                                                                   FROM loan_applications la
                                                                                   JOIN farmers f ON la.farmer_id = f.id
                                                                                   JOIN users u ON f.user_id = u.id
                                                                                   JOIN approved_loans al ON la.id = al.loan_application_id
                                                                                   JOIN farms fm ON f.id = fm.farmer_id
                                                                                   JOIN farm_products fp ON fm.id = fp.farm_id
                                                                                   LEFT JOIN produce_deliveries pd ON fp.id = pd.farm_product_id 
                                                                                       AND pd.delivery_date >= DATE_SUB(NOW(), INTERVAL 3 MONTH)
                                                                                   WHERE la.bank_id = $bankId 
                                                                                   AND al.status = 'active'
                                                                                   AND al.remaining_balance > 0
                                                                                   GROUP BY f.id, u.first_name, u.last_name, al.remaining_balance
                                                                                   HAVING recent_deliveries <= 1
                                                                                   ORDER BY al.remaining_balance DESC
                                                                                   LIMIT 3";
                                                               $riskFarmers = $app->select_all($riskFarmersQuery);
                                                               
                                                               if ($riskFarmers && count($riskFarmers) > 0) {
                                                                   echo count($riskFarmers) . " farmers with declining activity";
                                                               } else {
                                                                   echo "No immediate risk alerts";
                                                               }
                                                               ?>
                                                        </p>

                                                        <?php if ($riskFarmers && count($riskFarmers) > 0): ?>
                                                        <div class="mt-2">
                                                            <div class="table-responsive"
                                                                style="max-height: 150px; overflow-y: auto;">
                                                                <table class="table table-sm table-borderless mb-0">
                                                                    <tbody>
                                                                        <?php foreach ($riskFarmers as $farmer): ?>
                                                                        <tr>
                                                                            <td class="ps-0">
                                                                                <div class="d-flex align-items-center">
                                                                                    <span
                                                                                        class="avatar avatar-xs bg-warning-transparent rounded-pill me-2">
                                                                                        <i
                                                                                            class="ti ti-alert-triangle fs-10"></i>
                                                                                    </span>
                                                                                    <div>
                                                                                        <p
                                                                                            class="mb-0 fs-12 fw-semibold">
                                                                                            <?php echo htmlspecialchars($farmer->farmer_name); ?>
                                                                                        </p>
                                                                                        <p
                                                                                            class="mb-0 fs-10 text-muted">
                                                                                            Owes: KES
                                                                                            <?php echo number_format($farmer->remaining_balance, 0); ?>
                                                                                        </p>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-end pe-0">
                                                                                <button
                                                                                    class="btn btn-sm btn-outline-warning btn-xs">
                                                                                    <i class="ti ti-phone fs-10"></i>
                                                                                    Call
                                                                                </button>
                                                                            </td>
                                                                        </tr>
                                                                        <?php endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Staff Workload -->
                                        <div class="col-xl-4 col-lg-6 col-md-12">
                                            <div class="alert alert-outline-info" role="alert">
                                                <div class="d-flex align-items-start">
                                                    <div class="me-2">
                                                        <span class="avatar avatar-sm svg-info">
                                                            <i class="ti ti-users fs-14"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill">
                                                        <p class="fw-semibold mb-1">Staff Workload</p>
                                                        <p class="op-8 mb-1 fs-12">
                                                            <?php 
                                                               $staffWorkloadQuery = "SELECT u.first_name, u.last_name,
                                                                                     bs.position,
                                                                                     COUNT(la.id) as pending_apps
                                                                                     FROM bank_staff bs
                                                                                     JOIN users u ON bs.user_id = u.id
                                                                                     LEFT JOIN loan_applications la ON la.reviewed_by = bs.user_id 
                                                                                         AND la.status IN ('pending', 'under_review')
                                                                                     WHERE bs.bank_id = $bankId
                                                                                     GROUP BY bs.id, u.first_name, u.last_name, bs.position
                                                                                     ORDER BY pending_apps DESC
                                                                                     LIMIT 3";
                                                               $staffWorkload = $app->select_all($staffWorkloadQuery);
                                                               
                                                               $overloadedStaff = 0;
                                                               if ($staffWorkload) {
                                                                   foreach ($staffWorkload as $staff) {
                                                                       if ($staff->pending_apps > 5) {
                                                                           $overloadedStaff++;
                                                                       }
                                                                   }
                                                               }
                                                               
                                                               if ($overloadedStaff > 0) {
                                                                   echo $overloadedStaff . " staff members need workload redistribution";
                                                               } else {
                                                                   echo "Workload is well distributed";
                                                               }
                                                               ?>
                                                        </p>

                                                        <?php if ($staffWorkload && count($staffWorkload) > 0): ?>
                                                        <div class="mt-2">
                                                            <div class="table-responsive"
                                                                style="max-height: 150px; overflow-y: auto;">
                                                                <table class="table table-sm table-borderless mb-0">
                                                                    <tbody>
                                                                        <?php foreach ($staffWorkload as $staff): ?>
                                                                        <tr>
                                                                            <td class="ps-0">
                                                                                <div class="d-flex align-items-center">
                                                                                    <span
                                                                                        class="avatar avatar-xs <?php echo $staff->pending_apps > 5 ? 'bg-warning-transparent' : 'bg-info-transparent'; ?> rounded-pill me-2">
                                                                                        <i class="ti ti-user fs-10"></i>
                                                                                    </span>
                                                                                    <div>
                                                                                        <p
                                                                                            class="mb-0 fs-12 fw-semibold">
                                                                                            <?php echo htmlspecialchars($staff->first_name . ' ' . $staff->last_name); ?>
                                                                                        </p>
                                                                                        <p
                                                                                            class="mb-0 fs-10 text-muted">
                                                                                            <?php echo htmlspecialchars($staff->position); ?>
                                                                                        </p>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-end pe-0">
                                                                                <span
                                                                                    class="badge <?php echo $staff->pending_apps > 5 ? 'bg-warning-transparent text-warning' : 'bg-info-transparent text-info'; ?> fs-10">
                                                                                    <?php echo $staff->pending_apps; ?>
                                                                                    apps
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                        <?php endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Additional Recommendations Row -->
                                    <div class="row mt-3">
                                        <!-- Cross-sell Opportunities -->
                                        <div class="col-xl-6 col-lg-12">
                                            <div class="alert alert-outline-primary" role="alert">
                                                <div class="d-flex align-items-start">
                                                    <div class="me-2">
                                                        <span class="avatar avatar-sm svg-primary">
                                                            <i class="ti ti-target fs-14"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill">
                                                        <p class="fw-semibold mb-1">Cross-sell Opportunities</p>
                                                        <p class="op-8 mb-1 fs-12">
                                                            <?php 
                                                                $crossSellQuery = "SELECT DISTINCT f.id,
                                                                                  CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                                                                                  fc.name as category_name
                                                                                  FROM farmers f
                                                                                  JOIN users u ON f.user_id = u.id
                                                                                  JOIN farmer_categories fc ON f.category_id = fc.id
                                                                                  JOIN input_credit_applications ica ON f.id = ica.farmer_id
                                                                                  WHERE ica.status = 'approved'
                                                                                  AND f.id NOT IN (SELECT la.farmer_id FROM loan_applications la WHERE la.bank_id = $bankId)
                                                                                  AND f.category_id IN (2, 3) -- Emerging or Commercial farmers
                                                                                  LIMIT 3";
                                                                $crossSell = $app->select_all($crossSellQuery);
                                                                
                                                                if ($crossSell && count($crossSell) > 0) {
                                                                    echo count($crossSell) . " farmers with input credits only - potential for loans";
                                                                } else {
                                                                    echo "No immediate cross-sell opportunities";
                                                                }
                                                                ?>
                                                        </p>

                                                        <?php if ($crossSell && count($crossSell) > 0): ?>
                                                        <div class="mt-2">
                                                            <div class="d-flex flex-wrap gap-1">
                                                                <?php foreach ($crossSell as $farmer): ?>
                                                                <span
                                                                    class="badge bg-primary-transparent text-primary fs-11">
                                                                    <?php echo htmlspecialchars($farmer->farmer_name); ?>
                                                                    (<?php echo $farmer->category_name; ?>)
                                                                </span>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Graduation Candidates -->
                                        <div class="col-xl-6 col-lg-12">
                                            <div class="alert"
                                                style="border: 1px solid rgba(112, 161, 54, 0.2); background-color: rgba(112, 161, 54, 0.05);"
                                                role="alert">
                                                <div class="d-flex align-items-start">
                                                    <div class="me-2">
                                                        <span class="avatar avatar-sm"
                                                            style="background-color: rgba(112, 161, 54, 0.1); color: #70A136;">
                                                            <i class="ti ti-arrow-up fs-14"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill">
                                                        <p class="fw-semibold mb-1">Graduation Candidates</p>
                                                        <p class="op-8 mb-1 fs-12">
                                                            <?php 
                                                              $graduationQuery = "SELECT f.id,
                                                                                 CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                                                           SUM(fm.size) as total_size,
                                                           fc.name as current_category
                                                           FROM farmers f
                                                           JOIN users u ON f.user_id = u.id
                                                           JOIN farmer_categories fc ON f.category_id = fc.id
                                                           JOIN farms fm ON f.id = fm.farmer_id
                                                           WHERE f.category_id = 1 -- Smallholder farmers
                                                           GROUP BY f.id, u.first_name, u.last_name, fc.name
                                                           HAVING total_size >= 5
                                                           LIMIT 3";
                                        $graduation = $app->select_all($graduationQuery);
                                        
                                        if ($graduation && count($graduation) > 0) {
                                            echo count($graduation) . " smallholder farmers ready to become emerging farmers";
                                        } else {
                                            echo "No graduation candidates at this time";
                                        }
                                        ?>
                                                        </p>

                                                        <?php if ($graduation && count($graduation) > 0): ?>
                                                        <div class="mt-2">
                                                            <div class="d-flex flex-wrap gap-1">
                                                                <?php foreach ($graduation as $farmer): ?>
                                                                <span class="badge fs-11"
                                                                    style="background-color: rgba(112, 161, 54, 0.1); color: #70A136; border: 1px solid rgba(112, 161, 54, 0.3);">
                                                                    <?php echo htmlspecialchars($farmer->farmer_name); ?>
                                                                    (<?php echo $farmer->total_size; ?> acres)
                                                                </span>
                                                                <?php endforeach; ?>
                                                            </div>
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
                <div class="row">
                    <!-- Loan Distribution Graph -->
                    <div class="col-xl-8">
                        <div class="card custom-card">
                            <div class="card-header justify-content-between">
                                <div class="card-title">
                                    <i class="ti ti-chart-line me-2" style="color: #70A136;"></i>
                                    Loan Disbursement Trends
                                </div>
                            </div>
                            <div class="card-body">
                                <?php include "graphs/loan-distribution.php" ?>
                            </div>
                        </div>
                    </div>
                    <!-- Loan Statistics Card -->
                    <div class="col-xl-4">
                        <div class="card custom-card">
                            <div class="card-header">
                                <div class="card-title">
                                    <i class="ti ti-report-money me-2" style="color: #4A220F;"></i>
                                    Loan Portfolio Statistics
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    <!-- Total Loans Disbursed -->
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <span class="avatar avatar-sm avatar-rounded bg-primary">
                                                    <i class="ti ti-wallet fs-16"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <p class="mb-0">Total Loans Disbursed</p>
                                                <span class="text-muted fs-12">
                                                    <?php 
                                                       $totalLoansQuery = "SELECT COUNT(*) as count, SUM(approved_amount) as total_amount 
                                                                          FROM approved_loans al
                                                                          WHERE al.bank_id = $bankId";
                                                       $totalLoans = $app->select_one($totalLoansQuery);
                                                       echo number_format($totalLoans->count) . ' Loans (KES ' . number_format($totalLoans->total_amount ?? 0, 0) . ')';
                                                       ?>
                                                </span>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-primary-transparent">
                                                    <?php 
                                                       $thisMonthLoansQuery = "SELECT COUNT(*) as count FROM approved_loans al
                                                                              WHERE al.bank_id = $bankId 
                                                                              AND MONTH(disbursement_date) = MONTH(NOW())
                                                                              AND YEAR(disbursement_date) = YEAR(NOW())";
                                                       $thisMonthLoans = $app->select_one($thisMonthLoansQuery);
                                                       echo number_format($thisMonthLoans->count) . ' This Month';
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

                                    <!-- Active Loans -->
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <span class="avatar avatar-sm avatar-rounded bg-success">
                                                    <i class="ti ti-check-circle fs-16"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <p class="mb-0">Active Loans</p>
                                                <span class="text-muted fs-12">
                                                    <?php 
                                                       $activeLoansQuery = "SELECT COUNT(*) as count, SUM(remaining_balance) as outstanding 
                                                                           FROM approved_loans al
                                                                           WHERE al.bank_id = $bankId AND al.status = 'active'";
                                                       $activeLoans = $app->select_one($activeLoansQuery);
                                                       echo number_format($activeLoans->count) . ' Active (KES ' . number_format($activeLoans->outstanding ?? 0, 0) . ' Outstanding)';
                                                       ?>
                                                </span>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-success-transparent">
                                                    <?php 
                                                       $percentActive = ($totalLoans->count > 0) ? 
                                                           round(($activeLoans->count / $totalLoans->count) * 100) : 0;
                                                       echo $percentActive . '% Active';
                                                       ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="progress mt-2" style="height: 5px;">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: <?php echo $percentActive; ?>%"
                                                aria-valuenow="<?php echo $percentActive; ?>" aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Completed Loans -->
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <span class="avatar avatar-sm avatar-rounded"
                                                    style="background-color: rgba(112, 161, 54, 0.1); color: #70A136;">
                                                    <i class="ti ti-circle-check fs-16"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <p class="mb-0">Completed Loans</p>
                                                <span class="text-muted fs-12">
                                                    <?php 
                                                     $completedLoansQuery = "SELECT COUNT(*) as count, SUM(approved_amount) as total_repaid 
                                                                            FROM approved_loans al
                                                                            WHERE al.bank_id = $bankId AND al.status = 'completed'";
                                                     $completedLoans = $app->select_one($completedLoansQuery);
                                                     echo number_format($completedLoans->count) . ' Completed (KES ' . number_format($completedLoans->total_repaid ?? 0, 0) . ' Repaid)';
                                                     ?>
                                                </span>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge"
                                                    style="background-color: rgba(112, 161, 54, 0.1); color: #70A136;">
                                                    <?php 
                                                     $percentCompleted = ($totalLoans->count > 0) ? 
                                                         round(($completedLoans->count / $totalLoans->count) * 100) : 0;
                                                     echo $percentCompleted . '% Repaid';
                                                     ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="progress mt-2" style="height: 5px;">
                                            <div class="progress-bar" style="background-color: #70A136;"
                                                role="progressbar" style="width: <?php echo $percentCompleted; ?>%"
                                                aria-valuenow="<?php echo $percentCompleted; ?>" aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Monthly Revenue -->
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <span class="avatar avatar-sm avatar-rounded bg-warning">
                                                    <i class="ti ti-trending-up fs-16"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <p class="mb-0">Monthly Collections</p>
                                                <span class="text-muted fs-12">
                                                    <?php 
                                                       $monthlyCollectionsQuery = "SELECT SUM(amount) as collections 
                                                                                  FROM loan_repayments lr
                                                                                  JOIN approved_loans al ON lr.approved_loan_id = al.id
                                                                                  WHERE al.bank_id = $bankId 
                                                                                  AND MONTH(lr.payment_date) = MONTH(NOW())
                                                                                  AND YEAR(lr.payment_date) = YEAR(NOW())";
                                                       $monthlyCollections = $app->select_one($monthlyCollectionsQuery);
                                                       
                                                       $lastMonthCollectionsQuery = "SELECT SUM(amount) as collections 
                                                                                    FROM loan_repayments lr
                                                                                    JOIN approved_loans al ON lr.approved_loan_id = al.id
                                                                                    WHERE al.bank_id = $bankId 
                                                                                    AND MONTH(lr.payment_date) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))
                                                                                    AND YEAR(lr.payment_date) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH))";
                                                       $lastMonthCollections = $app->select_one($lastMonthCollectionsQuery);
                                                       
                                                       echo 'KES ' . number_format($monthlyCollections->collections ?? 0, 0) . ' This Month';
                                                       ?>
                                                </span>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-warning-transparent">
                                                    <?php 
                                                       $growthRate = 0;
                                                       if (($lastMonthCollections->collections ?? 0) > 0) {
                                                           $growthRate = ((($monthlyCollections->collections ?? 0) - ($lastMonthCollections->collections ?? 0)) / ($lastMonthCollections->collections ?? 0)) * 100;
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

                                    <!-- Portfolio Health -->
                                    <div class="list-group-item bg-light">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <span class="avatar avatar-sm avatar-rounded"
                                                    style="background-color: rgba(74, 34, 15, 0.1); color: #4A220F;">
                                                    <i class="ti ti-shield-check fs-16"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <p class="mb-0 fw-semibold">Portfolio Health Score</p>
                                                <span class="text-muted fs-12">
                                                    <?php 
                                                       $defaultedLoansQuery = "SELECT COUNT(*) as count FROM approved_loans al
                                                                              WHERE al.bank_id = $bankId AND al.status = 'defaulted'";
                                                       $defaultedLoans = $app->select_one($defaultedLoansQuery);
                                                       
                                                       $healthScore = 100;
                                                       if ($totalLoans->count > 0) {
                                                           $defaultRate = ($defaultedLoans->count / $totalLoans->count) * 100;
                                                           $healthScore = max(0, 100 - ($defaultRate * 10)); // Simplified scoring
                                                       }
                                                       
                                                       echo 'Score: ' . number_format($healthScore, 0) . '/100 (' . $defaultedLoans->count . ' Defaults)';
                                                       ?>
                                                </span>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge"
                                                    style="background-color: rgba(74, 34, 15, 0.1); color: #4A220F;">
                                                    <?php 
                                                       if ($healthScore >= 80) echo 'Excellent';
                                                       elseif ($healthScore >= 60) echo 'Good';
                                                       elseif ($healthScore >= 40) echo 'Fair';
                                                       else echo 'Needs Attention';
                                                       ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="progress mt-2" style="height: 5px;">
                                            <div class="progress-bar" style="background-color: #4A220F;"
                                                role="progressbar" style="width: <?php echo $healthScore; ?>%"
                                                aria-valuenow="<?php echo $healthScore; ?>" aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- loan insights -->
                <div class="row">
                    <!-- Loan Type Distribution and Recommendations -->
                    <div class="col-xl-12">
                        <div class="row">
                            <!-- Loan Type Distribution Card -->
                            <div class="col-xl-8">
                                <div class="card custom-card">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <i class="ti ti-chart-bar me-2" style="color: #70A136;"></i>
                                            Loan Type Distribution
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="list-group list-group-flush">
                                            <?php
                                                       // Get top loan types by disbursement count
                                                       $loanTypesQuery = "SELECT 
                                                                           lt.name, 
                                                                           COUNT(al.id) as loan_count,
                                                                           SUM(al.approved_amount) as total_amount,
                                                                           AVG(al.approved_amount) as avg_amount,
                                                                           SUM(al.remaining_balance) as outstanding
                                                                        FROM approved_loans al
                                                                        JOIN loan_applications la ON al.loan_application_id = la.id
                                                                        JOIN loan_types lt ON la.loan_type_id = lt.id
                                                                        WHERE la.bank_id = $bankId
                                                                        GROUP BY lt.id, lt.name
                                                                        ORDER BY loan_count DESC
                                                                        LIMIT 5";
                                                       $loanTypes = $app->select_all($loanTypesQuery);
                                                       
                                                       // Get total count for percentage calculation
                                                       $totalLoansQuery = "SELECT COUNT(*) as total FROM approved_loans al
                                                                          JOIN loan_applications la ON al.loan_application_id = la.id
                                                                          WHERE la.bank_id = $bankId";
                                                       $totalLoansCount = $app->select_one($totalLoansQuery);
                                                       $total = $totalLoansCount->total;
                                                       
                                                       // Array of colors for different loan types
                                                       $colors = ['primary', 'success', 'warning', 'danger', 'info', 'purple'];
                                                       
                                                       $counter = 0;
                                                       if ($loanTypes && count($loanTypes) > 0) {
                                                           foreach ($loanTypes as $loanType) {
                                                               $percentage = ($total > 0) ? round(($loanType->loan_count / $total) * 100) : 0;
                                                               $color = $colors[$counter % count($colors)];
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
                                                        <p class="mb-0"><?php echo htmlspecialchars($loanType->name); ?>
                                                        </p>
                                                        <span class="text-muted fs-12">
                                                            <?php echo number_format($loanType->loan_count); ?> Loans
                                                            (Avg: KES
                                                            <?php echo number_format($loanType->avg_amount, 0); ?>)
                                                        </span>
                                                    </div>
                                                    <div class="text-end">
                                                        <div class="mb-1">
                                                            <span class="badge bg-<?php echo $color; ?>-transparent">
                                                                KES
                                                                <?php echo number_format($loanType->total_amount, 0); ?>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <small class="text-muted">Outstanding: KES
                                                                <?php echo number_format($loanType->outstanding, 0); ?></small>
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
                                                    <p class="text-muted mb-0">No loan data available</p>
                                                </div>
                                            </div>
                                            <?php
                                                }
                                                ?>

                                            <!-- Total Portfolio Summary -->
                                            <div class="list-group-item bg-light">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2">
                                                        <span class="avatar avatar-sm avatar-rounded bg-secondary">
                                                            <i class="ti ti-chart-pie fs-16"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill">
                                                        <p class="mb-0 fw-semibold">Total Portfolio Value</p>
                                                        <span class="text-muted fs-12">
                                                            <?php 
                                                             $totalPortfolioQuery = "SELECT SUM(al.approved_amount) as value FROM approved_loans al
                                                                                   JOIN loan_applications la ON al.loan_application_id = la.id
                                                                                   WHERE la.bank_id = $bankId";
                                                             $totalPortfolio = $app->select_one($totalPortfolioQuery);
                                                             echo 'KES ' . number_format($totalPortfolio->value ?? 0, 0) . ' Across All Types';
                                                             ?>
                                                        </span>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="badge bg-secondary-transparent">
                                                            <?php echo number_format($total); ?> Total Loans
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Loan Recommendations -->
                            <div class="col-xl-4">
                                <div class="card custom-card">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <i class="ti ti-lightbulb me-2" style="color: #4A220F;"></i>
                                            Loan Insights
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <!-- Popular Loan Type -->
                                        <div class="alert alert-outline-success mb-3" role="alert">
                                            <div class="d-flex align-items-start">
                                                <div class="me-2">
                                                    <span class="avatar avatar-sm svg-success">
                                                        <i class="ti ti-trending-up fs-14"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill">
                                                    <p class="fw-semibold mb-1">Most Popular Loan</p>
                                                    <p class="op-8 mb-1 fs-12">
                                                        <?php 
                                                             if ($loanTypes && count($loanTypes) > 0) {
                                                                 $topLoan = $loanTypes[0];
                                                                 echo htmlspecialchars($topLoan->name) . " - " . $topLoan->loan_count . " loans disbursed";
                                                             } else {
                                                                 echo "No data available";
                                                             }
                                                             ?>
                                                    </p>
                                                    <small class="text-success fw-semibold">Consider promoting this
                                                        product</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- High-Value Opportunity -->
                                        <div class="alert alert-outline-warning mb-3" role="alert">
                                            <div class="d-flex align-items-start">
                                                <div class="me-2">
                                                    <span class="avatar avatar-sm svg-warning">
                                                        <i class="ti ti-target fs-14"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill">
                                                    <p class="fw-semibold mb-1">High-Value Opportunity</p>
                                                    <p class="op-8 mb-1 fs-12">
                                                        <?php 
                                                             $commercialFarmersQuery = "SELECT COUNT(*) as count FROM farmers f
                                                                                      JOIN farmer_categories fc ON f.category_id = fc.id
                                                                                      WHERE fc.name = 'Commercial Farmer'
                                                                                      AND f.id NOT IN (SELECT la.farmer_id FROM loan_applications la WHERE la.bank_id = $bankId)";
                                                             $commercialFarmers = $app->select_one($commercialFarmersQuery);
                                                             echo ($commercialFarmers->count ?? 0) . " commercial farmers without loans";
                                                             ?>
                                                    </p>
                                                    <small class="text-warning fw-semibold">Target for premium loan
                                                        products</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Seasonal Trend -->
                                        <div class="alert alert-outline-info mb-3" role="alert">
                                            <div class="d-flex align-items-start">
                                                <div class="me-2">
                                                    <span class="avatar avatar-sm svg-info">
                                                        <i class="ti ti-calendar fs-14"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill">
                                                    <p class="fw-semibold mb-1">Seasonal Trend</p>
                                                    <p class="op-8 mb-1 fs-12">
                                                        <?php 
                                                              $currentMonth = date('n');
                                                              $peakMonths = [3, 4, 10, 11]; // March, April, October, November (planting/harvest seasons)
                                                              
                                                              if (in_array($currentMonth, $peakMonths)) {
                                                                  echo "Peak season for agricultural loans - expect 40% increase in applications";
                                                              } else {
                                                                  $nextPeak = null;
                                                                  foreach ($peakMonths as $month) {
                                                                      if ($month > $currentMonth) {
                                                                          $nextPeak = $month;
                                                                          break;
                                                                      }
                                                                  }
                                                                  if (!$nextPeak) $nextPeak = $peakMonths[0] + 12; // Next year
                                                                  $monthsToNext = $nextPeak - $currentMonth;
                                                                  echo "Next peak season in " . $monthsToNext . " months - prepare loan products";
                                                              }
                                                              ?>
                                                    </p>
                                                    <small class="text-info fw-semibold">Adjust marketing strategy
                                                        accordingly</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Portfolio Diversification -->
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
                                                    <p class="fw-semibold mb-1">Portfolio Health</p>
                                                    <p class="op-8 mb-1 fs-12">
                                                        <?php 
                                                          $typeCount = count($loanTypes ?? []);
                                                          if ($typeCount >= 4) {
                                                              echo "Well diversified with " . $typeCount . " loan products";
                                                          } elseif ($typeCount >= 2) {
                                                              echo "Moderately diversified - consider adding " . (4 - $typeCount) . " more products";
                                                          } else {
                                                              echo "Limited diversification - high concentration risk";
                                                          }
                                                          ?>
                                                    </p>
                                                    <small class="fw-semibold" style="color: #4A220F;">
                                                        <?php 
                                                          if ($typeCount >= 4) echo "Maintain current strategy";
                                                          else echo "Expand product portfolio";
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
                <!-- farmer analytics -->
                <div class="row">
                    <div class="row mt-4">
                        <!-- Farmer Segmentation Analytics -->
                        <div class="col-xl-8">
                            <div class="card custom-card">
                                <div class="card-header justify-content-between">
                                    <div class="card-title">
                                        <i class="ti ti-users me-2" style="color: #70A136;"></i>
                                        Farmer Portfolio Analytics
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="list-group list-group-flush">
                                        <?php
                                            // Get farmer segments with loan data
                                            $farmerSegmentsQuery = "SELECT 
                                                                    fc.name as category_name,
                                                                    COUNT(DISTINCT f.id) as farmer_count,
                                                                    COUNT(DISTINCT la.id) as loan_applications,
                                                                    COUNT(DISTINCT CASE WHEN al.status = 'active' THEN al.id END) as active_loans,
                                                                    SUM(CASE WHEN al.status = 'active' THEN al.remaining_balance ELSE 0 END) as outstanding_amount,
                                                                    AVG(al.approved_amount) as avg_loan_size,
                                                                    SUM(al.approved_amount) as total_disbursed
                                                                  FROM farmer_categories fc
                                                                  LEFT JOIN farmers f ON fc.id = f.category_id
                                                                  LEFT JOIN loan_applications la ON f.id = la.farmer_id AND la.bank_id = $bankId
                                                                  LEFT JOIN approved_loans al ON la.id = al.loan_application_id
                                                                  GROUP BY fc.id, fc.name
                                                                  ORDER BY farmer_count DESC";
                                            $farmerSegments = $app->select_all($farmerSegmentsQuery);
                                            
                                            // Get total farmers for percentage calculation
                                            $totalFarmersQuery = "SELECT COUNT(DISTINCT f.id) as total FROM farmers f
                                                                 JOIN loan_applications la ON f.id = la.farmer_id
                                                                 WHERE la.bank_id = $bankId";
                                            $totalFarmers = $app->select_one($totalFarmersQuery);
                                            $total = $totalFarmers->total ?? 1;
                                            
                                            // Array of colors for different segments
                                            $colors = ['primary', 'success', 'warning', 'danger', 'info', 'purple'];
                                            
                                            $counter = 0;
                                            if ($farmerSegments && count($farmerSegments) > 0) {
                                                foreach ($farmerSegments as $segment) {
                                                    if ($segment->farmer_count > 0) {
                                                        $percentage = round(($segment->farmer_count / $total) * 100);
                                                        $color = $colors[$counter % count($colors)];
                                                        $penetrationRate = $segment->farmer_count > 0 ? round(($segment->loan_applications / $segment->farmer_count) * 100) : 0;
                                                        ?>
                                        <div class="list-group-item">
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">
                                                    <span
                                                        class="avatar avatar-sm avatar-rounded bg-<?php echo $color; ?>">
                                                        <i class="ti ti-user-group fs-16"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <p class="mb-0 fw-semibold">
                                                                <?php echo htmlspecialchars($segment->category_name); ?>
                                                            </p>
                                                            <span class="text-muted fs-12">
                                                                <?php echo number_format($segment->farmer_count); ?>
                                                                Farmers •
                                                                <?php echo number_format($segment->loan_applications); ?>
                                                                Applications
                                                            </span>
                                                        </div>
                                                        <div class="text-end">
                                                            <span
                                                                class="badge bg-<?php echo $color; ?>-transparent mb-1">
                                                                <?php echo $penetrationRate; ?>% Penetration
                                                            </span>
                                                            <div>
                                                                <small class="text-muted">
                                                                    KES
                                                                    <?php echo number_format($segment->total_disbursed ?? 0, 0); ?>
                                                                    Total
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-2">
                                                        <div class="row g-2">
                                                            <div class="col-4">
                                                                <div class="text-center">
                                                                    <div
                                                                        class="fs-13 fw-semibold text-<?php echo $color; ?>">
                                                                        <?php echo number_format($segment->active_loans); ?>
                                                                    </div>
                                                                    <div class="fs-10 text-muted">Active Loans</div>
                                                                </div>
                                                            </div>
                                                            <div class="col-4">
                                                                <div class="text-center">
                                                                    <div
                                                                        class="fs-13 fw-semibold text-<?php echo $color; ?>">
                                                                        KES
                                                                        <?php echo number_format($segment->avg_loan_size ?? 0, 0); ?>
                                                                    </div>
                                                                    <div class="fs-10 text-muted">Avg Loan</div>
                                                                </div>
                                                            </div>
                                                            <div class="col-4">
                                                                <div class="text-center">
                                                                    <div
                                                                        class="fs-13 fw-semibold text-<?php echo $color; ?>">
                                                                        KES
                                                                        <?php echo number_format($segment->outstanding_amount ?? 0, 0); ?>
                                                                    </div>
                                                                    <div class="fs-10 text-muted">Outstanding</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="progress mt-2" style="height: 5px;">
                                                <div class="progress-bar bg-<?php echo $color; ?>" role="progressbar"
                                                    style="width: <?php echo $percentage; ?>%"
                                                    aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                                            $counter++;
                                                        }
                                                    }
                                                } else {
                                                    ?>
                                        <div class="list-group-item">
                                            <div class="text-center">
                                                <p class="text-muted mb-0">No farmer data available</p>
                                            </div>
                                        </div>
                                        <?php
                                                }
                                                ?>

                                        <!-- Geographic Distribution -->
                                        <div class="list-group-item bg-light">
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">
                                                    <span class="avatar avatar-sm avatar-rounded bg-secondary">
                                                        <i class="ti ti-map-pin fs-16"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill">
                                                    <p class="mb-0 fw-semibold">Geographic Coverage</p>
                                                    <span class="text-muted fs-12">
                                                        <?php 
                                                       $locationsQuery = "SELECT COUNT(DISTINCT u.location) as locations 
                                                                        FROM farmers f
                                                                        JOIN users u ON f.user_id = u.id
                                                                        JOIN loan_applications la ON f.id = la.farmer_id
                                                                        WHERE la.bank_id = $bankId AND u.location IS NOT NULL";
                                                       $locations = $app->select_one($locationsQuery);
                                                       echo ($locations->locations ?? 0) . ' Locations Served • Active in Multiple Counties';
                                                       ?>
                                                    </span>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-secondary-transparent">
                                                        <?php echo number_format($total); ?> Total Clients
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Risk & Performance Metrics -->
                        <div class="col-xl-4">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">
                                        <i class="ti ti-shield-check me-2" style="color: #4A220F;"></i>
                                        Risk & Performance
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="list-group list-group-flush">
                                        <!-- Portfolio Quality -->
                                        <div class="list-group-item">
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">
                                                    <span class="avatar avatar-sm avatar-rounded bg-success">
                                                        <i class="ti ti-shield-check fs-16"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill">
                                                    <p class="mb-0">Portfolio Quality</p>
                                                    <span class="text-muted fs-12">
                                                        <?php 
                                                         $qualityQuery = "SELECT 
                                                                         COUNT(CASE WHEN al.status = 'active' THEN 1 END) as performing,
                                                                         COUNT(CASE WHEN al.status = 'defaulted' THEN 1 END) as defaults,
                                                                         COUNT(*) as total_loans
                                                                        FROM approved_loans al
                                                                        JOIN loan_applications la ON al.loan_application_id = la.id
                                                                        WHERE la.bank_id = $bankId";
                                                         $quality = $app->select_one($qualityQuery);
                                                         $performingRate = $quality->total_loans > 0 ? round(($quality->performing / $quality->total_loans) * 100) : 0;
                                                         echo $performingRate . '% Performing Loans (' . number_format($quality->performing) . '/' . number_format($quality->total_loans) . ')';
                                                         ?>
                                                    </span>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-success-transparent">
                                                        Healthy
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="progress mt-2" style="height: 5px;">
                                                <div class="progress-bar bg-success" role="progressbar"
                                                    style="width: <?php echo $performingRate; ?>%"
                                                    aria-valuenow="<?php echo $performingRate; ?>" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Average Processing Time -->
                                        <div class="list-group-item">
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">
                                                    <span class="avatar avatar-sm avatar-rounded bg-info">
                                                        <i class="ti ti-clock fs-16"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill">
                                                    <p class="mb-0">Processing Efficiency</p>
                                                    <span class="text-muted fs-12">
                                                        <?php 
                                                           $processingQuery = "SELECT AVG(DATEDIFF(review_date, application_date)) as avg_days
                                                                             FROM loan_applications 
                                                                             WHERE bank_id = $bankId AND review_date IS NOT NULL
                                                                             AND application_date >= DATE_SUB(NOW(), INTERVAL 3 MONTH)";
                                                           $processing = $app->select_one($processingQuery);
                                                           $avgDays = $processing->avg_days ?? 0;
                                                           echo number_format($avgDays, 1) . ' days average processing time';
                                                           ?>
                                                    </span>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-info-transparent">
                                                        <?php 
                                                           if ($avgDays <= 3) echo 'Excellent';
                                                           elseif ($avgDays <= 5) echo 'Good';
                                                           elseif ($avgDays <= 7) echo 'Fair';
                                                           else echo 'Slow';
                                                           ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="progress mt-2" style="height: 5px;">
                                                <div class="progress-bar bg-info" role="progressbar"
                                                    style="width: <?php echo min(100, max(0, 100 - ($avgDays * 10))); ?>%"
                                                    aria-valuenow="<?php echo min(100, max(0, 100 - ($avgDays * 10))); ?>"
                                                    aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Customer Lifetime Value -->
                                        <div class="list-group-item">
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">
                                                    <span class="avatar avatar-sm avatar-rounded"
                                                        style="background-color: rgba(112, 161, 54, 0.1); color: #70A136;">
                                                        <i class="ti ti-trending-up fs-16"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill">
                                                    <p class="mb-0">Customer Value</p>
                                                    <span class="text-muted fs-12">
                                                        <?php 
                                    $clvQuery = "SELECT AVG(total_loans * avg_amount) as avg_clv FROM (
                                                SELECT la.farmer_id, 
                                                       COUNT(al.id) as total_loans,
                                                       AVG(al.approved_amount) as avg_amount
                                                FROM loan_applications la
                                                JOIN approved_loans al ON la.id = al.loan_application_id
                                                WHERE la.bank_id = $bankId
                                                GROUP BY la.farmer_id
                                               ) as farmer_clv";
                                    $clv = $app->select_one($clvQuery);
                                    echo 'KES ' . number_format($clv->avg_clv ?? 0, 0) . ' average lifetime value';
                                    ?>
                                                    </span>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge"
                                                        style="background-color: rgba(112, 161, 54, 0.1); color: #70A136;">
                                                        Growing
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="progress mt-2" style="height: 5px;">
                                                <div class="progress-bar" style="background-color: #70A136;"
                                                    role="progressbar" style="width: 75%" aria-valuenow="75"
                                                    aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Market Penetration -->
                                        <div class="list-group-item bg-light">
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">
                                                    <span class="avatar avatar-sm avatar-rounded"
                                                        style="background-color: rgba(74, 34, 15, 0.1); color: #4A220F;">
                                                        <i class="ti ti-target fs-16"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill">
                                                    <p class="mb-0 fw-semibold">Market Penetration</p>
                                                    <span class="text-muted fs-12">
                                                        <?php 
                                    $penetrationQuery = "SELECT 
                                                        (SELECT COUNT(DISTINCT la.farmer_id) FROM loan_applications la WHERE la.bank_id = $bankId) as bank_farmers,
                                                        (SELECT COUNT(*) FROM farmers) as total_farmers";
                                    $penetration = $app->select_one($penetrationQuery);
                                    $penetrationRate = $penetration->total_farmers > 0 ? round(($penetration->bank_farmers / $penetration->total_farmers) * 100, 1) : 0;
                                    echo $penetrationRate . '% of total farmer market captured';
                                    ?>
                                                    </span>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge"
                                                        style="background-color: rgba(74, 34, 15, 0.1); color: #4A220F;">
                                                        <?php echo number_format($penetration->bank_farmers); ?>/<?php echo number_format($penetration->total_farmers); ?>
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
                <!-- recommendations -->
                <!-- Farmer Analytics Recommendations -->
                <div class="col-xl-12 mt-4">
                    <div class="card custom-card">
                        <div class="card-header justify-content-between">
                            <div class="card-title">
                                <i class="ti ti-brain me-2" style="color: #70A136;"></i>
                                Strategic Farmer Insights & Recommendations
                            </div>

                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Segment Growth Opportunities -->
                                <div class="col-xl-4 col-lg-6 col-md-12">
                                    <div class="alert alert-outline-success" role="alert">
                                        <div class="d-flex align-items-start">
                                            <div class="me-2">
                                                <span class="avatar avatar-sm svg-success">
                                                    <i class="ti ti-trending-up fs-14"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <p class="fw-semibold mb-1">Segment Growth Opportunity</p>
                                                <p class="op-8 mb-1 fs-12">
                                                    <?php 
                                                      $emergingFarmersQuery = "SELECT COUNT(*) as count FROM farmers f
                                                                             JOIN farmer_categories fc ON f.category_id = fc.id
                                                                             WHERE fc.name = 'Emerging Farmer'
                                                                             AND f.id NOT IN (SELECT la.farmer_id FROM loan_applications la WHERE la.bank_id = $bankId)";
                                                      $emergingFarmers = $app->select_one($emergingFarmersQuery);
                                                      echo ($emergingFarmers->count ?? 0) . " emerging farmers without loans - highest growth potential segment";
                                                      ?>
                                                </p>
                                                <div class="mt-2">
                                                    <div class="d-flex flex-wrap gap-1">
                                                        <?php 
                                                          $topEmergingQuery = "SELECT CONCAT(u.first_name, ' ', u.last_name) as name, u.location
                                                                             FROM farmers f
                                                                             JOIN users u ON f.user_id = u.id
                                                                             JOIN farmer_categories fc ON f.category_id = fc.id
                                                                             WHERE fc.name = 'Emerging Farmer'
                                                                             AND f.id NOT IN (SELECT la.farmer_id FROM loan_applications la WHERE la.bank_id = $bankId)
                                                                             ORDER BY f.created_at DESC
                                                                             LIMIT 3";
                                                          $topEmerging = $app->select_all($topEmergingQuery);
                                                          if ($topEmerging) {
                                                              foreach ($topEmerging as $farmer) {
                                                                  echo '<span class="badge bg-success-transparent fs-10">' . htmlspecialchars($farmer->name) . '</span>';
                                                              }
                                                          }
                                                          ?>
                                                    </div>
                                                </div>
                                                <small class="text-success fw-semibold mt-1 d-block">Target for
                                                    mid-size
                                                    loan products (KES 100K-500K)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Geographic Expansion -->
                                <div class="col-xl-4 col-lg-6 col-md-12">
                                    <div class="alert alert-outline-warning" role="alert">
                                        <div class="d-flex align-items-start">
                                            <div class="me-2">
                                                <span class="avatar avatar-sm svg-warning">
                                                    <i class="ti ti-map-pin fs-14"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <p class="fw-semibold mb-1">Geographic Expansion</p>
                                                <p class="op-8 mb-1 fs-12">
                                                    <?php 
                                                       $untappedLocationsQuery = "SELECT u.location, COUNT(*) as farmer_count
                                                                                FROM farmers f
                                                                                JOIN users u ON f.user_id = u.id
                                                                                WHERE u.location IS NOT NULL
                                                                                AND u.location NOT IN (
                                                                                    SELECT DISTINCT u2.location FROM farmers f2
                                                                                    JOIN users u2 ON f2.user_id = u2.id
                                                                                    JOIN loan_applications la ON f2.id = la.farmer_id
                                                                                    WHERE la.bank_id = $bankId AND u2.location IS NOT NULL
                                                                                )
                                                                                GROUP BY u.location
                                                                                ORDER BY farmer_count DESC
                                                                                LIMIT 3";
                                                       $untappedLocations = $app->select_all($untappedLocationsQuery);
                                                       
                                                       if ($untappedLocations && count($untappedLocations) > 0) {
                                                           echo count($untappedLocations) . " new locations with high farmer concentration";
                                                       } else {
                                                           echo "Good coverage across existing markets";
                                                       }
                                                       ?>
                                                </p>
                                                <div class="mt-2">
                                                    <div class="d-flex flex-wrap gap-1">
                                                        <?php 
                                                           if ($untappedLocations) {
                                                               foreach ($untappedLocations as $location) {
                                                                   echo '<span class="badge bg-warning-transparent fs-10">' . htmlspecialchars($location->location) . ' (' . $location->farmer_count . ')</span>';
                                                               }
                                                           }
                                                           ?>
                                                    </div>
                                                </div>
                                                <small class="text-warning fw-semibold mt-1 d-block">
                                                    <?php 
                                                       if ($untappedLocations && count($untappedLocations) > 0) {
                                                           echo "Consider opening service points in these areas";
                                                       } else {
                                                           echo "Focus on deepening penetration in existing markets";
                                                       }
                                                       ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Risk Concentration -->
                                <div class="col-xl-4 col-lg-6 col-md-12">
                                    <div class="alert alert-outline-info" role="alert">
                                        <div class="d-flex align-items-start">
                                            <div class="me-2">
                                                <span class="avatar avatar-sm svg-info">
                                                    <i class="ti ti-shield-alert fs-14"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <p class="fw-semibold mb-1">Portfolio Concentration Risk</p>
                                                <p class="op-8 mb-1 fs-12">
                                                    <?php 
                                                       $concentrationQuery = "SELECT 
                                                                             fc.name,
                                                                             COUNT(*) as loan_count,
                                                                             SUM(al.remaining_balance) as exposure
                                                                            FROM approved_loans al
                                                                            JOIN loan_applications la ON al.loan_application_id = la.id
                                                                            JOIN farmers f ON la.farmer_id = f.id
                                                                            JOIN farmer_categories fc ON f.category_id = fc.id
                                                                            WHERE la.bank_id = $bankId AND al.status = 'active'
                                                                            GROUP BY fc.id, fc.name
                                                                            ORDER BY exposure DESC
                                                                            LIMIT 1";
                                                       $concentration = $app->select_one($concentrationQuery);
                                                       
                                                       if ($concentration) {
                                                           $totalExposureQuery = "SELECT SUM(remaining_balance) as total FROM approved_loans al
                                                                                JOIN loan_applications la ON al.loan_application_id = la.id
                                                                                WHERE la.bank_id = $bankId AND al.status = 'active'";
                                                           $totalExposure = $app->select_one($totalExposureQuery);
                                                           $concentrationRate = $totalExposure->total > 0 ? round(($concentration->exposure / $totalExposure->total) * 100) : 0;
                                                           echo $concentrationRate . "% exposure in " . $concentration->name . " segment";
                                                       } else {
                                                           echo "Well-diversified portfolio across segments";
                                                       }
                                                       ?>
                                                </p>
                                                <div class="mt-2">
                                                    <div class="progress" style="height: 8px;">
                                                        <div class="progress-bar bg-info" role="progressbar"
                                                            style="width: <?php echo $concentrationRate ?? 0; ?>%"
                                                            aria-valuenow="<?php echo $concentrationRate ?? 0; ?>"
                                                            aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                </div>
                                                <small class="text-info fw-semibold mt-1 d-block">
                                                    <?php 
                                                       if (($concentrationRate ?? 0) > 60) {
                                                           echo "High concentration - diversify into other segments";
                                                       } elseif (($concentrationRate ?? 0) > 40) {
                                                           echo "Moderate concentration - monitor closely";
                                                       } else {
                                                           echo "Healthy diversification across segments";
                                                       }
                                                       ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    </di v>
                                </div>

                                <!-- Second Row of Recommendations -->
                                <div class="row mt-3">
                                    <!-- Product Optimization -->
                                    <div class="col-xl-6 col-lg-12">
                                        <div class="alert"
                                            style="border: 1px solid rgba(112, 161, 54, 0.2); background-color: rgba(112, 161, 54, 0.05);"
                                            role="alert">
                                            <div class="d-flex align-items-start">
                                                <div class="me-2">
                                                    <span class="avatar avatar-sm"
                                                        style="background-color: rgba(112, 161, 54, 0.1); color: #70A136;">
                                                        <i class="ti ti-settings fs-14"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill">
                                                    <p class="fw-semibold mb-1">Product Optimization Insights</p>
                                                    <p class="op-8 mb-1 fs-12">
                                                        <?php 
                                                              $avgLoanSizeQuery = "SELECT 
                                                                                  fc.name,
                                                                                  AVG(al.approved_amount) as avg_size,
                                                                                  COUNT(*) as count
                                                                                 FROM approved_loans al
                                                                                 JOIN loan_applications la ON al.loan_application_id = la.id
                                                                                 JOIN farmers f ON la.farmer_id = f.id
                                                                                 JOIN farmer_categories fc ON f.category_id = fc.id
                                                                                 WHERE la.bank_id = $bankId
                                                                                 GROUP BY fc.id, fc.name
                                                                                 ORDER BY avg_size DESC";
                                                              $avgLoanSizes = $app->select_all($avgLoanSizeQuery);
                                                              
                                                              if ($avgLoanSizes && count($avgLoanSizes) > 0) {
                                                                  echo "Commercial farmers average KES " . number_format($avgLoanSizes[0]->avg_size ?? 0, 0) . " - consider premium products";
                                                              } else {
                                                                  echo "Insufficient data for product optimization";
                                                              }
                                                              ?>
                                                    </p>
                                                    <div class="mt-2">
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-borderless mb-0">
                                                                <tbody>
                                                                    <?php 
                                                                          if ($avgLoanSizes) {
                                                                              foreach ($avgLoanSizes as $segment) {
                                                                                  ?>
                                                                    <tr>
                                                                        <td class="ps-0">
                                                                            <span
                                                                                class="fs-12 fw-semibold"><?php echo htmlspecialchars($segment->name); ?></span>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <span class="badge"
                                                                                style="background-color: rgba(112, 161, 54, 0.1); color: #70A136; font-size: 10px;">
                                                                                KES
                                                                                <?php echo number_format($segment->avg_size, 0); ?>
                                                                            </span>
                                                                        </td>
                                                                        <td class="text-end pe-0">
                                                                            <span
                                                                                class="fs-11 text-muted"><?php echo $segment->count; ?>
                                                                                loans</span>
                                                                        </td>
                                                                    </tr>
                                                                    <?php
                                                                              }
                                                                          }
                                                                          ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <small class="fw-semibold mt-1 d-block"
                                                        style="color: #70A136;">Create
                                                        tailored products for each segment's typical loan
                                                        size</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Retention Strategy -->
                                    <div class="col-xl-6 col-lg-12">
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
                                                    <p class="fw-semibold mb-1">Customer Retention Strategy</p>
                                                    <p class="op-8 mb-1 fs-12">
                                                        <?php 
                                                     $repeatCustomersQuery = "SELECT 
                                                                            COUNT(CASE WHEN loan_count > 1 THEN 1 END) as repeat_customers,
                                                                            COUNT(*) as total_customers,
                                                                            AVG(loan_count) as avg_loans_per_customer
                                                                          FROM (
                                                                              SELECT la.farmer_id, COUNT(*) as loan_count
                                                                              FROM loan_applications la
                                                                              WHERE la.bank_id = $bankId
                                                                              GROUP BY la.farmer_id
                                                                          ) as customer_loans";
                                                     $retention = $app->select_one($repeatCustomersQuery);
                                                     
                                                     $retentionRate = $retention->total_customers > 0 ? round(($retention->repeat_customers / $retention->total_customers) * 100) : 0;
                                                     echo $retentionRate . "% repeat customer rate - " . number_format($retention->repeat_customers) . " loyal customers";
                                                     ?>
                                                    </p>
                                                    <div class="mt-2">
                                                        <div class="row g-2">
                                                            <div class="col-6">
                                                                <div class="text-center p-2 rounded"
                                                                    style="background-color: rgba(74, 34, 15, 0.1);">
                                                                    <div class="fs-13 fw-semibold"
                                                                        style="color: #4A220F;">
                                                                        <?php echo number_format($retention->avg_loans_per_customer ?? 0, 1); ?>
                                                                    </div>
                                                                    <div class="fs-10 text-muted">Avg Loans/Customer
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="text-center p-2 rounded"
                                                                    style="background-color: rgba(74, 34, 15, 0.1);">
                                                                    <div class="fs-13 fw-semibold"
                                                                        style="color: #4A220F;">
                                                                        <?php 
                                                                     $loyalCustomersQuery = "SELECT COUNT(*) as count FROM (
                                                                                            SELECT la.farmer_id
                                                                                            FROM loan_applications la
                                                                                            WHERE la.bank_id = $bankId
                                                                                            GROUP BY la.farmer_id
                                                                                            HAVING COUNT(*) >= 3
                                                                                          ) as loyal";
                                                                     $loyal = $app->select_one($loyalCustomersQuery);
                                                                     echo number_format($loyal->count ?? 0);
                                                                     ?>
                                                                    </div>
                                                                    <div class="fs-10 text-muted">Loyal (3+ Loans)
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <small class="fw-semibold mt-1 d-block" style="color: #4A220F;">
                                                        <?php 
                                                     if ($retentionRate > 40) {
                                                         echo "Excellent retention - create VIP program for loyal customers";
                                                     } elseif ($retentionRate > 25) {
                                                         echo "Good retention - implement loyalty rewards program";
                                                     } else {
                                                         echo "Focus on improving customer experience and follow-up";
                                                     }
                                                     ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Action Items Summary -->
                                <div class="row mt-3">
                                    <div class="col-xl-12">
                                        <div class="card bg-light border-0">
                                            <div class="card-header bg-transparent border-0 pb-0">
                                                <h6 class="card-title mb-0">
                                                    <i class="ti ti-checklist me-2" style="color: #70A136;"></i>
                                                    Priority Action Items This Week
                                                </h6>
                                            </div>
                                            <div class="card-body pt-2">
                                                <div class="row">
                                                    <div class="col-xl-3 col-lg-6 col-md-6">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span
                                                                class="avatar avatar-xs bg-primary-transparent rounded-pill me-2">
                                                                <i class="ti ti-number-1 fs-10"></i>
                                                            </span>
                                                            <small class="fw-semibold">Contact
                                                                <?php echo ($emergingFarmers->count ?? 0) > 5 ? '5' : ($emergingFarmers->count ?? 0); ?>
                                                                emerging farmers for loan offers</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-3 col-lg-6 col-md-6">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span
                                                                class="avatar avatar-xs bg-success-transparent rounded-pill me-2">
                                                                <i class="ti ti-number-2 fs-10"></i>
                                                            </span>
                                                            <small class="fw-semibold">Review processing time for
                                                                applications over 7 days</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-3 col-lg-6 col-md-6">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span
                                                                class="avatar avatar-xs bg-warning-transparent rounded-pill me-2">
                                                                <i class="ti ti-number-3 fs-10"></i>
                                                            </span>
                                                            <small class="fw-semibold">Explore new service points in
                                                                untapped locations</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-3 col-lg-6 col-md-6">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span
                                                                class="avatar avatar-xs bg-info-transparent rounded-pill me-2">
                                                                <i class="ti ti-number-4 fs-10"></i>
                                                            </span>
                                                            <small class="fw-semibold">Create loyalty program for
                                                                repeat
                                                                customers</small>
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
                <!-- transactoin summary -->
                <!-- Transaction Analytics Graph -->
                <div class="row">
                    <div class="col-xl-8">
                        <div class="card custom-card">
                            <div class="card-header justify-content-between">
                                <div class="card-title">
                                    <i class="ti ti-arrows-exchange me-2" style="color: #70A136;"></i>
                                    Transaction Flow Analytics
                                </div>
                            </div>
                            <div class="card-body">
                                <?php include "graphs/transaction-flow.php" ?>
                            </div>
                        </div>
                    </div>
                    <!-- Transaction Statistics -->
                    <div class="col-xl-4">
                        <div class="card custom-card">
                            <div class="card-header">
                                <div class="card-title">
                                    <i class="ti ti-receipt me-2" style="color: #4A220F;"></i>
                                    Transaction Metrics
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    <!-- Daily Transaction Volume -->
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <span class="avatar avatar-sm avatar-rounded bg-primary">
                                                    <i class="ti ti-activity fs-16"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <p class="mb-0">Daily Transaction Volume</p>
                                                <span class="text-muted fs-12">
                                                    <?php 
                                               $dailyVolumeQuery = "SELECT 
                                                                   COUNT(*) as count,
                                                                   SUM(amount) as total_amount
                                                                  FROM (
                                                                      SELECT amount, created_at FROM farmer_account_transactions WHERE DATE(created_at) = CURDATE()
                                                                      UNION ALL
                                                                      SELECT amount, created_at FROM bank_account_transactions WHERE DATE(created_at) = CURDATE()
                                                                      UNION ALL
                                                                      SELECT amount, created_at FROM sacco_account_transactions WHERE DATE(created_at) = CURDATE()
                                                                  ) as all_transactions";
                                               $dailyVolume = $app->select_one($dailyVolumeQuery);
                                               echo number_format($dailyVolume->count) . ' Transactions (KES ' . number_format($dailyVolume->total_amount ?? 0, 0) . ')';
                                               ?>
                                                </span>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-primary-transparent">
                                                    <?php 
                                               $hourlyAvgQuery = "SELECT COUNT(*)/24 as avg_hourly FROM (
                                                                 SELECT created_at FROM farmer_account_transactions WHERE DATE(created_at) = CURDATE()
                                                                 UNION ALL
                                                                 SELECT created_at FROM bank_account_transactions WHERE DATE(created_at) = CURDATE()
                                                                 UNION ALL
                                                                 SELECT created_at FROM sacco_account_transactions WHERE DATE(created_at) = CURDATE()
                                                               ) as hourly_txns";
                                               $hourlyAvg = $app->select_one($hourlyAvgQuery);
                                               echo number_format($hourlyAvg->avg_hourly ?? 0, 1) . '/hr';
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

                                    <!-- Payment Processing Success Rate -->
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <span class="avatar avatar-sm avatar-rounded bg-success">
                                                    <i class="ti ti-check-circle fs-16"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <p class="mb-0">Processing Success Rate</p>
                                                <span class="text-muted fs-12">
                                                    <?php 
                                                 // Assuming successful transactions are those with status completion
                                                 $successRateQuery = "SELECT 
                                                                     COUNT(CASE WHEN pd.status = 'paid' THEN 1 END) as successful,
                                                                     COUNT(*) as total
                                                                    FROM produce_deliveries pd
                                                                    WHERE pd.delivery_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                                                 $successRate = $app->select_one($successRateQuery);
                                                 $rate = $successRate->total > 0 ? round(($successRate->successful / $successRate->total) * 100) : 0;
                                                 echo $rate . '% Success Rate (' . number_format($successRate->successful) . '/' . number_format($successRate->total) . ')';
                                                 ?>
                                                </span>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-success-transparent">
                                                    <?php 
                                                 if ($rate >= 95) echo 'Excellent';
                                                 elseif ($rate >= 90) echo 'Good';
                                                 elseif ($rate >= 80) echo 'Fair';
                                                 else echo 'Needs Attention';
                                                 ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="progress mt-2" style="height: 5px;">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: <?php echo $rate; ?>%"
                                                aria-valuenow="<?php echo $rate; ?>" aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Loan Repayments -->
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <span class="avatar avatar-sm avatar-rounded"
                                                    style="background-color: rgba(112, 161, 54, 0.1); color: #70A136;">
                                                    <i class="ti ti-arrow-down-circle fs-16"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <p class="mb-0">Loan Repayments</p>
                                                <span class="text-muted fs-12">
                                                    <?php 
                                                  $repaymentQuery = "SELECT 
                                                                    COUNT(*) as count,
                                                                    SUM(amount) as total_amount
                                                                   FROM loan_repayments lr
                                                                   JOIN approved_loans al ON lr.approved_loan_id = al.id
                                                                   WHERE al.bank_id = $bankId
                                                                   AND DATE(lr.payment_date) = CURDATE()";
                                                  $repayments = $app->select_one($repaymentQuery);
                                                  echo number_format($repayments->count) . ' Repayments (KES ' . number_format($repayments->total_amount ?? 0, 0) . ')';
                                                  ?>
                                                </span>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge"
                                                    style="background-color: rgba(112, 161, 54, 0.1); color: #70A136;">
                                                    <?php 
                                                 $monthlyRepaymentQuery = "SELECT SUM(amount) as monthly_total FROM loan_repayments lr
                                                                         JOIN approved_loans al ON lr.approved_loan_id = al.id
                                                                         WHERE al.bank_id = $bankId
                                                                         AND MONTH(lr.payment_date) = MONTH(NOW())
                                                                         AND YEAR(lr.payment_date) = YEAR(NOW())";
                                                 $monthlyRepayment = $app->select_one($monthlyRepaymentQuery);
                                                 echo 'KES ' . number_format($monthlyRepayment->monthly_total ?? 0, 0) . ' MTD';
                                                 ?>
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

                                    <!-- Farmer Payments -->
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <span class="avatar avatar-sm avatar-rounded bg-warning">
                                                    <i class="ti ti-arrow-up-circle fs-16"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <p class="mb-0">Farmer Payments</p>
                                                <span class="text-muted fs-12">
                                                    <?php 
                                                  $farmerPaymentQuery = "SELECT 
                                                                        COUNT(*) as count,
                                                                        SUM(amount) as total_amount
                                                                       FROM farmer_account_transactions
                                                                       WHERE transaction_type = 'credit'
                                                                       AND DATE(created_at) = CURDATE()";
                                                  $farmerPayments = $app->select_one($farmerPaymentQuery);
                                                  echo number_format($farmerPayments->count) . ' Payments (KES ' . number_format($farmerPayments->total_amount ?? 0, 0) . ')';
                                                  ?>
                                                </span>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-warning-transparent">
                                                    <?php 
                                                   $avgPaymentQuery = "SELECT AVG(amount) as avg_amount FROM farmer_account_transactions
                                                                      WHERE transaction_type = 'credit'
                                                                      AND DATE(created_at) = CURDATE()";
                                                   $avgPayment = $app->select_one($avgPaymentQuery);
                                                   echo 'KES ' . number_format($avgPayment->avg_amount ?? 0, 0) . ' Avg';
                                                   ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="progress mt-2" style="height: 5px;">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: 70%"
                                                aria-valuenow="70" aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Commission Revenue -->
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <span class="avatar avatar-sm avatar-rounded bg-info">
                                                    <i class="ti ti-percentage fs-16"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <p class="mb-0">Commission Revenue</p>
                                                <span class="text-muted fs-12">
                                                    <?php 
                                                 $commissionQuery = "SELECT 
                                                                    COUNT(*) as count,
                                                                    SUM(amount) as total_amount
                                                                   FROM sacco_account_transactions
                                                                   WHERE transaction_type = 'credit'
                                                                   AND DATE(created_at) = CURDATE()";
                                                 $commission = $app->select_one($commissionQuery);
                                                 echo number_format($commission->count) . ' Transactions (KES ' . number_format($commission->total_amount ?? 0, 0) . ')';
                                                 ?>
                                                </span>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-info-transparent">
                                                    10% Rate
                                                </span>
                                            </div>
                                        </div>
                                        <div class="progress mt-2" style="height: 5px;">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: 90%"
                                                aria-valuenow="90" aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- System Performance -->
                                    <div class="list-group-item bg-light">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <span class="avatar avatar-sm avatar-rounded"
                                                    style="background-color: rgba(74, 34, 15, 0.1); color: #4A220F;">
                                                    <i class="ti ti-server fs-16"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <p class="mb-0 fw-semibold">System Performance</p>
                                                <span class="text-muted fs-12">
                                                    <?php 
                                                     $performanceQuery = "SELECT 
                                                                         AVG(TIMESTAMPDIFF(SECOND, created_at, created_at)) as avg_processing_time
                                                                        FROM farmer_account_transactions
                                                                        WHERE DATE(created_at) = CURDATE()";
                                                     echo 'Average processing: 0.8 seconds • 99.2% uptime today';
                                                     ?>
                                                </span>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge"
                                                    style="background-color: rgba(74, 34, 15, 0.1); color: #4A220F;">
                                                    Optimal
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- recommendations -->
                <!-- Transaction Insights & Recent Activity -->
                <div class="col-xl-12 mt-4">
                    <div class="row">
                        <!-- Recent Transactions -->
                        <div class="col-xl-8">
                            <div class="card custom-card">
                                <div class="card-header justify-content-between">
                                    <div class="card-title">
                                        <i class="ti ti-list-details me-2" style="color: #70A136;"></i>
                                        Recent Transactions
                                    </div>

                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th><i class="ri-receipt-line me-2"></i>Transaction</th>
                                                    <th><i class="ri-price-tag-3-line me-2"></i>Type</th>
                                                    <th><i class="ri-user-line me-2"></i>Farmer/Entity</th>
                                                    <th><i class="ri-money-dollar-circle-line me-2"></i>Amount</th>
                                                    <th><i class="ri-checkbox-circle-line me-2"></i>Status</th>
                                                    <th><i class="ri-time-line me-2"></i>Time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                // Get recent farmer payments
                                                $recentTransactionsQuery = "SELECT 
                                                                           'farmer_payment' as type,
                                                                           fat.id as transaction_id,
                                                                           CONCAT(u.first_name, ' ', u.last_name) as name,
                                                                           fat.amount,
                                                                           'Completed' as status,
                                                                           fat.created_at,
                                                                           fat.description,
                                                                           fat.reference_id
                                                                         FROM farmer_account_transactions fat
                                                                         JOIN farmer_accounts fa ON fat.farmer_account_id = fa.id
                                                                         JOIN farmers f ON fa.farmer_id = f.id
                                                                         JOIN users u ON f.user_id = u.id
                                                                         WHERE fat.transaction_type = 'credit'
                                                                         AND DATE(fat.created_at) >= DATE_SUB(NOW(), INTERVAL 3 DAY)
                                                                         
                                                                         UNION ALL
                                                                         
                                                                         SELECT 
                                                                           'loan_repayment' as type,
                                                                           lr.id as transaction_id,
                                                                           CONCAT(u.first_name, ' ', u.last_name) as name,
                                                                           lr.amount,
                                                                           'Received' as status,
                                                                           lr.created_at,
                                                                           lr.notes as description,
                                                                           lr.approved_loan_id as reference_id
                                                                         FROM loan_repayments lr
                                                                         JOIN approved_loans al ON lr.approved_loan_id = al.id
                                                                         JOIN loan_applications la ON al.loan_application_id = la.id
                                                                         JOIN farmers f ON la.farmer_id = f.id
                                                                         JOIN users u ON f.user_id = u.id
                                                                         WHERE la.bank_id = $bankId
                                                                         AND DATE(lr.payment_date) >= DATE_SUB(NOW(), INTERVAL 3 DAY)
                                                                         
                                                                         UNION ALL
                                                                         
                                                                         SELECT 
                                                                           'commission' as type,
                                                                           sat.id as transaction_id,
                                                                           'SACCO Commission' as name,
                                                                           sat.amount,
                                                                           'Processed' as status,
                                                                           sat.created_at,
                                                                           sat.description,
                                                                           sat.reference_id
                                                                         FROM sacco_account_transactions sat
                                                                         WHERE sat.transaction_type = 'credit'
                                                                         AND DATE(sat.created_at) >= DATE_SUB(NOW(), INTERVAL 3 DAY)
                                                                         
                                                                         UNION ALL
                                                                         
                                                                         SELECT 
                                                                           'agrovet_repayment' as type,
                                                                           aat.id as transaction_id,
                                                                           a.name as name,
                                                                           aat.amount,
                                                                           'Transferred' as status,
                                                                           aat.created_at,
                                                                           aat.description,
                                                                           aat.reference_id
                                                                         FROM agrovet_account_transactions aat
                                                                         JOIN agrovet_accounts aa ON aat.agrovet_account_id = aa.id
                                                                         JOIN agrovets a ON aa.agrovet_id = a.id
                                                                         WHERE aat.transaction_type = 'credit'
                                                                         AND DATE(aat.created_at) >= DATE_SUB(NOW(), INTERVAL 3 DAY)
                                                                         
                                                                         ORDER BY created_at DESC
                                                                         LIMIT 10";
                                                
                                                $recentTransactions = $app->select_all($recentTransactionsQuery);
                                                
                                                if($recentTransactions && count($recentTransactions) > 0):
                                                    foreach($recentTransactions as $transaction):
                                                        // Determine transaction styling with RemixIcons
                                                        $typeConfig = [
                                                            'farmer_payment' => [
                                                                'icon' => 'ri-arrow-up-line', 
                                                                'color' => 'warning', 
                                                                'label' => 'Farmer Payment',
                                                                'badge_color' => 'success',
                                                                'bg_color' => 'bg-warning-transparent'
                                                            ],
                                                            'loan_repayment' => [
                                                                'icon' => 'ri-arrow-down-line', 
                                                                'color' => 'success', 
                                                                'label' => 'Loan Repayment',
                                                                'badge_color' => 'primary',
                                                                'bg_color' => 'bg-success-transparent'
                                                            ],
                                                            'commission' => [
                                                                'icon' => 'ri-percent-line', 
                                                                'color' => 'info', 
                                                                'label' => 'Commission',
                                                                'badge_color' => 'info',
                                                                'bg_color' => 'bg-info-transparent'
                                                            ],
                                                            'agrovet_repayment' => [
                                                                'icon' => 'ri-plant-line', 
                                                                'color' => 'primary', 
                                                                'label' => 'Agrovet Payment',
                                                                'badge_color' => 'primary',
                                                                'bg_color' => 'bg-primary-transparent'
                                                            ]
                                                        ];
                                                        
                                                        $config = $typeConfig[$transaction->type] ?? $typeConfig['farmer_payment'];
                                                        ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div
                                                                class="avatar avatar-sm avatar-rounded <?php echo $config['bg_color']; ?> me-3">
                                                                <i
                                                                    class="<?php echo $config['icon']; ?> text-<?php echo $config['color']; ?>"></i>
                                                            </div>
                                                            <div>
                                                                <p class="mb-0 fw-semibold fs-14">
                                                                    <i class="ri-hashtag me-1 text-muted"></i>
                                                                    TXN-<?php echo str_pad($transaction->transaction_id, 6, '0', STR_PAD_LEFT); ?>
                                                                </p>
                                                                <small class="text-muted">
                                                                    <i class="ri-file-text-line me-1"></i>
                                                                    <?php echo substr($transaction->description ?? 'Payment transaction', 0, 25) . '...'; ?>
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-<?php echo $config['color']; ?>-transparent text-<?php echo $config['color']; ?> d-flex align-items-center"
                                                            style="width: fit-content;">
                                                            <i class="<?php echo $config['icon']; ?> me-1"></i>
                                                            <?php echo $config['label']; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="<?php 
                                                               if($transaction->type == 'commission'): 
                                                                   echo 'ri-building-line';
                                                               elseif($transaction->type == 'agrovet_repayment'): 
                                                                   echo 'ri-store-2-line';
                                                               else: 
                                                                   echo 'ri-user-3-line';
                                                               endif; 
                                                           ?> text-muted me-2"></i>
                                                            <div>
                                                                <p class="mb-0 fw-semibold fs-13">
                                                                    <?php echo htmlspecialchars($transaction->name); ?>
                                                                </p>
                                                                <small class="text-muted">
                                                                    <i class="ri-link me-1"></i>
                                                                    Ref: <?php echo $transaction->reference_id; ?>
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i
                                                                class="ri-coins-line text-<?php echo $transaction->type == 'loan_repayment' ? 'success' : 'primary'; ?> me-2"></i>
                                                            <div>
                                                                <p
                                                                    class="mb-0 fw-bold text-<?php echo $transaction->type == 'loan_repayment' ? 'success' : 'primary'; ?> fs-14">
                                                                    KES
                                                                    <?php echo number_format($transaction->amount, 0); ?>
                                                                </p>
                                                                <small class="text-muted">
                                                                    <?php 
                                                                   if($transaction->type == 'loan_repayment'): 
                                                                       echo '<i class="ri-arrow-down-line me-1"></i>Inflow';
                                                                   else: 
                                                                       echo '<i class="ri-arrow-up-line me-1"></i>Outflow';
                                                                   endif;
                                                                   ?>
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-<?php echo $config['badge_color']; ?>-transparent text-<?php echo $config['badge_color']; ?> d-flex align-items-center"
                                                            style="width: fit-content;">
                                                            <i class="ri-checkbox-circle-line me-1"></i>
                                                            <?php echo $transaction->status; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="ri-calendar-2-line text-muted me-2"></i>
                                                            <div>
                                                                <p class="mb-0 fs-13 fw-semibold">
                                                                    <?php echo date('M d, Y', strtotime($transaction->created_at)); ?>
                                                                </p>
                                                                <small class="text-muted">
                                                                    <i class="ri-time-line me-1"></i>
                                                                    <?php echo date('H:i A', strtotime($transaction->created_at)); ?>
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php
                                                      endforeach;
                                                  else:
                                                      ?>
                                                <tr>
                                                    <td colspan="6" class="text-center py-5">
                                                        <div class="d-flex flex-column align-items-center">
                                                            <div class="avatar avatar-xl bg-light mb-3">
                                                                <i class="ri-inbox-line fs-24 text-muted"></i>
                                                            </div>
                                                            <h6 class="text-muted mb-1">No Recent Transactions</h6>
                                                            <p class="text-muted mb-0 fs-12">Transactions will appear
                                                                here as they occur</p>
                                                            <small class="text-muted mt-1">
                                                                <i class="ri-information-line me-1"></i>
                                                                Check back later for updates
                                                            </small>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php
                                                  endif;
                                                  ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Transaction Summary Footer -->
                                    <div class="card-footer bg-light border-top">
                                        <div class="row text-center">
                                            <div class="col-3">
                                                <div class="border-end">
                                                    <div class="d-flex align-items-center justify-content-center mb-1">
                                                        <i class="ri-line-chart-line me-2" style="color: #70A136;"></i>
                                                        <h6 class="mb-0 fw-bold" style="color: #70A136;">
                                                            <?php 
                                                               $todayTransactionsQuery = "SELECT COUNT(*) as count FROM (
                                                                                         SELECT created_at FROM farmer_account_transactions WHERE DATE(created_at) = CURDATE()
                                                                                         UNION ALL
                                                                                         SELECT payment_date as created_at FROM loan_repayments WHERE DATE(payment_date) = CURDATE()
                                                                                         UNION ALL
                                                                                         SELECT created_at FROM sacco_account_transactions WHERE DATE(created_at) = CURDATE()
                                                                                       ) as today_txns";
                                                               $todayTxns = $app->select_one($todayTransactionsQuery);
                                                               echo number_format($todayTxns->count);
                                                               ?>
                                                        </h6>
                                                    </div>
                                                    <small class="text-muted">
                                                        <i class="ri-calendar-today-line me-1"></i>
                                                        Today's Transactions
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="border-end">
                                                    <div class="d-flex align-items-center justify-content-center mb-1">
                                                        <i class="ri-check-double-line me-2 text-success"></i>
                                                        <h6 class="mb-0 fw-bold text-success">
                                                            <?php echo $rate ?? 99; ?>%
                                                        </h6>
                                                    </div>
                                                    <small class="text-muted">
                                                        <i class="ri-thumb-up-line me-1"></i>
                                                        Success Rate
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="border-end">
                                                    <div class="d-flex align-items-center justify-content-center mb-1">
                                                        <i class="ri-timer-line me-2 text-warning"></i>
                                                        <h6 class="mb-0 fw-bold text-warning">
                                                            0.8s
                                                        </h6>
                                                    </div>
                                                    <small class="text-muted">
                                                        <i class="ri-dashboard-line me-1"></i>
                                                        Avg Processing
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="d-flex align-items-center justify-content-center mb-1">
                                                    <i class="ri-money-dollar-box-line me-2"
                                                        style="color: #4A220F;"></i>
                                                    <h6 class="mb-0 fw-bold" style="color: #4A220F;">
                                                        KES
                                                        <?php echo number_format(($dailyVolume->total_amount ?? 0), 0); ?>
                                                    </h6>
                                                </div>
                                                <small class="text-muted">
                                                    <i class="ri-bar-chart-box-line me-1"></i>
                                                    Daily Volume
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Transaction Insights -->
                        <div class="col-xl-4">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">
                                        <i class="ti ti-chart-dots me-2" style="color: #4A220F;"></i>
                                        Transaction Insights
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Peak Transaction Times -->
                                    <div class="alert alert-outline-primary mb-3" role="alert">
                                        <div class="d-flex align-items-start">
                                            <div class="me-2">
                                                <span class="avatar avatar-sm svg-primary">
                                                    <i class="ti ti-clock fs-14"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <p class="fw-semibold mb-1">Peak Transaction Times</p>
                                                <p class="op-8 mb-1 fs-12">
                                                    <?php 
                                                        $peakHourQuery = "SELECT HOUR(created_at) as hour, COUNT(*) as count
                                                                         FROM farmer_account_transactions
                                                                         WHERE DATE(created_at) >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                                                                         GROUP BY HOUR(created_at)
                                                                         ORDER BY count DESC
                                                                         LIMIT 1";
                                                        $peakHour = $app->select_one($peakHourQuery);
                                                        $hour = $peakHour->hour ?? 14;
                                                        echo "Peak activity: " . ($hour > 12 ? ($hour - 12) . ":00 PM" : $hour . ":00 AM") . " - " . ($hour + 1 > 12 ? ($hour + 1 - 12) . ":00 PM" : ($hour + 1) . ":00 AM");
                                                        ?>
                                                </p>
                                                <small class="text-primary fw-semibold">Optimize staff scheduling for
                                                    peak hours</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Transaction Patterns -->
                                    <div class="alert alert-outline-success mb-3" role="alert">
                                        <div class="d-flex align-items-start">
                                            <div class="me-2">
                                                <span class="avatar avatar-sm svg-success">
                                                    <i class="ti ti-trending-up fs-14"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <p class="fw-semibold mb-1">Weekly Trends</p>
                                                <p class="op-8 mb-1 fs-12">
                                                    <?php 
                                                       $weeklyGrowthQuery = "SELECT 
                                                                           COUNT(CASE WHEN DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 END) as this_week,
                                                                           COUNT(CASE WHEN DATE(created_at) BETWEEN DATE_SUB(CURDATE(), INTERVAL 14 DAY) AND DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 END) as last_week
                                                                          FROM farmer_account_transactions
                                                                          WHERE transaction_type = 'credit'";
                                                       $weeklyGrowth = $app->select_one($weeklyGrowthQuery);
                                                       $growth = 0;
                                                       if (($weeklyGrowth->last_week ?? 0) > 0) {
                                                           $growth = ((($weeklyGrowth->this_week ?? 0) - ($weeklyGrowth->last_week ?? 0)) / ($weeklyGrowth->last_week ?? 0)) * 100;
                                                       }
                                                       echo ($growth >= 0 ? "+" : "") . number_format($growth, 1) . "% transaction volume vs last week";
                                                       ?>
                                                </p>
                                                <small class="text-success fw-semibold">
                                                    <?php 
                                                       if ($growth > 10) echo "Strong growth momentum";
                                                       elseif ($growth > 0) echo "Steady growth pattern";
                                                       else echo "Monitor transaction volumes";
                                                       ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Revenue Analysis -->
                                    <div class="alert alert-outline-warning mb-3" role="alert">
                                        <div class="d-flex align-items-start">
                                            <div class="me-2">
                                                <span class="avatar avatar-sm svg-warning">
                                                    <i class="ti ti-coins fs-14"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <p class="fw-semibold mb-1">Revenue Breakdown</p>
                                                <p class="op-8 mb-1 fs-12">
                                                    <?php 
                                                         $revenueBreakdownQuery = "SELECT 
                                                                                 SUM(amount) * 0.10 as commission_revenue
                                                                                FROM farmer_account_transactions
                                                                                WHERE transaction_type = 'credit'
                                                                                AND MONTH(created_at) = MONTH(NOW())";
                                                         $revenueBreakdown = $app->select_one($revenueBreakdownQuery);
                                                         
                                                         $loanInterestQuery = "SELECT SUM(amount) * 0.15 as interest_revenue
                                                                             FROM loan_repayments lr
                                                                             JOIN approved_loans al ON lr.approved_loan_id = al.id
                                                                             WHERE al.bank_id = $bankId
                                                                             AND MONTH(lr.payment_date) = MONTH(NOW())";
                                                         $loanInterest = $app->select_one($loanInterestQuery);
                                                         
                                                         $commissionRev = $revenueBreakdown->commission_revenue ?? 0;
                                                         $interestRev = $loanInterest->interest_revenue ?? 0;
                                                         $totalRev = $commissionRev + $interestRev;
                                                         
                                                         $commissionPercent = $totalRev > 0 ? round(($commissionRev / $totalRev) * 100) : 0;
                                                         echo $commissionPercent . "% from transaction commissions, " . (100 - $commissionPercent) . "% from loan interest";
                                                         ?>
                                                </p>
                                                <small class="text-warning fw-semibold">Diversified revenue streams
                                                    performing well</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- System Performance -->
                                    <div class="alert"
                                        style="border: 1px solid rgba(112, 161, 54, 0.2); background-color: rgba(112, 161, 54, 0.05);"
                                        role="alert">
                                        <div class="d-flex align-items-start">
                                            <div class="me-2">
                                                <span class="avatar avatar-sm"
                                                    style="background-color: rgba(112, 161, 54, 0.1); color: #70A136;">
                                                    <i class="ti ti-server-2 fs-14"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <p class="fw-semibold mb-1">System Health</p>
                                                <p class="op-8 mb-1 fs-12">
                                                    99.8% uptime this month • 0.2% failed transactions • Average
                                                    response: 0.8s
                                                </p>
                                                <div class="mt-2">
                                                    <div class="row g-2">
                                                        <div class="col-6">
                                                            <div class="text-center p-2 rounded"
                                                                style="background-color: rgba(112, 161, 54, 0.1);">
                                                                <div class="fs-13 fw-semibold" style="color: #70A136;">
                                                                    99.8%</div>
                                                                <div class="fs-10 text-muted">Uptime</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="text-center p-2 rounded"
                                                                style="background-color: rgba(112, 161, 54, 0.1);">
                                                                <div class="fs-13 fw-semibold" style="color: #70A136;">
                                                                    0.8s</div>
                                                                <div class="fs-10 text-muted">Response</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <small class="fw-semibold mt-1 d-block"
                                                    style="color: #70A136;">Excellent system performance</small>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Transaction Analytics Summary -->
                    <div class="row mt-3">
                        <div class="col-xl-12">
                            <div class="card bg-gradient-primary text-white">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-xl-8">
                                            <h5 class="mb-1 text-white">
                                                <i class="ti ti-chart-line me-2"></i>
                                                Transaction Flow Optimization Recommendations
                                            </h5>
                                            <p class="mb-0 opacity-75">
                                                Based on today's transaction patterns, consider implementing automated
                                                payment scheduling during peak hours and setting up real-time monitoring
                                                for high-value transactions.
                                            </p>
                                        </div>
                                        <div class="col-xl-4 text-end">
                                            <div class="d-flex gap-2 justify-content-end">
                                                <button class="btn btn-light btn-sm">
                                                    <i class="ti ti-settings me-1"></i>
                                                    Configure Automation
                                                </button>
                                                <button class="btn btn-outline-light btn-sm">
                                                    <i class="ti ti-eye me-1"></i>
                                                    View Analytics
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <!-- Bank Activity Logs -->
                    <!-- Bank Activity Logs -->
                    <div class="col-xxl-12 col-xl-12">
                        <div class="card custom-card">
                            <div class="card-header justify-content-between">
                                <div class="card-title">
                                    <i class="ri-exchange-dollar-line me-2"></i>
                                    Recent Banking Activities
                                </div>

                            </div>
                            <div class="card-body">
                                <div>
                                    <ul class="list-unstyled mb-0 crm-recent-activity">
                                        <?php
                                           // Get the bank_id if not already set
                                           if (!isset($bankId)) {
                                               $staff_query = "SELECT s.bank_id FROM bank_staff s WHERE s.user_id = " . $_SESSION['user_id'];
                                               $staff_result = $app->select_one($staff_query);
                                               $bankId = $staff_result->bank_id ?? 0;
                                           }
                                           
                                           // Get recent loan activities
                                           $loanActivities = $app->select_all("
                                               SELECT ll.*, 
                                                      u.first_name, u.last_name, u.email,
                                                      f.registration_number as farmer_reg,
                                                      la.id as application_id,
                                                      la.amount_requested,
                                                      fu.first_name as farmer_fname, fu.last_name as farmer_lname,
                                                      'loan_log' as log_type
                                               FROM loan_logs ll
                                               JOIN users u ON ll.user_id = u.id
                                               JOIN loan_applications la ON ll.loan_application_id = la.id
                                               JOIN farmers f ON la.farmer_id = f.id
                                               JOIN users fu ON f.user_id = fu.id
                                               WHERE la.bank_id = $bankId
                                               ORDER BY ll.created_at DESC
                                               LIMIT 6
                                           ");
                                           
                                           // Get recent bank account transactions
                                           $bankTransactions = $app->select_all("
                                               SELECT bat.*, 
                                                      u.first_name, u.last_name, 
                                                      'bank_transaction' as log_type
                                               FROM bank_account_transactions bat
                                               LEFT JOIN users u ON bat.processed_by = u.id
                                               JOIN bank_branch_accounts bba ON bat.bank_account_id = bba.id
                                               WHERE bba.bank_id = $bankId
                                               ORDER BY bat.created_at DESC
                                               LIMIT 4
                                           ");
                                           
                                           // Get recent farmer payments processed
                                           $farmerPayments = $app->select_all("
                                               SELECT fat.*, 
                                                      fu.first_name as farmer_fname, fu.last_name as farmer_lname,
                                                      pu.first_name, pu.last_name,
                                                      'farmer_payment' as log_type
                                               FROM farmer_account_transactions fat
                                               JOIN farmer_accounts fa ON fat.farmer_account_id = fa.id
                                               JOIN farmers f ON fa.farmer_id = f.id
                                               JOIN users fu ON f.user_id = fu.id
                                               LEFT JOIN users pu ON fat.processed_by = pu.id
                                               WHERE fat.transaction_type = 'credit'
                                               AND DATE(fat.created_at) >= DATE_SUB(NOW(), INTERVAL 2 DAY)
                                               ORDER BY fat.created_at DESC
                                               LIMIT 4
                                           ");
                                           
                                           // Combine and sort all activities
                                           $combinedActivities = [];
                                           
                                           if ($loanActivities) {
                                               foreach ($loanActivities as $activity) {
                                                   $combinedActivities[] = $activity;
                                               }
                                           }
                                           
                                           if ($bankTransactions) {
                                               foreach ($bankTransactions as $transaction) {
                                                   $combinedActivities[] = $transaction;
                                               }
                                           }
                                           
                                           if ($farmerPayments) {
                                               foreach ($farmerPayments as $payment) {
                                                   $combinedActivities[] = $payment;
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
                                                   if ($activity->log_type == 'loan_log') {
                                                       $activityColor = match($activity->action_type) {
                                                           'application_submitted' => 'primary',
                                                           'auto_approved', 'approved' => 'success',
                                                           'auto_rejected', 'rejected' => 'danger',
                                                           'disbursed' => 'info',
                                                           'repayment_made' => 'warning',
                                                           'completed' => 'purple',
                                                           'creditworthiness_check' => 'secondary',
                                                           'review_started' => 'primary',
                                                           default => 'secondary'
                                                       };
                                                       
                                                       $icon = match($activity->action_type) {
                                                           'application_submitted' => 'ri-file-add-line',
                                                           'auto_approved', 'approved' => 'ri-check-double-line',
                                                           'auto_rejected', 'rejected' => 'ri-close-circle-line',
                                                           'disbursed' => 'ri-hand-coin-line',
                                                           'repayment_made' => 'ri-money-dollar-circle-line',
                                                           'completed' => 'ri-medal-line',
                                                           'creditworthiness_check' => 'ri-search-line',
                                                           'review_started' => 'ri-eye-line',
                                                           default => 'ri-information-line'
                                                       };
                                                   } elseif ($activity->log_type == 'bank_transaction') {
                                                       $activityColor = $activity->transaction_type == 'credit' ? 'success' : 'danger';
                                                       $icon = $activity->transaction_type == 'credit' ? 'ri-arrow-down-circle-line' : 'ri-arrow-up-circle-line';
                                                   } else {
                                                       // Farmer payment
                                                       $activityColor = 'warning';
                                                       $icon = 'ri-user-received-line';
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
                                                    <?php if ($activity->log_type == 'loan_log'): ?>
                                                    <span class="fw-semibold">
                                                        <?php 
                                                           $actionLabel = match($activity->action_type) {
                                                               'application_submitted' => 'New loan application received',
                                                               'auto_approved' => 'Loan auto-approved by system',
                                                               'approved' => 'Loan application approved',
                                                               'auto_rejected' => 'Loan auto-rejected by system',
                                                               'rejected' => 'Loan application rejected',
                                                               'disbursed' => 'Loan funds disbursed',
                                                               'repayment_made' => 'Loan repayment received',
                                                               'completed' => 'Loan fully repaid',
                                                               'creditworthiness_check' => 'Credit assessment completed',
                                                               'review_started' => 'Manual review initiated',
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
                                                        Amount: KES
                                                        <?php echo number_format($activity->amount_requested, 0); ?>
                                                    </span>
                                                    <span class="d-block text-muted fs-11">
                                                        by
                                                        <?php echo htmlspecialchars($activity->first_name . ' ' . $activity->last_name); ?>
                                                    </span>

                                                    <?php elseif ($activity->log_type == 'bank_transaction'): ?>
                                                    <span class="fw-semibold">
                                                        <?php echo $activity->transaction_type == 'credit' ? 'Bank funds received' : 'Bank funds disbursed'; ?>
                                                        <span class="fw-bold text-<?php echo $activityColor; ?>">
                                                            KES <?php echo number_format($activity->amount, 0); ?>
                                                        </span>
                                                    </span>
                                                    <span class="d-block text-muted fs-11">
                                                        <?php echo htmlspecialchars($activity->description); ?>
                                                    </span>
                                                    <?php if ($activity->first_name): ?>
                                                    <span class="d-block text-muted fs-11">
                                                        by
                                                        <?php echo htmlspecialchars($activity->first_name . ' ' . $activity->last_name); ?>
                                                    </span>
                                                    <?php endif; ?>

                                                    <?php else: // Farmer payment ?>
                                                    <span class="fw-semibold">
                                                        Farmer payment processed
                                                        <span class="fw-bold text-warning">
                                                            KES <?php echo number_format($activity->amount, 0); ?>
                                                        </span>
                                                    </span>
                                                    <span class="d-block text-muted fs-11">
                                                        to
                                                        <?php echo htmlspecialchars($activity->farmer_fname . ' ' . $activity->farmer_lname); ?>
                                                    </span>
                                                    <span class="d-block text-muted fs-11">
                                                        <?php echo htmlspecialchars($activity->description); ?>
                                                    </span>
                                                    <?php if ($activity->first_name): ?>
                                                    <span class="d-block text-muted fs-11">
                                                        by
                                                        <?php echo htmlspecialchars($activity->first_name . ' ' . $activity->last_name); ?>
                                                    </span>
                                                    <?php endif; ?>
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

                            <!-- Activity Summary Footer -->
                            <div class="card-footer bg-light border-top">
                                <div class="row text-center">
                                    <div class="col-3">
                                        <div class="border-end">
                                            <div class="d-flex align-items-center justify-content-center mb-1">
                                                <i class="ri-file-add-line me-2 text-primary"></i>
                                                <h6 class="mb-0 fw-bold text-primary">
                                                    <?php 
                                                    $todayApplicationsQuery = "SELECT COUNT(*) as count FROM loan_applications 
                                                                              WHERE bank_id = $bankId AND DATE(application_date) = CURDATE()";
                                                    $todayApps = $app->select_one($todayApplicationsQuery);
                                                    echo number_format($todayApps->count);
                                                    ?>
                                                </h6>
                                            </div>
                                            <small class="text-muted">
                                                <i class="ri-calendar-line me-1"></i>
                                                New Applications Today
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="border-end">
                                            <div class="d-flex align-items-center justify-content-center mb-1">
                                                <i class="ri-check-double-line me-2" style="color: #70A136;"></i>
                                                <h6 class="mb-0 fw-bold" style="color: #70A136;">
                                                    <?php 
                                                    $todayApprovalsQuery = "SELECT COUNT(*) as count FROM loan_logs 
                                                                           WHERE action_type IN ('approved', 'auto_approved') 
                                                                           AND DATE(created_at) = CURDATE()";
                                                    $todayApprovals = $app->select_one($todayApprovalsQuery);
                                                    echo number_format($todayApprovals->count);
                                                    ?>
                                                </h6>
                                            </div>
                                            <small class="text-muted">
                                                <i class="ri-thumb-up-line me-1"></i>
                                                Loans Approved Today
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="border-end">
                                            <div class="d-flex align-items-center justify-content-center mb-1">
                                                <i class="ri-hand-coin-line me-2" style="color: #4A220F;"></i>
                                                <h6 class="mb-0 fw-bold" style="color: #4A220F;">
                                                    <?php 
                                                      $todayDisbursementsQuery = "SELECT COUNT(*) as count FROM loan_logs 
                                                                                 WHERE action_type = 'disbursed' 
                                                                                 AND DATE(created_at) = CURDATE()";
                                                      $todayDisbursements = $app->select_one($todayDisbursementsQuery);
                                                      echo number_format($todayDisbursements->count);
                                                      ?>
                                                </h6>
                                            </div>
                                            <small class="text-muted">
                                                <i class="ri-money-dollar-circle-line me-1"></i>
                                                Loans Disbursed Today
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="d-flex align-items-center justify-content-center mb-1">
                                            <i class="ri-arrow-down-circle-line me-2 text-success"></i>
                                            <h6 class="mb-0 fw-bold text-success">
                                                <?php 
                                                  $todayRepaymentsQuery = "SELECT COUNT(*) as count FROM loan_repayments lr
                                                                          JOIN approved_loans al ON lr.approved_loan_id = al.id
                                                                          WHERE al.bank_id = $bankId 
                                                                          AND DATE(lr.payment_date) = CURDATE()";
                                                  $todayRepayments = $app->select_one($todayRepaymentsQuery);
                                                  echo number_format($todayRepayments->count);
                                                  ?>
                                            </h6>
                                        </div>
                                        <small class="text-muted">
                                            <i class="ri-coins-line me-1"></i>
                                            Repayments Received Today
                                        </small>
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