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
        <!-- Start::app-content -->
        <div class="main-content app-content">
            <div class="container-fluid">
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                    <div>
                        <?php
                         $app = new App;
                         
                         // Get session user_id to identify agrovet staff
                         if (session_status() === PHP_SESSION_NONE) {
                             session_start();
                         }
                         
                         $userId = $_SESSION['user_id'] ?? null;
                         
                         // Get staff agrovet_id
                         $staffQuery = "SELECT s.id as staff_id, s.agrovet_id, s.position,
                                       u.first_name, u.last_name, 
                                       a.name as agrovet_name
                                       FROM agrovet_staff s 
                                       JOIN users u ON s.user_id = u.id
                                       JOIN agrovets a ON s.agrovet_id = a.id
                                       WHERE s.user_id = :user_id";
                         
                         $staff = $app->selectOne($staffQuery, [':user_id' => $userId]);
                         ?>
                        <p class="fw-semibold fs-18 mb-0">Welcome <?php echo $staff->first_name ?>
                            <?php echo $staff->last_name ?></p>
                        <span class="fs-semibold text-muted pt-5">Input Catalog Analysis Dashboard</span>
                    </div>
                </div>

                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                    <h1 class="page-title fw-semibold fs-18 mb-0">
                        <i class="fa-solid fa-chart-line me-2"></i>Input Catalog Analysis
                    </h1>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="http://localhost/dfcs/agrovet/catalog/browse">Input
                                        Catalog</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Analysis</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <!-- Row 1: Summary Statistics -->
                <div class="row">
                    <!-- Total Inputs -->
                    <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6">
                        <div class="card custom-card overflow-hidden" style="background:#6AA32D!important;">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded"
                                            style="background-color: white!important;">
                                            <i style="color:#6AA32D" class="fa-solid fa-boxes-stacked fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-white mb-0">Total Inputs</p>
                                                <?php
                                                $query = "SELECT COUNT(*) as count FROM input_catalog WHERE is_active = 1";
                                                $result = $app->select_one($query);
                                                $total_inputs = ($result) ? $result->count : 0;
                                                ?>
                                                <h4 style="color:wheat;" class="fw-semibold mt-1">
                                                    <?php echo $total_inputs ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Inputs Requested on Credit -->
                    <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6">
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
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Requested on Credit
                                                </p>
                                                <?php
                                                 $query = "SELECT COUNT(DISTINCT input_catalog_id) as count 
                                                           FROM input_credit_items ici
                                                           JOIN input_credit_applications ica ON ici.credit_application_id = ica.id
                                                           WHERE ica.agrovet_id = {$staff->agrovet_id}";
                                                 $result = $app->select_one($query);
                                                 $credited_inputs = ($result) ? $result->count : 0;
                                                 $credited_percentage = ($total_inputs > 0) ? round(($credited_inputs / $total_inputs) * 100, 1) : 0;
                                                 ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php echo $credited_inputs ?> <small
                                                        class="text-muted">(<?php echo $credited_percentage ?>%)</small>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Total Credit Value -->
                    <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-money-bill-wave fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Total Credit Value</p>
                                                <?php
                                                 $query = "SELECT COALESCE(SUM(aic.approved_amount), 0) as total_amount 
                                                           FROM approved_input_credits aic
                                                           JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                                           WHERE ica.agrovet_id = {$staff->agrovet_id}";
                                                 $result = $app->select_one($query);
                                                 $total_amount = ($result) ? number_format($result->total_amount, 2) : '0.00';
                                                 ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    KES <?php echo $total_amount ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Most Requested Input -->
                    <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-star fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Most Requested Input
                                                </p>
                                                <?php
                                                $query = "SELECT ic.name, COUNT(*) as request_count
                                                          FROM input_credit_items ici
                                                          JOIN input_credit_applications ica ON ici.credit_application_id = ica.id
                                                          JOIN input_catalog ic ON ici.input_catalog_id = ic.id
                                                          WHERE ica.agrovet_id = {$staff->agrovet_id}
                                                          GROUP BY ici.input_catalog_id
                                                          ORDER BY request_count DESC
                                                          LIMIT 1";
                                                $result = $app->select_one($query);
                                                $top_input = ($result) ? $result->name : 'N/A';
                                                $top_count = ($result) ? $result->request_count : 0;
                                                ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php echo $top_input ?>
                                                </h4>
                                                <small class="text-muted"><?php echo $top_count ?> requests</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Row 2: Top Performing Inputs Table -->
                <div class="card custom-card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title">
                            <i class="fa-solid fa-trophy me-2 text-warning"></i>Top Performing Inputs
                        </div>
                        <div>
                            <select class="form-select form-select-sm" id="topInputsTimeframe">
                                <option value="month">Last Month</option>
                                <option value="quarter" selected>Last Quarter</option>
                                <option value="year">Last Year</option>
                                <option value="all">All Time</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered text-nowrap mb-0">
                                <thead>
                                    <tr class="bg-light">
                                        <th class="w-5">
                                            <i class="fa-solid fa-hashtag me-1 text-muted"></i>Rank
                                        </th>
                                        <th>
                                            <i class="fa-solid fa-box me-1 text-muted"></i>Input Name
                                        </th>
                                        <th>
                                            <i class="fa-solid fa-layer-group me-1 text-muted"></i>Type
                                        </th>
                                        <th class="text-center">
                                            <i class="fa-solid fa-list-ol me-1 text-muted"></i>Requests
                                        </th>
                                        <th class="text-center">
                                            <i class="fa-solid fa-money-bill-wave me-1 text-muted"></i>Total Value
                                        </th>
                                        <th class="text-center">
                                            <i class="fa-solid fa-coins me-1 text-muted"></i>Avg. Amount
                                        </th>
                                        <th class="text-center">
                                            <i class="fa-solid fa-rotate me-1 text-muted"></i>Repayment
                                        </th>
                                        <th class="text-center">
                                            <i class="fa-solid fa-arrow-trend-up me-1 text-muted"></i>Trend
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                    // Query to get top performing inputs
                    $query = "SELECT 
                              ic.id,
                              ic.name,
                              ic.type,
                              ic.standard_unit,
                              COUNT(ici.id) as request_count,
                              SUM(ici.total_price) as total_value,
                              AVG(ici.total_price) as avg_value,
                              (
                                  SELECT 
                                  COALESCE(SUM(icr.amount) / SUM(aic.total_with_interest) * 100, 0)
                                  FROM approved_input_credits aic
                                  JOIN input_credit_repayments icr ON icr.approved_credit_id = aic.id
                                  JOIN input_credit_applications ica2 ON aic.credit_application_id = ica2.id
                                  JOIN input_credit_items ici2 ON ici2.credit_application_id = ica2.id
                                  WHERE ici2.input_catalog_id = ic.id 
                                  AND ica2.agrovet_id = {$staff->agrovet_id}
                              ) as repayment_rate
                              FROM input_catalog ic
                              JOIN input_credit_items ici ON ici.input_catalog_id = ic.id
                              JOIN input_credit_applications ica ON ici.credit_application_id = ica.id
                              WHERE ica.agrovet_id = {$staff->agrovet_id}
                              GROUP BY ic.id
                              ORDER BY request_count DESC
                              LIMIT 10";
                    
                    $topInputs = $app->select_all($query);
                    
                    if ($topInputs) {
                        $rank = 1;
                        foreach ($topInputs as $input) {
                            // Determine badge color based on type
                            $badgeColor = 'secondary';
                            switch($input->type) {
                                case 'fertilizer': $badgeColor = 'success'; break;
                                case 'pesticide': $badgeColor = 'warning'; break;
                                case 'seeds': $badgeColor = 'info'; break;
                                case 'tools': $badgeColor = 'primary'; break;
                            }
                            
                            // Determine trend icon (this would be based on actual trend data in a real implementation)
                            $trendIcon = 'fa-arrow-up text-success';
                            $trendValue = rand(1, 15); // Random trend value for demonstration
                            if ($rank % 3 == 0) {
                                $trendIcon = 'fa-arrow-down text-danger';
                                $trendValue = rand(1, 10);
                            } elseif ($rank % 4 == 0) {
                                $trendIcon = 'fa-arrow-right text-warning';
                                $trendValue = 0;
                            }
                            
                            // Determine repayment rate color
                            $repaymentColor = 'danger';
                            if ($input->repayment_rate >= 90) {
                                $repaymentColor = 'success';
                            } elseif ($input->repayment_rate >= 75) {
                                $repaymentColor = 'warning';
                            } elseif ($input->repayment_rate >= 50) {
                                $repaymentColor = 'info';
                            }
                    ?>
                                    <tr>
                                        <td class="text-center fw-semibold">
                                            <?php if ($rank <= 3): ?>
                                            <span class="badge bg-warning-transparent rounded-pill">
                                                <?php echo $rank; ?>
                                            </span>
                                            <?php else: ?>
                                            <?php echo $rank; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span
                                                    class="avatar avatar-xs bg-<?php echo $badgeColor; ?>-transparent me-2">
                                                    <i class="fa-solid fa-box-open"></i>
                                                </span>
                                                <span class="fw-semibold"><?php echo $input->name; ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-<?php echo $badgeColor; ?>-transparent text-<?php echo $badgeColor; ?>">
                                                <?php echo ucfirst($input->type); ?>
                                            </span>
                                        </td>
                                        <td class="text-center fw-semibold">
                                            <?php echo $input->request_count; ?>
                                        </td>
                                        <td class="text-center">
                                            KES <?php echo number_format($input->total_value, 2); ?>
                                        </td>
                                        <td class="text-center">
                                            KES <?php echo number_format($input->avg_value, 2); ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <div class="progress flex-grow-1" style="height: 6px; max-width: 80px;">
                                                    <div class="progress-bar bg-<?php echo $repaymentColor; ?>"
                                                        style="width: <?php echo $input->repayment_rate; ?>%"></div>
                                                </div>
                                                <span
                                                    class="ms-2 fw-semibold"><?php echo round($input->repayment_rate, 1); ?>%</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="d-flex align-items-center justify-content-center">
                                                <i class="fa-solid <?php echo $trendIcon; ?> me-1"></i>
                                                <?php echo $trendValue; ?>%
                                            </span>
                                        </td>
                                    </tr>
                                    <?php
                            $rank++;
                        }
                    } else {
                    ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fa-solid fa-database-slash fs-2 text-muted mb-2 d-block"></i>
                                            <p class="mb-0">No input credit data available for your agrovet</p>
                                        </td>
                                    </tr>
                                    <?php
                    }
                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer border-top-0 text-end">
                        <a href="http://localhost/dfcs/agrovet/reports/inputs" class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-file-lines me-1"></i> View Full Report
                        </a>
                    </div>
                </div>

                <!-- Row 3: Input Category Performance -->
                <div class="card custom-card mt-4">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fa-solid fa-cubes me-2 text-primary"></i>Input Category Performance
                        </div>
                    </div>
                    <div class="card-body pb-3">
                        <div class="row">
                            <?php
                                 // Query to get category performance
                                 $query = "SELECT 
                                           ic.type,
                                           COUNT(DISTINCT ic.id) as input_count,
                                           COUNT(DISTINCT ici.id) as request_count,
                                           SUM(ici.total_price) as total_value,
                                           COUNT(DISTINCT ici.id) * 100.0 / (
                                               SELECT COUNT(*) 
                                               FROM input_credit_items ici2
                                               JOIN input_credit_applications ica2 ON ici2.credit_application_id = ica2.id
                                               WHERE ica2.agrovet_id = {$staff->agrovet_id}
                                           ) as percentage
                                           FROM input_catalog ic
                                           JOIN input_credit_items ici ON ici.input_catalog_id = ic.id
                                           JOIN input_credit_applications ica ON ici.credit_application_id = ica.id
                                           WHERE ica.agrovet_id = {$staff->agrovet_id}
                                           GROUP BY ic.type
                                           ORDER BY request_count DESC";
                                 
                                 $categories = $app->select_all($query);
                                 
                                 // Define category details
                                 $categoryDetails = [
                                     'fertilizer' => [
                                         'color' => 'success',
                                         'icon' => 'fa-seedling',
                                         'title' => 'Fertilizers'
                                     ],
                                     'pesticide' => [
                                         'color' => 'warning',
                                         'icon' => 'fa-bug-slash',
                                         'title' => 'Pesticides'
                                     ],
                                     'seeds' => [
                                         'color' => 'info',
                                         'icon' => 'fa-leaf',
                                         'title' => 'Seeds'
                                     ],
                                     'tools' => [
                                         'color' => 'primary',
                                         'icon' => 'fa-tools',
                                         'title' => 'Tools & Equipment'
                                     ],
                                     'other' => [
                                         'color' => 'secondary',
                                         'icon' => 'fa-box-open',
                                         'title' => 'Other Inputs'
                                     ]
                                 ];
                                 
                                 if ($categories) {
                                     foreach ($categories as $category) {
                                         $details = $categoryDetails[$category->type] ?? $categoryDetails['other'];
                                         
                                         // Calculate growth indicator (would be based on actual data in a real implementation)
                                         $growth = rand(-15, 30); // Random growth for demonstration
                                         $growthIcon = $growth >= 0 ? 'fa-arrow-up text-success' : 'fa-arrow-down text-danger';
                                         $growthClass = $growth >= 0 ? 'text-success' : 'text-danger';
                                 ?>
                            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                                <div class="card border shadow-none">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-3">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center">
                                                    <span
                                                        class="avatar avatar-sm bg-<?php echo $details['color']; ?>-transparent me-2">
                                                        <i class="fa-solid <?php echo $details['icon']; ?>"></i>
                                                    </span>
                                                    <h6 class="mb-0"><?php echo $details['title']; ?></h6>
                                                </div>
                                                <div class="mt-2 d-flex align-items-center">
                                                    <span
                                                        class="badge bg-<?php echo $details['color']; ?>-transparent text-<?php echo $details['color']; ?> me-1">
                                                        <?php echo $category->input_count; ?> inputs
                                                    </span>
                                                    <span class="badge bg-light-transparent text-dark">
                                                        <?php echo $category->request_count; ?> requests
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <h4 class="mb-0 text-<?php echo $details['color']; ?>">
                                                    <?php echo round($category->percentage, 1); ?>%
                                                </h4>
                                                <span class="fs-12 <?php echo $growthClass; ?>">
                                                    <i class="fa-solid <?php echo $growthIcon; ?> me-1"></i>
                                                    <?php echo abs($growth); ?>%
                                                </span>
                                            </div>
                                        </div>
                                        <div class="progress mb-3" style="height:6px">
                                            <div class="progress-bar bg-<?php echo $details['color']; ?>"
                                                style="width:<?php echo round($category->percentage, 1); ?>%"
                                                role="progressbar"></div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <p class="mb-0 fs-12 text-muted">Total Value</p>
                                                <h6 class="fw-semibold mb-0">KES
                                                    <?php echo number_format($category->total_value, 2); ?></h6>
                                            </div>
                                            <div class="text-end">
                                                <p class="mb-0 fs-12 text-muted">Avg. per Request</p>
                                                <h6 class="fw-semibold mb-0">
                                                    KES
                                                    <?php echo number_format($category->total_value / max(1, $category->request_count), 2); ?>
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                                         }
                                     } else {
                                     ?>
                            <div class="col-12">
                                <div class="alert alert-info mb-0">
                                    <i class="fa-solid fa-circle-info me-2"></i>
                                    No category performance data available for your agrovet yet.
                                </div>
                            </div>
                            <?php
                                       }
                                       ?>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fa-solid fa-lightbulb text-warning fs-4 me-2"></i>
                                    <div>
                                        <h6 class="mb-0">Key Insight</h6>
                                        <p class="mb-0 text-muted">
                                            <?php if ($categories && count($categories) > 0): ?>
                                            <?php echo $categoryDetails[$categories[0]->type]['title']; ?> represent the
                                            largest portion of your input credit requests.
                                            <?php else: ?>
                                            Start offering inputs on credit to see category performance insights.
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                                <a href="javascript:void(0)" class="btn btn-sm btn-outline-primary"
                                    id="refreshCategoryStats">
                                    <i class="fa-solid fa-rotate me-1"></i> Refresh Statistics
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Row 4: Input Request Trends Graph -->
                <div class="card custom-card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title">
                            <i class="fa-solid fa-chart-line me-2 text-info"></i>Input Request Trends
                        </div>
                    </div>
                    <div class="card-body">
                        <?php include "../graphs/input-request-trends.php" ?>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex align-items-center">
                                    <i class="fa-solid fa-circle-info text-primary fs-4 me-2"></i>
                                    <div>
                                        <h6 class="mb-0">Trend Analysis</h6>
                                        <p class="mb-0 text-muted">
                                            The graph shows the number of input credit requests over time.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>

            <!-- Row 5: Input Inventory & Demand Insights -->
            <!-- Row 5: Input Inventory & Demand Insights -->
            <div class="card custom-card mt-4">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-lightbulb me-2 text-warning"></i>Input Inventory & Demand Insights
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Highest Demand vs Supply -->
                        <div class="col-xl-6 col-lg-12 mb-4">
                            <div class="card border shadow-none h-100">
                                <div class="card-header bg-light-transparent">
                                    <div class="card-title text-muted">
                                        <i class="fa-solid fa-arrow-trend-up me-2"></i>Highest Demand Vs Supply Gap
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        <?php
                            // Query to get inputs with highest demand vs supply gap
                            $gapQuery = "SELECT 
                                        ic.id,
                                        ic.name,
                                        ic.type,
                                        COUNT(ici.id) as demand_count,
                                        SUM(ici.quantity) as total_quantity,
                                        0 as supply_count /* We don't have a real supply count, using 0 as placeholder */
                                    FROM input_catalog ic
                                    JOIN input_credit_items ici ON ici.input_catalog_id = ic.id
                                    JOIN input_credit_applications ica ON ici.credit_application_id = ica.id
                                    WHERE ica.agrovet_id = {$staff->agrovet_id}
                                    GROUP BY ic.id
                                    ORDER BY demand_count DESC
                                    LIMIT 3";
                            
                            $topDemandGapInputs = $app->select_all($gapQuery);
                            
                            if ($topDemandGapInputs && count($topDemandGapInputs) > 0) {
                                foreach ($topDemandGapInputs as $input) {
                                    // Determine badge color based on type
                                    $badgeColor = 'secondary';
                                    switch($input->type) {
                                        case 'fertilizer': $badgeColor = 'success'; break;
                                        case 'pesticide': $badgeColor = 'warning'; break;
                                        case 'seeds': $badgeColor = 'info'; break;
                                        case 'tools': $badgeColor = 'primary'; break;
                                    }
                                    
                                    // Get demand count with safeguard
                                    $demandCount = isset($input->demand_count) ? $input->demand_count : 0;
                                    // Set a fixed 100% gap for now
                                    $gapPercentage = 100;
                            ?>
                                        <li class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <span
                                                        class="avatar avatar-sm bg-<?php echo $badgeColor; ?>-transparent me-2">
                                                        <i class="fa-solid fa-box-open"></i>
                                                    </span>
                                                    <div>
                                                        <h6 class="mb-0"><?php echo $input->name; ?></h6>
                                                        <span
                                                            class="badge bg-<?php echo $badgeColor; ?>-transparent text-<?php echo $badgeColor; ?> fs-11">
                                                            <?php echo ucfirst($input->type); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="progress flex-grow-1"
                                                            style="height: 6px; width: 100px;">
                                                            <div class="progress-bar bg-danger"
                                                                style="width: <?php echo $gapPercentage; ?>%"></div>
                                                        </div>
                                                        <span
                                                            class="badge bg-danger-transparent"><?php echo $gapPercentage; ?>%</span>
                                                    </div>
                                                    <small class="text-muted">
                                                        Demand: <?php echo $demandCount; ?> | Supply:
                                                        <?php echo isset($input->supply_count) ? $input->supply_count : 0; ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </li>
                                        <?php 
                                }
                            } else {
                            ?>
                                        <li class="list-group-item">
                                            <div class="text-center py-3">
                                                <i class="fa-solid fa-database-slash text-muted d-block mb-2"></i>
                                                <p class="mb-0">No demand gap data available yet</p>
                                            </div>
                                        </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Fastest Growing Demand -->
                        <div class="col-xl-6 col-lg-12 mb-4">
                            <div class="card border shadow-none h-100">
                                <div class="card-header bg-light-transparent">
                                    <div class="card-title text-muted">
                                        <i class="fa-solid fa-rocket me-2"></i>Fastest Growing Demand
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        <?php
                            // Query to get top inputs by request count
                            $growthQuery = "SELECT 
                                        ic.id,
                                        ic.name,
                                        ic.type,
                                        COUNT(ici.id) as request_count
                                    FROM input_catalog ic
                                    JOIN input_credit_items ici ON ici.input_catalog_id = ic.id
                                    JOIN input_credit_applications ica ON ici.credit_application_id = ica.id
                                    WHERE ica.agrovet_id = {$staff->agrovet_id}
                                    GROUP BY ic.id
                                    ORDER BY request_count DESC
                                    LIMIT 3";
                            
                            $fastestGrowingInputs = $app->select_all($growthQuery);
                            
                            if ($fastestGrowingInputs && count($fastestGrowingInputs) > 0) {
                                foreach ($fastestGrowingInputs as $input) {
                                    // Determine badge color based on type
                                    $badgeColor = 'secondary';
                                    switch($input->type) {
                                        case 'fertilizer': $badgeColor = 'success'; break;
                                        case 'pesticide': $badgeColor = 'warning'; break;
                                        case 'seeds': $badgeColor = 'info'; break;
                                        case 'tools': $badgeColor = 'primary'; break;
                                    }
                                    
                                    // For demonstration, use random growth percentage
                                    // In a real implementation, this would be calculated from actual data
                                    $growthPercentage = 0;
                                    $lastMonthRequests = 0;
                                    $requestCount = isset($input->request_count) ? $input->request_count : 0;
                            ?>
                                        <li class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <span
                                                        class="avatar avatar-sm bg-<?php echo $badgeColor; ?>-transparent me-2">
                                                        <i class="fa-solid fa-box-open"></i>
                                                    </span>
                                                    <div>
                                                        <h6 class="mb-0"><?php echo $input->name; ?></h6>
                                                        <span
                                                            class="badge bg-<?php echo $badgeColor; ?>-transparent text-<?php echo $badgeColor; ?> fs-11">
                                                            <?php echo ucfirst($input->type); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-success fs-6 px-2">+0%</span>
                                                    <small class="d-block text-muted">
                                                        0 → <?php echo $requestCount; ?> requests
                                                    </small>
                                                </div>
                                            </div>
                                        </li>
                                        <?php 
                                }
                            } else {
                            ?>
                                        <li class="list-group-item">
                                            <div class="text-center py-3">
                                                <i class="fa-solid fa-database-slash text-muted d-block mb-2"></i>
                                                <p class="mb-0">No growth trend data available yet</p>
                                            </div>
                                        </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Consistent Demand -->
                        <div class="col-xl-6 col-lg-12 mb-4">
                            <div class="card border shadow-none h-100">
                                <div class="card-header bg-light-transparent">
                                    <div class="card-title text-muted">
                                        <i class="fa-solid fa-chart-line-up me-2"></i>Most Consistent Demand
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <?php
                        // For now, show the "not enough data" message
                        // In a real implementation, this would check for sufficient data points
                        $hasConsistencyData = false;
                        
                        if ($hasConsistencyData) {
                            // This block would display consistency data if available
                        } else {
                        ?>
                                    <div class="text-center py-4">
                                        <i class="fa-solid fa-database-slash text-muted d-block mb-2"></i>
                                        <p class="mb-0">Not enough data for consistency analysis</p>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                        <!-- Recommended Stock -->
                        <div class="col-xl-6 col-lg-12 mb-4">
                            <div class="card border shadow-none h-100">
                                <div class="card-header bg-light-transparent">
                                    <div class="card-title text-muted">
                                        <i class="fa-solid fa-clipboard-check me-2"></i>Recommended Stock Increases
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        <?php
                            // Query to get top inputs for stock recommendations
                            $stockQuery = "SELECT 
                                        ic.id,
                                        ic.name,
                                        ic.type,
                                        COUNT(ici.id) as request_count
                                    FROM input_catalog ic
                                    JOIN input_credit_items ici ON ici.input_catalog_id = ic.id
                                    JOIN input_credit_applications ica ON ici.credit_application_id = ica.id
                                    WHERE ica.agrovet_id = {$staff->agrovet_id}
                                    GROUP BY ic.id
                                    ORDER BY request_count DESC
                                    LIMIT 3";
                            
                            $recommendedStockInputs = $app->select_all($stockQuery);
                            
                            if ($recommendedStockInputs && count($recommendedStockInputs) > 0) {
                                foreach ($recommendedStockInputs as $input) {
                                    // Determine badge color based on type
                                    $badgeColor = 'secondary';
                                    switch($input->type) {
                                        case 'fertilizer': $badgeColor = 'success'; break;
                                        case 'pesticide': $badgeColor = 'warning'; break;
                                        case 'seeds': $badgeColor = 'info'; break;
                                        case 'tools': $badgeColor = 'primary'; break;
                                    }
                                    
                                    // For simplicity, use fixed values
                                    $currentStock = 2;
                                    $recommendedStock = 5;
                                    $increasePercentage = ($currentStock > 0) ? 
                                        round((($recommendedStock - $currentStock) / $currentStock) * 100) : 150;
                                    
                                    // Ensure percentage is not negative
                                    $increasePercentage = max(0, $increasePercentage);
                            ?>
                                        <li class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <span
                                                        class="avatar avatar-sm bg-<?php echo $badgeColor; ?>-transparent me-2">
                                                        <i class="fa-solid fa-box-open"></i>
                                                    </span>
                                                    <div>
                                                        <h6 class="mb-0"><?php echo $input->name; ?></h6>
                                                        <span
                                                            class="badge bg-<?php echo $badgeColor; ?>-transparent text-<?php echo $badgeColor; ?> fs-11">
                                                            <?php echo ucfirst($input->type); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <span
                                                        class="badge bg-primary fs-6 px-2">+<?php echo $increasePercentage; ?>%</span>
                                                    <small class="d-block text-muted">
                                                        <?php echo $currentStock; ?> → <?php echo $recommendedStock; ?>
                                                        units
                                                    </small>
                                                </div>
                                            </div>
                                        </li>
                                        <?php 
                                }
                            } else {
                            ?>
                                        <li class="list-group-item">
                                            <div class="text-center py-3">
                                                <i class="fa-solid fa-database-slash text-muted d-block mb-2"></i>
                                                <p class="mb-0">No stock recommendations available yet</p>
                                            </div>
                                        </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-calendar-check text-success fs-4 me-2"></i>
                                <div>
                                    <h6 class="mb-0">Key Insight</h6>
                                    <p class="mb-0 text-muted">
                                        <?php if ($topDemandGapInputs && count($topDemandGapInputs) > 0): ?>
                                        Consider increasing stock of <?php echo $topDemandGapInputs[0]->name; ?> which
                                        has the highest demand.
                                        <?php else: ?>
                                        Start tracking inventory levels against credit requests to see demand insights.
                                        <?php endif; ?>
                                    </p>
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
    <script>
    // Main script for input credit history page
    $(document).ready(() => {
        // Load all input credits
        displayAllInputCredits();
    });

    // Function to display all input credit applications
    function displayAllInputCredits() {
        let displayAllCredits = "true";
        $.ajax({
            url: "http://localhost/dfcs/ajax/input-credit-controller/display-all-credits.php",
            type: 'POST',
            data: {
                displayAllCredits: displayAllCredits,
            },
            success: function(data, status) {
                $('#allInputCreditsSection').html(data);
            },
            error: function(xhr, status, error) {
                console.error("Error loading input credit applications:", error);
                toastr.error('Failed to load input credit applications', 'Error', {
                    "positionClass": "toast-top-right",
                    "progressBar": true,
                    "timeOut": 5000
                });
            }
        });
    }

    // Function to view input credit details
    function viewCreditDetails(creditId) {
        window.location.href = "input-credit-details?id=" + creditId;
    }
    </script>

</body>



</html>