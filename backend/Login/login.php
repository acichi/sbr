<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;300;400;600&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  <link rel="stylesheet" href="css/login.css" />
  <style>
    body {
      background-color: #f8f9fa;
      color: #333;
    }
    .card {
      background: #fff;
      color: #333;
      border-radius: 15px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .card h2 {
      color: #333;
    }
    .form-label {
      font-weight: 600;
    }
    .btn-warning {
      background-color: #ffc107;
      border-color: #ffc107;
    }
    .btn-success {
      background-color: #28a745;
      border-color: #28a745;
    }
    .btn-warning:hover, .btn-success:hover {
      opacity: 0.9;
    }
    #toggle-password {
      font-size: 1.2rem;
    }
    .text-primary {
      color: #7ab4a1 !important;
    }
  </style>
</head>
<body>
<div style="padding:60px"></div>
  <div class="container d-flex justify-content-center align-items-center min-vh-10">
    <div class="card p-4 shadow rounded animate__animated animate__fadeIn" style="max-width: 500px; width: 1500px;">
      <h2 class="text-center mb-4">Login</h2>

      <div class="text-center mb-4">
        <img src="image/profile.png" alt="Profile Image" class="rounded-circle" style="width: 100px; height: 100px;" />
      </div>

      <!-- LOGIN FORM -->
      <div id="login-form-wrapper">
        <form id="login-form" method="POST">
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required />
          </div>

          <div class="mb-3 position-relative">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required />
            <span id="toggle-password" class="position-absolute top-50 end-0 translate-middle-y pe-3" style="cursor: pointer;">👁️</span>
          </div>

          <button type="submit" id="login-button" class="btn btn-warning w-100 text-dark fw-semibold">Login</button>

          <p id="error-message" class="text-danger text-center mt-2 d-none"></p>
          <p id="success-message" class="text-success text-center mt-2 d-none"></p>
        </form>

        <div class="text-center mt-3">
          <p class="mb-0">Don't have an account? <a href="#" id="show-register" class="text-primary">Register here</a></p>
        </div>
      </div>

      <!-- REGISTER FORM-->
      <div id="register-form-wrapper" class="d-none">
        <form action="register_logic.php" method="POST" id="register-form">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="name" class="form-label">Full Name</label>
              <input type="text" id="name" name="name" class="form-control" required />
            </div>
            <div class="col-md-6 mb-3">
              <label for="reg-email" class="form-label">Email</label>
              <input type="email" id="reg-email" name="email" class="form-control" required />
            </div>
            <div class="col-md-6 mb-3">
              <label for="user" class="form-label">Username</label>
              <input type="text" id="user" name="user" class="form-control" required />
            </div>
            <div class="col-md-6 mb-3">
              <label for="pass" class="form-label">Password</label>
              <input type="password" id="pass" name="password" class="form-control" required />
            </div>
            <div class="col-md-6 mb-3">
              <label for="mobile" class="form-label">Mobile</label>
              <input type="text" id="mobile" name="mobile" class="form-control" required />
            </div>
            <div class="col-md-6 mb-3">
              <label for="address" class="form-label">Address</label>
              <input type="text" id="address" name="address" class="form-control" required />
            </div>
            <div class="col-md-6 mb-3">
              <label for="gender" class="form-label">Gender</label>
              <select id="gender" name="gender" class="form-control" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
              </select>
            </div>
          </div>

          <button type="submit" class="btn btn-success w-100 fw-semibold">Create Account</button>

          <div class="text-center mt-3">
            <p class="mb-0">Already have an account? <a href="#" id="show-login" class="text-primary">Login here</a></p>
          </div>
        </form>
      </div>

    </div>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  // Toggle Password Visibility
  $("#toggle-password").click(function() {
    const passwordField = $("#password");
    const type = passwordField.attr("type") === "password" ? "text" : "password";
    passwordField.attr("type", type);
  });

  // Switch to Register Form
  $("#show-register").click(function() {
    $("#login-form-wrapper").addClass("d-none");
    $("#register-form-wrapper").removeClass("d-none");
  });

  // Switch to Login Form
  $("#show-login").click(function() {
    $("#register-form-wrapper").addClass("d-none");
    $("#login-form-wrapper").removeClass("d-none");
  });
</script>
<script src="js/login.js"></script>
</body>
</html>
