<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'finance_manager');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
}
?> 