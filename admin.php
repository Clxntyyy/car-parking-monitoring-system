<?php

session_start();
include_once 'connections/connection.php';
include_once 'components/nav.php';

$conn = connection();

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>Admin Page</title>
</head>

<body>
    <?php nav(); ?>
    <div class="p-4">
        <h1>Admin Dashboard</h1>
        <table class="">

        </table>
    </div>
</body>

</html>