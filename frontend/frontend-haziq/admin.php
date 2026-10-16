<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Event Panel - EventHub</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --neon-lime: #D5FF40;
            --bg-dark: #0B0F17;
            --card-bg: #131822;
            --input-bg: #1A202C;
            --border-color: #202736;
            --text-dim: #94A3B8;
        }

        body {
            background-color: var(--bg-dark) !important;
            color: #FFFFFF !important;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }

        .card-custom {
            background-color: var(--card-bg) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 20px !important;
            color: #FFFFFF !important;
        }

        .admin-metric-card {
            background-color: var(--card-bg) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 16px !important;
            padding: 24px;
            text-align: center;
        }

        .admin-metric-value {
            font-size: 2.2rem;
            font-weight: 800;
            color: #ffffff;
        }

        .admin-metric-label {
            color: var(--text-dim);
            font-size: 0.85rem;
            margin-top: 6px;
            font-weight: 500;
        }

        /* Form Controls */
        .form-control, .form-select {
            background-color: var(--input-bg) !important;
            border: 1px solid var(--border-color) !important;
            color: #FFFFFF !important;
            border-radius: 12px !important;
            padding: 10px 16px;
        }

        .form-control::placeholder {
            color: var(--text-dim) !important;
        }

        .form-control:focus, .form-select:focus {
            background-color: var(--input-bg) !important;
            color: #FFFFFF !important;
            border-color: var(--neon-lime) !important;
            box-shadow: 0 0 0 0.25rem rgba(213, 255, 64, 0.15) !important;
        }

        /* Neon Lime Buttons */
        .btn-lime {
            background-color: var(--neon-lime) !important;
            color: #000000 !important;
            font-weight: 700 !important;
            border: none !important;
            border-radius: 12px !important;
            padding: 10px 24px;
            transition: all 0.25s ease !important;
        }

        .btn-lime:hover {
            background-color: #c4f52c !important;
            color: #000000 !important;
            box-shadow: 0 0 20px rgba(213, 255, 64, 0.4) !important;
            transform: translateY(-2px);
        }

        .btn-outline-dark-custom {
            background-color: rgba(255, 255, 255, 0.05);
            color: #FFFFFF;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            font-size: 0.88rem;
            padding: 8px 18px;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-outline-dark-custom:hover {
            border-color: var(--neon-lime);
            color: var(--neon-lime);
        }

        /* Dark Table */
        .table-dark-custom {
            --bs-table-bg: transparent !important;
            --bs-table-hover-bg: #1A202C !important;
            color: #FFFFFF !important;
        }

        .table-dark-custom th {
            border-bottom: 1px solid var(--border-color) !important;
            color: var(--text-dim) !important;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            padding: 14px 12px;
        }

        .table-dark-custom td {
            border-bottom: 1px solid var(--border-color) !important;
            padding: 16px 12px;
        }

        .progress-dark {
            background-color: var(--input-bg);
            height: 8px;
            border-radius: 4px;
        }

        .progress-bar-lime {
            background-color: var(--neon-lime);
        }

        .text-lime {
            color: var(--neon-lime) !important;
        }
    </style>
</head>
<body class="container my-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1 text-light">Admin Event Panel</h2>
            <p class="text-secondary small mb-0">Monitor performance, inspect booking logs, and manage system events</p>
        </div>
        <div>
            <a href="index.php" class="btn btn-outline-dark-custom"><i class="fa-solid fa-graduation-cap me-2 text-warning"></i>Customer Portal</a>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="admin-metric-card">
                <div id="metricBookings" class="admin-metric-value">0</div>
                <div class="admin-metric-label">Total Confirmed Bookings</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="admin-metric-card">
                <div id="metricRevenue" class="admin-metric-value text-lime">RM 0.00</div>
                <div class="admin-metric-label">Total Revenue Generated</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="admin-metric-card">
                <div id="metricAttendance" class="admin-metric-value">100%</div>
                <div class="admin-metric-label">Checked-in Attendance Rate</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="admin-metric-card">
                <div id="metricWaitlist" class="admin-metric-value">0</div>
                <div class="admin-metric-label">Currently on Waitlists</div>
            </div>
        </div>
    </div>

    <!-- Analytics & Admin Controls Table -->
    <div class="card card-custom p-4 shadow-lg mb-4">
        <h5 class="mb-3 text-lime fw-bold"><i class="fa-solid fa-chart-line me-2"></i>Event Performance Analytics & Controls</h5>
        <div class="table-responsive">
            <table class="table table-dark-custom table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>EVENT NAME</th>
                        <th>SCHEDULED DATES</th>
                        <th>CAPACITY</th>
                        <th>BOOKED</th>
                        <th style="width: 180px;">FILL RATE</th>
                        <th>GROSS SALES</th>
                        <th class="text-end">ADMIN CONTROLS</th>
                    </tr>
                </thead>
                <tbody id="performanceTableBody">
                    <tr><td colspan="7" class="text-secondary text-center py-4">Loading analytics...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Buyer Inspection Tool -->
    <div class="card card-custom p-4 shadow-lg">
        <h5 class="mb-2 text-lime fw-bold"><i class="fa-solid fa-user-check me-2"></i>Inspect Event Attendees & Buyers</h5>
        <div class="input-group mt-3">
            <input type="number" id="inspectEventId" class="form-control" placeholder="Enter Event ID to inspect buyers...">
            <button class="btn btn-lime px-4" onclick="loadBuyers()">Fetch Buyer Details</button>
        </div>
        <div id="buyersResult" class="mt-4"></div>
    </div>

    <script>
        const token = localStorage.getItem('auth_token');
        const role = localStorage.getItem('user_role');
        if(!token || role !== 'admin') window.location.href = 'login.php';

        function loadDashboardAnalytics() {
            fetch('../../backend/api_bookings.php?analytics=true', {
                headers: { 'Authorization': 'Bearer ' + token }
            })
            .then(res => res.json())
            .then(res => {
                if(res.status === 'success') {
                    document.getElementById('metricBookings').innerText = res.summary.total_confirmed_bookings;
                    document.getElementById('metricRevenue').innerText = 'RM ' + parseFloat(res.summary.total_revenue).toFixed(2);
                    document.getElementById('metricAttendance').innerText = res.summary.attendance_rate + '%';
                    document.getElementById('metricWaitlist').innerText = res.summary.waitlist_count;

                    let html = '';
                    if(res.event_performance.length > 0) {
                        res.event_performance.forEach(ev => {
                            html += `
                                <tr>
                                    <td class="fw-bold text-light">${ev.title}</td>
                                    <td class="text-secondary small">${ev.event_date}</td>
                                    <td class="fw-semibold text-light">${ev.total_tickets}</td>
                                    <td class="fw-semibold text-light">${ev.booked_seats}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress progress-dark flex-grow-1">
                                                <div class="progress-bar progress-bar-lime" style="width: ${ev.fill_rate}%"></div>
                                            </div>
                                            <span class="small text-secondary">${ev.fill_rate}%</span>
                                        </div>
                                    </td>
                                    <td class="fw-bold text-lime">RM ${parseFloat(ev.gross_sales).toFixed(2)}</td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteEvent(${ev.event_id}, '${ev.title.replace(/'/g, "\\'")}')">
                                            <i class="fa-solid fa-trash me-1"></i>Delete
                                        </button>
                                    </td>
                                </tr>`;
                        });
                    } else {
                        html = '<tr><td colspan="7" class="text-secondary text-center py-4">No event analytics available.</td></tr>';
                    }
                    document.getElementById('performanceTableBody').innerHTML = html;
                }
            });
        }

        function deleteEvent(eventId, title) {
            if(confirm(`Are you sure you want to permanently delete event #${eventId} ("${title}") and all associated bookings?`)) {
                fetch(`../../backend/api_events.php?event_id=${eventId}`, {
                    method: 'DELETE',
                    headers: { 'Authorization': 'Bearer ' + token }
                })
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') {
                        alert('Event deleted successfully.');
                        loadDashboardAnalytics();
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
            }
        }

        function loadBuyers() {
            let eventId = document.getElementById('inspectEventId').value;
            if(!eventId) return alert('Please enter an Event ID.');

            fetch(`../../backend/api_bookings.php?event_buyers=${eventId}`, {
                headers: { 'Authorization': 'Bearer ' + token }
            })
            .then(res => res.json())
            .then(res => {
                let html = '';
                if(res.status === 'success' && res.data.length > 0) {
                    html = `<table class="table table-dark-custom align-middle">
                        <thead><tr><th>BOOKING ID</th><th>BUYER NAME</th><th>EMAIL</th><th>PHONE</th><th>QTY</th><th>TOTAL (RM)</th><th>PAYMENT METHOD</th></tr></thead><tbody>`;
                    res.data.forEach(b => {
                        html += `<tr>
                            <td class="fw-semibold text-light">#${b.booking_id}</td>
                            <td class="fw-bold text-light">${b.full_name}</td>
                            <td class="text-secondary">${b.email}</td>
                            <td class="text-secondary">${b.phone || '-'}</td>
                            <td class="fw-semibold text-light">${b.quantity}</td>
                            <td class="text-lime fw-bold">RM ${parseFloat(b.total_amount).toFixed(2)}</td>
                            <td><span class="badge bg-success">${b.method}</span></td>
                        </tr>`;
                    });
                    html += `</tbody></table>`;
                } else {
                    html = '<p class="text-secondary mb-0">No buyers found for this Event ID.</p>';
                }
                document.getElementById('buyersResult').innerHTML = html;
            });
        }

        loadDashboardAnalytics();
    </script>
</body>
</html>