// webhook.php
<?php
require '../properties/connection.php';

$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Optional: Add webhook signature verification here for security

if (!isset($data['data']['attributes']['status'])) {
    http_response_code(400);
    exit('Invalid webhook data');
}

$status = $data['data']['attributes']['status'];

if ($status === 'paid') {
    $transactionId = $data['data']['id'];
    $lineItems = $data['data']['attributes']['line_items'][0];
    $description = $lineItems['description'];
    $name = $lineItems['name'];
    $amountPaid = $lineItems['amount'] / 100;
    $reservee = str_replace('Reservation for ', '', $name);

    $paymentType = $data['data']['attributes']['payments'][0]['attributes']['source']['type'] ?? 'Unknown';
    $timestamp = date('Y-m-d H:i:s');

    $query = $conn->prepare("SELECT date_checkin, date_checkout, date_booked FROM pending_transactions WHERE transaction_id = ?");
    $query->bind_param("s", $transactionId);
    $query->execute();
    $query->bind_result($dateCheckin, $dateCheckout, $dateBooked);

    if ($query->fetch()) {
        $query->close();

        $stmt = $conn->prepare("INSERT INTO receipt (
            transaction_id, reservee, facility_name, amount_paid, balance,
            date_checkin, date_checkout, timestamp, date_booked, payment_type
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $balance = 0;
        $stmt->bind_param("ssssdsssss",
            $transactionId,
            $reservee,
            $description,
            $amountPaid,
            $balance,
            $dateCheckin,
            $dateCheckout,
            $timestamp,
            $dateBooked,
            ucfirst($paymentType)
        );

        if ($stmt->execute()) {
            $update = $conn->prepare("UPDATE facility SET status = 'Unavailable' WHERE name = ?");
            $update->bind_param("s", $description);
            $update->execute();
            $update->close();
        }

        $stmt->close();

        $delete = $conn->prepare("DELETE FROM pending_transactions WHERE transaction_id = ?");
        $delete->bind_param("s", $transactionId);
        $delete->execute();
        $delete->close();

        http_response_code(200);
        echo 'Webhook handled';
    } else {
        http_response_code(404);
        echo 'Transaction not found';
    }

    $conn->close();
} else {
    http_response_code(200);
    echo 'No action needed';
}
