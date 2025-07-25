<?php
session_start();
require '../properties/connection.php';

// Ensure user is logged in and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login/login.php");
    exit;
}

// Safely extract username or fallback
$username = isset($_SESSION['user']['username']) ? htmlspecialchars($_SESSION['user']['username']) : 'Admin';
?>

      <div class="head-title">
        <div class="left">
          <h1>Dashboard</h1>
          <ul class="breadcrumb">
            <li><a class="active" href="#">
                <?php echo "Welcome back, $username!"; ?>
              </a></li>
          </ul>
        </div>
        <a href="#" class="btn-download">
          <i class='bx bxs-cloud-download'></i>
          <span class="text">Download PDF</span>
        </a>
      </div>

      <ul class="box-info">
        <li>
          <i class='bx bxs-calendar-check'></i>
          <span class="text">
            <h3>1020</h3>
            <p>Total Reservations</p>
          </span>
        </li>
        <li>
          <i class='bx bxs-group'></i>
          <span class="text">
            <h3>2834</h3>
            <p>Total Guests</p>
          </span>
        </li>
        <li>
          <i class='bx bxs-dollar-circle'></i>
          <span class="text">
            <h3>$2543</h3>
            <p>Total Sales</p>
          </span>
        </li>
      </ul>

      <div class="table-data" id="Reservations">
        <div class="order">
          <div class="head">
            <h3>Recent Reservations</h3>
            <i class='bx bx-search'></i>
            <i class='bx bx-filter'></i>
          </div>
          <table>
            <thead>
              <tr>
                <th>User</th>
                <th>Date Set</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><p>John Doe</p></td>
                <td>01-10-2021</td>
                <td><span class="status completed">Completed</span></td>
              </tr>
              <tr>
                <td><p>John Doe</p></td>
                <td>01-10-2021</td>
                <td><span class="status pending">Pending</span></td>
              </tr>
              <tr>
                <td><p>John Doe</p></td>
                <td>01-10-2021</td>
                <td><span class="status process">Process</span></td>
              </tr>
              <tr>
                <td><p>John Doe</p></td>
                <td>01-10-2021</td>
                <td><span class="status pending">Pending</span></td>
              </tr>
              <tr>
                <td><p>John Doe</p></td>
                <td>01-10-2021</td>
                <td><span class="status completed">Completed</span></td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Sales Graph Section -->
        <div class="todo">
          <div class="head" style="align-items: center;">
            <h3>Sales Overview</h3>
            <select id="timeRangeSelect" style="margin-left:auto; padding: 5px 10px; border-radius:4px; border: 1px solid #ccc; font-weight: 600;">
              <option value="weekly">Weekly</option>
              <option value="monthly" selected>Monthly</option>
              <option value="yearly">Yearly</option>
            </select>
          </div>
          <div id="totalSales" style="font-size: 1.2rem; font-weight: 700; margin: 10px 0; color: #4bc0c0;">
            Total Sales: $2,543
          </div>
          <canvas id="salesChart" width="400" height="300"></canvas>
        </div>
      </div>