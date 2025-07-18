<?php
session_start();

$data = json_decode(file_get_contents("php://input"), true);
$enteredOtp = $data['otp'] ?? '';

if ($_SESSION['otp'] == $enteredOtp) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
