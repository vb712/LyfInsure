CREATE TABLE IF NOT EXISTS purchases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_email VARCHAR(255) NOT NULL,
    plan_name VARCHAR(255) NOT NULL,
    premium VARCHAR(255) NOT NULL,
    base_coverage VARCHAR(255) NOT NULL,
    extra_coverage VARCHAR(255) NOT NULL,
    duration VARCHAR(255) NOT NULL,
    purchase_date DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_email) REFERENCES users(email)
); 