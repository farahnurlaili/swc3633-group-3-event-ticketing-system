<?php
session_start();

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    echo "<script>localStorage.removeItem('auth_token'); window.location.href='index.php';</script>";
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'set_session') {
    $data = json_decode(file_get_contents('php://input'), true);
    if ($data) {
        $_SESSION['user_id'] = 1;
        $_SESSION['user_name'] = $data['full_name'] ?? 'User';
        $_SESSION['user_email'] = $data['email'] ?? '';
        $_SESSION['user_role'] = strtolower($data['role'] ?? 'customer');
        echo json_encode(['status' => 'success']);
    }
    exit();
}
?>