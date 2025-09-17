<?php
session_start();

// Clear all the sessions we set during login
unset($_SESSION['user_id']);
unset($_SESSION['email']);
unset($_SESSION['role_id']);

// Destroy the session completely
session_destroy();

// Redirect to homepage
echo "<script>window.location.href='http://localhost/dfcs/'</script>";
exit();
?>