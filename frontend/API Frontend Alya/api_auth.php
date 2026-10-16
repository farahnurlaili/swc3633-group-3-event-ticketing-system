<?php
header('Content-Type: application/json');
require_once 'db.php';
require_once 'auth_middleware.php';

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

if ($method === 'POST') {
    if (isset($data['action']) && $data['action'] === 'register') {
        $fullName = trim($data['full_name'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        $phone = preg_replace('/[\\s-]+/', '', trim((string)($data['phone'] ?? '')));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
            http_response_code(422); echo json_encode(['status'=>'error','message'=>'Enter a valid email and password of at least 8 characters.']); exit;
        }

        if (!preg_match('/^0[0-9]{9,10}$/', $phone)) {
            http_response_code(422);
            echo json_encode(['status'=>'error','message'=>'Enter a valid Malaysian phone number (10 or 11 digits starting with 0).']);
            exit;
        }
        if (empty($fullName) || empty($email) || empty($password)) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
            exit;
        }

        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            echo json_encode(['status' => 'error', 'message' => 'Email is already registered.']);
            exit;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, 'customer')");
        if ($stmt->execute([$fullName, $email, $phone, $passwordHash])) {
            echo json_encode(['status' => 'success', 'message' => 'Registration successful.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Registration failed.']);
        }
        exit;
    }

    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Email and password are required.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'Email not found in database.']);
        exit;
    }

    $isValid = password_verify($password, $user['password_hash']);
    


    if ($isValid) {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['user_id'];
        $token = generate_token($user);
        echo json_encode([
            'status' => 'success',
            'token' => $token,
            'role' => $user['role'],
            'full_name' => $user['full_name']
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid password.']);
    }
}