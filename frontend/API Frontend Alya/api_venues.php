<?php
header('Content-Type: application/json');
require_once 'db.php';
require_once 'auth_middleware.php';
$user = verify_token();
if (!$user || strtolower($user['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['status'=>'error','message'=>'Administrator access required']);
    exit;
}
$stmt = $pdo->query('SELECT venue_id, venue_name, address, city, capacity FROM venues ORDER BY venue_id');
echo json_encode(['status'=>'success','data'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
