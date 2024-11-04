<?php
session_start();

include_once 'connections/connection.php';

$conn = connection();

if (!isset($_SESSION['staff_id'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT * FROM ticket_tbl";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Staff Dashboard - Payment Monitoring</title>
</head>

<body>
    <h2>Parking Violation Tickets - Payment Status</h2>
    <table border="1">
        <tr>
            <th>Ticket ID</th>
            <th>License Plate</th>
            <th>Slot Number</th>
            <th>Entry Time</th>
            <th>Exit Time</th>
            <th>Overstay Duration (hours)</th>
            <th>Fine Amount</th>
            <th>Payment Status</th>
            <th>Paid At</th>
            <th>Actions</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['license_plate']; ?></td>
                <td><?php echo $row['slot_number']; ?></td>
                <td><?php echo $row['entry_time']; ?></td>
                <td><?php echo $row['exit_time']; ?></td>
                <td><?php echo $row['overstay_duration']; ?> hours</td>
                <td>₱<?php echo number_format($row['fine_amount'], 2); ?></td>
                <td><?php echo $row['is_paid'] ? 'Paid' : 'Unpaid'; ?></td>
                <td><?php echo $row['paid_at'] ? $row['paid_at'] : 'N/A'; ?></td>
                <td>
                    <?php if (!$row['is_paid']): ?>
                        <form action="update_payment.php" method="post" style="display:inline;">
                            <input type="hidden" name="ticket_id" value="<?php echo $row['id']; ?>">
                            <button type="submit">Mark as Paid</button>
                        </form>
                    <?php else: ?>
                        Paid
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>

</html>

<?php
$conn->close();
?>