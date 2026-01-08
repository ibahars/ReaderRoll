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

    //tüm verileri çekme
    $sql = "SELECT books.*, users.username, 
            (SELECT COUNT(*) FROM comments WHERE comments.book_id = books.id) as yorum_sayisi
            FROM books 
            INNER JOIN users ON books.user_id = users.id 
            ORDER BY books.id DESC";

    $sorgu = $pdo->prepare($sql);
    $sorgu->execute();
    $tum_postlar = $sorgu->fetchAll(PDO::FETCH_ASSOC);

    // Yorum Gönderme İşlemi
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send-comment'])) {
        $comment_text = trim($_POST['comment_text']);
        $book_id = $_POST['book_id'];

        if (!empty($comment_text)) {
            $sql = "INSERT INTO comments (book_id, user_id, comment) VALUES (:bid, :uid, :txt)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':bid' => $book_id,
                ':uid' => $_SESSION['user_id'],
                ':txt' => $comment_text
            ]);
            // Sayfayı yenileyerek yorumun görünmesini sağla
            header("Location: feed.php");
            exit();
        }
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
            <?php if (count($tum_postlar) > 0): ?>
                <?php foreach ($tum_postlar as $post): ?>
                    <div class="post-card">
                        <div class="post-header">
                            <span class="user-badge">@<?php echo htmlspecialchars($post['username']); ?></span>
                            <span class="category-tag"><?php echo htmlspecialchars($post['category']); ?></span>
                        </div>
                        <div class="post-body">
                            <h2 class="book-title"><?php echo htmlspecialchars($post['name']); ?></h2>
                            <p class="author-name"><?php echo htmlspecialchars($post['author']); ?></p>
                        </div>
                        <div class="post-footer">
                            <a href="<?php echo htmlspecialchars($post['summary_file']); ?>" target="_blank" class="download-link">Özeti Gör</a>
                        </div>

                        <div class="interaction-section">
                            <div class="like-comment-stats">
                                <button class="action-btn like-btn">❤️ Beğen</button>
                                <span class="stats-text">0 Beğeni</span>
                                <span class="stats-text comment-toggle" style="cursor:pointer;" onclick="toggleComments(<?php echo $post['id']; ?>)">
                                    <?php echo $post['yorum_sayisi']; ?> Yorum
                                </span>
                            </div>

                            
                            <div id="comment-list-<?php echo $post['id']; ?>" class="comments-display-area" style="display:none;">
                                <?php
                                $c_sql = "SELECT comments.*, users.username FROM comments 
                                        JOIN users ON comments.user_id = users.id 
                                        WHERE book_id = :bid ORDER BY created_at ASC";
                                $c_stmt = $pdo->prepare($c_sql);
                                $c_stmt->execute([':bid' => $post['id']]);
                                $yorumlar = $c_stmt->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($yorumlar as $yorum): ?>
                                    <div class="single-comment">
                                        <strong>@<?php echo htmlspecialchars($yorum['username']); ?>:</strong>
                                        <span><?php echo htmlspecialchars($yorum['comment']); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <form method="POST" action="" class="comment-input-area">
                                <input type="hidden" name="book_id" value="<?php echo $post['id']; ?>">
                                <input type="text" name="comment_text" placeholder="Yorum ekle..." class="comment-input" required>
                                <button type="submit" name="send-comment" class="send-comment-btn">Paylaş</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="post-card">
                    <p style="text-align:center;">Henüz bir paylaşım yok.</p>
                </div>
            <?php endif; ?>
           
        </div>
    </div>

<script>
function toggleComments(bookId) {
    var commentArea = document.getElementById('comment-list-' + bookId);
    if (commentArea.style.display === "none") {
        commentArea.style.display = "block";
    } else {
        commentArea.style.display = "none";
    }
}
</script>

</body>
</html>