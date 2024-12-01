CREATE DATABASE IF NOT EXISTS accounting_system;

USE accounting_system;

CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    description VARCHAR(255) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    date DATE NOT NULL
);
