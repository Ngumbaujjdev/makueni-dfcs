<?php include "../../../config/config.php" ?>
<?php include "../../../libs/App.php" ?>
<link rel="stylesheet" href="http://localhost/dfcs/assets/libs/apexcharts/apexcharts.css">
<script src="https://cdn.jsdelivr.net/npm/tinycolor2@1.4.1/dist/tinycolor-min.js"></script>
<?php if (isset($_POST['displayData'])): ?>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Admin Table
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table text-nowrap table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Registered On</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $app = new App;
                                $query = "SELECT * FROM admin";
                                $admins = $app->select_all($query);
                                ?>
                                <?php foreach ($admins as $admin): ?>
                                    <tr>
                                        <th scope="row">
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-xs me-2 online avatar-rounded">
                                                    <?php if ($admin->Admin_profile === "male"): ?>
                                                        <img src="http://localhost/dfcs/assets/images/faces/13.jpg"
                                                            alt="img" width="32" height="32" class="rounded-circle" />
                                                    <?php elseif ($admin->Admin_profile === "female"): ?>
                                                        <img src="http://localhost/dfcs/assets/images/faces/2.jpg"
                                                            alt="img" width="32" height="32" class="rounded-circle" />
                                                    <?php else: ?>
                                                        <img src="http://localhost/dfcs/assets/images/faces/21.jpg"
                                                            alt="img" width="32" height="32" class="rounded-circle" />
                                                    <?php endif; ?>
                                                </span><?php echo $admin->Admin_firstname ?>
                                                <?php echo $admin->Admin_lastname ?>
                                            </div>
                                        </th>

                                        <td><?php echo $admin->Admin_email ?></td>
                                        <?php
                                        $created_at = $admin->created_at;
                                        $formatted_date = date('j M, Y', strtotime($created_at));

                                        ?>
                                        <td><?php echo $formatted_date ?></td>
                                        <td>
                                            <div class="hstack gap-2 flex-wrap">
                                                <a href="javascript:void(0);"
                                                    onclick="deleteAdmin(<?php echo $admin->Admin_id ?>)"
                                                    class="text-danger fs-14 lh-1"><i class="ri-delete-bin-5-line"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End:: row-4 -->
<?php endif; ?>