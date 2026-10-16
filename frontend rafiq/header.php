<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_role']);
$userName = $_SESSION['user_name'] ?? 'User';
$userRole = $_SESSION['user_role'] ?? 'customer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Event Ticketing System</title>
  
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- FontAwesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <style>
    body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    
    /* Dashboard Banner Styling */
    .dashboard-banner {
      background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
      color: #fff;
      padding: 30px;
      border-radius: 16px;
      margin-bottom: 25px;
      box-shadow: 0 10px 20px rgba(0,0,0,0.08);
    }
    .banner-badge {
      background: rgba(255,255,255,0.2);
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .banner-title { font-weight: 800; margin-top: 10px; margin-bottom: 8px; }
    .banner-subtitle { opacity: 0.9; font-size: 0.95rem; max-width: 650px; margin-bottom: 20px; }
    .banner-actions { display: flex; gap: 12px; flex-wrap: wrap; }
    
    .banner-btn-white {
      background: #ffffff;
      color: #1e3c72;
      padding: 8px 18px;
      border-radius: 8px;
      font-weight: 700;
      text-decoration: none;
      transition: all 0.2s;
    }
    .banner-btn-white:hover { background: #f1f5f9; color: #1e3c72; }
    
    .banner-btn-outline {
      border: 1px solid rgba(255,255,255,0.4);
      color: #ffffff;
      padding: 8px 18px;
      border-radius: 8px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.2s;
    }
    .banner-btn-outline:hover { background: rgba(255,255,255,0.1); color: #fff; }

    /* Metrics Grid Styling */
    .metrics-grid-3 {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 20px;
    }
    .metric-card-flex {
      background: #ffffff;
      padding: 20px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      gap: 16px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.03);
      border: 1px solid #edf2f7;
    }
    .metric-icon-box {
      width: 52px;
      height: 52px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.4rem;
    }
    .metric-icon-emerald { background: #e6fffa; color: #319795; }
    .metric-icon-blue { background: #ebf8ff; color: #3182ce; }
    .metric-icon-purple { background: #faf5ff; color: #805ad5; }
    
    .metric-label { font-size: 0.85rem; color: #718096; margin: 0; font-weight: 600; }
    .metric-value { font-size: 1.8rem; font-weight: 800; color: #2d3748; margin: 0; }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="dashboard.php">🎟️ Event Ticketing System</a>
    <div class="d-flex align-items-center gap-3">
      <?php if ($isLoggedIn): ?>
        <span class="text-white small">Welcome, <strong><?php echo htmlspecialchars($userName); ?></strong> <span class="badge bg-primary text-capitalize"><?php echo htmlspecialchars($userRole); ?></span></span>
        <a href="auth.php?action=logout" class="btn btn-outline-danger btn-sm fw-bold">Logout</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<div class="container py-4">