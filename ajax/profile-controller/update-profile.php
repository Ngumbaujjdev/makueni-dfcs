<?php include "../../../config/config.php"?>
<?php include "../../../libs/App.php"?>
<?php
if(isset($_POST["firstname"])){
     $phone=$_POST['phone'];
     $firstname=$_POST['firstname'];
     $lastname=$_POST['lastname'];
     $email=$_POST['email'];
     $bio=$_POST['bio'];
     $city=$_POST['city'];
     $old_email=$_SESSION['AdminEmail'];
     
     // instanciate the class
        $app=new App;
    //   insert the user to the database
       $query = "UPDATE admin SET Admin_phone = :phone,Admin_email= :email, Admin_firstname = :firstname, Admin_lastname = :lastname, Admin_address=:city, Admin_bio=:bio WHERE Admin_email= :oldEmail";

$arr = [
    ":phone" => $phone,
    ":email" => $email,
    ":firstname" => $firstname,
    ":lastname" => $lastname,
    ":oldEmail" => $old_email,
    ":bio" => $bio,
    ":city" => $city
];
         $app->updateToken($query, $arr);
         $_SESSION["AdminEmail"]= $email;
}



?>