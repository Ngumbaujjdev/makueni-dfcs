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
                        <span class="fs-semibold text-muted pt-5">Produce Management Dashboard</span>
                    </div>
                </div>
                <!-- End::page-header -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                    <h1 class="page-title fw-semibold fs-18 mb-0">
                        <i class="fas fa-history me-2" style="color:#6AA32D;"></i>Payment History
                    </h1>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#"><i class="fas fa-landmark me-1"></i>Banking</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Payment History</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <!-- Payment History Stats Cards -->
                <div class="row mt-2">
                    <!-- Total Payments Processed -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden shadow-sm" style="background:#6AA32D!important;">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span
                                            class="avatar avatar-md avatar-rounded d-flex align-items-center justify-content-center"
                                            style="background-color: white!important;">
                                            <i style="color:#6AA32D" class="fa-solid fa-check-double fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-white mb-0">Total Payments Processed</p>
                                                <?php
                                    // Count all processed payments (both farmers and agrovets)
                                    $query = "SELECT 
                                             (SELECT COUNT(*) FROM farmer_account_transactions WHERE transaction_type = 'credit') +
                                             (SELECT COUNT(*) FROM agrovet_account_transactions WHERE transaction_type = 'credit') +
                                             (SELECT COUNT(*) FROM bank_account_transactions WHERE transaction_type = 'debit') as count";
                                    $result = $app->select_one($query);
                                    $total_payments = ($result) ? $result->count : 0;
                                    ?>
                                                <h4 style="color:wheat;" class="fw-semibold mt-1">
                                                    <i class="fas fa-receipt me-1"></i><?php echo $total_payments ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Value Disbursed -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span
                                            class="avatar avatar-md avatar-rounded d-flex align-items-center justify-content-center"
                                            style="background:#6AA32D;">
                                            <i class="fa-solid fa-money-bill-transfer fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Total Value Disbursed
                                                </p>
                                                <?php
                                    // Calculate total amount paid (farmers + agrovets + other bank payments)
                                    $query = "SELECT 
                                             (SELECT COALESCE(SUM(amount), 0) FROM farmer_account_transactions WHERE transaction_type = 'credit') +
                                             (SELECT COALESCE(SUM(amount), 0) FROM agrovet_account_transactions WHERE transaction_type = 'credit') +
                                             (SELECT COALESCE(SUM(amount), 0) FROM bank_account_transactions WHERE transaction_type = 'debit') as total_disbursed";
                                    $result = $app->select_one($query);
                                    $total_disbursed = ($result) ? number_format($result->total_disbursed, 2) : 0;
                                    ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <i class="fas fa-coins me-1"></i>KES <?php echo $total_disbursed ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recipients Breakdown -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span
                                            class="avatar avatar-md avatar-rounded d-flex align-items-center justify-content-center"
                                            style="background:#6AA32D;">
                                            <i class="fa-solid fa-users fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Recipients Breakdown
                                                </p>
                                                <?php
                                    // Count unique farmers who have received payments
                                    $query_farmers = "SELECT COUNT(DISTINCT fm.id) as count 
                                             FROM farmer_account_transactions fat
                                             JOIN farmer_accounts fa ON fat.farmer_account_id = fa.id
                                             JOIN farmers fm ON fa.farmer_id = fm.id
                                             WHERE fat.transaction_type = 'credit'";
                                    
                                    // Count unique agrovets who have received payments
                                    $query_agrovets = "SELECT COUNT(DISTINCT a.id) as count 
                                             FROM agrovet_account_transactions aat
                                             JOIN agrovet_accounts aa ON aat.agrovet_account_id = aa.id
                                             JOIN agrovets a ON aa.agrovet_id = a.id
                                             WHERE aat.transaction_type = 'credit'";
                                    
                                    $result_farmers = $app->select_one($query_farmers);
                                    $result_agrovets = $app->select_one($query_agrovets);
                                    
                                    $farmers_paid = ($result_farmers) ? $result_farmers->count : 0;
                                    $agrovets_paid = ($result_agrovets) ? $result_agrovets->count : 0;
                                    $total_recipients = $farmers_paid + $agrovets_paid;
                                    ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <i
                                                        class="fas fa-user-check me-1"></i><?php echo $total_recipients ?>
                                                </h4>
                                                <span class="badge"
                                                    style="background-color: rgba(106, 163, 45, 0.2); color: #6AA32D;">
                                                    <i class="fas fa-tractor me-1"></i>Farmers:
                                                    <?php echo $farmers_paid ?>
                                                </span>
                                                <span class="badge"
                                                    style="background-color: rgba(106, 163, 45, 0.2); color: #6AA32D;">
                                                    <i class="fas fa-store me-1"></i>Agrovets:
                                                    <?php echo $agrovets_paid ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Processing Fees Collected -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span
                                            class="avatar avatar-md avatar-rounded d-flex align-items-center justify-content-center"
                                            style="background:#6AA32D;">
                                            <i class="fa-solid fa-percentage fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Processing Fees</p>
                                                <?php
                                    // Calculate processing fees (sum of all transactions * 0.01)
                                    $query = "SELECT 
                                             ((SELECT COALESCE(SUM(amount), 0) FROM farmer_account_transactions WHERE transaction_type = 'credit') +
                                             (SELECT COALESCE(SUM(amount), 0) FROM agrovet_account_transactions WHERE transaction_type = 'credit') +
                                             (SELECT COALESCE(SUM(amount), 0) FROM bank_account_transactions WHERE transaction_type = 'debit')) * 0.01 as processing_fees";
                                    $result = $app->select_one($query);
                                    $processing_fees = ($result) ? number_format($result->processing_fees, 2) : 0;
                                    ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <i class="fas fa-hand-holding-dollar me-1"></i>KES
                                                    <?php echo $processing_fees ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Analysis Section - First Row -->
                <div class="row mt-4">
                    <div class="col-xl-12">
                        <div class="card custom-card shadow-sm">
                            <div class="card-header d-flex align-items-center border-bottom"
                                style="background-color: rgba(106, 163, 45, 0.1);">
                                <div class="card-title mb-0">
                                    <h5 class="mb-0" style="color:#6AA32D;">
                                        <i class="fas fa-chart-line me-2"></i>Payment Trends Analysis
                                    </h5>
                                </div>
                                <div class="ms-auto">
                                    <button class="btn btn-sm" style="background-color: #6AA32D; color: white;"
                                        onclick="refreshPaymentAnalysis()">
                                        <i class="fas fa-sync-alt me-1"></i> Refresh Data
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Monthly Payment Statistics -->
                                    <div class="col-lg-8">
                                        <div class="p-4 border-end">
                                            <h6 class="mb-3" style="color:#6AA32D;">
                                                <i class="fas fa-calendar-alt me-1"></i> Monthly Payment Statistics
                                            </h6>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr style="background-color: rgba(106, 163, 45, 0.1);">
                                                                    <th style="color:#6AA32D;"><i
                                                                            class="fas fa-calendar-month me-1"></i>
                                                                        Month</th>
                                                                    <th style="color:#6AA32D;" class="text-center"><i
                                                                            class="fas fa-receipt me-1"></i>
                                                                        Transactions</th>
                                                                    <th style="color:#6AA32D;" class="text-end"><i
                                                                            class="fas fa-money-bill-transfer me-1"></i>
                                                                        Amount (KES)</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                    // Get last 6 months of payment data
                                                    $query = "SELECT 
                                                            DATE_FORMAT(created_at, '%b %Y') as month,
                                                            COUNT(*) as transaction_count,
                                                            SUM(amount) as total_amount
                                                            FROM (
                                                                SELECT amount, created_at FROM farmer_account_transactions WHERE transaction_type = 'credit'
                                                                UNION ALL
                                                                SELECT amount, created_at FROM agrovet_account_transactions WHERE transaction_type = 'credit'
                                                                UNION ALL
                                                                SELECT amount, created_at FROM bank_account_transactions WHERE transaction_type = 'debit'
                                                            ) as all_transactions
                                                            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                                                            ORDER BY DATE_FORMAT(created_at, '%Y-%m') DESC
                                                            LIMIT 6";
                                                    $month_results = $app->select_all($query);
                                                    
                                                    if ($month_results && count($month_results) > 0) {
                                                        foreach ($month_results as $month_data) {
                                                            echo '<tr>
                                                                <td><i class="fas fa-calendar-week me-1 text-success"></i> '.$month_data->month.'</td>
                                                                <td class="text-center">'.$month_data->transaction_count.'</td>
                                                                <td class="text-end">'.number_format($month_data->total_amount, 2).'</td>
                                                            </tr>';
                                                        }
                                                    } else {
                                                        echo '<tr><td colspan="3" class="text-center">No monthly data available</td></tr>';
                                                    }
                                                    ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-3 text-center">
                                                <div class="col-4">
                                                    <p class="mb-1 text-muted fs-12">Highest Month</p>
                                                    <h5 style="color:#6AA32D;" class="fw-semibold">
                                                        <i class="fas fa-arrow-trend-up me-1"></i>
                                                        <?php
                                            // Calculate highest payment month (combining all payment types)
                                            $query = "SELECT 
                                                    DATE_FORMAT(created_at, '%b %Y') as month,
                                                    SUM(amount) as total
                                                    FROM (
                                                        SELECT amount, created_at FROM farmer_account_transactions WHERE transaction_type = 'credit'
                                                        UNION ALL
                                                        SELECT amount, created_at FROM agrovet_account_transactions WHERE transaction_type = 'credit'
                                                        UNION ALL
                                                        SELECT amount, created_at FROM bank_account_transactions WHERE transaction_type = 'debit'
                                                    ) as all_transactions
                                                    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                                                    ORDER BY total DESC
                                                    LIMIT 1";
                                            $result = $app->select_one($query);
                                            echo ($result) ? $result->month : "N/A";
                                            ?>
                                                    </h5>
                                                </div>
                                                <div class="col-4">
                                                    <p class="mb-1 text-muted fs-12">Average Monthly</p>
                                                    <h5 style="color:#6AA32D;" class="fw-semibold">
                                                        <i class="fas fa-calculator me-1"></i>
                                                        <?php
                                            // Calculate average monthly payment volume
                                            $query = "SELECT 
                                                    AVG(monthly_total) as average
                                                    FROM (
                                                        SELECT 
                                                            DATE_FORMAT(created_at, '%Y-%m') as month,
                                                            SUM(amount) as monthly_total
                                                        FROM (
                                                            SELECT amount, created_at FROM farmer_account_transactions WHERE transaction_type = 'credit'
                                                            UNION ALL
                                                            SELECT amount, created_at FROM agrovet_account_transactions WHERE transaction_type = 'credit'
                                                            UNION ALL
                                                            SELECT amount, created_at FROM bank_account_transactions WHERE transaction_type = 'debit'
                                                        ) as all_transactions
                                                        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                                                    ) as monthly_totals";
                                            $result = $app->select_one($query);
                                            echo ($result && $result->average) ? "KES " . number_format($result->average, 2) : "KES 0.00";
                                            ?>
                                                    </h5>
                                                </div>
                                                <div class="col-4">
                                                    <p class="mb-1 text-muted fs-12">Current Month</p>
                                                    <h5 style="color:#6AA32D;" class="fw-semibold">
                                                        <i class="fas fa-calendar-check me-1"></i>
                                                        <?php
                                            // Calculate current month payment volume
                                            $query = "SELECT 
                                                    SUM(amount) as total
                                                    FROM (
                                                        SELECT amount, created_at FROM farmer_account_transactions 
                                                        WHERE transaction_type = 'credit' AND DATE_FORMAT(created_at, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
                                                        UNION ALL
                                                        SELECT amount, created_at FROM agrovet_account_transactions 
                                                        WHERE transaction_type = 'credit' AND DATE_FORMAT(created_at, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
                                                        UNION ALL
                                                        SELECT amount, created_at FROM bank_account_transactions 
                                                        WHERE transaction_type = 'debit' AND DATE_FORMAT(created_at, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
                                                    ) as current_month_transactions";
                                            $result = $app->select_one($query);
                                            echo ($result && $result->total) ? "KES " . number_format($result->total, 2) : "KES 0.00";
                                            ?>
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Payment Recipients Distribution -->
                                    <div class="col-lg-4">
                                        <div class="p-4">
                                            <h6 class="mb-3 d-flex align-items-center justify-content-between"
                                                style="color:#6AA32D;">
                                                <span><i class="fas fa-users me-2"></i> Payment Recipients</span>
                                                <span class="badge rounded-pill" style="background-color: #6AA32D;">
                                                    <i class="fas fa-chart-pie me-1"></i> Distribution
                                                </span>
                                            </h6>
                                            <div class="border rounded-3 p-4 shadow-sm position-relative overflow-hidden"
                                                style="background: linear-gradient(to right, rgba(106, 163, 45, 0.03), rgba(106, 163, 45, 0.09));">
                                                <!-- Background pattern element -->
                                                <div class="position-absolute"
                                                    style="top: -20px; right: -20px; opacity: 0.05; z-index: 0;">
                                                    <i class="fas fa-money-bill-transfer fa-8x text-success"></i>
                                                </div>

                                                <div class="text-center mb-4 position-relative">
                                                    <span
                                                        class="avatar avatar-lg rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm"
                                                        style="background-color:#6AA32D; width: 70px; height: 70px;">
                                                        <i class="fas fa-money-bill-transfer fa-2x text-white"></i>
                                                    </span>
                                                    <h4 class="mt-3 mb-1" style="color:#6AA32D;">Recipient Breakdown
                                                    </h4>
                                                    <p class="text-muted mb-0 small">Distribution of payments by
                                                        recipient type</p>

                                                    <!-- Total payments indicator -->
                                                    <?php
                // Calculate total payments
                $query = "SELECT 
                         (SELECT COALESCE(SUM(amount), 0) FROM farmer_account_transactions WHERE transaction_type = 'credit') +
                         (SELECT COALESCE(SUM(amount), 0) FROM agrovet_account_transactions WHERE transaction_type = 'credit') +
                         (SELECT COALESCE(SUM(amount), 0) FROM bank_account_transactions WHERE transaction_type = 'debit') as total_payments";
                $result = $app->select_one($query);
                $total_payments = ($result) ? number_format($result->total_payments, 2) : '0.00';
                ?>
                                                    <div class="mt-2">
                                                        <span class="badge rounded-pill fw-normal px-3 py-2 shadow-sm"
                                                            style="background-color: rgba(106, 163, 45, 0.15); color: #6AA32D;">
                                                            <i class="fas fa-money-bill-wave me-1"></i> Total: KES
                                                            <?php echo $total_payments; ?>
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="mt-4 position-relative">
                                                    <!-- Farmers -->
                                                    <div class="card border-0 mb-3 bg-white shadow-sm">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="me-3">
                                                                    <div class="avatar avatar-md rounded-circle d-flex align-items-center justify-content-center"
                                                                        style="background-color:#6AA32D;">
                                                                        <i class="fas fa-tractor text-white"></i>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center mb-1">
                                                                        <div>
                                                                            <h6 class="mb-0">Farmers</h6>
                                                                            <?php
                                        // Calculate percentage and amount paid to farmers
                                        $query = "SELECT 
                                               (SELECT COALESCE(SUM(amount), 0) FROM farmer_account_transactions WHERE transaction_type = 'credit') as farmer_amount,
                                               ((SELECT COALESCE(SUM(amount), 0) FROM farmer_account_transactions WHERE transaction_type = 'credit') +
                                               (SELECT COALESCE(SUM(amount), 0) FROM agrovet_account_transactions WHERE transaction_type = 'credit') +
                                               (SELECT COALESCE(SUM(amount), 0) FROM bank_account_transactions WHERE transaction_type = 'debit' 
                                                   AND description NOT LIKE '%to farmer%' AND description NOT LIKE '%to agrovet%')) as total_amount";
                                        $result = $app->select_one($query);
                                        $farmer_amount = ($result) ? number_format($result->farmer_amount, 2) : '0.00';
                                        $farmer_percentage = ($result && $result->total_amount > 0) ? 
                                            round(($result->farmer_amount / $result->total_amount) * 100) : 0;
                                        ?>
                                                                            <p class="text-muted mb-0 small">KES
                                                                                <?php echo $farmer_amount; ?></p>
                                                                        </div>
                                                                        <span class="badge rounded-pill fs-6 px-3"
                                                                            style="background-color: rgba(106, 163, 45, 0.15); color: #6AA32D;"><?php echo $farmer_percentage; ?>%</span>
                                                                    </div>
                                                                    <div class="progress mt-2"
                                                                        style="height: 8px; border-radius: 4px; background-color: rgba(106, 163, 45, 0.1);">
                                                                        <div class="progress-bar" role="progressbar"
                                                                            style="width: <?php echo $farmer_percentage; ?>%; background-color: #6AA32D; border-radius: 4px;"
                                                                            aria-valuenow="<?php echo $farmer_percentage; ?>"
                                                                            aria-valuemin="0" aria-valuemax="100"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Agrovets -->
                                                    <div class="card border-0 mb-3 bg-white shadow-sm">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="me-3">
                                                                    <div class="avatar avatar-md rounded-circle d-flex align-items-center justify-content-center"
                                                                        style="background-color:#6AA32D;">
                                                                        <i class="fas fa-store text-white"></i>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center mb-1">
                                                                        <div>
                                                                            <h6 class="mb-0">Agrovets</h6>
                                                                            <?php
                                        // Calculate percentage and amount paid to agrovets
                                        $query = "SELECT 
                                               (SELECT COALESCE(SUM(amount), 0) FROM agrovet_account_transactions WHERE transaction_type = 'credit') as agrovet_amount,
                                               ((SELECT COALESCE(SUM(amount), 0) FROM farmer_account_transactions WHERE transaction_type = 'credit') +
                                               (SELECT COALESCE(SUM(amount), 0) FROM agrovet_account_transactions WHERE transaction_type = 'credit') +
                                               (SELECT COALESCE(SUM(amount), 0) FROM bank_account_transactions WHERE transaction_type = 'debit' 
                                                   AND description NOT LIKE '%to farmer%' AND description NOT LIKE '%to agrovet%')) as total_amount";
                                        $result = $app->select_one($query);
                                        $agrovet_amount = ($result) ? number_format($result->agrovet_amount, 2) : '0.00';
                                        $agrovet_percentage = ($result && $result->total_amount > 0) ? 
                                            round(($result->agrovet_amount / $result->total_amount) * 100) : 0;
                                        ?>
                                                                            <p class="text-muted mb-0 small">KES
                                                                                <?php echo $agrovet_amount; ?></p>
                                                                        </div>
                                                                        <span class="badge rounded-pill fs-6 px-3"
                                                                            style="background-color: rgba(106, 163, 45, 0.15); color: #6AA32D;"><?php echo $agrovet_percentage; ?>%</span>
                                                                    </div>
                                                                    <div class="progress mt-2"
                                                                        style="height: 8px; border-radius: 4px; background-color: rgba(106, 163, 45, 0.1);">
                                                                        <div class="progress-bar" role="progressbar"
                                                                            style="width: <?php echo $agrovet_percentage; ?>%; background-color: #6AA32D; border-radius: 4px;"
                                                                            aria-valuenow="<?php echo $agrovet_percentage; ?>"
                                                                            aria-valuemin="0" aria-valuemax="100"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Other Recipients -->
                                                    <div class="card border-0 bg-white shadow-sm">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="me-3">
                                                                    <div class="avatar avatar-md rounded-circle d-flex align-items-center justify-content-center"
                                                                        style="background-color:#6AA32D;">
                                                                        <i class="fas fa-building text-white"></i>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center mb-1">
                                                                        <div>
                                                                            <h6 class="mb-0">Other Recipients</h6>
                                                                            <?php
                                        // Calculate percentage and amount paid to others
                                        $query = "SELECT 
                                               (SELECT COALESCE(SUM(amount), 0) FROM bank_account_transactions 
                                                   WHERE transaction_type = 'debit' 
                                                   AND description NOT LIKE '%to farmer%' 
                                                   AND description NOT LIKE '%to agrovet%') as other_amount,
                                               ((SELECT COALESCE(SUM(amount), 0) FROM farmer_account_transactions WHERE transaction_type = 'credit') +
                                               (SELECT COALESCE(SUM(amount), 0) FROM agrovet_account_transactions WHERE transaction_type = 'credit') +
                                               (SELECT COALESCE(SUM(amount), 0) FROM bank_account_transactions WHERE transaction_type = 'debit' 
                                                   AND description NOT LIKE '%to farmer%' AND description NOT LIKE '%to agrovet%')) as total_amount";
                                        $result = $app->select_one($query);
                                        $other_amount = ($result) ? number_format($result->other_amount, 2) : '0.00';
                                        $other_percentage = ($result && $result->total_amount > 0) ? 
                                            round(($result->other_amount / $result->total_amount) * 100) : 0;
                                        ?>
                                                                            <p class="text-muted mb-0 small">KES
                                                                                <?php echo $other_amount; ?></p>
                                                                        </div>
                                                                        <span class="badge rounded-pill fs-6 px-3"
                                                                            style="background-color: rgba(106, 163, 45, 0.15); color: #6AA32D;"><?php echo $other_percentage; ?>%</span>
                                                                    </div>
                                                                    <div class="progress mt-2"
                                                                        style="height: 8px; border-radius: 4px; background-color: rgba(106, 163, 45, 0.1);">
                                                                        <div class="progress-bar" role="progressbar"
                                                                            style="width: <?php echo $other_percentage; ?>%; background-color: #6AA32D; border-radius: 4px;"
                                                                            aria-valuenow="<?php echo $other_percentage; ?>"
                                                                            aria-valuemin="0" aria-valuemax="100"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Last updated timestamp -->
                                                    <div class="text-center mt-3">
                                                        <small class="text-muted">
                                                            <i class="fas fa-clock me-1"></i> Last updated:
                                                            <?php echo date('M d, Y - h:i A'); ?>
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

                    <!-- Payment Analysis Section - Second Row -->
                    <div class="row mt-4">
                        <div class="col-xl-12">
                            <div class="card custom-card shadow-sm">
                                <div class="card-header d-flex align-items-center border-bottom"
                                    style="background-color: rgba(106, 163, 45, 0.1);">
                                    <div class="card-title mb-0">
                                        <h5 class="mb-0" style="color:#6AA32D;">
                                            <i class="fas fa-money-check-dollar me-2"></i>Payment Categories & Recent
                                            Transactions
                                        </h5>
                                    </div>
                                    <div class="ms-auto">
                                        <a href="#" class="btn btn-sm" style="background-color: #6AA32D; color: white;">
                                            <i class="fas fa-list me-1"></i> View All Transactions
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Payment Categories -->
                                        <div class="col-lg-5">
                                            <div class="p-4 border-end h-100">
                                                <div class="d-flex justify-content-between align-items-center mb-4">
                                                    <h6 class="mb-0" style="color:#6AA32D;">
                                                        <i class="fas fa-tags me-2"></i> Payment Categories
                                                    </h6>
                                                    <span class="badge rounded-pill px-3 py-2"
                                                        style="background-color: rgba(106, 163, 45, 0.15); color: #6AA32D;">
                                                        <i class="fas fa-chart-pie me-1"></i> Distribution
                                                    </span>
                                                </div>

                                                <?php
                            // Get transaction type distribution 
                            // Produce Payments
                            $query = "SELECT 
                                    COUNT(*) as count,
                                    SUM(amount) as total_amount
                                    FROM farmer_account_transactions
                                    WHERE transaction_type = 'credit'
                                    AND description LIKE '%produce sale%'";
                            $produce_payments = $app->select_one($query);
                            
                            // Loan Disbursements
                            $query = "SELECT 
                                    COUNT(*) as count,
                                    SUM(amount) as total_amount
                                    FROM farmer_account_transactions
                                    WHERE transaction_type = 'credit'
                                    AND description LIKE '%loan disbursement%'";
                            $loan_disbursements = $app->select_one($query);
                            
                            // Input Credit Repayments
                            $query = "SELECT 
                                    COUNT(*) as count,
                                    SUM(amount) as total_amount
                                    FROM bank_account_transactions
                                    WHERE transaction_type = 'debit'
                                    AND description LIKE '%input credit repayments%'";
                            $input_repayments = $app->select_one($query);
                            
                            // Other Payments
                            $query = "SELECT 
                                    COUNT(*) as count,
                                    SUM(amount) as total_amount
                                    FROM bank_account_transactions
                                    WHERE transaction_type = 'debit'
                                    AND description NOT LIKE '%to farmer%'
                                    AND description NOT LIKE '%to agrovet%'
                                    AND description NOT LIKE '%input credit repayments%'";
                            $other_payments = $app->select_one($query);
                            
                            // Calculate grand total for percentages
                            $grand_total = 
                                ($produce_payments ? $produce_payments->total_amount : 0) +
                                ($loan_disbursements ? $loan_disbursements->total_amount : 0) +
                                ($input_repayments ? $input_repayments->total_amount : 0) +
                                ($other_payments ? $other_payments->total_amount : 0);
                            
                            // Calculate percentages
                            $produce_percentage = ($grand_total > 0 && $produce_payments) ? 
                                round(($produce_payments->total_amount / $grand_total) * 100) : 0;
                            
                            $loan_percentage = ($grand_total > 0 && $loan_disbursements) ? 
                                round(($loan_disbursements->total_amount / $grand_total) * 100) : 0;
                                
                            $input_percentage = ($grand_total > 0 && $input_repayments) ? 
                                round(($input_repayments->total_amount / $grand_total) * 100) : 0;
                                
                            $other_percentage = ($grand_total > 0 && $other_payments) ? 
                                round(($other_payments->total_amount / $grand_total) * 100) : 0;
                                
                            // Calculate total count of transactions
                            $total_count = 
                                ($produce_payments ? $produce_payments->count : 0) +
                                ($loan_disbursements ? $loan_disbursements->count : 0) +
                                ($input_repayments ? $input_repayments->count : 0) +
                                ($other_payments ? $other_payments->count : 0);
                            ?>

                                                <!-- Card-based payment categories -->
                                                <div class="row g-3">
                                                    <!-- Produce Payments Card -->
                                                    <div class="col-12">
                                                        <div class="card border-0 shadow-sm">
                                                            <div class="card-body p-3">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="avatar avatar-md rounded-circle d-flex align-items-center justify-content-center me-3"
                                                                        style="background-color: #6AA32D;">
                                                                        <i class="fas fa-apple-alt text-white"></i>
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <div
                                                                            class="d-flex justify-content-between align-items-center mb-2">
                                                                            <h6 class="mb-0">Produce Payments</h6>
                                                                            <span class="badge rounded-pill px-3"
                                                                                style="background-color: rgba(106, 163, 45, 0.15); color: #6AA32D;">
                                                                                <?php echo $produce_percentage; ?>%
                                                                            </span>
                                                                        </div>
                                                                        <div
                                                                            class="d-flex justify-content-between align-items-center text-muted small">
                                                                            <span><?php echo ($produce_payments) ? $produce_payments->count : 0; ?>
                                                                                transactions</span>
                                                                            <span>KES
                                                                                <?php echo ($produce_payments) ? number_format($produce_payments->total_amount, 2) : '0.00'; ?></span>
                                                                        </div>
                                                                        <div class="progress mt-2"
                                                                            style="height: 6px; border-radius: 4px;">
                                                                            <div class="progress-bar"
                                                                                style="width: <?php echo $produce_percentage; ?>%; background-color: #6AA32D; border-radius: 4px;">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Loan Disbursements Card -->
                                                    <div class="col-12">
                                                        <div class="card border-0 shadow-sm">
                                                            <div class="card-body p-3">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="avatar avatar-md rounded-circle d-flex align-items-center justify-content-center me-3"
                                                                        style="background-color: #6AA32D;">
                                                                        <i
                                                                            class="fas fa-hand-holding-dollar text-white"></i>
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <div
                                                                            class="d-flex justify-content-between align-items-center mb-2">
                                                                            <h6 class="mb-0">Loan Disbursements</h6>
                                                                            <span class="badge rounded-pill px-3"
                                                                                style="background-color: rgba(106, 163, 45, 0.15); color: #6AA32D;">
                                                                                <?php echo $loan_percentage; ?>%
                                                                            </span>
                                                                        </div>
                                                                        <div
                                                                            class="d-flex justify-content-between align-items-center text-muted small">
                                                                            <span><?php echo ($loan_disbursements) ? $loan_disbursements->count : 0; ?>
                                                                                transactions</span>
                                                                            <span>KES
                                                                                <?php echo ($loan_disbursements) ? number_format($loan_disbursements->total_amount, 2) : '0.00'; ?></span>
                                                                        </div>
                                                                        <div class="progress mt-2"
                                                                            style="height: 6px; border-radius: 4px;">
                                                                            <div class="progress-bar"
                                                                                style="width: <?php echo $loan_percentage; ?>%; background-color: #6AA32D; border-radius: 4px;">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Input Credit Repayments Card -->
                                                    <div class="col-12">
                                                        <div class="card border-0 shadow-sm">
                                                            <div class="card-body p-3">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="avatar avatar-md rounded-circle d-flex align-items-center justify-content-center me-3"
                                                                        style="background-color: #6AA32D;">
                                                                        <i class="fas fa-seedling text-white"></i>
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <div
                                                                            class="d-flex justify-content-between align-items-center mb-2">
                                                                            <h6 class="mb-0">Input Credit Repayments
                                                                            </h6>
                                                                            <span class="badge rounded-pill px-3"
                                                                                style="background-color: rgba(106, 163, 45, 0.15); color: #6AA32D;">
                                                                                <?php echo $input_percentage; ?>%
                                                                            </span>
                                                                        </div>
                                                                        <div
                                                                            class="d-flex justify-content-between align-items-center text-muted small">
                                                                            <span><?php echo ($input_repayments) ? $input_repayments->count : 0; ?>
                                                                                transactions</span>
                                                                            <span>KES
                                                                                <?php echo ($input_repayments) ? number_format($input_repayments->total_amount, 2) : '0.00'; ?></span>
                                                                        </div>
                                                                        <div class="progress mt-2"
                                                                            style="height: 6px; border-radius: 4px;">
                                                                            <div class="progress-bar"
                                                                                style="width: <?php echo $input_percentage; ?>%; background-color: #6AA32D; border-radius: 4px;">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Other Payments Card -->
                                                    <div class="col-12">
                                                        <div class="card border-0 shadow-sm">
                                                            <div class="card-body p-3">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="avatar avatar-md rounded-circle d-flex align-items-center justify-content-center me-3"
                                                                        style="background-color: #6AA32D;">
                                                                        <i class="fas fa-money-bill text-white"></i>
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <div
                                                                            class="d-flex justify-content-between align-items-center mb-2">
                                                                            <h6 class="mb-0">Other Payments</h6>
                                                                            <span class="badge rounded-pill px-3"
                                                                                style="background-color: rgba(106, 163, 45, 0.15); color: #6AA32D;">
                                                                                <?php echo $other_percentage; ?>%
                                                                            </span>
                                                                        </div>
                                                                        <div
                                                                            class="d-flex justify-content-between align-items-center text-muted small">
                                                                            <span><?php echo ($other_payments) ? $other_payments->count : 0; ?>
                                                                                transactions</span>
                                                                            <span>KES
                                                                                <?php echo ($other_payments) ? number_format($other_payments->total_amount, 2) : '0.00'; ?></span>
                                                                        </div>
                                                                        <div class="progress mt-2"
                                                                            style="height: 6px; border-radius: 4px;">
                                                                            <div class="progress-bar"
                                                                                style="width: <?php echo $other_percentage; ?>%; background-color: #6AA32D; border-radius: 4px;">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Total Summary Card -->
                                                    <div class="col-12">
                                                        <div class="card border-0 shadow-sm"
                                                            style="background-color: rgba(106, 163, 45, 0.08);">
                                                            <div class="card-body p-3">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="avatar avatar-md rounded-circle d-flex align-items-center justify-content-center me-3"
                                                                        style="background-color: #6AA32D;">
                                                                        <i class="fas fa-calculator text-white"></i>
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <div
                                                                            class="d-flex justify-content-between align-items-center mb-2">
                                                                            <h6 class="mb-0 fw-bold">Total Payments</h6>
                                                                            <span class="badge rounded-pill px-3"
                                                                                style="background-color: #6AA32D; color: white;">
                                                                                100%
                                                                            </span>
                                                                        </div>
                                                                        <div
                                                                            class="d-flex justify-content-between align-items-center fw-semibold">
                                                                            <span><?php echo $total_count; ?>
                                                                                transactions</span>
                                                                            <span>KES
                                                                                <?php echo number_format($grand_total, 2); ?></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Recent Payments List -->
                                        <div class="col-lg-7">
                                            <div class="p-4 h-100">
                                                <div class="d-flex justify-content-between align-items-center mb-4">
                                                    <h6 class="mb-0" style="color:#6AA32D;">
                                                        <i class="fas fa-history me-2"></i> Recent Transactions
                                                    </h6>
                                                    <div>
                                                        <button class="btn btn-sm shadow-sm border"
                                                            onclick="refreshRecentTransactions()">
                                                            <i class="fas fa-sync-alt" style="color: #6AA32D;"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="table-responsive shadow-sm rounded">
                                                    <table class="table table-hover mb-0">
                                                        <thead>
                                                            <tr style="background-color: rgba(106, 163, 45, 0.1);">
                                                                <th style="color:#6AA32D;"><i
                                                                        class="fas fa-hashtag me-1"></i> Reference</th>
                                                                <th style="color:#6AA32D;"><i
                                                                        class="fas fa-calendar me-1"></i> Date</th>
                                                                <th style="color:#6AA32D;"><i
                                                                        class="fas fa-user me-1"></i> Recipient</th>
                                                                <th style="color:#6AA32D;"><i
                                                                        class="fas fa-tag me-1"></i> Type</th>
                                                                <th style="color:#6AA32D;" class="text-end"><i
                                                                        class="fas fa-money-bill me-1"></i> Amount (KES)
                                                                </th>
                                                                <th style="color:#6AA32D;" class="text-center"><i
                                                                        class="fas fa-info-circle me-1"></i> Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                        // Query to get recent payments - combining all payment types
                                        $query = "SELECT 
                                                'Farmer Payment' as payment_type,
                                                fat.reference_id,
                                                fat.description,
                                                fat.amount,
                                                fat.created_at,
                                                CONCAT(u.first_name, ' ', u.last_name) as recipient_name
                                                FROM farmer_account_transactions fat
                                                JOIN farmer_accounts fa ON fat.farmer_account_id = fa.id
                                                JOIN farmers f ON fa.farmer_id = f.id
                                                JOIN users u ON f.user_id = u.id
                                                WHERE fat.transaction_type = 'credit'
                                                UNION ALL
                                                SELECT 
                                                'Agrovet Payment' as payment_type,
                                                aat.reference_id,
                                                aat.description,
                                                aat.amount,
                                                aat.created_at,
                                                a.name as recipient_name
                                                FROM agrovet_account_transactions aat
                                                JOIN agrovet_accounts aa ON aat.agrovet_account_id = aa.id
                                                JOIN agrovets a ON aa.agrovet_id = a.id
                                                WHERE aat.transaction_type = 'credit'
                                                UNION ALL
                                                SELECT 
                                                'Bank Payment' as payment_type,
                                                bat.reference_id,
                                                bat.description,
                                                bat.amount,
                                                bat.created_at,
                                                'Other Recipient' as recipient_name
                                                FROM bank_account_transactions bat
                                                WHERE bat.transaction_type = 'debit'
                                                AND bat.description NOT LIKE '%to farmer%'
                                                AND bat.description NOT LIKE '%to agrovet%'
                                                ORDER BY created_at DESC
                                                LIMIT 10";
                                        
                                        $recent_payments = $app->select_all($query);
                                        
                                        if ($recent_payments && count($recent_payments) > 0) {
                                            foreach ($recent_payments as $payment) {
                                                // Determine payment icon and class based on payment type
                                                $icon_class = 'fas fa-money-bill-transfer';
                                                $badge_class = 'bg-success';
                                                
                                                if (strpos($payment->payment_type, 'Farmer') !== false) {
                                                    $icon_class = 'fas fa-tractor';
                                                } else if (strpos($payment->payment_type, 'Agrovet') !== false) {
                                                    $icon_class = 'fas fa-store';
                                                    $badge_class = 'bg-primary';
                                                } else {
                                                    $badge_class = 'bg-info';
                                                }
                                                
                                                // Format date
                                                $payment_date = date('d M Y, h:i A', strtotime($payment->created_at));
                                                
                                                echo '<tr>
                                                    <td class="fw-medium">#REF-'.str_pad($payment->reference_id, 5, '0', STR_PAD_LEFT).'</td>
                                                    <td><span class="text-muted small">'.$payment_date.'</span></td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="avatar avatar-xs me-2" style="background-color:#6AA32D;">
                                                                <i class="'.$icon_class.' text-white fs-10"></i>
                                                            </span>
                                                            <span class="text-truncate" style="max-width: 150px;">'.$payment->recipient_name.'</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge '.$badge_class.' rounded-pill">'.$payment->payment_type.'</span>
                                                    </td>
                                                    <td class="text-end fw-semibold">'.number_format($payment->amount, 2).'</td>
                                                    <td class="text-center">
                                                        <span class="badge bg-success-transparent text-success">
                                                            <i class="fas fa-check-circle me-1"></i> Completed
                                                        </span>
                                                    </td>
                                                </tr>';
                                            }
                                        } else {
                                            echo '<tr><td colspan="6" class="text-center">No recent payments found</td></tr>';
                                        }
                                        ?>
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <div class="d-flex justify-content-center mt-4">
                                                    <a href="#" class="btn btn-sm px-4"
                                                        style="background-color: #6AA32D; color: white;">
                                                        <i class="fas fa-search me-1"></i> Browse All Transactions
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Pending Payments Section -->
                <div class="row mt-4">
                    <div class="col-xl-12">
                        <div id="pendingPaymentsSection"></div>
                    </div>
                </div>



            </div>


            <!-- Scroll To Top -->
            <div class=" scrollToTop">
                <span class="arrow"><i class="ri-arrow-up-s-fill fs-20"></i></span>
            </div>
            <div id="responsive-overlay"></div>
            <!-- Scroll To Top -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
                integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
                crossorigin="anonymous" referrerpolicy="no-referrer">
            </script>
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
            <script src="http://localhost/dfcs/assets/js/sticky.js">
            </script>
            <!-- Simplebar JS -->
            <script src="http://localhost/dfcs/assets/libs/simplebar/simplebar.min.js">
            </script>
            <script src="http://localhost/dfcs/assets/js/simplebar.js">
            </script>

            <!-- Color Picker JS -->
            <script src="http://localhost/dfcs/assets/libs/%40simonwep/pickr/pickr.es5.min.js">
            </script>
            <!-- Custom-Switcher JS -->
            <script src="http://localhost/dfcs/assets/js/custom-switcher.min.js">
            </script>

            <!-- Custom JS -->
            <script src="http://localhost/dfcs/assets/js/custom.js">
            </script>
            <!-- Used In Zoomable TIme Series Chart -->
            <script src="http://localhost/dfcs/assets/js/dataseries.js">
            </script>
            <!---Used In Annotations Chart-->
            <script src="http://localhost/dfcs/assets/js/apexcharts-stock-prices.js">
            </script>
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
            <script src="http://localhost/dfcs/assets/js/datatables.js">
            </script>
            <!-- Toastr JS -->
            <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4">
            </script>
            <!-- JavaScript for handling payment history display -->
            <script>
            $(document).ready(() => {
                // Initially load payment history section
                loadPaymentHistory();
            });

            // Function to refresh payment history data
            function refreshPaymentAnalysis() {
                location.reload();
            }

            // Function to display payment history
            function loadPaymentHistory() {
                // Show enhanced loader
                $('#pendingPaymentsSection').html(`
                   <div class="card custom-card shadow-sm">
                       <div class="card-body">
                           <div class="text-center py-5">
                               <div class="spinner-grow" style="width: 3rem; height: 3rem; color: #6AA32D;" role="status">
                                   <span class="visually-hidden">Loading...</span>
                               </div>
                               <div class="mt-3">
                                   <h5 style="color: #6AA32D;"><i class="fas fa-sync-alt fa-spin me-2"></i>Loading payment history...</h5>
                                   <p class="text-muted mb-0">Please wait while we fetch the transaction records</p>
                               </div>
                           </div>
                       </div>
                   </div>
                   `);

                // Fetch payment history data
                $.ajax({
                    url: "http://localhost/dfcs/ajax/payment-controller/display-payment-history.php",
                    type: 'POST',
                    data: {
                        displayPaymentHistory: "true",
                    },
                    success: function(data, status) {
                        $('#pendingPaymentsSection').html(data);
                    },
                    error: function() {
                        $('#pendingPaymentsSection').html(`
            <div class="card custom-card shadow-sm">
                <div class="card-body">
                    <div class="text-center py-4">
                        <i class="fa-solid fa-triangle-exclamation fa-3x mb-3" style="color: #dc3545;"></i>
                        <h5>Error Loading Data</h5>
                        <p class="text-muted">There was a problem loading the payment history data. Please try again.</p>
                        <button class="btn" style="background-color: #6AA32D; color: white;" onclick="loadPaymentHistory()">
                            <i class="fa-solid fa-sync-alt me-1"></i> Retry
                        </button>
                    </div>
                </div>
            </div>
        `);
                    }
                });
            }
            </script>
</body>



</html>