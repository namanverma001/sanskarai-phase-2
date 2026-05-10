-- Migration script for Guest Tracking Feature
-- Run this script to create the required table in the database

CREATE TABLE IF NOT EXISTS SAI_guest_tracking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NULL,
    action_type ENUM('view', 'search', 'ai_pandit') NOT NULL,
    action_details TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
