<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Models\SaleModel;
use App\Models\ProductModel;
use App\Models\CollectionModel;
use Exception;

class Sales extends BaseController {

    /**
     * List sales invoices (role-based filter)
     */
    public function index() {
        $this->requireAuth();
        
        $saleModel = new SaleModel();
        $role = $_SESSION['user_role'];
        $userId = $_SESSION['user_id'];

        // Salesmen only view their own transactions
        $sales = ($role === 'salesman') ? $saleModel->getSalesWithStaff($userId) : $saleModel->getSalesWithStaff();

        $this->render('sales/index', [
            'title' => 'Daily Sales Logs',
            'sales' => $sales
        ]);
    }

    /**
     * Create Sale Form (Interactive Multi-product Entry)
     */
    public function create() {
        $this->requireAuth(); // All roles (including Salesman) can register sales!

        $productModel = new ProductModel();
        // Fetch only active products with stock > 0
        $products = $productModel->query("SELECT * FROM products WHERE stock_quantity > 0 ORDER BY name ASC")->fetchAll(\PDO::FETCH_ASSOC);

        $this->render('sales/create', [
            'title' => 'Input New Daily Sales',
            'products' => $products
        ]);
    }

    /**
     * Process Daily Sales Transaction
     */
    public function store() {
        $this->requireAuth();

        $salesmanId = $_SESSION['user_id'];
        $productIds = $_POST['product_ids'] ?? [];
        $quantities = $_POST['quantities'] ?? [];
        $paidAmount = (float)($this->input('paid_amount') ?? 0.0);
        $paymentMethod = $this->input('payment_method') ?? 'Cash';

        if (empty($productIds) || empty($quantities)) {
            $_SESSION['form_error'] = "Please select at least one product.";
            $this->redirect('sales/create');
        }

        // Structure items payload
        $items = [];
        for ($i = 0; $i < count($productIds); $i++) {
            $prodId = (int)$productIds[$i];
            $qty = (int)$quantities[$i];

            if ($prodId > 0 && $qty > 0) {
                $items[] = [
                    'product_id' => $prodId,
                    'quantity' => $qty
                ];
            }
        }

        if (empty($items)) {
            $_SESSION['form_error'] = "No valid product quantities provided.";
            $this->redirect('sales/create');
        }

        $saleModel = new SaleModel();

        try {
            $saleId = $saleModel->createSale($salesmanId, $items, $paidAmount, $paymentMethod);
            $_SESSION['form_success'] = "Daily Sale recorded successfully. Invoice ID #{$saleId} compiled.";
            $this->redirect('sales');
        } catch (Exception $e) {
            $_SESSION['form_error'] = "Transaction Failed: " . $e->getMessage();
            $this->redirect('sales/create');
        }
    }

    /**
     * View Invoice Case Sheet Details
     */
    public function view($id) {
        $this->requireAuth();

        $saleModel = new SaleModel();
        $sale = $saleModel->getSaleDetails($id);

        if (!$sale) {
            $this->redirect('sales');
        }

        // Enforce Salesman role restriction
        if ($_SESSION['user_role'] === 'salesman' && $sale['salesman_id'] !== $_SESSION['user_id']) {
            $this->unauthorized();
        }

        $this->render('sales/view', [
            'title' => "Invoice Details: #{$sale['id']}",
            'sale' => $sale
        ]);
    }

    /**
     * List cash collections from credit accounts
     */
    public function collectionsIndex() {
        $this->requireAuth();
        
        $collectionModel = new CollectionModel();
        $role = $_SESSION['user_role'];
        $userId = $_SESSION['user_id'];

        $collections = ($role === 'salesman') ? $collectionModel->getCollectionsWithStaff($userId) : $collectionModel->getCollectionsWithStaff();

        $this->render('sales/collections_index', [
            'title' => 'Daily Cash Collections',
            'collections' => $collections
        ]);
    }

    /**
     * Create Collections Log Form
     */
    public function collectionsCreate() {
        $this->requireAuth();
        
        $saleModel = new SaleModel();
        $role = $_SESSION['user_role'];
        $userId = $_SESSION['user_id'];

        // Get sales that currently have due amounts to assign credit payments
        $sql = "SELECT s.id, s.due_amount, u.full_name as salesman_name 
                FROM sales s 
                INNER JOIN users u ON s.salesman_id = u.id 
                WHERE s.due_amount > 0";
        
        $params = [];
        if ($role === 'salesman') {
            $sql .= " AND s.salesman_id = :salesman_id";
            $params[':salesman_id'] = $userId;
        }

        $creditSales = $saleModel->query($sql, $params)->fetchAll(\PDO::FETCH_ASSOC);

        $this->render('sales/collections_create', [
            'title' => 'Log Credit Account Collection',
            'creditSales' => $creditSales
        ]);
    }

    /**
     * Store credit payment cash collection
     */
    public function collectionsStore() {
        $this->requireAuth();

        $salesmanId = $_SESSION['user_id'];
        $saleId = (int)$this->input('sale_id');
        $customerName = $this->input('customer_name');
        $amount = (float)$this->input('amount');
        $notes = $this->input('notes') ?? '';

        if ($saleId <= 0 || empty($customerName) || $amount <= 0) {
            $_SESSION['form_error'] = "Please fill in all required fields with positive cash amount.";
            $this->redirect('sales/collections/create');
        }

        $saleModel = new SaleModel();
        $collectionModel = new CollectionModel();

        // Retrieve Invoice due levels
        $sale = $saleModel->find($saleId);
        if (!$sale) {
            $_SESSION['form_error'] = "Invoice not found.";
            $this->redirect('sales/collections/create');
        }

        if ($_SESSION['user_role'] === 'salesman' && $sale['salesman_id'] !== $salesmanId) {
            $this->unauthorized();
        }

        if ($amount > $sale['due_amount']) {
            $_SESSION['form_error'] = "Collected amount cannot exceed the pending due level of {$sale['due_amount']} TK.";
            $this->redirect('sales/collections/create');
        }

        // Initialize Database transaction manually to synchronize tables
        $db = \App\Config\Database::getConnection();
        $db->beginTransaction();

        try {
            // 1. Record Collection Log
            $collectionModel->insert([
                'salesman_id' => $salesmanId,
                'customer_name' => $customerName,
                'amount' => $amount,
                'notes' => $notes
            ]);

            // 2. Adjust Sales due levels (Add to paid, subtract from due)
            $newPaid = $sale['paid_amount'] + $amount;
            $newDue = max(0.0, $sale['due_amount'] - $amount);
            
            $saleModel->update($saleId, [
                'paid_amount' => $newPaid,
                'due_amount' => $newDue
            ]);

            $db->commit();
            $_SESSION['form_success'] = "Daily Cash Collection logged. Pending invoice credit reduced by {$amount} TK.";
            $this->redirect('sales/collections');

        } catch (Exception $e) {
            $db->rollBack();
            $_SESSION['form_error'] = "Log failed: " . $e->getMessage();
            $this->redirect('sales/collections/create');
        }
    }
}
