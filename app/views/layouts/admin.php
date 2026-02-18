<?php
use App\Core\Auth;

// Get current user info if logged in
$isLoggedIn = Auth::check();
$currentUser = Auth::user();
$userRole = Auth::role();
$dashboardUrl = Auth::dashboardUrl();

// Settings URL based on role
$settingsUrl = match($userRole) {
    'admin' => '/admin/profile',
    'pandit' => '/pandit/profile',
    'user' => '/user/families',
    default => '/'
};

// Role display name and colors
$roleDisplay = match($userRole) {
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
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'><circle cx='32' cy='32' r='32' fill='%23FF6B35'/><text x='32' y='46' text-anchor='middle' font-size='40' font-family='serif' fill='white'>ॐ</text></svg>">
    <title><?= htmlspecialchars($title ?? 'Admin - Sanskar AI') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6366F1;
            --primary-dark: #4F46E5;
            --secondary: #8B5CF6;
            --accent: #EC4899;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
            --dark: #1E1E2E;
            --sidebar-bg: #1E1E2E;
            --content-bg: #F1F5F9;
            --saffron: #FF9933;
            --gold: #D4AF37;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
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
            border-bottom: 1px solid rgba(255,255,255,0.1);
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
            color: rgba(255,255,255,0.8);
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
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 50px;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: inherit;
            font-size: 0.95rem;
        }
        .user-profile-btn:hover {
            background: rgba(255,255,255,0.15);
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
            background: rgba(26,26,46,0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 16px;
            padding: 10px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 10001;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
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
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.2s ease;
            font-size: 0.95rem;
        }
        .dropdown-item:hover {
            background: rgba(255,153,51,0.15);
            color: var(--saffron);
        }
        .dropdown-item i {
            width: 18px;
            text-align: center;
            font-size: 1rem;
        }
        .dropdown-divider {
            height: 1px;
            background: rgba(255,255,255,0.1);
            margin: 8px 0;
        }
        .dropdown-item.logout-btn {
            color: #ff6b6b;
        }
        .dropdown-item.logout-btn:hover {
            background: rgba(255,107,107,0.15);
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
            left: 0;
            height: calc(100vh - 70px);
            overflow-y: auto;
            overflow-x: hidden;
            transition: width 0.3s ease;
            z-index: 100;
        }

        /* Desktop collapsed = icon-only 70px */
        .sidebar.collapsed {
            width: 70px;
        }
        .sidebar.collapsed .sidebar-header h1 {
            font-size: 0;
            opacity: 0;
        }
        .sidebar.collapsed .sidebar-header {
            padding: 15px 10px;
            text-align: center;
        }
        .sidebar.collapsed .sidebar-header span {
            display: none;
        }
        .sidebar.collapsed .menu-section {
            padding: 0 5px;
        }
        .sidebar.collapsed .menu-section-title {
            font-size: 0;
            opacity: 0;
            height: 0;
            margin: 0;
            overflow: hidden;
        }
        .sidebar.collapsed .menu-item {
            justify-content: center;
            padding: 14px 0;
            margin: 2px 5px;
        }
        .sidebar.collapsed .menu-item i {
            margin-right: 0;
            font-size: 1.15rem;
        }
        .sidebar.collapsed .menu-item span,
        .sidebar.collapsed .menu-item-text {
            display: none;
        }
        
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            white-space: nowrap;
            overflow: hidden;
        }
        
        .sidebar-header h1 {
            font-size: 1.3rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            transition: font-size 0.3s ease, opacity 0.3s ease;
        }
        
        .sidebar-header span {
            font-size: 0.75rem;
            background: var(--primary);
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: 5px;
        }
        
        .sidebar-menu { padding: 20px 0; }
        
        .menu-section {
            padding: 0 20px;
            margin-bottom: 15px;
            transition: padding 0.3s ease;
        }
        
        .menu-section-title {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.4);
            margin-bottom: 10px;
            transition: all 0.3s ease;
            white-space: nowrap;
            overflow: hidden;
        }
        
        .menu-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 2px 10px;
            white-space: nowrap;
            overflow: hidden;
        }
        
        .menu-item:hover, .menu-item.active {
            background: rgba(99,102,241,0.2);
            color: white;
        }
        
        .menu-item.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }
        
        .menu-item i {
            width: 20px;
            min-width: 20px;
            margin-right: 12px;
            text-align: center;
            transition: margin 0.3s ease;
        }
        
        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 30px;
            min-height: calc(100vh - 70px);
            transition: margin-left 0.3s ease;
        }

        .sidebar.collapsed ~ .main-content {
            margin-left: 70px;
        }
        
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 15px 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
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
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
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
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
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
            background: linear-gradient(135deg, rgba(99,102,241,0.1) 0%, rgba(139,92,246,0.1) 100%);
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
        
        .stat-icon.blue { background: rgba(99,102,241,0.1); color: #6366F1; }
        .stat-icon.green { background: rgba(16,185,129,0.1); color: #10B981; }
        .stat-icon.purple { background: rgba(139,92,246,0.1); color: #8B5CF6; }
        .stat-icon.orange { background: rgba(245,158,11,0.1); color: #F59E0B; }
        
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
        
        .table th, .table td {
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
        
        .badge-success { background: #D1FAE5; color: #065F46; }
        .badge-warning { background: #FEF3C7; color: #92400E; }
        .badge-danger { background: #FEE2E2; color: #991B1B; }
        .badge-info { background: #DBEAFE; color: #1E40AF; }
        
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
            transition: all 0.3s ease;
            text-decoration: none;
            font-family: inherit;
        }
        
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-dark); transform: translateY(-2px); }
        .btn-success { background: var(--success); color: white; }
        .btn-danger { background: var(--danger); color: white; }
        .btn-sm { padding: 6px 12px; font-size: 0.8rem; }
        
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .alert-success { background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0; }
        .alert-error { background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; }
        .alert-warning { background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: var(--dark); }
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #E5E7EB;
            border-radius: 10px;
            font-size: 1rem;
            font-family: inherit;
            transition: all 0.3s ease;
        }
        .form-control:focus { outline: none; border-color: var(--primary); }
        
        /* Sidebar Toggle Button in Navbar */
        .sidebar-toggle-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.1rem;
        }
        .sidebar-toggle-btn:hover {
            background: rgba(255,153,51,0.2);
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
            background: rgba(0,0,0,0.5);
            z-index: 99;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .sidebar-overlay.active { display: block; opacity: 1; }
        
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
            margin-left: 260px;
            transition: margin-left 0.3s ease;
        }
        
        .sidebar.collapsed ~ .main-content ~ .main-footer,
        .sidebar-collapsed-footer {
            margin-left: 70px;
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
            color: rgba(255,255,255,0.7);
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
            color: rgba(255,255,255,0.7);
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
            border-top: 1px solid rgba(255,255,255,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .footer-bottom p {
            color: rgba(255,255,255,0.6);
            font-size: 0.9rem;
        }
        
        .social-links {
            display: flex;
            gap: 15px;
        }
        
        .social-links a {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
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
        .mobile-only { display: none; }
        
        @media (max-width: 1024px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .footer-content { grid-template-columns: 1fr 1fr; }
        }
        
        @media (max-width: 768px) {
            .mobile-only { display: block; }
            /* Navbar mobile */
            .main-navbar {
                padding: 10px 15px;
                gap: 10px;
                min-height: 70px; /* Match sidebar top offset */
            }
            .main-navbar .logo {
                font-size: 1.2rem;
                white-space: nowrap;
            }
            .main-navbar .nav-links {
                display: none;
            }
            
            /* SHOW User Info on Mobile */
            .nav-user-info { 
                display: flex !important; /* Force visible */
                flex-direction: column;
                align-items: flex-end; /* Right align on mobile */
                margin-right: 5px;
            }
            .nav-user-name {
                font-size: 0.85rem;
                max-width: 100px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .nav-user-role-badge {
                font-size: 0.6rem;
                padding: 1px 6px;
            }
            
            .user-profile-btn {
                padding: 4px 8px;
                gap: 8px;
            }
            .nav-user-avatar {
                width: 28px;
                height: 28px;
                font-size: 0.75rem;
            }
            
            /* Sidebar: hidden by default, slide-in overlay on mobile */
            .sidebar {
                width: 260px;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 999; /* Below navbar (1000) so toggle button stays visible */
                display: flex;
                flex-direction: column;
                box-shadow: 4px 0 15px rgba(0,0,0,0.1);
            }
            .sidebar.collapsed {
                width: 260px;
                transform: translateX(-100%);
            }
            
            /* FORCE TEXT VISIBILITY ON MOBILE */
            .sidebar.collapsed .sidebar-header h1 { 
                font-size: 1.3rem !important; 
                opacity: 1 !important; 
                display: block !important;
            }
            .sidebar.collapsed .sidebar-header { 
                padding: 20px !important; 
                text-align: left !important; 
            }
            .sidebar.collapsed .sidebar-header span { 
                display: inline-block !important; 
            }
            .sidebar.collapsed .menu-section { 
                padding: 0 20px !important; 
            }
            .sidebar.collapsed .menu-section-title { 
                font-size: 0.7rem !important; 
                opacity: 1 !important; 
                height: auto !important; 
                margin-bottom: 10px !important; 
                display: block !important;
            }
            .sidebar.collapsed .menu-item { 
                justify-content: flex-start !important; 
                padding: 12px 20px !important; 
                margin: 2px 10px !important; 
            }
            .sidebar.collapsed .menu-item i { 
                margin-right: 12px !important; 
                font-size: 1rem !important; 
            }
            .sidebar.collapsed .menu-item span {
                display: inline !important;
            }
            
            .sidebar.mobile-open {
                transform: translateX(0);
                box-shadow: 4px 0 20px rgba(0,0,0,0.5);
            }
            
            /* Sidebar overlay */
            .sidebar-overlay {
                top: 0;
                z-index: 998; /* Below sidebar */
                background: rgba(0,0,0,0.6);
                backdrop-filter: blur(3px);
            }

            /* Navbar Profile - Ensure visible */
            .user-profile-dropdown {
                display: block !important;
            }
            .nav-user-info { 
                display: none; /* Hide text, keep avatar */ 
            }
            .user-profile-btn {
                padding: 6px 12px;
                background: rgba(255,255,255,0.1);
                border: 1px solid rgba(255,255,255,0.2);
            }
            .user-profile-btn:hover {
                background: rgba(255,255,255,0.2);
            }
            .user-profile-btn i {
                display: block !important; /* Show caret */
                font-size: 0.7rem;
                margin-left: 5px;
            }

            .main-content { 
                margin-left: 0 !important; 
                padding: 20px 15px;
            }
            
            .main-footer {
                margin-left: 0 !important;
            }
            
            /* Top bar */
            .top-bar {
                flex-direction: column;
                gap: 10px;
                padding: 15px;
                text-align: center;
            }
            .page-title { font-size: 1.2rem; }
            .user-menu { width: 100%; justify-content: center; }
            .user-info { text-align: center; }
            
            /* Stats / Cards */
            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .stat-card { padding: 18px 14px; }
            .stat-value { font-size: 1.5rem; }
            .stat-icon { width: 42px; height: 42px; font-size: 1.1rem; }
            
            .card { padding: 18px 14px; border-radius: 12px; }
            .card-header { flex-direction: column; gap: 10px; align-items: flex-start; }
            
            .btn { padding: 10px 16px; font-size: 0.85rem; }
            
            /* Tables */
            .table th, .table td { padding: 10px 8px; font-size: 0.85rem; }
            
            /* Dashboard 2-col grid → 1 col */
            div[style*="grid-template-columns: 1fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
            
            /* Footer */
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
        }
        
        @media (max-width: 480px) {
            .main-navbar { padding: 8px 3%; }
            .main-navbar .logo { font-size: 1.2rem; gap: 8px; }
            .main-navbar .logo i { font-size: 1.3rem; }
            .sidebar-toggle-btn { width: 36px; height: 36px; font-size: 1rem; }
            
            .stats-grid { grid-template-columns: 1fr; }
            .top-bar { padding: 12px; }
            .page-title { font-size: 1.05rem; }
            .logout-btn-top { padding: 6px 12px; font-size: 0.85rem; }
            
            .stat-card { padding: 15px 12px; }
            .stat-value { font-size: 1.3rem; }
            .stat-label { font-size: 0.8rem; }
            
            .card { padding: 15px 12px; }
            .card-title { font-size: 0.95rem; }
            
            .table th, .table td { padding: 8px 6px; font-size: 0.8rem; }
            .badge { font-size: 0.7rem; padding: 3px 8px; }
            
            .main-footer { 
                padding: 30px 15px; 
                width: 100%;
                margin-left: 0;
            }
            .social-links a { width: 35px; height: 35px; }
        }
        
        @media (max-width: 360px) {
            .main-content { padding: 15px 10px; }
            .top-bar { padding: 10px; }
            .page-title { font-size: 1rem; }
            .stat-card { padding: 12px 10px; }
            .card { padding: 12px 10px; }
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
                            <?= strtoupper(substr($currentUser['name'] ?? 'A', 0, 1)) ?>
                        </div>
                        <div class="nav-user-info">
                            <span class="nav-user-name"><?= htmlspecialchars($currentUser['name'] ?? 'Admin') ?></span>
                            <span class="nav-user-role-badge" style="background: <?= $roleDisplay['color'] ?>20; color: <?= $roleDisplay['color'] ?>;">
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
                            <i class="fas fa-user-circle"></i>
                            <span>My Profile</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <form action="/logout" method="POST" style="margin: 0;">
                            <?= App\Core\Auth::csrfField() ?>
                            <button type="submit" class="dropdown-item logout-btn" style="width: 100%; border: none; background: none; cursor: pointer; font-family: inherit;">
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
        <aside class="sidebar collapsed" id="sidebar">
            <div class="sidebar-header">
                <h1><i class="fas fa-om"></i> Sanskar AI <span>Admin</span></h1>
            </div>
            <nav class="sidebar-menu">
                <div class="menu-section">
                    <div class="menu-section-title">Main</div>
                    <a href="/admin/dashboard" class="menu-item <?= strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') !== false ? 'active' : '' ?>">
                        <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
                    </a>
                </div>
                <div class="menu-section">
                    <div class="menu-section-title">Management</div>
                    <a href="/admin/users" class="menu-item <?= strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false ? 'active' : '' ?>">
                        <i class="fas fa-users"></i> <span>Users</span>
                    </a>
                    <a href="/admin/pandits/pending" class="menu-item <?= strpos($_SERVER['REQUEST_URI'], '/admin/pandits') !== false ? 'active' : '' ?>">
                        <i class="fas fa-user-check"></i> <span>Pandit Approval</span>
                    </a>
                    <a href="/admin/rituals" class="menu-item <?= strpos($_SERVER['REQUEST_URI'], '/admin/rituals') !== false ? 'active' : '' ?>">
                        <i class="fas fa-pray"></i> <span>Rituals</span>
                    </a>
                </div>
                <div class="menu-section">
                    <div class="menu-section-title">System</div>
                    <a href="/admin/ai-logs" class="menu-item <?= strpos($_SERVER['REQUEST_URI'], '/admin/ai-logs') !== false ? 'active' : '' ?>">
                        <i class="fas fa-robot"></i> <span>AI Logs</span>
                    </a>
                    <a href="/admin/create-admin" class="menu-item <?= strpos($_SERVER['REQUEST_URI'], '/admin/create-admin') !== false ? 'active' : '' ?>">
                        <i class="fas fa-user-shield"></i> <span>Create Admin</span>
                    </a>
                </div>
                <div class="menu-section">
                    <div class="menu-section-title">Account</div>
                    <a href="/admin/profile" class="menu-item <?= strpos($_SERVER['REQUEST_URI'], '/admin/profile') !== false ? 'active' : '' ?>">
                        <i class="fas fa-user-circle"></i> <span>My Profile</span>
                    </a>
                </div>
            </nav>
            
            <!-- Mobile Logout Button (Visible only on mobile) -->
            <div class="sidebar-footer mobile-only" style="padding: 20px; margin-top: auto; border-top: 1px solid rgba(255,255,255,0.1);">
                <a href="/admin/profile" class="btn btn-outline-light mb-3" style="width: 100%; display: flex; justify-content: center; align-items: center; gap: 10px; text-decoration: none; margin-bottom: 15px;">
                    <i class="fas fa-user-circle"></i> My Profile
                </a>
                <form action="/logout" method="POST" style="margin: 0;">
                    <?= \App\Core\Auth::csrfField() ?>
                    <button type="submit" class="btn btn-danger" style="width: 100%; justify-content: center;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </aside>
        
        <main class="main-content">
            <div class="top-bar">
                <h1 class="page-title"><?= htmlspecialchars($title ?? 'Dashboard') ?></h1>

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
                <p>Preserving the sacred traditions of Sanatan Dharma through technology. Your trusted guide for Hindu rituals, ceremonies, and spiritual wisdom.</p>
            </div>
            <div class="footer-links">
                <h4>Quick Links</h4>
                <a href="/">Home</a>
                <a href="/admin/dashboard">Dashboard</a>
                <a href="/admin/users">Users</a>
                <a href="/admin/rituals">Rituals</a>
            </div>
            <div class="footer-links">
                <h4>Management</h4>
                <a href="/admin/pandits/pending">Pandit Approval</a>
                <a href="/admin/ai-logs">AI Logs</a>
            </div>
            <div class="footer-links">
                <h4>Contact</h4>
                <a href="mailto:info@sanskarai.com"><i class="fas fa-envelope"></i> info@sanskarai.com</a>
                <a href="#"><i class="fas fa-phone"></i> +91 98765 43210</a>
                <a href="#"><i class="fas fa-map-marker-alt"></i> Mumbai, India</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2024 Sanskar AI. All rights reserved. Made with <i class="fas fa-heart" style="color: var(--saffron);"></i> for Sanatan Dharma</p>
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

        function updateFooterMargin() {
            if (!mainFooter) return;
            if (isMobile()) {
                mainFooter.style.marginLeft = '0';
            } else if (sidebar.classList.contains('collapsed')) {
                mainFooter.style.marginLeft = '70px';
            } else {
                mainFooter.style.marginLeft = '260px';
            }
        }

        function openMobileSidebar() {
            sidebar.classList.add('mobile-open');
            sidebarOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeMobileSidebar() {
            sidebar.classList.remove('mobile-open');
            sidebarOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        function toggleSidebar() {
            if (isMobile()) {
                if (sidebar.classList.contains('mobile-open')) {
                    closeMobileSidebar();
                } else {
                    openMobileSidebar();
                }
            } else {
                sidebar.classList.toggle('collapsed');
                updateFooterMargin();
            }
            // Toggle icon
            const icon = sidebarToggle.querySelector('i');
            if (isMobile()) {
                icon.className = sidebar.classList.contains('mobile-open') ? 'fas fa-times' : 'fas fa-bars';
            } else {
                icon.className = sidebar.classList.contains('collapsed') ? 'fas fa-bars' : 'fas fa-times';
            }
        }

        sidebarToggle.addEventListener('click', toggleSidebar);
        sidebarOverlay.addEventListener('click', () => {
            closeMobileSidebar();
            sidebarToggle.querySelector('i').className = 'fas fa-bars';
        });

        // Close sidebar on mobile when clicking menu items
        sidebar.querySelectorAll('.menu-item').forEach(item => {
            item.addEventListener('click', () => {
                if (isMobile()) {
                    closeMobileSidebar();
                    sidebarToggle.querySelector('i').className = 'fas fa-bars';
                }
            });
        });

        // Handle window resize: reset states
        window.addEventListener('resize', () => {
            if (!isMobile()) {
                sidebar.classList.remove('mobile-open');
                sidebarOverlay.classList.remove('active');
                document.body.style.overflow = '';
                sidebarToggle.querySelector('i').className = sidebar.classList.contains('collapsed') ? 'fas fa-bars' : 'fas fa-times';
            } else {
                sidebarToggle.querySelector('i').className = 'fas fa-bars';
            }
            updateFooterMargin();
        });

        // Initial footer margin
        updateFooterMargin();
        
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
</body>
</html>
