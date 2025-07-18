<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login | Shelton Beach Haven</title>

  <!-- Fonts & Core Styles -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Roboto+Sans:400,700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/style.css" />
</head>
<body style="background: url('../pics/bg.png') no-repeat center center fixed; background-size: cover; font-family: 'Roboto Sans', sans-serif;">

<div class="container d-flex justify-content-center align-items-center min-vh-100">
  <div class="p-4 rounded-4 shadow" style="max-width: 600px; width: 100%; background-color: rgba(255,255,255,0.9); backdrop-filter: blur(5px);">
    <h2 class="text-center mb-3" style="font-family: 'Playfair Display', serif;">Welcome!</h2>

    <div class="text-center mb-3">
      <img src="../pics/" alt="Profile" class="rounded-circle" width="80" height="80">
    </div>

    <!-- Back to Home -->
    <div class="text-center mb-3">
      <a href="index.php" class="btn btn-sm btn-outline-dark">← Back to Home</a>
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
