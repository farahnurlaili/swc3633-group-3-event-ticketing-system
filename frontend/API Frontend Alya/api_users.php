<?php
header('Content-Type: application/json');
require_once 'db.php';
require_once 'auth_middleware.php';

$user = verify_token();
if (!$user || strtolower($user['role']) !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized. Administrator access required.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->query("SELECT user_id, full_name, email, role, created_at FROM users ORDER BY user_id ASC");
    $users = $stmt->fetchAll();
    echo json_encode(['status' => 'success', 'data' => $users]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if ($method === 'PUT') {
    $userId = intval($data['user_id'] ?? 0);
    $newRole = trim($data['role'] ?? '');

    if ($userId <= 0 || !in_array($newRole, ['admin', 'organiser', 'customer'], true)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid user ID or role.']);
        exit;
    }

    if ($userId === (int)$user['user_id'] && $newRole !== 'admin') { http_response_code(403); echo json_encode(['status'=>'error','message'=>'Cannot demote your own administrator account.']); exit; }
    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE user_id = ?");
    if ($stmt->execute([$newRole, $userId])) {
        echo json_encode(['status' => 'success', 'message' => 'User role updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update user role.']);
    }
    exit;
}

if ($method === 'DELETE') {
    $userId = intval($_GET['user_id'] ?? 0);

    if ($userId === intval($user['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'You cannot delete your own active administrator account!']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
    if ($stmt->execute([$userId])) {
        echo json_encode(['status' => 'success', 'message' => 'User removed successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete user.']);
    }
    exit;
}