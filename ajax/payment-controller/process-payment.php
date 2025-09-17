<?php
include "../../config/config.php";
include "../../libs/App.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $app = new App;
        
        // Begin transaction - all operations will be rolled back if any fails
        $app->beginTransaction();
        
        // ------------------------------------------------------------------
        // 1. Process and validate request parameters
        // ------------------------------------------------------------------
        
        // Get data from POST
        $produceId = isset($_POST['produceId']) ? intval($_POST['produceId']) : 0;
        $farmerId = isset($_POST['farmerId']) ? intval($_POST['farmerId']) : 0;
        $paymentNotes = isset($_POST['paymentNotes']) ? $_POST['paymentNotes'] : '';
        
        // Validate required parameters
        if ($produceId <= 0) {
            throw new Exception("Invalid produce ID");
        }
        
        if ($farmerId <= 0) {
            throw new Exception("Invalid farmer ID");
        }
        
        // Get the current user (SACCO/bank staff) from session
        $staffId = $_SESSION['user_id'];
        
        // ------------------------------------------------------------------
        // 2. Retrieve produce sale details
        // ------------------------------------------------------------------
        
        $produceQuery = "SELECT 
                            pd.id,
                            pd.farm_product_id, 
                            pd.quantity,
                            pd.unit_price,
                            pd.total_value,
                            pd.quality_grade,
                            CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                            fa.id as farmer_account_id,
                            fa.balance as current_account_balance,
                            fm.id as farmer_id,
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
        
        // ------------------------------------------------------------------
        // 3. Check if payment was already processed
        // ------------------------------------------------------------------
        
        $checkPaymentQuery = "SELECT id FROM farmer_account_transactions 
                             WHERE reference_id = :produce_id 
                             AND transaction_type = 'credit'";
                             
        $checkPaymentParams = [':produce_id' => $produceId];
        $existingPayment = $app->selectOne($checkPaymentQuery, $checkPaymentParams);
        
        if ($existingPayment) {
            throw new Exception("Payment for this produce has already been processed");
        }
        
        // Initialize variables for tracking payment components
        $saleValue = $produceDetails->total_value;
        $commission = 0;
        $availableForDistribution = 0;
        $totalInputCreditRepaymentAmount = 0;
        $totalSaccoLoanRepaymentAmount = 0;
        $totalBankLoanRepaymentAmount = 0;
        $finalFarmerPaymentAmount = 0;
        $inputCreditRepaymentDetails = [];
        $saccoLoanRepaymentDetails = [];
        $bankLoanRepaymentDetails = [];
        
        // ------------------------------------------------------------------
        // 4. NEW FAIR DISTRIBUTION CALCULATION
        // ------------------------------------------------------------------
        
        // Calculate SACCO commission (10% of total sale value)
        $commission = $saleValue * 0.10;
        
        // Calculate available amount for distribution (90% of total)
        $availableForDistribution = $saleValue - $commission;
        
        // Calculate fixed allocation for each category (25% each)
        $inputCreditAllocation = $availableForDistribution * 0.25;
        $saccoLoanAllocation = $availableForDistribution * 0.25;
        $bankLoanAllocation = $availableForDistribution * 0.25;
        $farmerAllocation = $availableForDistribution * 0.25;
        
        // ------------------------------------------------------------------
        // 5. Get account details
        // ------------------------------------------------------------------
        
        // Get SACCO account details
        $saccoQuery = "SELECT id, balance FROM sacco_accounts WHERE account_type = 'Current' LIMIT 1";
        $saccoAccount = $app->select_one($saccoQuery);
        
        if (!$saccoAccount) {
            throw new Exception("SACCO account not found");
        }
        
        // Get bank account details
        $bankQuery = "SELECT id, balance FROM bank_branch_accounts WHERE account_type = 'Current' LIMIT 1";
        $bankAccount = $app->select_one($bankQuery);
        
        if (!$bankAccount) {
            throw new Exception("Bank account not found");
        }
        
        // ------------------------------------------------------------------
        // 6. Process SACCO commission payment
        // ------------------------------------------------------------------
        
        // Credit SACCO account with commission
        $newSaccoBalance = floatval($saccoAccount->balance) + $commission;
        
        $updateSaccoQuery = "UPDATE sacco_accounts SET
                            balance = :balance
                            WHERE id = :account_id";
                            
        $updateSaccoParams = [
            ':balance' => $newSaccoBalance,
            ':account_id' => $saccoAccount->id
        ];
        
        $app->updateToken($updateSaccoQuery, $updateSaccoParams);
        
        // Create SACCO account transaction record
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
            ':description' => "Commission from produce sale DLVR" . str_pad($produceId, 5, '0', STR_PAD_LEFT),
            ':processed_by' => $staffId
        ];
        
        $app->insertWithoutPath($saccoTransactionQuery, $saccoTransactionParams);
        
        // ------------------------------------------------------------------
        // 7. Process INPUT CREDIT repayments (25% allocation)
        // ------------------------------------------------------------------
        
        // Retrieve active input credits for the farmer
        $inputCreditsQuery = "SELECT 
                                aic.id as approved_credit_id,
                                aic.credit_application_id,
                                aic.approved_amount,
                                aic.remaining_balance,
                                aic.status,
                                ica.agrovet_id,
                                CONCAT('INPCR', LPAD(ica.id, 5, '0')) as reference,
                                a.name as agrovet_name,
                                aa.id as agrovet_account_id,
                                aa.balance as agrovet_balance
                              FROM approved_input_credits aic
                              JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                              JOIN agrovets a ON ica.agrovet_id = a.id
                              JOIN agrovet_accounts aa ON a.id = aa.agrovet_id
                              WHERE ica.farmer_id = '{$farmerId}'
                              AND aic.status = 'active'
                              ORDER BY aic.approval_date ASC";
        
        $activeInputCredits = $app->select_all($inputCreditsQuery);
        
        if ($activeInputCredits && count($activeInputCredits) > 0) {
            // Calculate total outstanding input credit amount
            $totalOutstandingInputCredit = 0;
            foreach ($activeInputCredits as $credit) {
                $totalOutstandingInputCredit += floatval($credit->remaining_balance);
            }
            
            // Distribute the 25% allocation proportionally among active credits
            foreach ($activeInputCredits as $credit) {
                if ($totalOutstandingInputCredit > 0) {
                    // Calculate this credit's proportion of total outstanding
                    $creditProportion = floatval($credit->remaining_balance) / $totalOutstandingInputCredit;
                    
                    // Calculate repayment amount for this credit
                    $repaymentAmount = $inputCreditAllocation * $creditProportion;
                    
                    // Ensure we don't exceed the remaining balance
                    $repaymentAmount = min($repaymentAmount, floatval($credit->remaining_balance));
                    
                    // Round to 2 decimal places
                    $repaymentAmount = round($repaymentAmount, 2);
                    
                    // Skip if repayment amount is zero
                    if ($repaymentAmount <= 0) {
                        continue;
                    }
                    
                    // Update total repayment amount
                    $totalInputCreditRepaymentAmount += $repaymentAmount;
                    
                    // Calculate new remaining balance
                    $newRemainingBalance = floatval($credit->remaining_balance) - $repaymentAmount;
                    $newStatus = ($newRemainingBalance <= 0) ? 'completed' : 'active';
                    
                    // Create input credit repayment record
                    $repaymentQuery = "INSERT INTO input_credit_repayments (
                                        approved_credit_id,
                                        produce_delivery_id,
                                        produce_sale_amount,
                                        deducted_amount,
                                        amount,
                                        deduction_date,
                                        notes,
                                        created_at
                                    ) VALUES (
                                        :approved_credit_id,
                                        :produce_delivery_id,
                                        :produce_sale_amount,
                                        :deducted_amount,
                                        :amount,
                                        NOW(),
                                        :notes,
                                        NOW()
                                    )";
                                    
                    $repaymentParams = [
                        ':approved_credit_id' => $credit->approved_credit_id,
                        ':produce_delivery_id' => $produceId,
                        ':produce_sale_amount' => $saleValue,
                        ':deducted_amount' => $repaymentAmount,
                        ':amount' => $repaymentAmount,
                        ':notes' => "Fair distribution deduction (25%) from produce sale DLVR" . str_pad($produceId, 5, '0', STR_PAD_LEFT)
                    ];
                    
                    $app->insertWithoutPath($repaymentQuery, $repaymentParams);
                    $repaymentId = $app->lastInsertId();
                    
                    // Update input credit remaining balance and status
                    $updateCreditQuery = "UPDATE approved_input_credits SET
                                        remaining_balance = :remaining_balance,
                                        status = :status,
                                        updated_at = NOW()
                                     WHERE id = :credit_id";
                                     
                    $updateCreditParams = [
                        ':remaining_balance' => $newRemainingBalance,
                        ':status' => $newStatus,
                        ':credit_id' => $credit->approved_credit_id
                    ];
                    
                    $app->updateToken($updateCreditQuery, $updateCreditParams);
                    
                    // Credit agrovet account with repayment amount
                    $newAgrovetBalance = floatval($credit->agrovet_balance) + $repaymentAmount;
                    
                    $updateAgrovetQuery = "UPDATE agrovet_accounts SET
                                          balance = :balance
                                          WHERE id = :account_id";
                                          
                    $updateAgrovetParams = [
                        ':balance' => $newAgrovetBalance,
                        ':account_id' => $credit->agrovet_account_id
                    ];
                    
                    $app->updateToken($updateAgrovetQuery, $updateAgrovetParams);
                    
                    // Create agrovet account transaction record
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
                                              'credit',
                                              :amount,
                                              :reference_id,
                                              :description,
                                              :processed_by,
                                              NOW()
                                          )";
                                          
                    $agrovetTransactionParams = [
                        ':agrovet_account_id' => $credit->agrovet_account_id,
                        ':amount' => $repaymentAmount,
                        ':reference_id' => $repaymentId,
                        ':description' => "Input credit repayment (25% allocation) from produce sale DLVR" . str_pad($produceId, 5, '0', STR_PAD_LEFT),
                        ':processed_by' => $staffId
                    ];
                    
                    $app->insertWithoutPath($agrovetTransactionQuery, $agrovetTransactionParams);
                    
                    // Create input credit transaction record
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
                                                  'repayment',
                                                  :amount,
                                                  :reference_id,
                                                  :description,
                                                  :processed_by,
                                                  NOW()
                                              )";
                                              
                    $inputCreditTransactionParams = [
                        ':input_credit_id' => $credit->approved_credit_id,
                        ':amount' => $repaymentAmount,
                        ':reference_id' => $repaymentId,
                        ':description' => "Fair distribution repayment (25%) from produce sale DLVR" . str_pad($produceId, 5, '0', STR_PAD_LEFT),
                        ':processed_by' => $staffId
                    ];
                    
                    $app->insertWithoutPath($inputCreditTransactionQuery, $inputCreditTransactionParams);
                    
                    // Update application status if credit is completed
                    if ($newStatus === 'completed') {
                        $updateAppQuery = "UPDATE input_credit_applications SET
                                          status = 'completed'
                                          WHERE id = :credit_app_id";
                                         
                        $updateAppParams = [':credit_app_id' => $credit->credit_application_id];
                        $app->updateToken($updateAppQuery, $updateAppParams);
                        
                        // Log input credit completion
                        $creditLogQuery = "INSERT INTO input_credit_logs (
                                         input_credit_application_id,
                                         user_id,
                                         action_type,
                                         description,
                                         created_at
                                     ) VALUES (
                                         :input_credit_application_id,
                                         :user_id,
                                         'completed',
                                         :description,
                                         NOW()
                                     )";
                                        
                        $creditLogParams = [
                            ':input_credit_application_id' => $credit->credit_application_id,
                            ':user_id' => $staffId,
                            ':description' => "Input credit fully repaid via fair distribution from produce sale DLVR" . str_pad($produceId, 5, '0', STR_PAD_LEFT)
                        ];
                        
                        $app->insertWithoutPath($creditLogQuery, $creditLogParams);
                    }
                    
                    // Add to repayment details for reporting
                    $inputCreditRepaymentDetails[] = [
                        'credit_reference' => $credit->reference,
                        'agrovet_name' => $credit->agrovet_name,
                        'amount' => $repaymentAmount,
                        'new_status' => $newStatus
                    ];
                }
            }
        }
        
        // ------------------------------------------------------------------
        // 8. Process SACCO LOAN repayments (25% allocation)
        // ------------------------------------------------------------------
        
        // Retrieve active SACCO loans for the farmer
        $saccoLoansQuery = "SELECT 
                            al.id,
                            al.remaining_balance,
                            al.status,
                            al.loan_application_id,
                            la.id as loan_id,
                            CONCAT('SACCO-LOAN', LPAD(la.id, 5, '0')) as reference
                          FROM approved_loans al
                          JOIN loan_applications la ON al.loan_application_id = la.id
                          WHERE la.farmer_id = '{$farmerId}'
                          AND al.status = 'active'
                          AND (la.provider_type = 'sacco' OR la.bank_id IS NULL)
                          ORDER BY al.disbursement_date ASC";
                          
        $activeSaccoLoans = $app->select_all($saccoLoansQuery);
        
        if ($activeSaccoLoans && count($activeSaccoLoans) > 0) {
            // Calculate total outstanding SACCO loan amount
            $totalOutstandingSaccoLoans = 0;
            foreach ($activeSaccoLoans as $loan) {
                $totalOutstandingSaccoLoans += floatval($loan->remaining_balance);
            }
            
            // Distribute the 25% allocation proportionally among active SACCO loans
            foreach ($activeSaccoLoans as $loan) {
                if ($totalOutstandingSaccoLoans > 0) {
                    // Calculate this loan's proportion of total outstanding
                    $loanProportion = floatval($loan->remaining_balance) / $totalOutstandingSaccoLoans;
                    
                    // Calculate repayment amount for this loan
                    $repaymentAmount = $saccoLoanAllocation * $loanProportion;
                    
                    // Ensure we don't exceed the remaining balance
                    $repaymentAmount = min($repaymentAmount, floatval($loan->remaining_balance));
                    
                    // Round to 2 decimal places
                    $repaymentAmount = round($repaymentAmount, 2);
                    
                    // Skip if repayment amount is zero
                    if ($repaymentAmount <= 0) {
                        continue;
                    }
                    
                    // Update total repayment amount
                    $totalSaccoLoanRepaymentAmount += $repaymentAmount;
                    
                    // Calculate new remaining balance
                    $newRemainingBalance = floatval($loan->remaining_balance) - $repaymentAmount;
                    $newStatus = ($newRemainingBalance <= 0) ? 'completed' : 'active';
                    
                    // Create loan repayment record
                    $repaymentQuery = "INSERT INTO loan_repayments (
                                        approved_loan_id,
                                        produce_delivery_id,
                                        amount,
                                        payment_date,
                                        payment_method,
                                        notes,
                                        created_at
                                    ) VALUES (
                                        :approved_loan_id,
                                        :produce_delivery_id,
                                        :amount,
                                        NOW(),
                                        'produce_deduction',
                                        :notes,
                                        NOW()
                                    )";
                                    
                    $repaymentParams = [
                        ':approved_loan_id' => $loan->id,
                        ':produce_delivery_id' => $produceId,
                        ':amount' => $repaymentAmount,
                        ':notes' => "SACCO loan repayment (25% allocation) from produce sale DLVR" . str_pad($produceId, 5, '0', STR_PAD_LEFT)
                    ];
                    
                    $app->insertWithoutPath($repaymentQuery, $repaymentParams);
                    $repaymentId = $app->lastInsertId();
                    
                    // Update loan remaining balance and status
                    $updateLoanQuery = "UPDATE approved_loans SET
                                        remaining_balance = :remaining_balance,
                                        status = :status,
                                        updated_at = NOW()
                                     WHERE id = :loan_id";
                                     
                    $updateLoanParams = [
                        ':remaining_balance' => $newRemainingBalance,
                        ':status' => $newStatus,
                        ':loan_id' => $loan->id
                    ];
                    
                    $app->updateToken($updateLoanQuery, $updateLoanParams);
                    
                    // Add loan transaction record
                    $loanTransactionQuery = "INSERT INTO loan_transactions (
                                            loan_id,
                                            transaction_type,
                                            amount,
                                            reference_id,
                                            description,
                                            processed_by,
                                            created_at
                                        ) VALUES (
                                            :loan_id,
                                            'repayment',
                                            :amount,
                                            :reference_id,
                                            :description,
                                            :processed_by,
                                            NOW()
                                        )";
                                        
                    $loanTransactionParams = [
                        ':loan_id' => $loan->id,
                        ':amount' => $repaymentAmount,
                        ':reference_id' => $repaymentId,
                        ':description' => "SACCO loan repayment (25% allocation) from produce sale DLVR" . str_pad($produceId, 5, '0', STR_PAD_LEFT),
                        ':processed_by' => $staffId
                    ];
                    
                    $app->insertWithoutPath($loanTransactionQuery, $loanTransactionParams);
                    
                    // Update loan application status if loan is completed
                    if ($newStatus === 'completed') {
                        $updateAppQuery = "UPDATE loan_applications SET
                                          status = 'completed'
                                          WHERE id = :loan_app_id";
                                         
                        $updateAppParams = [':loan_app_id' => $loan->loan_application_id];
                        $app->updateToken($updateAppQuery, $updateAppParams);
                        
                        // Log loan completion
                        $loanLogQuery = "INSERT INTO loan_logs (
                                        loan_application_id,
                                        user_id,
                                        action_type,
                                        description,
                                        created_at
                                    ) VALUES (
                                        :loan_application_id,
                                        :user_id,
                                        'completed',
                                        :description,
                                        NOW()
                                    )";
                                    
                        $loanLogParams = [
                            ':loan_application_id' => $loan->loan_application_id,
                            ':user_id' => $staffId,
                            ':description' => "SACCO loan fully repaid via fair distribution from produce sale DLVR" . str_pad($produceId, 5, '0', STR_PAD_LEFT)
                        ];
                        
                        $app->insertWithoutPath($loanLogQuery, $loanLogParams);
                    }
                    
                    // Add to repayment details for reporting
                    $saccoLoanRepaymentDetails[] = [
                        'loan_reference' => $loan->reference,
                        'amount' => $repaymentAmount,
                        'new_status' => $newStatus
                    ];
                }
            }
        }
        
        // ------------------------------------------------------------------
        // 9. Process BANK LOAN repayments (25% allocation) - NEW!
        // ------------------------------------------------------------------
        
        // Retrieve active BANK loans for the farmer
        $bankLoansQuery = "SELECT 
                            al.id,
                            al.remaining_balance,
                            al.status,
                            al.loan_application_id,
                            al.bank_id,
                            la.id as loan_id,
                            CONCAT('BANK-LOAN', LPAD(la.id, 5, '0')) as reference,
                            b.name as bank_name
                          FROM approved_loans al
                          JOIN loan_applications la ON al.loan_application_id = la.id
                          LEFT JOIN banks b ON al.bank_id = b.id
                          WHERE la.farmer_id = '{$farmerId}'
                          AND al.status = 'active'
                          AND la.provider_type = 'bank'
                          AND al.bank_id IS NOT NULL
                          ORDER BY al.disbursement_date ASC";
                          
        $activeBankLoans = $app->select_all($bankLoansQuery);
        
        if ($activeBankLoans && count($activeBankLoans) > 0) {
            // Calculate total outstanding BANK loan amount
            $totalOutstandingBankLoans = 0;
            foreach ($activeBankLoans as $loan) {
                $totalOutstandingBankLoans += floatval($loan->remaining_balance);
            }
            
            // Distribute the 25% allocation proportionally among active BANK loans
            foreach ($activeBankLoans as $loan) {
                if ($totalOutstandingBankLoans > 0) {
                    // Calculate this loan's proportion of total outstanding
                    $loanProportion = floatval($loan->remaining_balance) / $totalOutstandingBankLoans;
                    
                    // Calculate repayment amount for this loan
                    $repaymentAmount = $bankLoanAllocation * $loanProportion;
                    
                    // Ensure we don't exceed the remaining balance
                    $repaymentAmount = min($repaymentAmount, floatval($loan->remaining_balance));
                    
                    // Round to 2 decimal places
                    $repaymentAmount = round($repaymentAmount, 2);
                    
                    // Skip if repayment amount is zero
                    if ($repaymentAmount <= 0) {
                        continue;
                    }
                    
                    // Update total repayment amount
                    $totalBankLoanRepaymentAmount += $repaymentAmount;
                    
                    // Calculate new remaining balance
                    $newRemainingBalance = floatval($loan->remaining_balance) - $repaymentAmount;
                    $newStatus = ($newRemainingBalance <= 0) ? 'completed' : 'active';
                    
                    // Create loan repayment record (with bank_id)
                    $repaymentQuery = "INSERT INTO loan_repayments (
                                        approved_loan_id,
                                        bank_id,
                                        produce_delivery_id,
                                        amount,
                                        payment_date,
                                        payment_method,
                                        notes,
                                        created_at
                                    ) VALUES (
                                        :approved_loan_id,
                                        :bank_id,
                                        :produce_delivery_id,
                                        :amount,
                                        NOW(),
                                        'produce_deduction',
                                        :notes,
                                        NOW()
                                    )";
                                    
                    $repaymentParams = [
                        ':approved_loan_id' => $loan->id,
                        ':bank_id' => $loan->bank_id,
                        ':produce_delivery_id' => $produceId,
                        ':amount' => $repaymentAmount,
                        ':notes' => "Bank loan repayment (25% allocation) from produce sale DLVR" . str_pad($produceId, 5, '0', STR_PAD_LEFT)
                    ];
                    
                    $app->insertWithoutPath($repaymentQuery, $repaymentParams);
                    $repaymentId = $app->lastInsertId();
                    
                    // Update loan remaining balance and status
                    $updateLoanQuery = "UPDATE approved_loans SET
                                        remaining_balance = :remaining_balance,
                                        status = :status,
                                        updated_at = NOW()
                                     WHERE id = :loan_id";
                                     
                    $updateLoanParams = [
                        ':remaining_balance' => $newRemainingBalance,
                        ':status' => $newStatus,
                        ':loan_id' => $loan->id
                    ];
                    
                    $app->updateToken($updateLoanQuery, $updateLoanParams);
                    
                    // Add loan transaction record
                    $loanTransactionQuery = "INSERT INTO loan_transactions (
                                            loan_id,
                                            transaction_type,
                                            amount,
                                            reference_id,
                                            description,
                                            processed_by,
                                            created_at
                                        ) VALUES (
                                            :loan_id,
                                            'repayment',
                                            :amount,
                                            :reference_id,
                                            :description,
                                            :processed_by,
                                            NOW()
                                        )";
                                        
                    $loanTransactionParams = [
                        ':loan_id' => $loan->id,
                        ':amount' => $repaymentAmount,
                        ':reference_id' => $repaymentId,
                        ':description' => "Bank loan repayment (25% allocation) from produce sale DLVR" . str_pad($produceId, 5, '0', STR_PAD_LEFT),
                        ':processed_by' => $staffId
                    ];
                    
                    $app->insertWithoutPath($loanTransactionQuery, $loanTransactionParams);
                    
                    // Update loan application status if loan is completed
                    if ($newStatus === 'completed') {
                        $updateAppQuery = "UPDATE loan_applications SET
                                          status = 'completed'
                                          WHERE id = :loan_app_id";
                                         
                        $updateAppParams = [':loan_app_id' => $loan->loan_application_id];
                        $app->updateToken($updateAppQuery, $updateAppParams);
                        
                        // Log loan completion
                        $loanLogQuery = "INSERT INTO loan_logs (
                                        loan_application_id,
                                        user_id,
                                        action_type,
                                        description,
                                        created_at
                                    ) VALUES (
                                        :loan_application_id,
                                        :user_id,
                                        'completed',
                                        :description,
                                        NOW()
                                    )";
                                    
                        $loanLogParams = [
                            ':loan_application_id' => $loan->loan_application_id,
                            ':user_id' => $staffId,
                            ':description' => "Bank loan fully repaid via fair distribution from produce sale DLVR" . str_pad($produceId, 5, '0', STR_PAD_LEFT)
                        ];
                        
                        $app->insertWithoutPath($loanLogQuery, $loanLogParams);
                    }
                    
                    // Add to repayment details for reporting
                    $bankLoanRepaymentDetails[] = [
                        'loan_reference' => $loan->reference,
                        'bank_name' => $loan->bank_name,
                        'amount' => $repaymentAmount,
                        'new_status' => $newStatus
                    ];
                }
            }
        }
        
        // ------------------------------------------------------------------
        // 10. Calculate final farmer payment (guaranteed 25%)
        // ------------------------------------------------------------------
        
        $finalFarmerPaymentAmount = $farmerAllocation;
        
        // ------------------------------------------------------------------
        // 11. Update produce delivery status and create transaction records
        // ------------------------------------------------------------------
        
        // Update produce delivery status to mark as paid
        $updateProduceStatusQuery = "UPDATE produce_deliveries SET 
                                    status = 'paid'
                                    WHERE id = :produce_id";
                                    
        $updateProduceStatusParams = [
            ':produce_id' => $produceId
        ];
        
        $app->updateToken($updateProduceStatusQuery, $updateProduceStatusParams);
        
        // Add produce log for payment disbursement
        $produceLogQuery = "INSERT INTO produce_logs (
                           produce_delivery_id,
                           user_id,
                           action_type,
                           description,
                           created_at
                       ) VALUES (
                           :produce_delivery_id,
                           :user_id,
                           'paid',
                           :description,
                           NOW()
                       )";
                        
        $logDescription = "Fair distribution payment of KES " . number_format($finalFarmerPaymentAmount, 2) . " disbursed to farmer (25% guaranteed allocation)";
        
        if ($totalInputCreditRepaymentAmount > 0) {
            $logDescription .= " with input credit repayments of KES " . number_format($totalInputCreditRepaymentAmount, 2) . " (25%)";
        }
        
        if ($totalSaccoLoanRepaymentAmount > 0) {
            $logDescription .= " and SACCO loan repayments of KES " . number_format($totalSaccoLoanRepaymentAmount, 2) . " (25%)";
        }
        
        if ($totalBankLoanRepaymentAmount > 0) {
            $logDescription .= " and bank loan repayments of KES " . number_format($totalBankLoanRepaymentAmount, 2) . " (25%)";
        }
        
        $produceLogParams = [
            ':produce_delivery_id' => $produceId,
            ':user_id' => $staffId,
            ':description' => $logDescription
        ];
        
        $app->insertWithoutPath($produceLogQuery, $produceLogParams);
        
        // ------------------------------------------------------------------
        // 12. Process bank account transactions
        // ------------------------------------------------------------------
        
        // Calculate total bank disbursement (farmer payment + input credit repayments)
        $totalBankDisbursement = $finalFarmerPaymentAmount + $totalInputCreditRepaymentAmount;
        
        // Debit bank account for total disbursement
        $newBankBalance = floatval($bankAccount->balance) - $totalBankDisbursement;
        
        $updateBankQuery = "UPDATE bank_branch_accounts SET
                           balance = :balance
                           WHERE id = :account_id";
                           
        $updateBankParams = [
            ':balance' => $newBankBalance,
            ':account_id' => $bankAccount->id
        ];
        
        $app->updateToken($updateBankQuery, $updateBankParams);
        
        // Record bank transaction for farmer payment
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
                               'debit',
                               :amount,
                               :reference_id,
                               :description,
                               :processed_by,
                               NOW()
                           )";
                           
        $bankTransactionParams = [
            ':bank_account_id' => $bankAccount->id,
            ':amount' => $finalFarmerPaymentAmount,
            ':reference_id' => $produceId,
            ':description' => "Fair distribution payment to farmer (25% allocation) for produce sale DLVR" . str_pad($produceId, 5, '0', STR_PAD_LEFT),
            ':processed_by' => $staffId
        ];
        
        $app->insertWithoutPath($bankTransactionQuery, $bankTransactionParams);
        
        // Record separate bank transaction for agrovet payments if applicable
        if ($totalInputCreditRepaymentAmount > 0) {
            $bankAgrovetTransactionQuery = "INSERT INTO bank_account_transactions (
                                          bank_account_id,
                                          transaction_type,
                                          amount,
                                          reference_id,
                                          description,
                                          processed_by,
                                          created_at
                                      ) VALUES (
                                          :bank_account_id,
                                          'debit',
                                          :amount,
                                          :reference_id,
                                          :description,
                                          :processed_by,
                                          NOW()
                                      )";
                                      
            $bankAgrovetTransactionParams = [
                ':bank_account_id' => $bankAccount->id,
                ':amount' => $totalInputCreditRepaymentAmount,
                ':reference_id' => $produceId,
                ':description' => "Input credit repayments to agrovets (25% allocation) for produce sale DLVR" . str_pad($produceId, 5, '0', STR_PAD_LEFT),
                ':processed_by' => $staffId
            ];
            
            $app->insertWithoutPath($bankAgrovetTransactionQuery, $bankAgrovetTransactionParams);
        }
        
        // ------------------------------------------------------------------
        // 13. Credit farmer's account
        // ------------------------------------------------------------------
        
        // Calculate new farmer account balance
        $newFarmerAccountBalance = floatval($produceDetails->current_account_balance) + $finalFarmerPaymentAmount;
        
        $updateFarmerAccountQuery = "UPDATE farmer_accounts SET
                                    balance = :balance
                                    WHERE id = :account_id";
                                    
        $updateFarmerAccountParams = [
            ':balance' => $newFarmerAccountBalance,
            ':account_id' => $produceDetails->farmer_account_id
        ];
        
        $app->updateToken($updateFarmerAccountQuery, $updateFarmerAccountParams);
        
        // Create farmer account transaction record
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
                            
        $farmerTransactionDescription = "Fair distribution payment for produce sale DLVR" . str_pad($produceId, 5, '0', STR_PAD_LEFT) . " (25% guaranteed allocation)";
                            
        $farmerTransactionParams = [
            ':farmer_account_id' => $produceDetails->farmer_account_id,
            ':amount' => $finalFarmerPaymentAmount,
            ':reference_id' => $produceId,
            ':description' => $farmerTransactionDescription,
            ':processed_by' => $staffId
        ];
        
        $app->insertWithoutPath($farmerTransactionQuery, $farmerTransactionParams);
        $farmerTransactionId = $app->lastInsertId();
        
        // ------------------------------------------------------------------
        // 14. Create activity and audit logs
        // ------------------------------------------------------------------
        
        // Add activity log
        $activityQuery = "INSERT INTO activity_logs (
                          user_id,
                          activity_type,
                          description,
                          created_at
                      ) VALUES (
                          :user_id,
                          'fair_payment_processed',
                          :description,
                          NOW()
                      )";
                      
        $activityDescription = "Fair distribution payment processed for produce delivery ID: " . $produceId . 
                             " to farmer: " . $produceDetails->farmer_name . 
                             ". Distribution: Commission 10% (KES " . number_format($commission, 2) . 
                             "), Input Credits 25% (KES " . number_format($totalInputCreditRepaymentAmount, 2) . 
                             "), SACCO Loans 25% (KES " . number_format($totalSaccoLoanRepaymentAmount, 2) . 
                             "), Bank Loans 25% (KES " . number_format($totalBankLoanRepaymentAmount, 2) . 
                             "), Farmer 25% (KES " . number_format($finalFarmerPaymentAmount, 2) . ")";
        
        $activityParams = [
            ':user_id' => $staffId,
            ':description' => $activityDescription
        ];
        
        $app->insertWithoutPath($activityQuery, $activityParams);
        
        // Add payment comment
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
                      
        $commentDescription = "Fair distribution payment processed. Total value: KES " . number_format($saleValue, 2) . 
                           ". SACCO commission (10%): KES " . number_format($commission, 2) . 
                           ". Input credit repayments (25%): KES " . number_format($totalInputCreditRepaymentAmount, 2) . 
                           ". SACCO loan repayments (25%): KES " . number_format($totalSaccoLoanRepaymentAmount, 2) . 
                           ". Bank loan repayments (25%): KES " . number_format($totalBankLoanRepaymentAmount, 2) . 
                           ". Farmer payment (25%): KES " . number_format($finalFarmerPaymentAmount, 2);
        
        if (!empty($paymentNotes)) {
            $commentDescription .= ". Notes: " . $paymentNotes;
        }
        
        $commentParams = [
            ':user_id' => $staffId,
            ':comment_type_id' => 7,
            ':reference_id' => $produceId,
            ':comment' => $commentDescription
        ];
        
        $app->insertWithoutPath($commentQuery, $commentParams);
        
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
                        'create',
                        'fair_distribution_payment',
                        :record_id,
                        NULL,
                        :new_values,
                        NOW()
                    )";
                    
        $auditValues = [
            'payment_system' => 'fair_distribution',
            'total_sale_value' => $saleValue,
            'sacco_commission' => $commission,
            'input_credit_allocation' => $inputCreditAllocation,
            'sacco_loan_allocation' => $saccoLoanAllocation,
            'bank_loan_allocation' => $bankLoanAllocation,
            'farmer_allocation' => $farmerAllocation,
            'actual_payments' => [
                'input_credits_paid' => $totalInputCreditRepaymentAmount,
                'sacco_loans_paid' => $totalSaccoLoanRepaymentAmount,
                'bank_loans_paid' => $totalBankLoanRepaymentAmount,
                'farmer_paid' => $finalFarmerPaymentAmount
            ],
            'payment_details' => [
                'input_credit_details' => $inputCreditRepaymentDetails,
                'sacco_loan_details' => $saccoLoanRepaymentDetails,
                'bank_loan_details' => $bankLoanRepaymentDetails
            ]
        ];
        
        $auditParams = [
            ':user_id' => $staffId,
            ':record_id' => $farmerTransactionId,
            ':new_values' => json_encode($auditValues)
        ];
        
        $app->insertWithoutPath($auditQuery, $auditParams);
        
        // ------------------------------------------------------------------
        // 15. Commit transaction and return response
        // ------------------------------------------------------------------
        
        $app->commit();
        
        // Prepare detailed response
        $response = [
            'success' => true,
            'message' => 'Fair distribution payment processed successfully',
            'payment_system' => 'fair_distribution',
            'payment_details' => [
                'transaction_id' => $farmerTransactionId,
                'produce_reference' => 'DLVR' . str_pad($produceId, 5, '0', STR_PAD_LEFT),
                'farmer_name' => $produceDetails->farmer_name,
                'product_name' => $produceDetails->product_name,
                'total_sale_value' => $saleValue,
                'distribution_breakdown' => [
                    'sacco_commission' => [
                        'percentage' => 10,
                        'amount' => $commission
                    ],
                    'input_credit_repayments' => [
                        'percentage' => 25,
                        'allocated' => $inputCreditAllocation,
                        'actual_paid' => $totalInputCreditRepaymentAmount,
                        'details' => $inputCreditRepaymentDetails
                    ],
                    'sacco_loan_repayments' => [
                        'percentage' => 25,
                        'allocated' => $saccoLoanAllocation,
                        'actual_paid' => $totalSaccoLoanRepaymentAmount,
                        'details' => $saccoLoanRepaymentDetails
                    ],
                    'bank_loan_repayments' => [
                        'percentage' => 25,
                        'allocated' => $bankLoanAllocation,
                        'actual_paid' => $totalBankLoanRepaymentAmount,
                        'details' => $bankLoanRepaymentDetails
                    ],
                    'farmer_payment' => [
                        'percentage' => 25,
                        'amount' => $finalFarmerPaymentAmount
                    ]
                ]
            ]
        ];
        
        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
        
    } catch (Exception $e) {
        // Rollback on error
        if (isset($app)) {
            $app->rollBack();
        }
        
        // Return error response
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Error processing fair distribution payment: ' . $e->getMessage()
        ]);
        exit;
    }
}

// Return error response for invalid request method
header('Content-Type: application/json');
echo json_encode([
    'success' => false,
    'message' => 'Invalid request method'
]);
exit;