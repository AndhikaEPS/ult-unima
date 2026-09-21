<?php
require_once __DIR__ . '/../includes/functions.php';

if (isAdminLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
$pageTitle = 'Login Admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk Sebagai Admin - Unit Layanan Terpadu</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<div class="login-page">
  <div class="login-card">
    <img src="<?= BASE_URL ?>/assets/img/logo.png" alt="Logo UNIMA" onerror="this.style.display='none'">
    <h2>Masuk Sebagai Admin</h2>
    <p class="sub">Unit Layanan Terpadu</p>

    <?php if ($error): ?>
      <div class="alert alert-error" style="text-align:left;"><?= e($error) ?></div>
    <?php endif; ?>

    <form action="process_login.php" method="POST">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" required autofocus>
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required>
      <button type="submit" class="btn-submit" style="border:none; width:100%; color:#fff; cursor:pointer;">Login</button>
    </form>
    <a href="../index.php" class="back-link">Kembali ke beranda</a>
  </div>
</div>
</body>
</html>
