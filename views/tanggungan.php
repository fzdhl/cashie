<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Tagihan dan Komitmen</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="views/styles/tanggungan.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>


<body class="d-flex flex-column min-vh-100" style="background-color: #F0F8F3"> 
  <?php include_once "header.php"; ?>
  <div class="container-fluid py-5 flex-grow-1 px-5"> 
  <h2 class="fw-bold mb-4">Tagihan dan Komitmen</h2>

    <!-- kartu dengan keterangan periode saat ini -->
    <div class="row g-3 mb-4">
      <div class="col-md-6 col-lg-3">
        <div class="card p-3 shadow-sm h-100">
          <h6 class="text-muted">Periode</h6>
          <h5><?= date('Y | F') ?></h5>
        </div>
      </div>

      <!-- kartu dengan keterangan jumlah tanggungan aktif -->
      <div class="col-md-6 col-lg-3">
        <div class="card p-3 shadow-sm h-100">
          <h6 class="text-muted">Tanggungan Aktif</h6>
          <h5 id="aktif">Rp. 0</h5>
        </div>
      </div>

      <!-- kartu dengan keterangan jumlah yang sudah dibayarkan -->
      <div class="col-md-6 col-lg-3">
        <div class="card p-3 shadow-sm h-100">
          <h6 class="text-muted">Jumlah Terbayar</h6>
          <h5 id="terbayar">Rp. 0</h5>
        </div>
      </div>

      <!-- kartu dengan keterangan saldo -->
      <div class="col-md-6 col-lg-3">
        <div class="card p-3 shadow-sm h-100">
          <h6 class="text-muted">Saldo</h6>
          <h5>Rp. 0</h5>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-start mb-3">
      <button type="button" class="btn btn-success" onclick="tambahBaris()">+ Tambah Tanggungan Baru</button>
    </div>

    <div class="table-responsive mt-4">
      <table class="table table-bordered align-middle bg-white">
      <thead>
      <tr>
        <th scope="col" class="text-center" style="width: 10%;">Aksi</th>
        <th scope="col" style="width: 25%;">Tanggungan</th>
        <th scope="col" style="width: 15%;">Jadwal Pembayaran</th>
        <th scope="col" style="width: 20%;">Kategori</th>
        <th scope="col" style="width: 15%;">Jumlah</th>
        <th scope="col" style="width: 15%;">Status</th>
      </tr>
    </thead>  

    <tbody id="tabelDaftarTanggungan">
        <?php
        $totalAktif = 0;
        $totalBayar = 0;
        ?>

        <?php if (!empty($tanggungan)): ?>
        <?php foreach ($tanggungan as $t):
            $tanggunganId = isset($t['id']) ? htmlspecialchars($t['id']) : '';
            $tanggunganNama = isset($t['tanggungan']) ? htmlspecialchars($t['tanggungan']) : '';
            $jadwalPembayaran = isset($t['jadwal_pembayaran']) ? htmlspecialchars($t['jadwal_pembayaran']) : '';
            $kategoriId = isset($t['kategori_id']) ? $t['kategori_id'] : '';
            $jumlah = isset($t['jumlah']) ? htmlspecialchars($t['jumlah']) : 0;
            $status = isset($t['status']) ? htmlspecialchars($t['status']) : 'Unknown';
            $permanen = isset($t['permanen']) ? $t['permanen'] : 0;

            $isSelesai = ($status === 'Selesai');
            $inputDisabled = $isSelesai ? 'disabled' : '';

            if ($isSelesai)
                $totalBayar += (int)$jumlah;
            else
                $totalAktif += (int)$jumlah;
            ?>

            <tr>
                <td class="text-center">
                    <div class="d-flex flex-column gap-1">
                        <?php if (!$isSelesai): ?>
                            <form method="post" action="?c=TanggunganController&m=update" class="form-update-tanggungan">
                                <input type="hidden" name="tanggungan_id" value="<?= $tanggunganId ?>">
                                <input type="hidden" name="tanggungan" value="<?= $tanggunganNama ?>">
                                <input type="hidden" name="jadwal_pembayaran" value="<?= $jadwalPembayaran ?>">
                                <input type="hidden" name="kategori_id" value="<?= $kategoriId ?>">
                                <input type="hidden" name="jumlah" value="<?= $jumlah ?>">
                                <button type="submit" class="btn btn-sm btn-primary w-100">Update</button>
                            </form>
                        <?php endif; ?>

                        <?php if (!$permanen && !$isSelesai): ?>
                            <form method="post" action="?c=TanggunganController&m=hapus&id=<?= $tanggunganId ?>" onsubmit="return confirm('Hapus tanggungan ini?')">
                                <button class="btn btn-sm btn-danger w-100">Hapus</button>
                            </form>
                        <?php elseif ($isSelesai): ?>
                            <button class="btn btn-sm btn-secondary w-100" disabled>Selesai</button>
                        <?php endif; ?>
                    </div>
                </td>

                <td><input type="text" name="tanggungan_display" class="form-control form-control-sm" value="<?= $tanggunganNama ?>" <?= $inputDisabled ?>></td>
                <td><input type="date" name="jadwal_pembayaran_display" class="form-control form-control-sm" value="<?= $jadwalPembayaran ?>" <?= $inputDisabled ?>></td>
                <td>
                    <select name="kategori_id_display" class="form-select form-select-sm" <?= $inputDisabled ?>>
                        <?php
                        if (isset($categories) && is_array($categories)) {
                            foreach ($categories as $category) {
                                $catId = isset($category['kategori_id']) ? htmlspecialchars($category['kategori_id']) : '';
                                $catNama = isset($category['kategori']) ? htmlspecialchars($category['kategori']) : '';
                                $selected = ($kategoriId == $catId) ? 'selected' : '';
                                echo '<option value="' . $catId . '" ' . $selected . '>' . $catNama . '</option>';
                            }
                        }
                        ?>
                    </select>
                </td>
                <td><input type="number" name="jumlah_display" class="form-control form-control-sm" value="<?= $jumlah ?>" <?= $inputDisabled ?>></td>
                <td><input type="text" name="status_display" value="<?= $status ?>" class="form-control form-control-sm" disabled></td>
            </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>

      </table>
      </table>
    </div>
  </div>

  <script>
    document.getElementById('aktif').innerText = 'Rp. <?= number_format($totalAktif, 0, ',', '.') ?>';
    document.getElementById('terbayar').innerText = 'Rp. <?= number_format($totalBayar, 0, ',', '.') ?>';

    // mengambil kategori dan meneruskan ke javascript
    const categories = <?= json_encode($categories ?? []) ?>;

    // fungsi untuk menambah baris input baru ke tabel daftar tanggungan
    function tambahBaris() {
        const tbody = document.getElementById('tabelDaftarTanggungan');
        const row = document.createElement('tr');
       
        let categoryOptions = '<option value="">Pilih...</option>';
        categories.forEach(category => {
            categoryOptions += `<option value="${category.kategori_id}">${category.kategori}</option>`;
        });

        // HTML untuk baris baru dengan input fields kosong
        row.innerHTML = `
            <td class="text-center">
                <div class="d-flex flex-column gap-1">
                    <button type="button" class="btn btn-sm btn-success w-100 btn-simpan-baru">Simpan</button>
                    <button type="button" class="btn btn-sm btn-secondary w-100" onclick="hapusBarisDariDOM(this)">&times; Batal</button>
                </div>
            </td>
            <td><input type="text" name="tanggungan" class="form-control form-control-sm" required></td>
            <td><input type="date" name="jadwal_pembayaran" class="form-control form-control-sm" required></td>
            <td>
                <select name="kategori_id" class="form-select form-select-sm" required>
                    ${categoryOptions}
                </select>
            </td>
            <td><input type="number" name="jumlah" class="form-control form-control-sm" required></td>
            <td><input type="text" name="status" value="Belum dibayar" class="form-control form-control-sm" disabled></td>
    `;
    tbody.appendChild(row);

    // Tambahkan event listener untuk tombol "Simpan" yang baru ditambahkan
    const simpanButton = row.querySelector('.btn-simpan-baru');
    simpanButton.addEventListener('click', function() {
        simpanTanggunganBaru(row); // Panggil fungsi baru untuk menyimpan dengan AJAX
        });
    }

    // fungsi baru untuk menghapus baris dari DOM saja (untuk baris baru yang belum disimpan)
    function hapusBarisDariDOM(el) {
        const row = el.closest('tr');
        if (row) row.remove();
    }

    // Fungsi untuk mengirim data tanggungan baru menggunakan AJAX
    async function simpanTanggunganBaru(rowElement) {
        const tanggunganInput = rowElement.querySelector('input[name="tanggungan"]');
        const jadwalPembayaranInput = rowElement.querySelector('input[name="jadwal_pembayaran"]');
        const kategoriIdSelect = rowElement.querySelector('select[name="kategori_id"]');
        const jumlahInput = rowElement.querySelector('input[name="jumlah"]');

        // Validasi sederhana di sisi klien
        if (!tanggunganInput.value || !jadwalPembayaranInput.value || !kategoriIdSelect.value || !jumlahInput.value) {
            alert('Semua kolom wajib diisi!');
            return;
        }

        const data = new URLSearchParams();
        data.append('tanggungan', tanggunganInput.value);
        data.append('jadwal_pembayaran', jadwalPembayaranInput.value);
        data.append('kategori_id', kategoriIdSelect.value);
        data.append('jumlah', jumlahInput.value);

        try {
            const response = await fetch('?c=TanggunganController&m=insert', {
                method: 'POST',
                body: data,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });

            const result = await response.json(); // Asumsi server mengembalikan JSON
            // Jika server tidak mengembalikan JSON, Anda perlu mengubah ini

            if (response.ok && result.isSuccess) {
                alert('Tanggungan berhasil disimpan!');
                // Opsional: Perbarui tampilan tanpa me-refresh seluruh halaman
                // atau refresh halaman untuk kesederhanaan
                window.location.reload(); // Refresh halaman setelah sukses
            } else {
                alert('Gagal menyimpan tanggungan: ' + (result.info || 'Terjadi kesalahan tidak dikenal.'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat berkomunikasi dengan server.');
        }
    }

    // fungsi untuk update data yang sudah ada
    async function updateTanggungan(buttonElement) {
        const rowElement = buttonElement.closest('tr');
        const tanggunganId = rowElement.dataset.tanggunganId; // Ambil ID dari data-tanggungan-id di tr

        const tanggunganInput = rowElement.querySelector('input[name="tanggungan"]');
        const jadwalPembayaranInput = rowElement.querySelector('input[name="jadwal_pembayaran"]');
        const kategoriIdSelect = rowElement.querySelector('select[name="kategori_id"]');
        const jumlahInput = rowElement.querySelector('input[name="jumlah"]');

        if (!tanggunganInput.value || !jadwalPembayaranInput.value || !kategoriIdSelect.value || !jumlahInput.value) {
            alert('Semua kolom wajib diisi untuk update!');
            return;
        }

        const data = new URLSearchParams();
        data.append('tanggungan_id', tanggunganId);
        data.append('tanggungan', tanggunganInput.value);
        data.append('jadwal_pembayaran', jadwalPembayaranInput.value);
        data.append('kategori_id', kategoriIdSelect.value);
        data.append('jumlah', jumlahInput.value);

        try {
            const response = await fetch('?c=TanggunganController&m=update', {
                method: 'POST',
                body: data,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });

            // Periksa apakah respons adalah JSON atau redirect
            if (response.headers.get('content-type')?.includes('application/json')) {
                const result = await response.json();
                if (response.ok && result.isSuccess) {
                    alert('Tanggungan berhasil diperbarui!');
                    window.location.reload(); // Refresh untuk melihat perubahan
                } else {
                    alert('Gagal memperbarui tanggungan: ' + (result.info || 'Terjadi kesalahan tidak dikenal.'));
                }
            } else {
                // Jika PHP melakukan redirect (misal karena error), maka kita tidak akan menerima JSON
                // Ini menandakan ada masalah di sisi server (controller update)
                console.error('Server did not return JSON. Likely a redirect or error.');
                alert('Gagal memperbarui tanggungan. Terjadi kesalahan server.');
                window.location.reload(); // Refresh untuk melihat pesan error dari $_SESSION['pesan_error']
            }

        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat berkomunikasi dengan server untuk update.');
        }
    }

    // untuk menghapus tanggungan yang sudah ada
    async function hapusTanggungan(buttonElement) {
        if (!confirm('Hapus tanggungan ini?')) {
            return;
        }

        const rowElement = buttonElement.closest('tr');
        const tanggunganId = rowElement.dataset.tanggunganId;

        try {
            const response = await fetch(`?c=TanggunganController&m=hapus&id=${tanggunganId}`, {
                method: 'POST' // Menggunakan POST untuk hapus, lebih aman dari GET
            });

            // Periksa apakah respons adalah JSON atau redirect
            if (response.headers.get('content-type')?.includes('application/json')) {
                const result = await response.json();
                if (response.ok && result.isSuccess) {
                    alert('Tanggungan berhasil dihapus!');
                    window.location.reload(); // Refresh untuk melihat perubahan
                } else {
                    alert('Gagal menghapus tanggungan: ' + (result.info || 'Terjadi kesalahan tidak dikenal.'));
                }
            } else {
                console.error('Server did not return JSON. Likely a redirect or error.');
                alert('Gagal menghapus tanggungan. Terjadi kesalahan server.');
                window.location.reload();
            }

        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat berkomunikasi dengan server untuk hapus.');
        }
    }

  </script>

  <?php include_once "footer.php"; ?>
</body>
</div>
</html>