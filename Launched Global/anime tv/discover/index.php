<?php
session_start();
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ./index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Discover - CinemaApp</title>
  <link rel="stylesheet" href="discover.css">
</head>

<body>
  <!-- Loading Overlay -->
  <div class="loading-overlay" id="loadingOverlay">
    <div class="loader"></div>
  </div>

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

  <!-- Page Header -->
  <div class="page-header">
    <h1 class="page-title">🔍 Discover</h1>
    <p class="page-subtitle">Explore movies and shows by genre</p>
    
    <!-- Search Bar -->
    <div class="search-container">
      <input type="text" class="search-bar" placeholder="Search for movies, shows, or genres..." id="searchInput">
      <button class="search-btn" onclick="performSearch()">🔍</button>
    </div>
  </div>

  <!-- Genre Cards -->
  <div class="thumbnail-container">
    <a href="crime.html" class="card" data-genre="crime">
      <img src="https://img.youtube.com/vi/sQ7Tjglg508/0.jpg" alt="Crime" loading="lazy" />
      <h3>🔫 Crime</h3>
    </a>
    
    <a href="comedy.html" class="card" data-genre="comedy">
      <img src="//img.youtube.com/vi/5oH9Nr3bKfw/0.jpg" alt="Comedy" loading="lazy" />
      <h3>😂 Comedy</h3>
    </a>
    
    <a href="horror.html" class="card" data-genre="horror">
      <img src="//img.youtube.com/vi/CnpnTK7ISH4/0.jpg" alt="Horror" loading="lazy" />
      <h3>👻 Horror</h3>
    </a>
    
    <a href="action.html" class="card" data-genre="action">
      <img src="//img.youtube.com/vi/9aOwrWdxYxU/0.jpg" alt="Action" loading="lazy" />
      <h3>💥 Action</h3>
    </a>
    
    <a href="drama.html" class="card" data-genre="drama">
      <img src="//img.youtube.com/vi/lFBwQu8YtNY/0.jpg" alt="Drama" loading="lazy" />
      <h3>🎭 Drama</h3>
    </a>
    
    <a href="mythology.html" class="card" data-genre="mythology">
      <img src="//img.youtube.com/vi/rOP3nvxj14A/0.jpg" alt="Mythology" loading="lazy" />
      <h3>⚔️ Mythology</h3>
    </a>
  </div>

  <!-- Floating Action Button -->
  <a href="#top" class="fab" title="Back to top">↑</a>

  <!-- Bottom Navigation -->
  <div class="bottom-nav">
    <a href="/pega/index.php">🏠 Home</a>
    <a href="/connect/connect.php">💬 Discussion</a>
    <a href="/seasonal/index.php">🎬 Seasonal</a>
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

  <script>
    // Sidebar functionality
    const sidebar = document.getElementById('sidebar');
    const avatarBtn = document.getElementById('avatarBtn');
    const closeSidebar = document.getElementById('closeSidebar');
    const loadingOverlay = document.getElementById('loadingOverlay');

    // Sidebar controls
    avatarBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      sidebar.style.width = '280px';
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

    // Loading animation
    window.addEventListener('load', () => {
      setTimeout(() => {
        loadingOverlay.classList.add('hidden');
      }, 1000);
    });

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const cards = document.querySelectorAll('.card');

    function performSearch() {
      const query = searchInput.value.toLowerCase().trim();
      
      if (!query) {
        cards.forEach(card => {
          card.style.display = 'block';
          card.style.opacity = '1';
        });
        return;
      }

      cards.forEach(card => {
        const genre = card.dataset.genre;
        const title = card.querySelector('h3').textContent;
        
        if (genre.includes(query) || title.toLowerCase().includes(query)) {
          card.style.display = 'block';
          card.style.opacity = '1';
          card.style.animation = 'fadeIn 0.5s ease';
        } else {
          card.style.opacity = '0.3';
          card.style.transform = 'scale(0.95)';
        }
      });
    }

    // Real-time search
    searchInput.addEventListener('input', performSearch);

    // Search on Enter key
    searchInput.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        performSearch();
      }
    });

    // Smooth scroll for FAB
    document.querySelector('.fab').addEventListener('click', (e) => {
      e.preventDefault();
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });

    // Card hover effects with sound (optional)
    cards.forEach(card => {
      card.addEventListener('mouseenter', () => {
        // Add subtle vibration on mobile
        if (navigator.vibrate) {
          navigator.vibrate(50);
        }
      });

      // Add loading state when clicking cards
      card.addEventListener('click', (e) => {
        const overlay = document.createElement('div');
        overlay.className = 'loading-overlay';
        overlay.innerHTML = '<div class="loader"></div>';
        document.body.appendChild(overlay);
        
        // Remove loading after navigation (fallback)
        setTimeout(() => {
          if (overlay.parentNode) {
            overlay.remove();
          }
        }, 2000);
      });
    });

    // Add scroll-based animations
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.animation = 'slideInUp 0.6s ease forwards';
        }
      });
    }, observerOptions);

    // Observe all cards
    cards.forEach(card => {
      observer.observe(card);
    });

    // Add CSS animation
    const style = document.createElement('style');
    style.textContent = `
      @keyframes slideInUp {
        from {
          opacity: 0;
          transform: translateY(30px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }
      
      @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
      }
    `;
    document.head.appendChild(style);

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
      // ESC to close sidebar
      if (e.key === 'Escape') {
        sidebar.style.width = '0';
      }
      
      // Ctrl+F or Cmd+F to focus search
      if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        searchInput.focus();
      }
    });

    // Performance optimization: Lazy load images
    if ('IntersectionObserver' in window) {
      const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            const img = entry.target;
            img.src = img.dataset.src || img.src;
            img.classList.remove('lazy');
            observer.unobserve(img);
          }
        });
      });

      document.querySelectorAll('img[loading="lazy"]').forEach(img => {
        imageObserver.observe(img);
      });
    }
  </script>
</body>

</html>