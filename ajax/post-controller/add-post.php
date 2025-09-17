<?php
include "../../../config/config.php";
include "../../../libs/App.php";

if (isset($_POST["postTitle"])) {
    try {
        $app = new App;

        // Get form data and sanitize
        $postTitle = trim($_POST['postTitle']);
        $postStatus = $_POST['postStatus'];
        $postCategory = $_POST['postCategory'];
        $postRemark = trim($_POST['postRemark']);
        $postContentStart = $_POST['postContentStart'];
        $postContentEnd = $_POST['postContentEnd'];
        $postTags = json_decode($_POST['postTags'], true); // Array of tag IDs

        // Handle first image upload
        $uniqueImage1 = '';
        if (isset($_FILES['postImage1']) && $_FILES['postImage1']['error'] == 0) {
            $image1 = $_FILES['postImage1']['name'];
            $image1_temp = $_FILES['postImage1']['tmp_name'];
            $uniqueImage1 = time() . '_1_' . $image1;
            move_uploaded_file($image1_temp, "../../../assets/img/blog-images/$uniqueImage1");
        }

        // Handle second image upload
        $uniqueImage2 = '';
        if (isset($_FILES['postImage2']) && $_FILES['postImage2']['error'] == 0) {
            $image2 = $_FILES['postImage2']['name'];
            $image2_temp = $_FILES['postImage2']['tmp_name'];
            $uniqueImage2 = time() . '_2_' . $image2;
            move_uploaded_file($image2_temp, "../../../assets/img/blog-images/$uniqueImage2");
        }

        // Begin transaction for post and tags
        $app->beginTransaction();

        // Insert post
        $query = "INSERT INTO posts (
            post_title, 
            post_status, 
            post_category_id,
            post_remarks,
            post_content,
            post_content_end,
            post_image,
            post_image_2
        ) VALUES (
            :postTitle,
            :postStatus,
            :postCategory,
            :postRemark,
            :postContentStart,
            :postContentEnd,
            :postImage1,
            :postImage2
        )";

        $arr = [
            ":postTitle" => $postTitle,
            ":postStatus" => $postStatus,
            ":postCategory" => $postCategory,
            ":postRemark" => $postRemark,
            ":postContentStart" => $postContentStart,
            ":postContentEnd" => $postContentEnd,
            ":postImage1" => $uniqueImage1,
            ":postImage2" => $uniqueImage2
        ];

        // Insert post and get ID
        $app->insertWithoutPath($query, $arr);
        $postId = $app->lastInsertId();

        // Insert tags
        if (!empty($postTags)) {
            foreach ($postTags as $tagId) {
                $queryTags = "INSERT INTO post_tags (post_id, tag_id) VALUES (:postId, :tagId)";
                $arrTags = [
                    ":postId" => $postId,
                    ":tagId" => $tagId
                ];
                $app->insertWithoutPath($queryTags, $arrTags);
            }
        }

        // Commit transaction
        $app->commit();

        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Post added successfully',
            'postId' => $postId
        ]);
    } catch (Exception $e) {
        // Rollback on error
        if (isset($app)) {
            $app->rollBack();
        }
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No data received'
    ]);
}
