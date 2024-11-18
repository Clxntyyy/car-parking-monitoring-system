<?php
session_start();
include_once 'connections/connection.php';

$conn = connection();

if (!isset($_SESSION['user']['staff_id'])) {
    header("Location: login.php");
    exit();
}

date_default_timezone_set('Asia/Manila');

// In the AJAX handler section (around line 15), modify to include amount:
if (isset($_GET['action']) && $_GET['action'] === 'get_overtime') {
    $query = "SELECT ticket_id, entry_time FROM ticket_tbl";
    $result = $conn->query($query);

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $entry_time = new DateTime($row['entry_time']);
        $threshold_time = clone $entry_time;
        $threshold_time->modify('+1 hour');
        $current_time = new DateTime();

        if ($current_time > $threshold_time) {
            $interval = $threshold_time->diff($current_time);
            $hours = $interval->h + $interval->days * 24;
            $amount = $hours * 100;
            $data[] = [
                'ticket_id' => $row['ticket_id'],
                'overtime' => $interval->format('%h hours %i minutes'),
                'amount' => $amount
            ];
        } else {
            $data[] = [
                'ticket_id' => $row['ticket_id'],
                'overtime' => 'No overtime',
                'amount' => 0
            ];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

// Fetch initial table data
$query = "
SELECT 
    t.ticket_id, t.ticket_no, t.entry_time, t.exit_time, t.is_overtime, 
    t.user_id, t.pslot_id, t.vehicle_id,
    u.*
FROM 
    ticket_tbl t
JOIN 
    user_tbl u ON t.user_id = u.user_id
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <title>Staff Dashboard - Payment Monitoring</title>
</head>

<body>
    <h2>Parking Violation Tickets - Payment Status</h2>
    <table class="table table-striped table-hover w-100 border shadow-sm">
        <thead>
            <tr>
                <th scope="col">Ticket ID</th>
                <th scope="col">User Name</th>
                <th scope="col">Email</th>
                <th scope="col">Phone Number</th>
                <th scope="col">Time Overstayed</th>
                <th scope="col">Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['ticket_id']; ?></td>
                    <td><?php echo $row['fname'] . ' ' . $row['lname']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['contact_no']; ?></td>
                    <td id="overtime-<?php echo $row['ticket_id']; ?>">
                        <?php
                        $entry_time = new DateTime($row['entry_time']);
                        $threshold_time = clone $entry_time;
                        $threshold_time->modify('+1 hour');
                        $current_time = new DateTime();

                        if ($current_time > $threshold_time) {
                            $interval = $threshold_time->diff($current_time);
                            echo $interval->format('%h hours %i minutes');
                        } else {
                            echo 'No overtime';
                        }
                        ?>
                    </td>
                    <td id="amount-<?php echo $row['ticket_id']; ?>">
                        <?php
                        $entry_time = new DateTime($row['entry_time']);
                        $threshold_time = clone $entry_time;
                        $threshold_time->modify('+1 hour');
                        $current_time = new DateTime();

                        if ($current_time > $threshold_time) {
                            $interval = $threshold_time->diff($current_time);
                            $hours = $interval->h + ($interval->days * 24);
                            $amount = $hours * 100;
                            echo '₱' . number_format($amount, 2);
                        } else {
                            echo '₱0.00';
                        }
                        ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <script>
        function updateOvertime() {
            $.ajax({
                url: 'staff.php?action=get_overtime',
                method: 'GET',
                success: function(data) {
                    data.forEach(ticket => {
                        $(`#overtime-${ticket.ticket_id}`).text(ticket.overtime);
                        $(`#amount-${ticket.ticket_id}`).text(
                            ticket.amount > 0 ? `₱${ticket.amount.toFixed(2)}` : '₱0.00'
                        );
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching overtime data:", error);
                }
            });
        }

        setInterval(updateOvertime, 60000); // Update overtime and amount every 60 seconds
        updateOvertime();
    </script>
</body>

</html>

<?php $conn->close(); ?>