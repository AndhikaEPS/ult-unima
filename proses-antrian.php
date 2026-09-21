<?php
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ambil-antrian.php');
    exit;
}

$pdo = getDB();
$errors = [];

$nama       = trim($_POST['nama_lengkap'] ?? '');
$nimNip     = trim($_POST['nim_nip'] ?? '');
$fakultas   = trim($_POST['fakultas_unit'] ?? '');
$email      = trim($_POST['email'] ?? '');
$noHp       = trim($_POST['no_hp'] ?? '');
$kategoriId = (int) ($_POST['kategori_id'] ?? 0);
$subLayananId = !empty($_POST['sub_layanan_id']) ? (int) $_POST['sub_layanan_id'] : null;
$detail     = trim($_POST['detail_keperluan'] ?? '');

if ($nama === '') $errors[] = 'Nama lengkap wajib diisi.';
if ($nimNip === '') $errors[] = 'NIM/No. ID wajib diisi.';
if ($fakultas === '') $errors[] = 'Fakultas/Unit wajib dipilih.';
if ($kategoriId <= 0) $errors[] = 'Kategori layanan wajib dipilih.';
if (!$subLayananId) $errors[] = 'Detail layanan wajib dipilih.';
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid.';

// Validasi kategori benar-benar ada & aktif
$kategori = null;
if ($kategoriId > 0) {
    $stmt = $pdo->prepare("SELECT * FROM kategori_layanan WHERE id = :id AND is_active = 1");
    $stmt->execute(['id' => $kategoriId]);
    $kategori = $stmt->fetch();
    if (!$kategori) $errors[] = 'Kategori layanan tidak valid.';
}

// Upload file (opsional, tapi divalidasi jika ada)
$uploadResult = ['success' => true, 'filename' => null, 'error' => null];
if (!empty($_FILES['file_dokumen']) && $_FILES['file_dokumen']['error'] !== UPLOAD_ERR_NO_FILE) {
    $uploadResult = handleFileUpload($_FILES['file_dokumen']);
    if (!$uploadResult['success']) {
        $errors[] = $uploadResult['error'];
    }
}

if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['form_old'] = $_POST;
    header('Location: ambil-antrian.php');
    exit;
}

// Generate nomor tiket & simpan
$nomorTiket = generateNomorTiket($pdo, $kategoriId, $kategori['kode_prefix']);

$stmt = $pdo->prepare(
    "INSERT INTO antrian
        (nomor_tiket, nama_lengkap, nim_nip, email, no_hp, fakultas_unit, kategori_id, sub_layanan_id, detail_keperluan, file_path, file_original_name, status, tanggal_antrian)
     VALUES
        (:nomor_tiket, :nama, :nim, :email, :hp, :fakultas, :kategori_id, :sub_id, :detail, :file_path, :file_orig, 'menunggu', CURDATE())"
);
$stmt->execute([
    'nomor_tiket' => $nomorTiket,
    'nama'        => $nama,
    'nim'         => $nimNip,
    'email'       => $email ?: null,
    'hp'          => $noHp ?: null,
    'fakultas'    => $fakultas,
    'kategori_id' => $kategoriId,
    'sub_id'      => $subLayananId,
    'detail'      => $detail ?: null,
    'file_path'   => $uploadResult['filename'],
    'file_orig'   => $uploadResult['filename'] ? $_FILES['file_dokumen']['name'] : null,
]);

$antrianId = (int) $pdo->lastInsertId();
header('Location: tiket.php?id=' . $antrianId);
exit;
