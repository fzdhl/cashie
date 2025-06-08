<header class="bg-success text-white shadow-sm position-sticky">
  <nav class="navbar navbar-expand-xl navbar-dark container py-3">
    <a class="navbar-brand fw-bold" href="#">💰 Cashie Dashboard</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link text-white" href="?c=DashboardController&m=index">Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white" href="?c=DashboardController&m=kategori">Kategori</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white" href="?c=DashboardController&m=catatanKeuangan">Tambah Catatan</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white" href="?c=DashboardController&m=report">Laporan Keuangan</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white" href="?c=DashboardController&m=calendar">Kalender</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white" href="?c=DashboardController&m=goals">Goals</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white" href="?c=DashboardController&m=arsip">Arsip</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Hello, <?= htmlspecialchars($_SESSION['user']->username) ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li><a class="dropdown-item" href="?c=ProfileController&m=index">Profile</a></li>
            <li><a class="dropdown-item" href="?c=DashboardController&m=logout">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
</header>