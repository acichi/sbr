<?php include 'properties/connection.php'; ?>

<section class="section bg-light">
  <div class="container">
    <div class="row justify-content-center text-center mb-5">
      <div class="col-md-8">
        <h2 class="heading" data-aos="fade-up">Gallery</h2>
        <p data-aos="fade-up" data-aos-delay="100">Photos uploaded by our admins — explore our scenic paradise!</p>
      </div>
    </div>

    <div class="row gallery-flex" data-aos="fade-up" data-aos-delay="200">
      <?php
        $sql = "SELECT * FROM gallery_images ORDER BY uploaded_at DESC";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
            echo '<div class="gallery-item">';
            echo '<a href="uploads/'.$row['filename'].'" data-fancybox="gallery" data-caption="'.$row['caption'].'">';
            echo '<img src="uploads/'.$row['filename'].'" alt="Gallery Image">';
            echo '</a>';
            if ($row['caption']) {
              echo '<p class="text-center mt-2">'.$row['caption'].'</p>';
            }
            echo '</div>';
          }
        } else {
          echo '<p class="text-center">No images yet. Stay tuned!</p>';
        }
      ?>
    </div>
  </div>
</section>
