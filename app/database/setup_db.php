<?php
/**
 * ----------------------------------------------------
 * SQLite Database Setup & Initial Seeding Script
 * ----------------------------------------------------
 */

$dbPath = __DIR__ . '/inventory.sqlite';
$isNew = !file_exists($dbPath);

// Explicitly make the database directory writable
@chmod(__DIR__, 0777);

try {
    $db = new PDO("sqlite:" . $dbPath);
    // Explicitly make the database file writable
    @chmod($dbPath, 0777);
    
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec('PRAGMA foreign_keys = ON;');

    if ($isNew || isset($_GET['reseed'])) {
        // Drop Tables if they exist (Reset Mode)
        $db->exec("DROP TABLE IF EXISTS collections;");
        $db->exec("DROP TABLE IF EXISTS damages;");
        $db->exec("DROP TABLE IF EXISTS sale_items;");
        $db->exec("DROP TABLE IF EXISTS sales;");
        $db->exec("DROP TABLE IF EXISTS products;");
        $db->exec("DROP TABLE IF EXISTS categories;");
        $db->exec("DROP TABLE IF EXISTS users;");

        // 1. Users Table
        $db->exec("CREATE TABLE users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            full_name TEXT NOT NULL,
            role TEXT NOT NULL CHECK(role IN ('super_admin', 'admin', 'salesman')),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );");

        // 2. Categories Table
        $db->exec("CREATE TABLE categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            description TEXT
        );");

        // 3. Products Table
        $db->exec("CREATE TABLE products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            category_id INTEGER NOT NULL,
            name TEXT NOT NULL,
            sku TEXT UNIQUE NOT NULL,
            price REAL NOT NULL,
            cost REAL NOT NULL,
            stock_quantity INTEGER NOT NULL DEFAULT 0,
            min_stock_alert INTEGER NOT NULL DEFAULT 5,
            description TEXT,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
        );");

        // 4. Sales Table
        $db->exec("CREATE TABLE sales (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            salesman_id INTEGER NOT NULL,
            sale_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            total_amount REAL NOT NULL,
            paid_amount REAL NOT NULL,
            due_amount REAL NOT NULL,
            payment_method TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (salesman_id) REFERENCES users(id) ON DELETE RESTRICT
        );");

        // 5. Sale Items Table
        $db->exec("CREATE TABLE sale_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            sale_id INTEGER NOT NULL,
            product_id INTEGER NOT NULL,
            quantity INTEGER NOT NULL,
            unit_price REAL NOT NULL,
            subtotal REAL NOT NULL,
            FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
        );");

        // 6. Damages (Waste) Table
        $db->exec("CREATE TABLE damages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            recorded_by_id INTEGER NOT NULL,
            product_id INTEGER NOT NULL,
            quantity INTEGER NOT NULL,
            reason TEXT NOT NULL CHECK(reason IN ('Broken', 'Expired', 'Lost', 'Spoiled')),
            record_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (recorded_by_id) REFERENCES users(id) ON DELETE RESTRICT,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        );");

        // 7. Collections Table
        $db->exec("CREATE TABLE collections (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            salesman_id INTEGER NOT NULL,
            customer_name TEXT NOT NULL,
            amount REAL NOT NULL,
            payment_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            notes TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (salesman_id) REFERENCES users(id) ON DELETE RESTRICT
        );");

        // --- SEED INITIAL USERS ---
        $superAdminHash = password_hash('admin123', PASSWORD_BCRYPT);
        $adminHash = password_hash('admin123', PASSWORD_BCRYPT);
        $salesmanHash = password_hash('salesman123', PASSWORD_BCRYPT);

        $stmt = $db->prepare("INSERT INTO users (username, password, full_name, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['superadmin', $superAdminHash, 'Md. Soadul Islam (Super)', 'super_admin']);
        $stmt->execute(['admin', $adminHash, 'BUBT Admin Coordinator', 'admin']);
        $stmt->execute(['salesman', $salesmanHash, 'Rafiqul Islam (Sales Rep)', 'salesman']);

        // --- SEED CATEGORIES ---
        $db->exec("INSERT INTO categories (name, description) VALUES 
            ('Beverages', 'Soft drinks, energy drinks, bottled juices, and water'),
            ('Grains & Staples', 'Rice, wheat flour, lentils, pulse, and oils'),
            ('Dairy & Eggs', 'Milk cartons, butter, cheese, and farm eggs'),
            ('Packaged Foods', 'Biscuits, noodles, chips, spices, and tea leaves')
        ;");

        // --- SEED PRODUCTS ---
        $db->exec("INSERT INTO products (category_id, name, sku, price, cost, stock_quantity, min_stock_alert, description) VALUES 
            (2, 'Premium Miniket Rice 25kg', 'GR-RICE-01', 1850.00, 1600.00, 45, 10, 'High-grade polished long grain white rice bag'),
            (2, 'Soyabean Oil 5L Bottle', 'GR-OIL-05', 820.00, 740.00, 30, 8, 'Fortified vitamin A pure soyabean edible oil'),
            (1, 'Energy Drink 250ml', 'BV-ENER-01', 35.00, 25.00, 120, 20, 'Carbonated taurine high caffeine energy drink'),
            (3, 'Fresh Milk Pasteurized 1L', 'DY-MILK-01', 90.00, 75.00, 4, 15, 'Pasteurized fresh milk pack (short shelf life)'),
            (4, 'Instant Noodles 8-Pack', 'PK-NOOD-08', 160.00, 130.00, 60, 12, 'Spicy masala instant noodles family pack')
        ;");

        // --- SEED SALES ---
        $db->exec("INSERT INTO sales (salesman_id, total_amount, paid_amount, due_amount, payment_method) VALUES 
            (3, 1105.00, 1000.00, 105.00, 'Cash'),
            (3, 1850.00, 1850.00, 0.00, 'bKash')
        ;");

        // --- SEED SALE ITEMS ---
        $db->exec("INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, subtotal) VALUES 
            (1, 3, 10, 35.00, 350.00),
            (1, 2, 1, 820.00, 820.00), -- Total 1170? Adjusting sum to matches sale 1
            (2, 1, 1, 1850.00, 1850.00)
        ;");
        
        // Correcting Sale 1 values (350 + 820 = 1170. Let's update Sale total)
        $db->exec("UPDATE sales SET total_amount = 1170.00, due_amount = 170.00 WHERE id = 1;");

        // --- SEED DAMAGES (WASTE) ---
        $db->exec("INSERT INTO damages (recorded_by_id, product_id, quantity, reason) VALUES 
            (3, 4, 3, 'Expired'),
            (2, 3, 5, 'Broken')
        ;");
        
        // Update product stock based on damages (Deduct from stock)
        $db->exec("UPDATE products SET stock_quantity = stock_quantity - 3 WHERE id = 4;");
        $db->exec("UPDATE products SET stock_quantity = stock_quantity - 5 WHERE id = 3;");

        // --- SEED COLLECTIONS ---
        $db->exec("INSERT INTO collections (salesman_id, customer_name, amount, notes) VALUES 
            (3, 'Al-Amin Grocery Store', 170.00, 'Received due amount for invoice #1')
        ;");

        // Update sales due amount since it was collected!
        $db->exec("UPDATE sales SET paid_amount = paid_amount + 170.00, due_amount = 0.00 WHERE id = 1;");
    }
} catch (PDOException $e) {
    die("Database seeding execution failed: " . $e->getMessage());
}
