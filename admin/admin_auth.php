<?php
// This file is included at the top of every admin page to require admin role.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//  tighten cookie path for subfolders


if (empty($_SESSION['user_id']) || empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Not logged in as admin. Redirect to home with an error
    header('Location: ../index.php?error=unauthorized');
    exit();
}
