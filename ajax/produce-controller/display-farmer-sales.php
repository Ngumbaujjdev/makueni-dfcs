<?php
include "../../config/config.php";
include "../../libs/App.php";

if (isset($_POST['displayDeliveries'])):
    $app = new App;
    $userId = $_SESSION['user_id']; 
    
    // Get the farmer's ID from the user ID
    $farmerQuery = "SELECT id FROM farmers WHERE user_id = $userId";
    $farmerResult = $app->select_one($farmerQuery);
    
    if ($farmerResult) {
        $farmerId = $farmerResult->id;
        
        // Get only SOLD produce deliveries for this farmer
        $query = "SELECT 
                    pd.id,
                    pd.quantity,
                    pd.unit_price,
                    pd.total_value,
                    pd.quality_grade,
                    pd.delivery_date,
                    pd.status,
                    pd.notes,
                    pt.name as product_name,
                    f.name as farm_name
                  FROM produce_deliveries pd
                  JOIN farm_products fp ON pd.farm_product_id = fp.id
                  JOIN product_types pt ON fp.product_type_id = pt.id
                  JOIN farms f ON fp.farm_id = f.id
                  WHERE f.farmer_id = $farmerId
                  AND pd.status = 'sold'
                  ORDER BY pd.delivery_date DESC";
        
        $deliveries = $app->select_all($query);
        
        // Get summary statistics for sold produce only
        $summaryQuery = "SELECT 
                            COUNT(*) as total_sold,
                            SUM(quantity) as total_quantity,
                            SUM(total_value) as total_sales_value,
                            AVG(total_value/quantity) as avg_price_per_kg
                         FROM produce_deliveries pd
                         JOIN farm_products fp ON pd.farm_product_id = fp.id
                         JOIN farms f ON fp.farm_id = f.id
                         WHERE f.farmer_id = $farmerId
                         AND pd.status = 'sold'";
        
        $summary = $app->select_one($summaryQuery);
        
        // Get product breakdown for sold products
        $productQuery = "SELECT 
                            pt.name as product_name,
                            COUNT(*) as delivery_count,
                            SUM(pd.quantity) as total_quantity,
                            SUM(pd.total_value) as sold_value,
                            AVG(pd.total_value/pd.quantity) as avg_price
                         FROM produce_deliveries pd
                         JOIN farm_products fp ON pd.farm_product_id = fp.id
                         JOIN product_types pt ON fp.product_type_id = pt.id
                         JOIN farms f ON fp.farm_id = f.id
                         WHERE f.farmer_id = $farmerId
                         AND pd.status = 'sold'
                         GROUP BY pt.name
                         ORDER BY sold_value DESC";
        
        $products = $app->select_all($productQuery);
        
        // Get buyer statistics
        $buyerQuery = "SELECT 
                          SUBSTRING_INDEX(SUBSTRING_INDEX(pd.notes, 'Sold to:', -1), '.', 1) as buyer,
                          COUNT(*) as purchase_count,
                          SUM(pd.quantity) as total_quantity,
                          SUM(pd.total_value) as total_value
                       FROM produce_deliveries pd
                       JOIN farm_products fp ON pd.farm_product_id = fp.id
                       JOIN farms f ON fp.farm_id = f.id
                       WHERE f.farmer_id = $farmerId
                       AND pd.status = 'sold'
                       AND pd.notes LIKE '%Sold to:%'
                       GROUP BY buyer
                       ORDER BY total_value DESC";
        
        $buyers = $app->select_all($buyerQuery);
    } else {
        $deliveries = [];
        $summary = null;
        $products = [];
        $buyers = [];
    }
?>
<!-- Sold Deliveries Table -->
<div class="card custom-card mt-3 shadow-sm border-0">
    <div class="card-header justify-content-between" style="background-color: #f0f8f0;">
        <div class="card-title d-flex align-items-center">
            <i class="fa-solid fa-hand-holding-dollar text-success me-2 fs-4"></i>
            <span class="fw-bold">My Sold Produce History</span>
        </div>
        <div>

        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="datatable-sold-deliveries" class="table table-hover text-nowrap w-100">
                <thead>
                    <tr class="bg-light">
                        <th><i class="fa-solid fa-receipt text-primary me-1"></i> Reference</th>
                        <th><i class="fa-solid fa-leaf text-success me-1"></i> Product</th>
                        <th><i class="fa-solid fa-tractor text-warning me-1"></i> Farm</th>
                        <th><i class="fa-solid fa-scale-balanced text-info me-1"></i> Quantity</th>
                        <th><i class="fa-solid fa-money-bill-trend-up text-success me-1"></i> Sale Value</th>
                        <th><i class="fa-solid fa-tags text-danger me-1"></i> Price/KG</th>
                        <th><i class="fa-solid fa-building text-primary me-1"></i> Buyer</th>
                        <th><i class="fa-solid fa-calendar-check text-success me-1"></i> Sale Date</th>
                        <th><i class="fa-solid fa-wand-magic-sparkles text-secondary me-1"></i> Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($deliveries): ?>
                    <?php foreach ($deliveries as $delivery): ?>
                    <tr class="border-bottom">
                        <td>
                            <span class="badge bg-primary-transparent text-primary rounded-pill fw-normal">
                                DLVR<?php echo str_pad($delivery->id, 5, '0', STR_PAD_LEFT); ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs avatar-rounded bg-success me-2 text-white">
                                    <?php echo substr($delivery->product_name, 0, 1); ?>
                                </span>
                                <span class="fw-medium">
                                    <?php echo htmlspecialchars($delivery->product_name) ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-seedling text-success me-1"></i>
                                <?php echo htmlspecialchars($delivery->farm_name) ?>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 me-2" style="height: 5px; max-width: 50px;">
                                    <div class="progress-bar bg-info"
                                        style="width: <?php echo min(100, ($delivery->quantity / 1000) * 100); ?>%">
                                    </div>
                                </div>
                                <span class="badge bg-info-transparent text-info rounded-pill fw-normal">
                                    <?php echo number_format($delivery->quantity, 2) ?> KGs
                                </span>
                            </div>
                        </td>
                        <td>
                            <span class="fw-bold text-success">
                                <i class="fa-solid fa-coins me-1"></i>
                                KES <?php echo number_format($delivery->total_value, 2) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-danger-transparent text-danger rounded-pill py-1 px-2">
                                KES <?php echo number_format($delivery->total_value / $delivery->quantity, 2) ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <?php 
                                // Extract buyer info from notes if it exists
                                if (strpos($delivery->notes, 'Sold to:') !== false) {
                                    $parts = explode('Sold to:', $delivery->notes);
                                    if (isset($parts[1])) {
                                        $buyerInfo = trim($parts[1]);
                                        if (strpos($buyerInfo, '.') !== false) {
                                            $buyerName = trim(substr($buyerInfo, 0, strpos($buyerInfo, '.')));
                                            echo '<span class="avatar avatar-xs avatar-rounded bg-primary-transparent text-primary me-1">
                                                  <i class="fa-solid fa-building-user"></i>
                                                  </span>';
                                            echo '<span class="text-dark">' . htmlspecialchars($buyerName) . '</span>';
                                        } else {
                                            echo '<span class="avatar avatar-xs avatar-rounded bg-primary-transparent text-primary me-1">
                                                  <i class="fa-solid fa-building-user"></i>
                                                  </span>';
                                            echo '<span class="text-dark">' . htmlspecialchars($buyerInfo) . '</span>';
                                        }
                                    }
                                } else {
                                    echo '<span class="text-muted">-</span>';
                                }
                                ?>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs avatar-rounded bg-success-transparent text-success me-1">
                                    <i class="fa-solid fa-calendar-day"></i>
                                </span>
                                <?php echo date('M d, Y', strtotime($delivery->delivery_date)) ?>
                            </div>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-success rounded-pill" title="View Details"
                                onclick="viewDeliveryDetails(<?php echo $delivery->id ?>)">
                                <i class="ri-eye-line me-1"></i> Details
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <div class="avatar avatar-lg avatar-rounded bg-light mb-3">
                                    <i class="fa-solid fa-shop text-muted fa-2x"></i>
                                </div>
                                <h5 class="text-muted">No sales records found</h5>
                                <p class="text-muted">Your sold produce history will appear here once you have completed
                                    sales</p>
                                <button class="btn btn-sm btn-outline-success rounded-pill mt-2">
                                    <i class="fa-solid fa-plus me-1"></i> Add New Delivery
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $('#datatable-sold-deliveries').DataTable({
        responsive: true,
        order: [
            [7, 'desc']
        ], // Sort by sale date
        language: {
            searchPlaceholder: 'Search sales...',
            sSearch: '',
        },
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
        ],
        buttons: ['copy', 'excel', 'pdf', 'print']
    });
});
</script>
<?php endif; ?>