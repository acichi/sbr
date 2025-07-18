<?php
session_start();

// === CONFIGURATION ===
$deviceId = '686e56ad633a2bfcd9e7cc09'; // Your TextBee device ID
$apiKey   = '8d25dfcd-4a88-434c-92cb-15fcdd46c933'; // Your API key
$recipient = '+639072891557'; // Use international format (TextBee requirement)

// === BASIC VALIDATION ===
if (!preg_match('/^\+63\d{10}$/', $recipient)) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid phone number format. Use +63XXXXXXXXXX.'
    ]);
    exit;
}

// === GENERATE OTP ===
$otp = rand(100000, 999999);
$_SESSION['otp'] = $otp;
$_SESSION['otp_time'] = time(); // Optional: for expiry validation

$message = "Your OTP is: $otp";

// === PREPARE REQUEST TO TEXTBEE ===
$url = "https://api.textbee.dev/api/v1/gateway/devices/$deviceId/send-sms";

$data = [
    'recipients' => [$recipient], // ✅ must be array
    'message' => $message,
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'x-api-key: ' . $apiKey,
    'Content-Type: application/json',
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

// === RETURN RESPONSE ===
header('Content-Type: application/json');

if ($httpcode === 200 && $response) {
    echo json_encode([
        'success' => true,
        'message' => 'OTP sent successfully',
        // 'otp' => $otp, // ✅ Uncomment only for development/testing
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to send OTP',
        'error' => $error ?: 'Unknown error',
        'api_response' => $response ?: 'No response from TextBee API'
    ]);
}
?>
