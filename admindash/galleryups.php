<?php
include '../properties/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $uploadDir = '/uploads/';
    $filename = basename($_FILES['image']['name']);
    $targetFile = $uploadDir . $filename;
    $caption = mysqli_real_escape_string($conn, $_POST['caption']);

    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($_FILES['image']['type'], $allowedTypes)) {
        echo "Invalid image type.";
        exit;
    }

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        $sql = "INSERT INTO gallery_images (filename, caption) VALUES ('$filename', '$caption')";
        if (mysqli_query($conn, $sql)) {
            echo "Image uploaded and saved to database.";
        } else {
            echo "Database error: " . mysqli_error($conn);
        }
    } else {
        echo "Upload failed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Upload Gallery Image</title>
  <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>
<body style="padding: 50px; font-family: 'Playfair Display', serif; background: #f9f9f9">
  <div class="container">
    <h2 class="mb-4">Admin Upload (Gallery)</h2>
    <form action="admin-upload.php" method="POST" enctype="multipart/form-data">
      <div class="form-group">
        <label>Image File</label>
        <input type="file" name="image" class="form-control" required>
      </div>
      <div class="form-group">
        <label>Caption (optional)</label>
        <input type="text" name="caption" class="form-control">
      </div>
      <button type="submit" class="btn btn-primary">Upload</button>
    </form>
  </div>
</body>
</html>
