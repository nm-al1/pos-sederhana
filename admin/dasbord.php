<?php
require_once '../includes/auth_check.php';
$role = $_SESSION['role'];

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard POS</title>
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
            <a href="dasbord.php" class="active">Dashboard</a>
            <a href="transaksi.php">Kasir</a>
            <a href="riwayat.php">Riwayat</a>
            <a href="../auth/logout.php" style="color: var(--danger);">Logout</a>
        </div>
    </nav>

    <h2>Selamat Datang di Dashboard POS</h2>
    <p>Anda login sebagai: <span class="pos-badge-role"><?= strtoupper($role); ?></span></p>

    <ul class="pos-menu-grid">
        <li class="pos-menu-item featured">
            <a href="transaksi.php">
                <span class="menu-icon">🛒</span>
                <span class="menu-title">Buka Kasir / Transaksi</span>
                <span class="menu-desc">Mulai input penjualan baru</span>
            </a>
        </li>
        <?php if ($role === 'admin'): ?>
            <li class="pos-menu-item">
                <a href="produk_tampil.php">
                    <span class="menu-icon">📦</span>
                    <span class="menu-title">Kelola Master Produk</span>
                    <span class="menu-desc">Data barang, harga, & stok (CRUD)</span>
                </a>
            </li>
        <?php endif; ?>
        <li class="pos-menu-item">
            <a href="riwayat.php">
                <span class="menu-icon">🧾</span>
                <span class="menu-title">Riwayat Transaksi</span>
                <span class="menu-desc">Lihat seluruh nota & struk kasir</span>
            </a>
        </li>
        <li class="pos-menu-item danger">
            <a href="../auth/logout.php" style="color: var(--danger);">
                <span class="menu-icon">🚪</span>
                <span class="menu-title">Logout</span>
                <span class="menu-desc">Keluar dari sesi sistem</span>
            </a>
        </li>
    </ul>
</body>

</html>