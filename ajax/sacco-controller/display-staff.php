<?php
include "../../config/config.php";
include "../../libs/App.php";

if (isset($_POST['displayStaff'])) {
    $app = new App;
    
    // Updated query to ensure we get all required fields
    $query = "SELECT 
                ss.id,
                ss.staff_id,
                ss.position,
                ss.department,
                ss.created_at,
                u.first_name,
                u.last_name,
                u.phone,
                u.email
              FROM sacco_staff ss
              INNER JOIN users u ON ss.user_id = u.id
              ORDER BY ss.created_at DESC";

    $staff = $app->select_all($query);
?>
<div class="card custom-card">
    <div class="card-header justify-content-between">
        <div class="card-title">
            SACCO Staff Overview
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="datatable-basic" class="table table-bordered text-nowrap w-100">
                <thead>
                    <tr>
                        <th>Staff ID</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Department</th>
                        <th>Contact</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($staff): ?>
                    <?php foreach ($staff as $member): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($member->staff_id); ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="fw-medium">
                                    <?php 
                                            $fullName = $member->first_name . ' ' . $member->last_name;
                                            echo htmlspecialchars($fullName); 
                                            ?>
                                </span>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($member->position); ?></td>
                        <td><?php echo htmlspecialchars($member->department); ?></td>
                        <td>
                            <div>
                                <div class="text-muted">
                                    <?php echo htmlspecialchars($member->phone); ?><br>
                                    <?php echo htmlspecialchars($member->email); ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php echo date('M d, Y', strtotime($member->created_at)); ?>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-info" title="Edit"
                                    onclick="editStaff(<?php echo $member->id; ?>)">
                                    <i class="ri-edit-line"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" title="Delete"
                                    onclick="deleteStaff(<?php echo $member->id; ?>)">
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
        ], // Sort by created date
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
<?php 
}
?>