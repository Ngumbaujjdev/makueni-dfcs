<?php
include "../../config/config.php";
include "../../libs/App.php";

// Check if repayment ID is provided
if (!isset($_POST['repaymentId']) || empty($_POST['repaymentId'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Repayment ID is required'
    ]);
    exit;
}

$repaymentId = intval($_POST['repaymentId']); // Ensure integer
$app = new App();

try {
    // First, get the basic repayment information
    $query1 = "SELECT * FROM input_credit_repayments WHERE id = :repayment_id";
    $repaymentBasic = $app->selectOne($query1, [':repayment_id' => $repaymentId]);
    
    if (!$repaymentBasic) {
        echo json_encode([
            'success' => false,
            'message' => 'Repayment ID ' . $repaymentId . ' not found'
        ]);
        exit;
    }
    
    // Second, get the approved credit details
    $query2 = "SELECT * FROM approved_input_credits WHERE id = :approved_credit_id";
    $approvedCredit = $app->selectOne($query2, [':approved_credit_id' => $repaymentBasic->approved_credit_id]);
    
    if (!$approvedCredit) {
        echo json_encode([
            'success' => false,
            'message' => 'Approved credit ID ' . $repaymentBasic->approved_credit_id . ' not found'
        ]);
        exit;
    }
    
    // Third, get the credit application details
    $query3 = "SELECT * FROM input_credit_applications WHERE id = :credit_application_id";
    $creditApplication = $app->selectOne($query3, [':credit_application_id' => $approvedCredit->credit_application_id]);
    
    if (!$creditApplication) {
        echo json_encode([
            'success' => false,
            'message' => 'Credit application ID ' . $approvedCredit->credit_application_id . ' not found'
        ]);
        exit;
    }
    
    // Fourth, get the farmer details
    $query4 = "SELECT f.*, CONCAT(u.first_name, ' ', u.last_name) as farmer_name, u.phone, u.email 
               FROM farmers f
               JOIN users u ON f.user_id = u.id
               WHERE f.id = :farmer_id";
    $farmer = $app->selectOne($query4, [':farmer_id' => $creditApplication->farmer_id]);
    
    if (!$farmer) {
        echo json_encode([
            'success' => false,
            'message' => 'Farmer ID ' . $creditApplication->farmer_id . ' not found'
        ]);
        exit;
    }
    
    // Fifth, get the agrovet details
    $query5 = "SELECT * FROM agrovets WHERE id = :agrovet_id";
    $agrovet = $app->selectOne($query5, [':agrovet_id' => $creditApplication->agrovet_id]);
    
    // Now, get the produce delivery details if available
    $produceDelivery = null;
    if ($repaymentBasic->produce_delivery_id) {
        $query6 = "SELECT pd.*, pt.name as produce_type FROM produce_deliveries pd
                  LEFT JOIN farm_products fp ON pd.farm_product_id = fp.id
                  LEFT JOIN product_types pt ON fp.product_type_id = pt.id
                  WHERE pd.id = :produce_id";
        $produceDelivery = $app->selectOne($query6, [':produce_id' => $repaymentBasic->produce_delivery_id]);
    }
    
    // Get input credit items
    $itemsQuery = "SELECT * FROM input_credit_items 
                  WHERE credit_application_id = '{$approvedCredit->credit_application_id}'";
    $items = $app->select_all($itemsQuery);
    
    // Build the response object
    $response = [
        'id' => $repaymentBasic->id,
        'approved_credit_id' => $repaymentBasic->approved_credit_id,
        'produce_delivery_id' => $repaymentBasic->produce_delivery_id,
        'amount' => $repaymentBasic->amount,
        'payment_date' => $repaymentBasic->deduction_date,
        'payment_method' => 'produce_deduction',
        'notes' => $repaymentBasic->notes,
        'created_at' => $repaymentBasic->created_at,
        'credit_application_id' => $approvedCredit->credit_application_id,
        'approved_amount' => $approvedCredit->approved_amount,
        'credit_percentage' => $approvedCredit->credit_percentage,
        'total_with_interest' => $approvedCredit->total_with_interest,
        'repayment_percentage' => $approvedCredit->repayment_percentage,
        'remaining_balance' => $approvedCredit->remaining_balance,
        'balance_before' => floatval($approvedCredit->remaining_balance) + floatval($repaymentBasic->amount),
        'fulfillment_date' => $approvedCredit->fulfillment_date,
        'credit_status' => $approvedCredit->status,
        'farmer_id' => $creditApplication->farmer_id,
        'agrovet_id' => $creditApplication->agrovet_id,
        'application_status' => $creditApplication->status,
        'requested_amount' => $creditApplication->total_amount,
        'farmer_name' => $farmer->farmer_name,
        'farmer_reg' => $farmer->registration_number,
        'farmer_phone' => $farmer->phone,
        'farmer_email' => $farmer->email,
        'agrovet_name' => $agrovet ? $agrovet->name : null,
        'items' => $items ?: []
    ];
    
    // Add produce details if available
    if ($produceDelivery) {
        $response['produce_quantity'] = $produceDelivery->quantity;
        $response['produce_unit_price'] = $produceDelivery->unit_price;
        $response['produce_total_value'] = $produceDelivery->total_value;
        $response['produce_quality'] = $produceDelivery->quality_grade;
        $response['produce_type'] = $produceDelivery->produce_type;
    }
    
    // Return the repayment details as JSON
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching repayment details: ' . $e->getMessage()
    ]);
}
?>