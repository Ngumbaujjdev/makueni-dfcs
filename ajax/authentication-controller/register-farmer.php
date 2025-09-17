<?php 
include "../../config/config.php";
include "../../libs/App.php";

if (isset($_POST['email'])) {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $location = $_POST['location'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    try {
        $app = new App;
        $app->beginTransaction();

        // Generate registration number
        $regNumber = 'FRM' . date('Y') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

        // Insert into users table with all common fields
        $userQuery = "INSERT INTO users (username, email, phone, password, role_id, first_name, last_name, location) 
                     VALUES (:username, :email, :phone, :password, :role_id, :firstname, :lastname, :location)";
        $userArr = [
            ":username" => $email,
            ":email" => $email,
            ":phone" => $phone,
            ":password" => $password,
            ":role_id" => 1,
            ":firstname" => $firstname,
            ":lastname" => $lastname,
            ":location" => $location
        ];
        
        $app->insertWithoutPath($userQuery, $userArr);
        $userId = $app->lastInsertId();

        // Insert into farmers table with registration number only
        $farmerQuery = "INSERT INTO farmers (user_id, registration_number) 
                       VALUES (:user_id, :registration_number)";
        $farmerArr = [
            ":user_id" => $userId,
            ":registration_number" => $regNumber
        ];
        
        $app->insertWithoutPath($farmerQuery, $farmerArr);
        $farmerId = $app->lastInsertId();

        // Log the activity
        $activityQuery = "INSERT INTO activity_logs (user_id, activity_type, description) 
                         VALUES (:user_id, :activity_type, :description)";
        $activityArr = [
            ":user_id" => $userId,
            ":activity_type" => 'registration',
            ":description" => "New farmer registration: $firstname $lastname ($regNumber)"
        ];
        $app->insertWithoutPath($activityQuery, $activityArr);

        // Add audit log for user creation
        $userAuditQuery = "INSERT INTO audit_logs (user_id, action_type, table_name, record_id, new_values) 
                          VALUES (:user_id, :action_type, :table_name, :record_id, :new_values)";
        $userAuditArr = [
            ":user_id" => $userId,
            ":action_type" => 'create',
            ":table_name" => 'users',
            ":record_id" => $userId,
            ":new_values" => json_encode([
                'email' => $email,
                'phone' => $phone,
                'first_name' => $firstname,
                'last_name' => $lastname,
                'location' => $location,
                'role_id' => 1
            ])
        ];
        $app->insertWithoutPath($userAuditQuery, $userAuditArr);

        // Add audit log for farmer creation
        $farmerAuditQuery = "INSERT INTO audit_logs (user_id, action_type, table_name, record_id, new_values) 
                            VALUES (:user_id, :action_type, :table_name, :record_id, :new_values)";
        $farmerAuditArr = [
            ":user_id" => $userId,
            ":action_type" => 'create',
            ":table_name" => 'farmers',
            ":record_id" => $farmerId,
            ":new_values" => json_encode([
                'registration_number' => $regNumber,
                'user_id' => $userId
            ])
        ];
        $app->insertWithoutPath($farmerAuditQuery, $farmerAuditArr);
        
        $app->commit();
        
        $_SESSION['user_id'] = $userId;
        $_SESSION['email'] = $email;
        $_SESSION['role_id'] = 1;
   

        echo json_encode([
            'status' => 'success',
            'reg_number' => $regNumber
        ]);
        
    } catch (Exception $e) {
        $app->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Registration failed: ' . $e->getMessage()
        ]);
    }
}
?>