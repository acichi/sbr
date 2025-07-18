<!doctype html>
<html lang="en" data-bs-theme="auto">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Facility Map - Customer View</title>

  <!-- Bootstrap -->
  <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom CSS Files -->
  <link href="../include/css/custom.css" rel="stylesheet">
  <link href="../include/css/dashboard.css" rel="stylesheet">
  <link rel="stylesheet" href="css/pins.css">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&display=swap" rel="stylesheet">

  <!-- Inline Theme Styling -->
  <style>
    body, h1, h2, h3, h4, h5, h6, p, a, span, label, button {
      font-family: 'Playfair Display', serif;
      color: #333;
    }

    body {
      background: url('../images/bg.png') no-repeat center center fixed;
      background-size: cover;
    }

    .h2 {
      color: #e08f5f;
      font-weight: bold;
      text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.05);
    }

    .modal-content {
      background-color: rgba(255, 255, 255, 0.95);
      border-radius: 15px;
      border: none;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .modal-header, .modal-footer {
      border: none;
    }

    #modalFacilityImage {
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .btn-success {
      background-color: #7ab4a1;
      border: none;
    }
    .btn-success:hover {
      background-color: #689e8f;
    }

    .btn-secondary {
      background-color: #e19985;
      border: none;
    }
    .btn-secondary:hover {
      background-color: #d38875;
    }

    
  </style>
</head>
<body>
  <?php include("../include/menu_client.php"); ?>

  <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
      <h1 class="h2">Available Facilities</h1>
    </div>

    <div class="svg-container mb-4">
      <svg id="map" viewBox="0 0 900 500" preserveAspectRatio="xMidYMid meet">
        <image href="images/map.svg" width="900" height="500" />
        <!-- Facility markers will be loaded dynamically here -->
      </svg>
    </div>

    <!-- Facility Details Modal -->
    <div class="modal fade" id="facilityDetailsModal" tabindex="-1" aria-labelledby="facilityDetailsLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="facilityDetailsLabel">Facility Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <h5 id="modalFacilityName" class="fw-bold"></h5>
            <p id="modalFacilityDetails"></p>
            <p><strong>Status:</strong> <span id="modalFacilityStatus"></span></p>
            <p><strong>Price:</strong> ₱<span id="modalFacilityPrice"></span></p>
            <img id="modalFacilityImage" src="" alt="Facility Image" class="img-fluid">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success" id="bookNowBtn">Book Now</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="../assets/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="js/retrieve_pin_customer.js"></script>

  <script>
    // Booking logic
    $('#bookNowBtn').on('click', function () {
      Swal.fire({
        icon: 'success',
        title: 'Booking Requested!',
        text: 'We will process your booking shortly.',
        timer: 2000,
        showConfirmButton: false
      });
      $('#facilityDetailsModal').modal('hide');
    });
  </script>
</body>
</html>
