<?php
require_once '../includes/auth_check.php';
require_once '../config/database.php';

// Inisialisasi keranjang belanja di session jika belum ada
if (!isset($_SESSION['keranjang'])) {$_SESSION['keranjang'] = [];
}

// 1. PROSES TAMBAH BARANG KE KERANJANG
if (isset($_POST['tambah_keranjang'])) {
    $produk_id =$_POST['produk_id'];
    $qty = (int)$_POST['qty'];

    $stmt =$pdo->prepare("SELECT * FROM produk WHERE id = ?");
    $stmt->execute([$produk_id]);
    $produk =$stmt->fetch(PDO::FETCH_ASSOC);

    if ($produk && $qty > 0) {$qty_di_keranjang = isset($_SESSION['keranjang'][$produk_id]) ? $_SESSION['keranjang'][$produk_id]['qty'] : 0;

        if (($qty + $qty_di_keranjang) >$produk['stok']) {
            echo "<script>alert('Stok tidak mencukupi! Sisa stok: " . $produk['stok'] . "');</script>";
        } else {
            if (isset($_SESSION['keranjang'][$produk_id])) {$_SESSION['keranjang'][$produk_id]['qty'] +=$qty;
                $_SESSION['keranjang'][$produk_id]['subtotal'] =$_SESSION['keranjang'][$produk_id]['qty'] *$produk['harga'];
            } else {
                $_SESSION['keranjang'][$produk_id] = [
                    'produk_id'       => $produk['id'],
                    'nama'            => $produk['produk'],
                    'harga_transaksi' => $produk['harga'],
                    'qty'             => $qty,
                    'subtotal'        => $produk['harga'] *$qty
                ];
            }
        }
    }
}

// 2. PROSES HAPUS ITEM DARI KERANJANG
if (isset($_GET['hapus'])) {
    $id_hapus =$_GET['hapus'];
    unset($_SESSION['keranjang'][$id_hapus]);
    header("Location: transaksi.php");
    exit();
}

// 3. PROSES CHECKOUT / SIMPAN TRANSAKSI KE DATABASE
if (isset($_POST['checkout'])) {
    if (empty($_SESSION['keranjang'])) {
        echo "<script>alert('Keranjang masih kosong!');</script>";
    } else {
        try {
            $pdo->beginTransaction();

            $total_jumlah = 0;
            foreach ($_SESSION['keranjang'] as$item) {
                $total_jumlah +=$item['subtotal'];
            }

            // A. Simpan ke tabel 'transaksi' (menggunakan kolom total_jumlah)
            $stmt_trx =$pdo->prepare("INSERT INTO transaksi (user_id, total_jumlah) VALUES (?, ?)");
            $stmt_trx->execute([$_SESSION['user_id'],$total_jumlah]);
            $transaksi_id =$pdo->lastInsertId();

            // B. Simpan ke 'transaksi_detail' (menggunakan qty & harga_transaksi) & kurangi stok
            $stmt_detail =$pdo->prepare("INSERT INTO transaksi_detail (transaksi_id, produk_id, qty, harga_transaksi, subtotal) VALUES (?, ?, ?, ?, ?)");
            $stmt_stok =$pdo->prepare("UPDATE produk SET stok = stok - ? WHERE id = ?");

            foreach ($_SESSION['keranjang'] as$item) {
                $stmt_detail->execute([$transaksi_id, 
                    $item['produk_id'],$item['qty'], 
                    $item['harga_transaksi'],$item['subtotal']
                ]);
                $stmt_stok->execute([$item['qty'],$item['produk_id']]);
            }

            $pdo->commit();$_SESSION['keranjang'] = [];

            echo "<script>
                    alert('Transaksi berhasil disimpan!');
                    window.location.href = 'riwayat.php?detail_id=$transaksi_id';
                  </script>";
        } catch (Exception $e) {$pdo->rollBack();
            echo "<script>alert('Transaksi gagal: " . addslashes($e->getMessage()) . "');</script>";
        }
    }
}

// Ambil daftar produk yang stoknya masih ada
$stmt_produk =$pdo->query("SELECT * FROM produk WHERE stok > 0 ORDER BY produk ASC");
$daftar_produk =$stmt_produk->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir - Transaksi Baru</title>
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
            <a href="transaksi.php" class="active">Kasir</a>
            <a href="riwayat.php">Riwayat</a>
            <a href="../auth/logout.php" style="color: var(--danger);">Logout</a>
        </div>
    </nav>

    <div class="pos-workspace">
        <!-- Kolom Kiri: Pilih Produk -->
        <div class="pos-col pos-entry">
            <h3>Pilih Barang</h3>
            <form action="" method="POST" class="pos-card-form">
                <label>Pilih Produk:</label>
                <select name="produk_id" required>
                    <option value="">-- Pilih Barang --</option>
                    <?php foreach ($daftar_produk as $p): ?>
                        <option value="<?= $p['id']; ?>">
                            <?= htmlspecialchars($p['produk']); ?> - Rp <?= number_format($p['harga'], 0, ',', '.'); ?> (Stok: <?= $p['stok']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Jumlah Beli (Qty):</label>
                <input type="number" name="qty" min="1" value="1" required>

                <button type="submit" name="tambah_keranjang" style="width: 100%;">+ Tambah ke Keranjang</button>
            </form>
        </div>

        <!-- Kolom Kanan: Keranjang & Billing -->
        <div class="pos-col pos-bill">
            <h3>Keranjang & Pembayaran</h3>
            <div class="pos-bill-card">
                <table border="1" cellpadding="10" cellspacing="0" class="pos-bill-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $grand_total = 0;
                        if (!empty($_SESSION['keranjang'])): 
                            foreach ($_SESSION['keranjang'] as $item): 
                                $grand_total += $item['subtotal'];
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($item['nama']); ?></td>
                                <td>Rp <?= number_format($item['harga_transaksi'], 0, ',', '.'); ?></td>
                                <td><?= $item['qty']; ?></td>
                                <td>Rp <?= number_format($item['subtotal'], 0, ',', '.'); ?></td>
                                <td><a href="transaksi.php?hapus=<?= $item['produk_id']; ?>">Batal</a></td>
                            </tr>
                        <?php 
                            endforeach; 
                        else: 
                        ?>
                            <tr>
                                <td colspan="5" align="center" style="color: var(--text-light); padding: 24px;">Keranjang masih kosong.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <?php if (!empty($_SESSION['keranjang'])): ?>
                    <div class="pos-total-display">
                        <span class="pos-total-label">TOTAL BAYAR</span>
                        <span class="pos-total-value">Rp <?= number_format($grand_total, 0, ',', '.'); ?></span>
                    </div>

                    <form action="" method="POST" class="pos-pay-form">
                        <label><strong>Uang Bayar (Rp):</strong></label>
                        <input type="number" name="uang_bayar" min="<?= $grand_total; ?>" placeholder="Masukkan nominal..." required>

                        <div class="pos-quick-cash">
                            <button type="button" class="btn-cash" data-val="<?= $grand_total; ?>">Uang Pas</button>
                            <button type="button" class="btn-cash" data-val="50000">Rp 50.000</button>
                            <button type="button" class="btn-cash" data-val="100000">Rp 100.000</button>
                        </div>

                        <button type="submit" name="checkout" onclick="return confirm('Proses pembayaran sekarang?');" class="btn-checkout">
                            Proses Bayar & Simpan Transaksi
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>