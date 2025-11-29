<?php
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

header('Content-Type: application/json');
ini_set('display_errors', 0); // Never output errors directly
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

require_once __DIR__ . '/handlers/db.php'; 
require_once __DIR__ . '/handlers/admin_log.php'; // optional for logging

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Please log in to join events.'
    ]);
    exit;
}

$user_id = (int) $_SESSION['user_id'];

// Get event name
$event_name = trim($_POST['event_name'] ?? '');
if ($event_name === '') {
    echo json_encode(['success' => false, 'message' => 'No event name provided.']);
    exit;
}

try {
    $pdo = getDBConnection();

    // Ensure table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'event_participants'");
    if ($stmt->rowCount() === 0) {
        echo json_encode([
            'success' => false,
            'message' => "Event participants table not found. Contact admin."
        ]);
        exit;
    }

    // Check for duplicate join
    $stmt = $pdo->prepare("SELECT id FROM event_participants WHERE user_id = :user_id AND event_name = :event_name");
    $stmt->execute(['user_id' => $user_id, 'event_name' => $event_name]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'You already joined this event.']);
        exit;
    }

    // Insert participant
    $stmt = $pdo->prepare("INSERT INTO event_participants (user_id, event_name) VALUES (:user_id, :event_name)");
    $stmt->execute(['user_id' => $user_id, 'event_name' => $event_name]);

    // Optional admin logging
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
        logAdminAction('event_join', "Admin user {$_SESSION['user_id']} confirmed joining event '$event_name'");
    }

    echo json_encode(['success' => true, 'message' => "Successfully joined '$event_name'."]);
    exit;

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
    exit;
}
