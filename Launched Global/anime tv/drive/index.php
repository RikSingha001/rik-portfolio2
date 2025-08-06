<!-- file name-index.php -->
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">

  <title>Movie Upload Form</title>
  <style>
  body {
    font-family: Arial, sans-serif;
    background: #111;
    color: #fff;
    display: flex;
    justify-content: center;
    padding: 40px;
  }

  form {
    background: #222;
    padding: 30px;
    border-radius: 10px;
    width: 400px;
  }

  input,
  textarea {
    width: 100%;
    margin-bottom: 15px;
    padding: 10px;
    border: none;
    border-radius: 5px;
  }

  input[type="submit"] {
    background: #28a745;
    color: white;
    cursor: pointer;
    font-weight: bold;
  }

  h2 {
    text-align: center;
  }
  </style>
</head>

<body>

  <form action="upload.php" method="post" enctype="multipart/form-data">
    <h2>🎬 Upload Movie Content</h2>

    <label>Movie Title:</label>
    <input type="text" name="title" required>

    <label>Description:</label>
    <textarea name="description" rows="4" required></textarea>

    <label>Upload Full Movie (mp4):</label>
    <input type="file" name="movie" accept="video/mp4" required>

    <label>Upload Trailer (mp4):</label>
    <input type="file" name="trailer" accept="video/mp4" required>

    <label>Upload Poster Image (jpg/png):</label>
    <input type="file" name="poster" accept="image/jpeg, image/png" required>

    <input type="submit" value="Upload to Google Drive">
  </form>
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
</body>

</html>