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
                        <span class="fs-semibold text-muted pt-5">Agrovet Management Dashboard</span>
                    </div>
                </div>

                <!-- End::page-header -->

                <div class="row mt-2">
                    <!-- Total Sold Produce -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden" style="background:#6AA32D!important;">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded"
                                            style="background-color: white!important;">
                                            <i style="color:#6AA32D" class="fa-solid fa-box-open fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-white mb-0">Sold Produce</p>
                                                <?php
                            $query = "SELECT COUNT(*) as count FROM produce_deliveries 
                                     WHERE status = 'sold' AND is_sold = 1";
                            $result = $app->select_one($query);
                            $sold_count = ($result) ? $result->count : 0;
                            ?>
                                                <h4 style="color:wheat;" class="fw-semibold mt-1">
                                                    <?php echo $sold_count ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Sales Value -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-money-bill-trend-up fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Total Sales Value</p>
                                                <?php
                            $query = "SELECT COALESCE(SUM(total_value), 0) as total 
                                     FROM produce_deliveries 
                                     WHERE status = 'sold' AND is_sold = 1";
                            $result = $app->select_one($query);
                            $total_sales = ($result) ? number_format($result->total, 2) : 0;
                            ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    KES <?php echo $total_sales ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SACCO Commission -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-percent fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">SACCO Commission</p>
                                                <?php
                            $query = "SELECT COALESCE(SUM(total_value * 0.10), 0) as commission 
                                     FROM produce_deliveries 
                                     WHERE status = 'sold' AND is_sold = 1";
                            $result = $app->select_one($query);
                            $commission = ($result) ? number_format($result->commission, 2) : 0;
                            ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    KES <?php echo $commission ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Weight Sold -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-scale-balanced fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Total Weight Sold</p>
                                                <?php
                            $query = "SELECT COALESCE(SUM(quantity), 0) as total_weight 
                                     FROM produce_deliveries 
                                     WHERE status = 'sold' AND is_sold = 1";
                            $result = $app->select_one($query);
                            $total_weight = ($result) ? number_format($result->total_weight, 2) : 0;
                            ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php echo $total_weight ?> KGs
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Farmers with Sold Produce -->
                    <div class="col-xxl-3 col-lg-3 col-md-6 mt-2">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-users-gear fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Farmers with Sales</p>
                                                <?php
                            $query = "SELECT COUNT(DISTINCT f.farmer_id) as count 
                                     FROM produce_deliveries pd
                                     JOIN farm_products fp ON pd.farm_product_id = fp.id
                                     JOIN farms f ON fp.farm_id = f.id
                                     WHERE pd.status = 'sold' AND pd.is_sold = 1";
                            $result = $app->select_one($query);
                            $farmers_with_sales = ($result) ? $result->count : 0;
                            ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php echo $farmers_with_sales ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Average Sale Value -->
                    <div class="col-xxl-3 col-lg-3 col-md-6 mt-2">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-chart-line fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Average Sale Value</p>
                                                <?php
                            $query = "SELECT COALESCE(AVG(total_value), 0) as avg_sale 
                                     FROM produce_deliveries 
                                     WHERE status = 'sold' AND is_sold = 1";
                            $result = $app->select_one($query);
                            $avg_sale = ($result) ? number_format($result->avg_sale, 2) : 0;
                            ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    KES <?php echo $avg_sale ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                // Get product-wise sales distribution
                $query = "SELECT 
                            pt.name as product_name,
                            COUNT(*) as sale_count,
                            SUM(pd.quantity) as total_quantity,
                            SUM(pd.total_value) as total_sales,
                            AVG(pd.unit_price) as avg_price,
                            MIN(pd.unit_price) as min_price,
                            MAX(pd.unit_price) as max_price
                         FROM produce_deliveries pd
                         JOIN farm_products fp ON pd.farm_product_id = fp.id
                         JOIN product_types pt ON fp.product_type_id = pt.id
                         WHERE pd.status = 'sold' AND pd.is_sold = 1
                         GROUP BY pt.name
                         ORDER BY total_sales DESC";
                
                $products = $app->select_all($query);

                // Calculate totals for percentages
                $totalSales = 0;
                $totalQuantity = 0;
                foreach ($products as $product) {
                    $totalSales += $product->total_sales;
                    $totalQuantity += $product->total_quantity;
                }
                ?>
            <!-- sales distribution -->
            <!-- Sales Distribution Analysis -->
            <!-- Sales Distribution Analysis -->
            <div class="row mt-4">
                <div class="col-xl-12">
                    <div class="card custom-card shadow-sm border-0">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="avatar avatar-lg bg-success-subtle rounded-circle">
                                        <i class="fa-solid fa-chart-pie fs-4 text-success"></i>
                                    </span>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0">Sales Distribution Analysis</h5>
                                    <small class="text-muted">Overview of product sales performance</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-success-subtle text-success rounded-pill px-3">
                                    <i class="fa-regular fa-clock me-1"></i>
                                    Last Updated: <?php echo date('M d, Y, h:i A'); ?>
                                </span>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <?php
                // Get product-wise sales distribution
                $query = "SELECT 
                            pt.name as product_name,
                            COUNT(*) as sale_count,
                            SUM(pd.quantity) as total_quantity,
                            SUM(pd.total_value) as total_sales,
                            AVG(pd.unit_price) as avg_price,
                            MIN(pd.unit_price) as min_price,
                            MAX(pd.unit_price) as max_price
                         FROM produce_deliveries pd
                         JOIN farm_products fp ON pd.farm_product_id = fp.id
                         JOIN product_types pt ON fp.product_type_id = pt.id
                         WHERE pd.status = 'sold' AND pd.is_sold = 1
                         GROUP BY pt.name
                         ORDER BY total_sales DESC";
                
                $products = $app->select_all($query);

                // Calculate totals for percentages
                $totalSales = 0;
                $totalQuantity = 0;
                foreach ($products as $product) {
                    $totalSales += $product->total_sales;
                    $totalQuantity += $product->total_quantity;
                }
                ?>

                            <div class="row g-4">
                                <!-- Top performing products table -->
                                <div class="col-xl-8">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="border-0 rounded-start">
                                                        <div class="d-flex align-items-center">
                                                            <span
                                                                class="avatar avatar-sm bg-success-subtle rounded-circle me-2">
                                                                <i class="fa-solid fa-box text-success"></i>
                                                            </span>
                                                            Product
                                                        </div>
                                                    </th>
                                                    <th class="border-0 text-center">
                                                        <div class="d-flex align-items-center justify-content-center">
                                                            <span
                                                                class="avatar avatar-sm bg-primary-subtle rounded-circle me-2">
                                                                <i class="fa-solid fa-hashtag text-primary"></i>
                                                            </span>
                                                            Sales Count
                                                        </div>
                                                    </th>
                                                    <th class="border-0 text-center">
                                                        <div class="d-flex align-items-center justify-content-center">
                                                            <span
                                                                class="avatar avatar-sm bg-info-subtle rounded-circle me-2">
                                                                <i class="fa-solid fa-weight-scale text-info"></i>
                                                            </span>
                                                            Quantity (KGs)
                                                        </div>
                                                    </th>
                                                    <th class="border-0 text-center">
                                                        <div class="d-flex align-items-center justify-content-center">
                                                            <span
                                                                class="avatar avatar-sm bg-warning-subtle rounded-circle me-2">
                                                                <i class="fa-solid fa-money-bill-wave text-warning"></i>
                                                            </span>
                                                            Total Sales
                                                        </div>
                                                    </th>
                                                    <th class="border-0 text-center rounded-end">Distribution</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($products as $product): 
                                        $salesPercentage = ($product->total_sales / $totalSales) * 100;
                                    ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div
                                                                class="avatar avatar-sm bg-success-subtle rounded-circle me-2">
                                                                <i class="fa-solid fa-leaf text-success"></i>
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-0">
                                                                    <?php echo htmlspecialchars($product->product_name); ?>
                                                                </h6>
                                                                <small class="text-muted">Unit Price: KES
                                                                    <?php echo number_format($product->avg_price, 2); ?></small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-primary-subtle text-primary rounded-pill">
                                                            <?php echo number_format($product->sale_count); ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-info-subtle text-info rounded-pill">
                                                            <?php echo number_format($product->total_quantity, 2); ?>
                                                            KGs
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <h6 class="mb-0 text-success">KES
                                                            <?php echo number_format($product->total_sales, 2); ?></h6>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="flex-grow-1">
                                                                <div class="progress" style="height: 8px;">
                                                                    <div class="progress-bar bg-success"
                                                                        role="progressbar"
                                                                        style="width: <?php echo $salesPercentage; ?>%"
                                                                        aria-valuenow="<?php echo $salesPercentage; ?>"
                                                                        aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                            </div>
                                                            <span class="badge bg-success-subtle text-success">
                                                                <?php echo number_format($salesPercentage, 1); ?>%
                                                            </span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Price Analysis Cards -->
                                <div class="col-xl-4">
                                    <!-- Sales Summary Card -->
                                    <div class="card border-0 shadow-sm bg-success-subtle mb-4">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <span class="avatar avatar-md bg-white rounded-circle me-3">
                                                    <i class="fa-solid fa-chart-line text-success fs-4"></i>
                                                </span>
                                                <h6 class="card-title mb-0 text-success">Sales Overview</h6>
                                            </div>

                                            <div class="row g-3">
                                                <div class="col-6">
                                                    <div class="p-3 bg-white rounded-3">
                                                        <small class="text-muted d-block">Total Sales</small>
                                                        <h5 class="mb-0">KES
                                                            <?php echo number_format($totalSales, 0); ?></h5>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="p-3 bg-white rounded-3">
                                                        <small class="text-muted d-block">Total Quantity</small>
                                                        <h5 class="mb-0"><?php echo number_format($totalQuantity, 0); ?>
                                                            KGs</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Price Analysis -->
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-4">
                                                <span class="avatar avatar-md bg-success-subtle rounded-circle me-3">
                                                    <i class="fa-solid fa-tags text-success fs-4"></i>
                                                </span>
                                                <h6 class="card-title mb-0">Price Analysis</h6>
                                            </div>

                                            <?php foreach ($products as $product): ?>
                                            <div class="mb-4">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <div>
                                                        <h6 class="mb-0">
                                                            <?php echo htmlspecialchars($product->product_name); ?></h6>
                                                        <small class="text-muted">Average Price/KG</small>
                                                    </div>
                                                    <h5 class="mb-0 text-success">KES
                                                        <?php echo number_format($product->avg_price, 2); ?></h5>
                                                </div>
                                                <div class="progress mb-2" style="height: 6px;">
                                                    <div class="progress-bar bg-success"
                                                        style="width: <?php echo ($product->avg_price / max(array_column((array)$products, 'avg_price'))) * 100; ?>%"
                                                        role="progressbar"></div>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span class="badge bg-light text-dark">
                                                        <i class="fa-solid fa-arrow-down text-danger me-1"></i>
                                                        Low: KES <?php echo number_format($product->min_price, 2); ?>
                                                    </span>
                                                    <span class="badge bg-light text-dark">
                                                        <i class="fa-solid fa-arrow-up text-success me-1"></i>
                                                        High: KES <?php echo number_format($product->max_price, 2); ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-light py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <i class="fa-solid fa-circle-info text-success me-2"></i>
                                    <span class="text-muted">Data updated in real-time based on sales
                                        transactions</span>
                                </div>
                                <button class="btn btn-sm btn-success">
                                    <i class="fa-solid fa-download me-1"></i>
                                    Export Report
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- row to display deliveries -->
            <!-- Sold Produce Section -->
            <div class="row">
                <div class="col-xl-12">
                    <div id="soldProduceSection"></div>
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
    $(document).ready(() => {
        displaySoldProduce();
    });

    // Function to display sold produce
    function displaySoldProduce() {
        $.ajax({
            url: "http://localhost/dfcs/ajax/produce-controller/display-sold-produce.php",
            type: 'POST',
            data: {
                displaySoldProduce: "true",
            },
            success: function(data, status) {
                $('#soldProduceSection').html(data);
            },
            error: function(xhr, status, error) {
                console.error("Error loading sold produce:", error);
                $('#soldProduceSection').html(
                    '<div class="alert alert-danger">Error loading sold produce data.</div>');
            }
        });
    }
    </script>

</body>



</html>