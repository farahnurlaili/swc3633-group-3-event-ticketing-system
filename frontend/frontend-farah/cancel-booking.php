<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$booking_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];

if ($booking_id) {
    // Verify ownership of the booking
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE booking_id = ? AND user_id = ?");
    $stmt->execute([$booking_id, $user_id]);
    $booking = $stmt->fetch();

    if ($booking && $booking['status'] !== 'cancelled') {
        $pdo->beginTransaction();
        try {
            // Update booking status to cancelled
            $updateBooking = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE booking_id = ?");
            $updateBooking->execute([$booking_id]);

            // Return tickets back to the event pool
            $returnTickets = $pdo->prepare("UPDATE events SET available_tickets = available_tickets + ? WHERE event_id = ?");
            $returnTickets->execute([$booking['quantity'], $booking['event_id']]);

            // Update payment status if exists
            $updatePayment = $pdo->prepare("UPDATE payments SET status = 'refunded' WHERE booking_id = ?");
            $updatePayment->execute([$booking_id]);

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
        }
    }
}

header("Location: my-bookings.php");
exit();