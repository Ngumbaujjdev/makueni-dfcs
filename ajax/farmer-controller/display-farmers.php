<?php
include "../../config/config.php";
include "../../libs/App.php";

// Initialize $farmers as an empty array to prevent the undefined variable error
$farmers = [];

if (isset($_POST['displayFarmers'])):
    $app = new App;
    $query = "SELECT 
                f.id,
                f.registration_number,
                f.is_verified,
                f.created_at,
                u.first_name,
                u.last_name,
                u.email,
                u.phone,
                u.location
              FROM farmers f
              INNER JOIN users u ON f.user_id = u.id
              ORDER BY f.id DESC";
    
    $farmers = $app->select_all($query);
    
    // Check if $farmers is not an array or is null, reset it to empty array
    if (!is_array($farmers)) {
        $farmers = [];
    }
?>
<div class="card custom-card shadow-sm border-0">
    <div class="card-header bg-gradient-primary text-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="card-title mb-0">
                <i class="ri-user-line me-2"></i> Farmers Overview
            </div>
            <div>
                <button class="btn btn-sm btn-light" title="Export data">
                    <i class="ri-download-2-line"></i> Export
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="datatable-basic" class="table table-hover table-striped text-nowrap w-100">
                <thead class="table-light">
                    <tr>
                        <th><i class="ri-user-fill me-1 text-primary"></i> Farmer Name</th>
                        <th><i class="ri-file-list-line me-1 text-primary"></i> Registration Number</th>
                        <th><i class="ri-contacts-line me-1 text-primary"></i> Contact Information</th>
                        <th><i class="ri-map-pin-line me-1 text-primary"></i> Location</th>
                        <th><i class="ri-calendar-line me-1 text-primary"></i> Registration Date</th>
                        <th><i class="ri-check-double-line me-1 text-primary"></i> Verification Status</th>
                        <th><i class="ri-tools-line me-1 text-primary"></i> Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($farmers)): ?>
                    <?php foreach ($farmers as $farmer): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2 bg-primary-subtle rounded-circle text-center"
                                    style="width: 32px; height: 32px; line-height: 32px;">
                                    <span
                                        class="fw-medium text-primary"><?php echo strtoupper(substr($farmer->first_name ?? '', 0, 1) . substr($farmer->last_name ?? '', 0, 1)); ?></span>
                                </div>
                                <span class="fw-medium">
                                    <?php echo htmlspecialchars(($farmer->first_name ?? '') . ' ' . ($farmer->last_name ?? '')) ?>
                                </span>
                            </div>
                        </td>
                        <td><span
                                class="badge bg-light text-dark"><?php echo htmlspecialchars($farmer->registration_number ?? '') ?></span>
                        </td>
                        <td>
                            <div>
                                <div class="text-muted small">
                                    <i class="ri-phone-line me-1 text-success"></i>
                                    <?php echo htmlspecialchars($farmer->phone ?? '') ?>
                                </div>
                                <div class="text-muted small">
                                    <i class="ri-mail-line me-1 text-info"></i>
                                    <?php echo htmlspecialchars($farmer->email ?? '') ?>
                                </div>
                            </div>
                        </td>
                        <td><i class="ri-map-pin-fill me-1 text-danger"></i>
                            <?php echo htmlspecialchars($farmer->location ?? '') ?></td>
                        <td><i class="ri-time-line me-1 text-secondary"></i>
                            <?php echo isset($farmer->created_at) ? date('M d, Y', strtotime($farmer->created_at)) : '-' ?>
                        </td>
                        <td>
                            <?php if (isset($farmer->is_verified) && $farmer->is_verified): ?>
                            <span class="badge bg-success-subtle text-success">
                                <i class="ri-check-line me-1"></i> Verified
                            </span>
                            <?php else: ?>
                            <span class="badge bg-warning-subtle text-warning">
                                <i class="ri-time-line me-1"></i> Pending
                            </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-info" title="View Details"
                                    onclick="viewFarmer(<?php echo $farmer->id ?? 0 ?>)">
                                    <i class="ri-eye-line"></i>
                                </button>
                                <?php if (!isset($farmer->is_verified) || !$farmer->is_verified): ?>
                                <button class="btn btn-sm btn-outline-success" title="Verify"
                                    onclick="verifyFarmer(<?php echo $farmer->id ?? 0 ?>)">
                                    <i class="ri-check-line"></i>
                                </button>
                                <?php endif; ?>

                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="text-muted">
                                <i class="ri-information-line fs-3 mb-3 d-block"></i>
                                No farmers found in the database
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
    $('#datatable-basic').DataTable({
        responsive: true,
        order: [
            [5, 'desc']
        ], // Sort by verification status
        language: {
            searchPlaceholder: 'Search farmers...',
            sSearch: '',
            processing: '<i class="ri-loader-2-line fa-spin"></i> Loading...',
        },
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
        ],
        dom: 'Bfrtip',
        buttons: [{
                extend: 'copy',
                className: 'btn btn-sm btn-outline-primary',
                text: '<i class="ri-file-copy-line me-1"></i> Copy'
            },
            {
                extend: 'excel',
                className: 'btn btn-sm btn-outline-success',
                text: '<i class="ri-file-excel-line me-1"></i> Excel'
            },
            {
                extend: 'pdf',
                className: 'btn btn-sm btn-outline-danger',
                text: '<i class="ri-file-pdf-line me-1"></i> PDF'
            },
            {
                extend: 'print',
                className: 'btn btn-sm btn-outline-info',
                text: '<i class="ri-printer-line me-1"></i> Print'
            }
        ]
    });

    // Apply custom styling to the DataTable components
    $('.dataTables_filter input').addClass('form-control form-control-sm');
    $('.dataTables_length select').addClass('form-select form-select-sm');
});
</script>
<?php endif; ?>