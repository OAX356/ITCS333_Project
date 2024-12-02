
-- Create database
CREATE DATABASE IF NOT EXISTS RoomBookingSystem;
USE RoomBookingSystem;

-- 1. Users Table: For user registration and roles
CREATE TABLE Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP        -- Timestamp when the user is created
);

-- 2. User Profile Table: For managing additional user details
CREATE TABLE User_Profile (
    user_id INT PRIMARY KEY,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    profile_picture VARCHAR(255),    -- Path to the user's profile picture (can be NULL)
    FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE
);

-- 3. Rooms Table: For storing room details
CREATE TABLE Rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    capacity INT NOT NULL,
    equipment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. Room Schedule Table: For tracking room availability
CREATE TABLE Room_Schedule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    timeslot_start DATETIME NOT NULL,
    timeslot_end DATETIME NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (room_id) REFERENCES Rooms(id) ON DELETE CASCADE
);

-- 5. Bookings Table: For managing room bookings
CREATE TABLE Bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    room_id INT NOT NULL,
    schedule_id INT NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES Rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (schedule_id) REFERENCES Room_Schedule(id) ON DELETE CASCADE
);

CREATE TABLE equipment (
    equipment_id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT,
    equipment_name VARCHAR(100),
    FOREIGN KEY (room_id) REFERENCES rooms(room_id)
);
