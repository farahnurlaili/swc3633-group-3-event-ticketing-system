<?php
require __DIR__ . '/../config.php';
require_role(['admin', 'organiser']);

$id = (int)($_GET['id'] ?? 0);
$editing = $id > 0;

$r = api_request('GET', '/venues');
$venues = ($r['ok'] && is_array($r['data'])) ? $r['data'] : [];
$r = api_request('GET', '/categories');
$categories = ($r['ok'] && is_array($r['data'])) ? $r['data'] : [];

$f = ['title' => '', 'description' => '', 'venue_id' => '', 'category_id' => '', 'event_date' => '',
      'start_time' => '', 'end_time' => '', 'ticket_price' => '', 'total_tickets' => '',
      'available_tickets' => '', 'status' => 'draft'];

if ($editing && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $res = api_request('GET', '/events/' . $id);          // GET /events/:id
    if (!$res['ok']) { flash('error', $res['message']); redirect('admin/events.php'); }
    foreach ($f as $k => $_) $f[$k] = $res['data'][$k] ?? '';
    $f['event_date'] = substr((string)$f['event_date'], 0, 10);
    $f['start_time'] = substr((string)$f['start_time'], 0, 5);
    $f['end_time']   = substr((string)$f['end_time'], 0, 5);
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    foreach ($f as $k => $_) $f[$k] = trim($_POST[$k] ?? '');

    if ($f['title'] === '' || $f['venue_id'] === '' || $f['category_id'] === '' || $f['event_date'] === ''
        || $f['start_time'] === '' || $f['end_time'] === '' || $f['ticket_price'] === '' || $f['total_tickets'] === '') {
        $error = 'Please fill in all required fields.';
    } elseif ($f['end_time'] <= $f['start_time']) {
        $error = 'End time must be after start time.';
    } elseif ((float)$f['ticket_price'] < 0 || (int)$f['total_tickets'] < 1) {
        $error = 'Price cannot be negative and total tickets must be at least 1.';
    } else {
        $body = [
            'title' => $f['title'], 'description' => $f['description'],
            'venue_id' => (int)$f['venue_id'], 'category_id' => (int)$f['category_id'],
            'event_date' => $f['event_date'], 'start_time' => $f['start_time'] . ':00', 'end_time' => $f['end_time'] . ':00',
            'ticket_price' => (float)$f['ticket_price'], 'total_tickets' => (int)$f['total_tickets'],
            'available_tickets' => $editing && $f['available_tickets'] !== '' ? (int)$f['available_tickets'] : (int)$f['total_tickets'],
            'status' => $f['status'],
        ];
        // POST /events  or  PUT /events/:id
        $res = $editing ? api_request('PUT', '/events/' . $id, $body) : api_request('POST', '/events', $body);
        if ($res['ok']) {
            flash('success', $editing ? 'Event updated.' : 'Event created.');
            redirect('admin/events.php');
        }
        $error = $res['message'];
    }
}

$pageTitle = $editing ? 'Edit Event' : 'New Event';
include __DIR__ . '/../includes/header.php';
?>
<h1><?= $editing ? 'Edit event #' . $id : 'Create event' ?></h1>
<div class="card">
  <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
  <form method="post" class="form-grid" novalidate>
    <?= csrf_field() ?>
    <label class="full">Title * <input name="title" value="<?= e($f['title']) ?>" required></label>
    <label class="full">Description <textarea name="description" rows="3"><?= e($f['description']) ?></textarea></label>
    <label>Venue *
      <select name="venue_id" required><option value="">-- choose --</option>
        <?php foreach ($venues as $v): ?>
          <option value="<?= (int)$v['venue_id'] ?>" <?= (string)$f['venue_id'] === (string)$v['venue_id'] ? 'selected' : '' ?>><?= e($v['venue_name']) ?></option>
        <?php endforeach; ?>
      </select></label>
    <label>Category *
      <select name="category_id" required><option value="">-- choose --</option>
        <?php foreach ($categories as $c): ?>
          <option value="<?= (int)$c['category_id'] ?>" <?= (string)$f['category_id'] === (string)$c['category_id'] ? 'selected' : '' ?>><?= e($c['category_name']) ?></option>
        <?php endforeach; ?>
      </select></label>
    <label>Date * <input type="date" name="event_date" value="<?= e($f['event_date']) ?>" required></label>
    <label>Ticket price (RM) * <input type="number" step="0.01" min="0" name="ticket_price" value="<?= e($f['ticket_price']) ?>" required></label>
    <label>Start time * <input type="time" name="start_time" value="<?= e($f['start_time']) ?>" required></label>
    <label>End time * <input type="time" name="end_time" value="<?= e($f['end_time']) ?>" required></label>
    <label>Total tickets * <input type="number" min="1" name="total_tickets" value="<?= e($f['total_tickets']) ?>" required></label>
    <?php if ($editing): ?>
      <label>Available tickets <input type="number" min="0" name="available_tickets" value="<?= e($f['available_tickets']) ?>"></label>
    <?php endif; ?>
    <label>Status
      <select name="status">
        <?php foreach (['draft', 'published', 'cancelled'] as $s): ?>
          <option value="<?= $s ?>" <?= $f['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
        <?php endforeach; ?>
      </select></label>
    <div class="full">
      <button class="btn" type="submit"><?= $editing ? 'Save changes' : 'Create event' ?></button>
      <a class="btn btn-outline" href="<?= url('admin/events.php') ?>">Cancel</a>
    </div>
  </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
