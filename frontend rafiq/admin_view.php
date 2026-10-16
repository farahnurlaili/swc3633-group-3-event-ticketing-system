<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');

    if ($_POST['action'] === 'save_venue_direct') {
        $venue_name = trim($_POST['venue_name'] ?? '');
        $address    = trim($_POST['address'] ?? '');
        $capacity   = intval($_POST['capacity'] ?? 100);

        if (empty($venue_name)) {
            echo json_encode(['status' => 'error', 'message' => 'Venue name is required']);
            exit();
        }

        try {
            if (isset($pdo)) {
                $stmt =$pdo->prepare("INSERT INTO venues (venue_name, address, capacity) VALUES (:vname, :addr, :cap)");
                $stmt->execute([':vname' =>$venue_name, ':addr' => $address, ':cap' =>$capacity]);
                $new_id =$pdo->lastInsertId();
            } else if (isset($conn)) {
                $stmt = mysqli_prepare($conn, "INSERT INTO venues (venue_name, address, capacity) VALUES (?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "ssi", $venue_name, $address,$capacity);
                mysqli_stmt_execute($stmt);
                $new_id = mysqli_insert_id($conn);
            }
            echo json_encode(['status' => 'success', 'message' => 'Venue saved successfully!', 'venue_id' => $new_id]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit();
    }

    if ($_POST['action'] === 'delete_venue_direct') {
        $venue_id = intval($_POST['venue_id'] ?? 0);

        if ($venue_id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid Venue ID']);
            exit();
        }

        try {
            if (isset($pdo)) {
                $stmt =$pdo->prepare("DELETE FROM venues WHERE venue_id = :id");
                $stmt->execute([':id' =>$venue_id]);
            } else if (isset($conn)) {
                $stmt = mysqli_prepare($conn, "DELETE FROM venues WHERE venue_id = ?");
                mysqli_stmt_bind_param($stmt, "i", $venue_id);
                mysqli_stmt_execute($stmt);
            }
            echo json_encode(['status' => 'success', 'message' => 'Venue deleted successfully!']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['fetch_venues_direct'])) {
    header('Content-Type: application/json');
    try {
        if (isset($pdo)) {
            $stmt =$pdo->query("SELECT * FROM venues ORDER BY venue_id DESC");
            $venues =$stmt->fetchAll(PDO::FETCH_ASSOC);
        } else if (isset($conn)) {
            $res = mysqli_query($conn, "SELECT * FROM venues ORDER BY venue_id DESC");
            $venues = mysqli_fetch_all($res, MYSQLI_ASSOC);
        }
        echo json_encode(['status' => 'success', 'data' => $venues]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit();
}
?>

<div class="dashboard-container">
    <div class="dashboard-banner bg-dark text-white p-4 rounded-3 mb-4 shadow-sm">
        <span class="badge bg-warning text-dark mb-2">Admin Portal</span>
        <h1 class="banner-title fw-bold">Admin Dashboard</h1>
        <p class="banner-subtitle text-light">Manage venues, events, users, and overall ticketing platform metrics</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-circle">
                        <i class="fa-solid fa-calendar-days fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted">Total Events</small>
                        <h3 class="fw-bold m-0" id="metric-total-events">0</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-success bg-opacity-10 text-success rounded-circle">
                        <i class="fa-solid fa-location-dot fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted">Total Venues</small>
                        <h3 class="fw-bold m-0" id="metric-total-venues">0</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-info bg-opacity-10 text-info rounded-circle">
                        <i class="fa-solid fa-ticket fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted">Total Bookings</small>
                        <h3 class="fw-bold m-0" id="metric-total-bookings">0</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-circle">
                        <i class="fa-solid fa-users fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted">Active Users</small>
                        <h3 class="fw-bold m-0" id="metric-total-users">0</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
            <h5 class="fw-bold m-0"><i class="fa-solid fa-building me-2 text-primary"></i>Venue Management</h5>
            <button class="btn btn-primary btn-sm fw-bold px-3" data-bs-toggle="modal" data-bs-target="#addVenueModal">
                <i class="fa-solid fa-plus me-1"></i> Add New Venue
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Venue Name</th>
                            <th>Address / Location</th>
                            <th>Capacity</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="venuesTableBody">
                        <tr><td colspan="5" class="text-center text-muted py-3">Loading venues...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="fw-bold m-0"><i class="fa-solid fa-calendar-check me-2 text-primary"></i>All Events List</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Venue</th>
                            <th>Date</th>
                            <th>Price</th>
                            <th>Tickets</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="eventsAdminTableBody">
                        <tr><td colspan="7" class="text-center text-muted py-3">Loading events...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addVenueModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold"><i class="fa-solid fa-location-dot me-2"></i>Add New Venue</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <form id="addVenueForm" onsubmit="event.preventDefault(); saveVenue();">
          <div class="mb-3">
            <label class="form-label fw-semibold">Venue Name</label>
            <input type="text" id="venue_name" class="form-control" placeholder="e.g. Grand Hall Center" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Address / Location</label>
            <textarea id="venue_address" class="form-control" rows="2" placeholder="e.g. 123 Main Street, City Center" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Capacity</label>
            <input type="number" id="venue_capacity" class="form-control" value="500" min="1" required>
          </div>
          <div class="text-end mt-4">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary px-4 fw-bold">Save Venue</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const token = localStorage.getItem('auth_token') || '';
    loadAdminVenues();
    loadAdminEvents(token);
});

function loadAdminVenues() {
    fetch('admin_view.php?fetch_venues_direct=1')
    .then(res => res.json())
    .then(res => {
        const venues = res.data || [];
        const tbody = document.getElementById('venuesTableBody');
        document.getElementById('metric-total-venues').innerText = venues.length;

        if (!venues || venues.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">No venues found.</td></tr>';
            return;
        }

        let html = '';
        venues.forEach(v => {
            const vId = v.venue_id;
            const name = v.venue_name || v.name || '-';
            const addr = v.address || v.city || v.location || '-';
            const cap = v.capacity || 0;

            html += `
            <tr>
                <td>#VN-${vId}</td>
                <td><strong>${name}</strong></td>
                <td><small class="text-muted">${addr}</small></td>
                <td><span class="badge bg-info text-dark">${cap} Pax</span></td>
                <td class="text-center">
                    <button onclick="deleteVenue(${vId}, '${name.replace(/'/g, "\\'")}')" class="btn btn-sm btn-outline-danger fw-bold">
                        <i class="fa-solid fa-trash"></i> Delete
                    </button>
                </td>
            </tr>`;
        });
        tbody.innerHTML = html;
    })
    .catch(err => {
        console.error("Fetch venues error:", err);
        document.getElementById('venuesTableBody').innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">Failed to load venues.</td></tr>';
    });
}

function loadAdminEvents(token) {
    fetch('api_events.php', {
        method: 'GET',
        headers: { 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(res => {
        const events = res.data || (Array.isArray(res) ? res : []);
        const tbody = document.getElementById('eventsAdminTableBody');
        document.getElementById('metric-total-events').innerText = events.length;

        if (!events || events.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-3">No events found.</td></tr>';
            return;
        }

        let html = '';
        events.forEach(e => {
            const price = parseFloat(e.ticket_price || 0).toFixed(2);
            const status = e.status || 'published';
            html += `
            <tr>
                <td>#EV-${e.event_id}</td>
                <td><strong>${e.title || 'Untitled'}</strong></td>
                <td><small>${e.venue_name || ('Venue ID: ' + (e.venue_id || '-'))}</small></td>
                <td><small>${e.event_date || '-'}</small></td>
                <td class="fw-bold text-primary">RM ${price}</td>
                <td>${e.available_tickets ?? e.total_tickets ?? 0} / ${e.total_tickets ?? 0}</td>
                <td><span class="badge ${status === 'active' || status === 'published' ? 'bg-success' : 'bg-secondary'}">${status}</span></td>
            </tr>`;
        });
        tbody.innerHTML = html;
    })
    .catch(err => {
        document.getElementById('eventsAdminTableBody').innerHTML = '<tr><td colspan="7" class="text-center text-muted py-3">Failed to load events.</td></tr>';
    });
}

function saveVenue() {
    const venueName = document.getElementById('venue_name').value.trim();
    const address = document.getElementById('venue_address').value.trim();
    const capacity = document.getElementById('venue_capacity').value;

    if (!venueName) {
        alert('Please enter a venue name!');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'save_venue_direct');
    formData.append('venue_name', venueName);
    formData.append('address', address);
    formData.append('capacity', capacity);

    fetch('admin_view.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Venue successfully saved to database!');
            document.getElementById('addVenueForm').reset();
            const modalElem = document.getElementById('addVenueModal');
            if (modalElem && window.bootstrap) {
                const modal = bootstrap.Modal.getInstance(modalElem) || new bootstrap.Modal(modalElem);
                modal.hide();
            }
            loadAdminVenues();
        } else {
            alert('Error: ' + (data.message || 'Failed to save venue.'));
        }
    })
    .catch(err => {
        console.error('Save Venue Error:', err);
        alert('Error connecting to database.');
    });
}

function deleteVenue(venueId, venueName) {
    if (!confirm('Are you sure you want to delete venue "' + venueName + '"?')) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'delete_venue_direct');
    formData.append('venue_id', venueId);

    fetch('admin_view.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Venue successfully deleted!');
            loadAdminVenues();
        } else {
            alert('Failed to delete venue: ' + data.message);
        }
    })
    .catch(err => {
        console.error('Delete error:', err);
        alert('Error connecting to database.');
    });
}
</script>