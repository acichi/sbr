// Minimal version of retrieve_pin.js for customers
$(document).ready(function () {
  $.ajax({
    url: 'fetch_pins.php',
    method: 'GET',
    dataType: 'json',
    success: function (pins) {
      pins.forEach(pin => {
        const marker = document.createElementNS("http://www.w3.org/2000/svg", "circle");
        marker.setAttribute("cx", pin.x);
        marker.setAttribute("cy", pin.y);
        marker.setAttribute("r", 8);
        marker.setAttribute("fill", pin.status === "Available" ? "green" : "red");
        marker.style.cursor = "pointer";
        marker.addEventListener("click", () => {
          $('#modalFacilityName').text(pin.name);
          $('#modalFacilityDetails').text(pin.details);
          $('#modalFacilityStatus').text(pin.status);
          $('#modalFacilityPrice').text(pin.price);
          $('#modalFacilityImage').attr('src', pin.image_path || 'placeholder.jpg');
          $('#bookNowBtn').prop('disabled', pin.status !== "Available");
          $('#facilityDetailsModal').modal('show');
        });
        document.getElementById("map").appendChild(marker);
      });
    }
  });
});
