<?php
include "../../config/config.php";
include "../../libs/App.php";

if (isset($_POST['displayFarms'])):
    $app = new App;
    $userId = $_SESSION['user_id']; 
    $farmerQuery = "SELECT id FROM farmers WHERE user_id = :user_id";
      $farmerParams = [':user_id' => $userId];
      $farmerResult = $app->selectOne($farmerQuery, $farmerParams);
      $farmerId = $farmerResult->id;
    
    $query = "SELECT 
                f.id,
                f.name as farm_name,
                f.location,
                f.size,
                f.created_at,
                GROUP_CONCAT(DISTINCT ft.name) as fruits,
                COUNT(DISTINCT ffm.fruit_type_id) as fruit_count,
                SUM(fp.estimated_production) as total_production
              FROM farms f
              LEFT JOIN farm_fruit_mapping ffm ON f.id = ffm.farm_id
              LEFT JOIN fruit_types ft ON ffm.fruit_type_id = ft.id
              LEFT JOIN farm_products fp ON f.id = fp.farm_id
              WHERE f.farmer_id = '{$farmerId}'
              GROUP BY f.id
              ORDER BY f.id DESC";
    
    $farms = $app->select_all($query);
?>
<div class="card custom-card shadow-sm">
    <div class="card-header justify-content-between bg-gradient-light">
        <div class="card-title d-flex align-items-center">
            <i class="ri-landscape-line fs-4 me-2 text-success"></i>
            <span class="fw-bold">My Farms Overview</span>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="datatable-basic" class="table table-hover table-striped rounded-3 text-nowrap w-100">
                <thead class="bg-light">
                    <tr>
                        <th><i class="ri-plant-line me-1 text-success"></i>Farm Name</th>
                        <th><i class="ri-map-pin-line me-1 text-primary"></i>Location</th>
                        <th><i class="ri-ruler-2-line me-1 text-warning"></i>Size (acres)</th>
                        <th><i class="ri-seedling-line me-1 text-success"></i>Fruits Grown</th>
                        <th><i class="ri-scales-3-line me-1 text-info"></i>Expected Production</th>
                        <th><i class="ri-calendar-check-line me-1 text-secondary"></i>Registration Date</th>
                        <th><i class="ri-tools-line me-1"></i>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($farms): ?>
                    <?php foreach ($farms as $farm): ?>
                    <tr class="align-middle">
                        <td>
                            <div class="d-flex align-items-center">
                                <div
                                    class="avatar avatar-sm bg-success-subtle rounded-circle me-2 d-flex align-items-center justify-content-center">
                                    <i class="ri-plant-line text-success"></i>
                                </div>
                                <span class="fw-medium">
                                    <?php echo htmlspecialchars($farm->farm_name) ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="ri-map-pin-fill text-danger me-1"></i>
                                <?php echo htmlspecialchars($farm->location) ?>
                            </div>
                        </td>
                        <td>
                            <span
                                class="badge bg-light-subtle border border-light-subtle text-dark px-3 py-2 rounded-pill">
                                <?php echo number_format($farm->size, 2) ?> acres
                            </span>
                        </td>
                        <td>
                            <div>
                                <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">
                                    <i class="ri-seedling-line me-1"></i>
                                    <?php echo $farm->fruit_count ?> types
                                </span>
                                <div class="text-muted small mt-1 fst-italic">
                                    <?php echo htmlspecialchars($farm->fruits) ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="ri-scales-3-line text-info me-1"></i>
                                <span class="fw-medium"><?php echo number_format($farm->total_production) ?> KGs</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="ri-calendar-check-line text-secondary me-1"></i>
                                <?php echo date('M d, Y', strtotime($farm->created_at)) ?>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="http://localhost/dfcs/farmers/farms/view-details?id=<?php echo $farm->id ?>"
                                    class="btn btn-sm btn-soft-info rounded-pill" title="View Details">
                                    <i class="ri-eye-line me-1"></i>View
                                </a>
                                <a href="http://localhost/dfcs/farmers/farms/edit-data?id=<?php echo $farm->id ?>"
                                    class="btn btn-sm btn-soft-primary rounded-pill" title="Edit Farm">
                                    <i class="ri-edit-line me-1"></i>Edit
                                </a>
                                <button class="btn btn-sm btn-soft-danger rounded-pill" title="Delete Farm"
                                    onclick="deleteFarm(<?php echo $farm->id ?>)">
                                    <i class="ri-delete-bin-line me-1"></i>Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#datatable-basic').DataTable({
        responsive: true,
        order: [
            [2, 'desc']
        ], // Sort by farm size
        language: {
            searchPlaceholder: 'Search farms...',
            sSearch: '',
        },
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
        ],
        buttons: ['copy', 'excel', 'pdf', 'print']
    });
});

// Function placeholders for actions
function viewFarm(farmId) {
    // Implement view farm details
    console.log('View farm:', farmId);
}

function editFarm(farmId) {
    // Implement edit farm
    console.log('Edit farm:', farmId);
}

function trackProduction(farmId) {
    // Implement production tracking
    console.log('Track production:', farmId);
}
</script>
<?php endif; ?>