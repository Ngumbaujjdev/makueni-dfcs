<?php 
include "../../config/config.php";
include "../../libs/App.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if (isset($_POST['email'])) {
    $userId = $_SESSION['user_id'];
    $firstname = $_POST['first_name'];
    $lastname = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $location = $_POST['location'];
    $newPassword = $_POST['new_password'] ?? null;
    
    try {
        $app = new App;
        $app->beginTransaction();

        // Verify user is an admin
        $adminCheckQuery = "SELECT a.id FROM admins a 
                          JOIN users u ON a.user_id = u.id 
                          WHERE u.id = $userId";
        $isAdmin = $app->select_one($adminCheckQuery);
        
        if (!$isAdmin) {
            throw new Exception('Unauthorized access');
        }

        // Get existing user data for audit log
        $existingUserQuery = "SELECT * FROM users WHERE id = $userId";
        $existingUser = $app->select_one($existingUserQuery);

        // Build update query
        $updateFields = [
            "username = :username",
            "email = :email",
            "phone = :phone",
            "first_name = :firstname",
            "last_name = :lastname",
            "location = :location"
        ];
        
        $userArr = [
            ":username" => $email,
            ":email" => $email,
            ":phone" => $phone,
            ":firstname" => $firstname,
            ":lastname" => $lastname,
            ":location" => $location,
            ":user_id" => $userId
        ];

        // Add password update if provided
        if (!empty($newPassword)) {
            $updateFields[] = "password = :password";
            $userArr[":password"] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        // Update users table
        $userQuery = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE id = :user_id";
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
            ":activity_type" => 'admin_profile_update',
            ":description" => "Admin profile updated" . (!empty($newPassword) ? " with password change" : "")
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
                'location' => $location,
                'password' => !empty($newPassword) ? 'password_updated' : 'unchanged'
            ])
        ];
        $app->insertWithoutPath($auditQuery, $auditArr);
        
        $app->commit();
        
        echo json_encode([
            'success' => true,
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
            'success' => false,
            'message' => 'Update failed: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Required data not provided'
    ]);
}