-- Table to track temporary seat holds
CREATE TABLE IF NOT EXISTS seat_hold (
    hold_id INT AUTO_INCREMENT PRIMARY KEY,
    match_id INT NOT NULL,
    seat_id INT NOT NULL,
    user_id INT,
    session_id VARCHAR(255),
    hold_expires_at DATETIME NOT NULL,
    status VARCHAR(20) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (match_id) REFERENCES match_table(match_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (seat_id) REFERENCES seat(seat_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_match_seat (match_id, seat_id),
    INDEX idx_expires (hold_expires_at),
    INDEX idx_status (status)
);

