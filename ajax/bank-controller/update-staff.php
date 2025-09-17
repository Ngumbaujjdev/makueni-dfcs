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
        
        $bankStaffId = (int)$_POST['id'];
        
        // First, get the current staff details
        $staffQuery = "SELECT bs.*, u.email, u.first_name, u.last_name 
                      FROM bank_staff bs
                      INNER JOIN users u ON bs.user_id = u.id
                      WHERE bs.id = :bank_staff_id";
        
        $staffParams = [':bank_staff_id' => $bankStaffId];
        $staff = $app->selectOne($staffQuery, $staffParams);
        
        if (!$staff) {
            // Add debug logging
            error_log("Staff not found for ID: " . $bankStaffId);
            throw new Exception("Staff member not found");
        }
        
        // Check if email changed
        $emailChanged = ($_POST['email'] !== $staff->email);
        
        if ($emailChanged) {
            $emailQuery = "SELECT COUNT(*) as count FROM users 
                          WHERE email = :email AND id != :user_id";
            $emailParams = [
                ':email' => $_POST['email'],
                ':user_id' => $staff->user_id
            ];
            $emailResult = $app->selectOne($emailQuery, $emailParams);
            
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
        
        // Execute user update
        $app->updateToken($userQuery, $userParams);
        
        // Update bank_staff table
        $bankStaffQuery = "UPDATE bank_staff SET 
                          position = :position,
                          staff_id = :staff_id,
                          department = :department
                          WHERE id = :bank_staff_id";
        
        $bankStaffParams = [
            ':position' => $_POST['position'],
            ':staff_id' => $_POST['staff_id'],
            ':bank_staff_id' => $bankStaffId,
            ':department' => $_POST['department']
        ];
        
        // Execute bank staff update
        $app->updateToken($bankStaffQuery, $bankStaffParams);
        
        // Log the activity
        $activityQuery = "INSERT INTO activity_logs 
                         (user_id, activity_type, description) 
                         VALUES (:user_id, :activity_type, :description)";
        
        $activityParams = [
            ':user_id' => $_SESSION['user_id'],
            ':activity_type' => 'bank_staff_updated',
            ':description' => "Bank staff details updated for: {$_POST['first_name']} {$_POST['last_name']}"
        ];
        
        $app->insertWithoutPath($activityQuery, $activityParams);
        
        // Add audit log for user update
        $userAuditQuery = "INSERT INTO audit_logs 
                          (user_id, action_type, table_name, record_id, old_values, new_values) 
                          VALUES (:user_id, :action_type, :table_name, :record_id, :old_values, :new_values)";
        
        $userAuditParams = [
            ':user_id' => $_SESSION['user_id'],
            ':action_type' => 'update',
            ':table_name' => 'users',
            ':record_id' => $staff->user_id,
            ':old_values' => json_encode([
                'first_name' => $staff->first_name,
                'last_name' => $staff->last_name,
                'email' => $staff->email
            ]),
            ':new_values' => json_encode([
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'email' => $_POST['email']
            ])
        ];
        
        $app->insertWithoutPath($userAuditQuery, $userAuditParams);
        
        // Add audit log for bank staff update
        $bankStaffAuditQuery = "INSERT INTO audit_logs 
                               (user_id, action_type, table_name, record_id, old_values, new_values) 
                               VALUES (:user_id, :action_type, :table_name, :record_id, :old_values, :new_values)";
        
        $bankStaffAuditParams = [
            ':user_id' => $_SESSION['user_id'],
            ':action_type' => 'update',
            ':table_name' => 'bank_staff',
            ':record_id' => $bankStaffId,
            ':old_values' => json_encode([
                'position' => $staff->position,
                'staff_id' => $staff->staff_id,
                'department' => $staff->department
            ]),
            ':new_values' => json_encode([
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
        error_log("Update failed: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Update failed: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No staff ID provided'
    ]);
}
?>