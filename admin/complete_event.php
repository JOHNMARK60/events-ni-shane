<?php
session_start();
include '../db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

/* ===== ADD: VIEW DETAILS LOGIC ===== */
$view_data = null;

if(isset($_GET['view'])){
    $id = $_GET['view'];
    $view_result = $conn->query("SELECT * FROM events WHERE id=$id");
    $view_data = $view_result->fetch_assoc();
}
/* ===== END ADD ===== */

$query = "SELECT * FROM events 
          WHERE event_date < CURDATE() 
          ORDER BY event_date DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Completed Events</title>

    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="complete.css?v=2">
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
            <a href="complete_event.php" class="active">Completed Events</a>
            <a href="history.php">Event History</a>
        </nav>
    </div>
    <a href="../logout.php" class="logout">Logout</a>
</aside>

<!-- MAIN -->
<main class="main">

<div class="topbar">
    <h1>Completed Events</h1>
</div>

<!-- ===== ADD: VIEW DETAILS UI ===== -->
<?php if($view_data): ?>
<div class="panel">

    <h2><?= $view_data['event_name'] ?></h2>

    <p><b>Type:</b> <?= $view_data['event_type'] ?></p>
    <p><b>Date:</b> <?= $view_data['event_date'] ?></p>
    <p><b>Time:</b> <?= $view_data['event_time'] ?></p>
    <p><b>Venue:</b> <?= $view_data['venue'] ?></p>
    <p><b>Guests:</b> <?= $view_data['guests'] ?></p>

    <p><b>Client:</b> <?= $view_data['client_name'] ?></p>
    <p><b>Contact:</b> <?= $view_data['client_contact'] ?></p>

    <p><b>Package:</b> <?= $view_data['package_type'] ?></p>
    <p><b>Budget:</b> <?= $view_data['budget'] ?></p>
    <p><b>Services:</b> <?= $view_data['services'] ?></p>

    <br>
    <a href="complete_event.php" class="view-btn">Back</a>

</div>
<?php endif; ?>
<!-- ===== END ADD ===== -->


<!-- ===== ADD: HIDE CARDS IF VIEWING ===== -->
<?php if(!$view_data): ?>

<div class="event-grid">

<?php if($result && $result->num_rows > 0): ?>
    <?php while($row = $result->fetch_assoc()): ?>

        <div class="event-card">

            <h3><?= $row['event_name'] ?></h3>
            <span class="event-type"><?= $row['event_type'] ?></span>

            <div class="info-row">
                <div class="icon-box">Date:</div>
                <?= $row['event_date'] ?>
            </div>

            <div class="info-row">
                <div class="icon-box">Time:</div>
                <?= $row['event_time'] ?>
            </div>

            <div class="info-row">
                <div class="icon-box">Venue:</div>
                <?= $row['venue'] ?>
            </div>

            <div class="card-footer">
                <div class="attendees">
                    Attendees<br>
                    <strong><?= $row['guests'] ?></strong>
                </div>

                <!-- ===== UPDATED BUTTON ===== -->
                <a href="?view=<?= $row['id'] ?>" class="view-btn">View Details</a>

            </div>

            <span class="badge-completed">Completed</span>

        </div>

    <?php endwhile; ?>
<?php else: ?>
    <p class="empty">No Completed Events</p>
<?php endif; ?>

</div>

<?php endif; ?>
<!-- ===== END ADD ===== -->

</main>
</div>

</body>
</html>