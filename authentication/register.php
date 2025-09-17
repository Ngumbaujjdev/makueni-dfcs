<?php include "../config/config.php" ?>
<?php include "../libs/App.php" ?>
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
                        <p class="h5 fw-semibold mb-2">Sign Up</p>
                        <p class="mb-3 text-muted op-7 fw-normal">Welcome & Join us by creating an account at Makueni
                            DFCS!</p>

                        <div class="row gy-3" id="#registrationForm">
                            <div class="col-xl-12 mt-0">
                                <label for="signup-firstname" class="form-label text-default">First Name</label>
                                <input type="text" class="form-control form-control-lg" id="signup-firstname"
                                    placeholder="First name">
                                <div style="color:red;" id="firstname-error"></div>
                            </div>

                            <div class="col-xl-12">
                                <label for="signup-lastname" class="form-label text-default">Last Name</label>
                                <input type="text" class="form-control form-control-lg" id="signup-lastname"
                                    placeholder="Last name">
                                <div style="color:red;" id="lastname-error"></div>
                            </div>

                            <div class="col-xl-12">
                                <label for="signup-email" class="form-label text-default">Email</label>
                                <input type="email" class="form-control form-control-lg" id="signup-email"
                                    placeholder="Email">
                                <div style="color:red;" id="email-error"></div>
                                <div style="color:green;" id="email-success"></div>
                            </div>

                            <div class="col-xl-12">
                                <label for="signup-phone" class="form-label text-default">Phone Number</label>
                                <input type="tel" class="form-control form-control-lg" id="signup-phone"
                                    placeholder="Phone Number">
                                <div style="color:red;" id="phone-error"></div>
                            </div>

                            <div class="col-xl-12">
                                <label for="signup-location" class="form-label text-default">Location in Makueni</label>
                                <input type="text" class="form-control form-control-lg" id="signup-location"
                                    placeholder="Your location">
                                <div style="color:red;" id="location-error"></div>
                            </div>

                            <div class="col-xl-12">
                                <label for="signup-password" class="form-label text-default">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control form-control-lg password"
                                        id="signup-password" placeholder="Password">
                                    <button class="btn btn-light" onclick="createpassword('signup-password',this)"
                                        type="button" id="button-addon2">
                                        <i class="ri-eye-off-line align-middle"></i>
                                    </button>
                                </div>
                                <div style="color:red;" id="password-error"></div>
                            </div>

                            <div class="col-xl-12 mb-3">
                                <label for="signup-confirmpassword" class="form-label text-default">Confirm
                                    Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control form-control-lg password2"
                                        id="signup-confirmpassword" placeholder="Confirm password">
                                    <button class="btn btn-light"
                                        onclick="createpassword('signup-confirmpassword',this)" type="button"
                                        id="button-addon21">
                                        <i class="ri-eye-off-line align-middle"></i>
                                    </button>
                                </div>
                                <div style="color:red;" id="password2-error"></div>
                                <div class="form-check mt-3">
                                    <input class="form-check-input" type="checkbox" value="" id="defaultCheck1"
                                        required>
                                    <label class="form-check-label text-muted fw-normal" for="defaultCheck1">
                                        By creating an account you agree to our <a href="javascript:void(0);"
                                            class="text-success"><u>Terms & Conditions</u></a>
                                        and <a href="javascript:void(0);" class="text-success"><u>Privacy Policy</u></a>
                                    </label>
                                </div>
                            </div>

                            <div class="col-xl-12 d-grid mt-2">
                                <button class="btn btn-lg text-white" id="registerButton" onclick="RegisterFarmer()"
                                    style="background-color:#6EA12F!important;" disabled>Create Account</button>
                            </div>
                        </div>
                        <div class="text-center">
                            <p class="fs-12 text-muted mt-4">Already have an account? <a href="http://localhost/dfcs/"
                                    style="color: #461F06!important; font-weight:bold!important;">Sign In</a></p>
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
                    <h5 class="modal-title" style="color:white!important;" id="successModalLabel">Registered succesfully
                    </h5>
                    <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span style="color:white!important;" aria-hidden="true">&times;</span>
                    </button> -->
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Second column with content -->
                        <div class="col-md-6 col-sm-12" style="margin-top:5%;">
                            <h5 style="font-weight:bold;">Success!</h5>
                            <p>Your account has been registered succesfully.Redirecting in a few seconds.....</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="http://localhost/dfcs/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Swiper JS -->
    <script src="http://localhost/dfcs/assets/libs/swiper/swiper-bundle.min.js"></script>

    <!-- Internal Sing-Up JS -->
    <script src="http://localhost/dfcs/assets/js/authentication.js"></script>

    <!-- Show Password JS -->
    <script src="http://localhost/dfcs/assets/js/show-password.js"></script>
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
                if (response === "available") {
                    $("#email-success").text("Email is available");
                    $("#email-error").text(""); // Clear the error message
                    $("#registerButton").prop("disabled", false);

                } else {
                    $("#email-error").text("Email is not available. Please try another email.");
                    $("#email-success").text(""); // Clear the success message
                    $("#registerButton").prop("disabled", true);

                }

            }
        });
    }

    // Listen to the email input field for changes
    $("#signup-email").on("input", function() {
        const email = $(this).val();
        if (email) {
            checkEmailAvailability(email);
        }
    });
    // the function to handle the registration
    function RegisterFarmer() {
        const firstname = $("#signup-firstname").val();
        const lastname = $("#signup-lastname").val();
        const email = $("#signup-email").val();
        const phone = $("#signup-phone").val();
        const location = $("#signup-location").val();
        const password = $("#signup-password").val();
        const confirmPassword = $("#signup-confirmpassword").val();

        if (firstname && lastname && email && phone && location && password &&
            confirmPassword && password === confirmPassword) {

            let formData = new FormData();
            formData.append('firstname', firstname);
            formData.append('lastname', lastname);
            formData.append('email', email);
            formData.append('phone', phone);
            formData.append('location', location);
            formData.append('password', password);

            $.ajax({
                url: "../ajax/authentication-controller/register-farmer.php",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    const result = JSON.parse(response);
                    if (result.status === "success") {
                        toastr.success('Registration successful!');
                        setTimeout(function() {
                            window.location.href =
                                'http://localhost/dfcs/farmers/dashboard/overview';
                        }, 1500);
                    } else {
                        toastr.error(result.message || 'Registration failed');
                    }
                },
                error: function() {
                    toastr.error('Registration failed. Please try again.');
                }
            });
        } else {
            if (!firstname) $("#firstname-error").text("Please enter first name");
            if (!lastname) $("#lastname-error").text("Please enter last name");
            if (!email) $("#email-error").text("Please enter email");
            if (!phone) $("#phone-error").text("Please enter phone number");
            if (!location) $("#location-error").text("Please enter location");
            if (!password) $("#password-error").text("Please enter password");
            if (!confirmPassword) $("#password2-error").text("Please confirm password");
            if (password !== confirmPassword) $("#password2-error").text("Passwords do not match");

            setTimeout(function() {
                $("[id$='-error']").text("");
            }, 3000);
        }
    }
    </script>


</body>


</html>