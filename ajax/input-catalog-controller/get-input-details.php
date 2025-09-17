<?php
include "../../config/config.php";
include "../../libs/App.php";

// Initialize response array
$response = [
    'success' => false,
    'message' => 'Invalid request',
    'input' => null
];

// Check if the request is a POST request with inputId
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inputId'])) {
    $app = new App;
    
    // Get session user_id to identify agrovet staff
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        $response['message'] = 'Not authenticated';
        echo json_encode($response);
        exit;
    }
    
    // Get the input ID from the request
    $inputId = intval($_POST['inputId']);
    
    if ($inputId <= 0) {
        $response['message'] = 'Invalid input ID';
        echo json_encode($response);
        exit;
    }
    
    // Get staff agrovet_id
    $staffQuery = "SELECT s.id as staff_id, s.agrovet_id 
                  FROM agrovet_staff s 
                  WHERE s.user_id = '{$userId}'";
    
    $staff = $app->select_one($staffQuery);
    
    if (!$staff) {
        $response['message'] = 'Staff information not found';
        echo json_encode($response);
        exit;
    }
    
    // Query to get detailed information about the input
    $query = "SELECT 
              ic.*,
              (SELECT COUNT(*) FROM input_credit_items ici WHERE ici.input_catalog_id = ic.id) as request_count,
              (
                SELECT COUNT(DISTINCT ica.id) 
                FROM input_credit_items ici 
                JOIN input_credit_applications ica ON ici.credit_application_id = ica.id 
                WHERE ici.input_catalog_id = ic.id AND ica.agrovet_id = {$staff->agrovet_id}
              ) as agrovet_request_count,
              (
                SELECT 
                  ROUND(
                    (COUNT(DISTINCT ica.id) / 
                     (SELECT COUNT(DISTINCT id) FROM input_credit_applications WHERE agrovet_id = {$staff->agrovet_id})
                    ) * 100, 1
                  )
                FROM input_credit_items ici 
                JOIN input_credit_applications ica ON ici.credit_application_id = ica.id 
                WHERE ici.input_catalog_id = ic.id AND ica.agrovet_id = {$staff->agrovet_id}
              ) as usage_percentage
              FROM input_catalog ic 
              WHERE ic.id = {$inputId} AND ic.is_active = 1";
    
    $input = $app->select_one($query);
    
    if (!$input) {
        $response['message'] = 'Input not found';
        echo json_encode($response);
        exit;
    }
    
    // Get usage trend (monthly data for the last 6 months)
    $trendQuery = "SELECT 
                  DATE_FORMAT(ica.application_date, '%b %Y') as month,
                  COUNT(DISTINCT ica.id) as count
                  FROM input_credit_items ici 
                  JOIN input_credit_applications ica ON ici.credit_application_id = ica.id 
                  WHERE ici.input_catalog_id = {$inputId} 
                  AND ica.agrovet_id = {$staff->agrovet_id}
                  AND ica.application_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                  GROUP BY month
                  ORDER BY ica.application_date ASC";
    
    $trendData = $app->select_all($trendQuery);
    
    // Format usage trend for the chart
    if ($trendData && count($trendData) > 0) {
        $months = [];
        $counts = [];
        
        foreach ($trendData as $data) {
            $months[] = $data->month;
            $counts[] = intval($data->count);
        }
        
        $input->usage_trend = [
            'months' => $months,
            'counts' => $counts
        ];
    } else {
        $input->usage_trend = null;
    }
    
    // Get related inputs (same type) specific to this agrovet
    $relatedQuery = "SELECT 
                    ic.id, ic.name, ic.type, ic.standard_price, ic.standard_unit,
                    (
                        SELECT COUNT(*) 
                        FROM input_credit_items ici 
                        JOIN input_credit_applications ica ON ici.credit_application_id = ica.id 
                        WHERE ici.input_catalog_id = ic.id 
                        AND ica.agrovet_id = {$staff->agrovet_id}
                    ) as agrovet_request_count
                    FROM input_catalog ic 
                    WHERE ic.type = '{$input->type}' 
                    AND ic.id != {$inputId} 
                    AND ic.is_active = 1
                    ORDER BY agrovet_request_count DESC
                    LIMIT 3";
    
    $relatedInputs = $app->select_all($relatedQuery);
    
    $input->related_inputs = $relatedInputs ?: [];
    
    // If usage_percentage is null (no credit applications yet), set to 0
    if ($input->usage_percentage === null) {
        $input->usage_percentage = 0;
    }
    
    // Set success response
    $response = [
        'success' => true,
        'message' => 'Input details retrieved successfully',
        'input' => $input
    ];
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit;