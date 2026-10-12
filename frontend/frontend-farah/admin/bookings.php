<?php
require __DIR__ . '/../config.php';
require_role(['admin', 'organiser']);

// PUT /bookings/:id/status  { status }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $bid = (int)($_POST['booking_id'] ?? 0);
    $status = $_POST['status'] ?? '';
    if (!in_array($status, ['pending', 'confirmed', 'cancelled'], true)) {
        flash('error', 'Invalid status.');
    } else {
        $res = api_request('PUT', '/bookings/' . $bid . '/status', ['status' => $status]);
        flash($res['ok'] ? 'success' : 'error', $res['ok'] ? 'Booking #' . $bid . ' set to ' . $status . '.' : $res['message']);
    }
    redirect('admin/bookings.php');
}

$res = api_request('GET', '/bookings');                                  // GET /bookings (all bookings)
$bookings = ($res['ok'] && is_array($res['data'])) ? $res['data'] : [];

$pageTitle = 'All Bookings';
include __DIR__ . '/../includes/header.php';
?>
<h1>All Bookings</h1>
<?php if (!$res['ok']): ?>
  <div class="alert alert-error"><?= e($res['message']) ?></div>
<?php else: ?>
<div class="table-wrap"><table>
  <thead><tr><th>#</th><th>Customer</th><th>Event</th><th>Qty</th><th>Total</th><th>Status</th><th>Update</th></tr></thead>
  <tbody>
  <?php foreach ($bookings as $b): ?>
    <tr>
      <td><?= (int)$b['booking_id'] ?></td>
      <td><?= e($b['customer_name'] ?? $b['full_name'] ?? 'User #' . ($b['user_id'] ?? '')) ?></td>
      <td><?= e($b['event_title'] ?? $b['title'] ?? 'Event #' . ($b['event_id'] ?? '')) ?></td>
      <td><?= (int)$b['quantity'] ?></td><td><?= money($b['total_price']) ?></td><td><?= badge($b['status']) ?></td>
      <td>
        <form method="post" class="inline">
          <?= csrf_field() ?><input type="hidden" name="booking_id" value="<?= (int)$b['booking_id'] ?>">
          <select name="status">
            <?php foreach (['pending', 'confirmed', 'cancelled'] as $s): ?>
              <option value="<?= $s ?>" <?= $b['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
          </select>
          <button class="btn btn-small" type="submit">Save</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
<?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
