
CREATE TABLE users ( user_id INT AUTO_INCREMENT PRIMARY KEY, name
VARCHAR(100), email VARCHAR(150) UNIQUE, password VARCHAR(255), phone
VARCHAR(20), role VARCHAR(50), created_at TIMESTAMP DEFAULT
CURRENT_TIMESTAMP );

CREATE TABLE admin ( admin_id INT AUTO_INCREMENT PRIMARY KEY,
designation VARCHAR(100), access_level VARCHAR(50) );

CREATE TABLE stadium ( stadium_id INT AUTO_INCREMENT PRIMARY KEY, name
VARCHAR(150), location VARCHAR(200), capacity INT, contact_info
VARCHAR(200) );

CREATE TABLE seat ( seat_id INT AUTO_INCREMENT PRIMARY KEY, stadium_id
INT, section VARCHAR(50), row_number VARCHAR(50), seat_number
VARCHAR(50), seat_type VARCHAR(50), status VARCHAR(20), FOREIGN KEY
(stadium_id) REFERENCES stadium(stadium_id) ON DELETE CASCADE ON UPDATE
CASCADE );

CREATE TABLE team ( team_id INT AUTO_INCREMENT PRIMARY KEY, name
VARCHAR(100), country VARCHAR(100), coach_name VARCHAR(100) );

CREATE TABLE match_table ( match_id INT AUTO_INCREMENT PRIMARY KEY,
stadium_id INT, home_team_id INT, away_team_id INT, match_date DATE,
start_time TIME, end_time TIME, status VARCHAR(20), FOREIGN KEY
(stadium_id) REFERENCES stadium(stadium_id) ON DELETE CASCADE ON UPDATE
CASCADE, FOREIGN KEY (home_team_id) REFERENCES team(team_id) ON DELETE
CASCADE ON UPDATE CASCADE, FOREIGN KEY (away_team_id) REFERENCES
team(team_id) ON DELETE CASCADE ON UPDATE CASCADE );

CREATE TABLE ticket_category ( category_id INT AUTO_INCREMENT PRIMARY
KEY, stadium_id INT, category_name VARCHAR(100), price DECIMAL(10,2),
status VARCHAR(20), FOREIGN KEY (stadium_id) REFERENCES
stadium(stadium_id) ON DELETE CASCADE ON UPDATE CASCADE );

CREATE TABLE ticket ( ticket_id INT AUTO_INCREMENT PRIMARY KEY, match_id
INT, seat_id INT, category_id INT, status VARCHAR(20), FOREIGN KEY
(match_id) REFERENCES match_table(match_id) ON DELETE CASCADE ON UPDATE
CASCADE, FOREIGN KEY (seat_id) REFERENCES seat(seat_id) ON DELETE
CASCADE ON UPDATE CASCADE, FOREIGN KEY (category_id) REFERENCES
ticket_category(category_id) ON DELETE CASCADE ON UPDATE CASCADE );

CREATE TABLE booking ( booking_id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT, match_id INT, booking_date DATETIME, total_amount
DECIMAL(10,2), payment_status VARCHAR(20), FOREIGN KEY (user_id)
REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE, FOREIGN
KEY (match_id) REFERENCES match_table(match_id) ON DELETE CASCADE ON
UPDATE CASCADE );

CREATE TABLE booking_details ( booking_detail_id INT AUTO_INCREMENT
PRIMARY KEY, booking_id INT, ticket_id INT, seat_id INT, FOREIGN KEY
(booking_id) REFERENCES booking(booking_id) ON DELETE CASCADE ON UPDATE
CASCADE, FOREIGN KEY (ticket_id) REFERENCES ticket(ticket_id) ON DELETE
CASCADE ON UPDATE CASCADE, FOREIGN KEY (seat_id) REFERENCES
seat(seat_id) ON DELETE CASCADE ON UPDATE CASCADE );

CREATE TABLE payment ( payment_id INT AUTO_INCREMENT PRIMARY KEY,
booking_id INT, amount DECIMAL(10,2), payment_method VARCHAR(50),
transaction_id VARCHAR(100), payment_date DATETIME, status VARCHAR(20),
FOREIGN KEY (booking_id) REFERENCES booking(booking_id) ON DELETE
CASCADE ON UPDATE CASCADE );

CREATE TABLE event_category ( event_category_id INT AUTO_INCREMENT
PRIMARY KEY, name VARCHAR(100), description TEXT );

CREATE TABLE event ( event_id INT AUTO_INCREMENT PRIMARY KEY,
event_category_id INT, match_id INT, title VARCHAR(150), poster_url
VARCHAR(255), FOREIGN KEY (event_category_id) REFERENCES
event_category(event_category_id) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (match_id) REFERENCES match_table(match_id) ON DELETE
CASCADE ON UPDATE CASCADE );

CREATE TABLE review ( review_id INT AUTO_INCREMENT PRIMARY KEY, user_id
INT, match_id INT, rating INT, comment TEXT, created_at TIMESTAMP
DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (user_id) REFERENCES
users(user_id) ON DELETE CASCADE ON UPDATE CASCADE, FOREIGN KEY
(match_id) REFERENCES match_table(match_id) ON DELETE CASCADE ON UPDATE
CASCADE );

CREATE TABLE notification ( notification_id INT AUTO_INCREMENT PRIMARY
KEY, user_id INT, title VARCHAR(150), message TEXT, sent_at DATETIME,
status VARCHAR(20), FOREIGN KEY (user_id) REFERENCES users(user_id) ON
DELETE CASCADE ON UPDATE CASCADE );
