<?php
// logout.php

session_start();

// Clear specific session data
unset($_SESSION['AdminEmail']);


echo "<script>window.location.href='http://localhost/dfcs/'</script>";

exit();
