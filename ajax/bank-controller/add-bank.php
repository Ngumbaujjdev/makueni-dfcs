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
        
        // Insert into banks table
        $query = "INSERT INTO banks (
                    name,
                    branch,
                    location,
                    phone
                ) VALUES (
                    :name,
                    :branch,
                    :location,
                    :phone
                )";
        
        $params = [
            ":name" => $_POST['name'],
            ":branch" => $_POST['branch'],
            ":location" => $_POST['location'],
            ":phone" => $_POST['phone']
        ];
        
        $app->insertWithoutPath($query, $params);
        $bankId = $app->lastInsertId();
        
        // Log the activity
        $activityQuery = "INSERT INTO activity_logs (user_id, activity_type, description) 
                         VALUES (:user_id, :activity_type, :description)";
        $activityParams = [
            ":user_id" => $_SESSION['user_id'],
            ":activity_type" => 'bank_added',
            ":description" => "New bank added: {$_POST['name']}"
        ];
        $app->insertWithoutPath($activityQuery, $activityParams);
        
        // Add audit log for bank creation
        $auditQuery = "INSERT INTO audit_logs (user_id, action_type, table_name, record_id, new_values) 
                      VALUES (:user_id, :action_type, :table_name, :record_id, :new_values)";
        $auditParams = [
            ":user_id" => $_SESSION['user_id'],
            ":action_type" => 'create',
            ":table_name" => 'banks',
            ":record_id" => $bankId,
            ":new_values" => json_encode($_POST)
        ];
        $app->insertWithoutPath($auditQuery, $auditParams);
        
        // Commit transaction
        $app->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Bank added successfully'
        ]);
    } catch (Exception $e) {
        $app->rollBack();
        echo json_encode([
            'success' => false,
            'message' => 'Failed to add bank: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No data provided'
    ]);
}
?>