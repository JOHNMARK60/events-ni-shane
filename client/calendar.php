<?php
session_start();
include '../db.php';

/* GET ALL EVENT DATES */
$dates = [];
$result = $conn->query("SELECT event_date FROM events");

while($row = $result->fetch_assoc()){
    $dates[] = $row['event_date'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Calendar</title>

    <!-- GLOBAL STYLE -->
    <link rel="stylesheet" href="client.css?v=2">

    <!-- CALENDAR STYLE -->
    <link rel="stylesheet" href="calendar.css?v=1">
</head>

<body>

<div class="container">

<!-- SIDEBAR -->
<aside class="sidebar">
    <h2 class="logo">Client Panel</h2>

    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="view_calendar.php" class="active">View Calendar</a>
        <a href="reserve.php">Make Reservation</a>
        <a href="my_reservation.php">My Reservations</a>
        
    </nav>

    <a href="../logout.php" class="logout">Logout</a>
</aside>

<!-- MAIN -->
<main class="main">

    <div class="topbar">
        <h1>Event Calendar</h1>
        <div class="user-box">Client</div>
    </div>

    <div class="calendar-box">

        <div class="calendar-header">
            <button onclick="prevMonth()">◀</button>
            <h2 id="monthYear"></h2>
            <button onclick="nextMonth()">▶</button>
        </div>

        <div class="calendar-grid" id="calendar"></div>

    </div>

</main>
</div>

<!-- PASS DATA TO JS -->
<script>
let bookedDates = <?php echo json_encode($dates); ?>;
</script>

<!-- JAVASCRIPT -->
<script>
let currentDate = new Date();

function renderCalendar() {
    const calendar = document.getElementById("calendar");
    const monthYear = document.getElementById("monthYear");

    calendar.innerHTML = "";

    let year = currentDate.getFullYear();
    let month = currentDate.getMonth();

    let firstDay = new Date(year, month, 1).getDay();
    let daysInMonth = new Date(year, month + 1, 0).getDate();

    monthYear.innerText = currentDate.toLocaleString('default', { month: 'long', year: 'numeric' });

    // empty cells
    for (let i = 0; i < firstDay; i++) {
        calendar.innerHTML += `<div></div>`;
    }

    // days
    for (let day = 1; day <= daysInMonth; day++) {

        let dateStr = year + "-" + 
                      String(month + 1).padStart(2, '0') + "-" + 
                      String(day).padStart(2, '0');

        let isBooked = bookedDates.includes(dateStr);

        if(isBooked){
            calendar.innerHTML += `
                <div class="day booked">
                    ${day}<br><small>Not Available</small>
                </div>
            `;
        } else {
            calendar.innerHTML += `
                <div class="day available">
                    ${day}<br><small>Available</small>
                </div>
            `;
        }
    }
}

function prevMonth(){
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
}

function nextMonth(){
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
}

renderCalendar();
</script>

</body>
</html>