<?php
include "../../config/config.php";
include "../../libs/App.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['farm_id'])) {
    try {
        $app = new App;
        $farmerId = $_POST['farm_id'];
        
       $query = "SELECT fp.*, pt.name as product_name, 
          ep.expected_quantity, ep.estimated_unit_price
          FROM farm_products fp
          JOIN product_types pt ON fp.product_type_id = pt.id
          LEFT JOIN expected_produce ep ON fp.id = ep.farm_product_id
          WHERE fp.farm_id = '{$farmerId}'
          AND fp.is_active = 1";
      
        $products = $app->select_all($query);

        $html = '<option value="">Select a product...</option>';
        
        if($products) {
            foreach($products as $product) {
                $expectedInfo = '';
                if($product->expected_quantity) {
                    $expectedInfo = sprintf(
                        ' (Expected: %s KG @ KES %s)',
                        number_format($product->expected_quantity, 2),
                        number_format($product->estimated_unit_price, 2)
                    );
                }
                
                $html .= sprintf(
                    '<option value="%s" data-expected="%s" data-price="%s">%s%s</option>',
                    $product->id,
                    $product->expected_quantity,
                    $product->estimated_unit_price,
                    htmlspecialchars($product->product_name),
                    $expectedInfo
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
            'message' => 'Error loading products: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
}