<!-- reservation_modal.php -->
<div class="modal fade" id="reservationModal" tabindex="-1" aria-labelledby="reservationModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="reservationModalLabel">Make a Reservation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="reservationForm">
          <div class="mb-3">
            <label for="facilityName" class="form-label">Facility Name</label>
            <input type="text" class="form-control" id="facilityName" readonly>
          </div>
          <div class="mb-3">
            <label for="reservationDate" class="form-label">Reservation Date</label>
            <input type="date" class="form-control" id="reservationDate" required>
          </div>
          <div class="mb-3">
            <label for="reservationTime" class="form-label">Reservation Time</label>
            <input type="time" class="form-control" id="reservationTime" required>
          </div>
          <div class="mb-3">
            <label for="additionalNotes" class="form-label">Additional Notes</label>
            <textarea class="form-control" id="additionalNotes" rows="3"></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Submit</button>
        </form>
      </div>
    </div>
  </div>
</div>
