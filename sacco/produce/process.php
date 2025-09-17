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
                        <!-- if the user is an admin -->
                        <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 5): ?>
                        <?php
                        $app = new App;
                        $email = $_SESSION['email'];
                        $query = "SELECT * FROM users WHERE id=" . $_SESSION['user_id'];
                        $admin = $app->select_one($query);
                        ?>
                        <p class="fw-semibold fs-18 mb-0">Welcome <?php echo $admin->first_name ?>
                            <?php echo $admin->last_name ?></p>

                        <?php else: ?>
                        <?php
                        $app = new App;
                        $query = "SELECT * FROM users WHERE id=" . $_SESSION['user_id'];
                        $staff = $app->select_one($query);
                        ?>
                        <p class="fw-semibold fs-18 mb-0">Welcome <?php echo $staff->first_name ?>
                            <?php echo $staff->last_name ?></p>
                        <?php endif; ?>
                        <span class="fs-semibold text-muted pt-5">Produce Management Dashboard</span>
                    </div>
                </div>

                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                    <h1 class="page-title fw-semibold fs-18 mb-0">Process Produce Sales</h1>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Produce</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Process Sales</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <!-- Produce Stats Cards -->
                <div class="row mt-2">
                    <!-- Pending Produce -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden" style="background:#6AA32D!important;">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded"
                                            style="background-color: white!important;">
                                            <i style="color:#6AA32D" class="fa-solid fa-hourglass-half fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-white mb-0">Pending Produce</p>
                                                <?php
                                                   $query = "SELECT COUNT(*) as count FROM produce_deliveries WHERE status = 'pending'";
                                                   $result = $app->select_one($query);
                                                   $pending_produce = ($result) ? $result->count : 0;
                                                   ?>
                                                <h4 style="color:wheat;" class="fw-semibold mt-1">
                                                    <?php echo $pending_produce ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Verified Produce -->
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
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Verified
                                                    Produce</p>
                                                <?php
                                             $query = "SELECT COUNT(*) as count FROM produce_deliveries WHERE status = 'verified'";
                                             $result = $app->select_one($query);
                                             $verified_produce = ($result) ? $result->count : 0;
                                             ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php echo $verified_produce ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sold Produce -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-money-bill-wave fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Sold Produce
                                                </p>
                                                <?php
                                                 $query = "SELECT COUNT(*) as count FROM produce_deliveries WHERE status = 'sold'";
                                                 $result = $app->select_one($query);
                                                 $sold_produce = ($result) ? $result->count : 0;
                                                 ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php echo $sold_produce ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Sales Value -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-cash-register fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Total Sales
                                                    Value</p>
                                                <?php
                                                  $query = "SELECT COALESCE(SUM(total_value), 0) as total_sales 
                                                           FROM produce_deliveries 
                                                           WHERE status = 'sold'";
                                                  $result = $app->select_one($query);
                                                  $total_sales = ($result) ? number_format($result->total_sales, 2) : 0;
                                                  ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    KES <?php echo $total_sales ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Produce Section -->
                <div class="row mt-4">
                    <div class="col-xl-12">
                        <div id="pendingProduceSection"></div>
                    </div>
                </div>

                <!-- Verified Produce Section -->
                <div class="row mt-4">
                    <div class="col-xl-12">
                        <div id="verifiedProduceSection"></div>
                    </div>
                </div>


                <!-- Sale Modal -->
                <!-- Sale Modal -->
                <div class="modal fade" id="saleModal" tabindex="-1" aria-labelledby="saleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="saleModalLabel">Mark Produce as Sold</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="saleForm">
                                    <input type="hidden" id="saleProduceId">

                                    <div class="mb-3">
                                        <label for="buyerName" class="form-label">Buyer Name *</label>
                                        <input type="text" class="form-control" id="buyerName"
                                            placeholder="Enter buyer name" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="saleNotes" class="form-label">Notes</label>
                                        <textarea class="form-control" id="saleNotes" rows="3"
                                            placeholder="Enter any additional notes about the sale"></textarea>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" onclick="processSale()">Confirm
                                    Sale</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Reject Modal -->
                <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="rejectModalLabel">Reject Produce</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="rejectForm">
                                    <input type="hidden" id="rejectProduceId">

                                    <div class="mb-3">
                                        <label for="rejectionReason" class="form-label">Reason for Rejection *</label>
                                        <textarea class="form-control" id="rejectionReason" rows="4"
                                            placeholder="Please provide a detailed reason for rejecting this produce"
                                            required></textarea>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-danger" onclick="processRejection()">Confirm
                                    Rejection</button>
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
    <script>
    $(document).ready(() => {
        displayPendingProduce();
        displayVerifiedProduce();
    });

    // Function to display pending produce deliveries
    function displayPendingProduce() {
        let displayPendingProduce = "true";
        $.ajax({
            url: "http://localhost/dfcs/ajax/produce-controller/display-pending-produce.php",
            type: 'POST',
            data: {
                displayPendingProduce: displayPendingProduce,
            },
            success: function(data, status) {
                $('#pendingProduceSection').html(data);
            },
        });
    }

    // Function to display verified produce ready for sale
    function displayVerifiedProduce() {
        let displayVerifiedProduce = "true";
        $.ajax({
            url: "http://localhost/dfcs/ajax/produce-controller/display-verified-produce.php",
            type: 'POST',
            data: {
                displayVerifiedProduce: displayVerifiedProduce,
            },
            success: function(data, status) {
                $('#verifiedProduceSection').html(data);
            },
        });
    }

    // Function to verify produce
    function verifyProduce(produceId) {
        $.ajax({
            url: "http://localhost/dfcs/ajax/produce-controller/verify-produce.php",
            type: "POST",
            data: {
                produceId: produceId
            },
            success: function(data, status) {
                let response = JSON.parse(data);
                if (response.success) {
                    toastr.success('Produce verified successfully', 'Success', {
                        "positionClass": "toast-top-right",
                        "progressBar": true,
                        "timeOut": 3000,
                        "extendedTimeOut": 1000,
                        "hideMethod": "fadeOut"
                    });

                    // Refresh both tables
                    setTimeout(function() {
                        displayPendingProduce();
                        displayVerifiedProduce();
                    }, 300);
                } else {
                    toastr.error(response.message, 'Error', {
                        "positionClass": "toast-top-right",
                        "progressBar": true,
                        "timeOut": 3000,
                        "extendedTimeOut": 1000,
                        "hideMethod": "fadeOut"
                    });
                }
            },
            error: function() {
                toastr.error('An error occurred while verifying produce', 'Error', {
                    "positionClass": "toast-top-right",
                    "progressBar": true,
                    "timeOut": 3000,
                    "extendedTimeOut": 1000,
                    "hideMethod": "fadeOut"
                });
            }
        });
    }

    // Function to reject produce
    function rejectProduce(produceId) {
        // Set the produce ID in the form
        $('#rejectProduceId').val(produceId);

        // Clear previous values
        $('#rejectionReason').val('');

        // Show the modal
        $('#rejectModal').modal('show');
    }

    // Function to process the rejection
    function processRejection() {
        // Get form values
        let produceId = $('#rejectProduceId').val();
        let rejectionReason = $('#rejectionReason').val();

        // Validate the form
        if (!rejectionReason) {
            toastr.error('Please provide a reason for rejection', 'Error', {
                "positionClass": "toast-top-right",
                "progressBar": true,
                "timeOut": 3000,
                "extendedTimeOut": 1000,
                "hideMethod": "fadeOut"
            });
            return;
        }

        // Process the rejection
        $.ajax({
            url: "http://localhost/dfcs/ajax/produce-controller/reject-produce.php",
            type: "POST",
            data: {
                produceId: produceId,
                rejectionReason: rejectionReason
            },
            success: function(data, status) {
                let response = JSON.parse(data);
                if (response.success) {
                    // Close the modal
                    $('#rejectModal').modal('hide');

                    toastr.success('Produce rejected successfully', 'Success', {
                        "positionClass": "toast-top-right",
                        "progressBar": true,
                        "timeOut": 3000,
                        "extendedTimeOut": 1000,
                        "hideMethod": "fadeOut"
                    });

                    // Refresh the pending produce table
                    setTimeout(function() {
                        displayPendingProduce();
                    }, 300);
                } else {
                    toastr.error(response.message, 'Error', {
                        "positionClass": "toast-top-right",
                        "progressBar": true,
                        "timeOut": 3000,
                        "extendedTimeOut": 1000,
                        "hideMethod": "fadeOut"
                    });
                }
            },
            error: function() {
                toastr.error('An error occurred while rejecting produce', 'Error', {
                    "positionClass": "toast-top-right",
                    "progressBar": true,
                    "timeOut": 3000,
                    "extendedTimeOut": 1000,
                    "hideMethod": "fadeOut"
                });
            }
        });
    }
    // Function to mark produce as sold
    function markAsSold(produceId) {
        // Open modal to collect sale information
        $('#saleProduceId').val(produceId);
        $('#saleModal').modal('show');
    }

    // Function to process the sale
    // Function to mark produce as sold
    function markAsSold(produceId) {
        // Open modal to collect sale information
        $('#saleProduceId').val(produceId);

        // Clear previous values
        $('#buyerName').val('');
        $('#saleNotes').val('');

        $('#saleModal').modal('show');
    }

    // Function to process the sale
    function processSale() {
        let produceId = $('#saleProduceId').val();
        let buyerName = $('#buyerName').val();
        let saleNotes = $('#saleNotes').val();

        if (!buyerName) {
            toastr.error('Please enter the buyer name', 'Error', {
                "positionClass": "toast-top-right",
                "progressBar": true,
                "timeOut": 3000,
                "extendedTimeOut": 1000,
                "hideMethod": "fadeOut"
            });
            return;
        }

        $.ajax({
            url: "http://localhost/dfcs/ajax/produce-controller/mark-as-sold.php",
            type: "POST",
            data: {
                produceId: produceId,
                buyerName: buyerName,
                saleNotes: saleNotes
            },
            success: function(data, status) {
                let response = JSON.parse(data);
                if (response.success) {
                    // Close the modal
                    $('#saleModal').modal('hide');

                    // Reset form
                    $('#saleForm')[0].reset();

                    toastr.success('Produce marked as sold successfully', 'Success', {
                        "positionClass": "toast-top-right",
                        "progressBar": true,
                        "timeOut": 3000,
                        "extendedTimeOut": 1000,
                        "hideMethod": "fadeOut"
                    });

                    // Refresh verified produce table
                    setTimeout(function() {
                        displayVerifiedProduce();
                    }, 300);
                } else {
                    toastr.error(response.message, 'Error', {
                        "positionClass": "toast-top-right",
                        "progressBar": true,
                        "timeOut": 3000,
                        "extendedTimeOut": 1000,
                        "hideMethod": "fadeOut"
                    });
                }
            },
            error: function() {
                toastr.error('An error occurred while processing the sale', 'Error', {
                    "positionClass": "toast-top-right",
                    "progressBar": true,
                    "timeOut": 3000,
                    "extendedTimeOut": 1000,
                    "hideMethod": "fadeOut"
                });
            }
        });
    }
    </script>

</body>



</html>