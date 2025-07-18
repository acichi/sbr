<?php
require '../properties/connection.php'; // Make sure this connects to your DB

header('Content-Type: application/json');

$query = "SELECT id, name, pin_x, pin_y, details, status, price FROM facility";
$result = $conn->query($query);

$pins = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pins[] = $row;
    }
}

echo json_encode($pins);

$conn->close();
?>
