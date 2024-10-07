<?php

session_start();

include_once(__DIR__ . "/connections/connection.php");
$con = connection();

if (!isset($_SESSION['user_id']) || $_SESSION['access'] !== 'admin') {
	header("Location: admin-dashboard.php");
	exit();
}

// Fetch parking slots from the database
$parkingSql = "SELECT ps.*, u.user_name FROM parking_slots ps LEFT JOIN tbl_users u ON ps.user_id = u.user_id"; // Adjust the column name if necessary
$stmtParking = $con->prepare($parkingSql);
$stmtParking->execute();
$parkingResult = $stmtParking->get_result();

$parkingSlots = [];
while ($row = $parkingResult->fetch_assoc()) {
	$parkingSlots[] = $row; // Store the rows in an array
}

// Handle updates to parking slots
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_slot'])) {
	$slot_number = $_POST['slot_number'];
	$is_occupied = $_POST['is_occupied'] === '1' ? 0 : 1; // Toggle occupancy
	$user_id = $is_occupied ? $_POST['user_id'] : NULL; // Only set user_id if slot is occupied
	$plate_number = $is_occupied ? $_POST['plate_number'] : NULL; // Set plate number if occupied

	// Update the parking slot status
	$updateSql = "UPDATE parking_slots SET is_occupied = ?, user_id = ?, plate_number = ? WHERE slot_number = ?";
	$stmtUpdate = $con->prepare($updateSql);
	$stmtUpdate->bind_param("isss", $is_occupied, $user_id, $plate_number, $slot_number);
	$stmtUpdate->execute();

	header("Location: admin-dashboard.php"); // Redirect to avoid resubmission
	exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" href="./assets/styles/homeadmin.css" />
	<title>CPMS — Admin</title>
</head>

<body>
	<h1>Admin Dashboard</h1>
	<table>
		<thead>
			<tr>
				<th>Slot Number</th>
				<th>Status</th>
				<th>User</th>
				<th>Plate Number</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($parkingSlots as $slot): ?>
				<tr>
					<td><?php echo htmlspecialchars($slot['slot_number']); ?></td>
					<?php echo $slot['is_occupied'] ? '<td><span style="display: inline-block; background: red; height: 1rem; width: 1rem; border-radius: 999px;"></span></td>' : '<td><span style="display: inline-block; background: green; height: 1rem; width: 1rem; border-radius: 999px;"></span></td>'; ?>
					<td><?php echo htmlspecialchars($slot['user_name'] ?: '---'); ?></td>
					<td><?php echo htmlspecialchars($slot['plate_number'] ?: '---'); ?></td>
					<td>
						<form method="POST">
							<input type="hidden" name="slot_number" value="<?php echo htmlspecialchars($slot['slot_number']); ?>">
							<input type="hidden" name="is_occupied" value="<?php echo $slot['is_occupied']; ?>">
							<input type="hidden" name="user_id" value="<?php echo htmlspecialchars($slot['user_id']); ?>">
							<input type="hidden" name="plate_number" value="<?php echo htmlspecialchars($slot['plate_number']); ?>">
							<button type="submit" name="update_slot">
								<?php echo $slot['is_occupied'] ? 'Mark as Available' : 'Mark as Occupied'; ?>
							</button>
						</form>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<a href="index.php">Logout</a>
</body>

</html>