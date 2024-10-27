<?php
session_start();
include_once(__DIR__ . "/connections/connection.php");
$con = connection();

if (!isset($_SESSION['user_id']) || $_SESSION['access'] !== 'user') {
    header("Location: home.php");
    exit();
}

$id = $_SESSION['user_id'] ?? NULL;

if ($id) {
    // Fetch user details
    $userSql = "SELECT * FROM user_tbl WHERE user_id = ?";
    $stmtUser = $con->prepare($userSql);
    $stmtUser->bind_param("i", $id);
    $stmtUser->execute();
    $user = $stmtUser->get_result()->fetch_assoc();
}

// Fetch all parking slots from the database
$parkingSql = "SELECT * FROM parkingslots_tbl";
$stmtParking = $con->prepare($parkingSql);
$stmtParking->execute();
$parkingResult = $stmtParking->get_result();

$parkingSlots = [];
while ($row = $parkingResult->fetch_assoc()) {
    $parkingSlots[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="assets/styles/home.css" />
    <title>CPMS — Home</title>
    <style>
        .occupied {
            background-color: red;
        }

        .available {
            background-color: green;
        }
    </style>
</head>

<body>
    <div class="parking-lot">
        <div class="header">
            <h1 class="greet">Welcome to ITC Car Parking Monitoring System, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
            <a class="logout" href="index.php">Logout</a>
        </div>
        
        <div class="top-container">
            <div class="left-parking">
                <?php
                foreach ($parkingSlots as $slot) {
                    if (strpos($slot['slot_number'], 'LP') === 0) {
                        $class = $slot['is_occupied'] ? 'occupied' : 'available';
                        echo "<div class='parking-slot $class'>" . htmlspecialchars($slot['slot_number']) . "</div>\n";
                    }
                }
                ?>
            </div>
            <div class="entrance"></div>
            <div class="right-parking">
                <?php
                foreach ($parkingSlots as $slot) {
                    if (strpos($slot['slot_number'], 'RP') === 0) {
                        $class = $slot['is_occupied'] ? 'occupied' : 'available';
                        echo "<div class='parking-slot $class'>" . htmlspecialchars($slot['slot_number']) . "</div>\n";
                    }
                }
                ?>
            </div>
        </div>

        <div class="bottom-container">
            <?php
            foreach ($parkingSlots as $slot) {
                if (strpos($slot['slot_number'], 'CP') === 0) {
                    $class = $slot['is_occupied'] ? 'occupied' : 'available';
                    echo "<div class='parking-slot $class'>" . htmlspecialchars($slot['slot_number']) . "</div>\n";
                }
            }
            ?>
        </div>
    </div>
</body>
</html>