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
</head>

<body>
    <?php include "../../includes/loader.php" ?>

    <div class="page">
        <!-- app-header -->
        <?php include "../../includes/navigation.php" ?>
        <!-- /app-header -->
        <!-- Start::app-sidebar -->
        <?php include "../../includes/sidebar.php" ?>
        <div class="main-content app-content">
            <div class="container-fluid">
                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
                    <h1 class="page-title fw-semibold fs-18 mb-0">Add Bank</h1>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Banks</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Add Bank</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <!-- Start::row-1 -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-header justify-content-between">
                                <div class="card-title">Add New Bank</div>
                            </div>
                            <div class="card-body">
                                <div class="row gy-3">
                                    <div class="col-xl-6">
                                        <label class="form-label">Bank Name</label>
                                        <input type="text" class="form-control" id="bank-name"
                                            placeholder="Enter bank name" required>
                                    </div>
                                    <div class="col-xl-6">
                                        <label class="form-label">Branch Name</label>
                                        <input type="text" class="form-control" id="branch"
                                            placeholder="Enter branch name" required>
                                    </div>
                                    <div class="col-xl-6">
                                        <label class="form-label">Location</label>
                                        <input type="text" class="form-control" id="location"
                                            placeholder="Enter bank location">
                                    </div>
                                    <div class="col-xl-6">
                                        <label class="form-label">Phone Number</llabel>
                                            <input type="tel" class="form-control" id="phone"
                                                placeholder="Enter phone number" required>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-4">
                                    <button class="btn text-white" onclick="submitBank()" style="background:#6AA32D;">
                                        Submit <i class="bi bi-check-lg ms-2"></i>
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
    <script>
    function submitBank() {
        // Get form values
        const bankName = $("#bank-name").val();
        const branch = $("#branch").val();
        const location = $("#location").val();
        const phone = $("#phone").val();

        // Validate required fields
        if (!bankName || !branch || !phone) {
            toastr.error('Please fill in all required fields', 'Error');
            return;
        }

        // Create form data
        let formData = new FormData();
        formData.append('name', bankName);
        formData.append('branch', branch);
        formData.append('location', location);
        formData.append('phone', phone);

        // Submit data
        $.ajax({
            url: 'http://localhost/dfcs/ajax/bank-controller/add-bank.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                try {
                    const data = JSON.parse(response);
                    if (data.success) {
                        toastr.success(data.message, 'Success', {
                            timeOut: 3000,
                            closeButton: true,
                            progressBar: true,
                            positionClass: "toast-top-right"
                        });
                        setTimeout(() => {
                            window.location.href = 'http://localhost/dfcs/admin/banks/view-data';
                        }, 2000);
                    } else {
                        toastr.error(data.message, 'Error');
                    }
                } catch (e) {
                    toastr.error('Error processing response', 'Error');
                }
            },
            error: function() {
                toastr.error('Server error occurred', 'Error');
            }
        });
    }
    </script>

</body>

</html>