<?php
header('Content-Type: application/json');
require_once 'db.php';
require_once 'auth_middleware.php';

$method = $_SERVER['REQUEST_METHOD'];
$imageMapFile = __DIR__ . '/image_map.json';

function get_dynamic_image($eventId, $imageMapFile) {
    $customImages = [];
    if (file_exists($imageMapFile)) {
        $customImages = json_decode(file_get_contents($imageMapFile), true) ?? [];
    }
    if (isset($customImages[$eventId])) {
        return $customImages[$eventId];
    }
    $defaults = [
        1 => 'images/rockfest.jpg',
        2 => 'images/techsummit.jpg',
        3 => 'images/futsal.jpg',
        4 => 'images/seniwarisan.jpg',
        5 => 'images/comedy.jpg',
        6 => 'images/pitchday.jpg'
    ];
    return $defaults[$eventId] ?? 'images/default.jpg';
}

if ($method === 'GET') {
    $search = $_GET['search'] ?? '';
    $sql = "SELECT e.*, v.venue_name, v.city AS location, c.category_name 
            FROM events e
            JOIN venues v ON e.venue_id = v.venue_id
            JOIN categories c ON e.category_id = c.category_id";
    
    if (!empty($search)) {
        $sql .= " WHERE e.title LIKE ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["%$search%"]);
    } else {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }
    
    $events = $stmt->fetchAll();
    foreach ($events as &$ev) {
        $ev['image_url'] = get_dynamic_image($ev['event_id'], $imageMapFile);
        if ($ev['status'] === 'published') $ev['status'] = 'active';
        if ($ev['status'] === 'cancelled') $ev['status'] = 'ended';
    }

    echo json_encode(['status' => 'success', 'data' => $events]);
    exit;
}

$user = verify_token();
if (!$user || ($user['role'] !== 'organiser' && $user['role'] !== 'admin')) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if ($method === 'POST') {
    $title = $data['title'] ?? '';
    $desc = $data['description'] ?? '';
    $venueId = $data['venue_id'] ?? 1;
    $catId = $data['category_id'] ?? 1;
    $date = $data['event_date'] ?? date('Y-m-d');
    $startTime = $data['start_time'] ?? '09:00:00';
    $endTime = $data['end_time'] ?? '17:00:00';
    $price = $data['ticket_price'] ?? 0.00;
    $totalTickets = $data['total_tickets'] ?? 100;
    $status = ($data['status'] === 'active') ? 'published' : 'draft';

    $stmt = $pdo->prepare("INSERT INTO events (title, description, venue_id, category_id, organiser_id, event_date, start_time, end_time, ticket_price, total_tickets, available_tickets, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$title, $desc, $venueId, $catId, $user['user_id'], $date, $startTime, $endTime, $price, $totalTickets, $totalTickets, $status])) {
        echo json_encode(['status' => 'success', 'message' => 'Event published successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to publish event.']);
    }
    exit;
}

if ($method === 'DELETE') {
    if ($user['role'] !== 'admin') {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized: Only administrators are allowed to delete events.']);
        exit;
    }

    $eventId = $_GET['event_id'] ?? 0;
    
    $pdo->prepare("DELETE FROM payments WHERE booking_id IN (SELECT booking_id FROM bookings WHERE event_id = ?)")->execute([$eventId]);
    $pdo->prepare("DELETE FROM bookings WHERE event_id = ?")->execute([$eventId]);
    
    $stmt = $pdo->prepare("DELETE FROM events WHERE event_id = ?");
    if ($stmt->execute([$eventId])) {
        echo json_encode(['status' => 'success', 'message' => 'Event deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete event.']);
    }
    exit;
}