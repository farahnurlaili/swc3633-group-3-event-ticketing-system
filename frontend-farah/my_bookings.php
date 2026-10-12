<?php
require __DIR__ . '/config.php';
require_role(['customer']);

// Cancel: PUT /bookings/:id/cancel
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $bid = (int)($_POST['booking_id'] ?? 0);
    $res = api_request('PUT', '/bookings/' . $bid . '/cancel');
    flash($res['ok'] ? 'success' : 'error', $res['ok'] ? 'Booking #' . $bid . ' cancelled.' : $res['message']);
    redirect('my_bookings.php');
}

// GET /bookings/my
$res = api_request('GET', '/bookings/my');
$bookings = ($res['ok'] && is_array($res['data'])) ? $res['data'] : [];

$pageTitle = 'My Bookings';
include __DIR__ . '/includes/header.php';
?>
<h1>My Bookings</h1>
<?php if (!$res['ok']): ?>
  <div class="alert alert-error"><?= e($res['message']) ?></div>
<?php elseif (!$bookings): ?>
  <div class="card empty">You have no bookings yet. <a href="<?= url('index.php') ?>">Browse events</a>.</div>
<?php else: ?>
<div class="table-wrap"><table>
  <thead><tr><th>#</th><th>Event</th><th>Date</th><th>Qty</th><th>Total</th><th>Booking</th><th>Payment</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($bookings as $b): $st = $b['status'] ?? 'pending'; ?>
    <tr>
      <td><?= (int)$b['booking_id'] ?></td>
      <td><a href="<?= url('event.php?id=' . (int)$b['event_id']) ?>"><?= e($b['event_title'] ?? $b['title'] ?? 'Event #' . $b['event_id']) ?></a></td>
      <td><?= isset($b['event_date']) ? fmt_date($b['event_date']) : '-' ?></td>
      <td><?= (int)$b['quantity'] ?></td>
      <td><?= money($b['total_price']) ?></td>
      <td><?= badge($st) ?></td>
      <td><?= !empty($b['payment_status']) ? badge($b['payment_status']) : '<span class="muted">-</span>' ?></td>
      <td>
        <?php if ($st !== 'cancelled'): ?>
        <form method="post" onsubmit="return confirm('Cancel this booking?');">
          <?= csrf_field() ?><input type="hidden" name="booking_id" value="<?= (int)$b['booking_id'] ?>">
          <button class="btn btn-small btn-danger" type="submit">Cancel</button>
        </form>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
