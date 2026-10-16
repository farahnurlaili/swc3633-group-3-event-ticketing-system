<?php
session_start();
require_once 'config/db.php';

// If user is already logged in, redirect them directly to their specific portal
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin.php");
        exit();
    } elseif ($_SESSION['role'] === 'organiser') {
        header("Location: organiser.php");
        exit();
    } else {
        header("Location: index.php");
        exit();
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        // Fetch user record from database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Verify user existence and password hash
        if ($user && password_verify($password, $user['password_hash'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Role-Based Access Control (RBAC) Redirection
            if ($user['role'] === 'admin') {
                header("Location: admin.php");
                exit();
            } elseif ($user['role'] === 'organiser') {
                header("Location: organiser.php");
                exit();
            } else {
                header("Location: index.php");
                exit();
            }
        } else {
            $error = "Invalid email or password. Please try again.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Eventra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Sora:wght@600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --font-heading: 'Sora', sans-serif;
            --font-body: 'Inter', sans-serif;
        }
        body { 
            background-color: #0b0f19; 
            color: #f8f9fa; 
            font-family: var(--font-body); 
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        h2 { font-family: var(--font-heading); }
        .card-custom { 
            background: #131b2e; 
            border: 1px solid rgba(255, 255, 255, 0.08); 
            border-radius: 20px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.4); 
            width: 100%;
            max-width: 420px;
        }
        .form-control { 
            background: rgba(15, 23, 42, 0.8); 
            border: 1px solid rgba(255, 255, 255, 0.1); 
            color: #fff; 
            padding: 12px 16px;
            border-radius: 10px;
        }
        .form-control:focus { 
            background: rgba(15, 23, 42, 0.95); 
            border-color: #8b5cf6; 
            color: #fff; 
            box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.15); 
        }
        .btn-custom { 
            background: linear-gradient(135deg, #8b5cf6, #6d28d9); 
            border: none; 
            color: white; 
            font-weight: 600; 
            padding: 12px;
            border-radius: 10px; 
            transition: all 0.25s ease;
        }
        .btn-custom:hover { 
            background: linear-gradient(135deg, #7c3aed, #5b21b6); 
            transform: translateY(-1px);
            color: white;
        }
    </style>
</head>
<body>
    <div class="card card-custom p-4 p-md-5">
        <div class="text-center mb-4">
            <a class="navbar-brand d-inline-flex align-items-center gap-2 text-white fw-bold fs-4 mb-2 text-decoration-none" href="index.php">
                <span class="p-2 rounded-3 text-white shadow-sm" style="background: #7c3aed;">✦</span> Eventra
            </a>
            <h2 class="fw-bold text-white fs-4 mb-1">Welcome Back</h2>
            <p class="text-secondary small">Sign in to access your account portal</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger border-0 bg-danger bg-opacity-25 text-danger small text-center mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="mb-3">
                <label class="form-label text-secondary small fw-semibold">EMAIL ADDRESS</label>
                <input type="email" class="form-control" name="email" placeholder="e.g. admin@eventhub.my" required>
            </div>
            <div class="mb-4">
                <label class="form-label text-secondary small fw-semibold">PASSWORD</label>
                <input type="password" class="form-control" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-custom w-100 mb-3">Sign In</button>
        </form>

        <div class="text-center pt-3 border-top border-secondary border-opacity-25">
            <span class="text-secondary small">Don't have an account? </span>
            <a href="register.php" class="text-decoration-none fw-semibold" style="color: #a78bfa;">Register now</a>
        </div>
    </div>
</body>
</html>