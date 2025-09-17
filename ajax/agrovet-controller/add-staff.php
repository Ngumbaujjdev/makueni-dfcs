<?php
include "../../config/config.php";
include "../../libs/App.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if (isset($_POST['agrovet_id'])) {
    try {
        $app = new App;
        $app->beginTransaction();

        // Validate employee number uniqueness
        $checkEmployeeQuery = "SELECT COUNT(*) as count FROM agrovet_staff 
                             WHERE employee_number = :employee_number";
        $employeeResult = $app->selectOne($checkEmployeeQuery, 
            [':employee_number' => $_POST['employee_number']]);

        if ($employeeResult->count > 0) {
            throw new Exception('Employee number already exists');
        }

        // Validate email uniqueness
        $checkEmailQuery = "SELECT COUNT(*) as count FROM users WHERE email = :email";
        $emailResult = $app->selectOne($checkEmailQuery, [':email' => $_POST['email']]);

        if ($emailResult->count > 0) {
            throw new Exception('Email already exists');
        }

        // First create user account
        $userQuery = "INSERT INTO users (
            first_name,
            last_name,
            email,
            password,
            username,
            role_id
        ) VALUES (
            :first_name,
            :last_name,
            :email,
            :password,
            :username,
            :role_id
        )";

        // Using email as username
        $userParams = [
            ":first_name" => $_POST['first_name'],
            ":last_name" => $_POST['last_name'],
            ":email" => $_POST['email'],
            ":password" => password_hash($_POST['password'], PASSWORD_DEFAULT),
            ":username" => $_POST['email'], // Using email as username
            ":role_id" => 4 // Role ID for agrovet staff
        ];

        $app->insertWithoutPath($userQuery, $userParams);
        $userId = $app->lastInsertId();

        // Insert into agrovet_staff table
        $staffQuery = "INSERT INTO agrovet_staff (
            user_id,
            agrovet_id,
            position,
            employee_number,
            phone,
            is_active
        ) VALUES (
            :user_id,
            :agrovet_id,
            :position,
            :employee_number,
            :phone,
            :is_active
        )";

        $staffParams = [
            ":user_id" => $userId,
            ":agrovet_id" => $_POST['agrovet_id'],
            ":position" => $_POST['position'],
            ":employee_number" => $_POST['employee_number'],
            ":phone" => $_POST['phone'],
            ":is_active" => 1 // Setting as active by default
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
            ':activity_type' => 'agrovet_staff_added',
            ':description' => "New agrovet staff added: {$_POST['first_name']} {$_POST['last_name']}"
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
            ':table_name' => 'agrovet_staff',
            ':record_id' => $staffId,
            ':new_values' => json_encode([
                'user_id' => $userId,
                'agrovet_id' => $_POST['agrovet_id'],
                'position' => $_POST['position'],
                'employee_number' => $_POST['employee_number'],
                'phone' => $_POST['phone'],
                'is_active' => 1
            ])
        ];

        $app->insertWithoutPath($auditQuery, $auditParams);

        $app->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Staff member added successfully'
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