<?php
require_once 'header.php';
?>

<div class="dashboard-container">
    <div class="dashboard-banner">
        <span class="banner-badge">Administrator Portal</span>
        <h1 class="banner-title">System Control Center</h1>
        <p class="banner-subtitle">Complete oversight of user accounts, event listings, system metrics, and access permissions.</p>
        <div class="banner-actions">
            <a href="users.php" class="banner-btn-white">Manage Users</a>
            <a href="manage_events.php" class="banner-btn-outline">Manage Events</a>
        </div>
    </div>

    <div class="metrics-grid-4">
        <div class="metric-card">
            <p class="metric-label">Total Users</p>
            <h3 class="metric-value" id="metric-total-users">-</h3>
        </div>
        <div class="metric-card">
            <p class="metric-label">Active Events</p>
            <h3 class="metric-value" id="metric-active-events">-</h3>
        </div>
        <div class="metric-card">
            <p class="metric-label">Registered Venues</p>
            <h3 class="metric-value" id="metric-registered-venues">-</h3>
        </div>
        <div class="metric-card">
            <p class="metric-label">Total Revenue</p>
            <h3 class="metric-value-emerald" id="metric-total-revenue">RM 0.00</h3>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const token = localStorage.getItem('auth_token');

    // Fetch admin analytics and bookings summary via api_bookings.php with ?analytics=1
    fetch('api_bookings.php?analytics=1', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer ' + (token || '')
        }
    })
    .then(response => response.json())
    .then(res => {
        if (res.status === 'success' && res.summary) {

            
            // Populate Total Sales Revenue from summary
            const revenue = parseFloat(res.summary.total_revenue || 0);
            document.getElementById('metric-total-revenue').innerText = 'RM ' + revenue.toFixed(2);
        }
    })
    .catch(err => console.error('Error fetching booking analytics:', err));

    // Fetch active events via api_events.php
    fetch('api_events.php', {
        method: 'GET',
        headers: { 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(res => {
        if (res.status === 'success' && res.data) {
            document.getElementById('metric-active-events').innerText = res.data.length;
        }
    })
    .catch(err => console.error('Error fetching events:', err));

    fetch('api_users.php', {headers: {'Authorization': 'Bearer ' + (token || '')}})
    .then(r => r.json()).then(res => {
        if (res.status === 'success' && Array.isArray(res.data))
            document.getElementById('metric-total-users').innerText = res.data.length;
    }).catch(console.error);

    fetch('api_venues.php', {headers: {'Authorization': 'Bearer ' + (token || '')}})
    .then(r => r.json()).then(res => {
        if (res.status === 'success' && Array.isArray(res.data))
            document.getElementById('metric-registered-venues').innerText = res.data.length;
    }).catch(console.error);
});
</script>

<?php require_once 'footer.php'; ?>