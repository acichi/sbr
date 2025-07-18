<!-- Link fonts (place this in your <head>) -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">

<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  html, body {
    font-family: 'Roboto', sans-serif;
    background: transparent !important;
  }

  .navbar {
    background-color: rgba(247, 135, 71, 0.35);
    padding: 15px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 999;
    backdrop-filter: blur(6px);
    transition: background 0.3s ease;
  }

  .navbar .logo {
    font-family: 'Playfair Display', serif;
    font-size: 32px;
    color: #fff;
    text-decoration: none;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
  }

  .navbar ul {
    list-style: none;
    display: flex;
    gap: 22px;
    transition: max-height 0.3s ease;
  }

  .navbar ul li a {
    color: #fff;
    text-decoration: none;
    font-weight: 600;
    padding: 8px 14px;
    border-radius: 25px;
    transition: 0.3s ease;
  }

  .navbar ul li a:hover,
  .navbar ul li.active a {
    background-color: #fff;
    color: #e08f5f;
  }

  .menu-toggle {
    display: none;
    flex-direction: column;
    cursor: pointer;
    gap: 5px;
  }

  .menu-toggle span {
    height: 3px;
    width: 25px;
    background: #fff;
    border-radius: 3px;
    transition: 0.3s ease;
  }

  @media (max-width: 768px) {
    .menu-toggle {
      display: flex;
    }

    .navbar ul {
      flex-direction: column;
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      background-color: rgba(224, 143, 95, 0.95);
      max-height: 0;
      overflow: hidden;
      padding: 0;
    }

    .navbar ul.show {
      max-height: 500px;
    }

    .navbar ul li {
      padding: 12px 20px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }

    .navbar ul li:last-child {
      border-bottom: none;
    }
  }
</style>

<!-- HTML Navbar -->
<nav class="navbar">
  <a class="logo" href="index.php">Shelton Beach Haven</a>
  <div class="menu-toggle" onclick="toggleMenu()">
    <span></span>
    <span></span>
    <span></span>
  </div>
  <ul id="menu">
    <li><a href="index.php">Home</a></li>
    <li><a href="about.php">About</a></li>
    <li><a href="gallery.php">Gallery</a></li>
    <li><a href="feedbacks.php">Feedbacks</a></li>
  </ul>
</nav>

<!-- JavaScript -->
<script>
  function toggleMenu() {
    document.getElementById("menu").classList.toggle("show");
  }

  // Add "active" class to current page link
  document.addEventListener("DOMContentLoaded", function () {
    const currentUrl = window.location.pathname.split("/").pop();
    const navLinks = document.querySelectorAll(".navbar ul li a");

    navLinks.forEach(link => {
      const linkHref = link.getAttribute("href");
      if (linkHref === currentUrl || (currentUrl === "" && linkHref === "index.php")) {
        link.parentElement.classList.add("active");
      } else {
        link.parentElement.classList.remove("active");
      }
    });
  });
</script>
