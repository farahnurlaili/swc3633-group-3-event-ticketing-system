<?php
require __DIR__ . '/config.php';
if (is_logged_in()) redirect('index.php');

$error = ''; $email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($email === '' || $password === '') {
        $error = 'Email and password are required.';
    } else {
        // POST /auth/login  -> { data: { token, user: { user_id, full_name, role } } }
        $res = api_request('POST', '/auth/login', ['email' => $email, 'password' => $password]);
        if ($res['ok'] && !empty($res['data']['token'])) {
            session_regenerate_id(true);
            $_SESSION['token'] = $res['data']['token'];
            $_SESSION['user']  = $res['data']['user'] ?? ['full_name' => $email, 'role' => 'customer'];
            flash('success', 'Welcome back, ' . ($_SESSION['user']['full_name'] ?? '') . '!');
            redirect(has_role(['admin', 'organiser']) ? 'admin/events.php' : 'index.php');
        }
        $error = $res['message'];
    }
}
$pageTitle = 'Login';
include __DIR__ . '/includes/header.php';
?>
<div class="card form-card">
  <h1>Login</h1>
  <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
  <form method="post" novalidate>
    <?= csrf_field() ?>
    <label>Email <input type="email" name="email" value="<?= e($email) ?>" required></label>
    <label>Password <input type="password" name="password" required></label>
    <button class="btn" type="submit">Login</button>
  </form>
  <p class="muted">No account? <a href="<?= url('register.php') ?>">Register here</a>.</p>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
