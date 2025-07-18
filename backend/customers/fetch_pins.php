<?php
// public/fetch_pins.php
require_once '../properties/connection.php'; // or your DB connection file

header('Content-Type: application/json');

$sql = "SELECT id, name, details, status, price, pin_x, pin_y, image FROM facility";
$result = $conn->query($sql);

$pins = [];

if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $pins[] = [
      'id' => $row['id'],
      'name' => $row['name'],
      'details' => $row['details'],
      'status' => $row['status'],
      'price' => $row['price'],
      'pin_x' => $row['pin_x'],
      'pin_y' => $row['pin_y'],
      'image_path' => $row['image'] ? 'uploads/' . $row['image'] : null
    ];
  }
}

echo json_encode($pins);
?>
