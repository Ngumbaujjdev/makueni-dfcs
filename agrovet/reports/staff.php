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
    <link rel="stylesheet" href="http://localhost/dfcs/toast/toast.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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
                    <h1 class="page-title fw-semibold fs-18 mb-0">SACCO Operations Report</h1>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">SACCO</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Operations Report</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <!-- Start::row-1 -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-header justify-content-between">
                                <div class="card-title">Generate SACCO Operations Report</div>
                            </div>
                            <div class="card-body">
                                <form id="report-form">
                                    <div class="d-flex justify-content-end gap-2 mt-4">

                                        <button type="button" class="btn text-white" id="downloadPDF"
                                            style="background:#6AA32D;">
                                            <i class="ri-file-pdf-line me-1"></i> Download PDF
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End::row-1 -->
                <!-- Operations Report Overview Card -->
                <div class="row mt-4">
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-header justify-content-between">
                                <div class="card-title">Operations Report Overview</div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Infographic for Member Activity -->
                                    <div class="col-md-6 col-xl-3">
                                        <div class="card custom-card bg-info-transparent">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <span class="avatar bg-info">
                                                            <i class="ri-team-line fs-18"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 fs-12 op-7">Member Activity</p>
                                                        <h5 class="mb-0 fw-semibold">Farmers Registration and
                                                            Verification</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Infographic for Produce Processing -->
                                    <div class="col-md-6 col-xl-3">
                                        <div class="card custom-card bg-success-transparent">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <span class="avatar bg-success">
                                                            <i class="ri-plant-line fs-18"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 fs-12 op-7">Produce Processing</p>
                                                        <h5 class="mb-0 fw-semibold">Delivery and Payment Statistics
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Infographic for Loan Operations -->
                                    <div class="col-md-6 col-xl-3">
                                        <div class="card custom-card bg-warning-transparent">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <span class="avatar bg-warning">
                                                            <i class="ri-money-dollar-circle-line fs-18"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 fs-12 op-7">Loan Operations</p>
                                                        <h5 class="mb-0 fw-semibold">Application and Approval Metrics
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Infographic for Complete Report -->
                                    <div class="col-md-6 col-xl-3">
                                        <div class="card custom-card bg-primary-transparent">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <span class="avatar bg-primary">
                                                            <i class="ri-file-chart-line fs-18"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 fs-12 op-7">Complete Report</p>
                                                        <h5 class="mb-0 fw-semibold">Comprehensive Operational Analysis
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <p class="fw-semibold mb-2">Report Details:</p>
                                    <ul class="list-group">
                                        <li class="list-group-item">
                                            <strong>Summary Report:</strong> Provides a high-level overview of key
                                            operational metrics including member activity, produce processing status,
                                            and loan operations.
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Member Activity Report:</strong> Focuses on farmer registration
                                            trends, verification status, and membership growth over the selected period.
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Produce Processing Report:</strong> Details the status of produce
                                            deliveries, quality grades, and processing metrics for the selected period.
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Loan Operations Report:</strong> Analyzes loan application trends,
                                            approval rates, and performance by loan type for the selected period.
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Complete Operations Report:</strong> A comprehensive report that
                                            includes all sections for a complete operational overview.
                                        </li>
                                    </ul>
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

    <!-- Gallery JS -->
    <script src="http://localhost/dfcs/assets/libs/glightbox/js/glightbox.min.js"></script>

    <!-- Internal Profile JS -->
    <script src="http://localhost/dfcs/assets/js/profile.js"></script>

    <!-- Custom JS -->
    <script src="http://localhost/dfcs/assets/js/custom.js"></script>
    <!-- the toast -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    <!-- JavaScript to initialize Select2 and handle form logic -->
    <script>
    $(document).ready(function() {
        // View Report Button Handler
        $('#viewReport').click(function() {
            // Redirect to view report page
            window.location.href = `view-sacco-operations-report.php`;
        });

        // Download PDF Button Handler
        $('#downloadPDF').click(function() {
            // Show loading message
            toastr.info('Preparing SACCO operations report for download...', 'Please wait', {
                "positionClass": "toast-top-right",
                "progressBar": true,
                "timeOut": 0,
                "extendedTimeOut": 0,
                "closeButton": false,
                "hideMethod": "fadeOut"
            });

            // AJAX call to generate PDF
            $.ajax({
                url: "http://localhost/dfcs/ajax/sacco-controller/generate-operations-report-pdf.php",
                type: "POST",
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
                        let filename = 'SACCO_Operations_Report.pdf';
                        const contentDisposition = xhr.getResponseHeader(
                            'Content-Disposition');
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

                        toastr.success('Report downloaded successfully', 'Success', {
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
                                    'Failed to generate report', 'Error', {
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
                    toastr.error('Failed to generate report. Please try again.', 'Error', {
                        "positionClass": "toast-top-right",
                        "progressBar": true,
                        "timeOut": 5000
                    });
                    console.error('Error generating PDF:', error);
                }
            });
        });
    });
    </script>
</body>

</html>