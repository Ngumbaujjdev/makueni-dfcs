<?php include "../../config/config.php" ?>
<?php include "../../libs/App.php" ?>
<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light"
    data-menu-styles="dark" data-toggled="close">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>
        Baituti - Your Partner in Unforgettable Journeys
    </title>
    <meta name="Description" content="East Africa Travel and Tour Adventures - Baituti Triple Tee Adventures" />
    <meta name="Author" content="Baituti Triple Tee Adventures" />
    <meta name="keywords"
        content="East Africa travel, safaris, beach escapes, personalized tours, adventure travel, responsible tourism, Baituti Triple Tee Adventures" />

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="http://localhost/dfcs/assets/images/favicon/favicon-96x96.png"
        sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="http://localhost/dfcs/assets/images/favicon/favicon.svg" />
    <link rel="shortcut icon" href="http://localhost/dfcs/assets/images/favicon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180"
        href="http://localhost/dfcs/assets/images/favicon/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Baituti Adventures" />
    <link rel="manifest" href="http://localhost/dfcs/assets/images/favicon/site.webmanifest" />
    <!-- font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Choices JS -->
    <script src="http://localhost/dfcs/assets/libs/choices.js/public/assets/scripts/choices.min.js">
    </script>

    <!-- Main Theme Js -->
    <script src="http://localhost/dfcs/assets/js/main.js"></script>

    <!-- Bootstrap Css -->
    <link id="style" href="http://localhost/dfcs/assets/libs/bootstrap/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Style Css -->
    <link href="http://localhost/dfcs/assets/css/styles.min.css" rel="stylesheet">

    <!-- Icons Css -->
    <link href="http://localhost/dfcs/assets/css/icons.css" rel="stylesheet">

    <!-- Node Waves Css -->
    <link href="../assets/libs/node-waves/waves.min.css" rel="stylesheet">

    <!-- Simplebar Css -->
    <link href="http://localhost/dfcs/assets/libs/simplebar/simplebar.min.css" rel="stylesheet">

    <!-- Color Picker Css -->
    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/flatpickr/flatpickr.min.css">
    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/%40simonwep/pickr/themes/nano.min.css">

    <!-- Choices Css -->
    <link rel="stylesheet"
        href="http://localhost/dfcs/assets/libs/choices.js/public/assets/styles/choices.min.css">

    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/quill/quill.snow.css">
    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/quill/quill.bubble.css">

    <!-- Filepond CSS -->
    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/filepond/filepond.min.css">
    <link rel="stylesheet"
        href="http://localhost/dfcs/assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css">
    <link rel="stylesheet"
        href="http://localhost/dfcs/assets/libs/filepond-plugin-image-edit/filepond-plugin-image-edit.min.css">

    <!-- Date & Time Picker CSS -->
    <link rel="stylesheet" href="http://localhost/dfcs/assets/libs/flatpickr/flatpickr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="http://localhost/dfcs/toast/toast.css">

</head>

<body>

    <!-- Start Switcher -->
    <?php include "../includes/start-switcher.php" ?>
    <!-- End Switcher -->


    <!-- Loader -->
    <?php include "../includes/loader.php" ?>
    <!-- Loader -->

    <div class="page">

        <!-- app-header -->
        <?php include "../includes/navigation.php" ?>

        <!-- /app-header -->
        <!-- Start::app-sidebar -->
        <?php include "../includes/sidebar.php" ?>
        <!-- End::app-sidebar -->

        <!-- Start::app-content -->
        <div class="main-content app-content">
            <div class="container-fluid">

                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
                    <h1 class="page-title fw-semibold fs-18 mb-0">Edit post</h1>
                    <div class="ms-md-1 ms-0">
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Blog</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit post</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <!-- Page Header Close -->

                <!-- Start::row-1 -->
                <!-- get the form data -->
                <!-- Start::row-1 -->

                <?php
                $app = new App;
                $post_id = $_GET['post_id'];
                $query = "SELECT * FROM posts WHERE post_id='{$post_id}'";
                $posts = $app->select_all($query);
                ?>
                <?php foreach ($posts as $post): ?>
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card custom-card">
                                <div class="card-header justify-content-between">
                                    <div class="card-title">Update Post</div>
                                </div>
                                <div class="card-body add-products p-0">
                                    <!-- Tabs Navigation -->
                                    <ul class="nav nav-tabs" id="postTabs" role="tablist">
                                        <li class="nav-item">
                                            <button class="nav-link active" data-bs-toggle="tab"
                                                data-bs-target="#basic-info" type="button" role="tab">
                                                <i class="bi bi-info-circle me-1"></i>Basic Info
                                            </button>
                                        </li>
                                        <li class="nav-item">
                                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tags-categories"
                                                type="button" role="tab">
                                                <i class="bi bi-tags me-1"></i>Tags & Categories
                                            </button>
                                        </li>
                                        <li class="nav-item">
                                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#content-start"
                                                type="button" role="tab">
                                                <i class="bi bi-file-text me-1"></i>Content Start
                                            </button>
                                        </li>
                                        <li class="nav-item">
                                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#content-end"
                                                type="button" role="tab">
                                                <i class="bi bi-file-text me-1"></i>Content End
                                            </button>
                                        </li>
                                        <li class="nav-item">
                                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#media"
                                                type="button" role="tab">
                                                <i class="bi bi-images me-1"></i>Media
                                            </button>
                                        </li>
                                    </ul>

                                    <!-- Tab Content -->
                                    <div class="tab-content p-4">
                                        <!-- Basic Info Tab -->
                                        <div class="tab-pane fade show active" id="basic-info" role="tabpanel">
                                            <div class="row gy-3">
                                                <div class="col-xl-12">
                                                    <input type="hidden" id="post-id"
                                                        value="<?php echo isset($_GET['post_id']) ? $_GET['post_id'] : ''; ?>">
                                                    <label class="form-label">Post Title</label>
                                                    <input type="text" class="form-control" id="post-title"
                                                        value="<?php echo $post->post_title ?>">
                                                </div>
                                                <div class="col-xl-12">
                                                    <label class="form-label">Published Status</label>
                                                    <select class="form-control" id="post-status">
                                                        <option value="published"
                                                            <?php echo $post->post_status === 'published' ? 'selected' : '' ?>>
                                                            Published
                                                        </option>
                                                        <option value="draft"
                                                            <?php echo $post->post_status === 'draft' ? 'selected' : '' ?>>
                                                            Draft</option>
                                                    </select>
                                                </div>
                                                <div class="col-xl-12">
                                                    <label class="form-label">Post Remark</label>
                                                    <textarea class="form-control" id="post-remark"
                                                        rows="3"><?php echo $post->post_remarks ?></textarea>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end mt-3">
                                                <button class="btn text-white" id="nextBasic" style="background:#6AA32D;">
                                                    Next <i class="bi bi-arrow-right ms-2"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Tags & Categories Tab -->
                                        <!-- Tags & Categories Tab -->
                                        <div class="tab-pane fade" id="tags-categories" role="tabpanel">
                                            <div class="row gy-3">
                                                <div class="col-xl-12">
                                                    <label for="post-category-add" class="form-label">Category</label>
                                                    <select class="form-control" id="post-category-add">
                                                        <option value="">Select Category</option>
                                                        <?php
                                                        $app = new App;
                                                        $query = "SELECT * FROM categories ORDER BY category_title ASC";
                                                        $categories = $app->select_all($query);
                                                        $current_category = $post->post_category_id;

                                                        if ($categories):
                                                            foreach ($categories as $category):
                                                        ?>
                                                                <option value="<?php echo $category->category_id ?>"
                                                                    <?php echo ($current_category == $category->category_id) ? 'selected' : ''; ?>>
                                                                    <?php echo $category->category_title ?>
                                                                </option>
                                                        <?php
                                                            endforeach;
                                                        endif;
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-xl-12">
                                                    <label class="form-label">Select Tags (Max 3)</label>
                                                    <select class="form-control select2-multiple" id="post-tags-add"
                                                        multiple="multiple">
                                                        <?php
                                                        // Get all available tags
                                                        $query = "SELECT * FROM tags ORDER BY tag_name ASC";
                                                        $tags = $app->select_all($query);

                                                        // Get current post's tags
                                                        $query = "SELECT tag_id FROM post_tags WHERE post_id = {$post->post_id}";
                                                        $current_tags = $app->select_all($query);
                                                        $selected_tag_ids = array_map(function ($tag) {
                                                            return $tag->tag_id;
                                                        }, $current_tags);

                                                        if ($tags):
                                                            foreach ($tags as $tag):
                                                        ?>
                                                                <option value="<?php echo $tag->tag_id ?>"
                                                                    <?php echo (in_array($tag->tag_id, $selected_tag_ids)) ? 'selected' : ''; ?>>
                                                                    <?php echo $tag->tag_name ?>
                                                                </option>
                                                        <?php
                                                            endforeach;
                                                        endif;
                                                        ?>
                                                    </select>
                                                    <small class="form-text text-muted mt-2">Currently selected:
                                                        <span
                                                            id="selected-tags-count"><?php echo count($selected_tag_ids); ?></span>/3
                                                    </small>
                                                </div>

                                                <!-- Display currently selected tags -->
                                                <div class="col-xl-12 mt-3">
                                                    <div id="selected-tags-display" class="d-flex flex-wrap gap-2">
                                                        <!-- Tags will be displayed here -->
                                                        <input type="hidden" id="original-tags"
                                                            value='<?php echo json_encode($selected_tag_ids); ?>'>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between mt-4">
                                                <button class="btn btn-light" id="prevTags">
                                                    <i class="bi bi-arrow-left me-2"></i>Previous
                                                </button>
                                                <button class="btn text-white" id="nextTags" style="background:#6AA32D;">
                                                    Next <i class="bi bi-arrow-right ms-2"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Content Start Tab -->
                                        <div class="tab-pane fade" id="content-start" role="tabpanel">
                                            <div class="row gy-4">
                                                <div class="col-xl-12">
                                                    <label class="form-label">Post Content Start</label>
                                                    <textarea id="summernote"><?php echo $post->post_content ?></textarea>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between mt-3">
                                                <button class="btn btn-light" id="prevContentStart">
                                                    <i class="bi bi-arrow-left me-2"></i>Previous
                                                </button>
                                                <button class="btn text-white" id="nextContentStart"
                                                    style="background:#6AA32D;">
                                                    Next <i class="bi bi-arrow-right ms-2"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Content End Tab -->
                                        <div class="tab-pane fade" id="content-end" role="tabpanel">
                                            <div class="row gy-4">
                                                <div class="col-xl-12">
                                                    <label class="form-label">Post Content End</label>
                                                    <textarea
                                                        id="summernote1"><?php echo $post->post_content_end ?></textarea>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between mt-3">
                                                <button class="btn btn-light" id="prevContentEnd">
                                                    <i class="bi bi-arrow-left me-2"></i>Previous
                                                </button>
                                                <button class="btn text-white" id="nextContentEnd"
                                                    style="background:#6AA32D;">
                                                    Next <i class="bi bi-arrow-right ms-2"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Media Tab -->
                                        <div class="tab-pane fade" id="media" role="tabpanel">
                                            <div class="row gy-4">
                                                <div class="col-xl-6">
                                                    <label class="form-label">Main Post Image</label>
                                                    <input type="file" class="form-control" id="post-image-1"
                                                        accept="image/*">
                                                    <?php if ($post->post_image): ?>
                                                        <div class="mt-2">
                                                            <img src="http://localhost/dfcs/assets/img/blog-images/<?php echo $post->post_image ?>"
                                                                class="img-thumbnail" style="height: 100px;"
                                                                alt="Current Image">
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-xl-6">
                                                    <label class="form-label">Secondary Post Image</label>
                                                    <input type="file" class="form-control" id="post-image-2"
                                                        accept="image/*">
                                                    <?php if ($post->post_image_2): ?>
                                                        <div class="mt-2">
                                                            <img src="http://localhost/dfcs/assets/img/blog-images/<?php echo $post->post_image_2 ?>"
                                                                class="img-thumbnail" style="height: 100px;"
                                                                alt="Current Image">
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between mt-3">
                                                <button class="btn btn-light" id="prevMedia">
                                                    <i class="bi bi-arrow-left me-2"></i>Previous
                                                </button>
                                                <button class="btn text-white" onclick="updatePost()"
                                                    style="background:#6AA32D;">
                                                    Update Post <i class="bi bi-check-lg ms-2"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!--End::row-1 -->
                <?php endforeach; ?>

            </div>
        </div>
        <!-- End::app-content -->


        <!-- footer start -->
        <?php include "../includes/footer.php" ?>
        <!-- Footer End -->

    </div>


    <!-- Scroll To Top -->
    <div class="scrollToTop">
        <span class="arrow"><i class="ri-arrow-up-s-fill fs-20"></i></span>
    </div>
    <div id="responsive-overlay"></div>
    <!-- Scroll To Top -->
    <script src="http://localhost/dfcs/assets/libs/%40popperjs/core/umd/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="http://localhost/dfcs/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Defaultmenu JS -->
    <script src="http://localhost/dfcs/assets/js/defaultmenu.min.js"></script>
    <!-- Node Waves JS-->
    <script src="http://localhost/dfcs/assets/libs/node-waves/waves.min.js"></script>
    <!-- Sticky JS -->
    <script src="http://localhost/dfcs/assets/js/sticky.js"></script>
    <!-- Simplebar JS -->
    <script src="http://localhost/dfcs/assets/libs/simplebar/simplebar.min.js"></script>
    <script src="http://localhost/dfcs/assets/js/simplebar.js"></script>
    <!-- Color Picker JS -->
    <script src="http://localhost/dfcs/assets/libs/%40simonwep/pickr/pickr.es5.min.js"></script>
    <!-- Custom-Switcher JS -->
    <script src="http://localhost/dfcs/assets/js/custom-switcher.min.js"></script>
    <!-- Prism JS -->
    <script src="http://localhost/dfcs/assets/libs/prismjs/prism.js"></script>
    <script src="http://localhost/dfcs/assets/js/prism-custom.js"></script>
    <!-- Custom JS -->
    <script src="http://localhost/dfcs/assets/js/custom.js"></script>
    <!-- summernote -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css">


    <!-- end of footer links -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2 with custom options
            $('#post-tags-add').select2({
                maximumSelectionLength: 3,
                placeholder: 'Select up to 3 tags',
                allowClear: true,
                width: '100%'
            }).on('change', function() {
                updateSelectedTags();
            });

            // Initialize Summernote editors
            $('#summernote, #summernote1').summernote({
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture']],
                    ['view', ['fullscreen', 'codeview']]
                ]
            });

            // Initial update of selected tags
            updateSelectedTags();

            // Tab Navigation
            $('#nextBasic').click(() => {
                if (validateBasicInfo()) {
                    $('#postTabs button[data-bs-target="#tags-categories"]').tab('show');
                }
            });

            $('#prevTags').click(() => $('#postTabs button[data-bs-target="#basic-info"]').tab('show'));

            $('#nextTags').click(() => {
                if (validateTagsCategories()) {
                    $('#postTabs button[data-bs-target="#content-start"]').tab('show');
                }
            });

            $('#prevContentStart').click(() => $('#postTabs button[data-bs-target="#tags-categories"]').tab(
                'show'));

            $('#nextContentStart').click(() => {
                if (validateContentStart()) {
                    $('#postTabs button[data-bs-target="#content-end"]').tab('show');
                }
            });

            $('#prevContentEnd').click(() => $('#postTabs button[data-bs-target="#content-start"]').tab('show'));

            $('#nextContentEnd').click(() => {
                if (validateContentEnd()) {
                    $('#postTabs button[data-bs-target="#media"]').tab('show');
                }
            });

            $('#prevMedia').click(() => $('#postTabs button[data-bs-target="#content-end"]').tab('show'));
        });

        function updateSelectedTags() {
            const selectedTags = $('#post-tags-add').select2('data');
            const tagsContainer = $('#selected-tags-display');
            const selectedCount = selectedTags.length;

            $('#selected-tags-count').text(selectedCount);
            tagsContainer.empty();

            selectedTags.forEach(tag => {
                const tagElement = $(`
            <span class="badge bg-light text-dark p-2 me-2 mb-2">
                ${tag.text}
                <i class="bi bi-x ms-1" style="cursor: pointer;" data-tag-id="${tag.id}"></i>
            </span>
        `);
                tagsContainer.append(tagElement);
            });

            $('.bi-x').on('click', function() {
                const tagId = $(this).data('tag-id');
                const currentValues = $('#post-tags-add').val();
                const newValues = currentValues.filter(value => value !== tagId.toString());
                $('#post-tags-add').val(newValues).trigger('change');
            });
        }

        function validateBasicInfo() {
            if (!$('#post-title').val().trim()) {
                toastr.error('Please enter post title');
                return false;
            }
            if (!$('#post-status').val()) {
                toastr.error('Please select post status');
                return false;
            }
            return true;
        }

        function validateTagsCategories() {
            if (!$('#post-category-add').val()) {
                toastr.error('Please select a category');
                return false;
            }
            const selectedTags = $('#post-tags-add').val();
            if (!selectedTags || selectedTags.length === 0) {
                toastr.error('Please select at least one tag');
                return false;
            }
            if (selectedTags.length > 3) {
                toastr.error('Maximum 3 tags allowed');
                return false;
            }
            return true;
        }

        function validateContentStart() {
            if ($('#summernote').summernote('isEmpty')) {
                toastr.error('Please enter content start');
                return false;
            }
            return true;
        }

        function validateContentEnd() {
            if ($('#summernote1').summernote('isEmpty')) {
                toastr.error('Please enter content end');
                return false;
            }
            return true;
        }

        function updatePost() {
            let formData = new FormData();
            formData.append('postId', $('#post-id').val());
            formData.append('postTitle', $('#post-title').val().trim());
            formData.append('postStatus', $('#post-status').val());
            formData.append('postRemark', $('#post-remark').val().trim());
            formData.append('postCategory', $('#post-category-add').val());
            formData.append('postTags', JSON.stringify($('#post-tags-add').val()));
            formData.append('postContentStart', $('#summernote').summernote('code'));
            formData.append('postContentEnd', $('#summernote1').summernote('code'));
            formData.append('originalTags', $('#original-tags').val());

            if ($('#post-image-1')[0].files[0]) {
                formData.append('postImage1', $('#post-image-1')[0].files[0]);
            }
            if ($('#post-image-2')[0].files[0]) {
                formData.append('postImage2', $('#post-image-2')[0].files[0]);
            }

            $.ajax({
                url: "http://localhost/dfcs/ajax/post-controller/edit-post.php",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    toastr.success('Post updated successfully');
                    // setTimeout(() => {
                    //     window.location.href = 'view-data';
                    // }, 2000);
                },
                error: function() {
                    toastr.error('Error updating post');
                }
            });
        }
    </script>


</body>

</html>