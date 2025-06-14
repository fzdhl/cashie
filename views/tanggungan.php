<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Tagihan dan Komitmen</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="views/styles/tanggungan.css" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<?php include_once "header.php"; ?>

<!-- latar belakang abu terang -->

<body class="bg-light">
  <!-- kontainer utama -->
  <div class="container my-5">
    <h2 class="fw-bold mb-4">Tagihan dan Komitmen</h2>

    <!-- kartu dengan keterangan periode saat ini -->
    <div class="row g-3 mb-4">
      <!-- styling menggunakan bootstrap -->
      <div class="col-md-6 col-lg-3">
        <div class="card p-3 shadow-sm h-100">
          <h6 class="text-muted">Periode</h6>
          <!-- menampilkan tahun dan bulan saat ini -->
          <h5><?= date('Y | F') ?></h5>
        </div>
      </div>

      <!-- kartu dengan keterangan jumlah tanggungan aktif -->
      <div class="col-md-6 col-lg-3">
        <div class="card p-3 shadow-sm h-100">
          <h6 class="text-muted">Tanggungan Aktif</h6>
          <!-- nilai akan diupdate dari php atau database -->
          <h5 id="aktif">Rp. 0</h5>
        </div>
      </div>

      <!-- kartu dengan keterangan jumlah yang sudah dibayarkan -->
      <div class="col-md-6 col-lg-3">
        <div class="card p-3 shadow-sm h-100">
          <h6 class="text-muted">Jumlah Terbayar</h6>
          <!-- nilai akan diisi otomatis setelah status berubah menjadi selesai -->
          <h5 id="terbayar">Rp. 0</h5>
        </div>
      </div>

      <!-- kartu dengan keterangan saldo -->
      <div class="col-md-6 col-lg-3">
        <div class="card p-3 shadow-sm h-100">
          <h6 class="text-muted">Saldo</h6>
          <!-- dihubungkan dengan tampilan saldo yang ada di dashboard utama -->
          <h5>Rp. 0</h5>
        </div>
      </div>
    </div>

    <?php
    // mengecek apakah data tanggungan sudah ada dan apakah sudah disimpan permanen
    // jika belum ada data maka diasumsikan masih boleh diedit
    $bolehEdit = isset($tanggungan[0]) ? !$tanggungan[0]['permanen'] : true;
    ?>

    <!-- jika tagihan belum permanen, tampilkan form input -->
    <?php if ($bolehEdit): ?>
      <form method="post" action="?c=TanggunganController&m=simpanPermanen">

        <!-- tombol aksi yang ada di form -->
        <!-- baris horizontal untuk jarak antar tombol -->
        <div class="d-flex justify-content-between mb-3">
          <!-- tombol untuk menambah baris input tanggungan -->
          <button type="button" class="btn btn-success" onclick="tambahBaris()">+ Tambah Tanggungan</button>
          <!-- tombol untuk menyimpan semua baris input ke database -->
          <button type="submit" class="btn btn-dark">Simpan Permanen</button>
        </div>

        <div class="table-responsive">
          <!-- tabel dengan border dan isi di tengah -->
          <table class="table table-bordered align-middle bg-white">
            <thead class="table-light text-center">
              <tr>
                <!-- masing-masing kolom -->
                <th class="col-md-1"></th>
                <th class="col-md-3">Tanggungan</th>
                <th class="col-md-2">Jadwal Pembayaran</th>
                <th class="col-md-2">Kategori</th>
                <th class="col-md-2">Total Tagihan</th>
                <th class="col-md-2">Status Pembayaran</th>
              </tr>
            </thead>
            <!-- tabel tempat baris input akan ditambahkan secara dinamis menggunakan javascript -->
            <tbody id="tabelTanggungan">
            </tbody>
          </table>
        </div>
      </form>
      <!-- penutup pengecekan apakah user masih boleh edit tagihan -->
    <?php endif; ?>

    <!-- kontainer tabel yang bisa discroll jika overflow -->
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
          <!-- looping tiap data -->
          <?php foreach ($tanggungan as $t):
            // memeriksa apakah status selesai
            $isSelesai = $t['status'] === 'Selesai';
            // jika sudah permanen maka input dimatikan
            $isDisable = $t['permanen'] ? 'disabled' : '';

            // mengakumulasi berdasarkan status
            if ($isSelesai)
              $totalBayar += $t['jumlah'];
            else
              $totalAktif += $t['jumlah'];
            ?>

            <!-- baris tabel -->
            <tr>
              <td class="text-center">
                <!-- tombol hapus hanya muncul jika belum permanen -->
                <?php if (!$t['permanen']): ?>
                  <form method="post" action="?c=TanggunganController&m=hapus&id=<?= $t['user_id'] ?>"
                    onsubmit="return confirm('Hapus?')">
                    <!-- tombol min sebagai hapus -->
                    <button class="btn btn-sm btn-danger">&minus;</button>
                  </form>
                <?php endif; ?>
              </td>

              <!-- input teks nama tanggungan -->
              <!-- class="form-control form-control-sm": menggunakan bootstrap untuk styling -->
              <td><input type="text" class="form-control form-control-sm" value="<?= $t['tanggungan'] ?>" <?= $isDisable ?>></td>
              <!-- input tanggal jadwal pembayarannya -->
              <td><input type="date" class="form-control form-control-sm" value="<?= $t['jadwal_pembayaran'] ?>"
                  <?= $isDisable ?>></td>
              <td>
                <!-- input jenis tangggungan menggunakan dropdown -->
                <!-- jika data sudah disimpan permanen maka dropdown akan dimatikan -->
                <select name="jenis[]" class="form-select form-select-sm" <?= $isDisable ?>>
                  <!-- buat munculin kategori di tabel given -->
                  <?php
                  // memastikan $categories ada dan merupakan array sebelum looping
                  if (isset($categories) && is_array($categories)) {
                      foreach ($categories as $category) {
                          $selected = (isset($t['kategori_id']) && $t['kategori_id'] == $category['kategori_id']) ? 'selected' : '';
                          echo '<option value="' . htmlspecialchars($category['kategori_id']) . '" ' . $selected . '>' . htmlspecialchars($category['kategori']) . '</option>';
                      }
                  }
                  ?>
                </select>
              </td>
              <!-- input jumlah atau biaya tanggungan -->
              <td><input type="number" class="form-control form-control-sm" value="<?= $t['jumlah'] ?>" <?= $isDisable ?>>
              </td>
              <!-- input status pembayaran yang selalu belum selesai karena user tidak bisa mengedit langsung -->
              <td><input type="text" class="form-control form-control-sm" value="<?= $t['status'] ?>" disabled></td>
            </tr>
            <!-- menutup perulangan loop -->
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
      </table>
    </table>
    </div>
  </div>

  <script>
    // script untuk update tampilan total tanggungan aktif
    // mencari elemen id="aktif"
    // kemudian mengganti teks menjadi Rp. (memasukkan jumlah tanggungan aktif)
    document.getElementById('aktif').innerText = 'Rp. <?= number_format($totalAktif, 0, ',', '.') ?>';
    // sama seperti update tanggungan aktif
    // diisi dengan total tagihan yang berstatus selesai
    document.getElementById('terbayar').innerText = 'Rp. <?= number_format($totalBayar, 0, ',', '.') ?>';

    // mengambil kategori dan meneruskan ke javascript
    const categories = <?= json_encode($categories ?? []) ?>;

    // fungsi untuk menambah baris baru ke dalam tabel input tanggungan
    function tambahBaris() {
      // mengambil elemen dengan id tabel tanggungan
      const tbody = document.getElementById('tabelTanggungan');
      // membuat elemen baris tabel baru
      const row = document.createElement('tr');

      let categoryOptions = '<option value="">Pilih...</option>'; // opsi default untuk dropdown
      categories.forEach(category => { // melakukan iterasi untuk setiap kategori dari array JavaScript `categories`
          // menambahkan opsi untuk setiap kategori dengan nilai kategori_id dan teks kategori
          categoryOptions += `<option value="${category.kategori_id}">${category.kategori}</option>`;
      });

      // nilai dari input dikirim ke server sebagai array ketika form disubmit
      // styling menggunakan bootstrap (form-control) merupakan kelas bootstrap untuk input rapi dan konsisten
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
      // menambahkan baris baru kedalam elemen di tabel
      tbody.appendChild(row);
    }

    function hapusBaris(el) {
      const row = el.closest('tr');
      if (row) row.remove();
    }

  </script>
  <?php include_once "footer.php"; ?>
</body>


</html>