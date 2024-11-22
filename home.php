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

$overtime = false;
$slot_number = ''; // Initialize the variable
$ticketSql = "SELECT t.entry_time, t.exit_time, t.is_overtime, v.plate_number 
              FROM ticket_tbl t 
              JOIN vehicle_tbl v ON t.vehicle_id = v.vehicle_id 
              WHERE t.user_id = ? AND t.exit_time IS NULL";
$stmtTicket = $con->prepare($ticketSql);
if ($stmtTicket) {
    $stmtTicket->bind_param("i", $id);
    $stmtTicket->execute();
    $stmtTicket->bind_result($entry_time, $exit_time, $is_overtime, $license_plate);
    $stmtTicket->fetch();
    $stmtTicket->close();

    if ($is_overtime) {
        $overtime = true;
        $entry_time = new DateTime($entry_time);
        $current_time = new DateTime();
        $interval = $entry_time->diff($current_time);
        $hours = $interval->h + $interval->days * 24;
        $overstay_duration = $interval->format('%h hours %i minutes');
        $fine_amount = $hours * 100;
    }
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

        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .ticket {
            width: 350px;
            height: auto;
            margin: 20px auto;
            border: 2px dashed #e74c3c;
            background-color: #fff;
            padding: 20px;
            position: relative;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .ticket-header {
            text-align: center;
            border-bottom: 2px dashed #e74c3c;
            padding-bottom: 10px;
        }

        .ticket-header h2 {
            margin: 0;
            font-size: 24px;
            color: #e74c3c;
        }

        .ticket-body {
            margin-top: 15px;
        }

        .ticket-body .violation-details {
            margin-bottom: 20px;
        }

        .violation-details h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .violation-details p {
            margin: 5px 0;
            font-size: 14px;
            color: #555;
        }

        .ticket-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
        }

        .ticket-footer .ticket-number {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }

        .ticket-footer .qr-section {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .qr-section .pay {
            margin: 0;
            font-weight: bold;
            color: #e74c3c;
        }

        .qr-section .qr-code {
            width: 150px;
            height: 150px;
            background-color: #eee;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 1px solid #ccc;
            margin-top: 10px;
        }

        .qr-section .qr-code img {
            width: 100%;
            height: 100%;
        }

        .ticket-body .fine-details {
            font-size: 14px;
            color: #555;
            margin-top: 10px;
        }

        .ticket-body .fine-details strong {
            color: #333;
        }

        .note {
            margin-top: 20px;
            font-size: 14px;
            color: #555;
            text-align: center;
        }

        @media print {
            body {
                background-color: #fff;
            }

            .ticket {
                border: none;
                box-shadow: none;
            }
        }
    </style>
</head>

<body>
    <div class="p-4">
        <h1>Map</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['user']['fname']); ?>!</p>
        <?php if ($overtime): ?>
            <div class="ticket">
                <div class="ticket-header">
                    <h2>Parking Violation Notice</h2>
                </div>

                <div class="ticket-body">
                    <div class="violation-details">
                        <h3>License Plate: <?php echo htmlspecialchars($license_plate); ?></h3>
                        <p>Slot Number: <?php echo htmlspecialchars($slot_number); ?></p>
                        <p>Date: <?php echo htmlspecialchars($entry_time->format('Y-m-d')); ?></p>
                        <p>Entry Time: <?php echo htmlspecialchars($entry_time->format('H:i:s')); ?></p>
                        <p>Exit Time: <?php echo htmlspecialchars($current_time->format('H:i:s')); ?></p>
                    </div>

                    <div class="fine-details">
                        <p><strong>Overstay Duration:</strong> <?php echo htmlspecialchars($overstay_duration); ?></p>
                        <p><strong>Fine Amount:</strong> ₱<?php echo number_format($fine_amount, 2); ?></p>
                    </div>
                </div>
            </div>
        <?php else: ?>
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
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="assets/scripts/admin-modal.js"></script>
</body>

</html>