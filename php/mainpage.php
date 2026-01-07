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

    //login chechk
    if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: ../login.html"); 
    exit();
    }
    $username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Misafir';

    //logout butonu
    if(isset($_POST['logout-button'])){
      session_destroy();
      header("Location: ../login.html"); 
    }
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['summary_file'])){
      $hedefKlasor = "../yuklenenler/";
      $dosyaAdi = time() . "_" . basename($_FILES['summary_file']['name']);
      $tamYol = $hedefKlasor . $dosyaAdi;

      if(move_uploaded_file($_FILES['summary_file']['tmp_name'],$tamYol)){
          $sql = "INSERT INTO books(user_id, name, author, bookpage,category,summary_file)
                  VALUES (:uid,:kitap,:yazar,:sayfa,:kat,:dosya)";

          $sorgu = $pdo->prepare($sql);
          $sonuc = $sorgu->execute([
              ":uid"   => $_SESSION["user_id"], 
              ":kitap" => $_POST["book-name"],
              ":yazar" => $_POST["author-name"],
              ":sayfa" => $_POST["page-number"],
              ":kat"   => $_POST["category-name"],
              ":dosya" => $tamYol 
          ]);
          if ($sonuc) {
           echo "<script>alert('Kitap makaleniz başarıyla eklendi!');</script>";
          }else{
           echo "<script>alert('kitap makaleniz eklenemedi!');</script>";
          }
      }
    }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <link rel="stylesheet" href="../mainpage.css" />
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
          <button type="submit" name="flow-button" class="flow-button">Akışa git</button>
        </form>
      </div>
    </div>
    <div class="main-content">
      <div class="box">
        <div class="left-box">
          <div class="current-box">
            <div class="current-title">En Son Okuduğun</div>
            <div class="book-photo">kitap fotosu buraya</div>
            <div class="book-name">kitap ismi</div>
          </div>
          <div class="stats">
            <div class="stats-title">Okuma Oranların</div>
            <div class="stats-text">Bu ay x kadar okudun</div>
          </div>
        </div>
        <div class="right-box">
           <div class="right-box-title">kitap makalesi ekle</div>
           <div class="right-box-content">
              <form method="POST" action="" enctype="multipart/form-data">
                <div class="name-entry">
                  <div>kitap ismi</div>
                  <input name="book-name" type="text">
                </div>
                <div class="author-entry">
                  <div>yazar ismi</div>
                  <input name="author-name" type="text">
                </div>
                <div class="page-entry">
                  <div>sayfa sayısı</div>
                  <input name="page-number" type="number">
                </div>
                <div class="category-entry">
                  <div>kategori</div>
                  <input name="category-name" type="text">
                </div>
                
                <div class="file-upload">
                  <div>Özet Belgesi</div>
                    <label for="file-input"  class="custom-file-upload">
                      Dosya Seç
                    </label>
                    <input id="file-input" name="summary_file" type="file"  class="file-upload-input">
                  </div>
                <button class="entry-button" id="entry-button">Kaydet</button>
              </form>
           </div>
        </div>
      </div>
    </div>
  </body>
</html>