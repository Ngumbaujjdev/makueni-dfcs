<?php
include "../../config/config.php";
include "../../libs/App.php";

if (isset($_POST['displayStaff'])):
    $app = new App;
    $query = "SELECT 
                s.id,
                s.position,
                s.employee_number,
                s.phone,
                s.is_active,
                s.agrovet_id,
                u.first_name,
                u.last_name,
                u.email,
                a.name as agrovet_name
              FROM agrovet_staff s
              INNER JOIN users u ON s.user_id = u.id
              LEFT JOIN agrovets a ON s.agrovet_id = a.id
              ORDER BY s.id DESC";
    
    $staff = $app->select_all($query);
?>
<div class="card custom-card">
    <div class="card-header justify-content-between">
        <div class="card-title">
            Agrovet Staff Overview
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="datatable-basic" class="table table-bordered text-nowrap w-100">
                <thead>
                    <tr>
                        <th>Staff Name</th>
                        <th>Agrovet</th>
                        <th>Position</th>
                        <th>Employee Number</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($staff): ?>
                    <?php foreach ($staff as $member): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="fw-medium">
                                    <?php echo htmlspecialchars($member->first_name . ' ' . $member->last_name) ?>
                                </span>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($member->agrovet_name) ?></td>
                        <td><?php echo htmlspecialchars($member->position) ?></td>
                        <td><?php echo htmlspecialchars($member->employee_number) ?></td>
                        <td>
                            <div>
                                <div class="text-muted">
                                    Phone:
                                    <?php echo isset($member->phone) ? htmlspecialchars($member->phone) : 'N/A' ?>
                                </div>
                                <div class="text-muted">
                                    Email: <?php echo htmlspecialchars($member->email) ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge <?php echo $member->is_active ? 'bg-success' : 'bg-danger' ?>">
                                <?php echo $member->is_active ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-info" title="Edit"
                                    onclick="editStaff(<?php echo $member->id ?>)">
                                    <i class="ri-edit-line"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" title="Delete"
                                    onclick="deleteStaff(<?php echo $member->id ?>)">
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
            searchPlaceholder: 'Search staff...',
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