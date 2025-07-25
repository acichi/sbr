document.addEventListener('DOMContentLoaded', () => {
  const svg = document.getElementById('map');
  fetch('retrieve_facility.php')
    .then(response => response.json())
    .then(data => {
      data.forEach(facility => {
        const { id, name, pin_x, pin_y, status, image, details, price } = facility;
        const normalizedStatus = status.trim().toLowerCase();
        const color = normalizedStatus === 'available' ? 'green' :
                      normalizedStatus === 'pending' ? 'yellow' : 'red';
        const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        circle.setAttribute('cx', pin_x);
        circle.setAttribute('cy', pin_y);
        circle.setAttribute('r', 10);
        circle.setAttribute('fill', color);
        circle.setAttribute('data-id', id);
        circle.style.cursor = 'pointer';
        circle.setAttribute('title', name);
        circle.addEventListener('click', () => {
          showFacilityModal({
            name,
            details,
            status: normalizedStatus,
            image: `${image}`,
            price
          });
        });
        svg.appendChild(circle);
      });
    })
    .catch(error => console.error('Error fetching facility data:', error));
});

function showFacilityModal(facility) {
  const { name, details, status, image, price } = facility;
  const modalContent = `
    <div class="modal fade" id="facilityModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">${name}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <img src="../admindash/${image}" class="img-fluid mb-3" alt="${name}">
            <p><strong>Status:</strong> ${status}</p>
            <p><strong>Price:</strong> ₱${price} per day</p>
            <p>${details}</p>
            <div class="text-end mt-4">
              <button class="btn btn-success me-2" id="reserveBtn">Reserve</button>
              <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
            <form id="reservationForm" class="mt-4 d-none">
              <input type="hidden" name="facility_name" value="${name}">
              <div class="mb-3">
                <label for="reservee" class="form-label">Your Name</label>
                <input type="text" class="form-control" id="reservee" name="reservee" required>
              </div>
              <div class="mb-3">
                <label for="date_start" class="form-label">Start Date</label>
                <input type="datetime-local" class="form-control" id="date_start" name="date_start" required>
              </div>
              <div class="mb-3">
                <label for="date_end" class="form-label">End Date</label>
                <input type="datetime-local" class="form-control" id="date_end" name="date_end" required>
              </div>
              <div class="mb-3">
                <label for="payment_type" class="form-label">Payment Type</label>
                <select class="form-control" id="payment_type" name="payment_type" required>
                  <option value="" disabled selected>Select payment type</option>
                  <option value="Cash">Cash</option>
                  <option value="GCash">GCash</option>
                  <option value="PayMaya">PayMaya</option>
                  <option value="Visa">Visa</option>
                  <option value="MasterCard">MasterCard</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="amount" class="form-label">Total Amount</label>
                <input type="number" class="form-control" id="amount" name="amount" readonly>
              </div>
              <div class="text-end">
                <button type="submit" class="btn btn-primary">Submit Reservation</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  `;

  const oldModal = document.getElementById('facilityModal');
  if (oldModal) oldModal.remove();
  document.body.insertAdjacentHTML('beforeend', modalContent);
  const modal = new bootstrap.Modal(document.getElementById('facilityModal'));
  modal.show();

  setTimeout(() => {
    const form = document.getElementById('reservationForm');
    const amountField = document.getElementById('amount');
    const startField = document.getElementById('date_start');
    const endField = document.getElementById('date_end');

    // Helper to recalculate total
    function recalculateAmount() {
      const start = new Date(startField.value);
      const end = new Date(endField.value);
      if (start && end && end > start) {
        const msPerDay = 1000 * 60 * 60 * 24;
        const days = Math.ceil((end - start) / msPerDay);
        const total = days * price;
        amountField.value = total.toFixed(2);
      } else {
        amountField.value = '';
      }
    }

    // Trigger calculation on input
    startField.addEventListener('input', recalculateAmount);
    endField.addEventListener('input', recalculateAmount);

    document.getElementById('reserveBtn')?.addEventListener('click', () => {
      form.classList.remove('d-none');
    });

    form?.addEventListener('submit', function (e) {
      e.preventDefault();
      const formData = new FormData(this);
      const paymentType = formData.get('payment_type');
      const amount = parseFloat(formData.get('amount'));

      const payload = {
        reservee: formData.get('reservee'),
        facility_name: formData.get('facility_name'),
        date_start: formData.get('date_start'),
        date_end: formData.get('date_end'),
        payment_type: paymentType,
        amount: amount,
        date_booked: new Date().toISOString().slice(0, 19).replace('T', ' '),
        status: 'Pending'
      };

      if (paymentType === 'Cash') {
        fetch('submit_reservation.php', {
          method: 'POST',
          body: JSON.stringify(payload),
          headers: {
            'Content-Type': 'application/json'
          }
        })
        .then(res => res.text())
        .then(response => {
          Swal.fire({
            icon: 'success',
            title: 'Reserved Successfully!',
            text: 'Your reservation has been recorded.',
          });
          modal.hide();
        })
        .catch(error => {
          Swal.fire({
            icon: 'error',
            title: 'Reservation Failed',
            text: 'There was a problem saving your reservation.',
          });
          console.error(error);
        });
      } else {
        fetch('create_payment.php', {
          method: 'POST',
          body: JSON.stringify(payload),
          headers: {
            'Content-Type': 'application/json'
          }
        })
        .then(res => res.json())
        .then(response => {
          if (response.checkout_url) {
            window.location.href = response.checkout_url;
          } else {
            Swal.fire('Error', 'Failed to initiate payment.', 'error');
          }
        })
        .catch(error => {
          Swal.fire('Error', 'Could not connect to PayMongo.', 'error');
          console.error(error);
        });
      }
    });
  }, 300);
}
