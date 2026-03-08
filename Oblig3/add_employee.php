<?php
include 'connect.php'; // Including the script to establish the connection to the database
include 'authenticate_admin.php'; // Including the script to verify the user is logged in as an admin

// Ensuring that the block is only ran when the request method is post, like submitting the form, and not on page load
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Trimming accidental whitespace from string values and ensuring age is an integer
    $name = trim($_POST['name']);
    $age = intval($_POST['age']);
    $job_title = trim($_POST['job_title']);
    $department = trim($_POST['department']);

    // Checking that the file is actually uploaded server side
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $photo = $_FILES['photo'];

        // Checks if the upload directory exists, if not creates it with read write permissions
        $uploadDirectory = "uploads/";
        if (!file_exists($uploadDirectory)) {
            mkdir($uploadDirectory, 0755, true);
        }

        // Ensuring that the file name is unique and clean from special characters before setting the path 
        $photoName = uniqid() . "_" . preg_replace("/[^a-zA-Z0-9\._-]/", "", basename($photo['name']));
        $targetFilePath = $uploadDirectory . $photoName;


        // Moving the file to the target location
        if (move_uploaded_file($photo['tmp_name'], $targetFilePath)) {
            // Prepare and execute the insert query
            $stmt = $conn->prepare("INSERT INTO employees (name, age, job_title, department, photo_path) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sisss", $name, $age, $job_title, $department, $targetFilePath);

            if ($stmt->execute()) {
                // Redirect to view page if successful
                header("Location: view_employees.php");
                exit();
            } else {
                // Display an error if the query fails
                echo '<div class="input-error">Error: ' . htmlspecialchars($stmt->error) . '</div>';
            }

            $stmt->close();
        } else {
            // Error if the file can't be moved, maybe because of permissions or connection, so asking the admin to seek external help if it persists
            echo '<div class="input-error">Error: Could not move the file, try again. If this issue persists, contact IT Department</div>';
        }
    } else {
        // Error in case a server-side issue occurs, or the connection is unstable
        echo '<div class="input-error">Error: There was an error uploading the file, try again</div>';
    }
}
// Closing the connection after use
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Employee</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h1>Add new Employee</h1>
    <form action="add_employee.php" method="post" enctype="multipart/form-data">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="age">Age:</label>
        <input type="number" id="age" name="age" required>

        <label for="job_title">Job Title:</label>
        <input type="text" id="job_title" name="job_title" required>

        <label for="department">Department:</label>
        <input type="text" id="department" name="department" required>

        <label for="photo">Profile Photo:</label>
        <input type="file" id="photo" name="photo" accept="image/*" required>

        <input type="submit" value="Add Employee">
    </form>
        
    <a class="link-spacing" href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>