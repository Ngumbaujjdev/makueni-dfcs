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
        $rejectionReason = isset($_POST['rejectionReason']) ? $_POST['rejectionReason'] : '';
        
        // Validate inputs
        if ($produceId <= 0) {
            throw new Exception("Invalid produce ID");
        }
        
        if (empty($rejectionReason)) {
            throw new Exception("Rejection reason cannot be empty");
        }
        
        // Get SACCO staff ID from session
        $staffId = $_SESSION['user_id'];
        
        // First, get the produce details for logging purposes
        $produceQuery = "SELECT 
                            pd.farm_product_id, 
                            pd.quantity,
                            pd.total_value,
                            CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                            f.farmer_id
                        FROM produce_deliveries pd
                        JOIN farm_products fp ON pd.farm_product_id = fp.id
                        JOIN farms f ON fp.farm_id = f.id
                        JOIN farmers fm ON f.farmer_id = fm.id
                        JOIN users u ON fm.user_id = u.id
                        WHERE pd.id = :produce_id";
                        
        $produceParams = [
            ':produce_id' => $produceId
        ];
        
        $produceDetails = $app->selectOne($produceQuery, $produceParams);
        
        if (!$produceDetails) {
            throw new Exception("Produce delivery not found");
        }
        
        // Update produce status
        $updateQuery = "UPDATE produce_deliveries SET 
                        status = 'rejected'
                        WHERE id = :produce_id";
                    
        $updateParams = [
            ':produce_id' => $produceId
        ];
        
        $app->updateToken($updateQuery, $updateParams);
        
        // Add comment with rejection reason
        $commentQuery = "INSERT INTO comments (
                            user_id,
                            comment_type_id,
                            reference_type,
                            reference_id,
                            comment,
                            created_at
                        ) VALUES (
                            :user_id,
                            :comment_type_id,
                            'produce_delivery',
                            :reference_id,
                            :comment,
                            NOW()
                        )";
                        
        $commentParams = [
            ':user_id' => $staffId,
            ':comment_type_id' => 5, // produce_rejection type
            ':reference_id' => $produceId,
            ':comment' => $rejectionReason
        ];
        
        $app->insertWithoutPath($commentQuery, $commentParams);
        
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
                                'rejected',
                                :description,
                                NOW()
                            )";
                            
        $produceLogParams = [
            ':produce_delivery_id' => $produceId,
            ':user_id' => $staffId,
            ':description' => "Produce delivery rejected. Reason: " . $rejectionReason
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
                            'produce_rejected',
                            :description,
                            NOW()
                        )";
                        
        $activityParams = [
            ':user_id' => $staffId,
            ':description' => "Produce delivery (ID: " . $produceId . ") from " . 
                             $produceDetails->farmer_name . " was rejected. Quantity: " . 
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
            'status' => 'rejected',
            'rejection_reason' => $rejectionReason
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
            'message' => 'Produce rejected successfully'
        ]);
        
    } catch (Exception $e) {
        // Rollback on error
        $app->rollBack();
        
        echo json_encode([
            'success' => false,
            'message' => 'Error rejecting produce: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}