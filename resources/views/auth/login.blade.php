{{--
==================================================================================
📍 FILE LOCATION: resources/views/auth/login.blade.php
==================================================================================

INSTRUCTIONS:
1. Go to your Laravel project folder
2. Navigate to: resources/views/auth/
3. Create a file named: login.blade.php
4. Paste ALL the code below into that file
5. Save the file

FULL PATH: resources/views/auth/login.blade.php

⚠️ IMPORTANT: Make sure you also have:
   - login.css in public/css/login.css
   - cvsu-logo.png in public/images/cvsu-logo.png

==================================================================================
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CAReERS Login | CVSU</title>
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

        /* ═══════════════════════════════════════════════
           FIX: CIRCULAR LOGO — no corners
        ═══════════════════════════════════════════════ */
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
                    <div class="logo-wrapper" style="width: 180px; height: 180px;">
                        <img src="{{ asset('images/cvsu-logo.png') }}" alt="CVSU Logo" class="brand-logo" style="width: 180px; height: 180px;">
                    </div>
                    <h1 class="brand-title" style="font-size: 56px; letter-spacing: 4px;">CAReERS</h1>
                    <p class="brand-subtitle">Job Recommendation System</p>
                    <p class="brand-description">Cavite State University &mdash; Carmona Campus</p>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="login-form-container">
            <div class="login-form-wrapper">
                <div class="form-header">
                    <h2>Welcome Back</h2>
                    <p>Please login to your account</p>
                </div>

                <!-- Login Form -->
                <form id="loginForm" onsubmit="handleLogin(event)">
                    <div class="form-group">
                        <label for="loginIdentifier">
                            <span class="material-symbols-outlined">email</span>
                            CvSU Email or Student Number
                        </label>
                        <input 
                            type="text" 
                            id="loginIdentifier" 
                            class="form-input" 
                            placeholder="your.email@cvsu.edu.ph or 202699991"
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
                        <a href="#" class="forgot-password">Forgot Password?</a>
                    </div>

                    <button type="submit" class="btn-login">
                        <span class="material-symbols-outlined">login</span>
                        Login
                    </button>
                </form>

                <div class="form-footer">
                    <p>Don't have an account? <a href="/register" class="link-register">Register here</a></p>
                </div>

                <div class="divider">
                    <span>OR</span>
                </div>

                <div class="social-login">
                    <button class="btn-social google">
                        <svg width="18" height="18" viewBox="0 0 18 18">
                            <path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z"/>
                            <path fill="#34A853" d="M9.003 18c2.43 0 4.467-.806 5.956-2.184l-2.908-2.258c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332C2.438 15.983 5.482 18 9.003 18z"/>
                            <path fill="#FBBC05" d="M3.964 10.71c-.18-.54-.282-1.117-.282-1.71s.102-1.17.282-1.71V4.958H.957C.347 6.173 0 7.548 0 9.001c0 1.452.348 2.827.957 4.041l3.007-2.332z"/>
                            <path fill="#EA4335" d="M9.003 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.464.891 11.426 0 9.002 0 5.482 0 2.438 2.017.957 4.958L3.964 7.29c.708-2.127 2.692-3.71 5.036-3.71z"/>
                        </svg>
                        Continue with Google
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/system_popup.js') }}"></script>
    <script>
        // Toggle password visibility
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

        // Handle login form submission
        function handleLogin(event) {
            event.preventDefault();
            
            const login = document.getElementById('loginIdentifier').value;
            const password = document.getElementById('loginPassword').value;
            const remember = document.getElementById('rememberMe').checked;
            
            const submitBtn = document.querySelector('.btn-login');
            const originalHTML = submitBtn.innerHTML;
            
            // Show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="material-symbols-outlined">hourglass_empty</span> Logging in...';
            
            // Get CSRF token
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Send login request
            fetch('/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    login: login,
                    password: password,
                    remember: remember
                })
            })
            .then(response => response.json())
            .then(data => {
            if (data.success) {
                    document.querySelector('.login-container').style.display = 'none';
                    document.body.style.background = '#fff';
                    window.location.href = data.redirect || '/student/dashboard';                } else {
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