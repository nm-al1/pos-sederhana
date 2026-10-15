<?php
require_once '../includes/auth_check.php';
require_once '../config/database.php';

$stmt = $pdo->query("SELECT * FROM produk ORDER BY id DESC");
$semua_produk = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Produk</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <script src="../assets/js/main.js" defer></script>
</head>
<body>
    <nav class="pos-topbar">
        <div class="pos-brand">
            <strong>POS KASIR</strong>
            <span id="pos-clock" class="pos-clock"></span>
        </div>
        <div class="pos-links">
            <a href="dasbord.php">Dashboard</a>
            <a href="produk_tampil.php" class="active">Master Produk</a>
            <a href="transaksi.php">Kasir</a>
            <a href="riwayat.php">Riwayat</a>
            <a href="../auth/logout.php" style="color: var(--danger);">Logout</a>
        </div>
    </nav>

    <h2>Daftar Master Produk</h2>
    <a href="dasbord.php">Kembali ke Dashboard</a>
    <a href="produk_tambah.php" style="background-color: var(--primary); color: #fff; margin-left: 8px;">+ Tambah Produk Baru</a>
    <br><br>

    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Produk</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($semua_produk) > 0): ?>
                <?php foreach ($semua_produk as $item): ?>
                <tr>
                    <td><?= $item['id']; ?></td>
                    <td><?= htmlspecialchars($item['produk']); ?></td>
                    <td>Rp <?= number_format($item['harga'], 0, ',', '.'); ?></td>
                    <td><?= $item['stok']; ?></td>
                    <td>
                        <a href="produk_edit.php?id=<?= $item['id']; ?>">Edit</a> | 
                        <a href="produk_hapus.php?id=<?= $item['id']; ?>" onclick="return confirm('Yakin ingin menghapus produk ini?');">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" align="center">Belum ada data produk.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>