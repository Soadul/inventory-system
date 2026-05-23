<?php
namespace App\Models;

use App\Core\BaseModel;
use PDO;
use Exception;

class DamageModel extends BaseModel {
    protected $table = 'damages';

    /**
     * Record a Product Damage (Deducts stock automatically)
     */
    public function recordDamage($recordedById, $productId, $qty, $reason) {
        $this->db->beginTransaction();

        try {
            $qty = (int)$qty;
            if ($qty <= 0) {
                throw new Exception("Damage quantity must be a positive integer.");
            }

            // Retrieve product details
            $pSql = "SELECT name, stock_quantity FROM products WHERE id = :id";
            $product = $this->query($pSql, [':id' => $productId])->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                throw new Exception("Product ID {$productId} does not exist.");
            }

            if ($product['stock_quantity'] < $qty) {
                throw new Exception("Cannot record damage greater than available stock. Available: {$product['stock_quantity']}");
            }

            // 1. Insert Damage Log
            $this->insert([
                'recorded_by_id' => $recordedById,
                'product_id' => $productId,
                'quantity' => $qty,
                'reason' => $reason
            ]);

            // 2. Deduct from Product Stock
            $deductSql = "UPDATE products SET stock_quantity = stock_quantity - :qty WHERE id = :id";
            $this->query($deductSql, [
                ':qty' => $qty,
                ':id' => $productId
            ]);

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Fetch damages with details
     */
    public function getDamagesWithDetails($salesmanId = null) {
        $sql = "SELECT d.*, p.name as product_name, p.sku as product_sku, u.full_name as recorder_name 
                FROM damages d 
                INNER JOIN products p ON d.product_id = p.id 
                INNER JOIN users u ON d.recorded_by_id = u.id";

        $params = [];
        if ($salesmanId !== null) {
            $sql .= " WHERE d.recorded_by_id = :recorder_id";
            $params[':recorder_id'] = $salesmanId;
        }

        $sql .= " ORDER BY d.created_at DESC";
        return $this->query($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
}
