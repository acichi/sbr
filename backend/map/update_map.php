<?php
require ('../properties/connection.php');

$data = json_decode(file_get_contents("php://input"), true);
$id = intval($data['id']);
$pin_x = floatval($data['pin_x']);
$pin_y = floatval($data['pin_y']);

$stmt = $conn->prepare("UPDATE facility SET pin_x = ?, pin_y = ? WHERE id = ?");
$stmt->bind_param("ddi", $pin_x, $pin_y, $id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
