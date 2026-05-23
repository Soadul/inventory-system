<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Models\DamageModel;
use App\Models\ProductModel;
use Exception;

class Damages extends BaseController {

    /**
     * List recorded stock damages
     */
    public function index() {
        $this->requireAuth();

        $damageModel = new DamageModel();
        $role = $_SESSION['user_role'];
        $userId = $_SESSION['user_id'];

        $damages = ($role === 'salesman') ? $damageModel->getDamagesWithDetails($userId) : $damageModel->getDamagesWithDetails();

        $this->render('damages/index', [
            'title' => 'Damage & Waste Logs',
            'damages' => $damages
        ]);
    }

    /**
     * Display Create Damage record Form
     */
    public function create() {
        $this->requireAuth();

        $productModel = new ProductModel();
        // Fetch all products to select from
        $products = $productModel->findAll('name ASC');

        $this->render('damages/create', [
            'title' => 'Record Stock Damage (Waste)',
            'products' => $products
        ]);
    }

    /**
     * Store Recorded Damage and deduct stock levels
     */
    public function store() {
        $this->requireAuth();

        $recordedById = $_SESSION['user_id'];
        $productId = (int)$this->input('product_id');
        $quantity = (int)$this->input('quantity');
        $reason = $this->input('reason');

        if ($productId <= 0 || $quantity <= 0 || empty($reason)) {
            $_SESSION['form_error'] = "Please fill in all required fields.";
            $this->redirect('damages/create');
        }

        $damageModel = new DamageModel();

        try {
            $damageModel->recordDamage($recordedById, $productId, $quantity, $reason);
            $_SESSION['form_success'] = "Stock damage recorded successfully. Product stock deducted by {$quantity} units.";
            $this->redirect('damages');
        } catch (Exception $e) {
            $_SESSION['form_error'] = "Error logging waste: " . $e->getMessage();
            $this->redirect('damages/create');
        }
    }
}
