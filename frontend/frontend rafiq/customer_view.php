<div class="dashboard-container">
    <!-- BANNER SECTION (NEW DEEP TEAL / SLATE DESIGN) -->
    <div class="customer-hero-card p-4 p-md-5 mb-4 text-white rounded-4 shadow-sm" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-left: 6px solid #0d9488;">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="badge bg-teal bg-opacity-20 text-teal fw-semibold px-3 py-2 rounded-pill mb-2" style="background-color: rgba(13, 148, 136, 0.2); color: #2dd4bf;">
                    <i class="fa-solid fa-user-check me-1"></i> CUSTOMER PORTAL
                </span>
                <h1 class="fw-bold fs-2 mb-2">Welcome back, <span id="user-welcome-name" class="text-info"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Customer'); ?></span>!</h1>
                <p class="text-light fs-6 mb-0 opacity-75" style="max-width: 650px;">Explore upcoming concerts, tech conferences, sports tournaments, and cultural showcases with instant booking.</p>
            </div>
            
            <!-- ACTION BUTTONS -->
            <div class="d-flex gap-2">
                <button onclick="focusBrowseSearch()" class="btn btn-light fw-bold px-4 py-2 rounded-3 shadow-sm text-dark d-flex align-items-center gap-2">
                    <i class="fa-solid fa-magnifying-glass text-teal" style="color: #0d9488;"></i> Browse Events
                </button>
                <button onclick="scrollToMyBookings()" class="btn btn-outline-light fw-bold px-4 py-2 rounded-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-ticket text-warning"></i> My Bookings
                </button>
            </div>
        </div>
    </div>

    <!-- METRICS GRID SECTION (UPDATED COLOR SCHEME) -->
    <div class="row g-3 mb-5">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-white d-flex flex-row align-items-center gap-3">
                <div class="p-3 rounded-4 text-white" style="background-color: #0d9488;">
                    <i class="fa-solid fa-ticket fs-3"></i>
                </div>
                <div>
                    <small class="text-muted fw-bold text-uppercase">Active Bookings</small>
                    <h2 class="fw-bold mb-0 text-dark" id="metric-active-bookings">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-white d-flex flex-row align-items-center gap-3">
                <div class="p-3 rounded-4 text-white" style="background-color: #059669;">
                    <i class="fa-solid fa-calendar-check fs-3"></i>
                </div>
                <div>
                    <small class="text-muted fw-bold text-uppercase">Upcoming Events</small>
                    <h2 class="fw-bold mb-0 text-dark" id="metric-upcoming-events">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-white d-flex flex-row align-items-center gap-3">
                <div class="p-3 rounded-4 text-white" style="background-color: #7c3aed;">
                    <i class="fa-solid fa-qrcode fs-3"></i>
                </div>
                <div>
                    <small class="text-muted fw-bold text-uppercase">QR Passes Ready</small>
                    <h2 class="fw-bold mb-0 text-dark" id="metric-qr-passes">0</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- AVAILABLE EVENTS SECTION (BROWSE & LIVE SEARCH) -->
    <div id="available-events-sec" class="mb-5">
        <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
            <div class="row align-items-center">
                <div class="col-md-6 mb-2 mb-md-0">
                    <h4 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-compass me-2 text-teal" style="color: #0d9488;"></i>Browse & Search Events</h4>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fa-solid fa-search text-muted"></i></span>
                        <input type="text" id="searchInput" class="form-control bg-light border-0 shadow-none" placeholder="Search event title, venue, or category..." onkeyup="filterEvents()">
                    </div>
                </div>
            </div>
        </div>
        
        <div id="eventList" class="row g-3">
            <div class="col-12 text-center text-muted py-4">Loading events...</div>
        </div>
    </div>

    <!-- MY BOOKINGS SECTION (CUSTOMER SPECIFIC HISTORY) -->
    <div id="my-bookings-sec" class="pt-2">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold m-0 text-dark"><i class="fa-solid fa-receipt me-2 text-warning"></i>My Bookings & History</h4>
            <span class="badge bg-dark px-3 py-2 rounded-pill">Customer Pass</span>
        </div>
        
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark" style="background-color: #1e293b;">
                            <tr>
                                <th class="ps-3">Booking ID</th>
                                <th>Event Title</th>
                                <th>Date</th>
                                <th>Quantity</th>
                                <th>Total Price</th>
                                <th>Status</th>
                                <th class="text-center pe-3">Action Pass</th>
                            </tr>
                        </thead>
                        <tbody id="bookingsTableBody">
                            <tr><td colspan="7" class="text-center text-muted py-4">Loading bookings...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL POPUP QR CODE PASS -->
<div class="modal fade" id="qrModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg text-center p-3 rounded-4">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold text-dark" id="qrModalTitle">Event Ticket Pass</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted small mb-2">Scan this QR code at the event entrance for entry verification.</p>
        <div class="d-flex justify-content-center my-3">
            <img id="qrImage" src="" alt="QR Code Pass" class="img-fluid border p-2 rounded-3 bg-white shadow-sm" style="width: 200px; height: 200px;">
        </div>
        <div class="p-3 bg-light rounded-3 text-start small">
            <p class="mb-1"><strong>Booking Reference:</strong> <span id="qrBkId" class="text-primary fw-bold"></span></p>
            <p class="mb-1"><strong>Ticket Holder:</strong> <span id="qrHolder" class="fw-bold"></span></p>
            <p class="mb-0"><strong>Status:</strong> <span class="badge bg-success">Verified Pass</span></p>
        </div>
      </div>
      <div class="modal-footer border-0 pt-0 justify-content-center">
        <button type="button" class="btn btn-secondary btn-sm px-4 fw-bold rounded-3" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const token = localStorage.getItem('auth_token') || '';

    if (token) {
        try {
            const decoded = JSON.parse(atob(token));
            if (decoded.full_name) {
                document.getElementById('user-welcome-name').innerText = decoded.full_name;
            }
        } catch (e) {}
    }

    loadEvents(token);
    loadBookings(token);
});

function focusBrowseSearch() {
    const searchSec = document.getElementById('available-events-sec');
    const searchInput = document.getElementById('searchInput');
    searchSec.scrollIntoView({ behavior: 'smooth' });
    setTimeout(() => { searchInput.focus(); }, 400);
}

function scrollToMyBookings() {
    document.getElementById('my-bookings-sec').scrollIntoView({ behavior: 'smooth' });
}

function loadEvents(token) {
    fetch('api_events.php', {
        method: 'GET',
        headers: { 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(res => {
        const events = res.data || (Array.isArray(res) ? res : []);
        const container = document.getElementById('eventList');

        if (!events || events.length === 0) {
            container.innerHTML = '<div class="col-12"><p class="text-muted text-center py-4">No active events available at the moment.</p></div>';
            return;
        }

        const activeEvents = events.filter(ev => ev.status === 'active' || ev.status === 'published');
        document.getElementById('metric-upcoming-events').innerText = activeEvents.length;

        let html = '';
        activeEvents.forEach(event => {
            const id = event.event_id;
            const title = event.title || 'Untitled Event';
            const price = parseFloat(event.ticket_price || 0).toFixed(2);
            const venue = event.venue_name || ('Venue ID: ' + (event.venue_id || '-'));
            const date = event.event_date || '-';
            const avail = event.available_tickets ?? event.total_tickets ?? 0;
            const total = event.total_tickets ?? 0;
            const category = event.category_name || 'General';

            const lowerTitle = title.toLowerCase();
            let imgUrl = 'images/default.jpg';

            if (lowerTitle.includes('rock')) {
                imgUrl = 'images/rockfest.jpg';
            } else if (lowerTitle.includes('tech') || lowerTitle.includes('innovat')) {
                imgUrl = 'images/techsummit.jpg';
            } else if (lowerTitle.includes('futsal') || lowerTitle.includes('sport')) {
                imgUrl = 'images/futsal.jpg';
            } else if (lowerTitle.includes('jiwa') || lowerTitle.includes('kacau')) {
                imgUrl = 'images/jiwakacau.jpg';
            } else if (lowerTitle.includes('pitch') || lowerTitle.includes('ai') || lowerTitle.includes('demo')) {
                imgUrl = 'images/pitchday.jpg';
            } else if (lowerTitle.includes('seni') || lowerTitle.includes('warisan') || lowerTitle.includes('art')) {
                imgUrl = 'images/seniwarisan.jpg';
            } else if (lowerTitle.includes('comedy') || lowerTitle.includes('lawak') || lowerTitle.includes('stand')) {
                imgUrl = 'images/comedy.jpg';
            } else if (event.image_url) {
                imgUrl = event.image_url;
            }

            html += `
            <div class="col-md-4 event-card-item">
              <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                <img src="${imgUrl}" class="card-img-top" alt="${title}" style="height: 160px; object-fit: cover;" onerror="this.onerror=null; this.src='images/default.jpg';">
                <div class="card-body d-flex flex-column p-3">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-success bg-opacity-10 text-success fw-bold px-2 py-1">Available</span>
                    <span class="badge bg-secondary px-2 py-1">${category}</span>
                  </div>
                  <h5 class="card-title fw-bold text-dark event-title mb-1">${title}</h5>
                  <p class="card-text text-muted mb-1 small"><i class="fa-solid fa-location-dot me-1 text-danger"></i>${venue}</p>
                  <p class="card-text text-muted mb-1 small"><i class="fa-solid fa-calendar me-1 text-primary"></i>Date: ${date}</p>
                  <p class="card-text text-muted mb-3 small"><i class="fa-solid fa-ticket me-1 text-warning"></i>Tickets Left: <strong>${avail}</strong> / ${total}</p>
                  <div class="mt-auto d-flex justify-content-between align-items-center pt-2 border-top">
                    <strong class="fs-5" style="color: #0d9488;">RM ${price}</strong>
                    <div class="d-flex align-items-center gap-1">
                      <input type="number" id="qty_${id}" value="1" min="1" max="${avail}" class="form-control form-control-sm rounded-2" style="width: 55px;">
                      <button onclick="bookTicket(${id})" class="btn btn-sm text-white fw-bold px-3 rounded-2" style="background-color: #0d9488;" ${avail <= 0 ? 'disabled' : ''}>Book</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>`;
        });
        container.innerHTML = html;
    })
    .catch(err => {
        console.error('Error loading events:', err);
        document.getElementById('eventList').innerHTML = '<div class="col-12 text-danger text-center">Failed to load events.</div>';
    });
}

function loadBookings(token) {
    if (!token) {
        document.getElementById('bookingsTableBody').innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Please log in to view your bookings.</td></tr>';
        return;
    }

    fetch('api_bookings.php', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer ' + token
        }
    })
    .then(res => res.json())
    .then(res => {
        const bookings = res.data || (Array.isArray(res) ? res : []);
        const count = bookings.length;

        document.getElementById('metric-active-bookings').innerText = count;
        document.getElementById('metric-qr-passes').innerText = count;

        const tbody = document.getElementById('bookingsTableBody');
        if (count === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">No booking records found.</td></tr>';
            return;
        }

        let html = '';
        bookings.forEach(b => {
            const bId = b.booking_id;
            const title = b.title || ('Event #' + b.event_id);
            const date = b.event_date || '-';
            const qty = b.quantity || 1;
            const total = parseFloat(b.total_amount || 0).toFixed(2);
            const status = b.payment_status || 'paid';
            const qrCodeValue = b.qr_code || ('TICKET-BK' + bId + '-' + (b.event_id || '1'));

            html += `
            <tr>
              <td class="ps-3 fw-bold text-dark">#BK-${bId}</td>
              <td><strong>${title}</strong></td>
              <td><small class="text-muted">${date}</small></td>
              <td><span class="badge bg-light text-dark border">${qty} Ticket(s)</span></td>
              <td class="fw-bold" style="color: #0d9488;">RM ${total}</td>
              <td><span class="badge ${status === 'paid' ? 'bg-success' : 'bg-warning'}">${status}</span></td>
              <td class="text-center pe-3">
                <button onclick="showQRModal('#BK-${bId}', '${title.replace(/'/g, "\\'")}', '${qrCodeValue}')" class="btn btn-sm btn-outline-dark fw-bold rounded-2">
                  <i class="fa-solid fa-qrcode me-1 text-primary"></i> View QR Pass
                </button>
              </td>
            </tr>`;
        });
        tbody.innerHTML = html;
    })
    .catch(err => {
        document.getElementById('bookingsTableBody').innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Failed to load booking records.</td></tr>';
    });
}

function showQRModal(bkId, title, qrData) {
    document.getElementById('qrModalTitle').innerText = title;
    document.getElementById('qrBkId').innerText = bkId;
    document.getElementById('qrHolder').innerText = document.getElementById('user-welcome-name').innerText;
    
    const qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(qrData);
    document.getElementById('qrImage').src = qrApiUrl;

    const modal = new bootstrap.Modal(document.getElementById('qrModal'));
    modal.show();
}

function bookTicket(eventId) {
    const token = localStorage.getItem('auth_token');
    if (!token) {
        alert('Please log in first!');
        return;
    }

    const qtyInput = document.getElementById('qty_' + eventId);
    const qty = qtyInput ? parseInt(qtyInput.value) : 1;

    fetch('api_bookings.php', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': 'Bearer ' + token
        },
        body: JSON.stringify({
            event_id: eventId,
            quantity: qty,
            payment_method: 'Online Banking'
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Booking Successful! Booking ID: #' + data.booking_id);
            loadEvents(token);
            loadBookings(token);
        } else {
            alert('Booking failed: ' + (data.message || 'Unknown error'));
        }
    });
}

function filterEvents() {
    let input = document.getElementById('searchInput').value.toLowerCase();
    let cards = document.getElementsByClassName('event-card-item');
    for (let card of cards) {
        let title = card.querySelector('.event-title').innerText.toLowerCase();
        card.style.display = title.includes(input) ? "" : "none";
    }
}
</script>