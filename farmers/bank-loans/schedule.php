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
    <meta name="apple-mobile-web-app-title" content="Baituti Adventures" />
    <link rel="manifest" href="http://localhost/dfcs/assets/images/favicon/site.webmanifest" />
    <!-- font awesome -->
    <!-- Choices JS -->
    <script src="http://localhost/dfcs/assets/libs/choices.js/public/assets/scripts/choices.min.js">
    </script>
    <!-- font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

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
    <!-- mermaid -->

    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/gridjs/theme/mermaid.min.css">

    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/apexcharts/apexcharts.css">
    <script src="https://cdn.jsdelivr.net/npm/tinycolor2@1.4.1/dist/tinycolor-min.js"></script>
    <link rel="stylesheet" href="http://localhost/dfcs/toast/toast.css">
    <!-- datatables -->
    <link rel="stylesheet" href="http://localhost/dfcs/assets/data-tables/1.12.1/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet"
        href="http://localhost/dfcs/assets/data-tables/responsive/2.3.0/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" href="http://localhost/dfcs/assets/data-tables/buttons/2.2.3/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="http://localhost/dfcs/toast/toast.css">
    <!-- TINY COLORS -->
    <!-- full calendar -->
    <!-- CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
</head>
<style>
.fc-event {
    cursor: pointer;
}

.fc-event-title {
    font-weight: 500;
}

.payment-completed {
    background-color: #6AA32D !important;
    border-color: #6AA32D !important;
}

.payment-upcoming {
    background-color: #FFC107 !important;
    border-color: #FFC107 !important;
    color: #343a40 !important;
}

.payment-overdue {
    background-color: #DC3545 !important;
    border-color: #DC3545 !important;
}
</style>
<style>
/* Additional styling for the calendar */
.loan-calendar {
    min-height: 600px;
    margin-bottom: 1rem;
}

.fc-daygrid-day.fc-day-today {
    background-color: rgba(106, 163, 45, 0.1) !important;
}

.fc-header-toolbar {
    margin-bottom: 1.5rem !important;
}

.fc-toolbar-title {
    font-size: 1.25rem !important;
    font-weight: 600 !important;
}

.fc-button-primary {
    background-color: #6AA32D !important;
    border-color: #6AA32D !important;
}

.fc-button-primary:hover {
    background-color: #588b25 !important;
    border-color: #588b25 !important;
}

.fc-button-active {
    background-color: #4c7820 !important;
    border-color: #4c7820 !important;
}

/* Event styling */
.payment-completed {
    background-color: #6AA32D !important;
    border-color: #6AA32D !important;
}

.payment-upcoming {
    background-color: #FFC107 !important;
    border-color: #FFC107 !important;
    color: #212529 !important;
}

.payment-overdue {
    background-color: #DC3545 !important;
    border-color: #DC3545 !important;
}

/* Mobile responsiveness improvements */
@media (max-width: 768px) {
    .fc-toolbar.fc-header-toolbar {
        flex-direction: column;
    }

    .fc-toolbar-chunk {
        margin-bottom: 0.5rem;
    }
}
</style>

<body>
    <!-- loader -->
    <?php include "../../includes/loader.php" ?>

    <div class="page">
        <!-- app-header -->
        <?php include "../../includes/navigation.php" ?>
        <!-- /app-header -->
        <!-- Start::app-sidebar -->
        <?php include "../../includes/sidebar.php" ?>
        <!-- End::app-sidebar -->

        <!-- End::app-sidebar -->
        <!-- End::app-sidebar -->

        <!-- Start::app-content -->
        <div class="main-content app-content">
            <div class="container-fluid">
                <!-- Page Header -->
                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                    <div>
                        <?php
                            $app = new App;
                            
                            // Get farmer details including their registration number
                            $query = "SELECT u.*, f.registration_number, f.category_id, fc.name as category_name
                                      FROM users u
                                      LEFT JOIN farmers f ON u.id = f.user_id
                                      LEFT JOIN farmer_categories fc ON f.category_id = fc.id
                                      WHERE u.id = " . $_SESSION['user_id'];
                            
                            $farmer = $app->select_one($query);
                            ?>

                        <p class="fw-semibold fs-18 mb-0">
                            Welcome <?php echo $farmer->first_name ?> <?php echo $farmer->last_name ?>
                            <span class="badge bg-success ms-2"><?php echo $farmer->registration_number ?></span>
                        </p>

                        <span class="fs-semibold text-muted pt-5">
                            Active Loans Dashboard
                            <?php if($farmer->category_name): ?>
                            - <?php echo $farmer->category_name ?> Farmer
                            <?php endif; ?>
                        </span>
                    </div>
                </div>

                <!-- Active Loans Summary -->
                <?php 
                        // Initialize the app
                        $app = new App;
                        
                        // Get farmer details including their registration number
                        $query = "SELECT u.*, f.id as farmer_id, f.registration_number, f.category_id, fc.name as category_name
                                  FROM users u
                                  LEFT JOIN farmers f ON u.id = f.user_id
                                  LEFT JOIN farmer_categories fc ON f.category_id = fc.id
                                  WHERE u.id = " . $_SESSION['user_id'];
                        
                        $farmer = $app->select_one($query);
                        $farmer_id = $farmer->farmer_id;
                        
                       
                        ?>
                <!-- Row 1: Repayment Schedule Summary -->
                <div class="row mt-4">
                    <div class="col-lg-12">
                        <div class="card custom-card shadow-sm border-0">
                            <div class="card-header d-flex align-items-center"
                                style="background: linear-gradient(to right, #f8faf5, #ffffff);">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-success-transparent me-2">
                                        <i class="fa-solid fa-calendar-check text-success"></i>
                                    </div>
                                    <h6 class="mb-0 fw-semibold">Loan Repayment Schedule</h6>
                                </div>
                                <div class="ms-auto">
                                    <span class="badge rounded-pill bg-primary-transparent text-primary">
                                        <i class="fa-solid fa-info-circle me-1"></i> Payment Timeline
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Next Payment Due -->
                                    <div class="col-lg-4 col-md-6">
                                        <div class="d-flex p-3 border rounded-3 bg-light-transparent">
                                            <div class="me-3">
                                                <?php
                                                        // Get nearest upcoming payment
                                                        $nextPaymentQuery = "SELECT 
                                                            al.id AS loan_id,
                                                            lt.name AS loan_type,
                                                            ADDDATE(al.disbursement_date, INTERVAL (al.approved_term/COUNT(DISTINCT lr.id)) MONTH) AS next_payment_date,
                                                            (al.total_repayment_amount / al.approved_term) AS monthly_amount
                                                        FROM 
                                                            approved_loans al
                                                        JOIN 
                                                            loan_applications la ON al.loan_application_id = la.id
                                                        JOIN 
                                                            loan_types lt ON la.loan_type_id = lt.id
                                                        LEFT JOIN 
                                                            loan_repayments lr ON al.id = lr.approved_loan_id
                                                        WHERE 
                                                            la.farmer_id = {$farmer_id} AND al.status = 'active'
                                                        GROUP BY 
                                                            al.id
                                                        HAVING 
                                                            next_payment_date >= CURDATE()
                                                        ORDER BY 
                                                            next_payment_date ASC
                                                        LIMIT 1";
                                                        
                                                        $nextPayment = $app->select_one($nextPaymentQuery);
                                                        
                                                        // Calculate days until next payment
                                                        $daysRemaining = 0;
                                                        $isUpcoming = false;
                                                        
                                                        if($nextPayment) {
                                                            $today = new DateTime();
                                                            $paymentDate = new DateTime($nextPayment->next_payment_date);
                                                            $interval = $today->diff($paymentDate);
                                                            $daysRemaining = $interval->days;
                                                            $isUpcoming = $daysRemaining <= 14;
                                                        }
                                                        ?>

                                                <div class="position-relative">
                                                    <div class="avatar avatar-md avatar-rounded bg-warning-transparent">
                                                        <i class="fa-solid fa-hourglass-half text-warning fs-18"></i>
                                                    </div>
                                                    <?php if($isUpcoming): ?>
                                                    <span
                                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                        <i class="fa-solid fa-exclamation"></i>
                                                    </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="fw-semibold mb-1">Next Payment Due</h6>
                                                <?php if($nextPayment): ?>
                                                <div>
                                                    <span
                                                        class="fs-14 fw-semibold <?php echo $isUpcoming ? 'text-danger' : 'text-dark'; ?>">
                                                        <?php echo date('M d, Y', strtotime($nextPayment->next_payment_date)); ?>
                                                    </span>
                                                    <div class="text-muted small">
                                                        <?php echo $daysRemaining; ?> days remaining
                                                    </div>
                                                    <div class="mt-1">
                                                        <span class="badge bg-success-transparent text-success">
                                                            KES
                                                            <?php echo number_format($nextPayment->monthly_amount, 2); ?>
                                                        </span>
                                                        <span class="badge bg-info-transparent text-info">
                                                            <?php echo $nextPayment->loan_type; ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <?php else: ?>
                                                <div class="text-muted">No upcoming payments scheduled</div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Monthly Payment Summary -->
                                    <div class="col-lg-4 col-md-6">
                                        <div class="d-flex p-3 border rounded-3 bg-light-transparent">
                                            <div class="me-3">
                                                <div class="avatar avatar-md avatar-rounded bg-success-transparent">
                                                    <i class="fa-solid fa-calendar-day text-success fs-18"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="fw-semibold mb-1">Monthly Repayments</h6>
                                                <?php
                                                  // Calculate total monthly payments from all active loans
                                                  $monthlyPaymentsQuery = "SELECT 
                                                      COUNT(al.id) as active_loans,
                                                      SUM(al.total_repayment_amount / al.approved_term) AS total_monthly
                                                  FROM 
                                                      approved_loans al
                                                  JOIN 
                                                      loan_applications la ON al.loan_application_id = la.id
                                                  WHERE 
                                                      la.farmer_id = {$farmer_id} AND al.status = 'active'";
                                                  
                                                  $monthlyPayments = $app->select_one($monthlyPaymentsQuery);
                                                  
                                                  if($monthlyPayments && $monthlyPayments->active_loans > 0):
                                                  ?>
                                                <div>
                                                    <span class="fs-14 fw-semibold text-success">
                                                        KES
                                                        <?php echo number_format($monthlyPayments->total_monthly, 2); ?>
                                                    </span>
                                                    <div class="text-muted small">
                                                        Combined monthly amount across
                                                        <?php echo $monthlyPayments->active_loans; ?> active loan(s)
                                                    </div>
                                                    <div class="mt-1 d-flex align-items-center">
                                                        <div class="me-2 small">Payment schedule:</div>
                                                        <span class="badge bg-secondary-transparent text-secondary">
                                                            Monthly
                                                        </span>
                                                    </div>
                                                </div>
                                                <?php else: ?>
                                                <div class="text-muted">No active loans requiring payment</div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Calendar Options -->
                                    <div class="col-lg-4 col-md-12">
                                        <div class="p-3 border rounded-3 bg-light-transparent h-100">
                                            <h6 class="fw-semibold mb-3">
                                                <i class="fa-solid fa-sliders text-primary me-1"></i> Calendar View
                                                Options
                                            </h6>
                                            <div class="mb-2 d-flex gap-2 flex-wrap">
                                                <button id="viewMonthBtn" class="btn btn-sm btn-outline-success active">
                                                    <i class="fa-solid fa-calendar-days me-1"></i> Month
                                                </button>
                                                <button id="viewWeekBtn" class="btn btn-sm btn-outline-success">
                                                    <i class="fa-solid fa-calendar-week me-1"></i> Week
                                                </button>
                                                <button id="viewListBtn" class="btn btn-sm btn-outline-success">
                                                    <i class="fa-solid fa-list-ul me-1"></i> List
                                                </button>
                                            </div>
                                            <div>
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="badge rounded-pill bg-success me-2"></span>
                                                    <span class="small">Completed payments</span>
                                                </div>
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="badge rounded-pill bg-warning me-2"></span>
                                                    <span class="small">Upcoming payments</span>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge rounded-pill bg-danger me-2"></span>
                                                    <span class="small">Overdue payments</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Alert for upcoming payments -->
                                <?php if($nextPayment && $isUpcoming): ?>
                                <div class="alert alert-warning mt-3 mb-0">
                                    <div class="d-flex">
                                        <div class="me-3">
                                            <i class="fa-solid fa-bell fs-24 text-warning"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Upcoming Payment Reminder</h6>
                                            <p class="mb-0">You have a payment of <strong>KES
                                                    <?php echo number_format($nextPayment->monthly_amount, 2); ?></strong>
                                                due on
                                                <strong><?php echo date('F d, Y', strtotime($nextPayment->next_payment_date)); ?></strong>.
                                                Please ensure you have sufficient funds in your account for the
                                                automatic deduction from your next produce sale.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Row 2: Repayment Calendar -->
                <div class="row mt-4">
                    <div class="col-lg-12">
                        <div class="card custom-card shadow-sm border-0">
                            <div class="card-body">
                                <!-- Calendar will be rendered here -->
                                <div id="loanRepaymentCalendar" class="loan-calendar"></div>
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

    <!-- JavaScript for Calendar Implementation -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize calendar when document is ready
        initializeRepaymentCalendar();

        // Handle the view buttons
        document.getElementById('viewMonthBtn').addEventListener('click', function() {
            calendar.changeView('dayGridMonth');
            updateActiveButton('viewMonthBtn');
        });

        document.getElementById('viewWeekBtn').addEventListener('click', function() {
            calendar.changeView('timeGridWeek');
            updateActiveButton('viewWeekBtn');
        });

        document.getElementById('viewListBtn').addEventListener('click', function() {
            calendar.changeView('listMonth');
            updateActiveButton('viewListBtn');
        });
    });

    let calendar; // Global variable to access calendar

    function updateActiveButton(activeButtonId) {
        // Remove active class from all buttons
        document.querySelectorAll('.btn-outline-success').forEach(btn => {
            btn.classList.remove('active');
        });
        // Add active class to the clicked button
        document.getElementById(activeButtonId).classList.add('active');
    }

    function initializeRepaymentCalendar() {
        // Get the calendar container
        const calendarEl = document.getElementById('loanRepaymentCalendar');

        // Initialize FullCalendar
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            height: 'auto',
            themeSystem: 'bootstrap5',
            firstDay: 1, // Start the week on Monday
            dayMaxEvents: true, // Allow "more" link when too many events
            events: function(info, successCallback, failureCallback) {
                // Fetch events via AJAX
                $.ajax({
                    url: 'http://localhost/dfcs/ajax/loan-controller/get-bank-repayment-events.php',
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        successCallback(response);
                    },
                    error: function() {
                        failureCallback({
                            message: 'Error loading repayment schedule'
                        });
                        toastr.error('Failed to load repayment schedule', 'Error', {
                            "positionClass": "toast-top-right",
                            "progressBar": true,
                            "timeOut": 3000
                        });
                    }
                });
            },
            eventClick: function(info) {
                showPaymentDetails(info.event);
            },
            eventDidMount: function(info) {
                // Enable Bootstrap tooltips on events
                $(info.el).tooltip({
                    title: info.event.extendedProps.tooltip,
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body'
                });
            }
        });

        // Render the calendar
        calendar.render();
    }

    function showPaymentDetails(event) {
        // Create modal content based on event data
        let status = event.extendedProps.status;
        let statusLabel = '';
        let statusClass = '';

        switch (status) {
            case 'completed':
                statusLabel = 'Completed';
                statusClass = 'success';
                break;
            case 'upcoming':
                statusLabel = 'Upcoming';
                statusClass = 'warning';
                break;
            case 'overdue':
                statusLabel = 'Overdue';
                statusClass = 'danger';
                break;
        }

        // Format date for display
        let paymentDate = new Date(event.start);
        let formattedDate = paymentDate.toLocaleString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        // Create modal HTML
        let modalContent = `
            <div class="modal fade" id="paymentDetailsModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-light">
                            <h5 class="modal-title">Payment Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar avatar-md bg-${statusClass}-transparent me-3">
                                    <i class="fa-solid fa-calendar-check text-${statusClass}"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">${event.title}</h6>
                                    <span class="badge bg-${statusClass}-transparent text-${statusClass}">${statusLabel}</span>
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <td class="fw-medium text-muted">Loan Reference:</td>
                                            <td>${event.extendedProps.loanReference}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium text-muted">Payment Date:</td>
                                            <td>${formattedDate}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium text-muted">Amount:</td>
                                            <td class="text-success fw-semibold">KES ${event.extendedProps.amount}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium text-muted">Loan Type:</td>
                                            <td>${event.extendedProps.loanType}</td>
                                        </tr>
                                        ${status === 'completed' ? `
                                        <tr>
                                            <td class="fw-medium text-muted">Payment Method:</td>
                                            <td>${event.extendedProps.paymentMethod || 'Automatic deduction'}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium text-muted">Payment Date:</td>
                                            <td>${event.extendedProps.actualPaymentDate || formattedDate}</td>
                                        </tr>
                                        ` : ''}
                                    </tbody>
                                </table>
                            </div>
                            
                            ${status === 'upcoming' ? `
                            <div class="alert alert-info mt-3">
                                <i class="fa-solid fa-info-circle me-2"></i>
                                This payment will be automatically deducted from your next produce sale after the due date.
                            </div>
                            ` : ''}
                            
                            ${status === 'overdue' ? `
                            <div class="alert alert-danger mt-3">
                                <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                This payment is overdue. Please make a produce delivery soon to clear this payment.
                            </div>
                            ` : ''}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            ${event.extendedProps.loanId ? `
                            <button type="button" class="btn btn-primary" onclick="viewLoanDetails(${event.extendedProps.loanId})">
                                View Loan Details
                            </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Append modal to body and show it
        $('body').append(modalContent);
        $('#paymentDetailsModal').modal('show');

        // Remove modal from DOM when hidden
        $('#paymentDetailsModal').on('hidden.bs.modal', function() {
            $(this).remove();
        });
    }
    </script>

</body>



</html>