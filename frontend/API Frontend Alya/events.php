<?php
require_once 'header.php';
?>

<div class="page-header-card">
    <div>
        <h2 class="page-title">Events & Ticket Catalog</h2>
        <p class="page-subtitle">Browse upcoming concerts, tech conferences, sports tournaments, and cultural showcases with instant booking</p>
    </div>
</div>

<div class="catalog-grid" id="events-catalog-grid">
    <p class="text-slate-500 text-sm">Loading events catalog...</p>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    fetchEventsCatalog();
});

function fetchEventsCatalog() {
    fetch('api_events.php', {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(res => {
        const grid = document.getElementById('events-catalog-grid');
        grid.innerHTML = '';

        if (res.status === 'success' && res.data && res.data.length > 0) {
            res.data.forEach(ev => {
                const priceFormatted = (parseFloat(ev.ticket_price) === 0) 
                    ? 'Free Admission' 
                    : 'RM ' + parseFloat(ev.ticket_price).toFixed(2);
                
                const imageUrl = ev.image_url || 'images/default.jpg';

                const card = `
                <div class="event-card flex flex-col justify-between">
                    <div>
                        <div class="w-full h-44 mb-3 overflow-hidden rounded-xl bg-slate-100">
                            <img src="${escapeHtml(imageUrl)}" alt="${escapeHtml(ev.title)}" class="w-full h-full object-cover">
                        </div>
                        <div class="event-card-header">
                            <span class="category-badge-brand">${escapeHtml(ev.category_name)}</span>
                            <span class="seats-badge">${ev.available_tickets} seats left</span>
                        </div>
                        <h3 class="event-card-title">${escapeHtml(ev.title)}</h3>
                        <p class="booking-details mt-1"><i class="fa-solid fa-location-dot mr-1"></i>${escapeHtml(ev.venue_name)}</p>
                        <p class="booking-details mt-0.5"><i class="fa-regular fa-calendar mr-1"></i>${escapeHtml(ev.event_date)} (${escapeHtml(ev.start_time)})</p>
                    </div>
                    <div class="event-card-footer mt-4">
                        <span class="event-card-price">${priceFormatted}</span>
                        <a href="checkout.php?event_id=${ev.event_id}" class="primary-btn text-xs space-x-1.5 py-2.5 px-5">
                            <i class="fa-solid fa-credit-card"></i>
                            <span>Book & Pay</span>
                        </a>
                    </div>
                </div>`;
                grid.innerHTML += card;
            });
        } else {
            grid.innerHTML = `<p class="text-slate-500 text-sm">No events available in the database.</p>`;
        }
    })
    .catch(err => {
        console.error('Error fetching events catalog:', err);
        document.getElementById('events-catalog-grid').innerHTML = `<p class="text-red-500 text-sm">Failed to load events from API.</p>`;
    });
}

function escapeHtml(text) {
    if (!text) return '';
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
}
</script>

<?php require_once 'footer.php'; ?>