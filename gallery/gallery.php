<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Gallery Upload</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">

<div class="container mt-5">
  <h3 class="mb-4">Upload Files to Gallery</h3>

  <form id="uploadForm" enctype="multipart/form-data">
    <div class="mb-3">
      <label for="description" class="form-label">Description</label>
      <textarea class="form-control" id="description" name="description" required></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Drag & Drop Files Below or Click to Browse</label>
      <div id="drop-area" class="border border-2 border-primary rounded p-4 text-center bg-white" style="cursor:pointer">
        <p>Drop files here or click to browse</p>
        <input type="file" id="files" name="files[]" multiple hidden>
      </div>
      <div id="fileList" class="mt-3"></div>
    </div>

    <button type="submit" class="btn btn-success">Upload</button>
  </form>
</div>

<script src="js/upload_script.js"></script>
</body>
</html>
