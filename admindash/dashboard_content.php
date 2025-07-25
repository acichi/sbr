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
   FETCH DATABASE COUNTS
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
   FETCH ALL RESERVATIONS
   ======================= 
   Join with users to get email
*/
$allReservations = $conn->query("
    SELECT r.reservee, u.email, r.facility_name, r.date_booked, r.date_start, r.date_end, 
           r.payment_type, r.amount, r.status
    FROM reservations r
    LEFT JOIN users u ON u.username = r.reservee
    ORDER BY r.date_booked DESC
");

/* =======================
   FETCH ALL ACCOUNTS
   ======================= */
$allAccounts = $conn->query("
    SELECT username, fullname, email, role, created_at
    FROM users
    ORDER BY created_at DESC
");

?>

<!-- DASHBOARD HEADER -->
<div class="head-title">
  <div class="left">
    <h1>Dashboard</h1>
    <ul class="breadcrumb">
      <li>
        <a class="active" href="#">
          <?php echo "Welcome back, $username!"; ?>
        </a>
      </li>
    </ul>
  </div>
  <a href="download_report.php" class="btn-download">
    <i class='bx bxs-cloud-download'></i>
    <span class="text">Download PDF</span>
  </a>
</div>

<!-- STAT CARDS -->
<ul class="box-info">
  <li>
    <i class='bx bxs-calendar-check'></i>
    <span class="text">
      <h3 id="reservationsCount"><?php echo $totalReservations; ?></h3>
      <p>Total Reservations</p>
    </span>
  </li>
  <li>
    <i class='bx bxs-group'></i>
    <span class="text">
      <h3 id="guestsCount"><?php echo $totalGuests; ?></h3>
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

<!-- TOP ROW: CHART + GALLERY UPLOAD -->
<div class="table-data" style="display:flex; gap:24px; margin-bottom:24px; flex-wrap: wrap;">

  <!-- LEFT: Sales Chart -->
  <div class="todo" style="flex: 1; min-width: 300px;">
    <div id=reports class="head">
      <h3>Sales Overview</h3>
      <select id="timeRangeSelect" style="margin-left:auto; padding:5px 10px; border-radius:4px; border:1px solid #ccc; font-weight:600;">
        <option value="weekly">Weekly</option>
        <option value="monthly" selected>Monthly</option>
        <option value="yearly">Yearly</option>
      </select>
    </div>
    <div id="totalSales" style="font-size:1.2rem; font-weight:700; margin:10px 0; color:#4bc0c0;">
      Total Sales: $<?php echo number_format($totalSales,2); ?>
    </div>
    <canvas id="salesChart" width="400" height="300"></canvas>
  </div>

 <!-- RIGHT: Styled Gallery Upload Form -->
<div class="upload-box">
  <h3>Upload to Gallery</h3>

  <form id="uploadForm" enctype="multipart/form-data">
    <div class="form-group">
      <label for="description">Description</label>
      <textarea id="description" name="description" required></textarea>
    </div>

    <div class="form-group">
      <label>Upload Image(s)</label>
      <div id="drop-area">
        <p>Drag & drop files here or click to browse</p>
        <input type="file" id="files" name="files[]" multiple hidden>
      </div>
      <div id="fileList"></div>
    </div>

    <button type="submit" class="upload-btn">Upload</button>
  </form>
</div>

<!-- BOTTOM: ALL RESERVATIONS TABLE -->
<div class="table-data" id="Reservations">
  <div class="order" style="width: 100%;">
    <div class="head">
      <h3>All Reservations</h3>
      <input type="text" id="searchInput" placeholder="Search user..." style="padding:5px 10px; border-radius:4px; border:1px solid #ccc;">
    </div>
    <table id="reservationsTable">
      <thead>
        <tr>
          <th>Reservee</th>
          <th>Email</th>
          <th>Facility</th>
          <th>Date Booked</th>
          <th>Start Date</th>
          <th>End Date</th>
          <th>Payment</th>
          <th>Amount</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $allReservations->fetch_assoc()) { ?>
          <tr>
            <td><?php echo htmlspecialchars($row['reservee']); ?></td>
            <td><?php echo htmlspecialchars($row['email'] ?? 'N/A'); ?></td>
            <td><?php echo htmlspecialchars($row['facility_name']); ?></td>
            <td><?php echo date('m-d-Y', strtotime($row['date_booked'])); ?></td>
            <td><?php echo date('m-d-Y H:i', strtotime($row['date_start'])); ?></td>
            <td><?php echo date('m-d-Y H:i', strtotime($row['date_end'])); ?></td>
            <td><?php echo htmlspecialchars($row['payment_type']); ?></td>
            <td>$<?php echo number_format($row['amount'], 2); ?></td>
            <td>
              <span class="status <?php echo strtolower($row['status']); ?>">
                <?php echo ucfirst($row['status']); ?>
              </span>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>



<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Animate counts
document.querySelectorAll('.box-info h3').forEach(el => {
  let target = parseFloat(el.textContent.replace(/[^0-9.]/g, ''));
  let start = 0;
  let increment = target / 50;
  let isMoney = el.textContent.includes('$');
  let update = setInterval(() => {
    start += increment;
    if (start >= target) {
      start = target;
      clearInterval(update);
    }
    el.textContent = isMoney ? `$${start.toFixed(0)}` : Math.floor(start);
  }, 30);
});

// Search filter
document.getElementById('searchInput').addEventListener('keyup', function() {
  let filter = this.value.toLowerCase();
  document.querySelectorAll('#reservationsTable tbody tr').forEach(row => {
    let user = row.cells[0].innerText.toLowerCase();
    row.style.display = user.includes(filter) ? '' : 'none';
  });
});

// Chart.js Sales Chart
const ctx = document.getElementById('salesChart').getContext('2d');
let salesChart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: ['Jan','Feb','Mar','Apr','May','Jun'],
    datasets: [{
      label: 'Sales',
      data: [500,1000,750,1300,900,1250], // Placeholder
      backgroundColor: 'rgba(75,192,192,0.2)',
      borderColor: '#4bc0c0',
      borderWidth: 2,
      fill: true,
      tension: 0.3,
      pointBackgroundColor: '#4bc0c0'
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: { y: { beginAtZero: true } }
  }
});

// Dropdown Update
document.getElementById('timeRangeSelect').addEventListener('change', function(){
  let data;
  if(this.value === 'weekly') data = [150,200,250,220,300,280];
  if(this.value === 'monthly') data = [500,1000,750,1300,900,1250];
  if(this.value === 'yearly') data = [4000,4200,4100,5000,4500,4800];
  salesChart.data.datasets[0].data = data;
  salesChart.update();
});

// Gallery Upload JS
document.addEventListener("DOMContentLoaded", () => {
  const dropArea = document.getElementById("drop-area");
  const fileInput = document.getElementById("files");
  const fileList = document.getElementById("fileList");
  const uploadForm = document.getElementById("uploadForm");

  dropArea.addEventListener("click", () => fileInput.click());

  ["dragenter", "dragover"].forEach(event => {
    dropArea.addEventListener(event, e => {
      e.preventDefault();
      dropArea.classList.add("bg-info", "text-white");
    });
  });

  ["dragleave", "drop"].forEach(event => {
    dropArea.addEventListener(event, e => {
      e.preventDefault();
      dropArea.classList.remove("bg-info", "text-white");
    });
  });

  dropArea.addEventListener("drop", e => {
    fileInput.files = e.dataTransfer.files;
    displayFileList();
  });

  fileInput.addEventListener("change", displayFileList);

  function displayFileList() {
    const files = fileInput.files;
    fileList.innerHTML = "";
    Array.from(files).forEach(file => {
      const item = document.createElement("div");
      item.textContent = file.name;
      fileList.appendChild(item);
    });
  }

  uploadForm.addEventListener("submit", async e => {
    e.preventDefault();

    const formData = new FormData(uploadForm);
    for (const file of fileInput.files) {
      formData.append("files[]", file);
    }

    try {
      const response = await fetch("upload.php", {
        method: "POST",
        body: formData,
      });

      const result = await response.json();

      if (result.success) {
        Swal.fire("Success!", result.message, "success");
        uploadForm.reset();
        fileList.innerHTML = "";
      } else {
        Swal.fire("Error!", result.message, "error");
      }
    } catch (error) {
      Swal.fire("Oops!", "Something went wrong.", "error");
    }
  });
});
</script>
