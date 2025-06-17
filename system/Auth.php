<?php
class Auth {
    public static function login($username, $password) {
        // Simulasi validasi user (ganti dengan database query)
        $users = [
            'admin' => 'password123',
            'user' => 'user123'
        ];
        
        if (isset($users[$username]) && $users[$username] === $password) {
            Session::set('user_id', $username);
            Session::set('logged_in', true);
            Session::set('login_time', time());
            return true;
        }
        return false;
    }
    
    public static function logout() {
        Session::remove('user_id');
        Session::remove('logged_in');
        Session::remove('login_time');
    }
    
    public static function check() {
        return Session::get('logged_in', false);
    }
    
    public static function user() {
        return Session::get('user_id');
    }
    
    public static function requireAuth() {
        if (!self::check()) {
            // Simpan URL yang ingin diakses untuk redirect setelah login
            Session::set('redirect_url', $_SERVER['REQUEST_URI']);
            Router::redirect('auth/login');
        }
    }
}
?>