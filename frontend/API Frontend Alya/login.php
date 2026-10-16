<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - EventTix Pro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-body">

    <div class="auth-card">
        <!-- Logo & Title -->
        <div class="auth-header">
            <div class="auth-icon-box">
                <i class="fa-solid fa-ticket"></i>
            </div>
            <h2 class="auth-title">Sign In</h2>
            <p class="auth-subtitle">Select a demo account or sign in to your portal</p>
        </div>

        <div id="error-container" class="auth-error hidden" style="display: none;"></div>

        <!-- Login Form -->
        <form id="login-form" class="auth-form">
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" id="email" name="email" required class="form-input" placeholder="admin@eventhub.my">
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" id="password" name="password" required value="Password123!" class="form-input" placeholder="••••••••">
            </div>
            
            <button type="submit" class="auth-submit-btn">
                Sign In
            </button>
        </form>

        <!-- Link to Register Page -->
        <div class="auth-footer-text">
            Don't have an account? 
            <a href="register.php" class="auth-link">Register Now</a>
        </div>

        <!-- Quick Demo Accounts -->
        <div class="demo-box">
            <p class="demo-title">Quick Demo Logins:</p>
            <div class="demo-list">
                <button type="button" onclick="fillCredentials('admin@eventhub.my')" class="demo-btn">
                    <span>&bull; Admin: <code class="demo-code">admin@eventhub.my</code></span>
                    <i class="fa-solid fa-arrow-right-long demo-arrow"></i>
                </button>
                <button type="button" onclick="fillCredentials('daniel@eventhub.my')" class="demo-btn">
                    <span>&bull; Organiser: <code class="demo-code">daniel@eventhub.my</code></span>
                    <i class="fa-solid fa-arrow-right-long demo-arrow"></i>
                </button>
                <button type="button" onclick="fillCredentials('farid@example.com')" class="demo-btn">
                    <span>&bull; Customer: <code class="demo-code">farid@example.com</code></span>
                    <i class="fa-solid fa-arrow-right-long demo-arrow"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
    function fillCredentials(email) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = 'Password123!';
    }

    document.getElementById('login-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const errorBox = document.getElementById('error-container');

        errorBox.style.display = 'none';

        fetch('api_auth.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email: email, password: password })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Store token in localStorage for subsequent API requests
                localStorage.setItem('auth_token', data.token);
                
                // Redirect based on returned role
                const role = data.role.toLowerCase();
                if (role === 'admin') {
                    window.location.href = 'dashboard_admin.php';
                } else if (role === 'organiser') {
                    window.location.href = 'dashboard_organiser.php';
                } else {
                    window.location.href = 'dashboard_customer.php';
                }
            } else {
                errorBox.innerText = data.message || 'Login failed.';
                errorBox.style.display = 'block';
            }
        })
        .catch(err => {
            console.error('Error:', err);
            errorBox.innerText = 'An unexpected error occurred. Please try again.';
            errorBox.style.display = 'block';
        });
    });
    </script>

</body>
</html>