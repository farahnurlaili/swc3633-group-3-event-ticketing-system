<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_role'])) {
    header("Location: index.php");
    exit();
}

// Ambil role dari session dan tukar ke huruf kecil
$currentRole = strtolower($_SESSION['user_role'] ?? 'customer');

require_once 'header.php';
?>

<div class="container my-4">
  <?php
    if ($currentRole === 'admin') {
        include 'admin_view.php';
    } elseif ($currentRole === 'organiser') {
        include 'organiser_view.php';
    } elseif ($currentRole === 'customer') {
        include 'customer_view.php';
    } else {
        echo '<div class="alert alert-danger">Invalid role detected!</div>';
    }
  ?>
</div>

<?php require_once 'footer.php'; ?>