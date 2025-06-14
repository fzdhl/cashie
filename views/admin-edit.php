<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Akun</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="views/styles/admin-edit.css">
</head>
<body>
  <div class="container">
    <div class="card">
      <h2>Edit Akun</h2>
      <form action="update_user.php" method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($user->user_id) ?>">
        
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" name="username" id="username" value="<?= htmlspecialchars($user->username) ?>" required>
        </div>
  
        <div class="form-group">
          <label for="email">E-mail</label>
          <input type="email" name="email" id="email" value="<?= htmlspecialchars($user->email) ?>" required>
        </div>
  
        <div class="form-group">
          <label for="email">Password</label>
          <!-- <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required> -->
          <a href="change_password.php?id=<?= 29 ?>" class="btn btn-secondary">Ubah Password</a>
        </div>
  
        <div class="action-group">
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>