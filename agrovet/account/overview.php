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
    <script src="http://localhost/dfcs/assets/libs/choices.js/public/assets/scripts/choices.min.js">
    </script>
    <!-- font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Main Theme Js -->
    <script src="http://localhost/dfcs/assets/js/main.js"></script>

    <!-- Botstrap Css -->
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
                          
                          // Get the agrovet info for the staff member
                          $agrovetQuery = "SELECT a.* FROM agrovets a 
                                          JOIN agrovet_staff s ON a.id = s.agrovet_id
                                          WHERE s.user_id=" . $_SESSION['user_id'];
                          $agrovet = $app->select_one($agrovetQuery);
                          ?>
                        <p class="fw-semibold fs-18 mb-0">Welcome <?php echo $staff->first_name ?>
                            <?php echo $staff->last_name ?></p>
                        <span class="fs-semibold text-muted pt-1"><?php echo $agrovet->name ?></span>
                        <?php endif; ?>
                        <span class="fs-semibold text-muted pt-5">Account</span>
                    </div>
                </div>
                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                    <h1 class="page-title fw-semibold fs-18 mb-0">Agrovet Account Overview</h1>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Accounts</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Agrovet Account</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <!-- Agrovet Account Quick Stats -->
                <div class="row mt-2">
                    <?php
                            // Get the agrovet_id for the logged-in user
                            $staff_query = "SELECT s.agrovet_id 
                                            FROM agrovet_staff s 
                                            WHERE s.user_id = " . $_SESSION['user_id'];
                            $staff_result = $app->select_one($staff_query);
                            $agrovet_id = $staff_result->agrovet_id;
                            
                            // Query to fetch Agrovet account information
                            $query = "SELECT * FROM agrovet_accounts WHERE agrovet_id = $agrovet_id";
                            $agrovet_account = $app->select_one($query);
                            
                            // Query for transaction statistics
                            $stats_query = "SELECT 
                                           COUNT(*) as total_transactions,
                                           SUM(CASE WHEN transaction_type = 'credit' THEN 1 ELSE 0 END) as credit_count,
                                           SUM(CASE WHEN transaction_type = 'debit' THEN 1 ELSE 0 END) as debit_count,
                                           SUM(CASE WHEN transaction_type = 'credit' THEN amount ELSE 0 END) as total_credits,
                                           SUM(CASE WHEN transaction_type = 'debit' THEN amount ELSE 0 END) as total_debits,
                                           MAX(created_at) as last_transaction_date
                                           FROM agrovet_account_transactions
                                           WHERE agrovet_account_id = (SELECT id FROM agrovet_accounts WHERE agrovet_id = $agrovet_id)";
                            $transaction_stats = $app->select_one($stats_query);
                            
                            // Calculate monthly average transaction volume
                            $monthly_query = "SELECT 
                                             AVG(monthly_total) as monthly_avg 
                                             FROM (
                                                 SELECT 
                                                 DATE_FORMAT(created_at, '%Y-%m') as month,
                                                 COUNT(*) as monthly_total
                                                 FROM agrovet_account_transactions
                                                 WHERE agrovet_account_id = (SELECT id FROM agrovet_accounts WHERE agrovet_id = $agrovet_id)
                                                 GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                                             ) as monthly_counts";
                            $monthly_stats = $app->select_one($monthly_query);
                        ?>

                    <!-- Current Balance -->
                    <div class="col-xxl-4 col-lg-4 col-md-6">
                        <div class="card custom-card overflow-hidden" style="background:#6AA32D!important;">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded"
                                            style="background-color: white!important;">
                                            <i style="color:#6AA32D" class="fa-solid fa-wallet fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-white mb-0">Current Balance</p>
                                                <h4 style="color:wheat;" class="fw-semibold mt-1">
                                                    KES <?php echo number_format($agrovet_account->balance ?? 0, 2); ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Total Credits -->
                    <div class="col-xxl-4 col-lg-4 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-arrow-down fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Total Credits</p>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    KES
                                                    <?php echo number_format($transaction_stats->total_credits ?? 0, 2); ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Total Debits -->
                    <div class="col-xxl-4 col-lg-4 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-arrow-up fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Total Debits</p>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    KES
                                                    <?php echo number_format($transaction_stats->total_debits ?? 0, 2); ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Active Input Credits -->
                    <div class="col-xxl-4 col-lg-4 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-credit-card fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Active Input Credits
                                                </p>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php 
                                                        // Count active credits for this agrovet
                                                        $credits_query = "SELECT COUNT(*) as active_count 
                                                                         FROM approved_input_credits aic
                                                                         JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                                                         WHERE ica.agrovet_id = $agrovet_id 
                                                                         AND aic.status = 'active'";
                                                        $credits_stats = $app->select_one($credits_query);
                                                        echo number_format($credits_stats->active_count ?? 0);
                                                        ?>
                                                    <small class="text-muted fs-12">active</small>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Outstanding Credit Balance -->
                    <div class="col-xxl-4 col-lg-4 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-money-bill-transfer fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Outstanding Credit</p>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    KES
                                                    <?php 
                                                           // Sum of outstanding balances
                                                           $outstanding_query = "SELECT SUM(remaining_balance) as total_outstanding 
                                                                                FROM approved_input_credits aic
                                                                                JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                                                                WHERE ica.agrovet_id = $agrovet_id 
                                                                                AND aic.status = 'active'";
                                                           $outstanding_stats = $app->select_one($outstanding_query);
                                                           echo number_format($outstanding_stats->total_outstanding ?? 0, 2);
                                                           ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Last Transaction -->
                    <div class="col-xxl-4 col-lg-4 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-clock-rotate-left fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Last Transaction</p>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php 
                                                      if($transaction_stats->last_transaction_date) {
                                                          echo date('M d', strtotime($transaction_stats->last_transaction_date));
                                                      } else {
                                                          echo 'N/A';
                                                      }
                                                      ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Agrovet Information Overview -->
                <div class="row mt-4 mb-4">
                    <div class="col-xl-12">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-header bg-light">
                                <div class="card-title">
                                    <i class="ri-store-3-line me-2"></i> Agrovet Information
                                </div>
                            </div>
                            <div class="card-body">
                                <?php
                                   $app = new App();
                                   
                                   // Get the agrovet_id for the logged-in user
                                   $staff_query = "SELECT s.agrovet_id 
                                                   FROM agrovet_staff s 
                                                   WHERE s.user_id = " . $_SESSION['user_id'];
                                   $staff_result = $app->select_one($staff_query);
                                   $agrovet_id = $staff_result->agrovet_id;
                                   
                                   // Get agrovet details
                                   $query = "SELECT a.*, at.name as type_name 
                                            FROM agrovets a
                                            JOIN agrovet_types at ON a.type_id = at.id
                                            WHERE a.id = $agrovet_id";
                                   $agrovet = $app->select_one($query);
                                   
                                   // Get number of staff for this agrovet
                                   $staff_count_query = "SELECT COUNT(*) as staff_count FROM agrovet_staff WHERE agrovet_id = $agrovet_id";
                                   $staff_count_result = $app->select_one($staff_count_query);
                                   $staff_count = $staff_count_result->staff_count ?? 0;
                                   
                                   // Get top 2 most active staff
                                   $top_staff_query = "SELECT 
                                                       u.id,
                                                       CONCAT(u.first_name, ' ', u.last_name) as staff_name,
                                                       s.position,
                                                       COUNT(aic.id) as credits_processed
                                                     FROM agrovet_staff s
                                                     JOIN users u ON s.user_id = u.id
                                                     LEFT JOIN approved_input_credits aic ON aic.approved_by = s.id
                                                     WHERE s.agrovet_id = $agrovet_id
                                                     GROUP BY u.id, u.first_name, u.last_name, s.position
                                                     ORDER BY credits_processed DESC
                                                     LIMIT 2";
                                   $top_staff = $app->select_all($top_staff_query);
                                   
                                   // Badges for agrovet types
                                   $typeBadges = [
                                       'Retail' => 'bg-primary',
                                       'Wholesale' => 'bg-success',
                                       'Distributor' => 'bg-info'
                                   ];
                                   $typeBadgeClass = $typeBadges[$agrovet->type_name] ?? 'bg-secondary';
                                   
                                   // Get account info
                                   $account_query = "SELECT * FROM agrovet_accounts WHERE agrovet_id = $agrovet_id";
                                   $account = $app->select_one($account_query);
                                   ?>
                                <div class="row">
                                    <!-- Agrovet Basic Info -->
                                    <div class="col-lg-6">
                                        <div class="d-flex align-items-center mb-4">
                                            <div
                                                class="avatar avatar-lg bg-success-transparent me-3 d-flex align-items-center justify-content-center rounded">
                                                <i class="ri-store-2-fill fs-2 text-success"></i>
                                            </div>
                                            <div>
                                                <h4 class="mb-1"><?php echo htmlspecialchars($agrovet->name); ?></h4>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge <?php echo $typeBadgeClass; ?>-transparent me-2">
                                                        <?php echo htmlspecialchars($agrovet->type_name); ?>
                                                    </span>
                                                    <span class="text-muted">License:
                                                        <?php echo htmlspecialchars($agrovet->license_number); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <div class="p-3 border rounded-3 bg-light-subtle">
                                                    <div class="d-flex align-items-center">
                                                        <span class="avatar avatar-sm bg-success me-3">
                                                            <i class="ri-map-pin-line"></i>
                                                        </span>
                                                        <div>
                                                            <h6 class="mb-1">Location</h6>
                                                            <p class="mb-0 text-muted">
                                                                <?php echo htmlspecialchars($agrovet->location); ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="p-3 border rounded-3 bg-light-subtle">
                                                    <div class="d-flex align-items-center">
                                                        <span class="avatar avatar-sm bg-primary me-3">
                                                            <i class="ri-team-line"></i>
                                                        </span>
                                                        <div>
                                                            <h6 class="mb-1">Staff Count</h6>
                                                            <p class="mb-0"><?php echo $staff_count; ?> employees</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <h6 class="mb-2"><i class="ri-contacts-line me-2"></i>Contact Information
                                            </h6>
                                            <div class="d-flex flex-column gap-2">
                                                <div class="d-flex align-items-center">
                                                    <span
                                                        class="avatar avatar-xs bg-info-transparent text-info me-2 rounded">
                                                        <i class="ri-phone-line"></i>
                                                    </span>
                                                    <span><?php echo htmlspecialchars($agrovet->phone); ?></span>
                                                </div>
                                                <?php if($agrovet->email): ?>
                                                <div class="d-flex align-items-center">
                                                    <span
                                                        class="avatar avatar-xs bg-danger-transparent text-danger me-2 rounded">
                                                        <i class="ri-mail-line"></i>
                                                    </span>
                                                    <span><?php echo htmlspecialchars($agrovet->email); ?></span>
                                                </div>
                                                <?php endif; ?>
                                                <div class="d-flex align-items-center">
                                                    <span
                                                        class="avatar avatar-xs bg-success-transparent text-success me-2 rounded">
                                                        <i class="ri-map-pin-line"></i>
                                                    </span>
                                                    <span><?php echo htmlspecialchars($agrovet->address); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Account Info and Top Staff -->
                                    <div class="col-lg-6">
                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <div class="p-3 border rounded-3 bg-light-subtle h-100">
                                                    <h6 class="mb-2"><i class="ri-bank-card-line me-2"></i>Account
                                                        Information</h6>
                                                    <div class="d-flex flex-column gap-2">
                                                        <div class="d-flex align-items-center">
                                                            <span
                                                                class="avatar avatar-xs bg-primary-transparent text-primary me-2 rounded">
                                                                <i class="ri-bank-line"></i>
                                                            </span>
                                                            <span>Account #:
                                                                <strong><?php echo htmlspecialchars($account->account_number ?? 'N/A'); ?></strong></span>
                                                        </div>
                                                        <div class="d-flex align-items-center">
                                                            <span
                                                                class="avatar avatar-xs bg-success-transparent text-success me-2 rounded">
                                                                <i class="ri-wallet-3-line"></i>
                                                            </span>
                                                            <span>Type:
                                                                <strong><?php echo htmlspecialchars($account->account_type ?? 'N/A'); ?></strong></span>
                                                        </div>
                                                        <div class="d-flex align-items-center">
                                                            <span
                                                                class="avatar avatar-xs bg-warning-transparent text-warning me-2 rounded">
                                                                <i class="ri-calendar-check-line"></i>
                                                            </span>
                                                            <span>Active Since:
                                                                <strong><?php echo date('M Y', strtotime($agrovet->created_at)); ?></strong></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="p-3 border rounded-3 bg-primary-subtle text-primary h-100">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <h6 class="mb-0"><i class="ri-line-chart-line me-2"></i>Account
                                                            Status</h6>
                                                        <div class="ms-auto">
                                                            <span
                                                                class="badge <?php echo ($account->balance ?? 0) > 0 ? 'bg-success' : 'bg-danger'; ?>">
                                                                <?php echo ($account->balance ?? 0) > 0 ? 'Active' : 'Low Balance'; ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="text-center mt-2">
                                                        <h3 class="mb-0">KES
                                                            <?php echo number_format($account->balance ?? 0, 2); ?></h3>
                                                        <p class="mb-0 small">Current Balance</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="p-3 border rounded-3 bg-light-subtle mt-2">
                                            <h6 class="mb-3"><i class="ri-user-star-line me-2"></i>Top Performing Staff
                                            </h6>
                                            <?php if($top_staff): ?>
                                            <?php foreach($top_staff as $index => $staff): ?>
                                            <div class="d-flex align-items-center mb-3">
                                                <div
                                                    class="avatar avatar-md me-3 <?php echo $index === 0 ? 'bg-warning' : 'bg-info'; ?>">
                                                    <?php echo strtoupper(substr($staff->staff_name, 0, 1)); ?>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0"><?php echo htmlspecialchars($staff->staff_name); ?>
                                                    </h6>
                                                    <p class="mb-0 text-muted small">
                                                        <?php echo htmlspecialchars($staff->position); ?></p>
                                                </div>
                                                <div class="text-end">
                                                    <span
                                                        class="badge <?php echo $index === 0 ? 'bg-warning' : 'bg-info'; ?>-transparent">
                                                        <?php echo $staff->credits_processed; ?> credits
                                                    </span>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                            <?php else: ?>
                                            <div class="text-center py-3">
                                                <i class="ri-user-search-line fs-3 text-muted mb-2"></i>
                                                <p class="mb-0 text-muted">No staff performance data available</p>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Input Credit Performance Metrics -->
                <div class="row mt-4">
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-header">
                                <div class="card-title">
                                    <i class="ri-line-chart-line me-2"></i> Monthly Agrovet Transaction Metrics
                                </div>
                            </div>
                            <div class="card-body">
                                <?php include "../graphs/agrovet-transaction-distribution.php" ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-xl-12">
                        <div id="recentAgrovetTransactionsSection"></div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-xl-12">
                        <div id="creditSourcesSection"></div>
                    </div>
                </div>
                <!-- Input Credit Analytics -->
                <div class="row mt-4">
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-header">
                                <div class="card-title">
                                    <i class="ri-bar-chart-grouped-line me-2"></i> Input Credit Analytics
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="inputCreditAnalyticsSection"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Agrovet Performance Indicators -->
                <div class="row mt-4">
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-header justify-content-between">
                                <div class="card-title">
                                    <i class="ri-line-chart-line me-2"></i> Account Performance Indicators
                                </div>
                                <div>
                                    <span class="badge bg-light text-dark">
                                        <i class="ri-calendar-line me-1"></i> Last Updated:
                                        <?php echo date('M d, Y, h:i A'); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php
                                          $app = new App();
                                          
                                          // Get the agrovet_id for the logged-in user
                                          $staff_query = "SELECT s.agrovet_id 
                                                          FROM agrovet_staff s 
                                                          WHERE s.user_id = " . $_SESSION['user_id'];
                                          $staff_result = $app->select_one($staff_query);
                                          $agrovet_id = $staff_result->agrovet_id;
                                          
                                          // Get agrovet account id
                                          $account_query = "SELECT id, balance FROM agrovet_accounts WHERE agrovet_id = $agrovet_id";
                                          $account_result = $app->select_one($account_query);
                                          $agrovet_account_id = $account_result->id ?? 0;
                                          $balance = $account_result->balance ?? 0;
                                          
                                          // Previous month's balance (for comparison)
                                          $query = "SELECT 
                                                      (SELECT balance FROM agrovet_accounts WHERE id = $agrovet_account_id) - 
                                                      COALESCE(SUM(CASE WHEN transaction_type = 'credit' THEN amount ELSE -amount END), 0) as previous_balance
                                                    FROM agrovet_account_transactions 
                                                    WHERE agrovet_account_id = $agrovet_account_id
                                                    AND created_at >= DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01') 
                                                    AND created_at < DATE_FORMAT(NOW(), '%Y-%m-01')";
                                          $prevBalanceResult = $app->select_one($query);
                                          $previousBalance = $prevBalanceResult->previous_balance ?? 0;
                                          
                                          // Calculate growth
                                          $balanceGrowth = 0;
                                          $balanceGrowthPercent = 0;
                                          if ($previousBalance > 0) {
                                              $balanceGrowth = $balance - $previousBalance;
                                              $balanceGrowthPercent = ($balanceGrowth / $previousBalance) * 100;
                                          }
                                          
                                          // Average transaction size (current month)
                                          $query = "SELECT AVG(amount) as avg_amount 
                                                    FROM agrovet_account_transactions 
                                                    WHERE agrovet_account_id = $agrovet_account_id
                                                    AND created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')";
                                          $avgTxnResult = $app->select_one($query);
                                          $avgTransaction = $avgTxnResult->avg_amount ?? 0;
                                          
                                          // Average transaction size (previous month)
                                          $query = "SELECT AVG(amount) as avg_amount 
                                                    FROM agrovet_account_transactions 
                                                    WHERE agrovet_account_id = $agrovet_account_id
                                                    AND created_at >= DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01') 
                                                    AND created_at < DATE_FORMAT(NOW(), '%Y-%m-01')";
                                          $prevAvgTxnResult = $app->select_one($query);
                                          $prevAvgTransaction = $prevAvgTxnResult->avg_amount ?? 0;
                                          
                                          // Calculate avg transaction growth
                                          $avgTxnGrowth = 0;
                                          $avgTxnGrowthPercent = 0;
                                          if ($prevAvgTransaction > 0) {
                                              $avgTxnGrowth = $avgTransaction - $prevAvgTransaction;
                                              $avgTxnGrowthPercent = ($avgTxnGrowth / $prevAvgTransaction) * 100;
                                          }
                                          
                                          // Current week transaction count
                                          $query = "SELECT COUNT(*) as txn_count 
                                                    FROM agrovet_account_transactions 
                                                    WHERE agrovet_account_id = $agrovet_account_id
                                                    AND created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL WEEKDAY(CURRENT_DATE()) DAY)";
                                          $weeklyTxnResult = $app->select_one($query);
                                          $weeklyTransactions = $weeklyTxnResult->txn_count ?? 0;
                                          
                                          // Previous week transaction count
                                          $query = "SELECT COUNT(*) as txn_count 
                                                    FROM agrovet_account_transactions 
                                                    WHERE agrovet_account_id = $agrovet_account_id
                                                    AND created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL WEEKDAY(CURRENT_DATE()) + 7 DAY)
                                                    AND created_at < DATE_SUB(CURRENT_DATE(), INTERVAL WEEKDAY(CURRENT_DATE()) DAY)";
                                          $prevWeeklyTxnResult = $app->select_one($query);
                                          $prevWeeklyTransactions = $prevWeeklyTxnResult->txn_count ?? 0;
                                          
                                          // Calculate weekly txn growth
                                          $weeklyTxnGrowth = $weeklyTransactions - $prevWeeklyTransactions;
                                          $weeklyTxnGrowthPercent = 0;
                                          if ($prevWeeklyTransactions > 0) {
                                              $weeklyTxnGrowthPercent = ($weeklyTxnGrowth / $prevWeeklyTransactions) * 100;
                                          }
                                          // Credit to Debit ratio (current month)
                                          $query = "SELECT 
                                                      SUM(CASE WHEN transaction_type = 'credit' THEN amount ELSE 0 END) as total_credits,
                                                      SUM(CASE WHEN transaction_type = 'debit' THEN amount ELSE 0 END) as total_debits
                                                    FROM agrovet_account_transactions 
                                                    WHERE agrovet_account_id = $agrovet_account_id
                                                    AND created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')";
                                          $ratioResult = $app->select_one($query);
                                          $totalCredits = $ratioResult->total_credits ?? 0;
                                          $totalDebits = $ratioResult->total_debits ?? 0;
                                          
                                          // Calculate ratio
                                          $creditDebitRatio = ($totalDebits > 0) ? ($totalCredits / $totalDebits) : $totalCredits;
                                          $ratioFormatted = number_format($creditDebitRatio, 1) . ':1';        
                                          // Previous month ratio
                                          $query = "SELECT 
                                                      SUM(CASE WHEN transaction_type = 'credit' THEN amount ELSE 0 END) as total_credits,
                                                      SUM(CASE WHEN transaction_type = 'debit' THEN amount ELSE 0 END) as total_debits
                                                    FROM agrovet_account_transactions 
                                                    WHERE agrovet_account_id = $agrovet_account_id
                                                    AND created_at >= DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01') 
                                                    AND created_at < DATE_FORMAT(NOW(), '%Y-%m-01')";
                                          $prevRatioResult = $app->select_one($query);
                                          $prevTotalCredits = $prevRatioResult->total_credits ?? 0;
                                          $prevTotalDebits = $prevRatioResult->total_debits ?? 0;
                                          
                                          // Calculate previous ratio
                                          $prevCreditDebitRatio = ($prevTotalDebits > 0) ? ($prevTotalCredits / $prevTotalDebits) : $prevTotalCredits;
                                          $prevRatioFormatted = number_format($prevCreditDebitRatio, 1) . ':1';
                                          
                                          // Get monthly data for financial health summary
                                          $periods = [];          
                                          // Current month
                                          $query = "SELECT 
                                                      'Current Month' as period,
                                                      SUM(CASE WHEN transaction_type = 'credit' THEN amount ELSE 0 END) as total_credits,
                                                      SUM(CASE WHEN transaction_type = 'debit' THEN amount ELSE 0 END) as total_debits
                                                    FROM agrovet_account_transactions 
                                                    WHERE agrovet_account_id = $agrovet_account_id
                                                    AND created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')";
                                          $currentMonthResult = $app->select_one($query);
                                          $periods[] = [
                                              'name' => 'Current Month',
                                              'credits' => $currentMonthResult->total_credits ?? 0,
                                              'debits' => $currentMonthResult->total_debits ?? 0,
                                              'net' => ($currentMonthResult->total_credits ?? 0) - ($currentMonthResult->total_debits ?? 0)
                                          ];     
                                          // Previous month
                                          $query = "SELECT 
                                                      'Previous Month' as period,
                                                      SUM(CASE WHEN transaction_type = 'credit' THEN amount ELSE 0 END) as total_credits,
                                                      SUM(CASE WHEN transaction_type = 'debit' THEN amount ELSE 0 END) as total_debits
                                                    FROM agrovet_account_transactions 
                                                    WHERE agrovet_account_id = $agrovet_account_id
                                                    AND created_at >= DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01') 
                                                    AND created_at < DATE_FORMAT(NOW(), '%Y-%m-01')";
                                          $prevMonthResult = $app->select_one($query);
                                          $periods[] = [
                                              'name' => 'Previous Month',
                                              'credits' => $prevMonthResult->total_credits ?? 0,
                                              'debits' => $prevMonthResult->total_debits ?? 0,
                                              'net' => ($prevMonthResult->total_credits ?? 0) - ($prevMonthResult->total_debits ?? 0)
                                          ];
                                          
                                          // Last quarter
                                          $query = "SELECT 
                                                      'Last Quarter' as period,
                                                      SUM(CASE WHEN transaction_type = 'credit' THEN amount ELSE 0 END) as total_credits,
                                                      SUM(CASE WHEN transaction_type = 'debit' THEN amount ELSE 0 END) as total_debits
                                                    FROM agrovet_account_transactions 
                                                    WHERE agrovet_account_id = $agrovet_account_id
                                                    AND created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 3 MONTH)";
                                          $quarterResult = $app->select_one($query);
                                          $periods[] = [
                                              'name' => 'Last Quarter',
                                              'credits' => $quarterResult->total_credits ?? 0,
                                              'debits' => $quarterResult->total_debits ?? 0,
                                              'net' => ($quarterResult->total_credits ?? 0) - ($quarterResult->total_debits ?? 0)
                                          ];                                         
                                          // Calculate trends
                                          foreach ($periods as $i => &$period) {
                                              if ($i < count($periods) - 1) {
                                                  $nextPeriod = $periods[$i + 1];
                                                  if ($nextPeriod['net'] != 0) {
                                                      $period['trend_percent'] = (($period['net'] - $nextPeriod['net']) / abs($nextPeriod['net'])) * 100;
                                                  } else {
                                                      $period['trend_percent'] = $period['net'] > 0 ? 100 : 0;
                                                  }
                                                  
                                                  // Set trend direction and color
                                                  if ($period['trend_percent'] > 0) {
                                                      $period['trend_direction'] = 'up';
                                                      $period['trend_color'] = 'success';
                                                  } elseif ($period['trend_percent'] < 0) {
                                                      $period['trend_direction'] = 'down';
                                                      $period['trend_color'] = 'danger';
                                                      $period['trend_percent'] = abs($period['trend_percent']);
                                                  } else {
                                                      $period['trend_direction'] = 'right';
                                                      $period['trend_color'] = 'warning';
                                                  }                                        
                                                  // Calculate progress bar width (max 95%)
                                                  $period['progress'] = min(95, max(5, abs($period['trend_percent'])));
                                              } else {
                                                  // Last period (quarter) - set trend based on net value
                                                  $period['trend_percent'] = $period['net'] > 0 ? 10 : ($period['net'] < 0 ? -10 : 0);
                                                  
                                                  if ($period['trend_percent'] > 0) {
                                                      $period['trend_direction'] = 'up';
                                                      $period['trend_color'] = 'success';
                                                  } elseif ($period['trend_percent'] < 0) {
                                                      $period['trend_direction'] = 'down';
                                                      $period['trend_color'] = 'danger';
                                                      $period['trend_percent'] = abs($period['trend_percent']);
                                                  } else {
                                                      $period['trend_direction'] = 'right';
                                                      $period['trend_color'] = 'warning';
                                                  }                       
                                                  $period['progress'] = 45; // Default for quarter
                                              }
                                          }
                                          ?>
                                <div class="row g-3">
                                    <!-- Month-over-month Balance Growth -->
                                    <div class="col-xl-3 col-md-6">
                                        <div class="p-3 border rounded-3 bg-light-subtle">
                                            <div class="d-flex align-items-center mb-2">
                                                <span
                                                    class="avatar avatar-sm <?php echo $balanceGrowth >= 0 ? 'bg-success' : 'bg-danger'; ?> me-3">
                                                    <i
                                                        class="ri-arrow-<?php echo $balanceGrowth >= 0 ? 'up' : 'down'; ?>-line"></i>
                                                </span>
                                                <h6 class="mb-0 flex-grow-1">Balance Growth</h6>
                                                <span
                                                    class="badge bg-<?php echo $balanceGrowth >= 0 ? 'success' : 'danger'; ?>-transparent">
                                                    <i
                                                        class="ri-arrow-<?php echo $balanceGrowth >= 0 ? 'up' : 'down'; ?>-line"></i>
                                                    <?php echo abs(round($balanceGrowthPercent, 1)); ?>%
                                                </span>
                                            </div>
                                            <div class="d-flex align-items-end">
                                                <h3 class="mb-0 fw-semibold">KES
                                                    <?php echo number_format($balance, 0); ?>
                                                </h3>
                                                <small class="text-muted ms-2 pb-1">Current</small>
                                            </div>
                                            <div class="mt-2">
                                                <div class="progress progress-xs">
                                                    <div class="progress-bar bg-<?php echo $balanceGrowth >= 0 ? 'success' : 'danger'; ?>"
                                                        style="width: <?php echo min(95, abs($balanceGrowthPercent) * 3); ?>%"
                                                        role="progressbar"></div>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between mt-1">
                                                    <small class="text-muted">Previous: KES
                                                        <?php echo number_format($previousBalance, 0); ?></small>
                                                    <small
                                                        class="text-<?php echo $balanceGrowth >= 0 ? 'success' : 'danger'; ?>">
                                                        <?php echo $balanceGrowth >= 0 ? '+' : '-'; ?>KES
                                                        <?php echo number_format(abs($balanceGrowth), 0); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Average Transaction Size -->
                                    <div class="col-xl-3 col-md-6">
                                        <div class="p-3 border rounded-3 bg-light-subtle">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="avatar avatar-sm bg-primary me-3">
                                                    <i class="ri-exchange-dollar-line"></i>
                                                </span>
                                                <h6 class="mb-0 flex-grow-1">Avg. Transaction</h6>
                                                <span
                                                    class="badge bg-<?php echo $avgTxnGrowth >= 0 ? 'primary' : 'danger'; ?>-transparent">
                                                    <i
                                                        class="ri-arrow-<?php echo $avgTxnGrowth >= 0 ? 'up' : 'down'; ?>-line"></i>
                                                    <?php echo abs(round($avgTxnGrowthPercent, 1)); ?>%
                                                </span>
                                            </div>
                                            <div class="d-flex align-items-end">
                                                <h3 class="mb-0 fw-semibold">KES
                                                    <?php echo number_format($avgTransaction, 0); ?></h3>
                                                <small class="text-muted ms-2 pb-1">Per txn</small>
                                            </div>
                                            <div class="mt-2">
                                                <div class="progress progress-xs">
                                                    <div class="progress-bar bg-primary"
                                                        style="width: <?php echo min(95, abs($avgTxnGrowthPercent) * 3); ?>%"
                                                        role="progressbar"></div>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between mt-1">
                                                    <small class="text-muted">Previous: KES
                                                        <?php echo number_format($prevAvgTransaction, 0); ?></small>
                                                    <small
                                                        class="text-<?php echo $avgTxnGrowth >= 0 ? 'primary' : 'danger'; ?>">
                                                        <?php echo $avgTxnGrowth >= 0 ? '+' : '-'; ?>KES
                                                        <?php echo number_format(abs($avgTxnGrowth), 0); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Weekly Transaction Volume -->
                                    <div class="col-xl-3 col-md-6">
                                        <div class="p-3 border rounded-3 bg-light-subtle">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="avatar avatar-sm bg-info me-3">
                                                    <i class="ri-stack-line"></i>
                                                </span>
                                                <h6 class="mb-0 flex-grow-1">Weekly Volume</h6>
                                                <span
                                                    class="badge bg-<?php echo $weeklyTxnGrowth >= 0 ? 'info' : 'danger'; ?>-transparent">
                                                    <i
                                                        class="ri-arrow-<?php echo $weeklyTxnGrowth >= 0 ? 'up' : 'down'; ?>-line"></i>
                                                    <?php echo abs(round($weeklyTxnGrowthPercent, 1)); ?>%
                                                </span>
                                            </div>
                                            <div class="d-flex align-items-end">
                                                <h3 class="mb-0 fw-semibold"><?php echo $weeklyTransactions; ?></h3>
                                                <small class="text-muted ms-2 pb-1">Transactions</small>
                                            </div>
                                            <div class="mt-2">
                                                <div class="progress progress-xs">
                                                    <div class="progress-bar bg-info"
                                                        style="width: <?php echo min(95, max(5, ($weeklyTransactions / max(1, $weeklyTransactions + $prevWeeklyTransactions)) * 100)); ?>%"
                                                        role="progressbar"></div>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between mt-1">
                                                    <small class="text-muted">Previous:
                                                        <?php echo $prevWeeklyTransactions; ?> transactions</small>
                                                    <small
                                                        class="text-<?php echo $weeklyTxnGrowth >= 0 ? 'success' : 'danger'; ?>">
                                                        <?php echo $weeklyTxnGrowth >= 0 ? '+' : '-'; ?><?php echo abs($weeklyTxnGrowth); ?>
                                                        transactions
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Credit to Debit Ratio -->
                                    <div class="col-xl-3 col-md-6">
                                        <div class="p-3 border rounded-3 bg-light-subtle">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="avatar avatar-sm bg-warning me-3">
                                                    <i class="ri-scales-3-line"></i>
                                                </span>
                                                <h6 class="mb-0 flex-grow-1">Credit:Debit Ratio</h6>
                                                <span
                                                    class="badge bg-<?php echo $creditDebitRatio >= $prevCreditDebitRatio ? 'success' : 'warning'; ?>-transparent">
                                                    <i
                                                        class="ri-arrow-<?php echo $creditDebitRatio > $prevCreditDebitRatio ? 'up' : ($creditDebitRatio < $prevCreditDebitRatio ? 'down' : 'right'); ?>-line"></i>
                                                    <?php echo $creditDebitRatio > $prevCreditDebitRatio ? 'Improving' : ($creditDebitRatio < $prevCreditDebitRatio ? 'Declining' : 'Stable'); ?>
                                                </span>
                                            </div>
                                            <div class="d-flex align-items-end">
                                                <h3 class="mb-0 fw-semibold"><?php echo $ratioFormatted; ?></h3>
                                                <small class="text-muted ms-2 pb-1">Ratio</small>
                                            </div>
                                            <div class="mt-2">
                                                <div class="progress progress-xs">
                                                    <?php
                                                      // Calculate credit percentage for progress bar
                                                      $creditPercent = ($totalCredits > 0 || $totalDebits > 0) 
                                                          ? ($totalCredits / ($totalCredits + $totalDebits)) * 100 
                                                          : 0;
                                                      $debitPercent = 100 - $creditPercent;
                                                      ?>
                                                    <div class="progress-bar bg-success"
                                                        style="width: <?php echo $creditPercent; ?>%"
                                                        role="progressbar">
                                                    </div>
                                                    <div class="progress-bar bg-danger"
                                                        style="width: <?php echo $debitPercent; ?>%" role="progressbar">
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between mt-1">
                                                    <small class="text-muted">Previous:
                                                        <?php echo $prevRatioFormatted; ?></small>
                                                    <small
                                                        class="text-<?php echo $creditDebitRatio >= 2 ? 'success' : ($creditDebitRatio >= 1 ? 'warning' : 'danger'); ?>">
                                                        <?php 
                                                          if ($creditDebitRatio >= 2) echo 'Healthy balance';
                                                          elseif ($creditDebitRatio >= 1) echo 'Neutral balance';
                                                          else echo 'Needs attention';
                                                          ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Financial Health Summary -->
                                <div class="mt-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <h6 class="mb-0"><i class="ri-heart-pulse-line me-2"></i> Financial Health
                                            Summary
                                        </h6>
                                        <div class="ms-auto">
                                            <select class="form-select form-select-sm">
                                                <option selected>30 Days</option>
                                                <option>60 Days</option>
                                                <option>90 Days</option>
                                                <option>Year to Date</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="text-center"><i class="ri-calendar-line me-1"></i> Period
                                                    </th>
                                                    <th class="text-center"><i
                                                            class="ri-arrow-down-circle-line me-1 text-success"></i>
                                                        Credits
                                                    </th>
                                                    <th class="text-center"><i
                                                            class="ri-arrow-up-circle-line me-1 text-danger"></i> Debits
                                                    </th>
                                                    <th class="text-center"><i class="ri-exchange-line me-1"></i> Net
                                                        Change
                                                    </th>
                                                    <th class="text-center"><i class="ri-line-chart-line me-1"></i>
                                                        Trend
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($periods as $period): ?>
                                                <tr>
                                                    <td class="fw-medium">
                                                        <?php echo htmlspecialchars($period['name']); ?>
                                                    </td>
                                                    <td class="text-end text-success">KES
                                                        <?php echo number_format($period['credits'], 0); ?></td>
                                                    <td class="text-end text-danger">KES
                                                        <?php echo number_format($period['debits'], 0); ?></td>
                                                    <td
                                                        class="text-end fw-semibold <?php echo $period['net'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                                        <?php echo $period['net'] >= 0 ? '+' : ''; ?>KES
                                                        <?php echo number_format($period['net'], 0); ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if (isset($period['trend_percent'])): ?>
                                                        <div class="d-flex align-items-center justify-content-center">
                                                            <span
                                                                class="badge bg-<?php echo $period['trend_color']; ?>-transparent">
                                                                <i
                                                                    class="ri-arrow-<?php echo $period['trend_direction']; ?>-line me-1"></i>
                                                                <?php echo number_format($period['trend_percent'], 1); ?>%
                                                            </span>
                                                            <div class="ms-2" style="width: 60px;">
                                                                <div class="progress progress-xs">
                                                                    <div class="progress-bar bg-<?php echo $period['trend_color']; ?>"
                                                                        style="width: <?php echo $period['progress']; ?>%"
                                                                        role="progressbar"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php else: ?>
                                                        <span class="text-muted">N/A</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted"><i class="ri-information-line me-1"></i> Data calculated
                                        daily
                                        from transaction records</span>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="ri-download-2-line me-1"></i> Export Report
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
    <script>
    $(document).ready(() => {
        displayRecentAgrovetTransactions();
        displayCreditSources();
        displayInputCreditAnalytics();
    });

    function displayRecentAgrovetTransactions() {
        let displayRecentAgrovetTransactions = "true";
        $.ajax({
            url: "http://localhost/dfcs/ajax/agrovet-controller/display-recent-agrovet-transactions.php",
            type: 'POST',
            data: {
                displayRecentAgrovetTransactions: displayRecentAgrovetTransactions,
            },
            success: function(data, status) {
                $('#recentAgrovetTransactionsSection').html(data);
            },
        });
    }

    function displayCreditSources() {
        let displayCreditSources = "true";
        $.ajax({
            url: "http://localhost/dfcs/ajax/agrovet-controller/display-credit-sources.php",
            type: 'POST',
            data: {
                displayCreditSources: displayCreditSources,
            },
            success: function(data, status) {
                $('#creditSourcesSection').html(data);
            },
        });
    }

    function displayInputCreditAnalytics() {
        let displayInputCreditAnalytics = "true";
        $.ajax({
            url: "http://localhost/dfcs/ajax/agrovet-controller/display-input-credit-analytics.php",
            type: 'POST',
            data: {
                displayInputCreditAnalytics: displayInputCreditAnalytics,
            },
            success: function(data, status) {
                $('#inputCreditAnalyticsSection').html(data);
            },
        });
    }
    </script>
</body>

</html>