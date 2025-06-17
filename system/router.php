<?php
class Router {
    private static $routes = [];
    private static $current_controller;
    private static $current_method;
    private static $params = [];
    
    public static function addRoute($pattern, $callback, $auth_required = false) {
        self::$routes[] = [
            'pattern' => $pattern,
            'callback' => $callback,
            'auth_required' => $auth_required
        ];
    }
    
    public static function get($pattern, $callback, $auth_required = false) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            self::addRoute($pattern, $callback, $auth_required);
        }
    }
    
    public static function post($pattern, $callback, $auth_required = false) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            self::addRoute($pattern, $callback, $auth_required);
        }
    }
    
    public static function redirect($url, $code = 302) {
        header("Location: " . URL::base($url), true, $code);
        exit;
    }
    
    public static function run() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = trim($uri, '/');
        
        // Cek routes yang sudah didefinisikan
        foreach (self::$routes as $route) {
            if (self::matchRoute($route['pattern'], $uri)) {
                // Cek autentikasi jika diperlukan
                if ($route['auth_required'] && !Auth::check()) {
                    Session::set('redirect_url', $_SERVER['REQUEST_URI']);
                    self::redirect('auth/login');
                }
                
                return self::executeCallback($route['callback']);
            }
        }
        
        // Fallback ke controller/method tradisional
        self::handleTraditionalRouting($uri);
    }
    
    private static function matchRoute($pattern, $uri) {
        // Konversi pattern ke regex
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $pattern);
        $pattern = str_replace('*', '(.*)', $pattern);
        $pattern = '/^' . str_replace('/', '\/', $pattern) . '$/';
        
        if (preg_match($pattern, $uri, $matches)) {
            array_shift($matches); // Remove full match
            self::$params = $matches;
            return true;
        }
        return false;
    }
    
    private static function executeCallback($callback) {
        if (is_callable($callback)) {
            return call_user_func_array($callback, self::$params);
        } elseif (is_string($callback)) {
            list($controller, $method) = explode('@', $callback);
            return self::callController($controller, $method, self::$params);
        }
    }
    
    private static function handleTraditionalRouting($uri) {
        $segments = explode('/', $uri);
        
        self::$current_controller = !empty($segments[0]) ? $segments[0] : Config::DEFAULT_CONTROLLER;
        self::$current_method = !empty($segments[1]) ? $segments[1] : Config::DEFAULT_METHOD;
        self::$params = array_slice($segments, 2);
        
        self::callController(self::$current_controller, self::$current_method, self::$params);
    }
    
    private static function callController($controller, $method, $params = []) {
        $controller_class = ucfirst($controller) . 'Controller';
        
        if (class_exists($controller_class)) {
            $controller_instance = new $controller_class();
            
            if (method_exists($controller_instance, $method)) {
                call_user_func_array([$controller_instance, $method], $params);
            } else {
                self::show404();
            }
        } else {
            self::show404();
        }
    }
    
    private static function show404() {
        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1>";
        exit;
    }
    
    public static function getCurrentController() {
        return self::$current_controller;
    }
    
    public static function getCurrentMethod() {
        return self::$current_method;
    }
    
    public static function getParams() {
        return self::$params;
    }
}
?>