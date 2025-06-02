<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Catatan Keuangan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
</head>

<body

  style="
  background-color: #f0f8f3;
  font-family: 'Inter', sans-serif;
  color: #1f3f2e;
  "
  >
  <?php include_once "header.php" ?>
  
  <div class="container py-4">
    <h1 class="text-center fw-bold mb-4">Kelola Catatan Keuangan Anda</h1>

    <!-- Tabs -->
    <div class="text-center mb-4 d-flex gap-3 mb-5 justify-content-center">
      <button class="btn btn-success col-lg-2 col-md-3 col-sm-3">Pengeluaran</button>
      <button class="btn btn-success col-lg-2 col-md-3 col-sm-3">Pemasukan</button>
    </div>

    <div class="row g-5">
      <!-- Kolom Kiri -->
      <!-- Kategori -->
      <div class="col-md-4">
        <h4 class="text-center mb-3" style="color: #408558">Kategori</h4>
        <div class="d-grid gap-3" style="
              grid-template-columns: repeat(3, 1fr);
              display: grid;
              max-height: 336px;
              overflow-y: auto;
            ">
          <!-- KODE BARU -->
          <!-- 🍽️ Kategori item -->
          <div class="bg-white rounded text-center py-3 border kategori-item" style="cursor: pointer"
            onclick="pilihKategori(this)" onmouseenter="hoverMasuk(this)" onmouseleave="hoverKeluar(this)">
            🍽️
            <div class="small">Makan</div>
          </div>

          <div class="bg-white rounded text-center py-3 border kategori-item" style="cursor: pointer"
            onclick="pilihKategori(this)" onmouseenter="hoverMasuk(this)" onmouseleave="hoverKeluar(this)">
            🍹
            <div class="small">Minuman</div>
          </div>
          <div class="bg-white rounded text-center py-3 border kategori-item" style="cursor: pointer"
            onclick="pilihKategori(this)" onmouseenter="hoverMasuk(this)" onmouseleave="hoverKeluar(this)">
            🚗
            <div class="small">Transport</div>
          </div>
          <div class="bg-white rounded text-center py-3 border kategori-item" style="cursor: pointer"
            onclick="pilihKategori(this)" onmouseenter="hoverMasuk(this)" onmouseleave="hoverKeluar(this)">
            🏠
            <div class="small">Sehari</div>
          </div>
          <div class="bg-white rounded text-center py-3 border kategori-item" style="cursor: pointer"
            onclick="pilihKategori(this)" onmouseenter="hoverMasuk(this)" onmouseleave="hoverKeluar(this)">
            💬
            <div class="small">Sosial</div>
          </div>
          <div class="bg-white rounded text-center py-3 border kategori-item" style="cursor: pointer"
            onclick="pilihKategori(this)" onmouseenter="hoverMasuk(this)" onmouseleave="hoverKeluar(this)">
            🧺
            <div class="small">Laundry</div>
          </div>
          <div class="bg-white rounded text-center py-3 border kategori-item" style="cursor: pointer"
            onclick="pilihKategori(this)" onmouseenter="hoverMasuk(this)" onmouseleave="hoverKeluar(this)">
            🥬
            <div class="small">Sayuran</div>
          </div>
          <div class="bg-white rounded text-center py-3 border kategori-item" style="cursor: pointer"
            onclick="pilihKategori(this)" onmouseenter="hoverMasuk(this)" onmouseleave="hoverKeluar(this)">
            🍐
            <div class="small">Buah</div>
          </div>
          <div class="bg-white rounded text-center py-3 border kategori-item" style="cursor: pointer"
            onclick="pilihKategori(this)" onmouseenter="hoverMasuk(this)" onmouseleave="hoverKeluar(this)">
            🍭
            <div class="small">Camilan</div>
          </div>
        </div>
      </div>
      <div class="col-md-8">
        <!-- Batas Pengeluaran -->
        <div class="position-relative mb-4">
          <label class="form-label">Batas Pengeluaran:</label>
          <div class="d-flex align-items-center gap-2">
            <input type="text" class="form-control" id="batasPengeluaran" value="Rp0" disabled
              style="border: 1.5px solid #408558; border-radius: 10px" />
            <button class="btn btn-sm" onclick="toggleEditPanel()" style="
                  border: 1px solid #408558;
                  background-color: #e1f2e8;
                  color: #408558;
                ">
              ✏️
            </button>
          </div>

          <div class="position-absolute bg-white p-3 rounded shadow border mt-2 w-100 d-none" id="editPanel"
            style="border-color: #408558; z-index: 10">
            <div class="mb-2">
              <label class="form-label">Nominal:</label>
              <input type="number" id="newNominal" class="form-control" placeholder="Masukkan nominal" />
            </div>
            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" id="aktifkanNotif" />
              <label class="form-check-label" for="aktifkanNotif">Aktifkan notifikasi</label>
            </div>
            <button class="btn w-100 text-white" style="background-color: #408558" onclick="simpanBatas()">
              Simpan
            </button>
          </div>
        </div>

        <!-- Form Tambah Catatan -->
        <div class="bg-white p-4 rounded-4 shadow border" style="border-color: #408558">
          <h4 class="mb-3" style="color: #408558">Tambah Catatan</h4>
          <input type="date" class="form-control mb-3" style="border: 1.5px solid #aad2c7; border-radius: 10px" />
          <input type="text" class="form-control mb-3" placeholder="Keterangan"
            style="border: 1.5px solid #aad2c7; border-radius: 10px" />
          <input type="number" class="form-control mb-3" placeholder="Jumlah (Rp)"
            style="border: 1.5px solid #aad2c7; border-radius: 10px" />
          <button class="btn btn-success w-100">Simpan Catatan</button>
        </div>
      </div>
    </div>
  </div>



  <script>
    function toggleEditPanel() {
      const panel = document.getElementById("editPanel");
      panel.classList.toggle("d-none");
    }

    function simpanBatas() {
      const nominal = document.getElementById("newNominal").value;
      const notifAktif = document.getElementById("aktifkanNotif").checked;
      const statusNotif = notifAktif
        ? "Notifikasi Aktif"
        : "Notifikasi Nonaktif";
      document.getElementById("batasPengeluaran").value =
        "Rp" + nominal + " (" + statusNotif + ")";
      document.getElementById("editPanel").classList.add("d-none");
    }

    function pilihKategori(element) {
      const semua = document.querySelectorAll(".kategori-item");
      semua.forEach((el) => {
        el.classList.remove("active-kategori");
        el.style.backgroundColor = "white";
      });
      element.classList.add("active-kategori");
      element.style.backgroundColor = "#408558"; // hijau utama
    }

    function hoverMasuk(el) {
      if (!el.classList.contains("active-kategori")) {
        el.style.backgroundColor = "#2e6346"; // hijau tua
      }
    }

    function hoverKeluar(el) {
      if (!el.classList.contains("active-kategori")) {
        el.style.backgroundColor = "white";
      }
    }
  </script>

  <?php include_once "footer.php" ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>