<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'organiser'])) {
    header("Location: login.php");
    exit();
}

$venues = $pdo->query("SELECT * FROM venues ORDER BY venue_name ASC")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories ORDER BY category_name ASC")->fetchAll();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category_id = intval($_POST['category_id']);
    $venue_id = intval($_POST['venue_id']);
    $event_date = $_POST['event_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $ticket_price = floatval($_POST['ticket_price']);
    $total_tickets = intval($_POST['total_tickets']);
    $organiser_id = $_SESSION['user_id'];

    if (!empty($title) && $total_tickets > 0) {
        $stmt = $pdo->prepare("INSERT INTO events (title, description, category_id, venue_id, event_date, start_time, end_time, ticket_price, total_tickets, available_tickets, organiser_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'published')");
        if ($stmt->execute([$title, $description, $category_id, $venue_id, $event_date, $start_time, $end_time, $ticket_price, $total_tickets, $total_tickets, $organiser_id])) {
            $success = "Event successfully created and published!";
        } else {
            $error = "Failed to create event.";
        }
    } else {
        $error = "Please fill in all required fields correctly.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Event - Eventra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Sora:wght@600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #0b0f19; color: #f8f9fa; font-family: 'Inter', sans-serif; }
        h1, h2, h3 { font-family: 'Sora', sans-serif; }
        .navbar { background: rgba(11, 15, 25, 0.95); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255, 255, 255, 0.08); }
        .card-custom { background: #131b2e; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.3); }
        .form-control, .form-select { background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(255, 255, 255, 0.1); color: #fff; }
        .form-control:focus, .form-select:focus { background: rgba(15, 23, 42, 0.95); border-color: #8b5cf6; color: #fff; box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.15); }
        .btn-custom { background: linear-gradient(135deg, #8b5cf6, #6d28d9); border: none; color: white; font-weight: 600; border-radius: 10px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top py-3">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><span class="p-2 rounded-3 text-white shadow-sm" style="background: #7c3aed;">✦</span> Eventra</a>
            <div class="navbar-nav ms-auto flex-row gap-4 align-items-center">
                <a class="nav-link" href="organiser.php">Dashboard</a>
                <a class="nav-link text-danger" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-5" style="max-width: 700px;">
        <a href="organiser.php" class="text-decoration-none text-secondary mb-4 d-inline-block">&larr; Back to Dashboard</a>
        <div class="card card-custom p-5">
            <h2 class="fw-bold text-white mb-4">Create New Event</h2>

            <?php if($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
            <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label text-secondary small fw-bold">EVENT TITLE</label>
                    <input type="text" class="form-control" name="title" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-secondary small fw-bold">DESCRIPTION</label>
                    <textarea class="form-control" name="description" rows="3" required></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-secondary small fw-bold">CATEGORY</label>
                        <select class="form-select" name="category_id" required>
                            <?php foreach($categories as $c): ?>
                                <option value="<?= $c['category_id'] ?>"><?= htmlspecialchars($c['category_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-secondary small fw-bold">VENUE</label>
                        <select class="form-select" name="venue_id" required>
                            <?php foreach($venues as $v): ?>
                                <option value="<?= $v['venue_id'] ?>"><?= htmlspecialchars($v['venue_name']) ?> (Max: <?= $v['capacity'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label text-secondary small fw-bold">EVENT DATE</label>
                        <input type="date" class="form-control" name="event_date" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label text-secondary small fw-bold">START TIME</label>
                        <input type="time" class="form-control" name="start_time" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label text-secondary small fw-bold">END TIME</label>
                        <input type="time" class="form-control" name="end_time" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label text-secondary small fw-bold">TICKET PRICE (RM)</label>
                        <input type="number" step="0.01" class="form-control" name="ticket_price" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label text-secondary small fw-bold">TOTAL TICKETS AVAILABLE</label>
                        <input type="number" class="form-control" name="total_tickets" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-custom w-100 py-3">Publish Event</button>
            </form>
        </div>
    </div>
</body>
</html>