<?php
require_once 'config.php';
$error = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - EventTix Pro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-body">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-icon-box">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <h2 class="auth-title">Create Account</h2>
            <p class="auth-subtitle">Register as a customer to book event passes</p>
        </div>

        <div id="error-container" class="auth-error hidden" style="display: none;"></div>
        <div id="success-container" class="auth-success hidden" style="display: none; color: #047857; background-color: #ecfdf5; padding: 10px; border-radius: 8px; font-size: 13px; margin-bottom: 15px; text-align: center;"></div>

        <form id="register-form" class="auth-form">
            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" id="name" name="name" required class="form-input" placeholder="Aiman Hakim">
            </div>
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" id="email" name="email" required class="form-input" placeholder="aiman@student.uptm.edu.my">
            </div>
            <div class="form-group">
                <label class="form-label" for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" required class="form-input"
                    placeholder="0123456789" inputmode="tel" autocomplete="tel"
                    pattern="0[0-9]{9,10}" maxlength="11"
                    title="Enter a 10 or 11 digit Malaysian phone number starting with 0">
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" id="password" name="password" required class="form-input" placeholder="••••••••">
            </div>
            <button type="submit" class="auth-submit-btn">Register & Start Booking</button>
        </form>

        <div class="auth-footer-text">
            Already have an account? <a href="login.php" class="auth-link">Sign In</a>
        </div>
    </div>

    <script>
    document.getElementById('register-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const fullName = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const phone = document.getElementById('phone').value.trim();
        const errorBox = document.getElementById('error-container');
        const successBox = document.getElementById('success-container');

        errorBox.style.display = 'none';
        successBox.style.display = 'none';

        fetch('api_auth.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'register',
                full_name: fullName,
                email: email,
                phone: phone,
                password: password
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                successBox.innerText = data.message || 'Registration successful! Redirecting to login...';
                successBox.style.display = 'block';
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 1500);
            } else {
                errorBox.innerText = data.message || 'Registration failed.';
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