 $(document).ready(function () {
    $('#exampleTable').DataTable({
      ajax: 'map_table.php',
      columns: [
        { title: "#" },
        { title: "Name" },
        { title: "Details" },
        { title: "Status" },
        { title: "Price" },
        { title: "Action", orderable: false }
      ],
      pageLength: 10
    });
  });