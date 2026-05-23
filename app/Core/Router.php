<?php
namespace App\Core;

class Router {
    protected $routes = [];

    /**
     * Add a route mapping
     */
    public function add($method, $route, $controllerAction) {
        // Convert route to regex pattern
        // E.g. products/edit/([0-9]+) -> ^products\/edit\/([0-9]+)$
        $routePattern = '^' . str_replace('/', '\/', $route) . '$';
        
        $this->routes[] = [
            'method' => strtoupper($method),
            'route' => $route,
            'pattern' => $routePattern,
            'controllerAction' => $controllerAction
        ];
    }

    /**
     * Resolve the current HTTP request
     */
    public function resolve() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $this->getURI();

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match('/' . $route['pattern'] . '/', $uri, $matches)) {
                // Remove the full match to leave only capturing groups (e.g. ID)
                array_shift($matches);

                // Split controller and action
                list($controllerName, $actionName) = explode('@', $route['controllerAction']);
                $fullControllerClass = "App\\Controllers\\" . $controllerName;

                if (class_exists($fullControllerClass)) {
                    $controllerInstance = new $fullControllerClass();
                    if (method_exists($controllerInstance, $actionName)) {
                        // Call action method with wildcard parameters
                        call_user_func_array([$controllerInstance, $actionName], $matches);
                        return;
                    }
                }
            }
        }

        // Route Not Found (404 Page)
        $this->show404();
    }

    /**
     * Extract URI parameter cleanly
     */
    protected function getURI() {
        $uri = trim($_SERVER['REQUEST_URI'], '/');
        
        // Remove query parameters if any (e.g., /products?page=2 -> /products)
        if (strpos($uri, '?') !== false) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }

        // Handle sub-folder installations (like /inventory-system/)
        // Find if public folder lies in the path and truncate preceding segments
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptName !== '/' && strpos($uri, trim($scriptName, '/')) === 0) {
            $uri = substr($uri, strlen(trim($scriptName, '/')));
        }
        
        return trim($uri, '/');
    }

    /**
     * Render a neat custom 404 page
     */
    protected function show404() {
        http_response_code(404);
        echo "<!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <title>404 - Resource Not Found</title>
            <style>
                body { background-color: #0b0c16; color: #fff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
                .container { text-align: center; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); padding: 40px; border-radius: 12px; backdrop-filter: blur(8px); }
                h1 { font-size: 5rem; margin: 0; color: #f43f5e; text-shadow: 0 0 10px rgba(244,63,94,0.3); }
                h2 { font-size: 1.5rem; margin: 10px 0 20px; color: #9ca3af; }
                a { color: #8b5cf6; text-decoration: none; font-weight: 600; }
                a:hover { text-decoration: underline; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h1>404</h1>
                <h2>The resource you are looking for has departed.</h2>
                <p><a href='" . $this->getBaseUrl() . "'>Return to System Command Dashboard</a></p>
            </div>
        </body>
        </html>";
        exit;
    }

    protected function getBaseUrl() {
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        return ($scriptName === '/' ? '' : $scriptName) . '/';
    }
}
