<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Models\ProductModel;
use App\Core\BaseModel;
use PDO;

class Products extends BaseController {

    /**
     * Display Product Index (Read-only list for Salesmen)
     */
    public function index() {
        $this->requireAuth();
        
        $productModel = new ProductModel();
        $products = $productModel->getProductsWithCategories();

        $this->render('products/index', [
            'title' => 'Product Inventory',
            'products' => $products
        ]);
    }

    /**
     * Display Create Product View (Admin/Super Admin only)
     */
    public function create() {
        $this->requireRole(['super_admin', 'admin']);
        
        $productModel = new ProductModel();
        // Fetch categories list
        $categories = $productModel->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

        $this->render('products/create', [
            'title' => 'Add New Inventory Item',
            'categories' => $categories
        ]);
    }

    /**
     * Store new product in database
     */
    public function store() {
        $this->requireRole(['super_admin', 'admin']);

        $data = $this->input();
        
        if (empty($data['name']) || empty($data['sku']) || !isset($data['price']) || !isset($data['cost']) || !isset($data['category_id'])) {
            $_SESSION['form_error'] = "Please fill in all required fields.";
            $this->redirect('products/create');
        }

        $productModel = new ProductModel();
        
        // Verify duplicate SKU
        $duplicate = $productModel->where(['sku' => $data['sku']]);
        if (!empty($duplicate)) {
            $_SESSION['form_error'] = "A product with SKU '{$data['sku']}' already exists.";
            $this->redirect('products/create');
        }

        $productModel->insert([
            'category_id' => (int)$data['category_id'],
            'name' => $data['name'],
            'sku' => strtoupper($data['sku']),
            'price' => (float)$data['price'],
            'cost' => (float)$data['cost'],
            'stock_quantity' => (int)($data['stock_quantity'] ?? 0),
            'min_stock_alert' => (int)($data['min_stock_alert'] ?? 5),
            'description' => $data['description'] ?? ''
        ]);

        $_SESSION['form_success'] = "Product '{$data['name']}' added to inventory successfully.";
        $this->redirect('products');
    }

    /**
     * Display Edit Product View
     */
    public function edit($id) {
        $this->requireRole(['super_admin', 'admin']);

        $productModel = new ProductModel();
        $product = $productModel->find($id);

        if (!$product) {
            $this->redirect('products');
        }

        $categories = $productModel->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

        $this->render('products/create', [
            'title' => "Edit Item: {$product['name']}",
            'product' => $product,
            'categories' => $categories
        ]);
    }

    /**
     * Update product details
     */
    public function update($id) {
        $this->requireRole(['super_admin', 'admin']);

        $data = $this->input();
        
        if (empty($data['name']) || empty($data['sku']) || !isset($data['price']) || !isset($data['cost']) || !isset($data['category_id'])) {
            $_SESSION['form_error'] = "Please fill in all required fields.";
            $this->redirect("products/edit/{$id}");
        }

        $productModel = new ProductModel();

        // Verify duplicate SKU (exclude current product)
        $duplicate = $productModel->query("SELECT id FROM products WHERE sku = :sku AND id != :id LIMIT 1", [
            ':sku' => strtoupper($data['sku']),
            ':id' => $id
        ])->fetch();

        if ($duplicate) {
            $_SESSION['form_error'] = "A product with SKU '{$data['sku']}' already exists.";
            $this->redirect("products/edit/{$id}");
        }

        $productModel->update($id, [
            'category_id' => (int)$data['category_id'],
            'name' => $data['name'],
            'sku' => strtoupper($data['sku']),
            'price' => (float)$data['price'],
            'cost' => (float)$data['cost'],
            'min_stock_alert' => (int)($data['min_stock_alert'] ?? 5),
            'description' => $data['description'] ?? ''
        ]);

        $_SESSION['form_success'] = "Product settings updated successfully.";
        $this->redirect('products');
    }

    /**
     * Delete product from database
     */
    public function delete($id) {
        $this->requireRole(['super_admin', 'admin']);

        $productModel = new ProductModel();
        $productModel->delete($id);

        $_SESSION['form_success'] = "Product deleted successfully.";
        $this->redirect('products');
    }

    /**
     * Restock View Page
     */
    public function restockView($id) {
        $this->requireRole(['super_admin', 'admin']);

        $productModel = new ProductModel();
        $product = $productModel->find($id);

        if (!$product) {
            $this->redirect('products');
        }

        $this->render('products/restock', [
            'title' => "Restock Item: {$product['name']}",
            'product' => $product
        ]);
    }

    /**
     * Process restocking additions
     */
    public function restockStore($id) {
        $this->requireRole(['super_admin', 'admin']);
        
        $qty = (int)$this->input('added_qty');
        
        if ($qty <= 0) {
            $_SESSION['form_error'] = "Added quantity must be greater than zero.";
            $this->redirect("products/restock/{$id}");
        }

        $productModel = new ProductModel();
        $productModel->adjustStock($id, $qty);

        $_SESSION['form_success'] = "Inventory restocked successfully. Stock increased.";
        $this->redirect('products');
    }
}
