<?php
include "../../config/config.php";
include "../../libs/App.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $app = new App;
        
        // Begin transaction
        $app->beginTransaction();

        // Get data from POST
        $loanId = isset($_POST['loanId']) ? intval($_POST['loanId']) : 0;
        $rejectionReason = isset($_POST['rejectionReason']) ? $_POST['rejectionReason'] : '';
        $rejectionCategory = isset($_POST['rejectionCategory']) ? $_POST['rejectionCategory'] : '';
        $recommendationNotes = isset($_POST['recommendationNotes']) ? $_POST['recommendationNotes'] : '';
        
        // Validate inputs
        if ($loanId <= 0) {
            throw new Exception("Invalid loan ID");
        }
        
        if (empty($rejectionReason)) {
            throw new Exception("Rejection reason cannot be empty");
        }
        
        // Get Bank staff ID from session
        $staffId = $_SESSION['user_id'];
        
        // First, get the bank loan application details for logging purposes
        $loanQuery = "SELECT 
                        la.farmer_id,
                        la.provider_type,
                        la.loan_type_id,
                        la.bank_id,
                        la.amount_requested,
                        b.name as bank_name,
                        CONCAT(u.first_name, ' ', u.last_name) as farmer_name
                      FROM loan_applications la
                      JOIN farmers f ON la.farmer_id = f.id
                      JOIN users u ON f.user_id = u.id
                      LEFT JOIN banks b ON la.bank_id = b.id
                      WHERE la.id = :loan_id
                      AND la.status = 'under_review'
                      AND la.provider_type = 'bank'";
                      
        $loanParams = [
            ':loan_id' => $loanId
        ];
        
        $loanDetails = $app->selectOne($loanQuery, $loanParams);
        
        if (!$loanDetails) {
            throw new Exception("Bank loan application not found or not in reviewable status");
        }
        
        // Get bank staff details to ensure they belong to the same bank
        $bankStaffQuery = "SELECT bank_id FROM bank_staff WHERE user_id = :user_id";
        $bankStaffParams = [':user_id' => $staffId];
        $bankStaff = $app->selectOne($bankStaffQuery, $bankStaffParams);
        
        if (!$bankStaff || $bankStaff->bank_id != $loanDetails->bank_id) {
            throw new Exception("You do not have permission to reject this loan application");
        }
        
        // 1. Update the loan application status to rejected
        $updateLoanQuery = "UPDATE loan_applications SET 
                            status = 'rejected',
                            rejection_reason = :rejection_reason,
                            reviewed_by = :reviewed_by,
                            review_date = NOW()
                            WHERE id = :loan_id
                            AND provider_type = 'bank'";
                        
        $updateLoanParams = [
            ':loan_id' => $loanId,
            ':rejection_reason' => $rejectionReason,
            ':reviewed_by' => $staffId
        ];
        
        $app->updateToken($updateLoanQuery, $updateLoanParams);
        
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
                            'loan_application',
                            :reference_id,
                            :comment,
                            1,
                            NOW()
                        )";
                        
        $commentParams = [
            ':user_id' => $staffId,
            ':comment_type_id' => 3, // loan_rejection type
            ':reference_id' => $loanId,
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
                                'loan_application',
                                :reference_id,
                                :comment,
                                NOW()
                            )";
                            
            $recommendationParams = [
                ':user_id' => $staffId,
                ':comment_type_id' => 7, // general comment type
                ':reference_id' => $loanId,
                ':comment' => "Recommendation: " . $recommendationNotes
            ];
            
            $app->insertWithoutPath($recommendationQuery, $recommendationParams);
        }
        
        // 4. Add loan log
        $loanLogQuery = "INSERT INTO loan_logs (
                            loan_application_id,
                            user_id,
                            action_type,
                            description,
                            created_at
                        ) VALUES (
                            :loan_application_id,
                            :user_id,
                            'rejected',
                            :description,
                            NOW()
                        )";
                        
        $loanLogParams = [
            ':loan_application_id' => $loanId,
            ':user_id' => $staffId,
            ':description' => "Bank loan application rejected. Reason: " . $rejectionReason . 
                             ". Category: " . $rejectionCategory . 
                             (!empty($recommendationNotes) ? ". Recommendation: " . $recommendationNotes : "")
        ];
        
        $app->insertWithoutPath($loanLogQuery, $loanLogParams);
        
        // 5. Add activity log
        $activityQuery = "INSERT INTO activity_logs (
                            user_id,
                            activity_type,
                            description,
                            created_at
                        ) VALUES (
                            :user_id,
                            'bank_loan_rejected',
                            :description,
                            NOW()
                        )";
                        
        $activityParams = [
            ':user_id' => $staffId,
            ':description' => "Bank loan application (ID: " . $loanId . ") from " . 
                             $loanDetails->farmer_name . " was rejected by " . $loanDetails->bank_name . 
                             " bank staff. Reason category: " . $rejectionCategory
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
                        'loan_applications',
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
            'reviewed_by' => $staffId,
            'review_date' => date('Y-m-d H:i:s')
        ];
        
        $auditParams = [
            ':user_id' => $staffId,
            ':record_id' => $loanId,
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
            'message' => 'Bank loan application rejected successfully'
        ]);
        
    } catch (Exception $e) {
        // Rollback on error
        $app->rollBack();
        
        // Make sure we send a proper JSON response
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Error rejecting bank loan: ' . $e->getMessage()
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