<?php include "../../config/config.php" ?>
<?php include "../../libs/App.php" ?>
<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light"
    data-menu-styles="dark" data-toggled="close">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>
        Baituti - Your Partner in Unforgettable Journeys
    </title>
    <meta name="Description" content="East Africa Travel and Tour Adventures - Baituti Triple Tee Adventures" />
    <meta name="Author" content="Baituti Triple Tee Adventures" />
    <meta name="keywords"
        content="East Africa travel, safaris, beach escapes, personalized tours, adventure travel, responsible tourism, Baituti Triple Tee Adventures" />

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Choices JS -->
    <script src="http://localhost/dfcs/assets/libs/choices.js/public/assets/scripts/choices.min.js">
    </script>

    <!-- Main Theme Js -->
    <script src="http://localhost/dfcs/assets/js/main.js"></script>

    <!-- Bootstrap Css -->
    <link id="style" href="http://localhost/dfcs/assets/libs/bootstrap/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Style Css -->
    <link href="http://localhost/dfcs/assets/css/styles.min.css" rel="stylesheet">

    <!-- Icons Css -->
    <link href="http://localhost/dfcs/assets/css/icons.css" rel="stylesheet">

    <!-- Node Waves Css -->
    <link href="../assets/libs/node-waves/waves.min.css" rel="stylesheet">

    <!-- Simplebar Css -->
    <link href="http://localhost/dfcs/assets/libs/simplebar/simplebar.min.css" rel="stylesheet">

    <!-- Color Picker Css -->
    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/flatpickr/flatpickr.min.css">
    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/%40simonwep/pickr/themes/nano.min.css">

    <!-- Choices Css -->
    <link rel="stylesheet"
        href="http://localhost/dfcs/assets/libs/choices.js/public/assets/styles/choices.min.css">

    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/quill/quill.snow.css">
    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/quill/quill.bubble.css">

    <!-- Filepond CSS -->
    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/filepond/filepond.min.css">
    <link rel="stylesheet"
        href="http://localhost/dfcs/assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css">
    <link rel="stylesheet"
        href="http://localhost/dfcs/assets/libs/filepond-plugin-image-edit/filepond-plugin-image-edit.min.css">

    <!-- Date & Time Picker CSS -->
    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/flatpickr/flatpickr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="http://localhost/dfcs/toast/toast.css">

</head>

<body>

    <!-- Start Switcher -->
    <?php include "../includes/start-switcher.php" ?>
    <!-- End Switcher -->


    <!-- Loader -->
    <?php include "../includes/loader.php" ?>
    <!-- Loader -->

    <div class="page">

        <!-- app-header -->
        <?php include "../includes/navigation.php" ?>

        <!-- /app-header -->
        <!-- Start::app-sidebar -->
        <?php include "../includes/sidebar.php" ?>
        <!-- End::app-sidebar -->
        <?php
        // Get package ID from URL
        $package_id = isset($_GET['id']) ? $_GET['id'] : null;

        if (!$package_id) {
            header("Location: view-packages");
            exit;
        }

        $app = new App;
        $query = "SELECT tp.*, tt.type_name, l.location_name, l.location_type 
          FROM tour_packages tp
          LEFT JOIN tour_types tt ON tp.tour_type_id = tt.tour_type_id
          LEFT JOIN locations l ON tp.location_id = l.location_id
          WHERE tp.package_id = '{$package_id}'";
        $package = $app->select_one($query);
        ?>

        <div class="main-content app-content">
            <div class="container-fluid">
                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                    <h1 class="page-title fw-semibold fs-18 mb-0">View Package Details</h1>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Tour Packages</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Package Details</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <!-- Basic Package Information Card -->
                <div class="card custom-card mb-4">
                    <div class="card-header justify-content-between">
                        <div class="card-title d-flex align-items-center">
                            <h4 class="mb-0"><?php echo $package->title ?></h4>
                            <span class="ms-2">
                                <?php
                                $statusClasses = [
                                    'active' => 'bg-success-transparent',
                                    'inactive' => 'bg-danger-transparent',
                                    'draft' => 'bg-warning-transparent'
                                ];
                                $statusClass = $statusClasses[$package->status] ?? 'bg-secondary-transparent';
                                ?>
                                <span
                                    class="badge <?php echo $statusClass ?>"><?php echo ucfirst($package->status) ?></span>
                            </span>
                        </div>
                        <div>
                            <a href="update-package?package_id=<?php echo $package->package_id ?>"
                                class="btn btn-sm btn-info">
                                <i class="ri-edit-line me-1"></i>Edit Package
                            </a>
                            <a href="view-packages" class="btn btn-sm btn-primary">
                                <i class="ri-arrow-left-line me-1"></i>Back to List
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Featured Image -->
                            <div class="col-lg-4">
                                <img src="http://localhost/dfcs/assets/img/package-images/<?php echo $package->featured_image ?>"
                                    class="img-fluid rounded" style="width: 100%; height: 300px; object-fit: cover;"
                                    alt="<?php echo $package->title ?>">
                            </div>

                            <!-- Basic Details -->
                            <div class="col-lg-8">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                            <div>
                                                <div class="fs-14 text-muted">Location</div>
                                                <div class="fw-semibold"><?php echo $package->location_name ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fas fa-tag text-primary me-2"></i>
                                            <div>
                                                <div class="fs-14 text-muted">Tour Type</div>
                                                <div class="fw-semibold"><?php echo $package->type_name ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fas fa-clock text-primary me-2"></i>
                                            <div>
                                                <div class="fs-14 text-muted">Duration</div>
                                                <div class="fw-semibold">
                                                    <?php echo $package->duration_days ?> Days /
                                                    <?php echo $package->duration_nights ?> Nights
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fas fa-dollar-sign text-primary me-2"></i>
                                            <div>
                                                <div class="fs-14 text-muted">Starting Price</div>
                                                <div class="fw-semibold">
                                                    KSH<?php echo number_format($package->display_price, 2) ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fas fa-users text-primary me-2"></i>
                                            <div>
                                                <div class="fs-14 text-muted">Group Size</div>
                                                <div class="fw-semibold">
                                                    Max: <?php echo $package->group_size_max ?> people
                                                    <?php if ($package->group_discount_percentage): ?>
                                                        <span class="badge bg-success-transparent ms-2">
                                                            <?php echo $package->group_discount_percentage ?>% Group
                                                            Discount
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fas fa-hiking text-primary me-2"></i>
                                            <div>
                                                <div class="fs-14 text-muted">Difficulty Level</div>
                                                <div class="fw-semibold">
                                                    <span class="badge bg-light text-dark">
                                                        <?php echo ucfirst($package->difficulty_level) ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Information Tabs -->
                <div class="card custom-card">
                    <div class="card-body">
                        <ul class="nav nav-tabs mb-4" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#description" role="tab">
                                    <i class="fas fa-info-circle me-2"></i>Description
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#pricing" role="tab">
                                    <i class="fas fa-dollar-sign me-2"></i>Pricing Details
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#hotels" role="tab">
                                    <i class="fas fa-hotel me-2"></i>Hotels
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#inclusions" role="tab">
                                    <i class="fas fa-list me-2"></i>Inclusions & Exclusions
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#itinerary" role="tab">
                                    <i class="fas fa-calendar-alt me-2"></i>Itinerary
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#gallery" role="tab">
                                    <i class="fas fa-images me-2"></i>Gallery
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#reviews" role="tab">
                                    <i class="fas fa-star me-2"></i>Reviews
                                </a>
                            </li>
                        </ul>
                        <!-- Tab Content -->
                        <div class="tab-content">
                            <!-- Description Tab -->
                            <div class="tab-pane fade show active" id="description" role="tabpanel">
                                <div class="content-section">
                                    <?php echo $package->description ?>
                                </div>
                            </div>

                            <!-- Pricing Details Tab -->
                            <div class="tab-pane fade" id="pricing" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-lg-6">
                                        <div class="card border">
                                            <div class="card-body">
                                                <h5 class="card-title mb-4">Standard Pricing</h5>
                                                <ul class="list-group list-group-flush">
                                                    <li
                                                        class="list-group-item d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <strong>Adult Price</strong>
                                                            <br><small class="text-muted">Per person</small>
                                                        </div>
                                                        <span
                                                            class="fs-5">KSH<?php echo number_format($package->price_per_adult, 2) ?></span>
                                                    </li>
                                                    <li
                                                        class="list-group-item d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <strong>Child Price</strong>
                                                            <br><small class="text-muted">Ages 4-12 years</small>
                                                        </div>
                                                        <span
                                                            class="fs-5">KSH<?php echo number_format($package->price_per_child, 2) ?></span>
                                                    </li>
                                                    <li
                                                        class="list-group-item d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <strong>Infant Price</strong>
                                                            <br><small class="text-muted">Ages 0-3 years</small>
                                                        </div>
                                                        <span
                                                            class="fs-5">KSH<?php echo number_format($package->price_per_infant, 2) ?></span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="card border">
                                            <div class="card-body">
                                                <h5 class="card-title mb-4">Group Pricing</h5>
                                                <?php if ($package->group_discount_percentage > 0): ?>
                                                    <div class="alert alert-success mb-4">
                                                        <i class="fas fa-users me-2"></i>
                                                        <strong><?php echo $package->group_discount_percentage ?>%
                                                            discount</strong> for groups of
                                                        <?php echo $package->min_people_for_group_discount ?> or more
                                                    </div>
                                                    <div class="small text-muted">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        Maximum group size: <?php echo $package->group_size_max ?> people
                                                    </div>
                                                <?php else: ?>
                                                    <p class="text-muted">No group discounts available for this package.</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hotels Tab -->
                            <div class="tab-pane fade" id="hotels" role="tabpanel">
                                <div class="row g-3">
                                    <?php
                                    $query = "SELECT h.* FROM hotels h 
                     JOIN package_hotel_mappings phm ON h.hotel_id = phm.hotel_id 
                     WHERE phm.package_id = '{$package_id}'";
                                    $hotels = $app->select_all($query);
                                    foreach ($hotels as $hotel):
                                    ?>
                                        <div class="col-md-4">
                                            <div class="card h-100">
                                                <img src="../../assets/img/hotels/<?php echo $hotel->hotel_image ?>"
                                                    class="card-img-top" style="height: 200px; object-fit: cover;"
                                                    alt="<?php echo $hotel->hotel_name ?>">
                                                <div class="card-body">
                                                    <h5 class="card-title"><?php echo $hotel->hotel_name ?></h5>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Inclusions & Exclusions Tab -->
                            <div class="tab-pane fade" id="inclusions" role="tabpanel">
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <h5 class="mb-3">What's Included</h5>
                                        <div class="list-group">
                                            <?php
                                            $query = "SELECT * FROM package_inclusions WHERE package_id = '{$package_id}'";
                                            $inclusions = $app->select_all($query);
                                            foreach ($inclusions as $inclusion):
                                            ?>
                                                <div class="list-group-item">
                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                    <?php echo $inclusion->inclusion_item ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <h5 class="mb-3">What's Not Included</h5>
                                        <div class="list-group">
                                            <?php
                                            $query = "SELECT * FROM package_exclusions WHERE package_id = '{$package_id}'";
                                            $exclusions = $app->select_all($query);
                                            foreach ($exclusions as $exclusion):
                                            ?>
                                                <div class="list-group-item">
                                                    <i class="fas fa-times-circle text-danger me-2"></i>
                                                    <?php echo $exclusion->exclusion_item ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Itinerary Tab -->
                            <div class="tab-pane fade" id="itinerary" role="tabpanel">
                                <div class="vertical-timeline">
                                    <?php
                                    $query = "SELECT * FROM package_itinerary WHERE package_id = '{$package_id}' ORDER BY day_number";
                                    $itinerary = $app->select_all($query);
                                    foreach ($itinerary as $day):
                                    ?>
                                        <div class="timeline-item mb-4">
                                            <div class="row">
                                                <div class="col-auto">
                                                    <div class="timeline-badge bg-primary">
                                                        Day <?php echo $day->day_number ?>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <h5 class="card-title"><?php echo $day->day_title ?></h5>
                                                            <p class="card-text"><?php echo $day->day_description ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Gallery Tab -->
                            <div class="tab-pane fade" id="gallery" role="tabpanel">
                                <?php
                                $query = "SELECT * FROM package_gallery WHERE package_id = '{$package_id}'";
                                $gallery = $app->select_one($query);
                                if ($gallery):
                                ?>
                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <img src="../../assets/img/package-images/<?php echo $gallery->image_1 ?>"
                                                class="img-fluid rounded" alt="Gallery Image 1"
                                                style="width: 100%; height: 400px; object-fit: cover;">
                                        </div>
                                        <div class="col-md-4">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <img src="../../assets/img/package-images/<?php echo $gallery->image_2 ?>"
                                                        class="img-fluid rounded" alt="Gallery Image 2"
                                                        style="width: 100%; height: 190px; object-fit: cover;">
                                                </div>
                                                <div class="col-12">
                                                    <img src="../../assets/img/package-images/<?php echo $gallery->image_3 ?>"
                                                        class="img-fluid rounded" alt="Gallery Image 3"
                                                        style="width: 100%; height: 190px; object-fit: cover;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Reviews Tab -->
                            <div class="tab-pane fade" id="reviews" role="tabpanel">
                                <?php
                                $query = "SELECT * FROM package_reviews WHERE package_id = '{$package_id}' ORDER BY created_at DESC";
                                $reviews = $app->select_all($query);
                                if ($reviews):
                                ?>
                                    <div class="row mb-4">
                                        <div class="col-lg-4">
                                            <?php
                                            $totalRating = 0;
                                            $ratingCount = count($reviews);
                                            foreach ($reviews as $review) {
                                                $totalRating += $review->overall_rating;
                                            }
                                            $averageRating = $ratingCount > 0 ? $totalRating / $ratingCount : 0;
                                            ?>
                                            <div class="text-center p-4 border rounded">
                                                <h1 class="display-4 fw-bold text-warning">
                                                    <?php echo number_format($averageRating, 1) ?></h1>
                                                <div class="text-warning mb-2">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <i
                                                            class="fas fa-star<?php echo $i <= $averageRating ? '' : '-o' ?>"></i>
                                                    <?php endfor; ?>
                                                </div>
                                                <p class="text-muted">Based on <?php echo $ratingCount ?> reviews</p>
                                            </div>
                                        </div>
                                        <div class="col-lg-8">
                                            <div class="review-list">
                                                <?php foreach ($reviews as $review): ?>
                                                    <div class="card mb-3">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <div>
                                                                    <h6 class="mb-0"><?php echo $review->reviewer_name ?></h6>
                                                                    <small class="text-muted">
                                                                        <?php echo date('F d, Y', strtotime($review->created_at)) ?>
                                                                    </small>
                                                                </div>
                                                                <div class="text-warning">
                                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                        <i
                                                                            class="fas fa-star<?php echo $i <= $review->overall_rating ? '' : '-o' ?> small"></i>
                                                                    <?php endfor; ?>
                                                                </div>
                                                            </div>
                                                            <p class="mb-0"><?php echo $review->comment ?></p>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i class="fas fa-star text-muted fa-3x mb-3"></i>
                                        <h5>No Reviews Yet</h5>
                                        <p class="text-muted">Be the first to review this package!</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <!-- Related Packages Section -->
                    <div class="card custom-card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-umbrella-beach me-2"></i>Similar Packages
                                <small class="text-muted">More <?php echo $package->type_name ?> packages</small>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <?php
                                // Get similar packages of same type, excluding current package
                                $query = "SELECT tp.*, tt.type_name, l.location_name,
                            (SELECT COUNT(*) FROM package_reviews pr WHERE pr.package_id = tp.package_id AND pr.status = 'approved') as review_count,
                            (SELECT AVG(overall_rating) FROM package_reviews pr WHERE pr.package_id = tp.package_id AND pr.status = 'approved') as avg_rating
                     FROM tour_packages tp
                     LEFT JOIN tour_types tt ON tp.tour_type_id = tt.tour_type_id
                     LEFT JOIN locations l ON tp.location_id = l.location_id
                     WHERE tp.tour_type_id = '{$package->tour_type_id}' 
                     AND tp.package_id != '{$package_id}'
                     AND tp.status = 'active'
                     LIMIT 3";
                                $related_packages = $app->select_all($query);

                                foreach ($related_packages as $related):
                                ?>
                                    <div class="col-md-4">
                                        <div class="card h-100">
                                            <img src="../../assets/img/package-images/<?php echo $related->featured_image ?>"
                                                class="card-img-top" alt="<?php echo $related->title ?>"
                                                style="height: 200px; object-fit: cover;">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h5 class="card-title mb-0"><?php echo $related->title ?></h5>
                                                    <span class="badge" style="background-color: #F59521!important;">
                                                        $<?php echo number_format($related->display_price, 0) ?>
                                                    </span>
                                                </div>
                                                <div class="mb-3">
                                                    <small class="text-muted">
                                                        <i
                                                            class="fas fa-map-marker-alt me-1"></i><?php echo $related->location_name ?>
                                                    </small>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <small class="d-block text-muted">
                                                            <i class="fas fa-clock me-1"></i>
                                                            <?php echo $related->duration_days ?> Days /
                                                            <?php echo $related->duration_nights ?> Nights
                                                        </small>
                                                    </div>
                                                    <div class="text-warning small">
                                                        <?php
                                                        $rating = $related->avg_rating ?? 0;
                                                        for ($i = 1; $i <= 5; $i++):
                                                        ?>
                                                            <i class="fas fa-star<?php echo $i <= $rating ? '' : '-o' ?>"></i>
                                                        <?php endfor; ?>
                                                        <span
                                                            class="text-muted ms-1">(<?php echo $related->review_count ?>)</span>
                                                    </div>
                                                </div>
                                                <div class="mt-3">
                                                    <?php if ($related->group_discount_percentage > 0): ?>
                                                        <div class="small text-success mb-2">
                                                            <i class="fas fa-users me-1"></i>
                                                            <?php echo $related->group_discount_percentage ?>% Group Discount
                                                            Available
                                                        </div>
                                                    <?php endif; ?>
                                                    <a href="view-package?id=<?php echo $related->package_id ?>"
                                                        class="btn btn-outline-primary btn-sm w-100">
                                                        View Details
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <?php if (empty($related_packages)): ?>
                                    <div class="col-12">
                                        <div class="text-center py-4">
                                            <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                                            <p class="text-muted mb-0">No similar packages found at the moment.</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>


                </div>
                <!-- End::app-content -->


                <!-- footer start -->
                <?php include "../includes/footer.php" ?>
                <!-- Footer End -->

            </div>


            <!-- Scroll To Top -->
            <div class="scrollToTop">
                <span class="arrow"><i class="ri-arrow-up-s-fill fs-20"></i></span>
            </div>
            <div id="responsive-overlay"></div>
            <!-- Scroll To Top -->
            <script src="http://localhost/dfcs/assets/libs/%40popperjs/core/umd/popper.min.js">
            </script>
            <!-- Bootstrap JS -->
            <script src="http://localhost/dfcs/assets/libs/bootstrap/js/bootstrap.bundle.min.js">
            </script>
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
            <script src="http://localhost/dfcs/assets/libs/%40simonwep/pickr/pickr.es5.min.js">
            </script>
            <!-- Custom-Switcher JS -->
            <script src="http://localhost/dfcs/assets/js/custom-switcher.min.js"></script>
            <!-- Prism JS -->
            <script src="http://localhost/dfcs/assets/libs/prismjs/prism.js"></script>
            <script src="http://localhost/dfcs/assets/js/prism-custom.js"></script>
            <!-- Custom JS -->
            <script src="http://localhost/dfcs/assets/js/custom.js"></script>
            <!-- summernote -->
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css">


            <!-- end of footer links -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
                integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
                crossorigin="anonymous" referrerpolicy="no-referrer"></script>
            <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js">
            </script>
            <!-- Toastr JS -->
            <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4"></script>
            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
            <script>
                $(document).ready(function() {
                    // Initialize Select2
                    $('.select2-multiple').select2({
                        maximumSelectionLength: 3,
                        placeholder: 'Select up to 3 tags',
                        allowClear: true
                    });

                    // Initialize Summernote editors
                    $('#summernote, #summernote1').summernote({
                        height: 300,
                        toolbar: [
                            ['style', ['style']],
                            ['font', ['bold', 'underline', 'clear']],
                            ['color', ['color']],
                            ['para', ['ul', 'ol', 'paragraph']],
                            ['table', ['table']],
                            ['insert', ['link', 'picture']],
                            ['view', ['fullscreen', 'codeview']]
                        ]
                    });

                    // Tab Navigation
                    $('#nextBasic').click(function() {
                        if (validateBasicInfo()) {
                            $('#postTabs button[data-bs-target="#tags-categories"]').tab(
                                'show');
                        }
                    });

                    $('#prevTags').click(function() {
                        $('#postTabs button[data-bs-target="#basic-info"]').tab('show');
                    });

                    $('#nextTags').click(function() {
                        if (validateTagsCategories()) {
                            $('#postTabs button[data-bs-target="#content-start"]').tab('show');
                        }
                    });

                    $('#prevContentStart').click(function() {
                        $('#postTabs button[data-bs-target="#tags-categories"]').tab('show');
                    });

                    $('#nextContentStart').click(function() {
                        if (validateContentStart()) {
                            $('#postTabs button[data-bs-target="#content-end"]').tab('show');
                        }
                    });

                    $('#prevContentEnd').click(function() {
                        $('#postTabs button[data-bs-target="#content-start"]').tab('show');
                    });

                    $('#nextContentEnd').click(function() {
                        if (validateContentEnd()) {
                            $('#postTabs button[data-bs-target="#media"]').tab('show');
                        }
                    });

                    $('#prevMedia').click(function() {
                        $('#postTabs button[data-bs-target="#content-end"]').tab('show');
                    });
                });

                // Validation functions
                function validateBasicInfo() {
                    if (!$('#post-title-add').val().trim()) {
                        toastr.error('Please enter post title');
                        return false;
                    }
                    if (!$('#post-status-add').val()) {
                        toastr.error('Please select post status');
                        return false;
                    }
                    return true;
                }

                function validateTagsCategories() {
                    if (!$('#post-category-add').val()) {
                        toastr.error('Please select a category');
                        return false;
                    }
                    if ($('#post-tags-add').val().length === 0) {
                        toastr.error('Please select at least one tag');
                        return false;
                    }
                    return true;
                }

                function validateContentStart() {
                    if ($('#summernote').summernote('isEmpty')) {
                        toastr.error('Please enter content start');
                        return false;
                    }
                    return true;
                }

                function validateContentEnd() {
                    if ($('#summernote1').summernote('isEmpty')) {
                        toastr.error('Please enter content end');
                        return false;
                    }
                    return true;
                }

                function validateAndSubmit() {
                    if (!validateBasicInfo() || !validateTagsCategories() ||
                        !validateContentStart() || !validateContentEnd()) {
                        return;
                    }

                    let formData = new FormData();
                    formData.append('postTitle', $('#post-title-add').val().trim());
                    formData.append('postStatus', $('#post-status-add').val());
                    formData.append('postRemark', $('#post-remark-add').val().trim());
                    formData.append('postCategory', $('#post-category-add').val());
                    formData.append('postTags', JSON.stringify($('#post-tags-add').val()));
                    formData.append('postContentStart', $('#summernote').summernote('code'));
                    formData.append('postContentEnd', $('#summernote1').summernote('code'));

                    if ($('#post-image-1')[0].files[0]) {
                        formData.append('postImage1', $('#post-image-1')[0].files[0]);
                    }
                    if ($('#post-image-2')[0].files[0]) {
                        formData.append('postImage2', $('#post-image-2')[0].files[0]);
                    }

                    $.ajax({
                        url: "http://localhost/dfcs/ajax/post-controller/add-post.php",
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            try {
                                let data = JSON.parse(response);
                                if (data.success) {
                                    toastr.success('Post added successfully');
                                    setTimeout(() => {
                                        window.location.href = 'view-data';
                                    }, 2000);
                                } else {
                                    toastr.error(data.message || 'Error adding post');
                                }
                            } catch (e) {
                                toastr.error('Error processing response');
                            }
                        },
                        error: function() {
                            toastr.error('Error adding post');
                        }
                    });
                }
            </script>

</body>

</html>