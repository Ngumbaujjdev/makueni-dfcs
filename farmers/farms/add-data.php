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
    <meta name="apple-mobile-web-app-title" content="Makueni DFCS" />
    <link rel="manifest" href="http://localhost/dfcs/assets/images/favicon/site.webmanifest" />
    <!-- Main Theme Js -->
    <!-- Choices JS -->
    <script src="http://localhost/dfcs/assets/libs/choices.js/public/assets/scripts/choices.min.js"></script>
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

    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/glightbox/css/glightbox.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="http://localhost/dfcs/toast/toast.css">

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

                <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
                    <h1 class="page-title fw-semibold fs-18 mb-0">Add Farm</h1>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Farms</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Add Farm</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-header justify-content-between">
                                <div class="card-title">Add New Farm</div>
                            </div>
                            <div class="card-body add-products p-0">
                                <!-- Tabs Navigation -->
                                <ul class="nav nav-tabs" id="farmTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#farm-info"
                                            type="button" role="tab">
                                            <i class="bi bi-info-circle me-1"></i>Farm Info
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#farm-size"
                                            type="button" role="tab">
                                            <i class="bi bi-rulers me-1"></i>Farm Size
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#fruit-selection"
                                            type="button" role="tab">
                                            <i class="bi bi-basket me-1"></i>Fruit Selection
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#farming-methods"
                                            type="button" role="tab">
                                            <i class="bi bi-gear me-1"></i>Farming Methods
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#production"
                                            type="button" role="tab">
                                            <i class="bi bi-graph-up me-1"></i>Production Details
                                        </button>
                                    </li>
                                </ul>

                                <!-- Tab Content -->
                                <div class="tab-content p-4">
                                    <!-- Farm Info Tab -->
                                    <div class="tab-pane fade show active" id="farm-info" role="tabpanel">
                                        <div class="row gy-3">
                                            <div class="col-xl-12">
                                                <label class="form-label">Farm Name</label>
                                                <input type="text" class="form-control" id="farm-name"
                                                    placeholder="Enter farm name">
                                            </div>
                                            <div class="col-xl-12">
                                                <label class="form-label">Location</label>
                                                <input type="text" class="form-control" id="farm-location"
                                                    placeholder="Enter farm location">
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end mt-3">
                                            <button class="btn text-white" id="nextFarmInfo"
                                                style="background:#6AA32D;">
                                                Next <i class="bi bi-arrow-right ms-2"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Farm Size Tab -->
                                    <div class="tab-pane fade" id="farm-size" role="tabpanel">
                                        <div class="row gy-3">
                                            <div class="col-xl-12">
                                                <label class="form-label">Total Farm Size</label>
                                                <input type="number" class="form-control" id="farm-total-size"
                                                    placeholder="Enter total farm size">
                                                <small class="form-text text-muted">Enter size in acres</small>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between mt-4">
                                            <button class="btn btn-light" id="prevFarmSize">
                                                <i class="bi bi-arrow-left me-2"></i>Previous
                                            </button>
                                            <button class="btn text-white" id="nextFarmSize"
                                                style="background:#6AA32D;">
                                                Next <i class="bi bi-arrow-right ms-2"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Fruit Selection Tab -->
                                    <div class="tab-pane fade" id="fruit-selection" role="tabpanel">
                                        <div class="row gy-3">
                                            <div class="col-xl-12">
                                                <label class="form-label">Select Fruits</label>
                                                <select class="form-control select2-multiple" id="farm-fruits"
                                                    multiple="multiple">
                                                    <?php
                                                 $app = new App;
                                                 $query = "SELECT * FROM fruit_types ORDER BY name ASC";
                                                 $fruits = $app->select_all($query);
                                                 if ($fruits):
                                                     foreach ($fruits as $fruit):
                                                 ?>
                                                    <option value="<?php echo $fruit->id ?>">
                                                        <?php echo $fruit->name ?>
                                                    </option>
                                                    <?php
                                                     endforeach;
                                                 endif;
                                                 ?>
                                                </select>
                                            </div>

                                            <!-- Dynamic acreage inputs will be added here -->
                                            <div class="col-xl-12 mt-4" id="acreage-inputs">
                                                <!-- Acreage inputs will be dynamically generated here based on fruit selection -->
                                            </div>

                                            <div class="col-xl-12">
                                                <div class="alert alert-info" role="alert">
                                                    <span id="remaining-acreage">Total farm size: <span
                                                            id="total-size-display">0</span> acres.
                                                        Remaining acreage: <span id="remaining-size">0</span>
                                                        acres</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between mt-4">
                                            <button class="btn btn-light" id="prevFruitSelection">
                                                <i class="bi bi-arrow-left me-2"></i>Previous
                                            </button>
                                            <button class="btn text-white" id="nextFruitSelection"
                                                style="background:#6AA32D;">
                                                Next <i class="bi bi-arrow-right ms-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <!-- Farming Methods Tab -->
                                    <div class="tab-pane fade" id="farming-methods" role="tabpanel">
                                        <div class="row gy-3">
                                            <div class="col-xl-12">
                                                <label class="form-label">Cultivation Method</label>
                                                <select class="form-control" id="cultivation-method">
                                                    <option value="">Select Cultivation Method</option>
                                                    <?php
                                                          $app = new App;
                                                          $query = "SELECT * FROM cultivation_types ORDER BY name ASC";
                                                          $methods = $app->select_all($query);
                                                          if ($methods):
                                                              foreach ($methods as $method):
                                                          ?>
                                                    <option value="<?php echo $method->id ?>">
                                                        <?php echo $method->name ?>
                                                    </option>
                                                    <?php
                                                              endforeach;
                                                          endif;
                                                          ?>
                                                </select>
                                            </div>

                                            <div class="col-xl-12">
                                                <label class="form-label">Harvesting Method</label>
                                                <select class="form-control" id="harvesting-method">
                                                    <option value="">Select Harvesting Method</option>
                                                    <?php
                                                        $query = "SELECT * FROM harvesting_methods ORDER BY name ASC";
                                                        $methods = $app->select_all($query);
                                                        if ($methods):
                                                            foreach ($methods as $method):
                                                        ?>
                                                    <option value="<?php echo $method->id ?>">
                                                        <?php echo $method->name ?>
                                                    </option>
                                                    <?php
                                                            endforeach;
                                                        endif;
                                                        ?>
                                                </select>
                                            </div>

                                            <div class="col-xl-12">
                                                <label class="form-label">Harvest Frequency</label>
                                                <select class="form-control" id="harvest-frequency">
                                                    <option value="">Select Harvest Frequency</option>
                                                    <?php
                                                     $query = "SELECT * FROM harvest_frequencies ORDER BY name ASC";
                                                     $frequencies = $app->select_all($query);
                                                     if ($frequencies):
                                                         foreach ($frequencies as $frequency):
                                                     ?>
                                                    <option value="<?php echo $frequency->id ?>">
                                                        <?php echo $frequency->name ?>
                                                    </option>
                                                    <?php
                                                         endforeach;
                                                     endif;
                                                     ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between mt-4">
                                            <button class="btn btn-light" id="prevFarmingMethods">
                                                <i class="bi bi-arrow-left me-2"></i>Previous
                                            </button>
                                            <button class="btn text-white" id="nextFarmingMethods"
                                                style="background:#6AA32D;">
                                                Next <i class="bi bi-arrow-right ms-2"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Production Details Tab -->
                                    <div class="tab-pane fade" id="production" role="tabpanel">
                                        <div class="row gy-3">
                                            <div class="col-xl-12">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h6 class="card-title mb-0">Expected Production Details</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div id="production-inputs">
                                                            <!-- Dynamic production inputs will be generated here based on selected fruits -->

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between mt-4">
                                            <button class="btn btn-light" id="prevProduction">
                                                <i class="bi bi-arrow-left me-2"></i>Previous
                                            </button>
                                            <button class="btn text-white" id="submitFarm" style="background:#6AA32D;">
                                                Submit Farm <i class="bi bi-check-lg ms-2"></i>
                                            </button>
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
                <link rel="stylesheet"
                    href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css">


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
                    // Initialize Select2 for fruit selection
                    $('.select2-multiple').select2({
                        placeholder: 'Select fruits to be grown',
                        allowClear: true
                    });

                    // Variable to store total farm size
                    let totalFarmSize = 0;

                    // Handle fruit selection changes
                    $('#farm-fruits').on('change', function() {
                        let selectedFruits = $(this).val();
                        generateAcreageInputs(selectedFruits);
                    });

                    // Function to generate acreage inputs
                    function generateAcreageInputs(selectedFruits) {
                        let container = $('#acreage-inputs');
                        container.empty();

                        if (selectedFruits && selectedFruits.length > 0) {
                            selectedFruits.forEach(function(fruitId) {
                                let fruitName = $('#farm-fruits option[value="' + fruitId + '"]')
                                    .text();
                                container.append(`
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">${fruitName} Acreage</label>
                            <input type="number" 
                                   class="form-control fruit-acreage" 
                                   data-fruit-id="${fruitId}"
                                   placeholder="Enter acreage for ${fruitName}">
                        </div>
                    </div>
                `);
                            });
                        }
                        updateRemainingAcreage();
                    }

                    // Function to update remaining acreage
                    function updateRemainingAcreage() {
                        let allocatedAcreage = 0;
                        $('.fruit-acreage').each(function() {
                            let acres = parseFloat($(this).val()) || 0;
                            allocatedAcreage += acres;
                        });

                        let remaining = totalFarmSize - allocatedAcreage;
                        $('#total-size-display').text(totalFarmSize);
                        $('#remaining-size').text(remaining.toFixed(2));

                        // Change color based on remaining acreage
                        if (remaining < 0) {
                            $('#remaining-acreage').addClass('text-danger').removeClass('text-success');
                        } else {
                            $('#remaining-acreage').addClass('text-success').removeClass('text-danger');
                        }
                    }

                    // Handle acreage input changes
                    $(document).on('input', '.fruit-acreage', function() {
                        updateRemainingAcreage();
                    });

                    // Store farm size when entered
                    $('#farm-total-size').on('input', function() {
                        totalFarmSize = parseFloat($(this).val()) || 0;
                        updateRemainingAcreage();
                    });

                    // Tab Navigation with Validation
                    $('#nextFarmInfo').click(function() {
                        if (validateFarmInfo()) {
                            $('#farmTabs button[data-bs-target="#farm-size"]').tab('show');
                        }
                    });

                    $('#prevFarmSize').click(function() {
                        $('#farmTabs button[data-bs-target="#farm-info"]').tab('show');
                    });

                    $('#nextFarmSize').click(function() {
                        if (validateFarmSize()) {
                            $('#farmTabs button[data-bs-target="#fruit-selection"]').tab('show');
                        }
                    });

                    $('#prevFruitSelection').click(function() {
                        $('#farmTabs button[data-bs-target="#farm-size"]').tab('show');
                    });

                    $('#nextFruitSelection').click(function() {
                        if (validateFruitSelection()) {
                            $('#farmTabs button[data-bs-target="#farming-methods"]').tab('show');
                        }
                    });

                    $('#nextFarmingMethods').click(function() {
                        if (validateFarmingMethods()) {
                            generateProductionInputs();
                            $('#farmTabs button[data-bs-target="#production"]').tab('show');
                        }
                    });

                    $('#prevProduction').click(function() {
                        $('#farmTabs button[data-bs-target="#farming-methods"]').tab('show');
                    });

                    $('#submitFarm').click(function() {
                        validateAndSubmit();
                    });
                    // Validation Functions
                    function validateFarmInfo() {
                        if (!$('#farm-name').val().trim()) {
                            toastr.error('Please enter farm name');
                            return false;
                        }
                        if (!$('#farm-location').val().trim()) {
                            toastr.error('Please enter farm location');
                            return false;
                        }
                        return true;
                    }

                    function validateFarmSize() {
                        let size = parseFloat($('#farm-total-size').val());
                        if (!size || size <= 0) {
                            toastr.error('Please enter a valid farm size');
                            return false;
                        }
                        return true;
                    }

                    function validateFruitSelection() {
                        let selectedFruits = $('#farm-fruits').val();
                        if (!selectedFruits || selectedFruits.length === 0) {
                            toastr.error('Please select at least one fruit');
                            return false;
                        }

                        let validAcreage = true;
                        let allocatedAcreage = 0;

                        $('.fruit-acreage').each(function() {
                            let acres = parseFloat($(this).val());
                            if (!acres || acres <= 0) {
                                validAcreage = false;
                            }
                            allocatedAcreage += acres || 0;
                        });

                        if (!validAcreage) {
                            toastr.error('Please enter valid acreage for all selected fruits');
                            return false;
                        }

                        if (Math.abs(allocatedAcreage - totalFarmSize) > 0.01) {
                            toastr.error('Total allocated acreage must match farm size');
                            return false;
                        }

                        return true;
                    }

                    function validateFarmingMethods() {
                        if (!$('#cultivation-method').val()) {
                            toastr.error('Please select a cultivation method');
                            return false;
                        }
                        if (!$('#harvesting-method').val()) {
                            toastr.error('Please select a harvesting method');
                            return false;
                        }
                        if (!$('#harvest-frequency').val()) {
                            toastr.error('Please select a harvest frequency');
                            return false;
                        }
                        return true;
                    }

                    function generateProductionInputs() {
                        let selectedFruits = $('#farm-fruits').val();
                        let container = $('#production-inputs');
                        container.empty();

                        if (selectedFruits && selectedFruits.length > 0) {
                            selectedFruits.forEach(function(fruitId) {
                                let fruitName = $('#farm-fruits option[value="' + fruitId + '"]')
                                    .text();
                                let acreage = $('.fruit-acreage[data-fruit-id="' + fruitId + '"]')
                                    .val();
                                container.append(`
                                     <div class="row mb-3">
                                         <div class="col-md-6">
                                             <label>${fruitName} (${acreage} acres)</label>
                                         </div>
                                         <div class="col-md-6">
                                             <input type="number" class="form-control production-input" 
                                                    data-fruit-id="${fruitId}"
                                                    placeholder="Expected production in KGs">
                                         </div>
                                     </div>
                                 `);
                            });
                        }
                    }

                    function validateProduction() {
                        let validProduction = true;
                        $('.production-input').each(function() {
                            let production = parseFloat($(this).val());
                            if (!production || production <= 0) {
                                validProduction = false;
                            }
                        });
                        if (!validProduction) {
                            toastr.error('Please enter valid expected production for all fruits');
                            return false;
                        }
                        return true;
                    }

                    function validateAndSubmit() {
                        if (!validateFarmInfo() || !validateFarmSize() ||
                            !validateFruitSelection() || !validateFarmingMethods() ||
                            !validateProduction()) {
                            return;
                        }

                        let formData = new FormData();
                        formData.append('farmName', $('#farm-name').val().trim());
                        formData.append('farmLocation', $('#farm-location').val().trim());
                        formData.append('farmSize', $('#farm-total-size').val());
                        formData.append('selectedFruits', JSON.stringify(getSelectedFruits()));
                        formData.append('cultivationMethod', $('#cultivation-method').val());
                        formData.append('harvestingMethod', $('#harvesting-method').val());
                        formData.append('harvestFrequency', $('#harvest-frequency').val());
                        formData.append('production', JSON.stringify(getProduction()));

                        $.ajax({
                            url: 'http://localhost/dfcs/ajax/farm-controller/add-farm.php',
                            type: 'POST',
                            data: formData,
                            contentType: false,
                            processData: false,
                            success: function(response) {
                                try {
                                    let data = JSON.parse(response);
                                    if (data.success) {
                                        toastr.success('Farm added successfully');
                                        resetForm();
                                    } else {
                                        toastr.error(data.message || 'Error adding farm');
                                    }
                                } catch (e) {
                                    toastr.error('Error processing response');
                                }
                            },
                            error: function() {
                                toastr.error('Error adding farm');
                            }
                        });
                    }

                    function getSelectedFruits() {
                        let selectedFruits = [];
                        $('#farm-fruits').val().forEach(function(fruitId) {
                            let acreage = $('.fruit-acreage[data-fruit-id="' + fruitId + '"]').val();
                            selectedFruits.push({
                                fruitId: fruitId,
                                acreage: acreage
                            });
                        });
                        return selectedFruits;
                    }

                    function getProduction() {
                        let production = [];
                        $('.production-input').each(function() {
                            let fruitId = $(this).data('fruit-id');
                            let expectedProduction = $(this).val();
                            production.push({
                                fruitId: fruitId,
                                expectedProduction: expectedProduction
                            });
                        });
                        return production;
                    }

                    function resetForm() {
                        $('#farmTabs button[data-bs-target="#farm-info"]').tab('show');
                        $('#farm-name').val('');
                        $('#farm-location').val('');
                        $('#farm-total-size').val('');
                        $('#farm-fruits').val(null).trigger('change');
                        $('#acreage-inputs').empty();
                        $('#cultivation-method').val('');
                        $('#harvesting-method').val('');
                        $('#harvest-frequency').val('');
                        $('#production-inputs').empty();
                    }
                });
                </script>

</body>

</html>