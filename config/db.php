<?php
require_once __DIR__ . '/helpers.php';

$host = "localhost";
$username = "root";
$password = "";
$database = "registration_event";

if(function_exists('mysqli_report')) {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
}

try {
    $conn = new mysqli($host, $username, $password);
    $conn->set_charset("utf8mb4");

    // Create the project database automatically when it is missing.
    $conn->query("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
    $conn->select_db($database);

    // Users table for login and registration.
    $conn->query("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            username VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL UNIQUE,
            contact VARCHAR(30) NOT NULL,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(20) NOT NULL DEFAULT 'client',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Client reservation requests. Keep `guest` singular because the existing PHP uses that column.
    $conn->query("
        CREATE TABLE IF NOT EXISTS reservations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT DEFAULT NULL,
            event_name VARCHAR(150) NOT NULL,
            event_type VARCHAR(80) DEFAULT NULL,
            event_date DATE NOT NULL,
            event_time TIME NOT NULL,
            venue VARCHAR(150) DEFAULT NULL,
            guest INT DEFAULT 0,
            client_name VARCHAR(120) NOT NULL,
            client_contact VARCHAR(50) DEFAULT NULL,
            package_type VARCHAR(50) DEFAULT NULL,
            budget DECIMAL(10,2) DEFAULT 0.00,
            services TEXT DEFAULT NULL,
            status VARCHAR(30) NOT NULL DEFAULT 'Pending',
            approved_at DATETIME DEFAULT NULL,
            rejected_at DATETIME DEFAULT NULL,
            cancelled_at DATETIME DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Approved/admin-created events. Keep `guests` plural because the existing admin PHP uses that column.
    $conn->query("
        CREATE TABLE IF NOT EXISTS events (
            id INT AUTO_INCREMENT PRIMARY KEY,
            event_name VARCHAR(150) NOT NULL,
            event_type VARCHAR(80) DEFAULT NULL,
            event_date DATE NOT NULL,
            event_time TIME NOT NULL,
            venue VARCHAR(150) DEFAULT NULL,
            guests INT DEFAULT 0,
            client_name VARCHAR(120) NOT NULL,
            client_contact VARCHAR(50) DEFAULT NULL,
            package_type VARCHAR(50) DEFAULT NULL,
            budget DECIMAL(10,2) DEFAULT 0.00,
            services TEXT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    upgrade_eventify_schema($conn);
    seed_default_admin($conn);
} catch (mysqli_sql_exception $error) {
    die("Database setup failed: " . $error->getMessage());
}

function upgrade_eventify_schema($conn) {
    eventify_ensure_column($conn, 'reservations', 'user_id', 'INT DEFAULT NULL AFTER id');
    eventify_ensure_column($conn, 'reservations', 'approved_at', 'DATETIME DEFAULT NULL AFTER status');
    eventify_ensure_column($conn, 'reservations', 'rejected_at', 'DATETIME DEFAULT NULL AFTER approved_at');
    eventify_ensure_column($conn, 'reservations', 'cancelled_at', 'DATETIME DEFAULT NULL AFTER rejected_at');

    eventify_ensure_column_index($conn, 'users', 'email', 'idx_users_email');
    eventify_ensure_column_index($conn, 'users', 'role', 'idx_users_role');
    eventify_ensure_column_index($conn, 'reservations', 'status', 'idx_reservations_status');
    eventify_ensure_column_index($conn, 'reservations', 'event_date', 'idx_reservations_event_date');
    eventify_ensure_column_index($conn, 'reservations', 'user_id', 'idx_reservations_user_id');
    eventify_ensure_column_index($conn, 'events', 'event_date', 'idx_events_event_date');
}

function seed_default_admin($conn) {
    $result = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role='admin'");
    $admin_count = $result->fetch_assoc()['total'];

    if((int) $admin_count > 0) {
        return;
    }

    $name = "System Admin";
    $username = "admin";
    $email = "admin@eventify.com";
    $contact = "0000000000";
    $role = "admin";
    $hashed = password_hash("admin123", PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users(name, username, email, contact, password, role) VALUES(?,?,?,?,?,?)");
    $stmt->bind_param("ssssss", $name, $username, $email, $contact, $hashed, $role);
    $stmt->execute();
}
?>
