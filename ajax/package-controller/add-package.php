<?php
include "../../../config/config.php";
include "../../../libs/App.php";

if (isset($_POST["packageTitle"])) {
    try {
        $app = new App;

        // Get form data and sanitize
        $packageTitle = trim($_POST['packageTitle']);
        $tourType = $_POST['tourType'];
        $location = $_POST['location'];
        $packageStatus = $_POST['packageStatus'];
        $packageDescription = $_POST['description'];
        $durationDays = $_POST['durationDays'];
        $durationNights = $_POST['durationNights'];
        $minimumAge = $_POST['minimumAge'];
        $difficultyLevel = $_POST['difficultyLevel'];
        $displayPrice = $_POST['displayPrice'];
        $adultPrice = $_POST['adultPrice'];
        $childPrice = $_POST['childPrice'];
        $infantPrice = $_POST['infantPrice'];
        $groupDiscount = $_POST['groupDiscount'];
        $minPeopleDiscount = $_POST['minPeopleDiscount'];
        $maxGroupSize = $_POST['maxGroupSize'];
        $hotels = json_decode($_POST['hotels'], true); // Array of hotel IDs
        $inclusions = json_decode($_POST['inclusions'], true); // Array of inclusions
        $exclusions = json_decode($_POST['exclusions'], true); // Array of exclusions
        $itinerary = json_decode($_POST['itinerary'], true); // Array of itinerary days

        // Handle featured image upload
        $featuredImage = '';
        if (isset($_FILES['featuredImage']) && $_FILES['featuredImage']['error'] == 0) {
            $image = $_FILES['featuredImage']['name'];
            $image_temp = $_FILES['featuredImage']['tmp_name'];
            $featuredImage = time() . '_featured_' . $image;
            move_uploaded_file($image_temp, "../../../assets/img/package-images/$featuredImage");
        }

        // Handle gallery images upload
        $galleryImages = [];
        if (isset($_FILES['galleryImages'])) {
            foreach ($_FILES['galleryImages']['tmp_name'] as $key => $tmp_name) {
                $image = $_FILES['galleryImages']['name'][$key];
                $uniqueImage = time() . '_gallery_' . $image;
                move_uploaded_file($tmp_name, "../../../assets/img/package-images/$uniqueImage");
                $galleryImages[] = $uniqueImage;
            }
        }

        // Begin transaction for package and related data
        $app->beginTransaction();

        // Insert package
        $query = "INSERT INTO tour_packages (
            tour_type_id,
            location_id,
            title,
            description,
            featured_image,
            duration_days,
            duration_nights,
            minimum_age,
            display_price,
            price_per_adult,
            price_per_child,
            price_per_infant,
            group_discount_percentage,
            min_people_for_group_discount,
            group_size_max,
            difficulty_level,
            status
        ) VALUES (
            :tourType,
            :location,
            :packageTitle,
            :packageDescription,
            :featuredImage,
            :durationDays,
            :durationNights,
            :minimumAge,
            :displayPrice,
            :adultPrice,
            :childPrice,
            :infantPrice,
            :groupDiscount,
            :minPeopleDiscount,
            :maxGroupSize,
            :difficultyLevel,
            :packageStatus
        )";

        $arr = [
            ":tourType" => $tourType,
            ":location" => $location,
            ":packageTitle" => $packageTitle,
            ":packageDescription" => $packageDescription,
            ":featuredImage" => $featuredImage,
            ":durationDays" => $durationDays,
            ":durationNights" => $durationNights,
            ":minimumAge" => $minimumAge,
            ":displayPrice" => $displayPrice,
            ":adultPrice" => $adultPrice,
            ":childPrice" => $childPrice,
            ":infantPrice" => $infantPrice,
            ":groupDiscount" => $groupDiscount,
            ":minPeopleDiscount" => $minPeopleDiscount,
            ":maxGroupSize" => $maxGroupSize,
            ":difficultyLevel" => $difficultyLevel,
            ":packageStatus" => $packageStatus
        ];

        // Insert package and get ID
        $app->insertWithoutPath($query, $arr);
        $packageId = $app->lastInsertId();

        // Insert package hotels
        if (!empty($hotels)) {
            foreach ($hotels as $hotelId) {
                $queryHotels = "INSERT INTO package_hotel_mappings (package_id, hotel_id) VALUES (:packageId, :hotelId)";
                $arrHotels = [
                    ":packageId" => $packageId,
                    ":hotelId" => $hotelId
                ];
                $app->insertWithoutPath($queryHotels, $arrHotels);
            }
        }

        // Insert package inclusions
        if (!empty($inclusions)) {
            foreach ($inclusions as $inclusion) {
                $queryInclusions = "INSERT INTO package_inclusions (package_id, inclusion_item) VALUES (:packageId, :inclusion)";
                $arrInclusions = [
                    ":packageId" => $packageId,
                    ":inclusion" => $inclusion
                ];
                $app->insertWithoutPath($queryInclusions, $arrInclusions);
            }
        }

        // Insert package exclusions
        if (!empty($exclusions)) {
            foreach ($exclusions as $exclusion) {
                $queryExclusions = "INSERT INTO package_exclusions (package_id, exclusion_item) VALUES (:packageId, :exclusion)";
                $arrExclusions = [
                    ":packageId" => $packageId,
                    ":exclusion" => $exclusion
                ];
                $app->insertWithoutPath($queryExclusions, $arrExclusions);
            }
        }

        // Insert package itinerary
        if (!empty($itinerary)) {
            foreach ($itinerary as $day) {
                $queryItinerary = "INSERT INTO package_itinerary (package_id, day_number, day_title, day_description) VALUES (:packageId, :dayNumber, :dayTitle, :dayDescription)";
                $arrItinerary = [
                    ":packageId" => $packageId,
                    ":dayNumber" => $day['dayNumber'],
                    ":dayTitle" => $day['title'],
                    ":dayDescription" => $day['description']
                ];
                $app->insertWithoutPath($queryItinerary, $arrItinerary);
            }
        }

        // Insert package gallery images
        if (!empty($galleryImages)) {
            $image1 = isset($galleryImages[0]) ? $galleryImages[0] : '';
            $image2 = isset($galleryImages[1]) ? $galleryImages[1] : '';
            $image3 = isset($galleryImages[2]) ? $galleryImages[2] : '';

            $queryGallery = "INSERT INTO package_gallery (package_id, image_1, image_2, image_3) VALUES (:packageId, :image1, :image2, :image3)";
            $arrGallery = [
                ":packageId" => $packageId,
                ":image1" => $image1,
                ":image2" => $image2,
                ":image3" => $image3
            ];
            $app->insertWithoutPath($queryGallery, $arrGallery);
        }

        // Commit transaction
        $app->commit();

        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Package added successfully',
            'packageId' => $packageId
        ]);
    } catch (Exception $e) {
        // Rollback on error
        if (isset($app)) {
            $app->rollBack();
        }
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No data received'
    ]);
}
