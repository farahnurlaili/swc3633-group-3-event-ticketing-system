<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organiser') {
    header("Location: login.php");
    exit();
}

$organiserId = $_SESSION['user_id'];
$message = '';

// Handle Event Status Updates or Deletion if requested by organiser
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_event') {
    $event_id = intval($_POST['event_id']);
    // Ensure the event belongs to this organiser
    $stmt = $pdo->prepare("DELETE FROM events WHERE event_id = ? AND organiser_id = ?");
    if ($stmt->execute([$event_id, $organiserId])) {
        $message = "Your event has been successfully deleted.";
    }
}

// Fetch events belonging to this organiser
$stmt = $pdo->prepare("
    SELECT e.*, v.venue_name, c.category_name, 
           (e.total_tickets - e.available_tickets) AS booked_seats
    FROM events e
    JOIN venues v ON e.venue_id = v.venue_id
    JOIN categories c ON e.category_id = c.category_id
    WHERE e.organiser_id = ?
    ORDER BY e.event_date DESC
");
$stmt->execute([$organiserId]);
$myEvents = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Organiser Control Panel - Eventra</title>
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
    </style>
</head>
<body>
    <!-- Dedicated Organiser Top Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top py-3">
        <div class="container">
            <span class="navbar-brand d-flex align-items-center gap-2">
                <span class="p-2 rounded-3 text-white shadow-sm" style="background: #7c3aed;">✦</span> Eventra Organiser Portal
            </span>
            <div class="navbar-nav ms-auto flex-row gap-4 align-items-center">
                <span class="text-info small fw-semibold">🎪 Logged in as Organiser</span>
                <a class="nav-link text-danger ms-2" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <?php if($message): ?><div class="alert alert-success border-0 bg-success bg-opacity-25 text-success mb-4"><?= $message ?></div><?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <span class="badge px-3 py-2 rounded-pill mb-2" style="background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3);">🎪 Event Management Workstation</span>
                <h2 class="fw-bold text-white display-6 mb-1">Organiser Dashboard</h2>
                <p class="text-secondary mb-0">Create, manage, and monitor your scheduled events and ticket sales.</p>
            </div>
            <div>
                <a href="create-event.php" class="btn btn-custom px-4 py-2">+ Create New Event</a>
            </div>
        </div>

        <div class="card card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold text-white m-0">Your Hosted Events</h4>
                <span class="text-secondary small stat-number"><?= count($myEvents) ?> total events</span>
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Venue Location</th>
                            <th>Category</th>
                            <th>Date & Time</th>
                            <th>Ticket Price</th>
                            <th>Booked Capacity</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($myEvents)): ?>
                            <?php foreach($myEvents as $ev): ?>
                            <tr>
                                <td><strong class="text-white"><?= htmlspecialchars($ev['title']) ?></strong></td>
                                <td class="text-secondary small"><?= htmlspecialchars($ev['venue_name']) ?></td>
                                <td><span class="badge" style="background: rgba(124, 58, 237, 0.3); color: #d8b4fe; border: 1px solid rgba(139, 92, 246, 0.4);"><?= htmlspecialchars($ev['category_name']) ?></span></td>
                                <td class="text-secondary"><?= htmlspecialchars($ev['event_date']) ?></td>
                                <td class="fw-semibold price-tag">RM<?= number_format($ev['ticket_price'], 2) ?></td>
                                <td><span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25"><?= $ev['booked_seats'] ?> / <?= $ev['total_tickets'] ?></span></td>
                                <td><span class="badge bg-secondary"><?= ucfirst($ev['status']) ?></span></td>
                                <td class="text-end">
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this event?');">
                                        <input type="hidden" name="action" value="delete_event">
                                        <input type="hidden" name="event_id" value="<?= $ev['event_id'] ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm py-1 px-2">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-secondary">You haven't created any events yet. Click "+ Create New Event" above to get started.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>