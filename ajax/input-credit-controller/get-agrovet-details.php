<?php
include "../../config/config.php";
include "../../libs/App.php";
// Check if request is properly made
if (!isset($_GET['agrovet_id']) || empty($_GET['agrovet_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Agrovet ID is required'
    ]);
    exit;
}

$agrovet_id = $_GET['agrovet_id'];
$app = new App();

try {
    // Query to get detailed agrovet information
    $query = "SELECT 
                a.id,
                a.name,
                a.license_number,
                a.location,
                a.phone,
                a.email,
                a.address,
                a.is_active,
                at.name as type_name,
                at.description as type_description
              FROM agrovets a
              LEFT JOIN agrovet_types at ON a.type_id = at.id
              WHERE a.id = :agrovet_id";
    
    $agrovet = $app->selectOne($query, [':agrovet_id' => $agrovet_id]);
    
    // If agrovet not found, return error
    if (!$agrovet) {
        echo json_encode([
            'success' => false,
            'message' => 'Agrovet not found'
        ]);
        exit;
    }
    
    // Return success with agrovet data
    echo json_encode([
        'success' => true,
        'data' => $agrovet
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching agrovet details: ' . $e->getMessage()
    ]);
}
?>