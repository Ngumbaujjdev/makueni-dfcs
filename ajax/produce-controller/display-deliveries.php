<?php
include "../../config/config.php";
include "../../libs/App.php";

if (isset($_POST['displayDeliveries'])):
    $app = new App;
    $userId = $_SESSION['user_id']; 
    
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
                f.name as farm_name,
                CONCAT(u.first_name, ' ', u.last_name) as farmer_name
              FROM produce_deliveries pd
              JOIN farm_products fp ON pd.farm_product_id = fp.id
              JOIN product_types pt ON fp.product_type_id = pt.id
              JOIN farms f ON fp.farm_id = f.id
              JOIN farmers fm ON f.farmer_id = fm.id
              JOIN users u ON fm.user_id = u.id
              ORDER BY pd.delivery_date DESC";
    
    $deliveries = $app->select_all($query);
?>
<div class="card custom-card">
    <div class="card-header justify-content-between">
        <div class="card-title">
            My Produce Deliveries
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="datatable-deliveries" class="table table-bordered text-nowrap w-100">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Product</th>
                        <th>Farm</th>
                        <th>Quantity (KGs)</th>
                        <th>Total Value</th>
                        <th>Status</th>
                        <th>Delivery Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($deliveries): ?>
                    <?php foreach ($deliveries as $delivery): ?>
                    <tr>
                        <td>DLVR<?php echo str_pad($delivery->id, 5, '0', STR_PAD_LEFT); ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="fw-medium">
                                    <?php echo htmlspecialchars($delivery->product_name) ?>
                                </span>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($delivery->farm_name) ?></td>
                        <td><?php echo number_format($delivery->quantity, 2) ?></td>
                        <td>KES <?php echo number_format($delivery->total_value, 2) ?></td>
                        <td>
                            <?php 
                            $statusClass = 'secondary';
                            if ($delivery->status == 'verified') {
                                $statusClass = 'info';
                            } elseif ($delivery->status == 'sold') {
                                $statusClass = 'success';
                            } elseif ($delivery->status == 'rejected') {
                                $statusClass = 'danger';
                            }
                            ?>
                            <span class="badge bg-<?php echo $statusClass; ?>">
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
                                            echo "Buyer: " . htmlspecialchars($buyerName);
                                        }
                                    }
                                }
                                ?>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($delivery->delivery_date)) ?></td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-info" title="View Details"
                                    onclick="viewDeliveryDetails(<?php echo $delivery->id ?>)">
                                    <i class="ri-eye-line"></i>
                                </button>

                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No produce deliveries found</td>
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