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
                         echo '<div class="alert alert-danger">No input credit application ID specified</div>';
                         exit;
                     }
                     
                     $app = new App;
                     $creditId = intval($_GET['id']);
                     $userId = $_SESSION['user_id'];
                     
                     // Fetch the staff information to get agrovet_id
                     $staffQuery = "SELECT s.id as staff_id, s.agrovet_id 
                                   FROM agrovet_staff s 
                                   WHERE s.user_id = :user_id";
                     
                     $staff = $app->selectOne($staffQuery, [':user_id' => $userId]);
                     
                     if (!$staff) {
                         echo '<div class="alert alert-danger">Staff information not found</div>';
                         exit;
                     }
                     
                     // Fetch the input credit application details
                     $query = "SELECT 
                                 ica.id,
                                 ica.farmer_id,
                                 ica.agrovet_id,
                                 ica.total_amount,
                                 ica.credit_percentage,
                                 ica.total_with_interest,
                                 ica.repayment_percentage,
                                 ica.application_date,
                                 ica.creditworthiness_score,
                                 ica.status,
                                 a.name as agrovet_name,
                                 a.type_id as agrovet_type_id,
                                 at.name as agrovet_type,
                                 CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                                 u.phone as farmer_phone,
                                 u.email as farmer_email,
                                 u.profile_picture,
                                 fm.registration_number as farmer_reg,
                                 fm.category_id,
                                 fc.name as category_name
                               FROM input_credit_applications ica
                               JOIN agrovets a ON ica.agrovet_id = a.id
                               JOIN agrovet_types at ON a.type_id = at.id
                               JOIN farmers fm ON ica.farmer_id = fm.id
                               JOIN users u ON fm.user_id = u.id
                               LEFT JOIN farmer_categories fc ON fm.category_id = fc.id
                               WHERE ica.id = :credit_id AND ica.agrovet_id = :agrovet_id";
                     
                     $params = [
                         ':credit_id' => $creditId,
                         ':agrovet_id' => $staff->agrovet_id
                     ];
                     
                     $credit = $app->selectOne($query, $params);
                     
                     // Check if the credit application exists and belongs to the staff's agrovet
                     if (!$credit) {
                         echo '<div class="alert alert-danger">Input credit application not found or you do not have permission to view it</div>';
                         exit;
                     }
                     ?>

            <!-- Page Header with Action Buttons -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <h1 class="page-title fw-semibold fs-18 mb-0">Input Credit Application Review</h1>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-success" id="approveCredit" data-bs-toggle="modal"
                        data-bs-target="#approveCreditModal">
                        <i class="ri-check-line me-1"></i> Approve Credit
                    </button>
                    <button class="btn btn-danger" id="rejectCredit" data-bs-toggle="modal"
                        data-bs-target="#rejectCreditModal">
                        <i class="ri-close-line me-1"></i> Reject Application
                    </button>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="javascript:history.back()">Input Credits</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Application Review</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Row 1: Essential Input Credit Information Cards -->
            <div class="row">
                <!-- Card 1: Credit Reference -->
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
                    <div class="card custom-card mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="avatar avatar-md avatar-rounded bg-primary">
                                        <i class="ri-shopping-basket-line fs-16"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted fs-12">Reference</p>
                                    <h5 class="fw-semibold mb-1">
                                        ICREDIT<?php echo str_pad($credit->id, 5, '0', STR_PAD_LEFT); ?></h5>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-<?php 
                                              echo ($credit->status == 'under_review') ? 'info' : 
                                                  (($credit->status == 'approved') ? 'success' : 
                                                  (($credit->status == 'rejected') ? 'danger' : 'secondary')); 
                                          ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $credit->status)); ?>
                                        </span>
                                        <span class="text-muted ms-2 fs-12">
                                            <?php echo date('M d, Y', strtotime($credit->application_date)); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Agrovet Information -->
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
                    <div class="card custom-card mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="avatar avatar-md avatar-rounded" style="background-color: #6AA32D;">
                                        <i class="ri-store-2-line fs-16"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted fs-12">Agrovet</p>
                                    <h5 class="fw-semibold mb-1"><?php echo htmlspecialchars($credit->agrovet_name); ?>
                                    </h5>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-light text-dark">
                                            <?php echo htmlspecialchars($credit->agrovet_type); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Amount & Terms -->
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
                    <div class="card custom-card mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="avatar avatar-md avatar-rounded bg-warning">
                                        <i class="ri-money-dollar-circle-line fs-16"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted fs-12">Input Amount</p>
                                    <h5 class="fw-semibold mb-1">KES
                                        <?php echo number_format($credit->total_amount, 2); ?></h5>
                                    <div class="d-flex align-items-center">
                                        <span class="text-dark fw-medium">
                                            <i class="ri-percent-line me-1"></i>
                                            <?php echo $credit->credit_percentage; ?>%
                                            Interest
                                        </span>
                                        <span class="ms-2 text-success fw-medium">
                                            <i class="ri-arrow-down-line me-1"></i>
                                            <?php echo $credit->repayment_percentage; ?>% Repayment
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Credit Score -->
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
                    <div class="card custom-card mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="avatar avatar-md avatar-rounded bg-<?php 
                    if ($credit->creditworthiness_score >= 80) echo 'success'; 
                    elseif ($credit->creditworthiness_score >= 60) echo 'warning';
                    else echo 'danger';
                ?>">
                                        <i class="ri-bar-chart-line fs-16"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted fs-12">Credit Score</p>
                                    <h5 class="fw-semibold mb-1">
                                        <?php echo number_format($credit->creditworthiness_score, 1); ?>
                                    </h5>
                                    <div class="text-muted fs-12">
                                        <?php 
                        if ($credit->creditworthiness_score >= 80) echo "Excellent";
                        elseif ($credit->creditworthiness_score >= 70) echo "Good";
                        elseif ($credit->creditworthiness_score >= 60) echo "Fair";
                        else echo "Poor";
                    ?>
                                        <span class="ms-2">
                                            <i class="ri-information-line" data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                title="Based on repayment history, financial obligations, and produce delivery history"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Row 2: Farmer Information Cards -->
            <div class="row">
                <!-- Card 1: Farmer Profile -->
                <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                    <div class="card custom-card mb-4">
                        <div class="card-header border-bottom-0">
                            <div class="card-title">
                                <i class="ri-user-line me-2"></i> Farmer Information
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-4">
                                    <?php if(!empty($credit->profile_picture) && file_exists($credit->profile_picture)): ?>
                                    <img src="<?php echo $credit->profile_picture; ?>"
                                        class="avatar avatar-xl avatar-rounded" alt="Farmer Profile">
                                    <?php else: ?>
                                    <span class="avatar avatar-xl avatar-rounded bg-primary">
                                        <?php echo strtoupper(substr($credit->farmer_name, 0, 1)); ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h5 class="mb-1"><?php echo htmlspecialchars($credit->farmer_name); ?></h5>
                                    <p class="mb-1">
                                        <i class="ri-fingerprint-line me-1 text-muted"></i>
                                        <span class="text-muted">Registration: </span>
                                        <span
                                            class="fw-medium"><?php echo htmlspecialchars($credit->farmer_reg); ?></span>
                                    </p>
                                    <p class="mb-1">
                                        <i class="ri-phone-line me-1 text-muted"></i>
                                        <span class="text-muted">Phone: </span>
                                        <span
                                            class="fw-medium"><?php echo htmlspecialchars($credit->farmer_phone); ?></span>
                                    </p>
                                    <p class="mb-0">
                                        <i class="ri-mail-line me-1 text-muted"></i>
                                        <span class="text-muted">Email: </span>
                                        <span
                                            class="fw-medium"><?php echo htmlspecialchars($credit->farmer_email); ?></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Farming Activity Summary -->
                <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                    <div class="card custom-card mb-4">
                        <div class="card-header border-bottom-0">
                            <div class="card-title">
                                <i class="ri-plant-line me-2"></i> Farming Activity
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row">
                                <?php
                                      // Get farm count
                                      $query = "SELECT COUNT(*) as farm_count FROM farms WHERE farmer_id = :farmer_id";
                                      $farmCount = $app->selectOne($query, [':farmer_id' => $credit->farmer_id]);
                                      
                                      // Get total farm size
                                      $query = "SELECT COALESCE(SUM(size), 0) as total_size FROM farms WHERE farmer_id = :farmer_id";
                                      $farmSize = $app->selectOne($query, [':farmer_id' => $credit->farmer_id]);
                                      
                                      // Get produce sales value
                                      $query = "SELECT 
                                                  COALESCE(SUM(pd.total_value), 0) as total_sales 
                                                FROM produce_deliveries pd
                                                JOIN farm_products fp ON pd.farm_product_id = fp.id
                                                JOIN farms f ON fp.farm_id = f.id
                                                WHERE f.farmer_id = :farmer_id AND pd.status = 'sold'";
                                      $sales = $app->selectOne($query, [':farmer_id' => $credit->farmer_id]);
                                      
                                      // Get unique products
                                      $query = "SELECT COUNT(DISTINCT pt.id) as product_count
                                                FROM farm_products fp
                                                JOIN product_types pt ON fp.product_type_id = pt.id
                                                JOIN farms f ON fp.farm_id = f.id
                                                WHERE f.farmer_id = :farmer_id";
                                      $products = $app->selectOne($query, [':farmer_id' => $credit->farmer_id]);
                                      ?>

                                <!-- Farm Count Card -->
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                    <div class="p-3">
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-sm bg-secondary me-2">
                                                <i class="ri-home-4-line"></i>
                                            </span>
                                            <div>
                                                <p class="fs-12 mb-0 text-muted">Farms</p>
                                                <h6 class="fw-semibold mb-0"><?php echo $farmCount->farm_count; ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Farm Size Card -->
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                    <div class="p-3">
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-sm bg-success me-2">
                                                <i class="ri-ruler-line"></i>
                                            </span>
                                            <div>
                                                <p class="fs-12 mb-0 text-muted">Total Area</p>
                                                <h6 class="fw-semibold mb-0">
                                                    <?php echo number_format($farmSize->total_size, 2); ?> Acres</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sales Value Card -->
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                    <div class="p-3">
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-sm bg-warning me-2">
                                                <i class="ri-money-dollar-circle-line"></i>
                                            </span>
                                            <div>
                                                <p class="fs-12 mb-0 text-muted">Total Sales</p>
                                                <h6 class="fw-semibold mb-0">KES
                                                    <?php echo number_format($sales->total_sales, 2); ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Product Types Card -->
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                    <div class="p-3">
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-sm bg-info me-2">
                                                <i class="ri-seedling-line"></i>
                                            </span>
                                            <div>
                                                <p class="fs-12 mb-0 text-muted">Product Types</p>
                                                <h6 class="fw-semibold mb-0"><?php echo $products->product_count; ?>
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Farmer Category -->
                                <div class="col-12 mt-2">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <span class="badge bg-primary-transparent text-primary">
                                            <i class="ri-user-star-line me-1"></i>
                                            <?php echo $credit->category_name ?? 'Uncategorized'; ?> Farmer
                                        </span>

                                        <span class="badge bg-success-transparent text-success ms-2">
                                            <i class="ri-funds-line me-1"></i>
                                            <?php echo ($credit->creditworthiness_score >= 70) ? 'Low Risk' : (($credit->creditworthiness_score >= 60) ? 'Medium Risk' : 'High Risk'); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Credit Purpose Card -->
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="card custom-card mb-4">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="ri-focus-3-line me-2"></i> Input Credit Details
                            </div>
                        </div>
                        <div class="card-body">
                            <?php
                               // Get application credit items
                               $query = "SELECT 
                                           ici.id,
                                           ici.input_type,
                                           ici.input_name,
                                           ici.quantity,
                                           ici.unit,
                                           ici.unit_price,
                                           ici.total_price,
                                           ici.description
                                         FROM input_credit_items ici
                                         WHERE ici.credit_application_id ='{$credit->id}'
                                         ORDER BY ici.input_type, ici.input_name";
                                         
                               $creditItems = $app->select_all($query);
                               ?>

                            <?php if ($creditItems && count($creditItems) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th scope="col" width="5%">#</th>
                                            <th scope="col" width="15%">Type</th>
                                            <th scope="col" width="25%">Input Item</th>
                                            <th scope="col" width="10%">Quantity</th>
                                            <th scope="col" width="15%">Unit Price</th>
                                            <th scope="col" width="20%">Total Price</th>
                                            <th scope="col" width="10%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                            $count = 1;
                            $totalAmount = 0;
                            foreach ($creditItems as $item): 
                                $totalAmount += $item->total_price;
                            ?>
                                        <tr>
                                            <td><?php echo $count++; ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                        switch($item->input_type) {
                                            case 'fertilizer': echo 'success'; break;
                                            case 'pesticide': echo 'danger'; break;
                                            case 'seeds': echo 'warning'; break;
                                            case 'tools': echo 'info'; break;
                                            default: echo 'secondary';
                                        }
                                    ?>">
                                                    <?php echo ucfirst($item->input_type); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar avatar-xs bg-light me-2">
                                                        <i class="ri-<?php 
                                                switch($item->input_type) {
                                                    case 'fertilizer': echo 'leaf'; break;
                                                    case 'pesticide': echo 'medicine-bottle'; break;
                                                    case 'seeds': echo 'seedling'; break;
                                                    case 'tools': echo 'tools'; break;
                                                    default: echo 'box';
                                                }
                                            ?>-line"></i>
                                                    </span>
                                                    <?php echo htmlspecialchars($item->input_name); ?>
                                                </div>
                                            </td>
                                            <td><?php echo $item->quantity . ' ' . $item->unit; ?></td>
                                            <td>KES <?php echo number_format($item->unit_price, 2); ?></td>
                                            <td class="fw-medium">KES
                                                <?php echo number_format($item->total_price, 2); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary"
                                                    data-bs-toggle="tooltip"
                                                    title="<?php echo htmlspecialchars($item->description ?: 'No description available'); ?>">
                                                    <i class="ri-information-line"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot class="bg-light">
                                        <tr>
                                            <td colspan="5" class="text-end fw-bold">Total:</td>
                                            <td class="fw-bold">KES <?php echo number_format($totalAmount, 2); ?></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="text-end fw-bold">Interest
                                                (<?php echo $credit->credit_percentage; ?>%):</td>
                                            <td class="fw-bold">KES
                                                <?php echo number_format($totalAmount * ($credit->credit_percentage / 100), 2); ?>
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="text-end fw-bold">Total with Interest:</td>
                                            <td class="fw-bold">KES
                                                <?php echo number_format($credit->total_with_interest, 2); ?></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="ri-alert-line me-2"></i> No input items found for this credit application.
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Row 3: Activity History & Logs -->
            <!-- Row 3: Farmer's Active Loans -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card custom-card">
                        <div class="card-header justify-content-between">
                            <div class="card-title">
                                <i class="ri-bank-card-line me-2"></i> Active Loans
                            </div>
                        </div>
                        <div class="card-body">
                            <?php
                                // Get farmer's active loans
                                $query = "SELECT 
                                            la.id as application_id,
                                            al.id as loan_id,
                                            lt.name as loan_type,
                                            la.application_date,
                                            al.approved_amount,
                                            al.interest_rate,
                                            al.approved_term,
                                            al.remaining_balance,
                                            al.disbursement_date,
                                            al.expected_completion_date,
                                            al.status
                                          FROM approved_loans al
                                          JOIN loan_applications la ON al.loan_application_id = la.id
                                          JOIN loan_types lt ON la.loan_type_id = lt.id
                                          WHERE la.farmer_id =  $credit->farmer_id AND al.status IN ('pending_disbursement', 'active')
                                          ORDER BY la.application_date DESC";
                                          
                                $activeLoans = $app->select_all($query);
                                ?>

                            <?php if ($activeLoans && count($activeLoans) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th><i class="ri-hash-line me-1"></i>Reference</th>
                                            <th><i class="ri-bank-line me-1"></i>Loan Type</th>
                                            <th><i class="ri-money-dollar-circle-line me-1"></i>Amount</th>
                                            <th><i class="ri-money-dollar-circle-line me-1"></i>Balance</th>
                                            <th><i class="ri-percent-line me-1"></i>Interest</th>
                                            <th><i class="ri-calendar-line me-1"></i>Disbursed On</th>
                                            <th><i class="ri-calendar-check-line me-1"></i>Expected Completion</th>
                                            <th><i class="ri-information-line me-1"></i>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($activeLoans as $activeLoan): ?>
                                        <tr>
                                            <td class="fw-semibold">
                                                LOAN<?php echo str_pad($activeLoan->application_id, 5, '0', STR_PAD_LEFT); ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($activeLoan->loan_type); ?></td>
                                            <td>
                                                KES <?php echo number_format($activeLoan->approved_amount, 2); ?>
                                            </td>
                                            <td>
                                                <span
                                                    class="fw-semibold text-<?php echo ($activeLoan->remaining_balance > 0) ? 'danger' : 'success'; ?>">
                                                    KES <?php echo number_format($activeLoan->remaining_balance, 2); ?>
                                                </span>
                                            </td>
                                            <td><?php echo $activeLoan->interest_rate; ?>%</td>
                                            <td><?php echo date('M d, Y', strtotime($activeLoan->disbursement_date)); ?>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($activeLoan->expected_completion_date)); ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php 
                                        echo ($activeLoan->status == 'active') ? 'primary' : 'warning'; 
                                    ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $activeLoan->status)); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-4">
                                <span class="avatar avatar-lg bg-light-transparent text-primary mb-3">
                                    <i class="ri-bank-card-line fs-1"></i>
                                </span>
                                <h6 class="fw-semibold">No Active Loans</h6>
                                <p class="text-muted mb-0">This farmer doesn't have any active loans at the moment.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 4: Input Credits -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card custom-card">
                        <div class="card-header justify-content-between">
                            <div class="card-title">
                                <i class="ri-shopping-bag-line me-2"></i> Input Credits
                            </div>
                        </div>
                        <div class="card-body">
                            <?php
                                   // Get farmer's active input credits
                                   $query = "SELECT 
                                               ica.id as application_id,
                                               aic.id as credit_id,
                                               ica.agrovet_id,
                                               a.name as agrovet_name,
                                               aic.approved_amount,
                                               aic.credit_percentage,
                                               aic.total_with_interest,
                                               aic.remaining_balance,
                                               aic.fulfillment_date,
                                               aic.status
                                             FROM approved_input_credits aic
                                             JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                             JOIN agrovets a ON ica.agrovet_id = a.id
                                             WHERE ica.farmer_id = '{$credit->farmer_id}' AND aic.status IN ('pending_fulfillment', 'active')
                                             ORDER BY ica.application_date DESC";
                                             
                                   $activeCredits = $app->select_all($query);
                                   ?>

                            <?php if ($activeCredits && count($activeCredits) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th><i class="ri-hash-line me-1"></i>Reference</th>
                                            <th><i class="ri-store-line me-1"></i>Agrovet</th>
                                            <th><i class="ri-money-dollar-circle-line me-1"></i>Amount</th>
                                            <th><i class="ri-money-dollar-circle-line me-1"></i>Balance</th>
                                            <th><i class="ri-percent-line me-1"></i>Interest</th>
                                            <th><i class="ri-calendar-line me-1"></i>Fulfillment Date</th>
                                            <th><i class="ri-information-line me-1"></i>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($activeCredits as $credit): ?>
                                        <tr>
                                            <td class="fw-semibold">
                                                CRED<?php echo str_pad($credit->application_id, 5, '0', STR_PAD_LEFT); ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($credit->agrovet_name); ?></td>
                                            <td>
                                                KES <?php echo number_format($credit->approved_amount, 2); ?>
                                            </td>
                                            <td>
                                                <span
                                                    class="fw-semibold text-<?php echo ($credit->remaining_balance > 0) ? 'danger' : 'success'; ?>">
                                                    KES <?php echo number_format($credit->remaining_balance, 2); ?>
                                                </span>
                                            </td>
                                            <td><?php echo $credit->credit_percentage; ?>%</td>
                                            <td><?php echo date('M d, Y', strtotime($credit->fulfillment_date)); ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                        if ($credit->status == 'active') echo 'primary';
                                        elseif ($credit->status == 'pending_fulfillment') echo 'warning';
                                        else echo 'secondary';
                                    ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $credit->status)); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-4">
                                <span class="avatar avatar-lg bg-light-transparent text-success mb-3">
                                    <i class="ri-shopping-bag-line fs-1"></i>
                                </span>
                                <h6 class="fw-semibold">No Active Input Credits</h6>
                                <p class="text-muted mb-0">This farmer doesn't have any active input credits at the
                                    moment.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Row 5: Input Credit History -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card custom-card">
                        <div class="card-header justify-content-between">
                            <div class="card-title">
                                <i class="ri-history-line me-2"></i> Input Credit History
                            </div>
                        </div>
                        <div class="card-body">
                            <?php
                             // Check if ID is provided
                     if (!isset($_GET['id']) || empty($_GET['id'])) {
                         echo '<div class="alert alert-danger">No input credit application ID specified</div>';
                         exit;
                     }
                     
                     $app = new App;
                     $creditId = intval($_GET['id']);
                     $userId = $_SESSION['user_id'];
                     
                     // Fetch the staff information to get agrovet_id
                     $staffQuery = "SELECT s.id as staff_id, s.agrovet_id 
                                   FROM agrovet_staff s 
                                   WHERE s.user_id = :user_id";
                     
                     $staff = $app->selectOne($staffQuery, [':user_id' => $userId]);
                     
                     if (!$staff) {
                         echo '<div class="alert alert-danger">Staff information not found</div>';
                         exit;
                     }
                     
                     // Fetch the input credit application details
                     $query = "SELECT 
                                 ica.id,
                                 ica.farmer_id,
                                 ica.agrovet_id,
                                 ica.total_amount,
                                 ica.credit_percentage,
                                 ica.total_with_interest,
                                 ica.repayment_percentage,
                                 ica.application_date,
                                 ica.creditworthiness_score,
                                 ica.status,
                                 a.name as agrovet_name,
                                 a.type_id as agrovet_type_id,
                                 at.name as agrovet_type,
                                 CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                                 u.phone as farmer_phone,
                                 u.email as farmer_email,
                                 u.profile_picture,
                                 fm.registration_number as farmer_reg,
                                 fm.category_id,
                                 fc.name as category_name
                               FROM input_credit_applications ica
                               JOIN agrovets a ON ica.agrovet_id = a.id
                               JOIN agrovet_types at ON a.type_id = at.id
                               JOIN farmers fm ON ica.farmer_id = fm.id
                               JOIN users u ON fm.user_id = u.id
                               LEFT JOIN farmer_categories fc ON fm.category_id = fc.id
                               WHERE ica.id = :credit_id AND ica.agrovet_id = :agrovet_id";
                     
                     $params = [
                         ':credit_id' => $creditId,
                         ':agrovet_id' => $staff->agrovet_id
                     ];
                     
                     $credit = $app->selectOne($query, $params);
                // Get farmer's past input credits (both active and completed)
                $query = "SELECT 
                            ica.id as application_id,
                            aic.id as credit_id,
                            ica.agrovet_id,
                            a.name as agrovet_name,
                            aic.approved_amount,
                            aic.credit_percentage,
                            aic.total_with_interest,
                            aic.remaining_balance,
                            aic.fulfillment_date,
                            aic.status,
                            (aic.total_with_interest - aic.remaining_balance) as amount_paid,
                            ROUND(((aic.total_with_interest - aic.remaining_balance) / aic.total_with_interest) * 100, 1) as payment_percentage
                          FROM approved_input_credits aic
                          JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                          JOIN agrovets a ON ica.agrovet_id = a.id
                          WHERE ica.farmer_id = '{$credit->farmer_id}'
                          ORDER BY aic.status ASC, aic.fulfillment_date DESC";
                          
                $creditHistory = $app->select_all($query);
                ?>

                            <?php if ($creditHistory && count($creditHistory) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap">
                                    <thead class="bg-light">
                                        <tr>
                                            <th><i class="ri-hash-line me-1"></i>Reference</th>
                                            <th><i class="ri-store-2-line me-1"></i>Agrovet</th>
                                            <th><i class="ri-money-dollar-circle-line me-1"></i>Amount</th>
                                            <th><i class="ri-percent-line me-1"></i>Interest</th>
                                            <th><i class="ri-coins-line me-1"></i>Total with Interest</th>
                                            <th><i class="ri-money-dollar-circle-line me-1"></i>Remaining</th>
                                            <th><i class="ri-bar-chart-line me-1"></i>Repayment</th>
                                            <th><i class="ri-calendar-line me-1"></i>Fulfillment Date</th>
                                            <th><i class="ri-information-line me-1"></i>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($creditHistory as $history): ?>
                                        <tr>
                                            <td class="fw-semibold">
                                                ICREDIT<?php echo str_pad($history->application_id, 5, '0', STR_PAD_LEFT); ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($history->agrovet_name); ?></td>
                                            <td>KES <?php echo number_format($history->approved_amount, 2); ?></td>
                                            <td><?php echo $history->credit_percentage; ?>%</td>
                                            <td>KES <?php echo number_format($history->total_with_interest, 2); ?></td>
                                            <td>
                                                <span
                                                    class="fw-semibold text-<?php echo ($history->remaining_balance > 0) ? 'danger' : 'success'; ?>">
                                                    KES <?php echo number_format($history->remaining_balance, 2); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress progress-sm flex-fill me-2"
                                                        style="height: 6px;">
                                                        <div class="progress-bar bg-<?php 
                                                if ($history->payment_percentage >= 90) echo 'success';
                                                elseif ($history->payment_percentage >= 60) echo 'info';
                                                elseif ($history->payment_percentage >= 30) echo 'warning';
                                                else echo 'danger';
                                            ?>" role="progressbar"
                                                            style="width: <?php echo $history->payment_percentage; ?>%"
                                                            aria-valuenow="<?php echo $history->payment_percentage; ?>"
                                                            aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <span
                                                        class="fs-12"><?php echo $history->payment_percentage; ?>%</span>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($history->fulfillment_date): ?>
                                                <?php echo date('M d, Y', strtotime($history->fulfillment_date)); ?>
                                                <?php else: ?>
                                                <span class="text-muted">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php 
                                        if ($history->status == 'completed') echo 'success';
                                        elseif ($history->status == 'active') echo 'primary';
                                        elseif ($history->status == 'pending_fulfillment') echo 'warning';
                                        elseif ($history->status == 'defaulted') echo 'danger';
                                        else echo 'secondary';
                                    ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $history->status)); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Summary Statistics for Input Credit History -->
                            <div class="row mt-4">
                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                                    <div class="card custom-card bg-primary-transparent mb-0">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <span class="avatar avatar-sm bg-primary">
                                                        <i class="ri-shopping-basket-line"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <p class="mb-0 text-muted fs-12">Total Credits</p>
                                                    <h5 class="fw-semibold mb-0">
                                                        <?php echo count($creditHistory); ?>
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                                    <div class="card custom-card bg-success-transparent mb-0">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <span class="avatar avatar-sm bg-success">
                                                        <i class="ri-check-line"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <p class="mb-0 text-muted fs-12">Completed Credits</p>
                                                    <?php
                                        $completedCount = 0;
                                        foreach ($creditHistory as $history) {
                                            if ($history->status == 'completed') {
                                                $completedCount++;
                                            }
                                        }
                                        ?>
                                                    <h5 class="fw-semibold mb-0">
                                                        <?php echo $completedCount; ?>
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                                    <div class="card custom-card bg-warning-transparent mb-0">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <span class="avatar avatar-sm bg-warning">
                                                        <i class="ri-time-line"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <p class="mb-0 text-muted fs-12">Active Credits</p>
                                                    <?php
                                        $activeCount = 0;
                                        foreach ($creditHistory as $history) {
                                            if ($history->status == 'active' || $history->status == 'pending_fulfillment') {
                                                $activeCount++;
                                            }
                                        }
                                        ?>
                                                    <h5 class="fw-semibold mb-0">
                                                        <?php echo $activeCount; ?>
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                                    <div class="card custom-card bg-danger-transparent mb-0">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <span class="avatar avatar-sm bg-danger">
                                                        <i class="ri-close-line"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <p class="mb-0 text-muted fs-12">Defaulted Credits</p>
                                                    <?php
                                        $defaultedCount = 0;
                                        foreach ($creditHistory as $history) {
                                            if ($history->status == 'defaulted') {
                                                $defaultedCount++;
                                            }
                                        }
                                        ?>
                                                    <h5 class="fw-semibold mb-0">
                                                        <?php echo $defaultedCount; ?>
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php else: ?>
                            <div class="text-center py-4">
                                <span class="avatar avatar-lg bg-light-transparent text-warning mb-3">
                                    <i class="ri-shopping-basket-line fs-1"></i>
                                </span>
                                <h6 class="fw-semibold">No Input Credit History</h6>
                                <p class="text-muted mb-0">This farmer hasn't taken any input credits previously.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 5: Recent Produce Sales -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card custom-card">
                        <div class="card-header justify-content-between">
                            <div class="card-title">
                                <i class="ri-leaf-line me-2"></i> Recent Produce Sales
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Quick Stats Cards for Produce -->
                            <div class="row mb-4">
                                <?php
                                  // Get total produce stats
                                  $query = "SELECT 
                                              COUNT(pd.id) as total_deliveries,
                                              COALESCE(SUM(pd.quantity), 0) as total_quantity,
                                              COALESCE(SUM(pd.total_value), 0) as total_value,
                                              COUNT(DISTINCT fp.product_type_id) as product_types
                                            FROM produce_deliveries pd
                                            JOIN farm_products fp ON pd.farm_product_id = fp.id
                                            JOIN farms f ON fp.farm_id = f.id
                                            WHERE f.farmer_id = :farmer_id AND pd.status = 'sold'";
                                            
                                  $produceStats = $app->selectOne($query, [':farmer_id' => $credit->farmer_id]);
                                  ?>

                                <!-- Total Deliveries -->
                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                                    <div class="card custom-card bg-primary-transparent mb-0">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <span class="avatar avatar-sm bg-primary">
                                                        <i class="ri-truck-line"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <p class="mb-0 text-muted fs-12">Total Deliveries</p>
                                                    <h5 class="fw-semibold mb-0">
                                                        <?php echo number_format($produceStats->total_deliveries); ?>
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Total Quantity -->
                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                                    <div class="card custom-card bg-success-transparent mb-0">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <span class="avatar avatar-sm bg-success">
                                                        <i class="ri-scales-3-line"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <p class="mb-0 text-muted fs-12">Total Quantity</p>
                                                    <h5 class="fw-semibold mb-0">
                                                        <?php echo number_format($produceStats->total_quantity, 2); ?>
                                                        KGs</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Total Sales Value -->
                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                                    <div class="card custom-card bg-warning-transparent mb-0">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <span class="avatar avatar-sm bg-warning">
                                                        <i class="ri-money-dollar-circle-line"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <p class="mb-0 text-muted fs-12">Total Sales Value</p>
                                                    <h5 class="fw-semibold mb-0">KES
                                                        <?php echo number_format($produceStats->total_value, 2); ?></h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Product Types -->
                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                                    <div class="card custom-card bg-info-transparent mb-0">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <span class="avatar avatar-sm bg-info">
                                                        <i class="ri-shopping-basket-line"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <p class="mb-0 text-muted fs-12">Product Types</p>
                                                    <h5 class="fw-semibold mb-0">
                                                        <?php echo number_format($produceStats->product_types); ?></h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php
                                  // Get farmer's recent produce sales (last 5)
                                  $query = "SELECT 
                                              pd.id,
                                              pd.quantity,
                                              pd.unit_price,
                                              pd.total_value,
                                              pd.quality_grade,
                                              pd.delivery_date,
                                              pt.name as product_name,
                                              f.name as farm_name,
                                              pd.notes,
                                              pd.created_at
                                            FROM produce_deliveries pd
                                            JOIN farm_products fp ON pd.farm_product_id = fp.id
                                            JOIN product_types pt ON fp.product_type_id = pt.id
                                            JOIN farms f ON fp.farm_id = f.id
                                            WHERE f.farmer_id = '{$credit->farmer_id}' AND pd.status = 'sold'
                                            ORDER BY pd.delivery_date DESC
                                            LIMIT 5";
                                            
                                  $recentProduce = $app->select_all($query);
                                  ?>

                            <?php if ($recentProduce && count($recentProduce) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th><i class="ri-hash-line me-1"></i>Reference</th>
                                            <th><i class="ri-plant-line me-1"></i>Product</th>
                                            <th><i class="ri-home-4-line me-1"></i>Farm</th>
                                            <th><i class="ri-scales-3-line me-1"></i>Quantity (KGs)</th>
                                            <th><i class="ri-money-dollar-circle-line me-1"></i>Unit Price</th>
                                            <th><i class="ri-money-dollar-circle-line me-1"></i>Total Value</th>
                                            <th><i class="ri-award-line me-1"></i>Quality</th>
                                            <th><i class="ri-calendar-line me-1"></i>Delivery Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentProduce as $produce): ?>
                                        <tr>
                                            <td class="fw-semibold">
                                                DLVR<?php echo str_pad($produce->id, 5, '0', STR_PAD_LEFT); ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($produce->product_name); ?></td>
                                            <td><?php echo htmlspecialchars($produce->farm_name); ?></td>
                                            <td><?php echo number_format($produce->quantity, 2); ?></td>
                                            <td>KES <?php echo number_format($produce->unit_price, 2); ?></td>
                                            <td class="fw-semibold">KES
                                                <?php echo number_format($produce->total_value, 2); ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                        echo ($produce->quality_grade == 'A') ? 'success' : 
                                            (($produce->quality_grade == 'B') ? 'warning' : 'danger'); 
                                    ?>">
                                                    Grade <?php echo $produce->quality_grade; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($produce->delivery_date)); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-4">
                                <span class="avatar avatar-lg bg-light-transparent text-warning mb-3">
                                    <i class="ri-leaf-line fs-1"></i>
                                </span>
                                <h6 class="fw-semibold">No Produce Sales Found</h6>
                                <p class="text-muted mb-0">This farmer hasn't sold any produce yet.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approve Input Credit Modal -->
            <div class="modal fade" id="approveCreditModal" tabindex="-1" aria-labelledby="approveCreditModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="approveCreditModalLabel">
                                <i class="ri-check-line me-1 text-success"></i> Approve Input Credit Application
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="approveCreditForm">
                                <input type="hidden" id="creditApplicationId" value="<?php echo $credit->id; ?>">

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="approvedAmount" class="form-label">Approved Amount (KES) *</label>
                                        <input type="number" class="form-control" id="approvedAmount"
                                            value="<?php echo $credit->total_amount; ?>" required>
                                        <small class="text-muted">Requested: KES
                                            <?php echo number_format($credit->total_amount, 2); ?></small>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="creditPercentage" class="form-label">Interest Rate (%) *</label>
                                        <input type="number" step="0.01" class="form-control" id="creditPercentage"
                                            value="<?php echo $credit->credit_percentage; ?>" required>
                                        <small class="text-muted">Requested:
                                            <?php echo $credit->credit_percentage; ?>%</small>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="repaymentPercentage" class="form-label">Repayment Percentage (%)
                                            *</label>
                                        <input type="number" step="0.01" class="form-control" id="repaymentPercentage"
                                            value="<?php echo $credit->repayment_percentage; ?>" required>
                                        <small class="text-muted">% of produce sales to be deducted for credit
                                            repayment</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="fulfillmentDate" class="form-label">Expected Fulfillment Date
                                            *</label>
                                        <?php 
                            // Calculate fulfillment date (default to tomorrow)
                            $fulfillmentDate = date('Y-m-d', strtotime('+1 day'));
                            ?>
                                        <input type="date" class="form-control" id="fulfillmentDate"
                                            value="<?php echo $fulfillmentDate; ?>" required>
                                        <small class="text-muted">When inputs will be provided to the farmer</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="approvalNotes" class="form-label">Approval Notes</label>
                                    <textarea class="form-control" id="approvalNotes" rows="3"
                                        placeholder="Enter any additional notes about this input credit approval"></textarea>
                                </div>

                                <div class="alert alert-info" role="alert">
                                    <i class="ri-information-line me-1"></i>
                                    <strong>Total Repayment:</strong> <span id="totalRepayment">KES
                                        <?php echo number_format($credit->total_with_interest, 2); ?></span>
                                    <small class="d-block mt-1">This includes the input cost plus interest</small>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-success" onclick="processCreditApproval()">
                                <i class="ri-check-line me-1"></i> Confirm Approval
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reject Input Credit Modal -->
            <div class="modal fade" id="rejectCreditModal" tabindex="-1" aria-labelledby="rejectCreditModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="rejectCreditModalLabel">
                                <i class="ri-close-line me-1 text-danger"></i> Reject Input Credit Application
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="rejectCreditForm">
                                <input type="hidden" id="rejectCreditId" value="<?php echo $credit->id; ?>">

                                <div class="mb-3">
                                    <label for="rejectionReason" class="form-label">Reason for Rejection *</label>
                                    <textarea class="form-control" id="rejectionReason" rows="4"
                                        placeholder="Please provide a detailed reason for rejecting this input credit application"
                                        required></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="rejectionCategory" class="form-label">Rejection Category *</label>
                                    <select class="form-select" id="rejectionCategory" required>
                                        <option value="">-- Select Category --</option>
                                        <option value="insufficient_creditworthiness">Insufficient Creditworthiness
                                        </option>
                                        <option value="excessive_debt">Excessive Existing Debt</option>
                                        <option value="improper_input_selection">Improper Input Selection</option>
                                        <option value="incomplete_documentation">Incomplete Documentation</option>
                                        <option value="insufficient_produce_history">Insufficient Produce History
                                        </option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="recommendationNotes" class="form-label">Recommendations</label>
                                    <textarea class="form-control" id="recommendationNotes" rows="3"
                                        placeholder="Enter any recommendations for the farmer to improve future applications"></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" onclick="processCreditRejection()">
                                <i class="ri-close-line me-1"></i> Confirm Rejection
                            </button>
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
    <!-- JavaScript to calculate total repayment -->
    <!-- JavaScript for Input Credit Review Page -->
    <script>
    $(document).ready(function() {
        // Calculate total repayment when interest rate or amount changes
        $('#approvedAmount, #creditPercentage').on('input', function() {
            calculateTotalRepayment();
        });

        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();
    });

    function calculateTotalRepayment() {
        let amount = parseFloat($('#approvedAmount').val()) || 0;
        let interestRate = parseFloat($('#creditPercentage').val()) || 0;
        let total = amount * (1 + (interestRate / 100));

        $('#totalRepayment').text('KES ' + total.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));
    }

    // Function to process input credit approval
    function processCreditApproval() {
        // Get form values
        let creditApplicationId = $('#creditApplicationId').val();
        let approvedAmount = $('#approvedAmount').val();
        let creditPercentage = $('#creditPercentage').val();
        let repaymentPercentage = $('#repaymentPercentage').val();
        let fulfillmentDate = $('#fulfillmentDate').val();
        let approvalNotes = $('#approvalNotes').val();

        // Validate form fields
        if (!approvedAmount || !creditPercentage || !repaymentPercentage || !fulfillmentDate) {
            toastr.error('Please fill in all required fields', 'Error', {
                "positionClass": "toast-top-right",
                "progressBar": true,
                "timeOut": 3000,
                "extendedTimeOut": 1000,
                "hideMethod": "fadeOut"
            });
            return;
        }

        // Calculate total with interest
        let totalWithInterest = parseFloat(approvedAmount) * (1 + (parseFloat(creditPercentage) / 100));

        // Show loading message
        toastr.info('Processing your request...', 'Please wait', {
            "positionClass": "toast-top-right",
            "progressBar": true,
            "timeOut": 0,
            "extendedTimeOut": 0,
            "closeButton": false,
            "hideMethod": "fadeOut"
        });

        // Send AJAX request directly without confirmation
        $.ajax({
            url: "http://localhost/dfcs/ajax/input-credit-controller/approve-credit.php",
            type: "POST",
            data: {
                creditApplicationId: creditApplicationId,
                approvedAmount: approvedAmount,
                creditPercentage: creditPercentage,
                totalWithInterest: totalWithInterest,
                repaymentPercentage: repaymentPercentage,
                fulfillmentDate: fulfillmentDate,
                approvalNotes: approvalNotes
            },
            success: function(response) {
                // Clear the loading message
                toastr.clear();

                // Handle response (could be JSON string or already parsed)
                let result;

                if (typeof response === 'string') {
                    try {
                        result = JSON.parse(response);
                    } catch (e) {
                        console.error('Invalid JSON response:', response);
                        // Show raw response for debugging
                        toastr.error('Server returned invalid data: ' + response.substring(0, 100),
                            'Error', {
                                "positionClass": "toast-top-right",
                                "progressBar": true,
                                "timeOut": 5000
                            });
                        return;
                    }
                } else {
                    result = response;
                }

                // Check success status
                if (result && result.success) {
                    // Hide the modal
                    $('#approveCreditModal').modal('hide');

                    // Reset form
                    $('#approveCreditForm')[0].reset();

                    // Show success message
                    toastr.success(result.message ||
                        'Input credit application has been approved successfully.', 'Success', {
                            "positionClass": "toast-top-right",
                            "progressBar": true,
                            "timeOut": 3000,
                            "extendedTimeOut": 1000,
                            "hideMethod": "fadeOut",
                            "onHidden": function() {
                                // Redirect back to pending credits list
                                window.location.href = "new";
                            }
                        });
                } else {
                    // Show error message
                    toastr.error(result.message || 'An error occurred while processing the approval.',
                        'Error', {
                            "positionClass": "toast-top-right",
                            "progressBar": true,
                            "timeOut": 5000,
                            "extendedTimeOut": 1000,
                            "hideMethod": "fadeOut"
                        });
                }
            },
            error: function(xhr, status, error) {
                toastr.clear();
                toastr.error('An error occurred while processing the approval. Please try again.',
                    'Error', {
                        "positionClass": "toast-top-right",
                        "progressBar": true,
                        "timeOut": 5000
                    });
                console.error('Error approving credit:', error);
            }
        });
    }
    // Function to process input credit rejection
    function processCreditRejection() {
        // Get form values
        let creditId = $('#rejectCreditId').val();
        let rejectionReason = $('#rejectionReason').val();
        let rejectionCategory = $('#rejectionCategory').val();
        let recommendationNotes = $('#recommendationNotes').val();

        // Validate form fields
        if (!rejectionReason || !rejectionCategory) {
            toastr.error('Please provide a reason and category for rejection', 'Error', {
                "positionClass": "toast-top-right",
                "progressBar": true,
                "timeOut": 3000,
                "extendedTimeOut": 1000,
                "hideMethod": "fadeOut"
            });
            return;
        }

        // Show loading message
        toastr.info('Processing your request...', 'Please wait', {
            "positionClass": "toast-top-right",
            "progressBar": true,
            "timeOut": 0,
            "extendedTimeOut": 0,
            "closeButton": false,
            "hideMethod": "fadeOut"
        });

        // Send AJAX request directly without confirmation
        $.ajax({
            url: "http://localhost/dfcs/ajax/input-credit-controller/reject-credit.php",
            type: "POST",
            data: {
                creditId: creditId,
                rejectionReason: rejectionReason,
                rejectionCategory: rejectionCategory,
                recommendationNotes: recommendationNotes
            },
            success: function(response) {
                // Clear the loading message
                toastr.clear();

                // Handle response (could be JSON string or already parsed)
                let result;

                if (typeof response === 'string') {
                    try {
                        result = JSON.parse(response);
                    } catch (e) {
                        console.error('Invalid JSON response:', response);
                        // Show raw response for debugging
                        toastr.error('Server returned invalid data: ' + response.substring(0, 100),
                            'Error', {
                                "positionClass": "toast-top-right",
                                "progressBar": true,
                                "timeOut": 5000
                            });
                        return;
                    }
                } else {
                    result = response;
                }

                // Check success status
                if (result && result.success) {
                    // Hide the modal - make sure we're using jQuery properly
                    $('#rejectCreditModal').modal('hide');

                    // Reset form
                    $('#rejectCreditForm')[0].reset();

                    // Show success message
                    toastr.success(result.message ||
                        'Input credit application has been rejected successfully.', 'Success', {
                            "positionClass": "toast-top-right",
                            "progressBar": true,
                            "timeOut": 3000,
                            "extendedTimeOut": 1000,
                            "hideMethod": "fadeOut",
                            "onHidden": function() {
                                // Redirect back to pending credits list
                                window.location.href = "new";
                            }
                        });
                } else {
                    // Show error message
                    toastr.error(result.message || 'An error occurred while processing the rejection.',
                        'Error', {
                            "positionClass": "toast-top-right",
                            "progressBar": true,
                            "timeOut": 5000,
                            "extendedTimeOut": 1000,
                            "hideMethod": "fadeOut"
                        });
                }
            },
            error: function(xhr, status, error) {
                toastr.clear();
                toastr.error('An error occurred while rejecting the application. Please try again.',
                    'Error', {
                        "positionClass": "toast-top-right",
                        "progressBar": true,
                        "timeOut": 5000
                    });
                console.error('Error rejecting credit:', error);
            }
        });
    }
    </script>

</body>



</html>