<?php
session_start();
include '../config/db.php';

eventify_require_role('client');

$user_id = eventify_current_user_id();
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$errors = [];
$today = date('Y-m-d');

$stmt = $conn->prepare("SELECT * FROM reservations WHERE id=? AND user_id=? LIMIT 1");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$reservation = $stmt->get_result()->fetch_assoc();

if(!$reservation) {
    eventify_set_flash('error', 'Reservation not found', 'That reservation does not belong to your account.');
    header("Location: my_reservations.php");
    exit();
}

if(strtolower($reservation['status']) !== 'pending') {
    eventify_set_flash('error', 'Edit unavailable', 'Only pending reservations can be edited.');
    header("Location: my_reservations.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(!eventify_verify_csrf()) {
        $errors[] = "Security check failed. Please try again.";
    } else {
        $payload = eventify_reservation_payload_from_post();
        $errors = eventify_validate_reservation_data($payload);
    }

    if(!empty($errors)) {
        eventify_set_flash('error', 'Reservation not updated', $errors[0]);
    } else {
        $budget = $payload['calculated_budget'];

        // Security: ownership and pending status are enforced in the UPDATE predicate.
        $stmt = $conn->prepare("
            UPDATE reservations
            SET event_name=?, event_type=?, event_date=?, event_time=?, venue=?, guest=?,
                client_name=?, client_contact=?, package_type=?, budget=?, services=?
            WHERE id=? AND user_id=? AND status='Pending'
        ");
        $stmt->bind_param("sssssisssdsii",
            $payload['event_name'],
            $payload['event_type'],
            $payload['event_date'],
            $payload['event_time'],
            $payload['venue'],
            $payload['guests'],
            $payload['client_name'],
            $payload['client_contact'],
            $payload['package_type'],
            $budget,
            $payload['services'],
            $id,
            $user_id
        );
        $stmt->execute();

        if($stmt->affected_rows >= 0) {
            eventify_prepare_email_notification('admin@eventify.com', 'Reservation edited', 'A client updated a pending Eventify reservation.');
            eventify_set_flash('success', 'Reservation updated', 'Your pending reservation was saved.');
            header("Location: my_reservations.php");
            exit();
        }

        $errors[] = "Reservation was not updated.";
    }
}

$form = array_merge($reservation, $_POST);
$selectedServices = array_filter(array_map('trim', explode(',', $_POST['services_text'] ?? $reservation['services'] ?? '')));
if(isset($_POST['services'])) {
    $selectedServices = array_map('trim', $_POST['services']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Reservation | Eventify</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php echo eventify_sweetalert_assets(); ?>
    <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: { primary: '#7C00D8', secondary: '#A855F7', soft: '#F6F3FF', dark: '#111827' },
          boxShadow: { soft: '0 15px 35px rgba(124, 0, 216, 0.15)' }
        }
      }
    }
    </script>
    <link rel="stylesheet" href="assets/css/client.css">
</head>
<body class="bg-soft text-dark">
    <header class="sticky top-0 z-30 border-b border-purple-100 bg-white/90 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4">
            <a href="my_reservations.php" class="font-semibold text-primary">Back</a>
            <h1 class="text-xl font-semibold sm:text-2xl">Edit Reservation</h1>
            <?php echo eventify_notification_widget($conn, 'client'); ?>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        <?php if(!empty($errors)): ?>
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700">
                <?php echo htmlspecialchars($errors[0]); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="rounded-[2rem] bg-white p-6 shadow-soft sm:p-8" data-package-budget-form data-loading-form>
            <?php echo eventify_csrf_field(); ?>
            <input type="hidden" name="id" value="<?php echo (int) $id; ?>">
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="text-sm font-bold text-slate-600">Event Name</label>
                    <input type="text" name="event_name" required value="<?php echo htmlspecialchars($form['event_name'] ?? '', ENT_QUOTES); ?>" class="mt-2 w-full rounded-xl border border-purple-100 bg-indigo-50 px-4 py-4 outline-none focus:border-primary focus:bg-white focus:ring-4 focus:ring-purple-100">
                </div>
                <div>
                    <label class="text-sm font-bold text-slate-600">Event Type</label>
                    <select name="event_type" required data-event-type-select class="mt-2 w-full rounded-xl border border-purple-100 bg-indigo-50 px-4 py-4 outline-none focus:border-primary focus:bg-white focus:ring-4 focus:ring-purple-100">
                        <option value="">Select Type</option>
                        <?php foreach(['Wedding', 'Birthday', 'Conference', 'Seminar'] as $type): ?>
                            <option <?php echo ($form['event_type'] ?? '') === $type ? 'selected' : ''; ?>><?php echo $type; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-bold text-slate-600">Package</label>
                    <select name="package_type" required data-package-select class="mt-2 w-full rounded-xl border border-purple-100 bg-indigo-50 px-4 py-4 outline-none focus:border-primary focus:bg-white focus:ring-4 focus:ring-purple-100">
                        <option value="">Select Package</option>
                        <?php foreach(array_keys(eventify_package_prices()) as $package): ?>
                            <option <?php echo ($form['package_type'] ?? '') === $package ? 'selected' : ''; ?>><?php echo $package; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-bold text-slate-600">Date</label>
                    <input type="date" name="event_date" min="<?php echo $today; ?>" required value="<?php echo htmlspecialchars($form['event_date'] ?? '', ENT_QUOTES); ?>" class="mt-2 w-full rounded-xl border border-purple-100 bg-indigo-50 px-4 py-4 outline-none focus:border-primary focus:bg-white focus:ring-4 focus:ring-purple-100">
                </div>
                <div>
                    <label class="text-sm font-bold text-slate-600">Time</label>
                    <input type="time" name="event_time" required value="<?php echo htmlspecialchars($form['event_time'] ?? '', ENT_QUOTES); ?>" class="mt-2 w-full rounded-xl border border-purple-100 bg-indigo-50 px-4 py-4 outline-none focus:border-primary focus:bg-white focus:ring-4 focus:ring-purple-100">
                </div>
                <div>
                    <label class="text-sm font-bold text-slate-600">Venue</label>
                    <input type="text" name="venue" required value="<?php echo htmlspecialchars($form['venue'] ?? '', ENT_QUOTES); ?>" class="mt-2 w-full rounded-xl border border-purple-100 bg-indigo-50 px-4 py-4 outline-none focus:border-primary focus:bg-white focus:ring-4 focus:ring-purple-100">
                </div>
                <div>
                    <label class="text-sm font-bold text-slate-600">Guests</label>
                    <input type="number" name="guests" min="1" required value="<?php echo htmlspecialchars($form['guests'] ?? $form['guest'] ?? '', ENT_QUOTES); ?>" class="mt-2 w-full rounded-xl border border-purple-100 bg-indigo-50 px-4 py-4 outline-none focus:border-primary focus:bg-white focus:ring-4 focus:ring-purple-100">
                </div>
                <div>
                    <label class="text-sm font-bold text-slate-600">Client Name</label>
                    <input type="text" name="client_name" required value="<?php echo htmlspecialchars($form['client_name'] ?? '', ENT_QUOTES); ?>" class="mt-2 w-full rounded-xl border border-purple-100 bg-indigo-50 px-4 py-4 outline-none focus:border-primary focus:bg-white focus:ring-4 focus:ring-purple-100">
                </div>
                <div>
                    <label class="text-sm font-bold text-slate-600">Client Contact</label>
                    <input type="text" name="client_contact" pattern="[0-9+\-\s()]{7,20}" required value="<?php echo htmlspecialchars($form['client_contact'] ?? '', ENT_QUOTES); ?>" class="mt-2 w-full rounded-xl border border-purple-100 bg-indigo-50 px-4 py-4 outline-none focus:border-primary focus:bg-white focus:ring-4 focus:ring-purple-100">
                </div>
                <div class="sm:col-span-2">
                    <label class="text-sm font-bold text-slate-600">Budget</label>
                    <input type="number" name="budget" readonly data-budget-input value="<?php echo htmlspecialchars($form['budget'] ?? '', ENT_QUOTES); ?>" class="mt-2 w-full rounded-xl border border-purple-100 bg-slate-100 px-4 py-4 text-slate-600 outline-none">
                </div>
                <div class="sm:col-span-2">
                    <p class="text-sm font-bold text-slate-600">Services</p>
                    <div class="mt-3 grid gap-3 sm:grid-cols-4">
                        <?php foreach(['Catering', 'Sound', 'Decoration', 'Lights'] as $service): ?>
                            <label class="flex cursor-pointer items-center gap-3 rounded-2xl bg-indigo-50 p-4 font-bold hover:bg-purple-50">
                                <input type="checkbox" name="services[]" value="<?php echo $service; ?>" <?php echo in_array($service, $selectedServices, true) ? 'checked' : ''; ?> class="h-5 w-5 rounded border-purple-200 text-primary focus:ring-primary">
                                <?php echo $service; ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <button type="submit" class="mt-8 w-full rounded-2xl bg-gradient-to-r from-primary to-secondary px-6 py-4 text-lg font-semibold text-white shadow-soft">
                Save Changes
            </button>
        </form>
    </main>

    <?php echo eventify_package_price_script(); ?>
    <?php echo eventify_sweetalert_flash(); ?>
    <script src="assets/js/client.js"></script>
</body>
</html>
