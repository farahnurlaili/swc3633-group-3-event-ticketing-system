<?php
// Configuration & Helper functions
define('DB_HOST', 'localhost');
define('DB_USER', 'root');      // Update with your database username
define('DB_PASS', '');          // Update with your database password
define('DB_NAME', 'event_ticketing');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Enforce role access control.
 * Redirects unauthorized users to login or appropriate dashboard.
 */
function check_access(array $allowed_roles): void {
    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit();
    }

    $user_role = $_SESSION['user']['role'];
    
    // Check if account is active
    if (isset($_SESSION['user']['status']) && $_SESSION['user']['status'] === 'suspended') {
        session_destroy();
        die("Your account has been suspended. Please contact support.");
    }

    if (!in_array($user_role, $allowed_roles)) {
        redirect_to_dashboard($user_role);
    }
}

/**
 * Helper to route user to their respective dashboard
 */
function redirect_to_dashboard(string $role): void {
    switch ($role) {
        case 'admin':
            header("Location: dashboard_admin.php");
            break;
        case 'organiser':
            header("Location: dashboard_organiser.php");
            break;
        case 'customer':
            header("Location: dashboard_customer.php");
            break;
        default:
            header("Location: login.php");
            break;
    }
    exit();
}