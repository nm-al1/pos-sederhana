<?php

require_once '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password_asli = $_POST['password'];
    $role = $_POST['role'];



$password_hashed = password_hash($password_asli, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (username, password, role) values (?, ?, ?)");

$stmt->execute([$username, $password_hashed, $role]);

echo "<script>
        alert('Registrasi akun $username berhasil!');
        window.location.href = 'login.php';
      </script>";
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Sederhana</title>
</head>

<body>
    <h2>Register</h2>
    <form action="" method="post">
        <label>Username </label><br>
        <input type="text" name="username" placeholder="masukkan username"required><br><br>

        <label>Password </label><br>
        <input type="password" name="password" placeholder="Masukkan Password"required><br><br>

        <label>Role</label><br>
        <select name="role">
            <option value="kasir">Kasir</option>
            <option value="admin">Admin</option>
        </select><br><br>

        <button type="submit">Daftar</button>
    </form>
</body>

</html>