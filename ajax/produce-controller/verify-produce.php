<?php
include "../../config/config.php";
include "../../libs/App.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $app = new App;
        
        // Begin transaction
        $app->beginTransaction();

        // Get data from POST
        $produceId = isset($_POST['produceId']) ? intval($_POST['produceId']) : 0;
        
        // Validate inputs
        if ($produceId <= 0) {
            throw new Exception("Invalid produce ID");
        }
        
        // Get SACCO staff ID from session
        $staffId = $_SESSION['user_id'];
        
        // First, get the produce details for logging purposes
        $produceQuery = "SELECT 
                            pd.farm_product_id,
                            pd.quantity,
                            pd.unit_price, 
                            pd.total_value,
                            pd.quality_grade,
                            CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                            f.farmer_id,
                            pt.name as product_name
                        FROM produce_deliveries pd
                        JOIN farm_products fp ON pd.farm_product_id = fp.id
                        JOIN product_types pt ON fp.product_type_id = pt.id
                        JOIN farms f ON fp.farm_id = f.id
                        JOIN farmers fm ON f.farmer_id = fm.id
                        JOIN users u ON fm.user_id = u.id
                        WHERE pd.id = :produce_id AND pd.status = 'pending'";
                        
        $produceParams = [
            ':produce_id' => $produceId
        ];
        
        $produceDetails = $app->selectOne($produceQuery, $produceParams);
        
        if (!$produceDetails) {
            throw new Exception("Produce delivery not found or not in pending status");
        }
        
        // Update produce status to verified
        $updateQuery = "UPDATE produce_deliveries SET 
                        status = 'verified'
                        WHERE id = :produce_id";
                    
        $updateParams = [
            ':produce_id' => $produceId
        ];
        
        $app->updateToken($updateQuery, $updateParams);
        
        // Add produce log
        $produceLogQuery = "INSERT INTO produce_logs (
                                produce_delivery_id,
                                user_id,
                                action_type,
                                description,
                                created_at
                            ) VALUES (
                                :produce_delivery_id,
                                :user_id,
                                'verified',
                                :description,
                                NOW()
                            )";
                            
        $produceLogParams = [
            ':produce_delivery_id' => $produceId,
            ':user_id' => $staffId,
            ':description' => "Produce delivery verified. " . 
                              "Product: " . $produceDetails->product_name . ", " .
                              "Quantity: " . number_format($produceDetails->quantity, 2) . " KGs, " .
                              "Quality: Grade " . $produceDetails->quality_grade
        ];
        
        $app->insertWithoutPath($produceLogQuery, $produceLogParams);
        
        // Add activity log
        $activityQuery = "INSERT INTO activity_logs (
                            user_id,
                            activity_type,
                            description,
                            created_at
                        ) VALUES (
                            :user_id,
                            'produce_verified',
                            :description,
                            NOW()
                        )";
                        
        $activityParams = [
            ':user_id' => $staffId,
            ':description' => "Produce delivery (ID: " . $produceId . ") from " . 
                             $produceDetails->farmer_name . " was verified. Quantity: " . 
                             number_format($produceDetails->quantity, 2) . " KGs, Value: KES " . 
                             number_format($produceDetails->total_value, 2)
        ];
        
        $app->insertWithoutPath($activityQuery, $activityParams);
        
        // Add audit log
        $auditQuery = "INSERT INTO audit_logs (
                        user_id,
                        action_type,
                        table_name,
                        record_id,
                        old_values,
                        new_values,
                        created_at
                    ) VALUES (
                        :user_id,
                        'update',
                        'produce_deliveries',
                        :record_id,
                        :old_values,
                        :new_values,
                        NOW()
                    )";
                    
        $oldValues = [
            'status' => 'pending'
        ];
        
        $newValues = [
            'status' => 'verified'
        ];
        
        $auditParams = [
            ':user_id' => $staffId,
            ':record_id' => $produceId,
            ':old_values' => json_encode($oldValues),
            ':new_values' => json_encode($newValues)
        ];
        
        $app->insertWithoutPath($auditQuery, $auditParams);
        
        // Commit transaction
        $app->commit();
        
        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Produce verified successfully'
        ]);
        
    } catch (Exception $e) {
        // Rollback on error
        $app->rollBack();
        
        echo json_encode([
            'success' => false,
            'message' => 'Error verifying produce: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}