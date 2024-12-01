<?php
class Auth {
    private static $instance = null;
    private $db;
    
    private function __construct() {
        $this->db = Database::getInstance();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function login($email, $password) {
        $conn = $this->db->getConnection();
        $email = $conn->real_escape_string($email);
        
        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($query);
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['last_activity'] = time();
                return true;
            }
        }
        return false;
    }
    
    public function logout() {
        session_destroy();
        session_start();
        return true;
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        $conn = $this->db->getConnection();
        $user_id = $_SESSION['user_id'];
        
        $query = "SELECT * FROM users WHERE id = $user_id";
        $result = $conn->query($query);
        
        return $result->fetch_assoc();
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit();
        }
    }
    
    public function checkSessionTimeout() {
        $config = require 'config/app.php';
        $timeout = $config['security']['session_lifetime'];
        
        if (isset($_SESSION['last_activity']) && 
            (time() - $_SESSION['last_activity'] > $timeout)) {
            $this->logout();
            return false;
        }
        
        $_SESSION['last_activity'] = time();
        return true;
    }
} 