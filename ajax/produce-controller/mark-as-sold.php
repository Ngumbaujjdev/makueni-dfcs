<?php
include "../../config/config.php";
include "../../libs/App.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $app = new App;
        
        // Begin transaction
        $app->beginTransaction();

        // Get data from POST
        $produceId = isset($_POST['produceId']) ? intval($_POST['produceId']) : 0;
        $buyerName = isset($_POST['buyerName']) ? $_POST['buyerName'] : '';
        $saleNotes = isset($_POST['saleNotes']) ? $_POST['saleNotes'] : '';
        
        // Validate inputs
        if ($produceId <= 0) {
            throw new Exception("Invalid produce ID");
        }
        
        if (empty($buyerName)) {
            throw new Exception("Buyer name cannot be empty");
        }
        
        // Get SACCO staff ID from session
        $staffId = $_SESSION['user_id'];
        
       
       // First, get the produce details for logging purposes
            $produceQuery = "SELECT 
                                pd.farm_product_id, 
                                pd.quantity,
                                pd.unit_price,
                                pd.total_value,
                                pd.notes,
                                CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                                f.farmer_id,
                                fp.farm_id,
                                fa.id as farmer_account_id
                            FROM produce_deliveries pd
                            JOIN farm_products fp ON pd.farm_product_id = fp.id
                            JOIN farms f ON fp.farm_id = f.id
                            JOIN farmers fm ON f.farmer_id = fm.id
                            JOIN farmer_accounts fa ON fm.id = fa.farmer_id
                            JOIN users u ON fm.user_id = u.id
                            WHERE pd.id = :produce_id
                            AND pd.status = 'verified'
                            AND pd.is_sold = 0";
                            
            $produceParams = [
                ':produce_id' => $produceId
            ];
            
            $produceDetails = $app->selectOne($produceQuery, $produceParams);
        
        if (!$produceDetails) {
            throw new Exception("Verified produce delivery not found or already sold");
        }
        
        // Use the total_value directly from the produce_deliveries record
        $saleValue = $produceDetails->total_value;
        
        // Calculate commission based on the existing total value
        $commission = $saleValue * 0.10; // 10% commission for SACCO
        $farmerPayment = $saleValue - $commission;
        
        // Update the produce to mark as sold but keep status as verified
        // Set is_sold to 1 and record the sale date
        $updateQuery = "UPDATE produce_deliveries SET 
                        is_sold = 1,
                        sale_date = NOW(),
                        notes = CONCAT(IFNULL(notes, ''), :sale_info)
                        WHERE id = :produce_id";
                    
        $updateParams = [
            ':produce_id' => $produceId,
            ':sale_info' => " Sold to: " . $buyerName . ". Sale notes: " . $saleNotes
        ];
        
        $app->updateToken($updateQuery, $updateParams);
        
        // Get SACCO account
        $saccoQuery = "SELECT id FROM sacco_accounts WHERE account_type = 'Current' LIMIT 1";
        $saccoAccount = $app->select_one($saccoQuery);
        
        if (!$saccoAccount) {
            throw new Exception("SACCO account not found");
        }
        
        // Get the bank branch account (for transferring farmer payment)
        $bankAccountQuery = "SELECT id FROM bank_branch_accounts WHERE account_type = 'Current' LIMIT 1";
        $bankAccount = $app->select_one($bankAccountQuery);
        
        if (!$bankAccount) {
            throw new Exception("Bank branch account not found");
        }
        
        // 1. Credit SACCO account with the commission
        $updateSaccoQuery = "UPDATE sacco_accounts SET 
                            balance = balance + :commission
                            WHERE id = :sacco_account_id";
                            
        $updateSaccoParams = [
            ':commission' => $commission,
            ':sacco_account_id' => $saccoAccount->id
        ];
        
        $app->updateToken($updateSaccoQuery, $updateSaccoParams);
        
        // 2. Record SACCO account transaction
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
                                    'credit',
                                    :amount,
                                    :reference_id,
                                    :description,
                                    :processed_by,
                                    NOW()
                                )";
                                
        $saccoTransactionParams = [
            ':sacco_account_id' => $saccoAccount->id,
            ':amount' => $commission,
            ':reference_id' => $produceId,
            ':description' => "Commission from produce sale ID: $produceId",
            ':processed_by' => $staffId
        ];
        
        $app->insertWithoutPath($saccoTransactionQuery, $saccoTransactionParams);
        
        // 3. Credit bank account with the farmer payment (for future transfer to farmer)
        $updateBankQuery = "UPDATE bank_branch_accounts SET 
                           balance = balance + :farmer_payment
                           WHERE id = :bank_account_id";
                           
        $updateBankParams = [
            ':farmer_payment' => $farmerPayment,
            ':bank_account_id' => $bankAccount->id
        ];
        
        $app->updateToken($updateBankQuery, $updateBankParams);
        
        // 4. Record bank account transaction
        $bankTransactionQuery = "INSERT INTO bank_account_transactions (
                                 bank_account_id,
                                 transaction_type,
                                 amount,
                                 reference_id,
                                 description,
                                 processed_by,
                                 created_at
                             ) VALUES (
                                 :bank_account_id,
                                 'credit',
                                 :amount,
                                 :reference_id,
                                 :description,
                                 :processed_by,
                                 NOW()
                             )";
                             
        $bankTransactionParams = [
            ':bank_account_id' => $bankAccount->id,
            ':amount' => $farmerPayment,
            ':reference_id' => $produceId,
            ':description' => "Farmer payment for produce sale ID: $produceId. To be transferred to farmer: " . $produceDetails->farmer_name,
            ':processed_by' => $staffId
        ];
        
        $app->insertWithoutPath($bankTransactionQuery, $bankTransactionParams);
        $transferId = $app->lastInsertId(); // Use the bank transaction ID instead
        
        // Add comment about the sale
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
                            'produce_delivery',
                            :reference_id,
                            :comment,
                            0,
                            NOW()
                        )";
                        
        $commentParams = [
            ':user_id' => $staffId,
            ':comment_type_id' => 7, // general comment type
            ':reference_id' => $produceId,
            ':comment' => "Produce sold to $buyerName. Total value: KES " . 
                         number_format($saleValue, 2) . ". Commission: KES " . number_format($commission, 2) . 
                         ". Farmer payment: KES " . number_format($farmerPayment, 2) . 
                         ($saleNotes ? ". Notes: $saleNotes" : "")
        ];
        
        $app->insertWithoutPath($commentQuery, $commentParams);
        
        // Add produce log
        $produceLogQuery = "INSERT INTO produce_logs (
                                produce_delivery_id,
                                user_id,
                                action_type,
                                description,
                                created_at
                            ) VALUES (
                                :produce_delivery_id,
                                :user_id,
                                'sold',
                                :description,
                                NOW()
                            )";
                            
        $produceLogParams = [
            ':produce_delivery_id' => $produceId,
            ':user_id' => $staffId,
            ':description' => "Produce sold to " . $buyerName . ". Sale value: KES " . number_format($saleValue, 2) . 
                             ". Farmer payment: KES " . number_format($farmerPayment, 2) . " (pending transfer)"
        ];
        
        $app->insertWithoutPath($produceLogQuery, $produceLogParams);
        
        // Add activity log
        $activityQuery = "INSERT INTO activity_logs (
                            user_id,
                            activity_type,
                            description,
                            created_at
                        ) VALUES (
                            :user_id,
                            'produce_sold',
                            :description,
                            NOW()
                        )";
                        
        $activityParams = [
            ':user_id' => $staffId,
            ':description' => "Produce delivery (ID: " . $produceId . ") from " . 
                             $produceDetails->farmer_name . " was sold to " . $buyerName . 
                             ". Quantity: " . number_format($produceDetails->quantity, 2) . 
                             " KGs, Sale value: KES " . number_format($saleValue, 2)
        ];
        
        $app->insertWithoutPath($activityQuery, $activityParams);
        
        // Add audit log
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
                        'produce_deliveries',
                        :record_id,
                        :old_values,
                        :new_values,
                        NOW()
                    )";
                    
        $oldValues = [
            'status' => 'verified',
            'is_sold' => 0,
            'sale_date' => null,
            'notes' => isset($produceDetails->notes) ? $produceDetails->notes : null
        ];

        $newValues = [
            'status' => 'verified',
            'is_sold' => 1,
            'sale_date' => date('Y-m-d H:i:s'),
            'notes' => (isset($produceDetails->notes) && $produceDetails->notes ? $produceDetails->notes . ' ' : '') . 
                    "Sold to: $buyerName. Sale notes: $saleNotes"
        ];

        $auditParams = [
            ':user_id' => $staffId,
            ':record_id' => $produceId,
            ':old_values' => json_encode($oldValues),
            ':new_values' => json_encode($newValues)
        ];

        $app->insertWithoutPath($auditQuery, $auditParams);
        
        // Commit transaction
        $app->commit();
        
        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Produce marked as sold successfully',
            'transfer_id' => $transferId
        ]);
        
    } catch (Exception $e) {
        // Rollback on error
        $app->rollBack();
        
        echo json_encode([
            'success' => false,
            'message' => 'Error marking produce as sold: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}