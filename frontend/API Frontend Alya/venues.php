<?php
require_once 'header.php';

// Fetch live venues from database using updated column names
$stmt = $pdo->query("SELECT * FROM venues ORDER BY venue_id ASC");
$venues = $stmt->fetchAll();
?>

<div class="page-header-card">
    <div>
        <h2 class="page-title">Venues & Halls Directory</h2>
        <p class="page-subtitle">Overview and management of registered event venues and halls</p>
    </div>
</div>

<div class="catalog-grid">
    <?php foreach ($venues as $v): ?>
    <div class="order-summary-card space-y-3">
        <div class="flex items-center justify-between">
            <span class="venue-status-badge">Active</span>
            <span class="text-xs text-slate-500 font-mono">ID: V-<?= $v['venue_id'] ?></span>
        </div>
        <div>
            <h4 class="font-bold text-slate-900 text-base"><?= htmlspecialchars($v['venue_name']) ?></h4>
            <p class="booking-details mt-1"><i class="fa-solid fa-map-pin mr-1"></i><?= htmlspecialchars($v['address'] . ', ' . $v['city']) ?></p>
        </div>
        <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
            <span class="text-slate-500">Max Capacity:</span>
            <span class="font-bold text-slate-800"><?= number_format($v['capacity']) ?> pax</span>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php require_once 'footer.php'; ?>