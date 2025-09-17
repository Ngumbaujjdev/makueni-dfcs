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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="http://localhost/dfcs/toast/toast.css">

</head>
<style>
/* Hover effects for financial snapshot cards */
.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
}

/* Pulse animation for loading */
@keyframes pulse {
    0% {
        opacity: 1;
    }

    50% {
        opacity: 0.5;
    }

    100% {
        opacity: 1;
    }
}

.pulse {
    animation: pulse 1.5s infinite;
}

/* Button hover effects */
.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}
</style>

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

                <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
                    <h1 class="page-title fw-semibold fs-18 mb-0">Apply for Bank Loan</h1>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Loans</a></li>
                                <li class="breadcrumb-item active" aria-current="page">New Application</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <!-- Tabs Navigation -->
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">Bank Loan Application</div>
                    </div>
                    <div class="card-body add-products p-0">
                        <ul class="nav nav-tabs" id="loanTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#loan-details"
                                    type="button" role="tab">
                                    <i class="bi bi-cash-coin me-1"></i>Loan Details
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#loan-purpose"
                                    type="button" role="tab">
                                    <i class="bi bi-file-text me-1"></i>Purpose & Details
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#loan-summary"
                                    type="button" role="tab">
                                    <i class="bi bi-clipboard-check me-1"></i>Summary & Submit
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#loan-status"
                                    type="button" role="tab">
                                    <i class="bi bi-clipboard-data me-1"></i>Application Status
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content Container -->
                        <div class="tab-content p-4">
                            <!-- 1. Loan Details Tab -->
                            <div class="tab-pane fade show active" id="loan-details" role="tabpanel">
                                <div class="row gy-3">
                                    <div class="col-xl-12">
                                        <?php
                                         $app = new App;
                                         
                                         // Get farmer details including their registration number
                                         $query = "SELECT u.*, f.id as farmer_id, f.registration_number, f.category_id, fc.name as category_name
                                                   FROM users u
                                                   LEFT JOIN farmers f ON u.id = f.user_id
                                                   LEFT JOIN farmer_categories fc ON f.category_id = fc.id
                                                   WHERE u.id = " . $_SESSION['user_id'];
                                         
                                         $farmer = $app->select_one($query);
                                         ?>
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle me-2"></i>
                                            Applying as:
                                            <strong><?php echo $farmer->first_name . ' ' . $farmer->last_name; ?></strong>
                                            <span
                                                class="badge bg-success ms-2"><?php echo $farmer->registration_number; ?></span>
                                            <?php if($farmer->category_name): ?>
                                            - <span class="text-muted"><?php echo $farmer->category_name; ?>
                                                Farmer</span>
                                            <?php endif; ?>
                                            <input type="hidden" name="farmer_id"
                                                value="<?php echo $farmer->farmer_id; ?>">
                                            <input type="hidden" name="provider_type" value="sacco">
                                        </div>
                                    </div>

                                    <div class="col-xl-12">
                                        <label class="form-label">Select Bank Loan Type</label>
                                        <select class="form-control" id="loan-type-select" name="loan_type_id" required>
                                            <option value="">Select a bank loan type...</option>
                                            <?php
                                                $query = "SELECT lt.id, lt.name, lt.interest_rate, lt.min_amount, lt.max_amount, lt.min_term, 
                                                                 lt.max_term, lt.processing_fee, b.name as bank_name 
                                                          FROM loan_types lt
                                                          LEFT JOIN banks b ON lt.bank_id = b.id 
                                                          WHERE lt.provider_type = 'bank' AND lt.is_active = 1";
                                                $loan_types = $app->select_all($query);
                                                foreach($loan_types as $loan_type): ?>
                                            <option value="<?php echo $loan_type->id; ?>"
                                                data-interest="<?php echo $loan_type->interest_rate; ?>"
                                                data-min-amount="<?php echo $loan_type->min_amount; ?>"
                                                data-max-amount="<?php echo $loan_type->max_amount; ?>"
                                                data-min-term="<?php echo $loan_type->min_term; ?>"
                                                data-max-term="<?php echo $loan_type->max_term; ?>"
                                                data-fee="<?php echo $loan_type->processing_fee; ?>">
                                                <?php echo $loan_type->name; ?> - <?php echo $loan_type->bank_name; ?>
                                                (<?php echo $loan_type->interest_rate; ?>% p.a.)
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-xl-6">
                                        <label class="form-label">Loan Amount (KES)</label>
                                        <input type="number" class="form-control" id="loan-amount"
                                            name="amount_requested" required>
                                        <div class="form-text" id="amount-range-text">Amount range: KES 0 - 0</div>
                                    </div>

                                    <div class="col-xl-6">
                                        <label class="form-label">Term (Months)</label>
                                        <input type="number" class="form-control" id="loan-term" name="term_requested"
                                            required>
                                        <div class="form-text" id="term-range-text">Term range: 0 - 0 months</div>
                                    </div>

                                    <div class="col-xl-12 mt-3">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6 class="card-title">Loan Calculation</h6>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p class="mb-2">Interest Rate: <span
                                                                id="interest-rate">0</span>%</p>
                                                        <p class="mb-0">Processing Fee: KES <span
                                                                id="processing-fee">0.00</span></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p class="mb-2">Monthly Payment: KES <span
                                                                id="monthly-payment">0.00</span></p>
                                                        <h5 class="mb-0">Total Repayment: KES <span
                                                                id="total-repayment">0.00</span></h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button class="btn text-white" id="nextToPurpose" style="background:#6AA32D;">
                                        Next <i class="bi bi-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- 2. Purpose & Details Tab -->
                            <div class="tab-pane fade" id="loan-purpose" role="tabpanel">
                                <div class="row gy-3">
                                    <div class="col-xl-12">
                                        <label class="form-label">Loan Purpose Category</label>
                                        <select class="form-control" id="purpose-select" name="purpose_category"
                                            required>
                                            <option value="">Select purpose...</option>
                                            <option value="farm_expansion">Farm Expansion</option>
                                            <option value="equipment_purchase">Equipment Purchase</option>
                                            <option value="input_purchase">Purchase of Inputs</option>
                                            <option value="irrigation">Irrigation System</option>
                                            <option value="processing">Processing Equipment</option>
                                            <option value="marketing">Marketing and Distribution</option>
                                            <option value="other">Other (please specify)</option>
                                        </select>
                                    </div>

                                    <div class="col-xl-12">
                                        <label class="form-label">Detailed Purpose Description</label>
                                        <textarea class="form-control" id="purpose-description" name="purpose" rows="3"
                                            required
                                            placeholder="Please provide details on how the loan will be used"></textarea>
                                    </div>

                                    <div class="col-xl-12">
                                        <label class="form-label">Additional Information</label>
                                        <textarea class="form-control" id="additional-info" name="additional_info"
                                            rows="3"
                                            placeholder="Any additional information to support your application"></textarea>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button class="btn btn-light" id="backToLoan">
                                        <i class="bi bi-arrow-left me-2"></i>Previous
                                    </button>
                                    <button class="btn text-white" id="nextToSummary" style="background:#6AA32D;">
                                        Next <i class="bi bi-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- 3. Summary & Submit Tab -->
                            <div class="tab-pane fade" id="loan-summary" role="tabpanel">
                                <div class="row gy-3">
                                    <!-- Loan Application Summary Card -->
                                    <div class="col-xl-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">Loan Application Summary</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row gy-3">
                                                    <!-- Reference Number -->
                                                    <div class="col-md-12">
                                                        <div class="alert alert-info mb-3">
                                                            <strong>Loan Application Reference Number:</strong>
                                                            <span id="loan-reference">
                                                                <?php 
                                                // Generate reference number: LOAN/YYYYMMDD/RANDOM4DIGITS
                                                $refNumber = 'LOAN/' . date('Ymd') . '/' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
                                                echo $refNumber;
                                                ?>
                                                            </span>
                                                            <input type="hidden" name="reference_number"
                                                                value="<?php echo $refNumber; ?>">
                                                        </div>
                                                    </div>

                                                    <!-- Summary Row -->
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Loan Type</label>
                                                        <p class="mb-0"><span id="summary-loan-type">-</span></p>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Loan Amount</label>
                                                        <p class="mb-0">KES <span id="summary-loan-amount">0.00</span>
                                                        </p>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Term</label>
                                                        <p class="mb-0"><span id="summary-term">0</span> months</p>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Interest Rate</label>
                                                        <p class="mb-0"><span id="summary-interest-rate">0</span>%</p>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Processing Fee</label>
                                                        <p class="mb-0">KES <span
                                                                id="summary-processing-fee">0.00</span></p>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Purpose</label>
                                                        <p class="mb-0"><span id="summary-purpose-category">-</span></p>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <label class="form-label fw-semibold">Purpose Details</label>
                                                        <p class="mb-0"><span id="summary-purpose-description">-</span>
                                                        </p>
                                                    </div>

                                                    <!-- Total Repayment -->
                                                    <div class="col-md-12 mt-4">
                                                        <div class="bg-light p-3 rounded">
                                                            <div class="row align-items-center">
                                                                <div class="col-md-6">
                                                                    <h6 class="mb-0">Total Repayment Amount</h6>
                                                                </div>
                                                                <div class="col-md-6 text-end">
                                                                    <h4 class="mb-0">KES <span
                                                                            id="summary-total-repayment">0.00</span>
                                                                    </h4>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Repayment Information -->
                                                    <div class="col-md-12 mt-3">
                                                        <div class="alert alert-info mb-0">
                                                            <i class="bi bi-info-circle me-2"></i>
                                                            <strong>Repayment Method:</strong> Loan repayments will be
                                                            automatically deducted from your produce delivery payments.
                                                        </div>
                                                    </div>

                                                    <!-- Application Notice -->
                                                    <div class="col-md-12 mt-3">
                                                        <div class="alert alert-warning mb-0">
                                                            <i class="bi bi-info-circle me-2"></i>
                                                            Your application will be processed within 48 hours. You'll
                                                            receive notifications about status changes.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Terms Checkbox -->
                                    <div class="col-xl-12 mt-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="terms-checkbox"
                                                required>
                                            <label class="form-check-label" for="terms-checkbox">
                                                I confirm that all information provided is accurate and I accept the
                                                loan terms and conditions
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="col-xl-12">
                                        <div class="d-flex justify-content-between mt-4">
                                            <button class="btn btn-light" id="backToPurpose">
                                                <i class="bi bi-arrow-left me-2"></i>Previous
                                            </button>
                                            <button type="submit" class="btn btn-success" id="submitLoanApplication">
                                                <i class="bi bi-check-circle me-2"></i>Submit Application
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- 4. Bank Loan Application Status & Financial Overview Tab -->
                            <div class="tab-pane fade" id="loan-status" role="tabpanel">
                                <div class="row gy-4">
                                    <!-- Application Decision Header -->
                                    <div class="col-xl-12">
                                        <div class="card shadow-sm border-0" id="decision-card"
                                            style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                            <div class="card-header d-flex align-items-center"
                                                style="background: linear-gradient(135deg, #70A136 0%, #6AA32D 100%); border: none;">
                                                <div class="me-3">
                                                    <i class="bi bi-bank text-white fs-1" id="decision-icon"
                                                        style="text-shadow: 0 2px 4px rgba(0,0,0,0.3);"></i>
                                                </div>
                                                <div class="text-white">
                                                    <h5 class="card-title mb-0 fw-bold" id="decision-title"
                                                        style="text-shadow: 0 1px 2px rgba(0,0,0,0.3);">Bank Loan
                                                        Application Status</h5>
                                                    <p class="card-text mb-0 opacity-90" id="decision-description">
                                                        Processing your bank loan application...</p>
                                                </div>
                                            </div>
                                            <div class="card-body p-4">
                                                <div class="row align-items-center">
                                                    <div class="col-md-8">
                                                        <div class="d-flex align-items-center mb-3">
                                                            <i class="bi bi-graph-up text-success me-2 fs-4"></i>
                                                            <h6 class="mb-0 fw-semibold" style="color: #4A220F;">
                                                                Creditworthiness Score</h6>
                                                        </div>
                                                        <div class="progress mb-2"
                                                            style="height: 30px; background-color: #f1f3f4; border-radius: 15px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);">
                                                            <div class="progress-bar" id="score-progress"
                                                                role="progressbar"
                                                                style="width: 0%; background: linear-gradient(90deg, #70A136 0%, #8BC34A 100%); border-radius: 15px; transition: width 1s ease-in-out;">
                                                                <span id="score-text" class="fw-bold text-white"
                                                                    style="text-shadow: 0 1px 2px rgba(0,0,0,0.5);">0</span>
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">
                                                            <i class="bi bi-info-circle me-1"></i>
                                                            Score: <span id="score-value" class="fw-semibold"
                                                                style="color: #4A220F;">0</span>/100
                                                        </small>
                                                    </div>
                                                    <div class="col-md-4 text-end">
                                                        <div class="badge fs-6 p-3 shadow-sm" id="status-badge"
                                                            style="background: linear-gradient(135deg, #70A136 0%, #6AA32D 100%); border-radius: 20px;">
                                                            <i class="bi bi-clock-history me-1"></i>Processing
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bank Information Display -->
                                    <div class="col-xl-12">
                                        <div class="card shadow-sm border-0">
                                            <div class="card-header"
                                                style="background: linear-gradient(135deg, #2c5aa0 0%, #1e3f73 100%); border: none;">
                                                <h6 class="card-title mb-0 text-white fw-semibold">
                                                    <i class="bi bi-building me-2"></i>Bank Partner Information
                                                </h6>
                                            </div>
                                            <div class="card-body p-4" style="background: #fafafa;">
                                                <div class="row align-items-center">
                                                    <div class="col-md-8">
                                                        <div class="d-flex align-items-center">
                                                            <i class="bi bi-bank2 fs-2 me-3"
                                                                style="color: #2c5aa0;"></i>
                                                            <div>
                                                                <h5 class="mb-1 fw-bold" id="selected-bank-name"
                                                                    style="color: #4A220F;">Selected Bank</h5>
                                                                <p class="mb-0 text-muted" id="selected-loan-type">Loan
                                                                    Type Details</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 text-end">
                                                        <div class="text-end">
                                                            <small class="text-muted d-block">Interest Rate</small>
                                                            <h4 class="mb-0 fw-bold" id="bank-interest-rate"
                                                                style="color: #2c5aa0;">0%</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Score Breakdown -->
                                    <div class="col-xl-12">
                                        <div class="card shadow-sm border-0">
                                            <div class="card-header"
                                                style="background: linear-gradient(135deg, #4A220F 0%, #5D2A13 100%); border: none;">
                                                <h6 class="card-title mb-0 text-white fw-semibold">
                                                    <i class="bi bi-graph-up me-2"></i>Creditworthiness Assessment
                                                </h6>
                                            </div>
                                            <div class="card-body p-4" style="background: #fafafa;">
                                                <div class="row gy-4">
                                                    <!-- Repayment History -->
                                                    <div class="col-md-6">
                                                        <div class="card h-100 border-0 shadow-sm"
                                                            style="background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);">
                                                            <div class="card-body p-3">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center mb-3">
                                                                    <div class="d-flex align-items-center">
                                                                        <i
                                                                            class="bi bi-clock-history text-primary me-2 fs-5"></i>
                                                                        <span class="fw-semibold"
                                                                            style="color: #4A220F;">Repayment
                                                                            History</span>
                                                                    </div>
                                                                    <span class="badge"
                                                                        style="background-color: #70A136; color: white;">30%
                                                                        Weight</span>
                                                                </div>
                                                                <div class="progress mb-2"
                                                                    style="height: 25px; background-color: #e9ecef; border-radius: 12px;">
                                                                    <div class="progress-bar" id="repayment-progress"
                                                                        role="progressbar"
                                                                        style="width: 0%; background: linear-gradient(90deg, #3498db 0%, #2980b9 100%); border-radius: 12px; transition: width 0.8s ease-in-out;">
                                                                        <span id="repayment-score"
                                                                            class="fw-bold text-white">0</span>
                                                                    </div>
                                                                </div>
                                                                <small class="text-muted" id="repayment-explanation">
                                                                    <i class="bi bi-info-circle me-1"></i>Past loan
                                                                    performance assessment
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Financial Obligations -->
                                                    <div class="col-md-6">
                                                        <div class="card h-100 border-0 shadow-sm"
                                                            style="background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);">
                                                            <div class="card-body p-3">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center mb-3">
                                                                    <div class="d-flex align-items-center">
                                                                        <i
                                                                            class="bi bi-wallet2 text-info me-2 fs-5"></i>
                                                                        <span class="fw-semibold"
                                                                            style="color: #4A220F;">Financial
                                                                            Obligations</span>
                                                                    </div>
                                                                    <span class="badge"
                                                                        style="background-color: #70A136; color: white;">25%
                                                                        Weight</span>
                                                                </div>
                                                                <div class="progress mb-2"
                                                                    style="height: 25px; background-color: #e9ecef; border-radius: 12px;">
                                                                    <div class="progress-bar" id="obligations-progress"
                                                                        role="progressbar"
                                                                        style="width: 0%; background: linear-gradient(90deg, #17a2b8 0%, #138496 100%); border-radius: 12px; transition: width 0.8s ease-in-out;">
                                                                        <span id="obligations-score"
                                                                            class="fw-bold text-white">0</span>
                                                                    </div>
                                                                </div>
                                                                <small class="text-muted" id="obligations-explanation">
                                                                    <i class="bi bi-info-circle me-1"></i>Current
                                                                    debt-to-income ratio
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Produce History -->
                                                    <div class="col-md-6">
                                                        <div class="card h-100 border-0 shadow-sm"
                                                            style="background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);">
                                                            <div class="card-body p-3">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center mb-3">
                                                                    <div class="d-flex align-items-center">
                                                                        <i
                                                                            class="bi bi-truck text-warning me-2 fs-5"></i>
                                                                        <span class="fw-semibold"
                                                                            style="color: #4A220F;">Produce
                                                                            History</span>
                                                                    </div>
                                                                    <span class="badge"
                                                                        style="background-color: #70A136; color: white;">35%
                                                                        Weight</span>
                                                                </div>
                                                                <div class="progress mb-2"
                                                                    style="height: 25px; background-color: #e9ecef; border-radius: 12px;">
                                                                    <div class="progress-bar" id="produce-progress"
                                                                        role="progressbar"
                                                                        style="width: 0%; background: linear-gradient(90deg, #ffc107 0%, #e0a800 100%); border-radius: 12px; transition: width 0.8s ease-in-out;">
                                                                        <span id="produce-score"
                                                                            class="fw-bold text-dark">0</span>
                                                                    </div>
                                                                </div>
                                                                <small class="text-muted" id="produce-explanation">
                                                                    <i class="bi bi-info-circle me-1"></i>Delivery
                                                                    frequency and value (6 months)
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Amount Ratio -->
                                                    <div class="col-md-6">
                                                        <div class="card h-100 border-0 shadow-sm"
                                                            style="background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);">
                                                            <div class="card-body p-3">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center mb-3">
                                                                    <div class="d-flex align-items-center">
                                                                        <i
                                                                            class="bi bi-calculator text-secondary me-2 fs-5"></i>
                                                                        <span class="fw-semibold"
                                                                            style="color: #4A220F;">Loan Amount
                                                                            Ratio</span>
                                                                    </div>
                                                                    <span class="badge"
                                                                        style="background-color: #70A136; color: white;">10%
                                                                        Weight</span>
                                                                </div>
                                                                <div class="progress mb-2"
                                                                    style="height: 25px; background-color: #e9ecef; border-radius: 12px;">
                                                                    <div class="progress-bar" id="ratio-progress"
                                                                        role="progressbar"
                                                                        style="width: 0%; background: linear-gradient(90deg, #6c757d 0%, #495057 100%); border-radius: 12px; transition: width 0.8s ease-in-out;">
                                                                        <span id="ratio-score"
                                                                            class="fw-bold text-white">0</span>
                                                                    </div>
                                                                </div>
                                                                <small class="text-muted" id="ratio-explanation">
                                                                    <i class="bi bi-info-circle me-1"></i>Loan amount
                                                                    vs. produce value
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Financial Snapshot -->
                                    <div class="col-xl-12">
                                        <div class="card shadow-sm border-0">
                                            <div class="card-header"
                                                style="background: linear-gradient(135deg, #70A136 0%, #6AA32D 100%); border: none;">
                                                <h6 class="card-title mb-0 text-white fw-semibold">
                                                    <i class="bi bi-wallet2 me-2"></i>Your Financial Snapshot
                                                </h6>
                                            </div>
                                            <div class="card-body p-4" style="background: #fafafa;">
                                                <div class="row gy-3">
                                                    <!-- Active Loans -->
                                                    <div class="col-md-3">
                                                        <div class="card h-100 border-0 shadow-sm"
                                                            style="background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%); transition: transform 0.2s ease;">
                                                            <div class="card-body text-center p-4">
                                                                <div class="mb-3">
                                                                    <i class="bi bi-cash-coin fs-1 mb-2"
                                                                        style="color: #70A136;"></i>
                                                                </div>
                                                                <h6 class="mb-2 fw-semibold" style="color: #4A220F;">
                                                                    Active Loans</h6>
                                                                <h4 class="mb-2 fw-bold" id="active-loans-count"
                                                                    style="color: #70A136;">0</h4>
                                                                <small class="text-muted">
                                                                    <i class="bi bi-currency-exchange me-1"></i>
                                                                    KES <span id="active-loans-balance"
                                                                        class="fw-semibold">0.00</span>
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Input Credits -->
                                                    <div class="col-md-3">
                                                        <div class="card h-100 border-0 shadow-sm"
                                                            style="background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%); transition: transform 0.2s ease;">
                                                            <div class="card-body text-center p-4">
                                                                <div class="mb-3">
                                                                    <i class="bi bi-bag-check fs-1 mb-2"
                                                                        style="color: #17a2b8;"></i>
                                                                </div>
                                                                <h6 class="mb-2 fw-semibold" style="color: #4A220F;">
                                                                    Input Credits</h6>
                                                                <h4 class="mb-2 fw-bold" id="input-credits-count"
                                                                    style="color: #17a2b8;">0</h4>
                                                                <small class="text-muted">
                                                                    <i class="bi bi-currency-exchange me-1"></i>
                                                                    KES <span id="input-credits-balance"
                                                                        class="fw-semibold">0.00</span>
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Recent Deliveries -->
                                                    <div class="col-md-3">
                                                        <div class="card h-100 border-0 shadow-sm"
                                                            style="background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%); transition: transform 0.2s ease;">
                                                            <div class="card-body text-center p-4">
                                                                <div class="mb-3">
                                                                    <i class="bi bi-truck fs-1 mb-2"
                                                                        style="color: #ffc107;"></i>
                                                                </div>
                                                                <h6 class="mb-2 fw-semibold" style="color: #4A220F;">
                                                                    Deliveries (6M)</h6>
                                                                <h4 class="mb-2 fw-bold" id="deliveries-count"
                                                                    style="color: #ffc107;">0</h4>
                                                                <small class="text-muted">
                                                                    <i class="bi bi-currency-exchange me-1"></i>
                                                                    KES <span id="deliveries-value"
                                                                        class="fw-semibold">0.00</span>
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Credit Capacity -->
                                                    <div class="col-md-3">
                                                        <div class="card h-100 border-0 shadow-sm"
                                                            style="background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%); transition: transform 0.2s ease;">
                                                            <div class="card-body text-center p-4">
                                                                <div class="mb-3">
                                                                    <i class="bi bi-speedometer2 fs-1 mb-2"
                                                                        style="color: #28a745;"></i>
                                                                </div>
                                                                <h6 class="mb-2 fw-semibold" style="color: #4A220F;">
                                                                    Credit Capacity</h6>
                                                                <h4 class="mb-2 fw-bold" id="credit-capacity"
                                                                    style="color: #28a745;">0%</h4>
                                                                <small class="text-muted">
                                                                    <i class="bi bi-check-circle me-1"></i>
                                                                    Available
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bank Processing Timeline -->
                                    <div class="col-xl-12">
                                        <div class="card shadow-sm border-0">
                                            <div class="card-header"
                                                style="background: linear-gradient(135deg, #2c5aa0 0%, #1e3f73 100%); border: none;">
                                                <h6 class="card-title mb-0 text-white fw-semibold">
                                                    <i class="bi bi-hourglass-split me-2"></i>Bank Processing Timeline
                                                </h6>
                                            </div>
                                            <div class="card-body p-4" style="background: #fafafa;">
                                                <div class="row">
                                                    <div class="col-md-3 text-center">
                                                        <div class="mb-3">
                                                            <i class="bi bi-check-circle-fill fs-2"
                                                                style="color: #28a745;"></i>
                                                        </div>
                                                        <h6 class="fw-semibold" style="color: #4A220F;">Application
                                                            Submitted</h6>
                                                        <small class="text-muted">Completed</small>
                                                    </div>
                                                    <div class="col-md-3 text-center">
                                                        <div class="mb-3">
                                                            <i class="bi bi-clock-history fs-2"
                                                                style="color: #ffc107;"></i>
                                                        </div>
                                                        <h6 class="fw-semibold" style="color: #4A220F;">Bank Review</h6>
                                                        <small class="text-muted">3-5 business days</small>
                                                    </div>
                                                    <div class="col-md-3 text-center">
                                                        <div class="mb-3">
                                                            <i class="bi bi-file-text fs-2" style="color: #6c757d;"></i>
                                                        </div>
                                                        <h6 class="fw-semibold" style="color: #4A220F;">Documentation
                                                        </h6>
                                                        <small class="text-muted">If required</small>
                                                    </div>
                                                    <div class="col-md-3 text-center">
                                                        <div class="mb-3">
                                                            <i class="bi bi-cash-stack fs-2"
                                                                style="color: #6c757d;"></i>
                                                        </div>
                                                        <h6 class="fw-semibold" style="color: #4A220F;">Disbursement
                                                        </h6>
                                                        <small class="text-muted">Upon approval</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Next Steps / Recommendations -->
                                    <div class="col-xl-12">
                                        <div class="card shadow-sm border-0" id="recommendations-card">
                                            <div class="card-header"
                                                style="background: linear-gradient(135deg, #4A220F 0%, #5D2A13 100%); border: none;">
                                                <h6 class="card-title mb-0 text-white fw-semibold">
                                                    <i class="bi bi-lightbulb me-2"></i>Next Steps & Recommendations
                                                </h6>
                                            </div>
                                            <div class="card-body p-4" style="background: #fafafa;">
                                                <div id="next-steps-content">
                                                    <!-- Content will be populated by JavaScript based on decision -->
                                                    <div class="d-flex align-items-center justify-content-center p-4">
                                                        <div class="spinner-border text-success me-3" role="status">
                                                            <span class="visually-hidden">Loading...</span>
                                                        </div>
                                                        <span class="text-muted">Loading recommendations...</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Application Reference -->
                                    <div class="col-xl-12">
                                        <div class="alert border-0 shadow-sm"
                                            style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-left: 5px solid #70A136 !important;">
                                            <div class="row align-items-center">
                                                <div class="col-md-8">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-bookmark-check fs-3 me-3"
                                                            style="color: #70A136;"></i>
                                                        <div>
                                                            <strong style="color: #4A220F;">Bank Loan Application
                                                                Reference:</strong>
                                                            <br>
                                                            <span id="final-reference-number" class="fw-bold fs-5"
                                                                style="color: #70A136;">LOAN/20250526/0001</span>
                                                            <br>
                                                            <small class="text-muted">
                                                                <i class="bi bi-info-circle me-1"></i>
                                                                Please save this reference number for bank
                                                                correspondence.
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 text-end">
                                                    <button class="btn shadow-sm" onclick="copyReference()"
                                                        style="background: linear-gradient(135deg, #70A136 0%, #6AA32D 100%); color: white; border: none;">
                                                        <i class="bi bi-clipboard me-2"></i>Copy Reference
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="col-xl-12">
                                        <div class="d-flex justify-content-end mt-4">
                                            <div>
                                                <button class="btn shadow-sm me-3"
                                                    onclick="window.location.href='applications'"
                                                    style="background: linear-gradient(135deg, #4A220F 0%, #5D2A13 100%); color: white; border: none;">
                                                    <i class="bi bi-list-ul me-2"></i>View All Applications
                                                </button>
                                                <button class="btn shadow-sm" onclick="window.location.href='apply'"
                                                    style="background: linear-gradient(135deg, #70A136 0%, #6AA32D 100%); color: white; border: none;">
                                                    <i class="bi bi-plus-circle me-2"></i>New Application
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
                <!-- Scroll To Top -->
                <div class="scrollToTop">
                    <span class="arrow"><i class="ri-arrow-up-s-fill fs-20"></i></span>
                </div>
                <div id="responsive-overlay"></div>
                <!-- Scroll To Top -->

                <!-- Scroll To Top -->
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
                <!-- Prism JS -->
                <script src="http://localhost/dfcs/assets/libs/prismjs/prism.js"></script>
                <script src="http://localhost/dfcs/assets/js/prism-custom.js"></script>
                <!-- Custom JS -->
                <script src="http://localhost/dfcs/assets/js/custom.js"></script>
                <!-- summernote -->
                <link rel="stylesheet"
                    href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css">


                <!-- end of footer links -->
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
                    integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
                    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
                <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
                <!-- Toastr JS -->
                <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4"></script>
                <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
                <script>
                // PART 1: BANK LOAN TAB MANAGEMENT AND NAVIGATION SYSTEM

                $(document).ready(function() {
                    // Initialize select2
                    $('.select2').select2();

                    // Store all bank loan application data
                    let loanData = {
                        loan_type_id: '',
                        loan_type_name: '',
                        bank_name: '',
                        interest_rate: 0,
                        amount_requested: 0,
                        term_requested: 0,
                        processing_fee: 0,
                        monthly_payment: 0,
                        total_repayment: 0,
                        purpose_category: '',
                        purpose: '',
                        additional_info: '',
                        reference_number: $('#loan-reference').text(),
                        provider_type: 'bank' // Bank-specific identifier
                    };

                    // Tab definitions - Updated to include the new status tab
                    const tabs = {
                        details: '#loan-details',
                        purpose: '#loan-purpose',
                        summary: '#loan-summary',
                        status: '#loan-status' // New bank loan status tab
                    };

                    // Tab order for navigation validation
                    const tabOrder = ['details', 'purpose', 'summary', 'status'];

                    // Show specified tab function
                    function showTab(tabId) {
                        // Remove active classes from all tabs
                        $('.nav-link').removeClass('active');
                        $('.tab-pane').removeClass('show active');

                        // Add active classes to target tab
                        $(`[data-bs-target="${tabId}"]`).addClass('active');
                        $(tabId).addClass('show active');

                        // Special handling for specific tabs
                        if (tabId === '#loan-summary') {
                            updateSummary();
                        }

                        // Bank loan status tab handling
                        if (tabId === '#loan-status') {
                            // Update bank-specific information in status tab
                            updateBankInformation();
                            // This tab should only be accessible after form submission
                        }
                    }

                    // Get current tab index
                    function getCurrentTabIndex() {
                        const activeTab = $('.tab-pane.show.active').attr('id');
                        switch (activeTab) {
                            case 'loan-details':
                                return 0;
                            case 'loan-purpose':
                                return 1;
                            case 'loan-summary':
                                return 2;
                            case 'loan-status':
                                return 3;
                            default:
                                return 0;
                        }
                    }

                    // Navigate to next tab
                    function goToNextTab() {
                        const currentIndex = getCurrentTabIndex();
                        if (currentIndex < tabOrder.length - 1) {
                            const nextTabKey = tabOrder[currentIndex + 1];
                            showTab(tabs[nextTabKey]);
                        }
                    }

                    // Navigate to previous tab
                    function goToPreviousTab() {
                        const currentIndex = getCurrentTabIndex();
                        if (currentIndex > 0) {
                            const prevTabKey = tabOrder[currentIndex - 1];
                            showTab(tabs[prevTabKey]);
                        }
                    }

                    // Navigate to bank loan status tab (after successful submission)
                    function goToStatusTab() {
                        showTab(tabs.status);
                        // Hide the status tab from regular navigation after showing it
                        $('[data-bs-target="#loan-status"]').parent().addClass('d-none');
                        // Update page title for bank loan
                        updatePageTitle('Bank Loan Application Status');
                    }

                    // Update bank-specific information in status tab
                    function updateBankInformation() {
                        if (loanData.bank_name && loanData.loan_type_name) {
                            $('#selected-bank-name').text(loanData.bank_name);
                            $('#selected-loan-type').text(loanData.loan_type_name);
                            $('#bank-interest-rate').text(loanData.interest_rate + '%');
                        }
                    }

                    // Validation function to check if previous tabs are valid
                    function validatePreviousTabs(targetTab) {
                        switch (targetTab) {
                            case '#loan-status':
                                // Status tab should only be accessible after submission
                                return false;
                            case '#loan-summary':
                                if (!validatePurposeTab()) return false;
                            case '#loan-purpose':
                                if (!validateDetailsTab()) return false;
                        }
                        return true;
                    }

                    // Handle direct tab clicks
                    $('.nav-link').click(function(e) {
                        e.preventDefault();
                        let targetTab = $(this).data('bs-target');

                        // Only allow clicking if previous tabs are valid or it's a backward navigation
                        if (validatePreviousTabs(targetTab) || isBackwardNavigation(targetTab)) {
                            showTab(targetTab);
                        }
                    });

                    // Check if navigation is backward (allowing users to go back)
                    function isBackwardNavigation(targetTab) {
                        const currentIndex = getCurrentTabIndex();
                        const targetIndex = getTabIndex(targetTab);
                        return targetIndex < currentIndex;
                    }

                    // Get tab index by tab ID
                    function getTabIndex(tabId) {
                        switch (tabId) {
                            case '#loan-details':
                                return 0;
                            case '#loan-purpose':
                                return 1;
                            case '#loan-summary':
                                return 2;
                            case '#loan-status':
                                return 3;
                            default:
                                return 0;
                        }
                    }

                    // Update page title
                    function updatePageTitle(newTitle) {
                        document.title = newTitle + ' - DFCS Bank Loan Application';
                        $('.page-title').text(newTitle);
                    }

                    // Tab Navigation Button Event Handlers
                    $('#nextToPurpose').click(function() {
                        if (validateDetailsTab()) {
                            showTab('#loan-purpose');
                        }
                    });

                    $('#backToLoan').click(function() {
                        showTab('#loan-details');
                    });

                    $('#nextToSummary').click(function() {
                        if (validatePurposeTab()) {
                            showTab('#loan-summary');
                        }
                    });

                    $('#backToPurpose').click(function() {
                        showTab('#loan-purpose');
                    });

                    // Bank-specific helper functions
                    function extractBankNameFromSelection() {
                        const selectedOption = $('#loan-type-select').find('option:selected');
                        const fullText = selectedOption.text();
                        // Extract bank name from "Loan Type - Bank Name (Interest%)" format
                        const parts = fullText.split(' - ');
                        if (parts.length >= 2) {
                            const bankPart = parts[1].split('(')[0].trim();
                            return bankPart;
                        }
                        return '';
                    }

                    // Export functions to global scope for use in other parts
                    window.bankLoanApp = {
                        showTab: showTab,
                        goToStatusTab: goToStatusTab,
                        goToNextTab: goToNextTab,
                        goToPreviousTab: goToPreviousTab,
                        updateBankInformation: updateBankInformation,
                        updatePageTitle: updatePageTitle,
                        extractBankNameFromSelection: extractBankNameFromSelection,
                        loanData: loanData,
                        validateDetailsTab: validateDetailsTab,
                        validatePurposeTab: validatePurposeTab
                    };


                    // Part 2 will handle loan calculations and data collection
                    // PART 2: BANK LOAN CALCULATIONS AND DATA COLLECTION

                    // Calculate bank loan details based on amount, term, and interest rate
                    function calculateLoan() {
                        if (loanData.amount_requested > 0 && loanData.term_requested > 0 && loanData
                            .interest_rate > 0) {
                            // Calculate processing fee for bank loans
                            loanData.processing_fee = (loanData.amount_requested * loanData
                                .processing_fee_percentage / 100).toFixed(2);

                            // Calculate monthly interest rate (annual rate / 12)
                            const monthlyRate = loanData.interest_rate / 100 / 12;

                            // Calculate monthly payment using PMT formula: P * r * (1+r)^n / ((1+r)^n - 1)
                            const numerator = monthlyRate * Math.pow(1 + monthlyRate, loanData.term_requested);
                            const denominator = Math.pow(1 + monthlyRate, loanData.term_requested) - 1;
                            loanData.monthly_payment = loanData.amount_requested * (numerator / denominator);

                            // Calculate total repayment for bank loan
                            loanData.total_repayment = (loanData.monthly_payment * loanData.term_requested)
                                .toFixed(2);

                            // Update UI elements with bank loan specific formatting
                            updateBankLoanCalculationDisplay();
                        } else {
                            // Reset values if inputs are invalid
                            resetBankLoanCalculationDisplay();
                        }
                    }

                    // Update bank loan calculation display elements
                    function updateBankLoanCalculationDisplay() {
                        $('#monthly-payment').text(loanData.monthly_payment.toFixed(2));
                        $('#total-repayment').text(loanData.total_repayment);
                        $('#processing-fee').text(loanData.processing_fee);

                        // Update bank-specific information if available
                        if (loanData.bank_name) {
                            updateBankSpecificDisplay();
                        }
                    }

                    // Update bank-specific display elements
                    function updateBankSpecificDisplay() {
                        // Update any bank-specific calculation displays
                        const totalCost = parseFloat(loanData.total_repayment) + parseFloat(loanData
                            .processing_fee);

                        // Add bank loan identifier to calculations if needed
                        $('.loan-calculation-note').text(
                            `Bank loan calculations based on ${loanData.bank_name} terms`);
                    }

                    // Reset bank loan calculation display
                    function resetBankLoanCalculationDisplay() {
                        $('#monthly-payment').text('0.00');
                        $('#total-repayment').text('0.00');
                        $('#processing-fee').text('0.00');
                        $('.loan-calculation-note').text('');
                    }

                    // Event Handlers for Bank Loan Data Collection

                    // Bank loan type selection handler
                    $('#loan-type-select').change(function() {
                        let selectedOption = $(this).find('option:selected');

                        // Update loan data with bank-specific information
                        loanData.loan_type_id = $(this).val();

                        // Parse bank loan type format: "Loan Type - Bank Name (Interest%)"
                        const fullText = selectedOption.text();
                        const parts = fullText.split(' - ');

                        if (parts.length >= 2) {
                            loanData.loan_type_name = parts[0].trim();
                            const bankPart = parts[1].split('(')[0].trim();
                            loanData.bank_name = bankPart;
                        } else {
                            loanData.loan_type_name = fullText.split('(')[0].trim();
                            loanData.bank_name = 'Selected Bank';
                        }

                        // Extract bank loan parameters
                        loanData.interest_rate = parseFloat(selectedOption.data('interest')) || 0;
                        loanData.processing_fee_percentage = parseFloat(selectedOption.data('fee')) ||
                            0;

                        // Get amount and term constraints for bank loan
                        const minAmount = selectedOption.data('min-amount') || 0;
                        const maxAmount = selectedOption.data('max-amount') || 0;
                        const minTerm = selectedOption.data('min-term') || 0;
                        const maxTerm = selectedOption.data('max-term') || 0;

                        // Update UI with bank loan constraints
                        updateBankLoanConstraints(minAmount, maxAmount, minTerm, maxTerm);

                        // Update interest rate display
                        $('#interest-rate').text(loanData.interest_rate);

                        // Show bank-specific information
                        displayBankInformation();

                        // Recalculate bank loan details
                        calculateLoan();
                    });

                    // Update bank loan amount and term constraints in UI
                    function updateBankLoanConstraints(minAmount, maxAmount, minTerm, maxTerm) {
                        // Update range display text with bank loan formatting
                        $('#amount-range-text').text(
                            `Bank loan amount range: KES ${minAmount.toLocaleString()} - ${maxAmount.toLocaleString()}`
                        );
                        $('#term-range-text').text(`Bank loan term range: ${minTerm} - ${maxTerm} months`);

                        // Set min/max attributes on input fields
                        $('#loan-amount').attr({
                            'min': minAmount,
                            'max': maxAmount,
                            'placeholder': `Enter amount between ${minAmount.toLocaleString()} - ${maxAmount.toLocaleString()}`
                        });

                        $('#loan-term').attr({
                            'min': minTerm,
                            'max': maxTerm,
                            'placeholder': `Enter term between ${minTerm} - ${maxTerm} months`
                        });

                        // Clear existing values if they're outside the new bank loan range
                        const currentAmount = parseFloat($('#loan-amount').val()) || 0;
                        const currentTerm = parseInt($('#loan-term').val()) || 0;

                        if (currentAmount < minAmount || currentAmount > maxAmount) {
                            $('#loan-amount').val('');
                            loanData.amount_requested = 0;
                        }

                        if (currentTerm < minTerm || currentTerm > maxTerm) {
                            $('#loan-term').val('');
                            loanData.term_requested = 0;
                        }
                    }

                    // Display bank-specific information
                    function displayBankInformation() {
                        if (loanData.bank_name && loanData.loan_type_name) {
                            // Create or update bank info display
                            let bankInfoHtml = `
                                   <div class="alert alert-info mt-3" id="bank-info-display">
                                       <div class="d-flex align-items-center">
                                           <i class="bi bi-bank2 fs-4 me-3" style="color: #2c5aa0;"></i>
                                           <div>
                                               <strong>Selected Bank:</strong> ${loanData.bank_name}<br>
                                               <strong>Loan Product:</strong> ${loanData.loan_type_name}<br>
                                               <strong>Interest Rate:</strong> ${loanData.interest_rate}% per annum
                                           </div>
                                       </div>
                                   </div>
                               `;

                            // Remove existing bank info and add new one
                            $('#bank-info-display').remove();
                            $('#loan-type-select').parent().after(bankInfoHtml);
                        }
                    }

                    // Bank loan amount input handler
                    $('#loan-amount').on('input', function() {
                        const amount = parseFloat($(this).val()) || 0;
                        loanData.amount_requested = amount;

                        // Validate amount against bank loan constraints
                        validateBankAmountInput($(this));

                        // Recalculate bank loan
                        calculateLoan();
                    });

                    // Bank loan term input handler
                    $('#loan-term').on('input', function() {
                        const term = parseInt($(this).val()) || 0;
                        loanData.term_requested = term;

                        // Validate term against bank loan constraints
                        validateBankTermInput($(this));

                        // Recalculate bank loan
                        calculateLoan();
                    });

                    // Validate bank loan amount input in real-time
                    function validateBankAmountInput($element) {
                        const value = parseFloat($element.val()) || 0;
                        const min = parseFloat($element.attr('min')) || 0;
                        const max = parseFloat($element.attr('max')) || 0;

                        // Remove existing validation classes
                        $element.removeClass('is-invalid is-valid');

                        if (value > 0) {
                            if (value >= min && value <= max) {
                                $element.addClass('is-valid');
                                updateAmountFeedback($element, 'valid', '');
                            } else {
                                $element.addClass('is-invalid');
                                updateAmountFeedback($element, 'invalid',
                                    `Amount must be between KES ${min.toLocaleString()} and KES ${max.toLocaleString()}`
                                );
                            }
                        }
                    }

                    // Validate bank loan term input in real-time
                    function validateBankTermInput($element) {
                        const value = parseInt($element.val()) || 0;
                        const min = parseInt($element.attr('min')) || 0;
                        const max = parseInt($element.attr('max')) || 0;

                        // Remove existing validation classes
                        $element.removeClass('is-invalid is-valid');

                        if (value > 0) {
                            if (value >= min && value <= max) {
                                $element.addClass('is-valid');
                                updateTermFeedback($element, 'valid', '');
                            } else {
                                $element.addClass('is-invalid');
                                updateTermFeedback($element, 'invalid',
                                    `Term must be between ${min} and ${max} months`);
                            }
                        }
                    }

                    // Update amount validation feedback
                    function updateAmountFeedback($element, type, message) {
                        $element.siblings('.invalid-feedback, .valid-feedback').remove();
                        if (message) {
                            const feedbackClass = type === 'valid' ? 'valid-feedback' : 'invalid-feedback';
                            $element.after(`<div class="${feedbackClass}">${message}</div>`);
                        }
                    }

                    // Update term validation feedback
                    function updateTermFeedback($element, type, message) {
                        $element.siblings('.invalid-feedback, .valid-feedback').remove();
                        if (message) {
                            const feedbackClass = type === 'valid' ? 'valid-feedback' : 'invalid-feedback';
                            $element.after(`<div class="${feedbackClass}">${message}</div>`);
                        }
                    }

                    // Purpose category selection handler (same as SACCO but for bank loans)
                    $('#purpose-select').change(function() {
                        loanData.purpose_category = $(this).val();

                        // Add validation styling
                        if ($(this).val()) {
                            $(this).removeClass('is-invalid').addClass('is-valid');
                        }
                    });

                    // Purpose description input handler (same as SACCO but for bank loans)
                    $('#purpose-description').on('input', function() {
                        loanData.purpose = $(this).val();

                        // Add validation styling with bank loan specific minimum length
                        const minLength = 15; // Bank loans might require more detailed descriptions
                        if ($(this).val().trim().length >= minLength) {
                            $(this).removeClass('is-invalid').addClass('is-valid');
                        } else if ($(this).val().trim().length > 0) {
                            $(this).removeClass('is-valid').addClass('is-invalid');
                        }
                    });

                    // Additional info input handler
                    $('#additional-info').on('input', function() {
                        loanData.additional_info = $(this).val();
                    });

                    // Update bank loan summary display
                    function updateSummary() {
                        $('#summary-loan-type').text(loanData.loan_type_name || '-');
                        $('#summary-loan-amount').text(loanData.amount_requested ? loanData.amount_requested
                            .toLocaleString() : '0.00');
                        $('#summary-term').text(loanData.term_requested || '0');
                        $('#summary-interest-rate').text(loanData.interest_rate || '0');
                        $('#summary-processing-fee').text(loanData.processing_fee || '0.00');
                        $('#summary-purpose-category').text($('#purpose-select option:selected').text() || '-');
                        $('#summary-purpose-description').text(loanData.purpose || '-');
                        $('#summary-total-repayment').text(loanData.total_repayment || '0.00');

                        // Add bank-specific summary information
                        if (loanData.bank_name) {
                            updateBankSummaryInfo();
                        }
                    }

                    // Update bank-specific summary information
                    function updateBankSummaryInfo() {
                        // Add bank name to summary if not already present
                        let bankSummaryHtml = `
                                <div class="col-md-12 mt-3" id="bank-summary-info">
                                    <div class="alert alert-info">
                                        <strong><i class="bi bi-bank2 me-2"></i>Bank Partner:</strong> ${loanData.bank_name}
                                    </div>
                                </div>
                            `;

                        // Remove existing and add new bank summary
                        $('#bank-summary-info').remove();
                        $('#summary-total-repayment').closest('.col-md-12').after(bankSummaryHtml);
                    }

                    // Format currency for bank loans (same as SACCO)
                    function formatCurrency(amount) {
                        return new Intl.NumberFormat('en-KE', {
                            style: 'currency',
                            currency: 'KES',
                            minimumFractionDigits: 2
                        }).format(amount);
                    }

                    // Format number with commas (same as SACCO)
                    function formatNumber(number) {
                        return new Intl.NumberFormat('en-KE').format(number);
                    }

                    // Export bank loan functions to global scope
                    window.bankLoanApp = {
                        ...window.bankLoanApp, // Preserve Part 1 exports
                        calculateLoan: calculateLoan,
                        updateSummary: updateSummary,
                        displayBankInformation: displayBankInformation,
                        updateBankSummaryInfo: updateBankSummaryInfo,
                        formatCurrency: formatCurrency,
                        formatNumber: formatNumber
                    };
                    // Part 3 will handle form validation
                    // PART 3: COMPREHENSIVE BANK LOAN VALIDATION

                    // Bank loan validation configuration object
                    const bankValidationConfig = {
                        details: {
                            loan_type: {
                                required: true,
                                message: 'Please select a bank loan type'
                            },
                            bank_selection: {
                                required: true,
                                message: 'Bank information is required'
                            },
                            amount: {
                                required: true,
                                min: 0,
                                max: 0, // Will be set dynamically
                                message: 'Please enter a valid bank loan amount'
                            },
                            term: {
                                required: true,
                                min: 0,
                                max: 0, // Will be set dynamically
                                message: 'Please enter a valid bank loan term'
                            }
                        },
                        purpose: {
                            category: {
                                required: true,
                                message: 'Please select a purpose category for your bank loan'
                            },
                            description: {
                                required: true,
                                minLength: 15, // Bank loans require more detailed descriptions
                                maxLength: 500,
                                message: 'Please provide a detailed purpose description (minimum 15 characters)'
                            }
                        },
                        bankSpecific: {
                            documentation: {
                                required: false, // May be required based on bank
                                message: 'Additional documentation may be required by the bank'
                            }
                        }
                    };

                    // Main validation function for Bank Loan Details tab
                    function validateDetailsTab() {
                        let isValid = true;
                        const errors = [];

                        // Clear previous validation states
                        clearBankValidationState('#loan-details');

                        // Validate bank loan type selection
                        if (!loanData.loan_type_id) {
                            markBankFieldInvalid('#loan-type-select', bankValidationConfig.details.loan_type
                                .message);
                            errors.push(bankValidationConfig.details.loan_type.message);
                            isValid = false;
                        } else {
                            markBankFieldValid('#loan-type-select');
                        }

                        // Validate bank selection (specific to bank loans)
                        if (!loanData.bank_name || loanData.bank_name === 'Selected Bank') {
                            const bankError = 'Please select a valid bank and loan product';
                            markBankFieldInvalid('#loan-type-select', bankError);
                            errors.push(bankError);
                            isValid = false;
                        }

                        // Validate bank loan amount
                        const bankAmountValidation = validateBankLoanAmount();
                        if (!bankAmountValidation.isValid) {
                            markBankFieldInvalid('#loan-amount', bankAmountValidation.message);
                            errors.push(bankAmountValidation.message);
                            isValid = false;
                        } else {
                            markBankFieldValid('#loan-amount');
                        }

                        // Validate bank loan term
                        const bankTermValidation = validateBankLoanTerm();
                        if (!bankTermValidation.isValid) {
                            markBankFieldInvalid('#loan-term', bankTermValidation.message);
                            errors.push(bankTermValidation.message);
                            isValid = false;
                        } else {
                            markBankFieldValid('#loan-term');
                        }

                        // Bank-specific additional validations
                        const bankSpecificValidation = validateBankSpecificRequirements();
                        if (!bankSpecificValidation.isValid) {
                            errors.push(...bankSpecificValidation.errors);
                            isValid = false;
                        }

                        // Show bank loan validation summary if there are errors
                        if (!isValid) {
                            showBankValidationErrors(errors);
                        }

                        return isValid;
                    }

                    // Validate bank loan amount with bank-specific constraints
                    function validateBankLoanAmount() {
                        const amount = loanData.amount_requested;
                        const selectedOption = $('#loan-type-select').find('option:selected');
                        const minAmount = selectedOption.data('min-amount') || 0;
                        const maxAmount = selectedOption.data('max-amount') || 0;

                        if (!amount || amount <= 0) {
                            return {
                                isValid: false,
                                message: 'Please enter a valid bank loan amount'
                            };
                        }

                        if (amount < minAmount) {
                            return {
                                isValid: false,
                                message: `Bank loan amount must be at least KES ${minAmount.toLocaleString()}`
                            };
                        }

                        if (amount > maxAmount) {
                            return {
                                isValid: false,
                                message: `Bank loan amount cannot exceed KES ${maxAmount.toLocaleString()} for ${loanData.bank_name}`
                            };
                        }

                        // Bank-specific amount validation (example: certain banks have different requirements)
                        const bankAmountValidation = validateBankSpecificAmount(amount, loanData.bank_name);
                        if (!bankAmountValidation.isValid) {
                            return bankAmountValidation;
                        }

                        return {
                            isValid: true
                        };
                    }

                    // Validate bank loan term with bank-specific constraints
                    function validateBankLoanTerm() {
                        const term = loanData.term_requested;
                        const selectedOption = $('#loan-type-select').find('option:selected');
                        const minTerm = selectedOption.data('min-term') || 0;
                        const maxTerm = selectedOption.data('max-term') || 0;

                        if (!term || term <= 0) {
                            return {
                                isValid: false,
                                message: 'Please enter a valid bank loan term'
                            };
                        }

                        if (term < minTerm) {
                            return {
                                isValid: false,
                                message: `Bank loan term must be at least ${minTerm} months for ${loanData.bank_name}`
                            };
                        }

                        if (term > maxTerm) {
                            return {
                                isValid: false,
                                message: `Bank loan term cannot exceed ${maxTerm} months for ${loanData.bank_name}`
                            };
                        }

                        return {
                            isValid: true
                        };
                    }

                    // Validate bank-specific amount requirements
                    function validateBankSpecificAmount(amount, bankName) {
                        // Example: Different banks might have different minimum amounts
                        const bankSpecificRules = {
                            'KCB Bank': {
                                minAmount: 50000,
                                message: 'KCB Bank requires minimum KES 50,000'
                            },
                            'Equity Bank': {
                                minAmount: 25000,
                                message: 'Equity Bank requires minimum KES 25,000'
                            },
                            'Co-operative Bank': {
                                minAmount: 30000,
                                message: 'Co-operative Bank requires minimum KES 30,000'
                            }
                            // Add more bank-specific rules as needed
                        };

                        if (bankSpecificRules[bankName]) {
                            const rule = bankSpecificRules[bankName];
                            if (amount < rule.minAmount) {
                                return {
                                    isValid: false,
                                    message: rule.message
                                };
                            }
                        }

                        return {
                            isValid: true
                        };
                    }

                    // Validate bank-specific requirements
                    function validateBankSpecificRequirements() {
                        const errors = [];
                        let isValid = true;

                        // Check if bank loan interest rate is reasonable
                        if (loanData.interest_rate > 25) {
                            errors.push(
                                `Interest rate of ${loanData.interest_rate}% seems high. Please verify with ${loanData.bank_name}`
                            );
                            isValid = false;
                        }

                        // Check loan amount to income ratio (if we had income data)
                        // This would be more sophisticated in a real application
                        if (loanData.amount_requested > 500000) {
                            errors.push('Bank loans above KES 500,000 may require additional documentation');
                            // This is a warning, not a blocking error
                        }

                        return {
                            isValid,
                            errors
                        };
                    }

                    // Main validation function for Bank Loan Purpose tab
                    function validatePurposeTab() {
                        let isValid = true;
                        const errors = [];

                        // Clear previous validation states
                        clearBankValidationState('#loan-purpose');

                        // Validate purpose category for bank loan
                        if (!loanData.purpose_category) {
                            markBankFieldInvalid('#purpose-select', bankValidationConfig.purpose.category
                                .message);
                            errors.push(bankValidationConfig.purpose.category.message);
                            isValid = false;
                        } else {
                            markBankFieldValid('#purpose-select');
                        }

                        // Validate purpose description with bank loan requirements
                        const bankPurposeValidation = validateBankLoanPurposeDescription();
                        if (!bankPurposeValidation.isValid) {
                            markBankFieldInvalid('#purpose-description', bankPurposeValidation.message);
                            errors.push(bankPurposeValidation.message);
                            isValid = false;
                        } else {
                            markBankFieldValid('#purpose-description');
                        }

                        // Bank-specific purpose validation
                        const bankPurposeSpecific = validateBankSpecificPurpose();
                        if (!bankPurposeSpecific.isValid) {
                            errors.push(...bankPurposeSpecific.errors);
                            isValid = false;
                        }

                        // Show bank loan validation summary if there are errors
                        if (!isValid) {
                            showBankValidationErrors(errors);
                        }

                        return isValid;
                    }

                    // Validate bank loan purpose description
                    function validateBankLoanPurposeDescription() {
                        const description = loanData.purpose ? loanData.purpose.trim() : '';
                        const minLength = bankValidationConfig.purpose.description.minLength;
                        const maxLength = bankValidationConfig.purpose.description.maxLength;

                        if (!description) {
                            return {
                                isValid: false,
                                message: 'Please provide a detailed purpose description for your bank loan'
                            };
                        }

                        if (description.length < minLength) {
                            return {
                                isValid: false,
                                message: `Bank loan purpose description must be at least ${minLength} characters long`
                            };
                        }

                        if (description.length > maxLength) {
                            return {
                                isValid: false,
                                message: `Purpose description cannot exceed ${maxLength} characters`
                            };
                        }

                        return {
                            isValid: true
                        };
                    }

                    // Validate bank-specific purpose requirements
                    function validateBankSpecificPurpose() {
                        const errors = [];
                        let isValid = true;

                        // Check for restricted purposes (example)
                        const restrictedKeywords = ['gambling', 'speculation', 'illegal'];
                        const description = loanData.purpose.toLowerCase();

                        for (const keyword of restrictedKeywords) {
                            if (description.includes(keyword)) {
                                errors.push(`Bank loans cannot be used for ${keyword}-related activities`);
                                isValid = false;
                            }
                        }

                        // Check if purpose aligns with loan type
                        if (loanData.purpose_category === 'other' && loanData.purpose.trim().length < 30) {
                            errors.push(
                                'Please provide more detailed explanation for "Other" purpose category');
                            isValid = false;
                        }

                        return {
                            isValid,
                            errors
                        };
                    }

                    // Validate bank loan summary tab (final validation before submission)
                    function validateSummaryTab() {
                        let isValid = true;
                        const errors = [];

                        // Re-validate all previous tabs
                        if (!validateDetailsTab()) {
                            errors.push('Please fix errors in the Bank Loan Details section');
                            isValid = false;
                        }

                        if (!validatePurposeTab()) {
                            errors.push('Please fix errors in the Purpose & Details section');
                            isValid = false;
                        }

                        // Validate terms acceptance for bank loan
                        if (!$('#terms-checkbox').is(':checked')) {
                            markBankFieldInvalid('#terms-checkbox',
                                'Please accept the bank loan terms and conditions');
                            errors.push('Please accept the bank loan terms and conditions to proceed');
                            isValid = false;
                        } else {
                            markBankFieldValid('#terms-checkbox');
                        }

                        // Final bank loan validation checks
                        const finalBankValidation = performFinalBankValidation();
                        if (!finalBankValidation.isValid) {
                            errors.push(...finalBankValidation.errors);
                            isValid = false;
                        }

                        // Show validation summary if there are errors
                        if (!isValid) {
                            showBankValidationErrors(errors);
                        }

                        return isValid;
                    }

                    // Perform final bank loan validation
                    function performFinalBankValidation() {
                        const errors = [];
                        let isValid = true;

                        // Check if all required bank loan data is present
                        if (!loanData.bank_name || !loanData.loan_type_name) {
                            errors.push('Bank and loan type information is incomplete');
                            isValid = false;
                        }

                        // Validate final calculations
                        if (!loanData.total_repayment || parseFloat(loanData.total_repayment) <= 0) {
                            errors.push('Loan calculations appear to be incomplete');
                            isValid = false;
                        }

                        return {
                            isValid,
                            errors
                        };
                    }

                    // Bank loan visual validation helper functions
                    function markBankFieldValid(selector) {
                        const field = $(selector);
                        field.removeClass('is-invalid').addClass('is-valid');
                        field.next('.invalid-feedback').remove();
                    }

                    function markBankFieldInvalid(selector, message) {
                        const field = $(selector);
                        field.removeClass('is-valid').addClass('is-invalid');

                        // Remove existing feedback
                        field.next('.invalid-feedback').remove();

                        // Add new feedback message
                        field.after(
                            `<div class="invalid-feedback"><i class="bi bi-exclamation-triangle me-1"></i>${message}</div>`
                        );
                    }

                    function clearBankValidationState(container) {
                        $(container).find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
                        $(container).find('.invalid-feedback').remove();
                    }

                    function showBankValidationErrors(errors) {
                        if (errors.length > 0) {
                            const errorTitle = `Bank Loan Application Error${errors.length > 1 ? 's' : ''}`;
                            const errorMessage = errors.length === 1 ?
                                errors[0] :
                                `Please fix the following issues:\n• ${errors.join('\n• ')}`;

                            toastr.error(errorMessage, errorTitle);
                        }
                    }

                    // Real-time bank loan validation helpers
                    function enableBankRealTimeValidation() {
                        // Bank loan amount validation on blur
                        $('#loan-amount').on('blur', function() {
                            if ($(this).val()) {
                                const validation = validateBankLoanAmount();
                                if (validation.isValid) {
                                    markBankFieldValid('#loan-amount');
                                } else {
                                    markBankFieldInvalid('#loan-amount', validation.message);
                                }
                            }
                        });

                        // Bank loan term validation on blur
                        $('#loan-term').on('blur', function() {
                            if ($(this).val()) {
                                const validation = validateBankLoanTerm();
                                if (validation.isValid) {
                                    markBankFieldValid('#loan-term');
                                } else {
                                    markBankFieldInvalid('#loan-term', validation.message);
                                }
                            }
                        });

                        // Bank loan purpose description validation on blur
                        $('#purpose-description').on('blur', function() {
                            if ($(this).val()) {
                                const validation = validateBankLoanPurposeDescription();
                                if (validation.isValid) {
                                    markBankFieldValid('#purpose-description');
                                } else {
                                    markBankFieldInvalid('#purpose-description', validation.message);
                                }
                            }
                        });

                        // Real-time character count for bank loan purpose description
                        $('#purpose-description').on('input', function() {
                            const current = $(this).val().length;
                            const minimum = bankValidationConfig.purpose.description.minLength;
                            const maximum = bankValidationConfig.purpose.description.maxLength;
                            const remaining = Math.max(0, minimum - current);

                            // Update or create character counter
                            let counter = $(this).siblings('.character-counter');
                            if (counter.length === 0) {
                                counter = $('<small class="character-counter text-muted"></small>');
                                $(this).after(counter);
                            }

                            if (remaining > 0) {
                                counter.text(
                                    `${remaining} more characters needed (${current}/${maximum})`);
                                counter.removeClass('text-success text-warning').addClass('text-muted');
                            } else if (current > maximum) {
                                const excess = current - maximum;
                                counter.text(`${excess} characters over limit (${current}/${maximum})`);
                                counter.removeClass('text-success text-muted').addClass('text-warning');
                            } else {
                                counter.text(`${current}/${maximum} characters`);
                                counter.removeClass('text-muted text-warning').addClass('text-success');
                            }
                        });
                    }

                    // Initialize bank loan real-time validation when document is ready
                    $(document).ready(function() {
                        enableBankRealTimeValidation();
                    });

                    // Bank loan form completeness check
                    function checkBankFormCompleteness() {
                        const completeness = {
                            details: {
                                completed: validateDetailsTab(),
                                percentage: calculateBankDetailsCompleteness()
                            },
                            purpose: {
                                completed: validatePurposeTab(),
                                percentage: calculateBankPurposeCompleteness()
                            }
                        };

                        return completeness;
                    }

                    function calculateBankDetailsCompleteness() {
                        let completed = 0;
                        let total = 4; // Including bank selection

                        if (loanData.loan_type_id) completed++;
                        if (loanData.bank_name && loanData.bank_name !== 'Selected Bank') completed++;
                        if (loanData.amount_requested > 0) completed++;
                        if (loanData.term_requested > 0) completed++;

                        return Math.round((completed / total) * 100);
                    }

                    function calculateBankPurposeCompleteness() {
                        let completed = 0;
                        let total = 2;

                        if (loanData.purpose_category) completed++;
                        if (loanData.purpose && loanData.purpose.trim().length >= bankValidationConfig.purpose
                            .description.minLength) completed++;

                        return Math.round((completed / total) * 100);
                    }

                    // Export bank loan validation functions to global scope
                    window.bankLoanApp = {
                        ...window.bankLoanApp, // Preserve previous parts
                        validateDetailsTab: validateDetailsTab,
                        validatePurposeTab: validatePurposeTab,
                        validateSummaryTab: validateSummaryTab,
                        checkBankFormCompleteness: checkBankFormCompleteness,
                        markBankFieldValid: markBankFieldValid,
                        markBankFieldInvalid: markBankFieldInvalid,
                        clearBankValidationState: clearBankValidationState,
                        bankValidationConfig: bankValidationConfig
                    };
                    // Part 4 will handle form submission and status tab updates
                    // PART 4: BANK LOAN FORM SUBMISSION AND STATUS TAB UPDATES

                    // Bank loan form submission handler
                    $('#submitLoanApplication').click(function(e) {
                        e.preventDefault();

                        // Show bank loan loading state
                        showBankSubmissionLoading(true);

                        // Final validation before bank loan submission
                        if (!validateSummaryTab()) {
                            showBankSubmissionLoading(false);
                            return;
                        }

                        // Prepare and submit bank loan application
                        submitBankLoanApplication();
                    });

                    // Main bank loan submission function
                    function submitBankLoanApplication() {
                        // Prepare bank loan form data
                        let formData = new FormData();

                        // Add all bank loan data to form
                        Object.keys(loanData).forEach(key => {
                            formData.append(key, loanData[key]);
                        });

                        // Add bank-specific fields
                        formData.append('provider_type', 'bank');
                        formData.append('bank_name', loanData.bank_name);

                        // Submit bank loan AJAX request
                        $.ajax({
                            url: 'http://localhost/dfcs/ajax/loan-controller/submit-bank-application.php',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            timeout: 45000, // 45 second timeout for bank processing
                            success: function(response) {
                                handleBankSubmissionSuccess(response);
                            },
                            error: function(xhr, status, error) {
                                handleBankSubmissionError(xhr, status, error);
                            },
                            complete: function() {
                                showBankSubmissionLoading(false);
                            }
                        });
                    }

                    // Handle successful bank loan submission
                    function handleBankSubmissionSuccess(response) {
                        try {
                            let result = JSON.parse(response);

                            if (result.success) {
                                // Show bank loan success message
                                toastr.success('Bank loan application submitted successfully!',
                                    'Application Submitted');

                                // Update bank loan status tab with results
                                updateBankStatusTab(result);

                                // Navigate to bank loan status tab
                                window.bankLoanApp.goToStatusTab();

                                // Hide the submit button and show bank status
                                $('#submitLoanApplication').prop('disabled', true).html(
                                    '<i class="bi bi-check-circle me-2"></i>Bank Application Submitted'
                                );

                                // Update page title for bank loan
                                updatePageTitle('Bank Loan Application Status');

                                // Show bank-specific success notification
                                showBankSuccessNotification(result);

                            } else {
                                toastr.error(result.message || 'Error submitting bank loan application',
                                    'Submission Error');
                                highlightBankSubmissionErrors(result);
                            }
                        } catch (e) {
                            console.error('Error parsing bank loan response:', e);
                            toastr.error('Error processing bank response', 'System Error');
                        }
                    }

                    // Handle bank loan submission errors
                    function handleBankSubmissionError(xhr, status, error) {
                        console.error('Bank loan submission error:', {
                            xhr,
                            status,
                            error
                        });

                        let errorMessage = 'Error submitting bank loan application';

                        if (status === 'timeout') {
                            errorMessage = 'Bank processing timeout. Please try again or contact support.';
                        } else if (xhr.status === 400) {
                            errorMessage = 'Invalid bank loan data. Please review and try again.';
                        } else if (xhr.status === 401) {
                            errorMessage = 'Session expired. Please log in again.';
                            // Redirect to login after delay
                            setTimeout(() => {
                                window.location.href = '/login';
                            }, 3000);
                        } else if (xhr.status === 422) {
                            errorMessage = 'Bank loan validation failed. Please check your information.';
                        } else if (xhr.status >= 500) {
                            errorMessage = 'Bank system error. Please try again later or contact support.';
                        }

                        toastr.error(errorMessage, 'Bank Loan Error');
                    }

                    // Show/hide bank loan submission loading state
                    function showBankSubmissionLoading(show) {
                        const submitBtn = $('#submitLoanApplication');

                        if (show) {
                            submitBtn.prop('disabled', true).html(
                                '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Processing Bank Application...'
                            );
                        } else {
                            submitBtn.prop('disabled', false).html(
                                '<i class="bi bi-check-circle me-2"></i>Submit Bank Application'
                            );
                        }
                    }

                    // Update bank loan status tab with application results
                    function updateBankStatusTab(result) {
                        if (!result.success || !result.assessment) return;

                        const assessment = result.assessment;
                        const score = assessment.score;
                        const details = assessment.details;
                        const status = result.status;

                        // Update reference number for bank loan
                        $('#final-reference-number').text(result.reference_number || loanData.reference_number);

                        // Update main bank loan score display
                        updateBankMainScore(score);

                        // Update bank decision card based on status
                        updateBankDecisionCard(status, score, assessment.status_description);

                        // Update bank information section
                        updateBankInformationDisplay();

                        // Update individual score breakdowns
                        updateBankScoreBreakdowns(details);

                        // Update financial snapshot (if available in response)
                        if (result.financial_snapshot) {
                            updateBankFinancialSnapshot(result.financial_snapshot);
                        }

                        // Update bank processing timeline
                        updateBankProcessingTimeline(status);

                        // Update recommendations based on bank loan status
                        updateBankRecommendations(status, score, details);
                    }

                    // Update main bank loan creditworthiness score display
                    function updateBankMainScore(score) {
                        $('#score-value').text(score);
                        $('#score-text').text(score + '%');

                        // Animate progress bar for bank loan
                        const progressBar = $('#score-progress');
                        progressBar.css('width', '0%');

                        setTimeout(() => {
                            progressBar.css('width', score + '%');
                            progressBar.addClass('transition-all');
                        }, 500);

                        // Set bank loan specific colors based on score
                        if (score >= 70) {
                            progressBar.removeClass('bg-warning bg-danger').addClass('bg-success');
                        } else if (score >= 50) {
                            progressBar.removeClass('bg-success bg-danger').addClass('bg-warning');
                        } else {
                            progressBar.removeClass('bg-success bg-warning').addClass('bg-danger');
                        }
                    }

                    // Update bank decision card based on application status
                    function updateBankDecisionCard(status, score, description) {
                        const icon = $('#decision-icon');
                        const title = $('#decision-title');
                        const desc = $('#decision-description');
                        const badge = $('#status-badge');
                        const card = $('#decision-card');

                        // Reset classes
                        icon.removeClass().addClass('fs-1 me-3');
                        badge.removeClass().addClass('badge fs-6 p-3 shadow-sm');
                        card.removeClass().addClass('card shadow-sm border-0');

                        switch (status) {
                            case 'under_review':
                                icon.addClass('bi bi-bank text-success');
                                title.text('Bank Application Under Review');
                                desc.text(description ||
                                    `Your application is being reviewed by ${loanData.bank_name}. You will be contacted within 3-5 business days.`
                                );
                                badge.addClass('bg-success').html(
                                    '<i class="bi bi-clock-history me-1"></i>Bank Review');
                                card.addClass('border-success');
                                break;

                            case 'pending':
                                icon.addClass('bi bi-hourglass-split text-warning');
                                title.text('Bank Review Pending');
                                desc.text(description ||
                                    `${loanData.bank_name} requires additional assessment. Our team will coordinate with the bank.`
                                );
                                badge.addClass('bg-warning text-dark').html(
                                    '<i class="bi bi-exclamation-triangle me-1"></i>Pending Review');
                                card.addClass('border-warning');
                                break;

                            case 'rejected':
                                icon.addClass('bi bi-x-circle-fill text-danger');
                                title.text('Bank Application Declined');
                                desc.text(description ||
                                    `Your application did not meet ${loanData.bank_name}'s current lending criteria.`
                                );
                                badge.addClass('bg-danger').html('<i class="bi bi-x-circle me-1"></i>Declined');
                                card.addClass('border-danger');
                                break;

                            default:
                                icon.addClass('bi bi-bank text-info');
                                title.text('Bank Application Processing');
                                desc.text(
                                    `Your application has been submitted to ${loanData.bank_name} and is being processed.`
                                );
                                badge.addClass('bg-info').html('<i class="bi bi-clock me-1"></i>Processing');
                                card.addClass('border-info');
                        }
                    }

                    // Update bank information display in status tab
                    function updateBankInformationDisplay() {
                        $('#selected-bank-name').text(loanData.bank_name || 'Selected Bank');
                        $('#selected-loan-type').text(loanData.loan_type_name || 'Bank Loan Product');
                        $('#bank-interest-rate').text((loanData.interest_rate || 0) + '%');
                    }

                    // Update individual score breakdown bars (same as SACCO but with bank context)
                    function updateBankScoreBreakdowns(details) {
                        updateBankScoreBar('repayment', details.repayment_history);
                        updateBankScoreBar('obligations', details.financial_obligations);
                        updateBankScoreBar('produce', details.produce_history);
                        updateBankScoreBar('ratio', details.amount_ratio);
                    }

                    // Update individual score progress bar
                    function updateBankScoreBar(type, score) {
                        const progress = $('#' + type + '-progress');
                        const scoreText = $('#' + type + '-score');

                        // Animate the progress bar with staggered timing
                        progress.css('width', '0%');
                        setTimeout(() => {
                            progress.css('width', score + '%');
                            scoreText.text(score);
                        }, 300 + (Math.random() * 200));
                    }

                    // Update financial snapshot section for bank loan
                    function updateBankFinancialSnapshot(snapshot) {
                        $('#active-loans-count').text(snapshot.active_loans_count || 0);
                        $('#active-loans-balance').text(formatCurrency(snapshot.active_loans_balance || 0));

                        $('#input-credits-count').text(snapshot.input_credits_count || 0);
                        $('#input-credits-balance').text(formatCurrency(snapshot.input_credits_balance || 0));

                        $('#deliveries-count').text(snapshot.deliveries_count || 0);
                        $('#deliveries-value').text(formatCurrency(snapshot.deliveries_value || 0));

                        $('#credit-capacity').text((snapshot.credit_capacity || 0) + '%');
                    }

                    // Update bank processing timeline
                    function updateBankProcessingTimeline(status) {
                        const timelineSteps = $('.bank-timeline-step');

                        // Reset all steps
                        timelineSteps.removeClass('completed current pending');

                        // Update based on status
                        switch (status) {
                            case 'under_review':
                                // Application submitted (completed), Bank review (current)
                                timelineSteps.eq(0).addClass('completed');
                                timelineSteps.eq(1).addClass('current');
                                break;
                            case 'pending':
                                // Application submitted (completed), Bank review (current)
                                timelineSteps.eq(0).addClass('completed');
                                timelineSteps.eq(1).addClass('current');
                                break;
                            case 'approved':
                                // First 3 steps completed, disbursement current
                                timelineSteps.slice(0, 3).addClass('completed');
                                timelineSteps.eq(3).addClass('current');
                                break;
                            default:
                                // Only first step completed
                                timelineSteps.eq(0).addClass('completed');
                        }
                    }

                    // Update recommendations section based on bank loan status
                    function updateBankRecommendations(status, score, details) {
                        const container = $('#next-steps-content');
                        let content = '';

                        if (status === 'under_review') {
                            content = generateBankApprovedRecommendations();
                        } else if (status === 'pending') {
                            content = generateBankPendingRecommendations(score, details);
                        } else if (status === 'rejected') {
                            content = generateBankRejectedRecommendations(details);
                        } else {
                            content = generateBankDefaultRecommendations();
                        }

                        container.html(content);
                    }

                    // Generate recommendations for bank approved applications
                    function generateBankApprovedRecommendations() {
                        return `
                                 <div class="alert alert-success">
                                     <h6><i class="bi bi-bank me-2"></i>Bank Review in Progress</h6>
                                     <p class="mb-2">Great news! Your application is being reviewed by ${loanData.bank_name}. Here's what happens next:</p>
                                     <ul class="mb-3">
                                         <li><strong>Bank Review:</strong> ${loanData.bank_name} will assess your application within 3-5 business days</li>
                                         <li><strong>Documentation:</strong> The bank may request additional documents</li>
                                         <li><strong>Verification:</strong> Bank may contact you for income/produce verification</li>
                                         <li><strong>Decision:</strong> Final approval decision will be communicated directly by the bank</li>
                                     </ul>
                                     <div class="alert alert-info mb-0">
                                         <small><i class="bi bi-telephone me-1"></i>The bank may contact you directly for verification or additional information.</small>
                                     </div>
                                 </div>
                             `;
                    }

                    // Generate recommendations for bank pending applications
                    function generateBankPendingRecommendations(score, details) {
                        return `
                                  <div class="alert alert-warning">
                                      <h6><i class="bi bi-exclamation-triangle me-2"></i>Additional Bank Assessment Required</h6>
                                      <p class="mb-2">Your application requires further review by ${loanData.bank_name}. To strengthen your application:</p>
                                      <ul class="mb-3">
                                          <li>Maintain consistent produce deliveries over the next 2-3 months</li>
                                          <li>Consider providing additional income documentation to the bank</li>
                                          <li>Work on reducing existing debt obligations if possible</li>
                                          <li>Contact ${loanData.bank_name} directly for specific requirements</li>
                                      </ul>
                                      <div class="alert alert-info mb-0">
                                          <small><i class="bi bi-clock me-1"></i>Bank review timeline: 5-10 business days for comprehensive assessment</small>
                                      </div>
                                  </div>
                              `;
                    }

                    // Generate recommendations for bank rejected applications
                    function generateBankRejectedRecommendations(details) {
                        const improvements = [];

                        if (details.repayment_history < 50) {
                            improvements.push('Complete any existing loan obligations successfully');
                        }
                        if (details.financial_obligations < 50) {
                            improvements.push('Reduce current debt-to-income ratio');
                        }
                        if (details.produce_history < 50) {
                            improvements.push('Establish consistent produce delivery history (6+ months)');
                        }
                        if (details.amount_ratio < 50) {
                            improvements.push(
                                'Apply for a smaller loan amount more aligned with your produce value');
                        }

                        return `
                             <div class="alert alert-danger">
                                 <h6><i class="bi bi-x-circle me-2"></i>Bank Application Declined</h6>
                                 <p class="mb-2">Your application did not meet ${loanData.bank_name}'s current lending criteria. To improve future applications:</p>
                                 <ul class="mb-3">
                                     ${improvements.map(item => `<li>${item}</li>`).join('')}
                                     <li>Consider alternative loan products from other partner banks</li>
                                     <li>Build stronger financial profile before reapplying</li>
                                 </ul>
                                 <div class="row">
                                     <div class="col-md-6">
                                         <div class="alert alert-info mb-0">
                                             <small><i class="bi bi-clock me-1"></i><strong>Recommended wait time:</strong> 6 months before reapplying to ${loanData.bank_name}</small>
                                         </div>
                                     </div>
                                     <div class="col-md-6">
                                         <div class="alert alert-success mb-0">
                                             <small><i class="bi bi-lightbulb me-1"></i><strong>Alternative:</strong> Consider SACCO loans with different criteria</small>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         `;
                    }

                    // Generate default bank recommendations
                    function generateBankDefaultRecommendations() {
                        return `
                              <div class="alert alert-info">
                                  <h6><i class="bi bi-info-circle me-2"></i>Bank Application Processing</h6>
                                  <p class="mb-2">Your application has been submitted to ${loanData.bank_name} and is being processed.</p>
                                  <ul class="mb-0">
                                      <li>You will receive SMS/email notifications about status changes</li>
                                      <li>The bank may contact you directly for additional information</li>
                                      <li>Expected processing time: 3-7 business days</li>
                                  </ul>
                              </div>
                          `;
                    }

                    // Show bank-specific success notification
                    function showBankSuccessNotification(result) {
                        // Create a more detailed success notification for bank loans
                        setTimeout(() => {
                            toastr.info(
                                `Your application has been forwarded to ${loanData.bank_name}. Reference: ${result.reference_number}`,
                                'Bank Processing Started', {
                                    timeOut: 8000
                                }
                            );
                        }, 2000);
                    }

                    // Highlight bank submission errors (if specific field errors are returned)
                    function highlightBankSubmissionErrors(result) {
                        if (result.field_errors) {
                            Object.keys(result.field_errors).forEach(field => {
                                const selector = getBankFieldSelector(field);
                                if (selector) {
                                    window.bankLoanApp.markBankFieldInvalid(selector, result
                                        .field_errors[field]);
                                }
                            });
                        }
                    }

                    // Map backend field names to frontend selectors for bank loans
                    function getBankFieldSelector(fieldName) {
                        const fieldMap = {
                            'loan_type_id': '#loan-type-select',
                            'amount_requested': '#loan-amount',
                            'term_requested': '#loan-term',
                            'purpose': '#purpose-description',
                            'purpose_category': '#purpose-select',
                            'bank_name': '#loan-type-select' // Bank name issues map to loan type selector
                        };

                        return fieldMap[fieldName] || null;
                    }

                    // Copy reference function for bank loans
                    function copyReference() {
                        const referenceText = document.getElementById('final-reference-number').textContent;
                        navigator.clipboard.writeText(referenceText).then(function() {
                            toastr.success('Bank loan reference number copied to clipboard!', 'Copied');
                        });
                    }

                    // Prevent form submission on enter key (except in textareas)
                    $(document).on('keypress', function(e) {
                        if (e.which === 13 && e.target.tagName !== 'TEXTAREA') {
                            e.preventDefault();
                        }
                    });

                    // Export bank loan functions to global scope
                    window.bankLoanApp = {
                        ...window.bankLoanApp, // Preserve previous parts
                        submitBankLoanApplication: submitBankLoanApplication,
                        updateBankStatusTab: updateBankStatusTab,
                        handleBankSubmissionSuccess: handleBankSubmissionSuccess,
                        handleBankSubmissionError: handleBankSubmissionError,
                        updateBankProcessingTimeline: updateBankProcessingTimeline,
                        showBankSuccessNotification: showBankSuccessNotification
                    };
                });
                </script>


</body>

</html>