<?php
include "../../config/config.php";
include "../../libs/App.php";

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

       // Get farmer details from session user_id
    $query = "SELECT u.*, f.id as farmer_id, f.registration_number, f.category_id, fc.name as category_name
              FROM users u
              LEFT JOIN farmers f ON u.id = f.user_id
              LEFT JOIN farmer_categories fc ON f.category_id = fc.id
              WHERE u.id = :user_id";
    
    $farmer = $app->selectOne($query, [':user_id' => $_SESSION['user_id']]);
    
    if (!$farmer || !$farmer->farmer_id) {
        throw new Exception("Farmer record not found for this user");
    }
    
    // Set the farmer_id and provider_type
    $farmer_id = $farmer->farmer_id;
    $provider_type = 'sacco'; // Always using SACCO as the provider
    
    // Continue with the rest of your loan application processing...
    $loan_type_id = $_POST['loan_type_id'];
    $amount_requested = $_POST['amount_requested'];
    $term_requested = $_POST['term_requested'];
    $purpose = $_POST['purpose'];
     
        $reference_number = $_POST['reference_number'];
        
        // Get farmer and loan type details
        $farmerQuery = "SELECT f.id, f.user_id, f.registration_number, u.first_name, u.last_name
                       FROM farmers f 
                       JOIN users u ON f.user_id = u.id 
                       WHERE f.id = :farmer_id";
        $farmer = $app->selectOne($farmerQuery, [':farmer_id' => $farmer_id]);
        
        $loanTypeQuery = "SELECT * FROM loan_types WHERE id = :loan_type_id";
        $loanType = $app->selectOne($loanTypeQuery, [':loan_type_id' => $loan_type_id]);
        
        // Validate loan amount and term against loan type limits
        if ($amount_requested < $loanType->min_amount || $amount_requested > $loanType->max_amount) {
            throw new Exception("Requested amount outside permitted range");
                             }
                             
                             if ($term_requested < $loanType->min_term || $term_requested > $loanType->max_term) {
                                 throw new Exception("Requested term outside permitted range");
                             }
                             // loan credit worthiness check
                             // Define creditworthiness assessment functions
                                 function calculateCreditworthiness($app, $farmer_id, $amount_requested) {
                                     // Initialize scores array
                                     $scores = [
                                         'repayment_history' => 0,
                                         'financial_obligations' => 0,
                                         'produce_history' => 0,
                                         'amount_ratio' => 0
                                     ];
                                     
                                     // 1. Check past loan repayment history (30% weight)
                                     $repaymentScore = checkLoanRepaymentHistory($app, $farmer_id);
                                     $scores['repayment_history'] = $repaymentScore;
                                     
                                     // 2. Check current financial obligations (25% weight)
                                     $obligationsScore = checkFinancialObligations($app, $farmer_id, $amount_requested);
                                     $scores['financial_obligations'] = $obligationsScore;
                                     
                                     // 3. Check produce delivery history and value (35% weight)
                                     $produceScore = checkProduceHistory($app, $farmer_id);
                                     $scores['produce_history'] = $produceScore;
                                     
                                     // 4. Check loan amount to average monthly produce value ratio (10% weight)
                                     $amountRatioScore = checkLoanAmountRatio($app, $farmer_id, $amount_requested);
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
                                 function calculateCreditCapacity($deliveriesValue, $activeLoansBalance, $inputCreditsBalance) {
                                    if ($deliveriesValue <= 0) return 0;
                                    
                                    $totalDebt = $activeLoansBalance + $inputCreditsBalance;
                                    $monthlyIncome = $deliveriesValue / 6; // 6 months average
                                    $debtToIncomeRatio = $totalDebt / ($monthlyIncome * 6);
                                    
                                    // Return available capacity as percentage (higher is better)
                                    $capacity = max(0, 100 - ($debtToIncomeRatio * 100));
                                    return min(100, $capacity);
                                }
                                function getFinancialSnapshot($app, $farmer_id) {
                                    // Get active loans count and balance
                                    $activeLoansQuery = "SELECT COUNT(*) as count, COALESCE(SUM(remaining_balance), 0) as balance 
                                                        FROM approved_loans al 
                                                        JOIN loan_applications la ON al.loan_application_id = la.id 
                                                        WHERE la.farmer_id = :farmer_id AND al.status = 'active'";
                                    $activeLoans = $app->selectOne($activeLoansQuery, [':farmer_id' => $farmer_id]);
                                    
                                    // Get input credits count and balance  
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
                                    
                                    return [
                                        'active_loans_count' => $activeLoans->count,
                                        'active_loans_balance' => $activeLoans->balance,
                                        'input_credits_count' => $inputCredits->count, 
                                        'input_credits_balance' => $inputCredits->balance,
                                        'deliveries_count' => $deliveries->count,
                                        'deliveries_value' => $deliveries->value,
                                        'credit_capacity' => calculateCreditCapacity($deliveries->value, $activeLoans->balance, $inputCredits->balance)
                                    ];
                                }
                                 
                                function checkLoanRepaymentHistory($app, $farmer_id) {
                                         // Check if farmer has any past loans
                                         $pastLoansQuery = "SELECT COUNT(*) as loan_count
                                                           FROM approved_loans al
                                                           JOIN loan_applications la ON al.loan_application_id = la.id
                                                           WHERE la.farmer_id = :farmer_id
                                                           AND al.status IN ('completed', 'defaulted')";
                                         
                                         $pastLoans = $app->selectOne($pastLoansQuery, [':farmer_id' => $farmer_id]);
                                         
                                         // If no past loans, give a neutral score of 70
                                         if ($pastLoans->loan_count == 0) {
                                             return 70;
                                         }
                                         
                                         // Check completed loans vs defaulted loans
                                         $loanRatioQuery = "SELECT 
                                                           SUM(CASE WHEN al.status = 'completed' THEN 1 ELSE 0 END) as completed_count,
                                                           SUM(CASE WHEN al.status = 'defaulted' THEN 1 ELSE 0 END) as defaulted_count
                                                           FROM approved_loans al
                                                           JOIN loan_applications la ON al.loan_application_id = la.id
                                                           WHERE la.farmer_id = :farmer_id
                                                           AND al.status IN ('completed', 'defaulted')";
                                         
                                         $loanRatio = $app->selectOne($loanRatioQuery, [':farmer_id' => $farmer_id]);
                                         
                                         // Calculate repayment score based on completed vs defaulted ratio
                                         $totalLoans = $loanRatio->completed_count + $loanRatio->defaulted_count;
                                         if ($totalLoans == 0) return 70;
                                         
                                         $completionRate = ($loanRatio->completed_count / $totalLoans) * 100;
                                         
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
                                           $totalObligations = $outstandingLoans->outstanding_loans + $outstandingCredits->outstanding_credits + $amount_requested;
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
                                 
                                     function checkLoanAmountRatio($app, $farmer_id, $amount_requested) {
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
                                         
                                         // Calculate loan amount to average monthly produce ratio
                                         $ratio = $amount_requested / ($avgValue->avg_monthly_value * 6); // Compare to 6 months of produce
                                         
                                         // Score based on ratio
                                         if ($ratio <= 0.5) {
                                             return 100; // Excellent: loan is less than 50% of 6-month produce value
                                         } elseif ($ratio <= 1.0) {
                                             return 80; // Good: loan is 50-100% of 6-month produce value
                                         } elseif ($ratio <= 1.5) {
                                             return 60; // Fair: loan is 100-150% of 6-month produce value
                                         } elseif ($ratio <= 2.0) {
                                             return 40; // Poor: loan is 150-200% of 6-month produce value
                                         } else {
                                             return 20; // Very poor: loan exceeds 200% of 6-month produce value
                                         }
                                     }
                                    // insert the application with a pending status
                                    // Insert loan application
                                $loanQuery = "INSERT INTO loan_applications (
                                    farmer_id,
                                    provider_type,
                                    loan_type_id,
                                    bank_id,
                                    amount_requested,
                                    term_requested,
                                    purpose,
                                    application_date,
                                    status,
                                    created_at
                                ) VALUES (
                                    :farmer_id,
                                    :provider_type,
                                    :loan_type_id,
                                    NULL,
                                    :amount_requested,
                                    :term_requested,
                                    :purpose,
                                    NOW(),
                                    'pending',
                                    NOW()
                                )";
                        
                                $loanParams = [
                                    ':farmer_id' => $farmer_id,
                                    ':provider_type' => $provider_type,
                                    ':loan_type_id' => $loan_type_id,
                                    ':amount_requested' => $amount_requested,
                                    ':term_requested' => $term_requested,
                                    ':purpose' => $purpose
                                ];
                        
                                $app->insertWithoutPath($loanQuery, $loanParams);
                                $loan_id = $app->lastInsertId();
                                
                                // Add initial loan log entry
                                $logQuery = "INSERT INTO loan_logs (
                                    loan_application_id,
                                    user_id,
                                    action_type,
                                    description,
                                    created_at
                                ) VALUES (
                                    :loan_application_id,
                                    :user_id,
                                    'application_submitted',
                                    :description,
                                    NOW()
                                )";
                                
                                $logParams = [
                                    ':loan_application_id' => $loan_id,
                                    ':user_id' => $_SESSION['user_id'],
                                    ':description' => "Loan application submitted. Amount: KES " . 
                                                     number_format($amount_requested, 2) . 
                                                     ", Term: " . $term_requested . " months"
                                ];
                                
                                $app->insertWithoutPath($logQuery, $logParams);
                                  // get now the credit worthiness of the farmer
                                  // Perform creditworthiness assessment
                                  $assessment = calculateCreditworthiness($app, $farmer_id, $amount_requested);
                                  $creditworthinessScore = $assessment['final_score'];
                                  
                                  // Update loan application with creditworthiness score
                                  $updateScoreQuery = "UPDATE loan_applications 
                                                     SET creditworthiness_score = :score 
                                                     WHERE id = :loan_id";
                                                     
                                  $app->updateToken($updateScoreQuery, [
                                      ':score' => $creditworthinessScore,
                                      ':loan_id' => $loan_id
                                  ]);
                                  
                                  // Determine application status based on score
                                  $status = '';
                                  $description = '';
                                  $action_type = '';
                                  
                                  if ($creditworthinessScore >= 70) {
                                      $status = 'under_review';
                                      $description = "Application passed initial creditworthiness screening with score " . 
                                                    $creditworthinessScore . ". Moved to staff review.";
                                      $action_type = 'passed_initial_screening';
                                  } else if ($creditworthinessScore >= 50) {
                                      $status = 'pending';
                                      $description = "Application requires additional review due to creditworthiness score " . 
                                                    $creditworthinessScore . ". Held for detailed assessment.";
                                      $action_type = 'requires_additional_review';
                                  } else {
                                      $status = 'rejected';
                                      $description = "Application automatically rejected due to low creditworthiness score " . 
                                                    $creditworthinessScore . ".";
                                      $action_type = 'auto_rejected';
                                  }
                                  
                                  // Update loan application status
                                  $updateStatusQuery = "UPDATE loan_applications 
                                                      SET status = :status 
                                                      WHERE id = :loan_id";
                                                      
                                  $app->updateToken($updateStatusQuery, [
                                      ':status' => $status,
                                      ':loan_id' => $loan_id
                                  ]);
                                  
                                  // Add creditworthiness log entry
                                  $creditLogQuery = "INSERT INTO loan_logs (
                                      loan_application_id,
                                      user_id,
                                      action_type,
                                      description,
                                      created_at
                                  ) VALUES (
                                      :loan_application_id,
                                      :user_id,
                                      'creditworthiness_check',
                                      :description,
                                      NOW()
                                  )";
                                  
                                  $detailedAssessment = "Creditworthiness assessment: " .
                                                       "Repayment history score: " . $assessment['scores']['repayment_history'] . ", " .
                                                       "Financial obligations score: " . $assessment['scores']['financial_obligations'] . ", " .
                                                       "Produce history score: " . $assessment['scores']['produce_history'] . ", " .
                                                       "Amount ratio score: " . $assessment['scores']['amount_ratio'] . ". " .
                                                       "Final score: " . $creditworthinessScore;
                                  
                                  $creditLogParams = [
                                      ':loan_application_id' => $loan_id,
                                     ':user_id' => $_SESSION['user_id'],
                                      ':description' => $detailedAssessment
                                  ];
                                  
                                  $app->insertWithoutPath($creditLogQuery, $creditLogParams);
                                  
                                  // Add status update log
                                  $statusLogQuery = "INSERT INTO loan_logs (
                                      loan_application_id,
                                      user_id,
                                      action_type,
                                      description,
                                      created_at
                                  ) VALUES (
                                      :loan_application_id,
                                      :user_id,
                                      :action_type,
                                      :description,
                                      NOW()
                                  )";
                                  
                                  $statusLogParams = [
                                      ':loan_application_id' => $loan_id,
                                       ':user_id' => $_SESSION['user_id'],
                                      ':action_type' => $action_type,
                                      ':description' => $description
                                  ];
                                  
                                  $app->insertWithoutPath($statusLogQuery, $statusLogParams);
                                  // Add activity log
                                  $activityQuery = "INSERT INTO activity_logs (
                                      user_id,
                                      activity_type,
                                      description,
                                      created_at
                                  ) VALUES (
                                      :user_id,
                                      'loan_application',
                                      :description,
                                      NOW()
                                  )";
                          
                                  $activityParams = [
                                      ':user_id' => $_SESSION['user_id'],
                                      ':description' => "Loan application submitted - Ref: " . $reference_number . 
                                                       " - Status: " . ucfirst($status)
                                  ];
                          
                                  $app->insertWithoutPath($activityQuery, $activityParams);
                          
                                  // Add audit log
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
                      'loan_applications',
                      :record_id,
                      :new_values,
                      NOW()
                  )";
          
                  $auditValues = [
                      'farmer_id' => $farmer_id,
                      'provider_type' => $provider_type,
                      'loan_type_id' => $loan_type_id,
                      'amount_requested' => $amount_requested,
                      'term_requested' => $term_requested,
                      'purpose' => $purpose,
                      'status' => $status,
                      'creditworthiness_score' => $creditworthinessScore
                  ];
          
                  $auditParams = [
                      ':user_id' => $_SESSION['user_id'],
                      ':record_id' => $loan_id,
                      ':new_values' => json_encode($auditValues)
                  ];
          
                  $app->insertWithoutPath($auditQuery, $auditParams);
                  // Get financial snapshot data
                  $financialSnapshot = getFinancialSnapshot($app, $farmer_id);
          
                  // Commit transaction
                  $app->commit();
          
                   
                   // Enhanced response
                   echo json_encode([
                       'success' => true,
                       'message' => 'Loan application submitted successfully',
                       'status' => $status,
                       'loan_id' => $loan_id,
                       'reference_number' => $reference_number,
                       'assessment' => [
                           'score' => $creditworthinessScore,
                           'details' => $assessment['scores'],
                           'status_description' => $description
                       ],
                       'financial_snapshot' => $financialSnapshot  // NEW DATA
                   ]);
          
              } catch (Exception $e) {
                  // Rollback on error
                  $app->rollBack();
                  
                  echo json_encode([
                      'success' => false,
                      'message' => 'Error submitting loan application: ' . $e->getMessage()
                  ]);
              }
          } else {
              echo json_encode([
                  'success' => false,
                  'message' => 'Invalid request method'
              ]);
          }
          ?>