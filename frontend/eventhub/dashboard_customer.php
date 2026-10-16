<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Portal - EventHub</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; margin: 0; padding: 20px; }
        .container { max-width: 1100px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; background: white; padding: 15px 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .btn-logout { background: #dc2626; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: bold; }
        
        .nav-tabs { display: flex; gap: 10px; margin-bottom: 20px; }
        .tab-btn { padding: 10px 20px; background: #e5e7eb; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; color: #374151; font-size: 14px; }
        .tab-btn.active { background: #2563eb; color: white; }

        .tab-content { display: none; }
        .tab-content.active { display: block; }

        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .card { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 18px; display: flex; flex-direction: column; justify-content: space-between; }
        .event-banner { width: 100%; height: 140px; object-fit: cover; border-radius: 8px; margin-bottom: 12px; background-color: #e5e7eb; }
        .card h3 { margin: 0 0 8px 0; font-size: 16px; color: #111827; }
        .card p { margin: 4px 0; font-size: 13px; color: #4b5563; }
        
        .venue-info { background: #f9fafb; border-left: 3px solid #2563eb; padding: 8px 10px; border-radius: 4px; margin: 10px 0; font-size: 12px; }
        .venue-info strong { color: #1f2937; }

        .btn-book { width: 100%; background: #2563eb; color: white; border: none; padding: 10px; border-radius: 6px; font-weight: bold; cursor: pointer; margin-top: 10px; font-size: 14px; }
        .btn-book:hover { background-color: #1d4ed8; }

        .table-card { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 12px; border-bottom: 1px solid #e5e7eb; font-size: 13px; vertical-align: middle; }
        th { background-color: #f9fafb; font-weight: 600; }
        .qr-img { width: 75px; height: 75px; border-radius: 6px; border: 1px solid #e5e7eb; display: block; }
        
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 1000; }
        .modal-content { background: white; padding: 25px; border-radius: 10px; width: 320px; text-align: center; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>🎟️ Customer Portal</h2>
        <div>
            Welcome, <strong id="userName">Customer</strong> | 
            <button class="btn-logout" onclick="logout()">Logout</button>
        </div>
    </div>

    <div class="nav-tabs">
        <button class="tab-btn active" onclick="switchTab('events')">🎪 Browse Events & Venues</button>
        <button class="tab-btn" onclick="switchTab('history')">📜 My Booking History</button>
    </div>

    <!-- 1. BROWSE EVENTS TAB -->
    <div id="tab-events" class="tab-content active">
        <div class="grid" id="eventsGrid">Loading events and venue details...</div>
    </div>

    <!-- 2. BOOKING HISTORY TAB -->
    <div id="tab-history" class="tab-content">
        <div class="table-card">
            <h3>Booking History & Scannable QR Tickets</h3>
            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Event Name</th>
                        <th>Date & Venue</th>
                        <th>Qty</th>
                        <th>Total Paid</th>
                        <th>QR Code Ticket</th>
                    </tr>
                </thead>
                <tbody id="bookingsTable">
                    <tr><td colspan="6">Loading booking history...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Booking Modal -->
<div class="modal" id="bookingModal">
    <div class="modal-content">
        <h3 id="modalEventTitle">Book Tickets</h3>
        <p style="font-size: 13px; color: #4b5563;">Price: RM <span id="modalEventPrice">0</span> / ticket</p>
        <div style="margin: 15px 0; text-align: left;">
            <label style="font-size: 12px; font-weight: bold;">Ticket Quantity:</label>
            <input type="number" id="ticketQty" value="1" min="1" max="10" style="width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; border: 1px solid #d1d5db; border-radius: 6px;">
        </div>
        <button class="btn-book" onclick="confirmBooking()">Confirm & Generate Ticket</button>
        <button onclick="closeModal()" style="width: 100%; background: #9ca3af; color: white; border: none; padding: 8px; border-radius: 6px; margin-top: 8px; cursor: pointer;">Cancel</button>
    </div>
</div>

<script>
    const token = localStorage.getItem('token');
    if (!token) window.location.href = 'login.php';
    document.getElementById('userName').innerText = localStorage.getItem('full_name') || 'Customer';

    let selectedEventId = null;

    function switchTab(tab) {
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        
        event.target.classList.add('active');
        document.getElementById('tab-' + tab).classList.add('active');

        if (tab === 'events') loadEvents();
        if (tab === 'history') loadHistory();
    }

    async function loadEvents() {
        const grid = document.getElementById('eventsGrid');
        try {
            const res = await fetch('api_events.php');
            const data = await res.json();
            
            if (data.status === 'success') {
                if (!data.data || data.data.length === 0) {
                    grid.innerHTML = '<p>No upcoming events available.</p>';
                    return;
                }

                grid.innerHTML = data.data.map(e => {
                    const fallbackImg = `https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?auto=format&fit=crop&w=400&q=80`;
                    const imgSrc = e.image_url ? e.image_url : fallbackImg;
                    const venueLocation = e.venue_location ? e.venue_location : 'Location TBA';
                    const venueCap = e.venue_capacity ? ` (Cap: ${e.venue_capacity})` : '';

                    return `
                        <div class="card">
                            <div>
                                <img src="${imgSrc}" class="event-banner" onerror="this.onerror=null; this.src='${fallbackImg}';">
                                <h3>${e.title}</h3>
                                <p><strong>📅 Date:</strong> ${e.event_date}</p>
                                <p><strong>💵 Price:</strong> RM ${parseFloat(e.ticket_price).toFixed(2)}</p>
                                <p><strong>🎟️ Available:</strong> ${e.available_tickets} seats</p>

                                <div class="venue-info">
                                    <strong>📍 Venue Details:</strong><br>
                                    ${e.venue_name || 'Main Venue'}${venueCap}<br>
                                    <small style="color: #6b7280;">${venueLocation}</small>
                                </div>
                            </div>
                            <button class="btn-book" onclick="openModal(${e.event_id}, '${e.title}', ${e.ticket_price})">Book Now</button>
                        </div>
                    `;
                }).join('');
            }
        } catch (err) {
            grid.innerHTML = '<p style="color:red;">Error loading events and venues.</p>';
        }
    }

    async function loadHistory() {
        const tbody = document.getElementById('bookingsTable');
        try {
            const res = await fetch('api_bookings.php', {
                method: 'GET',
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const data = await res.json();

            if (data.status === 'success') {
                if (!data.data || data.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">No previous bookings found.</td></tr>';
                    return;
                }

                tbody.innerHTML = data.data.map(b => `
                    <tr>
                        <td>#BK-${b.booking_id}</td>
                        <td><strong>${b.event_title}</strong></td>
                        <td>${b.event_date}<br><small style="color:#6b7280;">${b.venue_name || 'Main Venue'}</small></td>
                        <td>${b.quantity}</td>
                        <td>RM ${parseFloat(b.total_price).toFixed(2)}</td>
                        <td>
                            <a href="${b.qr_url}" target="_blank" title="Click to view QR Code">
                                <img src="${b.qr_url}" class="qr-img" alt="QR Ticket">
                            </a>
                        </td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = `<tr><td colspan="6" style="color:red; text-align:center;">${data.message}</td></tr>`;
            }
        } catch (err) {
            tbody.innerHTML = '<tr><td colspan="6" style="color:red; text-align:center;">Error loading booking history.</td></tr>';
        }
    }

    function openModal(id, title, price) {
        selectedEventId = id;
        document.getElementById('modalEventTitle').innerText = title;
        document.getElementById('modalEventPrice').innerText = price;
        document.getElementById('bookingModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('bookingModal').style.display = 'none';
    }

    async function confirmBooking() {
        const qty = document.getElementById('ticketQty').value;
        try {
            const res = await fetch('api_bookings.php', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token 
                },
                body: JSON.stringify({ action: 'create_booking', event_id: selectedEventId, quantity: qty })
            });

            const data = await res.json();
            if (data.status === 'success') {
                alert('Ticket Booked Successfully!');
                closeModal();
                switchTab('history');
            } else {
                alert('Booking Failed: ' + data.message);
            }
        } catch (err) {
            alert('Server error processing booking.');
        }
    }

    function logout() {
        localStorage.clear();
        window.location.href = 'login.php';
    }

    loadEvents();
</script>
</body>
</html>