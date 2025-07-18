<?php
require_once("../properties/connection.php");
header('Content-Type: application/json');

if (isset($_GET['transaction_id'])) {
    $transactionId = $_GET['transaction_id'];

    $stmt = $conn->prepare("SELECT * FROM receipt WHERE transaction_id = ?");
    $stmt->bind_param("s", $transactionId);
    $stmt->execute();
    $result = $stmt->get_result();
    $receipt = $result->fetch_assoc();

    if ($receipt) {
        echo json_encode(["success" => true, "receipt" => $receipt]);
    } else {
        echo json_encode(["success" => false, "message" => "Receipt not found."]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "No transaction ID provided."]);
}
