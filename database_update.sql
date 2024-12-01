CREATE TABLE recurring_transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    account_id INT,
    type ENUM('income', 'expense') NOT NULL,
    category VARCHAR(50) NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    description TEXT,
    frequency ENUM('daily', 'weekly', 'monthly', 'yearly') NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE,
    last_processed DATE,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (account_id) REFERENCES accounts(id)
); 