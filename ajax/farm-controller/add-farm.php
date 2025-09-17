<?php
include "../../config/config.php";
include "../../libs/App.php";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $app = new App;

        // Begin transaction
        $app->beginTransaction();
        $userId = $_SESSION['user_id'];

// Look up the farmer_id for this user
$farmerQuery = "SELECT id FROM farmers WHERE user_id = :user_id";
$farmerParams = [':user_id' => $userId];
$farmerResult = $app->selectOne($farmerQuery, $farmerParams);

if (!$farmerResult) {
    throw new Exception("No farmer record found for this user");
}

$farmerId = $farmerResult->id;


        // Get form data
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

        // Insert farm record
       // Insert farm record
           $farmQuery = "INSERT INTO farms (farmer_id, name, farm_type_id, location, size, size_unit, created_at, updated_at)
                         VALUES (:farmer_id, :name, :farm_type_id, :location, :size, 'acres', NOW(), NOW())";
           $farmParams = [
               ':farmer_id' => $farmerId,
               ':name' => $farmName,
               ':farm_type_id' => 2, // Adding farm_type_id = 2 for "Fruit Farm"
               ':location' => $farmLocation,
               ':size' => $farmSize
           ];
        $app->insertWithoutPath($farmQuery, $farmParams);
        $farmId = $app->lastInsertId();

        // Insert farm fruit mapping records
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

        // Insert farm product records
        foreach ($production as $item) {
            // $fruitId = $item['fruitId'];
            // $expectedProduction = $item['expectedProduction'];

            // $farmProductQuery = "INSERT INTO farm_products (farm_id, product_type_id, estimated_production, start_date, created_at, updated_at)
            //                      VALUES (:farm_id, :product_type_id, :estimated_production, NOW(), NOW(), NOW())";
            // $farmProductParams = [
            //     ':farm_id' => $farmId,
            //     ':product_type_id' => $fruitId,
            //     ':estimated_production' => $expectedProduction
            // ];
            // $app->insertWithoutPath($farmProductQuery, $farmProductParams);
            // $farmProductId = $app->lastInsertId();

            // $expectedProduceQuery = "INSERT INTO expected_produce (farm_product_id, expected_quantity, expected_delivery_date, created_at, updated_at)
            //                          VALUES (:farm_product_id, :expected_quantity, NOW(), NOW(), NOW())";
            // $expectedProduceParams = [
            //     ':farm_product_id' => $farmProductId,
            //     ':expected_quantity' => $expectedProduction
            // ];
            // $app->insertWithoutPath($expectedProduceQuery, $expectedProduceParams);
             $fruitId = $item['fruitId'];
    $expectedProduction = $item['expectedProduction'];

    // Get the corresponding product_type_id for this fruit
    $productTypeQuery = "SELECT id FROM product_types WHERE name = (SELECT name FROM fruit_types WHERE id = :fruit_id)";
    $productTypeParams = [':fruit_id' => $fruitId];
    $productType = $app->selectOne($productTypeQuery, $productTypeParams);

    if (!$productType) {
        throw new Exception("No product type found for fruit ID $fruitId");
    }

    $productTypeId = $productType->id;

    $farmProductQuery = "INSERT INTO farm_products (farm_id, product_type_id, estimated_production, start_date, created_at, updated_at)
                         VALUES (:farm_id, :product_type_id, :estimated_production, NOW(), NOW(), NOW())";
    $farmProductParams = [
        ':farm_id' => $farmId,
        ':product_type_id' => $productTypeId, // Use the mapped product_type_id
        ':estimated_production' => $expectedProduction
    ];
    $app->insertWithoutPath($farmProductQuery, $farmProductParams);
    $farmProductId = $app->lastInsertId();

        }

        // Commit the transaction
        $app->commit();

        // Return success response
        echo json_encode(['success' => true, 'message' => 'Farm added successfully']);
    } catch (Exception $e) {
        // Rollback the transaction if an error occurs
        $app->rollBack();

        // Return error response
        echo json_encode(['success' => false, 'message' => 'Error adding farm: ' . $e->getMessage()]);
    }
} else {
    // Return error response for invalid request method
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>