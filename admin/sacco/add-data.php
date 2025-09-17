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
        <?php include "../../includes/sidebar.php" ?>
        <!-- End::app-sidebar -->
        <!-- Start::app-content -->
        <div class="main-content app-content">
            <div class="container-fluid">
                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
                    <h1 class="page-title fw-semibold fs-18 mb-0">Add SACCO Staff</h1>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">SACCO</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Add Staff</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <!-- Start::row-1 -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-header justify-content-between">
                                <div class="card-title">Add New SACCO Staff</div>
                            </div>
                            <div class="card-body add-products p-0">
                                <!-- Tabs Navigation -->
                                <ul class="nav nav-tabs" id="saccoTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" data-bs-toggle="tab"
                                            data-bs-target="#basic-info" type="button" role="tab">
                                            <i class="bi bi-info-circle me-1"></i>Personal Info
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#work-info"
                                            type="button" role="tab">
                                            <i class="bi bi-briefcase me-1"></i>Work Details
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#account-info"
                                            type="button" role="tab">
                                            <i class="bi bi-person-circle me-1"></i>Account Details
                                        </button>
                                    </li>
                                </ul>
                                <!-- Tab Content -->
                                <div class="tab-content p-4">
                                    <!-- Personal Info Tab -->
                                    <div class="tab-pane fade show active" id="basic-info" role="tabpanel">
                                        <div class="row gy-3">
                                            <div class="col-xl-6">
                                                <label class="form-label">First Name</label>
                                                <input type="text" class="form-control" id="first-name"
                                                    placeholder="Enter first name" required>
                                            </div>
                                            <div class="col-xl-6">
                                                <label class="form-label">Last Name</label>
                                                <input type="text" class="form-control" id="last-name"
                                                    placeholder="Enter last name" required>
                                            </div>
                                            <div class="col-xl-6">
                                                <label class="form-label">Phone Number</label>
                                                <input type="tel" class="form-control" id="phone"
                                                    placeholder="Enter phone number">
                                            </div>
                                            <div class="col-xl-6">
                                                <label class="form-label">Email</label>
                                                <input type="email" class="form-control" id="email"
                                                    placeholder="Enter email" required>
                                                <div style="color:red;" id="email-error"></div>
                                                <div style="color:green;" id="email-success"></div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end mt-3">
                                            <button class="btn text-white" id="nextBasic" style="background:#6AA32D;">
                                                Next <i class="bi bi-arrow-right ms-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <!-- Work Details Tab -->
                                    <div class="tab-pane fade" id="work-info" role="tabpanel">
                                        <div class="row gy-3">
                                            <div class="col-xl-6">
                                                <label class="form-label">Staff ID</label>
                                                <input type="text" class="form-control" id="staff-id"
                                                    placeholder="Enter staff ID" required>
                                            </div>
                                            <div class="col-xl-6">
                                                <label class="form-label">Position</label>
                                                <input type="text" class="form-control" id="position"
                                                    placeholder="Enter position" required>
                                            </div>
                                            <div class="col-xl-12">
                                                <label class="form-label">Department</label>
                                                <input type="text" class="form-control" id="department"
                                                    placeholder="Enter department">
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between mt-4">
                                            <button class="btn btn-light" id="prevWork">
                                                <i class="bi bi-arrow-left me-2"></i>Previous
                                            </button>
                                            <button class="btn text-white" id="nextWork" style="background:#6AA32D;">
                                                Next <i class="bi bi-arrow-right ms-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <!-- Account Details Tab -->
                                    <div class="tab-pane fade" id="account-info" role="tabpanel">
                                        <div class="row gy-3">
                                            <div class="col-xl-6">
                                                <label class="form-label">Username</label>
                                                <input type="text" class="form-control" id="username"
                                                    placeholder="Enter username" required>
                                            </div>
                                            <div class="col-xl-6">
                                                <label class="form-label">Password</label>
                                                <input type="password" class="form-control" id="password"
                                                    placeholder="Enter password" required>
                                            </div>
                                            <div class="col-xl-6">
                                                <label class="form-label">Confirm Password</label>
                                                <input type="password" class="form-control" id="confirm-password"
                                                    placeholder="Confirm password" required>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between mt-3">
                                            <button class="btn btn-light" id="prevAccount">
                                                <i class="bi bi-arrow-left me-2"></i>Previous
                                            </button>
                                            <button class="btn text-white" onclick="submitSaccoStaff()"
                                                style="background:#6AA32D;">
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
    <script>
    $(document).ready(function() {
        // Tab navigation
        $('#nextBasic').click(function() {
            $('#saccoTabs button[data-bs-target="#work-info"]').tab('show');
        });

        $('#prevWork').click(function() {
            $('#saccoTabs button[data-bs-target="#basic-info"]').tab('show');
        });

        $('#nextWork').click(function() {
            $('#saccoTabs button[data-bs-target="#account-info"]').tab('show');
        });

        $('#prevAccount').click(function() {
            $('#saccoTabs button[data-bs-target="#work-info"]').tab('show');
        });
        // Real-time email checking
        function checkEmailAvailability(email) {
            $.ajax({
                type: "POST",
                url: "http://localhost/dfcs/ajax/authentication-controller/check-email-availability.php",
                data: {
                    email: email
                },
                success: function(response) {
                    if (response === "available") {
                        $("#email-success").text("Email is available");
                        $("#email-error").text("");
                        $("#submitSacco").prop("disabled", false);
                    } else {
                        $("#email-error").text("Email is not available. Please try another email.");
                        $("#email-success").text("");
                        $("#submitSacco").prop("disabled", true);
                    }
                }
            });
        }
        // Listen to email input changes
        $("#email").on("input", function() {
            const email = $(this).val();
            if (email) {
                checkEmailAvailability(email);
            } else {
                $("#email-error").text("");
                $("#email-success").text("");
            }
        });
    });
    // submitSaccoStaff function to handle form submission
    function submitSaccoStaff() {
        // Get all form values
        const firstName = $("#first-name").val();
        const lastName = $("#last-name").val();
        const email = $("#email").val();
        const phone = $("#phone").val();
        const staffId = $("#staff-id").val();
        const position = $("#position").val();
        const department = $("#department").val();
        const username = $("#username").val();
        const password = $("#password").val();
        const confirmPassword = $("#confirm-password").val();

        // Validate all required fields
        if (firstName && lastName && email && phone && staffId &&
            position && department && username && password &&
            confirmPassword && password === confirmPassword) {

            let formData = new FormData();
            formData.append('first_name', firstName);
            formData.append('last_name', lastName);
            formData.append('email', email);
            formData.append('phone', phone);
            formData.append('staff_id', staffId);
            formData.append('position', position);
            formData.append('department', department);
            formData.append('username', username);
            formData.append('password', password);
            $.ajax({
                url: 'http://localhost/dfcs/ajax/sacco-controller/add-staff.php',
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
                                window.location.href =
                                    'http://localhost/dfcs/admin/sacco/view-data';
                            }, 2000);
                        } else {
                            toastr.error(data.message, 'Error', {
                                timeOut: 3000,
                                closeButton: true,
                                progressBar: true,
                                positionClass: "toast-top-right"
                            });
                        }
                    } catch (e) {
                        toastr.error('Error processing response', 'Error');
                    }
                },
                error: function() {
                    toastr.error('Server error occurred', 'Error');
                }
            });
        } else {
            // Show errors for empty fields
            if (!firstName) $("#first-name-error").text("Please enter first name");
            if (!lastName) $("#last-name-error").text("Please enter last name");
            if (!email) $("#email-error").text("Please enter email");
            if (!phone) $("#phone-error").text("Please enter phone number");
            if (!staffId) $("#staff-id-error").text("Please enter staff ID");
            if (!position) $("#position-error").text("Please enter position");
            if (!department) $("#department-error").text("Please enter department");
            if (!username) $("#username-error").text("Please enter username");
            if (!password) $("#password-error").text("Please enter password");
            if (!confirmPassword) $("#confirm-password-error").text("Please confirm password");
            if (password !== confirmPassword) $("#confirm-password-error").text("Passwords do not match");
            // Clear error messages after 3 seconds
            setTimeout(function() {
                $("[id$='-error']").text("");
            }, 3000);
        }
    }
    </script>
</body>

</html>