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
                                   echo '<div class="alert alert-danger">No loan application ID specified</div>';
                                   exit;
                               }
                               
                               $app = new App;
                               $loanId = intval($_GET['id']);
                               $userId = $_SESSION['user_id'];
                               
                               // Fetch the loan application details with related information
                               $query = "SELECT 
                                           la.id,
                                           la.farmer_id,
                                           la.provider_type,
                                           la.loan_type_id,
                                           la.bank_id,
                                           la.amount_requested,
                                           la.term_requested,
                                           la.purpose,
                                           la.application_date,
                                           la.creditworthiness_score,
                                           la.status,
                                           la.rejection_reason,
                                           la.review_date,
                                           la.created_at,
                                           la.updated_at,
                                           lt.name as loan_type_name,
                                           lt.interest_rate,
                                           lt.processing_fee,
                                           CASE 
                                               WHEN la.status = 'approved' OR la.status = 'disbursed' OR la.status = 'completed' THEN 
                                                   (SELECT al.disbursement_date FROM approved_loans al WHERE al.loan_application_id = la.id)
                                               ELSE NULL
                                           END as disbursement_date,
                                           CASE 
                                               WHEN la.status = 'approved' OR la.status = 'disbursed' OR la.status = 'completed' THEN 
                                                   (SELECT al.total_repayment_amount FROM approved_loans al WHERE al.loan_application_id = la.id)
                                               ELSE NULL
                                           END as total_repayment_amount,
                                           CASE 
                                               WHEN la.status = 'approved' OR la.status = 'disbursed' OR la.status = 'completed' THEN 
                                                   (SELECT al.remaining_balance FROM approved_loans al WHERE al.loan_application_id = la.id)
                                               ELSE NULL
                                           END as remaining_balance,
                                           CASE 
                                               WHEN la.status = 'approved' OR la.status = 'disbursed' OR la.status = 'completed' THEN 
                                                   (SELECT al.expected_completion_date FROM approved_loans al WHERE al.loan_application_id = la.id)
                                               ELSE NULL
                                           END as expected_completion_date,
                                           CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                                           u.phone as farmer_phone,
                                           u.email as farmer_email,
                                           f.registration_number as farmer_registration,
                                           fc.name as farmer_category
                                         FROM loan_applications la
                                         JOIN loan_types lt ON la.loan_type_id = lt.id
                                         JOIN farmers f ON la.farmer_id = f.id
                                         JOIN farmer_categories fc ON f.category_id = fc.id
                                         JOIN users u ON f.user_id = u.id
                                         WHERE la.id = :loan_id";
                               
                               $params = [
                                   ':loan_id' => $loanId
                               ];
                               
                               $loan = $app->selectOne($query, $params);
                               
                               // Check if the loan exists and belongs to the current user
                               if (!$loan) {
                                   echo '<div class="alert alert-danger">Loan application not found</div>';
                                   exit;
                               }
                               
                               // Check if the user is the owner of this loan application
                               $checkOwnerQuery = "SELECT COUNT(*) as is_owner 
                                                  FROM loan_applications la 
                                                  JOIN farmers f ON la.farmer_id = f.id 
                                                  WHERE la.id = :loan_id AND f.user_id = :user_id";
                                                  
                               $ownerCheck = $app->selectOne($checkOwnerQuery, [
                                   ':loan_id' => $loanId,
                                   ':user_id' => $userId
                               ]);
                               
                               if (!$ownerCheck || $ownerCheck->is_owner == 0) {
                                   echo '<div class="alert alert-danger">You do not have permission to view this loan application</div>';
                                   exit;
                               }
                               
                               // Get creditworthiness breakdown from loan logs
                               $creditScoreQuery = "SELECT description 
                                                   FROM loan_logs 
                                                   WHERE loan_application_id = :loan_id 
                                                   AND action_type = 'creditworthiness_check' 
                                                   ORDER BY created_at DESC 
                                                   LIMIT 1";
                                                   
                               $creditScoreLog = $app->selectOne($creditScoreQuery, [':loan_id' => $loanId]);
                               
                               // Parse credit score components if available
                               $creditScores = [
                                   'repayment_history' => 0,
                                   'financial_obligations' => 0,
                                   'produce_history' => 0,
                                   'amount_ratio' => 0
                               ];
                               
                               if ($creditScoreLog && $creditScoreLog->description) {
                                   $description = $creditScoreLog->description;
                                   
                                   // Extract scores using regex
                                   preg_match('/Repayment history score: (\d+)/', $description, $repaymentMatches);
                                   preg_match('/Financial obligations score: (\d+)/', $description, $obligationsMatches);
                                   preg_match('/Produce history score: (\d+)/', $description, $produceMatches);
                                   preg_match('/Amount ratio score: (\d+)/', $description, $amountMatches);
                                   
                                   if (!empty($repaymentMatches)) $creditScores['repayment_history'] = intval($repaymentMatches[1]);
                                   if (!empty($obligationsMatches)) $creditScores['financial_obligations'] = intval($obligationsMatches[1]);
                                   if (!empty($produceMatches)) $creditScores['produce_history'] = intval($produceMatches[1]);
                                   if (!empty($amountMatches)) $creditScores['amount_ratio'] = intval($amountMatches[1]);
                               }
                               
                               // Format loan reference number
                               $loanReference = 'LOAN/' . date('Ymd', strtotime($loan->application_date)) . '/' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
                               
                               // Calculate months elapsed if loan is active
                               $monthsElapsed = 0;
                               if ($loan->status == 'disbursed' && $loan->disbursement_date) {
                                   $start = new DateTime($loan->disbursement_date);
                                   $end = new DateTime();
                                   $diff = $start->diff($end);
                                   $monthsElapsed = $diff->y * 12 + $diff->m;
                               }
                               
                               // Calculate repayment progress percentage
                               $repaymentProgress = 0;
                               if ($loan->status == 'disbursed' && $loan->total_repayment_amount > 0 && $loan->remaining_balance >= 0) {
                                   $paid = $loan->total_repayment_amount - $loan->remaining_balance;
                                   $repaymentProgress = ($paid / $loan->total_repayment_amount) * 100;
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
                                        LOAN<?php echo str_pad($loan->id, 5, '0', STR_PAD_LEFT); ?>
                                    </h5>
                                    <div class="d-flex align-items-center">
                                        <?php 
                            $statusClass = 'secondary';
                            $statusIcon = 'clock';
                            
                            if ($loan->status == 'under_review') {
                                $statusClass = 'primary';
                                $statusIcon = 'magnifying-glass';
                            } elseif ($loan->status == 'approved') {
                                $statusClass = 'info';
                                $statusIcon = 'check-double';
                            } elseif ($loan->status == 'disbursed') {
                                $statusClass = 'success';
                                $statusIcon = 'circle-check';
                            } elseif ($loan->status == 'rejected') {
                                $statusClass = 'danger';
                                $statusIcon = 'circle-xmark';
                            } elseif ($loan->status == 'completed') {
                                $statusClass = 'success';
                                $statusIcon = 'trophy';
                            } elseif ($loan->status == 'defaulted') {
                                $statusClass = 'danger';
                                $statusIcon = 'triangle-exclamation';
                            }
                            ?>
                                        <span class="badge bg-<?php echo $statusClass; ?>">
                                            <i class="ri-<?php echo $statusIcon; ?>-line me-1"></i>
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

                <!-- Card 2: Loan Type Details -->
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                    <div class="card custom-card mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="avatar avatar-md avatar-rounded bg-success">
                                        <i class="ri-bank-line fs-16"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted fs-12">Loan Type</p>
                                    <h5 class="fw-semibold mb-1"><?php echo htmlspecialchars($loan->loan_type_name); ?>
                                    </h5>
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
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                    <div class="card custom-card mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="avatar avatar-md avatar-rounded bg-warning">
                                        <i class="ri-money-dollar-circle-line fs-16"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted fs-12">Amount Requested</p>
                                    <h5 class="fw-semibold mb-1">KES
                                        <?php echo number_format($loan->amount_requested, 2); ?></h5>
                                    <div class="d-flex align-items-center">
                                        <span class="text-dark">
                                            <?php echo $loan->term_requested; ?> months term
                                        </span>
                                        <?php if($loan->status == 'approved' || $loan->status == 'disbursed' || $loan->status == 'completed'): ?>
                                        <span class="ms-2 text-success">
                                            <i class="ri-check-line"></i> Approved
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Creditworthiness Score -->
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
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
                        if ($loan->creditworthiness_score >= 70) {
                            $scoreClass = 'success';
                        } elseif ($loan->creditworthiness_score >= 50) {
                            $scoreClass = 'warning';
                        }
                        ?>
                                    <h5 class="fw-semibold mb-1 text-<?php echo $scoreClass; ?>">
                                        <?php echo $loan->creditworthiness_score; ?>/100
                                    </h5>
                                    <div class="progress mt-1" style="height: 6px;">
                                        <div class="progress-bar bg-<?php echo $scoreClass; ?>" role="progressbar"
                                            style="width: <?php echo $loan->creditworthiness_score; ?>%"
                                            aria-valuenow="<?php echo $loan->creditworthiness_score; ?>"
                                            aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Row 2: Creditworthiness Analysis -->
            <div class="row">
                <div class="col-12">
                    <div class="card custom-card mb-4">
                        <div class="card-header border-bottom">
                            <div class="card-title">
                                <i class="ri-bar-chart-line me-2 text-primary"></i>Creditworthiness Analysis
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Overall score display -->
                            <div class="text-center mb-4">
                                <?php 
                    $scoreClass = 'danger';
                    $scoreText = 'Poor';
                    
                    if ($loan->creditworthiness_score >= 85) {
                        $scoreClass = 'success';
                        $scoreText = 'Excellent';
                    } elseif ($loan->creditworthiness_score >= 70) {
                        $scoreClass = 'success';
                        $scoreText = 'Good';
                    } elseif ($loan->creditworthiness_score >= 50) {
                        $scoreClass = 'warning';
                        $scoreText = 'Fair';
                    }
                    ?>
                                <div class="d-inline-block position-relative">
                                    <div style="width: 120px; height: 120px;"
                                        class="rounded-circle border border-2 border-<?php echo $scoreClass; ?> d-flex align-items-center justify-content-center">
                                        <div class="text-center">
                                            <h2 class="mb-0 fw-bold text-<?php echo $scoreClass; ?>">
                                                <?php echo number_format($loan->creditworthiness_score, 1); ?></h2>
                                            <p class="mb-0 text-<?php echo $scoreClass; ?>"><?php echo $scoreText; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <h5 class="mt-3">Overall Creditworthiness Score</h5>
                                <p class="text-muted mb-0">
                                    This score determines your loan eligibility and terms.
                                    <?php if ($loan->creditworthiness_score >= 70): ?>
                                    <span class="text-success">Your score is good enough for loan approval!</span>
                                    <?php elseif ($loan->creditworthiness_score >= 50): ?>
                                    <span class="text-warning">Your score requires additional review by our
                                        staff.</span>
                                    <?php else: ?>
                                    <span class="text-danger">Your score is below our current approval threshold.</span>
                                    <?php endif; ?>
                                </p>
                            </div>

                            <!-- Detailed score breakdown -->
                            <div class="row mt-4">
                                <!-- Repayment History Score -->
                                <div class="col-md-6 col-lg-3 mb-4">
                                    <div class="card border shadow-none mb-0">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar avatar-sm me-2 bg-light">
                                                    <i class="ri-history-line text-primary"></i>
                                                </div>
                                                <h6 class="card-title mb-0">Repayment History</h6>
                                            </div>
                                            <?php 
                                $repaymentClass = 'danger';
                                if ($creditScores['repayment_history'] >= 70) {
                                    $repaymentClass = 'success';
                                } elseif ($creditScores['repayment_history'] >= 50) {
                                    $repaymentClass = 'warning';
                                }
                                ?>
                                            <div class="progress mb-2" style="height: 6px;">
                                                <div class="progress-bar bg-<?php echo $repaymentClass; ?>"
                                                    role="progressbar"
                                                    style="width: <?php echo $creditScores['repayment_history']; ?>%"
                                                    aria-valuenow="<?php echo $creditScores['repayment_history']; ?>"
                                                    aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-muted fs-12">Score (30%)</span>
                                                <span
                                                    class="fw-semibold text-<?php echo $repaymentClass; ?>"><?php echo $creditScores['repayment_history']; ?>/100</span>
                                            </div>
                                            <div class="mt-2 fs-12">
                                                <i class="ri-information-line me-1"></i>
                                                <?php if ($creditScores['repayment_history'] >= 70): ?>
                                                <span class="text-muted">Good history of timely repayments</span>
                                                <?php elseif ($creditScores['repayment_history'] == 0): ?>
                                                <span class="text-muted">No previous loan history</span>
                                                <?php else: ?>
                                                <span class="text-muted">Some issues with past loan repayments</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Financial Obligations Score -->
                                <div class="col-md-6 col-lg-3 mb-4">
                                    <div class="card border shadow-none mb-0">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar avatar-sm me-2 bg-light">
                                                    <i class="ri-wallet-line text-info"></i>
                                                </div>
                                                <h6 class="card-title mb-0">Financial Obligations</h6>
                                            </div>
                                            <?php 
                                $obligationsClass = 'danger';
                                if ($creditScores['financial_obligations'] >= 70) {
                                    $obligationsClass = 'success';
                                } elseif ($creditScores['financial_obligations'] >= 50) {
                                    $obligationsClass = 'warning';
                                }
                                ?>
                                            <div class="progress mb-2" style="height: 6px;">
                                                <div class="progress-bar bg-<?php echo $obligationsClass; ?>"
                                                    role="progressbar"
                                                    style="width: <?php echo $creditScores['financial_obligations']; ?>%"
                                                    aria-valuenow="<?php echo $creditScores['financial_obligations']; ?>"
                                                    aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-muted fs-12">Score (25%)</span>
                                                <span
                                                    class="fw-semibold text-<?php echo $obligationsClass; ?>"><?php echo $creditScores['financial_obligations']; ?>/100</span>
                                            </div>
                                            <div class="mt-2 fs-12">
                                                <i class="ri-information-line me-1"></i>
                                                <?php if ($creditScores['financial_obligations'] >= 70): ?>
                                                <span class="text-muted">Low debt-to-income ratio</span>
                                                <?php elseif ($creditScores['financial_obligations'] >= 50): ?>
                                                <span class="text-muted">Moderate existing financial obligations</span>
                                                <?php else: ?>
                                                <span class="text-muted">High existing debt burden</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Produce History Score -->
                                <div class="col-md-6 col-lg-3 mb-4">
                                    <div class="card border shadow-none mb-0">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar avatar-sm me-2 bg-light">
                                                    <i class="ri-plant-line text-success"></i>
                                                </div>
                                                <h6 class="card-title mb-0">Produce History</h6>
                                            </div>
                                            <?php 
                                $produceClass = 'danger';
                                if ($creditScores['produce_history'] >= 70) {
                                    $produceClass = 'success';
                                } elseif ($creditScores['produce_history'] >= 50) {
                                    $produceClass = 'warning';
                                }
                                ?>
                                            <div class="progress mb-2" style="height: 6px;">
                                                <div class="progress-bar bg-<?php echo $produceClass; ?>"
                                                    role="progressbar"
                                                    style="width: <?php echo $creditScores['produce_history']; ?>%"
                                                    aria-valuenow="<?php echo $creditScores['produce_history']; ?>"
                                                    aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-muted fs-12">Score (35%)</span>
                                                <span
                                                    class="fw-semibold text-<?php echo $produceClass; ?>"><?php echo $creditScores['produce_history']; ?>/100</span>
                                            </div>
                                            <div class="mt-2 fs-12">
                                                <i class="ri-information-line me-1"></i>
                                                <?php if ($creditScores['produce_history'] >= 70): ?>
                                                <span class="text-muted">Strong history of consistent deliveries</span>
                                                <?php elseif ($creditScores['produce_history'] >= 50): ?>
                                                <span class="text-muted">Average produce delivery history</span>
                                                <?php else: ?>
                                                <span class="text-muted">Limited or inconsistent produce
                                                    deliveries</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Amount Ratio Score -->
                                <div class="col-md-6 col-lg-3 mb-4">
                                    <div class="card border shadow-none mb-0">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar avatar-sm me-2 bg-light">
                                                    <i class="ri-scales-3-line text-warning"></i>
                                                </div>
                                                <h6 class="card-title mb-0">Amount Ratio</h6>
                                            </div>
                                            <?php 
                                $amountClass = 'danger';
                                if ($creditScores['amount_ratio'] >= 70) {
                                    $amountClass = 'success';
                                } elseif ($creditScores['amount_ratio'] >= 50) {
                                    $amountClass = 'warning';
                                }
                                ?>
                                            <div class="progress mb-2" style="height: 6px;">
                                                <div class="progress-bar bg-<?php echo $amountClass; ?>"
                                                    role="progressbar"
                                                    style="width: <?php echo $creditScores['amount_ratio']; ?>%"
                                                    aria-valuenow="<?php echo $creditScores['amount_ratio']; ?>"
                                                    aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-muted fs-12">Score (10%)</span>
                                                <span
                                                    class="fw-semibold text-<?php echo $amountClass; ?>"><?php echo $creditScores['amount_ratio']; ?>/100</span>
                                            </div>
                                            <div class="mt-2 fs-12">
                                                <i class="ri-information-line me-1"></i>
                                                <?php if ($creditScores['amount_ratio'] >= 70): ?>
                                                <span class="text-muted">Loan amount well aligned with income</span>
                                                <?php elseif ($creditScores['amount_ratio'] >= 50): ?>
                                                <span class="text-muted">Moderate loan-to-income ratio</span>
                                                <?php else: ?>
                                                <span class="text-muted">High loan amount compared to income</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Explanation cards -->
                            <div class="row mt-2">
                                <div class="col-12">
                                    <div class="alert alert-light border p-3">
                                        <h6 class="mb-2"><i class="ri-information-line me-2 text-primary"></i>How Your
                                            Score Is Calculated</h6>
                                        <p class="mb-0 text-muted">Your creditworthiness score is determined by four key
                                            factors, weighted as follows:</p>
                                        <ul class="text-muted mt-2 mb-0">
                                            <li><strong>Repayment History (30%)</strong>: Your track record of repaying
                                                previous loans</li>
                                            <li><strong>Financial Obligations (25%)</strong>: Your current debt compared
                                                to your income</li>
                                            <li><strong>Produce History (35%)</strong>: The consistency and value of
                                                your produce deliveries</li>
                                            <li><strong>Amount Ratio (10%)</strong>: How the requested loan amount
                                                compares to your average income</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- How to improve section -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6 class="mb-3">How to Improve Your Score</h6>

                                    <div class="row g-3">
                                        <?php if ($creditScores['repayment_history'] < 70): ?>
                                        <div class="col-md-6">
                                            <div class="d-flex p-3 rounded-3 bg-light">
                                                <div class="me-3 text-primary">
                                                    <i class="ri-check-double-line fs-24"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">Make Timely Repayments</h6>
                                                    <p class="mb-0 text-muted fs-12">Consistently repay any existing
                                                        loans on time to build a positive credit history.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <?php if ($creditScores['financial_obligations'] < 70): ?>
                                        <div class="col-md-6">
                                            <div class="d-flex p-3 rounded-3 bg-light">
                                                <div class="me-3 text-info">
                                                    <i class="ri-wallet-line fs-24"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">Reduce Existing Debt</h6>
                                                    <p class="mb-0 text-muted fs-12">Work on paying down existing loans
                                                        to improve your debt-to-income ratio.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <?php if ($creditScores['produce_history'] < 70): ?>
                                        <div class="col-md-6">
                                            <div class="d-flex p-3 rounded-3 bg-light">
                                                <div class="me-3 text-success">
                                                    <i class="ri-plant-line fs-24"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">Increase Produce Deliveries</h6>
                                                    <p class="mb-0 text-muted fs-12">Maintain regular and consistent
                                                        produce deliveries to the SACCO.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <?php if ($creditScores['amount_ratio'] < 70): ?>
                                        <div class="col-md-6">
                                            <div class="d-flex p-3 rounded-3 bg-light">
                                                <div class="me-3 text-warning">
                                                    <i class="ri-scales-3-line fs-24"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">Request Appropriate Loan Amounts</h6>
                                                    <p class="mb-0 text-muted fs-12">Apply for loan amounts that are
                                                        proportional to your income and production capacity.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 3: Loan Details & Terms -->
            <div class="row">
                <!-- Left Column: Loan Details -->
                <div class="col-xl-6 col-lg-6">
                    <div class="card custom-card mb-4">
                        <div class="card-header border-bottom">
                            <div class="card-title">
                                <i class="ri-file-info-line me-2 text-primary"></i>Loan Details
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td class="fw-medium bg-light" width="40%">Loan Type</td>
                                            <td><?php echo htmlspecialchars($loan->loan_type_name); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium bg-light">Provider</td>
                                            <td><?php echo ucfirst($loan->provider_type); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium bg-light">Amount Requested</td>
                                            <td>
                                                <span class="text-success fw-semibold">
                                                    KES <?php echo number_format($loan->amount_requested, 2); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium bg-light">Loan Term</td>
                                            <td><?php echo $loan->term_requested; ?> months</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium bg-light">Application Date</td>
                                            <td><?php echo date('M d, Y', strtotime($loan->application_date)); ?></td>
                                        </tr>
                                        <?php if($loan->status == 'approved' || $loan->status == 'disbursed' || $loan->status == 'completed'): ?>
                                        <tr>
                                            <td class="fw-medium bg-light">Approval Date</td>
                                            <td><?php echo date('M d, Y', strtotime($loan->review_date)); ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if($loan->disbursement_date): ?>
                                        <tr>
                                            <td class="fw-medium bg-light">Disbursement Date</td>
                                            <td><?php echo date('M d, Y', strtotime($loan->disbursement_date)); ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if($loan->expected_completion_date): ?>
                                        <tr>
                                            <td class="fw-medium bg-light">Expected Completion</td>
                                            <td><?php echo date('M d, Y', strtotime($loan->expected_completion_date)); ?>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                        <tr>
                                            <td class="fw-medium bg-light">Purpose</td>
                                            <td><?php echo htmlspecialchars($loan->purpose); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium bg-light">Current Status</td>
                                            <td>
                                                <?php 
                                    $statusClass = 'secondary';
                                    
                                    if ($loan->status == 'under_review') {
                                        $statusClass = 'primary';
                                    } elseif ($loan->status == 'approved') {
                                        $statusClass = 'info';
                                    } elseif ($loan->status == 'disbursed') {
                                        $statusClass = 'success';
                                    } elseif ($loan->status == 'rejected') {
                                        $statusClass = 'danger';
                                    } elseif ($loan->status == 'completed') {
                                        $statusClass = 'success';
                                    } elseif ($loan->status == 'defaulted') {
                                        $statusClass = 'danger';
                                    }
                                    ?>
                                                <span class="badge bg-<?php echo $statusClass; ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $loan->status)); ?>
                                                </span>

                                                <?php if($loan->status == 'rejected' && $loan->rejection_reason): ?>
                                                <div class="mt-2 p-2 rounded bg-danger-subtle text-danger">
                                                    <i class="ri-error-warning-line me-1"></i>
                                                    Reason: <?php echo htmlspecialchars($loan->rejection_reason); ?>
                                                </div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Terms and Conditions -->
                <div class="col-xl-6 col-lg-6">
                    <div class="card custom-card mb-4">
                        <div class="card-header border-bottom">
                            <div class="card-title">
                                <i class="ri-file-list-3-line me-2 text-primary"></i>Loan Terms & Conditions
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if($loan->status == 'pending' || $loan->status == 'under_review'): ?>
                            <!-- For pending/under review loans -->
                            <div class="text-center py-4">
                                <div class="avatar avatar-lg avatar-rounded mx-auto mb-3 bg-warning bg-opacity-10">
                                    <i class="ri-time-line text-warning fs-2"></i>
                                </div>
                                <h5>Application Under Review</h5>
                                <p class="text-muted">Your loan application is currently being evaluated. Detailed terms
                                    and conditions will be available once your loan is approved.</p>
                            </div>

                            <div class="alert alert-light border mt-3">
                                <h6 class="mb-2"><i class="ri-information-line me-2 text-primary"></i>Expected Terms
                                </h6>
                                <p class="mb-0 text-muted">If approved, your loan will include these standard terms:</p>
                                <ul class="text-muted mt-2 mb-0">
                                    <li><strong>Interest Rate:</strong> <?php echo $loan->interest_rate; ?>% per annum
                                    </li>
                                    <li><strong>Processing Fee:</strong> <?php echo $loan->processing_fee; ?>%</li>
                                    <li><strong>Repayment:</strong> Monthly installments </li>
                                    <li><strong>Term:</strong> <?php echo $loan->term_requested; ?> months</li>
                                </ul>
                            </div>

                            <?php elseif($loan->status == 'rejected'): ?>
                            <!-- For rejected loans -->
                            <div class="text-center py-4">
                                <div class="avatar avatar-lg avatar-rounded mx-auto mb-3 bg-danger bg-opacity-10">
                                    <i class="ri-close-circle-line text-danger fs-2"></i>
                                </div>
                                <h5>Application Rejected</h5>
                                <p class="text-muted">Unfortunately, your loan application has been rejected.</p>

                                <?php if($loan->rejection_reason): ?>
                                <div class="alert alert-danger">
                                    <i class="ri-error-warning-line me-1"></i>
                                    <strong>Reason:</strong> <?php echo htmlspecialchars($loan->rejection_reason); ?>
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
                                    <li>Ensuring steady farm income</li>
                                </ul>
                            </div>

                            <?php else: ?>
                            <!-- For approved/disbursed/completed loans -->
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td class="fw-medium bg-light" width="50%">Interest Rate</td>
                                            <td><?php echo $loan->interest_rate; ?>% per annum</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium bg-light">Processing Fee</td>
                                            <td>
                                                <?php echo $loan->processing_fee; ?>%
                                                (KES
                                                <?php echo number_format(($loan->amount_requested * $loan->processing_fee / 100), 2); ?>)
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium bg-light">Total Repayment Amount</td>
                                            <td>
                                                <span class="fw-semibold">
                                                    KES
                                                    <?php echo number_format($loan->total_repayment_amount ?? 0, 2); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium bg-light">Monthly Installment</td>
                                            <td>
                                                KES
                                                <?php echo number_format(($loan->total_repayment_amount ?? 0) / $loan->term_requested, 2); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium bg-light">Remaining Balance</td>
                                            <td>
                                                <?php if($loan->status == 'disbursed' || $loan->status == 'completed'): ?>
                                                <span
                                                    class="text-<?php echo ($loan->status == 'completed') ? 'success' : 'primary'; ?> fw-semibold">
                                                    KES <?php echo number_format($loan->remaining_balance ?? 0, 2); ?>
                                                </span>
                                                <?php else: ?>
                                                <span class="text-muted">Not yet disbursed</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php if($loan->status == 'disbursed'): ?>
                                        <tr>
                                            <td class="fw-medium bg-light">Repayment Progress</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 me-3" style="height: 6px;">
                                                        <div class="progress-bar bg-success" role="progressbar"
                                                            style="width: <?php echo $repaymentProgress; ?>%"
                                                            aria-valuenow="<?php echo $repaymentProgress; ?>"
                                                            aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <span
                                                        class="fs-12 text-muted"><?php echo round($repaymentProgress); ?>%</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium bg-light">Time Elapsed</td>
                                            <td>
                                                <?php echo $monthsElapsed; ?> / <?php echo $loan->term_requested; ?>
                                                months
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php if($loan->status == 'disbursed'): ?>
                            <div class="alert alert-primary mt-3">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <i class="ri-information-line fs-24"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Repayment Information</h6>
                                        <p class="mb-0">Loan repayments are automatically deducted from your produce
                                            sales. You can also make additional payments to clear your loan faster.</p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if($loan->status == 'completed'): ?>
                            <div class="alert alert-success mt-3">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <i class="ri-check-double-line fs-24"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Loan Fully Repaid</h6>
                                        <p class="mb-0">Congratulations! You have successfully repaid this loan, which
                                            will positively impact your creditworthiness for future loans.</p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Row 4: Loan Status Timeline -->
            <div class="row">
                <div class="col-12">
                    <div class="card custom-card mb-4">
                        <div class="card-header border-bottom">
                            <div class="card-title">
                                <i class="ri-timeline-line me-2 text-primary"></i>Loan Application Timeline
                            </div>
                        </div>
                        <div class="card-body px-4">
                            <?php 
                // Get timeline data from loan_logs
                $timelineQuery = "SELECT 
                                ll.action_type,
                                ll.description,
                                ll.created_at,
                                CONCAT(u.first_name, ' ', u.last_name) as user_name,
                                r.name as role_name
                              FROM loan_logs ll
                              JOIN users u ON ll.user_id = u.id
                              JOIN roles r ON u.role_id = r.id
                              WHERE ll.loan_application_id = '{$loanId}'
                              ORDER BY ll.created_at ASC";
               
                $timeline = $app->select_all($timelineQuery);
                
                // Define all possible steps in order
                $steps = [
                    [
                        'key' => 'application',
                        'label' => 'Application Submitted', 
                        'description' => 'Loan request submitted',
                        'icon' => 'ri-file-text-line'
                    ],
                    [
                        'key' => 'review',
                        'label' => 'Under Review', 
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
                        'key' => 'disbursement',
                        'label' => 'Loan Disbursement', 
                        'description' => 'Funds transferred to account',
                        'icon' => 'ri-bank-card-line'
                    ],
                    [
                        'key' => 'repayment',
                        'label' => 'Repayment Period', 
                        'description' => 'Regular loan repayments',
                        'icon' => 'ri-exchange-funds-line'
                    ],
                    [
                        'key' => 'completion',
                        'label' => 'Loan Completed', 
                        'description' => 'Loan fully repaid',
                        'icon' => 'ri-trophy-line'
                    ]
                ];
                
                // Determine current step based on status
                $currentStep = 0;
                if ($loan->status == 'pending' || $loan->status == 'under_review') {
                    $currentStep = 1; // Review stage
                } elseif ($loan->status == 'rejected') {
                    $currentStep = 2; // Stopped at approval stage with rejection
                } elseif ($loan->status == 'approved') {
                    $currentStep = 3; // Awaiting disbursement
                } elseif ($loan->status == 'disbursed') {
                    $currentStep = 4; // Repayment stage
                } elseif ($loan->status == 'completed') {
                    $currentStep = 5; // Completed stage
                }
                
                // Calculate progress percentage
                $totalSteps = count($steps) - 1; // -1 because we start from 0
                $progressPercentage = ($currentStep / $totalSteps) * 100;
                ?>

                            <!-- Overall progress bar -->
                            <div class="progress mb-5"
                                style="height: 8px; background-color: #eef2f7; border-radius: 20px;">
                                <div class="progress-bar <?php echo ($loan->status == 'rejected') ? 'bg-danger' : 'bg-success'; ?>"
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
                        $isPastStep = $index <= $currentStep && $index < 5;
                        $isCurrentStep = $index == $currentStep;
                        $isRejected = $loan->status == 'rejected' && $index == 2;
                        
                        if ($isPastStep && !$isRejected) {
                            $stepStatus = 'completed';
                        } elseif ($isCurrentStep) {
                            $stepStatus = $isRejected ? 'rejected' : 'active';
                        }
                        
                        // If loan is rejected, only steps before rejection are completed
                        if ($loan->status == 'rejected' && $index > 2) {
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
                                            <div style="position: absolute; bottom: -5px; right: -5px; width: 24px; height: 24px; 
                                border-radius: 50%; background-color: #28a745; color: white; 
                                display: flex; align-items: center; justify-content: center;">
                                                <i class="ri-check-line fs-14"></i>
                                            </div>
                                            <?php endif; ?>

                                            <?php if ($stepStatus == 'rejected'): ?>
                                            <!-- X mark for rejection -->
                                            <div style="position: absolute; bottom: -5px; right: -5px; width: 24px; height: 24px; 
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

                            <!-- Status summary box -->
                            <?php
                $alertClass = 'alert-warning';
                $iconClass = 'ri-time-line';
                $statusTitle = 'Application Under Review';
                $statusMessage = 'Your loan application is currently being evaluated by our team.';
                
                if ($loan->status == 'pending') {
                    $alertClass = 'alert-secondary';
                    $iconClass = 'ri-file-text-line';
                    $statusTitle = 'Application Submitted';
                    $statusMessage = 'Your loan application has been received and is awaiting initial screening.';
                }
                else if ($loan->status == 'rejected') {
                    $alertClass = 'alert-danger';
                    $iconClass = 'ri-error-warning-line';
                    $statusTitle = 'Application Rejected';
                    $reasonText = $loan->rejection_reason ?? 'Please contact our staff for more details.';
                    $statusMessage = "Your loan application has been rejected. Reason: {$reasonText}";
                } 
                else if ($loan->status == 'approved') {
                    $alertClass = 'alert-info';
                    $iconClass = 'ri-check-double-line';
                    $statusTitle = 'Application Approved';
                    $statusMessage = 'Your loan application has been approved. Funds will be disbursed soon.';
                }
                else if ($loan->status == 'disbursed') {
                    $alertClass = 'alert-primary';
                    $iconClass = 'ri-bank-card-line';
                    $statusTitle = 'Loan Disbursed';
                    $statusMessage = 'Your loan has been disbursed. Please check your account for the funds.';
                }
                else if ($loan->status == 'completed') {
                    $alertClass = 'alert-success';
                    $iconClass = 'ri-trophy-line';
                    $statusTitle = 'Loan Fully Repaid';
                    $statusMessage = 'Congratulations! You have successfully completed your loan repayment.';
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
            <!-- Row 5: Activity History & Logs -->
            <div class="row">
                <div class="col-12">
                    <div class="card custom-card mb-4">
                        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                            <div class="card-title mb-0">
                                <i class="ri-history-line me-2 text-primary"></i>Loan Activity History
                            </div>
                        </div>
                        <div class="card-body">
                            <?php
                // Fetch all activity logs related to this loan
                $logQuery = "SELECT 
                                ll.id,
                                ll.action_type,
                                ll.description,
                                ll.created_at,
                                CONCAT(u.first_name, ' ', u.last_name) as user_name,
                                r.name as role_name
                             FROM loan_logs ll
                             JOIN users u ON ll.user_id = u.id
                             JOIN roles r ON u.role_id = r.id
                             WHERE ll.loan_application_id = '{$loanId}'
                             ORDER BY ll.created_at DESC";
                
              
                $logs = $app->select_all($logQuery);
                
                // If there are loan repayments, add them to the timeline too
                $repaymentQuery = "SELECT 
                                   lr.id,
                                   lr.amount,
                                   lr.payment_date,
                                   pd.id as produce_delivery_id,
                                   pt.name as product_name,
                                   pd.quantity,
                                   pd.total_value
                                FROM loan_repayments lr
                                JOIN approved_loans al ON lr.approved_loan_id = al.id
                                JOIN produce_deliveries pd ON lr.produce_delivery_id = pd.id
                                JOIN farm_products fp ON pd.farm_product_id = fp.id
                                JOIN product_types pt ON fp.product_type_id = pt.id
                                WHERE al.loan_application_id = '{$loanId}'
                                ORDER BY lr.payment_date DESC";
                
               
                
                $repayments = $app->select_all($repaymentQuery);
                ?>

                            <?php if (empty($logs) && empty($repayments)): ?>
                            <div class="text-center py-5">
                                <div class="avatar avatar-lg mx-auto mb-3 bg-light">
                                    <i class="ri-history-line fs-2 text-muted"></i>
                                </div>
                                <h5 class="text-muted">No Activity Logs Yet</h5>
                                <p class="text-muted">There are no recorded activities for this loan application yet.
                                </p>
                            </div>
                            <?php else: ?>
                            <div class="timeline-main-container">
                                <div class="timeline-container">
                                    <?php 
                        // Combine logs and repayments into a single timeline
                        $timeline = [];
                        
                        // Process logs
                        if ($logs) {
                            foreach ($logs as $log) {
                                $timeline[] = [
                                    'type' => 'log',
                                    'date' => $log->created_at,
                                    'action_type' => $log->action_type,
                                    'description' => $log->description,
                                    'user_name' => $log->user_name,
                                    'role_name' => $log->role_name
                                ];
                            }
                        }
                        
                        // Process repayments
                        if ($repayments) {
                            foreach ($repayments as $repayment) {
                                $timeline[] = [
                                    'type' => 'repayment',
                                    'date' => $repayment->payment_date,
                                    'amount' => $repayment->amount,
                                    'produce_id' => $repayment->produce_delivery_id,
                                    'product_name' => $repayment->product_name,
                                    'produce_quantity' => $repayment->quantity,
                                    'produce_value' => $repayment->total_value
                                ];
                            }
                        }
                        
                        // Sort timeline by date (newest first)
                        usort($timeline, function($a, $b) {
                            return strtotime($b['date']) - strtotime($a['date']);
                        });
                        
                        foreach ($timeline as $index => $item):
                            if ($item['type'] == 'log'):
                                // Set icon and color based on action type
                                $icon = 'ri-information-line';
                                $color = '#6c757d';
                                $bgColor = '#f8f9fa';
                
                                switch ($item['action_type']) {
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
                                    case 'auto_approved':
                                    case 'approved':
                                        $icon = 'ri-check-double-line';
                                        $color = '#28a745';
                                        $bgColor = 'rgba(40, 167, 69, 0.1)';
                                        break;
                                    case 'auto_rejected':
                                    case 'rejected':
                                        $icon = 'ri-close-circle-line';
                                        $color = '#dc3545';
                                        $bgColor = 'rgba(220, 53, 69, 0.1)';
                                        break;
                                    case 'review_started':
                                        $icon = 'ri-search-line';
                                        $color = '#fd7e14';
                                        $bgColor = 'rgba(253, 126, 20, 0.1)';
                                        break;
                                    case 'disbursed':
                                        $icon = 'ri-bank-card-line';
                                        $color = '#6610f2';
                                        $bgColor = 'rgba(102, 16, 242, 0.1)';
                                        break;
                                    case 'repayment_made':
                                        $icon = 'ri-exchange-funds-line';
                                        $color = '#20c997';
                                        $bgColor = 'rgba(32, 201, 151, 0.1)';
                                        break;
                                    case 'completed':
                                        $icon = 'ri-trophy-line';
                                        $color = '#28a745';
                                        $bgColor = 'rgba(40, 167, 69, 0.1)';
                                        break;
                                    default:
                                        $icon = 'ri-file-list-line';
                                        $color = '#6c757d';
                                        $bgColor = 'rgba(108, 117, 125, 0.1)';
                                }
                        ?>
                                    <!-- Log timeline item -->
                                    <div class="timeline-block <?php echo ($index !== 0) ? 'mt-4' : ''; ?>">
                                        <div class="timeline-content">
                                            <span class="timeline-icon"
                                                style="background-color: <?php echo $bgColor; ?>; color: <?php echo $color; ?>;">
                                                <i class="<?php echo $icon; ?> fs-18"></i>
                                            </span>
                                            <div class="align-items-center d-flex timeline-indicator-text">
                                                <span class="fw-semibold me-1">
                                                    <?php echo ucwords(str_replace('_', ' ', $item['action_type'])); ?>
                                                </span>
                                                <span class="badge bg-light text-dark badge-sm">
                                                    <?php echo date('M d, Y', strtotime($item['date'])); ?>
                                                </span>
                                            </div>
                                            <div class="p-3 border-start border-2 ms-4 mt-2"
                                                style="border-color: #e9e9e9 !important; background-color: #f9f9f9; border-radius: 6px;">
                                                <?php echo htmlspecialchars($item['description']); ?>

                                                <div class="mt-2 d-flex align-items-center">
                                                    <div class="avatar avatar-xs avatar-rounded bg-light me-2">
                                                        <i class="ri-user-line fs-12 text-primary"></i>
                                                    </div>
                                                    <span
                                                        class="text-muted fs-12"><?php echo htmlspecialchars($item['user_name']); ?></span>
                                                    <span
                                                        class="badge bg-light text-dark ms-2 fs-12"><?php echo ucfirst($item['role_name']); ?></span>
                                                    <span
                                                        class="text-muted ms-auto fs-12"><?php echo date('h:i A', strtotime($item['date'])); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php 
                            elseif ($item['type'] == 'repayment'): 
                        ?>
                                    <!-- Repayment timeline item -->
                                    <div class="timeline-block <?php echo ($index !== 0) ? 'mt-4' : ''; ?>">
                                        <div class="timeline-content">
                                            <span class="timeline-icon"
                                                style="background-color: rgba(32, 201, 151, 0.1); color: #20c997;">
                                                <i class="ri-exchange-funds-line fs-18"></i>
                                            </span>
                                            <div class="align-items-center d-flex timeline-indicator-text">
                                                <span class="fw-semibold me-1">
                                                    Loan Repayment
                                                </span>
                                                <span class="badge bg-light text-dark badge-sm">
                                                    <?php echo date('M d, Y', strtotime($item['date'])); ?>
                                                </span>
                                            </div>
                                            <div class="p-3 border-start border-2 ms-4 mt-2"
                                                style="border-color: #e9e9e9 !important; background-color: #f9f9f9; border-radius: 6px;">
                                                <p class="mb-2">
                                                    <strong>Amount Paid:</strong> KES
                                                    <?php echo number_format($item['amount'], 2); ?>
                                                </p>

                                                <div class="d-flex align-items-center bg-light p-2 rounded mb-2">
                                                    <div class="avatar avatar-sm bg-success-transparent me-2">
                                                        <i class="ri-plant-line text-success"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fs-12 text-muted">Paid from produce sale</div>
                                                        <div class="fw-medium">
                                                            <?php echo htmlspecialchars($item['product_name']); ?> -
                                                            <?php echo number_format($item['produce_quantity'], 2); ?>
                                                            KGs
                                                        </div>
                                                    </div>
                                                    <div class="ms-auto text-end">
                                                        <div class="fs-12 text-muted">Sale Value</div>
                                                        <div class="fw-medium">KES
                                                            <?php echo number_format($item['produce_value'], 2); ?>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mt-2 d-flex align-items-center">
                                                    <div class="avatar avatar-xs avatar-rounded bg-light me-2">
                                                        <i class="ri-calendar-line fs-12 text-primary"></i>
                                                    </div>
                                                    <span class="text-muted fs-12">Payment processed on</span>
                                                    <span
                                                        class="ms-1 text-dark fs-12"><?php echo date('M d, Y', strtotime($item['date'])); ?></span>
                                                    <span
                                                        class="text-muted ms-auto fs-12"><?php echo date('h:i A', strtotime($item['date'])); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php 
                            endif;
                        endforeach; 
                        ?>
                                </div>
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
        toastr.info('Preparing your loan statement for download...', 'Please wait', {
            "positionClass": "toast-top-right",
            "progressBar": true,
            "timeOut": 0,
            "extendedTimeOut": 0,
            "closeButton": false,
            "hideMethod": "fadeOut"
        });

        // Get the loan ID from the page
        const loanId = <?php echo $_GET['id'] ?>;

        // AJAX call to generate PDF
        $.ajax({
            url: "http://localhost/dfcs/ajax/loan-controller/generate-loan-statement-pdf.php",
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