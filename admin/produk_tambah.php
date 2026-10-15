<?php
require_once '../includes/auth_check.php';
require_once '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_produk = trim($_POST['produk']);
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    if ($harga < 0 || $stok < 0) {
        echo "<script>alert('Harga dan stok tidak boleh bernilai negatif!');</script>";
    } else {
        $stmt = $pdo->prepare("INSERT INTO produk (produk, harga, stok) VALUES (?, ?, ?)");
        $stmt->execute([$nama_produk, $harga, $stok]);

        echo "<script>
                alert('Produk berhasil ditambahkan!');
                window.location.href = 'produk_tampil.php';
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <script src="../assets/js/main.js" defer></script>
</head>
<body>
    <h2>Tambah Produk Baru</h2>
    <form action="" method="POST">
        <label>Nama Produk:</label><br>
        <input type="text" name="produk" required><br><br>

        <label>Harga (Rp):</label><br>
        <input type="number" name="harga" min="0" required><br><br>

        <label>Stok Awal:</label><br>
        <input type="number" name="stok" min="0" required><br><br>

        <button type="submit">Simpan Produk</button>
        <a href="produk_tampil.php">Batal</a>
    </form>
</body>
</html>