<?php
require_once __DIR__ . '/includes/functions.php';
$pdo = getDB();
$pengaturan = [];
foreach ($pdo->query("SELECT nama_setting, nilai FROM pengaturan") as $row) {
    $pengaturan[$row['nama_setting']] = $row['nilai'];
}
$sedang = getAntrianSedangDilayani($pdo);
$totalMenunggu = getTotalMenunggu($pdo);
$pageTitle = 'Beranda';
require_once __DIR__ . '/includes/header.php';
?>

<section class="hero">
  <p class="greet">Selamat Datang,</p>
  <h1>Di Sistem Informasi Layanan Digital</h1>
  <p class="tagline">Layanan Lebih Mudah, Antrian Lebih Teratur.</p>
</section>

<div class="cta-wrap">
  <a href="ambil-antrian.php"><button class="btn-cta">Ambil Nomor Antrian</button></a>
</div>

<div class="stats-bar">
  <div class="stat-box">
    <h4>JAM LAYANAN</h4>
    <div class="value"><?= e($pengaturan['jam_buka_senin_kamis'] ?? '08:00') ?>-<?= e($pengaturan['jam_tutup_senin_kamis'] ?? '16:00') ?></div>
  </div>
  <div class="stat-box">
    <h4>SEDANG DI LAYANI</h4>
    <div class="value" id="stat-sedang-dilayani"><?= $sedang ? e($sedang['nomor_tiket']) : '-' ?></div>
  </div>
  <div class="stat-box">
    <h4>Total Menunggu</h4>
    <div class="value" id="stat-total-menunggu"><?= $totalMenunggu ?></div>
  </div>
</div>

<div class="page-wrap" style="padding-top:0;">
  <div class="page-title">
    <h1>Kenapa Menggunakan Layanan Digital?</h1>
  </div>
  <div class="layanan-grid">
    <div class="layanan-card">
      <h3><i class="bi bi-phone"></i> DAFTAR DARI MANA SAJA</h3>
      <ul>
        <li>Ambil nomor antrian online tanpa perlu datang lebih awal</li>
        <li>Isi biodata dan unggah dokumen langsung dari HP atau laptop</li>
      </ul>
    </div>
    <div class="layanan-card">
      <h3><i class="bi bi-clock-history"></i> PANTAU ANTRIAN REAL-TIME</h3>
      <ul>
        <li>Lihat status "Sedang Dilayani" dan estimasi giliran Anda</li>
        <li>Papan antrian digital diperbarui otomatis</li>
      </ul>
    </div>
    <div class="layanan-card">
      <h3><i class="bi bi-clipboard-check"></i> TRANSPARAN & TERUKUR</h3>
      <ul>
        <li>Setiap layanan tercatat rapi dan dapat dievaluasi</li>
        <li>Beri penilaian kepuasan setelah dilayani</li>
      </ul>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
