<?php
namespace App\Models;

use App\Core\BaseModel;
use PDO;
use Exception;

class SaleModel extends BaseModel {
    protected $table = 'sales';

    /**
     * Complete a Sale Transaction (Session-secure, rollback capability)
     * @param int $salesmanId
     * @param array $items Array of ['product_id' => X, 'quantity' => Y]
     * @param float $paidAmount
     * @param string $paymentMethod
     */
    public function createSale($salesmanId, $items, $paidAmount, $paymentMethod) {
        $this->db->beginTransaction();

        try {
            // 1. Calculate totals
            $totalAmount = 0.0;
            $itemsToInsert = [];

            foreach ($items as $item) {
                $productId = (int)$item['product_id'];
                $quantity = (int)$item['quantity'];

                if ($quantity <= 0) continue;

                // Retrieve product specs
                $pSql = "SELECT price, stock_quantity, name FROM products WHERE id = :id";
                $product = $this->query($pSql, [':id' => $productId])->fetch(PDO::FETCH_ASSOC);

                if (!$product) {
                    throw new Exception("Product ID {$productId} does not exist.");
                }

                if ($product['stock_quantity'] < $quantity) {
                    throw new Exception("Insufficient stock for product '{$product['name']}'. Requested: {$quantity}, Available: {$product['stock_quantity']}");
                }

                $subtotal = $product['price'] * $quantity;
                $totalAmount += $subtotal;

                $itemsToInsert[] = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_price' => $product['price'],
                    'subtotal' => $subtotal
                ];
            }

            if (empty($itemsToInsert)) {
                throw new Exception("No valid items selected for transaction.");
            }

            // Calculate dues
            $dueAmount = max(0.0, $totalAmount - $paidAmount);

            // 2. Insert Parent Sale Invoice
            $saleId = $this->insert([
                'salesman_id' => $salesmanId,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'payment_method' => $paymentMethod
            ]);

            // 3. Insert Sale Line Items & Deduct Product Stocks
            $stmtItem = $this->db->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)");
            $stmtDeduct = $this->db->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");

            foreach ($itemsToInsert as $item) {
                $stmtItem->execute([
                    $saleId,
                    $item['product_id'],
                    $item['quantity'],
                    $item['unit_price'],
                    $item['subtotal']
                ]);

                $stmtDeduct->execute([
                    $item['quantity'],
                    $item['product_id']
                ]);
            }

            $this->db->commit();
            return $saleId;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Fetch all sales with Salesman details
     */
    public function getSalesWithStaff($salesmanId = null) {
        $sql = "SELECT s.*, u.full_name as salesman_name 
                FROM sales s 
                INNER JOIN users u ON s.salesman_id = u.id";
        
        $params = [];
        if ($salesmanId !== null) {
            $sql .= " WHERE s.salesman_id = :salesman_id";
            $params[':salesman_id'] = $salesmanId;
        }
        
        $sql .= " ORDER BY s.created_at DESC";
        return $this->query($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve detailed items for a particular invoice
     */
    public function getSaleDetails($saleId) {
        // Fetch invoice master
        $sqlMaster = "SELECT s.*, u.full_name as salesman_name 
                      FROM sales s 
                      INNER JOIN users u ON s.salesman_id = u.id 
                      WHERE s.id = :id LIMIT 1";
        $sale = $this->query($sqlMaster, [':id' => $saleId])->fetch(PDO::FETCH_ASSOC);

        if (!$sale) return null;

        // Fetch invoice child items
        $sqlItems = "SELECT si.*, p.name as product_name, p.sku as product_sku 
                     FROM sale_items si 
                     INNER JOIN products p ON si.product_id = p.id 
                     WHERE si.sale_id = :sale_id";
        $sale['items'] = $this->query($sqlItems, [':sale_id' => $saleId])->fetchAll(PDO::FETCH_ASSOC);

        return $sale;
    }

    /**
     * Get dynamic aggregate statistics for dashboard reports
     */
    public function getFinancialAggregates($salesmanId = null) {
        $sql = "SELECT 
                    COUNT(id) as total_invoices,
                    COALESCE(SUM(total_amount), 0) as total_sales,
                    COALESCE(SUM(paid_amount), 0) as total_collected,
                    COALESCE(SUM(due_amount), 0) as total_dues 
                FROM sales";
        
        $params = [];
        if ($salesmanId !== null) {
            $sql .= " WHERE salesman_id = :salesman_id";
            $params[':salesman_id'] = $salesmanId;
        }

        return $this->query($sql, $params)->fetch(PDO::FETCH_ASSOC);
    }
}
