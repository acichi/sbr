<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<section id="sidebar"><br /><br /><br />
    <a href="#" class="brand">
      <img src="../pics/logo2.png" alt="SBR Logo" width="125" height="125"/>
    </a>
    <ul class="side-menu top">
      <li>
        <a href="cusdash.php" class="<?= $currentPage === 'cusdash.php' ? 'active' : '' ?>">
          <i class='bx bxs-dashboard'></i>
          <span class="text">Dashboard</span>
        </a>
      </li>
      <li>
        <a href="cusdash.php#reservationTable" class="<?= $currentPage === 'cusdash.php' ? 'active' : '' ?>">
          <i class='bx bxs-shopping-bag-alt'></i>
          <span class="text">My Reservations</span>
        </a>
      </li>
      <li>
        <a href="reserve_now.php" class="<?= $currentPage === 'reserve_now.php' ? 'active' : '' ?>">
          <i class='bx bxs-building'></i>
          <span class="text text-primary">Reserve Now</span>
        </a>
      </li>
      <li>
        <a href="my_reviews.php" class="<?= $currentPage === 'my_reviews.php' ? 'active' : '' ?>">
          <i class='bx bxs-message-dots'></i>
          <span class="text">My Reviews</span>
        </a>
      </li>
      <li>
        <a href="cusdash.php#transactionTable" class="<?= $currentPage === 'cusdash.php' ? 'active' : '' ?>">
          <i class='bx bxs-shopping-bag-alt'></i>
          <span class="text">Transaction History</span>
        </a>
      </li>
    </ul>
    <ul class="side-menu">
      <li>
        <a href="logout.php" class="logout <?= $currentPage === 'logout.php' ? 'active' : '' ?>">
          <i class='bx bxs-log-out-circle'></i>
          <span class="text">Logout</span>
        </a>
      </li>
    </ul>
  </section>