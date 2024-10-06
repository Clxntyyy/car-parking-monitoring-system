<?php
session_start();

include_once(__DIR__ . "/../connections/connection.php");
$con = connection();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $plate_number = $_POST['pnumber'];
    $phone_number = $_POST['phone'];
    $time_parked = date("Y-m-d H:i:s"); // Set the current date and time for 'time_parked'
    $access = "user"; // Default access level for signup

    // Check if the plate number already exists
    $checkSql = "SELECT * FROM tbl_users WHERE plate_number = ?";
    $stmtCheck = $con->prepare($checkSql);
    $stmtCheck->bind_param("s", $plate_number);
    $stmtCheck->execute();
    $checkResult = $stmtCheck->get_result();

    if ($checkResult->num_rows > 0) {
        echo "<script>alert('Plate number already exists!');</script>";
    } else {
        // Insert new user into the database
        $signupSql = "INSERT INTO tbl_users (user_name, plate_number, time_parked, phone_number, access) 
                      VALUES (?, ?, ?, ?, ?)";
        $stmtSignup = $con->prepare($signupSql);
        $stmtSignup->bind_param("sssss", $name, $plate_number, $time_parked, $phone_number, $access);
        $stmtSignup->execute();

        // Redirect to login page after successful registration
        if ($stmtSignup->affected_rows > 0) {
            echo "<script>alert('Sign up successful! You can now log in.');</script>";
            header("Location: login.php");
            exit();
        } else {
            echo "<script>alert('Error signing up! Please try again.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="./assets/styles/signup.css" />
    <title>CPMS — Sign Up</title>
</head>

<body>
    <div class="wrapper">
        <form action="" method="post">
            <div class="form-header">
                <img src="./assets/images/logo.jpg" alt="cpms-logo" width="60" height="60" />
                <div>
                    <h3>CPMS — Sign Up</h3>
                    <p>Create an account to park</p>
                </div>
            </div>
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" required />
            </div>
            <div class="form-group">
                <label for="pnumber">Plate Number</label>
                <input type="text" name="pnumber" id="pnumber" placeholder="1234-ABCD" required />
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" name="phone" id="phone" placeholder="09XXXXXXXXX" required />
            </div>
            <button type="submit">Sign Up</button>
        </form>
    </div>
</body>

</html>