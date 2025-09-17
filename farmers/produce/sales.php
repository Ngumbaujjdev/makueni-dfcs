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
                <!-- Page Header -->
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
                <!-- End Page Header -->
                <div class="row">
                    <div class="col-xl-12">
                        <?php
                        $app = new App;
                        $userId = $_SESSION['user_id'];

                        // Get the farmer's ID from the user ID
                        $farmerQuery = "SELECT id FROM farmers WHERE user_id = $userId";
                        $farmerResult = $app->select_one($farmerQuery);

                        if ($farmerResult) {
                        $farmerId = $farmerResult->id;

                        // Get only SOLD produce deliveries for this farmer
                        $query = "SELECT
                        pd.id,
                        pd.quantity,
                        pd.unit_price,
                        pd.total_value,
                        pd.quality_grade,
                        pd.delivery_date,
                        pd.status,
                        pd.notes,
                        pt.name as product_name,
                        f.name as farm_name
                        FROM produce_deliveries pd
                        JOIN farm_products fp ON pd.farm_product_id = fp.id
                        JOIN product_types pt ON fp.product_type_id = pt.id
                        JOIN farms f ON fp.farm_id = f.id
                        WHERE f.farmer_id = $farmerId
                        AND pd.status = 'sold'
                        ORDER BY pd.delivery_date DESC";

                        $deliveries = $app->select_all($query);

                        // Get summary statistics for sold produce only
                        $summaryQuery = "SELECT
                        COUNT(*) as total_sold,
                        SUM(quantity) as total_quantity,
                        SUM(total_value) as total_sales_value,
                        AVG(total_value/quantity) as avg_price_per_kg
                        FROM produce_deliveries pd
                        JOIN farm_products fp ON pd.farm_product_id = fp.id
                        JOIN farms f ON fp.farm_id = f.id
                        WHERE f.farmer_id = $farmerId
                        AND pd.status = 'sold'";

                        $summary = $app->select_one($summaryQuery);

                        // Get product breakdown for sold products
                        $productQuery = "SELECT
                        pt.name as product_name,
                        COUNT(*) as delivery_count,
                        SUM(pd.quantity) as total_quantity,
                        SUM(pd.total_value) as sold_value,
                        AVG(pd.total_value/pd.quantity) as avg_price
                        FROM produce_deliveries pd
                        JOIN farm_products fp ON pd.farm_product_id = fp.id
                        JOIN product_types pt ON fp.product_type_id = pt.id
                        JOIN farms f ON fp.farm_id = f.id
                        WHERE f.farmer_id = $farmerId
                        AND pd.status = 'sold'
                        GROUP BY pt.name
                        ORDER BY sold_value DESC";

                        $products = $app->select_all($productQuery);

                        // Get buyer statistics
                        $buyerQuery = "SELECT
                        SUBSTRING_INDEX(SUBSTRING_INDEX(pd.notes, 'Sold to:', -1), '.', 1) as buyer,
                        COUNT(*) as purchase_count,
                        SUM(pd.quantity) as total_quantity,
                        SUM(pd.total_value) as total_value
                        FROM produce_deliveries pd
                        JOIN farm_products fp ON pd.farm_product_id = fp.id
                        JOIN farms f ON fp.farm_id = f.id
                        WHERE f.farmer_id = $farmerId
                        AND pd.status = 'sold'
                        AND pd.notes LIKE '%Sold to:%'
                        GROUP BY buyer
                        ORDER BY total_value DESC";

                        $buyers = $app->select_all($buyerQuery);
                        } else {
                        $deliveries = [];
                        $summary = null;
                        $products = [];
                        $buyers = [];
                        }
                        ?>

                        <!-- Sales Summary Cards -->
                        <div class="row mt-2">
                            <!-- Total Sales -->
                            <div class="col-xxl-6 col-lg-6 col-md-6">
                                <div class="card custom-card overflow-hidden" style="background:#6AA32D!important;">
                                    <div class="card-body">
                                        <div class="d-flex align-items-top justify-content-between">
                                            <div>
                                                <span class="avatar avatar-md avatar-rounded"
                                                    style="background-color: white!important;">
                                                    <i style="color:#6AA32D" class="fa-solid fa-chart-line fs-16"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill ms-3">
                                                <div
                                                    class="d-flex align-items-center justify-content-between flex-wrap">
                                                    <div>
                                                        <p class="text-white mb-0">Total Sales</p>
                                                        <h4 style="color:wheat;" class="fw-semibold mt-1">
                                                            <?php echo $summary ? $summary->total_sold : 0 ?>
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Quantity Sold -->
                            <div class="col-xxl-6 col-lg-6 col-md-6">
                                <div class="card custom-card overflow-hidden">
                                    <div class="card-body">
                                        <div class="d-flex align-items-top justify-content-between">
                                            <div>
                                                <span class="avatar avatar-md avatar-rounded"
                                                    style="background:#6AA32D;">
                                                    <i class="fa-solid fa-weight-scale fs-16"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill ms-3">
                                                <div
                                                    class="d-flex align-items-center justify-content-between flex-wrap">
                                                    <div>
                                                        <p class="text-muted mb-0" style="color:#6AA32D;">Total Quantity
                                                            Sold</p>
                                                        <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                            <?php echo $summary ? number_format($summary->total_quantity, 2) : 0 ?>
                                                            KGs
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Revenue -->
                            <div class="col-xxl-6 col-lg-6 col-md-6">
                                <div class="card custom-card overflow-hidden">
                                    <div class="card-body">
                                        <div class="d-flex align-items-top justify-content-between">
                                            <div>
                                                <span class="avatar avatar-md avatar-rounded"
                                                    style="background:#6AA32D;">
                                                    <i class="fa-solid fa-money-bill-wave fs-16"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill ms-3">
                                                <div
                                                    class="d-flex align-items-center justify-content-between flex-wrap">
                                                    <div>
                                                        <p class="text-muted mb-0" style="color:#6AA32D;">Total Revenue
                                                        </p>
                                                        <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                            KES
                                                            <?php echo $summary ? number_format($summary->total_sales_value, 2) : 0 ?>
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Average Price -->
                            <div class="col-xxl-6 col-lg-6 col-md-6">
                                <div class="card custom-card overflow-hidden">
                                    <div class="card-body">
                                        <div class="d-flex align-items-top justify-content-between">
                                            <div>
                                                <span class="avatar avatar-md avatar-rounded"
                                                    style="background:#6AA32D;">
                                                    <i class="fa-solid fa-tags fs-16"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill ms-3">
                                                <div
                                                    class="d-flex align-items-center justify-content-between flex-wrap">
                                                    <div>
                                                        <p class="text-muted mb-0" style="color:#6AA32D;">Avg. Price per
                                                            KG</p>
                                                        <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                            KES
                                                            <?php echo $summary ? number_format($summary->avg_price_per_kg, 2) : 0 ?>
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Product and Buyer Breakdown Section -->
                        <div class="row mt-3">
                            <!-- Product Breakdown -->
                            <div class="col-lg-6">
                                <div class="card custom-card">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <i class="fa-solid fa-box-open me-2 text-success"></i> Sold Products
                                            Breakdown
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th><i class="fa-solid fa-lemon me-1 text-warning"></i> Product
                                                        </th>
                                                        <th><i class="fa-solid fa-weight-scale me-1 text-primary"></i>
                                                            Quantity (KGs)</th>
                                                        <th><i
                                                                class="fa-solid fa-money-bill-wave me-1 text-success"></i>
                                                            Revenue (KES)</th>
                                                        <th><i class="fa-solid fa-tag me-1 text-danger"></i> Avg. Price
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($products)): ?>
                                                    <?php foreach ($products as $product): ?>
                                                    <tr>
                                                        <td class="fw-medium">
                                                            <?php echo htmlspecialchars($product->product_name) ?></td>
                                                        <td>
                                                            <span class="badge bg-light text-primary rounded-pill">
                                                                <?php echo number_format($product->total_quantity, 2) ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="text-success fw-bold">
                                                                KES <?php echo number_format($product->sold_value, 2) ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-light text-dark rounded-pill">
                                                                KES <?php echo number_format($product->avg_price, 2) ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                    <?php else: ?>
                                                    <tr>
                                                        <td colspan="4" class="text-center">
                                                            <i
                                                                class="fa-solid fa-circle-exclamation text-warning me-2"></i>
                                                            No sold products found
                                                        </td>
                                                    </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Buyer Breakdown -->
                            <div class="col-lg-6">
                                <div class="card custom-card">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <i class="fa-solid fa-users me-2 text-success"></i> Buyer Statistics
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th><i class="fa-solid fa-building me-1 text-primary"></i> Buyer
                                                        </th>
                                                        <th><i class="fa-solid fa-cart-shg me-1 text-warning"></i>
                                                            Purchases</th>
                                                        <th><i class="fa-solid fa-scale-balanced me-1 text-info"></i>
                                                            Quantity (KGs)</th>
                                                        <th><i class="fa-solid fa-sack-dollar me-1 text-success"></i>
                                                            Value (KES)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($buyers)): ?>
                                                    <?php foreach ($buyers as $buyer): ?>
                                                    <tr>
                                                        <td class="fw-medium">
                                                            <i class="fa-solid fa-building-user text-primary me-1"></i>
                                                            <?php echo htmlspecialchars(trim($buyer->buyer)) ?>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-warning text-dark rounded-pill">
                                                                <?php echo $buyer->purchase_count ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-info text-white rounded-pill">
                                                                <?php echo number_format($buyer->total_quantity, 2) ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="text-success fw-bold">
                                                                KES <?php echo number_format($buyer->total_value, 2) ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                    <?php else: ?>
                                                    <tr>
                                                        <td colspan="4" class="text-center">
                                                            <i class="fa-solid fa-circle-info text-info me-2"></i> No
                                                            buyer information found
                                                        </td>
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

                                        <div class="card-body">
                                            <?php include "../graphs/montly-sales-distribution.php" ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="card custom-card">
                                    <div class="card-header justify-content-between">

                                        <div class="card-body">
                                            <?php include "../graphs/buyer-distribution.php" ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="card custom-card">
                                    <div class="card-header justify-content-between">

                                        <div class="card-body">
                                            <?php include "../graphs/produce-sales-perfomance-distribution.php" ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Produce Deliveries Section -->
                <div class="row">
                    <div class="col-xl-12">
                        <div id="displayDeliveries">
                            <!-- Content will be loaded here by AJAX -->
                            <div class="d-flex justify-content-center my-5">
                                <div class="spinner-border text-success" role="status">
                                    <span class="visually-hidden">Loading...</span>
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
    $(document).ready(() => {
        displayDeliveries();
    });

    function displayDeliveries() {
        let displayDeliveries = "true";
        $.ajax({
            url: "http://localhost/dfcs/ajax/produce-controller/display-farmer-sales.php",
            type: 'POST',
            data: {
                displayDeliveries: displayDeliveries,
            },
            success: function(data, status) {
                $('#displayDeliveries').html(data);
            },
            error: function(xhr, status, error) {
                console.error("Error loading deliveries:", error);
                $('#displayDeliveries').html(
                    '<div class="alert alert-danger">Error loading produce deliveries. Please try again later.</div>'
                );
            }
        });
    }

    function viewDeliveryDetails(deliveryId) {
        // Redirect to delivery details page
        window.location.href = "http://localhost/dfcs/farmers/produce/view-details?id=" + deliveryId;
    }
    </script>
</body>



</html>