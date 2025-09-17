<?php include "./config/config.php" ?>
<?php include "./libs/App.php" ?>
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
    <script src="http://localhost/dfcs/assets/js/authentication-main.js"></script>
    <!-- Bootstrap Css -->
    <link id="style" href="http://localhost/dfcs/assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Style Css -->
    <link href="http://localhost/dfcs/assets/css/styles.min.css" rel="stylesheet">
    <!-- Icons Css -->
    <link href="http://localhost/dfcs/assets/css/icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="http://localhost/dfcs/toast/toast.css">

</head>

<body class="bg-white">
    <div class="row authentication mx-0">
        <div class="col-xxl-7 col-xl-7 col-lg-12">
            <div class="row justify-content-center align-items-center h-100">
                <div class="col-xxl-6 col-xl-7 col-lg-7 col-md-7 col-sm-8 col-12">
                    <div class="p-5">
                        <div class="mb-3">
                            <a href="http://localhost/dfcs/">
                                <img src="http://localhost/dfcs/assets/images/brand-logos/logo3.png" alt=""
                                    class="authentication-brand desktop-logo">
                                <img src="http://localhost/dfcs/assets/images/brand-logos/logo3.png" alt=""
                                    class="authentication-brand desktop-dark">
                            </a>
                        </div>
                        <p class="h5 fw-semibold mb-2">Sign In</p>
                        <p class="mb-3 text-muted op-7 fw-normal">Welcome back to Makueni DFCS</p>
                        <div class="row gy-3" id="loginForm">
                            <div class="col-xl-12">
                                <label for="signup-email" class="form-label text-default">Email</label>
                                <input type="email" class="form-control form-control-lg" id="signup-email"
                                    placeholder="email">
                                <div style="color:red;" id="email-error"></div>
                                <div style="color:green;" id="email-success"></div>

                            </div>
                            <div class="col-xl-12 mb-3">
                                <label for="signin-password" class="form-label text-default d-block">Password<a
                                        href="http://localhost/dfcs/authentication/forgot.php?forgot=<?php echo uniqid(true); ?>"
                                        class="float-end"
                                        style="color: #461F06!important; font-weight:bold!important;">Forget
                                        password
                                        ?</a></label>
                                <div class="input-group">
                                    <input type="password" class="form-control form-control-lg" id="signin-password"
                                        placeholder="password">
                                    <button class="btn btn-light" type="button"
                                        onclick="createpassword('signin-password',this)" id="button-addon2"><i
                                            class="ri-eye-off-line align-middle"></i></button>
                                </div>
                                <div style="color:red;" id="password-error"></div>


                            </div>
                            <div class="col-xl-12 d-grid mt-2">
                                <button class="btn btn-lg text-white" id="loginButton" onclick="loginAdmin()"
                                    style="background-color:#6EA12F!important;" disabled>Sign
                                    In</button>
                                <div id="response" style="color:red;font-weight:bold;"></div>
                            </div>
                            <div class=" text-center">
                                <p class="fs-12 text-muted mt-4">Dont have an account? <a
                                        href="http://localhost/dfcs/authentication/register"
                                        style="color: #461F06;">Sign Up</a></p>
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
    <?php include "./includes/signin-footer-links.php" ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
    function checkEmailAvailability(email) {
        $.ajax({
            type: "POST",
            url: "./ajax/authentication-controller/check-email-availability.php",
            data: {
                email: email
            },
            success: function(response) {
                if (response === "not_available") {
                    $("#email-success").text("Email Exists");
                    $("#email-error").text(""); // Clear the error message
                    $("#loginButton").prop("disabled", false);
                } else {
                    $("#email-error").text("Email does not Exist");
                    $("#email-success").text(""); // Clear the success message
                    $("#loginButton").prop("disabled", true);

                }

            }
        });
    }


    $("#signup-email").on("input", function() {
        const email = $(this).val();
        if (email) {
            checkEmailAvailability(email);
        }
    });

    function loginAdmin() {
        const email = $("#signup-email").val();
        const password = $("#signin-password").val();

        if (email && password) {
            let formData = new FormData();
            formData.append('email', email);
            formData.append('password', password);

            $.ajax({
                url: "http://localhost/dfcs/ajax/authentication-controller/login-admin.php",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    const result = JSON.parse(response);

                    if (result.status === "success") {
                        // Show success toast
                        toastr.success('Login successful!', 'Success', {
                            timeOut: 1500,
                            closeButton: true,
                            progressBar: true,
                            positionClass: 'toast-top-right'
                        });

                        // Redirect based on role_id after toast
                        setTimeout(function() {
                            switch (parseInt(result.role_id)) {
                                case 1:
                                    window.location.href =
                                        'http://localhost/dfcs/farmers/dashboard/overview';
                                    break;
                                case 2:
                                    window.location.href =
                                        'http://localhost/dfcs/sacco/dashboard/system';
                                    break;
                                case 3:
                                    window.location.href =
                                        'http://localhost/dfcs/bank/dashboard/overview';
                                    break;
                                case 4:
                                    window.location.href =
                                        'http://localhost/dfcs/agrovet/dashboard/overview';
                                    break;
                                case 5:
                                    window.location.href =
                                        'http://localhost/dfcs/admin/dashboard/system';
                                    break;
                                default:
                                    toastr.error('Invalid user role', 'Error');
                            }
                        }, 1500);

                        // Clear form
                        $('#signup-email').val('');
                        $('#signin-password').val('');
                    } else {
                        toastr.error('Incorrect email or password', 'Error');
                    }
                },
                error: function() {
                    toastr.error('Login failed. Please try again.', 'Error');
                }
            });
        } else {
            if (!password) {
                $("#password-error").text("Please enter your password");
            }
            if (!email) {
                $("#email-error").text("Please enter your email");
            }
            // Clear errors after 3 seconds
            setTimeout(function() {
                $("#password-error").text("");
                $("#email-error").text("");
            }, 3000);
        }
    }
    </script>