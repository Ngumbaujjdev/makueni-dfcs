<?php include "../../../config/config.php" ?>
<?php include "../../../libs/App.php" ?>
<?php if(isset($_POST['deletesend'])){
    $deleteid = $_POST['deletesend'];
    $app=new App;
    $query="DELETE  FROM admin WHERE Admin_id='{$deleteid}'";
    $app->delete_without_path($query);
     
}




?>