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
                    <h1 class="page-title fw-semibold fs-18 mb-0">Bank Payment Reconciliation Report</h1>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Bank</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Payment Reconciliation</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <!-- Start::row-1 -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-header justify-content-between">
                                <div class="card-title">Generate Bank Payment Reconciliation Report</div>
                            </div>
                            <div class="card-body">
                                <form id="bank-reconciliation-report-form">
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <label for="start_date" class="form-label fw-semibold">Start Date</label>
                                            <input type="date" class="form-control" id="start_date" name="start_date"
                                                required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="end_date" class="form-label fw-semibold">End Date</label>
                                            <input type="date" class="form-control" id="end_date" name="end_date"
                                                required>
                                        </div>
                                    </div>

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
    <!-- JavaScript to initialize Form and handle logic -->
    <script>
    $(document).ready(function() {
        // Set default dates (current month)
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);

        // Format dates as YYYY-MM-DD
        const formatDate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        $('#start_date').val(formatDate(firstDay));
        $('#end_date').val(formatDate(today));

        // Download PDF Button Handler
        $('#downloadPDF').click(function() {
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();

            if (!startDate || !endDate) {
                toastr.error('Please select both start and end dates', 'Error', {
                    "positionClass": "toast-top-right",
                    "progressBar": true,
                    "timeOut": 3000
                });
                return;
            }

            if (new Date(startDate) > new Date(endDate)) {
                toastr.error('Start date must be before end date', 'Error', {
                    "positionClass": "toast-top-right",
                    "progressBar": true,
                    "timeOut": 3000
                });
                return;
            }

            // Show loading message
            toastr.info('Preparing bank payment reconciliation report for download...', 'Please wait', {
                "positionClass": "toast-top-right",
                "progressBar": true,
                "timeOut": 0,
                "extendedTimeOut": 0,
                "closeButton": false,
                "hideMethod": "fadeOut"
            });

            // AJAX call to generate PDF
            $.ajax({
                url: "http://localhost/dfcs/ajax/bank-controller/generate-reconciliation-report-pdf.php",
                type: "POST",
                data: {
                    start_date: startDate,
                    end_date: endDate
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
                        let filename =
                            `Bank_Payment_Reconciliation_Report_${startDate}_to_${endDate}.pdf`;
                        const contentDisposition = xhr.getResponseHeader(
                            'Content-Disposition');
                        if (contentDisposition) {
                            const filenameMatch = contentDisposition.match(
                                /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                            if (filenameMatch && filenameMatch[1]) {
                                filename = filenameMatch[1].replace(/['"]/g,
                                    '');
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

                        toastr.success(
                            'Bank payment reconciliation report downloaded successfully',
                            'Success', {
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
                                    'Failed to generate bank payment reconciliation report',
                                    'Error', {
                                        "positionClass": "toast-top-right",
                                        "progressBar": true,
                                        "timeOut": 5000
                                    });
                            };
                            reader.readAsText(response);
                        } catch (readError) {
                            toastr.error('Failed to process server response',
                                'Error', {
                                    "positionClass": "toast-top-right",
                                    "progressBar": true,
                                    "timeOut": 5000
                                });
                        }
                    }
                },
                error: function(xhr, status, error) {
                    toastr.clear();
                    toastr.error(
                        'Failed to generate bank payment reconciliation report. Please try again.',
                        'Error', {
                            "positionClass": "toast-top-right",
                            "progressBar": true,
                            "timeOut": 5000
                        });
                    console.error('Error generating Bank Payment Reconciliation PDF:',
                        error);
                }
            });
        });
    });
    </script>



</body>

</html>