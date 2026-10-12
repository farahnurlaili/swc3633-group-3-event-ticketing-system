<?php
require __DIR__ . '/config.php';

$search = trim($_GET['search'] ?? '');
$catId  = $_GET['category_id'] ?? '';

// GET /categories  and  GET /events?search=&category_id=
$catRes = api_request('GET', '/categories');
$evRes  = api_request('GET', '/events', null, ['search' => $search, 'category_id' => $catId]);
$categories = ($catRes['ok'] && is_array($catRes['data'])) ? $catRes['data'] : [];
$events     = ($evRes['ok'] && is_array($evRes['data'])) ? $evRes['data'] : [];

$pageTitle = 'Browse Events';
include __DIR__ . '/includes/header.php';
?>
<h1>Upcoming Events</h1>
<form class="filters" method="get">
  <input type="search" name="search" placeholder="Search events..." value="<?= e($search) ?>">
  <select name="category_id">
    <option value="">All categories</option>
    <?php foreach ($categories as $c): ?>
      <option value="<?= e($c['category_id']) ?>" <?= (string)$catId === (string)$c['category_id'] ? 'selected' : '' ?>>
        <?= e($c['category_name']) ?>
      </option>
    <?php endforeach; ?>
  </select>
  <button class="btn" type="submit">Filter</button>
  <?php if ($search !== '' || $catId !== ''): ?><a class="btn btn-outline" href="<?= url('index.php') ?>">Clear</a><?php endif; ?>
</form>

<?php if (!$evRes['ok']): ?>
  <div class="alert alert-error"><?= e($evRes['message']) ?></div>
<?php elseif (!$events): ?>
  <div class="card empty">No events found.</div>
<?php else: ?>
  <div class="grid">
  <?php foreach ($events as $ev): $left = (int)($ev['available_tickets'] ?? 0); ?>
    <article class="card event-card">
      <span class="tag"><?= e($ev['category_name'] ?? 'Event') ?></span>
      <h3><?= e($ev['title']) ?></h3>
      <p class="muted">&#128205; <?= e($ev['venue_name'] ?? '') ?></p>
      <p class="muted">&#128197; <?= fmt_date($ev['event_date']) ?>, <?= fmt_time($ev['start_time']) ?></p>
      <p class="price"><?= money($ev['ticket_price']) ?> <small>/ ticket</small></p>
      <p><?= $left > 0 ? '<span class="ok">' . $left . ' tickets left</span>' : '<span class="bad">Sold out</span>' ?></p>
      <a class="btn" href="<?= url('event.php?id=' . (int)$ev['event_id']) ?>">View details</a>
    </article>
  <?php endforeach; ?>
  </div>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
