<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

/* ===== ADD: DB CONNECTION ===== */
include '../db.php';

/* ===== ADD: RECENT EVENTS QUERY ===== */
$query = "SELECT * FROM events ORDER BY id DESC LIMIT 5";
$result = $conn->query($query);

/* ===== ADD: COUNTS ===== */
// TOTAL EVENTS
$total_events = $conn->query("SELECT COUNT(*) as total FROM events")
->fetch_assoc()['total'];

// UPCOMING EVENTS
$upcoming = $conn->query("
    SELECT COUNT(*) as total 
    FROM events 
    WHERE event_date >= CURDATE()
")->fetch_assoc()['total'];

// COMPLETED EVENTS
$completed = $conn->query("
    SELECT COUNT(*) as total 
    FROM events 
    WHERE event_date < CURDATE()
")->fetch_assoc()['total'];

// TOTAL CLIENTS
$clients = $conn->query("
    SELECT COUNT(DISTINCT client_name) as total 
    FROM events
")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin.css?v=1">
</head>

<body>

<div class="container">

<!-- SIDEBAR -->
<aside class="sidebar">
    <div>
        <h2 class="logo">Event Admin</h2>

        <nav>
            <a href="#" onclick="showDashboard()" class="active">Dashboard</a>
            <a href="add_event.php">Add Event</a>
            <a href="upcoming.php" onclick="showUpcoming()">Upcoming Events</a> 
            <a href="complete_event.php">Completed Events</a>
            <a href="pending.php">Pending Requests</a>
            <a href="history.php">Event History</a>
        </nav>
    </div>

    <a href="../logout.php" class="logout">Logout</a>
</aside>

<!-- MAIN -->
<main class="main">

<!-- ===== DASHBOARD CONTENT (WRAPPED) ===== -->
<div id="dashboard-content">

    <!-- TOPBAR -->
    <div class="topbar">
        <h1>Dashboard</h1>
        <div class="admin-box">Admin</div>
    </div>

    <!-- CARDS -->
    <div class="cards">
        <div class="card"><h3>Total Events</h3><h1><?php echo $total_events; ?></h1></div>
        <div class="card"><h3>Upcoming Events</h3><h1><?php echo $upcoming; ?></h1></div>
        <div class="card"><h3>Completed Events</h3><h1><?php echo $completed; ?></h1></div>
        <div class="card"><h3>Total Clients</h3><h1><?php echo $clients; ?></h1></div>
    </div>

    <!-- TABLE -->
    <div class="panel">
        <div class="panel-header">
            <h2>Recent Reservations</h2>
        </div>

        <table>
            <tr>
                <th>Event</th>
                <th>Type</th>
                <th>Date</th>
                <th>Status</th>
            </tr>

            <?php if($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['event_name']; ?></td>
                        <td><?php echo $row['event_type']; ?></td>
                        <td><?php echo $row['event_date']; ?></td>
                        <td><span class="badge">Saved</span></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No Data</td>
                </tr>
            <?php endif; ?>

        </table>
    </div>

</div>
<!-- ===== END DASHBOARD ===== -->


<!-- ===== UPCOMING CONTENT (ADDED) ===== -->
<div id="upcoming-content" style="display:none;">

    <div class="topbar">
        <h1>Upcoming Events</h1>
        <div class="admin-box">Admin</div>
    </div>

    <div class="panel">

        <table>
            <tr>
                <th>Event</th>
                <th>Type</th>
                <th>Date</th>
            </tr>

            <?php
            $upcoming_query = "SELECT * FROM events 
                              WHERE event_date >= CURDATE() 
                              ORDER BY event_date ASC";
            $upcoming_result = $conn->query($upcoming_query);
            ?>

            <?php if($upcoming_result->num_rows > 0): ?>
                <?php while($row = $upcoming_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['event_name']; ?></td>
                        <td><?php echo $row['event_type']; ?></td>
                        <td><?php echo $row['event_date']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No Upcoming Events</td>
                </tr>
            <?php endif; ?>

        </table>

    </div>

</div>
<!-- ===== END UPCOMING ===== -->

</main>
</div>

<!-- ===== JS (ADDED) ===== -->
<script>
function showUpcoming() {
    document.getElementById("dashboard-content").style.display = "none";
    document.getElementById("upcoming-content").style.display = "block";
}

function showDashboard() {
    document.getElementById("dashboard-content").style.display = "block";
    document.getElementById("upcoming-content").style.display = "none";
}
</script>

</body>
</html>