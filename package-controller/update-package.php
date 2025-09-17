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

        <!-- Start::app-content -->
        <div class="main-content app-content">
            <div class="container-fluid">

                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
                    <h1 class="page-title fw-semibold fs-18 mb-0">Edit Package</h1>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Tour Packages</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Package</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <!-- Start::row-1 -->
                <!-- get the form data -->
                <!-- Start::row-1 -->
                <?php
                $app = new App;
                $package_id = $_GET['package_id'];
                $query = "SELECT tp.*, tt.type_name, l.location_name 
          FROM tour_packages tp
          LEFT JOIN tour_types tt ON tp.tour_type_id = tt.tour_type_id
          LEFT JOIN locations l ON tp.location_id = l.location_id
          WHERE tp.package_id = '{$package_id}'";
                $package = $app->select_one($query);
                ?>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-header justify-content-between">
                                <div class="card-title">Update Package</div>
                            </div>
                            <div class="card-body add-products p-0">
                                <!-- Tabs Navigation -->
                                <ul class="nav nav-tabs" id="packageTabs" role="tablist">
                                    <li class="nav-item">
                                        <button class="nav-link active" data-bs-toggle="tab"
                                            data-bs-target="#basic-info" type="button" role="tab">
                                            <i class="bi bi-info-circle me-1"></i>Basic Details
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#pricing-info"
                                            type="button" role="tab">
                                            <i class="bi bi-currency-dollar me-1"></i>Pricing Details
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" data-bs-toggle="tab"
                                            data-bs-target="#description-gallery" type="button" role="tab">
                                            <i class="bi bi-images me-1"></i>Description & Gallery
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#hotels"
                                            type="button" role="tab">
                                            <i class="bi bi-building me-1"></i>Hotels
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" data-bs-toggle="tab"
                                            data-bs-target="#inclusions-exclusions" type="button" role="tab">
                                            <i class="bi bi-list-check me-1"></i>Inclusions & Exclusions
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#itinerary"
                                            type="button" role="tab">
                                            <i class="bi bi-calendar-event me-1"></i>Itinerary
                                        </button>
                                    </li>
                                </ul>

                                <!-- Tab Content -->
                                <div class="tab-content p-4">
                                    <!-- Basic Info Tab -->
                                    <div class="tab-pane fade show active" id="basic-info" role="tabpanel">
                                        <div class="row gy-3">
                                            <div class="col-xl-6">
                                                <input type="hidden" id="package-id" value="<?php echo $package_id; ?>">
                                                <label class="form-label">Package Title</label>
                                                <input type="text" class="form-control" id="package-title"
                                                    value="<?php echo $package->title; ?>">
                                            </div>
                                            <div class="col-xl-6">
                                                <label class="form-label">Tour Type</label>
                                                <select class="form-control" id="tour-type">
                                                    <option value="">Select Tour Type</option>
                                                    <?php
                                                    $query = "SELECT * FROM tour_types ORDER BY type_name ASC";
                                                    $types = $app->select_all($query);
                                                    if ($types):
                                                        foreach ($types as $type):
                                                    ?>
                                                            <option value="<?php echo $type->tour_type_id ?>"
                                                                <?php echo ($package->tour_type_id == $type->tour_type_id) ? 'selected' : ''; ?>>
                                                                <?php echo $type->type_name ?>
                                                            </option>
                                                    <?php
                                                        endforeach;
                                                    endif;
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-xl-6">
                                                <label class="form-label">Location</label>
                                                <select class="form-control" id="location">
                                                    <option value="">Select Location</option>
                                                    <?php
                                                    $query = "SELECT * FROM locations ORDER BY location_name ASC";
                                                    $locations = $app->select_all($query);
                                                    if ($locations):
                                                        foreach ($locations as $location):
                                                    ?>
                                                            <option value="<?php echo $location->location_id ?>"
                                                                <?php echo ($package->location_id == $location->location_id) ? 'selected' : ''; ?>>
                                                                <?php echo $location->location_name ?>
                                                            </option>
                                                    <?php
                                                        endforeach;
                                                    endif;
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-xl-3">
                                                <label class="form-label">Duration (Days)</label>
                                                <input type="number" class="form-control" id="duration-days" min="1"
                                                    value="<?php echo $package->duration_days; ?>">
                                            </div>
                                            <div class="col-xl-3">
                                                <label class="form-label">Duration (Nights)</label>
                                                <input type="number" class="form-control" id="duration-nights" min="0"
                                                    value="<?php echo $package->duration_nights; ?>">
                                            </div>
                                            <div class="col-xl-4">
                                                <label class="form-label">Minimum Age</label>
                                                <input type="number" class="form-control" id="minimum-age" min="0"
                                                    value="<?php echo $package->minimum_age; ?>">
                                            </div>
                                            <div class="col-xl-4">
                                                <label class="form-label">Difficulty Level</label>
                                                <select class="form-control" id="difficulty-level">
                                                    <option value="easy"
                                                        <?php echo ($package->difficulty_level == 'easy') ? 'selected' : ''; ?>>
                                                        Easy</option>
                                                    <option value="moderate"
                                                        <?php echo ($package->difficulty_level == 'moderate') ? 'selected' : ''; ?>>
                                                        Moderate</option>
                                                    <option value="challenging"
                                                        <?php echo ($package->difficulty_level == 'challenging') ? 'selected' : ''; ?>>
                                                        Challenging</option>
                                                </select>
                                            </div>
                                            <div class="col-xl-4">
                                                <label class="form-label">Status</label>
                                                <select class="form-control" id="package-status">
                                                    <option value="active"
                                                        <?php echo ($package->status == 'active') ? 'selected' : ''; ?>>
                                                        Active</option>
                                                    <option value="inactive"
                                                        <?php echo ($package->status == 'inactive') ? 'selected' : ''; ?>>
                                                        Inactive</option>
                                                    <option value="draft"
                                                        <?php echo ($package->status == 'draft') ? 'selected' : ''; ?>>
                                                        Draft</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end mt-3">
                                            <button class="btn text-white" id="nextBasic" style="background:#6AA32D;">
                                                Next <i class="bi bi-arrow-right ms-2"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Should I continue with the remaining tabs (Pricing, Description, Hotels, etc.)? -->
                                    <!-- Pricing Info Tab -->
                                    <div class="tab-pane fade" id="pricing-info" role="tabpanel">
                                        <div class="row gy-3">
                                            <div class="col-xl-6">
                                                <label class="form-label">Display Price (Starting From)</label>
                                                <input type="number" class="form-control" id="display-price" min="0"
                                                    step="0.01" value="<?php echo $package->display_price; ?>">
                                            </div>
                                            <div class="col-xl-6">
                                                <label class="form-label">Price Per Adult</label>
                                                <input type="number" class="form-control" id="adult-price" min="0"
                                                    step="0.01" value="<?php echo $package->price_per_adult; ?>">
                                            </div>
                                            <div class="col-xl-6">
                                                <label class="form-label">Price Per Child (4-12 years)</label>
                                                <input type="number" class="form-control" id="child-price" min="0"
                                                    step="0.01" value="<?php echo $package->price_per_child; ?>">
                                            </div>
                                            <div class="col-xl-6">
                                                <label class="form-label">Price Per Infant (0-3 years)</label>
                                                <input type="number" class="form-control" id="infant-price" min="0"
                                                    step="0.01" value="<?php echo $package->price_per_infant; ?>">
                                            </div>
                                            <div class="col-xl-4">
                                                <label class="form-label">Group Discount (%)</label>
                                                <input type="number" class="form-control" id="group-discount" min="0"
                                                    max="100"
                                                    value="<?php echo $package->group_discount_percentage; ?>">
                                            </div>
                                            <div class="col-xl-4">
                                                <label class="form-label">Min People for Group Discount</label>
                                                <input type="number" class="form-control" id="min-people-discount"
                                                    min="2"
                                                    value="<?php echo $package->min_people_for_group_discount; ?>">
                                            </div>
                                            <div class="col-xl-4">
                                                <label class="form-label">Maximum Group Size</label>
                                                <input type="number" class="form-control" id="max-group-size" min="1"
                                                    value="<?php echo $package->group_size_max; ?>">
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between mt-3">
                                            <button class="btn btn-light" id="prevPricing">
                                                <i class="bi bi-arrow-left me-2"></i>Previous
                                            </button>
                                            <button class="btn text-white" id="nextPricing" style="background:#6AA32D;">
                                                Next <i class="bi bi-arrow-right ms-2"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Description & Gallery Tab -->
                                    <div class="tab-pane fade" id="description-gallery" role="tabpanel">
                                        <div class="row gy-4">
                                            <div class="col-xl-12">
                                                <label class="form-label">Package Description</label>
                                                <textarea id="package-description"
                                                    class="form-control"><?php echo $package->description; ?></textarea>
                                            </div>
                                            <div class="col-xl-12">
                                                <label class="form-label">Featured Image</label>
                                                <input type="file" class="form-control" id="featured-image"
                                                    accept="image/*">
                                                <?php if ($package->featured_image): ?>
                                                    <div class="mt-2">
                                                        <img src="../../assets/img/package-images/<?php echo $package->featured_image; ?>"
                                                            class="img-thumbnail" style="height: 100px;"
                                                            alt="Current Featured Image">
                                                        <small class="d-block text-muted mt-1">Current featured image.
                                                            Upload new to replace.</small>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-xl-12">
                                                <label class="form-label">Gallery Images</label>
                                                <input type="file" class="form-control" id="gallery-images"
                                                    accept="image/*" multiple>
                                                <small class="text-muted">Select 3 images (1 main large, 2
                                                    small)</small>

                                                <?php
                                                // Fetch current gallery images
                                                $query = "SELECT * FROM package_gallery WHERE package_id = '{$package_id}'";
                                                $gallery = $app->select_one($query);
                                                if ($gallery):
                                                ?>
                                                    <div class="row mt-3">
                                                        <div class="col-md-6">
                                                            <div class="current-gallery-image">
                                                                <img src="../../assets/img/package-images/<?php echo $gallery->image_1; ?>"
                                                                    class="img-thumbnail" style="height: 150px;"
                                                                    alt="Gallery Image 1">
                                                                <small class="d-block text-muted mt-1">Main gallery
                                                                    image</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="current-gallery-image">
                                                                <img src="../../assets/img/package-images/<?php echo $gallery->image_2; ?>"
                                                                    class="img-thumbnail" style="height: 100px;"
                                                                    alt="Gallery Image 2">
                                                                <small class="d-block text-muted mt-1">Small image 1</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="current-gallery-image">
                                                                <img src="../../assets/img/package-images/<?php echo $gallery->image_3; ?>"
                                                                    class="img-thumbnail" style="height: 100px;"
                                                                    alt="Gallery Image 3">
                                                                <small class="d-block text-muted mt-1">Small image 2</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <small class="text-muted d-block mt-2">Upload new images to replace the
                                                        current ones</small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between mt-3">
                                            <button class="btn btn-light" id="prevDescription">
                                                <i class="bi bi-arrow-left me-2"></i>Previous
                                            </button>
                                            <button class="btn text-white" id="nextDescription"
                                                style="background:#6AA32D;">
                                                Next <i class="bi bi-arrow-right ms-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <!-- Hotels Tab -->
                                    <div class="tab-pane fade" id="hotels" role="tabpanel">
                                        <div class="row gy-3">
                                            <div class="col-xl-12">
                                                <label class="form-label">Select Hotels</label>
                                                <?php
                                                // Get current package hotels
                                                $query = "SELECT hotel_id FROM package_hotel_mappings WHERE package_id = '{$package_id}'";
                                                $current_hotels = $app->select_all($query);
                                                $selected_hotel_ids = array_map(function ($hotel) {
                                                    return $hotel->hotel_id;
                                                }, $current_hotels);
                                                ?>
                                                <select class="form-control select2-multiple" id="package-hotels"
                                                    multiple>
                                                    <!-- Hotels will be loaded dynamically based on location -->
                                                </select>
                                            </div>

                                            <div class="col-xl-12 mt-3">
                                                <div id="selected-hotels-preview" class="row g-3">
                                                    <!-- Selected hotels preview will appear here -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between mt-3">
                                            <button class="btn btn-light" id="prevHotels">
                                                <i class="bi bi-arrow-left me-2"></i>Previous
                                            </button>
                                            <button class="btn text-white" id="nextHotels" style="background:#6AA32D;">
                                                Next <i class="bi bi-arrow-right ms-2"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Inclusions & Exclusions Tab -->
                                    <div class="tab-pane fade" id="inclusions-exclusions" role="tabpanel">
                                        <div class="row gy-4">
                                            <div class="col-xl-6">
                                                <label class="form-label">Package Inclusions</label>
                                                <div id="inclusions-container">
                                                    <?php
                                                    // Get current inclusions
                                                    $query = "SELECT * FROM package_inclusions WHERE package_id = '{$package_id}'";
                                                    $inclusions = $app->select_all($query);
                                                    if ($inclusions):
                                                        foreach ($inclusions as $inclusion):
                                                    ?>
                                                            <div class="input-group mb-2">
                                                                <input type="text" class="form-control inclusion-item"
                                                                    value="<?php echo $inclusion->inclusion_item; ?>">
                                                                <button class="btn btn-danger remove-field" type="button">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </div>
                                                    <?php
                                                        endforeach;
                                                    endif;
                                                    ?>
                                                    <div class="input-group mb-2">
                                                        <input type="text" class="form-control inclusion-item"
                                                            placeholder="Add inclusion">
                                                        <button class="btn btn-success add-inclusion" type="button">
                                                            <i class="bi bi-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-6">
                                                <label class="form-label">Package Exclusions</label>
                                                <div id="exclusions-container">
                                                    <?php
                                                    // Get current exclusions
                                                    $query = "SELECT * FROM package_exclusions WHERE package_id = '{$package_id}'";
                                                    $exclusions = $app->select_all($query);
                                                    if ($exclusions):
                                                        foreach ($exclusions as $exclusion):
                                                    ?>
                                                            <div class="input-group mb-2">
                                                                <input type="text" class="form-control exclusion-item"
                                                                    value="<?php echo $exclusion->exclusion_item; ?>">
                                                                <button class="btn btn-danger remove-field" type="button">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </div>
                                                    <?php
                                                        endforeach;
                                                    endif;
                                                    ?>
                                                    <div class="input-group mb-2">
                                                        <input type="text" class="form-control exclusion-item"
                                                            placeholder="Add exclusion">
                                                        <button class="btn btn-success add-exclusion" type="button">
                                                            <i class="bi bi-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between mt-3">
                                            <button class="btn btn-light" id="prevInclusions">
                                                <i class="bi bi-arrow-left me-2"></i>Previous
                                            </button>
                                            <button class="btn text-white" id="nextInclusions"
                                                style="background:#6AA32D;">
                                                Next <i class="bi bi-arrow-right ms-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <!-- next one  -->
                                    <!-- Itinerary Tab -->
                                    <div class="tab-pane fade" id="itinerary" role="tabpanel">
                                        <div id="itinerary-days">
                                            <?php
                                            // Get current itinerary
                                            $query = "SELECT * FROM package_itinerary WHERE package_id = '{$package_id}' ORDER BY day_number ASC";
                                            $itinerary_days = $app->select_all($query);
                                            if ($itinerary_days):
                                                foreach ($itinerary_days as $day):
                                            ?>
                                                    <div class="itinerary-day mb-4">
                                                        <div class="row gy-3">
                                                            <div
                                                                class="col-xl-12 d-flex justify-content-between align-items-center">
                                                                <label class="form-label">Day <?php echo $day->day_number; ?>
                                                                    Title</label>
                                                                <?php if ($day->day_number > 1): ?>
                                                                    <button class="btn btn-sm btn-danger remove-day">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="col-xl-12">
                                                                <input type="text" class="form-control day-title"
                                                                    placeholder="Enter day title"
                                                                    value="<?php echo $day->day_title; ?>">
                                                            </div>
                                                            <div class="col-xl-12">
                                                                <label class="form-label">Day <?php echo $day->day_number; ?>
                                                                    Description</label>
                                                                <textarea class="form-control day-description"
                                                                    rows="3"><?php echo $day->day_description; ?></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php
                                                endforeach;
                                            else:
                                                // If no itinerary exists, show default first day
                                                ?>
                                                <div class="itinerary-day mb-4">
                                                    <div class="row gy-3">
                                                        <div class="col-xl-12">
                                                            <label class="form-label">Day 1 Title</label>
                                                            <input type="text" class="form-control day-title"
                                                                placeholder="Enter day title">
                                                        </div>
                                                        <div class="col-xl-12">
                                                            <label class="form-label">Day 1 Description</label>
                                                            <textarea class="form-control day-description"
                                                                rows="3"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <button class="btn btn-success mb-4" id="add-day">
                                            <i class="bi bi-plus-circle me-2"></i>Add Another Day
                                        </button>
                                        <div class="d-flex justify-content-between">
                                            <button class="btn btn-light" id="prevItinerary">
                                                <i class="bi bi-arrow-left me-2"></i>Previous
                                            </button>
                                            <button class="btn text-white" onclick="updatePackage()"
                                                style="background:#6AA32D;">
                                                Update Package <i class="bi bi-check-lg ms-2"></i>
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
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
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2-multiple').select2({
                theme: 'bootstrap-5',
                placeholder: 'Select options',
                allowClear: true
            });

            // Initialize Summernote
            $('#package-description').summernote({
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

            // Initialize handlers
            $('.add-inclusion').click(addInclusionField);
            $('.add-exclusion').click(addExclusionField);
            $('#add-day').click(addItineraryDay);

            // Remove field handlers
            $(document).on('click', '.remove-field', function() {
                $(this).closest('.input-group').remove();
            });

            $(document).on('click', '.remove-day', function() {
                $(this).closest('.itinerary-day').remove();
                reorderDays();
            });

            // Load hotels if location is selected
            let initialLocationId = $('#location').val();
            if (initialLocationId) {
                loadHotels(initialLocationId);
            }

            // Location change handler
            $('#location').change(function() {
                let locationId = $(this).val();
                if (locationId) {
                    loadHotels(locationId);
                } else {
                    $('#package-hotels').empty();
                    $('#selected-hotels-preview').empty();
                }
            });

            // Tab Navigation
            $('#nextBasic').click(() => {
                if (validateBasicInfo()) {
                    $('#packageTabs button[data-bs-target="#pricing-info"]').tab('show');
                }
            });

            $('#prevPricing').click(() => {
                $('#packageTabs button[data-bs-target="#basic-info"]').tab('show');
            });

            $('#nextPricing').click(() => {
                if (validatePricing()) {
                    $('#packageTabs button[data-bs-target="#description-gallery"]').tab('show');
                }
            });

            $('#prevDescription').click(() => {
                $('#packageTabs button[data-bs-target="#pricing-info"]').tab('show');
            });

            $('#nextDescription').click(() => {
                if (validateDescription()) {
                    $('#packageTabs button[data-bs-target="#hotels"]').tab('show');
                }
            });

            $('#prevHotels').click(() => {
                $('#packageTabs button[data-bs-target="#description-gallery"]').tab('show');
            });

            $('#nextHotels').click(() => {
                if (validateHotels()) {
                    $('#packageTabs button[data-bs-target="#inclusions-exclusions"]').tab('show');
                }
            });

            $('#prevInclusions').click(() => {
                $('#packageTabs button[data-bs-target="#hotels"]').tab('show');
            });

            $('#nextInclusions').click(() => {
                if (validateInclusionsExclusions()) {
                    $('#packageTabs button[data-bs-target="#itinerary"]').tab('show');
                }
            });

            $('#prevItinerary').click(() => {
                $('#packageTabs button[data-bs-target="#inclusions-exclusions"]').tab('show');
            });
        });

        // Validation Functions
        function validateBasicInfo() {
            const requiredFields = {
                'package-title': 'Package title',
                'tour-type': 'Tour type',
                'location': 'Location',
                'duration-days': 'Duration days'
            };

            for (const [id, name] of Object.entries(requiredFields)) {
                const value = $(`#${id}`).val();
                if (!value || (id === 'duration-days' && value < 1)) {
                    toastr.error(`Please enter valid ${name}`);
                    return false;
                }
            }
            return true;
        }

        function validatePricing() {
            const requiredFields = {
                'display-price': 'Display price',
                'adult-price': 'Adult price',
                'child-price': 'Child price'
            };

            for (const [id, name] of Object.entries(requiredFields)) {
                const value = $(`#${id}`).val();
                if (!value || value < 0) {
                    toastr.error(`Please enter valid ${name}`);
                    return false;
                }
            }
            return true;
        }

        function validateDescription() {
            if ($('#package-description').summernote('isEmpty')) {
                toastr.error('Please enter package description');
                return false;
            }
            return true;
        }

        function validateHotels() {
            if ($('#package-hotels').val().length === 0) {
                toastr.error('Please select at least one hotel');
                return false;
            }
            return true;
        }

        function validateInclusionsExclusions() {
            const inclusions = $('.inclusion-item').map(function() {
                return $(this).val().trim();
            }).get().filter(Boolean);

            if (inclusions.length === 0) {
                toastr.error('Please add at least one inclusion');
                return false;
            }
            return true;
        }

        function validateItinerary() {
            let valid = true;
            $('.itinerary-day').each(function(index) {
                const title = $(this).find('.day-title').val().trim();
                const description = $(this).find('.day-description').val().trim();

                if (!title || !description) {
                    toastr.error(`Please fill all details for Day ${index + 1}`);
                    valid = false;
                    return false;
                }
            });
            return valid;
        }

        // Dynamic Field Functions
        function addInclusionField() {
            const field = `
        <div class="input-group mb-2">
            <input type="text" class="form-control inclusion-item" placeholder="Add inclusion">
            <button class="btn btn-danger remove-field" type="button">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
            $(field).insertBefore('#inclusions-container .add-inclusion').closest('.input-group');
        }

        function addExclusionField() {
            const field = `
        <div class="input-group mb-2">
            <input type="text" class="form-control exclusion-item" placeholder="Add exclusion">
            <button class="btn btn-danger remove-field" type="button">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
            $(field).insertBefore('#exclusions-container .add-exclusion').closest('.input-group');
        }

        function addItineraryDay() {
            const dayCount = $('.itinerary-day').length + 1;
            const newDay = `
        <div class="itinerary-day mb-4">
            <div class="row gy-3">
                <div class="col-xl-12 d-flex justify-content-between align-items-center">
                    <label class="form-label">Day ${dayCount} Title</label>
                    <button class="btn btn-sm btn-danger remove-day">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <div class="col-xl-12">
                    <input type="text" class="form-control day-title" placeholder="Enter day title">
                </div>
                <div class="col-xl-12">
                    <label class="form-label">Day ${dayCount} Description</label>
                    <textarea class="form-control day-description" rows="3"></textarea>
                </div>
            </div>
        </div>
    `;
            $('#itinerary-days').append(newDay);
        }

        function reorderDays() {
            $('.itinerary-day').each(function(index) {
                const dayNum = index + 1;
                const dayLabels = $(this).find('.form-label');
                dayLabels.first().text(`Day ${dayNum} Title`);
                dayLabels.last().text(`Day ${dayNum} Description`);
            });
        }

        // Hotel Functions
        function loadHotels(locationId) {
            $.ajax({
                url: "../ajax/package-controller/get-location-hotels.php",
                type: 'POST',
                data: {
                    locationId: locationId,
                    packageId: $('#package-id').val()
                },
                success: function(response) {
                    $('#package-hotels').html(response).trigger('change');
                    updateHotelPreview();
                },
                error: function() {
                    toastr.error('Error loading hotels');
                }
            });
        }

        function updateHotelPreview() {
            const selectedHotels = $('#package-hotels').select2('data');
            const previewContainer = $('#selected-hotels-preview');
            previewContainer.empty();

            selectedHotels.forEach(hotel => {
                $.ajax({
                    url: "../ajax/package-controller/get-location-hotel-details.php",
                    type: 'POST',
                    data: {
                        hotelId: hotel.id
                    },
                    success: function(response) {
                        const hotelData = JSON.parse(response);
                        const hotelPreview = `
                    <div class="col-md-4">
                        <div class="card">
                            <img src="http://localhost/dfcs/assets/img/hotels/${hotelData.hotel_image}" 
                                class="card-img-top" style="height: 150px; object-fit: cover;" 
                                alt="${hotelData.hotel_name}">
                            <div class="card-body">
                                <h6 class="card-title">${hotelData.hotel_name}</h6>
                            </div>
                        </div>
                    </div>
                `;
                        previewContainer.append(hotelPreview);
                    }
                });
            });
        }

        // Update Package Function
        function updatePackage() {
            if (!validateItinerary()) return;

            const formData = new FormData();
            formData.append('packageId', $('#package-id').val());

            // Basic Info
            const basicFields = {
                'packageTitle': '#package-title',
                'tourType': '#tour-type',
                'location': '#location',
                'durationDays': '#duration-days',
                'durationNights': '#duration-nights',
                'minimumAge': '#minimum-age',
                'difficultyLevel': '#difficulty-level',
                'packageStatus': '#package-status'
            };

            for (const [key, selector] of Object.entries(basicFields)) {
                formData.append(key, $(selector).val());
            }

            // Pricing Info
            const pricingFields = {
                'displayPrice': '#display-price',
                'adultPrice': '#adult-price',
                'childPrice': '#child-price',
                'infantPrice': '#infant-price',
                'groupDiscount': '#group-discount',
                'minPeopleDiscount': '#min-people-discount',
                'maxGroupSize': '#max-group-size'
            };

            for (const [key, selector] of Object.entries(pricingFields)) {
                formData.append(key, $(selector).val());
            }

            // Description
            formData.append('description', $('#package-description').summernote('code'));

            // Images
            if ($('#featured-image')[0].files[0]) {
                formData.append('featuredImage', $('#featured-image')[0].files[0]);
            }

            const galleryFiles = $('#gallery-images')[0].files;
            for (let i = 0; i < galleryFiles.length; i++) {
                formData.append('galleryImages[]', galleryFiles[i]);
            }

            // Hotels
            formData.append('hotels', JSON.stringify($('#package-hotels').val()));

            // Inclusions & Exclusions
            const inclusions = $('.inclusion-item').map(function() {
                return $(this).val().trim();
            }).get().filter(Boolean);

            const exclusions = $('.exclusion-item').map(function() {
                return $(this).val().trim();
            }).get().filter(Boolean);

            formData.append('inclusions', JSON.stringify(inclusions));
            formData.append('exclusions', JSON.stringify(exclusions));

            // Itinerary
            const itinerary = $('.itinerary-day').map(function(index) {
                return {
                    dayNumber: index + 1,
                    title: $(this).find('.day-title').val().trim(),
                    description: $(this).find('.day-description').val().trim()
                };
            }).get();

            formData.append('itinerary', JSON.stringify(itinerary));

            // Send Update Request
            $.ajax({
                url: "../ajax/package-controller/update-package.php",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    try {
                        const data = JSON.parse(response);
                        if (data.success) {
                            toastr.success('Package updated successfully');
                            setTimeout(() => {
                                window.location.href = 'view-data';
                            }, 1000);
                        } else {
                            toastr.error(data.message || 'Error updating package');
                        }
                    } catch (e) {
                        toastr.error('Error processing response');
                    }
                },
                error: function() {
                    toastr.error('Error updating package');
                }
            });
        }
    </script>



</body>

</html>