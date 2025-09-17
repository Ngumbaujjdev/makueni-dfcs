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
        
        // Get all produce deliveries for this farmer
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
                  ORDER BY pd.delivery_date DESC";
        
        $deliveries = $app->select_all($query);
        
        // Get summary statistics for this farmer
        $summaryQuery = "SELECT 
                            COUNT(*) as total_deliveries,
                            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                            SUM(CASE WHEN status = 'verified' THEN 1 ELSE 0 END) as verified_count,
                            SUM(CASE WHEN status = 'sold' THEN 1 ELSE 0 END) as sold_count,
                            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_count,
                            SUM(quantity) as total_quantity,
                            SUM(CASE WHEN status = 'sold' THEN total_value ELSE 0 END) as total_sales_value
                         FROM produce_deliveries pd
                         JOIN farm_products fp ON pd.farm_product_id = fp.id
                         JOIN farms f ON fp.farm_id = f.id
                         WHERE f.farmer_id = $farmerId";
        
        $summary = $app->select_one($summaryQuery);
        
        // Get product breakdown
        $productQuery = "SELECT 
                            pt.name as product_name,
                            COUNT(*) as delivery_count,
                            SUM(pd.quantity) as total_quantity,
                            SUM(CASE WHEN pd.status = 'sold' THEN pd.total_value ELSE 0 END) as sold_value
                         FROM produce_deliveries pd
                         JOIN farm_products fp ON pd.farm_product_id = fp.id
                         JOIN product_types pt ON fp.product_type_id = pt.id
                         JOIN farms f ON fp.farm_id = f.id
                         WHERE f.farmer_id = $farmerId
                         GROUP BY pt.name
                         ORDER BY total_quantity DESC";
        
        $products = $app->select_all($productQuery);
    } else {
        $deliveries = [];
        $summary = null;
        $products = [];
    }
?>
<!-- Deliveries Table -->
<div class="card custom-card mt-2 shadow-sm border-0">
    <div class="card-header justify-content-between" style="background-color: #f8faf5;">
        <div class="card-title d-flex align-items-center">
            <i class="fa-solid fa-truck-fast text-success me-2 fs-4"></i>
            <span class="fw-bold">My Produce Delivery History</span>
        </div>
        <div>
            <button class="btn btn-sm btn-outline-success rounded-pill">
                <i class="fa-solid fa-file-export me-1"></i> Export History
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="datatable-deliveries" class="table table-hover table-striped text-nowrap w-100">
                <thead>
                    <tr class="bg-light">
                        <th><i class="fa-solid fa-hashtag text-muted me-1"></i> Reference</th>
                        <th><i class="fa-solid fa-apple-whole text-danger me-1"></i> Product</th>
                        <th><i class="fa-solid fa-mountain-sun text-success me-1"></i> Farm</th>
                        <th><i class="fa-solid fa-weight-scale text-primary me-1"></i> Quantity</th>
                        <th><i class="fa-solid fa-money-bill-wave text-success me-1"></i> Value</th>
                        <th><i class="fa-solid fa-circle-info text-info me-1"></i> Status</th>
                        <th><i class="fa-solid fa-calendar-day text-warning me-1"></i> Date</th>
                        <th><i class="fa-solid fa-sliders text-secondary me-1"></i> Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($deliveries): ?>
                    <?php foreach ($deliveries as $delivery): ?>
                    <tr>
                        <td>
                            <span class="badge bg-light text-dark rounded-pill">
                                DLVR<?php echo str_pad($delivery->id, 5, '0', STR_PAD_LEFT); ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs avatar-rounded bg-success-transparent me-2">
                                    <?php echo substr($delivery->product_name, 0, 1); ?>
                                </span>
                                <span class="fw-medium text-dark">
                                    <?php echo htmlspecialchars($delivery->product_name) ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-wheat-awn text-success me-1"></i>
                                <?php echo htmlspecialchars($delivery->farm_name) ?>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="progress me-2" style="width: 40px; height: 5px;">
                                    <div class="progress-bar bg-primary"
                                        style="width: <?php echo min(100, ($delivery->quantity / 1000) * 100); ?>%">
                                    </div>
                                </div>
                                <span><?php echo number_format($delivery->quantity, 2) ?> KGs</span>
                            </div>
                        </td>
                        <td>
                            <span class="text-success fw-bold">
                                KES <?php echo number_format($delivery->total_value, 2) ?>
                            </span>
                        </td>
                        <td>
                            <?php 
                            $statusClass = 'secondary';
                            $statusIcon = 'clock';
                            
                            if ($delivery->status == 'verified') {
                                $statusClass = 'info';
                                $statusIcon = 'check-double';
                            } elseif ($delivery->status == 'sold') {
                                $statusClass = 'success';
                                $statusIcon = 'circle-check';
                            } elseif ($delivery->status == 'rejected') {
                                $statusClass = 'danger';
                                $statusIcon = 'circle-xmark';
                            }
                            ?>
                            <span
                                class="badge bg-<?php echo $statusClass; ?>-transparent text-<?php echo $statusClass; ?> py-1 px-2 rounded">
                                <i class="fa-solid fa-<?php echo $statusIcon; ?> me-1"></i>
                                <?php echo ucfirst($delivery->status); ?>
                            </span>
                            <?php if ($delivery->status == 'sold'): ?>
                            <div class="text-muted small mt-1">
                                <?php 
                                // Extract buyer info from notes if it exists
                                if (strpos($delivery->notes, 'Sold to:') !== false) {
                                    $parts = explode('Sold to:', $delivery->notes);
                                    if (isset($parts[1])) {
                                        $buyerInfo = trim($parts[1]);
                                        if (strpos($buyerInfo, '.') !== false) {
                                            $buyerName = trim(substr($buyerInfo, 0, strpos($buyerInfo, '.')));
                                            echo "<i class='fa-solid fa-building-user me-1 text-success'></i>" . htmlspecialchars($buyerName);
                                        } else {
                                            echo "<i class='fa-solid fa-building-user me-1 text-success'></i>" . htmlspecialchars($buyerInfo);
                                        }
                                    }
                                }
                                ?>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="text-nowrap">
                                <i class="fa-regular fa-calendar me-1 text-muted"></i>
                                <?php echo date('M d, Y', strtotime($delivery->delivery_date)) ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-success rounded-pill btn-icon-text" title="View Details"
                                    onclick="viewDeliveryDetails(<?php echo $delivery->id ?>)">
                                    <i class="ri-eye-line me-1"></i> Details
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <i class="fa-solid fa-seedling text-muted fa-3x mb-3"></i>
                                <h5 class="text-muted">No produce deliveries found</h5>
                                <p class="text-muted">Your delivery history will appear here</p>
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
    $('#datatable-deliveries').DataTable({
        responsive: true,
        order: [
            [6, 'desc']
        ], // Sort by delivery date
        language: {
            searchPlaceholder: 'Search deliveries...',
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