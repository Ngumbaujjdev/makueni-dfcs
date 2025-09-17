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
                        <?php 
                        $app = new App;
                        
                        // Get farmer details including their registration number
                        $query = "SELECT u.*, f.registration_number, f.category_id, fc.name as category_name 
                                  FROM users u 
                                  LEFT JOIN farmers f ON u.id = f.user_id 
                                  LEFT JOIN farmer_categories fc ON f.category_id = fc.id 
                                  WHERE u.id = " . $_SESSION['user_id'];
                        
                        $farmer = $app->select_one($query);
                        ?>

                        <p class="fw-semibold fs-18 mb-0">
                            Welcome <?php echo $farmer->first_name ?> <?php echo $farmer->last_name ?>
                            <span class="badge bg-success ms-2"><?php echo $farmer->registration_number ?></span>
                        </p>

                        <span class="fs-semibold text-muted pt-5">
                            Farmer Dashboard
                            <?php if($farmer->category_name): ?>
                            - <?php echo $farmer->category_name ?> Farmer
                            <?php endif; ?>
                        </span>
                    </div>
                </div>

                <!-- End::page-header -->
            </div>
            <!-- row to display my farms details -->
            <!-- row 1 -->
            <?php
              $userId = $_SESSION['user_id']; 
    $farmerQuery = "SELECT id FROM farmers WHERE user_id = :user_id";
      $farmerParams = [':user_id' => $userId];
      $farmerResult = $app->selectOne($farmerQuery, $farmerParams);
      $farmerId = $farmerResult->id;
             $app = new App;
            $query = "SELECT
            f.*,
            GROUP_CONCAT(DISTINCT ft.name) as fruits,
            COUNT(DISTINCT ffm.fruit_type_id) as fruit_count,
            ct.name as cultivation_type,
            hm.name as harvesting_method,
            hf.name as harvest_frequency,
            SUM(fp.estimated_production) as total_production,
            ft2.name as farm_type
            FROM farms f
            LEFT JOIN farm_fruit_mapping ffm ON f.id = ffm.farm_id
            LEFT JOIN fruit_types ft ON ffm.fruit_type_id = ft.id
            LEFT JOIN cultivation_types ct ON ffm.cultivation_type_id = ct.id
            LEFT JOIN harvesting_methods hm ON ffm.harvesting_method_id = hm.id
            LEFT JOIN harvest_frequencies hf ON ffm.harvest_frequency_id = hf.id
            LEFT JOIN farm_types ft2 ON f.farm_type_id = ft2.id
            LEFT JOIN farm_products fp ON f.id = fp.farm_id
            WHERE f.id = :farm_id AND f.farmer_id = :farmer_id
            GROUP BY f.id";

            $params = [
            ':farm_id' => $_GET['id'],
            ':farmer_id' => $farmerId
            ];

            $farm = $app->selectOne($query, $params);
             ?>

            <!-- Row 1: Quick Overview Cards -->
            <div class="row mb-4">
                <div class="col-sm-12 col-md-6 col-xl-3">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="avatar avatar-md bg-primary">
                                        <i class="ri-home-4-line fs-16"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted">Farm Name</p>
                                    <h5 class="fw-semibold mb-0"><?php echo htmlspecialchars($farm->name) ?></h5>
                                    <div class="text-muted small">Since
                                        <?php echo date('M Y', strtotime($farm->created_at)) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-md-6 col-xl-3">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="avatar avatar-md" style="background:#6AA32D;">
                                        <i class="ri-ruler-line fs-16"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted">Total Size</p>
                                    <h5 class="fw-semibold mb-0"><?php echo number_format($farm->size, 2) ?> acres</h5>
                                    <div class="text-muted small"><?php echo $farm->farm_type ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-md-6 col-xl-3">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="avatar avatar-md bg-warning">
                                        <i class="ri-plant-line fs-16"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted">Fruits Grown</p>
                                    <h5 class="fw-semibold mb-0"><?php echo $farm->fruit_count ?> Types</h5>
                                    <div class="text-muted small text-truncate" style="max-width: 150px;">
                                        <?php echo $farm->fruits ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-md-6 col-xl-3">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="avatar avatar-md bg-success">
                                        <i class="ri-line-chart-line fs-16"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted">Expected Production</p>
                                    <h5 class="fw-semibold mb-0"><?php echo number_format($farm->total_production) ?>
                                        KGs</h5>
                                    <div class="text-muted small">Total Estimated</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 2: Farm Specifics -->
            <div class="row mb-4">
                <!-- Left Column -->
                <div class="col-md-6">
                    <div class="card custom-card">
                        <div class="card-header border-bottom">
                            <div class="card-title">
                                <i class="ri-settings-2-line me-2" style="color:#6AA32D"></i>Farming Methods
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="d-flex align-items-center p-3 rounded-3"
                                        style="background-color: rgba(106, 163, 45, 0.1);">
                                        <div class="me-3">
                                            <span class="avatar avatar-md" style="background-color: #6AA32D">
                                                <i class="ri-plant-line fs-16"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <label class="form-label text-muted mb-0">Cultivation Method</label>
                                            <div class="fw-semibold fs-15">
                                                <?php echo htmlspecialchars($farm->cultivation_type ?? 'Not Set') ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex align-items-center p-3 rounded-3"
                                        style="background-color: rgba(106, 163, 45, 0.1);">
                                        <div class="me-3">
                                            <span class="avatar avatar-md" style="background-color: #6AA32D">
                                                <i class="ri-scissors-cut-line fs-16"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <label class="form-label text-muted mb-0">Harvesting Method</label>
                                            <div class="fw-semibold fs-15">
                                                <?php echo htmlspecialchars($farm->harvesting_method ?? 'Not Set') ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex align-items-center p-3 rounded-3"
                                        style="background-color: rgba(106, 163, 45, 0.1);">
                                        <div class="me-3">
                                            <span class="avatar avatar-md" style="background-color: #6AA32D">
                                                <i class="ri-calendar-line fs-16"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <label class="form-label text-muted mb-0">Harvest Frequency</label>
                                            <div class="fw-semibold fs-15">
                                                <?php echo htmlspecialchars($farm->harvest_frequency ?? 'Not Set') ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-6">
                    <div class="card custom-card">
                        <div class="card-header border-bottom">
                            <div class="card-title">
                                <i class="ri-map-pin-line me-2" style="color:#6AA32D"></i>Location Details
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="d-flex align-items-center p-3 rounded-3"
                                        style="background-color: rgba(106, 163, 45, 0.1);">
                                        <div class="me-3">
                                            <span class="avatar avatar-md" style="background-color: #6AA32D">
                                                <i class="ri-map-2-line fs-16"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <label class="form-label text-muted mb-0">Location</label>
                                            <div class="fw-semibold fs-15">
                                                <?php echo htmlspecialchars($farm->location ?? 'Not Set') ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex align-items-center p-3 rounded-3"
                                        style="background-color: rgba(106, 163, 45, 0.1);">
                                        <div class="me-3">
                                            <span class="avatar avatar-md" style="background-color: #6AA32D">
                                                <i class="ri-building-line fs-16"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <label class="form-label text-muted mb-0">Farm Type</label>
                                            <div class="fw-semibold fs-15">
                                                <?php echo htmlspecialchars($farm->farm_type ?? 'Not Set') ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex align-items-center p-3 rounded-3"
                                        style="background-color: rgba(106, 163, 45, 0.1);">
                                        <div class="me-3">
                                            <span class="avatar avatar-md" style="background-color: #6AA32D">
                                                <i class="ri-time-line fs-16"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <label class="form-label text-muted mb-0">Last Updated</label>
                                            <div class="fw-semibold fs-15">
                                                <?php echo date('M d, Y', strtotime($farm->updated_at)) ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- row 3 -->
            <?php 
             // For fruit details (Row 3)
            $id = $_GET['id'];
                 $fruitQuery = "SELECT 
                     ffm.*,
                     ft.name as fruit_name,
                     fp.estimated_production,
                     fp.created_at as planting_date,
                     DATE_ADD(fp.created_at, INTERVAL 1 YEAR) as expected_harvest_date
                 FROM farm_fruit_mapping ffm
                 JOIN fruit_types ft ON ffm.fruit_type_id = ft.id
                 LEFT JOIN farm_products fp ON ffm.farm_id = fp.farm_id AND ffm.fruit_type_id = fp.product_type_id
                 WHERE ffm.farm_id = '{$id}'";
                 
                 $fruits = $app->select_all($fruitQuery);
                 
                 // For production tracking (Row 4)
              $productionQuery = "SELECT 
                        pd.*,
                        pt.name as product_name
                    FROM produce_deliveries pd
                    JOIN farm_products fp ON pd.farm_product_id = fp.id
                    JOIN product_types pt ON fp.product_type_id = pt.id
                    WHERE fp.farm_id = '{$id}'
                    ORDER BY pd.delivery_date DESC";
                 $productions = $app->select_all($productionQuery);
             ?>
            <!-- Row 3: Fruit Details -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card custom-card">
                        <div class="card-header">
                            <div class="card-title">Fruit Details & Planning</div>
                        </div>
                        <div class="card-body">
                            <?php if ($fruits && count($fruits) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Fruit Type</th>
                                            <th>Acreage Allocated</th>
                                            <th>Expected Production</th>
                                            <th>Planting Date</th>
                                            <th>Expected Harvest</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($fruits as $fruit): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($fruit->fruit_name ?? 'N/A') ?></td>
                                            <td><?php echo number_format($fruit->acreage ?? 0, 2) ?> acres</td>
                                            <td><?php echo $fruit->estimated_production ? number_format($fruit->estimated_production) . ' KGs' : 'Not set' ?>
                                            </td>
                                            <td><?php echo $fruit->planting_date ? date('M d, Y', strtotime($fruit->planting_date)) : 'Not set' ?>
                                            </td>
                                            <td><?php echo $fruit->expected_harvest_date ? date('M d, Y', strtotime($fruit->expected_harvest_date)) : 'Not set' ?>
                                            </td>
                                            <td><span class="badge bg-success">Active</span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-4">
                                <img src="http://localhost/dfcs/farmers/assets/images/no-data.png" alt="No Data"
                                    class="mb-3" style="width: 150px;">
                                <h6 class="fw-semibold">No Fruit Details Available</h6>
                                <p class="text-muted">No fruits have been added to this farm yet.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 4: Production Tracking -->
            <div class="row">
                <!-- Production History -->
                <!-- Production History -->
                <div class="col-md-8 mb-4">
                    <div class="card custom-card">
                        <div class="card-header">
                            <div class="card-title">Production History</div>
                        </div>
                        <div class="card-body">
                            <?php if ($productions && count($productions) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Quality Grade</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($productions as $prod): ?>
                                        <tr>
                                            <td><?php echo $prod->delivery_date ? date('M d, Y', strtotime($prod->delivery_date)) : 'N/A' ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($prod->product_name ?? 'N/A') ?></td>
                                            <td><?php echo $prod->quantity ? number_format($prod->quantity) . ' KGs' : 'N/A' ?>
                                            </td>
                                            <td>
                                                <?php if ($prod->quality_grade): ?>
                                                <span
                                                    class="badge bg-<?php echo $prod->quality_grade === 'A' ? 'success' : ($prod->quality_grade === 'B' ? 'warning' : 'danger') ?>">
                                                    Grade <?php echo $prod->quality_grade ?>
                                                </span>
                                                <?php else: ?>
                                                <span class="badge bg-secondary">Not Graded</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($prod->status): ?>
                                                <span
                                                    class="badge bg-<?php echo $prod->status === 'accepted' ? 'success' : ($prod->status === 'pending' ? 'warning' : 'danger') ?>">
                                                    <?php echo ucfirst($prod->status) ?>
                                                </span>
                                                <?php else: ?>
                                                <span class="badge bg-secondary">No Status</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-4">
                                <img src="http://localhost/dfcs/farmers/assets/images/no-data.png" alt="No Data"
                                    class="mb-3" style="width: 150px;">
                                <h6 class="fw-semibold">No Production History</h6>
                                <p class="text-muted">No production records have been added yet.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Production Stats -->
                <div class="col-md-4 mb-4">
                    <div class="card custom-card">
                        <div class="card-header">
                            <div class="card-title">Quality Distribution</div>
                        </div>
                        <div class="card-body">
                            <?php if ($productions && count($productions) > 0): ?>
                            <?php
                                $gradeA = $gradeB = $gradeC = 0;
                                foreach ($productions as $prod) {
                                    if ($prod->quality_grade === 'A') $gradeA++;
                                    elseif ($prod->quality_grade === 'B') $gradeB++;
                                    elseif ($prod->quality_grade === 'C') $gradeC++;
                                }
                                $total = $gradeA + $gradeB + $gradeC;
                                ?>
                            <div class="d-flex flex-column gap-3">
                                <div>
                                    <label class="form-label mb-1">Grade A</label>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-success"
                                            style="width: <?php echo $total ? ($gradeA/$total*100) : 0 ?>%">
                                            <?php echo $total ? round($gradeA/$total*100) : 0 ?>%
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label mb-1">Grade B</label>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-warning"
                                            style="width: <?php echo $total ? ($gradeB/$total*100) : 0 ?>%">
                                            <?php echo $total ? round($gradeB/$total*100) : 0 ?>%
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label mb-1">Grade C</label>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-danger"
                                            style="width: <?php echo $total ? ($gradeC/$total*100) : 0 ?>%">
                                            <?php echo $total ? round($gradeC/$total*100) : 0 ?>%
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-4">
                                <img src="http://localhost/dfcs/farmers/assets/images/no-data.png" alt="No Data"
                                    class="mb-3" style="width: 150px;">
                                <h6 class="fw-semibold">No Quality Data</h6>
                                <p class="text-muted">Quality distribution will be shown once production records are
                                    available.</p>
                            </div>
                            <?php endif; ?>
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