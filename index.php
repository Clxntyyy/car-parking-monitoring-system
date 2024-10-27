<?php
// Include database connection
include_once 'connections/connection.php';

// Establish a connection
$conn = connection();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and sanitize inputs
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $contact_no = trim($_POST['contact_no']);
    $email = trim($_POST['email']);
    $plate_number = trim($_POST['plate_number']);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit;
    }

    // Use `fname` as the username and `plate_number` as the password
    $username = $fname; // Set `username` as `fname`
    // Hash the plate number as the password (if needed)
    // Assuming plate_number is used directly in this case; adjust as necessary.
    
    // Prepare an SQL statement for inserting into user_tbl
    $stmt = $conn->prepare("INSERT INTO user_tbl (fname, lname, contact_no, email, plate_number) 
                             VALUES (?, ?, ?, ?, ?)");
    
    if ($stmt) {
        // Bind parameters
        $stmt->bind_param("sssss", $fname, $lname, $contact_no, $email, $plate_number);

        // Execute the statement
        if ($stmt->execute()) {
            echo "New record created successfully";
            // Redirect to login page after successful signup
            header("Location: login.php");
            exit();
        } else {
            echo "Error: Could not execute query: " . htmlspecialchars($stmt->error);
        }
        
        // Close statement
        $stmt->close();
    } else {
        echo "Error preparing statement: " . htmlspecialchars($conn->error);
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
</head>
<body>
    <h2>Sign Up</h2>
    <form action="signup.php" method="post">
        <label for="fname">First Name:</label>
        <input type="text" id="fname" name="fname" required><br><br>

        <label for="lname">Last Name:</label>
        <input type="text" id="lname" name="lname" required><br><br>

        <label for="contact_no">Contact Number:</label>
        <input type="text" id="contact_no" name="contact_no" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="plate_number">Plate Number:</label>
        <input type="text" id="plate_number" name="plate_number" required><br><br>

        <input type="submit" value="Sign Up">
    </form>

    <p>Do you have an account? <a href="login.php"><button>Log In</button></a></p>
</body>
</html>