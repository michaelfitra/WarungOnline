<?php 
class AuthController extends BaseController {
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (Auth::login($username, $password)) {
                Session::flash('success', 'Login successful!');
                
                // Redirect ke halaman yang dituju sebelumnya atau dashboard
                $redirect_url = Session::get('redirect_url', 'dashboard');
                Session::remove('redirect_url');
                $this->redirect($redirect_url);
            } else {
                Session::flash('error', 'Invalid credentials!');
            }
        }
        
        $this->view('auth/login');
    }
    
    public function logout() {
        Auth::logout();
        Session::flash('success', 'Logged out successfully!');
        $this->redirect('auth/login');
    }
}

?>