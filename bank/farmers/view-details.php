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
<style>
.timeline-page {
    position: relative;
    padding-left: 1rem;
}

.timeline-page:before {
    content: '';
    position: absolute;
    top: 0;
    left: 11px;
    height: 100%;
    width: 2px;
    background-color: #f0f0f0;
}

.timeline-date-label {
    position: relative;
    left: -1rem;
    margin-bottom: 1.5rem;
    text-align: left;
}

.timeline-badge {
    width: 24px;
    height: 24px;
    position: absolute;
    left: 0;
    top: 0;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    transform: translateX(-50%);
    z-index: 1;
}

.timeline-item {
    position: relative;
    padding-left: 1.25rem;
    margin-bottom: 1.5rem;
}

.timeline-item-content {
    margin-left: 0.5rem;
}

.activity-details {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-top: 0.5rem;
}

.detail-item {
    display: flex;
    align-items: center;
    font-size: 0.875rem;
    color: #6c757d;
}
</style>


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
                        <!-- 5 is admin role from your roles table -->
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
                        <span class="fs-semibold text-muted pt-5">Farmers Dashboard</span>
                    </div>
                </div>

                <!-- End::page-header -->
                <?php 
                                            
                                $app = new App();
                                
                                // Get farmer ID from URL
                                $farmerId = isset($_GET['id']) ? intval($_GET['id']) : 0;
                                
                                if (!$farmerId) {
                                    header("Location: index.php");
                                    exit();
                                }
                                
                                // 1. Get Basic Farmer Information
                                $farmerQuery = "SELECT f.*, u.first_name, u.last_name, u.email, u.phone, u.location, u.profile_picture,
                                                u.created_at as registration_date, fc.name as category_name
                                                FROM farmers f
                                                JOIN users u ON f.user_id = u.id
                                                LEFT JOIN farmer_category_mapping fcm ON f.id = fcm.farmer_id
                                                LEFT JOIN farmer_categories fc ON fcm.category_id = fc.id
                                                WHERE f.id = $farmerId";
                                $farmer = $app->select_one($farmerQuery);
                                
                              
                                 // 2. Get Farming Activities
                                  $activitiesQuery = "SELECT 
                                      fp.product_type_id as product_id,
                                      CASE 
                                          WHEN ft.name = 'Fruit Farm' THEN 
                                              CONCAT('Fruit - ', GROUP_CONCAT(DISTINCT frt.name SEPARATOR ', '))
                                          ELSE ft.name
                                      END as farming_type
                                      FROM farms f
                                      JOIN farm_types ft ON f.farm_type_id = ft.id
                                      LEFT JOIN farm_products fp ON f.id = fp.farm_id
                                      LEFT JOIN farm_fruit_mapping ffm ON f.id = ffm.farm_id
                                      LEFT JOIN fruit_types frt ON ffm.fruit_type_id = frt.id
                                      WHERE f.farmer_id = $farmerId
                                      GROUP BY f.id, ft.name";
                                  $activities = $app->select_all($activitiesQuery);
                                
                                // 3. Get Financial Overview
                                $financialQuery = "SELECT 
                                    (SELECT COALESCE(SUM(pd.total_value), 0)
                                     FROM produce_deliveries pd
                                     JOIN farm_products fp ON pd.farm_product_id = fp.id
                                     JOIN farms fm ON fp.farm_id = fm.id
                                     WHERE fm.farmer_id = $farmerId) as total_earnings,
                                    
                                    (SELECT COALESCE(SUM(al.remaining_balance), 0)
                                     FROM approved_loans al
                                     JOIN loan_applications la ON al.loan_application_id = la.id
                                     WHERE la.farmer_id = $farmerId AND al.status = 'active') as outstanding_loans,
                                    
                                    (SELECT COALESCE(SUM(aic.remaining_balance), 0)
                                     FROM approved_input_credits aic
                                     JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                     WHERE ica.farmer_id = $farmerId AND aic.status = 'active') as input_credit_balance";
                                $financials = $app->select_one($financialQuery);
                                
                                // 4. Get Farm Performance
                                $performanceQuery = "SELECT 
                                    COUNT(pd.id) as total_deliveries,
                                    AVG(CASE WHEN pd.quality_grade = 'A' THEN 3 
                                             WHEN pd.quality_grade = 'B' THEN 2
                                             WHEN pd.quality_grade = 'C' THEN 1 END) as avg_quality_score,
                                    COUNT(CASE WHEN pd.status = 'accepted' THEN 1 END) * 100.0 / COUNT(pd.id) as acceptance_rate
                                    FROM produce_deliveries pd
                                    JOIN farm_products fp ON pd.farm_product_id = fp.id
                                    JOIN farms f ON fp.farm_id = f.id
                                    WHERE f.farmer_id = $farmerId";
                                $performance = $app->select_one($performanceQuery);
                                
                                // 5. Get Active Farms
                                $farmsQuery = "SELECT f.*, ft.name as farm_type_name,
                                               COUNT(fp.id) as product_count
                                               FROM farms f
                                               JOIN farm_types ft ON f.farm_type_id = ft.id
                                               LEFT JOIN farm_products fp ON f.id = fp.farm_id
                                               WHERE f.farmer_id = $farmerId
                                               GROUP BY f.id";
                                $farms = $app->select_all($farmsQuery);
                                
                                // 6. Get Recent Deliveries
                                $deliveriesQuery = "SELECT pd.*, pt.name as product_name, f.name as farm_name
                                                    FROM produce_deliveries pd
                                                    JOIN farm_products fp ON pd.farm_product_id = fp.id
                                                    JOIN farms f ON fp.farm_id = f.id
                                                    JOIN product_types pt ON fp.product_type_id = pt.id
                                                    WHERE f.farmer_id = $farmerId
                                                    ORDER BY pd.delivery_date DESC
                                                    LIMIT 5";
                                $recentDeliveries = $app->select_all($deliveriesQuery);
                                
                                // 7. Get Active Loans
                                $loansQuery = "SELECT la.*, al.*, b.name as bank_name, lt.name as loan_type_name
                                               FROM loan_applications la
                                               JOIN approved_loans al ON la.id = al.loan_application_id
                                               LEFT JOIN banks b ON la.bank_id = b.id
                                               JOIN loan_types lt ON la.loan_type_id = lt.id
                                               WHERE la.farmer_id = $farmerId AND al.status = 'active'";
                                $activeLoans = $app->select_all($loansQuery);
                                
                                // 8. Get Activity Logs
                                $logsQuery = "SELECT al.*, u.first_name, u.last_name
                                              FROM activity_logs al
                                              JOIN users u ON al.user_id = u.id
                                              WHERE al.description LIKE CONCAT('%', (SELECT registration_number FROM farmers WHERE id = $farmerId), '%')
                                              ORDER BY al.created_at DESC
                                              LIMIT 10";
                                $activityLogs = $app->select_all($logsQuery);
                                $inputCreditsQuery = "SELECT ica.*, aic.*, a.name as agrovet_name
                                                 FROM input_credit_applications ica
                                                 LEFT JOIN approved_input_credits aic ON ica.id = aic.credit_application_id
                                                 LEFT JOIN agrovets a ON ica.agrovet_id = a.id
                                                 WHERE ica.farmer_id = $farmerId 
                                                 AND aic.status = 'active'
                                                 ORDER BY ica.application_date DESC";
                                 $inputCredits = $app->select_all($inputCreditsQuery);
                                                                 
                                ?>


                <!-- Farmer Profile Header -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <img src="http://localhost/dfcs/<?= $farmer->profile_picture ?? 'http://localhost/dfcs/assets/images/faces/face-image-1.jpg' ?>"
                                            class="img-fluid rounded-circle" style="width: 100px; height: 100px;"
                                            alt="Farmer Photo">
                                    </div>
                                    <div>
                                        <h4 class="mb-1">
                                            <?php echo htmlspecialchars($farmer->first_name . ' ' . $farmer->last_name) ?>
                                        </h4>
                                        <p class="mb-1">
                                            <span
                                                class="badge bg-<?php echo $farmer->is_verified ? 'success' : 'warning' ?>">
                                                <?php echo $farmer->is_verified ? 'Verified Farmer' : 'Pending Verification' ?>
                                            </span>
                                            <span
                                                class="badge bg-info ms-2"><?php echo htmlspecialchars($farmer->registration_number) ?></span>
                                            <span
                                                class="badge bg-primary ms-2"><?php echo htmlspecialchars($farmer->category_name ?? 'Uncategorized') ?></span>
                                        </p>
                                        <p class="text-muted mb-0">
                                            <i class="ri-map-pin-line me-1"></i>
                                            <?php echo htmlspecialchars($farmer->location) ?>
                                        </p>
                                    </div>
                                    <div class="ms-auto">
                                        <button class="btn btn-outline-primary btn-sm">
                                            <i class="ri-mail-line me-1"></i> Contact
                                        </button>
                                        <?php if (!$farmer->is_verified): ?>
                                        <button class="btn btn-success btn-sm ms-2"
                                            onclick="verifyFarmer(<?php echo $farmerId ?>)">
                                            <i class="ri-check-line me-1"></i> Verify Farmer
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Total Earnings Card -->
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span class="avatar avatar-md bg-primary">
                                            <i class="ri-money-dollar-circle-line"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="mb-1">Total Earnings</p>
                                        <h5 class="mb-0">KES
                                            <?php echo number_format($financials->total_earnings ?? 0, 2) ?></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Outstanding Loans -->
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span class="avatar avatar-md bg-warning">
                                            <i class="ri-bank-line"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="mb-1">Outstanding Loans</p>
                                        <h5 class="mb-0">KES
                                            <?php echo number_format($financials->outstanding_loans ?? 0, 2) ?></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Active Farms -->
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span class="avatar avatar-md bg-success">
                                            <i class="ri-plant-line"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="mb-1">Active Farms</p>
                                        <h5 class="mb-0"><?php echo is_array($farms) ? count($farms) : 0 ?></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Success Rate -->
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span class="avatar avatar-md bg-info">
                                            <i class="ri-truck-line"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="mb-1">Delivery Success Rate</p>
                                        <h5 class="mb-0">
                                            <?php 
                                                $rate = $performance->acceptance_rate ?? 0;
                                                echo number_format($rate, 1) . '%';
                                                ?>
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Produce Deliveries Row with Complete Payment Data -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-header">
                                <div class="card-title">
                                    <i class="ri-truck-line me-2 text-primary"></i>Produce Deliveries
                                </div>
                            </div>
                            <div class="card-body">
                                <?php
                                   // Dedicated query to get complete produce delivery and payment information
                                   $deliveryQuery = "SELECT 
                                       pd.id, pd.quantity, pd.unit_price, pd.total_value, pd.quality_grade, 
                                       pd.delivery_date, pd.received_by, pd.status, pd.is_sold, pd.sale_date, 
                                       pd.notes, 
                                       pt.name as product_name, pt.measurement_unit,
                                       f.name as farm_name, f.location as farm_location,
                                       u.first_name as received_first_name, u.last_name as received_last_name,
                                       (SELECT SUM(icr.amount) FROM input_credit_repayments icr WHERE icr.produce_delivery_id = pd.id) as input_credit_deduction,
                                       (SELECT SUM(lr.amount) FROM loan_repayments lr WHERE lr.produce_delivery_id = pd.id) as loan_deduction,
                                       (SELECT COALESCE(c.comment, '') FROM comments c WHERE c.reference_type = 'produce_delivery' AND c.reference_id = pd.id AND c.comment_type_id = 7 ORDER BY c.created_at DESC LIMIT 1) as sale_comment,
                                       (CASE 
                                           WHEN pd.status = 'paid' THEN 
                                               (SELECT MAX(fat.created_at) FROM farmer_account_transactions fat WHERE fat.reference_id = pd.id)
                                           ELSE NULL
                                       END) as payment_date,
                                       (CASE 
                                           WHEN pd.status = 'sold' OR pd.status = 'paid' THEN 
                                               (pd.total_value * 0.9)
                                           ELSE pd.total_value
                                       END) as gross_payment,
                                       (CASE 
                                           WHEN pd.status = 'sold' OR pd.status = 'paid' THEN 
                                               (pd.total_value * 0.1)
                                           ELSE 0
                                       END) as commission,
                                       (SELECT COALESCE(fat.amount, 0) 
                                        FROM farmer_account_transactions fat 
                                        WHERE fat.reference_id = pd.id
                                        ORDER BY fat.created_at DESC
                                        LIMIT 1) as final_payment
                                   FROM produce_deliveries pd
                                   JOIN farm_products fp ON pd.farm_product_id = fp.id
                                   JOIN farms f ON fp.farm_id = f.id
                                   JOIN product_types pt ON fp.product_type_id = pt.id
                                   LEFT JOIN users u ON pd.received_by = u.id
                                   WHERE f.farmer_id = $farmerId
                                   ORDER BY pd.delivery_date DESC";
                                   
                                   $deliveries = $app->select_all($deliveryQuery);
                                   ?>

                                <?php if ($deliveries && count($deliveries) > 0): ?>
                                <div class="table-responsive">
                                    <table id="produceDeliveriesTable"
                                        class="table table-hover display responsive nowrap" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>Date</th>
                                                <th>Product</th>
                                                <th>Farm</th>
                                                <th>Quantity</th>
                                                <th>Value</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($deliveries as $delivery): ?>
                                            <tr data-delivery-id="<?php echo $delivery->id; ?>">
                                                <td class="details-control text-center">
                                                    <i class="ri-add-circle-line text-primary fs-5"
                                                        style="cursor: pointer;"></i>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($delivery->delivery_date)) ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($delivery->product_name) ?></td>
                                                <td><?php echo htmlspecialchars($delivery->farm_name) ?></td>
                                                <td><?php echo number_format($delivery->quantity, 2) . ' ' . $delivery->measurement_unit ?>
                                                </td>
                                                <td>KES <?php echo number_format($delivery->total_value, 2) ?></td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                            echo $delivery->status === 'accepted' ? 'success' : 
                                                ($delivery->status === 'pending' ? 'warning' : 
                                                ($delivery->status === 'sold' ? 'info' : 
                                                ($delivery->status === 'paid' ? 'primary' : 'danger'))); 
                                        ?>">
                                                        <?php echo ucfirst($delivery->status) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary view-details"
                                                        data-delivery-id="<?php echo $delivery->id; ?>">
                                                        <i class="ri-eye-line"></i> View
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Detail View Container -->
                                <div id="deliveryDetailContainer"></div>
                                <?php else: ?>
                                <div class="text-center p-5 border rounded">
                                    <i class="ri-truck-line fs-1 text-muted mb-3"></i>
                                    <h6>No Deliveries Found</h6>
                                    <p class="text-muted">No produce deliveries have been recorded for this farmer yet
                                    </p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hidden delivery detail card template - will be cloned and shown when row is expanded -->
                <div id="deliveryDetailTemplate" class="d-none">
                    <div class="card border shadow-none mb-3">
                        <div class="card-header bg-light-subtle d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="ri-truck-line me-2 text-primary"></i>
                                <span class="delivery-product"></span> Delivery Details
                            </h6>
                            <button type="button" class="btn-close close-details" aria-label="Close"></button>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Delivery Information -->
                                <div class="col-md-6 mb-3">
                                    <h6 class="fw-semibold mb-2">Delivery Information</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td class="text-muted" width="35%">Delivery ID</td>
                                                <td><span class="delivery-id fw-medium"></span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Date</td>
                                                <td><span class="delivery-date fw-medium"></span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Farm</td>
                                                <td><span class="delivery-farm fw-medium"></span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Product</td>
                                                <td><span class="delivery-product fw-medium"></span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Quantity</td>
                                                <td><span class="delivery-quantity fw-medium"></span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Quality Grade</td>
                                                <td><span class="delivery-grade badge"></span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Status</td>
                                                <td><span class="delivery-status badge"></span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Received By</td>
                                                <td><span class="delivery-received-by fw-medium"></span></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <!-- Payment Information -->
                                <div class="col-md-6 mb-3">
                                    <h6 class="fw-semibold mb-2">Payment Information</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td class="text-muted" width="35%">Total Value</td>
                                                <td><span class="payment-total-value fw-medium"></span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Commission</td>
                                                <td><span class="payment-commission fw-medium"></span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Gross Payment</td>
                                                <td><span class="payment-gross fw-medium"></span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Input Credit Deduction</td>
                                                <td><span class="payment-input-credit fw-medium"></span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Loan Deduction</td>
                                                <td><span class="payment-loan-deduction fw-medium"></span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Final Payment</td>
                                                <td><span class="payment-final fw-semibold text-success"></span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Payment Date</td>
                                                <td><span class="payment-date fw-medium"></span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Payment Status</td>
                                                <td><span class="payment-status badge"></span></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Notes Section -->
                            <div class="row mt-2">
                                <div class="col-12">
                                    <h6 class="fw-semibold mb-2">Notes</h6>
                                    <div class="border rounded p-3 bg-light-subtle">
                                        <p class="delivery-notes mb-0"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="row mt-3">
                                <div class="col-12 text-end">
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="ri-file-download-line me-1"></i> Download Receipt
                                    </button>
                                    <button class="btn btn-sm btn-outline-info ms-2">
                                        <i class="ri-history-line me-1"></i> View History
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- JavaScript to handle row expansion and detail card population -->
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Initialize DataTable
                    var table = $('#produceDeliveriesTable').DataTable({
                        responsive: true,
                        ordering: true,
                        paging: true,
                        searching: true,
                        info: true,
                        lengthChange: true,
                        lengthMenu: [
                            [10, 25, 50, -1],
                            [10, 25, 50, "All"]
                        ],
                        columnDefs: [{
                                orderable: false,
                                targets: [0, 7]
                            },
                            {
                                className: "dt-center",
                                targets: [0, 6, 7]
                            }
                        ]
                    });

                    // Store delivery data for quick access
                    var deliveriesData = <?php echo json_encode($deliveries ?? []); ?>;
                    var deliveriesById = {};

                    // Create a lookup object
                    deliveriesData.forEach(function(delivery) {
                        deliveriesById[delivery.id] = delivery;
                    });

                    // Handle expand/collapse
                    $('#produceDeliveriesTable tbody').on('click', 'td.details-control, button.view-details',
                        function() {
                            var tr = $(this).closest('tr');
                            var row = table.row(tr);
                            var isControlCell = $(this).hasClass('details-control');
                            var deliveryId = tr.data('delivery-id');
                            var toggleIcon = tr.find('td.details-control i');

                            // If we're already showing this row, close it
                            if (row.child.isShown() && isControlCell) {
                                row.child.hide();
                                tr.removeClass('shown');
                                toggleIcon.removeClass('ri-subtract-circle-line').addClass(
                                    'ri-add-circle-line');
                                return;
                            }

                            // Get the delivery data
                            var deliveryData = deliveriesById[deliveryId];
                            if (!deliveryData) {
                                console.error('Delivery data not found for ID:', deliveryId);
                                return;
                            }

                            // Clone the template
                            var detailContent = $('#deliveryDetailTemplate').children().clone();

                            // Fill in the delivery details
                            detailContent.find('.delivery-id').text(deliveryData.id);
                            detailContent.find('.delivery-product').text(deliveryData.product_name);
                            detailContent.find('.delivery-date').text(formatDate(deliveryData
                                .delivery_date));
                            detailContent.find('.delivery-farm').text(deliveryData.farm_name + ' (' +
                                deliveryData.farm_location + ')');
                            detailContent.find('.delivery-quantity').text(
                                number_format(deliveryData.quantity, 2) + ' ' + deliveryData
                                .measurement_unit
                            );

                            // Format quality grade
                            var gradeClass = 'bg-success';
                            if (deliveryData.quality_grade === 'B') gradeClass = 'bg-warning';
                            if (deliveryData.quality_grade === 'C') gradeClass = 'bg-danger';

                            detailContent.find('.delivery-grade')
                                .addClass(gradeClass)
                                .text('Grade ' + deliveryData.quality_grade);

                            // Format status
                            var statusClass = 'bg-warning';
                            if (deliveryData.status === 'accepted') statusClass = 'bg-success';
                            if (deliveryData.status === 'rejected') statusClass = 'bg-danger';
                            if (deliveryData.status === 'sold') statusClass = 'bg-info';
                            if (deliveryData.status === 'paid') statusClass = 'bg-primary';

                            detailContent.find('.delivery-status')
                                .addClass(statusClass)
                                .text(capitalizeFirstLetter(deliveryData.status));

                            // Received by
                            if (deliveryData.received_first_name) {
                                detailContent.find('.delivery-received-by').text(
                                    deliveryData.received_first_name + ' ' + deliveryData
                                    .received_last_name
                                );
                            } else {
                                detailContent.find('.delivery-received-by').text('Not recorded');
                            }

                            // Payment information
                            detailContent.find('.payment-total-value').text('KES ' + number_format(
                                deliveryData.total_value, 2));
                            detailContent.find('.payment-commission').text('KES ' + number_format(
                                deliveryData.commission || 0, 2));
                            detailContent.find('.payment-gross').text('KES ' + number_format(deliveryData
                                .gross_payment || 0, 2));

                            // Deductions
                            detailContent.find('.payment-input-credit').text('KES ' + number_format(
                                deliveryData.input_credit_deduction || 0, 2));
                            detailContent.find('.payment-loan-deduction').text('KES ' + number_format(
                                deliveryData.loan_deduction || 0, 2));

                            // Final payment
                            detailContent.find('.payment-final').text('KES ' + number_format(deliveryData
                                .final_payment || 0, 2));

                            // Payment date
                            if (deliveryData.payment_date) {
                                detailContent.find('.payment-date').text(formatDate(deliveryData
                                    .payment_date));
                            } else {
                                detailContent.find('.payment-date').text('Not paid yet');
                            }

                            // Payment status
                            var paymentStatusClass = 'bg-warning';
                            var paymentStatusText = 'Pending';

                            if (deliveryData.status === 'sold') {
                                paymentStatusClass = 'bg-info';
                                paymentStatusText = 'Processed';
                            }

                            if (deliveryData.status === 'paid') {
                                paymentStatusClass = 'bg-success';
                                paymentStatusText = 'Paid';
                            }

                            detailContent.find('.payment-status')
                                .addClass(paymentStatusClass)
                                .text(paymentStatusText);

                            // Notes
                            if (deliveryData.notes || deliveryData.sale_comment) {
                                detailContent.find('.delivery-notes').text(deliveryData.sale_comment ||
                                    deliveryData.notes);
                            } else {
                                detailContent.find('.delivery-notes').text('No notes available');
                            }

                            // Handle close button
                            detailContent.find('.close-details').on('click', function() {
                                row.child.hide();
                                tr.removeClass('shown');
                                toggleIcon.removeClass('ri-subtract-circle-line').addClass(
                                    'ri-add-circle-line');
                            });

                            // Show the child row
                            row.child(detailContent).show();
                            tr.addClass('shown');
                            toggleIcon.removeClass('ri-add-circle-line').addClass(
                                'ri-subtract-circle-line');
                        });

                    // Helper functions
                    function formatDate(dateString) {
                        var date = new Date(dateString);
                        return date.toLocaleDateString('en-US', {
                            month: 'short',
                            day: 'numeric',
                            year: 'numeric'
                        });
                    }

                    function number_format(number, decimals) {
                        return parseFloat(number).toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    }

                    function capitalizeFirstLetter(string) {
                        return string.charAt(0).toUpperCase() + string.slice(1);
                    }
                });
                </script>
                <!-- Farms Section with Rich Bootstrap Styling -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card custom-card overflow-hidden">
                            <div
                                class="card-header d-flex justify-content-between align-items-center bg-light-subtle border-bottom-0">
                                <div class="card-title mb-0">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm bg-success-transparent rounded me-2">
                                            <i class="ri-plant-line fs-16 text-success"></i>
                                        </span>
                                        <h5 class="mb-0">Registered Farms</h5>
                                    </div>
                                </div>
                                <div>
                                    <button class="btn btn-sm btn-primary">
                                        <i class="ri-add-line me-1"></i> Add Farm
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php
                                     // Dedicated query to get complete farm information
                                     $farmsQuery = "SELECT 
                                         f.id, f.name, f.location, f.size, f.size_unit, f.is_active, f.created_at,
                                         ft.name as farm_type_name,
                                         GROUP_CONCAT(DISTINCT frt.name ORDER BY frt.name SEPARATOR ', ') as fruit_types,
                                         COUNT(DISTINCT ffm.id) as crop_count,
                                         GROUP_CONCAT(DISTINCT ct.name ORDER BY ct.name SEPARATOR ', ') as cultivation_methods,
                                         COALESCE(AVG(ffm.expected_production), 0) as avg_expected_production,
                                         COALESCE(SUM(ffm.acreage), 0) as total_cultivated_area
                                     FROM farms f
                                     JOIN farm_types ft ON f.farm_type_id = ft.id
                                     LEFT JOIN farm_fruit_mapping ffm ON f.id = ffm.farm_id
                                     LEFT JOIN fruit_types frt ON ffm.fruit_type_id = frt.id
                                     LEFT JOIN cultivation_types ct ON ffm.cultivation_type_id = ct.id
                                     WHERE f.farmer_id = $farmerId
                                     GROUP BY f.id
                                     ORDER BY f.created_at DESC";
                                     
                                     $farms = $app->select_all($farmsQuery);
                                     ?>

                                <?php if ($farms && count($farms) > 0): ?>
                                <div class="table-responsive">
                                    <table id="farmsTable" class="table table-hover border-0 text-nowrap mb-0">
                                        <thead>
                                            <tr class="bg-light">
                                                <th class="border-top-0">
                                                    <i class="ri-building-line me-1 text-muted"></i> Farm Name
                                                </th>
                                                <th class="border-top-0">
                                                    <i class="ri-map-pin-line me-1 text-muted"></i> Location
                                                </th>
                                                <th class="border-top-0">
                                                    <i class="ri-ruler-2-line me-1 text-muted"></i> Size
                                                </th>
                                                <th class="border-top-0">
                                                    <i class="ri-plant-line me-1 text-muted"></i> Farm Type
                                                </th>
                                                <th class="border-top-0">
                                                    <i class="ri-leaf-line me-1 text-muted"></i> Crops/Fruits
                                                </th>
                                                <th class="border-top-0 text-center">
                                                    <i class="ri-toggle-line me-1 text-muted"></i> Status
                                                </th>
                                                <th class="border-top-0 text-center">
                                                    <i class="ri-settings-line me-1 text-muted"></i> Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $rowClass = ""; ?>
                                            <?php foreach ($farms as $index => $farm): ?>
                                            <?php 
                                                  // Alternate row colors for better readability
                                                  $rowClass = $index % 2 == 0 ? "bg-white" : "bg-light-subtle";
                                              ?>
                                            <tr class="<?php echo $rowClass; ?>">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php 
                                            // Farm icon based on type
                                            $farmIcon = 'ri-home-4-line';
                                            $iconColor = 'primary';
                                            
                                            if (stripos($farm->farm_type_name, 'fruit') !== false) {
                                                $farmIcon = 'ri-apple-line';
                                                $iconColor = 'success';
                                            } elseif (stripos($farm->farm_type_name, 'vegetable') !== false) {
                                                $farmIcon = 'ri-plant-line';
                                                $iconColor = 'info';
                                            }
                                        ?>
                                                        <span
                                                            class="avatar avatar-xs bg-<?php echo $iconColor; ?>-transparent me-2">
                                                            <i class="<?php echo $farmIcon; ?>"></i>
                                                        </span>
                                                        <span
                                                            class="fw-medium"><?php echo htmlspecialchars($farm->name) ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span class="avatar avatar-xs bg-info-transparent me-2">
                                                            <i class="ri-map-pin-line"></i>
                                                        </span>
                                                        <?php echo htmlspecialchars($farm->location) ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span
                                                            class="fw-medium"><?php echo number_format($farm->size, 2) ?></span>
                                                        <span
                                                            class="ms-1 text-muted"><?php echo ucfirst($farm->size_unit) ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-<?php echo $iconColor; ?>-transparent text-<?php echo $iconColor; ?>">
                                                        <?php echo htmlspecialchars($farm->farm_type_name) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($farm->fruit_types): ?>
                                                    <?php 
                                            $fruit_types = explode(', ', $farm->fruit_types);
                                            foreach (array_slice($fruit_types, 0, 2) as $fruit): 
                                        ?>
                                                    <span class="badge bg-light-subtle text-dark me-1">
                                                        <?php echo htmlspecialchars($fruit) ?>
                                                    </span>
                                                    <?php endforeach; ?>

                                                    <?php if (count($fruit_types) > 2): ?>
                                                    <span class="badge bg-secondary-transparent text-secondary">
                                                        +<?php echo count($fruit_types) - 2 ?> more
                                                    </span>
                                                    <?php endif; ?>
                                                    <?php else: ?>
                                                    <span class="text-muted">No crops recorded</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($farm->is_active): ?>
                                                    <span class="badge bg-success-transparent">
                                                        <i class="ri-check-line me-1"></i> Active
                                                    </span>
                                                    <?php else: ?>
                                                    <span class="badge bg-danger-transparent">
                                                        <i class="ri-close-line me-1"></i> Inactive
                                                    </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center">
                                                        <button class="btn btn-sm btn-icon btn-primary-transparent me-1"
                                                            data-bs-toggle="tooltip" title="Edit Farm">
                                                            <i class="ri-edit-line"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-icon btn-info-transparent me-1"
                                                            data-bs-toggle="tooltip" title="View Details">
                                                            <i class="ri-file-list-line"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-icon btn-danger-transparent"
                                                            data-bs-toggle="tooltip" title="Delete Farm">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Farm Statistics Cards -->
                                <div class="row mt-4">
                                    <div class="col-md-3">
                                        <div class="card border bg-light-subtle h-100">
                                            <div class="card-body text-center">
                                                <div class="avatar avatar-md bg-success-transparent mx-auto mb-3">
                                                    <i class="ri-earth-line text-success"></i>
                                                </div>
                                                <h3 class="mb-1"><?php echo count($farms) ?></h3>
                                                <p class="text-muted mb-0">Total Farms</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card border bg-light-subtle h-100">
                                            <div class="card-body text-center">
                                                <div class="avatar avatar-md bg-primary-transparent mx-auto mb-3">
                                                    <i class="ri-layout-grid-line text-primary"></i>
                                                </div>
                                                <?php 
                                                      $totalArea = 0;
                                                      foreach ($farms as $farm) {
                                                          $totalArea += $farm->size;
                                                      }
                                                  ?>
                                                <h3 class="mb-1"><?php echo number_format($totalArea, 2) ?></h3>
                                                <p class="text-muted mb-0">Total Area
                                                    (<?php echo ucfirst($farms[0]->size_unit) ?>)</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card border bg-light-subtle h-100">
                                            <div class="card-body text-center">
                                                <div class="avatar avatar-md bg-warning-transparent mx-auto mb-3">
                                                    <i class="ri-plant-line text-warning"></i>
                                                </div>
                                                <?php 
                                                        $cropCount = 0;
                                                        $uniqueCrops = [];
                                                        foreach ($farms as $farm) {
                                                            if ($farm->fruit_types) {
                                                                $types = explode(', ', $farm->fruit_types);
                                                                foreach ($types as $type) {
                                                                    $uniqueCrops[$type] = true;
                                                                }
                                                            }
                                                        }
                                                        $cropCount = count($uniqueCrops);
                                                    ?>
                                                <h3 class="mb-1"><?php echo $cropCount ?></h3>
                                                <p class="text-muted mb-0">Different Crops</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card border bg-light-subtle h-100">
                                            <div class="card-body text-center">
                                                <div class="avatar avatar-md bg-info-transparent mx-auto mb-3">
                                                    <i class="ri-seedling-line text-info"></i>
                                                </div>
                                                <?php 
                                                              $activeFarms = 0;
                                                              foreach ($farms as $farm) {
                                                                  if ($farm->is_active) {
                                                                      $activeFarms++;
                                                                  }
                                                              }
                                                              $activePercentage = count($farms) > 0 ? round(($activeFarms / count($farms)) * 100) : 0;
                                                          ?>
                                                <h3 class="mb-1"><?php echo $activePercentage ?>%</h3>
                                                <p class="text-muted mb-0">Active Farms</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php else: ?>
                                <div class="text-center p-5">
                                    <div class="avatar avatar-lg bg-light-subtle mx-auto mb-3">
                                        <i class="ri-farm-line fs-2 text-muted"></i>
                                    </div>
                                    <h6>No Farms Found</h6>
                                    <p class="text-muted">No farms have been registered for this farmer yet</p>
                                    <button class="btn btn-sm btn-primary">
                                        <i class="ri-add-line me-1"></i> Register Farm
                                    </button>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Initialize tooltips
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll(
                        '[data-bs-toggle="tooltip"]'));
                    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });

                    // Initialize DataTable if available
                    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.DataTable !== 'undefined') {
                        $('#farmsTable').DataTable({
                            responsive: true,
                            ordering: true,
                            paging: true,
                            searching: true,
                            info: true,
                            lengthChange: true,
                            lengthMenu: [
                                [10, 25, 50, -1],
                                [10, 25, 50, "All"]
                            ],
                            columnDefs: [{
                                    orderable: false,
                                    targets: [6]
                                },
                                {
                                    className: "dt-center",
                                    targets: [5, 6]
                                }
                            ]
                        });
                    }
                });
                </script>
                <!-- Loans Section with Modern Styling -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card custom-card overflow-hidden">
                            <div
                                class="card-header d-flex justify-content-between align-items-center bg-light-subtle border-bottom-0">
                                <div class="card-title mb-0">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm bg-primary-transparent rounded me-2">
                                            <i class="ri-bank-line fs-16 text-primary"></i>
                                        </span>
                                        <h5 class="mb-0">Active Loans</h5>
                                    </div>
                                </div>
                                <div>
                                    <button class="btn btn-sm btn-primary">
                                        <i class="ri-add-line me-1"></i> Apply for Loan
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php
                                 // Dedicated query to get complete loan information
                                 $loansQuery = "SELECT 
                                     la.id, la.amount_requested, la.term_requested, la.purpose, 
                                     la.application_date, la.provider_type, la.status,
                                     al.approved_amount, al.interest_rate, al.total_repayment_amount, 
                                     al.remaining_balance, al.disbursement_date, al.expected_completion_date,
                                     lt.name as loan_type_name,
                                     b.name as bank_name,
                                     (al.total_repayment_amount - al.remaining_balance) / al.total_repayment_amount * 100 as repayment_percentage
                                 FROM loan_applications la
                                 LEFT JOIN approved_loans al ON la.id = al.loan_application_id
                                 LEFT JOIN loan_types lt ON la.loan_type_id = lt.id
                                 LEFT JOIN banks b ON la.bank_id = b.id
                                 WHERE la.farmer_id = $farmerId AND (la.status IN ('approved', 'disbursed') OR al.status IN ('active', 'pending_disbursement'))
                                 ORDER BY la.application_date DESC";
                                 
                                 $loans = $app->select_all($loansQuery);
                                 ?>

                                <?php if ($loans && count($loans) > 0): ?>
                                <div class="table-responsive">
                                    <table id="loansTable" class="table table-hover border-0 text-nowrap mb-0">
                                        <thead>
                                            <tr class="bg-light">
                                                <th class="border-top-0">
                                                    <i class="ri-hashtag me-1 text-muted"></i> Reference
                                                </th>
                                                <th class="border-top-0">
                                                    <i class="ri-bank-line me-1 text-muted"></i> Source
                                                </th>
                                                <th class="border-top-0 text-end">
                                                    <i class="ri-money-dollar-circle-line me-1 text-muted"></i> Amount
                                                </th>
                                                <th class="border-top-0">
                                                    <i class="ri-percent-line me-1 text-muted"></i> Interest
                                                </th>
                                                <th class="border-top-0">
                                                    <i class="ri-calendar-line me-1 text-muted"></i> Term
                                                </th>
                                                <th class="border-top-0">
                                                    <i class="ri-calendar-check-line me-1 text-muted"></i> Disbursed
                                                </th>
                                                <th class="border-top-0 text-end">
                                                    <i class="ri-refund-2-line me-1 text-muted"></i> Balance
                                                </th>
                                                <th class="border-top-0">
                                                    <i class="ri-bar-chart-line me-1 text-muted"></i> Repayment
                                                </th>
                                                <th class="border-top-0 text-center">
                                                    <i class="ri-flag-line me-1 text-muted"></i> Status
                                                </th>
                                                <th class="border-top-0 text-center">
                                                    <i class="ri-settings-line me-1 text-muted"></i> Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $rowClass = ""; ?>
                                            <?php foreach ($loans as $index => $loan): ?>
                                            <?php 
                                                    // Alternate row colors for better readability
                                                    $rowClass = $index % 2 == 0 ? "bg-white" : "bg-light-subtle";
                                                ?>
                                            <tr class="<?php echo $rowClass; ?>">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span class="avatar avatar-xs bg-primary-transparent me-2">
                                                            <i class="ri-file-list-3-line"></i>
                                                        </span>
                                                        <span
                                                            class="fw-medium">LOAN<?php echo str_pad($loan->id, 5, '0', STR_PAD_LEFT); ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php if ($loan->provider_type == 'sacco'): ?>
                                                        <span class="avatar avatar-xs bg-success-transparent me-2">
                                                            <i class="ri-community-line"></i>
                                                        </span>
                                                        <span>SACCO</span>
                                                        <?php else: ?>
                                                        <span class="avatar avatar-xs bg-info-transparent me-2">
                                                            <i class="ri-bank-line"></i>
                                                        </span>
                                                        <span><?php echo htmlspecialchars($loan->bank_name ?? 'Bank'); ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <span class="fw-semibold text-dark">
                                                        KES
                                                        <?php echo number_format($loan->approved_amount ?? $loan->amount_requested, 2); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary-transparent">
                                                        <?php echo number_format($loan->interest_rate, 1); ?>%
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php echo $loan->term_requested; ?> months
                                                </td>
                                                <td>
                                                    <?php if ($loan->disbursement_date): ?>
                                                    <?php echo date('M d, Y', strtotime($loan->disbursement_date)); ?>
                                                    <?php else: ?>
                                                    <span class="badge bg-warning-transparent">Pending</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-end">
                                                    <span class="fw-semibold">
                                                        KES
                                                        <?php echo number_format($loan->remaining_balance ?? $loan->approved_amount ?? $loan->amount_requested, 2); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php 
                                                        $percentage = $loan->repayment_percentage ?? 0;
                                                        $progressColor = 'danger';
                                                        
                                                        if ($percentage >= 75) {
                                                            $progressColor = 'success';
                                                        } elseif ($percentage >= 50) {
                                                            $progressColor = 'info';
                                                        } elseif ($percentage >= 25) {
                                                            $progressColor = 'warning';
                                                        }
                                                    ?>
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress flex-grow-1"
                                                            style="height: 6px; width: 80px;">
                                                            <div class="progress-bar bg-<?php echo $progressColor; ?>"
                                                                style="width: <?php echo $percentage; ?>%"
                                                                aria-valuenow="<?php echo $percentage; ?>"
                                                                aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <span
                                                            class="ms-2 fs-12"><?php echo round($percentage); ?>%</span>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <?php
                                                      $statusClass = 'warning';
                                                      $statusIcon = 'ri-time-line';
                                                      $statusText = 'Pending';
                                                      
                                                      if ($loan->status == 'disbursed' || $loan->status == 'active') {
                                                          $statusClass = 'success';
                                                          $statusIcon = 'ri-check-double-line';
                                                          $statusText = 'Active';
                                                      } elseif ($loan->status == 'completed') {
                                                          $statusClass = 'info';
                                                          $statusIcon = 'ri-check-line';
                                                          $statusText = 'Completed';
                                                      } elseif ($loan->status == 'defaulted') {
                                                          $statusClass = 'danger';
                                                          $statusIcon = 'ri-error-warning-line';
                                                          $statusText = 'Defaulted';
                                                      } elseif ($loan->status == 'approved') {
                                                          $statusClass = 'primary';
                                                          $statusIcon = 'ri-checkbox-circle-line';
                                                          $statusText = 'Approved';
                                                      }
                                                  ?>
                                                    <span class="badge bg-<?php echo $statusClass; ?>-transparent">
                                                        <i class="<?php echo $statusIcon; ?> me-1"></i>
                                                        <?php echo $statusText; ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center">
                                                        <button class="btn btn-sm btn-icon btn-primary-transparent me-1"
                                                            data-bs-toggle="tooltip" title="View Details">
                                                            <i class="ri-eye-line"></i>
                                                        </button>
                                                        <?php if ($loan->status == 'disbursed' || $loan->status == 'active'): ?>
                                                        <button class="btn btn-sm btn-icon btn-success-transparent"
                                                            data-bs-toggle="tooltip" title="View Repayments">
                                                            <i class="ri-exchange-funds-line"></i>
                                                        </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Loan Summary Cards -->
                                <div class="row mt-4">
                                    <div class="col-md-3">
                                        <div class="card border bg-light-subtle h-100">
                                            <div class="card-body text-center">
                                                <div class="avatar avatar-md bg-primary-transparent mx-auto mb-3">
                                                    <i class="ri-bank-line text-primary"></i>
                                                </div>
                                                <h3 class="mb-1"><?php echo count($loans); ?></h3>
                                                <p class="text-muted mb-0">Active Loans</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card border bg-light-subtle h-100">
                                            <div class="card-body text-center">
                                                <div class="avatar avatar-md bg-success-transparent mx-auto mb-3">
                                                    <i class="ri-money-dollar-circle-line text-success"></i>
                                                </div>
                                                <?php 
                                                      $totalLoans = 0;
                                                      foreach ($loans as $loan) {
                                                          $totalLoans += $loan->approved_amount ?? $loan->amount_requested;
                                                      }
                                                  ?>
                                                <h3 class="mb-1">KES <?php echo number_format($totalLoans, 0); ?></h3>
                                                <p class="text-muted mb-0">Total Borrowed</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card border bg-light-subtle h-100">
                                            <div class="card-body text-center">
                                                <div class="avatar avatar-md bg-danger-transparent mx-auto mb-3">
                                                    <i class="ri-refund-2-line text-danger"></i>
                                                </div>
                                                <?php 
                                                         $totalBalance = 0;
                                                         foreach ($loans as $loan) {
                                                             $totalBalance += $loan->remaining_balance ?? $loan->approved_amount ?? $loan->amount_requested;
                                                         }
                                                     ?>
                                                <h3 class="mb-1">KES <?php echo number_format($totalBalance, 0); ?></h3>
                                                <p class="text-muted mb-0">Outstanding Balance</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card border bg-light-subtle h-100">
                                            <div class="card-body text-center">
                                                <div class="avatar avatar-md bg-info-transparent mx-auto mb-3">
                                                    <i class="ri-percent-line text-info"></i>
                                                </div>
                                                <?php 
                                                    $avgInterest = 0;
                                                    $count = 0;
                                                    foreach ($loans as $loan) {
                                                        if ($loan->interest_rate) {
                                                            $avgInterest += $loan->interest_rate;
                                                            $count++;
                                                        }
                                                    }
                                                    $avgInterest = $count > 0 ? $avgInterest / $count : 0;
                                                ?>
                                                <h3 class="mb-1"><?php echo number_format($avgInterest, 1); ?>%</h3>
                                                <p class="text-muted mb-0">Average Interest Rate</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php else: ?>
                                <div class="text-center p-5">
                                    <div class="avatar avatar-lg bg-light-subtle mx-auto mb-3">
                                        <i class="ri-bank-line fs-2 text-muted"></i>
                                    </div>
                                    <h6>No Active Loans</h6>
                                    <p class="text-muted">This farmer doesn't have any active loans at the moment</p>
                                    <button class="btn btn-sm btn-primary">
                                        <i class="ri-add-line me-1"></i> Apply for Loan
                                    </button>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Initialize tooltips
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll(
                        '[data-bs-toggle="tooltip"]'));
                    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });

                    // Initialize DataTable if available
                    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.DataTable !== 'undefined') {
                        $('#loansTable').DataTable({
                            responsive: true,
                            ordering: true,
                            paging: true,
                            searching: true,
                            info: true,
                            lengthChange: true,
                            lengthMenu: [
                                [10, 25, 50, -1],
                                [10, 25, 50, "All"]
                            ],
                            columnDefs: [{
                                    orderable: false,
                                    targets: [9]
                                },
                                {
                                    className: "dt-center",
                                    targets: [8, 9]
                                },
                                {
                                    className: "dt-right",
                                    targets: [2, 6]
                                }
                            ]
                        });
                    }
                });
                </script>
                <!-- Input Credits Section with Modern Styling -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card custom-card overflow-hidden">
                            <div
                                class="card-header d-flex justify-content-between align-items-center bg-light-subtle border-bottom-0">
                                <div class="card-title mb-0">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm bg-success-transparent rounded me-2">
                                            <i class="ri-shopping-cart-line fs-16 text-success"></i>
                                        </span>
                                        <h5 class="mb-0">Input Credits</h5>
                                    </div>
                                </div>
                                <div>
                                    <button class="btn btn-sm btn-primary">
                                        <i class="ri-add-line me-1"></i> Apply for Input Credit
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php
                                   // Dedicated query to get complete input credit information
                                   $inputCreditsQuery = "SELECT 
                                       ica.id, ica.agrovet_id, ica.total_amount, ica.credit_percentage, 
                                       ica.repayment_percentage, ica.application_date, ica.status,
                                       aic.approved_amount, aic.total_with_interest, aic.remaining_balance, 
                                       aic.fulfillment_date, aic.status as credit_status,
                                       a.name as agrovet_name,
                                       (
                                           SELECT GROUP_CONCAT(CONCAT(ici.quantity, ' ', ici.unit, ' ', ici.input_name) SEPARATOR ', ')
                                           FROM input_credit_items ici 
                                           WHERE ici.credit_application_id = ica.id
                                       ) as items_list
                                   FROM input_credit_applications ica
                                   LEFT JOIN approved_input_credits aic ON ica.id = aic.credit_application_id
                                   LEFT JOIN agrovets a ON ica.agrovet_id = a.id
                                   WHERE ica.farmer_id = $farmerId
                                   ORDER BY ica.application_date DESC";
                                   
                                   $inputCredits = $app->select_all($inputCreditsQuery);
                                   ?>

                                <?php if ($inputCredits && count($inputCredits) > 0): ?>
                                <div class="table-responsive">
                                    <table id="inputCreditsTable" class="table table-hover border-0 text-nowrap mb-0">
                                        <thead>
                                            <tr class="bg-light">
                                                <th class="border-top-0">
                                                    <i class="ri-hashtag me-1 text-muted"></i> Reference
                                                </th>
                                                <th class="border-top-0">
                                                    <i class="ri-store-2-line me-1 text-muted"></i> Agrovet
                                                </th>
                                                <th class="border-top-0">
                                                    <i class="ri-calendar-line me-1 text-muted"></i> Date Requested
                                                </th>
                                                <th class="border-top-0">
                                                    <i class="ri-list-check-2 me-1 text-muted"></i> Items
                                                </th>
                                                <th class="border-top-0 text-end">
                                                    <i class="ri-money-dollar-circle-line me-1 text-muted"></i> Amount
                                                </th>
                                                <th class="border-top-0">
                                                    <i class="ri-percent-line me-1 text-muted"></i> Interest
                                                </th>
                                                <th class="border-top-0">
                                                    <i class="ri-calendar-check-line me-1 text-muted"></i> Fulfilled
                                                </th>
                                                <th class="border-top-0 text-end">
                                                    <i class="ri-refund-2-line me-1 text-muted"></i> Balance
                                                </th>
                                                <th class="border-top-0 text-center">
                                                    <i class="ri-flag-line me-1 text-muted"></i> Status
                                                </th>
                                                <th class="border-top-0 text-center">
                                                    <i class="ri-settings-line me-1 text-muted"></i> Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $rowClass = ""; ?>
                                            <?php foreach ($inputCredits as $index => $credit): ?>
                                            <?php 
                                                   // Alternate row colors for better readability
                                                   $rowClass = $index % 2 == 0 ? "bg-white" : "bg-light-subtle";
                                               ?>
                                            <tr class="<?php echo $rowClass; ?>">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span class="avatar avatar-xs bg-success-transparent me-2">
                                                            <i class="ri-file-list-3-line"></i>
                                                        </span>
                                                        <span
                                                            class="fw-medium">INPCR<?php echo str_pad($credit->id, 5, '0', STR_PAD_LEFT); ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span class="avatar avatar-xs bg-info-transparent me-2">
                                                            <i class="ri-store-2-line"></i>
                                                        </span>
                                                        <span><?php echo htmlspecialchars($credit->agrovet_name); ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php echo date('M d, Y', strtotime($credit->application_date)); ?>
                                                </td>
                                                <td>
                                                    <?php 
                                        $items = explode(', ', $credit->items_list);
                                        $displayItems = count($items) > 1 
                                            ? htmlspecialchars($items[0] . ' +' . (count($items) - 1) . ' more')
                                            : htmlspecialchars($credit->items_list);
                                    ?>
                                                    <span data-bs-toggle="tooltip"
                                                        title="<?php echo htmlspecialchars($credit->items_list); ?>">
                                                        <?php echo $displayItems; ?>
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <span class="fw-semibold text-dark">
                                                        KES
                                                        <?php echo number_format($credit->approved_amount ?? $credit->total_amount, 2); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary-transparent">
                                                        <?php echo number_format($credit->credit_percentage, 1); ?>%
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($credit->fulfillment_date): ?>
                                                    <?php echo date('M d, Y', strtotime($credit->fulfillment_date)); ?>
                                                    <?php else: ?>
                                                    <span class="badge bg-warning-transparent">Pending</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-end">
                                                    <span class="fw-semibold">
                                                        KES
                                                        <?php echo number_format($credit->remaining_balance ?? $credit->total_with_interest ?? ($credit->total_amount * (1 + $credit->credit_percentage / 100)), 2); ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <?php
                                                     $statusClass = 'warning';
                                                     $statusIcon = 'ri-time-line';
                                                     $statusText = 'Pending';
                                                     
                                                     if ($credit->status == 'fulfilled' || $credit->status == 'approved' || $credit->credit_status == 'active') {
                                                         $statusClass = 'success';
                                                         $statusIcon = 'ri-check-double-line';
                                                         $statusText = 'Active';
                                                     } elseif ($credit->status == 'rejected') {
                                                         $statusClass = 'danger';
                                                         $statusIcon = 'ri-close-line';
                                                         $statusText = 'Rejected';
                                                     } elseif ($credit->credit_status == 'completed') {
                                                         $statusClass = 'info';
                                                         $statusIcon = 'ri-check-line';
                                                         $statusText = 'Completed';
                                                     } elseif ($credit->status == 'under_review') {
                                                         $statusClass = 'primary';
                                                         $statusIcon = 'ri-file-search-line';
                                                         $statusText = 'Under Review';
                                                     }
                                                 ?>
                                                    <span class="badge bg-<?php echo $statusClass; ?>-transparent">
                                                        <i class="<?php echo $statusIcon; ?> me-1"></i>
                                                        <?php echo $statusText; ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center">
                                                        <button class="btn btn-sm btn-icon btn-primary-transparent me-1"
                                                            data-bs-toggle="tooltip" title="View Details">
                                                            <i class="ri-eye-line"></i>
                                                        </button>
                                                        <?php if ($credit->status == 'fulfilled' || $credit->credit_status == 'active'): ?>
                                                        <button class="btn btn-sm btn-icon btn-success-transparent"
                                                            data-bs-toggle="tooltip" title="View Repayments">
                                                            <i class="ri-exchange-funds-line"></i>
                                                        </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Input Credit Summary Cards -->
                                <div class="row mt-4">
                                    <div class="col-md-3">
                                        <div class="card border bg-light-subtle h-100">
                                            <div class="card-body text-center">
                                                <div class="avatar avatar-md bg-success-transparent mx-auto mb-3">
                                                    <i class="ri-shopping-cart-line text-success"></i>
                                                </div>
                                                <?php
                                                        $activeCount = 0;
                                                        foreach ($inputCredits as $credit) {
                                                            if ($credit->status == 'fulfilled' || $credit->status == 'approved' || 
                                                                $credit->credit_status == 'active') {
                                                                $activeCount++;
                                                            }
                                                        }
                                                    ?>
                                                <h3 class="mb-1"><?php echo $activeCount; ?></h3>
                                                <p class="text-muted mb-0">Active Credits</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card border bg-light-subtle h-100">
                                            <div class="card-body text-center">
                                                <div class="avatar avatar-md bg-primary-transparent mx-auto mb-3">
                                                    <i class="ri-money-dollar-circle-line text-primary"></i>
                                                </div>
                                                <?php 
                                                        $totalCredits = 0;
                                                        foreach ($inputCredits as $credit) {
                                                            if ($credit->status == 'fulfilled' || $credit->status == 'approved' || 
                                                                $credit->credit_status == 'active' || $credit->credit_status == 'completed') {
                                                                $totalCredits += $credit->approved_amount ?? $credit->total_amount;
                                                            }
                                                        }
                                                    ?>
                                                <h3 class="mb-1">KES <?php echo number_format($totalCredits, 0); ?></h3>
                                                <p class="text-muted mb-0">Total Credits Received</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card border bg-light-subtle h-100">
                                            <div class="card-body text-center">
                                                <div class="avatar avatar-md bg-danger-transparent mx-auto mb-3">
                                                    <i class="ri-refund-2-line text-danger"></i>
                                                </div>
                                                <?php 
                                                        $totalBalance = 0;
                                                        foreach ($inputCredits as $credit) {
                                                            if ($credit->status == 'fulfilled' || $credit->status == 'approved' || 
                                                                $credit->credit_status == 'active') {
                                                                $totalBalance += $credit->remaining_balance ?? 
                                                                    $credit->total_with_interest ?? 
                                                                    ($credit->total_amount * (1 + $credit->credit_percentage / 100));
                                                            }
                                                        }
                                                    ?>
                                                <h3 class="mb-1">KES <?php echo number_format($totalBalance, 0); ?></h3>
                                                <p class="text-muted mb-0">Outstanding Balance</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card border bg-light-subtle h-100">
                                            <div class="card-body text-center">
                                                <div class="avatar avatar-md bg-info-transparent mx-auto mb-3">
                                                    <i class="ri-store-2-line text-info"></i>
                                                </div>
                                                <?php 
                                                       $agrovets = [];
                                                       foreach ($inputCredits as $credit) {
                                                           if ($credit->agrovet_name) {
                                                               $agrovets[$credit->agrovet_name] = true;
                                                           }
                                                       }
                                                       $agrovetCount = count($agrovets);
                                                   ?>
                                                <h3 class="mb-1"><?php echo $agrovetCount; ?></h3>
                                                <p class="text-muted mb-0">Agrovets Used</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php else: ?>
                                <div class="text-center p-5">
                                    <div class="avatar avatar-lg bg-light-subtle mx-auto mb-3">
                                        <i class="ri-shopping-cart-line fs-2 text-muted"></i>
                                    </div>
                                    <h6>No Input Credits</h6>
                                    <p class="text-muted">This farmer hasn't received any input credits yet</p>
                                    <button class="btn btn-sm btn-primary">
                                        <i class="ri-add-line me-1"></i> Apply for Input Credit
                                    </button>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Initialize tooltips
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll(
                        '[data-bs-toggle="tooltip"]'));
                    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });

                    // Initialize DataTable if available
                    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.DataTable !== 'undefined') {
                        $('#inputCreditsTable').DataTable({
                            responsive: true,
                            ordering: true,
                            paging: true,
                            searching: true,
                            info: true,
                            lengthChange: true,
                            lengthMenu: [
                                [10, 25, 50, -1],
                                [10, 25, 50, "All"]
                            ],
                            columnDefs: [{
                                    orderable: false,
                                    targets: [9]
                                },
                                {
                                    className: "dt-center",
                                    targets: [8, 9]
                                },
                                {
                                    className: "dt-right",
                                    targets: [4, 7]
                                }
                            ]
                        });
                    }
                });
                </script>
                <!-- Activity Logs Section with Timeline View -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card custom-card overflow-hidden">
                            <div
                                class="card-header d-flex justify-content-between align-items-center bg-light-subtle border-bottom-0">
                                <div class="card-title mb-0">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm bg-info-transparent rounded me-2">
                                            <i class="ri-history-line fs-16 text-info"></i>
                                        </span>
                                        <h5 class="mb-0">Activity History</h5>
                                    </div>
                                </div>
                                <div>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="ri-filter-line me-1"></i> Filter
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php
                                  // Dedicated query to get activity logs for this farmer
                                  $activityLogsQuery = "SELECT 
                                      al.id, al.activity_type, al.description, al.created_at,
                                      u.first_name, u.last_name, u.role_id,
                                      r.name as role_name,
                                      CASE 
                                          WHEN al.description LIKE '%produce%' THEN 'Produce Management'
                                          WHEN al.description LIKE '%loan%' THEN 'Loan Management'
                                          WHEN al.description LIKE '%credit%' THEN 'Input Credit Management'
                                          WHEN al.description LIKE '%farm%' THEN 'Farm Management'
                                          ELSE 'General'
                                      END as module,
                                      CASE 
                                          WHEN al.description LIKE '%approved%' THEN 'Approved'
                                          WHEN al.description LIKE '%rejected%' THEN 'Rejected'
                                          WHEN al.description LIKE '%verified%' THEN 'Verified'
                                          WHEN al.description LIKE '%completed%' THEN 'Completed'
                                          WHEN al.description LIKE '%sold%' THEN 'Sold'
                                          WHEN al.description LIKE '%paid%' THEN 'Paid'
                                          ELSE NULL
                                      END as status_change,
                                      CASE
                                          WHEN al.description LIKE '%LOAN%' THEN SUBSTRING_INDEX(SUBSTRING_INDEX(al.description, 'LOAN', -1), ' ', 1)
                                          WHEN al.description LIKE '%DLVR%' THEN SUBSTRING_INDEX(SUBSTRING_INDEX(al.description, 'DLVR', -1), ' ', 1)
                                          WHEN al.description LIKE '%INPCR%' THEN SUBSTRING_INDEX(SUBSTRING_INDEX(al.description, 'INPCR', -1), ' ', 1)
                                          ELSE NULL
                                      END as reference_id
                                  FROM activity_logs al
                                  JOIN users u ON al.user_id = u.id
                                  JOIN roles r ON u.role_id = r.id
                                  WHERE al.description LIKE CONCAT('%', (SELECT registration_number FROM farmers WHERE id = $farmerId), '%')
                                     OR al.description LIKE CONCAT('%farmer_id = ', $farmerId, '%')
                                  ORDER BY al.created_at DESC
                                  LIMIT 20";
                                  
                                  $activityLogs = $app->select_all($activityLogsQuery);
                                  ?>

                                <?php if ($activityLogs && count($activityLogs) > 0): ?>
                                <div class="timeline-page mb-4">
                                    <?php 
                                           $currentDate = '';
                                           foreach ($activityLogs as $log): 
                                               $logDate = date('Y-m-d', strtotime($log->created_at));
                                
                                // Show date header when the date changes
                                if ($currentDate != $logDate):
                                    $currentDate = $logDate;
                                    if ($logDate == date('Y-m-d')):
                                          ?>
                                    <div class="timeline-date-label mb-4">
                                        <span class="badge bg-primary-transparent px-3 py-2">Today</span>
                                    </div>
                                    <?php elseif ($logDate == date('Y-m-d', strtotime('-1 day'))): ?>
                                    <div class="timeline-date-label mb-4">
                                        <span class="badge bg-primary-transparent px-3 py-2">Yesterday</span>
                                    </div>
                                    <?php else: ?>
                                    <div class="timeline-date-label mb-4">
                                        <span
                                            class="badge bg-primary-transparent px-3 py-2"><?php echo date('F j, Y', strtotime($log->created_at)); ?></span>
                                    </div>
                                    <?php endif;
                                            endif;
                                            
                                            // Set badge color based on activity type
                                            $activityColor = 'primary';
                                            $activityIcon = 'ri-file-list-line';
                                            
                                            if (strpos(strtolower($log->activity_type), 'loan') !== false) {
                                                $activityColor = 'warning';
                                                $activityIcon = 'ri-bank-line';
                                            } elseif (strpos(strtolower($log->activity_type), 'produce') !== false) {
                                                $activityColor = 'success';
                                                $activityIcon = 'ri-truck-line';
                                            } elseif (strpos(strtolower($log->activity_type), 'credit') !== false) {
                                                $activityColor = 'info';
                                                $activityIcon = 'ri-shopping-cart-line';
                                            } elseif (strpos(strtolower($log->activity_type), 'verify') !== false) {
                                                $activityColor = 'success';
                                                $activityIcon = 'ri-checkbox-circle-line';
                                            } elseif (strpos(strtolower($log->activity_type), 'reject') !== false) {
                                                $activityColor = 'danger';
                                                $activityIcon = 'ri-close-circle-line';
                                            }
                                            
                                            // Module icon
                                            $moduleIcon = 'ri-file-list-line';
                                            $moduleColor = 'secondary';
                                            
                                            if ($log->module == 'Produce Management') {
                                                $moduleIcon = 'ri-truck-line';
                                                $moduleColor = 'success';
                                            } elseif ($log->module == 'Loan Management') {
                                                $moduleIcon = 'ri-bank-line';
                                                $moduleColor = 'warning';
                                            } elseif ($log->module == 'Input Credit Management') {
                                                $moduleIcon = 'ri-shopping-cart-line';
                                                $moduleColor = 'info';
                                            } elseif ($log->module == 'Farm Management') {
                                                $moduleIcon = 'ri-plant-line';
                                                $moduleColor = 'primary';
                                            }
                                            
                                            // Status change colors
                                            $statusColor = 'secondary';
                                            $statusIcon = 'ri-information-line';
                                            
                                            if ($log->status_change == 'Approved') {
                                                $statusColor = 'success';
                                                $statusIcon = 'ri-check-line';
                                            } elseif ($log->status_change == 'Rejected') {
                                                $statusColor = 'danger';
                                                $statusIcon = 'ri-close-line';
                                            } elseif ($log->status_change == 'Verified') {
                                                $statusColor = 'info';
                                                $statusIcon = 'ri-checkbox-circle-line';
                                            } elseif ($log->status_change == 'Completed') {
                                                $statusColor = 'primary';
                                                $statusIcon = 'ri-check-double-line';
                                            } elseif ($log->status_change == 'Sold') {
                                                $statusColor = 'warning';
                                                $statusIcon = 'ri-exchange-line';
                                            } elseif ($log->status_change == 'Paid') {
                                                $statusColor = 'success';
                                                $statusIcon = 'ri-money-dollar-circle-line';
                                            }
                                          ?>
                                    <div class="timeline-item mb-4">
                                        <div class="timeline-badge bg-<?php echo $activityColor; ?>-transparent">
                                            <i
                                                class="<?php echo $activityIcon; ?> text-<?php echo $activityColor; ?>"></i>
                                        </div>
                                        <div class="timeline-item-content">
                                            <div class="card border">
                                                <div class="card-body p-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span
                                                            class="badge bg-<?php echo $activityColor; ?>-transparent text-<?php echo $activityColor; ?> fw-semibold">
                                                            <?php echo htmlspecialchars(ucfirst($log->activity_type)); ?>
                                                        </span>
                                                        <small class="text-muted">
                                                            <i class="ri-time-line me-1"></i>
                                                            <?php echo date('h:i A', strtotime($log->created_at)); ?>
                                                        </small>
                                                    </div>

                                                    <p class="mb-2"><?php echo htmlspecialchars($log->description); ?>
                                                    </p>

                                                    <div class="d-flex flex-wrap gap-2 mt-2 activity-details">
                                                        <div class="detail-item">
                                                            <i class="ri-user-line text-primary me-1"></i>
                                                            <span
                                                                class="text-dark fw-medium"><?php echo htmlspecialchars($log->first_name . ' ' . $log->last_name); ?></span>
                                                            <span
                                                                class="badge bg-light-subtle text-dark ms-1"><?php echo htmlspecialchars($log->role_name); ?></span>
                                                        </div>

                                                        <div class="detail-item">
                                                            <i
                                                                class="<?php echo $moduleIcon; ?> text-<?php echo $moduleColor; ?> me-1"></i>
                                                            <span><?php echo htmlspecialchars($log->module); ?></span>
                                                        </div>

                                                        <?php if ($log->reference_id): ?>
                                                        <div class="detail-item">
                                                            <i class="ri-hashtag text-secondary me-1"></i>
                                                            <span><?php echo htmlspecialchars($log->reference_id); ?></span>
                                                        </div>
                                                        <?php endif; ?>

                                                        <?php if ($log->status_change): ?>
                                                        <div class="detail-item">
                                                            <i
                                                                class="<?php echo $statusIcon; ?> text-<?php echo $statusColor; ?> me-1"></i>
                                                            <span
                                                                class="badge bg-<?php echo $statusColor; ?>-transparent text-<?php echo $statusColor; ?>">
                                                                <?php echo htmlspecialchars($log->status_change); ?>
                                                            </span>
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="text-center">
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="ri-history-line me-1"></i> Load More
                                    </button>
                                </div>
                                <?php else: ?>
                                <div class="text-center p-5">
                                    <div class="avatar avatar-lg bg-light-subtle mx-auto mb-3">
                                        <i class="ri-history-line fs-2 text-muted"></i>
                                    </div>
                                    <h6>No Activity History</h6>
                                    <p class="text-muted">No activities have been recorded for this farmer yet</p>
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