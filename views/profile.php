<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Profil Pengguna</title>
  <link rel="stylesheet" href="views/styles/profile.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <?php include_once "header.php" ?>
  <main class="container">
    <h1 class="judul">Kelola Profil Akun Anda</h1>

    <section class="profil-box">
      <div class="profil-foto">
        <img src="default-avatar.png" alt="Foto Profil" class="foto-preview">
        <div class="foto-btns">
          <button class="icon-btn">📷 Ganti</button>
          <button class="icon-btn red">🗑 Hapus</button>
        </div>
      </div>

      <div class="profil-form">
        <div class="form-row">
          <label for="nama">Nama</label>
          <input type="text" id="nama" placeholder="Nama Lengkap">
        </div>

        <div class="form-row">
          <label for="email">Email</label>
          <input type="email" id="email" placeholder="Tambahkan atau ganti email">
          <button class="icon-btn red">🗑</button>
        </div>

        <div class="form-row">
          <label for="telepon">No. HP</label>
          <input type="tel" id="telepon" placeholder="Tambahkan atau ganti nomor HP">
          <button class="icon-btn red">🗑</button>
        </div>

        <div class="form-row">
          <label>Tanggal Bergabung</label>
          <input type="text" disabled value="26 Mei 2025">
        </div>

        <button class="submit-btn">Simpan Perubahan</button>
      </div>
    </section>
  </main>
  <?php include_once "footer.php" ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>