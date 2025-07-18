<?php
require_once("../properties/connection.php");
include("../properties/code_generator.php");

ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

try {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Sanitize and extract inputs
        $reservee = trim($_POST['name']);
        $email = trim($_POST['email']);
        $facilityName = trim($_POST['facilityName']);
        $dateStart = $_POST['startDate'];
        $dateEnd = $_POST['endDate'];
        $amountDue = floatval(str_replace(',', '', $_POST['price']));
        $paymentType = isset($_POST['paymentMethod']) ? trim($_POST['paymentMethod']) : 'cash';
        $cashAmount = isset($_POST['cashAmount']) ? floatval($_POST['cashAmount']) : 0.0;

        $status = 'pending';
        if (strtolower($paymentType) === 'cash' && $cashAmount >= $amountDue) {
            $status = 'reserved';
        }

        $dateBooked = date("Y-m-d");
        $timestamp = date("Y-m-d H:i:s");
        $transactionId = generateTransactionId();

        // Validate facility exists
        $stmt = $conn->prepare("SELECT name FROM facility WHERE name = ?");
        $stmt->bind_param("s", $facilityName);
        $stmt->execute();
        $result = $stmt->get_result();
        $facility = $result->fetch_assoc();

        if (!$facility) {
            throw new Exception("Facility not found.");
        }

        // Save to reservations
// Determine amount paid and balance
$amountPaid = $cashAmount;
$balance = ($cashAmount >= $amountDue) ? 0 : ($amountDue - $cashAmount);

// Save to reservations (FIXED: store balance, not total amount)
$insert = $conn->prepare("INSERT INTO reservations 
    (reservee, facility_name, status, date_booked, date_start, date_end, payment_type, amount) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$insert->bind_param("sssssssd", 
    $reservee, $facilityName, $status, $dateBooked, $dateStart, $dateEnd, $paymentType, $balance
);


        if (!$insert->execute()) {
            throw new Exception("Reservation save failed: " . $insert->error);
        }

        // Update facility to unavailable
        $updateStatus = $conn->prepare("UPDATE facility SET status = 'unavailable' WHERE name = ?");
        $updateStatus->bind_param("s", $facilityName);
        if (!$updateStatus->execute()) {
            throw new Exception("Failed to update facility status: " . $updateStatus->error);
        }

        // Determine amount paid and balance
        $amountPaid = $cashAmount;
        $balance = ($cashAmount >= $amountDue) ? 0 : ($amountDue - $cashAmount);

        // Insert into receipt table with balance
        $receipt = $conn->prepare("INSERT INTO receipt 
            (transaction_id, reservee, facility_name, amount_paid, date_checkin, date_checkout, timestamp, date_booked, payment_type, balance) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $receipt->bind_param("sssddssssd", 
            $transactionId, $reservee, $facilityName, $amountPaid, $dateStart, $dateEnd, $timestamp, $dateBooked, $paymentType, $balance
        );

        if (!$receipt->execute()) {
            throw new Exception("Failed to save receipt: " . $receipt->error);
        }

        // Retrieve saved receipt for frontend
        $fetchReceipt = $conn->prepare("SELECT * FROM receipt WHERE transaction_id = ?");
        $fetchReceipt->bind_param("s", $transactionId);
        $fetchReceipt->execute();
        $receiptResult = $fetchReceipt->get_result();

        if (!$receiptResult || $receiptResult->num_rows === 0) {
            throw new Exception("Receipt was saved but could not be fetched.");
        }

        $receiptData = $receiptResult->fetch_assoc();

        echo json_encode([
            "success" => true,
            "message" => $status === 'reserved'
                ? "Reservation successful and fully paid."
                : "Reservation submitted with pending balance.",
            "receipt" => $receiptData
        ]);
    } else {
        throw new Exception("Invalid request.");
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}
