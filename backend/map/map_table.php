<?php
require ("../properties/connection.php"); // use your own DB connection file

$sql = "SELECT id, name, details, status, price FROM facility";
$result = $conn->query($sql);

$data = [];

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $data[] = [
      $row['id'],
      htmlspecialchars($row['name']),
      htmlspecialchars($row['details']),
      htmlspecialchars($row['status']),
      number_format($row['price'], 2),
      '<button class="btn btn-danger btn-sm delete-btn" data-id="' . $row['id'] . '">Delete</button>'
    ];
  }
}

echo json_encode(['data' => $data]);

$conn->close();
?>
