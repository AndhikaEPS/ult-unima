<?php
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json');

$kategoriId = (int) ($_GET['kategori_id'] ?? 0);
if ($kategoriId <= 0) {
    echo json_encode([]);
    exit;
}
$pdo = getDB();
echo json_encode(getSubLayananByKategori($pdo, $kategoriId));
