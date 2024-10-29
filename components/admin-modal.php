<?php function adminModal($users)
{
?>

  <!-- Information Modal -->
  <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="infoModalLabel">User Information</h5>
          <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p><strong>First Name:</strong> <span id="infoFname"></span></p>
          <p><strong>Last Name:</strong> <span id="infoLname"></span></p>
          <p><strong>Email:</strong> <span id="infoEmail"></span></p>
          <p><strong>Phone:</strong> <span id="infoPhone"></span></p>
          <!-- Add other user information fields here -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="makeAvailableButton">Mark Slot as Available</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="slotModal" tabindex="-1" aria-labelledby="slotModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="slotModalLabel">Slot Information</h5>
          <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="slotForm">
            <div class="form-group">
              <label for="slotNumber">Slot Number:</label>
              <span id="slotNumber"></span>
              <input type="hidden" id="hiddenSlotNumber" name="slotNumber">
            </div>
            <div class="form-group">
              <label for="status">Status:</label>
              <div>
                <input type="radio" id="occupied" name="status" value="occupied">
                <label for="occupied">Occupied</label>
              </div>
              <div>
                <input type="radio" id="available" name="status" value="available">
                <label for="available">Available</label>
              </div>
            </div>
            <div class="form-group">
              <label for="userId">User:</label>
              <select id="userId" name="user_id" class="form-control">
                <option value="">Select User</option>
                <?php
                foreach ($users as $user) {
                  $userVehicle = $user['fname'] . ' ' . $user['lname'] . ' (' . $user['plate_number'] . ')';
                  echo '<option value="' . $user['id'] . '" data-vehicle-id="' . $user['vehicle_id'] . '">' . $userVehicle . '</option>';
                }
                ?>
              </select>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="saveChanges">Save changes</button>
        </div>
      </div>
    </div>
  </div>

<?php
}
?>