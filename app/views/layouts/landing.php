<?php
use App\Core\Auth;

// Get current user info if logged in
$isLoggedIn = Auth::check();
$currentUser = Auth::user();
$userRole = Auth::role();
$dashboardUrl = Auth::dashboardUrl();

$hasGivenFeedback = false;
if ($isLoggedIn && $userRole === 'user') {
    $feedbackModel = new \App\Models\UserFeedback();
    $hasGivenFeedback = (bool) $feedbackModel->whereFirst(['user_id' => Auth::id()]);
}

// Settings URL based on role
$settingsUrl = match ($userRole) {
    'admin' => '/admin/profile', // Fixed: Point to actual profile
    'pandit' => '/pandit/profile',
    'user' => '/user/profile',   // Fixed: Point to profile instead of families
    default => '/'
};

// Role display name and colors
$roleDisplay = match ($userRole) {
    'admin' => ['name' => 'Admin', 'color' => '#D4AF37'],
    'pandit' => ['name' => 'Pandit', 'color' => '#6B5CE7'],
    'user' => ['name' => 'User', 'color' => '#FF9933'],
    default => ['name' => '', 'color' => '#FF9933']
};
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'><circle cx='32' cy='32' r='32' fill='%23FF6B35'/><text x='32' y='46' text-anchor='middle' font-size='40' font-family='serif' fill='white'>ॐ</text></svg>">
    <title><?= htmlspecialchars($title ?? 'Sanskar AI - Your Guide to Hindu Rituals') ?></title>
    <meta name="description"
        content="Discover the sacred world of Hindu rituals with AI-powered guidance. Connect with verified Pandits, learn traditional ceremonies, and preserve your family's spiritual heritage.">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
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

            /* Theme-specific variables */
            --bg-primary: linear-gradient(135deg, #1A1A2E 0%, #16213E 50%, #0F3460 100%);
            --bg-secondary: rgba(40, 40, 55, 0.95);
            --bg-card: rgba(255, 255, 255, 0.05);
            --bg-card-hover: rgba(255, 255, 255, 0.1);
            --text-primary: #FFFFFF;
            --text-secondary: rgba(255, 255, 255, 0.8);
            --text-muted: rgba(255, 255, 255, 0.6);
            --border-color: rgba(255, 255, 255, 0.1);
            --border-accent: rgba(255, 153, 51, 0.3);
            --shadow-color: rgba(0, 0, 0, 0.3);
            --nav-bg: rgba(40, 40, 55, 0.95);
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
            --hero-title-gradient: linear-gradient(135deg, #1A1A2E 0%, #1A1A2E 30%, var(--primary) 60%, var(--saffron) 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
            overflow-x: hidden;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
            transition: background 0.4s ease, color 0.4s ease;
        }

        /* Animated Background */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .bg-animation::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 20% 50%, rgba(255, 107, 53, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(107, 92, 231, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 80%, rgba(247, 184, 1, 0.05) 0%, transparent 50%);
            animation: bgFloat 20s ease-in-out infinite;
        }

        @keyframes bgFloat {

            0%,
            100% {
                transform: translate(0, 0) rotate(0deg);
            }

            25% {
                transform: translate(2%, 2%) rotate(1deg);
            }

            50% {
                transform: translate(0, 4%) rotate(0deg);
            }

            75% {
                transform: translate(-2%, 2%) rotate(-1deg);
            }
        }

        /* Scroll Animations */
        [data-animate] {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        [data-animate="fade-left"] {
            transform: translateX(50px);
        }

        [data-animate].animated {
            opacity: 1;
            transform: translate(0, 0);
        }

        /* Navigation - Pill Style */
        .nav-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 15px 5%;
            display: flex;
            justify-content: center;
            transition: all 0.3s ease;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 1200px;
            padding: 12px 25px;
            background: var(--nav-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-accent);
            border-radius: 60px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 30px var(--shadow-color);
        }

        nav.scrolled {
            background: var(--nav-bg);
            box-shadow: 0 8px 40px var(--shadow-color);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
            text-decoration: none;
            flex-shrink: 0;
        }

        /* Theme Toggle Button */
        .theme-toggle {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--bg-card);
            border: 2px solid var(--border-accent);
            color: var(--saffron);
            font-size: 1.1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            margin-right: 10px;
        }

        .theme-toggle:hover {
            background: var(--saffron);
            color: var(--dark);
            transform: rotate(180deg);
        }

        .theme-toggle .fa-sun {
            display: none;
        }

        .theme-toggle .fa-moon {
            display: block;
        }

        [data-theme="light"] .theme-toggle .fa-sun {
            display: block;
        }

        [data-theme="light"] .theme-toggle .fa-moon {
            display: none;
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-icon i {
            color: var(--saffron);
            font-size: 1.5rem;
        }

        .nav-center {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-links {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .nav-links>a,
        .nav-dropdown>.nav-dropdown-btn {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.3s;
            position: relative;
            padding: 10px 16px;
            border-radius: 30px;
            background: transparent;
            border: none;
            cursor: pointer;
            font-family: inherit;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-links>a:hover,
        .nav-dropdown:hover>.nav-dropdown-btn {
            color: var(--saffron);
            background: rgba(255, 153, 51, 0.1);
        }

        .nav-dropdown-btn i {
            font-size: 0.7rem;
            transition: transform 0.3s ease;
        }

        .nav-dropdown:hover .nav-dropdown-btn i {
            transform: rotate(180deg);
        }

        /* Nav Dropdown */
        .nav-dropdown {
            position: relative;
        }

        .nav-dropdown-menu {
            position: absolute;
            top: calc(100% + 10px);
            left: 50%;
            transform: translateX(-50%);
            min-width: 200px;
            background: var(--bg-secondary);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 10px;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1001;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
        }

        .nav-dropdown:hover .nav-dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
        }

        .nav-dropdown-menu a {
            display: block;
            padding: 12px 16px;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 10px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .nav-dropdown-menu a:hover {
            background: rgba(255, 153, 51, 0.15);
            color: var(--saffron);
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 15px;
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
            white-space: nowrap;
        }

        .nav-cta:hover {
            background: var(--saffron);
            color: #1A1A2E;
            box-shadow: 0 0 20px rgba(255, 153, 51, 0.4);
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
            background: rgba(255, 153, 51, 0.1);
        }

        /* Mobile menu - hidden by default on desktop */
        .mobile-menu {
            display: none;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 28px;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            font-family: inherit;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--saffron) 100%);
            color: white;
            box-shadow: 0 10px 30px rgba(255, 107, 53, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(255, 107, 53, 0.4);
        }

        .btn-outline {
            background: transparent;
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .btn-outline:hover {
            border-color: var(--saffron);
            color: var(--saffron);
        }

        .btn-lg {
            padding: 16px 36px;
            font-size: 1.1rem;
        }

        .btn-sm {
            padding: 10px 20px;
            font-size: 0.9rem;
        }

        /* Hero Section - Premium Centered Design */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 140px 20px 80px;
            position: relative;
            overflow: hidden;
            text-align: center;
            width: 100%;
        }

        /* Animated Background Gradient */
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(ellipse 80% 50% at 50% -20%, rgba(255, 153, 51, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse 60% 40% at 20% 60%, rgba(255, 107, 53, 0.1) 0%, transparent 50%),
                radial-gradient(ellipse 60% 40% at 80% 80%, rgba(212, 175, 55, 0.08) 0%, transparent 50%);
            animation: heroGradient 8s ease-in-out infinite alternate;
        }

        @keyframes heroGradient {
            0% {
                opacity: 0.8;
                transform: scale(1);
            }

            100% {
                opacity: 1;
                transform: scale(1.05);
            }
        }

        /* Floating Particles */
        .hero-particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            width: 6px;
            height: 6px;
            background: var(--saffron);
            border-radius: 50%;
            opacity: 0;
            animation: particleFloat 10s infinite;
        }

        .particle:nth-child(1) {
            left: 10%;
            animation-delay: 0s;
        }

        .particle:nth-child(2) {
            left: 20%;
            animation-delay: 2s;
        }

        .particle:nth-child(3) {
            left: 30%;
            animation-delay: 4s;
        }

        .particle:nth-child(4) {
            left: 40%;
            animation-delay: 1s;
        }

        .particle:nth-child(5) {
            left: 50%;
            animation-delay: 3s;
        }

        .particle:nth-child(6) {
            left: 60%;
            animation-delay: 5s;
        }

        .particle:nth-child(7) {
            left: 70%;
            animation-delay: 2.5s;
        }

        .particle:nth-child(8) {
            left: 80%;
            animation-delay: 4.5s;
        }

        .particle:nth-child(9) {
            left: 90%;
            animation-delay: 1.5s;
        }

        @keyframes particleFloat {
            0% {
                transform: translateY(100vh) scale(0);
                opacity: 0;
            }

            10% {
                opacity: 0.6;
            }

            90% {
                opacity: 0.6;
            }

            100% {
                transform: translateY(-10vh) scale(1);
                opacity: 0;
            }
        }

        /* Large Om Symbol Background */
        .hero-om-bg {
            position: absolute;
            font-size: 30rem;
            color: rgba(255, 153, 51, 0.08);
            top: 55%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 0;
            animation: omPulse 6s ease-in-out infinite;
            font-family: serif;
            user-select: none;
            pointer-events: none;
            text-shadow: 0 0 80px rgba(255, 153, 51, 0.05);
        }

        /* Light mode Om - darker color for visibility */
        [data-theme="light"] .hero-om-bg {
            color: rgba(255, 107, 53, 0.07);
            text-shadow: 0 0 60px rgba(255, 107, 53, 0.04);
        }

        @keyframes omPulse {

            0%,
            100% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1);
            }

            50% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1.06);
            }
        }

        .hero-content {
            max-width: 900px;
            width: 100%;
            z-index: 2;
            position: relative;
            margin: 0 auto;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            background: rgba(255, 153, 51, 0.1);
            border-radius: 50px;
            font-size: 0.95rem;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 153, 51, 0.3);
            animation: badgeGlow 3s infinite;
            backdrop-filter: blur(10px);
        }

        @keyframes badgeGlow {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(255, 153, 51, 0.2);
            }

            50% {
                box-shadow: 0 0 40px rgba(255, 153, 51, 0.4), 0 0 60px rgba(255, 153, 51, 0.2);
            }
        }

        .hero-badge i {
            color: var(--accent);
            font-size: 1rem;
        }

        .hero h1 {
            font-size: 4.5rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 30px;
            background: var(--hero-title-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -2px;
            animation: titleReveal 1s ease-out;
        }

        @keyframes titleReveal {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-subtitle {
            font-size: 1.35rem;
            line-height: 1.8;
            color: var(--text-secondary);
            margin-bottom: 45px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            animation: subtitleReveal 1s ease-out 0.2s both;
        }

        @keyframes subtitleReveal {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-buttons {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            animation: buttonsReveal 1s ease-out 0.4s both;
        }

        @keyframes buttonsReveal {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero .btn-primary {
            padding: 18px 40px;
            font-size: 1.1rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--saffron) 50%, var(--gold) 100%);
            background-size: 200% 200%;
            animation: gradientShift 3s ease infinite;
            box-shadow: 0 15px 40px rgba(255, 107, 53, 0.35);
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .hero .btn-primary:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 20px 50px rgba(255, 107, 53, 0.5);
        }

        .hero .btn-outline {
            padding: 18px 40px;
            font-size: 1.1rem;
            border: 2px solid var(--border-color);
            background: var(--bg-card);
            backdrop-filter: blur(10px);
            color: var(--text-primary);
        }

        .hero .btn-outline:hover {
            border-color: var(--saffron);
            background: rgba(255, 153, 51, 0.1);
            color: var(--saffron);
        }

        /* Trust Badges */
        .trust-badges {
            display: flex;
            gap: 40px;
            margin-top: 60px;
            padding-top: 40px;
            border-top: 1px solid var(--border-color);
            justify-content: center;
            align-items: center;
            animation: badgesReveal 1s ease-out 0.6s both;
        }

        @keyframes badgesReveal {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        .trust-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-secondary);
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .trust-item:hover {
            color: var(--text-primary);
            transform: translateY(-2px);
        }

        .trust-item i {
            color: var(--saffron);
            font-size: 1.2rem;
        }

        /* Decorative Corner Elements */
        .hero-corner {
            position: absolute;
            width: 150px;
            height: 150px;
            opacity: 0.15;
        }

        .hero-corner.top-left {
            top: 100px;
            left: 50px;
            border-top: 2px solid var(--saffron);
            border-left: 2px solid var(--saffron);
        }

        .hero-corner.bottom-right {
            bottom: 50px;
            right: 50px;
            border-bottom: 2px solid var(--saffron);
            border-right: 2px solid var(--saffron);
        }

        /* Scroll Indicator */
        .scroll-indicator {
            position: absolute;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            color: var(--text-muted);
            font-size: 0.85rem;
            animation: scrollBounce 2s ease-in-out infinite;
        }

        .scroll-indicator i {
            font-size: 1.2rem;
            animation: scrollArrow 2s ease-in-out infinite;
        }

        @keyframes scrollBounce {

            0%,
            100% {
                opacity: 0.5;
            }

            50% {
                opacity: 1;
            }
        }

        @keyframes scrollArrow {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(8px);
            }
        }

        /* Responsive Hero */
        @media (max-width: 1024px) {
            .hero-om-bg {
                font-size: 25rem;
            }
        }

        @media (max-width: 768px) {
            .hero {
                padding: 140px 5% 60px;
            }

            .hero h1 {
                font-size: 2.8rem;
                letter-spacing: -1px;
            }

            .hero-subtitle {
                font-size: 1.1rem;
            }

            .trust-badges {
                flex-direction: column;
                gap: 15px;
                align-items: center;
            }

            .hero-om-bg {
                font-size: 18rem;
            }

            .hero-corner {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .hero-om-bg {
                font-size: 14rem;
            }

            .hero h1 {
                font-size: 2.2rem;
            }
        }

        /* Stats Section */
        .stats {
            padding: 60px 20px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        .stat-card {
            text-align: center;
            padding: 40px 20px;
            background: var(--bg-card);
            border-radius: 20px;
            border: 1px solid var(--border-color);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-10px);
            background: var(--bg-card-hover);
            border-color: var(--saffron);
        }

        .stat-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--saffron) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 5px;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        /* How It Works Section */
        .how-it-works {
            padding: 100px 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .steps-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
            position: relative;
            margin-top: 50px;
        }

        .step-line {
            display: none;
        }

        .step-card {
            position: relative;
            text-align: center;
            padding: 40px 30px;
            background: var(--bg-card);
            border-radius: 24px;
            border: 1px solid var(--border-color);
            z-index: 1;
            transition: all 0.4s ease;
        }

        .step-card:hover {
            transform: translateY(-10px);
            border-color: var(--saffron);
            box-shadow: 0 20px 40px rgba(255, 107, 53, 0.2);
        }

        .step-number {
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary), var(--saffron));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .step-icon {
            width: 80px;
            height: 80px;
            margin: 20px auto;
            background: rgba(255, 153, 51, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--saffron);
        }

        .step-card h3 {
            font-size: 1.3rem;
            margin-bottom: 15px;
        }

        .step-card p {
            color: var(--text-secondary);
            line-height: 1.7;
        }

        /* Section Styles */
        .section-header {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 60px;
        }

        .section-tag {
            display: inline-block;
            padding: 8px 20px;
            background: rgba(255, 153, 51, 0.2);
            color: var(--saffron);
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .section-header h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .section-header p {
            color: var(--text-secondary);
            font-size: 1.1rem;
            line-height: 1.7;
        }

        /* Features Section */
        .features {
            padding: 100px 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        .feature-card {
            padding: 40px;
            background: var(--bg-card);
            border-radius: 24px;
            border: 1px solid var(--border-color);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--saffron), var(--secondary));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.4s ease;
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--saffron) 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 25px;
        }

        .feature-card h3 {
            font-size: 1.4rem;
            margin-bottom: 15px;
        }

        .feature-card p {
            color: var(--text-secondary);
            line-height: 1.7;
            margin-bottom: 20px;
        }

        .feature-link {
            color: var(--saffron);
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .feature-link:hover {
            gap: 12px;
        }

        /* Rituals Showcase */
        .rituals-showcase {
            padding: 100px 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .rituals-slider {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
        }

        .ritual-card {
            background: var(--bg-card);
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            transition: all 0.4s ease;
        }

        .ritual-card:hover {
            transform: translateY(-10px);
            border-color: var(--saffron);
        }

        .ritual-image {
            height: 180px;
            background: linear-gradient(135deg, var(--maroon) 0%, var(--primary) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ritual-overlay {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ritual-overlay i {
            font-size: 2.5rem;
            color: white;
        }

        .ritual-content {
            padding: 25px;
        }

        .ritual-category {
            display: inline-block;
            padding: 5px 12px;
            background: rgba(255, 153, 51, 0.2);
            color: var(--saffron);
            border-radius: 20px;
            font-size: 0.8rem;
            margin-bottom: 15px;
        }

        .ritual-content h3 {
            font-size: 1.3rem;
            margin-bottom: 10px;
        }

        .ritual-content p {
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .ritual-meta {
            display: flex;
            gap: 20px;
            color: var(--saffron);
            font-size: 0.85rem;
            margin-bottom: 20px;
        }

        .text-center {
            text-align: center;
        }

        /* Testimonials */
        .testimonials {
            padding: 100px 20px;
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            align-items: start;
        }

        .testimonial-card {
            padding: 35px;
            background: var(--bg-card);
            border-radius: 24px;
            border: 1px solid var(--border-color);
            transition: all 0.4s ease;
            position: relative;
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
            border-color: var(--saffron);
        }

        .testimonial-card.featured {
            background: linear-gradient(135deg, rgba(255, 107, 53, 0.1) 0%, rgba(255, 153, 51, 0.05) 100%);
            border-color: rgba(255, 153, 51, 0.3);
            transform: scale(1.05);
        }

        .testimonial-badge {
            position: absolute;
            top: -12px;
            right: 30px;
            padding: 6px 15px;
            background: linear-gradient(135deg, var(--primary), var(--saffron));
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .testimonial-rating {
            color: var(--accent);
            margin-bottom: 15px;
        }

        .testimonial-rating i {
            margin-right: 3px;
        }

        .testimonial-text {
            color: var(--text-primary);
            line-height: 1.8;
            font-style: italic;
            margin-bottom: 25px;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .author-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary), var(--saffron));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .author-info strong {
            display: block;
            margin-bottom: 3px;
        }

        .author-info span {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        /* App Showcase */
        .app-showcase {
            padding: 100px 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .app-content .section-tag {
            margin-bottom: 15px;
        }

        .app-content h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .app-content>p {
            color: var(--text-secondary);
            font-size: 1.1rem;
            line-height: 1.7;
            margin-bottom: 30px;
        }

        .app-features-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 35px;
        }

        .app-feature {
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .app-feature-icon {
            color: var(--saffron);
            font-size: 1.3rem;
            margin-top: 3px;
        }

        .app-feature strong {
            display: block;
            margin-bottom: 3px;
        }

        .app-feature p {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin: 0;
        }

        /* Phone Mockup */
        .app-visual {
            display: flex;
            justify-content: center;
        }

        .phone-mockup {
            width: 280px;
            height: 560px;
            background: linear-gradient(135deg, #2D2D44 0%, #1A1A2E 100%);
            border: none;
            border-radius: 40px;
            padding: 15px;
            box-shadow: 0 50px 100px rgba(0, 0, 0, 0.5);
            position: relative;
        }

        .phone-mockup::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 25px;
            background: #1A1A2E;
            border-radius: 20px;
        }

        .phone-screen {
            width: 100%;
            height: 100%;
            background: var(--bg-primary);
            border-radius: 28px;
            padding: 50px 20px 20px;
        }

        .screen-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .screen-header i {
            font-size: 2rem;
            color: var(--saffron);
            display: block;
            margin-bottom: 10px;
        }

        .screen-header span {
            font-weight: 600;
            color: var(--text-primary);
        }

        .screen-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .screen-card {
            background: var(--bg-card);
            border-radius: 15px;
            padding: 25px 15px;
            text-align: center;
            transition: all 0.3s;
        }

        .screen-card:hover {
            background: var(--bg-card-hover);
            transform: scale(1.05);
        }

        .screen-card i {
            font-size: 1.5rem;
            color: var(--saffron);
            display: block;
            margin-bottom: 10px;
        }

        .screen-card span {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        /* FAQ Section */
        .faq-section {
            padding: 100px 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .faq-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .faq-item {
            margin-bottom: 15px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .faq-item:hover {
            border-color: rgba(255, 153, 51, 0.3);
        }

        .faq-item.active {
            border-color: var(--saffron);
        }

        .faq-question {
            width: 100%;
            padding: 25px 30px;
            background: none;
            border: none;
            color: var(--text-primary);
            font-size: 1.1rem;
            font-weight: 500;
            text-align: left;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: inherit;
            transition: all 0.3s;
        }

        .faq-question:hover {
            color: var(--saffron);
        }

        .faq-question i {
            color: var(--saffron);
            transition: transform 0.3s;
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            padding: 0;
            box-sizing: border-box;
            transition: max-height 0.3s ease, padding 0.3s ease;
        }

        .faq-item.active .faq-answer {
            max-height: 300px;
            padding: 0 30px 25px;
        }

        .faq-answer p {
            color: var(--text-secondary);
            line-height: 1.8;
        }

        /* CTA Section */
        .cta {
            padding: 100px 20px;
            text-align: center;
            max-width: 1400px;
            margin: 0 auto;
        }

        .cta-box {
            max-width: 900px;
            margin: 0 auto;
            padding: 80px 60px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--maroon) 100%);
            border-radius: 30px;
            position: relative;
            overflow: hidden;
        }

        .cta-decoration {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
        }

        .deco-item {
            position: absolute;
            font-size: 3rem;
            opacity: 0.1;
        }

        .deco-item:nth-child(1) {
            top: 10%;
            left: 10%;
        }

        .deco-item:nth-child(2) {
            top: 20%;
            right: 15%;
        }

        .deco-item:nth-child(3) {
            bottom: 15%;
            left: 20%;
        }

        .cta h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            position: relative;
        }

        .cta p {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 35px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            position: relative;
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }

        .cta .btn-primary {
            background: white;
            color: var(--maroon);
        }

        .cta .btn-outline {
            border-color: rgba(255, 255, 255, 0.5);
        }

        .cta-trust {
            display: flex;
            gap: 30px;
            justify-content: center;
            flex-wrap: wrap;
            font-size: 0.9rem;
            opacity: 0.8;
            position: relative;
        }

        .cta-trust span {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Footer */
        footer {
            padding: 60px 5% 30px;
            border-top: 1px solid var(--border-color);
        }

        .footer-content {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 50px;
            margin-bottom: 50px;
        }

        .footer-brand p {
            color: var(--text-secondary);
            margin-top: 20px;
            line-height: 1.7;
        }

        .footer-links h4 {
            color: var(--text-primary);
            margin-bottom: 20px;
            font-size: 1.1rem;
        }

        .footer-links a {
            display: block;
            color: var(--text-secondary);
            text-decoration: none;
            margin-bottom: 12px;
            transition: all 0.3s;
        }

        .footer-links a:hover {
            color: var(--saffron);
            transform: translateX(5px);
        }

        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 30px;
            border-top: 1px solid var(--border-color);
            color: var(--text-muted);
        }

        .social-links {
            display: flex;
            gap: 15px;
        }

        .social-links a {
            width: 45px;
            height: 45px;
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
            transform: translateY(-5px);
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

        .mobile-menu-btn .hamburger-lines {
            display: flex;
            flex-direction: column;
            gap: 5px;
            width: 24px;
        }

        .mobile-menu-btn .hamburger-lines span {
            display: block;
            height: 2px;
            background: currentColor;
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        .mobile-menu-btn.active .hamburger-lines span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .mobile-menu-btn.active .hamburger-lines span:nth-child(2) {
            opacity: 0;
        }

        .mobile-menu-btn.active .hamburger-lines span:nth-child(3) {
            transform: rotate(-45deg) translate(5px, -5px);
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .hero h1 {
                font-size: 2.8rem;
            }

            .hero-visual {
                width: 400px;
                height: 400px;
            }

            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .app-showcase {
                gap: 50px;
            }
        }

        @media (max-width: 992px) {
            .stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .footer-content {
                grid-template-columns: repeat(2, 1fr);
                gap: 30px;
            }

            .hero-visual {
                display: none;
            }

            .hero {
                padding: 100px 5% 60px;
            }

            .hero-content {
                max-width: 100%;
            }

            .steps-container {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .step-line {
                display: none;
            }

            .testimonials-grid {
                grid-template-columns: 1fr;
            }

            .testimonial-card.featured {
                transform: scale(1);
            }

            .app-showcase {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 50px;
                padding: 80px 20px;
            }

            .app-content {
                order: 1;
            }

            .app-visual {
                order: 2;
            }

            .app-features-list {
                align-items: center;
                max-width: 340px;
                margin-left: auto;
                margin-right: auto;
            }

            .app-feature {
                text-align: left;
            }

            /* Hide center nav links on tablet */
            .nav-center {
                display: none;
            }

            .nav-right .nav-cta {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: flex;
                align-items: center;
            }

            /* Mobile navbar styles */
            .nav-wrapper {
                padding: 12px 4%;
            }

            nav {
                padding: 10px 18px;
                border-radius: 50px;
            }

            .logo {
                font-size: 1.1rem;
                gap: 8px;
            }

            .logo-icon {
                width: 28px;
                height: 28px;
            }

            .logo-icon i {
                font-size: 1.3rem;
            }

            /* Hide nav center and links on mobile, keep hamburger */
            .nav-center {
                display: none;
            }

            .nav-right .theme-toggle,
            .nav-right .nav-login,
            .nav-right .nav-cta,
            .nav-right .user-profile-dropdown {
                display: none;
            }

            /* Mobile menu - compact slide-in drawer */
            .mobile-menu {
                display: block;
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
                flex-direction: column;
                align-items: flex-start;
                gap: 0;
                z-index: 9998;
                transform: translateX(100%);
                opacity: 0;
                visibility: hidden;
                transition: transform 0.3s ease, opacity 0.3s ease, visibility 0.3s ease;
                padding: 80px 0 30px;
                overflow-y: auto;
                box-shadow: -10px 0 40px rgba(0, 0, 0, 0.3);
            }

            .mobile-menu.active {
                display: flex;
                transform: translateX(0);
                opacity: 1;
                visibility: visible;
            }

            /* Dark overlay behind drawer */
            .mobile-menu-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 9997;
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .mobile-menu-overlay.active {
                display: block;
                opacity: 1;
            }

            /* Top bar inside drawer */
            .mobile-menu .mobile-menu-top {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 10px;
                padding: 18px 20px;
                border-bottom: 1px solid var(--border-color);
            }

            .mobile-menu .mobile-menu-close {
                width: 36px;
                height: 36px;
                min-width: 36px;
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

            .mobile-menu .mobile-menu-close:hover {
                background: var(--saffron);
                color: #1A1A2E;
            }

            .mobile-menu .mobile-menu-top .mobile-theme-toggle {
                width: 36px;
                height: 36px;
                min-width: 36px;
                font-size: 0.85rem;
                background: rgba(255, 153, 51, 0.1);
                border: 1.5px solid var(--border-accent);
                border-color: var(--border-accent);
                position: static;
                margin: 0;
            }

            /* Nav links */
            .mobile-menu a {
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

            .mobile-menu a:hover {
                color: var(--saffron);
                background: rgba(255, 153, 51, 0.08);
                border-left-color: var(--saffron);
            }

            /* CTA button in drawer */
            .mobile-menu .mobile-cta {
                margin: 8px 20px 0;
                padding: 12px 24px;
                background: transparent;
                color: var(--saffron);
                border: 2px solid var(--saffron);
                border-radius: 12px;
                font-size: 0.95rem;
                font-weight: 600;
                text-align: center;
                width: calc(100% - 40px);
                border-left: 2px solid var(--saffron);
            }

            .mobile-menu .mobile-cta:hover {
                background: rgba(255, 153, 51, 0.15);
                color: var(--saffron);
            }

            /* Primary filled CTA */
            .mobile-menu .mobile-cta-primary {
                background: linear-gradient(135deg, var(--primary) 0%, var(--saffron) 100%);
                color: white;
                border-color: transparent;
            }

            .mobile-menu .mobile-cta-primary:hover {
                opacity: 0.9;
                color: white;
                background: linear-gradient(135deg, var(--primary) 0%, var(--saffron) 100%);
            }

            /* Auth links in drawer */
            .mobile-menu .mobile-auth {
                display: flex;
                flex-direction: column;
                gap: 10px;
                margin: 10px 20px 0;
                padding-top: 5px;
                width: calc(100% - 40px);
            }

            .mobile-menu .mobile-auth a,
            .mobile-menu .mobile-auth button {
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                width: 100%;
                padding: 13px 20px;
                border-radius: 12px;
                border-left: none;
                margin: 0;
                font-size: 0.95rem;
                font-weight: 600;
                text-decoration: none;
                transition: all 0.3s ease;
                cursor: pointer;
                font-family: inherit;
            }

            /* Dashboard - primary filled */
            .mobile-menu .mobile-auth a:first-child {
                background: linear-gradient(135deg, var(--primary) 0%, var(--saffron) 100%);
                color: white;
                border: none;
            }

            .mobile-menu .mobile-auth a:first-child:hover {
                opacity: 0.9;
                background: linear-gradient(135deg, var(--primary) 0%, var(--saffron) 100%);
                border-left: none;
            }

            /* My Profile - outline */
            .mobile-menu .mobile-auth a:nth-child(2) {
                background: transparent;
                color: var(--saffron);
                border: 2px solid var(--saffron);
            }

            .mobile-menu .mobile-auth a:nth-child(2):hover {
                background: rgba(255, 153, 51, 0.1);
                border-left: 2px solid var(--saffron);
            }

            /* Mobile User Profile */
            .mobile-user-profile {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 16px 20px;
                background: rgba(255, 153, 51, 0.06);
                border-bottom: 1px solid var(--border-color);
                margin-bottom: 5px;
                width: 100%;
            }

            .mobile-user-avatar {
                width: 42px;
                height: 42px;
                background: linear-gradient(135deg, var(--primary) 0%, var(--saffron) 100%);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 1rem;
                font-weight: 700;
                flex-shrink: 0;
            }

            .mobile-user-info {
                display: flex;
                flex-direction: column;
                gap: 2px;
                overflow: hidden;
            }

            .mobile-user-name {
                color: var(--text-primary);
                font-size: 0.95rem;
                font-weight: 600;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .mobile-user-role {
                font-size: 0.8rem;
                font-weight: 500;
            }

            .mobile-menu-divider {
                width: 100%;
                height: 1px;
                background: var(--border-color);
                margin: 8px 0;
            }

            /* Logout button - red outline */
            .mobile-menu .mobile-auth .mobile-logout {
                background: transparent !important;
                color: #ff6b6b !important;
                border: 2px solid #ff6b6b !important;
                border-radius: 12px;
            }

            .mobile-menu .mobile-auth .mobile-logout:hover {
                background: rgba(255, 107, 107, 0.1) !important;
            }

            /* Hero - Mobile */
            .hero {
                padding: 110px 16px 50px;
                min-height: auto;
            }

            .hero h1 {
                font-size: 2.2rem;
                letter-spacing: -0.5px;
            }

            .hero-subtitle {
                font-size: 1rem;
                padding: 0 10px;
            }

            .hero-badge {
                font-size: 0.8rem;
                padding: 8px 16px;
            }

            .hero-buttons {
                flex-direction: column;
                gap: 12px;
                width: 100%;
                max-width: 320px;
                margin: 0 auto;
            }

            .hero-buttons .btn {
                width: 100%;
                justify-content: center;
                text-align: center;
            }

            .trust-badges {
                flex-direction: column;
                gap: 12px;
                align-items: center;
            }

            .hero-om-bg {
                font-size: 16rem;
            }

            .hero-corner {
                display: none;
            }

            /* Stats - Mobile */
            .stats {
                padding: 40px 16px;
                gap: 12px;
                grid-template-columns: repeat(2, 1fr);
            }

            .stat-card {
                padding: 25px 15px;
                border-radius: 16px;
            }

            .stat-value {
                font-size: 1.8rem;
            }

            .stat-icon {
                width: 55px;
                height: 55px;
                font-size: 1.4rem;
            }

            .stat-label {
                font-size: 0.85rem;
            }

            /* Sections - Mobile */
            .section-header {
                padding: 0 10px;
                margin-bottom: 40px;
            }

            .section-header h2 {
                font-size: 1.7rem;
            }

            .section-header p {
                font-size: 0.95rem;
            }

            /* Features - Mobile */
            .features {
                padding: 60px 16px;
            }

            .features-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .feature-card {
                padding: 25px 20px;
                border-radius: 18px;
            }

            .feature-icon {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
                border-radius: 16px;
            }

            /* How It Works - Mobile */
            .how-it-works {
                padding: 60px 16px;
            }

            .steps-container {
                gap: 25px;
            }

            .step-card {
                padding: 30px 20px;
                border-radius: 18px;
            }

            /* Rituals - Mobile */
            .rituals-showcase {
                padding: 60px 16px;
            }

            .rituals-slider {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            /* Testimonials - Mobile */
            .testimonials {
                padding: 60px 16px;
            }

            .testimonial-card {
                padding: 25px 20px;
            }

            /* App Showcase - Mobile */
            .app-showcase {
                padding: 60px 16px;
                gap: 32px;
            }

            .app-content h2 {
                font-size: 1.7rem;
            }

            .app-visual {
                overflow: hidden;
                width: 100%;
                justify-content: center;
            }

            .phone-mockup {
                width: 180px;
                height: 360px;
                border-radius: 28px;
                padding: 12px;
            }

            .phone-screen {
                border-radius: 20px;
                padding: 38px 14px 14px;
            }

            .screen-header {
                margin-bottom: 20px;
            }

            .screen-header i {
                font-size: 1.5rem;
            }

            .screen-content {
                gap: 10px;
            }

            .screen-card {
                padding: 18px 10px;
                border-radius: 12px;
            }

            .screen-card i {
                font-size: 1.2rem;
                margin-bottom: 6px;
            }

            /* FAQ - Mobile */
            .faq-section {
                padding: 60px 16px;
            }

            .faq-container {
                max-width: 100%;
            }

            .faq-item {
                border-radius: 12px;
            }

            .faq-question {
                font-size: 0.9rem;
                padding: 16px 14px;
                gap: 10px;
            }

            .faq-question span {
                flex: 1;
                word-break: break-word;
                overflow-wrap: break-word;
            }

            .faq-question i {
                flex-shrink: 0;
                font-size: 0.85rem;
            }

            .faq-answer {
                padding: 0;
            }

            .faq-item.active .faq-answer {
                padding: 0 14px 16px;
            }

            .faq-answer p {
                font-size: 0.85rem;
                line-height: 1.6;
            }

            /* CTA - Mobile */
            .cta {
                padding: 60px 16px;
            }

            .cta-box {
                padding: 35px 20px;
                border-radius: 20px;
            }

            .cta h2 {
                font-size: 1.5rem;
            }

            .cta p {
                font-size: 0.95rem;
                padding: 0 5px;
            }

            .cta-buttons {
                flex-direction: column;
                width: 100%;
                max-width: 300px;
                margin: 0 auto;
            }

            .cta-buttons .btn {
                width: 100%;
                justify-content: center;
            }

            .cta-trust {
                flex-direction: column;
                gap: 12px;
            }

            /* Footer - Mobile */
            .footer-content {
                grid-template-columns: 1fr;
                gap: 30px;
                text-align: center;
            }

            .footer-brand {
                order: -1;
            }

            .footer-bottom {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }

            .social-links {
                justify-content: center;
            }

            footer {
                padding: 60px 16px 30px;
            }
        }

        @media (max-width: 480px) {
            .hero {
                padding: 100px 12px 40px;
            }

            .hero h1 {
                font-size: 1.8rem;
            }

            .hero-subtitle {
                font-size: 0.9rem;
            }

            .hero-badge {
                font-size: 0.75rem;
                padding: 7px 14px;
                gap: 6px;
            }

            .hero-om-bg {
                font-size: 12rem;
            }

            .hero-buttons {
                max-width: 280px;
            }

            .stats {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

            .stat-card {
                padding: 18px 12px;
            }

            .stat-value {
                font-size: 1.5rem;
            }

            .stat-icon {
                width: 45px;
                height: 45px;
                font-size: 1.2rem;
                margin-bottom: 12px;
            }

            .section-header h2 {
                font-size: 1.4rem;
            }

            .section-tag {
                font-size: 0.8rem;
                padding: 6px 16px;
            }

            .cta h2 {
                font-size: 1.3rem;
            }

            .btn-lg {
                padding: 14px 24px;
                font-size: 0.95rem;
            }

            .btn-sm {
                padding: 8px 16px;
                font-size: 0.85rem;
            }

            .feature-card h3 {
                font-size: 1.15rem;
            }

            .step-card h3 {
                font-size: 1.1rem;
            }

            .phone-mockup {
                width: 200px;
                height: 400px;
            }
        }

        @media (max-width: 360px) {
            .hero h1 {
                font-size: 1.5rem;
            }

            .hero-subtitle {
                font-size: 0.85rem;
            }

            .hero-om-bg {
                font-size: 10rem;
            }

            .stat-card {
                padding: 15px 10px;
            }

            .stat-value {
                font-size: 1.3rem;
            }

            .section-header h2 {
                font-size: 1.2rem;
            }
        }

        /* User Profile Dropdown */
        .user-profile-dropdown {
            position: relative;
        }

        .user-profile-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 16px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 50px;
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: inherit;
            font-size: 0.95rem;
        }

        .user-profile-btn:hover {
            background: var(--bg-card-hover);
            border-color: var(--saffron);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--saffron) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            line-height: 1.2;
        }

        .user-name {
            font-weight: 500;
            color: var(--text-primary);
        }

        .user-role-badge {
            font-size: 0.7rem;
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .user-profile-btn i.fa-chevron-down {
            font-size: 0.7rem;
            transition: transform 0.3s ease;
        }

        .user-profile-dropdown.active .user-profile-btn i.fa-chevron-down {
            transform: rotate(180deg);
        }

        /* Dropdown Menu */
        .user-dropdown-menu {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            min-width: 200px;
            background: var(--bg-secondary);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 10px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 10001;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .user-profile-dropdown.active .user-dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.2s ease;
            font-size: 0.95rem;
        }

        .dropdown-item:hover {
            background: rgba(255, 153, 51, 0.15);
            color: var(--saffron);
        }

        .dropdown-item i {
            width: 18px;
            text-align: center;
            font-size: 1rem;
        }

        .dropdown-divider {
            height: 1px;
            background: var(--border-color);
            margin: 8px 0;
        }

        .dropdown-item.logout-btn {
            color: #ff6b6b;
        }

        .dropdown-item.logout-btn:hover {
            background: rgba(255, 107, 107, 0.15);
            color: #ff6b6b;
        }

        /* Mobile Dropdown */
        @media (max-width: 768px) {
            .user-profile-dropdown {
                width: 100%;
                max-width: 280px;
            }

            .user-profile-btn {
                width: 100%;
                justify-content: center;
                padding: 12px 20px;
            }

            .user-dropdown-menu {
                position: static;
                width: 100%;
                max-width: 280px;
                margin-top: 10px;
                opacity: 1;
                visibility: visible;
                transform: none;
                display: none;
            }

            .user-profile-dropdown.active .user-dropdown-menu {
                display: block;
            }
        }
    </style>
</head>

<body>
    <div class="bg-animation"></div>

    <div class="nav-wrapper" id="navWrapper">
        <nav id="navbar">
            <a href="#" class="logo" onclick="event.preventDefault(); window.scrollTo({ top: 0, behavior: 'smooth' });">
                <div class="logo-icon">
                    <i class="fas fa-om"></i>
                </div>
                Sanskar AI
            </a>

            <div class="nav-center">
                <div class="nav-links" id="navLinks">
                    <!-- About Dropdown -->
                    <div class="nav-dropdown">
                        <button class="nav-dropdown-btn">
                            About Us <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="nav-dropdown-menu">
                            <a href="#how-it-works">How It Works</a>
                            <a href="#features">Our Features</a>
                            <a href="#testimonials">Reviews</a>
                        </div>
                    </div>

                    <!-- Services Dropdown -->
                    <div class="nav-dropdown">
                        <button class="nav-dropdown-btn">
                            Services <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="nav-dropdown-menu">
                            <a href="<?= $isLoggedIn ? '/user/rituals' : '/login' ?>">Explore Rituals</a>
                            <a href="<?= $isLoggedIn ? '/user/select-pandit' : '/login' ?>">Find Pandit</a>
                            <a href="<?= $isLoggedIn ? '/user/ai-suggestions' : '/login' ?>">AI Assistant</a>
                        </div>
                    </div>

                    <!-- Direct Links -->
                    <a href="#faq">FAQ</a>
                </div>
            </div>

            <div class="nav-right">
                <!-- Theme Toggle -->
                <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
                    <i class="fas fa-sun"></i>
                    <i class="fas fa-moon"></i>
                </button>

                <?php if ($isLoggedIn): ?>
                    <!-- Logged In: User Profile Dropdown -->
                    <div class="user-profile-dropdown" id="userDropdown">
                        <button class="user-profile-btn" id="userDropdownBtn" aria-expanded="false">
                            <div class="user-avatar">
                                <?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?>
                            </div>
                            <div class="user-info">
                                <span class="user-name"><?= htmlspecialchars($currentUser['name'] ?? 'User') ?></span>
                                <span class="user-role-badge"
                                    style="background: <?= $roleDisplay['color'] ?>20; color: <?= $roleDisplay['color'] ?>;">
                                    <?= $roleDisplay['name'] ?>
                                </span>
                            </div>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="user-dropdown-menu">
                            <a href="<?= $dashboardUrl ?>" class="dropdown-item">
                                <i class="fas fa-home"></i>
                                <span>Dashboard</span>
                            </a>
                            <a href="<?= $settingsUrl ?>" class="dropdown-item">
                                <!-- Reusing settingsUrl which points to profile for admin/pandit -->
                                <i class="fas fa-user-circle"></i>
                                <span>My Profile</span>
                            </a>
                            <a href="<?= $settingsUrl ?>" class="dropdown-item">
                                <i class="fas fa-cog"></i>
                                <span>Settings</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <form action="/logout" method="POST" style="margin: 0;" <?= ($isLoggedIn && $userRole === 'user' && !$hasGivenFeedback) ? 'onsubmit="handleLogoutClick(event)"' : '' ?>>
                                <?= App\Core\Auth::csrfField() ?>
                                <button type="submit" class="dropdown-item logout-btn"
                                    style="width: 100%; border: none; background: none; cursor: pointer; font-family: inherit;">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Guest: Login + CTA Button -->
                    <a href="/login" class="nav-login">Login</a>
                    <a href="/signup" class="nav-cta">Get Started</a>
                <?php endif; ?>
            </div>

            <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Toggle menu">
                <div class="hamburger-lines">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </button>
        </nav>
    </div>

    <!-- Mobile Menu Overlay Backdrop -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

    <!-- Mobile Menu Drawer -->
    <div class="mobile-menu" id="mobileMenu">
        <!-- Top bar with toggle & close -->
        <div class="mobile-menu-top">
            <button class="theme-toggle mobile-theme-toggle" id="mobileThemeToggle">
                <i class="fas fa-sun"></i>
                <i class="fas fa-moon"></i>
            </button>
            <button class="mobile-menu-close" id="mobileMenuClose" aria-label="Close menu">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <?php if ($isLoggedIn): ?>
            <div class="mobile-user-profile">
                <div class="mobile-user-avatar">
                    <?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?>
                </div>
                <div class="mobile-user-info">
                    <span class="mobile-user-name"><?= htmlspecialchars($currentUser['name'] ?? 'User') ?></span>
                    <span class="mobile-user-role"
                        style="color: <?= $roleDisplay['color'] ?>;"><?= $roleDisplay['name'] ?></span>
                </div>
            </div>
        <?php endif; ?>

        <a href="#how-it-works">How It Works</a>
        <a href="#features">Features</a>
        <a href="<?= $isLoggedIn ? '/user/rituals' : '/login' ?>">Explore Rituals</a>
        <a href="<?= $isLoggedIn ? '/user/select-pandit' : '/login' ?>">Find Pandit</a>
        <a href="#testimonials">Reviews</a>
        <a href="#faq">FAQ</a>

        <div class="mobile-menu-divider"></div>

        <?php if ($isLoggedIn): ?>
            <div class="mobile-auth">
                <a href="<?= $dashboardUrl ?>" class="mobile-cta">Dashboard</a>
                <a href="<?= $settingsUrl ?>" class="mobile-cta">My Profile</a>
                <form action="/logout" method="POST" style="margin: 0; width: 100%;" <?= ($isLoggedIn && $userRole === 'user' && !$hasGivenFeedback) ? 'onsubmit="handleLogoutClick(event)"' : '' ?>>
                    <?= App\Core\Auth::csrfField() ?>
                    <button type="submit" class="mobile-cta mobile-logout"
                        style="width: 100%; cursor: pointer; font-family: inherit;">Logout</button>
                </form>
            </div>
        <?php else: ?>
            <div class="mobile-auth">
                <a href="/login" class="mobile-cta">Login</a>
                <a href="/signup" class="mobile-cta mobile-cta-primary">Get Started</a>
            </div>
        <?php endif; ?>
    </div>

    <?= $content ?>

    <footer>
        <div class="footer-content">
            <div class="footer-brand">
                <a href="/" class="logo">
                    <i class="fas fa-om"></i>
                    Sanskar AI
                </a>
                <p>Preserving the sacred traditions of Sanatan Dharma through technology. Your trusted guide for Hindu
                    rituals, ceremonies, and spiritual wisdom.</p>
            </div>
            <div class="footer-links">
                <h4>Quick Links</h4>
                <a href="#how-it-works">How It Works</a>
                <a href="#features">Features</a>
                <a href="/login">Login</a>
                <a href="/signup">Sign Up</a>
            </div>
            <div class="footer-links">
                <h4>Resources</h4>
                <a href="#">Ritual Guide</a>
                <a href="#">Find Pandit</a>
                <a href="#">Cultural Insights</a>
                <a href="#faq">FAQ</a>
            </div>
            <div class="footer-links">
                <h4>Contact</h4>
                <a href="mailto:info@sanskarai.com"><i class="fas fa-envelope"></i> info@sanskarai.com</a>
                <a href="#"><i class="fas fa-phone"></i> +91 98765 43210</a>
                <a href="#"><i class="fas fa-map-marker-alt"></i> Mumbai, India</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© <?= date('Y') ?> Sanskar AI. All rights reserved. Made with <i class="fas fa-heart"
                    style="color: var(--saffron);"></i> for Sanatan Dharma</p>
            <div class="social-links">
                <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Theme Toggle Logic
            const themeToggle = document.getElementById('themeToggle');
            const mobileThemeToggle = document.getElementById('mobileThemeToggle');
            const html = document.documentElement;

            // Check for saved theme preference or use default (dark)
            const savedTheme = localStorage.getItem('theme') || 'dark';
            html.setAttribute('data-theme', savedTheme);
            console.log('Initial theme:', savedTheme);

            function toggleTheme(e) {
                if (e) e.preventDefault();

                const currentTheme = html.getAttribute('data-theme');
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';

                html.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                console.log('Theme switched to:', newTheme);
            }

            if (themeToggle) {
                themeToggle.addEventListener('click', toggleTheme);
                console.log('Desktop theme toggle attached');
            } else {
                console.error('Desktop theme toggle not found');
            }

            if (mobileThemeToggle) {
                mobileThemeToggle.addEventListener('click', toggleTheme);
                console.log('Mobile theme toggle attached');
            }
        });

        // Navbar scroll effect
        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const nav = document.getElementById('navbar');
            if (nav) {
                nav.classList.toggle('scrolled', window.scrollY > 50);
            }
        });

        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        const mobileMenuClose = document.getElementById('mobileMenuClose');
        const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');

        // Function to open mobile menu
        function openMobileMenu() {
            mobileMenuBtn.classList.add('active');
            mobileMenu.classList.add('active');
            if (mobileMenuOverlay) mobileMenuOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        // Function to close mobile menu
        function closeMobileMenu() {
            mobileMenuBtn.classList.remove('active');
            mobileMenu.classList.remove('active');
            if (mobileMenuOverlay) mobileMenuOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        mobileMenuBtn.addEventListener('click', () => {
            if (mobileMenu.classList.contains('active')) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        });

        // Close button click
        if (mobileMenuClose) {
            mobileMenuClose.addEventListener('click', closeMobileMenu);
        }

        // Close when clicking overlay backdrop
        if (mobileMenuOverlay) {
            mobileMenuOverlay.addEventListener('click', closeMobileMenu);
        }

        // Close mobile menu when clicking a link
        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', closeMobileMenu);
        });

        // User Profile Dropdown Toggle
        const userDropdown = document.getElementById('userDropdown');
        const userDropdownBtn = document.getElementById('userDropdownBtn');

        if (userDropdownBtn && userDropdown) {
            userDropdownBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('active');
                userDropdownBtn.setAttribute('aria-expanded',
                    userDropdown.classList.contains('active'));
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!userDropdown.contains(e.target)) {
                    userDropdown.classList.remove('active');
                    userDropdownBtn.setAttribute('aria-expanded', 'false');
                }
            });

            // Close dropdown on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && userDropdown.classList.contains('active')) {
                    userDropdown.classList.remove('active');
                    userDropdownBtn.setAttribute('aria-expanded', 'false');
                    userDropdownBtn.focus();
                }
            });
        }
    </script>
    <?php if ($isLoggedIn && $userRole === 'user' && !$hasGivenFeedback): ?>
        <!-- Logout Feedback Modal (Vanilla JS/CSS for Landing Page) -->
        <style>
            .custom-modal-overlay {
                position: fixed;
                top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(0,0,0,0.5);
                display: none;
                justify-content: center;
                align-items: center;
                z-index: 9999;
                opacity: 0;
                transition: opacity 0.3s ease;
            }
            .custom-modal-overlay.show {
                display: flex;
                opacity: 1;
            }
            .custom-modal {
                background: var(--bg-secondary);
                width: 90%;
                max-width: 500px;
                border-radius: 16px;
                overflow: hidden;
                box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                transform: translateY(20px);
                transition: transform 0.3s ease;
            }
            .custom-modal-overlay.show .custom-modal {
                transform: translateY(0);
            }
            .custom-modal-header {
                background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
                color: white;
                padding: 15px 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .custom-modal-title {
                margin: 0;
                font-size: 1.25rem;
            }
            .custom-modal-close {
                background: none;
                border: none;
                color: white;
                font-size: 1.5rem;
                cursor: pointer;
            }
            .custom-modal-body {
                padding: 30px 20px;
                text-align: center;
                color: var(--text-primary);
            }
            .custom-modal-footer {
                padding: 0 20px 30px;
                display: flex;
                justify-content: center;
                gap: 15px;
            }
        </style>

        <div class="custom-modal-overlay" id="logoutFeedbackModal">
            <div class="custom-modal">
                <div class="custom-modal-header">
                    <h5 class="custom-modal-title"><i class="fas fa-comment-dots me-2"></i> We Value Your Feedback</h5>
                    <form action="/logout" method="POST" id="directLogoutForm" style="display: none;">
                        <?= \App\Core\Auth::csrfField() ?>
                        <input type="hidden" name="force_logout" value="1">
                    </form>
                    <button type="button" class="custom-modal-close" aria-label="Close" onclick="document.getElementById('directLogoutForm').submit();">&times;</button>
                </div>
                <div class="custom-modal-body">
                    <div style="font-size: 3.5rem; color: var(--saffron); margin-bottom: 20px;">
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <h4 style="margin-bottom: 15px; font-weight: 600;">How was your experience?</h4>
                    <p style="color: var(--text-secondary); margin-bottom: 0; font-size: 0.95rem;">Before you log out, would you like to share your feedback? Your insights help us improve Sanskar AI.</p>
                </div>
                <div class="custom-modal-footer">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('directLogoutForm').submit();" style="padding: 10px 25px;">Maybe later</button>
                    <a href="/user/feedback?logout=1" class="btn btn-primary" style="padding: 10px 25px;">Give Feedback</a>
                </div>
            </div>
        </div>

        <script>
            function handleLogoutClick(e) {
                e.preventDefault();
                var modal = document.getElementById('logoutFeedbackModal');
                modal.classList.add('show');
            }
        </script>
    <?php endif; ?>
</body>

</html>