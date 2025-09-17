<?php
include "../../../config/config.php";
include "../../../libs/App.php";

if (isset($_POST["packageId"])) {
    try {
        $app = new App;

        // Get form data and sanitize
        $packageId = $_POST['packageId'];
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
        $hotels = json_decode($_POST['hotels'], true);
        $inclusions = json_decode($_POST['inclusions'], true);
        $exclusions = json_decode($_POST['exclusions'], true);
        $itinerary = json_decode($_POST['itinerary'], true);

        // First, verify package exists
        $query = "SELECT featured_image FROM tour_packages WHERE package_id = '{$packageId}'";
        $existingPackage = $app->select_one($query);

        if (!$existingPackage) {
            echo json_encode(['success' => false, 'message' => 'Package not found']);
            exit;
        }

        // Handle featured image
        if (isset($_FILES['featuredImage']) && $_FILES['featuredImage']['error'] === 0) {
            $image = $_FILES['featuredImage']['name'];
            $image_temp = $_FILES['featuredImage']['tmp_name'];
            $featuredImage = time() . '_featured_' . $image;
            move_uploaded_file($image_temp, "../../../assets/img/package-images/$featuredImage");
        } else {
            $featuredImage = $existingPackage->featured_image;
        }

        // Begin transaction
        $app->beginTransaction();

        // Update main package details
        $query = "UPDATE tour_packages SET 
            tour_type_id = :tourType,
            location_id = :location,
            title = :packageTitle,
            description = :packageDescription,
            featured_image = :featuredImage,
            duration_days = :durationDays,
            duration_nights = :durationNights,
            minimum_age = :minimumAge,
            display_price = :displayPrice,
            price_per_adult = :adultPrice,
            price_per_child = :childPrice,
            price_per_infant = :infantPrice,
            group_discount_percentage = :groupDiscount,
            min_people_for_group_discount = :minPeopleDiscount,
            group_size_max = :maxGroupSize,
            difficulty_level = :difficultyLevel,
            status = :packageStatus
            WHERE package_id = :packageId";

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
            ":packageStatus" => $packageStatus,
            ":packageId" => $packageId
        ];

        $app->updateToken($query, $arr);

        // Update hotels
        $query = "DELETE FROM package_hotel_mappings WHERE package_id = '{$packageId}'";
        $app->delete_without_path($query);

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

        // Update inclusions
        $query = "DELETE FROM package_inclusions WHERE package_id = '{$packageId}'";
        $app->delete_without_path($query);

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

        // Update exclusions
        $query = "DELETE FROM package_exclusions WHERE package_id = '{$packageId}'";
        $app->delete_without_path($query);

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

        // Update itinerary
        $query = "DELETE FROM package_itinerary WHERE package_id = '{$packageId}'";
        $app->delete_without_path($query);

        if (!empty($itinerary)) {
            foreach ($itinerary as $day) {
                $queryItinerary = "INSERT INTO package_itinerary (package_id, day_number, day_title, day_description) 
                                 VALUES (:packageId, :dayNumber, :dayTitle, :dayDescription)";
                $arrItinerary = [
                    ":packageId" => $packageId,
                    ":dayNumber" => $day['dayNumber'],
                    ":dayTitle" => $day['title'],
                    ":dayDescription" => $day['description']
                ];
                $app->insertWithoutPath($queryItinerary, $arrItinerary);
            }
        }

        // Handle gallery images
        if (isset($_FILES['galleryImages']) && !empty($_FILES['galleryImages']['name'][0])) {
            $galleryImages = [];
            foreach ($_FILES['galleryImages']['tmp_name'] as $key => $tmp_name) {
                $image = $_FILES['galleryImages']['name'][$key];
                $uniqueImage = time() . '_gallery_' . $image;
                move_uploaded_file($tmp_name, "../../../assets/img/package-images/$uniqueImage");
                $galleryImages[] = $uniqueImage;
            }

            if (!empty($galleryImages)) {
                $image1 = isset($galleryImages[0]) ? $galleryImages[0] : '';
                $image2 = isset($galleryImages[1]) ? $galleryImages[1] : '';
                $image3 = isset($galleryImages[2]) ? $galleryImages[2] : '';

                // Delete existing gallery
                $query = "DELETE FROM package_gallery WHERE package_id = '{$packageId}'";
                $app->delete_without_path($query);

                // Insert new gallery
                $queryGallery = "INSERT INTO package_gallery (package_id, image_1, image_2, image_3) 
                                VALUES (:packageId, :image1, :image2, :image3)";
                $arrGallery = [
                    ":packageId" => $packageId,
                    ":image1" => $image1,
                    ":image2" => $image2,
                    ":image3" => $image3
                ];
                $app->insertWithoutPath($queryGallery, $arrGallery);
            }
        }

        // Commit transaction
        $app->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Package updated successfully'
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
        'message' => 'No package ID provided'
    ]);
}
