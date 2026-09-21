<?php
require_once __DIR__ . '/../includes/functions.php';
requireAdminLogin();
if ($_SESSION['admin_role'] !== 'super_admin') {
    header('Location: dashboard.php');
    exit;
}
$pdo = getDB();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_akun'])) {
    $username = trim($_POST['username'] ?? '');
    $nama = trim($_POST['nama_lengkap'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'petugas';
    $kategoriId = !empty($_POST['kategori_id']) ? (int) $_POST['kategori_id'] : null;

    if ($username && $nama && $password) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO admin (username, password, nama_lengkap, role, kategori_id) VALUES (:u,:p,:n,:r,:k)");
        try {
            $stmt->execute(['u'=>$username,'p'=>$hash,'n'=>$nama,'r'=>$role,'k'=>$kategoriId]);
            $message = 'Akun berhasil dibuat.';
        } catch (PDOException $e) {
            $message = 'Gagal membuat akun (username mungkin sudah dipakai).';
        }
    }
}

if (isset($_GET['nonaktifkan'])) {
    $stmt = $pdo->prepare("UPDATE admin SET is_active = 0 WHERE id = :id AND id != :me");
    $stmt->execute(['id' => (int)$_GET['nonaktifkan'], 'me' => $_SESSION['admin_id']]);
    header('Location: akun.php');
    exit;
}

$akunList = $pdo->query("SELECT a.*, k.nama_kategori FROM admin a LEFT JOIN kategori_layanan k ON k.id=a.kategori_id ORDER BY a.id ASC")->fetchAll();
$kategoriList = getKategoriList($pdo);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Akun - Admin ULT</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<div class="admin-shell">
  <?php include __DIR__ . '/_sidebar.php'; ?>
  <div class="admin-main">
    <div class="admin-topbar"><h2>Kelola Akun</h2></div>
    <?php if ($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>

    <div class="mini-card" style="margin-bottom:24px;">
      <h3>Tambah Akun Baru</h3>
      <form method="POST" style="display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:12px; margin-top:14px;">
        <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required style="padding:10px; border-radius:8px; border:1px solid #d1d5db;">
        <input type="text" name="username" placeholder="Username" required style="padding:10px; border-radius:8px; border:1px solid #d1d5db;">
        <input type="password" name="password" placeholder="Password" required style="padding:10px; border-radius:8px; border:1px solid #d1d5db;">
        <select name="role" style="padding:10px; border-radius:8px; border:1px solid #d1d5db;">
          <option value="petugas">Petugas</option>
          <option value="super_admin">Super Admin</option>
        </select>
        <select name="kategori_id" style="padding:10px; border-radius:8px; border:1px solid #d1d5db;">
          <option value="">(Petugas: pilih kategori)</option>
          <?php foreach ($kategoriList as $k): ?>
            <option value="<?= $k['id'] ?>"><?= e($k['nama_kategori']) ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" name="tambah_akun" value="1" class="btn-sm btn-panggil">Tambah Akun</button>
      </form>
    </div>

    <div class="table-scroll">
      <table class="data-table">
        <thead><tr><th>Username</th><th>Nama</th><th>Role</th><th>Kategori</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
          <?php foreach ($akunList as $a): ?>
          <tr>
            <td><?= e($a['username']) ?></td>
            <td><?= e($a['nama_lengkap']) ?></td>
            <td><?= e($a['role']) ?></td>
            <td><?= e($a['nama_kategori'] ?? '-') ?></td>
            <td><?= $a['is_active'] ? 'Aktif' : 'Nonaktif' ?></td>
            <td><?php if ($a['id'] != $_SESSION['admin_id'] && $a['is_active']): ?><a href="?nonaktifkan=<?= $a['id'] ?>" onclick="return confirm('Nonaktifkan akun ini?');" style="color:#dc2626;">Nonaktifkan</a><?php endif; ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
