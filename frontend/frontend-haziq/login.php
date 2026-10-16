<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EventHub</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --neon-lime: #D5FF40;
            --bg-dark: #0B0F17;
            --card-bg: #131822;
            --input-bg: #1A202C;
            --border-color: #202736;
            --text-dim: #94A3B8;
        }

        body {
            background-color: var(--bg-dark);
            color: #FFFFFF;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Blurred Background Image Layer */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(180deg, rgba(11, 15, 23, 0.75) 0%, rgba(11, 15, 23, 0.90) 100%),
                        url('../../images/rockfest.jpg') center/cover no-repeat;
            filter: blur(12px);
            transform: scale(1.08); /* Scales up slightly to prevent blur edges */
            z-index: -1;
        }

        .auth-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
        }

        /* Left Side Banner Container */
        .auth-banner-wrapper {
            position: relative;
            height: 100%;
            min-height: 480px;
            overflow: hidden;
        }

        .auth-banner-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
        }

        .auth-banner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(180deg, rgba(11, 15, 23, 0.2) 0%, rgba(11, 15, 23, 0.6) 100%);
            z-index: 1;
        }

        .brand-logo {
            position: absolute;
            top: 30px;
            left: 30px;
            z-index: 2;
            font-weight: 800;
            font-size: 1.4rem;
            letter-spacing: -0.5px;
            color: #FFFFFF;
        }

        .brand-logo span {
            color: var(--neon-lime);
        }

        /* Right Side Form Container */
        .auth-form-container {
            padding: 40px;
        }

        .auth-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 24px;
            color: #FFFFFF;
        }

        .form-control-custom {
            background-color: var(--input-bg);
            border: 1px solid var(--border-color);
            color: #FFFFFF;
            border-radius: 12px;
            padding: 12px 16px 12px 42px;
            font-size: 0.95rem;
        }

        .form-control-custom:focus {
            background-color: var(--input-bg);
            border-color: var(--neon-lime);
            color: #FFFFFF;
            box-shadow: 0 0 0 0.25rem rgba(213, 255, 64, 0.15);
        }

        .input-icon-group {
            position: relative;
            margin-bottom: 16px;
        }

        .input-icon-group .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-dim);
            z-index: 5;
        }

        .input-icon-group .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-dim);
            cursor: pointer;
            z-index: 5;
        }

        .form-check-input {
            background-color: var(--input-bg);
            border-color: var(--border-color);
        }

        .form-check-input:checked {
            background-color: var(--neon-lime);
            border-color: var(--neon-lime);
        }

        .forgot-link {
            color: #A855F7;
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.2s;
        }

        .forgot-link:hover {
            color: #C084FC;
            text-decoration: underline;
        }

        .btn-lime {
            background-color: var(--neon-lime);
            color: #000000;
            font-weight: 700;
            border: none;
            border-radius: 12px;
            padding: 12px 28px;
            transition: all 0.25s ease;
            width: 100%;
        }

        .btn-lime:hover {
            background-color: #c4f52c;
            color: #000000;
            box-shadow: 0 0 20px rgba(213, 255, 64, 0.4);
            transform: translateY(-2px);
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            color: var(--text-dim);
            font-size: 0.8rem;
            margin: 24px 0;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid var(--border-color);
        }

        .divider span {
            padding: 0 10px;
        }

        .social-btn {
            background-color: var(--input-bg);
            border: 1px solid var(--border-color);
            color: #FFFFFF;
            border-radius: 12px;
            padding: 10px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .social-btn:hover {
            border-color: var(--text-dim);
            background-color: rgba(255, 255, 255, 0.05);
            color: #FFFFFF;
        }
    </style>
</head>
<body>

    <div class="card auth-card">
        <div class="row g-0">
            <!-- Left Banner / Image Side -->
            <div class="col-md-5 d-none d-md-block">
                <div class="auth-banner-wrapper">
                    <!-- Brand Title -->
                    <div class="brand-logo">
                        <i class="fa-solid fa-bolt me-2 text-warning"></i>EVENTHUB<span>.</span>
                    </div>
                    <!-- Direct Image Element -->
                    <img src="../../images/rockfest.jpg" alt="EventHub Login Banner" class="auth-banner-img" onerror="this.src='images/rockfest.jpg'">
                    <div class="auth-banner-overlay"></div>
                </div>
            </div>

            <!-- Right Form Side -->
            <div class="col-md-7">
                <div class="auth-form-container">
                    <h3 id="formTitle" class="auth-title text-center text-md-start">Log In to EventHub</h3>
                    
                    <form id="authForm">
                        <!-- Full Name (Registration Mode Only) -->
                        <div id="nameField" class="input-icon-group d-none">
                            <i class="fa-regular fa-user input-icon"></i>
                            <input type="text" id="fullName" class="form-control form-control-custom" placeholder="Full Name">
                        </div>

                        <!-- Email -->
                        <div class="input-icon-group">
                            <i class="fa-regular fa-user input-icon"></i>
                            <input type="email" id="email" class="form-control form-control-custom" placeholder="Email address" required>
                        </div>

                        <!-- Password -->
                        <div class="input-icon-group">
                            <i class="fa-solid fa-lock input-icon"></i>
                            <input type="password" id="password" class="form-control form-control-custom" placeholder="Password" required>
                            <i class="fa-regular fa-eye-slash toggle-password" onclick="togglePasswordVisibility()"></i>
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div id="loginOptions" class="d-flex justify-content-between align-items-center mb-4 fs-7">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="rememberMe">
                                <label class="form-check-label text-secondary small" for="rememberMe">Remember me</label>
                            </div>
                            <a href="#" class="forgot-link">Forgot password?</a>
                        </div>

                        <!-- Neon Lime Submit Button -->
                        <button type="submit" id="submitBtn" class="btn btn-lime">Log In</button>
                    </form>

                    <!-- Toggle Register / Login Mode -->
                    <div class="text-center mt-3">
                        <button id="toggleMode" class="btn btn-link text-secondary btn-sm text-decoration-none" onclick="toggleAuthMode()">
                            Don't have an account? <span style="color: var(--neon-lime)">Register here</span>
                        </button>
                    </div>

                    <div id="authAlert" class="mt-3"></div>

                    <!-- Social Logins Section -->
                    <div class="divider">
                        <span>Or continue with:</span>
                    </div>

                    <div class="row g-2">
                        <div class="col-4">
                            <button class="btn social-btn"><i class="fa-brands fa-google"></i></button>
                        </div>
                        <div class="col-4">
                            <button class="btn social-btn"><i class="fa-brands fa-apple"></i></button>
                        </div>
                        <div class="col-4">
                            <button class="btn social-btn"><i class="fa-brands fa-facebook-f"></i></button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        let isRegisterMode = false;

        function togglePasswordVisibility() {
            const pwdInput = document.getElementById('password');
            const icon = document.querySelector('.toggle-password');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            } else {
                pwdInput.type = 'password';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            }
        }

        function toggleAuthMode() {
            isRegisterMode = !isRegisterMode;
            document.getElementById('formTitle').innerText = isRegisterMode ? 'Create Account' : 'Log In to EventHub';
            document.getElementById('submitBtn').innerText = isRegisterMode ? 'Register Account' : 'Log In';
            document.getElementById('nameField').classList.toggle('d-none', !isRegisterMode);
            document.getElementById('loginOptions').classList.toggle('d-none', isRegisterMode);
            
            document.getElementById('toggleMode').innerHTML = isRegisterMode ? 
                'Already have an account? <span style="color: var(--neon-lime)">Log In</span>' : 
                'Don\'t have an account? <span style="color: var(--neon-lime)">Register here</span>';
            
            document.getElementById('authAlert').innerHTML = '';
        }

        document.getElementById('authForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            let email = document.getElementById('email').value;
            let password = document.getElementById('password').value;
            let fullName = document.getElementById('fullName').value;

            let payload = isRegisterMode ? 
                { action: 'register', full_name: fullName, email: email, password: password } : 
                { email: email, password: password };

            fetch('../../backend/api_auth.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(async res => {
                let text = await res.text();
                try { return JSON.parse(text); } 
                catch(err) { throw new Error("Server Response: " + text); }
            })
            .then(data => {
                if(data.status === 'success') {
                    if(isRegisterMode) {
                        alert('Registration successful! Please sign in.');
                        toggleAuthMode();
                    } else {
                        localStorage.setItem('auth_token', data.token);
                        localStorage.setItem('user_role', data.role);
                        localStorage.setItem('user_name', data.full_name);
                        window.location.href = 'index.php';
                    }
                } else {
                    document.getElementById('authAlert').innerHTML = `<div class="alert alert-danger py-2 small bg-dark border-danger text-danger">${data.message}</div>`;
                }
            })
            .catch(err => {
                document.getElementById('authAlert').innerHTML = `<div class="alert alert-danger py-2 small bg-dark border-danger text-danger">${err.message}</div>`;
            });
        });
    </script>
</body>
</html>