<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organiser Dashboard - EventHub</title>
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
        
        .btn-edit { background-color: #f59e0b; color: white; border: none; padding: 6px 12px; border-radius: 4px; font-weight: bold; cursor: pointer; }
        .btn-edit:hover { background-color: #d97706; }
        .btn-create { background-color: #2563eb; color: white; border: none; padding: 10px 18px; border-radius: 6px; font-weight: bold; cursor: pointer; margin-bottom: 15px; }

        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 1000; }
        .modal-content { background: white; padding: 25px; border-radius: 10px; width: 400px; text-align: left; }
        .form-group { margin-bottom: 12px; }
        .form-group label { display: block; font-size: 12px; font-weight: bold; color: #374151; margin-bottom: 4px; }
        .form-group input, .form-group select { width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box; font-size: 13px; }
        .btn-save { width: 100%; background-color: #2563eb; color: white; border: none; padding: 10px; border-radius: 6px; font-weight: bold; cursor: pointer; margin-top: 10px; }
        .btn-cancel { width: 100%; background-color: #9ca3af; color: white; border: none; padding: 8px; border-radius: 6px; margin-top: 6px; cursor: pointer; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>🎪 Organiser Control Panel</h2>
        <div>
            Welcome, <strong id="userName">Organiser</strong> | 
            <button class="btn-logout" onclick="logout()">Logout</button>
        </div>
    </div>

    <div class="nav-tabs">
        <button class="tab-btn active" onclick="switchTab('events')">🎟️ Manage Events</button>
        <button class="tab-btn" onclick="switchTab('bookings')">📖 Customer Bookings</button>
    </div>

    <!-- 1. EVENTS TAB -->
    <div id="tab-events" class="tab-content active">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h3>Your Events</h3>
            <button class="btn-create" onclick="openCreateModal()">+ Create New Event</button>
        </div>
        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>Event ID</th>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Venue</th>
                        <th>Price</th>
                        <th>Available Seats</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="eventsTable">
                    <tr><td colspan="7">Loading events...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 2. BOOKINGS TAB -->
    <div id="tab-bookings" class="tab-content">
        <h3>Customer Event Bookings</h3>
        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Customer</th>
                        <th>Event Title</th>
                        <th>Event Date</th>
                        <th>Qty</th>
                        <th>Total Paid</th>
                    </tr>
                </thead>
                <tbody id="bookingsTable">
                    <tr><td colspan="6">Loading bookings...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit / Create Event Modal -->
<div class="modal" id="eventModal">
    <div class="modal-content">
        <h3 id="modalTitle">Edit Event Details</h3>
        <input type="hidden" id="editEventId">
        
        <div class="form-group">
            <label>Event Title</label>
            <input type="text" id="eventTitle" required>
        </div>
        <div class="form-group">
            <label>Event Date</label>
            <input type="date" id="eventDate" required>
        </div>
        <div class="form-group">
            <label>Select Venue</label>
            <select id="eventVenue">
                <option value="1">Main Hall</option>
            </select>
        </div>
        <div class="form-group">
            <label>Ticket Price (RM)</label>
            <input type="number" step="0.01" id="eventPrice" required>
        </div>
        <div class="form-group">
            <label>Available Seats</label>
            <input type="number" id="eventSeats" required>
        </div>

        <button class="btn-save" onclick="saveEvent()">Save Changes</button>
        <button class="btn-cancel" onclick="closeModal()">Cancel</button>
    </div>
</div>

<script>
    const token = localStorage.getItem('token');
    if (!token) window.location.href = 'login.php';
    document.getElementById('userName').innerText = localStorage.getItem('full_name') || 'Organiser';

    let allEvents = [];
    let allVenues = [];

    function switchTab(tab) {
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        
        event.target.classList.add('active');
        document.getElementById('tab-' + tab).classList.add('active');

        if (tab === 'events') loadEvents();
        if (tab === 'bookings') loadBookings();
    }

    async function loadVenues() {
        try {
            const res = await fetch('api_organiser.php?action=get_venues', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const data = await res.json();
            if (data.status === 'success' && data.data.length > 0) {
                allVenues = data.data;
                const select = document.getElementById('eventVenue');
                select.innerHTML = allVenues.map(v => `<option value="${v.venue_id}">${v.venue_name} (Cap: ${v.capacity || 'N/A'})</option>`).join('');
            }
        } catch (e) {
            console.warn('Could not load venues list:', e);
        }
    }

    async function loadEvents() {
        const tbody = document.getElementById('eventsTable');
        try {
            const res = await fetch('api_events.php');
            const data = await res.json();
            
            if (data.status === 'success') {
                allEvents = data.data || [];
                
                if (allEvents.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;">No events available.</td></tr>';
                    return;
                }

                tbody.innerHTML = allEvents.map(e => `
                    <tr>
                        <td>#${e.event_id}</td>
                        <td><strong>${e.title}</strong></td>
                        <td>${e.event_date}</td>
                        <td>${e.venue_name || 'Venue #' + (e.venue_id || '1')}</td>
                        <td>RM ${parseFloat(e.ticket_price || 0).toFixed(2)}</td>
                        <td>${e.available_tickets}</td>
                        <td>
                            <button class="btn-edit" onclick="openEditModal(${e.event_id})">Edit Details</button>
                        </td>
                    </tr>
                `).join('');
            }
        } catch (e) {
            tbody.innerHTML = '<tr><td colspan="7" style="color:red; text-align:center;">Error connecting to API.</td></tr>';
        }
    }

    async function loadBookings() {
        const tbody = document.getElementById('bookingsTable');
        try {
            const res = await fetch('api_organiser.php?action=get_organiser_bookings', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const data = await res.json();

            if (data.status === 'success') {
                if (!data.data || data.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">No customer bookings found.</td></tr>';
                    return;
                }

                tbody.innerHTML = data.data.map(b => `
                    <tr>
                        <td>#BK-${b.booking_id}</td>
                        <td>${b.customer_name}<br><small style="color:#6b7280;">${b.customer_email}</small></td>
                        <td><strong>${b.event_title}</strong></td>
                        <td>${b.event_date}</td>
                        <td>${b.quantity}</td>
                        <td>RM ${parseFloat(b.total_price).toFixed(2)}</td>
                    </tr>
                `).join('');
            }
        } catch (e) {
            tbody.innerHTML = '<tr><td colspan="6" style="color:red; text-align:center;">Error loading bookings.</td></tr>';
        }
    }

    function openEditModal(id) {
        const ev = allEvents.find(item => item.event_id == id);
        if (!ev) return;

        document.getElementById('modalTitle').innerText = 'Edit Event #' + id;
        document.getElementById('editEventId').value = ev.event_id;
        document.getElementById('eventTitle').value = ev.title;
        document.getElementById('eventDate').value = ev.event_date;
        document.getElementById('eventPrice').value = ev.ticket_price;
        document.getElementById('eventSeats').value = ev.available_tickets;

        if (document.getElementById('eventVenue').querySelector(`option[value="${ev.venue_id}"]`)) {
            document.getElementById('eventVenue').value = ev.venue_id;
        }

        document.getElementById('eventModal').style.display = 'flex';
    }

    function openCreateModal() {
        document.getElementById('modalTitle').innerText = 'Create New Event';
        document.getElementById('editEventId').value = '';
        document.getElementById('eventTitle').value = '';
        document.getElementById('eventDate').value = '';
        document.getElementById('eventPrice').value = '';
        document.getElementById('eventSeats').value = '';

        document.getElementById('eventModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('eventModal').style.display = 'none';
    }

    async function saveEvent() {
        const id = document.getElementById('editEventId').value;
        const actionType = id ? 'update_event' : 'create_event';

        const payload = {
            action: actionType,
            event_id: id,
            title: document.getElementById('eventTitle').value,
            event_date: document.getElementById('eventDate').value,
            venue_id: document.getElementById('eventVenue').value,
            ticket_price: document.getElementById('eventPrice').value,
            available_tickets: document.getElementById('eventSeats').value
        };

        try {
            const res = await fetch('api_events.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();
            if (data.status === 'success') {
                alert(data.message);
                closeModal();
                loadEvents();
            } else {
                alert('Save failed: ' + data.message);
            }
        } catch (e) {
            alert('Server error saving event.');
        }
    }

    function logout() {
        localStorage.clear();
        window.location.href = 'login.php';
    }

    loadVenues();
    loadEvents();
</script>
</body>
</html>