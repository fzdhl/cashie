<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Tagihan dan Komitmen</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="views/styles/tanggungan.css">
</head>

<body class="d-flex flex-column">
  <?php include_once "header.php"; ?>
  <div class="container-fluid py-5 flex-grow-1 px-5">
    <h2 class="fw-bold mb-4">Tagihan dan Komitmen
      <?php if (isset($isAdmin) && $isAdmin): ?>
        <small class="text-muted">(Admin View)</small>
      <?php endif; ?>
    </h2>

    <div class="row g-3 mb-4 justify-content-center"> <div class="col-12 col-sm-6 col-md-4 col-lg-4"> <div class="card p-3 shadow-sm h-100">
          <h6 class="text-muted">Periode</h6>
          <h5><?= date('Y | F') ?></h5>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-md-4 col-lg-4"> <div class="card p-3 shadow-sm h-100">
          <h6 class="text-muted">Tanggungan Aktif</h6>
          <h5 id="aktif">Rp. 0</h5>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-md-4 col-lg-4"> <div class="card p-3 shadow-sm h-100">
          <h6 class="text-muted">Jumlah Terbayar</h6>
          <h5 id="terbayar">Rp. 0</h5>
        </div>
      </div>
      </div>

    <div class="d-flex justify-content-start mb-3">
      <?php if (!isset($isAdmin) || !$isAdmin): ?>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addTanggunganModal">
          + Tambah Tanggungan Baru
        </button>
      <?php endif; ?> 
      <?php if (isset($isAdmin) && $isAdmin): ?>
      <button type="button" class="btn btn-warning ms-2" onclick="resetAllTanggunan()">Reset Semua Tanggungan</button>
      <?php endif; ?>
    </div>

    <form id="tanggunganForm">
      <div class="table-responsive mt-4">
        <table class="table table-bordered align-middle bg-white">
          <thead>
            <tr>
              <th scope="col" class="text-center" style="width: 10%;">Aksi</th>
              <?php if (isset($isAdmin) && $isAdmin): ?>
                <th scope="col" style="width: 10%;">Pemilik</th>
              <?php endif; ?>
              <th scope="col" style="width: 20%;">Tanggungan</th>
              <th scope="col" style="width: 15%;">Jadwal Pembayaran</th>
              <th scope="col" style="width: 15%;">Kategori</th>
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
                $tanggunganId = isset($t['tanggungan_id']) ? htmlspecialchars($t['tanggungan_id']) : '';
                $tanggunganNama = isset($t['tanggungan']) ? htmlspecialchars($t['tanggungan']) : '';
                $jadwalPembayaran = isset($t['jadwal_pembayaran']) ? htmlspecialchars($t['jadwal_pembayaran']) : '';
                $kategoriId = isset($t['kategori_id']) ? $t['kategori_id'] : '';
                $jumlah = isset($t['jumlah']) ? htmlspecialchars($t['jumlah']) : 0;
                $status = isset($t['status']) ? htmlspecialchars($t['status']) : 'Unknown'; 
                $username = isset($t['username']) ? htmlspecialchars($t['username']) : ''; 
                $user_id_val = isset($t['user_id']) ? htmlspecialchars($t['user_id']) : ''; 

                $isSelesai = ($status === 'Selesai');
                $inputDisabled = (!$isAdmin && $isSelesai) ? 'disabled' : '';
                ?>

                <tr data-tanggungan-id="<?= $tanggunganId ?>" data-row-type="existing">
                  <td class="text-center">
                    <div class="d-flex flex-column gap-1">
                      <?php
                      $canDelete = (!$isAdmin && !$isSelesai) || $isAdmin;
                      ?>
                      <?php if ($canDelete): ?>
                        <button type="button" class="btn btn-sm btn-danger w-100 btn-hapus-tanggungan" onclick="hapusTanggungan(this)">Hapus</button>
                      <?php elseif ($isSelesai): ?>
                        <button class="btn btn-sm btn-secondary w-100" disabled>Selesai</button>
                      <?php endif; ?>
                    </div>
                  </td>

                  <?php if (isset($isAdmin) && $isAdmin): ?>
                    <td><input type="number" name="user_id_<?= $tanggunganId ?>" class="form-control form-control-sm" value="<?= $user_id_val ?>" <?= $inputDisabled ?>></td>
                  <?php endif; ?>

                  <td><input type="text" name="tanggungan_<?= $tanggunganId ?>" class="form-control form-control-sm" value="<?= $tanggunganNama ?>" <?= $inputDisabled ?>></td>
                  <td><input type="date" name="jadwal_pembayaran_<?= $tanggunganId ?>" class="form-control form-control-sm" value="<?= $jadwalPembayaran ?>" <?= $inputDisabled ?>></td>
                                            
                  <td>
                      <?php if (isset($isAdmin) && $isAdmin): // Jika admin, tampilkan sebagai teks statis?>
                          <span class="form-control form-control-sm border-0 bg-transparent">
                              <?php
                              // Cari nama kategori berdasarkan $kategoriId
                              $namaKategori = 'Tidak Diketahui';
                              if (isset($categories) && is_array($categories)) {
                                  foreach ($categories as $category) {
                                      if ($category['kategori_id'] == $kategoriId) {
                                          $namaKategori = htmlspecialchars($category['kategori']);
                                          break;
                                      }
                                  }
                              }
                              echo $namaKategori;
                              ?>
                          </span>
                          <input type="hidden" name="kategori_id_<?= $tanggunganId ?>" value="<?= htmlspecialchars($kategoriId) ?>">
                      <?php else: // Jika bukan admin, tampilkan dropdown seperti biasa?>
                          <select name="kategori_id_<?= $tanggunganId ?>" class="form-select form-select-sm" <?= $inputDisabled ?>>
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
                      <?php endif; ?>
                  </td>

                  <td><input type="number" name="jumlah_<?= $tanggunganId ?>" class="form-control form-control-sm" value="<?= $jumlah ?>" <?= $inputDisabled ?>></td>
                  <td>
                    <?php if (isset($isAdmin) && $isAdmin): ?>
                      <select name="status_<?= $tanggunganId ?>" class="form-select form-select-sm">
                        <option value="Belum dibayar" <?= ($status === 'Belum dibayar' ? 'selected' : '') ?>>Belum dibayar</option>
                        <option value="Selesai" <?= ($status === 'Selesai' ? 'selected' : '') ?>>Selesai</option>
                      </select>
                    <?php else: ?>
                      <input type="text" name="status_<?= $tanggunganId ?>" value="<?= $status ?>" class="form-control form-control-sm" disabled>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php
                if ($isSelesai) {
                  $totalBayar += (int)$jumlah;
                } else {
                  $totalAktif += (int)$jumlah;
                }
                ?>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <div class="d-flex justify-content-end mt-3">
        <button type="submit" class="btn btn-success" id="simpanSemuaBtn">Simpan Perubahan Data</button>
      </div>
    </form>
  </div>

  <div class="modal fade" id="addTanggunganModal" tabindex="-1" aria-labelledby="addTanggunganModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addTanggunganModalLabel">Tambah Tanggungan Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="addTanggunganForm" method="POST">
          <div class="modal-body">
            <?php if (isset($isAdmin) && $isAdmin): ?>
              <div class="mb-3">
                <label for="addUserId" class="form-label">ID Pengguna</label>
                <input type="number" name="user_id" id="addUserId" class="form-control" placeholder="Masukkan ID Pengguna" required>
              </div>
            <?php endif; ?>
            <div class="mb-3">
              <label for="addTanggungan" class="form-label">Nama Tanggungan</label>
              <input type="text" name="tanggungan" id="addTanggungan" class="form-control" placeholder="Contoh: Pulsa Telepon" required>
            </div>
            <div class="mb-3">
              <label for="addJadwalPembayaran" class="form-label">Jadwal Pembayaran</label>
              <input type="date" name="jadwal_pembayaran" id="addJadwalPembayaran" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="addKategoriId" class="form-label">Kategori</label>
              <select name="kategori_id" id="addKategoriId" class="form-select" required>
                <option value="">Pilih Kategori...</option>
                <?php
                if (isset($categories) && is_array($categories)) {
                  foreach ($categories as $category) {
                    $catId = isset($category['kategori_id']) ? htmlspecialchars($category['kategori_id']) : '';
                    $catNama = isset($category['kategori']) ? htmlspecialchars($category['kategori']) : '';
                    echo '<option value="' . $catId . '">' . $catNama . '</option>';
                  }
                }
                ?>
              </select>
            </div>
            <div class="mb-3">
              <label for="addJumlah" class="form-label">Jumlah (Rp)</label>
              <input type="number" name="jumlah" id="addJumlah" class="form-control" placeholder="Contoh: 50000" required min="0">
            </div>
            </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-success">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    const isAdmin = <?= json_encode($isAdmin ?? false) ?>;
    document.getElementById('aktif').innerText = 'Rp. <?= number_format($totalAktif, 0, ',', '.') ?>';
    document.getElementById('terbayar').innerText = 'Rp. <?= number_format($totalBayar, 0, ',', '.') ?>';

    async function hapusTanggungan(buttonElement) {
      if (!confirm('Hapus tanggungan ini?')) {
        return;
      }

      const rowElement = buttonElement.closest('tr');
      const tanggunganId = rowElement.dataset.tanggunganId;

      try {
        const data = new URLSearchParams();
        data.append('id', tanggunganId);

        const response = await fetch(`?c=TanggunganController&m=hapus`, {
          method: 'POST',
          body: data,
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          }
        });

        if (response.headers.get('content-type')?.includes('application/json')) {
          const result = await response.json();
          if (response.ok && result.isSuccess) {
            window.location.reload();
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

    <?php if (isset($isAdmin) && $isAdmin): ?>
    async function resetAllTanggunan() {
        if (!confirm('Anda yakin ingin mereset status semua tanggungan menjadi "Belum dibayar"? Tindakan ini tidak bisa dibatalkan!')) {
            return;
        }

        try {
            const response = await fetch('?c=TanggunganController&m=resetAwalBulan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });

            if (response.ok || response.redirected) {
                window.location.reload();
            } else {
                alert('Gagal mereset semua tanggungan. Terjadi kesalahan server.');
                window.location.reload();
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat berkomunikasi dengan server untuk reset.');
        }
    }
    <?php endif; ?>

    document.getElementById('tanggunganForm').addEventListener('submit', async function(event) {
        event.preventDefault();

        const rows = document.querySelectorAll('#tabelDaftarTanggungan tr[data-row-type="existing"]');
        const updates = [];

        for (const row of rows) {
            const tanggunganId = row.dataset.tanggunganId;
            const tanggungan = row.querySelector(`input[name="tanggungan_${tanggunganId}"]`).value;
            const jadwal_pembayaran = row.querySelector(`input[name="jadwal_pembayaran_${tanggunganId}"]`).value;
            // Kategori ID diambil dari input hidden jika admin, atau select jika user
            const kategori_id_element = row.querySelector(`[name="kategori_id_${tanggunganId}"]`);
            const kategori_id = kategori_id_element ? kategori_id_element.value : '';

            const jumlah = row.querySelector(`input[name="jumlah_${tanggunganId}"]`).value;
            let status = null;
            if (isAdmin) {
                const statusSelect = row.querySelector(`select[name="status_${tanggunganId}"]`);
                if (statusSelect) {
                    status = statusSelect.value;
                }
            } else {
                // Untuk non-admin, status adalah input type text yang disabled
                status = row.querySelector(`input[name="status_${tanggunganId}"]`).value;
            }


            let user_id_val = null;
            if (isAdmin) {
                const userIdInput = row.querySelector(`input[name="user_id_${tanggunganId}"]`);
                if (userIdInput) {
                    user_id_val = userIdInput.value;
                }
            }

            updates.push({
                tanggungan_id: tanggunganId,
                tanggungan: tanggungan,
                jadwal_pembayaran: jadwal_pembayaran,
                kategori_id: kategori_id,
                jumlah: jumlah,
                status: status, 
                user_id: user_id_val
            });
        }

        let allSuccess = true;
        let successCount = 0;
        let errorMessages = [];

        for (const data of updates) {
            const formData = new URLSearchParams();
            formData.append('tanggungan_id', data.tanggungan_id);
            formData.append('tanggungan', data.tanggungan);
            formData.append('jadwal_pembayaran', data.jadwal_pembayaran);
            formData.append('kategori_id', data.kategori_id);
            formData.append('jumlah', data.jumlah);
            if (data.status !== null) {
                formData.append('status', data.status);
            }
            if (isAdmin && data.user_id !== null) {
                formData.append('user_id', data.user_id);
            }

            try {
                const response = await fetch('?c=TanggunganController&m=update', {
                    method: 'POST',
                    body: formData,
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                });
                const result = await response.json();
                if (response.ok && result.isSuccess) {
                    successCount++;
                } else {
                    allSuccess = false;
                    errorMessages.push('Gagal memperbarui tanggungan ID ' + data.tanggungan_id + ': ' + (result.info || 'Terjadi kesalahan.'));
                }
            } catch (error) {
                allSuccess = false;
                errorMessages.push('Kesalahan jaringan saat memperbarui tanggungan ID ' + data.tanggungan_id + '.');
                console.error('Error updating tanggungan:', data, error);
            }
        }

        if (allSuccess && updates.length > 0) {
        } else if (successCount > 0 && errorMessages.length > 0) {
            alert('Beberapa perubahan tanggungan berhasil diproses, namun ada kesalahan:\n' + errorMessages.join('\n'));
        } else if (updates.length === 0) {
            alert('Tidak ada perubahan untuk disimpan.');
        } else {
            alert('Gagal memproses semua perubahan tanggungan:\n' + errorMessages.join('\n'));
        }

        window.location.reload();
    });

    document.getElementById('addTanggunganForm').addEventListener('submit', async function(event) {
        event.preventDefault();

        const form = event.target;
        const formData = new URLSearchParams(new FormData(form));

        const tanggungan = form.querySelector('[name="tanggungan"]').value;
        const jadwal_pembayaran = form.querySelector('[name="jadwal_pembayaran"]').value;
        const kategori_id = form.querySelector('[name="kategori_id"]').value;
        const jumlah = form.querySelector('[name="jumlah"]').value;
        let user_id = null;
        if (isAdmin) {
          user_id = form.querySelector('[name="user_id"]').value;
        }

        if (!tanggungan || !jadwal_pembayaran || !kategori_id || !jumlah || (isAdmin && !user_id)) {
            alert('Harap lengkapi semua kolom.');
            return;
        }
        
        try {
            const response = await fetch('?c=TanggunganController&m=insert', {
                method: 'POST',
                body: formData,
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            });
            const result = await response.json();
            if (response.ok && result.isSuccess) {
                const addTanggunganModal = bootstrap.Modal.getInstance(document.getElementById('addTanggunganModal'));
                addTanggunganModal.hide();
                form.reset();
                window.location.reload();
            } else {
                alert('Gagal menambahkan tanggungan baru: ' + (result.info || 'Terjadi kesalahan tidak dikenal.'));
            }
        } catch (error) {
            console.error('Error adding new tanggungan:', error);
            alert('Terjadi kesalahan jaringan saat menambahkan tanggungan baru.');
        }
    });

  </script>

  <?php include_once "footer.php"; ?>
</body>
</html>