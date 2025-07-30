<?php
require '../properties/connection.php';

$range = $_GET['range'] ?? 'monthly';

switch($range){
    case 'weekly':
        $groupBy = "WEEK(timestamp)";
        $labelFormat = "%b %d";
        break;
    case 'yearly':
        $groupBy = "YEAR(timestamp)";
        $labelFormat = "%Y";
        break;
    default: // monthly
        $groupBy = "MONTH(timestamp)";
        $labelFormat = "%b";
        break;
}

$query = $conn->query("
    SELECT DATE_FORMAT(timestamp, '$labelFormat') as label, COUNT(*) as total 
    FROM feedback 
    WHERE is_hidden = 0 OR is_hidden IS NULL
    GROUP BY $groupBy
    ORDER BY MIN(timestamp)
");

$labels = [];
$counts = [];
while($row = $query->fetch_assoc()){
    $labels[] = $row['label'];
    $counts[] = (int)$row['total'];
}

echo json_encode([
    "labels" => $labels,
    "counts" => $counts
]);
