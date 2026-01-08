<?php 
    session_start();
    
    //db bağlantı
    $host = 'localhost'; $db = 'proje'; $user = 'root'; $pass = ''; $charset = 'utf8mb4';
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    try {
        $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    } catch (\PDOException $e) {
        die("Bağlantı hatası: " . $e->getMessage());
    }

    //login 
    if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: ../login.html"); 
    exit();
    }

    //değişkenleri alma
    $username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Misafir';
    $user_id = $_SESSION['user_id']; 

    //logout butonu
    if(isset($_POST['logout-button'])){
      session_destroy();
      header("Location: ../login.html"); 
    }

    //profil butonu
    if (isset($_POST['profile-button'])) {
    header("Location: mainpage.php");
    exit();
    }
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reader Roll</title>
     <link rel="stylesheet" href="../feed.css" />
</head>
<body>
    <div class="side-bar">
      <div class="user-profile">
        <div class="svg-container">
          <img src="../assets/logo.svg" alt="user-icon" class="sidebar-svg" />
        </div>
        <p><?php echo $username; ?></p>
      </div>
      <div class="side-bar-content">
        <form method="POST" action = "">
          <button type="submit" name="logout-button" class="logout-button">Çıkış Yap</button>
        </form>
        <form method="POST" action="">
          <button type="submit" name="profile-button" class="profile-button">Profile Git</button>
        </form>
      </div>
    </div>
    <div class="main-content">
    <div class="feed-container">
        <div class="post-card">
            <div class="post-header">
                <span class="user-badge">@ahmet_yılmaz</span>
                <span class="category-tag">Roman</span>
            </div>
            <div class="post-body">
                <h2 class="book-title">Körlük</h2>
                <p class="author-name">Jose Saramago</p>
            </div>
            <div class="post-footer">
                <a href="#" class="download-link">Özeti Gör</a>
            </div>
            
        </div>

        <div class="post-card">
            <div class="post-header">
                <span class="user-badge">@elif_okur</span>
                <span class="category-tag">Bilim</span>
            </div>
            <div class="post-body">
                <h2 class="book-title">Sapiens</h2>
                <p class="author-name">Yuval Noah Harari</p>
            </div>
            <div class="post-footer">
                <a href="#" class="download-link">Özeti İndir / Görüntüle</a>
            </div>
        </div>
        
    </div>
</body>
</html>