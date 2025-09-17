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
                        <span class="fs-semibold text-muted pt-5">Produce</span>
                    </div>
                </div>

                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                    <h1 class="page-title fw-semibold fs-18 mb-0">Delivery Report Overview</h1>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Deliveries</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Report</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <!-- Delivery Reports Key Metrics -->
                <div class="row mt-2">
                    <?php
                         // Query to get total deliveries count
                         $deliveries_count_query = "SELECT COUNT(*) as total_deliveries FROM produce_deliveries";
                         $deliveries_count = $app->select_one($deliveries_count_query);
                         
                         // Query to get total quantity
                         $total_quantity_query = "SELECT SUM(quantity) as total_quantity FROM produce_deliveries";
                         $total_quantity = $app->select_one($total_quantity_query);
                         
                         // Query to get total value
                         $total_value_query = "SELECT SUM(total_value) as total_value FROM produce_deliveries";
                         $total_value = $app->select_one($total_value_query);
                         
                         // Query to get average quality grade
                         $quality_query = "SELECT 
                                             COUNT(CASE WHEN quality_grade = 'A' THEN 1 END) as grade_a,
                                             COUNT(CASE WHEN quality_grade = 'B' THEN 1 END) as grade_b,
                                             COUNT(CASE WHEN quality_grade = 'C' THEN 1 END) as grade_c,
                                             COUNT(*) as total_graded
                                           FROM produce_deliveries 
                                           WHERE quality_grade IS NOT NULL";
                         $quality_stats = $app->select_one($quality_query);
                         
                         // Calculate quality score (A=3, B=2, C=1)
                         $quality_score = 0;
                         $quality_percent = 0;
                         if ($quality_stats->total_graded > 0) {
                             $quality_score = (($quality_stats->grade_a * 3) + ($quality_stats->grade_b * 2) + ($quality_stats->grade_c * 1)) / $quality_stats->total_graded;
                             $quality_percent = ($quality_score / 3) * 100; // Convert to percentage
                         }
                         ?>

                    <!-- Total Deliveries -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden" style="background:#6AA32D!important;">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded"
                                            style="background-color: white!important;">
                                            <i style="color:#6AA32D" class="fa-solid fa-truck-fast fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-white mb-0">Total Deliveries</p>
                                                <h4 style="color:wheat;" class="fw-semibold mt-1">
                                                    <?php echo number_format($deliveries_count->total_deliveries ?? 0); ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Quantity -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-weight-scale fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Total Quantity</p>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php echo number_format($total_quantity->total_quantity ?? 0, 2); ?>
                                                    KGs
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Value -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
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
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Total Value</p>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    KES <?php echo number_format($total_value->total_value ?? 0, 2); ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quality Score -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-award fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Quality Score</p>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php echo number_format($quality_score, 1); ?> / 3.0
                                                    <small
                                                        class="text-muted fs-12">(<?php echo round($quality_percent); ?>%)</small>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <div class="progress progress-xs">
                                        <div class="progress-bar bg-success"
                                            style="width: <?php echo $quality_percent; ?>%" role="progressbar"></div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mt-1">
                                        <small class="text-muted">
                                            Grade A: <?php echo number_format($quality_stats->grade_a ?? 0); ?>
                                        </small>
                                        <small class="text-muted">
                                            Grade B: <?php echo number_format($quality_stats->grade_b ?? 0); ?>
                                        </small>
                                        <small class="text-muted">
                                            Grade C: <?php echo number_format($quality_stats->grade_c ?? 0); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <!-- Delivery Trends Over Time Graph -->
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-header">
                                <div class="card-title">
                                    <i class="ri-line-chart-line me-2"></i> Delivery Trends Over Time
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="deliveryTrendsChart"></div>
                                <?php include "../graphs/deliveryTrendsChart.php" ?>
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Value by Product Type Graph -->
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-header">
                                <div class="card-title">
                                    <i class="ri-bar-chart-grouped-line me-2"></i> Value Distribution by Product
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="productValueChart"></div>
                                <?php include "../graphs/productValueChart.php" ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!--row 3 -->
                <!-- Row 3: Delivery Status Breakdown -->
                <div class="row mt-4">
                    <!-- Status Distribution Card -->
                    <div class="col-xl-6 col-md-6">
                        <div class="card custom-card">
                            <div class="card-header">
                                <div class="card-title">
                                    <i class="ri-pie-chart-line me-2"></i>Status Distribution
                                </div>
                            </div>
                            <div class="card-body">
                                <?php
                                     // Query to get status distribution
                                     $status_query = "SELECT 
                                                        status,
                                                        COUNT(*) as count,
                                                        ROUND((COUNT(*) / (SELECT COUNT(*) FROM produce_deliveries)) * 100, 1) as percentage
                                                      FROM produce_deliveries
                                                      GROUP BY status
                                                      ORDER BY count DESC";
                                     $status_data = $app->select_all($status_query);
                                     
                                     // Initialize counters
                                     $total_deliveries = 0;
                                     $status_counts = [
                                         'pending' => 0,
                                         'rejected' => 0,
                                         'verified' => 0,
                                         'sold' => 0,
                                         'paid' => 0
                                     ];
                                     
                                     // Get counts for each status
                                     foreach ($status_data as $status) {
                                         $status_counts[$status->status] = $status->count;
                                         $total_deliveries += $status->count;
                                     }
                                     
                                     // Status color mapping
                                     $status_colors = [
                                         'pending' => '#FFC107',    // Amber
                                         'rejected' => '#E74C3C',   // Red
                                         'verified' => '#3498DB',   // Blue
                                         'sold' => '#2ECC71',       // Green
                                         'paid' => '#9B59B6'        // Purple
                                     ];
                                     
                                     // Status display names
                                     $status_names = [
                                         'pending' => 'Pending',
                                         'rejected' => 'Rejected',
                                         'verified' => 'Verified',
                                         'sold' => 'Sold',
                                         'paid' => 'Paid'
                                     ];
                                     ?>

                                <div class="d-flex justify-content-between mb-3">
                                    <h3 class="fw-semibold"><?php echo number_format($total_deliveries); ?></h3>
                                    <span class="badge bg-primary-transparent">Total Deliveries</span>
                                </div>

                                <div class="status-bars">
                                    <?php foreach ($status_counts as $status => $count): ?>
                                    <?php 
                                    $percentage = ($total_deliveries > 0) ? ($count / $total_deliveries) * 100 : 0;
                                    ?>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <div>
                                                <span class="badge"
                                                    style="background-color: <?php echo $status_colors[$status]; ?>;">
                                                    <i class="fa-solid fa-circle-dot me-1 fs-10"></i>
                                                    <?php echo $status_names[$status]; ?>
                                                </span>
                                            </div>
                                            <div>
                                                <span class="fw-semibold"><?php echo number_format($count); ?></span>
                                                <span
                                                    class="text-muted ms-1">(<?php echo round($percentage, 1); ?>%)</span>
                                            </div>
                                        </div>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar" role="progressbar"
                                                style="width: <?php echo $percentage; ?>%; background-color: <?php echo $status_colors[$status]; ?>;"
                                                aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quality Grade Analysis Card -->
                    <div class="col-xl-6 col-md-6">
                        <div class="card custom-card">
                            <div class="card-header">
                                <div class="card-title">
                                    <i class="ri-award-line me-2"></i>Quality Grade Analysis
                                </div>
                            </div>
                            <div class="card-body">
                                <?php
                                      // Query to get quality grade distribution
                                      $quality_query = "SELECT 
                                                         quality_grade,
                                                         COUNT(*) as count,
                                                         ROUND(AVG(total_value), 2) as avg_value,
                                                         ROUND((COUNT(*) / (SELECT COUNT(*) FROM produce_deliveries WHERE quality_grade IS NOT NULL)) * 100, 1) as percentage
                                                       FROM produce_deliveries
                                                       WHERE quality_grade IS NOT NULL
                                                       GROUP BY quality_grade
                                                       ORDER BY quality_grade";
                                      $quality_data = $app->select_all($quality_query);
                                      
                                      // Initialize counters
                                      $total_graded = 0;
                                      $grade_counts = [
                                          'A' => 0,
                                          'B' => 0,
                                          'C' => 0
                                      ];
                                      $grade_values = [
                                          'A' => 0,
                                          'B' => 0,
                                          'C' => 0
                                      ];
                                      
                                      // Get counts for each grade
                                      foreach ($quality_data as $grade) {
                                          if (isset($grade_counts[$grade->quality_grade])) {
                                              $grade_counts[$grade->quality_grade] = $grade->count;
                                              $grade_values[$grade->quality_grade] = $grade->avg_value;
                                              $total_graded += $grade->count;
                                          }
                                      }
                                      
                                      // Grade color mapping
                                      $grade_colors = [
                                          'A' => '#2ECC71',  // Green
                                          'B' => '#3498DB',  // Blue
                                          'C' => '#F39C12'   // Orange
                                      ];
                                      ?>

                                <div class="row mb-4">
                                    <div class="col-md-4 col-sm-4 text-center">
                                        <div class="p-3 rounded-3" style="background-color: rgba(46, 204, 113, 0.15);">
                                            <div class="avatar avatar-md avatar-rounded mb-2"
                                                style="background-color: #2ECC71;">
                                                <span class="fw-bold text-white">A</span>
                                            </div>
                                            <h5 class="mb-0"><?php echo number_format($grade_counts['A']); ?></h5>
                                            <p class="fs-11 text-muted mb-0">Grade A</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4 text-center">
                                        <div class="p-3 rounded-3" style="background-color: rgba(52, 152, 219, 0.15);">
                                            <div class="avatar avatar-md avatar-rounded mb-2"
                                                style="background-color: #3498DB;">
                                                <span class="fw-bold text-white">B</span>
                                            </div>
                                            <h5 class="mb-0"><?php echo number_format($grade_counts['B']); ?></h5>
                                            <p class="fs-11 text-muted mb-0">Grade B</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4 text-center">
                                        <div class="p-3 rounded-3" style="background-color: rgba(243, 156, 18, 0.15);">
                                            <div class="avatar avatar-md avatar-rounded mb-2"
                                                style="background-color: #F39C12;">
                                                <span class="fw-bold text-white">C</span>
                                            </div>
                                            <h5 class="mb-0"><?php echo number_format($grade_counts['C']); ?></h5>
                                            <p class="fs-11 text-muted mb-0">Grade C</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <h6 class="mb-3">Average Value by Grade</h6>
                                    <?php foreach ($grade_counts as $grade => $count): ?>
                                    <?php if ($count > 0): ?>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <div>
                                                <span class="badge"
                                                    style="background-color: <?php echo $grade_colors[$grade]; ?>;">
                                                    Grade <?php echo $grade; ?>
                                                </span>
                                            </div>
                                            <div>
                                                <span class="fw-semibold">KES
                                                    <?php echo number_format($grade_values[$grade], 0); ?></span>
                                            </div>
                                        </div>
                                        <div class="progress progress-sm">
                                            <?php 
                                               $max_value = max($grade_values['A'], $grade_values['B'], $grade_values['C']);
                                               $percentage = ($max_value > 0) ? ($grade_values[$grade] / $max_value) * 100 : 0;
                                               ?>
                                            <div class="progress-bar" role="progressbar"
                                                style="width: <?php echo $percentage; ?>%; background-color: <?php echo $grade_colors[$grade]; ?>;"
                                                aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Farmers Card -->
                    <div class="col-xl-12 col-md-12">
                        <div class="card custom-card">
                            <div class="card-header">
                                <div class="card-title">
                                    <i class="ri-user-star-line me-2"></i>Top Farmers
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <?php
                                   // Query to get top farmers by delivery value
                                   $farmers_query = "SELECT 
                                    f.id as farmer_id,
                                    CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                                    COUNT(pd.id) as delivery_count,
                                    SUM(pd.total_value) as total_value,
                                    AVG(CASE WHEN pd.quality_grade = 'A' THEN 3 WHEN pd.quality_grade = 'B' THEN 2 WHEN pd.quality_grade = 'C' THEN 1 ELSE NULL END) as avg_quality
                                  FROM produce_deliveries pd
                                  JOIN farm_products fp ON pd.farm_product_id = fp.id
                                  JOIN farms fm ON fp.farm_id = fm.id
                                  JOIN farmers f ON fm.farmer_id = f.id
                                  JOIN users u ON f.user_id = u.id
                                  GROUP BY f.id, farmer_name
                                  ORDER BY total_value DESC
                                  LIMIT 5";
                                $top_farmers = $app->select_all($farmers_query);
                                ?>

                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Farmer</th>
                                                <th class="text-center">Deliveries</th>
                                                <th class="text-end">Total Value</th>
                                                <th class="text-center">Avg. Quality</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($top_farmers as $index => $farmer): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm avatar-rounded me-2"
                                                            style="background-color: <?php echo sprintf('#%06X', mt_rand(0, 0xFFFFFF)); ?>;">
                                                            <span
                                                                class="text-white"><?php echo substr($farmer->farmer_name, 0, 1); ?></span>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0"><?php echo $farmer->farmer_name; ?></h6>
                                                            <span
                                                                class="fs-11 text-muted">FID-<?php echo $farmer->farmer_id; ?></span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-light text-dark">
                                                        <?php echo $farmer->delivery_count; ?>
                                                    </span>
                                                </td>
                                                <td class="text-end fw-semibold">
                                                    KES <?php echo number_format($farmer->total_value, 0); ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php 
                                                      $quality = round($farmer->avg_quality, 1);
                                                      $quality_color = '';
                                                      if ($quality >= 2.5) $quality_color = 'success';
                                                      else if ($quality >= 1.5) $quality_color = 'info';
                                                      else $quality_color = 'warning';
                                                      ?>
                                                    <span class="badge bg-<?php echo $quality_color; ?>-transparent">
                                                        <?php echo $quality; ?>/3.0
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>

                                            <?php if (count($top_farmers) === 0): ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="ri-information-line fs-24 mb-2 d-block"></i>
                                                        No farmer data available
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <?php if (count($top_farmers) > 0): ?>
                                <div class="bg-light p-3 text-center border-top">
                                    <a href="http://localhost/dfcs/sacco/produce/produce-deliveries"
                                        class="text-primary">
                                        <i class="ri-eye-line me-1"></i> View All Farmers
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- row 4 -->
                <!-- Row 4: Financial Impact -->
                <div class="row mt-4">
                    <!-- Commission Summary Card -->
                    <div class="col-xl-12 col-md-12">
                        <div class="card custom-card">
                            <div class="card-header">
                                <div class="card-title">
                                    <i class="ri-funds-line me-2"></i>Commission Summary
                                </div>
                            </div>
                            <div class="card-body">
                                <?php
                                  // Query to get commission data
                                  $commission_query = "SELECT 
                                                         SUM(s.amount) as total_commission,
                                                         COUNT(DISTINCT pd.id) as processed_deliveries,
                                                         AVG(s.amount) as avg_commission
                                                       FROM sacco_account_transactions s
                                                       JOIN produce_deliveries pd ON s.reference_id = pd.id
                                                       WHERE s.description LIKE '%Commission from produce sale%'";
                                  $commission_data = $app->select_one($commission_query);
                                  
                                  // Calculate commission percentage and total sales value
                                  $total_sales_query = "SELECT SUM(total_value) as total_sales FROM produce_deliveries WHERE status IN ('sold', 'paid')";
                                  $total_sales = $app->select_one($total_sales_query);
                                  
                                  $commission_percentage = 0;
                                  if ($total_sales->total_sales > 0 && $commission_data->total_commission > 0) {
                                      $commission_percentage = ($commission_data->total_commission / $total_sales->total_sales) * 100;
                                  }
                                  
                                  // Get monthly commission data for the chart
                                  $monthly_commission_query = "SELECT 
                                                                DATE_FORMAT(created_at, '%b') as month,
                                                                SUM(amount) as commission
                                                              FROM sacco_account_transactions
                                                              WHERE description LIKE '%Commission from produce sale%'
                                                              AND YEAR(created_at) = YEAR(CURRENT_DATE())
                                                              GROUP BY DATE_FORMAT(created_at, '%b'), MONTH(created_at)
                                                              ORDER BY MONTH(created_at)";
                                  $monthly_commissions = $app->select_all($monthly_commission_query);
                                  
                                  $chart_months = [];
                                  $chart_values = [];
                                  
                                  foreach ($monthly_commissions as $item) {
                                      $chart_months[] = $item->month;
                                      $chart_values[] = round($item->commission, 0);
                                  }
                                  ?>

                                <!-- Commission Key Metrics -->
                                <div class="d-flex align-items-center mb-4">
                                    <div class="me-4">
                                        <span class="avatar avatar-md avatar-rounded"
                                            style="background-color: #6AA32D;">
                                            <i class="fa-solid fa-hand-holding-dollar fs-16"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h3 class="fw-semibold mb-0">KES
                                            <?php echo number_format($commission_data->total_commission ?? 0, 0); ?>
                                        </h3>
                                        <p class="text-muted mb-0">Total Commission Earned</p>
                                    </div>
                                    <div class="ms-auto text-end">
                                        <h5 class="mb-0"><?php echo number_format($commission_percentage, 1); ?>%</h5>
                                        <p class="text-muted mb-0">Commission Rate</p>
                                    </div>
                                </div>

                                <!-- Commission Stats -->
                                <div class="row g-3 mb-3">
                                    <div class="col-6">
                                        <div class="p-3 border rounded-3 bg-light-subtle">
                                            <p class="fs-12 text-muted mb-0">Processed Deliveries</p>
                                            <h5 class="fw-semibold mb-0">
                                                <?php echo number_format($commission_data->processed_deliveries ?? 0); ?>
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-3 border rounded-3 bg-light-subtle">
                                            <p class="fs-12 text-muted mb-0">Avg. Commission</p>
                                            <h5 class="fw-semibold mb-0">KES
                                                <?php echo number_format($commission_data->avg_commission ?? 0, 0); ?>
                                            </h5>
                                        </div>
                                    </div>
                                </div>

                                <!-- Commission Mini Chart -->
                                <div class="mt-4">
                                    <h6 class="mb-3">Monthly Commission Trend</h6>
                                    <div id="commissionMiniChart" style="height: 120px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Farmer Payments Card -->
                    <div class="col-xl-6 col-md-12">
                        <div class="card custom-card">
                            <div class="card-header">
                                <div class="card-title">
                                    <i class="ri-money-dollar-circle-line me-2"></i>Farmer Payments
                                </div>
                            </div>
                            <div class="card-body">
                                <?php
                                        // Query to get payment data
                                        $payment_query = "SELECT 
                                                            SUM(amount) as total_payments,
                                                            COUNT(id) as payment_count,
                                                            AVG(amount) as avg_payment
                                                          FROM farmer_account_transactions
                                                          WHERE transaction_type = 'credit'
                                                          AND description LIKE '%Payment for produce sale%'";
                                        $payment_data = $app->select_one($payment_query);
                                        
                                        // Get latest payments
                                        $latest_payments_query = "SELECT 
                                            fat.amount,
                                            fat.created_at,
                                            CONCAT(u.first_name, ' ', u.last_name) as farmer_name
                                          FROM farmer_account_transactions fat
                                          JOIN farmer_accounts fa ON fat.farmer_account_id = fa.id
                                          JOIN farmers f ON fa.farmer_id = f.id
                                          JOIN users u ON f.user_id = u.id
                                          WHERE fat.transaction_type = 'credit'
                                          AND fat.description LIKE '%Payment for produce sale%'
                                          ORDER BY fat.created_at DESC
                                          LIMIT 3";
                                          $latest_payments = $app->select_all($latest_payments_query);
                                          
                                          // Get pending payments value
                                          $pending_query = "SELECT 
                                                              SUM(total_value) as pending_value,
                                                              COUNT(*) as pending_count
                                                            FROM produce_deliveries
                                                            WHERE status = 'sold'";
                                          $pending_data = $app->select_one($pending_query);
                                          ?>

                                <!-- Payment Key Metrics -->
                                <div class="d-flex align-items-center mb-4">
                                    <div class="me-4">
                                        <span class="avatar avatar-md avatar-rounded"
                                            style="background-color: #3498DB;">
                                            <i class="fa-solid fa-credit-card fs-16"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h3 class="fw-semibold mb-0">KES
                                            <?php echo number_format($payment_data->total_payments ?? 0, 0); ?></h3>
                                        <p class="text-muted mb-0">Total Payments to Farmers</p>
                                    </div>
                                </div>

                                <!-- Payment Stats -->
                                <div class="row g-3 mb-3">
                                    <div class="col-6">
                                        <div class="p-3 border rounded-3 bg-light-subtle">
                                            <p class="fs-12 text-muted mb-0">Payment Transactions</p>
                                            <h5 class="fw-semibold mb-0">
                                                <?php echo number_format($payment_data->payment_count ?? 0); ?></h5>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-3 border rounded-3 bg-light-subtle">
                                            <p class="fs-12 text-muted mb-0">Avg. Payment</p>
                                            <h5 class="fw-semibold mb-0">KES
                                                <?php echo number_format($payment_data->avg_payment ?? 0, 0); ?></h5>
                                        </div>
                                    </div>
                                </div>

                                <!-- Latest Payments -->
                                <div class="mt-4">
                                    <h6 class="mb-3">Latest Payments</h6>
                                    <?php if (count($latest_payments) > 0): ?>
                                    <?php foreach ($latest_payments as $payment): ?>
                                    <div
                                        class="d-flex align-items-center justify-content-between p-3 border rounded-3 mb-2">
                                        <div>
                                            <h6 class="mb-0"><?php echo $payment->farmer_name; ?></h6>
                                            <small
                                                class="text-muted"><?php echo date('M d, Y', strtotime($payment->created_at)); ?></small>
                                        </div>
                                        <div class="fw-semibold text-success">
                                            KES <?php echo number_format($payment->amount, 0); ?>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                    <?php else: ?>
                                    <div class="text-center py-4 text-muted">
                                        <i class="ri-information-line d-block mb-2 fs-24"></i>
                                        No payment records found
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Outstanding Balances Card -->
                    <div class="col-xl-6 col-md-12">
                        <div class="card custom-card">
                            <div class="card-header">
                                <div class="card-title">
                                    <i class="ri-timer-line me-2"></i>Outstanding Balances
                                </div>
                            </div>
                            <div class="card-body">
                                <?php
                                    // Get data about deliveries awaiting payment
                                    $outstanding_query = "SELECT 
                                        pd.id,
                                        pd.total_value,
                                        pd.delivery_date,
                                        pt.name as product_name,
                                        CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                                        DATEDIFF(CURRENT_DATE(), pd.delivery_date) as days_outstanding
                                      FROM produce_deliveries pd
                                      JOIN farm_products fp ON pd.farm_product_id = fp.id
                                      JOIN product_types pt ON fp.product_type_id = pt.id
                                      JOIN farms fm ON fp.farm_id = fm.id
                                      JOIN farmers f ON fm.farmer_id = f.id
                                      JOIN users u ON f.user_id = u.id
                                      WHERE pd.status = 'sold'
                                      ORDER BY days_outstanding DESC
                                      LIMIT 4";
                                    $outstanding_deliveries = $app->select_all($outstanding_query);
                                    
                                    // Calculate total outstanding amount and count
                                    $outstanding_total_query = "SELECT 
                                                                 SUM(total_value) as total_outstanding,
                                                                 COUNT(*) as outstanding_count,
                                                                 AVG(DATEDIFF(CURRENT_DATE(), delivery_date)) as avg_days
                                                               FROM produce_deliveries
                                                               WHERE status = 'sold'";
                                    $outstanding_totals = $app->select_one($outstanding_total_query);
                                    ?>

                                <!-- Outstanding Key Metrics -->
                                <div class="d-flex align-items-center mb-4">
                                    <div class="me-4">
                                        <span class="avatar avatar-md avatar-rounded"
                                            style="background-color: #E74C3C;">
                                            <i class="fa-solid fa-hourglass-half fs-16"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h3 class="fw-semibold mb-0">KES
                                            <?php echo number_format($outstanding_totals->total_outstanding ?? 0, 0); ?>
                                        </h3>
                                        <p class="text-muted mb-0">Total Outstanding Balance</p>
                                    </div>
                                    <div class="ms-auto text-end">
                                        <h5 class="mb-0">
                                            <?php echo number_format($outstanding_totals->outstanding_count ?? 0); ?>
                                        </h5>
                                        <p class="text-muted mb-0">Pending Payments</p>
                                    </div>
                                </div>

                                <!-- Average Days Outstanding -->
                                <div class="p-3 border rounded-3 bg-light-subtle mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="mb-0">Average Days Outstanding</p>
                                        <h5 class="mb-0 fw-semibold">
                                            <?php echo round($outstanding_totals->avg_days ?? 0); ?> days</h5>
                                    </div>
                                    <div class="progress progress-sm mt-2">
                                        <?php
                                             $avg_days = round($outstanding_totals->avg_days ?? 0);
                                             $progress_percentage = min(100, ($avg_days / 30) * 100); // 30 days as baseline
                                             $progress_color = $avg_days <= 7 ? '#2ECC71' : ($avg_days <= 15 ? '#F39C12' : '#E74C3C');
                                             ?>
                                        <div class="progress-bar" role="progressbar"
                                            style="width: <?php echo $progress_percentage; ?>%; background-color: <?php echo $progress_color; ?>;"
                                            aria-valuenow="<?php echo $progress_percentage; ?>" aria-valuemin="0"
                                            aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>

                                <!-- Outstanding Deliveries -->
                                <div class="mt-4">
                                    <h6 class="mb-3">Awaiting Payment</h6>
                                    <?php if (count($outstanding_deliveries) > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Farmer/Product</th>
                                                    <th class="text-end">Amount</th>
                                                    <th class="text-center">Age</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($outstanding_deliveries as $delivery): ?>
                                                <tr>
                                                    <td>
                                                        <h6 class="mb-0 fs-13"><?php echo $delivery->farmer_name; ?>
                                                        </h6>
                                                        <small
                                                            class="text-muted"><?php echo $delivery->product_name; ?></small>
                                                    </td>
                                                    <td class="text-end fw-semibold">
                                                        KES <?php echo number_format($delivery->total_value, 0); ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php 
                                                $days = $delivery->days_outstanding;
                                                $badge_color = $days <= 7 ? 'success' : ($days <= 15 ? 'warning' : 'danger');
                                                ?>
                                                        <span class="badge bg-<?php echo $badge_color; ?>-transparent">
                                                            <?php echo $days; ?> days
                                                        </span>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <?php if ($outstanding_totals->outstanding_count > 4): ?>
                                    <div class="text-center mt-3">
                                        <a href="pending-payments.php" class="btn btn-sm btn-outline-primary">
                                            View All <?php echo $outstanding_totals->outstanding_count; ?> Pending
                                            Payments
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                    <?php else: ?>
                                    <div class="text-center py-4 text-muted">
                                        <i class="ri-check-double-line d-block mb-2 fs-24"></i>
                                        No outstanding payments
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include "../graphs/mini-chart.php" ?>
                <!-- delivery -->
                <!-- Row 5: Delivery Analysis and Highlights -->
                <div class="row mt-4">
                    <!-- Top Performing Metrics -->
                    <div class="col-xl-6">
                        <div class="card custom-card">
                            <div class="card-header">
                                <div class="card-title">
                                    <i class="ri-award-line me-2"></i>Performance Leaders
                                </div>
                            </div>
                            <div class="card-body">
                                <?php
                // Query for top farmer
                $top_farmer_query = "SELECT 
                                       CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                                       COUNT(pd.id) as delivery_count,
                                       SUM(pd.total_value) as total_value,
                                       f.id as farmer_id
                                     FROM produce_deliveries pd
                                     JOIN farm_products fp ON pd.farm_product_id = fp.id
                                     JOIN farms fm ON fp.farm_id = fm.id
                                     JOIN farmers f ON fm.farmer_id = f.id
                                     JOIN users u ON f.user_id = u.id
                                     GROUP BY f.id, farmer_name
                                     ORDER BY total_value DESC
                                     LIMIT 1";
                $top_farmer = $app->select_one($top_farmer_query);
                
                // Query for top product
                $top_product_query = "SELECT 
                                       pt.name as product_name,
                                       COUNT(pd.id) as delivery_count,
                                       SUM(pd.quantity) as total_quantity,
                                       SUM(pd.total_value) as total_value
                                     FROM produce_deliveries pd
                                     JOIN farm_products fp ON pd.farm_product_id = fp.id
                                     JOIN product_types pt ON fp.product_type_id = pt.id
                                     GROUP BY pt.id, product_name
                                     ORDER BY total_value DESC
                                     LIMIT 1";
                $top_product = $app->select_one($top_product_query);
                
                // Query for most recent delivery
                $recent_delivery_query = "SELECT 
                                           pd.delivery_date,
                                           pd.total_value,
                                           pd.quantity,
                                           pd.quality_grade,
                                           pt.name as product_name,
                                           CONCAT(u.first_name, ' ', u.last_name) as farmer_name
                                         FROM produce_deliveries pd
                                         JOIN farm_products fp ON pd.farm_product_id = fp.id
                                         JOIN product_types pt ON fp.product_type_id = pt.id
                                         JOIN farms fm ON fp.farm_id = fm.id
                                         JOIN farmers f ON fm.farmer_id = f.id
                                         JOIN users u ON f.user_id = u.id
                                         ORDER BY pd.delivery_date DESC
                                         LIMIT 1";
                $recent_delivery = $app->select_one($recent_delivery_query);
                
                // Quality statistics
                $quality_query = "SELECT 
                                    quality_grade,
                                    COUNT(*) as count,
                                    SUM(total_value) as total_value
                                  FROM produce_deliveries
                                  WHERE quality_grade IS NOT NULL
                                  GROUP BY quality_grade
                                  ORDER BY quality_grade";
                $quality_stats = $app->select_all($quality_query);
                
                $quality_counts = [
                    'A' => 0,
                    'B' => 0,
                    'C' => 0
                ];
                
                foreach ($quality_stats as $stat) {
                    $quality_counts[$stat->quality_grade] = $stat->count;
                }
                
                $total_graded = array_sum($quality_counts);
                $best_quality = !empty($quality_counts) ? array_search(max($quality_counts), $quality_counts) : 'N/A';
                
                // Calculate percentages
                $quality_percentages = [];
                foreach ($quality_counts as $grade => $count) {
                    $quality_percentages[$grade] = ($total_graded > 0) ? ($count / $total_graded) * 100 : 0;
                }
                ?>

                                <div class="row g-3">
                                    <!-- Top Farmer Card -->
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded-3 h-100 bg-light-subtle position-relative">
                                            <div class="position-absolute top-0 end-0 mt-2 me-2">
                                                <span class="badge bg-primary rounded-pill px-2">Top Farmer</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar avatar-md avatar-rounded me-3"
                                                    style="background-color: <?php echo sprintf('#%06X', crc32($top_farmer->farmer_name ?? '') & 0xFFFFFF); ?>;">
                                                    <span
                                                        class="avatar-text text-white"><?php echo substr($top_farmer->farmer_name ?? 'N/A', 0, 1); ?></span>
                                                </div>
                                                <div>
                                                    <h5 class="mb-0 fw-semibold">
                                                        <?php echo $top_farmer->farmer_name ?? 'N/A'; ?></h5>
                                                    <p class="text-muted mb-0">Leading Producer</p>
                                                </div>
                                            </div>
                                            <div class="row g-2 text-center">
                                                <div class="col-6">
                                                    <div class="p-2 border rounded bg-white">
                                                        <h5 class="mb-0 fw-semibold">KES
                                                            <?php echo number_format($top_farmer->total_value ?? 0, 0); ?>
                                                        </h5>
                                                        <small class="text-muted">Total Value</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="p-2 border rounded bg-white">
                                                        <h5 class="mb-0 fw-semibold">
                                                            <?php echo number_format($top_farmer->delivery_count ?? 0); ?>
                                                        </h5>
                                                        <small class="text-muted">Deliveries</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Top Product Card -->
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded-3 h-100 bg-light-subtle position-relative">
                                            <div class="position-absolute top-0 end-0 mt-2 me-2">
                                                <span class="badge bg-success rounded-pill px-2">Top Product</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar avatar-md avatar-rounded me-3"
                                                    style="background-color: #6AA32D;">
                                                    <i class="fa-solid fa-seedling text-white"></i>
                                                </div>
                                                <div>
                                                    <h5 class="mb-0 fw-semibold">
                                                        <?php echo $top_product->product_name ?? 'N/A'; ?></h5>
                                                    <p class="text-muted mb-0">Most Valuable Crop</p>
                                                </div>
                                            </div>
                                            <div class="row g-2 text-center">
                                                <div class="col-6">
                                                    <div class="p-2 border rounded bg-white">
                                                        <h5 class="mb-0 fw-semibold">
                                                            <?php echo number_format($top_product->total_quantity ?? 0, 0); ?>
                                                            KG</h5>
                                                        <small class="text-muted">Total Quantity</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="p-2 border rounded bg-white">
                                                        <h5 class="mb-0 fw-semibold">KES
                                                            <?php echo number_format($top_product->total_value ?? 0, 0); ?>
                                                        </h5>
                                                        <small class="text-muted">Total Value</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Quality Distribution Card -->
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded-3 h-100 bg-light-subtle">
                                            <h6 class="mb-3">Quality Distribution</h6>

                                            <div class="mb-3">
                                                <div class="progress" style="height: 26px;">
                                                    <div class="progress-bar bg-success" role="progressbar"
                                                        style="width: <?php echo $quality_percentages['A']; ?>%"
                                                        aria-valuenow="<?php echo $quality_percentages['A']; ?>"
                                                        aria-valuemin="0" aria-valuemax="100">
                                                        Grade A
                                                    </div>
                                                    <div class="progress-bar bg-info" role="progressbar"
                                                        style="width: <?php echo $quality_percentages['B']; ?>%"
                                                        aria-valuenow="<?php echo $quality_percentages['B']; ?>"
                                                        aria-valuemin="0" aria-valuemax="100">
                                                        Grade B
                                                    </div>
                                                    <div class="progress-bar bg-warning" role="progressbar"
                                                        style="width: <?php echo $quality_percentages['C']; ?>%"
                                                        aria-valuenow="<?php echo $quality_percentages['C']; ?>"
                                                        aria-valuemin="0" aria-valuemax="100">
                                                        Grade C
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row g-2 text-center">
                                                <div class="col-4">
                                                    <div class="p-2 border rounded"
                                                        style="background-color: rgba(46, 204, 113, 0.1);">
                                                        <h6 class="mb-0 fw-semibold">
                                                            <?php echo number_format($quality_counts['A']); ?></h6>
                                                        <small class="text-success">Grade A</small>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="p-2 border rounded"
                                                        style="background-color: rgba(52, 152, 219, 0.1);">
                                                        <h6 class="mb-0 fw-semibold">
                                                            <?php echo number_format($quality_counts['B']); ?></h6>
                                                        <small class="text-info">Grade B</small>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="p-2 border rounded"
                                                        style="background-color: rgba(243, 156, 18, 0.1);">
                                                        <h6 class="mb-0 fw-semibold">
                                                            <?php echo number_format($quality_counts['C']); ?></h6>
                                                        <small class="text-warning">Grade C</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Latest Activity Card -->
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded-3 h-100 bg-light-subtle">
                                            <h6 class="mb-3">Latest Activity</h6>

                                            <?php if (isset($recent_delivery->farmer_name)): ?>
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar avatar-sm avatar-rounded me-2"
                                                    style="background-color: <?php echo sprintf('#%06X', crc32($recent_delivery->farmer_name ?? '') & 0xFFFFFF); ?>;">
                                                    <span
                                                        class="avatar-text text-white fs-12"><?php echo substr($recent_delivery->farmer_name ?? 'N/A', 0, 1); ?></span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0"><?php echo $recent_delivery->farmer_name; ?></h6>
                                                    <small class="text-muted">
                                                        Delivered
                                                        <?php echo date('M d, Y', strtotime($recent_delivery->delivery_date)); ?>
                                                    </small>
                                                </div>
                                            </div>

                                            <div class="bg-white p-3 rounded border mb-3">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">Product:</span>
                                                    <span
                                                        class="fw-medium"><?php echo $recent_delivery->product_name; ?></span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">Quantity:</span>
                                                    <span
                                                        class="fw-medium"><?php echo number_format($recent_delivery->quantity, 2); ?>
                                                        KG</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">Quality:</span>
                                                    <span
                                                        class="badge bg-<?php echo $recent_delivery->quality_grade == 'A' ? 'success' : ($recent_delivery->quality_grade == 'B' ? 'info' : 'warning'); ?>-transparent">
                                                        Grade <?php echo $recent_delivery->quality_grade; ?>
                                                    </span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-muted">Value:</span>
                                                    <span class="fw-semibold">KES
                                                        <?php echo number_format($recent_delivery->total_value, 2); ?></span>
                                                </div>
                                            </div>

                                            <div class="text-center">
                                                <a href="all-deliveries.php" class="btn btn-sm btn-primary">
                                                    <i class="ri-eye-line me-1"></i> View All Deliveries
                                                </a>
                                            </div>
                                            <?php else: ?>
                                            <div class="text-center py-4">
                                                <i class="ri-inbox-line d-block fs-24 text-muted mb-2"></i>
                                                <p class="mb-0">No recent delivery activity</p>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Metrics -->
                    <div class="col-xl-6">
                        <div class="card custom-card">
                            <div class="card-header">
                                <div class="card-title">
                                    <i class="ri-bar-chart-box-line me-2"></i>Delivery Analytics
                                </div>
                            </div>
                            <div class="card-body">
                                <?php
                // Get year-to-date metrics
                $ytd_query = "SELECT 
                                COUNT(*) as delivery_count,
                                SUM(quantity) as total_quantity,
                                SUM(total_value) as total_value,
                                AVG(total_value) as avg_value,
                                COUNT(DISTINCT farm_product_id) as product_count
                              FROM produce_deliveries
                              WHERE YEAR(delivery_date) = YEAR(CURRENT_DATE())";
                $ytd_stats = $app->select_one($ytd_query);
                
                // Get month-over-month growth
                $current_month_query = "SELECT 
                                         COUNT(*) as delivery_count,
                                         SUM(total_value) as total_value
                                       FROM produce_deliveries
                                       WHERE YEAR(delivery_date) = YEAR(CURRENT_DATE())
                                       AND MONTH(delivery_date) = MONTH(CURRENT_DATE())";
                $current_month = $app->select_one($current_month_query);
                
                $prev_month_query = "SELECT 
                                      COUNT(*) as delivery_count,
                                      SUM(total_value) as total_value
                                    FROM produce_deliveries
                                    WHERE YEAR(delivery_date) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
                                    AND MONTH(delivery_date) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))";
                $prev_month = $app->select_one($prev_month_query);
                
                // Calculate growth rates
                $delivery_growth = 0;
                $value_growth = 0;
                
                if ($prev_month->delivery_count > 0) {
                    $delivery_growth = (($current_month->delivery_count - $prev_month->delivery_count) / $prev_month->delivery_count) * 100;
                }
                
                if ($prev_month->total_value > 0) {
                    $value_growth = (($current_month->total_value - $prev_month->total_value) / $prev_month->total_value) * 100;
                }
                
                // Get status distribution data for doughnut chart
                $status_query = "SELECT 
                                  status,
                                  COUNT(*) as count
                                FROM produce_deliveries
                                GROUP BY status";
                $status_data = $app->select_all($status_query);
                
                $status_counts = [
                    'pending' => 0,
                    'rejected' => 0,
                    'verified' => 0,
                    'sold' => 0,
                    'paid' => 0
                ];
                
                foreach ($status_data as $status) {
                    $status_counts[$status->status] = $status->count;
                }
                ?>

                                <!-- Year-to-date summary -->
                                <div class="row g-3 mb-4">
                                    <div class="col-lg-6">
                                        <div class="p-3 border rounded-3 position-relative"
                                            style="background: linear-gradient(135deg, #6AA32D 0%, #4A7B1E 100%);">
                                            <h6 class="mb-4 text-white">Year-to-Date Summary</h6>
                                            <div class="d-flex flex-column">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="text-white opacity-75">Total Deliveries</span>
                                                    <h5 class="mb-0 text-white">
                                                        <?php echo number_format($ytd_stats->delivery_count ?? 0); ?>
                                                    </h5>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="text-white opacity-75">Total Quantity</span>
                                                    <h5 class="mb-0 text-white">
                                                        <?php echo number_format($ytd_stats->total_quantity ?? 0, 0); ?>
                                                        KG</h5>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="text-white opacity-75">Total Value</span>
                                                    <h5 class="mb-0 text-white">KES
                                                        <?php echo number_format($ytd_stats->total_value ?? 0, 0); ?>
                                                    </h5>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-white opacity-75">Unique Products</span>
                                                    <h5 class="mb-0 text-white">
                                                        <?php echo number_format($ytd_stats->product_count ?? 0); ?>
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="p-3 border rounded-3">
                                            <h6 class="mb-3">Month-over-Month Growth</h6>

                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span class="text-muted">Delivery Volume</span>
                                                    <span
                                                        class="fw-medium <?php echo $delivery_growth >= 0 ? 'text-success' : 'text-danger'; ?>">
                                                        <?php echo $delivery_growth >= 0 ? '+' : ''; ?><?php echo number_format($delivery_growth, 1); ?>%
                                                    </span>
                                                </div>
                                                <div class="progress progress-sm">
                                                    <div class="progress-bar <?php echo $delivery_growth >= 0 ? 'bg-success' : 'bg-danger'; ?>"
                                                        role="progressbar"
                                                        style="width: <?php echo min(100, abs($delivery_growth * 2)); ?>%"
                                                        aria-valuenow="<?php echo abs($delivery_growth); ?>"
                                                        aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span class="text-muted">Delivery Value</span>
                                                    <span
                                                        class="fw-medium <?php echo $value_growth >= 0 ? 'text-success' : 'text-danger'; ?>">
                                                        <?php echo $value_growth >= 0 ? '+' : ''; ?><?php echo number_format($value_growth, 1); ?>%
                                                    </span>
                                                </div>
                                                <div class="progress progress-sm">
                                                    <div class="progress-bar <?php echo $value_growth >= 0 ? 'bg-success' : 'bg-danger'; ?>"
                                                        role="progressbar"
                                                        style="width: <?php echo min(100, abs($value_growth * 2)); ?>%"
                                                        aria-valuenow="<?php echo abs($value_growth); ?>"
                                                        aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="px-3 py-2 bg-light-subtle rounded mt-4">
                                                <div class="d-flex justify-content-between">
                                                    <div class="text-center">
                                                        <h6 class="mb-0 fw-semibold">
                                                            <?php echo number_format($current_month->delivery_count ?? 0); ?>
                                                        </h6>
                                                        <small class="text-muted">Current</small>
                                                    </div>
                                                    <div class="text-center">
                                                        <h6 class="mb-0">
                                                            <?php echo number_format($prev_month->delivery_count ?? 0); ?>
                                                        </h6>
                                                        <small class="text-muted">Previous</small>
                                                    </div>
                                                    <div class="text-center">
                                                        <h6 class="mb-0 fw-semibold">KES
                                                            <?php echo number_format($current_month->total_value ?? 0, 0); ?>
                                                        </h6>
                                                        <small class="text-muted">Current</small>
                                                    </div>
                                                    <div class="text-center">
                                                        <h6 class="mb-0">KES
                                                            <?php echo number_format($prev_month->total_value ?? 0, 0); ?>
                                                        </h6>
                                                        <small class="text-muted">Previous</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Distribution Chart -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="border rounded-3 p-3">
                                            <h6 class="mb-3">Status Distribution</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div id="statusDonutChart" style="height: 200px;"></div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="d-flex flex-column justify-content-center h-100">
                                                        <?php
                                        $status_names = [
                                            'pending' => 'Pending',
                                            'verified' => 'Verified',
                                            'rejected' => 'Rejected',
                                            'sold' => 'Sold',
                                            'paid' => 'Paid'
                                        ];
                                        
                                        $status_colors = [
                                            'pending' => '#FFC107',
                                            'verified' => '#3498DB',
                                            'rejected' => '#E74C3C',
                                            'sold' => '#2ECC71',
                                            'paid' => '#9B59B6'
                                        ];
                                        
                                        $total_deliveries = array_sum($status_counts);
                                        
                                        foreach ($status_counts as $status => $count):
                                            $percentage = ($total_deliveries > 0) ? ($count / $total_deliveries) * 100 : 0;
                                        ?>
                                                        <div class="d-flex align-items-center mb-2">
                                                            <div class="me-2"
                                                                style="width: 12px; height: 12px; background-color: <?php echo $status_colors[$status]; ?>; border-radius: 50%;">
                                                            </div>
                                                            <div
                                                                class="flex-grow-1 d-flex justify-content-between align-items-center">
                                                                <span><?php echo $status_names[$status]; ?></span>
                                                                <div>
                                                                    <span
                                                                        class="badge bg-light text-dark me-1"><?php echo number_format($count); ?></span>
                                                                    <span
                                                                        class="text-muted">(<?php echo round($percentage, 1); ?>%)</span>
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
                </div>

                <!-- Status Distribution Chart Script -->
                <?php include "../graphs/status-chart.php" ?>




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
    $(document).ready(() => {
        displayRecentTransactions();
        displayTransactionSources()
    });

    function displayRecentTransactions() {
        let displayRecentTransactions = "true";
        $.ajax({
            url: "http://localhost/dfcs/ajax/sacco-controller/display-recent-transactions.php",
            type: 'POST',
            data: {
                displayRecentTransactions: displayRecentTransactions,
            },
            success: function(data, status) {
                $('#recentTransactionsSection').html(data);
            },
        });
    }

    function displayTransactionSources() {
        let displayTransactionSources = "true";
        $.ajax({
            url: "http://localhost/dfcs/ajax/sacco-controller/display-transaction-sources.php",
            type: 'POST',
            data: {
                displayTransactionSources: displayTransactionSources,
            },
            success: function(data, status) {
                $('#transactionSourcesSection').html(data);
            },
        });
    }
    </script>


</body>



</html>