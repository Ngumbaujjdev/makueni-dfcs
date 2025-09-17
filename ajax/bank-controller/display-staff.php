<?php 
include "../../config/config.php"; 
include "../../libs/App.php";

if (isset($_POST['displayBankStaff'])):
    $app = new App;
    $query = "SELECT bs.*, b.name as bank_name, b.branch as bank_branch, u.email as email, u.first_name, u.last_name  
              FROM bank_staff bs
              LEFT JOIN banks b ON bs.bank_id = b.id
              LEFT JOIN users u ON bs.user_id = u.id";
    $staff = $app->select_all($query); 
?>
<div class="card custom-card">
    <div class="card-header justify-content-between">
        <div class="card-title">
            Bank Staff Overview
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="datatable-basic" class="table table-bordered text-nowrap w-100">
                <thead>
                    <tr>
                        <th>Staff Name</th>
                        <th>Email</th>
                        <th>Bank</th>
                        <th>Position</th>
                        <th>Staff ID</th>
                        <th>Department</th>
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
                                    <?php echo $member->first_name . ' ' . $member->last_name ?>
                                </span>
                            </div>
                        </td>
                        <td><?php echo $member->email ?></td>
                        <td><?php echo $member->bank_name . ' - ' . $member->bank_branch ?></td>
                        <td><?php echo $member->position ?></td>
                        <td><?php echo $member->staff_id ?></td>
                        <td><?php echo $member->department ?></td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-info" title="Edit"
                                    onclick="editBankStaff(<?php echo $member->id ?>)">
                                    <i class="ri-edit-line"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" title="Delete"
                                    onclick="deleteBankStaff(<?php echo $member->id ?>)">
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
            [6, 'desc']
        ], // Sort by action column
        language: {
            searchPlaceholder: 'Search bank staff...',
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