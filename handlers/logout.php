<?php
session_start();

// Clear session variables
$_SESSION = [];

// Destroy session
session_destroy();

// Destroy session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Redirect user after logout
header("Location: ../index.php?toast=logged_out");
exit;
