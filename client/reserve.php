<?php
session_start();
include '../db.php';

if(isset($_POST['reserve'])){

    $event_name = $_POST['event_name'];
    $event_type = $_POST['event_type'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $venue = $_POST['venue'];
    $guests = $_POST['guests'];
    $client_name = $_POST['client_name'];
    $client_contact = $_POST['client_contact'];
    $package_type = $_POST['package_type'];
    $budget = $_POST['budget'];

    $services = isset($_POST['services']) ? implode(",", $_POST['services']) : "";

    // INSERT INTO reservations (NOT events)
    $stmt = $conn->prepare("INSERT INTO reservations 
    (event_name, event_type, event_date, event_time, venue, guest, client_name, client_contact, package_type, budget, services) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("sssssisssds",
        $event_name,
        $event_type,
        $event_date,
        $event_time,
        $venue,
        $guests,
        $client_name,
        $client_contact,
        $package_type,
        $budget,
        $services
    );

    if($stmt->execute()){
        echo "<script>
        alert('Reservation submitted! Waiting for admin approval.');
        window.location.href='reserve.php';
        </script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Make Reservation</title>

    <link rel="stylesheet" href="client.css?v=2">
    <link rel="stylesheet" href="reserve.css?v=1">
</head>

<body>

<div class="container">

<!-- SIDEBAR -->
<aside class="sidebar">
    <h2 class="logo">Client Panel</h2>

    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="calendar.php">View Calendar</a>
        <a href="reserve.php" class="active">Make Reservation</a>
        <a href="my_reservation.php">My Reservations</a>
        
    </nav>

    <a href="../logout.php" class="logout">Logout</a>
</aside>

<!-- MAIN -->
<main class="main">

    <div class="topbar">
        <h1>Make Reservation</h1>
        <div class="user-box">Client</div>
    </div>

    <div class="form-container">

        <form method="POST">

            <div class="form-group">
                <label>Event Name</label>
                <input type="text" name="event_name" required>
            </div>

            <div class="form-group">
                <label>Event Type</label>
                <select name="event_type">
                    <option value="">Select Type</option>
                    <option>Wedding</option>
                    <option>Birthday</option>
                </select>
            </div>

            <div class="form-group">
                <label>Date</label>
                <input type="date" name="event_date" required>
            </div>

            <div class="form-group">
                <label>Time</label>
                <input type="time" name="event_time" required>
            </div>

            <div class="form-group">
                <label>Venue</label>
                <input type="text" name="venue">
            </div>

            <div class="form-group">
                <label>Guests</label>
                <input type="number" name="guests">
            </div>

            <div class="form-group">
                <label>Client Name</label>
                <input type="text" name="client_name" required>
            </div>

            <div class="form-group">
                <label>Client Contact</label>
                <input type="text" name="client_contact">
            </div>

            <div class="form-group">
                <label>Package</label>
                <select name="package_type">
                    <option value="">Select Package</option>
                    <option>Basic</option>
                    <option>Standard</option>
                    <option>Premium</option>
                </select>
            </div>

            <div class="form-group">
                <label>Budget</label>
                <input type="number" name="budget">
            </div>

            <div class="form-group">
                <label>Services</label><br>
                <label><input type="checkbox" name="services[]" value="Catering"> Catering</label>
                <label><input type="checkbox" name="services[]" value="Sound"> Sound</label>
                <label><input type="checkbox" name="services[]" value="Decoration"> Decoration</label>
                <label><input type="checkbox" name="services[]" value="Lights"> Lights</label>
            </div>

            <button type="submit" name="reserve" class="btn-submit">
                Submit Reservation
            </button>

        </form>

    </div>

</main>
</div>

</body>
</html>