<?php
require_once __DIR__ . '/../includes/functions.php';
requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$pdo = getDB();
$id   = (int) ($_POST['id'] ?? 0);
$aksi = $_POST['aksi'] ?? '';
$adminId = $_SESSION['admin_id'];

$map = [
    'panggil'  => ['status' => 'dipanggil', 'extra' => "dipanggil_at = NOW(), dilayani_oleh = :admin_id"],
    'dilayani' => ['status' => 'dilayani',  'extra' => "dilayani_oleh = :admin_id"],
    'selesai'  => ['status' => 'selesai',   'extra' => "selesai_at = NOW()"],
    'batal'    => ['status' => 'dibatalkan','extra' => ""],
];

if ($id > 0 && isset($map[$aksi])) {
    $cfg = $map[$aksi];
    $sql = "UPDATE antrian SET status = :status" . ($cfg['extra'] ? ", {$cfg['extra']}" : "") . " WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $params = ['status' => $cfg['status'], 'id' => $id];
    if (strpos($cfg['extra'], ':admin_id') !== false) {
        $params['admin_id'] = $adminId;
    }
    $stmt->execute($params);
}

header('Location: dashboard.php');
exit;
