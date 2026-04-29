<?php
session_start();
include '../db.php';

/* ===== HANDLE APPROVE / REJECT ===== */
if(isset($_GET['id']) && isset($_GET['action'])){

    $id = $_GET['id'];
    $action = $_GET['action'];

    $res = $conn->query("SELECT * FROM reservations WHERE id=$id")->fetch_assoc();

    if($action == "approve"){

        $stmt = $conn->prepare("INSERT INTO events 
        (event_name,event_type,event_date,event_time,venue,guests,client_name,client_contact,package_type,budget,services)
        VALUES (?,?,?,?,?,?,?,?,?,?,?)");

        $stmt->bind_param("sssssisssds",
            $res['event_name'],
            $res['event_type'],
            $res['event_date'],
            $res['event_time'],
            $res['venue'],
            $res['guest'],
            $res['client_name'],
            $res['client_contact'],
            $res['package_type'],
            $res['budget'],
            $res['services']
        );

        $stmt->execute();

        $conn->query("UPDATE reservations SET status='Approved' WHERE id=$id");
    }

    else if($action == "reject"){
        $conn->query("UPDATE reservations SET status='Rejected' WHERE id=$id");
    }

    header("Location: pending.php");
    exit();
}

/* ===== FETCH PENDING ===== */
$result = $conn->query("SELECT * FROM reservations WHERE status='Pending'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pending Reservations</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="pending.css">
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
            <a href="pending.php" class="active">Pending Requests</a>
            <a href="complete_event.php">Completed Events</a>
            <a href="history.php">Event History</a>
            
        </nav>
    </div>

    <a href="../logout.php" class="logout">Logout</a>
</aside>

<!-- MAIN -->
<main class="main">

    <div class="topbar">
        <h1>Pending Reservations</h1>
        <div class="admin-box">Admin</div>
    </div>

    <div class="cards-container">

    <?php if($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>

        <div class="request-card">

            <h2><?php echo $row['event_name']; ?></h2>

            <p><b>Type:</b> <?php echo $row['event_type']; ?></p>
            <p><b>Date:</b> <?php echo $row['event_date']; ?></p>
            <p><b>Time:</b> <?php echo $row['event_time']; ?></p>
            <p><b>Venue:</b> <?php echo $row['venue']; ?></p>
            <p><b>Guests:</b> <?php echo $row['guest']; ?></p>

            <p><b>Client:</b> <?php echo $row['client_name']; ?></p>
            <p><b>Contact:</b> <?php echo $row['client_contact']; ?></p>

            <p><b>Package:</b> <?php echo $row['package_type']; ?></p>
            <p><b>Budget:</b> <?php echo $row['budget']; ?></p>
            <p><b>Services:</b> <?php echo $row['services']; ?></p>

            <!-- ACTIONS -->
            <div class="actions">
                <a href="?id=<?php echo $row['id']; ?>&action=approve" class="btn-approve">Accept</a>
                <a href="?id=<?php echo $row['id']; ?>&action=reject" class="btn-reject">Reject</a>
            </div>

        </div>

        <?php endwhile; ?>
    <?php else: ?>
        <p>No Pending Reservations</p>
    <?php endif; ?>

    </div>

</main>
</div>

</body>
</html>