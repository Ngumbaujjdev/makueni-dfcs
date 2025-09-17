<?php include "../../../config/config.php" ?>
<?php include "../../../libs/App.php" ?>
<?php include "../../includes/header.php"; ?>
<?php if(isset($_POST['id'])){
    $post_id=$_POST['id'];
    $status=$_POST['status'];
    $app=new App;
   if($status== 'published'){
    $draft="draft";
     
        $query="UPDATE  posts SET post_status = :new WHERE post_id = :id";
        $arr=[
           ":new"=>$draft,
           ":id"=>$post_id,   
          ];
         //  if succesiful
         
          $app->updateToken($query,$arr);
}else{
     $published="published";
        $query="UPDATE  posts SET post_status = :new WHERE post_id = :id";
        $arr=[
           ":new"=>$published,
           ":id"=>$post_id,   
          ];
         //  if succesiful
         
          $app->updateToken($query,$arr);

}}
?>
<?php include "../../includes/footer_links.php" ?>