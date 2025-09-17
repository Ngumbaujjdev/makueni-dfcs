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
        $query = "SELECT u.*, f.registration_number, f.id as farmer_id, f.category_id, fc.name as category_name
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
                            Input Credit Dashboard
                            <?php if($farmer->category_name): ?>
                            - <?php echo $farmer->category_name ?> Farmer
                            <?php endif; ?>
                        </span>
                    </div>
                </div>

                <!-- Input Credit Application Stats -->
                <?php 
// Initialize the app
$app = new App;
$farmer_id = $farmer->farmer_id;

// Get summary statistics
$summaryQuery = "SELECT 
    COUNT(*) as total_applications,
    SUM(CASE WHEN status = 'pending' OR status = 'under_review' THEN 1 ELSE 0 END) as pending_count,
    SUM(CASE WHEN status = 'approved' OR status = 'fulfilled' THEN 1 ELSE 0 END) as approved_count,
    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_count,
    SUM(total_amount) as total_credit_amount,
    SUM(total_with_interest) as total_with_interest,
    (SELECT COALESCE(SUM(aic.remaining_balance), 0) 
     FROM approved_input_credits aic 
     JOIN input_credit_applications ica ON aic.credit_application_id = ica.id 
     WHERE ica.farmer_id = {$farmer_id} AND aic.status = 'active') as outstanding_balance
FROM input_credit_applications 
WHERE farmer_id = {$farmer_id}";

$summary = $app->select_one($summaryQuery);

// Get agrovet breakdown
$agrovetQuery = "SELECT 
    a.name as agrovet_name, 
    COUNT(ica.id) as application_count,
    SUM(ica.total_amount) as total_amount,
    SUM(CASE WHEN ica.status = 'approved' OR ica.status = 'fulfilled' THEN 1 ELSE 0 END) as approved_count,
    ROUND((SUM(CASE WHEN ica.status = 'approved' OR ica.status = 'fulfilled' THEN 1 ELSE 0 END) / COUNT(ica.id)) * 100) as approval_rate
FROM input_credit_applications ica
JOIN agrovets a ON ica.agrovet_id = a.id
WHERE ica.farmer_id = {$farmer_id}
GROUP BY a.name
ORDER BY application_count DESC";

$agrovetBreakdown = $app->select_all($agrovetQuery);

// Get input type breakdown
$inputTypeQuery = "SELECT 
    ici.input_type, 
    COUNT(ici.id) as item_count,
    SUM(ici.total_price) as total_amount
FROM input_credit_items ici
JOIN input_credit_applications ica ON ici.credit_application_id = ica.id
WHERE ica.farmer_id = {$farmer_id}
GROUP BY ici.input_type
ORDER BY total_amount DESC";

$inputTypeBreakdown = $app->select_all($inputTypeQuery);
?>

                <!-- Input Credit Application Summary Cards -->
                <div class="row mt-2">
                    <!-- Total Applications -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden" style="background:#6AA32D!important;">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded"
                                            style="background-color: white!important;">
                                            <i style="color:#6AA32D" class="fa-solid fa-file-invoice fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-white mb-0">Total Credit Applications</p>
                                                <h4 style="color:wheat;" class="fw-semibold mt-1">
                                                    <?php echo $summary ? $summary->total_applications : 0 ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Applications -->
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
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Pending Review</p>
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

                    <!-- Approved Applications -->
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
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Approved Credits</p>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php echo $summary ? $summary->approved_count : 0 ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rejected Applications -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
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
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Rejected Applications
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

                    <!-- Total Credit Amount (Principal) -->
                    <div class="col-xxl-4 col-lg-4 col-md-6 mt-2">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-shopping-cart fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Total Input Value</p>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    KES
                                                    <?php echo $summary ? number_format($summary->total_credit_amount, 2) : 0 ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total With Interest -->
                    <div class="col-xxl-4 col-lg-4 col-md-6 mt-2">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-percentage fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Total With Interest
                                                </p>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    KES
                                                    <?php echo $summary ? number_format($summary->total_with_interest, 2) : 0 ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Outstanding Balance -->
                    <div class="col-xxl-4 col-lg-4 col-md-12 mt-2">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-credit-card fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Outstanding Balance
                                                </p>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    KES
                                                    <?php echo $summary ? number_format($summary->outstanding_balance, 2) : 0 ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Input Credit by Agrovet Section -->
                <div class="row mt-2">
                    <div class="col-lg-12 col-md-12">
                        <div class="card custom-card shadow-sm">
                            <div class="card-header bg-light">
                                <div class="card-title">
                                    <i class="fa-solid fa-store text-success me-2"></i> Input Credits by Agrovet
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr class="bg-light">
                                                <th><i class="fa-solid fa-shop text-primary me-1"></i> Agrovet Name</th>
                                                <th><i class="fa-solid fa-file-invoice text-warning me-1"></i>
                                                    Applications</th>
                                                <th><i class="fa-solid fa-money-bill-wave text-success me-1"></i> Total
                                                    Amount</th>
                                                <th><i class="fa-solid fa-check-circle text-info me-1"></i> Approval
                                                    Rate</th>
                                                <th><i class="fa-solid fa-calendar-check text-danger me-1"></i> Last
                                                    Application</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                            // Get agrovet breakdown with last application date
                            $agrovetDetailQuery = "SELECT 
                                a.name as agrovet_name, 
                                COUNT(ica.id) as application_count,
                                SUM(ica.total_amount) as total_amount,
                                SUM(CASE WHEN ica.status = 'approved' OR ica.status = 'fulfilled' THEN 1 ELSE 0 END) as approved_count,
                                ROUND((SUM(CASE WHEN ica.status = 'approved' OR ica.status = 'fulfilled' THEN 1 ELSE 0 END) / COUNT(ica.id)) * 100) as approval_rate,
                                MAX(ica.application_date) as last_application_date
                            FROM input_credit_applications ica
                            JOIN agrovets a ON ica.agrovet_id = a.id
                            WHERE ica.farmer_id = {$farmer_id}
                            GROUP BY a.name
                            ORDER BY application_count DESC";
                            
                            $agrovetDetail = $app->select_all($agrovetDetailQuery);
                            ?>

                                            <?php if(isset($agrovetDetail) && !empty($agrovetDetail)): ?>
                                            <?php foreach($agrovetDetail as $agrovet): ?>
                                            <tr>
                                                <td class="fw-medium">
                                                    <div class="d-flex align-items-center">
                                                        <span
                                                            class="avatar avatar-sm avatar-rounded bg-success-transparent me-2">
                                                            <i class="fa-solid fa-leaf text-success"></i>
                                                        </span>
                                                        <?php echo htmlspecialchars($agrovet->agrovet_name) ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-warning text-dark rounded-pill">
                                                        <?php echo $agrovet->application_count ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="text-success fw-bold">
                                                        KES <?php echo number_format($agrovet->total_amount, 2) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress me-2" style="width: 60px; height: 6px;">
                                                            <div class="progress-bar bg-success" role="progressbar"
                                                                style="width: <?php echo $agrovet->approval_rate; ?>%"
                                                                aria-valuenow="<?php echo $agrovet->approval_rate; ?>"
                                                                aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <span><?php echo $agrovet->approval_rate ?>%</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-muted">
                                                        <i class="fa-regular fa-clock me-1"></i>
                                                        <?php echo date('d M Y', strtotime($agrovet->last_application_date)) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center py-4">
                                                    <i class="fa-solid fa-store-slash text-muted fs-4 mb-2 d-block"></i>
                                                    <p class="mb-0">No agrovet applications found</p>
                                                    <a href="apply_input_credit.php"
                                                        class="btn btn-sm btn-outline-success mt-2">
                                                        <i class="fa-solid fa-plus-circle me-1"></i> Apply for Input
                                                        Credit
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Input Types Distribution Section -->
                <div class="row mt-4">
                    <div class="col-lg-12 col-md-12">
                        <div class="card custom-card shadow-sm">
                            <div class="card-header bg-light">
                                <div class="card-title">
                                    <i class="fa-solid fa-chart-pie text-success me-2"></i> Input Types Distribution
                                </div>
                            </div>
                            <div class="card-body">
                                <?php
                // Get input type distribution with more details
                $inputTypeDetailQuery = "SELECT 
                    ici.input_type, 
                    COUNT(ici.id) as item_count,
                    SUM(ici.total_price) as total_amount,
                    COUNT(DISTINCT ica.id) as applications_count,
                    ROUND(AVG(ici.unit_price), 2) as avg_unit_price
                FROM input_credit_items ici
                JOIN input_credit_applications ica ON ici.credit_application_id = ica.id
                WHERE ica.farmer_id = {$farmer_id}
                GROUP BY ici.input_type
                ORDER BY total_amount DESC";
                
                $inputTypeDetail = $app->select_all($inputTypeDetailQuery);
                
                // Calculate total amount for percentage
                $totalAmount = 0;
                if(isset($inputTypeDetail) && !empty($inputTypeDetail)) {
                    foreach($inputTypeDetail as $type) {
                        $totalAmount += $type->total_amount;
                    }
                }
                
                // Define icons and colors for each input type
                $typeIcons = [
                    'fertilizer' => '<i class="fa-solid fa-seedling"></i>',
                    'pesticide' => '<i class="fa-solid fa-bug-slash"></i>',
                    'seeds' => '<i class="fa-solid fa-wheat-awn"></i>',
                    'tools' => '<i class="fa-solid fa-tools"></i>',
                    'other' => '<i class="fa-solid fa-box"></i>'
                ];
                
                $typeColors = [
                    'fertilizer' => 'success',
                    'pesticide' => 'danger',
                    'seeds' => 'warning',
                    'tools' => 'primary',
                    'other' => 'info'
                ];
                ?>

                                <?php if(isset($inputTypeDetail) && !empty($inputTypeDetail)): ?>
                                <div class="row mb-4">
                                    <?php foreach($inputTypeDetail as $index => $type): ?>
                                    <?php 
                        $percentage = $totalAmount > 0 ? round(($type->total_amount / $totalAmount) * 100) : 0;
                        $color = isset($typeColors[$type->input_type]) ? $typeColors[$type->input_type] : 'secondary';
                        $icon = isset($typeIcons[$type->input_type]) ? $typeIcons[$type->input_type] : '<i class="fa-solid fa-question-circle"></i>';
                    ?>
                                    <div class="col-md-4 col-sm-6 mb-3">
                                        <div class="card shadow-sm border-0">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <div class="d-flex align-items-center">
                                                        <span
                                                            class="avatar avatar-md avatar-rounded bg-<?php echo $color; ?>-transparent me-2">
                                                            <?php echo $icon; ?>
                                                        </span>
                                                        <h6 class="mb-0 text-capitalize">
                                                            <?php echo $type->input_type; ?></h6>
                                                    </div>
                                                    <span
                                                        class="badge bg-<?php echo $color; ?> rounded-pill"><?php echo $percentage; ?>%</span>
                                                </div>

                                                <div class="progress mb-3" style="height: 8px;">
                                                    <div class="progress-bar bg-<?php echo $color; ?>"
                                                        role="progressbar" style="width: <?php echo $percentage; ?>%"
                                                        aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0"
                                                        aria-valuemax="100">
                                                    </div>
                                                </div>

                                                <div class="row g-2 text-center">
                                                    <div class="col-6 border-end">
                                                        <p class="text-muted mb-0 fs-12">Items</p>
                                                        <h6 class="mb-0"><?php echo $type->item_count; ?></h6>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="text-muted mb-0 fs-12">Amount</p>
                                                        <h6 class="mb-0">KES
                                                            <?php echo number_format($type->total_amount, 0); ?></h6>
                                                    </div>
                                                </div>

                                                <div class="d-flex justify-content-between mt-3">
                                                    <small class="text-muted">
                                                        <i class="fa-solid fa-money-bill-wave me-1"></i> Avg: KES
                                                        <?php echo number_format($type->avg_unit_price, 2); ?>
                                                    </small>
                                                    <small class="text-muted">
                                                        <i class="fa-solid fa-file-invoice me-1"></i> In
                                                        <?php echo $type->applications_count; ?> apps
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Input Categories Summary -->
                                <div class="table-responsive mt-2">
                                    <table class="table table-bordered">
                                        <thead class="bg-light">
                                            <tr>
                                                <th><i class="fa-solid fa-tag text-success me-1"></i> Category</th>
                                                <th><i class="fa-solid fa-calculator text-primary me-1"></i> Items Count
                                                </th>
                                                <th><i class="fa-solid fa-money-bill-wave text-warning me-1"></i> Total
                                                    Amount</th>
                                                <th><i class="fa-solid fa-chart-pie text-info me-1"></i> Distribution
                                                </th>
                                                <th><i class="fa-solid fa-arrow-trend-up text-danger me-1"></i> Average
                                                    Unit Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($inputTypeDetail as $type): ?>
                                            <?php 
                                $percentage = $totalAmount > 0 ? round(($type->total_amount / $totalAmount) * 100) : 0;
                                $color = isset($typeColors[$type->input_type]) ? $typeColors[$type->input_type] : 'secondary';
                                $icon = isset($typeIcons[$type->input_type]) ? $typeIcons[$type->input_type] : '<i class="fa-solid fa-question-circle"></i>';
                            ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span
                                                            class="avatar avatar-xs avatar-rounded bg-<?php echo $color; ?>-transparent me-2">
                                                            <?php echo $icon; ?>
                                                        </span>
                                                        <span
                                                            class="text-capitalize fw-medium"><?php echo $type->input_type; ?></span>
                                                    </div>
                                                </td>
                                                <td><?php echo $type->item_count; ?></td>
                                                <td>KES <?php echo number_format($type->total_amount, 2); ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress flex-grow-1 me-2" style="height: 5px;">
                                                            <div class="progress-bar bg-<?php echo $color; ?>"
                                                                role="progressbar"
                                                                style="width: <?php echo $percentage; ?>%"
                                                                aria-valuenow="<?php echo $percentage; ?>"
                                                                aria-valuemin="0" aria-valuemax="100">
                                                            </div>
                                                        </div>
                                                        <span
                                                            class="text-muted fs-12"><?php echo $percentage; ?>%</span>
                                                    </div>
                                                </td>
                                                <td>KES <?php echo number_format($type->avg_unit_price, 2); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <tr class="bg-light-transparent">
                                                <td colspan="2" class="fw-bold">Total</td>
                                                <td colspan="3" class="fw-bold">KES
                                                    <?php echo number_format($totalAmount, 2); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="fa-solid fa-box-open text-muted opacity-50" style="font-size: 3rem;"></i>
                                    <h5 class="mt-3 text-muted">No Input Data Available</h5>
                                    <p class="text-muted mb-4">You haven't requested any inputs on credit yet.</p>
                                    <a href="http://localhost/dfcs/farmers/credits/apply" class="btn btn-success">
                                        <i class="fa-solid fa-plus-circle me-1"></i> Apply for Input Credit
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Input Credit Applications Table -->
                <div id="displayInputCreditApplications">
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
        displayInputCreditApplications();
    });

    function displayInputCreditApplications() {
        let displayApplications = "true";
        $.ajax({
            url: "http://localhost/dfcs/ajax/input-credit-controller/display-farmer-applications.php",
            type: 'POST',
            data: {
                displayApplications: displayApplications,
            },
            success: function(data, status) {
                $('#displayInputCreditApplications').html(data);
            },
            error: function() {
                toastr.error('Failed to load input credit applications', 'Error', {
                    "positionClass": "toast-top-right",
                    "progressBar": true,
                    "timeOut": 3000,
                    "extendedTimeOut": 1000,
                    "hideMethod": "fadeOut"
                });
            }
        });
    }

    function viewInputCreditDetails(applicationId) {
        // Redirect to application details page
        window.location.href = "http://localhost/dfcs/farmers/credits/view-details?id=" + applicationId;
    }

    function applyForInputCredit() {
        // Redirect to input credit application form
        window.location.href = "http://localhost/dfcs/farmers/credits/apply";
    }
    </script>
</body>



</html>