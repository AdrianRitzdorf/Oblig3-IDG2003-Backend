<?php
include 'connect.php';
include 'authenticate_admin.php';

// Ensuring that the ID of the employee is part of the GET request
if (isset($_GET['id'])) {
    // Ensuring the employee ID is an integer 
    $employeeId = intval($_GET['id']);

    // Using prepared statements to get the photo path from the employees table, that matches the employee ID
    $stmt = $conn->prepare("SELECT photo_path FROM employees WHERE id = ?");
    $stmt->bind_param("i", $employeeId); // Replacing the ? with the employee ID
    $stmt->execute(); // Executing the query
    $result = $stmt->get_result(); // Retrieve the result of the query

    // Ensuring only one employee with the ID exists for good practice
    if ($result->num_rows === 1) {
        // Fetching the employee data as an associative array with fetch_assoc()
        $employee = $result->fetch_assoc();
        $photoPath = $employee['photo_path']; // Grabbing the photo path from the employee data

        // If the photo exists and is in the uploads folder, delete it
        if (!empty($photoPath) && file_exists($photoPath)) {
            unlink($photoPath);
        }
    } else {
        // If the employee can't be found, providing an error and a way to return to the employee list
        echo '<div class="input-error">Error: Employee not found</div>';
        echo '<a href="view_employees.php">Back to View Employees</a>';
        exit();
    }

    // Preparing the statement to delete the employee from the database
    $stmt = $conn->prepare("DELETE FROM employees WHERE id = ?");
    $stmt->bind_param("i", $employeeId); // Binding the employee ID to the statement (replacing the ?)

    // Executing the statement and ensuring it was successful
    if ($stmt->execute()) {
        // If successful, navigate to the employee list
        header("Location: view_employees.php");
        exit();
    } else {
        // If there is a server side issue provide the error, escaped with htmlspecialchars for good practice
        echo '<div class="input-error">Error: ' . htmlspecialchars($stmt->error) . '</div>';
        echo '<a href="view_employees.php">Back to View Employees</a>';
        exit();
    }
    // Closing the statement after use
    $stmt->close();
}
// Closing the database connection
$conn->close();
?>