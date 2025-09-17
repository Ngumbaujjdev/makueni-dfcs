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
    <link id="style" href="http://localhost/dfcs/assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">

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
    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/choices.js/public/assets/styles/choices.min.css">

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

                <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
                    <h1 class="page-title fw-semibold fs-18 mb-0">Add Tour Package</h1>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Tour Packages</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Add Package</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-header justify-content-between">
                                <div class="card-title">Create New Tour Package</div>
                            </div>
                            <div class="card-body">
                                <ul class="nav nav-tabs" id="packageTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" data-bs-toggle="tab"
                                            data-bs-target="#basic-info" type="button" role="tab">
                                            <i class="bi bi-info-circle me-1"></i>Basic Details
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#pricing-info"
                                            type="button" role="tab">
                                            <i class="bi bi-currency-dollar me-1"></i>Pricing Details
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="tab"
                                            data-bs-target="#description-gallery" type="button" role="tab">
                                            <i class="bi bi-images me-1"></i>Description & Gallery
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#hotels"
                                            type="button" role="tab">
                                            <i class="bi bi-building me-1"></i>Hotels
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="tab"
                                            data-bs-target="#inclusions-exclusions" type="button" role="tab">
                                            <i class="bi bi-list-check me-1"></i>Inclusions & Exclusions
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#itinerary"
                                            type="button" role="tab">
                                            <i class="bi bi-calendar-event me-1"></i>Itinerary
                                        </button>
                                    </li>
                                </ul>

                                <div class="tab-content p-4" id="packageTabsContent">
                                    <div class="tab-pane fade show active" id="basic-info" role="tabpanel">
                                        <div class="row gy-3">
                                            <div class="col-xl-6">
                                                <label class="form-label">Package Title</label>
                                                <input type="text" class="form-control" id="package-title"
                                                    placeholder="Enter package title">
                                            </div>
                                            <div class="col-xl-6">
                                                <label class="form-label">Tour Type</label>
                                                <select class="form-control" id="tour-type">
                                                    <option value="">Select Tour Type</option>
                                                    <?php
                                                    $app = new App;
                                                    $query = "SELECT * FROM tour_types ORDER BY type_name ASC";
                                                    $types = $app->select_all($query);
                                                    if ($types):
                                                        foreach ($types as $type):
                                                    ?>
                                                    <option value="<?php echo $type->tour_type_id ?>">
                                                        <?php echo $type->type_name ?></option>
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
                                                    <option value="<?php echo $location->location_id ?>">
                                                        <?php echo $location->location_name ?></option>
                                                    <?php
                                                        endforeach;
                                                    endif;
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-xl-3">
                                                <label class="form-label">Duration (Days)</label>
                                                <input type="number" class="form-control" id="duration-days" min="1">
                                            </div>
                                            <div class="col-xl-3">
                                                <label class="form-label">Duration (Nights)</label>
                                                <input type="number" class="form-control" id="duration-nights" min="0">
                                            </div>
                                            <div class="col-xl-4">
                                                <label class="form-label">Minimum Age</label>
                                                <input type="number" class="form-control" id="minimum-age" min="0">
                                            </div>
                                            <div class="col-xl-4">
                                                <label class="form-label">Difficulty Level</label>
                                                <select class="form-control" id="difficulty-level">
                                                    <option value="easy">Easy</option>
                                                    <option value="moderate">Moderate</option>
                                                    <option value="challenging">Challenging</option>
                                                </select>
                                            </div>
                                            <div class="col-xl-4">
                                                <label class="form-label">Status</label>
                                                <select class="form-control" id="package-status">
                                                    <option value="active">Active</option>
                                                    <option value="inactive">Inactive</option>
                                                    <option value="draft">Draft</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end mt-3">
                                            <button class="btn text-white" id="nextBasic" style="background:#6AA32D;">
                                                Next <i class="bi bi-arrow-right ms-2"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="pricing-info" role="tabpanel">
                                        <div class="row gy-3">
                                            <div class="col-xl-6">
                                                <label class="form-label">Display Price (Starting From)</label>
                                                <input type="number" class="form-control" id="display-price" min="0"
                                                    step="0.01">
                                            </div>
                                            <div class="col-xl-6">
                                                <label class="form-label">Price Per Adult</label>
                                                <input type="number" class="form-control" id="adult-price" min="0"
                                                    step="0.01">
                                            </div>
                                            <div class="col-xl-6">
                                                <label class="form-label">Price Per Child (4-12 years)</label>
                                                <input type="number" class="form-control" id="child-price" min="0"
                                                    step="0.01">
                                            </div>
                                            <div class="col-xl-6">
                                                <label class="form-label">Price Per Infant (0-3 years)</label>
                                                <input type="number" class="form-control" id="infant-price" min="0"
                                                    step="0.01">
                                            </div>
                                            <div class="col-xl-4">
                                                <label class="form-label">Group Discount (%)</label>
                                                <input type="number" class="form-control" id="group-discount" min="0"
                                                    max="100">
                                            </div>
                                            <div class="col-xl-4">
                                                <label class="form-label">Min People for Group Discount</label>
                                                <input type="number" class="form-control" id="min-people-discount"
                                                    min="2">
                                            </div>
                                            <div class="col-xl-4">
                                                <label class="form-label">Maximum Group Size</label>
                                                <input type="number" class="form-control" id="max-group-size" min="1">
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

                                    <div class="tab-pane fade" id="description-gallery" role="tabpanel">
                                        <div class="row gy-4">
                                            <div class="col-xl-12">
                                                <label class="form-label">Package Description</label>
                                                <textarea id="package-description" class="form-control"></textarea>
                                            </div>
                                            <div class="col-xl-12">
                                                <label class="form-label">Featured Image</label>
                                                <input type="file" class="form-control" id="featured-image"
                                                    accept="image/*">
                                            </div>
                                            <div class="col-xl-12">
                                                <label class="form-label">Gallery Images</label>
                                                <input type="file" class="form-control" id="gallery-images"
                                                    accept="image/*" multiple>
                                                <small class="text-muted">Select 3 images (1 main large, 2
                                                    small)</small>
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

                                    <div class="tab-pane fade" id="hotels" role="tabpanel">
                                        <div class="row gy-3">
                                            <div class="col-xl-12">
                                                <label class="form-label">Select Hotels</label>
                                                <select class="form-control select2-multiple" id="package-hotels"
                                                    multiple>
                                                    <?php
                                                    $app = new App;
                                                    $locationId = isset($_POST['location']) ? $_POST['location'] : '';

                                                    if ($locationId) {
                                                        $query = "SELECT h.hotel_id, h.hotel_name 
                              FROM hotels h
                              WHERE h.location_id = :locationId
                              ORDER BY h.hotel_name ASC";
                                                        $hotels = $app->select_all($query, [':locationId' => $locationId]);

                                                        if ($hotels):
                                                            foreach ($hotels as $hotel):
                                                    ?>
                                                    <option value="<?php echo $hotel->hotel_id ?>">
                                                        <?php echo $hotel->hotel_name ?></option>
                                                    <?php
                                                            endforeach;
                                                        endif;
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-xl-12">
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

                                    <div class="tab-pane fade" id="inclusions-exclusions" role="tabpanel">
                                        <div class="row gy-4">
                                            <div class="col-xl-6">
                                                <label class="form-label">Package Inclusions</label>
                                                <div id="inclusions-container">
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

                                    <div class="tab-pane fade" id="itinerary" role="tabpanel">
                                        <div id="itinerary-days">
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
                                        </div>
                                        <button class="btn btn-success mb-4" id="add-day">
                                            <i class="bi bi-plus-circle me-2"></i>Add Another Day
                                        </button>
                                        <div class="d-flex justify-content-between">
                                            <button class="btn btn-light" id="prevItinerary">
                                                <i class="bi bi-arrow-left me-2"></i>Previous
                                            </button>
                                            <button class="btn text-white" onclick="validateAndSubmitPackage()"
                                                style="background:#6AA32D;">
                                                Create Package <i class="bi bi-check-lg ms-2"></i>
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
        $('.select2-multiple').select2({
            theme: 'bootstrap-5',
            placeholder: 'Select options',
            allowClear: true
        });

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

        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };

        $('#nextBasic').click(function() {
            if (validateBasicInfo()) {
                $('#packageTabs button[data-bs-target="#pricing-info"]').tab('show');
            }
        });

        $('#prevPricing').click(() => {
            $('#packageTabs button[data-bs-target="#basic-info"]').tab('show');
        });

        $('#nextPricing').click(function() {
            if (validatePricing()) {
                $('#packageTabs button[data-bs-target="#description-gallery"]').tab('show');
            }
        });

        $('#prevDescription').click(() => {
            $('#packageTabs button[data-bs-target="#pricing-info"]').tab('show');
        });

        $('#nextDescription').click(function() {
            if (validateDescription()) {
                $('#packageTabs button[data-bs-target="#hotels"]').tab('show');
            }
        });

        $('#prevHotels').click(() => {
            $('#packageTabs button[data-bs-target="#description-gallery"]').tab('show');
        });

        $('#nextHotels').click(function() {
            if (validateHotels()) {
                $('#packageTabs button[data-bs-target="#inclusions-exclusions"]').tab('show');
            }
        });

        $('#prevInclusions').click(() => {
            $('#packageTabs button[data-bs-target="#hotels"]').tab('show');
        });

        $('#nextInclusions').click(function() {
            if (validateInclusionsExclusions()) {
                $('#packageTabs button[data-bs-target="#itinerary"]').tab('show');
            }
        });

        $('#prevItinerary').click(() => {
            $('#packageTabs button[data-bs-target="#inclusions-exclusions"]').tab('show');
        });

        $('#location').change(function() {
            let locationId = $(this).val();
            if (locationId) {
                loadHotels(locationId);
            }
        });

        $('.add-inclusion').click(addInclusionField);
        $('.add-exclusion').click(addExclusionField);
        $('#add-day').click(addItineraryDay);

        $(document).on('click', '.remove-field', function() {
            $(this).closest('.input-group').remove();
        });

        $(document).on('click', '.remove-day', function() {
            $(this).closest('.itinerary-day').remove();
            reorderDays();
        });
    });

    function loadHotels(locationId) {
        $.ajax({
            url: "../ajax/package-controller/get-hotels.php",
            type: 'POST',
            data: {
                locationId: locationId
            },
            success: function(response) {
                try {
                    $('#package-hotels').html(response).trigger('change');
                } catch (e) {
                    toastr.error('Error loading hotels');
                }
            },
            error: function() {
                toastr.error('Error loading hotels');
            }
        });
    }

    function addInclusionField() {
        const field = `
            <div class="input-group mb-2">
                <input type="text" class="form-control inclusion-item" placeholder="Add inclusion">
                <button class="btn btn-danger remove-field" type="button">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        $('#inclusions-container').append(field);
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
        $('#exclusions-container').append(field);
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
            $(this).find('.form-label').first().text(`Day ${dayNum} Title`);
            $(this).find('.form-label').last().text(`Day ${dayNum} Description`);
        });
    }

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
        if (!$('#featured-image').val()) {
            toastr.error('Please select featured image');
            return false;
        }
        if ($('#gallery-images')[0].files.length !== 3) {
            toastr.error('Please select exactly 3 gallery images');
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
        const days = $('.itinerary-day').length;
        if (days === 0) {
            toastr.error('Please add at least one day to the itinerary');
            return false;
        }

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

    function validateAndSubmitPackage() {
        if (!validateBasicInfo() || !validatePricing() || !validateDescription() ||
            !validateHotels() || !validateInclusionsExclusions() || !validateItinerary()) {
            return;
        }

        const formData = new FormData();

        const formFields = {
            'packageTitle': '#package-title',
            'tourType': '#tour-type',
            'location': '#location',
            'durationDays': '#duration-days',
            'durationNights': '#duration-nights',
            'minimumAge': '#minimum-age',
            'difficultyLevel': '#difficulty-level',
            'packageStatus': '#package-status',
            'displayPrice': '#display-price',
            'adultPrice': '#adult-price',
            'childPrice': '#child-price',
            'infantPrice': '#infant-price',
            'groupDiscount': '#group-discount',
            'minPeopleDiscount': '#min-people-discount',
            'maxGroupSize': '#max-group-size'
        };

        for (const [key, selector] of Object.entries(formFields)) {
            formData.append(key, $(selector).val());
        }

        formData.append('description', $('#package-description').summernote('code'));
        formData.append('featuredImage', $('#featured-image')[0].files[0]);
        Array.from($('#gallery-images')[0].files).forEach(file => {
            formData.append('galleryImages[]', file);
        });
        formData.append('hotels', JSON.stringify($('#package-hotels').val()));

        const inclusions = $('.inclusion-item').map(function() {
            return $(this).val().trim();
        }).get().filter(Boolean);

        const exclusions = $('.exclusion-item').map(function() {
            return $(this).val().trim();
        }).get().filter(Boolean);

        formData.append('inclusions', JSON.stringify(inclusions));
        formData.append('exclusions', JSON.stringify(exclusions));

        const itinerary = $('.itinerary-day').map(function(index) {
            return {
                dayNumber: index + 1,
                title: $(this).find('.day-title').val().trim(),
                description: $(this).find('.day-description').val().trim()
            };
        }).get();

        formData.append('itinerary', JSON.stringify(itinerary));

        $.ajax({
            url: "../ajax/package-controller/add-package.php",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    const data = JSON.parse(response);
                    if (data.success) {
                        toastr.success('Package created successfully');
                        // setTimeout(() => {
                        //     window.location.href = 'view';
                        // }, 2000);
                    } else {
                        toastr.error(data.message || 'Error creating package');
                    }
                } catch (e) {
                    toastr.error('Error processing response');
                }
            },
            error: function() {
                toastr.error('Error creating package');
            }
        });
    }
    </script>


</body>

</html>