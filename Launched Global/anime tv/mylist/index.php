<!-- file_path = 'anime-cop(2)/mylist/mylist.json'; -->

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
  <title>My List</title>
  <link rel="stylesheet" href="mylist.css">
<style>
    h1, h2 {
      text-align: center;
    }
    .movie-section {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      margin: 20px;
    }
    .card {
      width: 200px;
      margin: 10px;
      text-align: center;
      border: 2px solid #ccc;
      border-radius: 8px;
      padding: 10px;
      text-decoration: none;
      color: black;
    }
    img {
      width: 100%;
      border-radius: 5px;
    }
  </style>
</head>
<body>



<div class="nav-links">
  <a href ="profile.php"class="nav-link">profile</a>
  <a href ="/mylist/status.html"class="nav-link">previous</a>
  <a href="../index.php" class="nav-link">Logout</a>
</div>


<div class="bottom-nav">
  <a href="/pega/index.php">Home</a>
  <a href="/connect/connect.php">Discussion</a>
  <a href="/discover/index.php">Discover</a>
  <a href="/seasonal/index.php">Seasonal</a>
</div>
</body>

</html>