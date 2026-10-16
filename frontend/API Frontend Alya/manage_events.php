<?php
require_once 'header.php';

// Normalize role check
$userRole = strtolower($currentUser['role'] ?? '');
if (!in_array($userRole, ['organiser','admin'], true)) {
    echo "<div class='auth-error'>Access Denied. Organiser or Administrator role required.</div>";
    require_once 'footer.php';
    exit;
}

// Fetch venues and categories for dropdown selections directly via PHP
$venues =$pdo->query("SELECT * FROM venues ORDER BY venue_name ASC")->fetchAll();
$categories =$pdo->query("SELECT * FROM categories ORDER BY category_name ASC")->fetchAll();

// Never expose other organisers' customer records in the HTML.
if ($userRole === 'admin') {
    $bookingsStmt = $pdo->query("SELECT b.booking_id,b.event_id,b.quantity,b.total_price,b.status AS booking_status,u.full_name AS user_name,u.email AS user_email,p.status AS payment_status,p.method AS payment_method,p.amount AS payment_amount FROM bookings b JOIN users u ON b.user_id=u.user_id LEFT JOIN payments p ON b.booking_id=p.booking_id ORDER BY b.booked_at DESC");
} else {
    $bookingsStmt = $pdo->prepare("SELECT b.booking_id,b.event_id,b.quantity,b.total_price,b.status AS booking_status,u.full_name AS user_name,u.email AS user_email,p.status AS payment_status,p.method AS payment_method,p.amount AS payment_amount FROM bookings b JOIN events e ON b.event_id=e.event_id JOIN users u ON b.user_id=u.user_id LEFT JOIN payments p ON b.booking_id=p.booking_id WHERE e.organiser_id=? ORDER BY b.booked_at DESC");
    $bookingsStmt->execute([(int)$currentUser['user_id']]);
}
$allBookings = $bookingsStmt->fetchAll();

$bookingsByEvent = [];
foreach ($allBookings as $bk) {$bookingsByEvent[$bk['event_id']][] =$bk;
}
?>

<div class="dashboard-container">
    <!-- Create Event Form Card -->
    <div class="order-summary-card">
        <h2 class="page-title" id="event-form-heading">Create New Event</h2>
        <p class="text-xs text-slate-500 mb-3">Only events you own can be edited or deleted.</p>
        <form id="create-event-form" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="form-group sm:col-span-2">
                <label class="form-label">Event Title</label>
                <input type="text" id="event-title" required class="form-input text-xs" placeholder="AI & Future Tech Summit">
            </div>
            <div class="form-group">
                <label class="form-label">Category</label>
                <select id="event-category" required class="form-input text-xs bg-white">
                    <?php foreach ($categories as$cat): ?>
                        <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Venue</label>
                <select id="event-venue" required class="form-input text-xs bg-white">
                    <?php foreach ($venues as$v): ?>
                        <option value="<?= $v['venue_id'] ?>"><?= htmlspecialchars($v['venue_name']) ?> (Cap: <?=$v['capacity'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Event Date</label>
                <input type="date" id="event-date" required class="form-input text-xs">
            </div>
            <div class="form-group grid grid-cols-2 gap-2">
                <div>
                    <label class="form-label">Start Time</label>
                    <input type="time" id="event-start-time" required class="form-input text-xs" value="09:00">
                </div>
                <div>
                    <label class="form-label">End Time</label>
                    <input type="time" id="event-end-time" required class="form-input text-xs" value="17:00">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Ticket Price (RM)</label>
                <input type="number" step="0.01" id="event-price" required class="form-input text-xs" value="0.00">
            </div>
            <div class="form-group">
                <label class="form-label">Total Tickets Capacity</label>
                <input type="number" id="event-tickets" required class="form-input text-xs" value="100">
            </div>
            <div class="form-group sm:col-span-2">
                <label class="form-label">Image URL / Path</label>
                <input type="text" id="event-image" class="form-input text-xs" placeholder="images/techsummit.jpg" value="assets/images/default.jpg">
            </div>
            <div class="sm:col-span-2 pt-2">
                <button type="submit" id="event-submit-btn" class="primary-btn text-xs py-2.5 px-5">Add Event</button> <button type="button" id="cancel-edit-btn" class="hidden text-xs px-4 py-2" onclick="cancelEdit()">Cancel Edit</button>
            </div>
        </form>
    </div>

    <!-- Events List Table -->
    <div class="order-summary-card">
        <h2 class="page-title">Event Directory</h2>
        <div class="flex gap-2 mb-4 mt-2">
            <button type="button" id="tab-mine" class="primary-btn text-xs py-2 px-4" onclick="setEventTab('mine')">My Events (Manage)</button>
            <button type="button" id="tab-all" class="text-xs px-4 py-2 rounded border" onclick="setEventTab('all')">All Events (View Only)</button>
        </div>
        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Event Title</th>
                        <th>Date & Venue</th>
                        <th>Bookings / Capacity</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="events-table-body">
                    <tr><td colspan="4" class="p-4 text-center text-slate-500">Loading events...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ATTENDEES MODAL -->
<div id="attendees-modal" class="modal-overlay hidden">
    <div class="modal-card-lg">
        <div class="modal-header">
            <div>
                <h3 id="modal-event-name" class="modal-title text-base">Attendee Roster</h3>
                <p class="modal-subtitle">Full list of registered bookings and participants</p>
            </div>
            <button onclick="closeAttendeesModal()" class="modal-close-btn"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        
        <div class="overflow-y-auto flex-grow">
            <table class="custom-table text-xs">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Attendee Name</th>
                        <th>Email</th>
                        <th>Tickets / Total</th>
                        <th>Payment</th>
                    </tr>
                </thead>
                <tbody id="attendees-table-body"></tbody>
            </table>
        </div>

        <button onclick="closeAttendeesModal()" class="primary-btn w-full justify-center py-2.5 text-xs">Close Roster</button>
    </div>
</div>

<script>
const bookingsDataMap = <?= json_encode($bookingsByEvent, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
const currentUserId = <?= (int)($currentUser['user_id'] ?? 0) ?>;
const isAdmin = <?= $userRole === 'admin' ? 'true' : 'false' ?>;
let eventTab = 'mine';
let editingEventId = null;
let myEventRows = [];
function setEventTab(tab) {
    eventTab = tab;
    document.getElementById('tab-mine').className = tab === 'mine' ? 'primary-btn text-xs py-2 px-4' : 'text-xs px-4 py-2 rounded border';
    document.getElementById('tab-all').className = tab === 'all' ? 'primary-btn text-xs py-2 px-4' : 'text-xs px-4 py-2 rounded border';
    fetchEvents();
}
function cancelEdit() {
    editingEventId = null;
    document.getElementById('create-event-form').reset();
    document.getElementById('event-form-heading').textContent = 'Create New Event';
    document.getElementById('event-submit-btn').textContent = 'Add Event';
    document.getElementById('cancel-edit-btn').classList.add('hidden');
}
function editEvent(id) {
    const e = myEventRows.find(row => Number(row.event_id) === Number(id));
    if (!e) return;
    editingEventId = Number(id);
    const values = {'event-title':e.title,'event-category':e.category_id,'event-venue':e.venue_id,'event-date':e.event_date,'event-start-time':e.start_time?.slice(0,5),'event-end-time':e.end_time?.slice(0,5),'event-price':e.ticket_price,'event-tickets':e.total_tickets,'event-image':e.image_url};
    for (const [key,value] of Object.entries(values)) document.getElementById(key).value = value ?? '';
    document.getElementById('event-form-heading').textContent = 'Edit My Event';
    document.getElementById('event-submit-btn').textContent = 'Save Changes';
    document.getElementById('cancel-edit-btn').classList.remove('hidden');
    document.getElementById('create-event-form').scrollIntoView({behavior:'smooth'});
}

document.addEventListener("DOMContentLoaded", function() {
    fetchEvents();

    document.getElementById('create-event-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const payload = {
            title: document.getElementById('event-title').value,
            category_id: parseInt(document.getElementById('event-category').value),
            venue_id: parseInt(document.getElementById('event-venue').value),
            event_date: document.getElementById('event-date').value,
            start_time: document.getElementById('event-start-time').value + ':00',
            end_time: document.getElementById('event-end-time').value + ':00',
            ticket_price: parseFloat(document.getElementById('event-price').value),
            total_tickets: parseInt(document.getElementById('event-tickets').value),
            image_url: document.getElementById('event-image').value,
            status: 'active',
            ...(editingEventId ? {event_id: editingEventId} : {})
        };

        const token = localStorage.getItem('auth_token') || '';

        fetch('api_events.php', {
            method: editingEventId ? 'PUT' : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(editingEventId ? 'Event updated successfully!' : 'Event published successfully!');
                cancelEdit();
                fetchEvents();
            } else {
                alert('Failed to publish event: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(err => {
            console.error('Error creating event:', err);
            alert('An unexpected error occurred.');
        });
    });
});

function fetchEvents() {
    fetch('api_events.php?scope=' + (eventTab === 'mine' ? 'mine' : 'all'), {
        method: 'GET',
        headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + (localStorage.getItem('auth_token') || '') }
    })
    .then(response => response.json())
    .then(res => {
        const tbody = document.getElementById('events-table-body');
        tbody.innerHTML = '';

        if (res.status === 'success' && Array.isArray(res.data) && res.data.length > 0) {
            if (eventTab === 'mine') myEventRows = res.data;
            res.data.forEach(e => {
                const eventId = e.event_id;
                const eventBookings = bookingsDataMap[eventId] || [];
                const countBookings = eventBookings.length;
                const capacity = e.total_tickets || 100;

                let actionHtml = '';
                if (eventTab === 'mine' && countBookings > 0) {
                    const encodedBookings = encodeURIComponent(JSON.stringify(eventBookings));
                    const encodedTitle = encodeURIComponent(e.title);
                    actionHtml = `<button type="button" data-bookings="${encodedBookings}" data-title="${encodedTitle}" onclick="openAttendeesModalFromButton(this)" class="btn-view-roster"><i class="fa-solid fa-users mr-1"></i> View Bookings</button>`;
                } else if (eventTab === 'mine') {
                    actionHtml = `<span class="text-xs text-slate-400 italic">No bookings</span>`;
                }

                if (eventTab === 'mine') actionHtml += ` <button onclick="editEvent(${eventId})" class="text-xs px-2 py-1 rounded border">Edit</button> <button onclick="deleteEvent(${eventId})" class="btn-delete-row ml-1" title="Delete Event">
                                    <i class="fa-solid fa-trash"></i>
                                </button>`;

                if (eventTab === 'all') actionHtml = '<span class="text-xs text-slate-500">View Only</span>';
                const row = `
                <tr>
                    <td>
                        <span class="font-bold text-slate-900 block">${escapeHtml(e.title)}</span>
                        <span class="text-xs text-emerald-600 font-medium">${escapeHtml(e.category_name)}</span>
                    </td>
                    <td class="text-xs text-slate-600">
                        <div>${escapeHtml(e.event_date)}</div>
                        <div class="text-slate-400">${escapeHtml(e.venue_name)}</div>
                    </td>
                    <td>
                        <span class="seats-badge">${countBookings} / ${capacity} Bookings</span>
                    </td>
                    <td class="text-right">
                        <div class="flex items-center justify-end space-x-2">${actionHtml}</div>
                    </td>
                </tr>`;
                tbody.innerHTML += row;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="4" class="p-4 text-center text-slate-500">No events found in this section.</td></tr>';
        }
    })
    .catch(err => {
        console.error('Error fetching events:', err);
        document.getElementById('events-table-body').innerHTML = '<tr><td colspan="4" class="p-4 text-center text-red-500">Failed to load events from API.</td></tr>';
    });
}

function deleteEvent(eventId) {
    if (!confirm('Are you sure you want to delete this event?')) return;

    const token = localStorage.getItem('auth_token') || '';

    fetch(`api_events.php?event_id=${eventId}`, {
        method: 'DELETE',
        headers: {
            'Authorization': 'Bearer ' + token
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            fetchEvents();
        } else {
            alert(data.message || 'Failed to delete event.');
        }
    })
    .catch(err => {
        console.error('Error deleting event:', err);
        alert('An unexpected error occurred.');
    });
}

function openAttendeesModalFromButton(button) {
    try {
        const bookings = JSON.parse(decodeURIComponent(button.getAttribute('data-bookings')));
        const eventTitle = decodeURIComponent(button.getAttribute('data-title'));
        
        document.getElementById('modal-event-name').innerText = "Roster: " + eventTitle;
        let tbody = document.getElementById('attendees-table-body');
        tbody.innerHTML = '';

        if (!bookings || bookings.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="p-4 text-center text-slate-500">No bookings recorded for this event.</td></tr>';
        } else {
            bookings.forEach(b => {
                let row = `<tr class="hover:bg-slate-50">
                    <td class="p-2.5 font-mono font-bold text-emerald-700">BK-${b.booking_id}</td>
                    <td class="p-2.5 font-semibold text-slate-800">${escapeHtml(b.user_name)}</td>
                    <td class="p-2.5 text-slate-600">${escapeHtml(b.user_email)}</td>
                    <td class="p-2.5">${b.quantity} tickets (RM ${parseFloat(b.total_price).toFixed(2)})</td>
                    <td class="p-2.5"><span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded font-medium">${b.payment_status ? b.payment_status.toUpperCase() : 'PENDING'}</span></td>
                </tr>`;
                tbody.innerHTML += row;
            });
        }

        document.getElementById('attendees-modal').classList.remove('hidden');
    } catch (e) {
        console.error("Error parsing bookings data:", e);
    }
}

function closeAttendeesModal() {
    document.getElementById('attendees-modal').classList.add('hidden');
}

function escapeHtml(text) {
    if (!text) return '';
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
}
</script>

<?php require_once 'footer.php'; ?>