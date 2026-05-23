<?php
// Session-based routing helpers
$currentUri = trim($_SERVER['REQUEST_URI'], '/');
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
if ($scriptName !== '/' && strpos($currentUri, trim($scriptName, '/')) === 0) {
    $currentUri = substr($currentUri, strlen(trim($scriptName, '/')));
}
$currentUri = trim($currentUri, '/');

// Role definitions
$userRole = $_SESSION['user_role'] ?? '';
$fullName = $_SESSION['user_fullname'] ?? 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Inventory System' ?> | Command Center</title>
    <!-- Custom Style Sheet -->
    <link rel="stylesheet" href="<?= $this->getUrl('css/style.css') ?>">
</head>
<body>

    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <svg class="brand-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <rect x="3" y="3" width="7" height="9" rx="1"/>
                <rect x="14" y="3" width="7" height="5" rx="1"/>
                <rect x="14" y="12" width="7" height="9" rx="1"/>
                <rect x="3" y="16" width="7" height="5" rx="1"/>
            </svg>
            <span>OS-Inventory</span>
        </div>
        
        <!-- Profile Panel -->
        <div class="sidebar-profile">
            <div class="profile-name"><?= htmlspecialchars($fullName) ?></div>
            <div class="profile-role">
                <?php
                if ($userRole === 'super_admin') echo 'Super Admin';
                elseif ($userRole === 'admin') echo 'Administrator';
                elseif ($userRole === 'salesman') echo 'Sales Representative';
                else echo 'Guest';
                ?>
            </div>
        </div>

        <!-- Menu Links -->
        <ul class="sidebar-menu">
            <li class="menu-item <?= ($currentUri === '' || $currentUri === 'dashboard') ? 'active' : '' ?>">
                <a href="<?= $this->getUrl('dashboard') ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="9"/>
                        <rect x="14" y="3" width="7" height="5"/>
                        <rect x="14" y="12" width="7" height="9"/>
                        <rect x="3" y="16" width="7" height="5"/>
                    </svg>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <li class="menu-item <?= (strpos($currentUri, 'products') === 0) ? 'active' : '' ?>">
                <a href="<?= $this->getUrl('products') ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                    <span>Products Inventory</span>
                </a>
            </li>
            
            <li class="menu-item <?= ($currentUri === 'sales' || $currentUri === 'sales/create' || strpos($currentUri, 'sales/view') === 0) ? 'active' : '' ?>">
                <a href="<?= $this->getUrl('sales') ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="9" cy="21" r="1"/>
                        <circle cx="20" cy="21" r="1"/>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                    </svg>
                    <span>Daily Sales Records</span>
                </a>
            </li>

            <li class="menu-item <?= (strpos($currentUri, 'sales/collections') === 0) ? 'active' : '' ?>">
                <a href="<?= $this->getUrl('sales/collections') ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="1" x2="12" y2="23"/>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                    <span>Due Collections</span>
                </a>
            </li>

            <li class="menu-item <?= (strpos($currentUri, 'damages') === 0) ? 'active' : '' ?>">
                <a href="<?= $this->getUrl('damages') ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/>
                    </svg>
                    <span>Damage & Waste</span>
                </a>
            </li>

            <?php if ($userRole === 'super_admin'): ?>
            <li class="menu-item <?= (strpos($currentUri, 'users') === 0) ? 'active' : '' ?>">
                <a href="<?= $this->getUrl('users') ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <span>Staff Accounts</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>

        <!-- Logout Action -->
        <div class="sidebar-footer">
            <a href="<?= $this->getUrl('logout') ?>" class="logout-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>
                </svg>
                <span>Terminate Session</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Workspace Container -->
    <main class="main-wrapper">
        
        <!-- Header Panel with alert flash triggers -->
        <div class="header-panel">
            <h1 class="page-title"><?= htmlspecialchars($title ?? 'Inventory Panel') ?></h1>
            <div class="header-datetime" style="font-size:0.9rem; color:var(--text-secondary);">
                📅 Current Audit Time: <span style="color:var(--accent); font-weight:600;"><?= date('Y-m-d') ?></span>
            </div>
        </div>

        <!-- Render Session Alert boxes for notifications -->
        <?php if (isset($_SESSION['form_success'])): ?>
            <div class="alert alert-success">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                <span><?= htmlspecialchars($_SESSION['form_success']) ?></span>
                <?php unset($_SESSION['form_success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['form_error'])): ?>
            <div class="alert alert-danger">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span><?= htmlspecialchars($_SESSION['form_error']) ?></span>
                <?php unset($_SESSION['form_error']); ?>
            </div>
        <?php endif; ?>
