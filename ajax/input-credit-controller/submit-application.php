<?php
include "../../config/config.php";
include "../../libs/App.php";
ob_start();
// Check authentication
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}
// Check if this is a POST request with required data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $app = new App;
        $app->beginTransaction();
        
        // Extract and validate basic input credit application data
        if (!isset($_POST['farmer_id']) || !isset($_POST['agrovet_id']) || 
            !isset($_POST['total_amount']) || !isset($_POST['credit_percentage']) ||
            !isset($_POST['total_with_interest']) || !isset($_POST['repayment_percentage']) || 
            !isset($_POST['purpose']) || !isset($_POST['selected_inputs'])) {
            
            throw new Exception("Missing required fields for input credit application");
        }
        
        $farmer_id = $_POST['farmer_id'];
        $agrovet_id = $_POST['agrovet_id'];
        $total_amount = $_POST['total_amount'];
        $credit_percentage = $_POST['credit_percentage'];
        $total_with_interest = $_POST['total_with_interest'];
        $repayment_percentage = $_POST['repayment_percentage'];
        $purpose = $_POST['purpose'];
        $reference_number = $_POST['reference_number'] ?? 'ICREDIT/' . date('Ymd') . '/' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        // Decode selected inputs JSON
        $selected_inputs = json_decode($_POST['selected_inputs'], true);
        
        if (!is_array($selected_inputs) || empty($selected_inputs)) {
            throw new Exception("No input items selected or invalid input items format");
        }
        
        // Validate farmer exists
        $farmerQuery = "SELECT f.id, f.user_id, f.registration_number, f.category_id, 
                               u.first_name, u.last_name, fc.name as category_name
                        FROM farmers f 
                        JOIN users u ON f.user_id = u.id 
                        LEFT JOIN farmer_categories fc ON f.category_id = fc.id
                        WHERE f.id = :farmer_id";
        
        $farmer = $app->selectOne($farmerQuery, [':farmer_id' => $farmer_id]);
        
        if (!$farmer) {
            throw new Exception("Farmer record not found");
        }
        
        // Validate agrovet exists and is active
        $agrovetQuery = "SELECT a.id, a.name, a.type_id, a.location, a.is_active 
                         FROM agrovets a 
                         WHERE a.id = :agrovet_id";
        
        $agrovet = $app->selectOne($agrovetQuery, [':agrovet_id' => $agrovet_id]);
        
        if (!$agrovet) {
            throw new Exception("Agrovet not found");
        }
        
        if (!$agrovet->is_active) {
            throw new Exception("Selected agrovet is not active");
        }
        
        // Validate credit percentage (5-15%)
        if ($credit_percentage < 5 || $credit_percentage > 15) {
            throw new Exception("Credit percentage must be between 5% and 15%");
        }
        
        // Validate repayment percentage (10-50%)
        if ($repayment_percentage < 10 || $repayment_percentage > 50) {
            throw new Exception("Repayment percentage must be between 10% and 50%");
        }
        
        // Validate total amount and total with interest
        if ($total_amount <= 0) {
            throw new Exception("Total amount must be greater than zero");
        }
        
        $calculated_interest = $total_amount * ($credit_percentage / 100);
        $calculated_total = $total_amount + $calculated_interest;
        
        // Allow for small floating-point differences
        if (abs($calculated_total - $total_with_interest) > 1) {
            throw new Exception("Total with interest amount mismatch");
        }
        
        // Validate each input item
        foreach ($selected_inputs as $item) {
            if (!isset($item['id']) || !isset($item['name']) || !isset($item['type']) || 
                !isset($item['unit']) || !isset($item['price']) || !isset($item['quantity']) || 
                !isset($item['total'])) {
                
                throw new Exception("Invalid input item format");
            }
            
            if ($item['quantity'] <= 0) {
                throw new Exception("Input item quantity must be greater than zero");
            }
            
            if ($item['price'] <= 0) {
                throw new Exception("Input item price must be greater than zero");
            }
            
            $calculated_item_total = $item['price'] * $item['quantity'];
            
            // Allow for small floating-point differences
            if (abs($calculated_item_total - $item['total']) > 0.01) {
                throw new Exception("Input item total amount mismatch");
            }
            
            // Verify input item exists in catalog
            $catalogQuery = "SELECT id FROM input_catalog WHERE id = :item_id AND is_active = 1";
            $catalogItem = $app->selectOne($catalogQuery, [':item_id' => $item['id']]);
            
            if (!$catalogItem) {
                throw new Exception("One or more input items are not available in the catalog");
            }
        }
        
        // Continue to Part 2: Creditworthiness Assessment
        // Part 2: Creditworthiness Assessment
        
        // Define creditworthiness assessment functions
        function calculateInputCreditworthiness($app, $farmer_id, $total_amount) {
            // Initialize scores array
            $scores = [
                'input_repayment_history' => 0,  // 30% weight
                'financial_obligations' => 0,    // 25% weight
                'produce_history' => 0,          // 35% weight
                'amount_ratio' => 0              // 10% weight
            ];
            
            // 1. Check past input credit repayment history (30% weight)
            $repaymentScore = checkInputRepaymentHistory($app, $farmer_id);
            $scores['input_repayment_history'] = $repaymentScore;
            
            // 2. Check current financial obligations (25% weight)
            $obligationsScore = checkFinancialObligations($app, $farmer_id, $total_amount);
            $scores['financial_obligations'] = $obligationsScore;
            
            // 3. Check produce delivery history and value (35% weight)
            $produceScore = checkProduceHistory($app, $farmer_id);
            $scores['produce_history'] = $produceScore;
            
            // 4. Check input credit amount to average monthly produce value ratio (10% weight)
            $amountRatioScore = checkInputAmountRatio($app, $farmer_id, $total_amount);
            $scores['amount_ratio'] = $amountRatioScore;
            
            // Calculate final weighted score
            $finalScore = ($repaymentScore * 0.30) + 
                         ($obligationsScore * 0.25) + 
                         ($produceScore * 0.35) + 
                         ($amountRatioScore * 0.10);
            
            return [
                'final_score' => round($finalScore, 2),
                'scores' => $scores
            ];
        }
        
        // Helper assessment functions
        function checkInputRepaymentHistory($app, $farmer_id) {
            // Check if farmer has any past input credits
            $pastCreditsQuery = "SELECT COUNT(*) as credit_count
                                FROM approved_input_credits aic
                                JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                WHERE ica.farmer_id = :farmer_id
                                AND aic.status IN ('completed', 'defaulted')";
            
            $pastCredits = $app->selectOne($pastCreditsQuery, [':farmer_id' => $farmer_id]);
            
            // If no past input credits, give a neutral score of 70
            if ($pastCredits->credit_count == 0) {
                return 70;
            }
            
            // Check completed credits vs defaulted credits
            $creditRatioQuery = "SELECT 
                                SUM(CASE WHEN aic.status = 'completed' THEN 1 ELSE 0 END) as completed_count,
                                SUM(CASE WHEN aic.status = 'defaulted' THEN 1 ELSE 0 END) as defaulted_count
                                FROM approved_input_credits aic
                                JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                WHERE ica.farmer_id = :farmer_id
                                AND aic.status IN ('completed', 'defaulted')";
            
            $creditRatio = $app->selectOne($creditRatioQuery, [':farmer_id' => $farmer_id]);
            
            // Calculate repayment score based on completed vs defaulted ratio
            $totalCredits = $creditRatio->completed_count + $creditRatio->defaulted_count;
            if ($totalCredits == 0) return 70;
            
            $completionRate = ($creditRatio->completed_count / $totalCredits) * 100;
            
            // Score between 0-100 based on completion rate
            return $completionRate;
        }
        
        function checkFinancialObligations($app, $farmer_id, $amount_requested) {
            // Get outstanding loan amount
            $outstandingLoansQuery = "SELECT COALESCE(SUM(al.remaining_balance), 0) as outstanding_loans
                                      FROM approved_loans al
                                      JOIN loan_applications la ON al.loan_application_id = la.id
                                      WHERE la.farmer_id = :farmer_id
                                      AND al.status = 'active'";
            
            $outstandingLoans = $app->selectOne($outstandingLoansQuery, [':farmer_id' => $farmer_id]);
            
            // Get outstanding input credits
            $outstandingCreditsQuery = "SELECT COALESCE(SUM(aic.remaining_balance), 0) as outstanding_credits
                                        FROM approved_input_credits aic
                                        JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                        WHERE ica.farmer_id = :farmer_id
                                        AND aic.status = 'active'";
            
            $outstandingCredits = $app->selectOne($outstandingCreditsQuery, [':farmer_id' => $farmer_id]);
            
            // Get average monthly produce value
            $produceValueQuery = "SELECT COALESCE(AVG(pd.total_value), 0) as avg_monthly_value
                                  FROM produce_deliveries pd
                                  JOIN farm_products fp ON pd.farm_product_id = fp.id
                                  JOIN farms f ON fp.farm_id = f.id
                                  WHERE f.farmer_id = :farmer_id
                                  AND pd.status IN ('verified', 'sold')
                                  AND pd.delivery_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)";
            
            $produceValue = $app->selectOne($produceValueQuery, [':farmer_id' => $farmer_id]);
            
            // Calculate debt-to-income ratio
            $totalObligations = $outstandingLoans->outstanding_loans + 
                                $outstandingCredits->outstanding_credits + 
                                $amount_requested;
            
            $monthlyValue = $produceValue->avg_monthly_value;
            
            // If no monthly value, give a conservative score
            if ($monthlyValue <= 0) {
                return 40;
            }
            
            $debtToIncomeRatio = $totalObligations / ($monthlyValue * 6); // 6 months of income
            
            // Score based on debt-to-income ratio
            if ($debtToIncomeRatio <= 0.2) {
                return 100; // Excellent: debt is less than 20% of 6-month income
            } elseif ($debtToIncomeRatio <= 0.4) {
                return 80; // Good: debt is 20-40% of 6-month income
            } elseif ($debtToIncomeRatio <= 0.6) {
                return 60; // Fair: debt is 40-60% of 6-month income
            } elseif ($debtToIncomeRatio <= 0.8) {
                return 40; // Poor: debt is 60-80% of 6-month income
            } else {
                return 20; // Very poor: debt exceeds 80% of 6-month income
            }
        }
        
        function checkProduceHistory($app, $farmer_id) {
            // Check delivery count in the last 6 months
            $deliveryCountQuery = "SELECT COUNT(*) as delivery_count
                                  FROM produce_deliveries pd
                                  JOIN farm_products fp ON pd.farm_product_id = fp.id
                                  JOIN farms f ON fp.farm_id = f.id
                                  WHERE f.farmer_id = :farmer_id
                                  AND pd.status IN ('verified', 'sold')
                                  AND pd.delivery_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)";
            
            $deliveryCount = $app->selectOne($deliveryCountQuery, [':farmer_id' => $farmer_id]);
            
            // Get total value of deliveries
            $deliveryValueQuery = "SELECT COALESCE(SUM(pd.total_value), 0) as total_value
                                  FROM produce_deliveries pd
                                  JOIN farm_products fp ON pd.farm_product_id = fp.id
                                  JOIN farms f ON fp.farm_id = f.id
                                  WHERE f.farmer_id = :farmer_id
                                  AND pd.status IN ('verified', 'sold')
                                  AND pd.delivery_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)";
            
            $deliveryValue = $app->selectOne($deliveryValueQuery, [':farmer_id' => $farmer_id]);
            
            // Calculate delivery frequency score (0-50 points)
            $frequencyScore = 0;
            if ($deliveryCount->delivery_count >= 12) {
                $frequencyScore = 50; // 2+ deliveries per month
            } elseif ($deliveryCount->delivery_count >= 6) {
                $frequencyScore = 40; // 1+ delivery per month
            } elseif ($deliveryCount->delivery_count >= 3) {
                $frequencyScore = 30; // 1 delivery every 2 months
            } elseif ($deliveryCount->delivery_count >= 1) {
                $frequencyScore = 20; // At least 1 delivery
            } else {
                $frequencyScore = 0; // No deliveries
            }
            
            // Calculate value score (0-50 points)
            $valueScore = 0;
            if ($deliveryValue->total_value >= 500000) {
                $valueScore = 50; // Very high value
            } elseif ($deliveryValue->total_value >= 250000) {
                $valueScore = 40; // High value
            } elseif ($deliveryValue->total_value >= 100000) {
                $valueScore = 30; // Medium value
            } elseif ($deliveryValue->total_value >= 50000) {
                $valueScore = 20; // Low value
            } elseif ($deliveryValue->total_value > 0) {
                $valueScore = 10; // Very low value
            } else {
                $valueScore = 0; // No value
            }
            
            // Combine frequency and value scores
            return $frequencyScore + $valueScore;
        }
                        
                        function checkInputAmountRatio($app, $farmer_id, $amount_requested) {
                            // Get average monthly produce value
                            $avgValueQuery = "SELECT COALESCE(AVG(pd.total_value), 0) as avg_monthly_value
                                             FROM produce_deliveries pd
                                             JOIN farm_products fp ON pd.farm_product_id = fp.id
                                             JOIN farms f ON fp.farm_id = f.id
                                             WHERE f.farmer_id = :farmer_id
                                             AND pd.status IN ('verified', 'sold')
                                             AND pd.delivery_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)";
                            
                            $avgValue = $app->selectOne($avgValueQuery, [':farmer_id' => $farmer_id]);
                            
                            // If no monthly value, give a conservative score
                            if ($avgValue->avg_monthly_value <= 0) {
                                return 40;
                            }
                            
                            // Calculate input credit amount to average monthly produce ratio
                            $ratio = $amount_requested / ($avgValue->avg_monthly_value * 3); // Compare to 3 months of produce
                            
                            // Score based on ratio
                            if ($ratio <= 0.5) {
                                return 100; // Excellent: credit is less than 50% of 3-month produce value
                            } elseif ($ratio <= 1.0) {
                                return 80; // Good: credit is 50-100% of 3-month produce value
                            } elseif ($ratio <= 1.5) {
                                return 60; // Fair: credit is 100-150% of 3-month produce value
                            } elseif ($ratio <= 2.0) {
                                return 40; // Poor: credit is 150-200% of 3-month produce value
                            } else {
                                return 20; // Very poor: credit exceeds 200% of 3-month produce value
                            }
                        }
                        // Add this function after your existing creditworthiness functions
                function getInputCreditFinancialSnapshot($app, $farmer_id) {
                    // Get active loans count and balance
                    $activeLoansQuery = "SELECT COUNT(*) as count, COALESCE(SUM(remaining_balance), 0) as balance 
                                        FROM approved_loans al 
                                        JOIN loan_applications la ON al.loan_application_id = la.id 
                                        WHERE la.farmer_id = :farmer_id AND al.status = 'active'";
                    $activeLoans = $app->selectOne($activeLoansQuery, [':farmer_id' => $farmer_id]);
                    
                    // Get active input credits count and balance  
                    $inputCreditsQuery = "SELECT COUNT(*) as count, COALESCE(SUM(remaining_balance), 0) as balance
                                         FROM approved_input_credits aic
                                         JOIN input_credit_applications ica ON aic.credit_application_id = ica.id
                                         WHERE ica.farmer_id = :farmer_id AND aic.status = 'active'";
                    $inputCredits = $app->selectOne($inputCreditsQuery, [':farmer_id' => $farmer_id]);
                    
                    // Get deliveries in last 6 months
                    $deliveriesQuery = "SELECT COUNT(*) as count, COALESCE(SUM(total_value), 0) as value
                                       FROM produce_deliveries pd
                                       JOIN farm_products fp ON pd.farm_product_id = fp.id
                                       JOIN farms f ON fp.farm_id = f.id
                                       WHERE f.farmer_id = :farmer_id 
                                       AND pd.delivery_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                                       AND pd.status IN ('verified', 'sold')";
                    $deliveries = $app->selectOne($deliveriesQuery, [':farmer_id' => $farmer_id]);
                    
                    // Calculate credit capacity
                    $creditCapacity = calculateInputCreditCapacity($deliveries->value, $activeLoans->balance, $inputCredits->balance);
                    
                    return [
                        'active_loans_count' => $activeLoans->count,
                        'active_loans_balance' => $activeLoans->balance,
                        'input_credits_count' => $inputCredits->count, 
                        'input_credits_balance' => $inputCredits->balance,
                        'deliveries_count' => $deliveries->count,
                        'deliveries_value' => $deliveries->value,
                        'credit_capacity' => $creditCapacity
                    ];
                }
                
                function calculateInputCreditCapacity($deliveriesValue, $activeLoansBalance, $inputCreditsBalance) {
                    if ($deliveriesValue <= 0) return 0;
                    
                    $totalDebt = $activeLoansBalance + $inputCreditsBalance;
                    $monthlyIncome = $deliveriesValue / 6; // 6 months average
                    
                    if ($monthlyIncome <= 0) return 0;
                    
                    $debtToIncomeRatio = $totalDebt / ($monthlyIncome * 6);
                    
                    // Input credit specific capacity calculation
                    if ($debtToIncomeRatio <= 0.20) return 90;
                    if ($debtToIncomeRatio <= 0.35) return 75;
                    if ($debtToIncomeRatio <= 0.50) return 60;
                    if ($debtToIncomeRatio <= 0.65) return 40;
                    return 20;
                }
                function getAgrovetInformation($app, $agrovet_id) {
                    $agrovetInfoQuery = "SELECT a.name as agrovet_name, a.location, a.phone, a.email,
                                                at.name as agrovet_type
                                         FROM agrovets a
                                         LEFT JOIN agrovet_types at ON a.type_id = at.id
                                         WHERE a.id = :agrovet_id";
                    
                    $agrovetInfo = $app->selectOne($agrovetInfoQuery, [':agrovet_id' => $agrovet_id]);
                    
                    return [
                        'agrovet_name' => $agrovetInfo->agrovet_name ?? 'Selected Agrovet',
                        'location' => $agrovetInfo->location ?? 'Location',
                        'phone' => $agrovetInfo->phone ?? '',
                        'email' => $agrovetInfo->email ?? '',
                        'type' => $agrovetInfo->agrovet_type ?? 'Standard Agrovet'
                    ];
                }
        // Perform creditworthiness assessment
        $assessment = calculateInputCreditworthiness($app, $farmer_id, $total_amount);
        $creditworthinessScore = $assessment['final_score'];
        
        // Determine application status based on score
        $status = '';
        $description = '';
        $action_type = '';
        
        if ($creditworthinessScore >= 70) {
            $status = 'under_review';
            $description = "Application passed initial creditworthiness screening with score " . 
                          $creditworthinessScore . ". Moved to agrovet staff review.";
            $action_type = 'creditworthiness_check';
        } else if ($creditworthinessScore >= 50) {
            $status = 'pending';
            $description = "Application requires additional review due to creditworthiness score " . 
                          $creditworthinessScore . ". Held for detailed assessment.";
            $action_type = 'creditworthiness_check';
        } else {
            $status = 'rejected';
            $description = "Application automatically rejected due to low creditworthiness score " . 
                          $creditworthinessScore . ".";
            $action_type = 'auto_rejected';
        }
        
        // Continue to Part 3: Database Insertion
        // Part 3: Database Insertion
        
        // 1. Insert the main application record
        $applicationQuery = "INSERT INTO input_credit_applications (
            farmer_id,
            agrovet_id,
            total_amount,
            credit_percentage,
            total_with_interest,
            repayment_percentage,
            application_date,
            status,
            creditworthiness_score,
            created_at,
            updated_at
        ) VALUES (
            :farmer_id,
            :agrovet_id,
            :total_amount,
            :credit_percentage,
            :total_with_interest,
            :repayment_percentage,
            NOW(),
            :status,
            :creditworthiness_score,
            NOW(),
            NOW()
        )";

        $applicationParams = [
            ':farmer_id' => $farmer_id,
            ':agrovet_id' => $agrovet_id,
            ':total_amount' => $total_amount,
            ':credit_percentage' => $credit_percentage,
            ':total_with_interest' => $total_with_interest,
            ':repayment_percentage' => $repayment_percentage,
            ':status' => $status,
            ':creditworthiness_score' => $creditworthinessScore
        ];

        $app->insertWithoutPath($applicationQuery, $applicationParams);
        $application_id = $app->lastInsertId();
        
        // 2. Insert each selected input item
        foreach ($selected_inputs as $item) {
            $itemQuery = "INSERT INTO input_credit_items (
                credit_application_id,
                input_catalog_id,
                input_type,
                input_name,
                quantity,
                unit,
                unit_price,
                total_price,
                description,
                created_at
            ) VALUES (
                :credit_application_id,
                :input_catalog_id,
                :input_type,
                :input_name,
                :quantity,
                :unit,
                :unit_price,
                :total_price,
                :description,
                NOW()
            )";

            $itemParams = [
                ':credit_application_id' => $application_id,
                ':input_catalog_id' => $item['id'],
                ':input_type' => $item['type'],
                ':input_name' => $item['name'],
                ':quantity' => $item['quantity'],
                ':unit' => $item['unit'],
                ':unit_price' => $item['price'],
                ':total_price' => $item['total'],
                ':description' => $item['description'] ?? null
            ];

            $app->insertWithoutPath($itemQuery, $itemParams);
        }
        
        // 3. Create initial input credit log entry
        $initialLogQuery = "INSERT INTO input_credit_logs (
            input_credit_application_id,
            user_id,
            action_type,
            description,
            created_at
        ) VALUES (
            :input_credit_application_id,
            :user_id,
            'application_submitted',
            :description,
            NOW()
        )";
        
        $initialLogParams = [
            ':input_credit_application_id' => $application_id,
            ':user_id' => $_SESSION['user_id'],
            ':description' => "Input credit application submitted. Total amount: KES " . 
                             number_format($total_amount, 2) . 
                             ", Credit percentage: " . $credit_percentage . "%, " .
                             "Repayment percentage: " . $repayment_percentage . "%"
        ];
        
        $app->insertWithoutPath($initialLogQuery, $initialLogParams);
        
        // 4. Add creditworthiness assessment log
        $creditLogQuery = "INSERT INTO input_credit_logs (
            input_credit_application_id,
            user_id,
            action_type,
            description,
            created_at
        ) VALUES (
            :input_credit_application_id,
            :user_id,
            :action_type,
            :description,
            NOW()
        )";
        
        $detailedAssessment = "Creditworthiness assessment: " .
                             "Input repayment history score: " . $assessment['scores']['input_repayment_history'] . ", " .
                             "Financial obligations score: " . $assessment['scores']['financial_obligations'] . ", " .
                             "Produce history score: " . $assessment['scores']['produce_history'] . ", " .
                             "Amount ratio score: " . $assessment['scores']['amount_ratio'] . ". " .
                             "Final score: " . $creditworthinessScore;
        
        $creditLogParams = [
            ':input_credit_application_id' => $application_id,
            ':user_id' => $_SESSION['user_id'],
            ':action_type' => 'creditworthiness_check',
            ':description' => $detailedAssessment
        ];
        
        $app->insertWithoutPath($creditLogQuery, $creditLogParams);
        
        // 5. Add status update log if not a duplicate of the creditworthiness check
        if ($action_type != 'creditworthiness_check') {
            $statusLogQuery = "INSERT INTO input_credit_logs (
                input_credit_application_id,
                user_id,
                action_type,
                description,
                created_at
            ) VALUES (
                :input_credit_application_id,
                :user_id,
                :action_type,
                :description,
                NOW()
            )";
            
            $statusLogParams = [
                ':input_credit_application_id' => $application_id,
                ':user_id' => $_SESSION['user_id'],
                ':action_type' => $action_type,
                ':description' => $description
            ];
            
            $app->insertWithoutPath($statusLogQuery, $statusLogParams);
        }
        
        // 6. If auto-rejected, update application with rejection reason
        if ($status == 'rejected') {
            $rejectionQuery = "UPDATE input_credit_applications 
                              SET rejection_reason = :rejection_reason 
                              WHERE id = :application_id";
                              
            $app->updateToken($rejectionQuery, [
                ':rejection_reason' => "Automatically rejected due to low creditworthiness score of " . $creditworthinessScore,
                ':application_id' => $application_id
            ]);
        }
        
        // 7. If approved, create approved input credit record
        if ($status == 'approved') {
            $approvedCreditQuery = "INSERT INTO approved_input_credits (
                credit_application_id,
                approved_amount,
                credit_percentage,
                total_with_interest,
                repayment_percentage,
                remaining_balance,
                approved_by,
                approval_date,
                status,
                created_at,
                updated_at
            ) VALUES (
                :credit_application_id,
                :approved_amount,
                :credit_percentage,
                :total_with_interest,
                :repayment_percentage,
                :remaining_balance,
                :approved_by,
                NOW(),
                'pending_fulfillment',
                NOW(),
                NOW()
            )";
            
            $approvedCreditParams = [
                ':credit_application_id' => $application_id,
                ':approved_amount' => $total_amount,
                ':credit_percentage' => $credit_percentage,
                ':total_with_interest' => $total_with_interest,
                ':repayment_percentage' => $repayment_percentage,
                ':remaining_balance' => $total_with_interest,
                ':approved_by' => $_SESSION['user_id']
            ];
            
            $app->insertWithoutPath($approvedCreditQuery, $approvedCreditParams);
        }
        
        // Continue to Part 4: Finalization and Response
        // Part 4: Finalization and Response
        
        // 1. Record activity in activity_logs
        $activityQuery = "INSERT INTO activity_logs (
            user_id,
            activity_type,
            description,
            created_at
        ) VALUES (
            :user_id,
            'input_credit_application',
            :description,
            NOW()
        )";

        $activityParams = [
            ':user_id' => $_SESSION['user_id'],
            ':description' => "Input credit application submitted - Ref: " . $reference_number . 
                             " - Status: " . ucfirst($status)
        ];

        $app->insertWithoutPath($activityQuery, $activityParams);

        // 2. Record audit information in audit_logs
        $auditQuery = "INSERT INTO audit_logs (
            user_id,
            action_type,
            table_name,
            record_id,
            new_values,
            created_at
        ) VALUES (
            :user_id,
            'create',
            'input_credit_applications',
            :record_id,
            :new_values,
            NOW()
        )";

        $auditValues = [
            'farmer_id' => $farmer_id,
            'agrovet_id' => $agrovet_id,
            'total_amount' => $total_amount,
            'credit_percentage' => $credit_percentage,
            'total_with_interest' => $total_with_interest,
            'repayment_percentage' => $repayment_percentage,
            'status' => $status,
            'creditworthiness_score' => $creditworthinessScore
        ];

        $auditParams = [
            ':user_id' => $_SESSION['user_id'],
            ':record_id' => $application_id,
            ':new_values' => json_encode($auditValues)
        ];

        $app->insertWithoutPath($auditQuery, $auditParams);
        // Get financial snapshot and agrovet information
      $financialSnapshot = getInputCreditFinancialSnapshot($app, $farmer_id);
      $agrovetInformation = getAgrovetInformation($app, $agrovet_id);


        // 3. Commit transaction if everything is successful
        $app->commit();
        
        // 4. Prepare response message based on application status
        $message = '';
        if ($status == 'approved') {
            $message = 'Your input credit application has been automatically approved. The agrovet will contact you soon regarding fulfillment.';
        } else if ($status == 'under_review') {
            $message = 'Your input credit application has been submitted successfully and is now under review by the agrovet.';
        } else if ($status == 'pending') {
            $message = 'Your input credit application has been submitted and requires additional review. You will be notified of any updates.';
        } else if ($status == 'rejected') {
            $message = 'Your input credit application has been automatically declined due to creditworthiness criteria. Please contact support for more information.';
        } else {
            $message = 'Your input credit application has been submitted successfully.';
        }
        ob_end_clean();
     

              // Enhanced response with all data the status tab needs
              echo json_encode([
                  'success' => true,
                  'message' => $message,
                  'application_id' => $application_id,
                  'reference_number' => $reference_number,
                  'status' => $status,
                  'provider_type' => 'agrovet', // Distinguish from bank loans
                  'agrovet_information' => $agrovetInformation,
                  'assessment' => [
                      'score' => $creditworthinessScore,
                      'details' => $assessment['scores'],
                      'status_description' => $description
                  ],
                  'financial_snapshot' => $financialSnapshot,
                  'credit_summary' => [
                      'total_amount' => $total_amount,
                      'credit_percentage' => $credit_percentage,
                      'total_with_interest' => $total_with_interest,
                      'repayment_percentage' => $repayment_percentage,
                      'items_count' => count($selected_inputs)
                  ],
                  'agrovet_processing' => [
                      'expected_review_time' => '24-48 hours',
                      'contact_info' => 'The agrovet may contact you directly for input preparation',
                      'next_steps' => [
                          'Agrovet will review your creditworthiness assessment',
                          'Input items will be prepared for collection',
                          'You will be notified when items are ready',
                          'Credit repayment will begin with your next produce delivery'
                      ]
                  ],
                  'recommendations' => [
                      'maintain_deliveries' => 'Continue consistent produce deliveries for repayment',
                      'collection_readiness' => 'Be ready to collect inputs when notified',
                      'contact_availability' => 'Ensure you are available for agrovet contact'
                  ]
              ]);
              
                  } catch (Exception $e) {
                      // Rollback on error
                      $app->rollBack();
                      // Log error
                      // error_log('Input credit application error: ' . $e->getMessage());
                      
                      // Return error response
                      echo json_encode([
            'success' => false,
            'message' => 'Error submitting input credit application: ' . $e->getMessage()
        ]);
    }
} else {
    // Return error for invalid request method
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>