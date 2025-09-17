<?php
include "../../config/config.php";
include "../../libs/App.php";

if (isset($_POST['displayVerifiedProduce'])):
    $app = new App;
    
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
          WHERE pd.status = 'verified'
          AND pd.is_sold = 0
          ORDER BY pd.delivery_date DESC";
    
    $produces = $app->select_all($query);
?>
<div class="card custom-card mt-4">
    <div class="card-header justify-content-between">
        <div class="card-title">
            Verified Produce Ready for Sale
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="datatable-verified" class="table table-bordered text-nowrap w-100">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Farmer</th>
                        <th>Farm</th>
                        <th>Product</th>
                        <th>Quantity (KGs)</th>
                        <th>Unit Price</th>
                        <th>Total Value</th>
                        <th>Quality</th>
                        <th>Delivery Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($produces): ?>
                    <?php foreach ($produces as $produce): ?>
                    <tr>
                        <td>DLVR<?php echo str_pad($produce->id, 5, '0', STR_PAD_LEFT); ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="fw-medium">
                                    <?php echo htmlspecialchars($produce->farmer_name) ?>
                                </span>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($produce->farm_name) ?></td>
                        <td><?php echo htmlspecialchars($produce->product_name) ?></td>
                        <td><?php echo number_format($produce->quantity, 2) ?></td>
                        <td>KES <?php echo number_format($produce->unit_price, 2) ?></td>
                        <td>KES <?php echo number_format($produce->total_value, 2) ?></td>
                        <td>
                            <span
                                class="badge bg-<?php echo $produce->quality_grade == 'A' ? 'success' : ($produce->quality_grade == 'B' ? 'warning' : 'danger'); ?>">
                                Grade <?php echo $produce->quality_grade ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($produce->delivery_date)) ?></td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-primary" title="Mark as Sold"
                                    onclick="markAsSold(<?php echo $produce->id ?>)">
                                    <i class="ri-money-dollar-circle-line"></i> Mark as Sold
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center">No verified produce found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#datatable-verified').DataTable({
        responsive: true,
        order: [
            [8, 'desc']
        ], // Sort by delivery date
        language: {
            searchPlaceholder: 'Search produce...',
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