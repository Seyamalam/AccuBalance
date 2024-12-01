CREATE DATABASE finance_manager;
USE finance_manager;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE accounts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    account_name VARCHAR(100) NOT NULL,
    account_type ENUM('checking', 'savings', 'credit', 'investment') NOT NULL,
    balance DECIMAL(15,2) DEFAULT 0,
    currency VARCHAR(3) DEFAULT 'USD',
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    account_id INT,
    type ENUM('income', 'expense', 'transfer') NOT NULL,
    category VARCHAR(50) NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    description TEXT,
    transaction_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (account_id) REFERENCES accounts(id)
);

CREATE TABLE budgets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    category VARCHAR(50) NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE goals (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    goal_name VARCHAR(100) NOT NULL,
    target_amount DECIMAL(15,2) NOT NULL,
    current_amount DECIMAL(15,2) DEFAULT 0,
    deadline DATE NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
); 