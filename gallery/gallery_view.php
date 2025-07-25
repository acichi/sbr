<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Gallery Viewer</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .gallery-card img {
      object-fit: cover;
      height: 200px;
    }
    .pagination {
      justify-content: center;
    }
  </style>
</head>
<body class="bg-light">

<div class="container py-5">
  <h2 class="mb-4 text-center">📸 Public Gallery</h2>
  <div id="gallery" class="row g-4"></div>
  <nav>
    <ul class="pagination mt-4" id="pagination"></ul>
  </nav>
</div>

<script src="js/gallery_view.js"></script>
</body>
</html>
