<?php 
include "../../config/config.php";
include "../../libs/App.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

if (isset($_POST['email'])) {
    $userId = $_SESSION['user_id'];
    $firstname = $_POST['first_name'];
    $lastname = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $location = $_POST['location'];
    
    try {
        $app = new App;
        $app->beginTransaction();

        // Get existing user data for audit log
        $existingUserQuery = "SELECT * FROM users WHERE id = $userId";
        $existingUser = $app->select_one($existingUserQuery);

        // Update users table with all common fields
        $userQuery = "UPDATE users SET 
                    username = :username,
                    email = :email,
                    phone = :phone,
                    first_name = :firstname,
                    last_name = :lastname,
                    location = :location
                    WHERE id = :user_id";
        
        $userArr = [
            ":username" => $email,
            ":email" => $email,
            ":phone" => $phone,
            ":firstname" => $firstname,
            ":lastname" => $lastname,
            ":location" => $location,
            ":user_id" => $userId
        ];
        
        $app->updateToken($userQuery, $userArr);

        // Handle profile picture if uploaded
        if (!empty($_FILES['profile_picture']['name'])) {
            $fileName = time() . '_' . $_FILES['profile_picture']['name'];
            $uploadDir = '../../uploads/profiles/';
            
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $uploadPath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadPath)) {
                $pictureQuery = "UPDATE users SET profile_picture = :profile_picture WHERE id = :user_id";
                $pictureArr = [
                    ":profile_picture" => 'uploads/profiles/' . $fileName,
                    ":user_id" => $userId
                ];
                $app->updateToken($pictureQuery, $pictureArr);
            }
        }

        // Log activity
        $activityQuery = "INSERT INTO activity_logs (user_id, activity_type, description) 
                         VALUES (:user_id, :activity_type, :description)";
        $activityArr = [
            ":user_id" => $userId,
            ":activity_type" => 'profile_update',
            ":description" => "Profile updated by user"
        ];
        $app->insertWithoutPath($activityQuery, $activityArr);

        // Log audit
        $auditQuery = "INSERT INTO audit_logs (user_id, action_type, table_name, record_id, old_values, new_values) 
                      VALUES (:user_id, :action_type, :table_name, :record_id, :old_values, :new_values)";
        $auditArr = [
            ":user_id" => $userId,
            ":action_type" => 'update',
            ":table_name" => 'users',
            ":record_id" => $userId,
            ":old_values" => json_encode([
                'email' => $existingUser->email,
                'phone' => $existingUser->phone,
                'first_name' => $existingUser->first_name,
                'last_name' => $existingUser->last_name,
                'location' => $existingUser->location
            ]),
            ":new_values" => json_encode([
                'email' => $email,
                'phone' => $phone,
                'first_name' => $firstname,
                'last_name' => $lastname,
                'location' => $location
            ])
        ];
        $app->insertWithoutPath($auditQuery, $auditArr);
        
        $app->commit();
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'data' => [
                'first_name' => $firstname,
                'last_name' => $lastname,
                'email' => $email,
                'phone' => $phone,
                'location' => $location
            ]
        ]);
        
    } catch (Exception $e) {
        $app->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Update failed: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Required data not provided'
    ]);
}
?>