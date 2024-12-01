<?php
// CSRF Protection
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        throw new Exception('CSRF token validation failed');
    }
    return true;
}

// XSS Protection
function sanitizeOutput($data) {
    if (is_array($data)) {
        return array_map('sanitizeOutput', $data);
    }
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// SQL Injection Protection
function sanitizeInput($data) {
    global $conn;
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return $conn->real_escape_string($data);
}

// Rate Limiting
function checkRateLimit($user_id, $action, $limit = 60, $period = 60) {
    global $conn;
    
    $query = "SELECT COUNT(*) as count FROM activity_log 
              WHERE user_id = ? AND action = ? 
              AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param('isi', $user_id, $action, $period);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result['count'] >= $limit) {
        throw new Exception('Rate limit exceeded');
    }
    
    return true;
}

// Input Validation
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePassword($password) {
    // At least 8 characters, 1 uppercase, 1 lowercase, 1 number, 1 special char
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
}

// Session Security
function regenerateSession() {
    session_regenerate_id(true);
}

function secureSession() {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.use_only_cookies', 1);
} 