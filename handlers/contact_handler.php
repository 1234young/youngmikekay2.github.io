<?php
require_once __DIR__ . '/db.php';
// Detect if HTTPS is being used
$isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;

session_start([
    'cookie_httponly' => true,       // Prevents JS access to session cookie
    'cookie_secure' => $isSecure,    // Only send cookie over HTTPS
    'cookie_samesite' => 'Strict',   // Prevent CSRF
    'use_strict_mode' => true,       // Prevent session fixation
    'use_only_cookies' => true       // Don't allow URL-based session IDs
]);

// Optional: regenerate session ID on new session for extra security
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message_body = trim($_POST['message_body'] ?? '');

    // ✅ Basic validation
    if ($name === '' || $email === '' || $message_body === '') {
        echo "<script>window.location.href='../index.php?toast=error';</script>";
        exit;
    }

    try {
        $pdo = getDBConnection();

        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, message_body, date_sent) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$name, $email, $message_body]);

        echo "<script>window.location.href='../index.php?toast=success';</script>";
        exit;

    } catch (PDOException $e) {
        echo "<script>window.location.href='../index.php?toast=error';</script>";
        exit;
    }
}
?>
