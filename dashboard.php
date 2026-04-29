<?php
session_start();

if(!isset($_SESSION['name'])){
    header("Location: login.php");
}
?>

<h2>Welcome, <?php echo $_SESSION['name']; ?></h2>

<?php
if($_SESSION['role'] == 'admin'){
    echo "<h3>Admin Panel</h3>";
    echo "<a href='reserve.php'>View Reservations</a>"; // temporary
}else{
    echo "<h3>Client Panel</h3>";
    echo "<a href='reserve.php'>Reserve Event</a>";
}
?>

<br><br>
<a href="logout.php">Logout</a>