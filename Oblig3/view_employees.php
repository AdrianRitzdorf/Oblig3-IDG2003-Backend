<?php
include 'connect.php'; // Including the connection script to connect to the database
include 'authenticate_admin.php'; // Validating that the session belongs to the admin

// While there is no user input I'm trying to always use prepared statements as a good habit
$stmt = $conn->prepare("SELECT * FROM employees"); // Preparing to get all the employee records from the database
$stmt->execute(); // Executing the statement
$result = $stmt->get_result(); // Getting the result to display it in the table
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Employees</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h1>Employees List</h1>
    <a class="link-spacing" href="add_employee.php">Add New Employee</a>
    <a class="link-spacing" href="admin_dashboard.php">Back to Dashboard</a>
    <table>
        <thead>
            <tr>
                <!-- Creating the table headers -->
                <th>ID</th>
                <th>Name</th>
                <th>Age</th>
                <th>Job title</th>
                <th>Department</th>
                <th>Profile Photo</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Checking for any rows returned by the database
            if ($result->num_rows > 0) { 
                // Using a while loop to go through each row returned from the database
                while ($row = $result->fetch_assoc()) {
            ?>
            <tr>
                <!-- Displaying the details, with special character escaping for good practice, also using the echo shorthand for easier readability -->
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['age']) ?></td>
                <td><?= htmlspecialchars($row['job_title']) ?></td>
                <td><?= htmlspecialchars($row['department']) ?></td>
                <td>
                    <!-- Showing the employee photo -->
                    <img src="<?= htmlspecialchars($row['photo_path']) ?>" alt="Profile Photo" width="125" height="125">
                </td>
                <td>
                    <!-- Providing the action links for editing or deleting an employee -->
                    <a href="edit_employee.php?id=<?= htmlspecialchars($row['id']) ?>">Edit</a> |
                    <a href="delete_employee.php?id=<?= htmlspecialchars($row['id']) ?>" onclick="return confirm('Are you sure you want to remove this employee?');">Delete</a>
                </td>
            </tr>
            <?php
            }
            } else {
            // Shows a message if the table is empty, like when the database was just set up and admin hasn't added employees yet
            ?>
            <tr>
                <!-- Using colspan to make the message stretch across the table to form its own line, doesn't really matter but looked better -->
                <td colspan="7">There are currently no employees</td>
            </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</body>
</html>
<?php
// Closing the database connection after use
$conn->close();
?>