<?php
session_start();
require_once 'config/db.php';
$venues = $pdo->query("SELECT * FROM venues ORDER BY venue_name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Venues - Eventra</title>
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
        .navbar-brand { font-weight: 700; letter-spacing: -0.5px; }
        .nav-link { color: #94a3b8 !important; font-weight: 500; transition: color 0.2s; position: relative; }
        .nav-link:hover, .nav-link.active { color: #ffffff !important; }
        .nav-link::after { content: ''; position: absolute; width: 0; height: 2px; bottom: 4px; left: 0; background-color: #8b5cf6; transition: width 0.3s ease; }
        .nav-link:hover::after, .nav-link.active::after { width: 100%; }
        .venue-card { background: #131b2e; border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 18px; transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1); box-shadow: 0 4px 16px rgba(0,0,0,0.25); }
        .venue-card:hover { transform: translateY(-4px) scale(1.01); border-color: rgba(139, 92, 246, 0.5); box-shadow: 0 14px 30px rgba(139, 92, 246, 0.18); }
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
                <a class="nav-link active" href="venues.php">Venues</a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <?php if($_SESSION['role'] === 'customer'): ?>
                        <a class="nav-link" href="my-bookings.php">My Bookings</a>
                    <?php elseif($_SESSION['role'] === 'organiser'): ?>
                        <a class="nav-link" href="organiser.php">Organiser Portal</a>
                    <?php elseif($_SESSION['role'] === 'admin'): ?>
                        <a class="nav-link" href="admin.php">Admin Portal</a>
                    <?php endif; ?>
                    <span class="text-secondary small ms-2">Hi, <?= htmlspecialchars($_SESSION['full_name'] ?? '') ?></span>
                    <a class="nav-link text-danger ms-2" href="logout.php">Logout</a>
                <?php else: ?>
                    <a class="nav-link" href="login.php">Login</a>
                    <a class="nav-link btn btn-custom px-3 py-1 text-white" href="register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="mb-4">
            <span class="badge mb-2 px-3 py-2 rounded-pill" style="background: rgba(124, 58, 237, 0.3); color: #e9d5ff; border: 1px solid rgba(139, 92, 246, 0.4);">🏛️ Destinations & Locations</span>
            <h2 class="fw-bold text-white display-6 mb-1">Explore Venues</h2>
            <p class="text-secondary">Discover where world-class events take place.</p>
        </div>

        <div class="row">
            <?php foreach($venues as $v): ?>
                <div class="col-md-4 mb-4">
                    <div class="venue-card p-4 h-100 d-flex flex-column">
                        <h4 class="text-white fw-bold mb-2"><?= htmlspecialchars($v['venue_name']) ?></h4>
                        <p class="text-secondary small mb-3">📍 <?= htmlspecialchars($v['address']) ?>, <?= htmlspecialchars($v['city']) ?></p>
                        
                        <div class="mt-auto pt-3 border-top border-secondary border-opacity-25 d-flex align-items-center justify-content-between">
                            <span class="small fw-semibold stat-val" style="color: #a78bfa;">Capacity: <?= number_format($v['capacity']) ?> guests</span>
                            <a href="venue-details.php?id=<?= $v['venue_id'] ?>" class="btn btn-custom btn-sm px-3 py-2">View Events</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>