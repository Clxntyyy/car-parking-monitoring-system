<?php
if (!function_exists('connection')) {
    function connection() {
        $host = 'localhost'; // Your database host
        $user = 'root'; // Your database username
        $password = ''; // Your database password
        $database = 'cpms_db'; // Your database name

        $con = new mysqli($host, $user, $password, $database);

        // Check connection
        if ($con->connect_error) {
            die("Connection failed: " . $con->connect_error);
        }

        return $con;
    }
}
?>