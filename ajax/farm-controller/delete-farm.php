<?php
include "../../config/config.php";
include "../../libs/App.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if (isset($_POST['deletesend'])) {
    try {
        $app = new App;
        $app->beginTransaction();
        
        $farmId = $_POST['deletesend'];
        
        // Get farm details before deletion
        $query = "SELECT f.*, 
                  GROUP_CONCAT(DISTINCT ft.name) as fruits,
                  COALESCE(SUM(fp.estimated_production), 0) as total_production
                  FROM farms f
                  LEFT JOIN farm_fruit_mapping ffm ON f.id = ffm.farm_id
                  LEFT JOIN fruit_types ft ON ffm.fruit_type_id = ft.id
                  LEFT JOIN farm_products fp ON f.id = fp.farm_id
                  WHERE f.id = :farm_id AND f.farmer_id = :farmer_id
                  GROUP BY f.id";
        
        $farm = $app->selectOne($query, [
            ':farm_id' => $farmId,
            ':farmer_id' => $_SESSION['user_id']
        ]);
        
        if (!$farm) {
            throw new Exception("Farm not found or you don't have permission to delete it");
        }

        // Delete farm products
        $deleteProductsQuery = "DELETE FROM farm_products WHERE farm_id = :farm_id";
        $app->delete($deleteProductsQuery, [':farm_id' => $farmId]);

        // Delete farm fruit mappings
        $deleteMappingsQuery = "DELETE FROM farm_fruit_mapping WHERE farm_id = :farm_id";
        $app->delete($deleteMappingsQuery, [':farm_id' => $farmId]);

        // Delete expected produce
        $deleteExpectedQuery = "DELETE ep FROM expected_produce ep 
                              INNER JOIN farm_products fp ON ep.farm_product_id = fp.id 
                              WHERE fp.farm_id = :farm_id";
        $app->delete($deleteExpectedQuery, [':farm_id' => $farmId]);

        // Finally delete the farm
        $deleteFarmQuery = "DELETE FROM farms WHERE id = :farm_id AND farmer_id = :farmer_id";
        $app->delete($deleteFarmQuery, [
            ':farm_id' => $farmId,
            ':farmer_id' => $_SESSION['user_id']
        ]);
        
        // Log the activity
        $activityQuery = "INSERT INTO activity_logs (
            user_id, 
            activity_type, 
            description,
            created_at
        ) VALUES (
            :user_id,
            'farm_deleted',
            :description,
            NOW()
        )";
        
        $activityParams = [
            ":user_id" => $_SESSION['user_id'],
            ":description" => "Farm deleted: {$farm->name} ({$farm->size} acres)"
        ];
        $app->insertWithoutPath($activityQuery, $activityParams);
        
        // Add audit log for farm deletion
        $auditQuery = "INSERT INTO audit_logs (
            user_id,
            action_type,
            table_name,
            record_id,
            old_values,
            created_at
        ) VALUES (
            :user_id,
            'delete',
            'farms',
            :record_id,
            :old_values,
            NOW()
        )";
        
        $auditParams = [
            ":user_id" => $_SESSION['user_id'],
            ":record_id" => $farmId,
            ":old_values" => json_encode([
                'name' => $farm->name,
                'location' => $farm->location,
                'size' => $farm->size,
                'fruits' => $farm->fruits,
                'total_production' => $farm->total_production
            ])
        ];
        $app->insertWithoutPath($auditQuery, $auditParams);
        
        $app->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Farm deleted successfully'
        ]);
    } catch (Exception $e) {
        $app->rollBack();
        echo json_encode([
            'success' => false,
            'message' => 'Deletion failed: ' . $e->getMessage()
        ]);
    }
}
?>