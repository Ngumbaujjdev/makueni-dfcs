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
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">

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
                <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
                    <h1 class="page-title fw-semibold fs-18 mb-0">Apply for Input Credit</h1>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Input Credits</a></li>
                                <li class="breadcrumb-item active" aria-current="page">New Application</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <!-- Tabs Navigation -->
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">Input Credit Application</div>
                    </div>
                    <div class="card-body add-products p-0">
                        <ul class="nav nav-tabs" id="inputCreditTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#agrovet-selection"
                                    type="button" role="tab">
                                    <i class="bi bi-shop me-1"></i>Agrovet Selection
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#input-selection"
                                    type="button" role="tab">
                                    <i class="bi bi-cart-plus me-1"></i>Input Selection
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#credit-terms"
                                    type="button" role="tab">
                                    <i class="bi bi-cash-coin me-1"></i>Credit Terms
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#credit-summary"
                                    type="button" role="tab">
                                    <i class="bi bi-clipboard-check me-1"></i>Summary & Submit
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#credit-status"
                                    type="button" role="tab">
                                    <i class="bi bi-clipboard-data me-1"></i>Application Status
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content Container -->
                        <div class="tab-content p-4">
                            <!-- 1. Agrovet Selection Tab -->
                            <div class="tab-pane fade show active" id="agrovet-selection" role="tabpanel">
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
                                        </div>
                                    </div>

                                    <div class="col-xl-12">
                                        <label class="form-label">Select Agrovet</label>
                                        <select class="form-control" id="agrovet-select" name="agrovet_id" required>
                                            <option value="">Select an agrovet...</option>
                                            <?php
                                               $query = "SELECT a.id, a.name, a.location
                                                         FROM agrovets a 
                                                         WHERE a.is_active = 1
                                                         ORDER BY a.name";
                                               $agrovets = $app->select_all($query);
                                               foreach($agrovets as $agrovet): ?>
                                            <option value="<?php echo $agrovet->id; ?>">
                                                <?php echo $agrovet->name; ?> (<?php echo $agrovet->location; ?>)
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-xl-12 mt-3">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6 class="card-title">Selected Agrovet Details</h6>
                                                <div id="agrovet-details">
                                                    <p class="text-muted">Please select an agrovet to view details</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button class="btn text-white" id="nextToInputs" style="background:#6AA32D;">
                                        Next <i class="bi bi-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- 2. Input Selection Tab -->
                            <!-- 2. Input Selection Tab -->
                            <div class="tab-pane fade" id="input-selection" role="tabpanel">
                                <div class="row">
                                    <div class="col-xl-12 mb-3">
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle me-2"></i>
                                            <span>Available inputs from <strong id="selected-agrovet-name">Selected
                                                    Agrovet</strong></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Input Category Dropdown -->
                                <div class="form-group mb-3">
                                    <label class="form-label">Select Input Category</label>
                                    <select id="input-category-selector" class="form-control">
                                        <option value="fertilizer">Fertilizers</option>
                                        <option value="seeds">Seeds</option>
                                        <option value="pesticide">Pesticides</option>
                                        <option value="tools">Tools & Equipment</option>
                                        <option value="other">Other Inputs</option>
                                    </select>
                                </div>

                                <!-- Single Table for Category Items -->
                                <div id="category-items-container" class="mb-4">
                                    <div class="table-responsive">
                                        <table id="catalog-table" class="table table-bordered table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Input Name</th>
                                                    <th>Description</th>
                                                    <th>Unit</th>
                                                    <th>Price (KES)</th>
                                                    <th>Quantity</th>
                                                    <th>Total</th>
                                                    <th width="100">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="category-items">
                                                <!-- Items will be loaded here -->
                                                <tr>
                                                    <td colspan="7" class="text-center">Select an agrovet to view items
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Hidden containers for storing category data -->
                                <div style="display: none;">
                                    <div id="fertilizer-items"></div>
                                    <div id="seed-items"></div>
                                    <div id="pesticide-items"></div>
                                    <div id="tool-items"></div>
                                    <div id="other-items"></div>
                                </div>

                                <!-- Selected Items Summary -->
                                <div class="card bg-light mt-4">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Selected Inputs</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table id="selected-items-table" class="table table-bordered table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Input Name</th>
                                                        <th>Type</th>
                                                        <th>Quantity</th>
                                                        <th>Unit Price</th>
                                                        <th>Total</th>
                                                        <th width="100">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="selected-items">
                                                    <!-- No items selected message -->
                                                    <tr id="no-items-row">
                                                        <td colspan="6" class="text-center">No inputs selected yet</td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="4" class="text-end">Total Amount:</th>
                                                        <th id="total-input-amount">KES 0.00</th>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button class="btn btn-light" id="backToAgrovet">
                                        <i class="bi bi-arrow-left me-2"></i>Previous
                                    </button>
                                    <button class="btn text-white" id="nextToTerms" style="background:#6AA32D;">
                                        Next <i class="bi bi-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- 3. Credit Terms Tab -->
                            <div class="tab-pane fade" id="credit-terms" role="tabpanel">
                                <div class="row gy-3">
                                    <div class="col-xl-12">
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle me-2"></i>
                                            <span>Total input value: <strong>KES <span
                                                        id="credit-total-amount">0.00</span></strong></span>
                                        </div>
                                    </div>

                                    <div class="col-xl-6">
                                        <label class="form-label">Credit Percentage (%)</label>
                                        <input type="number" class="form-control" id="credit-percentage"
                                            name="credit_percentage" value="10" min="5" max="15" required>
                                        <div class="form-text">Interest rate for this credit</div>
                                    </div>

                                    <div class="col-xl-6">
                                        <label class="form-label">Repayment Percentage (%)</label>
                                        <input type="number" class="form-control" id="repayment-percentage"
                                            name="repayment_percentage" value="30" min="10" max="50" required>
                                        <div class="form-text">Percentage to deduct from your produce sales</div>
                                    </div>

                                    <div class="col-xl-12 mt-3">
                                        <label class="form-label">Purpose/Intended Use</label>
                                        <textarea class="form-control" id="credit-purpose" name="purpose" rows="3"
                                            required
                                            placeholder="Please describe how you plan to use these inputs"></textarea>
                                    </div>

                                    <div class="col-xl-12 mt-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <h6 class="card-title">Credit Calculation</h6>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p class="mb-2">Total Input Value: KES <span
                                                                id="calc-input-value">0.00</span></p>
                                                        <p class="mb-2">Interest Amount: KES <span
                                                                id="calc-interest-amount">0.00</span></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p class="mb-2">Repayment Method: <span>Produce Sale
                                                                Deductions</span></p>
                                                        <h5 class="mb-0">Total Repayment Amount: KES <span
                                                                id="calc-total-repayment">0.00</span></h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button class="btn btn-light" id="backToInputs">
                                        <i class="bi bi-arrow-left me-2"></i>Previous
                                    </button>
                                    <button class="btn text-white" id="nextToSummary" style="background:#6AA32D;">
                                        Next <i class="bi bi-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- 4. Summary & Submit Tab -->
                            <div class="tab-pane fade" id="credit-summary" role="tabpanel">
                                <div class="row gy-3">
                                    <!-- Credit Application Summary Card -->
                                    <div class="col-xl-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">Input Credit Application Summary</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row gy-3">
                                                    <!-- Reference Number -->
                                                    <div class="col-md-12">
                                                        <div class="alert alert-info mb-3">
                                                            <strong>Credit Application Reference Number:</strong>
                                                            <span id="credit-reference">
                                                                <?php 
                                                             // Generate reference number: ICREDIT/YYYYMMDD/RANDOM4DIGITS
                                                             $refNumber = 'ICREDIT/' . date('Ymd') . '/' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
                                                             echo $refNumber;
                                                             ?>
                                                            </span>
                                                            <input type="hidden" name="reference_number"
                                                                value="<?php echo $refNumber; ?>">
                                                        </div>
                                                    </div>

                                                    <!-- Summary Row -->
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Selected Agrovet</label>
                                                        <p class="mb-0"><span id="summary-agrovet-name">-</span></p>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Total Input Value</label>
                                                        <p class="mb-0">KES <span id="summary-input-value">0.00</span>
                                                        </p>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Credit Percentage</label>
                                                        <p class="mb-0"><span id="summary-credit-percentage">0</span>%
                                                        </p>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Repayment
                                                            Percentage</label>
                                                        <p class="mb-0"><span
                                                                id="summary-repayment-percentage">0</span>%</p>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <label class="form-label fw-semibold">Purpose</label>
                                                        <p class="mb-0"><span id="summary-purpose">-</span></p>
                                                    </div>

                                                    <!-- Selected Items -->
                                                    <div class="col-md-12 mt-3">
                                                        <label class="form-label fw-semibold">Selected Inputs</label>
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Input Name</th>
                                                                        <th>Type</th>
                                                                        <th>Quantity</th>
                                                                        <th>Unit Price</th>
                                                                        <th>Total</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="summary-selected-items">
                                                                    <tr>
                                                                        <td colspan="5" class="text-center">No inputs
                                                                            selected</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
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
                                                            <strong>Repayment Method:</strong> Credit repayments will be
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
                                                credit terms and conditions
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="col-xl-12">
                                        <div class="d-flex justify-content-between mt-4">
                                            <button class="btn btn-light" id="backToTerms">
                                                <i class="bi bi-arrow-left me-2"></i>Previous
                                            </button>
                                            <button type="submit" class="btn btn-success" id="submitCreditApplication">
                                                <i class="bi bi-check-circle me-2"></i>Submit Application
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- 5. Input Credit Application Status Tab -->
                            <div class="tab-pane fade" id="credit-status" role="tabpanel">
                                <div class="row gy-4">
                                    <!-- Main Status Card -->
                                    <div class="col-12">
                                        <div class="card border-0 shadow-sm" id="credit-decision-card">
                                            <div class="card-header text-white"
                                                style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-check-circle fs-2 me-3" id="status-icon"></i>
                                                    <div>
                                                        <h5 class="mb-1" id="status-title">Input Credit Application
                                                            Status</h5>
                                                        <p class="mb-0 opacity-90" id="status-description">Processing
                                                            your application...</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body p-4">
                                                <div class="row align-items-center">
                                                    <div class="col-md-8">
                                                        <h6 class="text-muted mb-2">Creditworthiness Score</h6>
                                                        <div class="progress mb-2" style="height: 25px;">
                                                            <div class="progress-bar bg-success"
                                                                id="credit-score-progress" role="progressbar"
                                                                style="width: 0%;">
                                                                <span id="credit-score-text" class="fw-bold">0%</span>
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">Score: <span
                                                                id="credit-score-value">0</span>/100</small>
                                                    </div>
                                                    <div class="col-md-4 text-end">
                                                        <span class="badge bg-success fs-6 p-3"
                                                            id="application-status-badge">
                                                            <i class="bi bi-hourglass-half me-1"></i>Processing
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Agrovet Partner Info -->
                                    <div class="col-md-6">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <div class="card-header bg-primary text-white">
                                                <h6 class="mb-0"><i class="bi bi-shop me-2"></i>Agrovet Partner</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-building fs-1 text-primary me-3"></i>
                                                    <div>
                                                        <h5 class="mb-1" id="partner-agrovet-name">Selected Agrovet</h5>
                                                        <p class="text-muted mb-1" id="partner-agrovet-location">
                                                            Location</p>
                                                        <small class="text-success">
                                                            <i class="bi bi-check-circle me-1"></i>Active Partner
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Credit Summary -->
                                    <div class="col-md-6">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <div class="card-header bg-info text-white">
                                                <h6 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Credit Summary</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row g-3">
                                                    <div class="col-6">
                                                        <small class="text-muted">Input Value</small>
                                                        <h6 class="mb-0">KES <span id="status-input-value">0.00</span>
                                                        </h6>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted">Interest Rate</small>
                                                        <h6 class="mb-0"><span id="status-interest-rate">0</span>%</h6>
                                                    </div>
                                                    <div class="col-12">
                                                        <small class="text-muted">Total Repayment</small>
                                                        <h4 class="text-success mb-0">KES <span
                                                                id="status-total-repayment">0.00</span></h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Assessment Breakdown -->
                                    <div class="col-12">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-header bg-dark text-white">
                                                <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Credit Assessment
                                                    Breakdown</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row g-4">
                                                    <!-- Repayment History -->
                                                    <div class="col-md-6">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-2">
                                                            <span class="fw-semibold">Repayment History</span>
                                                            <span class="badge bg-secondary">30% Weight</span>
                                                        </div>
                                                        <div class="progress mb-1" style="height: 20px;">
                                                            <div class="progress-bar bg-primary" id="repayment-progress"
                                                                role="progressbar" style="width: 0%;">
                                                                <span id="repayment-score" class="fw-bold">0</span>
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">Past credit performance</small>
                                                    </div>

                                                    <!-- Financial Obligations -->
                                                    <div class="col-md-6">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-2">
                                                            <span class="fw-semibold">Financial Obligations</span>
                                                            <span class="badge bg-secondary">25% Weight</span>
                                                        </div>
                                                        <div class="progress mb-1" style="height: 20px;">
                                                            <div class="progress-bar bg-info" id="obligations-progress"
                                                                role="progressbar" style="width: 0%;">
                                                                <span id="obligations-score" class="fw-bold">0</span>
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">Current debt-to-income ratio</small>
                                                    </div>

                                                    <!-- Produce History -->
                                                    <div class="col-md-6">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-2">
                                                            <span class="fw-semibold">Produce History</span>
                                                            <span class="badge bg-secondary">35% Weight</span>
                                                        </div>
                                                        <div class="progress mb-1" style="height: 20px;">
                                                            <div class="progress-bar bg-warning" id="produce-progress"
                                                                role="progressbar" style="width: 0%;">
                                                                <span id="produce-score"
                                                                    class="fw-bold text-dark">0</span>
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">Delivery consistency (6
                                                            months)</small>
                                                    </div>

                                                    <!-- Amount Ratio -->
                                                    <div class="col-md-6">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-2">
                                                            <span class="fw-semibold">Credit Amount Ratio</span>
                                                            <span class="badge bg-secondary">10% Weight</span>
                                                        </div>
                                                        <div class="progress mb-1" style="height: 20px;">
                                                            <div class="progress-bar bg-secondary" id="ratio-progress"
                                                                role="progressbar" style="width: 0%;">
                                                                <span id="ratio-score" class="fw-bold">0</span>
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">Credit vs. produce value</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Next Steps -->
                                    <div class="col-12">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-header bg-warning text-dark">
                                                <h6 class="mb-0"><i class="bi bi-lightbulb me-2"></i>Next Steps</h6>
                                            </div>
                                            <div class="card-body" id="next-steps-content">
                                                <div class="d-flex align-items-center justify-content-center p-4">
                                                    <div class="spinner-border text-success me-3" role="status"></div>
                                                    <span class="text-muted">Loading recommendations...</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Reference Number -->
                                    <div class="col-12">
                                        <div class="alert alert-light border">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <i class="bi bi-bookmark-check text-success fs-4 me-3"></i>
                                                    <strong>Application Reference:</strong>
                                                    <span id="final-reference-number"
                                                        class="fw-bold text-primary ms-2">ICREDIT/20250527/0001</span>
                                                </div>
                                                <button class="btn btn-outline-primary btn-sm"
                                                    onclick="copyReference()">
                                                    <i class="bi bi-clipboard me-1"></i>Copy
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
                <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js">
                </script>
                <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js">
                </script>
                <script>
                $(document).ready(function() {
                    // Initialize select2 for dropdowns
                    $('.select2').select2();

                    // Store all input credit application data
                    let creditData = {
                        agrovet_id: '',
                        agrovet_name: '',
                        agrovet_location: '',
                        selected_inputs: [], // Array to store selected input items
                        total_amount: 0,
                        credit_percentage: 10,
                        total_with_interest: 0,
                        repayment_percentage: 30,
                        purpose: '',
                        reference_number: $('#credit-reference').text()
                    };

                    // PART 1: INPUT CREDIT TAB MANAGEMENT AND NAVIGATION SYSTEM

                    // Tab definitions - Updated to include the new status tab
                    const tabs = {
                        agrovet: '#agrovet-selection',
                        inputs: '#input-selection',
                        terms: '#credit-terms',
                        summary: '#credit-summary',
                        status: '#credit-status' // New input credit status tab
                    };

                    // Tab order for navigation validation
                    const tabOrder = ['agrovet', 'inputs', 'terms', 'summary', 'status'];

                    // Show specified tab function
                    function showTab(tabId) {
                        // Remove active classes from all tabs
                        $('.nav-link').removeClass('active');
                        $('.tab-pane').removeClass('show active');

                        // Add active classes to target tab
                        $(`[data-bs-target="${tabId}"]`).addClass('active');
                        $(tabId).addClass('show active');

                        // Special handling for specific tabs
                        if (tabId === tabs.summary) {
                            updateSummary();
                        }

                        // Input credit status tab handling
                        if (tabId === tabs.status) {
                            // Update agrovet-specific information in status tab
                            updateAgrovetInformation();
                            // This tab should only be accessible after form submission
                        }
                    }

                    // Get current tab index
                    function getCurrentTabIndex() {
                        const activeTab = $('.tab-pane.show.active').attr('id');
                        switch (activeTab) {
                            case 'agrovet-selection':
                                return 0;
                            case 'input-selection':
                                return 1;
                            case 'credit-terms':
                                return 2;
                            case 'credit-summary':
                                return 3;
                            case 'credit-status':
                                return 4;
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

                    // Navigate to input credit status tab (after successful submission)
                    function goToStatusTab() {
                        showTab(tabs.status);
                        // Hide the status tab from regular navigation after showing it
                        $('[data-bs-target="#credit-status"]').parent().addClass('d-none');
                        // Update page title for input credit
                        updatePageTitle('Input Credit Application Status');
                    }

                    // Update agrovet-specific information in status tab
                    function updateAgrovetInformation() {
                        if (creditData.agrovet_name && creditData.agrovet_location) {
                            $('#partner-agrovet-name').text(creditData.agrovet_name);
                            $('#partner-agrovet-location').text(creditData.agrovet_location);
                        }
                    }

                    // Validation function to check if previous tabs are valid
                    function validatePreviousTabs(targetTab) {
                        switch (targetTab) {
                            case tabs.status:
                                // Status tab should only be accessible after submission
                                return false;
                            case tabs.summary:
                                if (!validateTermsTab()) return false;
                            case tabs.terms:
                                if (!validateInputsTab()) return false;
                            case tabs.inputs:
                                if (!validateAgrovetTab()) return false;
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
                            case tabs.agrovet:
                                return 0;
                            case tabs.inputs:
                                return 1;
                            case tabs.terms:
                                return 2;
                            case tabs.summary:
                                return 3;
                            case tabs.status:
                                return 4;
                            default:
                                return 0;
                        }
                    }

                    // Update page title
                    function updatePageTitle(newTitle) {
                        document.title = newTitle + ' - DFCS Input Credit Application';
                        $('.page-title').text(newTitle);
                    }

                    // Validation Rules for each tab
                    function validateAgrovetTab() {
                        if (!creditData.agrovet_id) {
                            toastr.error('Please select an agrovet');
                            return false;
                        }
                        return true;
                    }

                    function validateInputsTab() {
                        if (creditData.selected_inputs.length === 0) {
                            toastr.error('Please select at least one input item');
                            return false;
                        }
                        return true;
                    }

                    function validateTermsTab() {
                        if (!creditData.credit_percentage || creditData.credit_percentage < 5 || creditData
                            .credit_percentage > 15) {
                            toastr.error('Please enter a valid credit percentage (5-15%)');
                            return false;
                        }
                        if (!creditData.repayment_percentage || creditData.repayment_percentage < 10 ||
                            creditData.repayment_percentage > 50) {
                            toastr.error('Please enter a valid repayment percentage (10-50%)');
                            return false;
                        }
                        if (!creditData.purpose || creditData.purpose.trim() === '') {
                            toastr.error('Please provide the purpose for these inputs');
                            return false;
                        }
                        return true;
                    }

                    // Tab Navigation Button Event Handlers
                    $('#nextToInputs').click(function() {
                        if (validateAgrovetTab()) {
                            showTab(tabs.inputs);
                            // Select fertilizer category by default
                            setTimeout(function() {
                                if ($('#input-category-selector').length > 0) {
                                    $('#input-category-selector').val('fertilizer').trigger(
                                        'change');
                                }
                            }, 200);
                        }
                    });

                    $('#backToAgrovet').click(function() {
                        showTab(tabs.agrovet);
                    });

                    $('#nextToTerms').click(function() {
                        if (validateInputsTab()) {
                            showTab(tabs.terms);
                        }
                    });

                    $('#backToInputs').click(function() {
                        showTab(tabs.inputs);
                    });

                    $('#nextToSummary').click(function() {
                        if (validateTermsTab()) {
                            showTab(tabs.summary);
                        }
                    });

                    $('#backToTerms').click(function() {
                        showTab(tabs.terms);
                    });

                    // Input credit-specific helper functions
                    function extractAgrovetNameFromSelection() {
                        const selectedOption = $('#agrovet-select').find('option:selected');
                        const fullText = selectedOption.text();
                        // Extract agrovet name from "Agrovet Name (Location)" format
                        const parts = fullText.split('(');
                        if (parts.length >= 2) {
                            return parts[0].trim();
                        }
                        return fullText.trim();
                    }

                    function extractAgrovetLocationFromSelection() {
                        const selectedOption = $('#agrovet-select').find('option:selected');
                        const fullText = selectedOption.text();
                        // Extract location from "Agrovet Name (Location)" format
                        const match = fullText.match(/\((.*)\)/);
                        if (match && match[1]) {
                            return match[1].trim();
                        }
                        return '';
                    }

                    // Export functions to global scope for use in other parts
                    window.inputCreditApp = {
                        showTab: showTab,
                        goToStatusTab: goToStatusTab,
                        goToNextTab: goToNextTab,
                        goToPreviousTab: goToPreviousTab,
                        updateAgrovetInformation: updateAgrovetInformation,
                        updatePageTitle: updatePageTitle,
                        extractAgrovetNameFromSelection: extractAgrovetNameFromSelection,
                        extractAgrovetLocationFromSelection: extractAgrovetLocationFromSelection,
                        creditData: creditData,
                        validateAgrovetTab: validateAgrovetTab,
                        validateInputsTab: validateInputsTab,
                        validateTermsTab: validateTermsTab
                    };

                    // Helper function to format currency
                    function formatCurrency(amount) {
                        return amount.toLocaleString('en-KE', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    }

                    // Set default values
                    $('#credit-percentage').val(creditData.credit_percentage);
                    $('#repayment-percentage').val(creditData.repayment_percentage);

                    // Perform initial calculations
                    calculateCreditTerms();

                    // Continue to Part 2...
                    // PART 2: AGROVET SELECTION AND INPUT CATALOG LOADING

                    // Handle agrovet selection change
                    $('#agrovet-select').change(function() {
                        let selectedOption = $(this).find('option:selected');
                        let agrovetId = $(this).val();

                        if (!agrovetId) {
                            // Clear agrovet details if none selected
                            $('#agrovet-details').html(
                                '<p class="text-muted">Please select an agrovet to view details</p>'
                            );
                            creditData.agrovet_id = '';
                            creditData.agrovet_name = '';
                            creditData.agrovet_location = '';
                            return;
                        }

                        // Store agrovet information in creditData using helper functions
                        creditData.agrovet_id = agrovetId;
                        creditData.agrovet_name = window.inputCreditApp
                            .extractAgrovetNameFromSelection();
                        creditData.agrovet_location = window.inputCreditApp
                            .extractAgrovetLocationFromSelection();

                        // Update the selected agrovet display across tabs
                        $('#selected-agrovet-name').text(creditData.agrovet_name);
                        $('#summary-agrovet-name').text(creditData.agrovet_name + ' (' + creditData
                            .agrovet_location + ')');

                        // Update status tab agrovet information
                        $('#partner-agrovet-name').text(creditData.agrovet_name);
                        $('#partner-agrovet-location').text(creditData.agrovet_location);

                        // Load agrovet details
                        loadAgrovetDetails(agrovetId);

                        // Pre-load input catalog for this agrovet
                        loadInputCatalog(agrovetId);
                    });

                    // Function to load agrovet details with enhanced error handling
                    function loadAgrovetDetails(agrovetId) {
                        // Show loading state
                        $('#agrovet-details').html(`
            <div class="d-flex align-items-center">
                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                <span>Loading agrovet details...</span>
            </div>
        `);

                        $.ajax({
                            url: 'http://localhost/dfcs/ajax/input-credit-controller/get-agrovet-details.php',
                            type: 'GET',
                            data: {
                                agrovet_id: agrovetId
                            },
                            timeout: 10000, // 10 second timeout
                            success: function(response) {
                                try {
                                    let result = JSON.parse(response);
                                    if (result.success) {
                                        // Display agrovet details with enhanced layout
                                        let agrovet = result.data;
                                        let html = `
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-building text-primary me-2"></i>
                                        <strong>Name:</strong>
                                        <span class="ms-2">${agrovet.name}</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-geo-alt text-danger me-2"></i>
                                        <strong>Location:</strong>
                                        <span class="ms-2">${agrovet.location}</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-telephone text-success me-2"></i>
                                        <strong>Contact:</strong>
                                        <span class="ms-2">${agrovet.phone}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-envelope text-info me-2"></i>
                                        <strong>Email:</strong>
                                        <span class="ms-2">${agrovet.email || 'N/A'}</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-house text-warning me-2"></i>
                                        <strong>Address:</strong>
                                        <span class="ms-2">${agrovet.address || 'N/A'}</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-tags text-secondary me-2"></i>
                                        <strong>Type:</strong>
                                        <span class="ms-2">${agrovet.type_name || 'Standard Agrovet'}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="alert alert-success mb-0">
                                        <i class="bi bi-check-circle me-2"></i>
                                        <strong>Active Partner:</strong> This agrovet is an active partner in our network
                                    </div>
                                </div>
                            </div>
                        `;
                                        $('#agrovet-details').html(html);
                                    } else {
                                        $('#agrovet-details').html(`
                            <div class="alert alert-danger mb-0">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Error loading agrovet details: ${result.message}
                            </div>
                        `);
                                    }
                                } catch (e) {
                                    console.error('Error parsing agrovet details response:', e);
                                    $('#agrovet-details').html(`
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Error processing agrovet information
                        </div>
                    `);
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Agrovet details AJAX error:', {
                                    xhr,
                                    status,
                                    error
                                });
                                let errorMessage = 'Error loading agrovet details';

                                if (status === 'timeout') {
                                    errorMessage = 'Request timed out. Please try again.';
                                } else if (xhr.status === 404) {
                                    errorMessage = 'Agrovet information service not available';
                                } else if (xhr.status >= 500) {
                                    errorMessage = 'Server error. Please try again later.';
                                }

                                $('#agrovet-details').html(`
                    <div class="alert alert-danger mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        ${errorMessage}
                    </div>
                `);
                            }
                        });
                    }

                    // Function to load input catalog by category for the selected agrovet
                    function loadInputCatalog(agrovetId) {
                        // Clear previous content
                        $('#fertilizer-items, #seed-items, #pesticide-items, #tool-items, #other-items')
                            .empty();

                        // Show loading indicator with better UX
                        $('#category-items').html(`
            <tr>
                <td colspan="7" class="text-center py-4">
                    <div class="d-flex flex-column align-items-center">
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <h6 class="text-muted">Loading available inputs...</h6>
                        <small class="text-muted">This may take a moment</small>
                    </div>
                </td>
            </tr>
        `);

                        // Destroy DataTable if it exists
                        if ($.fn.DataTable.isDataTable('#catalog-table')) {
                            $('#catalog-table').DataTable().destroy();
                        }

                        // Make AJAX request to get the catalog items
                        $.ajax({
                            url: 'http://localhost/dfcs/ajax/input-credit-controller/get-input-catalog.php',
                            type: 'GET',
                            data: {
                                agrovet_id: agrovetId
                            },
                            timeout: 15000, // 15 second timeout for catalog loading
                            success: function(response) {
                                try {
                                    let result = JSON.parse(response);
                                    if (result.success) {
                                        // Clear containers
                                        $('#fertilizer-items, #seed-items, #pesticide-items, #tool-items, #other-items')
                                            .empty();

                                        // Group items by category with enhanced filtering
                                        let items = result.data;
                                        let fertilizers = items.filter(item => item.type ===
                                            'fertilizer');
                                        let seeds = items.filter(item => item.type === 'seeds');
                                        let pesticides = items.filter(item => item.type ===
                                            'pesticide');
                                        let tools = items.filter(item => item.type === 'tools');
                                        let others = items.filter(item => item.type === 'other');

                                        console.log("Input Catalog Loading Results:", {
                                            total: items.length,
                                            fertilizers: fertilizers.length,
                                            seeds: seeds.length,
                                            pesticides: pesticides.length,
                                            tools: tools.length,
                                            others: others.length,
                                            agrovet: creditData.agrovet_name
                                        });

                                        // Populate each category container
                                        populateCategoryItems('fertilizer-items', fertilizers);
                                        populateCategoryItems('seed-items', seeds);
                                        populateCategoryItems('pesticide-items', pesticides);
                                        populateCategoryItems('tool-items', tools);
                                        populateCategoryItems('other-items', others);

                                        // Show the current selected category
                                        const currentCategory = $('#input-category-selector')
                                            .val() || 'fertilizer';
                                        showCategoryItems(currentCategory);

                                        // Show success notification
                                        toastr.success(
                                            `Loaded ${items.length} input items from ${creditData.agrovet_name}`,
                                            'Catalog Loaded');
                                    } else {
                                        const errorHtml = `
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="alert alert-warning mb-0">
                                        <i class="bi bi-exclamation-triangle fs-3 mb-2"></i>
                                        <h6>No Items Available</h6>
                                        <p class="mb-0">${result.message}</p>
                                    </div>
                                </td>
                            </tr>
                        `;
                                        $('#category-items').html(errorHtml);
                                        toastr.warning(
                                            'No input items available from this agrovet');
                                    }
                                } catch (e) {
                                    console.error("Error processing input catalog response:", e);
                                    const errorHtml = `
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="alert alert-danger mb-0">
                                    <i class="bi bi-exclamation-triangle fs-3 mb-2"></i>
                                    <h6>Processing Error</h6>
                                    <p class="mb-0">Error processing catalog data: ${e.message}</p>
                                </div>
                            </td>
                        </tr>
                    `;
                                    $('#category-items').html(errorHtml);
                                    toastr.error('Error processing input catalog data');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error("Input catalog AJAX error:", {
                                    xhr,
                                    status,
                                    error
                                });

                                let errorMessage = 'Error loading input catalog';
                                let errorDetails = '';

                                if (status === 'timeout') {
                                    errorMessage = 'Request Timed Out';
                                    errorDetails = 'The request took too long. Please try again.';
                                } else if (xhr.status === 404) {
                                    errorMessage = 'Service Not Found';
                                    errorDetails = 'Input catalog service is not available.';
                                } else if (xhr.status === 500) {
                                    errorMessage = 'Server Error';
                                    errorDetails = 'Please try again later or contact support.';
                                } else if (xhr.status === 0) {
                                    errorMessage = 'Connection Error';
                                    errorDetails = 'Please check your internet connection.';
                                }

                                const errorHtml = `
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="alert alert-danger mb-0">
                                <i class="bi bi-exclamation-triangle fs-3 mb-2"></i>
                                <h6>${errorMessage}</h6>
                                <p class="mb-0">${errorDetails}</p>
                                <button class="btn btn-outline-danger btn-sm mt-2" onclick="loadInputCatalog(${agrovetId})">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Retry
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                                $('#category-items').html(errorHtml);
                                toastr.error(`Failed to load input catalog: ${errorMessage}`);
                            }
                        });
                    }

                    // Enhanced function to populate a category container with items
                    function populateCategoryItems(containerId, items) {
                        console.log(`Populating ${containerId} with ${items.length} items`);

                        // Get the container
                        const $container = $(`#${containerId}`);

                        // Clear any existing content
                        $container.empty();

                        // If no items, don't add anything (empty container is fine)
                        if (items.length === 0) {
                            console.log(`No items to populate in ${containerId}`);
                            return;
                        }

                        // Add each item row with enhanced UI
                        items.forEach(item => {
                            const row = $(`
                <tr>
                    <td>
                        <div class="fw-semibold">${item.name}</div>
                        <small class="text-muted">${item.type.charAt(0).toUpperCase() + item.type.slice(1)}</small>
                    </td>
                    <td>
                        <small>${item.description || 'No description available'}</small>
                    </td>
                    <td>
                        <span class="badge bg-secondary">${item.standard_unit}</span>
                    </td>
                    <td>
                        <strong>KES ${formatCurrency(parseFloat(item.standard_price))}</strong>
                    </td>
                    <td>
                        <div class="input-group input-group-sm" style="width: 120px;">
                            <input type="number" class="form-control input-quantity" 
                                   data-id="${item.id}" data-name="${item.name}" 
                                   data-type="${item.type}" data-unit="${item.standard_unit}"
                                   data-price="${item.standard_price}" 
                                   min="1" max="999" value="1">
                        </div>
                    </td>
                    <td>
                        <strong>KES ${formatCurrency(parseFloat(item.standard_price))}</strong>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-success add-input-btn" 
                                data-id="${item.id}" data-name="${item.name}" 
                                data-type="${item.type}" data-unit="${item.standard_unit}"
                                data-price="${item.standard_price}">
                            <i class="bi bi-plus-circle"></i>
                        </button>
                    </td>
                </tr>
            `);

                            // Add to container
                            $container.append(row);

                            // Add event handler for quantity input with debouncing
                            let quantityTimeout;
                            row.find('.input-quantity').on('input', function() {
                                clearTimeout(quantityTimeout);
                                quantityTimeout = setTimeout(() => {
                                    let price = parseFloat($(this).data('price'));
                                    let quantity = parseInt($(this).val()) || 0;
                                    let total = price * quantity;
                                    $(this).closest('tr').find('td:nth-child(6) strong')
                                        .text('KES ' + formatCurrency(total));
                                }, 300);
                            });
                        });

                        console.log(`Successfully populated ${items.length} items in #${containerId}`);
                    }

                    // Handle input category selection with enhanced UX
                    $(document).on('change', '#input-category-selector', function() {
                        const category = $(this).val();
                        const categoryNames = {
                            'fertilizer': 'Fertilizers',
                            'seeds': 'Seeds',
                            'pesticide': 'Pesticides',
                            'tools': 'Tools & Equipment',
                            'other': 'Other Inputs'
                        };

                        console.log(`Switching to category: ${categoryNames[category]}`);
                        showCategoryItems(category);

                        // Update UI to show current category
                        $('.input-category-title').text(categoryNames[category] || 'Input Items');
                    });

                    // Enhanced show items for the selected category
                    function showCategoryItems(category) {
                        console.log(`Showing items for category: ${category}`);

                        // Destroy existing DataTable if it exists
                        if ($.fn.DataTable.isDataTable('#catalog-table')) {
                            $('#catalog-table').DataTable().destroy();
                        }

                        // Clear current items
                        $('#category-items').empty();

                        // Map category to the appropriate item container ID
                        const containerMap = {
                            'fertilizer': 'fertilizer-items',
                            'seeds': 'seed-items',
                            'pesticide': 'pesticide-items',
                            'tools': 'tool-items',
                            'other': 'other-items'
                        };

                        const containerId = containerMap[category];
                        const $container = $(`#${containerId}`);

                        if ($container.children().length === 0) {
                            $('#category-items').html(`
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <div class="text-muted">
                            <i class="bi bi-inbox fs-3 mb-2"></i>
                            <h6>No Items Available</h6>
                            <p class="mb-0">No ${category} items available from this agrovet</p>
                        </div>
                    </td>
                </tr>
            `);
                        } else {
                            // Clone the content from the hidden container to the visible one
                            $container.children().clone().appendTo('#category-items');

                            // Re-attach event handlers for quantity inputs and add buttons
                            $('#category-items .input-quantity').on('input', function() {
                                let price = parseFloat($(this).data('price'));
                                let quantity = parseInt($(this).val()) || 0;
                                let total = price * quantity;
                                $(this).closest('tr').find('td:nth-child(6) strong').text('KES ' +
                                    formatCurrency(total));
                            });

                            // Initialize DataTable for the catalog with enhanced options
                            if ($('#category-items tr').length > 1) {
                                $('#catalog-table').DataTable({
                                    pageLength: 10,
                                    lengthMenu: [
                                        [5, 10, 25, -1],
                                        [5, 10, 25, "All"]
                                    ],
                                    autoWidth: false,
                                    responsive: true,
                                    language: {
                                        search: "Search items:",
                                        lengthMenu: "Show _MENU_ items per page",
                                        info: "Showing _START_ to _END_ of _TOTAL_ items",
                                        emptyTable: "No items available in this category"
                                    },
                                    columnDefs: [{
                                            targets: [4, 6],
                                            orderable: false
                                        }, // Disable sorting for quantity and action columns
                                        {
                                            targets: [2, 3, 5],
                                            className: "text-center"
                                        } // Center align specific columns
                                    ]
                                });
                            }
                        }

                        console.log(`Displayed items for category ${category}`);
                    }

                    // Continue to Part 3...
                    // PART 3: INPUT SELECTION AND SHOPPING CART FUNCTIONALITY

                    // Handle adding an input item to the selection with enhanced validation
                    $(document).on('click', '.add-input-btn', function() {
                        const btn = $(this);
                        const id = btn.data('id');
                        const name = btn.data('name');
                        const type = btn.data('type');
                        const unit = btn.data('unit');
                        const price = parseFloat(btn.data('price'));
                        const quantity = parseInt(btn.closest('tr').find('.input-quantity').val()) || 0;

                        // Validation checks
                        if (quantity <= 0) {
                            toastr.error('Please enter a valid quantity');
                            btn.closest('tr').find('.input-quantity').focus();
                            return;
                        }

                        if (quantity > 999) {
                            toastr.error('Maximum quantity is 999 per item');
                            btn.closest('tr').find('.input-quantity').val(999);
                            return;
                        }

                        // Check if item already exists in selection
                        const existingItemIndex = creditData.selected_inputs.findIndex(item => item
                            .id === id);

                        if (existingItemIndex !== -1) {
                            // Update existing item - replace quantity (not add to it)
                            const oldQuantity = creditData.selected_inputs[existingItemIndex].quantity;
                            creditData.selected_inputs[existingItemIndex].quantity = quantity;
                            creditData.selected_inputs[existingItemIndex].total = quantity * price;

                            toastr.info(`Updated ${name}: ${oldQuantity} → ${quantity} ${unit}`,
                                'Item Updated');
                        } else {
                            // Add new item with exact quantity specified
                            creditData.selected_inputs.push({
                                id: id,
                                name: name,
                                type: type,
                                unit: unit,
                                price: price,
                                quantity: quantity,
                                total: price * quantity,
                                added_at: new Date().toISOString() // Track when item was added
                            });

                            toastr.success(`Added ${quantity} ${unit} of ${name}`, 'Item Added');
                        }

                        // Reset quantity input to 1 and update total display
                        btn.closest('tr').find('.input-quantity').val(1);
                        btn.closest('tr').find('td:nth-child(6) strong').text('KES ' + formatCurrency(
                            price));

                        // Add visual feedback to the button
                        btn.removeClass('btn-success').addClass('btn-secondary');
                        btn.html('<i class="bi bi-check-circle"></i>');

                        // Reset button after 1 second
                        setTimeout(() => {
                            btn.removeClass('btn-secondary').addClass('btn-success');
                            btn.html('<i class="bi bi-plus-circle"></i>');
                        }, 1000);

                        // Update selected items display
                        updateSelectedItems();

                        // Update status tab summary
                        updateStatusTabSummary();
                    });

                    // Enhanced function to update the display of selected items
                    function updateSelectedItems() {
                        const container = $('#selected-items');
                        const summaryContainer = $('#summary-selected-items');

                        // Destroy DataTable if it exists
                        if ($.fn.DataTable.isDataTable('#selected-items-table')) {
                            $('#selected-items-table').DataTable().destroy();
                        }

                        // Clear containers
                        container.empty();
                        summaryContainer.empty();

                        if (creditData.selected_inputs.length === 0) {
                            // Show empty state with better UX
                            container.html(`
                <tr id="no-items-row">
                    <td colspan="6" class="text-center py-4">
                        <div class="text-muted">
                            <i class="bi bi-cart fs-3 mb-2"></i>
                            <h6>No inputs selected yet</h6>
                            <p class="mb-0">Select items from the catalog above to add them here</p>
                        </div>
                    </td>
                </tr>
            `);

                            summaryContainer.html(`
                <tr>
                    <td colspan="5" class="text-center py-3">
                        <div class="text-muted">
                            <i class="bi bi-inbox"></i>
                            <span class="ms-2">No inputs selected</span>
                        </div>
                    </td>
                </tr>
            `);

                            // Reset total amount and update displays
                            resetTotalAmounts();
                            return;
                        }

                        // Calculate total amount and sort items by type and name
                        let totalAmount = 0;
                        const sortedItems = [...creditData.selected_inputs].sort((a, b) => {
                            if (a.type !== b.type) {
                                return a.type.localeCompare(b.type);
                            }
                            return a.name.localeCompare(b.name);
                        });

                        // Add each selected item to the display with enhanced UI
                        sortedItems.forEach((item, index) => {
                            totalAmount += item.total;
                            const originalIndex = creditData.selected_inputs.findIndex(original =>
                                original.id === item.id);

                            // Create row for selected items table with better styling
                            let row = `
                <tr class="selected-item-row" data-item-id="${item.id}">
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="item-type-badge me-2">
                                <i class="bi ${getItemTypeIcon(item.type)} text-${getItemTypeColor(item.type)}"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">${item.name}</div>
                                <small class="text-muted">${formatItemType(item.type)}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-${getItemTypeColor(item.type)}">${formatItemType(item.type)}</span>
                    </td>
                    <td class="text-center">
                        <div class="quantity-display">
                            <span class="fw-bold">${item.quantity}</span>
                            <small class="text-muted d-block">${item.unit}</small>
                        </div>
                    </td>
                    <td class="text-end">
                        <strong>KES ${formatCurrency(item.price)}</strong>
                    </td>
                    <td class="text-end">
                        <strong class="text-success">KES ${formatCurrency(item.total)}</strong>
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary edit-quantity-btn" 
                                    data-index="${originalIndex}" data-current="${item.quantity}"
                                    title="Edit Quantity">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-outline-danger remove-input-btn" 
                                    data-index="${originalIndex}"
                                    title="Remove Item">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
                            container.append(row);

                            // Create row for summary table (without action buttons)
                            let summaryRow = `
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="bi ${getItemTypeIcon(item.type)} text-${getItemTypeColor(item.type)} me-2"></i>
                            <span>${item.name}</span>
                        </div>
                    </td>
                    <td><span class="badge bg-${getItemTypeColor(item.type)}">${formatItemType(item.type)}</span></td>
                    <td class="text-center">${item.quantity} ${item.unit}</td>
                    <td class="text-end">KES ${formatCurrency(item.price)}</td>
                    <td class="text-end"><strong class="text-success">KES ${formatCurrency(item.total)}</strong></td>
                </tr>
            `;
                            summaryContainer.append(summaryRow);
                        });

                        // Update total amount
                        creditData.total_amount = totalAmount;
                        updateAmountDisplays(totalAmount);

                        // Initialize DataTable for selected items with enhanced options
                        if (creditData.selected_inputs.length > 0) {
                            $('#selected-items-table').DataTable({
                                paging: creditData.selected_inputs.length > 10,
                                pageLength: 10,
                                searching: false,
                                info: creditData.selected_inputs.length > 10,
                                autoWidth: false,
                                ordering: true,
                                order: [
                                    [1, 'asc'],
                                    [0, 'asc']
                                ], // Sort by type, then name
                                columnDefs: [{
                                        targets: [5],
                                        orderable: false
                                    }, // Disable sorting for action column
                                    {
                                        targets: [2, 3, 4, 5],
                                        className: "text-center"
                                    }
                                ],
                                language: {
                                    emptyTable: "No inputs selected",
                                    info: "Showing _START_ to _END_ of _TOTAL_ selected items"
                                }
                            });
                        }

                        // Recalculate credit terms
                        calculateCreditTerms();

                        // Update item count badge
                        updateItemCountBadge();

                        console.log(
                            `Updated selected items display: ${creditData.selected_inputs.length} items, Total: KES ${formatCurrency(totalAmount)}`
                        );
                    }

                    // Handle editing item quantity inline
                    $(document).on('click', '.edit-quantity-btn', function() {
                        const btn = $(this);
                        const index = btn.data('index');
                        const currentQuantity = btn.data('current');
                        const item = creditData.selected_inputs[index];

                        // Create inline edit input
                        const quantityCell = btn.closest('tr').find('.quantity-display');
                        const originalContent = quantityCell.html();

                        quantityCell.html(`
            <div class="input-group input-group-sm">
                <input type="number" class="form-control quantity-edit-input" 
                       value="${currentQuantity}" min="1" max="999" 
                       style="width: 80px;">
                <button class="btn btn-success btn-sm save-quantity-btn" type="button">
                    <i class="bi bi-check"></i>
                </button>
                <button class="btn btn-secondary btn-sm cancel-edit-btn" type="button">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        `);

                        // Focus and select the input
                        quantityCell.find('.quantity-edit-input').focus().select();

                        // Handle save
                        quantityCell.find('.save-quantity-btn').on('click', function() {
                            const newQuantity = parseInt(quantityCell.find(
                                '.quantity-edit-input').val()) || 0;

                            if (newQuantity <= 0) {
                                toastr.error('Quantity must be greater than 0');
                                quantityCell.find('.quantity-edit-input').focus();
                                return;
                            }

                            if (newQuantity > 999) {
                                toastr.error('Maximum quantity is 999');
                                quantityCell.find('.quantity-edit-input').val(999).focus();
                                return;
                            }

                            // Update the item
                            creditData.selected_inputs[index].quantity = newQuantity;
                            creditData.selected_inputs[index].total = newQuantity * item.price;

                            // Refresh the display
                            updateSelectedItems();
                            toastr.success(`Updated ${item.name} quantity to ${newQuantity}`,
                                'Quantity Updated');
                        });

                        // Handle cancel
                        quantityCell.find('.cancel-edit-btn').on('click', function() {
                            quantityCell.html(originalContent);
                        });

                        // Handle Enter key
                        quantityCell.find('.quantity-edit-input').on('keypress', function(e) {
                            if (e.which === 13) { // Enter key
                                quantityCell.find('.save-quantity-btn').click();
                            }
                        });

                        // Handle Escape key
                        quantityCell.find('.quantity-edit-input').on('keyup', function(e) {
                            if (e.which === 27) { // Escape key
                                quantityCell.find('.cancel-edit-btn').click();
                            }
                        });
                    });

                    // Handle removing an item from the selection with confirmation
                    $(document).on('click', '.remove-input-btn', function() {
                        const index = $(this).data('index');
                        const item = creditData.selected_inputs[index];

                        // Show confirmation for expensive items
                        if (item.total > 10000) {
                            if (!confirm(
                                    `Are you sure you want to remove ${item.name}? This will remove KES ${formatCurrency(item.total)} from your order.`
                                )) {
                                return;
                            }
                        }

                        // Remove the item from the array
                        creditData.selected_inputs.splice(index, 1);

                        // Update the display
                        updateSelectedItems();

                        // Show confirmation
                        toastr.info(`Removed ${item.name} from your selection`, 'Item Removed');

                        // Update status tab
                        updateStatusTabSummary();
                    });

                    // Helper functions for item display
                    function getItemTypeIcon(type) {
                        const icons = {
                            'fertilizer': 'bi-flower1',
                            'seeds': 'bi-seed',
                            'pesticide': 'bi-bug',
                            'tools': 'bi-tools',
                            'other': 'bi-box'
                        };
                        return icons[type] || 'bi-box';
                    }

                    function getItemTypeColor(type) {
                        const colors = {
                            'fertilizer': 'success',
                            'seeds': 'warning',
                            'pesticide': 'danger',
                            'tools': 'info',
                            'other': 'secondary'
                        };
                        return colors[type] || 'secondary';
                    }

                    function formatItemType(type) {
                        const types = {
                            'fertilizer': 'Fertilizer',
                            'seeds': 'Seeds',
                            'pesticide': 'Pesticide',
                            'tools': 'Tools & Equipment',
                            'other': 'Other Input'
                        };
                        return types[type] || type.charAt(0).toUpperCase() + type.slice(1);
                    }

                    // Reset total amounts when no items selected
                    function resetTotalAmounts() {
                        creditData.total_amount = 0;
                        updateAmountDisplays(0);
                        calculateCreditTerms();
                    }

                    // Update all amount displays across tabs
                    function updateAmountDisplays(totalAmount) {
                        const formattedAmount = formatCurrency(totalAmount);

                        $('#total-input-amount').text('KES ' + formattedAmount);
                        $('#credit-total-amount').text(formattedAmount);
                        $('#calc-input-value').text(formattedAmount);
                        $('#summary-input-value').text(formattedAmount);
                        $('#status-input-value').text(formattedAmount);
                    }

                    // Update item count badge for better UX
                    function updateItemCountBadge() {
                        const count = creditData.selected_inputs.length;
                        let badge = $('.selected-items-badge');

                        if (badge.length === 0) {
                            // Create badge if it doesn't exist
                            badge = $('<span class="badge bg-primary selected-items-badge ms-2"></span>');
                            $('#nextToTerms').append(badge);
                        }

                        if (count > 0) {
                            badge.text(count).show();
                        } else {
                            badge.hide();
                        }
                    }

                    // Handle quantity updates in catalog with debouncing
                    let quantityUpdateTimeout;
                    $(document).on('input', '.input-quantity', function() {
                        clearTimeout(quantityUpdateTimeout);

                        const $input = $(this);
                        const price = parseFloat($input.data('price'));
                        const quantity = parseInt($input.val()) || 0;

                        // Validate quantity range
                        if (quantity > 999) {
                            $input.val(999);
                            toastr.warning('Maximum quantity is 999');
                            return;
                        }

                        quantityUpdateTimeout = setTimeout(() => {
                            const total = price * quantity;
                            $input.closest('tr').find('td:nth-child(6) strong').text('KES ' +
                                formatCurrency(total));
                        }, 300);
                    });

                    // Handle credit percentage change
                    $('#credit-percentage').on('input', function() {
                        const value = parseFloat($(this).val()) || 0;

                        // Validate range
                        if (value < 5) {
                            $(this).val(5);
                            creditData.credit_percentage = 5;
                            toastr.warning('Minimum credit percentage is 5%');
                        } else if (value > 15) {
                            $(this).val(15);
                            creditData.credit_percentage = 15;
                            toastr.warning('Maximum credit percentage is 15%');
                        } else {
                            creditData.credit_percentage = value;
                        }

                        calculateCreditTerms();
                    });

                    // Handle repayment percentage change
                    $('#repayment-percentage').on('input', function() {
                        const value = parseFloat($(this).val()) || 0;

                        // Validate range
                        if (value < 10) {
                            $(this).val(10);
                            creditData.repayment_percentage = 10;
                            toastr.warning('Minimum repayment percentage is 10%');
                        } else if (value > 50) {
                            $(this).val(50);
                            creditData.repayment_percentage = 50;
                            toastr.warning('Maximum repayment percentage is 50%');
                        } else {
                            creditData.repayment_percentage = value;
                        }

                        calculateCreditTerms();
                    });

                    // Handle purpose input with character counter
                    $('#credit-purpose').on('input', function() {
                        creditData.purpose = $(this).val();

                        // Add character counter
                        const currentLength = $(this).val().length;
                        const maxLength = 500;
                        let counter = $(this).siblings('.char-counter');

                        if (counter.length === 0) {
                            counter = $('<small class="char-counter text-muted"></small>');
                            $(this).after(counter);
                        }

                        counter.text(`${currentLength}/500 characters`);

                        if (currentLength > maxLength) {
                            counter.removeClass('text-muted').addClass('text-danger');
                            $(this).val($(this).val().substring(0, maxLength));
                        } else {
                            counter.removeClass('text-danger').addClass('text-muted');
                        }
                    });

                    // Enhanced calculate credit terms function
                    function calculateCreditTerms() {
                        // If no inputs selected, reset calculations
                        if (creditData.selected_inputs.length === 0 || creditData.total_amount <= 0) {
                            $('#calc-interest-amount').text('0.00');
                            $('#calc-total-repayment').text('0.00');
                            $('#summary-total-repayment').text('0.00');
                            $('#status-total-repayment').text('0.00');
                            $('#status-interest-rate').text('0');
                            return;
                        }

                        // Calculate interest amount
                        const interestAmount = creditData.total_amount * (creditData.credit_percentage / 100);

                        // Calculate total repayment amount
                        creditData.total_with_interest = creditData.total_amount + interestAmount;

                        // Update all displays
                        $('#calc-interest-amount').text(formatCurrency(interestAmount));
                        $('#calc-total-repayment').text(formatCurrency(creditData.total_with_interest));
                        $('#summary-total-repayment').text(formatCurrency(creditData.total_with_interest));
                        $('#status-total-repayment').text(formatCurrency(creditData.total_with_interest));
                        $('#status-interest-rate').text(creditData.credit_percentage);

                        // Update summary values
                        $('#summary-credit-percentage').text(creditData.credit_percentage);
                        $('#summary-repayment-percentage').text(creditData.repayment_percentage);
                    }

                    // Update status tab summary when items change
                    function updateStatusTabSummary() {
                        updateAmountDisplays(creditData.total_amount);
                        calculateCreditTerms();

                        // Update agrovet information
                        if (creditData.agrovet_name) {
                            $('#partner-agrovet-name').text(creditData.agrovet_name);
                            $('#partner-agrovet-location').text(creditData.agrovet_location);
                        }
                    }

                    // Continue to Part 4...
                    // PART 4: FORM SUBMISSION AND STATUS TAB UPDATES

                    // Enhanced form submission handler
                    $('#submitCreditApplication').click(function(e) {
                        e.preventDefault();

                        // Show loading state
                        showSubmissionLoading(true);

                        // Final validation before submission
                        if (!validateSummaryTab()) {
                            showSubmissionLoading(false);
                            return;
                        }

                        // Prepare and submit input credit application
                        submitInputCreditApplication();
                    });

                    // Comprehensive summary tab validation
                    function validateSummaryTab() {
                        let isValid = true;
                        const errors = [];

                        // Re-validate all previous tabs
                        if (!window.inputCreditApp.validateAgrovetTab()) {
                            errors.push('Please fix errors in the Agrovet Selection section');
                            isValid = false;
                        }

                        if (!window.inputCreditApp.validateInputsTab()) {
                            errors.push('Please fix errors in the Input Selection section');
                            isValid = false;
                        }

                        if (!window.inputCreditApp.validateTermsTab()) {
                            errors.push('Please fix errors in the Credit Terms section');
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

                        // Final validation checks
                        const finalValidation = performFinalValidation();
                        if (!finalValidation.isValid) {
                            errors.push(...finalValidation.errors);
                            isValid = false;
                        }

                        // Show validation summary if there are errors
                        if (!isValid) {
                            showValidationErrors(errors);
                        }

                        return isValid;
                    }

                    // Perform final validation before submission
                    function performFinalValidation() {
                        const errors = [];
                        let isValid = true;

                        // Check if all required data is present
                        if (!creditData.agrovet_id || !creditData.agrovet_name) {
                            errors.push('Agrovet selection is incomplete');
                            isValid = false;
                        }

                        if (creditData.selected_inputs.length === 0) {
                            errors.push('No input items selected');
                            isValid = false;
                        }

                        if (!creditData.total_amount || creditData.total_amount <= 0) {
                            errors.push('Total amount calculation is incomplete');
                            isValid = false;
                        }

                        if (!creditData.purpose || creditData.purpose.trim() === '') {
                            errors.push('Purpose description is required');
                            isValid = false;
                        }

                        // Check for minimum application amount
                        if (creditData.total_amount < 1000) {
                            errors.push('Minimum application amount is KES 1,000');
                            isValid = false;
                        }

                        return {
                            isValid,
                            errors
                        };
                    }

                    // Main input credit submission function
                    function submitInputCreditApplication() {
                        // Prepare form data with comprehensive information
                        let formData = new FormData();

                        // Add basic application data
                        formData.append('farmer_id', $('input[name="farmer_id"]').val());
                        formData.append('agrovet_id', creditData.agrovet_id);
                        formData.append('total_amount', creditData.total_amount);
                        formData.append('credit_percentage', creditData.credit_percentage);
                        formData.append('total_with_interest', creditData.total_with_interest);
                        formData.append('repayment_percentage', creditData.repayment_percentage);
                        formData.append('purpose', creditData.purpose);
                        formData.append('reference_number', creditData.reference_number);

                        // Add selected inputs as JSON string with enhanced data
                        const enrichedInputs = creditData.selected_inputs.map(item => ({
                            ...item,
                            category: item.type,
                            description: `${item.quantity} ${item.unit} of ${item.name}`,
                            unit_total: item.total
                        }));
                        formData.append('selected_inputs', JSON.stringify(enrichedInputs));

                        // Add application metadata
                        formData.append('application_source', 'web_application');
                        formData.append('items_count', creditData.selected_inputs.length);
                        formData.append('application_timestamp', new Date().toISOString());

                        // Submit AJAX request with enhanced error handling
                        $.ajax({
                            url: 'http://localhost/dfcs/ajax/input-credit-controller/submit-application.php',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            timeout: 45000, // 45 second timeout
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

                    // Handle successful input credit submission
                    function handleSubmissionSuccess(response) {
                        try {
                            let result = JSON.parse(response);

                            if (result.success) {
                                // Show success message
                                toastr.success('Input credit application submitted successfully!',
                                    'Application Submitted');

                                // Update status tab with results
                                updateInputCreditStatusTab(result);

                                // Navigate to status tab (KEY CHANGE - no redirect!)
                                window.inputCreditApp.goToStatusTab();

                                // Hide the submit button and show completion status
                                $('#submitCreditApplication').prop('disabled', true).html(
                                    '<i class="bi bi-check-circle me-2"></i>Application Submitted'
                                );

                                // Update page title
                                window.inputCreditApp.updatePageTitle('Input Credit Application Status');

                                // Show success notification with details
                                showSuccessNotification(result);

                            } else {
                                toastr.error(result.message || 'Error submitting input credit application',
                                    'Submission Error');
                                highlightSubmissionErrors(result);
                            }
                        } catch (e) {
                            console.error('Error parsing input credit response:', e);
                            toastr.error('Error processing server response', 'System Error');
                        }
                    }

                    // Handle input credit submission errors
                    function handleSubmissionError(xhr, status, error) {
                        console.error('Input credit submission error:', {
                            xhr,
                            status,
                            error
                        });

                        let errorMessage = 'Error submitting input credit application';

                        if (status === 'timeout') {
                            errorMessage = 'Request timeout. Please try again or contact support.';
                        } else if (xhr.status === 400) {
                            errorMessage = 'Invalid application data. Please review and try again.';
                        } else if (xhr.status === 401) {
                            errorMessage = 'Session expired. Please log in again.';
                            setTimeout(() => {
                                window.location.href = '/login';
                            }, 3000);
                        } else if (xhr.status === 422) {
                            errorMessage = 'Application validation failed. Please check your information.';
                        } else if (xhr.status >= 500) {
                            errorMessage = 'System error. Please try again later or contact support.';
                        }

                        toastr.error(errorMessage, 'Input Credit Error');
                    }

                    // Show/hide submission loading state
                    function showSubmissionLoading(show) {
                        const submitBtn = $('#submitCreditApplication');

                        if (show) {
                            submitBtn.prop('disabled', true).html(
                                '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Processing Application...'
                            );
                        } else {
                            submitBtn.prop('disabled', false).html(
                                '<i class="bi bi-check-circle me-2"></i>Submit Application'
                            );
                        }
                    }

                    // Update input credit status tab with application results (KEY FUNCTION)
                    function updateInputCreditStatusTab(result) {
                        if (!result.success || !result.assessment) return;

                        const assessment = result.assessment;
                        const score = assessment.score;
                        const details = assessment.details;
                        const status = result.status;

                        // Update reference number
                        $('#final-reference-number').text(result.reference_number || creditData
                            .reference_number);

                        // Update main creditworthiness score display
                        updateMainCreditScore(score);

                        // Update decision card based on status
                        updateDecisionCard(status, score, assessment.status_description);

                        // Update agrovet information section
                        updateAgrovetInformationDisplay();

                        // Update individual score breakdowns
                        updateScoreBreakdowns(details);

                        // Update credit summary information
                        updateCreditSummaryDisplay();

                        // Update next steps and recommendations
                        updateRecommendations(status, score, details);
                    }

                    // Update main creditworthiness score display
                    function updateMainCreditScore(score) {
                        $('#credit-score-value').text(score);
                        $('#credit-score-text').text(score + '%');

                        // Animate progress bar
                        const progressBar = $('#credit-score-progress');
                        progressBar.css('width', '0%');

                        setTimeout(() => {
                            progressBar.css('width', score + '%');
                            progressBar.addClass('transition-all');
                        }, 500);

                        // Set colors based on score
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
                        const icon = $('#status-icon');
                        const title = $('#status-title');
                        const desc = $('#status-description');
                        const badge = $('#application-status-badge');
                        const card = $('#credit-decision-card');

                        // Reset classes
                        icon.removeClass().addClass('fs-2 me-3');
                        badge.removeClass().addClass('badge fs-6 p-3');
                        card.removeClass().addClass('card border-0 shadow-sm');

                        switch (status) {
                            case 'under_review':
                                icon.addClass('bi bi-hourglass-half text-success');
                                title.text('Input Credit Under Review');
                                desc.text(description ||
                                    `Your application is being reviewed by ${creditData.agrovet_name}. You will be contacted within 24-48 hours.`
                                );
                                badge.addClass('bg-success').html(
                                    '<i class="bi bi-clock-history me-1"></i>Under Review');
                                card.addClass('border-success');
                                break;

                            case 'pending':
                                icon.addClass('bi bi-exclamation-triangle text-warning');
                                title.text('Additional Review Required');
                                desc.text(description ||
                                    `Your application requires additional assessment. Our team will coordinate with ${creditData.agrovet_name}.`
                                );
                                badge.addClass('bg-warning text-dark').html(
                                    '<i class="bi bi-exclamation-triangle me-1"></i>Pending Review');
                                card.addClass('border-warning');
                                break;

                            case 'rejected':
                                icon.addClass('bi bi-x-circle-fill text-danger');
                                title.text('Application Declined');
                                desc.text(description ||
                                    'Your application did not meet the current credit criteria.');
                                badge.addClass('bg-danger').html('<i class="bi bi-x-circle me-1"></i>Declined');
                                card.addClass('border-danger');
                                break;

                            default:
                                icon.addClass('bi bi-check-circle text-info');
                                title.text('Application Processing');
                                desc.text(
                                    'Your input credit application has been submitted and is being processed.'
                                );
                                badge.addClass('bg-info').html('<i class="bi bi-clock me-1"></i>Processing');
                                card.addClass('border-info');
                        }
                    }

                    // Update agrovet information display in status tab
                    function updateAgrovetInformationDisplay() {
                        $('#partner-agrovet-name').text(creditData.agrovet_name || 'Selected Agrovet');
                        $('#partner-agrovet-location').text(creditData.agrovet_location || 'Location');
                    }

                    // Update individual score breakdown bars
                    function updateScoreBreakdowns(details) {
                        updateScoreBar('repayment', details.input_repayment_history || 0);
                        updateScoreBar('obligations', details.financial_obligations || 0);
                        updateScoreBar('produce', details.produce_history || 0);
                        updateScoreBar('ratio', details.amount_ratio || 0);
                    }

                    // Update individual score progress bar
                    function updateScoreBar(type, score) {
                        const progress = $('#' + type + '-progress');
                        const scoreText = $('#' + type + '-score');

                        // Animate the progress bar with staggered timing
                        progress.css('width', '0%');
                        setTimeout(() => {
                            progress.css('width', score + '%');
                            scoreText.text(score);
                        }, 300 + (Math.random() * 200));
                    }

                    // Update credit summary display
                    function updateCreditSummaryDisplay() {
                        $('#status-input-value').text(formatCurrency(creditData.total_amount));
                        $('#status-interest-rate').text(creditData.credit_percentage);
                        $('#status-total-repayment').text(formatCurrency(creditData.total_with_interest));
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

                    // Generate recommendations for approved/under review applications
                    function generateApprovedRecommendations() {
                        return `
            <div class="alert alert-success">
                <h6><i class="bi bi-shop me-2"></i>Agrovet Review in Progress</h6>
                <p class="mb-2">Great news! Your application is being reviewed by ${creditData.agrovet_name}. Here's what happens next:</p>
                <ul class="mb-3">
                    <li><strong>Agrovet Review:</strong> ${creditData.agrovet_name} will assess your application within 24-48 hours</li>
                    <li><strong>Input Preparation:</strong> The agrovet will prepare your selected inputs</li>
                    <li><strong>Collection Notice:</strong> You'll be contacted when inputs are ready for collection</li>
                    <li><strong>Credit Terms:</strong> ${creditData.repayment_percentage}% will be deducted from your future produce sales</li>
                </ul>
                <div class="alert alert-info mb-0">
                    <small><i class="bi bi-telephone me-1"></i>The agrovet may contact you directly for clarification or to schedule collection.</small>
                </div>
            </div>
        `;
                    }

                    // Generate recommendations for pending applications
                    function generatePendingRecommendations(score, details) {
                        return `
            <div class="alert alert-warning">
                <h6><i class="bi bi-exclamation-triangle me-2"></i>Additional Assessment Required</h6>
                <p class="mb-2">Your application requires further review. To strengthen your application:</p>
                <ul class="mb-3">
                    <li>Maintain consistent produce deliveries over the next 1-2 months</li>
                    <li>Consider reducing the credit amount requested</li>
                    <li>Work on reducing existing debt obligations if possible</li>
                    <li>Contact ${creditData.agrovet_name} directly to discuss options</li>
                </ul>
                <div class="alert alert-info mb-0">
                    <small><i class="bi bi-clock me-1"></i>Review timeline: 3-5 business days for comprehensive assessment</small>
                </div>
            </div>
        `;
                    }

                    // Generate recommendations for rejected applications
                    function generateRejectedRecommendations(details) {
                        const improvements = [];

                        if (details.input_repayment_history < 50) {
                            improvements.push('Complete any existing input credit obligations successfully');
                        }
                        if (details.financial_obligations < 50) {
                            improvements.push('Reduce current debt-to-income ratio');
                        }
                        if (details.produce_history < 50) {
                            improvements.push('Establish consistent produce delivery history (3+ months)');
                        }
                        if (details.amount_ratio < 50) {
                            improvements.push(
                                'Apply for a smaller credit amount more aligned with your produce value');
                        }

                        return `
            <div class="alert alert-danger">
                <h6><i class="bi bi-x-circle me-2"></i>Application Declined</h6>
                <p class="mb-2">Your application did not meet the current credit criteria. To improve future applications:</p>
                <ul class="mb-3">
                    ${improvements.map(item => `<li>${item}</li>`).join('')}
                    <li>Consider applying for smaller amounts initially</li>
                    <li>Build stronger financial profile before reapplying</li>
                </ul>
                <div class="row">
                    <div class="col-md-6">
                        <div class="alert alert-info mb-0">
                            <small><i class="bi bi-clock me-1"></i><strong>Recommended wait time:</strong> 3 months before reapplying</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-success mb-0">
                            <small><i class="bi bi-lightbulb me-1"></i><strong>Alternative:</strong> Consider cash purchases or smaller amounts</small>
                        </div>
                    </div>
                </div>
            </div>
        `;
                    }

                    // Generate default recommendations
                    function generateDefaultRecommendations() {
                        return `
            <div class="alert alert-info">
                <h6><i class="bi bi-info-circle me-2"></i>Application Processing</h6>
                <p class="mb-2">Your application has been submitted to ${creditData.agrovet_name} and is being processed.</p>
                <ul class="mb-0">
                    <li>You will receive SMS/email notifications about status changes</li>
                    <li>The agrovet may contact you directly for additional information</li>
                    <li>Expected processing time: 24-48 hours</li>
                </ul>
            </div>
        `;
                    }

                    // Show success notification with enhanced details
                    function showSuccessNotification(result) {
                        setTimeout(() => {
                            toastr.info(
                                `Your application has been forwarded to ${creditData.agrovet_name}. Reference: ${result.reference_number}`,
                                'Processing Started', {
                                    timeOut: 8000
                                }
                            );
                        }, 2000);
                    }

                    // Highlight submission errors if specific field errors are returned
                    function highlightSubmissionErrors(result) {
                        if (result.field_errors) {
                            Object.keys(result.field_errors).forEach(field => {
                                const selector = getFieldSelector(field);
                                if (selector) {
                                    markFieldInvalid(selector, result.field_errors[field]);
                                }
                            });
                        }
                    }

                    // Map backend field names to frontend selectors
                    function getFieldSelector(fieldName) {
                        const fieldMap = {
                            'agrovet_id': '#agrovet-select',
                            'total_amount': '#total-input-amount',
                            'credit_percentage': '#credit-percentage',
                            'repayment_percentage': '#repayment-percentage',
                            'purpose': '#credit-purpose',
                            'selected_inputs': '.selected-items-table'
                        };
                        return fieldMap[fieldName] || null;
                    }

                    // Helper functions for validation display
                    function markFieldValid(selector) {
                        const field = $(selector);
                        field.removeClass('is-invalid').addClass('is-valid');
                        field.next('.invalid-feedback').remove();
                    }

                    function markFieldInvalid(selector, message) {
                        const field = $(selector);
                        field.removeClass('is-valid').addClass('is-invalid');
                        field.next('.invalid-feedback').remove();
                        field.after(`<div class="invalid-feedback">${message}</div>`);
                    }

                    function showValidationErrors(errors) {
                        if (errors.length > 0) {
                            const errorTitle = `Application Error${errors.length > 1 ? 's' : ''}`;
                            const errorMessage = errors.length === 1 ?
                                errors[0] :
                                `Please fix the following issues:\n• ${errors.join('\n• ')}`;
                            toastr.error(errorMessage, errorTitle);
                        }
                    }

                    // Copy reference function
                    function copyReference() {
                        const referenceText = document.getElementById('final-reference-number').textContent;
                        navigator.clipboard.writeText(referenceText).then(function() {
                            toastr.success('Reference number copied to clipboard!', 'Copied');
                        });
                    }

                    // Make copyReference function global
                    window.copyReference = copyReference;

                    // Update summary page with all application details
                    function updateSummary() {
                        // Update basic information
                        $('#summary-agrovet-name').text(creditData.agrovet_name + ' (' + creditData
                            .agrovet_location + ')');
                        $('#summary-input-value').text(formatCurrency(creditData.total_amount));
                        $('#summary-credit-percentage').text(creditData.credit_percentage);
                        $('#summary-repayment-percentage').text(creditData.repayment_percentage);
                        $('#summary-purpose').text(creditData.purpose || 'Not specified');

                        // Calculate final values
                        calculateCreditTerms();
                    }

                    // Prevent form submission on enter key (except in textareas)
                    $(document).on('keypress', function(e) {
                        if (e.which === 13 && e.target.tagName !== 'TEXTAREA') {
                            e.preventDefault();
                        }
                    });

                    // Export functions to global scope for access from other parts
                    window.inputCreditApp = {
                        ...window.inputCreditApp, // Preserve previous parts
                        submitInputCreditApplication: submitInputCreditApplication,
                        updateInputCreditStatusTab: updateInputCreditStatusTab,
                        handleSubmissionSuccess: handleSubmissionSuccess,
                        handleSubmissionError: handleSubmissionError,
                        updateSummary: updateSummary,
                        showSuccessNotification: showSuccessNotification
                    };

                    console.log(
                        'Input Credit Application system fully initialized with 5-tab navigation and status display'
                    );

                }); // End of document ready
                </script>


</body>

</html>