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
                        <span class="fs-semibold text-muted pt-5">Repayment Management Dashboard</span>
                    </div>
                </div>

                <!-- Page Header -->
                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                    <h1 class="page-title fw-semibold fs-18 mb-0">Loan Repayments</h1>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Loans</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Loan Repayments</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <!-- Bank Repayment Stats Cards -->
                <div class="row mt-2">
                    <!-- Total Bank Repayments -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden" style="background:#6AA32D!important;">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded"
                                            style="background-color: white!important;">
                                            <i style="color:#6AA32D" class="fa-solid fa-money-bill-transfer fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-white mb-0">Total Bank Repayments</p>
                                                <?php
                                   $query = "SELECT COUNT(*) as count FROM loan_repayments lr
                                            JOIN approved_loans al ON lr.approved_loan_id = al.id
                                            JOIN loan_applications la ON al.loan_application_id = la.id
                                            WHERE la.provider_type = 'bank'";
                                   $result = $app->select_one($query);
                                   $total_repayments = ($result) ? $result->count : 0;
                                ?>
                                                <h4 style="color:wheat;" class="fw-semibold mt-1">
                                                    <?php echo $total_repayments ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bank Repayment Amount -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-coins fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Bank Amount Repaid</p>
                                                <?php
                                    $query = "SELECT COALESCE(SUM(lr.amount), 0) as total_amount 
                                            FROM loan_repayments lr
                                            JOIN approved_loans al ON lr.approved_loan_id = al.id
                                            JOIN loan_applications la ON al.loan_application_id = la.id
                                            WHERE la.provider_type = 'bank'";
                                    $result = $app->select_one($query);
                                    $total_amount = ($result) ? number_format($result->total_amount, 2) : 0;
                                ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    KES <?php echo $total_amount ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Current Month Bank Repayments -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-calendar-check fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Bank Repaid This Month
                                                </p>
                                                <?php
                                  $query = "SELECT COUNT(*) as count, COALESCE(SUM(lr.amount), 0) as amount 
                                          FROM loan_repayments lr
                                          JOIN approved_loans al ON lr.approved_loan_id = al.id
                                          JOIN loan_applications la ON al.loan_application_id = la.id
                                          WHERE MONTH(lr.payment_date) = MONTH(CURRENT_DATE()) 
                                          AND YEAR(lr.payment_date) = YEAR(CURRENT_DATE())
                                          AND la.provider_type = 'bank'";
                                  $result = $app->select_one($query);
                                  $monthly_count = ($result) ? $result->count : 0;
                                  $monthly_amount = ($result) ? number_format($result->amount, 2) : 0;
                                ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php echo $monthly_count ?> <small class="text-muted">(KES
                                                        <?php echo $monthly_amount ?>)</small>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Active Bank Loans -->
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
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Active Bank Loans</p>
                                                <?php
                                                      $query = "SELECT COUNT(*) as count, COALESCE(SUM(al.remaining_balance), 0) as balance 
                                                               FROM approved_loans al
                                                               JOIN loan_applications la ON al.loan_application_id = la.id
                                                               WHERE al.status = 'active' AND la.provider_type = 'bank'";
                                                      $result = $app->select_one($query);
                                                      $active_loans = ($result) ? $result->count : 0;
                                                      $remaining_balance = ($result) ? number_format($result->balance, 2) : 0;
                                                 ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php echo $active_loans ?> <small class="text-muted">(KES
                                                        <?php echo $remaining_balance ?>)</small>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Repayment Section -->
                <div class="row mt-4">
                    <!-- Repayment History Section -->
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-header justify-content-between">
                                <div class="card-title">
                                    <i class="ri-history-line me-2"></i> Recent Repayments
                                </div>
                                <div class="btn-group">
                                    <button class="btn btn-outline-primary btn-sm"
                                        id="btnShowAllRepayments">All</button>
                                    <button class="btn btn-outline-success btn-sm" id="btnShowThisMonth">This
                                        Month</button>
                                    <button class="btn btn-outline-info btn-sm" id="btnShowLastMonth">Last
                                        Month</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="repaymentsSection">
                                    <div class="table-responsive">
                                        <table id="datatable-repayments" class="table table-bordered text-nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th><i class="ri-hash-line me-1"></i>ID</th>
                                                    <th><i class="ri-file-list-line me-1"></i>Loan</th>
                                                    <th><i class="ri-user-line me-1"></i>Farmer</th>
                                                    <th><i class="ri-money-dollar-circle-line me-1"></i>Amount</th>
                                                    <th><i class="ri-calendar-line me-1"></i>Date</th>
                                                    <th><i class="ri-bank-line me-1"></i>Method</th>
                                                    <th><i class="ri-settings-3-line me-1"></i>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Will be populated via AJAX -->
                                            </tbody>
                                        </table>
                                    </div>
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
    // Main script for loan repayments page
    $(document).ready(function() {
        // Load all repayments when page loads
        displayAllLoanRepayments();

        // Set up event listeners for filter buttons
        $('#btnShowAllRepayments').click(function() {
            displayAllLoanRepayments();
        });

        $('#btnShowThisMonth').click(function() {
            filterRepaymentsByMonth('current');
        });

        $('#btnShowLastMonth').click(function() {
            filterRepaymentsByMonth('last');
        });
    });

    // Function to display all loan repayments
    function displayAllLoanRepayments() {
        // Show loading message
        $('#repaymentsSection').html(
            '<div class="text-center p-5"><i class="ri-loader-4-line fa-spin fs-3"></i><p class="mt-2">Loading repayments...</p></div>'
        );

        $.ajax({
            url: "http://localhost/dfcs/ajax/loan-controller/display-all-bank-repayments.php",
            type: 'POST',
            data: {
                displayAllRepayments: "true"
            },
            success: function(data, status) {
                // Update the content with the AJAX response
                $('#repaymentsSection').html(data);

                console.log("Data loaded successfully");


            },
            error: function(xhr, status, error) {
                console.error("Error loading loan repayments:", error);
                $('#repaymentsSection').html(
                    '<div class="alert alert-danger"><i class="ri-error-warning-line me-1"></i> Failed to load loan repayments. Please try again.</div>'
                );

                toastr.error('Failed to load loan repayments', 'Error', {
                    "positionClass": "toast-top-right",
                    "progressBar": true,
                    "timeOut": 5000
                });
            }
        });
    }


    // Function to print repayment receipt
    function printRepaymentReceipt(repaymentId) {
        // Show loading message with toastr
        toastr.info('Preparing your receipt for download...', 'Please wait', {
            "positionClass": "toast-top-right",
            "progressBar": true,
            "timeOut": 0,
            "extendedTimeOut": 0,
            "closeButton": false,
            "hideMethod": "fadeOut"
        });

        // AJAX call to generate PDF
        $.ajax({
            url: "http://localhost/dfcs/ajax/loan-controller/generate-receipt-pdf.php",
            type: "POST",
            data: {
                repaymentId: repaymentId
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
                    let filename = 'Repayment_Receipt_REP' + String(repaymentId).padStart(5, '0') + '.pdf';
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

                    toastr.success('Receipt downloaded successfully', 'Success', {
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
                            toastr.error(errorJson.error || 'Failed to generate receipt',
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
                toastr.error('Failed to generate receipt. Please try again.', 'Error', {
                    "positionClass": "toast-top-right",
                    "progressBar": true,
                    "timeOut": 5000
                });
                console.error('Error generating PDF:', error);
            }
        });
    }

    // Function to filter repayments by month
    function filterRepaymentsByMonth(monthType) {
        let month, year;
        const now = new Date();

        if (monthType === 'current') {
            month = now.getMonth();
            year = now.getFullYear();
        } else if (monthType === 'last') {
            // Calculate last month
            if (now.getMonth() === 0) {
                month = 11;
                year = now.getFullYear() - 1;
            } else {
                month = now.getMonth() - 1;
                year = now.getFullYear();
            }
        }

        // Show loading message
        $('#repaymentsSection').html(
            '<div class="text-center p-5"><i class="ri-loader-4-line fa-spin fs-3"></i><p class="mt-2">Loading repayments...</p></div>'
        );

        $.ajax({
            url: "http://localhost/dfcs/ajax/loan-controller/display-all-repayments.php",
            type: 'POST',
            data: {
                filterMonth: month + 1, // Month is 0-indexed in JS, but 1-indexed in PHP
                filterYear: year
            },
            success: function(data, status) {
                // Update the content with the AJAX response
                $('#repaymentsSection').html(data);
                console.log("Filtered data loaded successfully");
            },
            error: function(xhr, status, error) {
                console.error("Error loading filtered repayments:", error);
                $('#repaymentsSection').html(
                    '<div class="alert alert-danger"><i class="ri-error-warning-line me-1"></i> Failed to load repayments. Please try again.</div>'
                );

                toastr.error('Failed to load repayments', 'Error', {
                    "positionClass": "toast-top-right",
                    "progressBar": true,
                    "timeOut": 5000
                });
            }
        });
    }
    </script>


</body>



</html>