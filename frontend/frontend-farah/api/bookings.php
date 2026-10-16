<?php
header('Content-Type: application/json');
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Please login.']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $event_id = intval($data['event_id'] ?? 0);
    $quantity = intval($data['quantity'] ?? 0);
    $user_id = $_SESSION['user_id'];

    if ($event_id <= 0 || $quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid booking details']);
        exit();
    }

    $pdo->beginTransaction();
    try {
        // Verify available inventory
        $stmt = $pdo->prepare("SELECT ticket_price, available_tickets FROM events WHERE event_id = ? FOR UPDATE");
        $stmt->execute([$event_id]);
        $event = $stmt->fetch();

        if (!$event || $event['available_tickets'] < $quantity) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Not enough tickets available']);
            exit();
        }

        $total_price = $event['ticket_price'] * $quantity;

        // Insert booking record
        $bookingStmt = $pdo->prepare("INSERT INTO bookings (user_id, event_id, quantity, total_price, status) VALUES (?, ?, ?, ?, 'confirmed')");
        $bookingStmt->execute([$user_id, $event_id, $quantity, $total_price]);
        $booking_id = $pdo->lastInsertId();

        // Update inventory
        $updateStmt = $pdo->prepare("UPDATE events SET available_tickets = available_tickets - ? WHERE event_id = ?");
        $updateStmt->execute([$quantity, $event_id]);

        // Create transaction record
        $txnRef = 'TXN-' . date('Ymd') . '-' . rand(1000, 9999);
        $paymentStmt = $pdo->prepare("INSERT INTO payments (booking_id, amount, method, transaction_ref, status) VALUES (?, ?, 'online_banking', ?, 'paid')");
        $paymentStmt->execute([$booking_id, $total_price, $txnRef]);

        $pdo->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Booking successfully confirmed!',
            'booking_id' => $booking_id,
            'total_price' => $total_price,
            'transaction_ref' => $txnRef
        ]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid request method']);