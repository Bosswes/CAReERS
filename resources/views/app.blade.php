<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CvSU Job Recommendation System</title>
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    
    <!-- External Libraries -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <!-- CRITICAL FIXES FOR LOGIN FORM LAYOUT -->
    <style>
        /* ===== LOGIN FORM CRITICAL FIXES ===== */
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            font-family: 'Inter', sans-serif;
        }
        
        .auth-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 450px;
            padding: 40px !important;
            position: relative;
            z-index: 1;
            margin: 0 auto;
            box-sizing: border-box !important;
            border-top: 4px solid #2E7D32;
        }
        
        .form-group {
            width: 100% !important;
            margin-bottom: 24px !important;
            position: relative !important;
            box-sizing: border-box !important;
        }
        
        .form-label {
            display: block !important;
            margin-bottom: 8px !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            color: #475569 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
        }
        
        .input-with-icon {
            position: relative !important;
            width: 100% !important;
            display: block !important;
        }
        
        .input-with-icon i {
            position: absolute !important;
            left: 16px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            color: #64748b !important;
            font-size: 18px !important;
            z-index: 2 !important;
            pointer-events: none !important;
            width: 20px !important;
            text-align: center !important;
        }
        
        .input-with-icon input {
            width: 100% !important;
            padding: 16px 50px 16px 48px !important;
            border: 2px solid #e2e8f0 !important;
            border-radius: 14px !important;
            font-size: 15px !important;
            font-family: 'Inter', sans-serif !important;
            color: #1e293b !important;
            background-color: white !important;
            transition: all 0.2s ease !important;
            font-weight: 500 !important;
            box-sizing: border-box !important;
            height: 56px !important;
            line-height: normal !important;
            display: block !important;
        }
        
        .input-with-icon input:focus {
            outline: none !important;
            border-color: #2E7D32 !important;
            box-shadow: 0 0 0 4px rgba(46, 125, 50, 0.15) !important;
        }
        
        .input-with-icon input::placeholder {
            color: #94a3b8 !important;
            font-weight: 400 !important;
            font-size: 14px !important;
        }
        
        .toggle-password {
            position: absolute !important;
            right: 12px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            background: none !important;
            border: none !important;
            color: #64748b !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            z-index: 3 !important;
            font-size: 18px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 40px !important;
            height: 40px !important;
            border-radius: 8px !important;
            padding: 0 !important;
        }
        
        .toggle-password:hover {
            color: #2E7D32 !important;
            background-color: #f1f5f9 !important;
        }
        
        .btn-login-account {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 100% !important;
            padding: 0 16px !important;
            background: #2E7D32 !important;
            color: white !important;
            border: none !important;
            border-radius: 14px !important;
            font-size: 16px !important;
            font-weight: 700 !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            margin: 20px 0 20px !important;
            text-transform: uppercase !important;
            letter-spacing: 1px !important;
            box-shadow: 0 4px 15px rgba(46, 125, 50, 0.3) !important;
            height: 56px !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        
        .btn-login-account:hover {
            background: #1B5E20 !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 20px rgba(46, 125, 50, 0.4) !important;
        }
        
        .auth-links {
            display: flex !important;
            justify-content: center !important;
            margin-bottom: 25px !important;
            width: 100% !important;
        }
        
        .forgot-password {
            font-size: 14px !important;
            color: #2E7D32 !important;
            text-decoration: none !important;
            font-weight: 600 !important;
            text-align: center !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            padding: 8px 16px !important;
            cursor: pointer !important;
            display: inline-block !important;
            border-radius: 8px !important;
            transition: all 0.2s ease !important;
        }
        
        .forgot-password:hover {
            text-decoration: underline !important;
            color: #1B5E20 !important;
            background-color: #f1f5f9 !important;
        }
        
        .demo-section {
            margin-top: 25px !important;
            padding-top: 25px !important;
            border-top: 2px solid #e2e8f0 !important;
            width: 100% !important;
        }
        
        .demo-header {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin-bottom: 20px !important;
            width: 100% !important;
        }
        
        .demo-line {
            flex: 1 !important;
            height: 2px !important;
            background: linear-gradient(90deg, transparent, #cbd5e1, transparent) !important;
        }
        
        .demo-text {
            padding: 0 15px !important;
            font-size: 13px !important;
            color: #64748b !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            white-space: nowrap !important;
        }
        
        .demo-accounts {
            background-color: #f8fafc !important;
            border-radius: 16px !important;
            padding: 20px !important;
            border-left: 4px solid #2E7D32 !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        
        .demo-account {
            font-size: 14px !important;
            color: #475569 !important;
            margin-bottom: 12px !important;
            line-height: 1.6 !important;
            padding: 6px 0 !important;
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            border-bottom: 1px dashed #cbd5e1 !important;
        }
        
        .demo-account:last-child {
            margin-bottom: 0 !important;
            border-bottom: none !important;
        }
        
        .demo-account strong {
            color: #2E7D32 !important;
            font-weight: 700 !important;
            min-width: 70px !important;
            display: inline-block !important;
            font-size: 14px !important;
        }
        
        .demo-account span {
            font-family: monospace !important;
            background: #e2e8f0 !important;
            padding: 2px 8px !important;
            border-radius: 4px !important;
            font-size: 13px !important;
        }
        
        /* Responsive fixes */
        @media (max-width: 480px) {
            .school-logo {
                width: 38px !important;
                height: 38px !important;
                padding: 4px !important;
            }
            
            .logo-text h1 {
                font-size: 14px !important;
            }
            
            .logo-text p {
                font-size: 8px !important;
            }
        }

        /* ===== RESUME STYLES ===== */
        .btn-print-resume {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 14px;
            background: #003366;
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 16px;
            transition: all 0.3s ease;
        }
        .btn-print-resume:hover { background: #002244; transform: translateY(-2px); }
        .resume-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .resume-modal.show { display: flex; }
        .resume-wrapper {
            background: white;
            width: 210mm;
            max-height: 95vh;
            overflow-y: auto;
            border-radius: 16px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
        }
        .resume-actions {
            display: flex;
            gap: 10px;
            padding: 16px 20px;
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
            border-radius: 16px 16px 0 0;
            justify-content: space-between;
            align-items: center;
        }
        .resume-actions span { font-weight: 700; color: #1e293b; font-size: 15px; }
        .resume-actions-btns { display: flex; gap: 10px; }
        #resume-content {
            padding: 20px 30px;
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            width: 210mm;
            min-height: 297mm;
            max-height: 297mm;
            overflow: hidden;
            box-sizing: border-box;
            font-size: 12px;
        }
        .resume-header { border-bottom: 2px solid #2E7D32; padding-bottom: 10px; margin-bottom: 12px; }
        .resume-header h1 { font-size: 20px; font-weight: 800; color: #1e293b; margin: 0 0 4px; text-transform: uppercase; letter-spacing: 1px; }
        .resume-header p { font-size: 11px; color: #64748b; margin: 2px 0; }
        .resume-section { margin-bottom: 10px; }
        .resume-section-title { font-size: 10px; font-weight: 800; color: #2E7D32; text-transform: uppercase; letter-spacing: 1.5px; border-bottom: 1px solid #d1fae5; padding-bottom: 4px; margin-bottom: 8px; }
        .resume-row { display: grid; grid-template-columns: 1fr 1fr; gap: 6px 16px; margin-bottom: 6px; }
        .resume-field label { font-size: 9px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.8px; display: block; margin-bottom: 1px; }
        .resume-field span { font-size: 11px; color: #1e293b; font-weight: 500; }
        .resume-skills { display: flex; flex-wrap: wrap; gap: 4px; margin-top: 4px; }
        .resume-skill-tag { background: #d1fae5; color: #065f46; padding: 2px 8px; border-radius: 20px; font-size: 10px; font-weight: 600; }
        .resume-school-item { display: grid; grid-template-columns: 1fr auto; gap: 2px; margin-bottom: 6px; padding-bottom: 6px; border-bottom: 1px dashed #e2e8f0; }
        .resume-school-item:last-child { border-bottom: none; }
        .resume-school-name { font-size: 11px; font-weight: 600; color: #1e293b; }
        .resume-school-level { font-size: 10px; color: #08408f; }
        .resume-school-year { font-size: 11px; color: #2E7D32; font-weight: 600; text-align: right; }
        @media print {
            body * { visibility: hidden; }
            #resume-content, #resume-content * { visibility: visible; }
            #resume-content {
                position: fixed;
                top: 0; left: 0;
                width: 210mm;
                padding: 15mm 20mm;
                font-size: 11px;
                max-height: none;
                overflow: visible;
            }
            .resume-modal { display: block !important; }
            .resume-actions { display: none !important; }
            @page { size: A4; margin: 0; }
            #resume-content input {
                border: none !important;
                background: transparent !important;
                font-size: 13px !important;
                font-weight: 500 !important;
                color: #1e293b !important;
                padding: 0 !important;
                width: auto !important;
            }
            #resume-content p[style*="color:#94a3b8"] { display: none !important; }
        }

        /* Responsive fixes */
        @media (max-width: 480px) {
            .auth-card {
                padding: 30px 25px !important;
            }
            
            .input-with-icon input {
                padding: 14px 45px 14px 45px !important;
                height: 52px !important;
                font-size: 14px !important;
            }
            
            .btn-login-account {
                height: 52px !important;
                font-size: 14px !important;
            }
            
            .demo-account {
                font-size: 13px !important;
                flex-wrap: wrap !important;
            }
            
            .demo-account strong {
                min-width: 65px !important;
            }
        }
        
        @media (max-width: 360px) {
            .auth-card {
                padding: 20px 15px !important;
            }
            
            .demo-account {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 5px !important;
            }
            
            .input-with-icon input {
                padding: 12px 40px 12px 40px !important;
                font-size: 13px !important;
            }
            
            .btn-login-account {
                height: 48px !important;
                font-size: 13px !important;
            }
        }
        
        /* Sidebar fixes */
        .sidebar-header {
            padding: 20px 16px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15) !important;
        }
        
        .logo-section {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            flex: 1 !important;
            min-width: 0 !important;
        }
        
        .school-logo {
            width: 45px !important;
            height: 45px !important;
            flex-shrink: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background-color: white !important;
            border-radius: 10px !important;
            overflow: hidden !important;
            padding: 6px !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2) !important;
        }
        
        .school-logo img {
            width: 100% !important;
            height: 100% !important;
            object-fit: contain !important;
            display: block !important;
        }
        
        .logo-text {
            flex: 1 !important;
            min-width: 0 !important;
        }
        
        .logo-text h1 {
            font-size: 16px !important;
            font-weight: 700 !important;
            color: white !important;
            margin-bottom: 2px !important;
            line-height: 1.2 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            letter-spacing: 0.5px !important;
        }
        
        .logo-text p {
            font-size: 10px !important;
            color: rgba(255, 255, 255, 0.9) !important;
            line-height: 1.2 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            letter-spacing: 0.3px !important;
            font-weight: 500 !important;
        }
        
        .sidebar-toggle {
            display: none !important;
            background: none !important;
            border: none !important;
            color: white !important;
            font-size: 18px !important;
            cursor: pointer !important;
            padding: 8px !important;
            border-radius: 8px !important;
            transition: all 0.3s ease !important;
            flex-shrink: 0 !important;
        }
        
        @media (max-width: 768px) {
            .sidebar-toggle {
                display: block !important;
            }
        }

        /* ===== RESUME STYLES ===== */
        .btn-print-resume {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 14px;
            background: #003366;
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 16px;
            transition: all 0.3s ease;
        }
        .btn-print-resume:hover {
            background: #002244;
            transform: translateY(-2px);
        }
        .resume-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .resume-modal.show {
            display: flex;
        }
        .resume-wrapper {
            background: white;
            width: 100%;
            max-width: 780px;
            max-height: 90vh;
            overflow-y: auto;
            border-radius: 16px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
        }
        .resume-actions {
            display: flex;
            gap: 10px;
            padding: 16px 20px;
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
            border-radius: 16px 16px 0 0;
            justify-content: space-between;
            align-items: center;
        }
        .resume-actions span {
            font-weight: 700;
            color: #1e293b;
            font-size: 15px;
        }
        .resume-actions-btns {
            display: flex;
            gap: 10px;
        }
        #resume-content {
            padding: 40px 50px;
            font-family: 'Inter', sans-serif;
            color: #1e293b;
        }
        .resume-header {
            text-align: center;
            border-bottom: 3px solid #2E7D32;
            padding-bottom: 20px;
            margin-bottom: 24px;
        }
        .resume-header h1 {
            font-size: 28px;
            font-weight: 800;
            color: #1e293b;
            margin: 0 0 6px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .resume-header p {
            font-size: 14px;
            color: #64748b;
            margin: 3px 0;
        }
        .resume-section {
            margin-bottom: 22px;
        }
        .resume-section-title {
            font-size: 13px;
            font-weight: 800;
            color: #2E7D32;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            border-bottom: 1.5px solid #d1fae5;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .resume-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 20px;
            margin-bottom: 8px;
        }
        .resume-field label {
            font-size: 10px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            display: block;
            margin-bottom: 2px;
        }
        .resume-field span {
            font-size: 14px;
            color: #1e293b;
            font-weight: 500;
        }
        .resume-skills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
        }
        .resume-skill-tag {
            background: #d1fae5;
            color: #065f46;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .resume-school-item {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 4px;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #e2e8f0;
        }
        .resume-school-item:last-child {
            border-bottom: none;
        }
        .resume-school-name {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
        }
        .resume-school-level {
            font-size: 11px;
            color: #64748b;
        }
        .resume-school-year {
            font-size: 13px;
            color: #2E7D32;
            font-weight: 600;
            text-align: right;
        }
        @media print {
            body * { visibility: hidden; }
            #resume-content, #resume-content * { visibility: visible; }
            #resume-content {
                position: fixed;
                top: 0; left: 0;
                width: 100%;
                padding: 30px 50px;
            }
            .resume-modal { display: block !important; }
            .resume-actions { display: none !important; }
        }
    </style>
</head>
<body>
    <!-- Auth Container with University Background -->
<div id="auth-container" class="auth-container" style="display:none;"style="background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('/images/university.png'); background-size: cover; background-position: center; background-repeat: no-repeat; background-attachment: fixed;">
        <!-- Login Card -->
        <div class="auth-card" id="login-card">
            <div class="osas-header">
                <div class="osas-text">
                    <div class="osas-line-1">CAReERS</div>
                    <div class="osas-line-2">JOB RECOMMENDATION SYSTEM</div>
                </div>
            </div>
            
            <div class="login-portal-title">
                <div class="portal-title-line">LOGIN PORTAL</div>
            </div>
            
            <form id="login-form" class="auth-form">
                <!-- Username/Email Field -->
                <div class="form-group">
                    <label for="login-student-id" class="form-label">USERNAME / EMAIL</label>
                    <div class="input-with-icon">
                        <i class="fas fa-id-card"></i>
                        <input type="text" id="login-student-id" placeholder="Enter your username or email" required>
                    </div>
                </div>
                
                <!-- Password Field -->
                <div class="form-group">
                    <label for="login-password" class="form-label">PASSWORD</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="login-password" placeholder="Enter your password" required>
                        <button type="button" class="toggle-password" id="toggle-password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Login Button -->
                <button type="submit" class="btn-login-account">
                    LOGIN TO DASHBOARD
                </button>
                
                <!-- Forgot Password Link -->
                <div class="auth-links">
                    <a href="#" class="forgot-password" id="forgot-password-link">FORGOT PASSWORD?</a>
                </div>
                
                <!-- Demo Accounts Section -->
                <div class="demo-section">
                    <div class="demo-header">
                        <div class="demo-line"></div>
                        <span class="demo-text">DEMO ACCOUNTS</span>
                        <div class="demo-line"></div>
                    </div>
                    <div class="demo-accounts">
                        <div class="demo-account">
                            <strong>Student:</strong> juan.delacruz@cvsu.edu.ph / student123
                        </div>
                        <div class="demo-account">
                            <strong>Employer:</strong> employer@company.com / demo123
                        </div>
                        <div class="demo-account">
                            <strong>Admin:</strong> admin / admin123
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Application Container -->
    <div id="app-container" class="app-container">
        <div class="app-layout">
            <!-- Sidebar with Fixed Logo -->
            <aside class="sidebar">
                <div class="sidebar-header">
                    <div class="logo-section">
                        <div class="school-logo">
                            <img src="{{ asset('images/cvsu.png') }}" alt="CvSU Logo" onerror="this.parentElement.classList.add('fallback'); this.style.display='none'; this.parentElement.innerHTML='C';">
                        </div>
                        <div class="logo-text">
                            <h1>CAReERS</h1>
                            <p>JOB RECOMMENDATION SYSTEM</p>
                        </div>
                    </div>
                    
                    <button class="sidebar-toggle" id="sidebar-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
                
                <nav class="sidebar-nav">
                    <ul id="sidebar-menu"></ul>
                </nav>
                
                <div class="sidebar-footer">
                    <div class="user-info">
                        <div class="user-avatar">
                            <span id="sidebar-avatar-initials">U</span>
                        </div>
                        <div class="user-details">
                            <span class="user-name" id="sidebar-user-name">User</span>
                            <span class="user-role" id="sidebar-user-role">Role</span>
                        </div>
                    </div>
                    <button class="logout-btn" id="sidebar-logout-btn" title="Logout" onclick="fetch('/logout', {method:'POST', headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content}}).then(res=>res.json()).then(data=> window.location.href=data.redirect || '/login')">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </div>
            </aside>

            <!-- Main Content Area -->
            <div class="main-content">
                <!-- Mobile Header -->
                <header class="mobile-header">
                    <div class="container">
                        <div class="mobile-header-content">
                            <button class="mobile-menu-toggle" id="mobile-menu-toggle">
                                <i class="fas fa-bars"></i>
                            </button>
                            <div class="mobile-logo">
                                <h1>CAReERS</h1>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Main Content -->
                <main class="app-main">
                    <div class="container">
                        <!-- ========== STUDENT DASHBOARD ========== -->
                        <div id="student-dashboard" class="dashboard-section">
                            <div class="dashboard-header">
                                <h2>Student Dashboard</h2>
                                <p class="dashboard-subtitle">Welcome back! Track your job applications and recommendations.</p>
                            </div>
                            
                            <div class="profile-completion-card" id="profile-completion-card">
                                <div class="completion-header">
                                    <h3>Profile Completion</h3>
                                    <span class="completion-percentage" id="completion-percentage">0%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" id="profile-progress" style="width: 0%;"></div>
                                </div>
                                <p class="completion-hint">Complete your profile to unlock personalized job recommendations</p>
                                <a href="#student-profile" class="btn-primary btn-sm complete-profile-btn">Complete Profile</a>
                            </div>
                            
                            <div class="stats-grid">
                                <div class="stat-card">
                                    <div class="stat-icon"><i class="fas fa-briefcase"></i></div>
                                    <div class="stat-content">
                                        <h3 class="stat-number" id="available-jobs-count">0</h3>
                                        <p class="stat-label">Available Jobs</p>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon"><i class="fas fa-paper-plane"></i></div>
                                    <div class="stat-content">
                                        <h3 class="stat-number" id="applications-count">0</h3>
                                        <p class="stat-label">Applications</p>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon"><i class="fas fa-star"></i></div>
                                    <div class="stat-content">
                                        <h3 class="stat-number" id="avg-match">0%</h3>
                                        <p class="stat-label">Avg Match Score</p>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon"><i class="fas fa-bullhorn"></i></div>
                                    <div class="stat-content">
                                        <h3 class="stat-number" id="announcements-count">0</h3>
                                        <p class="stat-label">Announcements</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="job-recommendations-section" style="display: none;">
                                <div class="section-header">
                                    <h3>Top Recommended Jobs</h3>
                                    <p>Based on your profile and skills</p>
                                </div>
                                <div class="jobs-horizontal-scroll" id="recommended-jobs-scroll"></div>
                            </div>
                            
                            <div class="section-header">
                                <h3>Quick Access</h3>
                            </div>
                            
                            <div class="actions-grid">
                                <a href="#student-profile" class="action-card">
                                    <div class="action-icon"><i class="fas fa-user-circle"></i></div>
                                    <div class="action-content">
                                        <h4>My Profile</h4>
                                        <p>Update your information and skills</p>
                                    </div>
                                    <div class="action-arrow"><i class="fas fa-chevron-right"></i></div>
                                </a>
                                <a href="#job-recommendations" class="action-card" id="recommendations-action-link">
                                    <div class="action-icon"><i class="fas fa-magic"></i></div>
                                    <div class="action-content">
                                        <h4>Job Recommendations</h4>
                                        <p>Get personalized job matches</p>
                                        <span class="badge locked" id="recommendations-badge">Locked</span>
                                    </div>
                                    <div class="action-arrow"><i class="fas fa-chevron-right"></i></div>
                                </a>
                                <a href="#announcements" class="action-card">
                                    <div class="action-icon"><i class="fas fa-bullhorn"></i></div>
                                    <div class="action-content">
                                        <h4>Announcements</h4>
                                        <p>View latest updates and events</p>
                                    </div>
                                    <div class="action-arrow"><i class="fas fa-chevron-right"></i></div>
                                </a>
                                <a href="#my-applications" class="action-card">
                                    <div class="action-icon"><i class="fas fa-file-alt"></i></div>
                                    <div class="action-content">
                                        <h4>My Applications</h4>
                                        <p>Track your job applications</p>
                                    </div>
                                    <div class="action-arrow"><i class="fas fa-chevron-right"></i></div>
                                </a>
                            </div>
                        </div>

                        <!-- ========== STUDENT PROFILE ========== -->
                        <div id="student-profile" class="dashboard-section" style="display: none;">
                            <div class="dashboard-header">
                                <h2>My Profile</h2>
                                <p class="dashboard-subtitle">Update your information for better job matches</p>
                            </div>
                            
                            <div class="profile-form-container">
                                <!-- Personal Information -->
                                <div class="form-section">
                                    <h3 class="form-section-title"><i class="fas fa-user"></i> Personal Information</h3>

                                    <!-- 1x1 Photo Upload -->
                                    <div style="display:flex; align-items:center; gap:20px; margin-bottom:20px; padding:16px; background:#f8fafc; border-radius:12px; border:1px solid #e2e8f0;">
                                        <div id="profile-photo-preview" style="width:100px; height:100px; border-radius:10px; border:2px dashed #2E7D32; overflow:hidden; display:flex; align-items:center; justify-content:center; background:#f0fdf4; flex-shrink:0;">
                                            <i class="fas fa-user" style="font-size:36px; color:#94a3b8;" id="profile-photo-icon"></i>
                                        </div>
                                        <div>
                                            <div style="font-weight:600; color:#1e293b; margin-bottom:4px;">Profile Photo (1x1)</div>
                                            <div style="font-size:12px; color:#64748b; margin-bottom:10px;">Upload a clear 1x1 photo for your resume</div>
                                            <input type="file" id="profile-photo-input" accept="image/*" style="display:none;" onchange="previewProfilePhoto(event)">
                                            <button type="button" onclick="document.getElementById('profile-photo-input').click()" style="background:#2E7D32; color:white; border:none; padding:8px 16px; border-radius:8px; cursor:pointer; font-size:13px; font-weight:600;">
                                                <i class="fas fa-upload"></i> Upload Photo
                                            </button>
                                            <button type="button" onclick="removeProfilePhoto()" id="remove-photo-btn" style="display:none; background:#e2e8f0; color:#1e293b; border:none; padding:8px 16px; border-radius:8px; cursor:pointer; font-size:13px; font-weight:600; margin-left:8px;">
                                                <i class="fas fa-trash"></i> Remove
                                            </button>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="profile-name">Full Name</label>
                                            <input type="text" id="profile-name" placeholder="Enter your full name">
                                        </div>
                                        <div class="form-group">
                                            <label for="profile-email">Email Address</label>
                                            <input type="email" id="profile-email" readonly>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="profile-student-id">Student ID</label>
                                            <input type="text" id="profile-student-id" placeholder="e.g., 2024-00001" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="profile-contact">Contact Number</label>
                                            <input type="tel" id="profile-contact" placeholder="e.g., 09123456789">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Education -->
                                <div class="form-section">
                                    <h3 class="form-section-title"><i class="fas fa-graduation-cap"></i> Education</h3>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="profile-degree">Degree Program</label>
                                            <select id="profile-degree">
                                                <option value="">Select degree program</option>
                                                <option value="Bachelor of Secondary Education">Bachelor of Secondary Education</option>
                                                <option value="BS Business Management">BS Business Management</option>
                                                <option value="BS Computer Engineering">BS Computer Engineering</option>
                                                <option value="BS Computer Science">BS Computer Science</option>
                                                <option value="BS Hospitality Management">BS Hospitality Management</option>
                                                <option value="BS Industrial Technology">BS Industrial Technology</option>
                                                <option value="BS Information Technology">BS Information Technology</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="profile-year-level">Year Level</label>
                                            <select id="profile-year-level">
                                                <option value="">Select year level</option>
                                                <option value="1">1st Year</option>
                                                <option value="2">2nd Year</option>
                                                <option value="3">3rd Year</option>
                                                <option value="4">4th Year</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="profile-gwa">GWA (1.0 - 5.0)</label>
                                            <input type="number" id="profile-gwa" step="0.01" min="1.0" max="5.0" placeholder="e.g., 1.75">
                                        </div>
                                        <div class="form-group">
                                            <label for="profile-section">Section</label>
                                            <input type="text" id="profile-section" placeholder="e.g., BSCS 4A">
                                        </div>
                                    </div>
                                </div>
                                
                                

                                <!-- Skills -->
                                <div class="form-section">
                                    <h3 class="form-section-title"><i class="fas fa-tools"></i> Skills & Expertise</h3>
                                    <div class="form-group">
                                        <label for="profile-skills">Technical Skills (comma separated)</label>
                                        <textarea id="profile-skills" rows="3" placeholder="e.g., Python, JavaScript, React, SQL, Data Analysis, UI/UX Design"></textarea>
                                        <p class="text-muted mt-2">Add relevant skills to improve job matching</p>
                                    </div>
                                </div>
                                
                                <!-- Certifications -->
                                <div class="form-section">
                                    <h3 class="form-section-title"><i class="fas fa-certificate"></i> Certifications</h3>
                                    <div class="certifications-list" id="certifications-list">
                                        <p class="text-muted">No certifications added yet.</p>
                                    </div>
                                    <button type="button" class="btn-secondary btn-sm" id="add-certification" style="margin-top: 12px;">
                                        <i class="fas fa-plus"></i> Add Certification
                                    </button>
                                </div>
                                
                                <!-- Form Actions -->
                                <div class="form-actions" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
                                    <div style="display:flex; gap:12px;">
                                        <button type="button" class="btn-primary" id="save-profile">Save Profile</button>
                                        <button type="button" class="btn-secondary" id="cancel-profile">Cancel</button>
                                    </div>
                                     <button type="button" class="btn-print-resume" id="view-resume-btn" style="width:auto; margin-top:0;" onclick="openResumeModal()">
                                    <i class="fas fa-file-alt"></i> View &amp; Print Resume
                                    </button>
                                </div>
                                </div>
                            </div>
                        </div>

                        <!-- ========== JOB RECOMMENDATIONS ========== -->
                        <div id="job-recommendations" class="dashboard-section" style="display: none;">
                            <div class="dashboard-header">
                                <h2>Job Recommendations</h2>
                                <p class="dashboard-subtitle">Personalized job matches based on your profile</p>
                            </div>
                            
                            <div class="profile-completion-warning" id="profile-warning" style="display: block;">
                                <div class="warning-content">
                                    <i class="fas fa-lock"></i>
                                    <div>
                                        <h3>Profile Incomplete</h3>
                                        <p>Complete your profile to unlock job recommendations</p>
                                        <a href="#student-profile" class="btn-primary btn-sm">Complete Profile Now</a>
                                    </div>
                                </div>
                            </div>
                            
                            
                            
                            <div class="jobs-grid" id="jobs-grid"></div>
                        </div>

                        <!-- ========== MY APPLICATIONS ========== -->
                        <div id="my-applications" class="dashboard-section" style="display: none;">
                            <div class="dashboard-header">
                                <h2>My Applications</h2>
                                <p class="dashboard-subtitle">Track your job applications</p>
                            </div>
                            
                            <div class="applications-list" id="applications-list">
                                <p class="text-muted">No applications yet. Browse jobs and apply!</p>
                            </div>
                        </div>

                        <!-- ========== ANNOUNCEMENTS (STUDENT VIEW) ========== -->
                        <div id="announcements" class="dashboard-section" style="display: none;">
                            <div class="dashboard-header">
                                <h2>Announcements</h2>
                                <p class="dashboard-subtitle">Stay updated with career events and opportunities</p>
                            </div>
                            
                            <div class="announcements-grid" id="student-announcements-grid"></div>
                        </div>

                        <!-- ========== EMPLOYER DASHBOARD ========== -->
                        <div id="employer-dashboard" class="dashboard-section" style="display: none;">
                            <div class="dashboard-header">
                                <h2>Employer Dashboard</h2>
                                <p class="dashboard-subtitle">Manage your job postings and applicants</p>
                            </div>
                            
                            <div class="stats-grid">
                                <div class="stat-card">
                                    <div class="stat-icon employer"><i class="fas fa-briefcase"></i></div>
                                    <div class="stat-content">
                                        <h3 class="stat-number" id="employer-active-jobs">0</h3>
                                        <p class="stat-label">Active Job Posts</p>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon employer"><i class="fas fa-users"></i></div>
                                    <div class="stat-content">
                                        <h3 class="stat-number" id="employer-total-applicants">0</h3>
                                        <p class="stat-label">Total Applicants</p>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon employer"><i class="fas fa-chart-line"></i></div>
                                    <div class="stat-content">
                                        <h3 class="stat-number" id="employer-avg-match">0%</h3>
                                        <p class="stat-label">Avg Match Score</p>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon employer"><i class="fas fa-eye"></i></div>
                                    <div class="stat-content">
                                        <h3 class="stat-number" id="employer-views">0</h3>
                                        <p class="stat-label">Profile Views</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="section-header with-action">
                                <h3>Recent Job Posts</h3>
                                <button class="btn-primary" id="create-job-btn">
                                    <i class="fas fa-plus"></i> Create Job Post
                                </button>
                            </div>
                            
                            <div class="jobs-list" id="employer-jobs-list"></div>
                            
                            <div class="section-header">
                                <h3>Top Matching Applicants</h3>
                            </div>
                            
                            <div class="applicants-horizontal-scroll" id="top-applicants-scroll"></div>
                        </div>

                        <!-- ========== CREATE JOB POSTING ========== -->
                        <div id="create-job" class="dashboard-section" style="display: none;">
                            <div class="dashboard-header">
                                <h2>Create Job Posting</h2>
                                <p class="dashboard-subtitle">Post a new job opportunity for students</p>
                            </div>
                            
                            <div class="job-form-container">
                                <!-- Job Details -->
                                <div class="form-section">
                                    <h3 class="form-section-title"><i class="fas fa-info-circle"></i> Job Details</h3>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="job-title">Job Title *</label>
                                            <input type="text" id="job-title" placeholder="e.g., Junior Software Developer">
                                        </div>
                                        <div class="form-group">
                                            <label for="job-industry">Industry *</label>
                                            <select id="job-industry">
                                                <option value="">Select industry</option>
                                                <option value="IT">IT & Software</option>
                                                <option value="Finance">Finance & Banking</option>
                                                <option value="Healthcare">Healthcare</option>
                                                <option value="Education">Education</option>
                                                <option value="Engineering">Engineering</option>
                                                <option value="Retail">Retail & Sales</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="job-type">Employment Type *</label>
                                            <select id="job-type" onchange="toggleOjtHours(this.value)">
                                                <option value="">Select type</option>
                                                <option value="full-time">Full-time</option>
                                                <option value="part-time">Part-time</option>
                                                <option value="internship">Internship (OJT)</option>
                                                <option value="contract">Contractual</option>
                                                <option value="freelance">Freelance</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="job-location">Location *</label>
                                            <input type="text" id="job-location" placeholder="e.g., Makati City, Metro Manila">
                                        </div>
                                    </div>

                                    <!-- OJT Required Hours Field -->
                                    <div class="form-row" id="ojt-hours-row" style="display: none;">
                                        <div class="form-group">
                                            <label for="job-ojt-hours">Required OJT Hours *</label>
                                            <input type="number" id="job-ojt-hours" min="1" placeholder="e.g., 300, 486, 600">
                                            <small style="color:#64748b; font-size:12px;">Total number of hours required for completion</small>
                                        </div>
                                    </div>
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="job-salary-min">Salary Range (Min)</label>
                                            <input type="number" id="job-salary-min" placeholder="e.g., 25000">
                                        </div>
                                        <div class="form-group">
                                            <label for="job-salary-max">Salary Range (Max)</label>
                                            <input type="number" id="job-salary-max" placeholder="e.g., 35000">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="job-description">Job Description *</label>
                                        <textarea id="job-description" rows="4" placeholder="Describe the role, responsibilities, and expectations..."></textarea>
                                    </div>
                                </div>
                                
                                <!-- Requirements -->
                                <div class="form-section">
                                    <h3 class="form-section-title"><i class="fas fa-clipboard-list"></i> Requirements</h3>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="job-education">Minimum Education *</label>
                                            <select id="job-education">
                                                <option value="">Select education level</option>
                                                <option value="bachelor">Bachelor's Degree</option>
                                                <option value="master">Master's Degree</option>
                                                <option value="associate">Associate Degree</option>
                                                <option value="highschool">High School Diploma</option>
                                                <option value="vocational">Vocational/Tech</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="job-experience">Years of Experience</label>
                                            <input type="number" id="job-experience" min="0" placeholder="e.g., 2 (optional)">
                                        </div>
                                    </div>
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="job-gwa">Preferred GWA (Optional)</label>
                                            <input type="number" id="job-gwa" step="0.01" min="1.0" max="5.0" placeholder="e.g., 2.0">
                                        </div>
                                        <div class="form-group">
                                            <label for="job-skills">Required Skills *</label>
                                            <input type="text" id="job-skills" placeholder="e.g., Python, JavaScript, React, SQL">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="job-benefits">Benefits (Optional)</label>
                                        <textarea id="job-benefits" rows="2" placeholder="e.g., HMO, 13th month pay, Hybrid setup"></textarea>
                                    </div>
                                </div>
                                
                                <!-- Form Actions -->
                                <div class="form-actions">
                                    <button type="button" class="btn-primary" id="submit-job">Submit for Approval</button>
                                    <button type="button" class="btn-secondary" id="cancel-job">Cancel</button>
                                </div>
                            </div>
                        </div>

                        <!-- ========== RANKED APPLICANTS ========== -->
                        <div id="ranked-applicants" class="dashboard-section" style="display: none;">
                            <div class="dashboard-header">
                                <h2>Ranked Applicants</h2>
                                <p class="dashboard-subtitle">View and compare applicants for your job posts</p>
                            </div>
                            
                            <div class="job-selector">
                                <div class="form-row" style="grid-template-columns: 1fr auto; align-items: end;">
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label for="select-job">Select Job Post:</label>
                                        <select id="select-job" style="max-width: 100%;">
                                            <option value="">-- Choose a job post --</option>
                                        </select>
                                    </div>
                                    <button class="btn-primary btn-sm" id="refresh-applicants" style="height: 42px;">
                                        <i class="fas fa-sync-alt"></i> Refresh
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Skills Comparison -->
                            <div class="skills-comparison" id="skills-comparison" style="display: none;">
                                <div class="section-header with-action" style="margin-bottom: 16px;">
                                    <h3>Skills Comparison</h3>
                                    <span class="badge info">Required vs. Applicant Skills</span>
                                </div>
                                <div style="overflow-x: auto;">
                                    <table class="comparison-table">
                                        <thead>
                                            <tr>
                                                <th>Skill</th>
                                                <th>Status</th>
                                                <th colspan="5">Applicants</th>
                                            </tr>
                                        </thead>
                                        <tbody id="skills-table-body"></tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Applicants Table -->
                            <div class="applicants-table-container">
                                <div class="section-header with-action" style="margin-bottom: 16px; padding: 0 16px;">
                                    <h3>Applicants Ranking</h3>
                                    <div>
                                        <span class="text-muted mr-2">Sort by:</span>
                                        <select id="sort-applicants" style="padding: 6px 10px; border-radius: 8px; border: 1px solid #dee2e6;">
                                            <option value="match">Match Score</option>
                                            <option value="gwa">GWA</option>
                                            <option value="name">Name</option>
                                        </select>
                                    </div>
                                </div>
                                <table class="applicants-table">
                                    <thead>
                                        <tr>
                                            <th>Rank</th>
                                            <th>Applicant</th>
                                            <th>Degree</th>
                                            <th>GWA</th>
                                            <th>Skills Match</th>
                                            <th>Match Score</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="applicants-table-body">
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">Select a job to view applicants</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- ========== ADMIN DASHBOARD ========== -->
                        <div id="admin-dashboard" class="dashboard-section" style="display: none;">
                            <div class="dashboard-header">
                                <h2>Admin Dashboard</h2>
                                <p class="dashboard-subtitle">System administration and monitoring</p>
                            </div>
                            
                            <div class="stats-grid">
                                <div class="stat-card">
                                    <div class="stat-icon admin"><i class="fas fa-user-graduate"></i></div>
                                    <div class="stat-content">
                                        <h3 class="stat-number" id="total-students">0</h3>
                                        <p class="stat-label">Total Students</p>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon admin"><i class="fas fa-building"></i></div>
                                    <div class="stat-content">
                                        <h3 class="stat-number" id="total-employers">0</h3>
                                        <p class="stat-label">Total Employers</p>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon admin"><i class="fas fa-briefcase"></i></div>
                                    <div class="stat-content">
                                        <h3 class="stat-number" id="total-jobs">0</h3>
                                        <p class="stat-label">Job Posts</p>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon admin"><i class="fas fa-file-alt"></i></div>
                                    <div class="stat-content">
                                        <h3 class="stat-number" id="total-applications">0</h3>
                                        <p class="stat-label">Applications</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="actions-grid">
                                <a href="#user-management" class="action-card" onclick="navigateTo('user-management')">
                                    <div class="action-icon"><i class="fas fa-users-cog"></i></div>
                                    <div class="action-content">
                                        <h4>User Management</h4>
                                        <p>Manage all user accounts</p>
                                    </div>
                                    <div class="action-arrow"><i class="fas fa-chevron-right"></i></div>
                                </a>
                                <a href="#job-management" class="action-card" onclick="navigateTo('job-management')">
                                    <div class="action-icon"><i class="fas fa-clipboard-check"></i></div>
                                    <div class="action-content">
                                        <h4>Job Management</h4>
                                        <p>Approve and manage job posts</p>
                                    </div>
                                    <div class="action-arrow"><i class="fas fa-chevron-right"></i></div>
                                </a>
                                <a href="#data-monitoring" class="action-card" onclick="navigateTo('data-monitoring')">
                                    <div class="action-icon"><i class="fas fa-chart-pie"></i></div>
                                    <div class="action-content">
                                        <h4>Data Monitoring</h4>
                                        <p>Track system metrics</p>
                                    </div>
                                    <div class="action-arrow"><i class="fas fa-chevron-right"></i></div>
                                </a>
                                <a href="#reports-announcements" class="action-card" onclick="navigateTo('reports-announcements')">
                                    <div class="action-icon"><i class="fas fa-bullhorn"></i></div>
                                    <div class="action-content">
                                        <h4>Reports & Announcements</h4>
                                        <p>Generate reports and manage announcements</p>
                                    </div>
                                    <div class="action-arrow"><i class="fas fa-chevron-right"></i></div>
                                </a>
                                <a href="#applications-management" class="action-card" onclick="navigateTo('applications-management')">
                                    <div class="action-icon"><i class="fas fa-file-alt"></i></div>
                                    <div class="action-content">
                                        <h4>Applications</h4>
                                        <p>View and manage student applications</p>
                                        <span class="badge" id="pending-applications-badge" style="background:#f59e0b;color:white;padding:2px 8px;border-radius:10px;font-size:11px;">0 pending</span>
                                    </div>
                                    <div class="action-arrow"><i class="fas fa-chevron-right"></i></div>
                                </a>
                            </div>
                            
                            <div class="section-header">
                                <h3>Recent Activity</h3>
                            </div>
                            
                            <div class="activity-feed" id="activity-feed"></div>
                        </div>

                        <!-- ========== USER MANAGEMENT ========== -->
                        <div id="user-management" class="dashboard-section" style="display: none;">
                            <div class="dashboard-header with-action">
                                <div>
                                    <h2>User Management</h2>
                                    <p class="dashboard-subtitle">Manage all user accounts in the system</p>
                                </div>
                                <div class="action-buttons">
                                    <button class="btn-primary" id="add-employer-btn">
                                        <i class="fas fa-plus"></i> Add Employer
                                    </button>
                                    <button class="btn-secondary btn-sm" id="export-users">
                                        <i class="fas fa-download"></i> Export
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Filters -->
                            <div class="filters-container" style="grid-template-columns: repeat(3, 1fr) auto; margin-bottom: 20px;">
                                <div class="filter-group">
                                    <label for="user-role-filter">Role</label>
                                    <select id="user-role-filter">
                                        <option value="">All Roles</option>
                                        <option value="student">Students</option>
                                        <option value="employer">Employers</option>
                                        <option value="admin">Admins</option>
                                    </select>
                                </div>
                                <div class="filter-group">
                                    <label for="user-status-filter">Status</label>
                                    <select id="user-status-filter">
                                        <option value="">All Status</option>
                                        <option value="active">Active</option>
                                        <option value="pending">Pending</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                                <div class="filter-group">
                                    <label for="user-search">Search</label>
                                    <input type="text" id="user-search" placeholder="Name or email...">
                                </div>
                                <button class="btn-primary btn-sm" id="apply-user-filters" style="align-self: flex-end;">Apply</button>
                            </div>
                            
                            <!-- Users Table -->
                            <div class="users-table-container">
                                <table class="users-table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th>Profile</th>
                                            <th>Last Active</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="users-table-body"></tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="pagination" style="display: flex; justify-content: flex-end; margin-top: 20px; gap: 8px;">
                                <button class="btn-secondary btn-sm" disabled>&laquo; Prev</button>
                                <span style="padding: 8px 12px;" id="pagination-info">Page 1 of 1</span>
                                <button class="btn-secondary btn-sm">Next &raquo;</button>
                            </div>
                        </div>

                        <!-- ========== JOB MANAGEMENT ========== -->
                        <div id="job-management" class="dashboard-section" style="display: none;">
                            <div class="dashboard-header with-action">
                                <div>
                                    <h2>Job Post Management</h2>
                                    <p class="dashboard-subtitle">Approve, review, and manage job posts</p>
                                </div>
                                <div>
                                    <span class="status-badge pending" id="pending-jobs-count">0 pending</span>
                                </div>
                            </div>
                            
                            <!-- Filter Bar -->
                            <div class="job-filter-bar" style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 16px;">
                                <div class="filter-group search-input">
                                    <i class="fas fa-search"></i>
                                    <input type="text" id="job-search-admin" placeholder="Search by job title or company...">
                                </div>
                                <div class="filter-group">
                                    <select id="job-status-filter">
                                        <option value="">All Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                        <option value="closed">Closed</option>
                                    </select>
                                </div>
                                <div class="filter-group">
                                    <select id="job-industry-filter">
                                        <option value="">All Industries</option>
                                        <option value="IT">IT & Software</option>
                                        <option value="Finance">Finance</option>
                                        <option value="Healthcare">Healthcare</option>
                                        <option value="Education">Education</option>
                                        <option value="Engineering">Engineering</option>
                                    </select>
                                </div>
                                <button class="btn-primary btn-sm" id="refresh-job-list">
                                    <i class="fas fa-sync-alt"></i> Refresh
                                </button>
                            </div>
                            
                            <!-- Stats Summary -->
                            <div class="stats-summary">
                                <div class="stat-summary-card">
                                    <div class="stat-summary-label">Total Jobs</div>
                                    <div class="stat-summary-value" id="total-jobs-count">0</div>
                                </div>
                                <div class="stat-summary-card">
                                    <div class="stat-summary-label">Pending</div>
                                    <div class="stat-summary-value" style="color: #f59e0b;" id="pending-jobs-count2">0</div>
                                </div>
                                <div class="stat-summary-card">
                                    <div class="stat-summary-label">Approved</div>
                                    <div class="stat-summary-value" style="color: #10b981;" id="approved-jobs-count">0</div>
                                </div>
                                <div class="stat-summary-card">
                                    <div class="stat-summary-label">Rejected</div>
                                    <div class="stat-summary-value" style="color: #ef4444;" id="rejected-jobs-count">0</div>
                                </div>
                            </div>
                            
                            <!-- Job Cards Container -->
                            <div class="job-cards-container" id="admin-job-cards"></div>
                        </div>

                        <!-- ========== DATA MONITORING ========== -->
                        <div id="data-monitoring" class="dashboard-section" style="display: none;">
                            <div class="dashboard-header">
                                <h2>Data Monitoring</h2>
                                <p class="dashboard-subtitle">Track system data completeness and metrics</p>
                            </div>
                            
                            <!-- Key Metrics Cards -->
                            <div class="stats-summary">
                                <div class="stat-summary-card">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(46,125,50,0.1); display: flex; align-items: center; justify-content: center; color: #2E7D32; font-size: 20px;">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <div>
                                            <div class="stat-summary-label">Total Students</div>
                                            <div class="stat-summary-value" id="total-students-monitor">0</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="stat-summary-card">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(255,107,53,0.1); display: flex; align-items: center; justify-content: center; color: #FF6B35; font-size: 20px;">
                                            <i class="fas fa-building"></i>
                                        </div>
                                        <div>
                                            <div class="stat-summary-label">Employers</div>
                                            <div class="stat-summary-value" id="total-employers-monitor">0</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="stat-summary-card">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(0,51,102,0.1); display: flex; align-items: center; justify-content: center; color: #003366; font-size: 20px;">
                                            <i class="fas fa-briefcase"></i>
                                        </div>
                                        <div>
                                            <div class="stat-summary-label">Active Jobs</div>
                                            <div class="stat-summary-value" id="active-jobs-monitor">0</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="stat-summary-card">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(23,162,184,0.1); display: flex; align-items: center; justify-content: center; color: #17a2b8; font-size: 20px;">
                                            <i class="fas fa-file-alt"></i>
                                        </div>
                                        <div>
                                            <div class="stat-summary-label">Applications</div>
                                            <div class="stat-summary-value" id="total-applications-monitor">0</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Main Monitoring Layout - 2 columns -->
                            <div class="monitoring-split-layout">
                                <!-- Left Column - Profile Completeness -->
                                <div class="monitoring-card">
                                    <div class="card-header">
                                        <h3><i class="fas fa-chart-pie"></i> Profile Completeness</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="big-stat">
                                            <span id="incomplete-profiles-count">0</span>
                                            <span class="stat-label">students with incomplete profiles</span>
                                        </div>
                                        
                                        <div style="margin: 20px 0;">
                                            <div class="average-completion">
                                                <span>Average Completion Rate</span>
                                                <span class="percentage" id="avg-completion-percentage">0%</span>
                                            </div>
                                            <div class="progress-bar large">
                                                <div class="progress-fill" id="avg-completion-progress" style="width: 0%;"></div>
                                            </div>
                                        </div>
                                        
                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 20px;">
                                            <div style="background: #f8f9fa; padding: 16px; border-radius: 12px; text-align: center;">
                                                <div style="font-size: 28px; font-weight: 700; color: #28a745;" id="complete-profiles-count">0</div>
                                                <div style="font-size: 12px; color: #64748b;">Complete Profiles</div>
                                            </div>
                                            <div style="background: #f8f9fa; padding: 16px; border-radius: 12px; text-align: center;">
                                                <div style="font-size: 28px; font-weight: 700; color: #dc3545;" id="incomplete-profiles-count2">0</div>
                                                <div style="font-size: 12px; color: #64748b;">Incomplete</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Right Column - Data Gaps -->
                                <div class="monitoring-card">
                                    <div class="card-header">
                                        <h3><i class="fas fa-exclamation-triangle"></i> Data Gaps Analysis</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="gap-item">
                                            <div class="gap-stat">
                                                <span id="missing-skills-count">0</span>
                                                <span class="gap-label">students missing skills</span>
                                            </div>
                                            <div class="progress-bar" style="margin-top: 8px;">
                                                <div class="progress-fill" id="missing-skills-progress" style="width: 0%; background: #ffc107;"></div>
                                            </div>
                                        </div>
                                        <div class="gap-item">
                                            <div class="gap-stat">
                                                <span id="missing-gwa-count">0</span>
                                                <span class="gap-label">students missing GWA</span>
                                            </div>
                                            <div class="progress-bar" style="margin-top: 8px;">
                                                <div class="progress-fill" id="missing-gwa-progress" style="width: 0%; background: #ffc107;"></div>
                                            </div>
                                        </div>
                                        <div class="gap-item">
                                            <div class="gap-stat">
                                                <span id="missing-degree-count">0</span>
                                                <span class="gap-label">students missing degree</span>
                                            </div>
                                            <div class="progress-bar" style="margin-top: 8px;">
                                                <div class="progress-fill" id="missing-degree-progress" style="width: 0%; background: #ffc107;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Student Profiles Table -->
                            <div class="student-data-table-container">
                                <div class="section-header with-action" style="margin-bottom: 16px;">
                                    <h3>Student Profiles Overview</h3>
                                    <div>
                                        <input type="text" id="student-search" placeholder="Search student..." style="padding: 8px 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 13px;">
                                    </div>
                                </div>
                                <table class="student-data-table">
                                    <thead>
                                        <tr>
                                            <th>Student Name</th>
                                            <th>Completion</th>
                                            <th>Skills</th>
                                            <th>GWA</th>
                                            <th>Degree</th>
                                            <th>Last Updated</th>
                                        </tr>
                                    </thead>
                                    <tbody id="student-data-table-body"></tbody>
                                </table>
                            </div>
                        </div>

                        <!-- ========== APPLICATIONS MANAGEMENT (ADMIN) ========== -->
                        <div id="applications-management" class="dashboard-section" style="display: none;">
                            <div class="dashboard-header">
                                <h2>Student Applications</h2>
                                <p class="dashboard-subtitle">View and manage all student job applications</p>
                            </div>
                            <div class="stats-summary" style="margin-bottom:20px;">
                                <div class="stat-summary-card">
                                    <div class="stat-summary-label">Total Applications</div>
                                    <div class="stat-summary-value" id="total-apps-count">0</div>
                                </div>
                                <div class="stat-summary-card">
                                    <div class="stat-summary-label">Pending</div>
                                    <div class="stat-summary-value" style="color:#f59e0b;" id="pending-apps-count">0</div>
                                </div>
                                <div class="stat-summary-card">
                                    <div class="stat-summary-label">Accepted</div>
                                    <div class="stat-summary-value" style="color:#10b981;" id="accepted-apps-count">0</div>
                                </div>
                                <div class="stat-summary-card">
                                    <div class="stat-summary-label">Rejected</div>
                                    <div class="stat-summary-value" style="color:#ef4444;" id="rejected-apps-count">0</div>
                                </div>
                            </div>
                            <div style="background:white; border-radius:12px; padding:20px; box-shadow:0 2px 8px rgba(0,0,0,0.06); overflow-x:auto;">
                                <table style="width:100%; border-collapse:collapse; font-size:13px;">
                                    <thead>
                                        <tr style="background:#f0fdf4; color:#1a4731;">
                                            <th style="padding:10px; text-align:left; border-bottom:2px solid #d1fae5;">Student</th>
                                            <th style="padding:10px; text-align:left; border-bottom:2px solid #d1fae5;">Email</th>
                                            <th style="padding:10px; text-align:left; border-bottom:2px solid #d1fae5;">Job Title</th>
                                            <th style="padding:10px; text-align:left; border-bottom:2px solid #d1fae5;">Company</th>
                                            <th style="padding:10px; text-align:left; border-bottom:2px solid #d1fae5;">Date Applied</th>
                                            <th style="padding:10px; text-align:left; border-bottom:2px solid #d1fae5;">Status</th>
                                            <th style="padding:10px; text-align:left; border-bottom:2px solid #d1fae5;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="applications-table-body">
                                        <tr><td colspan="7" style="text-align:center; padding:20px; color:#94a3b8;">Loading applications...</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- ========== REPORTS & ANNOUNCEMENTS ========== -->
                        <div id="reports-announcements" class="dashboard-section" style="display: none;">
                            <div class="dashboard-header">
                                <h2>Reports & Announcements</h2>
                                <p class="dashboard-subtitle">Generate system reports and manage campus announcements</p>
                            </div>
                            
                            <!-- Quick Stats Row -->
                            <div class="stats-grid" style="margin-bottom: 28px; grid-template-columns: repeat(3, 1fr);">
                                <div class="stat-card">
                                    <div class="stat-icon" style="background: linear-gradient(135deg, #2E7D32, #4CAF50);">
                                        <i class="fas fa-bullhorn"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h3 class="stat-number" id="total-announcements">0</h3>
                                        <p class="stat-label">Total Announcements</p>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon" style="background: linear-gradient(135deg, #FF6B35, #ff8c42);">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h3 class="stat-number" id="upcoming-events">0</h3>
                                        <p class="stat-label">Upcoming Events</p>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon" style="background: linear-gradient(135deg, #003366, #0056b3);">
                                        <i class="fas fa-file-pdf"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h3 class="stat-number" id="reports-generated">0</h3>
                                        <p class="stat-label">Reports Generated</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tab Navigation -->
                            <div style="display: flex; gap: 10px; margin-bottom: 24px; border-bottom: 2px solid #e9ecef; padding-bottom: 2px; flex-wrap: wrap;">
                                <button class="tab-btn active" data-tab="reports-tab">
                                    <i class="fas fa-chart-bar"></i> Generate Reports
                                </button>
                                <button class="tab-btn" data-tab="announcements-tab">
                                    <i class="fas fa-bullhorn"></i> Manage Announcements
                                </button>
                                <button class="tab-btn" data-tab="templates-tab">
                                    <i class="fas fa-file-alt"></i> Templates
                                </button>
                                <button class="tab-btn" data-tab="activities-tab">
                                    <i class="fas fa-calendar-check"></i> Activities
                                </button>
                            </div>
                            
                            <!-- Reports Tab Content -->
                            <div id="reports-tab" class="tab-content active">
                                <!-- Report Categories -->
                                <div style="margin-bottom: 28px;">
                                    <h3 style="font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                                        <i class="fas fa-chart-pie" style="color: #2E7D32;"></i>
                                        Quick Report Generation
                                    </h3>
                                    
                                    <div class="reports-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                                        <!-- Placement Report Card -->
                                        <div class="report-card" style="background: white; border-radius: 20px; padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); display: flex; gap: 20px; align-items: flex-start; border: 1px solid rgba(0,0,0,0.03);">
                                            <div style="width: 60px; height: 60px; border-radius: 16px; background: linear-gradient(135deg, #2E7D32, #4CAF50); display: flex; align-items: center; justify-content: center; color: white; font-size: 28px; flex-shrink: 0;">
                                                <i class="fas fa-briefcase"></i>
                                            </div>
                                            <div style="flex: 1;">
                                                <h4 style="font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 4px;">Placement Report</h4>
                                                <p style="font-size: 14px; color: #64748b; margin-bottom: 16px;">Monthly placement statistics, graduate outcomes, and hiring trends</p>
                                                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                                    <button class="btn-primary btn-sm generate-report" data-report="placement-pdf">
                                                        <i class="fas fa-file-pdf"></i> PDF
                                                    </button>
                                                    <button class="btn-secondary btn-sm generate-report" data-report="placement-excel">
                                                        <i class="fas fa-file-excel"></i> Excel
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Analytics Report Card -->
                                        <div class="report-card" style="background: white; border-radius: 20px; padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); display: flex; gap: 20px; align-items: flex-start; border: 1px solid rgba(0,0,0,0.03);">
                                            <div style="width: 60px; height: 60px; border-radius: 16px; background: linear-gradient(135deg, #FF6B35, #ff8c42); display: flex; align-items: center; justify-content: center; color: white; font-size: 28px; flex-shrink: 0;">
                                                <i class="fas fa-chart-line"></i>
                                            </div>
                                            <div style="flex: 1;">
                                                <h4 style="font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 4px;">Analytics Report</h4>
                                                <p style="font-size: 14px; color: #64748b; margin-bottom: 16px;">System usage metrics, user engagement, and platform analytics</p>
                                                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                                    <button class="btn-primary btn-sm generate-report" data-report="analytics-pdf">
                                                        <i class="fas fa-file-pdf"></i> PDF
                                                    </button>
                                                    <button class="btn-secondary btn-sm generate-report" data-report="analytics-excel">
                                                        <i class="fas fa-file-excel"></i> Excel
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Announcements Tab Content -->
                            <div id="announcements-tab" class="tab-content" style="display: none;">
                                <!-- Announcement Management Header -->
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
                                    <div>
                                        <h3 style="font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 4px;">Announcement Management</h3>
                                        <p style="font-size: 14px; color: #64748b;">Create and manage system-wide announcements</p>
                                    </div>
                                    <button class="btn-primary" id="create-announcement-btn">
                                        <i class="fas fa-plus-circle"></i> Create New Announcement
                                    </button>
                                </div>
                                
                                <!-- Announcement Filters -->
                                <div style="display: flex; gap: 16px; margin-bottom: 24px; flex-wrap: wrap; background: white; padding: 20px; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                                    <div style="flex: 2; min-width: 250px;">
                                        <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 6px; text-transform: uppercase;">Search</label>
                                        <div style="position: relative;">
                                            <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                                            <input type="text" id="announcement-search" placeholder="Search by title or content..." style="width: 100%; padding: 10px 12px 10px 36px; border: 1px solid #dee2e6; border-radius: 10px; font-size: 14px;">
                                        </div>
                                    </div>
                                    <div style="flex: 1;">
                                        <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 6px; text-transform: uppercase;">Type</label>
                                        <select id="announcement-type-filter" style="width: 100%; padding: 10px 12px; border: 1px solid #dee2e6; border-radius: 10px; font-size: 14px;">
                                            <option value="">All Types</option>
                                            <option value="event">Event</option>
                                            <option value="news">News</option>
                                            <option value="deadline">Deadline</option>
                                            <option value="general">General</option>
                                        </select>
                                    </div>
                                    <div style="flex: 1;">
                                        <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 6px; text-transform: uppercase;">Status</label>
                                        <select id="announcement-status-filter" style="width: 100%; padding: 10px 12px; border: 1px solid #dee2e6; border-radius: 10px; font-size: 14px;">
                                            <option value="">All Status</option>
                                            <option value="published">Published</option>
                                            <option value="draft">Draft</option>
                                        </select>
                                    </div>
                                    <div style="display: flex; align-items: flex-end;">
                                        <button class="btn-primary btn-sm" id="apply-announcement-filters">
                                            <i class="fas fa-filter"></i> Apply
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Announcements Stats -->
                                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px;">
                                    <div style="background: white; padding: 16px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); display: flex; align-items: center; gap: 12px;">
                                        <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(46,125,50,0.1); display: flex; align-items: center; justify-content: center; color: #2E7D32; font-size: 20px;">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                        <div>
                                            <div style="font-size: 20px; font-weight: 600; color: #1e293b;" id="published-count">0</div>
                                            <div style="font-size: 12px; color: #64748b;">Published</div>
                                        </div>
                                    </div>
                                    <div style="background: white; padding: 16px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); display: flex; align-items: center; gap: 12px;">
                                        <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(255,193,7,0.1); display: flex; align-items: center; justify-content: center; color: #f59e0b; font-size: 20px;">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div>
                                            <div style="font-size: 20px; font-weight: 600; color: #1e293b;" id="draft-count">0</div>
                                            <div style="font-size: 12px; color: #64748b;">Drafts</div>
                                        </div>
                                    </div>
                                    <div style="background: white; padding: 16px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); display: flex; align-items: center; gap: 12px;">
                                        <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(23,162,184,0.1); display: flex; align-items: center; justify-content: center; color: #17a2b8; font-size: 20px;">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                        <div>
                                            <div style="font-size: 20px; font-weight: 600; color: #1e293b;" id="upcoming-count">0</div>
                                            <div style="font-size: 12px; color: #64748b;">Upcoming</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Announcements List -->
                                <div class="announcements-list" id="admin-announcements-list"></div>
                            </div>
                            
                            <!-- Templates Tab Content -->
                            <div id="templates-tab" class="tab-content" style="display: none;">
                                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                                    <!-- Template Card 1 -->
                                    <div style="background: white; border-radius: 20px; padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.03);">
                                        <div style="width: 50px; height: 50px; border-radius: 12px; background: rgba(46,125,50,0.1); display: flex; align-items: center; justify-content: center; color: #2E7D32; font-size: 24px; margin-bottom: 16px;">
                                            <i class="fas fa-graduation-cap"></i>
                                        </div>
                                        <h4 style="font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 8px;">Placement Report Template</h4>
                                        <p style="font-size: 14px; color: #64748b; margin-bottom: 20px;">Standard template for monthly placement statistics</p>
                                        <div style="display: flex; gap: 10px;">
                                            <button class="btn-primary btn-sm" style="flex: 1;">Use Template</button>
                                            <button class="btn-secondary btn-sm" style="padding: 8px 12px;"><i class="fas fa-download"></i></button>
                                        </div>
                                    </div>
                                    
                                    <!-- Template Card 2 -->
                                    <div style="background: white; border-radius: 20px; padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.03);">
                                        <div style="width: 50px; height: 50px; border-radius: 12px; background: rgba(255,107,53,0.1); display: flex; align-items: center; justify-content: center; color: #FF6B35; font-size: 24px; margin-bottom: 16px;">
                                            <i class="fas fa-chart-line"></i>
                                        </div>
                                        <h4 style="font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 8px;">Analytics Dashboard</h4>
                                        <p style="font-size: 14px; color: #64748b; margin-bottom: 20px;">Key system metrics and trends</p>
                                        <div style="display: flex; gap: 10px;">
                                            <button class="btn-primary btn-sm" style="flex: 1;">Use Template</button>
                                            <button class="btn-secondary btn-sm" style="padding: 8px 12px;"><i class="fas fa-download"></i></button>
                                        </div>
                                    </div>
                                    
                                    <!-- Template Card 3 -->
                                    <div style="background: white; border-radius: 20px; padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.03);">
                                        <div style="width: 50px; height: 50px; border-radius: 12px; background: rgba(0,51,102,0.1); display: flex; align-items: center; justify-content: center; color: #003366; font-size: 24px; margin-bottom: 16px;">
                                            <i class="fas fa-bullhorn"></i>
                                        </div>
                                        <h4 style="font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 8px;">Announcement Template</h4>
                                        <p style="font-size: 14px; color: #64748b; margin-bottom: 20px;">Pre-formatted templates for announcements</p>
                                        <div style="display: flex; gap: 10px;">
                                            <button class="btn-primary btn-sm" style="flex: 1;">Use Template</button>
                                            <button class="btn-secondary btn-sm" style="padding: 8px 12px;"><i class="fas fa-download"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ========== ACTIVITIES TAB ========== -->
                            <div id="activities-tab" class="tab-content" style="display: none;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                                    <h3>Activities & Events</h3>
                                </div>

                                <!-- Stats -->
                                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px;">
                                    <div class="stat-summary-card">
                                        <div class="stat-summary-label">Total Events</div>
                                        <div class="stat-summary-value" id="act-total-events">0</div>
                                    </div>
                                    <div class="stat-summary-card">
                                        <div class="stat-summary-label">Total Registrants</div>
                                        <div class="stat-summary-value" id="act-total-registrants">0</div>
                                    </div>
                                    <div class="stat-summary-card">
                                        <div class="stat-summary-label">Total Attendance</div>
                                        <div class="stat-summary-value" id="act-total-attendance">0</div>
                                    </div>
                                </div>

<!-- List of Events -->
                                <div id="act-events-list-box" style="background:white; border-radius:12px; padding:20px; box-shadow:0 2px 8px rgba(0,0,0,0.06); margin-bottom: 24px;">
                                    <h4 style="margin-bottom:16px; color:#1a4731;"><i class="fas fa-list"></i> List of Events / Seminars</h4>
                                    <div style="overflow-x:auto;">
                                        <table style="width:100%; border-collapse:collapse; font-size:13px;" id="activities-events-table">
                                            <thead>
                                                <tr style="background:#f0fdf4; color:#1a4731;">
                                                    <th style="padding:10px; text-align:left; border-bottom:2px solid #d1fae5;">Event Title</th>
                                                    <th style="padding:10px; text-align:left; border-bottom:2px solid #d1fae5;">Date</th>
                                                    <th style="padding:10px; text-align:left; border-bottom:2px solid #d1fae5;">Location</th>
                                                    <th style="padding:10px; text-align:left; border-bottom:2px solid #d1fae5;">Registrants</th>
                                                    <th style="padding:10px; text-align:left; border-bottom:2px solid #d1fae5;">Attended</th>
                                                    <th style="padding:10px; text-align:left; border-bottom:2px solid #d1fae5;">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="activities-events-body">
                                                <tr><td colspan="6" style="text-align:center; padding:20px; color:#94a3b8;">Loading events...</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Registrants per Program/Section -->
                                <div id="event-detail-view" style="display:none;">
                                    <!-- Back Button -->
                                    <div style="margin-bottom:16px;">
                                        <button class="btn-secondary btn-sm" onclick="backToEventsList()">
                                            <i class="fas fa-arrow-left"></i> Back to Activities & Events
                                        </button>
                                    </div>
                                    <h3 id="event-detail-title" style="margin-bottom:20px; color:#1a4731; font-size:20px;"></h3>
                                </div>

                                <div id="registrants-section" style="background:white; border-radius:12px; padding:20px; box-shadow:0 2px 8px rgba(0,0,0,0.06); margin-bottom: 24px;">
                                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; flex-wrap:wrap; gap:8px;">
                                        <h4 style="color:#1a4731; margin:0;"><i class="fas fa-users"></i> Registrants per Program / Section <span id="registrants-event-name" style="font-size:13px; color:#64748b; font-weight:400;"></span></h4>
                                    </div>
                                    <div style="margin-bottom:12px; display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                                        <div>
                                            <label style="display:block; font-size:11px; font-weight:600; color:#475569; text-transform:uppercase; margin-bottom:4px;">Program</label>
                                            <select id="activities-program-filter" style="padding:8px 12px; border:1px solid #ddd; border-radius:8px; font-size:13px; min-width:160px;">
                                                <option value="">-- All Programs --</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label style="display:block; font-size:11px; font-weight:600; color:#475569; text-transform:uppercase; margin-bottom:4px;">Section</label>
                                            <input type="text" id="activities-section-filter" placeholder="e.g. 4A" style="padding:8px 12px; border:1px solid #ddd; border-radius:8px; font-size:13px; width:120px;">
                                        </div>
                                        <div style="display:flex; align-items:flex-end; gap:8px; padding-bottom:0;">
                                            <button class="btn-primary btn-sm" onclick="applyRegistrantFilters()" style="margin-top:20px;">
                                                <i class="fas fa-filter"></i> Filter
                                            </button>
                                            <button class="btn-secondary btn-sm" onclick="clearRegistrantFilters()" style="margin-top:20px;">
                                                Clear
                                            </button>
                                            <span id="registrant-section-count" style="margin-top:20px; padding:6px 14px; background:#f0fdf4; color:#2E7D32; border-radius:8px; font-size:13px; font-weight:600; display:none;">
                                                <i class="fas fa-users"></i> <span id="registrant-count-value">0</span> student(s)
                                            </span>
                                        </div>
                                    </div>
                                    <div style="overflow-x:auto;">
                                        <table style="width:100%; border-collapse:collapse; font-size:13px;">
                                            <thead>
                                                <tr style="background:#f0fdf4; color:#1a4731;">
                                                    <th style="padding:10px; text-align:left; border-bottom:2px solid #d1fae5;">Student Name</th>
                                                    <th style="padding:10px; text-align:left; border-bottom:2px solid #d1fae5;">Student No.</th>
                                                    <th style="padding:10px; text-align:left; border-bottom:2px solid #d1fae5;">Program</th>
                                                    <th style="padding:10px; text-align:left; border-bottom:2px solid #d1fae5;">Section</th>
                                                    <th style="padding:10px; text-align:left; border-bottom:2px solid #d1fae5;">Registered</th>
                                                    <th style="padding:10px; text-align:left; border-bottom:2px solid #d1fae5;">Attendance</th>
                                                </tr>
                                            </thead>
                                            <tbody id="activities-registrants-body">
                                                <tr><td colspan="6" style="text-align:center; padding:20px; color:#94a3b8;">Select an event to view registrants.</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Attendance -->
                                <div id="attendance-section" style="background:white; border-radius:12px; padding:20px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                                    <h4 style="margin-bottom:16px; color:#1a4731;"><i class="fas fa-clipboard-check"></i> Attendance Summary</h4>
                                    <div id="activities-attendance-summary" style="color:#94a3b8; font-size:13px;">Select an event above to view attendance.</div>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <!-- ========== MODALS ========== -->
    
    <!-- QR Code Modal -->
    <div class="modal" id="qr-modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Event QR Code</h3>
                <button class="close-modal" id="close-qr-modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="qr-code-container" id="qr-code"></div>
                <p class="qr-instructions" id="qr-event-name"></p>
                <div class="qr-actions">
                    <button class="btn-primary btn-sm" id="download-qr">
                        <i class="fas fa-download"></i> Download
                    </button>
                    <button class="btn-secondary btn-sm" id="print-qr">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- User Modal -->
    <div class="modal" id="user-modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-title">Manage User</h3>
                <button class="close-modal" id="close-user-modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="user-form">
                    <input type="hidden" id="modal-user-id">
                    <input type="hidden" id="modal-user-role">
                    
                    <div id="student-fields" style="display: none;">
                        <div class="form-group">
                            <label for="modal-student-id">Student ID</label>
                            <input type="text" id="modal-student-id">
                        </div>
                        <div class="form-group">
                            <label for="modal-student-name">Full Name</label>
                            <input type="text" id="modal-student-name">
                        </div>
                        <div class="form-group">
                            <label for="modal-student-email">Email</label>
                            <input type="email" id="modal-student-email">
                        </div>
                        <div class="form-group">
                            <label for="modal-student-degree">Degree Program</label>
                            <select id="modal-student-degree">
                                <option value="">Select degree</option>
                                <option value="Bachelor of Secondary Education">Bachelor of Secondary Education</option>
                                <option value="BS Business Management">BS Business Management</option>
                                <option value="BS Computer Engineering">BS Computer Engineering</option>
                                <option value="BS Computer Science">BS Computer Science</option>
                                <option value="BS Hospitality Management">BS Hospitality Management</option>
                                <option value="BS Industrial Technology">BS Industrial Technology</option>
                                <option value="BS Information Technology">BS Information Technology</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="modal-student-gwa">GWA</label>
                            <input type="number" id="modal-student-gwa" step="0.01" min="1.0" max="5.0">
                        </div>
                        <div class="form-group">
                            <label for="modal-student-skills">Skills</label>
                            <textarea id="modal-student-skills" rows="3"></textarea>
                        </div>
                    </div>
                    
                    <div id="employer-fields" style="display: none;">
                        <div class="form-group">
                            <label for="modal-employer-name">Company Name</label>
                            <input type="text" id="modal-employer-name">
                        </div>
                        <div class="form-group">
                            <label for="modal-employer-email">Email</label>
                            <input type="email" id="modal-employer-email">
                        </div>
                        <div class="form-group">
                            <label for="modal-employer-password">Password</label>
                            <input type="text" id="modal-employer-password">
                        </div>
                        <div class="form-group">
                            <label for="modal-employer-contact">Contact Person</label>
                            <input type="text" id="modal-employer-contact">
                        </div>
                        <div class="form-group">
                            <label for="modal-employer-industry">Industry</label>
                            <input type="text" id="modal-employer-industry">
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn-primary" id="save-user">Save</button>
                        <button type="button" class="btn-danger" id="delete-user" style="display: none;">Delete</button>
                        <button type="button" class="btn-secondary" id="cancel-user">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Announcement Modal -->
    <div class="modal" id="announcement-modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="announcement-modal-title">Create Announcement</h3>
                <button class="close-modal" id="close-announcement-modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="announcement-form">
                    <input type="hidden" id="announcement-id">
                    <div class="form-group">
                        <label for="announcement-title">Title</label>
                        <input type="text" id="announcement-title" required>
                    </div>
                    <div class="form-group">
                        <label for="announcement-content">Content</label>
                        <textarea id="announcement-content" rows="4" required></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="announcement-type">Type</label>
                            <select id="announcement-type">
                                <option value="event">Event</option>
                                <option value="news">News</option>
                                <option value="deadline">Deadline</option>
                                <option value="general">General</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="announcement-date">Start Date</label>
                            <input type="date" id="announcement-date">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="announcement-location">Location (Optional)</label>
                        <input type="text" id="announcement-location">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="announcement-end-date">End Date (Optional)</label>
                            <input type="date" id="announcement-end-date">
                        </div>
                        <div class="form-group">
                            <label for="announcement-registration-status">Registration Status</label>
                            <select id="announcement-registration-status">
                                <option value="open">Open</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" id="announcement-form-link" value="">
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="announcement-publish" checked>
                            Publish immediately
                        </label>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Save</button>
                        <button type="button" class="btn-secondary" id="cancel-announcement">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div class="modal" id="forgot-password-modal" style="display: none;">
        <div class="modal-content forgot-modal">
            <div class="modal-header">
                <h3>Reset Password</h3>
                <button class="close-modal" id="close-forgot-modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p class="forgot-instructions">Enter your email address and we'll send you instructions to reset your password.</p>
                <form id="forgot-password-form">
                    <div class="form-group">
                        <label for="forgot-email" class="form-label">EMAIL ADDRESS</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="forgot-email" placeholder="Enter your email" required>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Send Reset Link</button>
                        <button type="button" class="btn-secondary" id="cancel-forgot">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Resume Modal -->
    <div class="resume-modal" id="resume-modal">
        <div class="resume-wrapper">
            <div class="resume-actions">
                <span><i class="fas fa-file-alt"></i> Resume Preview</span>
                <div class="resume-actions-btns">
                    <button onclick="window.print()" style="background:#2E7D32;color:white;border:none;padding:8px 18px;border-radius:8px;cursor:pointer;font-weight:600;font-size:13px;"><i class="fas fa-print"></i> Print</button>
                    <button onclick="closeResumeModal()" style="background:#e2e8f0;color:#1e293b;border:none;padding:8px 18px;border-radius:8px;cursor:pointer;font-weight:600;font-size:13px;"><i class="fas fa-times"></i> Close</button>
                </div>
            </div>
            <div id="resume-content">
                <div class="resume-header" style="display:flex; align-items:center; gap:24px; text-align:left;">
                    <div id="r-photo-container" style="width:100px; height:100px; border-radius:8px; border:2px solid #2E7D32; overflow:hidden; flex-shrink:0; display:flex; align-items:center; justify-content:center; background:#f0fdf4;">
                        <i class="fas fa-user" style="font-size:36px; color:#94a3b8;"></i>
                    </div>
                    <div style="flex:1; border-bottom:3px solid #2E7D32; padding-bottom:16px;">
                        <h1 id="r-fullname" style="margin-bottom:6px;">-</h1>
                        <p id="r-email">-</p>
                        <p id="r-contact">-</p>
                        <p id="r-studentno">-</p>
                    </div>
                </div>
                <div class="resume-section">
                    <div class="resume-section-title">Personal Information</div>
                    <div class="resume-row">
                        <div class="resume-field"><label>Full Name</label><span id="r-name2">-</span></div>
                        <div class="resume-field"><label>Email Address</label><span id="r-email2">-</span></div>
                        <div class="resume-field"><label>Contact Number</label><span id="r-contact2">-</span></div>
                        <div class="resume-field"><label>Student Number</label><span id="r-studentno2">-</span></div>
                    </div>
                </div>
                <div class="resume-section">
                    <div class="resume-section-title">Education</div>
                    <div class="resume-row">
                        <div class="resume-field"><label>Degree Program</label><span id="r-degree">-</span></div>
                        <div class="resume-field"><label>Year Level</label><span id="r-year">-</span></div>
                        <div class="resume-field"><label>GWA</label><span id="r-gwa">-</span></div>
                        <div class="resume-field"><label>Section</label><span id="r-section">-</span></div>
                    </div>
                </div>
                <div class="resume-section">
                    <div class="resume-section-title">Educational Background</div>
                    <div class="resume-school-item"><div><div class="resume-school-name" id="r-shs-school">-</div><div class="resume-school-level">Senior High School</div></div><div class="resume-school-year" id="r-shs-year">-</div></div>
                    <div class="resume-school-item"><div><div class="resume-school-name" id="r-hs-school">-</div><div class="resume-school-level">High School</div></div><div class="resume-school-year" id="r-hs-year">-</div></div>
                    <div class="resume-school-item"><div><div class="resume-school-name" id="r-elem-school">-</div><div class="resume-school-level">Elementary</div></div><div class="resume-school-year" id="r-elem-year">-</div></div>
                </div>
                <div class="resume-section">
                    <div class="resume-section-title">Skills & Expertise</div>
                    <div class="resume-skills" id="r-skills"><span style="color:#94a3b8;font-size:13px;">No skills added yet.</span></div>
                </div>
                <div class="resume-section">
                    <div class="resume-section-title">Character References</div>
                    <p style="font-size:12px;color:#94a3b8;margin-bottom:10px;">Fill in before printing. This will not be saved.</p>
                    <div class="resume-row" style="margin-bottom:12px;">
                        <div class="resume-field">
                            <label>Reference 1 - Full Name</label>
                            <input type="text" id="r-ref1-name" placeholder="e.g., Juan dela Cruz" style="width:100%;border:1px solid #d1fae5;border-radius:6px;padding:6px 10px;font-size:13px;">
                        </div>
                        <div class="resume-field">
                            <label>Position / Title</label>
                            <input type="text" id="r-ref1-position" placeholder="e.g., Department Head" style="width:100%;border:1px solid #d1fae5;border-radius:6px;padding:6px 10px;font-size:13px;">
                        </div>
                        <div class="resume-field">
                            <label>Company / Organization</label>
                            <input type="text" id="r-ref1-company" placeholder="e.g., ABC Corporation" style="width:100%;border:1px solid #d1fae5;border-radius:6px;padding:6px 10px;font-size:13px;">
                        </div>
                        <div class="resume-field">
                            <label>Contact Number</label>
                            <input type="text" id="r-ref1-contact" placeholder="e.g., 09xxxxxxxxx" style="width:100%;border:1px solid #d1fae5;border-radius:6px;padding:6px 10px;font-size:13px;">
                        </div>
                    </div>
                    <div class="resume-row">
                        <div class="resume-field">
                            <label>Reference 2 - Full Name</label>
                            <input type="text" id="r-ref2-name" placeholder="e.g., Maria Santos" style="width:100%;border:1px solid #d1fae5;border-radius:6px;padding:6px 10px;font-size:13px;">
                        </div>
                        <div class="resume-field">
                            <label>Position / Title</label>
                            <input type="text" id="r-ref2-position" placeholder="e.g., Professor" style="width:100%;border:1px solid #d1fae5;border-radius:6px;padding:6px 10px;font-size:13px;">
                        </div>
                        <div class="resume-field">
                            <label>Company / Organization</label>
                            <input type="text" id="r-ref2-company" placeholder="e.g., CvSU Carmona" style="width:100%;border:1px solid #d1fae5;border-radius:6px;padding:6px 10px;font-size:13px;">
                        </div>
                        <div class="resume-field">
                            <label>Contact Number</label>
                            <input type="text" id="r-ref2-contact" placeholder="e.g., 09xxxxxxxxx" style="width:100%;border:1px solid #d1fae5;border-radius:6px;padding:6px 10px;font-size:13px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="toast">
        <div class="toast-content">
            <i class="fas fa-check-circle" id="toast-icon"></i>
            <span id="toast-message">Operation completed successfully!</span>
        </div>
    </div>

    <script>
    // Load applications for admin
    async function loadApplications() {
        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            const res = await fetch('/api/admin/applications', {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
                credentials: 'same-origin'
            });
            const data = await res.json();
            if (!data.success) return;

            const apps = data.applications;
            const tbody = document.getElementById('applications-table-body');
            if (!tbody) return;

            // Update counts
            const total = apps.length;
            const pending = apps.filter(a => a.status === 'pending').length;
            const accepted = apps.filter(a => a.status === 'accepted').length;
            const rejected = apps.filter(a => a.status === 'rejected').length;

            const totalEl = document.getElementById('total-apps-count');
            const pendingEl = document.getElementById('pending-apps-count');
            const acceptedEl = document.getElementById('accepted-apps-count');
            const rejectedEl = document.getElementById('rejected-apps-count');
            const badgeEl = document.getElementById('pending-applications-badge');

            if (totalEl) totalEl.textContent = total;
            if (pendingEl) pendingEl.textContent = pending;
            if (acceptedEl) acceptedEl.textContent = accepted;
            if (rejectedEl) rejectedEl.textContent = rejected;
            if (badgeEl) badgeEl.textContent = pending + ' pending';

            if (apps.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align:center; padding:20px; color:#94a3b8;">No applications yet.</td></tr>';
                return;
            }

            tbody.innerHTML = apps.map(app => {
                const statusColors = { pending: '#f59e0b', accepted: '#10b981', rejected: '#ef4444', sent: '#3b82f6' };
                const color = statusColors[app.status] || '#6b7280';
                const date = app.created_at ? new Date(app.created_at).toLocaleDateString('en-PH') : '-';
                return `<tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:10px; font-weight:600;">${app.first_name} ${app.last_name}</td>
                    <td style="padding:10px; color:#64748b;">${app.cvsu_email}</td>
                    <td style="padding:10px;">${app.job_title}</td>
                    <td style="padding:10px; color:#64748b;">${app.employer_name}</td>
                    <td style="padding:10px; color:#64748b;">${date}</td>
                    <td style="padding:10px;">
                        <span style="background:${color}20; color:${color}; padding:3px 10px; border-radius:10px; font-size:12px; font-weight:600; text-transform:capitalize;">${app.status}</span>
                    </td>
                    <td style="padding:10px;">
                        <select onchange="updateAppStatus(${app.application_id}, this.value)" style="padding:4px 8px; border-radius:6px; border:1px solid #e2e8f0; font-size:12px; cursor:pointer;">
                            <option value="pending" ${app.status==='pending'?'selected':''}>Pending</option>
                            <option value="sent" ${app.status==='sent'?'selected':''}>Sent to Employer</option>
                            <option value="accepted" ${app.status==='accepted'?'selected':''}>Accepted</option>
                            <option value="rejected" ${app.status==='rejected'?'selected':''}>Rejected</option>
                        </select>
                    </td>
                </tr>`;
            }).join('');
        } catch(e) {
            console.error('Error loading applications:', e);
        }
    }

    async function updateAppStatus(appId, status) {
        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            await fetch(`/api/admin/applications/${appId}/status`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
                credentials: 'same-origin',
                body: JSON.stringify({ status })
            });
            loadApplications();
        } catch(e) {
            console.error('Error updating status:', e);
        }
    }

    

    function previewProfilePhoto(event) {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            const photo = e.target.result;

            // Update profile photo preview box
            const preview = document.getElementById('profile-photo-preview');
            const icon = document.getElementById('profile-photo-icon');
            if (icon) icon.style.display = 'none';
            preview.innerHTML = `<img src="${photo}" style="width:100%;height:100%;object-fit:cover;">`;

            // Show remove button
            const removeBtn = document.getElementById('remove-photo-btn');
            if (removeBtn) removeBtn.style.display = 'inline-block';

            // Change Upload button to "Save Photo" hint
            const uploadBtn = document.querySelector('button[onclick*="profile-photo-input"]');
            if (uploadBtn) {
                uploadBtn.innerHTML = '<i class="fas fa-save"></i> Save Profile to Save Photo';
                uploadBtn.style.background = '#f59e0b';
            }

            // Update sidebar avatar
            const avatar = document.getElementById('sidebar-avatar-initials');
            if (avatar) {
                avatar.style.backgroundImage = `url(${photo})`;
                avatar.style.backgroundSize = 'cover';
                avatar.style.backgroundPosition = 'center';
                avatar.style.color = 'transparent';
                avatar.textContent = '';
            }
        };
        reader.readAsDataURL(file);
    }

    function removeProfilePhoto() {
        const user = JSON.parse(sessionStorage.getItem('currentUser') || '{}');
        const studentNo = user.student_number || user.id || 'guest';

        const preview = document.getElementById('profile-photo-preview');
        preview.innerHTML = '<i class="fas fa-user" id="profile-photo-icon" style="font-size:36px; color:#94a3b8;"></i>';
        sessionStorage.removeItem('profilePhoto_' + studentNo);
        document.getElementById('profile-photo-input').value = '';
        const removeBtn = document.getElementById('remove-photo-btn');
        if (removeBtn) removeBtn.style.display = 'none';

        // Reset upload button
        const uploadBtn = document.querySelector('button[onclick*="profile-photo-input"]');
        if (uploadBtn) {
            uploadBtn.innerHTML = '<i class="fas fa-upload"></i> Upload Photo';
            uploadBtn.style.background = '';
        }

        // Restore sidebar avatar to initials
        const avatar = document.getElementById('sidebar-avatar-initials');
        if (avatar) {
            const initials = (user.name || 'U').split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
            avatar.style.backgroundImage = '';
            avatar.style.color = '';
            avatar.textContent = initials;
        }
    }

    // Load saved photo on page load — per student
    document.addEventListener('DOMContentLoaded', function() {
        const user = JSON.parse(sessionStorage.getItem('currentUser') || '{}');
        const studentNo = user.student_number || user.id || 'guest';
        const savedPhoto = sessionStorage.getItem('profilePhoto_' + studentNo);
        if (savedPhoto) {
            const preview = document.getElementById('profile-photo-preview');
            if (preview) {
                preview.innerHTML = `<img src="${savedPhoto}" style="width:100%;height:100%;object-fit:cover;">`;
                const removeBtn = document.getElementById('remove-photo-btn');
                if (removeBtn) removeBtn.style.display = 'inline-block';
            }
            const avatar = document.getElementById('sidebar-avatar-initials');
            if (avatar) {
                avatar.style.backgroundImage = `url(${savedPhoto})`;
                avatar.style.backgroundSize = 'cover';
                avatar.style.backgroundPosition = 'center';
                avatar.style.color = 'transparent';
                avatar.textContent = '';
            }
        }
    });
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('resume-modal');
        if (modal) modal.addEventListener('click', function(e) { if (e.target === this) closeResumeModal(); });
        const btn = document.getElementById('view-resume-btn');
        if (btn) btn.onclick = function(e) { e.preventDefault(); openResumeModal(); };
    });
    function openResumeModal() {
        const user = JSON.parse(sessionStorage.getItem('currentUser') || '{}');

        // Guard: check if resume modal elements exist
        if (!document.getElementById('r-fullname')) {
            console.error('Resume modal elements not found. Make sure the profile section is rendered.');
            return;
        }

        // Header
        const name = document.getElementById('profile-name')?.value || user.name || '-';
        const email = document.getElementById('profile-email')?.value || user.email || '-';
        const contact = document.getElementById('profile-contact')?.value || '-';
        const studentNo = document.getElementById('profile-student-id')?.value || user.student_number || '-';

        document.getElementById('r-fullname').textContent = name;
        document.getElementById('r-email').innerHTML = '<i class="fas fa-envelope"></i> ' + email;
        document.getElementById('r-contact').innerHTML = '<i class="fas fa-phone"></i> ' + contact;
        document.getElementById('r-studentno').innerHTML = '<i class="fas fa-id-card"></i> Student No: ' + studentNo;
        document.getElementById('r-name2').textContent = name;
        document.getElementById('r-email2').textContent = email;
        document.getElementById('r-contact2').textContent = contact;
        document.getElementById('r-studentno2').textContent = studentNo;

        // Education
        const degree = document.getElementById('profile-degree')?.value || '-';
        const year = document.getElementById('profile-year-level')?.value || '-';
        const gwa = document.getElementById('profile-gwa')?.value || '-';
        const section = document.getElementById('profile-section')?.value || '-';
        const yearLabels = {'1':'1st Year','2':'2nd Year','3':'3rd Year','4':'4th Year','5':'5th Year'};

        document.getElementById('r-degree').textContent = degree || '-';
        document.getElementById('r-year').textContent = yearLabels[year] || year || '-';
        document.getElementById('r-gwa').textContent = gwa || '-';
        document.getElementById('r-section').textContent = section || '-';

        // School Background — pulled from registration data in DB via session
        const reg = JSON.parse(sessionStorage.getItem('registrationData') || '{}');
        document.getElementById('r-shs-school').textContent = reg.shsSchool || '-';
        document.getElementById('r-shs-year').textContent = reg.shsYearGrad || '-';
        document.getElementById('r-hs-school').textContent = reg.hsSchool || '-';
        document.getElementById('r-hs-year').textContent = reg.hsYearGrad || '-';
        document.getElementById('r-elem-school').textContent = reg.elemSchool || '-';
        document.getElementById('r-elem-year').textContent = reg.elemYearGrad || '-';

        // Guardian
        document.getElementById('r-guardian').textContent = reg.parentGuardian || '-';
        document.getElementById('r-relation').textContent = reg.parentRelationship || '-';
        document.getElementById('r-guardian-contact').textContent = reg.parentCellphone || '-';
        document.getElementById('r-guardian-address').textContent = reg.parentAddress || '-';

        // Skills
        const skillsRaw = document.getElementById('profile-skills')?.value || '';
        const skillsContainer = document.getElementById('r-skills');
        if (skillsRaw.trim()) {
            skillsContainer.innerHTML = skillsRaw.split(',')
                .map(s => s.trim())
                .filter(Boolean)
                .map(s => `<span class="resume-skill-tag">${s}</span>`)
                .join('');
        } else {
            skillsContainer.innerHTML = '<span style="color:#94a3b8;font-size:13px;">No skills added yet.</span>';
        }

        document.getElementById('resume-modal').classList.add('show');
    }

    function closeResumeModal() {
        document.getElementById('resume-modal').classList.remove('show');
    }

    // Close modal when clicking outside
    const resumeModalEl = document.getElementById('resume-modal');
    if (resumeModalEl) {
        resumeModalEl.addEventListener('click', function(e) {
            if (e.target === this) closeResumeModal();
        });
    }

    // Initialize data on load
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Data !== 'undefined') Data.initialize();
    });

    // ===== ACTIVITIES TAB LOGIC =====
    document.addEventListener('DOMContentLoaded', function() {
        // Tab switching (extend existing tab logic)
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const tab = this.dataset.tab;
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => { c.style.display = 'none'; c.classList.remove('active'); });
                this.classList.add('active');
                const content = document.getElementById(tab);
                if (content) { content.style.display = 'block'; content.classList.add('active'); }
                if (tab === 'activities-tab') loadActivitiesData();
            });
        });

        // Event filter change
        const eventFilter = document.getElementById('activities-event-filter');
        if (eventFilter) {
            eventFilter.addEventListener('change', function() {
                loadRegistrantsForEvent(this.value);
            });
        }
    });

    async function loadActivitiesData() {
        try {
            const res = await fetch('/api/announcements');
            const data = await res.json();
            const events = (data.announcements || []).filter(a => a.announcement_type === 'event' || a.announcement_type === 'seminar' || true);

            // Populate event filter dropdown
            const filter = document.getElementById('activities-event-filter');
            if (filter) {
                filter.innerHTML = '<option value="">-- Select Event --</option>' +
                    events.map(e => `<option value="${e.announcement_id}">${e.title}</option>`).join('');
            }

            // Load attendance stats per event
            let totalRegistrants = 0, totalAttended = 0;
            const tbody = document.getElementById('activities-events-body');
            if (!tbody) return;

            if (events.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:20px;color:#94a3b8;">No events found.</td></tr>';
                return;
            }

            // Fetch attendance per event
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            const rows = await Promise.all(events.map(async (ev) => {
                let registrants = 0, attended = 0;
                try {
                    const attRes = await fetch(`/api/admin/announcements/${ev.announcement_id}/registrants`, {
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
                        credentials: 'same-origin'
                    });
                    if (attRes.ok) {
                        const attData = await attRes.json();
                        registrants = attData.registrant_count || 0;
                        attended = attData.attendance_count || 0;
                    }
                } catch(e) {}
                totalRegistrants += registrants;
                totalAttended += attended;
                const date = ev.start_date ? new Date(ev.start_date).toLocaleDateString('en-PH', {year:'numeric',month:'short',day:'numeric'}) : '-';
                return `<tr>
                    <td style="padding:10px;border-bottom:1px solid #f0f0f0;">${ev.title}</td>
                    <td style="padding:10px;border-bottom:1px solid #f0f0f0;">${date}</td>
                    <td style="padding:10px;border-bottom:1px solid #f0f0f0;">${ev.location || '-'}</td>
                    <td style="padding:10px;border-bottom:1px solid #f0f0f0;">${registrants}</td>
                    <td style="padding:10px;border-bottom:1px solid #f0f0f0;">${attended}</td>
                    <td style="padding:10px;border-bottom:1px solid #f0f0f0;">
                        <button class="btn-primary btn-sm" onclick="openEventDetailView(${ev.announcement_id}, '${ev.title.replace(/'/g, "\\'")}')">
                            <i class="fas fa-eye"></i> View
                        </button>
                    </td>
                </tr>`;
            }));

            tbody.innerHTML = rows.join('');
            const totalEventsEl = document.getElementById('act-total-events');
            if (totalEventsEl) totalEventsEl.textContent = events.length;
            const totalRegEl = document.getElementById('act-total-registrants');
            if (totalRegEl) totalRegEl.textContent = totalRegistrants;
            const totalAttEl = document.getElementById('act-total-attendance');
            if (totalAttEl) totalAttEl.textContent = totalAttended;

        } catch(e) {
            console.error('Activities load error:', e);
        }
    }

    async function loadRegistrantsForEvent(eventId) {
        if (!eventId) return;
        const tbody = document.getElementById('activities-registrants-body');
        const summary = document.getElementById('activities-attendance-summary');
        if (tbody) tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:20px;color:#94a3b8;">Loading...</td></tr>';

        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            const res = await fetch(`/api/admin/announcements/${eventId}/registrants`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
                credentials: 'same-origin'
            });
            const data = await res.json();
            const registrants = data.registrants || [];

            if (tbody) {
                if (registrants.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:20px;color:#94a3b8;">No registrants yet.</td></tr>';
                } else {
                    tbody.innerHTML = registrants.map(r => {
                        const status = r.attendance_status || 'pending';
                        const statusStyle = status === 'present'
                            ? 'background:#d1fae5;color:#065f46;'
                            : status === 'absent'
                                ? 'background:#fee2e2;color:#991b1b;'
                                : 'background:#fef3c7;color:#92400e;';
                        const statusLabel = status === 'present' ? '✅ Present' : status === 'absent' ? '❌ Absent' : '⏳ Pending';
                        return `<tr>
                            <td style="padding:10px;border-bottom:1px solid #f0f0f0;">${r.first_name || ''} ${r.last_name || ''}</td>
                            <td style="padding:10px;border-bottom:1px solid #f0f0f0;">${r.student_number || '-'}</td>
                            <td style="padding:10px;border-bottom:1px solid #f0f0f0;">${r.program || r.course || '-'}</td>
                            <td style="padding:10px;border-bottom:1px solid #f0f0f0;">${r.section || '-'}</td>
                            <td style="padding:10px;border-bottom:1px solid #f0f0f0;">${r.created_at ? new Date(r.created_at).toLocaleDateString('en-PH') : '-'}</td>
                            <td style="padding:10px;border-bottom:1px solid #f0f0f0;">
                                <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;${statusStyle}">
                                    ${statusLabel}
                                </span>
                            </td>
                        </tr>`;
                    }).join('');
                }
            }

            // Attendance summary
            const presentCount = registrants.filter(r => r.attendance_status === 'present').length;
            const absentCount = registrants.filter(r => r.attendance_status === 'absent').length;
            const pendingCount = registrants.filter(r => r.attendance_status === 'pending').length;
            if (summary) {
                summary.innerHTML = `
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
                        <div style="background:#f0fdf4;padding:16px;border-radius:10px;text-align:center;">
                            <div style="font-size:24px;font-weight:700;color:#2E7D32;">${registrants.length}</div>
                            <div style="font-size:12px;color:#555;">Total Registered</div>
                        </div>
                        <div style="background:#f0fdf4;padding:16px;border-radius:10px;text-align:center;">
                            <div style="font-size:24px;font-weight:700;color:#2E7D32;">${presentCount}</div>
                            <div style="font-size:12px;color:#555;">Attended</div>
                        </div>
                        <div style="background:#fff7ed;padding:16px;border-radius:10px;text-align:center;">
                            <div style="font-size:24px;font-weight:700;color:#ea580c;">${absentCount}</div>
                            <div style="font-size:12px;color:#555;">Absent</div>
                        </div>
                    </div>`;
            }
        } catch(e) {
            if (tbody) tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:20px;color:#ef4444;">Error loading data.</td></tr>';
        }
    }

    // ===== EVENT DETAIL VIEW FUNCTIONS =====
    let currentEventId = null;
    let allRegistrantsData = [];

    function openEventDetailView(eventId, eventTitle) {
        currentEventId = eventId;
        allRegistrantsData = [];

        // Hide events list table, show detail view header
        const eventsListDiv = document.getElementById('activities-events-table')?.closest('div[style*="margin-bottom: 24px"]') || document.getElementById('activities-events-table')?.closest('div');
        document.getElementById('event-detail-view').style.display = 'block';
        document.getElementById('event-detail-title').innerHTML = `<i class="fas fa-calendar-check" style="color:#2E7D32;"></i> ${eventTitle}`;
        document.getElementById('registrants-event-name').textContent = `— ${eventTitle}`;

        // Hide the events list section
        const actStatCards = document.querySelector('#activities-tab > div:nth-child(1)');
        document.getElementById('act-events-list-box').style.display = 'none';

        // Show registrants & attendance sections
        document.getElementById('registrants-section').style.display = 'block';
        document.getElementById('attendance-section').style.display = 'block';

        // Load data
        loadRegistrantsForEvent(eventId);

        // Populate program filter
        populateProgramFilter(eventId);

        // Scroll to top of activities tab
        document.getElementById('activities-tab').scrollIntoView({ behavior: 'smooth' });
    }

    function backToEventsList() {
        document.getElementById('event-detail-view').style.display = 'none';
        currentEventId = null;
        allRegistrantsData = [];
        document.getElementById('registrant-section-count').style.display = 'none';
        document.getElementById('act-events-list-box').style.display = 'block';
        document.getElementById('activities-program-filter').value = '';
        document.getElementById('activities-section-filter').value = '';
        document.getElementById('activities-registrants-body').innerHTML = '<tr><td colspan="6" style="text-align:center;padding:20px;color:#94a3b8;">Select an event to view registrants.</td></tr>';
        document.getElementById('activities-attendance-summary').innerHTML = '<span style="color:#94a3b8;font-size:13px;">Select an event above to view attendance.</span>';
        document.getElementById('registrants-event-name').textContent = '';
    }

    async function populateProgramFilter(eventId) {
        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            const res = await fetch(`/api/admin/announcements/${eventId}/registrants`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
                credentials: 'same-origin'
            });
            const data = await res.json();
            allRegistrantsData = data.registrants || [];

            // Get unique programs
            const programs = [...new Set(allRegistrantsData.map(r => r.program || r.course).filter(Boolean))];
            const programSelect = document.getElementById('activities-program-filter');
            programSelect.innerHTML = '<option value="">-- All Programs --</option>' +
                programs.map(p => `<option value="${p}">${p}</option>`).join('');
        } catch(e) {}
    }

    function applyRegistrantFilters() {
        const programFilter = document.getElementById('activities-program-filter').value.trim().toLowerCase();
        const sectionFilter = document.getElementById('activities-section-filter').value.trim().toLowerCase();

        let filtered = allRegistrantsData;
        if (programFilter) filtered = filtered.filter(r => (r.program || r.course || '').toLowerCase() === programFilter);
        if (sectionFilter) filtered = filtered.filter(r => (r.section || '').toLowerCase().includes(sectionFilter));

        renderRegistrantsTable(filtered);

        // Show total count
        const countEl = document.getElementById('registrant-section-count');
        const countVal = document.getElementById('registrant-count-value');
        countEl.style.display = 'inline-flex';
        countVal.textContent = filtered.length;
    }

    function clearRegistrantFilters() {
        document.getElementById('activities-program-filter').value = '';
        document.getElementById('activities-section-filter').value = '';
        document.getElementById('registrant-section-count').style.display = 'none';
        renderRegistrantsTable(allRegistrantsData);
    }

    function renderRegistrantsTable(registrants) {
        const tbody = document.getElementById('activities-registrants-body');
        if (!tbody) return;
        if (registrants.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:20px;color:#94a3b8;">No registrants found.</td></tr>';
        } else {
            tbody.innerHTML = registrants.map(r => {
                const status = r.attendance_status || 'pending';
                const statusStyle = status === 'present'
                    ? 'background:#d1fae5;color:#065f46;'
                    : status === 'absent'
                        ? 'background:#fee2e2;color:#991b1b;'
                        : 'background:#fef3c7;color:#92400e;';
                const statusLabel = status === 'present' ? '✅ Present' : status === 'absent' ? '❌ Absent' : '⏳ Pending';
                return `<tr>
                    <td style="padding:10px;border-bottom:1px solid #f0f0f0;">${r.last_name || ''}, ${r.first_name || ''}</td>
                    <td style="padding:10px;border-bottom:1px solid #f0f0f0;">${r.student_number || '-'}</td>
                    <td style="padding:10px;border-bottom:1px solid #f0f0f0;">${r.program || r.course || '-'}</td>
                    <td style="padding:10px;border-bottom:1px solid #f0f0f0;">${r.section || '-'}</td>
                    <td style="padding:10px;border-bottom:1px solid #f0f0f0;">${r.created_at ? new Date(r.created_at).toLocaleDateString('en-PH') : '-'}</td>
                    <td style="padding:10px;border-bottom:1px solid #f0f0f0;">
                        <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;${statusStyle}">${statusLabel}</span>
                    </td>
                </tr>`;
            }).join('');
        }
        // Update attendance summary
        updateAttendanceSummary(registrants);
    }

    function updateAttendanceSummary(registrants) {
        const summary = document.getElementById('activities-attendance-summary');
        if (!summary) return;
        const presentCount = registrants.filter(r => r.attendance_status === 'present').length;
        const absentCount = registrants.filter(r => r.attendance_status === 'absent').length;
        const pendingCount = registrants.filter(r => !r.attendance_status || r.attendance_status === 'pending').length;
        summary.innerHTML = `
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;">
                <div style="background:#f0fdf4;padding:16px;border-radius:10px;text-align:center;">
                    <div style="font-size:24px;font-weight:700;color:#2E7D32;">${registrants.length}</div>
                    <div style="font-size:12px;color:#555;">Total Registered</div>
                </div>
                <div style="background:#f0fdf4;padding:16px;border-radius:10px;text-align:center;">
                    <div style="font-size:24px;font-weight:700;color:#2E7D32;">${presentCount}</div>
                    <div style="font-size:12px;color:#555;">Attended</div>
                </div>
                <div style="background:#fef3c7;padding:16px;border-radius:10px;text-align:center;">
                    <div style="font-size:24px;font-weight:700;color:#d97706;">${pendingCount}</div>
                    <div style="font-size:12px;color:#555;">Pending</div>
                </div>
                <div style="background:#fff1f2;padding:16px;border-radius:10px;text-align:center;">
                    <div style="font-size:24px;font-weight:700;color:#e11d48;">${absentCount}</div>
                    <div style="font-size:12px;color:#555;">Absent</div>
                </div>
            </div>`;
    }

    function navigateTo(sectionId) {
        document.querySelectorAll('.dashboard-section').forEach(s => s.style.display = 'none');
        const target = document.getElementById(sectionId);
        if (target) {
            target.style.display = 'block';
            target.scrollIntoView({ behavior: 'smooth' });
        }
        document.querySelectorAll('#sidebar-menu a').forEach(a => {
            a.parentElement.classList.remove('active');
            if (a.getAttribute('href') === '#' + sectionId) {
                a.parentElement.classList.add('active');
            }
        });
    }

    function toggleOjtHours(type) {
        const ojtRow = document.getElementById('ojt-hours-row');
        if (ojtRow) {
            ojtRow.style.display = (type === 'internship') ? 'grid' : 'none';
            const hoursInput = document.getElementById('job-ojt-hours');
            if (hoursInput) hoursInput.required = (type === 'internship');
        }
        // Kung student ang naka-login at pumili ng internship, redirect sa OJT Offerings
        const user = JSON.parse(sessionStorage.getItem('currentUser') || '{}');
        if (type === 'internship' && user.role === 'student') {
            navigateTo('ojt-offerings');
        }
    }
    </script>

    <!-- JavaScript Modules -->
    <script src="{{ asset('js/utils.js') }}"></script>
    <script src="{{ asset('js/api.js') }}"></script>
    <script src="{{ asset('js/data.js') }}"></script>
    <script src="{{ asset('js/auth.js') }}"></script>
    <script src="{{ asset('js/ui.js') }}"></script>
    <script src="{{ asset('js/student.js') }}"></script>
    <script src="{{ asset('js/employer.js') }}"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>