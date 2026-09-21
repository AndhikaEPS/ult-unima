<?php
require_once __DIR__ . '/../includes/functions.php';
requireAdminLogin();
$pdo = getDB();

$message = '';

// Tambah sub layanan baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_sub'])) {
    $kategoriId = (int) $_POST['kategori_id'];
    $nama = trim($_POST['nama_layanan'] ?? '');
    $wajibUpload = isset($_POST['wajib_upload']) ? 1 : 0;
    if ($kategoriId && $nama !== '') {
        $stmt = $pdo->prepare("INSERT INTO sub_layanan (kategori_id, nama_layanan, wajib_upload) VALUES (:k,:n,:w)");
        $stmt->execute(['k'=>$kategoriId,'n'=>$nama,'w'=>$wajibUpload]);
        $message = 'Layanan baru berhasil ditambahkan.';
    }
}

// Hapus / nonaktifkan sub layanan
if (isset($_GET['hapus_sub'])) {
    $stmt = $pdo->prepare("UPDATE sub_layanan SET is_active = 0 WHERE id = :id");
    $stmt->execute(['id' => (int) $_GET['hapus_sub']]);
    header('Location: kategori.php');
    exit;
}

$kategoriList = getKategoriList($pdo);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Layanan - Admin ULT</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<div class="admin-shell">
  <?php include __DIR__ . '/_sidebar.php'; ?>
  <div class="admin-main">
    <div class="admin-topbar"><h2>Kelola Layanan</h2></div>
    <?php if ($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>

    <?php foreach ($kategoriList as $kategori): ?>
      <?php $subs = getSubLayananByKategori($pdo, (int)$kategori['id']); ?>
      <div class="mini-card" style="margin-bottom:20px;">
        <h3><i class="bi <?= e($kategori['icon']) ?>"></i> <?= e($kategori['nama_kategori']) ?> <small style="color:#9ca3af;">(Prefix: <?= e($kategori['kode_prefix']) ?>)</small></h3>
        <table class="data-table" style="margin:14px 0;">
          <thead><tr><th>Nama Layanan</th><th>Wajib Upload</th><th>Aksi</th></tr></thead>
          <tbody>
            <?php foreach ($subs as $s): ?>
            <tr>
              <td><?= e($s['nama_layanan']) ?></td>
              <td><?= $s['wajib_upload'] ? 'Ya' : 'Tidak' ?></td>
              <td><a href="?hapus_sub=<?= $s['id'] ?>" onclick="return confirm('Nonaktifkan layanan ini?');" style="color:#dc2626;">Nonaktifkan</a></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <form method="POST" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
          <input type="hidden" name="kategori_id" value="<?= $kategori['id'] ?>">
          <input type="text" name="nama_layanan" placeholder="Nama layanan baru" required style="padding:9px 12px; border-radius:8px; border:1px solid #d1d5db; flex:1; min-width:200px;">
          <label style="font-size:.85rem;"><input type="checkbox" name="wajib_upload"> Wajib Upload</label>
          <button type="submit" name="tambah_sub" value="1" class="btn-sm btn-panggil">Tambah</button>
        </form>
      </div>
    <?php endforeach; ?>
  </div>
</div>
</body>
</html>
