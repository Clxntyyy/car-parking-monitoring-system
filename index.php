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
    $vehicle_type = trim($_POST['vehicle_type']);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit;
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert into user_tbl
        $stmt_user = $conn->prepare("INSERT INTO user_tbl (fname, lname, contact_no, email, access_level) 
                                     VALUES (?, ?, ?, ?, 'customer')");
        if ($stmt_user) {
            $stmt_user->bind_param("ssss", $fname, $lname, $contact_no, $email);
            $stmt_user->execute();
            $user_id = $stmt_user->insert_id; // Get the inserted user ID
            $stmt_user->close();
        } else {
            throw new Exception("Error preparing statement for user_tbl: " . $conn->error);
        }

        // Get the type_id from vehicletype_tbl
        $stmt_vehicletype = $conn->prepare("SELECT type_id FROM vehicletype_tbl WHERE type_name = ?");
        if ($stmt_vehicletype) {
            $stmt_vehicletype->bind_param("s", $vehicle_type);
            $stmt_vehicletype->execute();
            $stmt_vehicletype->bind_result($type_id);
            $stmt_vehicletype->fetch();
            $stmt_vehicletype->close();
        } else {
            throw new Exception("Error preparing statement for vehicletype_tbl: " . $conn->error);
        }

        if (!$type_id) {
            throw new Exception("Invalid vehicle type selected.");
        }

        // Insert into vehicle_tbl
        $stmt_vehicle = $conn->prepare("INSERT INTO vehicle_tbl (plate_number, user_id, vehicle_type_id) 
                                        VALUES (?, ?, ?)");
        if ($stmt_vehicle) {
            $stmt_vehicle->bind_param("sii", $plate_number, $user_id, $type_id);
            $stmt_vehicle->execute();
            $stmt_vehicle->close();
        } else {
            throw new Exception("Error preparing statement for vehicle_tbl: " . $conn->error);
        }

        // Commit transaction
        $conn->commit();
        echo "New record created successfully";
        // Redirect to login page after successful signup
        header("Location: login.php");
        exit();
    } catch (Exception $e) {
        // Rollback transaction if any error occurs
        $conn->rollback();
        echo "Error: " . $e->getMessage();
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
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>Sign Up</title>
</head>

<body>
    <div class="container">

        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center mt-5">Sign Up</h2>
                <form action="index.php" method="post" class="mt-4">
                    <div class="form-group">
                        <label for="fname">First Name:</label>
                        <input type="text" id="fname" name="fname" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="lname">Last Name:</label>
                        <input type="text" id="lname" name="lname" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="contact_no">Contact Number:</label>
                        <input type="text" id="contact_no" name="contact_no" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="plate_number">Plate Number:</label>
                        <input type="text" id="plate_number" name="plate_number" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="vehicle_type">Vehicle Type:</label>
                        <select id="vehicle_type" name="vehicle_type" class="form-control" required>
                            <option value="car">Car</option>
                            <option value="motor">Motor</option>
                            <option value="tricycle">Tricycle</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Sign Up</button>
                </form>
                <p class="text-center mt-3">Do you have an account? <a href="login.php">Log In</a></p>
            </div>
        </div>
    </div>
</body>

</html>