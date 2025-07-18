const inputs = document.querySelectorAll('.otp-input');

    inputs.forEach((input, index) => {
      input.addEventListener('input', () => {
        if (input.value.length === 1 && index < inputs.length - 1) {
          inputs[index + 1].focus();
        }
      });

      input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace') {
          if (input.value === '' && index > 0) {
            inputs[index - 1].focus();
          }
        } else if (e.key === 'ArrowLeft' && index > 0) {
          inputs[index - 1].focus();
        } else if (e.key === 'ArrowRight' && index < inputs.length - 1) {
          inputs[index + 1].focus();
        }
      });
    });

    function submitOTP() {
      const otp = Array.from(inputs).map(input => input.value).join('');
      if (otp.length === 6) {
        alert(`OTP entered: ${otp}`);
        // You can send it to backend here
      } else {
        alert('Please enter all 6 digits');
      }
    }