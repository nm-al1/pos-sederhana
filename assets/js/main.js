// === POS SEDERHANA NATIVE UTILITIES ===
document.addEventListener('DOMContentLoaded', () => {
  // 1. Live Digital Clock in Topbar
  const clockEl = document.getElementById('pos-clock');
  if (clockEl) {
    const updateClock = () => {
      const now = new Date();
      clockEl.textContent = now.toLocaleDateString('id-ID', {
        weekday: 'short',
        day: 'numeric',
        month: 'short'
      }) + ' ' + now.toLocaleTimeString('id-ID');
    };
    updateClock();
    setInterval(updateClock, 1000);
  }

  // 2. Live Change Calculator & Quick Cash Buttons (admin/transaksi.php)
  const uangBayarInput = document.querySelector('input[name="uang_bayar"]');
  if (uangBayarInput) {
    const minBayar = parseInt(uangBayarInput.getAttribute('min') || '0', 10);
    const infoBox = document.createElement('div');
    infoBox.className = 'pos-change-box pos-change-kurang';
    infoBox.textContent = 'Masukkan nominal pembayaran.';
    uangBayarInput.parentNode.insertBefore(infoBox, uangBayarInput.nextSibling);

    const updateKembalian = () => {
      const bayar = parseInt(uangBayarInput.value || '0', 10);
      if (bayar <= 0) {
        infoBox.className = 'pos-change-box pos-change-kurang';
        infoBox.textContent = 'Masukkan nominal pembayaran.';
      } else if (bayar < minBayar) {
        const kurang = minBayar - bayar;
        infoBox.className = 'pos-change-box pos-change-kurang';
        infoBox.textContent = 'Uang kurang: Rp ' + kurang.toLocaleString('id-ID');
      } else {
        const kembali = bayar - minBayar;
        infoBox.className = 'pos-change-box pos-change-ok';
        infoBox.textContent = 'Kembalian: Rp ' + kembali.toLocaleString('id-ID');
      }
    };

    uangBayarInput.addEventListener('input', updateKembalian);

    // Quick cash buttons
    const quickCashBtns = document.querySelectorAll('.btn-cash');
    quickCashBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        const val = parseInt(btn.getAttribute('data-val'), 10);
        if (val) {
          uangBayarInput.value = val;
          updateKembalian();
          uangBayarInput.focus();
        }
      });
    });
  }

  // 3. Table Client-Side Filter (produk_tampil.php & riwayat.php)
  const tables = document.querySelectorAll('table:not(.pos-bill-table)');
  tables.forEach(table => {
    const tbody = table.querySelector('tbody');
    const rows = tbody ? tbody.querySelectorAll('tr') : [];
    if (rows.length > 3) {
      const searchInput = document.createElement('input');
      searchInput.type = 'text';
      searchInput.placeholder = 'Cari data...';
      searchInput.className = 'table-search-input';

      searchInput.addEventListener('input', (e) => {
        const term = e.target.value.toLowerCase();
        rows.forEach(row => {
          const text = row.textContent.toLowerCase();
          row.style.display = text.includes(term) ? '' : 'none';
        });
      });

      table.parentNode.insertBefore(searchInput, table);
    }
  });

  // 4. Print Receipt Button in riwayat.php
  const notaBox = document.querySelector('div[style*="border: 2px solid"], div[style*="border: 1px"]');
  if (notaBox) {
    const printBtn = document.createElement('button');
    printBtn.type = 'button';
    printBtn.textContent = 'Cetak Nota';
    printBtn.style.marginLeft = '10px';
    printBtn.style.backgroundColor = '#475569';
    printBtn.addEventListener('click', () => window.print());

    const tutupLink = notaBox.querySelector('a[href="riwayat.php"]');
    if (tutupLink) {
      tutupLink.parentNode.insertBefore(printBtn, tutupLink.nextSibling);
    }
  }
});
