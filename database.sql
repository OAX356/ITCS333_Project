
CREATE DATABASE room_booking;

USE room_booking;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    profile_pic VARCHAR(255),                           -- Path to the user's profile picture (can be NULL)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP      -- Timestamp when the user is created
);
