<?php
session_start();

$host = 'localhost';
$db   = 'proje'; 
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $login_error = "E-posta veya şifre hatalı."; 

    if (empty($email) || empty($password)) {
        echo "<h2 style='color: red;'>Hata: Tüm alanları doldurunuz.</h2>";
        header("Location: ../login.html?error=empty");
        exit();
    }

    $stmt = $pdo->prepare("SELECT id, password, username, email, name, surname FROM users WHERE email = :email");    
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(); 

    if (!$user) {
        echo "<h2 style='color: red;'>Hata: " . $login_error . "</h2>";
        header("Location: ../login.html?error=failed");
        exit();
    }


    
    if (password_verify($password, $user['password'])) {
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['name'] = $user['name']; 
        $_SESSION['surname'] = $user['surname'];
        $_SESSION['is_logged_in'] = true;

        header("Location: mainpage.php");        
        exit();
        
    } else {
        
        echo "<h2 style='color: red;'>Hata: " . $login_error . "</h2>";
        header("Location: ../login.html?error=failed");
        exit();
    }

} else {

    header("Location: ../login.html");
    exit();
}
?>