<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'db.php';
require_once 'auth_middleware.php';
$user = verify_token();
if (!$user) { http_response_code(401); echo json_encode(['status'=>'error','message'=>'Login required']); exit; }
$role = $user['role'];
$id = (int)$user['user_id'];
if ($role === 'organiser') {
    $stmt=$pdo->prepare('SELECT COUNT(*) FROM events WHERE organiser_id=?');
    $stmt->execute([$id]); $managed=(int)$stmt->fetchColumn();
    $stmt=$pdo->prepare("SELECT COALESCE(SUM(b.quantity),0) FROM bookings b JOIN events e ON b.event_id=e.event_id WHERE e.organiser_id=? AND b.status='confirmed'");
    $stmt->execute([$id]); $sold=(int)$stmt->fetchColumn();
    $totalEvents=(int)$pdo->query('SELECT COUNT(*) FROM events')->fetchColumn();
    $venues=(int)$pdo->query('SELECT COUNT(*) FROM venues')->fetchColumn();
    echo json_encode(['status'=>'success','data'=>['full_name'=>$user['full_name'],'managed_events'=>$managed,'total_events'=>$totalEvents,'tickets_sold'=>$sold,'active_venues'=>$venues]]);
} elseif ($role === 'customer') {
    $stmt=$pdo->prepare("SELECT COUNT(*) FROM bookings WHERE user_id=? AND status IN ('pending','confirmed')");
    $stmt->execute([$id]); $active=(int)$stmt->fetchColumn();
    $stmt=$pdo->prepare("SELECT COUNT(*) FROM bookings WHERE user_id=? AND status='confirmed'");
    $stmt->execute([$id]); $passes=(int)$stmt->fetchColumn();
    $upcoming=(int)$pdo->query("SELECT COUNT(*) FROM events WHERE status='published' AND event_date>=CURRENT_DATE()")->fetchColumn();
    $totalEvents=(int)$pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
    echo json_encode(['status'=>'success','data'=>['full_name'=>$user['full_name'],'active_bookings'=>$active,'upcoming_events'=>$upcoming,'total_events'=>$totalEvents,'qr_passes'=>$passes]]);
} else {
    http_response_code(403); echo json_encode(['status'=>'error','message'=>'Unsupported dashboard role']);
}
