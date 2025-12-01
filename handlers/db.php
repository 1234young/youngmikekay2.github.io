<?php
require_once __DIR__ . '/config.php';

function getDBConnection() {
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    // Detect if running on local environment
    $isLocal =
        in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1']) ||
        in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);

    try {
        if ($isLocal) {
           
            // LOCAL XAMPP DATABASE
            $dsn  = "mysql:host=localhost;dbname=online fitness training platform;charset=utf8mb4";
            $user = "root";
            $pass = "";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ];

        } else {
           
            // AIVEN CLOUD DATABASE (SSL SECURE)
            $sslCA = __DIR__ . '/../certs/ca.pem';  // Path to the CA certificate

            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";

            $user = DB_USER;
            $pass = DB_PASS;

            // *** SSL + PDO secure connection ***
            $options = [
                PDO::MYSQL_ATTR_SSL_CA => $sslCA,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ];
        }

        // Create database connection
        $pdo = new PDO($dsn, $user, $pass, $options);
        return $pdo;

    } catch (PDOException $e) {
        error_log("DB ERROR: " . $e->getMessage());
        throw $e;
    }
}
