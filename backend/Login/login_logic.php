<?php

header('Content-Type: application/json'); // Always respond with JSON

require("../properties/connection.php");

$response = ['status' => 'error', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $response['message'] = 'Email and password are required.';
        echo json_encode($response);
        exit;
    }

    // Fetch user by email
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify hashed password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['fullname']  = $user['fullname'];
            $_SESSION['email']     = $user['email'];
            $_SESSION['role']      = $user['role'];

            $response['status'] = 'success';
            $response['message'] = 'Login successful.';
            $response['role'] = $user['role']; // Include the role in the response
        } else {
            $response['message'] = 'Incorrect password.';
        }
    } else {
        $response['message'] = 'No account found with that email.';
    }

    echo json_encode($response);
    exit;
}
?>
