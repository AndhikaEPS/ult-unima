<?php
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$pdo = getDB();
$stmt = $pdo->prepare("SELECT * FROM admin WHERE username = :u AND is_active = 1");
$stmt->execute(['u' => $username]);
$admin = $stmt->fetch();

if (!$admin || !password_verify($password, $admin['password'])) {
    $_SESSION['login_error'] = 'Username atau password salah.';
    header('Location: login.php');
    exit;
}

$_SESSION['admin_id']    = $admin['id'];
$_SESSION['admin_nama']  = $admin['nama_lengkap'];
$_SESSION['admin_role']  = $admin['role'];
$_SESSION['admin_kategori_id'] = $admin['kategori_id'];

header('Location: dashboard.php');
exit;
