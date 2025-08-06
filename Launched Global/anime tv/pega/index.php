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
    <title>CinemaApp - Your Movie Experience</title>
    <link rel="stylesheet" href="1stpage.css">
</head>

<body>
    <!-- Header -->
    <header class="header">
        <nav class="nav">
            <a href="#" class="logo">🎭 CinemaApp</a>
            <div class="nav-links">
                <img src="avatar.jpg" alt="User Avatar" class="avatar" id="avatarBtn">
            </div>
        </nav>
    </header>

    <!-- Sidebar Overlay -->
    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <!-- Sidebar -->
    <div id="sidebar" class="sidebar">
        <a href="#" class="close-btn" id="closeSidebar">&times;</a>
        <a href="/discover/history.html">📺 My List</a>
    <a href="../profile/profile.php">👤 Profile</a>
    <a href="../connect/connect.php">💬 Discussion</a>
<a href="../index.php" class="nav-link">Logout</a>
<a href="/drive/index.php" class="nav-link">admin</a>
    </div>















    







    <!-- Main Content -->
    <main class="main-content">
        <div class="welcome-section">
            <h1 class="welcome-title">Welcome to CinemaApp</h1>
            <p class="welcome-subtitle">Discover, explore, and enjoy the world of cinema</p>
        </div>
        
        <!-- Add your main content here -->
    </main>





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

















    <!-- Bottom Navigation -->
    <nav class="bottom-nav">
        <a href="/connect/connect.php">💬 Discussion</a>
        <a href="/discover/index.php">🔍 Discover</a>
        <a href="/seasonal/index.php">🎄 Seasonal</a>
    </nav>
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
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const avatarBtn = document.getElementById('avatarBtn');
        const closeSidebar = document.getElementById('closeSidebar');

        function openSidebar() {
            sidebar.classList.add('open');
            sidebarOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebarFunc() {
            sidebar.classList.remove('open');
            sidebarOverlay.classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // Event listeners
        avatarBtn.addEventListener('click', openSidebar);
        closeSidebar.addEventListener('click', closeSidebarFunc);
        sidebarOverlay.addEventListener('click', closeSidebarFunc);

        // Close sidebar on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeSidebarFunc();
            }
        });

        // Smooth scrolling for internal links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>

</html>