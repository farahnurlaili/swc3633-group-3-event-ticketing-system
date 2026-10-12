<?php
require __DIR__ . '/../config.php';
require_role(['admin', 'organiser']);

$error = ''; $old = ['venue_name' => '', 'address' => '', 'city' => '', 'capacity' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (($_POST['action'] ?? '') === 'delete') {
        $res = api_request('DELETE', '/venues/' . (int)($_POST['venue_id'] ?? 0));          // DELETE /venues/:id
        flash($res['ok'] ? 'success' : 'error', $res['ok'] ? 'Venue deleted.' : $res['message']);
        redirect('admin/venues.php');
    }
    foreach ($old as $k => $_) $old[$k] = trim($_POST[$k] ?? '');
    if (in_array('', $old, true)) {
        $error = 'All venue fields are required.';
    } elseif ((int)$old['capacity'] < 1) {
        $error = 'Capacity must be at least 1.';
    } else {
        $res = api_request('POST', '/venues', ['venue_name' => $old['venue_name'], 'address' => $old['address'],
                                               'city' => $old['city'], 'capacity' => (int)$old['capacity']]);  // POST /venues
        if ($res['ok']) { flash('success', 'Venue added.'); redirect('admin/venues.php'); }
        $error = $res['message'];
    }
}

$res = api_request('GET', '/venues');                                                      // GET /venues
$venues = ($res['ok'] && is_array($res['data'])) ? $res['data'] : [];

$pageTitle = 'Venues';
include __DIR__ . '/../includes/header.php';
?>
<h1>Venues</h1>
<div class="card">
  <h3>Add venue</h3>
  <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
  <form method="post" class="form-grid" novalidate>
    <?= csrf_field() ?>
    <label>Name <input name="venue_name" value="<?= e($old['venue_name']) ?>" required></label>
    <label>City <input name="city" value="<?= e($old['city']) ?>" required></label>
    <label>Address <input name="address" value="<?= e($old['address']) ?>" required></label>
    <label>Capacity <input type="number" min="1" name="capacity" value="<?= e($old['capacity']) ?>" required></label>
    <div class="full"><button class="btn" type="submit">Add venue</button></div>
  </form>
</div>
<div class="table-wrap"><table>
  <thead><tr><th>#</th><th>Name</th><th>Address</th><th>City</th><th>Capacity</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($venues as $v): ?>
    <tr>
      <td><?= (int)$v['venue_id'] ?></td><td><?= e($v['venue_name']) ?></td><td><?= e($v['address']) ?></td>
      <td><?= e($v['city']) ?></td><td><?= number_format((int)$v['capacity']) ?></td>
      <td>
        <form method="post" onsubmit="return confirm('Delete this venue?');">
          <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="venue_id" value="<?= (int)$v['venue_id'] ?>">
          <button class="btn btn-small btn-danger" type="submit">Delete</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
