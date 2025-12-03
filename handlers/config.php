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

    $db_host = '127.0.0.1'; // force TCP
    $db_port = 3306;
    $db_name = 'online_fitness';
    $db_user = 'root';
    $db_pass = '';
    $use_ssl = false;

} else {


/*
| PRODUCTION (RENDER + AIVEN MYSQL)
| Render injects DB variables. If not, we fallback safely.
*/

    $db_host = getenv('DB_HOST') ?: 'mysql-db-youngmikeowuor7-5b90.g.aivencloud.com';
    $db_port = getenv('DB_PORT') ?: 20799;
    $db_name = getenv('DB_NAME') ?: 'defaultdb';
    $db_user = getenv('DB_USER') ?: 'avnadmin';
   $db_pass = getenv('DB_PASS');
      if (!$db_pass) {
         die("Database password is not set in environment variables.");
      }


    $use_ssl = true;

    // SSL certificate path
    $ssl_ca = __DIR__ . '/../certs/ca.pem';

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

define('APP_NAME', 'work-out-planner');
define('APP_DEBUG', $app_debug);

?>