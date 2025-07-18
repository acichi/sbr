document.addEventListener("DOMContentLoaded", function () {
    const mapContainer = document.getElementById('map-container');
    const detailsContainer = document.getElementById("details-container");
    const closeDetailsButton = document.getElementById("closeDetails");
    const detailsTitle = document.getElementById("detailsTitle");
    const detailsText = document.getElementById("detailsText");
    const detailsPrice = document.getElementById("detailsPrice");
    const bookNowButton = document.getElementById("bookNowButton");

    let selectedMarker = null;

    function fetchMarkers() {
        fetch('map_retrieve.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    data.markers.forEach(markerData => {
                        addMarkerToMap(markerData);
                    });
                } else {
                    console.error("Error retrieving markers:", data.message);
                }
            })
            .catch(error => console.error("Fetch error:", error));
    }

    function addMarkerToMap(markerData) {
        const marker = document.createElement('div');
        marker.classList.add('marker');
        marker.style.backgroundColor = markerData.status.toLowerCase() === 'available' ? 'green' : 'red';
        marker.setAttribute('data-marker-id', markerData.id);

        function updateMarkerPosition() {
            const containerRect = mapContainer.getBoundingClientRect();
            const scaleX = containerRect.width / 1920;
            const scaleY = containerRect.height / 1080;
            const left = (markerData.x * scaleX) - (marker.offsetWidth - -5);
            const top = (markerData.y * scaleY) - (marker.offsetHeight - -6);
            marker.style.left = `${left}px`;
            marker.style.top = `${top}px`;
        }

        updateMarkerPosition();
        window.addEventListener('resize', updateMarkerPosition);

        marker.addEventListener('click', function () {
            if (selectedMarker) {
                selectedMarker.classList.remove('selected');
                selectedMarker.style.backgroundColor = selectedMarker.getAttribute('data-original-color');
            }

            selectedMarker = marker;
            marker.classList.add('selected');
            marker.setAttribute('data-original-color', marker.style.backgroundColor);
            marker.style.backgroundColor = 'blue';

            detailsTitle.textContent = markerData.name;

            if (markerData.status.toLowerCase() !== 'available') {
                detailsText.textContent = "This facility is currently unavailable.";
                detailsPrice.textContent = "Status: Unavailable";
                bookNowButton.disabled = true;
                bookNowButton.textContent = "Unavailable";
            } else {
                detailsText.textContent = markerData.details;
                detailsPrice.textContent = `Price: ₱${parseFloat(markerData.price).toFixed(2)}`;
                bookNowButton.disabled = false;
                bookNowButton.textContent = "Book Now";
            }

            bookNowButton.setAttribute("data-facility", markerData.name);
            bookNowButton.setAttribute("data-price", markerData.price);
            detailsContainer.classList.add("show");
        });

        mapContainer.appendChild(marker);
    }

    fetchMarkers();

    closeDetailsButton.addEventListener('click', function () {
        detailsContainer.classList.remove('show');
        if (selectedMarker) {
            selectedMarker.classList.remove('selected');
            selectedMarker.style.backgroundColor = selectedMarker.getAttribute('data-original-color');
            selectedMarker = null;
        }
    });
});


//for feedback
document.querySelectorAll('.show-details').forEach(button => {
    button.addEventListener('click', () => {
      const facility = button.getAttribute('data-facility');
      const details = button.getAttribute('data-details');
      const price = button.getAttribute('data-price');
  
      document.getElementById('detailsTitle').textContent = facility;
      document.getElementById('detailsText').textContent = details;
      document.getElementById('detailsPrice').textContent = 'Price: ₱' + parseFloat(price).toFixed(2);
      document.getElementById('facilityDisplay').value = facility;
      document.getElementById('facilityName').value = facility;
  
      document.getElementById('details-container').style.display = 'block';
      document.getElementById('feedback-container').style.display = 'block';
  
      // Fetch feedback dynamically (optional)
      fetch(`fetch_feedback.php?facility_name=${encodeURIComponent(facility)}`)
        .then(response => response.text())
        .then(html => {
          document.getElementById('feedback-content').innerHTML = html;
        })
        .catch(err => {
          document.getElementById('feedback-content').innerHTML = `<p class="text-danger">Failed to load feedback.</p>`;
        });
    });
  });
  