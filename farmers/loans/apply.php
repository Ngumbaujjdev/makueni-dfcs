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

/* Custom scrollbar for content areas */
#next-steps-content::-webkit-scrollbar {
    width: 6px;
}

#next-steps-content::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

#next-steps-content::-webkit-scrollbar-thumb {
    background: #70A136;
    border-radius: 3px;
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
                    <h1 class="page-title fw-semibold fs-18 mb-0">Apply for SACCO Loan</h1>
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
                        <div class="card-title">SACCO Loan Application</div>
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
                                        <label class="form-label">Select Loan Type</label>
                                        <select class="form-control" id="loan-type-select" name="loan_type_id" required>
                                            <option value="">Select a loan type...</option>
                                            <?php
                                                   $query = "SELECT id, name, interest_rate, min_amount, max_amount, min_term, max_term, processing_fee 
                                                             FROM loan_types 
                                                             WHERE provider_type = 'sacco' AND is_active = 1";
                                                   $loan_types = $app->select_all($query);
                                                   foreach($loan_types as $loan_type): ?>
                                            <option value="<?php echo $loan_type->id; ?>"
                                                data-interest="<?php echo $loan_type->interest_rate; ?>"
                                                data-min-amount="<?php echo $loan_type->min_amount; ?>"
                                                data-max-amount="<?php echo $loan_type->max_amount; ?>"
                                                data-min-term="<?php echo $loan_type->min_term; ?>"
                                                data-max-term="<?php echo $loan_type->max_term; ?>"
                                                data-fee="<?php echo $loan_type->processing_fee; ?>">
                                                <?php echo $loan_type->name; ?>
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
                            <!-- final tab -->
                            <!-- 4. Application Status & Financial Overview Tab -->
                            <div class="tab-pane fade" id="loan-status" role="tabpanel">
                                <div class="row gy-4">
                                    <!-- Application Decision Header -->
                                    <div class="col-xl-12">
                                        <div class="card shadow-sm border-0" id="decision-card"
                                            style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                            <div class="card-header d-flex align-items-center"
                                                style="background: linear-gradient(135deg, #70A136 0%, #6AA32D 100%); border: none;">
                                                <div class="me-3">
                                                    <i class="bi bi-check-circle-fill text-white fs-1"
                                                        id="decision-icon"
                                                        style="text-shadow: 0 2px 4px rgba(0,0,0,0.3);"></i>
                                                </div>
                                                <div class="text-white">
                                                    <h5 class="card-title mb-0 fw-bold" id="decision-title"
                                                        style="text-shadow: 0 1px 2px rgba(0,0,0,0.3);">Application
                                                        Status</h5>
                                                    <p class="card-text mb-0 opacity-90" id="decision-description">
                                                        Processing your application...</p>
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

                                    <!-- Score Breakdown -->
                                    <div class="col-xl-12">
                                        <div class="card shadow-sm border-0">
                                            <div class="card-header"
                                                style="background: linear-gradient(135deg, #4A220F 0%, #5D2A13 100%); border: none;">
                                                <h6 class="card-title mb-0 text-white fw-semibold">
                                                    <i class="bi bi-graph-up me-2"></i>Score Breakdown
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
                                                            <strong style="color: #4A220F;">Application
                                                                Reference:</strong>
                                                            <br>
                                                            <span id="final-reference-number" class="fw-bold fs-5"
                                                                style="color: #70A136;">LOAN/20250526/0001</span>
                                                            <br>
                                                            <small class="text-muted">
                                                                <i class="bi bi-info-circle me-1"></i>
                                                                Please save this reference number for future
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
                // PART 1: TAB MANAGEMENT AND NAVIGATION SYSTEM

                $(document).ready(function() {
                    // Initialize select2
                    $('.select2').select2();

                    // Store all loan application data
                    let loanData = {
                        loan_type_id: '',
                        loan_type_name: '',
                        interest_rate: 0,
                        amount_requested: 0,
                        term_requested: 0,
                        processing_fee: 0,
                        monthly_payment: 0,
                        total_repayment: 0,
                        purpose_category: '',
                        purpose: '',
                        additional_info: '',
                        reference_number: $('#loan-reference').text()
                    };

                    // Tab definitions - Updated to include the new status tab
                    const tabs = {
                        details: '#loan-details',
                        purpose: '#loan-purpose',
                        summary: '#loan-summary',
                        status: '#loan-status' // New status tab
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

                        // Status tab is only shown after successful submission
                        if (tabId === '#loan-status') {
                            // This tab should only be accessible after form submission
                            // Additional logic can be added here if needed
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

                    // Navigate to status tab (after successful submission)
                    function goToStatusTab() {
                        showTab(tabs.status);
                        // Hide the status tab from regular navigation after showing it
                        $('[data-bs-target="#loan-status"]').parent().addClass('d-none');
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

                    // Export functions to global scope for use in other parts
                    window.loanApp = {
                        showTab: showTab,
                        goToStatusTab: goToStatusTab,
                        goToNextTab: goToNextTab,
                        goToPreviousTab: goToPreviousTab,
                        loanData: loanData,
                        validateDetailsTab: validateDetailsTab,
                        validatePurposeTab: validatePurposeTab
                    };


                    // Part 2 will handle loan calculations and data collection
                    // PART 2: LOAN CALCULATIONS AND DATA COLLECTION

                    // Calculate loan details based on amount, term, and interest rate
                    function calculateLoan() {
                        if (loanData.amount_requested > 0 && loanData.term_requested > 0 && loanData
                            .interest_rate > 0) {
                            // Calculate processing fee
                            loanData.processing_fee = (loanData.amount_requested * loanData
                                .processing_fee_percentage / 100).toFixed(2);

                            // Calculate monthly interest rate (annual rate / 12)
                            const monthlyRate = loanData.interest_rate / 100 / 12;

                            // Calculate monthly payment using PMT formula: P * r * (1+r)^n / ((1+r)^n - 1)
                            const numerator = monthlyRate * Math.pow(1 + monthlyRate, loanData.term_requested);
                            const denominator = Math.pow(1 + monthlyRate, loanData.term_requested) - 1;
                            loanData.monthly_payment = loanData.amount_requested * (numerator / denominator);

                            // Calculate total repayment
                            loanData.total_repayment = (loanData.monthly_payment * loanData.term_requested)
                                .toFixed(2);

                            // Update UI elements
                            updateLoanCalculationDisplay();
                        } else {
                            // Reset values if inputs are invalid
                            resetLoanCalculationDisplay();
                        }
                    }

                    // Update loan calculation display elements
                    function updateLoanCalculationDisplay() {
                        $('#monthly-payment').text(loanData.monthly_payment.toFixed(2));
                        $('#total-repayment').text(loanData.total_repayment);
                        $('#processing-fee').text(loanData.processing_fee);
                    }

                    // Reset loan calculation display
                    function resetLoanCalculationDisplay() {
                        $('#monthly-payment').text('0.00');
                        $('#total-repayment').text('0.00');
                        $('#processing-fee').text('0.00');
                    }

                    // Event Handlers for Data Collection

                    // Loan type selection handler
                    $('#loan-type-select').change(function() {
                        let selectedOption = $(this).find('option:selected');

                        // Update loan data
                        loanData.loan_type_id = $(this).val();
                        loanData.loan_type_name = selectedOption.text().split('(')[0].trim();
                        loanData.interest_rate = parseFloat(selectedOption.data('interest')) || 0;
                        loanData.processing_fee_percentage = parseFloat(selectedOption.data('fee')) ||
                            0;

                        // Get amount and term constraints
                        const minAmount = selectedOption.data('min-amount') || 0;
                        const maxAmount = selectedOption.data('max-amount') || 0;
                        const minTerm = selectedOption.data('min-term') || 0;
                        const maxTerm = selectedOption.data('max-term') || 0;

                        // Update UI with constraints
                        updateAmountTermConstraints(minAmount, maxAmount, minTerm, maxTerm);

                        // Update interest rate display
                        $('#interest-rate').text(loanData.interest_rate);

                        // Recalculate loan details
                        calculateLoan();
                    });

                    // Update amount and term constraints in UI
                    function updateAmountTermConstraints(minAmount, maxAmount, minTerm, maxTerm) {
                        // Update range display text
                        $('#amount-range-text').text(
                            `Amount range: KES ${minAmount.toLocaleString()} - ${maxAmount.toLocaleString()}`
                        );
                        $('#term-range-text').text(`Term range: ${minTerm} - ${maxTerm} months`);

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

                        // Clear existing values if they're outside the new range
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

                    // Loan amount input handler
                    $('#loan-amount').on('input', function() {
                        const amount = parseFloat($(this).val()) || 0;
                        loanData.amount_requested = amount;

                        // Validate amount against constraints
                        validateAmountInput($(this));

                        // Recalculate loan
                        calculateLoan();
                    });

                    // Loan term input handler
                    $('#loan-term').on('input', function() {
                        const term = parseInt($(this).val()) || 0;
                        loanData.term_requested = term;

                        // Validate term against constraints
                        validateTermInput($(this));

                        // Recalculate loan
                        calculateLoan();
                    });

                    // Validate amount input in real-time
                    function validateAmountInput($element) {
                        const value = parseFloat($element.val()) || 0;
                        const min = parseFloat($element.attr('min')) || 0;
                        const max = parseFloat($element.attr('max')) || 0;

                        // Remove existing validation classes
                        $element.removeClass('is-invalid is-valid');

                        if (value > 0) {
                            if (value >= min && value <= max) {
                                $element.addClass('is-valid');
                            } else {
                                $element.addClass('is-invalid');
                            }
                        }
                    }

                    // Validate term input in real-time
                    function validateTermInput($element) {
                        const value = parseInt($element.val()) || 0;
                        const min = parseInt($element.attr('min')) || 0;
                        const max = parseInt($element.attr('max')) || 0;

                        // Remove existing validation classes
                        $element.removeClass('is-invalid is-valid');

                        if (value > 0) {
                            if (value >= min && value <= max) {
                                $element.addClass('is-valid');
                            } else {
                                $element.addClass('is-invalid');
                            }
                        }
                    }

                    // Purpose category selection handler
                    $('#purpose-select').change(function() {
                        loanData.purpose_category = $(this).val();

                        // Add validation styling
                        if ($(this).val()) {
                            $(this).removeClass('is-invalid').addClass('is-valid');
                        }
                    });

                    // Purpose description input handler
                    $('#purpose-description').on('input', function() {
                        loanData.purpose = $(this).val();

                        // Add validation styling
                        if ($(this).val().trim().length > 10) {
                            $(this).removeClass('is-invalid').addClass('is-valid');
                        } else if ($(this).val().trim().length > 0) {
                            $(this).removeClass('is-valid').addClass('is-invalid');
                        }
                    });

                    // Additional info input handler
                    $('#additional-info').on('input', function() {
                        loanData.additional_info = $(this).val();
                    });

                    // Update summary display
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
                    }

                    // Format currency for display
                    function formatCurrency(amount) {
                        return new Intl.NumberFormat('en-KE', {
                            style: 'currency',
                            currency: 'KES',
                            minimumFractionDigits: 2
                        }).format(amount);
                    }

                    // Format number with commas
                    function formatNumber(number) {
                        return new Intl.NumberFormat('en-KE').format(number);
                    }

                    // Export functions to global scope
                    window.loanApp = {
                        ...window.loanApp, // Preserve Part 1 exports
                        calculateLoan: calculateLoan,
                        updateSummary: updateSummary,
                        formatCurrency: formatCurrency,
                        formatNumber: formatNumber
                    };
                    // Part 3 will handle form validation
                    // PART 3: COMPREHENSIVE FORM VALIDATION

                    // Validation configuration object
                    const validationConfig = {
                        details: {
                            loan_type: {
                                required: true,
                                message: 'Please select a loan type'
                            },
                            amount: {
                                required: true,
                                min: 0,
                                max: 0, // Will be set dynamically
                                message: 'Please enter a valid loan amount'
                            },
                            term: {
                                required: true,
                                min: 0,
                                max: 0, // Will be set dynamically
                                message: 'Please enter a valid loan term'
                            }
                        },
                        purpose: {
                            category: {
                                required: true,
                                message: 'Please select a purpose category'
                            },
                            description: {
                                required: true,
                                minLength: 10,
                                message: 'Please provide a detailed purpose description (minimum 10 characters)'
                            }
                        }
                    };

                    // Main validation function for Details tab
                    function validateDetailsTab() {
                        let isValid = true;
                        const errors = [];

                        // Clear previous validation states
                        clearValidationState('#loan-details');

                        // Validate loan type selection
                        if (!loanData.loan_type_id) {
                            markFieldInvalid('#loan-type-select', validationConfig.details.loan_type.message);
                            errors.push(validationConfig.details.loan_type.message);
                            isValid = false;
                        } else {
                            markFieldValid('#loan-type-select');
                        }

                        // Validate loan amount
                        const amountValidation = validateAmount();
                        if (!amountValidation.isValid) {
                            markFieldInvalid('#loan-amount', amountValidation.message);
                            errors.push(amountValidation.message);
                            isValid = false;
                        } else {
                            markFieldValid('#loan-amount');
                        }

                        // Validate loan term
                        const termValidation = validateTerm();
                        if (!termValidation.isValid) {
                            markFieldInvalid('#loan-term', termValidation.message);
                            errors.push(termValidation.message);
                            isValid = false;
                        } else {
                            markFieldValid('#loan-term');
                        }

                        // Show validation summary if there are errors
                        if (!isValid) {
                            showValidationErrors(errors);
                        }

                        return isValid;
                    }

                    // Validate loan amount with constraints
                    function validateAmount() {
                        const amount = loanData.amount_requested;
                        const selectedOption = $('#loan-type-select').find('option:selected');
                        const minAmount = selectedOption.data('min-amount') || 0;
                        const maxAmount = selectedOption.data('max-amount') || 0;

                        if (!amount || amount <= 0) {
                            return {
                                isValid: false,
                                message: 'Please enter a valid loan amount'
                            };
                        }

                        if (amount < minAmount) {
                            return {
                                isValid: false,
                                message: `Loan amount must be at least KES ${minAmount.toLocaleString()}`
                            };
                        }

                        if (amount > maxAmount) {
                            return {
                                isValid: false,
                                message: `Loan amount cannot exceed KES ${maxAmount.toLocaleString()}`
                            };
                        }

                        return {
                            isValid: true
                        };
                    }

                    // Validate loan term with constraints
                    function validateTerm() {
                        const term = loanData.term_requested;
                        const selectedOption = $('#loan-type-select').find('option:selected');
                        const minTerm = selectedOption.data('min-term') || 0;
                        const maxTerm = selectedOption.data('max-term') || 0;

                        if (!term || term <= 0) {
                            return {
                                isValid: false,
                                message: 'Please enter a valid loan term'
                            };
                        }

                        if (term < minTerm) {
                            return {
                                isValid: false,
                                message: `Loan term must be at least ${minTerm} months`
                            };
                        }

                        if (term > maxTerm) {
                            return {
                                isValid: false,
                                message: `Loan term cannot exceed ${maxTerm} months`
                            };
                        }

                        return {
                            isValid: true
                        };
                    }

                    // Main validation function for Purpose tab
                    function validatePurposeTab() {
                        let isValid = true;
                        const errors = [];

                        // Clear previous validation states
                        clearValidationState('#loan-purpose');

                        // Validate purpose category
                        if (!loanData.purpose_category) {
                            markFieldInvalid('#purpose-select', validationConfig.purpose.category.message);
                            errors.push(validationConfig.purpose.category.message);
                            isValid = false;
                        } else {
                            markFieldValid('#purpose-select');
                        }

                        // Validate purpose description
                        const purposeValidation = validatePurposeDescription();
                        if (!purposeValidation.isValid) {
                            markFieldInvalid('#purpose-description', purposeValidation.message);
                            errors.push(purposeValidation.message);
                            isValid = false;
                        } else {
                            markFieldValid('#purpose-description');
                        }

                        // Show validation summary if there are errors
                        if (!isValid) {
                            showValidationErrors(errors);
                        }

                        return isValid;
                    }

                    // Validate purpose description
                    function validatePurposeDescription() {
                        const description = loanData.purpose ? loanData.purpose.trim() : '';
                        const minLength = validationConfig.purpose.description.minLength;

                        if (!description) {
                            return {
                                isValid: false,
                                message: 'Please provide a detailed purpose description'
                            };
                        }

                        if (description.length < minLength) {
                            return {
                                isValid: false,
                                message: `Purpose description must be at least ${minLength} characters long`
                            };
                        }

                        return {
                            isValid: true
                        };
                    }

                    // Validate summary tab (final validation before submission)
                    function validateSummaryTab() {
                        let isValid = true;
                        const errors = [];

                        // Re-validate all previous tabs
                        if (!validateDetailsTab()) {
                            errors.push('Please fix errors in the Loan Details section');
                            isValid = false;
                        }

                        if (!validatePurposeTab()) {
                            errors.push('Please fix errors in the Purpose & Details section');
                            isValid = false;
                        }

                        // Validate terms acceptance
                        if (!$('#terms-checkbox').is(':checked')) {
                            markFieldInvalid('#terms-checkbox', 'Please accept the terms and conditions');
                            errors.push('Please accept the terms and conditions to proceed');
                            isValid = false;
                        } else {
                            markFieldValid('#terms-checkbox');
                        }

                        // Show validation summary if there are errors
                        if (!isValid) {
                            showValidationErrors(errors);
                        }

                        return isValid;
                    }

                    // Visual validation helper functions
                    function markFieldValid(selector) {
                        const field = $(selector);
                        field.removeClass('is-invalid').addClass('is-valid');
                        field.next('.invalid-feedback').remove();
                    }

                    function markFieldInvalid(selector, message) {
                        const field = $(selector);
                        field.removeClass('is-valid').addClass('is-invalid');

                        // Remove existing feedback
                        field.next('.invalid-feedback').remove();

                        // Add new feedback message
                        field.after(`<div class="invalid-feedback">${message}</div>`);
                    }

                    function clearValidationState(container) {
                        $(container).find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
                        $(container).find('.invalid-feedback').remove();
                    }

                    function showValidationErrors(errors) {
                        if (errors.length > 0) {
                            const errorMessage = errors.length === 1 ?
                                errors[0] :
                                `Please fix the following errors:\n• ${errors.join('\n• ')}`;

                            toastr.error(errorMessage);
                        }
                    }

                    // Real-time validation helpers
                    function enableRealTimeValidation() {
                        // Amount validation on blur
                        $('#loan-amount').on('blur', function() {
                            if ($(this).val()) {
                                const validation = validateAmount();
                                if (validation.isValid) {
                                    markFieldValid('#loan-amount');
                                } else {
                                    markFieldInvalid('#loan-amount', validation.message);
                                }
                            }
                        });

                        // Term validation on blur
                        $('#loan-term').on('blur', function() {
                            if ($(this).val()) {
                                const validation = validateTerm();
                                if (validation.isValid) {
                                    markFieldValid('#loan-term');
                                } else {
                                    markFieldInvalid('#loan-term', validation.message);
                                }
                            }
                        });

                        // Purpose description validation on blur
                        $('#purpose-description').on('blur', function() {
                            if ($(this).val()) {
                                const validation = validatePurposeDescription();
                                if (validation.isValid) {
                                    markFieldValid('#purpose-description');
                                } else {
                                    markFieldInvalid('#purpose-description', validation.message);
                                }
                            }
                        });

                        // Real-time character count for purpose description
                        $('#purpose-description').on('input', function() {
                            const current = $(this).val().length;
                            const minimum = validationConfig.purpose.description.minLength;
                            const remaining = Math.max(0, minimum - current);

                            // Update or create character counter
                            let counter = $(this).siblings('.character-counter');
                            if (counter.length === 0) {
                                counter = $('<small class="character-counter text-muted"></small>');
                                $(this).after(counter);
                            }

                            if (remaining > 0) {
                                counter.text(`${remaining} more characters needed`);
                                counter.removeClass('text-success').addClass('text-muted');
                            } else {
                                counter.text(`${current} characters`);
                                counter.removeClass('text-muted').addClass('text-success');
                            }
                        });
                    }

                    // Initialize real-time validation when document is ready
                    $(document).ready(function() {
                        enableRealTimeValidation();
                    });

                    // Form data completeness check
                    function checkFormCompleteness() {
                        const completeness = {
                            details: {
                                completed: validateDetailsTab(),
                                percentage: calculateDetailsCompleteness()
                            },
                            purpose: {
                                completed: validatePurposeTab(),
                                percentage: calculatePurposeCompleteness()
                            }
                        };

                        return completeness;
                    }

                    function calculateDetailsCompleteness() {
                        let completed = 0;
                        let total = 3;

                        if (loanData.loan_type_id) completed++;
                        if (loanData.amount_requested > 0) completed++;
                        if (loanData.term_requested > 0) completed++;

                        return Math.round((completed / total) * 100);
                    }

                    function calculatePurposeCompleteness() {
                        let completed = 0;
                        let total = 2;

                        if (loanData.purpose_category) completed++;
                        if (loanData.purpose && loanData.purpose.trim().length >= 10) completed++;

                        return Math.round((completed / total) * 100);
                    }

                    // Export validation functions to global scope
                    window.loanApp = {
                        ...window.loanApp, // Preserve previous parts
                        validateDetailsTab: validateDetailsTab,
                        validatePurposeTab: validatePurposeTab,
                        validateSummaryTab: validateSummaryTab,
                        checkFormCompleteness: checkFormCompleteness,
                        markFieldValid: markFieldValid,
                        markFieldInvalid: markFieldInvalid,
                        clearValidationState: clearValidationState
                    };
                    // Part 4 will handle form submission and status tab updates
                    // PART 4: FORM SUBMISSION AND STATUS TAB UPDATES

                    // Form submission handler
                    $('#submitLoanApplication').click(function(e) {
                        e.preventDefault();

                        // Show loading state
                        showSubmissionLoading(true);

                        // Final validation before submission
                        if (!validateSummaryTab()) {
                            showSubmissionLoading(false);
                            return;
                        }

                        // Prepare and submit form data
                        submitLoanApplication();
                    });

                    // Main form submission function
                    function submitLoanApplication() {
                        // Prepare form data
                        let formData = new FormData();

                        // Add all loan data to form
                        Object.keys(loanData).forEach(key => {
                            formData.append(key, loanData[key]);
                        });

                        // Submit AJAX request
                        $.ajax({
                            url: 'http://localhost/dfcs/ajax/loan-controller/submit-application.php',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            timeout: 30000, // 30 second timeout
                            success: function(response) {
                                handleSubmissionSuccess(response);
                            },
                            error: function(xhr, status, error) {
                                handleSubmissionError(xhr, status, error);
                            },
                            complete: function() {
                                showSubmissionLoading(false);
                            }
                        });
                    }

                    // Handle successful form submission
                    function handleSubmissionSuccess(response) {
                        try {
                            let result = JSON.parse(response);

                            if (result.success) {
                                // Show success message
                                toastr.success('Loan application submitted successfully!');

                                // Update status tab with results
                                updateStatusTab(result);

                                // Navigate to status tab
                                window.loanApp.goToStatusTab();

                                // Hide the submit button and show status
                                $('#submitLoanApplication').prop('disabled', true).html(
                                    '<i class="bi bi-check-circle me-2"></i>Application Submitted'
                                );

                                // Update page title
                                updatePageTitle('Application Status');

                            } else {
                                toastr.error(result.message || 'Error submitting application');
                                highlightSubmissionErrors(result);
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                            toastr.error('Error processing server response');
                        }
                    }

                    // Handle submission errors
                    function handleSubmissionError(xhr, status, error) {
                        console.error('Submission error:', {
                            xhr,
                            status,
                            error
                        });

                        let errorMessage = 'Error submitting application';

                        if (status === 'timeout') {
                            errorMessage = 'Request timed out. Please try again.';
                        } else if (xhr.status === 400) {
                            errorMessage = 'Invalid application data. Please review and try again.';
                        } else if (xhr.status === 401) {
                            errorMessage = 'Session expired. Please log in again.';
                            // Redirect to login after delay
                            setTimeout(() => {
                                window.location.href = '/login';
                            }, 3000);
                        } else if (xhr.status >= 500) {
                            errorMessage = 'Server error. Please try again later.';
                        }

                        toastr.error(errorMessage);
                    }

                    // Show/hide submission loading state
                    function showSubmissionLoading(show) {
                        const submitBtn = $('#submitLoanApplication');

                        if (show) {
                            submitBtn.prop('disabled', true).html(
                                '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Submitting...'
                            );
                        } else {
                            submitBtn.prop('disabled', false).html(
                                '<i class="bi bi-check-circle me-2"></i>Submit Application'
                            );
                        }
                    }

                    // Update status tab with application results
                    function updateStatusTab(result) {
                        if (!result.success || !result.assessment) return;

                        const assessment = result.assessment;
                        const score = assessment.score;
                        const details = assessment.details;
                        const status = result.status;

                        // Update reference number
                        $('#final-reference-number').text(result.reference_number || loanData.reference_number);

                        // Update main score display
                        updateMainScore(score);

                        // Update decision card based on status
                        updateDecisionCard(status, score, assessment.status_description);

                        // Update individual score breakdowns
                        updateScoreBreakdowns(details);

                        // Update financial snapshot (if available in response)
                        if (result.financial_snapshot) {
                            updateFinancialSnapshot(result.financial_snapshot);
                        }

                        // Update recommendations based on status
                        updateRecommendations(status, score, details);
                    }

                    // Update main creditworthiness score display
                    function updateMainScore(score) {
                        $('#score-value').text(score);
                        $('#score-text').text(score + '%');

                        // Animate progress bar
                        const progressBar = $('#score-progress');
                        progressBar.css('width', '0%');

                        setTimeout(() => {
                            progressBar.css('width', score + '%');
                            progressBar.addClass('transition-all');
                        }, 500);

                        // Set color based on score
                        if (score >= 70) {
                            progressBar.removeClass('bg-warning bg-danger').addClass('bg-success');
                        } else if (score >= 50) {
                            progressBar.removeClass('bg-success bg-danger').addClass('bg-warning');
                        } else {
                            progressBar.removeClass('bg-success bg-warning').addClass('bg-danger');
                        }
                    }

                    // Update decision card based on application status
                    function updateDecisionCard(status, score, description) {
                        const icon = $('#decision-icon');
                        const title = $('#decision-title');
                        const desc = $('#decision-description');
                        const badge = $('#status-badge');
                        const card = $('#decision-card');

                        // Reset classes
                        icon.removeClass().addClass('fs-2 me-3');
                        badge.removeClass().addClass('badge fs-6 p-2');
                        card.removeClass().addClass('card');

                        switch (status) {
                            case 'under_review':
                                icon.addClass('bi bi-check-circle-fill text-success');
                                title.text('Application Approved for Review');
                                desc.text(description ||
                                    'Your application has passed initial screening and is being reviewed by staff.'
                                );
                                badge.addClass('bg-success').text('Approved for Review');
                                card.addClass('border-success');
                                break;

                            case 'pending':
                                icon.addClass('bi bi-clock-fill text-warning');
                                title.text('Additional Review Required');
                                desc.text(description ||
                                    'Your application requires additional assessment by our team.');
                                badge.addClass('bg-warning text-dark').text('Under Review');
                                card.addClass('border-warning');
                                break;

                            case 'rejected':
                                icon.addClass('bi bi-x-circle-fill text-danger');
                                title.text('Application Declined');
                                desc.text(description ||
                                    'Your application was automatically declined based on current financial profile.'
                                );
                                badge.addClass('bg-danger').text('Declined');
                                card.addClass('border-danger');
                                break;

                            default:
                                icon.addClass('bi bi-info-circle-fill text-info');
                                title.text('Application Submitted');
                                desc.text('Your application is being processed.');
                                badge.addClass('bg-info').text('Processing');
                                card.addClass('border-info');
                        }
                    }

                    // Update individual score breakdown bars
                    function updateScoreBreakdowns(details) {
                        updateScoreBar('repayment', details.repayment_history);
                        updateScoreBar('obligations', details.financial_obligations);
                        updateScoreBar('produce', details.produce_history);
                        updateScoreBar('ratio', details.amount_ratio);
                    }

                    // Update individual score progress bar
                    function updateScoreBar(type, score) {
                        const progress = $('#' + type + '-progress');
                        const scoreText = $('#' + type + '-score');

                        // Animate the progress bar
                        progress.css('width', '0%');
                        setTimeout(() => {
                            progress.css('width', score + '%');
                            scoreText.text(score);
                        }, 300 + (Math.random() * 200)); // Stagger animations
                    }

                    // Update financial snapshot section
                    function updateFinancialSnapshot(snapshot) {
                        $('#active-loans-count').text(snapshot.active_loans_count || 0);
                        $('#active-loans-balance').text(formatCurrency(snapshot.active_loans_balance || 0));

                        $('#input-credits-count').text(snapshot.input_credits_count || 0);
                        $('#input-credits-balance').text(formatCurrency(snapshot.input_credits_balance || 0));

                        $('#deliveries-count').text(snapshot.deliveries_count || 0);
                        $('#deliveries-value').text(formatCurrency(snapshot.deliveries_value || 0));

                        $('#credit-capacity').text((snapshot.credit_capacity || 0) + '%');
                    }

                    // Update recommendations section based on status
                    function updateRecommendations(status, score, details) {
                        const container = $('#next-steps-content');
                        let content = '';

                        if (status === 'under_review') {
                            content = generateApprovedRecommendations();
                        } else if (status === 'pending') {
                            content = generatePendingRecommendations(score, details);
                        } else if (status === 'rejected') {
                            content = generateRejectedRecommendations(details);
                        } else {
                            content = generateDefaultRecommendations();
                        }

                        container.html(content);
                    }

                    // Generate recommendations for approved applications
                    function generateApprovedRecommendations() {
                        return `
                            <div class="alert alert-success">
                                <h6><i class="bi bi-check-circle me-2"></i>Congratulations!</h6>
                                <p class="mb-2">Your application has been approved for staff review. Here's what happens next:</p>
                                <ul class="mb-0">
                                    <li>Our team will review your application within 48 hours</li>
                                    <li>You may be contacted for additional documentation</li>
                                    <li>Final approval decision will be communicated via SMS/email</li>
                                    <li>Upon approval, funds will be disbursed within 72 hours</li>
                                </ul>
                            </div>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Track your application status in the Applications section of your dashboard.
                                </small>
                            </div>
                        `;
                    }

                    // Generate recommendations for pending applications
                    function generatePendingRecommendations(score, details) {
                        return `
                           <div class="alert alert-warning">
                               <h6><i class="bi bi-exclamation-triangle me-2"></i>Additional Review Required</h6>
                               <p class="mb-2">Your application needs further assessment. To improve your chances:</p>
                               <ul class="mb-0">
                                   <li>Maintain consistent produce deliveries over the next few months</li>
                                   <li>Consider paying down existing obligations if possible</li>
                                   <li>Contact our support team for personalized advice</li>
                                   <li>Monitor your application status for updates</li>
                               </ul>
                           </div>
                           <div class="mt-3">
                               <small class="text-muted">
                                   <i class="bi bi-clock me-1"></i>
                                   Review timeline: 5-7 business days
                               </small>
                           </div>
                       `;
                    }

                    // Generate recommendations for rejected applications
                    function generateRejectedRecommendations(details) {
                        const improvements = [];

                        if (details.repayment_history < 50) {
                            improvements.push('Focus on completing any existing loan obligations');
                        }
                        if (details.financial_obligations < 50) {
                            improvements.push('Reduce current debt levels before applying');
                        }
                        if (details.produce_history < 50) {
                            improvements.push('Increase delivery frequency and value over the next 6 months');
                        }
                        if (details.amount_ratio < 50) {
                            improvements.push('Consider applying for a smaller loan amount');
                        }

                        return `
                           <div class="alert alert-danger">
                               <h6><i class="bi bi-x-circle me-2"></i>Application Declined</h6>
                               <p class="mb-2">To improve your creditworthiness for future applications:</p>
                               <ul class="mb-2">
                                   ${improvements.map(item => `<li>${item}</li>`).join('')}
                               </ul>
                               <p class="mb-0"><strong>Recommended wait time:</strong> 3-6 months before reapplying</p>
                           </div>
                           <div class="mt-3">
                               <small class="text-muted">
                                   <i class="bi bi-question-circle me-1"></i>
                                   Contact our support team for detailed guidance on improving your application.
                               </small>
                           </div>
                       `;
                    }

                    // Generate default recommendations
                    function generateDefaultRecommendations() {
                        return `
                              <div class="alert alert-info">
                                  <h6><i class="bi bi-info-circle me-2"></i>Application Processing</h6>
                                  <p class="mb-0">Your application is being processed. You will receive updates shortly.</p>
                              </div>
                          `;
                    }

                    // Update page title
                    function updatePageTitle(newTitle) {
                        document.title = newTitle + ' - DFCS Loan Application';
                        $('.page-title').text(newTitle);
                    }

                    // Highlight submission errors (if specific field errors are returned)
                    function highlightSubmissionErrors(result) {
                        if (result.field_errors) {
                            Object.keys(result.field_errors).forEach(field => {
                                const selector = getFieldSelector(field);
                                if (selector) {
                                    window.loanApp.markFieldInvalid(selector, result.field_errors[
                                        field]);
                                }
                            });
                        }
                    }

                    // Map backend field names to frontend selectors
                    function getFieldSelector(fieldName) {
                        const fieldMap = {
                            'loan_type_id': '#loan-type-select',
                            'amount_requested': '#loan-amount',
                            'term_requested': '#loan-term',
                            'purpose': '#purpose-description',
                            'purpose_category': '#purpose-select'
                        };

                        return fieldMap[fieldName] || null;
                    }

                    // Prevent form submission on enter key (except in textareas)
                    $(document).on('keypress', function(e) {
                        if (e.which === 13 && e.target.tagName !== 'TEXTAREA') {
                            e.preventDefault();
                        }
                    });

                    // Export functions to global scope
                    window.loanApp = {
                        ...window.loanApp, // Preserve previous parts
                        submitLoanApplication: submitLoanApplication,
                        updateStatusTab: updateStatusTab,
                        handleSubmissionSuccess: handleSubmissionSuccess,
                        handleSubmissionError: handleSubmissionError,
                        updatePageTitle: updatePageTitle
                    };

                });
                </script>


</body>

</html>