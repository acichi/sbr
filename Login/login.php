<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login | Shelton Beach Haven</title>

  <!-- Fonts & Core Styles -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      font-family: 'Roboto', sans-serif;
      background: url('../pics/bg.png') no-repeat center center fixed;
      background-size: cover;
      margin: 0;
      padding: 0;
    }

    .form-wrapper {
      background-color: rgba(247, 135, 71, 0.35);
      backdrop-filter: blur(6px);
      border: 1px solid rgba(255, 255, 255, 0.4);
      border-radius: 2rem;
      padding: 2.5rem;
      max-width: 600px;
      width: 100%;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    }

    .form-wrapper h2 {
      font-family: 'Playfair Display', serif;
      color: #fff;
      text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
    }

    .form-wrapper .form-label,
    .form-wrapper p,
    .form-wrapper a,
    .form-wrapper label {
      color: #fff;
    }

    .form-wrapper .form-control {
      background-color: rgba(255, 255, 255, 0.85);
      border: none;
    }

    .btn-warning {
      background-color: #e08f5f;
      border: none;
    }

    .btn-warning:hover {
      background-color: #d27c49;
    }

    .btn-success {
      background-color: #7ab4a1;
      border: none;
    }

    .btn-success:hover {
      background-color: #65a291;
    }

    /* Back to Home Button Styling */
    .back-home-btn {
      position: fixed;
      top: 20px;
      left: 20px;
      padding: 10px 20px;
      background-color: rgba(247, 135, 71, 0.35);
      border: 1px solid rgba(255, 255, 255, 0.4);
      border-radius: 50px;
      font-weight: 600;
      color: #fff;
      text-decoration: none;
      font-family: 'Roboto', sans-serif;
      backdrop-filter: blur(6px);
      transition: background 0.3s ease, box-shadow 0.3s ease;
      z-index: 999;
    }

    .back-home-btn:hover {
      background-color: rgba(255, 255, 255, 0.3);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
      color: #fff;
    }
  </style>
</head>
<body>

  <!-- Back to Home Button -->
  <a href="../index.php" class="back-home-btn">← Back to Home</a>

  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="form-wrapper">
      <h2 class="text-center mb-4">Welcome!</h2>

      <div class="text-center mb-4">
        <img src="../pics/profile.png" alt="Profile" class="rounded-circle" width="300" height="300">
      </div>

      <!-- LOGIN FORM -->
      <div id="login-form-wrapper">
        <form method="POST" action="login_logic.php">
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3 position-relative">
            <label class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
            <span id="toggle-password" class="position-absolute top-50 end-0 translate-middle-y pe-3" style="cursor: pointer;">👁️</span>
          </div>
          <button type="submit" class="btn btn-warning w-100">Login</button>
          <div class="text-center mt-3">
            <p>Don't have an account? <a href="#" id="show-register">Register here</a></p>
          </div>
        </form>
      </div>

      <!-- REGISTER FORM -->
      <div id="register-form-wrapper" class="d-none">
        <form method="POST" action="register_logic.php">
          <div class="row">
            <div class="col-md-6 mb-2">
              <label class="form-label">Full Name</label>
              <input type="text" name="name" class="form-control" required>
            </div>
            <div class="col-md-6 mb-2">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="col-md-6 mb-2">
              <label class="form-label">Username</label>
              <input type="text" name="user" class="form-control" required>
            </div>
            <div class="col-md-6 mb-2">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <div class="col-md-6 mb-2">
              <label class="form-label">Mobile</label>
              <input type="text" name="mobile" class="form-control" required>
            </div>
            <div class="col-md-6 mb-2">
              <label class="form-label">Address</label>
              <input type="text" name="address" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Gender</label>
              <select name="gender" class="form-control" required>
                <option value="">Select</option>
                <option>Male</option>
                <option>Female</option>
                <option>Other</option>
              </select>
            </div>
          </div>
          <button type="submit" class="btn btn-success w-100">Create Account</button>
          <div class="text-center mt-3">
            <p>Already have an account? <a href="#" id="show-login">Login here</a></p>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $("#toggle-password").click(function () {
      const input = $("#password");
      input.attr("type", input.attr("type") === "password" ? "text" : "password");
    });

    $("#show-register").click(function () {
      $("#login-form-wrapper").addClass("d-none");
      $("#register-form-wrapper").removeClass("d-none");
    });

    $("#show-login").click(function () {
      $("#register-form-wrapper").addClass("d-none");
      $("#login-form-wrapper").removeClass("d-none");
    });
  </script>
</body>
</html>
