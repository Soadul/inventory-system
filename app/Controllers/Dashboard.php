<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Models\SaleModel;
use App\Models\ProductModel;
use App\Models\DamageModel;
use App\Models\CollectionModel;

class Dashboard extends BaseController {

    public function index() {
        $this->requireAuth();

        $role = $_SESSION['user_role'];
        $userId = $_SESSION['user_id'];

        $saleModel = new SaleModel();
        $productModel = new ProductModel();
        $damageModel = new DamageModel();
        $collectionModel = new CollectionModel();

        // 1. Gather Data based on clearances
        if ($role === 'salesman') {
            // Salesman sees ONLY their personal operations
            $stats = $saleModel->getFinancialAggregates($userId);
            
            // Recount collections for salesman
            $collSql = "SELECT COALESCE(SUM(amount), 0) as total_coll FROM collections WHERE salesman_id = :id";
            $collRes = $saleModel->query($collSql, [':id' => $userId])->fetch();
            $stats['total_collected_cash'] = $collRes['total_coll'] ?? 0;
            
            $recentSales = $saleModel->getSalesWithStaff($userId);
            $recentDamages = $damageModel->getDamagesWithDetails($userId);
            $lowStock = []; // Salesman doesn't need low stock warnings
        } else {
            // Admin & Super Admin see the whole enterprise
            $stats = $saleModel->getFinancialAggregates();
            
            // Add total products count
            $prodSql = "SELECT COUNT(id) as cnt, COALESCE(SUM(stock_quantity), 0) as total_stock FROM products";
            $prodRes = $productModel->query($prodSql)->fetch();
            $stats['total_products'] = $prodRes['cnt'] ?? 0;
            $stats['total_stock'] = $prodRes['total_stock'] ?? 0;

            $recentSales = $saleModel->getSalesWithStaff();
            $recentDamages = $damageModel->getDamagesWithDetails();
            $lowStock = $productModel->getLowStockAlerts();
        }

        // Limit recent lists to max 6 elements for elegant styling layout
        $recentSales = array_slice($recentSales, 0, 6);
        $recentDamages = array_slice($recentDamages, 0, 6);

        // Render dashboard view
        $this->render('dashboard', [
            'title' => 'Dashboard Console',
            'stats' => $stats,
            'recentSales' => $recentSales,
            'recentDamages' => $recentDamages,
            'lowStock' => $lowStock
        ]);
    }
}
