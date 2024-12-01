<?php
class Session {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Set secure session parameters
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', 1);
        
        // Regenerate session ID periodically
        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } else if (time() - $_SESSION['created'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }
    }
    
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    public static function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }
    
    public static function delete($key) {
        unset($_SESSION[$key]);
    }
    
    public static function destroy() {
        session_destroy();
    }
    
    public static function flash($key, $value = null) {
        if ($value !== null) {
            $_SESSION['flash'][$key] = $value;
        } else {
            $value = $_SESSION['flash'][$key] ?? null;
            unset($_SESSION['flash'][$key]);
            return $value;
        }
    }
    
    public static function hasFlash($key) {
        return isset($_SESSION['flash'][$key]);
    }
} 