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

        // Get staff details with user information
        $staffId = $_POST['id'];
        $query = "SELECT s.*, u.id as user_id, u.email, u.first_name, u.last_name 
                 FROM agrovet_staff s
                 INNER JOIN users u ON s.user_id = u.id 
                 WHERE s.id = :staff_id";
        $staff = $app->selectOne($query, [':staff_id' => $staffId]);

        if (!$staff) {
            throw new Exception("Staff member not found");
        }

        // Check employee number uniqueness if changed
        if ($staff->employee_number !== $_POST['employee_number']) {
            $empQuery = "SELECT COUNT(*) as count FROM agrovet_staff 
                        WHERE employee_number = :employee_number AND id != :staff_id";
            $empResult = $app->selectOne($empQuery, [
                ':employee_number' => $_POST['employee_number'],
                ':staff_id' => $staffId
            ]);
            
            if ($empResult->count > 0) {
                throw new Exception('Employee number already exists');
            }
        }

        // Check email uniqueness if changed
        if ($staff->email !== $_POST['email']) {
            $emailQuery = "SELECT COUNT(*) as count FROM users 
                          WHERE email = :email AND id != :user_id";
            $emailResult = $app->selectOne($emailQuery, [
                ':email' => $_POST['email'],
                ':user_id' => $staff->user_id
            ]);
            
            if ($emailResult->count > 0) {
                throw new Exception('Email already exists');
            }
        }

        // Update users table
        $userQuery = "UPDATE users SET 
                     first_name = :first_name,
                     last_name = :last_name,
                     email = :email";
        
        $userParams = [
            ':first_name' => $_POST['first_name'],
            ':last_name' => $_POST['last_name'],
            ':email' => $_POST['email'],
            ':user_id' => $staff->user_id
        ];

        // Add password update if provided
        if (!empty($_POST['password'])) {
            $userQuery .= ", password = :password";
            $userParams[':password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }

        $userQuery .= " WHERE id = :user_id";
        $app->updateToken($userQuery, $userParams);

        // Update agrovet_staff table
        $staffQuery = "UPDATE agrovet_staff SET 
                      position = :position,
                      employee_number = :employee_number,
                      phone = :phone
                      WHERE id = :id";

        $staffParams = [
            ':position' => $_POST['position'],
            ':employee_number' => $_POST['employee_number'],
            ':phone' => $_POST['phone'],
            ':id' => $staffId
        ];

        $app->updateToken($staffQuery, $staffParams);

        // Log the activity
        $activityQuery = "INSERT INTO activity_logs (user_id, activity_type, description) 
                         VALUES (:user_id, :activity_type, :description)";
        
        $activityParams = [
            ':user_id' => $_SESSION['user_id'],
            ':activity_type' => 'agrovet_staff_updated',
            ':description' => "Agrovet staff updated: {$_POST['first_name']} {$_POST['last_name']}"
        ];

        $app->insertWithoutPath($activityQuery, $activityParams);

        // Add audit log for user update
        $oldUserValues = [
            'first_name' => $staff->first_name,
            'last_name' => $staff->last_name,
            'email' => $staff->email
        ];

        $newUserValues = [
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'email' => $_POST['email']
        ];

        if (!empty($_POST['password'])) {
            $newUserValues['password'] = 'password_updated';
        }

        $userAuditQuery = "INSERT INTO audit_logs 
                          (user_id, action_type, table_name, record_id, old_values, new_values) 
                          VALUES (:user_id, :action_type, :table_name, :record_id, :old_values, :new_values)";

        $userAuditParams = [
            ':user_id' => $_SESSION['user_id'],
            ':action_type' => 'update',
            ':table_name' => 'users',
            ':record_id' => $staff->user_id,
            ':old_values' => json_encode($oldUserValues),
            ':new_values' => json_encode($newUserValues)
        ];

        $app->insertWithoutPath($userAuditQuery, $userAuditParams);

        // Add audit log for staff update
        $oldStaffValues = [
            'position' => $staff->position,
            'employee_number' => $staff->employee_number,
            'phone' => $staff->phone
        ];

        $newStaffValues = [
            'position' => $_POST['position'],
            'employee_number' => $_POST['employee_number'],
            'phone' => $_POST['phone']
        ];

        $staffAuditQuery = "INSERT INTO audit_logs 
                           (user_id, action_type, table_name, record_id, old_values, new_values) 
                           VALUES (:user_id, :action_type, :table_name, :record_id, :old_values, :new_values)";

        $staffAuditParams = [
            ':user_id' => $_SESSION['user_id'],
            ':action_type' => 'update',
            ':table_name' => 'agrovet_staff',
            ':record_id' => $staffId,
            ':old_values' => json_encode($oldStaffValues),
            ':new_values' => json_encode($newStaffValues)
        ];

        $app->insertWithoutPath($staffAuditQuery, $staffAuditParams);

        $app->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Staff details updated successfully'
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