<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "corax_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection, if it can't be established post an error and offer to return to dashboard.
if ($conn->connect_error) {
  echo '<div class="input-error">The connection could not be established: ' . $conn->connect_error . '</div>';
  echo '<a href="admin_dashboard.php">Back to Admin Dashboard</a>';
  exit();
}
?>