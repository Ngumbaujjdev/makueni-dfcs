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
        $farmerId = isset($_POST['farmerId']) ? intval($_POST['farmerId']) : 0;
        
        // Validate inputs
        if ($produceId <= 0) {
            throw new Exception("Invalid produce ID");
        }
        
        if ($farmerId <= 0) {
            throw new Exception("Invalid farmer ID");
        }
        
        // Get the current user from session
        $staffId = $_SESSION['user_id'];
        
        // Get produce details
        $produceQuery = "SELECT 
                            pd.id,
                            pd.farm_product_id, 
                            pd.quantity,
                            pd.unit_price,
                            pd.total_value,
                            pd.quality_grade,
                            pd.delivery_date,
                            CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                            f.farmer_id,
                            fp.farm_id,
                            fa.id as farmer_account_id,
                            fa.balance as current_account_balance,
                            f.name as farm_name,
                            pt.name as product_name
                        FROM produce_deliveries pd
                        JOIN farm_products fp ON pd.farm_product_id = fp.id
                        JOIN product_types pt ON fp.product_type_id = pt.id
                        JOIN farms f ON fp.farm_id = f.id
                        JOIN farmers fm ON f.farmer_id = fm.id
                        JOIN farmer_accounts fa ON fm.id = fa.farmer_id
                        JOIN users u ON fm.user_id = u.id
                        WHERE pd.id = :produce_id
                        AND fm.id = :farmer_id
                        AND pd.status = 'verified'
                        AND pd.is_sold = 1";
                        
        $produceParams = [
            ':produce_id' => $produceId,
            ':farmer_id' => $farmerId
        ];
        
        $produceDetails = $app->selectOne($produceQuery, $produceParams);
    
        if (!$produceDetails) {
            throw new Exception("Produce sale not found or does not belong to this farmer");
        }
        
        // Check if payment was already processed
        $checkPaymentQuery = "SELECT id FROM farmer_account_transactions 
                             WHERE reference_id = :produce_id 
                             AND transaction_type = 'credit'";
                             
        $checkPaymentParams = [':produce_id' => $produceId];
        $existingPayment = $app->selectOne($checkPaymentQuery, $checkPaymentParams);
        
        if ($existingPayment) {
            throw new Exception("Payment for this produce has already been processed");
        }
        
        // Calculate farmer payment (total value minus 10% commission)
        $saleValue = $produceDetails->total_value;
        $commission = $saleValue * 0.10; // 10% commission for SACCO
        $farmerPaymentAmount = $saleValue - $commission;
        
        // Get active loans for the farmer
        $loansQuery = "SELECT 
                        al.id,
                        al.approved_amount,
                        al.interest_rate,
                        al.total_repayment_amount,
                        al.remaining_balance,
                        al.expected_completion_date,
                        al.disbursement_date,
                        al.status,
                        al.approved_term,
                        lt.name as loan_type,
                        CONCAT('LOAN', LPAD(la.id, 5, '0')) as reference
                      FROM approved_loans al
                      JOIN loan_applications la ON al.loan_application_id = la.id
                      JOIN loan_types lt ON la.loan_type_id = lt.id
                      WHERE la.farmer_id = '{$farmerId}'
                      AND al.status = 'active'
                      ORDER BY al.disbursement_date ASC";
                      
        $activeLoans = $app->select_all($loansQuery);
        
        // Calculate total outstanding debt
        $totalOutstandingDebt = 0;
        if ($activeLoans && count($activeLoans) > 0) {
            foreach ($activeLoans as $loan) {
                $totalOutstandingDebt += floatval($loan->remaining_balance);
            }
        }
        
        // Calculate maximum loan repayment (70% of farmer payment)
        $maxRepaymentAmount = $farmerPaymentAmount * 0.7;
        
        // Calculate estimated repayments for each loan
        $estimatedRepayments = [];
        if ($activeLoans && count($activeLoans) > 0) {
            $remainingForRepayment = $maxRepaymentAmount;
            
            foreach ($activeLoans as $loan) {
                $loanRepayment = new stdClass();
                $loanRepayment->loan_id = $loan->id;
                $loanRepayment->reference = $loan->reference;
                $loanRepayment->original_balance = floatval($loan->remaining_balance);
                
                if ($remainingForRepayment <= 0) {
                    // No more funds available for repayment
                    $loanRepayment->repayment_amount = 0;
                    $loanRepayment->new_balance = $loanRepayment->original_balance;
                    $loanRepayment->would_complete = false;
                } else if ($remainingForRepayment >= $loan->remaining_balance) {
                    // Can repay this loan in full
                    $loanRepayment->repayment_amount = floatval($loan->remaining_balance);
                    $loanRepayment->new_balance = 0;
                    $loanRepayment->would_complete = true;
                    $remainingForRepayment -= $loanRepayment->repayment_amount;
                } else {
                    // Partial repayment
                    $loanRepayment->repayment_amount = $remainingForRepayment;
                    $loanRepayment->new_balance = floatval($loan->remaining_balance) - $remainingForRepayment;
                    $loanRepayment->would_complete = false;
                    $remainingForRepayment = 0;
                }
                
                $estimatedRepayments[] = $loanRepayment;
            }
        }
        
        // Calculate total estimated repayment
        $totalEstimatedRepayment = 0;
        foreach ($estimatedRepayments as $repayment) {
            $totalEstimatedRepayment += $repayment->repayment_amount;
        }
        
        // Calculate the final payment to farmer after loan repayments
        $estimatedFinalPayment = $farmerPaymentAmount - $totalEstimatedRepayment;
        
        // Add activity log entry
        $activityQuery = "INSERT INTO activity_logs (
                            user_id,
                            activity_type,
                            description,
                            created_at
                        ) VALUES (
                            :user_id,
                            'payment_details_viewed',
                            :description,
                            NOW()
                        )";
                        
        $activityParams = [
            ':user_id' => $staffId,
            ':description' => "Payment details viewed for produce ID: " . $produceId . 
                             " from farmer: " . $produceDetails->farmer_name
        ];
        
        $app->insertWithoutPath($activityQuery, $activityParams);
        
        // Commit transaction
        $app->commit();
        
        // Return success response with all details
        echo json_encode([
            'success' => true,
            'farmerName' => $produceDetails->farmer_name,
            'productName' => $produceDetails->product_name,
            'totalValue' => floatval($produceDetails->total_value),
            'farmerAccountId' => $produceDetails->farmer_account_id,
            'loans' => $activeLoans ? $activeLoans : []
        ]);
        
    } catch (Exception $e) {
        // Rollback on error
        $app->rollBack();
        
        echo json_encode([
            'success' => false,
            'message' => 'Error fetching payment details: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}