<?php
// Load database connection
require_once __DIR__ . '/../handlers/db.php';

// Ensure admin is authenticated before accessing this page
require_once __DIR__ . '/admin_auth.php';

// Get PDO connection
$pdo = getDBConnection();

// Message holder for status feedback
$msg = '';

// Handle POST actions (role update / delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    $action = $_POST['action']; // Which action is performed?

    // 1. UPDATE USER ROLE
    if ($action === 'update_role' && !empty($_POST['user_id']) && isset($_POST['role'])) {

        $userId = (int)$_POST['user_id'];

        // Validate role to prevent malicious input
        $role = in_array($_POST['role'], ['admin','moderator','trainer','trainee']) ? $_POST['role'] : 'trainee';

        // Prevent removing admin privileges if only one admin remains
        if ($role !== 'admin') {

            // Count total admins
            $admins = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn();

            // Check if THIS user is an admin
            // (Note: execute() returns boolean, not count — this logic simply checks user exists)
            $isTargetAdmin = (int)$pdo->prepare("SELECT COUNT(*) FROM users WHERE user_id = ? AND role='admin'")
                                       ->execute([$userId]) ? true : false;

            // If trying to demote the last admin, block it
            if ($admins <= 1) {

                // Double-check user’s current role
                $stmt = $pdo->prepare("SELECT role FROM users WHERE user_id = ?");
                $stmt->execute([$userId]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row && $row['role'] === 'admin') {
                    $msg = "Cannot demote the last admin account.";
                }
            }
        }

        // If allowed, update role
        if (!$msg) {
            $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE user_id = ?");
            $stmt->execute([$role, $userId]);

            // Log the role update
            file_put_contents(
                __DIR__ . '/admin.log',
                date('c') . " | role_update | user:$userId | role:$role | by:{$_SESSION['user_id']}\n",
                FILE_APPEND
            );

            $msg = 'Role updated.';
        }
    }

   
    // 2. DELETE USER ACCOUNT
    if ($action === 'delete' && !empty($_POST['user_id'])) {

        $userId = (int)$_POST['user_id'];

        // Prevent admin from deleting themselves
        if ($userId === (int)$_SESSION['user_id']) {
            $msg = 'You cannot delete your own account.';
        } else {

            // Check the role of the user you are deleting
            $stmt = $pdo->prepare("SELECT role FROM users WHERE user_id = ?");
            $stmt->execute([$userId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Prevent deleting last admin
            if ($row && $row['role'] === 'admin') {

                $admins = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn();

                if ($admins <= 1) {
                    $msg = 'Cannot delete the last admin account.';
                }
            }

            // If allowed, delete the user
            if (!$msg) {
                $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
                $stmt->execute([$userId]);

                // Log deletion
                file_put_contents(
                    __DIR__ . '/admin.log',
                    date('c') . " | delete_user | user:$userId | by:{$_SESSION['user_id']}\n",
                    FILE_APPEND
                );

                $msg = 'User deleted.';
            }
        }
    }
}

// Fetch all users for display table
$users = $pdo->query("SELECT user_id,name,email,phone,role,created_at FROM users ORDER BY created_at DESC")
             ->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>manage-users/work-out-planner.dev</title>
  <link rel="stylesheet" href="admin_style.css">
</head>
<body>
  <div class="app">

    <!-- Sidebar navigation -->
    <aside class="sidebar">
      <div class="brand">
        <img src="../images/mikekay.webp" width="60" height="60" style="border-radius:8px;">
        <div>
          <h2>Workout Admin</h2>
          <div class="small">Users</div>
        </div>
      </div>

      <nav class="nav-links">
        <a href="admin_dashboard.php">Overview</a>
        <a href="manage_users.php" aria-current="page">Manage Users</a>
        <a href="manage_bookings.php">Manage Bookings</a>
        <a href="view_logs.php">View Logs</a>
        <a href="../index.php">View Site</a>
        <a href="../handlers/logout.php" class="small" style="color:#fda4af;">Logout</a>
      </nav>
    </aside>

    <!-- Main content area -->
    <main class="main">

      <div class="topbar">
        <div>
          <h1 style="margin:0">Manage Users</h1>
          <div class="small">Edit roles, delete accounts</div>
        </div>
        <div class="actions">
          <a class="btn" href="admin_dashboard.php">Back</a>
        </div>
      </div>

      <!-- User table -->
      <div class="card table-wrap">

        <!-- Display status messages -->
        <?php if ($msg): ?>
            <div class="notice"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <table class="table">
          <thead>
            <tr>
              <th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Joined</th><th>Actions</th>
            </tr>
          </thead>

          <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
              <td><?= htmlspecialchars($u['name']) ?></td>
              <td class="small"><?= htmlspecialchars($u['email']) ?></td>
              <td class="small"><?= htmlspecialchars($u['phone']) ?></td>
              <td><?= htmlspecialchars($u['role']) ?></td>
              <td class="small"><?= htmlspecialchars($u['created_at']) ?></td>

              <!-- Action buttons: update role / delete -->
              <td>
                <div class="actions">

                  <!-- Update role form -->
                  <form method="post" style="display:inline;">
                    <input type="hidden" name="user_id" value="<?= (int)$u['user_id'] ?>">
                    <input type="hidden" name="action" value="update_role">

                    <!-- Role dropdown -->
                    <select name="role" class="select">
                      <option value="trainee" <?= $u['role'] === 'trainee' ? 'selected' : '' ?>>trainee</option>
                      <option value="trainer" <?= $u['role'] === 'trainer' ? 'selected' : '' ?>>trainer</option>
                      <option value="moderator" <?= $u['role'] === 'moderator' ? 'selected' : '' ?>>moderator</option>
                      <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>admin</option>
                    </select>

                    <button class="btn" type="submit">Save</button>
                  </form>

                  <!-- Delete user form -->
                  <form method="post" style="display:inline;" onsubmit="return confirm('Delete user? This cannot be undone.');">
                    <input type="hidden" name="user_id" value="<?= (int)$u['user_id'] ?>">
                    <input type="hidden" name="action" value="delete">
                    <button class="btn btn-danger" type="submit">Delete</button>
                  </form>

                </div>
              </td>

            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

      </div>
    </main>
  </div>
</body>
</html>
