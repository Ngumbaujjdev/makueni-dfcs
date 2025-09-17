<?php
include "../../config/config.php";
include "../../libs/App.php";

if (isset($_POST['displayAllInputs'])):
    $app = new App;
    
    // Get session user_id to identify agrovet staff
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        exit;
    }
    
    // Get staff agrovet_id
    $staffQuery = "SELECT s.id as staff_id, s.agrovet_id 
                  FROM agrovet_staff s 
                  WHERE s.user_id = :user_id";
    
    $staff = $app->selectOne($staffQuery, [':user_id' => $userId]);
    
    // Get filter parameters
    $inputType = $_POST['inputType'] ?? '';
    $priceRange = $_POST['priceRange'] ?? '';
    $sortBy = $_POST['sortBy'] ?? 'name-asc';
    
    // Build filter conditions
    $conditions = ["is_active = 1"];
    $params = [];
    
    if (!empty($inputType)) {
        $conditions[] = "type = :input_type";
        $params[':input_type'] = $inputType;
    }
    
    if (!empty($priceRange)) {
        if ($priceRange == '0-500') {
            $conditions[] = "standard_price < 500";
        } elseif ($priceRange == '500-1000') {
            $conditions[] = "standard_price >= 500 AND standard_price <= 1000";
        } elseif ($priceRange == '1000-2000') {
            $conditions[] = "standard_price > 1000 AND standard_price <= 2000";
        } elseif ($priceRange == '2000+') {
            $conditions[] = "standard_price > 2000";
        }
    }
    
    // Build the WHERE clause
    $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
    
    // Build the ORDER BY clause
    $orderClause = "ORDER BY ";
    switch ($sortBy) {
        case 'name-asc':
            $orderClause .= "name ASC";
            break;
        case 'name-desc':
            $orderClause .= "name DESC";
            break;
        case 'price-asc':
            $orderClause .= "standard_price ASC";
            break;
        case 'price-desc':
            $orderClause .= "standard_price DESC";
            break;
        case 'popularity':
            $orderClause = "ORDER BY (SELECT COUNT(*) FROM input_credit_items ici WHERE ici.input_catalog_id = ic.id) DESC";
            break;
        default:
            $orderClause .= "name ASC";
    }
    
    // Query to get all input catalog items with popularity data
    $query = "SELECT ic.*, 
              (SELECT COUNT(*) FROM input_credit_items ici WHERE ici.input_catalog_id = ic.id) as request_count
              FROM input_catalog ic 
              $whereClause
              $orderClause";
    
    $inputs = $app->select_all($query);
    
    // Get statistics for the cards
    $statsQuery = "SELECT 
                    COUNT(*) as total_inputs,
                    COUNT(CASE WHEN type = 'fertilizer' THEN 1 END) as fertilizer_count,
                    COUNT(CASE WHEN type = 'pesticide' THEN 1 END) as pesticide_count,
                    COUNT(CASE WHEN type = 'seeds' THEN 1 END) as seed_count,
                    COUNT(CASE WHEN type = 'tools' THEN 1 END) as tool_count,
                    AVG(standard_price) as avg_price
                   FROM input_catalog
                   WHERE is_active = 1";
                   
    $stats = $app->selectOne($statsQuery);
?>

<!-- Update the stats cards via JavaScript -->
<script>
document.getElementById('total-inputs-count').textContent = '<?php echo $stats->total_inputs; ?>';
document.getElementById('fertilizers-count').textContent = '<?php echo $stats->fertilizer_count; ?>';
document.getElementById('pesticides-count').textContent = '<?php echo $stats->pesticide_count; ?>';
document.getElementById('average-price').textContent = 'KES <?php echo number_format($stats->avg_price, 2); ?>';
</script>

<div class="card custom-card">
    <div class="card-header justify-content-between">
        <div class="card-title">
            <i class="ri-shopping-bag-line me-2"></i> Input Catalog
        </div>
        <div class="btn-group">
            <button class="btn btn-outline-primary btn-sm" id="btnShowAll">All</button>
            <button class="btn btn-outline-success btn-sm" id="btnShowFertilizers">Fertilizers</button>
            <button class="btn btn-outline-warning btn-sm" id="btnShowPesticides">Pesticides</button>
            <button class="btn btn-outline-info btn-sm" id="btnShowSeeds">Seeds</button>
            <button class="btn btn-outline-secondary btn-sm" id="btnShowTools">Tools</button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="datatable-all-inputs" class="table table-bordered text-nowrap w-100">
                <thead>
                    <tr>
                        <th><i class="ri-hash-line me-1"></i>ID</th>
                        <th><i class="ri-product-hunt-line me-1"></i>Name</th>
                        <th><i class="ri-apps-line me-1"></i>Type</th>
                        <th><i class="ri-scales-line me-1"></i>Unit</th>
                        <th><i class="ri-money-dollar-circle-line me-1"></i>Price (KES)</th>
                        <th><i class="ri-file-list-line me-1"></i>Description</th>
                        <th><i class="ri-bar-chart-line me-1"></i>Popularity</th>
                        <th><i class="ri-settings-3-line me-1"></i>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($inputs): ?>
                    <?php foreach ($inputs as $input): ?>
                    <tr>
                        <td class="fw-semibold"><?php echo $input->id; ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm bg-<?php
                                    switch($input->type) {
                                        case 'fertilizer': echo 'success'; break;
                                        case 'pesticide': echo 'warning'; break;
                                        case 'seeds': echo 'info'; break;
                                        case 'tools': echo 'primary'; break;
                                        default: echo 'secondary';
                                    }
                                ?> me-2">
                                    <?php echo strtoupper(substr($input->name, 0, 1)); ?>
                                </span>
                                <span class="fw-medium">
                                    <?php echo htmlspecialchars($input->name) ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-<?php
                                switch($input->type) {
                                    case 'fertilizer': echo 'success'; break;
                                    case 'pesticide': echo 'warning'; break;
                                    case 'seeds': echo 'info'; break;
                                    case 'tools': echo 'primary'; break;
                                    default: echo 'secondary';
                                }
                            ?>-transparent">
                                <?php echo ucfirst($input->type); ?>
                            </span>
                        </td>
                        <td><?php echo $input->standard_unit; ?></td>
                        <td class="fw-semibold">KES <?php echo number_format($input->standard_price, 2); ?></td>
                        <td>
                            <?php 
                                $description = $input->description ?? 'No description available';
                                echo strlen($description) > 50 ? substr($description, 0, 50) . '...' : $description; 
                            ?>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1" style="height: 6px;">
                                    <?php 
                                        // Calculate percentage based on max request count
                                        $max_requests = max(array_column((array)$inputs, 'request_count'));
                                        $percentage = $max_requests > 0 ? ($input->request_count / $max_requests) * 100 : 0;
                                    ?>
                                    <div class="progress-bar bg-success" style="width: <?php echo $percentage; ?>%">
                                    </div>
                                </div>
                                <span class="ms-2"><?php echo $input->request_count; ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-primary" title="View Details"
                                    onclick="viewInputDetails(<?php echo $input->id ?>)">
                                    <i class="ri-eye-line"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="ri-information-line fs-2 text-muted mb-2"></i>
                            <p>No input catalog items found</p>
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
    var table = $('#datatable-all-inputs').DataTable({
        responsive: true,
        order: [
            [1, 'asc']
        ], // Sort by name
        language: {
            searchPlaceholder: 'Search inputs...',
            sSearch: '',
        },
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
        ],
        dom: 'Bfrtip',
        buttons: [{
            extend: 'collection',
            text: '<i class="ri-download-2-line me-1"></i> Export',
            buttons: [
                'copy',
                'excel',
                'csv',
                'pdf',
                'print'
            ]
        }]
    });

    // Filter buttons
    $('#btnShowAll').click(function() {
        table.search('').draw();
    });

    $('#btnShowFertilizers').click(function() {
        table.search('Fertilizer').draw();
    });

    $('#btnShowPesticides').click(function() {
        table.search('Pesticide').draw();
    });

    $('#btnShowSeeds').click(function() {
        table.search('Seeds').draw();
    });

    $('#btnShowTools').click(function() {
        table.search('Tools').draw();
    });

    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
<?php endif; ?>