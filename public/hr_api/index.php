<?php
// Simple REST API for karyawan using PDO
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$cfg = require __DIR__ . '/db_config.php';
$dsn = "mysql:host={$cfg['host']};dbname={$cfg['dbname']};charset={$cfg['charset']}";
try {
    $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

// Routing: support PATH_INFO or query param
$path = '';
if (!empty($_SERVER['PATH_INFO'])) {
    $path = $_SERVER['PATH_INFO'];
} else {
    $script = $_SERVER['SCRIPT_NAME'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (str_starts_with($uri, $script)) {
        $path = substr($uri, strlen($script));
    } else {
        $path = substr($uri, strlen(dirname($script)));
    }
}

$segments = array_values(array_filter(explode('/', $path)));
if (($segments[0] ?? null) === 'index.php') {
    array_shift($segments);
    $segments = array_values($segments);
}

$resource = $segments[0] ?? null;
$id = $segments[1] ?? null;

if (!$resource && isset($_GET['resource'])) {
    $resource = $_GET['resource'];
    $id = $_GET['id'] ?? null;
}

if ($resource !== 'karyawan') {
    http_response_code(404);
    echo json_encode(['error' => 'Resource not found']);
    exit;
}

function getInputData() {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!is_array($data)) {
        $data = $_POST;
    }
    return $data;
}

function nullableDate($value) {
    return empty($value) ? null : $value;
}

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if ($id) {
                $stmt = $pdo->prepare('SELECT * FROM karyawan WHERE id = ?');
                $stmt->execute([$id]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$row) { http_response_code(404); echo json_encode(['error'=>'Not found']); break; }
                echo json_encode($row);
            } else {
                $stmt = $pdo->query('SELECT * FROM karyawan ORDER BY id DESC');
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($rows);
            }
            break;

        case 'POST':
            $data = getInputData();
            $stmt = $pdo->prepare('INSERT INTO karyawan (nama, tgl_lahir, gaji) VALUES (?, ?, ?)');
            $stmt->execute([ $data['nama'] ?? null, nullableDate($data['tgl_lahir'] ?? null), $data['gaji'] ?? 0 ]);
            $newId = $pdo->lastInsertId();
            http_response_code(201);
            echo json_encode(['id' => $newId]);
            break;

        case 'PUT':
            if (!$id) { http_response_code(400); echo json_encode(['error'=>'Missing id']); break; }
            $data = getInputData();
            $stmt = $pdo->prepare('UPDATE karyawan SET nama = ?, tgl_lahir = ?, gaji = ? WHERE id = ?');
            $stmt->execute([ $data['nama'] ?? null, nullableDate($data['tgl_lahir'] ?? null), $data['gaji'] ?? 0, $id ]);
            echo json_encode(['updated' => $stmt->rowCount()]);
            break;

        case 'DELETE':
            if (!$id) { http_response_code(400); echo json_encode(['error'=>'Missing id']); break; }
            $stmt = $pdo->prepare('DELETE FROM karyawan WHERE id = ?');
            $stmt->execute([$id]);
            echo json_encode(['deleted' => $stmt->rowCount()]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error'=>'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
