<?php
// session_start();
// if (!isset($_SESSION['user'])) {
//     header("Location: ../Login/login.php");
//     exit;
// }
require '../properties/connection.php';

// $userId = $_SESSION['user']['id'];
$userId = 0; // Or set a default/test user ID

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

// My Reservations: Only latest per facility for this user
$reservee = $fullname;
$resSql = "SELECT r.*
           FROM receipt r
           INNER JOIN (
               SELECT facility_name, MAX(date_booked) AS latest_date
               FROM receipt
               WHERE reservee = ?
               GROUP BY facility_name
           ) latest
           ON r.facility_name = latest.facility_name AND r.date_booked = latest.latest_date
           WHERE r.reservee = ?
           ORDER BY r.date_booked DESC";
$resStmt = $conn->prepare($resSql);
$resStmt->bind_param("ss", $reservee, $reservee);
$resStmt->execute();
$resResult = $resStmt->get_result();
$reservations = [];
while($row = $resResult->fetch_assoc()) {
    $reservations[] = $row;
}

// Transaction History: All transactions
$transSql = "SELECT transaction_id, reservee, facility_name, date_booked, amount_paid, payment_type 
             FROM receipt 
             ORDER BY date_booked DESC";
$transResult = $conn->query($transSql);
$transactions = [];
if ($transResult) {
    while($row = $transResult->fetch_assoc()) {
        $transactions[] = $row;
    }
}

// Calculate Upcoming Reservations (reservations with future check-in dates)
$upcomingSQL = "SELECT COUNT(*) as upcoming_count 
                FROM receipt 
                WHERE reservee = ? 
                AND date_checkin > CURRENT_DATE()";
$upcomingStmt = $conn->prepare($upcomingSQL);
$upcomingStmt->bind_param("s", $reservee);
$upcomingStmt->execute();
$upcomingResult = $upcomingStmt->get_result();
$upcomingCount = $upcomingResult->fetch_assoc()['upcoming_count'];

// Calculate Total Reviews (you may need to adjust this if you have a separate reviews table)
$reviewsSQL = "SELECT COUNT(DISTINCT facility_name) as review_count 
               FROM receipt 
               WHERE reservee = ? 
               AND date_checkout < CURRENT_DATE()"; // Assuming past checkouts can be reviewed
$reviewsStmt = $conn->prepare($reviewsSQL);
$reviewsStmt->bind_param("s", $reservee);
$reviewsStmt->execute();
$reviewsResult = $reviewsStmt->get_result();
$reviewCount = $reviewsResult->fetch_assoc()['review_count'];

// Calculate Total Amount Spent
$spentSQL = "SELECT SUM(amount_paid) as total_spent 
             FROM receipt 
             WHERE reservee = ?";
$spentStmt = $conn->prepare($spentSQL);
$spentStmt->bind_param("s", $reservee);
$spentStmt->execute();
$spentResult = $spentStmt->get_result();
$totalSpent = $spentResult->fetch_assoc()['total_spent'] ?? 0;

// Close statements
$upcomingStmt->close();
$reviewsStmt->close();
$spentStmt->close();

// Feedback: All feedback given by this user
$feedbackSQL = "SELECT * FROM feedback WHERE fullname = ? ORDER BY timestamp DESC";
$feedbackStmt = $conn->prepare($feedbackSQL);
$feedbackStmt->bind_param("s", $fullname);
$feedbackStmt->execute();
$feedbackResult = $feedbackStmt->get_result();
$feedbacks = [];
while($row = $feedbackResult->fetch_assoc()) {
    $feedbacks[] = $row;
}
$feedbackStmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
  <link rel="stylesheet" href="css/style.css" />
  <!-- Bootstrap & DataTables CSS for beautification -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
  
  <style>
    .dataTables_wrapper .dataTables_length select {
      padding-right: 25px;
    }
    .table thead th {
      background-color: #0d6efd;
      color: white;
    }
    .dataTables_wrapper .dt-buttons {
      margin-bottom: 10px;
    }
    .dataTables_filter input {
      border-radius: 4px;
      padding: 4px 10px;
    }

    .collapse {
        transition: all 0.3s ease;
    }
    
    .btn-link:hover {
        opacity: 0.8;
    }
    
    .btn-link .bx-chevron-down {
        transition: transform 0.3s ease;
    }
    
    .btn-link[aria-expanded="true"] .bx-chevron-down {
        transform: rotate(180deg);
    }

    .rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
    }

    .rating input {
        display: none;
    }

    .rating label {
        cursor: pointer;
        font-size: 25px;
        color: #ddd;
        padding: 5px;
    }

    .rating input:checked ~ label,
    .rating label:hover,
    .rating label:hover ~ label {
        color: #ffc107;
    }

    .rating input:checked + label:hover,
    .rating input:checked ~ label:hover,
    .rating label:hover ~ input:checked ~ label,
    .rating input:checked ~ label:hover ~ label {
        color: #ffd700;
    }
  </style>
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
      <li>
        <i class='bx bxs-calendar-check'></i>
        <div class="text">
            <h3><?= htmlspecialchars($upcomingCount) ?></h3>
            <p>Upcoming Reservations</p>
        </div>
      </li>
      <li>
        <i class='bx bxs-star'></i>
        <div class="text">
            <h3><?= htmlspecialchars($reviewCount) ?></h3>
            <p>Reviews Sent</p>
        </div>
      </li>
      <li>
        <i class='bx bxs-dollar-circle'></i>
        <div class="text">
            <h3>₱<?= htmlspecialchars(number_format($totalSpent, 2)) ?></h3>
            <p>Total Spent</p>
        </div>
      </li>
    </ul>

    <!-- Account Details -->
    <div class="account-details">
      <h3>Account Details</h3>
      <p><strong>Name:</strong> <?= htmlspecialchars($fullname) ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
      <p><strong>Phone:</strong> <?= htmlspecialchars($number) ?></p>
      <p><strong>Member since:</strong> <?= $memberSince ?></p>
    </div>

    <!-- My Reservations Table -->
    <div class="order mb-4">
      <div class="head d-flex align-items-center justify-content-between">
        <button class="btn btn-link text-decoration-none text-dark p-0" type="button" data-bs-toggle="collapse" data-bs-target="#reservationsCollapse" aria-expanded="true" aria-controls="reservationsCollapse">
          <h3 class="mb-0">
            <i class='bx bxs-shopping-bag-alt'></i> My Reservations
            <i class='bx bx-chevron-down'></i>
          </h3>
        </button>
      </div>
      
      <div class="collapse show" id="reservationsCollapse">
        <div class="table-responsive mt-3">
          <table id="reservationTable" class="table table-striped table-bordered align-middle">
            <thead class="table-primary">
              <tr>
                <th>Transaction ID</th>
                <th>Date Booked</th>
                <th>Facility Name</th>
                <th>Status</th>
                <th>Amount Paid</th>
                <th>Payment Type</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($reservations as $row): ?>
              <tr>
                <td><?= htmlspecialchars($row['transaction_id']) ?></td>
                <td><?= htmlspecialchars($row['date_booked']) ?></td>
                <td><?= htmlspecialchars($row['facility_name']) ?></td>
                <td><span class="badge bg-success">Confirmed</span></td>
                <td>₱<?= htmlspecialchars(number_format($row['amount_paid'], 2)) ?></td>
                <td><?= htmlspecialchars($row['payment_type']) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php if (count($reservations) == 0): ?>
          <div class="alert alert-info mt-3">No reservations found.</div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Transaction History Table -->
    <div class="order mb-4">
      <div class="head d-flex align-items-center justify-content-between">
        <button class="btn btn-link text-decoration-none text-dark p-0" type="button" data-bs-toggle="collapse" data-bs-target="#transactionsCollapse" aria-expanded="false" aria-controls="transactionsCollapse">
          <h3 class="mb-0">
            <i class='bx bxs-dollar-circle'></i> Transaction History
            <i class='bx bx-chevron-down'></i>
          </h3>
        </button>
      </div>
      
      <div class="collapse" id="transactionsCollapse">
        <div class="table-responsive mt-3">
          <table id="transactionTable" class="table table-striped table-bordered align-middle">
            <thead class="table-primary">
              <tr>
                <th>Transaction ID</th>
                <th>Reservee</th>
                <th>Facility Name</th>
                <th>Date Booked</th>
                <th>Amount Paid</th>
                <th>Payment Type</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($transactions as $row): ?>
              <tr>
                <td><?= htmlspecialchars($row['transaction_id']) ?></td>
                <td><?= htmlspecialchars($row['reservee']) ?></td>
                <td><?= htmlspecialchars($row['facility_name']) ?></td>
                <td><?= htmlspecialchars($row['date_booked']) ?></td>
                <td>₱<?= htmlspecialchars(number_format($row['amount_paid'], 2)) ?></td>
                <td><?= htmlspecialchars($row['payment_type']) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Feedback Section -->
    <div class="feedback-section mb-4">
      <h3>My Feedback</h3>
      <?php if (count($feedbacks) > 0): ?>
        <div class="table-responsive">
          <table class="table table-striped table-bordered align-middle">
            <thead class="table-primary">
              <tr>
                <th>Facility Name</th>
                <th>Feedback</th>
                <th>Rating</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($feedbacks as $feedback): ?>
              <tr>
                <td><?= htmlspecialchars($feedback['facility_name']) ?></td>
                <td><?= htmlspecialchars($feedback['feedback']) ?></td>
                <td>
                    <?php for($i = 1; $i <= $feedback['rate']; $i++): ?>
                        <i class='bx bxs-star text-warning'></i>
                    <?php endfor; ?>
                </td>
                <td><?= htmlspecialchars(date('Y-m-d', strtotime($feedback['timestamp']))) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="alert alert-info">No feedback found.</div>
      <?php endif; ?>
    </div>

    <!-- Reviews Section -->
    <div class="order mb-4">
        <div class="head d-flex align-items-center justify-content-between">
            <button class="btn btn-link text-decoration-none text-dark p-0" type="button" data-bs-toggle="collapse" data-bs-target="#reviewsCollapse" aria-expanded="false" aria-controls="reviewsCollapse">
                <h3 class="mb-0">
                    <i class='bx bxs-message-dots'></i> My Reviews
                    <i class='bx bx-chevron-down'></i>
                </h3>
            </button>
        </div>
        
        <div class="collapse" id="reviewsCollapse">
            <!-- Submit Review Form -->
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title">Submit a Review</h5>
                    <form id="reviewForm" action="submit_review.php" method="POST">
                        <input type="hidden" name="fullname" value="<?= htmlspecialchars($fullname) ?>">
                        <div class="mb-3">
                            <label for="facility_name" class="form-label">Facility Name</label>
                            <select class="form-select" name="facility_name" required>
                                <option value="">Select Facility</option>
                                <?php foreach($reservations as $res): ?>
                                    <option value="<?= htmlspecialchars($res['facility_name']) ?>">
                                        <?= htmlspecialchars($res['facility_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="feedback" class="form-label">Your Review</label>
                            <textarea class="form-control" name="feedback" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rating</label>
                            <div class="rating">
                                <?php for($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" name="rate" value="<?= $i ?>" id="star<?= $i ?>" required>
                                    <label for="star<?= $i ?>"><i class='bx bxs-star'></i></label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Review</button>
                    </form>
                </div>
            </div>

            <!-- Reviews Table -->
            <div class="table-responsive mt-3">
                <table id="reviewsTable" class="table table-striped table-bordered align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>Facility Name</th>
                            <th>Review</th>
                            <th>Rating</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($feedbacks as $feedback): ?>
                        <tr>
                            <td><?= htmlspecialchars($feedback['facility_name']) ?></td>
                            <td><?= htmlspecialchars($feedback['feedback']) ?></td>
                            <td>
                                <?php for($i = 1; $i <= $feedback['rate']; $i++): ?>
                                    <i class='bx bxs-star text-warning'></i>
                                <?php endfor; ?>
                            </td>
                            <td><?= htmlspecialchars(date('M d, Y', strtotime($feedback['timestamp']))) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
  </main>
</section>

<!-- Bootstrap & DataTables JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- DataTables Enhanced JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<script>
$(document).ready(function() {
    // Shared configuration for both tables
    const commonConfig = {
        responsive: true,
        dom: '<"d-flex justify-content-between align-items-center mb-3"Bfl>rt<"d-flex justify-content-between align-items-center"ip>',
        buttons: [
            {extend: 'copy', className: 'btn btn-sm btn-primary'},
            {extend: 'excel', className: 'btn btn-sm btn-success'},
            {extend: 'pdf', className: 'btn btn-sm btn-danger'},
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search records...",
            lengthMenu: "_MENU_ records per page",
            info: "Showing _START_ to _END_ of _TOTAL_ records",
            paginate: {
                first: '<i class="bx bx-chevrons-left"></i>',
                last: '<i class="bx bx-chevrons-right"></i>',
                next: '<i class="bx bx-chevron-right"></i>',
                previous: '<i class="bx bx-chevron-left"></i>'
            }
        },
        pageLength: 5,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]]
    };

    // Initialize tables
    let reservationTable = $('#reservationTable').DataTable({
        ...commonConfig,
        order: [[1, "desc"]],
    });

    let transactionTable = $('#transactionTable').DataTable({
        ...commonConfig,
        order: [[3, "desc"]],
    });

    // Initialize reviews table
    let reviewsTable = $('#reviewsTable').DataTable({
        ...commonConfig,
        order: [[3, "desc"]],
    });

    // Redraw tables when shown
    $('#reservationsCollapse').on('shown.bs.collapse', function () {
        reservationTable.columns.adjust().responsive.recalc();
    });

    $('#transactionsCollapse').on('shown.bs.collapse', function () {
        transactionTable.columns.adjust().responsive.recalc();
    });

    // Redraw reviews table when shown
    $('#reviewsCollapse').on('shown.bs.collapse', function () {
        reviewsTable.columns.adjust().responsive.recalc();
    });

    // Handle review form submission
    $('#reviewForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: $(this).serialize(),
            success: function(response) {
                if(response.success) {
                    alert('Review submitted successfully!');
                    location.reload();
                } else {
                    alert('Error submitting review: ' + response.message);
                }
            },
            error: function() {
                alert('Error submitting review. Please try again.');
            }
        });
    });
});
</script>
</body>
</html>
<?php
$resStmt->close();
?>
