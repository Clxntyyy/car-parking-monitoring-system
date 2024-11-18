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

$unpaidTicketsSql = "SELECT COUNT(*) AS unpaid_tickets FROM ticket_tbl WHERE ticket_id NOT IN (SELECT ticket_id FROM payment_tbl)";
$unpaidTicketsResult = $conn->query($unpaidTicketsSql);
$unpaidTickets = $unpaidTicketsResult->fetch_assoc();

$filter = isset($_POST['filter']) ? $_POST['filter'] : 'all';
$sortColumn = isset($_POST['sortColumn']) ? $_POST['sortColumn'] : 'fname';
$sortOrder = isset($_POST['sortOrder']) ? $_POST['sortOrder'] : 'ASC';

$sql = "SELECT u.user_id, 
                u.fname, 
                u.lname, 
                v.plate_number, 
                t.ticket_id, 
                t.is_overtime, 
                p.payment_id
        FROM user_tbl u
        LEFT JOIN vehicle_tbl v ON u.user_id = v.user_id
        LEFT JOIN ticket_tbl t ON u.user_id = t.user_id
        LEFT JOIN payment_tbl p ON t.ticket_id = p.ticket_id
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
                            <th scope="col">Status</th>
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
                                        <td>" . ($row['is_overtime'] == 1 ? "<span class='badge badge-danger'>Issued</span>" : "<span class='badge badge-success'>No Ticket</span>") . "</td>
                                        <td><button class='btn btn-primary'>Send Ticket</button></td>
                                        <td>" . ($row['payment_id'] ? 'Paid' : 'Unpaid') . "</td>
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
                    labels: ['Available', 'Occupied', 'Unpaid Tickets'],
                    datasets: [{
                        data: [<?php echo $slotStatus['available']; ?>, <?php echo $slotStatus['occupied']; ?>, <?php echo $unpaidTickets['unpaid_tickets']; ?>],
                        backgroundColor: ['#45A557', '#E5484D', '#FFB224'],
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
    </script>
</body>

</html>