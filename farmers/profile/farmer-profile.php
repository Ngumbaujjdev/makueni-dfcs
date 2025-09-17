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
    <meta name="apple-mobile-web-app-title" content="Makueni DFCS" />
    <link rel="manifest" href="http://localhost/dfcs/assets/images/favicon/site.webmanifest" />
    <!-- Main Theme Js -->
    <!-- Choices JS -->
    <script src="http://localhost/dfcs/assets/libs/choices.js/public/assets/scripts/choices.min.js"></script>
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

    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/glightbox/css/glightbox.min.css">
    <link rel="stylesheet" href="http://localhost/dfcs/toast/toast.css">

</head>

<body>
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

                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                    <h1 class="page-title fw-semibold fs-18 mb-0">Profile</h1>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Pages</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Profile</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <!-- Page Header Close -->

                <!-- Start::row-1 -->
                <div class="row">
                    <?php
                                  // Get session user_id and role_id
                                  if (session_status() === PHP_SESSION_NONE) {
                                      session_start();
                                  }
                                                  
                                  $userId = $_SESSION['user_id'] ?? null;
                                  if (!$userId) {
                                      header("Location: http://localhost/dfcs/");
                                      exit();
                                  }
                                                  
                                  $app = new App();
                                                  
                                  // Get farmer ID and profile info from user_id
                                  $query = "SELECT f.id as farmer_id, u.first_name, u.last_name, u.phone, u.email, u.location, u.profile_picture 
                                            FROM farmers f
                                            JOIN users u ON f.user_id = u.id
                                            WHERE f.user_id = $userId";
                                  $farmer = $app->select_one($query);
                                                  
                                  if (!$farmer) {
                                      header("Location: http://localhost/dfcs/"); 
                                      exit();
                                  }
                                                  
                                  // Get stats
                                  $statsQuery = "SELECT 
                                      (SELECT COUNT(*) FROM farms WHERE farmer_id = $farmer->farmer_id) as total_farms,
                                      (SELECT COUNT(*) FROM farm_products fp 
                                       JOIN farms f ON fp.farm_id = f.id 
                                       WHERE f.farmer_id = $farmer->farmer_id AND fp.is_active = 1) as active_products,
                                      (SELECT COUNT(*) FROM produce_deliveries pd 
                                       JOIN farm_products fp ON pd.farm_product_id = fp.id
                                       JOIN farms f ON fp.farm_id = f.id  
                                       WHERE f.farmer_id = $farmer->farmer_id) as total_deliveries,
                                      (SELECT SUM(remaining_balance) FROM approved_loans al
                                       JOIN loan_applications la ON al.loan_application_id = la.id
                                       WHERE la.farmer_id = $farmer->farmer_id AND al.status = 'active') as loan_balance";
                                                  
                                  $stats = $app->select_one($statsQuery);
                       ?>

                    <div class="d-sm-flex align-items-top p-4 border-bottom-0 main-profile-cover">
                        <div>
                            <span class="avatar avatar-xxl avatar-rounded online me-3">
                                <img src="http://localhost/dfcs/<?= $farmer->profile_picture ?? 'http://localhost/dfcs/assets/images/faces/face-image-1.jpg' ?>"
                                    alt="">
                            </span>
                        </div>
                        <div class="flex-fill main-profile-info">
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="fw-semibold mb-1 text-fixed-white">
                                    <?= htmlspecialchars($farmer->first_name . ' ' . $farmer->last_name) ?>
                                </h6>
                            </div>
                            <p class="mb-1 text-muted text-fixed-white op-7">Farmer</p>
                            <p class="fs-12 text-fixed-white mb-4 op-5">
                                <span class="me-3">
                                    <i class="ri-map-pin-line me-1 align-middle"></i>
                                    <?= htmlspecialchars($farmer->location ?? 'Location not set') ?>
                                </span>
                            </p>
                            <div class="d-flex mb-0">
                                <div class="me-4">
                                    <p class="fw-bold fs-20 text-fixed-white text-shadow mb-0">
                                        <?= $stats->total_farms ?? 0 ?></p>
                                    <p class="mb-0 fs-11 op-5 text-fixed-white">Farms</p>
                                </div>
                                <div class="me-4">
                                    <p class="fw-bold fs-20 text-fixed-white text-shadow mb-0">
                                        <?= $stats->active_products ?? 0 ?></p>
                                    <p class="mb-0 fs-11 op-5 text-fixed-white">Active Products</p>
                                </div>
                                <div class="me-4">
                                    <p class="fw-bold fs-20 text-fixed-white text-shadow mb-0">
                                        <?= $stats->total_deliveries ?? 0 ?></p>
                                    <p class="mb-0 fs-11 op-5 text-fixed-white">Deliveries</p>
                                </div>
                                <div class="me-4">
                                    <p class="fw-bold fs-20 text-fixed-white text-shadow mb-0">KES
                                        <?= number_format($stats->loan_balance ?? 0, 2) ?></p>
                                    <p class="mb-0 fs-11 op-5 text-fixed-white">Active Loans</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Middle Section Stats Cards -->
                    <div class="row mt-4 mb-4">
                        <!-- Farm Summary Card -->
                        <div class="col-xl-4">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">Farm Summary</div>
                                </div>
                                <div class="card-body">
                                    <?php
                                          $farmSummaryQuery = "SELECT 
                                              COALESCE(SUM(size), 0) as total_land_size,
                                              COUNT(DISTINCT f.id) as farm_count,
                                              GROUP_CONCAT(DISTINCT pt.name) as active_crops
                                          FROM farms f
                                          LEFT JOIN farm_products fp ON f.id = fp.farm_id 
                                          LEFT JOIN product_types pt ON fp.product_type_id = pt.id
                                          WHERE f.farmer_id = $farmer->farmer_id AND fp.is_active = 1";
                                                              
                                          $farmSummary = $app->select_one($farmSummaryQuery);
                                      ?>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="ti ti-ruler me-2"></i>Total Land Size</span>
                                            <span
                                                class="badge bg-light text-dark"><?php echo number_format($farmSummary->total_land_size, 2) ?>
                                                acres</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="ti ti-plant me-2"></i>Active Crops</span>
                                            <span
                                                class="badge bg-light text-dark"><?php echo $farmSummary->active_crops ?? 'None' ?></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Overview Card -->
                        <div class="col-xl-4">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">Financial Overview</div>
                                </div>
                                <div class="card-body">
                                    <?php
                                   $financialQuery = "SELECT 
                                       COALESCE(SUM(pd.total_value), 0) as total_earnings,
                                       (SELECT COALESCE(SUM(remaining_balance), 0) 
                                        FROM approved_loans al 
                                        JOIN loan_applications la ON al.loan_application_id = la.id 
                                        WHERE la.farmer_id = $farmer->farmer_id AND al.status = 'active') as active_loans,
                                       (SELECT COALESCE(SUM(remaining_balance), 0) 
                                        FROM approved_input_credits aic
                                        JOIN input_credit_applications ica ON aic.credit_application_id = ica.id 
                                        WHERE ica.farmer_id = $farmer->farmer_id AND aic.status = 'active') as input_credits
                                   FROM produce_deliveries pd
                                   WHERE pd.farm_product_id IN (
                                       SELECT id FROM farm_products WHERE farm_id IN (
                                           SELECT id FROM farms WHERE farmer_id = $farmer->farmer_id
                                       )
                                   )";
                                                   
                                   $financialSummary = $app->select_one($financialQuery);
                                  ?>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="ti ti-coin me-2"></i>Total Earnings</span>
                                            <span class="badge bg-success-transparent">KES
                                                <?php echo number_format($financialSummary->total_earnings, 2) ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="ti ti-credit-card me-2"></i>Active Loans</span>
                                            <span class="badge bg-warning-transparent">KES
                                                <?php echo number_format($financialSummary->active_loans, 2) ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="ti ti-shopping-cart me-2"></i>Input Credits</span>
                                            <span class="badge bg-primary-transparent">KES
                                                <?php echo number_format($financialSummary->input_credits, 2) ?></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Next Delivery Card -->
                        <div class="col-xl-4">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <div class="card-title">Next Expected Delivery</div>
                                </div>
                                <div class="card-body">
                                    <?php
                                            $nextDeliveryQuery = "SELECT 
                                                ep.expected_delivery_date,
                                                ep.expected_quantity,
                                                pt.name as product_name
                                            FROM expected_produce ep
                                            JOIN farm_products fp ON ep.farm_product_id = fp.id
                                            JOIN product_types pt ON fp.product_type_id = pt.id
                                            WHERE fp.farm_id IN (
                                                SELECT id FROM farms WHERE farmer_id = $farmer->farmer_id
                                            )
                                            AND ep.status = 'pending'
                                            ORDER BY ep.expected_delivery_date ASC
                                            LIMIT 1";
                                
                                            $nextDelivery = $app->select_one($nextDeliveryQuery);
                                   ?>
                                    <?php if ($nextDelivery): ?>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="ti ti-calendar me-2"></i>Delivery Date</span>
                                            <span class="badge bg-light text-dark">
                                                <?php echo date('M d, Y', strtotime($nextDelivery->expected_delivery_date)) ?>
                                            </span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="ti ti-box me-2"></i>Product</span>
                                            <span
                                                class="badge bg-light text-dark"><?php echo $nextDelivery->product_name ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="ti ti-scale me-2"></i>Expected Quantity</span>
                                            <span
                                                class="badge bg-light text-dark"><?php echo number_format($nextDelivery->expected_quantity, 2) ?>
                                                KG</span>
                                        </li>
                                    </ul>
                                    <?php else: ?>
                                    <div class="text-center py-3">
                                        <i class="ti ti-calendar-off fs-1 text-muted"></i>
                                        <p class="mt-2 text-muted">No upcoming deliveries scheduled</p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Middle Section Stats Cards -->
                    <!-- new row for tabs -->
                    <div class="row">
                        <div class="col-xxl-12 col-xl-12">
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="card custom-card">
                                        <div class="card-body p-0">
                                            <!-- Tabs Section -->
                                            <!-- Tabs Navigation -->
                                            <div class="p-3 border-bottom border-block-end-dashed">
                                                <ul class="nav nav-tabs mb-0 tab-style-6 justify-content-start"
                                                    id="farmerTabs" role="tablist">
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link active" data-bs-toggle="tab"
                                                            data-bs-target="#farms-tab" type="button">
                                                            <i class="ti ti-tractor me-1"></i>My Farms
                                                        </button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link" data-bs-toggle="tab"
                                                            data-bs-target="#bank-loans-tab" type="button">
                                                            <i class="ti ti-building-bank me-1"></i>Bank Loans
                                                        </button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link" data-bs-toggle="tab"
                                                            data-bs-target="#sacco-loans-tab" type="button">
                                                            <i class="ti ti-coin me-1"></i>SACCO Loans
                                                        </button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link" data-bs-toggle="tab"
                                                            data-bs-target="#inputs-tab" type="button">
                                                            <i class="ti ti-shopping-cart me-1"></i>Input Credits
                                                        </button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link" data-bs-toggle="tab"
                                                            data-bs-target="#expected-tab" type="button">
                                                            <i class="ti ti-calendar me-1"></i>Expected Produce
                                                        </button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link" data-bs-toggle="tab"
                                                            data-bs-target="#profile-tab" type="button">
                                                            <i class="ti ti-user me-1"></i>Update Profile
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>

                                            <!-- Tab Content -->
                                            <div class="tab-content p-3" id="farmerTabsContent">
                                                <!-- 1. Farms Tab -->
                                                <div class="tab-pane fade show active" id="farms-tab" role="tabpanel">
                                                    <div class="card custom-card">
                                                        <div class="card-header">
                                                            <h6 class="card-title">My Farms</h6>

                                                        </div>
                                                        <div class="card-body">
                                                            <?php
                                                              $farmsQuery = "SELECT f.*, ft.name as farm_type_name 
                                                                             FROM farms f
                                                                             JOIN farm_types ft ON f.farm_type_id = ft.id
                                                                             WHERE f.farmer_id = $farmer->farmer_id";
                                                              $farms = $app->select_all($farmsQuery);
                                                              ?>
                                                            <div class="table-responsive">
                                                                <table class="table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Farm Name</th>
                                                                            <th>Location</th>
                                                                            <th>Size</th>
                                                                            <th>Type</th>
                                                                            <th>Products</th>

                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php if($farms): foreach($farms as $farm): ?>
                                                                        <tr>
                                                                            <td><?php echo htmlspecialchars($farm->name) ?>
                                                                            </td>
                                                                            <td><?php echo htmlspecialchars($farm->location) ?>
                                                                            </td>
                                                                            <td><?php echo $farm->size . ' ' . $farm->size_unit ?>
                                                                            </td>
                                                                            <td><?php echo htmlspecialchars($farm->farm_type_name) ?>
                                                                            </td>
                                                                            <td>
                                                                                <?php
                                                                                   $productsQuery = "SELECT pt.name 
                                                                                                   FROM farm_products fp 
                                                                                                   JOIN product_types pt ON fp.product_type_id = pt.id 
                                                                                                   WHERE fp.farm_id = $farm->id AND fp.is_active = 1";
                                                                                   $products = $app->select_all($productsQuery);
                                                                                   foreach($products as $product) {
                                                                                       echo "<span class='badge bg-light text-dark me-1'>{$product->name}</span>";
                                                                                   }
                                                                                   ?>
                                                                            </td>

                                                                        </tr>
                                                                        <?php endforeach; else: ?>
                                                                        <tr>
                                                                            <td colspan="6" class="text-center">No farms
                                                                                registered</td>
                                                                        </tr>
                                                                        <?php endif; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Bank Loans Tab -->
                                                <div class="tab-pane fade" id="bank-loans-tab" role="tabpanel">
                                                    <div class="card custom-card">
                                                        <div class="card-header">
                                                            <h6 class="card-title">Bank Loan History</h6>

                                                        </div>
                                                        <div class="card-body">
                                                            <?php
                                                               $bankLoansQuery = "SELECT la.*, al.*, b.name as bank_name, lt.name as loan_type_name, lt.interest_rate
                                                                   FROM loan_applications la 
                                                                   JOIN approved_loans al ON la.id = al.loan_application_id  /* Changed this line */
                                                                   JOIN banks b ON la.bank_id = b.id
                                                                   JOIN loan_types lt ON la.loan_type_id = lt.id
                                                                   WHERE la.farmer_id = $farmer->farmer_id 
                                                                   AND lt.provider_type = 'bank'
                                                                   ORDER BY la.application_date DESC";
                                                                $bankLoans = $app->select_all($bankLoansQuery);
                                                                ?>
                                                            <div class="table-responsive">
                                                                <table class="table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Bank</th>
                                                                            <th>Loan Type</th>
                                                                            <th>Amount</th>
                                                                            <th>Interest Rate</th>
                                                                            <th>Date</th>
                                                                            <th>Status</th>
                                                                            <th>Balance</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php if($bankLoans): foreach($bankLoans as $loan): ?>
                                                                        <tr>
                                                                            <td><?php echo htmlspecialchars($loan->bank_name) ?>
                                                                            </td>
                                                                            <td><?php echo htmlspecialchars($loan->loan_type_name) ?>
                                                                            </td>
                                                                            <td>KES
                                                                                <?php echo number_format($loan->approved_amount, 2) ?>
                                                                            </td>
                                                                            <td><?php echo $loan->interest_rate ?>%</td>
                                                                            <td><?php echo date('M d, Y', strtotime($loan->approval_date)) ?>
                                                                            </td>
                                                                            <td>
                                                                                <span
                                                                                    class="badge bg-<?php echo $loan->status == 'active' ? 'success' : 'secondary' ?>-transparent">
                                                                                    <?php echo ucfirst($loan->status) ?>
                                                                                </span>
                                                                            </td>
                                                                            <td>KES
                                                                                <?php echo number_format($loan->remaining_balance, 2) ?>
                                                                            </td>
                                                                        </tr>
                                                                        <?php endforeach; else: ?>
                                                                        <tr>
                                                                            <td colspan="7" class="text-center">No bank
                                                                                loans found</td>
                                                                        </tr>
                                                                        <?php endif; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- SACCO Loans Tab -->
                                                <div class="tab-pane fade" id="sacco-loans-tab" role="tabpanel">
                                                    <div class="card custom-card">
                                                        <div class="card-header">
                                                            <h6 class="card-title">SACCO Loan History</h6>

                                                        </div>
                                                        <div class="card-body">
                                                            <?php
                                                               $saccoLoansQuery = "SELECT la.*, al.*, lt.name as loan_type_name, lt.interest_rate
                                                                                  FROM loan_applications la 
                                                                                  JOIN approved_loans al ON la.id = al.loan_application_id 
                                                                                  JOIN loan_types lt ON la.loan_type_id = lt.id
                                                                                  WHERE la.farmer_id = $farmer->farmer_id 
                                                                                  AND lt.provider_type = 'sacco'
                                                                                  ORDER BY la.application_date DESC";
                                                               $saccoLoans = $app->select_all($saccoLoansQuery);
                                                               ?>
                                                            <div class="table-responsive">
                                                                <table class="table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Loan ID</th>
                                                                            <th>Type</th>
                                                                            <th>Amount</th>
                                                                            <th>Interest Rate</th>
                                                                            <th>Date</th>
                                                                            <th>Status</th>
                                                                            <th>Balance</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php if($saccoLoans): foreach($saccoLoans as $loan): ?>
                                                                        <tr>
                                                                            <td>SACCO-<?php echo str_pad($loan->id, 5, '0', STR_PAD_LEFT) ?>
                                                                            </td>
                                                                            <td><?php echo htmlspecialchars($loan->loan_type_name) ?>
                                                                            </td>
                                                                            <td>KES
                                                                                <?php echo number_format($loan->approved_amount, 2) ?>
                                                                            </td>
                                                                            <td><?php echo $loan->interest_rate ?>%</td>
                                                                            <td><?php echo date('M d, Y', strtotime($loan->approval_date)) ?>
                                                                            </td>
                                                                            <td>
                                                                                <span
                                                                                    class="badge bg-<?php echo $loan->status == 'active' ? 'success' : 'secondary' ?>-transparent">
                                                                                    <?php echo ucfirst($loan->status) ?>
                                                                                </span>
                                                                            </td>
                                                                            <td>KES
                                                                                <?php echo number_format($loan->remaining_balance, 2) ?>
                                                                            </td>
                                                                        </tr>
                                                                        <?php endforeach; else: ?>
                                                                        <tr>
                                                                            <td colspan="7" class="text-center">No SACCO
                                                                                loans found</td>
                                                                        </tr>
                                                                        <?php endif; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Input Credits Tab -->
                                                <div class="tab-pane fade" id="inputs-tab" role="tabpanel">
                                                    <div class="card custom-card">
                                                        <div class="card-header">
                                                            <h6 class="card-title">Input Credits</h6>
                                                            <div class="card-options">

                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <?php
                                                                $inputCreditsQuery = "SELECT ica.*, aic.*, a.name as agrovet_name, 
                                                                                     GROUP_CONCAT(CONCAT(ici.input_name, ' (', ici.quantity, ' ', ici.unit, ')')) as items
                                                                                     FROM input_credit_applications ica
                                                                                     JOIN approved_input_credits aic ON ica.id = aic.credit_application_id
                                                                                     JOIN agrovets a ON ica.agrovet_id = a.id
                                                                                     LEFT JOIN input_credit_items ici ON ica.id = ici.credit_application_id
                                                                                     WHERE ica.farmer_id = $farmer->farmer_id
                                                                                     GROUP BY ica.id
                                                                                     ORDER BY ica.application_date DESC";
                                                                $inputCredits = $app->select_all($inputCreditsQuery);
                                                                ?>
                                                            <div class="table-responsive">
                                                                <table class="table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Agrovet</th>
                                                                            <th>Items</th>
                                                                            <th>Amount</th>
                                                                            <th>Interest</th>
                                                                            <th>Date</th>
                                                                            <th>Status</th>
                                                                            <th>Balance</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php if($inputCredits): foreach($inputCredits as $credit): ?>
                                                                        <tr>
                                                                            <td><?php echo htmlspecialchars($credit->agrovet_name) ?>
                                                                            </td>
                                                                            <td>
                                                                                <small><?php echo htmlspecialchars($credit->items) ?></small>
                                                                            </td>
                                                                            <td>KES
                                                                                <?php echo number_format($credit->approved_amount, 2) ?>
                                                                            </td>
                                                                            <td><?php echo $credit->credit_percentage ?>%
                                                                            </td>
                                                                            <td><?php echo date('M d, Y', strtotime($credit->approval_date)) ?>
                                                                            </td>
                                                                            <td>
                                                                                <span
                                                                                    class="badge bg-<?php echo $credit->status == 'active' ? 'success' : 'secondary' ?>-transparent">
                                                                                    <?php echo ucfirst($credit->status) ?>
                                                                                </span>
                                                                            </td>
                                                                            <td>KES
                                                                                <?php echo number_format($credit->remaining_balance, 2) ?>
                                                                            </td>
                                                                        </tr>
                                                                        <?php endforeach; else: ?>
                                                                        <tr>
                                                                            <td colspan="7" class="text-center">No input
                                                                                credits found</td>
                                                                        </tr>
                                                                        <?php endif; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Expected Produce Tab -->
                                                <div class="tab-pane fade" id="expected-tab" role="tabpanel">
                                                    <div class="card custom-card">
                                                        <div class="card-header">
                                                            <h6 class="card-title">Expected Produce</h6>

                                                        </div>
                                                        <div class="card-body">
                                                            <?php
                                                             $expectedProduceQuery = "SELECT ep.*, pt.name as product_name, pt.measurement_unit,
                                                                                     f.name as farm_name
                                                                                     FROM expected_produce ep
                                                                                     JOIN farm_products fp ON ep.farm_product_id = fp.id
                                                                                     JOIN product_types pt ON fp.product_type_id = pt.id
                                                                                     JOIN farms f ON fp.farm_id = f.id
                                                                                     WHERE f.farmer_id = $farmer->farmer_id AND ep.status = 'pending'
                                                                                     ORDER BY ep.expected_delivery_date ASC";
                                                             $expectedProduce = $app->select_all($expectedProduceQuery);
                                                             ?>
                                                            <div class="table-responsive">
                                                                <table class="table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Farm</th>
                                                                            <th>Product</th>
                                                                            <th>Expected Quantity</th>
                                                                            <th>Unit Price</th>
                                                                            <th>Delivery Date</th>
                                                                            <th>Est. Value</th>
                                                                            <th>Actions</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php if($expectedProduce): foreach($expectedProduce as $produce): ?>
                                                                        <tr>
                                                                            <td><?php echo htmlspecialchars($produce->farm_name) ?>
                                                                            </td>
                                                                            <td><?php echo htmlspecialchars($produce->product_name) ?>
                                                                            </td>
                                                                            <td><?php echo number_format($produce->expected_quantity, 2) ?>
                                                                                <?php echo $produce->measurement_unit ?>
                                                                            </td>
                                                                            <td>KES
                                                                                <?php echo number_format($produce->estimated_unit_price, 2) ?>
                                                                            </td>
                                                                            <td><?php echo date('M d, Y', strtotime($produce->expected_delivery_date)) ?>
                                                                            </td>
                                                                            <td>KES
                                                                                <?php echo number_format($produce->estimated_total_value, 2) ?>
                                                                            </td>
                                                                            <td>
                                                                                <button
                                                                                    class="btn btn-sm btn-primary me-1"
                                                                                    onclick="editExpectedProduce(<?php echo $produce->id ?>)">
                                                                                    <i class="ti ti-edit"></i>
                                                                                </button>
                                                                                <button class="btn btn-sm btn-danger"
                                                                                    onclick="deleteExpectedProduce(<?php echo $produce->id ?>)">
                                                                                    <i class="ti ti-trash"></i>
                                                                                </button>
                                                                            </td>
                                                                        </tr>
                                                                        <?php endforeach; else: ?>
                                                                        <tr>
                                                                            <td colspan="7" class="text-center">No
                                                                                expected produce found</td>
                                                                        </tr>
                                                                        <?php endif; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Update Profile Tab -->
                                                <div class="tab-pane fade" id="profile-tab" role="tabpanel">
                                                    <div class="card custom-card">
                                                        <div class="card-header">
                                                            <h6 class="card-title">Update Profile</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <form id="updateProfileForm" method="post"
                                                                enctype="multipart/form-data">
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3">
                                                                        <label class="form-label">First Name</label>
                                                                        <input type="text" class="form-control"
                                                                            name="first_name"
                                                                            value="<?php echo htmlspecialchars($farmer->first_name) ?>"
                                                                            required>
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <label class="form-label">Last Name</label>
                                                                        <input type="text" class="form-control"
                                                                            name="last_name"
                                                                            value="<?php echo htmlspecialchars($farmer->last_name) ?>"
                                                                            required>
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <label class="form-label">Phone</label>
                                                                        <input type="tel" class="form-control"
                                                                            name="phone"
                                                                            value="<?php echo htmlspecialchars($farmer->phone) ?>"
                                                                            required>
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <label class="form-label">Email</label>
                                                                        <input type="email" class="form-control"
                                                                            name="email"
                                                                            value="<?php echo htmlspecialchars($farmer->email) ?>"
                                                                            required>
                                                                    </div>
                                                                    <div class="col-12 mb-3">
                                                                        <label class="form-label">Location</label>
                                                                        <input type="text" class="form-control"
                                                                            name="location"
                                                                            value="<?php echo htmlspecialchars($farmer->location) ?>"
                                                                            required>
                                                                    </div>
                                                                    <div class="col-12 mb-3">
                                                                        <label class="form-label">Profile
                                                                            Picture</label>
                                                                        <input type="file" class="form-control"
                                                                            name="profile_picture" accept="image/*">
                                                                        <?php if($farmer->profile_picture): ?>
                                                                        <div class="mt-2">
                                                                            <img src="<?php echo $farmer->profile_picture ?>"
                                                                                alt="Current profile picture"
                                                                                class="img-thumbnail"
                                                                                style="max-width: 100px;">
                                                                        </div>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <button type="submit"
                                                                            class="btn btn-primary">Update
                                                                            Profile</button>
                                                                    </div>
                                                                </div>
                                                            </form>
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
                </div>
            </div>
            <!-- End::app-content -->

            <!-- Footer Start -->
            <?php include "../../includes/footer.php" ?>
            <!-- Footer End -->

        </div>


        <!-- Scroll To Top -->
        <div class="scrollToTop">
            <span class="arrow"><i class="ri-arrow-up-s-fill fs-20"></i></span>
        </div>
        <div id="responsive-overlay"></div>
        <!-- Scroll To Top -->

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
        <script src="http://localhost/dfcs/assets/js/sticky.js"></script>

        <!-- Simplebar JS -->
        <script src="http://localhost/dfcs/assets/libs/simplebar/simplebar.min.js">
        </script>
        <script src="http://localhost/dfcs/assets/js/simplebar.js"></script>

        <!-- Color Picker JS -->
        <script src="http://localhost/dfcs/assets/libs/%40simonwep/pickr/pickr.es5.min.js">
        </script>



        <!-- Custom-Switcher JS -->
        <script src="http://localhost/dfcs/assets/js/custom-switcher.min.js">
        </script>

        <!-- Gallery JS -->
        <script src="http://localhost/dfcs/assets/libs/glightbox/js/glightbox.min.js">
        </script>

        <!-- Internal Profile JS -->
        <script src="http://localhost/dfcs/assets/js/profile.js"></script>

        <!-- Custom JS -->
        <script src="http://localhost/dfcs/assets/js/custom.js"></script>
        <!-- the toast -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
            integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js">
        </script>
        <script>
        $(document).ready(function() {
            // Handle profile update form submission
            $('#updateProfileForm').submit(function(e) {
                e.preventDefault();

                // Create form data object
                let formData = new FormData(this);

                // Send AJAX request
                $.ajax({
                    url: 'http://localhost/dfcs/ajax/farmer-controller/update-profile.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        try {
                            let data = JSON
                                .parse(
                                    response);
                            if (data.success) {
                                // Success toast
                                toastr.success(
                                    'Profile updated successfully',
                                    'Success', {
                                        timeOut: 3000,
                                        closeButton: true,
                                        progressBar: true,
                                        positionClass: "toast-top-right"
                                    });

                                // Reload page after 2 seconds
                                setTimeout(
                                    function() {
                                        location
                                            .reload();
                                    }, 2000);
                            } else {
                                // Error toast
                                toastr.error(
                                    data
                                    .message ||
                                    'Failed to update profile',
                                    'Error', {
                                        timeOut: 3000,
                                        closeButton: true,
                                        progressBar: true,
                                        positionClass: "toast-top-right"
                                    });
                            }
                        } catch (e) {
                            toastr.error(
                                'Error processing response',
                                'Error', {
                                    timeOut: 3000,
                                    closeButton: true,
                                    progressBar: true,
                                    positionClass: "toast-top-right"
                                });
                        }
                    },
                    error: function() {
                        toastr.error(
                            'Server error occurred',
                            'Error', {
                                timeOut: 3000,
                                closeButton: true,
                                progressBar: true,
                                positionClass: "toast-top-right"
                            });
                    }
                });
            });
        });
        </script>

</body>

</html>