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
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                    <div>
                        <?php
                              $app = new App;
                              
                              // Get session user_id to identify agrovet staff
                              if (session_status() === PHP_SESSION_NONE) {
                                  session_start();
                              }
                              
                              $userId = $_SESSION['user_id'] ?? null;
                              
                              // Get staff agrovet_id
                              $staffQuery = "SELECT s.id as staff_id, s.agrovet_id, s.position,
                                            u.first_name, u.last_name, 
                                            a.name as agrovet_name
                                            FROM agrovet_staff s 
                                            JOIN users u ON s.user_id = u.id
                                            JOIN agrovets a ON s.agrovet_id = a.id
                                            WHERE s.user_id = :user_id";
                              
                              $staff = $app->selectOne($staffQuery, [':user_id' => $userId]);
                              ?>
                        <p class="fw-semibold fs-18 mb-0">Welcome <?php echo $staff->first_name ?>
                            <?php echo $staff->last_name ?></p>
                        <span class="fs-semibold text-muted pt-5">Input Catalog History Dashboard</span>
                    </div>
                </div>
                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                    <h1 class="page-title fw-semibold fs-18 mb-0">Input Catalog</h1>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Input Catalog</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Browse</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <!-- Input Catalog Stats Cards -->
                <div class="row">
                    <!-- Total Inputs -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden" style="background:#6AA32D!important;">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded"
                                            style="background-color: white!important;">
                                            <i style="color:#6AA32D" class="fa-solid fa-boxes-stacked fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-white mb-0">Total Inputs</p>
                                                <h4 style="color:wheat;" class="fw-semibold mt-1"
                                                    id="total-inputs-count">0</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fertilizers -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-seedling fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Fertilizers</p>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;"
                                                    id="fertilizers-count">0</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pesticides -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-bug-slash fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Pesticides</p>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;"
                                                    id="pesticides-count">0</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Average Price -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-tag fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Average Price</p>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;" id="average-price">
                                                    KES 0.00</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- All Input Catalog Section -->
                <div class="row mt-4">
                    <div class="col-xl-12">
                        <div id="allInputCatalogSection">
                            <div class="card custom-card">
                                <div class="card-header justify-content-between">
                                    <div class="card-title">
                                        <i class="ri-shopping-bag-line me-2"></i> Input Catalog
                                    </div>
                                    <div class="btn-group">
                                        <button class="btn btn-outline-primary btn-sm" id="btnShowAll">All</button>
                                        <button class="btn btn-outline-success btn-sm"
                                            id="btnShowFertilizers">Fertilizers</button>
                                        <button class="btn btn-outline-warning btn-sm"
                                            id="btnShowPesticides">Pesticides</button>
                                        <button class="btn btn-outline-info btn-sm" id="btnShowSeeds">Seeds</button>
                                        <button class="btn btn-outline-secondary btn-sm"
                                            id="btnShowTools">Tools</button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="datatable-all-inputs" class="table table-bordered text-nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th><i class="ri-hash-line me-1"></i>ID</th>
                                                    <th><i class="ri-product-hunt-line me-1"></i>Name</th>
                                                    <th><i class="ri-apps-line me-1"></i>Type</th>
                                                    <th><i class="ri-scales-line me-1"></i>Unit</th>
                                                    <th><i class="ri-money-dollar-circle-line me-1"></i>Price (KES)</th>
                                                    <th><i class="ri-file-list-line me-1"></i>Description</th>
                                                    <th><i class="ri-bar-chart-line me-1"></i>Popularity</th>
                                                    <th><i class="ri-settings-3-line me-1"></i>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Rows will be populated by AJAX call -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="inputDetailsModal" tabindex="-1" aria-labelledby="inputDetailsModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title" id="inputDetailsModalLabel">Input Details</h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div id="inputDetailsContent">
                                    <!-- Content will be loaded dynamically -->
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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

    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.30.0/dist/apexcharts.min.js"></script>
    <script>
    // Main script for input catalog page
    $(document).ready(() => {
        // Load input catalog
        displayAllInputs();

        // Handle filter application
        $('#apply-filters').on('click', function() {
            displayAllInputs(
                $('#input-type').val(),
                $('#price-range').val(),
                $('#sort-by').val()
            );
        });
    });
    // Function to display all input catalog items with optional filters
    function displayAllInputs(inputType = '', priceRange = '', sortBy = 'name-asc') {
        let displayAllInputs = "true";
        // Show loading indicator
        $('#allInputCatalogSection').html(
            '<div class="text-center p-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading input catalog...</p></div>'
        );
        $.ajax({
            url: "http://localhost/dfcs/ajax/input-catalog-controller/display-all-inputs.php",
            type: 'POST',
            data: {
                displayAllInputs: displayAllInputs,
                inputType: inputType,
                priceRange: priceRange,
                sortBy: sortBy
            },
            success: function(data, status) {
                $('#allInputCatalogSection').html(data);
            },
            error: function(xhr, status, error) {
                console.error("Error loading input catalog:", error);
                toastr.error('Failed to load input catalog', 'Error', {
                    "positionClass": "toast-top-right",
                    "progressBar": true,
                    "timeOut": 5000
                });

                // Show error message in the section
                $('#allInputCatalogSection').html(`
                <div class="card custom-card">
                    <div class="card-body text-center">
                        <i class="ri-error-warning-line fs-3 text-danger mb-3"></i>
                        <h5>Failed to Load Input Catalog</h5>
                        <p class="text-muted">There was an error loading the input catalog. Please try again.</p>
                        <button class="btn btn-primary" onclick="displayAllInputs()">
                            <i class="ri-refresh-line me-1"></i> Retry
                        </button>
                    </div>
                </div>
            `);
            }
        });
    }

    // Function to view input details
    function viewInputDetails(inputId) {
        // Reset modal content and show loading state
        $('#inputDetailsContent').html(`
        <div class="text-center p-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2">Loading input details...</p>
        </div>
    `);

        // Update view analysis button
        $('#viewAnalysisBtn').attr('href', `http://localhost/dfcs/agrovet/catalog/input-analysis/${inputId}`);

        // Show modal
        $('#inputDetailsModal').modal('show');

        // Fetch input details
        $.ajax({
            url: "http://localhost/dfcs/ajax/input-catalog-controller/get-input-details.php",
            type: 'POST',
            data: {
                inputId: inputId
            },
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    const input = data.input;

                    // Determine badge color and icon based on type
                    let badgeColor, typeIcon;
                    switch (input.type) {
                        case 'fertilizer':
                            badgeColor = 'success';
                            typeIcon = 'fa-seedling';
                            break;
                        case 'pesticide':
                            badgeColor = 'warning';
                            typeIcon = 'fa-bug-slash';
                            break;
                        case 'seeds':
                            badgeColor = 'info';
                            typeIcon = 'fa-leaf';
                            break;
                        case 'tools':
                            badgeColor = 'primary';
                            typeIcon = 'fa-tools';
                            break;
                        default:
                            badgeColor = 'secondary';
                            typeIcon = 'fa-box';
                    }

                    // Format the content with all the available data
                    $('#inputDetailsContent').html(`
                    <!-- Header section with avatar and basic info -->
                    <div class="row">
                        <div class="col-md-12 mb-4 text-center">
                            <div class="avatar avatar-xl bg-${badgeColor}-transparent mb-2 mx-auto">
                                <i class="fa-solid ${typeIcon} fs-1 text-${badgeColor}"></i>
                            </div>
                            <h4 class="mb-1">${input.name}</h4>
                            <span class="badge bg-${badgeColor}-transparent text-${badgeColor} fs-6 px-3 py-2">
                                <i class="fa-solid ${typeIcon} me-1"></i> ${input.type.charAt(0).toUpperCase() + input.type.slice(1)}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Main details section -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="card custom-card h-100">
                                <div class="card-header">
                                    <div class="card-title"><i class="fa-solid fa-circle-info me-2"></i>Basic Information</div>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="fa-solid fa-hashtag me-2 text-muted"></i>ID</span>
                                            <span class="fw-semibold">${input.id}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="fa-solid fa-scale-balanced me-2 text-muted"></i>Unit</span>
                                            <span class="fw-semibold">${input.standard_unit}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="fa-solid fa-tag me-2 text-muted"></i>Price</span>
                                            <span class="fw-semibold text-${badgeColor}">KES ${parseFloat(input.standard_price).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="fa-solid fa-clock-rotate-left me-2 text-muted"></i>Last Updated</span>
                                            <span class="fw-semibold">${input.updated_at ? new Date(input.updated_at).toLocaleDateString() : 'N/A'}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="fa-solid fa-toggle-on me-2 text-muted"></i>Status</span>
                                            <span class="badge bg-success">Active</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card custom-card h-100">
                                <div class="card-header">
                                    <div class="card-title"><i class="fa-solid fa-chart-line me-2"></i>Usage Statistics</div>
                                </div>
                                <div class="card-body">
                                    <h5 class="mb-3"><i class="fa-solid fa-chart-simple me-2 text-${badgeColor}"></i>Request Distribution</h5>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Overall Credit Requests:</span>
                                        <span class="fw-semibold">${input.request_count}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span>Your Agrovet Requests:</span>
                                        <span class="fw-semibold">${input.agrovet_request_count}</span>
                                    </div>
                                    
                                    <h5 class="mb-3"><i class="fa-solid fa-percentage me-2 text-${badgeColor}"></i>Usage Percentage</h5>
                                    <div class="progress mb-2" style="height: 10px;">
                                        <div class="progress-bar bg-${badgeColor}" role="progressbar" 
                                             style="width: ${input.usage_percentage}%;" 
                                             aria-valuenow="${input.usage_percentage}" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                    <p class="small text-muted mb-0">
                                        This input appears in ${input.usage_percentage}% of all credit applications at your agrovet
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Description section -->
                    <div class="card custom-card mb-4">
                        <div class="card-header">
                            <div class="card-title"><i class="fa-solid fa-align-left me-2"></i>Description</div>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">${input.description || 'No description available'}</p>
                        </div>
                    </div>
                    
                    <!-- Usage trend section (if available) -->
                    ${input.usage_trend ? `
                    <div class="card custom-card mb-4">
                        <div class="card-header">
                            <div class="card-title"><i class="fa-solid fa-chart-area me-2"></i>Monthly Usage Trend</div>
                        </div>
                        <div class="card-body">
                            <div id="usageTrendChart" style="height: 200px;"></div>
                        </div>
                    </div>
                    ` : ''}
                    
                    <!-- Related inputs section (if available) -->
                    ${input.related_inputs && input.related_inputs.length > 0 ? `
                    <div class="card custom-card">
                        <div class="card-header">
                            <div class="card-title"><i class="fa-solid fa-layer-group me-2"></i>Related Inputs</div>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                ${input.related_inputs.map(related => `
                                <div class="col-md-6">
                                    <div class="card border">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="avatar avatar-sm bg-${badgeColor}-transparent me-2">
                                                    <i class="fa-solid ${typeIcon}"></i>
                                                </span>
                                                <h6 class="mb-0">${related.name}</h6>
                                            </div>
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-muted">Price:</span>
                                                <span>KES ${parseFloat(related.standard_price).toLocaleString(undefined, {minimumFractionDigits: 2})}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Unit:</span>
                                                <span>${related.standard_unit}</span>
                                            </div>
                                            <button class="btn btn-sm btn-${badgeColor}-transparent w-100" 
                                                    onclick="viewInputDetails(${related.id})">
                                                <i class="fa-solid fa-eye me-1"></i> View Details
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                `).join('')}
                            </div>
                        </div>
                    </div>
                    ` : ''}
                `);

                    // Initialize ApexCharts if usage trend data is available
                    // Initialize ApexCharts if usage trend data is available
                    if (input.usage_trend && input.usage_trend.months.length > 0) {
                        let chartColor;
                        switch (badgeColor) {
                            case 'success':
                                chartColor = '#28a745';
                                break;
                            case 'warning':
                                chartColor = '#ffc107';
                                break;
                            case 'info':
                                chartColor = '#17a2b8';
                                break;
                            case 'primary':
                                chartColor = '#0d6efd';
                                break;
                            default:
                                chartColor = '#6c757d';
                        }

                        setTimeout(() => {
                            var options = {
                                series: [{
                                    name: 'Credit Requests',
                                    data: input.usage_trend.counts
                                }],
                                chart: {
                                    height: 200,
                                    type: 'line',
                                    toolbar: {
                                        show: false
                                    },
                                    zoom: {
                                        enabled: false
                                    }
                                },
                                stroke: {
                                    curve: 'smooth',
                                    width: 3
                                },
                                markers: {
                                    size: 5,
                                    hover: {
                                        size: 7
                                    }
                                },
                                colors: [chartColor],
                                dataLabels: {
                                    enabled: true,
                                    offsetY: -5,
                                    style: {
                                        fontSize: '12px',
                                        fontWeight: 'bold'
                                    }
                                },
                                grid: {
                                    borderColor: '#e0e0e0',
                                    row: {
                                        colors: ['#f8f9fa', 'transparent'],
                                        opacity: 0.5
                                    }
                                },
                                xaxis: {
                                    categories: input.usage_trend.months,
                                    axisBorder: {
                                        show: true,
                                        color: '#e0e0e0'
                                    },
                                    axisTicks: {
                                        show: true,
                                        color: '#e0e0e0'
                                    }
                                },
                                yaxis: {
                                    min: 0,
                                    forceNiceScale: true,
                                    labels: {
                                        formatter: function(val) {
                                            return Math.round(val);
                                        }
                                    }
                                },
                                fill: {
                                    type: 'gradient',
                                    gradient: {
                                        shade: 'light',
                                        type: "vertical",
                                        shadeIntensity: 0.25,
                                        gradientToColors: undefined,
                                        inverseColors: true,
                                        opacityFrom: 0.85,
                                        opacityTo: 0.55,
                                        stops: [0, 100]
                                    }
                                },
                                tooltip: {
                                    shared: false,
                                    y: {
                                        formatter: function(val) {
                                            return val + " requests";
                                        }
                                    }
                                }
                            };

                            var chart = new ApexCharts(document.querySelector("#usageTrendChart"),
                                options);
                            chart.render();
                        }, 100);
                    }
                } else {
                    $('#inputDetailsContent').html(`
                    <div class="text-center p-5">
                        <div class="avatar avatar-xl bg-danger-transparent mb-3">
                            <i class="fa-solid fa-triangle-exclamation fs-1 text-danger"></i>
                        </div>
                        <h5>Error Loading Details</h5>
                        <p class="text-muted">${data.message || 'Failed to load input details'}</p>
                    </div>
                `);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
                console.log("Response:", xhr.responseText);

                $('#inputDetailsContent').html(`
                <div class="text-center p-5">
                    <div class="avatar avatar-xl bg-danger-transparent mb-3">
                        <i class="fa-solid fa-triangle-exclamation fs-1 text-danger"></i>
                    </div>
                    <h5>Error Loading Details</h5>
                    <p class="text-muted">Failed to load input details. Please try again.</p>
                    <button class="btn btn-sm btn-primary" onclick="viewInputDetails(${inputId})">
                        <i class="fa-solid fa-refresh me-1"></i> Retry
                    </button>
                </div>
            `);
            }
        });
    }
    // Helper function to dynamically load input analysis page
    function viewInputAnalysis(inputId) {
        window.location.href = `http://localhost/dfcs/agrovet/catalog/input-analysis/${inputId}`;
    }

    // Helper function to format currency
    function formatCurrency(amount) {
        return parseFloat(amount).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // Setup event handlers when the document is loaded
    $(document).ready(function() {
        // Handle quick filter buttons in the DataTable
        $(document).on('click', '#btnShowAll', function() {
            var table = $('#datatable-all-inputs').DataTable();
            table.search('').draw();
        });

        $(document).on('click', '#btnShowFertilizers', function() {
            var table = $('#datatable-all-inputs').DataTable();
            table.search('Fertilizer').draw();
        });

        $(document).on('click', '#btnShowPesticides', function() {
            var table = $('#datatable-all-inputs').DataTable();
            table.search('Pesticide').draw();
        });

        $(document).on('click', '#btnShowSeeds', function() {
            var table = $('#datatable-all-inputs').DataTable();
            table.search('Seeds').draw();
        });

        $(document).on('click', '#btnShowTools', function() {
            var table = $('#datatable-all-inputs').DataTable();
            table.search('Tools').draw();
        });

        // Initialize tooltips everywhere
        initTooltips();
    });

    // Function to initialize tooltips
    function initTooltips() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    </script>
</body>



</html>