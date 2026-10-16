<?php
header('Content-Type: application/json');
session_start();
require_once '../config/db.php';

$action = $_GET['action'] ?? '';

if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'role' => $user['role'],
                'user' => [
                    'id' => $user['user_id'],
                    'name' => $user['full_name'],
                    'email' => $user['email']
                ]
            ]);
            exit();
        }
    }
    echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
    exit();
}

if ($action === 'logout') {
    session_destroy();
    echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid endpoint action']);