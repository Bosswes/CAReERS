<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KABSCHOLAR Admin Login | CVSU</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        html, body {
            height: 100%;
            overflow: hidden;
        }

        body {
            padding: 0;
        }

        .login-container {
            width: 100vw;
            max-width: 100vw;
            min-height: 100vh;
            height: 100vh;
            border-radius: 0;
        }

        .login-form-container {
            overflow: hidden;
        }

        .logo-wrapper {
            border-radius: 50% !important;
            overflow: hidden !important;
        }

        .brand-logo {
            border-radius: 50% !important;
            object-fit: cover !important;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Side - Branding -->
        <div class="login-brand">
            <div class="brand-overlay">
                <div class="brand-content">
                    <div class="logo-wrapper">
                        <img src="{{ asset('images/cvsu-logo.png') }}" alt="CVSU Logo" class="brand-logo">
                    </div>
                    <h1 class="brand-title">KABSCHOLAR</h1>
                    <p class="brand-subtitle">Admin Access Portal</p>
                    <p class="brand-description">Authorized users only</p>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="login-form-container">
            <div class="login-form-wrapper">
                <div class="form-header">
                    <h2>Admin Login</h2>
                    <p>Enter your admin credentials</p>
                </div>

                <form id="adminLoginForm" onsubmit="handleAdminLogin(event)">
                    <div class="form-group">
                        <label for="loginEmail">
                            <span class="material-symbols-outlined">email</span>
                            Admin Email
                        </label>
                        <input 
                            type="email" 
                            id="loginEmail" 
                            class="form-input" 
                            placeholder="admin@cvsu.edu.ph"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="loginPassword">
                            <span class="material-symbols-outlined">lock</span>
                            Password
                        </label>
                        <div class="password-wrapper">
                            <input 
                                type="password" 
                                id="loginPassword" 
                                class="form-input" 
                                placeholder="Enter your password"
                                required
                            >
                            <span class="material-symbols-outlined password-toggle" onclick="togglePassword('loginPassword')">
                                visibility_off
                            </span>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" id="rememberMe">
                            <span>Remember me</span>
                        </label>
                    </div>

                    <button type="submit" class="btn-login">
                        <span class="material-symbols-outlined">login</span>
                        Admin Login
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/system_popup.js') }}"></script>
    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const toggle = field.nextElementSibling;
            
            if (field.type === 'password') {
                field.type = 'text';
                toggle.textContent = 'visibility';
            } else {
                field.type = 'password';
                toggle.textContent = 'visibility_off';
            }
        }

        function handleAdminLogin(event) {
            event.preventDefault();
            
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            const remember = document.getElementById('rememberMe').checked;
            
            const submitBtn = document.querySelector('.btn-login');
            const originalHTML = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="material-symbols-outlined">hourglass_empty</span> Logging in...';
            
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            fetch('{{ route('admin.login.submit') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    email: email,
                    password: password,
                    remember: remember
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelector('.login-container').style.display = 'none';
                    document.body.style.background = '#fff';
                    window.location.href = data.redirect || '/admin/dashboard';
                } else {
                    alert(data.message || 'Login failed. Please try again.');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHTML;
                }
            })
            .catch(error => {
                console.error('Login error:', error);
                alert('An error occurred during login. Please try again.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHTML;
            });
        }
    </script>
</body>
</html>
