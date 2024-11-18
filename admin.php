<?php

session_start();
include_once 'connections/connection.php';
include_once 'components/nav.php';

$conn = connection();

if (!isset($_SESSION['logged_in']) || $_SESSION['user']['access_level'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$slotStatusSql = "SELECT COUNT(CASE WHEN status = 'available' THEN 1 END) AS available, COUNT(CASE WHEN status = 'occupied' THEN 1 END) AS occupied, COUNT(CASE WHEN status = 'reserved' THEN 1 END) AS reserved FROM parkingslots_tbl";
$slotStatusResult = $conn->query($slotStatusSql);
$slotStatus = $slotStatusResult->fetch_assoc();

$filter = isset($_POST['filter']) ? $_POST['filter'] : 'all';
$sortColumn = isset($_POST['sortColumn']) ? $_POST['sortColumn'] : 'fname';
$sortOrder = isset($_POST['sortOrder']) ? $_POST['sortOrder'] : 'ASC';

$sql = "SELECT u.user_id, 
                u.fname, 
                u.lname, 
                v.plate_number, 
                t.ticket_id, 
                t.is_overtime, 
                p.payment_id,
                ps.status AS parking_status
        FROM user_tbl u
        LEFT JOIN vehicle_tbl v ON u.user_id = v.user_id
        LEFT JOIN ticket_tbl t ON u.user_id = t.user_id
        LEFT JOIN payment_tbl p ON t.ticket_id = p.ticket_id
        LEFT JOIN parkingslots_tbl ps ON u.user_id = ps.user_id
        WHERE u.access_level = 'customer'";

switch ($filter) {
    case 'parked':
        $sql .= " AND u.user_id IN (SELECT user_id FROM parkingslots_tbl WHERE status = 'occupied')";
        break;
    case 'not_active':
        $sql .= " AND u.user_id NOT IN (SELECT user_id FROM parkingslots_tbl WHERE status = 'occupied')";
        break;
    default:
        // No additional filter for 'all'
        break;
}

$sql .= " ORDER BY $sortColumn $sortOrder";

$result = $conn->query($sql);

if (isset($_POST['action']) && $_POST['action'] === 'update_overtime') {
    if (!isset($_SESSION['logged_in']) || $_SESSION['user']['access_level'] !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $ticket_id = $_POST['ticket_id'];
    $sql = "UPDATE ticket_tbl SET is_overtime = 1 WHERE ticket_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ticket_id);
    $success = $stmt->execute();

    echo json_encode(['success' => $success]);
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="assets/styles/global.css">
    <title>Admin Page</title>
</head>

<body>
    <?php nav(); ?>
    <div class="container-fluid p-4">
        <h1>Admin Dashboard</h1>
        <div class="d-flex flex-wrap mb-4">
            <div class="d-flex align-items-center mr-3">
                <div class="input-group">
                    <label class="input-group-text" for="filter" class="mr-2 d-block">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                            <path d='M4.5 7h15M7 12h10m-7 5h4' />
                        </svg>
                    </label>
                    <select id="filter" class="form-control">
                        <option value="all">All Users</option>
                        <option value="parked">Parked Users</option>
                        <option value="not_active">Not Active Users</option>
                    </select>
                </div>
            </div>
            <div class="d-flex align-items-center mr-3">
                <div class="input-group">
                    <label class="input-group-text" for="sortColumn" class="mr-2 d-block">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                            <path d='M4.5 7h15m-15 5h10m-10 5h4' />
                        </svg>
                    </label>
                    <select id="sortColumn" class="form-control">
                        <option value="fname">First Name</option>
                        <option value="lname">Last Name</option>
                        <option value="plate_number">Plate Number</option>
                    </select>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <label for="sortOrder" class="mr-2 d-block">Order:</label>
                <div id="sortOrder" class="d-flex">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="sortOrder" id="ascOrder" value="ASC" checked>
                        <label class="form-check-label" for="ascOrder">
                            <i class="fas fa-sort-alpha-down"></i>
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="sortOrder" id="descOrder" value="DESC">
                        <label class="form-check-label" for="descOrder">
                            <i class="fas fa-sort-alpha-down-alt"></i>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <table class="table table-striped table-hover w-100 border shadow-sm">
                    <thead>
                        <tr>
                            <th scope="col">First Name</th>
                            <th scope="col">Last Name</th>
                            <th scope="col">Plate Number</th>
                            <th scope="col">Ticket</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody id="userTable">
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$row['fname']}</td>
                                        <td>{$row['lname']}</td>
                                        <td class='geist-mono'>{$row['plate_number']}</td>
                                        <td id='ticket-status-{$row['ticket_id']}'>" .
                                    ($row['is_overtime'] == 1 ?
                                        "<span class='badge badge-danger'>Issued</span>" :
                                        "<span class='badge badge-success'>No Ticket</span>") .
                                    "</td>
                                    <td>" .
                                    (
                                        empty($row['parking_status']) ? // User not parked
                                        "<button class='btn btn-secondary' disabled>Not Parked</button>" : // Disabled button
                                        ($row['is_overtime'] == 1 ?
                                            "<button class='btn btn-primary' disabled>Ticket Sent</button>" :
                                            "<button class='btn btn-primary send-ticket' data-ticket-id='{$row['ticket_id']}'>Send Ticket</button>"
                                        )
                                    ) .
                                    "</td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center'>No users found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3 border p-4 shadow-sm">
                <canvas id="slotChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            function fetchFilteredData() {
                var filter = $('#filter').val();
                var sortColumn = $('#sortColumn').val();
                var sortOrder = $('input[name="sortOrder"]:checked').val();
                $.ajax({
                    url: 'admin.php',
                    type: 'POST',
                    data: {
                        filter: filter,
                        sortColumn: sortColumn,
                        sortOrder: sortOrder
                    },
                    success: function(response) {
                        var newTableBody = $(response).find('#userTable').html();
                        $('#userTable').html(newTableBody);
                    }
                });
            }

            $('#filter, #sortColumn, input[name="sortOrder"]').change(fetchFilteredData);

            const ctx = document.getElementById('slotChart').getContext('2d');
            const slotChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Available', 'Occupied'],
                    datasets: [{
                        data: [<?php echo $slotStatus['available']; ?>, <?php echo $slotStatus['occupied']; ?>],
                        backgroundColor: ['#45A557', '#E5484D'],
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        position: 'top',
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    }
                }
            });
        });
        // Add ticket update handler
        $(document).on('click', '.send-ticket', function() {
            const btn = $(this);
            const ticketId = btn.data('ticket-id');

            $.ajax({
                url: 'admin.php',
                method: 'POST',
                data: {
                    action: 'update_overtime',
                    ticket_id: ticketId
                },
                success: function(response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        // Update status badge
                        $(`#ticket-status-${ticketId}`).html(
                            '<span class="badge badge-danger">Issued</span>'
                        );
                        // Update button
                        btn.prop('disabled', true).text('Ticket Sent');
                    } else {
                        alert('Failed to update ticket status');
                    }
                },
                error: function() {
                    alert('Error updating ticket');
                }
            });
        });
    </script>
</body>

</html>