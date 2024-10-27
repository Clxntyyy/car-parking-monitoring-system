<?php
// Include database connection
include_once 'connections/connection.php';

// Establish a connection
$conn = connection();

session_start(); // Start session to manage login status

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and sanitize inputs
    $fname = trim($_POST['fname']);
    $plate_number = trim($_POST['plate_number']);

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM user_tbl WHERE fname = ?");
    $stmt->bind_param("s", $fname);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User found, now verify the password (assuming plate_number is stored as plain text)
        $user = $result->fetch_assoc();

        // Check if plate_number matches the stored value
        if ($plate_number === $user['plate_number']) {
            // Set session variables
            $_SESSION['logged_in'] = true;
            $_SESSION['fname'] = $user['fname'];
            $_SESSION['role'] = $user['access_level'];
            $_SESSION['user_id'] = $user['id'];

            // Redirect based on role
            if ($user['access_level'] == 'admin') {
                header("Location: admin.php");
            } elseif ($user['access_level'] == 'staff') {
                header("Location: staff.php");
            } else {
                header("Location: home.php");
            }
            exit();
        } else {
            echo "Invalid plate number.";
        }
    } else {
        echo "User not found.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>Log In</title>
</head>

<body>
    <h2>Log In</h2>
    <form action="login.php" method="post">
        <label for="fname">First Name (Username):</label>
        <input type="text" id="fname" name="fname" required><br><br>

        <label for="plate_number">Plate Number (Password):</label>
        <input type="password" id="plate_number" name="plate_number" required><br><br>

        <input type="submit" value="Log In">
    </form>

    <!-- Link to go back to Sign Up -->
    <p>Don't have an account? <a href="index.php">Sign Up</a></p>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>