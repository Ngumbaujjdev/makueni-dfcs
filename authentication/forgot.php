<?php include "../config/config.php" ?>
<?php include "../libs/App.php" ?>
<?php
if (!isset($_GET['forgot'])) {
    echo " <script>window.location.href='http://localhost/dfcs/'</script>";
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-vertical-style="overlay" data-theme-mode="light"
    data-header-styles="light" data-menu-styles="light" data-toggled="close">

<head>

    <!-- Meta Data -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Forgot</title>
    <meta name="Description" content="Bootstrap Responsive Admin Web Dashboard Template">
    <meta name="Author" content="Spruko Technologies Private Limited">
    <meta name="keywords"
        content="blazor bootstrap, c# blazor, admin panel, blazor c#, template dashboard, admin, bootstrap admin template, blazor, blazorbootstrap, bootstrap 5 templates, dashboard, dashboard template bootstrap, admin dashboard bootstrap.">
    <!-- Place favicon.ico in the root directory -->
    <link rel="icon" type="image/png" href="http://localhost/dfcs/assets/images/favicon/favicon-96x96.png"
        sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="http://localhost/dfcs/assets/images/favicon/favicon.svg" />
    <link rel="shortcut icon" href="http://localhost/dfcs/assets/images/favicon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180"
        href="http://localhost/dfcs/assets/images/favicon/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Baituti Adventures" />
    <script src="http://localhost/dfcs/assets/js/authentication-main.js"></script>
    <!-- Bootstrap Css -->
    <link id="style" href="http://localhost/dfcs/assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Style Css -->
    <link href="http://localhost/dfcs/assets/css/styles.min.css" rel="stylesheet">
    <!-- Icons Css -->
    <link href="http://localhost/dfcs/assets/css/icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/swiper/swiper-bundle.min.css">

</head>

<body class="bg-white">

    <!-- Start Switcher -->

    <!-- End Switcher -->

    <div class="row authentication mx-0">

        <div class="col-xxl-7 col-xl-7 col-lg-12">
            <div class="row justify-content-center align-items-center h-100">
                <div class="col-xxl-6 col-xl-7 col-lg-7 col-md-7 col-sm-8 col-12">
                    <div class="p-5">
                        <div class="mb-3">
                            <a href="http://localhost/dfcs/">
                                <img style="display: none!important;"
                                    src="../assets/images/brand-logos/desktop-logo.png" alt=""
                                    class="authentication-brand desktop-logo">
                                <img style="display: none!important;"
                                    src="../assets/images/brand-logos/desktop-dark.png" alt=""
                                    class="authentication-brand desktop-dark">
                            </a>
                        </div>
                        <p class="h5 fw-semibold mb-2">Forgot Password</p>
                        <p class="mb-5 text-muted op-7 fw-normal">Enter your email to Reset your password</p>

                        <div class="row gy-3">
                            <div class="col-xl-12">
                                <label for="signup-email" class="form-label text-default">Email</label>
                                <input type="email" class="form-control form-control-lg" id="signup-email"
                                    placeholder="email">
                                <div style="color:red;" id="email-error"></div>
                                <div style="color:green;" id="email-success"></div>

                            </div>
                            <div class="col-xl-12 d-grid mt-4">
                                <button class="btn btn-lg text-white" id="emailButton" onclick="sendEmail()"
                                    style="background-color: #6AA32D!important;" disabled>Send
                                    Email Reset</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-5 col-xl-5 col-lg-5 d-xl-block d-none px-0">
            <div class="authentication-cover">
                <div class="aunthentication-cover-content rounded">
                    <div class="swiper keyboard-control">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <div
                                    class="text-fixed-white text-center p-5 d-flex align-items-center justify-content-center">
                                    <div>
                                        <div class="mb-5">
                                            <img src="http://localhost/dfcs/assets/images/authentication/2.png"
                                                class="authentication-image" alt="">
                                        </div>
                                        <h6 class="fw-semibold text-fixed-white">Sign In</h6>
                                        <p class="fw-normal fs-14 op-7">Welcome to Makueni Distributed Farmers
                                            Cooperative System
                                            dashboard. Sign in to manage your fruit deliveries, loan applications, and
                                            agricultural
                                            inputs. Experience a streamlined system designed for Makueni's farming
                                            community.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div
                                    class="text-fixed-white text-center p-5 d-flex align-items-center justify-content-center">
                                    <div>
                                        <div class="mb-5">
                                            <img src="http://localhost/dfcs/assets/images/authentication/3.png"
                                                class="authentication-image" alt="">
                                        </div>
                                        <h6 class="fw-semibold text-fixed-white">Sign In</h6>
                                        <p class="fw-normal fs-14 op-7">Access your farming management hub with ease.
                                            Sign in to track produce deliveries, monitor loan status, and stay connected
                                            with Kilimo SACCO services. Your gateway to efficient agricultural
                                            operations starts
                                            here.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div
                                    class="text-fixed-white text-center p-5 d-flex align-items-center justify-content-center">
                                    <div>
                                        <div class="mb-5">
                                            <img src="http://localhost/dfcs/assets/images/authentication/2.png"
                                                class="authentication-image" alt="">
                                        </div>
                                        <h6 class="fw-semibold text-fixed-white">Sign In</h6>
                                        <p class="fw-normal fs-14 op-7">Step into your agricultural management workspace
                                            at
                                            Makueni DFCS. Sign in to access tools that help you manage your mango,
                                            orange, and pixie
                                            fruit farming operations. Your dashboard for successful farming experiences
                                            awaits.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background:#6AA32D!important;">
                    <h5 class="modal-title" style="color:white!important;" id="successModalLabel">Email Sent succesifuly
                    </h5>

                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Second column with content -->
                        <div class="col-md-6 col-sm-12" style="margin-top:5%;">
                            <h5 style="font-weight:bold;">Success!</h5>
                            <p>Email has been send successfully to reset your password!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="../assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Swiper JS -->
    <script src="../assets/libs/swiper/swiper-bundle.min.js"></script>

    <!-- Internal Sing-Up JS -->
    <script src="../assets/js/authentication.js"></script>

    <!-- Show Password JS -->
    <script src="../assets/js/show-password.js"></script>

</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
    integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
function checkEmailAvailability(email) {
    $.ajax({
        type: "POST",
        url: "../ajax/authentication-controller/check-email-availability.php",
        data: {
            email: email
        },
        success: function(response) {
            if (response === "not_available") {
                $("#email-success").text("Email Exists");
                $("#email-error").text(""); // Clear the error message
                $("#emailButton").prop("disabled", false);
            } else {
                $("#email-error").text("Email does not Exists");
                $("#email-success").text(""); // Clear the success message
                $("#emailButton").prop("disabled", true);

            }

        }
    });
}

// Listen to the email input field for changes
// $("Listen to the email input field for changes
$("#signup-email").on("input", function() {
    const email = $(this).val();
    if (email) {
        checkEmailAvailability(email);
    }
});
// register user
function sendEmail() {
    const email = $("#signup-email").val();
    if (email) {
        let formData = new FormData();
        // Append other form data to FormData
        formData.append('email', email);
        $.ajax({
            url: "../ajax/authentication-controller/send-email.php",
            type: 'POST',
            data: formData,
            contentType: false, // Important! Prevent jQuery from setting the content type
            processData: false, // Important! Prevent jQuery from processing the data
            success: function(data, status) {
                $('#successModal').modal('show');
                $('#signup-email').val('');
                $("#email-success").text("");

                // Hide the modal after 10 seconds
                setTimeout(function() {
                    $('#successModal').modal('hide');
                }, 10000);

            }

        });
    } else {
        $("#email-error").text("Please Input email");
        $("#emailButton").prop("disabled", true);
        // Reload the page after 3000 milliseconds (3 seconds)
        setTimeout(function() {
            location.reload();
        }, 3000);
    }
}
</script>

</html>