{{--
==================================================================================
📍 FILE LOCATION: resources/views/auth/register.blade.php
==================================================================================

INSTRUCTIONS:
1. Go to your Laravel project folder
2. Navigate to: resources/views/auth/
3. REPLACE the existing register.blade.php with this file
4. Save the file

FULL PATH: resources/views/auth/register.blade.php

⚠️ IMPORTANT: Also run the migration:
   php artisan migrate

==================================================================================
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CAReERS Registration | CVSU</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        /* ═══════════════════════════════════════════════
           FIX 1: FULL SCREEN NO SCROLL
        ═══════════════════════════════════════════════ */
        html, body {
            height: 100%;
            overflow: hidden;
        }

        body {
            padding: 0;
        }

        .login-container {
            height: 100vh;
            min-height: 100vh;
            overflow: hidden;
            display: flex;
            max-width: 100vw;
            width: 100vw;
            border-radius: 0;
        }

        /* Make green branding panel smaller on register */
        .login-brand {
            flex: 0 0 30%;
            overflow: hidden;
            padding: 22px 18px;
        }

        .brand-title {
            font-size: 32px;
        }

        .brand-description {
            margin-bottom: 0;
        }

        .login-form-container {
            flex: 0 0 70%;
            overflow: hidden;
            height: 100vh;
            padding: 12px 16px;
            align-items: flex-start;
            justify-content: flex-start;
        }

        /* ═══════════════════════════════════════════════
           FIX 2: CIRCULAR LOGO — no corners
        ═══════════════════════════════════════════════ */
        .logo-wrapper {
            border-radius: 50% !important;
            overflow: hidden !important;
        }

        .brand-logo {
            border-radius: 50% !important;
            object-fit: cover !important;
        }

        /* ═══════════════════════════════════════════════
           ALL ORIGINAL STYLES BELOW — UNCHANGED
        ═══════════════════════════════════════════════ */
        .register-form {
            max-width: 100% !important;
            height: 100%;
        }

        .progress-container {
            margin-bottom: 12px;
        }

        .progress-bar-wrapper {
            margin-bottom: 14px;
        }

        .progress-step-circle {
            width: 34px;
            height: 34px;
            font-size: 14px;
            margin: 0 auto 6px;
        }

        .progress-step-label {
            font-size: 10px;
        }

        .step-title {
            font-size: 18px;
            margin-bottom: 6px;
        }

        .step-description {
            margin-bottom: 10px;
            font-size: 13px;
        }

        .form-group {
            margin-bottom: 9px;
        }

        .form-group label {
            margin-bottom: 6px;
            font-size: 12px;
        }

        .form-input {
            padding: 10px 12px;
            font-size: 13px;
        }

        .photo-upload {
            margin-bottom: 8px;
        }

        .photo-preview {
            width: 90px;
            height: 90px;
            margin: 0 auto 6px;
        }

        .btn-primary {
            padding: 8px 14px;
            font-size: 12px;
        }

        .form-navigation {
            margin-top: 10px;
            padding-top: 10px;
            position: sticky;
            bottom: 0;
            background: linear-gradient(180deg, rgba(255,255,255,0.92) 0%, #fff 25%);
            z-index: 5;
        }

        .btn-prev, .btn-next {
            padding: 10px 14px;
            font-size: 12px;
        }

        .form-row-2, .form-row-3 {
            gap: 10px;
        }
        
        /* Progress Bar */
        .progress-container {
            margin-bottom: 30px;
        }
        
        .progress-bar-wrapper {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 30px;
        }
        
        .progress-bar-wrapper::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 3px;
            background: #e9ecef;
            z-index: 0;
        }
        
        .progress-line {
            position: absolute;
            top: 20px;
            left: 0;
            height: 3px;
            background: var(--cvsu-green);
            z-index: 1;
            transition: width 0.3s ease;
        }
        
        .progress-step {
            position: relative;
            z-index: 2;
            text-align: center;
            flex: 1;
        }
        
        .progress-step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: 700;
            font-size: 16px;
            transition: all 0.3s ease;
            border: 3px solid #e9ecef;
            cursor: pointer;
        }

        .progress-step.locked .progress-step-circle {
            cursor: not-allowed;
        }
        
        .progress-step.active .progress-step-circle {
            background: var(--cvsu-green);
            color: white;
            border-color: var(--cvsu-green);
            transform: scale(1.1);
        }
        
        .progress-step.completed .progress-step-circle {
            background: var(--cvsu-green);
            color: white;
            border-color: var(--cvsu-green);
        }
        
        .progress-step-label {
            font-size: 11px;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .progress-step.active .progress-step-label {
            color: var(--cvsu-green);
        }
        
        /* Form Steps */
        .form-step {
            display: none;
            animation: fadeIn 0.4s ease;
        }
        
        .form-step.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .step-title {
            font-size: 24px;
            font-weight: 800;
            color: var(--cvsu-green);
            margin-bottom: 10px;
        }
        
        .step-description {
            font-size: 14px;
            color: var(--text-medium);
            margin-bottom: 30px;
        }
        
        /* Form Navigation Buttons */
        .form-navigation {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #e9ecef;
        }
        
        .btn-prev, .btn-next {
            flex: 1;
            padding: 14px 24px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-family: var(--font-main);
            font-weight: 700;
            font-size: 14px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-prev {
            background: #e9ecef;
            color: var(--text-dark);
        }
        
        .btn-prev:hover {
            background: #dee2e6;
            transform: translateY(-2px);
        }
        
        .btn-next {
            background: linear-gradient(135deg, var(--cvsu-green) 0%, var(--cvsu-green-light) 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(46, 125, 30, 0.3);
        }
        
        .btn-next:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(46, 125, 30, 0.4);
        }

        .btn-next:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .btn-prev .material-symbols-outlined,
        .btn-next .material-symbols-outlined {
            font-size: 20px;
        }
        
        /* Photo Upload */
        .photo-upload {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .photo-preview {
            width: 150px;
            height: 150px;
            border: 3px solid var(--cvsu-green);
            border-radius: 12px;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            overflow: hidden;
        }
        
        .photo-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .photo-preview .material-symbols-outlined {
            font-size: 48px;
            color: #ccc;
        }
        
        /* Form Row Layouts */
        .form-row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .form-row-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }
        
        /* Radio/Checkbox Groups */
        .radio-group, .checkbox-group {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-top: 10px;
        }
        
        .radio-option, .checkbox-option {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .radio-option:hover, .checkbox-option:hover {
            border-color: var(--cvsu-green);
            background: rgba(46, 125, 30, 0.05);
        }
        
        .radio-option input:checked + span,
        .checkbox-option input:checked + span {
            color: var(--cvsu-green);
            font-weight: 600;
        }

        .btn-primary {
            padding: 10px 20px;
            background: var(--cvsu-green);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--cvsu-green-dark);
            transform: translateY(-2px);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .form-row-2, .form-row-3 {
                grid-template-columns: 1fr;
            }
            
            .progress-step-label {
                font-size: 9px;
            }
            
            .progress-step-circle {
                width: 35px;
                height: 35px;
                font-size: 14px;
            }
        }

        /* Final layout overrides (kept last so they win) */
        html, body {
            height: 100%;
            overflow: auto !important;
        }

        body {
            padding: 12px !important;
            align-items: flex-start;
        }

        .login-container {
            height: auto !important;
            min-height: calc(100vh - 24px) !important;
            max-width: 1500px !important;
            width: 100% !important;
            border-radius: 16px !important;
            overflow: hidden !important;
            box-shadow: 0 14px 42px rgba(16, 24, 40, 0.14) !important;
            border: 1px solid rgba(255, 255, 255, 0.6);
        }

        .login-brand {
            flex: 0 0 24% !important;
            padding: 18px 14px !important;
            background: linear-gradient(180deg, #3b9f2f 0%, #2e7d1e 48%, #215f16 100%);
        }

        .brand-title {
            font-size: 28px !important;
            letter-spacing: 1.5px;
        }

        .brand-subtitle {
            font-size: 13px;
            margin-bottom: 6px;
            color: rgba(255, 255, 255, 0.95);
        }

        .brand-description {
            font-size: 12px;
            margin-bottom: 0;
            color: rgba(255, 255, 255, 0.88);
        }

        .logo-wrapper {
            width: 88px;
            height: 88px;
            margin-bottom: 14px;
        }

        .login-form-container {
            flex: 1 1 76% !important;
            height: auto !important;
            max-height: calc(100vh - 24px) !important;
            overflow-y: auto !important;
            padding: 14px 16px !important;
            background:
                radial-gradient(circle at 4% 6%, rgba(46, 125, 30, 0.05), transparent 42%),
                radial-gradient(circle at 96% 96%, rgba(46, 125, 30, 0.04), transparent 38%),
                linear-gradient(135deg, #ffffff 0%, #fbfdf9 100%);
        }

        .register-form {
            height: auto !important;
            max-width: 100% !important;
        }

        .progress-container,
        .progress-bar-wrapper {
            margin-bottom: 10px !important;
        }

        .progress-bar-wrapper::before {
            height: 2px;
            background: #dfe5dd;
        }

        .progress-step-circle {
            width: 30px !important;
            height: 30px !important;
            font-size: 12px !important;
            margin: 0 auto 4px !important;
        }

        .progress-step-label {
            font-size: 9px !important;
            letter-spacing: 0.3px;
        }

        .step-title {
            font-size: 18px !important;
            margin-bottom: 4px !important;
            letter-spacing: 0.2px;
        }

        .step-description {
            font-size: 12px !important;
            margin-bottom: 8px !important;
            color: #6f7782;
        }

        .photo-upload {
            margin-bottom: 8px !important;
        }

        .photo-upload-card {
            display: flex;
            align-items: center;
            gap: 10px;
            width: fit-content;
            min-width: 360px;
            max-width: 100%;
            padding: 8px 10px;
            border: 1px solid #e2e9df;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 6px 16px rgba(17, 24, 39, 0.08);
        }

        .photo-upload-meta {
            display: flex;
            flex-direction: column;
            justify-content: center;
            margin-right: 4px;
            min-width: 120px;
        }

        .photo-upload-label {
            font-size: 11px;
            font-weight: 700;
            color: #223128;
            line-height: 1.2;
        }

        .photo-upload-hint {
            font-size: 10px;
            color: #6f7782;
            line-height: 1.2;
            margin-top: 2px;
        }

        .photo-preview {
            width: 82px !important;
            height: 82px !important;
            margin: 0 !important;
            border-radius: 14px !important;
            border: 2px dashed rgba(46, 125, 30, 0.65) !important;
            background: linear-gradient(135deg, #f7fbf6 0%, #ffffff 100%) !important;
        }

        .photo-preview .material-symbols-outlined {
            color: #b7bfbc;
        }

        .form-group {
            margin-bottom: 8px !important;
        }

        .form-group label {
            font-size: 11px !important;
            margin-bottom: 4px !important;
            color: #223128;
            font-weight: 600;
        }

        .form-input,
        select.form-input,
        input[type="date"].form-input,
        input[type="text"].form-input,
        input[type="number"].form-input,
        input[type="email"].form-input,
        input[type="password"].form-input {
            padding: 8px 10px !important;
            font-size: 12px !important;
            min-height: 38px;
            border-radius: 11px;
            border: 1.5px solid #d9e2d5;
            background: #fff;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.12s ease;
        }

        .form-input:hover {
            border-color: #b9cdb2;
        }

        .form-input:focus {
            border-color: #2e7d1e !important;
            box-shadow: 0 0 0 3px rgba(46, 125, 30, 0.14) !important;
            transform: translateY(-1px);
        }

        .form-input[readonly] {
            background: #f4f6f4;
            color: #4d5a52;
            cursor: not-allowed;
        }

        .popup-overlay {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(10, 18, 14, 0.45);
            z-index: 1200;
            padding: 16px;
        }

        .popup-overlay.show {
            display: flex;
        }

        .popup-card {
            width: min(460px, 100%);
            background: #fff;
            border-radius: 14px;
            border: 1px solid #dce5d8;
            box-shadow: 0 16px 40px rgba(8, 20, 12, 0.22);
            overflow: hidden;
        }

        .popup-header {
            padding: 14px 16px;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
        }

        .popup-header.success {
            background: linear-gradient(135deg, #2e7d1e 0%, #3f9b2d 100%);
        }

        .popup-header.error {
            background: linear-gradient(135deg, #b83a2f 0%, #d3544a 100%);
        }

        .popup-body {
            padding: 16px;
            color: #1f2a22;
            font-size: 13px;
            line-height: 1.5;
            white-space: pre-line;
        }

        .popup-actions {
            display: flex;
            justify-content: flex-end;
            padding: 0 16px 16px;
        }

        .popup-button {
            border: none;
            border-radius: 10px;
            padding: 8px 14px;
            font-size: 12px;
            font-weight: 700;
            color: #fff;
            cursor: pointer;
            background: #2e7d1e;
        }

        .form-navigation {
            margin-top: 10px !important;
            padding-top: 8px !important;
            position: static !important;
            background: transparent !important;
            border-top: 1px solid #e6ece4;
        }

        .btn-prev,
        .btn-next,
        .btn-primary {
            padding: 8px 12px !important;
            font-size: 12px !important;
            border-radius: 10px;
        }

        .btn-prev {
            background: #edf2eb !important;
            color: #2f3c34 !important;
        }

        .btn-next,
        .btn-primary {
            background: linear-gradient(135deg, #2e7d1e 0%, #3f9b2d 100%) !important;
            box-shadow: 0 8px 18px rgba(46, 125, 30, 0.3);
        }

        .photo-upload-card .btn-primary {
            margin-left: auto;
            white-space: nowrap;
        }

        .btn-next:hover,
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(46, 125, 30, 0.35);
        }

        .form-row-2,
        .form-row-3 {
            gap: 8px !important;
        }

        @media (min-width: 1200px) {
            .form-row-2 {
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            }

            .form-row-3 {
                grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
            }
        }

        @media (max-width: 1199px) {
            .form-row-2 {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }

            .form-row-3 {
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 0 !important;
            }

            .login-container {
                min-height: 100vh !important;
                border-radius: 0 !important;
            }

            .login-brand {
                display: none;
            }

            .login-form-container {
                max-height: none !important;
            }

            .form-row-2,
            .form-row-3 {
                grid-template-columns: 1fr !important;
            }

            .photo-upload-card {
                min-width: 0;
                width: 100%;
                flex-wrap: wrap;
                justify-content: flex-start;
            }

            .photo-upload-meta {
                min-width: 0;
            }
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

        <!-- Right Side - Multi-Step Form -->
        <div class="login-form-container">
            <div class="login-form-wrapper register-form">
                
                <!-- Progress Bar -->
                <div class="progress-container">
                    <div class="progress-bar-wrapper">
                        <div class="progress-line" id="progressLine"></div>
                        <div class="progress-step active" data-step="1">
                            <div class="progress-step-circle">1</div>
                            <div class="progress-step-label">Personal</div>
                        </div>
                        <div class="progress-step" data-step="2">
                            <div class="progress-step-circle">2</div>
                            <div class="progress-step-label">Address</div>
                        </div>
                        <div class="progress-step" data-step="3">
                            <div class="progress-step-circle">3</div>
                            <div class="progress-step-label">Academic</div>
                        </div>
                        <div class="progress-step" data-step="4">
                            <div class="progress-step-circle">4</div>
                            <div class="progress-step-label">Education</div>
                        </div>
                        <div class="progress-step" data-step="5">
                            <div class="progress-step-circle">5</div>
                            <div class="progress-step-label">Guardian</div>
                        </div>
                        <div class="progress-step" data-step="6">
                            <div class="progress-step-circle">6</div>
                            <div class="progress-step-label">Account</div>
                        </div>
                    </div>
                </div>

                <!-- Registration Form -->
                <form id="registerForm" onsubmit="return false;">
                    
                    <!-- STEP 1: Personal Information -->
                    <div class="form-step active" data-step="1">
                        <h3 class="step-title">Personal Information</h3>
                        <p class="step-description">Tell us about yourself</p>

                        <!-- Photo Upload -->
                        <div class="photo-upload photo-upload-card">
                            <div class="photo-preview" id="photoPreview">
                                <span class="material-symbols-outlined">account_circle</span>
                            </div>
                            <div class="photo-upload-meta">
                                <div class="photo-upload-label">Profile Photo</div>
                                <div class="photo-upload-hint">Upload a clear 1x1 image</div>
                            </div>
                            <input type="file" id="photo" accept="image/*" style="display: none;" onchange="previewPhoto(event)">
                            <button type="button" class="btn-primary" onclick="document.getElementById('photo').click()">
                                <span class="material-symbols-outlined">upload</span>
                                Upload 1x1 Picture
                            </button>
                        </div>

                        <div class="form-row-3">
                            <div class="form-group">
                                <label for="lastName">Last Name *</label>
                                <input type="text" id="lastName" class="form-input" placeholder="Dela Cruz" required oninput="toTitleCase(this)">
                            </div>
                            <div class="form-group">
                                <label for="firstName">First Name *</label>
                                <input type="text" id="firstName" class="form-input" placeholder="Juan" required oninput="toTitleCase(this)">
                            </div>
                            <div class="form-group">
                                <label for="middleName">Middle Name</label>
                                <input type="text" id="middleName" class="form-input" placeholder="Santos" oninput="toTitleCase(this)">
                            </div>
                        </div>

                        <div class="form-row-3">
                            <div class="form-group">
                            <label for="dateOfBirth">Date of Birth *</label>
                            <input type="date" id="dateOfBirth" class="form-input" required onchange="calculateAge(this.value)">
                        </div>
                        <div class="form-group">
                            <label for="placeOfBirth">Place of Birth *</label>
                            <input type="text" id="placeOfBirth" class="form-input" placeholder="Carmona, Cavite" required oninput="toTitleCase(this)">
                        </div>
                        <div class="form-group">
                            <label for="age">Age *</label>
                            <input type="number" id="age" class="form-input" placeholder="18" min="15" max="100" required readonly style="background:#f4f6f4; cursor:allowed;">
                        </div>
                        </div>

                        <div class="form-row-3">
                            <div class="form-group">
                                <label for="sex">Sex *</label>
                                <select id="sex" class="form-input" required>
                                    <option value="">Select</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="religion">Religion</label>
                                <input type="text" id="religion" class="form-input" placeholder="Roman Catholic">
                            </div>
                            <div class="form-group">
                                <label for="civilStatus">Civil Status *</label>
                                <select id="civilStatus" class="form-input" required>
                                    <option value="">Select</option>
                                    <option value="Single">Single</option>
                                    <option value="Married">Married</option>
                                    <option value="Widowed">Widowed</option>
                                    <option value="Separated">Separated</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="nationality">Nationality *</label>
                            <input type="text" id="nationality" class="form-input" placeholder="Filipino" value="Filipino" required>
                        </div>
                    </div>

                    <!-- STEP 2: Address & Contact -->
                    <div class="form-step" data-step="2">
                        <h3 class="step-title">Address & Contact</h3>
                        <p class="step-description">Where can we reach you?</p>

                        <div class="form-group">
                            <label for="houseNo">House No. & Street</label>
                            <input type="text" id="houseNo" class="form-input" placeholder="Blk 12 Lot 8, Mabuhay St." oninput="toTitleCase(this)">
                        </div>

                        <div class="form-row-3">
                            <div class="form-group">
                                <label for="barangay">Barangay *</label>
                                <input type="text" id="barangay" class="form-input" placeholder="Maduya" required oninput="toTitleCase(this)">
                            </div>
                            <div class="form-group">
                                <label for="town">Town/City *</label>
                                <input type="text" id="town" class="form-input" placeholder="Carmona" required oninput="handleTownInput(this)">
                            </div>
                            <div class="form-group">
                                <label for="province">Province *</label>
                                <input type="text" id="province" class="form-input" placeholder="Cavite" required oninput="toTitleCase(this)">
                            </div>
                        </div>

                        <div class="form-row-2">
                            <div class="form-group">
                                <label for="zipCode">Zip Code</label>
                                <input type="text" id="zipCode" class="form-input" placeholder="Auto-filled" readonly style="background:#f4f6f4; cursor:not-allowed;">
                            </div>
                        </div>

                        <div class="form-row-2">
                            <div class="form-group">
                                <label for="landlineNo">Landline No.</label>
                                <input type="tel" id="landlineNo" class="form-input" placeholder="046-XXX-XXXX">
                            </div>
                            <div class="form-group">
                                <label for="cellphoneNo">Cellphone No. *</label>
                                <input type="tel" id="cellphoneNo" class="form-input" placeholder="09XX-XXX-XXXX" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" class="form-input" placeholder="your.email@cvsu.edu.ph" pattern="^[^\s@]+@cvsu\.edu\.ph$" required>
                            <small class="form-hint">Use your official CVSU email</small>
                        </div>
                    </div>

                    <!-- STEP 3: Academic Information -->
                    <div class="form-step" data-step="3">
                        <h3 class="step-title">Academic Information</h3>
                        <p class="step-description">Your current enrollment details</p>

                        <div class="form-row-2">
                            <div class="form-group">
                                <label for="studentNumber">Student Number *</label>
                                <input type="text" id="studentNumber" class="form-input" placeholder="202400123" inputmode="numeric" pattern="^\d{9}$" maxlength="9" required>
                                <small class="form-hint">Student number must be exactly 9 digits</small>
                            </div>
                            <div class="form-group">
                                <label for="yearLevel">Year Level *</label>
                                <select id="yearLevel" class="form-input" required>
                                    <option value="">Select Year Level</option>
                                    <option value="1">1st Year</option>
                                    <option value="2">2nd Year</option>
                                    <option value="3">3rd Year</option>
                                    <option value="4">4th Year</option>
                                    <option value="5">5th Year</option>
                                    <option value="6">6th Year</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row-2">
                            <div class="form-group">
                                <label for="course">Course/Program *</label>
                                <select id="course" class="form-input" required>
                                    <option value="">Select Program</option>
                                    <option value="BSED">Bachelor of Secondary Education</option>
                                    <option value="BSBM">BS Business Management</option>
                                    <option value="BSCpE">BS Computer Engineering</option>
                                    <option value="BSCS">BS Computer Science</option>
                                    <option value="BSHM">BS Hospitality Management</option>
                                    <option value="BSIT">BS Industrial Technology</option>
                                    <option value="BSIT-IT">BS Information Technology</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="section">Section</label>
                                <input type="text" id="section" class="form-input" placeholder="A">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Student Classification *</label>
                            <div class="radio-group">
                                <label class="radio-option">
                                    <input type="radio" name="classification" value="new" required>
                                    <span>New</span>
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="classification" value="continuing">
                                    <span>Continuing</span>
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="classification" value="transferee">
                                    <span>Transferee</span>
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="classification" value="cross_enrollee">
                                    <span>Cross Enrollee</span>
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="classification" value="returnee">
                                    <span>Returnee</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group" id="shifteeFromGroup" style="display: none;">
                            <label for="shifteeFrom">Shiftee From</label>
                            <input type="text" id="shifteeFrom" class="form-input" placeholder="Previous Course">
                        </div>

                        <div class="form-group">
                            <label>Registration Status *</label>
                            <div class="radio-group">
                                <label class="radio-option">
                                    <input type="radio" name="regStatus" value="regular" required>
                                    <span>Regular</span>
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="regStatus" value="irregular">
                                    <span>Irregular</span>
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="regStatus" value="temporary">
                                    <span>Temporary</span>
                                </label>
                            </div>
                        </div>

                        {{-- ══════════════════════════════════════════════════════════ --}}
                        {{-- SPECIAL CATEGORY DROPDOWN                                --}}
                        {{-- ══════════════════════════════════════════════════════════ --}}
                        <div class="form-group">
                            <label for="special_category">Special Category</label>
                            <select id="special_category" name="special_category" class="form-input">
                                <option value="">-- None / Not Applicable --</option>
                                <option value="pwd">PWD – Person with Disability</option>
                                <option value="solo_parent">Solo Parent – Child of a solo parent</option>
                                <option value="orphan">Orphan – Has lost one or both parents</option>
                                <option value="child_ofw">Child of OFW – Overseas Filipino Worker's child</option>
                                <option value="indigenous_peoples">Member of Indigenous Peoples (IP)</option>
                            </select>
                        </div>
                        {{-- ══════════════════════════════════════════════════════════ --}}

                    </div>

                    <!-- STEP 4: Educational Background -->
                    <div class="form-step" data-step="4">
                        <h3 class="step-title">Educational Background</h3>
                        <p class="step-description">Your previous schools</p>

                        <h4 style="font-size: 14px; font-weight: 600; margin: 20px 0 15px 0; color: var(--cvsu-green);">Elementary</h4>
                        <div class="form-group">
                            <label for="elemSchool">School Name</label>
                            <input type="text" id="elemSchool" class="form-input" placeholder="Carmona Elementary School">
                        </div>
                        <div class="form-row-3">
                            <div class="form-group">
                                <label for="elemYearGrad">Year Graduated</label>
                                <input type="text" id="elemYearGrad" class="form-input" placeholder="2015">
                            </div>
                            <div class="form-group">
                                <label for="elemType">School Type</label>
                                <select id="elemType" class="form-input">
                                    <option value="">Select</option>
                                    <option value="public">Public</option>
                                    <option value="private">Private</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="elemAddress">Address</label>
                                <input type="text" id="elemAddress" class="form-input" placeholder="Carmona, Cavite">
                            </div>
                        </div>

                        <h4 style="font-size: 14px; font-weight: 600; margin: 20px 0 15px 0; color: var(--cvsu-green);">High School</h4>
                        <div class="form-group">
                            <label for="hsSchool">School Name</label>
                            <input type="text" id="hsSchool" class="form-input" placeholder="Carmona National High School">
                        </div>
                        <div class="form-row-3">
                            <div class="form-group">
                                <label for="hsYearGrad">Year Graduated</label>
                                <input type="text" id="hsYearGrad" class="form-input" placeholder="2019">
                            </div>
                            <div class="form-group">
                                <label for="hsType">School Type</label>
                                <select id="hsType" class="form-input">
                                    <option value="">Select</option>
                                    <option value="public">Public</option>
                                    <option value="private">Private</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="hsAddress">Address</label>
                                <input type="text" id="hsAddress" class="form-input" placeholder="Carmona, Cavite">
                            </div>
                        </div>

                        <h4 style="font-size: 14px; font-weight: 600; margin: 20px 0 15px 0; color: var(--cvsu-green);">Senior High School</h4>
                        <div class="form-group">
                            <label for="shsSchool">School Name</label>
                            <input type="text" id="shsSchool" class="form-input" placeholder="Carmona Senior High School">
                        </div>
                        <div class="form-row-3">
                            <div class="form-group">
                                <label for="shsYearGrad">Year Graduated</label>
                                <input type="text" id="shsYearGrad" class="form-input" placeholder="2021">
                            </div>
                            <div class="form-group">
                                <label for="shsType">School Type</label>
                                <select id="shsType" class="form-input">
                                    <option value="">Select</option>
                                    <option value="public">Public</option>
                                    <option value="private">Private</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="shsAddress">Address</label>
                                <input type="text" id="shsAddress" class="form-input" placeholder="Carmona, Cavite">
                            </div>
                        </div>

                        <div id="transfereeSection" style="display: none;">
                            <h4 style="font-size: 14px; font-weight: 600; margin: 20px 0 15px 0; color: var(--cvsu-green);">For Transferees/Cross Enrollees</h4>
                            <div class="form-group">
                                <label for="lastSchoolAttended">School Last Attended</label>
                                <input type="text" id="lastSchoolAttended" class="form-input" placeholder="Carmona National High School">
                            </div>
                            <div class="form-group">
                                <label for="lastSchoolAddress">Address</label>
                                <input type="text" id="lastSchoolAddress" class="form-input" placeholder="Carmona, Cavite">
                            </div>
                        </div>
                    </div>

                    <!-- STEP 5: Parent/Guardian -->
                    <div class="form-step" data-step="5">
                        <h3 class="step-title">Parent/Guardian Information</h3>
                        <p class="step-description">Emergency contact details</p>

                        <div class="form-group">
                            <label for="parentGuardian">Parent/Guardian Name *</label>
                            <input type="text" id="parentGuardian" class="form-input" placeholder="Full Name" required oninput="toTitleCase(this)">
                        </div>

                        <div class="form-group">
                            <label for="parentRelationship">Relationship to Student</label>
                            <input type="text" id="parentRelationship" class="form-input" placeholder="e.g. Mother, Father, Aunt, Guardian" oninput="toTitleCase(this)">
                        </div>

                        <div class="form-group">
                            <label for="parentAddress">Address</label>
                            <label class="terms-label" style="margin-bottom: 6px;">
                                <input type="checkbox" id="sameAsStudentAddress">
                                <span>Same as student address</span>
                            </label>
                            <input type="text" id="parentAddress" class="form-input" placeholder="Maduya, Carmona, Cavite">
                        </div>

                        <div class="form-row-2">
                            <div class="form-group">
                                <label for="parentOccupation">Occupation</label>
                                <input type="text" id="parentOccupation" class="form-input" placeholder="Occupation">
                            </div>
                            <div class="form-group">
                                <label for="parentLandline">Landline No.</label>
                                <input type="tel" id="parentLandline" class="form-input" placeholder="046-XXX-XXXX">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="parentCellphone">Cellphone No.</label>
                            <input type="tel" id="parentCellphone" class="form-input" placeholder="09XX-XXX-XXXX">
                        </div>
                    </div>

                    <!-- STEP 6: Account Security -->
                    <div class="form-step" data-step="6">
                        <h3 class="step-title">Create Your Account</h3>
                        <p class="step-description">Set up your login credentials</p>

                        <div class="form-group">
                            <label for="password">Password *</label>
                            <div class="password-wrapper">
                                <input type="password" id="password" class="form-input" placeholder="Create a strong password" minlength="8" required>
                                <span class="material-symbols-outlined password-toggle" onclick="togglePassword('password')">visibility_off</span>
                            </div>
                            <small class="form-hint">At least 8 characters</small>
                        </div>

                        <div class="form-group">
                            <label for="confirmPassword">Confirm Password *</label>
                            <div class="password-wrapper">
                                <input type="password" id="confirmPassword" class="form-input" placeholder="Re-enter your password" minlength="8" required>
                                <span class="material-symbols-outlined password-toggle" onclick="togglePassword('confirmPassword')">visibility_off</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="terms-label">
                                <input type="checkbox" id="agreeTerms" required>
                                <span>I certify that all information provided is true and correct</span>
                            </label>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="form-navigation">
                        <button type="button" class="btn-prev" id="prevBtn" onclick="changeStep(-1)">
                            <span class="material-symbols-outlined">arrow_back</span>
                            Previous
                        </button>
                        <button type="button" class="btn-next" id="nextBtn" onclick="changeStep(1)">
                            Next
                            <span class="material-symbols-outlined">arrow_forward</span>
                        </button>
                    </div>
                </form>

                <div class="form-footer">
                    <p>Already have an account? <a href="/login" class="link-register">Login here</a></p>
                </div>
            </div>
        </div>
    </div>

    <div class="popup-overlay" id="messagePopup" role="dialog" aria-modal="true" aria-labelledby="popupTitle">
        <div class="popup-card">
            <div class="popup-header error" id="popupTitle">Notice</div>
            <div class="popup-body" id="popupBody"></div>
            <div class="popup-actions">
                <button type="button" class="popup-button" id="popupOkBtn">OK</button>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/system_popup.js') }}"></script>
    <script>
        let currentStep = 1;
        const totalSteps = 6;
        let popupOnClose = null;
        let isStepChanging = false;

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateStepDisplay();
            bindAddressMirrorBehavior();
            bindProgressStepNavigation();

            const popupOkBtn = document.getElementById('popupOkBtn');
            const popupOverlay = document.getElementById('messagePopup');

            if (popupOkBtn) {
                popupOkBtn.addEventListener('click', closePopup);
            }

            if (popupOverlay) {
                popupOverlay.addEventListener('click', function(event) {
                    if (event.target === popupOverlay) closePopup();
                });
            }
            
            // Transferee section toggle
            document.querySelectorAll('input[name="classification"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const transfereeSection = document.getElementById('transfereeSection');
                    if (transfereeSection) {
                        if (this.value === 'transferee' || this.value === 'cross_enrollee') {
                            transfereeSection.style.display = 'block';
                        } else {
                            transfereeSection.style.display = 'none';
                        }
                    }
                });
            });

            const studentNumberField = document.getElementById('studentNumber');
            if (studentNumberField) {
                studentNumberField.addEventListener('input', function() {
                    this.value = this.value.replace(/\D/g, '').slice(0, 9);
                });
            }
        });

        function bindProgressStepNavigation() {
            document.querySelectorAll('.progress-step').forEach((stepEl) => {
                const targetStep = Number(stepEl.dataset.step);
                if (!targetStep) return;

                stepEl.addEventListener('click', function() {
                    navigateToStep(targetStep);
                });

                stepEl.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        navigateToStep(targetStep);
                    }
                });
            });
        }

        function navigateToStep(targetStep) {
            if (isStepChanging) return;
            if (!targetStep || targetStep < 1 || targetStep > totalSteps) return;
            if (targetStep === currentStep) return;

            if (targetStep < currentStep) {
                currentStep = targetStep;
                updateStepDisplay();
                return;
            }

            for (let step = currentStep; step < targetStep; step += 1) {
                currentStep = step;
                if (!validateCurrentStep({ targetStep })) {
                    updateStepDisplay();
                    return;
                }
            }

            currentStep = targetStep;
            updateStepDisplay();
        }

        function showPopup(message, type = 'error', onClose = null) {
            if (window.AppPopup && typeof window.AppPopup.toast === 'function') {
                const toastType = type === 'success' ? 'success' : 'warn';
                const timeout = type === 'success' ? 1800 : 2200;

                window.AppPopup.toast(message, toastType, timeout).then(() => {
                    if (typeof onClose === 'function') onClose();
                });
                return;
            }

            const popupOverlay = document.getElementById('messagePopup');
            const popupTitle = document.getElementById('popupTitle');
            const popupBody = document.getElementById('popupBody');
            const popupOkBtn = document.getElementById('popupOkBtn');

            if (!popupOverlay || !popupTitle || !popupBody || !popupOkBtn) {
                window.alert(message);
                if (typeof onClose === 'function') onClose();
                return;
            }

            popupTitle.textContent = type === 'success' ? 'Success' : 'Notice';
            popupTitle.className = `popup-header ${type === 'success' ? 'success' : 'error'}`;
            popupBody.textContent = message;
            popupOverlay.classList.add('show');
            popupOnClose = typeof onClose === 'function' ? onClose : null;
            popupOkBtn.focus();
        }

        function closePopup() {
            const popupOverlay = document.getElementById('messagePopup');
            if (popupOverlay) popupOverlay.classList.remove('show');

            if (popupOnClose) {
                const callback = popupOnClose;
                popupOnClose = null;
                callback();
            }
        }

        function getStudentAddressText() {
            const parts = [
                (document.getElementById('houseNo')?.value || '').trim(),
                (document.getElementById('barangay')?.value || '').trim(),
                (document.getElementById('town')?.value || '').trim(),
                (document.getElementById('province')?.value || '').trim(),
            ].filter(Boolean);

            return parts.join(', ');
        }

        function syncParentAddressFromStudent() {
            const checkbox = document.getElementById('sameAsStudentAddress');
            const parentAddress = document.getElementById('parentAddress');
            if (!checkbox || !parentAddress || !checkbox.checked) return;

            parentAddress.value = getStudentAddressText();
        }

        function bindAddressMirrorBehavior() {
            const checkbox = document.getElementById('sameAsStudentAddress');
            const parentAddress = document.getElementById('parentAddress');
            if (!checkbox || !parentAddress) return;

            const studentAddressFieldIds = ['houseNo', 'barangay', 'town', 'province'];
            studentAddressFieldIds.forEach((id) => {
                const field = document.getElementById(id);
                if (field) {
                    field.addEventListener('input', syncParentAddressFromStudent);
                }
            });

            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    syncParentAddressFromStudent();
                    parentAddress.setAttribute('readonly', 'readonly');
                } else {
                    parentAddress.removeAttribute('readonly');
                }
            });
        }

        function validateStudentNumberFormat() {
            const studentNumber = (document.getElementById('studentNumber')?.value || '').trim();
            return /^\d{9}$/.test(studentNumber);
        }

        function validateCvSUEmail() {
            const email = (document.getElementById('email')?.value || '').trim().toLowerCase();
            return /@cvsu\.edu\.ph$/.test(email);
        }

        function getStepLabel(stepNumber) {
            const stepElement = document.querySelector(`.progress-step[data-step="${stepNumber}"] .progress-step-label`);
            return stepElement ? stepElement.textContent.trim() : `Step ${stepNumber}`;
        }

        // ── Step navigation ───────────────────────────────────────────────────
        function changeStep(direction) {
            if (isStepChanging) return;

            if (direction === 1 && !validateCurrentStep()) return;

            if (currentStep === totalSteps && direction === 1) {
                submitRegistration();
                return;
            }

            isStepChanging = true;
            const nextBtn = document.getElementById('nextBtn');
            const prevBtn = document.getElementById('prevBtn');

            if (nextBtn) nextBtn.disabled = true;
            if (prevBtn) prevBtn.disabled = true;

            setTimeout(() => {
                currentStep += direction;
                if (currentStep < 1) currentStep = 1;
                if (currentStep > totalSteps) currentStep = totalSteps;

                updateStepDisplay();
                isStepChanging = false;

                if (nextBtn) nextBtn.disabled = false;
                if (prevBtn) prevBtn.disabled = false;
            }, 180);
        }

        // ── Validation ────────────────────────────────────────────────────────
        function validateCurrentStep(options = {}) {
            const targetStep = Number(options.targetStep || 0);
            const navigatingForwardByStepper = targetStep > currentStep;
            const currentStepElement = document.querySelector(`.form-step[data-step="${currentStep}"]`);
            if (!currentStepElement) {
                showPopup('An error occurred. Please refresh the page.');
                return false;
            }
            
            const requiredFields = currentStepElement.querySelectorAll('[required]');
            let isValid = true;
            let missingFields = [];
            
            requiredFields.forEach(field => {
                let fieldValid = false;
                
                if (field.type === 'radio') {
                    const radioName = field.getAttribute('name');
                    fieldValid = document.querySelector(`input[name="${radioName}"]:checked`) !== null;
                    if (!fieldValid && !missingFields.includes(radioName)) missingFields.push(radioName);
                } else if (field.type === 'checkbox') {
                    fieldValid = field.checked;
                    if (!fieldValid) {
                        field.style.outline = '2px solid #dc3545';
                        missingFields.push(field.id || 'checkbox');
                    } else {
                        field.style.outline = '';
                    }
                } else {
                    fieldValid = field.value && field.value.trim() !== '';
                    if (!fieldValid) {
                        field.style.borderColor = '#dc3545';
                        missingFields.push(field.id || field.name || 'field');
                    } else {
                        field.style.borderColor = '';
                    }
                }
                
                if (!fieldValid) isValid = false;
            });

            if (isValid && currentStep === 2 && !validateCvSUEmail()) {
                const emailField = document.getElementById('email');
                if (emailField) emailField.style.borderColor = '#dc3545';
                if (navigatingForwardByStepper) {
                    showPopup(`Please complete ${getStepLabel(currentStep)} first. Use your official CvSU email before proceeding to ${getStepLabel(targetStep)}.`);
                } else {
                    showPopup('Please input a CvSU email.');
                }
                return false;
            }

            if (isValid && currentStep === 3 && !validateStudentNumberFormat()) {
                const studentNumberField = document.getElementById('studentNumber');
                if (studentNumberField) studentNumberField.style.borderColor = '#dc3545';
                if (navigatingForwardByStepper) {
                    showPopup(`Please complete ${getStepLabel(currentStep)} first. Student number must contain exactly 9 digits before proceeding to ${getStepLabel(targetStep)}.`);
                } else {
                    showPopup('Student number must contain exactly 9 digits.');
                }
                return false;
            }
            
            if (!isValid) {
                const hasAgreementError = missingFields.includes('agreeTerms');
                if (hasAgreementError) {
                    showPopup('Please check the certification box before submitting your registration.');
                } else if (navigatingForwardByStepper) {
                    showPopup(`Please complete ${getStepLabel(currentStep)} first before proceeding to ${getStepLabel(targetStep)}.`);
                } else {
                    showPopup('Please fill in all required fields.');
                }
            }
            return isValid;
        }

        // ── Submit ────────────────────────────────────────────────────────────
        let isSubmitting = false;

        function submitRegistration() {
            if (isSubmitting) return;

            const submitBtn = document.getElementById('nextBtn');
            if (!submitBtn) { showPopup('Submit button not found. Please refresh the page.'); return; }

            if (!validateCurrentStep()) return;

            const passwordField        = document.getElementById('password');
            const confirmPasswordField = document.getElementById('confirmPassword');
            if (!passwordField || !confirmPasswordField) {
                showPopup('Password fields not found. Please refresh the page.');
                return;
            }

            const password        = passwordField.value;
            const confirmPassword = confirmPasswordField.value;
            if (password !== confirmPassword) { showPopup('Passwords do not match.'); return; }

            if (!validateCvSUEmail()) {
                showPopup('Please input a CvSU email.');
                currentStep = 2;
                updateStepDisplay();
                return;
            }

            if (!validateStudentNumberFormat()) {
                showPopup('Student number must contain exactly 9 digits.');
                currentStep = 3;
                updateStepDisplay();
                return;
            }

            const classificationElement = document.querySelector('input[name="classification"]:checked');
            const regStatusElement      = document.querySelector('input[name="regStatus"]:checked');

            if (!classificationElement) {
                showPopup('Please select student classification.');
                currentStep = 3; updateStepDisplay(); return;
            }
            if (!regStatusElement) {
                showPopup('Please select registration status.');
                currentStep = 3; updateStepDisplay(); return;
            }

            isSubmitting = true;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="material-symbols-outlined">hourglass_empty</span> Submitting...';

            function getValue(id) {
                const el = document.getElementById(id);
                return el ? (el.value || '').trim() : '';
            }

            // Collect special category from dropdown
            const specialCategory = getValue('special_category');

            const formData = {
                // Personal
                lastName:      getValue('lastName'),
                firstName:     getValue('firstName'),
                middleName:    getValue('middleName'),
                dateOfBirth:   getValue('dateOfBirth'),
                placeOfBirth:  getValue('placeOfBirth'),
                age:           parseInt(getValue('age')) || 0,
                sex:           getValue('sex'),
                religion:      getValue('religion'),
                civilStatus:   getValue('civilStatus'),
                nationality:   getValue('nationality'),

                // Address
                houseNo:       getValue('houseNo'),
                barangay:      getValue('barangay'),
                town:          getValue('town'),
                province:      getValue('province'),
                landlineNo:    getValue('landlineNo'),
                cellphoneNo:   getValue('cellphoneNo'),
                email:         getValue('email'),

                // Academic
                studentNumber:         getValue('studentNumber'),
                yearLevel:             parseInt(getValue('yearLevel')) || null,
                course:                getValue('course'),
                section:               getValue('section'),
                studentClassification: classificationElement.value,
                shifteeFrom:           getValue('shifteeFrom'),
                regStatus:             regStatusElement.value,

                // Special Category (dropdown)
                special_category: specialCategory,

                // Education
                elemSchool:          getValue('elemSchool'),
                elemYearGrad:        getValue('elemYearGrad'),
                elemType:            getValue('elemType'),
                elemAddress:         getValue('elemAddress'),
                hsSchool:            getValue('hsSchool'),
                hsYearGrad:          getValue('hsYearGrad'),
                hsType:              getValue('hsType'),
                hsAddress:           getValue('hsAddress'),
                shsSchool:           getValue('shsSchool'),
                shsYearGrad:         getValue('shsYearGrad'),
                shsType:             getValue('shsType'),
                shsAddress:          getValue('shsAddress'),
                lastSchoolAttended:  getValue('lastSchoolAttended'),
                lastSchoolAddress:   getValue('lastSchoolAddress'),

                // Guardian
                parentGuardian:   getValue('parentGuardian'),
                parentRelationship: getValue('parentRelationship'),
                parentAddress:    getValue('parentAddress'),
                parentOccupation: getValue('parentOccupation'),
                parentLandline:   getValue('parentLandline'),
                parentCellphone:  getValue('parentCellphone'),

                // Password
                password: password
            };

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Save registration data for resume use
                    const regData = {
                        shsSchool:        getValue('shsSchool'),
                        shsYearGrad:      getValue('shsYearGrad'),
                        shsType:          getValue('shsType'),
                        hsSchool:         getValue('hsSchool'),
                        hsYearGrad:       getValue('hsYearGrad'),
                        hsType:           getValue('hsType'),
                        elemSchool:       getValue('elemSchool'),
                        elemYearGrad:     getValue('elemYearGrad'),
                        elemType:         getValue('elemType'),
                        parentGuardian:   getValue('parentGuardian'),
                        parentRelationship: getValue('parentRelationship'),
                        parentCellphone:  getValue('parentCellphone'),
                        parentAddress:    getValue('parentAddress'),
                        parentOccupation: getValue('parentOccupation'),
                        // Personal details for resume
                        houseNo:          getValue('houseNo'),
                        street:           getValue('street'),
                        barangay:         getValue('barangay'),
                        town:             getValue('town'),
                        province:         getValue('province'),
                        zipCode:          getValue('zipCode'),
                        dateOfBirth:      getValue('dateOfBirth'),
                        age:              getValue('age'),
                        birthPlace:       getValue('placeOfBirth'),
                    };
                    sessionStorage.setItem('registrationData', JSON.stringify(regData));
                    showPopup(data.message || 'Registration successful! Redirecting to login...', 'success', function() {
                        window.location.href = '/login';
                    });
                } else {
                    let errorMessage = data.message || 'Registration failed. Please try again.';

                    if (data.errors && typeof data.errors === 'object') {
                        const validationMessages = Object.values(data.errors)
                            .flat()
                            .filter(Boolean);
                        if (validationMessages.length > 0) {
                            errorMessage += '\n\n' + validationMessages.join('\n');
                        }
                    }

                    showPopup(errorMessage);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<span class="material-symbols-outlined">how_to_reg</span> Submit Registration';
                    isSubmitting = false;
                }
            })
            .catch(err => {
                console.error('Registration error:', err);
                showPopup('An error occurred during registration. Please try again.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span class="material-symbols-outlined">how_to_reg</span> Submit Registration';
                isSubmitting = false;
            });
        }

        // ── UI helpers ────────────────────────────────────────────────────────
        function updateStepDisplay() {
            document.querySelectorAll('.form-step').forEach(s => s.classList.remove('active'));
            const el = document.querySelector(`.form-step[data-step="${currentStep}"]`);
            if (el) el.classList.add('active');

            document.querySelectorAll('.progress-step').forEach((step, i) => {
                step.classList.remove('active', 'completed');
                if (i + 1 < currentStep)        step.classList.add('completed');
                else if (i + 1 === currentStep)  step.classList.add('active');

                const isLocked = i + 1 > currentStep;
                step.classList.toggle('locked', isLocked);
                step.setAttribute('tabindex', '0');
                step.setAttribute('role', 'button');
                step.setAttribute('aria-disabled', isLocked ? 'true' : 'false');
            });

            const pct = ((currentStep - 1) / (totalSteps - 1)) * 100;
            const line = document.getElementById('progressLine');
            if (line) line.style.width = pct + '%';

            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            if (prevBtn) prevBtn.style.display = currentStep === 1 ? 'none' : 'flex';
            if (nextBtn) {
                nextBtn.innerHTML = currentStep === totalSteps
                    ? '<span class="material-symbols-outlined">how_to_reg</span> Submit Registration'
                    : 'Next <span class="material-symbols-outlined">arrow_forward</span>';
            }
        }

        function previewPhoto(event) {
            const preview = document.getElementById('photoPreview');
            if (!preview) return;
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => { preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`; };
                reader.readAsDataURL(file);
            }
        }

        // Auto-calculate age from date of birth
        function calculateAge(dob) {
            if (!dob) return;
            const today = new Date();
            const birthDate = new Date(dob);
            let age = today.getFullYear() - birthDate.getFullYear();
            const m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) age--;
            document.getElementById('age').value = age >= 0 ? age : '';
        }

        // Title case — capitalize first letter of each word
        function toTitleCase(input) {
            const pos = input.selectionStart;
            input.value = input.value.replace(/\b\w/g, c => c.toUpperCase());
            input.setSelectionRange(pos, pos);
        }

        // Zip code lookup by town/city name (Philippines)
        const zipCodes = {
            'Manila': '1000', 'Quezon City': '1100', 'Caloocan': '1400',
            'Las Pinas': '1740', 'Makati': '1200', 'Malabon': '1470',
            'Mandaluyong': '1550', 'Marikina': '1800', 'Muntinlupa': '1770',
            'Navotas': '1485', 'Paranaque': '1700', 'Pasay': '1300',
            'Pasig': '1600', 'Pateros': '1620', 'San Juan': '1500',
            'Taguig': '1630', 'Valenzuela': '1440', 'Bacoor': '4102',
            'Carmona': '4116', 'Cavite City': '4100', 'Dasmariñas': '4114',
            'Dasmarinas': '4114', 'General Trias': '4107', 'Imus': '4103',
            'Silang': '4118', 'Tagaytay': '4120', 'Trece Martires': '4109',
            'Alfonso': '4123', 'Amadeo': '4119', 'Gen. Mariano Alvarez': '4117',
            'Indang': '4122', 'Kawit': '4104', 'Magallanes': '4113',
            'Maragondon': '4112', 'Mendez': '4121', 'Naic': '4110',
            'Noveleta': '4105', 'Rosario': '4106', 'Tanza': '4108',
            'Ternate': '4111', 'Calamba': '4027', 'San Pablo': '4000',
            'Santa Rosa': '4026', 'Biñan': '4024', 'Cabuyao': '4025',
            'San Pedro': '4023', 'Antipolo': '1870', 'Cainta': '1900',
            'Taytay': '1920', 'Angono': '1930', 'Baras': '1970',
            'Binangonan': '1940', 'Cardona': '1950', 'Jala-jala': '1990',
            'Morong': '1960', 'Pililla': '1910', 'Rodriguez': '1860',
            'San Mateo': '1850', 'Tanay': '1980', 'Teresa': '1880',
            'Cebu City': '6000', 'Davao City': '8000', 'Zamboanga': '7000',
            'Iloilo City': '5000', 'Bacolod': '6100', 'Cagayan de Oro': '9000',
            'General Santos': '9500', 'Baguio': '2600', 'Batangas City': '4200',
            'Lipa': '4217', 'Tanauan': '4232', 'Lucena': '4301',
            'Olongapo': '2200', 'Angeles': '2009', 'San Fernando': '2000',
        };

        function handleTownInput(input) {
            toTitleCase(input);
            const town = input.value.trim();
            const zip = zipCodes[town] || '';
            const zipField = document.getElementById('zipCode');
            if (zipField) {
                zipField.value = zip;
                zipField.placeholder = zip ? 'Auto-filled' : 'Not found — enter manually';
                if (!zip) {
                    zipField.removeAttribute('readonly');
                    zipField.style.background = '';
                    zipField.style.cursor = '';
                } else {
                    zipField.setAttribute('readonly', 'readonly');
                    zipField.style.background = '#f4f6f4';
                    zipField.style.cursor = 'not-allowed';
                }
            }
        }

        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            if (!field) return;
            const toggle = field.nextElementSibling;
            if (!toggle) return;
            if (field.type === 'password') { field.type = 'text';     toggle.textContent = 'visibility'; }
            else                           { field.type = 'password'; toggle.textContent = 'visibility_off'; }
        }
    </script>
</body>
</html>