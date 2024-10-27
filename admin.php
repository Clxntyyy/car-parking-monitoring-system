<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: #333;
            overflow: hidden;
            position: absolute;
            top: 0;
            width: 100%;
        }

        .logo{
            float: left;
            width: 40px;
            height: 40px;
            padding: 8px 16px;
        }

        .navbar a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }

        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }

        .navbar .right {
            float: right;
        }
    </style>
</head>

<body>

    <div class="navbar">
        <img class="logo" src="assets/images/logo.jpg" alt="logo">
        <a href="adminmap.php">Map</a> 
        <div class="right">
            <a href="logout.php">Log out</a>
        </div>
    </div>
</body>

</html>