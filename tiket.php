<?php
require_once __DIR__ . '/includes/functions.php';
$pdo = getDB();

$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare(
    "SELECT a.*, k.nama_kategori, s.nama_layanan
     FROM antrian a
     JOIN kategori_layanan k ON k.id = a.kategori_id
     LEFT JOIN sub_layanan s ON s.id = a.sub_layanan_id
     WHERE a.id = :id"
);
$stmt->execute(['id' => $id]);
$antrian = $stmt->fetch();

if (!$antrian) {
    header('Location: index.php');
    exit;
}

// Hitung posisi antrian (berapa orang di depan pada kategori sama, status menunggu)
$stmtPos = $pdo->prepare(
    "SELECT COUNT(*) AS jumlah FROM antrian
     WHERE kategori_id = :kid AND status = 'menunggu' AND tanggal_antrian = CURDATE()
     AND created_at < :created_at"
);
$stmtPos->execute(['kid' => $antrian['kategori_id'], 'created_at' => $antrian['created_at']]);
$posisiDepan = (int) $stmtPos->fetch()['jumlah'];

$pageTitle = 'Tiket Antrian';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-wrap">
  <div class="tiket-card">
    <div class="label">NOMOR ANTRIAN ANDA</div>
    <div class="nomor"><?= e($antrian['nomor_tiket']) ?></div>

    <div class="info-row"><span>Nama</span><span><?= e($antrian['nama_lengkap']) ?></span></div>
    <div class="info-row"><span>NIM/No. ID</span><span><?= e($antrian['nim_nip']) ?></span></div>
    <div class="info-row"><span>Kategori</span><span><?= e($antrian['nama_kategori']) ?></span></div>
    <div class="info-row"><span>Layanan</span><span><?= e($antrian['nama_layanan'] ?? '-') ?></span></div>
    <div class="info-row"><span>Waktu Daftar</span><span><?= formatTanggalIndo($antrian['created_at']) ?></span></div>
    <div class="info-row"><span>Antrian di depan Anda</span><span><?= $posisiDepan ?> orang</span></div>

    <div class="tiket-actions">
      <button class="btn-outline-light" onclick="window.print()"><i class="bi bi-printer"></i> Cetak</button>
      <a href="status.php?tiket=<?= urlencode($antrian['nomor_tiket']) ?>" class="btn-fill-light">Pantau Status</a>
    </div>
  </div>

  <p style="text-align:center; color:#6b7280; margin-top:20px;">
    Simpan nomor tiket ini. Anda dapat memantau status antrian kapan saja melalui halaman <a href="status.php" style="color:#4f46e5; font-weight:600;">Cek Status</a>.
  </p>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
