<?php
require '../properties/connection.php'; // Make sure this connects to your DB

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $pin_x = $_POST['pin_x'];
    $pin_y = $_POST['pin_y'];
    $details = $_POST['details'];
    $status = $_POST['status'];
    $price = $_POST['price'];
    $date_added = date('Y-m-d H:i:s');
    $date_updated = $date_added;

    // Handle image upload
    $imagePath = '';
    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $target = "../uploads/" . $imageName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $imagePath = $imageName;
        }
    }

    $stmt = $conn->prepare("INSERT INTO facility (name, pin_x, pin_y, details, status, price, image, date_added, date_updated)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sddsssiss", $name, $pin_x, $pin_y, $details, $status, $price, $imagePath, $date_added, $date_updated);

    if ($stmt->execute()) {
        echo "<script>
            alert('Facility saved!');
            window.location.href = 'your_map_page.php';
        </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
