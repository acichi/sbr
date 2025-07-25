document.addEventListener("DOMContentLoaded", () => {
  const dropArea = document.getElementById("drop-area");
  const fileInput = document.getElementById("files");
  const fileList = document.getElementById("fileList");
  const uploadForm = document.getElementById("uploadForm");

  dropArea.addEventListener("click", () => fileInput.click());

  ["dragenter", "dragover"].forEach(event => {
    dropArea.addEventListener(event, e => {
      e.preventDefault();
      dropArea.classList.add("bg-info", "text-white");
    });
  });

  ["dragleave", "drop"].forEach(event => {
    dropArea.addEventListener(event, e => {
      e.preventDefault();
      dropArea.classList.remove("bg-info", "text-white");
    });
  });

  dropArea.addEventListener("drop", e => {
    fileInput.files = e.dataTransfer.files;
    displayFileList();
  });

  fileInput.addEventListener("change", displayFileList);

  function displayFileList() {
    const files = fileInput.files;
    fileList.innerHTML = "";
    Array.from(files).forEach(file => {
      const item = document.createElement("div");
      item.textContent = file.name;
      fileList.appendChild(item);
    });
  }

  uploadForm.addEventListener("submit", async e => {
    e.preventDefault();

    const formData = new FormData(uploadForm);
    for (const file of fileInput.files) {
      formData.append("files[]", file);
    }

    try {
      const response = await fetch("upload.php", {
        method: "POST",
        body: formData,
      });

      const result = await response.json();

      if (result.success) {
        Swal.fire("Success!", result.message, "success");
        uploadForm.reset();
        fileList.innerHTML = "";
      } else {
        Swal.fire("Error!", result.message, "error");
      }
    } catch (error) {
      Swal.fire("Oops!", "Something went wrong.", "error");
    }
  });
});
