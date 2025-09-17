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


                <!-- End::page-header -->
            </div>
            <!-- row to display my produce details -->
            <?php
                // Check if ID is provided
                if (!isset($_GET['id']) || empty($_GET['id'])) {
                    echo '<div class="alert alert-danger">No produce ID specified</div>';
                    exit;
                }
                
                $app = new App;
                $produceId = intval($_GET['id']);
                $userId = $_SESSION['user_id'];
                
                // Fetch the produce details
                $query = "SELECT 
                            pd.id,
                            pd.farm_product_id,
                            pd.quantity,
                            pd.unit_price,
                            pd.total_value,
                            pd.quality_grade,
                            pd.delivery_date,
                            pd.status,
                            pd.notes,
                            pd.created_at,
                            pt.name as product_name,
                            f.id as farm_id,
                            f.name as farm_name,
                            f.location as farm_location,
                            CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                            u.phone as farmer_phone,
                            u.email as farmer_email
                          FROM produce_deliveries pd
                          JOIN farm_products fp ON pd.farm_product_id = fp.id
                          JOIN product_types pt ON fp.product_type_id = pt.id
                          JOIN farms f ON fp.farm_id = f.id
                          JOIN farmers fm ON f.farmer_id = fm.id
                          JOIN users u ON fm.user_id = u.id
                          WHERE pd.id = :produce_id";
                
                $params = [
                    ':produce_id' => $produceId
                ];
                
                $produce = $app->selectOne($query, $params);
                
                // Check if the produce exists
                if (!$produce) {
                    echo '<div class="alert alert-danger">Produce delivery not found</div>';
                    exit;
                }
                ?>

            <!-- Page Header with Download Button -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <h1 class="page-title fw-semibold fs-18 mb-0">Produce Delivery Details</h1>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-primary" id="downloadPDF">
                        <i class="ri-file-download-line me-1"></i> Download PDF
                    </button>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="javascript:history.back()">Produce</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Delivery Details</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Row 1: Essential Produce Information Cards -->
            <div class="row">
                <!-- Card 1: Basic Information -->
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                    <div class="card custom-card mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="avatar avatar-md avatar-rounded bg-primary">
                                        <i class="ri-file-list-3-line fs-16"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted fs-12">Reference</p>
                                    <h5 class="fw-semibold mb-1">
                                        DLVR<?php echo str_pad($produce->id, 5, '0', STR_PAD_LEFT); ?></h5>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-<?php 
                                echo ($produce->status == 'verified') ? 'info' : 
                                    (($produce->status == 'sold') ? 'success' : 
                                    (($produce->status == 'rejected') ? 'danger' : 'secondary')); 
                            ?>">
                                            <?php echo ucfirst($produce->status); ?>
                                        </span>
                                        <span class="text-muted ms-2 fs-12">
                                            <?php echo date('M d, Y', strtotime($produce->delivery_date)); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Product Details -->
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                    <div class="card custom-card mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="avatar avatar-md avatar-rounded" style="background-color: #6AA32D;">
                                        <i class="ri-plant-line fs-16"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted fs-12">Product</p>
                                    <h5 class="fw-semibold mb-1"><?php echo htmlspecialchars($produce->product_name); ?>
                                    </h5>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-<?php 
                                echo ($produce->quality_grade == 'A') ? 'success' : 
                                    (($produce->quality_grade == 'B') ? 'warning' : 'danger'); 
                            ?>">
                                            Grade <?php echo $produce->quality_grade; ?>
                                        </span>
                                        <span class="text-muted ms-2 fs-12">
                                            From: <?php echo htmlspecialchars($produce->farm_name); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Quantity & Value -->
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                    <div class="card custom-card mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="avatar avatar-md avatar-rounded bg-warning">
                                        <i class="ri-scales-3-line fs-16"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted fs-12">Quantity</p>
                                    <h5 class="fw-semibold mb-1"><?php echo number_format($produce->quantity, 2); ?> KGs
                                    </h5>
                                    <div class="d-flex align-items-center">
                                        <span class="text-dark fw-medium">
                                            KES <?php echo number_format($produce->unit_price, 2); ?> / KG
                                        </span>
                                        <span class="ms-2 text-success fw-medium">
                                            KES <?php echo number_format($produce->total_value, 2); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Payment Status -->
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                    <div class="card custom-card mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="avatar avatar-md avatar-rounded bg-success">
                                        <i class="ri-money-dollar-circle-line fs-16"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted fs-12">Payment Status</p>
                                    <h5 class="fw-semibold mb-1">
                                        <?php if($produce->status == 'sold'): ?>
                                        Paid
                                        <?php elseif($produce->status == 'verified'): ?>
                                        Ready for Sale
                                        <?php elseif($produce->status == 'rejected'): ?>
                                        Rejected
                                        <?php else: ?>
                                        Pending Verification
                                        <?php endif; ?>
                                    </h5>
                                    <div class="text-muted fs-12">
                                        <?php if($produce->status == 'sold'): ?>
                                        <?php 
                                    $commission = $produce->total_value * 0.10; // 10% commission
                                    $farmerPayment = $produce->total_value - $commission;
                                    echo "Payment: KES " . number_format($farmerPayment, 2);
                                ?>
                                        <?php elseif($produce->status == 'verified'): ?>
                                        Expected after sale
                                        <?php else: ?>
                                        Not applicable
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Row 2: Main Information Sections -->
            <div class="row">
                <!-- Left Column: Product & Farm Details -->
                <div class="col-xl-12 col-lg-12">
                    <div class="card custom-card mb-4">
                        <div class="card-header border-bottom">
                            <div class="card-title">
                                <i class="ri-information-line me-2" style="color:#6AA32D"></i>Product & Farm Details
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td class="fw-medium bg-light" width="30%">Product Name</td>
                                            <td><?php echo htmlspecialchars($produce->product_name); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium bg-light">Quality Grade</td>
                                            <td>
                                                <span class="badge bg-<?php 
                                        echo ($produce->quality_grade == 'A') ? 'success' : 
                                            (($produce->quality_grade == 'B') ? 'warning' : 'danger'); 
                                    ?>">
                                                    Grade <?php echo $produce->quality_grade; ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium bg-light">Farm Name</td>
                                            <td><?php echo htmlspecialchars($produce->farm_name); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium bg-light">Farm Location</td>
                                            <td><?php echo htmlspecialchars($produce->farm_location); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium bg-light">Farmer</td>
                                            <td><?php echo htmlspecialchars($produce->farmer_name); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium bg-light">Contact</td>
                                            <td>
                                                <div><i class="ri-phone-line me-1"></i>
                                                    <?php echo htmlspecialchars($produce->farmer_phone); ?></div>
                                                <div><i class="ri-mail-line me-1"></i>
                                                    <?php echo htmlspecialchars($produce->farmer_email); ?></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium bg-light">Delivery Date</td>
                                            <td><?php echo date('M d, Y', strtotime($produce->delivery_date)); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <?php
                           // Get farm cultivation details - this gives us more farm-specific information
                           $cultivationQuery = "SELECT 
                               ft.name as fruit_type,
                               ct.name as cultivation_type,
                               hm.name as harvesting_method,
                               hf.name as harvest_frequency,
                               ffm.acreage
                           FROM farm_fruit_mapping ffm
                           JOIN farm_products fp ON ffm.farm_id = fp.farm_id AND ffm.fruit_type_id = fp.product_type_id
                           JOIN fruit_types ft ON ffm.fruit_type_id = ft.id
                           JOIN cultivation_types ct ON ffm.cultivation_type_id = ct.id
                           JOIN harvesting_methods hm ON ffm.harvesting_method_id = hm.id
                           JOIN harvest_frequencies hf ON ffm.harvest_frequency_id = hf.id
                           WHERE fp.id = :farm_product_id
                           LIMIT 1";
           
                           $cultivationParams = [
                               ':farm_product_id' => $produce->farm_product_id
                           ];
           
                           $cultivation = $app->selectOne($cultivationQuery, $cultivationParams);
           
                           if ($cultivation):
                           ?>
                            <div class="mt-4">
                                <h6 class="fw-semibold">Cultivation Information</h6>
                                <div class="row g-3 mt-2">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center p-3 rounded-3"
                                            style="background-color: rgba(106, 163, 45, 0.1);">
                                            <div class="me-3">
                                                <span class="avatar avatar-md" style="background-color: #6AA32D">
                                                    <i class="ri-plant-line fs-16"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <label class="form-label text-muted mb-0">Fruit Type</label>
                                                <div class="fw-semibold fs-15">
                                                    <?php echo htmlspecialchars($cultivation->fruit_type ?? 'Not Available'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center p-3 rounded-3"
                                            style="background-color: rgba(106, 163, 45, 0.1);">
                                            <div class="me-3">
                                                <span class="avatar avatar-md" style="background-color: #6AA32D">
                                                    <i class="ri-landscape-line fs-16"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <label class="form-label text-muted mb-0">Acreage</label>
                                                <div class="fw-semibold fs-15">
                                                    <?php echo number_format($cultivation->acreage, 2) ?? 'Not Available'; ?>
                                                    acres
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center p-3 rounded-3"
                                            style="background-color: rgba(106, 163, 45, 0.1);">
                                            <div class="me-3">
                                                <span class="avatar avatar-md" style="background-color: #6AA32D">
                                                    <i class="ri-seedling-line fs-16"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <label class="form-label text-muted mb-0">Cultivation Method</label>
                                                <div class="fw-semibold fs-15">
                                                    <?php echo htmlspecialchars($cultivation->cultivation_type ?? 'Not Available'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
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
                                                    <?php echo htmlspecialchars($cultivation->harvesting_method ?? 'Not Available'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12 col-lg-12">
                    <div class="card custom-card mb-4">
                        <div class="card-header border-bottom">
                            <div class="card-title">
                                <i class="ri-exchange-line me-2" style="color:#6AA32D"></i>Transaction Status
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="timeline-main-container">
                                <div class="timeline-container">
                                    <!-- Delivered -->
                                    <div class="timeline-block timeline-completed">
                                        <div class="timeline-content">
                                            <span class="timeline-icon bg-primary"
                                                style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                                                <i class="ri-truck-line fs-18"></i>
                                            </span>
                                            <div class="align-items-center d-flex timeline-indicator-text">
                                                <span class="fw-semibold me-1">Delivered</span>
                                                <span
                                                    class="badge bg-secondary badge-sm"><?php echo date('M d, Y', strtotime($produce->delivery_date)); ?></span>
                                            </div>
                                            <div class="p-3 border-start border-2 ms-4 mt-2"
                                                style="border-color: #e9e9e9 !important; background-color: #f9f9f9; border-radius: 6px;">
                                                Produce delivery recorded. Quantity:
                                                <?php echo number_format($produce->quantity, 2); ?> KGs,
                                                Value: KES <?php echo number_format($produce->total_value, 2); ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Verification -->
                                    <div
                                        class="timeline-block <?php echo ($produce->status == 'pending') ? '' : 'timeline-completed'; ?> mt-4">
                                        <div class="timeline-content">
                                            <span
                                                class="timeline-icon <?php echo ($produce->status == 'pending') ? 'bg-secondary' : (($produce->status == 'rejected') ? 'bg-danger' : 'bg-success'); ?>"
                                                style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                                                <i
                                                    class="<?php echo ($produce->status == 'rejected') ? 'ri-close-line' : 'ri-check-double-line'; ?> fs-18"></i>
                                            </span>
                                            <div class="align-items-center d-flex timeline-indicator-text">
                                                <span class="fw-semibold me-1">Verification</span>
                                                <?php if ($produce->status != 'pending'): ?>
                                                <span
                                                    class="badge <?php echo ($produce->status == 'rejected') ? 'bg-danger' : 'bg-info'; ?> badge-sm">
                                                    <?php echo ($produce->status == 'rejected') ? 'Rejected' : 'Verified'; ?>
                                                </span>
                                                <?php else: ?>
                                                <span class="badge bg-warning badge-sm">Pending</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="p-3 border-start border-2 ms-4 mt-2" style="border-color: <?php echo ($produce->status == 'rejected') ? '#f9d2d0' : '#e9e9e9'; ?> !important; 
                                                  background-color: <?php echo ($produce->status == 'rejected') ? '#fff5f5' : '#f9f9f9'; ?>;
                                                  border-radius: 6px;">
                                                <?php if ($produce->status == 'rejected'): ?>
                                                <strong class="text-danger">Produce was rejected</strong>
                                                <?php 
                                                     // Get rejection reason from comments
                                                     $rejectionQuery = "SELECT comment 
                                                                       FROM comments 
                                                                       WHERE reference_type = 'produce_delivery' 
                                                                       AND reference_id = :produce_id
                                                                       AND is_rejection_reason = 1
                                                                       LIMIT 1";
                                                     $rejectionParams = [':produce_id' => $produce->id];
                                                     $rejectionComment = $app->selectOne($rejectionQuery, $rejectionParams);
                                                     if ($rejectionComment): 
                                                     ?>
                                                <div class="mt-2 p-2 bg-danger bg-opacity-10 rounded">
                                                    <i class="ri-information-line text-danger me-1"></i>
                                                    <span class="text-danger">Reason:
                                                        <?php echo htmlspecialchars($rejectionComment->comment); ?></span>
                                                </div>
                                                <?php endif; ?>
                                                <?php elseif ($produce->status == 'verified' || $produce->status == 'sold'): ?>
                                                Produce quality verified. Ready for sale.
                                                <?php else: ?>
                                                Awaiting verification by SACCO staff.
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sale -->
                                    <div
                                        class="timeline-block <?php echo ($produce->status == 'sold') ? 'timeline-completed' : ''; ?> mt-4">
                                        <div class="timeline-content">
                                            <span
                                                class="timeline-icon <?php echo ($produce->status == 'sold') ? 'bg-success' : (($produce->status == 'rejected') ? 'bg-secondary' : 'bg-secondary'); ?>"
                                                style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                                                <i class="ri-money-dollar-circle-line fs-18"></i>
                                            </span>
                                            <div class="align-items-center d-flex timeline-indicator-text">
                                                <span class="fw-semibold me-1">Sale</span>
                                                <span
                                                    class="badge bg-<?php echo ($produce->status == 'sold') ? 'success' : (($produce->status == 'rejected') ? 'danger' : 'secondary'); ?> badge-sm">
                                                    <?php echo ($produce->status == 'sold') ? 'Completed' : (($produce->status == 'rejected') ? 'N/A' : 'Pending'); ?>
                                                </span>
                                            </div>
                                            <div class="p-3 border-start border-2 ms-4 mt-2"
                                                style="border-color: #e9e9e9 !important; background-color: #f9f9f9; border-radius: 6px;">
                                                <?php if ($produce->status == 'sold'): 
                                                                // Extract buyer info from notes
                                                                $buyerInfo = "Unknown Buyer";
                                                                if ($produce->notes && strpos($produce->notes, 'Sold to:') !== false) {
                                                                    $parts = explode('Sold to:', $produce->notes);
                                                                    if (isset($parts[1])) {
                                                                        $infoText = trim($parts[1]);
                                                                        if (strpos($infoText, '.') !== false) {
                                                                            $buyerInfo = trim(substr($infoText, 0, strpos($infoText, '.')));
                                                                        } else {
                                                                            $buyerInfo = $infoText;
                                                                        }
                                                                    }
                                                                }
                                                            ?>
                                                <div>Produce sold to
                                                    <strong><?php echo htmlspecialchars($buyerInfo); ?></strong>.
                                                </div>
                                                <?php 
                                                    $commission = $produce->total_value * 0.10; // 10% commission
                                                    $farmerPayment = $produce->total_value - $commission;
                                                    ?>
                                                <div class="row mt-3 gx-2">
                                                    <div class="col-md-4">
                                                        <div class="p-2 border rounded text-center"
                                                            style="border-radius: 6px;">
                                                            <div class="fs-12 text-muted">Sale Value</div>
                                                            <div class="fw-semibold">KES
                                                                <?php echo number_format($produce->total_value, 2); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="p-2 border rounded text-center"
                                                            style="border-radius: 6px;">
                                                            <div class="fs-12 text-muted">Commission (10%)</div>
                                                            <div class="fw-semibold">KES
                                                                <?php echo number_format($commission, 2); ?></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="p-2 border rounded text-center bg-success bg-opacity-10"
                                                            style="border-radius: 6px;">
                                                            <div class="fs-12 text-muted">Farmer Payment</div>
                                                            <div class="fw-semibold text-success">KES
                                                                <?php echo number_format($farmerPayment, 2); ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php elseif ($produce->status == 'verified'): ?>
                                                Produce is verified and ready for sale.
                                                <?php elseif ($produce->status == 'rejected'): ?>
                                                <span class="text-muted">Sale not applicable - produce was
                                                    rejected.</span>
                                                <?php else: ?>
                                                Produce must be verified before it can be sold.
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Payment -->
                                    <div
                                        class="timeline-block <?php echo ($produce->status == 'sold') ? 'timeline-completed' : ''; ?> mt-4">
                                        <div class="timeline-content">
                                            <span
                                                class="timeline-icon <?php echo ($produce->status == 'sold') ? 'bg-success' : (($produce->status == 'rejected') ? 'bg-secondary' : 'bg-secondary'); ?>"
                                                style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                                                <i class="ri-bank-line fs-18"></i>
                                            </span>
                                            <div class="align-items-center d-flex timeline-indicator-text">
                                                <span class="fw-semibold me-1">Payment</span>
                                                <span
                                                    class="badge bg-<?php echo ($produce->status == 'sold') ? 'success' : (($produce->status == 'rejected') ? 'danger' : 'secondary'); ?> badge-sm">
                                                    <?php echo ($produce->status == 'sold') ? 'Processed' : (($produce->status == 'rejected') ? 'N/A' : 'Pending'); ?>
                                                </span>
                                            </div>
                                            <div class="p-3 border-start border-2 ms-4 mt-2"
                                                style="border-color: #e9e9e9 !important; background-color: #f9f9f9; border-radius: 6px;">
                                                <?php if ($produce->status == 'sold'): 
                                                               $commission = $produce->total_value * 0.10; // 10% commission
                                                               $farmerPayment = $produce->total_value - $commission;
                                                           ?>
                                                <div class="d-flex align-items-center">
                                                    <div
                                                        style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background-color: rgba(25, 135, 84, 0.1); margin-right: 10px;">
                                                        <i class="ri-check-line text-success"></i>
                                                    </div>
                                                    <div>
                                                        Payment of <strong>KES
                                                            <?php echo number_format($farmerPayment, 2); ?></strong>
                                                        processed to farmer.
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center mt-3">
                                                    <div
                                                        style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background-color: rgba(13, 110, 253, 0.1); margin-right: 10px;">
                                                        <i class="ri-coins-line text-primary"></i>
                                                    </div>
                                                    <div>
                                                        Commission of <strong>KES
                                                            <?php echo number_format($commission, 2); ?></strong>
                                                        credited to SACCO.
                                                    </div>
                                                </div>
                                                <?php elseif ($produce->status == 'verified'): ?>
                                                <div class="text-muted">Payment will be processed after sale.</div>
                                                <?php elseif ($produce->status == 'rejected'): ?>
                                                <div class="text-muted">Payment not applicable - produce was rejected.
                                                </div>
                                                <?php else: ?>
                                                <div class="text-muted">Payment pending verification and sale.</div>
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
            <!-- Row 3: Produce Status Progress Tracker (Improved) -->
            <div class="row">
                <div class="col-12">
                    <div class="card custom-card mb-4">
                        <div class="card-header border-bottom">
                            <div class="card-title">
                                <i class="ri-bar-chart-horizontal-line me-2" style="color:#6AA32D"></i>Produce Status
                                Progress
                            </div>
                        </div>
                        <div class="card-body px-4">
                            <?php 
                                         // Define all possible steps in order
                                         $steps = [
                                             [
                                                 'key' => 'delivered',
                                                 'label' => 'Produce Delivered', 
                                                 'description' => 'Delivered to SACCO',
                                                 'icon' => 'ri-truck-line'
                                             ],
                                             [
                                                 'key' => 'verification',
                                                 'label' => 'Quality Check', 
                                                 'description' => 'Quality verification',
                                                 'icon' => 'ri-shield-check-line'
                                             ],
                                             [
                                                 'key' => 'verified',
                                                 'label' => 'Verified', 
                                                 'description' => 'Ready for market',
                                                 'icon' => 'ri-check-double-line'
                                             ],
                                             [
                                                 'key' => 'market',
                                                 'label' => 'On Market', 
                                                 'description' => 'Available for sale',
                                                 'icon' => 'ri-store-line'
                                             ],
                                             [
                                                 'key' => 'sold',
                                                 'label' => 'Sold', 
                                                 'description' => 'Sold to buyers',
                                                 'icon' => 'ri-money-dollar-circle-line'
                                             ],
                                             [
                                                 'key' => 'payment',
                                                 'label' => 'Payment', 
                                                 'description' => 'Payment processed',
                                                 'icon' => 'ri-bank-line'
                                             ]
                                         ];
                         
                                         // Determine current step based on status
                                         $currentStep = 0;
                                         if ($produce->status == 'pending') {
                                             $currentStep = 1; // Verification stage
                                         } elseif ($produce->status == 'rejected') {
                                             $currentStep = 1; // Stopped at verification stage
                                         } elseif ($produce->status == 'verified') {
                                             $currentStep = 3; // On Market stage
                                         } elseif ($produce->status == 'sold') {
                                             $currentStep = 5; // Payment stage
                                         }
                         
                                         // Calculate progress percentage
                                         $totalSteps = count($steps) - 1; // -1 because we start from 0
                                         $progressPercentage = ($currentStep / $totalSteps) * 100;
                                         ?>

                            <!-- Overall progress bar -->
                            <div class="progress mb-5"
                                style="height: 8px; background-color: #eef2f7; border-radius: 20px;">
                                <div class="progress-bar <?php echo ($produce->status == 'rejected') ? 'bg-danger' : 'bg-success'; ?>"
                                    role="progressbar"
                                    style="width: <?php echo $progressPercentage; ?>%; border-radius: 20px;"
                                    aria-valuenow="<?php echo $progressPercentage; ?>" aria-valuemin="0"
                                    aria-valuemax="100"></div>
                            </div>

                            <!-- Step indicators with more space -->
                            <div class="row position-relative">
                                <?php foreach ($steps as $index => $step): 
                                             // Determine step status
                                             $stepStatus = '';
                                             $isPastStep = $index < $currentStep;
                                             $isCurrentStep = $index == $currentStep;
                                             $isRejected = $produce->status == 'rejected' && $index == 1;
                                             
                                             if ($isPastStep) {
                                                 $stepStatus = 'completed';
                                             } elseif ($isCurrentStep) {
                                                 $stepStatus = $isRejected ? 'rejected' : 'active';
                                             }
                                         ?>
                                <div class="col-md-2 text-center mb-4">
                                    <div class="position-relative">
                                        <!-- Circle Icon -->
                                        <div class="mx-auto" style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
                                        background-color: <?php 
                                            if ($stepStatus == 'completed') echo '#6AA32D';
                                            elseif ($stepStatus == 'active') echo '#FFC107';
                                            elseif ($stepStatus == 'rejected') echo '#DC3545';
                                            else echo '#e9ecef';
                                        ?>;
                                        color: <?php echo ($stepStatus) ? '#fff' : '#6c757d'; ?>;
                                        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
                                        position: relative;
                                        z-index: 2;">
                                            <i class="<?php echo $step['icon']; ?> fs-24"></i>

                                            <?php if ($isPastStep): ?>
                                            <!-- Checkmark for completed steps -->
                                            <div style="position: absolute; bottom: -5px; right: -5px; width: 24px; height: 24px; 
                                            border-radius: 50%; background-color: #28a745; color: white; 
                                            display: flex; align-items: center; justify-content: center;">
                                                <i class="ri-check-line fs-14"></i>
                                            </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Label and description -->
                                        <div class="mt-3">
                                            <h6 class="mb-1 fw-semibold"><?php echo $step['label']; ?></h6>
                                            <p class="mb-0 fs-12 text-muted"><?php echo $step['description']; ?></p>

                                            <?php if ($isCurrentStep): ?>
                                            <span
                                                class="badge <?php echo ($isRejected) ? 'bg-danger' : 'bg-warning'; ?> mt-2">
                                                <?php echo ($isRejected) ? 'Rejected' : 'In Progress'; ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>

                                <!-- Connecting line behind the steps -->
                                <div class="step-connector position-absolute" style="top: 30px; left: 50%; right: 0; height: 2px; background-color: #e9ecef; z-index: 1;
                                transform: translateX(-50%); width: 80%;">
                                </div>
                            </div>

                            <!-- Status summary box -->
                            <?php
                                       $alertClass = 'alert-warning';
                                       $iconClass = 'ri-time-line';
                                       $statusTitle = 'Awaiting Verification';
                                       $statusMessage = 'Your produce delivery is currently undergoing quality verification.';
                                       
                                       if ($produce->status == 'rejected') {
                                           $alertClass = 'alert-danger';
                                           $iconClass = 'ri-error-warning-line';
                                           $statusTitle = 'Produce Rejected';
                                           
                                           // Get rejection reason
                                           $rejectionQuery = "SELECT comment 
                                                             FROM comments 
                                                             WHERE reference_type = 'produce_delivery' 
                                                             AND reference_id = :produce_id
                                                             AND is_rejection_reason = 1
                                                             LIMIT 1";
                                           $rejectionParams = [':produce_id' => $produce->id];
                                           $rejectionComment = $app->selectOne($rejectionQuery, $rejectionParams);
                                           $reasonText = $rejectionComment ? $rejectionComment->comment : 'Please contact SACCO staff for details.';
                                           
                                           $statusMessage = "Your produce delivery has been rejected. Reason: {$reasonText}";
                                       } 
                                       elseif ($produce->status == 'verified') {
                                           $alertClass = 'alert-info';
                                           $iconClass = 'ri-information-line';
                                           $statusTitle = 'Produce Verified - Ready for Sale';
                                           $statusMessage = 'Your produce has passed quality verification and is now available in the market for sale.';
                                       }
                                       elseif ($produce->status == 'sold') {
                                           $alertClass = 'alert-success';
                                           $iconClass = 'ri-check-double-line';
                                           $statusTitle = 'Produce Sold - Payment Processed';
                                           
                                           $commission = $produce->total_value * 0.10;
                                           $farmerPayment = $produce->total_value - $commission;
                                           $statusMessage = "Your produce has been sold successfully. Payment of KES " . number_format($farmerPayment, 2) . " has been processed to your account.";
                                       }
                                       ?>

                            <div class="alert <?php echo $alertClass; ?> mt-2 mb-0">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <span
                                            style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background-color: rgba(255,255,255,0.3);">
                                            <i class="<?php echo $iconClass; ?> fs-20"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h5 class="mb-1"><?php echo $statusTitle; ?></h5>
                                        <p class="mb-0"><?php echo $statusMessage; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Row 3: Activity History & Logs -->
            <div class="row">
                <div class="col-12">
                    <div class="card custom-card mb-4">
                        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                            <div class="card-title mb-0">
                                <i class="ri-history-line me-2" style="color:#6AA32D"></i>Activity History
                            </div>
                            <div>

                            </div>
                        </div>
                        <div class="card-body">
                            <?php
                                            // Fetch all activity logs related to this produce
                                            $logQuery = "SELECT 
                                                            pl.id,
                                                            pl.action_type,
                                                            pl.description,
                                                            pl.created_at,
                                                            CONCAT(u.first_name, ' ', u.last_name) as user_name,
                                                            r.name as role_name
                                                         FROM produce_logs pl
                                                         JOIN users u ON pl.user_id = u.id
                                                         JOIN roles r ON u.role_id = r.id
                                                         WHERE pl.produce_delivery_id = '{$produce->id}'
                                                         ORDER BY pl.created_at DESC";
                                            
                                           
                                            $logs = $app->select_all($logQuery);
                                            
                                            // Fetch comments related to this produce
                                            $commentQuery = "SELECT 
                                                            c.id,
                                                            c.comment,
                                                            c.created_at,
                                                            c.is_rejection_reason,
                                                            ct.name as comment_type,
                                                            CONCAT(u.first_name, ' ', u.last_name) as user_name,
                                                            r.name as role_name
                                                           FROM comments c
                                                           JOIN comment_types ct ON c.comment_type_id = ct.id
                                                           JOIN users u ON c.user_id = u.id
                                                           JOIN roles r ON u.role_id = r.id
                                                           WHERE c.reference_type = 'produce_delivery' 
                                                           AND c.reference_id = '{$produce->id}'
                                                           ORDER BY c.created_at DESC";
                                            
                                            
                                            $comments = $app->select_all($commentQuery);
                                            
                                            // Combine logs and comments into a single timeline
                                            $timeline = [];
                                            
                                            if ($logs) {
                                                foreach ($logs as $log) {
                                                    $timeline[] = [
                                                        'type' => 'log',
                                                        'action' => $log->action_type,
                                                        'description' => $log->description,
                                                        'created_at' => $log->created_at,
                                                        'user_name' => $log->user_name,
                                                        'role_name' => $log->role_name
                                                    ];
                                                }
                                            }
                                            
                                            if ($comments) {
                                                foreach ($comments as $comment) {
                                                    $timeline[] = [
                                                        'type' => 'comment',
                                                        'action' => $comment->is_rejection_reason ? 'rejection_reason' : 'comment',
                                                        'comment_type' => $comment->comment_type,
                                                        'description' => $comment->comment,
                                                        'created_at' => $comment->created_at,
                                                        'user_name' => $comment->user_name,
                                                        'role_name' => $comment->role_name
                                                    ];
                                                }
                                            }
                                            
                                            // Sort timeline by date (newest first)
                                            usort($timeline, function($a, $b) {
                                                return strtotime($b['created_at']) - strtotime($a['created_at']);
                                            });
                                            ?>

                            <?php if (empty($timeline)): ?>
                            <div class="text-center py-5">
                                <img src="assets/images/no-data.svg" alt="No Activities" class="img-fluid mb-3"
                                    style="max-width: 200px;">
                                <h5 class="text-muted">No activity logs found</h5>
                                <p class="text-muted">There are no recorded activities for this produce delivery yet.
                                </p>
                            </div>
                            <?php else: ?>
                            <div class="activity-timeline">
                                <?php foreach ($timeline as $index => $item): 
                                            // Set icon and color based on action type
                                            $icon = 'ri-information-line';
                                            $color = '#6c757d';
                                            $bgColor = '#f8f9fa';
                        
                                               if ($item['type'] == 'log') {
                                                   switch ($item['action']) {
                                                       case 'received':
                                                           $icon = 'ri-truck-line';
                                                           $color = '#28a745';
                                                           $bgColor = 'rgba(40, 167, 69, 0.1)';
                                                           break;
                                                       case 'verified':
                                                           $icon = 'ri-check-double-line';
                                                           $color = '#17a2b8';
                                                           $bgColor = 'rgba(23, 162, 184, 0.1)';
                                                           break;
                                                       case 'rejected':
                                                           $icon = 'ri-close-circle-line';
                                                           $color = '#dc3545';
                                                           $bgColor = 'rgba(220, 53, 69, 0.1)';
                                                           break;
                                                       case 'sold':
                                                           $icon = 'ri-shopping-cart-line';
                                                           $color = '#fd7e14';
                                                           $bgColor = 'rgba(253, 126, 20, 0.1)';
                                                           break;
                                                       case 'paid':
                                                           $icon = 'ri-bank-line';
                                                           $color = '#6AA32D';
                                                           $bgColor = 'rgba(106, 163, 45, 0.1)';
                                                           break;
                                                       default:
                                                           $icon = 'ri-file-list-line';
                                                           $color = '#6c757d';
                                                           $bgColor = 'rgba(108, 117, 125, 0.1)';
                                                   }
                                               } else { // Comment
                                                   if ($item['action'] == 'rejection_reason') {
                                                       $icon = 'ri-error-warning-line';
                                                       $color = '#dc3545';
                                                       $bgColor = 'rgba(220, 53, 69, 0.1)';
                                                   } else {
                                                       $icon = 'ri-chat-1-line';
                                                       $color = '#6610f2';
                                                       $bgColor = 'rgba(102, 16, 242, 0.1)';
                                                   }
                                               }
                                           ?>
                                <div
                                    class="activity-item d-flex align-items-start <?php echo ($index !== 0) ? 'mt-4' : ''; ?>">
                                    <div class="activity-icon me-3" style="flex-shrink: 0;">
                                        <div
                                            style="width: 48px; height: 48px; border-radius: 50%; background-color: <?php echo $bgColor; ?>; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(0,0,0,0.05);">
                                            <i class="<?php echo $icon; ?> fs-20"
                                                style="color: <?php echo $color; ?>;"></i>
                                        </div>
                                    </div>
                                    <div class="activity-content border-start ps-3"
                                        style="position: relative; flex-grow: 1;">
                                        <!-- Timeline connector line -->
                                        <div
                                            style="position: absolute; left: 0; top: 0; bottom: 0; width: 1px; background-color: #e9ecef;">
                                        </div>

                                        <div class="d-flex justify-content-between">
                                            <h6 class="mb-1 fw-semibold">
                                                <?php 
                                    if ($item['type'] == 'log') {
                                        echo ucfirst(str_replace('_', ' ', $item['action']));
                                    } else {
                                        echo $item['action'] == 'rejection_reason' ? 'Rejection Reason' : 'Comment';
                                    }
                                    ?>
                                            </h6>
                                            <small class="text-muted fs-12" style="white-space: nowrap;">
                                                <?php echo date('M d, Y h:i A', strtotime($item['created_at'])); ?>
                                            </small>
                                        </div>

                                        <p class="mb-2"><?php echo htmlspecialchars($item['description']); ?></p>

                                        <div class="activity-meta d-flex align-items-center">
                                            <div class="badge bg-light text-dark me-2">
                                                <i
                                                    class="ri-user-line me-1"></i><?php echo htmlspecialchars($item['user_name']); ?>
                                            </div>
                                            <div
                                                class="badge <?php echo ($item['role_name'] == 'sacco_staff') ? 'bg-primary' : 'bg-secondary'; ?> badge-sm">
                                                <?php echo ucfirst(str_replace('_', ' ', $item['role_name'])); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Row 4: Comments & Notes -->
            <!-- Row 3: Activity History & Logs -->
            <div class="row">
                <div class="col-12">
                    <div class="card custom-card mb-4">
                        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                            <div class="card-title mb-0">
                                <i class="ri-history-line me-2" style="color:#6AA32D"></i>Produce Logs
                            </div>
                        </div>
                        <div class="card-body">
                            <?php
                                // Fetch all activity logs related to this produce
                                $logQuery = "SELECT 
                                                pl.id,
                                                pl.action_type,
                                                pl.description,
                                                pl.created_at,
                                                CONCAT(u.first_name, ' ', u.last_name) as user_name
                                             FROM produce_logs pl
                                             JOIN users u ON pl.user_id = u.id
                                             WHERE pl.produce_delivery_id = '{$produce->id}'
                                             ORDER BY pl.created_at DESC";
                                
                               
                                $logs = $app->select_all($logQuery);
                                ?>

                            <?php if (empty($logs)): ?>
                            <div class="text-center py-5">
                                <div class="avatar avatar-lg mx-auto mb-3 bg-light">
                                    <i class="ri-history-line fs-2 text-muted"></i>
                                </div>
                                <h5 class="text-muted">No Activity Logs Yet</h5>
                                <p class="text-muted">There are no recorded activities for this produce delivery yet.
                                </p>
                            </div>
                            <?php else: ?>
                            <div class="logs-list">
                                <?php foreach ($logs as $index => $log): 
                        // Set icon and color based on action type
                        $icon = 'ri-information-line';
                        $color = '#6c757d';
                        $bgColor = '#f8f9fa';
                        
                        switch ($log->action_type) {
                            case 'received':
                                $icon = 'ri-truck-line';
                                $color = '#28a745';
                                $bgColor = 'rgba(40, 167, 69, 0.1)';
                                break;
                            case 'verified':
                                $icon = 'ri-check-double-line';
                                $color = '#17a2b8';
                                $bgColor = 'rgba(23, 162, 184, 0.1)';
                                break;
                            case 'rejected':
                                $icon = 'ri-close-circle-line';
                                $color = '#dc3545';
                                $bgColor = 'rgba(220, 53, 69, 0.1)';
                                break;
                            case 'sold':
                                $icon = 'ri-shopping-cart-line';
                                $color = '#fd7e14';
                                $bgColor = 'rgba(253, 126, 20, 0.1)';
                                break;
                            case 'paid':
                                $icon = 'ri-bank-line';
                                $color = '#6AA32D';
                                $bgColor = 'rgba(106, 163, 45, 0.1)';
                                break;
                            default:
                                $icon = 'ri-file-list-line';
                                $color = '#6c757d';
                                $bgColor = 'rgba(108, 117, 125, 0.1)';
                        }
                    ?>
                                <div
                                    class="activity-item d-flex <?php echo ($index !== 0) ? 'mt-4 pt-4 border-top' : ''; ?>">
                                    <div class="me-3">
                                        <div
                                            style="width: 48px; height: 48px; border-radius: 50%; background-color: <?php echo $bgColor; ?>; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                            <i class="<?php echo $icon; ?> fs-20"
                                                style="color: <?php echo $color; ?>;"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="text-capitalize mb-0 fw-semibold">
                                                <?php echo ucfirst(str_replace('_', ' ', $log->action_type)); ?>
                                            </h6>
                                            <span class="text-muted fs-12">
                                                <?php echo date('M d, Y h:i A', strtotime($log->created_at)); ?>
                                            </span>
                                        </div>
                                        <p class="mb-2"><?php echo htmlspecialchars($log->description); ?></p>
                                        <div class="d-flex align-items-center">

                                            <span class="text-muted fs-12">
                                                <?php echo htmlspecialchars($log->user_name); ?>
                                            </span>
                                            <span class="badge bg-success-subtle text-success ms-2">SACCO staff</span>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
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
    <script>
    // Script for PDF download functionality
    document.getElementById('downloadPDF').addEventListener('click', function() {
        // Show loading message with toastr
        toastr.info('Preparing your invoice for download...', 'Please wait', {
            "positionClass": "toast-top-right",
            "progressBar": true,
            "timeOut": 0,
            "extendedTimeOut": 0,
            "closeButton": false,
            "hideMethod": "fadeOut"
        });

        // Get the produce ID from the page
        const produceId = <?php echo $_GET['id'] ?>;

        // AJAX call to generate PDF
        $.ajax({
            url: "http://localhost/dfcs/ajax/produce-controller/generate-invoice-pdf.php",
            type: "POST",
            data: {
                produceId: produceId
            },
            xhrFields: {
                responseType: 'blob' // Important for handling binary data like PDFs
            },
            success: function(response, status, xhr) {
                toastr.clear(); // Clear the loading message

                try {
                    // Create a blob from the PDF data
                    const blob = new Blob([response], {
                        type: 'application/pdf'
                    });

                    // Get filename from Content-Disposition header if available
                    let filename = 'Produce_Invoice_DLVR' + String(produceId).padStart(5, '0') +
                        '.pdf';
                    const contentDisposition = xhr.getResponseHeader('Content-Disposition');
                    if (contentDisposition) {
                        const filenameMatch = contentDisposition.match(
                            /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                        if (filenameMatch && filenameMatch[1]) {
                            filename = filenameMatch[1].replace(/['"]/g, '');
                        }
                    }

                    // Create a download link and trigger it
                    const url = window.URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = filename;
                    document.body.appendChild(link);
                    link.click();

                    // Clean up
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(link);

                    toastr.success('Invoice downloaded successfully', 'Success', {
                        "positionClass": "toast-top-right",
                        "progressBar": true,
                        "timeOut": 3000,
                        "extendedTimeOut": 1000,
                        "hideMethod": "fadeOut"
                    });
                } catch (e) {
                    // If response isn't a PDF, it might be a JSON error message
                    try {
                        const reader = new FileReader();
                        reader.onload = function() {
                            const errorJson = JSON.parse(reader.result);
                            toastr.error(errorJson.error || 'Failed to generate invoice',
                                'Error', {
                                    "positionClass": "toast-top-right",
                                    "progressBar": true,
                                    "timeOut": 5000
                                });
                        };
                        reader.readAsText(response);
                    } catch (readError) {
                        toastr.error('Failed to process server response', 'Error', {
                            "positionClass": "toast-top-right",
                            "progressBar": true,
                            "timeOut": 5000
                        });
                    }
                }
            },
            error: function(xhr, status, error) {
                toastr.clear();
                toastr.error('Failed to generate invoice. Please try again.', 'Error', {
                    "positionClass": "toast-top-right",
                    "progressBar": true,
                    "timeOut": 5000
                });
                console.error('Error generating PDF:', error);
            }
        });
    });
    </script>



</body>



</html>