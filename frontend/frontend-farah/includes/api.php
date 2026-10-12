<?php
/**
 * api_request() - the ONE place where the frontend talks to the RESTful API.
 * The PHP server calls the API with cURL, sending the JWT from the session
 * as "Authorization: Bearer <token>". Returns a uniform array:
 *   ['ok' => bool, 'status' => int, 'data' => mixed, 'message' => string]
 */
function api_request(string $method, string $path, ?array $body = null, array $query = []): array
{
    $url = API_BASE . $path;
    $query = array_filter($query, fn($v) => $v !== '' && $v !== null);
    if ($query) $url .= '?' . http_build_query($query);

    $headers = ['Accept: application/json'];
    if (!empty($_SESSION['token'])) $headers[] = 'Authorization: Bearer ' . $_SESSION['token'];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => strtoupper($method),
        CURLOPT_TIMEOUT        => 10,
    ]);
    if ($body !== null) {
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $raw = curl_exec($ch);
    if ($raw === false) {
        $err = curl_error($ch);
        curl_close($ch);
        return ['ok' => false, 'status' => 0, 'data' => null,
                'message' => 'Cannot reach the API server (' . $err . '). Is the backend running?'];
    }
    $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $json = json_decode($raw, true);
    $ok = $status >= 200 && $status < 300;

    // Token missing/expired -> force a fresh login
    if ($status === 401 && !empty($_SESSION['token'])) {
        unset($_SESSION['token'], $_SESSION['user']);
        flash('error', 'Your session has expired. Please log in again.');
        redirect('login.php');
    }

    $message = is_array($json) ? ($json['message'] ?? $json['error'] ?? '') : '';
    if ($message === '') $message = $ok ? 'OK' : 'Request failed (HTTP ' . $status . ').';
    $data = (is_array($json) && array_key_exists('data', $json)) ? $json['data'] : $json;

    return ['ok' => $ok, 'status' => $status, 'data' => $data, 'message' => $message];
}
