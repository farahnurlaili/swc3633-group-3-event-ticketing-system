<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the database connection file to initialize $pdo
require_once 'db.php';

// Frontend pages use bearer-token authentication; never impersonate an admin.
$currentUser = $_SESSION['user'] ?? null;
if (!$currentUser && !empty($_SESSION['user_id'])) {
    $userStmt = $pdo->prepare('SELECT user_id, full_name, email, role FROM users WHERE user_id = ?');
    $userStmt->execute([(int)$_SESSION['user_id']]);
    $currentUser = $userStmt->fetch(PDO::FETCH_ASSOC) ?: null;
}


function redirectToDashboard($role) {
    // Roles in the updated schema are lowercase: admin, organiser, customer[cite: 1]
    switch (strtolower($role)) {
        case 'admin':
            header('Location: dashboard_admin.php');
            exit;
        case 'organiser':
            header('Location: dashboard_organiser.php');
            exit;
        case 'customer':
        default:
            header('Location: dashboard_customer.php');
            exit;
    }
}
?>