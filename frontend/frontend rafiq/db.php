<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'event_ticketing';

// 1. MySQLi Connection ($conn)
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("MySQLi Connection failed: " . $conn->connect_error);
}

// 2. PDO Connection ($pdo) untuk API Backend
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die("PDO Connection failed: " . $e->getMessage());
}
?>