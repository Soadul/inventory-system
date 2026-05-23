<?php
namespace App\Config;

use PDO;
use PDOException;

class Database {
    private static $connection = null;

    /**
     * Retrieve static PDO database connection
     */
    public static function getConnection() {
        if (self::$connection === null) {
            try {
                $dbPath = APP_PATH . '/database/inventory.sqlite';
                
                // Ensure directory exists
                $dbDir = dirname($dbPath);
                if (!file_exists($dbDir)) {
                    mkdir($dbDir, 0777, true);
                }

                // Explicitly make the database directory writable
                @chmod($dbDir, 0777);

                // Self-healing database initialization
                if (!file_exists($dbPath)) {
                    require_once APP_PATH . '/database/setup_db.php';
                }

                // Explicitly make the database file writable
                if (file_exists($dbPath)) {
                    @chmod($dbPath, 0777);
                }

                // Connect to SQLite Database
                self::$connection = new PDO("sqlite:" . $dbPath);
                
                // Set Attributes for Security and Performance
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                
                // Enable Foreign Key Constraints in SQLite
                self::$connection->exec('PRAGMA foreign_keys = ON;');
                
            } catch (PDOException $e) {
                die("SQLite Database Connection Failure: " . $e->getMessage());
            }
        }
        return self::$connection;
    }
}
