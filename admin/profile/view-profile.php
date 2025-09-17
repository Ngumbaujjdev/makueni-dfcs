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
        <!-- Start::app-content -->
        <div class="main-content app-content">
            <div class="container-fluid">
                <?php
                 $app = new App();
                 
                 // Get admin ID from session
                 $adminId = $_SESSION['user_id'] ?? null;
                 
                 if (!$adminId) {
                    header("Location: http://localhost/dfcs/");
                     exit();
                 }
                        // Get admin information
                        $adminQuery = "SELECT u.*, a.id as admin_id 
                                       FROM users u 
                                       JOIN admins a ON u.id = a.user_id 
                                       WHERE u.id = $adminId";
                        $admin = $app->select_one($adminQuery);
                        // Get admin activity statistics
                       $statsQuery = "SELECT 
                           (SELECT COUNT(*) FROM activity_logs WHERE user_id = $adminId) as total_activities,
                           (SELECT COUNT(*) FROM activity_logs WHERE user_id = $adminId AND DATE(created_at) = CURDATE()) as today_activities,
                           (SELECT COUNT(*) FROM activity_logs WHERE user_id = $adminId AND activity_type = 'registration') as total_registrations,
                           (SELECT COUNT(*) FROM activity_logs WHERE user_id = $adminId AND activity_type LIKE '%updated%') as total_updates";
                       $stats = $app->select_one($statsQuery);          
                       // Get recent activities
                       $activitiesQuery = "SELECT * FROM activity_logs 
                                           WHERE user_id = $adminId 
                                           ORDER BY created_at DESC 
                                           LIMIT 10";
                       $recentActivities = $app->select_all($activitiesQuery);
                       // Get registered entities counts
                       $entitiesQuery = "SELECT 
                           (SELECT COUNT(*) FROM farmers WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as farmers_count,
                           (SELECT COUNT(*) FROM agrovets) as agrovets_count,
                           (SELECT COUNT(*) FROM bank_staff) as bank_staff_count,
                           (SELECT COUNT(*) FROM agrovet_staff) as agrovet_staff_count";
                       $entities = $app->select_one($entitiesQuery);
                ?>
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                    <h1 class="page-title fw-semibold fs-18 mb-0">Admin Profile</h1>
                </div>
                <!-- Admin Profile Header -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <img src="http://localhost/dfcs/<?php echo $admin->profile_picture ?? 'http://localhost/dfcs/assets/images/faces/face-image-1.jpg' ?>"
                                            class="img-fluid rounded-circle" style="width: 100px; height: 100px;"
                                            alt="Admin Photo">
                                    </div>
                                    <div class="flex-grow-1">
                                        <h4 class="mb-1">
                                            <?php echo htmlspecialchars($admin->first_name . ' ' . $admin->last_name) ?>
                                        </h4>
                                        <p class="mb-1">
                                            <span class="badge bg-primary">System Administrator</span>
                                            <span class="text-muted ms-2">
                                                <i class="ri-map-pin-line"></i>
                                                <?php echo htmlspecialchars($admin->location ?? 'Location not set') ?>
                                            </span>
                                        </p>
                                        <p class="text-muted mb-0">
                                            <i
                                                class="ri-mail-line me-2"></i><?php echo htmlspecialchars($admin->email) ?>
                                        </p>
                                    </div>
                                    <div>
                                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editProfileModal">
                                            <i class="ri-edit-line me-1"></i>Edit Profile
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span class="avatar avatar-md bg-primary">
                                            <i class="ri-activity-line"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="mb-1">Total Activities</p>
                                        <h5 class="mb-0"><?php echo number_format($stats->total_activities ?? 0) ?></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span class="avatar avatar-md bg-success">
                                            <i class="ri-user-add-line"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="mb-1">Total Registrations</p>
                                        <h5 class="mb-0"><?php echo number_format($stats->total_registrations ?? 0) ?>
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span class="avatar avatar-md bg-info">
                                            <i class="ri-edit-2-line"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="mb-1">Updates Made</p>
                                        <h5 class="mb-0"><?php echo number_format($stats->total_updates ?? 0) ?></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span class="avatar avatar-md bg-warning">
                                            <i class="ri-calendar-check-line"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="mb-1">Today's Activities</p>
                                        <h5 class="mb-0"><?php echo number_format($stats->today_activities ?? 0) ?></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Tabs Section -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-body">
                                <ul class="nav nav-tabs mb-4" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#activities">
                                            <i class="ri-history-line me-2"></i>Recent Activities
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#entities">
                                            <i class="ri-database-2-line me-2"></i>Managed Entities
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#settings">
                                            <i class="ri-settings-4-line me-2"></i>Settings
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <!-- Activities Tab -->
                                    <div class="tab-pane fade show active" id="activities">
                                        <?php if ($recentActivities && count($recentActivities) > 0): ?>
                                        <div class="timeline-page">
                                            <?php foreach ($recentActivities as $activity): ?>
                                            <div class="timeline-item">
                                                <div class="timeline-badge">
                                                    <i class="ri-record-circle-line"></i>
                                                </div>
                                                <div class="timeline-item-content">
                                                    <div class="d-flex justify-content-between">
                                                        <span class="text-primary fw-semibold">
                                                            <?php echo ucfirst(str_replace('_', ' ', $activity->activity_type)) ?>
                                                        </span>
                                                        <small class="text-muted">
                                                            <?php echo $app->formatTimeAgo($activity->created_at) ?>
                                                        </small>
                                                    </div>
                                                    <p class="mt-2 mb-0">
                                                        <?php echo htmlspecialchars($activity->description) ?></p>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php else: ?>
                                        <div class="text-center p-4">
                                            <i class="ri-history-line fs-2 text-muted mb-3"></i>
                                            <p class="mb-0">No recent activities found</p>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <!-- Entities Tab -->
                                    <div class="tab-pane fade" id="entities">
                                        <div class="row">
                                            <div class="col-md-6 col-xl-3">
                                                <div class="card custom-card bg-primary text-white">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-3">
                                                                <i class="ri-user-2-line fs-3"></i>
                                                            </div>
                                                            <div>
                                                                <p class="mb-1">Farmers</p>
                                                                <h5 class="mb-0">
                                                                    <?php echo number_format($entities->farmers_count) ?>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-xl-3">
                                                <div class="card custom-card bg-success text-white">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-3">
                                                                <i class="ri-store-2-line fs-3"></i>
                                                            </div>
                                                            <div>
                                                                <p class="mb-1">Agrovets</p>
                                                                <h5 class="mb-0">
                                                                    <?php echo number_format($entities->agrovets_count) ?>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-xl-3">
                                                <div class="card custom-card bg-info text-white">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-3">
                                                                <i class="ri-bank-line fs-3"></i>
                                                            </div>
                                                            <div>
                                                                <p class="mb-1">Bank Staff</p>
                                                                <h5 class="mb-0">
                                                                    <?php echo number_format($entities->bank_staff_count) ?>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-xl-3">
                                                <div class="card custom-card bg-warning text-white">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-3">
                                                                <i class="ri-team-line fs-3"></i>
                                                            </div>
                                                            <div>
                                                                <p class="mb-1">Agrovet Staff</p>
                                                                <h5 class="mb-0">
                                                                    <?php echo number_format($entities->agrovet_staff_count) ?>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Settings Tab -->
                                    <div class="tab-pane fade" id="settings">
                                        <div class="card custom-card">
                                            <div class="card-header">
                                                <h6 class="card-title">Profile Settings</h6>
                                            </div>
                                            <div class="card-body">
                                                <form id="updateAdminForm">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">First Name</label>
                                                            <input type="text" class="form-control" name="first_name"
                                                                value="<?php echo htmlspecialchars($admin->first_name) ?>"
                                                                required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Last Name</label>
                                                            <input type="text" class="form-control" name="last_name"
                                                                value="<?php echo htmlspecialchars($admin->last_name) ?>"
                                                                required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Email</label>
                                                            <input type="email" class="form-control" name="email"
                                                                value="<?php echo htmlspecialchars($admin->email) ?>"
                                                                required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Phone</label>
                                                            <input type="tel" class="form-control" name="phone"
                                                                value="<?php echo htmlspecialchars($admin->phone) ?>"
                                                                required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Location</label>
                                                            <input type="text" class="form-control" name="location"
                                                                value="<?php echo htmlspecialchars($admin->location) ?>">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">New Password</label>
                                                            <input type="password" class="form-control"
                                                                name="new_password"
                                                                placeholder="Leave blank to keep current password">
                                                        </div>
                                                        <div class="col-12 mb-3">
                                                            <label class="form-label">Profile Picture</label>
                                                            <input type="file" class="form-control"
                                                                name="profile_picture" accept="image/*">
                                                            <?php if($admin->profile_picture): ?>
                                                            <div class="mt-2">
                                                                <img src="<?php echo htmlspecialchars($admin->profile_picture) ?>"
                                                                    alt="Current profile picture" class="img-thumbnail"
                                                                    style="max-width: 100px;">
                                                            </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="col-12">
                                                            <button type="submit" class="btn btn-primary">
                                                                <i class="ri-save-line me-1"></i>Save Changes
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
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
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
                    integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
                    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
                <!-- Popper JS -->
                <script src="http://localhost/dfcs/assets/libs/%40popperjs/core/umd/popper.min.js">
                </script>
                <!-- Bootstrap JS -->
                <script src="http://localhost/dfcs/assets/libs/bootstrap/js/bootstrap.bundle.min.js">
                </script>
                <!-- Defaultmenu JS -->
                <script src="http://localhost/dfcs/assets/js/defaultmenu.min.js">
                </script>
                <!-- Node Waves JS-->
                <script src="http://localhost/dfcs/assets/libs/node-waves/waves.min.js">
                </script>
                <!-- Sticky JS -->
                <script src="http://localhost/dfcs/assets/js/sticky.js">
                </script>
                <!-- Simplebar JS -->
                <script src="http://localhost/dfcs/assets/libs/simplebar/simplebar.min.js">
                </script>
                <script src="http://localhost/dfcs/assets/js/simplebar.js">
                </script>
                <!-- Color Picker JS -->
                <script src="http://localhost/dfcs/assets/libs/%40simonwep/pickr/pickr.es5.min.js">
                </script>
                <!-- Custom-Switcher JS -->
                <script src="http://localhost/dfcs/assets/js/custom-switcher.min.js">
                </script>
                <!-- Custom JS -->
                <script src="http://localhost/dfcs/assets/js/custom.js">
                </script>
                <!-- Used In Zoomable TIme Series Chart -->
                <script src="http://localhost/dfcs/assets/js/dataseries.js">
                </script>
                <!---Used In Annotations Chart-->
                <script src="http://localhost/dfcs/assets/js/apexcharts-stock-prices.js">
                </script>
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
                <script src="http://localhost/dfcs/assets/js/datatables.js">
                </script>
                <!-- Toastr JS -->
                <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4">
                </script>
                <script>
                $(document).ready(function() {
                    $('#updateAdminForm').submit(function(e) {
                        e.preventDefault();
                        let formData = new FormData(this);
                        $.ajax({
                            url: 'http://localhost/dfcs/ajax/admin-controller/update-admin.php',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                try {
                                    let data = JSON.parse(response);
                                    if (data.success) {
                                        toastr.success('Profile updated successfully',
                                            'Success', {
                                                timeOut: 3000,
                                                closeButton: true,
                                                progressBar: true,
                                                positionClass: "toast-top-right"
                                            });

                                        setTimeout(function() {
                                            location.reload();
                                        }, 2000);
                                    } else {
                                        toastr.error(data.message || 'Update failed',
                                            'Error', {
                                                timeOut: 3000,
                                                closeButton: true,
                                                progressBar: true,
                                                positionClass: "toast-top-right"
                                            });
                                    }
                                } catch (e) {
                                    toastr.error('Error processing response', 'Error', {
                                        timeOut: 3000,
                                        closeButton: true,
                                        progressBar: true,
                                        positionClass: "toast-top-right"
                                    });
                                }
                            },
                            error: function() {
                                toastr.error('Server error occurred', 'Error', {
                                    timeOut: 3000,
                                    closeButton: true,
                                    progressBar: true,
                                    positionClass: "toast-top-right"
                                });
                            }
                        });
                    });
                });
                </script>
</body>

</html>