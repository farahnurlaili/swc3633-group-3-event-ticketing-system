<?php
require_once 'header.php';
?>

<div class="checkout-wrapper">
    <div class="checkout-header-card">
        <div>
            <h2 class="page-title">Checkout & Payment Gateway</h2>
            <p class="page-subtitle">Secure transaction processing for EventTix Pro via API</p>
        </div>
        <span class="ssl-badge">
            <i class="fa-solid fa-lock"></i> 256-bit SSL Encrypted
        </span>
    </div>

    <div class="checkout-grid" id="checkout-content-grid">
        <!-- Event Summary -->
        <div class="order-summary-col order-summary-card">
            <h3 class="order-summary-title">Order Summary</h3>
            <div id="summary-details">
                <p class="text-slate-500 text-sm">Loading event details...</p>
            </div>
            <div class="order-total-row">
                <span class="text-slate-600 font-medium">Total Amount:</span>
                <span class="font-bold text-slate-900 text-lg" id="summary-total-amount">RM 0.00</span>
            </div>
        </div>

        <!-- Payment Form -->
        <div class="payment-form-col payment-form-card">
            <h3 class="font-bold text-slate-900 text-base">Select Payment Method</h3>
            
            <form id="checkout-form" class="space-y-4">
                <div class="payment-options-list">
                    <!-- FPX -->
                    <label class="payment-option-label">
                        <div class="flex items-center space-x-3">
                            <input type="radio" name="payment_method" value="FPX Online Banking" checked class="payment-radio">
                            <div>
                                <p class="text-xs font-bold text-slate-800">FPX Online Banking</p>
                                <p class="text-[11px] text-slate-500">Maybank, CIMB, Bank Islam, RHB</p>
                            </div>
                        </div>
                        <i class="fa-solid fa-building-columns text-slate-400"></i>
                    </label>

                    <!-- eWallet -->
                    <label class="payment-option-label">
                        <div class="flex items-center space-x-3">
                            <input type="radio" name="payment_method" value="Touch 'n Go eWallet" class="payment-radio">
                            <div>
                                <p class="text-xs font-bold text-slate-800">Touch 'n Go eWallet / GrabPay</p>
                                <p class="text-[11px] text-slate-500">Instant QR scan payment</p>
                            </div>
                        </div>
                        <i class="fa-solid fa-wallet text-slate-400"></i>
                    </label>

                    <!-- Card -->
                    <label class="payment-option-label">
                        <div class="flex items-center space-x-3">
                            <input type="radio" name="payment_method" value="Credit / Debit Card" class="payment-radio">
                            <div>
                                <p class="text-xs font-bold text-slate-800">Credit or Debit Card</p>
                                <p class="text-[11px] text-slate-500">Visa, Mastercard</p>
                            </div>
                        </div>
                        <i class="fa-solid fa-credit-card text-slate-400"></i>
                    </label>
                </div>

                <div class="pt-3">
                    <button type="submit" id="pay-btn" class="primary-btn w-full justify-center py-3 text-sm font-bold space-x-2">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span id="pay-btn-text">Complete Booking</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const urlParams = new URLSearchParams(window.location.search);
const eventId = urlParams.get('event_id');
let currentEventPrice = 0;

document.addEventListener("DOMContentLoaded", function() {
    if (!eventId) {
        window.location.href = 'events.php';
        return;
    }

    // Fetch event details via api_events.php[cite: 10]
    fetch('api_events.php', {
        method: 'GET',
        headers: { 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(res => {
        if (res.status === 'success' && res.data) {
            const event = res.data.find(e => String(e.event_id) === String(eventId));
            
            if (!event || parseInt(event.available_tickets) <= 0) {
                document.getElementById('checkout-content-grid').innerHTML = 
                    "<div class='p-6 bg-red-50 text-red-600 rounded-2xl col-span-2'>Event unavailable or sold out.</div>";
                return;
            }

            currentEventPrice = parseFloat(event.ticket_price || 0);
            const priceFormatted = currentEventPrice === 0 ? 'Free' : 'RM ' + currentEventPrice.toFixed(2);

            document.getElementById('summary-details').innerHTML = `
                <span class="category-tag">${escapeHtml(event.category_name)}</span>
                <h4 class="booking-title mt-2">${escapeHtml(event.title)}</h4>
                <p class="booking-details mt-1"><i class="fa-solid fa-location-dot mr-1"></i>${escapeHtml(event.venue_name)}</p>
                <p class="booking-details mt-0.5"><i class="fa-regular fa-calendar mr-1"></i>${escapeHtml(event.event_date)} (${escapeHtml(event.start_time)})</p>
            `;

            document.getElementById('summary-total-amount').innerText = priceFormatted;
            document.getElementById('pay-btn-text').innerText = `Pay ${priceFormatted === 'Free' ? 'RM 0.00' : priceFormatted} & Complete Booking`;
        } else {
            document.getElementById('summary-details').innerHTML = `<p class='text-red-500 text-sm'>Failed to load event details.</p>`;
        }
    })
    .catch(err => {
        console.error('Error fetching event for checkout:', err);
        document.getElementById('summary-details').innerHTML = `<p class='text-red-500 text-sm'>Error connecting to API.</p>`;
    });

    // Handle form submission via api_bookings.php[cite: 5]
    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
        const token = localStorage.getItem('auth_token') || '';

        const payload = {
            event_id: parseInt(eventId),
            quantity: 1,
            payment_method: selectedMethod
        };

        fetch('api_bookings.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Payment Successful! Ticket Pass Generated.');
                window.location.href = 'bookings.php';
            } else {
                alert('Booking failed: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(err => {
            console.error('Error processing checkout:', err);
            alert('An unexpected error occurred during payment processing.');
        });
    });
});

function escapeHtml(text) {
    if (!text) return '';
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
}
</script>

<?php require_once 'footer.php'; ?>