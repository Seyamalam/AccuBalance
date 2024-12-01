-- Goal Milestones
CREATE TABLE goal_milestones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    goal_id INT,
    title VARCHAR(100) NOT NULL,
    target_amount DECIMAL(15,2) NOT NULL,
    deadline DATE NOT NULL,
    achieved BOOLEAN DEFAULT FALSE,
    achieved_date DATE,
    FOREIGN KEY (goal_id) REFERENCES goals(id)
);

-- Goal Categories
CREATE TABLE goal_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    name VARCHAR(50) NOT NULL,
    icon VARCHAR(50),
    color VARCHAR(7),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Goal Notes
CREATE TABLE goal_notes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    goal_id INT,
    note TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (goal_id) REFERENCES goals(id)
);

-- Add new columns to goals table
ALTER TABLE goals 
ADD COLUMN category_id INT,
ADD COLUMN priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
ADD COLUMN auto_save BOOLEAN DEFAULT FALSE,
ADD COLUMN auto_save_amount DECIMAL(15,2),
ADD COLUMN auto_save_frequency ENUM('daily', 'weekly', 'monthly') DEFAULT 'monthly',
ADD COLUMN reminder_frequency INT DEFAULT 7,
ADD COLUMN last_reminder_date DATE,
ADD FOREIGN KEY (category_id) REFERENCES goal_categories(id); 