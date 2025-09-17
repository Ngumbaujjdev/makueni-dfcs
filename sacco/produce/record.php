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
                    <h1 class="page-title fw-semibold fs-18 mb-0">Record Produce Delivery</h1>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Produce</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Record Delivery</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <!-- Tabs Navigation -->
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">Record Farmer Produce</div>
                    </div>
                    <div class="card-body add-products p-0">
                        <ul class="nav nav-tabs" id="produceTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#farmer-selection"
                                    type="button" role="tab">
                                    <i class="bi bi-person me-1"></i>Select Farmer
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#farm-selection"
                                    type="button" role="tab">
                                    <i class="bi bi-house me-1"></i>Select Farm
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#product-selection"
                                    type="button" role="tab">
                                    <i class="bi bi-box me-1"></i>Farm Products
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#delivery-details"
                                    type="button" role="tab">
                                    <i class="bi bi-truck me-1"></i>Delivery Details
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#quality-grading"
                                    type="button" role="tab">
                                    <i class="bi bi-award me-1"></i>Quality & Grading
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#payment-info"
                                    type="button" role="tab">
                                    <i class="bi bi-cash-coin me-1"></i>Payment Info
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content Container -->
                        <div class="tab-content p-4">
                            <!-- Tab Content -->
                            <div class="tab-content p-4">
                                <!-- 1. Farmer Selection Tab -->
                                <div class="tab-pane fade show active" id="farmer-selection" role="tabpanel">
                                    <div class="row gy-3">
                                        <div class="col-xl-12">
                                            <label class="form-label">Select Farmer</label>
                                            <select class="form-control select2" id="farmer-select" name="farmer_id">
                                                <option value="">Select a farmer...</option>
                                                <?php
                                                   $query = "SELECT f.id, f.registration_number, 
                                                            u.first_name, u.last_name
                                                            FROM farmers f 
                                                            JOIN users u ON f.user_id = u.id
                                                            WHERE f.is_verified = 1";
                                                   $farmers = $app->select_all($query);
                                                   foreach($farmers as $farmer): ?>
                                                <option value="<?php echo $farmer->id; ?>">
                                                    <?php echo $farmer->first_name . ' ' . $farmer->last_name; ?>
                                                    (<?php echo $farmer->registration_number; ?>)
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end mt-3">
                                        <button class="btn text-white" id="nextToFarm" style="background:#6AA32D;">
                                            Next <i class="bi bi-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- 2. Farm Selection Tab -->
                                <div class="tab-pane fade" id="farm-selection" role="tabpanel">
                                    <div class="row gy-3">
                                        <div class="col-xl-12">
                                            <label class="form-label">Select Farm</label>
                                            <select class="form-control" id="farm-select" name="farm_id">
                                                <option value="">Select a farm...</option>
                                                <!-- Will be populated via AJAX -->
                                            </select>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between mt-4">
                                        <button class="btn btn-light" id="backToFarmer">
                                            <i class="bi bi-arrow-left me-2"></i>Previous
                                        </button>
                                        <button class="btn text-white" id="nextToProduce" style="background:#6AA32D;">
                                            Next <i class="bi bi-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- 3. Produce & Pricing Tab -->
                                <div class="tab-pane fade" id="produce-details" role="tabpanel">
                                    <div class="row gy-3">
                                        <div class="col-xl-12">
                                            <label class="form-label">Select Product</label>
                                            <select class="form-control" id="product-select" name="farm_product_id">
                                                <option value="">Select a product...</option>
                                                <!-- Will be populated via AJAX -->
                                            </select>
                                        </div>

                                        <div class="col-xl-6">
                                            <label class="form-label">Quantity (KG)</label>
                                            <input type="number" class="form-control" id="quantity" name="quantity">
                                        </div>

                                        <div class="col-xl-6">
                                            <label class="form-label">Unit Price (KES/KG)</label>
                                            <input type="number" class="form-control" id="unit-price" name="unit_price">
                                        </div>

                                        <div class="col-xl-12 mt-3">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h6 class="card-title">Total Value</h6>
                                                    <h3 class="mb-0" id="total-value">KES 0.00</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between mt-4">
                                        <button class="btn btn-light" id="backToFarms">
                                            <i class="bi bi-arrow-left me-2"></i>Previous
                                        </button>
                                        <button class="btn text-white" id="nextToQuality" style="background:#6AA32D;">
                                            Next <i class="bi bi-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                </div>
                                <!-- Continuing from previous tabs... -->

                                <!-- 4. Quality & Grading Tab -->
                                <div class="tab-pane fade" id="quality-check" role="tabpanel">
                                    <div class="row gy-3">
                                        <div class="col-xl-12">
                                            <label class="form-label">Quality Grade</label>
                                            <select class="form-control" id="quality-grade" name="quality_grade">
                                                <option value="">Select grade...</option>
                                                <option value="A">Grade A</option>
                                                <option value="B">Grade B</option>
                                                <option value="C">Grade C</option>
                                            </select>
                                        </div>

                                        <div class="col-xl-12">
                                            <label class="form-label">Rejection Reason (if applicable)</label>
                                            <textarea class="form-control" id="rejection-reason" name="rejection_reason"
                                                rows="3" placeholder="Enter reason if produce is rejected"></textarea>
                                        </div>

                                        <div class="col-xl-12">
                                            <label class="form-label">Notes/Comments</label>
                                            <textarea class="form-control" id="quality-notes" name="notes" rows="3"
                                                placeholder="Enter any additional notes about quality"></textarea>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between mt-4">
                                        <button class="btn btn-light" id="backToProduce">
                                            <i class="bi bi-arrow-left me-2"></i>Previous
                                        </button>
                                        <button class="btn text-white" id="nextToPayment" style="background:#6AA32D;">
                                            Next <i class="bi bi-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- 5. Payment Summary Tab -->
                                <div class="tab-pane fade" id="payment-summary" role="tabpanel">
                                    <div class="row gy-3">
                                        <!-- Delivery Summary Card -->
                                        <div class="col-xl-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h6 class="card-title mb-0">Delivery Summary</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p class="mb-2">Quantity Delivered: <span
                                                                    id="summary-quantity">0</span> KG</p>
                                                            <p class="mb-2">Unit Price: KES <span
                                                                    id="summary-unit-price">0</span></p>
                                                            <p class="mb-0">Quality Grade: <span
                                                                    id="summary-grade">-</span></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h5 class="mb-2">Total Amount: KES <span
                                                                    id="summary-total">0.00</span></h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Payment Details -->
                                        <div class="col-xl-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h6 class="card-title mb-0">Delivery Details</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row gy-3">
                                                        <!-- Reference Number -->
                                                        <div class="col-md-12">
                                                            <div class="alert alert-info mb-3">
                                                                <strong>Delivery Reference Number:</strong>
                                                                <span id="delivery-reference">
                                                                    <?php 
                                                                    // Generate reference number: DLVR/YYYYMMDD/RANDOM4DIGITS
                                                                    $refNumber = 'DLVR/' . date('Ymd') . '/' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
                                                                    echo $refNumber;
                                                                    ?>
                                                                </span>
                                                                <input type="hidden" name="reference_number"
                                                                    value="<?php echo $refNumber; ?>">
                                                            </div>
                                                        </div>

                                                        <!-- Summary Row -->
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-semibold">Quantity</label>
                                                            <p class="mb-0"><span id="final-quantity">0</span> KG</p>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <label class="form-label fw-semibold">Unit Price</label>
                                                            <p class="mb-0">KES <span id="final-unit-price">0</span></p>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <label class="form-label fw-semibold">Quality Grade</label>
                                                            <p class="mb-0"><span id="final-grade">-</span></p>
                                                        </div>

                                                        <!-- Total Value -->
                                                        <div class="col-md-12 mt-4">
                                                            <div class="bg-light p-3 rounded">
                                                                <div class="row align-items-center">
                                                                    <div class="col-md-6">
                                                                        <h6 class="mb-0">Total Value</h6>
                                                                    </div>
                                                                    <div class="col-md-6 text-end">
                                                                        <h4 class="mb-0">KES <span
                                                                                id="final-total">0.00</span></h4>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Payment Notice -->
                                                        <div class="col-md-12 mt-3">
                                                            <div class="alert alert-warning mb-0">
                                                                <i class="bi bi-info-circle me-2"></i>
                                                                Payment will be processed during the next scheduled
                                                                disbursement cycle
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Submit Button -->
                                        <div class="col-xl-12">
                                            <div class="d-flex justify-content-between mt-4">
                                                <button class="btn btn-light" id="backToQuality">
                                                    <i class="bi bi-arrow-left me-2"></i>Previous
                                                </button>
                                                <button type="submit" class="btn btn-success" id="submitDelivery">
                                                    <i class="bi bi-check-circle me-2"></i>Record Delivery
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
                $(document).ready(function() {
                    // Initialize select2
                    $('.select2').select2();

                    // Store all delivery data
                    let deliveryData = {
                        // Farmer Tab
                        farmer_id: '',
                        farmer_name: '',
                        farmer_reg: '',

                        // Farm Tab
                        farm_id: '',
                        farm_name: '',

                        // Produce Tab
                        product_id: '',
                        product_name: '',
                        quantity: 0,
                        unit_price: 0,
                        total_value: 0,

                        // Quality Tab
                        quality_grade: '',
                        notes: '',


                        reference_number: $('#delivery-reference').text()
                    };
                    // Product Selection
                    $('#product-select').change(function() {
                        let selectedOption = $(this).find('option:selected');
                        deliveryData.product_id = $(this).val();
                        deliveryData.product_name = selectedOption.text().split('(')[0].trim();
                    });
                    // Tab Navigation Controls
                    const tabs = {
                        farmer: '#farmer-selection',
                        farm: '#farm-selection',
                        produce: '#produce-details',
                        quality: '#quality-check',
                        payment: '#payment-summary'
                    };

                    // Validation Rules
                    function validateFarmerTab() {
                        if (!deliveryData.farmer_id) {
                            toastr.error('Please select a farmer');
                            return false;
                        }
                        return true;
                    }

                    function validateFarmTab() {
                        if (!deliveryData.farm_id) {
                            toastr.error('Please select a farm');
                            return false;
                        }
                        return true;
                    }

                    function validateProduceTab() {
                        if (!deliveryData.product_id) {
                            toastr.error('Please select a product');
                            return false;
                        }
                        if (!deliveryData.quantity || deliveryData.quantity <= 0) {
                            toastr.error('Please enter a valid quantity');
                            return false;
                        }
                        if (!deliveryData.unit_price || deliveryData.unit_price <= 0) {
                            toastr.error('Please enter a valid unit price');
                            return false;
                        }
                        return true;
                    }

                    function validateQualityTab() {
                        if (!deliveryData.quality_grade) {
                            toastr.error('Please select a quality grade');
                            return false;
                        }
                        return true;
                    }

                    // Event Handlers for Data Collection
                    $('#farmer-select').change(function() {
                        let selectedOption = $(this).find('option:selected');
                        deliveryData.farmer_id = $(this).val();
                        deliveryData.farmer_name = selectedOption.text().split('(')[0].trim();
                        deliveryData.farmer_reg = selectedOption.text().match(/\((.*?)\)/)[1];

                        // Update related farm select options visibility
                        $('#farm-select option').hide();
                        $(`#farm-select option[data-farmer="${deliveryData.farmer_id}"]`).show();
                    });

                    $('#farm-select').change(function() {
                        let selectedOption = $(this).find('option:selected');
                        deliveryData.farm_id = $(this).val();
                        deliveryData.farm_name = selectedOption.text().split('(')[0].trim();

                        // Update related product select options visibility
                        $('#product-select option').hide();
                        $(`#product-select option[data-farm="${deliveryData.farm_id}"]`).show();
                    });

                    // Calculate and update total value
                    function updateTotalValue() {
                        deliveryData.total_value = deliveryData.quantity * deliveryData.unit_price;
                        $('#total-value').text('KES ' + deliveryData.total_value.toFixed(2));
                        $('#final-total').text(deliveryData.total_value.toFixed(2));
                    }

                    // Quantity and Price Inputs
                    $('#quantity, #unit-price').on('input', function() {
                        deliveryData.quantity = parseFloat($('#quantity').val()) || 0;
                        deliveryData.unit_price = parseFloat($('#unit-price').val()) || 0;
                        updateTotalValue();
                    });

                    // Quality Grade Selection
                    $('#quality-grade').change(function() {
                        deliveryData.quality_grade = $(this).val();
                    });

                    // Notes Input
                    $('#quality-notes').on('input', function() {
                        deliveryData.notes = $(this).val();
                    });
                    // Continue from previous setup...

                    // Tab Navigation Functions
                    function showTab(tabId) {
                        $('.nav-link').removeClass('active');
                        $('.tab-pane').removeClass('show active');

                        $(`[data-bs-target="${tabId}"]`).addClass('active');
                        $(tabId).addClass('show active');

                        // Update summary when reaching payment tab
                        if (tabId === '#payment-summary') {
                            updatePaymentSummary();
                        }
                    }

                    // Tab Navigation Buttons
                    $('#nextToFarm').click(function() {
                        if (validateFarmerTab()) {
                            showTab('#farm-selection');
                        }
                    });

                    $('#backToFarmer').click(function() {
                        showTab('#farmer-selection');
                    });
                    // When Next is clicked on Farmer tab
                    $('#nextToFarm').click(function(e) {
                        e.preventDefault();
                        let farmerId = $('#farmer-select').val();

                        if (!farmerId) {
                            toastr.error('Please select a farmer first');
                            return false;
                        }

                        // Load farms for selected farmer
                        $.ajax({
                            url: 'http://localhost/dfcs/ajax/farmer-controller/get-farmer-farms.php',
                            type: 'POST',
                            data: {
                                farmer_id: farmerId
                            },
                            success: function(response) {
                                try {
                                    let data = JSON.parse(response);
                                    if (data.success) {
                                        $('#farm-select').html(data.html);
                                        $('#farmTabs button[data-bs-target="#farm-selection"]')
                                            .tab('show');
                                    } else {
                                        toastr.error(data.message || 'Error loading farms');
                                    }
                                } catch (e) {
                                    toastr.error('Error processing response');
                                }
                            },
                            error: function() {
                                toastr.error('Error loading farms');
                            }
                        });
                    });
                    $('#nextToProduce').click(function() {
                        if (validateFarmTab()) {
                            showTab('#produce-details');
                        }
                    });
                    // When Next is clicked on Farm tab
                    $('#nextToProduce').click(function(e) {
                        e.preventDefault();
                        let farmId = $('#farm-select').val();

                        if (!farmId) {
                            toastr.error('Please select a farm first');
                            return false;
                        }

                        // Load products for selected farm
                        $.ajax({
                            url: 'http://localhost/dfcs/ajax/farm-controller/get-farm-products.php',
                            type: 'POST',
                            data: {
                                farm_id: farmId
                            },
                            success: function(response) {
                                try {
                                    let data = JSON.parse(response);
                                    if (data.success) {
                                        $('#product-select').html(data.html);
                                        $('#farmTabs button[data-bs-target="#produce-details"]')
                                            .tab('show');
                                    } else {
                                        toastr.error(data.message ||
                                            'Error loading products');
                                    }
                                } catch (e) {
                                    toastr.error('Error processing response');
                                }
                            },
                            error: function() {
                                toastr.error('Error loading products');
                            }
                        });
                    });

                    // Calculate total value when quantity or price changes
                    $('#quantity, #unit-price').on('input', function() {
                        let quantity = parseFloat($('#quantity').val()) || 0;
                        let unitPrice = parseFloat($('#unit-price').val()) || 0;
                        let total = quantity * unitPrice;
                        $('#total-value').text('KES ' + total.toFixed(2));
                    });

                    $('#backToFarms').click(function() {
                        showTab('#farm-selection');
                    });

                    $('#nextToQuality').click(function() {
                        if (validateProduceTab()) {
                            showTab('#quality-check');
                        }
                    });

                    $('#backToProduce').click(function() {
                        showTab('#produce-details');
                    });

                    $('#nextToPayment').click(function() {
                        if (validateQualityTab()) {
                            showTab('#payment-summary');
                        }
                    });

                    $('#backToQuality').click(function() {
                        showTab('#quality-check');
                    });

                    // Update Payment Summary
                    function updatePaymentSummary() {
                        // Update Farmer Details
                        $('#summary-farmer-name').text(deliveryData.farmer_name);
                        $('#summary-farmer-reg').text(deliveryData.farmer_reg);

                        // Update Farm Details
                        $('#summary-farm-name').text(deliveryData.farm_name);
                        $('#summary-product').text(deliveryData.product_name);

                        // Update Delivery Details
                        $('#summary-quantity').text(deliveryData.quantity);
                        $('#final-quantity').text(deliveryData.quantity);

                        $('#summary-unit-price').text(deliveryData.unit_price);
                        $('#final-unit-price').text(deliveryData.unit_price);

                        $('#summary-grade').text(deliveryData.quality_grade);
                        $('#final-grade').text(deliveryData.quality_grade);

                        $('#summary-total').text(deliveryData.total_value.toFixed(2));
                        $('#summary-notes').text(deliveryData.notes || '-');
                    }

                    // Form Submission
                    $('#submitDelivery').click(function(e) {
                        e.preventDefault();

                        // Final validation
                        if (!validateAllTabs()) {
                            return;
                        }

                        // Prepare form data
                        let formData = new FormData();

                        // Add all delivery data
                        Object.keys(deliveryData).forEach(key => {
                            formData.append(key, deliveryData[key]);
                        });

                        // Submit form
                        $.ajax({
                            url: 'http://localhost/dfcs/ajax/produce-controller/add-record.php',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                try {
                                    let result = JSON.parse(response);
                                    if (result.success) {
                                        toastr.success(
                                            'Produce delivery recorded successfully');
                                        // Reset form or redirect
                                        setTimeout(() => {
                                            window.location.href =
                                                'produce-deliveries.php';
                                        }, 2000);
                                    } else {
                                        toastr.error(result.message ||
                                            'Error recording delivery');
                                    }
                                } catch (e) {
                                    toastr.error('Error processing response');
                                }
                            },
                            error: function() {
                                toastr.error('Error submitting delivery');
                            }
                        });
                    });

                    // Validate all tabs before final submission
                    function validateAllTabs() {
                        if (!validateFarmerTab()) {
                            showTab('#farmer-selection');
                            return false;
                        }
                        if (!validateFarmTab()) {
                            showTab('#farm-selection');
                            return false;
                        }
                        if (!validateProduceTab()) {
                            showTab('#produce-details');
                            return false;
                        }
                        if (!validateQualityTab()) {
                            showTab('#quality-check');
                            return false;
                        }
                        return true;
                    }

                    // Prevent form submission on enter key
                    $(document).on('keypress', function(e) {
                        if (e.which === 13) { // Enter key
                            e.preventDefault();
                        }
                    });

                    // Handle tab clicks (optional - if you want to allow direct tab clicking)
                    $('.nav-link').click(function(e) {
                        e.preventDefault();
                        let targetTab = $(this).data('bs-target');

                        // Only allow clicking if previous tabs are valid
                        if (validatePreviousTabs(targetTab)) {
                            showTab(targetTab);
                        }
                    });

                    function validatePreviousTabs(targetTab) {
                        switch (targetTab) {
                            case '#payment-summary':
                                if (!validateQualityTab()) return false;
                            case '#quality-check':
                                if (!validateProduceTab()) return false;
                            case '#produce-details':
                                if (!validateFarmTab()) return false;
                            case '#farm-selection':
                                if (!validateFarmerTab()) return false;
                        }
                        return true;
                    }
                });
                </script>
</body>

</html>