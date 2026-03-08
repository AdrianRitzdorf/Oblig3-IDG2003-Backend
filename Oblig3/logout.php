<?php
// Using session_start() to grab the session details
session_start();

// Using session_destroy() to delete the session details, logging the admin out
session_destroy();

// Navigating to the login screen if the admin wants to log in again
header("Location: login.php");
exit();
?>