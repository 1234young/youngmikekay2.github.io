<?php
// handlers/contact_handler.php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../lib/functions.php';
require_once __DIR__ . '/../lib/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verifyToken($token)) {
        jsonResponse('error', 'Invalid CSRF token.');
    }

    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        jsonResponse('error', 'All fields are required.');
    }

    $stmt = $pdo->prepare("INSERT INTO contacts (name, email, message, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$name, $email, $message]);

    jsonResponse('success', 'Message sent successfully!');
}
?>
