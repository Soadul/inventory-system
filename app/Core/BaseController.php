<?php
namespace App\Core;

abstract class BaseController {
    
    /**
     * Check if user is logged in. Redirect to login otherwise.
     */
    protected function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('login');
        }
    }

    /**
     * Restrict views based on user role
     * @param array|string $allowedRoles E.g. 'super_admin' or ['super_admin', 'admin']
     */
    protected function requireRole($allowedRoles) {
        $this->requireAuth();
        $userRole = $_SESSION['user_role'] ?? '';

        if (is_array($allowedRoles)) {
            if (!in_array($userRole, $allowedRoles)) {
                $this->unauthorized();
            }
        } else {
            if ($userRole !== $allowedRoles) {
                $this->unauthorized();
            }
        }
    }

    /**
     * Render the view merged with layout headers/footers
     */
    protected function render($viewName, $data = []) {
        // Extract data variables to be directly accessible inside views
        extract($data);

        // Include Header layout
        $headerFile = APP_PATH . '/Views/layout/header.php';
        if (file_exists($headerFile)) {
            require_once $headerFile;
        }

        // Include Main view file
        $viewFile = APP_PATH . '/Views/' . $viewName . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            echo "<p style='color:red;'>View file not found: app/Views/{$viewName}.php</p>";
        }

        // Include Footer layout
        $footerFile = APP_PATH . '/Views/layout/footer.php';
        if (file_exists($footerFile)) {
            require_once $footerFile;
        }
    }

    /**
     * Redirect request to a system page
     */
    protected function redirect($url) {
        $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
        $baseUrl = ($baseUrl === '/' ? '' : $baseUrl) . '/';
        header("Location: " . $baseUrl . ltrim($url, '/'));
        exit;
    }

    /**
     * Sanitize user input variables
     */
    protected function input($key = null) {
        if ($key === null) {
            $sanitized = [];
            foreach ($_POST as $k => $v) {
                $sanitized[$k] = is_array($v) ? $v : htmlspecialchars(trim($v), ENT_QUOTES, 'UTF-8');
            }
            return $sanitized;
        }
        
        if (!isset($_POST[$key])) {
            return null;
        }

        return is_array($_POST[$key]) ? $_POST[$key] : htmlspecialchars(trim($_POST[$key]), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Show access restricted message
     */
    protected function unauthorized() {
        http_response_code(403);
        echo "<!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <title>403 - Access Forbidden</title>
            <style>
                body { background-color: #0b0c16; color: #fff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
                .container { text-align: center; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); padding: 40px; border-radius: 12px; backdrop-filter: blur(8px); }
                h1 { font-size: 5rem; margin: 0; color: #f43f5e; text-shadow: 0 0 10px rgba(244,63,94,0.3); }
                h2 { font-size: 1.5rem; margin: 10px 0 20px; color: #9ca3af; }
                a { color: #8b5cf6; text-decoration: none; font-weight: 600; }
                a:hover { text-decoration: underline; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h1>403</h1>
                <h2>Access Forbidden - Insufficient System Clearance level.</h2>
                <p><a href='" . $this->getUrl('dashboard') . "'>Return to Authorized Dashboard</a></p>
            </div>
        </body>
        </html>";
        exit;
    }

    /**
     * Generate system URL
     */
    protected function getUrl($path = '') {
        $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
        return ($baseUrl === '/' ? '' : $baseUrl) . '/' . ltrim($path, '/');
    }
}
