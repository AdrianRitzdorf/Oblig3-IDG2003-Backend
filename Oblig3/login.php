<?php
// Starting a session to manage the admin login
session_start();
// Including the connect script to establish the database connection and check the stored password hash
include 'connect.php';

// Making sure the request is POST, since the data is login information
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the username and password from the request
    $username = $_POST['username'];
    $password = $_POST['password'];


    // Prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username); // Binding the username to prevent SQL injection
    $stmt->execute(); // Execute the statement to find the username in the admin table
    $result = $stmt->get_result(); // Get the result from the query

    // Check if the user exists
    if ($result->num_rows > 0) {
        // Using fetch_assoc() to get the results as an associative array
        $row = $result->fetch_assoc();
        
        // Verify the password hash against the stored password hash in the database with password_verify()
        if (password_verify($password, $row['password'])) {
            // If the password matches set the session variable to mark the user as logged in
            $_SESSION['admin_username'] = $username; // Storing the username in the session to track the admin being logged in
            // Navigate to the admin dashboard and exit the script 
            header("Location: admin_dashboard.php");
            exit();
        } else {
            // Error if the wrong password is entered
            echo '<div class="input-error">Incorrect password, try again</div>';
        }
    } else {
        // Error if the wrong username is entered
        echo '<div class="input-error">The username does not exist, try again</div>';
    }
    // Closing the statement after use
    $stmt->close();
}
// Closing the connection after use
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h1>Admin Login</h1>
    <form action="login.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" value="Login">
    </form>
</body>
</html>