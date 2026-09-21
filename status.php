<?php
require_once __DIR__ . '/includes/functions.php';
$pdo = getDB();
$pageTitle = 'Cek Status Antrian';

$antrian = null;
$notFound = false;
$nomorTiket = trim($_GET['tiket'] ?? '');

if ($nomorTiket !== '') {
    $stmt = $pdo->prepare(
        "SELECT a.*, k.nama_kategori FROM antrian a
         JOIN kategori_layanan k ON k.id = a.kategori_id
         WHERE a.nomor_tiket = :tiket AND a.tanggal_antrian = CURDATE()"
    );
    $stmt->execute(['tiket' => $nomorTiket]);
    $antrian = $stmt->fetch();
    if (!$antrian) $notFound = true;
}

$statusLabel = [
    'menunggu'   => 'Menunggu Panggilan',
    'dipanggil'  => 'Sedang Dipanggil',
    'dilayani'   => 'Sedang Dilayani',
    'selesai'    => 'Selesai',
    'dibatalkan' => 'Dibatalkan',
];

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-wrap">
  <div class="form-card" style="max-width:520px;">
    <h2>Cek Status Antrian</h2>
    <p class="desc">Masukkan nomor tiket Anda (contoh: A-001)</p>
    <form method="GET" style="display:flex; gap:10px; margin-bottom:24px;">
      <input type="text" name="tiket" value="<?= e($nomorTiket) ?>" placeholder="Nomor Tiket" style="flex:1; padding:12px 16px; border-radius:12px; border:1px solid #d1d5db;">
      <button type="submit" class="btn-submit" style="width:auto; padding:12px 22px;">Cek</button>
    </form>

    <?php if ($notFound): ?>
      <div class="alert alert-error">Nomor tiket tidak ditemukan untuk hari ini.</div>
    <?php elseif ($antrian): ?>
      <div class="info-row" style="display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid #eee;"><span>Nomor Tiket</span><strong><?= e($antrian['nomor_tiket']) ?></strong></div>
      <div class="info-row" style="display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid #eee;"><span>Nama</span><strong><?= e($antrian['nama_lengkap']) ?></strong></div>
      <div class="info-row" style="display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid #eee;"><span>Kategori</span><strong><?= e($antrian['nama_kategori']) ?></strong></div>
      <div class="info-row" style="display:flex; justify-content:space-between; padding:14px 0;"><span>Status</span><span class="badge badge-<?= e($antrian['status']) ?>"><?= e($statusLabel[$antrian['status']] ?? $antrian['status']) ?></span></div>

      <?php if ($antrian['status'] === 'selesai'): ?>
        <a href="evaluasi.php?id=<?= (int)$antrian['id'] ?>"><button class="btn-submit" style="margin-top:10px;">Beri Penilaian Layanan</button></a>
      <?php endif; ?>
    <?php endif; ?>
  </div>

  <p style="text-align:center; margin-top:30px;">
    <a href="papan-antrian.php" style="color:#4f46e5; font-weight:600;"><i class="bi bi-display"></i> Lihat Papan Antrian Langsung</a>
  </p>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
