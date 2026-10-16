<?php
session_start();
if (isset($_SESSION['user_role'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Event Ticketing System</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- FontAwesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <style>
    body {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .auth-card {
      border-radius: 20px;
      border: 1px solid rgba(255, 255, 255, 0.1);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }
    .nav-pills .nav-link {
      color: #94a3b8;
      font-weight: 600;
      border-radius: 12px;
      padding: 10px;
      transition: all 0.3s ease;
    }
    .nav-pills .nav-link.active {
      background-color: #0d9488 !important;
      color: #ffffff !important;
      box-shadow: 0 4px 12px rgba(13, 148, 136, 0.3);
    }
    .btn-teal {
      background-color: #0d9488;
      color: #ffffff;
      border: none;
      transition: all 0.3s ease;
    }
    .btn-teal:hover {
      background-color: #0f766e;
      color: #ffffff;
      box-shadow: 0 4px 12px rgba(13, 148, 136, 0.4);
    }
    .form-control:focus {
      border-color: #0d9488;
      box-shadow: 0 0 0 0.25rem rgba(13, 148, 136, 0.25);
    }
    .brand-logo-icon {
      width: 50px;
      height: 50px;
      background: rgba(13, 148, 136, 0.15);
      color: #2dd4bf;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 15px auto;
    }
  </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">

<div class="card auth-card bg-white p-2" style="max-width: 420px; width: 90%;">
  <div class="card-body p-4">
    <!-- BRAND LOGO HEADER -->
    <div class="text-center mb-4">
      <div class="brand-logo-icon fs-3">
        <i class="fa-solid fa-ticket-simple"></i>
      </div>
      <h3 class="fw-bold text-dark mb-1">Event Hub Portal</h3>
      <p class="text-muted small">Sign in to manage your tickets and events</p>
    </div>

    <!-- TAB NAVIGATION -->
    <ul class="nav nav-pills nav-justified mb-4 p-1 bg-light rounded-3" id="authTab" role="tablist">
      <li class="nav-item">
        <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login-panel" type="button"><i class="fa-solid fa-right-to-bracket me-1"></i> Sign In</button>
      </li>
      <li class="nav-item">
        <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register-panel" type="button"><i class="fa-solid fa-user-plus me-1"></i> Register</button>
      </li>
    </ul>

    <div class="tab-content" id="authTabContent">
      <!-- LOGIN FORM -->
      <div class="tab-pane fade show active" id="login-panel">
        <form id="loginForm">
          <div class="mb-3">
            <label class="form-label small fw-semibold text-dark">Email Address</label>
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-envelope text-muted"></i></span>
              <input type="email" id="login_email" class="form-control border-start-0" required placeholder="name@example.com">
            </div>
          </div>
          <div class="mb-4">
            <label class="form-label small fw-semibold text-dark">Password</label>
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
              <input type="password" id="login_password" class="form-control border-start-0" required placeholder="••••••••">
            </div>
          </div>
          <button type="submit" class="btn btn-teal w-100 py-2.5 fw-bold rounded-3">Sign In <i class="fa-solid fa-arrow-right-to-bracket ms-1"></i></button>
        </form>
      </div>

      <!-- REGISTER FORM -->
      <div class="tab-pane fade" id="register-panel">
        <form id="registerForm">
          <div class="mb-2">
            <label class="form-label small fw-semibold text-dark">Full Name</label>
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-user text-muted"></i></span>
              <input type="text" id="reg_name" class="form-control border-start-0" required placeholder="John Doe">
            </div>
          </div>
          <div class="mb-2">
            <label class="form-label small fw-semibold text-dark">Email Address</label>
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-envelope text-muted"></i></span>
              <input type="email" id="reg_email" class="form-control border-start-0" required placeholder="john@example.com">
            </div>
          </div>
          <div class="mb-4">
            <label class="form-label small fw-semibold text-dark">Password</label>
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
              <input type="password" id="reg_password" class="form-control border-start-0" required placeholder="••••••••">
            </div>
          </div>
          <button type="submit" class="btn btn-dark w-100 py-2.5 fw-bold rounded-3">Register Account <i class="fa-solid fa-check-circle ms-1 text-teal"></i></button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const email = document.getElementById('login_email').value;
    const password = document.getElementById('login_password').value;

    fetch('api_auth.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: email, password: password })
    })
    .then(async res => {
        const text = await res.text();
        try {
            return JSON.parse(text);
        } catch (err) {
            console.error("Backend Error Raw Output:", text);
            throw new Error("Server Error PHP: " + text.substring(0, 100));
        }
    })
    .then(data => {
        if (data.status === 'success') {
            localStorage.setItem('auth_token', data.token);
            
            fetch('auth.php?action=set_session', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    role: data.role,
                    full_name: data.full_name,
                    email: email
                })
            }).then(() => {
                window.location.href = 'dashboard.php';
            });
        } else {
            alert(data.message || 'Login failed!');
        }
    })
    .catch(err => {
        console.error(err);
        alert(err.message || 'Error connecting to API.');
    });
});

document.getElementById('registerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const payload = {
        action: 'register',
        full_name: document.getElementById('reg_name').value,
        email: document.getElementById('reg_email').value,
        password: document.getElementById('reg_password').value
    };

    fetch('api_auth.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Registration complete!');
        if (data.status === 'success') {
            location.reload();
        }
    });
});
</script>
</body>
</html>