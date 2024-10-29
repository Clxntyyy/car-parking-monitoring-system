<?php
// Include database connection
include_once 'connections/connection.php';

// Establish a connection
$conn = connection();

session_start(); // Start session to manage login status

$error_message = '';

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
        // User found, now verify the plate number
        $user = $result->fetch_assoc();
        $user_id = $user['user_id'];

        // Check if plate_number matches the stored value in vehicle_tbl
        $stmt_vehicle = $conn->prepare("SELECT * FROM vehicle_tbl WHERE plate_number = ? AND user_id = ?");
        $stmt_vehicle->bind_param("si", $plate_number, $user_id);
        $stmt_vehicle->execute();
        $result_vehicle = $stmt_vehicle->get_result();

        if ($result_vehicle->num_rows > 0) {
            // Plate number matches, set session variables
            $_SESSION['logged_in'] = true;
            $_SESSION['fname'] = $user['fname'];
            $_SESSION['lname'] = $user['lname'];
            $_SESSION['contact_no'] = $user['contact_no'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['access_level'] = $user['access_level'];
            $_SESSION['user_id'] = $user['user_id'];

            // Redirect based on role
            switch ($user['access_level']) {
                case 'admin':
                    header("Location: admin.php");
                    break;
                case 'staff':
                    header("Location: staff.php");
                    break;
                default:
                    header("Location: home.php");
                    break;
            }
            exit();
        } else {
            $error_message = "Invalid plate number.";
        }
    } else {
        $error_message = "User not found.";
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
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h2>Log In</h2>
                    </div>
                    <div class="card-body">
                        <form action="login.php" method="post">
                            <div class="form-group">
                                <label for="fname">First Name (Username):</label>
                                <input type="text" id="fname" name="fname" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="plate_number">Plate Number (Password):</label>
                                <input type="password" id="plate_number" name="plate_number" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Log In</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <p>Don't have an account? <a href="index.php">Sign Up</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- components/errorModal.php -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                </div>
                <div class="modal-body" id="errorModalBody">
                    <!-- Error message will be injected here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function showErrorModal(message) {
            $('#errorModalBody').text(message);
            $('#errorModal').modal('show');
        }

        <?php if (!empty($error_message)): ?>
            $(document).ready(function() {
                showErrorModal('<?php echo $error_message; ?>');
            });
        <?php endif; ?>
    </script>
</body>

</html>