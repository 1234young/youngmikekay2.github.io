<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/admin_log.php';
if (session_status() === PHP_SESSION_NONE) {
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
}


if (!isset($_SESSION['user_id'])) {
    header('Location: ../booking.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = getDBConnection();

    // collect and sanitize form inputs
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $date  = trim($_POST['date'] ?? '');
    $plan  = trim($_POST['plan'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    // basic validation
    if ($name === '' || $email === '' || $phone === '' || $plan === '') {
        echo "All required fields must be filled.";
        exit;
    }

    try {
        // insert data into 'bookings' table
        $stmt = $pdo->prepare("
            INSERT INTO bookings (user_id, name, email, phone, date, plan, notes, status, timestamp)
            VALUES (:user_id, :name, :email, :phone, :date, :plan, :notes, 'Pending', NOW())
        ");

        $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':name'    => $name,
            ':email'   => $email,
            ':phone'   => $phone,
            ':date'    => $date,
            ':plan'    => $plan,
            ':notes'   => $notes
        ]);

        // log user booking action
        logAdminAction('new_booking', "User {$_SESSION['user_id']} booked a session: $plan on $date");

        // redirect after success
        header('Location: ../booking.php?success=1');
        exit;

    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }

} else {
    echo "Invalid request.";
}
