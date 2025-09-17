<?php
include "../../config/config.php";
include "../../libs/App.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['farmer_id'])) {
    try {
        $app = new App;
        $farmerId = $_POST['farmer_id'];
        
        $query = "SELECT f.* 
                  FROM farms f 
                  WHERE f.farmer_id = '{$farmerId}'
                  ";
        
      
        $farms = $app->select_all($query);

        $html = '<option value="">Select a farm...</option>';
        
        if($farms) {
            foreach($farms as $farm) {
                $html .= sprintf(
                    '<option value="%s">%s (%s acres)</option>',
                    $farm->id,
                    htmlspecialchars($farm->name),
                    $farm->size
                );
            }
        }

        echo json_encode([
            'success' => true,
            'html' => $html
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error loading farms: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
}