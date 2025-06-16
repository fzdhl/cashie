<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Tagihan dan Komitmen</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="views/styles/tanggungan.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <style>
    html, body {
      height: 100%; 
      margin: 0; 
      font-family: "Inter", sans-serif;
    }
    body {
      background-color: #F0F8F3; 
    }
  </style>

</head>


<body class="d-flex flex-column min-vh-100"> 
  <?php include_once "header.php"; ?>
  <div class="container py-5 flex-grow-1"> 
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

    <?php
    // mengecek apakah data tanggungan sudah ada dan apakah sudah disimpan permanen
    $bolehEdit = isset($tanggungan[0]) ? !$tanggungan[0]['permanen'] : true;
    ?>

    <!-- jika tagihan belum permanen, tampilkan form input -->
    <?php if ($bolehEdit): ?>
      <form method="post" action="?c=TanggunganController&m=simpanPermanen">

        <div class="d-flex justify-content-between mb-3">
          <!-- tombol untuk menambah baris input tanggungan -->
          <button type="button" class="btn btn-success" onclick="tambahBaris()">+ Tambah Tanggungan</button>
          <!-- tombol untuk menyimpan semua baris input ke database -->
          <button type="submit" class="btn btn-dark">Simpan Permanen</button>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered align-middle bg-white">
            <thead class="table-light text-center">
              <tr>
                <th class="col-md-1"></th>
                <th class="col-md-3">Tanggungan</th>
                <th class="col-md-2">Jadwal Pembayaran</th>
                <th class="col-md-2">Kategori</th>
                <th class="col-md-2">Total Tagihan</th>
                <th class="col-md-2">Status Pembayaran</th>
              </tr>
            </thead>
            <tbody id="tabelTanggungan">
            </tbody>
          </table>
        </div>
      </form>
    <?php endif; ?>

    <div class="table-responsive mt-4">
      <table class="table table-bordered align-middle bg-white">
        <tbody>

          <!-- variabel yang menampung hasil masing-masing -->
          <?php
          $totalAktif = 0;
          $totalBayar = 0;
          ?>

          <!-- memeriksa apakah ada data tanggungan -->
          <?php if (!empty($tanggungan)): ?>
            <?php foreach ($tanggungan as $t):
              // memeriksa apakah status selesai
              $isSelesai = $t['status'] === 'Selesai';
              // jika sudah permanen maka input dimatikan
              $isDisable = $t['permanen'] ? 'disabled' : '';

              if ($isSelesai)
                $totalBayar += $t['jumlah'];
              else
                $totalAktif += $t['jumlah'];
              ?>

              <tr>
                <td class="text-center">
                  <!-- tombol hapus hanya muncul jika belum permanen -->
                  <?php if (!$t['permanen']): ?>
                    <form method="post" action="?c=TanggunganController&m=hapus&id=<?= $t['user_id'] ?>"
                      onsubmit="return confirm('Hapus?')">
                      <button class="btn btn-sm btn-danger">&minus;</button>
                    </form>
                  <?php endif; ?>
                </td>

                <td><input type="text" class="form-control form-control-sm" value="<?= $t['tanggungan'] ?>" <?= $isDisable ?>></td>
                <td><input type="date" class="form-control form-control-sm" value="<?= $t['jadwal_pembayaran'] ?>"
                    <?= $isDisable ?>></td>
                <td>
                  <select name="jenis[]" class="form-select form-select-sm" <?= $isDisable ?>>
                    <!-- menampilkan kategori sesuai halaman kategori -->
                    <?php
                    if (isset($categories) && is_array($categories)) {
                      foreach ($categories as $category) {
                        $selected = (isset($t['kategori_id']) && $t['kategori_id'] == $category['kategori_id']) ? 'selected' : '';
                        echo '<option value="' . htmlspecialchars($category['kategori_id']) . '" ' . $selected . '>' . htmlspecialchars($category['kategori']) . '</option>';
                      }
                    }
                    ?>
                  </select>
                </td>
                <td><input type="number" class="form-control form-control-sm" value="<?= $t['jumlah'] ?>" <?= $isDisable ?>>
                </td>
                <td><input type="text" class="form-control form-control-sm" value="<?= $t['status'] ?>" disabled></td>
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

    // fungsi untuk menambah baris baru ke dalam tabel input tanggungan
    function tambahBaris() {
      const tbody = document.getElementById('tabelTanggungan');
      const row = document.createElement('tr');

      let categoryOptions = '<option value="">Pilih...</option>';
      categories.forEach(category => {
        categoryOptions += `<option value="${category.kategori_id}">${category.kategori}</option>`;
      });

      // nilai dari input dikirim ke server sebagai array ketika form disubmit
      row.innerHTML = `
        <td class="text-center align-middle">
        <span onclick="hapusBaris(this)" style="cursor: pointer; font-weight: bold; ">−</span>
        </td>
        <td><input type="text" name="nama[]" class="form-control form-control-sm" required></td>
        <td><input type="date" name="jadwal[]" class="form-control form-control-sm" required></td>
        <td>
          <select name="jenis[]" class="form-select form-select-sm" required>
            ${categoryOptions}
          </select>
        </td>
        <td><input type="number" name="jumlah[]" class="form-control form-control-sm" required></td>
        <td><input type="text" value="Belum dibayar" class="form-control form-control-sm" readonly></td>
      `;
      tbody.appendChild(row);
    }

    function hapusBaris(el) {
      const row = el.closest('tr');
      if (row) row.remove();
    }

  </script>
  <?php include_once "footer.php"; ?>
</body>
</div>
</html>