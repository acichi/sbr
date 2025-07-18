<?php
require("../properties/connection.php");

// Fetch all available facilities
$sql = "SELECT * FROM facility WHERE status = 'available'";
$result = $conn->query($sql);

// Fetch user data if logged in
$userData = null;
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $userQuery = "SELECT * FROM users WHERE id = $userId";
    $userResult = $conn->query($userQuery);
    if ($userResult && $userResult->num_rows > 0) {
        $userData = $userResult->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Facility Booking</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/facility.css" rel="stylesheet">
  <link href="css/map.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script>
    const isLoggedIn = <?= isset($_SESSION['fullname']) ? 'true' : 'false' ?>;
  </script>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }

    h1, h2, h3, h4, h5, .card-title, .modal-title {
      font-family: 'Great Vibes', cursive;
      font-weight: 500;
      letter-spacing: 0.5px;
      color: #2c3e50 !important;
    }

    .btn-primary {
      background-color: #2c3e50 !important;
      border-color: #2c3e50 !important;
    }

    .btn-primary:hover {
      background-color: #34495e !important;
      border-color: #34495e !important;
    }

    .btn-secondary {
      background-color: #e74c3c !important;
      border-color: #e74c3c !important;
    }

    .btn-secondary:hover {
      background-color: #c0392b !important;
      border-color: #c0392b !important;
    }

    .btn-success {
      background-color: #e67e22 !important;
      border-color: #e67e22 !important;
    }

    .btn-success:hover {
      background-color: #d35400 !important;
      border-color: #d35400 !important;
    }

    /* Themed styling for details container */
    #details-container {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 60%; /* Smaller width */
      height: 40%;
      max-width: 400px; /* Smaller max-width */
      background-color: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
      z-index: 1000;
      border: 2px solid #2c3e50;
      font-family: 'Poppins', sans-serif;
    }

    #details-container h5 {
      font-family: 'Great Vibes', cursive;
      font-size: 32px;
      color: #2c3e50;
      margin-bottom: 15px;
    }

    #detailsPrice {
      color: #e67e22;
      font-weight: 600;
    }

    #closeDetails {
      position: absolute;
      top: 10px;
      right: 10px;
      background-color: #e74c3c;
      border: none;
      color: white;
      border-radius: 50%;
      width: 30px;
      height: 30px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      padding: 0;
      line-height: 1;
    }

    #bookNowButton {
      background-color: #2c3e50;
      border: none;
      padding: 10px 20px;
      border-radius: 30px;
      transition: all 0.3s ease;
      font-weight: 500;
      letter-spacing: 0.5px;
    }

    #bookNowButton:hover {
      background-color: #34495e;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Overlay for details container */
    .overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 999;
    }

    /* Themed Card Styling */
    .card {
      border-radius: 12px;
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }

    .card-header {
      background-color: #f8f8f8 !important;
      color: #2c3e50 !important;
      font-family: 'Poppins', sans-serif;
    }

    .show-details {
      background-color: white;
      color: #2c3e50;
      border-color: #2c3e50;
      border-radius: 30px !important;
      transition: all 0.3s ease;
    }

    .show-details:hover {
      background-color: #2c3e50;
      color: white;
      transform: translateY(-2px);
    }

    /* Modal Styling */
    .modal-content {
      border-radius: 12px !important;
      border: none;
    }

    .modal-header {
      background-color: #2c3e50;
      color: white;
      border-bottom: none;
      border-radius: 12px 12px 0 0 !important;
    }

    .modal-header .modal-title {
      font-family: 'Great Vibes', cursive;
      font-size: 28px;
      color: white;
    }

    .modal-header .btn-close {
      color: white;
      opacity: 1;
    }

    .form-control, .form-select {
      border-radius: 8px;
      border: 1px solid #ddd;
      padding: 10px 15px;
    }

    .form-control:focus, .form-select:focus {
      border-color: #2c3e50;
      box-shadow: 0 0 0 0.25rem rgba(44, 62, 80, 0.25);
    }

    /* Receipt styling */
    #receiptContent h4 {
      font-family: 'Great Vibes', cursive;
      font-size: 32px;
      color: #2c3e50;
    }

    #receiptContent table {
      border-radius: 8px;
      overflow: hidden;
    }

    #receiptContent th {
      background-color: rgba(44, 62, 80, 0.1);
      color: #2c3e50;
    }
  </style>
</head>
<body class="bg-light">
<?php include('../menu/menu.php'); ?>
<div class="container py-5">
  <h1 class="text-center mb-4">Available Facilities</h1>

  <div class="row g-4 mb-4">
    <!-- Map Column -->
    <div class="col-lg-8">
      <div id="map-container"></div>
    </div>

    <!-- Profile Column -->
    <div class="col-lg-4">
      <?php if (!empty($userData)): ?>
        <div class="card h-100 shadow-lg rounded-3">
          <div class="card-header text-white text-center" style="background-color: #2c3e50; padding: 15px;">
            <h5>Good Day!</h5>
            <h5 class="mb-0"><?= htmlspecialchars($userData['fullname']) ?></h5>
            <hr class="my-2" style="border-color: white;">
          </div>
          <div class="card-body text-center">
            <p><strong>Email:</strong> <?= htmlspecialchars($userData['email']) ?></p>
            <p><strong>Contact:</strong> <?= htmlspecialchars($userData['number']) ?></p>
            <p><strong>Gender:</strong> <?= htmlspecialchars($userData['gender']) ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($userData['address']) ?></p>
            <p><strong>Role:</strong> <?= htmlspecialchars($userData['role']) ?></p>
            <p class="text-muted"><small>Member since: <?= date("F d, Y", strtotime($userData['date_added'])) ?></small></p>
          </div>
        </div>
      <?php else: ?>
        <div class="card h-100 shadow-lg rounded-3">
          <div class="card-header text-white text-center" style="background-color: #2c3e50; padding: 15px;">
            <h5>Not Logged In</h5>
          </div>
          <div class="card-body text-center">
            <p class="text-muted">Please log in to view your profile and make bookings.</p>
            <a href="../client/login.php" class="btn btn-primary rounded-pill">Log In</a>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="row g-4">
    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="col-md-4">
          <div class="card h-100 shadow-lg rounded-3 border-0">
            <img src="../<?= htmlspecialchars($row['image']) ?>" class="card-img-top rounded-3" alt="<?= htmlspecialchars($row['name']) ?>" style="height: 200px; object-fit: cover;">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
              <p class="card-text"><?= htmlspecialchars($row['details']) ?></p>
              <p class="fw-bold" style="color: #e67e22;">Price: ₱<?= number_format($row['price'], 2) ?></p>
              <button
                class="btn btn-outline-primary mt-auto show-details rounded-pill"
                data-facility="<?= htmlspecialchars($row['name']) ?>"
                data-details="<?= htmlspecialchars($row['details']) ?>"
                data-price="<?= $row['price'] ?>"
                style="transition: background-color 0.3s, color 0.3s;">
                Show Details
              </button>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="text-center">No facilities available at the moment.</p>
    <?php endif; ?>
  </div>
</div>

<!-- Details Container (Fixed Positioning) -->
<div class="overlay" id="overlay"></div>
<div id="details-container">
  <button id="closeDetails" class="btn-close"></button>
  <h5 id="detailsTitle" class="card-title"></h5>
  <p id="detailsText" class="card-text"></p>
  <p class="fw-bold" id="detailsPrice"></p>
  <button id="bookNowButton" class="btn btn-primary mt-3 w-100" data-bs-toggle="modal" data-bs-target="#reservationModal">Book Now</button>
</div>

<!-- Reservation Modal -->
<div class="modal fade" id="reservationModal" tabindex="-1" aria-labelledby="reservationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4 shadow">
      <div class="modal-header">
        <h5 class="modal-title" id="reservationModalLabel">Reservation Form</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php if (!isset($_SESSION['fullname'])): ?>
          <div class="alert alert-warning text-center" style="background-color: rgba(224, 143, 95, 0.1); color: #e67e22; border-color: #e67e22;">
            <strong>Note:</strong> You must <a href="../login.php" style="color: #e67e22; text-decoration: underline;">log in</a> to make a reservation.
          </div>
        <?php endif; ?>
        <form id="reservationForm">
          <input type="hidden" id="facilityName" name="facilityName">

          <div class="mb-3">
            <label class="form-label">Facility</label>
            <input type="text" class="form-control" id="facilityDisplay" readonly>
          </div>

          <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= $_SESSION['fullname'] ?? '' ?>" required>
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= $_SESSION['email'] ?? '' ?>" required>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="startDate" class="form-label">Check in</label>
              <input type="date" class="form-control" id="startDate" name="startDate" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="endDate" class="form-label">Check out</label>
              <input type="date" class="form-control" id="endDate" name="endDate" required>
            </div>
          </div>

          <div class="mb-3">
            <label for="price" class="form-label">Total Price</label>
            <input type="text" class="form-control" id="price" name="price" readonly>
          </div>

          <div class="mb-3">
            <label for="paymentMethod" class="form-label">Payment Method</label>
            <select class="form-select" id="paymentMethod" name="paymentMethod" required>
              <option value="" disabled selected>Select a payment method</option>
              <option value="Cash">Cash</option>
            </select>
          </div>

          <div class="mb-3 d-none" id="cashAmountGroup">
            <label for="cashAmount" class="form-label">Enter Cash Amount</label>
            <input type="number" step="0.01" class="form-control" id="cashAmount" name="cashAmount" placeholder="Enter amount paid in cash">
            <div class="mt-2 fw-bold" id="paymentResult"></div>
          </div>

          <div class="mb-3">
            <label for="notes" class="form-label">Additional Notes</label>
            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
          </div>

          <button type="submit" class="btn btn-primary w-100 rounded-pill">Submit Reservation</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Receipt Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4 shadow">
      <div class="modal-header">
        <h5 class="modal-title" id="receiptModalLabel">Transaction Receipt</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="receiptContent">
        <div class="text-center mb-3">
          <h4 class="fw-bold">Reservation Receipt</h4>
          <p class="text-muted" id="receipt-timestamp"></p>
        </div>
        <table class="table table-bordered">
          <tbody>
            <tr><th>Transaction ID</th><td id="receipt-transaction-id"></td></tr>
            <tr><th>Reservee</th><td id="receipt-reservee"></td></tr>
            <tr><th>Facility</th><td id="receipt-facility"></td></tr>
            <tr><th>Amount Paid</th><td id="receipt-amount-paid"></td></tr>
            <tr><th>Payment Type</th><td id="receipt-payment-type"></td></tr>
            <tr><th>Check-In</th><td id="receipt-date-checkin"></td></tr>
            <tr><th>Check-Out</th><td id="receipt-date-checkout"></td></tr>
            <tr><th>Date Booked</th><td id="receipt-date-booked"></td></tr>
          </tbody>
        </table>
        <p>Please download or print the receipt for your records.</p>
        <button id="downloadPdfBtn" class="btn btn-success w-100 mt-3 rounded-pill">Download PDF</button>
        <button id="printReceiptBtn" class="btn btn-secondary w-100 mt-3 rounded-pill">Print Receipt</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="js/reservation.js"></script>
<script src="js/map.js"></script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const bookNowBtn = document.getElementById("bookNowButton");
    const detailsContainer = document.getElementById("details-container");
    const overlay = document.getElementById("overlay");
    const closeDetailsBtn = document.getElementById("closeDetails");
    const showDetailsButtons = document.querySelectorAll(".show-details");

    // Show details
    showDetailsButtons.forEach(btn => {
      btn.addEventListener("click", function() {
        const facility = this.getAttribute("data-facility");
        const details = this.getAttribute("data-details");
        const price = this.getAttribute("data-price");

        // Update details content
        document.getElementById("detailsTitle").textContent = facility;
        document.getElementById("detailsText").textContent = details;
        document.getElementById("detailsPrice").textContent = `Price: ₱${parseFloat(price).toFixed(2)}`;

        // Update form fields
        document.getElementById("facilityName").value = facility;
        document.getElementById("facilityDisplay").value = facility;

        // Calculate default price
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);

        const startDateField = document.getElementById("startDate");
        const endDateField = document.getElementById("endDate");

        startDateField.value = today.toISOString().split('T')[0];
        endDateField.value = tomorrow.toISOString().split('T')[0];

        // Set price
        document.getElementById("price").value = `₱${parseFloat(price).toFixed(2)}`;

        // Show overlay and details
        overlay.style.display = "block";
        detailsContainer.style.display = "block";
      });
    });

    // Close details
    closeDetailsBtn.addEventListener("click", function() {
      overlay.style.display = "none";
      detailsContainer.style.display = "none";
    });

    // Close details when clicking on overlay
    overlay.addEventListener("click", function() {
      overlay.style.display = "none";
      detailsContainer.style.display = "none";
    });

    if (bookNowBtn) {
      bookNowBtn.addEventListener("click", function (e) {
        if (!isLoggedIn) {
          e.preventDefault();
          Swal.fire({
            icon: 'warning',
            title: 'Login Required',
            text: 'Please log in to make a reservation.',
            confirmButtonText: 'Login',
            confirmButtonColor: '#2c3e50',
            background: '#fff',
            iconColor: '#e67e22'
          }).then((result) => {
            if (result.isConfirmed) {
              window.location.href = "../client/login.php";
            }
          });
        }
      });
    }

    // Reservation form handling
    const reservationForm = document.getElementById("reservationForm");
    if (reservationForm) {
      // Calculate price when dates change
      const startDateInput = document.getElementById("startDate");
      const endDateInput = document.getElementById("endDate");
      const priceInput = document.getElementById("price");

      function updatePrice() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);

        if (startDate && endDate && !isNaN(startDate) && !isNaN(endDate)) {
          // Calculate days difference
          const timeDiff = endDate - startDate;
          const daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));

          if (daysDiff > 0) {
            // Get base price from facility
            const facilityPrice = parseFloat(document.getElementById("detailsPrice").textContent.replace('Price: ₱', ''));
            const totalPrice = facilityPrice * daysDiff;

            priceInput.value = `₱${totalPrice.toFixed(2)}`;
          }
        }
      }

      startDateInput.addEventListener("change", updatePrice);
      endDateInput.addEventListener("change", updatePrice);

      // Show/hide cash amount field based on payment method
      const paymentMethodSelect = document.getElementById("paymentMethod");
      const cashAmountGroup = document.getElementById("cashAmountGroup");

      paymentMethodSelect.addEventListener("change", function() {
        if (this.value === "Cash") {
          cashAmountGroup.classList.remove("d-none");
        } else {
          cashAmountGroup.classList.add("d-none");
        }
      });

      // Calculate change for cash payment
      const cashAmountInput = document.getElementById("cashAmount");
      const paymentResultDiv = document.getElementById("paymentResult");

      cashAmountInput.addEventListener("input", function() {
        const cashAmount = parseFloat(this.value);
        const totalPrice = parseFloat(priceInput.value.replace('₱', ''));

        if (!isNaN(cashAmount) && !isNaN(totalPrice)) {
          if (cashAmount >= totalPrice) {
            const change = cashAmount - totalPrice;
            paymentResultDiv.textContent = `Change: ₱${change.toFixed(2)}`;
            paymentResultDiv.style.color = "#2c3e50";
          } else {
            paymentResultDiv.textContent = `Insufficient amount!`;
            paymentResultDiv.style.color = "#e74c3c";
          }
        }
      });

      // Form submission
      reservationForm.addEventListener("submit", function (e) {
        e.preventDefault();

        if (!isLoggedIn) {
          Swal.fire({
            icon: 'warning',
            title: 'Login Required',
            text: 'You must log in before submitting.',
            confirmButtonText: 'Login',
            confirmButtonColor: '#2c3e50',
            background: '#fff',
            iconColor: '#e67e22'
          }).then((result) => {
            if (result.isConfirmed) {
              window.location.href = "../client/login.php";
            }
          });
          return;
        }

        // Generate transaction ID
        const transactionId = "TR" + Date.now().toString().slice(-8);

        // Fill receipt data
        document.getElementById("receipt-transaction-id").textContent = transactionId;
        document.getElementById("receipt-reservee").textContent = document.getElementById("name").value;
        document.getElementById("receipt-facility").textContent = document.getElementById("facilityDisplay").value;
        document.getElementById("receipt-amount-paid").textContent = document.getElementById("price").value;
        document.getElementById("receipt-payment-type").textContent = document.getElementById("paymentMethod").value;
        document.getElementById("receipt-date-checkin").textContent = document.getElementById("startDate").value;
        document.getElementById("receipt-date-checkout").textContent = document.getElementById("endDate").value;
        document.getElementById("receipt-date-booked").textContent = new Date().toLocaleDateString();
        document.getElementById("receipt-timestamp").textContent = new Date().toLocaleString();

        // Hide reservation modal and show receipt
        const reservationModal = bootstrap.Modal.getInstance(document.getElementById('reservationModal'));
        reservationModal.hide();

        // Show receipt modal
        const receiptModal = new bootstrap.Modal(document.getElementById('receiptModal'));
        receiptModal.show();
      });

      // Disable form if not logged in
      if (!isLoggedIn) {
        reservationForm.querySelectorAll("input, select, textarea, button[type=submit]")
          .forEach(el => el.setAttribute("disabled", "disabled"));
      }

      // PDF Download
      document.getElementById("downloadPdfBtn").addEventListener("click", function() {
        const element = document.getElementById("receiptContent");
        const opt = {
          margin: 1,
          filename: 'receipt.pdf',
          image: { type: 'jpeg', quality: 0.98 },
          html2canvas: { scale: 2 },
          jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
        };

        html2pdf().set(opt).from(element).save();
      });

      // Print Receipt
      document.getElementById("printReceiptBtn").addEventListener("click", function() {
        const printContent = document.getElementById("receiptContent").innerHTML;
        const originalContent = document.body.innerHTML;

        document.body.innerHTML = `
          <div style="padding: 20px;">
            ${printContent}
          </div>
        `;

        window.print();
        document.body.innerHTML = originalContent;

        // Reattach event listeners
        document.addEventListener("DOMContentLoaded", function() {
          location.reload();
        });
      });
    }

    // Initialize min dates to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById("startDate").setAttribute("min", today);
    document.getElementById("endDate").setAttribute("min", today);

    // Initialize SweetAlert2 styling
    Swal.mixin({
      customClass: {
        confirmButton: 'btn btn-primary rounded-pill',
        cancelButton: 'btn btn-secondary rounded-pill'
      },
      buttonsStyling: false
    });
  });
</script>
</body>
</html>
<?php $conn->close(); ?>
