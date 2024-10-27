<?php

function nav()
{
  $current_page = basename($_SERVER['PHP_SELF']);
?>

  <nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">
        <img class="logo" src="assets/images/logo.jpg" alt="logo" width="40" height="40" />
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link <?php echo $current_page == 'admin.php' ? 'active' : 'text-secondary'; ?>" aria-current="page" href="admin.php">Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo $current_page == 'adminmap.php' ? 'active' : 'text-secondary'; ?>" aria-current="page" href="adminmap.php">Map</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

<?php
}

?>