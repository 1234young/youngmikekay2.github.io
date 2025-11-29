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
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role     = trim($_POST['selectRole'] ?? '');

    // Basic validation
    if ($name === '' || $email === '' || $phone === '' || $password === '' || $role === '' || $role === 'Select Role') {
        echo "Please fill in all fields.";
        exit;
    }

    try {
        $pdo = getDBConnection();

        // Check if email already exists
        $check = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            header("Location: ../index.php?error=email_exists");
            exit;
        }

        // Secure password hash
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert user with selected role
        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $hashedPassword, $role]);

        // Set session
        $_SESSION['is_registered'] = true;
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['user_name'] = $name;

        // Redirect after successful registration
        header("Location: ../index.php?success=1");
        exit;

    } catch (PDOException $e) {
        echo "Database error: " . htmlspecialchars($e->getMessage());
    }
}
?>
