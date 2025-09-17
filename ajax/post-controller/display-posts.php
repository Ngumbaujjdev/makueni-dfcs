<?php include "../../../config/config.php" ?>
<?php include "../../../libs/App.php" ?>
<?php if (isset($_POST['displayPosts'])): ?>
    <?php
    $app = new App;
    $query = "SELECT * FROM posts ORDER BY created_at DESC";
    $posts = $app->select_all($query);
    ?>
    <div class="card custom-card">
        <div class="card-header justify-content-between">
            <div class="card-title">
                Posts Overview
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="datatable-basic" class="table table-bordered text-nowrap w-100">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Tags</th>
                            <th>Status</th>
                            <th>Comments</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($posts): ?>
                            <?php foreach ($posts as $post): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="http://localhost/dfcs/assets/img/blog-images/<?php echo $post->post_image ?>"
                                                class="me-2 rounded-1" alt="post image"
                                                style="width: 40px; height: 40px; object-fit: cover;">
                                            <span class="fw-medium"><?php echo $post->post_title ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                        $category_id = $post->post_category_id;
                                        $query = "SELECT category_title FROM categories WHERE category_id = '{$category_id}'";
                                        $category = $app->selectOne($query);
                                        echo $category ? $category->category_title : 'Uncategorized';
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $query = "SELECT t.tag_name 
                                                 FROM tags t 
                                                 JOIN post_tags pt ON t.tag_id = pt.tag_id 
                                                 WHERE pt.post_id = {$post->post_id}";
                                        $tags = $app->select_all($query);
                                        if ($tags):
                                            foreach ($tags as $tag):
                                        ?>
                                                <span class="badge bg-light text-dark me-1"><?php echo $tag->tag_name ?></span>
                                        <?php
                                            endforeach;
                                        endif;
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($post->post_status === "published"): ?>
                                            <span class="badge bg-success-transparent"
                                                onclick="changePostStatus(<?php echo $post->post_id ?>,'<?php echo $post->post_status ?>')">
                                                Published
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning-transparent"
                                                onclick="changePostStatus(<?php echo $post->post_id ?>,'<?php echo $post->post_status ?>')">
                                                Draft
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $query = "SELECT COUNT(*) as count FROM comments WHERE comment_post_id = '{$post->post_id}'";
                                        $comments = $app->selectOne($query);
                                        ?>
                                        <span class="badge bg-light"><?php echo $comments->count ?></span>
                                    </td>
                                    <td>
                                        <?php echo date('M d, Y', strtotime($post->created_at)) ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="view-post?id=<?php echo $post->post_id ?>" class="btn btn-sm btn-light">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                            </a>
                                            <a href="update-data?post_id=<?php echo $post->post_id ?>" class="btn btn-sm btn-info"
                                                title="Edit">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                            <button onclick="deletePost(<?php echo $post->post_id ?>)" class="btn btn-sm btn-danger"
                                                title="Delete">
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
                [5, 'desc']
            ], // Sort by created date by default
            language: {
                searchPlaceholder: 'Search posts...',
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