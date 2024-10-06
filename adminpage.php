<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['access'] !== 'admin') {
    header("Location: adminpage.php");
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
	<div class="top-container">
		<div>
			<button>LP11</button>
			<button>LP10</button>
			<button>LP9</button>
			<button>LP8</button>
			<button>LP7</button>
			<button>LP6</button>
			<button>LP5</button>
			<button>LP4</button>
			<button>LP3</button>
			<button>LP2</button>
			<button>LP1</button>
		</div>
		<div class="entrance"></div>
		<div class="right-parking">
			<button>RP1</button>
			<button>RP2</button>
			<button>RP3</button>
			<button>RP4</button>
			<button>RP5</button>
			<button>RP6</button>
			<button>RP7</button>
		</div>
	</div>

	<div class="bottom-container">
		<button>CP21</button>
		<button>CP20</button>
		<button>CP19</button>
		<button>CP18</button>
		<button>CP17</button>
		<button>CP16</button>
		<button>CP15</button>
		<button>CP14</button>
		<button>CP13</button>
		<button>CP12</button>
		<button>CP11</button>
		<button>CP10</button>
		<button>CP9</button>
		<button>CP8</button>
		<button>CP7</button>
		<button>CP6</button>
		<button>CP5</button>
		<button>CP4</button>
		<button>CP3</button>
		<button>CP2</button>
		<button>CP1</button>
	</div>
	<a href="index.php">Logout</a>
</body>
</html>