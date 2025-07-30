<?php
session_start();
require '../properties/connection.php';

// Ensure user is logged in and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login/login.php");
    exit;
}

$username = isset($_SESSION['user']['username']) ? htmlspecialchars($_SESSION['user']['username']) : 'Admin';

/* =======================
   HELPER: Check Column Exists
   ======================= */
function columnExists($conn, $table, $column) {
    $result = $conn->query("SHOW COLUMNS FROM $table LIKE '$column'");
    return ($result && $result->num_rows > 0);
}

/* =======================
   FETCH COUNTS
   ======================= */
// Total Reservations
$resQuery = $conn->query("SELECT COUNT(*) AS count FROM reservations");
$totalReservations = $resQuery ? $resQuery->fetch_assoc()['count'] : 0;

// Total Guests
$guestQuery = $conn->query("SELECT COUNT(*) AS count FROM users WHERE role='customer'");
$totalGuests = $guestQuery ? $guestQuery->fetch_assoc()['count'] : 0;

// Total Sales
$salesQuery = $conn->query("SELECT IFNULL(SUM(amount), 0) AS total FROM reservations");
$totalSales = $salesQuery ? $salesQuery->fetch_assoc()['total'] : 0;

/* =======================
   FETCH SALES CHART DATA
   ======================= */
$chartLabels = [];
$chartData = [];
if (columnExists($conn, 'reservations', 'date_booked')) {
    $query = $conn->query("
        SELECT DATE_FORMAT(date_booked, '%b') as label, SUM(amount) as total 
        FROM reservations 
        GROUP BY MONTH(date_booked)
        ORDER BY MONTH(date_booked)
    ");
    while ($row = $query->fetch_assoc()) {
        $chartLabels[] = $row['label'];
        $chartData[] = (float) $row['total'];
    }
}

/* =======================
   FETCH ALL RESERVATIONS
   ======================= */
$dateBookedColumn = columnExists($conn, 'reservations', 'date_booked') ? 'r.date_booked' : 'NOW() AS date_booked';
$allReservations = $conn->query("
    SELECT r.reservee, u.email, r.facility_name, $dateBookedColumn, r.date_start, r.date_end, 
           r.payment_type, r.amount, r.status
    FROM reservations r
    LEFT JOIN users u ON u.username = r.reservee
    ORDER BY r.date_start DESC
");

/* =======================
   FETCH ALL ACCOUNTS
   ======================= */
$dateAddedColumn = columnExists($conn, 'users', 'date_added') ? 'date_added' : 'NOW() AS date_added';
$allAccounts = $conn->query("
    SELECT username, fullname, email, role, $dateAddedColumn
    FROM users
    ORDER BY date_added DESC
");

/* =======================
   FETCH ALL FEEDBACKS
   ======================= */
$hasTimestamp = columnExists($conn, 'feedback', 'timestamp');
$hasIsHidden = columnExists($conn, 'feedback', 'is_hidden');

$timestampCol = $hasTimestamp ? 'timestamp' : 'NOW() AS timestamp';
$isHiddenCol = $hasIsHidden ? 'is_hidden' : '0 AS is_hidden';

$whereClause = $hasIsHidden ? "WHERE is_hidden = 0 OR is_hidden IS NULL" : ""; 

$allFeedbacks = $conn->query("
    SELECT id, fullname, facility_name, feedback, rate, $timestampCol, $isHiddenCol 
    FROM feedback
    $whereClause
    ORDER BY timestamp DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Shelton Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="styles.css">
  <style>
    .btn {
      padding: 6px 12px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 14px;
    }
    .hide-btn { background: #e08f5f; color: #fff; border: none; }
    .unhide-btn { background: #7ab4a1; color: #fff; border: none; }
  </style>
</head>
<body>

<!-- HEADER -->
<div class="head-title">
  <div class="left">
    <h1>Dashboard</h1>
    <ul class="breadcrumb">
      <li><a class="active">Welcome back, <?php echo $username; ?>!</a></li>
    </ul>
  </div>
</div>

<!-- STAT CARDS -->
<ul class="box-info">
  <li>
    <i class='bx bxs-calendar-check'></i>
    <span class="text">
      <h3><?php echo $totalReservations; ?></h3>
      <p>Total Reservations</p>
    </span>
  </li>
  <li>
    <i class='bx bxs-group'></i>
    <span class="text">
      <h3><?php echo $totalGuests; ?></h3>
      <p>Total Guests</p>
    </span>
  </li>
  <li>
    <i class='bx bxs-dollar-circle'></i>
    <span class="text">
      <h3 id="salesCount">$<?php echo number_format($totalSales,2); ?></h3>
      <p>Total Sales</p>
    </span>
  </li>
</ul>

<!-- CHARTS: Sales & Feedback -->
<div class="table-data" style="display:flex; gap:24px; flex-wrap:wrap; margin-bottom:24px;">

  <!-- Sales Chart -->
  <div class="todo" style="flex:1; min-width:300px;">
    <div class="head">
      <h3 id="reports">Sales Overview</h3>
      <select id="salesRangeSelect" style="margin-left:auto; padding:5px 10px; border:1px solid #ccc;">
        <option value="weekly">Weekly</option>
        <option value="monthly" selected>Monthly</option>
        <option value="yearly">Yearly</option>
      </select>
    </div>
    <div id="totalSales" style="margin:10px 0; font-weight:bold; color:#4bc0c0;">
      Total Sales: $<?php echo number_format($totalSales,2); ?>
    </div>
    <canvas id="salesChart" width="400" height="300"></canvas>
  </div>

  <!-- Feedback Chart -->
  <div class="todo" style="flex:1; min-width:300px;">
    <div class="head">
      <h3>Feedback Overview</h3>
      <select id="feedbackRangeSelect" style="margin-left:auto; padding:5px 10px; border:1px solid #ccc;">
        <option value="weekly">Weekly</option>
        <option value="monthly" selected>Monthly</option>
        <option value="yearly">Yearly</option>
      </select>
    </div>
    <canvas id="feedbackChart" width="400" height="300"></canvas>
  </div>
</div>


<!-- RESERVATIONS TABLE -->
<div class="table-data">
  <div class="order" style="width:100%; margin-bottom:20px;">
    <div class="head">
      <h3 id="reservation">All Reservations</h3></div>
    <table>
      <thead>
        <tr>
          <th>Reservee</th><th>Email</th><th>Facility</th><th>Date Booked</th>
          <th>Start Date</th><th>End Date</th><th>Payment</th><th>Amount</th><th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if($allReservations && $allReservations->num_rows>0) {
          while($row=$allReservations->fetch_assoc()) { ?>
          <tr>
            <td><?php echo htmlspecialchars($row['reservee']); ?></td>
            <td><?php echo htmlspecialchars($row['email'] ?? 'N/A'); ?></td>
            <td><?php echo htmlspecialchars($row['facility_name']); ?></td>
            <td><?php echo date('m-d-Y', strtotime($row['date_booked'])); ?></td>
            <td><?php echo date('m-d-Y H:i', strtotime($row['date_start'])); ?></td>
            <td><?php echo date('m-d-Y H:i', strtotime($row['date_end'])); ?></td>
            <td><?php echo htmlspecialchars($row['payment_type']); ?></td>
            <td>$<?php echo number_format($row['amount'], 2); ?></td>
            <td><?php echo ucfirst($row['status']); ?></td>
          </tr>
        <?php } } else { ?>
          <tr><td colspan="9">No reservations found.</td></tr>
        <?php } ?>
      </tbody>
    </table>
  </div>

  <!-- ACCOUNTS TABLE -->
  <div class="order" style="width:100%; margin-bottom:20px;">
    <div class="head">
      <h3 id="accounts">All Accounts</h3></div>
    <table>
      <thead>
        <tr>
          <th>Username</th><th>Full Name</th><th>Email</th><th>Role</th><th>Date Added</th>
        </tr>
      </thead>
      <tbody>
        <?php if($allAccounts && $allAccounts->num_rows>0) {
          while($row=$allAccounts->fetch_assoc()) { ?>
          <tr>
            <td><?php echo htmlspecialchars($row['username']); ?></td>
            <td><?php echo htmlspecialchars($row['fullname']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo ucfirst($row['role']); ?></td>
            <td><?php echo date('m-d-Y', strtotime($row['date_added'])); ?></td>
          </tr>
        <?php } } else { ?>
          <tr><td colspan="5">No accounts found.</td></tr>
        <?php } ?>
      </tbody>
    </table>
  </div>

  <!-- FEEDBACK MANAGEMENT -->
  <div class="order" style="width:100%;">
    <div class="head">
      <h3 id="feedback">Manage Feedbacks</h3></div>
    <table>
      <thead>
        <tr>
          <th>User</th><th>Facility</th><th>Rating</th><th>Comment</th><th>Date</th><th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if($allFeedbacks && $allFeedbacks->num_rows>0) {
          while($row=$allFeedbacks->fetch_assoc()) { ?>
          <tr>
            <td><?php echo htmlspecialchars($row['fullname']); ?></td>
            <td><?php echo htmlspecialchars($row['facility_name']); ?></td>
            <td><?php for($i=0; $i<$row['rate']; $i++) echo "⭐"; ?></td>
            <td><?php echo htmlspecialchars($row['feedback']); ?></td>
            <td><?php echo date('m-d-Y', strtotime($row['timestamp'])); ?></td>
            <td>
              <?php if($row['is_hidden'] == 0) { ?>
                <button class="btn hide-btn">Hide</button>
              <?php } else { ?>
                <button class="btn unhide-btn">Unhide</button>
              <?php } ?>
            </td>
          </tr>
        <?php } } else { ?>
          <tr><td colspan="6">No feedbacks found.</td></tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>

<script>
// ----- Sales Chart -----
const salesCtx = document.getElementById('salesChart').getContext('2d');
new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($chartLabels); ?>,
        datasets: [{
            label: 'Total Sales',
            data: <?php echo json_encode($chartData); ?>,
            borderColor: '#4bc0c0',
            backgroundColor: 'rgba(75,192,192,0.2)',
            borderWidth: 2,
            tension: 0.3,
            fill: true,
            pointBackgroundColor: '#4bc0c0'
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false }}, scales: { y: { beginAtZero: true }} }
});

// ----- Feedback Chart -----
const feedbackCtx = document.getElementById('feedbackChart').getContext('2d');
let feedbackChart = null;

function loadFeedbackChart(range = 'monthly') {
    fetch('fetch_feedback_chart.php?range=' + range)
    .then(res => res.json())
    .then(data => {
        if (feedbackChart) feedbackChart.destroy();
        feedbackChart = new Chart(feedbackCtx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Feedback Count',
                    data: data.counts,
                    backgroundColor: '#e08f5f',
                    borderRadius: 6
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false }}, scales: { y: { beginAtZero: true }} }
        });
    });
}
loadFeedbackChart('monthly');
document.getElementById('feedbackRangeSelect').addEventListener('change', e => loadFeedbackChart(e.target.value));
</script>
</body>
</html>
