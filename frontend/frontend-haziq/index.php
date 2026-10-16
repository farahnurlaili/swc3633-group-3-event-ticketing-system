<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>EventHub - Book Your Favourite Events</title>
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
            --border-color: #202736;
            --text-dim: #94A3B8;
            --input-bg: #1A202C;
        }

        body {
            background-color: var(--bg-dark);
            color: #FFFFFF;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Navbar */
        .navbar-custom {
            background-color: rgba(11, 15, 23, 0.85);
            border-bottom: 1px solid var(--border-color);
            backdrop-filter: blur(12px);
            z-index: 100;
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

        /* Hero Banner Section */
        .hero-section {
            position: relative;
            padding: 90px 20px 60px 20px;
            background: linear-gradient(180deg, rgba(147, 51, 234, 0.35) 0%, rgba(11, 15, 23, 0.95) 75%, var(--bg-dark) 100%),
                        url('../../images/rockfest.jpg') center/cover no-repeat;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
        }

        .hero-title {
            font-size: 3.2rem;
            font-weight: 800;
            line-height: 1.2;
            background: linear-gradient(180deg, #FFFFFF 30%, #C084FC 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            max-width: 850px;
            margin: 0 auto 16px auto;
        }

        .hero-subtitle {
            color: #CBD5E1;
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto 35px auto;
        }

        /* Search Bar & Buttons */
        .search-box-container {
            max-width: 650px;
            margin: 0 auto 35px auto;
            background: rgba(20, 26, 38, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 16px;
            padding: 6px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(10px);
        }

        .search-input {
            background: transparent !important;
            border: none !important;
            color: #FFFFFF !important;
            box-shadow: none !important;
            padding-left: 20px;
        }

        .btn-lime {
            background-color: var(--neon-lime);
            color: #000000;
            font-weight: 700;
            border: none;
            border-radius: 12px;
            padding: 10px 28px;
            transition: all 0.25s ease;
        }

        .btn-lime:hover {
            background-color: #c4f52c;
            color: #000000;
            box-shadow: 0 0 20px rgba(213, 255, 64, 0.4);
            transform: translateY(-2px);
        }

        /* Stats Counter */
        .stats-container {
            display: flex;
            justify-content: center;
            gap: 50px;
            margin-bottom: 35px;
        }

        .stat-val {
            font-size: 1.8rem;
            font-weight: 800;
            color: #FFFFFF;
        }

        .stat-lbl {
            font-size: 0.85rem;
            color: var(--text-dim);
        }

        /* Category Pill Tags */
        .category-pills {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .pill-tag {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #E2E8F0;
            padding: 8px 18px;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            backdrop-filter: blur(5px);
        }

        .pill-tag:hover, .pill-tag.active {
            background: rgba(213, 255, 64, 0.15);
            border-color: var(--neon-lime);
            color: var(--neon-lime);
        }

        /* Grid Layout & Cards */
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
            padding: 40px 0;
        }

        .event-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .event-card:hover {
            border-color: var(--neon-lime);
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
        }

        .card-img-wrapper {
            position: relative;
            border-radius: 14px;
            overflow: hidden;
            margin-bottom: 16px;
        }

        .card-img-wrapper img {
            width: 100%;
            height: 170px;
            object-fit: cover;
        }

        .badge-id {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(11, 15, 23, 0.85);
            color: var(--neon-lime);
            border: 1px solid var(--neon-lime);
            font-size: 0.75rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
        }

        .event-card-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #FFFFFF;
            margin-bottom: 4px;
        }

        .event-card-venue {
            font-size: 0.85rem;
            color: var(--text-dim);
            margin-bottom: 8px;
        }

        .event-card-desc {
            font-size: 0.85rem;
            color: #718096;
            margin-bottom: 16px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .event-card-meta {
            font-size: 0.8rem;
            color: var(--text-dim);
            padding-top: 12px;
            border-top: 1px solid var(--border-color);
            margin-bottom: 12px;
        }

        .btn-outline-dark-custom {
            background-color: rgba(255, 255, 255, 0.05);
            color: #FFFFFF;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-size: 0.85rem;
            padding: 6px 14px;
        }

        .btn-outline-dark-custom:hover {
            border-color: var(--neon-lime);
            color: var(--neon-lime);
        }

        /* Modal styling */
        .modal-content-dark {
            background-color: var(--card-bg);
            border: 1px solid var(--neon-lime);
            border-radius: 20px;
            color: #FFFFFF;
        }

        /* --- MY HISTORY PAGE STYLES --- */
        .history-container {
            max-width: 1000px;
            margin: 40px auto;
        }

        .history-filter-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .history-item-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 20px;
            transition: border-color 0.2s ease;
        }

        .history-item-card:hover {
            border-color: rgba(213, 255, 64, 0.4);
        }

        .history-img-thumb {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 14px;
        }

        .filter-select {
            background-color: var(--input-bg);
            border: 1px solid var(--border-color);
            color: #FFFFFF;
            border-radius: 10px;
            padding: 8px 14px;
            font-size: 0.88rem;
        }

        .filter-select:focus {
            background-color: var(--input-bg);
            color: #FFFFFF;
            border-color: var(--neon-lime);
            box-shadow: none;
        }

        .btn-emerald {
            background-color: #10B981;
            color: #FFFFFF;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 0.85rem;
            transition: all 0.2s;
        }

        .btn-emerald:hover {
            background-color: #059669;
            color: #FFFFFF;
        }

        .btn-outline-ticket {
            background-color: transparent;
            border: 1px solid var(--border-color);
            color: #CBD5E1;
            font-weight: 600;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 0.85rem;
            transition: all 0.2s;
        }

        .btn-outline-ticket:hover {
            border-color: var(--neon-lime);
            color: var(--neon-lime);
        }
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom px-4 sticky-top">
        <a class="navbar-brand brand-text" href="index.php">
            <i class="fa-solid fa-bolt me-2 text-warning"></i>EventHub<span>.</span>
        </a>
        <div class="ms-auto d-flex align-items-center">
            <span id="userInfo" class="me-3 text-secondary small"></span>
            <button id="historyBtn" onclick="loadHistory()" class="btn btn-outline-dark-custom me-2 d-none">My History</button>
            <a href="events.php" id="staffLink" class="btn btn-outline-dark-custom me-2 d-none">Organiser Hub</a>
            <a href="admin.php" id="adminLink" class="btn btn-outline-dark-custom me-2 d-none">Admin Panel</a>
            <a href="logout.php" class="btn btn-outline-danger btn-sm px-3 rounded-2">Logout</a>
        </div>
    </nav>

    <!-- Hero Banner Area -->
    <div id="heroSection" class="hero-section">
        <h1 class="hero-title">Book Your Favourite<br>Events & Concert Tickets</h1>
        <p class="hero-subtitle">Experience the thrill of live events. Get your tickets now with exclusive pricing and member benefits.</p>

        <!-- Search Bar -->
        <div class="search-box-container">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-0 text-secondary ps-3"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" id="searchInput" class="form-control search-input" placeholder="Search events by title or keywords...">
                <button class="btn btn-lime" onclick="loadEvents()">Search</button>
            </div>
        </div>

        <!-- Metrics -->
        <div class="stats-container">
            <div>
                <div class="stat-val">500+</div>
                <div class="stat-lbl">Events Listed</div>
            </div>
            <div>
                <div class="stat-val">50K+</div>
                <div class="stat-lbl">Tickets Sold</div>
            </div>
            <div>
                <div class="stat-val">98%</div>
                <div class="stat-lbl">Satisfaction</div>
            </div>
        </div>

        <!-- Category Tag Pills -->
        <div class="category-pills">
            <span class="pill-tag active" onclick="filterCategory('')"><i class="fa-solid fa-ticket me-1"></i> All Events</span>
            <span class="pill-tag" onclick="filterCategory('Concert')"><i class="fa-solid fa-music me-1"></i> Concert</span>
            <span class="pill-tag" onclick="filterCategory('Tech')"><i class="fa-solid fa-microchip me-1"></i> Tech Conf</span>
            <span class="pill-tag" onclick="filterCategory('Charity')"><i class="fa-solid fa-hand-holding-heart me-1"></i> Charity</span>
            <span class="pill-tag" onclick="filterCategory('Futsal')"><i class="fa-solid fa-futbol me-1"></i> Futsal</span>
            <span class="pill-tag" onclick="filterCategory('Festival')"><i class="fa-solid fa-masks-theater me-1"></i> Festival</span>
        </div>
    </div>

    <!-- Main Container Grid -->
    <div class="container">
        <div id="mainContainer">
            <div id="eventsGrid" class="events-grid"></div>
        </div>
    </div>

    <!-- Booking Modal -->
    <div class="modal fade" id="bookingModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-dark p-3">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" style="color: var(--neon-lime)"><i class="fa-solid fa-ticket me-2"></i>Checkout & Booking</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="modalEventId">
                    <h4 id="modalEventTitle" class="fw-bold text-light mb-3"></h4>
                    
                    <div class="mb-3">
                        <label class="form-label text-secondary small">Quantity</label>
                        <input type="number" id="modalQty" class="form-control bg-dark text-light border-secondary" value="1" min="1" max="10" oninput="calculateTotal()">
                    </div>

                    <div class="p-3 mb-3 rounded-3" style="background-color: rgba(255,255,255,0.05);">
                        <span class="text-secondary small d-block">Total Amount</span>
                        <span id="modalTotal" class="fs-3 fw-bold" style="color: var(--neon-lime)">RM 0.00</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small">Payment Method</label>
                        <select id="paymentMethod" class="form-select bg-dark text-light border-secondary">
                            <option value="Online Banking">Online Banking / FPX</option>
                            <option value="Touch 'n Go eWallet">Touch 'n Go eWallet</option>
                            <option value="Credit Card">Credit / Debit Card</option>
                        </select>
                    </div>
                    <div id="qrResult"></div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" id="payBtn" class="btn btn-lime w-100 py-3" onclick="submitPaymentAndBook()">Pay Now & Get Ticket QR</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Ticket Detail Inspection Modal -->
    <div class="modal fade" id="ticketModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-dark p-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-light"><i class="fa-solid fa-ticket me-2 text-warning"></i>E-Ticket Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center" id="ticketModalBody">
                    <!-- Populated dynamically by JS -->
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-lime w-100" onclick="window.print()"><i class="fa-solid fa-print me-2"></i>Print Ticket</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const token = localStorage.getItem('auth_token');
        const role = localStorage.getItem('user_role');
        const name = localStorage.getItem('user_name');
        let currentEventPrice = 0;
        let userBookingsHistory = [];

        if(!token) window.location.href = 'login.php';
        document.getElementById('userInfo').innerText = `${name || 'User'} (${role})`;
        
        // Navigation visibility rules
        if (role === 'customer') {
            document.getElementById('historyBtn').classList.remove('d-none');
        } else {
            document.getElementById('historyBtn').classList.add('d-none');
        }
        
        if (role === 'organiser') {
            document.getElementById('staffLink').classList.remove('d-none');
        } else {
            document.getElementById('staffLink').classList.add('d-none');
        }

        if (role === 'admin') {
            document.getElementById('adminLink').classList.remove('d-none');
        } else {
            document.getElementById('adminLink').classList.add('d-none');
        }

        /* Helper function to select distinct image based on event title/category */
        function getEventImage(item) {
            if (item.image_url && item.image_url.trim() !== '') {
                return `../../${item.image_url}`;
            }
            
            let title = (item.title || '').toLowerCase();
            if (title.includes('futsal') || title.includes('sport')) {
                return '../../images/futsal.jpg';
            } else if (title.includes('tech') || title.includes('conf') || title.includes('seminar')) {
                return '../../images/techconf.jpg';
            } else if (title.includes('charity')) {
                return '../../images/charity.jpg';
            } else {
                return '../../images/rockfest.jpg';
            }
        }

        function filterCategory(catName) {
            document.getElementById('searchInput').value = catName;
            loadEvents();
        }

        function loadEvents() {
            // Re-enable hero section if hidden
            document.getElementById('heroSection').style.display = 'block';
            
            let search = document.getElementById('searchInput').value;
            fetch(`../../backend/api_events.php?search=${encodeURIComponent(search)}`)
                .then(res => res.json())
                .then(res => {
                    let html = '';
                    if(res.status === 'success' && res.data.length > 0) {
                        res.data.forEach(ev => {
                            let actionElement = '';
                            if (role === 'customer') {
                                actionElement = `<button class="btn btn-lime btn-sm w-100 py-2" onclick="openBooking(${ev.event_id}, '${ev.title.replace(/'/g, "\\'")}', ${ev.ticket_price})">Book Ticket</button>`;
                            } else {
                                actionElement = `<span class="badge bg-secondary w-100 py-2">Staff View</span>`;
                            }

                            let cardImg = getEventImage(ev);

                            html += `
                                <div class="event-card">
                                    <div class="card-img-wrapper">
                                        <img src="${cardImg}" onerror="this.src='../../images/rockfest.jpg'">
                                        <span class="badge-id">#${ev.event_id}</span>
                                    </div>
                                    <div class="event-card-title">${ev.title}</div>
                                    <div class="event-card-venue"><i class="fa-solid fa-location-dot me-1 text-warning"></i>${ev.venue_name || 'Venue'} (${ev.location || ''})</div>
                                    <div class="event-card-desc">${ev.description || 'No description provided.'}</div>
                                    
                                    <div class="event-card-meta d-flex justify-content-between align-items-center">
                                        <span><i class="fa-regular fa-calendar me-1"></i>${ev.event_date}</span>
                                        <span><i class="fa-solid fa-ticket me-1"></i>${ev.available_tickets} left</span>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="text-secondary small">Price</span>
                                        <span class="fs-5 fw-bold" style="color: var(--neon-lime)">RM ${parseFloat(ev.ticket_price).toFixed(2)}</span>
                                    </div>

                                    ${actionElement}
                                </div>`;
                        });
                    } else {
                        html = '<p class="text-secondary text-center py-5 w-100">No events match your query.</p>';
                    }
                    document.getElementById('mainContainer').innerHTML = `<div id="eventsGrid" class="events-grid">${html}</div>`;
                });
        }

        function openBooking(id, title, price) {
            document.getElementById('modalEventId').value = id;
            document.getElementById('modalEventTitle').innerText = title;
            document.getElementById('modalQty').value = 1;
            currentEventPrice = price;
            calculateTotal();
            document.getElementById('qrResult').innerHTML = '';
            document.getElementById('payBtn').style.display = 'block';
            new bootstrap.Modal(document.getElementById('bookingModal')).show();
        }

        function calculateTotal() {
            let qty = document.getElementById('modalQty').value;
            let total = qty * currentEventPrice;
            document.getElementById('modalTotal').innerText = `RM ${total.toFixed(2)}`;
        }

        function submitPaymentAndBook() {
            let eventId = document.getElementById('modalEventId').value;
            let qty = document.getElementById('modalQty').value;
            let method = document.getElementById('paymentMethod').value;

            fetch('../../backend/api_bookings.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                body: JSON.stringify({ event_id: parseInt(eventId), quantity: parseInt(qty), payment_method: method })
            })
            .then(async res => {
                let text = await res.text();
                try { return JSON.parse(text); } 
                catch(e) { throw new Error("Server Error: " + text); }
            })
            .then(data => {
                if(data.status === 'success') {
                    document.getElementById('qrResult').innerHTML = `
                        <div class="alert alert-success bg-dark text-light border-success text-center mt-3 rounded-4 p-3">
                            <p class="mb-1 text-success fw-bold">Success! Receipt ID: #${data.booking_id} | Paid: RM ${parseFloat(data.total_amount).toFixed(2)}</p>
                            <img src="${data.ticket_qr_code}" class="img-fluid bg-white p-2 rounded-3 mt-2" style="width: 130px;">
                            <p class="small text-muted mt-2 mb-0">Present this QR code at event entrance.</p>
                        </div>`;
                    document.getElementById('payBtn').style.display = 'none';
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(err => alert(err.message));
        }

        function loadHistory() {
            // Hide hero banner when viewing history
            document.getElementById('heroSection').style.display = 'none';

            fetch('../../backend/api_bookings.php', {
                headers: { 'Authorization': 'Bearer ' + token }
            })
            .then(res => res.json())
            .then(res => {
                if(res.status === 'success') {
                    userBookingsHistory = res.data || [];
                    renderHistoryUI(userBookingsHistory);
                }
            });
        }

        function renderHistoryUI(bookings) {
            let html = `
                <div class="history-container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="fw-bold text-light mb-0">My Bookings & Ticket History</h2>
                        <button class="btn btn-outline-dark-custom" onclick="loadEvents()"><i class="fa-solid fa-arrow-left me-2"></i>Back to Events</button>
                    </div>

                    <!-- Filters Header -->
                    <div class="history-filter-card">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label text-secondary small mb-1">Status</label>
                                <select id="statusFilter" class="form-select filter-select" onchange="applyHistoryFilters()">
                                    <option value="all">All Statuses</option>
                                    <option value="paid">Paid / Confirmed</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-secondary small mb-1">Order Date</label>
                                <select id="dateFilter" class="form-select filter-select" onchange="applyHistoryFilters()">
                                    <option value="all">Past orders</option>
                                    <option value="3months">Last 3 months</option>
                                    <option value="year">This year</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-secondary small mb-1">Search</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-secondary text-secondary"><i class="fa-solid fa-magnifying-glass"></i></span>
                                    <input type="text" id="historySearch" class="form-control filter-select" placeholder="Event name or order ID" onkeyup="applyHistoryFilters()">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bookings List -->
                    <div id="historyListContainer">`;

            if(bookings.length > 0) {
                bookings.forEach(item => {
                    let orderId = `#EH-${item.title.substring(0,4).toUpperCase()}-${String(item.booking_id).padStart(2, '0')}`;
                    let qrData = encodeURIComponent(`Event: ${item.title} | BookingID: ${item.booking_id} | Qty: ${item.quantity} | Status: ${item.payment_status}`);
                    let qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=${qrData}`;
                    
                    // Dynamic image assignment
                    let imgSrc = getEventImage(item);

                    // Action buttons / status indicators
                    let actionControls = '';
                    let statusStr = (item.payment_status || 'paid').toLowerCase();

                    if(statusStr === 'paid' || statusStr === 'confirmed' || statusStr === 'success') {
                        actionControls = `
                            <button class="btn btn-emerald w-100 mb-2" onclick="viewTicketDetails('${item.title.replace(/'/g, "\\'")}', '${orderId}', '${item.event_date}', '${item.quantity}', '${item.total_amount}', '${qrUrl}')">View Details</button>
                            <a href="${qrUrl}" download="Ticket-${item.booking_id}.png" target="_blank" class="btn btn-outline-ticket w-100 text-center text-decoration-none">Download E-Ticket</a>`;
                    } else if(statusStr === 'completed') {
                        actionControls = `<span class="badge bg-primary px-4 py-2 fs-6 rounded-pill">Completed</span>`;
                    } else if(statusStr === 'cancelled') {
                        actionControls = `<span class="badge bg-danger px-4 py-2 fs-6 rounded-pill">Cancelled</span>`;
                    } else {
                        actionControls = `<span class="badge bg-secondary px-4 py-2 fs-6 rounded-pill">${item.payment_status}</span>`;
                    }

                    html += `
                        <div class="history-item-card d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
                            <!-- Left: Image & Event Meta -->
                            <div class="d-flex align-items-center gap-3">
                                <img src="${imgSrc}" class="history-img-thumb" onerror="this.src='../../images/rockfest.jpg'">
                                <div>
                                    <h4 class="fw-bold text-light mb-1">${item.title}</h4>
                                    <p class="text-secondary small mb-1"><span class="text-light">Date:</span> ${item.event_date}</p>
                                    <p class="text-secondary small mb-1"><span class="text-light">Venue:</span> ${item.venue_name || 'Stadium X, City Z'}</p>
                                    <p class="text-secondary small mb-0"><span class="text-light">Order ID:</span> <span style="color: var(--neon-lime)">${orderId}</span></p>
                                </div>
                            </div>

                            <!-- Middle: Ticket info & Paid -->
                            <div class="text-md-start">
                                <p class="text-secondary small mb-1"><strong class="text-light">Tickets:</strong></p>
                                <p class="fw-semibold text-light mb-1">${item.quantity}x General Admission</p>
                                <p class="text-secondary small mb-1">Paid: <strong style="color: var(--neon-lime)">RM ${parseFloat(item.total_amount).toFixed(2)}</strong></p>
                                <p class="text-secondary small mb-0">Method: ${item.method || 'Card'}</p>
                            </div>

                            <!-- Right: Action Buttons -->
                            <div class="d-flex align-items-center justify-content-end" style="min-width: 160px;">
                                <div class="w-100 text-end">
                                    ${actionControls}
                                </div>
                            </div>
                        </div>`;
                });
            } else {
                html += '<p class="text-secondary text-center py-5">No booking records found.</p>';
            }

            html += `</div></div>`;
            document.getElementById('mainContainer').innerHTML = html;
        }

        /* Filter logic for History page */
        function applyHistoryFilters() {
            let status = document.getElementById('statusFilter').value;
            let query = document.getElementById('historySearch').value.toLowerCase();

            let filtered = userBookingsHistory.filter(item => {
                let matchesStatus = (status === 'all') || 
                                    (status === 'paid' && (item.payment_status === 'paid' || item.payment_status === 'confirmed' || item.payment_status === 'success')) ||
                                    (item.payment_status && item.payment_status.toLowerCase() === status);

                let matchesSearch = item.title.toLowerCase().includes(query) || 
                                    String(item.booking_id).includes(query);

                return matchesStatus && matchesSearch;
            });

            renderHistoryUI(filtered);
        }

        /* View Detailed E-Ticket Modal */
        function viewTicketDetails(title, orderId, date, qty, total, qrUrl) {
            document.getElementById('ticketModalBody').innerHTML = `
                <div class="bg-white text-dark p-4 rounded-4 mb-3">
                    <h3 class="fw-bold mb-1">${title}</h3>
                    <p class="text-muted small mb-3">${orderId}</p>
                    <hr>
                    <div class="d-flex justify-content-between small text-start mb-2">
                        <span>Event Date:</span> <strong>${date}</strong>
                    </div>
                    <div class="d-flex justify-content-between small text-start mb-2">
                        <span>Quantity:</span> <strong>${qty} Ticket(s)</strong>
                    </div>
                    <div class="d-flex justify-content-between small text-start mb-3">
                        <span>Total Paid:</span> <strong class="text-success fs-5">RM ${parseFloat(total).toFixed(2)}</strong>
                    </div>
                    <img src="${qrUrl}" class="img-fluid rounded-3 border p-2" style="width: 170px;">
                    <p class="small text-muted mt-2 mb-0">Present this QR code at venue gate for entry.</p>
                </div>`;
            new bootstrap.Modal(document.getElementById('ticketModal')).show();
        }

        loadEvents();
    </script>
</body>
</html>