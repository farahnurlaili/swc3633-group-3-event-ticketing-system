<?php
function generate_token($user) {
    $payload = [
        'user_id' => $user['user_id'],
        'email' => $user['email'],
        'role' => $user['role'],
        'full_name' => $user['full_name'],
        'exp' => time() + (86400)
    ];
    return base64_encode(json_encode($payload));
}

function verify_token() {
    $headers = apache_request_headers();
    $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : (isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '');
    
    if (!$authHeader && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    }

    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $decoded = json_decode(base64_decode($matches[1]), true);
        if ($decoded && isset($decoded['exp']) && $decoded['exp'] > time()) {
            return $decoded;
        }
    }
    return null;
}