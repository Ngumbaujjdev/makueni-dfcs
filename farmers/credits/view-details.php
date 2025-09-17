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
            <?php
// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo '<div class="alert alert-danger">No input credit application ID specified</div>';
    exit;
}

$app = new App;
$inputCreditId = intval($_GET['id']);
$userId = $_SESSION['user_id'];

// Fetch the input credit application details with related information
$query = "SELECT 
            ica.id,
            ica.farmer_id,
            ica.agrovet_id,
            ica.total_amount,
            ica.credit_percentage,
            ica.total_with_interest,
            ica.repayment_percentage,
            ica.application_date,
            ica.status,
            ica.creditworthiness_score,
            ica.rejection_reason,
            ica.reviewed_by,
            ica.review_date,
            ica.created_at,
            ica.updated_at,
            a.name as agrovet_name,
            a.location as agrovet_location,
            at.name as agrovet_type,
            CASE 
                WHEN ica.status = 'approved' OR ica.status = 'fulfilled' THEN 
                    (SELECT aic.fulfillment_date FROM approved_input_credits aic WHERE aic.credit_application_id = ica.id)
                ELSE NULL
            END as fulfillment_date,
            CASE 
                WHEN ica.status = 'approved' OR ica.status = 'fulfilled' THEN 
                    (SELECT aic.remaining_balance FROM approved_input_credits aic WHERE aic.credit_application_id = ica.id)
                ELSE NULL
            END as remaining_balance,
            CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
            u.phone as farmer_phone,
            u.email as farmer_email,
            f.registration_number as farmer_registration,
            fc.name as farmer_category
          FROM input_credit_applications ica
          JOIN agrovets a ON ica.agrovet_id = a.id
          JOIN agrovet_types at ON a.type_id = at.id
          JOIN farmers f ON ica.farmer_id = f.id
          JOIN farmer_categories fc ON f.category_id = fc.id
          JOIN users u ON f.user_id = u.id
          WHERE ica.id = :input_credit_id";

$params = [
    ':input_credit_id' => $inputCreditId
];

$inputCredit = $app->selectOne($query, $params);

// Check if the input credit application exists and belongs to the current user
if (!$inputCredit) {
    echo '<div class="alert alert-danger">Input credit application not found</div>';
    exit;
}

// Check if the user is the owner of this input credit application
$checkOwnerQuery = "SELECT COUNT(*) as is_owner 
                   FROM input_credit_applications ica 
                   JOIN farmers f ON ica.farmer_id = f.id 
                   WHERE ica.id = :input_credit_id AND f.user_id = :user_id";
                   
$ownerCheck = $app->selectOne($checkOwnerQuery, [
    ':input_credit_id' => $inputCreditId,
    ':user_id' => $userId
]);

if (!$ownerCheck || $ownerCheck->is_owner == 0) {
    echo '<div class="alert alert-danger">You do not have permission to view this input credit application</div>';
    exit;
}

// Get credit score breakdown from input credit logs
$creditScoreQuery = "SELECT description 
                    FROM input_credit_logs 
                    WHERE input_credit_application_id = :input_credit_id 
                    AND action_type = 'creditworthiness_check' 
                    ORDER BY created_at DESC 
                    LIMIT 1";
                    
$creditScoreLog = $app->selectOne($creditScoreQuery, [':input_credit_id' => $inputCreditId]);

// Parse credit score components if available
$creditScores = [
    'input_repayment_history' => 0,
    'financial_obligations' => 0,
    'produce_history' => 0,
    'amount_ratio' => 0
];

if ($creditScoreLog && $creditScoreLog->description) {
    $description = $creditScoreLog->description;
    
    // Extract scores using regex
    preg_match('/Input repayment history score: (\d+)/', $description, $repaymentMatches);
    preg_match('/Financial obligations score: (\d+)/', $description, $obligationsMatches);
    preg_match('/Produce history score: (\d+)/', $description, $produceMatches);
    preg_match('/Amount ratio score: (\d+)/', $description, $amountMatches);
    
    if (!empty($repaymentMatches)) $creditScores['input_repayment_history'] = intval($repaymentMatches[1]);
    if (!empty($obligationsMatches)) $creditScores['financial_obligations'] = intval($obligationsMatches[1]);
    if (!empty($produceMatches)) $creditScores['produce_history'] = intval($produceMatches[1]);
    if (!empty($amountMatches)) $creditScores['amount_ratio'] = intval($amountMatches[1]);
}

// Format application reference number
$inputCreditReference = 'INPCR' . str_pad($inputCredit->id, 5, '0', STR_PAD_LEFT);

// Get input items
$inputItemsQuery = "SELECT 
                    ici.id,
                    ici.input_type,
                    ici.input_name,
                    ici.quantity,
                    ici.unit,
                    ici.unit_price,
                    ici.total_price,
                    ici.description
                  FROM input_credit_items ici
                  WHERE ici.credit_application_id = '{$inputCreditId}'
                  ORDER BY ici.input_type, ici.input_name";

$inputItems = $app->select_all($inputItemsQuery);

// Calculate total number of items
$totalItems = count($inputItems);

// Group input items by type for statistics
$inputTypeStats = [];
foreach ($inputItems as $item) {
    if (!isset($inputTypeStats[$item->input_type])) {
        $inputTypeStats[$item->input_type] = [
            'count' => 0,
            'total' => 0
        ];
    }
    
    $inputTypeStats[$item->input_type]['count']++;
    $inputTypeStats[$item->input_type]['total'] += $item->total_price;
}

// Calculate repayment progress percentage
$repaymentProgress = 0;
if ($inputCredit->status == 'fulfilled' && $inputCredit->total_with_interest > 0 && $inputCredit->remaining_balance >= 0) {
    $paid = $inputCredit->total_with_interest - $inputCredit->remaining_balance;
    $repaymentProgress = ($paid / $inputCredit->total_with_interest) * 100;
}
?>

            <!-- Page Header with Download Button -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <h1 class="page-title fw-semibold fs-18 mb-0">Input Credit Application Details</h1>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-primary" id="downloadPDF">
                        <i class="ri-file-download-line me-1"></i> Download PDF
                    </button>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="javascript:history.back()">Input Credits</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Input Credit Details</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Row 1: Essential Input Credit Information Cards -->
            <div class="row">
                <!-- Card 1: Basic Information -->
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <div class="card custom-card mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="avatar avatar-md avatar-rounded bg-success">
                                        <i class="ri-file-list-3-line fs-16"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted fs-12">Reference</p>
                                    <h5 class="fw-semibold mb-1">
                                        <?php echo $inputCreditReference; ?>
                                    </h5>
                                    <div class="d-flex align-items-center">
                                        <?php 
                            $statusClass = 'secondary';
                            $statusIcon = 'clock';
                            
                            if ($inputCredit->status == 'under_review') {
                                $statusClass = 'primary';
                                $statusIcon = 'magnifying-glass';
                            } elseif ($inputCredit->status == 'approved') {
                                $statusClass = 'info';
                                $statusIcon = 'check-double';
                            } elseif ($inputCredit->status == 'fulfilled') {
                                $statusClass = 'success';
                                $statusIcon = 'circle-check';
                            } elseif ($inputCredit->status == 'rejected') {
                                $statusClass = 'danger';
                                $statusIcon = 'circle-xmark';
                            } elseif ($inputCredit->status == 'completed') {
                                $statusClass = 'success';
                                $statusIcon = 'trophy';
                            } elseif ($inputCredit->status == 'cancelled') {
                                $statusClass = 'danger';
                                $statusIcon = 'ban';
                            }
                            ?>
                                        <span class="badge bg-<?php echo $statusClass; ?>">
                                            <i class="ri-<?php echo $statusIcon; ?>-line me-1"></i>
                                            <?php echo ucfirst(str_replace('_', ' ', $inputCredit->status)); ?>
                                        </span>
                                        <span class="text-muted ms-2 fs-12">
                                            <?php echo date('M d, Y', strtotime($inputCredit->application_date)); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Agrovet Details -->
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <div class="card custom-card mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="avatar avatar-md avatar-rounded bg-primary">
                                        <i class="ri-store-2-line fs-16"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted fs-12">Agrovet Provider</p>
                                    <h5 class="fw-semibold mb-1">
                                        <?php echo htmlspecialchars($inputCredit->agrovet_name); ?></h5>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-light text-dark">
                                            <?php echo ucfirst($inputCredit->agrovet_type); ?>
                                        </span>
                                        <span class="text-muted ms-2 fs-12">
                                            <i
                                                class="ri-map-pin-line me-1"></i><?php echo $inputCredit->agrovet_location; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Amount & Interest -->
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <div class="card custom-card mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="avatar avatar-md avatar-rounded bg-warning">
                                        <i class="ri-shopping-cart-line fs-16"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted fs-12">Input Value</p>
                                    <h5 class="fw-semibold mb-1">KES
                                        <?php echo number_format($inputCredit->total_amount, 2); ?></h5>
                                    <div class="d-flex align-items-center">
                                        <span class="text-primary">
                                            <?php echo $inputCredit->credit_percentage; ?>% Interest
                                        </span>
                                        <span class="text-muted ms-2 fs-12">
                                            <?php echo $inputCredit->repayment_percentage; ?>% Repayment Rate
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Creditworthiness Score -->
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <div class="card custom-card mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="avatar avatar-md avatar-rounded bg-info">
                                        <i class="ri-bar-chart-line fs-16"></i>
                                    </span>
                                </div>
                                <div style="width: 100%;">
                                    <p class="mb-0 text-muted fs-12">Creditworthiness Score</p>
                                    <?php 
                        $scoreClass = 'danger';
                        if ($inputCredit->creditworthiness_score >= 70) {
                            $scoreClass = 'success';
                        } elseif ($inputCredit->creditworthiness_score >= 50) {
                            $scoreClass = 'warning';
                        }
                        ?>
                                    <h5 class="fw-semibold mb-1 text-<?php echo $scoreClass; ?>">
                                        <?php echo $inputCredit->creditworthiness_score; ?>/100
                                    </h5>
                                    <div class="progress mt-1" style="height: 6px;">
                                        <div class="progress-bar bg-<?php echo $scoreClass; ?>" role="progressbar"
                                            style="width: <?php echo $inputCredit->creditworthiness_score; ?>%"
                                            aria-valuenow="<?php echo $inputCredit->creditworthiness_score; ?>"
                                            aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Row 2: Input Items Breakdown -->
            <div class="row">
                <div class="col-12">
                    <div class="card custom-card mb-4">
                        <div class="card-header border-bottom d-flex justify-content-between">
                            <div class="card-title mb-0">
                                <i class="ri-list-check-3 me-2 text-success"></i>Input Items Breakdown
                            </div>
                            <div>
                                <span class="badge bg-light-subtle text-dark">Total Items:
                                    <?php echo $totalItems; ?></span>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Input type stats - row of small cards -->
                            <div class="row mb-4">
                                <?php
                    $typeIcons = [
                        'fertilizer' => 'ri-seedling-line',
                        'pesticide' => 'ri-bug-line',
                        'seeds' => 'ri-plant-line',
                        'tools' => 'ri-tools-line',
                        'other' => 'ri-box-3-line'
                    ];
                    
                    $typeColors = [
                        'fertilizer' => 'success',
                        'pesticide' => 'danger',
                        'seeds' => 'warning',
                        'tools' => 'primary',
                        'other' => 'info'
                    ];
                    
                    foreach ($inputTypeStats as $type => $stats) {
                        $icon = isset($typeIcons[$type]) ? $typeIcons[$type] : 'ri-box-3-line';
                        $color = isset($typeColors[$type]) ? $typeColors[$type] : 'secondary';
                        $percentage = ($inputCredit->total_amount > 0) ? round(($stats['total'] / $inputCredit->total_amount) * 100) : 0;
                    ?>
                                <div class="col-md-4 col-sm-6 mb-3">
                                    <div class="card border shadow-none mb-0">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="avatar avatar-sm me-2 bg-<?php echo $color; ?>-transparent">
                                                    <i class="<?php echo $icon; ?> text-<?php echo $color; ?>"></i>
                                                </div>
                                                <h6 class="mb-0 text-capitalize"><?php echo $type; ?></h6>
                                                <span
                                                    class="badge bg-<?php echo $color; ?>-transparent text-<?php echo $color; ?> ms-auto">
                                                    <?php echo $stats['count']; ?> items
                                                </span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="text-muted fs-12">Total</span>
                                                <span class="fw-semibold">KES
                                                    <?php echo number_format($stats['total'], 2); ?></span>
                                            </div>
                                            <div class="progress" style="height: 4px;">
                                                <div class="progress-bar bg-<?php echo $color; ?>" role="progressbar"
                                                    style="width: <?php echo $percentage; ?>%"
                                                    aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                </div>
                                            </div>
                                            <div class="text-end mt-1">
                                                <span class="fs-12 text-muted"><?php echo $percentage; ?>% of
                                                    total</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>

                            <!-- Input items table -->
                            <div class="table-responsive">
                                <table class="table table-hover border table-striped">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 5%;">#</th>
                                            <th style="width: 15%;">Type</th>
                                            <th style="width: 25%;">Input Name</th>
                                            <th style="width: 10%;">Quantity</th>
                                            <th style="width: 10%;">Unit</th>
                                            <th style="width: 15%;">Unit Price</th>
                                            <th style="width: 20%;">Total Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                            $counter = 1;
                            $lastType = '';
                            foreach ($inputItems as $item): 
                                $icon = isset($typeIcons[$item->input_type]) ? $typeIcons[$item->input_type] : 'ri-box-3-line';
                                $color = isset($typeColors[$item->input_type]) ? $typeColors[$item->input_type] : 'secondary';
                                
                                // Add a subtle header when type changes
                                $typeHeader = '';
                                if ($lastType != $item->input_type) {
                                    $typeHeader = '<tr class="bg-light-subtle">
                                        <td colspan="7" class="py-2">
                                            <span class="fw-medium text-capitalize">
                                                <i class="' . $icon . ' text-' . $color . ' me-1"></i> 
                                                ' . ucfirst($item->input_type) . ' Items
                                            </span>
                                        </td>
                                    </tr>';
                                    $lastType = $item->input_type;
                                }
                                echo $typeHeader;
                            ?>
                                        <tr>
                                            <td class="fw-medium"><?php echo $counter++; ?></td>
                                            <td>
                                                <span
                                                    class="badge bg-<?php echo $color; ?>-transparent text-<?php echo $color; ?> text-capitalize">
                                                    <i
                                                        class="<?php echo $icon; ?> me-1"></i><?php echo $item->input_type; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="fw-medium">
                                                    <?php echo htmlspecialchars($item->input_name); ?></div>
                                                <?php if ($item->description): ?>
                                                <small class="text-muted d-block text-truncate"
                                                    style="max-width: 200px;">
                                                    <?php echo htmlspecialchars($item->description); ?>
                                                </small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center"><?php echo number_format($item->quantity, 2); ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($item->unit); ?></td>
                                            <td>KES <?php echo number_format($item->unit_price, 2); ?></td>
                                            <td>
                                                <span class="fw-semibold text-success">
                                                    KES <?php echo number_format($item->total_price, 2); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>

                                        <!-- Summary row -->
                                        <tr class="bg-success-subtle">
                                            <td colspan="6" class="text-end fw-bold">Total Input Value</td>
                                            <td class="fw-bold text-success">KES
                                                <?php echo number_format($inputCredit->total_amount, 2); ?></td>
                                        </tr>
                                        <tr class="bg-info-subtle">
                                            <td colspan="6" class="text-end fw-bold">Interest
                                                (<?php echo $inputCredit->credit_percentage; ?>%)</td>
                                            <td class="fw-bold text-info">KES
                                                <?php echo number_format($inputCredit->total_with_interest - $inputCredit->total_amount, 2); ?>
                                            </td>
                                        </tr>
                                        <tr class="bg-primary-subtle">
                                            <td colspan="6" class="text-end fw-bold">Total With Interest</td>
                                            <td class="fw-bold text-primary">KES
                                                <?php echo number_format($inputCredit->total_with_interest, 2); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Additional Input Information -->
                            <?php if ($inputItems && $inputCredit->status != 'rejected'): ?>
                            <div class="alert alert-light border mt-4 mb-0">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <span class="avatar avatar-sm bg-success-transparent">
                                            <i class="ri-information-line text-success"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Important Information About Your Input Credit</h6>
                                        <ul class="mb-0 ps-3">
                                            <li>The total amount with interest (KES
                                                <?php echo number_format($inputCredit->total_with_interest, 2); ?>) will
                                                be repaid through produce deliveries.</li>
                                            <li><?php echo $inputCredit->repayment_percentage; ?>% of each produce sale
                                                will be automatically deducted toward repayment.</li>
                                            <li>To maintain good credit, ensure regular produce deliveries to complete
                                                repayment on schedule.</li>
                                            <li>Contact your agrovet for any questions regarding input specifications or
                                                usage.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <?php elseif ($inputCredit->status == 'rejected'): ?>
                            <div class="alert alert-danger mt-4 mb-0">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <span class="avatar avatar-sm bg-danger-transparent">
                                            <i class="ri-error-warning-line text-danger"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Application Rejected</h6>
                                        <p class="mb-0">Your input credit application has been rejected.
                                            <?php if ($inputCredit->rejection_reason): ?>
                                            Reason: <?php echo htmlspecialchars($inputCredit->rejection_reason); ?>
                                            <?php else: ?>
                                            Please contact your agrovet for more information.
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Row 3: Fulfillment & Repayment Status -->
            <div class="row">
                <!-- Left Column: Fulfillment Status -->
                <div class="col-xl-6 col-lg-6">
                    <div class="card custom-card mb-4">
                        <div class="card-header border-bottom">
                            <div class="card-title">
                                <i class="ri-shopping-bag-line me-2 text-success"></i>Input Fulfillment Status
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if($inputCredit->status == 'pending' || $inputCredit->status == 'under_review'): ?>
                            <!-- For pending/under review applications -->
                            <div class="text-center py-4">
                                <div class="avatar avatar-lg avatar-rounded mx-auto mb-3 bg-warning bg-opacity-10">
                                    <i class="ri-time-line text-warning fs-2"></i>
                                </div>
                                <h5>Awaiting Approval</h5>
                                <p class="text-muted">Your input credit application is still under review. Inputs will
                                    be prepared for collection once approved.</p>
                            </div>

                            <div class="alert alert-light border mt-3">
                                <h6 class="mb-2"><i class="ri-information-line me-2 text-primary"></i>What To Expect
                                </h6>
                                <p class="mb-0 text-muted">Once your application is approved:</p>
                                <ul class="text-muted mt-2 mb-0">
                                    <li>You will be notified when your inputs are ready for collection</li>
                                    <li>You'll need to visit <?php echo htmlspecialchars($inputCredit->agrovet_name); ?>
                                        at <?php echo htmlspecialchars($inputCredit->agrovet_location); ?> to collect
                                        your inputs</li>
                                    <li>A fulfillment receipt will be provided</li>
                                    <li>Repayments will be automatically deducted from your produce sales at a rate of
                                        <?php echo $inputCredit->repayment_percentage; ?>%</li>
                                </ul>
                            </div>

                            <?php elseif($inputCredit->status == 'rejected'): ?>
                            <!-- For rejected applications -->
                            <div class="text-center py-4">
                                <div class="avatar avatar-lg avatar-rounded mx-auto mb-3 bg-danger bg-opacity-10">
                                    <i class="ri-close-circle-line text-danger fs-2"></i>
                                </div>
                                <h5>Application Rejected</h5>
                                <p class="text-muted">Unfortunately, your input credit application has been rejected.
                                </p>

                                <?php if($inputCredit->rejection_reason): ?>
                                <div class="alert alert-danger">
                                    <i class="ri-error-warning-line me-1"></i>
                                    <strong>Reason:</strong>
                                    <?php echo htmlspecialchars($inputCredit->rejection_reason); ?>
                                </div>
                                <?php endif; ?>
                            </div>

                            <div class="alert alert-light border mt-3">
                                <h6 class="mb-2"><i class="ri-information-line me-2 text-primary"></i>What's Next?</h6>
                                <p class="mb-0 text-muted">You can work on improving your creditworthiness score and
                                    apply again after 30 days. Focus on:</p>
                                <ul class="text-muted mt-2 mb-0">
                                    <li>Delivering produce consistently</li>
                                    <li>Reducing existing debt obligations</li>
                                    <li>Improving your repayment history</li>
                                </ul>
                            </div>

                            <?php elseif($inputCredit->status == 'approved'): ?>
                            <!-- For approved but not yet fulfilled applications -->
                            <div class="text-center py-4">
                                <div class="avatar avatar-lg avatar-rounded mx-auto mb-3 bg-info bg-opacity-10">
                                    <i class="ri-check-double-line text-info fs-2"></i>
                                </div>
                                <h5>Inputs Ready for Collection</h5>
                                <p class="text-muted">Your input credit application has been approved. Your inputs are
                                    now ready for collection.</p>
                            </div>

                            <div class="alert alert-info mt-3">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <i class="ri-map-pin-line fs-24"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Collection Information</h6>
                                        <p class="mb-0">Please visit
                                            <strong><?php echo htmlspecialchars($inputCredit->agrovet_name); ?></strong>
                                            at
                                            <strong><?php echo htmlspecialchars($inputCredit->agrovet_location); ?></strong>
                                            to collect your agricultural inputs.
                                        </p>
                                        <p class="mt-2 mb-0">Bring your ID and farmer registration number
                                            <strong><?php echo htmlspecialchars($inputCredit->farmer_registration); ?></strong>
                                            for verification.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Input checklist -->
                            <h6 class="mt-4 mb-3">Items to Collect</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Input Item</th>
                                            <th>Quantity</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($inputItems as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php 
                                        $icon = isset($typeIcons[$item->input_type]) ? $typeIcons[$item->input_type] : 'ri-box-3-line';
                                        $color = isset($typeColors[$item->input_type]) ? $typeColors[$item->input_type] : 'secondary';
                                        ?>
                                                    <span
                                                        class="avatar avatar-xs avatar-rounded bg-<?php echo $color; ?>-transparent me-2">
                                                        <i class="<?php echo $icon; ?> text-<?php echo $color; ?>"></i>
                                                    </span>
                                                    <span><?php echo htmlspecialchars($item->input_name); ?></span>
                                                </div>
                                            </td>
                                            <td><?php echo $item->quantity; ?>
                                                <?php echo htmlspecialchars($item->unit); ?></td>
                                            <td><span class="badge bg-warning">Pending Collection</span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php elseif($inputCredit->status == 'fulfilled' || $inputCredit->status == 'completed'): ?>
                            <!-- For fulfilled applications -->
                            <div class="text-center py-4">
                                <div class="avatar avatar-lg avatar-rounded mx-auto mb-3 bg-success bg-opacity-10">
                                    <i class="ri-shopping-bag-line text-success fs-2"></i>
                                </div>
                                <h5>Inputs Successfully Delivered</h5>
                                <p class="text-muted">
                                    Your agricultural inputs have been delivered on
                                    <strong><?php echo date('F d, Y', strtotime($inputCredit->fulfillment_date)); ?></strong>.
                                </p>
                            </div>

                            <!-- Delivery details -->
                            <div class="card border shadow-none mb-3">
                                <div class="card-header bg-light-subtle py-2">
                                    <h6 class="card-title mb-0">
                                        <i class="ri-truck-line me-2 text-primary"></i>Delivery Information
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-2"><strong>Delivery Date:</strong></p>
                                            <div class="d-flex align-items-center mb-3">
                                                <div
                                                    class="avatar avatar-xs avatar-rounded bg-success-transparent me-2">
                                                    <i class="ri-calendar-check-line text-success"></i>
                                                </div>
                                                <span><?php echo date('F d, Y', strtotime($inputCredit->fulfillment_date)); ?></span>
                                            </div>

                                            <p class="mb-2"><strong>Delivered By:</strong></p>
                                            <div class="d-flex align-items-center">
                                                <div
                                                    class="avatar avatar-xs avatar-rounded bg-primary-transparent me-2">
                                                    <i class="ri-store-2-line text-primary"></i>
                                                </div>
                                                <span><?php echo htmlspecialchars($inputCredit->agrovet_name); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-2"><strong>Delivery Status:</strong></p>
                                            <div class="d-flex align-items-center mb-3">
                                                <span class="badge bg-success py-1 px-2">
                                                    <i class="ri-check-double-line me-1"></i>Completed
                                                </span>
                                            </div>

                                            <p class="mb-2"><strong>Receipt Number:</strong></p>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-xs avatar-rounded bg-info-transparent me-2">
                                                    <i class="ri-file-list-3-line text-info"></i>
                                                </div>
                                                <span>RCPT<?php echo str_pad($inputCreditId, 5, '0', STR_PAD_LEFT); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Input checklist -->
                            <h6 class="mt-4 mb-3">Delivered Items</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Input Item</th>
                                            <th>Quantity</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($inputItems as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php 
                                        $icon = isset($typeIcons[$item->input_type]) ? $typeIcons[$item->input_type] : 'ri-box-3-line';
                                        $color = isset($typeColors[$item->input_type]) ? $typeColors[$item->input_type] : 'secondary';
                                        ?>
                                                    <span
                                                        class="avatar avatar-xs avatar-rounded bg-<?php echo $color; ?>-transparent me-2">
                                                        <i class="<?php echo $icon; ?> text-<?php echo $color; ?>"></i>
                                                    </span>
                                                    <span><?php echo htmlspecialchars($item->input_name); ?></span>
                                                </div>
                                            </td>
                                            <td><?php echo $item->quantity; ?>
                                                <?php echo htmlspecialchars($item->unit); ?></td>
                                            <td><span class="badge bg-success">Delivered</span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Repayment Plan & Status -->
                <div class="col-xl-6 col-lg-6">
                    <div class="card custom-card mb-4">
                        <div class="card-header border-bottom">
                            <div class="card-title">
                                <i class="ri-exchange-funds-line me-2 text-success"></i>Repayment Plan & Status
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if($inputCredit->status == 'pending' || $inputCredit->status == 'under_review' || $inputCredit->status == 'rejected'): ?>
                            <!-- For pending/under review/rejected applications -->
                            <div class="text-center py-4">
                                <div class="avatar avatar-lg avatar-rounded mx-auto mb-3 bg-light">
                                    <i class="ri-bank-card-line text-muted fs-2"></i>
                                </div>
                                <h5>No Active Repayment Plan</h5>
                                <p class="text-muted">
                                    <?php if($inputCredit->status == 'rejected'): ?>
                                    Your application was rejected, so no repayment plan has been established.
                                    <?php else: ?>
                                    Your application is still being processed. Repayment details will be available once
                                    approved.
                                    <?php endif; ?>
                                </p>
                            </div>

                            <?php if($inputCredit->status != 'rejected'): ?>
                            <div class="alert alert-light border mt-3">
                                <h6 class="mb-2"><i class="ri-information-line me-2 text-primary"></i>Expected Repayment
                                    Terms</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tbody>
                                            <tr>
                                                <td width="50%" class="fw-medium">Principal Amount:</td>
                                                <td>KES <?php echo number_format($inputCredit->total_amount, 2); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Interest Rate:</td>
                                                <td><?php echo $inputCredit->credit_percentage; ?>%</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Amount With Interest:</td>
                                                <td>KES
                                                    <?php echo number_format($inputCredit->total_with_interest, 2); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Repayment Method:</td>
                                                <td>Via produce sales deductions</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Deduction Rate:</td>
                                                <td><?php echo $inputCredit->repayment_percentage; ?>% of produce sales
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php elseif($inputCredit->status == 'approved'): ?>
                            <!-- For approved but not yet fulfilled applications -->
                            <div class="text-center py-4">
                                <div class="avatar avatar-lg avatar-rounded mx-auto mb-3 bg-info bg-opacity-10">
                                    <i class="ri-calendar-todo-line text-info fs-2"></i>
                                </div>
                                <h5>Repayment Plan Established</h5>
                                <p class="text-muted">
                                    Your repayment plan has been established but will not begin until inputs are
                                    delivered.
                                </p>
                            </div>

                            <div class="card border shadow-none mb-3">
                                <div class="card-header bg-light-subtle py-2">
                                    <h6 class="card-title mb-0">
                                        <i class="ri-bank-card-line me-2 text-primary"></i>Repayment Details
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tbody>
                                                <tr>
                                                    <td width="50%" class="fw-medium">Principal Amount:</td>
                                                    <td>KES <?php echo number_format($inputCredit->total_amount, 2); ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-medium">Interest Amount
                                                        (<?php echo $inputCredit->credit_percentage; ?>%):</td>
                                                    <td>KES
                                                        <?php echo number_format($inputCredit->total_with_interest - $inputCredit->total_amount, 2); ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-medium">Total Repayable:</td>
                                                    <td class="fw-semibold text-primary">KES
                                                        <?php echo number_format($inputCredit->total_with_interest, 2); ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-medium">Repayment Method:</td>
                                                    <td>Automatic deductions from produce sales</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-medium">Deduction Rate:</td>
                                                    <td><?php echo $inputCredit->repayment_percentage; ?>% of each
                                                        produce sale</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-medium">Status:</td>
                                                    <td><span class="badge bg-warning">Pending Fulfillment</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info mt-4 mb-0">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <i class="ri-information-line fs-24"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Repayment Process</h6>
                                        <p class="mb-0">
                                            Once you collect your inputs,
                                            <?php echo $inputCredit->repayment_percentage; ?>% of each produce sale will
                                            be automatically
                                            deducted toward repayment of this input credit. The more frequently you sell
                                            produce, the faster
                                            you'll clear your balance.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <?php elseif($inputCredit->status == 'fulfilled' || $inputCredit->status == 'completed'): ?>
                            <!-- For fulfilled/completed applications -->
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="mb-0">Repayment Progress</h6>
                                <span
                                    class="badge <?php echo ($inputCredit->status == 'completed') ? 'bg-success' : 'bg-primary'; ?> rounded-pill">
                                    <?php echo ($inputCredit->status == 'completed') ? 'Completed' : 'Active'; ?>
                                </span>
                            </div>

                            <?php 
                // Calculate repayment progress
                $paidAmount = $inputCredit->total_with_interest - ($inputCredit->remaining_balance ?? 0);
                $repaymentProgress = ($inputCredit->total_with_interest > 0) 
                    ? round(($paidAmount / $inputCredit->total_with_interest) * 100) 
                    : 0;
                ?>

                            <!-- Repayment progress bar -->
                            <div class="p-4 border rounded-3 bg-light-subtle mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted fs-12">Progress</span>
                                    <span class="fs-12 fw-semibold"><?php echo $repaymentProgress; ?>% Complete</span>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: <?php echo $repaymentProgress; ?>%"
                                        aria-valuenow="<?php echo $repaymentProgress; ?>" aria-valuemin="0"
                                        aria-valuemax="100">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        <span class="d-block text-muted fs-12">Paid</span>
                                        <span class="fw-semibold text-success">KES
                                            <?php echo number_format($paidAmount, 2); ?></span>
                                    </div>
                                    <div class="text-end">
                                        <span class="d-block text-muted fs-12">Remaining</span>
                                        <span
                                            class="fw-semibold <?php echo ($inputCredit->status == 'completed') ? 'text-success' : 'text-danger'; ?>">
                                            KES <?php echo number_format($inputCredit->remaining_balance ?? 0, 2); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Detailed repayment information -->
                            <div class="card border shadow-none mb-3">
                                <div class="card-header bg-light-subtle py-2">
                                    <h6 class="card-title mb-0">
                                        <i class="ri-bank-card-line me-2 text-primary"></i>Repayment Details
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tbody>
                                                <tr>
                                                    <td width="50%" class="fw-medium">Principal Amount:</td>
                                                    <td>KES <?php echo number_format($inputCredit->total_amount, 2); ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-medium">Interest Amount
                                                        (<?php echo $inputCredit->credit_percentage; ?>%):</td>
                                                    <td>KES
                                                        <?php echo number_format($inputCredit->total_with_interest - $inputCredit->total_amount, 2); ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-medium">Total Repayable:</td>
                                                    <td>KES
                                                        <?php echo number_format($inputCredit->total_with_interest, 2); ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-medium">Amount Paid:</td>
                                                    <td class="text-success fw-semibold">KES
                                                        <?php echo number_format($paidAmount, 2); ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-medium">Remaining Balance:</td>
                                                    <td
                                                        class="<?php echo ($inputCredit->status == 'completed') ? 'text-success' : 'text-danger'; ?> fw-semibold">
                                                        KES
                                                        <?php echo number_format($inputCredit->remaining_balance ?? 0, 2); ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-medium">Deduction Rate:</td>
                                                    <td><?php echo $inputCredit->repayment_percentage; ?>% of each
                                                        produce sale</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <?php if($inputCredit->status == 'completed'): ?>
                            <!-- For completed repayments -->
                            <div class="alert alert-success mt-4 mb-0">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <i class="ri-trophy-line fs-24"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Input Credit Fully Repaid</h6>
                                        <p class="mb-0">
                                            Congratulations! You have successfully repaid your input credit in full.
                                            This will positively impact your creditworthiness for future input credits.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php else: ?>
                            <!-- For active repayments -->
                            <div class="alert alert-primary mt-4 mb-0">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <i class="ri-information-line fs-24"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Repayment Information</h6>
                                        <p class="mb-0">
                                            Input credit repayments are automatically deducted from your produce sales.
                                            <?php echo $inputCredit->repayment_percentage; ?>% of each sale is applied
                                            toward your credit balance.
                                            You can also make additional payments to clear your credit faster.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 4: Input Credit Application Timeline -->
            <div class="row">
                <div class="col-12">
                    <div class="card custom-card mb-4">
                        <div class="card-header border-bottom">
                            <div class="card-title">
                                <i class="ri-timeline-line me-2 text-success"></i>Input Credit Application Timeline
                            </div>
                        </div>
                        <div class="card-body px-4">
                            <?php 
                                           // Get timeline data from input_credit_logs
                                           $timelineQuery = "SELECT 
                                                           icl.action_type,
                                                           icl.description,
                                                           icl.created_at,
                                                           CONCAT(u.first_name, ' ', u.last_name) as user_name,
                                                           r.name as role_name
                                                         FROM input_credit_logs icl
                                                         JOIN users u ON icl.user_id = u.id
                                                         JOIN roles r ON u.role_id = r.id
                                                         WHERE icl.input_credit_application_id = '{$inputCreditId}'
                                                         ORDER BY icl.created_at ASC";
                                          
                                           $timeline = $app->select_all($timelineQuery);
                                           
                                           // Define all possible steps in order
                                           $steps = [
                                               [
                                                   'key' => 'application',
                                                   'label' => 'Application Submitted', 
                                                   'description' => 'Input credit request submitted',
                                                   'icon' => 'ri-file-text-line'
                                               ],
                                               [
                                                   'key' => 'assessment',
                                                   'label' => 'Credit Assessment', 
                                                   'description' => 'Application being evaluated',
                                                   'icon' => 'ri-search-line'
                                               ],
                                               [
                                                   'key' => 'approval',
                                                   'label' => 'Approval Decision', 
                                                   'description' => 'Application approved/rejected',
                                                   'icon' => 'ri-check-double-line'
                                               ],
                                               [
                                                   'key' => 'fulfillment',
                                                   'label' => 'Input Fulfillment', 
                                                   'description' => 'Agricultural inputs delivered',
                                                   'icon' => 'ri-shopping-bag-line'
                                               ],
                                               [
                                                   'key' => 'repayment',
                                                   'label' => 'Repayment Period', 
                                                   'description' => 'Repayments through produce',
                                                   'icon' => 'ri-exchange-funds-line'
                                               ],
                                               [
                                                   'key' => 'completion',
                                                   'label' => 'Credit Completed', 
                                                   'description' => 'Input credit fully repaid',
                                                   'icon' => 'ri-trophy-line'
                                               ]
                                           ];
                                           
                                           // Determine current step based on status
                                           $currentStep = 0;
                                           if ($inputCredit->status == 'pending' || $inputCredit->status == 'under_review') {
                                               $currentStep = 1; // Assessment stage
                                           } elseif ($inputCredit->status == 'rejected') {
                                               $currentStep = 2; // Stopped at approval stage with rejection
                                           } elseif ($inputCredit->status == 'approved') {
                                               $currentStep = 3; // Awaiting fulfillment
                                           } elseif ($inputCredit->status == 'fulfilled') {
                                               $currentStep = 4; // Repayment stage
                                           } elseif ($inputCredit->status == 'completed') {
                                               $currentStep = 5; // Completed stage
                                           }
                                           
                                           // Calculate progress percentage
                                           $totalSteps = count($steps) - 1; // -1 because we start from 0
                                           $progressPercentage = ($currentStep / $totalSteps) * 100;
                                           ?>

                            <!-- Overall progress bar -->
                            <div class="progress mb-5"
                                style="height: 8px; background-color: #eef2f7; border-radius: 20px;">
                                <div class="progress-bar <?php echo ($inputCredit->status == 'rejected') ? 'bg-danger' : 'bg-success'; ?>"
                                    role="progressbar"
                                    style="width: <?php echo $progressPercentage; ?>%; border-radius: 20px;"
                                    aria-valuenow="<?php echo $progressPercentage; ?>" aria-valuemin="0"
                                    aria-valuemax="100"></div>
                            </div>

                            <!-- Interactive Timeline with Icons and Connection Line -->
                            <div class="row position-relative">
                                <?php foreach ($steps as $index => $step): 
                        // Determine step status
                        $stepStatus = '';
                        $isPastStep = $index <= $currentStep && $index < 5;
                        $isCurrentStep = $index == $currentStep;
                        $isRejected = $inputCredit->status == 'rejected' && $index == 2;
                        
                        if ($isPastStep && !$isRejected) {
                            $stepStatus = 'completed';
                        } elseif ($isCurrentStep) {
                            $stepStatus = $isRejected ? 'rejected' : 'active';
                        }
                        
                        // If input credit is rejected, only steps before rejection are completed
                        if ($inputCredit->status == 'rejected' && $index > 2) {
                            $stepStatus = '';
                        }
                    ?>
                                <div class="col-md-2 text-center mb-4">
                                    <div class="position-relative">
                                        <!-- Circle Icon -->
                                        <div class="mx-auto" style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
                                                          background-color: <?php 
                                                              if ($stepStatus == 'completed') echo '#28a745';
                                                              elseif ($stepStatus == 'active') echo '#FFC107';
                                                              elseif ($stepStatus == 'rejected') echo '#DC3545';
                                                              else echo '#e9ecef';
                                                          ?>;
                                                          color: <?php echo ($stepStatus) ? '#fff' : '#6c757d'; ?>;
                                                          box-shadow: 0 3px 10px rgba(0,0,0,0.1);
                                                          position: relative;
                                                          z-index: 2;">
                                            <i class="<?php echo $step['icon']; ?> fs-24"></i>

                                            <?php if ($stepStatus == 'completed'): ?>
                                            <!-- Checkmark for completed steps -->
                                            <div
                                                style="position: absolute; bottom: -5px; right: -5px; width: 24px; height: 24px; 
                                                              border-radius: 50%; background-color: #28a745; color: white; 
                                                              display: flex; align-items: center; justify-content: center;">
                                                <i class="ri-check-line fs-14"></i>
                                            </div>
                                            <?php endif; ?>

                                            <?php if ($stepStatus == 'rejected'): ?>
                                            <!-- X mark for rejection -->
                                            <div
                                                style="position: absolute; bottom: -5px; right: -5px; width: 24px; height: 24px; 
                                                              border-radius: 50%; background-color: #dc3545; color: white; 
                                                              display: flex; align-items: center; justify-content: center;">
                                                <i class="ri-close-line fs-14"></i>
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

                            <!-- Detailed Timeline Events -->
                            <div class="mt-5">
                                <h6 class="fw-semibold mb-3">Application History</h6>

                                <?php if (empty($timeline)): ?>
                                <div class="text-center py-4">
                                    <div class="avatar avatar-lg mx-auto mb-3">
                                        <i class="ri-history-line fs-2 text-muted"></i>
                                    </div>
                                    <h6 class="text-muted">No Timeline Events Yet</h6>
                                    <p class="text-muted mb-0">Application history will be displayed here as it
                                        progresses.</p>
                                </div>
                                <?php else: ?>

                                <div class="timeline-main-container">
                                    <div class="timeline-container">
                                        <?php foreach ($timeline as $index => $event): 
                                                     // Set icon and color based on action type
                                                     $icon = 'ri-information-line';
                                                     $color = '#6c757d';
                                                     $bgColor = '#f8f9fa';
                                                     
                                                     switch ($event->action_type) {
                                                         case 'application_submitted':
                                                             $icon = 'ri-file-text-line';
                                                             $color = '#28a745';
                                                             $bgColor = 'rgba(40, 167, 69, 0.1)';
                                                             break;
                                                         case 'creditworthiness_check':
                                                             $icon = 'ri-bar-chart-line';
                                                             $color = '#17a2b8';
                                                             $bgColor = 'rgba(23, 162, 184, 0.1)';
                                                             break;
                                                         case 'under_review':
                                                             $icon = 'ri-search-line';
                                                             $color = '#fd7e14';
                                                             $bgColor = 'rgba(253, 126, 20, 0.1)';
                                                             break;
                                                         case 'approved':
                                                             $icon = 'ri-check-double-line';
                                                             $color = '#28a745';
                                                             $bgColor = 'rgba(40, 167, 69, 0.1)';
                                                             break;
                                                         case 'rejected':
                                                             $icon = 'ri-close-circle-line';
                                                             $color = '#dc3545';
                                                             $bgColor = 'rgba(220, 53, 69, 0.1)';
                                                             break;
                                                         case 'fulfilled':
                                                             $icon = 'ri-shopping-bag-line';
                                                             $color = '#6610f2';
                                                             $bgColor = 'rgba(102, 16, 242, 0.1)';
                                                             break;
                                                         case 'payment_made':
                                                             $icon = 'ri-exchange-funds-line';
                                                             $color = '#20c997';
                                                             $bgColor = 'rgba(32, 201, 151, 0.1)';
                                                             break;
                                                         case 'completed':
                                                             $icon = 'ri-trophy-line';
                                                             $color = '#28a745';
                                                             $bgColor = 'rgba(40, 167, 69, 0.1)';
                                                             break;
                                                         case 'defaulted':
                                                             $icon = 'ri-error-warning-line';
                                                             $color = '#dc3545';
                                                             $bgColor = 'rgba(220, 53, 69, 0.1)';
                                                             break;
                                                         default:
                                                             $icon = 'ri-information-line';
                                                             $color = '#6c757d';
                                                             $bgColor = 'rgba(108, 117, 125, 0.1)';
                                                     }
                                                 ?>
                                        <div class="timeline-block <?php echo ($index !== 0) ? 'mt-4' : ''; ?>">
                                            <div class="timeline-content">
                                                <span class="timeline-icon"
                                                    style="background-color: <?php echo $bgColor; ?>; color: <?php echo $color; ?>;">
                                                    <i class="<?php echo $icon; ?> fs-18"></i>
                                                </span>
                                                <div class="align-items-center d-flex timeline-indicator-text">
                                                    <span class="fw-semibold me-1">
                                                        <?php echo ucwords(str_replace('_', ' ', $event->action_type)); ?>
                                                    </span>
                                                    <span class="badge bg-light text-dark badge-sm">
                                                        <?php echo date('M d, Y', strtotime($event->created_at)); ?>
                                                    </span>
                                                </div>
                                                <div class="p-3 border-start border-2 ms-4 mt-2"
                                                    style="border-color: #e9e9e9 !important; background-color: #f9f9f9; border-radius: 6px;">
                                                    <?php echo htmlspecialchars($event->description); ?>

                                                    <div class="mt-2 d-flex align-items-center">
                                                        <div class="avatar avatar-xs avatar-rounded bg-light me-2">
                                                            <i class="ri-user-line fs-12 text-primary"></i>
                                                        </div>
                                                        <span
                                                            class="text-muted fs-12"><?php echo htmlspecialchars($event->user_name); ?></span>
                                                        <span
                                                            class="badge bg-light text-dark ms-2 fs-12"><?php echo ucfirst($event->role_name); ?></span>
                                                        <span
                                                            class="text-muted ms-auto fs-12"><?php echo date('h:i A', strtotime($event->created_at)); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Current Status Summary Box -->
                            <?php
                                $alertClass = 'alert-warning';
                                $iconClass = 'ri-time-line';
                                $statusTitle = 'Application Under Review';
                                $statusMessage = 'Your input credit application is currently being evaluated by the agrovet.';
                                
                                if ($inputCredit->status == 'pending') {
                                    $alertClass = 'alert-secondary';
                                    $iconClass = 'ri-file-text-line';
                                    $statusTitle = 'Application Submitted';
                                    $statusMessage = 'Your input credit application has been received and is awaiting initial screening.';
                                }
                                else if ($inputCredit->status == 'rejected') {
                                    $alertClass = 'alert-danger';
                                    $iconClass = 'ri-error-warning-line';
                                    $statusTitle = 'Application Rejected';
                                    $reasonText = $inputCredit->rejection_reason ?? 'Please contact the agrovet for more details.';
                                    $statusMessage = "Your input credit application has been rejected. Reason: {$reasonText}";
                                } 
                                else if ($inputCredit->status == 'approved') {
                                    $alertClass = 'alert-info';
                                    $iconClass = 'ri-check-double-line';
                                    $statusTitle = 'Application Approved';
                                    $statusMessage = 'Your input credit application has been approved. Inputs will be ready for collection soon.';
                                }
                                else if ($inputCredit->status == 'fulfilled') {
                                    $alertClass = 'alert-primary';
                                    $iconClass = 'ri-shopping-bag-line';
                                    $statusTitle = 'Inputs Delivered';
                                    $statusMessage = 'Your agricultural inputs have been delivered. Repayments will be deducted from your produce sales.';
                                }
                                else if ($inputCredit->status == 'completed') {
                                    $alertClass = 'alert-success';
                                    $iconClass = 'ri-trophy-line';
                                    $statusTitle = 'Credit Fully Repaid';
                                    $statusMessage = 'Congratulations! You have successfully completed your input credit repayment.';
                                }
                                ?>

                            <div class="alert <?php echo $alertClass; ?> mt-4 mb-0">
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
            <!-- Row 5: Activity History & Input Usage -->
            <div class="row">
                <div class="col-12">
                    <div class="card custom-card mb-4">
                        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                            <div class="card-title mb-0">
                                <i class="ri-history-line me-2 text-success"></i>Activity History & Input Usage
                            </div>
                            <div>
                                <button class="btn btn-sm btn-outline-success" id="refreshActivityLogs">
                                    <i class="ri-refresh-line me-1"></i> Refresh
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Nav tabs for different views -->
                            <ul class="nav nav-tabs mb-4" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#activityLogs">
                                        <i class="ri-file-list-line me-1"></i> Activity Logs
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#repayments">
                                        <i class="ri-exchange-funds-line me-1"></i> Repayments
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#inputUsage">
                                        <i class="ri-plant-line me-1"></i> Input Usage & Outcomes
                                    </a>
                                </li>
                            </ul>

                            <!-- Tab content -->
                            <div class="tab-content">
                                <!-- Activity Logs Tab -->
                                <div class="tab-pane fade show active" id="activityLogs">
                                    <?php
                        // Fetch all activity logs related to this input credit
                        $logQuery = "SELECT 
                                      icl.id,
                                      icl.action_type,
                                      icl.description,
                                      icl.created_at,
                                      CONCAT(u.first_name, ' ', u.last_name) as user_name,
                                      r.name as role_name
                                   FROM input_credit_logs icl
                                   JOIN users u ON icl.user_id = u.id
                                   JOIN roles r ON u.role_id = r.id
                                   WHERE icl.input_credit_application_id = '{$inputCreditId}'
                                   ORDER BY icl.created_at DESC";
                        
                        $logs = $app->select_all($logQuery);
                        
                        if (empty($logs)): 
                        ?>
                                    <div class="text-center py-5">
                                        <div class="avatar avatar-lg mx-auto mb-3 bg-light">
                                            <i class="ri-history-line fs-2 text-muted"></i>
                                        </div>
                                        <h5 class="text-muted">No Activity Logs Yet</h5>
                                        <p class="text-muted">There are no recorded activities for this input credit
                                            application yet.</p>
                                    </div>
                                    <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover border">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th style="width: 15%;">Date</th>
                                                    <th style="width: 15%;">Activity</th>
                                                    <th style="width: 50%;">Description</th>
                                                    <th style="width: 20%;">User</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($logs as $log): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <span
                                                                class="fw-medium"><?php echo date('M d, Y', strtotime($log->created_at)); ?></span>
                                                            <small
                                                                class="text-muted"><?php echo date('h:i A', strtotime($log->created_at)); ?></small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?php 
                                            $activityClass = 'secondary';
                                            $activityIcon = 'information-line';
                                            
                                            switch ($log->action_type) {
                                                case 'application_submitted':
                                                    $activityClass = 'primary';
                                                    $activityIcon = 'file-text-line';
                                                    break;
                                                case 'creditworthiness_check':
                                                    $activityClass = 'info';
                                                    $activityIcon = 'bar-chart-line';
                                                    break;
                                                case 'under_review':
                                                    $activityClass = 'warning';
                                                    $activityIcon = 'search-line';
                                                    break;
                                                case 'approved':
                                                    $activityClass = 'success';
                                                    $activityIcon = 'check-double-line';
                                                    break;
                                                case 'rejected':
                                                    $activityClass = 'danger';
                                                    $activityIcon = 'close-circle-line';
                                                    break;
                                                case 'fulfilled':
                                                    $activityClass = 'primary';
                                                    $activityIcon = 'shopping-bag-line';
                                                    break;
                                                case 'payment_made':
                                                    $activityClass = 'success';
                                                    $activityIcon = 'exchange-funds-line';
                                                    break;
                                                case 'completed':
                                                    $activityClass = 'success';
                                                    $activityIcon = 'trophy-line';
                                                    break;
                                                case 'defaulted':
                                                    $activityClass = 'danger';
                                                    $activityIcon = 'error-warning-line';
                                                    break;
                                            }
                                            ?>
                                                        <span
                                                            class="badge bg-<?php echo $activityClass; ?>-transparent text-<?php echo $activityClass; ?> py-1 px-2">
                                                            <i class="ri-<?php echo $activityIcon; ?> me-1"></i>
                                                            <?php echo ucwords(str_replace('_', ' ', $log->action_type)); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <p class="mb-0">
                                                            <?php echo htmlspecialchars($log->description); ?></p>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar avatar-xs avatar-rounded bg-light me-2">
                                                                <i class="ri-user-line fs-12 text-primary"></i>
                                                            </div>
                                                            <div>
                                                                <span
                                                                    class="d-block"><?php echo htmlspecialchars($log->user_name); ?></span>
                                                                <small
                                                                    class="text-muted"><?php echo ucfirst($log->role_name); ?></small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Repayments Tab -->
                                <div class="tab-pane fade" id="repayments">
                                    <?php
                        // Only show repayments if the application is fulfilled or completed
                        if ($inputCredit->status == 'fulfilled' || $inputCredit->status == 'completed'):
                            
                            // Fetch repayment records
                            $repaymentQuery = "SELECT 
                                              icr.id,
                                              icr.produce_delivery_id,
                                              icr.produce_sale_amount,
                                              icr.deducted_amount,
                                              icr.amount,
                                              icr.deduction_date,
                                              pd.quantity,
                                              pt.name as product_name
                                           FROM input_credit_repayments icr
                                           JOIN approved_input_credits aic ON icr.approved_credit_id = aic.id
                                           JOIN produce_deliveries pd ON icr.produce_delivery_id = pd.id
                                           JOIN farm_products fp ON pd.farm_product_id = fp.id
                                           JOIN product_types pt ON fp.product_type_id = pt.id
                                           WHERE aic.credit_application_id = '{$inputCreditId}'
                                           ORDER BY icr.deduction_date DESC";
                            
                            $repayments = $app->select_all($repaymentQuery);
                            
                            if (empty($repayments)):
                        ?>
                                    <div class="alert alert-info">
                                        <div class="d-flex">
                                            <div class="me-3">
                                                <i class="ri-information-line fs-24"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">No Repayments Made Yet</h6>
                                                <p class="mb-0">
                                                    Repayments will be automatically deducted from your produce sales at
                                                    a rate of
                                                    <?php echo $inputCredit->repayment_percentage; ?>% per sale. As you
                                                    deliver produce, repayment
                                                    records will appear here.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tips for timely repayment -->
                                    <div class="card border mt-4">
                                        <div class="card-header bg-light-subtle">
                                            <h6 class="card-title mb-0">
                                                <i class="ri-lightbulb-line me-2 text-warning"></i>Tips for Timely
                                                Repayment
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="d-flex mb-3">
                                                        <div class="avatar avatar-sm bg-success-transparent me-3">
                                                            <i class="ri-plant-line text-success"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-1">Regular Production</h6>
                                                            <p class="mb-0 fs-12 text-muted">Maintain consistent
                                                                production cycles to ensure regular produce deliveries.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="d-flex mb-3">
                                                        <div class="avatar avatar-sm bg-primary-transparent me-3">
                                                            <i class="ri-truck-line text-primary"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-1">Timely Deliveries</h6>
                                                            <p class="mb-0 fs-12 text-muted">Deliver your produce as
                                                                soon as it's ready to maintain cash flow.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="d-flex mb-3">
                                                        <div class="avatar avatar-sm bg-warning-transparent me-3">
                                                            <i class="ri-calendar-line text-warning"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-1">Plan Your Production</h6>
                                                            <p class="mb-0 fs-12 text-muted">Plan your farming
                                                                activities to match repayment schedule.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="d-flex mb-3">
                                                        <div class="avatar avatar-sm bg-info-transparent me-3">
                                                            <i class="ri-money-dollar-circle-line text-info"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-1">Additional Payments</h6>
                                                            <p class="mb-0 fs-12 text-muted">You can make additional
                                                                payments to clear your credit faster.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <?php else: ?>
                                    <!-- Repayment summary stats -->
                                    <div class="row mb-4">
                                        <?php 
                            // Calculate total repaid amount
                            $totalRepaid = 0;
                            foreach ($repayments as $repayment) {
                                $totalRepaid += $repayment->amount;
                            }
                            
                            // Calculate progress percentage
                            $repaymentProgress = ($inputCredit->total_with_interest > 0) 
                                ? round(($totalRepaid / $inputCredit->total_with_interest) * 100) 
                                : 0;
                            ?>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="card border shadow-none">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm bg-success-transparent me-3">
                                                            <i class="ri-money-dollar-circle-line text-success"></i>
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 text-muted fs-12">Total Repaid</p>
                                                            <h5 class="fw-semibold mb-0 text-success">
                                                                KES <?php echo number_format($totalRepaid, 2); ?>
                                                            </h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="card border shadow-none">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm bg-danger-transparent me-3">
                                                            <i class="ri-bank-card-line text-danger"></i>
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 text-muted fs-12">Remaining</p>
                                                            <h5
                                                                class="fw-semibold mb-0 <?php echo ($inputCredit->status == 'completed') ? 'text-success' : 'text-danger'; ?>">
                                                                KES
                                                                <?php echo number_format($inputCredit->remaining_balance ?? 0, 2); ?>
                                                            </h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="card border shadow-none">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm bg-primary-transparent me-3">
                                                            <i class="ri-pie-chart-line text-primary"></i>
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 text-muted fs-12">Progress</p>
                                                            <h5 class="fw-semibold mb-0 text-primary">
                                                                <?php echo $repaymentProgress; ?>% Complete
                                                            </h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="card border shadow-none">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm bg-info-transparent me-3">
                                                            <i class="ri-calendar-check-line text-info"></i>
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 text-muted fs-12">Payments Made</p>
                                                            <h5 class="fw-semibold mb-0 text-info">
                                                                <?php echo count($repayments); ?> Payments
                                                            </h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Repayment records table -->
                                    <div class="table-responsive">
                                        <table class="table table-hover border">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Reference</th>
                                                    <th>Produce</th>
                                                    <th>Sale Amount</th>
                                                    <th>Deduction %</th>
                                                    <th>Amount Paid</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($repayments as $repayment): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <span
                                                                class="fw-medium"><?php echo date('M d, Y', strtotime($repayment->deduction_date)); ?></span>
                                                            <small
                                                                class="text-muted"><?php echo date('h:i A', strtotime($repayment->deduction_date)); ?></small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <a href="javascript:void(0);" class="text-primary fw-medium">
                                                            DLVR<?php echo str_pad($repayment->produce_delivery_id, 5, '0', STR_PAD_LEFT); ?>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span
                                                                class="avatar avatar-xs avatar-rounded bg-success-transparent me-2">
                                                                <i class="ri-plant-line text-success"></i>
                                                            </span>
                                                            <span>
                                                                <?php echo htmlspecialchars($repayment->product_name); ?>
                                                                <small
                                                                    class="text-muted d-block"><?php echo $repayment->quantity; ?>
                                                                    KGs</small>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td>KES
                                                        <?php echo number_format($repayment->produce_sale_amount, 2); ?>
                                                    </td>
                                                    <td><?php echo ($repayment->produce_sale_amount > 0) ? round(($repayment->deducted_amount / $repayment->produce_sale_amount) * 100) : 0; ?>%
                                                    </td>
                                                    <td>
                                                        <span class="fw-semibold text-success">
                                                            KES <?php echo number_format($repayment->amount, 2); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <?php if ($inputCredit->status == 'completed'): ?>
                                    <!-- Completion certificate for completed credits -->
                                    <div class="alert alert-success mt-4">
                                        <div class="d-flex">
                                            <div class="me-3">
                                                <i class="ri-trophy-line fs-24"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">Repayment Completed Successfully</h6>
                                                <p class="mb-0">
                                                    You have successfully completed all repayments for this input
                                                    credit.
                                                    Your excellent repayment history improves your creditworthiness for
                                                    future applications.
                                                </p>
                                                <a href="javascript:void(0);" class="btn btn-sm btn-light mt-2">
                                                    <i class="ri-file-download-line me-1"></i> Download Completion
                                                    Certificate
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <?php endif; ?>

                                    <?php else: ?>
                                    <!-- Message for non-fulfilled applications -->
                                    <div class="text-center py-5">
                                        <div class="avatar avatar-lg mx-auto mb-3 bg-light">
                                            <i class="ri-time-line fs-2 text-muted"></i>
                                        </div>
                                        <h5 class="text-muted">Repayment Not Started</h5>
                                        <p class="text-muted">
                                            Repayment information will be available once your input credit application
                                            is approved and fulfilled.
                                        </p>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Input Usage & Outcomes Tab -->
                                <div class="tab-pane fade" id="inputUsage">
                                    <?php if ($inputCredit->status == 'fulfilled' || $inputCredit->status == 'completed'): ?>
                                    <!-- For fulfilled applications, show input usage form and stats -->
                                    <div class="mb-4">
                                        <h6 class="fw-semibold mb-3">Input Usage Reporting</h6>
                                        <p class="text-muted">
                                            Tracking how you use your inputs helps us improve our services and provides
                                            valuable data for future recommendations.
                                            Please share information about how you've used the agricultural inputs
                                            provided.
                                        </p>
                                    </div>

                                    <!-- Input usage form -->
                                    <div class="card border shadow-none mb-4">
                                        <div class="card-header bg-light-subtle">
                                            <h6 class="card-title mb-0">
                                                <i class="ri-file-text-line me-2 text-primary"></i>Input Usage Report
                                                Form
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <form id="inputUsageForm">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Input Used</label>
                                                        <select class="form-select" id="inputUsed">
                                                            <option value="">Select Input</option>
                                                            <?php foreach ($inputItems as $item): ?>
                                                            <option value="<?php echo $item->id; ?>">
                                                                <?php echo htmlspecialchars($item->input_name); ?>
                                                            </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Date Used</label>
                                                        <input type="date" class="form-control" id="dateUsed">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Farm/Plot</label>
                                                        <input type="text" class="form-control" id="farmLocation"
                                                            placeholder="Enter farm location">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Crop/Product</label>
                                                        <input type="text" class="form-control" id="cropProduct"
                                                            placeholder="Enter crop or product">
                                                    </div>
                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label">Usage Details</label>
                                                        <textarea class="form-control" id="usageDetails" rows="3"
                                                            placeholder="Describe how you used this input and any results observed"></textarea>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <button type="button" class="btn btn-success"
                                                            id="submitUsageReport">
                                                            <i class="ri-check-line me-1"></i> Submit Report
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Productivity Impact Analysis -->
                                    <div class="card border shadow-none">
                                        <div class="card-header bg-light-subtle">
                                            <h6 class="card-title mb-0">
                                                <i class="ri-line-chart-line me-2 text-primary"></i>Productivity Impact
                                                Analysis
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="alert alert-light">
                                                <div class="d-flex">
                                                    <div class="me-3">
                                                        <i class="ri-information-line fs-24 text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">Track Your Productivity Gains</h6>
                                                        <p class="mb-0">
                                                            Submit usage reports to see how these inputs are affecting
                                                            your productivity.
                                                            Data from your produce deliveries will be automatically
                                                            analyzed to measure the impact of these inputs.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Placeholder for productivity analysis chart -->
                                            <div class="bg-light rounded p-4 text-center my-3">
                                                <i class="ri-bar-chart-grouped-line fs-3 text-muted mb-2 d-block"></i>
                                                <p class="text-muted mb-0">Productivity analysis will be generated based
                                                    on your produce deliveries after input usage.</p>
                                            </div>

                                            <!-- Expected outcomes -->
                                            <h6 class="fw-semibold mt-4 mb-3">Expected Outcomes</h6>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <div class="card border-0 bg-success-subtle p-3 h-100">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span
                                                                class="avatar avatar-xs avatar-rounded bg-success me-2">
                                                                <i class="ri-arrow-up-line text-white"></i>
                                                            </span>
                                                            <h6 class="mb-0">Increased Yield</h6>
                                                        </div>
                                                        <p class="mb-0 fs-12">
                                                            Proper use of quality inputs typically results in 15-30%
                                                            yield increase compared to traditional methods.
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <div class="card border-0 bg-primary-subtle p-3 h-100">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span
                                                                class="avatar avatar-xs avatar-rounded bg-primary me-2">
                                                                <i class="ri-recycle-line text-white"></i>
                                                            </span>
                                                            <h6 class="mb-0">Sustainable Farming</h6>
                                                        </div>
                                                        <p class="mb-0 fs-12">
                                                            Using balanced fertilizers and proper pest management
                                                            practices promotes long-term soil health.
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <div class="card border-0 bg-info-subtle p-3 h-100">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span class="avatar avatar-xs avatar-rounded bg-info me-2">
                                                                <i class="ri-hand-coin-line text-white"></i>
                                                            </span>
                                                            <h6 class="mb-0">Better ROI</h6>
                                                        </div>
                                                        <p class="mb-0 fs-12">
                                                            For every KES invested in quality inputs, expect a return of
                                                            2-3 KES in additional produce value.
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <div class="card border-0 bg-warning-subtle p-3 h-100">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span
                                                                class="avatar avatar-xs avatar-rounded bg-warning me-2">
                                                                <i class="ri-award-line text-white"></i>
                                                            </span>
                                                            <h6 class="mb-0">Quality Produce</h6>
                                                        </div>
                                                        <p class="mb-0 fs-12">
                                                            Proper input use leads to higher quality produce that can
                                                            command premium prices in the market.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <?php else: ?>
                                    <!-- Message for non-fulfilled applications -->
                                    <div class="text-center py-5">
                                        <div class="avatar avatar-lg mx-auto mb-3 bg-light">
                                            <i class="ri-seedling-line fs-2 text-muted"></i>
                                        </div>
                                        <h5 class="text-muted">Input Usage Not Started</h5>
                                        <p class="text-muted">
                                            Input usage information will be available once you receive your agricultural
                                            inputs.
                                        </p>
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
        crossorigin="anonymous" referrerpolicy="no-referrer">
    </script>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Tab functionality is handled by Bootstrap

        // Handle refresh button for activity logs
        document.getElementById('refreshActivityLogs').addEventListener('click', function() {
            // Show loading spinner
            const activityLogsTab = document.getElementById('activityLogs');
            activityLogsTab.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;

            // Reload the activity logs via AJAX
            setTimeout(function() {
                // This would be an AJAX call in a real implementation
                // For demo purposes, we'll just reload the page
                window.location.reload();
            }, 1000);
        });

        // Handle input usage report submission
        const submitUsageBtn = document.getElementById('submitUsageReport');
        if (submitUsageBtn) {
            submitUsageBtn.addEventListener('click', function() {
                const inputUsed = document.getElementById('inputUsed').value;
                const dateUsed = document.getElementById('dateUsed').value;
                const farmLocation = document.getElementById('farmLocation').value;
                const cropProduct = document.getElementById('cropProduct').value;
                const usageDetails = document.getElementById('usageDetails').value;

                // Validate form fields
                if (!inputUsed || !dateUsed || !farmLocation || !cropProduct || !usageDetails) {
                    alert('Please fill in all fields');
                    return;
                }

                // Show success message (in real implementation, this would be after AJAX call)
                alert('Usage report submitted successfully!');

                // Clear the form
                document.getElementById('inputUsageForm').reset();
            });
        }
    });
    </script>

    <script>
    // Script for PDF download functionality
    document.getElementById('downloadPDF').addEventListener('click', function() {
        // Show loading message with toastr
        toastr.info('Preparing your input credit statement for download...', 'Please wait', {
            "positionClass": "toast-top-right",
            "progressBar": true,
            "timeOut": 0,
            "extendedTimeOut": 0,
            "closeButton": false,
            "hideMethod": "fadeOut"
        });

        // Get the input credit ID from the page
        const inputCreditId = <?php echo $_GET['id'] ?>;

        // AJAX call to generate PDF
        $.ajax({
            url: "http://localhost/dfcs/ajax/input-credit-controller/generate-input-credit-statement-pdf.php",
            type: "POST",
            data: {
                inputCreditId: inputCreditId
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
                    let filename = 'Input_Credit_Statement_INPCR' + String(inputCreditId).padStart(
                        5, '0') + '.pdf';
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

                    toastr.success('Input credit statement downloaded successfully', 'Success', {
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
                            toastr.error(errorJson.error ||
                                'Failed to generate input credit statement', 'Error', {
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
                toastr.error('Failed to generate input credit statement. Please try again.',
                    'Error', {
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