<!-- file_path = 'anime-copy(2)'; -->
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="1st.css">
</head>

<body>
  <header class="header">
    <a href="#" class="logo">🎬 CinemaApp</a>
    <div class="header-actions">
      <a href="/connect/connect.php" class="login-btn">Login</a>
            <a href="admins/login.php" class="login-btn">Admin / Login</a>

    </div>
  </header>

  <!-- Floating Background Animation -->
  <div class="floating-shapes">
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
  </div>

  <!-- Movie Theme Decorations -->
  <div class="movie-reel"></div>
  <div class="spotlight"></div>

  <!-- Main Content -->
  <main>
    
    <div class="hero-content">
      <h1>Welcome to CinemaApp</h1>
      <p>Your ultimate destination for discovering, watching, and discussing the latest movies and TV shows. Join millions of movie enthusiasts worldwide.</p>
    </div>
 <div class="register-section">
      <p class="register-text">Ready to start your cinematic journey?</p>
      <a href="register_page.php" class="register-btn">Create Your Account</a>
                  <a href="admins/register.php" class="register-btn">Admin </a>

    </div>
    <!-- Features Section -->
    <div class="features">
      <div class="feature">
        <span class="feature-icon">🎥</span>
        <h3>Discover</h3>
        <p>Explore thousands of movies and TV shows with our advanced recommendation system.</p>
      </div>
      <div class="feature">
        <span class="feature-icon">📺</span>
        <h3>Watch</h3>
        <p>Stream your favorite content in high quality with our premium viewing experience.</p>
      </div>
      <div class="feature">
        <span class="feature-icon">💬</span>
        <h3>Discuss</h3>
        <p>Connect with fellow movie lovers and share your thoughts in our community forums.</p>
      </div>
    </div>

    <!-- Register Section -->
   
  </main>

      <div id="supportWidget" style="position:fixed;bottom:18px;right:18px;background:#bd5fff;color:#fff;padding:15px 20px;border-radius:12px;box-shadow:0 4px 10px rgba(0,0,0,0.3);font-family:sans-serif;cursor:pointer;z-index:9999;">💜 Contact Me</div><div id="contactBox" style="display:none;position:fixed;bottom:80px;right:18px;background:#fff;color:#333;padding:20px;width:250px;border-radius:10px;box-shadow:0 4px 10px rgba(0,0,0,0.2);z-index:9999;"><div id="closeBtn" style="position:absolute;top:5px;right:10px;cursor:pointer;font-weight:bold;font-size:18px;">&times;</div><h3 style="margin-top:0;font-family:sans-serif;">Contact Me</h3><form action="https://formspree.io/f/mrblbawn" method="POST"><input type="text" name="name" placeholder="Your name" required style="width:100%;padding:8px;margin-top:8px;border-radius:5px;border:1px solid #ccc;font-family:sans-serif;"><input type="email" name="email" placeholder="Your email" required style="width:100%;padding:8px;margin-top:8px;border-radius:5px;border:1px solid #ccc;font-family:sans-serif;"><textarea name="message" placeholder="Your message" rows="3" required style="width:100%;padding:8px;margin-top:8px;border-radius:5px;border:1px solid #ccc;font-family:sans-serif;"></textarea><button type="submit" style="margin-top:10px;background:#bd5fff;color:#fff;padding:8px;border:none;border-radius:5px;cursor:pointer;width:100%;">Send</button></form></div><script>document.getElementById("supportWidget").onclick=function(){const b=document.getElementById("contactBox");b.style.display=b.style.display==="block"?"none":"block"};document.getElementById("closeBtn").onclick=function(){document.getElementById("contactBox").style.display="none";};</script>


        <footer>
    © 2025 Rik Singha | Designed with 💜 | <a href="mailto:riksingha420@gmail.com" style="color:white;">Contact Me</a>
        <h4>
      Hello, I am Rik Singha. I have built this app as part of a project assigned by Launched Global.
      This is a fully functional real-world app and can be used as both a web platform and a web form.
      It has two separate admin panels — one for managing chats and another for uploading movies.<br><br>

      The movie upload admin panel is not fully complete yet, due to some critical issues I faced with my Firebase Cloud setup.<br><br>

      You’ll find a link to my GitHub profile
      <a href="https://github.com/riksingha001" target="_blank">here</a>, and each page includes a "Contact Me" option —
      if you send an email through that, I’ll receive it directly.
      I’ve also included my
      <a href="https://www.linkedin.com/in/rik-singha-90b316301" target="_blank">LinkedIn profile</a>.<br><br>

      Most importantly, I want to extend my heartfelt thanks to Launched Global for giving me this project.
      Through this project, I’ve truly found myself. I’ve used Tailwind CSS extensively, and although I reused
      about 50% of the chat app from an existing source due to time constraints, the rest of the project is
      fully developed by me.<br><br>

      Once again, sincere thanks!
    </h4>
  </footer>
  <style>
 body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f9f9f9;
      color: #333;
    }

    .container {
      max-width: 900px;
      margin: 40px auto;
      padding: 20px;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    h4 {
      line-height: 1.7;
      font-size: 18px;
    }

    a {
      color: #bd5fff;
      text-decoration: none;
    }

    a:hover {
      text-decoration: underline;
    }

    footer {
      background-color: #bd5fff;
      color: white;
      text-align: center;
      padding: 15px 10px;
      position: relative;
      bottom: 0;
      width: 100%;
      font-size: 14px;
    }

    @media (max-width: 600px) {
      .container {
        margin: 20px;
        padding: 15px;
      }

      h3 {
        font-size: 16px;
      }
    }
  </style>
</body>

</html>