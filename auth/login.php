<?php

session_start();
require_once'../config/database.php';

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password_input = $_POST['password'];
    
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user && password_verify($password_input, $user['PASSWORD'])){

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];


        echo "<script>
                alert('login berhasil');
                window.location.href = '../admin/dasbord.php';
              </script>";
    } else{
        echo "<script> alert('Username atau Password salah!'); </script>";
    }
}
?>

    <!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login POS Sederhana</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <script src="../assets/js/main.js" defer></script>
</head>
<body>
    <h2>Login Sistem</h2>
    <form action="" method="POST">
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit" style="width: 100%;">Login</button>
        <p style="margin-top: 16px; font-size: 13px; text-align: center;">Belum punya akun? <a href="register.php">Daftar Akun</a></p>
    </form>
</body>
</html>