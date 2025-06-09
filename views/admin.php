<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Accounts</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f1f9f4;
    }
    .account-card {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      padding: 15px;
      margin-bottom: 15px;
    }
    .account-info {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .account-details {
      display: flex;
      flex-direction: column;
    }
    .account-actions i {
      cursor: pointer;
      margin-left: 15px;
    }
    .account-actions i:hover {
      color: #0d6efd;
    }
  </style>
</head>
<body>
  <div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Manage User Accounts</h2>
      <a href="add-user.php" class="btn btn-success">+ Add User</a>
    </div>

    <!-- Repeat this block for each user -->
    <div class="account-card">
      <div class="account-info">
        <div class="account-details">
          <strong>John Doe</strong>
          <small>Email: john@example.com</small>
          <small>Role: User</small>
        </div>
        <div class="account-actions">
          <a href="edit-user.php?id=1" title="Edit"><i class="bi bi-pencil-square"></i></a>
          <a href="delete-user.php?id=1" title="Delete" onclick="return confirm('Are you sure you want to delete this account?')">
            <i class="bi bi-trash"></i>
          </a>
        </div>
      </div>
    </div>
    <!-- End user block -->

    <!-- Add more users dynamically with PHP or JS -->

  </div>

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</body>
</html>
