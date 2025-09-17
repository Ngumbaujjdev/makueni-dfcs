<?php
include "../../config/config.php";
include "../../libs/App.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if (isset($_POST['deletesend'])) {
    try {
        $app = new App;
        $app->beginTransaction();
        
        $staffId = $_POST['deletesend'];
        
        // Get staff details
        $query = "SELECT bs.*, u.email FROM bank_staff bs 
                  LEFT JOIN users u ON bs.user_id = u.id
                  WHERE bs.id = :staff_id";
        $staff = $app->selectOne($query, [':staff_id' => $staffId]);
        
        if (!$staff) {
            throw new Exception("Staff member not found");
        }
        
        // Delete from bank_staff table
        $staffQuery = "DELETE FROM bank_staff WHERE id ='{$staff->user_id}'";
        $staffParams = [":id" => $staffId];
        $app->delete_without_path($staffQuery);
        
        // Delete from users table
        $userQuery = "DELETE FROM users WHERE id = '{$staff->user_id}'";
        $userParams = [":user_id" => $staff->user_id];
        $app->delete_without_path($userQuery);
        
        // Log the activity
        $activityQuery = "INSERT INTO activity_logs (user_id, activity_type, description) 
                         VALUES (:user_id, :activity_type, :description)";
        $activityParams = [
            ":user_id" => $_SESSION['user_id'],
            ":activity_type" => 'bank_staff_deleted',
            ":description" => "Bank staff member deleted: {$staff->first_name} {$staff->last_name}"
        ];
        $app->insertWithoutPath($activityQuery, $activityParams);
        
        // Add audit log for user deletion
        $userAuditQuery = "INSERT INTO audit_logs (user_id, action_type, table_name, record_id, old_values) 
                          VALUES (:user_id, :action_type, :table_name, :record_id, :old_values)";
        $userAuditParams = [
            ":user_id" => $_SESSION['user_id'],
            ":action_type" => 'delete',
            ":table_name" => 'users',
            ":record_id" => $staff->user_id,
            ":old_values" => json_encode([
                'first_name' => $staff->first_name,
                'last_name' => $staff->last_name,
                'email' => $staff->email
            ])
        ];
        $app->insertWithoutPath($userAuditQuery, $userAuditParams);
        
        // Add audit log for bank staff deletion
        $bankStaffAuditQuery = "INSERT INTO audit_logs (user_id, action_type, table_name, record_id, old_values) 
                               VALUES (:user_id, :action_type, :table_name, :record_id, :old_values)";
        $bankStaffAuditParams = [
            ":user_id" => $_SESSION['user_id'],
            ":action_type" => 'delete',
            ":table_name" => 'bank_staff',
            ":record_id" => $staffId,
            ":old_values" => json_encode([
                'position' => $staff->position,
                'staff_id' => $staff->staff_id,
                'department' => $staff->department
            ])
        ];
        $app->insertWithoutPath($bankStaffAuditQuery, $bankStaffAuditParams);
        
        $app->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Bank staff member deleted successfully'
        ]);
    } catch (Exception $e) {
        $app->rollBack();
        echo json_encode([
            'success' => false,
            'message' => 'Deletion failed: ' . $e->getMessage()
        ]);
    }
}
?>