<?php
session_start();
require_once 'handlers/db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Access denied.");
}

$pdo = getDBConnection();
$stmt = $pdo->query("
    SELECT l.log_id, l.action, l.details, l.timestamp, u.name AS admin_name
    FROM admin_activity_logs l
    JOIN users u ON l.admin_id = u.user_id
    ORDER BY l.timestamp DESC
");
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head><title>Admin Logs</title></head>
<body>
<h2>Admin Activity Logs</h2>
<table border="1" cellpadding="5">
<tr>
<th>ID</th><th>Admin</th><th>Action</th><th>Details</th><th>Timestamp</th>
</tr>
<?php foreach ($logs as $log): ?>
<tr>
<td><?= $log['log_id'] ?></td>
<td><?= htmlspecialchars($log['admin_name']) ?></td>
<td><?= htmlspecialchars($log['action']) ?></td>
<td><?= htmlspecialchars($log['details']) ?></td>
<td><?= $log['timestamp'] ?></td>
</tr>
<?php endforeach; ?>
</table>
</body>
</html>
