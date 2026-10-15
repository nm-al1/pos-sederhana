<?php
require_once '../includes/auth_check.php';
require_once '../config/database.php';

if (!isset($_GET['id'])) {
    header("Location: produk_tampil.php");
    exit();
}

$id = $_GET['id'];

// Ambil data produk saat ini
$stmt = $pdo->prepare("SELECT * FROM produk WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    echo "<script>
            alert('Produk tidak ditemukan!');
            window.location.href = 'produk_tampil.php';
          </script>";
    exit();
}

// Proses simpan perubahan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_produk = trim($_POST['produk']);
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    if ($harga < 0 || $stok < 0) {
        echo "<script>alert('Harga dan stok tidak boleh bernilai negatif!');</script>";
    } else {
        $stmt_update = $pdo->prepare("UPDATE produk SET produk = ?, harga = ?, stok = ? WHERE id = ?");
        $stmt_update->execute([$nama_produk, $harga, $stok, $id]);

        echo "<script>
                alert('Data produk berhasil diperbarui!');
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
    <title>Edit Produk</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <script src="../assets/js/main.js" defer></script>
</head>
<body>
    <h2>Edit Produk</h2>
    <form action="" method="POST">
        <label>Nama Produk:</label><br>
        <input type="text" name="produk" value="<?= htmlspecialchars($item['produk']); ?>" required><br><br>

        <label>Harga (Rp):</label><br>
        <input type="number" name="harga" min="0" value="<?= $item['harga']; ?>" required><br><br>

        <label>Stok:</label><br>
        <input type="number" name="stok" min="0" value="<?= $item['stok']; ?>" required><br><br>

        <button type="submit">Update Produk</button>
        <a href="produk_tampil.php">Batal</a>
    </form>
</body>
</html>