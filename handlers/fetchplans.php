<?php
// handlers/fetch_plans.php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../lib/functions.php';

$stmt = $pdo->query("SELECT id, plan_name, price, features FROM plans ORDER BY price ASC");
$plans = $stmt->fetchAll();

jsonResponse('success', 'Plans fetched successfully.', $plans);
?>
