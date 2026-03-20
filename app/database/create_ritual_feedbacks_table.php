<?php
/**
 * Sanskar AI - Create SAI_ritual_feedbacks Table
 * ================================================
 * Stores Like/Dislike feedback for AI-generated rituals
 * 
 * Run: php app/database/create_ritual_feedbacks_table.php
 */

require_once __DIR__ . '/../../index.php';

use App\Config\Database;

try {
    $pdo = Database::getConnection();

    $sql = "
        CREATE TABLE IF NOT EXISTS SAI_ritual_feedbacks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            community_name VARCHAR(255) NULL,
            religion VARCHAR(100) NULL,
            ritual_name VARCHAR(255) NOT NULL,
            feedback_type ENUM('like', 'dislike') NOT NULL,
            feedback_text TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_user_id (user_id),
            INDEX idx_ritual_name (ritual_name),
            INDEX idx_feedback_type (feedback_type),
            CONSTRAINT fk_ritual_feedbacks_user
                FOREIGN KEY (user_id) REFERENCES SAI_users(id)
                ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    $pdo->exec($sql);
    echo "✅ SAI_ritual_feedbacks table created successfully!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
