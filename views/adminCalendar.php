<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Riwayat Transaksi - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="views/styles/tanggungan.css">
</head>

<body class="d-flex flex-column">
  <?php include_once "header.php"; ?>
  <div class="container-fluid py-5 flex-grow-1 px-5">
    <h2 class="fw-bold mb-4">Riwayat Transaksi <small class="text-muted">(Admin View)</small></h2>

    <div class="d-flex justify-content-start mb-3">
        <button type="button" class="btn btn-warning" onclick="resetAllTransaksi()">Hapus Semua Transaksi</button>
    </div>
    
    <form id="transactionForm">
        <div class="table-responsive mt-4">
            <table class="table table-bordered align-middle bg-white">
              <thead>
                <tr>
                  <th scope="col" class="text-center" style="width: 5%;">Aksi</th>
                  <th scope="col" style="width: 12%;">Tanggal</th>
                  <th scope="col" style="width: 10%;">Pengguna</th>
                  <th scope="col" style="width: 15%;">Kategori</th>
                  <th scope="col" style="width: 23%;">Keterangan</th>
                  <th scope="col" class="" style="width: 15%;">Jumlah</th>
                </tr>
              </thead>

              <tbody id="tabelDaftarTransaksi">
                <?php if (!empty($transactions)): ?>
                    <?php foreach ($transactions as $t): ?>
                        <?php
                            $transaksiId = $t['transaksi_id'];
                            $tanggal = date('Y-m-d', strtotime($t['tanggal_transaksi']));
                        ?>
                        <tr data-transaction-id="<?= $transaksiId ?>">
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-danger w-100" onclick="hapusTransaksi(this)">Hapus</button>
                            </td>
                            <td><input type="date" name="tanggal_transaksi_<?= $transaksiId ?>" class="form-control form-control-sm" value="<?= $tanggal ?>"></td>
                            
                            <td>
                                <select name="user_id_<?= $transaksiId ?>" class="form-select form-select-sm">
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= htmlspecialchars($user['user_id']) ?>" <?= ($user['user_id'] == ($t['user_id'] ?? '')) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($user['username']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>

                            <td>
                                <select name="kategori_id_<?= $transaksiId ?>" class="form-select form-select-sm">
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= htmlspecialchars($category['kategori_id']) ?>" <?= ($category['kategori_id'] == ($t['kategori_id'] ?? '')) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($category['kategori']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>

                            <td><input type="text" name="keterangan_<?= $transaksiId ?>" class="form-control form-control-sm" value="<?= htmlspecialchars($t['keterangan']) ?>"></td>
                            <td><input type="number" step="any" name="jumlah_<?= $transaksiId ?>" class="form-control form-control-sm" value="<?= htmlspecialchars($t['jumlah']) ?>"></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            Belum ada transaksi yang tercatat dari pengguna manapun.
                        </td>
                    </tr>
                <?php endif; ?>
              </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-3">
            <button type="submit" class="btn btn-success" id="simpanSemuaBtn">Simpan Perubahan Data</button>
        </div>
    </form>
  </div>

  <?php include_once "footer.php"; ?>
  <script>
    // Fungsi hapus per baris
    async function hapusTransaksi(buttonElement) {
        if (!confirm('Anda yakin ingin menghapus transaksi ini?')) return;
        const rowElement = buttonElement.closest('tr');
        const transactionId = rowElement.dataset.transactionId;
        try {
            const formData = new URLSearchParams();
            formData.append('id', transactionId);
            const response = await fetch(`?c=AdminCalendarController&m=delete`, {
                method: 'POST', body: formData, headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            });
            const result = await response.json();
            alert(result.info);
            if (response.ok && result.isSuccess) window.location.reload();
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat berkomunikasi dengan server.');
        }
    }

    // Fungsi reset semua transaksi
    async function resetAllTransaksi() {
        if (!confirm('PERINGATAN! Anda akan menghapus SEMUA data transaksi. Tindakan ini tidak dapat dibatalkan. Lanjutkan?')) return;
        try {
            const response = await fetch('?c=AdminCalendarController&m=resetAllTransactions', { method: 'POST' });
            if (response.ok || response.redirected) window.location.reload();
            else alert('Gagal mereset transaksi.');
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat berkomunikasi dengan server untuk reset.');
        }
    }

    // Fungsi untuk simpan semua perubahan
    document.getElementById('transactionForm').addEventListener('submit', async function(event) {
        event.preventDefault();
        const rows = document.querySelectorAll('#tabelDaftarTransaksi tr');
        let updates = [];

        rows.forEach(row => {
            const transactionId = row.dataset.transactionId;
            if (transactionId) {
                updates.push({
                    transaksi_id: transactionId,
                    tanggal_transaksi: row.querySelector(`[name="tanggal_transaksi_${transactionId}"]`).value,
                    user_id: row.querySelector(`[name="user_id_${transactionId}"]`).value,
                    kategori_id: row.querySelector(`[name="kategori_id_${transactionId}"]`).value,
                    keterangan: row.querySelector(`[name="keterangan_${transactionId}"]`).value,
                    jumlah: row.querySelector(`[name="jumlah_${transactionId}"]`).value
                });
            }
        });

        let successCount = 0;
        let errorMessages = [];

        for (const data of updates) {
            const formData = new URLSearchParams();
            for (const key in data) formData.append(key, data[key]);

            try {
                const response = await fetch('?c=AdminCalendarController&m=update', {
                    method: 'POST', body: formData
                });
                const result = await response.json();
                if (response.ok && result.isSuccess) {
                    successCount++;
                } else {
                    errorMessages.push(`Gagal update ID ${data.transaksi_id}: ${result.info || 'Error tidak diketahui'}`);
                }
            } catch (error) {
                errorMessages.push(`Kesalahan jaringan saat update ID ${data.transaksi_id}.`);
            }
        }

        if (errorMessages.length > 0) {
            alert(`Selesai dengan ${successCount} sukses dan beberapa kesalahan:\n${errorMessages.join('\n')}`);
        } else if (successCount > 0) {
            alert('Semua perubahan berhasil disimpan!');
        } else {
            alert('Tidak ada perubahan untuk disimpan atau semua update gagal.');
        }
        window.location.reload();
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>