<?php
session_start();
require_once 'config/db.php';

// If already logged in, redirect based on role
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
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!empty($full_name) && !empty($email) && !empty($phone) && !empty($password) && !empty($confirm_password)) {
        if ($password !== $confirm_password) {
            $error = "Passwords do not match. Please try again.";
        } else {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = "An account with this email address already exists.";
            } else {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                // Default new registrations as 'customer'
                $role = 'customer';

                $insertStmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, ?)");
                if ($insertStmt->execute([$full_name, $email, $phone, $password_hash, $role])) {
                    $success = "Registration successful! You can now sign in.";
                } else {
                    $error = "Registration failed. Please try again later.";
                }
            }
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Eventra</title>
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
            padding: 40px 0;
        }
        h2 { font-family: var(--font-heading); }
        .card-custom { 
            background: #131b2e; 
            border: 1px solid rgba(255, 255, 255, 0.08); 
            border-radius: 20px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.4); 
            width: 100%;
            max-width: 450px;
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
            <h2 class="fw-bold text-white fs-4 mb-1">Create Account</h2>
            <p class="text-secondary small">Join Eventra to discover and book experiences</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger border-0 bg-danger bg-opacity-25 text-danger small text-center mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success border-0 bg-success bg-opacity-25 text-success small text-center mb-4">
                <?= htmlspecialchars($success) ?> <br>
                <a href="login.php" class="text-white fw-semibold text-decoration-underline">Click here to login</a>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="mb-3">
                <label class="form-label text-secondary small fw-semibold">FULL NAME</label>
                <input type="text" class="form-control" name="full_name" placeholder="e.g. Siti Aminah" required value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label text-secondary small fw-semibold">EMAIL ADDRESS</label>
                <input type="email" class="form-control" name="email" placeholder="e.g. siti@example.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label text-secondary small fw-semibold">PHONE NUMBER</label>
                <input type="text" class="form-control" name="phone" placeholder="e.g. 019-8889900" required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label text-secondary small fw-semibold">PASSWORD</label>
                <input type="password" class="form-control" name="password" placeholder="••••••••" required>
            </div>
            <div class="mb-4">
                <label class="form-label text-secondary small fw-semibold">CONFIRM PASSWORD</label>
                <input type="password" class="form-control" name="confirm_password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-custom w-100 mb-3">Register Account</button>
        </form>

        <div class="text-center pt-3 border-top border-secondary border-opacity-25">
            <span class="text-secondary small">Already have an account? </span>
            <a href="login.php" class="text-decoration-none fw-semibold" style="color: #a78bfa;">Sign in</a>
        </div>
    </div>
</body>
</html>