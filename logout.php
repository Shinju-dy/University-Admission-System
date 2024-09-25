<?php
session_start();

// Destroy the session to log out the user
session_destroy();

// Redirect the user back to the portal page
header("Location: portal.php");
exit();
?>
