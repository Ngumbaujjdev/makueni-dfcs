<?php
include "../../config/config.php";
include "../../libs/App.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $app = new App;

        // Begin transaction
        $app->beginTransaction();

        // Get form data
        $farmName = $_POST['farmName'];
        $farmLocation = $_POST['farmLocation'];
        $farmSize = $_POST['farmSize'];
        $selectedFruits = json_decode($_POST['selectedFruits'], true);
        $cultivationMethod = $_POST['cultivationMethod'];
        $harvestingMethod = $_POST['harvestingMethod'];
        $harvestFrequency = $_POST['harvestFrequency'];
        $production = json_decode($_POST['production'], true);

        // Get the current user's ID
        $userId = $_SESSION['user_id'];

        // Insert farm record
        $farmQuery = "INSERT INTO farms (
            farmer_id, 
            name, 
            farm_type_id,
            location, 
            size, 
            size_unit, 
            created_at, 
            updated_at
        ) VALUES (
            :farmer_id, 
            :name, 
            :farm_type_id,
            :location, 
            :size, 
            'acres', 
            NOW(), 
            NOW()
        )";
        
        $farmParams = [
            ':farmer_id' => $userId,
            ':name' => $farmName,
            ':farm_type_id' => 2, // 2 is for Fruit Farm
            ':location' => $farmLocation,
            ':size' => $farmSize
        ];
        
        $app->insertWithoutPath($farmQuery, $farmParams);
        $farmId = $app->lastInsertId();

        // Log the farm creation activity
        $activityQuery = "INSERT INTO activity_logs (
            user_id, 
            activity_type, 
            description, 
            created_at
        ) VALUES (
            :user_id,
            'farm_added',
            :description,
            NOW()
        )";
        
        $activityParams = [
            ':user_id' => $userId,
            ':description' => "New farm added: {$farmName} at {$farmLocation}"
        ];
        
        $app->insertWithoutPath($activityQuery, $activityParams);

        // Add audit log for farm creation
        $auditQuery = "INSERT INTO audit_logs (
            user_id,
            action_type,
            table_name,
            record_id,
            new_values,
            created_at
        ) VALUES (
            :user_id,
            'create',
            'farms',
            :record_id,
            :new_values,
            NOW()
        )";
        
        $auditParams = [
            ':user_id' => $userId,
            ':record_id' => $farmId,
            ':new_values' => json_encode([
                'name' => $farmName,
                'location' => $farmLocation,
                'size' => $farmSize,
                'farm_type_id' => 2
            ])
        ];
        
        $app->insertWithoutPath($auditQuery, $auditParams);

        // Insert farm fruit mapping records
        foreach ($selectedFruits as $fruit) {
            $fruitId = $fruit['fruitId'];
            $acreage = $fruit['acreage'];

            $farmFruitQuery = "INSERT INTO farm_fruit_mapping (
                farm_id, 
                fruit_type_id, 
                acreage, 
                cultivation_type_id, 
                harvesting_method_id, 
                harvest_frequency_id, 
                created_at, 
                updated_at
            ) VALUES (
                :farm_id, 
                :fruit_type_id, 
                :acreage, 
                :cultivation_type_id, 
                :harvesting_method_id, 
                :harvest_frequency_id, 
                NOW(), 
                NOW()
            )";
            
            $farmFruitParams = [
                ':farm_id' => $farmId,
                ':fruit_type_id' => $fruitId,
                ':acreage' => $acreage,
                ':cultivation_type_id' => $cultivationMethod,
                ':harvesting_method_id' => $harvestingMethod,
                ':harvest_frequency_id' => $harvestFrequency
            ];
            
            $app->insertWithoutPath($farmFruitQuery, $farmFruitParams);
            $mappingId = $app->lastInsertId();

            // Add audit log for fruit mapping
            $auditFruitParams = [
                ':user_id' => $userId,
                ':record_id' => $mappingId,
                ':new_values' => json_encode([
                    'farm_id' => $farmId,
                    'fruit_type_id' => $fruitId,
                    'acreage' => $acreage,
                    'cultivation_type_id' => $cultivationMethod,
                    'harvesting_method_id' => $harvestingMethod,
                    'harvest_frequency_id' => $harvestFrequency
                ])
            ];
            $app->insertWithoutPath($auditQuery, $auditFruitParams);
        }

       // Insert farm product records
foreach ($production as $item) {
    $fruitId = $item['fruitId'];
    $expectedProduction = $item['expectedProduction'];

    // First get the corresponding product type for this fruit
    $productTypeQuery = "SELECT pt.id 
                        FROM product_types pt 
                        INNER JOIN fruit_types ft ON pt.name = ft.name 
                        WHERE ft.id = :fruit_id";
    $productType = $app->selectOne($productTypeQuery, [':fruit_id' => $fruitId]);

    if (!$productType) {
        // If no matching product type exists, create one
        $fruitNameQuery = "SELECT name FROM fruit_types WHERE id = :fruit_id";
        $fruit = $app->selectOne($fruitNameQuery, [':fruit_id' => $fruitId]);
        
        $insertProductTypeQuery = "INSERT INTO product_types (
            name, 
            measurement_unit, 
            measurement_period, 
            description
        ) VALUES (
            :name,
            'kgs',
            'yearly',
            :description
        )";
        
        $insertProductTypeParams = [
            ':name' => $fruit->name,
            ':description' => $fruit->name . ' fruit production'
        ];
        
        $app->insertWithoutPath($insertProductTypeQuery, $insertProductTypeParams);
        $productTypeId = $app->lastInsertId();
    } else {
        $productTypeId = $productType->id;
    }

    // Now insert the farm product with the correct product_type_id
    $farmProductQuery = "INSERT INTO farm_products (
        farm_id, 
        product_type_id, 
        estimated_production, 
        start_date, 
        created_at, 
        updated_at
    ) VALUES (
        :farm_id, 
        :product_type_id, 
        :estimated_production, 
        NOW(), 
        NOW(), 
        NOW()
    )";
    
    $farmProductParams = [
        ':farm_id' => $farmId,
        ':product_type_id' => $productTypeId,
        ':estimated_production' => $expectedProduction
    ];
    
    $app->insertWithoutPath($farmProductQuery, $farmProductParams);
    $productId = $app->lastInsertId();

    // Add audit log for farm product
    $auditProductParams = [
        ':user_id' => $userId,
        ':record_id' => $productId,
        ':new_values' => json_encode([
            'farm_id' => $farmId,
            'product_type_id' => $productTypeId,
            'estimated_production' => $expectedProduction
        ])
    ];
    $app->insertWithoutPath($auditQuery, $auditProductParams);
}

        // Commit the transaction
        $app->commit();

        echo json_encode(['success' => true, 'message' => 'Farm added successfully']);
        
    } catch (Exception $e) {
        // Rollback the transaction if an error occurs
        $app->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error adding farm: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>