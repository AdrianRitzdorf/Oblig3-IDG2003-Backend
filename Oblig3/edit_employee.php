<?php
include 'connect.php';
include 'authenticate_admin.php';

// Retrieving the employee details to prefill the form 
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    // Ensuring the ID remains an integer
    $employeeId = intval($_GET['id']);

    // Preparing the statement to select all fields from the selected employee ID
    $stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
    $stmt->bind_param("i", $employeeId); // Adding the ID of the employee to the statement
    $stmt->execute(); // Executing the query
    $result = $stmt->get_result(); // Getting the information from the query

    // Ensuring only one employee with the ID was selected
    if ($result->num_rows == 1) {
        $employee = $result->fetch_assoc(); // Using fetch_assoc() to to get the results in an associative array
    } else {
        // If there was no employee matching the ID provide an error
        echo '<div class="input-error">Error: Employee not found</div>';
        exit();
    }
    
    // Closing the statement after use
    $stmt->close(); 
}

// Using the POST request from the form to update employee details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ensuring the data is valid by trimming the strings, and age and ID are integers
    $employeeId = intval($_POST['id']);
    $name = trim($_POST['name']);
    $age = intval($_POST['age']);
    $job_title = trim($_POST['job_title']);
    $department = trim($_POST['department']);
    // Setting the photoPath variable to null, it is checked later to see if the image should be updated or not
    $photoPath = null;

    // Retrieving the current photo path for the employee in case the image is changed
    $stmt = $conn->prepare("SELECT photo_path FROM employees WHERE id = ?");
    $stmt->bind_param("i", $employeeId); // Binding the ID of the employee to the statement
    $stmt->execute(); 
    $result = $stmt->get_result();

    // Ensuring only one employee matches the ID
    if ($result->num_rows == 1) {
        $employee = $result->fetch_assoc();
        $currentPhotoPath = $employee['photo_path']; // Storing the current photo path in case it is to be deleted
    } else {
        // If the employee with the ID can't be found, provide an error and exit
        echo '<div class="input-error">Error: Employee not found</div>';
        exit();
    }

    // Closing the statement after use
    $stmt->close();

    // If a new photo was uploaded go through the upload process again
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photo = $_FILES['photo'];

        // Setting the location for the image
        $uploadDirectory = "uploads/";

        // Ensuring the image name is clean of special characters and the name is unique
        $photoName = uniqid() . "_" . preg_replace("/[^a-zA-Z0-9\._-]/", "", basename($photo['name']));
        $targetFilePath = $uploadDirectory . $photoName;

        // Moving the new image to the uploads folder
        if (move_uploaded_file($photo['tmp_name'], $targetFilePath)) {
            // Deleting the old photo if a new photo was successfully added
            if (!empty($currentPhotoPath) && file_exists($currentPhotoPath)) {
                unlink($currentPhotoPath);
            }
            // Provide the new path for the image to the database
            $photoPath = $targetFilePath;
        } else {
            // In case something goes wrong, provide an error and stop the execution
            echo '<div class="input-error">Error: File update failed. Try again</div>';
            exit();
        }
    }

    // Preparing and binding the statement to update the employee data, either with a new photo or without
    if ($photoPath) {
        // If a new photo was added update the photo path with the other details
        $stmt = $conn->prepare("UPDATE employees SET name = ?, age = ?, job_title = ?, department = ?, photo_path = ? WHERE id = ?");
        $stmt->bind_param("sisssi", $name, $age, $job_title, $department, $photoPath, $employeeId);
    } else {
        // If a new photo was not added, then update the other details without the photo path
        $stmt = $conn->prepare("UPDATE employees SET name = ?, age = ?, job_title = ?, department = ? WHERE id = ?");
        $stmt->bind_param("sissi", $name, $age, $job_title, $department, $employeeId);
    }

    // Execute the statement and handle the result
    if ($stmt->execute()) {
        // If successful navigate to view employees
        header("Location: view_employees.php");
        exit();
    } else {
        // If something went wrong the error is presented, escaped with htmlspecialchars for good practice
        echo '<div class="input-error">Error: ' . htmlspecialchars($stmt->error) . '</div>';
    }

    // Closing the statement after use
    $stmt->close();
}
// Closing the database connection after use
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Employee</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h1>Edit Employee</h1>
    <a class="link-spacing" href="view_employees.php">Back to Employees</a>
    <form action="edit_employee.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($employee['id']); ?>">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($employee['name']); ?>" required>

        <label for="age">Age:</label>
        <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($employee['age']); ?>" required>

        <label for="job_title">Job Title:</label>
        <input type="text" id="job_title" name="job_title" value="<?php echo htmlspecialchars($employee['job_title']); ?>" required>

        <label for="department">Department:</label>
        <input type="text" id="department" name="department" value="<?php echo htmlspecialchars($employee['department']); ?>" required>

        <label for="photo">Profile Photo:</label>
        <input type="file" id="photo" name="photo" accept="image/*">

        <input type="submit" value="Update Employee">
    </form>
</body>
</html>