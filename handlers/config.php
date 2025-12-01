<?php
/*
| UNIVERSAL DATABASE CONFIG (LOCAL + RENDER + SSL)
*/

$app_env = getenv('APP_ENV') ?: 'local'; // local | prod
$app_debug = getenv('APP_DEBUG') === 'true' ? true : false;


/*
| LOCAL DEVELOPMENT (XAMPP)
*/
if ($app_env === 'local') {

    $db_host = 'localhost';
    $db_port = 3306;
    $db_name = 'online fitness training platform';
    $db_user = 'root';
    $db_pass = '';
    $use_ssl = false;

} else {


/*
| PRODUCTION (RENDER + AIVEN MYSQL)
| Render injects DB variables. If not, we fallback safely.
*/

    $db_host = getenv('DB_HOST') ?: 'mysql-d3c8168-youngmikeowuor7-5b90.g.aivencloud.com';
    $db_port = getenv('DB_PORT') ?: 20799;
    $db_name = getenv('DB_NAME') ?: 'defaultdb';
    $db_user = getenv('DB_USER') ?: 'avnadmin';
    $db_pass = getenv('DB_PASS') ?: 'AVNS_3YNUU-mWT-bDZYyb1tf';

    $use_ssl = true;

    // SSL certificate path
    $ssl_ca = __DIR__ . '/certs/ca.pem';

}


/*
| PDO CONNECTION (AUTO SSL WHEN REQUIRED)
*/

try {

    $dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";

    // Base PDO options
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    // Add SSL only in production
    if ($use_ssl) {
        if (!file_exists($ssl_ca)) {
            throw new Exception("SSL certificate not found at: " . $ssl_ca);
        }

        $options[PDO::MYSQL_ATTR_SSL_CA] = $ssl_ca;
    }

    // Connect
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);


    if ($app_debug) {
        echo "✅ Secure database connection successful!<br>";
    }

} catch (Exception $e) {

    if ($app_debug) {
        die("❌ Connection error: " . $e->getMessage());
    } else {
        die("❌ Secure database connection failed. Please try again later.");
    }

}


/*
| APP SETTINGS
*/

define('APP_NAME', 'Workout Planner');
define('APP_DEBUG', $app_debug);

?>
