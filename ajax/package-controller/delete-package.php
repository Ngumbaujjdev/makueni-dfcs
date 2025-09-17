<?php
include "../../../config/config.php";
include "../../../libs/App.php";

if (isset($_POST['deletesend'])) {
    try {
        $app = new App;
        $package_id = $_POST['deletesend'];

        // First, verify package exists and get image info for deletion
        $query = "SELECT featured_image FROM tour_packages WHERE package_id = '{$package_id}'";
        $package = $app->select_one($query);

        if (!$package) {
            echo json_encode(['success' => false, 'message' => 'Package not found']);
            exit;
        }

        // Get gallery images before deletion
        $query = "SELECT image_1, image_2, image_3 FROM package_gallery WHERE package_id = '{$package_id}'";
        $gallery = $app->select_one($query);

        // Begin transaction
        $app->beginTransaction();

        // Delete from package_reviews
        $query = "DELETE FROM package_reviews WHERE package_id = '{$package_id}'";
        $app->delete_without_path($query);

        // Delete from package_hotel_mappings
        $query = "DELETE FROM package_hotel_mappings WHERE package_id = '{$package_id}'";
        $app->delete_without_path($query);

        // Delete from package_inclusions
        $query = "DELETE FROM package_inclusions WHERE package_id = '{$package_id}'";
        $app->delete_without_path($query);

        // Delete from package_exclusions
        $query = "DELETE FROM package_exclusions WHERE package_id = '{$package_id}'";
        $app->delete_without_path($query);

        // Delete from package_itinerary
        $query = "DELETE FROM package_itinerary WHERE package_id = '{$package_id}'";
        $app->delete_without_path($query);

        // Delete from package_gallery
        $query = "DELETE FROM package_gallery WHERE package_id = '{$package_id}'";
        $app->delete_without_path($query);

        // Finally, delete the package itself
        $query = "DELETE FROM tour_packages WHERE package_id = '{$package_id}'";
        $app->delete_without_path($query);

        // Commit transaction
        $app->commit();

        // After successful database deletion, remove images
        if ($package->featured_image) {
            $featured_image_path = "../../../assets/img/package-images/" . $package->featured_image;
            if (file_exists($featured_image_path)) {
                unlink($featured_image_path);
            }
        }

        if ($gallery) {
            $gallery_images = [$gallery->image_1, $gallery->image_2, $gallery->image_3];
            foreach ($gallery_images as $image) {
                if ($image) {
                    $image_path = "../../../assets/img/package-images/" . $image;
                    if (file_exists($image_path)) {
                        unlink($image_path);
                    }
                }
            }
        }

        echo json_encode([
            'success' => true,
            'message' => 'Package and all related data deleted successfully'
        ]);
    } catch (Exception $e) {
        // Rollback on error
        if (isset($app)) {
            $app->rollBack();
        }
        echo json_encode([
            'success' => false,
            'message' => 'Error deleting package: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No package ID provided'
    ]);
}
