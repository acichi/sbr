<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Enter OTP</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=Roboto&display=swap" rel="stylesheet">
  
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <style>
    body {
      font-family: 'Playfair Display', serif;
      background: url('../pics/bg.png') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .otp-card {
      background: rgba(255, 255, 255, 0.85);
      border-radius: 20px;
      padding: 30px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
      max-width: 400px;
      width: 100%;
    }

    .otp-title {
      color: #e08f5f;
      font-weight: bold;
      text-align: center;
      margin-bottom: 25px;
    }

    .otp-input {
      width: 45px;
      height: 50px;
      text-align: center;
      font-size: 24px;
      font-family: 'Playfair Display', serif;
      margin: 0 5px;
      border: 2px solid #7ab4a1;
      border-radius: 10px;
      outline: none;
      transition: 0.3s ease;
    }

    .otp-input:focus {
      border-color: #e19985;
      box-shadow: 0 0 5px rgba(225, 153, 133, 0.6);
    }

    .otp-form {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin-bottom: 20px;
    }

    .btn-theme {
      background-color: #7ab4a1;
      border: none;
      font-weight: bold;
      font-family: 'Playfair Display', serif;
    }

    .btn-theme:hover {
      background-color: #e08f5f;
    }

    @media (max-width: 480px) {
      .otp-input {
        width: 38px;
        height: 45px;
        font-size: 20px;
        margin: 0 3px;
      }
    }
  </style>
</head>
<body>

  <div class="otp-card text-center">
    <h4 class="otp-title">OTP Verification</h4>
    <form method="POST" action="verify_otp.php" class="d-flex flex-column align-items-center">
      <div class="otp-form">
        <input type="text" name="otp1" maxlength="1" class="otp-input" required>
        <input type="text" name="otp2" maxlength="1" class="otp-input" required>
        <input type="text" name="otp3" maxlength="1" class="otp-input" required>
        <input type="text" name="otp4" maxlength="1" class="otp-input" required>
        <input type="text" name="otp5" maxlength="1" class="otp-input" required>
        <input type="text" name="otp6" maxlength="1" class="otp-input" required>
      </div>
      <button type="submit" class="btn btn-theme w-100 py-2">Verify</button>
    </form>
  </div>

  <script>
    // Auto-focus next input
    const inputs = document.querySelectorAll('.otp-input');
    inputs.forEach((input, i) => {
      input.addEventListener('input', () => {
        if (input.value && i < inputs.length - 1) {
          inputs[i + 1].focus();
        }
      });
    });
  </script>

</body>
</html>
