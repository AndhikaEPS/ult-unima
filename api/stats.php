<?php
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json');

$pdo = getDB();
$sedang = getAntrianSedangDilayani($pdo);
echo json_encode([
    'sedang_dilayani' => $sedang ? $sedang['nomor_tiket'] : null,
    'total_menunggu'  => getTotalMenunggu($pdo),
]);
