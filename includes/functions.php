<?php
// General utility functions

function formatCurrency($amount, $currency = 'USD') {
    return number_format($amount, 2, '.', ',');
}

function formatDate($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

function generateSlug($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return $text;
}

function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

function redirectTo($path) {
    header("Location: $path");
    exit();
}

function getCurrentUrl() {
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . 
           "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}

function getBaseUrl() {
    $config = require 'config/app.php';
    return $config['app']['url'];
}

function asset($path) {
    return getBaseUrl() . '/assets/' . ltrim($path, '/');
}

function generateRandomString($length = 10) {
    return bin2hex(random_bytes($length));
}

function flashMessage($key, $message = null) {
    if ($message) {
        $_SESSION['flash'][$key] = $message;
    } else {
        $message = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $message;
    }
} 