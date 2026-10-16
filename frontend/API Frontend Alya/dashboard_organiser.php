<?php
require_once 'header.php';
?>

<div class="dashboard-container">
    <div class="dashboard-banner">
        <span class="banner-badge">Organiser Control Center</span>
        <h1 class="banner-title">Welcome, <span id="organiser-welcome-name">Organiser</span></h1>
        <p class="banner-subtitle">Manage campus events, schedule venues, monitor ticket sales, and verify attendee QR passes.</p>
        <div class="banner-actions">
            <a href="manage_events.php" class="banner-btn-white">Manage Event Listings</a>
        </div>
    </div>

    <!-- Live Organiser Metrics Grid -->
    <div class="metrics-grid-3">
        <div class="metric-card-flex">
            <div class="metric-icon-box metric-icon-emerald">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
            <div>
                <p class="metric-label">Total Events</p>
                <h3 class="metric-value mt-0.5" id="metric-managed-events">0</h3>
            </div>
        </div>
        <div class="metric-card-flex">
            <div class="metric-icon-box metric-icon-emerald">
                <i class="fa-solid fa-ticket"></i>
            </div>
            <div>
                <p class="metric-label">Tickets Sold</p>
                <h3 class="metric-value mt-0.5" id="metric-tickets-sold">0</h3>
            </div>
        </div>
        <div class="metric-card-flex">
            <div class="metric-icon-box metric-icon-emerald">
                <i class="fa-solid fa-location-dot"></i>
            </div>
            <div>
                <p class="metric-label">Active Venues</p>
                <h3 class="metric-value mt-0.5" id="metric-active-venues">0</h3>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
  const token = localStorage.getItem('auth_token');
  if (!token) { window.location.href = 'login.php'; return; }
  try {
    const response = await fetch('api_dashboard_stats.php', {
      headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token },
      cache: 'no-store'
    });
    const result = await response.json();
    if (!response.ok || result.status !== 'success') throw new Error(result.message || 'Unable to load dashboard statistics');
    const stats = result.data;
    document.getElementById('organiser-welcome-name').textContent = stats.full_name || 'Organiser';
    document.getElementById('metric-managed-events').textContent = stats.total_events;
    document.getElementById('metric-tickets-sold').textContent = stats.tickets_sold;
    document.getElementById('metric-active-venues').textContent = stats.active_venues;
  } catch (error) {
    console.error('Dashboard statistics:', error);
  }
});
</script>

<?php require_once 'footer.php'; ?>