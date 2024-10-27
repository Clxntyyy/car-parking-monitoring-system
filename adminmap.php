<?php
session_start();
include_once 'connections/connection.php';
include_once 'components/nav.php';

$con = connection();
$parkingSql = "SELECT ps.*, u.fname, u.lname, v.plate_number, vt.type_name AS vehicle_type 
              FROM parkingslots_tbl ps 
              LEFT JOIN user_tbl u ON ps.user_id = u.user_id 
              LEFT JOIN vehicle_tbl v ON ps.vehicle_id = v.vehicle_id 
              LEFT JOIN vehicletype_tbl vt ON v.vehicle_type_id = vt.type_id";
$stmtParking = $con->prepare($parkingSql);
$stmtParking->execute();
$parkingResult = $stmtParking->get_result();
$parkingSlots = [];

while ($row = $parkingResult->fetch_assoc()) {
  $parkingSlots[] = $row;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/styles/map.css">
  <title>Map</title>
</head>

<body>
  <?php nav(); ?>
  <div class="p-4">
    <h1>Map</h1>
    <div class="map-wrapper">
      <div class="motor-parking">
        <?php
        foreach ($parkingSlots as $slot) {
          if (strpos($slot['slot_number'], 'MP') === 0) {
            echo '<button class="slot" data-toggle="modal" data-target="#slotModal" 
                      data-slot-number="' . $slot['slot_number'] . '" 
                      data-name="' . $slot['fname'] . ' ' . $slot['lname'] . '" 
                      data-vehicle-type="' . $slot['vehicle_type'] . '" 
                      data-plate-number="' . $slot['plate_number'] . '"></button>';
          }
        }
        ?>
      </div>
      <div class="right-map">
        <div class="top-parking">
          <div class="left-parking">
            <?php
            foreach ($parkingSlots as $slot) {
              if (strpos($slot['slot_number'], 'LP') === 0) {
                echo '<button class="slot" data-toggle="modal" data-target="#slotModal" 
                          data-slot-number="' . $slot['slot_number'] . '" 
                          data-name="' . $slot['fname'] . ' ' . $slot['lname'] . '" 
                          data-vehicle-type="' . $slot['vehicle_type'] . '" 
                          data-plate-number="' . $slot['plate_number'] . '"></button>';
              }
            }
            ?>
          </div>
          <div class="entrance"></div>
          <div class="right-parking">
            <?php
            foreach ($parkingSlots as $slot) {
              if (strpos($slot['slot_number'], 'RP') === 0) {
                echo '<button class="slot" data-toggle="modal" data-target="#slotModal" 
                          data-slot-number="' . $slot['slot_number'] . '" 
                          data-name="' . $slot['fname'] . ' ' . $slot['lname'] . '" 
                          data-vehicle-type="' . $slot['vehicle_type'] . '" 
                          data-plate-number="' . $slot['plate_number'] . '"></button>';
              }
            }
            ?>
          </div>
        </div>
        <div class="bottom-parking">
          <div class="trike-parking">
            <?php
            foreach ($parkingSlots as $slot) {
              if (strpos($slot['slot_number'], 'TP') === 0) {
                echo '<button class="slot" data-toggle="modal" data-target="#slotModal" 
                          data-slot-number="' . $slot['slot_number'] . '" 
                          data-name="' . $slot['fname'] . ' ' . $slot['lname'] . '" 
                          data-vehicle-type="' . $slot['vehicle_type'] . '" 
                          data-plate-number="' . $slot['plate_number'] . '"></button>';
              }
            }
            ?>
          </div>
          <div class="center-parking">
            <?php
            foreach ($parkingSlots as $slot) {
              if (strpos($slot['slot_number'], 'CP') === 0) {
                echo '<button class="slot" data-toggle="modal" data-target="#slotModal" 
                          data-slot-number="' . $slot['slot_number'] . '" 
                          data-name="' . $slot['fname'] . ' ' . $slot['lname'] . '" 
                          data-vehicle-type="' . $slot['vehicle_type'] . '" 
                          data-plate-number="' . $slot['plate_number'] . '"></button>';
              }
            }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="slotModal" tabindex="-1" aria-labelledby="slotModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="slotModalLabel">Slot Information</h5>
          <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Slot Number: <span id="slotNumber"></span><br>
          Name: <span id="name"></span><br>
          Vehicle Type: <span id="vehicleType"></span><br>
          Plate Number: <span id="plateNumber"></span>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script>
    $('#slotModal').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var slotNumber = button.data('slot-number');
      var name = button.data('name');
      var vehicleType = button.data('vehicle-type');
      var plateNumber = button.data('plate-number');
      var modal = $(this);
      modal.find('#slotNumber').text(slotNumber);
      modal.find('#name').text(name);
      modal.find('#vehicleType').text(vehicleType);
      modal.find('#plateNumber').text(plateNumber);
    });
  </script>
</body>

</html>