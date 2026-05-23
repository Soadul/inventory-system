<?php
namespace App\Core;

use App\Config\Database;
use PDO;
use PDOException;

abstract class BaseModel {
    protected $db;
    protected $table = '';

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Run a safe prepared SQL query
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            die("Database Query Failure: " . $e->getMessage() . "<br>SQL: " . $sql);
        }
    }

    /**
     * Fetch all records from the table
     */
    public function findAll($orderBy = null) {
        $sql = "SELECT * FROM {$this->table}";
        if ($orderBy) {
            $sql .= " ORDER BY " . $orderBy;
        }
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find a single record by its Primary Key
     */
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        return $this->query($sql, [':id' => $id])->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Insert a record into the table
     * @return int Last inserted record ID
     */
    public function insert($data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        
        $params = [];
        foreach ($data as $key => $val) {
            $params[':' . $key] = $val;
        }

        $this->query($sql, $params);
        return $this->db->lastInsertId();
    }

    /**
     * Update a record by ID
     */
    public function update($id, $data) {
        $updateParts = [];
        $params = [':id' => $id];

        foreach ($data as $key => $val) {
            $updateParts[] = "{$key} = :{$key}";
            $params[':' . $key] = $val;
        }

        $setString = implode(', ', $updateParts);
        $sql = "UPDATE {$this->table} SET {$setString} WHERE id = :id";

        $this->query($sql, $params);
        return true;
    }

    /**
     * Delete a record by ID
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $this->query($sql, [':id' => $id]);
        return true;
    }

    /**
     * Find records matching specific column conditions
     */
    public function where($conditions = [], $orderBy = null) {
        $whereParts = [];
        $params = [];

        foreach ($conditions as $col => $val) {
            $whereParts[] = "{$col} = :{$col}";
            $params[':' . $col] = $val;
        }

        $sql = "SELECT * FROM {$this->table}";
        if (!empty($whereParts)) {
            $sql .= " WHERE " . implode(' AND ', $whereParts);
        }

        if ($orderBy) {
            $sql .= " ORDER BY " . $orderBy;
        }

        return $this->query($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
}
