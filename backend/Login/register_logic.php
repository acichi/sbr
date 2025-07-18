<?php
// register_logic.php

header('Content-Type: application/json'); // Force JSON output

$response = ['status' => 'error', 'errors' => []];

require("../properties/connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and trim form data
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $username = trim($_POST['user']);
    $password = trim($_POST['password']);
    $mobile   = trim($_POST['mobile']);
    $address  = trim($_POST['address']);
    $gender   = trim($_POST['gender']);
    $role     = 'client';

    // === VALIDATIONS ===
    if (empty($name)) {
        $response['errors']['name'] = 'Full Name is required.';
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['errors']['email'] = 'A valid email is required.';
    }

    if (empty($username)) {
        $response['errors']['user'] = 'Username is required.';
    } elseif (strlen($username) < 3 || strlen($username) > 20) {
        $response['errors']['user'] = 'Username must be between 3 to 20 characters.';
    } elseif (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
        $response['errors']['user'] = 'Username can only contain letters, numbers, and underscores.';
    }

    if (empty($password)) {
        $response['errors']['password'] = 'Password is required.';
    } elseif (strlen($password) < 8 || strlen($password) > 16) {
        $response['errors']['password'] = 'Password must be between 8 to 16 characters.';
    }

    if (empty($mobile) || strlen($mobile) !== 11 || !preg_match("/^[0-9]+$/", $mobile)) {
        $response['errors']['mobile'] = 'Mobile number must be exactly 11 digits.';
    }

    if (empty($address)) {
        $response['errors']['address'] = 'Address is required.';
    }

    if (empty($gender)) {
        $response['errors']['gender'] = 'Gender is required.';
    } elseif (!in_array($gender, ['Male', 'Female', 'Other'])) {
        $response['errors']['gender'] = 'Invalid gender selected.';
    }

    // Stop and return validation errors
    if (!empty($response['errors'])) {
        echo json_encode($response);
        exit;
    }

    // === CHECK FOR DUPLICATES ===
    // Prepare query to check for existing email or mobile number
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR number = ?");
    $stmt->bind_param("ss", $email, $mobile);
    $stmt->execute();
    $stmt->store_result();

    // Check if the email or mobile already exists
    if ($stmt->num_rows > 0) {
        $response['errors']['general'] = 'Email or mobile number already exists.';
        echo json_encode($response);
        exit;
    }

    $stmt->close();

    // === HASH AND INSERT ===
    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new user into the database
    $stmt = $conn->prepare("INSERT INTO users (fullname, email, number, username, password, address, gender, role, date_added, date_updated) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
    $stmt->bind_param("ssssssss", $name, $email, $mobile, $username, $hashedPassword, $address, $gender, $role);

    if ($stmt->execute()) {
        // Success: User is registered
        $response['status'] = 'success';
        $response['message'] = 'Registration successful! You can now log in.';
    } else {
        // Failure: Something went wrong with the registration
        $response['errors']['general'] = 'Something went wrong during registration.';
    }

    echo json_encode($response);
    $stmt->close();
    $conn->close();
    exit;
}
?>
