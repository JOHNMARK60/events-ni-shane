<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'client') {
    header("Location: ../login.php");
    exit();
}

/* ===== ADD THIS ===== */
include '../db.php';

/* TOTAL RESERVATIONS */
$my_reservations = $conn->query("SELECT COUNT(*) as total FROM reservations")
->fetch_assoc()['total'];

/* UPCOMING EVENTS */
$upcoming = $conn->query("
    SELECT COUNT(*) as total 
    FROM events 
    WHERE event_date >= CURDATE()
")->fetch_assoc()['total'];

/* COMPLETED EVENTS */
$completed = $conn->query("
    SELECT COUNT(*) as total 
    FROM events 
    WHERE event_date < CURDATE()
")->fetch_assoc()['total'];
/* ===== END ADD ===== */
?>

<!DOCTYPE html>
<html>

<head>
    <title>Client Dashboard</title>
    <link rel="stylesheet" href="client.css?v=2">
</head>

<body>

<div class="container">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <h2 class="logo">Client Panel</h2>

        <nav>
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="calendar.php">View Calendar</a>
            <a href="reserve.php">Make Reservation</a>
            <a href="my_reservation.php">My Reservations</a>
           
        </nav>

        <a href="../logout.php" class="logout">Logout</a>

    </aside>

    <!-- MAIN -->
    <main class="main">

    <!-- TOPBAR -->
    <div class="topbar">
        <h1>Dashboard</h1>
        <div class="user-box">Client</div>
    </div>

    <!-- WELCOME BANNER -->
    <div class="welcome-box">
        <div>
            <h2>Welcome!</h2>
            <p>Ready to plan your next event?</p>
        </div>      
    </div>

    <!-- CARDS -->
    <div class="cards">

        <div class="card purple">
            <h3>My Reservations</h3>
            <h1><?php echo $my_reservations; ?></h1>
        </div>

        <div class="card blue">
            <h3>Upcoming</h3>
            <h1><?php echo $upcoming; ?></h1>
        </div>

        <div class="card pink">
            <h3>Completed</h3>
            <h1><?php echo $completed; ?></h1>
        </div>

    </div>

    <!-- RECENT LIST -->
    <div class="panel">
        <h2>Recent Reservations</h2>

        <?php
        $recent = $conn->query("SELECT * FROM reservations ORDER BY id DESC LIMIT 3");
        ?>

        <?php if($recent->num_rows > 0): ?>
            <?php while($row = $recent->fetch_assoc()): ?>
                <div class="list-item">
                    <div>
                        <h4><?php echo $row['event_name']; ?></h4>
                        <p><?php echo $row['event_date']; ?> | <?php echo $row['status']; ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="list-item">
                <div>
                    <h4>No reservations yet</h4>
                    <p>Start by creating your first booking.</p>
                </div>
            </div>
        <?php endif; ?>

    </div>

</main>

</div>

</body>
</html>