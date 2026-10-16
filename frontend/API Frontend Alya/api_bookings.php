<?php
header('Content-Type: application/json');
require_once 'db.php';
require_once 'auth_middleware.php';

$user = verify_token();
if (!$user) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET' && isset($_GET['analytics']) && $user['role'] === 'admin') {
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM bookings WHERE status = 'confirmed'");
    $totalBookings = $stmt->fetch()['total'] ?? 0;

    $stmt = $pdo->query("SELECT SUM(amount) AS revenue FROM payments WHERE status = 'paid'");
    $totalRevenue = $stmt->fetch()['revenue'] ?? 0.00;

    $stmt = $pdo->query("
        SELECT e.event_id, e.title, e.event_date, e.total_tickets,
               (e.total_tickets - e.available_tickets) AS booked_seats,
               COALESCE(SUM(CASE WHEN p.status = 'paid' THEN p.amount ELSE 0 END), 0) AS gross_sales
        FROM events e
        LEFT JOIN bookings b ON e.event_id = b.event_id AND b.status = 'confirmed'
        LEFT JOIN payments p ON b.booking_id = p.booking_id
        GROUP BY e.event_id
    ");
    $events = $stmt->fetchAll();

    $performance = [];
    foreach ($events as $ev) {
        $fillRate = $ev['total_tickets'] > 0 ? round(($ev['booked_seats'] / $ev['total_tickets']) * 100) : 0;
        $performance[] = [
            'event_id' => $ev['event_id'],
            'title' => $ev['title'],
            'event_date' => $ev['event_date'],
            'total_tickets' => $ev['total_tickets'],
            'booked_seats' => $ev['booked_seats'],
            'fill_rate' => $fillRate,
            'gross_sales' => $ev['gross_sales']
        ];
    }

    echo json_encode([
        'status' => 'success',
        'summary' => [
            'total_confirmed_bookings' => $totalBookings,
            'total_revenue' => $totalRevenue,
            'attendance_rate' => 100,
            'waitlist_count' => 0
        ],
        'event_performance' => $performance
    ]);
    exit;
}

if ($method === 'GET' && isset($_GET['event_buyers']) && $user['role'] === 'admin') {
    $eventId = $_GET['event_buyers'];
    $stmt = $pdo->prepare("
        SELECT b.booking_id, u.full_name, u.email, u.phone, b.quantity, p.amount AS total_amount, p.method
        FROM bookings b
        JOIN users u ON b.user_id = u.user_id
        LEFT JOIN payments p ON b.booking_id = p.booking_id
        WHERE b.event_id = ?
    ");
    $stmt->execute([$eventId]);
    $buyers = $stmt->fetchAll();

    echo json_encode(['status' => 'success', 'data' => $buyers]);
    exit;
}

if ($method === 'GET') {
    if ($user['role'] === 'customer') {
        $stmt = $pdo->prepare("
            SELECT b.booking_id, e.title, e.event_date, b.quantity, b.total_price AS total_amount, 
                   COALESCE(p.method, 'Online Banking') AS method, COALESCE(p.status, 'pending') AS payment_status
            FROM bookings b
            JOIN events e ON b.event_id = e.event_id
            LEFT JOIN payments p ON b.booking_id = p.booking_id
            WHERE b.user_id = ?
        ");
        $stmt->execute([$user['user_id']]);
    } elseif ($user['role'] === 'organiser') {
        $stmt = $pdo->prepare("
            SELECT b.booking_id, e.title, e.event_date, b.quantity, b.total_price AS total_amount,
                   COALESCE(p.method, 'Online Banking') AS method, COALESCE(p.status, 'pending') AS payment_status
            FROM bookings b JOIN events e ON b.event_id = e.event_id
            LEFT JOIN payments p ON b.booking_id = p.booking_id WHERE e.organiser_id = ?
        " );
        $stmt->execute([$user['user_id']]);
    } else {
        $stmt = $pdo->query("
            SELECT b.booking_id, e.title, e.event_date, b.quantity, b.total_price AS total_amount, 
                   COALESCE(p.method, 'Online Banking') AS method, COALESCE(p.status, 'pending') AS payment_status
            FROM bookings b
            JOIN events e ON b.event_id = e.event_id
            LEFT JOIN payments p ON b.booking_id = p.booking_id
        ");
    }
    $bookings = $stmt->fetchAll();
    echo json_encode(['status' => 'success', 'data' => $bookings]);
    exit;
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if ($user['role'] !== 'customer') { http_response_code(403); echo json_encode(['status'=>'error','message'=>'Customer account required.']); exit; }
    $eventId = filter_var($data['event_id'] ?? null, FILTER_VALIDATE_INT);
    $quantity = filter_var($data['quantity'] ?? null, FILTER_VALIDATE_INT);
    if (!$eventId || !$quantity || $quantity < 1 || $quantity > 20) { http_response_code(422); echo json_encode(['status'=>'error','message'=>'Invalid event or ticket quantity (1-20).']); exit; }
    $methodName = $data['payment_method'] ?? 'Online Banking';

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("SELECT ticket_price, available_tickets FROM events WHERE event_id = ? AND status = 'published' FOR UPDATE");
        $stmt->execute([$eventId]);
        $event = $stmt->fetch();
        if (!$event || $event['available_tickets'] < $quantity) {
            if ($pdo->inTransaction()) $pdo->rollBack(); http_response_code(409); echo json_encode(['status'=>'error','message'=>'Event unavailable or not enough tickets.']); exit;
        }
        $totalPrice = $event['ticket_price'] * $quantity;

        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, event_id, quantity, total_price, status) VALUES (?, ?, ?, ?, 'confirmed')");
        $stmt->execute([$user['user_id'], $eventId, $quantity, $totalPrice]);
        $bookingId = $pdo->lastInsertId();

        $stmt = $pdo->prepare("UPDATE events SET available_tickets = available_tickets - ? WHERE event_id = ?");
        $stmt->execute([$quantity, $eventId]);

        $dbMethod = 'online_banking';
        if (stripos($methodName, 'Card') !== false) $dbMethod = 'card';
        if (stripos($methodName, 'Wallet') !== false) $dbMethod = 'ewallet';

        $txnRef = 'TXN-' . date('Ymd') . '-' . str_pad($bookingId, 4, '0', STR_PAD_LEFT);
        $stmt = $pdo->prepare("INSERT INTO payments (booking_id, amount, method, status, transaction_ref, paid_at) VALUES (?, ?, ?, 'paid', ?, NOW())");
        $stmt->execute([$bookingId, $totalPrice, $dbMethod, $txnRef]);

        $pdo->commit();

        $qrData = urlencode("BookingID: #{$bookingId} | User: {$user['full_name']} | Qty: {$quantity} | Total: RM{$totalPrice}");
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=140x140&data={$qrData}";

        echo json_encode([
            'status' => 'success',
            'booking_id' => $bookingId,
            'total_amount' => $totalPrice,
            'ticket_qr_code' => $qrUrl
        ]);

    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Booking could not be completed. Please try again.']);
    }
    exit;
}