<?php
require_once 'header.php';
?>

<div class="dashboard-container">
    <!-- Welcome Banner -->
    <div class="dashboard-banner">
        <span class="banner-badge">Customer Portal</span>
        <h1 class="banner-title">Welcome back, <span id="user-welcome-name">Customer</span>!</h1>
        <p class="banner-subtitle">Explore upcoming concerts, tech conferences, sports tournaments, and cultural showcases with instant booking</p>
        <div class="banner-actions">
            <a href="events.php" class="banner-btn-white flex items-center space-x-2">
                <i class="fa-solid fa-magnifying-glass"></i>
                <span>Browse Events</span>
            </a>
            <a href="bookings.php" class="banner-btn-outline flex items-center space-x-2">
                <i class="fa-solid fa-ticket-simple"></i>
                <span>My Bookings</span>
            </a>
        </div>
    </div>

    <!-- Live Customer Metrics Grid -->
    <div class="metrics-grid-3">
        <div class="metric-card-flex">
            <div class="metric-icon-box metric-icon-emerald">
                <i class="fa-solid fa-ticket"></i>
            </div>
            <div>
                <p class="metric-label">Active Bookings</p>
                <h3 class="metric-value mt-0.5" id="metric-active-bookings">0</h3>
            </div>
        </div>
        <div class="metric-card-flex">
            <div class="metric-icon-box metric-icon-blue">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div>
                <p class="metric-label">Total Events</p>
                <h3 class="metric-value mt-0.5" id="metric-upcoming-events">0</h3>
            </div>
        </div>
        <div class="metric-card-flex">
            <div class="metric-icon-box metric-icon-purple">
                <i class="fa-solid fa-qrcode"></i>
            </div>
            <div>
                <p class="metric-label">QR Passes Ready</p>
                <h3 class="metric-value mt-0.5" id="metric-qr-passes">0</h3>
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
    document.getElementById('user-welcome-name').textContent = stats.full_name || 'Customer';
    document.getElementById('metric-active-bookings').textContent = stats.active_bookings;
    document.getElementById('metric-upcoming-events').textContent = stats.total_events;
    document.getElementById('metric-qr-passes').textContent = stats.qr_passes;
  } catch (error) {
    console.error('Dashboard statistics:', error);
    if (error.message === 'Login required') { localStorage.removeItem('auth_token'); window.location.href = 'login.php'; }
  }
});
</script>

<?php require_once 'footer.php'; ?>