<?php
// paymongo_success.php
require '../properties/connection.php';

$transaction_id = $_GET['checkout_session_id'] ?? uniqid(); // from PayMongo redirect
$reservee = $_GET['reservee'] ?? 'Unknown';
$facility_name = $_GET['facility_name'] ?? 'N/A';
$amount_paid = $_GET['amount'] ?? 0;
$payment_type = $_GET['payment_type'] ?? 'GCash'; // Or grab from PayMongo API
$date_booked = date("Y-m-d H:i:s");

// Insert to DB
$stmt = $conn->prepare("INSERT INTO receipt 
  (transaction_id, reservee, facility_name, amount_paid, balance, date_booked, payment_type) 
  VALUES (?, ?, ?, ?, 0, ?, ?)");
$stmt->bind_param("sssiss", $transaction_id, $reservee, $facility_name, $amount_paid, $date_booked, $payment_type);
$stmt->execute();
$stmt->close();

// Confirmation
echo "<script>
  alert('Payment successful! Reservation saved.');
  window.location.href = 'your-reservation-confirmation-page.php';
</script>";
