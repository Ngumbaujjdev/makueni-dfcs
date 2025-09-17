<?php
include "../../config/config.php";
include "../../libs/App.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if (isset($_POST['farmerId'])) {
    try {
        $app = new App;
        $app->beginTransaction();

        // Get farmer details
        $farmerId = $_POST['farmerId'];
        $query = "SELECT f.*, u.first_name, u.last_name 
                 FROM farmers f 
                 INNER JOIN users u ON f.user_id = u.id 
                 WHERE f.id = :farmer_id";
        $farmer = $app->selectOne($query, [':farmer_id' => $farmerId]);

        if (!$farmer) {
            throw new Exception("Farmer not found");
        }

        if ($farmer->is_verified) {
            throw new Exception("Farmer is already verified");
        }

        // Update farmers table to set verified status
        $farmerQuery = "UPDATE farmers SET 
                       is_verified = 1
                       WHERE id = :id";

        $farmerParams = [":id" => $farmerId];

        $app->updateToken($farmerQuery, $farmerParams);

        // Prepare farmer name and description
        $farmerName = $farmer->first_name . ' ' . $farmer->last_name;
        $description = sprintf("Farmer verified: %s (%s)", 
            $farmerName, 
            $farmer->registration_number
        );

        // Log the activity
        $activityQuery = "INSERT INTO activity_logs (
            user_id,
            activity_type,
            description
        ) VALUES (
            :user_id,
            :activity_type,
            :description
        )";

        $activityParams = [
            ':user_id' => $_SESSION['user_id'],
            ':activity_type' => 'farmer_verified',
            ':description' => $description
        ];

        $app->insertWithoutPath($activityQuery, $activityParams);

        // Add audit log
        $auditQuery = "INSERT INTO audit_logs (
            user_id,
            action_type,
            table_name,
            record_id,
            old_values,
            new_values
        ) VALUES (
            :user_id,
            :action_type,
            :table_name,
            :record_id,
            :old_values,
            :new_values
        )";

        $oldValues = [
            'is_verified' => 0,
            'farmer_name' => $farmerName,
            'registration_number' => $farmer->registration_number
        ];

        $newValues = [
            'is_verified' => 1,
            'farmer_name' => $farmerName,
            'registration_number' => $farmer->registration_number
        ];

        $auditParams = [
            ':user_id' => $_SESSION['user_id'],
            ':action_type' => 'status_change',
            ':table_name' => 'farmers',
            ':record_id' => $farmerId,
            ':old_values' => json_encode($oldValues),
            ':new_values' => json_encode($newValues)
        ];

        $app->insertWithoutPath($auditQuery, $auditParams);

        $app->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Farmer verified successfully'
        ]);

    } catch (Exception $e) {
        $app->rollBack();
        echo json_encode([
            'success' => false,
            'message' => 'Verification failed: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No farmer ID provided'
    ]);
}
?>