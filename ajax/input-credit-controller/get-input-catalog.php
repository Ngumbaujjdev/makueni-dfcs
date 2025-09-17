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
    // Query to get input catalog items
    // This fetches standardized pricing from the input_catalog table
    // You could add agrovet-specific pricing later if needed
    $query = "SELECT 
                ic.id,
                ic.name,
                ic.type,
                ic.description,
                ic.standard_unit,
                ic.standard_price,
                ic.is_active
              FROM input_catalog ic
              WHERE ic.is_active = 1
              ORDER BY ic.type, ic.name";
    
    $catalog_items = $app->select_all($query);
    
    // If no items found, return empty array
    if (!$catalog_items) {
        $catalog_items = [];
    }
    
    // Return success with catalog data
    echo json_encode([
        'success' => true,
        'data' => $catalog_items
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching input catalog: ' . $e->getMessage()
    ]);
}
?>