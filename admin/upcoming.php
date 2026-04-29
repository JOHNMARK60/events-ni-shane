<?php
session_start();
include '../db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$query = "SELECT * FROM events 
          WHERE event_date >= CURDATE() 
          ORDER BY event_date ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upcoming Events</title>

    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="upcoming.css">
</head>

<body>

<div class="container">

<aside class="sidebar">
    <div>
        <h2 class="logo">Event Admin</h2>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="add_event.php">Add Event</a>
            <a href="upcoming.php" class="active">Upcoming Events</a>
            <a href="pending.php">Pending Requests</a>
            <a href="complete_event.php">Completed Events</a>
            <a href="history.php">Event History</a>
        </nav>
    </div>
    <a href="../logout.php" class="logout">Logout</a>
</aside>

<main class="main">

<div class="topbar">
    <h1>Upcoming Events</h1>
    <div class="admin-box">Admin</div>
</div>

<div class="event-grid">

<?php while($row = $result->fetch_assoc()): ?>
<div class="event-card">
    <div class="event-content">

        <span class="event-tag"><?= $row['event_type'] ?></span>

        <h3><?= $row['event_name'] ?></h3>

        <p>Client Name: <?= $row['client_name'] ?></p>
        <p>Date: <?= $row['event_date'] ?></p>
        <p>Time: <?= $row['event_time'] ?></p>
        <p>Venue: <?= $row['venue'] ?></p>

        <button class="view-btn"
            data-id="<?= $row['id'] ?>"
            data-title="<?= $row['event_name'] ?>"
            data-type="<?= $row['event_type'] ?>"
            data-date="<?= $row['event_date'] ?>"
            data-time="<?= $row['event_time'] ?>"
            data-venue="<?= $row['venue'] ?>"
            data-guests="<?= $row['guests'] ?>"
            data-client="<?= $row['client_name'] ?>"
            data-contact="<?= $row['client_contact'] ?>"
            data-package="<?= $row['package_type'] ?>"
            data-budget="<?= $row['budget'] ?>"
            data-services="<?= $row['services'] ?>">
            View More
        </button>

    </div>
</div>
<?php endwhile; ?>

</div>

</main>
</div>

<!-- MODAL -->
<div id="eventModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>

        <h2 id="m_title"></h2>

        <div class="modal-grid">
            <p><b>Event Type:</b> <span id="m_type" contenteditable="false"></span></p>
            <p><b>Date:</b> <span id="m_date" contenteditable="false"></span></p>
            <p><b>Time:</b> <span id="m_time" contenteditable="false"></span></p>
            <p><b>Venue:</b> <span id="m_venue" contenteditable="false"></span></p>
            <p><b>Guests:</b> <span id="m_guests" contenteditable="false"></span></p>
            <p><b>Client:</b> <span id="m_client" contenteditable="false"></span></p>
            <p><b>Contact:</b> <span id="m_contact" contenteditable="false"></span></p>
            <p><b>Package:</b> <span id="m_package" contenteditable="false"></span></p>
            <p><b>Budget:</b> <span id="m_budget" contenteditable="false"></span></p>
            <p><b>Services:</b> <span id="m_services" contenteditable="false"></span></p>
        </div>

        <div class="modal-actions">
            <button id="editBtn">Edit</button>
            <button id="saveBtn" style="display:none;">Save</button>
            <button id="deleteBtn">Delete</button>
        </div>

    </div>
</div>

<script>
let currentId = null;

const modal = document.getElementById("eventModal");
const closeBtn = document.querySelector(".close");

document.querySelectorAll(".view-btn").forEach(btn => {
    btn.addEventListener("click", () => {

        currentId = btn.dataset.id;

        modal.style.display = "block";

        m_title.innerText = btn.dataset.title;
        m_type.innerText = btn.dataset.type;
        m_date.innerText = btn.dataset.date;
        m_time.innerText = btn.dataset.time;
        m_venue.innerText = btn.dataset.venue;
        m_guests.innerText = btn.dataset.guests;
        m_client.innerText = btn.dataset.client;
        m_contact.innerText = btn.dataset.contact;
        m_package.innerText = btn.dataset.package;
        m_budget.innerText = btn.dataset.budget;
        m_services.innerText = btn.dataset.services;
    });
});

closeBtn.onclick = () => modal.style.display = "none";

const fields = ["m_type","m_date","m_time","m_venue","m_guests","m_client","m_contact","m_package","m_budget","m_services"];

editBtn.onclick = () => {
    fields.forEach(id => document.getElementById(id).contentEditable = true);
    editBtn.style.display = "none";
    saveBtn.style.display = "inline-block";
};

saveBtn.onclick = () => {

    const data = {
        id: currentId,
        type: m_type.innerText,
        date: m_date.innerText,
        time: m_time.innerText,
        venue: m_venue.innerText,
        guests: m_guests.innerText,
        client: m_client.innerText,
        contact: m_contact.innerText,
        package: m_package.innerText,
        budget: m_budget.innerText,
        services: m_services.innerText
    };

    fetch('update_event.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.text())
    .then(msg => {
        alert(msg);
        location.reload();
    });
};

deleteBtn.onclick = () => {
    if(confirm("Delete this event?")) {
        fetch('delete_event.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: currentId })
        })
        .then(res => res.text())
        .then(msg => {
            alert(msg);
            location.reload();
        });
    }
};
</script>

</body>
</html>