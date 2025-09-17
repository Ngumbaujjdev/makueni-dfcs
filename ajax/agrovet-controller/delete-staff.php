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
        $query = "SELECT s.*, u.email, u.first_name, u.last_name, a.name as agrovet_name 
                 FROM agrovet_staff s
                 INNER JOIN users u ON s.user_id = u.id
                 LEFT JOIN agrovets a ON s.agrovet_id = a.id
                 WHERE s.id = :staff_id";
        
        $staff = $app->selectOne($query, [':staff_id' => $staffId]);
        
        if (!$staff) {
            throw new Exception("Staff member not found");
        }
        
        // First delete from agrovet_staff table (child table)
        $staffQuery = "DELETE FROM agrovet_staff WHERE id = '{$staffId}'";
        $app->delete_without_path($staffQuery);
        
        // Then delete from users table (parent table)
        $userQuery = "DELETE FROM users WHERE id = '{$staff->user_id}'";
        $app->delete_without_path($userQuery);
        
        // Log the activity
        $activityQuery = "INSERT INTO activity_logs (user_id, activity_type, description) 
                         VALUES (:user_id, :activity_type, :description)";
        
        $activityParams = [
            ":user_id" => $_SESSION['user_id'],
            ":activity_type" => 'agrovet_staff_deleted',
            ":description" => "Agrovet staff member deleted: {$staff->first_name} {$staff->last_name} from {$staff->agrovet_name}"
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
        
        // Add audit log for agrovet staff deletion
        $staffAuditQuery = "INSERT INTO audit_logs (user_id, action_type, table_name, record_id, old_values) 
                           VALUES (:user_id, :action_type, :table_name, :record_id, :old_values)";
        
        $staffAuditParams = [
            ":user_id" => $_SESSION['user_id'],
            ":action_type" => 'delete',
            ":table_name" => 'agrovet_staff',
            ":record_id" => $staffId,
            ":old_values" => json_encode([
                'agrovet_id' => $staff->agrovet_id,
                'position' => $staff->position,
                'employee_number' => $staff->employee_number,
                'phone' => $staff->phone,
                'agrovet_name' => $staff->agrovet_name
            ])
        ];
        
        $app->insertWithoutPath($staffAuditQuery, $staffAuditParams);
        
        $app->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Agrovet staff member deleted successfully'
        ]);
        
    } catch (Exception $e) {
        $app->rollBack();
        echo json_encode([
            'success' => false,
            'message' => 'Deletion failed: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No staff ID provided'
    ]);
}
?>a