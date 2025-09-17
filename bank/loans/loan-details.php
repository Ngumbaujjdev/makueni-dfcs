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
                           echo '<div class="alert alert-danger">No loan application ID specified</div>';
                           exit;
                       }
                       
                       $app = new App;
                       $loanId = intval($_GET['id']);
                       $userId = $_SESSION['user_id'];
                       
                       // Fetch the loan application details
                       $query = "SELECT 
                                   la.id,
                                   la.farmer_id,
                                   la.provider_type,
                                   la.loan_type_id,
                                   la.amount_requested,
                                   la.term_requested,
                                   la.purpose,
                                   la.application_date,
                                   la.creditworthiness_score,
                                   la.status,
                                   lt.name as loan_type,
                                   lt.interest_rate,
                                   lt.produce_value_percentage,
                                   CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                                   u.phone as farmer_phone,
                                   u.email as farmer_email,
                                   u.profile_picture,
                                   fm.registration_number as farmer_reg,
                                   fm.category_id,
                                   fc.name as category_name
                                 FROM loan_applications la
                                 JOIN loan_types lt ON la.loan_type_id = lt.id
                                 JOIN farmers fm ON la.farmer_id = fm.id
                                 JOIN users u ON fm.user_id = u.id
                                 LEFT JOIN farmer_categories fc ON fm.category_id = fc.id
                                 WHERE la.id = :loan_id";
                       
                       $params = [
                           ':loan_id' => $loanId
                       ];
                       
                       $loan = $app->selectOne($query, $params);
                       
                       // Check if the loan exists
                       if (!$loan) {
                           echo '<div class="alert alert-danger">Loan application not found</div>';
                           exit;
                       }
                       ?>


            <!-- Page Header with Download Button -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <h1 class="page-title fw-semibold fs-18 mb-0">Loan Application Details</h1>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-primary" id="downloadPDF">
                        <i class="ri-file-download-line me-1"></i> Download PDF
                    </button>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="javascript:history.back()">Loans</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Loan Details</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Row 1: Essential Loan Information Cards -->
            <div class="row">
                <!-- Card 1: Loan Reference -->
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
                    <div class="card custom-card mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="avatar avatar-md avatar-rounded bg-primary">
                                        <i class="ri-file-loan-line fs-16"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted fs-12">Reference</p>
                                    <h5 class="fw-semibold mb-1">
                                        LOAN<?php echo str_pad($loan->id, 5, '0', STR_PAD_LEFT); ?></h5>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-<?php 
                        echo ($loan->status == 'under_review') ? 'info' : 
                            (($loan->status == 'approved') ? 'success' : 
                            (($loan->status == 'rejected') ? 'danger' : 'secondary')); 
                    ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $loan->status)); ?>
                                        </span>
                                        <span class="text-muted ms-2 fs-12">
                                            <?php echo date('M d, Y', strtotime($loan->application_date)); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Loan Type -->
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
                    <div class="card custom-card mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="avatar avatar-md avatar-rounded" style="background-color: #6AA32D;">
                                        <i class="ri-bank-line fs-16"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted fs-12">Loan Type</p>
                                    <h5 class="fw-semibold mb-1"><?php echo htmlspecialchars($loan->loan_type); ?></h5>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-light text-dark">
                                            <?php echo ucfirst($loan->provider_type); ?>
                                        </span>
                                        <span class="text-muted ms-2 fs-12">
                                            <?php echo $loan->interest_rate; ?>% Interest
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Amount & Term -->
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
                                    <p class="mb-0 text-muted fs-12">Requested Amount</p>
                                    <h5 class="fw-semibold mb-1">KES
                                        <?php echo number_format($loan->amount_requested, 2); ?></h5>
                                    <div class="d-flex align-items-center">
                                        <span class="text-dark fw-medium">
                                            <i class="ri-calendar-line me-1"></i> <?php echo $loan->term_requested; ?>
                                            Months
                                        </span>
                                        <span class="ms-2 text-success fw-medium">
                                            <i class="ri-percent-line me-1"></i>
                                            <?php echo $loan->produce_value_percentage; ?>% of Produce
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
                            if ($loan->creditworthiness_score >= 80) echo 'success'; 
                            elseif ($loan->creditworthiness_score >= 60) echo 'warning';
                            else echo 'danger';
                        ?>">
                                        <i class="ri-bar-chart-line fs-16"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted fs-12">Credit Score</p>
                                    <h5 class="fw-semibold mb-1">
                                        <?php echo number_format($loan->creditworthiness_score, 1); ?>
                                    </h5>
                                    <div class="text-muted fs-12">
                                        <?php 
                                if ($loan->creditworthiness_score >= 80) echo "Excellent";
                                elseif ($loan->creditworthiness_score >= 70) echo "Good";
                                elseif ($loan->creditworthiness_score >= 60) echo "Fair";
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
                                    <?php if(!empty($loan->profile_picture) && file_exists($loan->profile_picture)): ?>
                                    <img src="<?php echo $loan->profile_picture; ?>"
                                        class="avatar avatar-xl avatar-rounded" alt="Farmer Profile">
                                    <?php else: ?>
                                    <span class="avatar avatar-xl avatar-rounded bg-primary">
                                        <?php echo strtoupper(substr($loan->farmer_name, 0, 1)); ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h5 class="mb-1"><?php echo htmlspecialchars($loan->farmer_name); ?></h5>
                                    <p class="mb-1">
                                        <i class="ri-fingerprint-line me-1 text-muted"></i>
                                        <span class="text-muted">Registration: </span>
                                        <span
                                            class="fw-medium"><?php echo htmlspecialchars($loan->farmer_reg); ?></span>
                                    </p>
                                    <p class="mb-1">
                                        <i class="ri-phone-line me-1 text-muted"></i>
                                        <span class="text-muted">Phone: </span>
                                        <span
                                            class="fw-medium"><?php echo htmlspecialchars($loan->farmer_phone); ?></span>
                                    </p>
                                    <p class="mb-0">
                                        <i class="ri-mail-line me-1 text-muted"></i>
                                        <span class="text-muted">Email: </span>
                                        <span
                                            class="fw-medium"><?php echo htmlspecialchars($loan->farmer_email); ?></span>
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
                    $farmCount = $app->selectOne($query, [':farmer_id' => $loan->farmer_id]);
                    
                    // Get total farm size
                    $query = "SELECT COALESCE(SUM(size), 0) as total_size FROM farms WHERE farmer_id = :farmer_id";
                    $farmSize = $app->selectOne($query, [':farmer_id' => $loan->farmer_id]);
                    
                    // Get produce sales value
                    $query = "SELECT 
                                COALESCE(SUM(pd.total_value), 0) as total_sales 
                              FROM produce_deliveries pd
                              JOIN farm_products fp ON pd.farm_product_id = fp.id
                              JOIN farms f ON fp.farm_id = f.id
                              WHERE f.farmer_id = :farmer_id AND pd.status = 'sold'";
                    $sales = $app->selectOne($query, [':farmer_id' => $loan->farmer_id]);
                    
                    // Get unique products
                    $query = "SELECT COUNT(DISTINCT pt.id) as product_count
                              FROM farm_products fp
                              JOIN product_types pt ON fp.product_type_id = pt.id
                              JOIN farms f ON fp.farm_id = f.id
                              WHERE f.farmer_id = :farmer_id";
                    $products = $app->selectOne($query, [':farmer_id' => $loan->farmer_id]);
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
                                            <?php echo $loan->category_name ?? 'Uncategorized'; ?> Farmer
                                        </span>

                                        <span class="badge bg-success-transparent text-success ms-2">
                                            <i class="ri-funds-line me-1"></i>
                                            <?php echo ($loan->creditworthiness_score >= 70) ? 'Low Risk' : (($loan->creditworthiness_score >= 60) ? 'Medium Risk' : 'High Risk'); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loan Purpose Card -->
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="card custom-card mb-4">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="ri-focus-3-line me-2"></i> Loan Purpose
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($loan->purpose)); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- loan repayments section -->
            <!-- Row 3: Loan Repayments -->
            <div class="row mt-4">
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="ri-refund-line me-2"></i> Loan Repayments
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="datatable-repayments" class="table table-bordered text-nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Source</th>
                                            <th>Reference</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT 
                                                    lr.payment_date,
                                                    lr.amount,  
                                                    lr.payment_method,
                                                    lr.notes,
                                                    pd.id as delivery_id
                                                  FROM loan_repayments lr
                                                  LEFT JOIN produce_deliveries pd ON lr.produce_delivery_id = pd.id  
                                                  WHERE lr.approved_loan_id = (
                                                    SELECT id FROM approved_loans WHERE loan_application_id = '{$loanId}'
                                                  )
                                                  ORDER BY lr.payment_date DESC";
                                                  
                                        $repayments = $app->select_all($query);
                                      ?>
                                        <?php if ($repayments): ?>
                                        <?php foreach ($repayments as $repayment): ?>
                                        <tr>
                                            <td><?php echo date('M d, Y', strtotime($repayment->payment_date)); ?></td>
                                            <td>KES <?php echo number_format($repayment->amount, 2); ?></td>
                                            <td><?php echo ucfirst($repayment->payment_method); ?></td>
                                            <td>
                                                <?php if ($repayment->delivery_id): ?>
                                                <a
                                                    href="produce-delivery-details?id=<?php echo $repayment->delivery_id; ?>">
                                                    DLVR<?php echo str_pad($repayment->delivery_id, 6, '0', STR_PAD_LEFT); ?>
                                                </a>
                                                <?php else: ?>
                                                <?php echo $repayment->notes ?? '-'; ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No repayments found</td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Row 4: Loan Comments -->
            <!-- Row 4: Loan Comments -->
            <div class="row mt-4">
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="ri-message-2-line me-2"></i> Comments & Notes
                            </div>
                        </div>
                        <div class="card-body">
                            <?php
                                       $query = "SELECT
                                                   c.comment,
                                                   c.is_rejection_reason,
                                                   CONCAT(u.first_name, ' ', u.last_name) as author,
                                                   u.profile_picture,
                                                   c.created_at
                                                 FROM comments c
                                                 JOIN users u ON c.user_id = u.id
                                                 WHERE c.reference_type = 'loan_application' AND c.reference_id = $loanId
                                                 ORDER BY c.created_at DESC";
                                                 
                                       $comments = $app->select_all($query);
                                     ?>
                            <?php if ($comments): ?>
                            <div class="comment-list">
                                <?php foreach ($comments as $comment): ?>
                                <div class="comment-item mb-4">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3">
                                            <?php if(!empty($comment->profile_picture) && file_exists($comment->profile_picture)): ?>
                                            <img src="<?php echo $comment->profile_picture; ?>"
                                                class="avatar avatar-sm rounded-circle" alt="Profile">
                                            <?php else: ?>
                                            <span class="avatar avatar-sm rounded-circle bg-primary">
                                                <?php echo strtoupper(substr($comment->author, 0, 1)); ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <div class="mb-2">
                                                <span
                                                    class="fw-medium"><?php echo htmlspecialchars($comment->author); ?></span>
                                                <span class="text-muted ms-2 fs-12">
                                                    <i class="ri-time-line me-1"></i>
                                                    <?php echo date('M d, Y h:i A', strtotime($comment->created_at)); ?>
                                                </span>
                                                <?php if ($comment->is_rejection_reason): ?>
                                                <span class="badge bg-danger ms-2">
                                                    <i class="ri-error-warning-line me-1"></i>
                                                    Rejection Reason
                                                </span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="fs-14"><?php echo nl2br(htmlspecialchars($comment->comment)); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <div class="text-center text-muted py-4">
                                <img src="/api/placeholder/400/250" alt="Empty Comments" class="mb-3" />
                                <h5>No Comments Found</h5>
                                <p class="mb-0">Be the first to add a comment or note to this loan application</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Row 5: Loan Logs -->
            <div class="row mt-4">
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="ri-file-list-3-line me-2"></i> Application Logs
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="timeline-wrapper">
                                <?php
                                 $query = "SELECT
                                             ll.action_type,
                                             ll.description,
                                             CONCAT(u.first_name, ' ', u.last_name) as user,
                                             ll.created_at
                                           FROM loan_logs ll 
                                           JOIN users u ON ll.user_id = u.id
                                           WHERE ll.loan_application_id = '{$loanId}'
                                           ORDER BY ll.created_at DESC";
                                           
                                 $logs = $app->select_all($query);
                               ?>
                                <?php if ($logs): ?>
                                <ul class="timeline timeline-primary">
                                    <?php foreach ($logs as $log): ?>
                                    <li class="timeline-item">
                                        <div class="timeline-marker"></div>
                                        <div class="timeline-content">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <h6 class="timeline-title mb-0 text-capitalize">
                                                    <i class="ri-checkbox-circle-line me-2" style="color: #6AA32D;"></i>
                                                    <?php echo str_replace('_', ' ', $log->action_type); ?>
                                                </h6>
                                                <span
                                                    class="timeline-date"><?php echo date('M d, Y', strtotime($log->created_at)); ?></span>
                                            </div>
                                            <p class="timeline-text mb-2">
                                                <?php echo nl2br(htmlspecialchars($log->description)); ?></p>
                                            <div class="timeline-meta text-muted fs-12">
                                                <i class="ri-user-line me-1"></i>
                                                <?php echo htmlspecialchars($log->user); ?>
                                                <i class="ri-time-line ms-3 me-1"></i>
                                                <?php echo date('h:i A', strtotime($log->created_at)); ?>
                                            </div>
                                        </div>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php else: ?>
                                <div class="text-center text-muted py-4">
                                    <img src="/api/placeholder/400/250" alt="Empty Logs" class="mb-3" />
                                    <h5>No Application Logs Found</h5>
                                    <p class="mb-0">Logging will begin once the loan application is processed</p>
                                </div>
                                <?php endif; ?>
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
    <!-- JavaScript to calculate total repayment -->
    <script>
    // Script for PDF download functionality
    document.getElementById('downloadPDF').addEventListener('click', function() {
        // Show loading message with toastr
        toastr.info('Preparing your loan statement for download...', 'Please wait', {
            "positionClass": "toast-top-right",
            "progressBar": true,
            "timeOut": 0,
            "extendedTimeOut": 0,
            "closeButton": false,
            "hideMethod": "fadeOut"
        });

        // Get the loan ID from the URL
        const urlParams = new URLSearchParams(window.location.search);
        const loanId = urlParams.get('id');

        if (!loanId) {
            toastr.error('Loan ID not found in URL', 'Error', {
                "positionClass": "toast-top-right",
                "progressBar": true,
                "timeOut": 5000
            });
            return;
        }

        // AJAX call to generate PDF
        $.ajax({
            url: "http://localhost/dfcs/ajax/loan-controller/generate-statement-pdf.php",
            type: "POST",
            data: {
                loanId: loanId
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
                    let filename = 'Loan_Statement_LOAN' + String(loanId).padStart(5, '0') + '.pdf';
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

                    toastr.success('Loan statement downloaded successfully', 'Success', {
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
                            toastr.error(errorJson.error || 'Failed to generate loan statement',
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
                        console.error('Error processing response:', readError);
                    }
                }
            },
            error: function(xhr, status, error) {
                toastr.clear();
                toastr.error('Failed to generate loan statement. Please try again.', 'Error', {
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