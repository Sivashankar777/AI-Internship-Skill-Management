<?php

class Router {
    protected $routes = [];

    public function add($method, $path, $controller, $action) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function dispatch($uri, $method) {
        $uri = parse_url($uri, PHP_URL_PATH);
        // Remove base path if needed (for localhost subdirectories)
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        if (strpos($uri, $scriptName) === 0) {
            $uri = substr($uri, strlen($scriptName));
        }
        $uri = '/' . trim($uri, '/');

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $uri) {
                require_once APP_ROOT . '/app/controllers/' . $route['controller'] . '.php';
                $controllerName = $route['controller'];
                $controller = new $controllerName();
                $action = $route['action'];
                $controller->$action();
                return;
            }
        }

        // 404 Not Found
        http_response_code(404);
        require_once APP_ROOT . '/app/views/404.php';
    }
}
