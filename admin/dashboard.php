<?php
require_once __DIR__ . '/../includes/functions.php';
requireAdminLogin();
$pdo = getDB();

$isPetugas = ($_SESSION['admin_role'] === 'petugas' && $_SESSION['admin_kategori_id']);

// Statistik ringkas hari ini
$totalHariIni = $pdo->query("SELECT COUNT(*) c FROM antrian WHERE tanggal_antrian = CURDATE()")->fetch()['c'];
$totalSelesai = $pdo->query("SELECT COUNT(*) c FROM antrian WHERE tanggal_antrian = CURDATE() AND status='selesai'")->fetch()['c'];
$totalMenunggu = getTotalMenunggu($pdo);
$sedang = getAntrianSedangDilayani($pdo);

// Daftar antrian hari ini (filter kategori bila petugas)
$sql = "SELECT a.*, k.nama_kategori, k.kode_prefix, s.nama_layanan
        FROM antrian a
        JOIN kategori_layanan k ON k.id = a.kategori_id
        LEFT JOIN sub_layanan s ON s.id = a.sub_layanan_id
        WHERE a.tanggal_antrian = CURDATE()";
$params = [];
if ($isPetugas) {
    $sql .= " AND a.kategori_id = :kid";
    $params['kid'] = $_SESSION['admin_kategori_id'];
}
$sql .= " ORDER BY FIELD(a.status,'dipanggil','dilayani','menunggu','selesai','dibatalkan'), a.created_at ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$daftarAntrian = $stmt->fetchAll();

$statusLabel = [
    'menunggu'   => 'Menunggu', 'dipanggil'  => 'Dipanggil', 'dilayani'   => 'Dilayani',
    'selesai'    => 'Selesai',  'dibatalkan' => 'Dibatalkan',
];

$pageTitle = 'Dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Admin - ULT</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<div class="admin-shell">
  <?php include __DIR__ . '/_sidebar.php'; ?>
  <div class="admin-main">
    <div class="admin-topbar">
      <h2>Dashboard</h2>
      <div>Halo, <strong><?= e($_SESSION['admin_nama']) ?></strong></div>
    </div>

    <div class="cards-row">
      <div class="mini-card"><div class="num"><?= $totalHariIni ?></div><div class="lbl">Total Antrian Hari Ini</div></div>
      <div class="mini-card"><div class="num"><?= $totalMenunggu ?></div><div class="lbl">Sedang Menunggu</div></div>
      <div class="mini-card"><div class="num"><?= $sedang ? e($sedang['nomor_tiket']) : '-' ?></div><div class="lbl">Sedang Dilayani</div></div>
      <div class="mini-card"><div class="num"><?= $totalSelesai ?></div><div class="lbl">Selesai Hari Ini</div></div>
    </div>

    <div class="table-scroll">
      <table class="data-table">
        <thead>
          <tr>
            <th>Tiket</th><th>Nama</th><th>Kategori</th><th>Layanan</th><th>Dokumen</th><th>Waktu</th><th>Status</th><th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($daftarAntrian)): ?>
            <tr><td colspan="8" style="text-align:center; padding:24px;">Belum ada antrian hari ini.</td></tr>
          <?php endif; ?>
          <?php foreach ($daftarAntrian as $a): ?>
            <tr>
              <td><strong><?= e($a['nomor_tiket']) ?></strong></td>
              <td><?= e($a['nama_lengkap']) ?><br><small style="color:#6b7280;"><?= e($a['nim_nip']) ?></small></td>
              <td><?= e($a['nama_kategori']) ?></td>
              <td><?= e($a['nama_layanan'] ?? '-') ?></td>
              <td>
                <?php if ($a['file_path']): ?>
                  <a href="../uploads/<?= e($a['file_path']) ?>" target="_blank"><i class="bi bi-file-earmark-check"></i> Lihat</a>
                <?php else: ?>-<?php endif; ?>
              </td>
              <td><?= date('H:i', strtotime($a['created_at'])) ?></td>
              <td><span class="badge badge-<?= e($a['status']) ?>"><?= e($statusLabel[$a['status']]) ?></span></td>
              <td>
                <form action="aksi_antrian.php" method="POST" style="display:flex; gap:6px; flex-wrap:wrap;">
                  <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                  <?php if ($a['status'] === 'menunggu'): ?>
                    <button type="submit" name="aksi" value="panggil" class="btn-sm btn-panggil">Panggil</button>
                  <?php elseif ($a['status'] === 'dipanggil'): ?>
                    <button type="submit" name="aksi" value="dilayani" class="btn-sm btn-panggil">Mulai Layani</button>
                  <?php elseif ($a['status'] === 'dilayani'): ?>
                    <button type="submit" name="aksi" value="selesai" class="btn-sm btn-selesai">Selesai</button>
                  <?php endif; ?>
                  <?php if (!in_array($a['status'], ['selesai','dibatalkan'], true)): ?>
                    <button type="submit" name="aksi" value="batal" class="btn-sm btn-batal" onclick="return confirm('Batalkan antrian ini?');">Batal</button>
                  <?php endif; ?>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
