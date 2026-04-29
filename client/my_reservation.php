<?php
session_start();
include '../db.php';

$result = $conn->query("SELECT * FROM reservations ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Reservations</title>

    <link rel="stylesheet" href="client.css?v=2">
    <link rel="stylesheet" href="my_reservation.css?v=4">
</head>

<body>

<div class="container">

<!-- SIDEBAR -->
<aside class="sidebar">
    <h2 class="logo">Client Panel</h2>

    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="calendar.php">View Calendar</a>
        <a href="reserve.php">Make Reservation</a>
        <a href="my_reservation.php" class="active">My Reservations</a>
        
    </nav>

    <a href="../logout.php" class="logout">Logout</a>
</aside>

<!-- MAIN -->
<main class="main">

<div class="topbar">
    <h1>My Reservations</h1>
</div>

<div class="cards-container">

<?php while($row = $result->fetch_assoc()): ?>

<div class="reservation-card">

<h2><?= htmlspecialchars($row['event_name']) ?></h2>

<p><b>Date:</b> <?= $row['event_date'] ?></p>
<p><b>Type:</b> <?= $row['event_type'] ?></p>
<p><b>Venue:</b> <?= $row['venue'] ?></p>

<span class="badge <?= strtolower($row['status']) ?>">
    <?= $row['status'] ?>
</span>

<div class="card-actions">

<button class="view-btn"
onclick="openModal(this)"
data-id="<?= $row['id'] ?>"
data-title="<?= htmlspecialchars($row['event_name'], ENT_QUOTES) ?>"
data-type="<?= htmlspecialchars($row['event_type'], ENT_QUOTES) ?>"
data-date="<?= $row['event_date'] ?>"
data-time="<?= $row['event_time'] ?>"
data-venue="<?= htmlspecialchars($row['venue'], ENT_QUOTES) ?>"
data-guests="<?= $row['guest'] ?>"
data-client="<?= htmlspecialchars($row['client_name'], ENT_QUOTES) ?>"
data-contact="<?= htmlspecialchars($row['client_contact'], ENT_QUOTES) ?>"
data-package="<?= htmlspecialchars($row['package_type'], ENT_QUOTES) ?>"
data-budget="<?= $row['budget'] ?>"
data-services="<?= htmlspecialchars($row['services'], ENT_QUOTES) ?>">
View Details
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

<span class="close" onclick="closeModal()">&times;</span>

<h2 id="m_name"></h2>

<div class="modal-grid">

<p><b>Type:</b> <span id="m_type"></span></p>
<p><b>Date:</b> <span id="m_date"></span></p>

<p><b>Time:</b> <span id="m_time"></span></p>
<p><b>Venue:</b> <span id="m_venue"></span></p>

<p><b>Guests:</b> <span id="m_guest"></span></p>
<p><b>Client:</b> <span id="m_client"></span></p>

<p><b>Contact:</b> <span id="m_contact"></span></p>
<p><b>Package:</b> <span id="m_package"></span></p>

<p><b>Budget:</b> <span id="m_budget"></span></p>
<p><b>Services:</b> <span id="m_services"></span></p>

</div>

<div class="modal-actions">
<button id="editBtn" onclick="enableEdit()" class="edit-btn">Edit</button>
<button id="saveBtn" onclick="saveEdit()" class="edit-btn" style="display:none;">Save</button>
<a id="deleteBtn" class="delete-btn">Delete</a>
</div>

</div>
</div>

<script>

let currentID = null;
let isEditing = false;

function openModal(btn){

currentID = btn.dataset.id;

// VIEW MODE (text)
setText("m_name", btn.dataset.title);
setText("m_type", btn.dataset.type);
setText("m_date", btn.dataset.date);
setText("m_time", btn.dataset.time);
setText("m_venue", btn.dataset.venue);
setText("m_guest", btn.dataset.guests);
setText("m_client", btn.dataset.client);
setText("m_contact", btn.dataset.contact);
setText("m_package", btn.dataset.package);
setText("m_budget", btn.dataset.budget);
setText("m_services", btn.dataset.services);

document.getElementById("deleteBtn").href = "delete_reservation.php?id=" + btn.dataset.id;

document.getElementById("saveBtn").style.display = "none";
document.getElementById("editBtn").style.display = "inline-block";

isEditing = false;

document.getElementById("eventModal").style.display = "block";
}

// helper
function setText(id, value){
    document.getElementById(id).innerText = value;
}

// EDIT MODE
function enableEdit(){

isEditing = true;

// convert spans to inputs
convertToInput("m_type");
convertToInput("m_date", "date");
convertToInput("m_time", "time");
convertToInput("m_venue");
convertToInput("m_guest", "number");
convertToInput("m_client");
convertToInput("m_contact");
convertToInput("m_package");
convertToInput("m_budget", "number");
convertToInput("m_services");

document.getElementById("editBtn").style.display = "none";
document.getElementById("saveBtn").style.display = "inline-block";
}

function convertToInput(id, type="text"){
    let el = document.getElementById(id);
    let val = el.innerText;

    el.innerHTML = `<input type="${type}" value="${val}" id="input_${id}">`;
}

// SAVE
function saveEdit(){

let data = {
id: currentID,
event_type: getVal("m_type"),
event_date: getVal("m_date"),
event_time: getVal("m_time"),
venue: getVal("m_venue"),
guests: getVal("m_guest"),
client_name: getVal("m_client"),
client_contact: getVal("m_contact"),
package_type: getVal("m_package"),
budget: getVal("m_budget"),
services: getVal("m_services")
};

fetch("update_reservation.php", {
method: "POST",
headers: {"Content-Type": "application/json"},
body: JSON.stringify(data)
})
.then(() => {
alert("Updated!");
location.reload();
});
}

function getVal(id){
return document.getElementById("input_"+id).value;
}

function closeModal(){
document.getElementById("eventModal").style.display = "none";
}

</script>

</body>
</html>