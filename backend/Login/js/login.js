$('#toggle-password').on('click', function () {
  const passwordField = $('#password');
  const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
  passwordField.attr('type', type);
  $(this).text(type === 'password' ? '👁️' : '🙈');
});

//register

$(document).ready(function () {
  $('#show-register').click(function (e) {
    e.preventDefault();
    $('#login-form-wrapper')
      .addClass('animate__animated animate__zoomOut')
      .one('animationend', function () {
        $(this).addClass('d-none').removeClass('animate__zoomOut');
        $('#register-form-wrapper').removeClass('d-none').addClass('animate__animated animate__zoomIn');
      });
  });

  $('#show-login').click(function (e) {
    e.preventDefault();
    $('#register-form-wrapper')
      .addClass('animate__animated animate__zoomOut')
      .one('animationend', function () {
        $(this).addClass('d-none').removeClass('animate__zoomOut');
        $('#login-form-wrapper').removeClass('d-none').addClass('animate__animated animate__zoomIn');
      });
  });
});




//field error handling with validation
$(document).ready(function() {
  $("#register-form").submit(function(e) {
    e.preventDefault(); // Prevent the form from submitting the default way

    // Clear previous errors
    $(".form-label").removeClass("text-danger");
    $(".is-invalid").removeClass("is-invalid");
    $(".error-message").remove();

    // Validate Password Length (8-16 characters)
    var password = $("#pass").val();
    if (password.length < 8 || password.length > 16) {
      var passwordField = $("#pass");
      passwordField.addClass("is-invalid");
      var errorMessage = $("<div>").addClass("error-message text-danger").text("Password must be between 8 and 16 characters.");
      passwordField.before(errorMessage);
      return;
    }

    // Collect form data
    var formData = $(this).serialize();

    // Send AJAX request
    $.ajax({
      url: "register_logic.php",
      type: "POST",
      data: formData,
      dataType: "json",
      success: function(response) {
        if (response.status === "error") {
          $.each(response.errors, function(field, message) {
            var fieldElement = $("[name='" + field + "']");
            fieldElement.addClass("is-invalid");
            var errorMessage = $("<div>").addClass("error-message text-danger").text(message);
            fieldElement.before(errorMessage);
          });
        } else if (response.status === "success") {
          Swal.fire({
            icon: "success",
            title: "Registration Successful",
            text: "You can now log in.",
            confirmButtonText: "Login"
          }).then(function() {
            window.location.href = "login.php";
          });
        }
      },
      error: function(xhr, status, error) {
        console.error("AJAX Error:", error);
        console.error("Response:", xhr.responseText);
        Swal.fire("Error", "Something went wrong. Please try again.", "error");
      }
    });
  });
});



//login field error handling with validation

$(document).ready(function () {
  $('#login-form').on('submit', function (e) {
    e.preventDefault();

    const email = $('#email').val().trim();
    const password = $('#password').val().trim();

    // Optional: Frontend validation
    if (!email || !password) {
      $('#error-message').removeClass('d-none').text('Email and password are required.');
      return;
    }

    $.ajax({
      url: 'login_logic.php', // Adjust if needed
      type: 'POST',
      dataType: 'json',
      data: { email, password },
      success: function (response) {
        if (response.status === 'success') {
          $('#error-message').addClass('d-none');
          $('#success-message').removeClass('d-none').text(response.message);

          // Optional SweetAlert
          if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'success',
              title: 'Logged in!',
              text: response.message,
              timer: 1500,
              showConfirmButton: false
            }).then(() => {
              if (response.role === 'admin') {
                window.location.href = '../admin/adminDash.php';
              } else if (response.role === 'client') {
                window.location.href = '../client/dashboard_client.php';
              } else {
                // Handle unexpected role
                console.error('Unexpected role:', response.role);
                $('#error-message').removeClass('d-none').text('Unexpected role.');
              }
            });
          } else {
            // Fallback
            if (response.role === 'admin') {
              window.location.href = '../admin/adminDash.php';
            } else if (response.role === 'client') {
              window.location.href = '../client/dashboard_client.php';
            } else {
              // Handle unexpected role
              console.error('Unexpected role:', response.role);
              $('#error-message').removeClass('d-none').text('Unexpected role.');
            }
          }

        } else {
          $('#success-message').addClass('d-none');
          $('#error-message').removeClass('d-none').text(response.message);

          if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'error',
              title: 'Login Failed',
              text: response.message
            });
          }
        }
      },
      error: function (xhr, status, error) {
        console.error('AJAX Error:', error);
        console.log('Response:', xhr.responseText);
        $('#error-message').removeClass('d-none').text('An unexpected error occurred.');
      }
    });
  });
});



  // Toggle password visibility
  $('#toggle-password').on('click', function () {
    const passwordField = $('#password');
    const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
    passwordField.attr('type', type);
    $(this).text(type === 'password' ? '👁️' : '🙈');
  });
