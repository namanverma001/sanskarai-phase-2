<?php
use App\Core\Auth;

// Get current user info if logged in
$isLoggedIn = Auth::check();
$currentUser = Auth::user();
$userRole = Auth::role();
$dashboardUrl = Auth::dashboardUrl();

// Settings URL based on role
$settingsUrl = match ($userRole) {
    'admin' => '/admin/users',
    'pandit' => '/pandit/profile',
    'user' => '/user/families',
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
    <title><?= htmlspecialchars($title ?? 'Pandit Panel - Sanskar AI') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #8B5CF6;
            --primary-dark: #7C3AED;
            --secondary: #EC4899;
            --accent: #F59E0B;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
            --dark: #1E1E2E;
            --sidebar-bg: #2D1B4E;
            --content-bg: #FAF5FF;
            --saffron: #FF9933;
            --gold: #D4AF37;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--content-bg);
            min-height: 100vh;
        }

        /* Main Navbar */
        .main-navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, #1A1A2E 0%, #16213E 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .main-navbar .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.4rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
        }

        .main-navbar .logo i {
            color: var(--saffron);
            font-size: 1.6rem;
        }

        .main-navbar .nav-links {
            display: flex;
            gap: 25px;
            align-items: center;
        }

        .main-navbar .nav-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .main-navbar .nav-links a:hover {
            color: var(--saffron);
        }

        /* User Profile Dropdown in Navbar */
        .user-profile-dropdown {
            position: relative;
        }

        .user-profile-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: inherit;
            font-size: 0.95rem;
        }

        .user-profile-btn:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--saffron);
        }

        .nav-user-avatar {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .nav-user-info {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            line-height: 1.2;
        }

        .nav-user-name {
            font-weight: 500;
            color: white;
        }

        .nav-user-role-badge {
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
            background: rgba(26, 26, 46, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
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
            color: rgba(255, 255, 255, 0.8);
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
            background: rgba(255, 255, 255, 0.1);
            margin: 8px 0;
        }

        .dropdown-item.logout-btn {
            color: #ff6b6b;
        }

        .dropdown-item.logout-btn:hover {
            background: rgba(255, 107, 107, 0.15);
            color: #ff6b6b;
        }

        .dashboard-container {
            display: flex;
            min-height: calc(100vh - 70px);
            margin-top: 70px;
        }

        .sidebar {
            width: 260px;
            background: var(--sidebar-bg);
            color: white;
            position: fixed;
            top: 70px;
            bottom: 0;
            overflow-y: auto;
            transition: transform 0.3s ease;
            z-index: 100;
        }

        .sidebar.collapsed {
            transform: translateX(-100%);
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h1 {
            font-size: 1.3rem;
            background: linear-gradient(135deg, #a78bfa 0%, #ec4899 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .sidebar-header span {
            font-size: 0.75rem;
            background: var(--primary);
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: 5px;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-section {
            padding: 0 20px;
            margin-bottom: 15px;
        }

        .menu-section-title {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, 0.4);
            margin-bottom: 10px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s;
            border-radius: 8px;
            margin: 2px 10px;
        }

        .menu-item:hover,
        .menu-item.active {
            background: rgba(139, 92, 246, 0.2);
            color: white;
        }

        .menu-item.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }

        .menu-item i {
            width: 20px;
            margin-right: 12px;
        }

        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 30px;
            min-height: calc(100vh - 70px);
            transition: margin-left 0.3s ease;
        }

        .sidebar.collapsed~.main-content {
            margin-left: 0;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 15px 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark);
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info {
            text-align: right;
        }

        .user-name {
            font-weight: 600;
            color: var(--dark);
        }

        .user-role {
            font-size: 0.75rem;
            color: #6B7280;
        }

        .logout-btn-top {
            background: var(--danger);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.3s ease;
        }

        .logout-btn-top:hover {
            background: #DC2626;
            transform: translateY(-2px);
        }

        .card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #E5E7EB;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.1) 0%, rgba(236, 72, 153, 0.1) 100%);
            border-radius: 50%;
            transform: translate(30%, -30%);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 15px;
        }

        .stat-icon.purple {
            background: rgba(139, 92, 246, 0.1);
            color: #8B5CF6;
        }

        .stat-icon.pink {
            background: rgba(236, 72, 153, 0.1);
            color: #EC4899;
        }

        .stat-icon.green {
            background: rgba(16, 185, 129, 0.1);
            color: #10B981;
        }

        .stat-icon.orange {
            background: rgba(245, 158, 11, 0.1);
            color: #F59E0B;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark);
        }

        .stat-label {
            color: #6B7280;
            font-size: 0.9rem;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #E5E7EB;
        }

        .table th {
            font-weight: 600;
            color: #6B7280;
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        .table tr:hover {
            background: #F9FAFB;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-success {
            background: #D1FAE5;
            color: #065F46;
        }

        .badge-warning {
            background: #FEF3C7;
            color: #92400E;
        }

        .badge-danger {
            background: #FEE2E2;
            color: #991B1B;
        }

        .badge-info {
            background: #EDE9FE;
            color: #5B21B6;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            font-family: inherit;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.8rem;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #D1FAE5;
            color: #065F46;
        }

        .alert-error {
            background: #FEE2E2;
            color: #991B1B;
        }

        .alert-warning {
            background: #FEF3C7;
            color: #92400E;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #E5E7EB;
            border-radius: 10px;
            font-size: 1rem;
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }

        /* Sidebar Toggle Button in Navbar */
        .sidebar-toggle-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.1rem;
        }

        .sidebar-toggle-btn:hover {
            background: rgba(255, 153, 51, 0.2);
            border-color: var(--saffron);
            color: var(--saffron);
        }

        /* Sidebar Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 70px;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 99;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }

        /* Desktop Sidebar Collapse Styles */
        @media (min-width: 769px) {
            .sidebar.collapsed {
                width: 70px;
                transform: none;
            }

            .sidebar.collapsed .sidebar-header {
                padding: 20px 0;
                display: flex;
                justify-content: center;
            }

            .sidebar.collapsed .sidebar-header h1 {
                font-size: 0;
            }

            .sidebar.collapsed .sidebar-header h1 i {
                font-size: 1.5rem;
                margin: 0;
            }

            .sidebar.collapsed .sidebar-header span {
                display: none;
            }

            .sidebar.collapsed .menu-section-title {
                display: none;
            }

            .sidebar.collapsed .menu-item {
                justify-content: center;
                padding: 0;
                width: 50px;
                height: 50px;
                margin: 5px auto;
                border-radius: 12px;
            }

            .sidebar.collapsed .menu-item i {
                margin-right: 0;
                font-size: 1.4rem;
                width: auto;
            }

            .sidebar.collapsed .menu-item span {
                display: none;
            }

            .sidebar.collapsed~.main-content {
                margin-left: 70px;
            }

            .sidebar.collapsed~.main-footer {
                margin-left: 70px;
            }
        }

        /* Table Responsive Wrapper */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Footer */
        .main-footer {
            background: linear-gradient(135deg, #1A1A2E 0%, #16213E 100%);
            color: white;
            padding: 60px 5% 30px;
            margin-left: 70px;
            /* Default collapsed margin */
            transition: margin-left 0.3s ease;
        }





        .footer-content {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-brand .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.4rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
            margin-bottom: 15px;
        }

        .footer-brand .logo i {
            color: var(--saffron);
        }

        .footer-brand p {
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.7;
            font-size: 0.95rem;
        }

        .footer-links h4 {
            font-size: 1.1rem;
            margin-bottom: 20px;
            color: white;
        }

        .footer-links a {
            display: block;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            margin-bottom: 12px;
            transition: all 0.3s;
            font-size: 0.95rem;
        }

        .footer-links a:hover {
            color: var(--saffron);
            transform: translateX(5px);
        }

        .footer-links a i {
            margin-right: 8px;
            width: 16px;
        }

        .footer-bottom {
            padding-top: 25px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .footer-bottom p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }

        .social-links {
            display: flex;
            gap: 15px;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
        }

        .social-links a:hover {
            background: var(--primary);
            transform: translateY(-3px);
        }

        /* Responsive Styles */
        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .footer-content {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 768px) {
            .main-navbar .nav-links {
                display: none;
            }

            .main-navbar .nav-links.active {
                display: flex;
                flex-direction: column;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: linear-gradient(135deg, #1A1A2E 0%, #16213E 100%);
                padding: 20px;
                gap: 15px;
            }

            .sidebar {
                transform: translateX(-100%);
                width: 260px;
                /* Reset width for mobile if needed */
            }

            .sidebar.collapsed {
                transform: translateX(-100%);
                width: 260px;
                /* Ensure mobile sidebar is full width even if collapsed class present */
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 20px 15px;
            }

            .sidebar.collapsed~.main-content {
                margin-left: 0;
            }

            .main-footer {
                margin-left: 0;
            }

            .top-bar {
                flex-direction: column;
                gap: 15px;
                padding: 15px;
                text-align: center;
            }

            .page-title {
                font-size: 1.25rem;
            }

            .user-menu {
                width: 100%;
                justify-content: center;
            }

            .user-info {
                text-align: center;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }

            .stat-card {
                padding: 20px 15px;
            }

            .stat-value {
                font-size: 1.5rem;
            }

            .card {
                padding: 20px 15px;
                border-radius: 12px;
            }

            .card-header {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }

            .btn {
                padding: 10px 16px;
                font-size: 0.85rem;
            }

            .table th,
            .table td {
                padding: 10px 8px;
                font-size: 0.85rem;
            }

            .footer-content {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .footer-brand .logo {
                justify-content: center;
            }

            .footer-links a:hover {
                transform: none;
            }

            .footer-bottom {
                flex-direction: column;
                text-align: center;
            }

            .main-footer {
                margin-left: 0 !important;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .top-bar {
                padding: 12px;
            }

            .page-title {
                font-size: 1.1rem;
            }

            .logout-btn-top {
                padding: 6px 12px;
                font-size: 0.85rem;
            }
        }
    </style>
</head>

<body>
    <!-- Main Navbar -->
    <nav class="main-navbar">
        <div style="display: flex; align-items: center; gap: 15px;">
            <button class="sidebar-toggle-btn" id="sidebarToggle" aria-label="Toggle sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <a href="/" class="logo">
                <i class="fas fa-om"></i>
                Sanskar AI
            </a>
        </div>
        <div class="nav-links" id="navLinks">
            <a href="/">Home</a>
            <a href="<?= $dashboardUrl ?>">Dashboard</a>

            <?php if ($isLoggedIn): ?>
                <!-- Logged In: User Profile Dropdown -->
                <div class="user-profile-dropdown" id="userDropdown">
                    <button class="user-profile-btn" id="userDropdownBtn" aria-expanded="false">
                        <div class="nav-user-avatar">
                            <?= strtoupper(substr($currentUser['name'] ?? 'P', 0, 1)) ?>
                        </div>
                        <div class="nav-user-info">
                            <span class="nav-user-name"><?= htmlspecialchars($currentUser['name'] ?? 'Pandit') ?></span>
                            <span class="nav-user-role-badge"
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
                        <a href="/pandit/profile" class="dropdown-item">
                            <i class="fas fa-user-circle"></i>
                            <span>My Profile</span>
                        </a>
                        <a href="<?= $settingsUrl ?>" class="dropdown-item">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <form action="/logout" method="POST" style="margin: 0;">
                            <?= App\Core\Auth::csrfField() ?>
                            <button type="submit" class="dropdown-item logout-btn"
                                style="width: 100%; border: none; background: none; cursor: pointer; font-family: inherit;">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        <!-- Sidebar collapsed by default -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h1><i class="fas fa-om"></i> Sanskar AI <span>Pandit</span></h1>
            </div>
            <nav class="sidebar-menu">
                <div class="menu-section">
                    <div class="menu-section-title">Main</div>
                    <a href="/pandit/dashboard"
                        class="menu-item <?= strpos($_SERVER['REQUEST_URI'], '/pandit/dashboard') !== false ? 'active' : '' ?>">
                        <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
                    </a>
                </div>
                <div class="menu-section">
                    <div class="menu-section-title">Services</div>
                    <a href="/pandit/assignments"
                        class="menu-item <?= strpos($_SERVER['REQUEST_URI'], '/pandit/assignments') !== false ? 'active' : '' ?>">
                        <i class="fas fa-calendar-check"></i> <span>Bookings</span>
                    </a>
                    <a href="/pandit/questions"
                        class="menu-item <?= strpos($_SERVER['REQUEST_URI'], '/pandit/questions') !== false ? 'active' : '' ?>">
                        <i class="fas fa-comments"></i> <span>Q&A Requests</span>
                    </a>
                    <a href="/pandit/custom-rituals"
                        class="menu-item <?= strpos($_SERVER['REQUEST_URI'], '/pandit/custom-rituals') !== false ? 'active' : '' ?>">
                        <i class="fas fa-check-double"></i> <span>Validate Rituals</span>
                    </a>
                </div>
                <div class="menu-section">
                    <div class="menu-section-title">Account</div>
                    <a href="/pandit/profile"
                        class="menu-item <?= strpos($_SERVER['REQUEST_URI'], '/pandit/profile') !== false ? 'active' : '' ?>">
                        <i class="fas fa-user-circle"></i> <span>My Profile</span>
                    </a>
                    <form action="/logout" method="POST" style="margin: 0;">
                        <?= \App\Core\Auth::csrfField() ?>
                        <button type="submit" class="menu-item"
                            style="width: 100%; border: none; background: transparent; cursor: pointer; font-family: inherit; font-size: inherit; text-align: left;">
                            <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <main class="main-content">
            <div class="top-bar">
                <h1 class="page-title"><?= htmlspecialchars($title ?? 'Dashboard') ?></h1>
                <!-- User menu removed as per request -->
            </div>

            <?php if (isset($_SESSION['flash']['success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['flash']['success']) ?>
                </div>
                <?php unset($_SESSION['flash']['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['flash']['error'])): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($_SESSION['flash']['error']) ?>
                </div>
                <?php unset($_SESSION['flash']['error']); ?>
            <?php endif; ?>

            <?= $content ?>
        </main>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
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
                <a href="/">Home</a>
                <a href="/pandit/dashboard">Dashboard</a>
                <a href="/pandit/assignments">Bookings</a>
                <a href="/pandit/questions">Q&A Requests</a>
            </div>
            <div class="footer-links">
                <h4>Services</h4>
                <a href="/pandit/custom-rituals">Validate Rituals</a>
                <a href="/pandit/profile">Profile</a>
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
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const mainFooter = document.querySelector('.main-footer');
        const isMobile = () => window.innerWidth <= 768;

        // Initialize footer margin based on default state
        if (!isMobile() && !sidebar.classList.contains('collapsed')) {
            if (mainFooter) mainFooter.style.marginLeft = '260px';
        }

        // Set correct icon on page load
        if (!isMobile()) {
            sidebarToggle.querySelector('i').className = sidebar.classList.contains('collapsed') ? 'fas fa-bars' : 'fas fa-times';
        }

        function toggleSidebar() {
            if (isMobile()) {
                // On mobile: toggle open/close (starts hidden)
                sidebar.classList.toggle('mobile-open');
                sidebarOverlay.classList.toggle('active');
            } else {
                // On desktop: toggle collapsed (starts collapsed)
                sidebar.classList.toggle('collapsed');
                if (sidebar.classList.contains('collapsed')) {
                    if (mainFooter) mainFooter.style.marginLeft = '70px';
                } else {
                    if (mainFooter) mainFooter.style.marginLeft = '260px'; // Expanded margin
                }
            }
            const icon = sidebarToggle.querySelector('i');
            icon.classList.toggle('fa-bars');
            icon.classList.toggle('fa-times');
        }

        sidebarToggle.addEventListener('click', toggleSidebar);
        sidebarOverlay.addEventListener('click', toggleSidebar);

        // Close sidebar when clicking menu items on mobile
        sidebar.querySelectorAll('.menu-item').forEach(item => {
            item.addEventListener('click', () => {
                if (isMobile()) {
                    toggleSidebar();
                }
            });
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

        // Mobile Navigation Toggle
        const mobileNavToggle = document.querySelector('.mobile-nav-toggle');
        const navLinks = document.querySelector('.nav-links');

        if (mobileNavToggle) {
            mobileNavToggle.addEventListener('click', () => {
                navLinks.classList.toggle('active');
            });
        }
    </script>
</body>

</html>