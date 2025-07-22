<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Boxicons -->
  <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
  <!-- My CSS -->
  <link rel="stylesheet" href="css/style.css" />

  <title> Dashboard</title>
</head>
<body>
  <!-- SIDEBAR -->
<?php include ("menu.php")?>
  <!-- SIDEBAR -->

  <!-- CONTENT -->
  <section id="content">
    <!-- NAVBAR -->
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
      <a href="#" class="profile">
        <img src="prof.png" />
      </a>
    </nav>
    <!-- NAVBAR -->

    <!-- MAIN -->

    <main>
      <div class="head-title">
        <div class="left">
          <h1>Dashboard</h1>
          <ul class="breadcrumb">
            <li><a href="#" class="active">Home</a></li>
            <li><a href="#">Dashboard</a></li>
          </ul>
        </div>
      </div>

      <ul class="box-info">
        <li>
          <i class='bx bxs-calendar-check'></i>
          <div class="text">
            <h3>5</h3>
            <p>Upcoming Reservations</p>
          </div>
        </li>
        <li>
          <i class='bx bxs-star '></i>
          <div class="text">
            <h3>12</h3>
            <p>Reviews Sent</p>
          </div>
        </li>
        <li>
          <i class='bx bxs-dollar-circle'></i>
          <div class="text">
            <h3>$1,250</h3>
            <p>Total Spent</p>
          </div>
        </li>
      </ul>
<div class="account-details">
  <h3>Account Details</h3>
  <p><strong>Name:</strong> Andrea Camille M. Antivola</p>
  <p><strong>Email:</strong> acbt21tata@gmail.com</p>
  <p><strong>Phone:</strong> 09072891557</p>
  <p><strong>Member since:</strong> 2023</p>
</div>

      <div class="table-data">
        <div class="order">
          <div class="head">
            <h3>My Reservations</h3>
          </div>
          <table>
            <thead>
              <tr>
                <th>Reservation ID</th>
                <th>Date</th>
                <th>Service</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>#1001</td>
                <td>2025-08-15</td>
                <td>Cottage</td>
                <td><span class="status completed">Confirmed</span></td>
              </tr>
              <tr>
                <td>#1002</td>
                <td>2025-08-20</td>
                <td>Table</td>
                <td><span class="status process">Pending</span></td>
              </tr>
              <tr>
                <td>#1003</td>
                <td>2025-08-25</td>
                <td>Room</td>
                <td><span class="status pending">Cancelled</span></td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="order">
          <div class="head">
            <h3 id="history">Transaction History</h3>
          </div>
          <table>
            <thead>
              <tr>
                <th>Transaction ID</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Method</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>T202501</td>
                <td>2025-07-01</td>
                <td>$300</td>
                <td>Credit Card</td>
              </tr>
              <tr>
                <td>T202502</td>
                <td>2025-06-15</td>
                <td>$450</td>
                <td>PayPal</td>
              </tr>
              <tr>
                <td>T202503</td>
                <td>2025-06-10</td>
                <td>$500</td>
                <td>Credit Card</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="table-data">
        <div class="order" style="width: 100%;">
          <div class="head">
            <h3>Reviews Sent</h3>
          </div>
          <table>
            <thead>
              <tr>
                <th>Review ID</th>
                <th>Date</th>
                <th>Service</th>
                <th>Rating</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>R1001</td>
                <td>2025-07-05</td>
                <td>Hotel Booking</td>
                <td>⭐⭐⭐⭐⭐</td>
              </tr>
              <tr>
                <td>R1002</td>
                <td>2025-06-20</td>
                <td>Car Rental</td>
                <td>⭐⭐⭐⭐</td>
              </tr>
              <tr>
                <td>R1003</td>
                <td>2025-06-12</td>
                <td>Flight Ticket</td>
                <td>⭐⭐⭐⭐⭐</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

  <script src="js/script.js"></script>
</body>
</html>