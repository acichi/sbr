<?php
require "../properties/connection.php";

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 6;
$offset = ($page - 1) * $limit;

$totalQuery = $conn->query("SELECT COUNT(*) AS total FROM gallery");
$totalRow = $totalQuery->fetch_assoc();
$totalItems = $totalRow['total'];
$totalPages = ceil($totalItems / $limit);

$query = $conn->prepare("SELECT * FROM gallery ORDER BY date_added DESC LIMIT ?, ?");
$query->bind_param("ii", $offset, $limit);
$query->execute();
$result = $query->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
  $items[] = [
    "id" => $row['id'],
    "description" => htmlspecialchars($row['description']),
    "location" => htmlspecialchars($row['location']),
    "date_added" => date("M d, Y", strtotime($row['date_added']))
  ];
}

echo json_encode([
  "items" => $items,
  "totalPages" => $totalPages,
  "currentPage" => $page
]);
