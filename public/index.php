<?php
/**
 * ----------------------------------------------------
 * Custom PHP MVC Front Controller & Autoloader
 * ----------------------------------------------------
 */

// Error Reporting (Development Mode)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define Constants
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');

// Namespace Autoloader
spl_autoload_register(function ($class) {
    // Convert namespace to file path
    // E.g. App\Controllers\Auth -> app/Controllers/Auth.php
    $classPath = str_replace('\\', '/', $class);
    
    // Replace 'App/' with 'app/' for PSR-4 compliance
    if (strpos($classPath, 'App/') === 0) {
        $classPath = 'app/' . substr($classPath, 4);
    }
    
    $file = ROOT_PATH . '/' . $classPath . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Load Config & Helpers
require_once APP_PATH . '/Config/Database.php';

// Instantiate Router & Parse Request
use App\Core\Router;

$router = new Router();

// Include Routes definitions
// Authentication
$router->add('GET', 'login', 'Auth@login');
$router->add('POST', 'login', 'Auth@attemptLogin');
$router->add('GET', 'logout', 'Auth@logout');

// Dashboard
$router->add('GET', '', 'Dashboard@index');
$router->add('GET', 'dashboard', 'Dashboard@index');

// Products (Inventory CRUD)
$router->add('GET', 'products', 'Products@index');
$router->add('GET', 'products/create', 'Products@create');
$router->add('POST', 'products/create', 'Products@store');
$router->add('GET', 'products/edit/([0-9]+)', 'Products@edit');
$router->add('POST', 'products/edit/([0-9]+)', 'Products@update');
$router->add('GET', 'products/delete/([0-9]+)', 'Products@delete');
$router->add('GET', 'products/restock/([0-9]+)', 'Products@restockView');
$router->add('POST', 'products/restock/([0-9]+)', 'Products@restockStore');

// Sales & Collections
$router->add('GET', 'sales', 'Sales@index');
$router->add('GET', 'sales/create', 'Sales@create');
$router->add('POST', 'sales/create', 'Sales@store');
$router->add('GET', 'sales/view/([0-9]+)', 'Sales@view');
$router->add('GET', 'sales/collections', 'Sales@collectionsIndex');
$router->add('GET', 'sales/collections/create', 'Sales@collectionsCreate');
$router->add('POST', 'sales/collections/create', 'Sales@collectionsStore');

// Damages (Waste Management)
$router->add('GET', 'damages', 'Damages@index');
$router->add('GET', 'damages/create', 'Damages@create');
$router->add('POST', 'damages/create', 'Damages@store');

// Admin User Management (Super Admin Only)
$router->add('GET', 'users', 'Users@index');
$router->add('GET', 'users/create', 'Users@create');
$router->add('POST', 'users/create', 'Users@store');
$router->add('GET', 'users/delete/([0-9]+)', 'Users@delete');

// Resolve Route
$router->resolve();
