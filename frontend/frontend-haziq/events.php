<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organiser Event Hub - EventHub</title>
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

        /* Navbar */
        .navbar-custom {
            background-color: rgba(11, 15, 23, 0.85);
            border-bottom: 1px solid var(--border-color);
            backdrop-filter: blur(12px);
        }

        .brand-text {
            color: #FFFFFF;
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: -0.5px;
        }

        .brand-text span {
            color: var(--neon-lime);
        }

        /* Dark Card Container */
        .card-custom {
            background-color: var(--card-bg) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 20px !important;
            color: #FFFFFF !important;
        }

        /* Form Controls */
        .form-label {
            color: var(--text-dim) !important;
            font-size: 0.88rem;
            font-weight: 500;
        }

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
            padding: 12px 28px;
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

        .text-lime {
            color: var(--neon-lime) !important;
        }
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom px-4 mb-5">
        <a class="navbar-brand brand-text" href="index.php">
            <i class="fa-solid fa-bolt me-2 text-warning"></i>EventHub<span>.</span>
        </a>
        <div class="ms-auto d-flex align-items-center">
            <a href="index.php" class="btn btn-outline-dark-custom"><i class="fa-solid fa-arrow-left me-2"></i>Back to Portal</a>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1 text-light">Organiser Event Hub</h2>
                <p class="text-secondary small mb-0">Create, schedule, and edit assigned event details</p>
            </div>
        </div>

        <!-- Publish / Edit Event Form Card -->
        <div class="card card-custom p-4 shadow-lg mb-5">
            <h4 id="formHeading" class="text-lime fw-bold mb-4"><i class="fa-solid fa-calendar-plus me-2"></i>Publish New Event</h4>
            <input type="hidden" id="editEventId" value="">
            
            <div class="mb-3">
                <label class="form-label">Event Title</label>
                <input type="text" id="title" class="form-control" placeholder="e.g. ASEAN Youth Tech & Music Fest 2026">
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea id="desc" class="form-control" rows="3" placeholder="Enter event details, performers, or schedule overview..."></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Event Image Path</label>
                <input type="text" id="imageUrl" class="form-control" value="images/rockfest.jpg">
                <small class="text-secondary">Tip: Place your images inside your project's `images/` folder and type their path here (e.g., `images/rockfest.jpg`).</small>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Venue ID</label>
                    <input type="number" id="venue" class="form-control" value="1">
                    <small class="text-secondary">1: KLCC, 2: Axiata Arena, 3: Stadium Bestari</small>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Category ID</label>
                    <input type="number" id="cat" class="form-control" value="1">
                    <small class="text-secondary">1: Music, 2: Sports, 3: Seminar</small>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Status</label>
                    <select id="status" class="form-select">
                        <option value="active">Active (Published)</option>
                        <option value="ended">Ended / Draft</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3"><label class="form-label">Date</label><input type="date" id="date" class="form-control" value="2026-12-31"></div>
                <div class="col-md-4 mb-3"><label class="form-label">Start Time</label><input type="time" id="start" class="form-control" value="19:00"></div>
                <div class="col-md-4 mb-3"><label class="form-label">End Time</label><input type="time" id="end" class="form-control" value="23:00"></div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Ticket Price (RM)</label><input type="number" step="0.01" id="price" class="form-control" value="50.00"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Total Tickets</label><input type="number" id="tickets" class="form-control" value="100"></div>
            </div>

            <div class="d-flex gap-2 mt-2">
                <button id="saveBtn" class="btn btn-lime px-4" onclick="saveEvent()">Publish Event</button>
                <button id="cancelBtn" class="btn btn-outline-secondary d-none px-4" onclick="resetForm()">Cancel Edit</button>
            </div>
        </div>

        <!-- Assigned Events List Table -->
        <div class="card card-custom p-4 shadow-lg">
            <h4 class="text-lime fw-bold mb-4"><i class="fa-solid fa-list-check me-2"></i>Assigned Events List</h4>
            <div class="table-responsive">
                <table class="table table-dark-custom table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>TITLE</th>
                            <th>DATE</th>
                            <th>PRICE</th>
                            <th>STATUS</th>
                            <th class="text-end">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody id="eventsList">
                        <tr><td colspan="6" class="text-secondary text-center py-4">Loading assigned events...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const token = localStorage.getItem('auth_token');
        const role = localStorage.getItem('user_role');
        if(!token || (role !== 'organiser' && role !== 'admin')) window.location.href = 'login.php';

        function loadEvents() {
            fetch('../../backend/api_events.php')
                .then(res => res.json())
                .then(res => {
                    let html = '';
                    if(res.status === 'success' && res.data.length > 0) {
                        res.data.forEach(ev => {
                            let evJson = JSON.stringify(ev).replace(/'/g, "&apos;");
                            html += `
                                <tr>
                                    <td>#${ev.event_id}</td>
                                    <td class="fw-bold text-light">${ev.title}</td>
                                    <td class="text-secondary small">${ev.event_date}</td>
                                    <td class="fw-bold text-lime">RM ${parseFloat(ev.ticket_price).toFixed(2)}</td>
                                    <td><span class="badge ${ev.status === 'active' ? 'bg-success' : 'bg-secondary'}">${ev.status}</span></td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-info" onclick='populateEdit(${evJson})'>
                                            <i class="fa-solid fa-pen-to-square me-1"></i>Edit
                                        </button>
                                    </td>
                                </tr>`;
                        });
                    } else {
                        html = '<tr><td colspan="6" class="text-secondary text-center py-4">No events found.</td></tr>';
                    }
                    document.getElementById('eventsList').innerHTML = html;
                });
        }

        function populateEdit(ev) {
            document.getElementById('editEventId').value = ev.event_id;
            document.getElementById('title').value = ev.title;
            document.getElementById('desc').value = ev.description || '';
            document.getElementById('imageUrl').value = ev.image_url || 'images/rockfest.jpg';
            document.getElementById('venue').value = ev.venue_id;
            document.getElementById('cat').value = ev.category_id;
            document.getElementById('status').value = ev.status;
            document.getElementById('date').value = ev.event_date;
            document.getElementById('start').value = ev.start_time.substring(0, 5);
            document.getElementById('end').value = ev.end_time.substring(0, 5);
            document.getElementById('price').value = ev.ticket_price;
            document.getElementById('tickets').value = ev.total_tickets;

            document.getElementById('formHeading').innerHTML = `<i class="fa-solid fa-pen-to-square me-2"></i>Edit Event (#${ev.event_id})`;
            document.getElementById('saveBtn').innerText = 'Update Event';
            document.getElementById('cancelBtn').classList.remove('d-none');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function resetForm() {
            document.getElementById('editEventId').value = '';
            document.getElementById('formHeading').innerHTML = `<i class="fa-solid fa-calendar-plus me-2"></i>Publish New Event`;
            document.getElementById('saveBtn').innerText = 'Publish Event';
            document.getElementById('cancelBtn').classList.add('d-none');
            document.getElementById('title').value = '';
            document.getElementById('desc').value = '';
            document.getElementById('imageUrl').value = 'images/rockfest.jpg';
        }

        function saveEvent() {
            let editId = document.getElementById('editEventId').value;
            let method = editId ? 'PUT' : 'POST';
            
            let payload = {
                title: document.getElementById('title').value,
                description: document.getElementById('desc').value,
                image_url: document.getElementById('imageUrl').value,
                venue_id: parseInt(document.getElementById('venue').value),
                category_id: parseInt(document.getElementById('cat').value),
                status: document.getElementById('status').value,
                event_date: document.getElementById('date').value,
                start_time: document.getElementById('start').value + ':00',
                end_time: document.getElementById('end').value + ':00',
                ticket_price: parseFloat(document.getElementById('price').value),
                total_tickets: parseInt(document.getElementById('tickets').value)
            };

            if(editId) payload.event_id = parseInt(editId);

            fetch('../../backend/api_events.php', {
                method: method,
                headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    alert(editId ? 'Event Updated Successfully!' : 'Event Published Successfully!');
                    resetForm();
                    loadEvents();
                } else { alert('Error: ' + data.message); }
            });
        }

        loadEvents();
    </script>
</body>
</html>