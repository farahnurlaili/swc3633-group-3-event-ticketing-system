<?php
error_reporting(0);
ini_set('display_errors', 0);

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
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

// Bearer Token Extractor & Organiser Verification
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

if (!$currentUser || !isset($currentUser['user_id']) || !in_array($currentUser['role'] ?? '', ['organiser', 'admin'])) {
    echo json_encode(['status' => 'error', 'message' => 'Access denied. Organiser role required.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// GET REQUESTS
if ($method === 'GET') {
    // 1. Fetch Venues List
    if ($action === 'get_venues') {
        try {
            $stmt = $pdo->query("SELECT venue_id, venue_name, capacity FROM venues ORDER BY venue_name ASC");
            echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'success', 'data' => []]);
        }
        exit;
    }

    // 2. View All Bookings
    if ($action === 'get_organiser_bookings') {
        try {
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
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Query error: ' . $e->getMessage()]);
        }
        exit;
    }
}