<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function eventify_package_prices() {
    return [
        'Basic' => 15000.00,
        'Standard' => 30000.00,
        'Premium' => 50000.00,
        'Birthday Package' => 15000.00,
        'Wedding Package' => 50000.00,
    ];
}

function eventify_package_budget($package_type) {
    $prices = eventify_package_prices();
    return $prices[$package_type] ?? null;
}

function eventify_event_type_prices() {
    return [
        'Wedding' => 50000.00,
        'Birthday' => 15000.00,
    ];
}

function eventify_event_type_budget($event_type) {
    $prices = eventify_event_type_prices();
    return $prices[$event_type] ?? null;
}

function eventify_calculated_budget($event_type, $package_type) {
    $event_budget = eventify_event_type_budget($event_type);

    if ($event_budget !== null) {
        return $event_budget;
    }

    return eventify_package_budget($package_type);
}

function eventify_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function eventify_csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(eventify_csrf_token(), ENT_QUOTES) . '">';
}

function eventify_verify_csrf() {
    $submitted = $_POST['csrf_token'] ?? '';
    $stored = $_SESSION['csrf_token'] ?? '';

    return $submitted !== '' && $stored !== '' && hash_equals($stored, $submitted);
}

function eventify_set_flash($icon, $title, $text = '') {
    $_SESSION['eventify_flash'] = [
        'icon' => $icon,
        'title' => $title,
        'text' => $text,
    ];
}

function eventify_sweetalert_assets() {
    return '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
}

function eventify_sweetalert_flash() {
    if (empty($_SESSION['eventify_flash'])) {
        return '';
    }

    $flash = $_SESSION['eventify_flash'];
    unset($_SESSION['eventify_flash']);

    return '<script>
document.addEventListener("DOMContentLoaded", function () {
    if (window.Swal) {
        Swal.fire(' . json_encode($flash) . ');
    }
});
</script>';
}

function eventify_require_role($role, $redirect = '../auth/login.php') {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        eventify_set_flash('error', 'Access denied', 'Please log in with the correct account.');
        header("Location: $redirect");
        exit();
    }
}

function eventify_current_user_id() {
    return (int) ($_SESSION['user_id'] ?? 0);
}

function eventify_valid_contact($contact) {
    return (bool) preg_match('/^[0-9+\-\s()]{7,20}$/', $contact);
}

function eventify_validate_reservation_data($data) {
    $errors = [];
    $today = date('Y-m-d');
    $budget = eventify_calculated_budget($data['event_type'] ?? '', $data['package_type'] ?? '');

    if (trim($data['event_name'] ?? '') === '') {
        $errors[] = 'Event name is required.';
    }

    if (trim($data['event_type'] ?? '') === '') {
        $errors[] = 'Event type is required.';
    }

    $event_date = trim($data['event_date'] ?? '');
    $parsed_date = DateTime::createFromFormat('Y-m-d', $event_date);

    if ($event_date === '') {
        $errors[] = 'Event date is required.';
    } elseif (!$parsed_date || $parsed_date->format('Y-m-d') !== $event_date) {
        $errors[] = 'Event date is invalid.';
    } elseif ($event_date < $today) {
        $errors[] = 'Event date must not be in the past.';
    }

    if (trim($data['event_time'] ?? '') === '') {
        $errors[] = 'Event time is required.';
    }

    if (trim($data['venue'] ?? '') === '') {
        $errors[] = 'Venue is required.';
    }

    if ((int) ($data['guests'] ?? 0) <= 0) {
        $errors[] = 'Guest count must be positive.';
    }

    if (trim($data['package_type'] ?? '') === '' && eventify_event_type_budget($data['event_type'] ?? '') === null) {
        $errors[] = 'Package type is required.';
    }

    if (isset($data['budget']) && $data['budget'] !== '' && $budget !== null && (float) $data['budget'] !== (float) $budget) {
        $errors[] = 'Budget must match the selected event or package.';
    }

    if (trim($data['client_name'] ?? '') === '') {
        $errors[] = 'Client name is required.';
    }

    if (!eventify_valid_contact(trim($data['client_contact'] ?? ''))) {
        $errors[] = 'Contact number format is invalid.';
    }

    return $errors;
}

function eventify_reservation_payload_from_post() {
    $package_type = trim($_POST['package_type'] ?? '');
    $event_type = trim($_POST['event_type'] ?? '');

    return [
        'event_name' => trim($_POST['event_name'] ?? ''),
        'event_type' => $event_type,
        'event_date' => trim($_POST['event_date'] ?? ''),
        'event_time' => trim($_POST['event_time'] ?? ''),
        'venue' => trim($_POST['venue'] ?? ''),
        'guests' => (int) ($_POST['guests'] ?? 0),
        'client_name' => trim($_POST['client_name'] ?? ''),
        'client_contact' => trim($_POST['client_contact'] ?? ''),
        'package_type' => $package_type,
        'budget' => $_POST['budget'] ?? '',
        'calculated_budget' => eventify_calculated_budget($event_type, $package_type),
        'services' => isset($_POST['services']) ? implode(',', array_map('trim', $_POST['services'])) : '',
    ];
}

function eventify_package_price_script() {
    return '<script>
window.eventifyPackagePrices = ' . json_encode(eventify_package_prices()) . ';
window.eventifyEventTypePrices = ' . json_encode(eventify_event_type_prices()) . ';
</script>';
}

function eventify_column_exists($conn, $table, $column) {
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = ?
          AND COLUMN_NAME = ?
    ");
    $stmt->bind_param("ss", $table, $column);
    $stmt->execute();

    return (int) $stmt->get_result()->fetch_assoc()['total'] > 0;
}

function eventify_ensure_column($conn, $table, $column, $definition) {
    if (!eventify_column_exists($conn, $table, $column)) {
        $conn->query("ALTER TABLE `$table` ADD COLUMN `$column` $definition");
    }
}

function eventify_column_has_index($conn, $table, $column) {
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = ?
          AND COLUMN_NAME = ?
    ");
    $stmt->bind_param("ss", $table, $column);
    $stmt->execute();

    return (int) $stmt->get_result()->fetch_assoc()['total'] > 0;
}

function eventify_ensure_column_index($conn, $table, $column, $index_name) {
    if (!eventify_column_has_index($conn, $table, $column)) {
        $conn->query("ALTER TABLE `$table` ADD INDEX `$index_name` (`$column`)");
    }
}
?>
