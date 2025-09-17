<?php
include "../../config/config.php";
include "../../libs/App.php";

if (isset($_POST['displayBanks'])):
    $app = new App;
    $query = "SELECT * FROM banks ";
    $banks = $app->select_all($query);
?>
<div class="card custom-card">
    <div class="card-header justify-content-between">
        <div class="card-title">
            Banks Overview
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="datatable-basic" class="table table-bordered text-nowrap w-100">
                <thead>
                    <tr>
                        <th>Bank Name</th>
                        <th>Branch</th>
                        <th>Location</th>
                        <th>Contact</th>

                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($banks): ?>
                    <?php foreach ($banks as $bank): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="fw-medium">
                                    <?php echo $bank->name ?>
                                </span>
                            </div>
                        </td>
                        <td><?php echo $bank->branch ?></td>
                        <td><?php echo $bank->location ?></td>
                        <td>
                            <div>
                                <div class="text-muted"><?php echo isset($bank->phone) ? $bank->phone : 'N/A' ?></div>
                            </div>
                        </td>

                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-info" title="Edit"
                                    onclick="editBank(<?php echo $bank->id ?>)">
                                    <i class="ri-edit-line"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" title="Delete"
                                    onclick="deleteBank(<?php echo $bank->id ?>)">
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
            [4, 'desc']
        ], // Sort by created date
        language: {
            searchPlaceholder: 'Search banks...',
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