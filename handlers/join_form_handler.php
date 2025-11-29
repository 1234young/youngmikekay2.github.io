<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$fullname    = trim($data['fullname'] ?? '');
$email       = trim($data['email'] ?? '');
$phone       = trim($data['phone'] ?? '');
$location    = trim($data['location'] ?? '');
$goals       = trim($data['goals'] ?? '');
$sessionType = trim($data['sessionType'] ?? ''); // FIXED key name

if ($fullname === '' || $email === '' || $phone === '' || $location === '' || $goals === '' || $sessionType === '') {
    echo json_encode(["success" => false, "message" => "Please fill in all required fields."]);
    exit;
}

try {
    $pdo = getDBConnection();

    $stmt = $pdo->prepare("INSERT INTO join_requests 
        (fullname, email, phone, location, goals, session_type, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$fullname, $email, $phone, $location, $goals, $sessionType]);

    echo json_encode(["success" => true, "message" => "Thank you for joining! We'll contact you soon."]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
