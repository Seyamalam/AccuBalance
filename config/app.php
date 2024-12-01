<?php
return [
    'app' => [
        'name' => 'AccuBalance',
        'version' => '1.0.0',
        'url' => 'http://localhost/accubalance',
        'timezone' => 'UTC',
        'debug' => true
    ],
    'database' => [
        'host' => 'localhost',
        'name' => 'accubalance',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4'
    ],
    'mail' => [
        'host' => 'smtp.mailtrap.io',
        'port' => 2525,
        'username' => '',
        'password' => '',
        'encryption' => 'tls'
    ],
    'security' => [
        'session_lifetime' => 7200,
        'rate_limit' => 60,
        'rate_limit_period' => 60
    ]
]; 