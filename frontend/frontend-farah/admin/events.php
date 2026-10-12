<?php
require __DIR__ . '/../config.php';
require_role(['admin', 'organiser']);

// Delete: DELETE /events/:id
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $eid = (int)($_POST['event_id'] ?? 0);
    $res = api_request('DELETE', '/events/' . $eid);
    flash($res['ok'] ? 'success' : 'error', $res['ok'] ? 'Event deleted.' : $res['message']);  // 409 if it has bookings
    redirect('admin/events.php');
}

// GET /events?manage=1  (backend should include draft events for admin/organiser)
$res = api_request('GET', '/events', null, ['manage' => 1]);
$events = ($res['ok'] && is_array($res['data'])) ? $res['data'] : [];

$pageTitle = 'Manage Events';
include __DIR__ . '/../includes/header.php';
?>
<div class="row-between">
  <h1>Manage Events</h1>
  <a class="btn" href="<?= url('admin/event_form.php') ?>">+ New event</a>
</div>
<?php if (!$res['ok']): ?>
  <div class="alert alert-error"><?= e($res['message']) ?></div>
<?php else: ?>
<div class="table-wrap"><table>
  <thead><tr><th>#</th><th>Title</th><th>Venue</th><th>Date</th><th>Price</th><th>Tickets</th><th>Status</th><th>Actions</th></tr></thead>
  <tbody>
  <?php foreach ($events as $ev): ?>
    <tr>
      <td><?= (int)$ev['event_id'] ?></td>
      <td><?= e($ev['title']) ?></td>
      <td><?= e($ev['venue_name'] ?? '') ?></td>
      <td><?= fmt_date($ev['event_date']) ?></td>
      <td><?= money($ev['ticket_price']) ?></td>
      <td><?= (int)$ev['available_tickets'] ?> / <?= (int)$ev['total_tickets'] ?></td>
      <td><?= badge($ev['status']) ?></td>
      <td class="actions">
        <a class="btn btn-small btn-outline" href="<?= url('admin/event_form.php?id=' . (int)$ev['event_id']) ?>">Edit</a>
        <form method="post" onsubmit="return confirm('Delete this event?');">
          <?= csrf_field() ?><input type="hidden" name="event_id" value="<?= (int)$ev['event_id'] ?>">
          <button class="btn btn-small btn-danger" type="submit">Delete</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
<?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
