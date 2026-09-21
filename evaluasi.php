<?php
require_once __DIR__ . '/includes/functions.php';
$pdo = getDB();

$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT a.*, k.nama_kategori FROM antrian a JOIN kategori_layanan k ON k.id=a.kategori_id WHERE a.id = :id");
$stmt->execute(['id' => $id]);
$antrian = $stmt->fetch();

if (!$antrian) { header('Location: index.php'); exit; }

$sudahDinilai = $pdo->prepare("SELECT id FROM evaluasi WHERE antrian_id = :id");
$sudahDinilai->execute(['id' => $id]);
$alreadyRated = (bool) $sudahDinilai->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$alreadyRated) {
    $rating = max(1, min(5, (int) ($_POST['rating'] ?? 5)));
    $komentar = trim($_POST['komentar'] ?? '');
    $ins = $pdo->prepare("INSERT INTO evaluasi (antrian_id, rating, komentar) VALUES (:id,:r,:k)");
    $ins->execute(['id' => $id, 'r' => $rating, 'k' => $komentar ?: null]);
    $alreadyRated = true;
}

$pageTitle = 'Evaluasi Layanan';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-wrap">
  <div class="form-card" style="max-width:520px;">
    <?php if ($alreadyRated): ?>
      <h2 style="text-align:center;">Terima Kasih! 🙏</h2>
      <p class="desc">Penilaian Anda untuk tiket <strong><?= e($antrian['nomor_tiket']) ?></strong> sudah kami terima.</p>
    <?php else: ?>
      <h2>Evaluasi Layanan</h2>
      <p class="desc">Tiket <?= e($antrian['nomor_tiket']) ?> - <?= e($antrian['nama_kategori']) ?></p>
      <form method="POST">
        <div class="form-group">
          <label>Bagaimana kepuasan Anda terhadap layanan ini?</label>
          <select name="rating" required>
            <option value="5">★★★★★ Sangat Puas</option>
            <option value="4">★★★★☆ Puas</option>
            <option value="3">★★★☆☆ Cukup</option>
            <option value="2">★★☆☆☆ Kurang Puas</option>
            <option value="1">★☆☆☆☆ Tidak Puas</option>
          </select>
        </div>
        <div class="form-group">
          <label>Komentar / Saran (opsional)</label>
          <textarea name="komentar" placeholder="Tulis masukan Anda..."></textarea>
        </div>
        <button type="submit" class="btn-submit">Kirim Penilaian</button>
      </form>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
