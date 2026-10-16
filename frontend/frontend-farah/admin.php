<?php
session_start();
require_once 'config/db.php';

// Ensure only admins can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = '';
$error = '';

// Handle Event Deletion or Venue Creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete_event') {
        $event_id = intval($_POST['event_id']);
        
        $pdo->beginTransaction();
        try {
            // 1. Delete associated payments first
            $stmtPay = $pdo->prepare("DELETE p FROM payments p JOIN bookings b ON p.booking_id = b.booking_id WHERE b.event_id = ?");
            $stmtPay->execute([$event_id]);

            // 2. Delete associated bookings
            $stmtBook = $pdo->prepare("DELETE FROM bookings WHERE event_id = ?");
            $stmtBook->execute([$event_id]);

            // 3. Finally, delete the event
            $stmtEvent = $pdo->prepare("DELETE FROM events WHERE event_id = ?");
            $stmtEvent->execute([$event_id]);

            $pdo->commit();
            $message = "Event and its associated records were successfully deleted from system catalog.";
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Failed to delete event: " . $e->getMessage();
        }
    } elseif ($_POST['action'] === 'create_venue') {
        $venue_name = trim($_POST['venue_name']);
        $address = trim($_POST['address']);
        $city = trim($_POST['city']);
        $capacity = intval($_POST['capacity']);

        if (!empty($venue_name) && $capacity > 0) {
            $stmt = $pdo->prepare("INSERT INTO venues (venue_name, address, city, capacity) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$venue_name, $address, $city, $capacity])) {
                $message = "New venue successfully registered!";
            }
        } else {
            $error = "Please fill in all venue details correctly.";
        }
    }
}

// Fetch global statistics
$totalBookings = $pdo->query("SELECT COUNT(*) AS total FROM bookings WHERE status = 'confirmed'")->fetch()['total'] ?? 0;
$totalRevenue = $pdo->query("SELECT SUM(amount) AS revenue FROM payments WHERE status = 'paid'")->fetch()['revenue'] ?? 0.00;
$totalEvents = $pdo->query("SELECT COUNT(*) AS total FROM events")->fetch()['total'] ?? 0;
$totalVenues = $pdo->query("SELECT COUNT(*) AS total FROM venues")->fetch()['total'] ?? 0;

// Fetch event sales performance
$eventsQuery = $pdo->query("
    SELECT e.event_id, e.title, e.event_date, e.total_tickets, e.status, v.venue_name,
           (e.total_tickets - e.available_tickets) AS booked_seats,
           COALESCE(SUM(CASE WHEN p.status = 'paid' THEN p.amount ELSE 0 END), 0) AS gross_sales
    FROM events e
    LEFT JOIN venues v ON e.venue_id = v.venue_id
    LEFT JOIN bookings b ON e.event_id = b.event_id AND b.status = 'confirmed'
    LEFT JOIN payments p ON b.booking_id = p.booking_id
    GROUP BY e.event_id
    ORDER BY e.event_date DESC
");
$eventsList = $eventsQuery->fetchAll();

// Fetch registered users
$usersList = $pdo->query("SELECT user_id, full_name, email, role, phone, created_at FROM users ORDER BY user_id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Control Panel - Eventra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Sora:wght@600;700&family=Space+Grotesk:wght@500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --font-heading: 'Sora', sans-serif;
            --font-body: 'Inter', sans-serif;
            --font-accent: 'Space Grotesk', sans-serif;
        }
        body { background-color: #0b0f19; color: #f8f9fa; font-family: var(--font-body); }
        h1, h2, h3, h4, h5, .navbar-brand { font-family: var(--font-heading); }
        .stat-val { font-family: var(--font-accent); }
        .navbar { background: rgba(11, 15, 25, 0.95); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255, 255, 255, 0.08); }
        .card-custom { background: #131b2e; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 18px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        .table-dark { background-color: transparent !important; color: #f8f9fa; }
        .table-dark th { border-color: rgba(255, 255, 255, 0.1); color: #94a3b8; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .table-dark td { border-color: rgba(255, 255, 255, 0.06); vertical-align: middle; }
        .btn-custom { background: linear-gradient(135deg, #8b5cf6, #6d28d9); border: none; color: white; font-weight: 600; border-radius: 10px; transition: all 0.25s ease; }
        .btn-custom:hover { background: linear-gradient(135deg, #7c3aed, #5b21b6); color: white; }
        .modal-content { background-color: #131b2e; border: 1px solid rgba(255, 255, 255, 0.1); color: #fff; }
        .form-control { background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(255, 255, 255, 0.1); color: #fff; }
        .form-control:focus { background: rgba(15, 23, 42, 0.95); border-color: #8b5cf6; color: #fff; box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.15); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top py-3">
        <div class="container">
            <span class="navbar-brand d-flex align-items-center gap-2">
                <span class="p-2 rounded-3 text-white shadow-sm" style="background: #7c3aed;">✦</span> Eventra Admin Control
            </span>
            <div class="navbar-nav ms-auto flex-row gap-4 align-items-center">
                <span class="text-warning small fw-semibold">🛡️ Logged in as Administrator</span>
                <a class="nav-link text-danger ms-2" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <?php if($message): ?><div class="alert alert-success border-0 bg-success bg-opacity-25 text-success mb-4"><?= $message ?></div><?php endif; ?>
        <?php if($error): ?><div class="alert alert-danger border-0 bg-danger bg-opacity-25 text-danger mb-4"><?= $error ?></div><?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <span class="badge px-3 py-2 rounded-pill mb-2" style="background: rgba(234, 179, 8, 0.2); color: #fde047; border: 1px solid rgba(234, 179, 8, 0.3);">⚡ Dedicated Operations Panel</span>
                <h2 class="fw-bold text-white display-6 mb-0">System Control Workstation</h2>
                <p class="text-secondary mb-0">Monitor financial performance, manage global events, and manage venues.</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-light px-3 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#createVenueModal">+ Register Venue</button>
                <a href="create-event.php" class="btn btn-custom px-4 py-2">+ Create Event</a>
            </div>
        </div>

        <!-- System Overview Metrics -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="card card-custom p-4">
                    <span class="text-secondary small mb-1">Confirmed Bookings</span>
                    <h3 class="fw-bold text-white stat-val m-0"><?= number_format($totalBookings) ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-custom p-4">
                    <span class="text-secondary small mb-1">Gross Sales Revenue</span>
                    <h3 class="fw-bold text-success stat-val m-0">RM<?= number_format($totalRevenue, 2) ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-custom p-4">
                    <span class="text-secondary small mb-1">Total Active Events</span>
                    <h3 class="fw-bold text-white stat-val m-0"><?= number_format($totalEvents) ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-custom p-4">
                    <span class="text-secondary small mb-1">Managed Venues</span>
                    <h3 class="fw-bold text-white stat-val m-0"><?= number_format($totalVenues) ?></h3>
                </div>
            </div>
        </div>

        <!-- Event & Ticket Management -->
        <div class="card card-custom p-4 mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold text-white m-0">Event Catalog & Ticket Inventory</h4>
                <a href="create-event.php" class="btn btn-custom btn-sm px-3 py-2">+ New Event</a>
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Event Title</th>
                            <th>Venue Location</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Booked Capacity</th>
                            <th>Gross Sales</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($eventsList as $ev): ?>
                        <tr>
                            <td><strong class="text-white"><?= htmlspecialchars($ev['title']) ?></strong></td>
                            <td class="text-secondary small"><?= htmlspecialchars($ev['venue_name'] ?? 'Unassigned') ?></td>
                            <td class="text-secondary"><?= htmlspecialchars($ev['event_date']) ?></td>
                            <td>
                                <?php if($ev['status'] === 'published'): ?>
                                    <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 px-2 py-1">Published</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary px-2 py-1"><?= ucfirst($ev['status']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?= $ev['booked_seats'] ?> / <?= $ev['total_tickets'] ?></td>
                            <td class="fw-semibold text-success stat-val">RM<?= number_format($ev['gross_sales'], 2) ?></td>
                            <td class="text-end">
                                <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this event and its associated bookings?');">
                                    <input type="hidden" name="action" value="delete_event">
                                    <input type="hidden" name="event_id" value="<?= $ev['event_id'] ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm py-1 px-2">Delete Event</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- User Accounts & Roles -->
        <div class="card card-custom p-4">
            <h4 class="fw-bold text-white mb-3">Registered User Directory</h4>
            <div class="table-responsive">
                <table class="table table-dark table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email Address</th>
                            <th>Phone Number</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($usersList as $u): ?>
                        <tr>
                            <td class="fw-bold" style="color: #a78bfa;">#<?= $u['user_id'] ?></td>
                            <td class="text-white fw-medium"><?= htmlspecialchars($u['full_name']) ?></td>
                            <td class="text-secondary"><?= htmlspecialchars($u['email']) ?></td>
                            <td class="text-secondary small"><?= htmlspecialchars($u['phone']) ?></td>
                            <td>
                                <?php if($u['role'] === 'admin'): ?>
                                    <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25 px-2 py-1">Admin</span>
                                <?php elseif($u['role'] === 'organiser'): ?>
                                    <span class="badge bg-info bg-opacity-25 text-info border border-info border-opacity-25 px-2 py-1">Organiser</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary px-2 py-1">Customer</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal for Registering Venues -->
    <div class="modal fade" id="createVenueModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold text-white">Register New Venue</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create_venue">
                        <div class="mb-3">
                            <label class="form-label text-secondary small fw-bold">VENUE NAME</label>
                            <input type="text" class="form-control" name="venue_name" required placeholder="e.g., Arena Seri Nusantara">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary small fw-bold">STREET ADDRESS</label>
                            <input type="text" class="form-control" name="address" required placeholder="e.g., 1 Jalan Stadium">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-secondary small fw-bold">CITY</label>
                                <input type="text" class="form-control" name="city" required placeholder="e.g., Kuala Lumpur">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-secondary small fw-bold">CAPACITY</label>
                                <input type="number" class="form-control" name="capacity" required placeholder="e.g., 15000">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-custom px-4">Save Venue</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>