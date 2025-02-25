CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ref VARCHAR(255) NOT NULL,
    transactionId VARCHAR(255) NOT NULL,
    mref VARCHAR(255) NOT NULL,
    message LONGTEXT NOT NULL,
    sender VARCHAR(255) NOT NULL,
    amount VARCHAR(255) NOT NULL,
    type ENUM('income', 'expense') NOT NULL,
    datetime DATETIME DEFAULT CURRENT_TIMESTAMP
);