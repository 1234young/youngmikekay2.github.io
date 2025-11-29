<?php
require_once __DIR__ . '/admin_auth.php';
$logfile = __DIR__ . '/admin.log';
$rows = [];
if (file_exists($logfile)) {
    $content = file($logfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    // show last 200 lines (adjust)
    $rows = array_slice($content, -200);
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Logs</title>
  <link rel="stylesheet" href="admin_style.css">
</head>
<body>
  <header class="header">
    <h1>Admin Logs</h1>
    <div><a class="btn" href="admin_dashboard.php">Back</a></div>
  </header>

  <div class="container">
    <div class="card">
      <h3>Recent Admin Activity</h3>
      <?php if (empty($rows)): ?>
        <div class="notice">No logs yet.</div>
      <?php else: ?>
        <pre style="white-space:pre-wrap;font-size:13px;color:#dbeafe;padding:10px;background:#071126;border-radius:8px;"><?= htmlspecialchars(implode("\n", array_reverse($rows))) ?></pre>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
