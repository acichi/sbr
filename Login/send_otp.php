<?php
function sendSMS($recipients, $message) {
    $deviceId = '686e56ad633a2bfcd9e7cc09';  // your device ID
    $apiKey   = '8d25dfcd-4a88-434c-92cb-15fcdd46c933';  // your API key

    $url = "https://api.textbee.dev/api/v1/gateway/devices/$deviceId/send-sms";

    $data = [
        'recipients' => $recipients,
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

    return [
        'status_code' => $httpcode,
        'response' => $response,
        'error' => $error
    ];
}
