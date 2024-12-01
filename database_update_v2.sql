-- Investment tracking
CREATE TABLE investments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    name VARCHAR(100) NOT NULL,
    type ENUM('stock', 'crypto', 'mutual_fund', 'bond', 'real_estate', 'other') NOT NULL,
    purchase_price DECIMAL(15,2) NOT NULL,
    current_price DECIMAL(15,2) NOT NULL,
    quantity DECIMAL(15,4) NOT NULL,
    purchase_date DATE NOT NULL,
    notes TEXT,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Bill reminders
CREATE TABLE bill_reminders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    title VARCHAR(100) NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    due_date DATE NOT NULL,
    category VARCHAR(50) NOT NULL,
    status ENUM('pending', 'paid', 'overdue') DEFAULT 'pending',
    recurring BOOLEAN DEFAULT FALSE,
    frequency ENUM('monthly', 'quarterly', 'yearly') NULL,
    notification_days INT DEFAULT 3,
    last_notification DATE,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Budget Categories
CREATE TABLE budget_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    name VARCHAR(50) NOT NULL,
    type ENUM('expense', 'income') NOT NULL,
    icon VARCHAR(50),
    color VARCHAR(7),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Savings Goals Analytics
CREATE TABLE savings_goal_progress (
    id INT PRIMARY KEY AUTO_INCREMENT,
    goal_id INT,
    amount DECIMAL(15,2) NOT NULL,
    date DATE NOT NULL,
    FOREIGN KEY (goal_id) REFERENCES goals(id)
);

-- Transaction Tags
CREATE TABLE transaction_tags (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    name VARCHAR(50) NOT NULL,
    color VARCHAR(7),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE transaction_tag_relations (
    transaction_id INT,
    tag_id INT,
    PRIMARY KEY (transaction_id, tag_id),
    FOREIGN KEY (transaction_id) REFERENCES transactions(id),
    FOREIGN KEY (tag_id) REFERENCES transaction_tags(id)
);

-- Financial Reports
CREATE TABLE saved_reports (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    name VARCHAR(100) NOT NULL,
    type ENUM('income', 'expense', 'investment', 'complete') NOT NULL,
    parameters JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- User Preferences
CREATE TABLE user_preferences (
    user_id INT PRIMARY KEY,
    default_currency VARCHAR(3) DEFAULT 'USD',
    theme ENUM('light', 'dark', 'system') DEFAULT 'system',
    notification_email BOOLEAN DEFAULT TRUE,
    notification_web BOOLEAN DEFAULT TRUE,
    dashboard_widgets JSON,
    FOREIGN KEY (user_id) REFERENCES users(id)
); 