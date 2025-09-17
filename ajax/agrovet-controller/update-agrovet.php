<?php
include "../../config/config.php";
include "../../libs/App.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if (isset($_POST['id'])) {
    try {
        $app = new App;
        $app->beginTransaction();

        // Get agrovet details
        $agrovetId = $_POST['id'];
        $query = "SELECT * FROM agrovets WHERE id = :agrovet_id";
        $agrovet = $app->selectOne($query, [':agrovet_id' => $agrovetId]);

        if (!$agrovet) {
            throw new Exception("Agrovet not found");
        }

        // Check if license number changed and validate uniqueness
        if ($agrovet->license_number !== $_POST['license_number']) {
            $checkQuery = "SELECT COUNT(*) as count FROM agrovets 
                          WHERE license_number = :license_number AND id != :id";
            $result = $app->selectOne($checkQuery, [
                ':license_number' => $_POST['license_number'],
                ':id' => $agrovetId
            ]);

            if ($result->count > 0) {
                throw new Exception('License number already exists');
            }
        }

        // Update agrovets table
        $agrovetQuery = "UPDATE agrovets SET 
                        name = :name,
                        license_number = :license_number,
                        location = :location,
                        phone = :phone,
                        email = :email,
                        address = :address
                        WHERE id = :id";

        $agrovetParams = [
            ":name" => $_POST['name'],
            ":license_number" => $_POST['license_number'],
            ":location" => $_POST['location'],
            ":phone" => $_POST['phone'],
            ":email" => $_POST['email'],
            ":address" => $_POST['address'],
            ":id" => $agrovetId
        ];

        $app->updateToken($agrovetQuery, $agrovetParams);

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
            ':activity_type' => 'agrovet_updated',
            ':description' => "Agrovet updated: {$_POST['name']} at {$_POST['location']}"
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

        $auditParams = [
            ':user_id' => $_SESSION['user_id'],
            ':action_type' => 'update',
            ':table_name' => 'agrovets',
            ':record_id' => $agrovetId,
            ':old_values' => json_encode([
                'name' => $agrovet->name,
                'license_number' => $agrovet->license_number,
                'location' => $agrovet->location,
                'phone' => $agrovet->phone,
                'email' => $agrovet->email,
                'address' => $agrovet->address
            ]),
            ':new_values' => json_encode([
                'name' => $_POST['name'],
                'license_number' => $_POST['license_number'],
                'location' => $_POST['location'],
                'phone' => $_POST['phone'],
                'email' => $_POST['email'],
                'address' => $_POST['address']
            ])
        ];

        $app->insertWithoutPath($auditQuery, $auditParams);

        $app->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Agrovet details updated successfully'
        ]);

    } catch (Exception $e) {
        $app->rollBack();
        echo json_encode([
            'success' => false,
            'message' => 'Update failed: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No data provided'
    ]);
}
?>