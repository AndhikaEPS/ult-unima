<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

/**
 * Generate nomor tiket antrian, format: {PREFIX}-{NNN}, reset setiap hari,
 * berurutan per kategori. Contoh: A-001, A-002, U-001, dst.
 */
function generateNomorTiket(PDO $pdo, int $kategoriId, string $prefix): string
{
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) AS jumlah FROM antrian
         WHERE kategori_id = :kid AND tanggal_antrian = CURDATE()"
    );
    $stmt->execute(['kid' => $kategoriId]);
    $jumlah = (int) $stmt->fetch()['jumlah'];
    $urutan = $jumlah + 1;
    return $prefix . '-' . str_pad((string) $urutan, 3, '0', STR_PAD_LEFT);
}

/**
 * Menangani upload file (foto/dokumen) dengan validasi tipe & ukuran.
 * Mengembalikan array ['success' => bool, 'filename' => string|null, 'error' => string|null]
 */
function handleFileUpload(array $file): array
{
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => true, 'filename' => null, 'error' => null]; // upload opsional
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'filename' => null, 'error' => 'Terjadi kesalahan saat mengunggah file.'];
    }
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['success' => false, 'filename' => null, 'error' => 'Ukuran file maksimal 2MB.'];
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_UPLOAD_TYPES, true)) {
        return ['success' => false, 'filename' => null, 'error' => 'Format file harus JPG, PNG, atau PDF.'];
    }
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }
    $newName = uniqid('doc_', true) . '.' . $ext;
    $target = UPLOAD_DIR . $newName;
    if (!move_uploaded_file($file['tmp_name'], $target)) {
        return ['success' => false, 'filename' => null, 'error' => 'Gagal menyimpan file ke server.'];
    }
    return ['success' => true, 'filename' => $newName, 'error' => null];
}

function getKategoriList(PDO $pdo): array
{
    return $pdo->query("SELECT * FROM kategori_layanan WHERE is_active = 1 ORDER BY urutan ASC")->fetchAll();
}

function getSubLayananByKategori(PDO $pdo, int $kategoriId): array
{
    $stmt = $pdo->prepare("SELECT * FROM sub_layanan WHERE kategori_id = :kid AND is_active = 1 ORDER BY nama_layanan ASC");
    $stmt->execute(['kid' => $kategoriId]);
    return $stmt->fetchAll();
}

function getAntrianSedangDilayani(PDO $pdo): ?array
{
    $stmt = $pdo->query(
        "SELECT a.*, k.kode_prefix FROM antrian a
         JOIN kategori_layanan k ON k.id = a.kategori_id
         WHERE a.status = 'dipanggil' AND a.tanggal_antrian = CURDATE()
         ORDER BY a.dipanggil_at DESC LIMIT 1"
    );
    $row = $stmt->fetch();
    return $row ?: null;
}

function getTotalMenunggu(PDO $pdo): int
{
    $stmt = $pdo->query("SELECT COUNT(*) AS jumlah FROM antrian WHERE status = 'menunggu' AND tanggal_antrian = CURDATE()");
    return (int) $stmt->fetch()['jumlah'];
}

function isAdminLoggedIn(): bool
{
    return !empty($_SESSION['admin_id']);
}

function requireAdminLogin(): void
{
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function e(?string $str): string
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function formatTanggalIndo(string $datetime): string
{
    $bulan = ['', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $ts = strtotime($datetime);
    return date('d', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts) . ' ' . date('H:i', $ts) . ' WITA';
}
