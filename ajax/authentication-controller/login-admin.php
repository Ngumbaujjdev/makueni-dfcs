<?php 
include "../../config/config.php";
include "../../libs/App.php";

if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $app = new App;
    
    // Simple query to get user information
    $query = "SELECT * FROM users WHERE email = '{$email}'";
              
    $data = [
        "email" => $email,
        "password" => $password,
    ];
    
    $result = $app->verify_user_login($query, $data);
    
    echo json_encode($result);
    exit();
}
?>