<?php
require_once 'header.php';

// Normalize role check (Strictly restrict to admin)
// Verify server-side session against the current database role.
$sessionUserId = (int)($_SESSION['user_id'] ?? 0);
$roleStmt = $pdo->prepare('SELECT role FROM users WHERE user_id = ?');
$roleStmt->execute([$sessionUserId]);
$userRole = strtolower((string)($roleStmt->fetchColumn() ?: ''));
if ($userRole !== 'admin') {
    echo "<div class='auth-error'>Access Denied. Administrator role required.</div>";
    require_once 'footer.php';
    exit;
}

$message = '';
$error = '';

// Handle New Venue Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_venue') {
    $venueName = trim($_POST['venue_name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $capacity = (int)($_POST['capacity'] ?? 0);

    if (empty($venueName) || empty($address) || empty($city) || $capacity <= 0) {
        $error = 'All fields are required, and capacity must be greater than zero.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO venues (venue_name, address, city, capacity) VALUES (?, ?, ?, ?)");
            $stmt->execute([$venueName, $address, $city, $capacity]);
            $message = 'New venue registered successfully!';
        } catch (PDOException $e) {
            $error = 'Failed to register venue (Name and city combination might already exist).';
        }
    }
}

// Fetch live venues from database using updated column names
$stmt = $pdo->query("SELECT * FROM venues ORDER BY venue_id ASC");
$venues = $stmt->fetchAll();
?>

<!-- Link custom stylesheet -->
<link rel="stylesheet" href="assets/css/venues_custom.css">

<div class="page-header-card flex items-center justify-between mb-6">
    <div>
        <h2 class="page-title text-2xl font-bold">Venues & Halls Directory</h2>
        <p class="page-subtitle text-xs text-slate-500 mt-1">Overview and management of registered event venues and halls</p>
    </div>
    <button type="button" onclick="openVenueModal()" class="px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow-sm transition">
        + Register New Venue
    </button>
</div>

<?php if (!empty($message)): ?>
    <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs rounded-xl font-medium"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-600 text-xs rounded-xl font-medium"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="catalog-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach ($venues as $v): ?>
    <div class="venue-card">
        <div class="flex items-center justify-between">
            <span class="venue-status-badge">Active</span>
            <span class="text-xs text-slate-400 font-mono">ID: V-<?= $v['venue_id'] ?></span>
        </div>
        <div>
            <h4 class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($v['venue_name']) ?></h4>
            <p class="booking-details text-xs text-slate-500 mt-1"><i class="fa-solid fa-map-pin mr-1 text-emerald-600"></i><?= htmlspecialchars($v['address'] . ', ' . $v['city']) ?></p>
        </div>
        <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
            <span class="text-slate-400">Max Capacity:</span>
            <span class="font-bold text-slate-800"><?= number_format($v['capacity']) ?> pax</span>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- REGISTER VENUE MODAL -->
<div id="venue-modal" class="venue-modal-overlay hidden">
    <div class="venue-modal-card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-slate-900">Register New Venue</h3>
            <button onclick="closeVenueModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        
        <form method="POST" class="space-y-4">
            <input type="hidden" name="action" value="add_venue">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Venue Name</label>
                <input type="text" name="venue_name" required class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-600" placeholder="e.g. Quill Convention Centre">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Street Address</label>
                <input type="text" name="address" required class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-600" placeholder="e.g. 101 Jalan Sultan Ismail">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">City</label>
                <input type="text" name="city" required class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-600" placeholder="e.g. Kuala Lumpur">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Max Capacity (Pax)</label>
                <input type="number" name="capacity" required min="1" value="500" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-600">
            </div>
            <div class="pt-2 flex space-x-2">
                <button type="button" onclick="closeVenueModal()" class="flex-1 py-2.5 bg-slate-100 text-slate-600 hover:bg-slate-200 font-bold text-xs rounded-xl transition">Cancel</button>
                <button type="submit" class="flex-1 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow-sm transition">Save Venue</button>
            </div>
        </form>
    </div>
</div>

<script>
function openVenueModal() {
    document.getElementById('venue-modal').classList.remove('hidden');
}
function closeVenueModal() {
    document.getElementById('venue-modal').classList.add('hidden');
}
</script>

<?php require_once 'footer.php'; ?>