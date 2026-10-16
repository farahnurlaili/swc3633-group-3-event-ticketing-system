<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');

    if ($_POST['action'] === 'create_event_direct') {
        $title       = trim($_POST['title'] ?? '');
        $venue_id    = intval($_POST['venue_id'] ?? 0);
        $event_date  = trim($_POST['event_date'] ?? '');
        $price       = floatval($_POST['ticket_price'] ?? 0);
        $total_tix   = intval($_POST['total_tickets'] ?? 100);

        if (empty($title) OR $venue_id <= 0 OR empty($event_date)) {
            echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields']);
            exit();
        }

        try {
            if (isset($pdo)) {
                $stmt =$pdo->prepare("INSERT INTO events (title, venue_id, event_date, ticket_price, total_tickets, available_tickets, status) VALUES (:title, :vid, :edate, :price, :tot, :avail, 'published')");
                $stmt->execute([
                    ':title' => $title,
                    ':vid'   => $venue_id,
                    ':edate' => $event_date,
                    ':price' => $price,
                    ':tot'   => $total_tix,
                    ':avail' => $total_tix
                ]);
                $new_id =$pdo->lastInsertId();
            } else if (isset($conn)) {
                $stmt = mysqli_prepare($conn, "INSERT INTO events (title, venue_id, event_date, ticket_price, total_tickets, available_tickets, status) VALUES (?, ?, ?, ?, ?, ?, 'published')");
                mysqli_stmt_bind_param($stmt, "sisdii", $title,$venue_id, $event_date,$price, $total_tix,$total_tix);
                mysqli_stmt_execute($stmt);
                $new_id = mysqli_insert_id($conn);
            }
            echo json_encode(['status' => 'success', 'message' => 'Event successfully created!', 'event_id' => $new_id]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit();
    }

    if ($_POST['action'] === 'delete_event_direct') {
        $event_id = intval($_POST['event_id'] ?? 0);

        if ($event_id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid Event ID']);
            exit();
        }

        try {
            if (isset($pdo)) {
                $stmt =$pdo->prepare("DELETE FROM events WHERE event_id = :id");
                $stmt->execute([':id' =>$event_id]);
            } else if (isset($conn)) {
                $stmt = mysqli_prepare($conn, "DELETE FROM events WHERE event_id = ?");
                mysqli_stmt_bind_param($stmt, "i", $event_id);
                mysqli_stmt_execute($stmt);
            }
            echo json_encode(['status' => 'success', 'message' => 'Event successfully deleted!']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['fetch_organiser_metrics'])) {
    header('Content-Type: application/json');
    try {
        $total_bookings = 0;
        $total_revenue = 0.0;

        if (isset($pdo)) {
            $b_stmt =$pdo->query("SELECT COUNT(*) as cnt, COALESCE(SUM(total_price), 0) as rev FROM bookings WHERE status = 'confirmed'");
            $b_res = $b_stmt->fetch(PDO::FETCH_ASSOC);$total_bookings = intval($b_res['cnt'] ?? 0);$total_revenue = floatval($b_res['rev'] ?? 0); } else if (isset($conn)) {
            $b_res = mysqli_query($conn, "SELECT COUNT(*) as cnt, COALESCE(SUM(total_price), 0) as rev FROM bookings WHERE status = 'confirmed'");
            $b_row = mysqli_fetch_assoc($b_res);
            $total_bookings = intval($b_row['cnt'] ?? 0);
            $total_revenue = floatval($b_row['rev'] ?? 0);
        }

        echo json_encode([
            'status' => 'success',
            'total_bookings' => $total_bookings,
            'total_revenue' => $total_revenue
        ]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit();
}
?>

<div class="dashboard-container">
    <div class="dashboard-banner bg-primary text-white p-4 rounded-3 mb-4 shadow-sm">
        <span class="badge bg-warning text-dark mb-2">Organiser Portal</span>
        <h1 class="banner-title fw-bold">Organiser Analytics Dashboard</h1>
        <p class="banner-subtitle text-light">Manage events, select venues, track ticket sales, and monitor total revenue</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 p-4 text-center">
                <p class="text-muted fw-bold text-uppercase mb-1 small">Total Confirmed Bookings</p>
                <h1 class="display-4 fw-bold text-primary m-0" id="org-total-bookings">0</h1>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0 p-4 text-center">
                <p class="text-muted fw-bold text-uppercase mb-1 small">Total Revenue</p>
                <h1 class="display-4 fw-bold text-success m-0" id="org-total-revenue">RM 0.00</h1>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
            <h5 class="fw-bold m-0"><i class="fa-solid fa-calendar-plus me-2 text-primary"></i>My Managed Events</h5>
            <button class="btn btn-primary btn-sm fw-bold px-3" data-bs-toggle="modal" data-bs-target="#createEventModal">
                <i class="fa-solid fa-plus me-1"></i> Create New Event
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Event Title</th>
                            <th>Venue</th>
                            <th>Event Date</th>
                            <th>Ticket Price</th>
                            <th>Tickets Available</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="orgEventsTableBody">
                        <tr><td colspan="8" class="text-center text-muted py-3">Loading event listings...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createEventModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold"><i class="fa-solid fa-calendar-plus me-2"></i>Create New Event</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <form id="createEventForm" onsubmit="event.preventDefault(); saveOrganiserEvent();">
          <div class="mb-3">
            <label class="form-label fw-semibold">Event Title</label>
            <input type="text" id="evt_title" class="form-control" placeholder="e.g. Kuala Lumpur Live Concert 2026" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Select Venue</label>
            <select id="evt_venue_id" class="form-select" required>
                <option value="">-- Select Venue --</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Event Date</label>
            <input type="date" id="evt_date" class="form-control" required>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Ticket Price (RM)</label>
                <input type="number" step="0.01" id="evt_price" class="form-control" value="50.00" min="0" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Total Tickets</label>
                <input type="number" id="evt_tickets" class="form-control" value="200" min="1" required>
            </div>
          </div>
          <div class="text-end mt-4">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary px-4 fw-bold">Publish Event</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    loadOrganiserDashboard();
});

function loadOrganiserDashboard() {
    fetch('organiser_view.php?fetch_organiser_metrics=1')
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('org-total-bookings').innerText = data.total_bookings || 0;
            document.getElementById('org-total-revenue').innerText = 'RM ' + parseFloat(data.total_revenue || 0).toFixed(2);
        }
    })
    .catch(err => console.error("Metrics load error:", err));

    fetch('admin_view.php?fetch_venues_direct=1')
    .then(res => res.json())
    .then(res => {
        const venues = res.data || [];
        const venueSelect = document.getElementById('evt_venue_id');
        venueSelect.innerHTML = '<option value="">-- Select Venue --</option>';
        venues.forEach(v => {
            venueSelect.innerHTML += `<option value="${v.venue_id}">${v.venue_name || v.name} (${v.capacity || 0} Pax)</option>`;
        });
    })
    .catch(err => console.error("Venues load error:", err));

    fetch('api_events.php', {
        method: 'GET',
        headers: { 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(res => {
        const events = res.data || (Array.isArray(res) ? res : []);
        const tbody = document.getElementById('orgEventsTableBody');

        if (!events || events.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-3">No events found. Click "Create New Event" to get started.</td></tr>';
            return;
        }

        let html = '';
        events.forEach(e => {
            const price = parseFloat(e.ticket_price || 0).toFixed(2);
            const status = e.status || 'published';
            const vName = e.venue_name || ('Venue ID: ' + (e.venue_id || '-'));

            html += `
            <tr>
                <td>#EV-${e.event_id}</td>
                <td><strong>${e.title || 'Untitled'}</strong></td>
                <td><small class="text-muted">${vName}</small></td>
                <td><small>${e.event_date || '-'}</small></td>
                <td class="fw-bold text-primary">RM ${price}</td>
                <td>${e.available_tickets ?? e.total_tickets ?? 0} / ${e.total_tickets ?? 0}</td>
                <td><span class="badge ${status === 'active' || status === 'published' ? 'bg-success' : 'bg-secondary'}">${status}</span></td>
                <td class="text-center">
                    <button onclick="deleteOrganiserEvent(${e.event_id}, '${(e.title || '').replace(/'/g, "\\'")}')" class="btn btn-sm btn-outline-danger fw-bold">
                        <i class="fa-solid fa-trash"></i> Delete
                    </button>
                </td>
            </tr>`;
        });
        tbody.innerHTML = html;
    })
    .catch(err => {
        console.error("Events load error:", err);
        document.getElementById('orgEventsTableBody').innerHTML = '<tr><td colspan="8" class="text-center text-muted py-3">Failed to load events.</td></tr>';
    });
}

function saveOrganiserEvent() {
    const title = document.getElementById('evt_title').value.trim();
    const venueId = document.getElementById('evt_venue_id').value;
    const date = document.getElementById('evt_date').value;
    const price = document.getElementById('evt_price').value;
    const tickets = document.getElementById('evt_tickets').value;

    if (!title || !venueId || !date) {
        alert('Please fill in all required fields!');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'create_event_direct');
    formData.append('title', title);
    formData.append('venue_id', venueId);
    formData.append('event_date', date);
    formData.append('ticket_price', price);
    formData.append('total_tickets', tickets);

    fetch('organiser_view.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Event successfully created and published!');
            document.getElementById('createEventForm').reset();
            const modalElem = document.getElementById('createEventModal');
            if (modalElem && window.bootstrap) {
                const modal = bootstrap.Modal.getInstance(modalElem) || new bootstrap.Modal(modalElem);
                modal.hide();
            }
            loadOrganiserDashboard();
        } else {
            alert('Error creating event: ' + (data.message || 'Database error'));
        }
    })
    .catch(err => {
        console.error('Save event error:', err);
        alert('Error connecting to database.');
    });
}

function deleteOrganiserEvent(eventId, eventTitle) {
    if (!confirm('Are you sure you want to delete event "' + eventTitle + '"?')) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'delete_event_direct');
    formData.append('event_id', eventId);

    fetch('organiser_view.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Event successfully deleted!');
            loadOrganiserDashboard();
        } else {
            alert('Failed to delete event: ' + data.message);
        }
    })
    .catch(err => {
        console.error('Delete event error:', err);
        alert('Error connecting to database.');
    });
}
</script>