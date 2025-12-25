/* =========================
   ROLE & USER
========================= */

CREATE TABLE role (
    id INT AUTO_INCREMENT PRIMARY KEY,
    designation VARCHAR(100) UNIQUE,
    access_level INT
);

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(150) UNIQUE,
    password VARCHAR(255),
    phone VARCHAR(20),
    role_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id)
        REFERENCES role(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

/* =========================
   STADIUM & SEAT (STATIC)
========================= */

CREATE TABLE stadium (
    stadium_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150),
    location VARCHAR(200),
    capacity INT,
    contact_info VARCHAR(200)
);

CREATE TABLE seat (
    seat_id INT AUTO_INCREMENT PRIMARY KEY,
    stadium_id INT,
    section CHAR(1),          -- A to W
    row_number TINYINT,       -- 1 to 8
    seat_number TINYINT,      -- 1 to 10
    seat_type VARCHAR(50),    -- VIP / Regular
    FOREIGN KEY (stadium_id)
        REFERENCES stadium(stadium_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    UNIQUE (stadium_id, section, row_number, seat_number)
);

/* =========================
   TEAM & MATCH
========================= */

CREATE TABLE team (
    team_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    country VARCHAR(100),
    coach_name VARCHAR(100)
);

CREATE TABLE match_table (
    match_id INT AUTO_INCREMENT PRIMARY KEY,
    stadium_id INT,
    home_team_id INT,
    away_team_id INT,
    match_date DATE,
    start_time TIME,
    end_time TIME,
    status VARCHAR(20),
    poster_url VARCHAR(255),
    FOREIGN KEY (stadium_id)
        REFERENCES stadium(stadium_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (home_team_id)
        REFERENCES team(team_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (away_team_id)
        REFERENCES team(team_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

/* =========================
   MATCH-SEAT (CORE LOGIC)
========================= */

CREATE TABLE match_seat (
    match_seat_id INT AUTO_INCREMENT PRIMARY KEY,
    match_id INT NOT NULL,
    seat_id INT NOT NULL,
    status ENUM('available','held','booked') DEFAULT 'available',
    UNIQUE (match_id, seat_id),
    FOREIGN KEY (match_id)
        REFERENCES match_table(match_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (seat_id)
        REFERENCES seat(seat_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

/* =========================
   TICKET CATEGORY
========================= */

CREATE TABLE ticket_category (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    stadium_id INT,
    category_name VARCHAR(100),
    price DECIMAL(10,2),
    status VARCHAR(20),
    FOREIGN KEY (stadium_id)
        REFERENCES stadium(stadium_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

/* =========================
   BOOKING & PAYMENT
========================= */

CREATE TABLE booking (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    match_id INT,
    booking_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10,2),
    payment_status VARCHAR(20),
    FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (match_id)
        REFERENCES match_table(match_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE payment (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    amount DECIMAL(10,2),
    payment_method VARCHAR(50),
    transaction_id VARCHAR(100),
    payment_date DATETIME,
    status VARCHAR(20),
    FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

/* =========================
   TICKET (ISSUED AFTER PAYMENT)
========================= */

CREATE TABLE ticket (
    ticket_id INT AUTO_INCREMENT PRIMARY KEY,
    match_seat_id INT,
    category_id INT,
    booking_id INT,
    issued_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (match_seat_id)
        REFERENCES match_seat(match_seat_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (category_id)
        REFERENCES ticket_category(category_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (booking_id)
        REFERENCES booking(booking_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

/* =========================
   TEMP SEAT HOLD (3 MIN)
========================= */

CREATE TABLE seat_hold (
    hold_id INT AUTO_INCREMENT PRIMARY KEY,
    match_id INT NOT NULL,
    seat_id INT NOT NULL,
    user_id INT,
    session_id VARCHAR(255),
    hold_expires_at DATETIME NOT NULL,
    status ENUM('active','expired','confirmed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (match_id)
        REFERENCES match_table(match_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (seat_id)
        REFERENCES seat(seat_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    INDEX idx_match_seat (match_id, seat_id),
    INDEX idx_expires (hold_expires_at),
    INDEX idx_status (status)
);

/* =========================
   EVENT & REVIEW
========================= */

CREATE TABLE refund (
    refund_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT,
    amount DECIMAL(10,2),
    reason VARCHAR(255),
    refund_date DATETIME,
    status VARCHAR(20),
    FOREIGN KEY (booking_id)
        REFERENCES booking(booking_id)
        ON DELETE CASCADE
);


CREATE TABLE review (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    match_id INT,
    rating INT,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (match_id)
        REFERENCES match_table(match_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

/* =========================
   NOTIFICATION
========================= */

CREATE TABLE notification (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    title VARCHAR(150),
    message TEXT,
    sent_at DATETIME,
    status VARCHAR(20),
    FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
