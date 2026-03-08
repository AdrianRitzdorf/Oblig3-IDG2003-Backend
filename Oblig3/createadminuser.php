<?php
include 'connect.php';

// setting the username and password for the admin user
$username = 'brucewayne'; 
$password = 'StoicBat69';

// hashing the password to secure it in the database
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// technically I shouldn't need a prepared statement here since the file is only used once, but I figured I'd do it for the practice and habit.
// using prepared statement to insert the admin data
$stmt = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");

// binding parameters to prevent SQL injection
$stmt->bind_param("ss", $username, $hashedPassword);

// executing the statement
if ($stmt->execute()) {
    echo "Admin user created successfully";
} else {
    echo "Error: " . htmlspecialchars($stmt->error);
}

// closing the statement and connection
$stmt->close();
$conn->close();
?>