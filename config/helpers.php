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

function eventify_page_url($page) {
    $params = $_GET;
    $params['page'] = max(1, (int) $page);

    return '?' . http_build_query($params);
}

function eventify_set_flash($icon, $title, $text = '') {
    $_SESSION['eventify_flash'] = [
        'icon' => $icon,
        'title' => $title,
        'text' => $text,
    ];
}

function eventify_prepare_email_notification($to, $subject, $message) {
    // External mail transport is intentionally not configured here.
    // Keep the payload in session so the app has a single place to wire mail later.
    $_SESSION['eventify_last_email'] = [
        'to' => $to,
        'subject' => $subject,
        'message' => $message,
        'prepared_at' => date('Y-m-d H:i:s'),
    ];
}

function eventify_create_notification($conn, $user_id, $role, $title, $message) {
    // Notifications are stored separately from reservations so read/unread state can be per audience.
    $user_id = $user_id ? (int) $user_id : null;
    $stmt = $conn->prepare("
        INSERT INTO notifications (user_id, role, title, message)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("isss", $user_id, $role, $title, $message);
    $stmt->execute();
}

function eventify_notification_scope_sql($role) {
    return $role === 'admin'
        ? "role='admin' AND user_id IS NULL"
        : "role='client' AND user_id=?";
}

function eventify_get_unread_notification_count($conn, $user_id, $role) {
    if ($role === 'admin') {
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM notifications WHERE " . eventify_notification_scope_sql($role) . " AND is_read=0");
    } else {
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM notifications WHERE " . eventify_notification_scope_sql($role) . " AND is_read=0");
        $stmt->bind_param("i", $user_id);
    }
    $stmt->execute();

    return (int) $stmt->get_result()->fetch_assoc()['total'];
}

function eventify_get_notifications($conn, $user_id, $role, $limit = 8) {
    if ($role === 'admin') {
        $stmt = $conn->prepare("
            SELECT *
            FROM notifications
            WHERE " . eventify_notification_scope_sql($role) . "
            ORDER BY created_at DESC
            LIMIT ?
        ");
        $stmt->bind_param("i", $limit);
    } else {
        $stmt = $conn->prepare("
            SELECT *
            FROM notifications
            WHERE " . eventify_notification_scope_sql($role) . "
            ORDER BY created_at DESC
            LIMIT ?
        ");
        $stmt->bind_param("ii", $user_id, $limit);
    }
    $stmt->execute();

    return $stmt->get_result();
}

function eventify_mark_notifications_as_read($conn, $user_id, $role) {
    if ($role === 'admin') {
        $stmt = $conn->prepare("UPDATE notifications SET is_read=1 WHERE " . eventify_notification_scope_sql($role));
    } else {
        $stmt = $conn->prepare("UPDATE notifications SET is_read=1 WHERE " . eventify_notification_scope_sql($role));
        $stmt->bind_param("i", $user_id);
    }
    $stmt->execute();
}

function eventify_notification_widget($conn, $role, $dashboard_url = 'dashboard.php', $action_url = 'notifications.php') {
    $user_id = eventify_current_user_id();
    $unread_count = eventify_get_unread_notification_count($conn, $user_id, $role);
    $notifications = eventify_get_notifications($conn, $user_id, $role);
    ob_start();
    ?>
    <div class="relative flex items-center gap-2" data-notification-root>
        <button type="button" class="relative grid h-11 w-11 place-items-center rounded-2xl border border-purple-100 bg-white text-primary shadow-sm hover:bg-purple-50" data-notification-toggle aria-label="Open notifications">
            <span class="text-xl leading-none">&#128276;</span>
            <?php if($unread_count > 0): ?>
                <span class="absolute right-2 top-2 h-3 w-3 rounded-full bg-red-500 ring-2 ring-white" data-notification-dot></span>
            <?php endif; ?>
        </button>
        <a href="<?php echo htmlspecialchars($dashboard_url, ENT_QUOTES); ?>" class="grid h-11 w-11 place-items-center rounded-2xl border border-purple-100 bg-white text-primary shadow-sm hover:bg-purple-50" aria-label="Dashboard">
            <span class="text-xl leading-none">&#9638;</span>
        </a>
        <a href="../auth/logout.php" class="grid h-11 w-11 place-items-center rounded-2xl border border-purple-100 bg-white font-bold text-primary shadow-sm hover:bg-purple-50" aria-label="Account">
            <?php echo $role === 'admin' ? 'A' : 'U'; ?>
        </a>

        <div class="absolute right-0 top-14 z-50 hidden w-80 overflow-hidden rounded-3xl border border-purple-100 bg-white shadow-soft" data-notification-menu>
            <div class="flex items-center justify-between gap-3 border-b border-purple-100 bg-indigo-50 p-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-primary">Notifications</p>
                <p class="text-sm text-slate-600"><span data-notification-unread-count><?php echo $unread_count; ?></span> unread</p>
                </div>
                <form method="POST" action="<?php echo htmlspecialchars($action_url, ENT_QUOTES); ?>" data-notification-read-form>
                    <?php echo eventify_csrf_field(); ?>
                    <button type="submit" name="notification_action" value="mark_all_read" class="rounded-xl bg-white px-3 py-2 text-xs font-bold text-primary hover:bg-purple-50">Mark all</button>
                </form>
            </div>
            <div class="max-h-96 overflow-y-auto p-3">
                <?php if($notifications && $notifications->num_rows > 0): ?>
                    <div class="space-y-2">
                        <?php while($notification = $notifications->fetch_assoc()): ?>
                            <article class="rounded-2xl border <?php echo (int) $notification['is_read'] === 0 ? 'border-purple-200 bg-purple-50' : 'border-slate-100 bg-white'; ?> p-3">
                                <div class="flex items-start gap-2">
                                    <?php if((int) $notification['is_read'] === 0): ?>
                                        <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-red-500"></span>
                                    <?php endif; ?>
                                    <div>
                                        <h3 class="text-sm font-bold text-dark"><?php echo htmlspecialchars($notification['title']); ?></h3>
                                        <p class="mt-1 text-sm leading-5 text-slate-600"><?php echo htmlspecialchars($notification['message']); ?></p>
                                        <p class="mt-2 text-xs font-semibold text-slate-400"><?php echo htmlspecialchars(date('M d, Y h:i A', strtotime($notification['created_at']))); ?></p>
                                    </div>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="rounded-2xl bg-indigo-50 p-5 text-center text-sm font-semibold text-slate-600">No notifications yet.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
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

function eventify_event_conflict_exists($conn, $event_date, $event_time, $venue, $exclude_event_id = 0) {
    if ($exclude_event_id > 0) {
        $stmt = $conn->prepare("
            SELECT id
            FROM events
            WHERE event_date=?
              AND event_time=?
              AND LOWER(TRIM(venue)) = LOWER(TRIM(?))
              AND id<>?
            LIMIT 1
        ");
        $stmt->bind_param("sssi", $event_date, $event_time, $venue, $exclude_event_id);
    } else {
        $stmt = $conn->prepare("
            SELECT id
            FROM events
            WHERE event_date=?
              AND event_time=?
              AND LOWER(TRIM(venue)) = LOWER(TRIM(?))
            LIMIT 1
        ");
        $stmt->bind_param("sss", $event_date, $event_time, $venue);
    }

    $stmt->execute();
    return (bool) $stmt->get_result()->fetch_assoc();
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
