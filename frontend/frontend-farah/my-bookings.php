<?php
session_start();
require_once 'config/db.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$stmt = $pdo->prepare("SELECT b.*, e.title, e.event_date, p.method, p.status as payment_status FROM bookings b JOIN events e ON b.event_id = e.event_id LEFT JOIN payments p ON b.booking_id = p.booking_id WHERE b.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$bookings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings & QR Tickets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #0b0f19; color: #f8f9fa; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .navbar { background: rgba(11, 15, 25, 0.95); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255, 255, 255, 0.08); }
        .navbar-brand { font-weight: 700; letter-spacing: -0.5px; }
        .nav-link { color: #94a3b8 !important; font-weight: 500; transition: color 0.2s; }
        .nav-link:hover, .nav-link.active { color: #ffffff !important; }
        .card-custom { background: #131b2e; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.3); }
        .table-dark { background-color: transparent !important; color: #f8f9fa; }
        .table-dark th { border-color: rgba(255, 255, 255, 0.1); color: #94a3b8; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .table-dark td { border-color: rgba(255, 255, 255, 0.06); vertical-align: middle; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
                <span class="p-2 rounded-3 text-white shadow-sm" style="background: #7c3aed;">✦</span> Event Booking
            </a>
            <div class="navbar-nav ms-auto flex-row gap-4 align-items-center">
                <a class="nav-link" href="index.php">Home</a>
                <a class="nav-link" href="venues.php">Venues</a>
                <a class="nav-link active" href="my-bookings.php">My Bookings</a>
                <a class="nav-link text-danger" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-5" style="max-width: 1000px;">
        <div class="mb-4">
            <span class="badge bg-purple mb-2 px-3 py-2 rounded-pill" style="background: rgba(124, 58, 237, 0.3); color: #e9d5ff; border: 1px solid rgba(139, 92, 246, 0.4);">🎟️ Passes & Access</span>
            <h2 class="fw-bold text-white display-6 mb-1">My Bookings & QR Tickets</h2>
            <p class="text-secondary">View your reserved experiences and digital entry passes.</p>
        </div>

        <div class="card card-custom p-4">
            <div class="table-responsive">
                <table class="table table-dark table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Event</th>
                            <th>Date</th>
                            <th>Qty</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th class="text-center">QR Ticket</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($bookings)): ?>
                            <?php foreach($bookings as $b): 
                                $qrData = urlencode("BookingID: #{$b['booking_id']} | Event: {$b['title']} | Qty: {$b['quantity']} | Total: RM{$b['total_price']}");
                                $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={$qrData}";
                            ?>
                            <tr>
                                <td class="fw-bold" style="color: #a78bfa;">#<?= $b['booking_id'] ?></td>
                                <td><strong class="text-white"><?= htmlspecialchars($b['title']) ?></strong></td>
                                <td class="text-secondary"><?= htmlspecialchars($b['event_date']) ?></td>
                                <td><?= htmlspecialchars($b['quantity']) ?></td>
                                <td class="fw-semibold">RM<?= number_format($b['total_price'], 2) ?></td>
                                <td>
                                     <?php if($b['status'] === 'confirmed'): ?>
                                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 px-2 py-1">Confirmed</span>
                                        <a href="cancel-booking.php?id=<?= $b['booking_id'] ?>" class="btn btn-outline-danger btn-sm ms-2 py-0 px-2" onclick="return confirm('Are you sure you want to cancel this booking?');">Cancel</a>
                                    <?php else: ?>
                                         <span class="badge bg-secondary px-2 py-1"><?= htmlspecialchars($b['status']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="bg-white p-2 rounded-2 d-inline-block shadow-sm">
                                        <img src="<?= $qrUrl ?>" alt="QR" style="width: 55px; height: 55px;">
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-secondary">You haven't made any bookings yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>