function enablePinDrag(pinElement, pinId) {
    let offsetX, offsetY;

    const svg = document.getElementById("map");

    function onMouseDown(e) {
        e.preventDefault();

        const pt = svg.createSVGPoint();
        pt.x = e.clientX;
        pt.y = e.clientY;
        const svgP = pt.matrixTransform(svg.getScreenCTM().inverse());

        offsetX = svgP.x - parseFloat(pinElement.getAttribute("cx"));
        offsetY = svgP.y - parseFloat(pinElement.getAttribute("cy"));

        svg.addEventListener("mousemove", onMouseMove);
        svg.addEventListener("mouseup", onMouseUp);
    }

    function onMouseMove(e) {
        const pt = svg.createSVGPoint();
        pt.x = e.clientX;
        pt.y = e.clientY;
        const svgP = pt.matrixTransform(svg.getScreenCTM().inverse());

        pinElement.setAttribute("cx", svgP.x - offsetX);
        pinElement.setAttribute("cy", svgP.y - offsetY);
    }

    function onMouseUp() {
        svg.removeEventListener("mousemove", onMouseMove);
        svg.removeEventListener("mouseup", onMouseUp);

        // Save new position to DB
        const newX = pinElement.getAttribute("cx");
        const newY = pinElement.getAttribute("cy");

        fetch('update_map.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: pinId, pin_x: newX, pin_y: newY })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Pin position updated!');
            } else {
                alert('Failed to update pin.');
            }
        })
        .catch(() => alert('Error connecting to server.'));
    }

    pinElement.addEventListener("mousedown", onMouseDown, { once: true });
}
