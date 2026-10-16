<?php
require_once 'header.php';
?>

<div class="page-header-card">
    <div>
        <h2 class="page-title">My Bookings & QR Passes</h2>
        <p class="page-subtitle">Active reservations fetched via API</p>
    </div>
    <a href="events.php" class="primary-btn">+ Book New Event</a>
</div>

<div class="bookings-container" id="bookings-container">
    <div class="empty-state-card" id="loading-state">Loading your bookings...</div>
</div>

<!-- QR CODE MODAL -->
<div id="qr-modal" class="modal-overlay hidden">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title">Official Ticket QR Pass</h3>
            <button onclick="closeQrModal()" class="modal-close-btn"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        
        <div class="qr-code-wrapper">
            <img id="qr-image-src" src="" alt="Ticket QR Code" class="qr-code-img">
        </div>

        <div class="modal-info-box">
            <p><strong class="text-slate-700">Booking ID:</strong> <span id="modal-ticket-id" class="font-mono font-bold text-brand-700"></span></p>
            <p><strong class="text-slate-700">Event:</strong> <span id="modal-event-title"></span></p>
            <p><strong class="text-slate-700">Date:</strong> <span id="modal-event-date"></span></p>
        </div>

        <button onclick="closeQrModal()" class="primary-btn w-full justify-center py-2.5">Close Pass</button>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    fetchBookings();
});

function fetchBookings() {
    const token = localStorage.getItem('auth_token') || '';

    // Fetch bookings from api_bookings.php endpoint with Authorization header
    fetch('api_bookings.php', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer ' + token
        }
    })
    .then(response => response.json())
    .then(res => {
        const container = document.getElementById('bookings-container');
        container.innerHTML = '';

        if (res.status === 'success' && res.data && res.data.length > 0) {
            res.data.forEach(b => {
                const bookingIdStr = 'BK-' + b.booking_id;
                const card = `
                <div class="booking-item-card">
                    <div class="flex items-center space-x-4">
                        <div class="booking-icon-box">
                            <i class="fa-solid fa-ticket-simple"></i>
                        </div>
                        <div>
                            <div class="booking-meta-group">
                                <span class="ticket-id-badge">${bookingIdStr}</span>
                                <span class="status-indicator">&#9679; Confirmed</span>
                            </div>
                            <h4 class="booking-title">${escapeHtml(b.title)}</h4>
                            <p class="booking-details">
                                <i class="fa-regular fa-calendar mr-1"></i>${escapeHtml(b.event_date)} &bull; 
                                <span class="font-medium text-slate-700">${b.quantity} Ticket(s)</span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="booking-actions">
                        <span class="payment-badge">
                            <i class="fa-solid fa-receipt mr-1"></i> ${escapeHtml(b.method)} (RM ${parseFloat(b.total_amount).toFixed(2)})
                        </span>
                        <button onclick="openQrModal('${bookingIdStr}', '${escapeHtml(b.title).replace(/'/g, "\\'")}', '${escapeHtml(b.event_date)}')" class="qr-btn">
                            <i class="fa-solid fa-qrcode"></i>
                            <span>View QR Pass</span>
                        </button>
                    </div>
                </div>`;
                container.innerHTML += card;
            });
        } else {
            container.innerHTML = `<div class="empty-state-card">No bookings found. Browse events to book tickets!</div>`;
        }
    })
    .catch(err => {
        console.error('Error fetching bookings:', err);
        document.getElementById('bookings-container').innerHTML = `<div class="empty-state-card text-red-600">Failed to load bookings from API.</div>`;
    });
}

function openQrModal(ticketId, eventTitle, eventDate) {
    document.getElementById('modal-ticket-id').innerText = ticketId;
    document.getElementById('modal-event-title').innerText = eventTitle;
    document.getElementById('modal-event-date').innerText = eventDate;
    
    const qrData = encodeURIComponent(`BOOKING_ID:${ticketId}|EVENT:${eventTitle}|DATE:${eventDate}`);
    const qrApiUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${qrData}`;
    
    document.getElementById('qr-image-src').src = qrApiUrl;
    document.getElementById('qr-modal').classList.remove('hidden');
}

function closeQrModal() {
    document.getElementById('qr-modal').classList.add('hidden');
}

function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
}
</script>

<?php require_once 'footer.php'; ?>