 const svg = document.getElementById('map');
  const contextMenu = document.getElementById('contextMenu');
  const removeBtn = document.getElementById('removePinBtn');
  let selectedPin = null;
  let isDragging = false;

  // Add pin on map click
// Get SVG bounds in user space (not pixels)
function clampToSVG(x, y) {
  const svgWidth = svg.viewBox.baseVal.width;
  const svgHeight = svg.viewBox.baseVal.height;

  const clampedX = Math.max(0, Math.min(svgWidth, x));
  const clampedY = Math.max(0, Math.min(svgHeight, y));
  return { x: clampedX, y: clampedY };
}

// Add pin on map click
svg.addEventListener('click', function (e) {
  if (e.target.classList.contains('pin')) return;

  const pt = svg.createSVGPoint();
  pt.x = e.clientX;
  pt.y = e.clientY;
  const svgP = pt.matrixTransform(svg.getScreenCTM().inverse());

  const pos = clampToSVG(svgP.x, svgP.y);

  const pin = document.createElementNS("http://www.w3.org/2000/svg", "circle");
  pin.setAttribute("cx", pos.x);
  pin.setAttribute("cy", pos.y);
  pin.setAttribute("r", 10);
  pin.setAttribute("class", "pin");
  svg.appendChild(pin);

  makeDraggable(pin);
});


  // Enable dragging
  function makeDraggable(pin) {
    let offset = { x: 0, y: 0 };
    let start = { x: 0, y: 0 };

    pin.addEventListener('mousedown', function (e) {
      isDragging = false;
      selectedPin = pin;
      start.x = e.clientX;
      start.y = e.clientY;

      const pt = svg.createSVGPoint();
      pt.x = e.clientX;
      pt.y = e.clientY;
      const svgP = pt.matrixTransform(svg.getScreenCTM().inverse());

      offset.x = svgP.x - parseFloat(pin.getAttribute("cx"));
      offset.y = svgP.y - parseFloat(pin.getAttribute("cy"));

    function onMouseMove(e) {
    isDragging = true;
    const pt = svg.createSVGPoint();
    pt.x = e.clientX;
    pt.y = e.clientY;
    const svgP = pt.matrixTransform(svg.getScreenCTM().inverse());

    const pos = clampToSVG(svgP.x - offset.x, svgP.y - offset.y);

    pin.setAttribute("cx", pos.x);
    pin.setAttribute("cy", pos.y);
    }


      function onMouseUp(e) {
        document.removeEventListener('mousemove', onMouseMove);
        document.removeEventListener('mouseup', onMouseUp);

        if (!isDragging) {
          // It's a click, not a drag
          showContextMenu(e.pageX, e.pageY, pin);
        }
      }

      document.addEventListener('mousemove', onMouseMove);
      document.addEventListener('mouseup', onMouseUp);
    });
  }

  function showContextMenu(x, y, pin) {
    selectedPin = pin;
    contextMenu.style.left = `${x}px`;
    contextMenu.style.top = `${y}px`;
    contextMenu.style.display = 'block';
  }

  // Remove the selected pin
  removeBtn.addEventListener('click', () => {
    if (selectedPin) {
      selectedPin.remove();
      contextMenu.style.display = 'none';
      selectedPin = null;
    }
  });

  // Hide context menu on map click
svg.addEventListener('click', function (e) {
  // Don't add a new pin if the user clicked an existing pin
  if (e.target.classList.contains('pin')) return;

  // If user just wants to click the map, hide context menu
  contextMenu.style.display = 'none';

  const pt = svg.createSVGPoint();
  pt.x = e.clientX;
  pt.y = e.clientY;
  const svgP = pt.matrixTransform(svg.getScreenCTM().inverse());

  const pos = clampToSVG(svgP.x, svgP.y);

  const pin = document.createElementNS("http://www.w3.org/2000/svg", "circle");
  pin.setAttribute("cx", pos.x);
  pin.setAttribute("cy", pos.y);
  pin.setAttribute("r", 10);
  pin.setAttribute("class", "pin");
  svg.appendChild(pin);

  makeDraggable(pin); // Make new pin draggable
});


  // Hide on outside click
  document.addEventListener('click', (e) => {
    if (!svg.contains(e.target) && !contextMenu.contains(e.target)) {
      contextMenu.style.display = 'none';
      selectedPin = null;
    }
  });


  //saving the pin

  let lastClickCoords = { x: 0, y: 0 };

  const svgMap = document.getElementById('map');
  const saveBtn = document.getElementById('savePinBtn');
  const modal = new bootstrap.Modal(document.getElementById('facilityModal'));

  svgMap.addEventListener('click', function (e) {
    const rect = svgMap.getBoundingClientRect();
    const x = ((e.clientX - rect.left) / rect.width) * svgMap.viewBox.baseVal.width;
    const y = ((e.clientY - rect.top) / rect.height) * svgMap.viewBox.baseVal.height;

    lastClickCoords = { x: x.toFixed(2), y: y.toFixed(2) };
    console.log(`Clicked at X: ${lastClickCoords.x}, Y: ${lastClickCoords.y}`);
  });

  saveBtn.addEventListener('click', function () {
    if (lastClickCoords.x === 0 && lastClickCoords.y === 0) {
      alert("Please click a spot on the map before saving.");
      return;
    }

    document.getElementById('pin_x').value = lastClickCoords.x;
    document.getElementById('pin_y').value = lastClickCoords.y;
    modal.show();
  });

  document.getElementById('facilityForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('map_save.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.text())
    .then(response => {
      alert('Facility saved successfully.');
      modal.hide();
      this.reset();
    })
    .catch(err => {
      console.error(err);
      alert('Error saving facility.');
    });
  });
// edit pin
svg.addEventListener('click', function (e) {
  // Don't add a new pin if the user clicked an existing pin
  if (e.target.classList.contains('pin')) return;

  // If user just wants to click the map, hide context menu
  contextMenu.style.display = 'none';

  const pt = svg.createSVGPoint();
  pt.x = e.clientX;
  pt.y = e.clientY;
  const svgP = pt.matrixTransform(svg.getScreenCTM().inverse());

  const pos = clampToSVG(svgP.x, svgP.y);

  const pin = document.createElementNS("http://www.w3.org/2000/svg", "circle");
  pin.setAttribute("cx", pos.x);
  pin.setAttribute("cy", pos.y);
  pin.setAttribute("r", 10);
  pin.setAttribute("class", "pin");
  svg.appendChild(pin);

  makeDraggable(pin); // Make new pin draggable
});



