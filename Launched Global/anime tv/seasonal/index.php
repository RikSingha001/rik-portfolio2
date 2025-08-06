<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Seasonal - CinemaApp</title>
<link rel="stylesheet" href="seasonal.css">
</head>

<body>
  <header class="header">
    <nav class="nav">
      <a href="#" class="logo">🎭 CinemaApp</a>
      <div class="nav-links">
        <img src="avatar.jpg" alt="avatar" class="avatar" id="avatarBtn">
      </div>
    </nav>
  </header>

  <!-- Sidebar -->
  <div id="sidebar" class="sidebar">
    <a href="#" class="close-btn" id="closeSidebar">&times;</a>
    <a href="/discover/history.html">📺 My List</a>
    <a href="../profile/profile.php">👤 Profile</a>
    <a href="../connect/connect.php">💬 Discussion</a>
<a href="../index.php" class="nav-link">Logout</a>
<a href="/drive/index.php" class="nav-link">admin</a>


  </div>



  









  <div class="main-content">
    <h1 class="page-title">🎬 Seasonal Collections</h1>
    <p class="page-subtitle">Discover trending movies and shows for every season</p>

    <!-- Main Advertisement Banner -->
    <div class="ad-banner" onclick="window.open('https://example.com/premium', '_blank')">
      <h3>🌟 Upgrade to Premium!</h3>
      <p>Get unlimited access to exclusive content, ad-free streaming, and 4K quality</p>
      <a href="#" class="ad-cta">Start Free Trial</a>
    </div>

    <!-- Multi-Ad Carousel -->
    <div class="ad-carousel">
      <div class="ad-carousel-inner" id="adCarousel">
        <a href="https://netflix.com" target="_blank" class="ad-slide">
          🎬 Netflix Original Series - Watch Now!
        </a>
        <a href="https://amazon.com/primevideo" target="_blank" class="ad-slide">
          📺 Amazon Prime Video - Latest Movies
        </a>
        <a href="https://disneyplus.com" target="_blank" class="ad-slide">
          🏰 Disney+ Exclusive Content
        </a>
        <a href="https://hulu.com" target="_blank" class="ad-slide">
          🔥 Hulu Originals - Stream Today
        </a>
      </div>
      <div class="carousel-nav">
        <div class="carousel-dot active" onclick="showSlide(0)"></div>
        <div class="carousel-dot" onclick="showSlide(1)"></div>
        <div class="carousel-dot" onclick="showSlide(2)"></div>
        <div class="carousel-dot" onclick="showSlide(3)"></div>
      </div>
    </div>
<div id="supportWidget" style="position:fixed;top:50%;left:18px;transform:translateY(-50%);background:#bd5fff;color:#fff;padding:15px 20px;border-radius:12px;box-shadow:0 4px 10px rgba(0,0,0,0.3);font-family:sans-serif;cursor:pointer;z-index:9999;">
  💜 Contact Me
</div>

<div id="contactBox" style="display:none;position:fixed;top:50%;left:90px;transform:translateY(-50%);background:#fff;color:#333;padding:20px;width:250px;border-radius:10px;box-shadow:0 4px 10px rgba(0,0,0,0.2);z-index:9999;">
  <div id="closeBtn" style="position:absolute;top:5px;right:10px;cursor:pointer;font-weight:bold;font-size:18px;">&times;</div>
  <h3 style="margin-top:0;font-family:sans-serif;">Contact Me</h3>
  <form action="https://formspree.io/f/mrblbawn" method="POST">
    <input type="text" name="name" placeholder="Your name" required style="width:100%;padding:8px;margin-top:8px;border-radius:5px;border:1px solid #ccc;font-family:sans-serif;">
    <input type="email" name="email" placeholder="Your email" required style="width:100%;padding:8px;margin-top:8px;border-radius:5px;border:1px solid #ccc;font-family:sans-serif;">
    <textarea name="message" placeholder="Your message" rows="3" required style="width:100%;padding:8px;margin-top:8px;border-radius:5px;border:1px solid #ccc;font-family:sans-serif;"></textarea>
    <button type="submit" style="margin-top:10px;background:#bd5fff;color:#fff;padding:8px;border:none;border-radius:5px;cursor:pointer;width:100%;">Send</button>
  </form>
</div>
<script>
  document.getElementById("supportWidget").onclick = function () {
    const b = document.getElementById("contactBox");
    b.style.display = b.style.display === "block" ? "none" : "block";
  };
  document.getElementById("closeBtn").onclick = function () {
    document.getElementById("contactBox").style.display = "none";
  };
</script>
    <!-- Seasonal Categories -->
    <div class="categories-grid">
      <!-- Winter Collection -->
      <div class="category-card">
        <span class="category-icon">❄️</span>
        <h3>Winter Blockbusters</h3>
        <p>Cozy up with the hottest winter releases and holiday classics</p>
        <div class="category-links">
          <a href="https://netflix.com/browse/genre/8711" target="_blank" class="direct-link">Netflix Winter Movies</a>
          <a href="https://disneyplus.com/movies/frozen" target="_blank" class="direct-link">Disney Holiday Collection</a>
        </div>
      </div>

      <!-- Spring Collection -->
      <div class="category-card">
        <span class="category-icon">🌸</span>
        <h3>Spring Awakening</h3>
        <p>Fresh stories and romantic comedies to welcome the new season</p>
        <div class="category-links">
          <a href="https://amazon.com/gp/video/storefront/romance" target="_blank" class="direct-link">Prime Romance</a>
          <a href="https://hulu.com/hub/spring-movies" target="_blank" class="direct-link">Hulu Spring Hub</a>
        </div>
      </div>

      <!-- Summer Collection -->
      <div class="category-card">
        <span class="category-icon">☀️</span>
        <h3>Summer Heat</h3>
        <p>Action-packed adventures and feel-good summer vibes</p>
        <div class="category-links">
          <a href="https://netflix.com/browse/genre/1365" target="_blank" class="direct-link">Action & Adventure</a>
          <a href="https://paramountplus.com/movies/action" target="_blank" class="direct-link">Paramount+ Action</a>
        </div>
      </div>

      <!-- Fall Collection -->
      <div class="category-card">
        <span class="category-icon">🍂</span>
        <h3>Autumn Chills</h3>
        <p>Thrillers, mysteries, and spine-tingling horror for fall nights</p>
        <div class="category-links">
          <a href="https://netflix.com/browse/genre/8933" target="_blank" class="direct-link">Netflix Thrillers</a>
          <a href="https://hulu.com/hub/horror-movies" target="_blank" class="direct-link">Hulu Horror Hub</a>
        </div>
      </div>

      <!-- New Releases -->
      <div class="category-card">
        <span class="category-icon">🆕</span>
        <h3>Latest Releases</h3>
        <p>Brand new movies and series just added to your favorite platforms</p>
        <div class="category-links">
          <a href="https://netflix.com/latest" target="_blank" class="direct-link">Netflix New & Popular</a>
          <a href="https://disneyplus.com/whats-new" target="_blank" class="direct-link">Disney+ New Releases</a>
        </div>
      </div>

      <!-- Trending Now -->
      <div class="category-card">
        <span class="category-icon">🔥</span>
        <h3>Trending Now</h3>
        <p>What everyone's watching right now - don't miss out!</p>
        <div class="category-links">
          <a href="https://netflix.com/browse/trending" target="_blank" class="direct-link">Netflix Trending</a>
          <a href="https://amazon.com/gp/video/storefront/trending" target="_blank" class="direct-link">Prime Trending</a>
        </div>
      </div>
    </div>
  </div>

  <div class="bottom-nav">
    <a href="/pega/index.php">🏠 Home</a>
    <a href="/connect/connect.php">💬 Discussion</a>
    <a href="/discover/index.php">🔍 Discover</a>
  </div>

  <script>
    // Sidebar functionality
    const sidebar = document.getElementById('sidebar');
    const avatarBtn = document.getElementById('avatarBtn');
    const closeSidebar = document.getElementById('closeSidebar');

    avatarBtn.addEventListener('click', () => {
      sidebar.style.width = '250px';
    });

    closeSidebar.addEventListener('click', () => {
      sidebar.style.width = '0';
    });

    // Close sidebar when clicking outside
    document.addEventListener('click', (e) => {
      if (!sidebar.contains(e.target) && !avatarBtn.contains(e.target)) {
        sidebar.style.width = '0';
      }
    });

    // Ad Carousel functionality
    let currentSlide = 0;
    const totalSlides = 4;
    const carousel = document.getElementById('adCarousel');
    const dots = document.querySelectorAll('.carousel-dot');

    function showSlide(index) {
      currentSlide = index;
      carousel.style.transform = `translateX(-${index * 100}%)`;
      
      // Update dots
      dots.forEach((dot, i) => {
        dot.classList.toggle('active', i === index);
      });
    }

    // Auto-rotate carousel
    setInterval(() => {
      currentSlide = (currentSlide + 1) % totalSlides;
      showSlide(currentSlide);
    }, 5000);

    // Add click tracking for ads (you can integrate with analytics)
    function trackAdClick(adName, url) {
      console.log(`Ad clicked: ${adName} -> ${url}`);
      // Add your analytics tracking code here
      // gtag('event', 'ad_click', { ad_name: adName, destination_url: url });
    }

    // Enhanced hover effects for category cards
    const categoryCards = document.querySelectorAll('.category-card');
    categoryCards.forEach(card => {
      card.addEventListener('mouseenter', () => {
        card.style.transform = 'translateY(-10px) scale(1.02)';
      });
      
      card.addEventListener('mouseleave', () => {
        card.style.transform = 'translateY(0) scale(1)';
      });
    });

    // Smooth scrolling for internal links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      });
    });

    // Add loading animation for external links
    document.querySelectorAll('a[target="_blank"]').forEach(link => {
      link.addEventListener('click', function() {
        this.style.opacity = '0.7';
        setTimeout(() => {
          this.style.opacity = '1';
        }, 1000);
      });
    });
  </script>
</body>

</html>