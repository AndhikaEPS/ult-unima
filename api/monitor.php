<?php
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json');

$pdo = getDB();
$sedang = getAntrianSedangDilayani($pdo);

$stmt = $pdo->query(
    "SELECT nomor_tiket FROM antrian
     WHERE status = 'menunggu' AND tanggal_antrian = CURDATE()
     ORDER BY created_at ASC LIMIT 12"
);
$berikutnya = array_column($stmt->fetchAll(), 'nomor_tiket');

echo json_encode([
    'sedang_dilayani'    => $sedang ? $sedang['nomor_tiket'] : null,
    'antrian_berikutnya' => $berikutnya,
]);
