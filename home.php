<?php

session_start();

include_once(__DIR__ . "/connections/connection.php");
$con = connection();

if (!isset($_SESSION['user_id']) || $_SESSION['access'] !== 'user') {
	header("Location: home.php");
	exit();
}

$id = $_SESSION['id'] ?? NULL;

if ($id) {
	$userSql = "SELECT * FROM tbl_users WHERE id = ?";
	$stmtUser = $con->prepare($userSql);
	$stmtUser->bind_param("i", $id);
	$stmtUser->execute();
	$user = $stmtUser->get_result()->fetch_assoc();
}

// Fetch parking slots from the database
$parkingSql = "SELECT * FROM parking_slots";
$stmtParking = $con->prepare($parkingSql);
$stmtParking->execute();
$parkingResult = $stmtParking->get_result();

$parkingSlots = [];
while ($row = $parkingResult->fetch_assoc()) {
	$parkingSlots[] = $row; // Store the rows in an array
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
			/* Color for occupied slots */
		}

		.available {
			background-color: green;
			/* Color for available slots */
		}
	</style>
</head>

<body>

	<div class="parking-lot">
		<div class="header">
			<h1 class="greet">Welcome to ITC Car Parking Monitoring System, <?php echo $_SESSION['user_name']; ?>!</h1>
			<a class="logout" href="index.php">Logout</a>
		</div>
		<div class="top-container">
			<div class="left-parking">
				<?php
				foreach ($parkingSlots as $slot) {
					if (strpos($slot['slot_number'], 'LP') === 0) {
						$class = $slot['is_occupied'] ? 'occupied' : 'available';
						echo "<div class='parking-slot $class'>{$slot['slot_number']}</div>\n";
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
						echo "<div class='parking-slot $class'>{$slot['slot_number']}</div>\n";
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
					echo "<div class='parking-slot $class'>{$slot['slot_number']}</div>\n";
				}
			}
			?>
		</div>
	</div>
</body>

</html>