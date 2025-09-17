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
        <!-- End::app-sidebar -->
        <!-- Start::app-content -->
        <div class="main-content app-content">
            <div class="container-fluid">

                <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
                    <h1 class="page-title fw-semibold fs-18 mb-0">Update Agrovet Staff</h1>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Agrovets</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Update Staff</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <?php
                 if (isset($_GET['id'])) {
                     $app = new App;
                     $id = $_GET['id'];
                     // Updated query to join with users table
                     $query = "SELECT s.*, a.name as agrovet_name, u.first_name, u.last_name, u.email
                               FROM agrovet_staff s
                               LEFT JOIN agrovets a ON s.agrovet_id = a.id
                               INNER JOIN users u ON s.user_id = u.id
                               WHERE s.id = :id";
                     $staff = $app->selectOne($query, [':id' => $id]);
                 ?>
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-header justify-content-between">
                                <div class="card-title">Update Staff Details</div>
                            </div>
                            <div class="card-body">
                                <input type="hidden" id="staff-id" value="<?php echo $staff->id ?>">

                                <div class="row gy-3">
                                    <div class="col-xl-12">
                                        <label class="form-label">Agrovet</label>
                                        <input type="text" class="form-control"
                                            value="<?php echo $staff->agrovet_name ?>" readonly>
                                    </div>
                                    <div class="col-xl-6">
                                        <label class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="first-name"
                                            value="<?php echo $staff->first_name ?>" required>
                                    </div>
                                    <div class="col-xl-6">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="last-name"
                                            value="<?php echo $staff->last_name ?>" required>
                                    </div>
                                    <div class="col-xl-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email"
                                            value="<?php echo $staff->email ?>" required>
                                    </div>
                                    <div class="col-xl-6">
                                        <label class="form-label">Position</label>
                                        <input type="text" class="form-control" id="position"
                                            value="<?php echo $staff->position ?>" required>
                                    </div>
                                    <div class="col-xl-6">
                                        <label class="form-label">Employee Number</label>
                                        <input type="text" class="form-control" id="employee-number"
                                            value="<?php echo $staff->employee_number ?>" required>
                                    </div>
                                    <div class="col-xl-6">
                                        <label class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone"
                                            value="<?php echo $staff->phone ?>" required>
                                    </div>
                                    <div class="col-xl-6">
                                        <label class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="password"
                                            placeholder="Leave blank to keep current password">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-4">
                                    <button class="btn text-white" onclick="updateAgrovetStaff()"
                                        style="background:#6AA32D;">
                                        Update <i class="bi bi-check-lg ms-2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>


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
    <!-- JavaScript for the page -->
    <script>
    function updateAgrovetStaff() {
        // Get form values
        const formData = new FormData();
        formData.append('id', $('#staff-id').val());
        formData.append('first_name', $('#first-name').val());
        formData.append('last_name', $('#last-name').val());
        formData.append('email', $('#email').val());
        formData.append('position', $('#position').val());
        formData.append('employee_number', $('#employee-number').val());
        formData.append('phone', $('#phone').val());
        formData.append('password', $('#password').val());

        // Validate required fields
        if (!$('#first-name').val() || !$('#last-name').val() ||
            !$('#email').val() || !$('#position').val() ||
            !$('#employee-number').val() || !$('#phone').val()) {
            toastr.error('Please fill in all required fields', 'Error');
            return;
        }
        // Validate email format
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test($('#email').val())) {
            toastr.error('Please enter a valid email address', 'Error');
            return;
        }
        // Validate phone number format
        const phoneRegex = /^[0-9]{10}$/;
        if (!phoneRegex.test($('#phone').val())) {
            toastr.error('Please enter a valid 10-digit phone number', 'Error');
            return;
        }
        // Sending the AJAX request
        toastr.info('Updating staff details, please wait...', 'Processing', {
            timeOut: 0,
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right"
        });
        $.ajax({
            url: 'http://localhost/dfcs/ajax/agrovet-controller/update-staff.php',
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
                                'http://localhost/dfcs/admin/agrovet-staff/view-data';
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