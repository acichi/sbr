<?php
session_start();
require '../properties/connection.php'; // Adjust path if needed

// Sanitize input
function sanitize($data) {
    return htmlspecialchars(trim($data));
}

$email    = sanitize($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Check if both email and password are present
if (empty($email) || empty($password)) {
    echo "<script>
        alert('❌ Please fill in all fields.');
        window.location.href = './index.php';
    </script>";
    exit;
}

// Prepare statement to get user by email
$stmt = $conn->prepare("SELECT id, fullname, email, number, username, password, gender, role, address FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Check if user exists
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Verify password hash
    if (password_verify($password, $user['password'])) {
        // Store user info in session (excluding password)
        $_SESSION['user'] = [
            'id'       => $user['id'],
            'fullname' => $user['fullname'],
            'email'    => $user['email'],
            'username' => $user['username'],
            'role'     => $user['role'],
        ];

        // Set fullname as a top-level session key for reservation use
        $_SESSION['fullname'] = $user['fullname'];

        // Redirect based on role
        if ($user['role'] === 'admin') {
            echo "<script>
                alert('✅ Welcome back, Admin!');
                window.location.href = '../admindash/admindash.php';
            </script>";
        } else {
            echo "<script>
                alert('✅ Login successful!');
                window.location.href = '../cusdash/cusdash.php';
            </script>";
        }
        exit;
    } else {
        // Invalid password
        echo "<script>
            alert('❌ Incorrect password.');
            window.location.href = './index.php';
        </script>";
    }
} else {
    // Email not found
    echo "<script>
        alert('❌ No account found with that email.');
        window.location.href = './index.php';
    </script>";
}

$stmt->close();
$conn->close();
