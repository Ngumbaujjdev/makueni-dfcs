<?php include "../../../config/config.php" ?>
<?php include "../../../libs/App.php" ?>
<?php if (isset($_POST['displayPackages'])): ?>
    <?php
    $app = new App;
    $query = "SELECT tp.*, tt.type_name, l.location_name, d.discount_percentage, d.discount_name 
              FROM tour_packages tp
              LEFT JOIN tour_types tt ON tp.tour_type_id = tt.tour_type_id
              LEFT JOIN locations l ON tp.location_id = l.location_id
              LEFT JOIN discounts d ON tp.discount_id = d.discount_id
              ORDER BY tp.created_at DESC";
    $packages = $app->select_all($query);
    ?>
    <div class="card custom-card">
        <div class="card-header justify-content-between">
            <div class="card-title">
                Tour Packages Overview
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="datatable-basic" class="table table-bordered text-nowrap w-100">
                    <thead>
                        <tr>
                            <th>Package Details</th>
                            <th>Location & Type</th>
                            <th>Duration</th>
                            <th>Pricing</th>
                            <th>Status</th>
                            <th>Reviews</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($packages): ?>
                            <?php foreach ($packages as $package): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="http://localhost/dfcs/assets/img/package-images/<?php echo $package->featured_image ?>"
                                                class="me-2 rounded-1" alt="package image"
                                                style="width: 60px; height: 40px; object-fit: cover;">
                                            <div>
                                                <span class="fw-medium d-block"><?php echo $package->title ?></span>
                                                <?php if ($package->discount_percentage): ?>
                                                    <span class="badge bg-danger-transparent">
                                                        <?php echo $package->discount_name ?> -
                                                        <?php echo $package->discount_percentage ?>% Off
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <span class="d-block"><i
                                                    class="fa-solid fa-location-dot me-1"></i><?php echo $package->location_name ?></span>
                                            <span class="text-muted small"><i
                                                    class="fa-solid fa-tag me-1"></i><?php echo $package->type_name ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <span class="fw-semibold"><?php echo $package->duration_days ?> Days</span>
                                            <span class="d-block text-muted small"><?php echo $package->duration_nights ?>
                                                Nights</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <span
                                                class="fw-semibold">KSH <?php echo number_format($package->display_price, 2) ?></span>
                                            <?php if ($package->group_discount_percentage > 0): ?>
                                                <span class="d-block text-success small">
                                                    <i class="fa-solid fa-users me-1"></i>Group discount:
                                                    <?php echo $package->group_discount_percentage ?>%
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                        $statusClasses = [
                                            'active' => 'bg-success-transparent',
                                            'inactive' => 'bg-danger-transparent',
                                            'draft' => 'bg-warning-transparent'
                                        ];
                                        $statusClass = $statusClasses[$package->status] ?? 'bg-secondary-transparent';
                                        ?>
                                        <span class="badge <?php echo $statusClass ?> cursor-pointer"
                                            onclick="changePackageStatus(<?php echo $package->package_id ?>,'<?php echo $package->status ?>')">
                                            <?php echo ucfirst($package->status) ?>
                                        </span>
                                        <?php if ($package->difficulty_level): ?>
                                            <span class="d-block badge bg-light text-dark mt-1">
                                                <?php echo ucfirst($package->difficulty_level) ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $query = "SELECT AVG(overall_rating) as avg_rating, COUNT(*) as count 
                                                 FROM package_reviews 
                                                 WHERE package_id = '{$package->package_id}' AND status = 'approved'";
                                        $reviews = $app->selectOne($query);
                                        $avgRating = number_format($reviews->avg_rating ?? 0, 1);
                                        ?>
                                        <div>
                                            <div class="text-warning mb-1">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star<?php echo $i <= $avgRating ? '' : '-o' ?> small"></i>
                                                <?php endfor; ?>
                                            </div>
                                            <span class="badge bg-light"><?php echo $reviews->count ?> reviews</span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php echo date('M d, Y', strtotime($package->created_at)) ?>
                                        <span class="d-block text-muted small">
                                            <?php echo date('h:i A', strtotime($package->created_at)) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="view-package?id=<?php echo $package->package_id ?>"
                                                class="btn btn-sm btn-light" title="View Details">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                            <a href="update-package?package_id=<?php echo $package->package_id ?>"
                                                class="btn btn-sm btn-info" title="Edit Package">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                            <button onclick="deletePackage(<?php echo $package->package_id ?>)"
                                                class="btn btn-sm btn-danger" title="Delete Package">
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
<?php endif; ?>

<script>
    $(document).ready(function() {
        $('#datatable-basic').DataTable({
            responsive: true,
            order: [
                [6, 'desc']
            ], // Sort by created date by default
            language: {
                searchPlaceholder: 'Search packages...',
                sSearch: '',
            },
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'copy',
                    className: 'btn btn-sm btn-light'
                },
                {
                    extend: 'excel',
                    className: 'btn btn-sm btn-light'
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-sm btn-light'
                },
                {
                    extend: 'print',
                    className: 'btn btn-sm btn-light'
                }
            ]
        });
    });
</script>