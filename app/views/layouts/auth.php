<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'><circle cx='32' cy='32' r='32' fill='%23FF6B35'/><text x='32' y='46' text-anchor='middle' font-size='40' font-family='serif' fill='white'>ॐ</text></svg>">
    <title><?= htmlspecialchars($title ?? 'Sanskar AI') ?></title>
    <meta name="description" content="Sanskar AI - Your guide to Hindu rituals and traditions">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Dark Theme (Default) */
        :root {
            --primary: #FF6B35;
            --primary-dark: #E55A2B;
            --secondary: #6B5CE7;
            --accent: #F7B801;
            --gold: #D4AF37;
            --saffron: #FF9933;
            --maroon: #800000;
            --dark: #1A1A2E;
            --light: #F8F9FA;
            --success: #10B981;
            --danger: #EF4444;
            
            /* Theme Variables - Dark Default */
            --bg-primary: linear-gradient(135deg, #1A1A2E 0%, #16213E 50%, #0F3460 100%);
            --bg-secondary: #232332;
            --bg-card: rgba(255, 255, 255, 0.05);
            --bg-card-hover: rgba(255, 255, 255, 0.1);
            --text-primary: #FFFFFF;
            --text-secondary: rgba(255, 255, 255, 0.7);
            --text-muted: rgba(255, 255, 255, 0.5);
            --border-color: rgba(255, 255, 255, 0.1);
            --border-accent: rgba(255, 153, 51, 0.3);
            --shadow-color: rgba(0, 0, 0, 0.3);
            --nav-bg: rgba(40, 40, 55, 0.95);
            --input-bg: rgba(255,255,255,0.08);
            --input-border: rgba(255,255,255,0.15);
            --input-focus-bg: rgba(255,255,255,0.12);
            --hero-title-gradient: linear-gradient(135deg, #FFFFFF 0%, #FFFFFF 30%, var(--saffron) 60%, var(--gold) 100%);
        }
        
        /* Light Theme */
        [data-theme="light"] {
            --bg-primary: linear-gradient(135deg, #FFF8F0 0%, #FFF5E6 50%, #FFFAF5 100%);
            --bg-secondary: rgba(255, 255, 255, 0.98);
            --bg-card: rgba(255, 153, 51, 0.08);
            --bg-card-hover: rgba(255, 153, 51, 0.15);
            --text-primary: #1A1A2E;
            --text-secondary: rgba(26, 26, 46, 0.85);
            --text-muted: rgba(26, 26, 46, 0.6);
            --border-color: rgba(26, 26, 46, 0.1);
            --border-accent: rgba(255, 107, 53, 0.4);
            --shadow-color: rgba(0, 0, 0, 0.1);
            --nav-bg: rgba(255, 255, 255, 0.95);
            --input-bg: #FFFFFF;
            --input-border: rgba(26, 26, 46, 0.15);
            --input-focus-bg: #FFFFFF;
            --hero-title-gradient: linear-gradient(135deg, #1A1A2E 0%, #1A1A2E 30%, var(--primary) 60%, var(--saffron) 100%);
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; overflow-x: hidden; }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            width: 100%;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            transition: background 0.4s ease, color 0.4s ease;
        }
        
        /* Animated Background */
        .bg-animation {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: -1;
            overflow: hidden;
        }
        .bg-animation::before {
            content: '';
            position: absolute;
            top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: radial-gradient(circle at 20% 50%, rgba(255,107,53,0.1) 0%, transparent 50%),
                        radial-gradient(circle at 80% 20%, rgba(107,92,231,0.1) 0%, transparent 50%),
                        radial-gradient(circle at 40% 80%, rgba(247,184,1,0.05) 0%, transparent 50%);
            animation: bgFloat 20s ease-in-out infinite;
        }
        @keyframes bgFloat {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(2%, 2%) rotate(1deg); }
            50% { transform: translate(0, 4%) rotate(0deg); }
            75% { transform: translate(-2%, 2%) rotate(-1deg); }
        }
        
        /* Navigation */
        .nav-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 20px 5%;
            transition: all 0.3s ease;
        }
        nav {
            max-width: 1100px;
            margin: 0 auto;
            padding: 12px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--nav-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-accent);
            border-radius: 60px;
            box-shadow: 0 8px 32px var(--shadow-color);
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-primary);
            text-decoration: none;
        }
        .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--saffron) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logo-icon i { color: white; font-size: 1.2rem; }
        
        /* Theme Toggle */
        .theme-toggle {
            background: var(--bg-card);
            border: 2px solid var(--border-accent);
            color: var(--saffron);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 5px;
        }
        .theme-toggle:hover {
            background: var(--saffron);
            color: var(--dark);
            transform: rotate(180deg);
        }
        .theme-toggle .fa-sun { display: none; }
        .theme-toggle .fa-moon { display: block; }
        [data-theme="light"] .theme-toggle .fa-sun { display: block; }
        [data-theme="light"] .theme-toggle .fa-moon { display: none; }
        .nav-right {
            display: flex;
            gap: 12px;
            align-items: center;
        }
        .nav-login {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            padding: 10px 18px;
            border-radius: 30px;
            transition: all 0.3s ease;
        }
        .nav-login:hover {
            color: var(--saffron);
            background: var(--bg-card-hover);
        }
        .nav-cta {
            padding: 10px 22px;
            background: transparent;
            color: var(--saffron);
            border: 2px solid var(--saffron);
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .nav-cta:hover {
            background: var(--saffron);
            color: #1A1A2E;
            box-shadow: 0 0 20px rgba(255, 153, 51, 0.4);
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--saffron) 100%);
            color: white;
            box-shadow: 0 10px 30px rgba(255,107,53,0.3);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(255,107,53,0.4);
        }
        .btn-outline {
            background: transparent;
            color: var(--text-primary);
            border: 2px solid var(--border-color);
        }
        .btn-outline:hover {
            border-color: var(--saffron);
            color: var(--saffron);
        }
        
        /* Main Content */
        main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 120px 5% 80px;
        }
        
        /* Auth Card */
        .auth-container {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid var(--border-color);
            box-shadow: 0 25px 50px var(--shadow-color);
            width: 100%;
            max-width: 480px;
            padding: 50px 40px;
            animation: slideUp 0.6s ease-out;
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 35px;
        }
        
        .auth-header .auth-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--saffron) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
        }
        
        .auth-header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 10px;
            background: var(--hero-title-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .auth-header p {
            color: var(--text-secondary);
            font-size: 1rem;
        }
        
        .form-group {
            margin-bottom: 22px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 500;
            color: var(--text-primary);
            font-size: 0.95rem;
        }
        
        .form-control {
            width: 100%;
            padding: 16px 18px;
            background: var(--input-bg);
            border: 2px solid var(--input-border);
            border-radius: 14px;
            font-size: 1rem;
            color: var(--text-primary);
            transition: all 0.3s ease;
            font-family: inherit;
        }
        
        .form-control::placeholder {
            color: var(--text-muted);
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--saffron);
            background: var(--input-focus-bg);
            box-shadow: 0 0 0 4px rgba(255,153,51,0.15);
        }
        
        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }
        
        .btn {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 14px;
            font-size: 1.05rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--saffron) 100%);
            color: white;
            box-shadow: 0 10px 30px rgba(255,107,53,0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(255,107,53,0.4);
        }
        
        .alert {
            padding: 16px 18px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 0.95rem;
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.2);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        
        .alert-error {
            background: rgba(239, 68, 68, 0.2);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        
        .alert ul {
            margin-top: 10px;
            padding-left: 20px;
        }
        
        .auth-links {
            text-align: center;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid var(--border-color);
            color: var(--text-secondary);
        }
        
        .auth-links a {
            color: var(--saffron);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .auth-links a:hover {
            color: var(--primary);
            text-decoration: underline;
        }
        
        .role-selector {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .role-option {
            flex: 1;
            padding: 20px 15px;
            background: var(--bg-card);
            border: 2px solid var(--border-color);
            border-radius: 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .role-option:hover {
            border-color: rgba(255,153,51,0.5);
            background: rgba(255,153,51,0.1);
        }
        
        .role-option.active {
            border-color: var(--saffron);
            background: rgba(255,153,51,0.15);
        }
        
        .role-option input {
            display: none;
        }
        
        .role-option i {
            font-size: 1.8rem;
            margin-bottom: 10px;
            color: var(--saffron);
            display: block;
        }
        
        .role-option span {
            display: block;
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .pandit-fields {
            display: none;
            animation: fadeIn 0.3s ease;
        }
        
        .pandit-fields.show {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* Footer */
        footer {
            padding: 30px 5%;
            border-top: 1px solid var(--border-color);
            text-align: center;
        }
        
        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .footer-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-primary);
            text-decoration: none;
            font-weight: 600;
        }
        
        .footer-brand i {
            color: var(--saffron);
            font-size: 1.3rem;
        }
        
        .footer-links {
            display: flex;
            gap: 25px;
        }
        
        .footer-links a {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s;
        }
        
        .footer-links a:hover {
            color: var(--saffron);
        }
        
        .social-links {
            display: flex;
            gap: 15px;
        }
        
        .social-links a {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-card);
            border-radius: 50%;
            color: var(--text-primary);
            transition: all 0.3s;
        }
        
        .social-links a:hover {
            background: var(--saffron);
            transform: translateY(-3px);
        }
        
        /* Mobile Menu Button */
        .mobile-menu-btn {
            display: none;
            background: transparent;
            border: none;
            color: var(--text-secondary);
            font-size: 1.4rem;
            cursor: pointer;
            padding: 8px;
            z-index: 10001;
            position: relative;
            transition: color 0.3s ease;
        }
        .mobile-menu-btn:hover {
            color: var(--saffron);
        }
        
        /* Mobile Menu Overlay Backdrop */
        .auth-mobile-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 9997;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .auth-mobile-overlay.active {
            display: block;
            opacity: 1;
        }
        
        /* Mobile Menu - Compact slide-in drawer */
        .auth-mobile-menu {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: 280px;
            max-width: 85vw;
            height: 100vh;
            background: var(--nav-bg);
            backdrop-filter: blur(30px);
            border-left: 1px solid var(--border-accent);
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0;
            z-index: 9998;
            transform: translateX(100%);
            opacity: 0;
            visibility: hidden;
            transition: transform 0.3s ease, opacity 0.3s ease, visibility 0.3s ease;
            padding: 70px 0 30px;
            overflow-y: auto;
            box-shadow: -10px 0 40px rgba(0,0,0,0.3);
        }
        .auth-mobile-menu.active {
            transform: translateX(0);
            opacity: 1;
            visibility: visible;
        }
        
        /* Top bar inside drawer */
        .auth-mobile-menu .mobile-menu-top {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-color);
        }
        .auth-mobile-menu .mobile-menu-close {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 153, 51, 0.1);
            border: 1.5px solid var(--border-accent);
            border-radius: 50%;
            color: var(--saffron);
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .auth-mobile-menu .mobile-menu-close:hover {
            background: var(--saffron);
            color: #1A1A2E;
        }
        .auth-mobile-menu .mobile-theme-toggle {
            width: 36px;
            height: 36px;
            font-size: 0.85rem;
            background: rgba(255, 153, 51, 0.1);
            border-color: var(--border-accent);
        }
        
        /* Nav links */
        .auth-mobile-menu a {
            display: block;
            width: 100%;
            color: var(--text-primary);
            text-decoration: none;
            font-size: 1rem;
            font-weight: 500;
            padding: 14px 28px;
            border-radius: 0;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }
        .auth-mobile-menu a:hover {
            color: var(--saffron);
            background: rgba(255, 153, 51, 0.08);
            border-left-color: var(--saffron);
        }
        
        /* CTA button in drawer */
        .auth-mobile-menu .mobile-cta {
            margin: 15px 20px 0;
            padding: 12px 24px;
            background: transparent;
            color: var(--saffron);
            border: 2px solid var(--saffron);
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 600;
            text-align: center;
            width: calc(100% - 40px);
        }
        .auth-mobile-menu .mobile-cta:hover {
            background: var(--saffron);
            color: #1A1A2E;
            border-left-color: var(--saffron);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .mobile-menu-btn { display: flex; align-items: center; }
            .nav-right .nav-login,
            .nav-right .nav-cta { display: none; }
            
            .nav-wrapper { padding: 12px 4%; }
            nav {
                padding: 10px 18px;
                border-radius: 50px;
            }
            .logo { font-size: 1.1rem; gap: 8px; }
            .logo-icon { width: 30px; height: 30px; }
            .logo-icon i { font-size: 1.1rem; }
            .theme-toggle { width: 36px; height: 36px; font-size: 0.9rem; }
            
            main { padding: 100px 16px 40px; }
            
            .auth-container {
                padding: 30px 22px;
                border-radius: 20px;
                max-width: 100%;
            }
            
            .auth-header .auth-icon {
                width: 65px;
                height: 65px;
                font-size: 1.6rem;
            }
            
            .auth-header h1 { font-size: 1.5rem; }
            .auth-header p { font-size: 0.9rem; }
            .auth-header { margin-bottom: 25px; }
            
            .form-group { margin-bottom: 16px; }
            .form-group label { font-size: 0.88rem; margin-bottom: 8px; }
            .form-control { padding: 13px 15px; font-size: 0.95rem; border-radius: 12px; }
            
            .btn { padding: 14px; font-size: 1rem; border-radius: 12px; }
            
            .role-selector { gap: 10px; }
            .role-option { padding: 15px 10px; border-radius: 14px; }
            .role-option i { font-size: 1.5rem; margin-bottom: 8px; }
            .role-option span { font-size: 0.9rem; }
            
            .auth-links { margin-top: 22px; padding-top: 18px; font-size: 0.9rem; }
            
            footer { padding: 25px 16px; }
            .footer-content {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            .footer-links {
                flex-wrap: wrap;
                justify-content: center;
                gap: 12px;
            }
            .social-links { justify-content: center; }
        }
        
        @media (max-width: 480px) {
            main { padding: 90px 12px 30px; }
            
            .auth-container {
                padding: 25px 18px;
                border-radius: 18px;
            }
            
            .auth-header .auth-icon {
                width: 60px;
                height: 60px;
                font-size: 1.4rem;
            }
            .auth-header h1 { font-size: 1.35rem; }
            .auth-header p { font-size: 0.85rem; }
            .auth-header { margin-bottom: 20px; }
            
            .form-group { margin-bottom: 14px; }
            .form-group label { font-size: 0.85rem; margin-bottom: 6px; }
            .form-control { padding: 12px 14px; font-size: 0.9rem; }
            
            .btn { padding: 13px; font-size: 0.95rem; }
            
            .role-selector { flex-direction: column; gap: 10px; }
            .role-option {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 12px;
                padding: 14px;
                flex-direction: row;
            }
            .role-option i { margin-bottom: 0; font-size: 1.3rem; }
            .role-option span { font-size: 0.9rem; }
            
            .footer-links { gap: 10px; }
            .footer-links a { font-size: 0.8rem; }
        }
        
        @media (max-width: 360px) {
            main { padding: 85px 10px 25px; }
            .auth-container { padding: 22px 15px; }
            .auth-header h1 { font-size: 1.2rem; }
            .form-control { padding: 11px 12px; font-size: 0.88rem; }
            .btn { padding: 12px; font-size: 0.9rem; }
        }
    </style>
</head>
<body>
    <div class="bg-animation"></div>
    
    <div class="nav-wrapper">
        <nav>
            <a href="/" class="logo">
                <div class="logo-icon">
                    <i class="fas fa-om"></i>
                </div>
                Sanskar AI
            </a>
            <div class="nav-right">
                <button id="themeToggle" class="theme-toggle" aria-label="Toggle theme">
                    <i class="fas fa-sun"></i>
                    <i class="fas fa-moon"></i>
                </button>
                <?php if (strpos($title ?? '', 'Login') !== false): ?>
                <a href="/" class="nav-login">Home</a>
                <a href="/signup" class="nav-cta">Sign Up</a>
                <?php else: ?>
                <a href="/" class="nav-login">Home</a>
                <a href="/login" class="nav-cta">Login</a>
                <?php endif; ?>
                <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Open menu">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </nav>
    </div>
    
    <!-- Mobile Menu Overlay Backdrop -->
    <div class="auth-mobile-overlay" id="authMobileOverlay"></div>
    
    <!-- Mobile Menu Drawer -->
    <div class="auth-mobile-menu" id="authMobileMenu">
        <!-- Top bar with toggle & close -->
        <div class="mobile-menu-top">
            <button class="theme-toggle mobile-theme-toggle" id="mobileThemeToggle" aria-label="Toggle theme">
                <i class="fas fa-sun"></i>
                <i class="fas fa-moon"></i>
            </button>
            <button class="mobile-menu-close" id="mobileMenuClose" aria-label="Close menu">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <a href="/">Home</a>
        <a href="/#features">Features</a>
        <a href="/#how-it-works">How It Works</a>
        <a href="/#faq">FAQ</a>
        <?php if (strpos($title ?? '', 'Login') !== false): ?>
        <a href="/signup" class="mobile-cta">Sign Up</a>
        <?php else: ?>
        <a href="/login" class="mobile-cta">Login</a>
        <?php endif; ?>
    </div>
    
    <main>
        <div class="auth-container">
            <?= $content ?>
        </div>
    </main>
    
    <footer>
        <div class="footer-content">
            <a href="/" class="footer-brand">
                <i class="fas fa-om"></i>
                Sanskar AI
            </a>
            <div class="footer-links">
                <a href="/">Home</a>
                <a href="/#features">Features</a>
                <a href="/#faq">FAQ</a>
                <a href="/#about">Contact</a>
            </div>
            <div class="social-links">
                <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </footer>
    
    <script>
        // Theme Toggle Logic
        document.addEventListener('DOMContentLoaded', () => {
             const themeToggle = document.getElementById('themeToggle');
             const mobileThemeToggle = document.getElementById('mobileThemeToggle');
             const html = document.documentElement;
             const savedTheme = localStorage.getItem('theme') || 'dark';
             
             html.setAttribute('data-theme', savedTheme);
             
             function toggleTheme() {
                 const currentTheme = html.getAttribute('data-theme');
                 const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                 html.setAttribute('data-theme', newTheme);
                 localStorage.setItem('theme', newTheme);
             }
             
             if(themeToggle) themeToggle.addEventListener('click', toggleTheme);
             if(mobileThemeToggle) mobileThemeToggle.addEventListener('click', toggleTheme);
             
             // Mobile Menu
             const mobileMenuBtn = document.getElementById('mobileMenuBtn');
             const mobileMenuClose = document.getElementById('mobileMenuClose');
             const mobileMenu = document.getElementById('authMobileMenu');
             const mobileOverlay = document.getElementById('authMobileOverlay');
             
             function openAuthMenu() {
                 if (mobileMenu) mobileMenu.classList.add('active');
                 if (mobileOverlay) mobileOverlay.classList.add('active');
                 document.body.style.overflow = 'hidden';
             }
             function closeAuthMenu() {
                 if (mobileMenu) mobileMenu.classList.remove('active');
                 if (mobileOverlay) mobileOverlay.classList.remove('active');
                 document.body.style.overflow = '';
             }
             
             if (mobileMenuBtn) mobileMenuBtn.addEventListener('click', openAuthMenu);
             if (mobileMenuClose) mobileMenuClose.addEventListener('click', closeAuthMenu);
             if (mobileOverlay) mobileOverlay.addEventListener('click', closeAuthMenu);
             
             // Close menu when clicking a link
             if (mobileMenu) {
                 mobileMenu.querySelectorAll('a').forEach(link => {
                     link.addEventListener('click', closeAuthMenu);
                 });
             }
        });

        // Role selector toggle
        document.querySelectorAll('.role-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.role-option').forEach(o => o.classList.remove('active'));
                this.classList.add('active');
                this.querySelector('input').checked = true;
                
                const panditFields = document.querySelector('.pandit-fields');
                if (panditFields) {
                    if (this.querySelector('input').value === 'pandit') {
                        panditFields.classList.add('show');
                    } else {
                        panditFields.classList.remove('show');
                    }
                }
            });
        });
    </script>
</body>
</html>
