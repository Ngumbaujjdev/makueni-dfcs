<?php
include "../../config/config.php";
include "../../libs/App.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $app = new App;

        // Begin transaction
        $app->beginTransaction();

        // Get form data
        $farmId = $_POST['farmId'];
        $farmName = $_POST['farmName'];
        $farmLocation = $_POST['farmLocation'];
        $farmSize = $_POST['farmSize'];
        $selectedFruits = json_decode($_POST['selectedFruits'], true);
        $cultivationMethod = $_POST['cultivationMethod'];
        $harvestingMethod = $_POST['harvestingMethod'];
        $harvestFrequency = $_POST['harvestFrequency'];
        $production = json_decode($_POST['production'], true);

        // Get the current user's ID (assuming you have user authentication in place)
        $userId = $_SESSION['user_id'];

        // Update farm record
        $farmQuery = "UPDATE farms 
                      SET name = :name, 
                          location = :location, 
                          size = :size,
                          updated_at = NOW()
                      WHERE id = :farm_id AND farmer_id = :farmer_id";
        $farmParams = [
            ':name' => $farmName,
            ':location' => $farmLocation,
            ':size' => $farmSize,
            ':farm_id' => $farmId,
            ':farmer_id' => $userId
        ];
        $app->updateToken($farmQuery, $farmParams);

        // Delete existing farm fruit mapping records
        $deleteFruitQuery = "DELETE FROM farm_fruit_mapping WHERE farm_id ='{$farmId}'";
        $app->delete_without_path($deleteFruitQuery);

        // Insert new farm fruit mapping records
        foreach ($selectedFruits as $fruit) {
            $fruitId = $fruit['fruitId'];
            $acreage = $fruit['acreage'];

            $farmFruitQuery = "INSERT INTO farm_fruit_mapping (farm_id, fruit_type_id, acreage, cultivation_type_id, harvesting_method_id, harvest_frequency_id, created_at, updated_at)
                               VALUES (:farm_id, :fruit_type_id, :acreage, :cultivation_type_id, :harvesting_method_id, :harvest_frequency_id, NOW(), NOW())";
            $farmFruitParams = [
                ':farm_id' => $farmId,
                ':fruit_type_id' => $fruitId,
                ':acreage' => $acreage,
                ':cultivation_type_id' => $cultivationMethod,
                ':harvesting_method_id' => $harvestingMethod,
                ':harvest_frequency_id' => $harvestFrequency
            ];
            $app->insertWithoutPath($farmFruitQuery, $farmFruitParams);
        }

        // Delete related records in expected_produce table
        $deleteExpectedProduceQuery = "DELETE ep 
                                       FROM expected_produce ep
                                       JOIN farm_products fp ON ep.farm_product_id = fp.id
                                       WHERE fp.farm_id = '{$farmId}'";
        $deleteExpectedProduceParams = [':farm_id' => $farmId];
        $app->delete_without_path($deleteExpectedProduceQuery);

        // Delete existing farm product records
        $deleteProductQuery = "DELETE FROM farm_products WHERE farm_id = '{$farmId}'";
        $app->delete_without_path($deleteProductQuery);

        // Insert new farm product records
        foreach ($production as $item) {
            $fruitId = $item['fruitId'];
            $expectedProduction = $item['expectedProduction'];

            // Get the corresponding product_type_id for the fruit
            $productTypeQuery = "SELECT id FROM product_types WHERE name = (SELECT name FROM fruit_types WHERE id = :fruit_id)";
            $productTypeParams = [':fruit_id' => $fruitId];
            $productType = $app->selectOne($productTypeQuery, $productTypeParams);

            if ($productType) {
                $productTypeId = $productType->id;

                $farmProductQuery = "INSERT INTO farm_products (farm_id, product_type_id, estimated_production, start_date, created_at, updated_at)
                                     VALUES (:farm_id, :product_type_id, :estimated_production, NOW(), NOW(), NOW())";
                $farmProductParams = [
                    ':farm_id' => $farmId,
                    ':product_type_id' => $productTypeId,
                    ':estimated_production' => $expectedProduction
                ];
                $app->insertWithoutPath($farmProductQuery, $farmProductParams);

                $farmProductId = $app->lastInsertId();

                $expectedProduceQuery = "INSERT INTO expected_produce (farm_product_id, expected_quantity, expected_delivery_date, created_at, updated_at)
                                         VALUES (:farm_product_id, :expected_quantity, NOW(), NOW(), NOW())";
                $expectedProduceParams = [
                    ':farm_product_id' => $farmProductId,
                    ':expected_quantity' => $expectedProduction
                ];
                $app->insertWithoutPath($expectedProduceQuery, $expectedProduceParams);
            }
        }

        // Commit the transaction
        $app->commit();

        // Return success response
        echo json_encode(['success' => true, 'message' => 'Farm updated successfully']);
    } catch (Exception $e) {
        // Rollback the transaction if an error occurs
        $app->rollBack();

        // Return error response
        echo json_encode(['success' => false, 'message' => 'Error updating farm: ' . $e->getMessage()]);
    }
} else {
    // Return error response for invalid request method
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>