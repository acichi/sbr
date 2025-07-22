<?php
session_start();
require '../properties/connection.php';

// Check if OTP and registration data exist
if (!isset($_SESSION['otp'], $_SESSION['registration'])) {
    die("❌ Session expired or invalid. Please register again.");
}

$input_otp = $_POST['otp'] ?? '';
if ($input_otp != $_SESSION['otp']) {
    die("❌ Invalid OTP. Please try again.");
}

// Extract data
$data = $_SESSION['registration'];
$name     = $data['name'];
$email    = $data['email'];
$username = $data['username'];
$password = $data['password'];
$number   = $data['number'];
$address  = $data['address'];
$gender   = $data['gender'];
$role     = 'customer';
$now      = date('Y-m-d H:i:s');

// Check if username/email already exists
$check = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
$check->bind_param("ss", $email, $username);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $check->close();
    die("❌ Username or email already exists.");
}

// Insert new user
$stmt = $conn->prepare("INSERT INTO users (fullname, email, number, username, password, gender, role, address, date_added, date_updated)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssssss", $name, $email, $number, $username, $password, $gender, $role, $address, $now, $now);

if ($stmt->execute()) {
    unset($_SESSION['otp']);
    unset($_SESSION['registration']);
    echo "<script>
        alert('✅ Registration successful! Please log in.');
        window.location.href = 'login.php';
    </script>";
} else {
    echo "❌ Something went wrong during registration.";
}

$stmt->close();
$conn->close();
