<?php
// ---------- output / formatting ----------
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function url(string $p = ''): string { return BASE_URL . '/' . ltrim($p, '/'); }
function money($n): string { return 'RM ' . number_format((float)$n, 2); }
function fmt_date($d): string { $t = strtotime((string)$d); return $t ? date('D, j M Y', $t) : e($d); }
function fmt_time($t): string { $x = strtotime((string)$t); return $x ? date('g:i A', $x) : e($t); }
function badge(string $s): string { return '<span class="badge badge-' . e($s) . '">' . e(ucfirst($s)) . '</span>'; }

// ---------- navigation / flash messages ----------
function redirect(string $p): void { header('Location: ' . url($p)); exit; }
function flash(string $type, string $msg): void { $_SESSION['flash'][] = ['type' => $type, 'msg' => $msg]; }

// ---------- authentication / role checks (UI side only; the API enforces them for real) ----------
function is_logged_in(): bool { return !empty($_SESSION['token']); }
function current_user(): ?array { return $_SESSION['user'] ?? null; }
function has_role(array $roles): bool { return is_logged_in() && in_array(current_user()['role'] ?? '', $roles, true); }
function require_login(): void {
    if (!is_logged_in()) { flash('error', 'Please log in first.'); redirect('login.php'); }
}
function require_role(array $roles): void {
    require_login();
    if (!has_role($roles)) { flash('error', '403 - You are not allowed to open that page.'); redirect('index.php'); }
}

// ---------- CSRF protection for every POST form ----------
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . csrf_token() . '">'; }
function csrf_check(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(419);
        die('Invalid or expired form token. Please go back and try again.');
    }
}
