<?php
require_once '../includes/auth_check.php';
require_once '../config/database.php';

// Ambil semua data transaksi beserta nama kasir/admin dari tabel users
$query = "SELECT transaksi.*, users.username 
          FROM transaksi 
          JOIN users ON transaksi.user_id = users.id 
          ORDER BY transaksi.id DESC";
$stmt = $pdo->query($query);
$riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Jika tombol 'Lihat Detail' diklik
$detail_items = [];
if (isset($_GET['detail_id'])) {
    $detail_id = $_GET['detail_id'];
    $stmt_detail = $pdo->prepare("SELECT transaksi_detail.*, produk.produk 
                                  FROM transaksi_detail 
                                  JOIN produk ON transaksi_detail.produk_id = produk.id 
                                  WHERE transaksi_detail.transaksi_id = ?");
    $stmt_detail->execute([$detail_id]);
    $detail_items = $stmt_detail->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi</title>
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
            <a href="produk_tampil.php">Master Produk</a>
            <a href="transaksi.php">Kasir</a>
            <a href="riwayat.php" class="active">Riwayat</a>
            <a href="../auth/logout.php" style="color: var(--danger);">Logout</a>
        </div>
    </nav>

    <h2>Riwayat Transaksi POS</h2>
    <a href="dasbord.php">Kembali ke Dashboard</a>
    <a href="transaksi.php" style="background-color: var(--primary); color: #fff; margin-left: 8px;">+ Transaksi Baru</a>
    <br><br>

    <!-- Blok Rincian Nota (Muncul jika 'Lihat Detail' diklik) -->
    <?php if (isset($_GET['detail_id']) && count($detail_items) > 0): ?>
        <div style="border: 2px solid #000; padding: 15px; margin-bottom: 20px; width: fit-content;">
            <h3>Rincian Nota #<?= htmlspecialchars($_GET['detail_id']); ?></h3>
            <table border="1" cellpadding="8" cellspacing="0">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Harga Saat Transaksi</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detail_items as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['produk']); ?></td>
                        <td>Rp <?= number_format($d['harga_transaksi'], 0, ',', '.'); ?></td>
                        <td><?= $d['qty']; ?></td>
                        <td>Rp <?= number_format($d['subtotal'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <br>
            <a href="riwayat.php">Tutup Rincian</a>
        </div>
    <?php endif; ?>

    <!-- Tabel Daftar Seluruh Transaksi -->
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>ID Nota</th>
                <th>Tanggal & Waktu</th>
                <th>Kasir</th>
                <th>Total Transaksi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($riwayat) > 0): ?>
                <?php foreach ($riwayat as $r): ?>
                <tr>
                    <td>#<?= $r['id']; ?></td>
                    <td><?= $r['transaksi_date']; ?></td>
                    <td><?= htmlspecialchars($r['username']); ?></td>
                    <td>Rp <?= number_format($r['total_jumlah'], 0, ',', '.'); ?></td>
                    <td>
                        <a href="riwayat.php?detail_id=<?= $r['id']; ?>">Lihat Detail</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" align="center">Belum ada riwayat transaksi.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>