<?php
include "../../config/config.php";
include "../../libs/App.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if (isset($_POST['name'])) {
    try {
        $app = new App;
        
        // Begin transaction
        $app->beginTransaction();
        
        // Validate license number uniqueness
        $checkQuery = "SELECT COUNT(*) as count FROM agrovets WHERE license_number = :license";
        $result = $app->selectOne($checkQuery, [':license' => $_POST['license_number']]);
        
        if ($result->count > 0) {
            throw new Exception('License number already exists');
        }
        
        // Insert into agrovets table
        $query = "INSERT INTO agrovets (
                    name,
                    type_id,
                    license_number,
                    location,
                    phone,
                    email,
                    address
                ) VALUES (
                    :name,
                    :type_id,
                    :license_number,
                    :location,
                    :phone,
                    :email,
                    :address
                )";
        
        $params = [
            ":name" => $_POST['name'],
            ":type_id" => $_POST['type_id'],
            ":license_number" => $_POST['license_number'],
            ":location" => $_POST['location'],
            ":phone" => $_POST['phone'],
            ":email" => $_POST['email'],
            ":address" => $_POST['address']
        ];
        
        $app->insertWithoutPath($query, $params);
        $agrovetId = $app->lastInsertId();
        
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
            ':activity_type' => 'agrovet_added',
            ':description' => "New agrovet added: {$_POST['name']} at {$_POST['location']}"
        ];
        
        $app->insertWithoutPath($activityQuery, $activityParams);
        
        // Add audit log
        $auditQuery = "INSERT INTO audit_logs (
            user_id,
            action_type,
            table_name,
            record_id,
            new_values
        ) VALUES (
            :user_id,
            :action_type,
            :table_name,
            :record_id,
            :new_values
        )";
        
        $auditParams = [
            ':user_id' => $_SESSION['user_id'],
            ':action_type' => 'create',
            ':table_name' => 'agrovets',
            ':record_id' => $agrovetId,
            ':new_values' => json_encode([
                'name' => $_POST['name'],
                'type_id' => $_POST['type_id'],
                'license_number' => $_POST['license_number'],
                'location' => $_POST['location'],
                'phone' => $_POST['phone'],
                'email' => $_POST['email'],
                'address' => $_POST['address']
            ])
        ];
        
        $app->insertWithoutPath($auditQuery, $auditParams);
        
        // Commit transaction
        $app->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Agrovet added successfully'
        ]);
        
    } catch (Exception $e) {
        $app->rollBack();
        echo json_encode([
            'success' => false,
            'message' => 'Failed to add agrovet: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No data provided'
    ]);
}
?>