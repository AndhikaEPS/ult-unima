<?php
require_once __DIR__ . '/../includes/functions.php';
requireAdminLogin();
$pdo = getDB();

$rows = $pdo->query(
    "SELECT e.*, a.nomor_tiket, a.nama_lengkap, k.nama_kategori
     FROM evaluasi e
     JOIN antrian a ON a.id = e.antrian_id
     JOIN kategori_layanan k ON k.id = a.kategori_id
     ORDER BY e.created_at DESC LIMIT 100"
)->fetchAll();

$avg = $pdo->query("SELECT AVG(rating) avg_rating, COUNT(*) total FROM evaluasi")->fetch();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Evaluasi Layanan - Admin ULT</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<div class="admin-shell">
  <?php include __DIR__ . '/_sidebar.php'; ?>
  <div class="admin-main">
    <div class="admin-topbar"><h2>Evaluasi Layanan</h2></div>
    <div class="cards-row">
      <div class="mini-card"><div class="num"><?= $avg['total'] ? number_format((float)$avg['avg_rating'],2) : '-' ?></div><div class="lbl">Rata-rata Rating (dari <?= (int)$avg['total'] ?> ulasan)</div></div>
    </div>
    <div class="table-scroll">
      <table class="data-table">
        <thead><tr><th>Tiket</th><th>Nama</th><th>Kategori</th><th>Rating</th><th>Komentar</th><th>Waktu</th></tr></thead>
        <tbody>
          <?php if (empty($rows)): ?><tr><td colspan="6" style="text-align:center; padding:24px;">Belum ada evaluasi.</td></tr><?php endif; ?>
          <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= e($r['nomor_tiket']) ?></td>
            <td><?= e($r['nama_lengkap']) ?></td>
            <td><?= e($r['nama_kategori']) ?></td>
            <td><?= str_repeat('★', (int)$r['rating']) . str_repeat('☆', 5-(int)$r['rating']) ?></td>
            <td><?= e($r['komentar'] ?: '-') ?></td>
            <td><?= formatTanggalIndo($r['created_at']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
