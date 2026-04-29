<?php
session_start();
include '../db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if(isset($_POST['save_event'])){

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

    $stmt = $conn->prepare("INSERT INTO events 
    (event_name, event_type, event_date, event_time, venue, guests, client_name, client_contact, package_type, budget, services) 
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
    alert('Event Added Successfully');
    window.location.href = 'add_event.php';
    </script>";
} else {
    echo "Error: " . $stmt->error;
}

} 

?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Event</title>
    <link rel="stylesheet" href="admin.css?v=1">
    <link rel="stylesheet" href="add.css">
</head>

<body>

<div class="container">

<!-- SIDEBAR -->
<aside class="sidebar">
    <div>
        <h2 class="logo">Event Admin</h2>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="add_event.php" class="active">Add Event</a>
            <a href="upcoming.php">Upcoming Events</a>
            <a href="pending.php">Pending Requests</a>
            <a href="complete_event.php">Completed Events</a>
            <a href="history.php">Event History</a>
        </nav>
    </div>
    <a href="../logout.php" class="logout">Logout</a>
</aside>

<!-- MAIN -->
<main class="main">

<div class="topbar">
    <h1>Add Event</h1>
</div>

<div class="form-wrapper">

<!-- FORM START -->
<form method="POST" class="form-grid">

    <div>
        <label>Event Name</label>
        <input type="text" name="event_name" required>
    </div>

    <div>
        <label>Event Type</label>
        <select name="event_type" required>
            <option value="">Select Type</option>
            <option>Wedding</option>
            <option>Birthday</option>
        </select>
    </div>

    <div>
        <label>Date</label>
        <input type="date" name="event_date" required>
    </div>

    <div>
        <label>Time</label>
        <input type="time" name="event_time" required>
    </div>

    <div>
        <label>Venue</label>
        <input type="text" name="venue" required>
    </div>

    <div>
        <label>Guests</label>
        <input type="number" name="guests" required>
    </div>

    <div>
        <label>Client Name</label>
        <input type="text" name="client_name" required>
    </div>

    <div>
        <label>Client Contact</label>
        <input type="text" name="client_contact" required>
    </div>

    <div>
        <label>Package</label>
        <select name="package_type" required>
            <option value="">Select Package</option>
            <option>Basic</option>
            <option>Standard</option>
            <option>Premium</option>
        </select>
    </div>

    <div>
        <label>Budget</label>
        <input type="number" name="budget">
    </div>

    <!-- SERVICES (NOW INSIDE FORM) -->
    <div class="services">
        <label>Services</label>

        <div class="services-grid">
            <label><input type="checkbox" name="services[]" value="Catering"> Catering</label>
            <label><input type="checkbox" name="services[]" value="Sound"> Sound</label>
            <label><input type="checkbox" name="services[]" value="Decoration"> Decoration</label>
            <label><input type="checkbox" name="services[]" value="Lights"> Lights</label>
        </div>
    </div>

    <!-- BUTTON (NOW INSIDE FORM) -->
    <div class="actions">
        <button type="submit" name="save_event" class="btn-save">Save Event</button>
    </div>

</form>
<!-- FORM END -->

</div>

</main>
</div>

</body>
</html>