<?php
require_once __DIR__ . '/includes/functions.php';
$pdo = getDB();
$kategoriList = getKategoriList($pdo);
$pageTitle = 'Layanan';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-wrap">
  <div class="page-title">
    <h1>Daftar Layanan</h1>
    <p style="color:#6b7280;">Pilih kategori layanan sesuai kebutuhan Anda</p>
  </div>

  <div class="layanan-grid">
    <?php foreach ($kategoriList as $kategori): ?>
      <?php $subList = getSubLayananByKategori($pdo, (int)$kategori['id']); ?>
      <div class="layanan-card">
        <h3><i class="bi <?= e($kategori['icon']) ?>"></i> <?= strtoupper(e($kategori['nama_kategori'])) ?></h3>
        <ul>
          <?php foreach ($subList as $sub): ?>
            <li><?= e($sub['nama_layanan']) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="cta-wrap" style="margin-top:50px;">
    <a href="ambil-antrian.php"><button class="btn-cta" style="font-size:1.1rem; padding:18px 40px;">Ambil Nomor Antrian Sekarang</button></a>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
