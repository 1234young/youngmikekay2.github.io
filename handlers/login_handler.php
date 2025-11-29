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

require_once __DIR__ . '/db.php';

// Detect if request is AJAX
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

try {
    $pdo = getDBConnection();
} catch (PDOException $e) {
    if ($isAjax) {
        header("Content-Type: application/json");
        echo json_encode(["success" => false, "message" => "Database error."]);
        exit;
    }
    exit("Database connection failed.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $source = trim($_POST['source'] ?? 'main'); // ← detect which login form sent it

    if (!$email || !$password) {
        if ($isAjax) {
            header("Content-Type: application/json");
            echo json_encode(["success" => false, "message" => "Please enter both email and password."]);
            exit;
        }
        echo "<p style='color:red;text-align:center'>Please enter both email and password.</p>";
        exit;
    }

    $stmt = $pdo->prepare("SELECT user_id, name, password, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {

       // Store session
       $_SESSION['user_id'] = $user['user_id'];
       $_SESSION['user_name'] = $user['name'];
       $_SESSION['role'] = $user['role'];          // the one your navbar checks
       $_SESSION['user_role'] = $user['role'];     // optional (keep if used elsewhere)
       $_SESSION['is_registered'] = true;

        if ($isAjax) {
            header("Content-Type: application/json");
            echo json_encode(["success" => true, "message" => "Login successful."]);
            exit;
        }

        // Redirect based on login source (UPDATED)
        if ($source === 'events') {
            header("Location: ../events.php?login=1");
        } else {
            header("Location: ../index.php?login=1");
        }
        exit;
    }

    // Invalid login
    if ($isAjax) {
        header("Content-Type: application/json");
        echo json_encode(["success" => false, "message" => "Invalid email or password."]);
        exit;
    }

    echo "<p style='color:red;text-align:center'>Invalid email or password.</p>";
    exit;
}

// Not POST
if ($isAjax) {
    header("Content-Type: application/json");
    echo json_encode(["success" => false, "message" => "Invalid request."]);
    exit;
}

header("Location: ../index.php?toast=login_failed");
exit;
