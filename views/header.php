<header class="bg-success text-white shadow sticky-top">
  <nav class="navbar navbar-expand-xl navbar-dark container py-3 px-3 px-md-0">
    <a class="navbar-brand fw-bold" href="?c=DashboardController&m=index">💰 Cashie Dashboard</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <?php if ($_SESSION['user']->privilege == 'admin'):?>
          <li class="nav-item">
            <a class="nav-link text-white" href="?c=AdminController&m=index">Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="?c=AdminCalendarController&m=index">Transaksi</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="?c=AdminKategoriController&m=index">Kategori</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="?c=TanggunganController&m=index">Tanggungan</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="?c=AdminTargetController&m=index">Target</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="?c=LaporanController&m=report">Laporan Keuangan</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="?c=AdminArsipController&m=index">Arsip</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Halo, <?= htmlspecialchars($_SESSION['user']->username) ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
              <li><a class="dropdown-item" href="?c=ProfileController&m=index">Profile</a></li>
              <li><a class="dropdown-item" href="?c=DashboardController&m=logout">Logout</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link text-white" href="?c=DashboardController&m=index">Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="?c=CalendarController&m=index">Transaksi</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="?c=KategoriController&m=index">Kategori</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="?c=TanggunganController&m=index">Tanggungan</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="?c=TargetController&m=index">Target</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="?c=LaporanController&m=report">Laporan Keuangan</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="?c=ArsipController&m=index">Arsip</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Halo, <?= htmlspecialchars($_SESSION['user']->username) ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
              <li><a class="dropdown-item" href="?c=ProfileController&m=index">Profile</a></li>
              <li><a class="dropdown-item" href="?c=DashboardController&m=logout">Logout</a></li>
            </ul>
          </li>
        <?php endif ?>
      </ul>
    </div>
  </nav>
</header>