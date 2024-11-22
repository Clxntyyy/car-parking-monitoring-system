<?php
session_start();
include_once 'connections/connection.php';

$conn = connection();

if (!isset($_SESSION['logged_in']) || $_SESSION['user']['access_level'] !== 'staff') {
    header("Location: login.php");
    exit();
}

date_default_timezone_set('Asia/Manila');

// In the AJAX handler section (around line 15), modify to include amount:
if (isset($_GET['action']) && $_GET['action'] === 'get_overtime') {
    $query = "SELECT ticket_id, entry_time, is_overtime FROM ticket_tbl";
    $result = $conn->query($query);

    $data = [];
    while ($row = $result->fetch_assoc()) {
        if ($row['is_overtime']) {
            $entry_time = new DateTime($row['entry_time']);
            $current_time = new DateTime();
            $interval = $entry_time->diff($current_time);
            $hours = $interval->h + $interval->days * 24;
            $amount = $hours * 100;
            $data[] = [
                'ticket_id' => $row['ticket_id'],
                'overtime' => $interval->format('%h hour/s %i minute/s'),
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

$query = "SELECT 
    t.ticket_id, t.ticket_no, t.entry_time, t.exit_time, t.is_overtime, 
    t.user_id, t.pslot_id, t.vehicle_id,
    u.*,
    CASE WHEN p.payment_id IS NOT NULL THEN 'Paid' ELSE 'Unpaid' END as payment_status,
    p.payment_method,
    p.payment_date
FROM 
    ticket_tbl t
JOIN 
    user_tbl u ON t.user_id = u.user_id
LEFT JOIN 
    payment_tbl p ON t.ticket_id = p.ticket_id
WHERE 
    p.payment_id IS NULL
";
$result = $conn->query($query);

if (isset($_POST['action']) && $_POST['action'] === 'process_payment') {
    $ticket_id = $_POST['ticket_id'];
    $user_id = $_POST['user_id'];
    $amount = $_POST['amount'];
    $payment_method = $_POST['payment_method'];

    $sql = "INSERT INTO payment_tbl (ticket_id, user_id, amount_paid, payment_method) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iids", $ticket_id, $user_id, $amount, $payment_method);

    if ($stmt->execute()) {
        // Update parking slot status to 'available'
        $update_slot_sql = "UPDATE parkingslots_tbl SET status = 'available', user_id = NULL, vehicle_id = NULL 
                            WHERE pslot_id = (SELECT pslot_id FROM ticket_tbl WHERE ticket_id = ?)";
        $update_slot_stmt = $conn->prepare($update_slot_sql);
        $update_slot_stmt->bind_param("i", $ticket_id);
        $update_slot_stmt->execute();

        // Delete the ticket
        $delete_sql = "DELETE FROM ticket_tbl WHERE ticket_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $ticket_id);
        $delete_stmt->execute();

        echo json_encode(['success' => true, 'ticket_id' => $ticket_id]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit();
}
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

<body class="p-4">
    <h1>Parking Violation Tickets - Payment Status</h1>
    <table class="table table-striped table-hover w-100 border shadow-sm">
        <thead>
            <tr>
                <th scope="col">Ticket</th>
                <th scope="col">User Name</th>
                <th scope="col">Email</th>
                <th scope="col">Phone Number</th>
                <th scope="col">Time Overstayed</th>
                <th scope="col">Amount</th>
                <th scope="col">Status</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['ticket_no']; ?></td>
                    <td><?php echo $row['fname'] . ' ' . $row['lname']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['contact_no']; ?></td>
                    <td id="overtime-<?php echo $row['ticket_id']; ?>">
                        <?php
                        if ($row['is_overtime']) {
                            $entry_time = new DateTime($row['entry_time']);
                            $current_time = new DateTime();
                            $interval = $entry_time->diff($current_time);
                            echo $interval->format('%h hours %i minutes');
                        } else {
                            echo 'No overtime';
                        }
                        ?>
                    </td>
                    <td id="amount-<?php echo $row['ticket_id']; ?>">
                        <?php
                        if ($row['is_overtime']) {
                            $entry_time = new DateTime($row['entry_time']);
                            $current_time = new DateTime();
                            $interval = $entry_time->diff($current_time);
                            $hours = $interval->h + ($interval->days * 24);
                            $amount = $hours * 100;
                            echo '₱' . number_format($amount, 2);
                        } else {
                            echo '₱0.00';
                        }
                        ?>
                    </td>
                    <?php
                    echo "<td>" .
                        (isset($row['payment_id']) && $row['payment_id'] ?
                            "<span class='badge badge-success'>Paid</span>" :
                            "<span class='badge badge-warning'>Unpaid</span>") .
                        "</td>";
                    ?>
                    <td>
                        <?php if (!isset($row['payment_id']) || !$row['payment_id']): ?>
                            <button
                                class="btn btn-primary btn-sm process-payment"
                                data-ticket-id="<?php echo $row['ticket_id']; ?>"
                                data-user-id="<?php echo $row['user_id']; ?>"
                                data-amount="<?php echo $amount; ?>">
                                Process Payment
                            </button>
                        <?php else: ?>
                            <span class="badge badge-success">
                                Paid via <?php echo ucfirst($row['payment_method']); ?>
                            </span>
                        <?php endif; ?>
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

        $(document).ready(function() {
            $('.process-payment').click(function() {
                const btn = $(this);
                const ticketId = btn.data('ticket-id');
                const userId = btn.data('user-id');
                const amount = btn.data('amount');

                // Show payment method selection
                const paymentMethod = prompt('Enter payment method (cash/gcash):').toLowerCase();

                if (paymentMethod !== 'cash' && paymentMethod !== 'gcash') {
                    alert('Invalid payment method. Please enter either cash or gcash.');
                    return;
                }

                $.ajax({
                    url: 'staff.php',
                    method: 'POST',
                    data: {
                        action: 'process_payment',
                        ticket_id: ticketId,
                        user_id: userId,
                        amount: amount,
                        payment_method: paymentMethod
                    },
                    success: function(response) {
                        const result = JSON.parse(response);
                        if (result.success) {
                            location.reload();
                        } else {
                            alert('Payment processing failed');
                        }
                    },
                    error: function() {
                        alert('Error processing payment');
                    }
                });
            });
        });
    </script>
</body>

</html>

<?php $conn->close(); ?>