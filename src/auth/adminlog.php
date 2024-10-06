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