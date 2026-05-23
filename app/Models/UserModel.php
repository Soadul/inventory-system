<?php
namespace App\Models;

use App\Core\BaseModel;
use PDO;

class UserModel extends BaseModel {
    protected $table = 'users';

    /**
     * Authenticate a user by username and password
     * @return array|false User details on success, false on failure
     */
    public function authenticate($username, $password) {
        $sql = "SELECT * FROM {$this->table} WHERE username = :username LIMIT 1";
        $user = $this->query($sql, [':username' => $username])->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    /**
     * Retrieve all salesmen users
     */
    public function getSalesmen() {
        return $this->where(['role' => 'salesman'], 'full_name ASC');
    }
}
