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
        1 => 'assets/images/rockfest.jpg',
        2 => 'assets/images/techsummit.jpg',
        3 => 'assets/images/futsal.jpg',
        4 => 'assets/images/seniwarisan.jpg',
        5 => 'assets/images/comedy.jpg',
        6 => 'assets/images/pitchday.jpg'
    ];
    return $defaults[$eventId] ?? 'assets/images/default.jpg';
}

function save_custom_image($eventId, $imageUrl, $imageMapFile) {
    $customImages = [];
    if (file_exists($imageMapFile)) {
        $customImages = json_decode(file_get_contents($imageMapFile), true) ?? [];
    }
    if (!empty($imageUrl)) {
        $customImages[$eventId] = $imageUrl;
        file_put_contents($imageMapFile, json_encode($customImages, JSON_PRETTY_PRINT));
    }
}

if ($method === 'GET' && isset($_GET['scope'])) {
    $viewer = verify_token();
    if (!$viewer || !in_array($viewer['role'], ['organiser','admin'], true)) {
        http_response_code(401);
        echo json_encode(['status'=>'error','message'=>'Authentication required']); exit;
    }
    $scope = $_GET['scope'];
    if (!in_array($scope, ['mine','all'], true)) { http_response_code(400); echo json_encode(['status'=>'error','message'=>'Invalid scope']); exit; }
    $sql = "SELECT e.*,v.venue_name,v.city AS location,c.category_name,u.full_name AS organiser_name FROM events e JOIN venues v ON e.venue_id=v.venue_id JOIN categories c ON e.category_id=c.category_id JOIN users u ON e.organiser_id=u.user_id";
    if ($scope === 'mine' && $viewer['role'] !== 'admin') {
        $sql .= ' WHERE e.organiser_id=?';
        $stmt=$pdo->prepare($sql.' ORDER BY e.event_date DESC'); $stmt->execute([(int)$viewer['user_id']]);
    } else { $stmt=$pdo->query($sql.' ORDER BY e.event_date DESC'); }
    $events=$stmt->fetchAll();
    foreach ($events as &$ev) { $ev['image_url']=get_dynamic_image($ev['event_id'],$imageMapFile); }
    echo json_encode(['status'=>'success','data'=>$events]); exit;
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
    $imageUrl = trim($data['image_url'] ?? 'assets/images/default.jpg');
    $venueId = $data['venue_id'] ?? 1;
    $catId = $data['category_id'] ?? 1;
    $date = $data['event_date'] ?? date('Y-m-d');
    $startTime = $data['start_time'] ?? '09:00:00';
    $endTime = $data['end_time'] ?? '17:00:00';
    $price = $data['ticket_price'] ?? 0.00;
    $totalTickets = $data['total_tickets'] ?? 100;
    $requestedStatus = $data['status'] ?? 'draft';
    $status = ['active'=>'published','published'=>'published','ended'=>'cancelled','cancelled'=>'cancelled','draft'=>'draft'][$requestedStatus] ?? 'draft';

    $stmt = $pdo->prepare("INSERT INTO events (title, description, venue_id, category_id, organiser_id, event_date, start_time, end_time, ticket_price, total_tickets, available_tickets, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$title, $desc, $venueId, $catId, $user['user_id'], $date, $startTime, $endTime, $price, $totalTickets, $totalTickets, $status])) {
        $newEveId = $pdo->lastInsertId();
        save_custom_image($newEveId, $imageUrl, $imageMapFile);
        echo json_encode(['status' => 'success', 'message' => 'Event published successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to publish event.']);
    }
    exit;
}

if ($method === 'PUT') {
    $eventId = $data['event_id'] ?? 0;
    $title = $data['title'] ?? '';
    $desc = $data['description'] ?? '';
    $imageUrl = trim($data['image_url'] ?? '');
    $venueId = $data['venue_id'] ?? 1;
    $catId = $data['category_id'] ?? 1;
    $date = $data['event_date'] ?? '';
    $startTime = $data['start_time'] ?? '';
    $endTime = $data['end_time'] ?? '';
    $price = $data['ticket_price'] ?? 0;
    $totalTickets = $data['total_tickets'] ?? 100;
    $requestedStatus = $data['status'] ?? 'draft';
    $status = ['active'=>'published','published'=>'published','ended'=>'cancelled','cancelled'=>'cancelled','draft'=>'draft'][$requestedStatus] ?? 'draft';

    $owner = $pdo->prepare('SELECT organiser_id, total_tickets, available_tickets FROM events WHERE event_id = ?');
    $owner->execute([$eventId]);
    $existing = $owner->fetch();
    if (!$existing || ($user['role'] !== 'admin' && (int)$existing['organiser_id'] !== (int)$user['user_id'])) { http_response_code(403); echo json_encode(['status'=>'error','message'=>'Event not found or not owned by you.']); exit; }
    $sold = (int)$existing['total_tickets'] - (int)$existing['available_tickets'];
    if (!filter_var($totalTickets, FILTER_VALIDATE_INT) || $totalTickets < $sold) { http_response_code(422); echo json_encode(['status'=>'error','message'=>'Total tickets cannot be less than tickets already sold.']); exit; }
    $stmt = $pdo->prepare("UPDATE events SET title = ?, description = ?, venue_id = ?, category_id = ?, event_date = ?, start_time = ?, end_time = ?, ticket_price = ?, total_tickets = ?, available_tickets = ?, status = ? WHERE event_id = ?");
    
    if ($stmt->execute([$title, $desc, $venueId, $catId, $date, $startTime, $endTime, $price, $totalTickets, $totalTickets - $sold, $status, $eventId])) {
        if (!empty($imageUrl)) {
            save_custom_image($eventId, $imageUrl, $imageMapFile);
        }
        echo json_encode(['status' => 'success', 'message' => 'Event updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update event.']);
    }
    exit;
}

if ($method === 'DELETE') {


    $eventId = (int)($_GET['event_id'] ?? 0);
    $ownerCheck=$pdo->prepare('SELECT organiser_id FROM events WHERE event_id=?');
    $ownerCheck->execute([$eventId]); $ownerId=$ownerCheck->fetchColumn();
    if ($ownerId === false) { http_response_code(404); echo json_encode(['status'=>'error','message'=>'Event not found']); exit; }
    if ($user['role'] !== 'admin' && (int)$ownerId !== (int)$user['user_id']) { http_response_code(403); echo json_encode(['status'=>'error','message'=>'You can only delete your own events']); exit; }
    // Preserve customer bookings and payment history. Only empty events can be deleted.
    $count = $pdo->prepare('SELECT COUNT(*) FROM bookings WHERE event_id = ?');
    $count->execute([$eventId]);
    if ((int)$count->fetchColumn() > 0) {
        http_response_code(409);
        echo json_encode(['status'=>'error','message'=>'This event has bookings and cannot be deleted. Cancel it instead.']);
        exit;
    }
    
    $stmt = $pdo->prepare("DELETE FROM events WHERE event_id = ?");
    if ($stmt->execute([$eventId])) {
        if (file_exists($imageMapFile)) {
            $customImages = json_decode(file_get_contents($imageMapFile), true) ?? [];
            unset($customImages[$eventId]);
            file_put_contents($imageMapFile, json_encode($customImages, JSON_PRETTY_PRINT));
        }
        echo json_encode(['status' => 'success', 'message' => 'Event deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete event.']);
    }
    exit;
}