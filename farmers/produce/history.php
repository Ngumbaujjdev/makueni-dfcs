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
                <!-- Farmer Produce Summary Cards -->
                <?php
                $app = new App;
                $userId = $_SESSION['user_id'];

                // Get the farmer's ID from the user ID
                $farmerQuery = "SELECT id FROM farmers WHERE user_id = $userId";
                $farmerResult = $app->select_one($farmerQuery);

                if ($farmerResult) {
                $farmerId = $farmerResult->id;

                // Get all produce deliveries for this farmer
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
                ORDER BY pd.delivery_date DESC";

                $deliveries = $app->select_all($query);

                // Get summary statistics for this farmer
                $summaryQuery = "SELECT
                COUNT(*) as total_deliveries,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                SUM(CASE WHEN status = 'verified' THEN 1 ELSE 0 END) as verified_count,
                SUM(CASE WHEN status = 'sold' THEN 1 ELSE 0 END) as sold_count,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_count,
                SUM(quantity) as total_quantity,
                SUM(CASE WHEN status = 'sold' THEN total_value ELSE 0 END) as total_sales_value
                FROM produce_deliveries pd
                JOIN farm_products fp ON pd.farm_product_id = fp.id
                JOIN farms f ON fp.farm_id = f.id
                WHERE f.farmer_id = $farmerId";

                $summary = $app->select_one($summaryQuery);

                // Get product breakdown
                $productQuery = "SELECT
                pt.name as product_name,
                COUNT(*) as delivery_count,
                SUM(pd.quantity) as total_quantity,
                SUM(CASE WHEN pd.status = 'sold' THEN pd.total_value ELSE 0 END) as sold_value
                FROM produce_deliveries pd
                JOIN farm_products fp ON pd.farm_product_id = fp.id
                JOIN product_types pt ON fp.product_type_id = pt.id
                JOIN farms f ON fp.farm_id = f.id
                WHERE f.farmer_id = $farmerId
                GROUP BY pt.name
                ORDER BY total_quantity DESC";

                $products = $app->select_all($productQuery);
                } else {
                $deliveries = [];
                $summary = null;
                $products = [];
                }
                ?>
                <div class="row mt-2">
                    <!-- Total Deliveries -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden" style="background:#6AA32D!important;">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded"
                                            style="background-color: white!important;">
                                            <i style="color:#6AA32D" class="fa-solid fa-truck-loading fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-white mb-0">Total Deliveries</p>
                                                <h4 style="color:wheat;" class="fw-semibold mt-1">
                                                    <?php echo $summary ? $summary->total_deliveries : 0 ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Deliveries -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-hourglass-half fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Pending Deliveries</p>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php echo $summary ? $summary->pending_count : 0 ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Verified Deliveries -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-check-circle fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Verified Deliveries
                                                </p>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php echo $summary ? $summary->verified_count : 0 ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sold Deliveries -->
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
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Sold Deliveries</p>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php echo $summary ? $summary->sold_count : 0 ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Produce Weight -->
                    <div class="col-xxl-4 col-lg-4 col-md-6 mt-2">
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
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Total Produce Weight
                                                </p>
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

                    <!-- Total Sales Value -->
                    <div class="col-xxl-4 col-lg-4 col-md-6 mt-2">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-cash-register fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Total Sales Value</p>
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

                    <!-- Rejected Deliveries -->
                    <div class="col-xxl-4 col-lg-4 col-md-6 mt-2">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-ban fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Rejected Deliveries
                                                </p>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php echo $summary ? $summary->rejected_count : 0 ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Breakdown Section -->
                <?php if (!empty($products)): ?>
                <div class="row mt-2">
                    <div class="col-lg-6 col-md-6">
                        <div class="card custom-card shadow-sm">
                            <div class="card-header bg-light">
                                <div class="card-title">
                                    <i class="fa-solid fa-boxes-stacked text-success me-2"></i> Product Breakdown
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr class="bg-light">
                                                <th><i class="fa-solid fa-apple-whole text-danger me-1"></i> Product
                                                </th>
                                                <th><i class="fa-solid fa-truck-loading text-warning me-1"></i>
                                                    Deliveries</th>
                                                <th><i class="fa-solid fa-weight-scale text-primary me-1"></i> Total
                                                    Quantity</th>
                                                <th><i class="fa-solid fa-sack-dollar text-success me-1"></i> Sales
                                                    Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($products as $product): ?>
                                            <tr>
                                                <td class="fw-medium">
                                                    <?php echo htmlspecialchars($product->product_name) ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-warning text-dark rounded-pill">
                                                        <?php echo $product->delivery_count ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress me-2" style="width: 60px; height: 6px;">
                                                            <div class="progress-bar bg-primary" role="progressbar"
                                                                style="width: <?php echo min(100, ($product->total_quantity / 1000) * 100); ?>%"
                                                                aria-valuenow="<?php echo $product->total_quantity; ?>"
                                                                aria-valuemin="0" aria-valuemax="1000"></div>
                                                        </div>
                                                        <span><?php echo number_format($product->total_quantity, 2) ?>
                                                            KGs</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-success fw-bold">
                                                        KES <?php echo number_format($product->sold_value, 2) ?>
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
                    <div class="col-lg-6 col-md-6">
                        <div class="card custom-card mt-2">
                            <div class="card-header justify-content-between">

                            </div>
                            <div class="card-body">
                                <?php include "../graphs/product-distribution.php" ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>


                <div class="row">
                    <div class="col-lg-12">
                        <div class="card custom-card mt-2">
                            <div class="card-header justify-content-between">

                            </div>
                            <div class="card-body">
                                <?php include "../graphs/monthly-distribution.php" ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="card custom-card mt-2">
                            <div class="card-header justify-content-between">

                            </div>
                            <div class="card-body">
                                <?php include "../graphs/sales-distribution.php" ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Produce Deliveries Section -->
                <div class="row">
                    <div class="col-xl-12">
                        <div id="displayDeliveries">
                            <!-- Content will be loaded here by AJAX -->
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border text-success" role="status">
                                    <span class="visually-hidden">Loading...</span>
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
    $(document).ready(() => {
        displayDeliveries();
    });

    function displayDeliveries() {
        let displayDeliveries = "true";
        $.ajax({
            url: "http://localhost/dfcs/ajax/produce-controller/display-farmer-deliveries.php",
            type: 'POST',
            data: {
                displayDeliveries: displayDeliveries,
            },
            success: function(data, status) {
                $('#displayDeliveries').html(data);
            },
        });
    }

    function viewDeliveryDetails(deliveryId) {
        // Redirect to delivery details page
        window.location.href = "http://localhost/dfcs/farmers/produce/view-details?id=" + deliveryId;
    }

    function cancelDelivery(deliveryId) {
        if (confirm("Are you sure you want to cancel this delivery? This action cannot be undone.")) {
            $.ajax({
                url: "http://localhost/dfcs/ajax/produce-controller/cancel-delivery.php",
                type: "POST",
                data: {
                    cancelId: deliveryId
                },
                success: function(data, status) {
                    let response = JSON.parse(data);
                    if (response.success) {
                        toastr.success('Delivery cancelled successfully', 'Success', {
                            "positionClass": "toast-top-right",
                            "progressBar": true,
                            "timeOut": 3000,
                            "extendedTimeOut": 1000,
                            "hideMethod": "fadeOut"
                        });

                        setTimeout(function() {
                            displayDeliveries();
                        }, 300);
                    } else {
                        toastr.error(response.message, 'Error', {
                            "positionClass": "toast-top-right",
                            "progressBar": true,
                            "timeOut": 3000,
                            "extendedTimeOut": 1000,
                            "hideMethod": "fadeOut"
                        });
                    }
                },
                error: function() {
                    toastr.error('An error occurred while cancelling the delivery', 'Error', {
                        "positionClass": "toast-top-right",
                        "progressBar": true,
                        "timeOut": 3000,
                        "extendedTimeOut": 1000,
                        "hideMethod": "fadeOut"
                    });
                }
            });
        }
    }
    </script>
</body>



</html>