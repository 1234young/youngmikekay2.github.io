<?php
require_once __DIR__ . '/db.php';
session_start();

function logAdminAction($action, $details = '') {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        return false; // Only admins can log actions
    }

    $admin_id = $_SESSION['user_id'];

    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            INSERT INTO admin_activity_logs (admin_id, action, details)
            VALUES (:admin_id, :action, :details)
        ");
        $stmt->execute([
            'admin_id' => $admin_id,
            'action'   => $action,
            'details'  => $details
        ]);
        return true;
    } catch (PDOException $e) {
        error_log("Admin log error: " . $e->getMessage());
        return false;
    }
}
