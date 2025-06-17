<?php
class BaseController {
    protected function view($view, $data = []) {
        extract($data);
        
        // Include view file
        $view_file = "views/{$view}.php";
        if (file_exists($view_file)) {
            include $view_file;
        } else {
            echo "View not found: {$view}";
        }
    }
    
    protected function json($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    protected function redirect($url) {
        Router::redirect($url);
    }
}
?>