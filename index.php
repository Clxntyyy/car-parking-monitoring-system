<?php
session_start();

include_once(__DIR__ . "/connections/connection.php");
$con = connection();

// Fetch all users from the table (optional for debugging purposes)
$userSql = "SELECT * FROM tbl_users";
$stmtUser = $con->prepare($userSql);
$stmtUser->execute();
$result = $stmtUser->get_result();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $name = $_POST['name'];
  $plate_number = $_POST['pnumber'];

  // Validate user input against the database
  $loginSql = "SELECT * FROM tbl_users WHERE user_name = ? AND plate_number = ?";
  $stmtLogin = $con->prepare($loginSql);
  $stmtLogin->bind_param("ss", $name, $plate_number);
  $stmtLogin->execute();
  $loginResult = $stmtLogin->get_result();

  if ($loginResult->num_rows > 0) {
    // User found, create session
    $user = $loginResult->fetch_assoc();
    
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['user_name'] = $user['user_name'];
    $_SESSION['access'] = $user['access'];

    if ($user['access'] === 'admin') {
      header("Location: admin-dashboard.php");
    } else {
      header("Location: home.php");
    }
    exit();
  } else {
    echo "<script>alert('Invalid name or plate number!');</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="./assets/styles/global.css" />
  <title>CPMS — User Login</title>
</head>

<body>
  <div class="wrapper">
    <form action="" method="post">
      <div class="form-header">
        <img src="./assets/images/logo.jpg" alt="cpms-logo" width="60" height="60" />
        <div>
          <h3>CPMS — Login</h3>
          <p>Enter your name and license plate number</p>
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
      <button type="submit">Login</button>
      <p style="text-align:center; margin-top:1rem;">
        <a href="signup.php">Sign up for new user</a>
      </p>
    </form>

    <!-- Display all users for debugging (optional) -->
    <!-- <div class="users-list">
            <h4>Registered Users:</h4>
            <ul>
                <?php while ($user = $result->fetch_assoc()) { ?>
                    <li><?php echo $user['user_name'] . " - " . $user['plate_number']; ?></li>
                <?php } ?>
            </ul>
        </div> -->
  </div>
</body>

</html>