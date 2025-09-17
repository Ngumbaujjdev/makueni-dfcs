<?php
include "../../config/config.php";
include "../../libs/App.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $app = new App;
        
        // Begin transaction
        $app->beginTransaction();

        // Get SACCO staff ID from session
        $staff_id = $_SESSION['user_id'];
        
        // Insert produce delivery
        $deliveryQuery = "INSERT INTO produce_deliveries (
            farm_product_id,
            quantity,
            unit_price,
            total_value,
            quality_grade,
            delivery_date,
            received_by,
            status,
            notes,
            created_at
        ) VALUES (
            :farm_product_id,
            :quantity,
            :unit_price,
            :total_value,
            :quality_grade,
            CURDATE(),
            :received_by,
            'pending',
            :notes,
            NOW()
        )";

        $total_value = $_POST['quantity'] * $_POST['unit_price'];
        
        $deliveryParams = [
            ':farm_product_id' => $_POST['product_id'],
            ':quantity' => $_POST['quantity'],
            ':unit_price' => $_POST['unit_price'],
            ':total_value' => $total_value,
            ':quality_grade' => $_POST['quality_grade'],
            ':received_by' => $staff_id,
            ':notes' => $_POST['notes']
        ];

        $app->insertWithoutPath($deliveryQuery, $deliveryParams);
        $deliveryId = $app->lastInsertId();

        // Add initial comment if provided
        if (isset($_POST['comment']) && !empty($_POST['comment'])) {
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
                ':user_id' => $staff_id,
                ':comment_type_id' => 4, // produce_quality type
                ':reference_id' => $deliveryId,
                ':comment' => $_POST['comment']
            ];
            
            $app->insertWithoutPath($commentQuery, $commentParams);
        }
        
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
            'received',
            :description,
            NOW()
        )";
        
        $produceLogParams = [
            ':produce_delivery_id' => $deliveryId,
            ':user_id' => $staff_id,
            ':description' => "Produce delivery received. Quantity: " . $_POST['quantity'] . 
                              " KG, Value: KES " . $total_value
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
            'produce_delivery',
            :description,
            NOW()
        )";

        $activityParams = [
            ':user_id' => $staff_id,
            ':description' => "New produce delivery recorded - Ref: " . $_POST['reference_number']
        ];

        $app->insertWithoutPath($activityQuery, $activityParams);

        // Add audit log
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
            'produce_deliveries',
            :record_id,
            :new_values,
            NOW()
        )";

        $auditValues = [
            'farm_product_id' => $_POST['product_id'],
            'quantity' => $_POST['quantity'],
            'unit_price' => $_POST['unit_price'],
            'total_value' => $total_value,
            'quality_grade' => $_POST['quality_grade'],
            'reference' => $_POST['reference_number']
        ];

        $auditParams = [
            ':user_id' => $staff_id,
            ':record_id' => $deliveryId,
            ':new_values' => json_encode($auditValues)
        ];

        $app->insertWithoutPath($auditQuery, $auditParams);

        // Commit transaction
        $app->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Produce delivery recorded successfully',
            'delivery_id' => $deliveryId
        ]);

    } catch (Exception $e) {
        // Rollback on error
        $app->rollBack();
        
        echo json_encode([
            'success' => false,
            'message' => 'Error recording delivery: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}