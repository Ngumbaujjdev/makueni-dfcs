<?php
include "../../config/config.php";
include "../../libs/App.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if (isset($_POST['first_name'])) {
    try {
        $app = new App;
        $app->beginTransaction();

        // Validate email uniqueness
        $checkEmailQuery = "SELECT COUNT(*) as count FROM users WHERE email = :email";
        $emailResult = $app->selectOne($checkEmailQuery, [':email' => $_POST['email']]);

        if ($emailResult->count > 0) {
            throw new Exception('Email already exists');
        }

        // Validate staff ID uniqueness
        $checkStaffIdQuery = "SELECT COUNT(*) as count FROM sacco_staff WHERE staff_id = :staff_id";
        $staffIdResult = $app->selectOne($checkStaffIdQuery, [':staff_id' => $_POST['staff_id']]);

        if ($staffIdResult->count > 0) {
            throw new Exception('Staff ID already exists');
        }

        // First create user account
        $userQuery = "INSERT INTO users (
            first_name,
            last_name,
            email,
            phone,
            username,
            password,
            role_id
        ) VALUES (
            :first_name,
            :last_name,
            :email,
            :phone,
            :username,
            :password,
            :role_id
        )";

        $userParams = [
            ":first_name" => $_POST['first_name'],
            ":last_name" => $_POST['last_name'],
            ":email" => $_POST['email'],
            ":phone" => $_POST['phone'],
            ":username" => $_POST['username'],
            ":password" => password_hash($_POST['password'], PASSWORD_DEFAULT),
            ":role_id" => 2  // Role ID for SACCO staff
        ];

        $app->insertWithoutPath($userQuery, $userParams);
        $userId = $app->lastInsertId();

        // Insert into sacco_staff table
        $staffQuery = "INSERT INTO sacco_staff (
            user_id,
            position,
            staff_id,
            department
        ) VALUES (
            :user_id,
            :position,
            :staff_id,
            :department
        )";

        $staffParams = [
            ":user_id" => $userId,
            ":position" => $_POST['position'],
            ":staff_id" => $_POST['staff_id'],
            ":department" => $_POST['department']
        ];

        $app->insertWithoutPath($staffQuery, $staffParams);
        $staffId = $app->lastInsertId();

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
            ':activity_type' => 'sacco_staff_added',
            ':description' => "New SACCO staff added: {$_POST['first_name']} {$_POST['last_name']}"
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
            ':table_name' => 'sacco_staff',
            ':record_id' => $staffId,
            ':new_values' => json_encode([
                'user_id' => $userId,
                'position' => $_POST['position'],
                'staff_id' => $_POST['staff_id'],
                'department' => $_POST['department']
            ])
        ];

        $app->insertWithoutPath($auditQuery, $auditParams);

        $app->commit();

        echo json_encode([
            'success' => true,
            'message' => 'SACCO staff member added successfully'
        ]);

    } catch (Exception $e) {
        $app->rollBack();
        echo json_encode([
            'success' => false,
            'message' => 'Failed to add staff member: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No data provided'
    ]);
}
?>