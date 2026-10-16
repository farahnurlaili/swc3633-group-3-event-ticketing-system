<?php
error_reporting(0);
ini_set('display_errors', 0);

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Database Connection
$host = 'localhost';
$db   = 'event_ticketing';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

// Bearer Token Extractor & Admin Verification
function getBearerToken() {
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } else if (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    if (!empty($headers) && preg_match('/Bearer\s(\S+)/i', $headers, $matches)) {
        return $matches[1];
    }
    return null;
}

$token = getBearerToken();
if (!$token) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized. Token missing.']);
    exit;
}

$decodedJson = base64_decode($token);
$currentUser = json_decode($decodedJson, true);

if (!$currentUser || !isset($currentUser['user_id']) || ($currentUser['role'] ?? '') !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Access denied. Admin role required.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$input  = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$action = $_GET['action'] ?? $input['action'] ?? '';

// GET REQUESTS
if ($method === 'GET') {
    // 1. Manage Users
    if ($action === 'get_users') {
        $stmt = $pdo->query("SELECT user_id, full_name, email, role FROM users ORDER BY user_id DESC");
        echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);
        exit;
    }

    // 2. Manage Venues
    if ($action === 'get_venues') {
        $stmt = $pdo->query("SELECT * FROM venues ORDER BY venue_id DESC");
        echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);
        exit;
    }

    // 3. View All Bookings
    if ($action === 'get_bookings') {
        $stmt = $pdo->query("
            SELECT b.booking_id, b.quantity, b.total_price, b.status,
                   u.full_name AS customer_name, u.email AS customer_email,
                   e.title AS event_title, e.event_date
            FROM bookings b
            JOIN users u ON b.user_id = u.user_id
            JOIN events e ON b.event_id = e.event_id
            ORDER BY b.booking_id DESC
        ");
        echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);
        exit;
    }
}

// POST REQUESTS
if ($method === 'POST') {
    // Update User Role
    if ($action === 'update_user_role') {
        $userId = intval($input['user_id'] ?? 0);
        $role   = trim($input['role'] ?? '');

        if ($userId <= 0 || !in_array($role, ['admin', 'organiser', 'customer'])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid user or role.']);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE user_id = ?");
        $stmt->execute([$role, $userId]);
        echo json_encode(['status' => 'success', 'message' => 'User role updated successfully!']);
        exit;
    }

    // Create / Update Venue
    if ($action === 'save_venue') {
        $venueId   = intval($input['venue_id'] ?? 0);
        $name      = trim($input['venue_name'] ?? '');
        $location  = trim($input['location'] ?? '');
        $capacity  = intval($input['capacity'] ?? 0);

        if (empty($name)) {
            echo json_encode(['status' => 'error', 'message' => 'Venue name is required.']);
            exit;
        }

        if ($venueId > 0) {
            $stmt = $pdo->prepare("UPDATE venues SET venue_name = ?, location = ?, capacity = ? WHERE venue_id = ?");
            $stmt->execute([$name, $location, $capacity, $venueId]);
            echo json_encode(['status' => 'success', 'message' => 'Venue updated successfully!']);
        } else {
            $stmt = $pdo->prepare("INSERT INTO venues (venue_name, location, capacity) VALUES (?, ?, ?)");
            $stmt->execute([$name, $location, $capacity]);
            echo json_encode(['status' => 'success', 'message' => 'Venue added successfully!']);
        }
        exit;
    }
}

// DELETE REQUESTS
if ($method === 'DELETE' || $action === 'delete') {
    $type = $_GET['type'] ?? '';
    $id   = intval($_GET['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid ID specified.']);
        exit;
    }

    if ($type === 'user') {
        if ($id === $currentUser['user_id']) {
            echo json_encode(['status' => 'error', 'message' => 'Cannot delete your own admin account.']);
            exit;
        }
        $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'User account deleted.']);
        exit;
    }

    if ($type === 'venue') {
        $stmt = $pdo->prepare("DELETE FROM venues WHERE venue_id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Venue deleted.']);
        exit;
    }
}