<?php include "../../config/config.php" ?>
<?php include "../../libs/App.php" ?>
<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light"
    data-menu-styles="dark" data-toggled="close">

<head>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="http://localhost/dfcs/assets/images/favicon/favicon-96x96.png"
        sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="http://localhost/dfcs/assets/images/favicon/favicon.svg" />
    <link rel="shortcut icon" href="http://localhost/dfcs/assets/images/favicon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180"
        href="http://localhost/dfcs/assets/images/favicon/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Baituti Adventures" />
    <link rel="manifest" href="http://localhost/dfcs/assets/images/favicon/site.webmanifest" />

    <!-- Choices JS -->
    <script src="http://localhost/dfcs/assets/libs/choices.js/public/assets/scripts/choices.min.js">
    </script>

    <!-- Main Theme Js -->
    <script src="http://localhost/dfcs/assets/js/main.js"></script>

    <!-- Bootstrap Css -->
    <link id="style" href="http://localhost/dfcs/assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Style Css -->
    <link href="http://localhost/dfcs/assets/css/styles.min.css" rel="stylesheet" />

    <!-- Icons Css -->
    <link href="http://localhost/dfcs/assets/css/icons.css" rel="stylesheet" />

    <!-- Node Waves Css -->
    <link href="http://localhost/dfcs/assets/libs/node-waves/waves.min.css" rel="stylesheet" />

    <!-- Simplebar Css -->
    <link href="http://localhost/dfcs/assets/libs/simplebar/simplebar.min.css" rel="stylesheet" />

    <!-- Color Picker Css -->
    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/flatpickr/flatpickr.min.css" />
    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/%40simonwep/pickr/themes/nano.min.css" />

    <!-- Choices Css -->
    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/choices.js/public/assets/styles/choices.min.css" />

    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/jsvectormap/css/jsvectormap.min.css" />

    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/swiper/swiper-bundle.min.css" />

    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
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
                <!-- Start::page-header -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                    <div>
                        <?php
                        // Get session user_id
                        if (session_status() === PHP_SESSION_NONE) {
                            session_start();
                        }
                        
                        $userId = $_SESSION['user_id'] ?? null;
                        if (!$userId) {
                            header("Location: http://localhost/dfcs/");
                            exit();
                        }
                        
                        $app = new App();
                        
                        // Get Agrovet staff profile info from user_id
                        $query = "SELECT s.id as staff_id, s.position, s.employee_number, s.agrovet_id, s.is_active,
                                  u.first_name, u.last_name, u.phone, u.email, u.location, u.profile_picture,
                                  a.name as agrovet_name, a.type_id, at.name as agrovet_type
                                  FROM agrovet_staff s
                                  JOIN users u ON s.user_id = u.id
                                  JOIN agrovets a ON s.agrovet_id = a.id
                                  JOIN agrovet_types at ON a.type_id = at.id
                                  WHERE s.user_id = $userId";
                        $staff = $app->select_one($query);
                        
                        if (!$staff) {
                            header("Location: http://localhost/dfcs/");
                            exit();
                        }
                
                        // Get stats for the cards
                         $query_active_credits = "SELECT 
                             COUNT(*) as active_count, 
                             COALESCE(SUM(aic.remaining_balance), 0) as total_outstanding,
                             COALESCE(SUM(aic.total_with_interest), 0) as total_with_interest,
                             COALESCE(AVG(DATEDIFF(NOW(), aic.fulfillment_date)), 0) as avg_duration,
                             COUNT(DISTINCT ica.farmer_id) as unique_farmers
                             FROM approved_input_credits aic
                             JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                             WHERE ica.agrovet_id = {$staff->agrovet_id} 
                             AND aic.status = 'active'
                             AND aic.remaining_balance > 0";
                         $credit_stats = $app->select_one($query_active_credits);                                 
                         // Calculate repayment performance (percentage paid)
                         $total_original = $credit_stats->total_with_interest ?? 0;
                         $total_remaining = $credit_stats->total_outstanding ?? 0;
                         $repayment_performance = 0;
                         
                         if ($total_original > 0) {
                             $total_paid = $total_original - $total_remaining;
                             $repayment_performance = ($total_paid / $total_original) * 100;
                         }                                
                         // Get the most recent credit approval date
                         $recent_credit_query = "SELECT 
                             MAX(aic.approval_date) as latest_approval
                             FROM approved_input_credits aic
                             JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                             WHERE ica.agrovet_id = {$staff->agrovet_id} 
                             AND aic.status = 'active'";
                         $recent_credit = $app->select_one($recent_credit_query);
                         $latest_approval = $recent_credit->latest_approval ?? null;
                         $days_since_last_approval = $latest_approval ? floor((time() - strtotime($latest_approval)) / (60 * 60 * 24)) : 0;
                        ?>
                        <p class="fw-semibold fs-18 mb-0">Welcome <?php echo $staff->first_name ?>
                            <?php echo $staff->last_name ?></p>
                        <span class="fs-semibold text-muted pt-5">Input Credit Management Dashboard</span>
                    </div>
                </div>
                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                    <h1 class="page-title fw-semibold fs-18 mb-0">Active Input Credits</h1>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Input Credits</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Active Credits</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <!-- Input Credit Stats Cards -->
                <div class="row mt-2">
                    <!-- Total Active Credits -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden" style="background:#6AA32D!important;">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded"
                                            style="background-color: white!important;">
                                            <i style="color:#6AA32D" class="fa-solid fa-credit-card fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-white mb-0">Active Credits</p>
                                                <h4 style="color:wheat;" class="fw-semibold mt-1">
                                                    <?php echo $credit_stats->active_count ?? 0 ?>
                                                </h4>
                                                <small class="text-white-50">For
                                                    <?php echo $credit_stats->unique_farmers ?? 0 ?> farmers</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Total Outstanding Amount -->
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
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Outstanding Amount</p>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    KES
                                                    <?php echo number_format($credit_stats->total_outstanding ?? 0, 2) ?>
                                                </h4>
                                                <small class="text-muted">Total value: KES
                                                    <?php echo number_format($credit_stats->total_with_interest ?? 0, 2) ?></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Repayment Performance -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-chart-pie fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Repayment Performance
                                                </p>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php echo number_format($repayment_performance, 1) ?>% <small
                                                        class="text-muted">repaid</small>
                                                </h4>
                                                <small class="text-muted">KES
                                                    <?php echo number_format($total_original - $total_remaining, 2) ?>
                                                    collected</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Average Duration & Last Approval -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-calendar-days fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Avg. Active Duration
                                                </p>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php echo number_format($credit_stats->avg_duration ?? 0, 0) ?>
                                                    <small class="text-muted">days</small>
                                                </h4>
                                                <?php if($latest_approval): ?>
                                                <small class="text-muted">Last approval:
                                                    <?php echo $days_since_last_approval ?> days ago</small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Active Input Credits Section -->
            <div class="row mt-4">
                <div class="col-xl-12">
                    <div id="activeInputCreditsSection">
                        <div class="card custom-card">
                            <div class="card-header justify-content-between">
                                <div class="card-title">
                                    <i class="ri-shopping-bag-line me-2"></i> Active Input Credits
                                </div>
                                <div class="btn-group">
                                    <button class="btn btn-outline-primary btn-sm" id="btnShowAll">All</button>
                                    <button class="btn btn-outline-warning btn-sm" id="btnShowHighBalance">High
                                        Balance</button>
                                    <button class="btn btn-outline-danger btn-sm"
                                        id="btnShowLongstanding">Longstanding</button>
                                    <button class="btn btn-outline-success btn-sm" id="btnShowNearCompletion">Near
                                        Completion</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="datatable-active-credits" class="table table-bordered text-nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th><i class="ri-hash-line me-1"></i>Credit ID</th>
                                                <th><i class="ri-user-line me-1"></i>Farmer</th>
                                                <th><i class="ri-money-dollar-circle-line me-1"></i>Original Amount</th>
                                                <th><i class="ri-money-dollar-circle-line me-1"></i>Remaining Balance
                                                </th>
                                                <th><i class="ri-percent-line me-1"></i>% Paid</th>
                                                <th><i class="ri-calendar-check-line me-1"></i>Fulfillment Date</th>
                                                <th><i class="ri-time-line me-1"></i>Active Duration</th>
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
    <!-- full calendar -->
    <!-- JavaScript -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>

    <script>
    $(document).ready(() => {
        // Load active input credits
        displayActiveInputCredits();
    });

    // Function to display active input credits
    function displayActiveInputCredits() {
        $.ajax({
            url: "http://localhost/dfcs/ajax/input-credit-controller/display-active-credits.php",
            type: 'POST',
            data: {
                displayActiveCredits: "true",
            },
            success: function(data, status) {
                $('#activeInputCreditsSection').html(data);
            },
            error: function(xhr, status, error) {
                console.error("Error loading active input credits:", error);
                toastr.error('Failed to load active input credits', 'Error', {
                    "positionClass": "toast-top-right",
                    "progressBar": true,
                    "timeOut": 5000
                });
            }
        });
    }
    //view input credit details
    function viewCreditDetails(creditId) {
        window.location.href = "input-credit-details?id=" + creditId;
    }
    // view repayment history
    function viewRepaymentHistory(creditId) {
        window.location.href = "input-credit-repayments?id=" + creditId;
    }
    </script>

</body>



</html>