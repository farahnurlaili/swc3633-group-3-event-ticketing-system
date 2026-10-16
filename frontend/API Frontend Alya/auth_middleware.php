<?php
// Signed bearer token. Set EVENTTIX_TOKEN_SECRET in the Apache/PHP environment in production.
function token_secret() {
    $secret = getenv('EVENTTIX_TOKEN_SECRET');
    if ($secret && strlen($secret) >= 32) return $secret;
    $file = __DIR__ . '/.token_secret';
    if (!is_file($file)) {
        $secret = bin2hex(random_bytes(32));
        if (file_put_contents($file, $secret, LOCK_EX) === false) throw new RuntimeException('Cannot store token secret');
        @chmod($file, 0600);
    }
    return trim(file_get_contents($file));
}
function generate_token($user) {
    $payload = ['user_id'=>(int)$user['user_id'], 'email'=>$user['email'], 'role'=>$user['role'], 'full_name'=>$user['full_name'], 'exp'=>time()+86400];
    $body = rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=');
    $sig = rtrim(strtr(base64_encode(hash_hmac('sha256', $body, token_secret(), true)), '+/', '-_'), '=');
    return $body . '.' . $sig;
}
function verify_token() {
    $headers = function_exists('getallheaders') ? getallheaders() : [];
    $auth = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
    foreach ($headers as $key=>$value) if (strcasecmp($key, 'Authorization') === 0) $auth=$value;
    if (!preg_match('/^Bearer\s+(\S+)$/i', trim($auth), $m)) return null;
    $parts = explode('.', $m[1]);
    if (count($parts) !== 2) return null;
    [$body, $sig] = $parts;
    $expected = rtrim(strtr(base64_encode(hash_hmac('sha256', $body, token_secret(), true)), '+/', '-_'), '=');
    if (!hash_equals($expected, $sig)) return null;
    $data = json_decode(base64_decode(strtr($body, '-_', '+/'), true), true);
    if (!is_array($data) || !isset($data['user_id'], $data['exp']) || $data['exp'] <= time()) return null;
    global $pdo;
    $stmt=$pdo->prepare('SELECT user_id, email, role, full_name FROM users WHERE user_id = ?');
    $stmt->execute([(int)$data['user_id']]);
    $user=$stmt->fetch();
    return $user ?: null;
}
