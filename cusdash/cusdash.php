<?php
session_start();
require '../properties/connection.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../Login/login.php");
    exit;
}

$userId = $_SESSION['user']['id'];

$query = "SELECT fullname, gender, email, number, date_added FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$fullname = "Guest";
$salutation = "Welcome";
$email = "";
$number = "";
$memberSince = "";

if ($result && $row = $result->fetch_assoc()) {
    $fullname = $row['fullname'];
    $gender = strtolower($row['gender']);
    $email = $row['email'];
    $number = $row['number'];
    $memberSince = date('Y', strtotime($row['date_added'] ?? 'now'));

    $firstName = explode(' ', $fullname)[0];

    if ($gender === 'male') {
        $salutation = "Welcome Mr. $firstName";
    } elseif ($gender === 'female') {
        $salutation = "Welcome Ms. $firstName";
    } else {
        $salutation = "Welcome $firstName";
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
  <link rel="stylesheet" href="css/style.css" />
  <title>Dashboard</title>
</head>
<body>

<?php include("menu.php") ?>

<section id="content">
  <!-- Navbar -->
  <nav>
    <i class='bx bx-menu'></i>
    <a href="#" class="nav-link">Shelton Beach</a>
    <form action="#">
      <div class="form-input">
        <input type="search" placeholder="Search..." />
        <button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
      </div>
    </form>
    <input type="checkbox" id="switch-mode" hidden />
    <label for="switch-mode" class="switch-mode"></label>
    <a href="#" class="notification">
      <i class='bx bxs-bell'></i>
      <span class="num">8</span>
    </a>
    <a href="#" class="profile"><img src="prof.png" /></a>
  </nav>

  <!-- Main Dashboard -->
  <main>
    <!-- Welcome -->
    <div class="head-title">
      <div class="left">
        <h1>Dashboard</h1>
        <ul class="breadcrumb">
          <li><a href="#" class="active"><?= htmlspecialchars($salutation) ?></a></li>
        </ul>
      </div>
    </div>

    <!-- Summary Boxes -->
    <ul class="box-info">
      <li><i class='bx bxs-calendar-check'></i><div class="text"><h3>5</h3><p>Upcoming Reservations</p></div></li>
      <li><i class='bx bxs-star'></i><div class="text"><h3>12</h3><p>Reviews Sent</p></div></li>
      <li><i class='bx bxs-dollar-circle'></i><div class="text"><h3>$1,250</h3><p>Total Spent</p></div></li>
    </ul>

    <!-- Account Details -->
    <div class="account-details">
      <h3>Account Details</h3>
      <p><strong>Name:</strong> <?= htmlspecialchars($fullname) ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
      <p><strong>Phone:</strong> <?= htmlspecialchars($number) ?></p>
      <p><strong>Member since:</strong> <?= $memberSince ?></p>
    </div>

    <!-- Reservation Table -->
    <div class="table-data">
      <div class="order">
        <div class="head"><h3>My Reservations</h3></div>
        <table>
          <thead><tr><th>Reservation ID</th><th>Date</th><th>Service</th><th>Status</th></tr></thead>
          <tbody>
            <tr><td>#1001</td><td>2025-08-15</td><td>Cottage</td><td><span class="status completed">Confirmed</span></td></tr>
            <tr><td>#1002</td><td>2025-08-20</td><td>Table</td><td><span class="status process">Pending</span></td></tr>
            <tr><td>#1003</td><td>2025-08-25</td><td>Room</td><td><span class="status pending">Cancelled</span></td></tr>
          </tbody>
        </table>
      </div>

      <!-- Transactions -->
      <div class="order">
        <div class="head"><h3>Transaction History</h3></div>
        <table>
          <thead><tr><th>Transaction ID</th><th>Date</th><th>Amount</th><th>Method</th></tr></thead>
          <tbody>
            <tr><td>T202501</td><td>2025-07-01</td><td>$300</td><td>Credit Card</td></tr>
            <tr><td>T202502</td><td>2025-06-15</td><td>$450</td><td>PayPal</td></tr>
            <tr><td>T202503</td><td>2025-06-10</td><td>$500</td><td>Credit Card</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Reviews -->
    <div class="table-data">
      <div class="order full-width">
        <div class="head"><h3>Reviews Sent</h3></div>
        <table>
          <thead><tr><th>Review ID</th><th>Date</th><th>Service</th><th>Rating</th></tr></thead>
          <tbody>
            <tr><td>R1001</td><td>2025-07-05</td><td>Hotel Booking</td><td>⭐⭐⭐⭐⭐</td></tr>
            <tr><td>R1002</td><td>2025-06-20</td><td>Car Rental</td><td>⭐⭐⭐⭐</td></tr>
            <tr><td>R1003</td><td>2025-06-12</td><td>Flight Ticket</td><td>⭐⭐⭐⭐⭐</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Reservation Progress -->
    <div class="section status-tracker">
      <h3>Reservation Progress</h3>
      <p><strong>Reservation #1001:</strong> Cottage - <span class="status completed">Confirmed</span></p>
      <div class="progress-bar-container">
        <div class="progress-bar" style="width: 50%;">Confirmed</div>
      </div>
    </div>

    <!-- Receipts -->
    <div class="section receipts">
      <h3>Download Receipts</h3>
      <ul>
        <li><a href="receipts/receipt_1001.pdf" target="_blank">📄 Receipt for Reservation #1001</a></li>
        <li><a href="receipts/receipt_1002.pdf" target="_blank">📄 Receipt for Reservation #1002</a></li>
      </ul>
    </div>

    <!-- Feedback -->
    <div class="section feedback-summary">
      <h3>Your Feedback Summary</h3>
      <p><strong>Average Rating:</strong> ⭐ 4.7</p>
      <div class="card">“Wonderful stay. Clean rooms and friendly staff.”<br><small>- June 2025, Hotel Booking</small></div>
      <div class="card">“Car was delivered late but condition was good.”<br><small>- May 2025, Car Rental</small></div>
    </div>

    <!-- Notifications -->
    <div class="section notifications">
      <h3>Recent Notifications</h3>
      <ul>
        <li>✅ Your reservation #1001 has been confirmed.</li>
        <li>📢 New promo: Get 20% off on weekday bookings!</li>
        <li>⚠️ Please verify your email to continue booking.</li>
      </ul>
    </div>

    <!-- Payment Methods -->
    <div class="section payment-methods">
      <h3>Saved Payment Methods</h3>
      <ul>
        <li>💳 Visa ending in 4242</li>
        <li>📱 GCash - Registered</li>
      </ul>
    </div>
  </main>
</section>

<script src="js/script.js"></script>
</body>
</html>
