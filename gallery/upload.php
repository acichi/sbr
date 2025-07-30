<?php
require "../properties/connection.php";

$response = ["success" => false, "message" => ""];

$uploadDir = "uploads/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$description = $_POST['description'] ?? '';
$date_now = date("Y-m-d H:i:s");

if (empty($_FILES['files']['name'][0])) {
    $response["message"] = "No files uploaded.";
    echo json_encode($response);
    exit;
}

$files = $_FILES['files'];

for ($i = 0; $i < count($files['name']); $i++) {
    $filename = basename($files['name'][$i]);
    $targetPath = $uploadDir . time() . "_" . $filename;
    $relativePath = "uploads/" . time() . "_" . $filename;

    if (move_uploaded_file($files['tmp_name'][$i], $targetPath)) {
        $stmt = $conn->prepare("INSERT INTO gallery (description, location, date_added, date_updated) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $description, $relativePath, $date_now, $date_now);
        $stmt->execute();
    } else {
        $response["message"] = "Failed to upload file: " . $filename;
        echo json_encode($response);
        exit;
    }
}

$response["success"] = true;
$response["message"] = "Files uploaded successfully!";
echo json_encode($response);
