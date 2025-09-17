<?php include "../../../config/config.php" ?>
<?php include "../../../libs/App.php" ?>
<?php include "../../includes/header.php"; ?>
<?php if(isset($_POST['productId'])){
    $product_id=$_POST['productId'];
    $badge=$_POST['badge'];
    $app=new App;
   if($badge== 'new'){
    $existing="existing";
     
        $query="UPDATE  products SET product_badge = :new WHERE product_id = :id";
        $arr=[
           ":new"=>$existing,
           ":id"=>$product_id,   
          ];
         //  if succesiful
         
          $app->updateToken($query,$arr);
}else{
     $new="new";
        $query="UPDATE  products SET product_badge = :new WHERE product_id = :id";
        $arr=[
           ":new"=>$new,
           ":id"=>$product_id,   
          ];
         //  if succesiful
         
          $app->updateToken($query,$arr);

}}
?>
<?php include "../../includes/footer_links.php" ?>