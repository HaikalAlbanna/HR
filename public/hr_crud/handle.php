<?php
// Handle form submissions for create/update/delete
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$cfg = require __DIR__ . '/../hr_api/db_config.php';
$dsn = "mysql:host={$cfg['host']};dbname={$cfg['dbname']};charset={$cfg['charset']}";
try {
    $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    die('DB error: ' . $e->getMessage());
}

$action = $_POST['action'] ?? '';
if ($action === 'create') {
    $stmt = $pdo->prepare('INSERT INTO karyawan (nama, tgl_lahir, gaji) VALUES (?, ?, ?)');
    $stmt->execute([ $_POST['nama'] ?? null, $_POST['tgl_lahir'] ?? null, $_POST['gaji'] ?? 0 ]);
    header('Location: index.php');
    exit;
} elseif ($action === 'update') {
    $stmt = $pdo->prepare('UPDATE karyawan SET nama = ?, tgl_lahir = ?, gaji = ? WHERE id = ?');
    $stmt->execute([ $_POST['nama'] ?? null, $_POST['tgl_lahir'] ?? null, $_POST['gaji'] ?? 0, $_POST['id'] ]);
    header('Location: index.php');
    exit;
} elseif ($action === 'delete') {
    $stmt = $pdo->prepare('DELETE FROM karyawan WHERE id = ?');
    $stmt->execute([ $_POST['id'] ]);
    header('Location: index.php');
    exit;
}

header('Location: index.php');
