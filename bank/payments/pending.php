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


</head>

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
                <!-- Start::page-header -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                    <div>
                        <!-- if the user is an admin -->
                        <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 5): ?>
                        <?php
                        $app = new App;
                        $email = $_SESSION['email'];
                        $query = "SELECT * FROM users WHERE id=" . $_SESSION['user_id'];
                        $admin = $app->select_one($query);
                        ?>
                        <p class="fw-semibold fs-18 mb-0">Welcome <?php echo $admin->first_name ?>
                            <?php echo $admin->last_name ?></p>

                        <?php else: ?>
                        <?php
                        $app = new App;
                        $query = "SELECT * FROM users WHERE id=" . $_SESSION['user_id'];
                        $staff = $app->select_one($query);
                        ?>
                        <p class="fw-semibold fs-18 mb-0">Welcome <?php echo $staff->first_name ?>
                            <?php echo $staff->last_name ?></p>
                        <?php endif; ?>
                        <span class="fs-semibold text-muted pt-5">Produce Management Dashboard</span>
                    </div>
                </div>

                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                    <h1 class="page-title fw-semibold fs-18 mb-0">Process Farmer Payments</h1>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Banking</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Process Payments</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <!-- Payment Processing Stats Cards -->
                <div class="row mt-2">
                    <!-- Pending Payments -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden" style="background:#6AA32D!important;">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded"
                                            style="background-color: white!important;">
                                            <i style="color:#6AA32D" class="fa-solid fa-wallet fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-white mb-0">Pending Payments</p>
                                                <?php
                                    // Count verified produce deliveries that have been marked as sold but not processed for payment
                                    $query = "SELECT COUNT(*) as count 
                                             FROM produce_deliveries pd
                                             WHERE pd.status = 'verified'
                                             AND pd.is_sold = 1
                                             AND NOT EXISTS (
                                                 SELECT 1 FROM farmer_account_transactions fat
                                                 WHERE fat.reference_id = pd.id
                                                 AND fat.transaction_type = 'credit'
                                             )";
                                    $result = $app->select_one($query);
                                    $pending_payments = ($result) ? $result->count : 0;
                                    ?>
                                                <h4 style="color:wheat;" class="fw-semibold mt-1">
                                                    <?php echo $pending_payments ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Processed Payments -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-check-circle fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Processed Payments</p>
                                                <?php
                                    // Count processed payments to farmers for produce
                                    $query = "SELECT COUNT(*) as count 
                                             FROM farmer_account_transactions 
                                             WHERE transaction_type = 'credit'
                                             AND reference_id IN (
                                                SELECT id FROM produce_deliveries WHERE is_sold = 1
                                             )";
                                    $result = $app->select_one($query);
                                    $processed_payments = ($result) ? $result->count : 0;
                                    ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php echo $processed_payments ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Farmers With Pending Payments -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-users fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Farmers Awaiting
                                                    Payment</p>
                                                <?php
                                    // Count unique farmers with pending payments
                                    $query = "SELECT COUNT(DISTINCT fm.id) as count 
                                             FROM produce_deliveries pd
                                             JOIN farm_products fp ON pd.farm_product_id = fp.id
                                             JOIN farms f ON fp.farm_id = f.id
                                             JOIN farmers fm ON f.farmer_id = fm.id
                                             WHERE pd.status = 'verified'
                                             AND pd.is_sold = 1
                                             AND NOT EXISTS (
                                                 SELECT 1 FROM farmer_account_transactions fat
                                                 WHERE fat.reference_id = pd.id
                                                 AND fat.transaction_type = 'credit'
                                             )";
                                    $result = $app->select_one($query);
                                    $farmers_pending = ($result) ? $result->count : 0;
                                    ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    <?php echo $farmers_pending ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Pending Value -->
                    <div class="col-xxl-3 col-lg-3 col-md-6">
                        <div class="card custom-card overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded" style="background:#6AA32D;">
                                            <i class="fa-solid fa-money-bill-transfer fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                <p class="text-muted mb-0" style="color:#6AA32D;">Pending Payment Value
                                                </p>
                                                <?php
                                       // Calculate total value of pending payments (90% of produce value since 10% is SACCO commission)
                                       $query = "SELECT COALESCE(SUM(total_value * 0.9), 0) as total_pending 
                                                FROM produce_deliveries pd
                                                WHERE pd.status = 'verified'
                                                AND pd.is_sold = 1
                                                AND NOT EXISTS (
                                                    SELECT 1 FROM farmer_account_transactions fat
                                                    WHERE fat.reference_id = pd.id
                                                    AND fat.transaction_type = 'credit'
                                                )";
                                       $result = $app->select_one($query);
                                       $total_pending = ($result) ? number_format($result->total_pending, 2) : 0;
                                       ?>
                                                <h4 class="fw-semibold mt-1" style="color:#6AA32D;">
                                                    KES <?php echo $total_pending ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Pending Payments Section -->
                <div class="row mt-4">
                    <div class="col-xl-12">
                        <div id="pendingPaymentsSection"></div>
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
    <script>
    $(document).ready(() => {
        displayPendingPayments();
    });

    // Function to display pending payments
    function displayPendingPayments() {
        let displayPendingPayments = "true";
        $.ajax({
            url: "http://localhost/dfcs/ajax/payment-controller/display-pending-payments.php",
            type: 'POST',
            data: {
                displayPendingPayments: displayPendingPayments,
            },
            success: function(data, status) {
                $('#pendingPaymentsSection').html(data);
            },
        });
    }

    // Function to open payment processing modal
    // Function to open payment processing modal
    function openPaymentModal(produceId, farmerId) {
        // Clear previous values
        $('#loanTableBody').empty();
        $('#paymentNotes').val('');

        // Set the produce ID in the form
        $('#paymentProduceId').val(produceId);
        $('#paymentFarmerId').val(farmerId);

        // Fetch produce and farmer details
        $.ajax({
            url: "http://localhost/dfcs/ajax/payment-controller/get-payment-details.php",
            type: "POST",
            data: {
                produceId: produceId,
                farmerId: farmerId
            },
            success: function(data, status) {
                let response = JSON.parse(data);
                if (response.success) {
                    // Fill in produce and payment details
                    $('#farmerName').text(response.farmerName);
                    $('#produceReference').text('DLVR' + String(produceId).padStart(5, '0'));
                    $('#totalSaleValue').text('KES ' + response.totalValue.toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));

                    let commission = response.totalValue * 0.1;
                    $('#saccoCommission').text('KES ' + commission.toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));

                    let farmerPayment = response.totalValue - commission;
                    $('#farmerPaymentAmount').text('KES ' + farmerPayment.toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));

                    // Initialize final payment amount
                    let finalPayment = farmerPayment;

                    // Handle loans if any
                    if (response.loans && response.loans.length > 0) {
                        $('#loanSection').show();

                        // Calculate maximum repayment amount (70% of farmer payment)
                        let maxRepaymentAmount = farmerPayment * 0.7;

                        // Calculate total outstanding loan amount
                        let totalOutstanding = 0;
                        response.loans.forEach(loan => {
                            totalOutstanding += parseFloat(loan.remaining_balance);
                        });

                        // Display total remaining balance
                        $('#totalRemainingBalance').text('KES ' + totalOutstanding.toLocaleString(
                            undefined, {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }));

                        // Calculate repayment amounts for each loan
                        let totalRepayment = 0;
                        response.loans.forEach(loan => {
                            let repaymentAmount = 0;

                            if (totalOutstanding <= maxRepaymentAmount) {
                                // We can repay the full loan amount
                                repaymentAmount = parseFloat(loan.remaining_balance);
                            } else {
                                // We need to prorate the repayment
                                let loanProportion = parseFloat(loan.remaining_balance) /
                                    totalOutstanding;
                                repaymentAmount = maxRepaymentAmount * loanProportion;

                                // Round to 2 decimal places
                                repaymentAmount = Math.round(repaymentAmount * 100) / 100;
                            }

                            // Ensure we don't exceed the remaining balance
                            repaymentAmount = Math.min(repaymentAmount, parseFloat(loan
                                .remaining_balance));

                            // Add to total repayment
                            totalRepayment += repaymentAmount;

                            // Create loan row
                            let loanRow = '<tr>';
                            loanRow += '<td class="px-3">' + loan.reference + '</td>';
                            loanRow += '<td class="px-3">KES ' + parseFloat(loan.remaining_balance)
                                .toLocaleString(undefined, {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                }) + '</td>';
                            loanRow += '<td class="px-3">KES ' + repaymentAmount.toLocaleString(
                                undefined, {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                }) + '</td>';
                            loanRow += '</tr>';

                            $('#loanTableBody').append(loanRow);
                        });

                        // Display total repayment amount
                        $('#totalRepaymentAmount').text('KES ' + totalRepayment.toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }));

                        // Update final payment
                        finalPayment = farmerPayment - totalRepayment;
                    } else {
                        $('#loanSection').hide();
                    }

                    // Set final payment amount
                    $('#finalPaymentAmount').text('KES ' + finalPayment.toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));

                    // Show the modal
                    $('#processPaymentModal').modal('show');
                } else {
                    toastr.error(response.message, 'Error', {
                        "positionClass": "toast-top-right",
                        "progressBar": true,
                        "timeOut": 3000,
                        "extendedTimeOut": 1000,
                        "hideMethod": "fadeOut"
                    });
                }
            },
            error: function() {
                toastr.error('An error occurred while fetching payment details', 'Error', {
                    "positionClass": "toast-top-right",
                    "progressBar": true,
                    "timeOut": 3000,
                    "extendedTimeOut": 1000,
                    "hideMethod": "fadeOut"
                });
            }
        });
    }
    // Function to process the payment
    function processPayment() {
        let produceId = $('#paymentProduceId').val();
        let farmerId = $('#paymentFarmerId').val();
        let paymentNotes = $('#paymentNotes').val();

        // Get repayment amounts for each loan (would need to be calculated or stored in data attributes)
        let loanRepayments = [];
        $('#loanTableBody tr').each(function() {
            let loanReference = $(this).find('td:first').text();
            let repaymentAmountText = $(this).find('td:last').text();
            let repaymentAmount = parseFloat(repaymentAmountText.replace('KES ', '').replace(/,/g, ''));

            loanRepayments.push({
                reference: loanReference,
                amount: repaymentAmount
            });
        });

        $.ajax({
            url: "http://localhost/dfcs/ajax/payment-controller/process-payment.php",
            type: "POST",
            data: {
                produceId: produceId,
                farmerId: farmerId,
                loanRepayments: JSON.stringify(loanRepayments),
                paymentNotes: paymentNotes
            },
            success: function(data, status) {
                if (data.success) {
                    // Close the modal
                    $('#processPaymentModal').modal('hide');

                    // Reset form
                    $('#paymentForm')[0].reset();

                    toastr.success('Payment processed successfully', 'Success', {
                        "positionClass": "toast-top-right",
                        "progressBar": true,
                        "timeOut": 3000,
                        "extendedTimeOut": 1000,
                        "hideMethod": "fadeOut"
                    });

                    // Refresh pending payments table
                    setTimeout(function() {
                        displayPendingPayments();
                    }, 300);
                } else {
                    toastr.error(data.message, 'Error', {
                        "positionClass": "toast-top-right",
                        "progressBar": true,
                        "timeOut": 3000,
                        "extendedTimeOut": 1000,
                        "hideMethod": "fadeOut"
                    });
                }
            },
            error: function() {
                toastr.error('An error occurred while processing the payment', 'Error', {
                    "positionClass": "toast-top-right",
                    "progressBar": true,
                    "timeOut": 3000,
                    "extendedTimeOut": 1000,
                    "hideMethod": "fadeOut"
                });
            }
        });
    }
    </script>

</body>



</html>