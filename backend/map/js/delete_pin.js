$(document).on('click', '.delete-btn', function () {
  const id = $(this).data('id');
  const row = $(this).closest('tr');

  Swal.fire({
    title: 'Are you sure?',
    text: "This will permanently delete the facility.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Yes, delete it!'
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: 'map_delete.php',
        type: 'POST',
        data: { id: id },
        dataType: 'json',
        success: function (response) {
          if (response.status === 'success') {
            $('#exampleTable').DataTable().ajax.reload();
            Swal.fire('Deleted!', 'The facility has been deleted.', 'success');
            location.reload();
          } else {
            Swal.fire('Error!', response.message || 'Could not delete.', 'error');
          }
        },
        error: function () {
          Swal.fire('Error!', 'Failed to send request.', 'error');
        }
      });
    }
  });
});
