<?php
//  Secure PDO Database connection with SSL

// Read environment variables
$db_host   = getenv('DB_HOST');
$db_port   = getenv('DB_PORT');
$db_name   = getenv('DB_NAME');
$db_user   = getenv('DB_USER');
$db_pass   = getenv('DB_PASS');
$app_debug = getenv('APP_DEBUG') === 'true';

// Path to your Aiven CA certificate
// Upload the CA certificate (ca.pem) to your project, e.g., in a 'certs' folder
$ssl_ca = __DIR__ . '/certs/ca.pem';

try {
    // DSN with SSL options
    $dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";

    // PDO options
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_SSL_CA       => $ssl_ca,  // SSL CA certificate
    ];

    // Create PDO instance
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);

    if ($app_debug) {
        echo "✅ Secure database connection successful!";
    }
} catch (PDOException $e) {
    if ($app_debug) {
        die("❌ Secure database connection failed: " . $e->getMessage());
    } else {
        die("❌ Secure database connection failed. Please try again later.");
    }
}


// APPLICATION SETTINGS
define('APP_NAME', 'work-out-lanner');
define('APP_DEBUG', getenv('APP_DEBUG') ?: true);

?>
