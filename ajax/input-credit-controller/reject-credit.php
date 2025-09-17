<?php
include "../../config/config.php";
include "../../libs/App.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $app = new App;
        
        // Begin transaction
        $app->beginTransaction();

        // Get data from POST
        $creditId = isset($_POST['creditId']) ? intval($_POST['creditId']) : 0;
        $rejectionReason = isset($_POST['rejectionReason']) ? $_POST['rejectionReason'] : '';
        $rejectionCategory = isset($_POST['rejectionCategory']) ? $_POST['rejectionCategory'] : '';
        $recommendationNotes = isset($_POST['recommendationNotes']) ? $_POST['recommendationNotes'] : '';
        
        // Validate inputs
        if ($creditId <= 0) {
            throw new Exception("Invalid credit application ID");
        }
        
        if (empty($rejectionReason)) {
            throw new Exception("Rejection reason cannot be empty");
        }
        
        // Get agrovet staff ID from session
        $userId = $_SESSION['user_id'];
        
        // Get staff information
        $staffQuery = "SELECT s.id as staff_id, s.agrovet_id 
                      FROM agrovet_staff s 
                      WHERE s.user_id = :user_id";
        
        $staff = $app->selectOne($staffQuery, [':user_id' => $userId]);
        
        if (!$staff) {
            throw new Exception("Staff information not found");
        }
        
        // First, get the credit application details for logging purposes
        $creditQuery = "SELECT 
                        ica.farmer_id,
                        ica.agrovet_id,
                        ica.total_amount,
                        CONCAT(u.first_name, ' ', u.last_name) as farmer_name
                      FROM input_credit_applications ica
                      JOIN farmers f ON ica.farmer_id = f.id
                      JOIN users u ON f.user_id = u.id
                      WHERE ica.id = :credit_id
                      AND ica.status = 'under_review'
                      AND ica.agrovet_id = :agrovet_id";
                      
        $creditParams = [
            ':credit_id' => $creditId,
            ':agrovet_id' => $staff->agrovet_id
        ];
        
        $creditDetails = $app->selectOne($creditQuery, $creditParams);
        
        if (!$creditDetails) {
            throw new Exception("Input credit application not found or not in reviewable status");
        }
        
        // 1. Update the credit application status to rejected
        $updateCreditQuery = "UPDATE input_credit_applications SET 
                            status = 'rejected',
                            rejection_reason = :rejection_reason,
                            reviewed_by = :reviewed_by,
                            review_date = NOW()
                            WHERE id = :credit_id";
                        
        $updateCreditParams = [
            ':credit_id' => $creditId,
            ':rejection_reason' => $rejectionReason,
            ':reviewed_by' => $staff->staff_id
        ];
        
        $app->updateToken($updateCreditQuery, $updateCreditParams);
        
        // 2. Add comment with rejection reason
        $commentQuery = "INSERT INTO comments (
                            user_id,
                            comment_type_id,
                            reference_type,
                            reference_id,
                            comment,
                            is_rejection_reason,
                            created_at
                        ) VALUES (
                            :user_id,
                            :comment_type_id,
                            'input_credit_application',
                            :reference_id,
                            :comment,
                            1,
                            NOW()
                        )";
                        
        $commentParams = [
            ':user_id' => $userId,
            ':comment_type_id' => 10, // produce_rejection can be used, or add a specific input_credit_rejection type
            ':reference_id' => $creditId,
            ':comment' => $rejectionReason
        ];
        
        $app->insertWithoutPath($commentQuery, $commentParams);
        
        // 3. Add recommendation notes if provided
        if (!empty($recommendationNotes)) {
            $recommendationQuery = "INSERT INTO comments (
                                user_id,
                                comment_type_id,
                                reference_type,
                                reference_id,
                                comment,
                                created_at
                            ) VALUES (
                                :user_id,
                                :comment_type_id,
                                'input_credit_application',
                                :reference_id,
                                :comment,
                                NOW()
                            )";
                            
            $recommendationParams = [
                ':user_id' => $userId,
                ':comment_type_id' => 7, // general comment type
                ':reference_id' => $creditId,
                ':comment' => "Recommendation: " . $recommendationNotes
            ];
            
            $app->insertWithoutPath($recommendationQuery, $recommendationParams);
        }
        
        // 4. Add input credit log
        $creditLogQuery = "INSERT INTO input_credit_logs (
                            input_credit_application_id,
                            user_id,
                            action_type,
                            description,
                            created_at
                        ) VALUES (
                            :input_credit_application_id,
                            :user_id,
                            'rejected',
                            :description,
                            NOW()
                        )";
                        
        $creditLogParams = [
            ':input_credit_application_id' => $creditId,
            ':user_id' => $userId,
            ':description' => "Input credit application rejected. Reason: " . $rejectionReason . 
                             ". Category: " . $rejectionCategory . 
                             (!empty($recommendationNotes) ? ". Recommendation: " . $recommendationNotes : "")
        ];
        
        $app->insertWithoutPath($creditLogQuery, $creditLogParams);
        
        // 5. Add activity log
        $activityQuery = "INSERT INTO activity_logs (
                            user_id,
                            activity_type,
                            description,
                            created_at
                        ) VALUES (
                            :user_id,
                            'input_credit_rejected',
                            :description,
                            NOW()
                        )";
                        
        $activityParams = [
            ':user_id' => $userId,
            ':description' => "Input credit application (ID: " . $creditId . ") from " . 
                             $creditDetails->farmer_name . " was rejected. Reason category: " . 
                             $rejectionCategory
        ];
        
        $app->insertWithoutPath($activityQuery, $activityParams);
        
        // 6. Add audit log
        $auditQuery = "INSERT INTO audit_logs (
                        user_id,
                        action_type,
                        table_name,
                        record_id,
                        old_values,
                        new_values,
                        created_at
                    ) VALUES (
                        :user_id,
                        'update',
                        'input_credit_applications',
                        :record_id,
                        :old_values,
                        :new_values,
                        NOW()
                    )";
                    
        $oldValues = [
            'status' => 'under_review',
            'rejection_reason' => null
        ];
        
        $newValues = [
            'status' => 'rejected',
            'rejection_reason' => $rejectionReason,
            'reviewed_by' => $staff->staff_id,
            'review_date' => date('Y-m-d H:i:s')
        ];
        
        $auditParams = [
            ':user_id' => $userId,
            ':record_id' => $creditId,
            ':old_values' => json_encode($oldValues),
            ':new_values' => json_encode($newValues)
        ];
        
        $app->insertWithoutPath($auditQuery, $auditParams);
        
        // Commit transaction
        $app->commit();
        
        // Return success response
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Input credit application rejected successfully'
        ]);
        
    } catch (Exception $e) {
        // Rollback on error
        $app->rollBack();
        
        // Make sure we send a proper JSON response
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Error rejecting input credit: ' . $e->getMessage()
        ]);
    }
} else {
    // Make sure we send a proper JSON response
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}