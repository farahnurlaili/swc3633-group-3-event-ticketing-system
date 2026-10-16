<?php
session_start();
require_once 'config/db.php';

$event_id =$_GET['id'] ?? null;
if (!$event_id) {
    header("Location: index.php");
    exit();
}

$stmt =$pdo->prepare("
    SELECT e.*, v.venue_name, v.address, v.city, v.capacity, c.category_name 
    FROM events e
    JOIN venues v ON e.venue_id = v.venue_id
    JOIN categories c ON e.category_id = c.category_id
    WHERE e.event_id = ?
");
$stmt->execute([$event_id]);
$event =$stmt->fetch();

if (!$event) {
    echo "Event not found.";
    exit();
}

function get_local_event_image($eventId) {$imageMap = [
        1 => 'images/rockfest.jpg',
        2 => 'images/techsummit.jpg',
        3 => 'images/futsal.jpg',
        4 => 'images/seniwarisan.jpg',
        5 => 'images/comedy.jpg',
        6 => 'images/pitchday.jpg'
    ];
    return $imageMap[$eventId] ?? 'images/default.jpg';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><= htmlspecialchars($event['title']) ?> - Eventra</title>
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
        .price-tag, .event-date, .stat-val { font-family: var(--font-accent); }
        .navbar { background: rgba(11, 15, 25, 0.95); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255, 255, 255, 0.08); }
        .navbar-brand { font-weight: 700; letter-spacing: -0.5px; }
        .nav-link { color: #94a3b8 !important; font-weight: 500; transition: color 0.2s; position: relative; }
        .nav-link:hover, .nav-link.active { color: #ffffff !important; }
        .nav-link::after { content: ''; position: absolute; width: 0; height: 2px; bottom: 4px; left: 0; background-color: #8b5cf6; transition: width 0.3s ease; }
        .nav-link:hover::after, .nav-link.active::after { width: 100%; }
        .card-custom { background: #131b2e; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.3); }
        .event-banner-img { width: 100%; height: 380px; object-fit: cover; border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.08); }
        .form-control { background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(255, 255, 255, 0.1); color: #fff; padding: 12px 16px; border-radius: 10px; }
        .form-control:focus { background: rgba(15, 23, 42, 0.95); border-color: #8b5cf6; color: #fff; box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.15); }
        .btn-custom { background: linear-gradient(135deg, #8b5cf6, #6d28d9); border: none; color: white; font-weight: 600; border-radius: 10px; transition: all 0.25s ease; box-shadow: 0 4px 12px rgba(109, 40, 217, 0.3); }
        .btn-custom:hover { background: linear-gradient(135deg, #7c3aed, #5b21b6); transform: translateY(-2px); box-shadow: 0 6px 18px rgba(124, 58, 237, 0.45); color: white; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
                <span class="p-2 rounded-3 text-white shadow-sm" style="background: #7c3aed;">✦</span> Eventra
            </a>
            <div class="navbar-nav ms-auto flex-row gap-4 align-items-center">
                <a class="nav-link" href="index.php">Home</a>
                <a class="nav-link" href="venues.php">Venues</a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a class="nav-link" href="my-bookings.php">My Bookings</a>
                    <span class="text-secondary small ms-2">Hi, <?= htmlspecialchars($_SESSION['full_name'] ?? '') ?></span>
                    <a class="nav-link text-danger ms-2" href="logout.php">Logout</a>
                <?php else: ?>
                    <a class="nav-link" href="login.php">Login</a>
                    <a class="nav-link btn btn-custom px-3 py-1 text-white" href="register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container my-5" style="max-width: 1000px;">
        <a href="index.php" class="text-decoration-none text-secondary mb-4 d-inline-block">&larr; Back to Events</a>

        <div class="position-relative mb-4">
            <img src="<?= get_local_event_image($event['event_id']) ?>" class="event-banner-img" alt="<?= htmlspecialchars($event['title']) ?>">
            <span class="position-absolute top-0 end-0 m-4 badge px-3 py-2 rounded-pill" style="background: rgba(124, 58, 237, 0.85); color: #fff; backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.2);">
                <?= htmlspecialchars($event['category_name']) ?>
            </span>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <h1 class="fw-bold text-white mb-3 display-5"><?= htmlspecialchars($event['title']) ?></h1>
                
                <div class="d-flex flex-wrap gap-4 mb-4 text-secondary">
                    <div>📍 <strong class="text-white"><?= htmlspecialchars($event['venue_name']) ?></strong> (<?= htmlspecialchars($event['city']) ?>)</div>
                    <div>📅 <span class="event-date text-white"><?= htmlspecialchars($event['event_date']) ?></span></div>
                    <div>🕒 <span class="text-white"><?= htmlspecialchars($event['start_time']) ?> - <?= htmlspecialchars($event['end_time']) ?></span></div>
                </div>

                <div class="card card-custom p-4 mb-4">
                    <h5 class="fw-bold text-white mb-3">About This Event</h5>
                    <p class="text-secondary lh-lg mb-0"><?= nl2br(htmlspecialchars($event['description'])) ?></p>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card card-custom p-4 sticky-top" style="top: 100px;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-secondary small fw-bold">TICKET PRICE</span>
                        <span class="fs-3 fw-bold text-white price-tag">RM<?= number_format($event['ticket_price'], 2) ?></span>
                    </div>

                    <div class="mb-4 pt-3 border-top border-secondary border-opacity-25">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-secondary small">Ticket Availability:</span>
                            <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 stat-val px-2 py-1">
                                <?= $event['available_tickets'] ?> remaining
                            </span>
                        </div>
                    </div>

                    <div id="alertBox" class="d-none alert border-0 mb-3 small text-center"></div>

                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <div class="alert alert-warning border-0 bg-warning bg-opacity-25 text-warning small text-center mb-3">
                            Please sign in to your account to book tickets.
                        </div>
                        <a href="login.php" class="btn btn-custom w-100 py-3">Login to Book</a>
                    <?php elseif ($event['available_tickets'] <= 0): ?>
                        <button class="btn btn-secondary w-100 py-3 disabled" disabled>Sold Out</button>
                    <?php else: ?>
                        <form id="bookingForm" onsubmit="event.preventDefault(); submitBooking();">
                            <input type="hidden" id="eventId" value="<?= $event['event_id'] ?>">
                            <div class="mb-3">
                                <label class="form-label text-secondary small fw-bold">SELECT QUANTITY</label>
                                <input type="number" id="ticketQty" class="form-control" value="1" min="1" max="<?= $event['available_tickets'] ?>" required>
                            </div>
                            <button type="submit" id="bookBtn" class="btn btn-custom w-100 py-3">Confirm Booking & Pay</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    async function submitBooking() {
        const eventId = document.getElementById('eventId').value;
        const quantity = document.getElementById('ticketQty').value;
        const submitBtn = document.getElementById('bookBtn');
        const alertBox = document.getElementById('alertBox');

        alertBox.classList.add('d-none');
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status"></span> Processing Booking...`;

        try {
            const response = await fetch('api/bookings.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    event_id: parseInt(eventId), 
                    quantity: parseInt(quantity) 
                })
            });

            const result = await response.json();

            if (result.success) {
                alertBox.className = "alert alert-success border-0 bg-success bg-opacity-25 text-success mb-3 small text-center";
                alertBox.innerText = result.message;
                alertBox.classList.remove('d-none');
                
                setTimeout(() => {
                    window.location.href = 'my-bookings.php';
                }, 1200);
            } else {
                alertBox.className = "alert alert-danger border-0 bg-danger bg-opacity-25 text-danger mb-3 small text-center";
                alertBox.innerText = result.message;
                alertBox.classList.remove('d-none');
                
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Confirm Booking & Pay';
            }
        } catch (error) {
            console.error('API Error:', error);
            alertBox.className = "alert alert-danger border-0 bg-danger bg-opacity-25 text-danger mb-3 small text-center";
            alertBox.innerText = 'An unexpected server error occurred. Please try again.';
            alertBox.classList.remove('d-none');
            
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Confirm Booking & Pay';
        }
    }
    </script>
</body>
</html>