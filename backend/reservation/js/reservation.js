document.addEventListener("DOMContentLoaded", function () {
  const detailsContainer = document.getElementById("details-container");
  const closeDetailsButton = document.getElementById("closeDetails");
  const detailsTitle = document.getElementById("detailsTitle");
  const detailsText = document.getElementById("detailsText");
  const detailsPrice = document.getElementById("detailsPrice");
  const bookNowButton = document.getElementById("bookNowButton");

  const modal = document.getElementById("reservationModal");
  const facilityInput = document.getElementById("facilityName");
  const facilityDisplay = document.getElementById("facilityDisplay");
  const startDate = document.getElementById("startDate");
  const endDate = document.getElementById("endDate");
  const priceField = document.getElementById("price");
  const form = document.getElementById("reservationForm");

  const paymentMethodSelect = document.getElementById("paymentMethod");
  const cashAmountGroup = document.getElementById("cashAmountGroup");
  const cashAmountInput = document.getElementById("cashAmount");
  const paymentResult = document.getElementById("paymentResult");

  const receiptModal = new bootstrap.Modal(document.getElementById("receiptModal"));
  const downloadPdfBtn = document.getElementById("downloadPdfBtn");

  let selectedButton = null;
  let dailyRate = 0;

  function toggleCashInput() {
    if (paymentMethodSelect.value === "Cash") {
      cashAmountGroup.classList.remove("d-none");
    } else {
      cashAmountGroup.classList.add("d-none");
      cashAmountInput.value = "";
      paymentResult.textContent = "";
    }
  }

  function calculateBalanceOrChange() {
    const price = parseFloat(priceField.value.replace(/[^0-9.-]+/g, ""));
    const cash = parseFloat(cashAmountInput.value) || 0;

    if (cash < price) {
      const balance = price - cash;
      paymentResult.textContent = `Balance Due: ₱${balance.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
      paymentResult.style.color = 'red';
    } else if (cash >= price) {
      const change = cash - price;
      paymentResult.textContent = `Change: ₱${change.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
      paymentResult.style.color = 'green';
    }
  }

  toggleCashInput();
  paymentMethodSelect.addEventListener("change", toggleCashInput);
  cashAmountInput.addEventListener("input", calculateBalanceOrChange);

  document.querySelectorAll(".show-details").forEach((button) => {
    button.addEventListener("click", function () {
      if (selectedButton) {
        selectedButton.classList.remove("selected");
        selectedButton.style.backgroundColor = selectedButton.getAttribute("data-original-color");
      }

      selectedButton = button;
      button.classList.add("selected");
      button.setAttribute("data-original-color", button.style.backgroundColor);
      button.style.backgroundColor = "blue";

      const facility = button.getAttribute("data-facility");
      const details = button.getAttribute("data-details");
      const price = button.getAttribute("data-price");

      detailsTitle.textContent = facility;
      detailsText.textContent = details;
      detailsPrice.textContent = `Price: ₱${parseFloat(price).toFixed(2)}`;
      bookNowButton.setAttribute("data-facility", facility);
      bookNowButton.setAttribute("data-price", price);

      detailsContainer.classList.add("show");
    });
  });

  closeDetailsButton.addEventListener("click", function () {
    detailsContainer.classList.remove("show");
    if (selectedButton) {
      selectedButton.classList.remove("selected");
      selectedButton.style.backgroundColor = selectedButton.getAttribute("data-original-color");
      selectedButton = null;
    }
  });

  modal.addEventListener("show.bs.modal", function (event) {
    const button = event.relatedTarget;
    const facility = button.getAttribute("data-facility");
    dailyRate = parseFloat(button.getAttribute("data-price")) || 0;

    facilityInput.value = facility;
    facilityDisplay.value = facility;
    priceField.value = "₱0.00";
    startDate.value = "";
    endDate.value = "";
    cashAmountInput.value = "";
    paymentResult.textContent = "";
    toggleCashInput();
  });

  function calculatePrice() {
    const start = new Date(startDate.value);
    const end = new Date(endDate.value);

    if (!isNaN(start) && !isNaN(end) && end > start) {
      const diffTime = end - start;
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      const total = diffDays * dailyRate;
      priceField.value = `₱${total.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
      calculateBalanceOrChange();
    } else {
      priceField.value = "";
    }
  }

  startDate.addEventListener("change", calculatePrice);
  endDate.addEventListener("change", calculatePrice);

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const priceValue = parseFloat(priceField.value.replace(/[^0-9.-]+/g, ""));
    const cashInputValue = parseFloat(cashAmountInput.value || 0);
    let balanceOrChange = 0;

    if (cashInputValue >= priceValue) {
      balanceOrChange = 0;
    } else {
      balanceOrChange = priceValue - cashInputValue;
    }

    const formData = new FormData(form);
    formData.set("price", priceValue);
    formData.set("cashAmount", cashInputValue);
    formData.set("balanceOrChange", balanceOrChange);

    fetch("submit_reservation.php", {
      method: "POST",
      body: formData,
    })
      .then((res) => {
        if (!res.ok) {
          throw new Error("Network response was not ok");
        }
        return res.json();
      })
      .then((data) => {
        if (data.success) {
          bootstrap.Modal.getInstance(modal).hide();
          form.reset();
          priceField.value = "₱0.00";
          // Show receipt modal with data
          showReceiptModal(data.receipt);
        } else {
          Swal.fire("Error", data.message, "error");
        }
      })
      .catch((err) => {
        console.error("Error:", err);
        Swal.fire("Error", "Something went wrong!", "error");
      });
  });

  function showReceiptModal(data) {
    document.getElementById("receipt-transaction-id").textContent = data.transaction_id;
    document.getElementById("receipt-reservee").textContent = data.reservee;
    document.getElementById("receipt-facility").textContent = data.facility_name;
    document.getElementById("receipt-amount-paid").textContent = `₱${parseFloat(data.amount_paid).toFixed(2)}`;
    document.getElementById("receipt-payment-type").textContent = data.payment_type;
    document.getElementById("receipt-date-checkin").textContent = data.date_checkin;
    document.getElementById("receipt-date-checkout").textContent = data.date_checkout;
    document.getElementById("receipt-date-booked").textContent = data.date_booked;
    document.getElementById("receipt-timestamp").textContent = `Transaction Date: ${data.timestamp}`;

    receiptModal.show();

    downloadPdfBtn.onclick = () => {
      const element = document.getElementById("receiptContent");
      const opt = {
        margin: 0.5,
        filename: `receipt-${data.transaction_id}.pdf`,
        image: { type: "jpeg", quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: "in", format: "letter", orientation: "portrait" },
      };
      html2pdf().set(opt).from(element).save();
    };
  }

  document.getElementById("printReceiptBtn").onclick = () => {
    const printContents = document.getElementById("receiptContent").innerHTML;
    const printWindow = window.open('', '', 'height=700,width=900');
  
    printWindow.document.write('<html><head><title>Print Receipt</title>');
    printWindow.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">');
    printWindow.document.write('</head><body>');
    printWindow.document.write(printContents);
    printWindow.document.write('</body></html>');
  
    printWindow.document.close();
    printWindow.focus();
  
    // Wait a bit for the styles to load before printing
    setTimeout(() => {
      printWindow.print();
      printWindow.close();
    }, 500);
  };
  
});