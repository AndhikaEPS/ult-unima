<?php
require_once __DIR__ . '/../includes/functions.php';
requireAdminLogin();
$pdo = getDB();

$tanggal = $_GET['tanggal'] ?? date('Y-m-d');
$statusFilter = $_GET['status'] ?? '';
$kategoriFilter = $_GET['kategori'] ?? '';

$sql = "SELECT a.*, k.nama_kategori, s.nama_layanan FROM antrian a
        JOIN kategori_layanan k ON k.id = a.kategori_id
        LEFT JOIN sub_layanan s ON s.id = a.sub_layanan_id
        WHERE a.tanggal_antrian = :tgl";
$params = ['tgl' => $tanggal];
if ($statusFilter !== '') { $sql .= " AND a.status = :status"; $params['status'] = $statusFilter; }
if ($kategoriFilter !== '') { $sql .= " AND a.kategori_id = :kategori"; $params['kategori'] = $kategoriFilter; }
$sql .= " ORDER BY a.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$kategoriList = getKategoriList($pdo);
$statusLabel = ['menunggu'=>'Menunggu','dipanggil'=>'Dipanggil','dilayani'=>'Dilayani','selesai'=>'Selesai','dibatalkan'=>'Dibatalkan'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Riwayat Antrian - Admin ULT</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<div class="admin-shell">
  <?php include __DIR__ . '/_sidebar.php'; ?>
  <div class="admin-main">
    <div class="admin-topbar"><h2>Riwayat Antrian</h2></div>

    <form method="GET" style="display:flex; gap:12px; margin-bottom:20px; flex-wrap:wrap;">
      <input type="date" name="tanggal" value="<?= e($tanggal) ?>" style="padding:10px; border-radius:8px; border:1px solid #d1d5db;">
      <select name="status" style="padding:10px; border-radius:8px; border:1px solid #d1d5db;">
        <option value="">Semua Status</option>
        <?php foreach ($statusLabel as $k=>$v): ?>
          <option value="<?= $k ?>" <?= $statusFilter===$k?'selected':'' ?>><?= $v ?></option>
        <?php endforeach; ?>
      </select>
      <select name="kategori" style="padding:10px; border-radius:8px; border:1px solid #d1d5db;">
        <option value="">Semua Kategori</option>
        <?php foreach ($kategoriList as $k): ?>
          <option value="<?= $k['id'] ?>" <?= $kategoriFilter==$k['id']?'selected':'' ?>><?= e($k['nama_kategori']) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn-sm btn-panggil" style="padding:10px 20px;">Filter</button>
    </form>

    <div class="table-scroll">
      <table class="data-table">
        <thead><tr><th>Tiket</th><th>Nama</th><th>NIM/ID</th><th>Kategori</th><th>Layanan</th><th>Waktu Daftar</th><th>Status</th></tr></thead>
        <tbody>
          <?php if (empty($rows)): ?>
            <tr><td colspan="7" style="text-align:center; padding:24px;">Tidak ada data.</td></tr>
          <?php endif; ?>
          <?php foreach ($rows as $r): ?>
          <tr>
            <td><strong><?= e($r['nomor_tiket']) ?></strong></td>
            <td><?= e($r['nama_lengkap']) ?></td>
            <td><?= e($r['nim_nip']) ?></td>
            <td><?= e($r['nama_kategori']) ?></td>
            <td><?= e($r['nama_layanan'] ?? '-') ?></td>
            <td><?= formatTanggalIndo($r['created_at']) ?></td>
            <td><span class="badge badge-<?= e($r['status']) ?>"><?= $statusLabel[$r['status']] ?></span></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
