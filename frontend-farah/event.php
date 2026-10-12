<?php
require __DIR__ . '/config.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) redirect('index.php');

// Book tickets: POST /bookings  { event_id, quantity }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    require_role(['customer']);
    $qty = (int)($_POST['quantity'] ?? 0);
    if ($qty < 1) {
        flash('error', 'Please choose at least 1 ticket.');
    } else {
        $res = api_request('POST', '/bookings', ['event_id' => $id, 'quantity' => $qty]);
        if ($res['ok']) {
            flash('success', 'Booking created successfully!');
            redirect('my_bookings.php');
        }
        flash('error', $res['message']);   // 400 invalid, 409 not enough tickets, 403 wrong role
    }
    redirect('event.php?id=' . $id);
}

// GET /events/:id
$res = api_request('GET', '/events/' . $id);
if (!$res['ok']) {
    flash('error', $res['status'] === 404 ? 'Event not found.' : $res['message']);
    redirect('index.php');
}
$ev = $res['data'];
$left = (int)($ev['available_tickets'] ?? 0);

$pageTitle = $ev['title'];
include __DIR__ . '/includes/header.php';
?>
<a href="<?= url('index.php') ?>" class="muted">&larr; Back to events</a>
<div class="detail">
  <section class="card">
    <span class="tag"><?= e($ev['category_name'] ?? 'Event') ?></span>
    <h1><?= e($ev['title']) ?></h1>
    <p><?= nl2br(e($ev['description'] ?? '')) ?></p>
    <ul class="info">
      <li>&#128205; <strong>Venue:</strong> <?= e($ev['venue_name'] ?? '') ?><?= !empty($ev['city']) ? ', ' . e($ev['city']) : '' ?></li>
      <li>&#128197; <strong>Date:</strong> <?= fmt_date($ev['event_date']) ?></li>
      <li>&#128337; <strong>Time:</strong> <?= fmt_time($ev['start_time']) ?> &ndash; <?= fmt_time($ev['end_time']) ?></li>
      <li>&#127915; <strong>Price:</strong> <?= money($ev['ticket_price']) ?> per ticket</li>
      <li>&#128100; <strong>Organiser:</strong> <?= e($ev['organiser_name'] ?? '-') ?></li>
    </ul>
  </section>

  <aside class="card booking-box">
    <h2>Book tickets</h2>
    <p><?= $left > 0 ? '<span class="ok">' . $left . ' of ' . (int)$ev['total_tickets'] . ' left</span>' : '<span class="bad">Sold out</span>' ?></p>
    <?php if (!is_logged_in()): ?>
      <p><a class="btn" href="<?= url('login.php') ?>">Login to book</a></p>
    <?php elseif (!has_role(['customer'])): ?>
      <p class="muted">Only customer accounts can book tickets.</p>
    <?php elseif ($left < 1 || ($ev['status'] ?? '') !== 'published'): ?>
      <p class="muted">Booking is not available for this event.</p>
    <?php else: ?>
      <form method="post">
        <?= csrf_field() ?>
        <label>Quantity
          <input type="number" id="qty" name="quantity" min="1" max="<?= $left ?>" value="1" required>
        </label>
        <p>Total: <strong id="total"><?= money($ev['ticket_price']) ?></strong></p>
        <button class="btn" type="submit">Book now</button>
      </form>
      <script>
        // live total (display only - the backend recalculates the real total)
        const price = <?= (float)$ev['ticket_price'] ?>;
        const qty = document.getElementById('qty'), total = document.getElementById('total');
        qty.addEventListener('input', () => {
          total.textContent = 'RM ' + (price * (parseInt(qty.value) || 0)).toFixed(2);
        });
      </script>
    <?php endif; ?>
  </aside>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
