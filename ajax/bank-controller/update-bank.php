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
        
        // Get staff details
        $staffId = $_POST['id'];
        $query = "SELECT bs.*, u.email FROM bank_staff bs 
                  LEFT JOIN users u ON bs.user_id = u.id
                  WHERE bs.id = :staff_id";
        $staff = $app->selectOne($query, [':staff_id' => $staffId]);
        
        if (!$staff) {
            throw new Exception("Staff member not found");
        }
        
        // Check if email changed
        $emailChanged = ($_POST['email'] !== $staff->email);
        
        // Validate email uniqueness if changed
        if ($emailChanged) {
            $checkEmailQuery = "SELECT COUNT(*) as count FROM users WHERE email = :email";
            $emailResult = $app->selectOne($checkEmailQuery, [':email' => $_POST['email']]);
            
            if ($emailResult->count > 0) {
                throw new Exception('Email already exists');
            }
        }
        
        // Update users table
        $userQuery = "UPDATE users SET
                     first_name = :first_name,
                     last_name = :last_name,
                     email = :email";
        
        // Update password if provided
        if (!empty($_POST['password'])) {
            $userQuery .= ", password = :password";
            $userParams[':password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }
        
        $userQuery .= " WHERE id = :user_id";
        
        $userParams = [
            ":first_name" => $_POST['first_name'],
            ":last_name" => $_POST['last_name'],
            ":email" => $_POST['email'],
            ":user_id" => $staff->user_id
        ];
        
        $app->updateToken($userQuery, $userParams);
        
        // Update bank_staff table
        $staffQuery = "UPDATE bank_staff SET
                     position = :position,
                     staff_id = :staff_id,
                     department = :department
                     WHERE id = :id";
        
        $staffParams = [
            ":position" => $_POST['position'],
            ":staff_id" => $_POST['staff_id'],
            ":department" => $_POST['department'],
            ":id" => $staffId
        ];
        
        $app->updateToken($staffQuery, $staffParams);
        
        // Log the activity
        $activityQuery = "INSERT INTO activity_logs (user_id, activity_type, description) 
                         VALUES (:user_id, :activity_type, :description)";
        $activityParams = [
            ":user_id" => $_SESSION['user_id'],
            ":activity_type" => 'bank_staff_updated',
            ":description" => "Bank staff details updated for: {$_POST['first_name']} {$_POST['last_name']}"
        ];
        $app->insertWithoutPath($activityQuery, $activityParams);
        
        // Add audit log for user update
        $userAuditQuery = "INSERT INTO audit_logs (user_id, action_type, table_name, record_id, old_values, new_values) 
                          VALUES (:user_id, :action_type, :table_name, :record_id, :old_values, :new_values)";
        $userAuditParams = [
            ":user_id" => $_SESSION['user_id'],
            ":action_type" => 'update',
            ":table_name" => 'users',
            ":record_id" => $staff->user_id,
            ":old_values" => json_encode([
                'first_name' => $staff->first_name,
                'last_name' => $staff->last_name,
                'email' => $staff->email
            ]),
            ":new_values" => json_encode([
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'email' => $_POST['email']
            ])
        ];
        $app->insertWithoutPath($userAuditQuery, $userAuditParams);
        
        // Add audit log for bank staff update
        $bankStaffAuditQuery = "INSERT INTO audit_logs (user_id, action_type, table_name, record_id, old_values, new_values) 
                               VALUES (:user_id, :action_type, :table_name, :record_id, :old_values, :new_values)";
        $bankStaffAuditParams = [
            ":user_id" => $_SESSION['user_id'],
            ":action_type" => 'update',
            ":table_name" => 'bank_staff',
            ":record_id" => $staffId,
            ":old_values" => json_encode([
                'position' => $staff->position,
                'staff_id' => $staff->staff_id,
                'department' => $staff->department
            ]),
            ":new_values" => json_encode([
                'position' => $_POST['position'],
                'staff_id' => $_POST['staff_id'],
                'department' => $_POST['department']
            ])
        ];
        $app->insertWithoutPath($bankStaffAuditQuery, $bankStaffAuditParams);
        
        $app->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Bank staff details updated successfully'
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