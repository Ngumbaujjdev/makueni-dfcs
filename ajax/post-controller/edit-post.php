<?php
include "../../../config/config.php";
include "../../../libs/App.php";

if (isset($_POST["postId"])) {
    try {
        $app = new App;

        $postId = $_POST['postId'];
        $postTitle = $_POST['postTitle'];
        $postStatus = $_POST['postStatus'];
        $postCategory = $_POST['postCategory'];
        $postRemark = $_POST['postRemark'];
        $postContentStart = $_POST['postContentStart'];
        $postContentEnd = $_POST['postContentEnd'];
        $newTags = json_decode($_POST['postTags'], true);

        // First, verify post exists
        $query = "SELECT post_image, post_image_2 FROM posts WHERE post_id = '{$postId}'";
        $existingPost = $app->select_one($query);

        if (!$existingPost) {
            echo json_encode(['success' => false, 'message' => 'Post not found']);
            exit;
        }

        // Handle first image
        if (isset($_FILES['postImage1']) && $_FILES['postImage1']['error'] === 0) {
            $image1 = $_FILES['postImage1']['name'];
            $image1_temp = $_FILES['postImage1']['tmp_name'];
            $uniqueImage1 = time() . '_1_' . $image1;
            move_uploaded_file($image1_temp, "../../../assets/img/blog-images/$uniqueImage1");
        } else {
            $uniqueImage1 = $existingPost->post_image;
        }

        // Handle second image
        if (isset($_FILES['postImage2']) && $_FILES['postImage2']['error'] === 0) {
            $image2 = $_FILES['postImage2']['name'];
            $image2_temp = $_FILES['postImage2']['tmp_name'];
            $uniqueImage2 = time() . '_2_' . $image2;
            move_uploaded_file($image2_temp, "../../../assets/img/blog-images/$uniqueImage2");
        } else {
            $uniqueImage2 = $existingPost->post_image_2;
        }

        // First update the post
        $query = "UPDATE posts SET 
            post_title = :title,
            post_status = :status,
            post_category_id = :category,
            post_remarks = :remark,
            post_content = :contentStart,
            post_content_end = :contentEnd,
            post_image = :image1,
            post_image_2 = :image2
            WHERE post_id = :id";

        $arr = [
            ':title' => $postTitle,
            ':status' => $postStatus,
            ':category' => $postCategory,
            ':remark' => $postRemark,
            ':contentStart' => $postContentStart,
            ':contentEnd' => $postContentEnd,
            ':image1' => $uniqueImage1,
            ':image2' => $uniqueImage2,
            ':id' => $postId
        ];

        $app->updateToken($query, $arr);

        // After updating post, handle tags
        // First remove existing tags
        $query = "DELETE FROM post_tags WHERE post_id = '{$postId}'";
        $app->delete_without_path($query);

        // Then add new tags
        if (!empty($newTags)) {
            foreach ($newTags as $tagId) {
                if (!empty($tagId)) {
                    $query = "INSERT INTO post_tags (post_id, tag_id) VALUES ('{$postId}', '{$tagId}')";
                    $app->insertRecord($query);
                }
            }
        }

        echo json_encode(['success' => true, 'message' => 'Post updated successfully']);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error updating post: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No post ID provided']);
}
