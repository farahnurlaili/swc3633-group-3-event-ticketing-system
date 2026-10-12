<?php $user = current_user(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="color-scheme" content="dark">
  <title><?= e($pageTitle ?? 'Home') ?> | <?= APP_NAME ?></title>
  <link rel="stylesheet" href="<?= url('assets/style.css') ?>">
</head>
<body>
<header class="topbar">
  <div class="container nav">
    <a class="brand" href="<?= url('index.php') ?>">&#127915; <?= APP_NAME ?></a>
    <nav>
      <a href="<?= url('index.php') ?>">Events</a>
      <?php if (has_role(['customer'])): ?>
        <a href="<?= url('my_bookings.php') ?>">My Bookings</a>
      <?php endif; ?>
      <?php if (has_role(['admin', 'organiser'])): ?>
        <a href="<?= url('admin/events.php') ?>">Manage Events</a>
        <a href="<?= url('admin/venues.php') ?>">Venues</a>
        <a href="<?= url('admin/bookings.php') ?>">Bookings</a>
      <?php endif; ?>
    </nav>
    <div class="auth">
      <?php if ($user): ?>
        <span class="who"><?= e($user['full_name'] ?? 'User') ?> <?= badge($user['role'] ?? 'customer') ?></span>
        <a class="btn btn-small btn-outline" href="<?= url('logout.php') ?>">Logout</a>
      <?php else: ?>
        <a class="btn btn-small btn-outline" href="<?= url('login.php') ?>">Login</a>
        <a class="btn btn-small" href="<?= url('register.php') ?>">Register</a>
      <?php endif; ?>
    </div>
  </div>
</header>
<main class="container">
<?php foreach (($_SESSION['flash'] ?? []) as $f): ?>
  <div class="alert alert-<?= e($f['type']) ?>"><?= e($f['msg']) ?></div>
<?php endforeach; unset($_SESSION['flash']); ?>
