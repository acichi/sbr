document.addEventListener("DOMContentLoaded", () => {
    const { jsPDF } = window.jspdf;
  
    // Function to show receipt modal with fetched data
    function showReceiptModal(receiptData) {
      const content = `
        <p><strong>Transaction ID:</strong> ${receiptData.transaction_id}</p>
        <p><strong>Reservee:</strong> ${receiptData.reservee}</p>
        <p><strong>Facility:</strong> ${receiptData.facility_name}</p>
        <p><strong>Amount Paid:</strong> ₱${receiptData.amount_paid.toFixed(2)}</p>
        <p><strong>Payment Type:</strong> ${receiptData.payment_type}</p>
        <p><strong>Date Check-in:</strong> ${receiptData.date_checkin}</p>
        <p><strong>Date Checkout:</strong> ${receiptData.date_checkout}</p>
        <p><strong>Date Booked:</strong> ${receiptData.date_booked}</p>
        <p><strong>Transaction Time:</strong> ${receiptData.timestamp}</p>
      `;
      document.getElementById("receiptContent").innerHTML = content;
  
      const receiptModal = new bootstrap.Modal(document.getElementById('receiptModal'));
      receiptModal.show();
  
      document.getElementById("downloadPDF").onclick = () => {
        const doc = new jsPDF();
        doc.setFontSize(14);
        doc.text("Transaction Receipt", 20, 20);
        let y = 40;
        Object.entries(receiptData).forEach(([key, value]) => {
          doc.text(`${key.replace(/_/g, ' ')}: ${value}`, 20, y);
          y += 10;
        });
        doc.save(`receipt_${receiptData.transaction_id}.pdf`);
      };
    }
  
    // Call this after successful form submit, using the transaction_id returned
    function fetchReceipt(transactionId) {
      fetch(`reservation_receipt.php?transaction_id=${transactionId}`)
        .then(res => res.json())
        .then(data => {
          if (data.success && data.receipt) {
            showReceiptModal(data.receipt);
          } else {
            alert("Failed to load receipt.");
          }
        })
        .catch(err => console.error("Fetch error:", err));
    }
  
    // Example usage: call this with returned transaction_id
    // fetchReceipt("ABC123");
  });