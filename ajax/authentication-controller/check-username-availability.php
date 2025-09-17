<?php include "../../config/config.php"?>
<?php include "../../libs/App.php"?>
<?php 
if (isset($_POST['username'])) {
    $username = $_POST['username'];
    
    // Customize this query to check username availability in your database
    $app = new App;
    $query = "SELECT * FROM users WHERE username = '$username'";
    $username_exists = $app->count($query);
    
    if ($username_exists > 0) {
        // Username already exists
        echo "not_available";
    } else {
        // Username is available
        echo "available";
    }
}
?>