<?php
require_once __DIR__ . '/../handlers/db.php';
$pdo = getDBConnection();

// Handle updating status
if (isset($_GET['action']) && $_GET['action'] === 'updateStatus') {
    $id = intval($_POST['id']);
    $status = trim($_POST['status']);

    $stmt = $pdo->prepare("UPDATE bookings SET status = :status WHERE booking_id = :id");
    $stmt->execute([':status' => $status, ':id' => $id]);
    exit;
}

// Handle delete
if (isset($_GET['action']) && $_GET['action'] === 'deleteBooking') {
    $id = intval($_POST['id']);

    $stmt = $pdo->prepare("DELETE FROM bookings WHERE booking_id = :id");
    $stmt->execute([':id' => $id]);
    exit;
}

// Normal AJAX listing
$search = trim($_POST['search'] ?? '');
$status = trim($_POST['status'] ?? '');
$page = intval($_POST['page'] ?? 1);
$limit = 5;
$offset = ($page - 1) * $limit;

$where = "WHERE 1";
$params = [];

if ($search !== "") {
    $where .= " AND (name LIKE :s OR email LIKE :s OR phone LIKE :s OR plan LIKE :s)";
    $params[':s'] = "%$search%";
}

if ($status !== "") {
    $where .= " AND status = :status";
    $params[':status'] = $status;
}

// Count rows for pagination
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM bookings $where");
$countStmt->execute($params);
$total = $countStmt->fetchColumn();
$pages = ceil($total / $limit);

// Fetch page data
$query = "
    SELECT booking_id, user_id, name, email, phone, date, plan, notes, status, timestamp
    FROM bookings
    $where
    ORDER BY booking_id DESC
    LIMIT $limit OFFSET $offset
";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Build table
echo "<table>
<tr>
    <th>ID</th>
    <th>User</th>
    <th>Name</th>
    <th>Email</th>
    <th>Phone</th>
    <th>Date</th>
    <th>Plan</th>
    <th>Notes</th>
    <th>Status</th>
    <th>Actions</th>
</tr>";

foreach ($rows as $b) {
    echo "<tr>
        <td>{$b['booking_id']}</td>
        <td>{$b['user_id']}</td>
        <td>{$b['name']}</td>
        <td>{$b['email']}</td>
        <td>{$b['phone']}</td>
        <td>{$b['date']}</td>
        <td>{$b['plan']}</td>
        <td>{$b['notes']}</td>

        <td>
            <select onchange=\"updateStatus({$b['booking_id']}, this.value)\">
                <option ".($b['status']=="Pending"?"selected":"").">Pending</option>
                <option ".($b['status']=="Approved"?"selected":"").">Approved</option>
                <option ".($b['status']=="Rejected"?"selected":"").">Rejected</option>
            </select>
        </td>

        <td>
            <button class='btn danger' onclick=\"deleteBooking({$b['booking_id']})\">Delete</button>
        </td>
    </tr>";
}

echo "</table>";

// Pagination links
echo "<div class='pagination'>";
for ($i = 1; $i <= $pages; $i++) {
    $active = ($i == $page) ? "active" : "";
    echo "<a class='$active' href='#' onclick='loadBookings($i)'>$i</a>";
}
echo "</div>";
