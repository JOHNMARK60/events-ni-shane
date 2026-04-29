<?php
session_start();
include '../db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

/* ALL EVENTS */
$query = "SELECT * FROM events ORDER BY event_date DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Event History</title>

    <link rel="stylesheet" href="admin.css?v=1">
    <link rel="stylesheet" href="history.css?v=1">
</head>

<body>

<div class="container">

<!-- SIDEBAR -->
<aside class="sidebar">
    <div>
        <h2 class="logo">Event Admin</h2>

        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="add_event.php">Add Event</a>
            <a href="upcoming.php">Upcoming Events</a>
            <a href="pending.php">Pending Requests</a>
            <a href="complete_event.php">Completed Events</a>
            <a href="history.php" class="active">Event History</a>
        </nav>
    </div>

    <a href="../logout.php" class="logout">Logout</a>
</aside>

<!-- MAIN -->
<main class="main">

    <div class="topbar">
        <h1>Event History</h1>
        <div class="admin-box">Admin</div>
    </div>

    <div class="event-grid">

    <?php if($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>

            <div class="event-card">
                <h3><?php echo $row['event_name']; ?></h3>

                <p><b>Client:</b> <?php echo $row['client_name']; ?></p>
                <p><b>Type:</b> <?php echo $row['event_type']; ?></p>
                <p><b>Date:</b> <?php echo $row['event_date']; ?></p>
                <p><b>Time:</b> <?php echo $row['event_time']; ?></p>
                <p><b>Venue:</b> <?php echo $row['venue']; ?></p>

                <!-- AUTO STATUS -->
                <?php if($row['event_date'] >= date('Y-m-d')): ?>
                    <span class="badge-upcoming">Upcoming</span>
                <?php else: ?>
                    <span class="badge-completed">Completed</span>
                <?php endif; ?>

            </div>

        <?php endwhile; ?>
    <?php else: ?>
        <p>No Events Found</p>
    <?php endif; ?>

    </div>

</main>
</div>

</body>
</html>