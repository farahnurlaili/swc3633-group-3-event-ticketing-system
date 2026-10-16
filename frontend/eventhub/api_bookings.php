<?php
error_reporting(0);
ini_set('display_errors', 0);

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Database Connection
$host = 'localhost';
$db   = 'event_ticketing';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

// Bearer Token Extractor
function getBearerToken() {
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } else if (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    if (!empty($headers) && preg_match('/Bearer\s(\S+)/i', $headers, $matches)) {
        return $matches[1];
    }
    return null;
}

$token = getBearerToken();
if (!$token) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized. Token missing.']);
    exit;
}

$decodedJson = base64_decode($token);
$currentUser = json_decode($decodedJson, true);

if (!$currentUser || !isset($currentUser['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or expired token.']);
    exit;
}

// Handle POST Booking Request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $action = $input['action'] ?? '';

    if ($action === 'create_booking') {
        $eventId  = intval($input['event_id'] ?? 0);
        $quantity = intval($input['quantity'] ?? 1);

        if ($eventId <= 0 || $quantity <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid event or quantity selection.']);
            exit;
        }

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT ticket_price, available_tickets FROM events WHERE event_id = ? FOR UPDATE");
            $stmt->execute([$eventId]);
            $event = $stmt->fetch();

            if (!$event || $event['available_tickets'] < $quantity) {
                $pdo->rollBack();
                echo json_encode(['status' => 'error', 'message' => 'Not enough tickets available.']);
                exit;
            }

            $totalAmount = $event['ticket_price'] * $quantity;

            $bookStmt = $pdo->prepare("INSERT INTO bookings (user_id, event_id, quantity, total_price, status) VALUES (?, ?, ?, ?, 'confirmed')");
            $bookStmt->execute([$currentUser['user_id'], $eventId, $quantity, $totalAmount]);
            $bookingId = $pdo->lastInsertId();

            $updateStmt = $pdo->prepare("UPDATE events SET available_tickets = available_tickets - ? WHERE event_id = ?");
            $updateStmt->execute([$quantity, $eventId]);

            $qrData = "TICKET-BK{$bookingId}-U{$currentUser['user_id']}-E{$eventId}";
            $qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($qrData);

            $pdo->commit();

            echo json_encode([
                'status'     => 'success',
                'message'    => 'Booking confirmed successfully!',
                'booking_id' => $bookingId,
                'qr_code'    => $qrData,
                'qr_url'     => $qrApiUrl
            ]);
            exit;

        } catch (\Exception $e) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
            exit;
        }
    }
}

// Handle GET Requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    // 1. Admin Analytics Mode (?analytics=1)
    if (isset($_GET['analytics'])) {
        try {
            $sumStmt = $pdo->query("SELECT COUNT(*) AS total_confirmed_bookings, COALESCE(SUM(total_price), 0) AS total_revenue FROM bookings WHERE status = 'confirmed'");
            $summary = $sumStmt->fetch();

            $perfStmt = $pdo->query("
                SELECT e.event_id, e.title, e.event_date,
                       COALESCE(SUM(b.quantity), 0) AS booked_seats,
                       (e.available_tickets + COALESCE(SUM(b.quantity), 0)) AS total_tickets,
                       COALESCE(SUM(b.total_price), 0) AS gross_sales
                FROM events e
                LEFT JOIN bookings b ON e.event_id = b.event_id AND b.status = 'confirmed'
                GROUP BY e.event_id
                ORDER BY e.event_id DESC
            ");
            $events = $perfStmt->fetchAll();

            foreach ($events as &$e) {
                $total = intval($e['total_tickets']);
                $booked = intval($e['booked_seats']);
                $e['fill_rate'] = ($total > 0) ? round(($booked / $total) * 100, 1) : 0;
            }

            echo json_encode([
                'status' => 'success',
                'summary' => $summary,
                'event_performance' => $events
            ]);
            exit;
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Analytics query error: ' . $e->getMessage()]);
            exit;
        }
    }

    // 2. Standard User Bookings List (b.created_at removed)
    try {
        $stmt = $pdo->prepare("
            SELECT b.booking_id, b.quantity, b.total_price, b.status,
                   e.title AS event_title, e.event_date,
                   v.venue_name
            FROM bookings b
            JOIN events e ON b.event_id = e.event_id
            LEFT JOIN venues v ON e.venue_id = v.venue_id
            WHERE b.user_id = ?
            ORDER BY b.booking_id DESC
        ");
        $stmt->execute([$currentUser['user_id']]);
        $bookings = $stmt->fetchAll();

        foreach ($bookings as &$b) {
            $qrPayload = "TICKET-BK{$b['booking_id']}-U{$currentUser['user_id']}";
            $b['qr_url'] = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($qrPayload);
        }

        echo json_encode(['status' => 'success', 'data' => $bookings]);
        exit;
    } catch (\Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Query error: ' . $e->getMessage()]);
        exit;
    }
}