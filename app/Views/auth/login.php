<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clearance Login | OS-Inventory</title>
    <!-- Custom Style Sheet -->
    <link rel="stylesheet" href="<?= dirname($_SERVER['SCRIPT_NAME']) === '/' ? '' : dirname($_SERVER['SCRIPT_NAME']) ?>/css/style.css">
</head>
<body class="login-wrapper">

    <div class="login-card">
        <div class="login-brand">
            <svg class="brand-icon" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <rect x="3" y="3" width="7" height="9" rx="1"/>
                <rect x="14" y="3" width="7" height="5" rx="1"/>
                <rect x="14" y="12" width="7" height="9" rx="1"/>
                <rect x="3" y="16" width="7" height="5" rx="1"/>
            </svg>
            <h2>OS-Inventory Control</h2>
            <p style="color:var(--text-secondary); font-size:0.85rem; margin-top:6px;">Staff Authentication Required</p>
        </div>

        <?php if (isset($_SESSION['login_error'])): ?>
            <div class="alert alert-danger" style="margin-bottom:20px; padding:12px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span style="font-size:0.85rem;"><?= htmlspecialchars($_SESSION['login_error']) ?></span>
                <?php unset($_SESSION['login_error']); ?>
            </div>
        <?php endif; ?>

        <form action="<?= dirname($_SERVER['SCRIPT_NAME']) === '/' ? '' : dirname($_SERVER['SCRIPT_NAME']) ?>/login" method="POST">
            <div class="form-group">
                <label for="username">Username ID</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="e.g. superadmin" required>
            </div>
            
            <div class="form-group">
                <label for="password">Security Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="padding:14px; margin-top:10px;">
                <span>Authenticate Session</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/>
                </svg>
            </button>
        </form>

        <div style="margin-top:30px; text-align:center; font-size:0.8rem; color:var(--text-muted);">
            <p>Demo Logins: <code>superadmin</code>, <code>admin</code>, <code>salesman</code></p>
            <p style="margin-top:4px;">Passwords: <code>admin123</code> (salesman is <code>salesman123</code>)</p>
        </div>
    </div>

</body>
</html>
