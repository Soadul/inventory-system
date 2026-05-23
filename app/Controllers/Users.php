<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Models\UserModel;

class Users extends BaseController {

    /**
     * List all system users
     */
    public function index() {
        $this->requireRole('super_admin');

        $userModel = new UserModel();
        $users = $userModel->findAll('role ASC, full_name ASC');

        $this->render('users/index', [
            'title' => 'System User Management',
            'users' => $users
        ]);
    }

    /**
     * Create user Form page
     */
    public function create() {
        $this->requireRole('super_admin');

        $this->render('users/create', [
            'title' => 'Create New Staff Account'
        ]);
    }

    /**
     * Process new user storage
     */
    public function store() {
        $this->requireRole('super_admin');

        $username = $this->input('username');
        $password = $this->input('password');
        $fullName = $this->input('full_name');
        $role = $this->input('role');

        if (empty($username) || empty($password) || empty($fullName) || empty($role)) {
            $_SESSION['form_error'] = "All fields are required to create a staff account.";
            $this->redirect('users/create');
        }

        $userModel = new UserModel();

        // Check duplicate username
        $duplicate = $userModel->where(['username' => $username]);
        if (!empty($duplicate)) {
            $_SESSION['form_error'] = "The username '{$username}' is already in use.";
            $this->redirect('users/create');
        }

        // Encrypt password securely via bcrypt
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $userModel->insert([
            'username' => strtolower($username),
            'password' => $hashedPassword,
            'full_name' => $fullName,
            'role' => $role
        ]);

        $_SESSION['form_success'] = "Staff account for '{$fullName}' registered successfully.";
        $this->redirect('users');
    }

    /**
     * Delete user from system
     */
    public function delete($id) {
        $this->requireRole('super_admin');

        if ((int)$id === (int)$_SESSION['user_id']) {
            $_SESSION['form_error'] = "Clearence Error: You cannot delete your own account.";
            $this->redirect('users');
        }

        $userModel = new UserModel();
        $userModel->delete($id);

        $_SESSION['form_success'] = "User account terminated successfully.";
        $this->redirect('users');
    }
}
