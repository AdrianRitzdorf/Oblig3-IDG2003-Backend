<?php
// Access the stored session information
session_start();

// Check if user is the admin, if it isn't equal (!) to the admin name, redirect them to the login page
if (!isset($_SESSION['admin_username'])) {
    header("Location: login.php");
    exit();
}
?>