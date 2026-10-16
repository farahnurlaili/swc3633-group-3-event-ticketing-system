<?php
header('Content-Type: application/json');
require_once '../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $event_id = $_GET['id'] ?? null;
    $search = $_GET['search'] ?? '';

    if ($event_id) {
        $stmt = $pdo->prepare("
            SELECT e.*, v.venue_name, v.address, v.city, c.category_name 
            FROM events e
            JOIN venues v ON e.venue_id = v.venue_id
            JOIN categories c ON e.category_id = c.category_id
            WHERE e.event_id = ?
        ");
        $stmt->execute([$event_id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($event) {
            echo json_encode(['success' => true, 'data' => $event]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Event not found']);
        }
    } else {
        $sql = "SELECT e.*, v.venue_name, c.category_name 
                FROM events e
                JOIN venues v ON e.venue_id = v.venue_id
                JOIN categories c ON e.category_id = c.category_id
                WHERE e.status = 'published'";
        
        if (!empty($search)) {
            $sql .= " AND (e.title LIKE ? OR v.venue_name LIKE ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["%$search%", "%$search%"]);
        } else {
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
        }

        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'count' => count($events), 'data' => $events]);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Method not allowed']);