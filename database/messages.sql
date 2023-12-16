-- Messages table   
CREATE TABLE messages (
    message_id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id VARCHAR(10) NOT NULL,
    receiver_id VARCHAR(10) NOT NULL,
    message_text VARCHAR(350) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(rcsid),
    FOREIGN KEY (receiver_id) REFERENCES users(rcsid)
);