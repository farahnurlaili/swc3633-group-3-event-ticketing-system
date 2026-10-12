<?php
require __DIR__ . '/config.php';
if (is_logged_in()) redirect('index.php');

$error = ''; $old = ['full_name' => '', 'email' => '', 'phone' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    foreach ($old as $k => $_) $old[$k] = trim($_POST[$k] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    // client-side style validation before calling the API
    if ($old['full_name'] === '' || $old['email'] === '' || $password === '') {
        $error = 'Name, email and password are required.';
    } elseif (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        // POST /auth/register  (customers only; role is decided by the backend)
        $res = api_request('POST', '/auth/register', $old + ['password' => $password]);
        if ($res['ok']) {
            flash('success', 'Account created. You can now log in.');
            redirect('login.php');
        }
        $error = $res['message'];   // e.g. 409 email already registered, 400 validation
    }
}
$pageTitle = 'Register';
include __DIR__ . '/includes/header.php';
?>
<div class="card form-card">
  <h1>Create account</h1>
  <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
  <form method="post" novalidate>
    <?= csrf_field() ?>
    <label>Full name <input name="full_name" value="<?= e($old['full_name']) ?>" required></label>
    <label>Email <input type="email" name="email" value="<?= e($old['email']) ?>" required></label>
    <label>Phone <input name="phone" value="<?= e($old['phone']) ?>"></label>
    <label>Password (min 8 characters) <input type="password" name="password" required></label>
    <label>Confirm password <input type="password" name="confirm" required></label>
    <button class="btn" type="submit">Register</button>
  </form>
  <p class="muted">Already registered? <a href="<?= url('login.php') ?>">Login</a>.</p>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
