<?php
class Config {
    const BASE_URL = 'http://localhost/WarungOnline/';
    const ASSETS_URL = 'http://localhost/WarungOnline/assets/';
    
    // const DEFAULT_CONTROLLER = 'home';
    // const DEFAULT_METHOD = 'index';
}
class URL {
    public static function base($path = '') {
        return Config::BASE_URL . ltrim($path, '/');
    }
    
    public static function assets($path = '') {
        return Config::ASSETS_URL . ltrim($path, '/');
    }
    
    public static function site($path = '') {
        return self::base($path);
    }
    
    public static function current() {
        return $_SERVER['REQUEST_URI'];
    }
}
?>

