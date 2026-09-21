<?php
require_once __DIR__ . '/../config/config.php';
$currentPage = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? e($pageTitle) . ' - ' : '' ?>Unit Layanan Terpadu | UNIMA</title>
<link rel="icon" href="<?= BASE_URL ?>/assets/img/logo.png">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<nav class="navbar">
  <a href="<?= BASE_URL ?>/index.php" class="brand">
    <img src="<?= BASE_URL ?>/assets/img/logo.png" alt="Logo UNIMA" onerror="this.style.display='none'">
    Unit Layanan Terpadu
  </a>
  <div class="nav-links" id="navLinks">
    <a href="index.php" class="<?= $currentPage === 'index.php' ? 'active' : '' ?>">Beranda</a>
    <a href="layanan.php" class="<?= $currentPage === 'layanan.php' ? 'active' : '' ?>">Layanan</a>
    <a href="panduan.php" class="<?= $currentPage === 'panduan.php' ? 'active' : '' ?>">Panduan</a>
    <a href="tentang.php" class="<?= $currentPage === 'tentang.php' ? 'active' : '' ?>">Tentang</a>
    <a href="status.php" class="<?= $currentPage === 'status.php' ? 'active' : '' ?>">Cek Status</a>
  </div>
  <div class="nav-right">
    <a href="admin/login.php"><button class="btn-login">Login</button></a>
    <div class="avatar-circle"><i class="bi bi-person-fill"></i></div>
    <button class="hamburger" id="hamburger"><i class="bi bi-list"></i></button>
  </div>
</nav>
