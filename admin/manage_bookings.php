<?php
require_once __DIR__ . '/../handlers/db.php';
require_once __DIR__ . '/../admin/admin_auth.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>manage-bookings/work-out-planner.dev</title>

    <style>
        *{box-sizing:border-box;font-family:Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;}
        html,body{height:100%;margin:auto;background:linear-gradient(180deg,#061226,#071126);color:#fff;}
        table { width: 100%; border-collapse: collapse; margin-top:20px; }
        th, td { padding: 10px; border: 1px solid #333; }
        th { background: #0b1220; }
        .btn { padding: 6px 10px; background: #007bff; color: #fff; text-decoration: none; border-radius: 4px; cursor:pointer; }
        .danger { background: #dc3545; }
        h2{
            font-weight: 700; font-size: 32px; 
            background: linear-gradient(90deg,#ffd166, #ff6b6b 100%);
            background-clip: text;color: transparent;
            display: flex; justify-content: center; margin-top:20px;
        }
        .filters { margin-top: 20px; display: flex; gap: 20px;}
        input, select { padding: 8px 10px; border-radius: 6px; border: 1px solid #999; }
        #results { margin-top: 20px; }
        .pagination a {
            padding: 6px 12px;
            background: #0d1b3d;
            color: #fff;
            margin-right: 4px;
            border-radius: 4px;
            text-decoration: none;
        }
        .pagination .active {
            background: #ff6b6b;
        }
        /* Mobile Responsive */
        @media(max-width:768px){
            h2{
                font-size:24px;
            }
            th, td{
                padding:10px;
                font-size:14px;
            }
            table{
                min-width:600px;
            }
        }
         @media(max-width:480px){
            h2{
                font-size:22px;
            }
            input, select{
                width:100%;
            }
            table{
                min-width:550px;
            }
        }
    </style>
</head>

<body>

<h2>Manage Bookings</h2>

<div class="filters">
    <input type="text" id="search" placeholder="Search name, email, phone, plan...">
    
    <select id="statusFilter">
        <option value="">All Status</option>
        <option value="Pending">Pending</option>
        <option value="Approved">Approved</option>
        <option value="Rejected">Rejected</option>
    </select>
</div>

<!-- AJAX Results Container -->
<div id="results">Loading...</div>

<script>
function loadBookings(page = 1) {
    const search = document.getElementById("search").value;
    const status = document.getElementById("statusFilter").value;

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax_booking.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhr.onload = function() {
        document.getElementById("results").innerHTML = this.responseText;
    }

    xhr.send("page=" + page + "&search=" + encodeURIComponent(search) + "&status=" + encodeURIComponent(status));
}

// Live search & filter
document.getElementById("search").onkeyup = () => loadBookings();
document.getElementById("statusFilter").onchange = () => loadBookings();

// Initial load
loadBookings();

// Handle status update
function updateStatus(id, status) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax_booking.php?action=updateStatus", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhr.onload = function() {
        loadBookings();
    }

    xhr.send("id=" + id + "&status=" + status);
}

// Delete booking
function deleteBooking(id) {
    if (!confirm("Are you sure you want to delete this booking?")) return;

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax_booking.php?action=deleteBooking", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhr.onload = function() {
        loadBookings();
    }

    xhr.send("id=" + id);
}
</script>

</body>
</html>
