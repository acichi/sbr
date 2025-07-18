<!doctype html>
<html lang="en" data-bs-theme="auto">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Shelton Beach Haven | Admin Dashboard</title>

  <!-- Favicon and Fonts -->
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=Roboto+Sans&display=swap" rel="stylesheet">

  <!-- CSS Libraries -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">

  <!-- Custom Styles -->
  <link href="../include/css/custom.css" rel="stylesheet">
  <link href="../include/css/dashboard.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Roboto Sans', sans-serif;
      background: url('../assets/img/bg.png') no-repeat center center fixed;
      background-size: cover;
      color: #333;
    }
    h1, h2, h3, .card-title {
      font-family: 'Playfair Display', serif;
    }
    .card {
      border-radius: 1rem;
      backdrop-filter: blur(10px);
      background-color: rgba(255, 255, 255, 0.8);
    }
    .modal-content {
      border-radius: 1rem;
    }
    .table {
      background-color: rgba(255, 255, 255, 0.9);
    }
    .toast {
      border-radius: 0.75rem;
    }
    .topbar-icons {
      display: flex;
      align-items: center;
      gap: 15px;
    }
    .topbar-icons i {
      font-size: 1.4rem;
      cursor: pointer;
      color: #555;
    }
    .topbar-icons .welcome-text {
      font-weight: 500;
    }
    @media (max-width: 768px) {
      .topbar-icons {
        flex-direction: column;
        align-items: end;
      }
    }
  </style>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
  <?php include("../include/menu.php"); ?>

  <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
      <h1 class="h2">Dashboard</h1>
      <div class="d-flex align-items-center gap-3">
        <div class="topbar-icons">
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
          <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">Share</button>
            <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
          </div>
          <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#calendarModal">
            <i class="bi bi-calendar3"></i> Calendar
          </button>
          <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#addReservationModal">
            <i class="bi bi-plus-circle"></i> Add Reservation
          </button>
        </div>
      </div>
    </div>

    <!-- Stats Filter -->
    <div class="d-flex justify-content-end mb-3">
      <select id="statsFilter" class="form-select w-auto">
        <option value="7">Last 7 days</option>
        <option value="30" selected>Last 30 days</option>
        <option value="90">Last 90 days</option>
      </select>
    </div>

    <!-- Stat Cards -->
    <div class="row mb-4">
      <div class="col-6 col-md-3 mb-3">
        <div class="card shadow-sm text-center">
          <div class="card-body">
            <h5 class="card-title text-success">Total Reservations</h5>
            <h3>152</h3>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3 mb-3">
        <div class="card shadow-sm text-center">
          <div class="card-body">
            <h5 class="card-title text-primary">Total Guests</h5>
            <h3>289</h3>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3 mb-3">
        <div class="card shadow-sm text-center">
          <div class="card-body">
            <h5 class="card-title text-warning">Pending Bookings</h5>
            <h3>8</h3>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3 mb-3">
        <div class="card shadow-sm text-center">
          <div class="card-body">
            <h5 class="card-title text-danger">Feedback Alerts</h5>
            <h3>3</h3>
          </div>
        </div>
      </div>
    </div>

    <!-- Line Chart -->
    <canvas class="my-4 w-100" id="myChart" height="300"></canvas>

    <!-- Reservation Table -->
    <h2>Recent Reservations</h2>
    <div class="table-responsive small">
      <table class="table table-striped table-hover" id="recentTable">
        <thead>
          <tr>
            <th>#</th>
            <th>Guest</th>
            <th>Date</th>
            <th>Status</th>
            <th>Facility</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>101</td>
            <td>Maria Gonzales</td>
            <td>July 18, 2025</td>
            <td><span class="badge bg-success">Confirmed</span></td>
            <td>Cottage 1</td>
          </tr>
          <tr>
            <td>102</td>
            <td>Leo Cruz</td>
            <td>July 19, 2025</td>
            <td><span class="badge bg-warning">Pending</span></td>
            <td>Function Hall</td>
          </tr>
        </tbody>
      </table>
    </div>
  </main>

  <!-- Toast -->
  <div class="position-fixed bottom-0 end-0 p-3">
    <div id="dashboardToast" class="toast align-items-center text-bg-info border-0" role="alert">
      <div class="d-flex">
        <div class="toast-body">📢 New reservation added successfully!</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  </div>

  <!-- Calendar Modal -->
  <div class="modal fade" id="calendarModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Booking Calendar</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <iframe src="https://calendar.google.com/calendar/embed?src=your_calendar_link" width="100%" height="400" frameborder="0" scrolling="no"></iframe>
        </div>
      </div>
    </div>
  </div>

  <!-- Add Reservation Modal -->
  <div class="modal fade" id="addReservationModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form>
          <div class="modal-header">
            <h5 class="modal-title">Add New Reservation</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Guest Name</label>
              <input type="text" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Date</label>
              <input type="date" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Facility</label>
              <select class="form-select">
                <option>Cottage 1</option>
                <option>Cottage 2</option>
                <option>Function Hall</option>
                <option>Pool Area</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Status</label>
              <select class="form-select">
                <option>Confirmed</option>
                <option>Pending</option>
                <option>Cancelled</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.2/dist/chart.umd.js"></script>
  <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>

  <script>
    // Line Chart
    new Chart(document.getElementById('myChart'), {
      type: 'line',
      data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
          label: 'Reservations',
          data: [12, 19, 3, 5, 2, 3, 7],
          borderColor: '#7ab4a1',
          backgroundColor: 'rgba(122,180,161,0.2)',
          tension: 0.3,
          fill: true
        }]
      },
      options: { responsive: true, plugins: { legend: { display: false } } }
    });

    // Pie Chart
    new Chart(document.getElementById('statusPieChart'), {
      type: 'pie',
      data: {
        labels: ['Confirmed', 'Pending', 'Cancelled'],
        datasets: [{
          label: 'Status',
          data: [68, 22, 10],
          backgroundColor: ['#198754', '#ffc107', '#dc3545']
        }]
      },
      options: { responsive: true }
    });

    // Toast
    const toastEl = document.getElementById('dashboardToast');
    if (toastEl) new bootstrap.Toast(toastEl).show();

    // DataTables
    $(document).ready(function () {
      $('#recentTable').DataTable();
    });
  </script>
</body>
</html>
