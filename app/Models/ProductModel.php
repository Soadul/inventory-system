<?php
namespace App\Models;

use App\Core\BaseModel;
use PDO;

class ProductModel extends BaseModel {
    protected $table = 'products';

    /**
     * Fetch products joined with Category labels
     */
    public function getProductsWithCategories() {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                INNER JOIN categories c ON p.category_id = c.id 
                ORDER BY p.name ASC";
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch products that are running low on stock
     */
    public function getLowStockAlerts() {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                INNER JOIN categories c ON p.category_id = c.id 
                WHERE p.stock_quantity <= p.min_stock_alert 
                ORDER BY p.stock_quantity ASC";
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Adjust product stock level (can be positive for restock or negative for sales/damage)
     */
    public function adjustStock($productId, $qtyChange) {
        $sql = "UPDATE {$this->table} 
                SET stock_quantity = stock_quantity + :qty 
                WHERE id = :id";
        return $this->query($sql, [
            ':qty' => $qtyChange,
            ':id' => $productId
        ]);
    }
}
