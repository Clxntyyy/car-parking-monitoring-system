<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Database connection
    $conn = new mysqli("localhost", "username", "password", "database_name");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get ticket details from the database
    $ticket_id = $_POST['ticket_id'];
    $sql = "SELECT license_plate, slot_number, entry_time, exit_time, is_overtime FROM ticket_tbl WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ticket_id);
    $stmt->execute();
    $stmt->bind_result($license_plate, $slot_number, $entry_time, $exit_time, $is_overtime);
    $stmt->fetch();
    $stmt->close();

    // Calculate overstay duration and fine if is_overtime is true
    if ($is_overtime) {
        $overstay_duration = ($exit_time->getTimestamp() - $entry_time->getTimestamp()) / 3600; // in hours
        $fine_amount = ceil($overstay_duration) * 100; // 100 PHP per hour
    } else {
        $overstay_duration = 0;
        $fine_amount = 0;
    }

    // Prepare email details
    $to = $_POST['email'];
    $subject = "Parking Violation Ticket";
    $message = file_get_contents('ticket.php');

    // Replace placeholders in the email template with dynamic values
    $message = str_replace(
        ['{{license_plate}}', '{{slot_number}}', '{{entry_time}}', '{{exit_time}}', '{{overstay_duration}}', '{{fine_amount}}'],
        [$license_plate, $slot_number, $entry_time, $exit_time, $overstay_duration . ' hours', '₱' . number_format($fine_amount, 2)],
        $message
    );

    // Send email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: no-reply@parkingmonitoring.com' . "\r\n";

    if (mail($to, $subject, $message, $headers)) {
        echo "Ticket sent successfully.";
    } else {
        echo "Failed to send ticket.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parking Violation Ticket</title>
    <style>
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

    <div class="ticket">
        <div class="ticket-header">
            <h2>Parking Violation Notice</h2>
        </div>

        <div class="ticket-body">
            <div class="violation-details">
                <h3>License Plate: {{license_plate}}</h3>
                <p>Slot Number: {{slot_number}}</p>
                <p>Date: {{entry_time}}</p>
                <p>Entry Time: {{entry_time}}</p>
                <p>Exit Time: {{exit_time}}</p>
            </div>

            <div class="fine-details">
                <p><strong>Overstay Duration:</strong> {{overstay_duration}}</p>
                <p><strong>Fine Amount:</strong> {{fine_amount}}</p>
            </div>
        </div>
    </div>

</body>

</html>