<?php
session_start();

// Automatically redirect admins and organisers to their dedicated portals
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin.php");
        exit();
    } elseif ($_SESSION['role'] === 'organiser') {
        header("Location: organiser.php");
        exit();
    }
}

require_once 'config/db.php';

$search = $_GET['search'] ?? '';
$sql = "SELECT e.*, v.venue_name, v.city, c.category_name 
        FROM events e
        JOIN venues v ON e.venue_id = v.venue_id
        JOIN categories c ON e.category_id = c.category_id
        WHERE e.status = 'published'";

if (!empty($search)) {
    $sql .= " AND (e.title LIKE ? OR v.venue_name LIKE ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$search%", "%$search%"]);
} else {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
}
$events = $stmt->fetchAll();

function get_local_event_image($eventId) {
    $imageMap = [
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
    <title>Eventra - Discover Experiences Worth Remembering</title>
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
        .price-tag, .event-date, .stat-number { font-family: var(--font-accent); }
        .navbar { background: rgba(11, 15, 25, 0.95); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255, 255, 255, 0.08); }
        .navbar-brand { font-weight: 700; letter-spacing: -0.5px; }
        .nav-link { color: #94a3b8 !important; font-weight: 500; transition: color 0.2s ease; position: relative; }
        .nav-link:hover, .nav-link.active { color: #ffffff !important; }
        .nav-link::after { content: ''; position: absolute; width: 0; height: 2px; bottom: 4px; left: 0; background-color: #8b5cf6; transition: width 0.3s ease; }
        .nav-link:hover::after, .nav-link.active::after { width: 100%; }
        .hero-section { background: linear-gradient(rgba(11, 15, 25, 0.75), rgba(11, 15, 25, 0.95)), url('https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&w=1600&q=80') center/cover; border-radius: 24px; padding: 80px 40px; border: 1px solid rgba(255, 255, 255, 0.08); margin-top: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.5); }
        .event-card { background: #131b2e; border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 18px; overflow: hidden; transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1); box-shadow: 0 4px 16px rgba(0,0,0,0.25); cursor: pointer; position: relative; }
        .event-card:hover { transform: translateY(-6px) scale(1.02); border-color: rgba(139, 92, 246, 0.6); box-shadow: 0 16px 35px rgba(139, 92, 246, 0.22); }
        .image-container { position: relative; overflow: hidden; height: 210px; }
        .event-img { height: 100%; object-fit: cover; width: 100%; transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1); }
        .event-card:hover .event-img { transform: scale(1.08); }
        .image-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(11, 15, 25, 0.85), rgba(11, 15, 25, 0.2)); opacity: 0; transition: opacity 0.3s ease; display: flex; align-items: center; justify-content: center; }
        .event-card:hover .image-overlay { opacity: 1; }
        .view-details-pill { background: rgba(139, 92, 246, 0.9); color: #fff; padding: 8px 18px; border-radius: 30px; font-weight: 600; font-size: 0.85rem; transform: translateY(10px); transition: transform 0.3s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.3); }
        .event-card:hover .view-details-pill { transform: translateY(0); }
        .badge-category { background: rgba(124, 58, 237, 0.25); color: #d8b4fe; font-weight: 600; font-size: 0.75rem; padding: 6px 12px; border-radius: 30px; border: 1px solid rgba(139, 92, 246, 0.35); }
        .btn-custom { background: linear-gradient(135deg, #8b5cf6, #6d28d9); border: none; color: white; font-weight: 600; border-radius: 10px; transition: all 0.25s ease; box-shadow: 0 4px 12px rgba(109, 40, 217, 0.3); }
        .btn-custom:hover { background: linear-gradient(135deg, #7c3aed, #5b21b6); transform: translateY(-2px); box-shadow: 0 6px 18px rgba(124, 58, 237, 0.45); color: white; }
        .form-control:focus { background-color: rgba(15, 23, 42, 0.95); border-color: #8b5cf6; box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.15); color: #fff; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
                <span class="p-2 rounded-3 text-white shadow-sm" style="background: #7c3aed;">✦</span> Eventra
            </a>
            <div class="navbar-nav ms-auto flex-row gap-4 align-items-center">
                <a class="nav-link active" href="index.php">Home</a>
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

    <div class="container my-4">
        <div class="hero-section mb-5">
            <span class="badge mb-3 px-3 py-2 rounded-pill" style="background: rgba(124, 58, 237, 0.3); color: #e9d5ff; border: 1px solid rgba(139, 92, 246, 0.4);">✨ Your Next Great Story Starts Here</span>
            <h1 class="display-4 fw-bold text-white mb-3" style="max-width: 650px; line-height: 1.2;">Discover Experiences Worth Remembering</h1>
            <p class="text-secondary mb-4 fs-5" style="max-width: 550px;">From unforgettable concerts to inspiring conversations, find and book instantly.</p>
            <form action="index.php" method="GET" class="d-flex gap-2 p-2 rounded-pill border border-secondary shadow-lg" style="max-width: 500px; background: rgba(15, 23, 42, 0.9);">
                <input type="text" name="search" class="form-control bg-transparent border-0 text-white shadow-none ps-3" placeholder="Search event title or venue..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="btn btn-custom rounded-pill px-4">Search</button>
            </form>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-white m-0">Featured Events</h3>
            <span class="text-secondary small stat-number"><?= count($events) ?> available events</span>
        </div>

        <div class="row">
            <?php if (!empty($events)): ?>
                <?php foreach($events as $event): ?>
                    <div class="col-md-4 mb-4">
                        <div class="event-card h-100 d-flex flex-column" onclick="window.location.href='event-details.php?id=<?= $event['event_id'] ?>'">
                            <div class="image-container">
                                <img src="<?= get_local_event_image($event['event_id']) ?>" class="event-img" alt="<?= htmlspecialchars($event['title']) ?>">
                                <div class="image-overlay">
                                    <span class="view-details-pill">View Details</span>
                                </div>
                                <div class="position-absolute top-0 end-0 m-3" style="z-index: 2;">
                                    <span class="badge-category"><?= htmlspecialchars($event['category_name']) ?></span>
                                </div>
                            </div>
                            <div class="p-4 d-flex flex-column flex-grow-1">
                                <h5 class="fw-bold text-white mb-2"><?= htmlspecialchars($event['title']) ?></h5>
                                <p class="text-secondary small mb-3">📍 <?= htmlspecialchars($event['venue_name']) ?> • <span class="event-date">📅 <?= htmlspecialchars($event['event_date']) ?></span></p>
                                <div class="mt-auto d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-25">
                                    <span class="fw-bold text-white fs-5 price-tag">RM<?= number_format($event['ticket_price'], 2) ?></span>
                                    <a href="event-details.php?id=<?= $event['event_id'] ?>" class="btn btn-custom btn-sm px-4 py-2" onclick="event.stopPropagation();">Book Now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-secondary fs-5">No events found matching your search criteria.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>