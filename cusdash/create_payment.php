// create_payment.php
<?php
require '../properties/connection.php';

$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

$amount = (float)$data['amount'] * 100;
$paymentType = strtolower($data['payment_type']);

$validTypes = ['gcash', 'paymaya', 'card'];
if ($paymentType === 'visa' || $paymentType === 'mastercard') {
    $paymentType = 'card';
}

if (!in_array($paymentType, $validTypes)) {
    echo json_encode(['error' => 'Invalid payment type']);
    exit;
}

$headers = [
    'Content-Type: application/json',
    'Authorization: Basic ' . base64_encode('sk_test_FSqBor49LkwXZ7N4fsn1ubtK:')
];

$body = [
    'data' => [
        'attributes' => [
            'line_items' => [[
                'currency' => 'PHP',
                'amount' => (int)$amount,
                'description' => $data['facility_name'],
                'name' => 'Reservation for ' . $data['reservee'],
                'quantity' => 1
            ]],
            'payment_method_types' => [$paymentType],
            'redirect' => [
                'success' => 'http://localhost/sbr/success.php',
                'failed' => 'http://localhost/sbr/failed.php'
            ]
        ]
    ]
];

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'https://api.paymongo.com/v1/checkout_sessions',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode($body),
    CURLOPT_HTTPHEADER => $headers,
]);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    echo json_encode(['error' => 'Curl error: ' . $err]);
    exit;
}

$res = json_decode($response, true);

if (isset($res['data']['id']) && isset($res['data']['attributes']['checkout_url'])) {
    $stmt = $conn->prepare("INSERT INTO pending_transactions (
        transaction_id, reservee, facility_name, amount_paid,
        date_checkin, date_checkout, date_booked
    ) VALUES (?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("sssdsss",
        $res['data']['id'],
        $data['reservee'],
        $data['facility_name'],
        $data['amount'],
        $data['date_start'],
        $data['date_end'],
        $data['date_booked']
    );
    $stmt->execute();
    $stmt->close();

    echo json_encode([
        'checkout_url' => $res['data']['attributes']['checkout_url'],
        'transaction_id' => $res['data']['id']
    ]);
} else {
    echo json_encode(['error' => 'Invalid PayMongo response']);
}

$conn->close();
