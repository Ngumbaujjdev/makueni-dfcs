<?php
include "../../config/config.php";
include "../../libs/App.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $app = new App;
        
        // Begin transaction
        $app->beginTransaction();

        // Get data from POST
        $loanApplicationId = isset($_POST['loanApplicationId']) ? intval($_POST['loanApplicationId']) : 0;
        $approvedAmount = isset($_POST['approvedAmount']) ? floatval($_POST['approvedAmount']) : 0;
        $approvedTerm = isset($_POST['approvedTerm']) ? intval($_POST['approvedTerm']) : 0;
        $interestRate = isset($_POST['interestRate']) ? floatval($_POST['interestRate']) : 0;
        $processingFee = isset($_POST['processingFee']) ? floatval($_POST['processingFee']) : 0;
        $totalRepaymentAmount = isset($_POST['totalRepaymentAmount']) ? floatval($_POST['totalRepaymentAmount']) : 0;
        $repaymentPercentage = isset($_POST['repaymentPercentage']) ? floatval($_POST['repaymentPercentage']) : 0;
        $expectedCompletionDate = isset($_POST['expectedCompletionDate']) ? $_POST['expectedCompletionDate'] : null;
        $approvalNotes = isset($_POST['approvalNotes']) ? $_POST['approvalNotes'] : '';
        
        // Get today's date for the disbursement
        $disbursementDate = date('Y-m-d');
        
        // Validate inputs
        if ($loanApplicationId <= 0) {
            throw new Exception("Invalid loan application ID");
        }
        
        if ($approvedAmount <= 0) {
            throw new Exception("Approved amount must be greater than zero");
        }
        
        if ($approvedTerm <= 0) {
            throw new Exception("Approved term must be greater than zero");
        }
        
        // Get SACCO staff ID from session
        $staffId = $_SESSION['user_id'];
        
        // First, get the loan application details for logging purposes
        $loanQuery = "SELECT 
                        la.farmer_id,
                        la.provider_type,
                        la.loan_type_id,
                        la.amount_requested,
                        CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                        fa.id as farmer_account_id
                      FROM loan_applications la
                      JOIN farmers f ON la.farmer_id = f.id
                      JOIN farmer_accounts fa ON f.id = fa.farmer_id
                      JOIN users u ON f.user_id = u.id
                      WHERE la.id = :loan_application_id
                      AND la.status = 'under_review'";
                      
        $loanParams = [
            ':loan_application_id' => $loanApplicationId
        ];
        
        $loanDetails = $app->selectOne($loanQuery, $loanParams);
        
        if (!$loanDetails) {
            throw new Exception("Loan application not found or not in reviewable status");
        }
        
        // Get SACCO account
        $saccoQuery = "SELECT id, balance FROM sacco_accounts WHERE account_type = 'Current' LIMIT 1";
        $saccoAccount = $app->select_one($saccoQuery);
        
        if (!$saccoAccount) {
            throw new Exception("SACCO account not found");
        }
        
        // Check if SACCO has sufficient funds
        if ($saccoAccount->balance < $approvedAmount) {
            throw new Exception("Insufficient funds in SACCO account for disbursement");
        }
        
        // 1. Update the loan application status
        $updateLoanQuery = "UPDATE loan_applications SET 
                            status = 'approved',
                            reviewed_by = :reviewed_by,
                            review_date = NOW()
                            WHERE id = :loan_application_id";
                        
        $updateLoanParams = [
            ':loan_application_id' => $loanApplicationId,
            ':reviewed_by' => $staffId
        ];
        
        $app->updateToken($updateLoanQuery, $updateLoanParams);
        
       // 2. Insert into approved_loans
       $approvedLoanQuery = "INSERT INTO approved_loans (
                        loan_application_id,
                        approved_amount,
                        approved_term,
                        interest_rate,
                        processing_fee,
                        total_repayment_amount,
                        remaining_balance,
                        disbursement_date,
                        expected_completion_date,
                        approved_by,
                        approval_date,
                        status,
                        created_at
                    ) VALUES (
                        :loan_application_id,
                        :approved_amount,
                        :approved_term,
                        :interest_rate,
                        :processing_fee,
                        :total_repayment_amount,
                        :remaining_balance,
                        :disbursement_date,
                        :expected_completion_date,
                        :approved_by,
                        NOW(),
                        'active',
                        NOW()
                    )";
                            
        $approvedLoanParams = [
            ':loan_application_id' => $loanApplicationId,
            ':approved_amount' => $approvedAmount,
            ':approved_term' => $approvedTerm,
            ':interest_rate' => $interestRate,
            ':processing_fee' => $processingFee,
            ':total_repayment_amount' => $totalRepaymentAmount,
            ':remaining_balance' => $totalRepaymentAmount,
            ':disbursement_date' => $disbursementDate,
            ':expected_completion_date' => $expectedCompletionDate,
            ':approved_by' => $staffId
        ];
        
        $app->insertWithoutPath($approvedLoanQuery, $approvedLoanParams);
        $approvedLoanId = $app->lastInsertId();
        
        // 3. Debit SACCO account
        $updateSaccoQuery = "UPDATE sacco_accounts SET 
                            balance = balance - :loan_amount
                            WHERE id = :sacco_account_id";
                            
        $updateSaccoParams = [
            ':loan_amount' => $approvedAmount,
            ':sacco_account_id' => $saccoAccount->id
        ];
        
        $app->updateToken($updateSaccoQuery, $updateSaccoParams);
        
        // 4. Record SACCO account transaction
        $saccoTransactionQuery = "INSERT INTO sacco_account_transactions (
                                    sacco_account_id,
                                    transaction_type,
                                    amount,
                                    reference_id,
                                    description,
                                    processed_by,
                                    created_at
                                ) VALUES (
                                    :sacco_account_id,
                                    'debit',
                                    :amount,
                                    :reference_id,
                                    :description,
                                    :processed_by,
                                    NOW()
                                )";
                                
        $saccoTransactionParams = [
            ':sacco_account_id' => $saccoAccount->id,
            ':amount' => $approvedAmount,
            ':reference_id' => $approvedLoanId,
            ':description' => "Loan disbursement to " . $loanDetails->farmer_name . " (LOAN" . str_pad($loanApplicationId, 5, '0', STR_PAD_LEFT) . ")",
            ':processed_by' => $staffId
        ];
        
        $app->insertWithoutPath($saccoTransactionQuery, $saccoTransactionParams);
        
        // 5. Credit farmer account (minus processing fee)
        $netDisbursement = $approvedAmount - $processingFee;
        $updateFarmerQuery = "UPDATE farmer_accounts SET 
                             balance = balance + :net_amount
                             WHERE id = :farmer_account_id";
                             
        $updateFarmerParams = [
            ':net_amount' => $netDisbursement,
            ':farmer_account_id' => $loanDetails->farmer_account_id
        ];
        
        $app->updateToken($updateFarmerQuery, $updateFarmerParams);
        
        // 6. Record farmer account transaction
        $farmerTransactionQuery = "INSERT INTO farmer_account_transactions (
                                  farmer_account_id,
                                  transaction_type,
                                  amount,
                                  reference_id,
                                  description,
                                  processed_by,
                                  created_at
                              ) VALUES (
                                  :farmer_account_id,
                                  'credit',
                                  :amount,
                                  :reference_id,
                                  :description,
                                  :processed_by,
                                  NOW()
                              )";
                              
        $farmerTransactionParams = [
            ':farmer_account_id' => $loanDetails->farmer_account_id,
            ':amount' => $netDisbursement,
            ':reference_id' => $approvedLoanId,
            ':description' => "Loan disbursement (LOAN" . str_pad($loanApplicationId, 5, '0', STR_PAD_LEFT) . ")",
            ':processed_by' => $staffId
        ];
        
        $app->insertWithoutPath($farmerTransactionQuery, $farmerTransactionParams);
        
        // 7. Add comment for approval notes if provided
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
                                'loan_application',
                                :reference_id,
                                :comment,
                                NOW()
                            )";
                            
            $commentParams = [
                ':user_id' => $staffId,
                ':comment_type_id' => 2, // loan_approval type
                ':reference_id' => $loanApplicationId,
                ':comment' => $approvalNotes
            ];
            
            $app->insertWithoutPath($commentQuery, $commentParams);
        }
        
        // 8. Add loan log
        $loanLogQuery = "INSERT INTO loan_logs (
                            loan_application_id,
                            user_id,
                            action_type,
                            description,
                            created_at
                        ) VALUES (
                            :loan_application_id,
                            :user_id,
                            'approved',
                            :description,
                            NOW()
                        )";
                        
        $loanLogParams = [
            ':loan_application_id' => $loanApplicationId,
            ':user_id' => $staffId,
            ':description' => "Loan approved. Amount: KES " . number_format($approvedAmount, 2) . 
                             ", Term: " . $approvedTerm . " months, Interest: " . $interestRate . "%. " . 
                             "Processing fee: KES " . number_format($processingFee, 2) . ". " . 
                             "Total repayment: KES " . number_format($totalRepaymentAmount, 2)
        ];
        
        $app->insertWithoutPath($loanLogQuery, $loanLogParams);
        
        // 9. Add activity log
        $activityQuery = "INSERT INTO activity_logs (
                            user_id,
                            activity_type,
                            description,
                            created_at
                        ) VALUES (
                            :user_id,
                            'loan_approved',
                            :description,
                            NOW()
                        )";
                        
        $activityParams = [
            ':user_id' => $staffId,
            ':description' => "Loan application (ID: " . $loanApplicationId . ") from " . 
                             $loanDetails->farmer_name . " was approved. Amount: KES " . 
                             number_format($approvedAmount, 2) . ", Term: " . $approvedTerm . " months"
        ];
        
        $app->insertWithoutPath($activityQuery, $activityParams);
        
        // 10. Add audit log
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
            'status' => 'under_review'
        ];
        
        $newValues = [
            'status' => 'approved',
            'reviewed_by' => $staffId,
            'review_date' => date('Y-m-d H:i:s')
        ];
        
        $auditParams = [
            ':user_id' => $staffId,
            ':record_id' => $loanApplicationId,
            ':old_values' => json_encode($oldValues),
            ':new_values' => json_encode($newValues)
        ];
        
        $app->insertWithoutPath($auditQuery, $auditParams);
        
        // Commit transaction
        $app->commit();
        
        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Loan approved and disbursed successfully',
            'loan_id' => $approvedLoanId
        ]);
        
    } catch (Exception $e) {
        // Rollback on error
        $app->rollBack();
        
        echo json_encode([
            'success' => false,
            'message' => 'Error approving loan: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}