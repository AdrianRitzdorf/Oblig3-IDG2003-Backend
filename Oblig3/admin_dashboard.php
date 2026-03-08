<?php
// Including the admin authenticaiton to see if the user is the admin
include 'authenticate_admin.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <!-- Letting the admin know they are logged in (Just wanted some content for the dashboard)-->
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></h1>
    <h2>You are currently logged in as admin</h2>
    <p>You can add, view, edit or delete employees using the navigation below</p>
    <a class="link-spacing" href="add_employee.php">Add New Employee</a>
    <a class="link-spacing" href="view_employees.php">View Employees</a>
    <a class="link-spacing" href="logout.php">Log out</a>
</body>
</html>