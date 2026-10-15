<?php
require_once '../includes/auth_check.php';
require_once '../config/database.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM produk WHERE id = ?");
        $stmt->execute([$id]);

        echo "<script>
                alert('Produk berhasil dihapus!');
                window.location.href = 'produk_tampil.php';
              </script>";
    } catch (PDOException $e) {
        echo "<script>
                alert('Gagal menghapus! Produk ini sudah tercatat dalam riwayat transaksi.');
                window.location.href = 'produk_tampil.php';
              </script>";
    }
} else {
    header("Location: produk_tampil.php");
    exit();
}
?>