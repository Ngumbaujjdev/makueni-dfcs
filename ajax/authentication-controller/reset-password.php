<?php
include "../../config/config.php";
include "../../libs/App.php";

if (isset($_POST["password"]) && isset($_POST["token"])) {
    try {
        $app = new App;
        $app->beginTransaction();
        
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $token = $_POST['token'];
        
        // First, verify the token exists and get user details
        $query = "SELECT * FROM users WHERE reset_token='{$token}'";
        $users = $app->select_all($query);
        
        if (empty($users)) {
            throw new Exception('Invalid or expired reset token');
        }
        
        $user = $users[0];
        $userId = $user->id;
        $oldPasswordHash = $user->password; // For audit logging
        
        // Update the user's password and clear the reset token
        $updateQuery = "UPDATE users SET password = :password, reset_token = NULL WHERE reset_token = :token";
        $updateParams = [
            ":password" => $password,
            ":token" => $token
        ];
        
        $app->updateToken($updateQuery, $updateParams);
        
        // Set session data for automatic login
        $_SESSION['user_id'] = $user->id;
        $_SESSION['email'] = $user->email;
        $_SESSION['role_id'] = $user->role_id;
        
        // Log the activity
        $activityQuery = "INSERT INTO activity_logs 
                         (user_id, activity_type, description) 
                         VALUES (:user_id, :activity_type, :description)";
        
        $activityParams = [
            ':user_id' => $userId,
            ':activity_type' => 'password_reset',
            ':description' => "Password reset completed for user: {$user->first_name} {$user->last_name} ({$user->email})"
        ];
        
        $app->insertWithoutPath($activityQuery, $activityParams);
        
        // Add audit log for password change
        $auditQuery = "INSERT INTO audit_logs 
                      (user_id, action_type, table_name, record_id, old_values, new_values) 
                      VALUES (:user_id, :action_type, :table_name, :record_id, :old_values, :new_values)";
        
        $auditParams = [
            ':user_id' => $userId,
            ':action_type' => 'update',
            ':table_name' => 'users',
            ':record_id' => $userId,
            ':old_values' => json_encode([
                'password_changed' => true,
                'reset_token_used' => $token,
                'previous_password_hash' => substr($oldPasswordHash, 0, 20) . '...' // Partial hash for security
            ]),
            ':new_values' => json_encode([
                'password_updated' => true,
                'reset_token_cleared' => true,
                'reset_method' => 'forgot_password_flow',
                'reset_timestamp' => date('Y-m-d H:i:s')
            ])
        ];
        
        $app->insertWithoutPath($auditQuery, $auditParams);
        
        // Log the successful login activity
        $loginActivityQuery = "INSERT INTO activity_logs 
                              (user_id, activity_type, description) 
                              VALUES (:user_id, :activity_type, :description)";
        
        $loginActivityParams = [
            ':user_id' => $userId,
            ':activity_type' => 'login',
            ':description' => "User logged in after password reset: {$user->first_name} {$user->last_name}"
        ];
        
        $app->insertWithoutPath($loginActivityQuery, $loginActivityParams);
        
        // Determine user role for response
        $roleNames = [
            1 => 'Farmer',
            2 => 'SACCO Staff', 
            3 => 'Bank Staff',
            4 => 'Agrovet Staff',
            5 => 'System Administrator'
        ];
        
        $userRole = $roleNames[$user->role_id] ?? 'User';
        
        $app->commit();
        
        echo json_encode([
            'success' => true,
            'message' => "Password reset successful! Welcome back, {$user->first_name}!",
            'role_id' => $user->role_id,
            'user_role' => $userRole,
            'user_name' => $user->first_name . ' ' . $user->last_name
        ]);
        
    } catch (Exception $e) {
        $app->rollBack();
        error_log("Password reset failed: " . $e->getMessage());
        
        // Log the failed attempt if we have user context
        if (isset($userId)) {
            try {
                $failedActivityQuery = "INSERT INTO activity_logs 
                                       (user_id, activity_type, description) 
                                       VALUES (:user_id, :activity_type, :description)";
                
                $failedActivityParams = [
                    ':user_id' => $userId,
                    ':activity_type' => 'password_reset_failed',
                    ':description' => "Password reset failed: " . $e->getMessage()
                ];
                
                $app->insertWithoutPath($failedActivityQuery, $failedActivityParams);
            } catch (Exception $logError) {
                error_log("Failed to log password reset failure: " . $logError->getMessage());
            }
        }
        
        echo json_encode([
            'success' => false,
            'message' => 'Password reset failed: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters'
    ]);
}
?>