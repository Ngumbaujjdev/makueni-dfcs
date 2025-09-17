<?php 
include "../../config/config.php"; 
include "../../libs/App.php";

if (isset($_POST['displayAgrovets'])):
    $app = new App;
    // Modified query to include agrovet type name using JOIN
    $query = "SELECT a.*, at.name as type_name 
              FROM agrovets a 
              LEFT JOIN agrovet_types at ON a.type_id = at.id";
    $agrovets = $app->select_all($query); 
?>
<div class="card custom-card">
    <div class="card-header justify-content-between">
        <div class="card-title">
            Agrovets Overview
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="datatable-basic" class="table table-bordered text-nowrap w-100">
                <thead>
                    <tr>
                        <th>Agrovet Name</th>
                        <th>Type</th>
                        <th>License Number</th>
                        <th>Location</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($agrovets): ?>
                    <?php foreach ($agrovets as $agrovet): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="fw-medium">
                                    <?php echo $agrovet->name ?>
                                </span>
                            </div>
                        </td>
                        <td><?php echo $agrovet->type_name ?></td>
                        <td><?php echo $agrovet->license_number ?></td>
                        <td><?php echo $agrovet->location ?></td>
                        <td>
                            <div>
                                <div class="text-muted">Phone:
                                    <?php echo isset($agrovet->phone) ? $agrovet->phone : 'N/A' ?></div>
                                <div class="text-muted">Email:
                                    <?php echo isset($agrovet->email) ? $agrovet->email : 'N/A' ?></div>
                            </div>
                        </td>
                        <td>
                            <span class="badge <?php echo $agrovet->is_active ? 'bg-success' : 'bg-danger' ?>">
                                <?php echo $agrovet->is_active ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-info" title="Edit"
                                    onclick="editAgrovet(<?php echo $agrovet->id ?>)">
                                    <i class="ri-edit-line"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" title="Delete"
                                    onclick="deleteAgrovet(<?php echo $agrovet->id ?>)">
                                    <i class="ri-delete-bin-5-line"></i>
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
            [5, 'desc']
        ], // Sort by status
        language: {
            searchPlaceholder: 'Search agrovets...',
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