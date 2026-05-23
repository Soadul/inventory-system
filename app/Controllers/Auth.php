<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Models\UserModel;

class Auth extends BaseController {

    /**
     * Display Login view page
     */
    public function login() {
        // Redirect to dashboard if session already active
        if (isset($_SESSION['user_id'])) {
            $this->redirect('dashboard');
        }

        // Render clean un-headered login panel
        // Use custom rendering logic so it doesn't merge navbar layout!
        $viewFile = APP_PATH . '/Views/auth/login.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            echo "Login view missing.";
        }
    }

    /**
     * Handle login submission
     */
    public function attemptLogin() {
        $username = $this->input('username');
        $password = $this->input('password');

        if (empty($username) || empty($password)) {
            $_SESSION['login_error'] = "Username and password are required fields.";
            $this->redirect('login');
        }

        $userModel = new UserModel();
        $user = $userModel->authenticate($username, $password);

        if ($user) {
            // Establish session keys
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['username'];
            $_SESSION['user_fullname'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];

            // Clear past error flags
            unset($_SESSION['login_error']);
            
            $this->redirect('dashboard');
        } else {
            $_SESSION['login_error'] = "Invalid username or password credentials.";
            $this->redirect('login');
        }
    }

    /**
     * Terminate active session
     */
    public function logout() {
        // Destroy keys
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        
        $this->redirect('login');
    }
}
