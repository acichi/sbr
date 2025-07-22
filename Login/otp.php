<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Enter OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4 shadow-sm">
                <h4 class="mb-3">OTP Verification</h4>
                <form method="POST" action="verify_otp.php">
                    <div class="mb-3">
                        <label for="otp" class="form-label">Enter OTP</label>
                        <input type="text" class="form-control" name="otp" id="otp" maxlength="6" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Verify</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
