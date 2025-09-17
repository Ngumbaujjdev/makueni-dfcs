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
    <title>Forgot Reset </title>
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
<?php if (!isset($_GET['forgot'])) {
    echo "<script>window.location.href='http://localhost/dfcs/'</script>";
} ?>

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
                        <p class="h5 fw-semibold mb-2">Create Password</p>
                        <?php
                         $app = new App;
                         $token = $_GET['forgot'];
                         $query = "SELECT * FROM users WHERE reset_token='{$token}'";
                         $users = $app->select_all($query);
                         ?>
                        <?php foreach ($users as $user): ?>
                        <p class="mb-4 text-muted op-7 fw-normal">Hello <?php echo $user->first_name; ?> reset
                            your password !</p>
                        <?php endforeach; ?>

                        <div class="row gy-3">
                            <div class="col-xl-12 mt-0">
                                <label for="create-password" class="form-label text-default">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control form-control-lg" id="create-password"
                                        placeholder="password">
                                    <button onclick="createpassword('create-password',this)" class="btn btn-light"
                                        type="button"><i class="ri-eye-off-line align-middle"></i></button>
                                </div>
                                <div style="color:red;" id="password-error"></div>
                            </div>
                            <div class="col-xl-12 mb-3">
                                <label for="create-confirmpassword" class="form-label text-default">Confirm
                                    Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control form-control-lg"
                                        id="create-confirmpassword" placeholder="password">
                                    <button onclick="createpassword('create-confirmpassword',this)"
                                        class="btn btn-light" type="button"><i
                                            class="ri-eye-off-line align-middle"></i></button>
                                </div>
                                <div style="color:red;" id="password2-error"></div>
                            </div>
                            <div class="col-xl-12 d-grid mt-2">
                                <button class="btn btn-lg text-white" onclick="resetPassword()"
                                    style="background-color:#6AA32D!important;">Save
                                    Password</button>
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

    <!-- Bootstrap JS -->
    <script src="../assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Swiper JS -->
    <script src="../assets/libs/swiper/swiper-bundle.min.js"></script>

    <!-- Internal Authentication JS -->
    <script src="../assets/js/authentication.js"></script>

    <!-- Show Password JS -->
    <script src="../assets/js/show-password.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
    function resetPassword() {
        const password = $("#create-password").val();
        const password2 = $("#create-confirmpassword").val();

        if (password && password2 && password === password2) {
            const token = '<?php echo $_GET['forgot'] ?>';

            let formData = new FormData();
            formData.append('password', password);
            formData.append('token', token);

            $.ajax({
                url: "../ajax/authentication-controller/reset-password.php",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    try {
                        const result = JSON.parse(response);

                        if (result.success) {
                            // Show success toast
                            toastr.success('Password reset successful! Redirecting...', 'Success', {
                                timeOut: 2000,
                                closeButton: true,
                                progressBar: true,
                                positionClass: 'toast-top-right'
                            });

                            // Redirect based on user role after toast
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
                                        window.location.href = 'http://localhost/dfcs/';
                                }
                            }, 2000);

                            // Clear form
                            $('#create-password').val('');
                            $('#create-confirmpassword').val('');
                        } else {
                            toastr.error(result.message || 'Password reset failed', 'Error');
                        }
                    } catch (e) {
                        toastr.error('Password reset failed. Please try again.', 'Error');
                    }
                },
                error: function() {
                    toastr.error('Password reset failed. Please try again.', 'Error');
                }
            });
        } else {
            if (!password) {
                $("#password-error").text("Please input password");
            }
            if (!password2) {
                $("#password2-error").text("Please confirm password");
            }
            if (password !== password2) {
                $("#password2-error").text("Please enter matching passwords");
            }

            $('#create-password').val('');
            $('#create-confirmpassword').val('');
            setTimeout(function() {
                location.reload();
            }, 1500);
        }
    }
    </script>


</body>

</html>