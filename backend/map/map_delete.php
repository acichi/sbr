<?php
require("../properties/connection.php"); // your DB connection

if (isset($_POST['id'])) {
  $id = intval($_POST['id']);
  $stmt = $conn->prepare("DELETE FROM facility WHERE id = ?");
  $stmt->bind_param("i", $id);

  if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
  } else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
  }

  $stmt->close();
} else {
  echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
}

$conn->close();
?>
