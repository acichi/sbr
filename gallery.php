<?php
// gallery.php
?>
<!DOCTYPE HTML>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Gallery | Shelton Beach Resort</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Sans:wght@400;700&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/animate.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/aos.css">
    <link rel="stylesheet" href="css/fancybox.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="pics/logo2.png" type="image/png">
  </head>

  <body>

    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Hero Section -->
    <section class="site-hero overlay" style="background-image: url('pics/bg.png');" data-stellar-background-ratio="0.5">
      <div class="container">
        <div class="row site-hero-inner justify-content-center align-items-center">
          <div class="col-md-10 text-center" data-aos="fade-up">
            <span class="custom-caption text-uppercase text-white d-block mb-3">Shelton Beach Resort</span>
            <h1 class="heading">Our Memories</h1>
          </div>
        </div>
      </div>
      <a class="mouse smoothscroll" href="#gallery-section">
        <div class="mouse-icon">
          <span class="mouse-wheel"></span>
        </div>
      </a>
    </section>

    <!-- Gallery Section -->
    <section class="section bg-light" id="gallery-section">
      <div class="container">
        <div class="row text-center mb-5">
          <div class="col-md-12" data-aos="fade-up">
            <h2 class="heading">Our Memories</h2>
            <p class="text-muted">Capture Yours. Visit Us Today!</p>
          </div>
        </div>
        <div class="row">
          <?php include("gallery/gallery_view.php");?>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="section bg-image overlay" style="background-image: url('pics/5.png');">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-12 col-md-6 text-center mb-4 mb-md-0 text-md-left" data-aos="fade-up">
            <h2 class="text-white font-weight-bold">Capture Yours. Visit Us Today!</h2>
          </div>
          <div class="col-12 col-md-6 text-center text-md-right" data-aos="fade-up" data-aos-delay="200">
            <a href="reservation.html" class="btn btn-outline-white-primary py-3 text-white px-5">Reserve Now!</a>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <!-- JS Libraries -->
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/jquery-migrate-3.0.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/jquery.stellar.min.js"></script>
    <script src="js/jquery.fancybox.min.js"></script>
    <script src="js/aos.js"></script>
    <script src="js/main.js"></script>

    <script>
      $(document).ready(function() {
        $('[data-fancybox="gallery"]').fancybox({
          buttons: [
            "zoom", "slideShow", "thumbs", "close"
          ]
        });
      });
    </script>

  </body>
</html>
