<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - EventHub</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card { background: #ffffff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); width: 380px; text-align: center; }
        .logo { font-size: 24px; font-weight: bold; color: #2563eb; margin-bottom: 5px; }
        .subtitle { font-size: 13px; color: #6b7280; margin-bottom: 20px; }
        .alert-danger { background-color: #fee2e2; color: #dc2626; padding: 10px; border-radius: 6px; font-size: 13px; margin-bottom: 15px; text-align: left; display: none; }
        .alert-success { background-color: #d1fae5; color: #065f46; padding: 10px; border-radius: 6px; font-size: 13px; margin-bottom: 15px; text-align: left; display: none; }
        .form-group { text-align: left; margin-bottom: 15px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 5px; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; box-sizing: border-box; background-color: #f9fafb; }
        
        /* Override Chrome/Edge Autofill Blue Background */
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px #f9fafb inset !important;
            -webkit-text-fill-color: #1f2937 !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        .btn-submit { width: 100%; padding: 11px; background-color: #2563eb; border: none; color: white; font-weight: bold; border-radius: 6px; cursor: pointer; font-size: 14px; margin-top: 10px; }
        .btn-submit:hover { background-color: #1d4ed8; }
        .footer-link { font-size: 13px; color: #6b7280; margin-top: 20px; }
        .footer-link a { color: #2563eb; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

    <div class="card">
        <div class="logo">🎟️ EventHub</div>
        <div class="subtitle">Create an account to book events</div>

        <div id="errorAlert" class="alert-danger"></div>
        <div id="successAlert" class="alert-success"></div>

        <form id="registerForm" autocomplete="off">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" id="fullName" name="reg_fullname_nofill" autocomplete="off" required placeholder="e.g. Farid Hakim">
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="text" id="email" name="reg_email_nofill" autocomplete="one-time-code" required placeholder="name@example.com">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" id="password" name="reg_password_nofill" autocomplete="new-password" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn-submit">Register Account</button>
        </form>

        <div class="footer-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const errorAlert = document.getElementById('errorAlert');
            const successAlert = document.getElementById('successAlert');
            errorAlert.style.display = 'none';
            successAlert.style.display = 'none';

            const full_name = document.getElementById('fullName').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            try {
                const res = await fetch('api_auth.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'register', full_name, email, password })
                });

                const data = await res.json();

                if (data.status === 'success') {
                    successAlert.innerHTML = data.message + " You can now <a href='login.php'>Login</a>.";
                    successAlert.style.display = 'block';
                    document.getElementById('registerForm').reset();
                } else {
                    errorAlert.innerText = data.message;
                    errorAlert.style.display = 'block';
                }
            } catch (err) {
                errorAlert.innerText = "Server error processing registration.";
                errorAlert.style.display = 'block';
            }
        });
    </script>
</body>
</html>