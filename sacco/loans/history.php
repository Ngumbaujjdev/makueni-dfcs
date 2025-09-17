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
                        <span class="fs-semibold text-muted pt-5">Loan Management Dashboard</span>
                    </div>
                </div>

                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                    <h1 class="page-title fw-semibold fs-18 mb-0">Loan History</h1>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Loans</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Loan History</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <!-- SACCO Loan Stats Cards -->
                <div class="row mt-2">
                    <!-- Total SACCO Loans -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden" style="background:#6AA32D!important;">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded"
                                            style="background-color: white!important;">
                                            <i style="color:#6AA32D" class="fa-solid fa-folder-open fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-white mb-0">Total SACCO Loans</p>
                                                <?php
                                   $query = "SELECT COUNT(*) as count FROM loan_applications WHERE provider_type = 'sacco'";
                                   $result = $app->select_one($query);
                                   $total_loans = ($result) ? $result->count : 0;
                                ?>
                                                <h4 style="color:wheat;" class="fw-semibold mt-1">
                                                    <?php echo $total_loans ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Approved SACCO Loans -->
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
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Approved SACCO Loans
                                                </p>
                                                <?php
                                    $query = "SELECT COUNT(*) as count FROM loan_applications 
                                             WHERE (status = 'approved' OR status = 'disbursed' OR status = 'completed')
                                             AND provider_type = 'sacco'";
                                    $result = $app->select_one($query);
                                    $approved_loans = ($result) ? $result->count : 0;
                                    $approval_rate = ($total_loans > 0) ? round(($approved_loans / $total_loans) * 100, 1) : 0;
                                ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php echo $approved_loans ?> <small
                                                        class="text-muted">(<?php echo $approval_rate ?>%)</small>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rejected SACCO Loans -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-times-circle fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Rejected SACCO Loans
                                                </p>
                                                <?php
                                  $query = "SELECT COUNT(*) as count FROM loan_applications 
                                           WHERE status = 'rejected' AND provider_type = 'sacco'";
                                  $result = $app->select_one($query);
                                  $rejected_loans = ($result) ? $result->count : 0;
                                  $rejection_rate = ($total_loans > 0) ? round(($rejected_loans / $total_loans) * 100, 1) : 0;
                                ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php echo $rejected_loans ?> <small
                                                        class="text-muted">(<?php echo $rejection_rate ?>%)</small>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total SACCO Loan Amount -->
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
                                                <p class="text-muted mb-0" style="color:#6AA32D;">SACCO Loans Disbursed
                                                </p>
                                                <?php
                                                      $query = "SELECT COALESCE(SUM(al.approved_amount), 0) as total_amount 
                                                               FROM approved_loans al
                                                               INNER JOIN loan_applications la ON al.loan_application_id = la.id
                                                               WHERE la.provider_type = 'sacco'";
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
                </div>
                <!-- Monthly Loan Metrics -->
                <div class="row mt-4">
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-header">
                                <div class="card-title">
                                    <i class="ri-line-chart-line me-2"></i> Monthly Loan Metrics
                                </div>
                            </div>
                            <div class="card-body">
                                <?php include "../graphs/montly-distribution.php" ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- All Loans Section -->
                <div class="row mt-4">
                    <div class="col-xl-12">
                        <div id="allLoansSection">
                            <div class="card custom-card">
                                <div class="card-header justify-content-between">
                                    <div class="card-title">
                                        <i class="ri-file-list-3-line me-2"></i> All Loan Applications
                                    </div>
                                    <div class="btn-group">
                                        <button class="btn btn-outline-primary btn-sm" id="btnShowAll">All</button>
                                        <button class="btn btn-outline-success btn-sm"
                                            id="btnShowApproved">Approved</button>
                                        <button class="btn btn-outline-danger btn-sm"
                                            id="btnShowRejected">Rejected</button>
                                        <button class="btn btn-outline-warning btn-sm"
                                            id="btnShowPending">Pending</button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="datatable-all-loans" class="table table-bordered text-nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th><i class="ri-hash-line me-1"></i>Reference</th>
                                                    <th><i class="ri-user-line me-1"></i>Farmer</th>
                                                    <th><i class="ri-file-list-line me-1"></i>Loan Type</th>
                                                    <th><i class="ri-money-dollar-circle-line me-1"></i>Amount (KES)
                                                    </th>
                                                    <th><i class="ri-calendar-line me-1"></i>Term</th>
                                                    <th><i class="ri-bar-chart-line me-1"></i>Credit Score</th>
                                                    <th><i class="ri-time-line me-1"></i>Date</th>
                                                    <th><i class="ri-shield-check-line me-1"></i>Status</th>
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

                <!-- Loan Type Distribution -->
                <div class="row mt-4">
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-header">
                                <div class="card-title">
                                    <i class="ri-pie-chart-line me-2"></i> Loan Type Distribution
                                </div>
                            </div>
                            <div class="card-body">
                                <?php include "../graphs/loan-type-distribution.php" ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-header">
                                <div class="card-title">
                                    <i class="ri-bar-chart-grouped-line me-2"></i> Loan Status Distribution
                                </div>
                            </div>
                            <div class="card-body">
                                <?php include "../graphs/loan-status-distribution.php" ?>
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
    // Main script for loan history page
    $(document).ready(() => {
        // Load all loans
        displayAllLoanApplications();

        // Load charts data
        loadLoanMetricsData();
        loadLoanTypeDistribution();
        loadLoanStatusDistribution();
    });

    // Function to display all loan applications
    function displayAllLoanApplications() {
        let displayAllLoans = "true";
        $.ajax({
            url: "http://localhost/dfcs/ajax/loan-controller/display-all-loans.php",
            type: 'POST',
            data: {
                displayAllLoans: displayAllLoans,
            },
            success: function(data, status) {
                $('#allLoansSection').html(data);
            },
            error: function(xhr, status, error) {
                console.error("Error loading loan applications:", error);
                toastr.error('Failed to load loan applications', 'Error', {
                    "positionClass": "toast-top-right",
                    "progressBar": true,
                    "timeOut": 5000
                });
            }
        });
    }


    // Function to view loan details
    function viewLoanDetails(loanId) {
        window.location.href = "loan-details?id=" + loanId;
    }

    // Function to review loan application
    function reviewLoanApplication(loanId) {
        window.location.href = "review-loan?id=" + loanId;
    }

    // Function to print loan statement
    function printLoanStatement(loanId) {
        // Show loading message with toastr
        toastr.info('Preparing your loan statement for download...', 'Please wait', {
            "positionClass": "toast-top-right",
            "progressBar": true,
            "timeOut": 0,
            "extendedTimeOut": 0,
            "closeButton": false,
            "hideMethod": "fadeOut"
        });

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
    }
    </script>


</body>



</html>