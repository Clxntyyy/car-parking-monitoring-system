<?php
session_start();
include_once 'connections/connection.php';

$con = connection();

if (!isset($_SESSION['user']['user_id']) || $_SESSION['user']['access_level'] !== 'customer') {
    header("Location: home.php");
    exit();
}

$id = $_SESSION['user']['user_id'] ?? NULL;

if ($id) {
    // Fetch user details
    $userSql = "SELECT * FROM user_tbl WHERE user_id = ?";
    $stmtUser = $con->prepare($userSql);
    if ($stmtUser) {
        $stmtUser->bind_param("i", $id);
        $stmtUser->execute();
        $user = $stmtUser->get_result()->fetch_assoc();
    } else {
        // Handle error
        die("Error preparing statement: {$con->error}");
    }
}

// Fetch all parking slots from the database
$parkingSql = "SELECT * FROM parkingslots_tbl";
$stmtParking = $con->prepare($parkingSql);
if ($stmtParking) {
    $stmtParking->execute();
    $parkingResult = $stmtParking->get_result();
} else {
    // Handle error
    die("Error preparing statement: {$con->error}");
}

$parkingSlots = [];
while ($row = $parkingResult->fetch_assoc()) {
    $parkingSlots[] = $row;
}

function renderParkingSlots($parkingSlots, $prefix, $userId)
{
    foreach ($parkingSlots as $slot) {
        if (strpos($slot['slot_number'], $prefix) === 0) {
            $borderClass = $slot['status'] === 'occupied' ? 'occupied' : 'available';
            $userClass = $slot['user_id'] == $userId ? 'user-slot' : '';
            echo "<div class='slot {$borderClass} {$userClass}'></div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/styles/map.css">
    <title>CPMS — Home</title>
    <style>
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 #da2f35;
            }

            70% {
                box-shadow: 0 0 0 5px rgba(0, 0, 0, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(0, 0, 0, 0);
            }
        }

        .user-slot {
            border: 2px solid red;
            animation: pulse 1s infinite;
        }
    </style>
</head>

<body>
    <div class="p-4">
        <h1>Map</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['user']['fname']); ?>!</p>
        <div class="map-wrapper">
            <div class="motor-parking">
                <?php renderParkingSlots($parkingSlots, 'MP', $id); ?>
            </div>
            <div class="right-map">
                <div class="top-parking">
                    <div class="left-parking">
                        <?php renderParkingSlots($parkingSlots, 'LP', $id); ?>
                    </div>
                    <div class="entrance">main entrance</div>
                    <div class="right-parking">
                        <?php renderParkingSlots($parkingSlots, 'RP', $id); ?>
                    </div>
                </div>
                <div class="bottom-parking">
                    <div class="trike-parking">
                        <?php renderParkingSlots($parkingSlots, 'TP', $id); ?>
                    </div>
                    <div class="center-parking">
                        <?php renderParkingSlots($parkingSlots, 'CP', $id); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="assets/scripts/admin-modal.js"></script>
</body>

</html>