<?php
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

    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $surname = isset($_POST['surname']) ? trim($_POST['surname']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $password_repeat = isset($_POST['password_repeat']) ? $_POST['password_repeat'] : '';
    
   
    $errors = [];

    if (empty($username)) $errors[] = "Kullanıcı Adı boş bırakılamaz.";
    if (empty($name)) $errors[] = "İsim boş bırakılamaz.";
    if (empty($surname)) $errors[] = "Soyad boş bırakılamaz.";

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Lütfen geçerli bir e-posta adresi girin.";
        header("Refresh: 3; url= ../register.html");

    }//burada filter_var fonksiyonunu internetten baktım.

    
    if (empty($password) || empty($password_repeat)) {
        $errors[] = "Şifre alanları boş bırakılamaz.";
    } elseif ($password !== $password_repeat) {
        $errors[] = "Şifreler birbiriyle uyuşmuyor.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Şifreniz en az 8 karakter olmalıdır.";
    }

    if (strlen($password) < 8) {
        $errors[] = "Şifreniz en az 8 karakter olmalıdır.";
    }

    
    if (empty($errors)) {
       
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (username, email, password, name, surname) VALUES (:username, :email, :password, :name, :surname)";
        $data = $pdo->prepare($sql);
        
        try {
            $data->execute([
                'username' => $username,
                'email' => $email,
                'password' => $hashed_password,
                'name' => $name,
                'surname' => $surname
            ]);

            
            echo "<h2 style='color: green;'>✅ Kayıt Başarılı!</h2>";
            echo "<p>Artık giriş yapabilirsiniz.</p>";
            header("Location: ../login.html");
            exit();
            
        } catch (\PDOException $e) {
            echo "<h2 style='color: red;'>Kayıt Sırasında Veritabanı Hatası!</h2>";
            echo "<p>Hata: " . $e->getMessage() . "</p>";
        }
        
    } else {
  
        echo "<h2>Kayıt Başarısız Oldu!</h2>";
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li style='color: red;'>$error</li>";
        }
        echo "</ul>";

    }

} else {
    header("Location: ../register.html");
    exit();
}

?>