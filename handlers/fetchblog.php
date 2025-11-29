<?php
// handlers/fetch_blogs.php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../lib/functions.php';

$stmt = $pdo->query("SELECT id, title, excerpt, image, date_posted FROM blogs ORDER BY date_posted DESC LIMIT 6");
$blogs = $stmt->fetchAll();

jsonResponse('success', 'Blogs fetched successfully.', $blogs);
?>
