<?php
include "../../config/config.php";
include "../../libs/App.php";

// Initialize response array
$events = [];

if(isset($_SESSION['user_id'])) {
    $app = new App;
    $userId = $_SESSION['user_id'];
    
    // Get the agrovet ID for the logged-in staff
    $staffQuery = "SELECT agrovet_id FROM agrovet_staff WHERE user_id = $userId";
    $staffResult = $app->select_one($staffQuery);
    
    if($staffResult) {
        $agrovetId = $staffResult->agrovet_id;
        
        // Get all active input credits issued by this agrovet
        $activeCreditQuery = "SELECT 
            aic.id AS credit_id,
            aic.credit_application_id,
            ica.farmer_id,
            f.registration_number AS farmer_reg,
            CONCAT(u.first_name, ' ', u.last_name) AS farmer_name,
            aic.approved_amount,
            aic.credit_percentage,
            aic.total_with_interest,
            aic.remaining_balance,
            aic.repayment_percentage,
            aic.fulfillment_date,
            aic.status
        FROM 
            approved_input_credits aic
        JOIN 
            input_credit_applications ica ON aic.credit_application_id = ica.id
        JOIN 
            farmers f ON ica.farmer_id = f.id
        JOIN 
            users u ON f.user_id = u.id
        WHERE 
            ica.agrovet_id = $agrovetId AND aic.status IN ('active', 'pending_fulfillment')";
        
        $activeCredits = $app->select_all($activeCreditQuery);
        
        // Get existing input credit repayments
        $repaymentsQuery = "SELECT 
            icr.id,
            icr.approved_credit_id,
            icr.produce_delivery_id,
            icr.produce_sale_amount,
            icr.deducted_amount,
            icr.amount,
            icr.deduction_date,
            icr.notes
        FROM 
            input_credit_repayments icr
        JOIN 
            approved_input_credits aic ON icr.approved_credit_id = aic.id
        JOIN 
            input_credit_applications ica ON aic.credit_application_id = ica.id
        WHERE 
            ica.agrovet_id = $agrovetId";
        
        $repayments = $app->select_all($repaymentsQuery);
        $repaymentsByCreditId = [];
        
        // Organize repayments by credit_id for easy lookup
        if($repayments) {
            foreach($repayments as $repayment) {
                if(!isset($repaymentsByCreditId[$repayment->approved_credit_id])) {
                    $repaymentsByCreditId[$repayment->approved_credit_id] = [];
                }
                $repaymentsByCreditId[$repayment->approved_credit_id][] = $repayment;
            }
        }
        
        // For active credits, add events for expected repayments
        if($activeCredits) {
            foreach($activeCredits as $credit) {
                $creditId = $credit->credit_id;
                $creditRef = 'INPCR' . str_pad($credit->credit_application_id, 5, '0', STR_PAD_LEFT);
                $startDate = new DateTime($credit->fulfillment_date);
                $today = new DateTime();
                
                // Calculate expected repayment schedule (every 30 days for 6 months)
                // This is an estimate since actual repayments depend on produce sales
                $expectedMonthlyPayment = $credit->total_with_interest / 6; // Assuming 6 months for repayment
                
                // Add fulfillment event
                $events[] = [
                    'id' => "credit-fulfillment-{$creditId}",
                    'title' => "Credit Issued: {$credit->farmer_name}",
                    'start' => $startDate->format('Y-m-d'),
                    'color' => '#6f42c1', // purple
                    'className' => "credit-issued",
                    'extendedProps' => [
                        'type' => 'fulfillment',
                        'creditId' => $creditId,
                        'creditReference' => $creditRef,
                        'farmerName' => $credit->farmer_name,
                        'farmerReg' => $credit->farmer_reg,
                        'amount' => number_format($credit->approved_amount, 2),
                        'totalWithInterest' => number_format($credit->total_with_interest, 2),
                        'tooltip' => "Credit Issued to {$credit->farmer_name}: KES " . number_format($credit->approved_amount, 2)
                    ]
                ];
                
                // Generate expected repayment dates (estimates)
                for($i = 1; $i <= 6; $i++) {
                    $paymentDate = clone $startDate;
                    $paymentDate->modify("+{$i}0 days"); // Every 30 days
                    
                    // Check if repayment already happened
                    $status = 'expected';
                    $actualPaymentInfo = null;
                    
                    if(isset($repaymentsByCreditId[$creditId])) {
                        foreach($repaymentsByCreditId[$creditId] as $repayment) {
                            $repaymentDate = new DateTime($repayment->deduction_date);
                            
                            // Consider a payment within 15 days as matching this estimated payment
                            $dateDiff = abs($paymentDate->diff($repaymentDate)->days);
                            if($dateDiff <= 15) {
                                $status = 'completed';
                                $actualPaymentInfo = $repayment;
                                break;
                            }
                        }
                    }
                    
                    // If credit is fully repaid, don't show future expected payments
                    if($credit->remaining_balance <= 0 && $status !== 'completed') {
                        continue;
                    }
                    
                    // If not completed and date is in the past, mark as pending
                    if($status !== 'completed' && $paymentDate < $today) {
                        $status = 'pending';
                    }
                    
                    // Color coding based on status
                    $eventColor = '';
                    switch($status) {
                        case 'completed':
                            $eventColor = '#198754'; // green
                            break;
                        case 'expected':
                            $eventColor = '#fd7e14'; // orange
                            break;
                        case 'pending':
                            $eventColor = '#ffc107'; // yellow
                            break;
                    }
                    
                    // Skip future payments if credit is completed
                    if($credit->status === 'completed' && $status !== 'completed') {
                        continue;
                    }
                    
                    // Only include the event if it's relevant
                    if($status === 'completed' || $credit->status === 'active') {
                        // Create event for this payment
                        $events[] = [
                            'id' => "credit-payment-{$creditId}-{$i}",
                            'title' => "{$credit->farmer_name}: Expected Repayment",
                            'start' => $paymentDate->format('Y-m-d'),
                            'color' => $eventColor,
                            'className' => "payment-{$status}",
                            'extendedProps' => [
                                'type' => 'payment',
                                'status' => $status,
                                'creditId' => $creditId,
                                'creditReference' => $creditRef,
                                'farmerName' => $credit->farmer_name,
                                'farmerReg' => $credit->farmer_reg,
                                'estimatedAmount' => number_format($expectedMonthlyPayment, 2),
                                'tooltip' => "Expected Repayment from {$credit->farmer_name}: ~KES " . number_format($expectedMonthlyPayment, 2) . " (" . ucfirst($status) . ")"
                            ]
                        ];
                    }
                }
                
                // Add actual repayment events from database
                if(isset($repaymentsByCreditId[$creditId])) {
                    foreach($repaymentsByCreditId[$creditId] as $repayment) {
                        $repaymentDate = new DateTime($repayment->deduction_date);
                        
                        // Only add if not already captured in expected payments
                        $isDuplicate = false;
                        foreach($events as $event) {
                            if(isset($event['extendedProps']['type']) && 
                               $event['extendedProps']['type'] === 'payment' &&
                               $event['extendedProps']['creditId'] === $creditId &&
                               $event['extendedProps']['status'] === 'completed' &&
                               abs(strtotime($event['start']) - $repaymentDate->getTimestamp()) < 15 * 24 * 60 * 60) {
                                $isDuplicate = true;
                                break;
                            }
                        }
                        
                        if(!$isDuplicate) {
                            $events[] = [
                                'id' => "credit-actual-payment-{$repayment->id}",
                                'title' => "{$credit->farmer_name}: Repayment Received",
                                'start' => $repaymentDate->format('Y-m-d'),
                                'color' => '#198754', // green
                                'className' => "payment-completed",
                                'extendedProps' => [
                                    'type' => 'actual-payment',
                                    'status' => 'completed',
                                    'creditId' => $creditId,
                                    'creditReference' => $creditRef,
                                    'farmerName' => $credit->farmer_name,
                                    'farmerReg' => $credit->farmer_reg,
                                    'repaymentId' => $repayment->id,
                                    'amount' => number_format($repayment->amount, 2),
                                    'deductionPercent' => $credit->repayment_percentage,
                                    'produceSaleAmount' => number_format($repayment->produce_sale_amount, 2),
                                    'tooltip' => "Repayment from {$credit->farmer_name}: KES " . number_format($repayment->amount, 2)
                                ]
                            ];
                        }
                    }
                }
            }
        }
    }
}

// Return events as JSON
header('Content-Type: application/json');
echo json_encode($events);
?>