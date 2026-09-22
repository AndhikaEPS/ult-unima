<?php
require_once __DIR__ . '/includes/functions.php';
$pdo = getDB();
$kategoriList = getKategoriList($pdo);
$pageTitle = 'Ambil Nomor Antrian';

$errors = $_SESSION['form_errors'] ?? [];
$old = $_SESSION['form_old'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['form_old']);

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-wrap">
   <div class="form-card">
    <a href="index.php" class="btn-back" title="Kembali ke Beranda"><i class="bi bi-arrow-left"></i></a>
    <h2>Ambil Nomor Antrian</h2>
    <p class="desc">Lengkapi data di bawah ini untuk mendapatkan nomor antrian Anda.</p>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-error">
        <ul style="margin:0; padding-left:18px;">
          <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form action="proses-antrian.php" method="POST" enctype="multipart/form-data">
      <div class="form-group">
        <label for="nama_lengkap">Nama Lengkap <span style="color:red;">*</span></label>
        <input type="text" id="nama_lengkap" name="nama_lengkap" required value="<?= e($old['nama_lengkap'] ?? '') ?>" placeholder="Contoh: John Doe">
      </div>

      <div class="form-group">
        <label for="nim_nip">NIM / No. ID <span style="color:red;">*</span></label>
        <input type="text" id="nim_nip" name="nim_nip" required value="<?= e($old['nim_nip'] ?? '') ?>" placeholder="Contoh: 20230123456">
      </div>

      <div class="form-group">
        <label for="fakultas_unit">Fakultas / Unit <span style="color:red;">*</span></label>
        <select id="fakultas_unit" name="fakultas_unit" required>
          <option value="">-- Pilih Fakultas/Unit --</option>
          <?php
          $fakultasOptions = [
              'Fakultas Ilmu Pendidikan', 'Fakultas Bahasa dan Seni', 'Fakultas Matematika dan IPA',
              'Fakultas Ilmu Sosial', 'Fakultas Teknik', 'Fakultas Ekonomi', 'Fakultas Ilmu Keolahragaan',
              'Fakultas Hukum', 'Pascasarjana', 'Umum / Non-Mahasiswa',
          ];
          foreach ($fakultasOptions as $f):
              $sel = (($old['fakultas_unit'] ?? '') === $f) ? 'selected' : '';
          ?>
            <option value="<?= e($f) ?>" <?= $sel ?>><?= e($f) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= e($old['email'] ?? '') ?>" placeholder="nama@email.com">
      </div>

      <div class="form-group">
        <label for="no_hp">No. HP / WhatsApp</label>
        <input type="text" id="no_hp" name="no_hp" value="<?= e($old['no_hp'] ?? '') ?>" placeholder="08xxxxxxxxxx">
      </div>

      <div class="form-group">
        <label for="kategori_id">Kategori Layanan <span style="color:red;">*</span></label>
        <select id="kategori_id" name="kategori_id" required>
          <option value="">-- Pilih Kategori Layanan --</option>
          <?php foreach ($kategoriList as $k):
              $sel = (($old['kategori_id'] ?? '') == $k['id']) ? 'selected' : ''; ?>
            <option value="<?= (int)$k['id'] ?>" <?= $sel ?>><?= e($k['nama_kategori']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="sub_layanan_id">Detail Layanan <span style="color:red;">*</span></label>
        <select id="sub_layanan_id" name="sub_layanan_id" required>
          <option value="">-- Pilih kategori terlebih dahulu --</option>
        </select>
      </div>

      <div class="form-group">
        <label for="detail_keperluan">Keterangan Tambahan</label>
        <textarea id="detail_keperluan" name="detail_keperluan" placeholder="Jelaskan kebutuhan Anda secara singkat (opsional)"><?= e($old['detail_keperluan'] ?? '') ?></textarea>
      </div>

      <div class="form-group">
        <label>Unggah Dokumen Pendukung (Foto/PDF, maks. 2MB)</label>
        <label class="upload-box" id="uploadBox" for="file_dokumen">
          <i class="bi bi-cloud-arrow-up" style="font-size:1.6rem;"></i>
          <div class="upload-text">Klik untuk unggah foto/dokumen (JPG, PNG, PDF)</div>
        </label>
        <input type="file" id="file_dokumen" name="file_dokumen" accept=".jpg,.jpeg,.png,.pdf" style="display:none;">
        <small class="hint">Contoh: KTM, KTP, surat pengantar, bukti pembayaran, dsb.</small>
      </div>

      <button type="submit" class="btn-submit">DAFTAR SEKARANG</button>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
