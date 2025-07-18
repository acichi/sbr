<?php
require_once("../properties/connection.php");

$facility = isset($_GET['facility_name']) ? $conn->real_escape_string($_GET['facility_name']) : '';

if ($facility) {
  $sql = "SELECT fullname, feedback, rate, timestamp FROM feedback WHERE facility_name = '$facility' ORDER BY timestamp DESC";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      echo '<div class="mb-3">';
      echo '<h6 class="mb-1">' . htmlspecialchars($row['fullname']) . ' <small class="text-muted">(' . $row['rate'] . '/5)</small></h6>';
      echo '<p class="mb-1">' . htmlspecialchars($row['feedback']) . '</p>';
      echo '<small class="text-muted">' . date("M d, Y H:i", strtotime($row['timestamp'])) . '</small>';
      echo '</div><hr>';
    }
  } else {
    echo "<p>No feedback yet.</p>";
  }
} else {
  echo "<p>Invalid facility.</p>";
}
$conn->close();
?>
