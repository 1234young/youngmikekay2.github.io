<?php
//  database connection file
require_once __DIR__ . '/../handlers/db.php';

// admin authentication check
require_once __DIR__ . '/admin_auth.php';

// Get PDO database connection
$pdo = getDBConnection();

// Count total users
$userCount = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

// Count total bookings
$bookingCount = (int)$pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();

// Fetch the 6 most recent users
$recentUsers = $recentUsers = $pdo->query("SELECT user_id,name,email,role,created_at FROM users ORDER BY created_at DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>admin-dashboard/work-out-planner.dev</title>
  <!-- Load admin panel styles -->
  <link rel="stylesheet" href="admin_style.css">
</head>
<body>
  <div class="app">

    <!-- Sidebar Navigation -->
    <aside class="sidebar">
      <div class="brand">
        <!-- Logo -->
        <img src="../images/fitnessfirst.webp" alt="logo" width="60" height="60" style="border-radius:8px;">
        <div>
          <h2>Workout Admin</h2>
          <div class="small">Control Panel</div>
        </div>
      </div>

      <!-- Admin Menu Links -->
      <nav class="nav-links" aria-label="Admin">
        <a href="admin_dashboard.php">Overview <span class="small">›</span></a>
        <a href="manage_users.php">Manage Users <span class="small">(<?= $userCount ?>)</span></a>
        <a href="manage_bookings.php">Manage Bookings <span class="small">(<?= $bookingCount ?>)</span></a>
        <a href="view_logs.php">View Logs</a>
        <a href="../index.php" style="margin-top:12px;">View Site</a>
        <a href="../handlers/logout.php" class="small" style="color:#fda4af;">Logout</a>
      </nav>
    </aside>

    <!-- Main Dashboard Area -->
    <main class="main">

      <!-- Top Bar Greeting + Actions -->
      <div class="topbar">
        <div>
          <h1 style="margin:0">Dashboard</h1>
          <!-- Displays admin username if stored in session -->
          <div class="small">Welcome back, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></div>
        </div>

        <!-- Quick Access Buttons -->
        <div class="actions">
          <a class="btn" href="manage_users.php">Manage Users</a>
          <a class="btn" href="manage_bookings.php">Manage Bookings</a>
        </div>
      </div>

      <!-- Summary Cards -->
      <div class="cards">

        <div class="card">
          <h3>Registered users</h3>
          <div class="big"><?= $userCount ?></div>
          <div class="small" style="margin-top:8px;">Total users on the platform</div>
        </div>

        <div class="card">
          <h3>Bookings</h3>
          <!-- Total number of bookings -->
          <div class="big"><?= $bookingCount ?></div>
          <div class="small" style="margin-top:8px;">All bookings</div>
        </div>

        <div class="card">
          <h3>Quick actions</h3>
          <div style="margin-top:8px;">
            <a class="btn" href="manage_users.php">Users</a>
            <a class="btn" href="view_logs.php">Logs</a>
          </div>
        </div>

      </div>

      <!-- Recent Users Table -->
      <div style="margin-top:18px;" class="card table-wrap">
        <h3 style="margin-top:0">Recent users</h3>

        <table class="table" style="margin-top:8px;">
          <thead>
            <tr>
              <th>Name</th><th>Email</th><th>Role</th><th>Joined</th>
            </tr>
          </thead>

          <tbody>
            <?php foreach ($recentUsers as $r): ?>
              <tr>
                <!-- Display user name -->
                <td><?= htmlspecialchars($r['name']) ?></td>

                <!-- Display user email -->
                <td class="small"><?= htmlspecialchars($r['email']) ?></td>

                <!-- Display user role (admin/user) -->
                <td><?= htmlspecialchars($r['role']) ?></td>

                <!-- Display created_at timestamp -->
                <td class="small"><?= htmlspecialchars($r['created_at']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </main>
  </div>
</body>
</html>
