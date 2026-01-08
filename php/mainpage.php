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

    //değişkenleri alma
    $username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Misafir';
    $user_id = $_SESSION['user_id']; 


    //son okunanları çekme
    $kitapSorgu = $pdo->prepare("SELECT name, summary_file FROM books WHERE user_id = :uid ORDER BY id DESC");
    $kitapSorgu->execute([':uid' => $user_id]);
    $kitaplar = $kitapSorgu->fetchAll(PDO::FETCH_ASSOC);

    //logout butonu
    if(isset($_POST['logout-button'])){
      session_destroy();
      header("Location: ../login.html"); 
    }

    //kitap ekleme
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
          <div class="stats">
            <div class="last-reads-box">
                <div class="right-box-title">son okunanlar</div>
                <div class="last-reads-list">
                    
                    <?php if (isset($kitaplar) && count($kitaplar) > 0): ?>
                        <?php foreach ($kitaplar as $kitap): ?>
                            <div class="read-item">
                                <span class="book-item-name">
                                    <?php echo htmlspecialchars($kitap['name']); ?>
                                </span>
                                <a href="<?php echo htmlspecialchars($kitap['summary_file']); ?>" target="_blank" class="view-summary">
                                    Özeti Gör
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="read-item">
                            <span class="book-item-name" style="color: #888;">Henüz kitap eklenmemiş.</span>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
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