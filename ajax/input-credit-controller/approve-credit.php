<?php
include "../../config/config.php";
include "../../libs/App.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $app = new App;
        
        // Begin transaction
        $app->beginTransaction();

        // Get data from POST
        $creditApplicationId = isset($_POST['creditApplicationId']) ? intval($_POST['creditApplicationId']) : 0;
        $approvedAmount = isset($_POST['approvedAmount']) ? floatval($_POST['approvedAmount']) : 0;
        $creditPercentage = isset($_POST['creditPercentage']) ? floatval($_POST['creditPercentage']) : 0;
        $totalWithInterest = isset($_POST['totalWithInterest']) ? floatval($_POST['totalWithInterest']) : 0;
        $repaymentPercentage = isset($_POST['repaymentPercentage']) ? floatval($_POST['repaymentPercentage']) : 0;
        $fulfillmentDate = isset($_POST['fulfillmentDate']) ? $_POST['fulfillmentDate'] : date('Y-m-d');
        $approvalNotes = isset($_POST['approvalNotes']) ? $_POST['approvalNotes'] : '';
        
        // Validate inputs
        if ($creditApplicationId <= 0) {
            throw new Exception("Invalid credit application ID");
        }
        
        if ($approvedAmount <= 0) {
            throw new Exception("Approved amount must be greater than zero");
        }
        
        if ($creditPercentage <= 0) {
            throw new Exception("Credit percentage must be greater than zero");
        }
        
        if ($repaymentPercentage <= 0) {
            throw new Exception("Repayment percentage must be greater than zero");
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
                        CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                        a.name as agrovet_name,
                        fa.id as farmer_account_id
                      FROM input_credit_applications ica
                      JOIN farmers f ON ica.farmer_id = f.id
                      JOIN farmer_accounts fa ON f.id = fa.farmer_id
                      JOIN users u ON f.user_id = u.id
                      JOIN agrovets a ON ica.agrovet_id = a.id
                      WHERE ica.id = :credit_id
                      AND ica.status = 'under_review'
                      AND ica.agrovet_id = :agrovet_id";
                      
        $creditParams = [
            ':credit_id' => $creditApplicationId,
            ':agrovet_id' => $staff->agrovet_id
        ];
        
        $creditDetails = $app->selectOne($creditQuery, $creditParams);
        
        if (!$creditDetails) {
            throw new Exception("Input credit application not found or not in reviewable status");
        }
        
        // 1. Update the credit application status (combining approval and fulfillment)
        $updateCreditQuery = "UPDATE input_credit_applications SET 
                            status = 'fulfilled', 
                            reviewed_by = :reviewed_by,
                            review_date = NOW()
                            WHERE id = :credit_id";
                        
        $updateCreditParams = [
            ':credit_id' => $creditApplicationId,
            ':reviewed_by' => $staff->staff_id
        ];
        
        $app->updateToken($updateCreditQuery, $updateCreditParams);
        
        // 2. Insert into approved_input_credits (with active status since it's being fulfilled)
        $approvedCreditQuery = "INSERT INTO approved_input_credits (
                            credit_application_id,
                            approved_amount,
                            credit_percentage,
                            total_with_interest,
                            repayment_percentage,
                            remaining_balance,
                            fulfillment_date,
                            approved_by,
                            approval_date,
                            status,
                            created_at
                        ) VALUES (
                            :credit_application_id,
                            :approved_amount,
                            :credit_percentage,
                            :total_with_interest,
                            :repayment_percentage,
                            :remaining_balance,
                            :fulfillment_date,
                            :approved_by,
                            NOW(),
                            'active',
                            NOW()
                        )";
                            
        $approvedCreditParams = [
            ':credit_application_id' => $creditApplicationId,
            ':approved_amount' => $approvedAmount,
            ':credit_percentage' => $creditPercentage,
            ':total_with_interest' => $totalWithInterest,
            ':repayment_percentage' => $repaymentPercentage,
            ':remaining_balance' => $totalWithInterest, // Initially, remaining balance equals total with interest
            ':fulfillment_date' => $fulfillmentDate,
            ':approved_by' => $staff->staff_id
        ];
        
        $app->insertWithoutPath($approvedCreditQuery, $approvedCreditParams);
        $approvedCreditId = $app->lastInsertId();
        
        // 3. Record transaction in agrovet account (credit is extending value to farmer)
        // First, get the agrovet account ID
        $agrovetAccountQuery = "SELECT id FROM agrovet_accounts WHERE agrovet_id = :agrovet_id LIMIT 1";
        $agrovetAccount = $app->selectOne($agrovetAccountQuery, [':agrovet_id' => $staff->agrovet_id]);
        
        if (!$agrovetAccount) {
            throw new Exception("Agrovet account not found");
        }
        
        // Record agrovet account transaction
        $agrovetTransactionQuery = "INSERT INTO agrovet_account_transactions (
                                  agrovet_account_id,
                                  transaction_type,
                                  amount,
                                  reference_id,
                                  description,
                                  processed_by,
                                  created_at
                              ) VALUES (
                                  :agrovet_account_id,
                                  'debit',
                                  :amount,
                                  :reference_id,
                                  :description,
                                  :processed_by,
                                  NOW()
                              )";
                              
        $agrovetTransactionParams = [
            ':agrovet_account_id' => $agrovetAccount->id,
            ':amount' => $approvedAmount,
            ':reference_id' => $approvedCreditId,
            ':description' => "Input credit provided to " . $creditDetails->farmer_name . " (ICRED" . str_pad($creditApplicationId, 5, '0', STR_PAD_LEFT) . ")",
            ':processed_by' => $userId
        ];
        
        $app->insertWithoutPath($agrovetTransactionQuery, $agrovetTransactionParams);
        
        // 4. Record input credit transaction
        $inputCreditTransactionQuery = "INSERT INTO input_credit_transactions (
                                      input_credit_id,
                                      transaction_type,
                                      amount,
                                      reference_id,
                                      description,
                                      processed_by,
                                      created_at
                                  ) VALUES (
                                      :input_credit_id,
                                      'disbursement',
                                      :amount,
                                      :reference_id,
                                      :description,
                                      :processed_by,
                                      NOW()
                                  )";
                                  
        $inputCreditTransactionParams = [
            ':input_credit_id' => $approvedCreditId,
            ':amount' => $approvedAmount,
            ':reference_id' => $approvedCreditId,
            ':description' => "Input credit fulfillment",
            ':processed_by' => $userId
        ];
        
        $app->insertWithoutPath($inputCreditTransactionQuery, $inputCreditTransactionParams);
        
        // 5. Add comment for approval/fulfillment notes if provided
        if (!empty($approvalNotes)) {
            $commentQuery = "INSERT INTO comments (
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
                            
            $commentParams = [
                ':user_id' => $userId,
                ':comment_type_id' => 9, // input_credit_approval
                ':reference_id' => $creditApplicationId,
                ':comment' => $approvalNotes
            ];
            
            $app->insertWithoutPath($commentQuery, $commentParams);
        }
        
        // 6. Add logs for both approval and fulfillment
        // Approval log
        $approvalLogQuery = "INSERT INTO input_credit_logs (
                            input_credit_application_id,
                            user_id,
                            action_type,
                            description,
                            created_at
                        ) VALUES (
                            :input_credit_application_id,
                            :user_id,
                            'approved',
                            :description,
                            NOW()
                        )";
                        
        $approvalLogParams = [
            ':input_credit_application_id' => $creditApplicationId,
            ':user_id' => $userId,
            ':description' => "Input credit application approved. Amount: KES " . number_format($approvedAmount, 2) . 
                             ", Credit percentage: " . $creditPercentage . "%, Total with interest: KES " . 
                             number_format($totalWithInterest, 2) . ", Repayment percentage: " . $repaymentPercentage . "%"
        ];
        
        $app->insertWithoutPath($approvalLogQuery, $approvalLogParams);
        
        // Fulfillment log
        $fulfillmentLogQuery = "INSERT INTO input_credit_logs (
                               input_credit_application_id,
                               user_id,
                               action_type,
                               description,
                               created_at
                           ) VALUES (
                               :input_credit_application_id,
                               :user_id,
                               'fulfilled',
                               :description,
                               NOW()
                           )";
                           
        $fulfillmentLogParams = [
            ':input_credit_application_id' => $creditApplicationId,
            ':user_id' => $userId,
            ':description' => "Input credit fulfillment completed. Inputs provided to farmer on " . $fulfillmentDate
        ];
        
        $app->insertWithoutPath($fulfillmentLogQuery, $fulfillmentLogParams);
        
        // 7. Add activity log
        $activityQuery = "INSERT INTO activity_logs (
                            user_id,
                            activity_type,
                            description,
                            created_at
                        ) VALUES (
                            :user_id,
                            'input_credit_approved',
                            :description,
                            NOW()
                        )";
                        
        $activityParams = [
            ':user_id' => $userId,
            ':description' => "Input credit application (ID: " . $creditApplicationId . ") from " . 
                             $creditDetails->farmer_name . " was approved and fulfilled. Amount: KES " . 
                             number_format($approvedAmount, 2) . ", Interest: " . $creditPercentage . "%"
        ];
        
        $app->insertWithoutPath($activityQuery, $activityParams);
        
        // 8. Add audit log
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
            'status' => 'under_review'
        ];
        
        $newValues = [
            'status' => 'fulfilled',
            'reviewed_by' => $staff->staff_id,
            'review_date' => date('Y-m-d H:i:s')
        ];
        
        $auditParams = [
            ':user_id' => $userId,
            ':record_id' => $creditApplicationId,
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
            'message' => 'Input credit application approved and fulfilled successfully',
            'credit_id' => $approvedCreditId
        ]);
        
    } catch (Exception $e) {
        // Rollback on error
        $app->rollBack();
        
        // Return error response
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Error approving input credit: ' . $e->getMessage()
        ]);
    }
} else {
    // Return error for invalid request method
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}