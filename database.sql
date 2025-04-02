CREATE DATABASE IF NOT EXISTS ridesharing;
USE ridesharing;

CREATE TABLE IF NOT EXISTS passengers (
    passenger_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    email VARCHAR(100) UNIQUE,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(50) NOT NULL,    -- Plain text password (for testing purposes only)
    registration_date DATETIME DEFAULT CURRENT_TIMESTAMP
); 

CREATE TABLE IF NOT EXISTS drivers (
    driver_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    email VARCHAR(100) UNIQUE,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(50) NOT NULL,    -- Plain text password (for testing purposes only)
    license_number VARCHAR(20) NOT NULL,
    vehicle_type VARCHAR(50) NOT NULL,
    vehicle_plate VARCHAR(20) NOT NULL,
    registration_date DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS ride_bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    passenger_id INT NOT NULL,
    driver_id INT,                    -- NULL when no driver accepts the ride
    pickup_location VARCHAR(255) NOT NULL,
    destination VARCHAR(255) NOT NULL,
    distance_km DECIMAL(6, 2) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    booking_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'accepted', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (passenger_id) REFERENCES passengers(passenger_id),
    FOREIGN KEY (driver_id) REFERENCES drivers(driver_id)
);

CREATE TABLE IF NOT EXISTS ride_records (
    record_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    passenger_id INT NOT NULL,        -- For retrieving passenger name
    driver_id INT NOT NULL,           -- For retrieving driver name
    FOREIGN KEY (booking_id) REFERENCES ride_bookings(booking_id),
    FOREIGN KEY (passenger_id) REFERENCES passengers(passenger_id),
    FOREIGN KEY (driver_id) REFERENCES drivers(driver_id)
);

-- Sample data for passengers table
INSERT INTO passengers (first_name, last_name, phone_number, email, username, password) VALUES
('John', 'Smith', '0901234567', 'john.smith@email.com', 'johnsmith', '123456'),
('Mary', 'Johnson', '0912345678', 'mary.johnson@email.com', 'maryjohnson', '123456'),
('David', 'Williams', '0923456789', 'david.williams@email.com', 'davidwilliams', '123456'),
('Sarah', 'Brown', '0934567890', 'sarah.brown@email.com', 'sarahbrown', '123456'),
('Michael', 'Jones', '0945678901', 'michael.jones@email.com', 'michaeljones', '123456'),
('Emma', 'Davis', '0956789012', 'emma.davis@email.com', 'emmadavis', '123456'),
('James', 'Miller', '0967890123', 'james.miller@email.com', 'jamesmiller', '123456'),
('Lisa', 'Wilson', '0978901234', 'lisa.wilson@email.com', 'lisawilson', '123456'),
('Robert', 'Taylor', '0989012345', 'robert.taylor@email.com', 'roberttaylor', '123456'),
('Emily', 'Anderson', '0990123456', 'emily.anderson@email.com', 'emilyanderson', '123456');

-- Sample data for drivers table
INSERT INTO drivers (first_name, last_name, phone_number, email, username, password, license_number, vehicle_type, vehicle_plate) VALUES
('Daniel', 'Martin', '0901111111', 'daniel.martin@email.com', 'danielmartin', '123456', 'B2-111111', 'Toyota Vios', '51A-111.11'),
('Sophie', 'Clark', '0902222222', 'sophie.clark@email.com', 'sophieclark', '123456', 'B2-222222', 'Hyundai Accent', '51A-222.22'),
('William', 'Lee', '0903333333', 'william.lee@email.com', 'williamlee', '123456', 'B2-333333', 'Honda City', '51A-333.33'),
('Olivia', 'Walker', '0904444444', 'olivia.walker@email.com', 'oliviawalker', '123456', 'B2-444444', 'Toyota Vios', '51A-444.44'),
('Thomas', 'Hall', '0905555555', 'thomas.hall@email.com', 'thomashall', '123456', 'B2-555555', 'Hyundai Accent', '51A-555.55'),
('Grace', 'White', '0906666666', 'grace.white@email.com', 'gracewhite', '123456', 'B2-666666', 'Honda City', '51A-666.66'),
('Henry', 'Lewis', '0907777777', 'henry.lewis@email.com', 'henrylewis', '123456', 'B2-777777', 'Toyota Vios', '51A-777.77'),
('Alice', 'Young', '0908888888', 'alice.young@email.com', 'aliceyoung', '123456', 'B2-888888', 'Hyundai Accent', '51A-888.88'),
('George', 'King', '0909999999', 'george.king@email.com', 'georgeking', '123456', 'B2-999999', 'Honda City', '51A-999.99'),
('Lucy', 'Wright', '0900000000', 'lucy.wright@email.com', 'lucywright', '123456', 'B2-000000', 'Toyota Vios', '51A-000.00');