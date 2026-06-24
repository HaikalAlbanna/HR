<?php
// Simple server-rendered CRUD form for karyawan
$cfg = require __DIR__ . '/../hr_api/db_config.php';
$dsn = "mysql:host={$cfg['host']};dbname={$cfg['dbname']};charset={$cfg['charset']}";
try {
    $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    echo "DB connection error: " . htmlspecialchars($e->getMessage());
    exit;
}

// Handle edit mode
$edit = null;
if (!empty($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM karyawan WHERE id = ?');
    $stmt->execute([$_GET['edit']]);
    $edit = $stmt->fetch(PDO::FETCH_ASSOC);
}

$stmt = $pdo->query('SELECT * FROM karyawan ORDER BY id DESC');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>CRUD Karyawan</title>
  <style>body{font-family:Arial,Helvetica,sans-serif}table{border-collapse:collapse;width:100%}td,th{border:1px solid #ccc;padding:8px}</style>
</head>
<body>
  <h1>CRUD Karyawan</h1>

  <form method="post" action="handle.php">
    <input type="hidden" name="action" value="<?php echo $edit ? 'update' : 'create'; ?>">
    <?php if ($edit): ?><input type="hidden" name="id" value="<?php echo htmlspecialchars($edit['id']); ?>"><?php endif; ?>
    <label>Nama: <input name="nama" required value="<?php echo $edit ? htmlspecialchars($edit['nama']) : ''; ?>"></label><br>
    <label>Tgl Lahir: <input name="tgl_lahir" type="date" value="<?php echo $edit ? htmlspecialchars($edit['tgl_lahir']) : ''; ?>"></label><br>
    <label>Gaji: <input name="gaji" type="number" step="0.01" value="<?php echo $edit ? htmlspecialchars($edit['gaji']) : '0.00'; ?>"></label><br>
    <button type="submit"><?php echo $edit ? 'Update' : 'Create'; ?></button>
    <?php if ($edit): ?><a href="index.php">Cancel</a><?php endif; ?>
  </form>

  <h2>Daftar Karyawan</h2>
  <table>
    <tr><th>ID</th><th>Nama</th><th>Tgl Lahir</th><th>Gaji</th><th>Aksi</th></tr>
    <?php foreach ($rows as $r): ?>
    <tr>
      <td><?php echo $r['id']; ?></td>
      <td><?php echo htmlspecialchars($r['nama']); ?></td>
      <td><?php echo htmlspecialchars($r['tgl_lahir']); ?></td>
      <td><?php echo htmlspecialchars($r['gaji']); ?></td>
      <td>
        <a href="?edit=<?php echo $r['id']; ?>">Edit</a>
        <form method="post" action="handle.php" style="display:inline" onsubmit="return confirm('Hapus?');">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
          <button type="submit">Delete</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
</body>
</html>
