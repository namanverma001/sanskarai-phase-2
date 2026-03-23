<?php
/**
 * Migration: Create AI Pandit Chat Tables
 * ========================================
 * Creates SAI_pandit_chat_sessions and SAI_pandit_chat_messages tables
 * 
 * Run: php app/database/create_pandit_chat_tables.php
 */

require_once __DIR__ . '/../../index.php';

use App\Config\Database;

try {
    $db = Database::getConnection();
    
    echo "Creating AI Pandit Chat tables...\n\n";

    // 1. Chat Sessions table
    $db->exec("
        CREATE TABLE IF NOT EXISTS SAI_pandit_chat_sessions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(255) DEFAULT 'New Conversation',
            user_details JSON DEFAULT NULL COMMENT 'Stores collected DOB, birth time, place, gotra etc.',
            status ENUM('active', 'archived') DEFAULT 'active',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_user_id (user_id),
            INDEX idx_status (status),
            INDEX idx_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Created SAI_pandit_chat_sessions table\n";

    // 2. Chat Messages table
    $db->exec("
        CREATE TABLE IF NOT EXISTS SAI_pandit_chat_messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            session_id INT NOT NULL,
            role ENUM('user', 'assistant') NOT NULL,
            content TEXT NOT NULL,
            tokens_used INT DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_session_id (session_id),
            INDEX idx_role (role),
            CONSTRAINT fk_chat_msg_session FOREIGN KEY (session_id) 
                REFERENCES SAI_pandit_chat_sessions(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Created SAI_pandit_chat_messages table\n";

    echo "\n🎉 AI Pandit Chat tables created successfully!\n";

} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
