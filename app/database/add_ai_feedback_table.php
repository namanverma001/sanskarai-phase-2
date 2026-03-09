<?php
/**
 * Sanskar AI - Add AI Ritual Feedback Table
 * ==========================================
 * Stores user feedback on AI-generated rituals for self-learning
 * 
 * Usage: php app/database/add_ai_feedback_table.php
 */

require_once __DIR__ . '/../config/database.php';
use App\Config\Database;

echo "==============================================\n";
echo "  Adding SAI_ai_ritual_feedback table\n";
echo "==============================================\n\n";

try {
    $pdo = Database::getConnection(true);
    $pdo->exec("USE SAI");

    echo "Creating SAI_ai_ritual_feedback...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS SAI_ai_ritual_feedback (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            ritual_name VARCHAR(200) NOT NULL,
            community_name VARCHAR(150) NULL,
            religion VARCHAR(100) NULL,
            generation_session_id VARCHAR(64) NOT NULL,
            round_number INT NOT NULL DEFAULT 1,
            ai_response JSON NULL,
            user_feedback TEXT NULL,
            feedback_type ENUM('accepted', 'rejected', 'refined') NOT NULL DEFAULT 'refined',
            search_criteria JSON NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            
            FOREIGN KEY (user_id) REFERENCES SAI_users(id) ON DELETE CASCADE,
            
            INDEX idx_user_id (user_id),
            INDEX idx_ritual_name (ritual_name),
            INDEX idx_session_id (generation_session_id),
            INDEX idx_feedback_type (feedback_type),
            INDEX idx_community (community_name),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ SAI_ai_ritual_feedback created successfully!\n\n";
    
    echo "Done! Migration complete.\n";

} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
