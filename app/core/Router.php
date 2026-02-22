<?php
/**
 * Sanskar AI - Router
 * ====================
 * Custom routing system with middleware support
 */

namespace App\Core;

class Router
{
    private static array $routes = [];
    private static array $namedRoutes = [];
    private static string $prefix = '';
    private static array $middlewareStack = [];
    
    /**
     * Add GET route
     */
    public static function get(string $path, $handler, array $middleware = [], ?string $name = null): void
    {
        self::addRoute('GET', $path, $handler, $middleware, $name);
    }
    
    /**
     * Add POST route
     */
    public static function post(string $path, $handler, array $middleware = [], ?string $name = null): void
    {
        self::addRoute('POST', $path, $handler, $middleware, $name);
    }
    
    /**
     * Add PUT route
     */
    public static function put(string $path, $handler, array $middleware = [], ?string $name = null): void
    {
        self::addRoute('PUT', $path, $handler, $middleware, $name);
    }
    
    /**
     * Add DELETE route
     */
    public static function delete(string $path, $handler, array $middleware = [], ?string $name = null): void
    {
        self::addRoute('DELETE', $path, $handler, $middleware, $name);
    }
    
    /**
     * Group routes with prefix and middleware
     */
    public static function group(array $options, callable $callback): void
    {
        $previousPrefix = self::$prefix;
        $previousMiddleware = self::$middlewareStack;
        
        // Apply prefix
        if (isset($options['prefix'])) {
            self::$prefix .= '/' . trim($options['prefix'], '/');
        }
        
        // Apply middleware
        if (isset($options['middleware'])) {
            $middleware = is_array($options['middleware']) ? $options['middleware'] : [$options['middleware']];
            self::$middlewareStack = array_merge(self::$middlewareStack, $middleware);
        }
        
        // Execute callback
        $callback();
        
        // Restore previous state
        self::$prefix = $previousPrefix;
        self::$middlewareStack = $previousMiddleware;
    }
    
    /**
     * Add route to registry
     */
    private static function addRoute(string $method, string $path, $handler, array $middleware, ?string $name): void
    {
        $fullPath = self::$prefix . '/' . trim($path, '/');
        $fullPath = '/' . trim($fullPath, '/');
        
        // Merge group middleware with route middleware
        $allMiddleware = array_merge(self::$middlewareStack, $middleware);
        
        // Convert path to regex pattern
        $pattern = self::pathToRegex($fullPath);
        
        $route = [
            'method' => $method,
            'path' => $fullPath,
            'pattern' => $pattern,
            'handler' => $handler,
            'middleware' => $allMiddleware,
            'name' => $name,
        ];
        
        self::$routes[] = $route;
        
        if ($name) {
            self::$namedRoutes[$name] = $route;
        }
    }
    
    /**
     * Convert path pattern to regex
     */
    private static function pathToRegex(string $path): string
    {
        // Replace {param} with named capture groups
        $pattern = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '(?P<$1>[^/]+)', $path);
        
        // Escape forward slashes
        $pattern = str_replace('/', '\/', $pattern);
        
        return '/^' . $pattern . '$/';
    }
    
    /**
     * Generate URL for named route
     */
    public static function route(string $name, array $params = []): string
    {
        if (!isset(self::$namedRoutes[$name])) {
            throw new \RuntimeException("Route '$name' not found");
        }
        
        $path = self::$namedRoutes[$name]['path'];
        
        // Replace parameters
        foreach ($params as $key => $value) {
            $path = str_replace('{' . $key . '}', $value, $path);
        }
        
        return $path;
    }
    
    /**
     * Dispatch the request
     */
    public static function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Handle PUT/DELETE from forms with _method field
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }
        
        // Find matching route
        foreach (self::$routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            
            if (preg_match($route['pattern'], $uri, $matches)) {
                // Extract named parameters
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                // Run middleware
                foreach ($route['middleware'] as $middleware) {
                    $middlewareClass = self::resolveMiddleware($middleware);
                    if (!$middlewareClass->handle()) {
                        return; // Middleware blocked the request
                    }
                }
                
                // Execute handler
                self::executeHandler($route['handler'], $params);
                return;
            }
        }
        
        // No route found - 404
        self::notFound();
    }
    
    /**
     * Resolve middleware class
     */
    private static function resolveMiddleware(string $middleware): object
    {
        $middlewareMap = [
            'admin' => \App\Middleware\AdminMiddleware::class,
            'pandit' => \App\Middleware\PanditMiddleware::class,
            'user' => \App\Middleware\UserMiddleware::class,
            'auth' => \App\Middleware\AuthMiddleware::class,
            'guest' => \App\Middleware\GuestMiddleware::class,
        ];
        
        $class = $middlewareMap[$middleware] ?? $middleware;
        
        if (!class_exists($class)) {
            throw new \RuntimeException("Middleware class '$class' not found");
        }
        
        return new $class();
    }
    
    /**
     * Execute route handler
     */
    private static function executeHandler($handler, array $params): void
    {
        if (is_callable($handler)) {
            call_user_func_array($handler, array_values($params));
            return;
        }
        
        if (is_string($handler)) {
            // Controller@method format
            if (strpos($handler, '@') !== false) {
                list($controller, $method) = explode('@', $handler);
            } else {
                list($controller, $method) = explode('::', $handler);
            }
            
            $controllerClass = "\\App\\Controllers\\$controller";
            
            if (!class_exists($controllerClass)) {
                throw new \RuntimeException("Controller '$controllerClass' not found");
            }
            
            $instance = new $controllerClass();
            
            if (!method_exists($instance, $method)) {
                throw new \RuntimeException("Method '$method' not found in '$controllerClass'");
            }
            
            call_user_func_array([$instance, $method], array_values($params));
            return;
        }
        
        if (is_array($handler)) {
            list($controller, $method) = $handler;
            
            if (is_string($controller)) {
                $controller = new $controller();
            }
            
            call_user_func_array([$controller, $method], array_values($params));
            return;
        }
        
        throw new \RuntimeException("Invalid route handler");
    }
    
    /**
     * Handle 404 Not Found
     */
    private static function notFound(): void
    {
        http_response_code(404);
        
        $viewPath = \App\Config\App::viewsPath('errors/404.php');
        
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo '<h1>404 - Page Not Found</h1>';
            echo '<p>The requested page could not be found.</p>';
        }
    }
    
    /**
     * Redirect to URL
     */
    public static function redirect(string $url, int $code = 302): void
    {
        http_response_code($code);
        header("Location: $url");
        exit;
    }
    
    /**
     * Redirect back to previous page
     */
    public static function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        self::redirect($referer);
    }
    
    /**
     * Get current URI
     */
    public static function currentUri(): string
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }
    
    /**
     * Check if current route matches
     */
    public static function is(string $pattern): bool
    {
        $uri = self::currentUri();
        $pattern = str_replace('*', '.*', $pattern);
        return (bool) preg_match('/^' . str_replace('/', '\/', $pattern) . '$/', $uri);
    }
}
