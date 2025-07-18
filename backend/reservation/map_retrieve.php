<?php
require("../properties/connection.php");

header('Content-Type: application/json; charset=utf-8');

$response = ['markers' => []];

$statusFilter = isset($_GET['status']) ? $_GET['status'] : null;

try {
    if ($statusFilter) {
        $sql = "SELECT id, name, ST_X(location) AS x, ST_Y(location) AS y, details, status, price FROM facility WHERE status = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $statusFilter);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $sql = "SELECT id, name, ST_X(location) AS x, ST_Y(location) AS y, details, status, price FROM facility";
        $result = $conn->query($sql);
    }

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $response['markers'][] = $row;
        }
        $response['status'] = 'success';
    } else {
        $response['status'] = 'success';
        $response['message'] = 'No markers found';
    }

} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = 'Query failed: ' . $e->getMessage();
}

$conn->close();

echo json_encode($response);
?>
