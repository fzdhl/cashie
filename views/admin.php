<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Accounts</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="views/styles/universal.css" rel="stylesheet">
  <link href="views/styles/admin.css" rel="stylesheet">

</head>
<body>
  <?php include_once "header.php" ?>

  <div class="container py-5" id="main">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Kelola Akun</h2>
      <a href="?c=DashboardController&m=logout" class="btn btn-success">Keluar</a>
    </div>

    <?php foreach ($users as $user) : ?>
        <div class="account-card">
        <div class="account-info">
            <div class="account-details">
                <strong><?=htmlspecialchars($user['username'])?></strong>
                <small>E-mail: <?=htmlspecialchars($user['email'])?></small>
            </div>
            <div class="account-actions">
                <a href="?c=AdminController&m=edit&user_id=<?=$user['user_id']?>" title="Edit"><i class="bi bi-pencil-square"></i></a>
                <a href="?c=AdminController&m=delete&user_id=<?=$user['user_id']?>" title="Delete" onclick="return confirm('Are you sure you want to delete this account?')" id="deleteButton">
                <i class="bi bi-trash"></i>
            </a>
            </div>
        </div>
        </div>
    <?php endforeach; ?>

  </div>

  <?php include_once "footer.php" ?>

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
