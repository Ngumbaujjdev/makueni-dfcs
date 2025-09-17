<?php
include "../../config/config.php";
include "../../libs/App.php";

// Initialize response array
$events = [];

if(isset($_SESSION['user_id'])) {
    $app = new App;
    $userId = $_SESSION['user_id'];
    
    // Get the farmer's ID from the user ID
    $farmerQuery = "SELECT id FROM farmers WHERE user_id = $userId";
    $farmerResult = $app->select_one($farmerQuery);
    
    if($farmerResult) {
        $farmerId = $farmerResult->id;
        
        // Step 1: Get all active bank loans
        $activeLoansQuery = "SELECT 
            al.id AS loan_id,
            al.loan_application_id,
            la.farmer_id,
            lt.name AS loan_type,
            b.name AS bank_name,
            al.approved_amount,
            al.approved_term,
            al.interest_rate,
            al.total_repayment_amount,
            al.remaining_balance,
            al.disbursement_date,
            al.expected_completion_date,
            al.status
        FROM 
            approved_loans al
        JOIN 
            loan_applications la ON al.loan_application_id = la.id
        JOIN 
            loan_types lt ON la.loan_type_id = lt.id
        JOIN
            banks b ON la.bank_id = b.id
        WHERE 
            la.farmer_id = $farmerId 
            AND la.provider_type = 'bank'
            AND al.status IN ('active', 'pending_disbursement', 'completed')";
        
        $activeLoans = $app->select_all($activeLoansQuery);
        
        // Check if activeLoans is valid
        if (!$activeLoans) {
            $activeLoans = [];
        }
        
        // Step 2: Get existing bank loan repayments
        $repaymentsQuery = "SELECT 
            lr.id,
            lr.approved_loan_id,
            lr.produce_delivery_id,
            lr.amount,
            lr.payment_date,
            lr.payment_method,
            lr.notes
        FROM 
            loan_repayments lr
        JOIN 
            approved_loans al ON lr.approved_loan_id = al.id
        JOIN 
            loan_applications la ON al.loan_application_id = la.id
        WHERE 
            la.farmer_id = $farmerId
            AND la.provider_type = 'bank'";
        
        $repayments = $app->select_all($repaymentsQuery);
        
        // Check if repayments is valid
        if (!$repayments) {
            $repayments = [];
        }
        
        $repaymentsByLoanId = [];
        
        // Only organize repayments if there are any
        if (count($repayments) > 0) {
            // Organize repayments by loan_id for easy lookup
            foreach($repayments as $repayment) {
                if(!isset($repaymentsByLoanId[$repayment->approved_loan_id])) {
                    $repaymentsByLoanId[$repayment->approved_loan_id] = [];
                }
                $repaymentsByLoanId[$repayment->approved_loan_id][] = $repayment;
            }
        }
        
        // Only process loans if there are any
        if (count($activeLoans) > 0) {
            // Step 3: Generate events for each bank loan's repayment schedule
            foreach($activeLoans as $loan) {
                $loanId = $loan->loan_id;
                $loanReference = 'BANK' . str_pad($loan->loan_application_id, 5, '0', STR_PAD_LEFT);
                $monthlyAmount = $loan->total_repayment_amount / $loan->approved_term;
                $disbursementDate = new DateTime($loan->disbursement_date);
                $today = new DateTime();
                
                // Generate payment events for each month of the loan term
                for($i = 1; $i <= $loan->approved_term; $i++) {
                    // Calculate payment date (same day each month)
                    $paymentDate = clone $disbursementDate;
                    $paymentDate->modify("+$i months");
                    
                    // Format for calendar
                    $formattedDate = $paymentDate->format('Y-m-d');
                    
                    // Check if this payment has been made (exists in repayments)
                    $status = 'upcoming';
                    $actualPaymentDate = null;
                    $paymentMethod = null;
                    $paymentNotes = null;
                    
                    // Look for this payment in actual repayments
                    if(isset($repaymentsByLoanId[$loanId])) {
                        foreach($repaymentsByLoanId[$loanId] as $repayment) {
                            $repaymentDate = new DateTime($repayment->payment_date);
                            
                            // Simplified matching - consider a payment within 15 days before or after scheduled date as matching
                            $dateDiff = abs($paymentDate->diff($repaymentDate)->days);
                            if($dateDiff <= 15) {
                                $status = 'completed';
                                $actualPaymentDate = $repayment->payment_date;
                                $paymentMethod = $repayment->payment_method;
                                $paymentNotes = $repayment->notes;
                                break;
                            }
                        }
                    }
                    
                    // If not completed and date is in the past, mark as overdue
                    if($status !== 'completed' && $paymentDate < $today) {
                        $status = 'overdue';
                    }
                    
                    // Color coding based on status
                    $eventColor = '';
                    switch($status) {
                        case 'completed':
                            $eventColor = '#6AA32D'; // green
                            break;
                        case 'upcoming':
                            $eventColor = '#FFC107'; // yellow
                            break;
                        case 'overdue':
                            $eventColor = '#DC3545'; // red
                            break;
                    }
                    
                    // Create event for this payment
                    $events[] = [
                        'id' => "bank-payment-{$loanId}-{$i}",
                        'title' => "Bank Payment #$i: KES " . number_format($monthlyAmount, 2),
                        'start' => $formattedDate,
                        'color' => $eventColor,
                        'className' => "payment-{$status}",
                        'extendedProps' => [
                            'status' => $status,
                            'loanId' => $loanId,
                            'loanReference' => $loanReference,
                            'loanType' => $loan->loan_type,
                            'bankName' => $loan->bank_name,
                            'amount' => number_format($monthlyAmount, 2),
                            'paymentNumber' => $i,
                            'actualPaymentDate' => $actualPaymentDate,
                            'paymentMethod' => $paymentMethod,
                            'notes' => $paymentNotes,
                            'tooltip' => "Bank Payment #$i: KES " . number_format($monthlyAmount, 2) . " (" . ucfirst($status) . ")"
                        ]
                    ];
                }
            }
        }
        
        // Ensure that we always return a valid JSON array, even if empty
        echo json_encode($events ?? []);
    } else {
        // Return empty array if no farmer found
        echo json_encode([]);
    }
} else {
    // Return empty array if user not logged in
    echo json_encode([]);
}

?>