// main.js
document.addEventListener('DOMContentLoaded', function () {
    fetch('map_retrieve.php')
        .then(response => response.json())
        .then(data => {
            const svg = document.getElementById('map');
            data.forEach(pin => {
                const pinElement = document.createElementNS("http://www.w3.org/2000/svg", "circle");
                pinElement.setAttribute("cx", pin.pin_x);
                pinElement.setAttribute("cy", pin.pin_y);
                pinElement.setAttribute("r", 10);
                pinElement.setAttribute("fill", "red");
                pinElement.setAttribute("class", "pin");
                pinElement.setAttribute("data-id", pin.id);
                pinElement.dataset.pinInfo = JSON.stringify(pin); // for use in edit_pin.js

                pinElement.addEventListener('click', function () {
                    showPinDetails(pin, pinElement);
                });

                svg.appendChild(pinElement);
            });
        })
        .catch(error => console.error('Error fetching pins:', error));
});

function showPinDetails(pin, pinElement) {
    const confirmEdit = confirm(
        `Pin Details:\nName: ${pin.name}\nDetails: ${pin.details}\nStatus: ${pin.status}\nPrice: ${pin.price}\n\nDo you want to edit this pin position?`
    );
    if (confirmEdit) {
        enablePinDrag(pinElement, pin.id); // Delegates to edit_pin.js
    }
}
