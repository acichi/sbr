<?php
require '../properties/connection.php';
header('Content-Type: application/json');

$sql = "
SELECT 
    f.id,
    f.name,
    f.pin_x,
    f.pin_y,
    f.details,
    f.status,
    f.price,
    f.image,
    MAX(r.end_datetime) AS latest_reserved_until
FROM facility f
LEFT JOIN receipt r ON f.id = r.facility_id
GROUP BY f.id
";

$result = $conn->query($sql);
$facilities = [];

if ($result && $result->num_rows > 0) {
    date_default_timezone_set('Asia/Manila'); // Set timezone for accurate date compare
    $now = date('Y-m-d H:i:s');

    while ($row = $result->fetch_assoc()) {
        $latest = $row['latest_reserved_until'];

        // Determine booking availability based on current time vs latest reserved
        if ($latest && $now < $latest) {
            $row['dynamic_status'] = 'Unavailable until ' . date('M d, Y H:i', strtotime($latest));
            $row['available_until'] = null;
        } else {
            $row['dynamic_status'] = 'Available';
            $row['available_until'] = $latest ? date('M d, Y H:i', strtotime($latest)) : null;
        }

        $facilities[] = $row;
    }
}

echo json_encode($facilities);
$conn->close();
