<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Panduan';
require_once __DIR__ . '/includes/header.php';

$steps = [
    ['no' => 1, 'judul' => 'Akses Fitur Antrian Web',   'icon' => 'bi-laptop',       'ket' => 'Buka website ULT dan klik tombol "Ambil Nomor Antrian Online".'],
    ['no' => 2, 'judul' => 'Pilih Kategori Layanan',    'icon' => 'bi-grid-3x3-gap', 'ket' => 'Pilih kategori: Akademik, Beasiswa, Legalisir, Keuangan, atau lainnya.'],
    ['no' => 3, 'judul' => 'Isi Biodata Anda',           'icon' => 'bi-person-lines-fill', 'ket' => 'Lengkapi nama, NIM/No. ID, fakultas/unit, detail layanan, dan unggah dokumen jika diperlukan.'],
    ['no' => 4, 'judul' => 'Terima Tiket Antrian',       'icon' => 'bi-ticket-perforated', 'ket' => 'Nomor tiket antrian Anda akan tampil di layar, siap disimpan atau dicetak.'],
    ['no' => 5, 'judul' => 'Ruang Tunggu & Monitor',     'icon' => 'bi-display',      'ket' => 'Pantau status dan urutan antrian melalui papan antrian digital.'],
    ['no' => 6, 'judul' => 'Panggilan Petugas',          'icon' => 'bi-megaphone',    'ket' => 'Petugas akan memanggil nomor antrian Anda saat giliran tiba.'],
    ['no' => 7, 'judul' => 'Pelaksanaan Layanan',        'icon' => 'bi-briefcase',    'ket' => 'Petugas memproses kebutuhan layanan Anda di loket.'],
    ['no' => 8, 'judul' => 'Evaluasi Layanan',           'icon' => 'bi-star',         'ket' => 'Berikan penilaian kepuasan setelah layanan selesai.'],
];
?>

<div class="page-wrap">
  <div class="page-title">
    <h1>PANDUAN<br>UNIT LAYANAN TERPADU (ULT)</h1>
    <p class="sub">UNIVERSITAS NEGERI MANADO</p>
  </div>

  <div class="panduan-box">
    <div class="panduan-grid">
      <?php foreach ($steps as $s): ?>
        <div class="panduan-step">
          <h5><?= $s['no'] ?>. <?= strtoupper(e($s['judul'])) ?></h5>
          <div class="icon-box"><i class="bi <?= e($s['icon']) ?>"></i></div>
          <p style="font-size:.78rem; color:#374151; margin-top:10px;"><?= e($s['ket']) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
