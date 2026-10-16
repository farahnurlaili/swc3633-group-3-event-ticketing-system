<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - EventHub</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; margin: 0; padding: 20px; }
        .container { max-width: 1100px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; background: white; padding: 15px 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .btn-logout { background: #dc2626; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: bold; }
        
        .nav-tabs { display: flex; gap: 10px; margin-bottom: 20px; }
        .tab-btn { padding: 10px 20px; background: #e5e7eb; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; color: #374151; font-size: 14px; }
        .tab-btn.active { background: #2563eb; color: white; }

        .tab-content { display: none; }
        .tab-content.active { display: block; }

        .table-card { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 12px; border-bottom: 1px solid #e5e7eb; font-size: 13px; vertical-align: middle; }
        th { background-color: #f9fafb; font-weight: 600; }
        
        .btn-action { padding: 5px 10px; border-radius: 4px; border: none; font-weight: bold; cursor: pointer; font-size: 12px; }
        .btn-edit { background: #f59e0b; color: white; }
        .btn-delete { background: #ef4444; color: white; }
        .btn-add { background: #2563eb; color: white; padding: 8px 15px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; margin-bottom: 15px; }

        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 1000; }
        .modal-content { background: white; padding: 25px; border-radius: 10px; width: 380px; }
        .form-group { margin-bottom: 12px; }
        .form-group label { display: block; font-size: 12px; font-weight: bold; margin-bottom: 4px; }
        .form-group input, .form-group select { width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>⚡ Admin Control Panel</h2>
        <div>
            Welcome, <strong id="adminName">Admin</strong> | 
            <button class="btn-logout" onclick="logout()">Logout</button>
        </div>
    </div>

    <div class="nav-tabs">
        <button class="tab-btn active" onclick="switchTab('users')">👥 User Accounts</button>
        <button class="tab-btn" onclick="switchTab('events')">🎟️ Manage Events</button>
        <button class="tab-btn" onclick="switchTab('venues')">📍 Venues</button>
        <button class="tab-btn" onclick="switchTab('bookings')">📖 All Bookings</button>
    </div>

    <!-- 1. USERS TAB -->
    <div id="tab-users" class="tab-content active">
        <div class="table-card">
            <h3>Registered Accounts</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="usersTable"><tr><td colspan="5">Loading users...</td></tr></tbody>
            </table>
        </div>
    </div>

    <!-- 2. EVENTS TAB -->
    <div id="tab-events" class="tab-content">
        <div class="table-card">
            <h3>All System Events</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Price</th>
                        <th>Seats</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="eventsTable"><tr><td colspan="6">Loading events...</td></tr></tbody>
            </table>
        </div>
    </div>

    <!-- 3. VENUES TAB -->
    <div id="tab-venues" class="tab-content">
        <button class="btn-add" onclick="openVenueModal()">+ Add New Venue</button>
        <div class="table-card">
            <h3>System Venues</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Venue Name</th>
                        <th>Location</th>
                        <th>Capacity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="venuesTable"><tr><td colspan="5">Loading venues...</td></tr></tbody>
            </table>
        </div>
    </div>

    <!-- 4. BOOKINGS TAB -->
    <div id="tab-bookings" class="tab-content">
        <div class="table-card">
            <h3>Global Customer Bookings</h3>
            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Customer</th>
                        <th>Event</th>
                        <th>Date</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody id="bookingsTable"><tr><td colspan="6">Loading bookings...</td></tr></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Venue Modal -->
<div class="modal" id="venueModal">
    <div class="modal-content">
        <h3 id="venueModalTitle">Add Venue</h3>
        <input type="hidden" id="venueId">
        <div class="form-group">
            <label>Venue Name</label>
            <input type="text" id="venueName">
        </div>
        <div class="form-group">
            <label>Location</label>
            <input type="text" id="venueLocation">
        </div>
        <div class="form-group">
            <label>Capacity</label>
            <input type="number" id="venueCapacity">
        </div>
        <button class="btn-add" style="width: 100%;" onclick="saveVenue()">Save Venue</button>
        <button onclick="closeModal('venueModal')" style="width: 100%; background: #9ca3af; color: white; border: none; padding: 8px; border-radius: 6px; cursor: pointer;">Cancel</button>
    </div>
</div>

<script>
    const token = localStorage.getItem('token');
    const role = localStorage.getItem('role');

    if (!token || role !== 'admin') {
        window.location.href = 'login.php';
    }

    document.getElementById('adminName').innerText = localStorage.getItem('full_name') || 'Admin';

    function switchTab(tab) {
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        
        event.target.classList.add('active');
        document.getElementById('tab-' + tab).classList.add('active');

        if (tab === 'users') loadUsers();
        if (tab === 'events') loadEvents();
        if (tab === 'venues') loadVenues();
        if (tab === 'bookings') loadBookings();
    }

    // USERS
    async function loadUsers() {
        const res = await fetch('api_admin.php?action=get_users', { headers: { 'Authorization': 'Bearer ' + token } });
        const data = await res.json();
        if (data.status === 'success') {
            document.getElementById('usersTable').innerHTML = data.data.map(u => `
                <tr>
                    <td>#${u.user_id}</td>
                    <td><strong>${u.full_name}</strong></td>
                    <td>${u.email}</td>
                    <td>
                        <select onchange="updateRole(${u.user_id}, this.value)">
                            <option value="customer" ${u.role === 'customer' ? 'selected' : ''}>Customer</option>
                            <option value="organiser" ${u.role === 'organiser' ? 'selected' : ''}>Organiser</option>
                            <option value="admin" ${u.role === 'admin' ? 'selected' : ''}>Admin</option>
                        </select>
                    </td>
                    <td><button class="btn-action btn-delete" onclick="deleteItem('user', ${u.user_id})">Delete</button></td>
                </tr>
            `).join('');
        }
    }

    async function updateRole(userId, newRole) {
        await fetch('api_admin.php?action=update_user_role', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
            body: JSON.stringify({ user_id: userId, role: newRole })
        });
        alert('User role updated!');
    }

    // EVENTS
    async function loadEvents() {
        const res = await fetch('api_events.php');
        const data = await res.json();
        if (data.status === 'success') {
            document.getElementById('eventsTable').innerHTML = data.data.map(e => `
                <tr>
                    <td>#${e.event_id}</td>
                    <td><strong>${e.title}</strong></td>
                    <td>${e.event_date}</td>
                    <td>RM ${parseFloat(e.ticket_price || 0).toFixed(2)}</td>
                    <td>${e.available_tickets}</td>
                    <td><button class="btn-action btn-delete" onclick="deleteEvent(${e.event_id})">Delete</button></td>
                </tr>
            `).join('');
        }
    }

    async function deleteEvent(id) {
        if (!confirm("Delete this event?")) return;
        await fetch(`api_events.php?event_id=${id}&action=delete`, { method: 'DELETE', headers: { 'Authorization': 'Bearer ' + token } });
        loadEvents();
    }

    // VENUES
    async function loadVenues() {
        const res = await fetch('api_admin.php?action=get_venues', { headers: { 'Authorization': 'Bearer ' + token } });
        const data = await res.json();
        if (data.status === 'success') {
            document.getElementById('venuesTable').innerHTML = data.data.map(v => `
                <tr>
                    <td>#${v.venue_id}</td>
                    <td><strong>${v.venue_name}</strong></td>
                    <td>${v.location || 'N/A'}</td>
                    <td>${v.capacity || 'N/A'}</td>
                    <td><button class="btn-action btn-delete" onclick="deleteItem('venue', ${v.venue_id})">Delete</button></td>
                </tr>
            `).join('');
        }
    }

    function openVenueModal() {
        document.getElementById('venueId').value = '';
        document.getElementById('venueName').value = '';
        document.getElementById('venueLocation').value = '';
        document.getElementById('venueCapacity').value = '';
        document.getElementById('venueModal').style.display = 'flex';
    }

    async function saveVenue() {
        const payload = {
            action: 'save_venue',
            venue_id: document.getElementById('venueId').value,
            venue_name: document.getElementById('venueName').value,
            location: document.getElementById('venueLocation').value,
            capacity: document.getElementById('venueCapacity').value
        };
        await fetch('api_admin.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
            body: JSON.stringify(payload)
        });
        closeModal('venueModal');
        loadVenues();
    }

    // BOOKINGS
    async function loadBookings() {
        const res = await fetch('api_admin.php?action=get_bookings', { headers: { 'Authorization': 'Bearer ' + token } });
        const data = await res.json();
        if (data.status === 'success') {
            document.getElementById('bookingsTable').innerHTML = data.data.map(b => `
                <tr>
                    <td>#BK-${b.booking_id}</td>
                    <td>${b.customer_name}<br><small>${b.customer_email}</small></td>
                    <td><strong>${b.event_title}</strong></td>
                    <td>${b.event_date}</td>
                    <td>${b.quantity}</td>
                    <td>RM ${parseFloat(b.total_price).toFixed(2)}</td>
                </tr>
            `).join('');
        }
    }

    async function deleteItem(type, id) {
        if (!confirm(`Are you sure you want to delete this ${type}?`)) return;
        const res = await fetch(`api_admin.php?action=delete&type=${type}&id=${id}`, {
            method: 'DELETE',
            headers: { 'Authorization': 'Bearer ' + token }
        });
        const data = await res.json();
        if (data.status === 'success') {
            if (type === 'user') loadUsers();
            if (type === 'venue') loadVenues();
        } else {
            alert(data.message);
        }
    }

    function closeModal(id) { document.getElementById(id).style.display = 'none'; }
    function logout() { localStorage.clear(); window.location.href = 'login.php'; }

    loadUsers();
</script>
</body>
</html>