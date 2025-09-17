<?php include "../../config/config.php"?>
<?php include "../../libs/App.php"?>
<?php 
if (isset($_POST['email'])) {
    $email = $_POST['email'];

    // Customize this query to check email availability in your database
    $app=new App;
    $query = "SELECT * FROM users WHERE email = '$email'";

  $email_exists=$app->count($query);

    if ($email_exists > 0) {
        // Email already exists
        echo "not_available";
    } else {
        // Email is available
        echo "available";
    }
}